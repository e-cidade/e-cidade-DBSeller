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

$cliptubaseregimovel->rotulo->label();
$clpercposserural->rotulo->label();

$clcgm = new cl_cgm();
$clcgm->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j69_descr");
$clrotulo->label("j01_numcgm");
$clrotulo->label("j107_sequencial");
$clrotulo->label("j107_nome");
$clrotulo->label("j40_registrocartografico");
$clrotulo->label("j01_distrito");
$clrotulo->label("j01_hectare");
$clrotulo->label("j01_situcad");
$clrotulo->label("j01_datacad");
$clrotulo->label("j01_processo");
$clrotulo->label("j01_incra");
$clrotulo->label("j01_descrlocal");

?>
<script>
function js_verinome(){
  if(document.form1.z01_nome.value==""){
    alert("Verifique se o nome do NUMCGM esta correto!");
    return false;
  }

  if(document.form1.j01_fracao && document.form1.j01_fracao.value > 100){
   alert("Fração Ideal informada maior que 100%");
   document.form1.j01_fracao.value = "";
   document.form1.j01_fracao.focus();
   return false;
  }

  return  parent.js_veripros("iptubase");

}
</script>
<br>
<?php
  db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
<table border="0" bgcolor="#CCCCCC">
	<tr>
		<td colspan="2">
		<fieldset><legend> <b>Dados referentes a matrícula</b></legend>
		<table>
			<tr>
				<td nowrap title="<?php echo @$Tj01_matric?>" width="200" ><?php echo @$Lj01_matric?></td>
				<td><?php
                    db_input('j01_matric',10,$Ij01_matric,true,'text',3,"");
                    db_input('j01_idbql',10,$Ij01_idbql,true,'hidden',3,"");
				    ?>
                </td>
			</tr>
			<tr>
				<td nowrap title="<?php echo @$Tj01_unidade?>"><?php echo @$Lj01_unidade?> </td>
				<td><?php db_input('j01_unidade',10,$Ij01_unidade,true,'number',$db_opcao,"onchange='js_alteraInscricaoImobiliaria();'"); ?> </td>
			</tr>
			<tr>
				<td nowrap title="<?php echo @$Tj01_numcgm?>"><?php
				db_ancora(@$Lj01_numcgm,"js_pesquisaj01_numcgm(true);",$db_opcao);
				?></td>
				<td><?php
                    db_input('j01_numcgm',10,$Ij01_numcgm,true,'text',$db_opcao," onchange='js_pesquisaj01_numcgm(false);'");
                    db_input('z01_nome',56,$Iz01_nome,true,'text',3,'');
                    ?>
				</td>
			</tr>
                       <!--Tipo de proprietário-->
                    
                    <tr>
                        <td nowrap title="<?php echo $Tj163_descricao ?>"><?php echo $Lj163_descricao ?></td>
                        <td id="listaProprietario"></td>
                    </tr>
		<?php 

		empty($j01_tipoimovel) ? $j01_tipoimovel="" : $j01_tipoimovel ;
		empty($tipoImovel) ? $tipoImovel="" : $tipoImovel ;

		if ($tipoImovel == "2" || $j01_tipoimovel == "2") :
			if (empty($j166_sequencial)) {
				$j166_sequencial="";
			}
			?>
			<tr>
				<td><strong>Percentual de Posse (%):</strong></td>
				<td>
                    <input type="hidden" name="j166_sequencial" id="j166_sequencial" value="<?php echo $j166_sequencial ?>">
                    <?php db_input('j166_percentual',10,$Ij166_percentual,true,'text',1,"","","","",5); ?>
				</td>
			</tr>
		<?php endif;?>

	  <?php if ($tipoImovel != "2" && $j01_tipoimovel != "2") : ?>
		    <tr>
                <td nowrap title="Fra&ccedil;&atilde;o por propriet&aacute;rio"><b>Fra&ccedil;&atilde;o por propriet&aacute;rio</b></td>
                <td><?php db_input('j01_fracaoproprietario',9,$Ij01_fracaoproprietario,true,'text',"3","") ?>%</td>
            </tr>
			<tr>
				<td nowrap title="<?php echo @$Tj01_fracao?>"><?php echo @$Lj01_fracao?></td>
				<td><?php db_input('j01_fracao',9,$Ij01_fracao,true,'text',$db_opcao,"onchange=\"js_calculaFracao('fracao');\""); ?>%</td>
			</tr>
			<?php
			    $escondeInput = 'hidden';
			    if ($j18_utilizaareaprivativa != "f") {
                    $escondeInput = 'text';
            ?>
			<tr> 
				<td nowrap title="<?php echo @$Tj01_areaprivativa?>"><?php echo $Lj01_areaprivativa?></td>
				<td>
            <?php } ?>
					<?php db_input('j01_areaprivativa',9,$Ij01_areaprivativa,true,$escondeInput,$db_opcao,"onchange=\"js_calculaFracao('areaprivativa');\"") ?>
					<input type="hidden" name="j34_area" id="j34_area" value="<?php echo $j34_area?>">
					<?php if ($j18_utilizaareaprivativa != "f") { ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td nowrap title="<?php echo @$Tj40_refant?>"><?php echo @$Lj40_refant?></td>
				<td><?php db_input('j40_refant',9,$Ij40_refant,true,'text',isset($db_opcaorefant)?$db_opcaorefant:$db_opcao,"") ?></td>
			</tr>
            <tr>
                <td nowrap title="<?php echo @$Tj40_registrocartografico?>"><?php echo @$Lj40_registrocartografico?></td>
                <td><?php db_input('j40_registrocartografico',9,$Ij40_registrocartografico,true,'text',$db_opcao,"") ?></td>
            </tr>
			<tr>
				<td><?php db_ancora('<b>Cód Condomínio</b>','js_pesquisa_j107_sequencial(true)',$db_opcao); ?></td>
				<td>
                    <?php
                        db_input('j107_sequencial',10,$Ij107_sequencial,true,'text',$db_opcao,'onchange=js_pesquisa_j107_sequencial(false)');
                        db_input('j107_nome',56,$Ij107_nome,true,'text',3,'');
                    ?>
				</td>
			</tr>
			<tr>
				<td id="predio">
					<?php
						if(isset($j111_nome)){
							echo "<b>Prédio:</b>";
						}
					?>
				</td>
				<td id="sltpredio">
					<?php
						if(isset($j111_nome)){
					?>
						<input type="hidden" name="predios" id="predios" value="<?php echo $j111_sequencial?>">
						<input type="text" name="nomepredio" id="nomepredio" value="<?php echo $j111_nome?>" disabled="disabled" style="background-color: #DEB887; color:#000000;">
					<?php
						}
					?>
				</td>
			</tr>

			<tr>
                <td colspan="2" title="<?php echo @$Tj26_obs?>">
                <fieldset>
                  <legend><?php echo @$Lj26_obs?></legend>
                    <?php db_textarea('j26_obs',10,93,@$Ij26_obs,true,'text',$db_opcao,"","","#E6E4F1"); ?>
                </fieldset>
			</tr>
		</table>
		</fieldset>
		<td>
		<?php endif ?>
	<tr>
		<td colspan="2">
		<fieldset><legend> <b>Dados referentes ao registro de imóveis</b></legend>
		<table>
		<?php if ($tipoImovel != "2" && $j01_tipoimovel != "2") : ?>
			<tr>
				<td nowrap title="<?php echo @$Tj04_setorregimovel?>" width="200"><?php
				db_ancora(@$Lj04_setorregimovel,"js_pesquisaj04_setorregimovel(true);",$db_opcao);
				?></td>
				<td><?php
				db_input('j04_setorregimovel',10,$Ij04_setorregimovel,true,'text',$db_opcao," onchange='js_pesquisaj04_setorregimovel(false);'");
				db_input('j69_descr',56,$Ij69_descr,true,'text',3,'');
				?></td>
			</tr>
		<?php endif ?>

		<?php if ($tipoImovel == "2" || $j01_tipoimovel == "2") : 
			if (empty($tipoImovel) && !empty($j01_tipoimovel)) {
				$tipoImovel = $j01_tipoimovel;
			} else if (!empty($tipoImovel) && empty($j01_tipoimovel)) {
				$j01_tipoimovel = $tipoImovel;
			}

			// Busca dados do imovel rural se existir uma matrícula para alterar
			if (!empty($j01_matric)) {
				$buscaLoteRural = db_query("SELECT * FROM iptubase WHERE j01_idbql = 1000000000 and j01_matric = $j01_matric and j01_tipoimovel = $j01_tipoimovel");
				db_fieldsmemory($buscaLoteRural, 0);
			}
		?>

			<!-- Distrito -->
			<tr>
				<td nowrap title="<?php echo @$Tj01_distrito?>">
					<input name="oid" type="hidden" value="<?php echo @$oid?>">
					<?php
						db_ancora('Distrito',"js_pesquisaj01_distrito(true);",$db_opcao);
					?>
				</td>
				<td>
					<?php
						$j90_descr = "";
						if (!empty($j01_distrito)) {
							// Busca descrição do distrito na tabela setorfiscal
							$sql = "SELECT j90_descr FROM setorfiscal WHERE j90_codigo = $j01_distrito";
							$result = db_query($sql);
							db_fieldsmemory($result, 0);	
						}
						db_input('j01_distrito',10,$Ij01_distrito,true,'text',$db_opcao," onchange='js_pesquisaj01_distrito(false);'");
						db_input('j90_descr',40,$j90_descr,true,'text',3,'');
					?>
				</td>
			</tr>

			<!-- Hectare --> 
			<tr>
				<td nowrap title="<?php echo @$Tj01_hectare?>">
					<label><strong>Hectare(ha):</strong></label>
				</td>
				<td><?php db_input('j01_hectare',10,$Ij01_hectare,'float',$db_opcao,""); ?></td>
			</tr>

			<!-- Situacao Cadastro --> 
			<tr>

				<td nowrap title="<?php echo @$Tj01_situcad?>">
					<label><strong>Situação Cadastral:</strong></label>
				</td>
				<td>
					<select name="j01_situcad" id="j01_situcad">
						<option value="Ativo">Ativo</option>
						<?php
						if (!empty($j01_matric)) {
							// Busca se a matrícula atual possui status de baixa
							$sql = "SELECT * FROM iptubase WHERE j01_matric = $j01_matric AND j01_baixa IS NOT NULL";
							$result = db_query($sql);

							// Caso for matrícula baixada, exibi opção de baixado
							if (pg_num_rows($result) > 0) {
							?>
							<option value="Baixado" selected>Baixado</option>
							<?php
							} 
						}
						?>
						<option value="Suspenso">Suspenso</option>

					</select>
				</td>
			</tr>

			<!-- Data Cadastro -->
			<tr>
				<td nowrap title="<?php echo @$Tj01_datacad?>">
					<label><strong>Data Cadastro:</strong></label>
				</td>
				<td><?php
				$j01_datacad_dia = !empty($j01_datacad) ? date('d',strtotime($j01_datacad)) : "" ;
				$j01_datacad_mes = !empty($j01_datacad) ? date('m',strtotime($j01_datacad)) : "" ;
				$j01_datacad_ano = !empty($j01_datacad) ? date('Y',strtotime($j01_datacad)) : "" ;

				db_inputdata('j01_datacad',$j01_datacad_dia,$j01_datacad_mes,$j01_datacad_ano,true,'text',1,"");
				?></td>
			</tr>
		</table>

		<fieldset>
			<table>
				<!-- Processo -->
				<tr>
					<td nowrap title="Processos registrado no sistema?">
						<strong>Processo do Sistema:</strong>
					</td>
					<td nowrap>
						<?php
						$lProcessoSistema = true;
						db_select('lProcessoSistema', array(true => 'SIM', false => 'NÃO'), true, $db_opcao,
							"onchange='js_processoSistema()' style='width: 95px'")
						?>
					</td>
				</tr>

				<tr id="processoSistema">
					<td nowrap title="<?php echo @$Tp58_codproc ?>">
						<strong>
							<?php db_ancora('Processo:', 'js_pesquisaProcesso(true)', $db_opcao); ?>
						</strong>
					</td>
					<td nowrap>
						<?php
						$p58_requer = "";
						if (!empty($j01_processo)) {
							// Busca nome do responsável pelo processo
							$sql = "SELECT p58_requer FROM protprocesso WHERE p58_codproc = $j01_processo";
							$result = db_query($sql);
							db_fieldsmemory($result, 0);
						}
						db_input('j01_processo', 10, false, true, 'text', $db_opcao, 'onchange="js_pesquisaProcesso(false)"');
						db_input('p58_requer', 60, $p58_requer, true, 'text', 3);
						?>
					</td>
				</tr>

				<tr id="processoExterno1" style="display: none;">
					<td nowrap title="Número do processo externo">
						<strong>Processo:</strong>
					</td>
					<td nowrap>
						<?php
						db_input('j01_processoExterno', 10, "", true, 'text', $db_opcao, null, null, null,
							"background-color: rgb(230, 228, 241);");
						?>
					</td>
				</tr>

				<tr id="processoExterno2" style="display: none;">
					<td nowrap title="Número do processo externo">
						<strong>
						Titular do Processo:
						</strong>
					</td>
					<td nowrap>
						<?php db_input('v01_titular', 74, 'false', true, 'text', $db_opcao); ?>
					</td>
				</tr>

				<tr id="processoExterno3" style="display: none;">
					<td nowrap title="Número do processo externo">
						<strong>
						Data do Processo:
						</strong>
					</td>
					<td nowrap>
						<?php
						db_inputdata('v01_dtprocesso', @$v01_dtprocesso_dia, @$v01_dtprocesso_mes, @$v01_dtprocesso_ano, true,
							'text', $db_opcao);
						?>
					</td>
				</tr>
			</table>
		</fieldset>

		<table>
			<!-- INCRA -->
			<tr>

				<td nowrap title="<?php echo @$Tj01_incra?>">
					<label><strong>INCRA:</strong></label>
				</td>
				<td><?php db_input('j01_incra',10,$Ij01_incra,'integer',$db_opcao,"") ?></td>
			</tr>

			<!-- DESCRIÇÃO LOCAL -->
			<tr>

				<td nowrap title="<?php echo @$Tj01_descrlocal?>">
					<label><strong>Descrição Local:</strong></label>
				</td>
				<td><?php db_input('j01_descrlocal',40,$Ij01_descrlocal,'text',$db_opcao,""); ?></td>
			</tr>

		<?php endif ?>


		<?php if ($tipoImovel != "2" && $j01_tipoimovel != "2") : ?>
			<tr>
				<td nowrap title="<?php echo @$Tj04_matricregimo?>"><?php echo @$Lj04_matricregimo ?>
				</td>
				<td><?php db_input('j04_matricregimo',10,$Ij04_matricregimo,true,'text',$db_opcao,""); ?></td>
			</tr>
			<tr>
				<td nowrap title="<?php echo @$Tj04_quadraregimo?>"><?php echo @$Lj04_quadraregimo ?></td>
				<td><?php db_input('j04_quadraregimo',10,$Ij04_quadraregimo,true,'text',$db_opcao,""); ?></td>
			</tr>
			<tr>
				<td nowrap title="<?=@$Tj04_loteregimo?>"><?=@$Lj04_loteregimo?></td>
				<td><?php db_input('j04_loteregimo',10,$Ij04_loteregimo,true,'text',$db_opcao,""); ?></td>
			</tr>
		<?php endif ?>
				
		</table>
		</fieldset>
		</td>
	</tr>

</table>
<center>
<input
	name="<?php echo ($db_opcao==1?"incluir":"alterar")?>" type="submit"
	id="db_opcao"
	value="<?php echo ($db_opcao==1?"Incluir":($db_opcao==2?"Alterar":"Excluir"))?>"
	<?php echo ($db_botao==false?"disabled":"")?> onclick="return js_verinome()"
	>
</center>

<?php

if (!isset($j01_tipoproprietario)) {
    $tipoproprietario = 0;
} else {
    $tipoproprietario = $j01_tipoproprietario;
}
?>
<script>

js_listaTipoProprietario();

    const tipoproprietario = parseInt(<?=$tipoproprietario?>);

    function js_listaTipoProprietario() {
        var oParam = new Object();
        oParam.executa = "lista";

        new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getTipoProprietario).execute();
    }

    function js_getTipoProprietario(oRetorno) {
        if (oRetorno.mensagem != "") {
            alert(oRetorno.mensagem);
        }

        if (oRetorno.erro) {
            return;
        }

        const lista = document.getElementById("listaProprietario");
        lista.innerHTML = "";

        var select = document.createElement("select");
        select.setAttribute("id", "j01_tipoproprietario");
        select.setAttribute("name", "j01_tipoproprietario");
        lista.appendChild(select);

        for (var index = 0; index < oRetorno.lista.length; index++) {
            var option = document.createElement("option");
            option.setAttribute("value", oRetorno.lista[index].j163_tipoproprietario);
            option.setAttribute("id", "j01_tipoproprietario_"+oRetorno.lista[index].j163_tipoproprietario);
            var t = document.createTextNode(oRetorno.lista[index].j163_descricao);

            if (oRetorno.lista[index].j163_descricao == 'Proprietário') {
                option.setAttribute("selected", "selected");
            }

            option.appendChild(t);
            select.appendChild(option);
        }

        if (tipoproprietario != NaN) {
            js_selecionaCampo();
        }
    }

    function js_selecionaCampo()
    {
        const j01_tipoproprietario = document.getElementById("j01_tipoproprietario_"+tipoproprietario);
        j01_tipoproprietario.selected = true;
    }

