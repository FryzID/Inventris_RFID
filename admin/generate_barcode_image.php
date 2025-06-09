<?php
// File: INVENTRIS_RFID/admin/generate_barcode_image.php

// Path ke autoloader relatif dari LOKASI FILE INI (admin/)
// admin/generate_barcode_image.php
// vendor/ ada di INVENTRIS_RFID/vendor/
// Jadi, kita naik satu level dari 'admin/' ke 'INVENTRIS_RFID/', lalu masuk ke 'vendor/'
$autoloader_path_preview = dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists($autoloader_path_preview)) {
    require_once $autoloader_path_preview;
} else {
    echo '<p style="color:red;">Kesalahan Konfigurasi Server: Barcode library (autoloader) tidak ditemukan di ' . htmlspecialchars($autoloader_path_preview) . '.</p>';
    error_log("CRITICAL: Composer autoload.php not found at " . $autoloader_path_preview . " (called from generate_barcode_image.php)");
    exit;
}

// Anda bisa mengaktifkan ini jika peringatan deprecated sangat mengganggu output AJAX
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

if (isset($_POST['barcode_data'])) {
    $barcode_data = trim($_POST['barcode_data']);
    if (!empty($barcode_data)) {
        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
        try {
            $widthFactor = 2; // Pastikan integer
            $height = 50;     // Pastikan integer

            echo $generator->getBarcode($barcode_data, $generator::TYPE_CODE_128, $widthFactor, $height);
            echo "<p style='font-family: monospace; letter-spacing: 1.5px; font-size:11px; margin-top:5px; text-align:center; word-break: break-all;'>" . htmlspecialchars($barcode_data) . "</p>";
        } catch (Exception $e) {
            echo '<p style="color:red;">Error saat membuat barcode: ' . htmlspecialchars($e->getMessage()) . '</p>';
            error_log("Barcode Generation Exception in generate_barcode_image.php: " . $e->getMessage() . " for data: " . $barcode_data);
        }
    } else {
        echo '<p>Data barcode kosong. Silakan masukkan nilai.</p>';
    }
} else {
    echo '<p>Permintaan tidak valid. Tidak ada data barcode diterima.</p>';
}
?>