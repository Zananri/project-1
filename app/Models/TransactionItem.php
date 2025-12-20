<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'uraian_transaksi',
        'kebutuhan',
        'total',
        'dasar_transaksi',
        'lawan_transaksi',
        'rekening_transaksi',
        'rencana_tanggal_transaksi',
        'pengakuan_transaksi',
        'keterangan',
        'urutan',
        'parent_urutan',
    ];

    protected $casts = [
        'rencana_tanggal_transaksi' => 'date',
        'total' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
