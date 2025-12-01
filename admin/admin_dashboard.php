<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../koneksi.php';

// Proteksi Login Admin
if (!isset($_SESSION['is_admin_login']) || $_SESSION['is_admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        /* Tambahan agar dashboard tampil seperti tampilan utama */
        .admin-header {
            background: #4CAF50;
            padding: 15px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h2 {
            margin: 0;
        }
        .admin-header a {
            color: white;
            background: #e53935;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .admin-header a:hover {
            background: #c62828;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <!-- HEADER ADMIN -->
    <div class="admin-header">
        <h2>Dashboard Admin — Selamat Datang, <?= htmlspecialchars($admin_username); ?></h2>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="container">
        <h3>Manajemen Produk</h3>

        <p>
            <a href="admin_tambah.php" class="btn">+ Tambah Produk Baru</a>
        </p>

        <!-- TABEL PRODUK -->
        <?php
        $result = $koneksi->query("SELECT * FROM produk ORDER BY id_produk DESC");

        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='10' cellspacing='0' width='100%'>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Buah</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td>{$row['id_produk']}</td>
                    <td><img src='../gambar/{$row['gambar']}' width='80'></td>
                    <td>{$row['nama_buah']}</td>
                    <td>Rp " . number_format($row['harga'],0,',','.') . "</td>
                    <td>{$row['stok']}</td>
                    <td>
                        <a href='admin_edit.php?id={$row['id_produk']}'>Edit</a> |
                        <a href='proses_hapus.php?id={$row['id_produk']}' onclick='return confirm(\"Hapus produk ini?\")'>Hapus</a>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Belum ada produk.</p>";
        }
        ?>

    </div>

</body>
</html>
