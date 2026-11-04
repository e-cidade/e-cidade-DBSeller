<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Resources\SetoresAmbulatoriaisResource;
use App\Domain\Saude\Ambulatorial\Services\SetoresAmbulatoriaisService;

class SetoresAmbulatoriaisController
{
    /**
     * @var SetoresAmbulatoriaisService
     */
    private $service;

    public function __construct(SetoresAmbulatoriaisService $service)
    {
        $this->service = $service;
    }

    public function getSetores()
    {
        $dados = $this->service->recuperaSetores();
        $setores = SetoresAmbulatoriaisResource::toArray($dados);
        return new DBJsonResponse($setores);
    }
}
