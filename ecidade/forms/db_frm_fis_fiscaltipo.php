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

//MODULO: fiscal
include modification("dbforms/db_classesgenericas.php");
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clfiscaltipo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y30_data");
$clrotulo->label("y30_dtvenc");
$clrotulo->label("y29_descr");
$clrotulo->label("y39_codandam");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
if(isset($opcao) && $opcao == "alterar"){
  echo "<script>parent.iframe_fiscaltipo.location.href='fis1_fis_fiscaltipo002.php?chavepesquisa=$y31_codnoti&chavepesquisa1=$y31_codtipo$getIntimacao'</script>";
}
if(isset($opcao) && $opcao == "excluir"){
  echo "<script>parent.iframe_fiscaltipo.location.href='fis1_fis_fiscaltipo003.php?chavepesquisa=$y31_codnoti&chavepesquisa1=$y31_codtipo$getIntimacao'</script>";
}

// Ticket 108335
if(isset($intimacao) && $intimacao == 1){
  $Ty31_codnoti = 'Código da Intimação

Campo:y31_codnoti                             ';

  $Ty30_data = 'Data da Intimação

Campo:y30_data                             ';
  
  $Ly31_codnoti = '<strong>Código da Intimação:</strong>';
  $Ly30_data    = '<strong>Data da Intimação:</strong>';

}
// -------------
?>
<form name="form1" method="post" action="">
<input type='hidden' name='andamento' value=''>
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ty31_codnoti?>">
       <?php 
       db_ancora(@$Ly31_codnoti,"",3);
       ?>
    </td>
    <td> 
<?php 
db_input('y31_codnoti',10,$Iy31_codnoti,true,'text',3," ");
db_input('y39_codandam',10,$Iy39_codandam,true,'hidden',3,"");
?>
       <?php 
