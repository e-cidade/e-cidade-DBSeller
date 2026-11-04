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

$clrotulo = new rotulocampo();

$clrotulo->label("it06_matric");
$clrotulo->label("it04_descr");
$clrotulo->label("j40_refant");

$clitbi->rotulo->label();
$clitbidadosimovel->rotulo->label();
$clitbiavalia->rotulo->label();

$tipo  		 = $oGet->tipo;

if ( $tipo == "urbano") {
  $sPrefix     = "do ";
  $sTerraLabel = "Terreno";
  $sMedida     = "m²";
} else {
  $sPrefix     = "da ";
  $sTerraLabel = "Terra";
  $sMedida     = "ha";
}

if (isset($it14_guia) && !empty($it14_guia)) {
  $db_botao = true;
}
?>
<form name="form1" method="post" action="" id="frm1">
  <table>
    <tr>
      <td>
          <fieldset>
            <legend>
              <b>Liberação de ITBI</b>
            </legend>
            <table>
          <tr>
          <td title="<?=@$Tit14_guia?>">
            <?=@$Lit14_guia?>
          </td>
          <td>
          <?php
            db_input('it14_guia',10,$Iit14_guia,true,'text',3," onchange='js_pesquisait14_guia(false);'");
            db_input('tipo',10,"",true,'hidden',3);
            db_input('listaFormas',10,"",true,'hidden',3);
            db_input('desconto_avalia',10,"",true,'hidden',3);
          ?>
          </td>
          <td>
            <?=@$Lit01_data?>
          </td>
          <td>
            <?php
            db_inputdata('it01_data',@$it01_data_dia,@$it01_data_mes,@$it01_data_ano,true,'text',3,"");
            ?>
          </td>
          <td>
            <?=@$Lit01_id_usuario?>
          </td>
          <td>
            <?php
            db_input('it01_id_usuario',10,"",true,'hidden',3,"");
            db_input('nome',50,"",true,'text'  ,3,"");
            ?>
          </td>
        </td>
    </tr>

    <!-- PROCESSO -->
        <tr>
            <td title="<?=@$Tit01_processo?>">
                <?=@$Lit01_processo?>
            </td>
            <td colspan="6">
                <?php
                    db_input('it01_processo',10,$Lit01_processo,true,'text',3);
                    db_input('it01_tituprocesso',50,"",true,'text'  ,3,"");
                ?>
            </td>
        </tr>

        <?php if (!empty($it01_dtprocesso)) : ?>
            <tr>
                <td title="<?=@$Tit01_processo?>">
                    <?=@$Lit01_dtprocesso?>
                </td>
                <td>
                    <?php
                        db_inputdata('it01_dtprocesso',@$it01_dtprocesso_dia,@$it01_dtprocesso_mes,@$it01_dtprocesso_ano,true,'text',3,"");
                    ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
                <td>
                  <strong>Tipo:</strong>
                </td>
                <td>
                  <select name="codigoTipoTaxa" id="tipoTaxa" onchange="js_buscarTaxaTipo(this.value)">
                    <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                  </select>
                </td>
              </tr>
        <tr>
            <td>
                <strong>Cartório:</strong>
            </td>
            <td colspan="6">
                <?php
                    db_input('j167_descricao',50,"",true,'text'  ,3,"");
                ?>
            </td>
        </tr>

			<tr>
			  <td colspan="6">
			    <fieldset>
			      <legend>
			        <b>Identificação do Imóvel:</b>
			      </legend>
			      <table>
			        <tr>
			          <td>
			            <b>Matrícula RI:</b>
			          </td>
			          <td>
			            <?php
				  	 	  db_input('it22_matricri',10,$Iit22_matricri,true,'text',3,"");
			            ?>
			          </td>
			          <?php if ( $tipo == "urbano" ) {?>
			          <td>
			            <b>Matrícula:</b>
			          </td>
			          <td>
			            <?php
				  	 	  db_input('it06_matric',10,$Iit06_matric,true,'text',3,"");
			            ?>
			            <input type="button" name="verMatric" value="Ver" onClick="js_verMatric();" <?=($db_botao==false?"disabled":"")?>/>
			          </td>
			          <td>
                  <?=$Lj40_refant?>
			          </td>
			          <td align="right">
			            <?php
				  	 	  db_input('j40_refant',25,"",true,'text',3,"");
			            ?>
			          </td>
			          <?php } else { ?>
			          <td>
			            <b>Distância da Cidade:</b>
			          </td>
			          <td align="right" colspan="3">
			            <?php
				  	 	  db_input('it18_distcidade	',10,"",true,'text',3,"");
			            ?>
			            <b>Km</b>
			          </td>
			          <?php }?>
			        </tr>
			        <tr>
			          <td>
			            <b>Setor/Bairro:</b>
			          </td>
			          <td colspan="5">
			            <?php
				  	 	  db_input('it22_setor',100,"",true,'text',3,"");
			            ?>
			          </td>
			        </tr>
			        <tr>
			          <td>
			            <b>Logradouro:</b>
			          </td>
			          <td colspan="5">
			            <?php
				  	 	  db_input('it22_descrlograd',100,"",true,'text',3,"");
			            ?>
			          </td>
			        </tr>
					<?php if ( $tipo == "urbano" ) {?>
					<tr>
					  <td>
					    <?=@$Lit22_numero?>
					  </td>
					  <td>
					    <?php
					  	  db_input('it22_numero',20,$Iit22_numero,true,'text',3,"");
						?>
					  </td>
					  <td align="right" colspan="2">
					    <?=@$Lit22_compl?>
					  </td>
					  <td align="right" colspan="2">
					    <?php
						  db_input('it22_compl',20,$Iit22_compl,true,'text',3,"");
						?>
					  </td>
				  	</tr>
					<tr>
					  <td>
					    <?=@$Lit22_quadra?>
					  </td>
					  <td>
					    <?php
					   	  db_input('it22_quadra',20,$Iit22_quadra,true,'text',3,"");
						?>
					  </td>
					  <td align="right" colspan="2">
					    <?=@$Lit22_lote?>
					  </td>
					  <td align="right" colspan="2">
						<?php
						  db_input('it22_lote',20,$Iit22_lote,true,'text',3,"");
						?>
					  </td>
					</tr>
					<?php } ?>
                    <tr>
                      <td>
                        <b>Área:</b>
                      </td>
                      <td>
                        <?php
                          db_input('it01_areaterreno',20,$Iit01_areaterreno,true,'text',3,"");
                        ?>
                        <b><?=$sMedida?></b>
                      </td>
                      <td  align="right" colspan="2">
                        <b>Área Transmitida:</b>
                      </td>
                      <td  align="right" colspan="2">
                        <?php
                          db_input('it01_areatrans',20,$Iit01_areatrans,true,'text',3,"");
                        ?>
                        <b><?=$sMedida?></b>
                      </td>
                    </tr>
			        <tr>
			          <td>
			            <b>Transmitente Princ:</b>
			          </td>
			          <td colspan="5">
			            <?php
				  	 	  db_input('transmitenteprinc',100,"",true,'text',3,"");
			            ?>
			          </td>
			        </tr>
			        <tr>
			          <td>
			            <b>Adquirente Princ:</b>
			          </td>
			          <td colspan="5">
			            <?php
				  	 	  db_input('adquirenteprinc',100,"",true,'text',3,"");
			            ?>
			          </td>
			        </tr>
			      </table>
			    </fieldset>
			  </td>
			</tr>
			<tr>
			  <td colspan="6">
			    <fieldset>
			      <legend>
			        <b>Valores Declarados:</b>
			      </legend>
			      <table>
					<tr>
					  <td title="<?=@$Tit01_tipotransacao?>">
					    <?=@$Lit01_tipotransacao?>
					  </td>
					  <td colspan="5">
						<?php
						  db_input('it01_tipotransacao',20,"",true,'hidden',3);
						  db_input('it04_descr',100,$Iit04_descr,true,'text',3,'');
					    ?>
					  </td>
					</tr>
					<tr>
					  <td>
					    <b>Valor  <?=$sPrefix.$sTerraLabel?>:</b>
					  </td>
					  <td>
					    <?php
					      db_input('it01_valorterreno',15,$Iit01_valorterreno,true,'text',3, "", "", "", "text-align:right");
					    ?>
					  </td>
					  <td>
					    <b>Valor das Benfeitorias:</b>
					  </td>
					  <td>
					    <?php
					      db_input('it01_valorconstr',15,$Iit01_valorconstr,true,'text',3, "", "", "", "text-align:right");
					    ?>
					  </td>
					  <td>
					    <b>Valor Total:</b>
					  </td>
					  <td align="right">
						<?php
						  db_input('it01_valortransacao',15,$Iit01_valortransacao,true,'text',3, "", "", "", "text-align:right");
					    ?>
					  </td>
					</tr>
					<tr>
					  <td colspan="6">
					    <div id="listaFormasPgto"></div>
					  </td>
					</tr>
			      </table>
			    </fieldset>
			  </td>
      </tr>

      <?php if(!((isset($it05_guia) && trim($it05_guia)) && $oParam->it24_comparavaloresavaliacao === 't') || $oParam->it24_comparavaloresavaliacao === 'f') : ?>
        <tr align="center">
          <td colspan="6">
            <input type="button" name="concordaValores" value="Concordar com Valores" onClick="js_concordaValores();" <?=($db_botao==false?"disabled":"")?>>
          </td>
        </tr>
      <tr>
    <?php endif; ?>

    <?php if((isset($it05_guia) && trim($it05_guia)) && $oParam->it24_comparavaloresavaliacao === 't') : ?>

	          <td colspan="6">
	            <fieldset>
	              <legend>
	                <b>Valores Venais</b>
	              </legend>
	              <table>
                <tr>
                <td colspan="1">
                  <strong>Valor  <?=$sPrefix.$sTerraLabel?>:</strong>
                </td>
                <td colspan="1" width="165px">
                  <?php
                    db_input('j23_vlrter',20,$Ij23_vlrter,true,'text',3,"onchange='js_validaValores(this)'", "", "", "text-align:right");
                  ?>
                </td>
                <td colspan="1" width="130px">
                  <strong>Valor das Benfeitorias:</strong>
                </td>
                <td colspan="1" width="165px">
                  <?php
                    db_input('j22_valor',20,$Iit01_valorconstr,true,'text',3,"onchange='js_validaValores(this)'", "", "", "text-align:right");
                  ?>
                </td>
                <td colspan="1" width="63px">
                  <strong>Valor Total:</strong>
                </td>
                <td colspan="1">
                  <?php
                    db_input('valorVenalTotal',20,$Iit01_valortransacao,true,'text',3,"onchange='js_validaValores(this)'", "", "", "text-align:right");
                  ?>
                </td>
              </tr>
	              </table>
	            </fieldset>
	          </td>
	        </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <?php endif; ?>
			<tr>
			  <td colspan="6">
			    <fieldset>
			      <legend>
			        <b>Avaliação:</b>
			      </legend>
			      <table>
					<tr>
					  <td title="<?=@$Tit01_tipotransacao?>">
			      		<?php
			        	  db_ancora(@$Lit01_tipotransacao,"js_pesquisait01_tipotransacao(true);",3);
			      		?>
					  </td>
					  <td colspan="5">
						<?php
						  db_input('it01_tipotransacao_avalia',20,"",true,'hidden',3);
						  db_input('it04_descr_avalia',100,$Iit04_descr,true,'text',3,'');
					    ?>
					  </td>
					</tr>
					<tr>
					  <td>
					    <b>Valor <?=$sPrefix.$sTerraLabel?>:</b>
					  </td>
					  <td>
					    <?php
					      db_input('it01_valorterreno_avalia',15,$Iit01_valorterreno,true,'text',$db_opcao_avaliados,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "text-align:right", 50, true);
					    ?>
					  </td>
					  <td>
					    <b>Valor das Benfeitorias:</b>
					  </td>
					  <td>
					    <?php
					      db_input('it01_valorconstr_avalia',15,$Iit01_valorconstr,true,'text',$db_opcao_avaliados,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "text-align:right", 50, true);
					    ?>
					  </td>
					  <td>
					    <b>Valor Total:</b>
					  </td>
					  <td align="right">
						<?php
						  db_input('it01_valortransacao_avalia',15,$Iit01_valortransacao,true,'text',$db_opcao_avaliados,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "text-align:right", 50, true);
					    ?>
					  </td>
					</tr>
					<tr>
					  <td colspan="6">
					    <div id="listaFormasPgtoAvalia"></div>
					  </td>
                    </tr>
                    <tr>
                        <td colspan="6">

                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <fieldset>
                                <legend>
                                    <strong>Taxas</strong>
                                </legend>
                                <div id="ctnGridTaxas"></div>
                            </fieldset>
                        </td>
                    </tr>
			        <tr>
					  <td>
					    <b>Valor do Imposto R$:</b>
					  </td>
					  <td>
					    <?php
					      db_input('imposto_avalia',15,"",true,'text',3, "", "", "", "text-align:right");
					    ?>
                      </td>
                      <td>
                          <b>Valor das Taxas R$:</b>
                      </td>
                      <td>
                        <?php
					      db_input('taxas_avalia',15,"",true,'text',3, "", "", "", "text-align:right");
					    ?>
                      </td>
                      <td>
                          <b>Valor Total R$:</b>
                      </td>
                      <td>
                        <?php
					      db_input('total_avalia',15,"",true,'text',3, "", "", "", "text-align:right");
					    ?>
                      </td>
                    </tr>
                    <tr>
					  <td>
					    <b>Vencimento:</b>
					  </td>
					  <td>
						<?php
				 		  db_inputdata('it14_dtvenc',@$it14_dtvenc_dia,@$it14_dtvenc_mes,@$it14_dtvenc_ano,true,'text',$db_opcao,"");
					    ?>
					  </td>
			        </tr>
			      </table>
			    </fieldset>
			  </td>
      </tr>
	        <tr>
	          <td colspan="6">
	            <fieldset>
	              <legend>
	                <b>Observações</b>
	              </legend>
	              <table>
				    <tr>
				      <td>
	 			        <?php
						  db_textarea('it14_obs',3,120	,$Iit01_obs,true,'text',$db_opcao,"");
				        ?>
				      </td>
				    </tr>
	              </table>
	            </fieldset>
	          </td>
	        </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr align="center">
      <td colspan="6">
		 <input name="liberar"   type="submit" id="liberar"   value="Liberar Guia" <?=(($db_botao==false OR !$habilitaBotaoLiberar)?"disabled":"")?> onClick=" return js_validaCampos();">
		 <input name="visualizar" type="button" id="visualizar" value="Visualizar Guia" onclick="js_visualizar(<?php echo !empty($it14_guia) ? $it14_guia : ''; ?>);" <?=($db_botao==false?"disabled":"")?>>
         <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
         <?php //dd($oParam->it24_solicitanotificacao == "t" AND !$habilitaBotaoLiberar); ?>
         <?php if ($oParam->it24_solicitanotificacao == "t" AND !$habilitaBotaoLiberar) : ?>
            <input type="submit" name="notificacao" value="Notificação Enviada">
         <?php  endif; ?>
      </td>
    </tr>
  </table>
  <input type="hidden" name="aTaxas" id="aTaxas">
