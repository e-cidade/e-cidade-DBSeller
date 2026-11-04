<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use cl_ordenacaocgf;
use DBException;
use ECidade\Tributario\Arrecadacao\Model\OrdenacaoCgf;
use stdClass;

class OrdenacaoCgfRepository
{
    /**
     * @param OrdenacaoCgf $ordenacaoCgf
     * @return int
     * @throws DBException
     */
    public function persist(OrdenacaoCgf $ordenacaoCgf)
    {
        $oDaoOrdenacaoCgf = new cl_ordenacaocgf();
        $oDaoOrdenacaoCgf->k241_grupo = $ordenacaoCgf->getGrupo();
        $oDaoOrdenacaoCgf->k241_tipo = $ordenacaoCgf->getTipo();

        $sequencial = $ordenacaoCgf->getSequencial();
        if (isset($sequencial) && !empty($sequencial)) {
            $oDaoOrdenacaoCgf->k241_sequencial = $sequencial;
            $oDaoOrdenacaoCgf->alterar($sequencial);
        } else {
            $oDaoOrdenacaoCgf->incluir();
        }

        if ($oDaoOrdenacaoCgf->erro_status == 0) {
            throw new DBException($oDaoOrdenacaoCgf->erro_msg);
        }
        return $oDaoOrdenacaoCgf->k241_sequencial;
    }

    /**
     * @param integer $cadtipo
     * @return void
     * @throws DBException
     */
    public function apagarPorGrupo($cadtipo)
    {
        $oDaoOrdenacaoCgf = new cl_ordenacaocgf();
        $oDaoOrdenacaoCgf->excluir(null, "k241_grupo = {$cadtipo}");

        if ($oDaoOrdenacaoCgf->erro_status == 0) {
            throw new DBException($oDaoOrdenacaoCgf->erro_msg);
        }
    }

    /**
     * @param integer $cadtipo
     * @return OrdenacaoCgf[]
     */
    public function getByGrupoDebito($cadtipo)
    {
        $oDaoOrdenacao = new cl_ordenacaocgf();
        $sqlOrdenacao = $oDaoOrdenacao->sql_query_file(
            null,
            '*',
            null,
            "k241_grupo = {$cadtipo}"
        );
        $rsOrdenacao = db_query($sqlOrdenacao);
        if (!$rsOrdenacao) {
            throw new DBException('Ocorreu um erro ao buscar as ordenações.');
        }
        return $this->buildCollection($rsOrdenacao);
    }

    /**
     * @param $rsResult
     * @return OrdenacaoCgf[]
     * @throws \Exception
     */
    private function buildCollection($rsResult)
    {
        $aCollection = array();
        $aResult = pg_fetch_all($rsResult);
        if (empty($aResult)) {
            return array();
        }
        foreach ($aResult as $oResult) {
            $aCollection[] = $this->buildModel((object) $oResult);
        }
        return $aCollection;
    }

    /**
     * @param stdClass $stdClass
     * @return OrdenacaoCgf
     */
    private function buildModel(stdClass $stdClass)
    {
        $ordenacao = new OrdenacaoCgf();
        $ordenacao->setSequencial($stdClass->k241_sequencial);
        $ordenacao->setGrupo($stdClass->k241_grupo);
        $ordenacao->setTipo($stdClass->k241_tipo);
        return $ordenacao;
    }
}
