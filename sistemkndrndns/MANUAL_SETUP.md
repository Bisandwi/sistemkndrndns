# Manual Setup dan Instalasi

Panduan ini menggunakan XAMPP di Windows. Nama folder, host, dan database dapat disesuaikan apabila konfigurasi aplikasi ikut diperbarui.

## A. Persiapan

1. Instal XAMPP dengan versi PHP 8.0 atau lebih baru (disarankan PHP 8.1+).
2. Pastikan ekstensi `pdo_mysql` aktif pada konfigurasi PHP.
3. Buka XAMPP Control Panel, lalu jalankan **Apache** dan **MySQL**.
4. Buka `http://localhost/` untuk memastikan Apache berjalan.

## B. Instalasi baru

### 1. Ekstrak source code

1. Ekstrak ZIP.
2. Salin folder `sistem-kendaraan-dinas` ke `C:\xampp\htdocs\`.
3. Pastikan file `index.php` berada di `C:\xampp\htdocs\sistem-kendaraan-dinas\index.php`.

### 2. Buat database

1. Buka `http://localhost/phpmyadmin`.
2. Pilih tab **Import**.
3. Pilih file `database.sql` dari folder project.
4. Jalankan import dan pastikan database `db_kendaraan_dinas` beserta tabelnya terbentuk.

File `database.sql` membuat database bernama `db_kendaraan_dinas`. Jika menggunakan nama database lain, ubah nama database pada file SQL dan `config/database.php` secara konsisten.

### 3. Atur koneksi database

Buka `config/database.php` dan sesuaikan konfigurasi:

- Host: umumnya `localhost`.
- Database: `db_kendaraan_dinas`.
- Username MySQL: umumnya `root` pada XAMPP lokal.
- Password MySQL: sesuai konfigurasi MySQL Anda (instalasi XAMPP tertentu menggunakan password kosong).

Simpan file setelah perubahan. Jangan gunakan kredensial contoh untuk server produksi.

### 4. Masukkan data demo (opsional)

Langkah ini hanya untuk database uji yang baru dibuat:

1. Pilih database `db_kendaraan_dinas` di phpMyAdmin.
2. Impor `data_contoh.sql`.
3. Data contoh kendaraan, pegawai, jadwal servis, dan akun operator akan ditambahkan sesuai isi SQL.

Akun awal dari `database.sql`:
- `admin` / `password`

Akun operator setelah `data_contoh.sql` diimpor:
- `operator_pendidikan` / `password`
- `operator_kesehatan` / `password`
- `operator_pu` / `password`

Data demo bersifat fiktif. Jangan mengimpor data demo ke database produksi yang berisi data resmi.

### 5. Buka aplikasi

Akses:

`http://localhost/sistem-kendaraan-dinas/login.php`

Login menggunakan akun administrator. Ganti password bawaan sebelum aplikasi digunakan untuk pencatatan nyata.

## C. Upgrade dari database versi sebelumnya

**Jangan impor `database.sql` ke database lama.** File tersebut untuk instalasi baru dan dapat menimbulkan konflik atau merusak struktur yang sudah berjalan.

### 1. Backup

1. Buka phpMyAdmin.
2. Pilih database aplikasi.
3. Pilih **Export → Quick → SQL → Export**.
4. Simpan file backup di lokasi terpisah.
5. Pastikan backup dapat dibaca dan jangan menimpa backup sebelumnya.

### 2. Periksa versi skema saat ini

Sebelum memilih migrasi, periksa apakah tabel/kolom berikut sudah tersedia di database:

- `chat_dinas`, `riwayat_kendaraan`, dan kolom lanjutan pada `users`, `pegawai`, serta `kendaraan`.
- `jadwal_perawatan`.
- `referensi_kerusakan`, `jadwal_perawatan.kerusakan_id`, dan `jadwal_perawatan.tanggal_perawatan_berikutnya`.
- `kendaraan.kategori_kendaraan`.

Jalankan hanya migrasi yang memang belum diterapkan dan sesuai dengan kondisi awal database.

### 3. Urutan migrasi berdasarkan kondisi

Untuk database yang sangat lama dan belum memiliki fitur lanjutan, jalankan migrasi yang belum ada dengan urutan berikut:

1. `upgrade_fitur_lanjutan.sql` — menambahkan fitur lanjutan, chat, dan riwayat.
2. `upgrade_jadwal_perawatan.sql` — membuat tabel jadwal perawatan jika belum tersedia.
3. `upgrade_katalog_jadwal_perawatan.sql` — membuat katalog kerusakan dan menambahkan relasi katalog serta tanggal perawatan berikutnya pada tabel jadwal.
4. `upgrade_kategori_kendaraan.sql` — mengubah kategori lama menjadi Non-Niaga/Niaga.

**Penting:** urutan di atas bukan instruksi untuk menjalankan semua file secara membabi buta. Lewati migrasi yang fiturnya sudah tersedia. Contohnya, jika `jadwal_perawatan` sudah ada, jangan jalankan migrasi jadwal yang tidak diperlukan; lanjutkan ke migrasi katalog hanya jika kolom/tabel katalog belum ada. `upgrade_katalog_jadwal_perawatan.sql` memerlukan tabel `jadwal_perawatan` terlebih dahulu.

Setiap migrasi jalankan satu kali. Jika muncul pesan duplicate table/column atau enum, hentikan proses dan periksa skema sebelum melanjutkan. Jangan menghapus tabel atau data untuk mengatasi error tanpa backup dan pemeriksaan.

