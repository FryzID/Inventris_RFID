<?php
// INVENTRIS_RFID/index.php
$page_title = "Form Peminjaman";
$base_path = "./"; // Atau biarkan kosong: $base_path = "";
include 'includes/header.php'; // Path relatif dari root
?>

<h1>Form Peminjaman Inventaris Laboratorium</h1>

<?php
if (isset($_SESSION['borrow_message'])) {
    echo '<div class="message ' . $_SESSION['borrow_message_type'] . '">' . $_SESSION['borrow_message'] . '</div>';
    unset($_SESSION['borrow_message']);
    unset($_SESSION['borrow_message_type']);
}
?>

<form id="borrowForm" action="php/process_borrow.php" method="POST">
    <label for="student_name">Nama Siswa:</label>
    <input type="text" id="student_name" name="student_name" required placeholder="Ketik nama siswa">

    <h2>Tambah Barang (Scan dengan RFID)</h2>
    <p class="no-print">
        <em>
            Simulasi: Klik tombol di bawah ini untuk mengambil data barang yang seolah-olah baru di-scan oleh Arduino.
            Arduino harusnya memanggil: <code><?php echo htmlspecialchars(dirname($_SERVER['PHP_SELF'], 2) . "/php/api/get_item_by_rfid.php?rfid_tag=RFIDTAGDARIARDUINO"); ?></code>
        </em>
    </p>
    <button type="button" id="fetchRfidItemButton" class="no-print">Ambil Barang dari Scan RFID Terakhir</button>
    <div id="rfid_status"></div>

    <h2>Daftar Barang Akan Dipinjam:</h2>
    <ul id="borrow_list">
        <!-- Item yang di-scan akan ditambahkan di sini oleh JavaScript -->
    </ul>
    <p id="no_items_message" style="display: none; color: #777;">Belum ada barang yang ditambahkan.</p>


    <label for="notes">Catatan (Opsional):</label>
    <textarea id="notes" name="notes" placeholder="Catatan tambahan jika ada"></textarea>

    <input type="submit" value="Proses Peminjaman">
</form>

<script>
$(document).ready(function() {
    function updateNoItemsMessage() {
        if ($('#borrow_list li').length === 0) {
            $('#no_items_message').show();
        } else {
            $('#no_items_message').hide();
        }
    }
    updateNoItemsMessage(); // Panggil saat halaman dimuat

    $('#fetchRfidItemButton').on('click', function() {
        $('#rfid_status').html('Memproses...').removeClass('status-success status-error');
        $.ajax({
            url: 'php/fetch_scanned_item.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let itemExists = false;
                    $('#borrow_list input[name="item_ids[]"]').each(function() {
                        if ($(this).val() == response.item_id) {
                            itemExists = true;
                            return false; 
                        }
                    });

                    if (itemExists) {
                        $('#rfid_status').html('Error: Barang "' + response.item_name + '" sudah ada dalam daftar.').addClass('status-error');
                    } else {
                        $('#borrow_list').append(
                            '<li>' +
                            '<span>' + response.item_name + ' (ID: ' + response.item_id + ')</span>' +
                            '<input type="hidden" name="item_ids[]" value="' + response.item_id + '">' +
                            '<button type="button" class="remove-item no-print">Hapus</button>' +
                            '</li>'
                        );
                        $('#rfid_status').html('Barang berhasil ditambahkan: ' + response.item_name).addClass('status-success');
                    }
                } else {
                    $('#rfid_status').html('Error: ' + response.message).addClass('status-error');
                }
                updateNoItemsMessage();
            },
            error: function() {
                $('#rfid_status').html('Error: Gagal terhubung ke server.').addClass('status-error');
                updateNoItemsMessage();
            }
        });
    });

    $('#borrow_list').on('click', '.remove-item', function() {
        $(this).closest('li').remove();
        $('#rfid_status').html('Item dihapus dari daftar.').removeClass('status-success status-error');
        updateNoItemsMessage();
    });

    $('#borrowForm').on('submit', function(e) {
        if ($('#student_name').val().trim() === '') {
            alert('Nama siswa tidak boleh kosong.');
            e.preventDefault();
            return false;
        }
        if ($('#borrow_list li').length === 0) {
            alert('Silakan tambahkan minimal satu barang untuk dipinjam.');
            e.preventDefault(); 
            return false;
        }
    });
});
</script>

<?php
include 'includes/footer.php'; // Path relatif dari root    
?>  