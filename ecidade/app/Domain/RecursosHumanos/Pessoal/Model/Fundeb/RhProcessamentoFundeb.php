<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Fundeb;

use Illuminate\Database\Eloquent\Model;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal as ModelsRhPessoal;
use App\Domain\RecursosHumanos\Pessoal\Model\Calculo\Gerfsal;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhParametrosFundeb;
use Illuminate\Support\Facades\DB;

/**
 * Class RhProcessamentoFundeb
 * @property int rh286_sequencial
 * @property int rh286_matricula
 * @property int rh286_ano
 * @property int rh286_mes
 * @property int rh286_cargo
 * @property int rh286_local_trabalho
 * @property string rh286_rubrica
 * @property double rh286_valor
 * @property int rh286_instituicao
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Fundeb
 */

class RhProcessamentoFundeb extends Model
{
    protected $table = 'pessoal.rhprocessamentofundeb';
    protected $primaryKey = ['rh286_sequencial'];
    public $incrementing = false;
    public $timestamps = false;
    public static function quantidadeServidorProcessar($ano, $mes)
    {
        $data = date("Y-m-01", strtotime("{$ano}-{$mes}-01"));
        $dataFim = date("Y-m-t", strtotime($data . ' -1 month'));
        $dataInicio = date("Y-m-01", strtotime($dataFim));

        $rhfuncao = ModelsRhPessoal::select(
            'rh01_regist',
            'rh284_tipo',
            'rh283_quantidade',
            'rh02_lota',
            'rh284_cargo',
            'rh284_funcao',
            'rh284_local_trabalho',
            'rh284_instituicao'
        )
            ->join(
                'rhpessoalmov',
                'rh02_regist',
                '=',
                DB::Raw('rh01_regist and rh02_instit = rh01_instit')
            )
            ->join(
                'rhfuncao',
                'rh37_funcao',
                '=',
                DB::Raw('rh02_funcao and rh37_instit = rh02_instit')
            )
            ->join(
                'rhparametrosfundeb',
                'rh284_cargo',
                '=',
                DB::Raw('rh37_funcao and rh284_instituicao = rh37_instit')
            )
            ->join(
                'rhcargosfundeb',
                'rh283_codigo',
                '=',
                'rh284_tipo'
            )
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->leftJoin('rhpessoal as a', 'a.rh01_regist', '=', 'h16_regist')
                    ->whereRaw('h16_regist = rhpessoal.rh01_regist')
                    ->whereRaw('h16_assent in (
                        select 
                            rh284_assentamento 
                        from 
                            rhparametrosfundeb 
                        where 
                            rh284_instituicao = a.rh01_instit)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $rhcargo = ModelsRhPessoal::select(
            'rh01_regist',
            'rh284_tipo',
            'rh283_quantidade',
            'rh02_lota',
            'rh284_cargo',
            'rh284_funcao',
            'rh284_local_trabalho',
            'rh284_instituicao'
        )
            ->join(
                'rhpessoalmov',
                'rh02_regist',
                '=',
                DB::Raw('rh01_regist and rh02_instit = rh01_instit')
            )
            ->join(
                'rhpescargo',
                'rh20_seqpes',
                '=',
                DB::Raw('rh02_seqpes and rh20_instit = rh02_instit')
            )
            ->join(
                'rhcargo',
                'rh04_codigo',
                '=',
                DB::Raw('rh20_cargo and rh04_instit = rh20_instit')
            )
            ->join(
                'rhparametrosfundeb',
                'rh284_funcao',
                '=',
                DB::Raw('rh04_codigo and rh284_instituicao = rh04_instit')
            )
            ->join(
                'rhcargosfundeb',
                'rh283_codigo',
                '=',
                'rh284_tipo'
            )
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->leftJoin('rhpessoal as a', 'a.rh01_regist', '=', 'h16_regist')
                    ->whereRaw('h16_regist = rhpessoal.rh01_regist')
                    ->whereRaw('h16_assent in (
                        select 
                            rh284_assentamento 
                        from 
                            rhparametrosfundeb 
                        where 
                            rh284_instituicao = a.rh01_instit)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $rhlocaltrab = ModelsRhPessoal::select(
            'rh01_regist',
            'rh284_tipo',
            'rh283_quantidade',
            'rh02_lota',
            'rh284_cargo',
            'rh284_funcao',
            'rh284_local_trabalho',
            'rh284_instituicao'
        )
            ->join(
                'rhpessoalmov',
                'rh02_regist',
                '=',
                DB::Raw('rh01_regist and rh02_instit = rh01_instit')
            )
            ->leftJoin(
                'rhpeslocaltrab',
                function ($join) {
                    $join->on('rh56_seqpes', '=', 'rh02_seqpes')
                        ->where('rh56_princ', '=', 'true');
                }
            )
            ->leftJoin(
                'rhlocaltrab',
                'rh56_localtrab',
                '=',
                DB::Raw('rh55_codigo and rh55_instit = rh02_instit')
            )
            ->join(
                'rhparametrosfundeb',
                'rh284_local_trabalho',
                '=',
                'rh56_localtrab'
            )
            ->join(
                'rhcargosfundeb',
                'rh283_codigo',
                '=',
                'rh284_tipo'
            )
            ->leftJoin('rhpesrescisao', 'rh05_seqpes', '=', 'rh02_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('rhpeslocaltrab')
                    ->whereRaw("rh56_localtrab in (select rh284_local_trabalho from rhparametrosfundeb)");
            })
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->leftJoin('rhpessoal as a', 'a.rh01_regist', '=', 'h16_regist')
                    ->whereRaw('h16_regist = rhpessoal.rh01_regist')
                    ->whereRaw('h16_assent in (
                            select 
                                rh284_assentamento 
                            from 
                                rhparametrosfundeb 
                            where 
                                rh284_instituicao = a.rh01_instit)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $query = $rhfuncao->unionAll($rhcargo)
            ->unionAll($rhlocaltrab);

        $resultado = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw('DISTINCT ON (rh01_regist) rh01_regist,
                        max(rh283_quantidade) as maior_quantidade,
                        rh02_lota,
                        rh284_cargo,
                        rh284_funcao,
                        rh284_local_trabalho,
                        rh284_instituicao')
            ->groupBy(
                'rh01_regist',
                'rh284_tipo',
                'rh02_lota',
                'rh284_cargo',
                'rh284_funcao',
                'rh284_local_trabalho',
                'rh284_instituicao'
            )
            ->orderBy('rh01_regist', 'asc')
            ->get();

        return $resultado;
    }

    public static function valorAbatimentoFundeb($ano, $mes)
    {
        $rubrica = RhParametrosFundeb::select(
            'rh284_rubrica_abatimento'
        )
            ->whereRaw("rh284_rubrica_abatimento is not null and rh284_rubrica_abatimento <> ''")
            ->get();

        $rubricaAbatimento = $rubrica->first()->rh284_rubrica_abatimento;

        $totalAbatido = Gerfsal::where('r14_anousu', $ano)
            ->where('r14_mesusu', $mes)
            ->where('r14_rubric', $rubricaAbatimento)
            ->sum('r14_valor');
        return $totalAbatido;
    }
}
