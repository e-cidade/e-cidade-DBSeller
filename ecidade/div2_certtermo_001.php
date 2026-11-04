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
require_once(modification("libs/db_utils.php"));

$clrotulo = new rotulocampo;
$clrotulo->label("v93_termo");
$clrotulo->label("DBtxt14");
$clrotulo->label("DBtxt15");
$clrotulo->label("dataInicial");
$clrotulo->label("dataFinal");

$oGet = db_utils::postMemory($_GET);

if (isset($oGet->iCdaDividaIni) && isset($oGet->iCdaDividaFim)) {

  $v93_termo  = $oGet->iCdaDividaIni;
  $v93_termo1 = $oGet->iCdaDividaFim;
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" content="0">
<script language="javascript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="javascript" type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript">

function js_AbreJanelaRelatorio() {

  if (js_verifica() == true) {

  	  datacertidao = '';

      if (document.form1.DBtxt15_ano.value != '') {
    	  datacertidao = document.form1.DBtxt15_ano.value+'/'+document.form1.DBtxt15_mes.value+'/'+document.form1.DBtxt15_dia.value;
    	}
      jan = window.open('div2_certtermo_002.php?certid='+document.form1.v93_termo.value+'&certid1='+document.form1.v93_termo1.value+'&valormaximo='+document.form1.dataFinal.value+'&valorminimo='+document.form1.dataInicial.value+'&datacertidao='+datacertidao+'&dataini='+document.form1.dataInicial.value+'&datafim='+document.form1.dataFinal.value+'&anoexerc='+document.form1.anoExerc.value+'&origemtipo='+document.form1.origem.value+'&origem='+document.form1.origemcod.value+'&proced='+document.form1.v03_codigo.value+'&totexe='+document.form1.totexe.value+'&reemissao='+document.form1.DBtxt14.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
      jan.moveTo(0, 0);
  }
}

</script>
<link href="estilos.css" rel="stylesheet" type="text/css"/>
<style type="text/css">

#dataInicial, #dataFinal, #DBtxt14, #origem, #anoExerc{
  width: 83px;
}


</style>
</head>
<body class="body-default" onLoad="a=1" >
<form class="container" name="form1" method="post" action="div2_certparc_002.php" onsubmit="return js_verifica()">
	<fieldset>
		<legend>Termo de Inscrição Dívida Ativa</legend>
			<table class="form-container">

        <tr>
          <td title="<?php echo $Tv93_termo; ?>">
            <?php
              db_ancora("Termo:", "js_pesquisaparcel(true)", 1);
            ?>
          </td>
          <td>
            <?php

              $Sv93_termo = "Termo";

              db_input("v93_termo", 10, 1, true, "text", 1, "onchange='js_pesquisaparcel(false);document.form1.v93_termo.value=this.value'");

              db_ancora("<strong>à</strong>", "js_pesquisaparcel1(true)", 1);

              db_input("v93_termo1", 10, 1, true, "text", 1, "onchange='js_pesquisaparcel(false);document.form1.v93_termo1.value=this.value'");
            ?>
          </td>
        </tr>

        <tr>
          <td class="field-size2">
            <label for="periodo">Período:</label>
          </td>
          <td class="field-size2">
            <?php
              db_inputdata("dataInicial", "", "", "", true, "text", 1, "");
            ?>
            <strong>à</strong>
            <?php
              db_inputdata("dataFinal", "", "", "", true, "text", 1, "");
            ?>
          </td>
        </tr>

        <tr class='hide'>
          <td nowrap title="Informe o ano de exercício" class="field-size2">
            <label for="DBtxt15">Ano Exercício:</label>
          </td>
          <td> 
            <?php db_inputdata("DBtxt15", "", "", "", true, "text", 1) ?>
          </td>
        </tr>

        <tr>
          <td nowrap title="Informe o ano de exercício" class="field-size2">
            <label for="anoExerc">Ano Exercício:</label>
          </td>
          <td> 
            <?php db_input('anoExerc',4,1,true,'text',1,"","","","",4); ?>
          </td>
        </tr>

        <tr>
        <td>
            <?php 
                db_ancora('Origem:', 'js_pesquisaOrigem(true);', 1);
            ?>
        </td>
        <td>
            <?php
                $aOrigem = array("0" => "Selecione..","1" => "CGM", "2" => "Matrícula", "3" => "Inscrição");
                db_select("origem", $aOrigem, true, 4, "onchange='js_pesquisaOrigem()'");
                db_input('origemcod', 10, 0, true, 'text', 1, 'onchange="js_pesquisaOrigem(false);"');
                db_input("origemdescr", 34, 1, true, "text", 3, 'onchange="js_pesquisaOrigem(false);document.form1.origemdescr1.value=this.value"');
            ?>
        </td>
       </tr>

        <tr>
          <td title="<?php echo 'Procedência'; ?>">
            <?php
              db_ancora("Procedência:", "js_pesquisaproced(true)", 1);
            ?>
          </td>
          <td>
            <?php

              $Sv93_termo = "Procedência";

              db_input("v03_codigo", 10, 1, true, "text", 1, "onchange='js_pesquisaproced(false);document.form1.v03_codigo1.value=this.value'");
              db_input("v03_dcomp", 50, 1, true, "text", 3, "onchange='js_pesquisaproced(false);document.form1.v03_dcomp1.value=this.value'");
            ?>
          </td>
        </tr>

        <tr>
          <td nowrap title="<?php echo $TDBtxt14;?>">
              <label for="DBtxt14"><?php echo "Atualiza valores:";?></label>
            </td>
          <td>
            <?php
              $aReemissao = array("false" => "Não", "true" => "Sim");
              db_select("DBtxt14", $aReemissao, true, 4, "");
            ?>
          </td>
        </tr>

        <tr>
          <td class="field-size4 hide"><label for="totexe">Totaliza por exercício:</label></td >
          <td class="field-size4 hide">
            <?php
              $aTotaliza = array("f" => "Não", "t" => "Sim");
              db_select("totexe", $aTotaliza, true, 4, "");
            ?>
          </td>
        </tr>
      </table>
  </fieldset>
  <input name="exibir_relatorio" type="button" id="exibir_relatorio" value="Processar" onClick="js_AbreJanelaRelatorio()"/>
</form>

<?php
  if (!isset($oGet->iCdaDividaIni) && !isset($oGet->iCdaDividaFim)) {
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  }
?>
</body>
</html>
<script type="text/javascript">
function js_pesquisaparcel(mostra){

     if(mostra == true){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_termoinscrreg.php?funcao_js=parent.js_mostratermo1|0','Pesquisa',true);
     }else{
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_termoinscrreg.php?pesquisa_chave='+document.form1.v93_termo.value+'&funcao_js=parent.js_mostratermo','Pesquisa',false);
     }
}
function js_mostratermo(chave,erro){
  
  document.form1.v93_termo.value = chave1;

  if(erro==true){

     document.form1.v93_termo.focus();
     document.form1.v93_termo.value = '';
     document.form1.v93_termo1.value = '';
  }
}
function js_mostratermo1(chave1){

     document.form1.v93_termo.value = chave1;
     db_iframe.hide();
}
function js_pesquisaparcel1(mostra){

     if(mostra==true){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_termoinscrreg.php?funcao_js=parent.js_mostratermo3|0','Pesquisa',true);
     }else{
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_termoinscrreg.php?pesquisa_chave='+document.form1.v93_termo1.value+'&funcao_js=parent.js_mostratermo2','Pesquisa',false);
     }
}
function js_mostratermo2(chave,erro){

  document.form1.v93_termo1.value = chave;

  if(erro==true){
     document.form1.v93_termo1.focus();
     document.form1.v93_termo1.value = '';
  }
}
function js_mostratermo3(chave1){

     document.form1.v93_termo1.value = chave1;
     db_iframe.hide();
}

function js_pesquisaproced(mostra){

if( mostra == true ) {
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_proced','func_proced.php?funcao_js=parent.js_mostraproced1|v03_codigo|v03_dcomp','Pesquisa',true,'0');
}else{

   if ( document.form1.v03_codigo.value != '' ) {
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_proced','func_proced.php?pesquisa_chave='+document.form1.v03_codigo.value+'&funcao_js=parent.js_mostraproced','Pesquisa',false);
   }else{
     document.form1.v03_dcomp.value = '';
   }
}
}

function js_mostraproced( chave, erro ) {

document.form1.v03_dcomp.value = chave;

if( erro == true ) {

  document.form1.v03_codigo.focus();
  document.form1.v03_codigo.value = '';
}
}

function js_mostraproced1( chave1, chave3 ) {

document.form1.v03_codigo.value = chave1;
document.form1.v03_dcomp.value = chave3;
db_iframe_proced.hide();
}

/**
 * Função que efetua a pesquisa de CGM.
 */


function js_pesquisaOrigem(mostra) {
  var origem = $('origem');

  if (origem.value == 1) {

    if (mostra == true) {

      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_cgm',
                          'func_cgm.php?funcao_js=parent.js_retornoPesquisa|z01_numcgm|z01_nome',
                          'Pesquisa CGM',
                          true);                  
    } else {

      var iCgmFormulario = $F('origemcod');
      if (iCgmFormulario == ''){
        $('origemdescr').value = '';
      } else {
        js_OpenJanelaIframe('CurrentWindow.corpo',
                            'db_iframe_cgm',
                            'func_cgm.php?pesquisa_chave='+iCgmFormulario+
                            '&funcao_js=parent.js_retornoPesquisaCodigo',
                            'Pesquisa CGM',
                            false);
      }
    }
  } 

  if (origem.value == 2) {
    
    if (mostra == true) {
    js_OpenJanelaIframe('CurrentWindow.corpo',
                        'db_iframe_matric',
                        'func_iptubase.php?funcao_js=parent.js_retornoPesquisa|j01_matric|z01_nome',
                        'Pesquisa Matricula',
                        true);
    } else {

      var iMatriculaFormulario = $F('origemcod');
      if (iMatriculaFormulario == ''){
        $('origemdescr').value = '';
      } else {
        js_OpenJanelaIframe('CurrentWindow.corpo',
                            'db_iframe_matric',
                            'func_iptubase.php?pesquisa_chave='+iMatriculaFormulario+
                            '&funcao_js=parent.js_retornoPesquisaCodigo',
                            'Pesquisa Matricula',
                            false);
      }
    }
  }  

  if (origem.value == 3) {
    
    if (mostra == true) {
    js_OpenJanelaIframe('CurrentWindow.corpo',
                        'db_iframe',
                        'func_issbase.php?funcao_js=parent.js_retornoPesquisa|q02_inscr|z01_nome',
                        'Pesquisa Inscrição',
                        true);
    } else {

    var iInscricaoFormulario = $F('origemcod');
      if (iInscricaoFormulario == ''){
        $('origemdescr').value = '';
      } else {
        js_OpenJanelaIframe('CurrentWindow.corpo',
                            'db_iframe',
                            'func_issbase.php?pesquisa_chave='+iInscricaoFormulario+
                            '&funcao_js=parent.js_retornoPesquisaCodigo',
                            'Pesquisa Inscrição',
                            false);
      }
    }
  }
}

