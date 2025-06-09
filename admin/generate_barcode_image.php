<?php
// File: INVENTRIS_RFID/admin/generate_barcode_image.php

// Path ke autoloader relatif dari LOKASI FILE INI (admin/)
// admin/generate_barcode_image.php
// vendor/ ada di INVENTRIS_RFID/vendor/
$autoloader_path_preview = dirname(__DIR__) . '/vendor/autoload.php'; // Naik satu level ke INVENTRIS_RFID, lalu ke vendor

if (file_exists($autoloader_path_preview)) {
    require_once $autoloader_path_preview;
} else {
    // Jika autoloader tidak ditemukan, kirim pesan error yang jelas
    // Ini akan ditampilkan di #barcode_preview_container melalui AJAX
    echo '<p style="color:red; font-weight:bold;">Kesalahan Konfigurasi Server!</p><p style="color:red;">File pustaka barcode (autoloader) tidak ditemukan di server pada path: ' . htmlspecialchars($autoloader_path_preview) . '. Harap periksa instalasi Composer.</p>';
    error_log("KRITIS: Composer autoload.php tidak ditemukan di " . htmlspecialchars($autoloader_path_preview) . " (dipanggil dari generate_barcode_image.php)");
    exit; // Hentikan eksekusi jika library tidak ada
}

// Anda bisa mengaktifkan ini untuk menyembunyikan notice deprecated HANYA untuk output AJAX ini
// Namun, lebih baik mengaturnya secara global jika memungkinkan atau library diperbarui
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

if (isset($_POST['barcode_data'])) {
    $barcode_data = trim($_POST['barcode_data']);
    if (!empty($barcode_data)) {
        // Membuat instance dari generator HTML
        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
        try {
            // Parameter: data, tipe, width factor (integer), height (integer), foreground color (string)
            $widthFactor = 2;     // Integer
            $height = 50;         // Integer
            $foregroundColor = 'black';

            // Menghasilkan HTML untuk barcode
            // TYPE_CODE_128 adalah pilihan yang baik untuk data alfanumerik umum
            echo $generator->getBarcode($barcode_data, $generator::TYPE_CODE_128, $widthFactor, $height, $foregroundColor);
            
            // Menambahkan teks nilai barcode di bawah gambar barcode
            echo "<p style='font-family: monospace; letter-spacing: 1.5px; font-size:11px; margin-top:5px; text-align:center; word-break: break-all;'>" . htmlspecialchars($barcode_data) . "</p>";

        } catch (Picqer\Barcode\Exceptions\BarcodeException $e) { // Menangkap exception spesifik dari library jika ada
            echo '<p style="color:red;">Error saat membuat barcode (Library): ' . htmlspecialchars($e->getMessage()) . '</p>';
            error_log("Picqer Barcode Library Exception in generate_barcode_image.php: " . $e->getMessage() . " for data: " . $barcode_data);
        } catch (Exception $e) { // Menangkap exception umum lainnya
            echo '<p style="color:red;">Terjadi kesalahan umum saat membuat barcode: ' . htmlspecialchars($e->getMessage()) . '</p>';
            error_log("General Exception in generate_barcode_image.php: " . $e->getMessage() . " for data: " . $barcode_data);
        }
    } else {
        echo '<p>Data barcode kosong. Silakan masukkan nilai pada field barcode.</p>';
    }
} else {
    // Ini akan muncul jika file diakses langsung via GET atau tanpa parameter 'barcode_data' via POST
    echo '<p>Permintaan tidak valid. Data barcode tidak diterima dengan benar.</p>';
}
?>