<?php 
// Panggil header.php (Memastikan session, koneksi, dan layout dasar dimuat)
include 'header.php'; 

// =================================================================
// 1. Ambil ID Pesanan dari URL
// =================================================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada ID pesanan, arahkan ke halaman utama
    header('Location: index.php');
    exit;
}

$id_pesanan = (int)$_GET['id'];

// =================================================================
// 2. Query Detail Pesanan Utama
// =================================================================
$query_pesanan = "SELECT * FROM pesanan WHERE id_pesanan = ?";
$stmt_pesanan = $koneksi->prepare($query_pesanan);
$stmt_pesanan->bind_param("i", $id_pesanan);
$stmt_pesanan->execute();
$result_pesanan = $stmt_pesanan->get_result();

if ($result_pesanan->num_rows == 0) {
    // Pesanan tidak ditemukan
    echo '<div class="main-content container">';
    echo '<h2 class="section-title" style="color: red;">Pesanan Tidak Ditemukan!</h2>';
    echo '<p style="text-align: center;">Maaf, ID Pesanan #' . $id_pesanan . ' tidak valid atau tidak ada dalam sistem kami.</p>';
    echo '</div>';
    include 'footer.php';
    exit;
}

$data_pesanan = $result_pesanan->fetch_assoc();
$stmt_pesanan->close();

// =================================================================
// 3. Query Detail Item Pesanan
// =================================================================
$query_detail = "SELECT dp.*, p.nama_buah 
                 FROM detail_pesanan dp
                 JOIN produk p ON dp.id_produk = p.id_produk
                 WHERE dp.id_pesanan = ?";
$stmt_detail = $koneksi->prepare($query_detail);
$stmt_detail->bind_param("i", $id_pesanan);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$data_detail = [];

while($row = $result_detail->fetch_assoc()) {
    $data_detail[] = $row;
}
$stmt_detail->close();
?>

<div class="konfirmasi-wrapper">
    
    <h2 class="section-title" style="color: #4CAF50;">✅ Pesanan Berhasil Dibuat!</h2>

    <?php 
    // Tampilkan Pesan Sukses dari proses_checkout.php
    if (isset($_SESSION['pesan_sukses'])) {
        echo '<div class="alert success konfirmasi-alert">' . $_SESSION['pesan_sukses'] . '</div>';
        unset($_SESSION['pesan_sukses']); 
    }
    ?>

    <div class="detail-container">
        <div class="detail-box">
            <h3>📝 Detail Pesanan Anda</h3>
            <table>
                <tr>
                    <th>ID Pesanan</th>
                    <td>#<?= $data_pesanan['id_pesanan']; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Pesan</th>
                    <td><?= date('d F Y H:i:s', strtotime($data_pesanan['tanggal_pesan'])); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="status-badge"><?= $data_pesanan['status_pesan']; ?></span></td>
                </tr>
                <tr>
                    <th>Metode Bayar</th>
                    <td><?= $data_pesanan['metode_bayar']; ?></td>
                </tr>
                <tr>
                    <th>Total Produk</th>
                    <td>Rp <?= number_format($data_pesanan['total_harga_produk'], 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <div class="detail-box">
            <h3>🚚 Detail Pengiriman</h3>
            <table>
                <tr>
                    <th>Nama Penerima</th>
                    <td><?= $data_pesanan['nama_pelanggan']; ?></td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td><?= $data_pesanan['telepon_pelanggan']; ?></td>
                </tr>
                <tr>
                    <th>Alamat Kirim</th>
                    <td><?= nl2br($data_pesanan['alamat_kirim']); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h3 style="margin-top: 40px; border-bottom: 2px solid #ccc; padding-bottom: 10px;">Daftar Item Pesanan</h3>
    <div class="table-responsive">
        <table class="item-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Produk</th>
                    <th>Harga Satuan</th>
                    <th>Kuantitas (kg)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($data_detail as $item) { ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= $item['nama_buah']; ?></td>
                        <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                        <td><?= $item['kuantitas']; ?></td>
                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>TOTAL AKHIR (Harga Produk)</strong></td>
                    <td><strong>Rp <?= number_format($data_pesanan['total_harga_produk'], 0, ',', '.'); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <p class="catatan-akhir">
        **PENTING:** Pesanan Anda sedang menunggu konfirmasi admin. Kami akan segera menghubungi Anda melalui nomor **<?= $data_pesanan['telepon_pelanggan']; ?>** untuk mengkonfirmasi alamat dan total biaya pengiriman.
    </p>

</div>

<?php
// Panggil footer
include 'footer.php'; 
?>