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

//MODULO: issqn
$clcnae->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("q157_descricao");
$clrotulo->label("q178_data_fim");

$db_opcao2 = $db_opcao;
if ($db_opcao==2) {
	$db_opcao2 = 3;
}

?>
<form name="form1" method="post" action="">
<center>
<?php if ($db_opcao==2) { ?>
	<br><br>
<?php } ?>
<fieldset><legend><b>Cadastro de CNAE</b></legend>
<table border="0">
  <tr>
     <td align="left"><b>Tipo:</b></td>
       <td align="left" >
         <?php
           $arraymostra = array("" =>"- Selecione -", "S"=> "Sintética ", "A" => "Analítica ");
           db_select("Tipo",$arraymostra,1,1,"onchange='js_mostraVinculo(this.value)'");
         ?>
      </td>
    </tr>
  <tr>
    <td nowrap title="<?=@$Tq71_sequencial?>">
       <?=@$Lq71_sequencial?>
    </td>
    <td>
<?php
db_input('q71_sequencial',10,$Iq71_sequencial,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tq71_estrutural?>">
       <?=@$Lq71_estrutural?>
    </td>
    <td>
<?php
db_input('q71_estrutural',10,$Iq71_estrutural,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tq71_descr?>">
       <?=@$Lq71_descr?>
    </td>
    <td>
<?php
db_input('q71_descr',80,$Iq71_descr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tq71_aliquota?>">
       <?=@$Lq71_aliquota?>
    </td>
    <td>
        <?php
        db_input('q71_aliquota',10,$Iq71_aliquota,true,'text',$db_opcao,"")
        ?>
    </td>
  </tr>
  <tr id="trVinculo" style="display: none;">
    <td colspan="2">
      <fieldset>
        <legend>Vincular Anexo ao CNAE</legend>
        <?php db_input('q178_sequencial',10,$Iq178_sequencial,true,'text',3,"", "", "", "display:none;") ?>
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
            <td nowrap title="<?php echo $Tq178_data_fim; ?>">
              <?php echo $Lq178_data_fim; ?>
            </td>
            <td>
              <?php db_inputdata('q178_data_fim', @$q178_data_fim_dia, @$q178_data_fim_mes, @$q178_data_fim_ano, true, 'text', $db_opcao); ?>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  </table>
  </center>
 </fieldset>
<br>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
                 type="submit" id="db_opcao"
                 onclick="return js_validaTipo()"
                 value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
                 <?=($db_botao==false?"disabled":"")?> >
                 <?php
                 if ($db_opcao != 1) :
                 ?>
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >

<input name="novoVinculo" type="button" id="novoVinculo" value="Novo Vínculo" onclick="js_novoVinculo();" <?php echo $db_opcao == 22 ? 'disabled="true"' : '';?> >
                 <?php endif;?>

  <?php
	if ($q71_sequencial != NULL) {
		$anousu = db_getsession('DB_anousu');
		$sql = "SELECT * FROM issqn.isscnaeanexos
				INNER JOIN issqn.cnae ON q178_cnae = q71_sequencial
				INNER JOIN issqn.cnaeanalitica ON q72_cnae = q71_sequencial
				INNER JOIN issqn.issgscadanexos ON q178_issgscadanexos = q157_sequencial
				WHERE q71_sequencial = $q71_sequencial
				ORDER BY q178_data_fim DESC
        ";

		$result = db_query($sql);
        $num = pg_numrows($result);
		if ($num > 0) {
		?>

		<br><br>
		<fieldset id="fieldsetListaVinculo" style="display: none;">
			<legend>Anexos vinculados ao CNAE</legend>
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
						if (!empty($q178_data_fim)) {
							// Formata data para mostrar na tabela de vínculos
							$data_fim = strtotime($q178_data_fim);
							$data_fim = date('d/m/Y',$data_fim);

							$q178_data_fim = strtotime($q178_data_fim);
						} else {
							$data_fim = "Sem Data";
							$q178_data_fim = "";
						}
					?>
					<tr class="cores">
						<td class="text-center field-size1"><?=$q71_estrutural?></td>
						<td class="text-left field-size6"><?=$q71_descr?></td>
						<td class="text-left"><?=$q157_descricao?></td>
						<td class="text-left"><?=$data_fim?></td>
						<td class="text-center">
							<input id="numcgm" name="numcgm" type="hidden" value="<?=$j42_numcgm?>">
							<input name="alterar" type="button" href="#" title="Alterar vínculo." onclick="js_alterar(<?=$q178_sequencial?>, <?=$q178_issgscadanexos?>, <?=$q178_data_fim?>)" value="A"> |
							<!-- <input name="excluir" id="excluir" type="submit" title="Excluir vínculo." value="E"> -->
							<input name="excluir" type="button" title="Excluir vínculo." onclick="return js_excluir(<?=$q178_sequencial?>)" value="E">
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</fieldset>
	<?php
		}
	}
	?>

</form>
<script>

function js_validaTipo(){
  if( document.getElementById('Tipo').value == '' ){
    alert('É preciso informar o tipo de cadastro para o CNAE');
    return false;
  }
}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cnae','func_cnae.php?funcao_js=parent.js_preenchepesquisa|q71_sequencial|q72_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave, chave2){
  db_iframe_cnae.hide();

  <?php
  if($db_opcao != 1) {
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave+'&chave2='+chave2";
  }
  ?>
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
	select.setAttribute("name","q178_issgscadanexos");
	select.setAttribute("id","selectanexo");
	option.value = 0;
	option.appendChild(document.createTextNode(descricao));
	select.appendChild(option);
	lista.appendChild(select);

	for (var index = 0; index < oRetorno.lista.length; index++) {
		const descricao = oRetorno.lista[index].q157_descricao;
		const option = document.createElement("option");

		option.value = oRetorno.lista[index].q157_sequencial;
		option.appendChild(document.createTextNode(descricao));
		select.appendChild(option);
		lista.appendChild(select);
	}

}


function js_novoVinculo() {
	obj = document.form1;
	obj.q178_sequencial.value = '';
	obj.selectanexo.value = 0;
	obj.q178_data_fim.value = '';
}

function js_alterar(sequencial, anexo, datafim) {
	obj = document.form1;
	obj.q178_sequencial.value = sequencial;
	obj.selectanexo.value = anexo;

	if (datafim === undefined) {
		obj.q178_data_fim.value = "";
	} else {
		var data = new Date(datafim * 1000);
		obj.q178_data_fim.value = data.getDateBR();
	}

}

// Confirm de exclusão de submit
const excluir = document.getElementById('excluir');

if (excluir) {
    excluir.onclick = function() {
        if (confirm('Deseja excluir o vínculo?')) {
            obj = document.form1;
            obj.q178_sequencial.value = sequencial;

            return true;
        } else {
            return false;
        }
    }
}

function js_excluir(sequencial) {

	obj = document.form1;
	obj.q178_sequencial.value = sequencial;

	if (confirm("Deseja excluir o vínculo ?")) {
		var oParam = new Object();
		oParam.q178_sequencial = sequencial;
		oParam.executa = "excluirVinculoCnae";

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

function js_mostraVinculo(sTipo)
{
    if(sTipo == 'A'){
        document.getElementById('trVinculo').show();
    } else {
        document.getElementById('trVinculo').hide();
    }
}
</script>
