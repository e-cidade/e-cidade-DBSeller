<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use CgmJuridico;
use stdClass;

class ExameToxicologicoMotoristaProfissionalEmpregadoFormatter extends Formatter
{

    /**
     * @var CgmJuridico
     */
    private $empregador;


    public function formatar($dados)
    {
        $dadosFormatados = [];

        foreach ($dados as $dado) {
            $dadosFormatados[] = $this->formatarDado($dado);
        }
        return $dadosFormatados;
    }

    private function formatarDado($dado)
    {
        $dadoFormatado = new stdClass();

        $dadoFormatado->referencia =
            $dado->eso41_matricula . "_" . str_replace('-', '', $dado->eso41_dtexame);
        $dadoFormatado->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $this->montarIdeVinculo($dadoFormatado, $dado);
        $this->montarToxicologico($dadoFormatado, $dado);
        return $dadoFormatado;
    }

    private function montarIdeVinculo(&$dadoFormatado, $dado)
    {
        $dadoFormatado->ideVinculo = new stdClass();
        $dadoFormatado->ideVinculo->cpfTrab = $dado->eso41_cpftrab;
        $dadoFormatado->ideVinculo->matricula = $dado->eso41_matricula;
    }

    private function montarToxicologico(&$dadoFormatado, $dado)
    {
        $dadoFormatado->toxicologico = new stdClass();
        $dadoFormatado->toxicologico->dtExame = $dado->eso41_dtexame;
        $dadoFormatado->toxicologico->cnpjLab = $dado->eso41_cnpjlab;
        $dadoFormatado->toxicologico->codSeqExame = $dado->eso41_codseqexame;
        $dadoFormatado->toxicologico->nmMed = $dado->eso41_nmmed;
        if ($dado->eso41_nrcrm) {
            $dadoFormatado->toxicologico->nrCRM = $dado->eso41_nrcrm;
        }
        if ($dado->eso41_ufcrm) {
            $dadoFormatado->toxicologico->ufCRM = $dado->eso41_ufcrm;
        }
    }

    public function setEmpregador($empregador)
    {
        $this->empregador = $empregador;
    }

    public function getEmpregador()
    {
        return $this->empregador;
    }
}
