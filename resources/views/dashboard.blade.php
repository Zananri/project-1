@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header mb-4">
            <h2>Dashboard</h2>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }} ({{ Auth::user()->role_name }})</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-muted">Total Transaksi</h6>
                        <h3 class="mb-0">{{ $stats['total_transactions'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-muted">Menunggu Approval</h6>
                        <h3 class="mb-0">{{ $stats['pending_approval'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-muted">Disetujui</h6>
                        <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-muted">Ditolak</h6>
                        <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Transaksi Terbaru</h5>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @if($recentTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Pemohon</th>
                                <th>Perusahaan</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                            <tr>
                                <td><strong>{{ $transaction->nomor_transaksi }}</strong></td>
                                <td>{{ $transaction->nama_pemohon }}</td>
                                <td>{{ $transaction->nama_perusahaan }}</td>
                                <td>{{ $transaction->tanggal_pengajuan->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClass = match($transaction->status) {
                                            'draft' => 'secondary',
                                            'ditolak' => 'danger',
                                            'selesai' => 'success',
                                            default => 'warning'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $transaction->status)) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada transaksi</p>
                    @if(Auth::user()->isPemohon())
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->isPemohon())
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">Butuh mengajukan transaksi baru?</h5>
                        <p class="mb-0">Klik tombol di samping untuk membuat pengajuan transaksi resmi perusahaan.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('transactions.create') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-plus-circle"></i> Buat Pengajuan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
