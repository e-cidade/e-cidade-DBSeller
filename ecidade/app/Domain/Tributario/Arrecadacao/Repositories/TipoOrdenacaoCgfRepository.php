<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use cl_tipoordenacaocgf;
use DBException;
use ECidade\Tributario\Arrecadacao\Model\TipoOrdenacaoCgf;
use stdClass;

class TipoOrdenacaoCgfRepository
{

    public function getTiposDeOrdenacaoCgf()
    {
        $oDaoTipoOrdenacao = new cl_tipoordenacaocgf();
        $sqlTipoOrdenacao = $oDaoTipoOrdenacao->sql_query_file();
        $rsTipoOrdenacao = db_query($sqlTipoOrdenacao);
        if (!$rsTipoOrdenacao) {
            throw new DBException('Ocorreu um erro ao buscar os tipos de ordenação.');
        }
        return $this->buildCollection($rsTipoOrdenacao);
    }

    /**
     * @param $rsResult
     * @return TipoOrdenacaoCgf[]
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
     * @return TipoOrdenacaoCgf
     */
    private function buildModel(stdClass $stdClass)
    {
        $tipoOrdenacao = new TipoOrdenacaoCgf();
        $tipoOrdenacao->setSequencial($stdClass->k240_sequencial);
        $tipoOrdenacao->setDescricao($stdClass->k240_descricao);
        return $tipoOrdenacao;
    }
}
