<?php
// File: php/fetch_item_detail_by_id.php (OPSIONAL, jika ingin validasi ulang status untuk manual)
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    echo json_encode(["status" => "error", "message" => "Parameter item_id tidak valid."]);
    exit;
}

$item_id = intval($_GET['item_id']);
$sql = "SELECT item_id, item_name, status FROM items WHERE item_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($item = mysqli_fetch_assoc($result)) {
            echo json_encode([
                "status" => "success",
                "item_id" => $item['item_id'],
                "item_name" => $item['item_name'],
                "item_status" => $item['status'] // Kirim status untuk dicek JS
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Barang dengan ID ".htmlspecialchars($item_id)." tidak ditemukan."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal eksekusi query."]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal siapkan statement."]);
}
mysqli_close($conn);
?>