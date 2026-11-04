<?php
/*
*     E-cidade Software Publico para Gestao Municipal
*  Copyright (C) 2009  DBSeller Servicos de Informatica
*                            www.dbseller.com.br
*                         e-cidade@dbseller.com.br
*
*  Este programa e software livre; voce pode redistribui-lo e/ou
*  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
*  publicada pela Free Software Foundation; tanto a versao 2 da
*  Licenca como (a seu criterio) qualquer versao mais nova.
*
*  Este programa e distribuido na expectativa de ser util, mas SEM
*  QUALQUER GARANTIA; sem mesmo a garantia implicita de
*  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
*  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
*  detalhes.
*
*  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
*  junto com este programa; se nao, escreva para a Free Software
*  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
*  02111-1307, USA.
*
*  Copia da licenca no diretorio licenca/licenca_en.txt
*                                licenca/licenca_pt.txt
*/

namespace ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository;

use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha as ProcessoForoPartilhaEntity;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta as ProcessoForoPartilhaCustasEntity;
use ECidade\Tributario\Arrecadacao\Repository\Taxa as TaxaRepository;
use cl_processoforopartilhacusta;
use db_utils;
use DBException;

class ProcessoForoPartilhaCusta extends \BaseClassRepository
{

    /**
     * @var ProcessoForoPartilhaCustas
     */
    protected static $oInstance;

    /**
     * @param ProcessoForoPartilhaCustasEntity $oCustas
     * @return bool
     * @throws DBException
     */
    public function persist(ProcessoForoPartilhaCustasEntity $oCustas)
    {
        $oDaoCustas = new cl_processoforopartilhacusta();
        $iSequencial = $oCustas->getCodigo();

        $oDaoCustas->v77_taxa = $oCustas->getCodigoTaxa();
        $oDaoCustas->v77_processoforopartilha = $oCustas->getCodigoProcessoForoPartilha();

        $oProcessoForoPartilha = $oCustas->getProcessoForoPartilha();

        if (!empty($oProcessoForoPartilha)) {
            $oDaoCustas->v77_processoforopartilha = $oProcessoForoPartilha->getCodigo();
        }

        $oDaoCustas->v77_valor = $oCustas->getValor();
        $oDaoCustas->v77_numnov = $oCustas->getNumnov();
        $oDaoCustas->v77_dispensalancamentorecibo = ($oCustas->isDispensaLancamentoRecibo() ? 't' : 'f');

        if (!empty($iSequencial)) {
            $oDaoCustas->v77_sequencial = $iSequencial;
            $lResult = $oDaoCustas->alterar($iSequencial);
        } else {
            if (db_utils::ativaParcelamentoCustas()) {
                $this->deletarCustaPartilha($oDaoCustas->v77_taxa, $oDaoCustas->v77_processoforopartilha);
            }
            $lResult = $oDaoCustas->incluir(null);
            $oCustas->setCodigo($oDaoCustas->v77_sequencial);
        }

        if (!$lResult) {
            $sMensagem  = 'Ocorreu um erro ao ';
            $sMensagem .= (empty($iSequencial) ? 'incluir' : 'alterar');
            $sMensagem .= ' a custas do processo do foro. ' . $oDaoCustas->erro_msg;
            throw new DBException($sMensagem);
        }

        return true;
    }

    private function deletarCustaPartilha($sequencialTaxa, $sequencialProcesso)
    {
        $oDaoPartilhaCusta = new cl_processoforopartilhacusta();
        $oDaoPartilhaCusta->excluir(null, "v77_taxa = $sequencialTaxa 
        and v77_processoforopartilha = $sequencialProcesso");
    }

    /**
     * @param \stdClass $oDados
     * @return ProcessoForoPartilhaCustasEntity|null
     */
    protected function make($oDados)
    {
        if (empty($oDados)) {
            return null;
        }

        $oCustas = new ProcessoForoPartilhaCustasEntity();
        $oCustas->setCodigo($oDados->v77_sequencial);
        $oCustas->setCodigoTaxa($oDados->v77_taxa);
        $oCustas->setCodigoProcessoForoPartilha($oDados->v77_processoforopartilha);
        $oCustas->setValor($oDados->v77_valor);
        $oCustas->setNumnov($oDados->v77_numnov);
        $oCustas->setDispensaLancamentoRecibo($oDados->v77_dispensalancamentorecibo == 't');

        $oTaxaRepository = TaxaRepository::getInstance();
        $oTaxa = $oTaxaRepository->getByCodigo($oDados->v77_taxa);
        $oCustas->setTaxa($oTaxa);

        return $oCustas;
    }

    /**
     * Monta uma collection
     * @param $rsResult
     * @return ProcessoForoPartilhaCustasEntity[]
     */
    private function makeCollection($rsResult)
    {
        $aCollection = array();
        $aResult = pg_fetch_all($rsResult);

        if (empty($aResult)) {
            return array();
        }

        foreach ($aResult as $oResult) {
            $aCollection[] = $this->make((object)$oResult);
        }

        return $aCollection;
    }

    /**
     * @param ProcessoForoPartilhaCustasEntity $oCustas
     * @return bool
     * @throws DBException
     */
    public function delete(ProcessoForoPartilhaCustasEntity $oCustas)
    {
        $oDao = new cl_processoforopartilhacusta();
        $lResult = $oDao->excluir($oCustas->getCodigo());

        if (!$lResult) {
            $sMsg = "Erro ao apagar a custas da taxa {$oCustas->getCodigoTaxa()} da partilha de processo ";
            $sMsg .= "do foro {$oCustas->getCodigoInicialPartilha()}";
            throw new DBException($sMsg);
        }

        return true;
    }

    /**
     * @param $iCodigoProcessoForoPartilha, $iCodigoTaxa, $iNumnov
     * @return object|null
     * @throws DBException
     */
    public function getByProcessoForoTaxa($iCodigoProcessoForoPartilha, $iCodigoTaxa, $iNumnov)
    {
        $oDao = new cl_processoforopartilhacusta();

        $sWhere = "v77_taxa = {$iCodigoTaxa} and v77_processoforopartilha = {$iCodigoProcessoForoPartilha} and ";
        $sWhere .= "v77_numnov = {$iNumnov}";
        $sSql = $oDao->sql_query_file(
            null,
            "*",
            null,
            $sWhere
        );

        $rsResult = db_query($sSql);

        if (!$rsResult) {
            $sMsg = "Ocorreu um erro ao buscar informações de Custas presentes na Partilha do Processo do Foro.";
            throw new DBException($sMsg);
        }

        if (pg_num_rows($rsResult) == 0) {
            return null;
        }

        return $this->make(pg_fetch_object($rsResult, 0));
    }

    /**
     * Busca as custas vínculadas a uma partilha
     *
     * @param integer $partilha
     *
     * @return array|ProcessoForoPartilhaCustasEntity[]

     * @throws \Exception
     */
    public function getByPartilha($partilha)
    {
        $dao = new cl_processoforopartilhacusta();
        $sql = $dao->sql_query_file(
            null,
            "*",
            null,
            "v77_processoforopartilha = {$partilha}"
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Erro ao buscar custas da partilha.");
        }

        if (pg_num_rows($rs) == 0) {
            return array();
        }

        return $this->makeCollection($rs);
    }
}
