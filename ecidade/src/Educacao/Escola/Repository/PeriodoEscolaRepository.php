<?php

namespace ECidade\Educacao\Escola\Repository;

class PeriodoEscolaRepository
{
    private $dao;
    public function __construct()
    {
        $this->dao = new \cl_periodoescola();
    }
    public function getByPeriodoAulaEscolaTurno($escola, $periodoAula, $turno, $campos = "*")
    {
        $where = "ed17_i_periodoaula = {$periodoAula}";
        $where .= " and ed17_i_escola = {$escola} and ed17_i_turno = {$turno}";
        $sql = $this->dao->sql_query_file(
            null,
            $campos,
            null,
            $where
        );
        $rs = db_query($sql);
        return $rs ? pg_fetch_assoc($rs) : false;
    }
}
