<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../koneksi.php';

// FIX SESSION LOGIN
if (!isset($_SESSION['is_admin_login']) || $_SESSION['is_admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['submit_edit'])) {

    $id_produk = $koneksi->real_escape_string($_POST['id_produk']);
    $nama_buah = $koneksi->real_escape_string($_POST['nama_buah']);
    $harga     = $koneksi->real_escape_string($_POST['harga']);
    $stok      = $koneksi->real_escape_string($_POST['stok']);
    $deskripsi = $koneksi->real_escape_string($_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];

    $gambar_baru = $gambar_lama;

    // Jika memilih upload gambar baru
    if ($_FILES['gambar']['error'] !== 4) {

        $folder = "../gambar/";
        $filename = basename($_FILES["gambar"]["name"]);
        $path = $folder . $filename;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $_SESSION['error_edit'] = "Format gambar tidak valid!";
            header("Location: admin_edit.php?id=$id_produk");
            exit();
        }

        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $path)) {
            // Hapus file lama
            if ($gambar_lama != $filename && file_exists($folder . $gambar_lama)) {
                unlink($folder . $gambar_lama);
            }
            $gambar_baru = $filename;
        } else {
            $_SESSION['error_edit'] = "Gagal upload gambar.";
            header("Location: admin_edit.php?id=$id_produk");
            exit();
        }
    }

    // Update ke database
    $sql = "UPDATE produk SET
                nama_buah = '$nama_buah',
                harga = '$harga',
                stok = '$stok',
                deskripsi = '$deskripsi',
                gambar = '$gambar_baru'
            WHERE id_produk = '$id_produk'";

    if ($koneksi->query($sql)) {
        $_SESSION['notif_sukses'] = "Produk berhasil diperbarui.";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error_edit'] = "Gagal update database.";
        header("Location: admin_edit.php?id=$id_produk");
        exit();
    }

} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
