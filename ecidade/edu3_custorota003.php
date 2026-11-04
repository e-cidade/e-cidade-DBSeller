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
include(modification("classes/db_linha_classe.php"));
include(modification("classes/db_itinerario_classe.php"));
include(modification("classes/db_veicretirada_classe.php"));
include(modification("classes/db_veicmanut_classe.php"));
include(modification("classes/db_rotamov_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$cllinha = new cl_linha;
$clveicmanut = new cl_veicmanut;
$clrotamov = new cl_rotamov;
$clveicretirada = new cl_veicretirada;
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$cllinha->rotulo->label();
$clveicretirada->rotulo->label();
$clveicmanut->rotulo->label();
$db_opcao = 1;
$db_botao = true;
$datainicial=substr($datainicial,6,4)."-".substr($datainicial,3,2)."-".substr($datainicial,0,2);
$datafim=substr($datafim,6,4)."-".substr($datafim,3,2)."-".substr($datafim,0,2);
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
     <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Por Período</b></legend>
     <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
      <?php
      $sql= "select sum ( ve61_medidadevol - ve60_medidasaida ),ve01_placa,ve61_medidadevol,ve61_horadevol,
             ve61_datadevol,ve60_medidasaida,ve60_horasaida,ve60_datasaida
             from veicretirada
             inner join veicdevolucao on ve61_veicretirada= ve60_codigo
             inner join rotamov on ed220_i_veicretirada = ve60_codigo
             inner join rota on ed217_i_codigo = ed220_i_rota
             inner join veiculos on ve01_codigo = ve60_veiculo
             where ed220_i_rota=$chavepesquisa
             and ve60_datasaida between '$datainicial' and '$datafim'
             group by ve01_placa,ve61_medidadevol,ve61_horadevol,ve61_datadevol,ve60_medidasaida,
             ve60_horasaida,ve60_datasaida";
      $result = db_query($sql);
      $linhas= pg_num_rows($result);
      if($linhas>0){
       ?>
        <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
             <td width="20%" colspan="2">
               <b>Placa</b>
             </td>
             <td width="20%" colspan="2">
               <b>KMs Devolução</b>
             </td>
             <td width="20%" colspan="2">
              <b>Hora Devolução</b>
             </td>
             <td width="20%" colspan="2">
              <b>Data Devolução</b>
             </td>
             <td width="10%" colspan="2">
              <b>KMs Saída</b>
             </td>
             <td width="20%" colspan="2">
              <b>Hora</b>
             </td>
             <td width="20%" colspan="2">
              <b>Data</b>
             </td>
            </tr>
           </table>
          </td>
         </tr>
       <?php
       for($f=0;$f<$linhas;$f++){
        db_fieldsmemory($result,$f);
        ?>
         <tr>
         <td>
          <table width="100%" cellspacing="0" cellpading="0">
           <tr>
           <td width="20%" colspan="4">
             <?=@$ve01_placa?>
            </td>
           <td width="20%" colspan="4">
             <?=@$ve61_medidadevol?>
            </td>
            <td width="20%" colspan="4">
             <?=@$ve61_horadevol?>
            </td>
            <td width="20%" colspan="4">
            <?=@db_formatar($ve61_datadevol,'d')?>
            </td>
            <td width="20%" colspan="4">
             <?=@$ve60_medidasaida?>
            </td>
            <td width="20%" colspan="4">
             <?=@$ve60_horasaida?>
            </td>
            <td width="20%" colspan="4">
             <?=@db_formatar($ve60_datasaida,'d')?>
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
    <?php if($evento==2){?>
     <tr>
    <td valign="top" >
     <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Gastos Manutenção</b></legend>
     <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
      <?php
      $sql1 ="select sum ( ve62_vlrmobra + ve62_vlrpecas ),ve62_hora,ve62_vlrpecas,ve62_data,ve01_placa,ve60_medidasaida,ve60_hora,ve60_data
              from veicmanut
              inner join veicmanutretirada on ve65_veicmanut= ve62_codigo
              inner join veicretirada on ve60_codigo= ve65_veicretirada
              inner join veiculos on ve01_codigo = ve60_veiculo
              inner join rotamov on ed220_i_veicretirada = ve60_codigo
              inner join rota on ed217_i_codigo = ed220_i_rota
              where ed220_i_rota=$chavepesquisa
              group by ve62_hora,ve62_vlrpecas,ve62_data,ve01_placa,ve60_medidasaida,ve60_hora,ve60_data";
      $result1 = db_query($sql1);
      $linhas1= pg_numrows($result1);
      if($linhas1>0){
       ?>
        <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
            <td width="20%" colspan="1">
               <b>Placa</b>
             </td>
             <td width="20%" colspan="1">
               <b>Hora Manutenção</b>
             </td>
             <td width="20%" colspan="1">
               <b>Data Manutenção</b>
             </td>
             <td width="20%" colspan="1">
               <b>KMs Saída</b>
             </td>
             <td width="20%" colspan="1">
               <b>Hora Retirada</b>
             </td>
             <td width="20%" colspan="1">
               <b>Data Retirada</b>
             </td>
            </tr>
           </table>
          </td>
         </tr>
       <?php
       for($f=0;$f<$linhas1;$f++){
        db_fieldsmemory($result1,$f);
        ?>
        <tr>
         <td>
          <table width="100%" cellspacing="0" cellpading="0">
           <tr>
           <td width="20%" colspan="1">
             <?=@$ve01_placa?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve62_hora?>
            </td>
            <td width="20%" colspan="1">
             <?=@db_formatar($ve62_data,'d')?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve60_medidasaida?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve60_hora?>
            </td>
            <td width="20%" colspan="1">
             <?=@db_formatar($ve60_data,'d')?>
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
     <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Abastecimento</b></legend>
     <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
      <?php
      $sql1 ="select sum ( ve70_litros ),ve70_litros,ve70_data,ve70_hora,ve70_medida,ve01_placa
               from veicabast
              inner join veicabastretirada on ve73_veicabast= ve70_codigo
              inner join veicretirada on ve60_codigo= ve73_veicretirada
              inner join veiculos on ve01_codigo = ve60_veiculo
              inner join rotamov on ed220_i_veicretirada = ve60_codigo
              inner join rota on ed217_i_codigo = ed220_i_rota
              where ed220_i_rota=$chavepesquisa
              group by ve70_litros,ve70_data,ve70_hora,ve70_medida,ve01_placa";
      $result1 = db_query($sql1);
      $linhas1= pg_numrows($result1);
      if($linhas1>0){
       ?>
        <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
            <td width="20%" colspan="1">
               <b>Placa</b>
             </td>
             <td width="20%" colspan="1">
               <b>Data Abastecimento</b>
             </td>
             <td width="20%" colspan="1">
               <b>Hora Abastecimento</b>
             </td>
             <td width="20%" colspan="1">
               <b>KMs Abastecimento</b>
             </td>
             <td width="20%" colspan="1">
               <b>Litros</b>
             </td>
            </tr>
           </table>
          </td>
         </tr>
       <?php
       for($f=0;$f<$linhas1;$f++){
        db_fieldsmemory($result1,$f);
        ?>
        <tr>
         <td>
          <table width="100%" cellspacing="0" cellpading="0">
           <tr>
           <td width="20%" colspan="1">
             <?=@$ve01_placa?>
            </td>
            <td width="20%" colspan="1">
             <?=@db_formatar($ve70_data,'d')?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve70_hora?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve70_medida?>
            </td>
            <td width="20%" colspan="1">
             <?=@$ve70_litros?>
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