### 4. Pembaruan kategori kendaraan

File `upgrade_kategori_kendaraan.sql` ditujukan untuk database lama yang sudah memiliki kolom `kendaraan.kategori_kendaraan` dengan kategori lama. Migrasi ini:

- Memperluas enum sementara agar nilai lama tetap valid.
- Mengubah label `Pribadi` dan `Non Pribadi` menjadi `Non-Niaga`.
- Menetapkan pilihan akhir `Non-Niaga` dan `Niaga`.

Setelah migrasi, tinjau daftar kendaraan dan ubah kendaraan angkutan barang berat menjadi **Niaga** jika sesuai. Migrasi menormalkan nilai lama ke Non-Niaga agar data tetap ada; migrasi tidak dapat menentukan tipe kendaraan secara otomatis dari merk/model.

### 5. Pasang source code terbaru

1. Simpan salinan folder aplikasi lama.
2. Ganti source code dengan versi ZIP terbaru.
3. Pertahankan konfigurasi database lokal yang benar pada `config/database.php`.
4. Uji login dan modul utama.

## D. Klasifikasi kategori kendaraan

Kategori yang digunakan aplikasi:

| Kategori | Definisi aplikasi | Contoh |
|---|---|---|
| **Non-Niaga (Operasional)** | Kendaraan operasional umum/non-niaga | Sedan, SUV, MPV, Pick Up, Blind Van |
| **Niaga (Angkutan Barang)** | Kendaraan niaga/angkutan barang berat | Truk, Dump Truck, Truk Box |

Dalam aturan aplikasi ini, **Pick Up dan Blind Van termasuk Non-Niaga**. Klasifikasi ini merupakan aturan operasional aplikasi; apabila instansi memiliki pedoman resmi yang berbeda, ikuti pedoman instansi.

## E. Penggunaan awal

1. Login sebagai administrator.
2. Periksa dan lengkapi data **Perangkat Daerah**.
3. Masukkan data **Pegawai**, status kepegawaian, dan tanggal pensiun jika tersedia.
4. Masukkan **Kendaraan** beserta nomor polisi, merk, tipe, tahun, kategori, kondisi, status, perangkat daerah, penanggung jawab, dan informasi masa pengelolaan.
5. Buat akun operator dan tetapkan perangkat daerahnya.
6. Catat penyerahan, pengembalian, mutasi, pemeliharaan, dan perubahan penanggung jawab melalui menu riwayat.
7. Gunakan katalog kerusakan sebagai referensi, lalu buat jadwal perawatan dengan temuan aktual dan rencana tindakan.
8. Periksa menu Monitoring dan Laporan secara berkala.

## F. Jadwal perawatan dan katalog kerusakan

Menu **Jadwal Perawatan Kendaraan** digunakan untuk mencatat kendaraan, tanggal perawatan, jenis servis, katalog kerusakan, uraian kerusakan aktual, rencana tindakan, bengkel, estimasi biaya, status, catatan, dan tanggal perawatan berikutnya.

Status jadwal:
- Terjadwal
- Dalam Perbaikan
- Selesai
- Dibatalkan

Katalog berisi referensi umum seperti mesin, rem, ban/kaki-kaki, kelistrikan, transmisi, AC, bodi/interior, dan administrasi KIR. Gejala dan rekomendasi tindakan bukan diagnosis final. Konfirmasi kondisi aktual dengan teknisi/bengkel dan manual kendaraan.

## G. Troubleshooting

- **Koneksi database gagal:** pastikan MySQL aktif, nama database/user/password benar, dan ekstensi `pdo_mysql` aktif.
- **404 Not Found:** pastikan folder project berada di `htdocs` dan URL sesuai nama folder.
- **Halaman kosong atau HTTP 500:** periksa log error Apache/PHP. Aktifkan error reporting hanya sementara di lingkungan lokal.
- **Dropdown tidak terbuka:** pastikan browser dapat memuat Bootstrap bundle CDN dan periksa Console browser. Bundle pada versi ini dimuat melalui footer.
- **Data tidak tampil:** pastikan aplikasi terhubung ke database yang benar dan proses import selesai tanpa error.
- **Operator tidak melihat data sesuai tugas:** periksa perangkat daerah yang ditetapkan pada akun operator.
- **Migrasi gagal karena kolom/tabel sudah ada:** hentikan migrasi, periksa skema aktual, lalu tentukan migrasi mana yang masih diperlukan. Jangan mengulang file migrasi secara otomatis.

## H. Checklist sebelum produksi

- [ ] Ganti password administrator dan seluruh akun demo.
- [ ] Hapus akun/data demo yang tidak diperlukan.
- [ ] Gunakan HTTPS.
- [ ] Gunakan password database yang kuat dan batasi hak akses database.
- [ ] Jangan membuka phpMyAdmin ke jaringan publik.
- [ ] Terapkan CSRF protection, rate limiting, hardening session, dan audit log.
- [ ] Verifikasi otorisasi setiap role pada sisi server.
- [ ] Atur backup berkala dan lakukan uji restore.
- [ ] Verifikasi data kendaraan, pegawai, masa manfaat, dan tanggal pensiun berdasarkan dokumen resmi.
- [ ] Ganti logo rekonstruksi dengan master logo resmi yang disetujui.
