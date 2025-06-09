// File: INVENTRIS_RFID/js/script.js
$(document).ready(function() {

    // Fungsi untuk escape HTML (mencegah XSS sederhana)
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') {
            if (typeof unsafe === 'number' || typeof unsafe === 'boolean') {
                return unsafe.toString();
            }
            console.warn("escapeHtml dipanggil dengan nilai bukan string/number/boolean:", unsafe);
            return ''; 
        }
        return unsafe
             .replace(/&/g, "&")
             .replace(/</g, "<")
             .replace(/>/g, ">")
             .replace(/"/g, """)
             .replace(/'/g, "'");
    }

    // --- CACHE SELECTOR UMUM UNTUK HALAMAN PEMINJAMAN ---
    const borrowList = $('#borrow_list');
    const noItemsMessage = $('#no_items_message');
    const scanStatusDiv = $('#scan_status'); 
    const studentNameInput = $('#student_name');
    const barcodeInputPeminjaman = $('#barcode_input_val'); 
    const manualItemCoreUIEl = document.getElementById('manual_item_coreui_multiselect'); // Elemen DOM asli untuk CoreUI
    const $manualItemCoreUI = $(manualItemCoreUIEl); // Elemen jQuery untuk event jQuery

    // --- LOGIKA UMUM UNTUK DAFTAR PEMINJAMAN ---
    // Hanya jalankan jika elemen utama halaman peminjaman ada
    if (borrowList.length > 0 || barcodeInputPeminjaman.length > 0 || manualItemCoreUIEl) {

        function updateNoItemsMessage() {
            if (borrowList.find('li').length === 0) {
                noItemsMessage.show();
            } else {
                noItemsMessage.hide();
            }
        }
        if (borrowList.length) {
            updateNoItemsMessage();
        }

        // Fungsi generik untuk menambahkan item ke daftar peminjaman
        function addItemToBorrowList(itemId, itemName, origin = 'scan') {
            let itemExists = false;
            const itemIdStr = String(itemId);

            borrowList.find('input[name="item_ids[]"]').each(function() {
                if ($(this).val() === itemIdStr) { itemExists = true; return false; }
            });

            if (itemExists) {
                scanStatusDiv.html('Info: Barang "' + escapeHtml(itemName) + '" sudah ada dalam daftar peminjaman.').addClass('status-info').removeClass('status-success status-error');
                // Jika dari CoreUI, dan item sudah ada, kita perlu "membatalkan" seleksi terakhir di CoreUI.
                // Ini memerlukan interaksi dengan API CoreUI yang mungkin rumit jika tidak ada cara mudah.
                // Untuk sekarang, kita biarkan, pesan error sudah cukup.
                if (origin === 'manual_coreui' && manualItemCoreUIEl) {
                    // Coba unselect secara programatik jika CoreUI instance ada dan punya metode `setValue` atau `update`
                    // const coreUIInstance = coreui.MultiSelect.getInstance(manualItemCoreUIEl);
                    // if (coreUIInstance) { /* coba manipulasi nilai terpilih */ }
                }
                return false; 
            } else {
                borrowList.append(
                    '<li data-item-id="' + escapeHtml(itemIdStr) + '">' +
                    '<span>' + escapeHtml(itemName) + ' (ID: ' + escapeHtml(itemIdStr) + ')</span>' +
                    '<input type="hidden" name="item_ids[]" value="' + escapeHtml(itemIdStr) + '">' +
                    '<button type="button" class="remove-item no-print button danger small">Hapus</button>' +
                    '</li>'
                );
                scanStatusDiv.html('Barang berhasil ditambahkan: ' + escapeHtml(itemName)).addClass('status-success').removeClass('status-error status-info');
                updateNoItemsMessage();

                if (manualItemCoreUIEl) {
                    const optionInCoreUI = $manualItemCoreUI.find('option[value="' + itemIdStr + '"]');
                    if (optionInCoreUI.length) {
                        optionInCoreUI.prop('disabled', true);
                        // Beritahu CoreUI untuk me-refresh tampilannya (JIKA ADA METODENYA)
                        if (typeof coreui !== 'undefined' && coreui.MultiSelect && coreui.MultiSelect.getInstance) {
                            const multiSelectInstance = coreui.MultiSelect.getInstance(manualItemCoreUIEl);
                            if (multiSelectInstance && typeof multiSelectInstance.update === 'function') {
                                multiSelectInstance.update();
                                console.log("CoreUI MultiSelect instance updated after disabling option: " + itemIdStr);
                            }
                        }
                    }
                }
                return true; 
            }
        }

        if (borrowList.length) {
            borrowList.on('click', '.remove-item', function() {
                const listItem = $(this).closest('li');
                const itemId = String(listItem.data('item-id')); 
                listItem.remove(); 

                if (manualItemCoreUIEl && itemId) {
                    const optionInCoreUI = $manualItemCoreUI.find('option[value="' + itemId + '"]');
                    if (optionInCoreUI.length) {
                        optionInCoreUI.prop('disabled', false);
                        
                        // Hapus dari nilai terpilih CoreUI MultiSelect & update
                        let currentCoreUIValues = $manualItemCoreUI.val() || [];
                        const index = currentCoreUIValues.indexOf(itemId);
                        if (index > -1) {
                            currentCoreUIValues.splice(index, 1);
                            $manualItemCoreUI.val(currentCoreUIValues).trigger('change'); // Memicu 'change' agar CoreUI update
                        } else {
                            // Jika tidak ada di value, mungkin CoreUI perlu di-update secara manual
                           if (typeof coreui !== 'undefined' && coreui.MultiSelect && coreui.MultiSelect.getInstance) {
                                const multiSelectInstance = coreui.MultiSelect.getInstance(manualItemCoreUIEl);
                                if (multiSelectInstance && typeof multiSelectInstance.update === 'function') {
                                    multiSelectInstance.update();
                                }
                            }
                        }
                    }
                }
                scanStatusDiv.html('Item dihapus dari daftar.').addClass('status-info');
                updateNoItemsMessage();
                if (barcodeInputPeminjaman.length > 0) barcodeInputPeminjaman.focus();
            });
        }

        // --- LOGIKA SCAN BARCODE ---
        if (barcodeInputPeminjaman.length > 0) {
            function processBarcodeAddItemScan() {
                const barcodeValue = barcodeInputPeminjaman.val().trim();
                if (barcodeValue === "") return;
                scanStatusDiv.html('<i>Mencari barang via scan...</i>').removeClass('status-success status-error status-info');
                $.ajax({
                    url: 'php/fetch_item_detail_by_barcode.php', 
                    type: 'GET', data: { barcode_value: barcodeValue }, dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            if (addItemToBorrowList(response.item_id, response.item_name, 'scan')) {
                                barcodeInputPeminjaman.val('').focus(); 
                            } else { barcodeInputPeminjaman.select().focus(); }
                        } else {
                            scanStatusDiv.html('Error: ' + escapeHtml(response.message)).addClass('status-error');
                            barcodeInputPeminjaman.select().focus();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        scanStatusDiv.html('Error: Gagal menghubungi server (Scan). (' + escapeHtml(textStatus) + ')').addClass('status-error');
                        console.error("AJAX Error (Peminjaman Scan): ", textStatus, errorThrown, jqXHR.responseText);
                    }
                });
            }
            $('#addByBarcodeBtn').on('click', processBarcodeAddItemScan);
            barcodeInputPeminjaman.on('keypress', function(e) { if (e.which == 13) { e.preventDefault(); processBarcodeAddItemScan(); } });
            barcodeInputPeminjaman.focus(); 
        }

        // --- LOGIKA COREUI MULTI-SELECT ---
        if (manualItemCoreUIEl) { // Gunakan elemen DOM asli untuk CoreUI
            console.log("CoreUI: Elemen #manual_item_coreui_multiselect ditemukan. JS CoreUI harus menginisialisasinya.");

            let lastSelectedCoreUIValues = $manualItemCoreUI.val() || [];

            $manualItemCoreUI.on('change', function(event) {
                // Pastikan CoreUI sudah menginisialisasi elemen ini
                // Cek apakah instance CoreUI MultiSelect ada, jika tidak, jangan lakukan apa-apa
                if (typeof coreui === 'undefined' || !coreui.MultiSelect || !coreui.MultiSelect.getInstance(manualItemCoreUIEl)) {
                    console.warn("CoreUI MultiSelect instance tidak ditemukan untuk #manual_item_coreui_multiselect. Event 'change' mungkin dari select asli.");
                    // Jika ini terjadi, kemungkinan besar CoreUI JS tidak dimuat atau gagal inisialisasi.
                    // Untuk select HTML standar multiple, $(this).val() akan berisi array yang dipilih.
                    // Kita bisa coba tangani secara manual, tapi lebih baik pastikan CoreUI aktif.
                }

                const currentSelectedValues = $(this).val() || [];
                const selectElement = $(this); // elemen <select> jQuery
                
                console.log("CoreUI MultiSelect 'change' event. Last:", lastSelectedCoreUIValues, "Current:", currentSelectedValues);

                // Deteksi item yang BARU DIPILIH
                currentSelectedValues.forEach(itemId => {
                    if (!lastSelectedCoreUIValues.includes(itemId)) {
                        const optionElement = selectElement.find('option[value="' + itemId + '"]');
                        const itemName = optionElement.data('name') || optionElement.text().split(' (Barcode:')[0].trim();
                        
                        if (itemId && itemName) {
                            if (!addItemToBorrowList(itemId, itemName, 'manual_coreui')) {
                                // Gagal tambah (sudah ada).
                                // Jika CoreUI tidak otomatis unselect saat option di-disable,
                                // kita mungkin perlu cara untuk menghapus pilihan ini dari CoreUI.
                                // Ini adalah bagian yang paling bergantung pada API CoreUI.
                                console.warn("Item " + itemName + " sudah ada, tapi mungkin masih terpilih di CoreUI.");
                            }
                        }
                    }
                });

                // Deteksi item yang BARU DI-UNSELECTED
                lastSelectedCoreUIValues.forEach(oldItemId => {
                    if (!currentSelectedValues.includes(oldItemId)) {
                        const optionElement = selectElement.find('option[value="' + oldItemId + '"]');
                        const oldItemName = optionElement.data('name') || optionElement.text().split(' (Barcode:')[0].trim();
                        
                        borrowList.find('li[data-item-id="' + oldItemId + '"]').remove();
                        if (optionElement.length) {
                            optionElement.prop('disabled', false);
                            // Beritahu CoreUI untuk update (JIKA ADA METODENYA)
                            if (typeof coreui !== 'undefined' && coreui.MultiSelect && coreui.MultiSelect.getInstance) {
                                const multiSelectInstance = coreui.MultiSelect.getInstance(manualItemCoreUIEl);
                                if (multiSelectInstance && typeof multiSelectInstance.update === 'function') {
                                    multiSelectInstance.update();
                                }
                            }
                        }
                        scanStatusDiv.html('Item "' + escapeHtml(oldItemName) + '" dihapus dari pilihan.').addClass('status-info');
                        updateNoItemsMessage();
                    }
                });
                
                lastSelectedCoreUIValues = [...currentSelectedValues]; 
            });
        } else {
            console.log("CoreUI: Element #manual_item_coreui_multiselect TIDAK ditemukan.");
        }

        // --- VALIDASI FORM PEMINJAMAN ---
        if ($('#borrowForm').length) { /* ... (validasi form seperti sebelumnya) ... */ }

    } // Akhir dari if elemen peminjaman ada

    // --- LOGIKA ADMIN AREA (PREVIEW BARCODE) ---
    const adminBarcodeValueInput = $('#barcode_value');
    const adminGeneratePreviewBtn = $('#generateBarcodePreviewBtn');
    const adminBarcodePreviewContainer = $('#barcode_preview_container');

    if (adminGeneratePreviewBtn.length > 0 && adminBarcodeValueInput.length > 0 && adminBarcodePreviewContainer.length > 0) {
        /* ... (kode showAdminBarcodePreview dan event listenernya seperti sebelumnya) ... */
    }
});