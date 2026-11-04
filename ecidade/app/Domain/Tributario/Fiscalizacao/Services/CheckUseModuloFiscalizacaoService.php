<?php

namespace App\Domain\Tributario\Fiscalizacao\Services;

final class CheckUseModuloFiscalizacaoService
{
    /**
     * Verifica se o cliente usa o modulo fiscalizacao
     *
     * @return boolean
     * */
    public static function verify()
    {
        $idModuloFiscalizacao = 229038;
        $sql = "select libcliente from db_itensmenu where id_item = {$idModuloFiscalizacao} limit 1";
        $rsSql = db_query($sql);
        if (pg_num_rows($rsSql) > 0) {
            $isLibcliente = pg_fetch_object($rsSql, 0);
            if ($isLibcliente->libcliente === 't') {
                return true;
            }
        }
        return false;
    }
}
