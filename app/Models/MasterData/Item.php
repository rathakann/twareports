<?php

namespace App\Models\MasterData;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'item_code',
        'item_name',
        'foreign_name',
        'itms_grp_cod',
        'itms_grp_nam',
        'item_class',
        'item_type',
        'uom_code',
        'ugp_code',
        'barcode',
        'is_inventory_item',
        'is_sales_item',
        'is_purchase_item',
        'is_asset_item',
        'on_hand',
        'is_committed',
        'on_order',
        'cost_price',
        'sales_price',
        'default_whs',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_inventory_item' => 'boolean',
        'is_sales_item' => 'boolean',
        'is_purchase_item' => 'boolean',
        'is_asset_item' => 'boolean',
        'on_hand' => 'decimal:2',
        'is_committed' => 'decimal:2',
        'on_order' => 'decimal:2',
        'cost_price' => 'decimal:4',
        'sales_price' => 'decimal:4',
    ];
}
