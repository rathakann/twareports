<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\ApInvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApInvoice extends Model
{
    /** @use HasFactory<ApInvoiceFactory> */
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'invoice_num',
        'po_reference',
        'vendor_code',
        'vendor_name',
        'doc_date',
        'due_date',
        'currency',
        'subtotal',
        'tax_amount',
        'doc_total',
        'paid_amount',
        'balance_due',
        'status',
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
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'doc_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];
}
