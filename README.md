# Sistem Informasi Data Mahasiswa

Aplikasi web CRUD data mahasiswa berbasis **PHP Native 8.2 + MySQL (PDO)** yang berjalan di **XAMPP**.
Antarmuka mengikuti screenshot referensi: kartu putih, header biru, tabel dengan baris berselang-seling,
tombol **Ubah** kuning dan **Hapus** merah.

---

## 1. Kebutuhan

| Komponen | Versi / Catatan |
|---|---|
| XAMPP | Apache + MySQL/MariaDB berjalan |
| PHP | 8.2 atau lebih baru (dengan ekstensi `pdo_mysql`) |
| Browser | Chrome, Edge, Firefox, atau Safari versi terbaru |

---

## 2. Struktur Proyek

```text
C:\xampp\htdocs\Caca\
│
├── assets\
│   ├── css\
│   │   ├── bootstrap.min.css
│   │   └── style.css
│   └── js\
│       └── app.js
│
├── config\
│   └── database.php
│
├── includes\
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── index.php
├── tambah.php
├── edit.php
├── hapus.php
├── database.sql
├── .htaccess
├── README.md
└── TESTING.md
```

---

## 3. Instalasi

### Langkah 1 — Jalankan XAMPP
Buka **XAMPP Control Panel**, lalu klik **Start** pada:

- Apache
- MySQL

### Langkah 2 — Letakkan proyek
Proyek sudah berada di:

```text
C:\xampp\htdocs\Caca
```

### Langkah 3 — Impor database

**Cara A — phpMyAdmin**

1. Buka <http://localhost/phpmyadmin/>
2. Pilih tab **Import**
3. Pilih file `C:\xampp\htdocs\Caca\database.sql`
4. Klik **Import**

**Cara B — Command line**

```bat
C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\Caca\database.sql
```

File SQL tersebut membuat database `db_mahasiswa`, tabel `mahasiswa`, dan mengisi 4 baris data contoh.

### Langkah 4 — Sesuaikan konfigurasi (bila perlu)
Buka `config/database.php` dan sesuaikan bila instalasi MySQL lokal Anda tidak memakai nilai default:

```php
$host     = 'localhost';
$port     = '3306';
$dbname   = 'db_mahasiswa';
$username = 'root';
$password = '';
```

### Langkah 5 — Buka aplikasi

```text
http://localhost/Caca/index.php
```

---

## 4. Halaman

| Halaman | Fungsi |
|---|---|
| `index.php` | Menampilkan daftar mahasiswa (Read) |
| `tambah.php` | Form tambah data (Create) |
| `edit.php?id=..` | Form ubah data (Update) |
| `hapus.php` | Konfirmasi lalu hapus data (Delete, khusus POST) |

---

## 5. Aturan Validasi

| Field | Aturan |
|---|---|
| NIM | Wajib, maksimal 20 karakter, unik |
| Nama Lengkap | Wajib, maksimal 100 karakter |
| Alamat | Wajib |
| Prodi | Wajib, maksimal 100 karakter |

Validasi dijalankan di dua tempat: JavaScript (kenyamanan pengisian) dan PHP (penentu utama).

---

## 6. Keamanan yang Diterapkan

| Aspek | Implementasi |
|---|---|
| SQL Injection | PDO prepared statement pada semua query, `ATTR_EMULATE_PREPARES => false` |
| XSS | Seluruh output di-escape melalui `htmlspecialchars()` (fungsi `e()`) |
| CSRF | Token per session, hidden input, diverifikasi dengan `hash_equals()` pada semua POST |
| Method mutation | Create, update, delete hanya melalui POST; `hapus.php` menolak GET dengan HTTP 405 |
| Duplicate NIM | Dicek di aplikasi dan dijaga oleh constraint `UNIQUE` di database |
| Error database | Detail hanya masuk `error_log`, pengguna melihat pesan umum |
| Id tidak valid | Divalidasi sebagai bilangan bulat positif, jika tidak → HTTP 404 |
| Credential | Hanya berada di `config/database.php`, tidak pernah ditampilkan |

---

## 7. Skema Database

```sql
CREATE TABLE mahasiswa (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nim VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    prodi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_mahasiswa_nim (nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 8. Pengujian

Lihat `TESTING.md` untuk hasil pengujian CRUD, validasi, keamanan, dan tampilan yang benar-benar dijalankan
terhadap Apache + MariaDB pada mesin ini.

---

## 9. Catatan

- Aplikasi tidak memakai framework, tidak memakai Node.js, dan tidak memakai layanan database eksternal.
- Bootstrap 5 disimpan lokal di `assets/css/bootstrap.min.css`, sehingga tampilan tetap berfungsi tanpa internet.
- Penghapusan selalu melewati halaman konfirmasi yang menampilkan NIM, nama, dan prodi mahasiswa terkait.
