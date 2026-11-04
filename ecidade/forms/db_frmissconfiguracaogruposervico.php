<?php
/**
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

$oDaoConfiguracaoGrupo->rotulo->label();

$oRotulo = new rotulocampo();
$oRotulo->label("q126_sequencial");

$aLocalPagamento = array(1 => 'Local de prestação', 2 => 'Sede Prestador', 3 => 'Sede Tomador');
$aTipoTributacao = array(1 => 'Fixo', 2 => 'Variável', 3 => 'Não Incide');

$sDesabilitaBotao = $db_opcao ? null : 'disabled="true"';

?>
<form name="form1" method="post" action="">
	<input type="hidden" value="<?= $q162_sequencial?>" id="q162_sequencial" name="q162_sequencial">
	<center>
	<fieldset style="width:480px;">
		<legend>Manutenção de Grupos de Serviço</legend>
		<table border="0">
			<tr>
				<td nowrap title="<?php echo $Tq136_issgruposervico?>">
					<strong>Grupo de serviço:</strong>
				</td>
				<td>
					<?php db_input('iCodigoGrupoServico', 10, $Iq136_issgruposervico, true, 'text', 3); ?>
					<?php db_input('sDescricaoGrupoServico', 50, null, true, 'text', 3); ?>
					<?php db_input('q136_sequencial', 10, null, true, 'hidden', 3); ?>
					<?php db_input('q136_issgruposervico', 10, null, true, 'hidden', 3); ?>
				</td>
			</tr>
			<tr>
				<td nowrap title="<?php echo $Tq136_exercicio; ?>">
					 <?php echo $Lq136_exercicio; ?>
				</td>
				<td>
					<?php db_input('q136_exercicio', 10, $Iq136_exercicio, true, 'text', 22, "") ?>
					<input type="hidden" id="exercicio_atual" value="<?php echo db_getsession('DB_anousu'); ?>" />
				</td>
			</tr>
			<tr>
				<td nowrap title="<?php echo $Tq136_tipotributacao; ?>">
					 <?php echo $Lq136_tipotributacao; ?>
				</td>
				<td>
					<?php db_select('q136_tipotributacao', $aTipoTributacao, true, $db_opcao, "onchange='js_tipoTributacao(this);'"); ?>
				</td>
			</tr>
			<tr>
				<td id="valor" nowrap title="<?php echo $Tq136_valor; ?>">
					<strong>Valor do índice:</strong>
				</td>
				<td>
					<?php db_input('q136_valor', 10, $Iq136_valor, true, 'text', $db_opcao, "") ?>
					<span id="porcentagem" style="display:none;">%</span>
				</td>
			</tr>
			<tr>
				<td nowrap title="<?php echo $Tq136_localpagamento; ?>">
					 <?php echo $Lq136_localpagamento; ?>
				</td>
				<td>
					<?php db_select('q136_localpagamento', $aLocalPagamento, true, $db_opcao, ""); ?>
				</td>
			</tr>
            <tr>
                <td nowrap title="<?php echo $Tq136_deducao ?>">
                    <strong>Deduz Valor Nota:</strong>
                </td>
                <td colspan="2">
                    <?php db_input('q136_deducao', 10, 'q136_deducao', true, 'checkbox', $db_opcao, 'onclick="isdeducao(this)"'); ?>
<!--                    <input type="checkbox" id="q136_deducao" name="q136_deducao">-->
                </td>
            </tr>
			<tr>
                <td nowrap title="<?php echo $Tq136_salaoparceiro ?>">
                    <strong>Salão Parceiro:</strong>
                </td>
                <td colspan="2">
                    <?php db_input('q136_salaoparceiro', 10, 'q136_salaoparceiro', true, 'checkbox', $db_opcao, 'onclick="issalaoparceiro(this)"'); ?>
<!--                    <input type="checkbox" id="q136_salaoparceiro" name="q136_salaoparceiro">-->
                </td>
            </tr>
            <tr>
                <td nowrap title="<?php echo $Tq136_retencao ?>">
                    <strong>Retenção p/ Prestação Fora do Município:</strong>
                </td>
                <td colspan="2">
                    <?php db_input('q136_retencao', 10, 'q136_retencao', true, 'checkbox', $db_opcao, 'onclick="isretencao(this)"'); ?>
<!--                    <input type="checkbox" id="q136_retencao" name="q136_retencao">-->
                </td>
            </tr>
			<tr>
				<td colspan="2">
					<fieldset>
						<legend>Vincular Anexo ao Grupo de Serviço</legend>
						<table>
							<tr>
								<td nowrap title="<?php echo $Tq157_descricao; ?>">
									<?php echo $Lq157_descricao; ?>
								</td>
								<td>
									<div id="descricaoAnexos">
										<!--Função JS Lista popula os campos daqui-->
									</div>
								</td>
							</tr>

							<tr>
								<td nowrap title="<?php echo $Tq162_data_fim; ?>">
									<?php echo $Lq162_data_fim; ?>
								</td>
								<td>
									<?php db_inputdata('q162_data_fim', "", "", "", true, 'text', $db_opcao); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</fieldset>
	</center>

	<br>

	<input name="salvar" type="submit" id="salvar" value="Salvar" <?php echo $sDesabilitaBotao; ?> />

	<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >

	<input name="novoVinculo" type="button" id="novoVinculo" value="Novo Vínculo" onclick="js_novoVinculo();" <?php echo $db_opcao == 22 ? 'disabled="true"' : '';?> >

	<?php
	if ($iCodigoGrupoServico != NULL) {
		$anousu = db_getsession('DB_anousu');
		$sql = "SELECT * FROM issqn.issgsanexos
				INNER JOIN issqn.issgruposervico ON q162_issgruposervico = q126_sequencial
				INNER JOIN issqn.issgscadanexos ON q162_issgscadanexos = q157_sequencial
				INNER JOIN issqn.issconfiguracaogruposervico ON q162_issgruposervico = q136_issgruposervico
				INNER JOIN db_estruturavalor ON db_estruturavalor.db121_sequencial = issgruposervico.q126_db_estruturavalor
				WHERE q126_sequencial = $q136_issgruposervico AND q136_exercicio = $anousu
				ORDER BY q162_data_fim DESC
		";
		$result = db_query($sql);
		$num = pg_numrows($result);
		?>
		<br><br>
		<fieldset>
			<legend>Anexos vinculados ao Grupo de Serviço</legend>
			<table class="form-container" border="1">
				<thead>
				<tr style="background-color: #e1e1e1">
					<th class="text-left">Estrutural</th>
					<th class="text-left">Descriçao da Estrutural</th>
					<th class="text-left">Descricao dos anexos</th>
					<th class="text-left">Data limite</th>
					<th class="text-left">Ações</th>
				</tr>
				</thead>
				<tbody>
					<?php for($i=0; $i < $num; $i++) {
						db_fieldsmemory($result,$i);
						if (!empty($q162_data_fim)) {
							// Formata data para mostrar na tabela de vínculos
							$data_fim = strtotime($q162_data_fim);
							$data_fim = date('d/m/Y',$data_fim);

							$q162_data_fim = strtotime($q162_data_fim);
						} else {
							$data_fim = "Sem Data";
							$q162_data_fim = "";
						}
					?>
					<tr class="cores">
						<td class="text-center field-size1"><?=$db121_estrutural?></td>
						<td class="text-left field-size6"><?=$db121_descricao?></td>
						<td class="text-left"><?=$q157_descricao?></td>
						<td class="text-left"><?=$data_fim?></td>
						<td class="text-center">
							<input id="numcgm" name="numcgm" type="hidden" value="<?=$j42_numcgm?>">
							<input name="alterar" type="button" href="#" title="Alterar vínculo." onclick="js_alterar(<?=$q162_sequencial?>, <?=$q162_issgscadanexos?>, <?=$q162_data_fim?>)" value="A"> |
							<!-- <input name="excluir" id="excluir" type="submit" title="Excluir vínculo." value="E"> -->
							<input name="excluir" type="button" title="Excluir vínculo." onclick="return js_excluir(<?=$q162_sequencial?>)" value="E">
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</fieldset>

	<?php
	}
	?>
</form>

<script type="text/javascript">

js_removeObj('msgBox');

function isretencao(checkbox) {
    if (checkbox.checked) {
        checkbox.value = 't';
        document.form1.q136_retencao = checkbox.value;
    } else {
        checkbox.value = 'f';
        document.form1.q136_retencao = checkbox.value;
    }
}

function issalaoparceiro(checkbox) {
    if (checkbox.checked) {
        checkbox.value = 't';
        document.form1.q136_salaoparceiro = checkbox.value;
    } else {
        checkbox.value = 'f';
        document.form1.salaoparceiro = checkbox.value;
    }
}

function isdeducao(checkbox) {
    if (checkbox.checked) {
        checkbox.value = 't';
        document.form1.q136_deducao = checkbox.value;
    } else {
        checkbox.value = 'f';
        document.form1.q136_deducao = checkbox.value;
    }
}


js_tipoTributacao( $('q136_tipotributacao') );
$('salvar').disabled = true;


if( !empty($F('iCodigoGrupoServico')) ){
  $('salvar').disabled = false;
}
/**
 * Funcao chamada ao alterar tipo de tributacao
 */
