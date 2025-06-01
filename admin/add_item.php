<?php
// Contoh untuk INVENTRIS_RFID/admin/index.php
$page_title = "Admin Area";
$base_path = "../"; // Naik satu level untuk mencapai root
include '../includes/header.php'; // Path relatif dari folder admin/
?>

<h1>Tambah Barang Baru</h1>

<form action="process_add_item.php" method="POST">
    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"></textarea>

    <label for="rfid_tag">Tag RFID:</label>
    <input type="text" id="rfid_tag" name="rfid_tag" required placeholder="Scan atau ketik ID Tag RFID unik">
    <small>Pastikan Tag RFID ini unik untuk setiap barang.</small>

    <input type="submit" value="Tambah Barang">
</form>

<p style="margin-top: 20px;"><a href="index.php">Kembali ke Admin Area</a></p>

<?php
include '../includes/footer.php'; // Path relatif dari folder admin/
?>