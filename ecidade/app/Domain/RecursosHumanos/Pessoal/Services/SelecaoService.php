<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services;

use Exception;
use App\Domain\RecursosHumanos\Pessoal\Repository\SelecaoRepository;
use BusinessException;
use stdClass;

class SelecaoService
{
    private $codigoInstituicao;

    public function __construct($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function getSelecoes()
    {
        try {
            $dados = SelecaoRepository::getSelecoes($this->codigoInstituicao);
            $retorno = [];
            foreach ($dados as $dado) {
                $obj = new stdClass();
                $obj->codigo = $dado->getCodigo();
                $obj->descricao = $dado->getDescricao();
                $retorno [] = $obj;
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }

    public function getSelecao($codigo)
    {
        try {
            $retorno = new stdClass();
            $dado = SelecaoRepository::getSelecao($this->codigoInstituicao, $codigo);
            if ($dado) {
                $retorno->codigo = $dado->getCodigo();
                $retorno->descricao = $dado->getDescricao();
            } else {
                $retorno->codigo = null;
                $retorno->descricao = "Código de seleção não encontrado.";
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }
}
