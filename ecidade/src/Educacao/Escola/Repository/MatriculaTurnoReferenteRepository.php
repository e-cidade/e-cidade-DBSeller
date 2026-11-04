<?php

namespace ECidade\Educacao\Escola\Repository;

class MatriculaTurnoReferenteRepository
{
    private $dao;

    /**
     * @param $dao
     */
    public function __construct()
    {
        $this->dao = new \cl_matriculaturnoreferente();
    }

    public function find($pk, $campos = "*")
    {
        $sql = $this->dao->sql_query_file($pk, $campos);
        $rs = db_query($sql);

        return $rs ? pg_fetch_assoc($rs) : false;
    }

    public function getByTurmaTurnoReferente($codigo, $campos = "*")
    {
        $retorno = [];
        $sql = $this->dao->sql_query_file(
            null,
            $campos,
            null,
            "ed337_turmaturnoreferente = {$codigo}"
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Falha ao buscar matriculaturnoreferente");
        }
        while ($row = pg_fetch_assoc($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function getByMatricula($codigo, $campos = "*")
    {
        $retorno = [];
        $sql = $this->dao->sql_query_file(
            null,
            $campos,
            null,
            "ed337_matricula = {$codigo}"
        );

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Falha ao buscar matriculaturnoreferente");
        }
        while ($row = pg_fetch_assoc($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function update($pk, $parametros)
    {
        $matricula = $this->find($pk);

        foreach ($parametros as $campo => $valor) {
            $matricula[$campo] = $valor;
        }

        $this->dao->ed337_codigo = $matricula['ed337_codigo'];
        $this->dao->ed337_matricula = $matricula['ed337_matricula'];
        $this->dao->ed337_turmaturnoreferente = $matricula['ed337_turmaturnoreferente'];

        return $this->dao->alterar($pk);
    }

    public function save($parametros)
    {
        $this->dao->ed337_codigo = null;
        $this->dao->ed337_matricula = $parametros['ed337_matricula'];
        $this->dao->ed337_turmaturnoreferente = $parametros['ed337_turmaturnoreferente'];

        return $this->dao->incluir(null);
    }

    public function excluir($pk = null, $where = "")
    {
        $dbwhere = !is_null($pk) ? "ed337_codigo = {$pk}" : $where;
        return $this->dao->excluir(null, $dbwhere);
    }
}
