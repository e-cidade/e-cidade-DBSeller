<?php 
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
// ini_set('display_errors',1);
// ini_set('display_startup_erros',1);
// error_reporting(E_ALL);
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("libs/db_utils.php");
require_once  modification("classes/db_fis_autotipo_classe.php");
require_once  modification("classes/db_fis_autotipobaixaproc_classe.php");
require_once  modification("classes/db_fis_autotipobaixaprocproc_classe.php");
require_once  modification("classes/db_fis_autotipobaixa_classe.php");
require_once  modification("classes/db_fis_autonumpre_classe.php");
require_once  modification("classes/db_arrecad_classe.php");
require_once  modification("classes/db_fis_auto_classe.php");
require_once  modification("classes/db_fis_parfiscal_classe.php");
require_once  modification("dbforms/db_funcoes.php");
include modification ("classes/db_cancdebitos_classe.php");
include modification ("classes/db_cancdebitosprot_classe.php");
include modification ("classes/db_cancdebitosreg_classe.php");
include modification ("classes/db_cancdebitosproc_classe.php");
include modification ("classes/db_cancdebitosprocreg_classe.php");
include modification ("classes/db_cancdebitosprocconcarpeculiar_classe.php");
require modification("classes/db_cancdebitosconcarpeculiar_classe.php");
include modification("classes/db_fis_fandam_classe.php");
include modification("classes/db_fis_autoandam_classe.php");
include modification("classes/db_fis_autoultandam_classe.php");

include modification ("libs/db_sql.php");


$clautotipo              = new cl_fis_autotipo;
$clautotipobaixaproc     = new cl_fis_autotipobaixaproc;
$clautotipobaixaprocproc = new cl_fis_autotipobaixaprocproc;
$clautotipobaixa         = new cl_fis_autotipobaixa;
$clautonumpre            = new cl_fis_autonumpre;
$clarrecad               = new cl_arrecad;
$clparfiscal             = new cl_fis_parfiscal;
$clauto                  = new cl_fis_auto;
$clcancdebitos = new cl_cancdebitos;
$clcancdebitosprot = new cl_cancdebitosprot;
$clcancdebitosreg = new cl_cancdebitosreg;
$clcancdebitosproc = new cl_cancdebitosproc;
$clcancdebitosprocreg = new cl_cancdebitosprocreg;
$clcancdebitosprocconcarpeculiar = new cl_cancdebitosprocconcarpeculiar;
$clcancdebitosconcarpeculiar = new cl_cancdebitosconcarpeculiar;
$clarrecant = new cl_arrecant;
$clautoandam = new cl_fis_autoandam;
$clautoultandam = new cl_fis_autoultandam;
$clfandam        = new cl_fis_fandam;

db_postmemory($HTTP_POST_VARS);

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clautotipobaixaprocproc->rotulo->label();

$db_opcao           = 1;
$db_botao           = true;
$lProcProtBaixaAuto = false;

$iInstit  = db_getsession('DB_instit');

$sSqlParFiscal  = $clparfiscal->sql_query($iInstit,"fis_parfiscal.y32_procprotbaixaauto",null,"");
$rsSqlParFiscal = $clparfiscal->sql_record($sSqlParFiscal);
if ($clparfiscal->numrows > 0) {
	
	$oParFiscal = db_utils::fieldsMemory($rsSqlParFiscal,0);
	if (isset($oParFiscal->y32_procprotbaixaauto) && $oParFiscal->y32_procprotbaixaauto == 1) {
		$lProcProtBaixaAuto = true;
	}
}

