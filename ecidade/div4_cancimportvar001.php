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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("classes/db_issbase_classe.php");
require_once modification("classes/db_iptubase_classe.php");
require_once modification("classes/db_cgm_classe.php");
db_postmemory($_SERVER);
db_postmemory($_POST);
$db_botao=1;
$db_opcao=1;
$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$clissbase = new cl_issbase;
$clissbase->rotulo->label();
$clcgm = new cl_cgm;
$clcgm->rotulo->label();
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
<body bgcolor=#CCCCCC>  
<form class="container" name="form1" method="post" action="div4_cancimportvar002.php"  onSubmit="return js_verifica_campos_digitados();" >
  <fieldset>
  	<legend>Cancela Importação de ISSQN Variavel</legend>
    <table class="form-container">

      <tr>   
        <td>
          <?php db_ancora($Lq02_inscr,' js_inscr(true); ',1); ?>
        </td>
        <td> 
          <?php
            db_input('q02_inscr',10,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
            db_input('z01_nome',81,0,true,'text',3,"","z01_nomeinscr");
          ?>
        </td>
      </tr>

      <tr>
        <td title="CGM">
            <?php db_ancora("CGM",' js_cgm(true); ',1); ?>
        </td>
        <td> 
          <?php
            db_input('z01_numcgm',10,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'","z_numcgm");
            db_input('z01_nome',81,0,true,'text',3,"","nomeCGM");
          ?>
        </td>
      </tr>

    </table>
  </fieldset> 	 
  <input type="submit" name="processar" value="Processar" >
</form>

<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
  }
}
function js_mostrainscr(chave1,chave2){
  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;
  db_iframe.hide();
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nomeinscr.value = chave; 
  if(erro==true){ 
    document.form1.q02_inscr.focus(); 
    document.form1.q02_inscr.value = ''; 
  }
}

function js_cgm(mostra){
  var cgm=document.form1.z_numcgm.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?funcao_js=parent.js_mostracgm|0|1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
  }
}

function js_mostracgm(chave1,chave2){
  document.form1.z_numcgm.value = chave1;
  document.form1.nomeCGM.value  = chave2;
  db_iframe2.hide();
}

function js_mostracgm1(erro, nome) {
  if (erro) {
    document.form1.z_numcgm.value = '';
  }
  document.form1.nomeCGM.value = nome;
  document.form1.submit();
} 
</script>