function js_pesquisa_j107_sequencial(mostra){
  if(mostra==true){
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_condominio','func_condominio.php?funcao_js=parent.js_mostra_j107_sequencial1|j107_sequencial|j107_nome','Pesquisa',true);
  }else{
     if(document.form1.j107_sequencial.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_condominio','func_condominio.php?pesquisa_chave='+document.form1.j107_sequencial.value+'&funcao_js=parent.js_mostra_j107_sequencial','Pesquisa',false);
     }else{
       document.form1.j107_nome.value = '';
     }
  }
}
function js_mostra_j107_sequencial(chave,erro){
  document.form1.j107_nome.value = chave;
  if(erro==true){
    document.form1.j107_sequencial.focus();
    document.form1.j107_sequencial.value = '';
  }else{
  	js_processaRequest();
  }
}
function js_mostra_j107_sequencial1(chave1,chave2){
  document.form1.j107_sequencial.value = chave1;
  document.form1.j107_nome.value       = chave2;
  db_iframe_condominio.hide();
  js_processaRequest();
}

function js_processaRequest(){

  js_divCarregando('Aguarde Processando...','msgCarrega');

  var url = 'cad1_iptubaseRPC.php';
  var oParametro = new Object();
  oParametro.executa = 'buscaCondominio';
  oParametro.j107_sequencial = document.form1.j107_sequencial.value;

  new AjaxRequest(url, oParametro, js_loadSelect).execute();

}

