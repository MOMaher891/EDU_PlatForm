<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'payment_gateways';

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'mode',
        'credentials',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mode' => 'string',
        'credentials' => 'encrypted:array',
        'sort_order' => 'integer',
    ];
}
