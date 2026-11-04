<?php
require_once modification("libs/db_stdlib.php");
require_once modification("std/db_stdClass.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/JSON.php");
require_once modification("libs/db_libcontabilidade.php");
require_once modification("libs/db_liborcamento.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("model/slip.model.php");
require_once modification("interfaces/ILancamentoAuxiliar.interface.php");
require_once modification("interfaces/IRegraLancamentoContabil.interface.php");
require_once modification("model/caixa/slip/Transferencia.model.php");
require_once modification("model/configuracao/Instituicao.model.php");
require_once modification("model/CgmFactory.model.php");
require_once modification(Modification::getFile('model/agendaPagamento.model.php'));
require_once modification("model/contabilidade/planoconta/ContaPlano.model.php");

db_app::import("MaterialCompras");
db_app::import("caixa.*");
db_app::import("caixa.slip.*");
db_app::import("exceptions.*");
db_app::import("contabilidade.*");
db_app::import("financeiro.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("contabilidade.contacorrente.*");
db_app::import("contabilidade.planoconta.*");
db_app::import("orcamento.*");
db_app::import("configuracao.*");

$oJson             = new services_json();
$oParam            = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno          = new stdClass();
$oRetorno->status  = 1;
$oRetorno->message = '';
$oRetorno->erro    = false;

$iAnoSessao         = db_getsession("DB_anousu");
$iInstituicaoSessao = db_getsession("DB_instit");


switch ($oParam->exec) {

case "getDadosSlip" :

    try {

        $oSlip                            = TransferenciaFactory::getInstance(null, $oParam->iCodigoSlip);

        $oRetorno->iDocumentoContabil     = $oSlip->getDocumentoPorTipoInclusao();
        $oRetorno->iTipoOperacao          = (int)$oSlip->getTipoOperacaoPorInclusao();
        $oRetorno->iCodigoSlip            = $oSlip->getCodigoSlip();
        $oRetorno->iContaDebito           = $oSlip->getContaDebito();
        $oRetorno->sDescricaoContaDebito  = urlencode(ContaPlano::getDescricaoContaPorReduzido($oSlip->getContaDebito()));
        $oRetorno->iContaCredito          = $oSlip->getContaCredito();
        $oRetorno->sDescricaoContaCredito = urlencode(ContaPlano::getDescricaoContaPorReduzido($oSlip->getContaCredito()));
        $oRetorno->nValor                 = $oSlip->getValor();
        $oRetorno->iHistorico             = $oSlip->getHistorico();
        $oRetorno->sHistorico             = "";
        $oRetorno->sObservacao            = urlencode($oSlip->getObservacao());
        $oRetorno->iTipoPagamento         = $oSlip->getTipoPagamento();
        $oRetorno->iSituacao              = $oSlip->getSituacao();
        $oRetorno->dtData                 = $oSlip->getData();

        $oRetorno->sCaracteristicaDebito  = $oSlip->getCaracteristicaPeculiarDebito();
        $oRetorno->sCaracteristicaCredito = $oSlip->getCaracteristicaPeculiarCredito();

        $oRetorno->iInstituicaoDestino    = "null";
        $oRetorno->sInstituicaoDestino    = "null";

        if ($oSlip instanceof TransferenciaFinanceira) {

            $oInstituicaoDestino           = new Instituicao($oSlip->getInstituicaoDestino());
            $oRetorno->iInstituicaoDestino = $oSlip->getInstituicaoDestino();
            $oRetorno->sInstituicaoDestino = urlencode($oInstituicaoDestino->getDescricao());
        }

        $oRetorno->iInstituicaoOrigem = $oSlip->getInstituicao();

        $iInstituicaoOrigem = $iInstituicaoSessao;

        if (isset($oParam->lRecebimento) && $oParam->lRecebimento) {
            $iInstituicaoOrigem = $oSlip->getInstituicao();
        }

        $oInstituicao                          = new Instituicao($iInstituicaoOrigem);
        $oRetorno->sDescricaoInstituicaoOrigem = urlencode($oInstituicao->getDescricao());

        /**
         * Busca os dados do CGM favorecido/concessor
         */
        $oCgm                 = CgmFactory::getInstanceByCgm($oSlip->getCodigoCgm());
        $oRetorno->iCodigoCgm = $oSlip->getCodigoCgm();
        $oRetorno->sNomeCgm   = urlencode($oCgm->getNome());
        if (method_exists($oCgm, "getCnpj")) {
            $oRetorno->sCNPJ = db_formatar($oCgm->getCnpj(), "cnpj");
        } else {
            $oRetorno->sCNPJ = db_formatar($oCgm->getCpf(), "cpf");
        }

        // buscamos o processo
        $oRetorno->k145_numeroprocesso = null;
        $oDaoSlipProcesso = new cl_slipprocesso();
        $sSqlSlipProcesso = $oDaoSlipProcesso->sql_query_file(null, "*", null, "k145_slip = {$oParam->iCodigoSlip}");
        $rsSlipProcesso   = $oDaoSlipProcesso->sql_record($sSqlSlipProcesso);
        if ($oDaoSlipProcesso->numrows > 0) {

            $oDadosSlipProcesso = db_utils::fieldsMemory($rsSlipProcesso, 0);
            $oRetorno->k145_numeroprocesso = urlencode($oDadosSlipProcesso->k145_numeroprocesso);
        }

        /*
         * descrição dos demais campos:
         *
         * conta debito
         * caracteristica debito
         * conta credito
         * caracteristica credito
         * descricao do historico
         */
        $oCaracteristicaCredito = new CaracteristicaPeculiar($oRetorno->sCaracteristicaCredito);
        $oCaracteristicaDebito  = new CaracteristicaPeculiar($oRetorno->sCaracteristicaDebito);
        $oRetorno->sCaracteristicaPeculiarCredito = urlencode($oCaracteristicaCredito->getDescricao());
        $oRetorno->sCaracteristicaPeculiarDebito  = urlencode($oCaracteristicaDebito->getDescricao());

        $oDaoConHist = new cl_conhist;
        $sSqlConHist = $oDaoConHist->sql_query($oRetorno->iHistorico);
        $rsConHist   = $oDaoConHist->sql_record($sSqlConHist);
        if ($oDaoConHist->numrows > 0) {
          $oRetorno->sHistorico = urlencode(db_utils::fieldsMemory($rsConHist, 0)->c50_descr);
        }

        $parametroMomentoSlipAuto = ParametroCaixa::getParametroMomentoSlipAutomatico(db_getsession("DB_anousu"));
        /*
         * Verificamos se o slip possui configuração na agenda
         */
        $oRetorno->configuracao_agenda = 'false';
        $clEmpAgeConf = new cl_empageconf();
        $rsEmpageConf   = $clEmpAgeConf->sql_record($clEmpAgeConf->sql_query_file($oSlip->getMovimento()));
        if ($clEmpAgeConf->numrows > 0 && $parametroMomentoSlipAuto == 2) {
            $oRetorno->configuracao_agenda = 'true';
        }

    } catch (Exception $eErro) {

         $oRetorno->message = urlencode(str_replace("\n", "\\n", $eErro->getMessage()));
         $oRetorno->status  = 2;
         db_fim_transacao(true);
    }


    break;

  /**
   * Salva os dados do slip
   */
case "salvarSlip":

    db_inicio_transacao();
    try {

        $iCodigoSlip = null;
        if (isset($oParam->k17_codigo) && !empty($oParam->k17_codigo)) {
            $iCodigoSlip = $oParam->k17_codigo;
        }

        $oContaDebito  = new ContaPlanoPCASP(null, db_getsession("DB_anousu"), $oParam->k17_debito, null);
        $oContaCredito = new ContaPlanoPCASP(null, db_getsession("DB_anousu"), $oParam->k17_credito, null);

        if ($oContaDebito->getReduzido() == '') {
            throw new BusinessException("A Conta Débito informada é inválida.");
        }

        if ($oContaCredito->getReduzido() == '') {
            throw new BusinessException("A Conta Crédito informada é inválida.");
        }

        /**
         * devemos validar a conta da tesouraria vinculada aos reduzidos
         * nao pode permitir usar uma conta que tenha data limite.
         */
        if ($oContaCredito->getContaTesouraria()->getDataLimite() != null) {

            throw new BusinessException("A Conta Credito informada possui data limite.");
        }
        if ($oContaDebito->getContaTesouraria()->getDataLimite() != null) {

            throw new BusinessException("A Conta Debito informada possui data limite.");
        }

        $oTransferencia = TransferenciaFactory::getInstance($oParam->iCodigoTipoOperacao, $iCodigoSlip);
        $oTransferencia->setContaDebito($oParam->k17_debito);
        $oTransferencia->setContaCredito($oParam->k17_credito);
        $oTransferencia->setValor(str_replace(",", ".", $oParam->k17_valor));
        $oTransferencia->setHistorico($oParam->k17_hist);
        $oTransferencia->setObservacao(addslashes(db_stdClass::normalizeStringJsonEscapeString($oParam->k17_texto)));
        $oTransferencia->setTipoPagamento(0);
        $oTransferencia->setSituacao(1);
        $oTransferencia->setCodigoCgm($oParam->iCGM);
        $oTransferencia->setCaracteristicaPeculiarDebito($oParam->sCaracteristicaPeculiarDebito);
        $oTransferencia->setCaracteristicaPeculiarCredito($oParam->sCaracteristicaPeculiarCredito);
        $oTransferencia->setData(date("Y-m-d", db_getsession("DB_datausu")));
        $oTransferencia->setProcessoAdministrativo(db_stdClass::normalizeStringJsonEscapeString($oParam->k145_numeroprocesso));

        if ($oTransferencia instanceof TransferenciaFinanceira) {
            $oTransferencia->setInstituicaoDestino($oParam->iCodigoInstituicaoDestino);
        }

        $oTransferencia->salvar();

        /**
         * Se o tipo de operacao do slip importado for pagamento, mais precisamente, 9 ou 13.
         */
        $aTipoOperacao = [9,13];
        if(in_array($oParam->iCodigoTipoOperacao,$aTipoOperacao)){
            $slipImportacao = Slip::getSlipCompleto($oParam->codigoSlipAux);
            Slip::salvarVinculoImportacao($slipImportacao, $oTransferencia->getCodigoSlip());
        }

        $oDaoExcluiFinalidade = new cl_slipfinalidadepagamentofundeb();
        $oDaoExcluiFinalidade->excluir(null, "e153_slip = {$oTransferencia->getCodigoSlip()}");
        if ($oDaoExcluiFinalidade->erro_status == "0") {
            throw new Exception("Não foi possível desvincular a finalidade.");
        }

        if (!empty($oParam->sCodigoFinalidadeFundeb)) {

            $oFinalidadePagamento = FinalidadePagamentoFundeb::getInstanciaPorCodigo($oParam->sCodigoFinalidadeFundeb);
            $oTransferencia->setFinalidadePagamentoFundebCredito($oFinalidadePagamento);
            $oTransferencia->salvarFinalidadePagamentoFundeb();
        }

        if (isset($oParam->iInscricao)) {

            $iCodigoSlip       = $oTransferencia->getCodigoSlip();
            LancamentoAuxiliarSlip::vinculaSlipInscricao($iCodigoSlip, $oParam->iInscricao);
        }

        $oRetorno->message     = urlencode("Transferência {$oTransferencia->getCodigoSlip()} salva com sucesso.");
        $oRetorno->iCodigoSlip = $oTransferencia->getCodigoSlip();


        /**
         * se o parametro da tesouraria slip::getParametroSlipAutomaticoLiquidacao() estiver true
         * sera gerado um slip adicional invertendo debito e credito para esse novo
         * slip gerado, o restante dos dados segue igual ao slip original
         *
         * os criterios são:
         *   o parametro da tesouraria  estar ativado (Gerar Slip Automático das Retenções:)
         *   para os tipos de Operação ($oParam->iCodigoTipoOperacao)  07 , 11
         */
        $oRetorno->slipAutomatico = "";
        if (slip::getParametroSlipAutomaticoLiquidacao()) {

            $aTiposPagamentos = array(7, 11);

            if (in_array($oParam->iCodigoTipoOperacao, $aTiposPagamentos) ) {

                switch($oParam->iCodigoTipoOperacao){

                case 7:
                    $iTipoPagamento = 9;
                    break;

                case 11:
                    $iTipoPagamento = 13;
                    break;
                }

                $sObservacao = "Correspondente ao recolhimento do valor apropriado através do ";
                $sObservacao .= "Slip {$oTransferencia->getCodigoSlip()}.";
                $oNovoSlip = new Slip();
                $oNovoSlip->setContaCredito($oTransferencia->getContaDebito());
                $oNovoSlip->setContaDebito($oTransferencia->getContaCredito());
                $oNovoSlip->setCaracteristicaPeculiarCredito($oTransferencia->getCaracteristicaPeculiarDebito());
                $oNovoSlip->setCaracteristicaPeculiarDebito($oTransferencia->getCaracteristicaPeculiarCredito());
                $oNovoSlip->setValor($oTransferencia->getValor());
                $oNovoSlip->setTipoPagamento($oTransferencia->getTipoPagamento());
                $oNovoSlip->setSituacao(1);
                $oNovoSlip->setNumCgm($oTransferencia->getCodigoCgm());
                $oNovoSlip->setHistorico($oTransferencia->getHistorico());
                $oNovoSlip->setObservacoes($sObservacao);
                $oNovoSlip->save();
                Slip::vincularTipoOperacaoSlip($oNovoSlip->getSlip(), $iTipoPagamento);
                Slip::vincularSlipOperacaoExtra($oTransferencia->getCodigoSlip(), $oNovoSlip->getSlip());

                $oRetorno->slipAutomatico = $oNovoSlip->getSlip();
            }
        }

        db_fim_transacao(false);

    } catch (Exception $eErro) {

        $oRetorno->message = str_replace("\n", "\\n", urlencode($eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }

    break;

case "efetuarPagamentoSlip":

      db_inicio_transacao();

    try {

        $iCodigoSlip = null;
        if (isset($oParam->k17_codigo) && !empty($oParam->k17_codigo)) {
            $iCodigoSlip = $oParam->k17_codigo;
        }

        $oContaDebito  = new ContaPlanoPCASP(null, db_getsession("DB_anousu"), $oParam->k17_debito, null);
        $oContaCredito = new ContaPlanoPCASP(null, db_getsession("DB_anousu"), $oParam->k17_credito, null);

        if ($oContaDebito->getReduzido() == '') {
            throw new BusinessException("A Conta Débito informada é inválida.");
        }
        if ($oContaCredito->getReduzido() == '') {
            throw new BusinessException("A Conta Crédito informada é inválida.");
        }

        $oTransferencia = TransferenciaFactory::getInstance($oParam->iCodigoTipoOperacao, $iCodigoSlip);
        $oTransferencia->setContaDebito($oParam->k17_debito);
        $oTransferencia->setContaCredito($oParam->k17_credito);
        $oTransferencia->setValor(str_replace(",", ".", $oParam->k17_valor));
        $oTransferencia->setHistorico($oParam->k17_hist);
        $oTransferencia->setObservacao(addslashes(db_stdClass::normalizeStringJsonEscapeString($oParam->k17_texto)));
        $oTransferencia->setTipoPagamento(0);
        $oTransferencia->setSituacao(1);
        $oTransferencia->setCodigoCgm($oParam->iCGM);
        $oTransferencia->setCaracteristicaPeculiarDebito($oParam->sCaracteristicaPeculiarDebito);
        $oTransferencia->setCaracteristicaPeculiarCredito($oParam->sCaracteristicaPeculiarCredito);
        $oTransferencia->setData(date("Y-m-d", db_getsession("DB_datausu")));
        $oTransferencia->setProcessoAdministrativo(db_stdClass::normalizeStringJsonEscapeString($oParam->k145_numeroprocesso));

        if ($oTransferencia instanceof TransferenciaFinanceira) {
            $oTransferencia->setInstituicaoDestino($oParam->iCodigoInstituicaoDestino);
        }

        $oTransferencia->salvar();

        $oDaoExcluiFinalidade = new cl_slipfinalidadepagamentofundeb();
        $oDaoExcluiFinalidade->excluir(null, "e153_slip = {$oTransferencia->getCodigoSlip()}");
        if ($oDaoExcluiFinalidade->erro_status == "0") {
            throw new Exception("Não foi possível desvincular a finalidade.");
        }

        if (!empty($oParam->sCodigoFinalidadeFundeb)) {

            $oFinalidadePagamento = FinalidadePagamentoFundeb::getInstanciaPorCodigo($oParam->sCodigoFinalidadeFundeb);
            $oTransferencia->setFinalidadePagamentoFundebCredito($oFinalidadePagamento);
            $oTransferencia->salvarFinalidadePagamentoFundeb();
        }


        if (isset($oParam->iInscricao)) {

            $iCodigoSlip       = $oTransferencia->getCodigoSlip();
            LancamentoAuxiliarSlip::vinculaSlipInscricao($iCodigoSlip, $oParam->iInscricao);
        }

        $oMovimento = new stdClass();
        $oMovimento->iCodMov   = $oTransferencia->getMovimento();
        $oMovimento->iCodForma = 4;
        $oMovimento->nValor    = $oTransferencia->getValor();
        $oMovimento->iContaFornecedor = 0;
        $oMovimento->iContaPagadora   = $oTransferencia->getContaCredito();
        $oMovimento->iCodNota         = $oTransferencia->getCodigoSlip();
        $oMovimento->nValorRetencao   = 0;

        $oDaoEmpAgeTipo = new cl_empagetipo();
        $buscaCodigoContaPagadora = $oDaoEmpAgeTipo->sql_query_file(null, 'e83_codtipo', null, 'e83_conta = '.$oParam->k17_credito);
        $buscaCodigoContaPagadora = db_query($buscaCodigoContaPagadora);
        if (pg_num_rows($buscaCodigoContaPagadora) === 0) {
            throw new Exception("A conta {$oParam->k17_credito} não está cadastrada como conta pagadora. Verifique no menu: Tesouraria > Cadastros > Cadastro de conta pagadora");
        }
        $iCodTipo = db_utils::fieldsMemory($buscaCodigoContaPagadora, 0)->e83_codtipo;
        $oMovimento->iContaPagadora = $iCodTipo;

        $oAgenda= new agendaPagamento();

        $dtPagamento = DBDate::now();
        $oAgenda->configurarPagamentos($dtPagamento, $oMovimento);

        $oDataPagamento   = new DBDate($dtPagamento);
        $oDataEmissaoSlip = new DBDate($oTransferencia->getData());
        if($oDataPagamento->getTimeStamp() < $oDataEmissaoSlip->getTimeStamp()) {
            throw new Exception('Data de pagamento deve ser igual ou superior a data de emissão do slip.');
        }

        $oTransferencia->executaAutenticacao();
        if (USE_PCASP) {
            $oTransferencia->executarLancamentoContabil();
        }

        $oRetorno->message     = urlencode("Transferência {$oTransferencia->getCodigoSlip()} salva com sucesso.");
        $oRetorno->iCodigoSlip = $oTransferencia->getCodigoSlip();
        db_fim_transacao(false);

    } catch (Exception $eErro) {

        $oRetorno->message = str_replace("\n", "\\n", urlencode($eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }

    break;

case "excluirSlip":

    try {

        db_inicio_transacao();


        if (slip::getParametroSlipAutomaticoLiquidacao()) {

            /**
             * exclui o slip extra
             */
            $oSlip = new slip($oParam->iCodigoSlip); // inicia o slip que esta sendo estornado
            $aSlipPagamentoOperacaoExtra = $oSlip->getSlipPagamentoOperacaoExtra();
            foreach($aSlipPagamentoOperacaoExtra as $iSlipExtra){

                $oTransferenciaSlipAutomatico = TransferenciaFactory::getInstance( null, $iSlipExtra);
                $oTransferenciaSlipAutomatico->excluir();
            }

        }

        $oTransferencia = TransferenciaFactory::getInstance(null, $oParam->iCodigoSlip);
        $oTransferencia->excluir();
        $oRetorno->message = urlencode("Slip excluído com sucesso.");

        db_fim_transacao(false);

    } catch (Exception $eErro) {

        $oRetorno->message = str_replace("\n", "\\n", urlencode($eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }

    break;

  /**
   * Recebe um slip originado em outro departamento e executa os lançamentos contabeis
   */
case "receberSlip":

    db_inicio_transacao();
    try {

        $oTransferencia = new TransferenciaFinanceira($oParam->iCodigoSlipRecebido);
        $oTransferencia->setTipoOperacao(3);
        $oTransferencia->setTipoPagamento(0);
        $oTransferencia->setInstituicao($oParam->iCodigoInstituicaoOrigem);
        $oTransferencia->setContaDebito($oParam->k17_debito);
        $oTransferencia->setCaracteristicaPeculiarDebito($oParam->sCaracteristicaPeculiarDebito);
        $oTransferencia->setContaCredito($oParam->k17_credito);
        $oTransferencia->setCaracteristicaPeculiarCredito($oParam->sCaracteristicaPeculiarCredito);
        $oTransferencia->setHistorico($oParam->k17_hist);
        $oTransferencia->setValor(str_replace(",", ".", trim($oParam->k17_valor)));
        $oTransferencia->setObservacao(db_stdClass::normalizeStringJsonEscapeString($oParam->k17_texto));
        $oTransferencia->setData(date("Y-m-d", db_getsession("DB_datausu")));
        $oTransferencia->setProcessoAdministrativo(db_stdClass::normalizeStringJsonEscapeString($oParam->k145_numeroprocesso));

        /**
         * Verifica qual transferência financeira o slip é originário
         * Usa essa informação para que a transferência seja marcada como recebida, na tabela transferenciafinanceirarecebimento
         */
        $oDaoTransferenciaFinanceira = new cl_transferenciafinanceira;
        $sSqlTransferenciaFinanceira = $oDaoTransferenciaFinanceira->sql_query_file(null, "*", null, "k150_slip = {$oParam->iCodigoSlipRecebido}");
        $rsTransferenciaFinanceira   = $oDaoTransferenciaFinanceira->sql_record($sSqlTransferenciaFinanceira);

        if ($oDaoTransferenciaFinanceira->erro_status == "0") {
            throw new Exception("Não foi possível receber a transação.\n\nErro Técnico 1: {$oDaoConPlano->erro_msg}");
        }

        $iCodigoTransferencia = db_utils::fieldsMemory($rsTransferenciaFinanceira, 0)->k150_sequencial;

        if ($oTransferencia instanceof TransferenciaFinanceira) {
            $oTransferencia->setInstituicaoDestino($iInstituicaoSessao);
        }

        $oTransferencia->receberTransferencia($iCodigoTransferencia);
        $oRetorno->message     = urlencode("Transferência {$iCodigoTransferencia} recebida com sucesso.");
        $oRetorno->iCodigoSlip = $oTransferencia->getCodigoSlip();

        db_fim_transacao(false);


    } catch (Exception $eException) {

        $oRetorno->message = str_replace("\\n", "\n", urlencode($eException->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);

    }
    break;


  /**
   * verifica se tem slip automatico de pagamento
   * só para avisar o usuario
   */
  case "buscaSlipAutomatico":

    $oSlipRecebimento = new slip($oParam->k17_codigo);
    $aSlipAuto = $oSlipRecebimento->getSlipPagamentoOperacaoExtra(2);
    $oRetorno->slipAutomatico = implode(", ", $aSlipAuto);

    break;

  /**
   * Anula um slip e executa os lancamentos contábeis, caso haja necessidade
   */
case "anularSlip" :

    db_inicio_transacao();

    try {

        $oTransferencia = TransferenciaFactory::getInstance($oParam->iCodigoTipoOperacao, $oParam->k17_codigo);
        $oTransferencia->anular($oParam->sMotivo);

        /**
         * Verifica existência de lançamento contábil
         */
        $oDaoLancamentoSlip  = new cl_conlancamslip;
        $sSqlLancamento      = $oDaoLancamentoSlip->sql_query_file(null, "*", null, "c84_slip = {$oParam->k17_codigo}");
        $rsLancamento        = $oDaoLancamentoSlip->sql_record($sSqlLancamento);

        if ($oDaoLancamentoSlip->numrows > 0) {
            $oTransferencia->executarLancamentoContabil(null, true);
        }

        if (slip::getParametroSlipAutomaticoLiquidacao()) {

          /**
           * estornar o slip extra gerado automaticamente
           */
          $oSlip = new slip($oParam->k17_codigo); // inicia o slip que esta sendo estornado
          $aSlipPagamentoOperacaoExtra = $oSlip->getSlipPagamentoOperacaoExtra(2); // busca os extras autenticados

          foreach($aSlipPagamentoOperacaoExtra as $iSlipExtra){

              $smg = "Estorno de Slip Gerado automaticamente do SLIP {$oParam->k17_codigo}";
              $oTransferenciaSlipAutomatico = TransferenciaFactory::getInstance( null, $iSlipExtra);
              $oTransferenciaSlipAutomatico->anular($smg);
              $oTransferenciaSlipAutomatico->executarLancamentoContabil(null, true);
          }
        }

        $oRetorno->message = urlencode("Procedimento executado com sucesso.");

        db_fim_transacao(false);

    } catch (Exception $eErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }
    break;

  /**
   * Retorna os dados da transferência que serão apresentados no front-end
   */
case "getDadosTransferencia" :

    try {

        $oTransferencia                   = TransferenciaFactory::getInstance($oParam->iCodigoTipoOperacao, $oParam->k17_codigo);
        $oRetorno->iCodigoSlip            = $oTransferencia->getCodigoSlip();
        $oRetorno->iContaDebito           = $oTransferencia->getContaDebito();
        $oRetorno->iContaCredito          = $oTransferencia->getContaCredito();
        $oRetorno->nValor                 = $oTransferencia->getValor();
        $oRetorno->iHistorico             = $oTransferencia->getHistorico();
        $oRetorno->sObservacao            = urlencode($oTransferencia->getObservacao());
        $oRetorno->iTipoPagamento         = $oTransferencia->getTipoPagamento();
        $oRetorno->iSituacao              = $oTransferencia->getSituacao();
        $oRetorno->dtData                 = $oTransferencia->getData();

        $oRetorno->sCaracteristicaDebito  = $oTransferencia->getCaracteristicaPeculiarDebito();
        $oRetorno->sCaracteristicaCredito = $oTransferencia->getCaracteristicaPeculiarCredito();

        if ($oTransferencia instanceof TransferenciaFinanceira) {
            $oRetorno->iInstituicaoDestino = $oTransferencia->getInstituicaoDestino();
        }

        $oRetorno->iInstituicaoOrigem = $oTransferencia->getInstituicao();

        $iInstituicaoOrigem = $iInstituicaoSessao;
        if (isset($oParam->lRecebimento) && $oParam->lRecebimento) {
            $iInstituicaoOrigem = $oTransferencia->getInstituicao();
        }

        $oInstituicao                          = new Instituicao($iInstituicaoOrigem);
        $oRetorno->sDescricaoInstituicaoOrigem = urlencode($oInstituicao->getDescricao());

        // buscamos o processo
        $oRetorno->k145_numeroprocesso = null;
        $oDaoSlipProcesso = new cl_slipprocesso();
        $sSqlSlipProcesso = $oDaoSlipProcesso->sql_query_file(null, "*", null, "k145_slip = {$oParam->k17_codigo}");
        $rsSlipProcesso   = $oDaoSlipProcesso->sql_record($sSqlSlipProcesso);
        if ($oDaoSlipProcesso->numrows > 0) {

            $oDadosSlipProcesso = db_utils::fieldsMemory($rsSlipProcesso, 0);
            $oRetorno->k145_numeroprocesso = urlencode($oDadosSlipProcesso->k145_numeroprocesso);
        }

        /**
         * Busca os dados do CGM favorecido/concessor
         */
        $oCgm                 = CgmFactory::getInstanceByCgm($oTransferencia->getCodigoCgm());
        $oRetorno->iCodigoCgm = $oTransferencia->getCodigoCgm();
        $oRetorno->sNomeCgm   = urlencode($oCgm->getNome());
        if (method_exists($oCgm, "getCnpj")) {
            $oRetorno->sCNPJ = db_formatar($oCgm->getCnpj(), "cnpj");
        } else {
            $oRetorno->sCNPJ = db_formatar($oCgm->getCpf(), "cpf");
        }

        $daoRecursoConta = new cl_sliprecursocontas();
        $campos = implode(
            ',', array(
            'recursodebito.o15_codigo as codigo_recurso_debito',
            'recursodebito.o15_descr as descricao_recurso_debito',
            'recursocredito.o15_codigo as codigo_recurso_credito',
            'recursocredito.o15_descr as descricao_recurso_credito',
            )
        );
        $buscaRecurso = $daoRecursoConta->sql_query(null, $campos, null, "k181_slip = {$oTransferencia->getCodigoSlip()}");
        $buscaRecurso = db_query($buscaRecurso);

        $oRetorno->codigo_recurso_debito = null;
        $oRetorno->descricao_recurso_debito = null;
        $oRetorno->codigo_recurso_credito = null;
        $oRetorno->descricao_recurso_credito = null;
        if (pg_num_rows($buscaRecurso) > 0) {

            $dados =db_utils::fieldsMemory($buscaRecurso, 0);
            $oRetorno->codigo_recurso_debito  = $dados->codigo_recurso_debito;
            $oRetorno->descricao_recurso_debito = $dados->descricao_recurso_debito;
            $oRetorno->codigo_recurso_credito = $dados->codigo_recurso_credito;
            $oRetorno->descricao_recurso_credito = $dados->descricao_recurso_credito;
        }

        if($oParam->importacaoCG){
            $slipRetencaoReceitas = Slip::getSlipRetencaoReceitas($oTransferencia->getCodigoSlip());
            $slipReceitaPlanilha = Slip::getSlipReceitaPlanilha($oTransferencia->getCodigoSlip());
            $slipOperacaoExtra = Slip::getSlipOperacaoExtra($oTransferencia->getCodigoSlip());

            if(!empty($slipReceitaPlanilha) || !empty($slipOperacaoExtra) || !empty($slipRetencaoReceitas)){
                $oRetorno->vinculo    = true;
            }
        }

    } catch (Exception $eErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }
    break;

  /**
   * Busca as contas caixa/banco da tabela saltes
   */
case "getContasSaltes":

    try{

        $aContasCredito = array();
        $oDaoSaltes     = new cl_saltes;
        $sCamposSaltes  = "k13_reduz as reduzido, k13_descr as descricao";
        $sSqlSaltes     = $oDaoSaltes->sql_query(null, $sCamposSaltes, "k13_reduz");
        $rsDadosSaltes  = $oDaoSaltes->sql_record($sSqlSaltes);

        if ($oDaoSaltes->erro_status == "0") {
            throw new Exception("Não foi possível localizar as contas crédito.");
        }

        if ($oDaoSaltes->numrows > 0) {
            $aContasCredito = db_utils::getCollectionByRecord($rsDadosSaltes, false, false, true);
        }
        $oRetorno->aContas = $aContasCredito;

    } catch (BusinessException $eBEErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eBEErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);

    } catch (Exception $eErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }
    break;

  /**
   * Busca os dados da instituicao da sessao
   */
case 'getDadosInstituicaoOrigem':

    $oInstituicao                       = new Instituicao($iInstituicaoSessao);
    $oRetorno->sInstituicaoOrigem       =  urlencode($oInstituicao->getDescricao());
    $oRetorno->iCodigoInstituicaoOrigem =  $oInstituicao->getSequencial();
    $oRetorno->iCodigoCgm               = $oInstituicao->getCgm()->getCodigo();
    $oRetorno->sNomeCgm                 = urlencode($oInstituicao->getCgm()->getNome());
    $oRetorno->sCNPJ                    = db_formatar($oInstituicao->getCgm()->getCnpj(), "cnpj");
    break;

  /**
   * Transferencias disponiveis para recebimento
   */
case 'pesquisaTranferenciasRecebimento':

    try{

        $oDaoTransferenciaFinanceira       = new cl_transferenciafinanceira;
        $sCamposTransferencia              = "k17_codigo, k17_valor, k17_data,k17_dtaut, k17_instit, k17_hist, k150_instituicao";
        $sWhereTransferencia               = " (k151_sequencial is null or k151_estornado is true) and k150_instituicao = {$iInstituicaoSessao}";
        $sWhereTransferencia              .= " and k17_dtaut is not null ";
        $sWhereTransferencia              .= " and not exists(select 1 ";
        $sWhereTransferencia              .= "             from transferenciafinanceirarecebimento rec";
        $sWhereTransferencia              .= "            where rec.k151_transferenciafinanceira = k150_sequencial";
        $sWhereTransferencia              .= "              and rec.k151_estornado is false)";
        $sWhereTransferencia              .= " group by {$sCamposTransferencia} ";
        $sSqlBuscaTransferenciasPendentes  = $oDaoTransferenciaFinanceira->sql_query_recebimento(null, $sCamposTransferencia, "k17_codigo", $sWhereTransferencia);

        $rsBuscaTransferencia              = $oDaoTransferenciaFinanceira->sql_record($sSqlBuscaTransferenciasPendentes);

        if ($oDaoTransferenciaFinanceira->numrows == 0) {
            throw new Exception("Nenhuma transferência para a instituição.");
        }

        $aTransferenciasRecebimento = array();
        for ($iRowTransferencia = 0; $iRowTransferencia < $oDaoTransferenciaFinanceira->numrows; $iRowTransferencia++) {

            $oDadoTransferencia          = db_utils::fieldsMemory($rsBuscaTransferencia, $iRowTransferencia);
            $oDaoHistorico               = new cl_conhist;
            $sSqlBuscaDescricaoHistorico = $oDaoHistorico->sql_query_file($oDadoTransferencia->k17_hist);
            $rsBuscaHistorico            = $oDaoHistorico->sql_record($sSqlBuscaDescricaoHistorico);
            if ($oDaoHistorico->numrows == 0) {
                throw new Exception("Não foi possível localizar o histórico.");
            }

            $oDadoTransferencia->c50_compl = urlencode(db_utils::fieldsMemory($rsBuscaHistorico, 0)->c50_descr);

            $oInstituicaoOrigem = new Instituicao($oDadoTransferencia->k17_instit);
            $oDadoTransferencia->sInstituicaoOrigem = urlencode($oInstituicaoOrigem->getDescricao());
            $oDadoTransferencia->k17_data = db_formatar($oDadoTransferencia->k17_dtaut, "d");
            $oDadoTransferencia->nValor   = $oDadoTransferencia->k17_valor;
            $aTransferenciasRecebimento[] = $oDadoTransferencia;
        }
        $oRetorno->aTransferenciasRecebimento = $aTransferenciasRecebimento;

    } catch (BusinessException $eBEErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eBEErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);

    } catch (Exception $eErro) {

        $oRetorno->message = urlencode(str_replace("\n", "\\n", $eErro->getMessage()));
        $oRetorno->status  = 2;
        db_fim_transacao(true);
    }
    break;

case 'pesquisaEstornoRecebimento':

    try{

        $oDaoRecebimento = new cl_transferenciafinanceirarecebimento;
        /**
         * Busca todas transferências com Situação igual a 2
         * Slip Autenticado
         */
        $sWhere          = "slip.k17_instit = {$iInstituicaoSessao} and slip.k17_situacao = 2";

        /* [Extensão] - Filtro da Despesa */

        $sSqlRecebimento = $oDaoRecebimento->sql_query(null, "*", null, $sWhere);
        $rsRecebimento   = $oDaoRecebimento->sql_record($sSqlRecebimento);

        if ($oDaoRecebimento->erro_status == "0") {
            throw new Exception("Não há transferências para efetuar recebimento.");
        }


        $oRetorno->aTransferenciasRecebimento = array();

        for ($i = 0; $i < $oDaoRecebimento->numrows; $i++) {

            $oConsulta      = db_utils::fieldsMemory($rsRecebimento, $i);

            $oTransferenciaFinanceira = new TransferenciaFinanceira($oConsulta->k17_codigo);

            /**
             *  Se não possuir recebimento, ela deve ser listada na Grid
             */
            $oTransferencia = new stdClass();

            //Seta Propriedade Código do slip e valor da transferência
            $oTransferencia->k17_codigo = $oConsulta->k17_codigo;
            $oTransferencia->nValor     = db_formatar($oConsulta->k17_valor, "f");
            $oTransferencia->k17_data   = $oTransferenciaFinanceira->getData();



            //Busca e seta Instituição Origem
            $iInstitOrigem       = $oConsulta->k17_instit;
            $oDaoInstituicao     = new cl_db_config;
            $sSqlInstituicao     = $oDaoInstituicao->sql_query(null, "nomeinst", null, "codigo = {$iInstitOrigem}");
            $rsInstit            = $oDaoInstituicao->sql_record($sSqlInstituicao);
            $sInstituicaoOrigem  = urlencode(db_utils::fieldsMemory($rsInstit, 0)->nomeinst);
            unset($oDaoInstituicao);

            $oTransferencia->sInstituicaoOrigem = $sInstituicaoOrigem;

            //Busca e seta descrição Histórico
            $iHistorico     = $oConsulta->k17_hist;
            $oDaoHistorico  = new cl_conhist;
            $sSQLHistorico  = $oDaoHistorico->sql_query($iHistorico, "c50_descr");
            $rsHistorico    = $oDaoHistorico->sql_record($sSQLHistorico);
            $sHistorico     = db_utils::fieldsMemory($rsHistorico, 0)->c50_descr;
            unset($oDaoHistorico);
            $oTransferencia->c50_compl = $sHistorico;

            $oRetorno->aTransferenciasRecebimento[] = $oTransferencia;

            unset($oTransferencia);
            unset($oConsulta);
        }


    } catch (Exception $eErro) {

        $oRetorno->message = urlencode(str_replace("\\n", "\n", $eErro->getMessage()));
        $oRetorno->status  = 2;
    }
    break;

  /**
   * Retornas as contas de um evento contabil de ordem 1
   *
   * Valido se o parametro lContaCredito esta setado e é 'true', caso seja, alteramos o método que será utilizado para
   * buscar as contas
   */
case "getContaEventoContabil":

    try {

        /*
        * Verifico que método utilizar para buscar as contas na conplano
        */
        $sMetodoConta = 'getContaDebito';
        if (isset($oParam->lContaCredito) && $oParam->lContaCredito == true) {
            $sMetodoConta = 'getContaCredito';
        }

        $aContasEvento   = array();
        $oEventoContabil = new EventoContabil(getDocumentoPorTipoInclusao($oParam->iTipoTransferencia), $iAnoSessao);
        $aLancamentos    = $oEventoContabil->getEventoContabilLancamento();
        foreach ($aLancamentos as $oLancamento) {

            if ($oLancamento->getOrdem() == 1) {

                $aRegrasLancamento = $oLancamento->getRegrasLancamento();
                foreach ($aRegrasLancamento as $oContaRegraLancamento) {


                    $oDaoConPlano   = new cl_conplano;
                    $sCamposPlano   = " c61_reduz as reduzido, c60_descr  as descricao";
                    $sWherePlano    = " conplanoreduz.c61_reduz = {$oContaRegraLancamento->$sMetodoConta()} ";
                    $sSqlDadosConta = $oDaoConPlano->sql_query(null, null, $sCamposPlano, null, $sWherePlano);
                    $rsDadosConta   = $oDaoConPlano->sql_record($sSqlDadosConta);

                    if ($oDaoConPlano->erro_status == "0") {
                        throw new Exception("Não foi possível localizar as contas para débito.");
                    }

                    if ($oDaoConPlano->numrows > 0) {

                        $oDadoConta      = db_utils::fieldsMemory($rsDadosConta, 0);
                        $aContasEvento[] = $oDadoConta;
                    }
                }
                break;
            }
            break;
        }
        $oRetorno->aContas = $aContasEvento;

    } catch (Exception $eException) {

        $oRetorno->message = urlencode(str_replace("\\n", "\n", $eException->getMessage()));
        $oRetorno->status  = 2;
    }

    break;

  /**
   * Busca conta do lançamento da inscrição
   * quando for do tipo baixa de pagamento
   */
case "getContaFavorecido":

    $oDAOInscricaoPassivo     = new cl_inscricaopassivo;
    $sCampos                  = "c69_credito as conta_credito_reduzido, ";
    $sCampos                 .= "c36_cgm     as cgm_favorecido,  ";
    $sCampos                 .= "c60_descr   as descricao  ";
    $sWhere                   = "     conlancaminscricaopassivo.c37_instit = ".db_getsession("DB_instit");
    $sWhere                  .= " and conlancaminscricaopassivo.c37_inscricaopassivo = {$oParam->iInscricao}";
    $sWhere                  .= " and c70_anousu =".db_getsession("DB_anousu");

    $sSqlLancamentoInscricao  = $oDAOInscricaoPassivo->sql_lancamento_inscricao(null, $sCampos, null, $sWhere);
    $rsLancamentoInscricao   = $oDAOInscricaoPassivo->sql_record($sSqlLancamentoInscricao);

    if ($oDAOInscricaoPassivo->numrows != 1) {
        throw new Exception("Não foi possível localizar a conta vinculada à inscrição");
    }

    $oDadosLancamentoInscricao = db_utils::fieldsMemory($rsLancamentoInscricao, 0);
    $oConta                    = new stdClass();
    $oConta->reduzido          = $oDadosLancamentoInscricao->conta_credito_reduzido;
    $oConta->descricao         = $oDadosLancamentoInscricao->descricao;
    $oRetorno->iFavorecido     = $oDadosLancamentoInscricao->cgm_favorecido;
    $oRetorno->aContas         = array();
    $oRetorno->aContas[]       = $oConta;

    /**
     * Busca os dados do CGM favorecido/concessor
     */
    $oCgm                  = CgmFactory::getInstanceByCgm($oRetorno->iFavorecido);
    $oRetorno->sFavorecido = urlencode($oCgm->getNome());

    /**
     * Resgata o valor total da inscrição
     */
    $oInscricaoPassivo              = new InscricaoPassivoOrcamento($oParam->iInscricao);
    $aItensInscricao                = $oInscricaoPassivo->getItens();
    $nTotalInscricao                = $oInscricaoPassivo->getValorTotalInscricao();
    $oRetorno->nValorTotalInscricao = $nTotalInscricao;

    if (method_exists($oCgm, "getCnpj")) {
        $oRetorno->sCNPJ                       = db_formatar($oCgm->getCnpj(), "cnpj");
    } else {
        $oRetorno->sCNPJ                       = db_formatar($oCgm->getCpf(), "cpf");
    }
    break;

case "getDadosInscricao":

    $oInscricao      = new InscricaoPassivoOrcamento($oParam->iCodigoInscricao);

    $oRetorno->nValorTotalInscricao = $oInscricao->getValorTotalInscricao();;

    $oRetorno->iCgmFavorecido       = $oInscricao->getFavorecido()->getCodigo();
    $oRetorno->sNomeFavorecido      = urlencode($oInscricao->getFavorecido()->getNomeCompleto());

    $oDAOInscricaoPassivo     = new cl_inscricaopassivo;
    $sCampos                  = "c69_credito as conta_debito, ";
    $sCampos                 .= "c36_cgm     ,  ";
    $sCampos                 .= "c60_descr    ";
    $sWhere                   = "     conlancaminscricaopassivo.c37_instit = ".db_getsession("DB_instit");
    $sWhere                  .= " and conlancaminscricaopassivo.c37_inscricaopassivo = {$oParam->iCodigoInscricao}";
    $sWhere                  .= " and c70_anousu =".db_getsession("DB_anousu");
    $sSqlLancamentoInscricao  = $oDAOInscricaoPassivo->sql_lancamento_inscricao(null, $sCampos, null, $sWhere);
    $rsLancamentoInscricao    = $oDAOInscricaoPassivo->sql_record($sSqlLancamentoInscricao);
    $sDescricaoContaDebito    = '';
    $iContaContaDebito        = '';
    if ($rsLancamentoInscricao && $oDAOInscricaoPassivo->numrows > 0) {

        $oLancamentoInscricao   = db_utils::fieldsMemory($rsLancamentoInscricao, 0);
        $sDescricaoContaDebito  = urlencode($oLancamentoInscricao->c60_descr);
        $iContaContaDebito      = $oLancamentoInscricao->conta_debito;
    }

    $oRetorno->iContaDebito         = $iContaContaDebito;
    $oRetorno->sDescrContaDebito    = $sDescricaoContaDebito;

    break;

case "getAutenticacoesSlip":

    $sWhere  = "       k12_codigo = {$oParam->iCodigoSlip}";
    $sWhere .= " order by corlanc.k12_data,";
    $sWhere .= "          corrente.k12_autent";

    $sCampos  = "corrente.k12_id,";
    $sCampos .= "corrente.k12_data,";
    $sCampos .= "corrente.k12_hora,";
    $sCampos .= "corrente.k12_autent,";
    $sCampos .= "corrente.k12_valor,";
    $sCampos .= "e91_cheque,";
    $sCampos .= "coalesce(e96_descr, (select e96_descr
                                        from empageslip
                                             inner join empagemovforma on e89_codmov = e97_codmov
                                             inner join empageforma on e97_codforma = e96_codigo
                                       where e89_codigo = k12_codigo)) as descricao,";
    $sCampos .= "(select coalesce(c86_conlancam, 0)
                    from conlancamcorrente
                     inner join conlancaminstit on c02_codlan = c86_conlancam
                   where c86_id     = corrente.k12_id
                     and c86_data   = corrente.k12_data
                     and c86_autent = corrente.k12_autent
                     and c02_instit = {$iInstituicaoSessao}) as codigo_lancamento";

    $oDaoCorLanc = new cl_corlanc;
    $sSqlBuscaAutenticacao = $oDaoCorLanc->sql_query_slip(null, null, null, $sCampos, null, $sWhere);
    $rsBuscaAutenticacao   = $oDaoCorLanc->sql_record($sSqlBuscaAutenticacao);
    $aDadosRetorno         = array();
    if ($oDaoCorLanc->numrows > 0) {
        $aDadosRetorno = db_utils::getCollectionByRecord($rsBuscaAutenticacao);
    }

    $oRetorno->aAutenticacoes = $aDadosRetorno;
    break;

case "getFinalidadePagamentoTransferencia":

    $oTransferencia       = TransferenciaFactory::getInstance(null, $oParam->iCodigoSlip);
    $oFinalidadePagamento = $oTransferencia->getFinalidadePagamentoFundebCredito();

    $oRetorno->lPossuiFinalidadePagamento = false;
    if (!empty($oFinalidadePagamento)) {

        $oRetorno->oFinalidadePagamentoFundeb                 = new stdClass();
        $oRetorno->oFinalidadePagamentoFundeb->e151_codigo    = $oFinalidadePagamento->getCodigo();
        $oRetorno->oFinalidadePagamentoFundeb->e151_descricao = urlencode($oFinalidadePagamento->getDescricao());
        $oRetorno->lPossuiFinalidadePagamento = true;
    }

    break;


case "buscaContaPagadoraPadrao":

    try {

        $daoParametro = new cl_caiparametro();
        $buscaContas = $daoParametro->sql_query_file($iInstituicaoSessao, "k29_contapadraoslip as conta_padrao_slip");
        $buscaContas = db_query($buscaContas);
        if (!$buscaContas) {
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            throw new DBException("Ocorreu um erro ao consultar as conta padrão do slip para a instituição {$instituicao->getDescricaoAbreviada()}.");
        }

        $oRetorno->conta_sugerida = '';
        if (pg_num_rows($buscaContas) > 0) {
            $oRetorno->conta_sugerida = db_utils::fieldsMemory($buscaContas, 0)->conta_padrao_slip;
        }

    } catch (Exception $e) {

        $oRetorno->message = $e->getMessage();
        $oRetorno->erro    = true;
    }

    break;
}
echo $oJson->encode($oRetorno);

/**
 * Retorna o código do documento para executar na inclusão de um slip
 *
 * @param  integer $iTipoOperacao
 * @return integer
 */
function getDocumentoPorTipoInclusao($iTipoOperacao)
{

    $iCodigoDocumento = 0;
    switch ($iTipoOperacao) {

    /**
     * Transferencia Financeira
     */
    case 1:
    case 2:
        $iCodigoDocumento = 120;
        break;
    case 3:
    case 4:
        $iCodigoDocumento = 130;
        break;

        /**
         * Transferencia Bancaria
         */
    case 5:
    case 6:
        $iCodigoDocumento = 140;
        break;

    /**
     * Caução
     */
    case 7:
    case 8:
        $iCodigoDocumento = 150;
        break;
    case 9:
    case 10:
        $iCodigoDocumento = 151;
        break;

      /**
       * Depósito de Diversas Origens
       */
    case 11:
    case 12:
        $iCodigoDocumento = 160;
        break;

    case 13:
    case 14:
        $iCodigoDocumento = 161;
        break;
    }

    return $iCodigoDocumento;
}
