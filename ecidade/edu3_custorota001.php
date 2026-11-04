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
include(modification("classes/db_serie_classe.php"));
include(modification("classes/db_escolaproc_classe.php"));
include(modification("classes/db_alunocurso_classe.php"));
include(modification("classes/db_veicretirada_classe.php"));
include(modification("classes/db_alunopossib_classe.php"));
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_cursoescola_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$claluno = new cl_aluno;
$clserie = new cl_serie;
$cllinha = new cl_linha;
$clescolaproc = new cl_escolaproc;
$clalunocurso = new cl_alunocurso;
$clalunopossib = new cl_alunopossib;
$clcursoescola = new cl_cursoescola;
$clveicretirada = new cl_veicretirada;
$clrotamov = new cl_rotamov;
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$clrotulo->label("ed60_i_codigo");
$clrotulo->label("ed217_c_origem");
$clrotulo->label("ed57_i_serie");
$clrotulo->label("ed31_i_curso");
$clrotulo->label("ed56_i_escola");
$clrotulo->label("ed18_i_codigo");
$clrotulo->label("ve60_i_codigo");
$clrotulo->label("ed220_i_codigo");
$clrotulo->label("ve60_datasaida");
$db_opcao = 1;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<script>
function js_redireciona(chave){
 js_OpenJanelaIframe('','db_iframe_custolinha','edu3_custolinha002.php?chavepesquisa='+chave,'Consulta de Rotas',true);
}
</script>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form3" action="">
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
<fieldset style="width:95%"><legend><b>Consulta Custo de Rotas</b></legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
 <tr>
  <td valign="top">
   <table width="100%" border="0" cellspacing="0">
    <tr>
     <td nowrap title="<?=$Ted217_i_codigo?>">
      <?=$Led217_i_codigo?>
     </td>
     <td nowrap>
      <?php db_input("ed217_i_codigo",10,@$ed217_i_codigo,true,"text",1);?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted217_c_origem?>">
      <?=$Led217_c_origem?>
     </td>
     <td nowrap>
      <?php db_input("ed217_c_origem",50,@$ed217_c_origem,true,"text",1);?>
     </td>
    </tr>
  </table>
  </td>
  <td valign="top">
   <table border="0" cellspacing="0">
    <tr>
     <td nowrap title="<?=$Ted56_i_escola?>">
      <?=$Led56_i_escola?>
     </td>
     <td>
      <?php
     $sql = "SELECT ed18_i_codigo,ed18_c_nome,'M' as tipoescola1
          FROM escola
          UNION
          SELECT ed82_i_codigo,ed82_c_nome,'F' as tipoescola1
          FROM escolaproc
         ";
      $result_escola = db_query($sql);
      $linhas= pg_numrows($result_escola);
      if($linhas==0){
      $x = array(''=>'');
      db_select('ed56_i_escola',$x,true,1,"style='width:300px;'");
      }
      else{
       ?>
       <select name="ed56_i_escola" id="ed56_i_escola" onchange="js_escola(this.value);" style="width:300px;">
        <option value=""></option>
        <?php
        for($x=0;$x<$linhas;$x++){
         db_fieldsmemory($result_escola,$x);
         $socodigo= $ed18_i_codigo;
         $ed18_i_codigo= $ed18_i_codigo."|".$tipoescola1;
         ?>
         <option value="<?=$ed18_i_codigo?>" <?=@$codescola==$socodigo?"selected":""?>><?=$ed18_c_nome?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted31_i_curso?>">
      <?=$Led31_i_curso?>
     </td>
     <td>
      <?php
      $codescola = isset($codescola)?$codescola:0;
      $disabled = $codescola!=0?"":"disabled";
      if(@$tipoescola=="M"){
          $sql = "SELECT distinct ed29_i_codigo,ed29_c_descr
          FROM alunocurso
          inner join base on  base.ed31_i_codigo = alunocurso.ed56_i_base
          inner join cursoedu on cursoedu.ed29_i_codigo = base.ed31_i_curso";

      }else{
      $sql= "
         SELECT  distinct ed29_i_codigo,ed29_c_descr
          FROM alunofora
          inner join cursoedu on cursoedu.ed29_i_codigo = alunofora.ed216_i_cursoedu
         ";
      }
      $result_curso = db_query($sql);
      $linhas= pg_numrows($result_curso);
      if($linhas==0){
       $x = array(''=>'');
       db_select('ed31_i_curso',$x,true,1," $disabled style='width:300px;'");
      }else{
       ?>
       <select name="ed31_i_curso" id="ed31_i_curso" onchange="js_curso(this.value,document.form3.ed56_i_escola.value);" style="width:300px;" <?=$disabled?>>
        <option value=""></option>
        <?php
        for($x=0;$x<$linhas;$x++){
         db_fieldsmemory($result_curso,$x);
         ?>
         <option value="<?=$ed29_i_codigo?>" <?=@$codcurso==$ed29_i_codigo?"selected":""?>><?=$ed29_c_descr?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td nowrap>
      <?=$Led57_i_serie?>
     </td>
     <td>
      <?php
      $codcurso = isset($codcurso)?$codcurso:0;
      $disabled1 = $codcurso!=0?"":"disabled";
      $result_serie = $clalunopossib->sql_record($clalunopossib->sql_query("","DISTINCT ed11_i_codigo,ed11_c_descr,ed11_i_sequencia",""," ed31_i_curso = $codcurso AND ed56_i_escola = $codescola"));
      if($clalunopossib->numrows==0){
       $x = array(''=>'');
       db_select('ed57_i_serie',$x,true,1," $disabled1 style='width:300px;'");
      }else{
       ?>
       <select name="ed57_i_serie" id="ed57_i_serie" <?=$disabled1?> style='width:300px;'>
        <option value=""></option>
        <?php
        for($x=0;$x<$clalunopossib->numrows;$x++){
         db_fieldsmemory($result_serie,$x);
         ?>
         <option value="<?=$ed11_i_codigo?>" <?=@$ed57_i_serie==$ed11_i_codigo?"selected":""?>><?=$ed11_c_descr?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td nowrap>
   </table>
  </td>
 </tr>
 <tr>
  <td colspan="2" align="center">
   <br>
   <input name="pesquisar" id="pesquisar" type="button" value="Pesquisar" onclick="js_pesquisar();">
   <input name="limpar" type="button" value="Limpar" onclick="location.href='edu3_custolinha001.php'">
  </td>
 </tr>
</table>
</fieldset>
</form>
<table width="100%">
 <tr>
  <td valign="top" align="center" >
   <?php
   if(isset($pesquisar)){
    ?><fieldset style="width:95%"><legend><b>Registros</b></legend><?php
    if($tipoescola=="M"){
   $where= "left join itinerarioescola on itinerarioescola.ed221_i_itinerario= itinerario.ed218_i_codigo
              left join escola on escola.ed18_i_codigo= itinerarioescola.ed221_i_escola
              left join alunocurso on alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
              left join base on  base.ed31_i_codigo = alunocurso.ed56_i_base
              left join cursoedu on cursoedu.ed29_i_codigo = base.ed31_i_curso
              left join alunopossib on alunopossib.ed79_i_alunocurso=alunocurso.ed56_i_codigo
            ";
    }elseif($tipoescola=="F"){
    $where= "left join itinerarioescolaproc on itinerarioescolaproc.ed222_i_itinerario= itinerario.ed218_i_codigo
                  left join escolaproc on escolaproc.ed82_i_codigo= itinerarioescolaproc.ed222_i_escolaproc
                  left join alunofora on alunofora.ed216_i_aluno=aluno.ed47_i_codigo
                  left join cursoedu on cursoedu.ed29_i_codigo = alunofora.ed216_i_cursoedu";
    }else{
     $where="";
    }
   $sql = "SELECT distinct ed217_i_codigo,ed217_c_origem from linha
            left join itinerario on itinerario.ed218_i_linha = linha.ed217_i_codigo
            left join rotaaluno on rotaaluno.ed219_i_rota= rota.ed217_i_codigo
            left join aluno on aluno.ed47_i_codigo= rotaaluno.ed219_i_aluno
            $where";
    if(isset($ed217_i_codigo)){
     $repassa = array("ed217_i_codigo"=>$ed217_i_codigo);
    }
    $sql .= " WHERE ed217_i_codigo > 0 ";
    if(isset($ed217_c_origem) && (trim($ed217_c_origem)!="") ){
     $sql .= " AND ed217_c_origem like'$ed217_c_origem%' ";
    }
    if(isset($ed217_i_codigo) && (trim($ed217_i_codigo)!="") ){
     $sql .= " AND ed217_i_codigo = $ed217_i_codigo ";
    }
    if(isset($ed56_i_escola) && (trim($ed56_i_escola)!="") ){
     if($tipoescola=="M"){
     $sql .= " AND ed221_i_escola = $ed56_i_escola ";
     }else{
      $sql .= " AND ed222_i_escolaproc = $ed56_i_escola ";
     }

    }
    if(isset($ed31_i_curso) && (trim($ed31_i_curso)!="")){
      if($tipoescola=="M"){
     $sql .= " AND ed31_i_curso = $ed31_i_curso ";
     }else{
           $sql .= " AND ed216_i_cursoedu = $ed31_i_curso ";
     }

    }
    if(isset($ed57_i_serie) && (trim($ed57_i_serie)!="") ){
     if($tipoescola=="M"){
      $sql .= " AND ed79_i_serie = $ed57_i_serie ";
     }else{
            $sql .= " AND ed216_i_serie = $ed57_i_serie ";
     }

    }
    $sql .= "ORDER BY ed217_c_origem";
    db_lovrot(@$sql,12,"()","","js_redireciona|ed217_i_codigo","","NoMe",$repassa);
    ?></fieldset><?php
   }
   ?>
  </td>
 </tr>
</table>
</center>
</body>
</html>
<script>
document.getElementById("ed217_c_origem").focus();
function js_escola(valor){
 tipo= valor.split("|");
 codigo = document.getElementById("ed217_i_codigo").value;
 nome = document.getElementById("ed217_c_origem").value;
 if(valor==""){
  location.href = "edu3_custolinha001.php?loc&ed217_i_codigo="+codigo+"&ed217_c_origem="+nome;
 }else{
  location.href = "edu3_custolinha001.php?loc&codescola="+tipo[0]+"&tipoescola="+tipo[1]+"&ed217_i_codigo="+codigo+"&ed217_c_origem="+origem;
 }
}




function js_curso(valor,escola){
tipo= escola.split("|");
 codigo = document.getElementById("ed217_i_codigo").value;
 nome = document.getElementById("ed217_c_origem").value;
 if(valor==""){
  location.href = "edu3_custolinha001.php?loc&codescola="+escola+"&ed217_i_codigo="+codigo+"&ed217_c_origem="+nome;
 }else{
  location.href = "edu3_custolinha001.php?loc&codcurso="+valor+"&codescola="+tipo[0]+"&tipoescola="+tipo[1]+"&ed217_i_codigo="+codigo+"&ed217_c_origem="+origem;
 }
}
function js_pesquisar(){
 codigo = document.getElementById("ed217_i_codigo").value;
 nome = document.getElementById("ed217_c_origem").value;
 escola = document.getElementById("ed56_i_escola").value;
 tipo= escola.split("|");
 curso = document.getElementById("ed31_i_curso").value;
 serie = document.getElementById("ed57_i_serie").value;
 location.href = "edu3_custolinha001.php?pesquisar&codcurso="+curso+"&codescola="+tipo[0]+"&tipoescola="+tipo[1]+"&ed217_i_codigo="+codigo+"&ed217_c_origem="+origem+"&ed56_i_escola="+tipo[0]+"&ed57_i_serie="+serie+"&ed31_i_curso="+curso;
}
<?php
if(isset($loc)){
 ?>document.getElementById("pesquisar").click();<?php
}
?>
</script>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
