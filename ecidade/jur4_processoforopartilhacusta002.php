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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$clrotulo = new rotulocampo;

$clrotulo->label('v76_tipolancamento');
$clrotulo->label('v76_valorpartilha');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$db_opcao = 1;
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>

    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>

        textarea {
            width: 100%;
        }

        #ctnCustas {
            width: 100%;
            height: 200px;
            overflow: auto;
        }

        table.form-container:not([rel="ignore-css"]) tr > td:nth-child(odd) {
            width: 35%;
        }

        table.form-container:not([rel="ignore-css"]) tr > td:nth-child(even):not([rel="ignore-css"]) input {
            width: 100%;
        }
    </style>
</head>
<body class='body-default'>

<form name="form1" method="post">
    <div class="container">
        <fieldset style="width:570px;">
            <legend>Manutenção de Taxas/Custas</legend>
            <fieldset class='separator'>
                <legend>Dados Processo Foro</legend>

                <table class="form-container">
                    <tr>
                        <td class="bold" nowrap="nowrap">Processo do Sistema :</td>
                        <td>
                            <?php
                            db_input("v70_sequencial", 30, "", true, "text", 3, "");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="bold" nowrap="nowrap">Código Processo Foro:</td>
                        <td>
                            <?php
                            db_input("v70_codforo", 30, "", true, "text", 3, "");
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset class='separator' style='margin-top:15px;'>
                <legend>Dados da Partilha</legend>

                <table class="form-container">
                    <tr>
                        <td class="bold" nowrap="nowrap">Tipo de Lançamento :</td>
                        <td>
                            <?php
                            $aTipoLancamento = array("0" => "Selecione...", "2" => "Pagamento", "3" => "Isenção");
                            db_select("v76_tipolancamento", $aTipoLancamento, true, $db_opcao,
                                "onchange='js_lancamento(this.value);'", "v76_tipolancamento");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold" nowrap="nowrap">Data do Pagamento :</td>
                        <td rel="ignore-css">
                            <?php
                            db_inputdata('v76_dtpagamento', @$v76_dtpagamento_dia, @$v76_dtpagamento_mes,
                                @$v76_dtpagamento_ano, true, 'text', $db_opcao);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <fieldset>
                                <legend>Observação</legend>
                                <?php db_textarea("v76_obs", 3, 50, "", true, "text", $db_opcao, "", "", "", 400); ?>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>
    </div>

    <div class='subcontainer'>
        <fieldset style="margin-top:15px; min-height: 300px;">
            <legend>Taxas/Custas</legend>
            <div id='ctnGridCustas' style='width : 600px;'></div>
        </fieldset>
    </div>
    <div class='subcontainer'>
        <input type='button' id='incluir' name='incluir' value='Processar'/>
        <input type='button' id="pesquisar" value='Pesquisar' style="margin-right:15px;"/>
    </div>
</form>
</body>
<?php
db_menu();
?>
</html>

