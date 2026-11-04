<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Repositories\UnidadesEncaminhadorasRepository;

class UnidadesEncaminhadorasService extends UnidadesEncaminhadorasRepository
{
    protected $repository;
    protected $descricao;
    protected $ativo;
    protected $codigo;

    public function __construct()
    {
        $this->repository = new UnidadesEncaminhadorasRepository();
    }

    public function saveUnidade($dados)
    {
        if (empty($dados['codigo'])) {
            return $this->repository->createUnidade($dados);
        }
        return $this->repository->updateUnidade($dados);
    }

    public function getByUnidade($id)
    {
        $response = $this->repository->getByUnidade($id);
        return $response;
    }

    public function get()
    {
        $response = $this->repository->get();
        return $response;
    }

    public function deleteUnidade($dados)
    {
        $response = $this->repository->deleteUnidade($dados);
        return $response;
    }
}
