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
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\InformacaoComplementar;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\Lancamento as LancamentoModel;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\Lancamento as LancamentoRepository;
use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepositoryAlias;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_contacorrenteatributos.php"));
require_once(modification("classes/db_conlancamval_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_conlancam_classe.php"));
require_once(modification("classes/db_conlancamcompl_classe.php"));
require_once(modification("classes/db_conlancamdig_classe.php"));
require_once(modification("classes/db_conlancamdoc_classe.php"));
require_once(modification("classes/db_conplano_classe.php"));

db_postmemory($HTTP_POST_VARS);

$clconplano = new cl_conplano;
$clconlancamval = new cl_conlancamval;
$clconlancamcompl = new cl_conlancamcompl;
$clconlancamdig = new cl_conlancamdig;
$clconlancamdoc = new cl_conlancamdoc;
$clconlancam = new cl_conlancam;

$db_opcao = 1;
$db_botao = true;
$anousu = db_getsession('DB_anousu');
$anoSessao = db_getsession('DB_anousu');
$instituicaoSessao = db_getsession('DB_instit');


if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {

    db_putsession("ldia", "$c70_data_dia");
    db_putsession("lmes", "$c70_data_mes");
    db_putsession("llote", "$c78_chave");

    try {

        if ($c69_debito == "" || $c69_debito == "0") {
            throw  new \Exception('Conta Débito não informada ! ');
        } else if ($c69_credito == "" || $c69_credito == "0") {
            throw  new \Exception('Conta Credito não informada !  ');
        } else if ($c69_credito == $c69_debito) {
            throw  new \Exception('Contas não podem ser iguais !  ');
        } else if ($c69_valor == "" || $c69_valor == "0") {
            throw  new \Exception('Valor não informado ! ');
        } else if ($c69_codhist == "" || $c69_codhist == "0") {
            throw  new \Exception('Histórico não informado !  ');
        } else {


            $daoConplanoatributos = new cl_conplanoatributos();
            $whereContas = "c61_reduz in({$c69_credito}, {$c69_debito}) and c61_anousu = {$anousu} and c120_conplanosistema in (10, 25)";
            $sqlVerificacao = $daoConplanoatributos->sql_query_reduzido("array_to_string(array_accum(distinct c120_conplanosistema||' - '||c122_descricao), ', ') as sistemas,  c61_reduz", $whereContas, "c61_reduz");

            $rsVerificacao = db_query($sqlVerificacao);
            if (pg_num_rows($rsVerificacao) > 0) {
                $contas = db_utils::getCollectionByRecord($rsVerificacao);
                $mensagemErro = 'Não é possível realizar o lançamento manual, pois foram encontrados as seguintes inconsistências:\n';
                foreach ($contas as $conta) {

                    if ($c69_credito == $conta->c61_reduz) {
                        $mensagemErro .= "A conta crédito está vinculada ao conta corrente: " . $conta->sistemas . '\n';
                    }
                    if ($c69_debito == $conta->c61_reduz) {
                        $mensagemErro .= "A conta débito está vinculada ao conta corrente: " . $conta->sistemas . ' \n';
                    }
                }
                throw  new \Exception($mensagemErro);
            }

            db_inicio_transacao();
            $clconlancam->c70_anousu = db_getsession('DB_anousu');
            $clconlancam->c70_data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
            $clconlancam->c70_valor = $c69_valor;
            $clconlancam->incluir("");
            $codlan = $clconlancam->c70_codlan; //pega o codigo gerado

            $erro = !EventoContabil::vincularLancamentoNaInstituicao($codlan, db_getsession('DB_instit'));
            if ($c78_chave != "") {
                $clconlancamdig->c78_chave = $c78_chave;
                $clconlancamdig->c78_data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
                $clconlancamdig->incluir($codlan);
            }
            if ($c72_complem != "") {
                $clconlancamcompl->c72_complem = $c72_complem;
                $clconlancamcompl->incluir($codlan);
            }

            if ($c71_coddoc == "2000") {
                $clconlancamdoc->c71_codlan = $codlan;
                $clconlancamdoc->c71_coddoc = '2000';
                $clconlancamdoc->c71_data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
                $clconlancamdoc->incluir($codlan);
            } else {
                if ($c71_coddoc == "1000") {
                    $clconlancamdoc->c71_codlan = $codlan;
                    $clconlancamdoc->c71_coddoc = '1000';
                    $clconlancamdoc->c71_data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
                    $clconlancamdoc->incluir($codlan);
                }
            }

            // ------- verifica se o sistema de contas esta correto, por exemplo
            // ------- não pode haver lan?emento entre contas Patrimonial X Financeiro
            $r = $clconplano->sql_record($clconplano->sql_query(null, null, "c52_descrred as sistema_debito", null,
                " c61_anousu=$anousu and c61_reduz=$c69_debito"));
            db_fieldsmemory($r, 0);
            $r = $clconplano->sql_record($clconplano->sql_query(null, null, "c52_descrred as sistema_credito", null,
                "c61_anousu=$anousu and c61_reduz=$c69_credito"));
            db_fieldsmemory($r, 0);
            if ($c71_coddoc == '1000' || $c71_coddoc == '2000') {
                // nda
            } elseif (!USE_PCASP && (($sistema_debito == 'F') && ($sistema_credito == 'P') || ($sistema_debito == 'P') && ($sistema_credito == 'F'))) {
                throw  new \Exception("Não é permitido lançamentos entre o sistema Financeiro e Patrimonial !");

            }
            // ------------------------ * ------------------- * --------------------------
            $clconlancamval->c69_anousu = $anousu;
            $clconlancamval->c69_codlan = $codlan;
            $clconlancamval->c69_codhist = $c69_codhist;
            $clconlancamval->c69_debito = $c69_debito;
            $clconlancamval->c69_credito = $c69_credito;
            $clconlancamval->c69_valor = $c69_valor;
            $clconlancamval->c69_data = "$c70_data_ano-$c70_data_mes-$c70_data_dia";
            $clconlancamval->incluir("");

            if (USE_PCASP) {

                $oDaoConLancamDoc = db_utils::getDao('conlancamdoc');
                $oDaoConLancamDoc->c71_codlan = $codlan;
                $oDaoConLancamDoc->c71_coddoc = $iDocumento;
                $oDaoConLancamDoc->c71_data = "{$c70_data_ano}-{$c70_data_mes}-{$c70_data_dia}";
                $oDaoConLancamDoc->incluir($codlan);
                if ($oDaoConLancamDoc->erro_status == "0") {
                    throw  new \Exception("Não foi possível vincular o documento ao lançamento. Procedimento abortado.");
                }
            }

            $atributosDebito = JSON::create()->parse(str_replace("\\", "", $_POST['atributosDebito']));
            $atributosCredito = JSON::create()->parse(str_replace("\\", "", $_POST['atributosCredito']));

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

                            $atributosDebitoIndexado = [];

                            foreach ($dadosContaCorrente->atributos as $dadosAtributos) {
                                $atributosDebitoIndexado[$dadosAtributos->sigla] = $dadosAtributos->valor;
                                if ($dadosAtributos->sigla === "DDR") {
                                    $recurso = $dadosAtributos->valor;
                                    $atributosDebitoIndexado[$dadosAtributos->sigla] =$dadosAtributos->valor;
                                }

                                if ($dadosAtributos->sigla === "RV") {
                                    $atributosDebitoIndexado[$dadosAtributos->sigla] = $dadosAtributos->valor;
                                }
                            }

                            DBContaCorrenteAtributos::salvarAtributos(
                                $clconlancamval,
                                $dadosContaCorrente->codigo,
                                $atributosDebitoIndexado,
                                $atributoDebito
                            );
                        }
                    }

                    if (!empty($recurso)) {
                        DBContaCorrenteAtributos::salvarRecursoLancamento(
                            $clconlancamval,
                            $recurso,
                            $atributoDebito
                        );
                    }
                }
            }
        }

        db_fim_transacao($erro);
    } catch (Exception $e) {
        $erro = true;
        db_msgbox($e->getMessage());
        db_fim_transacao(true);
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
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/AtributosLancamento.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body >

<div class="container">
    <?php
    require_once(modification("forms/db_frmconlancamval.php"));
    ?>
</div>
</body>
</html>
<?php
db_menu();

if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"]) == "Incluir") {
    if ($clconlancamval->erro_status == "0") {
        $clconlancamval->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clconlancamval->erro_campo != "") {
            echo "<script> document.form1." . $clconlancamval->erro_campo
                . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clconlancamval->erro_campo
                . ".focus();</script>";

        }
    } else {

        if (!$erro) {
            echo <<<SCRIPT
<script>

if (confirm('Deseja emitir o a nota de lançamento manual?')) {
    window.open("con2_notadelancamento002.php?lancamentos=$codlan");
}
</script>
SCRIPT;
        }
        $clconlancamval->erro(true, true);
    }
}


?>
