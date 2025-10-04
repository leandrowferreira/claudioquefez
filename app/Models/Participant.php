<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Participant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'state',
        'codigo',
    ];

    public function draw(): HasOne
    {
        return $this->hasOne(Draw::class);
    }
}
