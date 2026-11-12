# AGENTS.md — Panduan Agen dan Pengembang

## 1. Tujuan proyek

Aplikasi pengelolaan kendaraan dinas operasional berbasis PHP Native, PDO, MySQL/MariaDB, Bootstrap 5, dan Chart.js. Pertahankan implementasi yang sederhana, mudah dipasang di XAMPP, dan konsisten dengan struktur yang sudah ada.

## 2. Struktur dan tanggung jawab

- `config/database.php`: konfigurasi koneksi PDO (`$pdo`).
- `includes/auth.php`: session, autentikasi, helper, dan pemeriksaan role.
- `includes/header.php`, `includes/footer.php`: layout, navigasi, dan pemuatan aset.
- `index.php`: dashboard statistik dan grafik.
- `kendaraan.php`: CRUD kendaraan dan kategori.
- `pegawai.php`: CRUD pegawai, status, dan tanggal pensiun.
- `perangkat_daerah.php`: data perangkat daerah.
- `pengguna.php`: akun administrator/operator.
- `monitoring.php`: monitoring masa pakai kendaraan dan pensiun.
- `riwayat.php`: riwayat peristiwa kendaraan.
- `jadwal_perawatan.php`: katalog kerusakan dan jadwal servis/perbaikan.
- `chat.php`: ruang chat berdasarkan perangkat daerah.
- `laporan.php`: laporan, filter, cetak, dan ekspor CSV.
- `database.sql`: skema instalasi baru.
- `data_contoh.sql`: data uji fiktif.
- `upgrade_*.sql`: migrasi database versi lama.

## 3. Aturan domain kendaraan

Gunakan label kategori berikut secara konsisten di form, tabel, filter, dashboard, laporan, ekspor, dokumentasi, dan SQL:

1. `Non-Niaga` — kendaraan operasional umum/non-niaga, termasuk sedan, SUV, MPV, Pick Up, dan Blind Van.
2. `Niaga` — kendaraan angkutan barang/niaga berat, misalnya truk, dump truck, dan truk box.

Jangan gunakan kembali label `Pribadi` atau `Non Pribadi` sebagai pilihan aktif pada UI atau skema baru. Untuk kompatibilitas data lama, kode tampilan boleh menormalisasi label lama ke `Non-Niaga` selama proses transisi. Jangan mengasumsikan kategori kendaraan hanya dari merk; tinjau data kendaraan aktual dan pedoman klasifikasi instansi.

## 4. Aturan perubahan kode

1. Pertahankan PHP Native; jangan menambahkan framework besar tanpa persetujuan.
2. Gunakan PDO prepared statements untuk query yang menerima input pengguna.
3. Escape output HTML dengan `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` atau helper escaping proyek yang setara.
4. Validasi input di sisi server; jangan mengandalkan validasi HTML/JavaScript saja.
5. Periksa autentikasi dan otorisasi pada setiap endpoint/halaman yang dilindungi. Menyembunyikan menu bukan kontrol keamanan.
6. Operator hanya boleh mengakses ruang chat sesuai perangkat daerah yang ditetapkan. Jangan memperluas hak akses operator tanpa persetujuan.
7. Pertahankan foreign key dan integritas relasi. Jangan menghapus data historis otomatis.
8. Saat menambah/mengubah kolom atau tabel, perbarui `database.sql` untuk instalasi baru dan sediakan migrasi terpisah untuk database lama.
9. Migrasi harus didokumentasikan, memiliki prasyarat yang jelas, dan tidak menghilangkan data. Jangan menyatakan migrasi aman dijalankan berulang jika tidak idempoten.
10. Jangan menyimpan kredensial produksi, token, atau data pribadi nyata di source code, SQL demo, atau dokumentasi.
11. Gunakan Bahasa Indonesia untuk label UI dan dokumentasi pengguna.
12. Jaga tampilan responsif pada desktop dan seluler.
13. Hindari memuat Bootstrap bundle lebih dari satu kali. Periksa interaksi dropdown setelah mengubah header/footer.
14. Jangan mengubah asumsi masa manfaat BMD tanpa dasar. Nilai 7 tahun adalah default aplikasi, bukan ketentuan resmi.