function js_loadSelect(resposta){

  var objJ = resposta.predios;

  if (objJ == "Vazio") {
  	if ($('predios')) {
        $('predios').value = 0 ;
  	}
  	$('sltpredio').innerHTML = "";
  	$('predio').innerHTML = "";

    js_removeObj('msgCarrega');
    return false;
  }

  var objSelect;
  objSelect  = '<select name="predios" id="predios">';
  if( objJ.length > 1){
  	objSelect +='<option value="0" selected>NENHUM</option>';
  }
  for(i = 0; i < objJ.length; i++){
  		objSelect +='<option value="'+ objJ[i].j111_sequencial +'">'+objJ[i].j111_nome.urlDecode()+'</option>';
  }

  objSelect += '</select>';
  $('sltpredio').innerHTML = objSelect;
  $('predio').innerHTML		 = '<b>Prédio:</b>';
  js_removeObj('msgCarrega');

}

function js_pesquisaj01_numcgm(mostra) {
	if (mostra == true) {
		js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase', 'func_nome', 'func_nome.php?testanome=true&funcao_js=parent.js_mostranumcgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
	} else {
		if (document.form1.j01_numcgm.value != '') {
		js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase', 'func_nome', 'func_nome.php?testanome=true&pesquisa_chave=' + document.form1.j01_numcgm.value + '&funcao_js=parent.js_mostranumcgm', 'Pesquisa', false);
		} else {
		document.form1.z01_nome.value = "";
		}
	}
}

