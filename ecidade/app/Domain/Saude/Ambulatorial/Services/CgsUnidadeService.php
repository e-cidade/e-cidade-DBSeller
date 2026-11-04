<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Repositories\CgsUnidadeRepository;

class CgsUnidadeService
{
    /**
     * @var CgsUnidadeRepository $cgsUnidadeRespository
     */
    private $cgsUnidadeRespository;

    public function __construct(CgsUnidadeRepository $cgsUnidadeRespository)
    {
        $this->cgsUnidadeRespository = $cgsUnidadeRespository;
    }

    /**
     * @param $dados
     * @return object
     */
    public function atualizarCgsCpf($dados)
    {
        $salvou = $this->cgsUnidadeRespository->atualizarCgsCpf($dados);
        $resposta = [];

        $resposta['mensagem'] = "Falha ao atualizar dados!";
        $resposta['statusCode'] = 500;
        $resposta['sucesso'] = false;

        if ($salvou) {
            $resposta['mensagem'] = "Dados atualizados com sucesso!";
            $resposta['statusCode'] = 200;
            $resposta['sucesso'] = true;
        }

        return (object)$resposta;
    }
}
