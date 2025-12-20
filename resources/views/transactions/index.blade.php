@extends('layouts.app')

@section('title', 'Daftar Transaksi')
@section('page-title', 'Daftar Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Transaksi</h5>
                @if(Auth::user()->isPemohon())
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactionsTable" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Pemohon</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/transactions-index.js') }}"></script>
@endpush