<script>

	// exemplo de grid com checkbox rec4_db_departrhlocaltrab001.php

	var oRetorno = null;

	var get = js_urlToObject();

	var collection = new Collection().setId("id");
	var grid = new DatagridCollection(collection).configure({order: false, 'height': 300, 'width': '600px'});
	grid.addColumn('id');
	grid.addColumn("descricao", {'label': "Taxa/Custa", "width": '45%'});
	grid.addColumn("valor", {'label': "Valor", "width": '17%'})
		.transform(function (value, item) {

			var classe = '';

			if (item.tipoLancamento != 3 && item.tipoLancamento != 1) {
				classe = 'valor-custa';
			}

			return '<input id="valor-custa-' + item.id + '" class="' + classe + '" type="text" value="' + value + '" disabled style="width:100%; color:black;" onblur="js_ValidaMaiusculo(this, \'f\', event)" oninput="js_ValidaCampos(this, 4, \'Valor da Partilha\')" onkeydown="return js_controla_tecla_enter(this,event);">';
		});
	grid.addColumn("status", {'label': "Status", "width": '30%', 'align': 'center'});

	grid.grid.setCheckbox(0);
	grid.hideColumns([1]);
	grid.show($('ctnGridCustas'));

	grid.setEvent('onafterrenderrows', function () {
		var tipoLancamento = $F('v76_tipolancamento');

		if (tipoLancamento == 0) {
			return;
		}

		for (row of grid.grid.getRows()) {
			if (row.itemCollection.tipoLancamento == tipoLancamento || !row.itemCollection.status) {
				continue;
			}

			var content = row.aCells[0].content;

			var checkbox = createElementFromHTML(content);

			$(checkbox.id).disabled = true;
		}
	});

	function buscaTaxas() {
		var parametros = {'sExecucao': 'buscarTaxasProcessuais', 'processo': get.v70_sequencial};

		if (oRetorno) {
			montarGrid(oRetorno.taxas);
			return;
		}

		new AjaxRequest('jur4_manutencaotaxacusta.RPC.php', parametros, function (retorno, erro) {
			if (erro) {
				alert(retorno.sMensagem);
				return false;
			}

			oRetorno = retorno;

			montarGrid(retorno.taxas);
		}).setMessage('Buscando taxas').execute();
	}

	function createElementFromHTML(htmlString) {
		var div = document.createElement('div');
		div.innerHTML = htmlString.trim();

		return div.firstChild;
	}

	function montarGrid(taxas) {

		$("incluir").enable();

		for (var taxa of taxas) {
			if (typeof taxa.ID !== "undefined") {
				delete taxa.ID;
			}

			if (!taxa.liberaCadastro) {
				$("incluir").disable();
				break;
			}
		}

		grid.collection.clear();
		grid.clear();

		grid.setSelectedItens([]);

		for (var taxa of taxas) {

			if (typeof taxa.ID !== "undefined") {
				delete taxa.ID;
			}

			grid.collection.add(taxa);

			if (taxa.tipoLancamento == $F('v76_tipolancamento')) {
				grid.addSelectedItens(taxa.id);
			}
		}

		grid.reload();
	}

	var aBotaoCalendario = document.getElementsByName("dtjs_v76_dtpagamento");

	function js_valida() {
		var iLancamento = $F('v76_tipolancamento');

		if (iLancamento == null || iLancamento == '' || iLancamento == '0') {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.selecione_tipo_lancamento'));
			return false;
		}

		if (iLancamento == 3 && $F("v76_obs").trim() == "") {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.preencha_observacao'));
			$("v76_obs").focus();

			return false;
		}

		if (iLancamento == 2 && $F("v76_dtpagamento").trim() == "") {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.informe_data_pagamento'));
			$("v76_dtpagamento").focus();

			return false;
		}
	}

	function js_lancamento(iTipo) {
		buscaTaxas();

		$$('.valor-custa').each(function (item) {
			item.removeAttribute('disabled');
		});

		//Nenhum tipo de lançamento selecionado
		if (iTipo == 0) {
			$$('.valor-custa').each(function (item) {
				item.setAttribute('disabled', 'disabled');
			});

			$('v76_obs').setAttribute('readonly', 'readonly');
			$('v76_obs').style.backgroundColor = "#DEB887";
			$('v76_obs').value = '';
			$('v76_dtpagamento').setAttribute('readonly', 'readonly');
			$('v76_dtpagamento').style.backgroundColor = "#DEB887";
			$('v76_dtpagamento').value = '';
			aBotaoCalendario[0].style.display = 'none';
		}

		if (iTipo == 3) {
			/* isento */
			$$('.valor-custa').each(function (item) {
				item.setAttribute('disabled', 'disabled');
			});

			$('v76_obs').removeAttribute('readonly');
			$('v76_obs').value = oRetorno.observacaoIsencao;
			$('v76_dtpagamento').setAttribute('readonly', 'readonly');
			$('v76_obs').style.backgroundColor = "#FFF";
			$('v76_dtpagamento').style.backgroundColor = "#DEB887";
			$('v76_dtpagamento').value = '';
			aBotaoCalendario[0].style.display = 'none';
		}

		if (iTipo == 2) {  // manual
			$('v76_obs').removeAttribute('readonly');
			$('v76_obs').style.backgroundColor = "#FFF";
			$('v76_obs').value = oRetorno.observacaoPagamento;
			$('v76_dtpagamento').removeAttribute('readonly');
			$('v76_dtpagamento').style.backgroundColor = "#FFF";
			$('v76_dtpagamento').value = oRetorno.dtpagamento;
			aBotaoCalendario[0].style.display = 'inline';
		}
	}

	function js_validaDados() {
		if ($F('v76_tipolancamento') == 0) {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.tipolancamento_obrigatorio'));
			return false;
		}

		/**
		 * Validacões para Tipo de Lançamento = Manual/Pago
		 */
		if ($F('v76_tipolancamento') == 2 && $F('v76_dtpagamento') == '') {
			$('v76_dtpagamento').value = '';

			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.datapagamento_obrigatorio'));
			return false;
		}

		/**
		 * Validacões para Tipo de Lançamento = Isento
		 */
		if ($F('v76_tipolancamento') == 3 && $F('v76_obs') == '') {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.observacao_obrigatorio'));
			return false;
		}

		if ($F('v76_obs').length > 400) {
			alert(_M('tributario.juridico.jur4_processoforopartilhacusta002.limite_observacao'));
			$('v76_obs').value = $F('v76_obs').substr(0, 399);
			return false;
		}

		return true;
	}

	$('incluir').observe('click', function () {
		if (!js_validaDados()) {
			return false;
		}

		var custas = [];

		for (var obj of collection.get()) {
			if (!obj.datagridRow.isSelected) {
				continue;
			}

			var custa = obj.build(),
				fieldValue = $('valor-custa-' + obj.id).value;
			custa.id = obj.id;

			if (fieldValue != custa.valor) {
				custa.valor = fieldValue;
			}

			custas.push(custa);
		}

		var parametros = {
			'sExecucao': 'manutencaoCustasProcessuais',
			'tipoLancamento': $F('v76_tipolancamento'),
			'processo': get.v70_sequencial,
			'dtPagamento': $F('v76_dtpagamento'),
			'observacao': $F('v76_obs'),
			'custas': custas
		};

		new AjaxRequest('jur4_manutencaotaxacusta.RPC.php', parametros, function (retorno, erro) {
			alert(retorno.sMensagem);
			if (erro) {
				return false;
			}

			$('v76_tipolancamento').value = 0;

			oRetorno = null;
			js_lancamento(0);

		}).setMessage('Processando custas...').execute();
	});

	$('pesquisar').observe('click', function () {
		location.href = 'jur4_processoforopartilhacusta001.php';
	});

</script>
<?php
echo "<script>js_lancamento(0, true); </script>";
?>
