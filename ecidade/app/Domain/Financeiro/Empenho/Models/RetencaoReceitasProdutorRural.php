<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class RetencaoReceitasProdutorRural extends Model
{
    protected $table = 'empenho.retencaoreceitasprodutorrural';
    protected $primaryKey = 'e158_sequencial';
    public $timestamps = false;

    public function notas($instit, $ano, $mes, $filtroOrgaoUnidade = false, $unidadeCnpjBase = null)
    {
        $notas = DB::table($this->table)

        // fields
        ->select([
            'db_config.cgc as contribuinte',
            'z01_numcgm as cgm',
            'z01_cgccpf as nrinscProd',
            'e159_tipo as indAquis',
        ])
        ->distinct()
        ->selectRaw("
            sum(e70_vlrliq) as vlrbruto,
            sum(e158_vlrcp) as vlrcpdescpr,
            sum(e158_vlrrat) as vlrratdescpr,
            sum(e158_vlrsenar) as vlrsenardesc
        ")
        ->selectRaw("array_agg(e158_sequencial) as ids")

        // relations
        ->join('empnota', 'e158_empnota', '=', 'e69_codnota')
        ->join('empnotaele', 'empnotaele.e70_codnota', '=', 'empnota.e69_codnota')
        ->join('empempenho', 'e69_numemp', 'e60_numemp')
        ->join('cgm', 'e60_numcgm', '=', 'z01_numcgm')
        ->join('db_config', 'e60_instit', '=', 'db_config.codigo')
        ->join('pcforne', 'pc60_numcgm', 'z01_numcgm')
        ->join('emptipoaquisicaoproducaorural', 'e159_empempenho', '=', 'e60_numemp')
        ->join(
            'pagordemnota',
            'empnota.e69_codnota',
            '=',
            DB::Raw('pagordemnota.e71_codnota and pagordemnota.e71_anulado is false')
        )
        ->leftJoin('retencaoreceitas', 'e158_retencaoreceitas', '=', 'e23_sequencial')
        ->leftJoin('retencaoempagemov', 'e27_retencaoreceitas', '=', 'e23_sequencial')
        ->leftJoin('empagemov', 'e81_codmov', '=', 'e27_empagemov')

        // grouping
        ->groupBy('e159_tipo', 'db_config.cgc', 'z01_numcgm', 'z01_cgccpf')

        // conditions
        ->whereMonth('e69_dtnota', '=', $mes)
        ->whereYear('e69_dtnota', '=', $ano)
        ->where('e60_instit', '=', $instit)
        ->whereNull('e81_cancelado')
        ->where(function ($q) {
            $q->whereNull('e23_ativo')->orWhere('e23_ativo', true);
        })

        ->when($filtroOrgaoUnidade, function ($query) use ($unidadeCnpjBase) {
            $query
                // dotacao
                ->join('orcdotacao', function ($join) {
                    $join
                        ->on('o58_anousu', '=', 'e60_anousu')
                        ->on('o58_coddot', '=', 'e60_coddot');
                })

                // unidade
                ->join('orcunidade', function ($join) {
                    $join
                        ->on('o58_orgao', '=', 'o41_orgao')
                        ->on('o58_unidade', '=', 'o41_unidade')
                        ->on('o58_anousu', '=', 'o41_anousu');
                })

                // cnpj raiz
                ->select('o41_cnpj as unidade')
                ->groupBy('o41_cnpj')
                ->whereRaw("substr(o41_cnpj, 1, 8) = '{$unidadeCnpjBase}'");
        })
        ->get();

        return $notas;
    }
}
