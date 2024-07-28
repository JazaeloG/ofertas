<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paso extends Model
{
    use HasFactory;

    protected $fillable = [
        'oferta_id',
        'orden',
        'descripcion',
    ];

    public function oferta()
    {
        return $this->belongsTo(Oferta::class);
    }
}