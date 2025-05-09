<?php

namespace App\Models;

use App\Helpers\JsonCast;
use App\Libraries\LaravelVueDatatable\LaravelVueDatatableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes, LaravelVueDatatableTrait;

    const TYPE_QUITTANCE_LOYER = 1;

    const TYPE_QUITTANCE_LOYER_NAME = 'Quittance template editor';


    protected $guarded = [];

    protected $casts = [
        'raw' => JsonCast::class,
    ];

    protected $dataTableColumns = [
        'id' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'name' => [
            'searchable' => true,
            'orderable' => true,
        ],
        'created_by' => [
            'searchable' => false,
            'orderable' => false,
        ],
        'type' => [
            'searchable' => false,
            'orderable' => false,
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return string[]
     */
    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_QUITTANCE_LOYER => self::TYPE_QUITTANCE_LOYER_NAME
        ];
    }
}
