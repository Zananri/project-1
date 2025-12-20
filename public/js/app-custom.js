// Main Application JavaScript
$(document).ready(function() {
    // Setup AJAX CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Sidebar Toggle
    $('#sidebarToggle, #mobileSidebarToggle').click(function() {
        $('.wrapper').toggleClass('sidebar-collapsed');
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Initialize tooltips
    if ($.fn.tooltip) {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    // Money format input
    $('.money-format').on('keyup', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            $(this).val(formatMoney(value));
        }
    });

    // Number only input
    $('.number-only').on('keypress', function(e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
});

// Format money
function formatMoney(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Parse money to number
function parseMoney(money) {
    return parseFloat(money.replace(/\./g, '').replace(/,/g, '.')) || 0;
}

// Show loading overlay
function showLoading() {
    Swal.fire({
        title: 'Memproses...',
        html: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Hide loading overlay
function hideLoading() {
    Swal.close();
}

// Show success message
function showSuccess(message, callback) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback();
        }
    });
}

// Show error message
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        confirmButtonText: 'OK'
    });
}

// Show confirmation dialog
function showConfirmation(title, message, confirmCallback, cancelCallback) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            if (confirmCallback && typeof confirmCallback === 'function') {
                confirmCallback();
            }
        } else {
            if (cancelCallback && typeof cancelCallback === 'function') {
                cancelCallback();
            }
        }
    });
}

// Handle AJAX errors
function handleAjaxError(xhr, status, error) {
    hideLoading();
    
    let message = 'Terjadi kesalahan saat memproses permintaan.';
    
    if (xhr.responseJSON) {
        if (xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        } else if (xhr.responseJSON.errors) {
            let errors = xhr.responseJSON.errors;
            message = Object.values(errors).flat().join('<br>');
        }
    } else if (xhr.responseText) {
        message = xhr.responseText;
    }
    
    showError(message);
}

// Validate form
function validateForm(formId) {
    let form = $(formId)[0];
    if (form.checkValidity() === false) {
        form.classList.add('was-validated');
        return false;
    }
    return true;
}

// Reset form validation
function resetFormValidation(formId) {
    $(formId)[0].classList.remove('was-validated');
}
