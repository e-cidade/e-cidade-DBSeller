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

use ECidade\Financeiro\Contabilidade\ContaCorrente\Services\Processamento;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\LancamentoRecurso;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\PosProcessamento;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\Service\ComplementoLancamentoService;

/**
 * Model para lançamentos contabeis
 * @package contabilidade
 * @author Iuri Guntchnigg Revisão$Author: dbmatheus.felini $
 * @version $Revision: 1.87 $
 */
class lancamentoContabil
{

    public $iCodLanc = null;
    private $dDataLanc = null;
    private $iAnousu = null;
    private $iCodDoc = null;
    private $nValorLancando = null;
    private $iAnoEmpenho = null;
    private $iCodCom = null;
    private $oTransaction = null;
    private $lInverterLancamento = false;
    private $aLancamentos = array();

    /**
     * metodo Construtor
     * @param integer $iCodDoc Código do documento a set executado
     * @param integer $iAnoUsu Ano do Lancamento
     * @param date $dDataLanc Data do lancamento.
     * @param float $nValorLancado valor do lancamento;.
     */
    function __construct($iCodDoc, $iAnousu, $dDataLanc, $nValorLancado)
    {

        $this->dDataLanc = $dDataLanc;
        $this->iAnoUsu = $iAnousu;
        $this->nValorLancado = $nValorLancado;
        $this->iCodDoc = $iCodDoc;
        $this->lSqlErro = false;
        $this->sErroMsg = '';
        $this->aLancamentos = array(
          "lancamCgm" => array("set" => 0),
          "lancamCompl" => array("set" => 0),
          "lancamDig" => array("set" => 0),
          "lancamDot" => array("set" => 0),
          "lancamEle" => array("set" => 0),
          "lancamEmp" => array("set" => 0),
          "lancamNota" => array("set" => 0),
          "lancamOrd" => array("set" => 0),
          "lancamPag" => array("set" => 0),
          "lancamRec" => array("set" => 0),
          "lancamRetif" => array("set" => 0),
          "lancamSup" => array("set" => 0)
        );

        if (!db_utils::inTransaction()) {
            throw new exception("{$iCodDoc} Nao foi possível iniciar lancamento.Nao foi possível achar uma transacao valida;");
        }
    }

    /**
     * Seta o cgm do lancamento
     * @param integer $iNumCgm Codigo do CGM
     * @return void
     */
    function setCgm($iNumCgm)
    {

        $this->aLancamentos["lancamCgm"]["set"] = 1;
        $this->aLancamentos["lancamCgm"]["c76_numcgm"] = $iNumCgm;
        $this->aLancamentos["lancamCgm"]["c76_data"] = $this->dDataLanc;
        $this->oLancamCgm = new cl_conlancamcgm;
    }

    /**
     * Seta o complemento do lancamento
     * @param string $sComplemento complemento do lancamento
     * @return void
     */

    function setComplemento($sComplemento)
    {

        $this->aLancamentos["lancamCompl"]["set"] = 1;
        $this->aLancamentos["lancamCompl"]["c72_complem"] = $sComplemento;
        $this->oLancamCompl = new cl_conlancamcompl;
    }

    /**
     * Seta o grupo de Lancamentos (tabela conlancamdig);
     * @param string $sChave chave identificadora do Grupo
     * @return void
     */

    function setDigito($sChave)
    {

        $this->aLancamentos["lancamDig"]["set"] = 1;
        $this->aLancamentos["lancamDig"]["c78_chave"] = $sChave;
        $this->aLancamentos["lancamDig"]["c78_data"] = $this->dDataLanc;
    }

    /**
     * Seta o empenho
     * @param integer $iChave código do empenho
     * @param integer $iAnoEmpenho seta o ano do empenho (para doc 32,31,33)
     * @param integer $iCodCom tipo da compra
     */
    function setEmpenho($iChave, $iAnoEmpenho = null, $iCodCom = null)
    {

        $this->aLancamentos["lancamEmp"]["set"] = 1;
        $this->aLancamentos["lancamEmp"]["c75_numemp"] = $iChave;
        $this->aLancamentos["lancamEmp"]["c75_data"] = $this->dDataLanc;
        $this->iAnoEmpenho = $iAnoEmpenho;
        $this->iCodCom = $iCodCom;
    }

    /**
     * Seta o elemento
     * @param integer $iChave código do elemento
     */
    function setElemento($iChave)
    {

        $this->aLancamentos["lancamEle"]["set"] = 1;
        $this->aLancamentos["lancamEle"]["c67_codele"] = $iChave;
    }

    /**
     * Seta a dotação
     * @param integer $iChave código da dotação
     */
    function setDotacao($iChave)
    {

        $this->aLancamentos["lancamDot"]["set"] = 1;
        $this->aLancamentos["lancamDot"]["c73_coddot"] = $iChave;
        $this->aLancamentos["lancamDot"]["c73_anousu"] = $this->iAnoUsu;
        $this->aLancamentos["lancamDot"]["c73_data"] = $this->dDataLanc;
    }

    /**
     * Seta a ordem de pagamento
     * @param integer $iChave código da orde de pagamento
     */
    function setOrdemPagamento($iChave)
    {

        $this->aLancamentos["lancamOrd"]["set"] = 1;
        $this->aLancamentos["lancamOrd"]["c80_codord"] = $iChave;
        $this->aLancamentos["lancamOrd"]["c80_data"] = $this->dDataLanc;
    }

    /**
     * Seta a nota de empenho
     * @param integer $iChave código da nota
     */
    function setNota($iChave)
    {

        $this->aLancamentos["lancamNota"]["set"] = 1;
        $this->aLancamentos["lancamNota"]["c66_conota"] = $iChave;
    }

    /**
     * seta o código reduzido do lancamento.
     * @param integer $iChave código do Reduzido.
     */

    function setReduz($iChave)
    {

        $this->aLancamentos["lancamPag"]["set"] = 1;
        $this->aLancamentos["lancamPag"]["c82_reduz"] = $iChave;
        $this->aLancamentos["lancamPag"]["c82_anousu"] = $this->iAnoUsu;
    }

    /**
     * retorna o código do lançamento gerado .
     * @return integer
     */
    function getCodigoLancamento()
    {
        return $this->iCodLanc;
    }

    /**
     * seto código da suplementação;
     *
     * @param integer $iSuplementacao código da suplementação
     */
    function setCodigoSuplementacao($iSuplementacao)
    {

        $this->aLancamentos["lancamSup"]["set"] = 1;
        $this->aLancamentos["lancamSup"]["c79_codsup"] = $iSuplementacao;
        $this->aLancamentos["lancamSup"]["c79_data"] = $this->dDataLanc;
    }

    function setReceita($iReceita)
    {

        $this->aLancamentos["lancamRec"]["set"] = 1;
        $this->aLancamentos["lancamRec"]["c74_codrec"] = $iReceita;
        $this->aLancamentos["lancamRec"]["c74_data"] = $this->dDataLanc;
        $this->aLancamentos["lancamRec"]["c74_anousu"] = $this->iAnoUsu;
    }

