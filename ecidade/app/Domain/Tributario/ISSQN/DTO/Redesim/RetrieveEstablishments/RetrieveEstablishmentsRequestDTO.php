<?php

namespace App\Domain\Tributario\ISSQN\DTO\Redesim\RetrieveEstablishments;

use App\Domain\Tributario\ISSQN\DTO\Redesim\BaseRequestDTO;

class RetrieveEstablishmentsRequestDTO extends BaseRequestDTO
{
    public $maximoRegistros;

    public $versao;

    public function __construct($accessKeyId, $secretAccessKey, $maxItems, $version)
    {
        parent::__construct($accessKeyId, $secretAccessKey);

        $this->maximoRegistros = $maxItems;
        $this->versao = $version;
    }
}