</form>
<script>
document.getElementById("imposto_avalia").value = 0;
document.getElementById("taxas_avalia").value = 0;

function js_visualizar(guia) {
  var iGuia  = guia;
  var sParam = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height="+
                (screen.height-100)+",width="+(screen.width-100);
  window.open('reciboitbi.php?itbi='+iGuia,"",sParam);
}

js_buscaTipos();
function js_buscaTipos()
{
  const tipo = document.getElementById("tipo").value;
  const tipoTaxa = document.getElementById("tipoTaxa");

  var oParam = new Object();
  oParam.executa = "listarTipos";
  oParam.tipo = tipo;

  new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
      if (oRetorno.erro) {
        alert(oRetorno.mensagem);
        return;
      }

      const aTipos = oRetorno.aTipos;

      aTipos.forEach(function (oTipo){
        const option = document.createElement("option");
        option.setAttribute("value", oTipo.it36_sequencial);
        option.innerHTML = oTipo.it36_descricao;
        if (aTipos.length == 1 || oTipo.it36_sequencial == <?= ((isset($oDadosTaxaGuia->it38_taxasitbi) AND !empty($oDadosTaxaGuia->it38_taxasitbi)) ? $oDadosTaxaGuia->it38_taxasitbi : 0) ?>) {
            option.setAttribute("selected", "selected");
            js_buscarTaxaTipo(oTipo.it36_sequencial);
        }

        tipoTaxa.appendChild(option);
      });
  }).execute();
}

