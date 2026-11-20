<?php
declare(strict_types=1);

/**
 * Script inisialisasi tabel database untuk pertama kali di hosting.
 */

header('Content-Type: text/plain; charset=UTF-8');

require __DIR__ . '/config/database.php';

try {
    $sqlFile = __DIR__ . '/database_hosting.sql';
    if (!file_exists($sqlFile)) {
        throw new RuntimeException("Berkas database_hosting.sql tidak ditemukan.");
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    // Cek apakah data berhasil di-seed
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM mahasiswa");
    $total = $stmt->fetchColumn();

    echo "BERHASIL: Migrasi tabel dan seed data mahasiswa selesai!\n";
    echo "Total data mahasiswa saat ini: " . $total . " baris.\n\n";
    echo "Silakan akses halaman utama web Anda sekarang.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "GAGAL: " . $e->getMessage() . "\n";
}
