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

require_once(modification("dbforms/db_classesgenericas.php"));

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

db_postmemory($HTTP_POST_VARS);

 if(isset($opcao) && $opcao == "alterar"){
   echo "<script>parent.iframe_lanclevanta.location.href='fis1_fis_lanclevanta002.php?chavepesquisa=$nl01_codlanc&chavepesquisa1=$db_levantamento'</script>";
 }
 if(isset($opcao) && $opcao == "excluir"){
   echo "<script>parent.iframe_lanclevanta.location.href='fis1_fis_lanclevanta003.php?chavepesquisa=$nl01_codlanc&chavepesquisa1=$db_levantamento'</script>";
 }
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <?php
  db_input('z01_numcgm',40,"",true,'hidden',$db_opcao,'','z01_numcgm');
  if (isset($z01_numcgm)) {
    echo "<script>document.form1.z01_numcgm.value = '$z01_numcgm'</script>";
  }

  db_input('q02_inscr',40,"",true,'hidden',$db_opcao,'','q02_inscr');
  if (isset($q02_inscr)) {
    echo "<script>document.form1.q02_inscr.value = '$q02_inscr'</script>";
  }

  db_input('z01_nomecgminscr',40,"",true,'hidden',$db_opcao,'','z01_nomecgminscr');
  if (isset($z01_nomecgminscr)) {
    echo "<script>document.form1.z01_nomecgminscr.value = '$z01_nomecgminscr'</script>";
  }

  if ( ($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33) ) {
    db_input('nl15_sequencial',10,$Inl15_sequencial,true,'hidden',$db_opcao,"","nl15_sequencial");
    echo "<script>document.form1.nl15_sequencial.value = '$nl15_sequencial'</script>";
  }
  ?>
  <tr>
     <td nowrap title="<?=@$Tnl01_codlanc?>">
       <b>Notificação de Lançamento</b>
    </td>
    <td>
      <?php 
        db_input('nl01_codlanc',10,'',true,'text',3," onchange='js_pesquisanl01_codlanc(false);'");
        db_input('nl01_nome',40,'',true,'text',3,'');
      ?>
    </td>
  </tr>
  <tr>
     <td nowrap title="<?=@$Ty60_codlev?>">
       <?php 
       db_ancora("<strong>Levantamento:</sctrong>","js_lev(true);",1);
       ?>
    </td>
    <td>
      <?php 
        db_input('y60_codlev',10,'',true,'text',$db_opcao," onchange='js_lev(false);'");
        db_input('z01_nome',40,'',true,'text',3,'');
        if ($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33) {
          echo "<script>document.form1.z01_nome.value = '$dbtxtnome_origem'</script>";
        }
      ?>
    </td>
  </tr>

  <tr>
    <td align="center" colspan="2">
      <?php
        if ($db_opcao == 1) {
          $sValorBotao = "Incluir";
        }else if ($db_opcao==2||$db_opcao==22) {
          $sValorBotao = "Alterar";
        } else if ($db_opcao==3||$db_opcao==33) {
          $sValorBotao = "Excluir";
        } else {
          $sValorBotao = $db_opcao;
        }
      ?>
      <input name="db_opcao" type="submit" id="db_opcao" value="<?php echo $sValorBotao?>" <?php echo ($db_botao==false?"disabled":"")?> >
        <?php
          if ( ($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33) ) {
            echo '<input name="novo" type="button" id="novo" value="Novo" onclick="location.href=\'fis1_fis_lanclevanta001.php?nl01_codlanc='.$nl01_codlanc.'\'">';
          }
        ?>
    </td>
  </tr>
  <tr>
    <td align="top" colspan="2">
      <?php
       $chavepri= array("db_notificacao"=>@$nl01_codlanc,"db_levantamento"=>@$y60_codlev);
       $cliframe_alterar_excluir->chavepri = $chavepri;
       $cliframe_alterar_excluir->campos   = "db_notificacao, db_levantamento";
       $cliframe_alterar_excluir->sql      = $cllanclevanta->sql_query("","nl15_lancamento as db_notificacao, nl15_levanta as db_levantamento",""," nl15_lancamento = $nl01_codlanc");
       $cliframe_alterar_excluir->legenda       = "Levantamentos vinculados ao Auto";
       $cliframe_alterar_excluir->msg_vazio     = "<font size='1'>Nenhum Levantamento Cadastrado!</font>";
       $cliframe_alterar_excluir->textocabec    = "darkblue";
       $cliframe_alterar_excluir->textocorpo    = "black";
       $cliframe_alterar_excluir->fundocabec    = "#aacccc";
       $cliframe_alterar_excluir->fundocorpo    = "#ccddcc";
       $cliframe_alterar_excluir->iframe_height = "170";
       $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
       ?>
    </td>
  </tr>
</table>
</center>
</form>
<script type="text/javascript">

function js_lev(mostra){

  var z01_numcgm        = document.form1.z01_numcgm.value;
  var q02_inscr         = document.form1.q02_inscr.value;
  var z01_nomecgminscr  = document.form1.z01_nomecgminscr.value;
  var nomeVar           = "";

  if (q02_inscr != "") {
    nomeVar = "z01_nomeinscr";
  }
  if (z01_numcgm != "") {
    nomeVar = "z01_nomecgm";
  }

  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe','func_fis_levantalanc.php?todos=true&responsavel=true&'+nomeVar+'='+z01_nomecgminscr+'&z01_numcgm='+z01_numcgm+'&q02_inscr='+q02_inscr+'&funcao_js=parent.js_mostralev1|y60_codlev|dbtxtnome_origem','Pesquisa',true);
  }else{

    console.log(z01_numcgm);
    console.log(q02_inscr);

    var y60_codlev = document.form1.y60_codlev.value;
    js_OpenJanelaIframe('','db_iframe','func_fis_levantalanc.php?todos=true&responsavel=true&'+nomeVar+'='+z01_nomecgminscr+'&pesquisa_chave='+y60_codlev+'&z01_numcgm='+z01_numcgm+'&q02_inscr='+q02_inscr+'&funcao_js=parent.js_mostralev','Pesquisa',false);
  }
}
function js_mostralev(chave,erro){

  if(erro==true){

    alert('Levantamento inválido.');
    document.form1.y60_codlev.value = "";
    document.form1.z01_nome.value   = "";
    document.form1.y60_codev.focus();
  } else{
    document.form1.z01_nome.value = chave;
  }
}
function js_mostralev1(chave1,chave2){
  document.form1.y60_codlev.value = chave1;
  document.form1.z01_nome.value   = chave2;
  db_iframe.hide();
}


function js_pesquisanl01_codlanc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_lanc','func_fis_lancamento.php?funcao_js=parent.js_mostralanc1|nl01_codlanc|nl01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_lanc','func_fis_lancamento.php?pesquisa_chave='+document.form1.nl01_codlanc.value+'&funcao_js=parent.js_mostralanc','Pesquisa',false);
  }
}
function js_mostralanc(chave,erro){

  document.form1.nl01_nome.value = chave;
  if(erro==true){
    document.form1.nl01_codlanc.focus();
    document.form1.nl01_codlanc.value = '';
  }
}
function js_mostralanc1(chave1,chave2){

  document.form1.nl01_codlanc.value = chave1;
  document.form1.nl01_nome.value    = chave2;
  db_iframe_lanc.hide();
}
</script>
<?php
if(isset($nl01_codlanc) && $nl01_codlanc != ""){
  echo "<script>js_pesquisanl01_codlanc(false)</script>";
}
?>
