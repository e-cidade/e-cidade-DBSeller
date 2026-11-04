<?php

namespace ECidade\Patrimonial\Material\Repositories;

use ECidade\Educacao\Escola\Repository\Repository;
use ECidade\Patrimonial\Material\Models\Fabricante;
use Exception;

class FabricanteRepository extends Repository
{
    /**
     * @param integer $id
     * @throws Exception
     * @return Fabricante
     */
    public static function find($id)
    {
        $dao = new \cl_matfabricante;
        $sql = $dao->sql_query_file($id);
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new Exception("Fabricante não encontrado.");
        }

        return Fabricante::fromState(pg_fetch_assoc($rs, 0));
    }
}
