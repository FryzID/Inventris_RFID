<?php
// File: INVENTRIS_RFID/index.php
$page_title = "Form Peminjaman";
$base_path = "./";
include 'includes/header.php'; // Memuat Select2 CSS, CoreUI CSS (jika masih ada), dan Autoloader
require_once 'php/db_connect.php';

$available_items_for_select = [];
$sql_all_items = "SELECT item_id, item_name, barcode_value FROM items WHERE status = 'available' ORDER BY item_name ASC";
$result_all_items = mysqli_query($conn, $sql_all_items);
if ($result_all_items) {
    while ($row = mysqli_fetch_assoc($result_all_items)) {
        $available_items_for_select[] = $row;
    }
    mysqli_free_result($result_all_items);
}
?>

<h1>Form Peminjaman Inventaris Laboratorium</h1>

<?php
if (isset($_SESSION['borrow_message'])) { /* ... (tampilkan pesan) ... */ }
?>

<form id="borrowForm" action="php/process_borrow.php" method="POST">
    <label for="student_name">Nama Siswa:</label>
    <input type="text" id="student_name" name="student_name" required placeholder="Ketik nama siswa">

    <h2>Tambah Barang</h2>
    
    <!-- Bagian untuk Scan Barcode -->
    <fieldset style="margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
        <legend style="padding: 0 10px; font-weight:bold;">Via Scan Barcode</legend>
        <label for="barcode_input_val">Scan Barcode Barang:</label>
        <div class="barcode-input-group no-print">
            <input type="text" id="barcode_input_val" placeholder="Arahkan scanner ke barcode barang">
            <button type="button" id="addByBarcodeBtn" class="button secondary">Tambahkan via Scan</button>
        </div>
        <p class="no-print"><small><em>Setelah memindai, nilai akan muncul. Klik tombol atau tekan Enter.</em></small></p>
    </fieldset>

    <!-- Bagian untuk Pilih Manual dengan Select2 -->
    <fieldset style="border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
        <legend style="padding: 0 10px; font-weight:bold;">Pilih Manual dari Daftar</legend>
        <label for="manual_item_select2">Cari dan Pilih Barang (satu per satu):</label>
        <div class="manual-select-group no-print" style="display:flex; align-items:center; gap:10px;">
            <select id="manual_item_select2" name="manual_item_selector" style="width: 70%; flex-grow:1;">
                <option value="">-- Ketik untuk mencari barang --</option>
                <?php foreach ($available_items_for_select as $item): ?>
                    <option value="<?php echo htmlspecialchars($item['item_id']); ?>" 
                            data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                            data-barcode="<?php echo htmlspecialchars($item['barcode_value']); ?>">
                        <?php echo htmlspecialchars($item['item_name']); ?> (Barcode: <?php echo htmlspecialchars($item['barcode_value']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="addByManualSelectBtn" class="button secondary" style="flex-shrink:0;">Tambahkan Pilihan</button>
        </div>
         <p class="no-print"><small><em>Pilih satu barang dari daftar, lalu klik "Tambahkan Pilihan". Ulangi untuk barang lain.</em></small></p>
    </fieldset>
    
    <div id="scan_status" style="margin-top:15px; min-height:1.5em; padding: 10px; border-radius: 4px; border: 1px solid transparent;"></div>


    <h2>Daftar Barang Akan Dipinjam:</h2>
    <ul id="borrow_list">
        <!-- Item akan ditambahkan di sini -->
    </ul>
    <p id="no_items_message" style="color: #777; margin-top:10px;">Belum ada barang yang ditambahkan.</p>

    <label for="notes" style="margin-top:20px;">Catatan (Opsional):</label>
    <textarea id="notes" name="notes" placeholder="Catatan tambahan jika ada"></textarea>

    <input type="submit" value="Proses Peminjaman" style="margin-top:20px;">
</form>

<style> /* ... (style fieldset, legend, barcode-input-group, scan_status, borrow_list seperti sebelumnya) ... */ 
/* Pastikan Select2 CSS dimuat dari header untuk styling yang benar */
.select2-container { /* Umumnya Select2 akan mengatur lebarnya sendiri jika select asli punya style width */
    width: 100% !important; /* Atau atur di JS saat inisialisasi */
}
</style>

<?php
mysqli_close($conn);
include 'includes/footer.php'; // Memuat jQuery, Select2 JS, dan script.js
?>