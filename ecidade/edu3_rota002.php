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
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_itinerario_classe.php"));
include(modification("classes/db_rotaaluno_classe.php"));
include(modification("classes/db_veicretirada_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$claluno = new cl_aluno;
$cllinha = new cl_linha;
$clitinerario = new cl_itinerario;
$clrotaaluno = new cl_rotaaluno;
$clrotamov = new cl_rotamov;
$clveicretirada = new cl_veicretirada;
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$claluno->rotulo->label();
$cllinha->rotulo->label();
$clrotaaluno->rotulo->label();
$clitinerario->rotulo->label();
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
.titulo{
 font-size: 11;
 color: #DEB887;
 background-color:#444444;
 font-weight: bold;
 border: 1px solid #f3f3f3;
}
.cabec1{
 font-size: 11;
 color: #000000;
 background-color:#999999;
 font-weight: bold;
}
.aluno{
 color: #000000;
 font-family : Tahoma;
 font-size: 10;
 font-weight: bold;
}
.aluno1{
 color: #000000;
 font-family : Tahoma;
 font-weight: bold;
 text-align: center;
 font-size: 10;
}
</style>
</style>
</head>
<body bgcolor="#f3f3f3" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table bgcolor="#f3f3f3" width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td valign="top" bgcolor="#CCCCCC">
   <table border="0" bgcolor="#f3f3f3" width="100%" cellspacing="0" cellpading="0" height="800" >
    <?php if($evento==1){?>
    <tr>
     <td valign="top" >
      <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Itinerários</b></legend>
      <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="4">
       <tr>
        <td>
         <?=@$Led218_v_nome?> <?=@$ed218_v_nome==""?"Não Informado":$ed218_v_nome?>
         &nbsp;&nbsp;
         <?=@$Led218_d_datacad?> <?=@$ed218_d_datacad==""?"Não Informado":$ed218_d_datacad?>
        </td>
       </tr>
      </table>
      </fieldset>
     </td>
    </tr>
    <?php }?>
    <?php if($evento==2){?>
     <tr>
    <td valign="top" >
     <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Alunos por Rota</b></legend>
     <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
      <?php
      $result = $clrotaaluno->sql_record($clrotaaluno->sql_query("","*","ed219_i_codigo"," ed219_i_rota = $chavepesquisa"));
      if($clrotaaluno->numrows>0){
       ?>
        <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
             <td width="20%" colspan="1">
               <?=@$Led219_d_datainicio?>
             </td>
             <td width="20%" colspan="1">
               <?=@$Led219_d_datafim?>
             </td>
             <td width="20%" colspan="1">
              <?=@$Led219_i_aluno?>
             </td>
            </tr>
           </table>
          </td>
         </tr>
       <?php
       for($f=0;$f<$clrotaaluno->numrows;$f++){
        db_fieldsmemory($result,$f);
        ?>
        <tr>
         <td>
          <table width="100%" cellspacing="0" cellpading="0">
           <tr>
            <td width="20%" colspan="1">
            <?=@db_formatar($ed219_d_datainicio,'d')?>
            </td>
            <td width="20%" colspan="1">
             <?=@db_formatar($ed219_d_datafim,'d')?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ed47_v_nome?>
            </td>
           </tr>
          </table>
         </td>
        </tr>
       <?php
       }
      }else{
       ?>
       <tr>
        <td>
         Nenhum registro.
        </td>
       </tr>
       <?php
      }
      ?>
     </table>
     </fieldset>
    </td>
   </tr>
   <?php }?>
    <?php if($evento==3){?>
     <tr>
    <td valign="top" >
     <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Movimentação das Rotas</b></legend>
     <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
      <?php
      $result1 = $clrotamov->sql_record($clrotamov->sql_query("","*","ed220_i_codigo"," ed220_i_rota = $chavepesquisa"));
      if($clrotamov->numrows>0){
       ?>
        <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
            <td width="20%" colspan="1">
               <b>Código</b>
             </td>
             <td width="20%" colspan="1">
               <b>Data Cadastro</b>
             </td>
             <td width="20%" colspan="1">
               <b>Hora Cadastro</b>
             </td>
             <td width="20%" colspan="1">
              <b>Rota</b>
             </td>
             <td width="20%" colspan="1">
              <b>Destino Retirada</b>
             </td>
            </tr>
           </table>
          </td>
         </tr>
       <?php
       for($f=0;$f<$clrotamov->numrows;$f++){
        db_fieldsmemory($result1,$f);
        ?>
        <tr>
         <td>
          <table width="100%" cellspacing="0" cellpading="0">
           <tr>
           <td width="20%" colspan="1">
             <?=@$ed220_i_codigo?>
            </td>
            <td width="20%" colspan="1">
            <?=@db_formatar($ed220_d_datacad,'d')?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ed220_c_horacad?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ed217_c_nome?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve60_destino?>
            </td>
           </tr>
          </table>
         </td>
        </tr>
       <?php
       }
      }else{
       ?>
       <tr>
        <td>
         Nenhum registro.
        </td>
       </tr>
       <?php
      }
      ?>
     </table>
     </fieldset>
    </td>
   </tr>
   <?php }?>
   </table>
  </td>
 </tr>
</table>
</body>
</html>
