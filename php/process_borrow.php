<?php
// File: php/process_borrow.php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST['student_name']);
    $item_ids = isset($_POST['item_ids']) ? $_POST['item_ids'] : [];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    if (empty($student_name) || empty($item_ids)) {
        $_SESSION['borrow_message'] = "Nama siswa dan minimal satu barang harus diisi.";
        $_SESSION['borrow_message_type'] = "error";
        header("Location: ../index.php");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // 1. Insert ke tabel transactions
        $sql_transaction = "INSERT INTO transactions (student_name, notes) VALUES (?, ?)";
        $stmt_transaction = mysqli_prepare($conn, $sql_transaction);
        mysqli_stmt_bind_param($stmt_transaction, "ss", $student_name, $notes);
        mysqli_stmt_execute($stmt_transaction);
        $transaction_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_transaction);

        if (!$transaction_id) {
            throw new Exception("Gagal membuat transaksi baru.");
        }

        // 2. Insert ke tabel borrowed_items dan update status item
        $sql_borrowed_item = "INSERT INTO borrowed_items (transaction_id, item_id) VALUES (?, ?)";
        $stmt_borrowed_item = mysqli_prepare($conn, $sql_borrowed_item);

        $sql_update_item = "UPDATE items SET status = 'borrowed' WHERE item_id = ? AND status = 'available'";
        $stmt_update_item = mysqli_prepare($conn, $sql_update_item);

        foreach ($item_ids as $item_id) {
            // Cek status item sebelum update (lebih aman)
            $check_status_sql = "SELECT status FROM items WHERE item_id = ?";
            $stmt_check = mysqli_prepare($conn, $check_status_sql);
            mysqli_stmt_bind_param($stmt_check, "i", $item_id);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            $item_data = mysqli_fetch_assoc($result_check);
            mysqli_stmt_close($stmt_check);

            if (!$item_data || $item_data['status'] != 'available') {
                throw new Exception("Barang dengan ID " . htmlspecialchars($item_id) . " tidak tersedia atau tidak ditemukan.");
            }
            
            // Insert ke borrowed_items
            mysqli_stmt_bind_param($stmt_borrowed_item, "ii", $transaction_id, $item_id);
            mysqli_stmt_execute($stmt_borrowed_item);
            if (mysqli_stmt_affected_rows($stmt_borrowed_item) == 0) {
                 throw new Exception("Gagal mencatat peminjaman barang ID " . htmlspecialchars($item_id));   
            }


            // Update status item
            mysqli_stmt_bind_param($stmt_update_item, "i", $item_id);
            mysqli_stmt_execute($stmt_update_item);
            if (mysqli_stmt_affected_rows($stmt_update_item) == 0) {
                throw new Exception("Gagal update status barang ID " . htmlspecialchars($item_id) . ". Mungkin sudah dipinjam orang lain.");
            }
        }
        mysqli_stmt_close($stmt_borrowed_item);
        mysqli_stmt_close($stmt_update_item);

        mysqli_commit($conn);
        $_SESSION['borrow_message'] = "Peminjaman berhasil diproses.";
        $_SESSION['borrow_message_type'] = "success";
        header("Location: print_receipt.php?id=" . $transaction_id);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['borrow_message'] = "Terjadi kesalahan: " . $e->getMessage();
        $_SESSION['borrow_message_type'] = "error";
        header("Location: ../index.php");
        exit();
    } finally {
        mysqli_close($conn);
    }

} else {
    header("Location: ../index.php");
    exit();
}
?>