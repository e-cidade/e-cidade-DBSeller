<?php
/*
*     E-cidade Software Publico para Gestao Municipal
*  Copyright (C) 2017  DBselller Servicos de Informatica
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

use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use cl_processoforopartilha;
use DateTime;
use db_utils;
use DBException;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha as ProcessoForoPartilhaEntity;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilhaCusta
    as ProcessoForoPartilhaCustaRepository;

/**
 * Class ProcessoForoPartilha
 *
 * @method static ProcessoForoPartilha getInstance()
 */
class ProcessoForoPartilha extends \BaseClassRepository implements Interfaces\CalculaParcelamentoHonorario
{
    const TIPO_LANCAMENTO_PAGAMENTO_MANUAL = 2;

    const TIPO_LANCAMENTO_ISENCAO = 3;
    /**
     * @var ProcessoForoPartilha
     */
    protected static $oInstance;

    /**
     * @param ProcessoForoPartilhaEntity $oProcessoForoPartilha
     * @param bool $comCalculoCustas
     * @return bool
     * @throws DBException
     */
    public function persist(ProcessoForoPartilhaEntity $oProcessoForoPartilha, $comCalculoCustas = true)
    {
        $oDaoProcessoForoPartilha = new cl_processoforopartilha();
        $iSequencial = $oProcessoForoPartilha->getCodigo();

        $oDaoProcessoForoPartilha->v76_processoforo = $oProcessoForoPartilha->getCodigoProcessoForo();
        $oDaoProcessoForoPartilha->v76_tipolancamento = $oProcessoForoPartilha->getTipoLancamento();
        $oDaoProcessoForoPartilha->v76_valorpartilha = $oProcessoForoPartilha->getValorPartilha();

        $oDaoProcessoForoPartilha->v76_obs = null;
        if ($oProcessoForoPartilha->getObservacao()) {
            $oDaoProcessoForoPartilha->v76_obs = $oProcessoForoPartilha->getObservacao();
        }

        $oDaoProcessoForoPartilha->v76_dtpagamento = null;
        if ($oProcessoForoPartilha->getDataPagamento()) {
            $oDaoProcessoForoPartilha->v76_dtpagamento = $oProcessoForoPartilha->getDataPagamento()->format('Y-m-d');
        }

        $oDaoProcessoForoPartilha->v76_datapartilha = null;
        if ($oProcessoForoPartilha->getDataPartilha()) {
            $oDaoProcessoForoPartilha->v76_datapartilha = $oProcessoForoPartilha->getDataPartilha()->format('Y-m-d');
        }

        if (!empty($iSequencial)) {
            $oDaoProcessoForoPartilha->v76_sequencial = $iSequencial;
            $lResult = $oDaoProcessoForoPartilha->alterar($iSequencial);
        } else {
            $lResult = $oDaoProcessoForoPartilha->incluir(null);
            $oProcessoForoPartilha->setCodigo($oDaoProcessoForoPartilha->v76_sequencial);
        }

        if (!$lResult) {
            $sMensagem = 'Ocorreu um erro ao ';
            $sMensagem .= (empty($iSequencial) ? 'incluir' : 'alterar');
            $sMensagem .= ' a partilha do processo do foro. ' . $oDaoProcessoForoPartilha->erro_msg;
            throw new DBException($sMensagem);
        }

        $oProcessoForoPartilha->setCodigo($oDaoProcessoForoPartilha->v76_sequencial);

        if ($comCalculoCustas) {
            if (count($oProcessoForoPartilha->getCustas()) > 0) {
                foreach ($oProcessoForoPartilha->getCustas() as $oCustas) {
                    $oCustas->setProcessoForoPartilha($oProcessoForoPartilha);
                    $oProcessoForoPartilhaCustaRepository = ProcessoForoPartilhaCustaRepository::getInstance();
                    $oProcessoForoPartilhaCustaRepository->persist($oCustas);
                }
            }
        }

        return true;
    }

    /**
     * @param \stdClass $oDados
     * @return ProcessoForoPartilhaEntity|null
     */
    protected function make($oDados)
    {
        if (empty($oDados)) {
            return null;
        }

        $oProcessoForoPartilha = new ProcessoForoPartilhaEntity();
        $oProcessoForoPartilha->setCodigo($oDados->v76_sequencial);
        $oProcessoForoPartilha->setCodigoProcessoForo($oDados->v76_processoforo);
        $oProcessoForoPartilha->setTipoLancamento($oDados->v76_tipolancamento);
        $oProcessoForoPartilha->setDataPagamento(new DateTime($oDados->v76_dtpagamento));
        $oProcessoForoPartilha->setObservacao($oDados->v76_obs);
        $oProcessoForoPartilha->setValorPartilha($oDados->v76_valorpartilha);
        $oProcessoForoPartilha->setDataPartilha(new DateTime($oDados->v76_datapartilha));

        $oProcessoForoPartilhaCustaRepository = ProcessoForoPartilhaCustaRepository::getInstance();
        $aCustas = $oProcessoForoPartilhaCustaRepository->getByPartilha($oDados->v76_sequencial);

        if (count($aCustas) > 0) {
            foreach ($aCustas as $oCustas) {
                $oProcessoForoPartilha->addCustas($oCustas);
            }
        }

        return $oProcessoForoPartilha;
    }