function js_somaValores(){


  var aObjGrid 	    = gridFormasPgtoAvalia.getSelection("object");
  var nTotalImposto = 0;

  for ( var iInd=0; iInd < aObjGrid.length; iInd++ ) {

	var nValorAliquota    = js_strToFloat(aObjGrid[iInd].aCells[2].getValue());
	var nValorForma       = new Number(aObjGrid[iInd].aCells[3].getValue().replaceAll(".", "").replace(",", "."));
	var nValorImposto     = nValorForma * ( nValorAliquota / 100 );
	var nValorDescImposto = nValorImposto * ( document.form1.desconto_avalia.value / 100 );
			nValorImposto     = nValorImposto - nValorDescImposto;
			nTotalImposto     = nTotalImposto + nValorImposto;

  }

  document.form1.imposto_avalia.value = nTotalImposto.toLocaleString('pt-BR', {maximumFractionDigits: 2});

  const imposto_avalia = document.form1.imposto_avalia.value.replaceAll(".", "").replace(",", ".");
  const taxas_avalia = document.form1.taxas_avalia.value.replaceAll(".", "").replace(",", ".");

  const valorTotal = parseFloat(imposto_avalia) + parseFloat(taxas_avalia);

  document.form1.total_avalia.value = valorTotal.toLocaleString('pt-BR', {maximumFractionDigits: 2});
}


