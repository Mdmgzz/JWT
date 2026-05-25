<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'release_date',
        'sinopsis',     
        'duration',     
        'gendre',       
        'director_id',
    ];

    // Relación: Una película pertenece a un director
    public function director()
    {
        return $this->belongsTo(Director::class);
    }
}