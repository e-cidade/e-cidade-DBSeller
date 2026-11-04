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

include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$cllancrespons->rotulo->label();

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_POST_VARS);
if(isset($opcao) && $opcao == "alterar"){
  echo "<script>parent.iframe_responsavel.location.href='fis1_fis_lancrespons002.php?chavepesquisa=$nl12_codlanc&chavepesquisa1=$db_numcgm'</script>";
}
if(isset($opcao) && $opcao == "excluir"){
  echo "<script>parent.iframe_responsavel.location.href='fis1_fis_lancrespons003.php?chavepesquisa=$nl12_codlanc&chavepesquisa1=$db_numcgm'</script>";
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tnl12_codlanc?>">
       <?php 
       db_ancora("<strong>Código de Notificação:</strong>","js_pesquisanl12_codlanc(true);",3);
       ?>
    </td>
    <td>
    <?php 
    db_input('nl12_codlanc',10,'Notificação',true,'text',3," onchange='js_pesquisanl12_codlanc(false);'")
    ?>
    <?php 
    db_input('nl01_nome',40,'Notificação',true,'text',3,'');
    echo "<script>js_OpenJanelaIframe('','db_iframe_lanc','func_fis_lancamento.php?pesquisa_chave=$nl12_codlanc&funcao_js=parent.js_mostralanc','Pesquisa',false);</script>";
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl12_numcgm?>">
    <?php 
    db_ancora("<strong>Numcgm:</strong>","js_pesquisanl12_numcgm(true);",$db_opcao);
    ?>
    </td>
    <td>
    <?php 
    db_input('nl12_numcgm',10,'NumCGM',true,'text',$db_opcao," onchange='js_pesquisanl12_numcgm(false);'");
    if($db_opcao == 2){
      db_input('nl12_numcgm',10,'NumCGM',true,'hidden',$db_opcao," ","nl12_numcgm_old");
      echo "<script>document.form1.nl12_numcgm_old.value='$nl12_numcgm'</script>";
    }
    ?>
    <?php 
    db_input('z01_nome',40,'NomeCGM',true,'text',3,'')
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="Tipo">
    <b>Tipo: </b>
    </td>
    <td>
    <?php 
    db_select('nl12_tipo', array('0' => 'Selecione', '1' => 'SOLIDÁRIO', '2' => 'SUBSIDIÁRIO'), true, $db_opcao);
    ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">
      <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
      <?php 
      if(($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33)){
      ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="location.href='fis1_fis_lancrespons001.php?nl12_codlanc=<?=$nl12_codlanc?>'">
      <?php 
      }
      ?>
    </td>
  </tr>
  <tr>
    <td align="top" colspan="2">
   <?php 

    $chavepri= array("db_lancamento"=>$nl12_codlanc,"db_numcgm"=>@$nl12_numcgm);
    $cliframe_alterar_excluir->chavepri=$chavepri;
    $cliframe_alterar_excluir->campos="db_lancamento,db_numcgm,z01_nome, db_tipo";
    $cliframe_alterar_excluir->sql=$cllancrespons->sql_query("","","nl12_codlanc as db_lancamento,nl12_numcgm as db_numcgm, CASE nl12_tipo WHEN 1 THEN 'SOLIDÁRIO' ELSE 'SUBSIDIÁRIO' END AS db_tipo, cgm.*",""," nl12_codlanc = $nl12_codlanc");
    $cliframe_alterar_excluir->legenda="RESPONSÁVEIS DA NOTIFICAÇÃO DE LANÇAMENTO";
    $cliframe_alterar_excluir->msg_vazio ="<font size='1'>Nenhum registro encontrado!</font>";
    $cliframe_alterar_excluir->textocabec ="darkblue";
    $cliframe_alterar_excluir->textocorpo ="black";
    $cliframe_alterar_excluir->fundocabec ="#aacccc";
    $cliframe_alterar_excluir->fundocorpo ="#ccddcc";
    $cliframe_alterar_excluir->iframe_height ="170";
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
   </td>
 </tr>
  </table>
  </center>
</form>
<script>
function js_setatabulacao(){
  js_tabulacaoforms("form1","nl12_numcgm",true,1,"nl12_numcgm",true);
}
function js_pesquisanl12_numcgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.nl12_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
  }
}
function js_mostracgm(x,chave,erro){
  document.form1.z01_nome.value = chave;
  if(erro==true){
    document.form1.nl12_numcgm.focus();
    document.form1.nl12_numcgm.value = '';
  }
}
function js_mostracgm1(chave1,chave2){
  document.form1.nl12_numcgm.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_cgm.hide();
}
function js_pesquisanl12_codlanc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lanc','func_fis_lancamento.php?funcao_js=parent.js_mostralanc1|nl01_codlanc|nl01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lanc','func_fis_lancamento.php?pesquisa_chave='+document.form1.nl12_codlanc.value+'&funcao_js=parent.js_mostralanc','Pesquisa',false);
  }
}
function js_mostralanc(chave,erro){
  document.form1.nl01_nome.value = chave;
  if(erro==true){
    document.form1.nl12_codlanc.focus();
    document.form1.nl12_codlanc.value = '';
  }
}
function js_mostralanc1(chave1,chave2){
  document.form1.nl12_codlanc.value = chave1;
  document.form1.nl01_nome.value = chave2;
  db_iframe_lanc.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lancresponsavel','func_lancrespons.php?funcao_js=parent.js_preenchepesquisa|nl12_numcgm|1','Pesquisa',true);
}
function js_preenchepesquisa(chave,chave1){
  db_iframe_lancrespons.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave+'&chavepesquisa1='+chave1";
  }
  ?>
}
</script>
