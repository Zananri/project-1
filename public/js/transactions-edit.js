// Transactions Edit Page JavaScript
$(document).ready(function() {
    // Check if this is a "dilengkapi" status transaction
    const isDilengkapi = $('#transactionEditForm').data('status') === 'dilengkapi';
    
    // Form submit
    $('#transactionEditForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm('#transactionEditForm')) {
            showError('Mohon lengkapi semua field yang wajib diisi');
            return false;
        }

        showLoading();
        
        let formData = new FormData(this);
        
        // Convert money format to number
        let totalValue = parseMoney($('#total').val());
        formData.set('total', totalValue);

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
