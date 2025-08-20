<?php

namespace App\Exceptions;

use Exception;

class AbonoNegativoException extends Exception
{
    public function __construct() {
        parent::__construct("el abono no puede ser negativo");
    }
}
