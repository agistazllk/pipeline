<?php
// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
include '../koneksi.php';

// ====== VALIDASI LOGIN ADMIN ======
if (!isset($_SESSION['is_admin_login']) || $_SESSION['is_admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// ====== CEK ID PRODUK ======
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_produk = $koneksi->real_escape_string($_GET['id']);

// ====== AMBIL GAMBAR DARI DATABASE ======
$sql_gambar = "SELECT gambar FROM produk WHERE id_produk = '$id_produk' LIMIT 1";
$res_gambar = $koneksi->query($sql_gambar);

if ($res_gambar->num_rows < 1) {
    $_SESSION['error_hapus'] = "Produk tidak ditemukan.";
    header("Location: admin_dashboard.php");
    exit();
}

$data = $res_gambar->fetch_assoc();
$nama_file_gambar = $data['gambar'];

// ====== HAPUS PRODUK DARI DATABASE ======
$sql_hapus = "DELETE FROM produk WHERE id_produk = '$id_produk'";

if ($koneksi->query($sql_hapus) === TRUE) {

    // ====== HAPUS FILE GAMBAR DI FOLDER ======
    $folder = "../gambar/";
    $path_file = $folder . $nama_file_gambar;

    if (file_exists($path_file)) {
        unlink($path_file);
    }

    $_SESSION['notif_sukses'] = "Produk berhasil dihapus.";
    header("Location: admin_dashboard.php");
    exit();

} else {

    $_SESSION['error_hapus'] = "Gagal menghapus produk: " . $koneksi->error;
    header("Location: admin_dashboard.php");
    exit();
}
?>
