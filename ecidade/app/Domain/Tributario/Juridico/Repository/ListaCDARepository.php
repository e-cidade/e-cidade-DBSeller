<?php

namespace App\Domain\Tributario\Juridico\Repository;

use App\Domain\Tributario\Notificacoes\Model\Lista;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Traits\Pagination;
use Illuminate\Support\Facades\DB;

class ListaCDARepository
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
                DB::raw('(select count(distinct k00_numpar)  
                    from arrecad where k00_numpre = v07_numpre 
                    and k00_dtvenc < CURRENT_DATE) as qtd_parcelas_vencidas'),
                DB::raw('case when v07_situacao = 1 
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

    public static function verificaTiposDebitos($k60_codigo)
    {
        return DB::select(
            "select 
                distinct b.k03_tipo, k03_descr
            from lista 
            inner join listatipos on k60_codigo = k62_lista 
            inner join arretipo a on k62_tipodeb = a.k00_tipo 
            inner join cadtipo b on a.k03_tipo = b.k03_tipo  
            where k60_codigo = ?",
            [$k60_codigo]
        );
    }

    public function getLista($k60_codigo)
    {
        return DB::select(
            "select
            *
        from
            (
            select
                distinct v51_inicial,
                v13_certid,
                v13_dtemis,
                'cda_divida' as tipo_cda,
                a.k00_tipo as tipo_debito,
                b.k00_descr,
                b.k03_tipo
            from
                divida
            inner join certdiv on
                v01_coddiv = v14_coddiv
            inner join certid on
                v14_certid = v13_certid
            inner join arrecad a on
                v01_numpre = a.k00_numpre
                and v01_numpar = a.k00_numpar
            inner join arretipo b on
                a.k00_tipo = b.k00_tipo
            left join inicialcert on
                v14_certid = v51_certidao
            where
                v01_numpre in (
                select
                    distinct k61_numpre
                from
                    listadeb
                where
                    k61_codigo = ?)
        union all
            select
                distinct v51_inicial,
                v13_certid,
                v13_dtemis,
                'cda_parcelamento' as tipo_cda,
                a.k00_tipo as tipo_debito,
                b.k00_descr,
                b.k03_tipo
            from
                certid
            inner join certter on
                v13_certid = v14_certid
            inner join termo on
                v14_parcel = v07_parcel
            inner join arrecad a on
                v07_numpre = a.k00_numpre
            inner join arretipo b on
                a.k00_tipo = b.k00_tipo
            left join inicialcert on
                v14_certid = v51_certidao
            where
                v07_numpre in (
                select
                    distinct k61_numpre
                from
                    listadeb
                where
                    k61_codigo = ?)) as x;",
            [$k60_codigo, $k60_codigo]
        );
    }

    public static function cdaslAtivas($k60_codigo)
    {
        return DB::select(
            "select
            *
        from
            (
            select
                distinct v51_inicial,
                v13_certid
            from
                divida
            inner join certdiv on
                v01_coddiv = v14_coddiv
            inner join certid on
                v14_certid = v13_certid
            inner join arrecad a on
                v01_numpre = a.k00_numpre
                and v01_numpar = a.k00_numpar
            inner join arretipo b on
                a.k00_tipo = b.k00_tipo
            left join inicialcert on
                v14_certid = v51_certidao
            where
                v01_numpre in (
                select
                    distinct k61_numpre
                from
                    listadeb
                where
                    k61_codigo = ?)
        union all
            select
                distinct v51_inicial,
                v13_certid
            from
                certid
            inner join certter on
                v13_certid = v14_certid
            inner join termo on
                v14_parcel = v07_parcel
            inner join arrecad a on
                v07_numpre = a.k00_numpre
            inner join arretipo b on
                a.k00_tipo = b.k00_tipo
            left join inicialcert on
                v14_certid = v51_certidao
            where
                v07_numpre in (
                select
                    distinct k61_numpre
                from
                    listadeb
                where
                    k61_codigo = ?)) as x;",
            [$k60_codigo, $k60_codigo]
        );
    }

    public static function pesquisaCDAinclusaoInicial($k60_codigo)
    {
        return DB::select(
            "select
            *
        from
            (
            select
                distinct 
                v51_inicial,
                case
                    when certdiv.v14_certid is not null 
                        then certdiv.v14_certid
                    else acertdiv.v14_certid
                end as v13_certid,
                case
                    when certdiv.v14_certid is not null 
                        then 'Ativa'
                    else 'Anulada'
                end as situacao,
                case
                    when (arrematric.k00_matric) is not null 
                        then 'M-'|| arrematric.k00_matric  
                    when (arreinscr.k00_inscr) is not null 
                        then 'I-'||(arreinscr.k00_inscr)
                else 'C-'||v01_numcgm
            end as origem
            from
                divida
            left join certdiv    on v01_coddiv = certdiv.v14_coddiv	
            left join acertdiv   on v01_coddiv = acertdiv.v14_coddiv	
            inner join arrecad a  on v01_numpre = a.k00_numpre	and v01_numpar = a.k00_numpar
            left join inicialcert on certdiv.v14_certid = v51_certidao or acertdiv.v14_certid = v51_certidao
            left join arrematric on arrematric.k00_numpre = v01_numpre
            left join arreinscr on arreinscr.k00_numpre = v01_numpre
            where
                v01_numpre in (
                select
                    distinct k61_numpre
                from
                    listadeb
                where
                    k61_codigo = ?)
        union all 
        select
            distinct v51_inicial,
                case
                    when certter.v14_certid is not null 
                        then certter.v14_certid
                    else acertter.v14_certid
                end as v13_certid,
                case
                    when certter.v14_certid is not null 
                        then 'Ativa'
                    else 'Anulada'
                end as situacao,
                case
                    when (select k00_matric from arrematric where k00_numpre = a.k00_numpre limit 1 ) is not null 
                        then 'M-'||(select k00_matric from arrematric where k00_numpre = a.k00_numpre  limit 1)
                    when (select k00_inscr from arreinscr where k00_numpre = a.k00_numpre limit 1) is not null 
                        then 'I-'||(select k00_inscr from arreinscr where k00_numpre = a.k00_numpre limit 1)
                    else 'C-'||a.k00_numcgm
                end as origem
        from
            termo
            left join  acertter on acertter.v14_parcel = v07_parcel
            left join  certter  on certter.v14_parcel = v07_parcel
            inner join arrecad a on v07_numpre = a.k00_numpre 
            left join inicialcert on certter.v14_certid = v51_certidao or acertter.v14_certid = v51_certidao
        where
            v07_numpre in (
            select
                    distinct k61_numpre
            from
                    listadeb
            where
                    k61_codigo = ?)) as x",
            [$k60_codigo, $k60_codigo]
        );
    }

    public function getHistoricoInicial($k60_codigo)
    {
        return DB::select(
            "select
                    max(v56_inicial),
                    min(v56_inicial)
                from
                    inicialmov
                where
                    v56_obs like '%Incluida conforme lista: $k60_codigo'"
        );
    }
}
