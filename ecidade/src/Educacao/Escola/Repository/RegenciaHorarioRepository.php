<?php


namespace ECidade\Educacao\Escola\Repository;

/**
 * Class RegenciaHorarioRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class RegenciaHorarioRepository extends Repository
{

    public $dao;

    public function __construct()
    {
        $this->dao = new \cl_regenciahorario();
    }

    public function find($pk, $campos = "*")
    {
        $sql = $this->dao->sql_query_file($pk, $campos);
        $rs = db_query($sql);

        return $rs ? pg_fetch_assoc($rs) : false;
    }
    public function saveRegenciaHorario($codigo, $oParam, $dtInicioAnterior, $dtFimAnterior)
    {
        $aRegencias = array();
        $dtInicio = str_replace("/", "-", $oParam->dDataInicio);
        $dtInicio = date('Y-m-d', strtotime($dtInicio));
        $dtFim = str_replace("/", "-", $oParam->dDataFim);
        $dtFim = date('Y-m-d', strtotime($dtFim));

        $sWhere = " turma.ed57_i_calendario = {$codigo}";
        $sCampos    = " ed58_i_codigo, ed58_datainicio, ed58_datafim, ed58_ativo";
        $sSqlRegencias  = $this->dao->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );
        $rsRegencias    = db_query($sSqlRegencias);

        if (pg_num_rows($rsRegencias) > 0) {
            $aRegencias = \db_utils::getCollectionByRecord($rsRegencias, false, false, true);

            foreach ($aRegencias as $regencia) {
                $oDaoRegencia = new \cl_regenciahorario();

                if ($regencia->ed58_datainicio == $dtInicioAnterior || $regencia->ed58_datafim == $dtFimAnterior) {
                    if ($regencia->ed58_datainicio == $dtInicioAnterior) {
                        $oDaoRegencia->ed58_datainicio  = $dtInicio;
                    }

                    if ($regencia->ed58_datafim == $dtFimAnterior) {
                        $oDaoRegencia->ed58_datafim = $dtFim;
                    }

                    $oDaoRegencia->ed58_ativo = $regencia->ed58_ativo;
                    $oDaoRegencia->ed58_i_codigo = $regencia->ed58_i_codigo;
                    $oDaoRegencia->alterar($regencia->ed58_i_codigo);
                }
            }
        }
    }

    public function getPeriodosAulaByRegencia(array $codigos)
    {
        $retorno  = [];
        $string = implode(",", $codigos);
        $sql = $this->dao->sql_query_regenciahorario_periodo_aula(
            null,
            'ed58_i_codigo, ed08_i_codigo',
            null,
            "ed58_i_regencia in ({$string}) and ed58_ativo = true"
        );
        $rs = db_query($sql);
        while ($resp = pg_fetch_assoc($rs)) {
            $retorno[] = $resp;
        }
        return $retorno;
    }

    public function update($pk, array $parametros)
    {
        $regencia = $this->find($pk);
        foreach ($parametros as $campo => $valor) {
            $regencia[$campo] = $valor;
        }
        $this->dao->ed58_i_codigo = $regencia['ed58_i_codigo'];
        $this->dao->ed58_i_regencia = $regencia['ed58_i_regencia'];
        $this->dao->ed58_i_diasemana = $regencia['ed58_i_diasemana'];
        $this->dao->ed58_i_periodo = $regencia['ed58_i_periodo'];
        $this->dao->ed58_i_rechumano = $regencia['ed58_i_rechumano'];
        $this->dao->ed58_ativo = $regencia['ed58_ativo'] ? 't' : 'f';
        $this->dao->ed58_tipovinculo = $regencia['ed58_tipovinculo'];
        $this->dao->ed58_datainicio = $regencia['ed58_datainicio'];
        $this->dao->ed58_datafim = $regencia['ed58_datafim'];

        return $this->dao->alterar($pk);
    }
}
