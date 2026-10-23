<?php

namespace App\Models;

use Database\Factories\OutgoingPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingPayment extends Model
{
    /** @use HasFactory<OutgoingPaymentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'payment_num',
        'doc_date',
        'vendor_code',
        'vendor_name',
        'payment_method',
        'reference_num',
        'currency',
        'amount_paid',
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
        'amount_paid' => 'decimal:2',
    ];
}
