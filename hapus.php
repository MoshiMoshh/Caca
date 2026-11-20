<?php
declare(strict_types=1);

/**
 * Halaman hapus data mahasiswa (DELETE).
 *
 * Alur:
 * 1. Tombol Hapus pada index.php mengirim POST berisi id + token CSRF.
 * 2. Halaman ini menampilkan konfirmasi berisi nama dan NIM mahasiswa.
 * 3. Penghapusan hanya dijalankan pada POST kedua dengan penanda confirm=1.
 *
 * Penghapusan tidak pernah dilakukan melalui GET.
 */

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

start_session();

// Hanya menerima POST. Permintaan GET ditolak agar data tidak terhapus lewat tautan.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render_method_not_allowed(
        'Penghapusan data hanya dapat dilakukan melalui tombol Hapus pada halaman Data Mahasiswa.'
    );
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Sesi form sudah kedaluwarsa. Silakan ulangi proses hapus data.');
    redirect_to('index.php');
}

$id = positive_int_id($_POST['id'] ?? null);

if ($id === null) {
    render_not_found('Data mahasiswa yang akan dihapus tidak valid atau sudah tidak tersedia.');
}

$mahasiswa = find_mahasiswa($pdo, $id);

if ($mahasiswa === null) {
    set_flash('danger', 'Data mahasiswa tidak ditemukan. Kemungkinan data sudah dihapus sebelumnya.');
    redirect_to('index.php');
}

$confirmed = ($_POST['confirm'] ?? '') === '1';

if ($confirmed) {
    try {
        $statement = $pdo->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $statement->execute([':id' => $id]);

        if ($statement->rowCount() === 1) {
            set_flash('success', 'Data mahasiswa berhasil dihapus.');
        } else {
            set_flash('danger', 'Data mahasiswa tidak ditemukan. Kemungkinan data sudah dihapus sebelumnya.');
        }
    } catch (PDOException $exception) {
        error_log('[db_mahasiswa] Gagal menghapus data: ' . $exception->getMessage());
        set_flash('danger', 'Data gagal dihapus karena kesalahan pada database.');
    }

    redirect_to('index.php');
}

$pageTitle = 'Konfirmasi Hapus Data';
require __DIR__ . '/includes/header.php';
?>

            <div class="confirm-body">
                <p>Apakah Anda yakin ingin menghapus data mahasiswa berikut?</p>

                <div class="confirm-summary">
                    <div class="confirm-summary__row">
                        <div class="confirm-summary__label">NIM</div>
                        <div class="confirm-summary__value"><?= e((string) $mahasiswa['nim']) ?></div>
                    </div>
                    <div class="confirm-summary__row">
                        <div class="confirm-summary__label">Nama Lengkap</div>
                        <div class="confirm-summary__value"><?= e((string) $mahasiswa['nama']) ?></div>
                    </div>
                    <div class="confirm-summary__row">
                        <div class="confirm-summary__label">Prodi</div>
                        <div class="confirm-summary__value"><?= e((string) $mahasiswa['prodi']) ?></div>
                    </div>
                </div>

                <p class="confirm-warning">Data yang sudah dihapus tidak dapat dikembalikan.</p>

                <form method="post" action="hapus.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="confirm" value="1">
                    <div class="form-actions">
                        <button type="submit" class="btn-app btn-app--delete">Hapus</button>
                        <a class="btn-app btn-app--secondary" href="index.php">Batal</a>
                    </div>
                </form>
            </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
