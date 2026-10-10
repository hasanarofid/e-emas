<?php
/**
 * --------------------------------------------------------------------------
 * Cahaya Ornamen - Automated Deployment Webhook Script for Shared Hosting
 * --------------------------------------------------------------------------
 * Script ini digunakan untuk menarik perubahan (git pull) otomatis ke Shared Hosting
 * setelah Anda push dari lokal.
 * 
 * Cara Penggunaan:
 * 1. Buka melalui browser / webhook URL:
 *    https://cahayaornamen.com/deploy.php?token=GANTI_DENGAN_TOKEN_RAHASIA_ANDA
 * 
 * 2. Atau pasang di GitHub Webhook:
 *    - Payload URL: https://cahayaornamen.com/deploy.php?token=GANTI_DENGAN_TOKEN_RAHASIA_ANDA
 *    - Content type: application/json
 *    - Events: Just the push event
 */

// Ganti token ini dengan string rahasia yang unik dan aman
define('DEPLOY_SECRET_TOKEN', 'cahaya_ornamen_deploy_2026_xyz');

// Branch yang ingin ditarik
define('DEPLOY_BRANCH', 'main');

// Path root Laravel (satu level di atas public)
$basePath = realpath(__DIR__ . '/..');

// Nonaktifkan buffering untuk streaming output langsung
if (ob_get_level()) {
    ob_end_clean();
}

// 1. Verifikasi Keamanan Token
$requestToken = $_GET['token'] ?? $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? null;

if (!$requestToken || !hash_equals(DEPLOY_SECRET_TOKEN, $requestToken)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "===========================================\n";
    echo " 403 FORBIDDEN: Akses Ditolak!\n";
    echo " Token deployment tidak valid atau belum diisi.\n";
    echo "===========================================\n";
    exit;
}

// 2. Proteksi Lock File (Cegah Eksekusi Ganda Bersamaan)
$lockFile = $basePath . '/storage/framework/deploy.lock';
if (file_exists($lockFile) && (time() - filemtime($lockFile) < 180)) {
    http_response_code(429);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Deployment sedang berlangsung oleh proses lain. Silakan tunggu beberapa saat.";
    exit;
}

touch($lockFile);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deploying Cahaya Ornamen...</title>
    <style>
        body {
            background-color: #063b00;
            color: #fff8cf;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            padding: 30px;
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .header {
            border-bottom: 2px solid #2a7c13;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .badge {
            display: inline-block;
            background: #e1e100;
            color: #063b00;
            padding: 3px 8px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .command {
            color: #76c457;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .output {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(118, 196, 87, 0.2);
            padding: 10px 14px;
            border-radius: 6px;
            white-space: pre-wrap;
            word-break: break-all;
            color: #e5e5e5;
        }
        .success {
            color: #e1e100;
            font-weight: bold;
            margin-top: 25px;
            padding: 15px;
            background: rgba(42, 124, 19, 0.3);
            border: 1px solid #76c457;
            border-radius: 6px;
        }
        .error {
            color: #ff6b6b;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <span class="badge">Deployment Engine</span>
    <h2 style="margin: 8px 0 0 0; color: #fff8cf; font-family: serif;">Cahaya Ornamen — Shared Hosting Auto Pull</h2>
    <div style="font-size: 11px; opacity: 0.8;">Waktu Server: <?= date('Y-m-d H:i:s') ?> | Target: <?= htmlspecialchars($basePath) ?></div>
</div>

<?php
flush();

// Cek ketersediaan fungsi exec
if (!function_exists('exec') && !function_exists('shell_exec')) {
    echo '<div class="error">❌ ERROR: Fungsi exec() atau shell_exec() dinonaktifkan oleh shared hosting Anda di php.ini.</div>';
    echo '<div>Silakan aktifkan fungsi exec melalui cPanel > Select PHP Version > Options (hapus exec dari disable_functions).</div>';
    unlink($lockFile);
    exit;
}

// Daftar perintah deployment otomatis
$commands = [
    'Git Status' => 'git status --short',
    'Menarik Perubahan Terbaru (Git Pull)' => 'git pull origin ' . DEPLOY_BRANCH . ' 2>&1',
    'Menjalankan Migrasi Database' => 'php artisan migrate --force 2>&1',
    'Membersihkan Cache Lama' => 'php artisan optimize:clear 2>&1',
    'Membuat Cache Konfigurasi Baru' => 'php artisan config:cache 2>&1',
    'Membuat Cache Rute Baru' => 'php artisan route:cache 2>&1',
    'Membuat Cache Tampilan Blade' => 'php artisan view:cache 2>&1',
];

$hasError = false;

foreach ($commands as $label => $cmd) {
    echo "<div class=\"command\">▶ {$label}: <span style=\"opacity: 0.7;\">({$cmd})</span></div>";
    
    // Pindah ke root folder lalu jalankan perintah
    $fullCommand = "cd {$basePath} && {$cmd}";
    $output = [];
    $exitCode = 0;
    
    exec($fullCommand, $output, $exitCode);
    
    $resultText = implode("\n", $output);
    if (empty(trim($resultText))) {
        $resultText = "[Berhasil dijalankan tanpa output]";
    }
    
    echo "<div class=\"output\">" . htmlspecialchars($resultText) . "</div>";
    
    if ($exitCode !== 0 && strpos($cmd, 'git pull') !== false) {
        $hasError = true;
        echo "<div class=\"error\">⚠️ Terjadi kendala saat git pull (exit code: {$exitCode}). Pastikan permission folder dan SSH/Git credential di cPanel sudah sesuai.</div>";
    }
    
    flush();
}

// Hapus lock file
if (file_exists($lockFile)) {
    unlink($lockFile);
}

if (!$hasError) {
    echo '<div class="success">✨ DEPLOYMENT SELESAI DENGAN SUKSES! Website Cahaya Ornamen telah diperbarui dengan aset terbaru.</div>';
} else {
    echo '<div class="error" style="margin-top: 20px;">⚠️ Deployment selesai dengan catatan peringatan. Silakan periksa log di atas.</div>';
}
?>

</body>
</html>
