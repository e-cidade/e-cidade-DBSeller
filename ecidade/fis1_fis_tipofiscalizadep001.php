<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("classes/db_fis_fisdocdep_classe.php");
include modification("dbforms/db_funcoes.php");


db_postmemory($HTTP_POST_VARS);
$clfisdocdep = new cl_fis_fisdocdep;
$db_botao = true;
$sqlerro = false;

if(isset($incluir)){
  db_inicio_transacao();
  
  $num = count($fd02_coddep);
  for ($i=0; $i < count($fd02_coddep); $i++) { 
    
    $departamento = $fd02_coddep[$i];
    $clfisdocdep->fd02_coddep  = $departamento;
    $clfisdocdep->fd02_codtipo = $y27_codtipo;
    $clfisdocdep->fd02_instit  = db_getsession('DB_instit');

    $clfisdocdep->incluir();
  }
  
  db_fim_transacao();
}
elseif (isset($alterar)) {

  db_inicio_transacao();

  $depts = array();
  $depts = $fd02_coddep;
  $depin = array();

  if(!isset($fd02_coddep)){
    $resuldep = db_query("select * from fiscalizacao.fis_fisdocdep where fd02_codtipo = ".$y27_codtipo);
    $var = pg_num_rows($resuldep);
    if(pg_num_rows($resuldep) > 0){
      $clfisdocdep->fd02_codtipo = $y27_codtipo;
      $clfisdocdep->fd02_instit = db_getsession('DB_instit');
      $clfisdocdep->excluirtodos();
    }
  }

    if(isset($fd02_coddep)){
    $depexcluir = implode(',', $fd02_coddep);
    $sqlex = db_query("select * from fiscalizacao.fis_fisdocdep where fd02_coddep not in (".$depexcluir.") and fd02_codtipo =".$y27_codtipo." and fd02_instit =".db_getsession('DB_instit'));
    if (pg_num_rows($sqlex) > 0) {    
      
      for ($i=0; $i < pg_num_rows($sqlex); $i++) { 

        db_fieldsmemory($sqlex,$i);
        $clfisdocdep->fd02_coddep  = $fd02_coddep;
        $clfisdocdep->fd02_codtipo = $y27_codtipo;
        $clfisdocdep->fd02_instit  = db_getsession('DB_instit');

        for($a=0; $a < count($depts); $a++){
          if($depts[$a] == $fd02_coddep){
            $depin[] = '';           
          }else{
            $depin[] = $depts[$a];
          }
        }
        $clfisdocdep->excluir();
      }
      if($clfisdocdep->erro_status==0){
        $sqlerro = true;
      } 
    }
    $linhas = array();
    if($depin == null){
      $linhas = $depts;
    }else{
      $linhas = $depin;
    }
    if($sqlerro == false){
      for ($a=0; $a < count($linhas); $a++) { 
        
        $departamento = $linhas[$a];
        $clfisdocdep->fd02_coddep  = $departamento;
        $clfisdocdep->fd02_codtipo = $y27_codtipo;
        $clfisdocdep->fd02_instit  = db_getsession('DB_instit');

        $Metodo  = "select * from fiscalizacao.fis_fisdocdep where fd02_codtipo =".$y27_codtipo;
        $Metodo .= " and fd02_coddep = ".$departamento;
        $Metodo .= " and fd02_instit = ".db_getsession('DB_instit');
        $RMetodo = db_query($Metodo);
    

        if(pg_num_rows($RMetodo) == 0){
          $clfisdocdep->incluir();
        }else{
          db_fieldsmemory($RMetodo,0);
          $clfisdocdep->fd02_codigo = $fd02_codigo;
          $clfisdocdep->alterar();
        }
      }

      if($clfisdocdep->erro_status==0){
        $sqlerro = true;
      } 
    }
  }
  db_fim_transacao($sqlerro);  

}

if(isset($chavepesquisa)){

  $sql    = "select * from fiscalizacao.fis_fisdocdep where fd02_codtipo =".$chavepesquisa;
  $result = db_query($sql);
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
<br>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php 
	include modification("forms/db_frm_fis_tipofiscalizadep.php");
	?>
    </center>
	</td>
  </tr>
</table>

</body>
</html>
<?php 
if(isset($incluir) || isset($alterar)){
  if($clfisdocdep->erro_status=="0"){
    $clfisdocdep->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clfisdocdep->erro_campo!=""){
      echo "<script> document.form1.".$clfisdocdep->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfisdocdep->erro_campo.".focus();</script>";
    };
  }else{
    $clfisdocdep->erro(true,false);
    echo "<script>parent.iframe_tipodep.location.href='fis1_fis_tipofiscalizadep001.php?chavepesquisa=$y27_codtipo&y27_codtipo=$y27_codtipo&db_opcao=2';</script>";
    echo "<script>parent.mo_camada('tipodep');</script>";
    echo "<script>parent.document.formaba.tipodep.disabled=false;</script>";
  };
}
?>