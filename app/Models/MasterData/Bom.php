<?php

namespace App\Models\MasterData;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_boms';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'tree_code',
        'tree_name',
        'tree_type',
        'output_qty',
        'uom_code',
        'whs_code',
        'components_count',
        'total_cost',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'output_qty' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'components_count' => 'integer',
    ];
}