function js_validaCampos(){

  var aObjFormasPgto = js_getElementbyClass(document.all,'formasPgto');
  var sQuery 		 = "";

  if (aObjFormasPgto.length == 0) {

    alert('Nenhuma forma de pagamento informada!')
    return false;

  } else {

    var sPrefix = "";
    for ( var iInd=0; iInd < aObjFormasPgto.length; iInd++ ) {
      sQuery += sPrefix+aObjFormasPgto[iInd].id+"X"+aObjFormasPgto[iInd].value.replaceAll(".", "").replace(",", ".");
      sPrefix = "|";
    }

    document.form1.listaFormas.value = sQuery;

  }

  //aqui
  const aInputs = document.querySelectorAll("[isTaxa='true']");
  const aTaxas = [];

  aInputs.forEach(function (oInput){
      const oTaxa = new Object();
      oTaxa.codigo = oInput.getAttribute("codigoTaxa");
      oTaxa.calculaSobre = oInput.getAttribute("calculaSobre");
      oTaxa.aliquota = oInput.getAttribute("aliquota");
      oTaxa.tipo = oInput.getAttribute("tipo");
      oTaxa.valor = oInput.innerHTML.replaceAll(".", "").replace(",", ".");

      aTaxas.push(oTaxa);
  });

  document.getElementById("aTaxas").value = JSON.stringify(aTaxas);

  const it01_valorterreno_avalia = document.getElementById("it01_valorterreno_avalia");
  const it01_valorconstr_avalia = document.getElementById("it01_valorconstr_avalia");
  const it01_valortransacao_avalia = document.getElementById("it01_valortransacao_avalia");

  it01_valorterreno_avalia.value = it01_valorterreno_avalia.value.replaceAll(".", "").replace(",", ".");
  it01_valorconstr_avalia.value = it01_valorconstr_avalia.value.replaceAll(".", "").replace(",", ".");
  it01_valortransacao_avalia.value = it01_valortransacao_avalia.value.replaceAll(".", "").replace(",", ".");
}



function js_validaValores(obj){

  var sNomeCampo		= obj.name;
  var doc				= document.form1;
  var nValorTotal 	    = new Number(doc.it01_valortransacao_avalia.value.replaceAll(".", "").replace(",", "."));
  var nValorTerreno 	= new Number(doc.it01_valorterreno_avalia.value.replaceAll(".", "").replace(",", "."));
  var nValorBenfeitoria = new Number(doc.it01_valorconstr_avalia.value.replaceAll(".", "").replace(",", "."));

  if ( nValorTerreno != 0 || nValorBenfeitoria != 0 ) {
	doc.it01_valortransacao_avalia.disabled = true;
    doc.it01_valortransacao_avalia.value    = new Number(nValorTerreno + nValorBenfeitoria).toLocaleString('pt-BR', {minimumFractionDigits: 2});
  } else if ( nValorTerreno == 0 && nValorBenfeitoria == 0 && sNomeCampo == "it01_valortransacao_avalia" && nValorTotal != 0) {
    doc.it01_valorterreno_avalia.disabled   = true;
    doc.it01_valorconstr_avalia.disabled    = true;
  } else if ( nValorTerreno == 0 && nValorBenfeitoria == 0 && sNomeCampo != "it01_valortransacao_avalia" <?= ($oParam->it24_comparavaloresavaliacao == "t" ? " && false " : "") ?>) {
    doc.it01_valortransacao_avalia.value    = 0;
    doc.it01_valortransacao_avalia.disabled = false;
  } else {
    doc.it01_valorterreno_avalia.disabled   = false;
    doc.it01_valorconstr_avalia.disabled    = false;
    doc.it01_valortransacao_avalia.disabled = false;
  }

  js_calculaTaxas(obj);

  <?php if (!isset($bValorInformadoMaior) || (isset($bValorInformadoMaior) && !$bValorInformadoMaior)) : ?>
      if ( doc.primeiro_avalia != undefined ) {
        js_limpaValorFormaPgto();
        doc.primeiro_avalia.value = doc.it01_valortransacao_avalia.value;
      }
  <?php endif; ?>

  js_somaValores();
}

