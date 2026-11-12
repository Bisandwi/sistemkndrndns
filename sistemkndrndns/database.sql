CREATE DATABASE IF NOT EXISTS db_kendaraan_dinas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kendaraan_dinas;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  role ENUM('admin','operator') NOT NULL DEFAULT 'admin',
  perangkat_daerah_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE perangkat_daerah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_dinas VARCHAR(150) NOT NULL UNIQUE,
  keterangan VARCHAR(255) NULL
);

ALTER TABLE users ADD CONSTRAINT fk_user_dinas FOREIGN KEY (perangkat_daerah_id) REFERENCES perangkat_daerah(id) ON UPDATE CASCADE ON DELETE SET NULL;

CREATE TABLE pegawai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pegawai VARCHAR(150) NOT NULL,
  nip VARCHAR(30) NOT NULL UNIQUE,
  jabatan VARCHAR(120) NOT NULL,
  golongan VARCHAR(30) NOT NULL,
  perangkat_daerah_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  tanggal_pensiun DATE NULL,
  status_pegawai ENUM('Aktif','Dicopot') NOT NULL DEFAULT 'Aktif',
  CONSTRAINT fk_pegawai_dinas FOREIGN KEY (perangkat_daerah_id) REFERENCES perangkat_daerah(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE kendaraan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_polisi VARCHAR(20) NOT NULL UNIQUE,
  merk VARCHAR(100) NOT NULL,
  tipe VARCHAR(100) NOT NULL,
  tahun_produksi YEAR NOT NULL,
  warna VARCHAR(50) NOT NULL,
  kondisi ENUM('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  status_kendaraan ENUM('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  kategori_kendaraan ENUM('Non-Niaga','Niaga') NOT NULL DEFAULT 'Non-Niaga',
  tanggal_diterima DATE NULL,
  masa_pakai_tahun SMALLINT NOT NULL DEFAULT 7,
  tanggal_akhir_masa_pakai DATE NULL,
  perangkat_daerah_id INT NOT NULL,
  pegawai_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_kendaraan_dinas FOREIGN KEY (perangkat_daerah_id) REFERENCES perangkat_daerah(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_kendaraan_pegawai FOREIGN KEY (pegawai_id) REFERENCES pegawai(id)
    ON UPDATE CASCADE ON DELETE SET NULL
);

INSERT INTO perangkat_daerah (nama_dinas) VALUES
('Dinas Pendidikan'), ('Dinas Kesehatan'), ('Dinas Pekerjaan Umum');

-- Login awal: admin / password
-- Password hash untuk "password" (silakan segera ganti setelah login)
INSERT INTO users (username, password, nama)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

CREATE TABLE chat_dinas (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  perangkat_daerah_id INT NOT NULL,
  user_id INT NOT NULL,
  pesan TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_chat_dinas_created (perangkat_daerah_id, created_at),
  CONSTRAINT fk_chat_dinas FOREIGN KEY (perangkat_daerah_id) REFERENCES perangkat_daerah(id) ON DELETE CASCADE,
  CONSTRAINT fk_chat_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE riwayat_kendaraan (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  kendaraan_id INT NOT NULL,
  jenis_peristiwa ENUM('PENYERAHAN','PENGEMBALIAN','PINDAH_TANGAN','PEMELIHARAAN','PERUBAHAN_PENANGGUNG_JAWAB') NOT NULL,
  tanggal_peristiwa DATE NOT NULL,
  perangkat_daerah_asal_id INT NULL,
  perangkat_daerah_tujuan_id INT NULL,
  pegawai_asal_id INT NULL,
  pegawai_tujuan_id INT NULL,
  catatan TEXT NULL,
  dibuat_oleh INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_riwayat_kendaraan_tanggal (kendaraan_id, tanggal_peristiwa),
  CONSTRAINT fk_riwayat_kendaraan FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE CASCADE,
  CONSTRAINT fk_riwayat_dinas_asal FOREIGN KEY (perangkat_daerah_asal_id) REFERENCES perangkat_daerah(id) ON DELETE SET NULL,
  CONSTRAINT fk_riwayat_dinas_tujuan FOREIGN KEY (perangkat_daerah_tujuan_id) REFERENCES perangkat_daerah(id) ON DELETE SET NULL,
  CONSTRAINT fk_riwayat_pegawai_asal FOREIGN KEY (pegawai_asal_id) REFERENCES pegawai(id) ON DELETE SET NULL,
  CONSTRAINT fk_riwayat_pegawai_tujuan FOREIGN KEY (pegawai_tujuan_id) REFERENCES pegawai(id) ON DELETE SET NULL,
  CONSTRAINT fk_riwayat_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS jadwal_perawatan (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, kendaraan_id INT NOT NULL, kerusakan_id INT NULL, tanggal_jadwal DATE NOT NULL, tanggal_perawatan_berikutnya DATE NULL,
 jenis_perawatan VARCHAR(100) NOT NULL, spesifik_kerusakan TEXT NOT NULL, rencana_tindakan TEXT NULL,
 bengkel VARCHAR(180) NULL, estimasi_biaya DECIMAL(15,2) NULL,
 status ENUM('Terjadwal','Dalam Perbaikan','Selesai','Dibatalkan') NOT NULL DEFAULT 'Terjadwal',
 catatan TEXT NULL, dibuat_oleh INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_jadwal_perawatan_tanggal (tanggal_jadwal), INDEX idx_jadwal_perawatan_status (status),
 CONSTRAINT fk_jadwal_perawatan_kerusakan FOREIGN KEY (kerusakan_id) REFERENCES referensi_kerusakan(id) ON UPDATE CASCADE ON DELETE SET NULL,\n CONSTRAINT fk_jadwal_perawatan_kendaraan FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_jadwal_perawatan_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
