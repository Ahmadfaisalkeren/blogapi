<?php

namespace App\Models\Editor;

use App\Models\Post;
use App\Models\Editor\Code;
use App\Models\Editor\Image;
use App\Models\Editor\Header;
use App\Models\Editor\ListItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentBlocks extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'content_blocks';
    protected $fillable = ['parent_id', 'parent_type', 'type', 'order'];

    public function parent()
    {
        return $this->morphTo();
    }

    public function paragraphs()
    {
        return $this->hasMany(Paragraph::class, 'content_block_id');
    }
    public function headers()
    {
        return $this->hasMany(Header::class, 'content_block_id');
    }

    public function codes()
    {
        return $this->hasOne(Code::class, 'content_block_id');
    }

    public function images()
    {
        return $this->hasOne(Image::class, 'content_block_id');
    }

    public function listItems()
    {
        return $this->hasMany(ListItem::class, 'content_block_id');
    }
}
