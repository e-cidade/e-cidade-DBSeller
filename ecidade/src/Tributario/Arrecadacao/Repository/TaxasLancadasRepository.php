<?php

namespace ECidade\Tributario\Arrecadacao\Repository;

use App\Domain\Tributario\Arrecadacao\Repositories\TaxaGrupoTaxasRepository;
use db_utils;
use ECidade\Tributario\Arrecadacao\Model\TaxasLancadas;
use ECidade\V3\Extension\Registry;

class TaxasLancadasRepository extends \BaseClassRepository
{
    public function persist(TaxasLancadas $entity)
    {
        $dao = new \cl_taxaslancadas();

        $dao->ar44_sequencial = $entity->getSequencial();
        $dao->ar44_descricao = $entity->getDescricao();
        $dao->ar44_valorinflator = $entity->getValorinflator();
        $dao->ar44_inflator = $entity->getInflator();
        $dao->ar44_diasvencimento = $entity->getDiasvencimento();
        $dao->ar44_tipo = $entity->getTipo();
        $dao->ar44_receitaxaexpediente = $entity->getReceitaxaexpediente();
        $dao->ar44_valortaxaexpediente = $entity->getValortaxaexpediente();
        $dao->ar44_datavigencia = $entity->getDatavigencia();
        $dao->ar44_procedencia = $entity->getProcedencia();
        $dao->ar44_receita = $entity->getReceita();
        $dao->ar44_emissaoweb = $entity->isEmissaoweb();
        $dao->ar44_recursoadm = $entity->isRecursoadm();
        $dao->ar44_origem = $entity->getOrigem();
        $dao->ar44_permitesubtaxas = $entity->isPermiteSubtaxas();

        if (!empty($dao->ar44_sequencial)) {
            $dao->alterar($dao->ar44_sequencial);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == "0") {
            throw new \Exception($dao->erro_msg);
        }

        return $dao->ar44_sequencial;
    }

    public function make($oObject)
    {
        $taxasLancadas = new TaxasLancadas();
        $oInflator = $oObject->oInflator;

        if (!empty($oObject->ar44_sequencial)) {
            $taxasLancadas->setSequencial($oObject->ar44_sequencial);
        }

        if (!empty($oObject->ar44_descricao)) {
            $taxasLancadas->setDescricao($oObject->ar44_descricao);
        }

        if (!empty($oObject->ar44_valorinflator)) {
            $taxasLancadas->setValorinflator($oObject->ar44_valorinflator);
        }

        if (!empty($oObject->ar44_inflator)) {
            $taxasLancadas->setInflator($oObject->ar44_inflator);
        }

        if (!empty($oObject->ar44_diasvencimento)) {
            $taxasLancadas->setDiasvencimento($oObject->ar44_diasvencimento);
        }

        if ($oObject->ar44_tipo != "") {
            $taxasLancadas->setTipo($oObject->ar44_tipo);
        }

        if (!empty($oObject->ar44_receitaxaexpediente)) {
            $taxasLancadas->setReceitaxaexpediente($oObject->ar44_receitaxaexpediente);
        }

        if (!empty($oObject->ar44_valortaxaexpediente)) {
            $taxasLancadas->setValortaxaexpediente($oObject->ar44_valortaxaexpediente);
        }

        if (!empty($oObject->ar44_datavigencia)) {
            $taxasLancadas->setDatavigencia($oObject->ar44_datavigencia);
        }

        if (!empty($oObject->ar44_procedencia)) {
            $taxasLancadas->setProcedencia($oObject->ar44_procedencia);
        }

        if (!empty($oObject->ar44_receita)) {
            $taxasLancadas->setReceita($oObject->ar44_receita);
        }

        if (!empty($oObject->ar44_emissaoweb)) {
            $taxasLancadas->setEmissaoweb($oObject->ar44_emissaoweb);
        }

        if (!empty($oObject->ar44_recursoadm)) {
            $taxasLancadas->setRecursoadm($oObject->ar44_recursoadm);
        }

        if (!empty($oObject->ar44_permitesubtaxas)) {
            $taxasLancadas->setPermiteSubtaxas($oObject->ar44_permitesubtaxas);
        }

        if ($oInflator && $oInflator->getValor() != "") {
            $taxasLancadas->setValor($oInflator->getValor());
        }

        if (!empty($oObject->ar44_origem)) {
            $taxasLancadas->setOrigem($oObject->ar44_origem);
        }

        return $taxasLancadas;
    }

