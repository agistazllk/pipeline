<?php 
// Panggil header.php (Memastikan session, koneksi, dan layout dasar dimuat)
include 'header.php'; 

// =================================================================
// 1. Cek Ketersediaan Keranjang
// =================================================================
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    // Jika kosong, arahkan kembali ke keranjang dengan notifikasi
    $_SESSION['notif_keranjang'] = "Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.";
    header('Location: keranjang.php');
    exit;
}

// =================================================================
// 2. Ambil Data Produk dari Database
// =================================================================
// Ambil semua ID produk dari array keranjang
$ids_produk = array_keys($_SESSION['keranjang']);
// Buat string ID yang dipisahkan koma untuk query SQL (misal: 1,3,5)
$id_list = implode(',', $ids_produk);

// Query untuk mengambil detail semua produk yang ada di keranjang
$query_produk = "SELECT id_produk, nama_buah, harga, stok FROM produk WHERE id_produk IN ($id_list)";
$result_produk = $koneksi->query($query_produk);

$grand_total = 0;
$data_keranjang = []; // Array untuk menyimpan detail produk + kuantitas

if ($result_produk->num_rows > 0) {
    while($row = $result_produk->fetch_assoc()) {
        $id = $row['id_produk'];
        $kuantitas = $_SESSION['keranjang'][$id];
        
        // Cek jika kuantitas di keranjang melebihi stok yang ada
        if ($kuantitas > $row['stok']) {
            $kuantitas = $row['stok']; // Paksa kuantitas sama dengan stok
            $_SESSION['keranjang'][$id] = $kuantitas; // Update session
            // Beri notifikasi ke user (opsional)
            $_SESSION['notif_checkout'] = "Kuantitas " . $row['nama_buah'] . " disesuaikan karena stok hanya tersedia " . $row['stok'] . " kg.";
        }
        
        $subtotal = $row['harga'] * $kuantitas;
        $grand_total += $subtotal;

        $data_keranjang[$id] = [
            'nama_buah' => $row['nama_buah'],
            'harga' => $row['harga'],
            'kuantitas' => $kuantitas,
            'subtotal' => $subtotal,
        ];
    }
}
?>

<h2 class="section-title">Checkout Pesanan Anda</h2>

<?php 
// Notifikasi flash
if (isset($_SESSION['notif_checkout'])) {
    // Gunakan class 'alert error' atau 'alert warning' untuk notifikasi kesalahan/penyesuaian
    echo '<div class="alert success">' . $_SESSION['notif_checkout'] . '</div>';
    unset($_SESSION['notif_checkout']); 
}
?>

<div class="checkout-grid">
    
    <div class="ringkasan-pesanan">
        <h3>Ringkasan Belanja</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty (kg)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_keranjang as $item) { ?>
                        <tr>
                            <td><?= $item['nama_buah']; ?></td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td><?= $item['kuantitas']; ?></td>
                            <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>TOTAL HARGA</strong></td>
                        <td><strong>Rp <?= number_format($grand_total, 0, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="catatan">*Biaya pengiriman akan dihitung dan dikonfirmasi oleh Admin setelah pesanan masuk. Total di atas hanya mencakup harga produk.</p>
    </div>

    <div class="formulir-pengiriman">
        <h3>Detail Pengiriman</h3>
        
        <form action="proses_checkout.php" method="POST">
            
            <input type="hidden" name="total_bayar" value="<?= $grand_total; ?>">

            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama_pelanggan" required>
            </div>
            
            <div class="form-group">
                <label for="telepon">Nomor Telepon:</label>
                <input type="tel" id="telepon" name="telepon" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat Lengkap (Termasuk Kecamatan/Kota):</label>
                <textarea id="alamat" name="alamat_kirim" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="metode">Metode Pembayaran:</label>
                <select id="metode" name="metode_bayar" required>
                    <option value="COD">Cash On Delivery (Bayar di Tempat)</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>
            </div>
            
            <button type="submit" name="checkout" class="btn-checkout">
                Konfirmasi Pesanan & Bayar (Rp <?= number_format($grand_total, 0, ',', '.'); ?>)
            </button>
        </form>
    </div>

</div> <?php
// Panggil footer
include 'footer.php'; 
?>