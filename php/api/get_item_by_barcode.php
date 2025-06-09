<?php
// File: php/api/get_item_by_barcode.php
session_start();
require_once dirname(__DIR__) . '/db_connect.php'; // Ke db_connect.php di folder php

header('Content-Type: application/json');

if (isset($_GET['barcode_value'])) {
    $barcode_value = trim($_GET['barcode_value']);

    if (!empty($barcode_value)) {
        // Simpan barcode yang di-scan ke session untuk diambil oleh fetch_scanned_item.php
        $_SESSION['last_scanned_barcode_value'] = $barcode_value;

        echo json_encode([
            "status" => "success",
            "message" => "Barcode value received: " . htmlspecialchars($barcode_value),
            "received_barcode" => $barcode_value
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Barcode value parameter is empty."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Required parameter 'barcode_value' is missing."]);
}
?>