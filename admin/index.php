<?php
$page_title = "Admin Area";
$base_path = "../"; // Untuk path CSS dari folder admin
include '../includes/header.php';

// Sederhana, tanpa login untuk saat ini
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . $_SESSION['admin_message_type'] . '">' . $_SESSION['admin_message'] . '</div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}
?>

<h1>Admin Area Inventaris Laboratorium</h1>
<p>Selamat datang di area administrasi. Dari sini Anda dapat mengelola data barang.</p>

<ul>
    <li><a href="add_item.php">Tambah Barang Baru</a></li>
    <li><a href="list_items.php">Lihat Daftar Barang</a></li>
    <li><a href="list_transactions.php">Lihat Daftar Transaksi Peminjaman</a> (TODO)</li>
</ul>


<?php include '../includes/footer.php'; ?>