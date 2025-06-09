<?php
// File: php/fetch_scanned_item.php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (isset($_SESSION['last_scanned_barcode_value'])) {
    $barcode_value = $_SESSION['last_scanned_barcode_value'];
    // Query berdasarkan barcode_value
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
                    echo json_encode(["status" => "error", "message" => "Barang dengan barcode '".htmlspecialchars($barcode_value)."' sedang dipinjam."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Barang dengan barcode '".htmlspecialchars($barcode_value)."' tidak ditemukan."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengeksekusi query."]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mempersiapkan statement SQL."]);
    }
    mysqli_close($conn);
    // Hapus session setelah diambil agar tidak salah ambil jika tombol ditekan lagi tanpa scan baru
    // unset($_SESSION['last_scanned_barcode_value']);
} else {
    echo json_encode(["status" => "error", "message" => "Belum ada barang yang di-scan via Barcode. Silakan scan barang."]);
}
?>