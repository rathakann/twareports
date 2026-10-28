<?php

namespace App\Models\Sap;

class BusinessPartner extends HanaModel
{
    protected $table = 'OCRD';

    protected $primaryKey = 'DocEntry';

    public $incrementing = true;

    public function groupTable(): string
    {
        return env('HANA_DATABASE').'.OCRD';
    }
}
