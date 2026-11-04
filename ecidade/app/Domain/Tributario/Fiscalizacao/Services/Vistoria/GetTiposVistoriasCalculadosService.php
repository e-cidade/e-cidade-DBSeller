<?php

namespace App\Domain\Tributario\Fiscalizacao\Services\Vistoria;

use App\Domain\Tributario\Fiscalizacao\Services\CheckUseModuloFiscalizacaoService;
use DB;

class GetTiposVistoriasCalculadosService
{
    public function execute($params)
    {
        $numcgm = $params['numcgm'];
        $schema = CheckUseModuloFiscalizacaoService::verify() ? 'fiscalizacao.fis_' : 'fiscal.';
        $tipos = DB::select("
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
            select distinct y77_descricao as name, y77_codtipo as id
            from {$schema}vistoriacalculo
            inner join {$schema}vistorias on y123_codvist = y70_codvist
            inner join {$schema}tipovistorias on y77_codtipo = y70_tipovist
            inner join vistorias_cgm on vistorias_cgm.codvist = y123_codvist
            order by y77_codtipo desc
        ", ['numcgm' => $numcgm]);

        return $tipos;
    }
}
