<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confession extends Model
{
    protected $fillable = [
        'message',
        'category',
        'emotion'
    ];
}
