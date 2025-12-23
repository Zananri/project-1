@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Transaksi - {{ $transaction->nomor_transaksi }}</h5>
                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($transaction->status === 'dilengkapi')
                    @php
                        $lastApproval = $transaction->approvals
                            ->where('status', 'pending')
                            ->whereNotNull('catatan')
                            ->sortByDesc('created_at')
                            ->first();
                    @endphp
                    @if($lastApproval && $lastApproval->catatan)
                    <div class="alert alert-warning d-flex align-items-start mb-4">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-1">⚠️ Permintaan Kelengkapan Data</h6>
                            <p class="mb-1"><strong>Dari:</strong> {{ $lastApproval->user->name }} ({{ ucwords($lastApproval->role) }})</p>
                            <p class="mb-0"><strong>Catatan:</strong> {{ $lastApproval->catatan }}</p>
                            <small class="text-muted">Silakan lengkapi data sesuai permintaan di atas, kemudian simpan. Transaksi akan otomatis diajukan kembali untuk persetujuan.</small>
                        </div>
                    </div>
                    @endif
                @endif

                <form id="transactionEditForm" enctype="multipart/form-data" data-status="{{ $transaction->status }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_pemohon" class="form-label required">1. Nama Pemohon</label>
                            <input type="text" class="form-control" id="nama_pemohon" name="nama_pemohon" value="{{ $transaction->nama_pemohon }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_perusahaan" class="form-label required">2. Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="{{ $transaction->nama_perusahaan }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pengajuan" class="form-label required">3. Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ $transaction->tanggal_pengajuan->format('Y-m-d') }}" required>
                        </div>

                        @if(!$transaction->hasItems())
                        <div class="col-md-6 mb-3">
                            <label for="total" class="form-label required">5. Total</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control money-format" id="total" name="total" value="{{ number_format($transaction->total, 0, ',', '.') }}" required>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($transaction->hasItems())
                    <!-- DETAILED MODE - Multiple Items -->
                    <input type="hidden" name="use_detailed_mode" value="1">
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label required">4. Detail Item Transaksi</label>
                            <div class="card">
                                <div class="card-body">
                                    <div id="itemsContainer">
                                        @foreach($transaction->items as $index => $item)
                                        <div class="item-group mb-4 border-bottom pb-3" data-index="{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Item #<span class="item-number">{{ $index + 1 }}</span></h6>
                                                @if($index > 0)
                                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required">Uraian Transaksi</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][uraian_transaksi]" value="{{ $item->uraian_transaksi }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required">Kebutuhan</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][kebutuhan]" value="{{ $item->kebutuhan }}" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required">Total</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control money-format" name="items[{{ $index }}][total]" value="{{ number_format($item->total, 0, ',', '.') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Dasar Transaksi</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][dasar_transaksi]" value="{{ $item->dasar_transaksi }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Lawan Transaksi</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][lawan_transaksi]" value="{{ $item->lawan_transaksi }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Rekening Transaksi</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][rekening_transaksi]" value="{{ $item->rekening_transaksi }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Rencana Tanggal Transaksi</label>
                                                    <input type="date" class="form-control" name="items[{{ $index }}][rencana_tanggal_transaksi]" value="{{ $item->rencana_tanggal_transaksi ? $item->rencana_tanggal_transaksi->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Pengakuan Transaksi</label>
                                                    <input type="text" class="form-control" name="items[{{ $index }}][pengakuan_transaksi]" value="{{ $item->pengakuan_transaksi }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">Keterangan Item</label>
                                                    <textarea class="form-control" name="items[{{ $index }}][keterangan_item]" rows="2">{{ $item->keterangan_item }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                                        <i class="bi bi-plus-circle"></i> Tambah Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- SIMPLE MODE - Single Item -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="uraian_transaksi" class="form-label required">4. Uraian Transaksi</label>
                            <textarea class="form-control" id="uraian_transaksi" name="uraian_transaksi" rows="4" required>{{ $transaction->uraian_transaksi }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="dasar_transaksi" class="form-label">6. Dasar Transaksi</label>
                            <textarea class="form-control" id="dasar_transaksi" name="dasar_transaksi" rows="3">{{ $transaction->dasar_transaksi }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lawan_transaksi" class="form-label">7. Lawan Transaksi</label>
                            <input type="text" class="form-control" id="lawan_transaksi" name="lawan_transaksi" value="{{ $transaction->lawan_transaksi }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rekening_transaksi" class="form-label">8. Rekening Transaksi</label>
                            <input type="text" class="form-control" id="rekening_transaksi" name="rekening_transaksi" value="{{ $transaction->rekening_transaksi }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rencana_tanggal_transaksi" class="form-label">9. Rencana Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="rencana_tanggal_transaksi" name="rencana_tanggal_transaksi" value="{{ $transaction->rencana_tanggal_transaksi ? $transaction->rencana_tanggal_transaksi->format('Y-m-d') : '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pengakuan_transaksi" class="form-label">10. Pengakuan Transaksi</label>
                            <input type="text" class="form-control" id="pengakuan_transaksi" name="pengakuan_transaksi" value="{{ $transaction->pengakuan_transaksi }}">
                        </div>
                    </div>
                    @endif

                    @if(!$transaction->hasItems())
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="keterangan" class="form-label">11. Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $transaction->keterangan }}</textarea>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="lampiran_dokumen" class="form-label">Lampiran Dokumen</label>
                            @if($transaction->lampiran_dokumen)
                            <div class="mb-2">
                                <small class="text-muted">File saat ini: 
                                    <a href="{{ Storage::url($transaction->lampiran_dokumen) }}" target="_blank">
                                        {{ basename($transaction->lampiran_dokumen) }}
                                    </a>
                                </small>
                            </div>
                            @endif
                            <input type="file" class="form-control" id="lampiran_dokumen" name="lampiran_dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <div class="form-text">Upload dokumen baru untuk mengganti. Maksimal 5MB.</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const transactionId = {{ $transaction->id }};
</script>
<script src="{{ asset('js/transactions-edit.js') }}"></script>
@endpush