    private function workData(TaxasLancadas $oTaxa)
    {
        $oDados = (object) array();

        $oDados->ar44_sequencial = $oTaxa->getSequencial();
        $oDados->ar44_descricao = $oTaxa->getDescricao();
        $oDados->ar44_valorinflator = $oTaxa->getValorinflator();
        $oDados->i02_valor = floatval($oTaxa->getValor() * $oDados->ar44_valorinflator);
        $oDados->ar44_inflator = $oTaxa->getInflator();
        $oDados->ar44_diasvencimento = $oTaxa->getDiasvencimento();
        $oDados->ar44_tipo = $oTaxa->getTipo();
        $oDados->ar44_receitaxaexpediente = $oTaxa->getReceitaxaexpediente();
        $oDados->ar44_valortaxaexpediente = $oTaxa->getValortaxaexpediente();
        $oDados->ar44_datavigencia = $oTaxa->getDatavigencia();
        $oDados->ar44_procedencia = $oTaxa->getProcedencia();
        $oDados->ar44_receita = $oTaxa->getReceita();
        $oDados->ar44_emissaoweb = $oTaxa->isEmissaoweb();
        $oDados->ar44_recursoadm = $oTaxa->isRecursoadm();
        $oDados->geraDebito = $oTaxa->isGeraDebito();
        $oDados->valorTaxaExpediente = round(($oTaxa->getValortaxaexpediente() * $oTaxa->getValor()), 2);
        $oDados->ar44_origem = $oTaxa->getOrigem();
        $oDados->ar44_permitesubtaxas = $oTaxa->isPermiteSubtaxas();

        return $oDados;
    }

    public function makeCollection($rReturn)
    {
        $oReturn = db_utils::getColectionByRecord($rReturn);
        $inflaRepository = Registry::get('app.container')->get('tributario.container')->get('InflaRepository');
        $aDados = array();

        foreach ($oReturn as $item) {
            $oDados = (object) array();

            $sWhere = " infla.i02_codigo = '{$item->ar44_inflator}' AND EXTRACT(YEAR FROM infla.i02_data) = ".date("Y");

            if ($item->i01_dm == "0") {
                $sWhere .= " AND EXTRACT(MONTH FROM infla.i02_data) = ".date("m");
            } else {
                if ($item->i01_dm == "1") {
                    $sWhere .= " AND EXTRACT(DAY FROM infla.i02_data) = ".date("d");
                }
            }

            $oInflator = null;

            $aInflator = $inflaRepository->findAll($sWhere);

            if (count($aInflator) > 0) {
                $oInflator = $aInflator[0];
            }

            $item->oInflator = $oInflator;

            $oTaxa = $this->make($item);

            $oDados = $this->workData($oTaxa);

            $oDados->grupos = $this->getGruposTaxa($oDados->ar44_sequencial);

            $aDados[] = $oDados;
        }

        return $aDados;
    }

    public function getTaxas($sWhere = "")
    {
        $dao = new \cl_taxaslancadas();

        $result = db_query($dao->sql_query("", "", "ar44_sequencial", $sWhere));

        if (!$result) {
            throw new \Exception("Erro ao buscar as taxas");
        }

        return $this->makeCollection($result);
    }

    public function getTaxa($sequencial)
    {
        $dao = new \cl_taxaslancadas();

        $result = $dao->sql_record($dao->sql_query("", "*", "", " ar44_sequencial = {$sequencial}"));

        if (!$result) {
            throw new \Exception("Erro aos buscar a taxa. \n\n {$dao->erro_msg}");
        }

        return $this->makeCollection($result)[0];
    }

    public function getUgTaxasGrm($codigo = null)
    {
        $dao = new \cl_taxaslancadas();

        return $dao->getUgTaxasGrm($codigo);
    }

    public function getTaxasUgGrm($codigoUg, $codigoTaxa = null, $isTaxa = false)
    {
        $dao = new \cl_taxaslancadas();

        return $dao->getTaxasUgGrm($codigoUg, $codigoTaxa, $isTaxa);
    }

    private function getGruposTaxa($sequencialTaxa)
    {
        $taxaGrupoTaxasRepository = new TaxaGrupoTaxasRepository();
        $resultados = $taxaGrupoTaxasRepository->buscar(null, $sequencialTaxa);

        return array_map(function ($item) {
            return $item['ar57_grupotaxas'];
        }, $resultados);
    }
}
