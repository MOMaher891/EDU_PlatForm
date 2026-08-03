<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Check Transactions Table ---\n";
try {
    $transactions = \DB::table('transactions')->select('status', \DB::raw('count(*) as count'), \DB::raw('sum(amount) as total'))->groupBy('status')->get();
    foreach ($transactions as $t) {
        echo "Status: {$t->status} | Count: {$t->count} | Total: {$t->total}\n";
    }
} catch (\Exception $e) {
    echo "Error checking transactions: " . $e->getMessage() . "\n";
}

echo "--- Check Orders Table ---\n";
try {
    $orders = \DB::table('orders')->select('status', \DB::raw('count(*) as count'), \DB::raw('sum(total_amount) as total'))->groupBy('status')->get();
    foreach ($orders as $o) {
        echo "Status: {$o->status} | Count: {$o->count} | Total: {$o->total}\n";
    }
} catch (\Exception $e) {
    echo "Error checking orders: " . $e->getMessage() . "\n";
}

echo "--- Check Lesson Payments Table ---\n";
try {
    $lessonPayments = \DB::table('lesson_payments')->select('status', \DB::raw('count(*) as count'), \DB::raw('sum(amount) as total'))->groupBy('status')->get();
    foreach ($lessonPayments as $lp) {
        echo "Status: {$lp->status} | Count: {$lp->count} | Total: {$lp->total}\n";
    }
} catch (\Exception $e) {
    echo "Error checking lesson_payments: " . $e->getMessage() . "\n";
}
