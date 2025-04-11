<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Hero extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'heroes';
    protected $fillable = [
        'title',
        'image',
    ];
}