    /**
     * @param $rsResult
     * @return ProcessoForoPartilhaEntity[]
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
     * @param ProcessoForoPartilhaEntity $oPartilha
     * @return bool
     * @throws DBException
     */
    public function delete(ProcessoForoPartilhaEntity $oPartilha)
    {
        $oDao = new cl_processoforopartilha();
        $lResult = $oDao->excluir($oPartilha->getCodigo());

        if (!$lResult) {
            throw new DBException("Erro ao apagar a Partilha de Processo do Foro {$oPartilha->getCodigo()}.");
        }

        return true;
    }

    /**
     * @param int $iCodigo
     * @return ProcessoForoPartilhaEntity
     * @throws DBException
     */
    public function getByCodigo($iCodigo)
    {
        $oDao = new cl_processoforopartilha();
        $sql = $oDao->sql_query_file($iCodigo);
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException("Houve uma falha ao buscar a Partilha do Processo do Foro com o código {$iCodigo}.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Não foi possível buscar a partilha.");
        }

        return $this->make(pg_fetch_object($rs, 0));
    }

    /**
     * @param $iProcessoForo
     * @return ProcessoForoPartilhaEntity[]
     * @throws DBException
     */
    public function getIsencaoByProcessoForoCodigo($iProcessoForo)
    {
        return $this->getPartilhaByTipoLancamento($iProcessoForo, TipoLancamento::ISENCAO);
    }

    /**
     * @param $iProcessoForo
     * @param $tipoLancamento
     * @return ProcessoForoPartilhaEntity[]
     * @throws DBException
     */
    private function getPartilhaByTipoLancamento($iProcessoForo, $tipoLancamento)
    {
        $sql = " select distinct processoforopartilha.* ";
        $sql .= "   from processoforopartilha ";
        $sql .= "        inner join processoforopartilhacusta on v77_processoforopartilha = v76_sequencial ";
        $sql .= "  where processoforopartilha.v76_processoforo = {$iProcessoForo} ";
        $sql .= "    and processoforopartilha.v76_tipolancamento = $tipoLancamento ";
        $sql .= "    and processoforopartilhacusta.v77_numnov = 0 ";

        $rs = db_query($sql);

        if (!$rs) {
            $erro = "Houve uma falha ao buscar a Partilha do Processo do Foro com o código {$iProcessoForo}.";
            throw new DBException($erro);
        }

        return $this->makeCollection($rs);
    }

    /**
     * @param $iProcessoForo
     * @return ProcessoForoPartilhaEntity[]
     * @throws DBException
     */
    public function getPagoByProcessoForoCodigo($iProcessoForo)
    {
        $sql  = " select distinct processoforopartilha.* ";
        $sql .= "   from processoforopartilha ";
        $sql .= "        inner join processoforopartilhacusta on v77_processoforopartilha = v76_sequencial ";
        $sql .= "  where processoforopartilha.v76_processoforo = {$iProcessoForo} ";
        $sql .= "    and (   (v76_tipolancamento = 2 and v77_numnov = 0) ";
        $sql .= "         or (v76_tipolancamento = 1 and v76_dtpagamento is not null)) ";

        $rs = db_query($sql);

        if (!$rs) {
            $erro = "Houve uma falha ao buscar a Partilha do Processo do Foro com o código {$iProcessoForo}.";
            throw new DBException($erro);
        }

        return $this->makeCollection($rs);
    }

    /**
     * @param $iProcessoForo
     * @return ProcessoForoPartilhaEntity[]
     * @throws DBException
     */
    public function getPagoManualByProcessoForoCodigo($iProcessoForo)
    {
        return $this->getPartilhaByTipoLancamento($iProcessoForo, TipoLancamento::PAGAMENTO_MANUAL);
    }

    public function getPagoManualByNumnov($numnov)
    {
        $dao = new cl_processoforopartilha();
        $sql = $dao->sql_partilhas_pagas_by_numnov($numnov);

        $result = db_query($sql);

        if (!$result) {
            throw new \Exception('Erro ao consultar as partilhas pelo numnov: ' . $numnov);
        }

        if (!pg_num_rows($result)) {
            return array();
        }

        return db_utils::getCollectionByRecord($result);
    }

    /**
     * @param $iNumnov
     * @param integer|null $codigoProcesso
     * @return \stdClass[]
     * @throws DBException
     */
    public function getDadosRecibo($iNumnov, $codigoProcesso = null)
    {
        $oDao = new cl_processoforopartilha();
        $sSql = $oDao->sql_query_recibo_custas($iNumnov, $codigoProcesso);

        $rsResult = db_query($sSql);

        if (!$rsResult) {
            throw new DBException("Erro ao buscar os dados de custas do recibo.");
        }

        return db_utils::getCollectionByRecord($rsResult);
    }

    /**
     * Busca as Partilhas de um processo que ainda não foi gerado o recibo
     *
     * @param integer $processo
     *
     * @return array|ProcessoForoPartilhaEntity
     *
     * @throws DBException
     * @throws \Exception
     */
    public function getPartilhaByProcessoSemRecibo($processo)
    {
        if (empty($processo)) {
            throw new \Exception("Processo não informado.");
        }
        $sql = "select distinct processoforopartilha.* ";
        $sql .= "  from processoforopartilha ";
        $sql .= "  join processoforopartilhacusta on v77_processoforopartilha = v76_sequencial ";
        $sql .= " where v76_processoforo = {$processo}";
        $sql .= " and v76_dtpagamento is not null and v76_tipolancamento = 1";


        $rs = db_query($sql);
        if (!$rs) {
            throw new DBException("Erro ao buscar partilhas do processo.");
        }

        if (pg_num_rows($rs) == 0) {
            return array();
        }

        $partilhas = $this->makeCollection($rs);

        return $partilhas;
    }

    public function getPagoSemHonorariosByProcessoForo(ProcessoForo $processo)
    {
        $partilhas = $this->getPagoByProcessoForoCodigo($processo->getCodigo());

        foreach ($partilhas as $indice => $partilha) {
            $custas = $partilha->getCustas();
            $partilha->resetCustas();

            foreach ($custas as $custa) {
                if (!$custa->getTaxa()->isAplicaHonorario()) {
                    $partilha->addCustas($custa);
                }
            }

            $custas = $partilha->getCustas();

            if (empty($custas)) {
                unset($partilhas[$indice]);
            }
        }

        return $partilhas;
    }

    public function getParcelasPaga(\Taxa $taxa, $processoForo)
    {
        $dao = new cl_processoforopartilha();
        $sql = $dao->sql_parcelas_pagas($taxa->getCodigoTaxa(), $processoForo->getCodigo());

        $result = db_query($sql);

        if (!$result) {
            throw new DBException("Erro ao buscar as parcelas pagas dos honorários.");
        }

        return \db_utils::makeCollectionFromRecord($result, function ($parcela) {
            return $parcela->k00_numpar;
        });
    }

    /**
     * @param $processoForo | $codigoTaxa
     * @return bool
     * @throws DBException
     */
    public function getHonorarioPagoProcessoForo($processoForo, $codigoTaxa)
    {
        $sql = "SELECT ap.*
                  FROM processoforo pf
                  JOIN processoforoinicial pfi
                    ON pfi.v71_processoforo = pf.v70_sequencial
                  JOIN termoini ti
                    ON ti.inicial           = pfi.v71_inicial
                  JOIN termo t
                    ON t.v07_parcel         = ti.parcel
                  JOIN arrepaga ap
                    ON ap.k00_numpre        = t.v07_numpre 
                  JOIN taxa tx
                    ON tx.ar36_receita      = ap.k00_receit
                 WHERE pf.v70_sequencial  = {$processoForo}
                   AND tx.ar36_honorario  = 't'
                   AND tx.ar36_sequencial = {$codigoTaxa};";

        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException('Erro ao verificar os honorários pagos.');
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        return true;
    }

    /**
     * @param Taxa $taxa
     * @param ProcessoForo $processoForo
     * @return mixed
     * @throws DBException
     */
    public function getValorPago(\Taxa $taxa, ProcessoForo $processoForo, \DateTime $data = null)
    {
        $sql  = "select v77_valor ";
        $sql .= "  from processoforopartilha ";
        $sql .= "       inner join processoforopartilhacusta on v77_processoforopartilha = v76_sequencial ";
        $sql .= " where v76_dtpagamento is not null ";
        $sql .= "   and v77_taxa = " . $taxa->getCodigoTaxa();
        $sql .= "   and v76_tipolancamento = " . TipoLancamento::PAGAMENTO;
        $sql .= "   and v76_processoforo = " .  $processoForo->getCodigo();

        if (!is_null($data)) {
            $sql .= "   and v76_dtpagamento < '{$data->format('Y-m-d')}'";
        }

        $rs = db_query($sql);

        if (empty($rs)) {
            throw new DBException('Erro ao buscar valor pago da partilha.');
        }

        if (pg_num_rows($rs) == 0) {
            return 0;
        }

        $resultado = \db_utils::makeCollectionFromRecord($rs, function ($taxa) {
            return round($taxa->v77_valor, 2);
        });

        return array_sum($resultado);
    }

    /**
     * @param \Taxa $taxa
     * @param ProcessoForo $processoForo
     * @return bool
     * @throws DBException
     */
    public function hasPagamentoInicial(\Taxa $taxa, ProcessoForo $processoForo)
    {
        $dao = new cl_processoforopartilha();
        $sql = $dao->sql_parcelas_pagas_inicial($taxa->getCodigoTaxa(), $processoForo->getCodigo());

        $result = db_query($sql);

        if (!$result) {
            throw new DBException("Erro ao buscar os pagamentos de taxas por inicial.");
        }

        if (pg_num_rows($result) == 0) {
            return false;
        }

        return true;
    }
}
