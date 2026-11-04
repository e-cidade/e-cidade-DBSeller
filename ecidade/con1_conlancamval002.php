<?PHP
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


require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_contacorrenteatributos.php"));
require_once(modification("classes/db_conlancamval_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_conlancam_classe.php"));
require_once(modification("classes/db_conlancamcompl_classe.php"));
require_once(modification("classes/db_conlancamdig_classe.php"));
require_once(modification("classes/db_conlancamdoc_classe.php"));
require_once(modification("classes/db_conplano_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$post = db_utils::postMemory($_POST);

$clconplano = new cl_conplano;
$clconlancamval = new cl_conlancamval;
$clconlancamcompl = new cl_conlancamcompl;
$clconlancamdig = new cl_conlancamdig;
$clconlancam = new cl_conlancam;
$clconlancamdoc = new cl_conlancamdoc;

$anousu = db_getsession("DB_anousu");
$db_opcao = 22;
$db_botao = false;
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "Alterar") {


    $erro = false;
    $data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
    if (strlen($data) < 9) {
        echo "<script> alert('Data inválida ! '); </script>";
    }
    if ($c69_debito == "" || $c69_debito == "0") {
        echo "<script> alert('Conta Débito não informada ! '); </script>";
        $db_opcao = 2;
        $db_botao = true;
    } else {
        if ($c69_credito == "" || $c69_credito == "0") {
            echo "<script> alert('Conta Credito não informada !  '); </script>";
        } else {
            if ($c69_credito == $c69_debito) {
                echo "<script> alert('Contas não podem ser iguais !  '); </script>";
                $db_opcao = 2;
                $db_botao = true;
            } else {
                if ($c69_valor == "" || $c69_valor == "0") {
                    echo "<script> alert('Valor não informado ! '); </script>";
                    $db_opcao = 2;
                    $db_botao = true;
                } else {
                    if ($c69_codhist == "" || $c69_codhist == "0") {
                        echo "<script> alert('Histórico não informado !  '); </script>";
                        $db_opcao = 2;
                        $db_botao = true;
                    } else {
                        db_inicio_transacao();

                        /*
                         * - estornar lancamento
                         * - incluir novo lancamento
                         * - gravar log de lancamentos
                         *
                         */

                        try {
                            $options = array('ignorar_conta_corrente' => true);
                            /* estorno lançamento original */
                            $campos = 'conlancamval.*, c72_complem, c53_coddoc';
                            $where = "c69_codlan = {$post->c70_codlan}";
                            $daoConlancam = new cl_conlancam();
                            $buscaLancamento = $daoConlancam->sql_query_nota_lancamento($campos, $where);
                            $resBuscaLancamento = db_query($buscaLancamento);
                            if (!$resBuscaLancamento || pg_num_rows($resBuscaLancamento) == 0) {
                                throw new Exception('Não foi possível consultar o lançamento de origem.');
                            }

                            $stdDadosInclusao = db_utils::fieldsMemory($resBuscaLancamento, 0);

                            $dataLancamentoOriginal = new DBDate($stdDadosInclusao->c69_data);
                            $dataSessao = new DBDate(date('Y-m-d', db_getsession('DB_datausu')));
                            if ($dataSessao->getTimeStamp() < $dataLancamentoOriginal->getTimeStamp()) {
                                $mensagem = "A data do lançamento alterado não pode ser superior a data atual.";
                                $mensagem .= "Data do Lançamento: {$dataLancamentoOriginal->getDate(DBDate::DATA_PTBR)}\n";
                                $mensagem .= "Data Atual: {$dataSessao->getDate(DBDate::DATA_PTBR)}";
                                throw new Exception($mensagem);
                            }

                            $lancamentoAuxiliarEstorno = new LancamentoAuxiliarRetificacao();
                            $lancamentoAuxiliarEstorno->setContaCredito($stdDadosInclusao->c69_debito);
                            $lancamentoAuxiliarEstorno->setContaDebito($stdDadosInclusao->c69_credito);
                            $lancamentoAuxiliarEstorno->setObservacaoHistorico($stdDadosInclusao->c72_complem);
                            $lancamentoAuxiliarEstorno->setValorTotal($stdDadosInclusao->c69_valor);
                            $lancamentoAuxiliarEstorno->setHistorico($stdDadosInclusao->c69_codhist);
                            $eventoContabil = new EventoContabil($stdDadosInclusao->c53_coddoc, $anousu);
                            $eventoContabilEstorno = $eventoContabil->getEventoInverso();
                            if (!$eventoContabilEstorno) {
                                $msg = "Documento de estorno código {$eventoContabil->getDocumentoInverso()} não cadastrado.\n\nVerifique o cadastro de transações.";
                                throw new Exception($msg);
                            }
                            $codigoLancamentoEstorno = $eventoContabilEstorno->executaLancamento($lancamentoAuxiliarEstorno, null, $options);
                            $stdDadosInclusao->c69_codlan = $codigoLancamentoEstorno;
                            $stdDadosInclusao->c69_debito = $lancamentoAuxiliarEstorno->getContaCredito();
                            $stdDadosInclusao->c69_credito = $lancamentoAuxiliarEstorno->getContaDebito();
                            $atributosInclusao = DBContaCorrenteAtributos::getAtributosLancamento($post->c70_codlan, $anousu);
                            foreach ($atributosInclusao as $indice => $atributo) {
                                if (!empty($atributo)) {
                                    $sinalSalvar = $atributo->sinal === 'D' ? 'C' : 'D';
                                    $atributoDebito = $atributo->sinal === 'D';
                                    $recurso = null;
                                    foreach ($atributo->conta_corrente as $dadosContaCorrente) {
                                        if (empty($dadosContaCorrente)) {
                                            continue;
                                        }
                                        $atributosIndexados = array();
                                        foreach ($dadosContaCorrente->atributos as $dadosAtributos) {
                                            $atributosIndexados[$dadosAtributos->sigla] = $dadosAtributos->valor;
                                            if ($dadosAtributos->sigla === "FR") {
                                                $recurso = $dadosAtributos->valor;
                                            }
                                        }

                                        DBContaCorrenteAtributos::salvarAtributos(
                                            $stdDadosInclusao,
                                            $dadosContaCorrente->codigo,
                                            $atributosIndexados,
                                            $atributoDebito,
                                            $sinalSalvar
                                        );
                                    }

                                    DBContaCorrenteAtributos::salvarRecursoLancamento(
                                        $stdDadosInclusao,
                                        $recurso,
                                        $atributoDebito,
                                        $sinalSalvar
                                    );
                                }
                            }

                            /* novo lancamento */
                            $lancamentoAuxiliar = new LancamentoAuxiliarRetificacao();
                            $lancamentoAuxiliar->setContaCredito($post->c69_credito);
                            $lancamentoAuxiliar->setContaDebito($post->c69_debito);
                            $lancamentoAuxiliar->setObservacaoHistorico($post->c72_complem);
                            $lancamentoAuxiliar->setValorTotal($c69_valor);
                            $lancamentoAuxiliar->setHistorico($c69_codhist);
                            $eventoContabil = new EventoContabil($post->iDocumento, $anousu);
                            $codigoLancamentoNovo = $eventoContabil->executaLancamento($lancamentoAuxiliar, null, $options);

                            $daoConlancamval = new cl_conlancamval();
                            $buscaConlancamval = $daoConlancamval->sql_query_file(
                                null,
                                '*',
                                null,
                                "c69_codlan = {$codigoLancamentoNovo}"
                            );
                            $resBuscaConlancamval = db_query($buscaConlancamval);
                            $totalRegistros = pg_num_rows($resBuscaConlancamval);
                            if (!$resBuscaConlancamval || $totalRegistros === 0) {
                                throw new Exception('Ocorreu um erro ao consultar os valores do lançamentos. ->'.pg_last_error());
                            }
                            $stdDadosConlancamVal = db_utils::fieldsMemory($resBuscaConlancamval, 0);

                            $atributosDebito = JSON::create()->parse(
                                str_replace("\\", "", $_POST['atributosDebito'])
                            );
                            $atributosCredito = JSON::create()->parse(
                                str_replace("\\", "",$_POST['atributosCredito'])
                            );

                            $atributos = array($atributosDebito, $atributosCredito);
                            foreach ($atributos as $indice => $atributo) {
                                $atributoDebito = $atributo[0]->sinal === 'D';

                                if (!empty($atributo)) {
                                    $recurso = null;
                                    foreach ($atributo as $stdDadosAtributo) {
                                        foreach ($stdDadosAtributo->conta_corrente as $dadosContaCorrente) {
                                            if (empty($dadosContaCorrente)) {
                                                continue;
                                            }
                                            $atributosIndexados = array();
                                            foreach ($dadosContaCorrente->atributos as $dadosAtributos) {
                                                $atributosIndexados[$dadosAtributos->sigla] = $dadosAtributos->valor;
                                                if ($dadosAtributos->sigla === "DDR") {
                                                    $recurso = $dadosAtributos->valor;
                                                }
                                            }

                                            DBContaCorrenteAtributos::salvarAtributos(
                                                $stdDadosConlancamVal,
                                                $dadosContaCorrente->codigo,
                                                $atributosIndexados,
                                                $atributoDebito
                                            );
                                        }
                                    }

                                    DBContaCorrenteAtributos::salvarRecursoLancamento(
                                        $stdDadosConlancamVal,
                                        $recurso,
                                        $atributoDebito
                                    );
                                }
                            }


                            EventoContabil::vincularLancamentos(
                                $post->c70_codlan,
                                $codigoLancamentoEstorno,
                                $codigoLancamentoNovo
                            );

                            $alteracaoComplemento = array(
                                $post->c70_codlan => "Lançamento Retificado. Estorno do lançamento gerado no código de lançamento: {$codigoLancamentoEstorno}. Novo lançamento criado: {$codigoLancamentoNovo}.",
                                $codigoLancamentoEstorno => "Lançamento criado a partir da retificação do lançamento {$post->c70_codlan}. Novo lançamento criado: {$codigoLancamentoNovo}.",
                                $codigoLancamentoNovo => "Lançamento criado a partir da retificação do lançamento {$post->c70_codlan}. Lançamento de estorno criado: {$codigoLancamentoEstorno}."
                            );
                            foreach ($alteracaoComplemento as $codigo => $mensagem) {


                                $dao = new cl_conlancamcompl();
                                $buscaComplemento = $dao->sql_query_file($codigo);
                                $resBusca = db_query($buscaComplemento);
                                if (!$resBusca) {
                                    throw new Exception('Ocorreu um erro para consultar o complemento.');
                                }
                                $complemento = db_utils::fieldsMemory($resBusca, 0)->c72_complem;
                                $dao->c72_codlan = $codigo;
                                $dao->c72_complem = $complemento . " # NOTA DO SISTEMA: {$mensagem}";
                                $dao->alterar($codigo);
                                if ($dao->erro_status == '0') {
                                    throw new Exception('Ocorreu um erro para alterar o complemento dos lançamentos.');
                                }
                            }

                            $erro = false;
                            $erro_msg = 'Lançamento executado com sucesso.';
                        } catch (Exception $e) {
                            $erro_msg = $e->getMessage();
                            $erro = true;
                        }

                        db_fim_transacao($erro);
                    }
                }
            }
        }
    }
} else {
    if (isset($chavepesquisa)) {
        $db_opcao = 2;
        if (isset($sequen)) {
            $result = $clconlancamval->sql_record($clconlancamval->sql_query("", "*", "",
                "c69_codlan=$chavepesquisa and c69_sequen=$sequen"));
        } else {
            $result = $clconlancamval->sql_record($clconlancamval->sql_query($chavepesquisa));
        }
        db_fieldsmemory($result, 0);
        $db_botao = true;

        $result = $clconlancamdoc->sql_record($clconlancamdoc->sql_query($c69_codlan, '*'));
        if ($clconlancamdoc->numrows != 0) {
            db_fieldsmemory($result, 0);
        }

    }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/AtributosLancamento.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body >


<div class="container">
    <?PHP
    if (USE_PCASP) {
        require_once(modification("forms/db_frmconlancamval.php"));
    } else {
        require_once(modification("forms/db_frmconlancamval_old.php"));
    }
    ?>
</div>
<?PHP
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
    db_getsession("DB_instit"));
?>
</body>
</html>
<?PHP
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "Alterar") {
//  if($clconlancamval->erro_status=="0"){
//    $clconlancamval->erro(true,false);


    if ($erro == true) {
        db_msgbox($erro_msg);

        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled= false;</script>  ";
        if ($clconlancamval->erro_campo != "") {
            echo "<script> document.form1." . $clconlancamval->erro_campo
                . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clconlancamval->erro_campo . ".focus();</script>";
        };
    } else {
        $c70_codlan = $codigoLancamentoNovo;
        echo <<<SCRIPT
<script>

if (confirm('Deseja emitir o a nota de lançamento manual?')) {
    window.open("con2_notadelancamento002.php?lancamentos=$c70_codlan");
}
</script>
SCRIPT;
        $clconlancamval->erro(true, true);
    };
};
if ($db_opcao == 22 and $desabilitafunc == false) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
