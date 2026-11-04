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

//MODULO: cadastro
require_once(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$claverbacgm->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j75_codigo");
$clrotulo->label("z01_nome");

$cltipoproprietario = new cl_tipoproprietario;
$cltipoproprietario->rotulo->label();
$cltipoproprietario->rotulo->tlabel();

$cltipopromitente = new cl_tipopromitente;
$cltipopromitente->rotulo->label();
$cltipopromitente->rotulo->tlabel();

if (isset($db_opcaoal)) {
	$db_opcao = 33;
	$db_botao = false;
} else if (isset($opcao) && $opcao == "alterar") {
	$db_botao = true;
	$db_opcao = 2;
} else if (isset($opcao) && $opcao == "excluir") {
	$db_opcao = 3;
	$db_botao = true;
} else {
	$db_opcao = 1;
	$db_botao = true;
	if (isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && $sqlerro == false)) {
		$j76_codigo = "";
		$j76_numcgm = "";
		$z01_nome = "";
		$j76_tipo = "";
		$j76_principal = "";
	}
}
$result = $claverbacao->sql_record($claverbacao->sql_query_file($j76_averbacao));
db_fieldsmemory($result, 0);
if ($j75_situacao == 2) {
	$db_opcao = 3;
	$db_botao = false;
}
?>
<div class="container">
	<form name="form1" method="post" action="">
		<input type="hidden" id="propri" value="<?= $j76_tipoproprietario ?>">
		<input type="hidden" id="promi" value="<?= $j76_tipopromitente ?>">
		<fieldset>
			<legend>Cgm</legend>
			<table class="form-container">
				<tr>
					<td nowrap title="<?php //=@$Tj76_codigo
										?>">
						<?php //=@$Lj76_codigo
						?>
					</td>
					<td>
						<?php
						db_input('j76_codigo', 6, $Ij76_codigo, true, 'hidden', 3, "")
						?>
					</td>
				</tr>
				<tr>
					<td nowrap title="<?= @$Tj76_averbacao ?>">
						<?php
						db_ancora(@$Lj76_averbacao, "js_pesquisaj76_averbacao(true);", 3);
						?>
					</td>
					<td>
						<?php
						db_input('j76_averbacao', 6, $Ij76_averbacao, true, 'text', 3, " onchange='js_pesquisaj76_averbacao(false);'")
						?>
					</td>
				</tr>
				<tr>
					<td nowrap title="<?= @$Tj76_numcgm ?>">
						<?php
						db_ancora(@$Lj76_numcgm, "js_pesquisaj76_numcgm(true);", $db_opcao);
						?>
					</td>
					<td>
						<?php
						db_input('j76_numcgm', 10, $Ij76_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisaj76_numcgm(false);'")
						?>
						<?php
						db_input('z01_nome', 50, $Iz01_nome, true, 'text', 3, '')
						?>
					</td>
				</tr>
				<?php
				if ($j75_regra == 1) {
				?>
					<tr>
						<td nowrap title="<?= @$Tj163_descricao ?>">
							<?= @$Lj163_descricao ?>
						</td>
						<td id="listaProprietario"></td>
					</tr>
				<?php
				} else if ($j75_regra == 2) {
				?>
					<tr>
						<td nowrap title="<?= @$Tj164_descricao ?>">
							<?= @$Lj164_descricao ?>
						</td>
						<td id="listaPromitente"></td>
					</tr>
				<?php
				}
				?>

				<tr>
					<td nowrap title="<?= @$Tj76_principal ?>">
						<?= @$Lj76_principal ?>
					</td>
					<td>

						<?php
						$x = array("t" => "Sim", "f" => "Não");
						db_select('j76_principal', $x, true, $db_opcao, "");
						?>

					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" id="verificaPrincipal" name="verificaPrincipal" value="<?=$verificaPrincipal?>">
					</td>
				</tr>
                <tr>
                    <td>
                        <input type="hidden" id="verificaRegra" name="verificaRegra" value="<?=$j75_regra?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" id="tipoProprietarioPromitente" name="tipoProprietarioPromitente" value="<?= $j164_tipopromitente ? $j164_tipopromitente : $j163_tipoproprietario?>">
                    </td>
                </tr>
			</table>
		</fieldset>
		<input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?>>
		<input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?= ($db_opcao == 1 || isset($db_opcaoal) ? "style='visibility:hidden;'" : "") ?>>
		<br /><br />
		<table>
			<tr>
				<td valign="top" align="center">
					<?php
					$chavepri = array("j76_codigo" => @$j76_codigo);
					$cliframe_alterar_excluir->chavepri = $chavepri;
					$cliframe_alterar_excluir->sql     = $claverbacgm->sql_query(null, "j76_codigo,j76_numcgm,z01_nome,j76_principal,j163_descricao as j76_tipoproprietario,j164_descricao as j76_tipopromitente", null, "j76_averbacao=$j76_averbacao");
					if ($j75_regra == 1) {
						$cliframe_alterar_excluir->campos  = "j76_codigo,j76_numcgm,z01_nome,j76_principal,j76_tipoproprietario";
					} elseif ($j75_regra == 2) {
						$cliframe_alterar_excluir->campos  = "j76_codigo,j76_numcgm,z01_nome,j76_principal,j76_tipopromitente";
					}
					$cliframe_alterar_excluir->legenda = "ITENS LANÇADOS";
					$cliframe_alterar_excluir->iframe_height = "160";
					$cliframe_alterar_excluir->iframe_width = "700";

					$cliframe_alterar_excluir->iframe_alterar_excluir(1);
					?>
				</td>
			</tr>
		</table>
	</form>
</div>
<script>
const opcao = document.getElementById('db_opcao').value;

// Desabilita select 'Principal' caso já houver um CGM principal
const principal = document.getElementById("verificaPrincipal");
if (principal.value == 'false') {
  const selectPrincipal = document.getElementById("j76_principal");
  selectPrincipal.disabled = true;
      selectPrincipal.value = 'f';
  }

//CHAMADAS DE MÉTODOS
  const regra = document.getElementById('verificaRegra').value;
if(regra == 1){
      js_listaTipoProprietario();
  }else{
  js_listaTipoPromitente();
}

//LISTAS
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

  var select = document.createElement("select");
  select.setAttribute("id", "j76_tipoproprietario");
  select.setAttribute("name", "j76_tipoproprietario");
  select.setAttribute("style", "width:200px");

  // Se clicado no E de excluir na grid
      if(opcao == 'Excluir'){
          select.setAttribute("disabled", "disabled");
      }

  for (var index = 0; index < oRetorno.lista.length; index++) {
    var option = document.createElement("option");
    option.setAttribute("value", oRetorno.lista[index].j163_tipoproprietario);
    var t = document.createTextNode(oRetorno.lista[index].j163_descricao);

    option.appendChild(t);

          if(opcao == 'Alterar'){
              const tipo_promitente_proprietario = document.getElementById('tipoProprietarioPromitente').value;
              if (tipo_promitente_proprietario == oRetorno.lista[index].j163_tipoproprietario) {
                  option.setAttribute("selected", "selected");
              }
          }

    select.appendChild(option);
  }
  lista.appendChild(select);
}

  function js_getTipoPromitente(oRetorno) {
      if (oRetorno.mensagem != "") {
          alert(oRetorno.mensagem);
      }

      if (oRetorno.erro) {
          return;
      }

      const lista = document.getElementById("listaPromitente");

      var select = document.createElement("select");
      select.setAttribute("id", "j76_tipopromitente");
      select.setAttribute("name", "j76_tipopromitente");
      select.setAttribute("style", "width:200px");

      // Se clicado no E de excluir na grid
      if(opcao == 'Excluir'){
          select.setAttribute("disabled", "disabled");
      }

      for (var index = 0; index < oRetorno.lista.length; index++) {
          var option = document.createElement("option");
          option.setAttribute("value", oRetorno.lista[index].j164_tipopromitente);
          var t = document.createTextNode(oRetorno.lista[index].j164_descricao);

          option.appendChild(t);

          if(opcao == 'Alterar'){
              const tipo_promitente_proprietario = document.getElementById('tipoProprietarioPromitente').value;
              if (tipo_promitente_proprietario == oRetorno.lista[index].j164_tipopromitente) {
                  option.setAttribute("selected", "selected");
              }
          }

          select.appendChild(option);
      }

      lista.appendChild(select);

  }

