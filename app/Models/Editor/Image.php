<?php

namespace App\Models\Editor;

use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'images';
    protected $fillable = ['content_block_id', 'parent_type', 'image_url', 'type', 'caption'];

    public function contentBlock()
    {
        return $this->belongsTo(ContentBlocks::class, 'content_block_id');
    }
}
