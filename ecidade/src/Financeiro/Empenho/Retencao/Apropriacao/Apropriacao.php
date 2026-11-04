<?php

namespace ECidade\Financeiro\Empenho\Retencao\Apropriacao;

use cl_saltes;
use db_utils;
use ECidade\Financeiro\Empenho\Autenticacao;
use Exception;
use ParametroCaixa;
use planilhaRetencao;
use recibo;
use retencaoNota;
use stdClass;

/**
 * Class Apropriacao
 *
 * @package ECidade\Financeiro\Empenho\Retencao\Apropriacao
 */
class Apropriacao
{
    /**
     * @var \EmpenhoFinanceiro
     */
    private $empenho;

    /**
     * @var \DateTime
     */
    private $dataEvento = null;

    /**
     * Ano do processamento
     *
     * @var int
     */
    private $ano;

    /**
     * Nota que deve ser estornada
     *
     * @var int
     */
    private $nota;

    /**
     * Grupo de autenticacao
     *
     * @var integer
     */
    private $codigoGrupoAutenticacao;

    /**
     * Código da ordem de pagameno
     *
     * @var int
     */
    private $ordemPagamento;


    /**
     * Define se o evento é de estorno
     *
     * @var bool
     */
    private $estorno = false;

    /**
     * Codigo do lancametno do recibo
     *
     * @var int
     */
    private $codigoLancamentoEmpenho;

    /**
     * Códigos de lançamentos gerados para o estorno da apropriação
     *
     * @var array
     */
    private $codigosLancamentos = array();

    /**
     * Apropriação de retençoes em e um empenho
     * Apropriacao constructor.
     *
     * @param \EmpenhoFinanceiro $empenho
     * @param $ano
     */
    public function __construct(\EmpenhoFinanceiro $empenho, $ano)
    {

        $this->empenho = $empenho;
        $this->ano = $ano;
    }

    public function filtrarRetencaoZerada($retencoes)
    {

        $retencaoFiltrada = [];
        foreach ($retencoes as $retencoes) {
            if ($retencoes->e23_valorretencao <= 0) {
                continue;
            }
            $retencaoFiltrada[] = $retencoes;
        }
        return $retencaoFiltrada;
    }

    /**
     * Realiza a baixa das retenções envolvidas no movimento informado
     *
     * @param integer $movimento
     * @return string
     * @throws Exception
     */
    public function apropriar($nota, $ordemPagamento, $codigoGrupoAutenticacao = null, $movimento = null)
    {
        $this->estorno = false;
        /**
         * Incluindo grupo de autenticação
         */
        if (empty($codigoGrupoAutenticacao)) {
            $oDaoGruoAutenticacao = new \cl_corgrupo();
            $oDaoGruoAutenticacao->k104_tipo = 1;
            $oDaoGruoAutenticacao->incluir(null);
            if ($oDaoGruoAutenticacao->erro_status == '0') {
                throw new Exception(
                    sprintf(
                        'Houve um problema ao incluir grupo de autenticação. %s',
                        $oDaoGruoAutenticacao->erro_msg
                    )
                );
            }
            $codigoGrupoAutenticacao = $oDaoGruoAutenticacao->k104_sequencial;
        }

        /**
         * 4 - recolher as retenções
         * 4.1 - ajustar lancamento do recibo para os documentos novos (verificar os tipos)
         */
        $this->nota = $nota;
        $this->ordemPagamento = $ordemPagamento;
        $this->codigoGrupoAutenticacao = $codigoGrupoAutenticacao;
        $data = $this->dataEvento->format('Y-m-d');
        $oRetencaoNota = new retencaoNota($nota);
        $nValorTotalRetencoes = $oRetencaoNota->getValorRetencaoMovimento($movimento, false, $data);
        if ($nValorTotalRetencoes == 0) {
            return '';
        }
        $dadosNota = $this->getDadosDaNota($nota);
        $this->atualizarValorEmpenho($nValorTotalRetencoes, $ordemPagamento);
        $oRetencaoNota->setINotaLiquidacao($ordemPagamento);
        $oRetencaoNota->setCodigoMovimento($movimento);

        $retencoes = $oRetencaoNota->getRetencoesFromDB($ordemPagamento, false);
        /**
         * remove retencoes zeradas da folha , somente para o processamento
         */
        $retencoes = $this->filtrarRetencaoZerada($retencoes);

        $contaPagadora = \cl_translan::getContaParaPagamento($this->empenho->getNumero(), $dadosNota->e50_anousu);

        $oRetencaoNota->setGrupoAutenticacao($codigoGrupoAutenticacao);
        $oRetencaoNota->setConta($contaPagadora);
        $oRetencaoNota->setDataBase($data);
        $oRetencaoNota->setNumCgm($this->empenho->getCgm()->getCodigo());

        $autenticacao = new Autenticacao($this->empenho, $movimento, $nValorTotalRetencoes, $this->dataEvento);
        $autenticacao->setContaExtraOrcamentaria($contaPagadora);
        $autenticacao->setContaPagadora($contaPagadora);
        $autenticacao->setTipoAutenticacao(Autenticacao::AUTENTICACAO_RETENCAO);
        $autenticacao->setOrdemPagamento($ordemPagamento);
        $autenticacao->setGrupoAutenticacao($codigoGrupoAutenticacao);
        $stringAutenticacao = $autenticacao->autenticar();

        $slipAutomatico = ParametroCaixa::getParametroMomentoSlipAutomatico(db_getsession("DB_instit"));

        foreach ($retencoes as $retencao) {
            $this->realizarLancamentoContabilDaRetencao($retencao);
            if ($retencao->k02_tipo === 'E') {
                $this->gerarDadosParaSlip($retencao, $movimento);
            } elseif ($retencao->k02_tipo === 'O' && !$this->empenho->isRecursoLivre() && $slipAutomatico == 0) {
                $this->gerarDadosParaSlipOrcamentario($retencao, $movimento);
            }
            $this->realizarLancamentoNaInstituicaoInformada($retencao);
            /**
             * Baixamos as retenções
             */
            $oRetencaoNota->baixarRetencoes(array($retencao));
        }
        return $stringAutenticacao;
    }


