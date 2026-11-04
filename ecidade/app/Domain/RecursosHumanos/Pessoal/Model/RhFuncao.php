<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal as ModelsRhPessoal;
use Illuminate\DatabASe\Eloquent\Builder;
use Illuminate\DatabASe\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * ClASs RhFuncao
 * @property int rh37_instit
 * @property int rh37_funcao
 * @property string rh37_descr
 * @property int rh37_vagAS
 * @property string  rh37_cbo
 * @property string rh37_lei
 * @property string rh37_clASs
 * @property bool rh37_ativo
 * @property int rh37_funcaogrupo
 * @property string rh37_datainicial
 * @property string rh37_datafinal
 * @property string rh37_descricaocompleta
 * @property int rh37_rhinstrucao
 * @property string rh37_descricaoatividades
 * @property string rh37_acumcargo
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 */
class RhFuncao extends Model
{
    protected $table = 'pessoal.rhfuncao';
    protected $primaryKey = ['rh37_funcao', 'rh37_instit'];
    public $incrementing = false;
    public $timestamps = false;

    public static function quantidadeServidores($ano, $mes)
    {
        $data = date("Y-m-01", strtotime("{$ano}-{$mes}-01"));
        $dataFim = date("Y-m-t", strtotime($data . ' -1 month'));
        $dataInicio = date("Y-m-01", strtotime($dataFim));

        $rhfuncao = ModelsRhPessoal::select('rh284_tipo', 'rh02_regist', 'rh283_quantidade')
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
                DB::Raw('rh284_tipo')
            )
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->whereRaw('h16_regist = rh01_regist')
                    ->whereRaw('h16_assent in (select rh284_assentamento from rhparametrosfundeb)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $rhcargo = ModelsRhPessoal::select('rh284_tipo', 'rh02_regist', 'rh283_quantidade')
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
                DB::Raw('rh284_tipo')
            )
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->whereRaw('h16_regist = rh01_regist')
                    ->whereRaw('h16_assent in (select rh284_assentamento from rhparametrosfundeb)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $rhlocaltrab = ModelsRhPessoal::select('rh284_tipo', 'rh02_regist', 'rh283_quantidade')
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
                DB::Raw('rh55_codigo and rh284_instituicao = rh55_instit')
            )
            ->join(
                'rhcargosfundeb',
                'rh283_codigo',
                '=',
                DB::Raw('rh284_tipo')
            )
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes')
            ->whereNotExists(function ($query) use ($dataInicio, $dataFim) {
                $query->select(DB::raw(1))
                    ->from('assenta')
                    ->whereRaw('h16_regist = rh01_regist')
                    ->whereRaw('h16_assent in (select rh284_assentamento from rhparametrosfundeb)')
                    ->whereRaw("(h16_dtterm is null or (h16_dtterm >= '$dataInicio' and h16_dtconc <= '$dataFim'))");
            })
            ->whereRaw("rh01_admiss < '$data'")
            ->where('rh02_anousu', $ano)
            ->where('rh02_mesusu', $mes);

        $query = $rhfuncao->unionAll($rhcargo)
            ->unionAll($rhlocaltrab);

        $resultado = DB::table(
            DB::raw(
                "(
        select
            DISTINCT ON (rh02_regist) rh02_regist,
            rh284_tipo AS tipo,
            max(rh283_quantidade) AS maior_quantidade FROM
            ({$query->toSql()})
        AS query
        group by
            rh02_regist, rh284_tipo
        order by rh02_regist
        ) AS total_por_tipo
        group by tipo
        order by tipo"
            )
        )->mergeBindings($query->getQuery())->selectRaw('COUNT(rh02_regist) AS total, tipo')->get();

        return $resultado;
    }
}
