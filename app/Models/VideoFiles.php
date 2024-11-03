<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoFiles extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type'
    ];
}
