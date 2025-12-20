@extends('layouts.app')

@section('title', 'Perlu Persetujuan')
@section('page-title', 'Perlu Persetujuan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi yang Memerlukan Persetujuan Anda</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pendingTransactionsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>NO. TRANSAKSI</th>
                                <th>PEMOHON</th>
                                <th class="text-center">TANGGAL PENGAJUAN</th>
                                <th class="text-end">TOTAL</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/transactions-pending.js') }}"></script>
@endpush
