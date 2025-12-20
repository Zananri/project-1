<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fix transaction status
$transaction = App\Models\Transaction::find(1);

echo "Before:\n";
echo "Status: " . $transaction->status . "\n";

// Pejabat 2 sudah approved, jadi status harus menunggu_pejabat_3
$transaction->update(['status' => 'menunggu_pejabat_3']);

echo "\nAfter:\n";
echo "Status: " . $transaction->status . "\n";
echo "Current approval step: " . $transaction->getCurrentApprovalStep() . "\n";
echo "Can be approved by pejabat_3: " . ($transaction->canBeApprovedBy('pejabat_3') ? 'YES' : 'NO') . "\n";
