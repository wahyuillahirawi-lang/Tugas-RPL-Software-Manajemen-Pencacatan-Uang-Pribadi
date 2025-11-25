<?php
include('config.koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $query = "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama_barang', '$harga', '$stok')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: barang.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
    
    mysqli_close($koneksi);
} else {
    header("Location: barang.php");
    exit();
}
?>