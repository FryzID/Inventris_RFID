<?php
// File: INVENTRIS_RFID/includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}
$autoloader_path = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoloader_path)) {
    require_once $autoloader_path;
} else {
    error_log("KRITIS: File autoloader Composer (vendor/autoload.php) tidak ditemukan di: " . htmlspecialchars($autoloader_path));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Inventaris Lab'; ?></title>
    
    <!-- PASTIKAN INI ADA DAN PATHNYA BENAR -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/coreui/css/coreui.min.css" rel="stylesheet"> 
    <!-- CSS Kustom Anda -->
    <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/styles.css">
    
    <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/styles.css">
    <?php if (isset($is_print_page) && $is_print_page): ?>
        <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/print.css" media="print">
    <?php endif; ?>
    <style>
        /* Perbaikan kecil agar Select2 tampil baik dengan style Anda */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ccc; /* Sesuaikan dengan border input Anda */
            min-height: 38px; /* Sesuaikan dengan tinggi input Anda */
            box-sizing: border-box; /* Tambahkan ini */
            width: 100% !important; /* Pastikan mengambil lebar penuh jika diperlukan */
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 5px; 
            width: 100% !important; /* Pastikan field pencarian bisa melebar */
        }
        .select2-container { /* Pastikan container select2 juga mengambil lebar yang sesuai */
            width: 100% !important; 
            box-sizing: border-box;
        }
        /* Style untuk fieldset agar tidak terlalu sempit */
        fieldset .manual-select-group {
            /* width: 100%; tidak perlu di sini jika select2-container sudah 100% */
        }
    </style>
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