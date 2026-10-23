<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrialBalanceAccount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trial_balance_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_code',
        'account_name',
        'classification',
        'statement_type',
        'doc_date',
        'beginning_balance',
        'debit_amount',
        'credit_amount',
        'ending_balance',
        'currency',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'beginning_balance' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'ending_balance' => 'decimal:2',
    ];
}
