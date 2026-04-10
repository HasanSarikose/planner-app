<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['user_id', 'text', 'priority', 'done'];

    protected $casts = ['done' => 'boolean'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
