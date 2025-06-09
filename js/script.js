// File: INVENTRIS_RFID/js/script.js
$(document).ready(function() {

    // Fungsi untuk escape HTML (mencegah XSS sederhana)
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') {
            if (typeof unsafe === 'number' || typeof unsafe === 'boolean') {
                return unsafe.toString();
            }
            return '';
        }
        return unsafe
             .replace(/&/g, "&")
             .replace(/</g, "<")
             .replace(/>/g, ">")
             .replace(/"/g, """)
             .replace(/'/g, "'");
    }

    // --- LOGIKA UNTUK HALAMAN PEMINJAMAN (index.php) ---
    if ($('#barcode_input_val').length > 0 && $('#borrow_list').length > 0) {
        // Fungsi untuk menampilkan/menyembunyikan pesan "Belum ada barang"
        function updateNoItemsMessage() {
            if ($('#borrow_list li').length === 0) {
                $('#no_items_message').show();
            } else {
                $('#no_items_message').hide();
            }
        }
        updateNoItemsMessage(); // Panggil saat halaman dimuat

        // Fungsi untuk memproses penambahan barang berdasarkan barcode
        function processBarcodeAddItem() {
            const barcodeValue = $('#barcode_input_val').val().trim();
            const statusDiv = $('#scan_status');

            if (barcodeValue === "") {
                // statusDiv.html('Info: Input barcode kosong.').addClass('status-info').removeClass('status-success status-error');
                return;
            }

            statusDiv.html('<i>Mencari barang...</i>').removeClass('status-success status-error status-info');

            $.ajax({
                url: 'php/fetch_item_detail_by_barcode.php', // Path relatif dari index.php
                type: 'GET',
                data: { barcode_value: barcodeValue },
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
                            statusDiv.html('Info: Barang "' + escapeHtml(response.item_name) + '" sudah ada dalam daftar.').addClass('status-info').removeClass('status-success status-error');
                        } else {
                            $('#borrow_list').append(
                                '<li>' +
                                '<span>' + escapeHtml(response.item_name) + ' (ID: ' + escapeHtml(response.item_id.toString()) + ')</span>' +
                                '<input type="hidden" name="item_ids[]" value="' + escapeHtml(response.item_id.toString()) + '">' +
                                '<button type="button" class="remove-item no-print button danger small">Hapus</button>' +
                                '</li>'
                            );
                            statusDiv.html('Barang berhasil ditambahkan: ' + escapeHtml(response.item_name)).addClass('status-success').removeClass('status-error status-info');
                            $('#barcode_input_val').val('').focus();
                        }
                    } else {
                        statusDiv.html('Error: ' + escapeHtml(response.message)).addClass('status-error').removeClass('status-success status-info');
                        $('#barcode_input_val').select().focus();
                    }
                    updateNoItemsMessage();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    statusDiv.html('Error: Gagal menghubungi server. (' + escapeHtml(textStatus) + ')').addClass('status-error').removeClass('status-success status-info');
                    console.error("AJAX Error (Peminjaman): ", textStatus, errorThrown, jqXHR.responseText);
                    updateNoItemsMessage();
                }
            });
        }

        // Event handler untuk tombol "Tambahkan Barang" di halaman peminjaman
        $('#addByBarcodeBtn').on('click', function() {
            processBarcodeAddItem();
        });

        // Event handler untuk Enter pada input barcode di halaman peminjaman
        $('#barcode_input_val').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                processBarcodeAddItem();
            }
        });
        $('#barcode_input_val').focus(); // Otomatis fokus saat halaman peminjaman dimuat

        // Event listener untuk menghapus item dari daftar di halaman peminjaman
        $('#borrow_list').on('click', '.remove-item', function() {
            $(this).closest('li').remove();
            $('#scan_status').html('Item dihapus dari daftar.').addClass('status-info').removeClass('status-success status-error');
            updateNoItemsMessage();
            $('#barcode_input_val').focus();
        });

        // Validasi form peminjaman sebelum submit
        if ($('#borrowForm').length) {
            $('#borrowForm').on('submit', function(e) {
                if ($('#student_name').val().trim() === '') {
                    alert('Nama siswa tidak boleh kosong.');
                    $('#student_name').focus();
                    e.preventDefault();
                    return false;
                }
                if ($('#borrow_list li').length === 0) {
                    alert('Silakan tambahkan minimal satu barang untuk dipinjam.');
                    $('#barcode_input_val').focus();
                    e.preventDefault();
                    return false;
                }
            });
        }
    } // Akhir dari if untuk logika halaman peminjaman

    // --- LOGIKA UNTUK HALAMAN ADMIN (add_item.php & edit_item.php) ---
    if ($('#generateBarcodePreviewBtn').length > 0 && $('#barcode_value').length > 0 && $('#barcode_preview_container').length > 0) {
        
        function showAdminBarcodePreview() {
            var barcodeVal = $('#barcode_value').val().trim(); // Input field di form admin
            var previewContainer = $('#barcode_preview_container');

            if (barcodeVal === "") {
                previewContainer.html('Masukkan nilai barcode untuk melihat preview.');
                return;
            }

            previewContainer.html('<i>Memuat preview barcode...</i>');

            $.ajax({
                url: 'generate_barcode_image.php', // Path relatif dari admin/add_item.php atau admin/edit_item.php
                type: 'POST',
                data: { barcode_data: barcodeVal },
                success: function(response) {
                    previewContainer.html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    previewContainer.html('<p style="color:red;">Gagal memuat preview barcode. Cek console (F12).</p>');
                    console.error("AJAX Error (Admin Barcode Preview):", textStatus, errorThrown);
                    if (jqXHR.responseText) {
                        console.error("Server Response (Admin Barcode Preview):", jqXHR.responseText);
                    }
                }
            });
        }

        $('#generateBarcodePreviewBtn').on('click', function() {
            showAdminBarcodePreview();
        });

        // Untuk halaman edit, barcode awal sudah di-render oleh PHP.
        // Untuk halaman add, jika ada nilai default dan ingin langsung preview:
        var initialAdminBarcodeValue = $('#barcode_value').val().trim();
        // Cek apakah kita di halaman add_item.php (bisa dengan cara lain jika perlu)
        if (initialAdminBarcodeValue !== "" && window.location.pathname.includes('add_item.php')) {
            // showAdminBarcodePreview(); // Aktifkan jika ingin auto preview di add_item.php saat load jika ada nilai
        }
    } // Akhir dari if untuk logika admin preview barcode

});