function js_mostranumcgm(erro, chave) {
	document.form1.z01_nome.value = chave;
	if (erro == true) {
		document.form1.j01_numcgm.value = '';
		document.form1.j01_numcgm.focus();
	} else {
		js_defineFracaoDefault();
	}
}

function js_mostranumcgm1(chave1, chave2) {
	document.form1.j01_numcgm.value = chave1;
	document.form1.z01_nome.value = chave2;
	js_defineFracaoDefault();
	func_nome.hide();
}

function js_pesquisaj01_idbql(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_lote','func_lote.php?funcao_js=parent.js_mostralote1|0|1','Pesquisa',true,0);
   }else{
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_lote', 'func_lote.php?pesquisa_chave='+document.form1.j01_idbql.value+'&funcao_js=parent.js_mostralote','Pesquisa',false,0);
  }
}
function js_mostralote(chave,erro){
  document.form1.j34_setor.value = chave;
  if(erro==true){
    document.form1.j01_idbql.focus();
    document.form1.j01_idbql.value = '';
  }
}
function js_mostralote1(chave1,chave2){
  document.form1.j01_idbql.value = chave1;
  document.form1.j34_setor.value = chave2;
  db_iframe_lote.hide();
}
function js_pesquisa(){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe', 'func_iptubase.php?funcao_js=parent.js_preenchepesquisa|0','Pesquisa',true,0);

}
function js_preenchepesquisa(chave){
  db_iframe.hide();
  location.href = '<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>'+"?chavepesquisa="+chave;
}

