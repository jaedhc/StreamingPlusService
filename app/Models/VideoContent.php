<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'url',
        'file_type_id',
    ];

    public $timestamps = false;

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }
}
