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


require_once(modification("model/contabilidade/GrupoContaOrcamento.model.php"));
require_once(modification("model/contabilidade/DocumentoContabil.model.php"));

/**
 * Model para controle de ordens de pagamento (Notas liquidacao)
 * @package empenho
 */
class ordemPagamento
{

    /**
     * @var string
     */
    const COMPLEMENTO = 'Controle de prestação de contas.';

    /**
     * Codigo da ordem;
     */
    private $iCodOrdem = null;
    /**
     * ano da ordem (variavel da sessao)
     */
    private $iAnousu = null;
    /**
     * envia as string do objeto com urlencode. true para encodificar.
     */
    private $lEncode = false;
    /**
     * data atual da sessao
     */
    private $dtDataUsu = null;

    /**
     * Dao da tabela pagordem;
     */
    private $oDaoPagOrdem = null;

    /**
     * retorno da autenticação
     *
     * @var string
     */
    private $sRetornoAutentica;
    /**
     * Código do movimento da agenda;
     */
    private $iMovimentoAgenda = "null";

    private $sHistorico = null;

    private $iContaRP = "";
    /**
     * Retencoes da nota de liquidacao
     * @var array;
     */
    private $aRetencoes = array();
    /**
     * Codigo do grupo de autenticação
     *
     * @var integer
     */
    private $iCodigoGrupo = null;
    /**
     * Codigo do cheque da agenda
     *
     * @var integer
     */
    public $iCodChequeAgenda = null;
    /**
     * Dados da ordem de pagamento
     *
     * @var _db_fields object
     */
    public $oDadosOrdem = null;

    public $iAnoUsu;
    public $iCodConta;
    public $iCodCheque;

    /**
     * Tipo da Autenticação no caixa;
     *
     * @var integer
     */
    private $iTipoAutentica = null;
    private $iNovoMovimento = null;
    const AUTENTICAR = true;
    const ESTORNAR = false;
    public $iCodLanc;

    /**
     * Valor pago no movimento
     * @var float
     */
    protected $nValorPago = 0;


    /**
     * metodo construtor;
     * @param integer $iCodOrdem codigo da ordem
     */
    public function __construct($iCodOrdem)
    {
        $this->iCodOrdem = $iCodOrdem;
        $this->iAnoUsu = db_getsession("DB_anousu");
        $this->dtDataUsu = date("Y-m-d", db_getsession("DB_datausu"));
        $this->oDaoPagOrdem = new cl_pagordem;
    }

    /**
     * seta o historico do movimento.
     * @param string $sHistorico
     */
    public function setHistorico($sHistorico)
    {
        $this->sHistorico = $sHistorico;
    }

    public function getHistorico()
    {
        return $this->sHistorico;
    }

    /**
     * Seta o urlencode das strings
     * @param boolean $lEncode
     * @return void
     */
    public function setEncode($lEncode)
    {
        $this->lEncode = $lEncode;
    }

    public function getEncode()
    {

        return $this->lEncode;
    }

    /**
     * Seta a conta a set debita/creditado o pagamento;
     * @param integer $iCodConta codigo da conta
     */
    public function setConta($iCodConta)
    {
        $this->iCodConta = $iCodConta;
    }

    /**
     * Retorna a conta selecionado pelo usuario.
     * @return integer
     */
    public function getConta()
    {
        return $this->iCodConta;
    }

    /**
     * Seta o cheque
     * @param integer $iCodCheque codigo do cheque
     */
    public function setCheque($iCodCheque)
    {
        $this->iCodCheque = $iCodCheque;
    }

    /**
     * Retorna o cheque
     * @return integer
     */
    public function getCheque()
    {
        if ($this->iCodCheque == null) {
            $this->iCodCheque = 0;
        }
        return $this->iCodCheque;
    }

    /**
     * Seta o cheque da agenda de pagamento.
     * @param integer $iCodChequeAgenda codigo do cheque da agenda
     */
    public function setChequeAgenda($iCodCheque)
    {
        $this->iCodChequeAgenda = $iCodCheque;
    }

    /**
     * Retorna o cheque da agenda de pagagamento
     * @return integer
     */
    public function getChequeAgenda()
    {

        if ($this->iCodChequeAgenda == null) {
            $this->iCodChequeAgenda = 0;
        }
        return $this->iCodChequeAgenda;
    }

    /**
     * Seta o valor pago
     * @param float $nValorPago valor pago
     */
    public function setValorPago($nValorPago)
    {
        $this->nValorPago = $nValorPago;
    }

    /**
     * retorna o valor pago
     * @return float
     */
    public function getValorPago()
    {
        return $this->nValorPago;
    }

    /**
     * Seta a Data do pagamento
     * @param string $sData Data
     */
    public function setDataUsu($sData)
    {
        $this->dtDataUsu = $sData;
    }

    /**
     * retorna o Data do pagamento
     * @return string
     */
    public function getDataUsu()
    {
        return $this->dtDataUsu;
    }

    /**
     * seta o codigo do movimento da agenda
     * @param integer $iCodMov codigo do movimento da agenda
     * @return void
     *
     */

    public function setMovimentoAgenda($iCodMov)
    {
        $this->iMovimentoAgenda = $iCodMov;
    }

    /**
     * retorna o codigo do movimento da agenda
     * @return integer
     */
    public function getMovimentoAgenda()
    {
        if ($this->iMovimentoAgenda == "") {
            $this->iMovimentoAgenda = "null";
        }
        return $this->iMovimentoAgenda;
    }

    /**
     * @param null $sWhere
     *
     * @return _db_fields|stdClass
     * @throws exception
     */
    public function getDadosOrdem($sWhere = null)
    {
        $sSqlOrdem = "select pagordem.*,pagordemele.*,empnota.*,empempenho.*,empnota.*,  ";
        $sSqlOrdem .= "       case when cgmordem.z01_numcgm is not null then cgmordem.z01_numcgm else cgm.z01_numcgm end as z01_numcgm,";
        $sSqlOrdem .= "       case when cgmordem.z01_nome is not null then cgmordem.z01_nome else cgm.z01_nome end as z01_nome,";
        $sSqlOrdem .= "       case when cgmordem.z01_cgccpf is not null then cgmordem.z01_cgccpf else cgm.z01_cgccpf end as z01_cgccpf";
        $sSqlOrdem .= "  from pagordem                                              ";
        $sSqlOrdem .= "       inner join pagordemele   on e50_codord  = e53_codord  ";
        $sSqlOrdem .= "       inner join empempenho    on e60_numemp  = e50_numemp  ";
        $sSqlOrdem .= "       inner join cgm           on e60_numcgm  = z01_numcgm  ";
        $sSqlOrdem .= "       left  join pagordemnota  on e50_codord  = e71_codord  ";
        $sSqlOrdem .= "       left join pagordemconta on  e49_codord  = e71_codord";
        $sSqlOrdem .= "       left join cgm cgmordem  on  e49_numcgm  = cgmordem.z01_numcgm";
        $sSqlOrdem .= "       left  join empnota       on e71_codnota = e69_codnota ";
        $sSqlOrdem .= " where e50_codord = {$this->iCodOrdem}";
        if ($sWhere != null || $sWhere != '') {
            $sSqlOrdem .= " and {$sWhere}";
        }
        $rsOrdem = $this->oDaoPagOrdem->sql_record($sSqlOrdem);
        if ($rsOrdem) {
            if ($this->oDaoPagOrdem->numrows > 0) {
                $oDadosOrdem = db_utils::fieldsMemory($rsOrdem, 0, false, false, $this->getEncode());
                $this->oDadosOrdem = $oDadosOrdem;
                return $oDadosOrdem;
            } else {
                throw new exception("Ordem ({$this->iCodOrdem}) não Encontrada!");
            }
        } else {
            throw new exception("Erro ao processar dados da ordem.\nTente novamente.\nErro Técnico" . pg_last_error());
        }
    }

    /**
     * Retorna a string da autenticacao da nota de liquidacao.
     *
     * @return string
     */
    public function getRetornoautenticacao()
    {
        return $this->sRetornoAutentica;
    }

    /**
     * Aqui vamos gerar slip de transferência.
     * para Extras: soma as retencoes Extras e lanca a debito a conta extra cadastrada para a conta principal (credito)
     * para Orçamentárias: verifica o recurso se é livre ou não , se NAO FOR LIVRE  gera o slip
     * com débito a contrapartida cadastrada na conta principal (Credito)
     *
     */

    public function gerarSlipDasRetencoesApropriadas($oMovimento = null)
    {
        $oInstituicao = new Instituicao(db_getsession("DB_instit"));
        $oDadosOrdem = $this->getDadosOrdem();
        $oEmpenhoFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($oDadosOrdem->e60_numemp);
        $ParametroContaExtra = ParametroCaixa::getUtilizaContaExtraOrcamentaria(db_getsession("DB_instit"));

        $cgm = $oInstituicao->getCgm()->getCodigo();
        $oContaTesouraria = new contaTesouraria($this->getConta());
        $iContaCredito = $oContaTesouraria->getCodigoConta();
        $iContaDebito = $oContaTesouraria->getContaExtra();

        $sObservacao = "Correspondente a transferencia bancária da OP: ";
        $sObservacao .= "{$this->iCodOrdem}.";

        $nValorSlip = 0;

        $gerarSlipAuto = ParametroCaixa::getParametroMomentoSlipAutomatico(db_getsession("DB_instit"));

        /**
         * Verifica se Tem receita Orcamentaria
         * e nao for recurso Livre , para recurso Livre não gera slip de transferencia
         * se nao for livre e tiver orcamentaria, usamos a debito = contrapartida
         */

        if (!$oEmpenhoFinanceiro->isRecursoLivre() && $gerarSlipAuto != 0) {
            // quando nao for livre gera um segundo somente com as orcamentarias
            $sqlOrcamentario = "
                select coalesce(sum(e23_valorretencao), 0) as total
                   from retencaopagordem
                   inner join retencaoreceitas on e23_retencaopagordem = e20_sequencial
                   join retencaotiporec on e21_sequencial = e23_retencaotiporec
                   join tabrec on e21_receita = k02_codigo
                              and k02_tipo = 'O'
                   where e20_pagordem = {$this->iCodOrdem}
                     and e23_ativo is true
                     and e23_recolhido is true
                ";

            $rsLivre = db_query($sqlOrcamentario);
            if (pg_num_rows($rsLivre) > 0) {
                $valor = db_utils::fieldsMemory($rsLivre, 0)->total;

                if ($valor > 0) {
                    $iContaDebitoContraPartida = $oContaTesouraria->getContrapartida();

                    if ($iContaDebitoContraPartida == $iContaCredito) {
                        $erro = "
                      [2] - Não é permitida a emissão de um Slip debitanto e creditando a mesma conta.\n
                      Verifique no cadastro da conta tesouraria {$iContaCredito}  a configuração \n
                      constante no campo Conta Extra Orçamentaria: \n
                      Menu: DB:FINANCEIRO > Caixa > Cadastros > Contas > Contas Tesouraria
                    ";

                        throw new exception($erro);
                    }

                    if (empty($iContaDebitoContraPartida)) {
                        $erro = "
                      [2] - É necessário configurar no cadastro da conta {$iContaCredito} na tesouraria uma conta extra orçamentária \n
                      para que seja possível realizar os movimentos financeiros dos valores retidos. \n
                      DB:FINANCEIRO > Caixa > Cadastros > Contas > Contas Tesouraria
                    ";
                        throw new exception($erro);
                    }

                    $oSlipRecurso = new \slip();
                    $oSlipRecurso->setContaCredito($iContaCredito);
                    $oSlipRecurso->setContaDebito($iContaDebitoContraPartida);
                    $oSlipRecurso->setNumCgm($cgm);
                    $oSlipRecurso->setHistorico(9700);
                    $oSlipRecurso->setCaracteristicaPeculiarCredito("000");
                    $oSlipRecurso->setCaracteristicaPeculiarDebito("000");
                    $oSlipRecurso->setValor($valor);
                    $oSlipRecurso->setTipoPagamento(3);
                    $oSlipRecurso->setSituacao(1);
                    $oSlipRecurso->setObservacoes("Sem Recurso Livre: " . $sObservacao);
                    $oSlipRecurso->save();
                    \Slip::vincularTipoOperacaoSlip($oSlipRecurso->getSlip(), 17);
                    \Slip::vincularSlipOrdemPagamento($oSlipRecurso->getSlip(), $this->iCodOrdem);
                }
            }
        }
    }

    /**
     *  busca o ano do ultimo lancamento de pagamento de emepenho
     */
    public function getAnoDoUltimoPagamento()
    {

        $sql = <<<SQL

            select extract( year from c80_data) as anopagamento
              from conlancamord
              join conlancamdoc on c71_codlan = c80_codlan
              join conhistdoc on c53_coddoc = c71_coddoc
            where c53_tipo in (30)
              and c80_codord = $this->iCodOrdem
              order by c80_codlan desc
              limit 1;

SQL;

        $rs = db_query($sql);
        if (pg_num_rows($rs) <= 0) {
            throw new exception("Não foi encontrado lançamento de pagamento para a ordem {$this->iCodOrdem}.");
        }
        return db_utils::fieldsMemory($rs, 0)->anopagamento;
    }


