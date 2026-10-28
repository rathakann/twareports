<?php

namespace App\Models\Sap;

class Item extends HanaModel
{
    protected $table = 'OITM';

    protected $primaryKey = 'ItemCode';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get the fully-qualified SAP HANA table name.
     */
    public function groupTable(): string
    {
        return static::hanaTable($this->table);
    }
}
