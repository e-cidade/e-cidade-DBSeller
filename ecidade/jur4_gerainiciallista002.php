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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
include(modification("classes/db_inicial_classe.php"));
include(modification("classes/db_inicialcert_classe.php"));
include(modification("classes/db_inicialmov_classe.php"));
include(modification("classes/db_inicialnomes_classe.php"));
include(modification("classes/db_inicialnumpre_classe.php"));
include(modification("classes/db_arrecad_classe.php"));
include(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);

$clinicial = new cl_inicial;
$clinicialcert = new cl_inicialcert;
$clinicialmov = new cl_inicialmov;
$clinicialnomes = new cl_inicialnomes;
$clinicialnumpre = new cl_inicialnumpre;
$clarrecad = new cl_arrecad;

$clrotulo = new rotulocampo;
$clrotulo->label("v13_certid");
$clrotulo->label("v50_advog");
$clrotulo->label("v54_descr");
$clrotulo->label("v50_codlocal");
$clrotulo->label("z01_nome");
$clrotulo->label('k60_codigo');
$clrotulo->label('k60_descr');

?>
<html>

<head>
	<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Expires" CONTENT="0">
	<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.css" rel="stylesheet">
	<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
	<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
	<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
	<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC>
	<form class="container" name="form1" method="post" action="">
		<fieldset>
			<legend>Procedimentos - Inicial/Inclusão por Lista</legend>
			<table class="form-container">
				<tr>
					<td nowrap title="<?php echo $Tk60_codigo ?>">
						<?php
						db_ancora($Lk60_codigo, "js_pesquisalista(true);", 4);
						?>
					</td>

					<td>
						<div>
							<?php
							db_input("k60_codigo", 4, $Ik60_codigo, true, "text", 4, "onchange='js_pesquisalista(false);'");
							db_input("k60_descr", 40, $Ik60_descr, true, "text", 3, "");
							?>
							<button id="btpesquisar" type="button" onClick="js_pesquisa()" disabled="disabled">Pesquisar</button>
						</div>
					</td>
				</tr>
				<tr>
					<td><b>Agrupar por:</b></td>
					<td>
						<?php
						$tipo_arr = array("mi" => "Matricula e Inscrição", "c" => "CGM", "n" => "Não Agrupar");
						db_select("agrupa", $tipo_arr, true, "text", 1);
						?>
					</td>
				</tr>
				<tr>
					<td title="<?= @$Tv50_advog ?>">
						<?php
						db_ancora("<strong>Advogado</strong>", ' js_advog(true); ', 1);
						?>
					</td>
					<td>
						<?php
						db_input('v50_advog', 6, $Iv50_advog, true, 'text', 1, "onchange='js_advog(false)'");
						db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3);
						?>
					</td>
				</tr>
				<tr>
					<td nowrap title="<?= @$Tv50_codlocal ?>">
						<?php
						db_ancora("<strong>Local Foro</strong>", "js_codlocal(true);", 1);
						?>
					</td>
					<td>
						<?php
						db_input('v50_codlocal', 6, $Iv50_codlocal, true, 'text', 1, " onchange='js_codlocal(false);'")
						?>
						<?php
						db_input('v54_descr', 40, $Iv54_descr, true, 'text', 3)
						?>
					</td>
				</tr>
				<tr>
					<div id="divtable">
						<table id="table"></table>
					</div>
				</tr>
			</table>
		</fieldset>
		<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" align="center">
			<input type="button" id="btprocessar" value="Processar Inclusão por Lista" onclick="js_processar()" style="display: none;">
		</div>
		<div id="progress" style="display: none; justify-content: center;">
			<?php db_criatermometro('termometro', 'Concluido...', 'blue', 1); ?>
		</div>
	</form>
</body>

