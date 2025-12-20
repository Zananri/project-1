// Transactions Pending Page JavaScript
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#pendingTransactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/transactions-pending/get-data',
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
                    
                    // Action button based on flow type
                    if (row.is_backward_flow) {
                        // Backward flow: show forward button
                        buttons += `
                            <a href="/transactions/${row.id}" class="btn btn-primary" title="Teruskan">
                                <i class="bi bi-send"></i>
                            </a>
                        `;
                    } else {
                        // Normal flow: show approve button
                        buttons += `
                            <a href="/transactions/${row.id}" class="btn btn-success" title="Setujui">
                                <i class="bi bi-check-circle"></i>
                            </a>
                        `;
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
            zeroRecords: "Tidak ada transaksi yang memerlukan persetujuan Anda",
            emptyTable: "Tidak ada transaksi yang memerlukan persetujuan Anda",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        }
    });
});
