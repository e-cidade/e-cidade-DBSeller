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
 * Classe para autenticar planilha de arrecadação
 * @author matheus.felini
 * @package caixa
 * @version $Revision: 1.32 $
 */
class AutenticacaoPlanilha
{

    /**
     * Planilha de Arrecadacao
     * @var PlanilhaArrecadacao
     */
    private $oPlanilha;

    /**
     * Data da autenticacao
     * @var integer
     */
    private $dtAutenticacao;

    /**
     * IP do terminal em que foi autenticado a planilha
     * @var string
     */
    private $sIpTerminal;

    /**
     * Codigo do usuario que esta autenticando a planilha
     * @var integer
     */
    private $iCodigoUsuario;

    /**
     * Constrói o objeto para executar a autenticação da planilha passada pelo parâmetro ao construtor
     * @param PlanilhaArrecadacao $oPlanilha
     * @throws ParameterException
     */
    public function __construct(PlanilhaArrecadacao $oPlanilha = null)
    {

        if (!$oPlanilha instanceof PlanilhaArrecadacao) {
            throw new ParameterException("Não é um objeto do tipo PlanilhaArrecadacao.");
        }
        $this->oPlanilha = $oPlanilha;
        $this->dtAutenticacao = date("Y-m-d", db_getsession("DB_datausu"));
        $this->iCodigoUsuario = db_getsession("DB_id_usuario");
        $this->sIpTerminal = db_getsession("DB_ip");
    }

    /**
     * Autentica as receitas inclusas em uma planilha
     * @throws BusinessException
     * @throws Exception
     */
    public function autenticar()
    {
        if (!db_utils::inTransaction()) {
            throw new BusinessException("Sem transação ativa com o banco de dados.");
        }

        $iCodigoPlanilha = $this->oPlanilha->getCodigo();
        $sSqlAutenticacao = " select fc_autenticaplanilha({$iCodigoPlanilha}, '{$this->dtAutenticacao}', ";
        $sSqlAutenticacao .= "'{$this->sIpTerminal}', $this->iCodigoUsuario, true)";

        $rsAutenticacao = db_query($sSqlAutenticacao);
        if (!$rsAutenticacao) {
            throw new BusinessException("Não foi possível autenticar a planilha.\nPossível causa: Conciliação da conta bancária pode estar fechada. Verifique.");
        }

        $sRetornoAutenticacao = db_utils::fieldsMemory($rsAutenticacao, 0)->fc_autenticaplanilha;
        if (substr($sRetornoAutenticacao, 0, 1) != '1') {
            $sMsgErro = "Erro ao Autenticar.\n";
            $sMsgErro .= $sRetornoAutenticacao;
            throw new BusinessException($sMsgErro);
        }

        $aAutenticacoes = explode(",", substr_replace($sRetornoAutenticacao, '', 0, 1));
        $aReceitaPlanilha = $this->oPlanilha->getReceitasPlanilha();
        foreach ($aAutenticacoes as $i => $iCodigoAutenticacao) {
            /**
             * Foi observado um sincronismo entre o  numero de autenticacoes e de receitas da planilha; alem disso o
             * codigo da autenticacao aponta para a mesma receita na mesma ordem obtida pelo array de ReceitaPlanilha
             * Por isso, a posicao referenciada no array.
             */
            $oReceitaPlanilha = $aReceitaPlanilha[$i];
            $oDadosAutenticacao = self::getDadosAutenticacao(null);
            $lReceita = $this->executarLancamentoContabeis($iCodigoAutenticacao, false, $oDadosAutenticacao, $oReceitaPlanilha);
            $lReceitaExtra = $this->executarLancamentosReceitaExtraOrcamentaria($iCodigoAutenticacao, false, $oDadosAutenticacao);

            if (!$lReceita && !$lReceitaExtra) {
                throw new BusinessException("Não encontradas receitas para serem arrecadadas");
            }
        }

        /**
         * Quando os parâmetros abaixo estiverem configurados como demonstrado:
         * - k29_gerarslipautomaticoreceitaretencao: 0 (Não gera Slips automaticamente)
         * - k29_utilizarcontaextra: t (SIM)
         * Devemos prepara os dados para gerar o slip de transferência para cobertura extra orçamentária
         */
        if (PlanilhaArrecadacao::devePrepararSlipsParaCoberturaExtraOrcamentaria()) {
            $this->prepararSlipCoberturaExtraOrcamentaria();
        }

        /**
         * Quando o parâmetro k29_gerarslipautomaticoreceitaretencao = 1 (SIM - no momento da apropriação da retenção)
         * o sistema gera o slip de transferência.
         */
        if (slip::getParametroSlipAutomaticoLiquidacao()) {
            $this->gerarSlipDasReceitasExtras();
        }

        return true;
    }

