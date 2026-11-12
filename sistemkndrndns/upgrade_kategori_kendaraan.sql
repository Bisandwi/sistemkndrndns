-- Jalankan satu kali pada database db_kendaraan_dinas yang sudah ada.
-- Backup database sebelum menjalankan migrasi.
USE db_kendaraan_dinas;

-- Perluas ENUM sementara agar nilai lama tetap valid selama proses migrasi.
ALTER TABLE kendaraan
  MODIFY COLUMN kategori_kendaraan ENUM('Pribadi','Non Pribadi','Non-Niaga','Niaga')
  NOT NULL DEFAULT 'Non-Niaga';

-- Label kategori lama dinormalisasi ke Non-Niaga.
-- Tinjau ulang kendaraan setelah migrasi dan ubah ke Niaga jika sesuai.
UPDATE kendaraan
SET kategori_kendaraan = 'Non-Niaga'
WHERE kategori_kendaraan IN ('Pribadi','Non Pribadi');

-- Tetapkan pilihan kategori baru.
ALTER TABLE kendaraan
  MODIFY COLUMN kategori_kendaraan ENUM('Non-Niaga','Niaga')
  NOT NULL DEFAULT 'Non-Niaga';