function js_pesquisaj04_setorregimovel(mostra){
  if(mostra==true){
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_setorregimovel','func_setorregimovel.php?funcao_js=parent.js_mostrasetorregimovel1|j69_sequencial|j69_descr','Pesquisa',true);
  }else{
     if(document.form1.j04_setorregimovel.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptubase','db_iframe_setorregimovel','func_setorregimovel.php?pesquisa_chave='+document.form1.j04_setorregimovel.value+'&funcao_js=parent.js_mostrasetorregimovel','Pesquisa',false);
     }else{
       document.form1.j69_descr.value = '';
     }
  }
}
function js_mostrasetorregimovel(chave,erro){
  document.form1.j69_descr.value = chave;
  if(erro==true){
    document.form1.j04_setorregimovel.focus();
    document.form1.j04_setorregimovel.value = '';
  }
}
function js_mostrasetorregimovel1(chave1,chave2){
  document.form1.j04_setorregimovel.value = chave1;
  document.form1.j69_descr.value = chave2;
  db_iframe_setorregimovel.hide();
}


// Funções Distrito
function js_pesquisaj01_distrito(mostra){
	if(mostra==true){
		js_OpenJanelaIframe('','db_iframe_setorfiscal','func_setorfiscal.php?funcao_js=parent.js_mostrasetorfiscal1|j90_codigo|j90_descr','Pesquisa',true,0);
	}else{
		if(document.form1.j01_distrito.value != ''){
			js_OpenJanelaIframe('','db_iframe_setorfiscal','func_setorfiscal.php?pesquisa_chave='+document.form1.j01_distrito.value+'&funcao_js=parent.js_mostrasetorfiscal','Pesquisa',false);
		}else{
			document.form1.j90_descr.value = '';
		}
	}
}
function js_mostrasetorfiscal(chave,erro){
	document.form1.j90_descr.value = chave;
	if(erro==true){
		document.form1.j01_distrito.focus();
		document.form1.j01_distrito.value = '';
	}
}
function js_mostrasetorfiscal1(chave1,chave2){
	document.form1.j01_distrito.value = chave1;
	document.form1.j90_descr.value = chave2;
	db_iframe_setorfiscal.hide();
}

