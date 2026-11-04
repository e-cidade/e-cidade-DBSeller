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

namespace ECidade\Tributario\Juridico\ProcessoForo\Repository;

use cl_processoforo;
use DateTime;
use DBException;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo as ProcessoForoEntity;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;
use ECidade\Tributario\Juridico\Repository\HonorariosParcelamento as HonorariosParcelamentoRepository;

/**
 * Repository para operações nos processos.
 *
 * @method static ProcessoForo getInstance()
 */
class ProcessoForo extends \BaseClassRepository
{
    /** @var bool */
    private $returnFullItem;

    /**
     * @var ProcessoForo
     */
    protected static $oInstance;

    /**
     * @param ProcessoForoEntity $oProcessoForo
     * @return bool
     * @throws DBException
     */
    public function persist(ProcessoForoEntity $oProcessoForo)
    {
        $oDaoProcessoForo = new cl_processoforo();
        $iSequencial = $oProcessoForo->getCodigo();

        $oDaoProcessoForo->v70_codforo = $oProcessoForo->getCodigoProcessoForo();
        $oDaoProcessoForo->v70_processoforomov = $oProcessoForo->getCodigoProcessoForoMov();
        $oDaoProcessoForo->v70_id_usuario = $oProcessoForo->getCodigoUsuario();
        $oDaoProcessoForo->v70_vara = $oProcessoForo->getCodigoVara();

        $oDaoProcessoForo->v70_data = null;
        if ($oProcessoForo->getData()) {
            $oDaoProcessoForo->v70_data = $oProcessoForo->getData()->format('Y-m-d');
        }

        $oDaoProcessoForo->v70_valorinicial = $oProcessoForo->getValorInicial();
        $oDaoProcessoForo->v70_observacao = $oProcessoForo->getObservacao();
        $oDaoProcessoForo->v70_anulado = $oProcessoForo->getAnulado();
        $oDaoProcessoForo->v70_instit = $oProcessoForo->getInstit();
        $oDaoProcessoForo->v70_cartorio = $oProcessoForo->getCodigoCartorio();

        if (!empty($iSequencial)) {
            $oDaoProcessoForo->v70_sequencial = $iSequencial;
            $lResult = $oDaoProcessoForo->alterar($iSequencial);
        } else {
            $lResult = $oDaoProcessoForo->incluir(null);
            $oProcessoForo->setCodigo($oDaoProcessoForo->v70_sequencial);
        }

        if (!$lResult) {
            $sMensagem = 'Ocorreu um erro ao ';
            $sMensagem .= (empty($iSequencial) ? 'incluir' : 'alterar');
            $sMensagem .= ' o processo do foro. ' . $oDaoProcessoForo->erro_msg;
            throw new DBException($sMensagem);
        }

        return true;
    }

    /**
     * @param \stdClass $oDados
     * @return ProcessoForoEntity
     */
    protected function make($oDados)
    {
        $oProcessoForo = new ProcessoForoEntity();
        $oProcessoForo->setCodigo($oDados->v70_sequencial);
        $oProcessoForo->setCodigoForo($oDados->v70_codforo);
        $oProcessoForo->setCodigoProcessoForoMov($oDados->v70_processoforomov);
        $oProcessoForo->setCodigoUsuario($oDados->v70_id_usuario);
        $oProcessoForo->setCodigoVara($oDados->v70_vara);
        $oProcessoForo->setData(new DateTime($oDados->v70_data));
        $oProcessoForo->setValorInicial($oDados->v70_valorinicial);
        $oProcessoForo->setObservacao($oDados->v70_observacao);
        $oProcessoForo->setAnulado($oDados->v70_anulado);
        $oProcessoForo->setInstit($oDados->v70_instit);
        $oProcessoForo->setCodigoCartorio($oDados->v70_cartorio);

        if ($this->isReturnFullItem()) {
            $inicialRepository = InicialRepository::getInstance();
            $inicialRepository->setReturnFullItem(true);

            $iniciais = $inicialRepository->getByProcessoForo($oDados->v70_sequencial);

            if (!empty($iniciais)) {
                $oProcessoForo->setIniciais($iniciais);
            }

            $honorariosParcelamentoRepository = HonorariosParcelamentoRepository::getInstance();
            $parcelas = $honorariosParcelamentoRepository->getByProcessoForo($oProcessoForo);
            $oProcessoForo->setParcelasHonorarios($parcelas);
        }

        return $oProcessoForo;
    }


    /**
     * @param $rsResult
     * @return ProcessoForoEntity[]
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
     * @param ProcessoForoEntity $oProcessoForo
     * @return bool
     * @throws DBException
     */
    public function delete(ProcessoForoEntity $oProcessoForo)
    {
        $oDao = new cl_processoforo();
        $lResult = $oDao->excluir($oProcessoForo->getCodigo());

        if (!$lResult) {
            throw new DBException("Erro ao apagar o Processo do Foro {$oProcessoForo->getCodigo()}.");
        }

        return true;
    }

