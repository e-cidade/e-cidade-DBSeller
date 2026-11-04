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
include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_aluno_classe.php"));
include(modification("classes/db_alunopassagem_classe.php"));
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_matricula_classe.php"));
include(modification("classes/db_rechumanoescola_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$claluno = new cl_aluno;
$cllinha = new cl_linha;
$clmatricula = new cl_matricula;
$clalunopassagem = new cl_alunopassagem;
$clrotulo = new rotulocampo;
$claluno->rotulo->label("ed47_i_codigo");
$clrotulo->label("ed215_i_codigo");
$clrotulo->label("ed226_i_codigo");
$clrotulo->label("ed18_i_escola");
$clrotulo->label("ed47_v_nome");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<script>
function js_redireciona(chave){
 js_OpenJanelaIframe('','db_iframe_alunopassagem','edu3_alunopassagem002.php?chavepesquisa='+chave,'Consulta Alunos por Passagens',true);
}
</script>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td width="360" height="18">&nbsp;</td>
  <td width="263">&nbsp;</td>
  <td width="25">&nbsp;</td>
  <td width="140">&nbsp;</td>
 </tr>
</table>
<br>
<center>
<fieldset style="width:95%"><legend><b>Consulta de Alunos com Passagens</b></legend>
<table width="100%" border="0" cellspacing="0" bgcolor="#CCCCCC">
 <tr>
  <td align="center" valign="top">
   <table width="100%" border="0" align="center" cellspacing="0">
    <form name="form1" method="post" action="" >
    <tr>
     <td width="20%" nowrap title="<?=$Ted60_i_codigo?>">
      <?=$Led47_i_codigo?>
     </td>
     <td>
      <?php db_input("ed60_i_codigo",10,@$Ied47_i_codigo,true,"text",4,"","chave_ed60_i_codigo");?>
     </td>
    </tr>
    <tr>
     <td nowrap>
      <b>Nome:</b>
     </td>
     <td>
      <?php db_input("ed47_v_nome",50,@$Ied47_v_nome,true,"text",4,"","chave_ed47_v_nome");?>
     </td>
    </tr>
    <tr>
     <td nowrap>
      <b>Escola:</b>
     </td>
     <td>
      <?php
      $sql= "select ed18_i_codigo,ed18_c_nome,ed47_i_codigo,ed47_v_nome,'M' as tipoescola
       From aluno
       inner join alunopassagem on alunopassagem.ed215_i_aluno = aluno.ed47_i_codigo
       left join alunocurso  on  alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
       left join escola  on  escola.ed18_i_codigo = alunocurso.ed56_i_escola
       left join alunofora  on  alunofora.ed216_i_aluno = aluno.ed47_i_codigo
       left join escolaproc  on escolaproc.ed82_i_codigo = alunofora.ed216_i_escolaproc";
      /* UNION
       select  ed82_i_codigo,ed82_c_nome,ed47_i_codigo,ed47_v_nome,'F' as tipoescola
       FROM aluno
       inner join alunopassagem on alunopassagem.ed215_i_aluno= aluno.ed47_i_codigo */
      $result1 = db_query($sql);
      $linhas1 = pg_num_rows($result1);
      ?>
      <select name="chave_ed226_i_escola" style="font-size:10px;width:300px">
       <option value=''></option>
       <?php
        for($x=0;$x<$linhas1;$x++){
         db_fieldsmemory($result1,$x);
         echo "<option value='$ed18_i_codigo' ".(@$ed226_i_escola==$ed18_i_codigo?"selected":"").">$ed18_c_nome</option>";
        }
       ?>
      </select>
     </td>
    </tr>
    <tr>
     <td align="center" colspan="3">
      <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
      <input name="limpar" type="button" value="Limpar" onClick="location.href='edu3_alunopassagem001.php'">
     </td>
    </tr>
   </table>
   </form>
  </td>
 </tr>
</table>
</fieldset>
<table width="100%">
 <tr>
  <td align="center" valign="top">
   <?php
   $escola = db_getsession("DB_coddepto");
   if(isset($pesquisar)){
    ?><fieldset style="width:95%"><legend><b>Registros</b></legend><?php
    $campos = "ed47_i_codigo,
               ed47_v_nome,
               ed215_i_codigo,
               ed215_i_ano,
               ed217_c_origem
              ";
    $where = "where ed47_i_codigo>0";
    if(isset($chave_ed47_i_codigo) && (trim($chave_ed47_i_codigo)!="") ){
     $where .= " AND ed47_i_codigo = $chave_ed47_i_codigo";
    }
    if(isset($chave_ed47_v_nome) && (trim($chave_ed47_v_nome)!="") ){
     $where .= " AND ed47_v_nome like '$chave_ed47_v_nome%'";
    }
    if(isset($chave_ed226_i_escola) && (trim($chave_ed226_i_escola)!="") ){
     $where .= " AND ed18_i_codigo = $chave_ed226_i_escola or ed82_i_codigo = $chave_ed226_i_escola ";
    }
    $sql = "select $campos
       From aluno
       inner join alunopassagem on alunopassagem.ed215_i_aluno = aluno.ed47_i_codigo
       left join alunocurso  on  alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
       left join escola  on  escola.ed18_i_codigo = alunocurso.ed56_i_escola
       left join alunofora  on  alunofora.ed216_i_aluno = aluno.ed47_i_codigo
       left join escolaproc  on escolaproc.ed82_i_codigo = alunofora.ed216_i_escolaproc
       inner join linha on linha.ed217_i_codigo = alunopassagem.ed215_i_linha
       $where";
    $repassa = array();
    if(isset($chave_ed18_i_codigo)){
     $repassa = array("chave_ed47_i_codigo"=>$chave_ed60_i_codigo,"chave_ed47_v_nome"=>$chave_ed47_v_nome);
    }
    if(isset($pesquisar)){
     db_lovrot(@$sql,15,"()","","js_redireciona|ed215_i_codigo","","NoMe",$repassa);
    }
    ?></fieldset><?php
   }
   ?>
   </td>
  </tr>
</table>
</center>
</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