</html>
<script>
	var processando = true;
	const url = '<?= ECIDADE_REQUEST_PATH ?>';
	const routers = {
		'pesquisar': url + 'v4/api/tributario/juridico/inclusaoiniciallista/pesquisar',
		'processar': url + 'v4/api/tributario/juridico/inclusaoiniciallista/processar',
		'getprocessamento': url + 'v4/api/tributario/juridico/inclusaoiniciallista/getprocessamento'
	};

	async function js_pesquisa() {

		k60_codigo = jQuery('#k60_codigo').val();
		if (k60_codigo == '') {
			alert('Informe a Lista');
			return
		}
		$table.bootstrapTable('destroy');
		const data = {
			k60_codigo: k60_codigo
		};
		const dado = new FormData;
		for (index in data) {
			dado.append(index, data[index]);
		}

		HttpClient.post(routers.pesquisar, {
				body: dado,
				reportProgress: true
			})
			.then((res) => {
				if (res.hasOwnProperty('data')) {
					if (res.error) {
						alert(res.message);
						return
					}
					jQuery('#btprocessar').show();
					$table.bootstrapTable({
						cache: false,
						columns,
						striped: true,
						pagination: true,
						pageSize: 10,
						pageList: [10, 25, 50, 100],
						data: res.data
					});
				}
			}).catch((err) => {
				console.error(err.response.data);
			})
	}

	function js_processar() {
		k60_codigo = jQuery('#k60_codigo').val();
		v50_advog = jQuery('#v50_advog').val();
		v50_codlocal = jQuery('#v50_codlocal').val();
		agrupa = jQuery('#agrupa').val();

		if (k60_codigo == '') {
			alert('Informe a Lista');
			return
		}

		if (v50_advog == '') {
			alert('Informe o campo Advogado');
			return
		}

		if (v50_codlocal == '') {
			alert('Informe o Local Foro');
			return
		}

		if (!confirm("Deseja fazer a Inclusão da Inicial por lista?")) {
			return false;
		}
		const data = {
			k60_codigo: k60_codigo,
			v50_advog: v50_advog,
			v50_codlocal: v50_codlocal,
			agrupa: agrupa
		};
		const dado = new FormData;
		for (index in data) {
			dado.append(index, data[index]);
		}

		HttpClient.post(routers.processar, {
				body: dado,
				reportProgress: true
			})
			.then((res) => {
				if (res.hasOwnProperty('data')) {
					if (res.error) {
						alert(res.message);
						return
					}
					alert('Iniciando Processamento de Inclusão!');
					jQuery('#btprocessar').hide();
					$table.bootstrapTable('destroy');
					jQuery('#k60_codigo').val('');
					jQuery('#v50_advog').val('');
					jQuery('#v50_codlocal').val('');

					setTimeout(function() {
						processando = true;
						intervalo();
					}, 5000);

				}
			}).catch((err) => {
				console.error(err.response.data);
			})
	}

	var $table = jQuery('#table')
	const columns = [{
			field: 'origem',
			title: 'Origem',
			sortable: true
		}, {
			field: 'v13_certid',
			title: 'Certidão',
			sortable: true,
			align: 'center',
		},
		{
			field: 'situacao',
			align: 'center',
			title: 'Situação',
			sortable: true
		},
		{
			align: 'center',
			title: 'Inicial',
			field: 'v51_inicial',
			sortable: true
		}
	];

	function js_pesquisalista(mostra) {
		if (mostra == true) {
			js_OpenJanelaIframe('', 'db_iframe_lista', 'func_lista.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr', 'Pesquisa', true);
		} else {
			js_OpenJanelaIframe('', 'db_iframe_lista', 'func_lista.php?pesquisa_chave=' + document.form1.k60_codigo.value + '&funcao_js=parent.js_mostralista', 'Pesquisa', 'false');
		}
	}

	function js_mostralista(chave, erro) {
		document.form1.k60_descr.value = chave;
		if (erro == true) {
			document.form1.k60_descr.focus();
			document.form1.k60_descr.value = '';
		} else {
			resetlist();
			jQuery('#btpesquisar').prop('disabled', false);
		}
		db_iframe_lista.hide();
	}

	function js_mostralista1(chave1, chave2) {
		document.form1.k60_codigo.value = chave1;
		document.form1.k60_descr.value = chave2;
		resetlist();
		jQuery('#btpesquisar').prop('disabled', false);
		db_iframe_lista.hide();
	}

	function js_codlocal(mostra) {
		if (mostra == true) {
			js_OpenJanelaIframe('', 'db_iframe_localiza', 'func_localiza.php?funcao_js=parent.js_mostralocaliza1|v54_codlocal|v54_descr', 'Pesquisa', true);
		} else {
			if (document.form1.v50_codlocal.value != '') {
				js_OpenJanelaIframe('', 'db_iframe_localiza', 'func_localiza.php?pesquisa_chave=' + document.form1.v50_codlocal.value + '&funcao_js=parent.js_mostralocaliza', 'Pesquisa', false);
			} else {
				document.form1.v54_descr.value = '';
			}
		}
	}

	function js_mostralocaliza(chave, erro) {
		document.form1.v54_descr.value = chave;
		if (erro == true) {
			document.form1.v50_codlocal.focus();
			document.form1.v50_codlocal.value = '';
		}
	}

	function js_mostralocaliza1(chave1, chave2) {
		document.form1.v50_codlocal.value = chave1;
		document.form1.v54_descr.value = chave2;
		db_iframe_localiza.hide();
	}

	function js_advog(mostra) {
		if (mostra == true) {
			js_OpenJanelaIframe('', 'db_iframe_advog', 'func_advog.php?funcao_js=parent.js_mostraadvog1|v57_numcgm|z01_nome', 'Pesquisa', true);
		} else {
			if (document.form1.v50_advog.value != '') {
				js_OpenJanelaIframe('', 'db_iframe_advog', 'func_advog.php?pesquisa_chave=' + document.form1.v50_advog.value + '&funcao_js=parent.js_mostraadvog', 'Pesquisa', false);
			} else {
				document.form1.z01_nome.value = '';
			}
		}
	}

	function js_mostraadvog(chave, erro) {
		document.form1.z01_nome.value = chave;
		if (erro == true) {
			document.form1.v50_advog.focus();
			document.form1.v50_advog.value = '';
		}
	}

	function js_mostraadvog1(chave1, chave2) {
		document.form1.v50_advog.value = chave1;
		document.form1.z01_nome.value = chave2;
		db_iframe_advog.hide();
	}

	function resetlist() {
		$table.bootstrapTable('destroy');
		jQuery('#btprocessar').hide();
	}

	function intervalo() {
		var intervalo = setInterval(() => {
				if (processando) {
					const dado = new FormData;
					HttpClient.get(routers.getprocessamento, {
							body: dado,
							reportProgress: false
						})
						.then((res) => {
							const data = res.data
							if (data.processamento) {
								jQuery('#progress').show();
								processando = true;
								var value = data.quantidade;
								if (value == 0) {
									value = 1;
								}
								js_termo_termometro(value);

								jQuery('#btprocessar').hide();

							} else {
								processando = false;
								jQuery('#progress').hide();
								clearInterval(intervalo);
							}
						}).catch(() => {
							processando = false;
							jQuery('#progress').hide();
							clearInterval(intervalo);
						});
				}
			},
			5000);
	}

	intervalo();
</script>