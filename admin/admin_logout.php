<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// kosongkan session dan destroy
$_SESSION = array();
session_destroy();

// kembali ke halaman utama toko
header('Location: ../index.php');
exit;
