<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLossItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profit_loss_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_code',
        'account_name',
        'category',
        'doc_date',
        'amount',
        'previous_period_amount',
        'budget_amount',
        'currency',
        'department',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'amount' => 'decimal:2',
        'previous_period_amount' => 'decimal:2',
        'budget_amount' => 'decimal:2',
    ];
}
