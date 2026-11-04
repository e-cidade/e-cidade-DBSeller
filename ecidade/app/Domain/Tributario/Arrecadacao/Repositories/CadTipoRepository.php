<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use cl_cadtipo;
use DBException;
use ECidade\Tributario\Arrecadacao\Model\CadTipo;
use stdClass;

class CadTipoRepository
{
    /**
     * @return CadTipo[]
     */
    public function getGruposDebito()
    {
        $oDaoCadTipo = new cl_cadtipo();
        $sqlCadTipo = $oDaoCadTipo->sql_query_file(null, '*', 'k03_tipo');
        $rsCadTipo = db_query($sqlCadTipo);
        if (!$rsCadTipo) {
            throw new DBException('Ocorreu um erro ao buscar os grupos de débito.');
        }
        return $this->buildCollection($rsCadTipo);
    }

    /**
     * @param $rsResult
     * @return CadTipo[]
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
     * @return CadTipo
     */
    private function buildModel(stdClass $stdClass)
    {
        $tipoOrdenacao = new CadTipo();
        $tipoOrdenacao->setSequencial($stdClass->k03_tipo);
        $tipoOrdenacao->setDescricao($stdClass->k03_descr);
        $tipoOrdenacao->setParelaSomanteAnoAtual($stdClass->k03_parcano);
        $tipoOrdenacao->setIsParcelamento($stdClass->k03_parcelamento);
        $tipoOrdenacao->setPermiteParcelar($stdClass->k03_permparc);
        return $tipoOrdenacao;
    }
}
