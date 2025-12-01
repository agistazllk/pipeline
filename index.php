<?php
echo "Agista Aja"
?>
<?php 
// Panggil header.php (Diasumsikan sudah memulai session dan memanggil koneksi.php)
include 'header.php'; 
?>

<section class="hero-section">
    <div class="container">
        <h2>Buah & Sayur Segar Langsung ke Dapur Anda! agista</h2>
        <p>Gratis Ongkir untuk wilayah tertentu. Jaminan Kesegaran 100%</p>
        <a href="#produk" class="btn-hero">Lihat Semua Produk</a>
    </div>
</section>

<div class="container">
    
    <?php 
    // Tampilkan Notifikasi Flash setelah Menambah ke Keranjang
    if (isset($_SESSION['notif_keranjang'])) {
        echo '<div class="alert success">' . $_SESSION['notif_keranjang'] . '</div>';
        unset($_SESSION['notif_keranjang']); 
    }
    ?>

    <h2 id="produk" class="section-title">Daftar Buah Segar Pilihan Hari Ini</h2>
    
    <div class="product-grid">
        <?php
        // Query untuk mengambil semua produk yang stoknya > 0
        $query = "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_buah ASC";
        
        // Asumsi $koneksi didefinisikan di 'header.php' (atau 'config_db.php')
        if (isset($koneksi)) {
            $result = $koneksi->query($query);
        } else {
            $result = null; 
            echo "<p class='no-product-message' style='color: red;'>⚠️ Database tidak terhubung. Cek file koneksi.php Anda!</p>";
        }


        if ($result && $result->num_rows > 0) {
            // Lakukan looping untuk menampilkan setiap produk
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="product-item">
                    <img src="gambar/<?= $row['gambar']; ?>" alt="<?= $row['nama_buah']; ?>">
                    <h3><?= $row['nama_buah']; ?></h3>
                    
                    <p class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?>/kg</p>
                    
                    <p><small>Stok Tersedia: <?= $row['stok']; ?> kg</small></p>

                    <a href="tambah_keranjang.php?id=<?= $row['id_produk']; ?>" class="btn-pesan">Tambah ke Keranjang</a>
                </div>
                <?php
            }
        } else if ($result && $result->num_rows == 0) {
            echo "<p class='no-product-message'>Maaf, belum ada produk buah yang tersedia saat ini.</p>";
        }
        ?>
    </div>
</div>

<?php
// Panggil footer.php
include 'footer.php'; 
?>
