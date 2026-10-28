<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationGroup extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'navigation_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'label',
        'icon',
        'sort_order',
        'is_active',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Retrieve the model for a bound value (supports both numeric ID and UUID).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * All modules belonging to this navigation group.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'navigation_group_id')->orderBy('sort_order');
    }

    /**
     * Root top-level modules belonging to this navigation group.
     */
    public function rootModules(): HasMany
    {
        return $this->hasMany(Module::class, 'navigation_group_id')
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    /**
     * Scope for active navigation groups.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
