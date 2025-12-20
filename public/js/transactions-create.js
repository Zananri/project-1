// Transactions Create Page JavaScript
$(document).ready(function() {
    let shouldSubmit = false;

    // Form submit - Save as draft
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        
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
        
        // Convert money format to number
        let totalValue = parseMoney($('#total').val());
        formData.set('total', totalValue);

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
