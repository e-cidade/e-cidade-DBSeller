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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_alunopassagem_classe.php"));
include(modification("classes/db_alunopassagemescola_classe.php"));
include(modification("classes/db_alunobairro_classe.php"));
include(modification("classes/db_escola_classe.php"));
include(modification("classes/db_transporteparam_classe.php"));
include(modification("classes/db_alunopassagemescolaproc_classe.php"));
include(modification("classes/db_distancia_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clalunopassagem = new cl_alunopassagem;
$cldistancia = new cl_distancia;
$cltransporteparam = new cl_transporteparam;
$clalunopassagemescola = new cl_alunopassagemescola;
$clalunobairro = new cl_alunobairro;
$clescola = new cl_escola;
$clalunopassagemescolaproc = new cl_alunopassagemescolaproc;
$db_opcao = 22;
$db_botao = false;
if(isset($alterar)){
 $db_opcao = 2;
 $erro=false;
 $result= $cltransporteparam->sql_record($cltransporteparam->sql_query("","ed224_f_limitekm","",""));
 if($cltransporteparam->numrows==0){
  $clalunopassagem->erro_msg= "Parâmetros não configurados! (Procedimentos / Parâmetros)";
  $clalunopassagem->erro_status="0";
  $erro=true;
 }else{
  if($tipoescola=="M"){
   db_fieldsmemory($result,0);
   $result1= $clalunobairro->sql_record($clalunobairro->sql_query("","ed225_i_bairro","","ed225_i_aluno=$ed215_i_aluno"));
   if($clalunobairro->numrows==0){
    $ed225_i_bairro = 0;
   }else{
    db_fieldsmemory($result1,0);
   }
   $result2= $clescola->sql_record($clescola->sql_query("","ed18_i_bairro","","ed18_i_codigo=$ed18_i_codigo"));
   db_fieldsmemory($result2,0);
   $result3= $cldistancia->sql_record( $cldistancia->sql_query("","ed223_f_km","","ed223_i_bairroorigem=$ed225_i_bairro and ed223_i_bairrodestino= $ed18_i_bairro"));
   if($cldistancia->numrows>0){
    db_fieldsmemory($result3,0);
    if($ed223_f_km<$ed224_f_limitekm){
     $clalunopassagem->erro_msg="Limite de distância entre aluno e escola deve ser maior que $ed224_f_limitekm metros!\\n Aluno selecionado mora atualmente à $ed223_f_km metros da escola.";
     $clalunopassagem->erro_status="0";
     $erro=true;
    }
   }
  }
 }
 if($erro==false){
  db_inicio_transacao();
  $clalunopassagem->ed215_i_usuario= db_getsession('DB_id_usuario');
  $clalunopassagem->alterar($ed215_i_codigo);
  db_fim_transacao();
 }
}else if(isset($chavepesquisa)){
 $db_opcao = 2;
 $campos= "alunopassagem.*,
           ed47_v_nome,
           ed217_c_origem,
           bairro.j13_descr as bairroaluno,
           case when ed56_i_codigo is null then ed82_c_nome else ed18_c_nome end as ed18_c_nome,
           case when ed56_i_codigo is null then ed82_i_codigo else ed18_i_codigo end as ed18_i_codigo,
           case when ed56_i_codigo is null then ed82_c_bairro else bairro.j13_descr end as bairroescola
          ";
   //echo $clalunopassagem->sql_query("",$campos,"ed215_i_ano","ed215_i_codigo= $chavepesquisa");
 $result = $clalunopassagem->sql_record($clalunopassagem->sql_query("",$campos,"ed215_i_ano","ed215_i_codigo= $chavepesquisa"));
// die(pg_errormessage());
 db_fieldsmemory($result,0);
 $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td width="360" height="18">&nbsp;</td>
  <td width="263">&nbsp;</td>
  <td width="25">&nbsp;</td>
  <td width="140">&nbsp;</td>
 </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
   <br>
   <center>
   <fieldset style="width:95%"><legend><b>Alteração de Alunos que utilizam passagens</b></legend>
    <?include(modification("forms/db_frmalunopassagem.php"));?>
   </fieldset>
   </center>
  </td>
 </tr>
</table>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<?php
if(isset($alterar)){
 if($clalunopassagem->erro_status=="0"){
  $clalunopassagem->erro(true,false);
  $db_botao=true;
  echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  if($clalunopassagem->erro_campo!=""){
   echo "<script> document.form1.".$clalunopassagem->erro_campo.".style.backgroundColor='#99A9AE';</script>";
   echo "<script> document.form1.".$clalunopassagem->erro_campo.".focus();</script>";
  }
 }else{
  if($tipoescola=="F"){
   if($origemescola=="F"){
    db_inicio_transacao();
    $clalunopassagemescolaproc->ed227_i_alunopassagem=$ed215_i_codigo;
    $clalunopassagemescolaproc->ed227_i_escolaproc= $ed18_i_codigo;
    $clalunopassagemescolaproc->ed227_i_codigo= $ed226_i_codigo;
    $clalunopassagemescolaproc->alterar($ed226_i_codigo);
    db_fim_transacao();
   }else{
    db_inicio_transacao();
    $clalunopassagemescola->excluir("","ed226_i_codigo=$ed226_i_codigo");
    $clalunopassagemescolaproc->ed227_i_alunopassagem=$ed215_i_codigo;
    $clalunopassagemescolaproc->ed227_i_escolaproc= $ed18_i_codigo;
    $clalunopassagemescolaproc->incluir(null);
    db_fim_transacao();
   }
  }else{
   if($origemescola=="M"){
    db_inicio_transacao();
    $clalunopassagemescola->ed226_i_alunopassagem= $ed215_i_codigo;
    $clalunopassagemescola->ed226_i_escola=$ed18_i_codigo;
    $clalunopassagemescola->alterar($ed226_i_codigo);
    db_fim_transacao();
   }else{
    db_inicio_transacao();
    $clalunopassagemescolaproc->excluir("","ed227_i_codigo=$ed226_i_codigo");
    $clalunopassagemescola->ed226_i_alunopassagem=$ed215_i_codigo;
    $clalunopassagemescola->ed226_i_escola= $ed18_i_codigo;
    $clalunopassagemescola->incluir(null);
   }
  }
  $clalunopassagem->erro(true,true);
 }
}
if($db_opcao==22){
 echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","ed215_i_ano",true,1,"ed215_i_ano",true);
</script>
