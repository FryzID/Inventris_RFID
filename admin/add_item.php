<?php
$page_title = "Tambah Barang Baru";
$base_path = "../"; // Path dari admin/ ke root/
include '../includes/header.php';
?>

<h1>Tambah Barang Baru</h1>

<?php
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
    // unset($_SESSION['admin_message_type']); // Sebaiknya keduanya di-unset
}
?>

<form action="process_add_item.php" method="POST">
    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo isset($_SESSION['form_data']['item_name']) ? htmlspecialchars($_SESSION['form_data']['item_name']) : ''; ?>" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"><?php echo isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : ''; ?></textarea>

    <label for="barcode_value">Nilai Barcode:</label>
    <input type="text" id="barcode_value" name="barcode_value" value="<?php echo isset($_SESSION['form_data']['barcode_value']) ? htmlspecialchars($_SESSION['form_data']['barcode_value']) : 'ITEM' . date('YmdHis'); ?>" required placeholder="Ketik nilai unik untuk barcode">
    <button type="button" id="generateBarcodePreviewBtn" class="button secondary" style="margin-top: 5px; margin-bottom:10px;">Lihat Preview Barcode</button>
    <small style="display:block; margin-bottom:10px;">Pastikan Nilai Barcode ini unik.</small>

    <div id="barcode_preview_container" style="margin-top:10px; margin-bottom:20px; padding:10px; border:1px solid #ccc; min-height: 70px; text-align:center; background-color: #f9f9f9;">
        Masukkan nilai pada field "Nilai Barcode" lalu klik tombol "Lihat Preview Barcode".
    </div>

    <input type="submit" value="Tambah Barang">
</form>

<p style="margin-top: 20px;"><a href="index.php">Kembali ke Admin Area</a></p>

<?php
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
include '../includes/footer.php';
?>