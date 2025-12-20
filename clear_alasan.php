<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$transaction = App\Models\Transaction::find(1);
$transaction->update(['alasan_penolakan' => null]);

echo "Alasan penolakan berhasil dihapus untuk transaksi ID 1\n";
echo "Status: " . $transaction->status . "\n";