/**
* Função que recebe o retorno da pesquisa de cgm e trata o resultado.
*/

function js_retornoPesquisa() {
 
    if (origem.value == 1) {
      db_iframe_cgm.hide();
    } else if (origem.value == 2) {
      db_iframe_matric.hide();
    } else {  
     db_iframe.hide();
  }

    if (arguments[0] == true) {
      $('origemcod').value   = '';
      $('origemdescr').value = arguments[1];
    } else if (arguments[0] == false) {
      $('origemdescr').value = arguments[1];
    } else {
      $('origemcod').value   = arguments[0];
      $('origemdescr').value = arguments[1];
    }
}

function js_retornoPesquisaCodigo() {
 
 if (origem.value == 1) {
   db_iframe_cgm.hide();
 } else if (origem.value == 2) {
   db_iframe_matric.hide();
 } else {  
  db_iframe.hide();
}

 if (arguments[0] == true) {
   $('origemcod').value   = '';
   $('origemdescr').value = arguments[1];
 } else if (arguments[0] == false) {
   $('origemdescr').value = arguments[1];
 } else {
   if (origem.value == 1) {
    $('origemcod').value   = arguments[0];
    $('origemdescr').value = arguments[1];
   } else {
    $('origemdescr').value = arguments[0];
   }
 }
}

