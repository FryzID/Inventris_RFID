<?php
// File: admin/process_add_item.php
session_start();
// Autoload sudah di-include via header.php atau akan di-include jika diperlukan langsung
// Jika tidak melalui halaman yang include header.php, maka perlu:
// require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once '../php/db_connect.php'; // Path relatif dari folder admin ke php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $barcode_value_input = trim($_POST['barcode_value']);

    // Simpan data form ke session untuk diisi kembali jika error
    $_SESSION['form_data'] = $_POST;

    if (empty($item_name) || empty($barcode_value_input)) {
        $_SESSION['admin_message'] = "Nama barang dan Nilai Barcode tidak boleh kosong.";
        $_SESSION['admin_message_type'] = "error";
        header("Location: add_item.php");
        exit();
    }

    // Cek apakah barcode_value sudah ada
    $sql_check = "SELECT item_id FROM items WHERE barcode_value = ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "s", $barcode_value_input);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $_SESSION['admin_message'] = "Nilai Barcode '".htmlspecialchars($barcode_value_input)."' sudah terdaftar untuk barang lain.";
            $_SESSION['admin_message_type'] = "error";
            mysqli_stmt_close($stmt_check);
            header("Location: add_item.php");
            exit();
        }
        mysqli_stmt_close($stmt_check);
    } else {
        // Error saat prepare statement
        $_SESSION['admin_message'] = "Error database saat pengecekan barcode: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
        header("Location: add_item.php");
        exit();
    }


    // Kolom rfid_tag bisa diisi NULL atau sama dengan barcode_value jika masih digunakan
    // Untuk contoh ini, kita isi null jika kolom rfid_tag masih ada
    $rfid_tag_val_placeholder = null;

    // Pastikan kolom 'rfid_tag' ada di query jika kolomnya masih ada di tabel Anda.
    // Jika Anda telah menghapus kolom 'rfid_tag', hapus dari query juga.
    $sql_insert = "INSERT INTO items (item_name, description, barcode_value, status) VALUES (?, ?, ?, 'available')";
    // Jika rfid_tag masih ada:
    // $sql_insert = "INSERT INTO items (item_name, description, rfid_tag, barcode_value, status) VALUES (?, ?, ?, ?, 'available')";

    if ($stmt_insert = mysqli_prepare($conn, $sql_insert)) {
        // Sesuaikan jumlah 's' dengan jumlah placeholder
        mysqli_stmt_bind_param($stmt_insert, "sss", $item_name, $description, $barcode_value_input);
        // Jika rfid_tag masih ada:
        // mysqli_stmt_bind_param($stmt_insert, "ssss", $item_name, $description, $rfid_tag_val_placeholder, $barcode_value_input);

        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['admin_message'] = "Barang '".htmlspecialchars($item_name)."' berhasil ditambahkan.";
            $_SESSION['admin_message_type'] = "success";
            unset($_SESSION['form_data']); // Hapus data form dari session jika sukses
        } else {
            $_SESSION['admin_message'] = "Gagal menambahkan barang: " . mysqli_stmt_error($stmt_insert);
            $_SESSION['admin_message_type'] = "error";
            header("Location: add_item.php"); // Kembali ke form jika gagal eksekusi
            exit();
        }
        mysqli_stmt_close($stmt_insert);
    } else {
        // Error saat prepare statement insert
        $_SESSION['admin_message'] = "Error database saat menambahkan barang: " . mysqli_error($conn);
        $_SESSION['admin_message_type'] = "error";
        header("Location: add_item.php");
        exit();
    }

    mysqli_close($conn);
    header("Location: list_items.php"); // Arahkan ke daftar barang setelah proses
    exit();

} else {
    // Jika bukan POST, redirect
    $_SESSION['admin_message'] = "Akses tidak sah.";
    $_SESSION['admin_message_type'] = "error";
    header("Location: add_item.php");
    exit();
}
?>