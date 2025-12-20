<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_transactions' => 0,
            'pending_approval' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        if ($user->isPemohon()) {
            $stats['total_transactions'] = Transaction::where('user_id', $user->id)->count();
            $stats['pending_approval'] = Transaction::where('user_id', $user->id)
                ->whereNotIn('status', ['draft', 'selesai', 'ditolak'])->count();
            $stats['approved'] = Transaction::where('user_id', $user->id)
                ->where('status', 'selesai')->count();
            $stats['rejected'] = Transaction::where('user_id', $user->id)
                ->where('status', 'ditolak')->count();
        } elseif ($user->isPejabat()) {
            $stats['total_transactions'] = Transaction::whereHas('approvals', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            $stats['pending_approval'] = Transaction::where('status', 'like', '%menunggu%')
                ->orWhere('status', 'like', '%diskusi%')
                ->orWhere('status', 'like', '%pemeriksaan%')
                ->count();

            $stats['approved'] = Transaction::where('status', 'selesai')->count();
            $stats['rejected'] = Transaction::where('status', 'ditolak')->count();
        }

        // Get recent transactions
        $recentTransactions = Transaction::with(['user'])
            ->when($user->isPemohon(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentTransactions'));
    }
}
