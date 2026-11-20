<?php
declare(strict_types=1);

/**
 * Halaman utama — menampilkan daftar mahasiswa (READ).
 */

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

start_session();

try {
    $statement = $pdo->query(
        'SELECT id, nim, nama, alamat, prodi FROM mahasiswa ORDER BY id ASC'
    );
    $mahasiswa = $statement->fetchAll();
} catch (PDOException $exception) {
    error_log('[db_mahasiswa] Gagal mengambil data: ' . $exception->getMessage());
    set_flash('danger', 'Data mahasiswa gagal dimuat karena kesalahan pada database.');
    $mahasiswa = [];
}

$totalData = count($mahasiswa);

$pageTitle = 'Data Mahasiswa';
require __DIR__ . '/includes/header.php';
?>

            <div class="toolbar">
                <a class="btn-app btn-app--primary" href="tambah.php">Tambah Data</a>
            </div>

<?php if ($totalData === 0): ?>
            <div class="empty-state">
                <p class="empty-state__text">
                    Belum ada data mahasiswa. Klik tombol Tambah Data untuk menambahkan data pertama.
                </p>
            </div>
<?php else: ?>
            <div class="table-wrapper">
                <table class="table-mahasiswa">
                    <caption class="visually-hidden">Daftar data mahasiswa</caption>
                    <thead>
                        <tr>
                            <th class="col-no" scope="col">No.</th>
                            <th class="col-nim" scope="col">NIM</th>
                            <th scope="col">Nama Lengkap</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Prodi</th>
                            <th class="col-aksi" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
<?php $nomor = 1; ?>
<?php foreach ($mahasiswa as $row): ?>
                        <tr>
                            <td class="col-no"><?= e((string) $nomor) ?></td>
                            <td class="col-nim"><?= e((string) $row['nim']) ?></td>
                            <td><?= e((string) $row['nama']) ?></td>
                            <td><?= e((string) $row['alamat']) ?></td>
                            <td><?= e((string) $row['prodi']) ?></td>
                            <td class="col-aksi">
                                <div class="cell-aksi">
                                    <a class="btn-app btn-app--edit btn-app--sm"
                                       href="edit.php?id=<?= (int) $row['id'] ?>">Ubah</a>
                                    <form method="post" action="hapus.php"
                                          data-confirm="Hapus data mahasiswa &quot;<?= e((string) $row['nama']) ?>&quot; (NIM <?= e((string) $row['nim']) ?>)?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn-app btn-app--delete btn-app--sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
<?php $nomor++; ?>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