function js_tipoTributacao(oElemento) {

	var sValor = '';

	/**
	 * Fixo
	 */
	if ( oElemento.value == '1' ) {
		sValor += 'Valor do índice';
		$('porcentagem').style.display = 'none';
		$('q136_valor').readOnly = false;
	}

	/**
	 * Variavel
	 */
	else if (oElemento.value == '2') {

		sValor += 'Alíquota:';
		$('porcentagem').style.display = '';
		$('q136_valor').readOnly = false;
	}

	/**
	 * Não incide
	 */
  else if (oElemento.value == '3') {

  	sValor += 'Não Incide';
  	$('porcentagem').style.display = 'none';
  	$('q136_valor').value = '0';
  	$('q136_valor').readOnly = true;
	}

	$('valor').innerHTML = '<strong>' + sValor + '</strong>';
}

function js_pesquisa() {
	js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_issgruposervico', 'func_issgruposervico.php?funcao_js=parent.js_preenchePesquisa|q126_sequencial', 'Pesquisa', true);
}

function js_preenchePesquisa(iCodigoGrupoServico, sDescricao) {

  js_divCarregando('Buscando dados do grupo de serviço', 'msgBox');
  db_iframe_issgruposervico.hide();
  location.href = 'iss1_issconfiguracaogruposervico002.php?iCodigoGrupoServico=' + iCodigoGrupoServico;
}