    /**
     * busca a conta pelo recurso da dotacao do empenho
     */
    protected function getContaRecursoVinculada($recurso)
    {
        $oContas = new stdClass();
        $oContas->credito = null;
        $oContas->debito = null;

        $oDaoSaltes = new cl_saltes;

        $campos = " DISTINCT saltes.k13_conta as credito,
                    (select k103_contrapartida
                       from saltescontrapartida
                      where k103_saltes = k13_conta
                      limit 1) as debito ";

        // fixo sistema para apropriacao logica da alteracao de conta da tesouraria
        $sWhere = " c60_codsis in (5, 6)
                and o15_recurso = '{$recurso}' ";

        // busca a primeira que tenha contrapartida para o slip
        $sql = $oDaoSaltes->sql_query_anousu(null, $campos, "credito", $sWhere);
        $sql = " select * from ({$sql}) as x where debito is not null limit 1";
        $rs = $oDaoSaltes->sql_record($sql);
        if ($oDaoSaltes->numrows <= 0) {
            throw new Exception("Não foi encontrado contrapartida para a conta do recurso vinculado:{$recurso}");
        }
        $oDados = db_utils::fieldsMemory($rs, 0);
        $oContas->credito = $oDados->credito;
        $oContas->debito = $oDados->debito;

        return $oContas;
    }

    /**
     * Realiza a baixa das retenções envolvidas no movimento informado
     *
     * @param integer $movimento
     * @return string
     * @throws Exception
     */
    public function estornar($nota, $ordemPagamento, $retencoes, $codigoGrupoAutenticacao = null, $movimento = null)
    {
        $this->estorno = true;
        if (count($retencoes) == 0) {
            return false;
        }
        /**
         * Incluindo grupo de autenticação
         */
        if (empty($codigoGrupoAutenticacao)) {
            $oDaoGruoAutenticacao = new \cl_corgrupo();
            $oDaoGruoAutenticacao->k104_tipo = 2;
            $oDaoGruoAutenticacao->incluir(null);
            if ($oDaoGruoAutenticacao->erro_status == '0') {
                throw new Exception(
                    sprintf(
                        'Houve um problema ao incluir grupo de autenticação. %s',
                        $oDaoGruoAutenticacao->erro_msg
                    )
                );
            }
            $codigoGrupoAutenticacao = $oDaoGruoAutenticacao->k104_sequencial;
        }

        $this->nota = $nota;
        $this->ordemPagamento = $ordemPagamento;
        $this->codigoGrupoAutenticacao = $codigoGrupoAutenticacao;
        $data = $this->dataEvento->format('Y-m-d');
        $oRetencaoNota = new retencaoNota($nota);
        //        $nValorTotalRetencoes = $oRetencaoNota->getValorRetencaoMovimento($movimento, true, $data);
        $nValorTotalRetencoes = 0;
        foreach ($retencoes as $retencao) {
            $nValorTotalRetencoes += $retencao->nValor;
        }

        $dadosNota = $this->getDadosDaNota($nota);

        $this->atualizarValorEmpenho($nValorTotalRetencoes * -1, $ordemPagamento);
        $oRetencaoNota->setINotaLiquidacao($ordemPagamento);
        $oRetencaoNota->setCodigoMovimento($movimento);
        $contaPagadora = \cl_translan::getContaParaPagamento($this->empenho->getNumero(), $dadosNota->e50_anousu);
        $oRetencaoNota->setGrupoAutenticacao($codigoGrupoAutenticacao);
        $oRetencaoNota->setConta($contaPagadora);
        $oRetencaoNota->setDataBase($data);
        $oRetencaoNota->setNumCgm($this->empenho->getCgm()->getCodigo());

        $autenticacao = new Autenticacao($this->empenho, $movimento, $nValorTotalRetencoes, $this->dataEvento);
        $autenticacao->setContaExtraOrcamentaria($contaPagadora);
        $autenticacao->setContaPagadora($contaPagadora);
        $autenticacao->setTipoAutenticacao(Autenticacao::ESTORNO_RETENCAO);
        $autenticacao->setOrdemPagamento($ordemPagamento);
        $autenticacao->setGrupoAutenticacao($codigoGrupoAutenticacao);
        $stringAutenticacao = $autenticacao->estornar();

        foreach ($retencoes as $retencao) {
            $dadosRetencao = $oRetencaoNota->getRetencaoByCodigo($retencao->iRetencao, true);
            $this->realizarLancamentoContabilDaRetencao($dadosRetencao);
            $this->realizarLancamentoNaInstituicaoInformada($dadosRetencao);
            /**
             * Baixamos as retenções
             */
            $oRetencaoNota->estornarRetencoes($retencao);
        }
        return $stringAutenticacao;
    }


