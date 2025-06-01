<?php
// File: admin/process_add_item.php
session_start();
require_once '../php/db_connect.php'; // Path relatif dari folder admin ke php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $rfid_tag = trim($_POST['rfid_tag']);

    if (empty($item_name) || empty($rfid_tag)) {
        $_SESSION['admin_message'] = "Nama barang dan Tag RFID tidak boleh kosong.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: add_item.php");
        exit();
    }

    // Cek apakah RFID tag sudah ada
    $sql_check = "SELECT item_id FROM items WHERE rfid_tag = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $rfid_tag);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['admin_message'] = "Tag RFID '".htmlspecialchars($rfid_tag)."' sudah terdaftar untuk barang lain.";
        $_SESSION['admin_message_type'] = "error";
        mysqli_stmt_close($stmt_check);
        header("Location: add_item.php");
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // Insert barang baru
    $sql_insert = "INSERT INTO items (item_name, description, rfid_tag, status) VALUES (?, ?, ?, 'available')";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sss", $item_name, $description, $rfid_tag);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['admin_message'] = "Barang '".htmlspecialchars($item_name)."' berhasil ditambahkan.";
        $_SESSION['admin_message_type'] = "success";
    } else {
        $_SESSION['admin_message'] = "Gagal menambahkan barang: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
    }
    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
    header("Location: list_items.php"); // Arahkan ke daftar barang setelah proses
    exit();

} else {
    header("Location: add_item.php");
    exit();
}
?>