function js_limpaValorFormaPgto(){

  var aObjFormasPgto = js_getElementbyClass(document.all,'formasPgto');
  for ( var iInd=0; iInd < aObjFormasPgto.length; iInd++ ) {
     aObjFormasPgto[iInd].value = new Number(000).toLocaleString("pt-BR", {minimumFractionDigits: 2});
  }

}


function js_concordaValores(){

  var doc = document.form1;

  <?php  if ($oParam->it24_comparavaloresavaliacao === 'f') : ?>
      doc.it01_valortransacao_avalia.value = doc.it01_valortransacao.value;
      doc.it01_valorterreno_avalia.value   = doc.it01_valorterreno.value;
      doc.it01_valorconstr_avalia.value    = doc.it01_valorconstr.value;
  <?php endif; ?>

  js_consultaFormaPgtoCadastrada(document.form1.it14_guia.value,js_retornoFormaPgtoAvaliaCadastrada);

  const it01_valorterreno_avalia = js_removeMascaraMoeda(doc.it01_valorterreno_avalia.value);
  const it01_valorconstr_avalia = js_removeMascaraMoeda(doc.it01_valorconstr_avalia.value);

  if ( it01_valorterreno_avalia != 0 ){
	js_validaValores(doc.it01_valorterreno_avalia);
	doc.it01_valorterreno_avalia.focus();
  }

  if ( it01_valorconstr_avalia != 0 ){
	js_validaValores(doc.it01_valorconstr_avalia);
	doc.it01_valorconstr_avalia.focus();
  }

  if ( it01_valorterreno_avalia == 0 && it01_valorconstr_avalia == 0 ){
  	js_validaValores(doc.it01_valortransacao_avalia);
  	doc.it01_valortransacao_avalia.focus();
  }
}


function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itbi','func_itbinaoliberado.php?funcao_js=parent.js_preenchepesquisa|it01_guia','Pesquisa',true);
}


function js_preenchepesquisa(chave){
  db_iframe_itbi.hide();
  location.href = 'itb1_itbiavalia001.php?chavepesquisa='+chave;
}

function js_criaGrid() {

  gridFormasPgto              = new DBGrid("listaFormasPgto");
  gridFormasPgto.nameInstance = "gridFormasPgto";

  gridFormasPgto.setCellAlign( new Array("center","left","center","right") );
  gridFormasPgto.setHeader   ( new Array("Código","Descrição","Alíquota %","Valor"));
  gridFormasPgto.setCellWidth( new Array("10%","50%","20%","20%"));

  gridFormasPgto.setHeight(80);
  gridFormasPgto.show(document.getElementById('listaFormasPgto'));

}

function js_criaGridAvalia() {

  gridFormasPgtoAvalia              = new DBGrid("listaFormasPgtoAvalia");
  gridFormasPgtoAvalia.nameInstance = "gridFormasPgtoAvalia";

  gridFormasPgtoAvalia.setCellAlign( new Array("center","left","center","right") );
  gridFormasPgtoAvalia.setHeader   ( new Array("Código","Descrição","Alíquota %","Valor"));
  gridFormasPgtoAvalia.setCellWidth( new Array("10%","50%","20%","20%"));

  gridFormasPgtoAvalia.setHeight(80);
  gridFormasPgtoAvalia.show(document.getElementById('listaFormasPgtoAvalia'));

}


function js_consultaFormaPgto(iCodTransacao){

  js_divCarregando('Aguarde...','msgBox');

  var url          = "itb4_consultaformaPagamentoRPC.php";
  var sQuery	   = "codtransacao="+iCodTransacao;
      sQuery	  += "&tipoPesquisa=formasDisponiveis";
      sQuery	  += "&tipoITBI="+document.form1.tipo.value;
  var oAjax        = new Ajax.Request( url, {
                                              method: 'post',
                                              parameters: sQuery,
                                              onComplete: js_retornoFormaPgtoAvalia
                                            }
                                      );

}


function js_consultaFormaPgtoCadastrada(iGuia,sCallback){

  js_divCarregando('Aguarde...','msgBox');

  var url          = "itb4_consultaformaPagamentoRPC.php";
  var sQuery	   = "codguia="+iGuia;
      sQuery	  += "&tipoPesquisa=formasCadastradas";
  var oAjax        = new Ajax.Request( url, {
                                              method: 'post',
                                              parameters: sQuery,
                                              onComplete: sCallback
                                            }
                                      );

}


function js_retornoFormaPgtoCadastrada(oAjax){

  var objListaForma = JSON.parse(oAjax.responseText);
  var nValor		= 0;

  gridFormasPgto.clearAll(true);

  if ( objListaForma.iStatus && objListaForma.iStatus == 2){
   	js_removeObj("msgBox");
   	alert(objListaForma.sMensagem.urlDecode());
   	return false ;
  }

  for ( var iInd = 0; iInd < objListaForma.length; iInd++ ) {

    with (objListaForma[iInd]) {

      var sDisabled  = "disabled";
      if ( iInd == 0 ) {
        var sNomeCampo = "name='primeiro'";
      } else {
        var sNomeCampo = "";
      }

      let nValor = new Number(it26_valor.urlDecode()).toLocaleString('pt-BR', {minimumFractionDigits: 2});

      var sInputValor  = "<input type='text' id='teste_"+it25_sequencial.urlDecode()+"' value='"+nValor+"'";
    	  sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' "+sDisabled+" "+sNomeCampo+">";

      var aLinha	= new Array();
          aLinha[0] = it25_sequencial.urlDecode();
   	      aLinha[1] = it27_descricao.urlDecode();
    	  aLinha[2] = js_formatar(it27_aliquota.urlDecode(),'f');
    	  aLinha[3] = sInputValor;

      gridFormasPgto.addRow(aLinha);
      gridFormasPgto.renderRows();

    }
  }

  js_removeObj("msgBox");

}

