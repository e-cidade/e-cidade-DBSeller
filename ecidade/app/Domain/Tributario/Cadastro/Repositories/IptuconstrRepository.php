<?php

namespace App\Domain\Tributario\Cadastro\Repositories;

use App\Domain\Tributario\Cadastro\Models\Iptuconstr;

class IptuconstrRepository
{
    private $iptuconstr;

    public function __construct()
    {
        $this->iptuconstr = new Iptuconstr();
    }

    public function getByMatric($matricula, $campos = ["*"])
    {
        return $this->iptuconstr->where(
            "j39_matric",
            "=",
            $matricula
        )->get($campos);
    }

    public function getByMatricIdCons($matricula, $idCons, $campos = ["*"])
    {
        return $this->iptuconstr->where(
            "j39_matric",
            "=",
            $matricula
        )->where(
            "j39_idcons",
            "=",
            $idCons
        )->first($campos);
    }

    public function getAllDtDemoNull($matricula, $campos = ["*"])
    {
        return $this->iptuconstr->where(
            "j39_matric",
            "=",
            $matricula
        )->whereNull(
            "j39_dtdemo"
        )->where(
            "j39_idprinc",
            "=",
            'false'
        )->get($campos);
    }
}
