<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlowEntry extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_flow_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reference_num',
        'doc_date',
        'activity_type',
        'flow_type',
        'description',
        'account_name',
        'inflow_amount',
        'outflow_amount',
        'net_amount',
        'payment_method',
        'currency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'inflow_amount' => 'decimal:2',
        'outflow_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];
}
