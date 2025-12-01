<?php
// proses_login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include koneksi (sesuaikan path)
include_once '../koneksi.php';

// Pastikan form dikirim
if (!isset($_POST['submit_login'])) {
    header('Location: admin_login.php');
    exit;
}

// Cek koneksi
if (!$koneksi || mysqli_connect_errno()) {
    $_SESSION['error_login'] = "Koneksi database gagal. Cek koneksi.php";
    header('Location: admin_login.php');
    exit;
}

// Ambil input (trim)
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Simpan username agar bisa ditampilkan ulang jika gagal
$_SESSION['old_username'] = $username;

// Validasi singkat
if ($username === '' || $password === '') {
    $_SESSION['error_login'] = "Username dan password wajib diisi!";
    header('Location: admin_login.php');
    exit;
}

// Prepared statement: ambil id, username, password dari DB berdasarkan username
$stmt = $koneksi->prepare("SELECT id_admin, username, password FROM admin WHERE username = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['error_login'] = "Kesalahan sistem (prepare statement).";
    header('Location: admin_login.php');
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    // Username tidak ditemukan
    $_SESSION['error_login'] = "Username tidak ditemukan!";
    $stmt->close();
    header('Location: admin_login.php');
    exit;
}

$stmt->bind_result($id_admin, $username_db, $password_db);
$stmt->fetch();
$stmt->close();

// Bandingkan plain text password
if ($password === $password_db) {
    // Login sukses: set session
    $_SESSION['is_admin_login'] = true;
    $_SESSION['admin_id'] = $id_admin;
    $_SESSION['admin_username'] = $username_db;

    // hapus old username jika ada
    if (isset($_SESSION['old_username'])) unset($_SESSION['old_username']);

    header('Location: admin_dashboard.php');
    exit;
} else {
    $_SESSION['error_login'] = "Password salah!";
    header('Location: admin_login.php');
    exit;
}
?>
