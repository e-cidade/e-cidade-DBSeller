<?php

namespace ECidade\Educacao\Escola\Repository;

class RegenciaRepository
{
    public $dao;

    public function __construct()
    {
        $this->dao = new \cl_regencia();
    }
    public function getByTurma($turma, $campos = '*')
    {
        $retorno = [];
        $sSqlRegencia = $this->dao->sql_query_file(
            null,
            $campos,
            null,
            "ed59_i_turma = {$turma}"
        );
        $rsRegencia = db_query($sSqlRegencia);

        while ($row = pg_fetch_assoc($rsRegencia)) {
            $retorno[] = $row;
        }

        return $retorno;
    }
}