    /*
     * Metodo responsavel por gerar os slips ref as retencoes no momento que eh realizada a configuracao dos movimentos
     * na Manutencao de pagamentos.
     * As retencoes sao recolhidas (pagas) e os slips sao gerados.
     */
    public function gerarSlipPorRetencaoConfiguracaoAgenda($oMovimento)
    {
        $oRetorno = new stdClass();
        $oRetorno->erro = false;
        $oRetorno->msg = "";
        $oRetorno->slips = array();

        $aSlips = array();
        $aMovimentosProcessados = array();

        $oDaoRetencaoReceitas = new cl_retencaoreceitas();
        $oDaoEmpAgeMovConta = new cl_empagemovconta();
        $oDaoEmpAgeMovTipoTransmissao = new cl_empagemovtipotransmissao();
        $ParametroCaixa = new ParametroCaixa();

        try {
            if (!db_utils::inTransaction()) {
                throw new exception("Não foi possível encontrar uma transação válida.\nOperação cancelada");
            }

            foreach ($oMovimento->aRetencoes as $oRetencoes) {
                $sCamposRetencaoReceitas = "retencaoreceitas.e23_sequencial as retencao,
                                      retencaopagordem.e20_pagordem as pagordem,
                                      pagordemnota.e71_codnota as empnota,
                                      (select retencaotiporeccgm.e48_cgm
                                         from retencaotiporeccgm
                                        where retencaotiporeccgm.e48_retencaotiporec = retencaotiporec.e21_sequencial) as numcgm,
                                      (select o58_codigo
                                         from orcdotacao
                                        inner join empempenho on e60_coddot = o58_coddot
                                                             and e60_anousu = o58_anousu
                                                             and e60_instit = o58_instit
                                        where e60_numemp = e50_numemp) as recurso,
                                      retencaoreceitas.e23_valorretencao as valor,
                                      {$oRetencoes->iContaPagadora} as conta_credito,
                                      (select tabplan.k02_reduz
                                         from tabplan
                                        where tabplan.k02_codigo = retencaotiporec.e21_receita
                                          and tabplan.k02_anousu = extract(year from retencaopagordem.e20_data)
                                        union
                                       select conplanoreduz.c61_reduz
                                         from taborc
                                              inner join orcreceita                on taborc.k02_codrec = orcreceita.o70_codrec
                                                                                  and taborc.k02_anousu = orcreceita.o70_anousu
                                              inner join orcfontes                 on orcfontes.o57_codfon = orcreceita.o70_codfon
                                                                                  and orcfontes.o57_anousu = orcreceita.o70_anousu
                                              inner join conplanoconplanoorcamento on conplanoconplanoorcamento.c72_conplanoorcamento = orcfontes.o57_codfon
                                                                                  and conplanoconplanoorcamento.c72_anousu = orcfontes.o57_anousu
                                              inner join conplanoreduz             on conplanoreduz.c61_codcon = conplanoconplanoorcamento.c72_conplano
                                                                                  and conplanoreduz.c61_anousu = conplanoconplanoorcamento.c72_anousu
                                                                                  and conplanoreduz.c61_instit = retencaotiporec.e21_instit
                                        where taborc.k02_codigo = retencaotiporec.e21_receita
                                          and taborc.k02_anousu = extract(year from retencaopagordem.e20_data)) as conta_debito";

                $sSqlRetencaoReceitas = $oDaoRetencaoReceitas->sql_query_consulta(
                    null,
                    $sCamposRetencaoReceitas,
                    null,
                    "e23_sequencial = {$oRetencoes->iRetencao} and e27_principal is true"
                );
                $rsDadosRetencaoReceitas = $oDaoRetencaoReceitas->sql_record($sSqlRetencaoReceitas);
                $oDadosRetencoes = db_utils::fieldsMemory($rsDadosRetencaoReceitas, 0);
                $oDadosRetencoes->sHistorico = "";

                /*
                 * Realizamos a configuração e recolhimentodas retencoes
                 * Necessário para poder gerar os slips
                 *
                 */
                if (!in_array($oMovimento->iCodMov, $aMovimentosProcessados)) {
                    /* confirmamos o pagamento das retenções */
                    $oRetencaoNota = new retencaoNota($oDadosRetencoes->empnota);
                    $oRetencaoNota->setINotaLiquidacao($oDadosRetencoes->pagordem);
                    $oRetencaoNota->setCodigoMovimento($oMovimento->iCodMov);
                    $oRetencaoNota->configurarPagamentoRetencoes();

                    $iCodNovoMovimento = $oRetencaoNota->getCodigoNovoMovimento();
                    $oDaoEmpAgeMovConta->sql_record($oDaoEmpAgeMovConta->sql_query_file($iCodNovoMovimento));
                    if ($oDaoEmpAgeMovConta->numrows == 0) {
                        $oDaoEmpAgeMovConta->e98_codmov = $iCodNovoMovimento;
                        $oDaoEmpAgeMovConta->e98_contabanco = $oMovimento->iContaFornecedor;
                        $oDaoEmpAgeMovConta->incluir($iCodNovoMovimento);
                        if ($oDaoEmpAgeMovConta->erro_status == 0) {
                            $msg = "Erro ao vincular a conta do fornecedor ao movimento do empenho\n";
                            $msg .= $oDaoEmpAgeMovConta->erro_msg . "\n";
                            $msg .= "Verifique a conta informada em 'Banco/Ag'";
                            throw new Exception($msg);
                        }
                    }

                    $oDaoEmpAgeMovTipoTransmissao->excluir(null, "e25_empagemov = {$iCodNovoMovimento}");
                    if ($oDaoEmpAgeMovTipoTransmissao->erro_status == 0) {
                        throw new Exception("ERRO [0] - Vinculando Movimento Tipo Transmissao - " . $oDaoEmpAgeMovTipoTransmissao->erro_msg);
                    }

                    $oDaoEmpAgeMovTipoTransmissao->e25_empagemov = $iCodNovoMovimento;
                    $oDaoEmpAgeMovTipoTransmissao->e25_empagetipotransmissao = $ParametroCaixa->getTipoTransmissaoPadrao();
                    $oDaoEmpAgeMovTipoTransmissao->incluir(null);
                    if ($oDaoEmpAgeMovTipoTransmissao->erro_status == 0) {
                        throw new Exception("ERRO [1] - Vinculando Movimento Tipo Transmissao - " . $oDaoEmpAgeMovTipoTransmissao->erro_msg);
                    }

                    /* realizamos o pagamento dos movimentos das retencoes */
                    $oOrdemPagamento = new ordemPagamento($oDadosRetencoes->pagordem);
                    $oOrdemPagamento->setCheque(null);
                    $oOrdemPagamento->setChequeAgenda(null);
                    $oOrdemPagamento->setConta($oMovimento->iContaSaltes);
                    $oOrdemPagamento->setValorPago("0");
                    $oOrdemPagamento->setMovimentoAgenda($iCodNovoMovimento);
                    $oOrdemPagamento->setHistorico($oDadosRetencoes->sHistorico);
                    $oOrdemPagamento->pagarOrdem();

                    $aMovimentosProcessados[] = $oMovimento->iCodMov;
                }

                /*
                 * Geramos os slips das retenções
                 *
                 */
                $oDaoOPAuxiliar = new cl_empageordem();
                $oDaoOPAuxiliar->e42_dtpagamento = date("Y-m-d", db_getsession("DB_datausu"));
                $oDaoOPAuxiliar->incluir(null);

                $oDaoRetencaoCorGrupoCorrente = new cl_retencaocorgrupocorrente();
                $sSqlBuscaNumpreRetencao = $oDaoRetencaoCorGrupoCorrente->sql_query_numpre(
                    null,
                    "k12_numpre",
                    null,
                    "e23_sequencial = {$oDadosRetencoes->retencao}"
                );
                $rsBuscaNumpreRetencao = $oDaoRetencaoCorGrupoCorrente->sql_record($sSqlBuscaNumpreRetencao);
                $oDadosRetencoes->iNumpre = db_utils::fieldsMemory($rsBuscaNumpreRetencao, 0)->k12_numpre;

                $oSlip = new slip();
                $oSlip->addRecurso($oDadosRetencoes->recurso, $oDadosRetencoes->valor);
                $oSlip->setContaCredito($oDadosRetencoes->conta_credito);
                $oSlip->setCaracteristicaPeculiarCredito("000");
                $oSlip->setContaDebito($oDadosRetencoes->conta_debito);
                $oSlip->setCaracteristicaPeculiarDebito("000");
                $oSlip->setValor($oDadosRetencoes->valor);
                $oSlip->setTipoPagamento(2);
                $oSlip->setSituacao(1);
                $oSlip->addArrecadacao($oDadosRetencoes->iNumpre);
                $oSlip->setHistorico(9017);
                $oSlip->setNumCgm($oDadosRetencoes->numcgm);
                $sObservacao = "Referente ao pagamento das retenções geradas para o recurso {$oDadosRetencoes->recurso}";
                $sObservacao .= ", cujo pagamento é ref. a OP {$oDadosRetencoes->pagordem} - seq. retencao {$oDadosRetencoes->retencao}";
                $oSlip->setObservacoes($sObservacao);
                $oSlip->save();

                $iCodigoSlip = $oSlip->getSlip();
                $aSlips[] = $iCodigoSlip;

                $clSlipRetencaoReceitas = new cl_slipretencaoreceitas();
                $clSlipRetencaoReceitas->k206_slip = $iCodigoSlip;
                $clSlipRetencaoReceitas->k206_retencaoreceitas = $oDadosRetencoes->retencao;
                $clSlipRetencaoReceitas->incluir(null);
                if ($clSlipRetencaoReceitas->erro_status == "0") {
                    throw new Exception("ERRO [2] - Vinculando Slip e Retenção" . $clSlipRetencaoReceitas->erro_msg);
                }

                $oDaoNotaOrdem = new cl_empagenotasordem();
                $oDaoNotaOrdem->e43_ordempagamento = $oDaoOPAuxiliar->e42_sequencial;
                $oDaoNotaOrdem->e43_empagemov = $oSlip->getMovimento();
                $oDaoNotaOrdem->e43_autorizado = "true";
                $oDaoNotaOrdem->e43_valor = $oSlip->getValor();
                $oDaoNotaOrdem->incluir(null);
                if ($oDaoNotaOrdem->erro_status == "0") {
                    throw new Exception("ERRO [3] - " . $oDaoNotaOrdem->erro_msg);
                }

                /**
                 * Vinculamos o slip gerado ao tipo Depósito de Diversos - Pagamento
                 */
                if (USE_PCASP) {
                    $oDaoTipoOperacaoVinculo = new cl_sliptipooperacaovinculo();
                    $oDaoTipoOperacaoVinculo->k153_slip = $iCodigoSlip;
                    $oDaoTipoOperacaoVinculo->k153_slipoperacaotipo = 13;
                    $oDaoTipoOperacaoVinculo->incluir($iCodigoSlip);
                    if ($oDaoTipoOperacaoVinculo->erro_status == 0) {
                        $sMensagemErro = "Não foi possível víncular o tipo de operacao ao slip.\n\n";
                        $sMensagemErro .= "Erro Técnico: {$oDaoTipoOperacaoVinculo->erro_msg}";
                        throw new Exception($sMensagemErro);
                    }
                }
            }

            $oRetorno->slips = $aSlips;
        } catch (Exception $oException) {
            $oRetorno->erro = true;
            $oRetorno->msg = $oException->getMessage();
        }

        return $oRetorno;
    }

    /**
     * faz o pagamento da ordem;
     * @return void;
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function pagarOrdem()
    {
        //Testa se existe uma transacao aberta com o banco
        if (!db_utils::inTransaction()) {
            throw new exception("Não foi possível encontrar uma transação válida.\nOperação cancelada");
        }

        $oDadosOrdem = $this->getDadosOrdem();
        $iCodDoc = null;

        //$this->gerarSlipDasRetencoes();

        /*
         * Testa para ver se a nota já está paga.
         *
         */
        if (db_round(($oDadosOrdem->e53_vlrpag + $this->getValorPago()), 2) > db_round($oDadosOrdem->e53_valor, 2)) {
            $sMsg = "Pagamento já foi efetuado.";
            throw new exception($sMsg);
            return false;
        }

        /**
         * Validadmos a data da emissao do cheque
         */

        if ($this->getChequeAgenda() != 0) {
            /**
             *  Validamos a data de emissao do cheque com a data de sessao
             */

            $sSqlEmpAgeConfChe = " select e86_data                                          ";
            $sSqlEmpAgeConfChe .= "   from empageconfche                                     ";
            $sSqlEmpAgeConfChe .= "        inner join empageconf on e86_codmov = e91_codmov  ";
            $sSqlEmpAgeConfChe .= "  where e91_codcheque = {$this->getChequeAgenda()}  and e91_ativo is true ";

            $rsEmpAgeConfChe = db_query($sSqlEmpAgeConfChe);
            $iEmpAgeConfChe = pg_num_rows($rsEmpAgeConfChe);

            if ($iEmpAgeConfChe > 0) {
                $oEmpAgeConfChe = db_utils::fieldsMemory($rsEmpAgeConfChe, 0);
                /**
                 * Veririfica data de emissão do cheque com a data de sessao
                 */

                if (db_strtotime($this->dtDataUsu) < db_strtotime($oEmpAgeConfChe->e86_data)) {
                    $sMsg = "Data atual é menor que a data de emissão do cheque. \n";
                    $sMsg .= "Processamento cancelado.";
                    throw new exception($sMsg);
                    return false;
                }
            }
        }

        /**
         * Validadmos o valor da retencao
         */
        require_once(modification("model/retencaoNota.model.php"));
        if ($oDadosOrdem->e69_codnota != "") {
            $dtSessao = date("Y-m-d");
            $oRetencaoNota = new retencaoNota($oDadosOrdem->e69_codnota);
            $nValorTotalRetencoes = $oRetencaoNota->getValorRetencaoMovimento(
                $this->getMovimentoAgenda(),
                false,
                $this->dtDataUsu
            );
            if ($nValorTotalRetencoes > $oDadosOrdem->e53_valor) {
                $sMsg = "Pagamento da OP {$this->iCodOrdem} não Efetuado.\n";
                $sMsg .= "Valor da Retenção maior que o valor que está sendo pago.\n";
                throw new exception($sMsg);
            }
        }

        /*
         * Somente podemos pagar, se a data do pagamento for maior ou igual a data da nota de liquidacao;
         *
         */
        if (db_strtotime($this->dtDataUsu) < db_strtotime($oDadosOrdem->e60_emiss)
            || $this->iAnoUsu < $oDadosOrdem->e60_anousu) {
            $sMsg = "Data inválida. Data de pagamento menor que a data do empenho.";
            throw new exception($sMsg);
        }

        /**
         * Verificamos se o boletim de caixa já foi processado/liberado.
         * caso esteja, cancelamos o processamento do pagamento.
         */
        $sSqlBoletim = "select *       ";
        $sSqlBoletim .= "  from boletim ";
        $sSqlBoletim .= " where k11_data   = '{$this->dtDataUsu}' ";
        $sSqlBoletim .= "   and k11_instit = " . db_getsession("DB_instit");
        $rsBoletim = db_query($sSqlBoletim);
        $oBoletim = db_utils::fieldsMemory($rsBoletim, 0);

        if ($oBoletim->k11_libera == "t") {
            $dtDiaBoletim = db_formatar($this->dtDataUsu, "d");
            $sMsg = "Pagamento não realizado.\nBoletim do caixa para o dia {$dtDiaBoletim} ";
            $sMsg .= "já liberado para a contabilidade.";
            throw new exception($sMsg);
        }

        if ($oBoletim->k11_lanca == "t") {
            $dtDiaBoletim = db_formatar($this->dtDataUsu, "d");
            $sMsg = "Pagamento não realizado.\nBoletim do caixa para o dia {$dtDiaBoletim} ";
            $sMsg .= "já processado na contabilidade.";
            throw new exception($sMsg);
        }

        /*
         * Verificamos se o empenho ainda tem saldo para fazer o pagamento do empenho.
         */
        $nSaldoEmpenho = (($oDadosOrdem->e60_vlremp - $oDadosOrdem->e60_vlranu) - $oDadosOrdem->e60_vlrpag);
        $nValorPagoTotal = ($this->getValorPago() + $oDadosOrdem->e60_vlrpag);
        if ((float)round($this->getValorPago(), 2) > (float)round($nSaldoEmpenho, 2)) {
            $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
            $sMsg .= "não possui saldo para pagamento.";
            throw new exception($sMsg);
        }
        /*
         * Verificamos qual tipo de Documento deve ser lançado.
         * e verificamos se possui saldo contábil para fazer o pagamento.
         */

