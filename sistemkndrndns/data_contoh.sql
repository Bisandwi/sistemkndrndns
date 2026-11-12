-- DATA CONTOH / DEMO SISTEM KENDARAAN DINAS
-- Jalankan SETELAH database.sql (instalasi baru) atau setelah upgrade_fitur_lanjutan.sql (database lama).
-- Aman dijalankan ulang untuk data contoh ini karena menggunakan INSERT IGNORE dan kunci unik.
USE db_kendaraan_dinas;

-- Pastikan perangkat daerah contoh tersedia
INSERT IGNORE INTO perangkat_daerah (nama_dinas, keterangan) VALUES
('Dinas Pendidikan', 'Data contoh/demo'),
('Dinas Kesehatan', 'Data contoh/demo'),
('Dinas Pekerjaan Umum', 'Data contoh/demo');

-- Akun demo: admin/password (sudah dibuat oleh database.sql), operator/password
INSERT IGNORE INTO users (username, password, nama, role, perangkat_daerah_id)
SELECT 'operator_pendidikan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator Dinas Pendidikan', 'operator', id
FROM perangkat_daerah WHERE nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO users (username, password, nama, role, perangkat_daerah_id)
SELECT 'operator_kesehatan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator Dinas Kesehatan', 'operator', id
FROM perangkat_daerah WHERE nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO users (username, password, nama, role, perangkat_daerah_id)
SELECT 'operator_pu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator Dinas Pekerjaan Umum', 'operator', id
FROM perangkat_daerah WHERE nama_dinas='Dinas Pekerjaan Umum';

-- Data pegawai contoh (NIP dan tanggal adalah data fiktif untuk demo)
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Andi Pratama','199001012015011001','Kepala Subbagian','III/c',id,'2048-01-01','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Siti Rahmawati','198805152010012002','Pengelola Barang','III/b',id,'2046-05-15','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Budi Santoso','197912102005011003','Staf Administrasi','III/a',id,'2039-12-10','Dicopot' FROM perangkat_daerah WHERE nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Dewi Anggraini','199203202016012004','Kepala Seksi','III/c',id,'2050-03-20','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Rizky Maulana','198611252008011005','Pengadministrasi','III/b',id,'2044-11-25','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Nur Aisyah','199407102018012006','Staf Umum','III/a',id,'2052-07-10','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Agus Setiawan','198503052009011007','Kepala Subbagian','III/d',id,'2043-03-05','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Pekerjaan Umum';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Fajar Hidayat','199109182014011008','Pengelola Kendaraan','III/b',id,'2049-09-18','Aktif' FROM perangkat_daerah WHERE nama_dinas='Dinas Pekerjaan Umum';
INSERT IGNORE INTO pegawai (nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai)
SELECT 'Maya Puspita','198702282012012009','Staf Operasional','III/a',id,'2045-02-28','Dicopot' FROM perangkat_daerah WHERE nama_dinas='Dinas Pekerjaan Umum';

