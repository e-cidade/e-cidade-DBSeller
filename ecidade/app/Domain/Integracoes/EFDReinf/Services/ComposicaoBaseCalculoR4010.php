<?php

namespace App\Domain\Integracoes\EFDReinf\Services;

use App\Domain\Integracoes\EFDReinf\Retencao\Interfaces\ComposicaoBaseCalculoInterface;
use Illuminate\Support\Facades\DB;

class ComposicaoBaseCalculoR4010 implements ComposicaoBaseCalculoInterface
{
    private $retencao;

    public function setRetencao($retencao)
    {
        $this->retencao = $retencao;
    }

    public function getComposicao()
    {
        $retencaoDados = DB::select("
            select z01_cgccpf as cpfCredor, e23_dtcalculo as data
            from retencaoreceitas
            inner join empenho.retencaopagordem on e20_sequencial = e23_retencaopagordem
            inner join empenho.pagordem on e20_pagordem = e50_codord
            inner join empempenho on e50_numemp = e60_numemp
            inner join cgm on e60_numcgm = z01_numcgm
            where e23_sequencial = :retencao
            limit 1
        ", ['retencao' => $this->retencao]);

        $query = DB::select("
            select
            e60_codemp || '/' || e60_anousu as empenho,
            e50_codord as ordem,
            sum(case when c71_coddoc = 5 then c70_valor else c70_valor * -1 end)
            over(partition by e60_numemp) as valor
            from conlancamord inner join conlancam on c70_codlan = c80_codlan
            inner join conlancamdoc on c70_codlan = c71_codlan
            inner join pagordem on c80_codord = e50_codord
            inner join empempenho on e50_numemp = e60_numemp
            inner join cgm cgmempenho on e60_numcgm = cgmempenho.z01_numcgm
            left  join pagordemconta on e49_codord = e50_codord
            left  join cgm cgmordem on e49_numcgm = cgmordem.z01_numcgm
            where extract (year from c70_data) = :anoCalculo
            and extract (month from c70_data) = :mesCalculo
            and c70_data <= :dtCalculo
            and (
                case when e49_numcgm is null
                then cgmempenho.z01_cgccpf = :cpfCredor
                else cgmordem.z01_cgccpf = :cpfCredor
            end)
            and c71_coddoc in (5,6)
        ", [
           'cpfCredor' => $retencaoDados[0]->cpfcredor,
           'dtCalculo' => $retencaoDados[0]->data,
           'anoCalculo' => date('Y', strtotime($retencaoDados[0]->data)),
           'mesCalculo' => date('m', strtotime($retencaoDados[0]->data))
        ]);

        return $query;
    }
}
