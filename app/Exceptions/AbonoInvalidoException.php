<?php

namespace App\Exceptions;

use Exception;

class AbonoInvalidoException extends Exception
{
    public function __construct(float $precioTotal) {
        parent::__construct("el abono no puede ser mayor a {$precioTotal}");
    }
}
