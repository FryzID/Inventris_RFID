<?php
// File: php/fetch_item_detail_by_barcode.php
require_once 'db_connect.php'; // Path ke file koneksi database Anda

header('Content-Type: application/json');

if (!isset($_GET['barcode_value']) || empty(trim($_GET['barcode_value']))) {
    echo json_encode(["status" => "error", "message" => "Parameter barcode_value tidak ada atau kosong."]);
    exit;
}

$barcode_value = trim($_GET['barcode_value']);

// Query berdasarkan barcode_value
// Pastikan nama kolom di database Anda adalah 'barcode_value'
$sql = "SELECT item_id, item_name, status FROM items WHERE barcode_value = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $barcode_value);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($item = mysqli_fetch_assoc($result)) {
            if ($item['status'] == 'available') {
                echo json_encode([
                    "status" => "success",
                    "item_id" => $item['item_id'],
                    "item_name" => $item['item_name']
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Barang dengan barcode '".htmlspecialchars($barcode_value)."' sedang tidak tersedia (status: ".htmlspecialchars($item['status']).")."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Barang dengan barcode '".htmlspecialchars($barcode_value)."' tidak ditemukan di sistem."]);
        }
    } else {
        // Error saat eksekusi query
        error_log("SQL Execute Error in fetch_item_detail_by_barcode: " . mysqli_stmt_error($stmt));
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan pada server saat mencari barang."]);
    }
    mysqli_stmt_close($stmt);
} else {
    // Error saat mempersiapkan statement
    error_log("SQL Prepare Error in fetch_item_detail_by_barcode: " . mysqli_error($conn));
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan pada server saat mempersiapkan pencarian."]);
}
mysqli_close($conn);
?>