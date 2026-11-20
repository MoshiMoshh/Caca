# Laporan Pengujian

Pengujian dijalankan pada **Apache 2.4 + PHP 8.2.12 + MariaDB 10.4.32 (XAMPP)** di mesin ini,
bukan simulasi. Semua baris di bawah berasal dari eksekusi nyata terhadap
`http://localhost/Caca/`.

Database dikembalikan ke kondisi awal (4 baris data contoh) setelah pengujian selesai.

---

## 1. Pemeriksaan Sintaks

| Berkas | Hasil |
|---|---|
| `index.php` | No syntax errors detected |
| `tambah.php` | No syntax errors detected |
| `edit.php` | No syntax errors detected |
| `hapus.php` | No syntax errors detected |
| `config/database.php` | No syntax errors detected |
| `includes/functions.php` | No syntax errors detected |
| `includes/header.php` | No syntax errors detected |
| `includes/footer.php` | No syntax errors detected |

Tidak ada `Warning`, `Notice`, `Deprecated`, atau `Fatal error` pada output `index.php`,
`tambah.php`, `edit.php?id=1`, `edit.php?id=abc`, maupun `hapus.php`.

---

## 2. CRUD

| # | Skenario | Harapan | Hasil |
|---|---|---|---|
| 1 | `GET /index.php` | 200, tabel tampil | **HTTP 200**, 4 baris, kolom lengkap |
| 2 | `GET /tambah.php` | 200, token CSRF 64 karakter | **HTTP 200**, `token_len=64` |
| 3 | Create data valid | 302 ke `index.php`, baris masuk DB | **302**, DB berisi `55.55.055` |
| 4 | Create NIM duplikat | Ditolak, pesan field NIM | **HTTP 200**, "NIM tersebut sudah terdaftar. Gunakan NIM lain." |
| 5 | Create semua field kosong | 4 pesan error | **HTTP 200**, 4 pesan "wajib diisi." |
| 6 | Create dengan CSRF salah | Ditolak, tidak ada baris baru | **302**, jumlah baris NIM tersebut `0` |
| 7 | Update data | 302, nilai berubah di DB | **302**, DB: `Verifikasi Setelah Ubah / Jl. Baru 9 / S1 - Teknik Sipil` |
| 8 | Update ke NIM milik orang lain | Ditolak | **HTTP 200**, "sudah dipakai data mahasiswa lain" |
| 9 | `GET /hapus.php?id=..` | Ditolak, data utuh | **HTTP 405**, baris masih ada |
| 10 | `POST /hapus.php` tanpa `confirm` | Halaman konfirmasi, data utuh | **HTTP 200**, "Konfirmasi Hapus Data", baris masih ada |
| 11 | `POST /hapus.php` dengan `confirm=1` | 302, baris terhapus | **302**, sisa baris `0` |

---

## 3. Validasi & Error Handling

| # | Skenario | Hasil |
|---|---|---|
| 12 | `id=999` (tidak ada) | **HTTP 404** |
| 13 | `id=abc` (bukan angka) | **HTTP 404** |
| 14 | `id=-1` | **HTTP 404** |
| 15 | `id=0` | **HTTP 404** |
| 16 | `id=` (kosong) | **HTTP 404** |
| 17 | Database kosong | Empty state tampil, tabel tidak dirender |
| 18 | Flash message setelah redirect | Tampil sekali, hilang setelah reload (`alert count: 0`) |

---

## 4. Keamanan

| # | Skenario | Hasil |
|---|---|---|
| 19 | XSS pada NIM `<b>9</b>` | Ter-escape: `&lt;b&gt;9&lt;/b&gt;` |
| 20 | XSS pada nama `<script>x</script>` | Ter-escape: `&lt;script&gt;x&lt;/script&gt;`, tag mentah `0` |
| 21 | XSS pada alamat `<img src=x onerror=alert(1)>` | Ter-escape penuh |
| 22 | Karakter khusus `A & B "C"` | Ter-escape: `A &amp; B &quot;C&quot;` |
| 23 | SQL injection lewat input form | Tidak terjadi — semua query memakai PDO prepared statement |
| 24 | Akses `database.sql` | **HTTP 403** |
| 25 | Akses `README.md` | **HTTP 403** |
| 26 | Akses `includes/header.php` langsung | **HTTP 403** |
| 27 | Akses `includes/functions.php` langsung | **HTTP 403** |
| 28 | Akses `.htaccess` | **HTTP 403** |

---

## 5. Tampilan (diukur di browser sungguhan)

Nilai diambil dari `getComputedStyle` pada halaman yang dirender Apache.

| Elemen | Nilai terukur | Target referensi |
|---|---|---|
| Header | `rgb(13, 110, 253)`, tinggi 42px, teks putih 16px | `#0D6EFD` |
| Judul header | "Data Mahasiswa" | "Data Mahasiswa" |
| Tombol Tambah Data | `rgb(8, 122, 101)`, teks putih, 108×35px | `#087A65` |
| Border kartu | `1px rgb(222, 226, 230)`, radius 4px | `#DEE2E6` |
| Header tabel | `#f1f1f1` (rgb 241, 241, 241) | `#F1F1F1` |
| Baris data | tinggi 48px, padding 8px 12px | — |
| Baris berselang-seling | putih / `rgb(246, 246, 246)` | bergantian |
| Tombol Ubah | `rgb(255, 193, 7)`, teks `rgb(33, 37, 41)` | `#FFC107` |
| Tombol Hapus | `rgb(244, 81, 69)`, teks putih | `#F45145` |
| Kolom | No. 64px, NIM 137px, Nama 289px, Alamat 274px, Prodi 247px, Aksi 184px | 6 kolom |
| Perataan | semua sel rata kiri, sesuai referensi | rata kiri |
| Grid tabel | border luar 1px abu-abu, garis vertikal antar kolom, border bawah baris terakhir | grid penuh |

### Responsif

| Viewport | Overflow horizontal halaman | Tabel | Tombol Tambah |
|---|---|---|---|
| Desktop 1262px | tidak | penuh, tanpa scroll | 108px, rata kiri |
| Tablet 768px | tidak | scroll horizontal dalam wrapper | — |
| Mobile 390px | tidak | scroll horizontal dalam wrapper (820px) | selebar kontainer |

Form pada mobile: 4 field, masing-masing 348px (selebar area konten 372px dikurangi padding),
tombol Simpan dan Batal masing-masing 170px, tidak ada overflow halaman.

---

## 6. Catatan

- Bootstrap 5.3.3 disimpan lokal (`assets/css/bootstrap.min.css`, 232 KB) sehingga tampilan
  tidak bergantung pada CDN.
- Konfirmasi hapus memakai dialog browser (`window.confirm`) sebagai lapis pertama. Lapis kedua
  adalah halaman `hapus.php` yang menampilkan NIM, nama, dan prodi sebelum data dihapus.
  Tanpa JavaScript, penghapusan tetap tidak bisa terjadi tanpa melewati halaman konfirmasi.
- Seluruh pengujian di atas dijalankan ulang setelah perbaikan CSS grid dan perataan kolom.
