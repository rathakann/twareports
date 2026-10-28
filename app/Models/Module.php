<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'navigation_group_id',
        'group_label',
        'group_order',
        'label',
        'icon',
        'route',
        'url',
        'permission',
        'parent_id',
        'sort_order',
        'is_active',
        'badge',
        'target',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'group_order' => 'integer',
        'sort_order' => 'integer',
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
     * Parent navigation group relationship.
     */
    public function navigationGroup(): BelongsTo
    {
        return $this->belongsTo(NavigationGroup::class, 'navigation_group_id');
    }

    /**
     * Parent module relationship.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child sub-modules relationship.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Scope for active modules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root modules (top level items in a group).
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Convert module instance to standard navigation item array.
     *
     * @return array<string, mixed>
     */
    public function toMenuItem(): array
    {
        $item = [
            'label' => $this->label,
            'icon' => $this->icon ?: 'circle',
            'route' => $this->route,
            'url' => $this->url,
            'permission' => $this->permission,
            'target' => $this->target ?: '_self',
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->badge) {
            $item['badge'] = $this->badge;
        }

        if ($this->relationLoaded('children') && $this->children->isNotEmpty()) {
            $item['children'] = $this->children
                ->filter(fn (self $c) => (bool) $c->is_active)
                ->map(fn (self $c) => $c->toMenuItem())
                ->values()
                ->all();
        }

        return $item;
    }
}
