<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services;

use Exception;
use App\Domain\RecursosHumanos\Pessoal\Repository\LotacaoRepository;
use BusinessException;
use stdClass;

class LotacaoService
{
    private $codigoInstituicao;

    public function __construct($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function getLotacoes()
    {
        try {
            $dados = LotacaoRepository::getLotacoes($this->codigoInstituicao);
            $retorno = [];
            foreach ($dados as $dado) {
                $obj = new stdClass();
                $obj->codigo = $dado->getCodigo();
                $obj->estrutural = $dado->getCodigoEstrutural();
                $obj->descricao = $dado->getDescricao();
                $retorno [] = $obj;
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }

    public function getLotacao($codigo)
    {
        try {
            $retorno = new stdClass();
            $dado = LotacaoRepository::getLotacao($this->codigoInstituicao, $codigo);
            if ($dado) {
                $retorno->codigo = $dado->getCodigo();
                $retorno->estrutural = $dado->getCodigoEstrutural();
                $retorno->descricao = $dado->getDescricao();
            } else {
                $retorno->codigo = null;
                $retorno->descricao = "Código da lotação não encontrado.";
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }

    public function getLotacoesAtivas()
    {
        try {
            $dados = LotacaoRepository::getLotacoesAtivas($this->codigoInstituicao);
            $retorno = [];
            foreach ($dados as $dado) {
                $obj = new stdClass();
                $obj->codigo = $dado->getCodigo();
                $obj->estrutural = $dado->getCodigoEstrutural();
                $obj->descricao = $dado->getDescricao();
                $retorno [] = $obj;
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }
}
