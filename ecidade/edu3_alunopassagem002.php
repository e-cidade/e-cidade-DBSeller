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
require(modification("libs/db_libpessoal.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_rechumano_classe.php"));
include(modification("classes/db_aluno_classe.php"));
include(modification("classes/db_escola_classe.php"));
include(modification("classes/db_alunopassagem_classe.php"));
include(modification("classes/db_alunopassagemqtd_classe.php"));
include(modification("classes/db_matricula_classe.php"));
include(modification("classes/db_cgm_classe.php"));
db_postmemory($HTTP_POST_VARS);
$clrechumano = new cl_rechumano;
$clcgm = new cl_cgm;
$claluno = new cl_aluno;
$clescola = new cl_escola;
$clalunopassagem = new cl_alunopassagem;
$clalunopassagemqtd = new cl_alunopassagemqtd;
$clmatricula = new cl_matricula;
$clalunopassagem->rotulo->label();
$clrechumano->rotulo->label();
$clcgm->rotulo->label();
$claluno->rotulo->label();
$clescola->rotulo->label();
$clalunopassagemqtd->rotulo->label();
$clmatricula->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed47_v_nome");
$clrotulo->label("ed217_c_origem");
$clrotulo->label("ed18_c_nome");
$clrotulo->label("ed215_i_aluno");
$db_opcao = 1;
if(isset($chavepesquisa)){
 $escola = db_getsession("DB_coddepto");
 $result1 = $clalunopassagem->sql_record($clalunopassagem->sql_query($chavepesquisa));
 db_fieldsmemory($result1,0);
 $result = $claluno->sql_record($claluno->sql_query("","*","","ed47_i_codigo = $ed47_i_codigo"));
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
    <table border="0" width="100%" cellspacing="0" cellpading="0" bgcolor="#f3f3f3">
     <tr>
      <td>
       <fieldset style="background:#f3f3f3;padding:0px;border:2px solid #000000"><legend class="cabec"><b>Nome</b></legend>
       <table border="0" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
        <tr>
         <td style="font-size:18px;font-weight:bold;font-family:verdana;">
          &nbsp;&nbsp;<?=$ed47_i_codigo?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$ed47_v_nome?>
         </td>
         <td align="right">
          <input type="button" value="Fechar" onclick="parent.db_iframe_alunopassagem.hide();">&nbsp;&nbsp;
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
         <td width="20%">
          <fieldset style="height:139px;background:#f3f3f3;padding:0px;border:4px outset #000000"><legend class="cabec"><b>Foto</b></legend>
          <table border="0" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="0">
           <tr>
            <td align="center">
             <?php
             if($ed47_o_oid!=0){
              $arquivo = "tmp/".$ed47_c_foto;
              db_query("begin");
              pg_loexport($ed47_o_oid,$arquivo);
              db_query("end");
             }else{
              $arquivo = "imagens/none1.jpeg";
             }
             ?>
             <img src="<?=$arquivo?>" width="100" height="120" style="border:0px solid #000000">
            </td>
           </tr>
          </table>
          </fieldset>
         </td>
         <td valign="top">
          <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Dados Pessoais</b></legend>
          <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="4">
           <tr>
            <td>
             <?=@$Led47_d_nasc?> <?=@db_formatar($ed47_d_nasc,'d')?>
             &nbsp;&nbsp;
             <?=@$Led47_i_nacion?> <?=@$red47_i_nacion==""?"Não Informado":$ed47_i_nacion?>
            </td>
           </tr>
           <tr>
            <td>
             <?=@$Led47_v_ender?> <?=@$ed47_v_ender==""?"Não Informado":$ed47_v_ender?>
             &nbsp;&nbsp;
             <?=@$Led47_c_numero?> <?=@$ed47_c_numero==""?"Não Informado":$ed47_c_numero?>
            </td>
           </tr>
           <tr>
            <td>
             <?=@$Led47_v_bairro?> <?=@$ed47_v_bairro==""?"Não Informado":$ed47_v_bairro?>
             &nbsp;&nbsp;
             <?=@$Led47_v_compl?> <?=@$ed47_v_compl?>
            </td>
           </tr>
           <tr>
            <td>
             <?=@$Led47_i_censomunicend?> <?=@$ed47_i_censomunicend==""?"Não Informado":$ed47_i_censomunicend?>
             &nbsp;&nbsp;
             <?=@$Led47_i_censoufend?> <?=@$ed47_i_censoufend==""?"Não Informado":$ed47_i_censoufend?>
             &nbsp;&nbsp;
             <?=@$Led47_v_cep?> <?=@$ed47_v_cep==""?"Não Informado":$ed47_v_cep?>
            </td>
           </tr>
           <tr>
            <td>
             <?=@$Led47_v_sexo?> <?=@$ed47_v_sexo=="M"?"Masculino":"Feminino"?>
             &nbsp;&nbsp;
             <?=@$Led47_i_estciv?>
             <?php
             if($ed47_i_estciv==1){
              echo "Solteiro";
             }elseif($ed47_i_estciv==2){
              echo "Casado";
             }elseif($ed47_i_estciv==3){
              echo "Viúvo";
             }elseif($ed47_i_estciv==4){
              echo "Divorciado";
             }
             ?>
            </td>
           </tr>
           <tr>
            <td>
             <?=@$Led47_v_telef?> <?=@$ed47_v_telef==""?"Não Informado":$ed47_v_telef?>
             &nbsp;&nbsp;
             <?=@$Led47_v_telcel?> <?=@$ed47_v_telcel==""?"Não Informado":$ed47_v_telcel?>
            </td>
           </tr>
          </table>
          </fieldset>
         </td>
        </tr>
        <tr>
         <td valign="top" colspan="2">
          <fieldset style="background:#f3f3f3;border:2px solid #000000"><legend class="cabec"><b>Quantidade Passagens por Alunos</b></legend>
          <table border="1" width="100%" bgcolor="#f3f3f3" cellspacing="0" cellpading="4">
         <?php
         $result1 = $clalunopassagemqtd->sql_record($clalunopassagemqtd->sql_query("","*","ed227_i_codigo"," ed227_i_alunopassagem = $chavepesquisa"));
         if($clalunopassagemqtd->numrows>0){
         ?>
            <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
            <td width="20%" colspan="1">
             <?=@$Led217_c_origem?>
             </td>
               <td width="20%" colspan="1">
             <?=@$Led227_d_datacad?>
             </td>
              <td width="20%" colspan="1">
             <?=@$Led227_d_datainicio?>
             </td>
             <td width="20%" colspan="1">
             <?=@$Led227_d_datafim?>
             </td>
             <td width="20%" colspan="1">
             <?=@$Led227_i_qtde?>
             </td>
              <td width="20%" colspan="1">
             <?=@$Led230_f_valor?>
             </td>
             <td width="20%" colspan="1">
             <b>Valor</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             </td>
             <td width="20%" colspan="1">
             <b>Valor Total</b>
             </td>
             </td>
            </tr>
           </table>
          </td>
         </tr>
         <?php
         for($f=0;$f<$clalunopassagemqtd->numrows;$f++){
         db_fieldsmemory($result1,$f);
         ?>
         <tr>
          <td>
           <table width="100%" cellspacing="0" cellpading="0">
            <tr>
              <td width="20%" colspan="1">
              <?=@$ed217_c_origem?>
             </td>
             <td width="20%" colspan="1">
              <?=@db_formatar($ed227_d_datacad,'d')?>
             </td>
              <td width="20%" colspan="1">
              <?=@db_formatar($ed227_d_datainicio,'d')?>
             </td>
             <td width="20%" colspan="1">
              <?=@db_formatar($ed227_d_datafim,'d')?>
             </td>
             <td width="20%" colspan="1">
              <?=@$ed227_i_qtde?>
              </td>
             <td width="20%" colspan="1">
              <?=@$ed230_f_valor?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             </td>
             <td width="20%" colspan="1">
              <?=@$ed230_f_valor * $ed227_i_qtde?>
             </td>
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
       </td>
      </tr>
     </table>
   </td>
  </tr>
 </table>
 </body>
 </html>
<script>
function js_imprimir(chave){
 jan = window.open('edu2_alunopassagem001.php?chavepesquisa='+chave,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
 jan.moveTo(0,0);
}
</script>