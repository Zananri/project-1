<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transaction Routes
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions-pending', [TransactionController::class, 'pending'])->name('transactions.pending');
    
    // AJAX Routes for Transactions
    Route::post('/transactions/get-data', [TransactionController::class, 'getData'])->name('transactions.getData');
    Route::post('/transactions-pending/get-data', [TransactionController::class, 'getPendingData'])->name('transactions.getPendingData');
    Route::get('/transactions/{id}/detail', [TransactionController::class, 'getDetail'])->name('transactions.getDetail');
    Route::get('/transactions/{transaction}/download-excel', [TransactionController::class, 'downloadExcel'])->name('transactions.downloadExcel');
    Route::get('/transactions/{transaction}/download-all', [TransactionController::class, 'downloadAll'])->name('transactions.downloadAll');
    Route::post('/transactions/{transaction}/submit', [TransactionController::class, 'submit'])->name('transactions.submit');
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    Route::post('/transactions/{transaction}/request-completion', [TransactionController::class, 'requestCompletion'])->name('transactions.requestCompletion');
    Route::post('/transactions/{transaction}/conditional-approve', [TransactionController::class, 'conditionalApprove'])->name('transactions.conditionalApprove');
});

require __DIR__.'/auth.php';
