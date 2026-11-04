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

include_once(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
if(isset($opcao) && $opcao == "alterar"){
  //die('************************');
  echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu002.php?chavepesquisa=$nl14_codlanc&chavepesquisa1=$db_usuario&nl14_codlanc=$nl14_codlanc&y39_codandam=$y39_codandam'</script>";}
if(isset($opcao) && $opcao == "excluir"){
  echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu003.php?chavepesquisa=$nl14_codlanc&chavepesquisa1=$db_usuario&nl18_codlanc=$nl18_codlanc&y39_codandam=$y39_codandam'</script>";
}

$rsConsultaProc = db_query(" select nl09_procfiscal as procfiscal from fiscalizacao.fis_procfiscallanc where nl09_lanc = $nl14_codlanc");
if( pg_num_rows( $rsConsultaProc ) > 0 ){
    db_fieldsmemory($rsConsultaProc,0);
} 

?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap>
       <?php
       db_ancora("<strong>Código da Notificação:</strong>","js_pesquisanl14_codlanc(true);",3);
       ?>
    </td>
    <td>
      <?php

        db_input('nl14_codlanc',10,$Inl14_codlanc,true,'text',3," onchange='js_pesquisanl14_codlanc(false);'");
        //echo "<script>document.form1.nl14_codlanc.value='$nl18_codlanc'</script>";
        db_input('nl18_codlanc',10,$Inl18_codlanc,true,'hidden',3,"");
        db_input('y39_codandam',20,$Iy39_codandam,true,'hidden',3,"");
        db_input('nl01_nome',40,$Inl01_nome,true,'text',3,'');
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap>
    
       <?php
       db_ancora("<strong>Numcgm:</strong>","js_pesquisanl14_id_usuario(true);",$db_opcao);
       ?>
    </td>
    <td>
       <?php

        db_input('nl14_id_usuario',10,1,true,'text',$db_opcao," onchange='js_pesquisanl14_id_usuario(false);'");
        if($db_opcao == 2){

          db_input('nl14_id_usuario',10,1,true,'hidden',$db_opcao,"","nl14_id_usuario_old");
          echo "<script>document.form1.nl14_id_usuario_old.value = '$nl14_id_usuario'</script>";
        }

        db_input('nome',40,$Inome,true,'text',3,'');
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl14_obs?>">
       <?=@$Lnl14_obs?>
    </td>
    <td>
      <?php
        db_textarea('nl14_obs',3,50,$Inl14_obs,true,'text',$db_opcao,"")
      ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">
      <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
      <?php
      if(($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33)){
      ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="location.href='fis1_fis_lancusu001.php?nl18_codlanc=<?=$nl18_codlanc?>'">
      <?php
      }
      ?>
    </td>
  </tr>
  <tr>
    <td align="top" colspan="2">
   <?php
     $chavepri = array("db_lancamento"=>@$nl14_codlanc, "db_usuario"=>@$nl14_id_usuario);
     $cliframe_alterar_excluir->chavepri      = $chavepri;
     $cliframe_alterar_excluir->campos        = "db_lancamento,db_usuario,db_obs,nome";
     $cliframe_alterar_excluir->sql           = $cllancusu->sql_query("",""," nl14_codlanc as db_lancamento,nl14_id_usuario as db_usuario,nl14_obs as db_obs,db_usuarios.*",""," nl14_codlanc = $nl14_codlanc");
     $cliframe_alterar_excluir->legenda       = "Fiscais da Vistoria";
     $cliframe_alterar_excluir->msg_vazio     = "<font size='1'>Nenhum Usuário Cadastrado!</font>";
     $cliframe_alterar_excluir->textocabec    = "darkblue";
     $cliframe_alterar_excluir->textocorpo    = "black";
     $cliframe_alterar_excluir->fundocabec    = "#aacccc";
     $cliframe_alterar_excluir->fundocorpo    = "#ccddcc";
     $cliframe_alterar_excluir->iframe_height = "170";
     $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
     $cllancusu1 = new cl_fis_lancusu;
     //die('***************************'.$cllancusu1->sql_query("",""," fis_lancusu.*,db_usuarios.*",""," nl14_codlanc = $nl14_codlanc limit 1"));
     $cllancusu1->sql_record($cllancusu1->sql_query("",""," fis_lancusu.*,db_usuarios.*",""," nl14_codlanc = $nl14_codlanc"));

     if($cllancusu1->numrows == 0){
   ?>
      <p align="center"><small>*A Notificação de Lançamento deve ter no mínimo um fiscal cadastrado!</small></p>
   <?php
     }
   ?>
   </td>
 </tr>
  </table>
  </center>
</form>
<script>
function js_setatabulacao(){
  js_tabulacaoforms("form1","nl14_id_usuario",true,1,"nl14_id_usuario",true);
}
function js_pesquisanl14_codlanc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_lancamento','func_fis_lancamento.php?funcao_js=parent.js_mostralancamento1|nl01_codlanc|nl01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_lancamento','func_fis_lancamento.php?pesquisa_chave='+document.form1.nl14_codlanc.value+'&funcao_js=parent.js_mostralancamento','Pesquisa',false);
  }
}
function js_mostralancamento(chave,erro){
  document.form1.nl01_nome.value = chave;
  if(erro==true){
    document.form1.nl14_codlanc.focus();
    document.form1.nl14_codlanc.value = '';
  }
}
function js_mostralancamento1(chave1,chave2){
  document.form1.nl14_codlanc.value = chave1;
  document.form1.nl01_nome.value = chave2;
  db_iframe_lancamento.hide();
}
function js_pesquisanl14_id_usuario(mostra){
<?php 
if(isset($procfiscal) && $procfiscal != ""){
?>  
sUrl = "&procfiscal="+<?php  echo $procfiscal ?>;
<?php 
}else{?>
 sUrl = "";
<?php }?> 

  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_db_usuarios','func_fis_cadfiscaisdepto.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome'+sUrl,'Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_db_usuarios','func_fis_cadfiscaisdepto.php?pesquisa_chave='+document.form1.nl14_id_usuario.value+'&funcao_js=parent.js_mostradb_usuarios'+sUrl,'Pesquisa',false);
  }
}
function js_mostradb_usuarios(chave,chave1,erro){
  document.form1.nome.value = chave1;
  if(erro==true){
    document.form1.nl14_id_usuario.focus();
    document.form1.nl14_id_usuario.value = '';
  }
}
function js_mostradb_usuarios1(chave1,chave2){
  document.form1.nl14_id_usuario.value = chave1;
  document.form1.nome.value = chave2;
  db_iframe_db_usuarios.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_lancusu','func_fis_lancusu.php?funcao_js=parent.js_preenchepesquisa|nl14_codlanc|1','Pesquisa',true);
}
function js_preenchepesquisa(chave,chave1){
  db_iframe_lancusu.hide();
}
</script>
<?php
if(isset($nl14_codlanc) && $nl14_codlanc != ""){
  echo "<script>js_pesquisanl14_codlanc(false)</script>";
}
?>
