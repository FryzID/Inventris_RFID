<?php
$page_title = "Edit Barang";
$base_path = "../";
include '../includes/header.php';
require_once '../php/db_connect.php';

$item_id = null;
$item_name_val = '';
$description_val = '';
$rfid_tag_val = '';
$current_status_val = 'available'; // Default status

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_message'] = "ID Barang tidak valid untuk diedit.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}

$item_id = intval($_GET['id']);

// Ambil data barang yang akan diedit
$sql_fetch = "SELECT item_name, description, rfid_tag, status FROM items WHERE item_id = ?";
$stmt_fetch = mysqli_prepare($conn, $sql_fetch);
mysqli_stmt_bind_param($stmt_fetch, "i", $item_id);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$item = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$item) {
    $_SESSION['admin_message'] = "Barang dengan ID " . htmlspecialchars($item_id) . " tidak ditemukan.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}

$item_name_val = $item['item_name'];
$description_val = $item['description'];
$rfid_tag_val = $item['rfid_tag'];
$current_status_val = $item['status'];

// Tampilkan pesan jika ada
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}
?>

<h1>Edit Barang: <?php echo htmlspecialchars($item_name_val); ?></h1>

<form action="process_edit_item.php" method="POST">
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name_val); ?>" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"><?php echo htmlspecialchars($description_val); ?></textarea>

    <label for="rfid_tag">Tag RFID:</label>
    <input type="text" id="rfid_tag" name="rfid_tag" value="<?php echo htmlspecialchars($rfid_tag_val); ?>" required>
    <small>Pastikan Tag RFID ini unik untuk setiap barang.</small>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="available" <?php echo ($current_status_val == 'available') ? 'selected' : ''; ?>>Available</option>
        <option value="borrowed" <?php echo ($current_status_val == 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
        <!-- Tambahkan status lain jika ada, misal 'maintenance', 'broken' -->
    </select>
    <small>Ubah status dengan hati-hati. Jika status 'borrowed', pastikan ada transaksi peminjaman yang aktif untuk barang ini.</small>


    <input type="submit" value="Update Barang">
</form>

<p style="margin-top: 20px;"><a href="list_items.php">Kembali ke Daftar Barang</a></p>

<?php
mysqli_close($conn);
include '../includes/footer.php';
?>