<?php
$page_title = "Tambah Barang Baru";
$base_path = "../"; // Path dari admin/ ke root/
// Autoload sudah di-include via header.php
include '../includes/header.php';
// Tidak perlu require_once '../php/db_connect.php'; di sini karena tidak ada query DB langsung
?>

<h1>Tambah Barang Baru</h1>

<?php
// Tampilkan pesan dari proses_add_item.php jika ada
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}
?>

<form action="process_add_item.php" method="POST">
    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo isset($_SESSION['form_data']['item_name']) ? htmlspecialchars($_SESSION['form_data']['item_name']) : ''; ?>" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"><?php echo isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : ''; ?></textarea>

    <label for="barcode_value">Nilai Barcode:</label>
    <input type="text" id="barcode_value" name="barcode_value" value="<?php echo isset($_SESSION['form_data']['barcode_value']) ? htmlspecialchars($_SESSION['form_data']['barcode_value']) : 'ITEM' . date('YmdHis'); ?>" required placeholder="Ketik nilai unik untuk barcode (misal: LAB001)">
    <button type="button" id="generateBarcodePreviewBtn" class="button secondary" style="margin-top: 5px; margin-bottom:10px;">Lihat Preview Barcode</button>
    <small style="display:block; margin-bottom:10px;">Pastikan Nilai Barcode ini unik. Ini akan digunakan untuk scan.</small>

    <div id="barcode_preview_container" style="margin-top:10px; margin-bottom:20px; padding:10px; border:1px solid #ccc; min-height: 70px; text-align:center;">
        Preview Barcode akan muncul di sini setelah klik tombol atau saat halaman dimuat jika ada nilai.
    </div>

    <input type="submit" value="Tambah Barang">
</form>

<p style="margin-top: 20px;"><a href="index.php">Kembali ke Admin Area</a></p>

<?php
// Hapus data form dari session setelah ditampilkan
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
include '../includes/footer.php'; // Ini akan include js/script.js
?>