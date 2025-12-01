<?php
// Pastikan sesi dimulai dan koneksi database tersedia
// Biasanya include file koneksi di sini
session_start();
// Ganti ini dengan file koneksi database Anda
include 'koneksi.php'; 

// =================================================================
// 1. Validasi Metode Request dan Cek Data Keranjang
// =================================================================
if (!isset($_POST['checkout'])) {
    // Jika diakses tanpa submit form, arahkan kembali
    header('Location: checkout.php');
    exit;
}

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    $_SESSION['notif_keranjang'] = "Keranjang Anda kosong saat proses checkout.";
    header('Location: keranjang.php');
    exit;
}

// =================================================================
// 2. Ambil dan Validasi Data POST
// =================================================================
$nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$alamat_kirim = trim($_POST['alamat_kirim'] ?? '');
$metode_bayar = trim($_POST['metode_bayar'] ?? '');
$grand_total = (float)($_POST['total_bayar'] ?? 0); // Ambil total bayar tersembunyi

// Validasi Data Input Wajib
if (empty($nama_pelanggan) || empty($telepon) || empty($alamat_kirim) || empty($metode_bayar) || $grand_total <= 0) {
    $_SESSION['notif_checkout'] = "Data pengiriman tidak lengkap atau total belanja tidak valid.";
    header('Location: checkout.php');
    exit;
}

// =================================================================
// 3. Proses Transaksi Database (Menerapkan ACID Properties)
// =================================================================
$koneksi->begin_transaction();
$transaksi_sukses = true;
$id_transaksi_baru = null;
$error_message = "";

try {
    
    // --- Langkah A: Insert ke Tabel Transaksi Utama (orders/transaksi) ---
    $tanggal_transaksi = date('Y-m-d H:i:s');
    // Status awal: 'Menunggu Konfirmasi Admin' karena biaya kirim belum dihitung
    $status_transaksi = 'Menunggu Konfirmasi Admin'; 

    // Query INSERT menggunakan Prepared Statements untuk keamanan
    $stmt_transaksi = $koneksi->prepare("
        INSERT INTO transaksi (
            tanggal_transaksi, 
            nama_pelanggan, 
            telepon, 
            alamat_kirim, 
            metode_bayar, 
            total_produk, 
            status_transaksi
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // "ssdssds" = string, string, double/decimal, string, string, double/decimal, string
    // Kita asumsikan total_produk adalah double/decimal, selebihnya string.
    $stmt_transaksi->bind_param(
        "ssssdds", 
        $tanggal_transaksi, 
        $nama_pelanggan, 
        $telepon, 
        $alamat_kirim, 
        $metode_bayar, 
        $grand_total, // Total harga produk
        $status_transaksi
    );

    if (!$stmt_transaksi->execute()) {
        throw new Exception("Gagal menyimpan transaksi utama: " . $stmt_transaksi->error);
    }
    
    $id_transaksi_baru = $koneksi->insert_id;
    $stmt_transaksi->close();

    // --- Langkah B: Insert ke Detail Transaksi (detail_transaksi) ---
    $stmt_detail = $koneksi->prepare("
        INSERT INTO detail_transaksi (
            id_transaksi, 
            id_produk, 
            nama_produk, 
            harga_satuan, 
            kuantitas, 
            subtotal
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    // Ambil data keranjang dari SESSION (Data ini sudah diolah di checkout.php)
    $data_keranjang_session = $_SESSION['keranjang']; 
    
    // Kita perlu mengambil detail produk lagi untuk harga dan nama yang akurat di database
    // Ambil semua ID produk dari array keranjang
    $ids_produk = array_keys($data_keranjang_session);
    $id_list = implode(',', $ids_produk);
    
    $query_produk = "SELECT id_produk, nama_buah, harga, stok FROM produk WHERE id_produk IN ($id_list)";
    $result_produk = $koneksi->query($query_produk);
    
    while($row = $result_produk->fetch_assoc()) {
        $id_produk = $row['id_produk'];
        $kuantitas = $data_keranjang_session[$id_produk]; // Kuantitas dari sesi
        
        // Final check stok (meskipun sudah divalidasi di checkout.php)
        $kuantitas = min($kuantitas, $row['stok']); 

        $nama_produk = $row['nama_buah'];
        $harga_satuan = $row['harga'];
        $subtotal_item = $harga_satuan * $kuantitas;

        // Bind parameter untuk detail transaksi: "iisddi"
        // Asumsi: id_transaksi, id_produk = int; nama_produk=string; harga_satuan, kuantitas, subtotal = decimal/double
        // Ubah format binding sesuai tipe data di DB Anda. Contoh: "iisddi"
        $stmt_detail->bind_param(
            "iisiid", // Sesuaikan ini dengan tipe data kolom Anda (int, int, string, int, int, double)
            $id_transaksi_baru, 
            $id_produk, 
            $nama_produk, 
            $harga_satuan, 
            $kuantitas, 
            $subtotal_item
        );

        if (!$stmt_detail->execute()) {
            throw new Exception("Gagal menyimpan detail transaksi untuk produk ID " . $id_produk . ": " . $stmt_detail->error);
        }
        
        // --- Langkah C: Update Stok Produk ---
        // Kurangi stok di tabel produk
        $stmt_stok = $koneksi->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?");
        $stmt_stok->bind_param("iii", $kuantitas, $id_produk, $kuantitas);
        
        if (!$stmt_stok->execute()) {
             throw new Exception("Gagal mengurangi stok produk ID " . $id_produk . ": " . $stmt_stok->error);
        }
        $stmt_stok->close();
    }
    
    $stmt_detail->close();

    // Jika semua query berhasil, lakukan COMMIT
    $koneksi->commit();
    $transaksi_sukses = true;
    
} catch (Exception $e) {
    // Jika ada error, lakukan ROLLBACK
    $koneksi->rollback();
    $transaksi_sukses = false;
    $error_message = $e->getMessage();
}

// =================================================================
// 4. Validasi Pengiriman dan Pengarahan
// =================================================================

if ($transaksi_sukses) {
    // Kosongkan keranjang setelah berhasil
    unset($_SESSION['keranjang']);
    
    // Set notifikasi sukses
    $_SESSION['notif_sukses'] = "Pesanan Anda dengan ID **#T" . $id_transaksi_baru . "** telah berhasil dikirim. Admin akan segera menghubungi Anda untuk konfirmasi biaya kirim.";
    
    // Arahkan ke halaman terima kasih atau detail pesanan
    header("Location: terima_kasih.php?id=" . $id_transaksi_baru);
    exit;

} else {
    // Set notifikasi error dan kembalikan ke halaman checkout
    $_SESSION['notif_checkout'] = "⚠️ **PROSES GAGAL!** Terjadi kesalahan database. " . $error_message;
    header('Location: checkout.php');
    exit;
}

?>