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
			<legend>Histórico - Inclusão por Lista</legend>
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
			</table>
			<div>
				<b><h3 id="intervalo"></h3></b>
			</div>
		</fieldset>
	</form>
	<?php
	db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
	?>
</body>

</html>
<script>
	var processando = true;
	const url = '<?= ECIDADE_REQUEST_PATH ?>';
	const routers = {
		'pesquisar': url + 'v4/api/tributario/juridico/inclusaoiniciallista/gethistorico',
	};

	async function js_pesquisa() {

		k60_codigo = jQuery('#k60_codigo').val();
		if (k60_codigo == '') {
			alert('Informe a Lista');
			return
		}
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
					if(!res.data.min || !res.data.max){
						alert('Sem Intervalos');
					}else{
						alert('Intervalo de '+res.data.min+' a '+res.data.max);
						jQuery('#intervalo').text('Intervalo de '+res.data.min+' a '+res.data.max);
					}

				}
			}).catch((err) => {
				console.error(err.response.data);
			})
	}

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
			jQuery('#btpesquisar').prop('disabled', false);
		}
		db_iframe_lista.hide();
	}

	function js_mostralista1(chave1, chave2) {
		document.form1.k60_codigo.value = chave1;
		document.form1.k60_descr.value = chave2;
		jQuery('#btpesquisar').prop('disabled', false);
		db_iframe_lista.hide();
	}

</script>