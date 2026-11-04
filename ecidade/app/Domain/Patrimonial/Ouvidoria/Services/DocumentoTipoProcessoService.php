<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use Illuminate\Support\Facades\DB;

class DocumentoTipoProcessoService
{
    public static function getDocumentos($tipo_processo)
    {
        $sql = "
                    SELECT
            procdoc.p56_coddoc AS documento_codigo,
            procdoc.p56_descr AS documento_descricao,
            procdoc.p56_ouvidoriatipodado AS documento_tipodado,
            procdoctipo.p57_ouvidoriaobrigatorio AS documento_obrigatorio
        FROM
            procdoc
        INNER JOIN
            procdoctipo ON procdoctipo.p57_coddoc = procdoc.p56_coddoc
        WHERE
            procdoctipo.p57_codigo = ?;
        ";

        return DB::select($sql, [$tipo_processo]);
    }
}
