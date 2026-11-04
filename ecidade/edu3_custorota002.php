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
include(modification("classes/db_aluno_classe.php"));
include(modification("classes/db_alunocurso_classe.php"));
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
include(modification("classes/db_veicretirada_classe.php"));
include(modification("classes/db_itinerario_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$claluno = new cl_aluno;
$clitinerario = new cl_itinerario;
$clrotulo = new rotulocampo;
$cllinha = new cl_linha;
$clrotamov = new cl_rotamov;
$clveicretirada = new cl_veicretirada;
$clrotulo->label("ed31_i_curso");
$claluno->rotulo->label();
$cllinha->rotulo->label();
$clitinerario->rotulo->label();
$clrotamov->rotulo->label();
$clveicretirada->rotulo->label();
$db_opcao = 1;
$db_botao = true;
if(isset($chavepesquisa)){
 $result = $cllinha->sql_record($cllinha->sql_query("","*",""," ed217_i_codigo = $chavepesquisa"));
 db_fieldsmemory($result,0);
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
 text-align: center;
 font-size: 11;
 color: #DEB887;
 background-color:#444444;
 border:1px solid #CCCCCC;
 font-weight: bold;
}
</style>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td valign="top" bgcolor="#CCCCCC">
   <fieldset style="width:97%"><legend><b>Consulta Custo de Rotas</b></legend>
    <table border="0" width="100%" cellspacing="0" cellpading="0" bgcolor="#f3f3f3">
     <tr>
      <td>
       <fieldset style="background:#f3f3f3;padding:0px;border:2px solid #000000">
       <legend class="cabec"><b>Nome</b></legend>
       <table border="0" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
        <tr>
         <td style="font-size:18px;font-weight:bold;font-family:verdana;">
          &nbsp;&nbsp;<?=$ed217_i_codigo?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$ed217_c_nome?>
         </td>
         <td align="right">
          <input type="button" value="Fechar" onclick="parent.db_iframe_custolinha.hide();">&nbsp;&nbsp;
          <input type="button" value="Imprimir" onclick="js_imprimir(<?=$chavepesquisa?>)">&nbsp;&nbsp;
         </td>
        </tr>
       </table>
       </fieldset>
      </td>
     </tr>
     <tr>
      <td>
       <table border="0" width="100%" cellspacing="0" cellpading="0">
        <tr>
         <td width="21%">
         </td>
         <td valign="top">
          <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Rotas</b></legend>
          <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="4">
           <tr>
            <td>
            <?=@$Led217_c_descr?> <?=@$ed217_c_descr==""?"Não Informado":$ed217_c_descr?>
             &nbsp;&nbsp;
              <?=@$Led217_d_datacad?> <?=@db_formatar($ed217_d_datacad,'d')?>
            </td>
           </tr>
          </table>
          </fieldset>
         </td>
        </tr>
        <tr>
        <td valign="top">
        <table border="0" width="100%">
         <tr>
          <td nowrap title="Período">
          Período<br>
          <?php db_inputdata('datainicial',@$datainicial_dia,@$datainicial_mes,@$datainicial_ano,true,'text',$db_opcao,"")?>
          <?php db_inputdata('datafim',@$datafim_dia,@$datafim_mes,@$datafim_ano,true,'text',$db_opcao,"")?>
          </td>
         </tr>
           <tr>
            <td id="menu1" bgcolor="#444444" style="border:2px outset #f3f3f3" onmouseover="document.getElementById('menu1').style.border='2px inset #f3f3f3'" onmouseout="document.getElementById('menu1').style.border='2px outset #f3f3f3'">
            <a style="color:#DEB887;font-weight:bold;" href="javascript:js_periodo('1');">
             Por período
            </a>
            </td>
           </tr>
            <tr>
            <td id="menu2" bgcolor="#444444" style="border:2px outset #f3f3f3" onmouseover="document.getElementById('menu2').style.border='2px inset #f3f3f3'" onmouseout="document.getElementById('menu2').style.border='2px outset #f3f3f3'">
             <a style="color:#DEB887;font-weight:bold;" href="javascript:js_periodo('2');">
             Gastos Manutenção
             </a>
            </td>
           </tr>
           <tr>
            <td id="menu3" bgcolor="#444444" style="border:2px outset #f3f3f3" onmouseover="document.getElementById('menu3').style.border='2px inset #f3f3f3'" onmouseout="document.getElementById('menu3').style.border='2px outset #f3f3f3'">
             <a style="color:#DEB887;font-weight:bold;" href="javascript:js_periodo('3');">
             Abastecimento
             </a>
            </td>
           </tr>
          </table>
         </td>
         <td valign="top">
          <iframe name="iframe_dados" src="" frameborder="0" width="99%" height="500"></iframe>
         </td>
        </tr>
       </table>
      </td>
     </tr>
    </table>
   </fieldset>
  </td>
 </tr>
</table>
</body>
</html>
<script>
function js_imprimir(chave){
 jan = window.open('edu2_custolinha000.php?chavepesquisa='+chave,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
 jan.moveTo(0,0);
}

function js_periodo(chave){
datainicial = document.getElementById("datainicial").value;
datafim = document.getElementById("datafim").value;
if(datainicial=="" || datafim==""){
 alert("data em branco");
}else{
iframe_dados.location="edu3_custolinha003.php?chavepesquisa=<?=$chavepesquisa?>&evento="+chave+"&datainicial="+datainicial+"&datafim="+datafim;
}
}

</script>
