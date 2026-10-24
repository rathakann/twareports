<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceSheetItem extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'balance_sheet_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'account_code',
        'account_name',
        'category',
        'as_of_date',
        'amount',
        'previous_amount',
        'currency',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'as_of_date' => 'date',
        'amount' => 'decimal:2',
        'previous_amount' => 'decimal:2',
    ];
}
