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
        
        // Insert contoh data jika tabel kosong
        $check_data = "SELECT COUNT(*) as total FROM barang";
        $result_check = mysqli_query($koneksi_temp, $check_data);
        $row = mysqli_fetch_assoc($result_check);
        
        if ($row['total'] == 0) {
            // Tambahkan data contoh
            $sample_data = [
                "('Laptop ASUS ROG', 15000000, 5)",
                "('Mouse Wireless Logitech', 250000, 20)",
                "('Keyboard Mechanical', 750000, 10)",
                "('Monitor 24 inch', 2500000, 8)",
                "('Webcam HD 1080p', 500000, 15)",
                "('Headphone Gaming', 800000, 12)",
                "('Printer Epson', 1200000, 6)",
                "('SSD 500GB', 600000, 25)"
            ];
            
            foreach ($sample_data as $data) {
                mysqli_query($koneksi_temp, "INSERT INTO barang (nama_barang, harga, stok) VALUES $data");
            }
        }
        
        // Koneksi ulang dengan database
        $koneksi = mysqli_connect($host, $username, $password, $database);
    } else {
        die("Koneksi ke MySQL gagal. Pastikan MySQL berjalan di XAMPP.");
    }
}

// Handle pencarian
$keyword = "";
$results = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $keyword = trim($_GET['search']);
    $search_query = "SELECT * FROM barang WHERE 
                    nama_barang LIKE '%$keyword%'
                    ORDER BY nama_barang";
    $result = mysqli_query($koneksi, $search_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pencarian Barang</title>
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
        input[type="text"] {
            width: 100%;
            max-width: 500px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input[type="submit"] {
            background-color: #e74c3c;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #c0392b;
        }
        .search-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            text-align: center;
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
        .results-count {
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 15px;
        }
        .add-to-cart {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-to-cart:hover {
            background-color: #219a52;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 18px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        <h1>🔍 Pencarian Barang</h1>
        
        <!-- Search Form -->
        <div class="search-section">
            <h2>Cari Barang</h2>
            <form method="GET" action="">
                <div class="form-group">
                    <input type="text" 
                           name="search" 
                           placeholder="Masukkan nama barang yang dicari..." 
                           value="<?php echo htmlspecialchars($keyword); ?>"
                           required
                           autofocus>
                </div>
                <input type="submit" value="🔍 Cari Barang">
            </form>
        </div>

        <!-- Results Section -->
        <?php if (!empty($keyword)): ?>
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2>Hasil Pencarian</h2>
                
                <div class="results-count">
                    Ditemukan <?php echo count($results); ?> hasil untuk "<?php echo htmlspecialchars($keyword); ?>"
                </div>
                
                <?php if (!empty($results)): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>NAMA BARANG</th>
                            <th>HARGA</th>
                            <th>STOK</th>
                            <th>AKSI</th>
                        </tr>
                        <?php foreach ($results as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['nama_barang']; ?></td>
                            <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $product['stok']; ?></td>
                            <td>
                                <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo $product['nama_barang']; ?>')">
                                    🛒 Tambah ke Cart
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div class="no-results">
                        <p>😔 Tidak ditemukan barang dengan kata kunci "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</p>
                        <p style="margin-top: 15px;">Silakan coba dengan kata kunci lain.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>🔍 Silakan masukkan kata kunci pencarian di atas</p>
                <p style="margin-top: 15px;">Contoh: laptop, mouse, keyboard, monitor</p>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 Antlerset Dafner Belanja Britech</p>
        </div>
    </div>

    <script>
        function addToCart(productId, productName) {
            if (confirm('Tambahkan "' + productName + '" ke cart?')) {
                // Simpan ke localStorage (sementara)
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Cek apakah produk sudah ada di cart
                const existingItem = cart.find(item => item.id === productId);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({
                        id: productId,
                        name: productName,
                        quantity: 1
                    });
                }
                
                localStorage.setItem('cart', JSON.stringify(cart));
                
                alert('✅ "' + productName + '" berhasil ditambahkan ke cart!');
            }
        }

        // Focus pada input pencarian saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
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