// Transactions Edit Page JavaScript
$(document).ready(function() {
    // Check if this is a "dilengkapi" status transaction
    const isDilengkapi = $('#transactionEditForm').data('status') === 'dilengkapi';
    
    // Item counter for detailed mode
    let itemIndex = $('.item-group').length;

    // Add new item (for detailed mode)
    $('#addItemBtn').on('click', function() {
        const newItem = createItemTemplate(itemIndex);
        $('#itemsContainer').append(newItem);
        itemIndex++;
        updateItemNumbers();
        initMoneyFormat();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-group').remove();
        updateItemNumbers();
    });

    // Update item numbers
    function updateItemNumbers() {
        $('.item-group').each(function(index) {
            $(this).find('.item-number').text(index + 1);
        });
    }

    // Create item template
    function createItemTemplate(index) {
        return `
            <div class="item-group mb-4 border-bottom pb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Item #<span class="item-number">${index + 1}</span></h6>
                    <button type="button" class="btn btn-sm btn-danger remove-item">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Uraian Transaksi</label>
                        <input type="text" class="form-control" name="items[${index}][uraian_transaksi]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Kebutuhan</label>
                        <input type="text" class="form-control" name="items[${index}][kebutuhan]" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Total</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control money-format" name="items[${index}][total]" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dasar Transaksi</label>
                        <input type="text" class="form-control" name="items[${index}][dasar_transaksi]">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lawan Transaksi</label>
                        <input type="text" class="form-control" name="items[${index}][lawan_transaksi]">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rekening Transaksi</label>
                        <input type="text" class="form-control" name="items[${index}][rekening_transaksi]">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rencana Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="items[${index}][rencana_tanggal_transaksi]">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengakuan Transaksi</label>
                        <input type="text" class="form-control" name="items[${index}][pengakuan_transaksi]">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Keterangan Item</label>
                        <textarea class="form-control" name="items[${index}][keterangan_item]" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Form submit
    $('#transactionEditForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm('#transactionEditForm')) {
            showError('Mohon lengkapi semua field yang wajib diisi');
            return false;
        }

        showLoading();
        
        let formData = new FormData();
        
        // Add CSRF and method
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');
        
        // Add basic fields
        formData.append('nama_pemohon', $('input[name="nama_pemohon"]').val());
        formData.append('nama_perusahaan', $('input[name="nama_perusahaan"]').val());
        formData.append('tanggal_pengajuan', $('input[name="tanggal_pengajuan"]').val());
        
        // Check if detailed mode
        if ($('input[name="use_detailed_mode"]').length) {
            formData.append('use_detailed_mode', '1');
            
            // Re-index items to ensure sequential array indices
            $('.item-group').each(function(newIndex) {
                $(this).find('input, textarea').each(function() {
                    let name = $(this).attr('name');
                    if (name && name.includes('items[')) {
                        // Extract field name (e.g., "uraian_transaksi" from "items[0][uraian_transaksi]")
                        let fieldName = name.match(/\[([^\]]+)\]$/)[1];
                        let value = $(this).val();
                        
                        // Apply money format conversion for total field
                        if (fieldName === 'total') {
                            value = parseMoney(value);
                        }
                        
                        if (value) {
                            formData.append(`items[${newIndex}][${fieldName}]`, value);
                        }
                    }
                });
            });
        } else {
            // Simple mode
            formData.append('uraian_transaksi', $('textarea[name="uraian_transaksi"]').val());
            formData.append('total', parseMoney($('input[name="total"]').val()));
            
            if ($('textarea[name="dasar_transaksi"]').length) {
                formData.append('dasar_transaksi', $('textarea[name="dasar_transaksi"]').val() || '');
            }
            if ($('input[name="lawan_transaksi"]').length) {
                formData.append('lawan_transaksi', $('input[name="lawan_transaksi"]').val() || '');
            }
            if ($('input[name="rekening_transaksi"]').length) {
                formData.append('rekening_transaksi', $('input[name="rekening_transaksi"]').val() || '');
            }
            if ($('input[name="rencana_tanggal_transaksi"]').length) {
                formData.append('rencana_tanggal_transaksi', $('input[name="rencana_tanggal_transaksi"]').val() || '');
            }
            if ($('input[name="pengakuan_transaksi"]').length) {
                formData.append('pengakuan_transaksi', $('input[name="pengakuan_transaksi"]').val() || '');
            }
            if ($('textarea[name="keterangan"]').length) {
                formData.append('keterangan', $('textarea[name="keterangan"]').val() || '');
            }
        }
        
        // Add file if exists
        let fileInput = document.getElementById('lampiran_dokumen');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('lampiran_dokumen', fileInput.files[0]);
        }

        $.ajax({
            url: `/transactions/${transactionId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    let message = response.message;
                    
                    // If this was a "dilengkapi" transaction, show special message
                    if (isDilengkapi) {
                        message = 'Data berhasil dilengkapi dan transaksi telah diajukan kembali untuk persetujuan!';
                    }
                    
                    showSuccess(message, function() {
                        window.location.href = `/transactions/${transactionId}`;
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    });

    // File upload validation
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
