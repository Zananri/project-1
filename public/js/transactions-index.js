// Transactions Index Page JavaScript
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/transactions/get-data',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('DataTable error:', error, code);
                showError('Gagal memuat data transaksi');
            }
        },
        columns: [
            { 
                data: 'nomor_transaksi',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { data: 'nama_pemohon' },
            { 
                data: 'tanggal_pengajuan',
                className: 'text-center'
            },
            { 
                data: 'total',
                className: 'text-end'
            },
            { 
                data: 'status_label',
                className: 'text-center',
                orderable: false
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    let buttons = `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="/transactions/${row.id}" class="btn btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                    `;
                    
                    // Edit button for draft status
                    if (row.status === 'draft') {
                        buttons += `
                            <a href="/transactions/${row.id}/edit" class="btn btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-delete" data-id="${row.id}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                    
                    // Action button for pejabat
                    if (row.can_approve) {
                        if (row.is_backward_flow) {
                            // Backward flow: show forward button
                            buttons += `
                                <button type="button" class="btn btn-primary btn-approve" data-id="${row.id}" title="Teruskan">
                                    <i class="bi bi-send"></i>
                                </button>
                            `;
                        } else {
                            // Normal flow: show approve button
                            buttons += `
                                <button type="button" class="btn btn-success btn-approve" data-id="${row.id}" title="Setujui">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            `;
                        }
                    }
                    
                    buttons += `</div>`;
                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            loadingRecords: "Memuat data...",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data yang tersedia",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        }
    });

    // Delete transaction
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        showConfirmation(
            'Hapus Transaksi?',
            'Apakah Anda yakin ingin menghapus transaksi ini?',
            function() {
                showLoading();
                
                $.ajax({
                    url: `/transactions/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            showSuccess(response.message, function() {
                                table.ajax.reload();
                            });
                        } else {
                            showError(response.message);
                        }
                    },
                    error: handleAjaxError
                });
            }
        );
    });

    // Quick approve from list
    $(document).on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        window.location.href = `/transactions/${id}`;
    });
});
