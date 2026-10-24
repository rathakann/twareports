<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\SalesPurchaseOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPurchaseOrder extends Model
{
    /** @use HasFactory<SalesPurchaseOrderFactory> */
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'doc_num',
        'customer_code',
        'customer_name',
        'customer_ref_num',
        'doc_date',
        'delivery_date',
        'currency',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'doc_total',
        'status',
        'sales_employee',
        'payment_terms',
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
