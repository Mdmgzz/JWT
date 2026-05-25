<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Hidden(['updated_at','created_at'])]
#[Fillable(['name', 'surname', 'biography', 'birthdate'])] 
class Director extends Model
{
    //Usamos las factorias de laravel
    use HasFactory;
    public function films(): HasMany
    {
        return $this->hasMany(Film::class);
    }
}