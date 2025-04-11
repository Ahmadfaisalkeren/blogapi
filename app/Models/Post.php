<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'posts';
    protected $fillable = [
        'title',
        'slug',
        'author',
        'post_date',
        'status',
    ];

    public function contentBlocks()
    {
        return $this->hasMany(ContentBlocks::class, 'parent_id')
            ->where('parent_type', Post::class);
    }
}