function js_retornoFormaPgtoAvalia(oAjax){
  var objListaForma = JSON.parse(oAjax.responseText);
  var nValor		= 0;

  gridFormasPgtoAvalia.clearAll(true);

  if ( objListaForma.iStatus && objListaForma.iStatus == 2){
   	js_removeObj("msgBox");
   	alert(objListaForma.sMensagem.urlDecode());
   	return false ;
  }

  for ( var iInd = 0; iInd < objListaForma.length; iInd++ ) {

    with (objListaForma[iInd]) {

      if ( new Number(document.form1.it01_valortransacao_avalia.value) != 0 && it28_sequencial == 1 ){
		var nValor = document.form1.it01_valortransacao_avalia.value;
	  } else {
	    var nValor = new Number(000).toLocaleString("pt-BR", {minimumFractionDigits: 2});
	  }

      if ( iInd == 0 ) {
        var sDisabled  = "disabled";
        var sNomeCampo = "name='primeiro_avalia'";
      } else {
		var sDisabled  = "";
        var sNomeCampo = "";
      }

      var sInputValor  = "<input type='text' id='"+it25_sequencial.urlDecode()+"' class='formasPgto' value='"+nValor+"'";
    	  sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' "+sDisabled+" "+sNomeCampo+"";
          sInputValor += "onkeyup='jsFormataMoeda(this, (nValor) => {js_controlaValoresFormaPgto(this);})'>";

      var aLinha	= new Array();
   	      aLinha[0] = it25_sequencial.urlDecode();
   	      aLinha[1] = it27_descricao.urlDecode();
    	  aLinha[2] = js_formatar(it27_aliquota.urlDecode(),'f');
    	  aLinha[3] = sInputValor;

      gridFormasPgtoAvalia.addRow(aLinha);
      gridFormasPgtoAvalia.aRows[iInd].isSelected = true;
      gridFormasPgtoAvalia.renderRows();

    }
  }

  js_removeObj("msgBox");
  js_somaValores();
}


function js_retornoFormaPgtoAvaliaCadastrada(oAjax){


  var objListaForma = JSON.parse(oAjax.responseText);

  gridFormasPgtoAvalia.clearAll(true);

  if ( objListaForma.iStatus && objListaForma.iStatus == 2){
   	js_removeObj("msgBox");
   	alert(objListaForma.sMensagem.urlDecode());
   	return false ;
  }

  for ( var iInd = 0; iInd < objListaForma.length; iInd++ ) {

    with (objListaForma[iInd]) {

      if ( iInd == 0 ) {
        var sDisabled  = "disabled";
        var sNomeCampo = "name='primeiro_avalia'";
      } else {
		var sDisabled  = "";
        var sNomeCampo = "";
      }

      let nValor = new Number(it26_valor.urlDecode()).toLocaleString("pt-BR", {minimumFractionDigits: 2});

      var sInputValor  = "<input type='text' id='"+it25_sequencial.urlDecode()+"' class='formasPgto' value='"+nValor+"'";
    	  sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' "+sDisabled+" "+sNomeCampo+"";
    	  sInputValor += "onkeyup='jsFormataMoeda(this, (nValor) => {js_controlaValoresFormaPgto(this);})'>";

      var aLinha	= new Array();
   	      aLinha[0] = it25_sequencial.urlDecode();
   	      aLinha[1] = it27_descricao.urlDecode();
    	  aLinha[2] = js_formatar(it27_aliquota.urlDecode(),'f');
    	  aLinha[3] = sInputValor;

      gridFormasPgtoAvalia.addRow(aLinha);
      gridFormasPgtoAvalia.aRows[iInd].isSelected = true;
      gridFormasPgtoAvalia.renderRows();

    }
  }

  js_removeObj("msgBox");
  js_somaValores();
}




function js_controlaValoresFormaPgto(obj){

  var doc 	          = document.form1;
  var aObjFormasPgto  = js_getElementbyClass(document.all,'formasPgto');
  var nValorTotal	  = new Number(doc.it01_valortransacao_avalia.value.replaceAll(".", "").replace(",", "."));
  var nValorAlterado  = new Number(obj.value.replaceAll(".", "").replace(",", "."));
  var nValorResto	  = new Number();


  for ( var iInd=0; iInd < aObjFormasPgto.length; iInd++ ) {
    if ( aObjFormasPgto[iInd].name != "primeiro_avalia" ) {
     var nValLinha = new Number(aObjFormasPgto[iInd].value.replaceAll(".", "").replace(",", "."));
	 nValorResto  += nValLinha;
	}
  }

  var nValorAvista = new Number( nValorTotal - nValorResto );

  if ( nValorAvista < 0 ) {

    nValorAvista = nValorTotal - ( nValorResto - new Number(obj.value));
    alert("A soma dos valores das formas de pagamento não conferem com o valor total do imóvel!");
    obj.value         = 0;

  }

  doc.primeiro_avalia.value = new Number(nValorAvista).toLocaleString("pt-BR", {minimumFractionDigits: 2});

  js_somaValores();

}


function js_pesquisait01_tipotransacao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_itbitransacao','func_itbitransacao.php?validadata=true&funcao_js=parent.js_mostraitbitransacao1|it04_codigo|it04_descr','Pesquisa',true);
  }else{
     if(document.form1.it01_tipotransacao.value != ''){
        js_OpenJanelaIframe('','db_iframe_itbitransacao','func_itbitransacao.php?validadata=true&pesquisa_chave='+document.form1.it01_tipotransacao.value+'&funcao_js=parent.js_mostraitbitransacao','Pesquisa',false);
     }else{
       document.form1.it04_descr_avalia.value = '';
     }
  }
}

