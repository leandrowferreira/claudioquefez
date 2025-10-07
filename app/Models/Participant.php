<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'state',
        'codigo',
        'event_id',
    ];

    public function draw(): HasOne
    {
        return $this->hasOne(Draw::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
