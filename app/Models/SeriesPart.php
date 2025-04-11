<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Editor\ContentBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeriesPart extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'series_parts';
    protected $fillable = [
        'series_id',
        'part_number',
        'title',
    ];

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }

    public function contentBlocks()
    {
        return $this->hasMany(ContentBlocks::class, 'parent_id')
            ->where('parent_type', SeriesPart::class);
    }
}
