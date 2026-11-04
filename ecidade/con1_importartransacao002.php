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
 * Carregamos as bibliotecas nescessárias
 */
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/contabilidade/EventoContabil.model.php"));
require_once(modification("model/contabilidade/EventoContabilLancamento.model.php"));
require_once(modification("model/contabilidade/RegraLancamentoContabil.model.php"));

/**
 * Verificamos se o arquivo é de extensão xml e enviamos o mesmo para o tmp.
 * Caso houver um problema em algum desses processos redirecionamos para a página anterior
 * e informamos o ocorrido.
 */
$post = db_utils::postMemory($_POST);
$instituicaoOriginal = db_getsession('DB_instit');
$instituicoesSelecionadas = explode(',', $post->instituicoes);

if ($_FILES['arquivoTransacoes']['type'] == 'text/xml') {
    $tipo = "xml";
} elseif ($_FILES['arquivoTransacoes']['type'] == 'text/csv') {
    $tipo = "csv";
} else {
    $sErrorMessage = "O arquivo enviado não é do formato correto. São permitidos arquivos dos formatos xml ou csv.
  Favor verificar o arquivo.";
    db_redireciona("con1_importartransacao001.php?lErro=true&sErrorMessage=" . $sErrorMessage);
}

if (!move_uploaded_file($_FILES['arquivoTransacoes']['tmp_name'], "/tmp/importacaotransacao{$_FILES['arquivoTransacoes']['name']}")) {

    $sErrorMessage = "Ocorreu um erro na importação do arquivo de implantação. Favor verificar o arquivo.";
    db_redireciona("con1_importartransacao001.php?lErro=true&sErrorMessage=" . $sErrorMessage);
}
/**
 * Verifica existência do documento passado na assinatuta na tabela "conhistdoc"
 *
 * @param integer $iCodigoDocumento
 *
 * @return boolean
 */
function existeDocumentoTransacao($iCodigoDocumento)
{

    $oDaoConhistdoc = new cl_conhistdoc;
    $sSqlBuscaDocumento = $oDaoConhistdoc->sql_query_file($iCodigoDocumento);
    $rsBuscaDocumento = $oDaoConhistdoc->sql_record($sSqlBuscaDocumento);
    if ($oDaoConhistdoc->numrows > 0) {
        return true;
    }
    return false;
}

/**
 * Verifica existência da transação para para o documento informado e o ano e intituição logadas
 *
 * @param integer $iCodigoDocumento
 *
 * @return boolean
 */
function existeTransacaoParaDocumento($iCodigoDocumento)
{

    $oDaoContrans = new cl_contrans;
    $iAnousu = db_getsession("DB_anousu");
    $iInstituicao = db_getsession("DB_instit");
    $sWhereBuscaTransacao = "     c45_coddoc = {$iCodigoDocumento} ";
    $sWhereBuscaTransacao .= " and c45_anousu = {$iAnousu} ";
    $sWhereBuscaTransacao .= " and c45_instit = {$iInstituicao} ";
    $sSqlBuscaTransacao = $oDaoContrans->sql_query_file(null, "*", null, $sWhereBuscaTransacao);
    $rsBuscaTransacao = $oDaoContrans->sql_record($sSqlBuscaTransacao);
    if ($oDaoContrans->numrows > 0) {
        return db_utils::fieldsMemory($rsBuscaTransacao, 0)->c45_seqtrans;
    }
    return false;
}

/**
 * Verifica existência de registro na conplano para o estrutural passado na asinatura.
 * Se o mesmo for 0 retorna como se houvesse pois isso indica que a conta não possui conta
 * de crédito/débito (alguns casos possuem apenas conta de credito ou débito)
 *
 * @param string $sEstrutural
 *
 * @return boolean
 */
