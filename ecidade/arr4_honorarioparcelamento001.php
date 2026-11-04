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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

$clrotulo = new rotulocampo;

$clrotulo->label("v50_inicial");
$clrotulo->label("v70_codforo");
$clrotulo->label("v70_sequencial");

db_app::load("prototype.js");

?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container">
    <form name="form1" method="post">

        <input type="hidden" name="sequencial" id="sequencial">

        <fieldset>
            <legend>Liberação Parcelamento</legend>

            <table class="form-container">
                <tr>
                    <td title="<?= @$Tv70_codforo ?>">
                        <?php
                        db_ancora(@$Lv70_codforo, "js_pesquisaprocessoforo(true);", 4);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("v70_sequencial", 4, $Iv70_sequencial, true, "text", 4, "onchange='js_pesquisaprocessoforo(false);'");
                        db_input("v70_codforo", 40, $Iv70_codforo, true, "text", 3, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 83px;">
                        <?php
                        db_ancora("Inicial do Foro:", "js_pesquisaInicial(true);", 1);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("iInicial", 20, 1, true, "text", 1, "onchange='js_pesquisaInicial(false);'", null, null, "width:83px;");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="honorario" style="width: 70px;">
                        Parcelas do Honorário:
                    </td>
                    <td>
                        <?php
                        db_input("ar43_numeroparcelas", 4, true, "ar43_numeroparcelas", true, "text", 4);
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="salvar" id="salvar" type="button" onclick='js_salvar();' value="Salvar">
        <input name="excluir" id="excluir" type="button" onclick='js_excluir();' value="Excluir">
        <input name="limpa" type="button" onclick='js_limpa();' value="Limpar Campos">
    </form>
</div>
<?php
db_menu();
?>
</body>

</html>

<script>
	$("excluir").disable();

	function js_salvar() {
		obj = document.form1;

		if (obj.v70_sequencial.value === '' && obj.iInicial.value === '') {
			alert('Campos Código Processo Foro ou Inicial do Foro devem ser preenchidos');
			return;
		};

		if (parseInt(obj.ar43_numeroparcelas.value) === 0 || obj.ar43_numeroparcelas.value === '') {
			alert('Ajuste o campo das parcelas.');
			obj.ar43_numeroparcelas.focus();
			return;
		}

		var oParam = new Object();
		oParam.executa = "salvar";
		oParam.sequencial = obj.sequencial.value;
		oParam.processoForo = obj.v70_sequencial.value;
		oParam.inicial = obj.iInicial.value;
		oParam.numeroParcelas = obj.ar43_numeroparcelas.value;

		new AjaxRequest("arr4_honorarioparcelamento.RPC.php", oParam, js_getSalvar).execute();
	}

	function js_excluir() {
		obj = document.form1;
		if (obj.v70_sequencial.value === '' && obj.iInicial.value === '') {
			alert('Campos Código Processo Foro ou Inicial do Foro devem ser preenchidos');
			return;
		};

		var oParam = new Object();
		oParam.executa = "excluir";
		oParam.sequencial = obj.sequencial.value;

		new AjaxRequest("arr4_honorarioparcelamento.RPC.php", oParam, js_getSalvar).execute();
	}

	function js_getSalvar(oRetorno) {
		alert(oRetorno.mensagem);

		if (oRetorno.erro) {
			return;
		}

		js_limpa();
	}

	function js_getParcela(oRetorno) {
		$("salvar").enable();
		$("excluir").enable();
		$("ar43_numeroparcelas").enable();
		obj.sequencial.value = null;

		if (oRetorno.erro) {
			if (oRetorno.validado) {
				alert(oRetorno.mensagemValidacao);
				$("salvar").disable();
				$("excluir").disable();
				$("ar43_numeroparcelas").disable();
			}

			if (oRetorno.mensagem != "") {
				alert(oRetorno.mensagem);
				return;
			}
		}

		if (oRetorno.numeroParcelas !== undefined) {
			obj.sequencial.value          = oRetorno.sequencial;
			obj.ar43_numeroparcelas.value = oRetorno.numeroParcelas;
		} else {
			$("excluir").disable();
        }
	}

	function js_limpa() {
		location.href = 'arr4_honorarioparcelamento001.php';
	}

	function js_pesquisaprocessoforo(mostra) {
		if (mostra == true) {
			var sUrl = 'func_processoforo.php?lAnuladas=false&funcao_js=parent.js_mostraProcessoForoJanela|v70_sequencial|v70_codforo'
			js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', true)

		} else {
			if (document.form1.v70_sequencial.value != '') {
				var sUrl = 'func_processoforo.php?pesquisa_chave=' + document.form1.v70_sequencial.value + '&funcao_js=parent.js_mostraprocessoforo' + '&lAnuladas=false';
				js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', false);
			}
		}
	}

	function js_mostraprocessoforo(chave, erro, chave2) {
		document.form1.v70_codforo.value = chave;
		$('v70_codforo').value = chave2;

		db_iframe_processoforo.hide();

		if (erro == true) {
			$('v70_sequencial').value = '';
			document.form1.v70_codforo.focus();
			document.form1.v70_codforo.value = '';
			$('v70_codforo').value = chave;
			return false;
		}

		$('iInicial').disable()

		js_carregaNumeroParcelas()
	}

	function js_mostraProcessoForoJanela(chave1, chave2) {

		document.form1.v70_sequencial.value = chave1;
		document.form1.v70_codforo.value = chave2;
		db_iframe_processoforo.hide();

		js_carregaNumeroParcelas();
	}

	function js_pesquisaInicial(lMostra) {
		if (lMostra == true) {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?funcao_js=parent.mostraInicialJanela|0", "Pesquisa", true);
		} else {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?pesquisa_chave=" + document.form1.iInicial.value + "&funcao_js=parent.mostraInicial", "Pesquisa", false);
		}
	}

	function js_carregaNumeroParcelas() {
		obj = document.form1;
		obj.ar43_numeroparcelas.value = '';

		var oParam = new Object();
		oParam.executa = "buscar";
		oParam.processoForo = obj.v70_sequencial.value;
		oParam.inicial = obj.iInicial.value;

		new AjaxRequest("arr4_honorarioparcelamento.RPC.php", oParam, js_getParcela).execute();
	}

	function mostraInicialJanela(iInicial) {
		document.form1.iInicial.value = iInicial;
		db_iframe_inicial.hide();
		mostraInicial(iInicial, false);
	}

	function mostraInicial(iInicial, lErro) {
		lAlteracao = false;

		if (lErro === true) {
			document.form1.iInicial.value = "";
			document.form1.iInicial.focus();
			alert("Código de Inicial do Foro inválido!");
		}

		$('v70_sequencial').disable()

		js_carregaNumeroParcelas()
	}

	$("v70_sequencial").addClassName("field-size2")
	$("v70_codforo").addClassName("field-size7")
	$("ar43_numeroparcelas").disable()
</script>
