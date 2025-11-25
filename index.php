<?php
// KONEKSI DATABASE LANGSUNG - UNTUK STATISTIK
$host = "localhost";
$username = "root";
$password = "";
$database = "rpl";

// Buat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Hitung statistik jika koneksi berhasil
$total_barang = 0;
$total_transaksi = 0;
$total_stok = 0;

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
    
    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventoPro - Sistem Manajemen Inventori Modern</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styles */
        .header {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }
        
        .logo {
            font-size: 4em;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .header h1 {
            font-size: 3.5em;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .tagline {
            font-size: 1.4em;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Navigation */
        .nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .nav-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 2px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .nav-link.primary {
            background: #e74c3c;
            border-color: #e74c3c;
        }
        
        .nav-link.primary:hover {
            background: #c0392b;
            transform: translateY(-3px);
        }
        
        /* Hero Section */
        .hero {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .hero h2 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .hero p {
            color: #7f8c8d;
            font-size: 1.2em;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 30px;
        }
        
        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
        }
        
        .stat-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .feature-card p {
            color: #7f8c8d;
            line-height: 1.6;
        }
        
        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 60px 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(231, 76, 60, 0.3);
        }
        
        .cta h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .cta p {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-cta {
            background: white;
            color: #e74c3c;
            padding: 18px 35px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: white;
            opacity: 0.8;
        }
        
        .footer p {
            margin-bottom: 5px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5em;
            }
            
            .tagline {
                font-size: 1.1em;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-link {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }
            
            .hero {
                padding: 30px 20px;
            }
            
            .hero h2 {
                font-size: 2em;
            }
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header fade-in">
            <div class="logo">🚀</div>
            <h1>InventoPro</h1>
            <p class="tagline">
                Transformasi Digital Manajemen Inventori Anda - 
                Lebih Cerdas, Lebih Cepat, Lebih Efisien
            </p>
        </header>

        <!-- Navigation -->
        <nav class="nav slide-in">
            <div class="nav-links">
                <a href="barang.php" class="nav-link">🏠 Dashboard</a>
                <a href="barang.php" class="nav-link">🧾 Manajemen Barang</a>
                <a href="pencarian.php" class="nav-link">🔍 Pencarian Cerdas</a>
                <a href="transaksi.php" class="nav-link primary">💰 Mulai Transaksi</a>
                <a href="laporan.php" class="nav-link">📊 Analytics</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero fade-in">
            <h2>✨ Selamat Datang di Era Baru Manajemen Inventori</h2>
            <p>
                <strong>InventoPro</strong> menghadirkan revolusi dalam mengelola inventaris bisnis Anda. 
                Dengan teknologi terkini dan antarmuka yang intuitif, kelola stok, pantau transaksi, 
                dan optimalkan operasional bisnis Anda dalam genggaman.
            </p>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-number"><?php echo $total_barang; ?></div>
                    <div class="stat-label">Produk Terdaftar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number"><?php echo $total_transaksi; ?></div>
                    <div class="stat-label">Transaksi Sukses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-number"><?php echo $total_stok; ?></div>
                    <div class="stat-label">Total Stok</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime System</div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="feature-card slide-in">
                <div class="feature-icon">🤖</div>
                <h3>Kecerdasan Buatan</h3>
                <p>
                    Sistem prediktif yang membantu forecasting stok dan rekomendasi 
                    pembelian berdasarkan pola historis data.
                </p>
            </div>
            <div class="feature-card slide-in">
                <div class="feature-icon">📱</div>
                <h3>Real-time Sync</h3>
                <p>
                    Update data secara real-time di semua perangkat. Perubahan stok 
                    langsung terpantau tanpa delay.
                </p>
            </div>
            <div class="feature-card slide-in">
                <div class="feature-icon">🔒</div>
                <h3>Keamanan Enterprise</h3>
                <p>
                    Enkripsi end-to-end dan backup otomatis menjaga data bisnis Anda 
                    tetap aman dan terlindungi.
                </p>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta fade-in">
            <h2>🚀 Siap Mengoptimalkan Bisnis Anda?</h2>
            <p>
                Bergabung dengan ratusan bisnis yang telah bertransformasi digital. 
                Mulai perjalanan efisiensi Anda sekarang!
            </p>
            <a href="transaksi.php" class="btn-cta">
                💫 Mulai Sekarang - Gratis!
            </a>
        </section>

        <!-- Testimonials Section -->
        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Testimoni Pengguna</h3>
                <p>
                    <em>"InventoPro mengubah cara kami mengelola inventori. Efisiensi meningkat 300%!"</em>
                    <br><br>
                    <strong>- Sarah, Manager Retail</strong>
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3>Penghargaan</h3>
                <p>
                    <em>Best Inventory System 2024 - Tech Innovation Awards</em>
                    <br><br>
                    Terbukti dan terpercaya oleh industri
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <h3>Global Reach</h3>
                <p>
                    Digunakan oleh bisnis di 15+ negara. Support multi-bahasa 
                    dan multi-mata uang.
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2025 InventoPro - Revolutionizing Inventory Management</p>
            <p>Dibuat dengan ❤️ oleh Tim Inovasi Teknologi</p>
            <p style="margin-top: 15px; font-size: 0.9em;">
                🏆 Best Innovation Award 2024 | 🚀 Startup of The Year | 💫 Customer Choice
            </p>
        </footer>
    </div>

    <script>
        // Animasi scroll
        document.addEventListener('DOMContentLoaded', function() {
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

            // Observe semua elemen dengan class fade-in dan slide-in
            document.querySelectorAll('.fade-in, .slide-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // Efek ketik untuk tagline
            const tagline = document.querySelector('.tagline');
            if (tagline) {
                const text = tagline.textContent;
                tagline.textContent = '';
                let i = 0;
                
                function typeWriter() {
                    if (i < text.length) {
                        tagline.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeWriter, 50);
                    }
                }
                
                setTimeout(typeWriter, 1000);
            }
        });
    </script>
</body>
</html>