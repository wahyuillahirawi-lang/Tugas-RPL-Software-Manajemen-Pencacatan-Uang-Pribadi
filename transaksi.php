<?php
// KONEKSI DATABASE LANGSUNG - TIDAK PERLU FILE TERPISAH
$host = "localhost";
$username = "root";
$password = "";
$database = "rpl";

// Buat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    // Jika database tidak ada, coba buat
    $koneksi_temp = mysqli_connect($host, $username, $password);
    
    if ($koneksi_temp) {
        // Buat database jika belum ada
        mysqli_query($koneksi_temp, "CREATE DATABASE IF NOT EXISTS $database");
        mysqli_select_db($koneksi_temp, $database);
        
        // Buat tabel transaksi jika belum ada (VERSI DIPERBAIKI)
        $create_table = "CREATE TABLE IF NOT EXISTS transaksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_barang VARCHAR(100) NOT NULL,
            jumlah INT NOT NULL,x
            total DECIMAL(10,2) NOT NULL,
            tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($koneksi_temp, $create_table);
        
        // Koneksi ulang dengan database
        $koneksi = mysqli_connect($host, $username, $password, $database);
    } else {
        die("Koneksi ke MySQL gagal. Pastikan MySQL berjalan di XAMPP.");
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];
    $total = $jumlah * $harga_satuan;
    
    // Insert data transaksi (VERSI DIPERBAIKI - tanpa kolom harga)
    $query = "INSERT INTO transaksi (nama_barang, jumlah, total) VALUES ('$nama_barang', '$jumlah', '$total')";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Transaksi berhasil disimpan! Total: Rp " . number_format($total, 0, ',', '.');
        // Refresh untuk menampilkan data terbaru
        header("Location: transaksi.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Query untuk mengambil data transaksi
$query_transaksi = "SELECT * FROM transaksi ORDER BY tanggal DESC";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .nav {
            background-color: #2c3e50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        .nav a:hover {
            background-color: #34495e;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #219a52;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .form-section {
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .total-row {
            background-color: #2ecc71 !important;
            color: white;
            font-weight: bold;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            color: #34495e;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            border-top: 1px solid #ddd;
            color: #7f8c8d;
        }
        .calculation {
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <div class="nav">
            <a href="barang.php">🏠 Home</a>
            <a href="pencarian.php">🔍 Pencarian Barang</a>
            <a href="transaksi.php">💰 Transaksi</a>
            <a href="laporan.php">📊 Laporan</a>
        </div>

        <h1>💰 Transaksi Barang</h1>
        
        <!-- Form Transaksi -->
        <div class="form-section">
            <h2>➕ Input Transaksi Baru</h2>
            
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="transaksiForm">
                <div class="form-group">
                    <label>Nama Barang:</label>
                    <input type="text" name="nama_barang" placeholder="Masukkan nama barang" required>
                </div>
                
                <div class="form-group">
                    <label>Jumlah:</label>
                    <input type="number" name="jumlah" id="jumlah" min="1" placeholder="Jumlah barang" required onchange="hitungTotal()">
                </div>
                
                <div class="form-group">
                    <label>Harga per Item:</label>
                    <input type="number" name="harga_satuan" id="harga_satuan" min="0" placeholder="Harga satuan" required onchange="hitungTotal()">
                </div>
                
                <div class="calculation" id="totalCalculation">
                    Total: Rp 0
                </div>
                
                <input type="submit" value="💾 Simpan Transaksi">
            </form>
        </div>

        <!-- Daftar Transaksi -->
        <div class="form-section">
            <h2>📋 Daftar Transaksi</h2>
            
            <?php if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>NAMA BARANG</th>
                        <th>JUMLAH</th>
                        <th>TOTAL</th>
                        <th>TANGGAL</th>
                    </tr>
                    <?php 
                    $grand_total = 0;
                    while ($row = mysqli_fetch_assoc($result_transaksi)): 
                        $grand_total += $row['total'];
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>TOTAL KESELURUHAN:</strong></td>
                        <td colspan="2"><strong>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></strong></td>
                    </tr>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 30px; color: #7f8c8d; font-size: 16px;">
                    📝 Belum ada data transaksi. Silakan input transaksi baru di atas.
                </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Antlerset Dafner Belanja Britech</p>
        </div>
    </div>

    <script>
        function hitungTotal() {
            var jumlah = document.getElementById('jumlah').value;
            var harga = document.getElementById('harga_satuan').value;
            
            if (jumlah && harga) {
                var total = jumlah * harga;
                document.getElementById('totalCalculation').innerHTML = 
                    'Total: Rp ' + total.toLocaleString('id-ID');
            } else {
                document.getElementById('totalCalculation').innerHTML = 'Total: Rp 0';
            }
        }

        // Hitung total saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            hitungTotal();
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi
if (isset($koneksi)) {
    mysqli_close($koneksi);
}