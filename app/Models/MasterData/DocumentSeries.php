<?php

namespace App\Models\MasterData;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSeries extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_document_series';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'object_code',
        'doc_name',
        'series_code',
        'series_name',
        'prefix',
        'suffix',
        'initial_num',
        'next_num',
        'last_num',
        'period_indicator',
        'branch_code',
        'is_locked',
        'is_manual',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'initial_num' => 'integer',
        'next_num' => 'integer',
        'last_num' => 'integer',
        'is_locked' => 'boolean',
        'is_manual' => 'boolean',
    ];
}