        if ($oDadosOrdem->e60_anousu == $this->iAnoUsu) {
            $iCodDoc = 5; //ordem do exercicio;
        } elseif ($oDadosOrdem->e50_anousu == $this->iAnoUsu) {
            //verificamos se está na empresto. geramos um pagamento de RP não processado.
            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                $iCodDoc = 37; //rp não processado
            } else {
                $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                throw new exception($sMsg);
            }
        } elseif ($oDadosOrdem->e50_anousu < $this->iAnoUsu) {
            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                $iCodDoc = 35; //pagamento de rp processado
            } else {
                $sMsg = "erro eu Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                throw new exception($sMsg);
            }
        }

        // Caso nao esteje como nota de liquidação, devemos setar o codigo do documento como 35;
        $oDaoEmpparametro = new cl_empparametro;
        $rsParametro = $oDaoEmpparametro->sql_record($oDaoEmpparametro->sql_query_file($this->iAnoUsu));
        if ($oDaoEmpparametro->numrows > 0) {
            $oParametro = db_utils::fieldsMemory($rsParametro, 0);
            if ($oParametro->e30_notaliquidacao == '') {
                if ($oDadosOrdem->e60_anousu < $this->iAnoUsu) {
                    $iCodDoc = 35;
                } else {
                    $iCodDoc = 5;
                }
            }
        } else {
            if ($oDadosOrdem->e60_anousu < $this->iAnoUsu) {
                $iCodDoc = 35;
            } else {
                $iCodDoc = 5;
            }
        }
        $sSqlVerifica = "
            select fc_verifica_lancamento(
            {$oDadosOrdem->e60_numemp},
                '{$this->dtDataUsu}',
                {$iCodDoc},
                " . $this->getValorPago() . ") as retorno";
        $rsVerifica = db_query($sSqlVerifica);
        $sRetorno = pg_fetch_result($rsVerifica, 0, 0);
        if ((int)substr($sRetorno, 0, 2) > 0) {
            $sErroMsg = "Erro: " . substr($sRetorno, 3);
            throw new exception($sErroMsg);
        }
        /*
         * Iniciamos as alterações necessárias nas tabelas para efetuar o pagamento.
         * (empempenho, empelemento, pagordemele, pagordem)
         */

        $oDaoEmpenho = new cl_empempenho;
        $oDaoEmpenho->e60_numemp = $oDadosOrdem->e60_numemp;
        $oDaoEmpenho->e60_vlrpag = $nValorPagoTotal;
        $oDaoEmpenho->alterar($oDadosOrdem->e60_numemp);
        if ($oDaoEmpenho->erro_status == 0) {
            $sErroMsg = "Erro [1] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpenho->erro_msg}";
            throw new exception($sErroMsg);
        }
        $oDaoEmpElemento = new cl_empelemento;
        /*
         * Verificamos se o empenho possui mais de um elemento.
         * Caso houver, devemos cancelar o processamento, pois
         * um empenho nao pode ter mais de um elemento.
         */

        $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query_file($oDadosOrdem->e60_numemp));
        if ($oDaoEmpElemento->numrows > 1) {
            $sErroMsg = "Erro [2] - Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu}";
            $sErroMsg .= "Possui dois elementos. Processo Cancelado.";
            throw new exception($sErroMsg);
        }
        $oElemento = db_utils::fieldsMemory($rsElemento, 0);
        $oDaoEmpElemento->e64_numemp = $oDadosOrdem->e60_numemp;
        $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
        $oDaoEmpElemento->e64_vlrpag = $nValorPagoTotal;
        $oDaoEmpElemento->alterar($oDadosOrdem->e60_numemp, $oElemento->e64_codele);
        if ($oDaoEmpElemento->erro_status == 0) {
            $sErroMsg = "Erro [3] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpElemento->erro_msg}";
            throw new exception($sErroMsg);
        }

        $oDaoPagOrdemEle = new cl_pagordemele;
        $rsElementoOrdem = $oDaoPagOrdemEle->sql_record(
            $oDaoPagOrdemEle->sql_query_file($oDadosOrdem->e50_codord, $oDadosOrdem->e53_codele)
        );
        if ($oDaoPagOrdemEle->numrows == 0) {
            $sErroMsg = "Erro [4] - Ordem de pagamento {$oDadosOrdem->e50_codord}";
            $sErroMsg .= "não possui elemento cadastrado. Operação cancelada";
            throw new exception($sErroMsg);
        }
        $oPagOrdemEle = db_utils::fieldsMemory($rsElementoOrdem, 0);
        $oDaoPagOrdemEle->e53_vlrpag = $oPagOrdemEle->e53_vlrpag + $this->getValorPago();
        $oDaoPagOrdemEle->e53_codele = $oDadosOrdem->e53_codele;
        $oDaoPagOrdemEle->e53_codord = $oDadosOrdem->e50_codord;


        if (round($oDaoPagOrdemEle->e53_vlrpag, 2) > round($oPagOrdemEle->e53_valor, 2)) {
            throw new exception("Empenho já está pago.");
        }

        $oDaoPagOrdemEle->alterar($oDadosOrdem->e50_codord);
        if ($oDaoPagOrdemEle->erro_status == 0) {
            $sErroMsg = "Erro [5] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoPagOrdemEle->erro_msg}";
            throw new exception($sErroMsg);
        }

        //Iniciamos os lancamentos Contabeis;
        require_once(modification("classes/lancamentoContabil.model.php"));
        $oDaoSaltes = new cl_saltes;
        $rsSaltes = $oDaoSaltes->sql_record($oDaoSaltes->sql_query_file($this->getConta(), "k13_reduz"));
        if ($oDaoSaltes->numrows > 0) {
            $oSaltes = db_utils::fieldsMemory($rsSaltes, 0);
        } else {
            throw new exception("Conta tributária inválida!");
        }

        $oDaoCorGrupo = new cl_corgrupo;
        $oDaoCorGrupo->k104_tipo = 1;
        $oDaoCorGrupo->incluir(null);
        if ($oDaoCorGrupo->erro_status == 0) {
            throw new exception("Erro ao Incluir grupo de autenticação!");
        }
        $this->setCodigoGrupoCorrente($oDaoCorGrupo->k104_sequencial);
        //Apos os lancamentos, lancamos o valor na corrente.
        if ($this->getValorPago() > 0) {
            $oLancam = new LancamentoContabil(
                $iCodDoc,
                $this->iAnoUsu,
                $this->dtDataUsu,
                $this->getValorPago()
            );

            $oLancam->setCgm($oDadosOrdem->e60_numcgm);
            $oLancam->setEmpenho($oDadosOrdem->e60_numemp, $oDadosOrdem->e60_anousu, $oDadosOrdem->e60_codcom);
            $oLancam->setElemento($oElemento->e64_codele);
            if (trim($this->getHistorico()) != "") {
                $oLancam->setComplemento($this->getHistorico());
            }
            $oLancam->setOrdemPagamento($oDadosOrdem->e50_codord);
            $oLancam->setReduz($oSaltes->k13_reduz);
            if ($oDadosOrdem->e60_anousu == $this->iAnoUsu) {
                $sSqlSaldoDot = "select fc_lancam_dotacao({$oDadosOrdem->e60_coddot},";
                $sSqlSaldoDot .= "                         '{$this->dtDataUsu}',";
                $sSqlSaldoDot .= "                          {$iCodDoc},";
                $sSqlSaldoDot .= $this->getValorPago() . ") as dotacao";
                $rsDotacaoSaldo = db_query($sSqlSaldoDot);
                $oSaldoDot = db_utils::fieldsMemory($rsDotacaoSaldo, 0);
                if (substr($oSaldoDot->dotacao, 0, 1) == 0) {
                    throw new exception("Erro na atualização do orçamento \\n " . substr($oSaldoDot->dotacao, 1));
                }
                $oLancam->setDotacao($oDadosOrdem->e60_coddot);
            }

            $oLancam->salvar();
            $this->iCodLanc = $oLancam->getCodigoLancamento();
            $this->iContaRP = $oLancam->iContaEmp;
            $this->autenticar(1, self::AUTENTICAR);


            /**
             * incluimos o lancamento contabil no grupo de autenticações
             */
            $oDaoCorrenteGrupo = new cl_corgrupocorrente;
            $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(null, "*", null, "k105_corgrupo = " . $this->getCodigoGrupoCorrente());
            $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
            if ($oDaoCorrenteGrupo->numrows == 0) {
                throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
            }
            $oDaoConlancamCorrente = new cl_conlancamcorgrupocorrente;
            $oDaoConlancamCorrente->c23_conlancam = $this->iCodLanc;
            $oDaoConlancamCorrente->c23_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
            $oDaoConlancamCorrente->incluir(null);
        }

        $lPrestacaoConta = false;
        $oEmpenhoFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($oDadosOrdem->e60_numemp);
        if (USE_PCASP) {
            $lPrestacaoConta = $oEmpenhoFinanceiro->isPrestacaoContas();
        }

        /**
         * Se o empenho for uma prestacao de contas devemos efetuar lancamento no documento 90 - SUPRIMENTO DE FUNDOS
         */
        if ($this->getValorPago() > 0 && $lPrestacaoConta) {
            $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
            $oContaCorrenteDetalhe->setCredor($oEmpenhoFinanceiro->getCgm());

            $oEventoContabilPrestacaoConta = new EventoContabil(90, $this->iAnoUsu);
            $oLancamentoAuxiliarPrestacaoConta = new LancamentoAuxiliarEmpenho();
            $oLancamentoAuxiliarPrestacaoConta->setObservacaoHistorico(self::COMPLEMENTO);
            $oLancamentoAuxiliarPrestacaoConta->setFavorecido($oEmpenhoFinanceiro->getFornecedor()->getCodigo());
            $oLancamentoAuxiliarPrestacaoConta->setCodigoElemento($oElemento->e64_codele);
            $oLancamentoAuxiliarPrestacaoConta->setCodigoDotacao($oDadosOrdem->e60_coddot);
            $oLancamentoAuxiliarPrestacaoConta->setCaracteristicaPeculiar($oEmpenhoFinanceiro->getCaracteristicaPeculiar());
            $oLancamentoAuxiliarPrestacaoConta->setEmpenhoFinanceiro($oEmpenhoFinanceiro);
            $oLancamentoAuxiliarPrestacaoConta->setNumeroEmpenho($oDadosOrdem->e60_numemp);
            $oLancamentoAuxiliarPrestacaoConta->setValorTotal($this->getValorPago());
            $oLancamentoAuxiliarPrestacaoConta->setContaCorrenteDetalhe($oContaCorrenteDetalhe);
            $oEventoContabilPrestacaoConta->executaLancamento($oLancamentoAuxiliarPrestacaoConta);
        }

        /*
         * Verificamos se a nota possui retencoes.
         * se possuir, fizemos o lancamento do valor da retencao na contabilidade
         * na mesma conta que pagamos o valor ao fornecedor.
         */

        $nValorPagoTotalOrdem = $this->getValorPago();
        require_once(modification("model/retencaoNota.model.php"));
        if ($oDadosOrdem->e69_codnota != "") {
            $dtSessao = date("Y-m-d");
            $lAtualizaObjetoRetorno = true;
            if ($this->getValorPago() > 0) {
                $lAtualizaObjetoRetorno = false;
            }
            $oRetencaoNota = new \retencaoNota($oDadosOrdem->e69_codnota);
            $nValorTotalRetencoes = $oRetencaoNota->getValorRetencaoMovimento(
                $this->getMovimentoAgenda(),
                false,
                $this->dtDataUsu
            );
            $nValorPagoTotalOrdem += $nValorTotalRetencoes;
            if (APROPRIACAO_RETENCAO) {
                $sIpUsuario = db_getsession("DB_ip");
                //rotina que verifica se o ip do usuario irá imprimir autenticar ou naum ira fazer nada
                $oDaoAutentica = new cl_cfautent;
                $sql = $oDaoAutentica->sql_query_file(
                    null,
                    "k11_tipautent",
                    '',
                    "k11_ipterm = '{$sIpUsuario}' and k11_instit = " . db_getsession("DB_instit")
                );
                $rsTipoAutentica = $oDaoAutentica->sql_record($sql);
                if ($oDaoAutentica->numrows > 0) {
                    $this->oAutentica = db_utils::fieldsMemory($rsTipoAutentica, 0);
                } else {
                    $sErroMsg = "Cadastre o ip {$sIpUsuario} como um caixa.";
                    throw new exception($sErroMsg);
                }

                $retornoAutenticacao = $this->apropriarRetencoes($oEmpenhoFinanceiro);
                if ($lAtualizaObjetoRetorno) {
                    $this->sRetornoAutentica = $retornoAutenticacao;
                }
            } else {
                if ($nValorTotalRetencoes > 0) {
                    $oRetencaoNota->setINotaLiquidacao($oDadosOrdem->e50_codord);
                    $sInfoRetencao = "";
                    $oRetencaoNota->setCodigoMovimento($this->getMovimentoAgenda());
                    $aRetencoes = $oRetencaoNota->getRetencoesFromDB($oDadosOrdem->e50_codord, false);

                    foreach ($aRetencoes as $oRetencao) {
                        $sInfoRetencao .= "Referente a {$oRetencao->e21_descricao} no valor " . db_formatar(
                            $oRetencao->e23_valorretencao,
                            "f"
                        );
                        $sInfoRetencao .= " da Nota {$oDadosOrdem->e69_numero}\n";
                    }

                    /*
                     *  Fazemos o lançamento contabil do valor total das retenções
                     */

                    $this->setValorPago($nValorTotalRetencoes);
                    $this->setCheque("");
                    $this->setChequeAgenda("");
                    $oLancam = new LancamentoContabil(
                        $iCodDoc,
                        $this->iAnoUsu,
                        $this->dtDataUsu,
                        $this->getValorPago()
                    );
                    $oLancam->setCgm($oDadosOrdem->e60_numcgm);
                    $oLancam->setComplemento($sInfoRetencao);
                    $oLancam->setEmpenho($oDadosOrdem->e60_numemp, $oDadosOrdem->e60_anousu, $oDadosOrdem->e60_codcom);
                    $oLancam->setElemento($oElemento->e64_codele);
                    $oLancam->setOrdemPagamento($oDadosOrdem->e50_codord);
                    $oLancam->setReduz($oSaltes->k13_reduz);

                    if ($oDadosOrdem->e60_anousu == $this->iAnoUsu) {
                        $sSqlSaldoDot = "select fc_lancam_dotacao({$oDadosOrdem->e60_coddot},";
                        $sSqlSaldoDot .= "                         '{$this->dtDataUsu}',";
                        $sSqlSaldoDot .= "                          {$iCodDoc},";
                        $sSqlSaldoDot .= $this->getValorPago() . ") as dotacao";
                        $rsDotacaoSaldo = db_query($sSqlSaldoDot);
                        $oSaldoDot = db_utils::fieldsMemory($rsDotacaoSaldo, 0);
                        if (substr($oSaldoDot->dotacao, 0, 1) == 0) {
                            throw new exception("Erro na atualização do orçamento \\n " . substr($oSaldoDot->dotacao, 1));
                            return false;
                        }
                        $oLancam->setDotacao($oDadosOrdem->e60_coddot);
                    }
                    $oLancam->salvar();
                    if ($this->iCodLanc == "") {
                        $this->iCodLanc = $oLancam->getCodigoLancamento();
                    }
                    $this->iContaRP = $oLancam->iContaEmp;

                    /*
                     * Verificamos se nao existe uma conta de recurso livre vinculado com a conta.
                     * caso existir, devemos realizar a autenticaççao do recibo nessa conta,
                     * e criar um slip para esse movimetno., creditando esse valor contra a conta
                     * pagadora do empenho.
                     */
                    //Autenticamos como recolhimento de retencao
                    $this->autenticar(2, self::AUTENTICAR, $lAtualizaObjetoRetorno);
                    $oDaoSaltesContapartida = new cl_saltescontrapartida;
                    require_once(modification("std/db_stdClass.php"));
                    $oInstit = db_stdClass::getDadosInstit();
                    $sSqlContrapartida = $oDaoSaltesContapartida->sql_query(
                        null,
                        "*",
                        null,
                        "k103_saltes = " . $this->getConta()
                    );
                    $rsContrapartida = $oDaoSaltesContapartida->sql_record($sSqlContrapartida);
                    if ($oDaoSaltesContapartida->numrows > 0 && $oInstit->prefeitura = "t") {
                        $oContaLivre = db_utils::fieldsMemory($rsContrapartida, 0);
                        $oDaoEmpAgeSlip = new cl_empagemovslips;
                        $oDaoEmpAgeSlip->k107_data = $this->dtDataUsu;
                        $oDaoEmpAgeSlip->k107_empagemov = $this->getMovimentoAgenda();
                        $oDaoEmpAgeSlip->k107_valor = $this->getValorPago();
                        $oDaoEmpAgeSlip->k107_ctadebito = $oContaLivre->k103_contrapartida;
                        $oDaoEmpAgeSlip->k107_ctacredito = $oContaLivre->k103_saltes;
                        $oDaoEmpAgeSlip->incluir(null);
                        if ($oDaoEmpAgeSlip->erro_status == 0) {
                            throw new Exception("Erro Ao incluir informacoes do slip.\n Processamento cancelado.");
                        }
                    }

                    /**
                     * incluimos o lancamento contabil no grupo de autenticações
                     */
                    $oDaoCorrenteGrupo = new cl_corgrupocorrente;
                    $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
                        null,
                        "*",
                        "k105_autent desc limit 1",
                        "k105_corgrupo = " . $this->getCodigoGrupoCorrente()
                    );
                    $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
                    if ($oDaoCorrenteGrupo->numrows == 0) {
                        throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
                    }
                    $oDaoConlancamCorrente = new cl_conlancamcorgrupocorrente;
                    $oDaoConlancamCorrente->c23_conlancam = $oLancam->getCodigoLancamento();
                    $oDaoConlancamCorrente->c23_corgrupocorrente = db_utils::fieldsMemory(
                        $rsCorGrupo,
                        0
                    )->k105_sequencial;
                    $oDaoConlancamCorrente->incluir(null);

                    $oDaoEmpenho = new cl_empempenho;
                    $oDaoEmpenho->e60_numemp = $oDadosOrdem->e60_numemp;
                    $oDaoEmpenho->e60_vlrpag = $nValorPagoTotal + $this->getValorPago();
                    $oDaoEmpenho->alterar($oDadosOrdem->e60_numemp);
                    if ($oDaoEmpenho->erro_status == 0) {
                        $sErroMsg = "Erro [1] - Erro ao pagar empenho.\n";
                        $sErroMsg .= "Erro Técnico: {$oDaoEmpenho->erro_msg}";
                        throw new exception($sErroMsg);
                        return false;
                    }

                    $oDaoEmpElemento = new cl_empelemento;
                    /*
                     * Verificamos se o empenho possui mais de um elemento.
                     * Caso houver, devemos cancelar o processamento, pois
                     * um empenho nao pode ter mais de um elemento.
                     */

                    $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query_file($oDadosOrdem->e60_numemp));
                    if ($oDaoEmpElemento->numrows > 1) {
                        $sErroMsg = "Erro [2] - Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu}";
                        $sErroMsg .= "Possui dois elementos. Processo Cancelado.";
                        throw new exception($sErroMsg);
                        return false;
                    }
                    $oElemento = db_utils::fieldsMemory($rsElemento, 0);
                    $oDaoEmpElemento->e64_numemp = $oDadosOrdem->e60_numemp;
                    $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
                    $oDaoEmpElemento->e64_vlrpag = $nValorPagoTotal + $this->getValorPago();
                    $oDaoEmpElemento->alterar($oDadosOrdem->e60_numemp, $oElemento->e64_codele);
                    if ($oDaoEmpElemento->erro_status == 0) {
                        $sErroMsg = "Erro [3] - Erro ao pagar empenho.\n";
                        $sErroMsg .= "Erro Técnico: {$oDaoEmpElemento->erro_msg}";
                        throw new exception($sErroMsg);
                        return false;
                    }

                    $oDaoPagOrdemEle = new cl_pagordemele;
                    $rsElementoOrdem = $oDaoPagOrdemEle->sql_record(
                        $oDaoPagOrdemEle->sql_query_file($oDadosOrdem->e50_codord, $oDadosOrdem->e53_codele)
                    );
                    if ($oDaoPagOrdemEle->numrows == 0) {
                        $sErroMsg = "Erro [4] - Ordem de pagamento {$oDadosOrdem->e50_codord}";
                        $sErroMsg .= "não possui elemento cadastrado. Operação cancelada";
                        throw new exception($sErroMsg);
                        return false;
                    }
                    $oPagOrdemEle = db_utils::fieldsMemory($rsElementoOrdem, 0);
                    $oDaoPagOrdemEle->e53_vlrpag = $oPagOrdemEle->e53_vlrpag + $this->getValorPago();
                    $oDaoPagOrdemEle->e53_codele = $oDadosOrdem->e53_codele;
                    $oDaoPagOrdemEle->e53_codord = $oDadosOrdem->e50_codord;
                    $oDaoPagOrdemEle->alterar($oDadosOrdem->e50_codord);
                    if ($oDaoPagOrdemEle->erro_status == 0) {
                        $sErroMsg = "Erro [5] - Erro ao pagar empenho.\n";
                        $sErroMsg .= "Erro Técnico: {$oDaoPagOrdemEle->erro_msg}";
                        throw new exception($sErroMsg);
                        return false;
                    }

                    /**
                     * baixamos as Retencoes.
                     */
                    $oRetencaoNota->setGrupoAutenticacao($this->getCodigoGrupoCorrente());
                    $oRetencaoNota->setConta($this->getConta());
                    $oRetencaoNota->setDataBase($this->dtDataUsu);
                    $oRetencaoNota->setNumCgm($oDadosOrdem->z01_numcgm);
                    $oRetencaoNota->baixarRetencoes($aRetencoes);

                    /*
                     * verifica se é prestação de conta e possui retenção
                     * para realizar o lancamento de suprimento de fundo para a retenção
                     */

                    if ($this->getValorPago() > 0 && $lPrestacaoConta && $nValorTotalRetencoes > 0) {
                        // Valida se exite a ContaCorrenteDetalhe, caso nao exista, cria o objeto.
                        if (empty($oContaCorrenteDetalhe)) {
                            $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
                            $oContaCorrenteDetalhe->setCredor($oEmpenhoFinanceiro->getCgm());
                        }
                        $oEventoContabilRetencao = new EventoContabil(90, $this->iAnoUsu);
                        $oLancamentoAuxiliarRetencao = new LancamentoAuxiliarEmpenho();
                        $oLancamentoAuxiliarRetencao->setObservacaoHistorico("Lançamento da retenção de prestação de contas.");
                        $oLancamentoAuxiliarRetencao->setFavorecido($oEmpenhoFinanceiro->getFornecedor()->getCodigo());
                        $oLancamentoAuxiliarRetencao->setCodigoElemento($oElemento->e64_codele);
                        $oLancamentoAuxiliarRetencao->setCodigoDotacao($oDadosOrdem->e60_coddot);
                        $oLancamentoAuxiliarRetencao->setCaracteristicaPeculiar($oEmpenhoFinanceiro->getCaracteristicaPeculiar());
                        $oLancamentoAuxiliarRetencao->setEmpenhoFinanceiro($oEmpenhoFinanceiro);
                        $oLancamentoAuxiliarRetencao->setNumeroEmpenho($oDadosOrdem->e60_numemp);
                        $oLancamentoAuxiliarRetencao->setValorTotal($nValorTotalRetencoes);
                        $oLancamentoAuxiliarRetencao->setContaCorrenteDetalhe($oContaCorrenteDetalhe);
                        $oEventoContabilRetencao->executaLancamento($oLancamentoAuxiliarRetencao);
                    }
                }
            }
        }
        /**
         * Verificamos se a nota faz parte de um pacto
         */
        $this->atualizarSaldoPacto($nValorPagoTotalOrdem, $oDadosOrdem->e69_codnota, $oDadosOrdem->e53_valor);
        return true;
    }


    /**
     * Seta o codigo do grupo;
     *
     * @param integer $iCodigoGrupo
     */
    public function setCodigoGrupoCorrente($iCodigoGrupo)
    {
        $this->iCodigoGrupo = $iCodigoGrupo;
    }

    /**
     * retorna o codigo do grupo da autenticação
     *
     * @return integer
     */
    public function getCodigoGrupoCorrente()
    {
        return $this->iCodigoGrupo;
    }

    /**
     * realiza a autenticação do empenho no caixa.
     *
     * @param integer $iCodigoTipoGrupo Código do tipo
     * @return unknown
     * @throws exception
     */
    public function autenticar($iCodigoTipoGrupo, $lAutenticar, $lAtualizarObjetoAutenticacao = true)
    {

        $iCodigogrupo = $this->getCodigoGrupoCorrente();
        if ($iCodigogrupo == null) {
            throw new Exception("Grupo da autenticação nao informado.\\nProcessamento Cancelado");
        }
        $oDadosOrdem = $this->getDadosOrdem();
        $iCodigoAgenda = $this->getMovimentoAgenda();
        $sIpUsuario = db_getsession("DB_ip");
        //rotina que verifica se o ip do usuario irá imprimir autenticar ou naum ira fazer nada
        $oDaoAutentica = new cl_cfautent;
        $rsTipoAutentica = $oDaoAutentica->sql_record(
            $oDaoAutentica->sql_query_file(
                null,
                "k11_tipautent",
                '',
                "k11_ipterm = '{$sIpUsuario}'
          and k11_instit = " . db_getsession("DB_instit")
            )
        );
        if ($oDaoAutentica->numrows > 0) {
            $this->oAutentica = db_utils::fieldsMemory($rsTipoAutentica, 0);
        } else {
            $sErroMsg = "Cadastre o ip {$sIpUsuario} como um caixa.";
            throw new exception($sErroMsg);
        }
        if ($lAutenticar) {
            $sExecFuncao = "fc_autentemp";
            $nValorAutenticar = $this->getValorPago();
        } else {
            $sExecFuncao = "fc_estornaemp";
            $nValorAutenticar = $this->getValorPago() * -1;
        }
        $this->setTipoAutentica($this->oAutentica->k11_tipautent);
        if ($this->oAutentica->k11_tipautent != 3) {
            if ($oDadosOrdem->e60_anousu < $this->iAnoUsu) {
                /*RESTO A PAGAR*/
                if ($this->iContaRP == "") {
                    throw new exception("Verifique o tipo do RP e o cadastro das transações !");
                    return false;
                } else {
                    $sSqlAut = "select {$sExecFuncao}({$oDadosOrdem->e60_numemp},";
                    $sSqlAut .= $this->getConta() . ", ";
                    $sSqlAut .= "{$this->iContaRP},";
                    $sSqlAut .= "'{$this->dtDataUsu}',";
                    $sSqlAut .= "{$nValorAutenticar},";
                    $sSqlAut .= $this->getCheque() . ",";
                    $sSqlAut .= "'{$sIpUsuario}',";
                    $sSqlAut .= $this->getChequeAgenda() . ",";
                    $sSqlAut .= "{$oDadosOrdem->e50_codord},";
                    $sSqlAut .= db_getsession("DB_instit") . ", {$iCodigoAgenda}, {$iCodigogrupo}, {$iCodigoTipoGrupo}) as retorno";
                }
            } else {
                $sSqlAut = "select {$sExecFuncao}({$oDadosOrdem->e60_numemp},";
                $sSqlAut .= $this->getConta() . ", ";
                $sSqlAut .= "0,";
                $sSqlAut .= "'{$this->dtDataUsu}',";
                $sSqlAut .= "{$nValorAutenticar},";
                $sSqlAut .= $this->getCheque() . ",";
                $sSqlAut .= "'{$sIpUsuario}',";
                $sSqlAut .= $this->getChequeAgenda() . ",";
                $sSqlAut .= "{$oDadosOrdem->e50_codord},";
                $sSqlAut .= db_getsession("DB_instit") . ", {$iCodigoAgenda}, $iCodigogrupo,{$iCodigoTipoGrupo}) as retorno";
                //$ql = "select fc_autentemp($e60_numemp,$k13_conta,0,'".$datausu."','$vlrpag',$k12_cheque,'$ip','$e91_codcheque',$orde,".db_getsession("DB_instit").", $codigomovimento) as retorno";
            }
            $rsAut = $this->oDaoPagOrdem->sql_record($sSqlAut);
            if (!$rsAut) {
                $sErroMsg = "Erro na autenticação do empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu}.\n";
                $sErroMsg .= "Contate suporte." . pg_last_error();
                throw new exception($sErroMsg);
                return false;
            } else {
                $oAut = db_utils::fieldsMemory($rsAut, 0);
                if ($lAtualizarObjetoAutenticacao) {
                    $this->sRetornoAutentica = $oAut->retorno;
                }
                if (substr($oAut->retorno, 0, 1) != '1') {
                    $sErroMsg = $oAut->retorno;
                    throw new exception($sErroMsg);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Retorna o tipo de autenticação
     * @return integer
     */
    public function getTipoAutenticacao()
    {

        return $this->iTipoAutentica;
    }

    /**
     * Define qual o tipo de autenticação do terminal;.
     *
     * @param integer $iTipo
     */
    public function setTipoAutentica($iTipo)
    {
        $this->iTipoAutentica = $iTipo;
    }

    /**
     * estorna os slips gerados automaticamente
     */
    public function estornarSlipsAutomaticos()
    {
        $aSlips = $this->getSlipsGeradosAutomaticamente(2);
        foreach ($aSlips as $iSlipAuto) {
            $smg = "Estorno de Slip Gerado automaticamente da OP {$this->iCodOrdem}";
            $oTransferenciaSlipAutomatico = TransferenciaFactory::getInstance(null, $iSlipAuto);
            $oTransferenciaSlipAutomatico->anular($smg);
            $oTransferenciaSlipAutomatico->executarLancamentoContabil(null, true);
        }
    }

    /**
     * @param string $iCaracteristicaPeculiar
     *
     * @return bool
     * @throws \BusinessException
     * @throws \exception
     */
    public function estornarOrdem($iCaracteristicaPeculiar = null)
    {

        //Testa se existe uma transacao aberta com o banco
        if (!db_utils::inTransaction()) {
            throw new exception("Não foi possível encontrar uma transação válida.\nOperação cancelada");
        }
        $oDadosOrdem = $this->getDadosOrdem();
        $iCodDoc = null;
        $criarMovimento = true;

        /*
         * Somente podemos estornar, se a data do estorno for maior ou igual a data da nota de liquidacao;
         */
        if (db_strtotime($this->dtDataUsu) < db_strtotime($oDadosOrdem->e50_data)
            || $this->iAnoUsu < $oDadosOrdem->e50_anousu) {
            $sMsg = "Data inválida. Data do estorno menor que a data da nota de liquidação.";
            throw new exception($sMsg);
        }


        /*
         * Verificamos se o empenho ainda tem saldo para fazer o pagamento do empenho.
         */
        $nSaldoEstornar = $oDadosOrdem->e60_vlrpag - $this->getValorPago();
        if ($this->getValorPago() > $oDadosOrdem->e60_vlrpag) {
            $sErroMsg = $this->getValorPago() . "Valor solicitado maior que o {$oDadosOrdem->e60_vlrpag} saldo pago do empenho.\nOperação cancelada";
            throw new exception($sErroMsg);
        }
        /*
         * Verificamos o saldo contabil, para poder realizar os lancamentos.
         */
        if ($oDadosOrdem->e60_anousu < $oDadosOrdem->e50_anousu) {
            //verificamos se está na empresto. geramos um estorno pagamento de RP não processado.
            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                $iCodDoc = 38; //rp não processado

                $sqlBuscaPagamento = "
           select conlancam.*
             from conlancam
                  inner join conlancamord on conlancamord.c80_codlan = conlancam.c70_codlan
                  inner join conlancamdoc on conlancamdoc.c71_codlan = conlancamord.c80_codlan
            where conlancamord.c80_codord = {$this->iCodOrdem}
              and conlancamdoc.c71_coddoc in (35)
              and conlancam.c70_valor = {$this->nValorPago}
            order by c70_codlan desc limit 1;
        ";
                $resBuscaPagamento = db_query($sqlBuscaPagamento);
                if (!$resBuscaPagamento) {
                    throw new Exception("Ocorreu um erro ao buscar o lançamento contábil para o pagamento que está sendo estornado.");
                }

                if (pg_num_rows($resBuscaPagamento) === 0) {
                    $iCodDoc = 38;
                } else {
                    $stdLancamento = db_utils::fieldsMemory($resBuscaPagamento, 0);
                    if ($oDadosOrdem->e50_anousu < $stdLancamento->c70_anousu) {
                        $iCodDoc = 36;
                    }
                }
            } else {
                $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                throw new exception($sMsg);
            }
        } elseif ($oDadosOrdem->e60_anousu == $oDadosOrdem->e50_anousu) {
            //se o ano da ordem for igual ao ano corrente, é um estorno de pagamento normal
            if ($oDadosOrdem->e50_anousu == $this->iAnoUsu) {
                $iCodDoc = 6; //ordem do exercicio;
            } else {
                if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                    $iCodDoc = 36; //estorno pagamento de rp processado
                } else {
                    $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                    $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                    throw new exception($sMsg);
                }
            }
        }
        //Caso nao esteje como nota de liquidação, devemos setar o codigo do documento como 35;
        $oDaoEmpparametro = new cl_empparametro;
        $rsParametro = $oDaoEmpparametro->sql_record($oDaoEmpparametro->sql_query_file($this->iAnoUsu));
        if ($oDaoEmpparametro->numrows > 0) {
            $oParametro = db_utils::fieldsMemory($rsParametro, 0);
            if ($oParametro->e30_notaliquidacao == '') {
                if ($oDadosOrdem->e60_anousu < $this->iAnoUsu) {
                    $iCodDoc = 36;
                } else {
                    $iCodDoc = 6;
                }
            }
        } else {
            if ($oDadosOrdem->e60_anousu < $this->iAnoUsu) {
                $iCodDoc = 36;
            } else {
                $iCodDoc = 6;
            }
        }

        $iDocumentoEstorno = $iCodDoc;

        $sSqlVerifica = "select fc_verifica_lancamento({$oDadosOrdem->e60_numemp},
      '{$this->dtDataUsu}',
      {$iCodDoc},
      " . $this->getValorPago() . ") as retorno";
        $rsVerifica = db_query($sSqlVerifica);
        $sRetorno = pg_fetch_result($rsVerifica, 0, 0);
        if ((int)substr($sRetorno, 0, 2) > 0) {
            $sErroMsg = substr($sRetorno, 3);
            throw new exception($sErroMsg . "|" . $sSqlVerifica);
        }

        /*
         * alteramos as tabelas necessárias (empempenho, empelemento, pagordemele)
         */

        $oDaoEmpenho = new cl_empempenho;
        $oDaoEmpenho->e60_numemp = $oDadosOrdem->e60_numemp;
        $oDaoEmpenho->e60_vlrpag = "$nSaldoEstornar";
        $oDaoEmpenho->alterar($oDadosOrdem->e60_numemp);

        if ($oDaoEmpenho->erro_status == 0) {
            $sErroMsg = "Erro [1] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpenho->erro_msg}";
            throw new exception($sErroMsg);
        }

        $oDaoEmpElemento = new cl_empelemento;
        /*
         * Verificamos se o empenho possui mais de um elemento.
         * Caso houver, devemos cancelar o processamento, pois
         * um empenho nao pode ter mais de um elemento.
         */

        $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query_file($oDadosOrdem->e60_numemp));
        if ($oDaoEmpElemento->numrows > 1) {
            $sErroMsg = "Erro [2] - Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu}";
            $sErroMsg .= "Possui dois elementos. Processo Cancelado.";
            throw new exception($sErroMsg);
        }
        $oElemento = db_utils::fieldsMemory($rsElemento, 0);
        $oDaoEmpElemento->e64_numemp = $oDadosOrdem->e60_numemp;
        $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
        $oDaoEmpElemento->e64_vlrpag = "{$nSaldoEstornar}";
        $oDaoEmpElemento->alterar($oDadosOrdem->e60_numemp, $oElemento->e64_codele);
        if ($oDaoEmpElemento->erro_status == 0) {
            $sErroMsg = "Erro [3] - Erro ao estornar pagamento de empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpElemento->erro_msg}";
            throw new exception($sErroMsg);
        }

        $oDaoPagOrdemEle = new cl_pagordemele;
        $rsElementoOrdem = $oDaoPagOrdemEle->sql_record(
            $oDaoPagOrdemEle->sql_query_file($oDadosOrdem->e50_codord, $oDadosOrdem->e53_codele)
        );
        if ($oDaoPagOrdemEle->numrows == 0) {
            $sErroMsg = "Erro [4] - Ordem de pagamento {$oDadosOrdem->e50_codord}";
            $sErroMsg .= "não possui elemento cadastrado. Operação cancelada";
            throw new exception($sErroMsg);
        }

        $oPagOrdemEle = db_utils::fieldsMemory($rsElementoOrdem, 0);
        $nValorEstornar = round($oPagOrdemEle->e53_vlrpag - $this->getValorPago(), 2);
        $oDaoPagOrdemEle->e53_vlrpag = "$nValorEstornar";
        $oDaoPagOrdemEle->e53_codele = $oDadosOrdem->e53_codele;
        $oDaoPagOrdemEle->e53_codord = $oDadosOrdem->e50_codord;
        $oDaoPagOrdemEle->alterar($oDadosOrdem->e50_codord);
        if ($oDaoPagOrdemEle->erro_status == 0) {
            $sErroMsg = "Erro [5] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoPagOrdemEle->erro_msg}";
            throw new exception($sErroMsg);
        }
        //Iniciamos os lancamentos Contabeis;
        require_once(modification("classes/lancamentoContabil.model.php"));
        $oDaoSaltes = new cl_saltes;
        $rsSaltes = $oDaoSaltes->sql_record($oDaoSaltes->sql_query_file($this->getConta(), "k13_reduz"));
        if ($oDaoSaltes->numrows > 0) {
            $oSaltes = db_utils::fieldsMemory($rsSaltes, 0);
        } else {
            throw new exception("Conta tributária inválida!");
        }

        $oLancam = new LancamentoContabil(
            $iCodDoc,
            $this->iAnoUsu,
            $this->dtDataUsu,
            $this->getValorPago()
        );
        $oLancam->setCgm($oDadosOrdem->e60_numcgm);
        $oLancam->setEmpenho($oDadosOrdem->e60_numemp, $oDadosOrdem->e60_anousu, $oDadosOrdem->e60_codcom);
        $oLancam->setElemento($oElemento->e64_codele);
        $oLancam->setOrdemPagamento($oDadosOrdem->e50_codord);
        $oLancam->setReduz($oSaltes->k13_reduz);
        if ($this->getHistorico() != '') {
            $oLancam->setComplemento($this->getHistorico());
        }
        if ($oDadosOrdem->e60_anousu == $this->iAnoUsu) {
            $sSqlSaldoDot = "select fc_lancam_dotacao({$oDadosOrdem->e60_coddot},";
            $sSqlSaldoDot .= "                         '{$this->dtDataUsu}',";
            $sSqlSaldoDot .= "                          {$iCodDoc},";
            $sSqlSaldoDot .= $this->getValorPago() . ") as dotacao";
            $rsDotacaoSaldo = db_query($sSqlSaldoDot);
            $oSaldoDot = db_utils::fieldsMemory($rsDotacaoSaldo, 0);
            if (substr($oSaldoDot->dotacao, 0, 1) == 0) {
                throw new exception("Erro na atualização do orçamento \\n " . substr($oSaldoDot->dotacao, 1));
            }
            $oLancam->setDotacao($oDadosOrdem->e60_coddot);
        }
        $oLancam->salvar();

        $this->iContaRP = $oLancam->iContaEmp;
        $this->iCodLanc = $oLancam->getCodigoLancamento();

        unset($oLancam);

        /**
         * Verificamos se empenho eh uma prestacao de conta
         */
        $oEmpenhoFinanceiro = new EmpenhoFinanceiro($oDadosOrdem->e60_numemp);
        $lPrestacaoConta = $oEmpenhoFinanceiro->isPrestacaoContas();

        if (USE_PCASP && $lPrestacaoConta) {

            /**
             * Query para identificar o tipo de lancamento de extorno a ser utilizada
             * 91 - ESTORNO SUPRIMENTO DE FUNDOS
             * 92 - DEVOLUCAO DE ADIANTAMENTO
             */
            $oDaoEmpPresta = new cl_emppresta;
            $sSqlEmpPresta = $oDaoEmpPresta->sql_query_file(
                null,
                "e45_conferido",
                null,
                "e45_numemp = {$oDadosOrdem->e60_numemp}     "
                . "and e45_codmov = {$this->iMovimentoAgenda}"
            );
            $rsEmpPresta = $oDaoEmpPresta->sql_record($sSqlEmpPresta);

            $dtEncerramento = db_utils::fieldsMemory($rsEmpPresta, 0)->e45_conferido;

            $iDocumentoEstorno = 92;
            $sComplemento = "Lançamento de devolução de adiantamento.";

            if (empty($dtEncerramento)) {
                $iDocumentoEstorno = 91;
                $sComplemento = "Lançamento de estorno de suprimento de fundos.";
            }


            /**
             * quando for uma prestação de contas...devolucao de adiantamentos
             * validar se a devolucao ja nao possui o doc 92
             * problema é se manter duas abas com a rotina de devolução aberta com o mesmo empenho
             * e executar uma , depois a outra sistema faz duas vezes a devolucao...
             * se for prestacao de contas e uma devolucao, foc 92
             * nao precisa criar lancamento novo
             */
            if ($iDocumentoEstorno == 92) {
                $criarMovimento = false;
                $sql = "

                 select c71_codlan, c71_coddoc, e82_codmov 
                   from conlancamord
                   join conlancamdoc on c80_codlan = c71_codlan
                   join empord on e82_codord = c80_codord
                   where e82_codord = {$oDadosOrdem->e50_codord}
                     and c71_coddoc = 92

                ";
                $rsJaExiste = db_query($sql);

                if (pg_num_rows($rsJaExiste) > 0) {
                    $erro = "Ja existe o Lançamento de devolução de adiantamento (92) para o empenho ";
                    $erro .= "{$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} Mov: {$this->getMovimentoAgenda()}";
                    throw new Exception($erro);
                }
            }


            $oEventoContabil = new EventoContabil($iDocumentoEstorno, $this->iAnoUsu);
            $oLancamentoAuxiliar = new LancamentoAuxiliarEmpenho();
            $oLancamentoAuxiliar->setObservacaoHistorico($sComplemento);
            $oLancamentoAuxiliar->setFavorecido($oEmpenhoFinanceiro->getFornecedor()->getCodigo());
            $oLancamentoAuxiliar->setCodigoElemento($oElemento->e64_codele);
            $oLancamentoAuxiliar->setCodigoOrdemPagamento($oDadosOrdem->e50_codord);
            $oLancamentoAuxiliar->setCodigoDotacao($oDadosOrdem->e60_coddot);
            $oLancamentoAuxiliar->setCaracteristicaPeculiar($oEmpenhoFinanceiro->getCaracteristicaPeculiar());
            $oLancamentoAuxiliar->setEmpenhoFinanceiro($oEmpenhoFinanceiro);
            $oLancamentoAuxiliar->setNumeroEmpenho($oDadosOrdem->e60_numemp);
            $oLancamentoAuxiliar->setValorTotal($this->getValorPago());

            $oEventoContabil->executaLancamento($oLancamentoAuxiliar);
        }
        // Fim dos lancamento de prestacao de conta


        $oDaoCorGrupo = new cl_corgrupo;
        $oDaoCorGrupo->k104_tipo = 1;
        $oDaoCorGrupo->incluir(null);
        if ($oDaoCorGrupo->erro_status == "0") {
            throw new exception("Erro ao Incluir grupo de autenticação!");
        }
        $this->setCodigoGrupoCorrente($oDaoCorGrupo->k104_sequencial);
        $this->autenticar(4, self::ESTORNAR);
        $oDaoCorrenteGrupo = new cl_corgrupocorrente();
        $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
            null,
            "*",
            null,
            "k105_corgrupo = " . $this->getCodigoGrupoCorrente() . "
                                                            and k105_corgrupotipo = 4"
        );
        $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
        if ($oDaoCorrenteGrupo->numrows == 0) {
            throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
        }
        $oDaoConlancamCorrente = new cl_conlancamcorgrupocorrente();
        $oDaoConlancamCorrente->c23_conlancam = $this->iCodLanc;
        $oDaoConlancamCorrente->c23_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
        $oDaoConlancamCorrente->incluir(null);

        if ($oDaoConlancamCorrente->erro_status == "0") {
            throw new Exception("Ocorreu um erro ao salvar o grupo grupo da tesouraria.");
        }



        $existeRetencaoMovimentoAgenda = false;
        $sqlVerificaRetencao = "select e27_retencaoreceitas 
                                  from retencaoempagemov 
                                 where e27_empagemov = {$this->getMovimentoAgenda()}";
        $rsVerificaRetencao = db_query($sqlVerificaRetencao);
        if (pg_num_rows($rsVerificaRetencao) > 0) {
            $existeRetencaoMovimentoAgenda = true;
        }

        /**
         * para o doc 92 devolução de adiantamento
         * paramos de criar movimento novo e zerar o atual, ele se perde na prestacao se fizer isso
         * cria uma nova e nao coloca o item que havia sido prestado...
         * aí fica como se so tivesse devolvido o saldo
         */
        if ($this->getMovimentoAgenda() != "null" && $iDocumentoEstorno != 92) {
            /*
             * Se o parametro APROPRIACAO_RETENCAO estiver ativo, o movimento sera cancelado
             * Senao, serao realizadas algumas verificacoes:
             *
             *  - Se existir retencao para o movimento da agenda:
             *     - Alteramos o valor desse movimento para o total pago das retencoes.
             *  - Se NAO existir retencao:
             *     - Cancelamos o movimento.
             */
            if (APROPRIACAO_RETENCAO) {
                $oDaoEmpAgeMov = new cl_empagemov;
                $oDaoEmpAgeMov->e81_cancelado = date('Y-m-d', db_getsession('DB_datausu'));
                $oDaoEmpAgeMov->e81_codmov = $this->getMovimentoAgenda();
                $oDaoEmpAgeMov->alterar($this->getMovimentoAgenda());
                if ($oDaoEmpAgeMov->erro_status == 0) {
                    throw new Exception("Não Foi possivel cancelar Movimento da agenda");
                }
            } else {
                if ($existeRetencaoMovimentoAgenda) {
                    $oDaoEmpAgeMov = new cl_empagemov;
                    $oDaoEmpAgeMov->e81_valor = $nValorEstornar;
                    $oDaoEmpAgeMov->e81_codmov = $this->getMovimentoAgenda();
                    $oDaoEmpAgeMov->alterar($this->getMovimentoAgenda());
                    if ($oDaoEmpAgeMov->erro_status == 0) {
                        throw new Exception("Não Foi possivel cancelar Movimento da agenda");
                    }
                } else {
                    $oDaoEmpAgeMov = new cl_empagemov;
                    $oDaoEmpAgeMov->e81_cancelado = date('Y-m-d', db_getsession('DB_datausu'));
                    $oDaoEmpAgeMov->e81_codmov = $this->getMovimentoAgenda();
                    $oDaoEmpAgeMov->alterar($this->getMovimentoAgenda());
                    if ($oDaoEmpAgeMov->erro_status == 0) {
                        throw new Exception("Não Foi possivel cancelar Movimento da agenda");
                    }
                }
            }
        }

        $aWhere = array(
            "e82_codord = {$this->iCodOrdem}",
            "e97_codmov is null",
            "e85_codmov is null",
            "e43_empagemov is null",
            "e81_cancelado is null",
            "k12_sequencial is null"
        );

        $oDaoMovimentoPago = new cl_empagemov();
        $sSqlMovimentoPago = $oDaoMovimentoPago->sql_query_movimentos_desconto("e81_codmov, e81_valor", implode(' and ', $aWhere));
        $rsMovimento = db_query($sSqlMovimentoPago);
        if (!$rsMovimento) {
            throw new Exception("Ocorreu um erro ao buscar os movimentos em aberto para pagamento.");
        }

        if (pg_num_rows($rsMovimento) > 0) {
            $oMovimento = db_utils::fieldsMemory($rsMovimento, 0);
            $this->iNovoMovimento = $oMovimento->e81_codmov;
            $oDaoEmpAgeMov = new cl_empagemov;
            $oDaoEmpAgeMov->e81_valor = $oMovimento->e81_valor + $this->getValorPago();
            $oDaoEmpAgeMov->e81_codmov = $oMovimento->e81_codmov;
            $oDaoEmpAgeMov->alterar($oMovimento->e81_codmov);
            if ($oDaoEmpAgeMov->erro_status == "0") {
                throw new Exception("Ocorreu um erro ao atualizar o movimento existente para pagamento.");
            }
        } else {
            require_once(modification('model/agendaPagamento.model.php'));
            $oAgendaPagamento = new agendaPagamento();
            $oNovoMovimento = new stdClass();
            $oNovoMovimento->iCodTipo = null;
            $oNovoMovimento->iNumEmp = $oDadosOrdem->e50_numemp;
            $oNovoMovimento->nValor = $this->getValorPago();
            $oNovoMovimento->iCodNota = $oDadosOrdem->e50_codord;
            if (isset($iCaracteristicaPeculiar) && $iCaracteristicaPeculiar != null) {
                $oNovoMovimento->iConcarPeculiar = $iCaracteristicaPeculiar;
            }
            if ($criarMovimento) {

                $this->iNovoMovimento = $oAgendaPagamento->addMovimentoAgenda(1, $oNovoMovimento);
            }
        }

        $this->atualizarSaldoPacto($this->getValorPago() * -1, $oDadosOrdem->e69_codnota, $this->oDadosOrdem->e53_valor);

        return true;
    }


    /**
     * @param        $oNota
     * @param        $nValor
     * @param string $sMotivo
     *
     * @return bool
     * @throws Exception
     */
    public function desconto($oNota, $nValor, $sMotivo = '')
    {

        $oDadosOrdem = $this->getDadosOrdem();

        $oDaoEmpord = new cl_empord;
        $sCampos = 'e90_cancelado, e91_ativo, e90_codgera, e91_cheque';
        $sSqlEmpord = $oDaoEmpord->sql_query_validaDescontoMovimento($oDadosOrdem->e50_codord, $sCampos);
        $rsEmpord = $oDaoEmpord->sql_record($sSqlEmpord);

        /**
         * Verifica se existe cheque ou arquivo gerado
         * - Caso encontre lanca uma excecao
         */
        if ($oDaoEmpord->numrows > 0) {
            $iEmpOrd = $oDaoEmpord->numrows;

            for ($iIndice = 0; $iIndice < $iEmpOrd; $iIndice++) {
                $oEmpord = db_utils::fieldsMemory($rsEmpord, $iIndice);
                $sMensagemErro = "Não é possivel lançar nota de liquidação de desconto\n";

                if ($oEmpord->e90_cancelado == 'f') {
                    throw new Exception($sMensagemErro . 'Existe arquivo gerado. Número do arquivo: ' . $oEmpord->e90_codgera);
                }

                if ($oEmpord->e91_ativo == 't') {
                    throw new Exception($sMensagemErro . 'Existe cheque emitido. Código do cheque: ' . $oEmpord->e91_cheque);
                }
            }
        }

        $iCodDoc = null;
        $iCodNota = $oNota->e69_codnota;

        if (!db_utils::inTransaction()) {
            throw new exception("Não foi possível encontrar uma transação válida.\nOperação cancelada");
        }


        $oDaoLancamentosAutonomos = new cl_rhautonomolanc;
        $sSqlVerificaAutonomos = $oDaoLancamentosAutonomos->sql_query_file(
            null,
            "rh89_anousu,
                                                                           rh89_mesusu,
                                                                           rh89_valorretinss",
            null,
            "rh89_codord={$oDadosOrdem->e50_codord}"
        );

        $rsVerificaAutonomos = $oDaoLancamentosAutonomos->sql_record($sSqlVerificaAutonomos);

        if ($oDaoLancamentosAutonomos->numrows > 0) {
            $sMsgNotasVinculadas = "Estorno da liquidação das notas selecionadas não foram efetuadas.\n";
            $sMsgNotasVinculadas .= "As notas abaixo estão vinculadas a geração da Sefip:\n";
            $aNotasVinculadas = db_utils::getCollectionByRecord($rsVerificaAutonomos);
            foreach ($aNotasVinculadas as $oNotaVinculada) {
                $sMsgNotasVinculadas .= "OP : {$oDadosOrdem->e50_codord}  ";
                $sMsgNotasVinculadas .= "Período: {$oNotaVinculada->rh89_mesusu}/{$oNotaVinculada->rh89_anousu} ";
                $sMsgNotasVinculadas .= "Valor INSS: " . db_formatar($oNotaVinculada->rh89_valorretinss, "f") . "\n";
            }
            $sMsgNotasVinculadas .= "Entre em contato com o setor de Recursos Humanos para procederem ";
            $sMsgNotasVinculadas .= "com o cancelamento da Sefip.";
            unset($oNotaVinculada);
            unset($aNotasVinculadas);
            throw new Exception($sMsgNotasVinculadas);
        }

        /*
         * Somente podemos estornar, se a data do estorno for maior ou igual a data da nota de liquidacao;
         */
        if (db_strtotime($this->dtDataUsu) < db_strtotime($oDadosOrdem->e50_data)
            || $this->iAnoUsu < $oDadosOrdem->e50_anousu) {
            $sMsg = "Data inválida. Data do desconto menor que a data da nota de liquidação.";
            throw new exception($sMsg);
        }
        /*
         * Verificamos o saldo contabil, para poder realizar os lancamentos.
         * anulamos a liquidação.
         */
        if ($oDadosOrdem->e60_anousu < $oDadosOrdem->e50_anousu) {
            //verificamos se está na empresto. geramos um estorno pagamento de RP não processado.
            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                $iCodDoc = 34; //rp não processado
            } else {
                $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                throw new exception($sMsg);
            }
        } elseif ($oDadosOrdem->e60_anousu == $oDadosOrdem->e50_anousu) {
            //se o ano da ordem for igual ao ano corrente, é um estorno de pagamento normal
            if ($oDadosOrdem->e50_anousu == $this->iAnoUsu) {
                $iCodDoc = 4; //do exercicio;
            } else {
                if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                    if ($oDadosOrdem->e60_anousu == $oDadosOrdem->e50_anousu) {
                        $iCodDoc = 31; //estorno de liquidacao
                    } else {
                        $iCodDoc = 34;
                    }
                } else {
                    $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                    $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                    throw new exception($sMsg);
                }
            }
        }
        $sql = "select fc_verifica_lancamento(" . $oDadosOrdem->e60_numemp . ",'" .
            date("Y-m-d", db_getsession("DB_datausu")) .
            "'," . $iCodDoc . "," . $nValor . ") as teste";
        $result = db_query($sql);
        $status = pg_fetch_result($result, 0, "teste");
        if (substr($status, 0, 2) > 0) {
            $sErroMsg = "Validação (codigo: fc_verifica_lançamento) " . substr($status, 3);
            throw new exception($sErroMsg);
        }
        //primeiro atualizamos a tabelas necessárias, (empempenho, empelemento, empnotalem pagordemele.)
        $oDaoEmpenho = new cl_empempenho;
        $rsEmpenho = $oDaoEmpenho->sql_record($oDaoEmpenho->sql_query($oDadosOrdem->e60_numemp));
        if ($oDaoEmpenho->numrows == 0) {
            throw new exception("Empenho não encontrado");
        }

        $oEmpenho = db_utils::fieldsMemory($rsEmpenho, 0);
        $nValorLiq = ($oEmpenho->e60_vlrliq - $nValor);
        $nValorAnu = ($oEmpenho->e60_vlranu + $nValor);
        $oDaoEmpenho->e60_vlrliq = "{$nValorLiq}";
        $oDaoEmpenho->e60_vlranu = "{$nValorAnu}";
        $oDaoEmpenho->e60_numemp = $oEmpenho->e60_numemp;
        $oDaoEmpenho->alterar($oEmpenho->e60_numemp);
        if ($oDaoEmpenho->erro_status == 0) {
            throw new exception("Erro[1] - Não foi possível alterar Empenho.\nErro tecnico:" . pg_last_error());
        }
        $oDaoEmpElemento = new cl_empelemento;
        $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query($oEmpenho->e60_numemp));
        if ($oDaoEmpElemento->numrows == 0) {
            throw new exception("Erro[2] - Não foi possível encontrar elemento do empenho");
        }

        $oElemento = db_utils::fieldsMemory($rsElemento, 0);
        $nValorLiq = $oElemento->e64_vlrliq - $nValor;
        $nValorAnu = $oElemento->e64_vlranu + $nValor;
        $oDaoEmpElemento->e64_vlrliq = "{$nValorLiq}";
        $oDaoEmpElemento->e64_vlranu = "$nValorAnu";
        $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
        $oDaoEmpElemento->e64_numemp = $oElemento->e64_numemp;
        $oDaoEmpElemento->alterar($oElemento->e64_numemp, $oElemento->e64_codele);
        if ($oDaoEmpElemento->erro_status == 0) {
            throw new exception("Erro[3] - Não foi possível alterar elemento do empenho");
        }

        $oDaoEmpNotaEle = new cl_empnotaele;
        $rsEmpNotaEle = $oDaoEmpNotaEle->sql_record($oDaoEmpNotaEle->sql_query_file($iCodNota));
        if ($oDaoEmpNotaEle->numrows == 0) {
            throw new exception("Erro[5] - Nota de liquidação sem Elemento");
        }

        $oEmpNotaEle = db_utils::fieldsMemory($rsEmpNotaEle, 0);
        $nValorLiquidado = $oEmpNotaEle->e70_vlrliq - $nValor;
        $nValorAnulado = $oEmpNotaEle->e70_vlranu + $nValor;
        $oDaoEmpNotaEle->e70_vlrliq = $nValorLiquidado;
        $oDaoEmpNotaEle->e70_vlranu = $nValorAnulado;
        $oDaoEmpNotaEle->e70_codele = $oEmpNotaEle->e70_codele;
        $oDaoEmpNotaEle->e70_codnota = $oEmpNotaEle->e70_codnota;
        $oDaoEmpNotaEle->alterar($iCodNota, $oEmpNotaEle->e70_codele);
        if ($oDaoEmpNotaEle->erro_status == 0) {
            throw new exception("Erro[8] - nao foi possivel alterar elemento.");
        }

        $oDaoEmpNotaItem = new cl_empnotaitem;

        foreach ($oNota->aItens as $oDadosItem) {
            if ($oDadosItem->nTotalDesconto > 0) {
                $sBuscaDadosItem = $oDaoEmpNotaItem->sql_query($oDadosItem->e72_sequencial);
                $rsBuscaDadosItem = $oDaoEmpNotaItem->sql_record($sBuscaDadosItem);
                $oRetornoDadosItem = db_utils::fieldsMemory($rsBuscaDadosItem, 0);
                $nValorAnulado = $oRetornoDadosItem->e72_vlranu + $oDadosItem->nTotalDesconto;

                $oDaoEmpNotaItem->e72_sequencial = $oDadosItem->e72_sequencial;
                $oDaoEmpNotaItem->e72_vlranu = $nValorAnulado;
                $oDaoEmpNotaItem->alterar($oDadosItem->e72_sequencial);
                if ($oDaoEmpNotaItem->erro_status == 0) {
                    throw new Exception('Erro[5] - Não foi possivel anular Item');
                }
            }
        }


        $oDaoPagOrdemEle = new cl_pagordemele;
        $rsPagOrdemEle = $oDaoPagOrdemEle->sql_record($oDaoPagOrdemEle->sql_query_file($this->iCodOrdem));
        if ($oDaoPagOrdemEle->numrows == 0) {
            throw new exception("Erro[5] - Nota de liquidação sem Elemento");
        }

        $oPagOrdemEle = db_utils::fieldsMemory($rsPagOrdemEle, 0);
        $nValorAnu = round($oPagOrdemEle->e53_vlranu + $nValor, 2);
        $oDaoPagOrdemEle->e53_vlranu = "$nValorAnu";
        $oDaoPagOrdemEle->e53_codele = $oPagOrdemEle->e53_codele;
        $oDaoPagOrdemEle->e53_codord = $this->iCodOrdem;
        $oDaoPagOrdemEle->alterar($this->iCodOrdem, $oDaoPagOrdemEle->e53_codele);
        if ($oDaoPagOrdemEle->erro_status == 0) {
            throw new exception("Erro[4] - Não foi possível alterar a nota de liquidacao!");
        }


        //Alteração do Valor do Movimento
        $oDaoEmpAgeMov = new cl_empagemov();
        $sWhere = " e53_codord = {$this->iCodOrdem}     ";
        $sWhere .= " and e97_codforma is null            ";
        $sWhere .= " and corempagemov.k12_codmov is null ";
        $sWhere .= " and e81_cancelado is null           ";
        $nValorTotalDescontoFinal = round($nValor, 2);
        $nValorTotalDescontoMenosValorMovimento = round($nValor, 2);

        $rsDaoEmpAgeMov = $oDaoEmpAgeMov->sql_record($oDaoEmpAgeMov->sql_query_consemp(null, "e81_codmov, e81_valor", "e81_valor", $sWhere));
        if ($oDaoEmpAgeMov->numrows > 0) {
            for ($x = 0; $x < $oDaoEmpAgeMov->numrows; $x++) {
                $oEmpAgeMov = db_utils::fieldsMemory($rsDaoEmpAgeMov, $x);
                if (round($nValorTotalDescontoFinal, 2) > 0) {
                    $nValorTotalDescontoMenosValorMovimento = round($nValorTotalDescontoMenosValorMovimento, 2) - round($oEmpAgeMov->e81_valor, 2);
                    $oDaoEmpAgeMov->e81_codmov = $oEmpAgeMov->e81_codmov;
                    $oDaoEmpAgeMov->e81_cancelado = '';
                    $oDaoEmpAgeMov->e81_valor = $oEmpAgeMov->e81_valor - $nValor;

                    if ($oEmpAgeMov->e81_valor == $nValorTotalDescontoFinal) {
                        $oDaoEmpAgeMov->e81_cancelado = date("Y-m-d", db_getsession('DB_datausu'));
                        $oDaoEmpAgeMov->e81_codmov = $oEmpAgeMov->e81_codmov;
                        $oDaoEmpAgeMov->e81_valor = $oEmpAgeMov->e81_valor;
                        $oDaoEmpAgeMov->alterar($oEmpAgeMov->e81_codmov);
                    }

                    $oDaoEmpAgeMov->alterar($oEmpAgeMov->e81_codmov);
                    if ($oDaoEmpAgeMov->erro_status == "0") {
                        throw new Exception("Ocorreu um erro ao atualizar os valores do movimento existente na agenda de pagamento.");
                    }
                }
            }
            if (round($nValorTotalDescontoMenosValorMovimento, 2) > 0) {
                throw new exception("Erro[8] - Valor do Movimento menor que o valor de desconto [" . round($nValorTotalDescontoMenosValorMovimento, 2) . "]");
            }
        }

        //Fim da alteração do valor do Movimento


        $oDaoPagOrdemDesconto = new cl_pagordemdesconto;
        $oDaoPagOrdemDesconto->e34_codord = $this->iCodOrdem;
        $oDaoPagOrdemDesconto->e34_data = $this->dtDataUsu;
        $oDaoPagOrdemDesconto->e34_valordesconto = "$nValor";
        $oDaoPagOrdemDesconto->e34_motivo = $sMotivo;
        $oDaoPagOrdemDesconto->incluir(null);
        if ($oDaoPagOrdemDesconto->erro_status == 0) {
            throw new exception("Erro[5] - Não foi possível incluir desconto do empenho [ET]" . pg_last_error());
        }

        require_once(modification("classes/lancamentoContabil.model.php"));

        /*se o documento e diferente de 32, (estorno RP processado) devemos fazer o lancamento de estorno de liquicao.
         * conforme cada elemento;
         */
        $oEmpenhoFinanceiro = new EmpenhoFinanceiro($oDadosOrdem->e60_numemp);
        $lPrestacaoConta = $oEmpenhoFinanceiro->isPrestacaoContas();
        $lProvisaoFerias = $oEmpenhoFinanceiro->isProvisaoFerias();
        $lProvisaoDecimoTerceiro = $oEmpenhoFinanceiro->isProvisaoDecimoTerceiro();
        $lAmortizacaoDivida = $oEmpenhoFinanceiro->isAmortizacaoDivida();
        $lPrecatoria = $oEmpenhoFinanceiro->isPrecatoria();
        $lPassivo = $oEmpenhoFinanceiro->isEmpenhoPassivo();
        $oCodigoGrupoContaOrcamento = GrupoContaOrcamento::getGrupoConta(
            $oEmpenhoFinanceiro->getDesdobramentoEmpenho(),
            $this->iAnoUsu
        );

        if ($iCodDoc != 31) {
            if ($this->iAnoUsu == $oEmpenho->e60_anousu) {
                if (substr($oEmpenho->o56_elemento, 0, 2) == '33') {
                    $iCodDoc = '4'; // despesa corrente
                } elseif (substr($oEmpenho->o56_elemento, 0, 2) == '34') {
                    $iCodDoc = '24'; //estorno de despesa capital
                }
                /**
                 * Verificamos se o empenho eh prestacao de contas
                 * Se usar o documento de estorno: 413 - ESTORNO DE LIQUIDACAO SUPRIMENTO DE FUNDOS
                 */
                if (USE_PCASP) {
                    if ($lPrestacaoConta) {
                        $iCodDoc = 413;
                    }
                    if ($lPassivo) {
                        $iCodDoc = 85;
                    }
                    if ($lPrecatoria) {
                        $iCodDoc = 503;
                    }
                    if ($lAmortizacaoDivida) {
                        $iCodDoc = 507;
                    }

                    if ($oCodigoGrupoContaOrcamento && !$lPassivo && !$lPrestacaoConta) {
                        switch ($oCodigoGrupoContaOrcamento->getCodigo()) {
                            case 7:
                                $iCodDoc = 203;
                                break;
                            case 8:
                                $iCodDoc = 205;
                                break;
                            case 9:
                                $iCodDoc = 207;
                                break;
                            default:
                                $iCodDoc = $iCodDoc;
                        }
                    }
                }
            } else {
                $iCodDoc = 34; // estorno de liquacao RPs
            }

            if (!cl_translan::possuiLancamentoDeControle($oEmpenhoFinanceiro->getNumero(), false) && in_array($iCodDoc, array(203, 205))) {
                $iCodDoc = 4;
            }

            $oLancam = new lancamentoContabil($iCodDoc, $this->iAnoUsu, $this->dtDataUsu, $nValor);
            $oLancam->setCgm($oEmpenho->e60_numcgm);
            $oLancam->setEmpenho($oEmpenho->e60_numemp, $oEmpenho->e60_anousu, $oEmpenho->e60_codcom);
            $oLancam->setElemento($oElemento->e64_codele);
            $oLancam->setOrdemPagamento($this->iCodOrdem);
            $oLancam->setNota($iCodNota);
            if ($sMotivo != '') {
                $oLancam->setComplemento($sMotivo);
            }
            if ($oEmpenho->e60_anousu == $this->iAnoUsu) {
                $oLancam->setDotacao($oEmpenho->e60_coddot);
            }
            $oLancam->salvar();

            /* #1 - modification: ContratosPADRS */

            //incluimos o lancamento do desconto na pagordemdescontolanc;
            $oDaoPagordemDescontoLanc = new cl_pagordemdescontolanc;
            $oDaoPagordemDescontoLanc->e33_pagordemdesconto = $oDaoPagOrdemDesconto->e34_sequencial;
            $oDaoPagordemDescontoLanc->e33_conlancam = $oLancam->iCodLanc;
            $oDaoPagordemDescontoLanc->incluir(null);
            if ($oDaoPagordemDescontoLanc->erro_status == 0) {
                throw new exception("Erro[5] - Não foi possível incluir desconto do empenho");
            }
        }
        /* agora, fazemos o lancamento de anulacao do empenho.
         * para o o ano corrente, lancamos um  documento 2,
         * para RP, verificamos o ano da nota. caso o ano da nota seje o mesmo do empenho, temos um RP processadp (31)
         * caso  o ano da nota seje maior, temos um RP nao processado,
         */
        if ($oDadosOrdem->e60_anousu < $oDadosOrdem->e50_anousu) {
            //verificamos se está na empresto. geramos um estorno pagamento de RP não processado.
            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                $iCodDoc = 32; //rp não processado
            } else {
                $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                throw new exception($sMsg);
            }
        } elseif ($oDadosOrdem->e60_anousu == $oDadosOrdem->e50_anousu) {
            //se o ano da ordem for igual ao ano corrente, é um estorno de pagamento normal
            if ($oDadosOrdem->e50_anousu == $this->iAnoUsu) {
                $iCodDoc = 2; //do exercicio;

                if (USE_PCASP) {
                    if ($lPrestacaoConta) {
                        $iCodDoc = 411;
                    }
                    /**
                     * Verificamos se o empenho eh uma provisao de ferias
                     */
                    if ($lProvisaoFerias) {
                        $iCodigoDocumento = 305; // ESTORNO DE EMPENHO DA PROVISÃO DE FÉRIAS
                    }

                    /**
                     *  Verificamos se o empenho eh uma provisao de 13o
                     */
                    if ($lProvisaoDecimoTerceiro) {
                        $iCodigoDocumento = 309; // ESTORNO DE EMPENHO DA PROVISÃO DE 13º SALÁRIO
                    }

                    if ($lPrecatoria) {
                        $iCodigoDocumento = 501;
                    }

                    if ($lAmortizacaoDivida) {
                        $iCodigoDocumento = 505;  //  ESTORNO EMPENHO AMORT. DIVIDA
                    }
                }
            } else {
                if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                    $iCodDoc = 31; //estorno de liquidacao
                } else {
                    $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                    $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                    throw new exception($sMsg);
                }
            }
        }

        /**
         * Verificamos se o empenho eh prestacao de contas
         * Se usar o documento de estorno: 411 - ESTORNO DE EMPENHO SUPRIMENTO DE FUNDOS
         */
        $oLancam = new lancamentoContabil($iCodDoc, $this->iAnoUsu, $this->dtDataUsu, $nValor);
        $oLancam->setCgm($oEmpenho->e60_numcgm);
        $oLancam->setEmpenho($oEmpenho->e60_numemp, $oEmpenho->e60_anousu, $oEmpenho->e60_codcom);
        $oLancam->setElemento($oElemento->e64_codele);
        $oLancam->setOrdemPagamento($this->iCodOrdem);
        $oLancam->setNota($iCodNota);
        if ($sMotivo != '') {
            $oLancam->setComplemento($sMotivo);
        }
        if ($oEmpenho->e60_anousu == $this->iAnoUsu) {
            $oLancam->setDotacao($oEmpenho->e60_coddot);
        }
        $oLancam->salvar();
        //incluimos o lancamento do desconto na pagordemdescontolanc;
        $oDaoPagordemDescontoLanc = new cl_pagordemdescontolanc;
        $oDaoPagordemDescontoLanc->e33_pagordemdesconto = $oDaoPagOrdemDesconto->e34_sequencial;
        $oDaoPagordemDescontoLanc->e33_conlancam = $oLancam->iCodLanc;
        $oDaoPagordemDescontoLanc->incluir(null);
        if ($oDaoPagordemDescontoLanc->erro_status == 0) {
            throw new exception("Erro[5] - Não foi possível incluir desconto do empenho");
        }
        $clempanulado = new cl_empanulado;
        $clempanulado->e94_numemp = $oEmpenho->e60_numemp;
        $clempanulado->e94_valor = $nValor;
        $clempanulado->e94_saldoant = $nValor;
        $clempanulado->e94_motivo = $sMotivo;
        $clempanulado->e94_empanuladotipo = 2;
        $clempanulado->e94_data = date("Y-m-d", db_getsession("DB_datausu"));
        $clempanulado->incluir(null);

        if ($clempanulado->erro_status == 0) {
            throw new Exception("Erro ao Incluir anulação de Empenho.\n{$clempanulado->erro_msg}");
        }

        /**
         * Vinculo da anulação do empenho com a nota de desconto
         */
        $oDaoVinculoDescontoAnulacao = new cl_pagordemdescontoempanulado();
        $oDaoVinculoDescontoAnulacao->e06_sequencial = null;
        $oDaoVinculoDescontoAnulacao->e06_pagordemdesconto = $oDaoPagOrdemDesconto->e34_sequencial;
        $oDaoVinculoDescontoAnulacao->e06_empanulado = $clempanulado->e94_codanu;
        $oDaoVinculoDescontoAnulacao->incluir(null);
        if ($oDaoVinculoDescontoAnulacao->erro_status == "0") {
            throw new Exception("Impossível vincular a anu lação do empenho com o desconto.");
        }

        $oDaoEmpanuladoItem = new cl_empanuladoitem;
        foreach ($oNota->aItens as $oItem) {
            if ($oItem->nTotalDesconto > 0) {
                $sBuscaDadosItem = $oDaoEmpNotaItem->sql_query_file($oItem->e72_sequencial);
                $rsBuscaDadosItem = $oDaoEmpNotaItem->sql_record($sBuscaDadosItem);
                $oRetornoDadosItem = db_utils::fieldsMemory($rsBuscaDadosItem, 0);

                $oDaoEmpanuladoItem->e37_empempitem = $oRetornoDadosItem->e72_empempitem;
                $oDaoEmpanuladoItem->e37_empanulado = $clempanulado->e94_codanu;
                $oDaoEmpanuladoItem->e37_vlranu = $oItem->nTotalDesconto;
                $oDaoEmpanuladoItem->e37_qtd = "0";
                $oDaoEmpanuladoItem->incluir(null);

                if ($oDaoEmpanuladoItem->erro_status == 0) {
                    throw new Exception("Erro a anular o  item do empenho \n{$oDaoEmpanuladoItem->erro_msg}");
                }
            }
        }

        /**
         * incluimos os itens na tabela empanuladoite,
         */
        $clempanuladoele = new cl_empanuladoele;
        $clempanuladoele->e95_valor = $nValor;
        $clempanuladoele->e95_codele = $oElemento->e64_codele;
        $clempanuladoele->e95_codanu = $clempanulado->e94_codanu;
        $clempanuladoele->incluir($clempanulado->e94_codanu);

        $this->iCodigoDesconto = $clempanulado->e94_codanu;

        /**[Extensao OrdenadorDespesa] inclusao_ordenador*/

        return true;
    }

    /**
     * Verifica se o empenho está cadastrado como resto a pagar no ano corrente.
     * @param integer $iNumEmp Código do empenho
     * @return boolean;
     */

    public function isRestoPagar($iNumEmp)
    {

        $oEmpResto = new cl_empresto;
        $rsEmpResto = $oEmpResto->sql_record($oEmpResto->sql_query_empenho($this->iAnoUsu, $iNumEmp));
        if ($oEmpResto->numrows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Define quais retenções serao usadas na Ordem;
     *
     * @param array $aRetencoes
     */
    public function setRetencoes($aRetencoes)
    {
        $this->aRetencoes = $aRetencoes;
    }


    /**
     * Estorno de retenções
     *
     * @return bool
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     * @throws ReflectionException
     */
    public function estornarRetencoes()
    {
        $this->getDadosOrdem();
        $oDadosOrdem = $this->oDadosOrdem;
        /**
         * Verificamos se foi definido um grupo de autenticação
         */
        if ($this->getCodigoGrupoCorrente() == "") {
            $oDaoCorGrupo = new cl_corgrupo;
            $oDaoCorGrupo->k104_tipo = 2;
            $oDaoCorGrupo->incluir(null);
            if ($oDaoCorGrupo->erro_status == 0) {
                throw new exception("Erro ao Incluir grupo de autenticação!");
            }
            $this->setCodigoGrupoCorrente($oDaoCorGrupo->k104_sequencial);
        }
        $oEmpenhoFinanceiro = new EmpenhoFinanceiro($oDadosOrdem->e60_numemp);
        $nValorRetencoes = 0;
        if (APROPRIACAO_RETENCAO) {


            /**
             * faz mao aqui
             */
            $sIpUsuario = db_getsession("DB_ip");
            //rotina que verifica se o ip do usuario irá imprimir autenticar ou naum ira fazer nada
            $oDaoAutentica = new cl_cfautent;
            $rsTipoAutentica = $oDaoAutentica->sql_record(
                $oDaoAutentica->sql_query_file(
                    null,
                    "k11_tipautent",
                    '',
                    "k11_ipterm = '{$sIpUsuario}'
          and k11_instit = " . db_getsession("DB_instit")
                )
            );
            if ($oDaoAutentica->numrows > 0) {
                $this->oAutentica = db_utils::fieldsMemory($rsTipoAutentica, 0);
            } else {
                $sErroMsg = "Cadastre o ip {$sIpUsuario} como um caixa.";
                throw new exception($sErroMsg);
            }
            $this->estornarApropriacaoDasRetencoes($oEmpenhoFinanceiro);
            foreach ($this->aRetencoes as $oRetencao) {
                $nValorRetencoes += $oRetencao->nValor;
            }
        } else {
            require_once(modification("model/retencaoNota.model.php"));
            $oRetencaoNota = new retencaoNota($this->oDadosOrdem->e69_codnota);
            $oRetencaoNota->setINotaLiquidacao($this->oDadosOrdem->e50_codord);
            $nValorRetencoes = 0;
            if (is_array($this->aRetencoes) && count($this->aRetencoes) > 0) {
                /*
                 * Percorremos as retencoes, e repassamos ela ao model retencaoNota,
                 * para fazermos o estorno conforme o tipo da retencao.
                 */
                foreach ($this->aRetencoes as $oRetencao) {
                    $oRetencaoNota->setGrupoAutenticacao($this->getCodigoGrupoCorrente());
                    $oRetencaoNota->setConta($this->getConta());
                    $oRetencaoNota->setNumCgm($this->oDadosOrdem->e60_numcgm);
                    $oRetencaoNota->estornarRetencoes($oRetencao);
                    $nValorRetencoes += $oRetencao->nValor;
                }

                /**
                 * Atualizamos o empenho, e fizemos o lancamento contabil de estorno de pagamento
                 */
                if ($oDadosOrdem->e60_anousu < $oDadosOrdem->e50_anousu) {
                    //verificamos se está na empresto. geramos um estorno pagamento de RP não processado.
                    if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                        $iCodDoc = 38; //rp não processado

                        $sqlBuscaPagamento = "
           select conlancam.*
             from conlancam
                  inner join conlancamord on conlancamord.c80_codlan = conlancam.c70_codlan
                  inner join conlancamdoc on conlancamdoc.c71_codlan = conlancamord.c80_codlan
            where conlancamord.c80_codord = {$this->iCodOrdem}
              and conlancamdoc.c71_coddoc in (35)
              and conlancam.c70_valor = {$this->nValorPago}
            order by c70_codlan desc limit 1;
        ";
                        $resBuscaPagamento = db_query($sqlBuscaPagamento);
                        if (!$resBuscaPagamento) {
                            throw new Exception("Ocorreu um erro ao buscar o lançamento contábil para o pagamento que está sendo estornado.");
                        }

                        if (pg_num_rows($resBuscaPagamento) === 0) {
                            $iCodDoc = 38;
                        } else {
                            $stdLancamento = db_utils::fieldsMemory($resBuscaPagamento, 0);
                            if ($oDadosOrdem->e50_anousu < $stdLancamento->c70_anousu) {
                                $iCodDoc = 36;
                            }
                        }
                    } else {
                        $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                        $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                        throw new exception($sMsg);
                    }
                } else {
                    if ($oDadosOrdem->e60_anousu == $oDadosOrdem->e50_anousu) {
                        //se o ano da ordem for igual ao ano corrente, é um estorno de pagamento normal
                        if ($oDadosOrdem->e50_anousu == $this->iAnoUsu) {
                            $iCodDoc = 6; //ordem do exercicio;
                        } else {
                            if ($this->isRestoPagar($oDadosOrdem->e60_numemp)) {
                                $iCodDoc = 36; //estorno pagamento de rp processado
                            } else {
                                $sMsg = "Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu} ";
                                $sMsg .= "não cadastrado como Restos a Pagar em {$this->iAnoUsu} ";
                                throw new exception($sMsg);
                            }
                        }
                    }
                }
                /*
                 * setamos o valor final do empenho.
                 */
                $nValorEstornar = round($this->oDadosOrdem->e60_vlrpag, 2) - round($nValorRetencoes, 2);
                $oDaoEmpenho = new cl_empempenho;
                $oDaoEmpenho->e60_numemp = $oDadosOrdem->e60_numemp;
                $oDaoEmpenho->e60_vlrpag = "$nValorEstornar";
                $oDaoEmpenho->e60_concarpeculiar = $oEmpenhoFinanceiro->getCaracteristicaPeculiar();
                $oDaoEmpenho->alterar($oDadosOrdem->e60_numemp);
                if ($oDaoEmpenho->erro_status == 0) {
                    $sErroMsg = "Erro [1] - Erro ao pagar empenho.\n";
                    $sErroMsg .= "Erro Técnico: {$oDaoEmpenho->erro_msg}";
                    throw new exception($sErroMsg);
                }


                $oDaoEmpElemento = new cl_empelemento;
                /*
                 * Verificamos se o empenho possui mais de um elemento.
                 * Caso houver, devemos cancelar o processamento, pois
                 * um empenho nao pode ter mais de um elemento.
                 */

                $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query_file($oDadosOrdem->e60_numemp));
                if ($oDaoEmpElemento->numrows > 1) {
                    $sErroMsg = "Erro [2] - Empenho {$oDadosOrdem->e60_codemp}/{$oDadosOrdem->e60_anousu}";
                    $sErroMsg .= "Possui dois elementos. Processo Cancelado.";
                    throw new exception($sErroMsg);
                    return false;
                }
                $oElemento = db_utils::fieldsMemory($rsElemento, 0);
                $oDaoEmpElemento->e64_numemp = $oDadosOrdem->e60_numemp;
                $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
                $oDaoEmpElemento->e64_vlrpag = "{$nValorEstornar}";
                $oDaoEmpElemento->alterar($oDadosOrdem->e60_numemp, $oElemento->e64_codele);
                if ($oDaoEmpElemento->erro_status == 0) {
                    $sErroMsg = "Erro [3] - Erro ao estornar pagamento de empenho.\n";
                    $sErroMsg .= "Erro Técnico: {$oDaoEmpElemento->erro_msg}";
                    throw new exception($sErroMsg);
                    return false;
                }

                $oDaoPagOrdemEle = new cl_pagordemele;
                $rsElementoOrdem = $oDaoPagOrdemEle->sql_record(
                    $oDaoPagOrdemEle->sql_query_file($oDadosOrdem->e50_codord, $oDadosOrdem->e53_codele)
                );
                if ($oDaoPagOrdemEle->numrows == 0) {
                    $sErroMsg = "Erro [4] - Ordem de pagamento {$oDadosOrdem->e50_codord}";
                    $sErroMsg .= "não possui elemento cadastrado. Operação cancelada";
                    throw new exception($sErroMsg);
                    return false;
                }
                $oPagOrdemEle = db_utils::fieldsMemory($rsElementoOrdem, 0);
                $nValorEstornar = round($oPagOrdemEle->e53_vlrpag - $nValorRetencoes, 2);
                $oDaoPagOrdemEle->e53_vlrpag = "{$nValorEstornar}";
                $oDaoPagOrdemEle->e53_codele = $oDadosOrdem->e53_codele;
                $oDaoPagOrdemEle->e53_codord = $oDadosOrdem->e50_codord;
                $oDaoPagOrdemEle->alterar($oDadosOrdem->e50_codord);
                if ($oDaoPagOrdemEle->erro_status == 0) {
                    $sErroMsg = "Erro [5] - Erro ao pagar empenho.\n";
                    $sErroMsg .= "Erro Técnico: {$oDaoPagOrdemEle->erro_msg}";
                    throw new exception($sErroMsg);
                    return false;
                }
            }
            $this->setValorPago($nValorRetencoes);
            /**
             * Iniciamos os lancamentos contabeis do estorno.
             */
            require_once(modification("classes/lancamentoContabil.model.php"));
            require_once(modification("std/db_stdClass.php"));
            $oInstit = db_stdClass::getDadosInstit();

            $oDaoSaltesContapartida = new cl_saltescontrapartida;
            $sSqlContrapartida = $oDaoSaltesContapartida->sql_query(null, "*", null, "k103_saltes = " . $this->getConta());
            $rsContrapartida = $oDaoSaltesContapartida->sql_record($sSqlContrapartida);

            if ($oDaoSaltesContapartida->numrows > 0 && $oInstit->prefeitura == "t") {
                $oContaLivre = db_utils::fieldsMemory($rsContrapartida, 0);
                $oDaoEmpAgeSlip = new cl_empagemovslips;
                $oDaoEmpAgeSlip->k107_data = $this->dtDataUsu;
                $oDaoEmpAgeSlip->k107_empagemov = $this->getMovimentoAgenda();
                $oDaoEmpAgeSlip->k107_valor = $nValorRetencoes * -1;
                $oDaoEmpAgeSlip->k107_ctadebito = $oContaLivre->k103_contrapartida;
                $oDaoEmpAgeSlip->k107_ctacredito = $oContaLivre->k103_saltes;
                $oDaoEmpAgeSlip->incluir(null);
                if ($oDaoEmpAgeSlip->erro_status == 0) {
                    throw new Exception("Erro Ao incluir informacoes do slip.\n Processamento cancelado.");
                }
            }
            $oLancam = new LancamentoContabil(
                $iCodDoc,
                $this->iAnoUsu,
                $this->dtDataUsu,
                $nValorRetencoes
            );
            $oLancam->setCgm($oDadosOrdem->e60_numcgm);
            $oLancam->setEmpenho($oDadosOrdem->e60_numemp, $oDadosOrdem->e60_anousu, $oDadosOrdem->e60_codcom);
            $oLancam->setElemento($oElemento->e64_codele);
            $oLancam->setOrdemPagamento($oDadosOrdem->e50_codord);
            $oLancam->setReduz($this->getConta());
            if ($this->getHistorico() != '') {
                $oLancam->setComplemento($this->getHistorico());
            }
            if ($oDadosOrdem->e60_anousu == $this->iAnoUsu) {
                $sSqlSaldoDot = "select fc_lancam_dotacao({$oDadosOrdem->e60_coddot},";
                $sSqlSaldoDot .= "                         '{$this->dtDataUsu}',";
                $sSqlSaldoDot .= "                          {$iCodDoc},";
                $sSqlSaldoDot .= $this->getValorPago() . ") as dotacao";
                $rsDotacaoSaldo = db_query($sSqlSaldoDot);
                $oSaldoDot = db_utils::fieldsMemory($rsDotacaoSaldo, 0);
                if (substr($oSaldoDot->dotacao, 0, 1) == 0) {
                    throw new exception("Erro na atualização do orçamento \\n " . substr($oSaldoDot->dotacao, 1));
                    return false;
                }
                $oLancam->setDotacao($oDadosOrdem->e60_coddot);
            }
            $oLancam->salvar();
            $this->iCodLanc = $oLancam->getCodigoLancamento();
            $this->iContaRP = $oLancam->iContaEmp;

            $this->autenticar(5, self::ESTORNAR);
            /*
             * incluimos o lancamento contabil no grupo de autenticações
             */
            $oDaoCorrenteGrupo = new cl_corgrupocorrente;
            $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
                null,
                "*",
                null,
                "k105_corgrupo         = " . $this->getCodigoGrupoCorrente() . "
                                                            and k105_corgrupotipo  = 5"
            );
            $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
            if ($oDaoCorrenteGrupo->numrows == 0) {
                throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
            }
            $oDaoConlancamCorrente = new cl_conlancamcorgrupocorrente;
            $oDaoConlancamCorrente->c23_conlancam = $oLancam->getCodigoLancamento();
            $oDaoConlancamCorrente->c23_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
            $oDaoConlancamCorrente->incluir(null);
        }


        /**
         * Verificamos se o movimento atual esta com valor 0 (ZERO), se sim inserimos a data de anulação
         */
        $oDaoEmpAgeMov = new cl_empagemov;
        $iMovimento = $this->getMovimentoAgenda();

        $sSqlVerificaMovimento = $oDaoEmpAgeMov->sql_query_file($iMovimento);
        $rsEmpagemov = $oDaoEmpAgeMov->sql_record($sSqlVerificaMovimento);
        $oMovimento = db_utils::fieldsMemory($rsEmpagemov, 0);

        $daoRetencaoEmpAgemov = new cl_retencaoempagemov();
        $sqlTotalRetencao = $daoRetencaoEmpAgemov->sql_query(
            null,
            "retencaoreceitas.*",
            null,
            "e81_codmov = {$iMovimento} and e23_ativo is true"
        );
        $resTotalRetencao = db_query($sqlTotalRetencao);
        $totalRegistrosRetencao = pg_num_rows($resTotalRetencao);
        if ($nValorRetencoes > 0 && $resTotalRetencao && $totalRegistrosRetencao == 0) {
            $oDaoEmpAgeMov->e81_codmov = $iMovimento;
            $oDaoEmpAgeMov->e81_cancelado = date('Y-m-d');
            $oDaoEmpAgeMov->alterar($iMovimento);
        }

        if ($oMovimento->e81_valor == 0 && $nValorRetencoes == 0) {
            $oDaoEmpAgeMov->e81_codmov = $iMovimento;
            $oDaoEmpAgeMov->e81_cancelado = date('Y-m-d');
            $oDaoEmpAgeMov->alterar($iMovimento);
        }
        unset($rsEmpagemov);


        /*
         * Verificamos se existe um movimento já gerado.
         * caso exista, atualizamos o valor do movimento, como o valor das retencoes;
         * senao. incluimos um movimento pela agenda;
         */
        $sequencialEmpenho = $oEmpenhoFinanceiro->getNumero();
        $whereMovimento = implode(' and ', array(
                "not exists (select 1 from corempagemov where corempagemov.k12_codmov = empagemov.e81_codmov)",
                "not exists (select 1 from empageconfche where empageconfche.e91_codmov = empagemov.e81_codmov)",
                "e81_numemp = {$sequencialEmpenho}",
                "e81_valor > 0",
                "e82_codord = {$oDadosOrdem->e50_codord}",
                "e81_cancelado is null"
            )) . " order by empagemov.e81_codmov limit 1 ";
        $daoEmpOrd = new cl_empord();
        $buscaMovimento = $daoEmpOrd->sql_query_movimento_ordem("empagemov.*", $whereMovimento);
        $buscaMovimento = db_query($buscaMovimento);
