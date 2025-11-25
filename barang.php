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
        
        // Buat tabel barang jika belum ada
        $create_table = "CREATE TABLE IF NOT EXISTS barang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_barang VARCHAR(100) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            stok INT NOT NULL
        )";
        mysqli_query($koneksi_temp, $create_table);
        
        // Koneksi ulang dengan database
        $koneksi = mysqli_connect($host, $username, $password, $database);
    } else {
        die("Koneksi ke MySQL gagal. Pastikan MySQL berjalan di XAMPP.");
    }
}

// Handle form submission untuk tambah barang
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_barang'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $query = "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama_barang', '$harga', '$stok')";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Barang berhasil ditambahkan!";
        header("Location: barang.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Handle hapus barang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM barang WHERE id = $id";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Barang berhasil dihapus!";
        header("Location: barang.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// Query untuk mengambil data barang
$query = "SELECT * FROM barang ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Barang</title>
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
        .btn-edit {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin-right: 5px;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
        .btn-edit:hover {
            background-color: #2980b9;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9em;
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

        <h1>🧾 Manajemen Barang</h1>

        <!-- Statistics -->
        <?php
        // Hitung statistik
        $total_barang = 0;
        $total_stok = 0;
        $total_nilai = 0;
        
        if ($result) {
            // Reset pointer untuk menghitung ulang
            mysqli_data_seek($result, 0);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $total_barang++;
                $total_stok += $row['stok'];
                $total_nilai += ($row['harga'] * $row['stok']);
            }
            
            // Reset pointer lagi untuk tabel
            mysqli_data_seek($result, 0);
        }
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_barang; ?></div>
                <div class="stat-label">Total Jenis Barang</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_stok; ?></div>
                <div class="stat-label">Total Stok Barang</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Rp <?php echo number_format($total_nilai, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Nilai Inventaris</div>
            </div>
        </div>
        
        <!-- Form Input Barang -->
        <div class="form-section">
            <h2>➕ Tambah Barang Baru</h2>
            
            <?php if (isset($success)): ?>
                <div class="success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="tambah_barang" value="1">
                <div class="form-group">
                    <label>Nama Barang:</label>
                    <input type="text" name="nama_barang" placeholder="Masukkan nama barang" required>
                </div>
                
                <div class="form-group">
                    <label>Harga:</label>
                    <input type="number" name="harga" min="0" placeholder="Harga barang" required>
                </div>
                
                <div class="form-group">
                    <label>Stok:</label>
                    <input type="number" name="stok" min="0" placeholder="Jumlah stok" required>
                </div>
                
                <input type="submit" value="💾 Simpan Barang">
            </form>
        </div>

        <!-- Tabel Data Barang -->
        <div class="form-section">
            <h2>📋 Daftar Barang</h2>
            
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>NAMA BARANG</th>
                        <th>HARGA</th>
                        <th>STOK</th>
                        <th>AKSI</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['stok']; ?></td>
                        <td>
                            <a href="edit_barang.php?id=<?php echo $row['id']; ?>" class="btn-edit">✏️ Edit</a>
                            <a href="?hapus=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin hapus barang <?php echo $row['nama_barang']; ?>?')">🗑️ Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 30px; color: #7f8c8d; font-size: 16px;">
                    📝 Belum ada data barang. Silakan tambah barang baru di atas.
                </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Antlerset Dafner Belanja Britech</p>
        </div>
    </div>

    <script>
        // Auto focus pada input pertama
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[name="nama_barang"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi
if (isset($koneksi)) {
    mysqli_close($koneksi);
}