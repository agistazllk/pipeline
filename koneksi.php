<?php
// Pengaturan Koneksi Database
// Ubah nilai di bawah ini sesuai dengan konfigurasi server lokal Anda (XAMPP/MAMP)
$host = "localhost";
$user = "root";     // Biasanya 'root' jika menggunakan XAMPP/MAMP
$pass = "";         // Biasanya kosong jika menggunakan XAMPP/MAMP
$db = "db_buah";    // Ganti dengan NAMA DATABASE YANG ANDA BUAT DI PHPMYADMIN

// Buat koneksi menggunakan objek mysqli
$koneksi = new mysqli($host, $user, $pass, $db);

// Cek koneksi: jika ada error, hentikan script dan tampilkan pesan
if ($koneksi->connect_error) {
    // Tampilkan error koneksi yang jelas
    die("❌ Koneksi database gagal! Pastikan Database '{$db}' sudah dibuat dan berjalan. Error: " . $koneksi->connect_error);
}

// Opsional: Set karakter set menjadi utf8mb4 agar mendukung emoji dan karakter khusus
$koneksi->set_charset("utf8mb4");

// HINDARI TAG PENUTUP PHP (?>