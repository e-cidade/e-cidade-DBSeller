<?php

namespace App\Domain\Tributario\Fiscalizacao\Services\Vistoria;

use App\Domain\Tributario\Fiscalizacao\Services\CheckUseModuloFiscalizacaoService;
use DB;

class GetExerciciosCalculadosService
{
    public function execute($params)
    {
        $numcgm = $params['numcgm'];
        $schema = CheckUseModuloFiscalizacaoService::verify() ? 'fiscalizacao.fis_' : 'fiscal.';
        $exercicios = DB::select("
            with inscricao_cgm as (
                select y71_codvist from issbase
                inner join {$schema}vistinscr on y71_inscr = q02_inscr
                where q02_numcgm = :numcgm
            ),
            sanitario_cgm as (
                select y74_codvist from {$schema}sanitario
                inner join {$schema}vistsanitario on y74_codsani = y80_codsani
                where y80_numcgm = :numcgm
            ),
            vistorias_cgm AS (
                select y71_codvist as codvist from inscricao_cgm
                union
                select y74_codvist from sanitario_cgm
            )
            select distinct y123_anousu
            from {$schema}vistoriacalculo
            inner join vistorias_cgm on y123_codvist = vistorias_cgm.codvist
            order by y123_anousu desc
        ", ['numcgm' => $numcgm]);

        return $exercicios;
    }
}
