// js/script.js
$(document).ready(function() {
    function updateNoItemsMessage() {
        if ($('#borrow_list li').length === 0) {
            $('#no_items_message').show();
        } else {
            $('#no_items_message').hide();
        }
    }
    // Panggil saat halaman dimuat jika elemen #borrow_list ada
    if ($('#borrow_list').length) {
        updateNoItemsMessage();
    }

    $('#fetchRfidItemButton').on('click', function() {
        $('#rfid_status').html('Memproses...').removeClass('status-success status-error');
        $.ajax({
            url: 'php/fetch_scanned_item.php', // Path ini relatif dari halaman yang memanggil AJAX (root index.php)
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
                if ($('#borrow_list').length) {
                    updateNoItemsMessage();
                }
            },
            error: function() {
                $('#rfid_status').html('Error: Gagal terhubung ke server.').addClass('status-error');
                if ($('#borrow_list').length) {
                    updateNoItemsMessage();
                }
            }
        });
    });

    // Hanya jalankan jika #borrow_list ada di halaman
    if ($('#borrow_list').length) {
        $('#borrow_list').on('click', '.remove-item', function() {
            $(this).closest('li').remove();
            $('#rfid_status').html('Item dihapus dari daftar.').removeClass('status-success status-error');
            updateNoItemsMessage();
        });
    }
    
    // Hanya jalankan jika #borrowForm ada di halaman
    if ($('#borrowForm').length) {
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
    }

    // Anda bisa menambahkan fungsi JavaScript lain di sini untuk halaman admin jika perlu,
    // atau membuat file JS terpisah khusus admin.
});