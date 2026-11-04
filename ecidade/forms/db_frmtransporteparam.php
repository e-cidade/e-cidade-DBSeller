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

//MODULO: educação
$cltransporteparam->rotulo->label();
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
 <tr>
  <td nowrap title="<?=@$Ted224_f_limitekm?>">
   <?=@$Led224_f_limitekm?>
  </td>
  <td>
   <?php db_input('ed224_f_limitekm',5,$Ied224_f_limitekm,true,'text',$db_opcao,"")?>
   <?php db_input('ed224_i_codigo',10,$Ied224_i_codigo,true,'hidden',3,"")?>
  </td>
 </tr>
</table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
</form>
<script>
function js_pesquisa(){
 js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_transporteparam','func_transporteparam.php?funcao_js=parent.js_preenchepesquisa|ed224_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
 db_iframe_transporteparam.hide();
 <?php
 if($db_opcao!=1){
   echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
 }
 ?>
}
</script>
