<?php 
// Pastikan sesi dimulai dan header/footer dimuat
session_start();
include 'header.php'; // Atau file yang memuat koneksi dan layout
?>

<div class="container-terima-kasih" style="text-align: center; padding: 50px;">
    
    <h2>✅ Pesanan Anda Berhasil Dibuat!</h2>
    <p>Terima kasih telah berbelanja.</p>

    <?php 
    // Ambil ID transaksi dari URL (query parameter)
    $id_transaksi = $_GET['id'] ?? 'N/A'; 
    
    // Tampilkan notifikasi sukses yang diset di proses_checkout.php
    if (isset($_SESSION['notif_sukses'])) {
        echo '<div class="alert success" style="max-width: 600px; margin: 20px auto; background-color: #e6ffe6; border: 1px solid #4CAF50; padding: 15px; border-radius: 5px;">';
        echo '<p style="color: #333;">' . $_SESSION['notif_sukses'] . '</p>';
        echo '</div>';
        unset($_SESSION['notif_sukses']); 
    }
    ?>

    <p>Nomor Transaksi Anda adalah: <strong>#T<?= htmlspecialchars($id_transaksi); ?></strong></p>
    <p>Admin akan segera menghubungi Anda melalui telepon yang terdaftar untuk mengonfirmasi total biaya kirim.</p>
    
    <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
        Kembali ke Beranda
    </a>

</div>

<?php 
include 'footer.php'; 
?>