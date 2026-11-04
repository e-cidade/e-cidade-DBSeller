<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Exceptions;

class VersionNotFoundException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