    /**
     * salva os Lancamentos no banco;
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     * @throws ReflectionException
     * @throws Exception
     */
    public function salvar()
    {
        $oConLancam = new cl_conlancam();
        $oconLancamVal = new cl_conlancamval();
        $oConLancam->c70_anousu = $this->iAnoUsu;
        $oConLancam->c70_data = $this->dDataLanc;
        $oConLancam->c70_valor = $this->nValorLancado;
        $res = $oConLancam->incluir(null);
        
        $instituicao = db_getsession('DB_instit');
        if ($oConLancam->erro_status == 0) {
            $this->lSqlErro = true;
            $this->sErroMsg = "Não foi Possível incluir lançamento\nErro Técnico:{$oConLancam->erro_msg}";
            throw new exception($this->sErroMsg);
        }

        if (!EventoContabil::vincularLancamentoNaInstituicao($oConLancam->c70_codlan, db_getsession('DB_instit'))) {
            throw new Exception('Não foi possível vincular o lançamento a instituição.');
        }

        if (!EventoContabil::vincularOrdem($oConLancam->c70_codlan)) {
            throw new Exception('Não foi possível setar a ordem para o lançamento.');
        }


        $this->iCodLanc = $oConLancam->c70_codlan;
        $oConLancamDoc = new cl_conlancamdoc;
        $oConLancamDoc->c71_data = $this->dDataLanc;
        $oConLancamDoc->c71_coddoc = $this->iCodDoc;
        $oConLancamDoc->c71_codlan = $oConLancam->c70_codlan;
        $oConLancamDoc->incluir($oConLancam->c70_codlan);
        if ($oConLancamDoc->erro_status == 0) {
            $this->lSqlErro = true;
            $this->sErroMsg = "Não foi possível iniciar lançamentos Contábeis\n";
            $this->sErroMsg .= "({$oConLancamDoc->erro_msg})";
            throw new exception($this->sErroMsg);
        }

        /*
         * buscamos a transacao cadastrada para o documento selecionado,
         * e fazemos os lancamentos necessários;
         */
        if ($this->oTransaction == null) {
            try {
                $this->getTransacao();
            } catch (Exception $eErro) {
                throw new exception($eErro->getMessage());
            }
        }
        if (count($this->oTransaction->arr_debito) == 0 || count($this->oTransaction->arr_credito) == 0) {
            throw new exception("Verifique o cadastro do Documento {$this->iCodDoc}.\nEncontrado Inconsistências");
        }
        $aCredito = $this->oTransaction->arr_credito;
        $aDebito = $this->oTransaction->arr_debito;
        //se a flag InverterLancamento estiver true, invertemos  as contas de credito e debito;
        if ($this->lInverterLancamento) {
            $aCredito = $this->oTransaction->arr_debito;
            $aDebito = $this->oTransaction->arr_credito;
        }

        $aHistori = $this->oTransaction->arr_histori;
        $aSeqtranslr = $this->oTransaction->arr_seqtranslr;
        $oPlanoReduz = new cl_conplanoreduz;
        $oDocumentoEvento = DocumentoEventoContabilRepository::getPorCodigo($this->iCodDoc);
        for ($iTran = 0; $iTran < count($aCredito); $iTran++) {
            $oPlanoReduz->sql_record($oPlanoReduz->sql_query_file(
                null,
                null,
                'c61_codcon',
                '',
                "c61_anousu = " . db_getsession("DB_anousu") . " and c61_reduz=" . $aDebito[$iTran]
            ) .' and c61_instit = '.$instituicao);
            if ($oPlanoReduz->numrows == 0) {
                $contaDebito = $this->getCodigoReduzido($aDebito[$iTran], $this->iAnoUsu, $instituicao);
                if (empty($contaDebito)) {
                    $this->lSqlErro = true;
                    $sErroMsg = "(D) Conta {$aDebito[$iTran]} não disponível para o exercício!";
                    throw new exception($sErroMsg);
                    break;
                }
                $aDebito[$iTran] = $contaDebito;
            }
            $oPlanoReduz->sql_record($oPlanoReduz->sql_query_file(
                null,
                null,
                'c61_codcon',
                '',
                "c61_anousu = " . db_getsession("DB_anousu") . "
                and c61_reduz ={$aCredito[$iTran]}  and c61_instit = {$instituicao}"
            ));
            if ($oPlanoReduz->numrows == 0 && $this->lSqlErro == false) {
                $contaCredito = $this->getCodigoReduzido($aCredito[$iTran], $this->iAnoUsu, $instituicao);
                if (empty($contaCredito)) {
                    $this->lSqlErro = true;
                    $sErroMsg = "(C) Conta {$aCredito[$iTran]} não disponível para o exercício!";
                    throw new exception($sErroMsg);
                    break;
                }
                $aCredito[$iTran] = $contaCredito;
            }
            unset($oConLancamVal);
            $oConLancamVal = new cl_conlancamval;
            $oConLancamVal->c69_codlan = $oConLancam->c70_codlan;
            $oConLancamVal->c69_credito = $aCredito[$iTran];
            $oConLancamVal->c69_debito = $aDebito[$iTran];
            $oConLancamVal->c69_codhist = $aHistori[$iTran];
            $oConLancamVal->c69_valor = $this->nValorLancado;
            $oConLancamVal->c69_data = $this->dDataLanc;
            $oConLancamVal->c69_anousu = $this->iAnoUsu;
            $oConLancamVal->incluir(null);
            if ($oConLancamVal->erro_status == 0) {
                $this->sErroMsg = "Conlancamval:" . $oConLancamVal->erro_msg;
                throw new exception($this->sErroMsg);
            }

            /**
             * Salvamos os dados referente a conta corrente
             */
            if ($this->aLancamentos["lancamEmp"]["set"] == 1) {
                require_once modification("libs/db_app.utils.php");

                db_app::import("contabilidade.contacorrente.ContaCorrenteFactory");
                db_app::import("Acordo");
                db_app::import("AcordoComissao");
                db_app::import("CgmFactory");
                db_app::import("financeiro.*");
                db_app::import("contabilidade.*");
                db_app::import("contabilidade.lancamento.*");
                db_app::import("Dotacao");

                require_once(modification("model/contabilidade/contacorrente/ContaCorrenteFactory.model.php"));
                require_once(modification("model/contabilidade/contacorrente/ContaCorrenteBase.model.php"));
                require_once(modification("model/financeiro/ContaBancaria.model.php"));
                require_once(modification("model/contabilidade/planoconta/SistemaConta.model.php"));
                require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));
                require_once(modification("model/contabilidade/planoconta/ClassificacaoConta.model.php"));
                require_once(modification("model/contabilidade/planoconta/ContaCorrente.model.php"));
                require_once(modification("model/contabilidade/planoconta/ContaOrcamento.model.php"));
                require_once(modification("model/contabilidade/planoconta/ContaPlanoPCASP.model.php"));

                db_app::import("contabilidade.contacorrente.*");

                $oContaCorrenteDetalheCredito = new ContaCorrenteDetalhe();
                $oContaCorrenteDetalheDebito = new ContaCorrenteDetalhe();
                //$oEmpenhoFinanceiro = new EmpenhoFinanceiro($this->aLancamentos["lancamEmp"]["c75_numemp"]);
                $oEmpenhoFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero(
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                $oLancamentoAuxiliar = new LancamentoAuxiliarEmpenho();

                $oLancamentoAuxiliar->setCaracteristicaPeculiar($oEmpenhoFinanceiro->getCaracteristicaPeculiar());
                $oLancamentoAuxiliar->setFavorecido($oEmpenhoFinanceiro->getCgm()->getCodigo());

                $iCodigoContrato = $oEmpenhoFinanceiro->getCodigoContrato();

                if (!empty($iCodigoContrato)) {
                    $oLancamentoAuxiliar->setAcordo($iCodigoContrato);
                }

                if ($this->aLancamentos["lancamDot"]["set"] == 1) {
                    $oDotacao = new Dotacao($this->aLancamentos["lancamDot"]["c73_coddot"], $this->iAnoUsu);
                    $oLancamentoAuxiliar->setCodigoRecurso($oDotacao->getRecurso());
                }

                $oDotacaoEmpenho = $oEmpenhoFinanceiro->getDotacao();

                $oContaCorrenteDetalheCredito->setDotacao($oDotacaoEmpenho);
                $oContaCorrenteDetalheDebito->setDotacao($oDotacaoEmpenho);
                $oContaCorrenteDetalheCredito->setRecurso($oDotacaoEmpenho->getDadosRecurso());
                $oContaCorrenteDetalheDebito->setRecurso($oDotacaoEmpenho->getDadosRecurso());

                if (!empty($this->aLancamentos["lancamPag"]["c82_reduz"]) &&
                  $oDocumentoEvento->getTipo() == DocumentoEventoContabil::TIPO_PAGAMENTO_EMPENHO) {
                    $iReduzidoContaPagadora = $this->aLancamentos["lancamPag"]["c82_reduz"];
                    $oPlanoConta = new ContaPlanoPCASP(null, $this->iAnoUsu, $iReduzidoContaPagadora);
                    $oContaCorrenteDetalheCredito->setRecurso(new Recurso($oPlanoConta->getRecurso()));
                }

                if (!empty($this->aLancamentos["lancamPag"]["c82_reduz"]) &&
                  $oDocumentoEvento->getTipo() == DocumentoEventoContabil::TIPO_ESTORNO_PAGAMENTO_EMPENHO) {
                    $iReduzidoContaPagadora = $this->aLancamentos["lancamPag"]["c82_reduz"];
                    $oPlanoConta = new ContaPlanoPCASP(
                        null,
                        $this->iAnoUsu,
                        $iReduzidoContaPagadora,
                        db_getsession('DB_instit')
                    );
                    $oContaCorrenteDetalheDebito->setRecurso(new Recurso($oPlanoConta->getRecurso()));
                }

                $oLancamentoAuxiliar->setNumeroEmpenho($this->aLancamentos["lancamEmp"]["c75_numemp"]);
                $oContaCorrenteDetalheCredito->setEmpenho($oEmpenhoFinanceiro);
                $oContaCorrenteDetalheDebito->setEmpenho($oEmpenhoFinanceiro);

                $oConplanoPCASP = new ContaPlanoPCASP(
                    null,
                    $this->iAnoUsu,
                    $oConLancamVal->c69_credito,
                    db_getsession('DB_instit')
                );

                if ($oConplanoPCASP->getContaBancaria()) {
                    $oContaCorrenteDetalheCredito->setContaBancaria($oConplanoPCASP->getContaBancaria());
                }

                $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalheCredito);

                $oContaCorrenteCredito = ContaCorrenteFactory::getInstance(
                    $oConLancamVal->c69_sequen,
                    $oConLancamVal->c69_credito,
                    $oLancamentoAuxiliar
                );


                if ($oContaCorrenteCredito) {
                    $oContaCorrenteCredito->salvar();
                }

                $oConplanoPCASP = new ContaPlanoPCASP(
                    null,
                    $this->iAnoUsu,
                    $oConLancamVal->c69_debito,
                    db_getsession('DB_instit')
                );

                if ($oConplanoPCASP->getContaBancaria()) {
                    $oContaCorrenteDetalheDebito->setContaBancaria($oConplanoPCASP->getContaBancaria());
                }

                $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalheDebito);

                $oContaCorrenteDebito = ContaCorrenteFactory::getInstance(
                    $oConLancamVal->c69_sequen,
                    $oConLancamVal->c69_debito,
                    $oLancamentoAuxiliar
                );

                if ($oContaCorrenteDebito) {
                    $oContaCorrenteDebito->salvar();
                }

