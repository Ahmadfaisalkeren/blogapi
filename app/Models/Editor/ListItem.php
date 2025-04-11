<?php

namespace App\Models\Editor;

use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'list_items';
    protected $fillable = ['content_block_id', 'list', 'style', 'meta', 'order'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function contentBlock()
    {
        return $this->belongsTo(ContentBlocks::class);
    }
}
