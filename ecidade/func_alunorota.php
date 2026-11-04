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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_alunopassagemescola_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clalunopassagemescola = new cl_alunopassagemescola;
$clalunopassagemescola->rotulo->label("ed18_i_codigo");
$clalunopassagemescola->rotulo->label("ed18_c_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed47_v_nome");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
 <tr>
  <td height="63" align="center" valign="top">
   <table width="35%" border="0" align="center" cellspacing="0">
    <form name="form2" method="post" action="" >
    <tr>
     <td width="4%" align="right" nowrap title="<?=$Ted47_i_codigo?>">
      <?=$Led47_i_codigo?>
     </td>
     <td width="96%" align="left" nowrap>
      <?php db_input("ed47_i_codigo",10,@$Ied47_i_codigo,true,"text",4,"","chave_ed47_i_codigo");?>
     </td>
    </tr>
    <tr>
     <td width="4%" align="right" nowrap title="<?=$Ted47_v_nome?>">
      <?=$Led47_v_nome?>
     </td>
     <td width="96%" align="left" nowrap>
      <?php db_input("ed47_v_nome",40,@$Ied47_v_nome,true,"text",4,"","chave_ed47_v_nome");?>
      </td>
    </tr>
    <tr>
     <td colspan="2" align="center">
      <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
      <input name="limpar" type="reset" id="limpar" value="Limpar" >
      <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_aluno.hide();">
     </td>
    </tr>
    </form>
   </table>
  </td>
 </tr>
 <tr>
  <td align="center" valign="top">
   <?php
   if(!isset($pesquisa_chave)){
    $hoje = date("Y-m-d",db_getsession("DB_datausu"));
    $where = "WHERE ed47_i_codigo > 0 ";
    if(isset($chave_ed47_i_codigo) && (trim($chave_ed47_i_codigo)!="") ){
     $where .= " and ed47_i_codigo = $chave_ed47_i_codigo";
    }elseif(isset($chave_ed47_v_nome) && (trim($chave_ed47_v_nome)!="") ){
     $where .= " and ed47_v_nome like '$chave_ed47_v_nome%'";
    }
    $sql= "SELECT ed47_i_codigo,ed47_v_nome,bairroaluno.j13_descr as bairroaluno,ed18_i_codigo,ed18_c_nome,bairroescola.j13_descr as bairroescola,'M' as tipoescola
           FROM aluno
            inner join alunocurso on alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
            left join alunobairro on alunobairro.ed225_i_aluno = aluno.ed47_i_codigo
            left join bairro as bairroaluno on bairroaluno.j13_codi = alunobairro.ed225_i_bairro
            inner join escola on escola.ed18_i_codigo= alunocurso.ed56_i_escola
            inner join bairro as bairroescola on bairroescola.j13_codi = escola.ed18_i_bairro
           $where
           AND not exists(select * from alunopassagem
                          where ed215_i_aluno = ed47_i_codigo
                          and '$hoje' between ed215_d_datainicio and ed215_d_datafim)
           AND not exists(select * from rotaaluno
                          where ed219_i_aluno = ed47_i_codigo
                          and ed219_d_datafim is null)
           UNION
           SELECT ed47_i_codigo,ed47_v_nome,bairro.j13_descr as bairroaluno,ed82_i_codigo,ed82_c_nome,ed82_c_bairro as bairroescola,'F' as tipoescola
           FROM aluno
            inner join alunofora on alunofora.ed216_i_aluno= aluno.ed47_i_codigo
            left join alunobairro on alunobairro.ed225_i_aluno = aluno.ed47_i_codigo
            left join bairro on bairro.j13_codi = alunobairro.ed225_i_bairro
            inner join escolaproc on escolaproc.ed82_i_codigo =alunofora.ed216_i_escolaproc
           $where
           AND not exists(select * from alunopassagem
                          where ed215_i_aluno = ed47_i_codigo
                          and '$hoje' between ed215_d_datainicio and ed215_d_datafim)
           AND not exists(select * from rotaaluno
                          where ed219_i_aluno = ed47_i_codigo
                          and ed219_d_datafim is null)

           ";
    if(isset($chave_ed47_i_codigo)){
     $repassa = array("chave_ed47_i_codigo"=>$chave_ed47_i_codigo,"chave_ed47_v_nome"=>$chave_ed47_v_nome);
     db_lovrot(@$sql,12,"()","",$funcao_js,"","NoMe",$repassa);
    }
   }else{
    if($pesquisa_chave!=null && $pesquisa_chave!=""){
     $result = $clalunopassagemescola->sql_record($clalunopassagemescola->sql_query($pesquisa_chave));
     if($clalunopassagemescola->numrows!=0){
      db_fieldsmemory($result,0);
      echo "<script>".$funcao_js."('$ed226_i_codigo',false);</script>";
     }else{
      echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
     }
    }else{
     echo "<script>".$funcao_js."('',false);</script>";
    }
   }
   ?>
  </td>
 </tr>
</table>
</body>
</html>
<script>
js_tabulacaoforms("form2","chave_ed47_v_nome",true,1,"chave_ed47_v_nome",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
