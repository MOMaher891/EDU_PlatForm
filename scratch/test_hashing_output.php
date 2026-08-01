<?php

require 'c:/laragon/www/EDU_PlatForm/vendor/autoload.php';
$app = require_once 'c:/laragon/www/EDU_PlatForm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Payment\Drivers\KashierDriver;

$driver = new KashierDriver();
echo "Merchant ID loaded: " . $driver->getMerchantId() . "\n";
echo "Secret Key loaded length: " . strlen($driver->getSecretKey()) . "\n";
echo "Secret Key loaded preview: " . substr($driver->getSecretKey(), 0, 32) . "...\n";

// From your latest screenshot parameters:
$orderId = "ORD-4Y39N3BE8I"; // or from latest request
$amount = "30.00";
$currency = "EGP";

$hash = $driver->generateHash($orderId, $amount, $currency);
echo "Generated Hash: " . $hash . "\n";
echo "Path hashed: /?payment=" . $driver->getMerchantId() . "." . $orderId . "." . $amount . "." . $currency . "\n";
