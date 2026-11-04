<?php

namespace App\Domain\Tributario\ISSQN\DTO\Redesim\ConfirmReceipt;

use App\Domain\Tributario\ISSQN\DTO\Redesim\BaseRequestDTO;

class ConfirmReceiptRequestDTO extends BaseRequestDTO
{
    public $identificador;

    public function __construct($accessKeyId, $secretAccessKey, $identifiers)
    {
        parent::__construct($accessKeyId, $secretAccessKey);

        $this->identificador = $identifiers;
    }
}
