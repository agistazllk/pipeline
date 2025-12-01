<?php 
include 'header.php';
?>

<h2 class="section-title">Hubungi Kami</h2>

<div class="form-container" style="max-width: 600px; margin-top: 40px;">
    <p style="text-align: center; margin-bottom: 20px;">Kami siap melayani Anda. Jangan ragu menghubungi kami melalui salah satu cara di bawah ini:</p>

    <div style="margin-bottom: 30px;">
        <h3>Informasi Kontak</h3>
        <p><strong>Alamat Gudang:</strong> Jl. Petani Makmur No. 10, Kota Bandung, 40292</p>
        <p><strong>Nomor Telepon:</strong> 0812-3456-7890</p>
        <p><strong>Email:</strong> support@panenraya.com</p>
    </div>

    <h3>Kirim Pesan Langsung</h3>
    <form action="" method="POST"> 
        <div class="form-group">
            <label for="nama">Nama Anda:</label>
            <input type="text" id="nama" name="nama" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="pesan">Pesan:</label>
            <textarea id="pesan" name="pesan" rows="5" required></textarea>
        </div>
        <button type="submit">Kirim Pesan</button>
    </form>
</div>

<?php 
include 'footer.php';
?>