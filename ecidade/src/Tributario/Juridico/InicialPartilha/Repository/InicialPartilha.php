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

namespace ECidade\Tributario\Juridico\InicialPartilha\Repository;

use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;

use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha as InicialPartilhaEntity;
use ECidade\Tributario\Juridico\InicialPartilha\Repository\InicialPartilhaCustas as CustasRepository;
use cl_inicialpartilha;
use DBException;
use DateTime;

/**
 * Class InicialPartilha
 *
 * @method static InicialPartilha getInstance()
 *
 * @author  Davi Busanello <davi@dbseller.com.br>
 */
class InicialPartilha extends \BaseClassRepository implements Interfaces\CalculaParcelamentoHonorario
{
    /**
     * @var InicialPartilhaEntity
     */
    protected static $oInstance;

    /**
     * @param InicialPartilhaEntity $oInicialPartilha
     * @return bool
     * @throws DBException
     */
    public function persist(InicialPartilhaEntity $oInicialPartilha)
    {
        $oDaoInicialPartilha = new cl_inicialpartilha();
        $iSequencial = $oInicialPartilha->getCodigo();

        $oDaoInicialPartilha->v35_inicial = $oInicialPartilha->getCodigoInicial();
        $oDaoInicialPartilha->v35_tipolancamento = $oInicialPartilha->getTipoLancamento();
        $oDaoInicialPartilha->v35_valorpartilha = $oInicialPartilha->getValorPartilha();
        $oDaoInicialPartilha->v35_obs = null;
        $oDaoInicialPartilha->v35_justificativa  = $oInicialPartilha->getJustificativa();
        if ($oInicialPartilha->getObservacao()) {
            $oDaoInicialPartilha->v35_obs = $oInicialPartilha->getObservacao();
        }

        $oDaoInicialPartilha->v35_dtpagamento = null;
        $oDataPagamento = $oInicialPartilha->getDataPagamento();
        if (!empty($oDataPagamento)) {
            $oDaoInicialPartilha->v35_dtpagamento = $oDataPagamento->format('Y-m-d');
        }

        $oDaoInicialPartilha->v35_datapartilha = null;
        if ($oInicialPartilha->getDataPartilha()) {
            $oDaoInicialPartilha->v35_datapartilha = $oInicialPartilha->getDataPartilha()->format('Y-m-d');
        }

        if (!empty($iSequencial)) {
            $oDaoInicialPartilha->v35_sequencial = $iSequencial;
            $lResult = $oDaoInicialPartilha->alterar($iSequencial);
        } else {
            $lResult = $oDaoInicialPartilha->incluir(null);
            $oInicialPartilha->setCodigo($oDaoInicialPartilha->v35_sequencial);
        }

        if (!$lResult) {
            $sMensagem = 'Ocorreu um erro ao ';
            $sMensagem .= (empty($iSequencial) ? 'incluir' : 'alterar');
            $sMensagem .= ' a partilha da inicial. ' . $oDaoInicialPartilha->erro_msg;
            throw new DBException($sMensagem);
        }

        $oInicialPartilha->setCodigo($oDaoInicialPartilha->v35_sequencial);
        if (count($oInicialPartilha->getCustas()) > 0) {
            foreach ($oInicialPartilha->getCustas() as $oCustas) {
                $oCustas->setInicialPartilha($oInicialPartilha);

                $oCustasRepository = CustasRepository::getInstance();
                $oCustasRepository->persist($oCustas);
            }
        }

        return true;
    }

    /**
     * @param $oDados
     * @return InicialPartilhaEntity|void|null
     * @throws DBException
     */
    protected function make($oDados)
    {
        if (empty($oDados)) {
            return null;
        }

        $oInicialPartilha = new InicialPartilhaEntity();
        $oInicialPartilha->setCodigo($oDados->v35_sequencial);
        $oInicialPartilha->setCodigoInicial($oDados->v35_inicial);
        $oInicialPartilha->setTipoLancamento($oDados->v35_tipolancamento);

        if (!empty($oDados->v35_dtpagamento)) {
            $oInicialPartilha->setDataPagamento(new DateTime($oDados->v35_dtpagamento));
        }

        $oInicialPartilha->setObservacao($oDados->v35_obs);
        $oInicialPartilha->setValorPartilha($oDados->v35_valorpartilha);

        if (!empty($oDados->v35_datapartilha)) {
            $oInicialPartilha->setDataPartilha(new DateTime($oDados->v35_datapartilha));
        }

        $oCustasRepository = CustasRepository::getInstance();
        $aCustas = $oCustasRepository->getByInicialPartilha($oDados->v35_sequencial);

        if (count($aCustas) > 0) {
            foreach ($aCustas as $oCustas) {
                $oInicialPartilha->addCustas($oCustas);
            }
        }

        return $oInicialPartilha;
    }

    /**
     * @param $rsResult
     * @return InicialPartilhaEntity[]
     */
    private function makeCollection($rsResult)
    {
        $aCollection = array();
        $aResult = pg_fetch_all($rsResult);

        if (empty($aResult)) {
            return array();
        }

        foreach ($aResult as $oResult) {
            $aCollection[] = $this->make((object) $oResult);
        }

        return $aCollection;
    }

    /**
     * @param int $iCodigo
     * @return InicialPartilhaEntity
     * @throws DBException
     */
    public function getByCodigo($iCodigo)
    {
        $oDao = new cl_inicialpartilha();
        $oDados = $oDao->findBydId($iCodigo);

        if (empty($oDados)) {
            throw new DBException("Houve uma falha ao buscar a Partilha com o código {$iCodigo}.");
        }

        return $this->make($oDados);
    }

