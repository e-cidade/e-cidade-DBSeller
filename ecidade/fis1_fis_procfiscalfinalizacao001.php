<?php 
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1); 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_procfiscal_classe.php"));
require_once(modification('classes/db_fis_processoprorrogacaofinalizacao_classe.php'));
require_once(modification("dbforms/db_classesgenericas.php"));

$clprocfiscal = new cl_fis_procfiscal;
$clprocessoprorrogacaofinalizacao = new cl_fis_processoprorrogacaofinalizacao;
$db_opcao = 1;

db_postmemory($HTTP_POST_VARS);

if (isset($incluir)) {
	$sqlerro=false;
// print_r($_POST); exit;
	db_inicio_transacao();



	if(isset($processo_fiscal) and $processo_fiscal != ""){
		$sql = "SELECT * FROM fiscalizacao.fis_processoprorrogacaofinalizacao WHERE processo_fiscal = ".$processo_fiscal." order by sequencial desc limit 1";
        $result = db_query($sql);
                // die($sql);
        if( pg_num_rows($result) > 0 ){

			$procfisc = db_utils::fieldsMemory($result, 0);
				
			//$procfisc->processo_fiscal;

			if ($procfisc->situacao == 2 && $situacao == 2){
			    $sqlerro=true;
	            $erro_msg = "Processo finalizado anteriormente.";
			}
			
			if ($procfisc->situacao == 1 && $situacao == 1){
	            $sqlerro=true;
	            $erro_msg = "Processo prorrogado anteriormente.";

			}
        }        

		if($sqlerro == false){

			$clprocessoprorrogacaofinalizacao->processo_fiscal = $processo_fiscal;
			$clprocessoprorrogacaofinalizacao->data_abertura   = date('d/m/Y');
			if (isset($data_prorrogacao) && $data_prorrogacao != "") {
				$clprocessoprorrogacaofinalizacao->data_prorrogacao = "$data_prorrogacao_dia/$data_prorrogacao_mes/$data_prorrogacao_ano";
			}else{
				$clprocessoprorrogacaofinalizacao->data_prorrogacao = "//";	
			}

			if (isset($data_fim) && $data_fim != "") {
				$clprocessoprorrogacaofinalizacao->data_fim = "$data_fim_dia/$data_fim_mes/$data_fim_ano";
			}else{
				$clprocessoprorrogacaofinalizacao->data_fim = "//";
			}
			
			$clprocessoprorrogacaofinalizacao->id_usuario = db_getsession("DB_id_usuario");
			$clprocessoprorrogacaofinalizacao->situacao   = $situacao;
			$clprocessoprorrogacaofinalizacao->observacao = $observacao;
			$clprocessoprorrogacaofinalizacao->incluir();
		
			if ($clprocessoprorrogacaofinalizacao->erro_status == 0) {
				$sqlerro = true;
			}

			$erro_msg = $clprocessoprorrogacaofinalizacao->erro_msg;
		} 
	}

	// die(pg_last_error());

	db_fim_transacao($sqlerro);
	if($sqlerro == false){
		unset($processo_fiscal);
		unset($data_fim);
		unset($data_fim_dia);
		unset($data_fim_mes);
		unset($data_fim_ano);
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
<body bgcolor=#CCCCCC onLoad="a=1">
	<div class="container">
	    <?php require_once(modification("forms/db_frm_fis_procfiscalfinalizacao.php")); ?>
	</div>
	<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit")); ?>
</body>
</html>

<?php 
if (isset($erro_msg) and $erro_msg != ''){
	if ($sqlerro == true) {
		$clprocessoprorrogacaofinalizacao->erro(true, false);
	} else {
		db_msgbox($erro_msg);
		//$clprocessoprorrogacaofinalizacao->erro(true, true);
	}
}
?>