//    if ($this->iNovoMovimento != null) {
        if (pg_num_rows($buscaMovimento) > 0) {
            $stdMovimento = db_utils::fieldsMemory($buscaMovimento, 0);
            $daoMovimento = new cl_empagemov();
            $daoMovimento->e81_valor = ($stdMovimento->e81_valor + $nValorRetencoes);
            $daoMovimento->e81_codmov = $stdMovimento->e81_codmov;
            $daoMovimento->alterar($daoMovimento->e81_codmov);
            if ($daoMovimento->erro_status === '0') {
                throw new Exception("Não foi possível alterar o valor do movimento.");
            }
        } else {
            require_once(modification(Modification::getFile('model/agendaPagamento.model.php')));
            $oAgendaPagamento = new agendaPagamento();
            $oNovoMovimento = new stdClass();
            $oNovoMovimento->iCodTipo = null;
            $oNovoMovimento->iNumEmp = $oDadosOrdem->e50_numemp;
            $oNovoMovimento->nValor = $nValorRetencoes;
            $oNovoMovimento->iCodNota = $oDadosOrdem->e50_codord;
            $oAgendaPagamento->addMovimentoAgenda(1, $oNovoMovimento);
        }
        $this->atualizarSaldoPacto($nValorRetencoes * -1, $oDadosOrdem->e69_codnota, $this->oDadosOrdem->e53_valor);

        // estorna slips gerados automaticamente
        if (slip::getParametroSlipAutomaticoLiquidacao()) {
            $this->estornarSlipsAutomaticos();
        }

        return true;
    }

    public function atualizarSaldoPacto($nValorPago, $iNota, $nValorTotalNota)
    {
        require_once(modification("std/db_stdClass.php"));
        $lControlePacto = false;
        $aParametrosOrcamento = db_stdClass::getParametro("orcparametro", array(db_getsession("DB_anousu")));
        if (count($aParametrosOrcamento) > 0) {
            if (isset($aParametrosOrcamento[0]->o50_utilizapacto)) {
                $lControlePacto = @$aParametrosOrcamento[0]->o50_utilizapacto == "t" ? true : false;
            }
        }

        if ($lControlePacto) {
            require_once(modification("model/itempacto.model.php"));

            $oDaoEmpItem = new cl_empempitem;
            $sSqlItemPacto = $oDaoEmpItem->sql_query_item_pacto(
                null,
                null,
                "*",
                null,
                "e72_codnota = {$iNota}"
            );
            $rsItemPacto = $oDaoEmpItem->sql_record($sSqlItemPacto);
            if ($oDaoEmpItem->numrows > 0) {
                $aItensPacto = db_utils::getCollectionByRecord($rsItemPacto);
                $iTotalItens = $oDaoEmpItem->numrows;

                for ($i = 0; $i < $iTotalItens; $i++) {
                    $oItemNota = $aItensPacto[$i];
                    $oItemPacto = new itemPacto($oItemNota->o88_pactovalor);

                    $nValorPerc = round(($nValorPago * 100) / $nValorTotalNota, 2);
                    $nValorPagoItem = round(($oItemNota->e72_valor * $nValorPerc) / 100, 2);
                    $oItemPacto->atualizaSaldoRealizado($nValorPagoItem, $this->getConta(), $this->iCodOrdem);
                    unset($oItemPacto);
                    unset($oItemNota);
                }
            }
        }
    }


    /**
     * retorna os slips gerados automaticamente para a OP
     *  1 | Não - Autenticado
     *  2 | Autenticado
     *  3 | Estornado
     *  4 | Anulado
     */

    public function getSlipsGeradosAutomaticamente($situacao = null)
    {
        $oDao = new cl_slipretencaoreceitas;
        $aWhere = array();

        $aWhere[] = "e20_pagordem = {$this->iCodOrdem}";
        if (!empty($situacao)) {
            $aWhere[] = "k17_situacao = {$situacao}";
        }

        $sWhere = implode(" and ", $aWhere);
        $sql = $oDao->sql_query(null, "k206_slip", 1, $sWhere);
        $rs = $oDao->sql_record($sql);
        $aSlips = array();

        if ($oDao->numrows > 0) {
            for ($i = 0; $i < $oDao->numrows; $i++) {
                $aSlips[] = db_utils::fieldsMemory($rs, $i)->k206_slip;
            }
        }
        /**
         * busca o slip de transferencia bancaria gerado automaticamente
         */
        $aWhere = array("k209_pagordem = {$this->iCodOrdem}");
        if (!empty($situacao)) {
            $aWhere[] = "k17_situacao = {$situacao}";
        }
        $sWhere = implode(" and ", $aWhere);
        $oDaoSlipPagordem = new cl_slippagordem;
        $sql = $oDaoSlipPagordem->sql_query(null, "k17_codigo", 1, $sWhere);
        $rs = $oDaoSlipPagordem->sql_record($sql);
        if ($oDaoSlipPagordem->numrows > 0) {
            for ($i = 0; $i < $oDaoSlipPagordem->numrows; $i++) {
                $aSlips[] = db_utils::fieldsMemory($rs, $i)->k17_codigo;
            }
        }

        return $aSlips;
    }


    /**
     * Realiza a apropriacao das retenções
     * @param EmpenhoFinanceiro $empenhoFinanceiro
     * @throws Exception
     */
    public function apropriarRetencoes(EmpenhoFinanceiro $empenhoFinanceiro)
    {
        if (!APROPRIACAO_RETENCAO) {
            return;
        }

        $apropriacao = new \ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao($empenhoFinanceiro, $this->iAnoUsu);
        $apropriacao->setDataEvento(new \DateTime($this->dtDataUsu));
        $apropriacao->apropriar($this->oDadosOrdem->e69_codnota, $this->oDadosOrdem->e50_codord, $this->getCodigoGrupoCorrente(), $this->getMovimentoAgenda());
    }

    /**
     * @param EmpenhoFinanceiro $empenhoFinanceiro
     * @throws Exception
     */
    public function estornarApropriacaoDasRetencoes(EmpenhoFinanceiro $empenhoFinanceiro)
    {
        if (!APROPRIACAO_RETENCAO) {
            return;
        }

        $apropriacao = new \ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao($empenhoFinanceiro, $this->iAnoUsu);
        $apropriacao->setDataEvento(new \DateTime($this->dtDataUsu));
        $this->sRetornoAutentica = $apropriacao->estornar(
            $this->oDadosOrdem->e69_codnota,
            $this->oDadosOrdem->e50_codord,
            $this->aRetencoes,
            $this->getCodigoGrupoCorrente(),
            $this->getMovimentoAgenda()
        );
        $this->iCodLanc = implode(',', $apropriacao->getCodigosLancamentos());
    }


    /* [Extensão] ContratosPADRS: Atributos e Persistência do Tipo Instrumento Contratual */
}
