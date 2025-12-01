<?php
session_start();
if (isset($_SESSION['is_logged_in'])) {
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
}
.navbar {
    background: #43A047;
    padding: 15px;
    color: white;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
}
.container {
    max-width: 400px;
    margin: 60px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}
h2 { text-align: center; color: #2E7D32; margin-bottom: 20px; }
label { font-weight: bold; color: #333; }
input[type="text"], input[type="password"] {
    width: 100%; padding: 12px; margin-top: 6px; margin-bottom: 18px;
    border: 1px solid #ccc; border-radius: 6px; font-size: 15px;
}
button {
    width: 100%; background: #FFC107; color: black; padding: 12px;
    border: none; border-radius: 6px; font-size: 16px; cursor: pointer;
    font-weight: bold;
}
button:hover { background: #e0a800; }
.error {
    background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px;
    margin-bottom: 15px; border: 1px solid #f5c6cb;
}
.back { display: block; text-align: center; margin-top: 15px; color: #2E7D32; text-decoration: none; font-weight: bold; }
</style>
</head>
<body>

<div class="navbar">Admin Login</div>
<div class="container">
<h2>Login Admin</h2>

<?php
if (isset($_SESSION['error_login'])) {
    echo "<div class='error'>".$_SESSION['error_login']."</div>";
    unset($_SESSION['error_login']);
}
?>

<form action="proses_login.php" method="POST">
    <label>Username:</label>
    <input type="text" name="username" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="submit_login">LOGIN</button>
</form>
<a href="../index.php" class="back">← Kembali ke Beranda</a>
</div>
</body>
</html>
