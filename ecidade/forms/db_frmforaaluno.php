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
$claluno->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed47_v_nome");
$clrotulo->label("ed48_i_codigo");
$escola = db_getsession("DB_coddepto");
?>
<form name="form1" method="post" action="" enctype="multipart/form-data">
<center>
<table border="0" width="100%">
 <tr>
  <td nowrap title="<?=@$Ted47_i_codigo?>" width="25%">
   <?php db_ancora(@$Led47_i_codigo,"",$db_opcao1);?>
  </td>
  <td>
   <?php db_input('ed47_i_codigo',20,$Ied47_i_codigo,true,'text',$db_opcao1,"")?>
   <?php db_input('ed47_v_nome',40,$Ied47_v_nome,true,'text',3,'')?>
  </td>
  <td rowspan="6" valign="top" align="center">
   <iframe name="frame_imagem" id="frame_imagem" src="edu4_mostraimagem.php" width="110" height="125" frameborder="1" scrolling="no"></iframe>
   <?php
   if((isset($chavepesquisa) || isset($alterar)) && isset($ed47_c_foto)){
    if($ed47_o_oid!=0){
     $arquivo = "tmp/".$ed47_c_foto;
     db_query("begin");
     pg_loexport($ed47_o_oid,$arquivo);
     db_query("end");
     if($db_botao==true){
      echo "<br><a href='?chavepesquisa=$chavepesquisa&excluirfoto'>Excluir Foto</a>";
     }
    }else{
     $arquivo = "imagens/none1.jpeg";
    }
   ?>
   <script>
    frame_imagem.location.href="edu4_mostraimagem.php?imagem_gerada=<?=$arquivo?>";
   </script>
   <?php }?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted47_c_codigoinep?>">
   <?=@$Led47_c_codigoinep?>
  </td>
  <td>
   <?php db_input('ed47_c_codigoinep',20,$Ied47_c_codigoinep,true,'text',$db_opcao,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted47_c_certidaotipo?>">
   <?=@$Led47_c_certidaotipo?>
  </td>
  <td>
   <?php
   $x = array('N'=>'NASCIMENTO','C'=>'CASAMENTO');
   db_select('ed47_c_certidaotipo',$x,true,$db_opcao,"");
   ?>
   <input type="button" value="+" name="certidao" onclick="document.getElementById('certidaoadic').style.visibility='visible'" style="width:10px;">
   <?=@$Led47_c_raca?>
   <?php
   $x = array('NÃO DECLARADA'=>'NÃO DECLARADA','BRANCA'=>'BRANCA','PRETA'=>'PRETA','PARDA'=>'PARDA','AMARELA'=>'AMARELA','INDÍGENA'=>'INDÍGENA');
   db_select('ed47_c_raca',$x,true,$db_opcao,"");
   ?>
   <table id="certidaoadic" style="visibility:hidden;position:absolute;border:2px outset #000000;" bgcolor="#CCCCCC" cellspacing="2" cellpading="2">
    <tr>
     <td colspan="2">
      <table width="100%" cellspacing="0" cellpading="0" style="border:2px outset #000000;">
       <tr bgcolor="blue" >
        <td style="color:#FFFFFF;font-weight:bold;">
         &nbsp;&nbsp;Dados adicionais da certidão:
        </td>
        <td width="10%" align="right" style="color:#FFFFFF;font-weight:bold;">
         <img src="imagens/jan_fechar_off.jpg" align="center" onclick="document.getElementById('certidaoadic').style.visibility='hidden'">
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaonum?>
     </td>
     <td>
      <?php db_input('ed47_c_certidaonum',20,$Ied47_c_certidaonum,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaolivro?>
     </td>
     <td>
      <?php db_input('ed47_c_certidaolivro',20,$Ied47_c_certidaolivro,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaofolha?>
     </td>
     <td>
      <?php db_input('ed47_c_certidaofolha',20,$Ied47_c_certidaofolha,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaodata?>
     </td>
     <td>
      <?php db_inputdata('ed47_c_certidaodata',@$ed47_c_certidaodata_dia,@$ed47_c_certidaodata_mes,@$ed47_c_certidaodata_ano,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaocart?>
     </td>
     <td>
      <?php db_input('ed47_c_certidaocart',30,$Ied47_c_certidaocart,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_c_certidaomunic?>
     </td>
     <td>
      <?php db_input('ed47_c_certidaomunic',40,$Ied47_c_certidaomunic,true,'text',$db_opcao,"")?>
     </td>
    </tr>
    <tr>
     <td>
      <?=@$Led47_i_censoufcert?>
     </td>
     <td>
      <?php db_input('ed47_i_censoufcert',2,$Ied47_i_censoufcert,true,'text',$db_opcao,"")?>
     </td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted47_i_censomunicnat?>">
   <?=@$Led47_i_censomunicnat?>
  </td>
  <td colspan="2">
   <?php db_input('ed47_i_censomunicnat',30,$Ied47_i_censomunicnat,true,'text',$db_opcao,"")?>
   <?=@$Led47_i_censoufnat?>
   <?php db_input('ed47_i_censoufnat',2,$Ied47_i_censoufnat,true,'text',$db_opcao,"")?>
  </td>
 </tr>db_frmforaaluno.php
 <tr>
  <td nowrap title="<?=@$Ted47_c_nomeresp?>">
   <?=@$Led47_c_nomeresp?>
  </td>
  <td colspan="2">
   <?php db_input('ed47_c_nomeresp',40,$Ied47_c_nomeresp,true,'text',$db_opcao,"")?>
  </td>
 </tr>
 <tr>
  <td>
   <?=@$Led47_c_emailresp?>
  </td>
  <td colspan="2">
   <?php db_input('ed47_c_emailresp',40,$Ied47_c_emailresp,true,'text',$db_opcao,"")?>
  </td>
 <tr>
 </tr>
  <td nowrap>
   <?=@$Led47_c_atendesp?>
  </td>
  <td colspan="2">
   <?php
   $x = array(''=>'','HOSPITALAR'=>'HOSPITALAR','DOMICILIAR'=>'DOMICILIAR');
   db_select('ed47_c_atendesp',$x,true,$db_opcao,"");
   ?>
   <?=@$Led47_c_transporte?>
   <?php
   $x = array(''=>'','MUNICIPAL'=>'MUNICIPAL','ESTADUAL'=>'ESTADUAL');
   db_select('ed47_c_transporte',$x,true,$db_opcao,"onchange='js_transporte(this.value)';");
   ?>
   <?=@$Led47_c_zona?>
   <?php
   $x = array(''=>'','URBANA'=>'URBANA','RURAL'=>'RURAL');
   db_select('ed47_c_zona',$x,true,$db_opcao,"onchange='js_transporte1(document.form1.ed47_c_transporte.value,this.value)';");
    ?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted47_c_nis?>">
   <?=@$Led47_c_nis?>
  </td>
  <td colspan="2">
   <?php db_input('ed47_c_nis',20,$Ied47_c_nis,true,'text',$db_opcao,"")?>
   <?=@$Led47_c_bolsafamilia?>
   <?php
   $x = array('N'=>'NÃO','S'=>'SIM');
   db_select('ed47_c_bolsafamilia',$x,true,$db_opcao,"");
   ?>
   <?=@$Led47_c_passaporte?>
   <?php db_input('ed47_c_passaporte',20,$Ied47_c_passaporte,true,'text',$db_opcao,"")?>  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted47_c_foto?>">
   <b>Foto:</b>
  </td>
  <td colspan="2">
   <iframe name="frame_file" id="frame_file" src="edu1_framefile.php" width="100%" height="25" frameborder="0" scrolling="no"></iframe>
  </td>
 </tr>
 <tr>
  <td colspan="3">
   <table width="100%">
    <tr>
     <td nowrap title="<?=@$Ted47_t_obs?>">
      <?=@$Led47_t_obs?><br>
      <?php db_textarea('ed47_t_obs',4,60,$Ied47_t_obs,true,'text',$db_opcao,"")?>
     </td>
     <td>
      <?=@$Led47_v_contato?><br>
      <?php db_textarea('ed47_v_contato',4,60,$Ied47_v_contato,true,'text',$db_opcao,"")?>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</center>
<input name="alterar" type="submit" value="Alterar" <?=($db_botao==false?"disabled":"")?>>
<input name="ed47_o_oid" type="hidden" id="ed47_o_oid" value="<?=@$ed47_c_foto?>" size="30">
</form>
<script>
function js_transporte(transporte){
 if(transporte==""){
  document.form1.ed47_c_zona.value="";
 }
}
function js_transporte1(transporte,zona){
 if(transporte==""){
  document.form1.ed47_c_zona.value="";
 }
 if(zona==""){
  document.form1.ed47_c_transporte.value="";
 }
}
function js_novo(){
 parent.location="edu1_alunoforaabas001.php";
}
</script>
