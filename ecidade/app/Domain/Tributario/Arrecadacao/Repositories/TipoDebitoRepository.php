<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;

class TipoDebitoRepository
{
    /**
     * Retorna uma lista de tipos de débitos
     * @return array
     */
    public static function getTiposDebito()
    {
        $sql =<<<SQL
            select
                arretipo.k00_tipo,
                arretipo.k00_descr,
                arretipo.k00_emrec,
                arretipo.k03_tipo
            from
                arretipo
            inner join db_config on
                db_config.codigo = arretipo.k00_instit
            inner join cadtipo on
                cadtipo.k03_tipo = arretipo.k03_tipo
            inner join cgm on
                cgm.z01_numcgm = db_config.numcgm
            inner join db_tipoinstit on
                db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit
            order by
                k00_descr;
SQL;

        return DB::select(DB::raw($sql));
    }

    public static function getTipoDebito($k00_tipo)
    {
        $sql =<<<SQL
            select
                arretipo.k00_tipo,
                arretipo.k00_descr,
                arretipo.k00_emrec,
                arretipo.k03_tipo
            from
                arretipo
            inner join db_config on
                db_config.codigo = arretipo.k00_instit
            inner join cadtipo on
                cadtipo.k03_tipo = arretipo.k03_tipo
            inner join cgm on
                cgm.z01_numcgm = db_config.numcgm
            inner join db_tipoinstit on
                db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit
            where
                arretipo.k00_tipo = {$k00_tipo}
            limit 1;
SQL;

        $result = DB::select(DB::raw($sql));

        if (count($result) === 0) {
            throw new Exception("Nenhum resultado foi encotrado.");
        } elseif (count($result) > 1) {
            throw new Exception('Foi encontrados mais de um resultado possivel');
        }

        return $result[0];
    }
}
