<?php
// File: INVENTRIS_RFID/includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

// Path ke autoload.php relatif dari lokasi header.php
// header.php ada di INVENTRIS_RFID/includes/header.php
// vendor/ ada di INVENTRIS_RFID/vendor/
// Jadi, kita naik satu level dari 'includes/' ke 'INVENTRIS_RFID/', lalu masuk ke 'vendor/'
$autoloader_path = dirname(__DIR__) . '/vendor/autoload.php'; // dirname(__DIR__) dari 'includes/' adalah 'INVENTRIS_RFID/'

if (file_exists($autoloader_path)) {
    require_once $autoloader_path;
} else {
    // Ini krusial. Jika autoloader tidak ada, library barcode tidak akan bekerja.
    $errorMessage = "KESALAHAN KONFIGURASI: File autoloader Composer tidak ditemukan di: " . htmlspecialchars($autoloader_path) . ". Fungsi barcode tidak akan bekerja.";
    error_log($errorMessage); // Catat di log server
    // Anda bisa memilih untuk menghentikan skrip atau menampilkan pesan di halaman
    // die($errorMessage); // Menghentikan eksekusi
    // Untuk pengembangan, mungkin lebih baik membiarkannya agar halaman tetap termuat tapi ada error log.
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Inventaris Lab'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/styles.css">
    <?php if (isset($is_print_page) && $is_print_page): ?>
        <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/print.css" media="print">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="container-nav">
            <a class="navbar-brand" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>index.php">Inventaris Lab</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>index.php">Peminjaman</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>admin/index.php">Admin Area</a></li>
            </ul>
        </div>
    </nav>
    <div class="main-container">