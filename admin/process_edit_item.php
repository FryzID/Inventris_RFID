<?php
// File: admin/process_edit_item.php
session_start();
require_once '../php/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['item_id']) || !is_numeric($_POST['item_id'])) {
        $_SESSION['admin_message'] = "ID Barang tidak valid.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: list_items.php");
        exit();
    }

    $item_id = intval($_POST['item_id']);
    $item_name = trim($_POST['item_name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $rfid_tag = trim($_POST['rfid_tag']);
    $status = trim($_POST['status']); // Ambil status dari form

    if (empty($item_name) || empty($rfid_tag) || empty($status)) {
        $_SESSION['admin_message'] = "Nama barang, Tag RFID, dan Status tidak boleh kosong.";
        $_SESSION['admin_message_type'] = "error";
        // Redirect kembali ke halaman edit dengan ID yang benar
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }

    // Validasi status
    if (!in_array($status, ['available', 'borrowed'])) { // Tambahkan status lain jika ada
        $_SESSION['admin_message'] = "Status tidak valid.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }

    // Cek apakah RFID tag sudah ada untuk BARANG LAIN
    $sql_check = "SELECT item_id FROM items WHERE rfid_tag = ? AND item_id != ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "si", $rfid_tag, $item_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['admin_message'] = "Tag RFID '".htmlspecialchars($rfid_tag)."' sudah terdaftar untuk barang lain.";
        $_SESSION['admin_message_type'] = "error";
        mysqli_stmt_close($stmt_check);
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // Update barang
    $sql_update = "UPDATE items SET item_name = ?, description = ?, rfid_tag = ?, status = ? WHERE item_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssssi", $item_name, $description, $rfid_tag, $status, $item_id);

    if (mysqli_stmt_execute($stmt_update)) {
        if (mysqli_stmt_affected_rows($stmt_update) > 0) {
            $_SESSION['admin_message'] = "Barang '".htmlspecialchars($item_name)."' berhasil diperbarui.";
            $_SESSION['admin_message_type'] = "success";
        } else {
            $_SESSION['admin_message'] = "Tidak ada perubahan data pada barang '".htmlspecialchars($item_name)."' atau barang tidak ditemukan.";
            $_SESSION['admin_message_type'] = "info"; // Gunakan 'info' jika tidak ada perubahan
        }
    } else {
        $_SESSION['admin_message'] = "Gagal memperbarui barang: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        mysqli_close($conn);
        exit();
    }
    mysqli_stmt_close($stmt_update);
    mysqli_close($conn);
    header("Location: list_items.php");
    exit();

} else {
    // Jika bukan POST, redirect ke daftar barang
    header("Location: list_items.php");
    exit();
}
?>