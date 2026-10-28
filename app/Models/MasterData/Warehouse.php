<?php

namespace App\Models\MasterData;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_warehouses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'whs_code',
        'whs_name',
        'branch_code',
        'location',
        'is_bin_act',
        'is_drop_ship',
        'is_net_whs',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_bin_act' => 'boolean',
        'is_drop_ship' => 'boolean',
        'is_net_whs' => 'boolean',
    ];
}
