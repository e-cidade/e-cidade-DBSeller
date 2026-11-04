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

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
td {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
}
input {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        height: 17px;
        border: 1px solid #999999;
}

</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script>
function js_testacamp(){
  var lanc = document.form1.nl01_codlanc.value;
  if(lanc == ""){
    alert("Informe um código para prosseguir!");
    return false;
  }
  document.form1.action = 'fis3_fis_fandamlancamento00<?=$db_opcao?>.php?pri=true&abas=1&nl01_codlanc='+lanc;

  var nl01_codlanc = document.getElementById('nl01_codlanc');
  var z01_nome = document.getElementById('z01_nome');

  if(z01_nome.value != '' && z01_nome.value != 'Chave('+nl01_codlanc.value+') não Encontrado'){
    document.form1.submit();
  }else{
    return false;
  }

}
</script>
<table height="430" width="790" border="1" valign="top" cellspacing="0" cellpadding="0" bgcolor="#cccccc">
  <tr>
  <td align="center" valign="top" bgcolor="#cccccc">
  <form name="form1" method="post" action=""  onSubmit="return js_verifica_campos_digitados();" >
   <table border="0" cellspacing="0" cellpadding="0">
   <br>
   <br>
    <tr>
      <td title=" Notificação de Lançamento" >
      <?php
      db_ancora("<b>Notificação de Lançamento</b>",' js_lancamento(true); ',1);
      ?>
      </td>
      <td title="Notificacao de Lançamento" colspan="4">
      <?php
      db_input('nl01_codlanc',6,@$Inl01_codlanc,true,'text',1,"onchange='js_lancamento(false)'");
      db_input('z01_nome',40,0,true,'text',3);
      ?>
      </td>
    </tr>
     <tr>
       <td colspan="2" align="center">
     <br>
	   <input type="button" name="vistoria" value="Confirma" onclick="return js_testacamp();" >
       </td>
     </tr>
    </table>
  </form>
  </td>
  </tr>
</table>
</body>
</html>
<script>
function js_auto(mostra){
  var auto=document.form1.y50_codauto.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe','func_fis_andamento_auto.php?funcao_js=parent.js_mostraauto|dl_auto|z01_nome&fisauto=1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe','func_fis_andamento_auto.php?pesquisa_chave='+auto+'&funcao_js=parent.js_mostraauto1&fisauto=1','Pesquisa',false);
  }
}
function js_mostraauto(chave1,chave2){
  document.form1.y50_codauto.value = chave1;
  document.form1.y56_id_usuario.value = chave2;
  db_iframe.hide();
}
function js_mostraauto1(chave,erro){
  document.form1.y56_id_usuario.value = chave;
  if(erro==true){
    document.form1.y50_codauto.focus();
    document.form1.y50_codauto.value = '';
  }
}
function js_lancamento(mostra){
  var codlanc = document.form1.nl01_codlanc.value;
  if( mostra == true ){
    js_OpenJanelaIframe('','db_iframe_lanc','func_fis_notificacao_lancamento.php?funcao_js=parent.js_mostralanc|dl_Notificacao_Lancamento|z01_nome','Pesquisa',true);
  }else{
    if( codlanc != "" ){
      js_OpenJanelaIframe('','db_iframe_lanc','func_fis_notificacao_lancamento.php?pesquisa_chave='+codlanc+'&funcao_js=parent.js_mostralanc1','Pesquisa',false);
    }else{
      document.form1.z01_nome.value = "";
    }
  }
}
function js_mostralanc(chave1,chave2){

  document.form1.nl01_codlanc.value = chave1;
  document.form1.z01_nome.value     = chave2;
  db_iframe_lanc.hide();

}
function js_mostralanc1(chave,erro){

  document.form1.z01_nome.value = chave;
  if( erro == true ){
    document.form1.nl01_codlanc.focus();
    document.form1.nl01_codlanc.value = '';
  }else{
  }

}
</script>