    /**
     * @param int $iCodigo
     * @return ProcessoForoEntity
     * @throws DBException
     */
    public function getByCodigo($iCodigo)
    {
        $dao = new cl_processoforo();
        $sql = $dao->sql_query_file($iCodigo);
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar processo foro.");
        }

        $oDados = pg_fetch_object($rs, 0);
        if (empty($oDados)) {
            throw new DBException("Houve uma falha ao buscar o Processo do Foro com o código {$iCodigo}.");
        }

        return $this->make($oDados);
    }

    /**
     * @param int $iCodigoInicial
     * @return ProcessoForoEntity
     * @throws DBException
     */
    public function getByInicial($iCodigoInicial)
    {
        $sWhere = "processoforo.v70_sequencial = (select v71_processoforo from processoforoinicial ";
        $sWhere .= " where v71_inicial = {$iCodigoInicial} and v71_anulado is false)";
        $sWhere .= " and processoforo.v70_anulado is false ";

        $oDao = new cl_processoforo();
        $sSql = $oDao->sql_query_file(null, "*", null, $sWhere);

        $rsResult = db_query($sSql);

        if (!$rsResult) {
            throw new DBException("Ocorreu um erro ao buscar o Processo do Foro da inicial {$iCodigoInicial}.");
        }

        if (!pg_num_rows($rsResult)) {
            return null;
        }

        return $this->make(pg_fetch_object($rsResult, 0));
    }

    /**
     * @return bool
     */
    public function isReturnFullItem()
    {
        return $this->returnFullItem;
    }

    /**
     * @param bool $returnFullItem
     * @return ProcessoForo
     */
    public function setReturnFullItem($returnFullItem)
    {
        $this->returnFullItem = $returnFullItem;
        return $this;
    }
    /**
     * Função que verifica se alguma das iniciais passadas pertence a migração e ja foi paga.
     * @param array
     * @return bool
     **/
    public function verificaProcessoMigracaoPago($aIniciais)
    {
        if (empty($aIniciais)) {
            return false;
        }

        $sIniciais = implode(',', $aIniciais);

        $sql = "SELECT v70_sequencial 
                  FROM processoforo 
                  JOIN processoforoinicial 
                    ON v71_processoforo = v70_sequencial 
                 WHERE v71_inicial IN ({$sIniciais}) 
                   AND (v70_observacao ilike '%MIGRAÇÃO%' 
                        OR
                        v70_observacao ilike '%MIGRACAO%')";

        $rs = db_query($sql);

        /**
         * Se Processo de migração
         **/
        if (pg_num_rows($rs) > 0) {
            $arrProcessos = array_map(function ($var) {
                return $var["v70_sequencial"];
            }, pg_fetch_all($rs));

            $strProcessos = implode(',', $arrProcessos);

            $sql = "SELECT DISTINCT arrepaga.k00_numpre
                               FROM arrepaga
                               JOIN recibopaga
                                 ON arrepaga.k00_numpre = recibopaga.k00_numpre
                               JOIN processoforopartilhacusta
                                 ON v77_numnov = recibopaga.k00_numnov
                               JOIN processoforopartilha
                                 ON v77_processoforopartilha = v76_sequencial
                              WHERE v76_processoforo IN ($strProcessos)
                                AND (v76_obs ilike '%MIGRAÇÃO%'
                                     OR 
                                     v76_obs ilike '%MIGRACAO%');";

            $rs = db_query($sql);

            if (pg_num_rows($rs) > 0) {
                return true;
            }
        }

        return false;
    }

    public function isParcelamento($codigoProcessoForo)
    {
        $condicao  = "processoforo.v70_sequencial = {$codigoProcessoForo}";
        $condicao .= "AND exists (SELECT 1
                               FROM processoforo pf
                               JOIN processoforoinicial pfi
                                 ON pfi.v71_processoforo = pf.v70_sequencial
                               JOIN termoini ti
                                 ON ti.inicial = pfi.v71_inicial
                               JOIN termo t
                                 ON t.v07_parcel = ti.parcel
                               LEFT JOIN termoanu ta 
                                 ON ta.v09_parcel = t.v07_parcel
                              WHERE pf.v70_sequencial = {$codigoProcessoForo}
                                AND ta.v09_parcel is null )";

        $dao = new \cl_processoforo();
        $sql = $dao->sql_query(null, '*', null, $condicao);
        $rs  = db_query($sql);

        if (!$rs) {
            throw new DBException('Erro ao validar se é um parcelamento.');
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        return true;
    }
}
