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
        
        // Buat tabel transaksi jika belum ada
        $create_table = "CREATE TABLE IF NOT EXISTS transaksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_barang VARCHAR(100) NOT NULL,
            jumlah INT NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($koneksi_temp, $create_table);
        
        // Insert contoh data jika tabel kosong
        $check_data = "SELECT COUNT(*) as total FROM transaksi";
        $result_check = mysqli_query($koneksi_temp, $check_data);
        $row = mysqli_fetch_assoc($result_check);
        
        if ($row['total'] == 0) {
            // Tambahkan data contoh
            $sample_data = [
                "('Laptop ASUS', 2, 8500000, 17000000)",
                "('Mouse Wireless', 5, 150000, 750000)",
                "('Keyboard Mechanical', 3, 750000, 2250000)",
                "('Monitor 24 inch', 1, 2500000, 2500000)",
                "('Webcam HD', 4, 300000, 1200000)"
            ];
            
            foreach ($sample_data as $data) {
                mysqli_query($koneksi_temp, "INSERT INTO transaksi (nama_barang, jumlah, harga, total) VALUES $data");
            }
        }
        
        // Koneksi ulang dengan database
        $koneksi = mysqli_connect($host, $username, $password, $database);
    } else {
        die("Koneksi ke MySQL gagal. Pastikan MySQL berjalan di XAMPP.");
    }
}

// Query untuk mengambil data transaksi
$query = "SELECT * FROM transaksi ORDER BY tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
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
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .total-row {
            background-color: #2ecc71 !important;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
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
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            text-align: center;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex: 1;
            margin: 0 10px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation - HOME DIUBAH MENJADI home.php -->
        <div class="nav">
            <a href="home.php">🏠 Home</a>
            <a href="barang.php">🧾 Manajemen Barang</a>
            <a href="pencarian.php">🔍 Pencarian Barang</a>
            <a href="transaksi.php">💰 Transaksi</a>
            <a href="laporan.php">📊 Laporan</a>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>📊 Laporan Transaksi</h1>
            <p>Laporan lengkap semua transaksi yang telah dilakukan</p>
        </div>

        <!-- Statistics -->
        <?php
        // Hitung statistik
        $total_transaksi = 0;
        $total_barang = 0;
        $grand_total = 0;
        
        if ($result && mysqli_num_rows($result) > 0) {
            // Reset pointer result
            mysqli_data_seek($result, 0);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $total_transaksi++;
                $total_barang += $row['jumlah'];
                $grand_total += $row['total'];
            }
            
            // Reset pointer lagi untuk tabel
            mysqli_data_seek($result, 0);
        }
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_transaksi; ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_barang; ?></div>
                <div class="stat-label">Total Barang Terjual</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>

        <!-- Tabel Laporan Transaksi -->
        <div style="background: white; padding: 25px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h2>📋 Detail Transaksi</h2>
            
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>NAMA BARANG</th>
                        <th>JUMLAH</th>
                        <th>HARGA SATUAN</th>
                        <th>TOTAL</th>
                        <th>TANGGAL</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>TOTAL KESELURUHAN:</strong></td>
                        <td colspan="3"><strong>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></strong></td>
                    </tr>
                </table>
                
                <!-- Tombol Export -->
                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()" style="background-color: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        🖨️ Cetak Laporan
                    </button>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #7f8c8d; font-size: 16px;">
                    📝 Belum ada data transaksi. 
                    <br><br>
                    <a href="transaksi.php" style="color: #3498db; text-decoration: none; font-weight: bold;">
                        ➕ Klik di sini untuk membuat transaksi pertama
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Antlerset Dafner Belanja Britech</p>
            <p style="font-size: 12px; margin-top: 5px;">Laporan dihasilkan pada: <?php echo date('d-m-Y H:i:s'); ?></p>
        </div>
    </div>

    <script>
        // Auto refresh setiap 30 detik untuk update data real-time
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
// Tutup koneksi
if (isset($koneksi)) {
    mysqli_close($koneksi);
}