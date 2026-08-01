<?php

require 'c:/laragon/www/EDU_PlatForm/vendor/autoload.php';
$app = require_once 'c:/laragon/www/EDU_PlatForm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$g = App\Models\PaymentGateway::where('code', 'kashier')->first();

if ($g) {
    echo "Kashier name in DB: " . $g->name . "\n";
    echo "Kashier active in DB: " . ($g->is_active ? 'YES' : 'NO') . "\n";
    echo "Kashier mode in DB: " . $g->mode . "\n";
    echo "Kashier credentials in DB:\n";
    print_r($g->credentials);
} else {
    echo "No Kashier gateway found in DB!\n";
}
