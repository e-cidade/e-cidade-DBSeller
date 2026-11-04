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

/**
 * Classe representa a folha de pagamento do complementar
 *
 * @author $Author: dbfabio.egidio $
 * @version $Revision: 1.24 $
 */
class FolhaPagamentoComplementar extends FolhaPagamento
{
    const MENSAGENS = 'recursoshumanos.pessoal.FolhaPagamentoComplementar.';

    /**
     * FolhaPagamentoComplementar constructor.
     * @param null $iSequencial
     * @throws BusinessException
     * @throws DBException
     */
    function __construct($iSequencial = null)
    {
        parent::__construct($iSequencial, FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR);
    }

    /**
     * @param DBCompetencia|null $oCompetencia
     * @return bool|FolhaPagamentoComplementar
     * @throws BusinessException
     * @throws DBException
     */
    public static function getFolhaAberta(DBCompetencia $oCompetencia = null)
    {
        $iCodigoFolha = FolhaPagamento::getCodigoFolha(FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR, true, $oCompetencia);

        if ($iCodigoFolha) {
            return new FolhaPagamentoComplementar($iCodigoFolha);
        }

        return false;
    }

    /**
     * Retorna se há uma folha aberta
     *
     * Validação de parâmetros existente devido a implementação inicial do método, que não seguiu a assinatura da classe
     * pai. Em virtude do impacto na quantidade de rotinas que já utilizam, foi utilizada esta solução.
     *
     * @param int $tipoFolha
     * @param DBCompetencia|null $oCompetencia
     * @return bool
     * @throws DBException
     */
    public static function hasFolhaAberta(
      $tipoFolha = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR,
      DBCompetencia $oCompetencia = null
    ) {
        $tipoFolhaPagamento = $tipoFolha;
        $competencia = $oCompetencia;

        if ($tipoFolha instanceof DBCompetencia) {
            $tipoFolhaPagamento = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR;
            $competencia = $tipoFolha;
        }

        return FolhaPagamento::hasFolhaAberta($tipoFolhaPagamento, $competencia);
    }

    /**
     * Verifica se existe algum registro do tipo folha salario na
     * competencia passada por parametro ou caso não seja passado
     * pega a competencia atual
     *
     * @param DBCompetencia|null $oCompetencia
     * @return bool
     * @throws Exception
     */
    public static function hasFolha(DBCompetencia $oCompetencia = null)
    {
        if ($oCompetencia) {
            return FolhaPagamento::hasFolhaTipo(FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR, $oCompetencia);
        }

        return FolhaPagamento::hasFolhaTipo(FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR,
          new DBCompetencia(DBPessoal::getAnoFolha(), DBPessoal::getMesFolha())
        );
    }

    /**
     * @param DBCompetencia|null $oCompetencia
     * @return FolhaPagamentoComplementar
     * @throws BusinessException
     * @throws DBException
     */
    public static function getUltimaFolha(DBCompetencia $oCompetencia = null)
    {
        return new FolhaPagamentoComplementar(
          FolhaPagamento::getCodigoFolha(
            FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR,
            null,
            $oCompetencia
          )
        );
    }

    /**
     * Retorna o ultimo número unico da folha pagamento, conforme o tipo passado.
     * @param int $tipoFolha
     * @return int
     * @throws DBException
     */
    public static function getProximoNumero($tipoFolha = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR)
    {
        return FolhaPagamento::getProximoNumero($tipoFolha);
    }

