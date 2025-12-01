<?php
// Pengaturan Koneksi Database
$servername = "localhost";
$username = "root";     // GANTI
$password = "";         // GANTI
$dbname = "db_buah";    // GANTI (Berdasarkan screenshot Anda: Database_db_buah)

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi dan hentikan skrip jika gagal
if ($conn->connect_error) {
    die("❌ Koneksi Database Gagal: " . $conn->connect_error);
}
?>