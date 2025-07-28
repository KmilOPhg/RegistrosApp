<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    /**
     * Tabla que hace referencia a este modelo
     *
     * @var string
     */
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'role_user', 'id_role', 'id_user');
    }
}
