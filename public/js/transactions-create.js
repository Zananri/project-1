// Transactions Create Page JavaScript
$(document).ready(function() {
    let shouldSubmit = false;
    let itemCounter = 0;
    let currentGroupCounter = 0;
    let groups = {}; // Track groups by uraian

    // Mode switching
    $('input[name="entry_mode"]').on('change', function() {
        const mode = $(this).val();
        if (mode === 'simple') {
            $('#use_detailed_mode').val('0');
            $('#simpleFields').show();
            $('#detailedFields').hide();
            $('#simpleTotalField').show();
            $('#generalKeterangan').show();
            // Make simple fields required
            $('#uraian_transaksi, #total').prop('required', true);
        } else {
            $('#use_detailed_mode').val('1');
            $('#simpleFields').hide();
            $('#detailedFields').show();
            $('#simpleTotalField').hide();
            $('#generalKeterangan').hide();
            // Remove required from simple fields
            $('#uraian_transaksi, #total').prop('required', false);
            // Focus on first input
            $('#newUraian').focus();
        }
    });
    
    // Apply money format to newNominal field
    applyMoneyFormat($('#newNominal'));

    // Add new item button
    $('#btnAddItem').on('click', function() {
        const uraian = $('#newUraian').val().trim();
        const kebutuhan = $('#newKebutuhan').val().trim();
        const nominal = $('#newNominal').val().trim();
        const dasar = $('#newDasar').val().trim();
        const lawan = $('#newLawan').val().trim();
        const rekening = $('#newRekening').val().trim();
        const tanggal = $('#newTanggal').val();
        const pengakuan = $('#newPengakuan').val().trim();
        const keterangan = $('#newKeterangan').val().trim();
        
        if (!uraian) {
            showError('Uraian Transaksi harus diisi');
            return;
        }
        
        if (!kebutuhan) {
            showError('Kebutuhan harus diisi');
            return;
        }
        
        if (!nominal) {
            showError('Nominal harus diisi');
            return;
        }
        
        addNewItem(uraian, kebutuhan, nominal, dasar, lawan, rekening, tanggal, pengakuan, keterangan);
        
        // Clear all fields except uraian for grouping
        $('#newKebutuhan').val('');
        $('#newNominal').val('');
        $('#newDasar').val('');
        $('#newLawan').val('');
        $('#newRekening').val('');
        $('#newTanggal').val('');
        $('#newPengakuan').val('');
        $('#newKeterangan').val('');
        $('#newKebutuhan').focus();
    });
    
    // Allow enter key to add item
    $('#newUraian, #newKebutuhan, #newNominal, #newDasar, #newLawan, #newRekening, #newTanggal, #newPengakuan, #newKeterangan').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $('#btnAddItem').click();
        }
    });

    // Function to add new item row
    function addNewItem(uraian, kebutuhan, nominal, dasar, lawan, rekening, tanggal, pengakuan, keterangan) {
        itemCounter++;
        
        // Parse nominal value
        const nominalValue = parseMoney(nominal);
        const formattedNominal = formatMoney(nominalValue);
        const formattedTanggal = tanggal ? new Date(tanggal).toLocaleDateString('id-ID') : '-';
        
        // Check if this uraian already exists
        let parentUrutan = null;
        if (groups[uraian]) {
            parentUrutan = groups[uraian].parentUrutan;
            // Add as child row
            const row = `
                <tr data-item-id="${itemCounter}" data-parent="${parentUrutan}">
                    <td class="text-center"></td>
                    <td style="padding-left: 20px;"><small>${kebutuhan}</small>
                        <input type="hidden" name="items[${itemCounter}][uraian_transaksi]" value="${escapeHtml(uraian)}">
                        <input type="hidden" name="items[${itemCounter}][kebutuhan]" value="${escapeHtml(kebutuhan)}">
                        <input type="hidden" name="items[${itemCounter}][parent_urutan]" value="${parentUrutan}">
                    </td>
                    <td><small>${formattedNominal}</small>
                        <input type="hidden" class="item-total" name="items[${itemCounter}][total]" value="${nominalValue}">
                    </td>
                    <td><small>${escapeHtml(dasar)}</small>
                        <input type="hidden" name="items[${itemCounter}][dasar_transaksi]" value="${escapeHtml(dasar)}">
                    </td>
                    <td><small>${escapeHtml(lawan)}</small>
                        <input type="hidden" name="items[${itemCounter}][lawan_transaksi]" value="${escapeHtml(lawan)}">
                    </td>
                    <td><small>${escapeHtml(rekening)}</small>
                        <input type="hidden" name="items[${itemCounter}][rekening_transaksi]" value="${escapeHtml(rekening)}">
                    </td>
                    <td><small>${formattedTanggal}</small>
                        <input type="hidden" name="items[${itemCounter}][rencana_tanggal_transaksi]" value="${tanggal}">
                    </td>
                    <td><small>${escapeHtml(pengakuan)}</small>
                        <input type="hidden" name="items[${itemCounter}][pengakuan_transaksi]" value="${escapeHtml(pengakuan)}">
                    </td>
                    <td><small>${escapeHtml(keterangan)}</small>
                        <input type="hidden" name="items[${itemCounter}][keterangan_item]" value="${escapeHtml(keterangan)}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            // Find the last row of this group and insert after it
            let lastRow = $(`tr[data-parent="${parentUrutan}"]`).last();
            if (lastRow.length > 0) {
                lastRow.after(row);
            } else {
                $(`tr[data-item-id="${parentUrutan}"]`).after(row);
            }
            
            groups[uraian].count++;
        } else {
            // Create new parent row
            currentGroupCounter++;
            parentUrutan = itemCounter;
            groups[uraian] = {
                parentUrutan: parentUrutan,
                count: 1
            };
            
            const row = `
                <tr data-item-id="${itemCounter}" data-is-parent="true">
                    <td class="text-center"><strong>${currentGroupCounter}</strong></td>
                    <td>
                        <strong>${escapeHtml(uraian)}</strong><br>
                        <small style="padding-left: 15px;">${escapeHtml(kebutuhan)}</small>
                        <input type="hidden" name="items[${itemCounter}][uraian_transaksi]" value="${escapeHtml(uraian)}">
                        <input type="hidden" name="items[${itemCounter}][kebutuhan]" value="${escapeHtml(kebutuhan)}">
                    </td>
                    <td><small>${formattedNominal}</small>
                        <input type="hidden" class="item-total" name="items[${itemCounter}][total]" value="${nominalValue}">
                    </td>
                    <td><small>${escapeHtml(dasar)}</small>
                        <input type="hidden" name="items[${itemCounter}][dasar_transaksi]" value="${escapeHtml(dasar)}">
                    </td>
                    <td><small>${escapeHtml(lawan)}</small>
                        <input type="hidden" name="items[${itemCounter}][lawan_transaksi]" value="${escapeHtml(lawan)}">
                    </td>
                    <td><small>${escapeHtml(rekening)}</small>
                        <input type="hidden" name="items[${itemCounter}][rekening_transaksi]" value="${escapeHtml(rekening)}">
                    </td>
                    <td><small>${formattedTanggal}</small>
                        <input type="hidden" name="items[${itemCounter}][rencana_tanggal_transaksi]" value="${tanggal}">
                    </td>
                    <td><small>${escapeHtml(pengakuan)}</small>
                        <input type="hidden" name="items[${itemCounter}][pengakuan_transaksi]" value="${escapeHtml(pengakuan)}">
                    </td>
                    <td><small>${escapeHtml(keterangan)}</small>
                        <input type="hidden" name="items[${itemCounter}][keterangan_item]" value="${escapeHtml(keterangan)}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item" data-is-parent="true" title="Hapus Semua">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#itemsTableBody').append(row);
        }
        
        calculateTotal();
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Remove item
    $(document).on('click', '.btn-remove-item', function() {
        const isParent = $(this).data('is-parent');
        const row = $(this).closest('tr');
        
        if (isParent) {
            // Remove parent and all children
            const itemId = row.data('item-id');
            const uraianInput = row.find('input[name*="[uraian_transaksi]"]');
            const uraian = uraianInput.val();
            
            // Confirm deletion
            if (confirm('Hapus semua item dalam grup "' + uraian + '"?')) {
                // Remove all child rows
                $(`tr[data-parent="${itemId}"]`).remove();
                // Remove parent row
                row.remove();
                // Remove from groups
                delete groups[uraian];
                updateGroupNumbers();
                calculateTotal();
            }
        } else {
            // Remove single child row
            if ($('#itemsTableBody tr').length > 1) {
                const parentId = row.data('parent');
                const uraianInput = row.find('input[name*="[uraian_transaksi]"]');
                const uraian = uraianInput.val();
                
                row.remove();
                
                // Update group count
                if (groups[uraian]) {
                    groups[uraian].count--;
                    // If only parent left, update display
                    if (groups[uraian].count === 1) {
                        // Parent row only, keep it as is
                    }
                }
                
                calculateTotal();
            } else {
                showError('Minimal harus ada 1 item transaksi');
            }
        }
    });

    // Update group numbers
    function updateGroupNumbers() {
        let counter = 1;
        $('#itemsTableBody tr[data-is-parent="true"]').each(function() {
            $(this).find('td:first strong').text(counter);
            counter++;
        });
        currentGroupCounter = counter - 1;
    }

    // Remove updateItemNumbers function as we use groups now

    // Calculate total
    $(document).on('input', '.item-total', function() {
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('.item-total').each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                total += value;
            }
        });
        $('#totalSum').text('Rp ' + formatMoney(total));
    }

    // Apply money format
    function applyMoneyFormat(element) {
        element.on('input', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatMoney(value));
            }
        });
        
        element.on('focus', function() {
            let value = parseMoney($(this).val());
            if (value > 0) {
                $(this).val(value);
            }
        });
        
        element.on('blur', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatMoney(value));
            } else {
                $(this).val('');
            }
        });
    }

    function formatMoney(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Form submit - Save as draft
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate based on mode
        const isDetailed = $('#use_detailed_mode').val() === '1';
        
        if (isDetailed) {
            if ($('#itemsTableBody tr').length === 0) {
                showError('Minimal harus ada 1 uraian transaksi dan nominal');
                return false;
            }
        }
        
        if (!validateForm('#transactionForm')) {
            showError('Mohon lengkapi semua field yang wajib diisi');
            return false;
        }

        // Save as draft
        submitForm(false);
    });

    // Submit button - Save and submit for approval
    $('#btnSubmit').on('click', function(e) {
        e.preventDefault();
        
        // Validate based on mode
        const isDetailed = $('#use_detailed_mode').val() === '1';
        
        if (isDetailed) {
            if ($('#itemsTableBody tr').length === 0) {
                showError('Minimal harus ada 1 uraian transaksi dan nominal');
                return false;
            }
        }
        
        if (!validateForm('#transactionForm')) {
            showError('Mohon lengkapi semua field yang wajib diisi');
            return false;
        }

        showConfirmation(
            'Ajukan Transaksi?',
            'Apakah Anda yakin ingin mengajukan transaksi ini untuk persetujuan? Transaksi yang sudah diajukan tidak dapat diedit.',
            function() {
                submitForm(true);
            }
        );
    });

    function submitForm(submit) {
        showLoading();
        
        let formData = new FormData($('#transactionForm')[0]);
        
        const isDetailed = $('#use_detailed_mode').val() === '1';
        
        if (!isDetailed) {
            // Convert money format to number for simple mode
            let totalValue = parseMoney($('#total').val());
            formData.set('total', totalValue);
        } else {
            // Hidden inputs already have numeric values, no conversion needed
        }

        $.ajax({
            url: '/transactions',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // If should submit after save
                    if (submit) {
                        submitTransaction(response.data.id);
                    } else {
                        hideLoading();
                        showSuccess(response.message, function() {
                            window.location.href = '/transactions';
                        });
                    }
                } else {
                    hideLoading();
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr, status, error);
            }
        });
    }

    function submitTransaction(transactionId) {
        $.ajax({
            url: `/transactions/${transactionId}/submit`,
            type: 'POST',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showSuccess(response.message, function() {
                        window.location.href = `/transactions/${transactionId}`;
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // File upload preview
    $('#lampiran_dokumen').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 5242880) { // 5MB
                showError('Ukuran file maksimal 5MB');
                $(this).val('');
                return;
            }
            
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/jpg',
                'image/png'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                showError('Format file tidak didukung. Gunakan PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG');
                $(this).val('');
                return;
            }
        }
    });
});
