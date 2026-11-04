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

//MODULO: escola
$oDaoParametroDependencia->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed18_i_codigo");
$clrotulo->label("ed18_c_nome");
$clrotulo->label("ed296_cursoedu");
?>
<form name="form1" method="post" action="">
  <center>
    <table border="0">
      <tr>
        <td nowrap title="<?=@$Ted295_sequencial?>">
          <?=@$Led295_sequencial?>
        </td>
        <td>
          <?php db_input('ed295_sequencial',10,$Ied295_sequencial,true,'text',3,"")?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ted295_habilitaprogressao?>">
          <?=@$Led295_habilitaprogressao?>
        </td>
        <td>
          <?php
            $aHabilita = array(
                               '1' => 'Desabilitada',
                               '2'=>'Habilitada'
                              );
            db_select('ed295_habilitaprogressao', $aHabilita, true, $db_opcao, "onchange = 'js_troca(this.value)';");
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ted295_qtddiscdependente?>">
          <?=@$Led295_qtddiscdependente?>
        </td>
        <td>
          <?php db_input('ed295_qtddiscdependente',17,$Ied295_qtddiscdependente,true,'text',$db_opcao,"")?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ted295_controledependencia?>">
          <?=@$Led295_controledependencia?>
        </td>
        <td>
          <?php
            $aControleDependencia = array(
                                          '0' => 'Selecione',
                                          '1' => 'Por Etapa',
                                          '2' => 'Por Base Curricular'
                                         );
            db_select('ed295_controledependencia',$aControleDependencia,true,$db_opcao,"");
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ted295_controlefreq?>">
          <?=@$Led295_controlefreq?>
        </td>
        <td>
          <?php
            $aControleFreq = array(
                                   '1' => 'Sim',
                                   '2' => 'Não'
                                  );
            db_select('ed295_controlefreq',$aControleFreq,true,$db_opcao,"");
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ted295_disceliminadep?>">
          <?=@$Led295_disceliminadep?>
        </td>
        <td>
          <?php
            $aDiscEliminaDep = array(
                                     '1' => 'Não',
                                     '2' => 'Sim'
                                    );
            db_select('ed295_disceliminadep',$aDiscEliminaDep,true,$db_opcao,"");
          ?>
        </td>
      </tr>
      <tr>
        <td id = 'ed296_cursoedu' nowrap title="<?=@$Ted296_cursoedu?>" colspan= '2' >
          <?php db_ancora(@$Led296_cursoedu,"js_pesquisaed296_cursoedu(true);",$db_opcao);?>

          <div id="arquivoAux">
            <?php db_input('webauxilia', 50, '', true, 'hidden', 3, '')?>
            <select multiple size="5" name="oAux" id="oAux" style="width:100%;"
                    onDblClick="js_apagarLinha(this);"
                    <?=($db_opcao == 1 || $db_opcao == 2 || $db_opcao == 22 ?"" : "disabled")?> >
            </select>
            <p align="center"><b>Dois cliques sobre o item exclui!</b></p>
          </div>

          <table border='0'>
            <tr>
              <td><b>Etapa(s) do(s) Curso(s):</b> <br />
                <select name="etapacurso" id="etapacurso" size="20" onclick="js_desabinc()"
                        multiple style="font-size:9px;width:350px;"
                        <?=($db_opcao == 1 || $db_opcao == 2 || $db_opcao == 22 ?"" : "disabled")?>>
                </select>
              </td>
              <td>
                <table border="0">
                  <tr>
                    <td>
                      <input id="incluirum" name="incluirum" title="Incluir" type="button" value=">"
                             onclick="js_incluir();" style="border:1px outset;border-top-color:#f3f3f3;
                             border-left-color:#f3f3f3;background:#cccccc; font-size:12px;font-weight:bold;
                             width:30px;height:15px;padding:0px;" disabled>
                    </td>
                  </tr>
                  <tr>
                    <td height="1"></td>
                  </tr>
                  <tr>
                    <td>
                      <input id="incluirtodos" name="incluirtodos" title="Incluir Todos" type="button" value=">>"
                             onclick="js_incluirtodos();" style="border:1px outset;border-top-color:#f3f3f3;
                             border-left-color:#f3f3f3;background:#cccccc; font-size:12px;font-weight:bold;
                             width:30px;height:15px;padding:0px;" disabled>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input id="excluirum" name="excluirum" title="Excluir" type="button" value="<"
                             onclick="js_excluir();" style="border:1px outset;border-top-color:#f3f3f3;
                             border-left-color:#f3f3f3;background:#cccccc;font-size:12px;font-weight:bold;
                             width:30px;height:15px;padding:0px;" disabled>
                    </td>
                  </tr>
                  <tr>
                    <td height="1"></td>
                  </tr>
                  <tr>
                    <td>
                      <input id="excluirtodos" name="excluirtodos" title="Excluir Todos" type="button" value="<<"
                             onclick="js_excluirtodos();"  style="border:1px outset;border-top-color:#f3f3f3;
                             border-left-color:#f3f3f3;background:#cccccc;font-size:12px;font-weight:bold;width:30px;
                             height:15px;padding:0px;" disabled >
                    </td>
                  </tr>
                </table>
              </td>
              <td>
                <b>Etapas com Progressão Parcial :</b> <br />
                <select name="etapaparcial[]" id="etapaparcial" size="20" onclick="js_desabexc()"
                        multiple style="font-size:9px;width:350px;"
                        <?=($db_opcao == 1 || $db_opcao == 2 || $db_opcao == 22 ?"" : "disabled")?>>
                </select>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
         type="submit" id="db_opcao" value="<?=($db_opcao_botao==1?"Incluir":($db_opcao_botao==2||$db_opcao_botao==22?"Alterar":"Excluir"))?>"
         <?=($db_botao==false?"disabled":"")?> onClick="return js_valida();" >
