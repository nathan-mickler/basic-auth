<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetModel extends Model
{
    protected $fillable = [
        'name',
        'color',
        'size',
        'count',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'count' => 'integer',
            'active' => 'boolean',
        ];
    }

    protected $table = 'widgets';
}
