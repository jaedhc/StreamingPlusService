<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subscription extends Model
{
    use HasFactory;

    protected $table = 'suscriptions';

    protected $fillable = [
        'subscriber_id',
        'subscribed_id',
    ];

    public function subscriber(){
        return $this->hasMany(User::class);
    }

    public function subscribed(){
        return $this->hasMany(User::class);
    }
}
