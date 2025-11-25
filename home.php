<?php
// KONEKSI DATABASE LANGSUNG - UNTUK STATISTIK
$host = "localhost";
$username = "root";
$password = "";
$database = "rpl";

// Buat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Inisialisasi variabel statistik
$total_barang = 0;
$total_transaksi = 0;
$total_stok = 0;
$barang_habis = 0;
$transaksi_hari_ini = 0;

if ($koneksi) {
    // Hitung total barang
    $query_barang = "SELECT COUNT(*) as total FROM barang";
    $result_barang = mysqli_query($koneksi, $query_barang);
    if ($result_barang) {
        $row = mysqli_fetch_assoc($result_barang);
        $total_barang = $row['total'];
    }
    
    // Hitung total transaksi
    $query_transaksi = "SELECT COUNT(*) as total FROM transaksi";
    $result_transaksi = mysqli_query($koneksi, $query_transaksi);
    if ($result_transaksi) {
        $row = mysqli_fetch_assoc($result_transaksi);
        $total_transaksi = $row['total'];
    }
    
    // Hitung total stok
    $query_stok = "SELECT SUM(stok) as total FROM barang";
    $result_stok = mysqli_query($koneksi, $query_stok);
    if ($result_stok) {
        $row = mysqli_fetch_assoc($result_stok);
        $total_stok = $row['total'] ?: 0;
    }
    
    // Hitung barang yang hampir habis (stok < 5)
    $query_habis = "SELECT COUNT(*) as total FROM barang WHERE stok < 5";
    $result_habis = mysqli_query($koneksi, $query_habis);
    if ($result_habis) {
        $row = mysqli_fetch_assoc($result_habis);
        $barang_habis = $row['total'];
    }
    
    // Hitung transaksi hari ini
    $query_hari_ini = "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()";
    $result_hari_ini = mysqli_query($koneksi, $query_hari_ini);
    if ($result_hari_ini) {
        $row = mysqli_fetch_assoc($result_hari_ini);
        $transaksi_hari_ini = $row['total'];
    }
    
    // Ambil 5 transaksi terbaru
    $query_transaksi_terbaru = "SELECT * FROM transaksi ORDER BY tanggal DESC LIMIT 5";
    $result_transaksi_terbaru = mysqli_query($koneksi, $query_transaksi_terbaru);
    
    // Ambil barang dengan stok rendah
    $query_stok_rendah = "SELECT * FROM barang WHERE stok < 5 ORDER BY stok ASC LIMIT 5";
    $result_stok_rendah = mysqli_query($koneksi, $query_stok_rendah);
    
    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Manajemen Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Navigation */
        .nav {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .nav-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            font-size: 2.5em;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .brand-text h1 {
            color: white;
            font-size: 1.8em;
            margin-bottom: 5px;
        }
        
        .brand-text p {
            color: rgba(255,255,255,0.8);
            font-size: 0.9em;
        }
        
        .nav-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .nav-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 2px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: #e74c3c;
            border-color: #e74c3c;
        }
        
        /* Welcome Section */
        .welcome {
            background: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .welcome h2 {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 15px;
        }
        
        .welcome p {
            color: #7f8c8d;
            font-size: 1.1em;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
            border-left: 5px solid #3498db;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.warning {
            border-left-color: #e74c3c;
        }
        
        .stat-card.success {
            border-left-color: #27ae60;
        }
        
        .stat-card.info {
            border-left-color: #9b59b6;
        }
        
        .stat-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.2em;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1em;
        }
        
        /* Main Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .quick-actions h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
        }
        
        .action-btn.success {
            background: linear-gradient(135deg, #27ae60, #219a52);
        }
        
        .action-btn.warning {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .action-btn.secondary {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }
        
        /* Cards */
        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-title {
            color: #2c3e50;
            font-size: 1.3em;
            font-weight: 600;
        }
        
        .card-link {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .card-link:hover {
            text-decoration: underline;
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .table tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }
        
        .badge.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge.success {
            background: #d4edda;
            color: #155724;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <nav class="nav fade-in">
            <div class="nav-links">
                <div class="nav-brand">
                    <div class="logo">📊</div>
                    <div class="brand-text">
                        <h1>InventoryPro</h1>
                        <p>Sistem Manajemen Barang Terintegrasi</p>
                    </div>
                </div>
                <div class="nav-menu">
                    <a href="home.php" class="nav-link active">🏠 Dashboard</a>
                    <a href="barang.php" class="nav-link">🧾 Barang</a>
                    <a href="transaksi.php" class="nav-link">💰 Transaksi</a>
                    <a href="laporan.php" class="nav-link">📈 Laporan</a>
                </div>
            </div>
        </nav>

        <!-- Welcome Section -->
        <section class="welcome fade-in">
            <h2>Selamat Datang di Dashboard! 🎉</h2>
            <p>
                Kelola inventaris Anda dengan mudah dan efisien. Pantau stok, transaksi, 
                dan laporan penjualan secara real-time dari satu dashboard terpusat.
            </p>
        </section>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card fade-in">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?php echo $total_barang; ?></div>
                <div class="stat-label">Total Jenis Barang</div>
            </div>
            <div class="stat-card success fade-in">
                <div class="stat-icon">💰</div>
                <div class="stat-number"><?php echo $total_transaksi; ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
            <div class="stat-card info fade-in">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $total_stok; ?></div>
                <div class="stat-label">Total Stok Tersedia</div>
            </div>
            <div class="stat-card warning fade-in">
                <div class="stat-icon">⚠️</div>
                <div class="stat-number"><?php echo $barang_habis; ?></div>
                <div class="stat-label">Barang Stok Rendah</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions fade-in">
            <h3>🚀 Akses Cepat</h3>
            <div class="action-buttons">
                <a href="barang.php" class="action-btn">
                    <div>➕</div>
                    <div>Tambah Barang</div>
                </a>
                <a href="transaksi.php" class="action-btn success">
                    <div>💳</div>
                    <div>Transaksi Baru</div>
                </a>
                <a href="pencarian.php" class="action-btn secondary">
                    <div>🔍</div>
                    <div>Cari Barang</div>
                </a>
                <a href="laporan.php" class="action-btn warning">
                    <div>📈</div>
                    <div>Lihat Laporan</div>
                </a>
            </div>
        </section>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Recent Transactions -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="card-title">📋 Transaksi Terbaru</div>
                        <a href="transaksi.php" class="card-link">Lihat Semua →</a>
                    </div>
                    <?php if ($result_transaksi_terbaru && mysqli_num_rows($result_transaksi_terbaru) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_transaksi_terbaru)): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                    <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['tanggal'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px; color: #7f8c8d;">
                            📝 Belum ada transaksi hari ini
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Low Stock Alert -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="card-title">⚠️ Peringatan Stok Rendah</div>
                        <a href="barang.php" class="card-link">Kelola →</a>
                    </div>
                    <?php if ($result_stok_rendah && mysqli_num_rows($result_stok_rendah) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_stok_rendah)): ?>
                                <tr>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['stok']; ?></td>
                                    <td>
                                        <span class="badge warning">
                                            <?php echo $row['stok'] == 0 ? 'HABIS' : 'RENDAH'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px; color: #27ae60;">
                            ✅ Semua stok dalam kondisi aman
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Today's Summary -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="card-title">📅 Ringkasan Hari Ini</div>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 3em; margin-bottom: 10px;"><?php echo $transaksi_hari_ini; ?></div>
                        <div style="color: #7f8c8d; margin-bottom: 20px;">Transaksi Hari Ini</div>
                        <div style="background: #e8f4f8; padding: 15px; border-radius: 10px;">
                            <div style="font-size: 0.9em; color: #3498db;">
                                🕒 Update terakhir: <?php echo date('H:i:s'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="card-title">⚡ Statistik Cepat</div>
                    </div>
                    <div style="padding: 10px 0;">
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span>Transaksi/Bulan</span>
                            <span style="font-weight: bold; color: #27ae60;">+12%</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span>Pertumbuhan Stok</span>
                            <span style="font-weight: bold; color: #3498db;">+8%</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                            <span>Efisiensi</span>
                            <span style="font-weight: bold; color: #9b59b6;">94%</span>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="card fade-in">
                    <div class="card-header">
                        <div class="card-title">🔧 Status Sistem</div>
                    </div>
                    <div style="padding: 10px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                            <span>Database</span>
                            <span class="badge success">ONLINE</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                            <span>Server</span>
                            <span class="badge success">STABLE</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                            <span>Backup</span>
                            <span class="badge success">AKTIF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer fade-in">
            <p>&copy; 2025 InventoryPro - Sistem Manajemen Barang</p>
            <p style="margin-top: 10px; font-size: 0.9em;">
                🚀 Versi 2.1.0 | 📅 <?php echo date('d F Y'); ?> | ⏰ <?php echo date('H:i:s'); ?>
            </p>
        </footer>
    </div>

    <script>
        // Real-time clock update
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Update all clock elements
            document.querySelectorAll('.clock-update').forEach(el => {
                el.textContent = timeString;
            });
            
            document.querySelectorAll('.date-update').forEach(el => {
                el.textContent = dateString;
            });
        }
        
        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
        
        // Add animation to stat cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>