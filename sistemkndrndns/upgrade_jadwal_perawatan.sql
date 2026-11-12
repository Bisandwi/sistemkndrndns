USE db_kendaraan_dinas;

CREATE TABLE IF NOT EXISTS jadwal_perawatan (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, kendaraan_id INT NOT NULL, tanggal_jadwal DATE NOT NULL,
 jenis_perawatan VARCHAR(100) NOT NULL, spesifik_kerusakan TEXT NOT NULL, rencana_tindakan TEXT NULL,
 bengkel VARCHAR(180) NULL, estimasi_biaya DECIMAL(15,2) NULL,
 status ENUM('Terjadwal','Dalam Perbaikan','Selesai','Dibatalkan') NOT NULL DEFAULT 'Terjadwal',
 catatan TEXT NULL, dibuat_oleh INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_jadwal_perawatan_tanggal (tanggal_jadwal), INDEX idx_jadwal_perawatan_status (status),
 CONSTRAINT fk_jadwal_perawatan_kendaraan FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_jadwal_perawatan_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
