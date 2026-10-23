<?php

namespace App\Models;

use Database\Factories\DepositFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    /** @use HasFactory<DepositFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'deposit_num',
        'deposit_date',
        'deposit_type',
        'bank_name',
        'bank_account',
        'currency',
        'total_amount',
        'reconciliation_status',
        'status',
        'journal_ref',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deposit_date' => 'date',
        'total_amount' => 'decimal:2',
    ];
}
