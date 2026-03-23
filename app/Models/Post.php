<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    // Automatic generate UUID
    use HasFactory, HasUuids, SoftDeletes;

    const CACHE_KEY_RECOMMENDED = "posts_recommended";

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'content',
        'image_path',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    // References
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
