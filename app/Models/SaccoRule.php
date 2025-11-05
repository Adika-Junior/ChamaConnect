<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaccoRule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public static function allSlugs(): array
    {
        return static::query()->orderBy('name')->pluck('slug')->all();
    }
}