function js_mostraitbitransacao(chave,erro){

  document.form1.it04_descr_avalia.value = chave;

  if(erro==true){
    document.form1.it01_tipotransacao.focus();
    document.form1.it01_tipotransacao.value = '';
  } else {
      <?php  if ($oParam->it24_comparavaloresavaliacao === 'f') : ?>
            js_consultaFormaPgto(document.form1.it01_tipotransacao.value);
      <?php endif; ?>
  }

}

function js_mostraitbitransacao1(chave1,chave2){

  document.form1.it01_tipotransacao.value = chave1;
  document.form1.it04_descr_avalia.value  = chave2;
  db_iframe_itbitransacao.hide();
    <?php  if ($oParam->it24_comparavaloresavaliacao === 'f') : ?>
        js_consultaFormaPgto(chave1);
    <?php endif; ?>

}



function js_verMatric(){
  js_OpenJanelaIframe('CurrentWindow.corpo',"db_iframe_consulta",'cad3_conscadastro_002.php?cod_matricula='+document.form1.it06_matric.value,'Detalhes da Pesquisa',true);
}

js_criaGrid();
js_criaGridAvalia();

<?php
  if ( isset($oGet->chavepesquisa) && !isset($oPost->liberar) ) {
	echo "js_consultaFormaPgtoCadastrada(".$oGet->chavepesquisa.",js_retornoFormaPgtoCadastrada);";
	echo "js_pesquisait01_tipotransacao(false)";
  }
?>

function js_limpaForm(){
  $('it14_guia').value        = "";
  $('it01_data').value        = "";
  $('nome').value             = "";
  $('it22_matricri').value    = "";
  $('it22_setor').value       = "";
  $('it22_descrlograd').value = "";
  $('it01_areaterreno').value = "";
  $('it01_areatrans').value   = "";
  $('it22_matricri').value    = "";
  $('adquirenteprinc').value  = "";
  $('transmitenteprinc').value = "";
  $('it04_descr').value = "";
  $('it01_valorterreno').value = "";
  $('it01_valorconstr').value = "";
  $('it01_valortransacao').value = "";
  $('it04_descr_avalia').value = "";
  $('it01_valorterreno_avalia').value = "";
  $('it01_valorconstr_avalia').value = "";
  $('it01_valortransacao_avalia').value = "";
  $('imposto_avalia').value = "";
  $('it14_dtvenc').value = "";
  gridFormasPgtoAvalia.clearAll(true);
  //gridFormasPgtoAvalia.renderRows();
  gridFormasPgto.clearAll(true);
  //gridFormasPgto.renderRows();
}

var oGridTaxas = new DBGrid('gridTaxas');
var aHeaders   = ["Código", "Descrição", "Tipo de Valor", "Calcula Sobre", "Aliquota %", "Faixa", "Valor"];
var aCellWidth = ["10%", "25%", "15%", "15%", "7%", "18%", "10%"];
var aCellAlign = ["center", "left", "center", "center", "center", "center", "right"];

oGridTaxas.nameInstance = 'oGridTaxas';
oGridTaxas.setCellWidth(aCellWidth);
oGridTaxas.setCellAlign(aCellAlign);
oGridTaxas.setHeader(aHeaders);
oGridTaxas.setHeight(100);
oGridTaxas.show($('ctnGridTaxas'));

var aTaxas = [];

function js_buscarTaxaTipo(codigo)
{
    oGridTaxas.clearAll(true);

    if (codigo != "" && codigo != undefined) {
        const tipo = document.getElementById("tipo").value;
        const matricula = document.getElementById("it06_matric").value;

        if (tipo == "urbano" && !matricula) {
            return;
        }

        var oParam = new Object();
        oParam.executa = "buscarTaxasTipo";
        oParam.it36_sequencial = codigo;
        oParam.tipo = tipo;
        oParam.matricula = matricula;

        new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
          if (oRetorno.erro) {
            alert(oRetorno.mensagem);
            return;
          }

          aTaxas = oRetorno.aTaxas;

          js_verificaTaxasFaixa();
          js_montaGridTaxas(true);
        }).execute();
    }
}

