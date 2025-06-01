<?php
// File: php/fetch_scanned_item.php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (isset($_SESSION['last_scanned_rfid_tag'])) {
    $rfid_tag = $_SESSION['last_scanned_rfid_tag'];
    $sql = "SELECT item_id, item_name, status FROM items WHERE rfid_tag = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $rfid_tag);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($item = mysqli_fetch_assoc($result)) {
                if ($item['status'] == 'available') {
                    echo json_encode([
                        "status" => "success",
                        "item_id" => $item['item_id'],
                        "item_name" => $item['item_name']
                    ]);
                    // Jangan unset session di sini agar bisa dipanggil lagi jika perlu verifikasi.
                    // Arduino akan menimpa nilainya saat scan baru.
                } else {
                    echo json_encode(["status" => "error", "message" => "Barang dengan tag RFID '".htmlspecialchars($rfid_tag)."' sedang dipinjam."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Barang dengan tag RFID '".htmlspecialchars($rfid_tag)."' tidak ditemukan."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengeksekusi query."]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mempersiapkan statement SQL."]);
    }
    mysqli_close($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Belum ada barang yang di-scan via RFID. Silakan scan barang terlebih dahulu."]);
}
?>