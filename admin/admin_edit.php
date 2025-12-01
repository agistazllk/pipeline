<?php 
// Memulai session dan koneksi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../koneksi.php'; 

// FIX SESSION LOGIN
if (!isset($_SESSION['is_admin_login']) || $_SESSION['is_admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Ambil ID produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_produk = $koneksi->real_escape_string($_GET['id']);

// Query produk
$query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
$result = $koneksi->query($query);

if ($result->num_rows == 0) {
    $_SESSION['error_tambah'] = "Produk tidak ditemukan.";
    header("Location: admin_dashboard.php");
    exit();
}

$data_produk = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">

    <h2>Edit Produk: <?= $data_produk['nama_buah']; ?></h2>

    <?php
    if (isset($_SESSION['error_edit'])) {
        echo '<div class="alert alert-error">' . $_SESSION['error_edit'] . '</div>';
        unset($_SESSION['error_edit']);
    }
    ?>

    <a href="admin_dashboard.php">← Kembali</a>

    <form action="proses_edit.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id_produk" value="<?= $data_produk['id_produk']; ?>">
        <input type="hidden" name="gambar_lama" value="<?= $data_produk['gambar']; ?>">

        <label>Nama Buah:</label>
        <input type="text" name="nama_buah" value="<?= $data_produk['nama_buah']; ?>" required>

        <label>Harga:</label>
        <input type="number" name="harga" value="<?= $data_produk['harga']; ?>" required>

        <label>Stok:</label>
        <input type="number" name="stok" value="<?= $data_produk['stok']; ?>" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="4"><?= $data_produk['deskripsi']; ?></textarea>

        <label>Gambar Saat Ini:</label><br>
        <img src="../gambar/<?= $data_produk['gambar']; ?>" width="120"><br><br>

        <label>Ganti Gambar:</label>
        <input type="file" name="gambar">

        <button type="submit" name="submit_edit">Update Produk</button>

    </form>

</div>

</body>
</html>
