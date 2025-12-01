<?php
// Sertakan file koneksi database
include 'config_db.php';

// Harga tetap dari formulir
$total_harga = 40000.00; 

// Variabel untuk menampung pesan status (akan tampil di halaman)
$status_pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan data POST
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nomor_telepon = trim($_POST['nomor_telepon']);
    $alamat_lengkap = trim($_POST['alamat_lengkap']);
    $metode_pembayaran = trim($_POST['metode_pembayaran']);
    
    // Karena form tidak menyediakan input terpisah untuk kecamatan/kota, kita set NULL.
    // Ini hanya berfungsi jika kolom 'kecamatan_kota' di DB TIDAK diset NOT NULL.
    // Jika NOT NULL, hapus kolom ini dari query atau berikan nilai string kosong.
    $kecamatan_kota = NULL; 

    // Validasi sederhana
    if (empty($nama_lengkap) || empty($nomor_telepon) || empty($alamat_lengkap) || empty($metode_pembayaran)) {
        $status_pesan = "<div style='color: red; padding: 10px; border: 1px solid red;'>⚠️ Gagal: Semua kolom harus diisi!</div>";
    } else {
        
        // --- TRANSAKSI DATABASE DIMULAI ---
        $conn->begin_transaction();
        $success = true;

        // A. Insert ke Tabel Pelanggan (customers)
        // Jika kolom 'kecamatan_kota' adalah NOT NULL, ubah '? ? ?' menjadi '? ? ? ?'
        // dan tambahkan $kecamatan_kota di bind_param.
        $sql_customer = "INSERT INTO customers (nama_lengkap, nomor_telepon, alamat_lengkap, kecamatan_kota) VALUES (?, ?, ?, ?)";
        
        if ($stmt_cust = $conn->prepare($sql_customer)) {
            // Bind parameter: "ssss" berarti empat string
            // Jika $kecamatan_kota NULL, gunakan 'sss' dan hapus $kecamatan_kota
            $stmt_cust->bind_param("ssss", $nama_lengkap, $nomor_telepon, $alamat_lengkap, $kecamatan_kota);
            
            if (!$stmt_cust->execute()) {
                $status_pesan = "<div style='color: red;'>❌ Gagal Eksekusi Pelanggan: " . $stmt_cust->error . "</div>";
                $success = false;
            } else {
                $customer_id = $conn->insert_id; 
                // echo "DEBUG: Customer ID berhasil dibuat: " . $customer_id . "<br>"; // UNTUK DEBUGGING
            }
            $stmt_cust->close();
        } else {
            $status_pesan = "<div style='color: red;'>❌ Error Prepare Statement (Pelanggan): " . $conn->error . "</div>";
            $success = false;
        }

        // B. Insert ke Tabel Transaksi (orders) jika langkah A berhasil
        if ($success) {
            $tanggal_transaksi = date('Y-m-d H:i:s');
            $status_awal = "Menunggu Pengiriman"; 
            
            // Format total_harga tanpa pemisah ribuan, 2 desimal, titik sebagai pemisah desimal
            $total_bayar_db = number_format($total_harga, 2, '.', '');
            
            $sql_order = "INSERT INTO orders (customer_id, tanggal_transaksi, metode_pembayaran, total_bayar, status_transaksi) 
                          VALUES (?, ?, ?, ?, ?)";
            
            if ($stmt_order = $conn->prepare($sql_order)) {
                // Bind parameter: "isdds" (integer, string, string, double/decimal, string)
                $stmt_order->bind_param("isdds", $customer_id, $tanggal_transaksi, $metode_pembayaran, $total_bayar_db, $status_awal);
                
                if (!$stmt_order->execute()) {
                    $status_pesan = "<div style='color: red;'>❌ Gagal Eksekusi Transaksi: " . $stmt_order->error . "</div>";
                    $success = false;
                }
                $stmt_order->close();
            } else {
                $status_pesan = "<div style='color: red;'>❌ Error Prepare Statement (Transaksi): " . $conn->error . "</div>";
                $success = false;
            }
        }

        // C. Commit atau Rollback Transaksi
        if ($success) {
            $conn->commit();
            $status_pesan = "<div style='color: green; padding: 10px; border: 1px solid green;'>
                                ✅ **Pesanan Berhasil Dikonfirmasi!** <br> 
                                Total Bayar: Rp " . number_format($total_harga, 0, ',', '.') . 
                             "</div>";
        } else {
            $conn->rollback();
            // Pesan error sudah disiapkan di atas
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
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;}
        .container { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.2); width: 450px; }
        h2 { color: #2e7d32; border-bottom: 1px solid #c8e6c9; padding-bottom: 10px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-konfirmasi { background-color: #4CAF50; color: white; padding: 15px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 17px; font-weight: bold; transition: background-color 0.3s; }
        .btn-konfirmasi:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pengiriman</h2>
        <?php echo $status_pesan; ?> 

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

            <button type="submit" class="btn-konfirmasi">Konfirmasi Pesanan & Bayar (Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>)</button>
        </form>
    </div>
</body>
</html>