-- Backup database terlebih dahulu. Jalankan SATU KALI pada db_kendaraan_dinas.
USE db_kendaraan_dinas;

CREATE TABLE IF NOT EXISTS referensi_kerusakan (
 id INT AUTO_INCREMENT PRIMARY KEY,
 kategori VARCHAR(80) NOT NULL,
 nama_kerusakan VARCHAR(150) NOT NULL UNIQUE,
 gejala TEXT NULL,
 rekomendasi_tindakan TEXT NULL,
 interval_pemeriksaan_hari INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO referensi_kerusakan (kategori,nama_kerusakan,gejala,rekomendasi_tindakan,interval_pemeriksaan_hari) VALUES
('Mesin','Oli mesin berkurang/bocor','Terdapat rembesan oli atau volume oli cepat berkurang','Periksa seal, gasket, baut pembuangan dan sumber kebocoran; isi/ganti oli sesuai spesifikasi',30),
('Mesin','Mesin panas / overheat','Indikator suhu naik, uap atau cairan pendingin berkurang','Hentikan kendaraan dengan aman; periksa radiator, kipas, thermostat dan coolant',30),
('Mesin','Mesin sulit dihidupkan','Starter berat, mesin tidak menyala atau langsam tidak stabil','Periksa aki, busi, sistem bahan bakar dan sistem starter',30),
('Rem','Rem kurang pakem','Jarak pengereman bertambah atau pedal terasa dalam','Periksa kampas, cakram, minyak rem dan kebocoran; uji fungsi setelah perbaikan',30),
('Rem','Bunyi pada sistem rem','Bunyi gesekan/decit saat pengereman','Periksa kampas dan cakram/tromol; ganti komponen aus sesuai hasil pemeriksaan',30),
('Ban dan Kaki-kaki','Ban aus / botak','Alur ban menipis atau aus tidak merata','Periksa tekanan, spooring-balancing dan ganti ban bila melewati batas aman',30),
('Ban dan Kaki-kaki','Suspensi berbunyi / bocor','Bunyi saat melewati jalan tidak rata atau shockbreaker berminyak','Periksa bushing, ball joint, tie rod dan shockbreaker',60),
('Kelistrikan','Aki lemah','Starter lambat atau tegangan aki rendah','Uji aki dan sistem pengisian; bersihkan terminal atau ganti aki bila diperlukan',30),
('Kelistrikan','Lampu kendaraan mati','Lampu utama, sein atau rem tidak menyala','Periksa bohlam, sekring, kabel dan konektor',30),
('Transmisi','Perpindahan gigi sulit','Gigi sulit masuk atau terasa menghentak','Periksa oli transmisi, kopling dan sistem pemindah gigi',60),
('Pendingin / AC','AC tidak dingin','Hembusan tidak dingin atau kompresor tidak bekerja normal','Periksa filter kabin, kebocoran, tekanan refrigeran dan kompresor',90),
('Bodi dan Interior','Wiper / washer tidak berfungsi','Karet wiper rusak atau air washer tidak keluar','Periksa motor, pompa, selang dan karet wiper',90),
('Administrasi','KIR / uji berkala mendekati jatuh tempo','Masa berlaku uji berkala akan berakhir','Jadwalkan pemeriksaan dan pengujian sesuai ketentuan yang berlaku',NULL),
('Lainnya','Kerusakan lainnya','Gejala kerusakan belum tercantum pada daftar','Lakukan diagnosis oleh teknisi dan catat hasil pemeriksaan',NULL);

ALTER TABLE jadwal_perawatan
 ADD COLUMN kerusakan_id INT NULL AFTER kendaraan_id,
 ADD COLUMN tanggal_perawatan_berikutnya DATE NULL AFTER tanggal_jadwal,
 ADD CONSTRAINT fk_jadwal_perawatan_kerusakan FOREIGN KEY (kerusakan_id)
 REFERENCES referensi_kerusakan(id) ON UPDATE CASCADE ON DELETE SET NULL;