function existeEstrutural($sEstrutural)
{

    if ($sEstrutural == "0") {
        return true;
    }
    $oDaoConplano = new cl_conplano;
    $sWhereBuscaEstrutural = " c60_estrut = '{$sEstrutural}' and c60_anousu = " . db_getsession("DB_anousu");
    $sSqlBuscaEstrutural = $oDaoConplano->sql_query_file(null, null, "*", null, $sWhereBuscaEstrutural);
    $rsBuscaEstrutural = $oDaoConplano->sql_record($sSqlBuscaEstrutural);
    if ($oDaoConplano->numrows > 0) {
        return true;
    }
    return false;
}

/**
 * Verifica existência de reduzido para o estrutural passado na assinatura.
 * Se o mesmo for 0 retorna como se houvesse pois isso indica que a conta não possui conta
 * de crédito/débito (alguns casos possuem apenas conta de crédito ou débito)
 *
 * @param string $sEstrutural
 *
 * @return boolean
 */
function existeReduzido($sEstrutural)
{

    if ($sEstrutural == "0") {
        return true;
    }
    $oDaoConplanoReduz = new cl_conplanoreduz;
    $sWhereBuscaReduzido = "     conplano.c60_estrut = '{$sEstrutural}' ";
    $sWhereBuscaReduzido .= " and conplano.c60_anousu = " . db_getsession("DB_anousu");
    $sWhereBuscaReduzido .= " and conplanoreduz.c61_reduz is not null and c61_instit = " . db_getsession("DB_instit");
    $sSqlBuscaReduzido = $oDaoConplanoReduz->sql_query(null, null, "*", null, $sWhereBuscaReduzido);
    $rsSqlBuscaReduzido = $oDaoConplanoReduz->sql_record($sSqlBuscaReduzido);
    if ($oDaoConplanoReduz->numrows > 0) {
        return true;
    }
    return false;
}

/**
 * Buscamos o reduzido através do estrutural da conta
 *
 * @param string $sEstrutural
 */
function getReduzidoFromEstrutural($sEstrutural)
{

    if ($sEstrutural == "0") {
        return '0';
    }
    $oDaoConplanoReduz = new cl_conplanoreduz;
    $iAnoUsu = db_getsession("DB_anousu");
    $sWhereBuscaReduzido
        = " conplano.c60_estrut = '{$sEstrutural}' and conplanoreduz.c61_anousu = {$iAnoUsu} and c61_instit = "
        . db_getsession("DB_instit");
    $sSqlBuscaReduzido = $oDaoConplanoReduz->sql_query(null, null, " conplanoreduz.c61_reduz ",
        null, $sWhereBuscaReduzido);
    $rsBuscaReduzido = $oDaoConplanoReduz->sql_record($sSqlBuscaReduzido);
    if ($oDaoConplanoReduz->numrows > 0) {

        $oBuscaReduzido = db_utils::fieldsMemory($rsBuscaReduzido, 0);
        return $oBuscaReduzido->c61_reduz;
    }
    return null;
}

/**
 * Carregamos o arquivo de importação de transações na classe DOMDocument e após isso
 * percorremos o mesmo e montamos uma collection com as informações do mesmo
 */

