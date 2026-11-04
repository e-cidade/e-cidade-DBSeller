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

//MODULO: educa��o
$claluno->rotulo->label();
$clrotulo = new rotulocampo;
$escola = db_getsession("DB_coddepto");
if($ed47_i_nacion==3){
 $db_opcao1 = 3;
}else{
 $db_opcao1 = 1;
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr valign="top">
  <td align="center">
   <?php db_ancora(@$Led47_i_codigo,"",3);?>
   <?php db_input('ed47_i_codigo',20,$Ied47_i_codigo,true,'text',3,"")?>
   <?php db_input('ed47_v_nome',40,$Ied47_v_nome,true,'text',3,'')?>
   <?=@$Led47_c_codigoinep?>
   <?php db_input('ed47_c_codigoinep',12,$Ied47_c_codigoinep,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td>
   <fieldset><legend><b>Certid�o</b></legend>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
     <tr>
      <td width="15%">
       <?=@$Led47_c_certidaotipo?>
      </td>
      <td>
       <?php
       $x = array(''=>'','N'=>'NASCIMENTO','C'=>'CASAMENTO');
       db_select('ed47_c_certidaotipo',$x,true,$db_opcao,"");
       ?>
       <?=@$Led47_c_certidaonum?>
       <?php db_input('ed47_c_certidaonum',8,$Ied47_c_certidaonum,true,'text',$db_opcao,"")?>
      </td>
     </tr>
     <tr>
      <td>
       <?=@$Led47_c_certidaofolha?>
      </td>
      <td>
       <?php db_input('ed47_c_certidaofolha',4,$Ied47_c_certidaofolha,true,'text',$db_opcao,"")?>
       <?=@$Led47_c_certidaolivro?>
       <?php db_input('ed47_c_certidaolivro',8,$Ied47_c_certidaolivro,true,'text',$db_opcao,"")?>
      </td>
     </tr>
     <tr>
      <td>
       <?=@$Led47_c_certidaocart?>
      </td>
      <td>
       <?php db_input('ed47_c_certidaocart',30,$Ied47_c_certidaocart,true,'text',$db_opcao," onKeyUp=\"js_ValidaCamposEdu(this,2,'$GLOBALS[Sed47_c_certidaocart]','t','t',event);\"")?>
       <?=@$Led47_c_certidaodata?>
       <?php db_inputdata('ed47_c_certidaodata',@$ed47_c_certidaodata_dia,@$ed47_c_certidaodata_mes,@$ed47_c_certidaodata_ano,true,'text',$db_opcao,"")?>
      </td>
     </tr>
     <tr>
      <td>
       <?=@$Led47_i_censoufcert?>
      </td>
      <td>
       <?php
       $result_uf = $clcensouf->sql_record($clcensouf->sql_query_file("","ed260_i_codigo,ed260_c_nome","ed260_c_nome"));
       db_selectrecord("ed47_i_censoufcert",$result_uf,"",$db_opcao,"","","","  ","iframe_uf.location.href='edu1_aluno004.php?campo=cert&censouf='+this.value",1);
       ?>
       <?=@$Led47_i_censomuniccert?>
       <?php
       if(isset($ed47_i_censoufcert) && $ed47_i_censoufcert!=""){
        $result_munic = $clcensomunic->sql_record($clcensomunic->sql_query_file("","ed261_i_codigo,ed261_c_nome","ed261_c_nome","ed261_i_censouf = $ed47_i_censoufcert"));
        if($clcensomunic->numrows==0){
         $x = array(' '=>'Selecione o Estado');
         db_select('ed47_i_censomuniccert',$x,true,$db_opcao,"");
        }else{
         db_selectrecord("ed47_i_censomuniccert",$result_munic,"",$db_opcao,"","","","  ","",1);
        }
       }else{
        $x = array(' '=>'Selecione o Estado');
        db_select('ed47_i_censomuniccert',$x,true,$db_opcao,"");
       }
       ?>
       <iframe name="iframe_uf" src="" framedorder="0" width="0" height="0" style="visibility:hidden;position:absolute;"></iframe>
      </td>
     </tr>
    </table>
   </fieldset>
  </td>
 </tr>
 <tr>
  <td>
   <fieldset><legend><b>Identidade</b></legend>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
     <tr>
      <td width="15%">
       <?=@$Led47_v_ident?>
      </td>
      <td>
       <?php db_input('ed47_v_ident',15,$Ied47_v_ident,true,'text',$db_opcao1);?>
       <?=@$Led47_v_identcompl?>
       <?php db_input('ed47_v_identcompl',4,@$Ied47_v_identcompl,true,'text',$db_opcao1);?>
       <?=@$Led47_i_censoufident?>
       <?php
       $result_uf = $clcensouf->sql_record($clcensouf->sql_query_file("","ed260_i_codigo,ed260_c_nome","ed260_c_nome"));
       db_selectrecord("ed47_i_censoufident",$result_uf,"",$db_opcao1,"","","","  ","",1);
       ?>
      </td>
     </tr>
     <tr>
      <td>
       <?=@$Led47_i_censoorgemissrg?>
      </td>
      <td>
       <?php
       $result_org = $clcensoorgemissrg->sql_record($clcensoorgemissrg->sql_query_file("","ed132_i_codigo,ed132_c_descr","ed132_c_descr"));
       db_selectrecord("ed47_i_censoorgemissrg",$result_org,"",$db_opcao1,"","","","  ","",1);
       ?>
       <?=@$Led47_d_identdtexp?>
       <?php db_inputdata('ed47_d_identdtexp',@$ed47_d_identdtexp_dia,@$ed47_d_identdtexp_mes,@$ed47_d_identdtexp_ano,true,'text',$db_opcao1);?>
      </td>
     </tr>
    </table>
   </fieldset>
  </td>
 </tr>
 <tr>
  <td>
   <fieldset><legend><b>CNH</b></legend>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
     <tr>
      <td width="15%">
       <?=@$Led47_v_cnh?>
      </td>
      <td>
       <?php db_input('ed47_v_cnh',15,$Ied47_v_cnh,true,'text',$db_opcao1,"");?>
       <?=@$Led47_v_categoria?>
       <?php
       $y = array(""=>"","A"=>"A","B"=>"B","C"=>"C","D"=>"D","E"=>"E");
       db_select('ed47_v_categoria',$y,true,$db_opcao1);
       ?>
       <?=@$Led47_d_dtemissao?>
       <?php db_inputdata('ed47_d_dtemissao',@$ed47_d_dtemissao_dia,@$ed47_d_dtemissao_mes,@$ed47_d_dtemissao_ano,true,'text',$db_opcao1);?>
      </td>
     </tr>
     <tr>
      <td>
       <?=@$Led47_d_dthabilitacao?>
      </td>
      <td>
       <?php db_inputdata('ed47_d_dthabilitacao',@$ed47_d_dthabilitacao_dia,@$ed47_d_dthabilitacao_mes,@$ed47_d_dthabilitacao_ano,true,'text',$db_opcao1);?>
       <?=@$Led47_d_dtvencimento?>
       <?php db_inputdata('ed47_d_dtvencimento',@$ed47_d_dtvencimento_dia,@$ed47_d_dtvencimento_mes,@$ed47_d_dtvencimento_ano,true,'text',$db_opcao1);?>
      </td>
     </tr>
    </table>
   </fieldset>
  </td>
 </tr>
 <tr>
  <td>
   <fieldset><legend><b>Outros</b></legend>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
     <tr>
      <td width="15%">
       <?=@$Led47_v_cpf?>
      </td>
      <td>
       <?php db_input('ed47_v_cpf',11,@$Ied47_v_cpf,true,'text',$db_opcao1,"onChange='js_verificacpf(this);'");?>
       <?php $desabpassaporte = $ed47_i_nacion!=3?"readOnly style='background:#DEB887'":""?>
       <?=@$Led47_c_passaporte?>
       <?php db_input('ed47_c_passaporte',20,$Ied47_c_passaporte,true,'text',$db_opcao," $desabpassaporte ")?>
      </td>
      </td>
     </tr>
    </table>
   </fieldset>
  </td>
 </tr>
 <tr>
  <td align="center">
   <table align="center">
    <tr>
     <td nowrap title="<?=@$Ted47_t_obs?>">
      <?=@$Led47_t_obs?><br>
      <?php db_textarea('ed47_t_obs',4,60,$Ied47_t_obs,true,'text',$db_opcao,"")?>
     </td>
     <td width="10%"></td>
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
<input name="alterar" type="submit" value="Alterar" <?=($db_botao==false?"disabled":"")?> onclick="return js_valida()">
<input type="button" value="Fechar" onclick="js_fechar();">
</form>
<script>
function js_valida(){
 nacion = <?=$ed47_i_nacion?>;
 datanasc = "<?=$ed47_d_nasc?>";
 if(nacion!=3){
  identnum = document.form1.ed47_v_ident.value;
  identcomp = document.form1.ed47_v_identcompl.value;
  identorg = document.form1.ed47_i_censoorgemissrg.value;
  identuf = document.form1.ed47_i_censoufident.value;
  identdata = document.form1.ed47_d_identdtexp.value;
  if(nacion==3 && (identnum!="" || identcomp!="" || identorg!=" " || identuf!=" " || identdata!="")){
   alert("Aluno com nacionalidade Estrangeira (Aba Dados Pessoais).\nCampos referente a Identidade N�O devem ser informados!");
   return false;
  }
  if(identnum=="" && (identcomp!="" || identorg!=" " || identuf!=" " || identdata!="") ){
   alert("Campo N� Identidade deve ser informado quando\num dos campos abaixo estiverem informados:\n\nComplemento\nUF Identidade\n�rgao Emissor\nData Expedi��o Identidade");
   return false;
  }
  if(identorg==" " && (identnum!="" || identuf!=" ") ){
   alert("Campo �rg�o Emissor deve ser informado quando\num dos campos abaixo estiverem informados:\n\nN� Identidade\nUF Identidade");
   return false;
  }
  if(identuf==" " && (identnum!="" || identorg!=" ") ){
   alert("Campo UF Identidade deve ser informado quando\num dos campos abaixo estiverem informados:\n\nN� Identidade\n�rg�o Emissor");
   return false;
  }
  if(identcomp!="" && identnum=="" && identorg==" " && identuf==" "){
   alert("Campo Complemento s� pode ser informado quando\num dos campos abaixo estiverem informados:\n\nN� Identidade\n�rg�o Emissor\nUF Identidade");
   return false;
  }
  if(identdata!="" && identnum=="" && identorg==" " && identuf==" "){
   alert("Campo Data Expedi��o Identidade s� pode ser informado quando\num dos campos abaixo estiverem informados:\n\nN� Identidade\n�rg�o Emissor\nUF Identidade");
   return false;
  }
  if(identdata!=""){
   diaident = identdata.substr(0,2);
   mesident = identdata.substr(3,2);
   anoident = identdata.substr(6,4);
   dianasc = datanasc.substr(8,2);
   mesnasc = datanasc.substr(5,2);
   anonasc = datanasc.substr(0,4);
   data_hj = <?=date("Y").date("m").date("d")?>;
   if(anoident<1900){
    alert("Ano da Data de Expedi��o deve ser maior que 1899!");
    return false;
   }
   data_ident = anoident+""+mesident+""+diaident;
   data_nasc = anonasc+""+mesnasc+""+dianasc;
   if(parseInt(data_ident)>=parseInt(data_hj)){
    alert("Campo Data de Expedi��o deve ser menor que a data corrente!");
    return false;
   }
   if(parseInt(data_ident)<=parseInt(data_nasc)){
    alert("Campo Data de Expedi��o deve ser maior que a data de nascimento do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!");
    return false;
   }
  }
  certtip = document.form1.ed47_c_certidaotipo.value;
  certnum = document.form1.ed47_c_certidaonum.value;
  certfol = document.form1.ed47_c_certidaofolha.value;
  certliv = document.form1.ed47_c_certidaolivro.value;
  certcar = document.form1.ed47_c_certidaocart.value;
  certdat = document.form1.ed47_c_certidaodata.value;
  certuf = document.form1.ed47_i_censoufcert.value;
  certmun = document.form1.ed47_i_censomuniccert.value;
  if(nacion==3 && (certtip!="" || certnum!="" || certfol!="" || certliv!="" || certcar!="" || certdat!="" || certuf!=" " || certmun!=" ")){
   alert("Aluno com nacionalidade Estrangeira (Aba Dados Pessoais).\nCampos referente a Certid�o N�O devem ser informados!");
   return false;
  }
  if(certtip=="" && (certnum!="" || certfol!="" || certliv!="" || certdat!="" || certuf!=" " || certcar!="" || certmun!=" " ) ){
   alert("Campo Tipo de Certid�o deve ser informado quando\num dos campos abaixo estiverem informados:\n\nN�mero do Termo\nFolha\nLivro\nData da Emiss�o\nUF Cart�rio\nCart�rio\nMunic�pio");
   return false;
  }
  if(certnum=="" && (certtip!="" || certuf!=" " || certcar!="" || certmun!=" " ) ){
   alert("Campo N�mero do Termo deve ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nUF Cart�rio\nCart�rio\nMunic�pio");
   return false;
  }
  if(certcar=="" && (certtip!="" || certuf!=" " || certnum!="" || certmun!=" " ) ){
   alert("Campo Cart�rio deve ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nUF Cart�rio\nN�mero do Termo\nMunic�pio");
   return false;
  }
  if(certuf==" " && (certtip!="" || certcar!="" || certnum!="" || certmun!=" " ) ){
   alert("Campo UF Cart�rio deve ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nCart�rio\nN�mero do Termo\nMunic�pio");
   return false;
  }
  if(certmun==" " && (certtip!="" || certcar!="" || certnum!="" || certuf!=" " ) ){
   alert("Campo Munic�pio deve ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nCart�rio\nN�mero do Termo\nUF cart�rio");
   return false;
  }
  if(certfol!="" && certtip=="" && certnum=="" && certuf==" " && certcar==""){
   alert("Campo Folha s� pode ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nN�mero do Termo\nUF Cart�rio\nCart�rio");
   return false;
  }
  if(certliv!="" && certtip=="" && certnum=="" && certuf==" " && certcar==""){
   alert("Campo Livro s� pode ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nN�mero do Termo\nUF Cart�rio\nCart�rio");
   return false;
  }
  if(certdat!="" && certtip=="" && certnum=="" && certuf==" " && certcar==""){
   alert("Campo Data de Emiss�o s� pode ser informado quando\num dos campos abaixo estiverem informados:\n\nTipo de Certid�o\nN�mero do Termo\nUF Cart�rio\nCart�rio");
   return false;
  }
  if(certdat!=""){
   diacert = certdat.substr(0,2);
   mescert = certdat.substr(3,2);
   anocert = certdat.substr(6,4);
   dianasc = datanasc.substr(8,2);
   mesnasc = datanasc.substr(5,2);
   anonasc = datanasc.substr(0,4);
   data_hj = <?=date("Y").date("m").date("d")?>;
   data_cert = anocert+""+mescert+""+diacert;
   data_nasc = anonasc+""+mesnasc+""+dianasc;
   if(parseInt(data_cert)>=parseInt(data_hj)){
    alert("Campo Data de Emiss�o deve ser menor que a data corrente!");
    return false;
   }
   if(certtip=="N"){
    if(parseInt(data_cert)<parseInt(data_nasc)){
     alert("Campo Data de Emiss�o deve ser maior ou igual a data de nascimento do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!");
     return false;
    }
   }else if(certtip=="C"){
    if(parseInt(data_cert)<=parseInt(data_nasc)){
     alert("Campo Data de Emiss�o deve ser maior que a data de nascimento do aluno ("+dianasc+"/"+mesnasc+"/"+anonasc+")!");
     return false;
    }
   }
  }
 }
 if(nacion!=3 && document.form1.ed47_c_passaporte.value!=""){
  alert("Campo N� Passaporte s� pode ser informado quando nacionalidade do aluno for Estrangeira (Aba Dados Pessoais).");
  return false;
 }
 return true;
}
function js_TestaNi(cNI){
 var NI;
 NI = js_LimpaCampo(cNI.value,10);
 if (NI.length != 11){
  alert('O n�mero do CPF informado est� incorreto');
  cNI.value = "";
  cNI.select();
  cNI.focus();
  return(false);
 }
 if (NI.substr(9,2) != js_CalculaDV(NI.substr(0,9), 11)){
  alert('O n�mero do CPF informado est� incorreto');
  cNI.value = "";
  cNI.select();
  cNI.focus();
  return(false);
 }
 return (true);
}
function js_verificacpf(obcgc){
 if(obcgc.value==00000000000 || obcgc.value==00000000191){
  alert('Valor Informado n�o � V�lido para CPF.');
  obcgc.value = "";
  obcgc.select();
  obcgc.focus();
 }
 if(obcgc.value.length == 11){
  return js_TestaNi(obcgc);
 }
 if(obcgc.value!=""){
  alert('Valor Informado n�o � V�lido para CPF.');
  obcgc.value = "";
  obcgc.select();
  obcgc.focus();
 }
 return false;
}
function js_fechar(){
 if(parent.parent.document.form1.codigo.value!=""){
  parent.parent.db_iframe_alteradados.hide();
  parent.parent.dadosleitor.location.href = "bib1_leitor004.php?chavepesquisa=<?=$ed47_i_codigo?>&tipo=ALUNO";
 }else{
  parent.parent.db_iframe_alteradados.hide();
  parent.parent.document.form1.codigo.value = <?=$ed47_i_codigo?>;
  parent.parent.document.form1.nome.value = "<?=$ed47_v_nome?>";
  parent.parent.document.form1.tipo.value = "ALUNO";
 }
}
</script>
