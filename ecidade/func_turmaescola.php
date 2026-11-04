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

include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_matricula_classe.php"));
include(modification("classes/db_calendario_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$clmatricula  = new cl_matricula;
$clcalendario = new cl_calendario;
$db_opcao     = 1;
$db_botao     = true;

$campos       = "   ed57_i_codigo,ed57_c_descr,ed15_c_nome,ed11_i_codigo";
$sql          = "  SELECT  $campos   ";
$sql         .= "        FROM matricula ";
$sql         .= "        inner join turma on ed57_i_codigo = ed60_i_turma ";
$sql         .= "        inner join matriculaserie on ed221_i_matricula = ed60_i_codigo ";
$sql         .= "        inner join serie on ed11_i_codigo = ed221_i_serie ";
$sql         .= "        inner join turno on ed15_i_codigo = ed57_i_turno ";
$sql         .= "        inner join ensino on ed10_i_codigo = ed11_i_ensino ";
$sql         .= "        inner join mer_cardapioescola on me32_i_escola = ed57_i_escola";
$sql         .= "   WHERE ed57_i_escola = $codigo ";
$sql         .= "         AND ed221_c_origem = 'S' ";
$sql         .= "         AND ed60_c_ativa = 'S'      ";
$sql         .= "         AND  me32_i_escola = $codigo ";
$sql         .= "   GROUP BY ed57_c_descr,ed57_i_codigo,ed15_c_nome,ed11_i_codigo ";
$sql         .= "   ORDER BY ed57_c_descr ";
$result       = db_query($sql);
$linhas       = pg_num_rows($result);
if ($linhas==0) {?>
  <table width='100%'>
   <tr>
    <td align='center'>
     <font color='#FF0000' face='arial'>
      <b>Nenhum registro encontrado.<br>
      <input type='button' value='Fechar' onclick='window.close()'></b>
     </font>
    </td>
   </tr>
  </table>
 <?php
 exit;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
.cabec{
 text-align: left;
 font-size: 13;
 font-weight: bold;
 color: #DEB887;
 background-color:#444444;
 border:1px solid #CCCCCC;
}
.aluno{
 font-size: 11;
}
</style>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<a name="topo"></a>
<form name="form1" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td align="center" valign="top" bgcolor="#CCCCCC">
   <br>
   <fieldset style="width:95%"><legend><b><?=$nome?>  -  Alunos Matriculados</b></legend>
           <table border='1px' width="100%" bgcolor="#cccccc" style="" cellspacing="0px">
            <tr>
             <td>
               <input name='voltar' type='button' value='Voltar' Onclick='parent.db_iframe_escolas.hide();'></input>
             </td>
            </tr>
         <tr bgcolor="">
         <td>
         <table border='1px' width="100%" bgcolor="#cccccc" style="" cellspacing="0px">
          <tr><td width="65%"><b>Turma</b></td>
            <td><b>Turno</b></td>
            <td><b>Quantidade de Alunos</b></td></tr>

           <?php if ($linhas>0) {

           	$cor1 = "#dbdbdb";
            $cor2 = "#f3f3f3";
           for ($w = 0; $w < $linhas; $w++) {
              db_fieldsmemory($result,$w);
           	?>
           	<tr bgcolor="<?=$cor2?>">
            <td width="65%">
            <a href="javascript:js_alunos(<?=$ed57_i_codigo?>,<?=$ed11_i_codigo?>)"
               title="Veja os alunos matriculados nesta turma"><?=$ed57_c_descr?></a></td>
            <td>Turno: <?=$ed15_c_nome?></td>
             <td>xxxx </td>
            </tr>
            <?}
           }else{?>

           <table border='1px' width="100%" bgcolor="#cccccc" style="" cellspacing="0px">
           <tr bgcolor="#EAEAEA">
            <td class='aluno'>NENHUMA TURMA NESTE CALENDÁRIO.</td>
           </tr>
          </table>
           <?php }?>
          </table>
         </td>
         </tr>
      </table>
   </fieldset>
  </td>
 </tr>
</table>
</form>
</body>
</html>
<script>
function js_alunos(turma,ed11_i_codigo) {

 	 top = ( screen.availHeight-710) / 2;
	 left = ( screen.availWidth-800 ) / 2;   
	 js_OpenJanelaIframe("","db_iframe_matriculas","func_etapacardapioescola.php?turma="+turma+
			             "&etapaserie="+ed11_i_codigo,"Alunos Matriculados na Turma ",true,top, left, 900,400
			            );

}
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
