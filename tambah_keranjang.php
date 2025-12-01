<?php
// Wajib: Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// Cek apakah ID produk dikirim melalui URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $id_produk = $_GET['id'];
    
    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = array();
    }
    
    // Logika penambahan item ke keranjang:
    
    // Jika produk sudah ada di keranjang, tambahkan kuantitasnya
    if (array_key_exists($id_produk, $_SESSION['keranjang'])) {
        $_SESSION['keranjang'][$id_produk] += 1;
        $pesan = "Kuantitas produk berhasil ditambahkan!";
    } 
    // Jika produk belum ada di keranjang, tambahkan dengan kuantitas 1
    else {
        $_SESSION['keranjang'][$id_produk] = 1;
        $pesan = "Produk berhasil ditambahkan ke keranjang!";
    }
    
    // Simpan pesan notifikasi ke session (opsional)
    $_SESSION['notif_keranjang'] = $pesan;
    
}

// Redirect pengguna kembali ke halaman utama toko
header("Location: index.php");
exit();
?>