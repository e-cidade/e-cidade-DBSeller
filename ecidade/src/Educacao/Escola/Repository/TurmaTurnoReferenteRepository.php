<?php

namespace ECidade\Educacao\Escola\Repository;

class TurmaTurnoReferenteRepository
{
    private $dao;

    /**
     * @param $dao
     */
    public function __construct()
    {
        $this->dao = new \cl_turmaturnoreferente();
    }

    public function find($pk, $campos = "*")
    {
        $sql = $this->dao->sql_query_file($pk, $campos);
        $rs = db_query($sql);

        return $rs ? pg_fetch_assoc($rs) : false;
    }


    public function getByTurma($turma, $campos = "*")
    {
        $retorno = [];
        $sql = $this->dao->sql_query_file(null, $campos, null, "ed336_turma = {$turma}");
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Falha ao buscar turmaturnoreferente");
        }
        while ($row = pg_fetch_assoc($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function update($pk, $parametros)
    {
        $turmaTurnoReferente = $this->find($pk);

        foreach ($parametros as $campo => $valor) {
            $turmaTurnoReferente[$campo] = $valor;
        }

        $this->dao->ed336_codigo = $turmaTurnoReferente['ed336_codigo'];
        $this->dao->ed336_turma = $turmaTurnoReferente['ed336_turma'];
        $this->dao->ed336_turnoreferente = $turmaTurnoReferente['ed336_turnoreferente'];
        $this->dao->ed336_vagas = $turmaTurnoReferente['ed336_vagas'];

        return $this->dao->alterar($pk);
    }

    public function save($parametros)
    {
        $this->dao->ed336_codigo = null;
        $this->dao->ed336_turma = $parametros['ed336_turma'];
        $this->dao->ed336_turnoreferente = $parametros['ed336_turnoreferente'];
        $this->dao->ed336_vagas = $parametros['ed336_vagas'];

        $this->dao->incluir(null);
        return $this->dao;
    }

    public function excluir($pk = null, $where = "")
    {
        $dbwhere = !is_null($pk) ? "ed336_codigo = {$pk}" : $where;
        return $this->dao->excluir(null, $dbwhere);
    }
}
