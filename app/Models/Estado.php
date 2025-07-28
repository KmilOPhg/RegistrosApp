<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    /**
     * Tabla asociada con el modelo
     *
     * @var string
     */
    protected $table = 'estados';

    protected $fillable = [
        'estado',
    ];

    public function registro() {
        return $this->hasMany(Registro::class, 'id_estado');
    }
}
