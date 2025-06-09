<?php
// File: admin/process_edit_item.php
session_start();
// require_once dirname(__DIR__) . '/vendor/autoload.php'; // Jika tidak via header
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
    $barcode_value_input = trim($_POST['barcode_value']); // Sebelumnya rfid_tag
    $status = trim($_POST['status']);

    // Simpan data form ke session untuk diisi kembali jika error
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_data']['item_id_for_redirect'] = $item_id; // Untuk redirect jika error

    if (empty($item_name) || empty($barcode_value_input) || empty($status)) {
        $_SESSION['admin_message'] = "Nama barang, Nilai Barcode, dan Status tidak boleh kosong.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }

    if (!in_array($status, ['available', 'borrowed'])) {
        $_SESSION['admin_message'] = "Status tidak valid.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }

    // Cek apakah barcode_value sudah ada untuk BARANG LAIN
    $sql_check = "SELECT item_id FROM items WHERE barcode_value = ? AND item_id != ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "si", $barcode_value_input, $item_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $_SESSION['admin_message'] = "Nilai Barcode '".htmlspecialchars($barcode_value_input)."' sudah terdaftar untuk barang lain.";
            $_SESSION['admin_message_type'] = "error";
            mysqli_stmt_close($stmt_check);
            header("Location: edit_item.php?id=" . $item_id);
            exit();
        }
        mysqli_stmt_close($stmt_check);
    } else {
        $_SESSION['admin_message'] = "Error database saat pengecekan barcode: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        exit();
    }


    // Update barang
    // Jika kolom rfid_tag sudah tidak ada, hapus dari query
    $sql_update = "UPDATE items SET item_name = ?, description = ?, barcode_value = ?, status = ? WHERE item_id = ?";
    // Jika rfid_tag masih ada dan ingin di-null-kan atau diupdate:
    // $rfid_tag_val_placeholder = null;
    // $sql_update = "UPDATE items SET item_name = ?, description = ?, rfid_tag = ?, barcode_value = ?, status = ? WHERE item_id = ?";

    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
        // Sesuaikan jumlah 's' dan 'i'
        mysqli_stmt_bind_param($stmt_update, "ssssi", $item_name, $description, $barcode_value_input, $status, $item_id);
        // Jika rfid_tag masih ada:
        // mysqli_stmt_bind_param($stmt_update, "sssssi", $item_name, $description, $rfid_tag_val_placeholder, $barcode_value_input, $status, $item_id);

        if (mysqli_stmt_execute($stmt_update)) {
            if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                $_SESSION['admin_message'] = "Barang '".htmlspecialchars($item_name)."' berhasil diperbarui.";
                $_SESSION['admin_message_type'] = "success";
            } else {
                $_SESSION['admin_message'] = "Tidak ada perubahan data pada barang '".htmlspecialchars($item_name)."' atau barang tidak ditemukan.";
                $_SESSION['admin_message_type'] = "info";
            }
            unset($_SESSION['form_data']); // Hapus data form jika sukses
        } else {
            $_SESSION['admin_message'] = "Gagal memperbarui barang: " . mysqli_stmt_error($stmt_update);
            $_SESSION['admin_message_type'] = "error";
            header("Location: edit_item.php?id=" . $item_id);
            mysqli_close($conn);
            exit();
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['admin_message'] = "Error database saat update barang: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
        header("Location: edit_item.php?id=" . $item_id);
        mysqli_close($conn);
        exit();
    }

    mysqli_close($conn);
    header("Location: list_items.php");
    exit();

} else {
    $_SESSION['admin_message'] = "Akses tidak sah.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: list_items.php"); // Redirect ke daftar barang jika bukan POST
    exit();
}
?>