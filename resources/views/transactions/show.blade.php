@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi - {{ $transaction->nomor_transaksi }}</h5>
                    <div>
                        @if(($transaction->status === 'draft' || $transaction->status === 'dilengkapi') && $transaction->user_id === Auth::id())
                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit & Lengkapi
                        </a>
                        @endif
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Status Badge -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-{{ $transaction->status === 'selesai' ? 'success' : ($transaction->status === 'ditolak' ? 'danger' : 'warning') }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Status: 
                                        <strong>{{ ucwords(str_replace('_', ' ', $transaction->status)) }}</strong>
                                    </h6>
                                    @if($transaction->alasan_penolakan && in_array($transaction->status, ['ditolak', 'dilengkapi', 'disetujui_bersyarat']))
                                    <hr>
                                    <p class="mb-0">
                                        <strong>
                                            @if($transaction->status === 'ditolak')
                                            Alasan Penolakan:
                                            @elseif($transaction->status === 'dilengkapi')
                                            Catatan Kelengkapan:
                                            @elseif($transaction->status === 'disetujui_bersyarat')
                                            Syarat Persetujuan:
                                            @endif
                                        </strong> {{ $transaction->alasan_penolakan }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="200"><strong>Nomor Transaksi</strong></td>
                                <td>: {{ $transaction->nomor_transaksi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Pemohon</strong></td>
                                <td>: {{ $transaction->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Perusahaan</strong></td>
                                <td>: {{ $transaction->nama_perusahaan }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengajuan</strong></td>
                                <td>: {{ $transaction->tanggal_pengajuan->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>: <strong class="text-primary">Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if(!$transaction->hasItems() || $transaction->items->count() == 1)
                            <!-- Only show these for simple mode -->
                            <tr>
                                <td width="200"><strong>Lawan Transaksi</strong></td>
                                <td>: {{ $transaction->lawan_transaksi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rekening Transaksi</strong></td>
                                <td>: {{ $transaction->rekening_transaksi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rencana Tanggal</strong></td>
                                <td>: {{ $transaction->rencana_tanggal_transaksi ? $transaction->rencana_tanggal_transaksi->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pengakuan Transaksi</strong></td>
                                <td>: {{ $transaction->pengakuan_transaksi ?? '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if(!$transaction->hasItems() || $transaction->items->count() == 1)
                <!-- Only show these sections for simple mode -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Uraian Transaksi</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $transaction->uraian_transaksi }}
                        </div>
                    </div>
                </div>

                @if($transaction->dasar_transaksi)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Dasar Transaksi</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $transaction->dasar_transaksi }}
                        </div>
                    </div>
                </div>
                @endif

                @if($transaction->keterangan)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Keterangan</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $transaction->keterangan }}
                        </div>
                    </div>
                </div>
                @endif
                @endif

                <!-- Transaction Items Table -->
                @if($transaction->hasItems() && $transaction->items->count() > 1)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3">Detail Transaksi</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="3%">No</th>
                                        <th width="15%">Uraian Transaksi</th>
                                        <th width="8%">Total</th>
                                        <th width="15%">Dasar Transaksi</th>
                                        <th width="10%">Lawan Transaksi</th>
                                        <th width="10%">Rekening</th>
                                        <th width="8%">Tgl Rencana</th>
                                        <th width="10%">Pengakuan</th>
                                        <th width="15%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupedItems = [];
                                        foreach($transaction->items as $item) {
                                            $uraian = $item->uraian_transaksi;
                                            if (!isset($groupedItems[$uraian])) {
                                                $groupedItems[$uraian] = [];
                                            }
                                            $groupedItems[$uraian][] = $item;
                                        }
                                        $groupNo = 1;
                                    @endphp
                                    
                                    @foreach($groupedItems as $uraian => $items)
                                        @foreach($items as $index => $item)
                                            <tr>
                                                @if($index === 0)
                                                    <td class="text-center" rowspan="{{ count($items) }}"><strong>{{ $groupNo }}</strong></td>
                                                    <td>
                                                        <strong>{{ $uraian }}</strong><br>
                                                        <small style="padding-left: 15px;">{{ $item->kebutuhan }}</small>
                                                    </td>
                                                @else
                                                    <td style="padding-left: 20px;"><small>{{ $item->kebutuhan }}</small></td>
                                                @endif
                                                <td class="text-end"><small>{{ number_format($item->total, 0, ',', '.') }}</small></td>
                                                <td><small>{{ $item->dasar_transaksi ?? '-' }}</small></td>
                                                <td><small>{{ $item->lawan_transaksi ?? '-' }}</small></td>
                                                <td><small>{{ $item->rekening_transaksi ?? '-' }}</small></td>
                                                <td><small>{{ $item->rencana_tanggal_transaksi ? $item->rencana_tanggal_transaksi->format('d/m/Y') : '-' }}</small></td>
                                                <td><small>{{ $item->pengakuan_transaksi ?? '-' }}</small></td>
                                                <td><small>{{ $item->keterangan ?? '-' }}</small></td>
                                            </tr>
                                        @endforeach
                                        @php $groupNo++; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="2" class="text-end">TOTAL:</th>
                                        <th class="text-end">Rp {{ number_format($transaction->getTotalFromItems(), 0, ',', '.') }}</th>
                                        <th colspan="6"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if($transaction->dasar_transaksi && !$transaction->hasItems())
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Dasar Transaksi</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $transaction->dasar_transaksi }}
                        </div>
                    </div>
                </div>
                @endif

                @if($transaction->keterangan)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6>Keterangan</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $transaction->keterangan }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Download Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3">Lampiran & Dokumen</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($transaction->excel_path)
                            <a href="{{ route('transactions.downloadExcel', $transaction->id) }}" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Download Form Excel
                            </a>
                            @endif
                            
                            @if($transaction->lampiran_dokumen)
                            <a href="{{ Storage::url($transaction->lampiran_dokumen) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-file-earmark-arrow-down"></i> Download Lampiran Upload
                            </a>
                            @endif
                            
                            @if($transaction->excel_path || $transaction->lampiran_dokumen)
                            <a href="{{ route('transactions.downloadAll', $transaction->id) }}" class="btn btn-warning">
                                <i class="bi bi-file-earmark-zip"></i> Download Semua (ZIP)
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$transaction->excel_path && !$transaction->lampiran_dokumen)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Tidak ada lampiran dokumen
                        </div>
                    </div>
                </div>
                @endif

                <!-- Approval Timeline -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5 class="mb-4">Timeline Persetujuan</h5>
                        <div class="timeline">
                            @foreach($transaction->approvals as $approval)
                            <div class="timeline-item {{ $approval->status === 'approved' ? 'completed' : ($approval->status === 'rejected' ? 'rejected' : 'pending') }}">
                                <div class="timeline-marker">
                                    @if($approval->status === 'approved')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @elseif($approval->status === 'rejected')
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                    @else
                                        <i class="bi bi-clock-fill text-warning"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $approval->role_name }}</h6>
                                            <p class="mb-0 text-muted small">{{ $approval->user->name }}</p>
                                            @if($approval->catatan)
                                            <p class="mb-0 mt-2"><strong>Catatan:</strong> {{ $approval->catatan }}</p>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $approval->status_badge }}">
                                                {{ ucfirst($approval->status) }}
                                            </span>
                                            @if($approval->tanggal_approval)
                                            <p class="mb-0 text-muted small mt-1">
                                                {{ $approval->tanggal_approval->format('d/m/Y H:i') }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action Buttons for Pejabat -->
                @if(Auth::user()->isPejabat() && $transaction->canBeApprovedBy(Auth::user()->role))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                @if($transaction->isBackwardFlow())
                                    <h6 class="mb-3">Tindakan Penerusan</h6>
                                    <div class="d-flex gap-2">
                                        @if($transaction->status === 'disetujui_pejabat_4' || $transaction->status === 'disetujui_bersyarat')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="bi bi-send"></i> Teruskan ke Pejabat 2
                                        </button>
                                        @elseif($transaction->status === 'diinformasikan')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="bi bi-send"></i> Selesaikan & Kirim ke Pemohon
                                        </button>
                                        @endif
                                    </div>
                                @else
                                    <h6 class="mb-3">Tindakan Persetujuan</h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="bi bi-x-circle"></i> Tolak
                                        </button>
                                        @if(Auth::user()->role === 'pejabat_3')
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#requestCompletionModal">
                                            <i class="bi bi-arrow-clockwise"></i> Minta Kelengkapan
                                        </button>
                                        @endif
                                        @if(Auth::user()->role === 'pejabat_4')
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#conditionalApproveModal">
                                            <i class="bi bi-check-circle-fill"></i> Setujui Bersyarat
                                        </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Success Notification for Pemohon when transaction is completed -->
                @if($transaction->status === 'selesai' && $transaction->user_id === Auth::id())
                <div class="row mt-4">
                    <div class="col-12">
                        @php
                            // Check if approval is conditional
                            $pejabat4Approval = $transaction->approvals()
                                ->where('role', 'pejabat_4')
                                ->whereIn('status', ['approved', 'conditional'])
                                ->first();
                            $isConditional = $pejabat4Approval && $pejabat4Approval->status === 'conditional';
                        @endphp
                        
                        <div class="card {{ $isConditional ? 'bg-info' : 'bg-success' }} text-white">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    <i class="bi bi-check-circle-fill"></i> 
                                    Transaksi Telah {{ $isConditional ? 'Disetujui Bersyarat' : 'Disetujui' }}
                                </h5>
                                
                                @if($isConditional)
                                    <p class="mb-2">Formulir Pengajuan transaksi Anda telah disetujui bersyarat oleh semua Pejabat yang berwenang.</p>
                                    <p class="mb-3"><strong>Perhatian:</strong> Terdapat syarat/ketentuan yang harus dipenuhi dalam pelaksanaan transaksi ini.</p>
                                    
                                    @if($pejabat4Approval->catatan)
                                    <div class="alert alert-warning mb-0">
                                        <h6 class="mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Syarat Persetujuan dari Pejabat 4:</h6>
                                        <p class="mb-0">{{ $pejabat4Approval->catatan }}</p>
                                    </div>
                                    @endif
                                @else
                                    <p class="mb-2">Selamat! Formulir Pengajuan transaksi Anda telah disetujui oleh semua Pejabat yang berwenang.</p>
                                    <p class="mb-0">Anda dapat melakukan arsip dokumen ini sebagai dasar untuk melaksanakan transaksi.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Submit Button for Pemohon -->
                @if($transaction->status === 'draft' && $transaction->user_id === Auth::id())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="mb-3">Ajukan Transaksi</h6>
                                <p class="mb-3">Setelah yakin semua data sudah benar, klik tombol di bawah untuk mengajukan transaksi ini untuk persetujuan.</p>
                                <button type="button" class="btn btn-light" id="btnSubmitTransaction">
                                    <i class="bi bi-send"></i> Ajukan Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if($transaction->isBackwardFlow())
                    @if($transaction->status === 'disetujui_pejabat_4' || $transaction->status === 'disetujui_bersyarat')
                    <h5 class="modal-title">Teruskan ke Pejabat 2</h5>
                    @elseif($transaction->status === 'diinformasikan')
                    <h5 class="modal-title">Selesaikan & Kirim ke Pemohon</h5>
                    @endif
                @else
                    <h5 class="modal-title">Setujui Transaksi</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    @if($transaction->isBackwardFlow())
                        @if($transaction->status === 'disetujui_pejabat_4')
                        <p>Transaksi telah disetujui oleh Pejabat 4. Anda akan melampirkan bukti approval dan meneruskannya ke Pejabat 2.</p>
                        @elseif($transaction->status === 'disetujui_bersyarat')
                        <p>Transaksi telah disetujui bersyarat oleh Pejabat 4. Anda akan melampirkan bukti approval dan meneruskannya ke Pejabat 2.</p>
                        @elseif($transaction->status === 'diinformasikan')
                        <p>Anda akan menyelesaikan proses approval dan meneruskan formulir yang telah disetujui kepada Pemohon.</p>
                        @endif
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    @if($transaction->isBackwardFlow())
                    <button type="submit" class="btn btn-primary">Teruskan</button>
                    @else
                    <button type="submit" class="btn btn-success">Setujui</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Alasan Penolakan</label>
                        <textarea class="form-control" name="alasan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Request Completion Modal -->
<div class="modal fade" id="requestCompletionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Minta Kelengkapan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="requestCompletionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Catatan Kelengkapan</label>
                        <textarea class="form-control" name="catatan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conditional Approve Modal -->
<div class="modal fade" id="conditionalApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Setujui Bersyarat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="conditionalApproveForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Persetujuan bersyarat memerlukan catatan/syarat yang harus dipenuhi.
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Catatan Syarat</label>
                        <textarea class="form-control" name="catatan" rows="4" required placeholder="Tuliskan syarat atau kondisi yang harus dipenuhi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Setujui Bersyarat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const transactionId = {{ $transaction->id }};
</script>
<script src="{{ asset('js/transactions-show.js') }}"></script>
@endpush