    /**
     * Retorna o texto formatado da retencao
     *
     * @param  $retencao
     * @return string
     */
    private function getObservacaoDaRetencao($retencao)
    {
        $dao = new \cl_empnota();
        $sql = $dao->sql_query_file($this->nota, 'e69_numero');
        $numeroNota = db_utils::fieldsMemory(db_query($sql), 0)->e69_numero;

        return sprintf(
            "Referente a %s no valor %s da Seq. Nota %s Nº Nota %s.",
            $retencao->e21_descricao,
            db_formatar($retencao->e23_valorretencao, "f"),
            $this->nota,
            $numeroNota
        );
    }


    /**
     * Atualiza os valores pagos do empenho
     *
     * @param  $valor
     * @return bool
     * @throws Exception
     */
    private function atualizarValorEmpenho($valor, $ordemPagamento)
    {

        $codigoEmpenho = $this->empenho->getNumero();
        $oDaoEmpElemento = new \cl_empelemento();
        $rsElemento = $oDaoEmpElemento->sql_record($oDaoEmpElemento->sql_query_file($codigoEmpenho));
        $oElemento = \db_utils::fieldsMemory($rsElemento, 0);

        $valorElemento = $oElemento->e64_vlrpag + $valor;
        $oDaoEmpElemento->e64_numemp = $codigoEmpenho;
        $oDaoEmpElemento->e64_codele = $oElemento->e64_codele;
        $oDaoEmpElemento->e64_vlrpag = (string)"{$valorElemento}";
        $oDaoEmpElemento->alterar($codigoEmpenho, $oElemento->e64_codele);
        if ($oDaoEmpElemento->erro_status == 0) {
            $sErroMsg = "Erro [3] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpElemento->erro_msg}";
            throw new Exception($sErroMsg);
        }

        $oDaoPagOrdemEle = new \cl_pagordemele;
        $rsElementoOrdem = $oDaoPagOrdemEle->sql_record($oDaoPagOrdemEle->sql_query_file($ordemPagamento));
        if ($oDaoPagOrdemEle->numrows == 0) {
            $sErroMsg = "Erro [4] - Ordem de pagamento {$ordemPagamento}";
            $sErroMsg .= "não possui elemento cadastrado. Operação cancelada";
            throw new Exception($sErroMsg);
        }
        $oPagOrdemEle = \db_utils::fieldsMemory($rsElementoOrdem, 0);

        $valorOrdem = $oPagOrdemEle->e53_vlrpag + $valor;
        $oDaoPagOrdemEle->e53_vlrpag = "{$valorOrdem}";
        $oDaoPagOrdemEle->e53_codele = $oPagOrdemEle->e53_codele;
        $oDaoPagOrdemEle->e53_codord = $oPagOrdemEle->e53_codord;
        $oDaoPagOrdemEle->alterar($oPagOrdemEle->e53_codord);
        if ($oDaoPagOrdemEle->erro_status == 0) {
            $sErroMsg = "Erro [5] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoPagOrdemEle->erro_msg}";
            throw new Exception($sErroMsg);
        }

        $valorEmpenho = $this->empenho->getValorPagoDasOrdens();
        $oDaoEmpenho = new \cl_empempenho;
        $oDaoEmpenho->e60_numemp = $codigoEmpenho;
        $oDaoEmpenho->e60_vlrpag = (string)"{$valorEmpenho}";
        $oDaoEmpenho->alterar($codigoEmpenho);
        if ($oDaoEmpenho->erro_status == 0) {
            $sErroMsg = "Erro [1] - Erro ao pagar empenho.\n";
            $sErroMsg .= "Erro Técnico: {$oDaoEmpenho->erro_msg}";
            throw new Exception($sErroMsg);
        }

        return true;
    }