-- Data kendaraan contoh (nomor polisi dan penempatan bersifat fiktif)
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 1234 PKA','Toyota','Avanza 1.3 G',2021,'Hitam','Baik','Aktif','Non-Niaga','2021-03-15',7,'2028-03-15',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='199001012015011001' WHERE d.nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 2345 PKA','Honda','Vario 160',2022,'Merah','Baik','Aktif','Non-Niaga','2022-06-01',7,'2029-06-01',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='198805152010012002' WHERE d.nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 3456 PKA','Mitsubishi','L300',2017,'Putih','Rusak Ringan','Aktif','Non-Niaga','2017-02-10',7,'2024-02-10',d.id,NULL FROM perangkat_daerah d WHERE d.nama_dinas='Dinas Pendidikan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 4567 DKK','Toyota','Innova Zenix',2023,'Silver','Baik','Aktif','Non-Niaga','2023-08-12',7,'2030-08-12',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='199203202016012004' WHERE d.nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 5678 DKK','Suzuki','APV',2018,'Abu-abu','Rusak Ringan','Aktif','Non-Niaga','2018-04-20',7,'2025-04-20',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='198611252008011005' WHERE d.nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 6789 DKK','Yamaha','NMAX',2020,'Biru','Baik','Aktif','Non-Niaga','2020-11-05',7,'2027-11-05',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='199407102018012006' WHERE d.nama_dinas='Dinas Kesehatan';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 7890 DPW','Isuzu','Traga',2019,'Putih','Baik','Aktif','Non-Niaga','2019-07-01',7,'2026-07-01',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='198503052009011007' WHERE d.nama_dinas='Dinas Pekerjaan Umum';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 8901 DPW','Toyota','Hilux',2022,'Putih','Baik','Aktif','Non-Niaga','2022-09-14',7,'2029-09-14',d.id,p.id FROM perangkat_daerah d JOIN pegawai p ON p.nip='199109182014011008' WHERE d.nama_dinas='Dinas Pekerjaan Umum';
INSERT IGNORE INTO kendaraan (nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 9012 DPW','Daihatsu','Gran Max',2016,'Hitam','Rusak Berat','Tidak Aktif','Non-Niaga','2016-01-18',7,'2023-01-18',d.id,NULL FROM perangkat_daerah d WHERE d.nama_dinas='Dinas Pekerjaan Umum';

-- Contoh riwayat dan percakapan
INSERT INTO riwayat_kendaraan (kendaraan_id,jenis_peristiwa,tanggal_peristiwa,perangkat_daerah_tujuan_id,pegawai_tujuan_id,catatan,dibuat_oleh)
SELECT k.id,'PENYERAHAN','2023-08-12',k.perangkat_daerah_id,k.pegawai_id,'Penyerahan kendaraan operasional (data demo)',u.id
FROM kendaraan k JOIN users u ON u.username='admin' WHERE k.nomor_polisi='B 4567 DKK'
AND NOT EXISTS (SELECT 1 FROM riwayat_kendaraan r WHERE r.kendaraan_id=k.id AND r.catatan='Penyerahan kendaraan operasional (data demo)');
INSERT INTO chat_dinas (perangkat_daerah_id,user_id,pesan)
SELECT d.id,u.id,'Selamat datang di ruang koordinasi. Ini pesan contoh/demo.'
FROM perangkat_daerah d JOIN users u ON u.username='operator_pendidikan'
WHERE d.nama_dinas='Dinas Pendidikan'
AND NOT EXISTS (SELECT 1 FROM chat_dinas c WHERE c.user_id=u.id AND c.pesan='Selamat datang di ruang koordinasi. Ini pesan contoh/demo.');

-- Akun demo operator: operator_pendidikan / password; operator_kesehatan / password; operator_pu / password.
-- Semua nama, NIP, nomor polisi, dan data aset di file ini FIKTIF untuk pengujian. Hapus/ganti sebelum produksi.

-- Jadwal perawatan demo (data simulasi, dapat dijalankan ulang tanpa duplikasi)
INSERT INTO jadwal_perawatan
(kendaraan_id,kerusakan_id,tanggal_jadwal,tanggal_perawatan_berikutnya,jenis_perawatan,spesifik_kerusakan,rencana_tindakan,bengkel,estimasi_biaya,status,catatan,dibuat_oleh)
SELECT k.id,r.id,'2026-11-20','2027-02-20','Perbaikan Kerusakan',
       'Pedal rem terasa lebih dalam dari biasanya; perlu pemeriksaan langsung.',
       r.rekomendasi_tindakan,'Bengkel Mitra Demo',850000,'Terjadwal',
       'Jadwal contoh. Periksa kondisi aktual sebelum pekerjaan.',u.id
FROM kendaraan k
JOIN referensi_kerusakan r ON r.nama_kerusakan='Rem kurang pakem'
JOIN users u ON u.username='admin'
WHERE k.nomor_polisi='B 3456 PKA'
AND NOT EXISTS (SELECT 1 FROM jadwal_perawatan j WHERE j.kendaraan_id=k.id AND j.catatan='Jadwal contoh. Periksa kondisi aktual sebelum pekerjaan.');

INSERT INTO jadwal_perawatan
(kendaraan_id,kerusakan_id,tanggal_jadwal,tanggal_perawatan_berikutnya,jenis_perawatan,spesifik_kerusakan,rencana_tindakan,bengkel,estimasi_biaya,status,catatan,dibuat_oleh)
SELECT k.id,r.id,'2026-11-25','2027-02-25','Servis Berkala',
       'Pemeriksaan berkala sistem pelumasan dan kondisi mesin.',
       r.rekomendasi_tindakan,'Bengkel Mitra Demo',500000,'Terjadwal',
       'Jadwal servis berkala contoh.',u.id
FROM kendaraan k
JOIN referensi_kerusakan r ON r.nama_kerusakan='Oli mesin berkurang/bocor'
JOIN users u ON u.username='admin'
WHERE k.nomor_polisi='B 1234 PKA'
AND NOT EXISTS (SELECT 1 FROM jadwal_perawatan j WHERE j.kendaraan_id=k.id AND j.catatan='Jadwal servis berkala contoh.');

-- Contoh kendaraan kategori Niaga (angkutan barang berat)
INSERT IGNORE INTO kendaraan
(nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai,perangkat_daerah_id,pegawai_id)
SELECT 'B 1122 DPW','Hino','Dutro 110 SD',2020,'Putih','Baik','Aktif','Niaga','2020-05-10',7,'2027-05-10',d.id,NULL
FROM perangkat_daerah d WHERE d.nama_dinas='Dinas Pekerjaan Umum';
