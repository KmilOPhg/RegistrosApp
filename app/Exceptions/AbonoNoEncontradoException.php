<?php

namespace App\Exceptions;

use Exception;

class AbonoNoEncontradoException extends Exception
{
    public function __construct() {
        parent::__construct("Abono no encontrado");
    }
}
