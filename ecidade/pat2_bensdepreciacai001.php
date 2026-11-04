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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_db_depart_classe.php"));
require_once(modification("classes/db_cfpatri_classe.php"));
require_once(modification("classes/db_bens_classe.php"));
require_once(modification("classes/db_clabens_classe.php"));
require_once(modification("classes/db_departdiv_classe.php"));

$cldb_depart 		= new cl_db_depart;
$clcfpatri 			= new cl_cfpatri;
$clbens      		= new cl_bens;
$clclabens   		= new cl_clabens;
$cldepartdiv 		= new cl_departdiv;
$cldb_estrut 		= new cl_db_estrut;
$oAuxDpto       = new cl_arquivo_auxiliar;
$oAuxDpto       = new cl_arquivo_auxiliar;
$iAnoSessao     = db_getsession("DB_anousu");

$clbens->rotulo->label();
$clclabens->rotulo->label();
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Expires" CONTENT="0">
	<script type="text/javascript" src="scripts/scripts.js"></script>
	<script type="text/javascript" src="scripts/strings.js"></script>
	<script type="text/javascript" src="scripts/prototype.js"></script>
	<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body style="margin-top:35px; background-color: #CCCCCC;" >
<center>
<form name="form1">
	<fieldset style="width: 250px;">
		<legend><b>Relatório de Depreciações</b></legend>
    
    <?php
            $oAuxDpto->cabecalho      = "<strong>Departamento</strong>";
            $oAuxDpto->codigo         = "coddepto"; //chave de retorno da func
            $oAuxDpto->descr          = "descrdepto";   //chave de retorno
            $oAuxDpto->nomeobjeto     = 'departamento';
            $oAuxDpto->funcao_js      = 'js_mostra_departamento';
            $oAuxDpto->funcao_js_hide = 'js_mostra_departamento1';
            $oAuxDpto->sql_exec       = "";
            $oAuxDpto->func_arquivo   = "func_db_depart.php";  //func a executar
            $oAuxDpto->nomeiframe     = "db_iframe_db_depart";
            $oAuxDpto->localjan       = "";
            $oAuxDpto->executa_script_apos_incluir = "";
            $oAuxDpto->db_opcao       = 2;
            $oAuxDpto->tipo           = 2;
            $oAuxDpto->top            = 0;
            $oAuxDpto->linhas         = 5;
            $oAuxDpto->vwidth         = 400;
            $oAuxDpto->nome_botao     = 'db_lanca';
            $oAuxDpto->fieldset       = false;        
            $oAuxDpto->funcao_gera_formulario();      
          ?>    
    <table width="100%">
      <tr>
        <td colspan="4">
          <fieldset style="border:none; border-top: outset #000 1px;">
            <legend style="font-weight: bold;">Classificações</legend>
            <table>
              <tr rowrap="nowrap" title="<?=@$Tt64_class?>" style="text-align:left;">
                <td >
                  <?php
                    db_ancora("<b>De:</b> ","js_pesquisaClasseInicial(true);",1);
                  ?>
                </td>
                <td nowrap="nowrap">
                  <?php
                    db_input('t64_classInicio',10,$It64_class,true,'text',3,'');
                    db_input('t64_descrInicio',30,$It64_descr,true,'text',3,'');
                  ?>
                </td>
              </tr>
              <tr>
                <td style="text-align:right;">
                  <?php
                    db_ancora("<b>até</b>","js_pesquisaClasseFinal(true);",1);
                  ?>
                </td>
                <td nowrap="nowrap">
                  <?php
                    db_input('t64_classFinal',10,$It64_class,true,'text',3,'');
                    db_input('t64_descrFinal',30,$It64_descr,true,'text',3,'');
                  ?>
                </td>
              </tr>
            </table>
          </fieldset> 
        
        </td>
      </tr>
      <tr>
        <td align="right" nowrap style="text-align:left;" >
          <strong>Período:</strong>
        </td>
        <td align="left" nowrap>
          <?php 
            db_inputdata('periodoInicial',null, null, null, true,'text',1,"");
          ?>
        </td>
        <td style="text-align:right;">
          <strong>até:</strong>
        </td>
        <td>
          <?php
            db_inputdata('periodoFinal',null, null, null, true,'text',1,"");
          ?>
        </td>  
   	  </tr>
      <tr>
        <td nowrap="nowrap">
          <strong>Tipo de Impressão:</strong>
        </td>
        <td colspan="4">
          <?php
            $aTipoImpressao = array("S"=>"Sintético","A"=>"Análitico"); 
            db_select("impressao", $aTipoImpressao, true, 1);
          ?>
        </td>
      </tr>
      
    </table>
	</fieldset>
  <br>
  <input type="button" name="btnProcessar" id="btnProcessar" value="Imprimir">