js_descricaoAnexos();

function js_descricaoAnexos()
{
	var oParam = new Object();
	oParam.executa = "lista";

	new AjaxRequest("iss1_issconfiguracaogruposervico002.RPC.php", oParam, js_getDescricaoAnexos).execute();
}

function js_getDescricaoAnexos(oRetorno)
{
	if (oRetorno.mensagem != "") {
		alert(oRetorno.mensagem);
	}

	if (oRetorno.erro) {
		return;
	}

	const lista = document.getElementById("descricaoAnexos");
	lista.innerHTML = "";
	const select = document.createElement("select");

	const descricao = 'Selecionar';
	const option = document.createElement("option");
	select.setAttribute("name","selectanexo");
	select.setAttribute("id","selectanexo");
	option.value = 0;
	option.appendChild(document.createTextNode(descricao));
	select.appendChild(option);
	lista.appendChild(select);

	for (var index = 0; index < oRetorno.lista.length; index++) {
		const descricao = oRetorno.lista[index].q157_descricao;
		const option = document.createElement("option");

		select.setAttribute("name","selectanexo");
		select.setAttribute("id","selectanexo");
		option.value = oRetorno.lista[index].q157_sequencial;
		option.appendChild(document.createTextNode(descricao));
		select.appendChild(option);
		lista.appendChild(select);
	}

}

function js_novoVinculo() {
	obj = document.form1;
	obj.q162_sequencial.value = '';
	obj.selectanexo.value = 0;
	obj.q162_data_fim.value = '';
}

function js_alterar(sequencial, anexo, datafim) {
	obj = document.form1;
	obj.q162_sequencial.value = sequencial;
	obj.selectanexo.value = anexo;



	if (datafim === undefined) {
		obj.q162_data_fim.value = "";
	} else {
		var data = new Date(datafim * 1000);
		obj.q162_data_fim.value = data.getDateBR();
	}

}

// Confirm de exclusão de submit
document.getElementById('excluir').onclick = function() {
    if (confirm('Deseja excluir o vínculo?')) {
		obj = document.form1;
		obj.q162_sequencial.value = sequencial;

		return true;
    } else {
		return false;
    }
}

function js_excluir(sequencial) {

	obj = document.form1;
	obj.q162_sequencial.value = sequencial;

	if (confirm("Deseja excluir o vínculo ?")) {
		var oParam = new Object();
		oParam.q162_sequencial = sequencial;
		oParam.executa = "excluir";

		new AjaxRequest("iss1_issconfiguracaogruposervico002.RPC.php", oParam, js_getExcluir).execute();
	}
}

function js_getExcluir(oRetorno) {
	alert(oRetorno.mensagem);

	if (oRetorno.erro) {
		return;
	}
	window.location = window.location.href;
}

</script>
