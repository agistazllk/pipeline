<?php
include 'header.php'; // Memanggil session, koneksi, dan header HTML

$grand_total = 0; // Inisialisasi total belanja
?>

<div class="container" style="min-height: 50vh;">
    <h2 style="color: #4CAF50;">🛒 Keranjang Belanja Anda</h2>

    <?php 
    // Tampilkan notifikasi jika ada (dari tambah_keranjang.php)
    if (isset($_SESSION['notif_keranjang'])) {
        echo '<div class="alert success">' . $_SESSION['notif_keranjang'] . '</div>';
        unset($_SESSION['notif_keranjang']); 
    }
    
    // Cek apakah keranjang kosong
    if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])): 
    ?>
        <div class="empty-cart" style="text-align: center; padding: 50px; border: 1px dashed #ddd;">
            <p style="font-size: 1.1rem;">Keranjang belanja Anda masih kosong.</p>
            <p><a href="index.php" style="color: #FF9800; text-decoration: none; font-weight: bold;">Mulai belanja sekarang!</a></p>
        </div>
    <?php else: ?>
    
        <table class="cart-table" border="1" cellpadding="10" cellspacing="0" width="100%">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Produk</th>
                    <th>Harga/kg</th>
                    <th>Jumlah (kg)</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            // Ambil ID produk yang ada di keranjang
            $list_id_produk = array_keys($_SESSION['keranjang']);
            $id_string = implode(',', $list_id_produk); // Konversi array ID menjadi string: "1,2,3"
            
            // Query untuk mendapatkan detail produk sekaligus
            $query_detail = "SELECT id_produk, nama_buah, harga FROM produk WHERE id_produk IN ($id_string)";
            $result_detail = $koneksi->query($query_detail);

            if ($result_detail->num_rows > 0) {
                while($detail = $result_detail->fetch_assoc()) {
                    $id = $detail['id_produk'];
                    $jumlah = $_SESSION['keranjang'][$id]; // Ambil kuantitas dari session
                    $harga = $detail['harga'];
                    $subtotal = $harga * $jumlah;
                    $grand_total += $subtotal;
                    ?>
                    <tr>
                        <td align="center"><?= $no++; ?></td>
                        <td><?= $detail['nama_buah']; ?></td>
                        <td align="right">Rp <?= number_format($harga, 0, ',', '.'); ?></td>
                        <td align="center">
                            <a href="ubah_keranjang.php?id=<?= $id; ?>&action=kurang" class="btn-qty" style="margin-right: 5px;">-</a>
                            <?= $jumlah; ?>
                            <a href="ubah_keranjang.php?id=<?= $id; ?>&action=tambah" class="btn-qty" style="margin-left: 5px;">+</a>
                        </td>
                        <td align="right">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td align="center">
                            <a href="hapus_keranjang.php?id=<?= $id; ?>" onclick="return confirm('Yakin ingin menghapus item ini?')" class="btn-hapus" style="color: red; text-decoration: none;">Hapus</a>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
                <tr>
                    <td colspan="4" align="right"><strong>TOTAL BELANJA</strong></td>
                    <td align="right"><strong>Rp <?= number_format($grand_total, 0, ',', '.'); ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 20px; text-align: right;">
            <a href="index.php" class="btn-lanjut" style="background-color: #555; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Lanjut Belanja</a>
            <a href="checkout.php" class="btn-checkout" style="background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-left: 10px;">Checkout</a>
        </div>

    <?php endif; ?>

</div>

<style>
/* Tambahkan styling ini ke style.css jika Anda mau */
.cart-table {
    border-collapse: collapse;
}
.cart-table th, .cart-table td {
    border: 1px solid #ddd;
}
.btn-qty {
    background-color: #ddd;
    padding: 3px 8px;
    text-decoration: none;
    border-radius: 3px;
    color: #333;
}
.alert.success {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
</style>

<?php
include 'footer.php'; 
?>