<?php

namespace App\Models\Sap;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Branch extends HanaModel
{
    protected $table = 'OBPL';

    protected $primaryKey = 'BPLId';

    public $incrementing = false;

    protected $keyType = 'int';

    public function groupTable(): string
    {
        return static::hanaTable($this->table);
    }

    protected function updateDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                return Carbon::parse($value)->format('M j, Y');
            },
        );
    }
    // protected function status(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn() => $this->Disabled === 'N' ? 'Active' : 'Inactive',
    //     );
    // }
}
