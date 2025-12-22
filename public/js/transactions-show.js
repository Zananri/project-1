// Transactions Show Page JavaScript
$(document).ready(function() {
    // Submit transaction
    $('#btnSubmitTransaction').on('click', function() {
        showConfirmation(
            'Ajukan Transaksi?',
            'Apakah Anda yakin ingin mengajukan transaksi ini untuk persetujuan? Transaksi yang sudah diajukan tidak dapat diedit.',
            function() {
                submitTransaction();
            }
        );
    });

    function submitTransaction() {
        showLoading();
        
        $.ajax({
            url: `/transactions/${transactionId}/submit`,
            type: 'POST',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // Approve form submit
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        
        showConfirmation(
            'Setujui Transaksi?',
            'Apakah Anda yakin ingin menyetujui transaksi ini?',
            function() {
                approveTransaction();
            }
        );
    });

    function approveTransaction() {
        showLoading();
        
        let formData = {
            catatan: $('#approveForm textarea[name="catatan"]').val()
        };
        
        $.ajax({
            url: `/transactions/${transactionId}/approve`,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#approveModal').modal('hide');
                
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // Reject form submit
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#rejectForm textarea[name="alasan_penolakan"]').val()) {
            showError('Alasan penolakan harus diisi');
            return;
        }
        
        showConfirmation(
            'Tolak Transaksi?',
            'Apakah Anda yakin ingin menolak transaksi ini?',
            function() {
                rejectTransaction();
            }
        );
    });

    function rejectTransaction() {
        showLoading();
        
        let formData = {
            alasan_penolakan: $('#rejectForm textarea[name="alasan_penolakan"]').val()
        };
        
        $.ajax({
            url: `/transactions/${transactionId}/reject`,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#rejectModal').modal('hide');
                
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // Request completion form submit
    $('#requestCompletionForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#requestCompletionForm textarea[name="catatan"]').val()) {
            showError('Catatan kelengkapan harus diisi');
            return;
        }
        
        showConfirmation(
            'Minta Kelengkapan?',
            'Apakah Anda yakin ingin meminta kelengkapan untuk transaksi ini?',
            function() {
                requestCompletion();
            }
        );
    });

    function requestCompletion() {
        showLoading();
        
        let formData = {
            catatan: $('#requestCompletionForm textarea[name="catatan"]').val()
        };
        
        $.ajax({
            url: `/transactions/${transactionId}/request-completion`,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#requestCompletionModal').modal('hide');
                
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // Conditional approve form submit
    $('#conditionalApproveForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#conditionalApproveForm textarea[name="catatan"]').val()) {
            showError('Catatan syarat harus diisi');
            return;
        }
        
        showConfirmation(
            'Setujui Bersyarat?',
            'Apakah Anda yakin ingin menyetujui transaksi ini dengan syarat?',
            function() {
                conditionalApprove();
            }
        );
    });

    function conditionalApprove() {
        showLoading();
        
        let formData = {
            catatan: $('#conditionalApproveForm textarea[name="catatan"]').val()
        };
        
        $.ajax({
            url: `/transactions/${transactionId}/conditional-approve`,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#conditionalApproveModal').modal('hide');
                
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: handleAjaxError
        });
    }

    // Reset modal forms when closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        resetFormValidation($(this).find('form'));
    });
});