    /**
     * Retorna o grupo de lançamentos do corrente
     *
     * @param  $codigoGrupoCorrente
     * @return mixed
     * @throws Exception
     */
    private function getCodigoGrupoLancamentos($codigoGrupoCorrente)
    {
        $oDaoCorrenteGrupo = new \cl_corgrupocorrente;
        $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
            null,
            "*",
            "k105_autent desc limit 1",
            "k105_corgrupo = " . $codigoGrupoCorrente
        );
        $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
        if ($oDaoCorrenteGrupo->numrows == 0) {
            throw new Exception("Não Foi possivel encontrar grupo de lançamentos.");
        }
        return \db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
    }

    /**
     * @return \EmpenhoFinanceiro
     */
    public function getEmpenho()
    {
        return $this->empenho;
    }

    /**
     * @param \EmpenhoFinanceiro $empenho
     */
    public function setEmpenho($empenho)
    {
        $this->empenho = $empenho;
    }

    /**
     * @return \DateTime
     */
    public function getDataEvento()
    {
        return $this->dataEvento;
    }

    /**
     * @param \DateTime $dataEvento
     */
    public function setDataEvento($dataEvento)
    {
        $this->dataEvento = $dataEvento;
    }

    /**
     * REaliza o lancamento contabil de cada retencao
     *
     * @param  $retencao
     * @throws \BusinessException
     * @throws Exception
     */
    protected function realizarLancamentoContabilDaRetencao($retencao)
    {
        $codigoDocumento = $this->getDocumentoExecutar($retencao);
        $oEventoContabilRetencao = new \EventoContabil($codigoDocumento, $this->ano);

        $oLancamentoAuxiliarRetencao = new \LancamentoAuxiliarApropriacaoRetencao();
        $oLancamentoAuxiliarRetencao->setObservacaoHistorico($this->getObservacaoDaRetencao($retencao));
        $oLancamentoAuxiliarRetencao->setFavorecido($this->empenho->getFornecedor()->getCodigo());
        $oLancamentoAuxiliarRetencao->setCodigoElemento($this->empenho->getDesdobramentoEmpenho());
        $oLancamentoAuxiliarRetencao->setCodigoDotacao($this->empenho->getDotacao()->getCodigo());
        $oLancamentoAuxiliarRetencao->setEmpenhoFinanceiro($this->empenho);
        $oLancamentoAuxiliarRetencao->setNumeroEmpenho($this->empenho->getNumero());
        $oLancamentoAuxiliarRetencao->setValorTotal($retencao->e23_valorretencao);
        $oLancamentoAuxiliarRetencao->setRetencao($retencao->e21_sequencial);
        $oLancamentoAuxiliarRetencao->setTipoCalculoRetencao($retencao->e21_retencaotipocalc);
        $oLancamentoAuxiliarRetencao->setEstorno($this->estorno);
        $oLancamentoAuxiliarRetencao->setCodigoOrdemPagamento($this->ordemPagamento);

        $codigoLancamento = $oEventoContabilRetencao->executaLancamento($oLancamentoAuxiliarRetencao);
        $this->codigoLancamentoEmpenho = $codigoLancamento;
        $this->codigosLancamentos[] = $codigoLancamento;

        $oDaoConlancamCorrente = new \cl_conlancamcorgrupocorrente;
        $oDaoConlancamCorrente->c23_conlancam = $codigoLancamento;
        $oDaoConlancamCorrente->c23_corgrupocorrente = $this->getCodigoGrupoLancamentos($this->codigoGrupoAutenticacao);
        $oDaoConlancamCorrente->incluir(null);
    }

    /**
     * @param  $retencao
     * @param  $movimento
     * @throws Exception
     */
    private function gerarDadosParaSlip($retencao, $movimento)
    {
        $oDaoEmpAgeSlip = new \cl_empagemovslips;
        $oDaoEmpAgeSlip->k107_data = $this->dataEvento->format("Y-m-d");
        $oDaoEmpAgeSlip->k107_empagemov = $movimento;
        $oDaoEmpAgeSlip->k107_valor = $retencao->e23_valorretencao;
        $oDaoEmpAgeSlip->k107_ctadebito = "0";
        $oDaoEmpAgeSlip->k107_ctacredito = $this->getContaExtraOrcamentariaDaRetencao($retencao->e21_sequencial);
        $oDaoEmpAgeSlip->k107_retencao = $retencao->e23_sequencial;
        $oDaoEmpAgeSlip->incluir(null);
        if ($oDaoEmpAgeSlip->erro_status == 0) {
            throw new Exception("Erro Ao incluir informações do slip.\n Processamento cancelado.");
        }
    }

    /**
     * Retirna o Codigo da receita extra da retencao
     *
     * @param  $codigoRetencao
     * @return null|int
     */
    private function getContaExtraOrcamentariaDaRetencao($codigoRetencao)
    {
        $sSqlTipoRec = " SELECT k02_reduz                                                   ";
        $sSqlTipoRec .= "   from retencaotiporec ";
        $sSqlTipoRec .= "       inner join tabrec  on e21_receita = k02_codigo               ";
        $sSqlTipoRec .= "       inner join tabplan on tabrec.k02_codigo = tabplan.k02_codigo ";
        $sSqlTipoRec .= " where e21_sequencial  = {$codigoRetencao} ";
        $sSqlTipoRec .= "   and k02_anousu = " . db_getsession("DB_anousu");
        $rsTipoRec = db_query($sSqlTipoRec);
        if (pg_num_rows($rsTipoRec) == 0) {
            return null;
        }
        return \db_utils::fieldsMemory($rsTipoRec, 0)->k02_reduz;
    }

    /**
     * Realiza o lancamento contabil de cada retencao
     *
     * @param  $retencao
     * @throws \BusinessException
     * @throws Exception
     *     *
     */
    protected function realizarLancamentoNaInstituicaoInformada($retencao)
    {
        if (empty($retencao->e21_enterecebedor)) {
            return;
        }

        self::setInstituicaoProcessamento($retencao->e21_enterecebedor);

        try {
            $this->realizarLancamentoExtraNoEnteDaRetencao($retencao, $this->estorno);
            self::restaurarInstituicaoUsuario();
        } catch (Exception $e) {
            db_fim_transacao(true);
            self::restaurarInstituicaoUsuario();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Gera os recibos na instituição de destino e autenticaca os mesmos
     *
     * @param  $instituicao
     * @param  $retencao
     * @throws \BusinessException
     * @throws Exception
     */
    public function gerarRecibosDeArrecadacaoNaInstituicaoParaRetencao($instituicao, $retencao)
    {
        $empenho = $this->empenho;
        $oDotacao = $this->empenho->getDotacao();

        $nomeCgm = str_replace("'", "", $empenho->getCgm()->getNome());
        $dataRecibo = $this->dataEvento->format("Y-m-d");
        $sHistoricoRecibo = "Neste pagamento  foi lançada uma retenção ";
        $sHistoricoRecibo .= "para o empenho {$empenho->getCodigo()}/{$empenho->getAno()} ";
        $sHistoricoRecibo .= "no valor de R$ " . trim(db_formatar($retencao->e23_valorretencao, "f"));
        $sHistoricoRecibo .= " pela Ordem de Pagamento n° {$this->ordemPagamento}";
        $sHistoricoRecibo .= " CGM: " . $empenho->getCgm()->getCodigo() . " - " . $nomeCgm;

        $conta = \Transferencia::getContaPagadoraParaRetencaoDoCredor($retencao->credor);

        if ($retencao->e21_retencaotipocalc != 5) {
            $oReciboAvulso = new recibo(1, $empenho->getCgm()->getCodigo());
            $oReciboAvulso->setConta($conta);
            $oReciboAvulso->adicionarRecurso($oDotacao->getRecurso());
            $oReciboAvulso->setDataRecibo($dataRecibo);
            $oReciboAvulso->setDataVencimentoRecibo($dataRecibo);
            $oReciboAvulso->setGrupoAutenticacao($this->codigoGrupoAutenticacao);
            $oReciboAvulso->setHistorico($sHistoricoRecibo);
            $oReciboAvulso->adicionarReceita(
                $retencao->e21_receitaenterecebedor,
                $retencao->e23_valorretencao,
                0,
                '000'
            );

            $oReciboAvulso->adicionarRecurso($oDotacao->getRecurso());
            $oReciboAvulso->emiteRecibo();
            $codigoRetencao = $retencao->e21_sequencial;
            $oReciboAvulso->autenticarRecibo(
                $dataRecibo,
                $empenho->getCaracteristicaPeculiar(),
                $empenho->getNumero(),
                $codigoRetencao,
                7
            );

            $nValorRecibo = $oReciboAvulso->getTotalRecibo();
            if ($nValorRecibo != $retencao->e23_valorretencao) {
                $sMsgValorRetencao = "A retenção {$retencao->k02_codigo} com valor {$retencao->e23_valorretencao} é ";
                $sMsgValorRetencao .= "diferente do valor total do recibo {$nValorRecibo}";
                throw new Exception($sMsgValorRetencao);
            }
        } else {
            if ($retencao->e21_retencaotipocalc == 5) {
                $sSqlCgm = "select cgc, numcgm from db_config where codigo = " . $instituicao;
                $rsCgm = db_query($sSqlCgm);
                $oCgm = \db_utils::fieldsMemory($rsCgm, 0);
                /*
                 * Consultamos o cnpj do credor da ordem de pagamento
                */
                $cgm = $this->empenho->getCgm()->getCodigo();
                $sSqlCnpjCredor = "select z01_cgccpf from cgm where z01_numcgm = {$cgm}";
                $rsCnpjCredor = db_query($sSqlCnpjCredor);
                $oCgmCredor = db_utils::fieldsMemory($rsCnpjCredor, 0);
                if ($oCgmCredor->z01_cgccpf == "") {
                    $str = "Não Foi possível efetuar a baixa da Retenção. Credor com CPF/CNPJ nulo ou inválido.";
                    throw new Exception($str);
                }
                $oPlanilha = new \planilhaRetencao(null, $oCgm->numcgm);
                $oNotaPlanilha = new \stdClass();

                $dadosDaNota = $this->getDadosDaNota($this->nota);
                $oNotaPlanilha->sCnpj = $oCgm->numcgm;
                $oNotaPlanilha->dtNota = $dadosDaNota->e69_dtnota;
                $oNotaPlanilha->sNumeroNota = $dadosDaNota->e69_numero;
                $oNotaPlanilha->nValor = $dadosDaNota->e53_valor;
                $oNotaPlanilha->sNome = str_replace("'", "\'", $this->empenho->getCgm()->getNome());

                $oNotaPlanilha->nValorTotalRetencao = $retencao->e23_valorretencao;
                $oNotaPlanilha->nValorBase = $retencao->e23_valorbase;
                $oNotaPlanilha->nValorDeducao = $retencao->e23_deducao;
                $oNotaPlanilha->nAliquota = $retencao->e23_aliquota;
                $oNotaPlanilha->iNotaLiquidacao = $this->nota;
                $oPlanilha->adicionaNota($oNotaPlanilha);
                $oPlanilha->setDatausu($dataRecibo);
                $oPlanilha->gerarDebito($sHistoricoRecibo);
                $iNumpre = $oPlanilha->getNumpre();

                //Incluimos o recibo e o autenticamos.
                $oReciboDebito = new recibo(2, $oCgm->numcgm, 25);
                $oReciboDebito->addNumpre($iNumpre, 1);

                $oReciboDebito->adicionarRecurso($oDotacao->getRecurso());

                $oReciboDebito->setConta($conta);
                $oReciboDebito->adicionarRecurso($oDotacao->getRecurso());
                $oReciboDebito->setDataRecibo($dataRecibo);
                $oReciboDebito->setDataVencimentoRecibo($dataRecibo);
                $oReciboDebito->setGrupoAutenticacao($this->codigoGrupoAutenticacao);
                $oReciboDebito->setHistorico(str_replace("'", "", $sHistoricoRecibo));
                $oReciboDebito->emiteRecibo();

                $codigoRetencao = $retencao->e21_sequencial;
                $oReciboDebito->autenticarRecibo(
                    $dataRecibo,
                    $empenho->getCaracteristicaPeculiar(),
                    $empenho->getNumero(),
                    $codigoRetencao,
                    7
                );
                $nValorRecibo = $oReciboDebito->getTotalRecibo();
                if (($nValorRecibo == 0) || $nValorRecibo != $retencao->e23_valorretencao) {
                    $sMsgValorRetencao = "A retenção {$retencao->k02_codigo} com valor {$retencao->e23_valorretencao}";
                    $sMsgValorRetencao .= " é diferente do valor total do recibo {$nValorRecibo}.";
                    throw new \Exception($sMsgValorRetencao);
                }
            }
        }

        /**
         * Vinculamos a retencao ao grupo do lancamento
         */
        $oDaoCorrenteGrupo = new \cl_corgrupocorrente;
        $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
            null,
            "*",
            "k105_sequencial desc limit 1",
            "k105_corgrupo = " . $this->codigoGrupoAutenticacao
        );
        $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
        if ($oDaoCorrenteGrupo->numrows == 0) {
            throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
        }
        $oDaoRetencaoCorrente = new \cl_retencaocorgrupocorrente;
        $oDaoRetencaoCorrente->e47_corgrupocorrente = \db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
        $oDaoRetencaoCorrente->e47_retencaoreceita = $retencao->e23_sequencial;
        $oDaoRetencaoCorrente->incluir(null);
        if ($oDaoRetencaoCorrente->erro_status == 0) {
            throw new Exception(
                sprintf(
                    "Não Foi possivel vincular a retencao a autenticação.\n%s",
                    $oDaoRetencaoCorrente->erro_msg
                )
            );
        }
    }

    /**
     * Retorna os dados da nota de liquidacao;.
     *
     * @param  $nota
     * @return \_db_fields|\stdClass
     */
    private function getDadosDaNota($nota)
    {
        $sSqlDadosNota = "select * ";
        $sSqlDadosNota .= "  from empnota ";
        $sSqlDadosNota .= "       inner join  pagordemnota on e71_codnota = e69_codnota";
        $sSqlDadosNota .= "                               and e71_anulado is false";
        $sSqlDadosNota .= "       inner join pagordem on e50_codord = e71_codord";
        $sSqlDadosNota .= "       inner join pagordemele on e50_codord = e53_codord";
        $sSqlDadosNota .= "   where e69_codnota = {$nota}";
        $rsDadosNota = db_query($sSqlDadosNota);
        return \db_utils::fieldsMemory($rsDadosNota, 0);
    }

    /**
     * Define a instituicao de processamento
     *
     * @param $instituicao
     */
    public static function setInstituicaoProcessamento($instituicao)
    {
        $_SESSION["DB_instit_logada"] = $_SESSION["DB_instit"];
        if (!empty($instituicao)) {
            $_SESSION["DB_instit"] = $instituicao;
            db_query("select fc_putsession('DB_instit', '{$instituicao}')");
        }
    }


    /**
     * restaura os dados da sessão do usuario
     */
    public static function restaurarInstituicaoUsuario()
    {

        db_putsession("DB_instit", $_SESSION["DB_instit_logada"]);
        db_query("select fc_putsession('DB_instit', '{$_SESSION["DB_instit_logada"]}')");
    }


    /**
     * Retorna o documento que devemos executar
     *
     * @param  $retencao
     * @return int
     */
    private function getDocumentoExecutar($retencao)
    {
        if ($this->estorno) {
            return $this->getDocumentoEstornoExecutar($retencao);
        }
        $codigoDocumento = $retencao->k02_tipo === 'O' ? 6004 : 6002;

        /**
         * Verificamos se o empenho é de restos a pagar
         */
        if ($this->empenho->isRP($this->ano)) {
            $dadosDaNota = $this->getDadosDaNota($this->nota);
            $codigoDocumento = 6010;
            if ($this->ano > $dadosDaNota->e50_anousu) {
                $codigoDocumento = 6008;
            }
        }
        return $codigoDocumento;
    }

    /**
     * Retorna o documento que devemos executar
     *
     * @param  $retencao
     * @return int
     */
    private function getDocumentoEstornoExecutar($retencao)
    {
        $codigoDocumento = $retencao->k02_tipo === 'O' ? 6005 : 6003;
        /**
         * Verificamos se o empenho é de restos a pagar
         */
        if ($this->empenho->isRP($this->ano)) {
            $dadosDaNota = $this->getDadosDaNota($this->nota);
            $codigoDocumento = 6011;
            if ($this->ano > $dadosDaNota->e50_anousu) {
                $codigoDocumento = 6009;
            }
        }
        return $codigoDocumento;
    }

    /**
     * Retorna o código do lancamento contabil do empenho,
     *
     * @return int
     */
    public function getCodigoLancamentoEstorno()
    {
        return $this->codigoLancamentoEmpenho;
    }

    /**
     * Retorna os códigos de lançamentos gerados no estorno da apropriação.
     *
     * @return array
     */
    public function getCodigosLancamentos()
    {
        return $this->codigosLancamentos;
    }


    /**
     * Realiza o estorno da Recibo de recolhimento da receita no ente Recebedor.
     *
     * @param  $oRetencao
     * @return bool
     * @throws \BusinessException
     * @throws \DBException
     * @throws \ParameterException
     * @throws Exception
     */
    public function estornarRecidosNoEnteRecebedor($oRetencao)
    {

        $oDotacao = $this->empenho->getDotacao();
        $cgm = $this->empenho->getCgm();
        $oInstit = \db_stdClass::getDadosInstit();
        $conta = \Transferencia::getContaPagadoraParaRetencaoDoCredor($oRetencao->credor);
        if ($oRetencao->e23_valorretencao > 0) {
            if ($oRetencao->e21_retencaotipocalc != 5 || $oInstit->prefeitura == "f") {
                $oReciboAvulso = new recibo(1, $cgm->getCodigo());
                $oReciboAvulso->setConta($conta);
                $oReciboAvulso->setGrupoAutenticacao($this->codigoGrupoAutenticacao);
                $oReciboAvulso->adicionarRecurso($oDotacao->getRecurso());
                if (isset($oReciboAvulso)) {
                    $iNumpre = retencaoNota::getNumpreRetencao($oRetencao->e23_sequencial, 7);
                    $sSqlBuscaCaracteristica = "
                    select empempenho.e60_concarpeculiar
					  from retencaoreceitas
					  join retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
					  join empagemov         on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
					  join empempenho        on empempenho.e60_numemp = empagemov.e81_numemp
					 where retencaoreceitas.e23_sequencial = {$oRetencao->e23_sequencial}";
                    $sCaracteristicaPeculiar = db_utils::fieldsMemory(
                        db_query($sSqlBuscaCaracteristica),
                        0
                    )->e60_concarpeculiar;
                    $codigoRetencao = null;
                    if (APROPRIACAO_RETENCAO) {
                        $codigoRetencao = $oRetencao->e21_sequencial;
                    }

                    $oReciboAvulso->estornarRecibo(
                        $iNumpre,
                        $sCaracteristicaPeculiar,
                        null,
                        $codigoRetencao,
                        8
                    );
                }
            } else {
                include_once modification('model/planilhaRetencao.model.php');
                $oReciboDebito = new  recibo(2, null, 25);
                $oReciboDebito->setConta($conta);
                $oReciboDebito->setGrupoAutenticacao($this->codigoGrupoAutenticacao);
                $iNumpre = retencaoNota::getNumpreRetencao($oRetencao->e23_sequencial, 7);
                $oReciboDebito->adicionarRecurso($oDotacao->getRecurso());
                $codigoRetencao = null;
                if (APROPRIACAO_RETENCAO) {
                    $codigoRetencao = $oRetencao->e21_sequencial;
                }
                $sSqlBuscaCaracteristica = "
                select empempenho.e60_concarpeculiar
                 from retencaoreceitas
                 join retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
                 join empagemov         on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
                 join empempenho        on empempenho.e60_numemp = empagemov.e81_numemp
                where retencaoreceitas.e23_sequencial = {$oRetencao->e23_sequencial}";
                $sCaracteristicaPeculiar = db_utils::fieldsMemory(db_query($sSqlBuscaCaracteristica), 0)
                    ->e60_concarpeculiar;

                $oReciboDebito->estornarRecibo(
                    $iNumpre,
                    $sCaracteristicaPeculiar,
                    null,
                    $codigoRetencao,
                    8
                );
                $oPlanilha = retencaoNota::getPlanilhaRetencao($oRetencao->e23_sequencial, 7);
                if (!$oPlanilha) {
                    throw new Exception("Erro [3] - Não foi possivel encontrar planilha de retencao.");
                } else {
                    $oPlanilhaRetencao = new planilhaRetencao($oPlanilha->q20_planilha);
                    $oPlanilhaRetencao->anularPlanilha("Estorno de recolhimento de Retenção");
                }
            }
            /**
             * Vinculamos a retencao ao grupo do lancamento
             */
            $oDaoCorrenteGrupo = db_utils::getDao("corgrupocorrente");
            $sSqlCorgrupo = $oDaoCorrenteGrupo->sql_query_file(
                null,
                "*",
                "k105_sequencial desc limit 1",
                "k105_corgrupo = " . $this->codigoGrupoAutenticacao
            );
            $rsCorGrupo = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
            if ($oDaoCorrenteGrupo->numrows == 0) {
                throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
            }
            $oDaoRetencaoCorrente = db_utils::getDao("retencaocorgrupocorrente");
            $oDaoRetencaoCorrente->e47_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo, 0)->k105_sequencial;
            $oDaoRetencaoCorrente->e47_retencaoreceita = $oRetencao->e23_sequencial;
            $oDaoRetencaoCorrente->incluir(null);
            if ($oDaoRetencaoCorrente->erro_status == 0) {
                throw new Exception(
                    "Não Foi possivel vincular a retencao a autenticação.\n{$oDaoRetencaoCorrente->erro_msg}"
                );
            }
        }
        return true;
    }


    /**
     * Realiza o p
     *
     * @param  $retencao
     * @param bool $estorno
     * @throws Exception
     */
    protected function realizarLancamentoExtraNoEnteDaRetencao($retencao, $estorno = false)
    {
        $instituicaoSessao = $_SESSION["DB_instit"];
        try {
            $_SESSION["DB_instit"] = $retencao->e21_enterecebedor;

            $receitaPagadora = retencaoNota::getContaContabilDaReceitaDaTesouraria(
                $retencao->e21_receitaenterecebedor,
                $this->ano,
                $retencao->e21_enterecebedor
            );
            if (empty($receitaPagadora)) {
                $mensagem = "Não foi encontrada uma conta contábil para a receita  da tesouraria ({$receitaPagadora})";
                $mensagem .= " na instituicao {$instituicaoSessao}.\n";
                $mensagem .= "Para realizar a apropriação é necessário que a receita  da tesouraria, ";
                $mensagem .= "( DB:FINANCEIRO > Tesouraria > Cadastros > Manutenção de Receitas  ),\n";
                $mensagem .= "esteja vinculada a uma conta contábil (DB:FINANCEIRO > Contabilidade > Cadastros ";
                $mensagem .= "> Plano de Contas Orçamentario).";
                throw new \BusinessException($mensagem);
            }

            $documento = !$estorno ? 6012 : 6013;
            $oLancamentoAuxiliarRetencao = new \LancamentoAuxiliarApropriacaoRetencao();
            $oLancamentoAuxiliarRetencao->setObservacaoHistorico($this->getObservacaoDaRetencao($retencao));
            $oLancamentoAuxiliarRetencao->setFavorecido($this->empenho->getFornecedor()->getCodigo());
            $oLancamentoAuxiliarRetencao->setEmpenhoFinanceiro($this->empenho);
            $oLancamentoAuxiliarRetencao->setNumeroEmpenho($this->empenho->getNumero());
            $oLancamentoAuxiliarRetencao->setValorTotal($retencao->e23_valorretencao);
            $oLancamentoAuxiliarRetencao->setRetencao($retencao->e21_sequencial);
            $oLancamentoAuxiliarRetencao->setCodigoReduzido($receitaPagadora);
            $oLancamentoAuxiliarRetencao->setTipoCalculoRetencao($retencao->e21_retencaotipocalc);
            $oLancamentoAuxiliarRetencao->setEstorno($this->estorno);
            $oEventoContabil = \EventoContabilRepository::getEventoContabilByCodigo(
                $documento,
                db_getsession("DB_anousu"),
                $retencao->e21_enterecebedor
            );
            $oEventoContabil->executaLancamento($oLancamentoAuxiliarRetencao);
            $_SESSION["DB_instit"] = $instituicaoSessao;
        } catch (Exception $e) {
            $_SESSION["DB_instit"] = $instituicaoSessao;
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * @param int $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @return int
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * @param int $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * @return int
     */
    public function getCodigoGrupoAutenticacao()
    {
        return $this->codigoGrupoAutenticacao;
    }

    /**
     * @param int $codigoGrupoAutenticacao
     */
    public function setCodigoGrupoAutenticacao($codigoGrupoAutenticacao)
    {
        $this->codigoGrupoAutenticacao = $codigoGrupoAutenticacao;
    }

    /**
     * @return int
     */
    public function getOrdemPagamento()
    {
        return $this->ordemPagamento;
    }

    /**
     * @param int $ordemPagamento
     */
    public function setOrdemPagamento($ordemPagamento)
    {
        $this->ordemPagamento = $ordemPagamento;
    }

    /**
     * @return bool
     */
    public function isEstorno()
    {
        return $this->estorno;
    }

    /**
     * @param bool $estorno
     */
    public function setEstorno($estorno)
    {
        $this->estorno = $estorno;
    }

    /**
     * @return int
     */
    public function getCodigoLancamentoEmpenho()
    {
        return $this->codigoLancamentoEmpenho;
    }

    /**
     * @param int $codigoLancamentoEmpenho
     */
    public function setCodigoLancamentoEmpenho($codigoLancamentoEmpenho)
    {
        $this->codigoLancamentoEmpenho = $codigoLancamentoEmpenho;
    }

    /**
     * Criado função para preparar os slips de transferências bancárias de retenções.
     *
     * Essa função só é usada quando:
     * - o parâmetro Gerar Slip Automático das Retenções e Transferências: estiver configurado como
     * "Não gera slips automático"
     * - Apenas quando o empenho é de recurso vinculado
     * - Retenção Orçamentária
     *
     * @param $retencao
     * @param $movimento
     * @return void
     * @throws Exception
     */
    private function gerarDadosParaSlipOrcamentario($retencao, $movimento)
    {
        $sql = "
            select k13_conta as creditar,
                   coalesce(k103_contrapartida, 0) as debitar
            from empagepag
            left join empagetipo on empagetipo.e83_codtipo = empagepag.e85_codtipo
            left join saltes on saltes.k13_conta = empagetipo.e83_conta
            left join saltescontrapartida on saltescontrapartida.k103_saltes = saltes.k13_conta
            where e85_codmov = $movimento
        ";

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception('Erro ao buscar conta da contra partida.');
        }

        $contas = db_utils::fieldsMemory($rs, 0);

        $oDaoEmpAgeSlip = new \cl_empagemovslips;
        $oDaoEmpAgeSlip->k107_data = $this->dataEvento->format("Y-m-d");
        $oDaoEmpAgeSlip->k107_empagemov = $movimento;
        $oDaoEmpAgeSlip->k107_valor = $retencao->e23_valorretencao;
        $oDaoEmpAgeSlip->k107_ctadebito = empty($contas->debitar) ? 0 : $contas->debitar;
        $oDaoEmpAgeSlip->k107_ctacredito = empty($contas->creditar) ? 0 : $contas->creditar;
        $oDaoEmpAgeSlip->k107_retencao = $retencao->e23_sequencial;
        $oDaoEmpAgeSlip->incluir(null);
        if ($oDaoEmpAgeSlip->erro_status == 0) {
            throw new Exception("Erro Ao incluir informações do slip.\n Processamento cancelado.");
        }
    }
}
