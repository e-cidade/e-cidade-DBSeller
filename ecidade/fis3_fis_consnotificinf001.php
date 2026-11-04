<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("dbforms/db_funcoes.php");
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("y30_codnoti");
$clrotulo->label("y30_numbloco");
db_postmemory($HTTP_POST_VARS);
// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------
// Ticket 108335
if(isset($intimacao) && $intimacao == 1){
  $Ty30_codnoti = 'Código da Intimação

Campo:y30_codnoti                             ';
  
  $Ly30_codnoti = '<strong>Código da Intimação:</strong>';

}
// -------------
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function js_consulta(){
   if (document.form1.y30_codnoti.value==""){
   	  if (document.form1.y30_numbloco.value!=""){
   	  	js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_notif','func_fis_fiscalalt.php?chave_y30_numbloco='+document.form1.y30_numbloco.value+'&funcao_js=parent.js_consulta2|y30_codnoti<?=$getIntimacao?>','Pesquisa',true);
   	  }else{
     	alert('Informe uma <?=$getLabel?>!!Campo vazio!!');
     	document.form1.y30_codnoti.focus();
   	  }
   }else{
     js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','fis3_fis_consnotificinf002.php?codfiscal='+document.form1.y30_codnoti.value+'<?=$getIntimacao?>','Consulta <?=$getLabel?>',true);
   }
}
function js_consulta2(y30_codnoti){
	db_iframe_notif.hide();
	js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','fis3_fis_consnotificinf002.php?codfiscal='+y30_codnoti+'<?=$getIntimacao?>','Consulta <?=$getLabel?>',true);
}
</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
      <center>
      <form name="form1" method="post" action="">
      <table border="0">
	<tr>   
	  <br><br>
	  <td title="<?=@$Ty30_codnoti?>" >
	  <?php 
	   db_ancora(@$Ly30_codnoti,' js_notif(true); ',1);
	  ?>
	  </td>    
	  <td title="<?=@$Ty30_codnoti?>" colspan="4">
	  <?php 
	   db_input('y30_codnoti',5,@$Iy30_codnoti,true,'text',1,"onchange='js_notif(false)'");
	   db_input('z01_nome',50,0,true,'text',3);
	  ?>
	  </td>
	</tr>
  <tr>
      <td nowrap title="<?=@$y100_sequencial?>"><?php db_ancora('Processo fiscal: ',"js_codprocfiscal(true);",1);?></td>
      <td>
          <?php
              db_input('y100_sequencial',6,$Iy50_codauto,true,'text',1,"onblur='js_codprocfiscal(false);'");
              db_input('y30_nome',35,$Iy50_nome,true,'text',3,'');
          ?>
      </td>
  </tr>
	<tr>
	<td title="<?=@$Ty30_numbloco?>" >
	  <?=@$Ly30_numbloco?>
	  </td>
	
	<td title="<?=@$Ty30_numbloco?>" colspan="4">
	  <?php 
	   db_input('y30_numbloco',10,0,true,'text',1);
	  ?>
	  </td>
	 </tr>
	<tr>
	<td colspan=2 align=center >
	     <input type=button name='processar'  value='Processar' onclick='js_consulta();' >
	</td>
	</tr>
       </table>
       </center>
       </form>
</table>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_notif(mostra){
  var notif=document.form1.y30_codnoti.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_notif','func_fis_consulta_intimacao.php?funcao_js=parent.js_mostranotif|dl_Codigo|z01_nome<?=$getIntimacao?>','Pesquisa',true);
  }else{
    if(notif!=""){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_notif','func_fis_consulta_intimacao.php?pesquisa_chave='+notif+'&funcao_js=parent.js_mostranotif1<?=$getIntimacao?>','Pesquisa',false);
    }else{
      document.form1.z01_nome.value="";
      document.form1.submit();  
    }
  }
}
function js_mostranotif(chave1,chave2){
  document.form1.y30_codnoti.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_notif.hide();
  document.form1.submit(); 
}
function js_mostranotif1(chave,erro){
  document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.y30_codnoti.focus(); 
    document.form1.y30_codnoti.value = ''; 
  }else{
    document.form1.submit();
  }
}
function js_codprocfiscal(mostra){
    if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_auto', 'func_fis_intimacao.php?funcao_js=parent.js_mostracodprocfiscal1|dl_Codigo|dl_Processo_Fiscal|z01_nome<?=$getIntimacao?>','Pesquisa',true)
    } else {
        y100_sequencial = document.form1.y100_sequencial.value;
        if (y100_sequencial != '') {
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_auto','func_fis_intimacao.php?pesquisa_chave='+y100_sequencial+'&funcao_js=parent.js_mostracodprocfiscal1|dl_Codigo|dl_Processo_Fiscal|z01_nome<?=$getIntimacao?>','Pesquisa',true);
        } else {
     document.getElementById('y30_codnoti').value = '';
     document.getElementById('z01_nome').value = '';
     document.getElementById('y30_nome').value = '';
  }
    }
}

function js_mostracodprocfiscal1(chave1,chave2,chave3){
    document.form1.y30_codnoti.value = chave1
    document.form1.y100_sequencial.value = chave2;
    document.form1.z01_nome.value    = chave3;
    document.form1.y30_nome.value = chave3;
    db_iframe_auto.hide();
}

function js_mostracodprocfiscal(chave,erro){
    document.form1.z01_nome.value = chave;
    if(erro==true){
        document.form1.y100_sequencial.focus();
        document.form1.y100_sequencial.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nome.value = '';
  document.form1.y30_nome.value = '';
    }
}
</script>
