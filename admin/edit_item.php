<?php
$page_title = "Edit Barang";
$base_path = "../"; // Path dari admin/ ke root/ untuk CSS, JS di header/footer
include '../includes/header.php'; // Ini akan include vendor/autoload.php
require_once '../php/db_connect.php'; // Diperlukan untuk fetch data barang

$item_id = null;
$item_name_val = '';
$description_val = '';
$barcode_value_val = '';
$current_status_val = 'available';
$item_exists = false; // Flag untuk menandakan apakah item ditemukan

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_message'] = "ID Barang tidak valid untuk diedit.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}

$item_id = intval($_GET['id']);

// Ambil data barang yang akan diedit dari database
$sql_fetch = "SELECT item_name, description, barcode_value, status FROM items WHERE item_id = ?";
if ($stmt_fetch = mysqli_prepare($conn, $sql_fetch)) {
    mysqli_stmt_bind_param($stmt_fetch, "i", $item_id);
    mysqli_stmt_execute($stmt_fetch);
    $result_fetch = mysqli_stmt_get_result($stmt_fetch);
    $item_db = mysqli_fetch_assoc($result_fetch); // Data dari database
    mysqli_stmt_close($stmt_fetch);

    if ($item_db) {
        $item_exists = true;
        // Default values dari database
        $item_name_val = $item_db['item_name'];
        $description_val = $item_db['description'];
        $barcode_value_val = $item_db['barcode_value'];
        $current_status_val = $item_db['status'];

        // Timpa dengan data dari session jika ada (misalnya setelah error validasi di process_edit_item.php)
        if (isset($_SESSION['form_data']) && isset($_SESSION['form_data']['item_id_for_redirect']) && $_SESSION['form_data']['item_id_for_redirect'] == $item_id) {
            $item_name_val = isset($_SESSION['form_data']['item_name']) ? $_SESSION['form_data']['item_name'] : $item_name_val;
            $description_val = isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : $description_val;
            $barcode_value_val = isset($_SESSION['form_data']['barcode_value']) ? $_SESSION['form_data']['barcode_value'] : $barcode_value_val;
            $current_status_val = isset($_SESSION['form_data']['status']) ? $_SESSION['form_data']['status'] : $current_status_val;
        }

    } else {
        $_SESSION['admin_message'] = "Barang dengan ID " . htmlspecialchars($item_id) . " tidak ditemukan.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: list_items.php");
        exit();
    }
} else {
    // Error saat prepare statement
    $_SESSION['admin_message'] = "Error database saat mengambil data barang: " . mysqli_error($conn);
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}

// Tampilkan pesan notifikasi jika ada
if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']); // Hapus pesan setelah ditampilkan
    // Jangan unset $_SESSION['form_data'] di sini, biarkan process_edit_item.php yang menghapusnya jika sukses
}
?>

<h1>Edit Barang: <?php echo htmlspecialchars($item_db['item_name']); // Tampilkan nama asli dari DB untuk judul ?></h1>

<?php if ($item_exists): // Hanya tampilkan form jika item ditemukan ?>
<form action="process_edit_item.php" method="POST">
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name_val); ?>" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"><?php echo htmlspecialchars($description_val); ?></textarea>

    <label for="barcode_value">Nilai Barcode:</label>
    <input type="text" id="barcode_value" name="barcode_value" value="<?php echo htmlspecialchars($barcode_value_val); ?>" required>
    <!-- <button type="button" id="generateBarcodePreviewBtn" class="button secondary" style="margin-top: 5px; margin-bottom:10px;">Lihat Preview Barcode</button> -->
    <small style="display:block; margin-bottom:10px;">Pastikan Nilai Barcode ini unik untuk setiap barang.</small>

    <div id="barcode_preview_container" style="margin-top:10px; margin-bottom:20px; padding:10px; border:1px solid #ccc; min-height: 70px; text-align:center;">
        <?php
        // Tampilkan barcode awal oleh PHP saat halaman edit dimuat jika ada barcode_value
        if (!empty($barcode_value_val)) {
            // Autoloader sudah di-include via header.php
            $generatorPHP = new Picqer\Barcode\BarcodeGeneratorHTML();
            try {
                echo $generatorPHP->getBarcode($barcode_value_val, $generatorPHP::TYPE_CODE_128, 2, 50); // widthFactor=2, height=50
                echo "<p style='font-family: monospace; letter-spacing: 1.5px; font-size:11px; margin-top:5px; text-align:center; word-break: break-all;'>" . htmlspecialchars($barcode_value_val) . "</p>";
            } catch (Exception $e) {
                echo '<p style="color:red;">Error saat membuat barcode awal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo 'Preview Barcode akan muncul di sini setelah nilai dimasukkan dan tombol diklik.';
        }
        ?>
    </div>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="available" <?php echo ($current_status_val == 'available') ? 'selected' : ''; ?>>Available</option>
        <option value="borrowed" <?php echo ($current_status_val == 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
        <!-- Tambahkan status lain jika ada -->
    </select>
    <small style="display:block; margin-bottom:10px;">Ubah status dengan hati-hati. Jika barang sedang dipinjam, mengubah status di sini mungkin menyebabkan inkonsistensi data.</small>

    <input type="submit" value="Update Barang">
</form>
<?php endif; ?>

<p style="margin-top: 20px;"><a href="list_items.php">Kembali ke Daftar Barang</a></p>

<?php
// Jangan unset $_SESSION['form_data'] di sini, biarkan process_edit_item.php
mysqli_close($conn);
include '../includes/footer.php'; // Ini akan include js/script.js
?>