    /**
     * Função que verifica se existe pelo menos um registro no
     * ponto de complementar na competência e instituição atual.
     * @return bool
     * @throws DBException
     */
    public function pesquisarPonto()
    {
        /**
         * Verifica se existe pelo menos um registro
         * para a folha complementar.
         */
        $oDaoPontoCom = new cl_pontocom();

        $sWherePonto = "    r47_anousu  = {$this->getCompetencia()->getAno()}";
        $sWherePonto .= " and r47_mesusu = {$this->getCompetencia()->getMes()}";
        $sWherePonto .= " and r47_instit = {$this->getInstituicao()->getSequencial()}";

        $sSqlPontoCom = $oDaoPontoCom->sql_query_file(
          null,
          null,
          null,
          null,
          "distinct r47_regist",
          null,
          $sWherePonto
        );
        $rsPontoCom = db_query($sSqlPontoCom);

        if (!$rsPontoCom) {
            throw new DBException(_M(self::MENSAGENS . "erro_ponto"));
        }

        if (pg_num_rows($rsPontoCom) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Função para fechamento da folha, antes de realizar o fechamento verifica
     * se existe pelo menos um registro no ponto de complementar na competência e
     * instituição atual.
     *
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    public function fechar()
    {
        /**
         * Verifica se a folha esta aberta
         */
        if (!$this->isAberto()) {
            throw new DBException(_M(self::MENSAGENS . "fechamento_folha_fechada"));
        }

        /**
         * Verifica se existe pelo menos um registro
         * para a folha complementar.
         */
        $aServidoresPontoCom = ServidorRepository::getServidoresNoPontoPorFolhaPagamento($this);

        if (count($aServidoresPontoCom) == 0) {
            throw new BusinessException(_M(self::MENSAGENS . "sem_registro_ponto"));
        }

        /**
         * Remove os pontos lançados para a folha atual
         */
        $oDaoPontoCom = new cl_pontocom;
        $sWherePontoCom = "     r47_anousu = {$this->getCompetencia()->getAno()}";
        $sWherePontoCom .= " and r47_mesusu = {$this->getCompetencia()->getMes()}";
        $sWherePontoCom .= " and r47_instit = {$this->getInstituicao()->getSequencial()}";
        $oDaoPontoCom->excluir(null, null, null, null, $sWherePontoCom);

        /**
         * Faz update no semest para ficar com o
         * numero atual da folha de pagamento.
         */
        $oDaoGerfCom = new cl_gerfcom();
        $oDaoGerfCom->r48_anousu = $this->getCompetencia()->getAno();
        $oDaoGerfCom->r48_mesusu = $this->getCompetencia()->getMes();
        $oDaoGerfCom->r48_semest = $this->getNumero();

        $oDaoGerfCom->alterar($this->getCompetencia()->getAno(), $this->getCompetencia()->getMes());

        $this->fecharFolha();

        return true;
    }

    /**
     * Função abstrata para cancelamento da abertura da folha.
     * - Verifica se a folha esta aberta
     * - Remove os calculos lançados para a folha atual.
     * - Remove os pontos lançados para a folha atual.
     *
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    public function cancelarAbertura()
    {
        /**
         * Verifica se a folha esta aberta.
         */
        if (!$this->isAberto()) {
            throw new BusinessException(_M(self::MENSAGENS . 'folha_fechada'));
        }

        $oDaoAssentaLoteregistroponto = new cl_assentaloteregistroponto;

        /**
         * Recupera os lotes vinculados a esta folha para removê-los
         */
        $oDaoLoteregistropontoRhfolhapagamento = new cl_loteregistropontorhfolhapagamento;
        $sWhereLoteregistropontoRhfolhapagamento = " rh162_rhfolhapagamento = " . $this->getSequencial();
        $sSqlLoteregistropontoRhfolhapagamento = $oDaoLoteregistropontoRhfolhapagamento->sql_query_file(null,
          "rh162_sequencial, rh162_loteregistroponto", null, $sWhereLoteregistropontoRhfolhapagamento);
        $rsLoteregistropontoRhfolhapagamento = db_query($sSqlLoteregistropontoRhfolhapagamento);

        if (is_resource($rsLoteregistropontoRhfolhapagamento) && pg_num_rows($rsLoteregistropontoRhfolhapagamento) > 0) {
            for ($iIndLotesregistroponto = 0; $iIndLotesregistroponto < pg_num_rows($rsLoteregistropontoRhfolhapagamento); $iIndLotesregistroponto++) {
                /**
                 * Remove a ligação entre lote e a folha de pagamento
                 */
                $oDaoLoteregistropontoRhfolhapagamento->excluir(db_utils::fieldsMemory($rsLoteregistropontoRhfolhapagamento,
                  $iIndLotesregistroponto)->rh162_sequencial);

                /**
                 * Remove a ligação entre o lote e um assentamento de substituição
                 */
                $oDaoAssentaLoteregistroponto->excluir(null,
                  "rh160_loteregistroponto = " . db_utils::fieldsMemory($rsLoteregistropontoRhfolhapagamento,
                    $iIndLotesregistroponto)->rh162_loteregistroponto);

                /**
                 * Remove os lotes de registros vinculados a esta folha
                 */
                LoteRegistrosPontoRepository::remover(LoteRegistrosPontoRepository::getInstanceByCodigo(db_utils::fieldsMemory($rsLoteregistropontoRhfolhapagamento,
                  $iIndLotesregistroponto)->rh162_loteregistroponto));
            }
        }

        /**
         * Remove os calculos lançados para a folha atual
         */
        $oDaoGerfCom = new cl_gerfcom();

        $sWhereGerfCom = "     r48_anousu = {$this->getCompetencia()->getAno()}";
        $sWhereGerfCom .= " and r48_mesusu = {$this->getCompetencia()->getMes()}";
        $sWhereGerfCom .= " and r48_instit = {$this->getInstituicao()->getSequencial()}";

        $oDaoGerfCom->excluir(null, null, null, null, $sWhereGerfCom);

        if ($oDaoGerfCom->erro_status == "0") {
            throw new DBException(_M(self::MENSAGENS . 'erro_excluir_gerfcom'));
        }

        /**
         * Remove os pontos lançados para a folha atual
         */
        $oDaoPontoCom = new cl_pontocom;

        $sWherePontoCom = "     r47_anousu = {$this->getCompetencia()->getAno()}";
        $sWherePontoCom .= " and r47_mesusu = {$this->getCompetencia()->getMes()}";
        $sWherePontoCom .= " and r47_instit = {$this->getInstituicao()->getSequencial()}";

        $oDaoPontoCom->excluir(null, null, null, null, $sWherePontoCom);

        if ($oDaoPontoCom->erro_status == "0") {
            throw new DBException(_M(self::MENSAGENS . 'erro_excluir_pontocom'));
        }

        $this->excluir();

        $aFolhasAnteriores = FolhaPagamentoComplementar::getFolhasFechadasCompetencia(DBPessoal::getCompetenciaFolha());

        foreach ($aFolhasAnteriores as $oFolhaAnterior) {
            if (!!$oFolhaAnterior->getSequencial()) {
                $oFolhaAnterior->retornarCalculo();

                $oDaoGerfCom = new cl_gerfcom();
                $oDaoGerfCom->r48_anousu = $oFolhaAnterior->getCompetencia()->getAno();
                $oDaoGerfCom->r48_mesusu = $oFolhaAnterior->getCompetencia()->getMes();
                $oDaoGerfCom->r48_semest = $oFolhaAnterior->getNumero();

                $oDaoGerfCom->alterar($oFolhaAnterior->getCompetencia()->getAno(),
                  $oFolhaAnterior->getCompetencia()->getMes(), null, null, 0);
            }
        }

        return true;
    }

    /**
     * Este função é sobrecarga
     *
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    public function cancelarFechamento()
    {
        parent::cancelarFechamento();

        $oDaoGerfCom = new cl_gerfcom();
        $oDaoGerfCom->r48_anousu = $this->getCompetencia()->getAno();
        $oDaoGerfCom->r48_mesusu = $this->getCompetencia()->getMes();
        $oDaoGerfCom->r48_semest = "0";
        $oDaoGerfCom->alterar($this->getCompetencia()->getAno(), $this->getCompetencia()->getMes());

        if ($oDaoGerfCom->erro_status == "0") {
            throw new DBException($oDaoGerfCom->erro_msg);
        }

        return true;
    }

    /**
     * Retorna todas as folhas complementares fechadas na compentência
     * @param DBCompetencia $oCompetencia
     * @param int|null $tipoFolha
     * @return array
     */
    public static function getFolhasFechadasCompetencia(
      DBCompetencia $oCompetencia,
      $tipoFolha = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR
    ) {
        return FolhaPagamento::getFolhasFechadasCompetencia($oCompetencia, $tipoFolha);
    }
}
