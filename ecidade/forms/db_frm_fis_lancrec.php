<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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

$cllancrec->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nl01_codlanc");
$clrotulo->label("nl18_codlanc");
$clrotulo->label("nl01_nome");
$clrotulo->label("k02_descr");
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
if(isset($opcao) && $opcao == "alterar"){
  echo "<script>parent.iframe_receitas.location.href='fis1_fis_lancrec002.php?chavepesquisa=$nl22_codlanc&chavepesquisa1=$nl22_receit&nl18_codlanc=$nl18_codlanc'</script>";
}
if(isset($opcao) && $opcao == "excluir"){
  echo "<script>parent.iframe_receitas.location.href='fis1_fis_lancrec003.php?chavepesquisa=$nl22_codlanc&chavepesquisa1=$nl22_receit&nl18_codlanc=$nl18_codlanc'</script>";
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tnl22_codlanc?>">
       <?php 
       db_ancora(@$Lnl22_codauto,"js_pesquisanl22_codlanc(true);",3);
       ?>
    </td>
    <td>
      <?php 
      db_input('nl22_codauto',10,$Inl22_codauto,true,'text',3," onchange='js_pesquisanl22_codlanc(false);'");
      echo "<script>document.form1.nl22_codauto.value='$nl18_codlanc'</script>";
      db_input('nl18_codlanc',10,$Inl18_codlanc,true,'hidden',3,"");
      ?>
       <?php 
db_input('nl01_nome',40,$Inl01_nome,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl22_receit?>">
       <?php 
       db_ancora(@$Lnl22_receit,"js_pesquisanl22_receit(true);",$db_opcao);
       ?>
    </td>
    <td>
<?php 
db_input('nl22_receit',10,$Inl22_receit,true,'text',$db_opcao," onchange='js_pesquisanl22_receit(false);'");
if($db_opcao == 2){
  db_input('nl22_receit',10,$Inl22_receit,true,'hidden',$db_opcao,"","nl22_receit_old");
  echo "<script>document.form1.nl22_receit_old.value = '$nl22_receit'</script>";
}
?>
       <?php 
db_input('k02_descr',40,$Ik02_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl22_descr?>">
       <?=@$Lnl22_descr?>
    </td>
    <td>
<?php 
db_input('nl22_descr',54,$Inl22_descr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr class="hide">
    <td nowrap title="<?=@$Tnl22_valor?>">
       <?=@$Lnl22_valor?>
    </td>
    <td>
<?php
db_input('nl22_valor',10,$Inl22_valor,true,'hidden',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
      <?php 
      if(($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33)){
      ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="location.href='fis1_fis_lancrec001.php?nl18_codlanc=<?=$nl18_codlanc?>'">
      <?php 
      }
      ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="top">
   <?php 
    $chavepri= array("nl22_codauto"=>@$nl22_codlanc,"nl22_receit"=>@$nl22_receit);
    $cliframe_alterar_excluir->chavepri=$chavepri;
    $cliframe_alterar_excluir->campos="nl22_codauto,nl22_receit,nl22_descr";
    $cliframe_alterar_excluir->sql=$cllancrec->sql_query("","","*",""," nl22_codauto = $nl18_codlanc");
    $cliframe_alterar_excluir->legenda="Receitas do Auto de Infração";
    $cliframe_alterar_excluir->msg_vazio ="<font size='1'>Nenhum Registro Encontrado!</font>";
    $cliframe_alterar_excluir->textocabec ="darkblue";
    $cliframe_alterar_excluir->textocorpo ="black";
    $cliframe_alterar_excluir->fundocabec ="#aacccc";
    $cliframe_alterar_excluir->fundocorpo ="#ccddcc";
    $cliframe_alterar_excluir->iframe_height ="170";
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    $cllancrec1 = new cl_fis_autorec;
    $result = $cllancrec1->sql_record($cllancrec1->sql_query($nl18_codlanc));
   ?>

    </td>
  </tr>
  </table>
  </center>
</form>
<script>
function js_setatabulacao(){
  js_tabulacaoforms("form1","nl22_receit",true,1,"nl22_receit",true);
}
function js_pesquisanl22_codlanc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_auto','func_fis_lancamento.php?funcao_js=parent.js_mostralanc1|nl01_codlanc|nl01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_auto','func_fis_lancamento.php?pesquisa_chave='+document.form1.nl22_codauto.value+'&funcao_js=parent.js_mostralanc','Pesquisa',false);
  }
}
function js_mostralanc(chave,erro){
  document.form1.nl01_nome.value = chave;
  if(erro==true){
    document.form1.nl22_codauto.focus();
    document.form1.nl22_codauto.value = '';
  }
}
function js_mostralanc1(chave1,chave2){
  document.form1.nl22_codauto.value = chave1;
  document.form1.nl01_nome.value = chave2;
  db_iframe_auto.hide();
}
function js_pesquisanl22_receit(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_tabrec','func_tabrec.php?funcao_js=parent.js_mostratabrec1|k02_codigo|k02_descr','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_tabrec','func_tabrec.php?pesquisa_chave='+document.form1.nl22_receit.value+'&funcao_js=parent.js_mostratabrec','Pesquisa',false);
  }
}
function js_mostratabrec(chave,erro){
  document.form1.k02_descr.value = chave;
  if(erro==true){
    document.form1.nl22_receit.focus();
    document.form1.nl22_receit.value = '';
  }
}
function js_mostratabrec1(chave1,chave2){
  document.form1.nl22_receit.value = chave1;
  document.form1.k02_descr.value = chave2;
  db_iframe_tabrec.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_autorec','func_fis_lancrec.php?funcao_js=parent.js_preenchepesquisa|nl22_codauto|1','Pesquisa',true);
}
function js_preenchepesquisa(chave,chave1){
  db_iframe_autorec.hide();
}
</script>
<?php 
if(isset($nl18_codlanc) && $nl18_codlanc != ""){
  echo "<script>js_pesquisanl22_codlanc(false)</script>";
}
?>