// Funções Processo
function js_pesquisaProcesso(lMostra) {

	var sTitulo = 'Pesquisa Processo';

	if(lMostra) {

	js_OpenJanelaIframe(
		'',
		'db_iframe_matric',
		'func_protprocesso.php?funcao_js=parent.js_mostraProcesso|p58_codproc|z01_nome',
		sTitulo,
		lMostra
	);
	} else {

	js_OpenJanelaIframe(
		'',
		'db_iframe_matric',
		'func_protprocesso.php?pesquisa_chave=' + document.form1.j01_processo.value + '&funcao_js=parent.js_mostraProcessoHidden',
		sTitulo,
		lMostra
	);
	}
}

function js_mostraProcesso(iCodProcesso, sRequerente) {

	document.form1.j01_processo.value = iCodProcesso;
	document.form1.p58_requer.value   = sRequerente;

	db_iframe_matric.hide();
	}

	function js_mostraProcessoHidden(iCodProcesso, sNome, lErro) {

	if(lErro == true) {

	document.form1.j01_processo.value = "";
	document.form1.p58_requer.value  = sNome;
	} else {
	document.form1.p58_requer.value  = sNome;
	}
}

function js_processoSistema() {

	var lProcessoSistema = $F('lProcessoSistema');

	if (lProcessoSistema == 1) {
        document.getElementById('processoExterno1').style.display = 'none';
        document.getElementById('processoExterno2').style.display = 'none';
        document.getElementById('processoExterno3').style.display = 'none';
        document.getElementById('processoSistema').style.display = '';
        $('j01_processo').value = "";
        $('p58_requer').value = "";
	} else {
        document.getElementById('processoExterno1').style.display = '';
        document.getElementById('processoExterno2').style.display = '';
        document.getElementById('processoExterno3').style.display = '';
        document.getElementById('processoSistema').style.display = 'none';

        $('j01_processo').value = "";
        $('j01_processoExterno').value = "";
        $('v01_titular').value = "";
        $('v01_dtprocesso').value = "";
	}
}

