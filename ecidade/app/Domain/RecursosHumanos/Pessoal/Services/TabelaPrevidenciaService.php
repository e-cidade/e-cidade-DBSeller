<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services;

use Exception;
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use App\Domain\RecursosHumanos\Pessoal\Repository\TabelaPrevidenciaRepository;
use BusinessException;
use stdClass;

class TabelaPrevidenciaService
{
    private $codigoInstituicao;

    public function __construct($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    public function getTabelasPrevidencia()
    {
        $competencia = CompetenciaHelper::get();
        $ano = (int) $competencia->getAno();
        $mes = (int) $competencia->getMes();
        try {
            $dados = TabelaPrevidenciaRepository::getTabelasPrevidencia($this->codigoInstituicao, $ano, $mes);
            $retorno = [];
            foreach ($dados as $dado) {
                $obj = new stdClass();
                $obj->codigo = $dado->getCodigoTabela();
                $obj->descricao = $dado->getDescricao();
                $retorno [] = $obj;
            }
            return $retorno;
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }
}
