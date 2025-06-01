<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Inventaris Lab'; ?></title>
    <!-- Menggunakan $base_path untuk path CSS -->
    <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/styles.css">
    <?php if (isset($is_print_page) && $is_print_page): ?>
        <link rel="stylesheet" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>css/print.css" media="print">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="container-nav">
            <!-- Menggunakan $base_path untuk link navigasi jika diperlukan -->
            <a class="navbar-brand" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>index.php">Inventaris Lab</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>index.php">Peminjaman</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>admin/index.php">Admin Area</a></li>
            </ul>
        </div>
    </nav>
    <div class="main-container">