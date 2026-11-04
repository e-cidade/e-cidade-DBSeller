<?php

namespace App\Domain\Educacao\Escola\Services;

use cl_alunopossib;
use Etapa;
use Exception;
use Turma;

class AtaResultadosFinaisSigaService
{
    /**
     * @var Turma
     */
    private $turma;

    /**
     * @var Etapa
     */
    private $etapa;

    /**
     * @var boolean
     */
    private $somenteAlunosAtivos = false;

    /**
     * @param Turma $turma
     * @param Etapa $etapa
     */
    public function __construct(Turma $turma, Etapa $etapa)
    {
        $this->turma = $turma;
        $this->etapa = $etapa;
    }

    /**
     * @param $somenteAlunosAtivos
     * @return $this
     */
    public function setSomenteAlunosAtivos($somenteAlunosAtivos)
    {
        $this->somenteAlunosAtivos = $somenteAlunosAtivos;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getDados()
    {
        $dadosRelatorio = (object)[
            "turma" => $this->turma,
            "etapa" => $this->etapa,
            "alunos" => []
        ];
        $matriculas = $this->turma->getAlunosMatriculadosNaTurmaPorSerie($this->etapa);
        $matriculas = array_filter($matriculas, function ($matricula) {
            return $matricula->getSituacao() == 'MATRICULADO';
        });

        foreach ($matriculas as $matricula) {
            $termoResultadoFinal = \DBEducacaoTermo::getTermoEncerramento(
                $this->etapa->getEnsino()->getCodigo(),
                $matricula->getDiarioDeClasse()->getResultadoFinal()
            );

            $sqlEtapaDestino = "SELECT ed11_c_descr FROM etapadestinomatricula JOIN serie
    ON ed11_i_codigo = ed364_etapadestino WHERE ed364_matricula = {$matricula->getCodigo()}
                                          order by ed364_codigo desc ";
            $rs = db_query($sqlEtapaDestino);
            if (!$rs) {
                throw new Exception("Erro ao buscar etapa de destino.");
            }

            $etapaDestino = "-";
            if (pg_num_rows($rs) > 0) {
                $etapaDestino = pg_fetch_assoc($rs)['ed11_c_descr'];
            }
            $dadosRelatorio->alunos[$matricula->getCodigo()] = (object)[
                "matricula" => $matricula,
                "resultadoFinal" => $termoResultadoFinal[0]->sDescricao,
                "etapaDestino" => $etapaDestino
            ];
        }

        return $dadosRelatorio;
    }
}
