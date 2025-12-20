<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'nama_pemohon',
        'nama_perusahaan',
        'tanggal_pengajuan',
        'uraian_transaksi',
        'total',
        'dasar_transaksi',
        'lawan_transaksi',
        'rekening_transaksi',
        'rencana_tanggal_transaksi',
        'pengakuan_transaksi',
        'keterangan',
        'status',
        'alasan_penolakan',
        'tanggal_disetujui',
        'tanggal_ditolak',
        'lampiran_dokumen',
        'excel_path',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'rencana_tanggal_transaksi' => 'date',
        'tanggal_disetujui' => 'datetime',
        'tanggal_ditolak' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(TransactionApproval::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class)->orderBy('urutan');
    }

    public function hasItems()
    {
        return $this->items()->count() > 0;
    }

    public function getTotalFromItems()
    {
        return $this->items()->sum('total');
    }

    public function getApprovalByRole($role)
    {
        return $this->approvals()->where('role', $role)->first();
    }

    public function getCurrentApprovalStep()
    {
        $statusMap = [
            'diskusi_pra_permohonan' => 'pejabat_1',
            'pemeriksaan_tahap_1' => 'pejabat_2',
            'pemeriksaan_tahap_2' => 'pejabat_3',
            'menunggu_pejabat_4' => 'pejabat_4',
            'dilengkapi' => 'pejabat_2',
            'disetujui_pejabat_4' => 'pejabat_3', // Backward: P3 menerima dari P4
            'diinformasikan' => 'pejabat_2', // Backward: P2 menerima dari P3
        ];

        return $statusMap[$this->status] ?? null;
    }

    public function canBeApprovedBy($role)
    {
        // Check if current step matches the role
        if ($this->getCurrentApprovalStep() !== $role) {
            return false;
        }

        // For backward flow statuses, allow action without checking approval history
        if (in_array($this->status, ['disetujui_pejabat_4', 'diinformasikan'])) {
            return true;
        }

        // Check if user has already approved this transaction
        $existingApproval = $this->approvals()
            ->where('role', $role)
            ->whereIn('status', ['approved', 'rejected'])
            ->exists();

        // Can only approve if haven't approved/rejected yet
        return !$existingApproval;
    }

    public function isBackwardFlow()
    {
        return in_array($this->status, ['disetujui_pejabat_4', 'diinformasikan']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->nomor_transaksi) {
                $transaction->nomor_transaksi = self::generateNomorTransaksi();
            }
        });
    }

    public static function generateNomorTransaksi()
    {
        $year = date('Y');
        $month = date('m');
        $lastTransaction = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction ? intval(substr($lastTransaction->nomor_transaksi, 0, 2)) + 1 : 1;
        
        return sprintf('%02d.%s/%s', $sequence, $month, $year);
    }
}
