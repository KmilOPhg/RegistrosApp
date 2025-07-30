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

    //Hacer que restante aparezca en json
    protected $appends = ['restante'];

    protected $fillable = [
        'nombre',
        'descripcion',
        'valor',
        'id_estado',
    ];

    public function estado() {
        return $this->belongsTo(Estado::class, 'id_estado', 'id');
    }

    public function abonos() {
        return $this -> hasMany(Abono::class, 'id_registro', 'id');
    }

    public function getRestanteAttribute(): float {
        if ($this->id_estado === 1) {
            return 0.00;
        }
        $totalAbonos = $this->abonos()->sum('valor');
        return $this->valor - $totalAbonos;
    }
}
