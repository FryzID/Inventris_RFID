<?php
// File: php/api/get_item_by_rfid.php
session_start();
header('Content-Type: application/json');

if (isset($_GET['rfid_tag'])) {
    $rfid_tag = trim($_GET['rfid_tag']);
    if (!empty($rfid_tag)) {
        $_SESSION['last_scanned_rfid_tag'] = $rfid_tag;
        echo json_encode([
            "status" => "success",
            "message" => "RFID tag received: " . htmlspecialchars($rfid_tag),
            "received_tag" => $rfid_tag
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "RFID tag parameter is empty."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Required parameter 'rfid_tag' is missing."]);
}
?>