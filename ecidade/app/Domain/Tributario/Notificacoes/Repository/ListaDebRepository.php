<?php

namespace App\Domain\Tributario\Notificacoes\Repository;

use App\Domain\Tributario\Notificacoes\Model\Lista;
use App\Traits\Pagination;
use Illuminate\Support\Facades\DB;

class ListaDebRepository
{
    use Pagination;
    public function getByParams($k60_codigo, $porPagina, $page)
    {
        $lista = new Lista();
        $where = " k60_codigo = $k60_codigo ";

        $pesquisa = $lista->whereRaw($where)->distinct()
            ->join('listadeb', 'k60_codigo', '=', 'k61_codigo')
            ->join("debitos", function ($join) {
                $join->on("k61_numpre", "=", "k22_numpre")
                    ->on("k61_numpar", "=", "k22_numpar")
                    ->on("k22_data", "=", "k60_datadeb");
            })
            ->join('termo', 'k22_numpre', '=', 'v07_numpre')
            ->join('arretipo', 'k22_tipo', '=', 'k00_tipo')
            ->select([
                'v07_parcel as parcelamento',
                'v07_numpre as numpre',
                'k00_descr as tipo',
                'k60_tipo',
                \DB::raw('(select count(distinct k00_numpar)  
                    from arrecad where k00_numpre = v07_numpre 
                    and k00_dtvenc < CURRENT_DATE) as qtd_parcelas_vencidas'),
                \DB::raw('case when v07_situacao = 1 
                            and (select distinct k00_numpre 
                        from arrecad where k00_numpre = v07_numpre) is not null  then \'Ativo\' 
                        when v07_situacao = 1 
                        and (select distinct k00_numpre from arrecad where k00_numpre = v07_numpre) is  null  then 
                            \'Quitado, Cancelado ou Suspenso\' 
                        when v07_situacao = 2 then \'Anulado\' 
                        when v07_situacao = 3 then \'Reparcelado\' 
                    end as situacao ')
            ])->orderBy('v07_parcel', 'ASC')->get();

        return $this->paginate($pesquisa, $porPagina, $page);
    }

    public static function parcelAtivos($k60_codigo)
    {
        $lista = new Lista();
        return $lista->where('k60_codigo', $k60_codigo)
            ->where('v07_situacao', 1)
            ->join('listadeb', 'k60_codigo', '=', 'k61_codigo')
            ->join('termo', 'k61_numpre', '=', 'v07_numpre')
            ->select('v07_parcel as parcelamento')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('arrecad')
                    ->whereRaw('k00_numpre = v07_numpre');
            })
            ->distinct()->get();
    }
}
