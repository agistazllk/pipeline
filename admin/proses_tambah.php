<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php';

// Validasi login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Jika tombol submit tidak ditekan
if (!isset($_POST['submit_tambah'])) {
    $_SESSION['error_tambah'] = "Akses tidak valid!";
    header("Location: tambah_produk.php");
    exit();
}

// Ambil data form
$nama_buah = $_POST['nama_buah'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$deskripsi = $_POST['deskripsi'];

// Validasi file upload
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== 0) {
    $_SESSION['error_tambah'] = "Gambar wajib diupload!";
    header("Location: tambah_produk.php");
    exit();
}

$gambar = $_FILES['gambar'];
$nama_file = $gambar['name'];
$tmp_file = $gambar['tmp_name'];
$ukuran_file = $gambar['size'];

$ext_valid = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

if (!in_array($ext, $ext_valid)) {
    $_SESSION['error_tambah'] = "Format gambar hanya JPG atau PNG!";
    header("Location: tambah_produk.php");
    exit();
}

if ($ukuran_file > 500000) { 
    $_SESSION['error_tambah'] = "Ukuran gambar maksimal 500KB!";
    header("Location: tambah_produk.php");
    exit();
}

// Generate nama file unik
$nama_baru = uniqid() . "." . $ext;

// Pindahkan file gambar ke folder
$folder_tujuan = "../gambar_produk/";

if (!is_dir($folder_tujuan)) {
    mkdir($folder_tujuan, 0777, true);
}

if (!move_uploaded_file($tmp_file, $folder_tujuan . $nama_baru)) {
    $_SESSION['error_tambah'] = "Gagal mengupload gambar!";
    header("Location: tambah_produk.php");
    exit();
}

// Insert ke database
$sql = "INSERT INTO produk (nama_buah, harga, stok, deskripsi, gambar)
        VALUES ('$nama_buah', '$harga', '$stok', '$deskripsi', '$nama_baru')";

if (mysqli_query($koneksi, $sql)) {
    header("Location: admin_dashboard.php?status=sukses_tambah");
    exit();
} else {
    $_SESSION['error_tambah'] = "Gagal menambah produk: " . mysqli_error($koneksi);
    header("Location: tambah_produk.php");
    exit();
}
?>
