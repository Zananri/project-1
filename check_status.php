<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$transaction = App\Models\Transaction::find(1);

echo "Transaction ID: 1\n";
echo "Nomor: " . $transaction->nomor_transaksi . "\n";
echo "Status: " . $transaction->status . "\n";
echo "Current approval step: " . $transaction->getCurrentApprovalStep() . "\n";
echo "Can be approved by pejabat_3: " . ($transaction->canBeApprovedBy('pejabat_3') ? 'YES' : 'NO') . "\n";
echo "\nApprovals:\n";
foreach ($transaction->approvals as $approval) {
    echo "  - Role: " . $approval->role . ", Status: " . $approval->status . ", User: " . $approval->user->name . "\n";
}
