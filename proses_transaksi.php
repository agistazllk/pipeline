<?php
// =========================================================================
// 1. PENGATURAN KONEKSI DATABASE
// =========================================================================
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = "";     // Ganti dengan password database Anda
$dbname = "nama_database_anda"; // Ganti dengan nama database yang Anda buat

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// =========================================================================
// 2. PROSES DATA FORMULIR
// =========================================================================
$total_harga = 40000.00; // Harga statis dari tombol konfirmasi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan data dari form
    $nama_lengkap = $conn->real_escape_string(trim($_POST['nama_lengkap']));
    $nomor_telepon = $conn->real_escape_string(trim($_POST['nomor_telepon']));
    $alamat_lengkap = $conn->real_escape_string(trim($_POST['alamat_lengkap']));
    $metode_pembayaran = $conn->real_escape_string(trim($_POST['metode_pembayaran']));
    
    // Validasi sederhana (pastikan semua kolom terisi)
    if (empty($nama_lengkap) || empty($nomor_telepon) || empty($alamat_lengkap) || empty($metode_pembayaran)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } else {
        
        // --- Langkah A: Cek dan Masukkan Data Pelanggan (customers) ---
        // Asumsi: Kita masukkan data pelanggan baru setiap kali, atau bisa juga dicek apakah nomor telepon sudah ada.
        
        $sql_customer = "INSERT INTO customers (nama_lengkap, nomor_telepon, alamat_lengkap) 
                         VALUES ('$nama_lengkap', '$nomor_telepon', '$alamat_lengkap')";
                         
        if ($conn->query($sql_customer) === TRUE) {
            
            // Ambil ID pelanggan yang baru saja dimasukkan
            $customer_id = $conn->insert_id; 
            
            // --- Langkah B: Masukkan Data Transaksi (orders) ---
            $tanggal_transaksi = date('Y-m-d H:i:s'); // Mengambil waktu server saat ini
            $status_transaksi = "Menunggu Pengiriman"; // Status awal
            
            $sql_order = "INSERT INTO orders (customer_id, tanggal_transaksi, metode_pembayaran, total_bayar, status_transaksi)
                          VALUES ('$customer_id', '$tanggal_transaksi', '$metode_pembayaran', '$total_harga', '$status_transaksi')";
                          
            if ($conn->query($sql_order) === TRUE) {
                echo "<h2>✅ Pesanan Berhasil Dikonfirmasi!</h2>";
                echo "<p>Nomor Transaksi Anda: " . $conn->insert_id . "</p>";
                echo "<p>Total Pembayaran: Rp " . number_format($total_harga, 0, ',', '.') . "</p>";
                // Anda bisa mengarahkan user ke halaman sukses: header("Location: sukses.php");
            } else {
                echo "Error memasukkan data order: " . $sql_order . "<br>" . $conn->error;
            }
            
        } else {
            echo "Error memasukkan data pelanggan: " . $sql_customer . "<br>" . $conn->error;
        }
    }
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengiriman</title>
    <style>
        /* Gaya sederhana agar mirip dengan gambar */
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #4CAF50; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pengiriman</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>

            <label for="nomor_telepon">Nomor Telepon:</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" required>

            <label for="alamat_lengkap">Alamat Lengkap (Termasuk Kecamatan/Kota):</label>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="4" required></textarea>

            <label for="metode_pembayaran">Metode Pembayaran:</label>
            <select id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="Cash On Delivery (Bayar di Tempat)" selected>Cash On Delivery (Bayar di Tempat)</option>
                <option value="Transfer Bank">Transfer Bank</option>
            </select>

            <button type="submit">Konfirmasi Pesanan & Bayar (Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>)</button>
        </form>
    </div>
</body>
</html>