</form>

<script language="JavaScript">
<?php if($db_opcao!=3){?>
js_troca();
<?php }?>

function js_inicializa(iEscola) {

  var sUrl           = "edu4_escola.RPC.php";
  var jsRetorno      = "js_retornoInicializa";

  var oParam         = new Object();
      oParam.exec    = "getParametrosProgressaoParcial";
      oParam.iEscola = iEscola;

  js_webajax(oParam, jsRetorno, sUrl, false);

}

function js_retornoInicializa(oRetorno) {

  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus == 0) {

    alert(oRetorno.sMessage.urlDecode());
    return false;

  } else {

    /* Preenche os cursos */
    for (var iCont = 0; iCont < oRetorno.aCursos.length; iCont++) {

      var iCodigo    = oRetorno.aCursos[iCont].codigo;
      var sDescricao = oRetorno.aCursos[iCont].descricao.urlDecode();

      $('oAux').options[$('oAux').length] = new Option(sDescricao, iCodigo);

    }

    /* Preenche as etapas já escolhidas */
    for (var iCont = 0; iCont < oRetorno.aEtapas.length; iCont++) {

      var iCodigo     = oRetorno.aEtapas[iCont].codigo;
      var sDescricao  = oRetorno.aEtapas[iCont].curso.urlDecode()+" - ";
          sDescricao += oRetorno.aEtapas[iCont].descricao.urlDecode();

      $('etapaparcial').options[$('etapaparcial').length] = new Option(sDescricao, iCodigo);

    }

    $('excluirtodos').disabled = false;
    js_atualizaEtapas();

  }

}

function js_valida() {

  if (js_checaCampos()) {

    var sUrl               = "edu4_escola.RPC.php";
    var jsRetorno          = "js_retornoAjax";

    var oParam             = new Object();
        oParam.exec        = "setParametrosProgressaoParcial";
        oParam.sAcao       = $('db_opcao').value;
        oParam.iEscola     = <?=$iEscola?>;
        oParam.iCodigo     = $('ed295_sequencial').value;
        oParam.iHabProgres = $('ed295_habilitaprogressao').value;
        oParam.iQuantidade = $('ed295_qtddiscdependente').value;
        oParam.iContDepend = $('ed295_controledependencia').value;
        oParam.iContFreq   = $('ed295_controlefreq').value;
        oParam.iElimDep    = $('ed295_disceliminadep').value;
        oParam.iCursos     = js_buscaCursos();
        oParam.iEtapas     = js_buscaEtapas();

    js_webajax(oParam, jsRetorno, sUrl, false);

  }

  return false;

}

function js_retornoAjax(oRetorno) {

  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus == 0) {

    alert(oRetorno.sMessage.urlDecode());
    return false;

  } else {

    alert(oRetorno.sMessage.urlDecode());
    location.href = "edu1_parametrodependencia001.php";

  }

}

