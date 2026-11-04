<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="post" action="">
<div class="container">
<fieldset>
  <legend>Relatório de Liquidações Aptas ao Repasse Financeiro (Natal)</legend>
  <table>
      
      <tr>
          <td>
            <label for="orgao_numero"><?php db_ancora('Órgão:', 'buscarOrgao(true)', 1, null, 'orgao_numero_ancora'); ?></label>
          </td>
          <td>
            <?php
            $Sorgao_numero = 'Órgão';
            db_input('orgao_numero', 5, 1, true, 'text', 1, 'onChange="buscarOrgao(false)"');
            db_input('orgao_descricao', 40, 0, true, 'text', 3);
            ?>
          </td>
        </tr>

        <tr>
          <td>
            <label for="unidade_numero>">
              <?php db_ancora('Unidade:', 'buscarUnidade(true)', 1, null, 'unidade_numero_ancora'); ?>
            </label>
          </td>
          <td>
            <?php
            $Sunidade_numero = 'Unidade';
            db_input('unidade_numero', 5, 1, true, 'text', 1, 'onChange="buscarUnidade(false)"');
            db_input('unidade_descricao', 40, 0, true, 'text', 3);
            ?>
          </td>
        </tr>

        <tr>
          <td>
            <label for="Anexo">
              <?php db_ancora('Anexo:', 'buscarAnexo(true)', 1, null, 'anexo_ancora'); ?>
            </label>
          </td>
          <td>
            <?php
            $Sanexo = 'Anexo';
            db_input('anexo', 5, 1, true, 'text', 1, 'onChange="buscarAnexo(false)"');
            db_input('anexo_descricao', 40, 0, true, 'text', 3);
            ?>
          </td>
        </tr>
        
        <tr>
          <td>
            <label for="Fonte de Recurso">
              <?php db_ancora('Recurso:', 'buscarRecurso(true)', 1, null, 'recurso_ancora'); ?>
            </label>
          </td>
          <td>
            <?php
            $Srecurso = 'Recurso';
            db_input('recurso', 5, 1, true, 'text', 1, 'onChange="buscarRecurso(false)"');
            db_input('recurso_descricao', 40, 0, true, 'text', 3);
            ?>
          </td>
        </tr>        
        
        <tr>
          <td>Exercício:</td>
          <td>
            <?php
            db_input('exercicio', 5, 0, true, 'text', 1);
            ?>
          </td>
        </tr>

        <tr>
          <td>Formato:</td>
          <td>
            <select name="formato" style="width: 60px;">
               <option value="PDF">PDF</option>
               <option value="CSV">CSV</option>
           </select>
         </td>
        </tr>
  </table>
  </fieldset>
  <input  name="emite2" id="emite2" type="button" value="Emitir Relatório" onclick="js_emite();" >
  </div>
  </form>
<?
  db_menu();
