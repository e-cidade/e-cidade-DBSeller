<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;

class CotaUnicaRepository
{
    /**
     * Retorna uma lista de data de vencimento e desconto de cota única
     *
     * @return array
     */
    public static function getCotaUnica($k00_tipo = null)
    {
        $tipo    = (!is_null($k00_tipo)) ? 'and k00_tipo = ' . $k00_tipo : '';
        $datausu = date('Y-m-d', db_getsession('DB_datausu'));

        $sql =<<<SQL
            select
                distinct recibounica.k00_dtvenc,
                recibounica.k00_dtoper,
                recibounica.k00_percdes
            from
                recibounica
                join arrecad 
                    on arrecad.k00_numpre = recibounica.k00_numpre
                    {$tipo}
            where
                k00_tipoger = 'G'
                and recibounica.k00_dtvenc > '{$datausu}'
            order by
                k00_dtvenc,
                k00_percdes;
SQL;

        return DB::select(DB::raw($sql));
    }

    /**
     * Retorna os dados da CotaUnica Geral
     *
     * @param $k00_tipo Tipo de debito
     * @param $k00_dtvenc Data de vencimento da CotaUnica
     *
     * @throws Exception Caso nao ache nenhuma CotaUnica com os paramentros passado
     *
     * @return CotaUnica
     */
    public static function getCotaUnicaGeral($k00_tipo, $k00_dtvenc)
    {
        $sql =<<<SQL
            select
                distinct recibounica.k00_dtvenc,
                recibounica.k00_dtoper,
                recibounica.k00_percdes
            from
                recibounica
                join arrecad 
                    on arrecad.k00_numpre = recibounica.k00_numpre
                    and k00_tipo = {$k00_tipo}
            where
                k00_tipoger = 'G'
                and recibounica.k00_dtvenc = '{$k00_dtvenc}'
            order by
                k00_dtvenc,
                k00_percdes limit 1;
SQL;

        $result = DB::select($sql);

        if (count($result) <= 0) {
            throw new Exception('Nenhuma cota Unica foi encontrada');
        }

        $result = $result[0];
        
        return new CotaUnica($result->k00_dtvenc, $result->k00_percdes, true);
    }
}