function js_verificaTaxasFaixa()
{
    const it01_valorterreno = document.getElementById("it01_valorterreno_avalia").value.replaceAll(".", "").replace(",", ".");
    const it01_valorconstr = document.getElementById("it01_valorconstr_avalia").value.replaceAll(".", "").replace(",", ".");
    const it01_valortransacao = document.getElementById("it01_valortransacao_avalia").value.replaceAll(".", "").replace(",", ".");

    aTaxas.forEach(function (oTaxa, key){
        if (oTaxa.ar44_tipo == 3) {
            if (oTaxa.it37_calculasobre == 1) {
                if (!(parseFloat(it01_valorterreno) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valorterreno) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            } else if (oTaxa.it37_calculasobre == 2) {
                if (!(parseFloat(it01_valorconstr) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valorconstr) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            } else if (oTaxa.it37_calculasobre == 3) {
                if (!(parseFloat(it01_valortransacao) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valortransacao) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            }
        }
    });
}

function js_montaGridTaxas(bRequest = false)
{
    oGridTaxas.clearAll(true);

    for (const oTaxa of aTaxas) {
        if (!oTaxa.bMostra) {
            continue;
        }

        var aLinha = [];
        aLinha.push(oTaxa.ar44_sequencial);
        aLinha.push(oTaxa.ar44_descricao);

        if (oTaxa.ar44_tipo == 2 || oTaxa.ar44_tipo == 3) {
            if (oTaxa.ar44_tipo == 3) {
                aLinha.push("Fixo Sobre Faixa");
            } else {
                aLinha.push("Percentual");
            }

            if (oTaxa.it37_calculasobre == 1) {
                aLinha.push("Valor do Terreno");
            } else if (oTaxa.it37_calculasobre == 2) {
                aLinha.push("Valor da Construção");
            } else if (oTaxa.it37_calculasobre == 3) {
                aLinha.push("Ambos");
            }

            aLinha.push(oTaxa.aliquota.toLocaleString('pt-BR', { maximumFractionDigits: 2}));

            if (oTaxa.ar44_tipo == 3) {
                aLinha.push(`${oTaxa.it37_iniciofaixa} à ${oTaxa.it37_fimfaixa}`);
            } else {
                aLinha.push("");
            }
        } else {
            aLinha.push("Fixo");
            aLinha.push("");
            aLinha.push("");
            aLinha.push("");
        }

        const span = document.createElement("span");
        span.setAttribute("id", "id_"+oTaxa.ar44_sequencial);
        span.setAttribute("isTaxa", "true");
        span.setAttribute("codigoTaxa", oTaxa.ar44_sequencial);
        span.setAttribute("calculaSobre", oTaxa.it37_calculasobre);
        span.setAttribute("tipo", oTaxa.ar44_tipo);
        span.setAttribute("aliquota", (oTaxa.aliquota != undefined ? oTaxa.aliquota.toLocaleString('pt-BR', { maximumFractionDigits: 2}) : 0));
        span.innerHTML = oTaxa.i02_valor.toLocaleString('pt-BR', { maximumFractionDigits: 2});

        aLinha.push(span.outerHTML);

        oGridTaxas.addRow(aLinha);
    }

    oGridTaxas.renderRows();

    if (bRequest) {
        const it01_valorterreno_avalia = document.getElementById("it01_valorterreno_avalia");
        const it01_valorconstr_avalia = document.getElementById("it01_valorconstr_avalia");

        if (it01_valorterreno_avalia.value == 0 && it01_valorconstr_avalia.value == 0) {
            js_validaValores(document.getElementById("it01_valortransacao_avalia"));
        } else {
            js_validaValores(it01_valorterreno_avalia);
            js_validaValores(it01_valorconstr_avalia);
        }
    }
}

function js_calculaTaxas(oCampo)
{
    const aSpans = document.querySelectorAll("[isPercentual='true']");
    const it01_valortransacao = document.getElementById("it01_valortransacao");

    aSpans.forEach(function (aSpan){
        if (aSpan.getAttribute("sobre") == 1) {
            if (oCampo.name == "it01_valorterreno") {
                const valorFinal = ((aSpan.getAttribute("percentual") / 100) * oCampo.value);

                aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', { maximumFractionDigits: 2});
            }
        } else if (aSpan.getAttribute("sobre") == 2) {
            if (oCampo.name == "it01_valorconstr") {
                const valorFinal = ((aSpan.getAttribute("percentual") / 100) * oCampo.value);

                aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', { maximumFractionDigits: 2});
            }
        } else if (aSpan.getAttribute("sobre") == 3) {
            const valorFinal = ((aSpan.getAttribute("percentual") / 100) * it01_valortransacao.value);

            aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', { maximumFractionDigits: 2});
        }
    });

    js_verificaTaxasFaixa();
    js_montaGridTaxas();

    js_atualizaValorTotal();
}

function js_atualizaValorTotal()
{
    const aInputs = document.querySelectorAll("[isTaxa='true']");
    const taxas_avalia = document.getElementById("taxas_avalia");
    var valor = 0;

    aInputs.forEach(function (oInput){
        const valorTaxa = oInput.innerHTML.replaceAll(".", "").replace(",", ".");
        valor = valor + parseFloat(valorTaxa);
    });

    taxas_avalia.value = valor.toLocaleString('pt-BR', {maximumFractionDigits: 2});
}

<?php if (!empty($oDadosTaxaGuia)) : ?>
    js_buscarTaxaTipo(<?= $oDadosTaxaGuia->it38_taxasitbi ?>);
<?php endif; ?>

<?php  if ($oParam->it24_comparavaloresavaliacao === 't') : ?>
    js_concordaValores();
<?php endif; ?>

function js_removeMascaraMoeda(sValor)
{
    return sValor.replaceAll(".", "").replace(",", ".");
}

js_adicionarTabindexNosInputs();
function js_adicionarTabindexNosInputs() {
  const inputArray = document.querySelectorAll('input,select');
  const inputs = [];
  for (let index = 0; index < inputArray.length; index++) {
    if(inputArray[index].type != "hidden"){
      inputs.push(inputArray[index]);
    }
  }
  for (let index = 0; index < inputs.length; index++) {
      inputs[index].setAttribute('tabindex', index.toString());
  }
}

js_interacaoTeclaEnter();
function js_interacaoTeclaEnter() {
  const inputs = document.querySelectorAll("input,select");
  for (let i = 0 ; i < inputs.length; i++) {
    inputs[i].addEventListener("keypress", function(e){
      if (e.which == 13) {
        e.preventDefault();
        let nextInput = document.querySelectorAll('[tabIndex="' + (this.tabIndex + 1) + '"]');
        if (nextInput.length === 0) {
          nextInput = document.querySelectorAll('[tabIndex="0"]');
        }
        nextInput[0].focus();
      }
    })
  }
}
</script>
