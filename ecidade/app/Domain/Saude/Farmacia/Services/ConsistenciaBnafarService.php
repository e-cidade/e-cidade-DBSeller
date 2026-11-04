<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Factories\IntegracaoBnafarFactory;
use App\Domain\Saude\Farmacia\Relatorios\InconsistenciasBnafarPdf;
use App\Domain\Saude\Farmacia\Resources\InconsistenciaDispensacaoBnafarResource;
use App\Domain\Saude\Farmacia\Resources\InconsistenciaEntradaBnafarResource;
use App\Domain\Saude\Farmacia\Resources\InconsistenciaSaidaBnafarResource;
use App\Domain\Saude\Farmacia\Strategies\ProcedimentoBnafarStrategy;
use DBCompetencia;
use UnidadeProntoSocorro;

class ConsistenciaBnafarService
{
    /**
     * @param array $periodo
     * @param UnidadeProntoSocorro $unidade
     * @param array $procedimentos
     * @return array
     * @throws \Exception
     */
    public static function consistir(array $periodo, UnidadeProntoSocorro $unidade, array $procedimentos)
    {
        $movimentacoes = [
            ProcedimentoBnafarStrategy::ENTRADA => 'Entrada',
            ProcedimentoBnafarStrategy::SAIDA => 'Saída',
            ProcedimentoBnafarStrategy::DISPENSACAO => 'Dispensação'
        ];

        $dados = [];
        foreach ($procedimentos as $tipo) {
            $strategy = IntegracaoBnafarFactory::getStrategy($tipo, $unidade);
            $inconsistencias = $strategy->verificarInconsistencias($periodo);
            $dado = (object)[
                'tipo' => $tipo,
                'inconsistente' => $inconsistencias->isNotEmpty(),
                'relatorio' => null
            ];

            if ($inconsistencias->isNotEmpty()) {
                $titulo = "Inconsistências {$movimentacoes[$tipo]} BNAFAR";
                $pdf = new InconsistenciasBnafarPdf($inconsistencias, $titulo, $periodo);
                $dado->relatorio = $pdf->imprimir();
            }
            $dados[] = $dado;
        }

        return $dados;
    }

    /**
     * @param DBCompetencia $competencia
     * @param UnidadeProntoSocorro $unidade
     * @return array
     * @throws \Exception
     */
    public static function getInconsistencias(DBCompetencia $competencia, UnidadeProntoSocorro $unidade)
    {
        $dados = [];
        $procedimentos = [
            ProcedimentoBnafarStrategy::ENTRADA => new InconsistenciaEntradaBnafarResource(),
            ProcedimentoBnafarStrategy::SAIDA => new InconsistenciaSaidaBnafarResource(),
            ProcedimentoBnafarStrategy::DISPENSACAO => new InconsistenciaDispensacaoBnafarResource()
        ];
        $periodoInicio = new \DateTime("{$competencia->getAno()}-{$competencia->getMes()}-01");
        $periodo = [$periodoInicio, new \DateTime($periodoInicio->format('Y-m-t'))];
        foreach ($procedimentos as $tipo => $resource) {
            $strategy = IntegracaoBnafarFactory::getStrategy($tipo, $unidade);
            $inconsistencias = $strategy->verificarInconsistencias($periodo);
            $dados = array_merge($dados, $resource->toResponse($inconsistencias));
        }

        return $dados;
    }
}
