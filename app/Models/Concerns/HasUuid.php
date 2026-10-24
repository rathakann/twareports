<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the HasUuid trait for the model.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retrieve the model for a bound value.
     * Strictly verifies that the value is a valid UUID, returning 404 immediately
     * if a numeric ID or invalid string is provided. This prevents enumeration
     * and ID guessing attacks.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'uuid' && ! Str::isUuid($value)) {
            abort(404, 'Resource not found.');
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'uuid' && ! Str::isUuid($value)) {
            abort(404, 'Resource not found.');
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }
}
