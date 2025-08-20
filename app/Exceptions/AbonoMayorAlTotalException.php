<?php

namespace App\Exceptions;

use Exception;

class AbonoMayorAlTotalException extends Exception
{
    public function __construct(float $precioTotal) {
        parent::__construct("el abono no puede ser mayor a {$precioTotal} (Precio total)");
    }
}
