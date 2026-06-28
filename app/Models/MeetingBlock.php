<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'starts_at',
    'ends_at',
    'is_full_day',
    'notes',
])]
class MeetingBlock extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_full_day' => 'boolean',
        ];
    }
}
