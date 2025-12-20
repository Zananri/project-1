@extends('layouts.app')

@section('title', 'Buat Pengajuan Transaksi')
@section('page-title', 'Buat Pengajuan Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Approval Transaksi Resmi Perusahaan</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Mode Selector -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-primary">
                            <h6 class="alert-heading mb-3">Pilih Mode Pengisian:</h6>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="entry_mode" id="modeSimple" value="simple" checked>
                                <label class="form-check-label" for="modeSimple">
                                    <strong>Mode Sederhana</strong> - Untuk 1 transaksi
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="entry_mode" id="modeDetailed" value="detailed">
                                <label class="form-check-label" for="modeDetailed">
                                    <strong>Mode Detail</strong> - Untuk multiple transaksi (seperti format Excel)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="transactionForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="use_detailed_mode" id="use_detailed_mode" value="0">
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Ketentuan Pengisian Form:</h6>
                                <ol class="mb-0">
                                    <li>Setiap kolom dalam Form Approval Transaksi Resmi Perusahaan harus diisi sesuai ketentuan yang berlaku.</li>
                                    <li>Formulir Pengajuan diajukan setiap hari Senin dan Kamis sebelum pukul 12.00 WIB.</li>
                                    <li>Pengajuan yang dilakukan setelah batas waktu akan diproses pada hari kerja di pengajuan berikutnya.</li>
                                    <li>Pihak yang mengajukan bertanggung jawab atas kebenaran dan ketepatan data yang disampaikan.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_pemohon" class="form-label required">1. Nama Pemohon</label>
                            <input type="text" class="form-control" id="nama_pemohon" name="nama_pemohon" value="{{ Auth::user()->name }}" required>
                            <div class="form-text">Diisi dengan nama pemohon yang mengajukan approval transaksi resmi.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_perusahaan" class="form-label required">2. Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                            <div class="form-text">Diisi dengan nama perusahaan pemohon yang mengajukan approval transaksi resmi.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pengajuan" class="form-label required">3. Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}" required>
                            <div class="form-text">Diisi dengan tanggal pengumpulan form approval kepada Business Executive.</div>
                        </div>

                        <div class="col-md-6 mb-3" id="simpleTotalField">
                            <label for="total" class="form-label required">4. Total</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control money-format" id="total" name="total" required>
                            </div>
                            <div class="form-text">Diisi dengan nilai nominal rupiah transaksi yang diajukan.</div>
                        </div>
                    </div>

                    <!-- Simple Mode Fields -->
                    <div id="simpleFields">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="uraian_transaksi" class="form-label required">5. Uraian Transaksi</label>
                                <textarea class="form-control" id="uraian_transaksi" name="uraian_transaksi" rows="4" required></textarea>
                                <div class="form-text">Diisi dengan tujuan transaksi yang diajukan. Transaksi dikelompokkan sesuai dengan jenisnya masing-masing.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="dasar_transaksi" class="form-label">6. Dasar Transaksi</label>
                                <textarea class="form-control" id="dasar_transaksi" name="dasar_transaksi" rows="3"></textarea>
                                <div class="form-text">Diisi dengan dasar transaksi yang menyatakan tujuan, nilai, dan lawan transaksi. Dapat berupa dokumen atau non-dokumen.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lawan_transaksi" class="form-label">7. Lawan Transaksi</label>
                                <input type="text" class="form-control" id="lawan_transaksi" name="lawan_transaksi">
                                <div class="form-text">Diisi dengan nama lawan transaksi yang melakukan penyerahan barang/jasa atau yang menerima pembayaran atas tujuan tertentu selain penyerahan barang/jasa.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rekening_transaksi" class="form-label">8. Rekening Transaksi</label>
                                <input type="text" class="form-control" id="rekening_transaksi" name="rekening_transaksi">
                                <div class="form-text">Diisi dengan rekening yang menjadi tujuan transfer. Bisa rekening lawan transaksi langsung atau yang menjadi perantara.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rencana_tanggal_transaksi" class="form-label">9. Rencana Tanggal Transaksi</label>
                                <input type="date" class="form-control" id="rencana_tanggal_transaksi" name="rencana_tanggal_transaksi">
                                <div class="form-text">Diisi dengan rencana tanggal transaksi akan dieksekusi.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pengakuan_transaksi" class="form-label">10. Pengakuan Transaksi</label>
                                <input type="text" class="form-control" id="pengakuan_transaksi" name="pengakuan_transaksi">
                                <div class="form-text">Diisi dengan pengakuan akun pembiayaan atau akun tertentu sesuai tujuan transaksi.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Mode Fields -->
                    <div id="detailedFields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label required">4. Detail Transaksi</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="newUraian" placeholder="Uraian Transaksi (contoh: Biaya Produksi/Cost Revenue)">
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="newKebutuhan" placeholder="Kebutuhan (contoh: [Kebutuhan A])">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control money-format" id="newNominal" placeholder="Nominal">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <textarea class="form-control" id="newDasar" rows="2" placeholder="Dasar Transaksi"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="newLawan" placeholder="Lawan Transaksi">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="newRekening" placeholder="Rekening Transaksi">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <input type="date" class="form-control" id="newTanggal" placeholder="Rencana Tanggal Transaksi">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="newPengakuan" placeholder="Pengakuan Transaksi">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <textarea class="form-control" id="newKeterangan" rows="2" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="text-end">
                                            <button type="button" class="btn btn-primary" id="btnAddItem">
                                                <i class="bi bi-plus-circle"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text mt-2">Isi Uraian Transaksi untuk kategori utama, lalu isi detail kebutuhan dan field lainnya. Uraian Transaksi yang sama akan dikelompokkan.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="itemsTable">
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
                                                <th width="6%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary">
                                                <th colspan="2" class="text-end">TOTAL:</th>
                                                <th><span id="totalSum">Rp 0</span></th>
                                                <th colspan="7"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="generalKeterangan">
                        <div class="col-12 mb-3">
                            <label for="keterangan" class="form-label">11. Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                            <div class="form-text">Wajib diisi dengan keterangan tertentu seperti tujuan transaksi atau hal lain yang diperlukan.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="lampiran_dokumen" class="form-label">Lampiran Dokumen</label>
                            <input type="file" class="form-control" id="lampiran_dokumen" name="lampiran_dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <div class="form-text">Upload dokumen pendukung (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG). Maksimal 5MB.</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btnSaveDraft">
                                    <i class="bi bi-save"></i> Simpan sebagai Draft
                                </button>
                                <button type="button" class="btn btn-success" id="btnSubmit">
                                    <i class="bi bi-send"></i> Simpan dan Ajukan
                                </button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
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
<script src="{{ asset('js/transactions-create.js') }}"></script>
@endpush
