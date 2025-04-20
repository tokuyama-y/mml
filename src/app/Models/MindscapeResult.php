<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MindscapeResult extends Model
{
    protected $fillable = [
        'uploaded_image_url',
        'mindscape_image_url',
        'karesansui_image_url',
        'haiku',
        'timelapse_video_url',
    ];
}
