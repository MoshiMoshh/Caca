<?php
declare(strict_types=1);

/**
 * Halaman tambah data mahasiswa (CREATE).
 *
 * GET  : menampilkan form kosong.
 * POST : memvalidasi, menyimpan ke MySQL, lalu redirect ke index.php.
 */

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

start_session();

$data = [
    'nim'    => '',
    'nama'   => '',
    'alamat' => '',
    'prodi'  => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Sesi form sudah kedaluwarsa. Silakan isi ulang form.');
        redirect_to('tambah.php');
    }

    $hasil = validate_student_input($_POST);
    $data = $hasil['data'];
    $errors = $hasil['errors'];

    if ($errors === [] && nim_exists($pdo, $data['nim'])) {
        $errors['nim'] = 'NIM tersebut sudah terdaftar. Gunakan NIM lain.';
    }

    if ($errors === []) {
        try {
            $statement = $pdo->prepare(
                'INSERT INTO mahasiswa (nim, nama, alamat, prodi)
                 VALUES (:nim, :nama, :alamat, :prodi)'
            );
            $statement->execute([
                ':nim'    => $data['nim'],
                ':nama'   => $data['nama'],
                ':alamat' => $data['alamat'],
                ':prodi'  => $data['prodi'],
            ]);

            set_flash('success', 'Data mahasiswa berhasil ditambahkan.');
            redirect_to('index.php');
        } catch (PDOException $exception) {
            error_log('[db_mahasiswa] Gagal menyimpan data: ' . $exception->getMessage());

            if ($exception->getCode() === '23000') {
                $errors['nim'] = 'NIM tersebut sudah terdaftar. Gunakan NIM lain.';
            } else {
                set_flash('danger', 'Data gagal disimpan karena kesalahan pada database.');
                redirect_to('tambah.php');
            }
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    render_method_not_allowed('Metode permintaan tidak didukung.');
}

$pageTitle = 'Tambah Data Mahasiswa';
require __DIR__ . '/includes/header.php';
?>

            <form method="post" action="tambah.php" data-validate="mahasiswa" novalidate>
                <?= csrf_field() ?>

                <div class="form-field">
                    <label class="form-label" for="nim">
                        NIM <span class="required-mark">*</span>
                    </label>
                    <input class="form-control-app<?= isset($errors['nim']) ? ' is-invalid' : '' ?>"
                           type="text" id="nim" name="nim" value="<?= e($data['nim']) ?>"
                           maxlength="20" autocomplete="off" required
                           <?= isset($errors['nim']) ? 'aria-invalid="true"' : '' ?>>
                    <span class="form-error" data-error-for="nim"><?= e($errors['nim'] ?? '') ?></span>
                    <span class="form-help">Maksimal 20 karakter dan tidak boleh sama dengan data lain.</span>
                </div>

                <div class="form-field">
                    <label class="form-label" for="nama">
                        Nama Lengkap <span class="required-mark">*</span>
                    </label>
                    <input class="form-control-app<?= isset($errors['nama']) ? ' is-invalid' : '' ?>"
                           type="text" id="nama" name="nama" value="<?= e($data['nama']) ?>"
                           maxlength="100" autocomplete="off" required
                           <?= isset($errors['nama']) ? 'aria-invalid="true"' : '' ?>>
                    <span class="form-error" data-error-for="nama"><?= e($errors['nama'] ?? '') ?></span>
                </div>

                <div class="form-field">
                    <label class="form-label" for="alamat">
                        Alamat <span class="required-mark">*</span>
                    </label>
                    <textarea class="form-control-app<?= isset($errors['alamat']) ? ' is-invalid' : '' ?>"
                              id="alamat" name="alamat" rows="3" required
                              <?= isset($errors['alamat']) ? 'aria-invalid="true"' : '' ?>><?= e($data['alamat']) ?></textarea>
                    <span class="form-error" data-error-for="alamat"><?= e($errors['alamat'] ?? '') ?></span>
                </div>

                <div class="form-field">
                    <label class="form-label" for="prodi">
                        Prodi <span class="required-mark">*</span>
                    </label>
                    <input class="form-control-app<?= isset($errors['prodi']) ? ' is-invalid' : '' ?>"
                           type="text" id="prodi" name="prodi" value="<?= e($data['prodi']) ?>"
                           list="prodi-options" maxlength="100" autocomplete="off" required
                           <?= isset($errors['prodi']) ? 'aria-invalid="true"' : '' ?>>
                    <datalist id="prodi-options">
<?php foreach (prodi_suggestions() as $prodi): ?>
                        <option value="<?= e($prodi) ?>"></option>
<?php endforeach; ?>
                    </datalist>
                    <span class="form-error" data-error-for="prodi"><?= e($errors['prodi'] ?? '') ?></span>
                    <span class="form-help">Ketik nama program studi, atau pilih dari daftar yang tersedia.</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-app btn-app--primary">Simpan</button>
                    <a class="btn-app btn-app--secondary" href="index.php">Batal</a>
                </div>
            </form>

<?php require __DIR__ . '/includes/footer.php'; ?>
