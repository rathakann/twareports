<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\PurchaseOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    /** @use HasFactory<PurchaseOrderFactory> */
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'doc_num',
        'vendor_code',
        'vendor_name',
        'doc_date',
        'delivery_date',
        'currency',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'doc_total',
        'status',
        'payment_terms',
        'buyer_name',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'doc_total' => 'decimal:2',
    ];
}
