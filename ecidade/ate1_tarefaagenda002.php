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
include(modification("classes/db_tarefa_agenda_classe.php"));
include(modification("classes/db_tarefa_classe.php"));
include(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_POST_VARS);
$cltarefa_agenda = new cl_tarefa_agenda;
$cltarefa        = new cl_tarefa;
$db_opcao        = 22;
$db_botao        = false;
$sqlerro         = false;   
  
  if(isset($incluir)){
    db_inicio_transacao();
    $cltarefa_agenda->at13_tarefa  = $at13_tarefa;
    $cltarefa_agenda->at13_dia     = $at13_dia_ano."-".$at13_dia_mes."-".$at13_dia_dia;
    $cltarefa_agenda->at13_horaini = $at13_horaini;
    $cltarefa_agenda->at13_horafim = $at13_horafim;
    $cltarefa_agenda->incluir($at13_sequencial);
    
    if ($cltarefa_agenda->erro_status   == 0) {
      $erro_msg = $cltarefa_agenda->erro_msg;
      $sqlerro  = true;  
    }
    
    db_fim_transacao($sqlerro);
    db_redireciona("ate1_tarefaagenda002.php?at13_tarefa=".$at13_tarefa);
   
   }
   
   if(isset($alterar)){
    db_inicio_transacao();
    $cltarefa_agenda->at13_tarefa  = $at13_tarefa;
    $cltarefa_agenda->at13_dia     = $at13_dia_ano."-".$at13_dia_mes."-".$at13_dia_dia;
    $cltarefa_agenda->at13_horaini = $at13_horaini;
    $cltarefa_agenda->at13_horafim = $at13_horafim;
    $cltarefa_agenda->alterar($at13_sequencial);
    
    if ($cltarefa_agenda->erro_status   == 0) {
      $erro_msg = $cltarefa_agenda->erro_msg;
      $sqlerro  = true;  
    }
    
    db_fim_transacao($sqlerro);
    db_redireciona("ate1_tarefaagenda002.php?at13_tarefa=".$at13_tarefa);
   
   }
   
   if(isset($excluir)){
    db_inicio_transacao();
    $cltarefa_agenda->excluir($at13_sequencial);
    
    if ($cltarefa_agenda->erro_status   == 0) {
      $erro_msg = $cltarefa_agenda->erro_msg;
      $sqlerro  = true;  
    }
    
    db_fim_transacao($sqlerro);
    db_redireciona("ate1_tarefaagenda002.php?at13_tarefa=".$at13_tarefa);
   
   }
   
   if(isset($opcao) && ( $opcao == "alterar" || $opcao == "excluir") ){
      $rsTarefaAgenda = $cltarefa_agenda->sql_record($cltarefa_agenda->sql_query_file($at13_sequencial));
      if($cltarefa_agenda->numrows > 0){
        db_fieldsmemory($rsTarefaAgenda,0); 
      }
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php
	include(modification("forms/db_frmtarefaagenda.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php
if(isset($alterar) || isset($excluir) || isset($incluir)){
    db_msgbox($erro_msg);
    if($cltarefausu->erro_campo!=""){
        echo "<script> document.form1.".$cltarefausu->erro_campo.".style.backgroundColor='#99A9AE';</script>";
       echo "<script> document.form1.".$cltarefausu->erro_campo.".focus();</script>";
    }
}
?>