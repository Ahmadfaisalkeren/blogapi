<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Series extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'series';
    protected $fillable = [
        'title',
        'slug',
        'author',
        'series_date',
        'status',
    ];

    public function seriesParts()
    {
        return $this->hasMany(SeriesPart::class, 'series_id', 'id');
    }
}
