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
include(modification("classes/db_fis_procfiscalfiscais_classe.php"));
include(modification("classes/db_fis_procfiscal_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_autousu_classe.php"));
require_once(modification("classes/db_fis_fiscalusuario_classe.php"));
require_once(modification('classes/db_fis_processofiscalativo_classe.php'));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
$clprocfiscal = new cl_fis_procfiscal;
$clautousu   = new cl_fis_autousu;
$clfiscalusuario   = new cl_fis_fiscalusuario;
$clprocessofiscalativo = new cl_fis_processofiscalativo;
$db_opcao = 22;
$db_botao = false;

if(isset($alterar) || isset($excluir) || isset($incluir)){
  $sqlerro = false;
  /*
$clprocfiscalfiscais->y106_sequencial = $y106_sequencial;
$clprocfiscalfiscais->y106_procfiscal = $y106_procfiscal;
$clprocfiscalfiscais->y106_cadfiscais = $y106_cadfiscais;
  */
}
if(isset($incluir)){
  if($sqlerro==false){
    db_inicio_transacao();
    
    $clprocfiscalfiscais->y106_procfiscal = $y106_procfiscal;
    $clprocfiscalfiscais->y106_cadfiscais = $y106_cadfiscais;
    $clprocfiscalfiscais->incluir(null);
    $erro_msg = $clprocfiscalfiscais->erro_msg;
    if($clprocfiscalfiscais->erro_status == '0'){
      $sqlerro=true;
    }

    if ($sqlerro == false) {
      $clprocessofiscalativo->processo_fiscal = $y106_procfiscal;
      $clprocessofiscalativo->fiscal = $y106_cadfiscais;
      $clprocessofiscalativo->ativo = $ativo;
      $clprocessofiscalativo->incluir();
      if ($cl_processofiscalativo->erro_status == '0') {
        $erro_msg = $clprocessofiscalativo->erro_msg;
        $sqlerro = true;
      }
    }

    $sqlAuto = 'select * from fiscalizacao.fis_procfiscalauto where y111_procfiscal = '.$y106_procfiscal;
    // die($sqlAuto);
    $rsAuto = db_query($sqlAuto);
    if(pg_num_rows($rsAuto) > 0){
      db_fieldsmemory($rsAuto, 0);
      $clautousu->incluir($y111_auto,$y106_cadfiscais);
    }

    $sqlNoti = 'select * from fiscalizacao.fis_procfiscalnotificacao where y110_procfiscal = '.$y106_procfiscal;
    // die($sqlAuto);
    $rsNoti = db_query($sqlNoti);
    if(pg_num_rows($rsNoti) > 0){
      db_fieldsmemory($rsNoti, 0);
      $clfiscalusuario->incluir($y110_notificacaofiscal, $y106_cadfiscais);
    }
    // die(pg_last_error());


    db_fim_transacao($sqlerro);
  }
}else if(isset($alterar)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clprocfiscalfiscais->alterar($y106_sequencial);
    $erro_msg = $clprocfiscalfiscais->erro_msg;
    if($clprocfiscalfiscais->erro_status == '0'){
      $sqlerro=true;
    }

    if ($sqlerro == false) {
      $clprocessofiscalativo->processo_fiscal = $y106_procfiscal;
      // $clprocessofiscalativo->fiscal = $y106_cadfiscais;
      $clprocessofiscalativo->ativo = $ativo;
      $clprocessofiscalativo->sequencial = $sequencial;
      $clprocessofiscalativo->alterar();
      if ($cl_processofiscalativo->erro_status == '0') {
        $erro_msg = $clprocessofiscalativo->erro_msg;
        $sqlerro = true;
      }
    }

    db_fim_transacao($sqlerro);
  }
}else if(isset($excluir)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clprocfiscalfiscais->excluir($y106_sequencial);
    $erro_msg = $clprocfiscalfiscais->erro_msg;
    if($clprocfiscalfiscais->erro_status==0){
      $sqlerro=true;
    }

    if ($sqlerro == false) {
      $clprocessofiscalativo->excluir(null, " where processo_fiscal = $y106_procfiscal and fiscal = $y106_cadfiscais");
      if ($cl_processofiscalativo->erro_status == '0') {
        $erro_msg = $clprocessofiscalativo->erro_msg;
        $sqlerro = true;
      }
    }
    $sqlAuto = 'select * from fiscalizacao.fis_procfiscalauto 
                    inner join fiscalizacao.fis_autousu on y56_codauto = y111_auto 
                    where y111_procfiscal = '.$y106_procfiscal.' and y56_id_usuario = '.$y106_cadfiscais;
    $rsAuto = db_query($sqlAuto);
    if( pg_num_rows( $rsAuto ) > 0 ){
      db_fieldsmemory( $rsAuto, 0 );
      // $clautousu->excluir($y56_codauto,$y56_id_usuario);
      $erro_msg = " Esse Fiscal está vinculado ao Auto :  ".$y111_auto."\nExclusão não permitida!";
      $sqlerro  = true; 
    }

    $sqlNoti = 'select * from fiscalizacao.fis_procfiscalnotificacao 
                    inner join fiscalizacao.fis_fiscalusuario on y38_codnoti = y110_notificacaofiscal 
                    where y110_procfiscal = '.$y106_procfiscal.' and y38_id_usuario = '.$y106_cadfiscais;
    $rsNoti = db_query($sqlNoti);
    // die($sqlNoti);
    if( pg_num_rows( $rsNoti ) > 0 ){
      db_fieldsmemory( $rsNoti , 0 );
      $getTipo = db_query("select in01_intimacao from fiscalizacao.fis_fiscalintimacao where in01_codnoti =$y38_codnoti ");
      db_fieldsmemory($getTipo,0);
      $label  = ($in01_intimacao == 'f' ? "Notificação"  : "Intimação");
      $erro_msg = " Esse está  Fiscal vinculado a ".$label." : $y38_codnoti \n Exclusão não permitida!";
      $sqlerro  = true;
      // $clfiscalusuario->excluir($y38_codnoti,$y38_id_usuario);
    }
    // die(pg_last_error());

    db_fim_transacao($sqlerro);
  }
}else if(isset($opcao)){ 
    $sql  = " select fis_procfiscalfiscais.*, fis_processofiscalativo.*,nome from fiscalizacao.fis_procfiscalfiscais ";
    $sql .= " inner join fiscalizacao.fis_cadfiscais  on  fis_cadfiscais.id_usuario = fis_procfiscalfiscais.y106_cadfiscais ";
    $sql .= " inner join fiscalizacao.fis_procfiscal  on  fis_procfiscal.y100_sequencial = fis_procfiscalfiscais.y106_procfiscal ";
    $sql .= " inner join db_usuarios  on  db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
    $sql .= " inner join db_config  on  db_config.codigo = fis_procfiscal.y100_instit ";
    $sql .= " inner join db_depart  on  db_depart.coddepto = fis_procfiscal.y100_coddepto ";
    $sql .= " inner join fiscalizacao.fis_procfiscalcadtipo  on  fis_procfiscalcadtipo.y33_sequencial = fis_procfiscal.y100_procfiscalcadtipo ";
    $sql .= " left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y106_procfiscal and y106_cadfiscais = fis_processofiscalativo.fiscal ";
    $sql .= " where y106_sequencial = $y106_sequencial ";

    $result = db_query($sql);

   //$result = $clprocfiscalfiscais->sql_record($clprocfiscalfiscais->sql_query($y106_sequencial));
   
   if($result != false){
     db_fieldsmemory($result,0);
   }
}

/*$fiscalAtivoNoProcesso = $clprocessofiscalativo->sql_record($clprocessofiscalativo->sql_query(null, '*', null, " processo_fiscal = $y106_procfiscal "));
if ($fiscalAtivoNoProcesso != false) {
  db_fieldsmemory($fiscalAtivoNoProcesso, 0);
}*/

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
  
  include(modification("forms/db_frm_fis_procfiscalfiscais.php"));

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
    
    
}
?>
