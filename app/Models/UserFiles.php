<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFiles extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type'
    ];
}