function js_verifica(){

    if (!empty(document.form1.dataInicial.value) && empty(document.form1.dataFinal.value)) {
      alert('Insira data final do período!');
      return false;
    }

    if (!empty(document.form1.dataFinal.value) && empty(document.form1.dataInicial.value)) {
      alert('Insira data inicial do período!');
      return false;
    }

    obj = document.form1;
    data_ini =  obj.dataInicial_ano.value+'-'+obj.dataInicial_mes.value+'-'+obj.dataInicial_dia.value;
    data_fin =  obj.dataFinal_ano.value+'-'+obj.dataFinal_mes.value+'-'+obj.dataFinal_dia.value;

    if (data_fin < data_ini){
      alert('Data final menor que a data inicial.');
      return false;
    }
    
    if (document.form1.origem.value == 1 & empty(document.form1.origemcod.value)) {
      alert('Se deseja buscar a origem por CGM, insira o número');
      return false;
    }

    if (document.form1.origem.value == 2 & empty(document.form1.origemcod.value)) {
      alert('Se deseja buscar a origem por Matrícula, insira o número');
      return false;
    }

    if (document.form1.origem.value == 3 & empty(document.form1.origemcod.value)) {
      alert('Se deseja buscar a origem por Inscrição, insira o número');
      return false;
    }

    if (document.form1.origem.value == 0 & !empty(document.form1.origemcod.value)) {
      alert('Selecione o tipo de Origem!');
      return false;
    }
    
    if (!empty(document.form1.v93_termo.value) && empty(document.form1.v93_termo1.value)) {
      alert('Insira o valor final do intervalo termo!');
      return false;
    }

    if (!empty(document.form1.v93_termo1.value) && empty(document.form1.v93_termo.value)) {
      alert('Insira o valor inicial do intervalo termo!');
      return false;
    }

    if (!empty(document.form1.v03_codigo.value) && empty(document.form1.dataInicial.value)) {
      alert('Insira um período para emitir o relatório!');
      return false;
    }

    if (empty(document.form1.v93_termo.value)
        && empty(document.form1.v93_termo1.value)
        && empty(document.form1.dataInicial.value)
        && empty(document.form1.dataFinal.value)
        && empty(document.form1.anoExerc.value)
        && empty(document.form1.origemcod.value)
        && empty(document.form1.v03_codigo.value)) {
      alert('Para processar é necessário colocar algum paramêtro!');
      return false;
    }

  return true;  
}

$("v93_termo").addClassName("field-size2");
$("v93_termo1").addClassName("field-size2");
$("DBtxt15").addClassName("field-size2");
$("DBtxt14").setAttribute("rel","ignore-css");
$("DBtxt14").addClassName("field-size2");
$("totexe").setAttribute("rel","ignore-css");
$("totexe").addClassName("field-size2");

</script>