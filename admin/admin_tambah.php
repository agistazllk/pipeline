<?php 
// Memulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// Memanggil koneksi database
include '../koneksi.php'; 

// LOGIKA PENGAMANAN WAJIB
// Gunakan session yang sama dengan admin_dashboard.php
if (!isset($_SESSION['is_admin_login']) || $_SESSION['is_admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru - Admin</title>
    <link rel="stylesheet" href="../style.css"> 
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input[type="text"], 
        .form-group input[type="number"], 
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Tambah Produk Baru 🥕</h2>
        <p><a href="admin_dashboard.php">&larr; Kembali ke Dashboard</a></p>

        <?php
        // Menampilkan pesan error dari session jika ada
        if (isset($_SESSION['error_tambah'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error_tambah'] . '</div>';
            unset($_SESSION['error_tambah']); 
        }
        ?>

        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nama_buah">Nama Buah:</label>
                <input type="text" id="nama_buah" name="nama_buah" required>
            </div>
            
            <div class="form-group">
                <label for="harga">Harga (per kg/satuan):</label>
                <input type="number" id="harga" name="harga" required min="1000">
            </div>
            
            <div class="form-group">
                <label for="stok">Stok (kg/satuan):</label>
                <input type="number" id="stok" name="stok" required min="1">
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi Produk:</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Produk (Max 500KB, JPG/PNG):</label>
                <input type="file" id="gambar" name="gambar" required>
            </div>
            
            <button type="submit" name="submit_tambah" class="btn-submit">Simpan Produk</button>
            
        </form>
    </div>

</body>
</html>
