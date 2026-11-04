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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_matparam_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/configuracao/DBEstrutura.model.php"));
require_once(modification("model/configuracao/DBEstruturaValor.model.php"));
require_once(modification("model/estoque/MaterialGrupo.model.php"));

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$clmatparam = new cl_matparam;
$db_opcao = 22;
$db_botao = false;
if(isset($alterar)){
  
   $lSqlErro = false;
   db_inicio_transacao();
   
   $iDbEstruturaAnterior = $m90_db_estrutura_anterior;
   $iDbEstruturaNova     = $m90_db_estrutura;
   
   /**
    * Valido se o usuário alterou a estrutura que deve ser utilizada pelo grupo de material
    * Alteramos os dados em DB_EstruturaValor para o novo parâmetro selecionado pelo usuário
    */
   if ($iDbEstruturaAnterior != $iDbEstruturaNova) {
     
     try {
       
       $oEstruturaNova           = new DBEstrutura($iDbEstruturaNova);
       $aNiveisEstruturaNova     = $oEstruturaNova->getNiveis();
       $oEstruturaAnterior       = new DBEstrutura($iDbEstruturaAnterior);
       $aNiveisEstruturaAnterior = $oEstruturaAnterior->getNiveis();
       
       $iTotalNiveisNova     = count($aNiveisEstruturaNova);
       $iTotalNiveisAnterior = count($aNiveisEstruturaAnterior);
       
       if ($iTotalNiveisNova < $iTotalNiveisAnterior) {
         throw new Exception("Não é possível alterar a Estrutura dos Grupos para níveis menores que o atual.");
       }
       
       $iTotalNivelCriar  = ($iTotalNiveisNova - $iTotalNiveisAnterior);
       $aNovaEstrutura   = array();
       foreach ($aNiveisEstruturaNova as $oStdNivel) {
         
         if ($oStdNivel->nivel <= $iTotalNivelCriar) {

           if ($oStdNivel->nivel == 1) {
             $aNovaEstrutura[] = str_pad('1', $oStdNivel->digitos, STR_PAD_LEFT, "0");
           } else {
             $aNovaEstrutura[] = str_pad('0', $oStdNivel->digitos, STR_PAD_LEFT, "0");
           }
         }
       }
       $sEstruturaNova          = implode('.', $aNovaEstrutura);
       $oDaoEstruturaValor      = db_utils::getDao('db_estruturavalor');
       $sSqlBuscaEstruturaValor = $oDaoEstruturaValor->sql_query_file(null, "*", null, "db121_db_estrutura = {$iTotalNiveisAnterior}");
       $rsBuscaEstruturaValor   = $oDaoEstruturaValor->sql_record($sSqlBuscaEstruturaValor);
       
       if ($oDaoEstruturaValor->numrows > 0) {
         
         for ($iRowEstrutura = 0; $iRowEstrutura < $oDaoEstruturaValor->numrows; $iRowEstrutura++) {
           
           $oDadoEstruturaValor = db_utils::fieldsMemory($rsBuscaEstruturaValor, $iRowEstrutura);
           
           $oDaoUpdadeEstruturaValor = db_utils::getDao('db_estruturavalor');
           $oDaoUpdadeEstruturaValor->db121_sequencial        = $oDadoEstruturaValor->db121_sequencial;
           $oDaoUpdadeEstruturaValor->db121_db_estrutura      = $iDbEstruturaNova;
           $oDaoUpdadeEstruturaValor->db121_estrutural        = "{$sEstruturaNova}.{$oDadoEstruturaValor->db121_estrutural}";
           $oDaoUpdadeEstruturaValor->db121_descricao         = $oDadoEstruturaValor->db121_descricao;
           $oDaoUpdadeEstruturaValor->db121_estruturavalorpai = $oDadoEstruturaValor->db121_estruturavalorpai;
           $oDaoUpdadeEstruturaValor->db121_nivel             = ($oDadoEstruturaValor->db121_nivel+$iTotalNivelCriar);
           $oDaoUpdadeEstruturaValor->db121_tipoconta         = $oDadoEstruturaValor->db121_tipoconta;
           $oDaoUpdadeEstruturaValor->alterar($oDadoEstruturaValor->db121_sequencial);
           
           if ($oDaoUpdadeEstruturaValor->erro_status == 0) {
             throw new Exception("Impossivel alterar os dados da estrutura.");
           }
         }
       }
       
       
     } catch (Exception $eErro) {
       db_msgbox($eErro->getMessage());
       $lSqlErro = true;
     }
   }
   
   
   $result = $clmatparam->sql_record($clmatparam->sql_query());
   if($result==false || $clmatparam->numrows==0){
     $clmatparam->incluir();
   }else{
     $clmatparam->alterar();
   }
   db_fim_transacao($lSqlErro);
}
$db_opcao = 2;
$result = $clmatparam->sql_record($clmatparam->sql_query());
if($result!=false && $clmatparam->numrows>0){
  db_fieldsmemory($result,0);
}
$db_botao = true;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
fieldset.interno table tr > td:FIRST-CHILD {
	width: 250px;
}
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table align="center" style="padding-top:15px;" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" valign="top" bgcolor="#CCCCCC"> 
			<center>
				<?php
					include(modification("forms/db_frmmatparam.php"));
				?>
    </center>
	</td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($alterar)){
  if($clmatparam->erro_status=="0"){
    $clmatparam->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clmatparam->erro_campo!=""){
      echo "<script> document.form1.".$clmatparam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clmatparam->erro_campo.".focus();</script>";
    };
  }else{
    $clmatparam->erro(true,true);
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>