function js_calculaFracao(tipo) {
    let area = Number($('j34_area').value);

	if (!area) {
		const urlSearchParams = new URLSearchParams(window.location.search);
		if (urlSearchParams.has('j34_area')) {
			area = urlSearchParams.get('j34_area');
		}
	}

    let fracao = Number($('j01_fracao').value);
    let areaprivativa = Number($('j01_areaprivativa').value);

    if (!area) {
        return;
    }
    
    if (tipo === 'fracao' && fracao > 100) {
        alert("O valor da fração deve ser no máximo 100%!");
        return;
    }

    if (tipo === 'areaprivativa' && areaprivativa > area) {
        alert("A área privativa do lote é maior que a área do lote.");
        return;
    }
    
    let calc = 0;
    if (tipo === 'fracao' && fracao) {
        calc = area * (fracao / 100);
        $('j01_areaprivativa').value = calc.toFixed(2);
    }

	if (tipo === 'areaprivativa') {
        calc = (areaprivativa / area) * 100;
        $('j01_fracao').value = calc.toFixed(2);
    }
}

function js_alteraInscricaoImobiliaria() {
	isPB = '<?php echo isParaiba() ?>';
	if (isPB && $('j40_refant').value.length > 8) {
		let tamUnidade = $('j01_unidade').value.length;
		$('j40_refant').value = $('j40_refant').value.slice(0,-(tamUnidade < 4 ? 4 : tamUnidade)) + ($('j01_unidade').value).padStart(4,'0');
	}
}

function js_defineFracaoDefault() {
	<?php
	if (!isset($_GET['j01_matric'])) {
		echo "document.getElementById('j01_fracaoproprietario').value = '100.0000'";
	}
	?>
}

</script>
