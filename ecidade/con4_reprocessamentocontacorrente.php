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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

?>
<html>
    <head>

        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="" quiv="Expires" CONTENT="0">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
        <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputCheckboxRadio.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>

        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <style>
        .ComboRazao {
            width: 220px;
        }
        #data1, #data2 {
            width: 70px;
        }
        label {
            font-weight: bold;
        }
    </style>
    <body bgcolor=#CCCCCC bgcolor="#CCCCCC" onload="">
    <center>
        <form name="form1" method="post" action="">
            <fieldset style="margin-top: 30px; width: 580px; text-align: left;">
                <legend>Reprocessamento de conta corrente</legend>
                <table style="text-align: left" border='0'>
                    <tr>
                        <td>
                            <label for="competencia"><b>Competência:</b></label>
                        </td>
                        <td>
                            <input id="competencia" />
                        </td>
                    </tr>
                    <tr >
                        <td>
                            <?php
                            db_ancora("<b>Conta Corrente:</b>", "js_pesquisaContaCorrente(true)", 1);
                            ?>
                        </td>
                        <td nowrap="nowrap">
                            <?php
                            db_input("iCodigoContaCorrente", 10, null, true, "text", 1, "onchange='js_pesquisaContaCorrente(false);'");
                            db_input("sDescricaoContaCorrente", 35, null, true, "text", 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id='ctnLancadorContas' style="margin-top: 10px; width: 600px;"> </div>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" value="Reprocessar" id="btnProcessar">
        </form>
    </center>
    </body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
    db_getsession("DB_instit")); ?>

<script>

    var competencia = new DBInput($('competencia'));
    competencia.inputElement.placeholder = '__/____';
    competencia.inputElement.size        = '7';
    competencia.inputElement.maxLength   = '7';
    var contaCorrente = $('iCodigoContaCorrente');
    competencia.inputElement.observe('blur', function(){

        var conteudo = $('competencia').value.replace(/\s+/g, '');

        var dataCampo = conteudo.split('/');

        var mes = dataCampo[0].trim();

        if (mes < 1 || mes > 12) {

            alert('Mês inválido.');
            $('competencia').value = '';
            return;
        }

    }.bind(competencia));

    new MaskedInput(competencia.inputElement, '99/9999', {placeholder: '_'});


    function js_pesquisaContaCorrente(lMostraWindow) {

        if (lMostraWindow) {
            var sUrl = 'func_conplanosistema.php?tipo=2&funcao_js=parent.js_preencheContaCorrente|c122_sequencial|c122_descricao';
            js_OpenJanelaIframe('','db_iframe_conplanosistema',sUrl,'Pesquisa de Conta Corrente',true,'0');
        } else {

            if ($("iCodigoContaCorrente").value != '') {
                var sUrl  = 'func_conplanosistema.php?tipo=2&pesquisa_chave='+$F("iCodigoContaCorrente");
                sUrl +='&funcao_js=parent.js_completaContaCorrente';
                js_OpenJanelaIframe('','db_iframe_conplanosistema',sUrl,'Pesquisa',false);
            } else {
                $("sDescricaoContaCorrente").value = '';
            }
        }
    }
    function js_preencheContaCorrente(iCodigoContaCorrente, sDescricaoContaCorrente) {

        $('iCodigoContaCorrente').value    = iCodigoContaCorrente;
        $('sDescricaoContaCorrente').value = sDescricaoContaCorrente;
        db_iframe_conplanosistema.hide();
    }
    function js_completaContaCorrente(sDescricaoContaCorrente, lErro) {

        if (!lErro) {
            $('sDescricaoContaCorrente').value = sDescricaoContaCorrente;
        } else {
            $('iCodigoContaCorrente').value    = '';
            $('sDescricaoContaCorrente').value = sDescricaoContaCorrente;
        }
    }
    /**
     * Cria o lançador para as contas
     */
    function js_criarLancadorContas()
    {
        oLancadorContas = new DBLancador("oLancadorContas");
        oLancadorContas.setNomeInstancia("oLancadorContas");
        oLancadorContas.setLabelAncora("Contas: ");
        oLancadorContas.setTextoFieldset("Contas Selecionadas");
        oLancadorContas.setParametrosPesquisa("func_conplanoexe.php", ['c62_reduz', 'c60_descr']);
        oLancadorContas.setGridHeight(200);
        oLancadorContas.setTituloJanela("Pesquisar Contas");
        oLancadorContas.show($("ctnLancadorContas"));
    }
    js_criarLancadorContas();

    $('btnProcessar').observe('click', function(){

        if (!confirm("Confirma o reprocessamento da conta corrente informada?")) {
            return false;
        }
        if (competencia.getValue() == '') {
            alert('O campo Competência deve ser informado.');
            return false;
        }
        if (contaCorrente.value == '') {
            alert('O campo Conta Corrente deve ser informado.');
            return false;
        }
        var contas = [];
        for (var conta of oLancadorContas.getRegistros()) {
            contas.push(conta.sCodigo);
        }
        var parametros = {
            exec: 'reprocessar',
            competencia: competencia.getValue(),
            conta_corrente: contaCorrente.value,
            contas: contas
        }

        new AjaxRequest('con4_reprocessamentocontacorrente.RPC.php', parametros, function (response, erro){

            alert(response.message);
            if (erro) {
                return false;
            }

        }).setMessage('Aguarde, reprocessando contas correntes...').execute();
    });
</script>