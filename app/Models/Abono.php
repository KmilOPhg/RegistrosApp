<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $table = 'abonos';

    protected  $fillable = [
        'id_registro',
        'valor',
    ];

    public function registro() {
        return $this->belongsTo(Registro::class, 'id_registro', 'id');
    }
}
