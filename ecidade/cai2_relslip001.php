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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orctiporec_classe.php"));

db_postmemory($_POST);
db_postmemory($_GET);

$clorctiporec = new cl_orctiporec;
$cliframe_seleciona = new cl_iframe_seleciona;
$clrotulo = new rotulocampo;
$clrotulo->label('c60_codcon');
$clrotulo->label('c60_descr');
$clrotulo->label("k17_codigo");
$clrotulo->label("c50_descr");
$clrotulo->label("k17_hist");

$db_opcao = 1;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript">

        function js_emite() {
            var var_obj = document.getElementById('cgm').length;
            var cods = "";
            var vir = "";
            var selinstit = document.form1.db_selinstit.value;
            var sel_instit = new Number(selinstit);

            if (sel_instit == 0) {
                alert('Você não escolheu nenhuma Instituição. Verifique!');
                return false;
            }

            for (y = 0; y < var_obj; y++) {
                var_if = parseInt(document.getElementById('cgm').options[y].value)
                cods += vir + var_if;
                vir = ",";
            }

            if (document.getElementById("data").value != '' && document.getElementById("data1").value != '') {

                var oDataInicial = new Date(document.form1.data_ano.value, document.form1.data_mes.value, document.form1.data_dia.value),
                    oDataFinal = new Date(document.form1.data1_ano.value, document.form1.data1_mes.value, document.form1.data1_dia.value);

                if (oDataInicial.getTime() > oDataFinal.getTime()) {
                    return alert("A data inicial deve ser menor ou igual a data final.");
                }
            }

            qry = 'codigos=' + cods;
            qry += '&situac=' + document.form1.situacao.value;
            qry += '&data=' + document.form1.data_ano.value + '-' + document.form1.data_mes.value + '-' + document.form1.data_dia.value;
            qry += '&data1=' + document.form1.data1_ano.value + '-' + document.form1.data1_mes.value + '-' + document.form1.data1_dia.value;
            qry += '&parametro=' + document.form1.parametro.value;
            qry += '&slip1=' + document.form1.k17_codigo.value;
            qry += '&recurso=' + document.form1.o15_recurso.value;
            qry += '&slip2=' + document.form1.k17_codigo02.value;
            qry += '&hist=' + document.form1.k17_hist.value;
            qry += '&k145_numeroprocesso=' + document.form1.k145_numeroprocesso.value;
            qry += '&db_selinstit=' + selinstit;
            qry += "&folha=<?php echo(isset($modulo) && $modulo == 'pessoal' ? 't' : 'f'); ?>";
            qry += '&contadebito='+document.form1.oTxtContaDebitoCodigo.value;
            qry += '&contacredito='+document.form1.oTxtContaCreditoCodigo.value;

            jan = window.open('cai2_relslip002.php?' + qry,
                '',
                'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            jan.moveTo(0, 0);
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">
    <form name="form1">
        <fieldset>
            <legend>Relatório >> Slip</legend>
            <table>
                <tr>
                    <td nowrap>
                        <?php db_ancora(@$Lk17_codigo, "js_pesquisak17_codigo(true);", $db_opcao); ?>
                    </td>
                    <td>
                        <?php db_input('k17_codigo', 10, $Ik17_codigo, true, 'text', $db_opcao, " onchange='js_pesquisak17_codigo(false);'") ?>
                    </td>
                    <td>
                        <?php db_ancora("<b>Até</b>", "js_pesquisak17_codigo02(true);", $db_opcao); ?>
                    </td>
                    <td>
                        <?php db_input('k17_codigo', 10, $Ik17_codigo, true, 'text', $db_opcao, " onchange='js_pesquisak17_codigo02(false);'", "k17_codigo02") ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <b>De:</b>
                    </td>
                    <td>
                        <?php db_inputdata("data", "", "", "", "true", "text", 2); ?>
                    </td>
                    <td>
                        <b>Até:</b>
                    </td>
                    <td>
                        <?php db_inputdata("data1", "", "", "", "true", "text", 2); ?>
                    </td>
                </tr>

                <tr>
                    <td><?php db_ancora(@$Lk17_hist, "js_pesquisac50_codhist(true);", $db_opcao); ?></td>
                    <td colspan="4">
                        <?php
                        db_input('k17_hist', 10, $Ik17_hist, true, 'text', $db_opcao, " onchange='js_pesquisac50_codhist(false);'");
                        db_input('c50_descr', 30, $Ic50_descr, true, 'text', 3);
                        ?>
                    </td>
                <tr>

                <tr>
                    <td>
                    <?php db_ancora("Conta Débito:", "pesquisaContaSaltes(true, false);", $db_opcao); ?>
                    </td>
                    <td colspan="3">
                      <input id="oTxtContaDebitoCodigo" size="10" type="text" name="oTxtContaDebitoCodigo" value="" onchange="pesquisaContaSaltes(false, false);"/>
                      <input id="oTxtContaDebitoDescricao" class="readonly" type="text" size="30" name="oTxtContaDebitoDescricao" value="" />
                    </td>
                </tr>

                <tr>
                    <td>
                      <?php db_ancora("Conta Crédito:", "pesquisaContaSaltes(true, true);", $db_opcao); ?>
                    </td>
                    <td colspan="3">
                      <input id="oTxtContaCreditoCodigo" size="10" type="text" name="oTxtContaCreditoCodigo" value="" onchange="pesquisaContaSaltes(false, true);"/>
                      <input id="oTxtContaCreditoDescricao" class="readonly" type="text" size="30" name="oTxtContaCreditoDescricao" value="" />
                    </td>
                </tr>

                <tr>
                    <td><strong>Recurso:</strong></td>
                    <td colspan='4'>
                        <?php
                        $dbwhere = " o15_datalimite is null or o15_datalimite > '" . date('Y-m-d', db_getsession('DB_datausu')) . "'";
                        $rs = $clorctiporec->sql_record($clorctiporec->sql_query(null, "distinct o15_recurso, o15_descr", "o15_recurso", $dbwhere));
                        db_selectrecord("o15_recurso", $rs, false, 1, "", "", "", "0");
                        ?>
                    </td>
                </tr>                

                <tr>
                    <td><strong>Situação:</strong></td>
                    <td colspan=3>
                        <?php
                        $tipo_ordem = array("A" => "Todas", "1" => "Não Autenticado", "2" => "Autenticado", "3" => "Estornado", "4" => "Cancelado");
                        db_select("situacao", $tipo_ordem, true, 2); ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap="nowrap">
                        <strong>Processo Administrativo:</strong>
                    </td>
                    <td colspan="3">
                        <?php db_input('k145_numeroprocesso', 10, null, true, 'text', 1, null, null, null, null, 15); ?>
                    </td>
                </tr>

                <tr>
                    <td colspan=4 style="text-align: center">
                        <?php db_selinstit('', 425, 100); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <table style="width: 100%">
                            <tr>
                                <td style="text-align: center">
                                    <?php
                                    $aux = new cl_arquivo_auxiliar;
                                    $aux->cabecalho = "<strong>CGM</strong>";
                                    $aux->codigo = "z01_numcgm";
                                    $aux->descr = "z01_nome";
                                    $aux->isfuncnome = true;
                                    $aux->nomeobjeto = 'cgm';
                                    $aux->funcao_js = 'js_mostra';
                                    $aux->funcao_js_hide = 'js_mostra1';
                                    $aux->sql_exec = "";
                                    $aux->func_arquivo = "func_nome.php";
                                    $aux->nomeiframe = "func_nome";
                                    $aux->localjan = "";
                                    $aux->db_opcao = 2;
                                    $aux->tipo = 2;
                                    $aux->top = 0;
                                    $aux->linhas = 6;
                                    $aux->vwidth = 400;
                                    $aux->funcao_gera_formulario();
                                    ?>
                                <td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><strong>Opção de Seleção :<strong></td>
                    <td colspan="3">
                        <?php
                        $xxx = array("S" => "Somente Selecionados", "N" => "Menos os Selecionados");
                        db_select('parametro', $xxx, true, 2);
                        ?>
                        <script>
                            document.getElementById('parametro').style.width = '100%';
                        </script>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="emite2" id="emite2" type="button" value="Emitir Relatório" onclick="js_emite();">
    </form>
</div>
<?php db_menu(); ?>
</body>
</html>
<script>

document.getElementById('o15_recurso').style.width = '50';
document.getElementById('o15_recursodescr').style.width = '250';
document.getElementById('situacao').style.width = '305';
document.getElementById('parametro').style.width = '305';

    //-----------------------------------------------------------
    //---slip 01
    function js_pesquisak17_codigo(mostra) {

        sFuncao = 'func_slip.php?';
        <?php
        if (isset($modulo) and $modulo == 'pessoal') {
            ?>
        sFuncao += 'modulo=pessoal&';
            <?php
        }
        ?>

        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', sFuncao + 'funcao_js=parent.js_mostraslip1|k17_codigo', 'Pesquisa', true);
        } else {
            slip01 = new Number(document.form1.k17_codigo.value);
            if (slip01 != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', sFuncao + 'pesquisa_chave=' + slip01 + '&funcao_js=parent.js_mostraslip', 'Pesquisa', false);
            } else {
                document.form1.k17_codigo.value = '';
            }
        }
    }

    function js_mostraslip(chave, erro) {
        if (erro == true) {
            document.form1.k17_codigo.focus();
            document.form1.k17_codigo.value = '';
        }
    }

    function js_mostraslip1(chave1, chave2) {
        document.form1.k17_codigo.value = chave1;
        db_iframe_slip.hide();
    }

    //-----------------------------------------------------------
    //---slip 02
    function js_pesquisak17_codigo02(mostra) {

        sFuncao = 'func_slip.php?';
        <?php
        if (isset($modulo) and $modulo == 'pessoal') {
            ?>
        sFuncao += 'modulo=pessoal&';
            <?php
        }
        ?>

        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', sFuncao + 'funcao_js=parent.js_mostraslip12|k17_codigo', 'Pesquisa', true);
        } else {
            slip01 = new Number(document.form1.k17_codigo02.value);
            if (slip01 != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', sFuncao + 'pesquisa_chave=' + slip01 + '&funcao_js=parent.js_mostraslip2', 'Pesquisa', false);
            } else {
                document.form1.k17_codigo02.value = '';
            }
        }
    }

    function js_mostraslip2(chave, erro) {
        if (erro == true) {
            document.form1.k17_codigo02.focus();
            document.form1.k17_codigo02.value = '';
        }
    }

    function js_mostraslip12(chave1, chave2) {
        document.form1.k17_codigo02.value = chave1;
        db_iframe_slip.hide();
    }

    function js_pesquisac50_codhist(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_conhist.php?funcao_js=parent.js_mostrahist1|c50_codhist|c50_descr', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_conhist.php?pesquisa_chave=' + document.form1.k17_hist.value + '&funcao_js=parent.js_mostrahist', 'Pesquisa', false);
        }
    }

    function js_mostrahist(chave, erro) {
        document.form1.c50_descr.value = chave;
        if (erro == true) {
            document.form1.k17_hist.focus();
            document.form1.k17_hist.value = '';
        }
    }

    function js_mostrahist1(chave1, chave2) {
        document.form1.k17_hist.value = chave1;
        document.form1.c50_descr.value = chave2;
        db_iframe.hide();
    }

  function pesquisaContaSaltes (lMostra, lCredito) {

    /**
     * Controla que função chamar para completar os campos
     * e também para pegar o valor do input certo
     */
    var sFunctionCompleta = "Debito";
    var sIframe           = "debito";
    if (lCredito) {

      sFunctionCompleta = "Credito";
      sIframe           = "credito";
    }

    var oTxtConta       = $('oTxtConta'+sFunctionCompleta+'Codigo');

    var sUrlSaltes = "func_saltesreduz.php?ver_datalimite=1&pesquisa_chave="+oTxtConta.value+"&funcao_js=parent.preenche"+sFunctionCompleta;
    if (lMostra) {
      sUrlSaltes = "func_saltesreduz.php?ver_datalimite=1&funcao_js=parent.completa"+sFunctionCompleta+"|k13_reduz|k13_descr";
    }

    js_OpenJanelaIframe("", 'db_iframe_'+sIframe, sUrlSaltes, "Pesquisa Contas", lMostra);
  }

  function completaDebito(iReduzido, sDescricao) {

   $('oTxtContaDebitoCodigo').value = iReduzido;
   $('oTxtContaDebitoDescricao').value = sDescricao;
   db_iframe_debito.hide();
  }

  function preencheDebito(sDescricao, lErro) {

    $('oTxtContaDebitoDescricao').value = sDescricao;
    if (lErro) {
      $('oTxtContaDebitoCodigo').value = "";
    }

  }

  function completaCredito(iReduzido, sDescricao) {

   $('oTxtContaCreditoCodigo').value = iReduzido;
   $('oTxtContaCreditoDescricao').value = sDescricao;
   db_iframe_credito.hide();
  }

  function preencheCredito(sDescricao, lErro) {

    $('oTxtContaCreditoDescricao').value = sDescricao;
    if (lErro) {
      $('oTxtContaCreditoCodigo').value = "";
    }
  }
</script>
