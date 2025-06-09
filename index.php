<?php
$page_title = "Form Peminjaman";
$base_path = "./"; // Path relatif dari root ke folder css, js, dll.
include 'includes/header.php'; // Ini akan include vendor/autoload.php jika dikonfigurasi di sana
?>

<h1>Form Peminjaman Inventaris Laboratorium</h1>

<?php
// Tampilkan pesan dari proses sebelumnya (jika ada)
if (isset($_SESSION['borrow_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['borrow_message_type']) . '">' . htmlspecialchars($_SESSION['borrow_message']) . '</div>';
    unset($_SESSION['borrow_message']);
    unset($_SESSION['borrow_message_type']);
}
?>

<form id="borrowForm" action="php/process_borrow.php" method="POST">
    <label for="student_name">Nama Siswa:</label>
    <input type="text" id="student_name" name="student_name" required placeholder="Ketik nama siswa">

    <h2>Tambah Barang (Scan dengan Barcode Scanner)</h2>
    <div class="barcode-input-group no-print">
        <label for="barcode_input_val">Scan Barcode Barang:</label>
        <input type="text" id="barcode_input_val" placeholder="Arahkan scanner ke barcode barang">
        <button type="button" id="addByBarcodeBtn" class="button secondary">Tambahkan Barang</button>
    </div>
    <p class="no-print">
        <em>
            Setelah memindai barcode, nilai akan muncul di atas. Klik "Tambahkan Barang" atau sistem akan otomatis memproses setelah scanner mengirim 'Enter'.
        </em>
    </p>
    <div id="scan_status"></div> <!-- Ganti nama ID dari rfid_status -->

    <h2>Daftar Barang Akan Dipinjam:</h2>
    <ul id="borrow_list">
        <!-- Item yang di-scan akan ditambahkan di sini oleh JavaScript -->
    </ul>
    <p id="no_items_message" style="color: #777;">Belum ada barang yang ditambahkan.</p>


    <label for="notes">Catatan (Opsional):</label>
    <textarea id="notes" name="notes" placeholder="Catatan tambahan jika ada"></textarea>

    <input type="submit" value="Proses Peminjaman">
</form>

<style>
/* Style tambahan untuk grup input barcode jika belum ada di styles.css */
.barcode-input-group {
    display: flex;
    align-items: center; /* Vertically align items */
    margin-bottom: 15px;
}
.barcode-input-group label {
    margin-right: 10px; /* Space between label and input */
    margin-bottom: 0; /* Override default label margin-bottom */
    white-space: nowrap; /* Prevent label text from wrapping */
}
.barcode-input-group input[type="text"] {
    flex-grow: 1; /* Input takes remaining space */
    margin-right: 10px; /* Space between input and button */
    margin-bottom: 0; /* Override default input margin-bottom */
}
.barcode-input-group button {
    margin-bottom: 0; /* Override default button margin-bottom */
    flex-shrink: 0; /* Prevent button from shrinking */
}
/* Untuk status message */
#scan_status { margin-top: 10px; padding: 10px; border-radius: 4px; border: 1px solid transparent; }
.status-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.status-error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.status-info { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
</style>

<?php include 'includes/footer.php'; // Ini akan include js/script.js ?>