function js_listaTipoPromitente() {
  var oParam = new Object();
  oParam.executa = "buscarVinculo";
  oParam.j41_matric = parseInt(<?= $j75_matric ?>);

  new AjaxRequest("cad1_tipopromitente.RPC.php", oParam, js_getTipoPromitente).execute();
}

$("j76_averbacao").addClassName('field-size2');
$("j76_numcgm").addClassName('field-size2');
$("j76_principal").setAttribute('rel', 'ignore-css');
$("j76_principal").addClassName('field-size2');

function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}
function js_pesquisaj76_averbacao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_averbacgm','db_iframe_averbacao','func_averbacao.php?funcao_js=parent.js_mostraaverbacao1|j75_codigo|j75_codigo','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.j76_averbacao.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_averbacgm','db_iframe_averbacao','func_averbacao.php?pesquisa_chave='+document.form1.j76_averbacao.value+'&funcao_js=parent.js_mostraaverbacao','Pesquisa',false);
     }else{
       document.form1.j75_codigo.value = ''; 
     }
  }
}
function js_mostraaverbacao(chave,erro){
  document.form1.j75_codigo.value = chave; 
  if(erro==true){ 
    document.form1.j76_averbacao.focus(); 
    document.form1.j76_averbacao.value = ''; 
  }
}
function js_mostraaverbacao1(chave1,chave2){
  document.form1.j76_averbacao.value = chave1;
  document.form1.j75_codigo.value = chave2;
  db_iframe_averbacao.hide();
}
function js_pesquisaj76_numcgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_averbacgm','db_iframe_cgm','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome&testanome=1','Pesquisa');
  }else{
     if(document.form1.j76_numcgm.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_averbacgm','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.j76_numcgm.value+'&funcao_js=parent.js_mostracgm&testanome=1','Pesquisa',false);
     }else{
       document.form1.z01_nome.value = ''; 
     }
  }
}
function js_mostracgm(erro,chave){
  document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.j76_numcgm.focus(); 
    document.form1.j76_numcgm.value = ''; 
  }
}
function js_mostracgm1(chave1,chave2){
  document.form1.j76_numcgm.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_cgm.hide();
}
</script>