    /**
     * Apaga a partilha
     * @param InicialPartilhaEntity $oPartilha
     * @return bool
     * @throws DBException
     */
    public function delete(InicialPartilhaEntity $oPartilha)
    {
        $oDao = new cl_inicialpartilha();
        $lResult = $oDao->excluir($oPartilha->getCodigo());

        if (!$lResult) {
            throw new DBException("Erro ao apagar a partilha {$oPartilha->getCodigo()}.");
        }

        return true;
    }

    /**
     * @param $iInicial
     * @return InicialPartilhaEntity[]|null
     * @throws DBException
     */
    public function getInicialPartilhaIsencao($iInicial)
    {
        $sSql  = " select distinct inicialpartilha.*                                               ";
        $sSql .= "   from inicialpartilha                                                          ";
        $sSql .= "        inner join inicialpartilhacustas on v36_inicialpartilha = v35_sequencial ";
        $sSql .= "  where inicialpartilha.v35_inicial = {$iInicial}                                ";
        $sSql .= "    and inicialpartilha.v35_tipolancamento = 3                                   ";
        $sSql .= "    and inicialpartilhacustas.v36_numnov is null                                 ";

        $rsResult = db_query($sSql);

        if (!$rsResult) {
            throw new DBException("Ocorreu um erro ao buscar as Partilhas da Inicial: {$iInicial}.");
        }

        return $this->makeCollection($rsResult);
    }

    /**
     * @param $iInicial
     * @return InicialPartilhaEntity[]|null
     * @throws DBException
     */
    public function getInicialPartilhaPago($iInicial)
    {
        $sSql  = " select distinct inicialpartilha.*                                               ";
        $sSql .= "   from inicialpartilha                                                          ";
        $sSql .= "        inner join inicialpartilhacustas on v36_inicialpartilha = v35_sequencial ";
        $sSql .= "  where inicialpartilha.v35_inicial = {$iInicial}                                ";
        $sSql .= "    and inicialpartilha.v35_tipolancamento = 1                                   ";
        $sSql .= "    and inicialpartilha.v35_dtpagamento is not null                              ";
        $sSql .= "    and inicialpartilhacustas.v36_numnov is not null                             ";

        $rsResult = db_query($sSql);

        if (!$rsResult) {
            throw new DBException("Ocorreu um erro ao buscar as Partilhas da Inicial: {$iInicial}.");
        }

        return $this->makeCollection($rsResult);
    }

    /**
     * @param $iCodigoInicial
     * @return InicialPartilhaEntity
     * @throws DBException
     */
    public function getUltimaByInicial($iCodigoInicial)
    {
        $sSql  = "SELECT * FROM inicialpartilha WHERE v35_inicial = {$iCodigoInicial} ";
        $sSql .= "ORDER BY v35_sequencial DESC LIMIT 1";
        $rsResult = db_query($sSql);

        if (!$rsResult) {
            throw new DBException("Ocorreu um erro ao buscar as Partilhas da Inicial: {$iCodigoInicial}.");
        }

        if (!pg_num_rows($rsResult)) {
            return null;
        }

        return $this->make(pg_fetch_object($rsResult, 0));
    }

    public function getParcelasPaga(\Taxa $taxa, $inicial)
    {
        $dao = new cl_inicialpartilha();
        $sql = $dao->sql_parcelas_pagas($taxa->getCodigoTaxa(), $inicial->getCodigo());

        $result = db_query($sql);

        if (!$result) {
            throw new DBException("Erro ao buscar as parcelas pagas dos honorários.");
        }

        return \db_utils::makeCollectionFromRecord($result, function ($parcela) {
            return $parcela->k00_numpar;
        });
    }

    public function getPagoSemHonorariosByInicial(Inicial $inicial)
    {
        $partilhas = $this->getInicialPartilhaPago($inicial->getCodigo());

        /**
         * @todo
         * Abstrair para um método da classe pai, para que as classes de partilha adm e júridica partilhem (xD) da
         * mesma lógica
         */
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

    /**
     * @param \Taxa $taxa
     * @param Inicial $inicial
     * @return float|int
     * @throws DBException
     */
    public function getValorPago(\Taxa $taxa, Inicial $inicial, \DateTime $data = null)
    {
        $sql  = "select v36_valor ";
        $sql .= "  from inicialpartilha ";
        $sql .= "       inner join inicialpartilhacustas on v36_inicialpartilha = v35_sequencial ";
        $sql .= " where v35_dtpagamento is not null ";
        $sql .= "   and v36_taxa = " . $taxa->getCodigoTaxa();
        $sql .= "   and v35_tipolancamento = " . TipoLancamento::PAGAMENTO;
        $sql .= "   and v35_inicial = " .  $inicial->getCodigo();

        if (!is_null($data)) {
            $sql .= "   and v35_dtpagamento < '{$data->format('Y-m-d')}'";
        }

        $rs = db_query($sql);

        if (empty($rs)) {
            throw new DBException('Erro ao buscar valor pago da partilha.');
        }

        if (pg_num_rows($rs) == 0) {
            return 0;
        }

        $resultado = \db_utils::makeCollectionFromRecord($rs, function ($taxa) {
            return round($taxa->v36_valor, 2);
        });

        return array_sum($resultado);
    }
}
