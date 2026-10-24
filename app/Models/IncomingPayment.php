<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\IncomingPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingPayment extends Model
{
    /** @use HasFactory<IncomingPaymentFactory> */
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'payment_num',
        'doc_date',
        'customer_code',
        'customer_name',
        'payment_method',
        'reference_num',
        'currency',
        'amount_received',
        'bank_account',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'amount_received' => 'decimal:2',
    ];
}
