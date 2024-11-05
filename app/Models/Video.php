<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    // Si quieres especificar las columnas que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'duration',
    ];

    // Definir las relaciones, si corresponde
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contents(){
        return $this->hasMany(VideoContent::class);
    }
}
