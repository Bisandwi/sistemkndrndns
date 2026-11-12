# Sistem Pengelolaan Kendaraan Dinas Operasional

Aplikasi web untuk pencatatan dan pemantauan kendaraan operasional instansi. Dibangun menggunakan **PHP Native, PDO, MySQL/MariaDB, Bootstrap 5, dan Chart.js**.

> Gunakan data yang telah diverifikasi oleh pengelola aset. Data demo dalam repositori ini bersifat fiktif dan hanya untuk pengujian.

## 1. Fitur aplikasi

- Login dan logout dengan peran **Administrator** dan **Operator**.
- Dashboard statistik kendaraan dan grafik kondisi/data kendaraan.
- Manajemen data kendaraan, pegawai, perangkat daerah, dan pengguna.
- Kategori kendaraan:
  - **Non-Niaga (Operasional):** sedan, SUV, MPV, pick up, blind van, dan kendaraan operasional umum.
  - **Niaga (Angkutan Barang):** truk, dump truck, truk box, dan kendaraan angkutan barang/niaga berat.
- Kondisi kendaraan: Baik, Rusak Ringan, dan Rusak Berat.
- Status pegawai Aktif/Dicopot dan pencatatan tanggal pensiun.
- Pencatatan masa pengelolaan dan perkiraan akhir masa pakai kendaraan.
- Monitoring akhir masa pakai kendaraan dan tanggal pensiun.
- Riwayat penyerahan, pengembalian, pindah tangan, pemeliharaan, dan perubahan penanggung jawab.
- Katalog referensi kerusakan dan jadwal perawatan kendaraan.
- Chat koordinasi berdasarkan perangkat daerah.
- Laporan, filter, cetak, dan ekspor CSV.

## 2. Teknologi dan persyaratan

- PHP **8.0 atau lebih baru** (disarankan PHP 8.1+).
- MySQL 5.7+/8.x atau MariaDB yang kompatibel.
- Ekstensi PHP `pdo_mysql`.
- Apache atau Nginx. Untuk pengembangan lokal, XAMPP dapat digunakan.
- Koneksi internet untuk memuat Bootstrap/Chart.js dari CDN.

## 3. Instalasi singkat (database baru)

1. Ekstrak ZIP ke document root, misalnya `C:\xampp\htdocs\sistem-kendaraan-dinas`.
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka phpMyAdmin (`http://localhost/phpmyadmin`) dan impor `database.sql`.
4. Periksa kredensial database di `config/database.php`.
5. (Opsional) Pilih database `db_kendaraan_dinas`, lalu impor `data_contoh.sql` untuk data uji.
6. Buka `http://localhost/sistem-kendaraan-dinas/login.php`.

Akun awal:
- Administrator: `admin` / `password`
- Akun operator demo (setelah `data_contoh.sql` diimpor):
  - `operator_pendidikan` / `password`
  - `operator_kesehatan` / `password`
  - `operator_pu` / `password`

**Ganti password awal sebelum aplikasi digunakan untuk data nyata.** Lihat [MANUAL_SETUP.md](MANUAL_SETUP.md) untuk langkah rinci, termasuk pembaruan database lama.

## 4. Kategori kendaraan

Kategori di aplikasi adalah klasifikasi operasional, bukan penanda kepemilikan pribadi.

| Kategori | Penggunaan | Contoh |
|---|---|---|
| Non-Niaga (Operasional) | Kendaraan operasional umum/non-niaga | Sedan, SUV, MPV, Pick Up, Blind Van |
| Niaga (Angkutan Barang) | Kendaraan angkutan barang/niaga berat | Truk, Dump Truck, Truk Box |

Pick Up dan Blind Van masuk **Non-Niaga** pada aturan klasifikasi aplikasi ini. Jika kebijakan instansi memiliki definisi klasifikasi berbeda, sesuaikan pilihan dan aturan aplikasi dengan pedoman resmi instansi.

## 5. Struktur file penting

- `config/database.php` — konfigurasi koneksi PDO.
- `includes/auth.php` — autentikasi, session, dan pemeriksaan hak akses.
- `includes/header.php`, `includes/footer.php` — layout dan navigasi.
- `index.php` — dashboard.
- `kendaraan.php` — data kendaraan.
- `pegawai.php` — data pegawai.
- `perangkat_daerah.php` — data perangkat daerah.
- `pengguna.php` — manajemen pengguna.
- `monitoring.php` — monitoring masa pakai dan pensiun.
- `riwayat.php` — riwayat kendaraan.
- `jadwal_perawatan.php` — katalog kerusakan dan jadwal perawatan.
- `chat.php` — komunikasi per perangkat daerah.
- `laporan.php` — laporan dan ekspor CSV.
- `database.sql` — skema lengkap untuk instalasi baru.
- `data_contoh.sql` — data demo fiktif.
- `upgrade_*.sql` — migrasi database untuk versi terdahulu.

## 6. Database: aturan penting

- `database.sql` **hanya untuk instalasi baru**. Jangan impor ulang ke database yang sudah berisi data.
- Sebelum menjalankan file `upgrade_*.sql`, buat backup dan periksa apakah perubahan yang sama sudah pernah diterapkan.
- File migrasi ditujukan untuk struktur database versi yang sesuai. Baca [MANUAL_SETUP.md](MANUAL_SETUP.md) untuk urutan dan prasyaratnya.
- Jangan jalankan migrasi berulang kali tanpa memeriksa tabel/kolom; operasi `ALTER TABLE` tertentu tidak idempoten.
- `data_contoh.sql` hanya untuk pengujian dan dapat menambah baris contoh. Jangan impor ke database produksi.

## 7. Catatan operasional

- Nilai masa pakai awal 7 tahun adalah nilai default aplikasi, **bukan penetapan masa manfaat resmi BMD**. Verifikasi dengan ketentuan dan dokumen yang berlaku.
- Katalog kerusakan dan interval perawatan adalah referensi umum, bukan diagnosis teknis. Ikuti manual kendaraan dan rekomendasi teknisi/bengkel.
- Monitoring pensiun bergantung pada tanggal pensiun yang diinput.
- Chat belum menggunakan WebSocket; pengguna perlu memuat ulang halaman untuk melihat pesan baru.
- `assets/logo-bpad.svg` merupakan rekonstruksi visual, bukan master logo resmi.

## 8. Keamanan sebelum produksi

Lakukan peninjauan keamanan sebelum aplikasi digunakan pada jaringan instansi atau menyimpan data resmi:

- Ganti seluruh password bawaan dan gunakan password unik yang kuat.
- Aktifkan HTTPS dan batasi akses server/database.
- Jangan membuka phpMyAdmin ke jaringan publik.
- Terapkan perlindungan CSRF, pembatasan percobaan login, hardening session, dan audit log.
- Validasi seluruh input di sisi server dan pastikan query memakai prepared statements.
- Atur backup otomatis dan uji proses restore.
- Hapus data demo dan verifikasi data master.
- Gunakan logo resmi yang telah disetujui.

## 9. Panduan untuk pengembang

Baca `AGENTS.md` sebelum mengubah kode. Dokumen tersebut memuat aturan struktur, keamanan, migrasi, dan pemeriksaan perubahan.
