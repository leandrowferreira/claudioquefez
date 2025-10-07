<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(Draw::class);
    }

    public static function getActiveEvent(): ?Event
    {
        return self::where('start_datetime', '<=', now())
            ->where('end_datetime', '>=', now())
            ->orderBy('start_datetime', 'desc')
            ->first();
    }
}