## 5. Database dan migrasi

- Database default: `db_kendaraan_dinas`.
- Charset yang digunakan: `utf8mb4`.
- `database.sql` hanya untuk instalasi baru.
- `data_contoh.sql` hanya untuk database uji; jangan impor ke produksi.
- `upgrade_fitur_lanjutan.sql` menambahkan fitur lanjutan pada skema lama yang sesuai.
- `upgrade_jadwal_perawatan.sql` membuat tabel jadwal perawatan jika belum ada.
- `upgrade_katalog_jadwal_perawatan.sql` membutuhkan tabel `jadwal_perawatan` dan menambahkan katalog serta kolom relasi/tanggal perawatan berikutnya.
- `upgrade_kategori_kendaraan.sql` membutuhkan tabel kendaraan dan kolom `kategori_kendaraan` yang sudah ada. Migrasi menormalkan nilai lama `Pribadi`/`Non Pribadi` menjadi `Non-Niaga`, lalu membatasi enum ke `Non-Niaga`/`Niaga`.
- Sebelum migrasi, backup database dan periksa struktur aktual. Jalankan hanya migrasi yang belum diterapkan dan sesuai dengan versi skema.
- Untuk database lama, jangan mengimpor `database.sql`.

## 6. Perawatan dan katalog kerusakan

- Katalog kerusakan adalah referensi umum, bukan diagnosis otomatis.
- Simpan uraian gejala/kerusakan aktual kendaraan pada jadwal perawatan.
- Rekomendasi tindakan dan interval inspeksi harus diperlakukan sebagai panduan awal; manual kendaraan dan hasil pemeriksaan teknisi menjadi rujukan operasional.
- Pertahankan status jadwal: `Terjadwal`, `Dalam Perbaikan`, `Selesai`, dan `Dibatalkan`.
- Pastikan perubahan skema jadwal tercermin pada `database.sql`, migrasi, form, tabel, dan dokumentasi.

## 7. Keamanan dan privasi

- Jangan menampilkan password/hash, konfigurasi rahasia, atau data sensitif dalam log maupun output.
- Jangan mengendurkan autentikasi atau pemeriksaan role untuk mempermudah pengujian.
- Pertimbangkan CSRF protection, rate limiting, hardening session, audit log, HTTPS, dan validasi server-side sebagai kebutuhan sebelum produksi.
- Data demo harus tetap fiktif.
- Logo `assets/logo-bpad.svg` adalah rekonstruksi visual; jangan menyebutnya master resmi.

## 8. Pemeriksaan setelah perubahan

1. Jalankan `php -l` untuk setiap file PHP yang diubah.
2. Cocokkan nama kolom dan tipe data pada query dengan `database.sql` serta migrasi.
3. Uji kategori Non-Niaga dan Niaga pada tambah, edit, daftar, filter, dashboard, laporan, dan ekspor CSV.
4. Uji bahwa Pick Up dan Blind Van dapat dipilih sebagai Non-Niaga.
5. Uji login admin/operator dan otorisasi setiap modul.
6. Uji CRUD serta relasi kendaraan, pegawai, dan perangkat daerah.
7. Uji katalog kerusakan dan jadwal perawatan, termasuk status dan tanggal berikutnya.
8. Uji desktop/mobile, dropdown Bootstrap, dan Console browser.
9. Periksa log PHP/Apache.
10. Jika membuat ZIP rilis, sertakan source code dan dokumentasi; jangan sertakan backup database, file rahasia, atau data produksi.

## 9. Batasan yang diketahui

- Chat belum real-time; pengguna perlu memuat ulang halaman untuk melihat pesan baru.
- Monitoring pensiun bergantung pada tanggal pensiun yang dimasukkan.
- Masa pakai default bukan ketetapan masa manfaat resmi.
- Katalog kerusakan dan interval servis bukan pengganti pemeriksaan teknisi.
- Logo BPAD saat ini merupakan rekonstruksi visual, bukan master resmi.