function js_buscaCursos() {

  var sCursos = "";
  var sSep    = "";
  var oSelect = $('oAux');

  for (iCont = 0; iCont < oSelect.length; iCont++) {

    sCursos += sSep+oSelect.options[iCont].value;
    sSep     = "|";

  }

  return sCursos;

}

function js_buscaEtapas() {

  var sEtapas = "";
  var sSep    = "";
  var oSelect = $('etapaparcial');

  for (iCont = 0; iCont < oSelect.length; iCont++) {

    sEtapas += sSep+oSelect.options[iCont].value;
    sSep     = "|";

  }

  return sEtapas;

}

function js_checaCampos() {

  var lStatus = true;
  var sErro   = "";

  //if ($('ed295_habilitaprogressao').value == "1") {

	//lStatus = false;
	//sErro  += "Habilitar Progressão Parcial/Dependência troque a opção por HABILITADA!\n";

  //}

  if ($('ed295_qtddiscdependente').value == "") {

    lStatus = false;
    sErro  += "Informe a quantidade de disciplinas dependentes!\n";

  }

  if ($('ed295_controledependencia').value == "0") {

    lStatus = false;
    sErro  += "Selecione o controle de dependência!\n";

  }

  if (parseInt($('oAux').length, 10) < 1) {

    lStatus = false;
    sErro  += "Informe ao menos um Curso com Progressão Parcial!\n";

  }

  if (parseInt($('etapaparcial').length, 10) < 1) {

    lStatus = false;
    sErro  += "Escolha ao menos uma etapa antes de incluir!\n";

  }

  if (sErro.trim() != "") {
    alert("Corrija os erros antes de prosseguir: \n\n"+sErro);
  }

  return lStatus;

}

function js_pesquisaed295_escola(mostra) {

  if (mostra == true) {

    js_OpenJanelaIframe('','db_iframe_escola',
    	                'func_escola.php?funcao_js=parent.js_mostraescola1|ed18_i_codigo|ed18_c_nome','Pesquisa',true
    	               );

  } else {

    if (document.form1.ed295_escola.value != '') {

      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escola',
    	                  'func_escola.php?pesquisa_chave='+document.form1.ed295_escola.value+
    	                  '&funcao_js=parent.js_mostraescola','Pesquisa',false
    	                 );

    } else {
      $('ed18_i_codigo').value = '';
    }

  }

}

function js_mostraescola(chave,erro) {

  document.form1.ed18_i_codigo.value = chave;
  if (erro == true) {

    document.form1.ed295_escola.focus();
    document.form1.ed295_escola.value = '';

  }

}

function js_mostraescola1(chave1,chave2) {

  document.form1.ed295_escola.value  = chave1;
  document.form1.ed18_c_nome.value = chave2;
  db_iframe_escola.hide();

}


function js_pesquisaed296_cursoedu(mostra) {

  if (mostra == true) {

	js_OpenJanelaIframe('','db_iframe_cursoedu',
			            'func_cursoescola.php?funcao_js=parent.js_mostracursoedu1|'+
			            'ed29_i_codigo|ed29_c_descr','Pesquisa',true
			           );

  } else {

	if (document.form1.ed296_cursoedu.value != '') {

	  js_OpenJanelaIframe('','db_iframe_cursoedu',
			              'func_cursoescola.php?pesquisa_chave='+document.form1.ed296_cursoedu.value+
			              '&funcao_js=parent.js_mostracursoedu','Pesquisa',false
			             );

	} else {
	  document.form1.ed29_i_codigo.value = '';
	}

  }

}

function js_mostracursoedu(chave,erro) {

  document.form1.ed29_i_codigo.value = chave;
  if (erro == true) {

	document.form1.ed296_cursoedu.focus();
	document.form1.ed296_cursoedu.value = '';

  }

}

function js_mostracursoedu1(chave1,chave2) {

  if (js_checaSelect(chave1) == false) {
    $('oAux').options[$('oAux').length] = new Option(chave2, chave1);
  }
  db_iframe_cursoedu.hide();

  /* Busca todas as etapas do curso escolhido  */
  var oParam     = new Object();
  oParam.exec    = "getEtapas";
  oParam.iCurso  = chave1;
  oParam.iEscola = <?=$iEscola?>;

  var sUrl = "edu4_escola.RPC.php";

  js_webajax(oParam, 'js_retornoEtapas', sUrl, false);

}

