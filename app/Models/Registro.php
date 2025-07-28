<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    /**
     * Tabla que hace referncias a este modelo
     *
     * @var string
     */
    protected $table = 'registros';

    protected $fillable = [
        'nombre',
        'descripcion',
        'valor',
        'abono',
        'id_estado',
    ];

    public function estado() {
        return $this->belongsTo(Estado::class, 'id_estado', 'id');
    }
}
