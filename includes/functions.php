<?php
declare(strict_types=1);

/**
 * Kumpulan helper yang dipakai bersama oleh seluruh halaman.
 */

/** Memulai session sekali saja. */
function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/** Escape output untuk mencegah XSS. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Token CSRF milik session saat ini. */
function csrf_token(): string
{
    start_session();

    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/** Hidden input berisi token CSRF. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Validasi token CSRF dengan perbandingan waktu-konstan. */
function verify_csrf(mixed $token): bool
{
    start_session();

    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Simpan pesan flash untuk ditampilkan pada request berikutnya. */
function set_flash(string $type, string $message): void
{
    start_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil sekaligus hapus pesan flash. */
function take_flash(): ?array
{
    start_session();

    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

/** Redirect lalu hentikan eksekusi. */
function redirect_to(string $location): never
{
    header('Location: ' . $location);
    exit;
}

/** Pilihan program studi yang disarankan pada form. */
function prodi_suggestions(): array
{
    return [
        'S1 - Teknik Informatika',
        'S1 - Sistem Informasi',
        'S1 - Teknik Sipil',
        'S1 - Manajemen',
        'S1 - Akuntansi',
        'D3 - Manajemen Informatika',
    ];
}

/** Ambil id dari query string / payload dan pastikan bilangan bulat positif. */
function positive_int_id(mixed $raw): ?int
{
    if (is_int($raw)) {
        return $raw > 0 ? $raw : null;
    }

    if (!is_string($raw) || $raw === '' || !ctype_digit($raw)) {
        return null;
    }

    $id = (int) $raw;

    return $id > 0 ? $id : null;
}

/**
 * Validasi input mahasiswa (server-side).
 *
 * @return array{data: array<string,string>, errors: array<string,string>}
 */
function validate_student_input(array $input): array
{
    $data = [
        'nim'    => trim((string) ($input['nim'] ?? '')),
        'nama'   => trim((string) ($input['nama'] ?? '')),
        'alamat' => trim((string) ($input['alamat'] ?? '')),
        'prodi'  => trim((string) ($input['prodi'] ?? '')),
    ];

    $errors = [];

    if ($data['nim'] === '') {
        $errors['nim'] = 'NIM wajib diisi.';
    } elseif (mb_strlen($data['nim']) > 20) {
        $errors['nim'] = 'NIM maksimal 20 karakter.';
    }

    if ($data['nama'] === '') {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($data['nama']) > 100) {
        $errors['nama'] = 'Nama lengkap maksimal 100 karakter.';
    }

    if ($data['alamat'] === '') {
        $errors['alamat'] = 'Alamat wajib diisi.';
    }

    if ($data['prodi'] === '') {
        $errors['prodi'] = 'Program studi wajib diisi.';
    } elseif (mb_strlen($data['prodi']) > 100) {
        $errors['prodi'] = 'Program studi maksimal 100 karakter.';
    }

    return ['data' => $data, 'errors' => $errors];
}

/** Cek NIM yang sudah dipakai, mengabaikan id tertentu saat mode edit. */
function nim_exists(PDO $pdo, string $nim, ?int $ignoreId = null): bool
{
    if ($ignoreId === null) {
        $statement = $pdo->prepare('SELECT 1 FROM mahasiswa WHERE nim = :nim LIMIT 1');
        $statement->execute([':nim' => $nim]);
    } else {
        $statement = $pdo->prepare('SELECT 1 FROM mahasiswa WHERE nim = :nim AND id <> :id LIMIT 1');
        $statement->execute([':nim' => $nim, ':id' => $ignoreId]);
    }

    return (bool) $statement->fetchColumn();
}

/** Ambil satu baris mahasiswa berdasarkan primary key. */
function find_mahasiswa(PDO $pdo, int $id): ?array
{
    $statement = $pdo->prepare('SELECT id, nim, nama, alamat, prodi FROM mahasiswa WHERE id = :id LIMIT 1');
    $statement->execute([':id' => $id]);
    $row = $statement->fetch();

    return is_array($row) ? $row : null;
}

/** Halaman respons ketika data tidak ditemukan (HTTP 404). */
function render_not_found(string $message, string $title = 'Data Tidak Ditemukan'): never
{
    http_response_code(404);
    $pageTitle = $title;
    require __DIR__ . '/header.php';

    echo '<div class="empty-state">';
    echo '<p class="empty-state__text">' . e($message) . '</p>';
    echo '<a class="btn-app btn-app--secondary" href="index.php">Kembali ke Data Mahasiswa</a>';
    echo '</div>';

    require __DIR__ . '/footer.php';
    exit;
}

/** Halaman respons ketika metode HTTP tidak diizinkan (HTTP 405). */
function render_method_not_allowed(string $message): never
{
    http_response_code(405);
    $pageTitle = 'Permintaan Tidak Valid';
    require __DIR__ . '/header.php';

    echo '<div class="empty-state">';
    echo '<p class="empty-state__text">' . e($message) . '</p>';
    echo '<a class="btn-app btn-app--secondary" href="index.php">Kembali ke Data Mahasiswa</a>';
    echo '</div>';

    require __DIR__ . '/footer.php';
    exit;
}
