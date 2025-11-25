<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "rpl";

// Coba koneksi dengan database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Jika database tidak ada, buat terlebih dahulu
if (!$koneksi) {
    // Koneksi tanpa database dulu
    $koneksi_temp = mysqli_connect($host, $username, $password);
    
    if ($koneksi_temp) {
        // Buat database
        $sql_create_db = "CREATE DATABASE IF NOT EXISTS $database";
        if (mysqli_query($koneksi_temp, $sql_create_db)) {
            // Koneksi ulang dengan database
            $koneksi = mysqli_connect($host, $username, $password, $database);
            
            // Buat tabel
            $sql_create_table = "CREATE TABLE IF NOT EXISTS barang (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama_barang VARCHAR(100) NOT NULL,
                harga DECIMAL(10,2) NOT NULL,
                stok INT NOT NULL
            )";
            mysqli_query($koneksi, $sql_create_table);
        }
        mysqli_close($koneksi_temp);
    }
}

// Cek koneksi final
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>