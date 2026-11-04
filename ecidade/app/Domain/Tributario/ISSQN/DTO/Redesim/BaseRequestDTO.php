<?php

namespace App\Domain\Tributario\ISSQN\DTO\Redesim;

class BaseRequestDTO
{
    public $accessKeyId;

    public $secretAccessKey;

    public function __construct($accessKeyId, $secretAccessKey)
    {
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
    }
}
