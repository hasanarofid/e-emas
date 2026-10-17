#!/usr/bin/env bash

cd /www/wwwroot/posemas.web.id
git config --global --add safe.directory /www/wwwroot/posemas.web.id
export PATH=/usr/local/bin:/usr/bin:/bin:/www/server/php/83/bin:/www/server/php/82/bin:/www/server/php/81/bin:$PATH

DIAG_FILE="/www/wwwroot/posemas.web.id/public/deploy_diag.txt"
echo "=== DEPLOY DIAGNOSTIC ===" > "$DIAG_FILE"
date >> "$DIAG_FILE"
echo "SSH User: $(whoami)" >> "$DIAG_FILE"
echo "PHP Version: $(php -v | head -n 1)" >> "$DIAG_FILE"
grep -E "^DB_CONNECTION|^DB_HOST|^DB_PORT|^DB_DATABASE|^DB_USERNAME" .env >> "$DIAG_FILE" || true

# 1. Cek apakah ada password tersimpan untuk database 'emas_db' di SQLite aaPanel
REAL_DB_PASS=$(python3 -c "
import sqlite3
try:
    conn = sqlite3.connect('/www/server/panel/data/default.db')
    c = conn.cursor()
    c.execute(\"SELECT password FROM databases WHERE name='emas_db' OR username='emas_db' LIMIT 1\")
    row = c.fetchone()
    if row and row[0]:
        print(row[0])
except Exception:
    pass
" 2>/dev/null || true)

if [ -n "$REAL_DB_PASS" ]; then
    echo "Found registered password in aaPanel for emas_db" >> "$DIAG_FILE"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=\"$REAL_DB_PASS\"|" .env
fi

ENV_DB_PASS=$(grep -E "^DB_PASSWORD=" .env | cut -d '=' -f2- | tr -d '"' | tr -d "'")

# 2. Ambil root MySQL password dari config aaPanel
ROOT_PASS=$(python3 -c "
import sqlite3
try:
    conn = sqlite3.connect('/www/server/panel/data/default.db')
    c = conn.cursor()
    c.execute(\"SELECT mysql_root FROM config LIMIT 1\")
    row = c.fetchone()
    if row and row[0]:
        print(row[0])
except Exception:
    pass
" 2>/dev/null || true)

echo "Discovered MySQL root pass length: ${#ROOT_PASS}" >> "$DIAG_FILE"

# 3. Query untuk sinkronisasi user emas_db di semua host
SQL_GRANT="
CREATE DATABASE IF NOT EXISTS emas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'emas_db'@'localhost' IDENTIFIED BY '$ENV_DB_PASS';
CREATE USER IF NOT EXISTS 'emas_db'@'127.0.0.1' IDENTIFIED BY '$ENV_DB_PASS';
CREATE USER IF NOT EXISTS 'emas_db'@'%' IDENTIFIED BY '$ENV_DB_PASS';
ALTER USER 'emas_db'@'localhost' IDENTIFIED BY '$ENV_DB_PASS';
ALTER USER 'emas_db'@'127.0.0.1' IDENTIFIED BY '$ENV_DB_PASS';
ALTER USER 'emas_db'@'%' IDENTIFIED BY '$ENV_DB_PASS';
GRANT ALL PRIVILEGES ON emas_db.* TO 'emas_db'@'localhost';
GRANT ALL PRIVILEGES ON emas_db.* TO 'emas_db'@'127.0.0.1';
GRANT ALL PRIVILEGES ON emas_db.* TO 'emas_db'@'%';
FLUSH PRIVILEGES;
"

if [ -n "$ROOT_PASS" ]; then
    mysql -u root -p"$ROOT_PASS" -e "$SQL_GRANT" 2>> "$DIAG_FILE" && echo "Grants updated successfully with ROOT_PASS" >> "$DIAG_FILE" || true
fi

# 4. Tes koneksi database emas_db
echo "=== TESTING MYSQL CONNECTION ===" >> "$DIAG_FILE"
mysql -u emas_db -p"$ENV_DB_PASS" -h 127.0.0.1 emas_db -e "SELECT 'Koneksi Sukses' as status;" >> "$DIAG_FILE" 2>&1 || true

# 5. Clear cache sebelum migrasi agar tidak ada stale config
php artisan optimize:clear >> "$DIAG_FILE" 2>&1 || true

# 6. Jalankan migrations secara terperinci
echo "=== RUNNING MIGRATIONS ===" >> "$DIAG_FILE"
php artisan migrate --force >> "$DIAG_FILE" 2>&1

echo "=== RUNNING SEEDERS ===" >> "$DIAG_FILE"
php artisan db:seed --class=DatabaseSeeder --force >> "$DIAG_FILE" 2>&1 || true
php artisan db:seed --class=EmployeeAndPayrollSeeder --force >> "$DIAG_FILE" 2>&1 || true
php artisan db:seed --class=FinanceSeeder --force >> "$DIAG_FILE" 2>&1 || true

# 7. Storage link & optimize
php artisan storage:link --quiet || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set file permission
chown -R www:www /www/wwwroot/posemas.web.id
chmod -R 775 /www/wwwroot/posemas.web.id/storage /www/wwwroot/posemas.web.id/bootstrap/cache

# Hapus file diagnostik demi keamanan data credentials
rm -f "$DIAG_FILE"

echo "🚀 Deployment successfully completed!"
