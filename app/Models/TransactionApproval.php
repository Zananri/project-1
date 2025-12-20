<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'role',
        'status',
        'catatan',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleNameAttribute()
    {
        $roles = [
            'pejabat_1' => 'Pejabat 1',
            'pejabat_2' => 'Pejabat 2',
            'pejabat_3' => 'Pejabat 3',
            'pejabat_4' => 'Pejabat 4',
        ];

        return $roles[$this->role] ?? $this->role;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'info',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
