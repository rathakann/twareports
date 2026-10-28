<?php

namespace App\Models\Sap;

use Illuminate\Database\Eloquent\Model;
use LogicException;

abstract class HanaModel extends Model
{
    protected $connection = 'saphana';

    public $timestamps = false;

    public static function hanaTable(string $table): string
    {
        $schema = config('database.connections.saphana.database');

        return $schema.'.'.$table;
    }

    /**
     * Get the model's fully-qualified table name.
     */
    public function getTable()
    {
        return static::hanaTable($this->table);
    }

    /**
     * Prevent standard Eloquent model write operations.
     */
    protected static function booted(): void
    {
        static::saving(function (Model $model): void {
            throw new LogicException(
                $model::class.' is read-only.'
            );
        });

        static::deleting(function (Model $model): void {
            throw new LogicException(
                $model::class.' is read-only.'
            );
        });
    }
}
