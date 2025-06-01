<?php
$page_title = "Daftar Barang";
$base_path = "../"; // Dari folder admin ke root
include '../includes/header.php';
require_once '../php/db_connect.php';

if (isset($_SESSION['admin_message'])) {
    echo '<div class="message ' . htmlspecialchars($_SESSION['admin_message_type']) . '">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}

$sql = "SELECT item_id, item_name, description, rfid_tag, status, added_date FROM items ORDER BY item_id DESC";
$result = mysqli_query($conn, $sql);
?>

<h1>Daftar Barang Inventaris</h1>
<p><a href="add_item.php" class="button">Tambah Barang Baru</a></p>

<?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Deskripsi</th>
                <th>Tag RFID</th>
                <th>Status</th>
                <th>Tgl Ditambahkan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['item_id']; ?></td>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : ''); ?></td>
                <td><?php echo htmlspecialchars($row['rfid_tag']); ?></td>
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
<?php else: ?>
    <p>Belum ada barang yang ditambahkan.</p>
<?php endif; ?>

<p style="margin-top: 20px;"><a href="index.php">Kembali ke Admin Area</a></p>

<style>
/* Tambahan style untuk status tag dan tombol kecil */
.status-tag { padding: 3px 8px; border-radius: 4px; color: white; font-size: 0.85em; display: inline-block; }
.status-available { background-color: #28a745; }
.status-borrowed { background-color: #dc3545; }
.button.small { padding: 4px 8px; font-size: 0.9em; }
a.button { text-decoration: none; display: inline-block; margin-right: 5px; }
</style>

<?php
mysqli_close($conn);
include '../includes/footer.php';
?>