<?php
declare(strict_types=1);

/**
 * Konfigurasi koneksi database.
 * Otomatis mendeteksi environment:
 * - Localhost (XAMPP)
 * - Hosting (InfinityFree)
 */

$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'], true);

if ($isLocal) {
    // Pengaturan MySQL Localhost (XAMPP)
    $host     = 'localhost';
    $port     = '3306';
    $dbname   = 'db_mahasiswa';
    $username = 'root';
    $password = '';
} else {
    // Pengaturan MySQL Hosting InfinityFree
    // Silakan disesuaikan dengan detail di dashboard InfinityFree akun if0_43489015
    $host     = 'MYSQL_HOST_PLACEHOLDER';
    $port     = '3306';
    $dbname   = 'MYSQL_DBNAME_PLACEHOLDER';
    $username = 'if0_43489015';
    $password = 'MYSQL_PASS_PLACEHOLDER';
}

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $exception) {
    // Detail teknis hanya masuk ke log server, tidak pernah ditampilkan ke pengguna umum.
    error_log('[db_mahasiswa] Koneksi database gagal: ' . $exception->getMessage());

    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');

    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Koneksi Database Gagal</title>';
    echo '<style>body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;'
        . 'background:#f5f6f7;color:#212529;margin:0;padding:48px 16px}'
        . '.box{max-width:640px;margin:0 auto;background:#fff;border:1px solid #dee2e6;'
        . 'border-radius:6px;overflow:hidden}'
        . 'h1{margin:0;background:#0d6efd;color:#fff;font-size:16px;font-weight:600;padding:12px 16px}'
        . '.body{padding:20px 16px;font-size:14px;line-height:1.6}'
        . 'code{background:#f1f1f1;padding:1px 5px;border-radius:3px}</style></head><body>';
    echo '<div class="box"><h1>Koneksi Database Gagal</h1><div class="body">';
    echo '<p>Aplikasi tidak dapat terhubung ke database.</p>';
    if ($isLocal) {
        echo '<p>Periksa hal berikut:</p><ul>';
        echo '<li>Service <strong>Apache</strong> dan <strong>MySQL</strong> pada XAMPP sudah berjalan.</li>';
        echo '<li>Database <code>db_mahasiswa</code> sudah dibuat melalui phpMyAdmin.</li>';
        echo '<li>Konfigurasi pada <code>config/database.php</code> sudah sesuai.</li>';
        echo '</ul>';
    } else {
        echo '<p>Periksa konfigurasi MySQL di <code>config/database.php</code> (Host, Database Name, Username, Password).</p>';
    }
    echo '<p>Detail teknis telah dicatat pada log server.</p>';
    echo '</div></div></body></html>';

    exit;
}
