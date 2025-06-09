<?php
// File: php/print_receipt.php
$page_title = "Bukti Peminjaman";
$base_path = "../"; // Untuk path CSS dari folder php
$is_print_page = true;
include '../includes/header.php'; // Autoload via header
require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='message error'>ID Transaksi tidak valid.</p>";
    include '../includes/footer.php';
    exit();
}

$transaction_id = intval($_GET['id']);

// Ambil data transaksi
$sql_transaction = "SELECT transaction_id, student_name, borrow_date, notes FROM transactions WHERE transaction_id = ?";
$stmt_transaction = mysqli_prepare($conn, $sql_transaction);
mysqli_stmt_bind_param($stmt_transaction, "i", $transaction_id);
mysqli_stmt_execute($stmt_transaction);
$result_transaction = mysqli_stmt_get_result($stmt_transaction);
$transaction = mysqli_fetch_assoc($result_transaction);
mysqli_stmt_close($stmt_transaction);

if (!$transaction) {
    echo "<p class='message error'>Transaksi tidak ditemukan.</p>";
    include '../includes/footer.php';
    exit();
}

// Ambil data barang yang dipinjam, pastikan mengambil 'barcode_value'
$sql_items = "SELECT i.item_name, i.barcode_value 
              FROM borrowed_items bi
              JOIN items i ON bi.item_id = i.item_id
              WHERE bi.transaction_id = ?";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, "i", $transaction_id);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
$borrowed_items_list = [];
while ($row = mysqli_fetch_assoc($result_items)) {
    $borrowed_items_list[] = $row;
}
mysqli_stmt_close($stmt_items);
mysqli_close($conn);
?>

<h1>Bukti Peminjaman Inventaris</h1>
<button onclick="window.print()" class="no-print button" style="margin-bottom: 20px; background-color: #007bff;">Cetak Bukti Ini</button>

<div class="receipt-details">
    <p><strong>ID Transaksi:</strong> <?php echo htmlspecialchars($transaction['transaction_id']); ?></p>
    <p><strong>Nama Peminjam:</strong> <?php echo htmlspecialchars($transaction['student_name']); ?></p>
    <p><strong>Tanggal Pinjam:</strong> <?php echo date("d M Y, H:i", strtotime($transaction['borrow_date'])); ?></p>
    <?php if ($transaction['notes']): ?>
    <p><strong>Catatan:</strong> <?php echo nl2br(htmlspecialchars($transaction['notes'])); ?></p>
    <?php endif; ?>

    <h2>Barang yang Dipinjam:</h2>
    <?php if (!empty($borrowed_items_list)): ?>
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Nilai Barcode</th> <!-- Diubah dari Tag RFID -->
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($borrowed_items_list as $item): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['barcode_value']); ?></td> <!-- Menampilkan barcode_value -->
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada barang yang tercatat dalam peminjaman ini.</p>
    <?php endif; ?>

    <div class="signature-area print-only" style="margin-top: 50px;">
        <table style="width:100%; border:none;">
            <tr style="border:none;">
                <td style="width:50%; text-align:center; border:none; padding:10px;">
                    <p>Peminjam,</p>
                    <br><br><br><br>
                    <p>( <?php echo htmlspecialchars($transaction['student_name']); ?> )</p>
                </td>
                <td style="width:50%; text-align:center; border:none; padding:10px;">
                    <p>Petugas Laboratorium,</p>
                    <br><br><br><br>
                    <p>( ...................................... )</p>
                </td>
            </tr>
        </table>
    </div>
    <p class="print-only" style="text-align: center; margin-top: 20px; font-size: 0.8em;">
        Harap kembalikan barang sesuai dengan kondisi semula dan tepat waktu.
    </p>
</div>

<?php include '../includes/footer.php'; ?>