try {

    db_inicio_transacao();

    $aLogErrosTransacoes = array();
    $aLogErros = array();

    $arquivoTransacoes = '/tmp/importacaotransacao' . $_FILES["arquivoTransacoes"]['name'];




    if ($tipo == 'xml') {

        $oDomXML = new DomDocument();
        $oDomXML->load($arquivoTransacoes);
        $aTransacoes = array();
        $oTransacoes = $oDomXML->getElementsByTagName('transacao');
        foreach ($oTransacoes as $oTransacao) {

            $oTransacaoAtual = new stdClass();
            $oTransacaoAtual->c45_seqtrans = $oTransacao->getAttribute('c45_seqtrans');
            $oTransacaoAtual->c45_anousu = $oTransacao->getAttribute('c45_anousu');
            $oTransacaoAtual->c45_coddoc = $oTransacao->getAttribute('c45_coddoc');
            $oTransacaoAtual->c53_descr = trim(utf8_decode($oTransacao->getAttribute('c53_descr')));
            $oTransacaoAtual->c45_instit = $oTransacao->getAttribute('c45_instit');

            $oTransacaoAtual->aLancamentos = array();
            foreach ($oTransacao->getElementsByTagName('lancamento') as $oLancamento) {

                $oLancamentoAtual = new stdClass();
                $oLancamentoAtual->c46_seqtranslan = $oLancamento->getAttribute('c46_seqtranslan');
                $oLancamentoAtual->c46_seqtrans = $oLancamento->getAttribute('c46_seqtrans');
                $oLancamentoAtual->c46_codhist = $oLancamento->getAttribute('c46_codhist');
                $oLancamentoAtual->c46_obs = trim(utf8_decode($oLancamento->getAttribute('c46_obs')));
                $oLancamentoAtual->c46_valor = trim(utf8_decode($oLancamento->getAttribute('c46_valor')));
                $oLancamentoAtual->c46_obrigatorio = $oLancamento->getAttribute('c46_obrigatorio');
                $oLancamentoAtual->c46_evento = $oLancamento->getAttribute('c46_evento');
                $oLancamentoAtual->c46_descricao = trim(utf8_decode($oLancamento->getAttribute('c46_descricao')));
                $oLancamentoAtual->c46_ordem = $oLancamento->getAttribute('c46_ordem');

                $oLancamentoAtual->aContas = array();
                foreach ($oLancamento->getElementsByTagName('conta') as $oConta) {

                    $oContaAtual = new stdClass();
                    $oContaAtual->c47_seqtranslr = $oConta->getAttribute('c47_seqtranslr');
                    $oContaAtual->c47_seqtranslan = $oConta->getAttribute('c47_seqtranslan');
                    $oContaAtual->c47_debito = trim(utf8_decode($oConta->getAttribute('c47_debito')));
                    $oContaAtual->c47_debito_descricao
                        = trim(utf8_decode($oConta->getAttribute('c47_debito_descricao')));
                    $oContaAtual->c47_credito = trim(utf8_decode($oConta->getAttribute('c47_credito')));
                    $oContaAtual->c47_credito_descricao
                        = trim(utf8_decode($oConta->getAttribute('c47_credito_descricao')));
                    $oContaAtual->c47_obs = trim(utf8_decode($oConta->getAttribute('c47_obs')));
                    $oContaAtual->c47_ref = trim(utf8_decode($oConta->getAttribute('c47_ref')));
                    $oContaAtual->c47_anousu = $oConta->getAttribute('c47_anousu');
                    $oContaAtual->c47_instit = $oConta->getAttribute('c47_instit');
                    $oContaAtual->c47_compara = trim(utf8_decode($oConta->getAttribute('c47_compara')));
                    $oContaAtual->c47_tiporesto = trim(utf8_decode($oConta->getAttribute('c47_tiporesto')));
                    $oContaAtual->c114_elemento = $oConta->getAttribute('c114_elemento');
                    $oLancamentoAtual->aContas[] = $oContaAtual;
                }
                $oTransacaoAtual->aLancamentos[] = $oLancamentoAtual;
            }
            $aTransacoes[] = $oTransacaoAtual;
        }

    } else {


        if (trim(file_get_contents($arquivoTransacoes)) == "") {
            throw new BusinessException("Não é possível importar arquivo vazio.");
        }

        $ano = db_getsession("DB_anousu");
        $instituicao = InstituicaoRepository::getInstituicaoSessao();

        $oArquivo = new SplFileObject($arquivoTransacoes);
        $oArquivo->setFlags(\SplFileObject::READ_CSV);

        $transacoesIncluidas = array();
        $oDaoDocumento = new cl_conhistdoc();
        $temDoc = false;
        $aDocumentosIncluidos = array();

        foreach ($instituicoesSelecionadas as $codigoInstituicao) {

            $instituicao = InstituicaoRepository::getInstituicaoByCodigo($codigoInstituicao);
            db_putsession('DB_instit', $codigoInstituicao);
            db_query(" select fc_putsession('db_instit', '{$codigoInstituicao}') ");
            while (!$oArquivo->eof()) {

                $variaveisDecodificar = $oArquivo->fgetcsv();

                if (empty($variaveisDecodificar[0]) || trim($variaveisDecodificar[0]) == "tipo") {
                    continue;
                }
                foreach ($variaveisDecodificar as $value) {
                    if (db_utils::isUTF8($value)) {
                        $value = utf8_decode($value);
                    }
                }

                list($tipo,
                    $documento,
                    $documento_estorno,
                    $descricao_documento,
                    $historico,
                    $ordem,
                    $descricao_lancamento,
                    $conta_debito,
                    $conta_credito,
                    $tipo_comparacao,
                    $valor_comparacao,
                    $sql)
                    = $variaveisDecodificar;

                $temDoc = existeDocumentoTransacao($documento);
                if (!$temDoc) {

                    $oDaoDocumento = new cl_conhistdoc();
                    $oDaoDocumento->c53_descr = $descricao_documento;
                    $oDaoDocumento->c53_tipo = $tipo;
                    $oDaoDocumento->incluir($documento);
                    if ($oDaoDocumento->erro_status == "0") {
                        $msg = "Ocorreu algo inexperado ao incluir documento para a transação: ";
                        $msg .= $oDaoDocumento->erro_msg;
                        throw new Exception($msg);
                    }

                    $oDaoVinculoDocumento = new cl_vinculoeventoscontabeis();
                    $oDaoVinculoDocumento->c115_conhistdocinclusao = $documento;
                    $oDaoVinculoDocumento->c115_conhistdocestorno = $documento_estorno;
                    $oDaoVinculoDocumento->incluirSemEstorno();

                    if ($oDaoVinculoDocumento->erro_status == "0") {
                        $msg = "Ocorreu algo inexperado ao incluir vinculo do documento de estorno para a transação: ";
                        $msg .= $oDaoVinculoDocumento->erro_msg ;
                        throw new Exception($msg);
                    }
                }

                if (!empty($conta_debito)) {
                    $contaDebito = ContaPlanoPCASPRepository::getContaPorEstrutural($conta_debito, $ano, $instituicao);
                    if (!$contaDebito) {
                        $oErro = new stdClass();
                        $oErro->c45_coddoc = $documento;
                        $oErro->c53_descr = $descricao_documento;
                        $oErro->c46_descricao = $descricao_lancamento;
                        $oErro->c46_ordem = $ordem;
                        $oErro->c47_seqtranslr = 0;
                        $oErro->c47_debito = $conta_debito;
                        $oErro->c47_credito = 0;
                        $oErro->sDescricao = "A conta de débito não foi encontrada no plano de contas.";
                        $aLogErros[] = $oErro;
                        $aContasContasInconsistentes[] = $conta_debito;
                        continue;
                    }
                }

                if (!empty($conta_credito)) {

                    $contaCredito = ContaPlanoPCASPRepository::getContaPorEstrutural($conta_credito, $ano,
                        $instituicao);
                    if (!$contaCredito) {
                        $oErro = new stdClass();
                        $oErro->c45_coddoc = $documento;
                        $oErro->c53_descr = $descricao_documento;
                        $oErro->c46_descricao = $descricao_lancamento;
                        $oErro->c46_ordem = $ordem;
                        $oErro->c47_seqtranslr = 0;
                        $oErro->c47_debito = 0;
                        $oErro->c47_credito = $conta_credito;
                        $oErro->sDescricao = "A conta de crédito não foi encontrada no plano de contas.";
                        $aLogErros[] = $oErro;
                        $aContasContasInconsistentes[] = $conta_credito;
                        continue;
                    }
                }

                if (!in_array($documento, $aDocumentosIncluidos)) {

                    $oTransacaoAtual = new stdClass();
                    $oTransacaoAtual->c45_anousu = $ano;
                    $oTransacaoAtual->c45_coddoc = $documento;
                    $oTransacaoAtual->c53_descr = $descricao_lancamento;
                    $oTransacaoAtual->c45_instit = $instituicao->getCodigo();
                    $oTransacaoAtual->aLancamentos = array();
                    $aTransacoes[] = $oTransacaoAtual;
                    $aDocumentosIncluidos[] = $documento;
                }

                $oLancamentoAtual = new stdClass();
                $oLancamentoAtual->c46_codhist = $historico;
                $oLancamentoAtual->c46_obs = "Transação incluida via importação";
                $oLancamentoAtual->c46_valor = 0;
                $oLancamentoAtual->c46_obrigatorio = true;
                $oLancamentoAtual->c46_evento = 0;
                $oLancamentoAtual->c46_descricao = $descricao_lancamento;
                $oLancamentoAtual->c46_ordem = $ordem;
                if (empty($oTransacaoAtual->aLancamentos[$ordem])) {
                    $oLancamentoAtual->aContas = array();
                    $oTransacaoAtual->aLancamentos[$ordem] = $oLancamentoAtual;
                }

                $oContaAtual = new stdClass();
                $oContaAtual->c47_debito = empty($conta_debito) ? "0" : $contaDebito->getEstrutural();
                $oContaAtual->c47_debito_descricao = empty($conta_debito) ? null : $contaDebito->getDescricao();
                $oContaAtual->c47_credito = empty($conta_credito) ? "0" : $contaCredito->getEstrutural();
                $oContaAtual->c47_credito_descricao = empty($conta_credito) ? null : $contaCredito->getDescricao();
                $oContaAtual->c47_obs = "";
                $valor_comparacao = trim($valor_comparacao);
                $oContaAtual->c47_ref = (empty($valor_comparacao) ? "0" : $valor_comparacao);
                $oContaAtual->c47_anousu = $ano;
                $oContaAtual->c47_instit = $instituicao->getCodigo();
                $tipo_comparacao = trim($tipo_comparacao);
                $oContaAtual->c47_compara = (empty($tipo_comparacao) ? "0" : $tipo_comparacao);
                $oContaAtual->c47_tiporesto = "0";
                $oContaAtual->c92_regra = (empty($sql) ? "" : $sql);
                $oTransacaoAtual->aLancamentos[$ordem]->aContas[] = $oContaAtual;
            }
        }
    }

    $stop = 1;

    /**
     * Iniciamos o pré-processamento para avaliar possíveis erros na importação de transações.
     * Todos os erros encontrados nesse processo são enviados para o array de erros
     */
    foreach ($instituicoesSelecionadas as $codigoInstituicao) {

        db_putsession('DB_instit', $codigoInstituicao);
        db_query(" select fc_putsession('db_instit', '{$codigoInstituicao}') ");
        $iIstituicaoDestino = db_getsession('DB_instit');
        $iAnoUsuDestino = db_getsession("DB_anousu");

        /**
         * Pré-processamento das transações
         */
        foreach ($aTransacoes as $oTransacao) {

            $codigoTransacao = existeTransacaoParaDocumento($oTransacao->c45_coddoc);
            if ($codigoTransacao) {

                $oEventoContabil = EventoContabilRepository::getPorCodigo($codigoTransacao);
                $lancamentos = $oEventoContabil->getEventoContabilLancamento();
                foreach ($lancamentos as $lancamento) {
                    $lancamento->excluir();
                }
                $oEventoContabil->excluir();
            }
        }

        /**
         * Se não houverem problemas no pré-processamento das transações passamos ao pré-processamento
         * dos lançamentos e das contas.
         */
        $aContasContasInconsistentes = array();
        foreach ($aTransacoes as $oTransacao) {

            foreach ($oTransacao->aLancamentos as $oLancamento) {

                foreach ($oLancamento->aContas as $oConta) {

                    /**
                     * Efetuamos as validações competentes às contas
                     */
                    if ($oConta->c47_debito != "0" && !existeEstrutural($oConta->c47_debito)) {
                        $aContasContasInconsistentes[] = $oConta->c47_debito;
                        $oErro = new stdClass();
                        $oErro->c45_coddoc = $oTransacao->c45_coddoc;
                        $oErro->c53_descr = $oTransacao->c53_descr;
                        $oErro->c46_descricao = $oLancamento->c46_descricao;
                        $oErro->c46_ordem = $oLancamento->c46_ordem;
                        $oErro->c47_seqtranslr = $oConta->c47_seqtranslr;
                        $oErro->c47_debito = $oConta->c47_debito;
                        $oErro->c47_credito = $oConta->c47_credito;
                        $oErro->sDescricao = "A conta de débito não existe na tabela 'conplano'.";
                        $aLogErros[] = $oErro;
                    } else {
                        if ($oConta->c47_debito != "0" && !existeReduzido($oConta->c47_debito)) {
                            $aContasContasInconsistentes[] = $oConta->c47_debito;
                            $oErro = new stdClass();
                            $oErro->c45_coddoc = $oTransacao->c45_coddoc;
                            $oErro->c53_descr = $oTransacao->c53_descr;
                            $oErro->c46_descricao = $oLancamento->c46_descricao;
                            $oErro->c46_ordem = $oLancamento->c46_ordem;
                            $oErro->c47_seqtranslr = $oConta->c47_seqtranslr;
                            $oErro->c47_debito = $oConta->c47_debito;
                            $oErro->c47_credito = $oConta->c47_credito;
                            $oErro->sDescricao = "A conta de débito não existe não possui reduzido.";
                            $aLogErros[] = $oErro;
                        }
                    }
                    if ($oConta->c47_credito != "0" && !existeEstrutural($oConta->c47_credito)) {
                        $aContasContasInconsistentes[] = $oConta->c47_credito;
                        $oErro = new stdClass();
                        $oErro->c45_coddoc = $oTransacao->c45_coddoc;
                        $oErro->c53_descr = $oTransacao->c53_descr;
                        $oErro->c46_descricao = $oLancamento->c46_descricao;
                        $oErro->c46_ordem = $oLancamento->c46_ordem;
                        $oErro->c47_seqtranslr = $oConta->c47_seqtranslr;
                        $oErro->c47_debito = $oConta->c47_debito;
                        $oErro->c47_credito = $oConta->c47_credito;
                        $oErro->sDescricao = "A conta de crédido não existe na tabela 'conplano'.";
                        $aLogErros[] = $oErro;
                    } else {
                        if ($oConta->c47_credito != "0" && !existeReduzido($oConta->c47_credito)) {
                            $aContasContasInconsistentes[] = $oConta->c47_credito;
                            $oErro = new stdClass();
                            $oErro->c45_coddoc = $oTransacao->c45_coddoc;
                            $oErro->c53_descr = $oTransacao->c53_descr;
                            $oErro->c46_descricao = $oLancamento->c46_descricao;
                            $oErro->c46_ordem = $oLancamento->c46_ordem;
                            $oErro->c47_seqtranslr = $oConta->c47_seqtranslr;
                            $oErro->c47_debito = $oConta->c47_debito;
                            $oErro->c47_credito = $oConta->c47_credito;
                            $oErro->sDescricao = "A conta de crédito não existe não possui reduzido.";
                            $aLogErros[] = $oErro;
                        }
                    }
                }
            }
        }


        /**
         * Aqui encerramos o pré-processamento e iniciamos a importação das inclusões
         */
        foreach ($aTransacoes as $oTransacao) {

            /**
             * Setamos as informações de transação
             */
            $oEventoContabil = new EventoContabil();
            $oEventoContabil->setCodigoDocumento($oTransacao->c45_coddoc);
            $oEventoContabil->setInstituicao(db_getsession("DB_instit"));
            $oEventoContabil->setAnoUso(db_getsession("DB_anousu"));
            $oEventoContabil->salvar();
            foreach ($oTransacao->aLancamentos as $oLancamento) {

                /**
                 * Setamos as informações de lançamento
                 */
                $oEventoContabilLancamento = new EventoContabilLancamento();
                $oEventoContabilLancamento->setHistorico($oLancamento->c46_codhist);
                $oEventoContabilLancamento->setObservacao($oLancamento->c46_obs);
                $oEventoContabilLancamento->setValor($oLancamento->c46_valor);
                $oEventoContabilLancamento->setObrigatorio($oLancamento->c46_obrigatorio);
                $oEventoContabilLancamento->setEvento($oLancamento->c46_evento);
                $oEventoContabilLancamento->setDescricao($oLancamento->c46_descricao);
                $oEventoContabilLancamento->setOrdem($oLancamento->c46_ordem);
                $oEventoContabilLancamento->setSequencialTransacao($oEventoContabil->getSequencialTransacao());
                $oEventoContabilLancamento->salvar();

                foreach ($oLancamento->aContas as $oConta) {

                    if (in_array($oConta->c47_debito, $aContasContasInconsistentes)) {
                        continue;
                    }
                    if (in_array($oConta->c47_credito, $aContasContasInconsistentes)) {
                        continue;
                    }

                    $oRegraLancamentoContabil = new RegraLancamentoContabil();
                    $oRegraLancamentoContabil->setSequencialLancamento($oEventoContabilLancamento->getSequencialLancamento());
                    $oRegraLancamentoContabil->setContaDebito(getReduzidoFromEstrutural($oConta->c47_debito));
                    $oRegraLancamentoContabil->setContaCredito(getReduzidoFromEstrutural($oConta->c47_credito));
                    $oRegraLancamentoContabil->setObservacao(str_replace("\\", "", $oConta->c47_obs));
                    $oRegraLancamentoContabil->setReferencia($oConta->c47_ref);
                    $oRegraLancamentoContabil->setAnoUso(db_getsession("DB_anousu"));
                    $oRegraLancamentoContabil->setInstituicao(db_getsession("DB_instit"));
                    $oRegraLancamentoContabil->setCompara($oConta->c47_compara);
                    $oRegraLancamentoContabil->setTipoResto($oConta->c47_tiporesto);

                    $oRegraLancamentoContabil->salvar();
                    if ($oConta->c114_elemento != "") {
                        $oRegraLancamentoContabil->vincularElemento($oConta->c114_elemento);
                    }

                    if (!empty($oConta->c92_regra)) {

                        $oDaoConshistdocregra = new cl_conhistdocregra();
                        $oDaoConshistdocregra->c92_conhistdoc = $oTransacao->c45_coddoc;
                        $oDaoConshistdocregra->c92_descricao = "Origem dos dados documento {$oTransacao->c45_coddoc}";
                        $oDaoConshistdocregra->c92_regra = pg_escape_string($oConta->c92_regra);
                        $oDaoConshistdocregra->c92_anousu = db_getsession("DB_anousu");
                        $oDaoConshistdocregra->incluir();
                        if ($oDaoConshistdocregra->erro_status == "0") {
                            $msg = "Ocorreu algo inexperado ao incluir regra para a transação: ";
                            $msg .= $oDaoConshistdocregra->erro_msg;
                            throw new Exception($msg);
                        }
                    }
                }
            }

            unset($oRegraLancamentoContabil);
            unset($oEventoContabilLancamento);
            unset($oEventoContabil);
        }
    }

    db_fim_transacao(false);

    db_putsession('DB_instit', $instituicaoOriginal);
    db_query(" select fc_putsession('db_instit', '{$instituicaoOriginal}') ");
    if (count($aLogErros) > 0) {
        $_SESSION['aConflitoLancamento'] = $aLogErros;
        $_SESSION['sMsgConflitoTransacaoLancamento']
            = "Houve conflito entre lançamentos. Confira no relatório que será emitido a seguir.";
        db_redireciona("con1_importartransacao001.php?lRelatorio=true&sUrlRelatorio=con2_lancamentosemconflito002.php");
    }


    $sMessage = "Importação das transações realizada com sucesso!";
    $_SESSION['sMsgConflitoTransacaoLancamento'] = $sMessage;
    db_redireciona('con1_importartransacao001.php?lErro=true');
} catch (Exception $eException) {

    db_putsession('DB_instit', $instituicaoOriginal);
    db_query(" select fc_putsession('db_instit', '{$instituicaoOriginal}') ");

    db_fim_transacao(true);

    db_msgbox($eException->getMessage());

    $_SESSION['sMsgConflitoTransacaoLancamento'] = $eException->getMessage();
    db_redireciona('con1_importartransacao001.php?lErro=true');
}
