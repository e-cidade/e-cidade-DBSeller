<?php

namespace App\Domain\Tributario\Fiscalizacao\Services\Vistoria;

use App\Domain\Tributario\Fiscalizacao\Services\CheckUseModuloFiscalizacaoService;
use Illuminate\Support\Facades\DB;

class GetCalculosVistoriaService
{
    public function execute($params)
    {
        $exercicio = $params['exercicio'];
        $numcgm = $params['numcgm'];
        $tipoVistoria = $params['tipoVistoria'];

        $schema = CheckUseModuloFiscalizacaoService::verify() ? 'fiscalizacao.fis_' : 'fiscal.';

        $whereTipoVistoria = '';
        if (!empty($tipoVistoria)) {
            $whereTipoVistoria = "and tipovist.y77_codtipo = {$tipoVistoria}";
        }

        $result = DB::select("
            with inscricao_cgm as (
                select q02_inscr, y71_codvist, q02_dtcada from issbase
                inner join {$schema}vistinscr on y71_inscr = q02_inscr
                where q02_numcgm = :numcgm
            ),
            sanitario_cgm as (
                select y80_codsani, y74_codvist, y80_data from {$schema}sanitario
                inner join {$schema}vistsanitario on y74_codsani = y80_codsani
                where y80_numcgm = :numcgm
            ),
            vistorias_cgm as (
                select
                    y71_codvist as codvist,
                    q02_inscr as info,
                    q02_dtcada as date,
                    true as inscricao
                from inscricao_cgm
                union
                select
                    y74_codvist,
                    y80_codsani,
                    y80_data,
                    false
                from sanitario_cgm
            )
            select vistcalculo.*,
            tipcalc.q81_codigo || ' - ' || tipcalc.q81_descr as tipocalculo,
            ativid.q03_ativ || ' - ' || ativid.q03_descr as atividade,
            forcaldesc.q87_codigo || ' - ' ||  forcaldesc.q87_descr as formacalculo,
            tabrec.k02_codigo || ' - ' || tabrec.k02_descr as receita,
            tipovist.y77_codtipo || ' - ' || tipovist.y77_descricao as tipovistoria,
            vistorias_cgm.*
            from {$schema}vistoriacalculo vistcalculo
            inner join vistorias_cgm on vistcalculo.y123_codvist = vistorias_cgm.codvist
            inner join tipcalc on vistcalculo.y123_tipcalc = tipcalc.q81_codigo
            inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
            inner join forcaldesc on forcaldesc.q87_codigo = cadcalc.q85_forcal
            inner join ativid on vistcalculo.y123_ativ = ativid.q03_ativ
            inner join {$schema}vistorias vist on vist.y70_codvist = vistcalculo.y123_codvist
            inner join {$schema}tipovistorias tipovist on tipovist.y77_codtipo = vist.y70_tipovist
            inner join tabrec on vistcalculo.y123_recei = tabrec.k02_codigo
            where vistcalculo.y123_anousu = :anousu
            {$whereTipoVistoria}
        ", ['anousu' => $exercicio, 'numcgm' => $numcgm]);

        return $result;
    }
}
