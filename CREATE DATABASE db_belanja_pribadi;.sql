CREATE DATABASE db_belanja_pribadi;
USE db_belanja_pribadi;

CREATE TABLE barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  harga DOUBLE
);

CREATE TABLE transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barang_id INT,
  jumlah INT,
  total DOUBLE,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (barang_id) REFERENCES barang(id)
);
