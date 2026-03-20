<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class System extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'systems';
    protected $guarded = [];

    // Since we don't want to create a real table if we can avoid it, 
    // maybe we can reuse an existing table or create a migration for this singleton.
    // Actually, "System" settings usually don't need a row if we only attach media.
    // But Media Library relationships need a valid model ID and class.
    // I will create a migration for a simple 'systems' table with just an id.
}