?>
</body>
</html>
<script>
function js_emite(){
  var sUrl = "emp2_liquidacoesaptasrepassefinanceiroOrdemCronologica002_natal.php?";
  sUrl += "orgao="+document.form1.orgao_numero.value;
  sUrl += "&unidade="+document.form1.unidade_numero.value;
  sUrl += "&anexo="+document.form1.anexo.value;
  sUrl += "&recurso="+document.form1.recurso.value;
  sUrl += "&exercicio="+document.form1.exercicio.value;
  sUrl += "&formato="+document.form1.formato.value;
  jan = window.open(sUrl,'emp2_liquidacoesaptasrepassefinanceiroOrdemCronologica002_natal','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}

function buscarOrgao(lMostrar) {

  var sQuerySring = 'funcao_js=parent.retornoOrgao|0|2';
  var sArquivo    = 'func_orcorgao.php';
  var sTituloTela = 'Pesquisar Órgão';

  if (!lMostrar) {
    sQuerySring = 'pesquisa_chave=' + $F('orgao_numero') + '&funcao_js=parent.retornoOrgaoChave';
  }

  js_OpenJanelaIframe('', 'db_iframe_orcorgao', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
}

function retornoOrgaoChave(sDescricao, lErro) {

	$('unidade_numero').value    = '';
    $('unidade_descricao').value = '';

    if (lErro) {
      $('orgao_numero').value = "";  
    }
    $('orgao_descricao').value = sDescricao;
    
}

function retornoOrgao(iCodigo, sDescricao, lErro) {

	$('unidade_numero').value    = '';
    $('unidade_descricao').value = '';

    $('orgao_numero').value = iCodigo;
    $('orgao_descricao').value = sDescricao;
    
    db_iframe_orcorgao.hide();
    
}


function buscarUnidade(lMostrar) {

  var iOrgao = $F('orgao_numero');

  if (iOrgao == '') {

    alert("Para selecionar uma unidade, você deve primeiro informar o Órgão.");
    return false;
  }

  var sQuerySring = 'orgao=' + iOrgao + '&funcao_js=parent.retornoUnidade|2|4';
  var sArquivo    = 'func_orcunidade.php';
  var sTituloTela = 'Pesquisar Unidade';

  if (!lMostrar) {
    sQuerySring = 'pesquisa_chave=' + $F('unidade_numero') + '&orgao=' + iOrgao + '&funcao_js=parent.retornoUnidadeChave';
  }

  js_OpenJanelaIframe('', 'db_iframe_orcunidade', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
}	  

function retornoUnidade(iCodigo, sDescricao, lErro) {

	$('unidade_numero').value    = iCodigo;
    $('unidade_descricao').value = sDescricao;
	
    db_iframe_orcunidade.hide();
}

function retornoUnidadeChave(sDescricao, lErro) {

    if (lErro) {
      $('unidade_numero').value = "";  
    }
    $('unidade_descricao').value = sDescricao;
    
}

function buscarAnexo(mostra){
  if (mostra==true) {
    js_OpenJanelaIframe('','db_iframe_anexo','func_ppasubtitulolocalizadorgasto.php?funcao_js=parent.retornoAnexo1|o11_sequencial|o11_descricao','Pesquisa',true);
  } else {
    js_OpenJanelaIframe('','db_iframe_anexo','func_ppasubtitulolocalizadorgasto.php?pesquisa_chave='+document.form1.anexo.value+'&funcao_js=parent.retornoAnexo','Pesquisa',false);
  }
}
function retornoAnexo(chave,erro){
  document.form1.anexo_descricao.value = chave; 
  if (erro==true) { 
    document.form1.anexo.focus(); 
    document.form1.anexo.value = ''; 
  }
}
function retornoAnexo1(chave1,chave2){
  document.form1.anexo.value = chave1;
  document.form1.anexo_descricao.value = chave2;
  db_iframe_anexo.hide();
}

function buscarRecurso(mostra){

  var sRecursosAnteriores = "";
  if (document.form1.anexo.value == 5) {
	  sRecursosAnteriores = "ativo=1&";
  }	  	
  
  if (mostra==true) {
    js_OpenJanelaIframe('','db_iframe_recurso','func_orctiporec.php?'+sRecursosAnteriores+'funcao_js=parent.retornoRecurso1|o15_codigo|o15_descr','Pesquisa',true);
  } else {
    js_OpenJanelaIframe('','db_iframe_recurso','func_orctiporec.php?'+sRecursosAnteriores+'pesquisa_chave='+document.form1.recurso.value+'&funcao_js=parent.retornoRecurso','Pesquisa',false);
  }
}

function retornoRecurso(chave,erro){
  document.form1.recurso_descricao.value = chave; 
  if (erro==true) { 
    document.form1.recurso.focus(); 
    document.form1.recurso.value = ''; 
  }
}

function retornoRecurso1(chave1,chave2){
  document.form1.recurso.value = chave1;
  document.form1.recurso_descricao.value = chave2;
  db_iframe_recurso.hide();
}

</script>