                unset($oEmpenhoFinanceiro);
                unset($oContaCorrenteCredito);
                unset($oContaCorrenteDebito);
            } elseif ($this->aLancamentos["lancamDot"]["set"] == 1 && $this->aLancamentos["lancamEmp"]["set"] == 0) {
                $oDotacao = new Dotacao(
                    $this->aLancamentos["lancamDot"]["c73_coddot"],
                    $this->aLancamentos["lancamDot"]["c73_anousu"]
                );

                $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
                $oContaCorrenteDetalhe->setDotacao($oDotacao);
                $oContaCorrenteDetalhe->setRecurso($oDotacao->getDadosRecurso());

                $oLancamentoAuxiliar = new LancamentoAuxiliarSuplementacao();
                $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);

                $oContaCorrenteCredito = ContaCorrenteFactory::getInstance(
                    $oConLancamVal->c69_sequen,
                    $oConLancamVal->c69_credito,
                    $oLancamentoAuxiliar
                );

                $oContaCorrenteDebito = ContaCorrenteFactory::getInstance(
                    $oConLancamVal->c69_sequen,
                    $oConLancamVal->c69_debito,
                    $oLancamentoAuxiliar
                );
                if ($oContaCorrenteCredito) {
                    $oContaCorrenteCredito->salvar();
                }
                if ($oContaCorrenteDebito) {
                    $oContaCorrenteDebito->salvar();
                }
                unset($oDotacao);
                unset($oContaCorrenteCredito);
                unset($oContaCorrenteDebito);
            } elseif ($this->aLancamentos["lancamRec"]["set"] == 1
                      && $this->aLancamentos["lancamDot"]["set"] == 0
                      && $this->aLancamentos["lancamEmp"]["set"] == 0) {
                $receitaOrcamentaria = new ReceitaOrcamentaria($this->aLancamentos["lancamRec"]["c74_codrec"]);
                
                $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
                $oContaCorrenteDetalhe->setRecurso(new Recurso($receitaOrcamentaria->getTipoRecurso()));

                $oLancamentoAuxiliar = new LancamentoAuxiliarSuplementacao();
                $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);
            }
        }

        /*
         * Percorremos os lancamentos configurados pelo usuario.
         */
        foreach ($this->aLancamentos as $obj => $lancam) {
            if ($lancam["set"] == 1) {
                $sObjNome = "con" . strtolower($obj);
                $oObjeto = db_utils::getDao($sObjNome);
                //Percorremos as propriedades do lancamento para lancar .
                foreach ($lancam as $sPropriedade => $sValor) {
                    $oObjeto->$sPropriedade = $sValor;
                }
                //algumas das conlancam possuem mais de um parametro. tratamos aqui.
                switch ($sObjNome) {
                    case "conlancamnota":
                        $oObjeto->incluir($oConLancam->c70_codlan, $this->aLancamentos["lancamNota"]["c66_conota"]);
                        break;

                    default:
                        $oObjeto->incluir($oConLancam->c70_codlan);
                        break;
                }
                if ($oObjeto->erro_status == 0) {
                    $this->sErroMsg = "{$sObjNome}: " . $oObjeto->erro_msg;
                    throw new exception($this->sErroMsg);
                }
            }
        }

        
        /**
         * Regra adicionada para pegar sempre a caracteristica da dotação, caso exista empenho a CP será sobrescrita pela do empenho
         */
        $caracteristicaPeculiar = '';
        if (!empty($this->aLancamentos["lancamDot"]["c73_coddot"])) {
            $oDotacao = DotacaoRepository::getDotacaoPorCodigoAno(
                $this->aLancamentos["lancamDot"]["c73_coddot"],
                $this->aLancamentos["lancamDot"]["c73_anousu"]
            );
            $caracteristicaPeculiar = $oDotacao->getCaracteristicaPeculiar();
        }

        if (!empty($this->aLancamentos["lancamEmp"]["c75_numemp"])) {
            $oEmpenhoFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero(
                $this->aLancamentos["lancamEmp"]["c75_numemp"]
            );
            $caracteristicaPeculiar = $oEmpenhoFinanceiro->getCaracteristicaPeculiar();
        }

        if (!empty($caracteristicaPeculiar)) {
            $daoConlancamCaracterisstica = new cl_conlancamconcarpeculiar();
            $daoConlancamCaracterisstica->c08_sequencial = null;
            $daoConlancamCaracterisstica->c08_codlan = $oConLancam->c70_codlan;
            $daoConlancamCaracterisstica->c08_concarpeculiar = $caracteristicaPeculiar;
            $daoConlancamCaracterisstica->incluir(null);
            if ($daoConlancamCaracterisstica->erro_status === '0') {
                throw new DBException("Ocorreu um erro ao vincular o lançamento contábil com a caracteristica peculiar.");
            }
        }

        $recurso = new LancamentoRecurso($this->iCodDoc, $oConLancam->c70_codlan, $this->iAnoUsu);
        $recurso->processar($oLancamentoAuxiliar);

        $instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));
        $data = new \DBDate($this->dDataLanc);
        $competencia = new DBCompetencia($data->getAno(), $data->getMes());
        $oProcessamento = new Processamento($instituicao, $competencia);
        $oProcessamento->processar(array($oConLancam->c70_codlan));
        PosProcessamento::processar($oConLancam->c70_codlan);

        ComplementoLancamentoService::createIfNotExists($oConLancam->c70_codlan);
    }


    /**
     * Retorna o objeto de transacao com as contas que devem ser lançadas;
     * valida se os lancamentos configurados pelo usuario estao corretos.
     * @return object;
     */

    private function getTransacao()
    {

        require_once(modification("libs/db_libcontabilidade.php"));
        if (!class_exists("cl_translan")) {
            throw new exception("Problema ao buscar transações.");
        }
        $oTransLan = new cl_translan();
        switch ($this->iCodDoc) {
            //lancamento de Empenho
            case 1:
            case 304: // EMPENHO DA PROVISAO DE FERIAS
            case 308: // EMPENHO DA PROVISAO DE 13º SALARIO
            case 410: // EMPENHO SUPRIMENTO DE FUNDOS
            case 500: // EMPENHO DE PRECATORIOS
            case 504: // EMPENHO AMORT. DA DIVIDA
                //verificamos se o usuario setou  o codigo da conta
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado código da conta do lançamento.");
                }
                $oTransLan->db_trans_empenho($this->iCodCom, $this->iAnoUsu, $this->iCodDoc);
                $this->oTransaction = $oTransLan;
                break;

            //liquidacao de empenho
            case 3:
            case 84: // Liquidacao de Empenho Passivo
            case 306: // Liquidacao de Empenho provisao de ferias
            case 202: // LIQUIDAÇÃO DESPESA COM SERVIÇOS
            case 204: // LIQUIDAÇÃO DESPESA MATERIAL DE CONSUMO
            case 206: // LIQUIDAÇÃO AQUISIÇÃO MATERIAL PERMANENTE
            case 310: // LIQUIDACAO DA PROVISAO DE 13º SALARIO
            case 502: // LIQUIDAÇÃO DE PRECATÓRIOS
            case 506: // LIQUIDACAO AMORT. DIVIDA
                $iCodigoDocumento = $this->iCodDoc;
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da conta;
                 */

                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                $oTransLan->db_trans_liquida(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoUsu,
                    $iCodigoDocumento
                );
                $this->oTransaction = $oTransLan;
                break;

            //restos a pagar.;
            case 4:
            case 85: // Estorno de Liquidacao Passivo
            case 307: // ESTORNO DA LIQUIDACAO DA PROVISAO DE FERIAS
            case 311: // ESTORNO DA LIQUIDACAO DA PROVISAO DE 13º SALARIO
            case 203: // ESTORNO DE LIQUIDAÇÃO DESPESA COM SERVIÇOS
            case 205: // ESTORNO DE LIQ. DESPESA MATERIAL DE CONSUM
            case 207: // ESTORNO DE LIQ. AQ. MATERIAL PERMANENTE
            case 507: // ESTORNO LIQUIDACAO AMORT. DIVIDA
            case 503: // ESTORNO DA LIQUIDACAO DE PRECATORIOS
                $iCodigoDocumento = $this->iCodDoc;
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da contai, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                $oTransLan->db_trans_estorna_liquida(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoUsu,
                    $iCodigoDocumento
                );
                $this->oTransaction = $oTransLan;
                break;

            //estorno de Liquidacao capital
            case 23:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da conta;
                 */

                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                $oTransLan->db_trans_liquida_capital(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoUsu
                );
                $this->oTransaction = $oTransLan;
                break;
            case 24:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da contai, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                $oTransLan->db_trans_estorna_liquida_capital(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoUsu
                );
                $this->oTransaction = $oTransLan;
                break;

            //liquidacao de restos a pagar
            case 33:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da contai, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_liquida_resto(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                $this->oTransaction = $oTransLan;
                break;

            //estorno de liquidacao de empenho RP
            case 34:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da contai, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_estorna_liquida_resto(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                $this->oTransaction = $oTransLan;
                break;
            //estorno de restos a pagar processados
            case 31:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da conta, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_rp(31, $this->aLancamentos["lancamEmp"]["c75_numemp"]);
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;
                break;
            //estorno de restos a pagar não processados

            case 32:
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da conta, e o ano do Empenho;
                 */
                $this->lInverterLancamento = false;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_rp(32, $this->aLancamentos["lancamEmp"]["c75_numemp"]);
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;
                break;

            case 5: //pagamento de Empenho
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o Reduzido do lançamento.");
                }

                if ($this->aLancamentos["lancamDot"]["set"] != 1) {
                    throw new exception("Não foi informado a dotação do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                $oTransLan->db_trans_pagamento(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoUsu,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                $this->oTransaction = $oTransLan;
                $this->iContaEmp = $oTransLan->conta_emp;
                break;

            case 35: //pagamento de RPS
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o reduzido do lançamento.");
                }

                $oTransLan->db_trans_pagamento_resto(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iCodDoc
                );
                $this->iContaEmp = $oTransLan->conta_emp;
                $this->oTransaction = $oTransLan;
                break;

            case 37: //pagamento de RPS
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o reduzido do lançamento.");
                }

                $oTransLan->db_trans_pagamento_resto(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iCodDoc
                );
                $this->iContaEmp = $oTransLan->conta_emp;
                $this->oTransaction = $oTransLan;
                break;

            case 6: // estorno de pagamento de empenho (exercicio corrente);
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o Reduzido do lançamento.");
                }

                if ($this->aLancamentos["lancamDot"]["set"] != 1) {
                    throw new exception("Não foi informado a dotação do lançamento.");
                }
                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                $oTransLan->db_trans_estorna_pagamento(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoUsu,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );

                if (count($oTransLan->arr_debito) == 0 || count($oTransLan->arr_credito) == 0) {
                    throw new exception("Verifique o cadastro do Documento {$this->iCodDoc}.\nEncontrado Inconsistências");
                }
                $this->oTransaction = $oTransLan;
                $this->iContaEmp = $oTransLan->conta_emp;
                break;

            case 91: // ESTORNO SUPRIMENTO DE FUNDOS
            case 92: // DEVOLUCAO DE ADIANTAMENTO
                $oTransLan = new cl_translan();
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o Reduzido do lançamento.");
                }

                if ($this->aLancamentos["lancamDot"]["set"] != 1) {
                    throw new exception("Não foi informado a dotação do lançamento.");
                }
                $oTransLan->db_trans_estorna_pagamento_prestacao_contas(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoUsu,
                    $this->iCodDoc,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );

                if (count($oTransLan->arr_debito) == 0 || count($oTransLan->arr_credito) == 0) {
                    throw new exception("Verifique o cadastro do Documento {$this->iCodDoc}.\nEncontrado Inconsistências");
                }

                $this->oTransaction = $oTransLan;
                $this->iContaEmp = $oTransLan->conta_emp;
                break;

            case 36:
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o reduzido do lançamento.");
                }

                $oTransLan->db_trans_estorna_pagamento_resto(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iCodDoc
                );
                $this->iContaEmp = $oTransLan->conta_emp;
                $this->oTransaction = $oTransLan;
                break;

            case 38:
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o reduzido do lançamento.");
                }

                $oTransLan->db_trans_estorna_pagamento_resto(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoEmpenho,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iCodDoc
                );
                $this->iContaEmp = $oTransLan->conta_emp;
                $this->oTransaction = $oTransLan;
                break;

            case 2:  // ESTORNO DE EMPENHO
            case 83:  // estorno de empenho passivo
            case 305: // ESTORNO DE EMPENHO DA PROVISAO DE FERIAS
            case 309: // ESTORNO DE EMPENHO DA PROVISAO DE 13º SALÁRIO
            case 411: // ESTORNO DE EMPENHO SUPRIMENTO DE FUNDOS
            case 501: // ESTORNO DE EMPENHO DE PRECATORIOS
            case 505: // ESTORNO EMPENHO AMORT. DIVIDA
                $iCodigoDocumento = $this->iCodDoc;
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_estorna_empenho($this->iCodCom, $this->iAnoEmpenho, $iCodigoDocumento);
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;

                break;
            case 900:
                $iCodigoDocumento = $this->iCodDoc;
                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                break;
            case 903:
                $iCodigoDocumento = $this->iCodDoc;
                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                if ($this->aLancamentos["lancamCgm"]["set"] != 1) {
                    throw new exception("Não foi informado o credor do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }
                $this->aLancamentos["lancamEle"]["set"] = 0;
                $oTransLan->db_trans_empenho_contrato(
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iAnoEmpenho,
                    903
                );
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;
                break;

            case 901:
            case 904:
                $iCodigoDocumento = $this->iCodDoc;
                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                if ($this->aLancamentos["lancamCgm"]["set"] != 1) {
                    throw new exception("Não foi informado o credor do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }
                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }
                $this->aLancamentos["lancamEle"]["set"] = 0;
                $oTransLan->db_trans_liquidacao_contrato(
                    $this->aLancamentos["lancamEmp"]["c75_numemp"],
                    $this->iAnoEmpenho,
                    $this->iCodDoc
                );
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;
                break;
            case 412: // LIQUIDACAO SUPRIMENTO DE FUNDOS
                /*
                 * Devemos verificar ser o usuario setou o elemento, e o codigo da conta;
                 */
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                $oTransLan->db_trans_liquida(
                    $this->iCodCom,
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->iAnoUsu,
                    $this->iCodDoc
                );
                $this->oTransaction = $oTransLan;
                break;

            case 413: // ESTORNO DE LIQUIDAÇÃO SUPRIMENTO DE FUNDOS
                if ($this->iCodCom == null) {
                    throw new exception("Não foi informado o código da conta do lançamento.");
                }

                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamEmp"]["set"] != 1) {
                    throw new exception("Não foi informado o empenho do lançamento.");
                }

                if ($this->iAnoEmpenho == null) {
                    throw new exception("Não foi informado o ano do empenho.");
                }

                $oTransLan->db_trans_estorna_empenho(
                    $this->iCodCom,
                    $this->iAnoEmpenho,
                    $this->iCodDoc,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                if ($oTransLan->sqlerro) {
                    throw new exception($oTransLan->erro_msg);
                }
                $this->oTransaction = $oTransLan;
                break;
            case 90: // SUPRIMENTO DE FUNDOS
                if ($this->aLancamentos["lancamEle"]["set"] != 1) {
                    throw new exception("Não foi informado o elemento do lançamento.");
                }

                if ($this->aLancamentos["lancamPag"]["set"] != 1) {
                    throw new exception("Não foi informado o Reduzido do lançamento.");
                }

                if ($this->aLancamentos["lancamDot"]["set"] != 1) {
                    throw new exception("Não foi informado a dotação do lançamento.");
                }
                $oTransLan->db_trans_pagamento_prestacao_contas(
                    $this->aLancamentos["lancamEle"]["c67_codele"],
                    $this->aLancamentos["lancamPag"]["c82_reduz"],
                    $this->iAnoUsu,
                    $this->aLancamentos["lancamEmp"]["c75_numemp"]
                );
                $this->oTransaction = $oTransLan;
                $this->iContaEmp = $oTransLan->conta_emp;
                break;

            default:
                throw new exception(" Documento {$this->iCodDoc} inválido!");
                break;
        }


        /** @todo mudar as contas da prefeitura pelas contas da instituicao */


        return $this->oTransaction;
    }

    public static function getInfoLancamento($iCodigoLancamento, $lMostraContas = true)
    {

        $sSqlDadosLancamento = "select c70_codlan  as codigo, ";
        $sSqlDadosLancamento .= "       c70_data    as data, ";
        $sSqlDadosLancamento .= "       c70_valor   as valor, ";
        $sSqlDadosLancamento .= "       c71_coddoc  as documento,";
        $sSqlDadosLancamento .= "       c53_descr   as descricaoevento,";
        $sSqlDadosLancamento .= "       c80_codord  as ordempagamento,";
        $sSqlDadosLancamento .= "       c75_numemp  as empenho, ";
        $sSqlDadosLancamento .= "       c76_numcgm  as cgm, ";
        $sSqlDadosLancamento .= "       z01_nome    as nome,  ";
        $sSqlDadosLancamento .= "       e69_numero  as notafiscal, ";
        $sSqlDadosLancamento .= "       e69_codnota as codigonotafiscal, ";
        $sSqlDadosLancamento .= "       c72_complem as complemento, ";
        $sSqlDadosLancamento .= "       c73_coddot  as dotacao,";
        $sSqlDadosLancamento .= "       c74_codrec  as receita,";
        $sSqlDadosLancamento .= "       c70_anousu  as anolancamento,";
        $sSqlDadosLancamento .= "       c53_tipo    as tipoevento,";
        $sSqlDadosLancamento .= "       c67_codele  as codigoelemento";
        $sSqlDadosLancamento .= "  from conlancam  ";
        $sSqlDadosLancamento .= "       inner join conlancamdoc on c71_codlan = c70_codlan ";
        $sSqlDadosLancamento .= "       inner join conhistdoc   on c71_coddoc = c53_coddoc ";
        $sSqlDadosLancamento .= "       left  join conlancamord on c70_codlan = c80_codlan ";
        $sSqlDadosLancamento .= "       left join conlancamemp on c75_codlan  = c70_codlan ";
        $sSqlDadosLancamento .= "       left join conlancamcgm on c70_codlan  = c76_codlan ";
        $sSqlDadosLancamento .= "       left join cgm on z01_numcgm = c76_numcgm ";
        $sSqlDadosLancamento .= "       left join conlancamnota on c70_codlan  =  c66_codlan ";
        $sSqlDadosLancamento .= "       left join empnota on c66_codnota       = e69_codnota ";
        $sSqlDadosLancamento .= "       left join conlancamcompl on c70_codlan = c72_codlan ";
        $sSqlDadosLancamento .= "       left join conlancamdot on c73_codlan   = c70_codlan ";
        $sSqlDadosLancamento .= "                             and c73_anousu   = c70_anousu ";
        $sSqlDadosLancamento .= "       left join conlancamele on c67_codlan   = c70_codlan ";
        $sSqlDadosLancamento .= "       left join conlancamrec on c74_codlan   = c70_codlan ";
        $sSqlDadosLancamento .= "                             and c74_anousu   = c70_anousu ";
        $sSqlDadosLancamento .= " where c70_codlan = {$iCodigoLancamento}";
        $rsDadosLancamento = db_query($sSqlDadosLancamento);
        if (pg_num_rows($rsDadosLancamento) == 0) {
            throw new Exception("Lançamento {$iCodigoLancamento} não existe.");
        }
        $oDadosLancamento = db_utils::fieldsMemory($rsDadosLancamento, 0, false, false, true);
        $oDadosLancamento->contas = array();
        if ($lMostraContas) {
            $sSqlContas = "select  c69_debito as contadebito, ";
            $sSqlContas .= "      c1.c60_descr as descricaodebito, ";
            $sSqlContas .= "      c1.c60_estrut as estruturaldebito, ";
            $sSqlContas .= "      c69_credito as contacredito, ";
            $sSqlContas .= "      c2.c60_descr as descricaocredito, ";
            $sSqlContas .= "      c2.c60_estrut as estruturalcredito, ";
            $sSqlContas .= "      c69_valor    as valor,";
            $sSqlContas .= "      c69_ordem    as ordem,";
            $sSqlContas .= "      fc_atributo_conta_corrente({$iCodigoLancamento}, c69_debito, 'D') as atributos_cc_debito,";
            $sSqlContas .= "      fc_atributo_conta_corrente({$iCodigoLancamento}, c69_credito, 'C') as atributos_cc_credito";
            $sSqlContas .= " from conlancam  ";
            $sSqlContas .= "      inner join conlancamval       on c70_codlan      = c69_codlan ";
            $sSqlContas .= "      inner join conplanoreduz red1 on red1.c61_reduz  = conlancamval.c69_debito  ";
            $sSqlContas .= "                                   and red1.c61_anousu = conlancamval.c69_anousu ";
            $sSqlContas .= "      inner join conplano c1        on c1.c60_codcon   = red1.c61_codcon ";
            $sSqlContas .= "                                   and c1.c60_anousu   = red1.c61_anousu ";
            $sSqlContas .= "      inner join conplanoreduz red2 on red2.c61_reduz  = conlancamval.c69_credito ";
            $sSqlContas .= "                                   and red2.c61_anousu = conlancamval.c69_anousu ";
            $sSqlContas .= "      inner join conplano c2        on c2.c60_codcon   = red2.c61_codcon ";
            $sSqlContas .= "                                   and c2.c60_anousu   = red2.c61_anousu ";
            $sSqlContas .= " where c70_codlan={$iCodigoLancamento}";
            $sSqlContas .= " order by c69_ordem, c69_sequen ";

            $rsContas = db_query($sSqlContas);
            $oDadosLancamento->contas = db_utils::getCollectionByRecord($rsContas, false, false, true);
        }



        /**
         * Busca os dados do recurso usado no lançamento contábil
         */
        $sql = "
            select distinct o15_codigo, o15_recurso, o15_descr, o200_sequencial, o200_descricao
             from conlancamrecurso
             join orctiporec on orctiporec.o15_codigo = conlancamrecurso.c130_orctiporec
             join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
             where c130_conlancam = {$iCodigoLancamento};
        ";
        $rsRecursoLancamento = db_query($sql);

        $oDadosLancamento->recursoLancamento = db_utils::getCollectionByRecord($rsRecursoLancamento);

        return $oDadosLancamento;
    }


    public static function alterarDataLancamento($iCodigoLancamento, $dtData)
    {

        if (empty($iCodigoLancamento)) {
            throw new Exception("Informe o codigo do lançamento");
        }

        /**
         * alteramos a data da conlancam
         */
        $oConlancam = new cl_conlancam;
        $oConlancam->c70_codlan = $iCodigoLancamento;
        $oConlancam->c70_data = $dtData;
       // $oConlancam->c70_anousu =
        $oConlancam->alterar($iCodigoLancamento);
        if ($oConlancam->erro_status == 0) {
            throw new Exception("Erro[1] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamval
         */
        $oDaoConlancamVal = new cl_conlancamval;
        $sSqlLancamentos = $oDaoConlancamVal->sql_query_file(null, "*", null, "c69_codlan = {$iCodigoLancamento}");
        $rsLancamentos = $oDaoConlancamVal->sql_record($sSqlLancamentos);
        if ($oDaoConlancamVal->numrows > 0) {
            $aLancamentos = db_utils::getCollectionByRecord($rsLancamentos);
            foreach ($aLancamentos as $oLancamento) {

                /**
                 * Tratamento para os dados da ContaCorrente
                 */
                $oDaoContaCorrenteDetalheValor = new cl_contacorrentedetalheconlancamval;
                $sWhereExcluirContaCorrenteDetalhe = "c28_conlancamval = {$oLancamento->c69_sequen}";
                $sSqlBuscaContaCorrente = $oDaoContaCorrenteDetalheValor->sql_query_file(
                    null,
                    "*",
                    null,
                    $sWhereExcluirContaCorrenteDetalhe
                );
                $rsBuscaContaCorrente = db_query($sSqlBuscaContaCorrente);
                if (!$rsBuscaContaCorrente) {
                    throw new Exception("Não foi possível buscar os dados da conta corrente.");
                }
                $aDadosContaCorrente = array();
                if (pg_num_rows($rsBuscaContaCorrente) > 0) {
                    $aDadosContaCorrente = db_utils::getCollectionByRecord($rsBuscaContaCorrente);
                }
                $oDaoContaCorrenteDetalheValor->excluir(null, $sWhereExcluirContaCorrenteDetalhe);
                if ($oDaoContaCorrenteDetalheValor->erro_status == 0) {
                    throw new Exception("Não foi possível excluir os dados da conta corrente.");
                }


                $oDaoConlancamlr = new cl_conlancamlr;
                $sSqlLancamentoConfig = $oDaoConlancamlr->sql_query_file($oLancamento->c69_sequen);
                $rsLancamentosConfig = $oDaoConlancamlr->sql_record($sSqlLancamentoConfig);
                $aLancamentosConfig = db_utils::getCollectionByRecord($rsLancamentosConfig);
                $oDaoConlancamlr->excluir($oLancamento->c69_sequen);
                if ($oDaoConlancamlr->erro_status == 0) {
                    throw new Exception("Erro[2] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.");
                }

                $oDaoConlancamVal->excluir($oLancamento->c69_sequen);
                if ($oDaoConlancamVal->erro_status == 0) {
                    $sErroMensagem = "Erro[3] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.\n";
                    $sErroMensagem .= $oDaoConlancamVal->erro_banco;
                    throw new Exception($sErroMensagem);
                }

                /**
                 * Incluimos a conlancam a data alterada
                 */
                $data = new \DBDate($dtData);
                $ano = $data->getAno();

                $oDaoConlancamVal->c69_anousu = $ano;
                $oDaoConlancamVal->c69_codhist = $oLancamento->c69_codhist;
                $oDaoConlancamVal->c69_codlan = $oLancamento->c69_codlan;
                $oDaoConlancamVal->c69_credito = $oLancamento->c69_credito;
                $oDaoConlancamVal->c69_data = $dtData;
                $oDaoConlancamVal->c69_debito = $oLancamento->c69_debito;
                $oDaoConlancamVal->c69_valor = $oLancamento->c69_valor;
                $oDaoConlancamVal->c69_ordem = $oLancamento->c69_ordem;
                $oDaoConlancamVal->incluir(null);
                if ($oDaoConlancamVal->erro_status == 0) {
                    $sErroMsg = "Erro[4] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.";
                    $sErroMsg .= "\nErro Técnico:{$oDaoConlancamVal->erro_msg}";
                    throw new Exception($sErroMsg);
                }
                /**
                 * Configuramos a conta corrente novamente.
                 */
                foreach ($aDadosContaCorrente as $oStdContaCorrente) {
                    $oDaoContaCorrenteDetalheValor = new cl_contacorrentedetalheconlancamval;
                    $oDaoContaCorrenteDetalheValor->c28_sequencial = null;
                    $oDaoContaCorrenteDetalheValor->c28_contacorrentedetalhe = $oStdContaCorrente->c28_contacorrentedetalhe;
                    $oDaoContaCorrenteDetalheValor->c28_conlancamval = $oDaoConlancamVal->c69_sequen;
                    $oDaoContaCorrenteDetalheValor->c28_tipo = $oStdContaCorrente->c28_tipo;
                    $oDaoContaCorrenteDetalheValor->incluir(null);
                    if ($oDaoContaCorrenteDetalheValor->erro_status == 0) {
                        throw new Exception("Não foi possível inserir os dados para a conta corrente {$oStdContaCorrente->c28_contacorrentedetalhe}. Contate o Suporte.");
                    }
                    unset($oDaoContaCorrenteDetalheValor);
                }


                /**
                 * incluimos na conlancamlr
                 */
                foreach ($aLancamentosConfig as $oLancamentoConfig) {
                    $oDaoConlancamlr->c81_sequen = $oDaoConlancamVal->c69_sequen;
                    $oDaoConlancamlr->c81_seqtranslr = $oLancamentoConfig->c81_seqtranslr;
                    $oDaoConlancamlr->incluir($oDaoConlancamVal->c69_sequen, $oLancamentoConfig->c81_seqtranslr);
                    if ($oDaoConlancamVal->erro_status == 0) {
                        throw new Exception("Erro[5] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.");
                    }
                }
            }
        }

        /**
         * Alteramos a tabela conlancamcgm
         */
        $oConlancamcgm = new cl_conlancamcgm;
        $oConlancamcgm->c76_codlan = $iCodigoLancamento;
        $oConlancamcgm->c76_data = $dtData;
        $oConlancamcgm->alterar($iCodigoLancamento);
        if ($oConlancamcgm->erro_status == 0) {
            throw new Exception("Erro[6] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamdig
         */
        $oConlancamdig = new cl_conlancamdig;
        $oConlancamdig->c78_codlan = $iCodigoLancamento;
        $oConlancamdig->c78_data = $dtData;
        $oConlancamdig->alterar($iCodigoLancamento);
        if ($oConlancamdig->erro_status == 0) {
            throw new Exception("Erro[7] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamdoc
         */
        $oConlancamdoc = new cl_conlancamdoc;
        $oConlancamdoc->c71_codlan = $iCodigoLancamento;
        $oConlancamdoc->c71_data = $dtData;
        $oConlancamdoc->alterar($iCodigoLancamento);
        if ($oConlancamdoc->erro_status == 0) {
            throw new Exception("Erro[8] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamdot
         */
        $oConlancamdot = new cl_conlancamdot;
        $oConlancamdot->c73_codlan = $iCodigoLancamento;
        $oConlancamdot->c73_data = $dtData;
        $oConlancamdot->alterar($iCodigoLancamento);
        if ($oConlancamdot->erro_status == 0) {
            throw new Exception("Erro[9] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamemp
         */
        $oConlancamemp = new cl_conlancamemp;
        $oConlancamemp->c75_codlan = $iCodigoLancamento;
        $oConlancamemp->c75_data = $dtData;
        $oConlancamemp->alterar($iCodigoLancamento);
        if ($oConlancamemp->erro_status == 0) {
            throw new Exception("Erro[10] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamord
         */
        $oConlancamord = new cl_conlancamord;
        $oConlancamord->c80_codlan = $iCodigoLancamento;
        $oConlancamord->c80_data = $dtData;
        $oConlancamord->alterar($iCodigoLancamento);
        if ($oConlancamord->erro_status == 0) {
            throw new Exception("Erro[11] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamrec
         */
        $oConlancamrec = new cl_conlancamrec;
        $oConlancamrec->c74_codlan = $iCodigoLancamento;
        $oConlancamrec->c74_data = $dtData;
        $oConlancamrec->alterar($iCodigoLancamento);
        if ($oConlancamrec->erro_status == 0) {
            throw new Exception("Erro[12] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamretif
         */
        $oConlancamretif = new cl_conlancamretif;
        $oConlancamretif->c79_codlan = $iCodigoLancamento;
        $oConlancamretif->c79_data = $dtData;
        $oConlancamretif->alterar($iCodigoLancamento);
        if ($oConlancamretif->erro_status == 0) {
            throw new Exception("Erro[13] - Erro ao alterar a data do lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamsup
         */
        $oConlancamsup = new cl_conlancamsup;
        $oConlancamsup->c79_codlan = $iCodigoLancamento;
        $oConlancamsup->c79_data = $dtData;
        $oConlancamsup->alterar($iCodigoLancamento);
        if ($oConlancamsup->erro_status == 0) {
            throw new Exception("Erro[14] - Erro ao alterar a data do lançamento contábil.");
        }
    }

    public static function excluirLancamento($iCodigoLancamento)
    {

        if (empty($iCodigoLancamento)) {
            throw new Exception("Informe o codigo do lançamento");
        }

        /**
         * Excluindo departamento do lancamento contabil
         */
        $oConlancamDepartamento = new cl_conlancamdepartamento;
        $oConlancamDepartamento->c128_conlancam = $iCodigoLancamento;
        $oConlancamDepartamento->excluir(null, "c128_conlancam = {$iCodigoLancamento} ");
        if ($oConlancamDepartamento->erro_status == 0) {
            throw new Exception("Erro[1] - Erro ao excluir departamento do lançamento contábil.");
        }

        /**
         * Excluindo recurso do lancamento contabil
         */
        $oConlancamRecurso = new cl_conlancamrecurso;
        $oConlancamRecurso->c130_conlancam = $iCodigoLancamento;
        $oConlancamRecurso->excluir(null, "c130_conlancam = {$iCodigoLancamento} ");
        if ($oConlancamRecurso->erro_status == 0) {
            throw new Exception("Erro[2] - Erro ao excluir recurso do lançamento contábil.");
        }

        /**
         * excluimos a tabela conlancamsup
         */
        $oConlancamsup = new cl_conlancamsup;
        $oConlancamsup->c79_codlan = $iCodigoLancamento;
        $oConlancamsup->excluir($iCodigoLancamento);
        if ($oConlancamsup->erro_status == 0) {
            throw new Exception("Erro[3] - Erro ao excluir a data do lançamento contábil.");
        }

        /**
         * excluimos a tabela conlancamacordo
         */
        $oConlancamacordo = new cl_conlancamacordo;
        $oConlancamacordo->c87_codlan = $iCodigoLancamento;
        $oConlancamacordo->excluir($iCodigoLancamento);
        if ($oConlancamacordo->erro_status == 0) {
            throw new Exception("Erro[4] - Erro ao excluir vinculo do lançamento contábil com o contrato.");
        }
        $oProtProcessoDocumento = new cl_protprocessodocumento;
        $sql = $oProtProcessoDocumento->sql_query_file(null, "p01_sequencial", null, "p01_c70codlan = {$iCodigoLancamento}");
        $rs = $oProtProcessoDocumento->sql_record($sql);
        if ($oProtProcessoDocumento->numrows > 0) {
            $sequencial = db_utils::fieldsMemory($rs, 0)->p01_sequencial;
            $oProtProcessoDocumento->excluir($sequencial, "p01_c70codlan = {$iCodigoLancamento}");
            if ($oProtProcessoDocumento->erro_status == 0) {
                throw new Exception("Erro[4.1] - Erro ao excluir vinculo do lançamento contábil com os documentos do acordo.");
            }
        }

        /**
         * excluimos a conlancamval
         */
        $oDaoConlancamVal = new cl_conlancamval;
        $sSqlLancamentos = $oDaoConlancamVal->sql_query_file(null, "*", null, "c69_codlan = {$iCodigoLancamento}");
        $rsLancamentos = $oDaoConlancamVal->sql_record($sSqlLancamentos);
        if ($oDaoConlancamVal->numrows > 0) {
            $aLancamentos = db_utils::getCollectionByRecord($rsLancamentos);
            foreach ($aLancamentos as $oLancamento) {
                $oDaoConlancamlr = new cl_conlancamlr;
                $sSqlLancamentoConfig = $oDaoConlancamlr->sql_query_file($oLancamento->c69_sequen);
                $rsLancamentosConfig = $oDaoConlancamlr->sql_record($sSqlLancamentoConfig);
                $aLancamentosConfig = db_utils::getCollectionByRecord($rsLancamentosConfig);
                $oDaoConlancamlr->excluir($oLancamento->c69_sequen);
                if ($oDaoConlancamlr->erro_status == 0) {
                    throw new Exception("Erro[5] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.");
                }

                if (USE_PCASP) {
                    if (!class_exists("ContaCorrenteBase")) {
                        require_once(modification("model/contabilidade/contacorrente/ContaCorrenteBase.model.php"));
                    }

                    /**
                     * Atualizamos o saldo da conta corrente
                     */
                    ContaCorrenteBase::atualizarSaldoContaCorrenteReprocessamento($oLancamento);

                    /**
                     * Após atualizar o saldo da conta corrente podemos excluir o vinculo
                     * do lançamento contábil com a conta corrente
                     */
                    $oDaoContaCorrenteDetalheConLancamVal = new cl_contacorrentedetalheconlancamval;
                    $sWhereExcluirVinculoContaCorrente = "c28_conlancamval = {$oLancamento->c69_sequen}";
                    $oDaoContaCorrenteDetalheConLancamVal->excluir(null, $sWhereExcluirVinculoContaCorrente);
                    if ($oDaoContaCorrenteDetalheConLancamVal->erro_status == "0") {
                        $sMensagemException = "Erro[6] - Erro ao excluir vínculo do lançamento contábil com a conta corrente.\n";
                        $sMensagemException .= $oDaoContaCorrenteDetalheConLancamVal->erro_banco;
                        throw new Exception($sMensagemException);
                    }
                }

                $oDaoConlancamVal->excluir($oLancamento->c69_sequen);
                if ($oDaoConlancamVal->erro_status == 0) {
                    $sErroMensagem = "Erro[7] - Erro ao alterar a data do lançamento contábil!\nErro ao reprocessar lançamentos.\n";
                    $sErroMensagem .= $oDaoConlancamVal->erro_banco;
                    throw new Exception($sErroMensagem);
                }
            }
        }
        /**
         * excluimos a tabela conlancamcgm
         */
        $oConlancamcgm = new cl_conlancamcgm;
        $oConlancamcgm->c76_codlan = $iCodigoLancamento;
        $oConlancamcgm->excluir($iCodigoLancamento);
        if ($oConlancamcgm->erro_status == 0) {
            throw new Exception("Erro[8] - Erro ao excluir vínculo cgm do lançamento contábil.");
        }
        /**
         * excluir a tabela conlancamdoc
         */
        $oConlancamdoc = new cl_conlancamdoc;
        $oConlancamdoc->c71_codlan = $iCodigoLancamento;
        $oConlancamdoc->excluir($iCodigoLancamento);
        if ($oConlancamdoc->erro_status == 0) {
            throw new Exception("Erro[9] - Erro ao exlcuir vínculo documento do lançamento contábil.");
        }


        /**
         * Alteramos a tabela conlancamdig
         */
        $oConlancamdig = new cl_conlancamdig;
        $oConlancamdig->c78_codlan = $iCodigoLancamento;
        $oConlancamdig->excluir($iCodigoLancamento);
        if ($oConlancamdig->erro_status == 0) {
            throw new Exception("Erro[10] - Erro ao excluir vínculo de lote do lançamento contábil.");
        }


        /**
         * Alteramos a tabela conlancamdot
         */
        $oConlancamdot = new cl_conlancamdot;
        $oConlancamdot->c73_codlan = $iCodigoLancamento;
        $oConlancamdot->excluir($iCodigoLancamento);
        if ($oConlancamdot->erro_status == 0) {
            throw new Exception("Erro[11] - Erro ao excluir vínculo da dotação com o lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamemp
         */
        $oConlancamemp = new cl_conlancamemp;
        $oConlancamemp->c75_codlan = $iCodigoLancamento;
        $oConlancamemp->excluir($iCodigoLancamento);
        if ($oConlancamemp->erro_status == 0) {
            throw new Exception("Erro[12] - Erro ao excluir vínculo do empenho com o lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamemp
         */
        $oConlancampag = new cl_conlancampag;
        $oConlancampag->c82_codlan = $iCodigoLancamento;
        $oConlancampag->excluir($iCodigoLancamento);
        if ($oConlancampag->erro_status == 0) {
            throw new Exception("Erro[13] - Erro ao excluir vínculo da conta pagadora com o lançamento contábil.");
        }
        /**
         * Alteramos a tabela conlancamord
         */
        $oConlancamord = new cl_conlancamord;
        $oConlancamord->c80_codlan = $iCodigoLancamento;
        $oConlancamord->excluir($iCodigoLancamento);
        if ($oConlancamord->erro_status == 0) {
            throw new Exception("Erro[14] - Erro ao excluir vínculo da ordem de pagamento com o lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamrec
         */
        $oConlancamrec = new cl_conlancamrec;
        $oConlancamrec->c74_codlan = $iCodigoLancamento;
        $oConlancamrec->excluir($iCodigoLancamento);
        if ($oConlancamrec->erro_status == 0) {
            throw new Exception("Erro[15] - Erro ao excluir vinculo da receita com o lançamento contábil.");
        }

        /**
         * Alteramos a tabela conlancamrec
         */
        $oConlancamele = new cl_conlancamele;
        $oConlancamele->c74_codlan = $iCodigoLancamento;
        $oConlancamele->excluir($iCodigoLancamento);
        if ($oConlancamrec->erro_status == 0) {
            throw new Exception("Erro[16] - Erro ao excluir vinculo do elemento com o lançamento contábil.");
        }
        /**
         * Alteramos a tabela conlancamretif
         */
        $oConlancamretif = new cl_conlancamretif;
        $oConlancamretif->c79_codlan = $iCodigoLancamento;
        $oConlancamretif->excluir($iCodigoLancamento);
        if ($oConlancamretif->erro_status == 0) {
            throw new Exception("Erro[17] - Erro ao excluir vinculo da retificação com o lançamento contábil.");
        }

        /**
         * excluimos  a tabela conlancamcompl
         */
        $oConlancamcompl = new cl_conlancamcompl;
        $oConlancamcompl->c72_codlan = $iCodigoLancamento;
        $oConlancamcompl->excluir($iCodigoLancamento);
        if ($oConlancamcompl->erro_status == 0) {
            throw new Exception("Erro[18] - Erro ao excluir complemento do lançamento contábil.\n{$oConlancamcompl->erro_msg}");
        }

        $oConlancamnota = new cl_conlancamnota;
        $oConlancamnota->c66_codlan = $iCodigoLancamento;
        $oConlancamnota->excluir($iCodigoLancamento);
        if ($oConlancamcompl->erro_status == 0) {
            throw new Exception("Erro[19] - Erro ao excluir dados da nota do lançamento contábil.\n{$oConlancamcompl->erro_msg}");
        }

        /**
         * Exclui a tabela conlancamconcarpeculiar
         */
        $oConlanconcarpeculiar = new cl_conlancamconcarpeculiar;
        $oConlanconcarpeculiar->excluir(null, "c08_codlan = {$iCodigoLancamento}");

        if ($oConlanconcarpeculiar->erro_status == 0) {
            throw new Exception("Erro[20] - Erro ao excluir vinculo da caracteristica peculiar com o lançamento contábil.");
        }

        /**
         * Exclui a tabela conlancaminstit
         */
        $oConlancaminstit = new cl_conlancaminstit;
        $oConlancaminstit->excluir(null, "c02_codlan = {$iCodigoLancamento}");
        if ($oConlancaminstit->erro_status == 0) {
            throw new Exception("Erro[21] - Erro ao excluir vinculo da instituição com o lançamento contábil.");
        }

        $oConlancamOrdem = new cl_conlancamordem;
        $oConlancamOrdem->excluir(null, "c03_codlan = {$iCodigoLancamento}");
        if ($oConlancamOrdem->erro_status == "0") {
            throw new Exception("Erro[22] - Erro ao excluir o vinculo da ordem do lançamento contábil.");
        }

        $oConplanoatributolancamentos = new cl_conplanoatributolancamentos;
        $sqlAtributos = $oConplanoatributolancamentos->sql_query_file(null, "c124_sequencial", null, "c124_lancamento = $iCodigoLancamento");

        $oInfocomplementarvalor = new cl_infocomplementarvalor();
        $oInfocomplementarvalor->excluir(null, "c123_conplanoatributolancamentos in ($sqlAtributos)");
        if ($oInfocomplementarvalor->erro_status == 0) {
            throw new Exception("Erro[23] - Erro ao excluir informações complementares da matriz de saldo contábil.\n{$oInfocomplementarvalor->erro_msg}");
        }

        $oConplanoatributolancamentos->excluir(null, "c124_lancamento = $iCodigoLancamento");
        if ($oConplanoatributolancamentos->erro_status == 0) {
            throw new Exception("Erro[24] - Erro ao excluir lançamento da matriz de saldo contábil.\n{$oConplanoatributolancamentos->erro_msg}");
        }

        $oConlancamdepartamento = new cl_conlancamdepartamento();
        $oConlancamdepartamento->excluir(null, "c128_conlancam = $iCodigoLancamento");
        if ($oConlancamdepartamento->erro_status == 0) {
            throw new Exception("Erro[25] - Erro ao excluir o vínculo do departamento.\n{$oConlancamdepartamento->erro_msg}");
        }

        $daoLancamRecurso = new cl_conlancamrecurso();
        $daoLancamRecurso->excluir(null, "c130_conlancam = {$iCodigoLancamento}");
        if ($daoLancamRecurso->erro_status == '0') {
            throw new Exception("Erro[26] - Erro ao excluir o vinculo com recurso.\n{$daoLancamRecurso->erro_msg}");
        }

        $daoLancamRetencao = new cl_conlancamretencao();
        $daoLancamRetencao->excluir(null, "c127_conlancam = {$iCodigoLancamento}");
        if ($daoLancamRetencao->erro_status == '0') {
            throw new Exception("Erro[27] - Erro ao excluir o vinculo com a retenção.\n{$daoLancamRetencao->erro_msg}");
        }

        $daoLancamAbertura = new cl_conlancamaberturaexercicioorcamento();
        $daoLancamAbertura->excluir(null, "c105_codlan = {$iCodigoLancamento}");
        if ($daoLancamAbertura->erro_status == '0') {
            throw new Exception("Erro[28] - Erro ao excluir o vínculo com com a abertura.\n{$daoLancamAbertura->erro_msg}");
        }
        $rsLogs = db_query("delete from contabilidade.conlancamlogatributos where c134_codlan = {$iCodigoLancamento}");
        if (!$rsLogs) {
            throw new Exception("Erro[29] - Erro ao excluir dados de log do lancamento.");
        }

        $daoConlancamComplementoRecurso = new cl_conlancamcomplementorecurso();
        $daoConlancamComplementoRecurso->excluir(null, "o201_codlan = {$iCodigoLancamento}");
        if ($daoConlancamComplementoRecurso->erro_status == '0') {
            throw new Exception("Erro[30] - Erro ao excluir o complemento de recurso.\n{$daoConlancamComplementoRecurso->erro_msg}");
        }

        $oDaoconlancamcorgrupocorrente = new cl_conlancamcorgrupocorrente;
        $oDaoconlancamcorgrupocorrente->excluir(null, "c23_conlancam = {$iCodigoLancamento}");
        if ($oDaoconlancamcorgrupocorrente->erro_status == '0') {
            throw new Exception("Erro[31] - Erro ao excluir o grupo corrente do lancamento.\n{$daoConlancamComplementoRecurso->erro_msg}");
        }

        $oDaoconlancamcorrente = new cl_conlancamcorrente;
        $oDaoconlancamcorrente->excluir(null, "c86_conlancam = {$iCodigoLancamento}");
        if ($oDaoconlancamcorrente->erro_status == '0') {
            throw new Exception("Erro[31] - Erro ao excluir o grupo corrente do lancamento.\n{$oDaoconlancamcorrente->erro_msg}");
        }


        /*
         * alteramos a data da conlancam
         */
        $oConlancam = new cl_conlancam;
        $oConlancam->c70_codlan = $iCodigoLancamento;
        $oConlancam->excluir($iCodigoLancamento);
        if ($oConlancam->erro_status == 0) {
            throw new Exception("Erro[0] - Erro ao excluir a data do lançamento contábil.\n{$oConlancam->erro_msg}");
        }
    }

    /**
     * Exclui os lançamentos (e tesouraria) referente a apropriação de um lançamento de retenção.
     * @param $iLancamento int Lançamento de retenção.
     *
     * @throws DBException
     */
    public static function excluirLancamentosApropriacao($iLancamento)
    {

        $sCamposGrupoCorrente = "k105_corgrupo";
        $sWhereGrupoCorrente = "c23_conlancam = {$iLancamento} and k105_corgrupotipo in (2, 5)";

        $sSqlGrupoCorrente = "
			select {$sCamposGrupoCorrente}
			from conlancamcorgrupocorrente
				inner join corgrupocorrente on c23_corgrupocorrente = k105_sequencial
			where {$sWhereGrupoCorrente}";

        $sCamposApropriacao = "c70_codlan as lancamento, k105_data as \"data\", k105_autent as autenticacao, k105_id as autenticadora";
        $sWhereApropriacao = "k105_corgrupo in ({$sSqlGrupoCorrente}) and k105_corgrupotipo in (3, 6)  and c70_codlan <> {$iLancamento}";

        $sSqlApropriacao = "
				select {$sCamposApropriacao}
				from conlancam
					inner join conlancamcorgrupocorrente on c70_codlan = c23_conlancam
					inner join corgrupocorrente on c23_corgrupocorrente =  k105_sequencial
					inner join corgrupotipo on k105_corgrupotipo = k106_sequencial
				where {$sWhereApropriacao}
      ";

        $rsApropriacao = db_query($sSqlApropriacao);
        if (!$rsApropriacao) {
            throw new DBException("Houve um erro ao verificar os lançamentos de apropriação.");
        }

        $iLinhas = pg_num_rows($rsApropriacao);
        $aTesourariaExcluida = array();
        for ($i = 0; $i < $iLinhas; $i++) {
            $oStd = db_utils::fieldsMemory($rsApropriacao, $i);

            $sCorrente = $oStd->autenticadora . $oStd->data . $oStd->autenticacao;
            if (empty($aTesourariaExcluida) || !in_array($sCorrente, $aTesourariaExcluida)) {
                ManutencaoTesouraria::excluirTesouraria($oStd->autenticadora, $oStd->data, $oStd->autenticacao);
                $aTesourariaExcluida[] = $sCorrente;
            }
            lancamentoContabil::excluirLancamento($oStd->lancamento);
        }
    }

    /**
     * define qual o objeto que possui os lancamentos necessarios
     *
     * @param objeto $oDadosTransacao objeto cl_translan
     */
    public function setDadosTransacao($oDadosTransacao)
    {
        $this->oTransaction = $oDadosTransacao;
    }


    /**
     * @param $codigoLancamento
     * @throws Exception
     * @return stdClass[]
     */
    public static function getLog($codigoLancamento)
    {

        $buscaLog = self::buscaDadosLog($codigoLancamento, 1);

        if (pg_num_rows($buscaLog) === 0) {
            /* busca os dados da tabela auditoria */
            $buscaLogAuditoria = db_query("
                    select to_char(datahora_servidor , 'dd/mm/YYYY HH24:MI:SS') as data,
                           datahora_servidor as data_original,
                           (select id_usuario from db_usuarios where lower(db_usuarios.login) = lower(db_auditoria.usuario)) as id_usuario,
                           db_auditoria.usuario as login,
                           (mudancas).valor_novo[2]::integer as lancamento
                      from db_auditoria
                     where tabela = 'conlancaminstit'
                       and operacao = 'I'
                       and instit = ".db_getsession('DB_instit')."
                       and (mudancas).valor_novo[2]::integer = {$codigoLancamento}
                     limit 1;
                ");

            if (!$buscaLogAuditoria) {
                throw new Exception("Ocorreu um erro ao consultar o log do lançamento contábil na tabela de auditoria.");
            }

            if (pg_num_rows($buscaLogAuditoria) === 1) {
                $stdRegistro = db_utils::fieldsMemory($buscaLogAuditoria, 0);
                $insertLog = db_query("
                        insert into lancamentoscontabeislog
                             values (nextval('lancamentoscontabeislog_sequencial_seq'), {$codigoLancamento}, {$stdRegistro->id_usuario}, '{$stdRegistro->data_original}', 1);
                    ");
                if (!$insertLog) {
                    throw new Exception("Ocorreu um erro ao migrar o registro de log.");
                }
            }

            /* busca os dados da tabela db_acount */
            if (pg_num_rows($buscaLogAuditoria) === 0) {
                $buscaLogAccount = db_query("
                        select datahr,
                               id_usuario
                          from db_acount
                         where contatu::integer = {$codigoLancamento}
                           and codcam = 20641
                           and codarq = 3718
                         limit 1
                    ");

                if (!$buscaLogAccount) {
                    throw new Exception("Ocorreu um erro ao consultar os dados do db_account.");
                }

                if (pg_num_rows($buscaLogAccount) === 1) {
                    $stdRegistro = db_utils::fieldsMemory($buscaLogAccount, 0);
                    $data = date('Y-m-d', $stdRegistro->datahr) . ' 00:00:00';

                    $insertLog = db_query("
                        insert into lancamentoscontabeislog
                             values (nextval('lancamentoscontabeislog_sequencial_seq'), {$codigoLancamento}, {$stdRegistro->id_usuario}, '{$data}', 1);
                    ");
                    if (!$insertLog) {
                        throw new Exception("Ocorreu um erro ao migrar o registro de log.");
                    }
                }
            }
        }


        $retornoLog = array();
        $buscaLog = self::buscaDadosLog($codigoLancamento);
        for ($i = 0; $i < pg_num_rows($buscaLog); $i++) {
            $stdLog = db_utils::fieldsMemory($buscaLog, $i);

            $tipoMovimento = 'Incluído';
            if ((int)$stdLog->tipo_movimento === 2) {
                $tipoMovimento = 'Reprocessado';
            }

            if ((int)$stdLog->tipo_movimento === 3) {
                $tipoMovimento = 'Excluído';
            }
            $stdLog->tipo_movimento = $tipoMovimento;
            $retornoLog[] = $stdLog;
        }

        return $retornoLog;
    }


    /**
     * @param $codigoLancamento
     * @return bool|resource
     * @throws Exception
     */
    public static function buscaDadosLog($codigoLancamento, $tipoMovimento = null)
    {

        $sql = "
        select db_usuarios.login,
               db_usuarios.nome,
               to_char(lancamentoscontabeislog.data , 'dd/mm/YYYY HH24:MI:SS') as data,
               lancamentoscontabeislog.tipo_movimento
          from lancamentoscontabeislog
               join db_usuarios on db_usuarios.id_usuario = lancamentoscontabeislog.id_usuario
         where lancamentoscontabeislog.codlan = {$codigoLancamento}";

        if (!empty($tipoMovimento)) {
            $sql .= " and tipo_movimento = {$tipoMovimento} ";
        }

        $sql .= " order by lancamentoscontabeislog.data ";

        $buscaLog = db_query($sql);
        if (!$buscaLog) {
            throw new Exception("Ocorreu um erro ao consultar os logs do lançamento contábil.");
        }

        return $buscaLog;
    }


    /**
     * @param $codigo
     * @param null $codigoTipoDocumento
     * @return stdClass[]
     * @throws Exception
     */
    public static function buscaLogPorCodigoDocumento($codigo, $codigoTipoDocumento = null)
    {

        $where = array();
        $sql  = "select c70_codlan ";
        $sql .= "  from conlancam ";
        $sql .= "       join conlancamdoc on c71_codlan = c70_codlan ";
        $sql .= "       join conhistdoc   on c53_coddoc = c71_coddoc ";

        if ($codigoTipoDocumento === 10) {
            $sql .= " join conlancamemp on c75_codlan = c70_codlan ";
            $where[] = " c53_tipo = {$codigoTipoDocumento} ";
            $where[] = " c75_numemp = {$codigo} ";
        }

        if ($codigoTipoDocumento === 20) {
            $sql .= " join conlancamord on c80_codlan = c70_codlan ";
            $where[] = " c53_tipo = {$codigoTipoDocumento} ";
            $where[] = " c80_codord in ({$codigo}) ";
        }

        $sql .= " where " . implode(' and ', $where) . ' order by c70_codlan limit 1';
        $buscaCodigoLancamento = db_query($sql);

        return self::getLog(db_utils::fieldsMemory($buscaCodigoLancamento, 0)->c70_codlan);
    }


    /**
     * @param $reduzido
     * @param $ano
     * @param $instituicao
     * @return int
     * @throws Exception
     */
    public function getCodigoReduzido($reduzido, $ano, $instituicao)
    {

        $instituicaoPrefeitura = InstituicaoRepository::getInstituicaoPrefeitura();

        if ($instituicao === $instituicaoPrefeitura->getCodigo()) {
            return $reduzido;
        }

        $contaAtual = ContaPlanoPCASPRepository::getContaPorReduzido($reduzido, $ano);
        if (empty($contaAtual)) {
            return null;
        }
        $contaDestino = ContaPlanoPCASPRepository::getReduzidoPorContaInstituicao($contaAtual->getCodigoConta(), $ano, $instituicao);
        if (empty($contaDestino)) {
            return null;
        }
        return $contaDestino->getReduzido();
    }
}