</form>	
</center>
</body>
</html>
<?php 
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<script>


/**
 * Verificar se vai ser usado classe mais descrição
 */
function js_pesquisaClasseInicial(mostra) {
  
  if (mostra==true) {
    
    js_OpenJanelaIframe('CurrentWindow.corpo',
                        'db_iframe_clabens',
                        'func_clabens.php?funcao_js=parent.js_mostraClasseInicial1|t64_class|t64_descr&analitica=true',
                        'Pesquisa',
                        true);  
  } else {
    
     testa = new String($F("t64_classInicio"));     
     if (testa != '' && testa != 0) {
       
       i = 0;       
       for (i = 0;i < document.form1.t64_classInicio.value.length;i++) {
         testa = testa.replace('.','');       
       }
       js_OpenJanelaIframe('CurrentWindow.corpo',
                           'db_iframe_clabens',
                           'func_clabens.php?pesquisa_chave='+testa+'&funcao_js=parent.js_mostraClasseInicial&analitica=true',
                           'Pesquisa',false);
     }else{
       $("t64_descrInicio").value = '';
     }
  }
}

function js_mostraClasseInicial(chave, erro) {
  
  $("t64_descrInicio").value = chave;
  if (erro) {
    
    $("t64_classInicio").value = '';
    $("t64_classInicio").focus();
  }
}

function js_mostraClasseInicial1(chave1, chave2) {
  
  $("t64_classInicio").value = chave1;
  $("t64_descrInicio").value = chave2;
  db_iframe_clabens.hide();
}

/**
 * Pesquisa Classe Final
 */
function js_pesquisaClasseFinal(mostra) {
  
  if (mostra) {
    
    js_OpenJanelaIframe('CurrentWindow.corpo',
                        'db_iframe_clabens',
                        'func_clabens.php?funcao_js=parent.js_mostraClasseFinal1|t64_class|t64_descr&analitica=true',
                        'Pesquisa',
                        true);  
  } else {
    
     testa = new String($F("t64_classFinal"));     
     if (testa != '' && testa != 0) {
       
       i = 0;       
       for (i = 0;i < document.form1.t64_classFinal.value.length; i++) {
         testa = testa.replace('.','');       
       }
       js_OpenJanelaIframe('CurrentWindow.corpo',
                           'db_iframe_clabens',
                           'func_clabens.php?pesquisa_chave='+testa+'&funcao_js=parent.js_mostraClasseFinal&analitica=true',
                           'Pesquisa',false);
     }else{
       $("t64_descrFinal").value = '';
     }
  }
}

function js_mostraClasseFinal(chave, erro) {
  
  $("t64_descrFinal").value = chave;
  if (erro) {
    
    $("t64_classFinal").value = '';
    $("t64_classFinal").focus();
  }
}

function js_mostraClasseFinal1(chave1, chave2) {
  
  $("t64_classFinal").value = chave1;
  $("t64_descrFinal").value = chave2;
  db_iframe_clabens.hide();
}




/**
 * Quando clicado em Imprimir, processa as informações e envia ao fonte do relatório.
 */
$("btnProcessar").observe("click", function() {

  var sDepartamentos = "";
  var sDataInico     = $F("periodoInicial");
  var sDataFinal     = $F("periodoFinal");
  var iDepartamentos = $("departamento").length;
  var sClasseInicio  = $F("t64_classInicio");
  var sClasseFim     = $F("t64_classFinal"); 
  var sImpressao     = $F("impressao");

  iDptos = document.getElementById("departamento").length;
  sValoresDpto = "";
  
  for (var i = 0; i < iDepartamentos; i++) {
    
    if (sDepartamentos == "") {
      sDepartamentos += $("departamento")[i].value;
    } else {
      sDepartamentos += ","+$("departamento")[i].value;
    }
    
  }

  if (sDataInico !="" && sDataFinal !="") {
    
    if (js_comparadata(sDataInico, sDataFinal, ">")) {
      alert("Período Inicial não pode ser maior que o período final.");  
    }
  }

  

  
  var sLocation  = "pat2_bensdepreciacao002.php?sDataInicio="+sDataInico+"&sDataFinal="+sDataFinal;
      sLocation += "&sClasseInicio="+sClasseInicio+"&sClasseFim="+sClasseFim+"&sDepartamentos="+sDepartamentos;
      sLocation += "&sImpressao="+sImpressao
  jan = window.open(sLocation,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
});

</script>