<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pais extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'link',
        'oferta_id',
    ];

    public function oferta()
    {
        return $this->belongsTo(Oferta::class);
    }
}
