<?php

namespace App\Models\Editor;

use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Code extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'codes';
    protected $fillable = [
        'content_block_id',
        'code'
    ];

    public function contentBlock()
    {
        return $this->belongsTo(ContentBlocks::class, 'content_block_id');
    }
}
