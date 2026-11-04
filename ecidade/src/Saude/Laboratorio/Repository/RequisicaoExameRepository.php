<?php

namespace ECidade\Saude\Laboratorio\Repository;

use RequisicaoLaboratorial;
use RequisicaoExame;

class RequisicaoExameRepository
{
    /**
     * @var \cl_lab_requiitem
     */
    private $dao;

    /**
     * @var string[]
     */
    private $scopes;

    /**
     * RequisicaoExameRepository constructor.
     * @param \cl_lab_requiitem $dao
     */
    public function __construct(\cl_lab_requiitem $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param RequisicaoLaboratorial $requisicao
     * @return array|bool
     * @throws \DBException
     */
    public function getRequisicaoExamePorRequisicao(
        RequisicaoLaboratorial $requisicao
    ) {
        $columns = '
           la21_i_codigo, la21_i_requisicao, la21_d_entrega, la21_d_data, la21_c_hora,
           la21_i_setorexame, la21_i_emergencia, la21_c_situacao, la21_i_quantidade,
           la21_observacao, la08_i_codigo, la22_i_cgs
        ';
        $sql = $this->dao->sql_query_materiais_exame_requisicao(
            $requisicao->getCodigo(),
            $columns,
            ''
        );

        $rs = db_query($sql);
        $numeroRegistros = pg_num_rows($rs);

        if (!$rs || $numeroRegistros === 0) {
            return false;
        }

        $requisicaoExames = array();
        for ($i = 0; $i < $numeroRegistros; $i++) {
            $row = pg_fetch_array($rs, $i);
            $requisicaoExames[] = RequisicaoExame::fromState($row);
        }

        return $requisicaoExames;
    }

    /**
     * @return RequisicaoExame
     * @throws \DBException
     */
    public function getRequisicaoExame($txtSql)
    {
        $resultadoSql = db_query($txtSql);

        $numeroRegistros = pg_num_rows($resultadoSql);

        if (!$resultadoSql || $numeroRegistros === 0) {
            return false;
        }

        $linha = pg_fetch_array($resultadoSql, 0);
        return RequisicaoExame::fromState($linha);
    }
}
