<?php

require __DIR__.'/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configure Midtrans
\Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
\Midtrans\Config::$isProduction = $_ENV['MIDTRANS_IS_PRODUCTION'] === 'true';
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

echo "Testing Midtrans Configuration:\n";
echo "Server Key: " . substr($_ENV['MIDTRANS_SERVER_KEY'], 0, 20) . "...\n";
echo "Is Production: " . ($_ENV['MIDTRANS_IS_PRODUCTION'] === 'true' ? 'YES' : 'NO') . "\n";
echo "\n";

try {
    $params = [
        'payment_type' => 'bank_transfer',
        'transaction_details' => [
            'order_id' => 'TEST-' . time(),
            'gross_amount' => 10000,
        ],
        'customer_details' => [
            'first_name' => 'Test',
            'email' => 'test@example.com',
        ],
        'bank_transfer' => [
            'bank' => 'bca',
        ],
    ];

    echo "Creating test charge...\n";
    $charge = \Midtrans\CoreApi::charge($params);

    echo "SUCCESS!\n";
    echo "VA Number: " . $charge->va_numbers[0]->va_number . "\n";
    echo "Bank: " . $charge->va_numbers[0]->bank . "\n";
    echo "Transaction ID: " . $charge->transaction_id . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
