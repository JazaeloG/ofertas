<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'titulo',
        'descripcion',
        'logo',
        'link',
    ];

    public function pasos()
    {
        return $this->hasMany(Paso::class)->orderBy('orden');
    }

    public function paises()
    {
        return $this->hasMany(Pais::class);
    }
}