if (isset($baixar)) {  
  
  db_inicio_transacao();
  
  $sqlerro      = false;
  
  if($decisao == 0){
    
    /* Verifica se auto foi importado para diversos */
    $sSqlDiver   = " select * from fiscalizacao.fis_autonumpre ";
    $sSqlDiver  .= "    inner join diverimportaold on y17_numpre = dv13_numpre";
    $sSqlDiver  .= "    inner join diversos on dv13_diversos = dv05_coddiver";
    $sSqlDiver  .= "  where y17_codauto =".$y50_codauto;
    
    $ResultDiver = db_query($sSqlDiver);
    if(pg_num_rows($ResultDiver) > 0){
      $sqlerro = true;
      $erro = 'Débitos de auto parcelados – Auto não pode ser Baixado';
    }

     /* Verifica se auto foi importado para divida ativa */
    if($sqlerro == false){ 
      
      $sSqlDiv   = " select * from fiscalizacao.fis_autonumpre ";
      $sSqlDiv  .= "  inner join divold on y17_numpre = k10_numpre";
      $sSqlDiv  .= "  inner join divida on v01_coddiv = k10_coddiv";
      $sSqlDiv  .= "   where y17_codauto =".$y50_codauto;
      
      $ResultDiv = db_query($sSqlDiv);
      if(pg_num_rows($ResultDiv) > 0){
        $sqlerro = true;
        $erro = 'Débitos inscritos em Divida – auto não pode ser Baixado';
      }
    }

  }
  
/*
 * Inicio Melhoria Ticket 102311
 */
  if($sqlerro == false){

    /*  Verifica se existem débitos no arrecad vinculados ao auto de infração  tabela autonumpre*/
   $sSqlAuto  = " select * from (select distinct y17_numpre FROM fiscalizacao.fis_autonumpre WHERE y17_codauto = ".$y50_codauto;
   $sSqlAuto .= " UNION ALL select distinct q05_numpre FROM fiscalizacao.fis_autonumpre ";
   $sSqlAuto .= " LEFT JOIN fiscalizacao.fis_autolevanta ON y17_codauto = y117_auto ";
   $sSqlAuto .= " LEFT JOIN issvarlev ON y117_levanta = q18_codlev ";
   $sSqlAuto .= " LEFT JOIN issvar ON q18_codigo = q05_codigo ";
   $sSqlAuto .= " WHERE y17_codauto = ".$y50_codauto.") as x where x.y17_numpre is not null";


    $ResultAuto = pg_query($sSqlAuto);
    $numlinhas = pg_num_rows($ResultAuto);
    $debitos = array();

    if(pg_num_rows($ResultAuto) > 0){

      for ($i=0;$i<pg_num_rows($ResultAuto);$i++) { 

        db_fieldsmemory($ResultAuto,$i);

        $sSqlArrecad = db_query("SELECT * FROM arrecad where k00_numpre = ".$y17_numpre);
        if(pg_num_rows($sSqlArrecad) > 0){
          $numpar = array();

          for ($iDebitos=0; $iDebitos < pg_num_rows($sSqlArrecad); $iDebitos++) { 
            db_fieldsmemory($sSqlArrecad,$iDebitos);
            $numpar[] = $k00_numpar;
          }
          $debitos[] = array(
            'numpre' => $k00_numpre,
            'numpar' => $numpar
          );
        }
      }

      /*
       * Inicio Cancelamento do Processo
       */
      $k20_descr = "";
      if ($y114_processo != "" && $k20_descr == "") {
        $clcancdebitos->k20_descr = "Auto :".$y50_codauto."  Baixado pelo processo:".$y114_processo;
      } 

      $clcancdebitos->k20_cancdebitostipo = 1;
      $clcancdebitos->k20_hora    = db_hora();
      $clcancdebitos->k20_data    = date("Y-m-d", db_getsession("DB_datausu"));
      $clcancdebitos->k20_usuario = db_getsession("DB_id_usuario");
      $clcancdebitos->k20_instit  = db_getsession("DB_instit");
      $clcancdebitos->incluir(null);
      if ($clcancdebitos->erro_status == 0) {
        $sqlerro = true;
        $erro = $clcancdebitos->erro_msg; 
      }
      $erro_msg = $clcancdebitos->erro_msg;        
      

      if ($sqlerro == false) {
        if ($y114_processo != "") {
          $clcancdebitosprot->k25_codproc     = $y114_processo;
          $clcancdebitosprot->k25_cancdebitos = $clcancdebitos->k20_codigo;
          $clcancdebitosprot->incluir($clcancdebitos->k20_codigo);
          if ($clcancdebitosprot->erro_status == 0) {
            $sqlerro  = true;
            $erro = $clcancdebitosprot->erro_msg;
          }
        }
      }
        
      if ($sqlerro == false) {
        for ($i = 0; $i < count($debitos); $i ++) {
          $numpre2 = $debitos[$i]['numpre'];

          for ($iNumpar=0; $iNumpar < count($debitos[$i]['numpar']); $iNumpar++) { 
            $numpar2 = $debitos[$i]['numpar'][$iNumpar];
          
            $sqlrec = "select distinct k00_receit from arrecad where k00_numpre =$numpre2 and k00_numpar = $numpar2";
            $resultrec= pg_query($sqlrec);
            $linhasrec = pg_num_rows($resultrec);
            if ($resultrec>0) {
              for ($r=0; $r < $linhasrec; $r++) {
                db_fieldsmemory($resultrec,$r);
                if ($sqlerro == false) {
                  $clcancdebitosreg->k21_receit = $k00_receit;
                  $clcancdebitosreg->k21_hora   = db_hora();
                  $clcancdebitosreg->k21_data   = date("Y-m-d", db_getsession("DB_datausu"));
                  $clcancdebitosreg->k21_obs    = "Número do auto:".$y50_codauto." \n Levantamento:".$numpre2."\n";
                  $clcancdebitosreg->k21_codigo = $clcancdebitos->k20_codigo;
                  $clcancdebitosreg->k21_numpre = $numpre2;
                  $clcancdebitosreg->k21_numpar = $numpar2;
                  $clcancdebitosreg->incluir("");
                  if ($clcancdebitosreg->erro_status == 0) {
                    $sqlerro = true;
                    $erro = $clcancdebitosreg->erro_msg;
                    break;
                  }
                }
              }// do for $r
            }
          }
          
        }


      }
      // db_msgbox($erro_msg);

      /*
       * Inicio Processa Cancelamento de Debitos
       */
      if ($sqlerro == false) {  
        $k20_codigo = $clcancdebitos->k20_codigo;
        $k23_obs = $clcancdebitos->k20_descr;
        //declaro a variavel $er pq aum sei o que faz da erro de variaval indefinida 
        $er = "";
        $result = $clcancdebitosreg->sql_record($clcancdebitosreg->sql_query("", "k21_sequencia,k21_codigo,k21_numpre,k21_numpar,k21_receit", "k21_numpre,k21_numpar,k21_receit", "k21_codigo=$k20_codigo"));
        $numrows = $clcancdebitosreg->numrows;
        $clcancdebitosproc->k23_data = date("Y-m-d", db_getsession("DB_datausu"));
        $clcancdebitosproc->k23_hora = date("H:i");
        $clcancdebitosproc->k23_usuario = db_getsession("DB_id_usuario");
        $clcancdebitosproc->k23_obs = "$k23_obs ";
        $clcancdebitosproc->k23_cancdebitostipo = 1;
        $clcancdebitosproc->incluir(null); //$k21_codigo);
        if ($clcancdebitosproc->erro_status == "0") {
          $erro = "Operação não Efetuada - $er".$clcancdebitosproc->erro_msg;
          $sqlerro = true;
        }else{
          $codigo_proc=$clcancdebitosproc->k23_codigo;
        }

        if ($sqlerro == false) {
          for ($x = 0; $x < $numrows; $x ++) {
            db_fieldsmemory($result, $x);
            $result_arrecad=$clarrecad->sql_record($clarrecad->sql_query_file_instit(null,"*",null,"arrecad.k00_numpre=$k21_numpre and k00_numpar=$k21_numpar " . ($k21_receit == 0?"":" and k00_receit = $k21_receit and k00_instit = ".db_getsession('DB_instit') )));
            if ($clarrecad->numrows==0){
              continue;
            }
            
            $data = db_getsession("DB_datausu");
            $result_deb = debitos_numpre($k21_numpre, 0, 0, $data, db_getsession("DB_anousu"), $k21_numpar);
            if (gettype($result_deb) == "boolean") {
              $sqlerro = true;
            } else {
              db_fieldsmemory($result_deb, 0);
            }
            if ($sqlerro==false){
              $clarrecant->incluir_arrecant($k21_numpre, $k21_numpar, $k21_receit, true);
              if ($clarrecant->erro_status == "0") {
                $sqlerro = true;
                $erro = "Operação não Efetuada - $er".$clarrecant->erro_msg;
              }
            }
       
            if ($sqlerro==false){
              $clcancdebitosprocreg->k24_codigo         = @$codigo_proc;
              $clcancdebitosprocreg->k24_cancdebitosreg = $k21_sequencia;
              $clcancdebitosprocreg->k24_vlrhis         = $vlrhis;
              $clcancdebitosprocreg->k24_vlrcor         = $vlrcor;
              $clcancdebitosprocreg->k24_juros          = $vlrjuros;
              $clcancdebitosprocreg->k24_multa          = $vlrmulta;
              $clcancdebitosprocreg->k24_desconto       = $vlrdesconto;
              $clcancdebitosprocreg->incluir(null);
              if ($clcancdebitosprocreg->erro_status == "0") {
                $sqlerro = true;        
                $erro = "Operação não Efetuada - $er".$clcancdebitosprocreg->erro_msg;
              }
            }
          }
        }

        if ($sqlerro == true) {
          $erro = "Operação não Efetuada - ".@$erro_msg."\n";
        } else {
          $erro_msg .= " Cancelamento Efetuado -".@$codigo_proc."\n";
        }
      }  

             
      /*
      * Fim Cancelamento de Debitos
      */   

    }
          
  }

  /*
   * Inicio Gera Andamento.
   */
  if ($sqlerro == false) {
    

    $y39_data_dia = date("d",db_getsession("DB_datausu"));
    $y39_data_mes = date("m",db_getsession("DB_datausu"));
    $y39_data_ano = date("Y",db_getsession("DB_datausu"));

    $clfandam->y39_data       = $y39_data_ano.'-'.$y39_data_mes.'-'.$y39_data_dia;
    $clfandam->y39_codtipo    = 99;
    $clfandam->y39_id_usuario = db_getsession('DB_id_usuario');
    $clfandam->y39_hora       = db_hora();
    $clfandam->incluir($y39_codandam);
    if($clfandam->erro_status==0){
      $sqlerro = true;
      $erro = $clfandam->erro_msg."\n";
    }

    $clautoultandam->y16_codnoti=$y50_codauto;
    $clautoultandam->excluir($y50_codauto);
    $clautoultandam->incluir($y50_codauto,$clfandam->y39_codandam);
    $clautoandam->incluir($y50_codauto,$clfandam->y39_codandam);
    if($clautoultandam->erro_status==0){
      $erro = $clautoultandam->erro_msg."\n";
      $sqlerro = true;
    }

  }
  /*
   * Fim Gera Andamento.
   */  

/*
 * Fim Melhoria Ticket 102311
 */
  if ($sqlerro == false) {    

    $dtbaixa      = $q07_databx_ano.'-'.$q07_databx_mes.'-'.$q07_databx_dia;
    $data         = date('Y-m-d',db_getsession('DB_datausu'));
    $usu          = db_getsession('DB_id_usuario');
    
    $sWhereAutoTipo  = "y87_dtbaixa   = '{$dtbaixa}'    and y87_data    = '{$data}' and y87_usuario = {$usu} and ";
    $sWhereAutoTipo .= "y59_codauto = {$y50_codauto}                                                             ";
    
    if (isset($y114_processo) && !empty($y114_processo)) {
      $sWhereAutoTipo .= " and fis_autotipobaixaprocproc.y114_processo = {$y114_processo} ";  	
    }
    
    $sSqlAutoTipo    = $clautotipo->sql_query_baixa(null,"*",null,$sWhereAutoTipo);
    $result_baixa    = $clautotipo->sql_record($sSqlAutoTipo);
    
    if ($clautotipo->numrows == 0) {
    	
      $clautotipobaixaproc->y87_dtbaixa  = $dtbaixa;
      $clautotipobaixaproc->y87_data     = date('Y-m-d',db_getsession('DB_datausu'));
      $clautotipobaixaproc->y87_hora     = db_hora();
      $clautotipobaixaproc->y87_usuario  = db_getsession('DB_id_usuario');
      $clautotipobaixaproc->incluir(null);
      $erro .= $clautotipobaixaproc->erro_msg;
      if ($clautotipobaixaproc->erro_status == 0) {
         $sqlerro=true;
      }
      
      $codigo = $clautotipobaixaproc->y87_baixaproc;
    } else {
    	
      db_fieldsmemory($result_baixa,0);
      $codigo   = $y87_baixaproc;
      $erro   .= 'Inclusão efetuada com Sucesso!!';
    }
    
    if ($sqlerro == false) {
    	
  	  if (isset($lProcProtBaixaAuto) && $lProcProtBaixaAuto == true || isset($y114_processo) && !empty($y114_processo)) {
  	    
  	    $clautotipobaixaprocproc->y114_baixaproc = $clautotipobaixaproc->y87_baixaproc;
  	    $clautotipobaixaprocproc->y114_processo  = $y114_processo;
  	    $clautotipobaixaprocproc->incluir(null);
  	    if ($clautotipobaixaprocproc->erro_status == 0) {
  	    	
  	       $sqlerro  = true;
  	       $erro    .= $clautotipobaixaprocproc->erro_msg;
  	    }
  	  }
    }
    
    if ($sqlerro == false) {
    	
      $cods = explode('#',$chaves);
      for($x = 0; $x < count($cods); $x++) {
      	
        if ($sqlerro == false) {
        	
  	      $clautotipobaixa->y86_codbaixaproc = $codigo;
  	      $clautotipobaixa->incluir($cods[$x]);
  	      if ($clautotipobaixa->erro_status == 0) {
  	      	
  	        $sqlerro = true;
  	        $erro    .= $clautotipobaixa->erro_msg;
  	      }
        }
      }
      
      $sWhereAutoNumpre = "y17_codauto = {$y50_codauto}";
      $result_numpre    = $clautonumpre->sql_record($clautonumpre->sql_query_file(null,"*",null,$sWhereAutoNumpre));
      if ($clautonumpre->numrows > 0) {
      	
      	$numrows_numpre = $clautonumpre->numrows;
      	// for($w = 0; $w < $numrows; $w++) {
      		
      	// 	db_fieldsmemory($result_numpre,$w);
      	// 	$clarrecad->excluir(null,"k00_numpre = {$y17_numpre}");
      	// 	if ($clarrecad->erro_status == 0) {
      			
      	// 		$sqlerro=true;
      	// 		$erro .= $clarrecad->erro_msg;
      	// 		break;
      	// 	}
      	// }
      	
      	if ($sqlerro == false) {
      		
      		$clautonumpre->excluir(null,"y17_codauto = {$y50_codauto}");
      		if ($clautonumpre->erro_status == 0) {
      			
  	  			$sqlerro=true;
  	  			$erro .= $clautonumpre->erro_msg;
  			  }
      	}
        
      	$sWhereAutoTipo  = "y59_codauto = {$y50_codauto} and y86_codautotipo is null";
      	$sSqlAutoTipo    = $clautotipo->sql_query_baixa(null,"*",null,$sWhereAutoTipo);
      	$result_baixadas = $clautotipo->sql_record($sSqlAutoTipo);
  	    if ($clautotipo->numrows > 0) {
  	    	
      	  $result_calc = $clauto->sql_calculo($y50_codauto);
          db_fieldsmemory($result_calc,0);
          $info = $fc_autodeinfracao;	
      	}
      }
      
    }
  }
  db_fim_transacao($sqlerro);
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  >
<table align="center" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td align="left" valign="top" bgcolor="#CCCCCC"> 
			<?php 
			  include modification("forms/db_frm_fis_autobaixaproc.php");
			?>
	</td>
  </tr>
</table>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
<?php 
if (isset($baixar)) {
	
  if ($sqlerro == true) { 
  	
    db_msgbox($erro);
    if ($clautotipobaixaproc->erro_campo != "") {
    	
      echo "<script> document.form1.".$clautotipobaixaproc->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clautotipobaixaproc->erro_campo.".focus();</script>";
    } 
  } else {
  	 
    if (isset($erro) && !empty($erro)) {
      db_msgbox($erro); 
    }
  }
}
?>
</html>