    /**
     * Verifica slips ja gerados para planilha
     */
    public function slipJaVinculado($codigoReceita)
    {

        $oDao = new cl_slipplacaixarec;
        $sql = $oDao->sql_query_file(null, "*", null, "k207_placaixarec = {$codigoReceita}");
        $oDao->sql_record($sql);
        if ($oDao->numrows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gerar slip para as receitas extras da planilha
     */
    public function gerarSlipDasReceitasExtras()
    {
        $aReceitas = $this->oPlanilha->getReceitasPlanilha();
        foreach ($aReceitas as $oReceita) {
            if ($this->slipJaVinculado($oReceita->getCodigo())) {
                continue;
            }

            if (ReceitaExtraOrcamentaria::isExtra($oReceita->getTipoReceita())) {
                $oReceitaExtra = new ReceitaExtraOrcamentaria($oReceita->getTipoReceita());

                $iContaDebito = $oReceitaExtra->getContaPlanoPCASP()->getReduzido();
                $iContaCredito = $oReceita->getContaTesouraria()->getCodigoConta();
                if (!empty($oReceita->getContaTesouraria()->getContaExtra())) {
                    $iContaCredito = $oReceita->getContaTesouraria()->getContaExtra();
                }

                $sObservacao = "Correspondente ao recolhimento do valor apropriado através da planilha de lançamentos:";
                $sObservacao .= " {$this->oPlanilha->getCodigo()}:";
                $oSlip = new slip();
                $oSlip->setContaCredito($iContaCredito);
                $oSlip->setContaDebito($iContaDebito);
                $oSlip->setHistorico(9017);
                $oSlip->setCaracteristicaPeculiarCredito("000");
                $oSlip->setCaracteristicaPeculiarDebito("000");
                $oSlip->setValor($oReceita->getValor());
                $oSlip->setTipoPagamento(3);
                $oSlip->setSituacao(1);
                $oSlip->setNumCgm($oReceita->getCGM()->getCodigo());
                $oSlip->setObservacoes($sObservacao);
                $oSlip->save();
                Slip::vincularTipoOperacaoSlip($oSlip->getSlip(), 13);
                Slip::vincularSlipReceitaPlanilha($oSlip->getSlip(), $oReceita->getCodigo());
            }
        }
    }

    /**
     * Prepara o slip de transferência para cobertura extra orçamentária
     * @return void
     * @throws Exception
     */
    public function prepararSlipCoberturaExtraOrcamentaria()
    {
        $receitas = $this->oPlanilha->getReceitasPlanilha();
        foreach ($receitas as $receita) {
            if ($this->slipJaVinculado($receita->getCodigo())) {
                continue;
            }

            if (ReceitaExtraOrcamentaria::isExtra($receita->getTipoReceita())) {
                $contaTesouraria = $receita->getContaTesouraria();
                $contaDebito = $contaTesouraria->getContaExtra();
                $contaCredito = $contaTesouraria->getCodigoConta();

                if (empty($contaCredito)) {
                    throw new Exception(sprintf(
                        'A conta %s - %s não possui conta extra configurada. Para configurar acesse: %s',
                        $contaTesouraria->getCodigoConta(),
                        $contaTesouraria->getDescricao(),
                        'DB:FINANCEIRO > Tesouraria > Cadastros > Contas > Contas Tesouraria > Alteração de Conta'
                    ));
                }

                $dao = new cl_planilha_gerar_slips_cobertura_extra();
                $dao->placaixarec_id = $receita->getCodigo();
                $dao->credor = $receita->getCGM()->getCodigo();
                $dao->creditar = $contaCredito;
                $dao->debitar = $contaDebito;
                $dao->historico = 9140;
                $dao->cp = '000';
                $dao->valor = $receita->getValor();
                $dao->tipo_pagamento = 3;
                $dao->tipo_operacao = 17;

                $dao->incluir(null);
                if ($dao->erro_status == 0) {
                    $msg = sprintf(
                        '%s %s. Conta sem conta extra configurada: %s',
                        'Erro ao preparar slip de cobertura de transferência de cobertura extra orçamentária.',
                        $dao->erro_msg,
                        $contaCredito
                    );
                    throw new Exception($msg);
                }
            }
        }
    }

    /**
     * Estorna a autenticação da planilha
     * @return boolean true
     * @throws BusinessException
     */
    public function estornar()
    {

        if (!db_utils::inTransaction()) {
            throw new BusinessException("Sem transação ativa com o banco de dados.");
        }

        $sIpAutenticadora = db_getsession("DB_ip");
        $iIdUsuario = db_getsession("DB_id_usuario");
        $dtEstorno = date('Y-m-d', db_getsession("DB_datausu"));

        $sSql = "select fc_estornoplanilha({$this->oPlanilha->getCodigo()}, '{$dtEstorno}', '{$sIpAutenticadora}', {$iIdUsuario}, true)";
        $rsEstorno = db_query($sSql);

        if (!$rsEstorno) {
            throw new BusinessException("Não foi possível estornar a autenticação da planilha.\nPossível causa: Conciliação da conta bancária pode estar fechada. Verifique.");
        }

        $sRetornoEstorno = db_utils::fieldsMemory($rsEstorno, 0)->fc_estornoplanilha;
        if (substr($sRetornoEstorno, 0, 1) != '1') {
            $sMsgErro = "Erro ao Autenticar.\n";
            $sMsgErro .= $sRetornoEstorno;
            throw new BusinessException($sMsgErro);
        }

        $aAutenticacoes = explode(",", substr_replace($sRetornoEstorno, '', 0, 1));
        $aReceitaPlanilha = $this->oPlanilha->getReceitasPlanilha();

        if ($this->oPlanilha->existeLancamentoContabil()) {
            foreach ($aAutenticacoes as $i => $iCodigoAutenticacao) {
                /**
                 * Foi observado um sincronismo entre o  numero de autenticacoes e de receitas da planilha; alem disso o
                 * codigo da autenticacao aponta para a mesma receita na mesma ordem obtida pelo array de ReceitaPlanilha
                 * Por isso, a posicao referenciada no array.
                 */
                $oReceitaPlanilha = $aReceitaPlanilha[$i];
                $oDadoAutenticacao = self::getDadosAutenticacao(null);
                $lReceita = $this->executarLancamentoContabeis($iCodigoAutenticacao, true, $oDadoAutenticacao, $oReceitaPlanilha);
                $lReceitaExtra = $this->executarLancamentosReceitaExtraOrcamentaria($iCodigoAutenticacao, true, $oDadoAutenticacao);

                if (!$lReceita && !$lReceitaExtra) {
                    throw new BusinessException("Não encontradas receitas para serem arrecadadas");
                }
            }
        }

        if (PlanilhaArrecadacao::devePrepararSlipsParaCoberturaExtraOrcamentaria()) {
            $this->estornarPrepararSlipCoberturaExtraOrcamentaria();
        }

        if (slip::getParametroSlipAutomaticoLiquidacao()) {
            $this->estornarSlipsGeradosAutomaticos();
        }

        return true;
    }


    /**
     * Estorna os slips gerados automaticamente para as receitas extras da planilha
     */
    public function estornarSlipsGeradosAutomaticos()
    {

        $iCodigoPlanilha = $this->oPlanilha->getCodigo();
        // busca slips autenticados
        $aSlipsAutomaticos = $this->oPlanilha->getSlipsAutomaticosDasExtras(2);
        foreach ($aSlipsAutomaticos as $slip) {
            $oTransferencia = TransferenciaFactory::getInstance(null, $slip);
            $oTransferencia->anular("Estorno de Slip Gerado automaticamente da planilha {$iCodigoPlanilha}");
            $oTransferencia->executarLancamentoContabil(null, true);
        }
    }

    /**
     * Estora ou exclui a preparação do slip conforme as seguintes situações
     * 1 - Planilha autenticada, slips não gerados
     * - Exclui a preparação do slip
     * 2 - Planilha autenticada, slips emitidos
     * - bloqueia o estorno
     *
     * Foi acordado com o Leandro que não vai mais existir estorno de planilha que tenha essa situação.
     * @return void
     * @throws Exception
     */
    private function estornarPrepararSlipCoberturaExtraOrcamentaria()
    {
        // valida a situação 3 prevista no comentário do método
        $slips = $this->oPlanilha->getSlipsAutomaticosDasExtras();
        if (count($slips) > 0) {
            throw new Exception("Você não pode estornar a planilha, pois já foram emitidas transferências bancárias.");
        }

        $receitas = $this->oPlanilha->getReceitasPlanilha();
        $codigosExcluir = [];
        foreach ($receitas as $receita) {
            $codigosExcluir[] = $receita->getCodigo();
        }
        $dao = new cl_planilha_gerar_slips_cobertura_extra();
        $dao->excluir(null, "placaixarec_id in (" . implode(', ', $codigosExcluir) . ")");
        if ($dao->erro_status == 0) {
            throw new Exception('');
        }
    }


    public function executarLancamentoContabeis(
        $iCodigoAutenticacao,
        $lEstorno = false,
        $oDadoAutenticacao,
        $oReceitaPlanilha = null
    )
    {

        $oDaoCorrente = new cl_corrente;
        $sSqlBuscaDadosCorrente = $oDaoCorrente->sql_query_arrecadacao_receita(
            $oDadoAutenticacao->k12_id,
            $oDadoAutenticacao->k12_data,
            $iCodigoAutenticacao,
            "xxx.*, orcreceita.o70_codigo"
        );

        $rsBuscaDadosPlanilha = db_query($sSqlBuscaDadosCorrente);

        if (!$rsBuscaDadosPlanilha) {
            $sMsgErro = "Não é possível buscar os dados da autenticação para executar os lançamentos contábeis.";
            throw new DBException($sMsgErro);
        }

        $iTotalReceitas = pg_num_rows($rsBuscaDadosPlanilha);

        if ($iTotalReceitas == 0) {
            return false;
        }

        for ($iRowReceita = 0; $iRowReceita < $iTotalReceitas; $iRowReceita++) {
            $oDadoSqlGeral = db_utils::fieldsMemory($rsBuscaDadosPlanilha, $iRowReceita);
            $aReceitas = array();

            $iAno = db_getsession('DB_anousu');
            $oReceitaContabil = ReceitaContabilRepository::getReceitaByCodigo($oDadoSqlGeral->k02_codrec, $iAno);
            $iCodigoEstrutural = substr($oReceitaContabil->getContaOrcamento()->getEstrutural(), 0, 1);
            /**
             *
             *
             * @todo
             * Precisamos urgentemente refatorar este model, principalmente este trecho. O mesmo foi criado para atender
             * uma solicitação urgente do PO (Product Owner)
             *
             * Descrição do Problema:
             *  - Quando o usuário cria uma planilha lançando valores negativos na receita, o programa deve entender que
             *  se trata de um estorno, portanto devemos corrigir as variáveis com o valor necessário e setar a flag
             *  estorno = true.
             *
             *  - Quando o usuário acessar a rotina de ESTORNO dessa planilha com valores negativos devemos entender que não
             *  se trata de um estorno e sim de uma arrecadação.
             *
             *  Básicamente, a lógica se INVERTE quando se trata de receita com valores negativos. O mesmo acontece quando se
             *  trata de uma receita de dedução. Ou seja, começa com o estrutural ilike '9%'
             *
             *
             */
            if ($lEstorno) {
                if ($iCodigoEstrutural == 9) {
                    $oDadoSqlGeral->arrecada = abs($oDadoSqlGeral->arrecada);
                    $lEstorno = false;
                } elseif ($oDadoSqlGeral->arrecada > 0 && $oDadoSqlGeral->estorna == 0 && $lEstorno) {
                    $oDadoSqlGeral->arrecada = abs($oDadoSqlGeral->arrecada);
                    $lEstorno = false;
                } else {
                    $oDadoSqlGeral->arrecada = abs($oDadoSqlGeral->estorna);
                    $lEstorno = true;
                }
            }

            if ($iCodigoEstrutural == 9 &&
                $oDadoSqlGeral->arrecada == 0 && !$lEstorno) {
                $oDadoSqlGeral->arrecada = abs($oDadoSqlGeral->estorna);
                $lEstorno = true;
            }

            /**
             * Quando a planilha é criada com valor negativo, devemos entender que ele está estornando algum lançamento, portanto
             * setamos a variável estorno = true para que a receita saiba o que fazer de lançamento para estorno
             */
            if ($oDadoSqlGeral->arrecada == 0 && $oDadoSqlGeral->estorna < 0 && !$lEstorno) {
                $oDadoSqlGeral->arrecada = abs($oDadoSqlGeral->estorna);
                $lEstorno = true;
            }

            $cp = null;
            $cgm = null;

            if (!empty($oReceitaPlanilha) && $oReceitaPlanilha instanceof ReceitaPlanilha) {
                $cp = $oReceitaPlanilha->getCaracteristicaPeculiar()->getSequencial();
                $cgm = $oReceitaPlanilha->getCGM()->getCodigo();
            }

            // @todo - revisar questao da receita (codrec) para mais de uma instituicao
            // @todo - arrumar nome para este metodo
            $oReceitaContabil->processaLancamentosReceita(
                $oDadoSqlGeral->arrecada,
                $oDadoAutenticacao->k12_id,
                $oDadoAutenticacao->k12_data,
                $oDadoSqlGeral->k12_autent,
                $lEstorno,
                $oDadoSqlGeral->k12_conta,
                $oDadoSqlGeral->k12_histcor,
                $this->oPlanilha->getCodigo(),
                null,
                null,
                false,
                null,
                $cp,
                $cgm
            );
        }
        return true;
    }

    /**
     * Executa os lançamentos contábeis para receitas extras orçamentárias
     *
     * @param      $iCodigoAutenticacao
     * @param bool $lEstorno
     * @param      $oDadoAutenticacao
     * @return bool
     * @throws BusinessException
     * @throws Exception
     */
    public function executarLancamentosReceitaExtraOrcamentaria($iCodigoAutenticacao, $lEstorno = false, $oDadoAutenticacao)
    {

        $sCamposExtra = "corrente.k12_autent, corrente.k12_data, corrente.k12_id, k12_conta,";
        $sCamposExtra .= "tabrec.k02_codigo,";
        $sCamposExtra .= "k02_reduz,";
        $sCamposExtra .= "k81_concarpeculiar,";
        $sCamposExtra .= "k81_codigo,";
        $sCamposExtra .= "k81_numcgm,";
        $sCamposExtra .= "k12_histcor,";
        $sCamposExtra .= "corrente.k12_id,";
        $sCamposExtra .= "corrente.k12_autent,";
        $sCamposExtra .= "corrente.k12_estorn,";
        $sCamposExtra .= "case when";
        $sCamposExtra .= "  corrente.k12_estorn = 'f'";
        $sCamposExtra .= "    then cornump.k12_valor";
        $sCamposExtra .= "  else case when";
        $sCamposExtra .= "         corrente.k12_estorn = 't'";
        $sCamposExtra .= "           then cornump.k12_valor*-1";
        $sCamposExtra .= "       end ";
        $sCamposExtra .= "end as valor_arrecadar ";

        $sWhereExtra = "     corrente.k12_instit = " . db_getsession("DB_instit");
        $sWhereExtra .= " and corrente.k12_data   = '{$oDadoAutenticacao->k12_data}'";
        $sWhereExtra .= " and corrente.k12_autent = $iCodigoAutenticacao";
        $sWhereExtra .= " and corrente.k12_id     = {$oDadoAutenticacao->k12_id}";

        $oDaoCorrente = new cl_corrente;
        $sSqlBuscaReceitaExtra = $oDaoCorrente->sql_query_autenticacao_receita_extra_planilha(
            null,
            null,
            null,
            $sCamposExtra,
            null,
            $sWhereExtra
        );

        $rsBuscaReceitaExtra = db_query($sSqlBuscaReceitaExtra);
        if (!$rsBuscaReceitaExtra) {
            throw new BusinessException("Erro Técnico: Não foi possível localizar as receitas extras-orçamentárias para arrecadar.");
        }

        $iTotalReceitasExtras = pg_num_rows($rsBuscaReceitaExtra);
        if ($iTotalReceitasExtras == 0) {
            return false;
        }

        $daoPlacaixaRecFolha = new cl_rhempenhofolharubricaplanilha();
        $where = " rh111_placaixarec = " . $this->oPlanilha->getCodigo();
        $sqlPlanilhaFolha = $daoPlacaixaRecFolha->sql_query_file(null, "*", null, $where);
        $rsPlanilhaFolha = $daoPlacaixaRecFolha->sql_record($sqlPlanilhaFolha);
        $planilhaFolha = false;
        if ($rsPlanilhaFolha && pg_num_rows($rsPlanilhaFolha) > 0) {
            $planilhaFolha = true;
        }
        $iAnoSessao = db_getsession("DB_anousu");
        $dtDataAutenticacao = $oDadoAutenticacao->k12_data;
        for ($iRowAutenticacao = 0; $iRowAutenticacao < $iTotalReceitasExtras; $iRowAutenticacao++) {
            $oDadoAutenticacao = db_utils::fieldsMemory($rsBuscaReceitaExtra, $iRowAutenticacao);

            /**
             * Decidimos o tipo de documento aqui pois a receita pode ter sido lançada na planilha com valor negativo
             */

            $iCodigoDocumento = 160;
            $lEstorno = false;
            if ($oDadoAutenticacao->k12_estorn == "t") {
                $lEstorno = true;
                $iCodigoDocumento = 162;
            }

            $oContaContabil = new ContaPlanoPCASP(null, $iAnoSessao, $oDadoAutenticacao->k02_reduz);
            if (substr($oContaContabil->getEstrutural(), 0, 4) != '2188') {
                $iCodigoDocumento = ($lEstorno ? 152 : 150);
                if ($planilhaFolha) {
                    $iCodigoDocumento = ($lEstorno ? 153 : 151);
                }
            }


            $sObservacaoHistorico = "Planilha de Receita Extra-Orçamentária";
            if ($oDadoAutenticacao->k12_histcor != "") {
                $sObservacaoHistorico = $oDadoAutenticacao->k12_histcor;
            }

            $oLancamentoAuxiliar = new LancamentoAuxiliarArrecadacaoReceitaExtraOrcamentaria();
            $oLancamentoAuxiliar->setObservacaoHistorico($sObservacaoHistorico);
            $oLancamentoAuxiliar->setValorTotal(abs($oDadoAutenticacao->valor_arrecadar));
            $oLancamentoAuxiliar->setHistorico(9500);
            $oLancamentoAuxiliar->setContaCredito($oDadoAutenticacao->k02_reduz);
            $oLancamentoAuxiliar->setContaDebito($oDadoAutenticacao->k12_conta);
            $oLancamentoAuxiliar->setEstorno($lEstorno);
            $oLancamentoAuxiliar->setFavorecido($oDadoAutenticacao->k81_numcgm);
            $oLancamentoAuxiliar->setCaracteristicaPeculiar($oDadoAutenticacao->k81_concarpeculiar);
            $oLancamentoAuxiliar->setAutenticacao($oDadoAutenticacao->k12_id);
            $oLancamentoAuxiliar->setDataAutenticacao($oDadoAutenticacao->k12_data);
            $oLancamentoAuxiliar->setAutenticadora($iCodigoAutenticacao);
            $oEventoContabil = new EventoContabil($iCodigoDocumento, $iAnoSessao);
            $oEventoContabil->executaLancamento($oLancamentoAuxiliar, $dtDataAutenticacao);
        }
        return true;
    }


    /**
     * Retorna os dados criados para a autenticacao atual
     * @return stdClass - k12_data | k12_id | k12_autent
     * @throws BusinessException
     */
    public static function getDadosAutenticacao($dtAutenticacao = null)
    {

        $dtDataBuscar = $dtAutenticacao;
        if (empty($dtAutenticacao)) {
            $dtDataBuscar = date("Y-m-d", db_getsession("DB_datausu"));
        }
        $oDaoCorrenteAutenticacao = new cl_corautent;
        $sCampoAutenticacao = "max(k12_autent) as k12_autent, ";
        $sCampoAutenticacao .= "k12_data, ";
        $sCampoAutenticacao .= "k12_id ";
        $sWhereAutenticacao = "k12_data = '{$dtDataBuscar}' ";
        $sWhereAutenticacao .= " and k12_id   = (select k11_id ";
        $sWhereAutenticacao .= "                   from cfautent where k11_ipterm = '" . db_getsession("DB_ip") . "'";
        $sWhereAutenticacao .= "                    and k11_instit = " . db_getsession("DB_instit") . ")";
        $sWhereAutenticacao .= "group by k12_data, k12_id ";
        $sSqlBuscaUltimaAutenticacao = $oDaoCorrenteAutenticacao->sql_query_file(null, null, null, $sCampoAutenticacao, "k12_data", $sWhereAutenticacao);

        $rsBuscaUltimaAutenticacao = $oDaoCorrenteAutenticacao->sql_record($sSqlBuscaUltimaAutenticacao);
        if ($oDaoCorrenteAutenticacao->erro_status == "0") {
            throw new BusinessException("Não foi possível validar a última autenticação executada.");
        }
        return db_utils::fieldsMemory($rsBuscaUltimaAutenticacao, 0);
    }


    /**
     * Alteramos o tipo de vínculo do SLIP caso as receitas da planilha estejam vinculadas a um SLIP
     * @return boolean
     * @throws BusinessException
     */
    private function verificaReceitasVinculadasEmSlip()
    {

        $oDaoPlaCaixaRecSlip = new cl_placaixarecslip;
        $sWhereSlip = "placaixa.k80_codpla = {$this->oPlanilha->getCodigo()}";
        $sSqlBuscaSlip = $oDaoPlaCaixaRecSlip->sql_query_planilha_slip(null, "sliptipooperacaovinculo.*", null, $sWhereSlip);
        $rsBuscaSlip = $oDaoPlaCaixaRecSlip->sql_record($sSqlBuscaSlip);

        if ($oDaoPlaCaixaRecSlip->numrows > 0) {
            for ($iRowSlip = 0; $iRowSlip < $oDaoPlaCaixaRecSlip->numrows; $iRowSlip++) {
                $oStdDadoSlip = db_utils::fieldsMemory($rsBuscaSlip, $iRowSlip);
                $oTransferencia = new TransferenciaFactory($oStdDadoSlip->k153_slipoperacaotipo, $oStdDadoSlip->k153_slip);
                $oTransferencia->setTipoOperacao(11);
                $oTransferencia->alteraVinculoSlip();
                unset($oTransferencia);
            }
        }
        return true;
    }


}
