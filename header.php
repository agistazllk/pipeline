<?php 
// 1. Wajib di awal setiap file PHP yang menggunakan session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// 2. Gunakan include_once untuk mencegah pemanggilan koneksi berulang
// ASUMSI: file ini (header.php) berada di root folder bersama koneksi.php
include_once 'koneksi.php'; 

// Hitung total item di keranjang (Menggunakan array_sum untuk kuantitas)
// Jika $_SESSION['keranjang'] belum diinisialisasi, totalnya 0
$total_item_keranjang = isset($_SESSION['keranjang']) ? array_sum($_SESSION['keranjang']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panen Raya - Toko Buah Segar Online</title>
    
    <link rel="stylesheet" href="style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <meta name="description" content="Panen Raya menyediakan buah-buahan segar, sayur, dan bahan pokok berkualitas premium. Jaminan kesegaran dan harga terbaik.">
    <meta property="og:title" content="Panen Raya: Belanja Buah Online Segar">
    <meta property="og:description" content="Pesan buah segar, langsung dari petani. Kualitas premium, cepat sampai.">
    <meta property="og:type" content="website">
    <link rel="icon" href="favicon.ico" type="image/x-icon"> 
</head>
<body>

    <header class="main-header">
        <div class="container">
            <h1><a href="index.php">Panen Raya 🍊</a></h1> 
            
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="kontak.php">Contact</a></li>
                    
                    <li>
                        <a href="keranjang.php" class="btn-keranjang">
                            🛒 Keranjang (<?= $total_item_keranjang; ?>)
                        </a>
                    </li>
                    
                    <li><a href="admin/admin_login.php">Admin Login</a></li> 
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="main-content container">