-- Jalankan sekali pada database db_kendaraan_dinas yang sudah ada.
USE db_kendaraan_dinas;

ALTER TABLE users
  ADD COLUMN role ENUM('admin','operator') NOT NULL DEFAULT 'admin',
  ADD COLUMN perangkat_daerah_id INT NULL,
  ADD CONSTRAINT fk_user_dinas FOREIGN KEY (perangkat_daerah_id) REFERENCES perangkat_daerah(id)
    ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE pegawai
  ADD COLUMN tanggal_pensiun DATE NULL,
  ADD COLUMN status_pegawai ENUM('Aktif','Dicopot') NOT NULL DEFAULT 'Aktif';

ALTER TABLE kendaraan
  ADD COLUMN kategori_kendaraan ENUM('Non-Niaga','Niaga') NOT NULL DEFAULT 'Non-Niaga',
  ADD COLUMN tanggal_diterima DATE NULL,
  ADD COLUMN masa_pakai_tahun SMALLINT NOT NULL DEFAULT 7,
  ADD COLUMN tanggal_akhir_masa_pakai DATE NULL;

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
