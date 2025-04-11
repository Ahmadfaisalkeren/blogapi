<?php

namespace App\Models\Editor;

use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Header extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'headers';
    protected $fillable = [
        'content_block_id',
        'level',
        'header'
    ];

    public function contentBlock()
    {
        return $this->belongsTo(ContentBlocks::class, 'content_block_id');
    }
}
