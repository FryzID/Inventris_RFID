<?php
$page_title = "Edit Barang";
$base_path = "../";
include '../includes/header.php';
require_once '../php/db_connect.php';

$item_id = null;
$item_name_val = '';
$description_val = '';
$barcode_value_val = '';
$current_status_val = 'available';
$item_exists = false;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_message'] = "ID Barang tidak valid.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}
$item_id = intval($_GET['id']);

$sql_fetch = "SELECT item_name, description, barcode_value, status FROM items WHERE item_id = ?";
if ($stmt_fetch = mysqli_prepare($conn, $sql_fetch)) {
    mysqli_stmt_bind_param($stmt_fetch, "i", $item_id);
    mysqli_stmt_execute($stmt_fetch);
    $result_fetch = mysqli_stmt_get_result($stmt_fetch);
    $item_db = mysqli_fetch_assoc($result_fetch);
    mysqli_stmt_close($stmt_fetch);

    if ($item_db) {
        $item_exists = true;
        $item_name_val = $item_db['item_name'];
        $description_val = $item_db['description'];
        $barcode_value_val = $item_db['barcode_value'];
        $current_status_val = $item_db['status'];

        if (isset($_SESSION['form_data']) && isset($_SESSION['form_data']['item_id_for_redirect']) && $_SESSION['form_data']['item_id_for_redirect'] == $item_id) {
            $item_name_val = isset($_SESSION['form_data']['item_name']) ? $_SESSION['form_data']['item_name'] : $item_name_val;
            $description_val = isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : $description_val;
            $barcode_value_val = isset($_SESSION['form_data']['barcode_value']) ? $_SESSION['form_data']['barcode_value'] : $barcode_value_val;
            $current_status_val = isset($_SESSION['form_data']['status']) ? $_SESSION['form_data']['status'] : $current_status_val;
        }
    } else { /* ... error handling item tidak ditemukan ... */ 
        $_SESSION['admin_message'] = "Barang dengan ID " . htmlspecialchars($item_id) . " tidak ditemukan.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: list_items.php");
        exit();
    }
} else { /* ... error handling prepare statement ... */
    $_SESSION['admin_message'] = "Error database: " . mysqli_error($conn);
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php");
    exit();
}

if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
}
?>

<h1>Edit Barang: <?php echo htmlspecialchars($item_db['item_name']); ?></h1>

<?php if ($item_exists): ?>
<form action="process_edit_item.php" method="POST">
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

    <label for="item_name">Nama Barang:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name_val); ?>" required>

    <label for="description">Deskripsi (Opsional):</label>
    <textarea id="description" name="description"><?php echo htmlspecialchars($description_val); ?></textarea>

    <label for="barcode_value">Nilai Barcode:</label>
    <input type="text" id="barcode_value" name="barcode_value" value="<?php echo htmlspecialchars($barcode_value_val); ?>" required>
    <button type="button" id="generateBarcodePreviewBtn" class="button secondary" style="margin-top: 5px; margin-bottom:10px;">Perbarui Preview Barcode</button>
    <small style="display:block; margin-bottom:10px;">Pastikan Nilai Barcode ini unik.</small>

    <div id="barcode_preview_container" style="margin-top:10px; margin-bottom:20px; padding:10px; border:1px solid #ccc; min-height: 70px; text-align:center; background-color: #f9f9f9;">
        <?php
        if (!empty($barcode_value_val)) {
            $generatorPHP = new Picqer\Barcode\BarcodeGeneratorHTML();
            try {
                echo $generatorPHP->getBarcode($barcode_value_val, $generatorPHP::TYPE_CODE_128, 2, 50, 'black');
                echo "<p style='font-family: monospace; letter-spacing: 1.5px; font-size:11px; margin-top:5px; text-align:center; word-break: break-all;'>" . htmlspecialchars($barcode_value_val) . "</p>";
            } catch (Exception $e) {
                echo '<p style="color:red;">Error membuat barcode awal: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo 'Preview akan muncul di sini setelah nilai barcode diisi/diubah dan tombol diklik.';
        }
        ?>
    </div>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="available" <?php echo ($current_status_val == 'available') ? 'selected' : ''; ?>>Available</option>
        <option value="borrowed" <?php echo ($current_status_val == 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
    </select>
    <small style="display:block; margin-bottom:10px;">Ubah status dengan hati-hati.</small>

    <input type="submit" value="Update Barang">
</form>
<?php endif; ?>

<p style="margin-top: 20px;"><a href="list_items.php">Kembali ke Daftar Barang</a></p>

<?php
if (isset($_SESSION['form_data']) && isset($_SESSION['form_data']['item_id_for_redirect']) && $_SESSION['form_data']['item_id_for_redirect'] == $item_id) {
    unset($_SESSION['form_data']); // Hapus data form dari session setelah ditampilkan di halaman edit
}
mysqli_close($conn);
include '../includes/footer.php';
?>