function js_retornoEtapas(oRetorno) {

  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus == 0) {

    alert(oRetorno.sMessage.urlDecode());
    return false;

  } else {

    if (oRetorno.iTotalRegistros == 0) {

      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {

      for (var iCont = 0; iCont < oRetorno.iTotalRegistros; iCont++) {

        js_adicionaLinhaEtapa(oRetorno.aResultado[iCont].ed11_i_codigo,
                              oRetorno.aResultado[iCont].ed11_c_descr.urlDecode(),
                              oRetorno.aResultado[iCont].ed29_c_descr.urlDecode());

      }

    }

  }

}

function js_adicionaLinhaEtapa(iCodigo, sDescricao, sCurso) {

  if (js_checaSelectEtapa(iCodigo) == false) {
    $('etapacurso').options[$('etapacurso').length] = new Option(sCurso+" - "+sDescricao, iCodigo);
  }

}

function js_pesquisa() {

  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_parametrodependencia',
		              'func_parametrodependencia.php?funcao_js=parent.js_preenchepesquisa|ed295_sequencial',
		              'Pesquisa',true
		             );

}

function js_preenchepesquisa(chave) {

  db_iframe_parametrodependencia.hide();
  <?php
  if ($db_opcao != 1) {
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>

}

/* Apaga a linha após um duplo clique na linha escolhida. */
function js_apagarLinha(oAux) {

  $('oAux').options[oAux.selectedIndex] = null;
  $('etapaparcial').innerHTML = "";
  js_atualizaEtapas();

}

function js_atualizaEtapas() {

  var oSelect  = $('oAux');
  var iSize    = oSelect.length;
  var sCodigos = "";
  var sSep     = "";

  for (var iCont = 0; iCont < iSize; iCont++) {

    sCodigos += sSep+oSelect.options[iCont].value;
    sSep      = ", ";

  }

  if (sCodigos == "") {

    $('etapacurso').innerHTML = "";
    $('etapaparcial').innerHTML = "";

  } else {

    var oParam     = new Object();
    oParam.exec    = "getEtapas";
    oParam.iCurso  = sCodigos;

    if (js_getSelectEtapas() != "") {
      oParam.iEtapas = js_getSelectEtapas();
    }

    <?php if ($iEscola != 7159) { ?>
    oParam.iEscola = <?=$iEscola?>;
    <?php } else { ?>
    //oParam.iEscola = input da escola
    <?php } ?>

    var sUrl = "edu4_escola.RPC.php";

    js_webajax(oParam, 'js_retornoAtualizaEtapas', sUrl);

  }

}

function js_getSelectEtapas() {

  var sEtapas = "";
  var sSep    = "";
  var oSelect = $('etapaparcial');

  for (var iCont = 0; iCont < oSelect.length; iCont++) {

    sEtapas += sSep+oSelect.options[iCont].value;
    sSep     = ", ";

  }

  return sEtapas;

}

function js_retornoAtualizaEtapas(oRetorno) {

  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus == 0) {

    alert(oRetorno.sMessage.urlDecode());
    return false;

  } else {

    if (oRetorno.iTotalRegistros == 0) {

      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {

      $('etapacurso').innerHTML = "";

      for (var iCont = 0; iCont < oRetorno.iTotalRegistros; iCont++) {

         js_adicionaLinhaEtapa(oRetorno.aResultado[iCont].ed11_i_codigo,
                               oRetorno.aResultado[iCont].ed11_c_descr.urlDecode(),
                               oRetorno.aResultado[iCont].ed29_c_descr.urlDecode());

      }

      $('incluirtodos').disabled = false;

    }

  }

}

function js_checaSelect(iCodigo) {

  var oSelect = $('oAux');
  var iSize   = oSelect.length;
  var lResult = false;

  for (var iCont = 0; iCont < iSize; iCont++) {

	if (oSelect.options[iCont].value == iCodigo) {
	  lResult = true;
	}

  }

  return lResult;

}

function js_checaSelectEtapa(iCodigo) {

  var oSelect = $('etapacurso');
  var iSize   = oSelect.length;
  var lResult = false;

  for (var iCont = 0; iCont < iSize; iCont++) {

    if (oSelect.options[iCont].value == iCodigo) {
      lResult = true;
    }

  }

  return lResult;

}

function js_incluir() {

  var Tam = $('etapacurso').length;
  var F   = document.form1;

  for (x = 0; x < Tam; x++) {

	if (F.etapacurso.options[x].selected == true) {

	  F.elements['etapaparcial[]'].options[F.elements['etapaparcial[]'].options.length] = new Option(F.etapacurso.options[x].text,
	    	                                                                                F.etapacurso.options[x].value)
	  F.etapacurso.options[x] = null;
	  Tam--;
	  x--;

	}

  }

  if ($('etapaparcial').length > 0) {
	$('etapaparcial').options[0].selected = true;
  } else {

	$('incluirum').disabled    = true;
	$('incluirtodos').disabled = true;

  }

  $('excluirtodos').disabled = false;
  $('etapaparcial').focus();

}

function js_incluirtodos() {

  var Tam = $('etapacurso').length;
  var F   = document.form1;
  for (i = 0; i < Tam; i++) {

	F.elements['etapaparcial[]'].options[F.elements['etapaparcial[]'].options.length] = new Option(F.etapacurso.options[0].text,
	            	                                                                               F.etapacurso.options[0].value)
	F.etapacurso.options[0] = null;

  }

  $('incluirum').disabled    = true;
  $('incluirtodos').disabled = true;
  $('excluirtodos').disabled = false;
  $('etapaparcial').focus();

}

function js_excluir() {

  var F = document.getElementById("etapaparcial");
  Tam   = F.length;

  for (x = 0; x < Tam; x++) {

	if (F.options[x].selected == true) {

	  document.form1.etapacurso.options[document.form1.etapacurso.length] = new Option(F.options[x].text,
	  	                                                                               F.options[x].value);
	  F.options[x] = null;
	  Tam--;
	  x--;

	}

  }

  if ($('etapaparcial').length > 0) {
	$('etapaparcial').options[0].selected = true;
  }

  if ($('etapaparcial').length == 0) {

	$('excluirum').disabled    = true;
	$('excluirtodos').disabled = true;
	$('incluirtodos').disabled = false;

  }

  $('etapacurso').focus();

}

function js_excluirtodos() {

  var Tam = $('etapaparcial').length;
  var F   = document.getElementById("etapaparcial");
  for (i = 0; i < Tam; i++) {

	$('etapacurso').options[$('etapacurso').length] = new Option($('etapaparcial').options[0].text,
	     	                                                     $('etapaparcial').options[0].value);
	$('etapaparcial').options[0] = null;

  }

  if ($('etapaparcial').length == 0) {

	$('excluirum').disabled    = true;
	$('excluirtodos').disabled = true;
	$('incluirtodos').disabled = false;

  }

  $('etapacurso').focus();

}

function js_desabinc() {

  for (i = 0; i < $('etapacurso').length; i++) {

	if ($('etapacurso').length > 0 && $('etapacurso').options[i].selected) {

	  if ($('etapaparcial').length > 0) {
	    $('etapaparcial').options[0].selected = false;
	  }

	  $('incluirum').disabled = false;
	  $('excluirum').disabled = true;

	}

  }

}

function js_desabexc() {

  for (i = 0; i < $('etapaparcial').length; i++) {

	if ($('etapaparcial').length > 0 && $('etapaparcial').options[i].selected) {

	  if ($('etapacurso').length > 0) {
	    $('etapacurso').options[0].selected = false;
	  }

	  $('incluirum').disabled = true;
	  $('excluirum').disabled = false;

	}

  }

}

function js_troca(valor) {

  if ($('ed295_habilitaprogressao').value == 2) {

	$('ed295_qtddiscdependente').disabled   = false;
	$('ed295_controledependencia').disabled = false;
	$('ed295_controlefreq').disabled        = false;
	$('ed295_disceliminadep').disabled      = false;
	$('oAux').disabled                      = false;
	$('etapacurso').disabled                = false;
	$('etapaparcial').disabled              = false;
	$('ed296_cursoedu').style.display       = '';

  } else {

	$('ed295_qtddiscdependente').disabled   = true;
	$('ed295_controledependencia').disabled = true;
	$('ed295_controlefreq').disabled        = true;
	$('ed295_disceliminadep').disabled      = true;
	$('oAux').disabled                      = true;
	$('etapacurso').disabled                = true;
	$('etapaparcial').disabled              = true;
	$('ed296_cursoedu').style.display       = 'none';

  }

}

<?php if (isset($iCodEscola)) {  ?>

  js_inicializa(<?=$iCodEscola?>);

<?php } ?>

</script>