db_input('y30_data',50,$Iy30_data,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty31_codtipo?>">
       <?php 
       db_ancora(@$Ly31_codtipo,"js_pesquisay31_codtipo(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php 
db_input('y31_codtipo',10,$Iy31_codtipo,true,'text',$db_opcao," onchange='js_pesquisay31_codtipo(false);'");
db_input('y31_codtipo',10,$Iy31_codtipo,true,'hidden',$db_opcao,"","y31_codtipo_old");
echo "<script>document.form1.y31_codtipo_old.value='".@$y31_codtipo."'</script>";
?>
       <?php 
db_input('y29_descr',50,$Iy29_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"> 
      <input <?php ($db_opcao == 1?"disabled":"")?> name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
      <?php 
      if(($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33)){
      ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="location.href='fis1_fis_fiscaltipo001.php?y31_codnoti=<?=$y31_codnoti?>&abas=1<?=$getIntimacao?>'" onblur="js_setatabulacao();">
      <?php 
      }
      ?>
    </td>
  </tr>  
  <tr>
    <td colspan="2" align="center">
    <?php 
    $chavepri= array("y31_codnoti"=>@$y31_codnoti,"y31_codtipo"=>@$y31_codtipo);
    $cliframe_alterar_excluir->chavepri=$chavepri;
    $cliframe_alterar_excluir->campos="y31_codtipo,y29_descr,y41_descr";
    $cliframe_alterar_excluir->sql=$clfiscaltipo->sql_query("",""," * ",""," y31_codnoti = $y31_codnoti");
    $cliframe_alterar_excluir->legenda="Procedências da $getLabel";
    $cliframe_alterar_excluir->msg_vazio ="<font size='1'>Nenhuma Procedência Cadastrada!</font>";
    $cliframe_alterar_excluir->textocabec ="darkblue";
    $cliframe_alterar_excluir->textocorpo ="black";
    $cliframe_alterar_excluir->fundocabec ="#aacccc";
    $cliframe_alterar_excluir->fundocorpo ="#ccddcc";
    $cliframe_alterar_excluir->iframe_height ="150";
    $cliframe_alterar_excluir->iframe_width ="700";
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);    
    ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <fieldset>
      <legend align="center"><strong>Escolha um Andamento Padrão</strong></legend>
<?php 
$clfiscaltipo1 = new cl_fis_fiscaltipo;
$clfiscalandam = new cl_fis_fiscalandam;
$clfandam      = new cl_fis_fandam;
$clfiscaltipo1->rotulo->label();
$result = $clfiscaltipo1->sql_record($clfiscaltipo1->sql_query("",""," distinct(y41_codtipo),y41_descr,y31_codnoti ",""," y31_codnoti = $y31_codnoti"));
if($clfiscaltipo1->numrows == 0){
  // echo "<script>parent.document.formaba.venc.disabled=true;</script>";
}
if($clfiscaltipo1->numrows > 0){
  db_fieldsmemory($result,0);
  $result1 = $clfiscalandam->sql_record($clfiscalandam->sql_query_file("",""," max(y49_codandam) as y49_codandam ",""," y49_codnoti = $y31_codnoti"));
  if($clfiscalandam->numrows > 0){
    db_fieldsmemory($result1,0);
    $result1 = $clfandam->sql_record($clfandam->sql_query_file("","*",""," y39_codandam = $y49_codandam"));
    if($clfandam->numrows > 0){
      db_fieldsmemory($result1,0);
      if($clfiscaltipo1->numrows == 1){
        if($y41_codtipo != $y39_codtipo){
          db_inicio_transacao();
	  $clfandam->y39_codtipo = $y41_codtipo;
	  $clfandam->y39_codandam = $y49_codandam;
	  $clfandam->alterar("");
          db_fim_transacao();
	}
      }
    }else{
      if(isset($y41_codtipo) && $y41_codtipo != ""){
	db_inicio_transacao();
	$clfandam->y39_codtipo = $y41_codtipo;
	$clfandam->y39_obs="0";
	$clfandam->y39_id_usuario= db_getsession("DB_id_usuario");
	$clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
	$clfandam->y39_hora=db_hora();
	$clfandam->incluir("");
	$clfiscalultandam->y19_codnoti = $y31_codnoti;
	$clfiscalultandam->y19_codandam = $clfandam->y39_codandam;
	$clfiscalultandam->incluir($y31_codnoti,$clfandam->y39_codandam);
	$clfiscalandam->y49_codnoti = $y31_codnoti;
	$clfiscalandam->y49_codandam = $clfandam->y39_codandam;
	$clfiscalandam->incluir($y31_codnoti,$clfandam->y39_codandam);
	$y39_codtipo = $y41_codtipo;
	db_fim_transacao();
      }
    }
  }else{
    if(isset($y41_codtipo) && $y41_codtipo != ""){
      db_inicio_transacao();
      $clfandam->y39_codtipo = $y41_codtipo;
      $clfandam->y39_obs="0";
      $clfandam->y39_id_usuario= db_getsession("DB_id_usuario");
      $clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
      $clfandam->y39_hora=db_hora();
      $clfandam->incluir("");
      $clfiscalultandam->y19_codnoti = $y31_codnoti;
      $clfiscalultandam->y19_codandam = $clfandam->y39_codandam;
      $clfiscalultandam->incluir($y31_codnoti,$clfandam->y39_codandam);
      $clfiscalandam->y49_codnoti = $y31_codnoti;
      $clfiscalandam->y49_codandam = $clfandam->y39_codandam;
      $clfiscalandam->incluir($y31_codnoti,$clfandam->y39_codandam);
      $y39_codtipo = $y41_codtipo;
      db_fim_transacao();
    }
  }
  // echo "<script>parent.document.formaba.receitas.disabled=false;</script>";
  echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
  // echo "<script>parent.document.formaba.artigos.disabled=false;</script>";
  // echo "<script>parent.document.formaba.venc.disabled=false;</script>";
  // echo "<script>parent.iframe_receitas.location.href='fis1_fis_fiscalrec001.php?y42_codnoti=".$y31_codnoti."&abas=1".$getIntimacao."';</script>\n";
  echo "<script>parent.iframe_fiscais.location.href='fis1_fis_fiscalusuario001.php?y38_codnoti=".$y31_codnoti."&abas=1&y39_codandam=$y39_codandam".$getIntimacao."';</script>\n";
  // echo "<script>parent.iframe_artigos.location.href='fis1_fis_fiscarquivos001.php?y26_codnoti=".$y31_codnoti."&abas=1&y39_codandam=$y39_codandam".$getIntimacao."';</script>\n";
  // echo "<script>parent.iframe_venc.location.href='fis1_fis_vencimento001.php?y30_codnoti=".$y31_codnoti."&abas=1&y39_codandam=$y39_codandam".$getIntimacao."';</script>\n";
  echo "<table cellpadding='1' cellspacing='2' border='0' width='600'>";
  for($i=0;$i<$clfiscaltipo1->numrows;$i++){
    db_fieldsmemory($result,$i);
    echo "<tr bgcolor='#999999'>
	    <td align='center' valign='center'>
	      <input  type='radio' name='tipoandam' ".($y41_codtipo == @$y39_codtipo?"checked":"")." value='$y41_codtipo' onChange='document.form1.andamento.value=this.value;document.form1.action=\"fis1_fis_fiscaltipo001.php$getAbas\";document.form1.submit();'>
	      ".($i == 0?"<script>document.form1.andamento.value='$y41_codtipo'</script>":"")."
	    </td>
	    <td><small>TIPO DE ANDAMENTO</small></td>
	    <td>$y41_descr</td>
	  </tr>";
  }
  echo "</table>";
}
?>
      </fieldset>
    </td>
  </tr>
  </table>
  </center>
</form>
<script>
function js_setatabulacao(){
  js_tabulacaoforms("form1","y31_codtipo",true,1,"y31_codtipo",true);
}
// function js_pesquisay31_codnoti(mostra){
//   if(mostra==true){
//     js_OpenJanelaIframe('','db_iframe_fiscal','func_fis_fiscal.php?funcao_js=parent.js_mostrafiscal1|y30_codnoti|y30_data<?=$getIntimacao?>','Pesquisa',true);
//   }else{
//     js_OpenJanelaIframe('','db_iframe_fiscal','func_fis_fiscal.php?pesquisa_chave='+document.form1.y31_codnoti.value+'&funcao_js=parent.js_mostrafiscal<?=$getIntimacao?>','Pesquisa',false);
//   }
// }
// function js_mostrafiscal(chave,erro){
//   document.form1.y30_data.value = chave; 
//   if(erro==true){ 
//     document.form1.y31_codnoti.focus(); 
//     document.form1.y31_codnoti.value = ''; 
//   }
// }
// function js_mostrafiscal1(chave1,chave2){
//   document.form1.y31_codnoti.value = chave1;
//   document.form1.y30_data.value = chave2;
//   db_iframe_fiscal.hide();
// }
function js_pesquisay31_codtipo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_fiscalproc','func_fis_fiscalprocaltnoti.php?funcao_js=parent.js_mostrafiscalproc1|y29_codtipo|y29_descr<?=$getIntimacao?>','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_fiscalproc','func_fis_fiscalprocaltnoti.php?pesquisa_chave='+document.form1.y31_codtipo.value+'&funcao_js=parent.js_mostrafiscalproc<?=$getIntimacao?>','Pesquisa',false);
  }
}
function js_mostrafiscalproc(chave,erro){
  document.form1.y29_descr.value = chave; 
  if(erro==true){ 
    document.form1.y31_codtipo.focus(); 
    document.form1.y31_codtipo.value = ''; 
  }else{
    document.form1.db_opcao.disabled=false;
  }
}
function js_mostrafiscalproc1(chave1,chave2){
  document.form1.y31_codtipo.value = chave1;
  document.form1.y29_descr.value = chave2;
  document.form1.db_opcao.disabled=false;
  db_iframe_fiscalproc.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_fiscaltipo','func_fis_fiscaltipo.php?funcao_js=parent.js_preenchepesquisa|y31_codnoti|1<?=$getIntimacao?>','Pesquisa',true);
}
function js_preenchepesquisa(chave,chave1){
  db_iframe_fiscaltipo.hide();
  <?php 
    if($db_opcao == 2 || $db_opcao == 22){
      echo " location.href = 'fis1_fis_fiscaltipo002.php?abas=1<?=$getIntimacao?>&chavepesquisa='+chave;";
    }elseif($db_opcao == 33 || $db_opcao == 3){
      echo " location.href = 'fis1_fis_fiscaltipo003.php?abas=1<?=$getIntimacao?>&chavepesquisa='+chave;";
    }
  ?>
}
</script>
<?php 
if(isset($y31_codnoti) && $y31_codnoti != ""){
  echo "<script>js_OpenJanelaIframe('','db_iframe_fiscal','func_fis_fiscal.php?pesquisa_chave=$y31_codnoti&funcao_js=parent.js_mostrafiscal".$getIntimacao."','Pesquisa',false);</script>";
}
?>