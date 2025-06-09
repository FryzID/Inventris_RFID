<?php
$page_title = "Daftar Barang";
$base_path = "../";
include '../includes/header.php'; // Autoload via header
require_once '../php/db_connect.php';

if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}

// Pastikan query mengambil 'barcode_value'
$sql = "SELECT item_id, item_name, description, barcode_value, status, added_date FROM items ORDER BY item_id DESC";
$result = mysqli_query($conn, $sql);

// Inisialisasi generator barcode di sini agar tidak diulang dalam loop
$generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();
?>

<h1>Daftar Barang Inventaris</h1>
<p><a href="add_item.php" class="button">Tambah Barang Baru</a></p>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang & Deskripsi</th>
                <th>Barcode</th>
                <th>Status</th>
                <th>Tgl Ditambahkan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['item_id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($row['item_name']); ?></strong><br>
                    <small><i><?php echo htmlspecialchars(substr($row['description'], 0, 70)) . (strlen($row['description']) > 70 ? '...' : ''); ?></i></small>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <?php
                    if (!empty($row['barcode_value'])) {
                        try {
                            // Parameter: data, tipe, width factor, height
                            echo $generatorHTML->getBarcode($row['barcode_value'], $generatorHTML::TYPE_CODE_128, 1.8, 40);
                            echo "<div style='font-size:10px; letter-spacing:1.5px; margin-top:3px;'>" . htmlspecialchars($row['barcode_value']) . "</div>";
                        } catch (Exception $e) {
                            echo "<small style='color:red;'>Error</small>";
                        }
                    } else {
                        echo "N/A";
                    }
                    ?>
                </td>
                <td>
                    <span class="status-tag <?php echo $row['status'] == 'available' ? 'status-available' : 'status-borrowed'; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
                <td><?php echo date("d M Y", strtotime($row['added_date'])); ?></td>
                <td>
                    <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="button secondary small">Edit</a>
                    <a href="delete_item.php?id=<?php echo $row['item_id']; ?>" class="button danger small" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini? Tindakan ini tidak dapat dibatalkan.');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php elseif (!$result): ?>
    <p class="message error">Terjadi kesalahan saat mengambil data barang: <?php echo mysqli_error($conn); ?></p>
<?php else: ?>
    <p>Belum ada barang yang ditambahkan.</p>
<?php endif; ?>

<p style="margin-top: 20px;"><a href="index.php">Kembali ke Admin Area</a></p>

<style>
/* Tambahan style untuk status tag dan tombol kecil jika belum global */
.status-tag { padding: 3px 8px; border-radius: 4px; color: white; font-size: 0.85em; display: inline-block; }
.status-available { background-color: #28a745; }
.status-borrowed { background-color: #dc3545; }
.button.small { padding: 4px 8px; font-size: 0.9em; }
a.button { text-decoration: none; display: inline-block; margin-right: 5px; }
table td { vertical-align: middle; } /* Agar konten sel sejajar tengah vertikal */
</style>

<?php
if($result) mysqli_free_result($result);
mysqli_close($conn);
include '../includes/footer.php';
?>