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
  require_once(modification("dbforms/db_funcoes.php"));
  require_once(modification("classes/db_aguabase_classe.php"));
  require_once(modification("classes/db_aguabaseresp_classe.php"));
  require_once(modification("classes/db_aguabasecorresp_classe.php"));
  require_once(modification("classes/db_aguabasecar_classe.php"));
  require_once(modification("classes/db_aguaconstr_classe.php"));
  require_once(modification("classes/db_aguabasevenc_classe.php"));
  require_once(modification("classes/db_histocorrencia_classe.php"));
  require_once(modification("classes/db_histocorrenciamatric_classe.php"));

  $claguabase             = new cl_aguabase;
  $claguabasebaixa        = new cl_aguabasebaixa;
  $claguacalc             = new cl_aguacalc;
  $claguacalcval          = new cl_aguacalcval;
  $claguabasecar          = new cl_aguabasecar;  
  $claguabaseresp         = new cl_aguabaseresp;
  $claguabasecorresp      = new cl_aguabasecorresp;
  $claguaconstr           = new cl_aguaconstr;
  $claguabasevenc         = new cl_aguabasevenc;
  $clhistocorrencia       = new cl_histocorrencia;
  $clhistocorrenciainscr  = new cl_histocorrenciainscr;
  $clhistocorrenciacgm    = new cl_histocorrenciacgm;
  $clhistocorrenciamatric = new cl_histocorrenciamatric;

  db_postmemory($_POST);
  
  $db_opcao = 33;
  $db_botao = false;

  if (isset($excluir)) {
    
    $sqlerro = false;
  
    db_inicio_transacao();
  
    $claguabaseresp->x14_matric = $x01_matric;
    
    $claguabaseresp->excluir($x01_matric);

    if ($claguabaseresp->erro_status == 0) {
      $sqlerro = true;
    } 
    
    $erro_msg = $claguabaseresp->erro_msg; 
    $claguabasecorresp->x32_matric = $x01_matric;
    
    $claguabasecorresp->excluir($x01_matric);

    if ($claguabasecorresp->erro_status == 0) {
      $sqlerro = true;
    }
     
    $erro_msg = $claguabasecorresp->erro_msg; 

    $claguabasecar->excluir($x01_matric);

    if ($claguabasecar->erro_status == 0) {
      $sqlerro=true;
    } 
    
    $erro_msg = $claguabasecar->erro_msg; 

    $claguaconstr->x11_codconstr = $x01_matric;
    
    $claguaconstr->excluir($x01_matric);

    if ($claguaconstr->erro_status == 0) {
      $sqlerro=true;
    }
     
    $erro_msg = $claguaconstr->erro_msg; 
    $claguabasevenc->x27_matric = $x01_matric;
    
    $claguabasevenc->excluir($x01_matric);

    if ($claguabasevenc->erro_status == 0) {
      $sqlerro = true;
    } 
    
    $erro_msg = $claguabasevenc->erro_msg; 

    $claguabasebaixa->excluir($x01_matric);

    $sqlCalculosAgua = $claguacalc->sql_query_file(null, 'x22_codcalc', null, "x22_matric = $x01_matric");
    $rsCalculosAgua = db_query($sqlCalculosAgua);
    $existeCalculoAgua = (pg_num_rows($rsCalculosAgua)) > 0;

    if ($existeCalculoAgua){
      $calculosAgua = db_utils::getCollectionByRecord($rsCalculosAgua);

      foreach ($calculosAgua as $calculoAgua){
        $claguacalcval->excluir($calculoAgua->x22_codcalc);
      }

      $claguacalc->excluir(null, "x22_matric = $x01_matric");
    }
    
    $claguabase->excluir($x01_matric);
    
    if ($claguabase->erro_status == 0) {
      $sqlerro = true;
    } 
    
    $erro_msg = $claguabase->erro_msg; 
  
    //exclusao das ocorrencias 
    $result  = $clhistocorrenciamatric->sql_record($clhistocorrenciamatric->sql_query("", "ar25_histocorrencia", "ar25_histocorrencia", "ar25_matric = $x01_matric"));
    $numrows = pg_num_rows($result);
    
    if ($numrows > 0) {
      $ar23_sequenciais_excluir = [];

      for($i = 0; $i < $numrows; $i++) {
        
        db_fieldsmemory($result, $i);
        
        $ar23_sequenciais_excluir[] = $ar25_histocorrencia;
      }
      
      $clhistocorrenciamatric->excluir("", "ar25_matric = $x01_matric");

      foreach ($ar23_sequenciais_excluir as $ar23_sequencial_excluir) {

        $sqlOcorrenciasInscricao = $clhistocorrenciainscr->sql_query("", "ar26_sequencial", null, "ar26_histocorrencia = $ar23_sequencial_excluir");
        $rsOcorrenciasInscricao = $clhistocorrenciainscr->sql_record($sqlOcorrenciasInscricao);
        $existemOcorrenciasParaInscricao = (pg_num_rows($rsOcorrenciasInscricao)) > 0;

        if($existemOcorrenciasParaInscricao){
          $ocorrenciasInscricao = db_utils::getCollectionByRecord($rsOcorrenciasInscricao);

          foreach($ocorrenciasInscricao as $ocorrenciaInscricao){
            $clhistocorrenciainscr->excluir($ocorrenciaInscricao->ar26_sequencial);
          }
        }
        
        $sqlOcorrenciasCgm = $clhistocorrenciacgm->sql_query("", "ar24_sequencial", null, "ar24_histocorrencia = $ar23_sequencial_excluir");
        $rsOcorrenciasCgm = $clhistocorrenciacgm->sql_record($sqlOcorrenciasCgm);
        $existemOcorrenciasParaCgm = (pg_num_rows($rsOcorrenciasCgm)) > 0;

        if($existemOcorrenciasParaCgm){
          $ocorrenciasCgm = db_utils::getCollectionByRecord($rsOcorrenciasCgm);

          foreach($ocorrenciasCgm as $ocorrenciaCgm){
            $clhistocorrenciacgm->excluir($ocorrenciaCgm->ar24_sequencial);
          }
        }
        
        $clhistocorrencia->excluir("", "ar23_sequencial IN ($ar23_sequencial_excluir)");
        
        $erro_msg = $clhistocorrencia->erro_msg;

        if ($clhistocorrencia->erro_status == 0) {
          $sqlerro = true;
          break;
        } 
      }
    }
  
    db_fim_transacao($sqlerro);
  
    $db_opcao = 3;
    $db_botao = true;
  
  } else if(isset($chavepesquisa)) {
    $db_opcao = 3;
    $db_botao = true;
    $result = $claguabase->sql_record($claguabase->sql_query($chavepesquisa)); 

    db_fieldsmemory($result,0);

    // Busca Caracteristicas 
    $result = $claguabasecar->sql_record($claguabasecar->sql_query($x01_matric));
    $caracteristica = null;
    $car = "X";
   
    for ($i = 0; $i < $claguabasecar->numrows; $i++) {
      db_fieldsmemory($result, $i);
      $caracteristica .= $car.$x30_codigo ;
      $car = "X";
    }
    
    $caracteristica .= $car;
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
    <center>
      <table width="790" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
            <center>
  	          <?php
	              include(modification("forms/db_frmaguabase.php"));
	            ?>
            </center>
	        </td>
        </tr>
      </table>
    </center>
  </body>
</html>

<?php
  if (isset($excluir)) {
  
    if ($sqlerro == true) {
      
      db_msgbox($erro_msg);
      
      if ($claguabase->erro_campo != "") {
        echo "<script> document.form1." . $claguabase->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1." . $claguabase->erro_campo . ".focus();</script>";
      }
  
    } else {
      
      db_msgbox($erro_msg);
      echo "
            <script>
              function js_db_tranca(){
                parent.location.href='agu1_aguabase003.php';
              }\n
              js_db_tranca();
            </script>\n
           ";
    }
  }
  
  if (isset($chavepesquisa)) {
    echo "
      <script>
        function js_db_libera(){
           parent.document.formaba.aguabaseresp.disabled=false;
           (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguabaseresp.location.href='agu1_aguabaseresp001.php?db_opcaoal=33&x14_matric=" . @$x01_matric . "';
           parent.document.formaba.aguabasecorresp.disabled=false;
           (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguabasecorresp.location.href='agu1_aguabasecorresp001.php?db_opcaoal=33&x32_matric=" . @$x01_matric . "';
           //parent.document.formaba.aguabasecar.disabled=false;
           //(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguabasecar.location.href='agu1_aguabasecar001.php?db_opcaoal=33&x30_matric=" . @$x01_matric . "';
           parent.document.formaba.aguaconstr.disabled=false;
           //(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguaconstr.location.href='agu1_aguaconstr001.php?db_opcaoal=33&x11_codconstr=" . @$x01_matric . "';
           (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguaconstr.location.href='agu1_aguaconstr001.php?db_opcaoal=33&x11_matric=" . @$x01_matric . "';
           parent.document.formaba.aguabasevenc.disabled=false;
           (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_aguabasevenc.location.href='agu1_aguabasevenc001.php?db_opcaoal=33&x27_matric=" . @$x01_matric . "';
           parent.document.formaba.histocorrencia.disabled=false;
           (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_histocorrencia.location.href='agu1_histocorrencia001.php?db_opcaoal=33&ar25_matric=" . @$x01_matric . "';
         ";
         if (isset($liberaaba)) {
           echo "  parent.mo_camada('aguabaseresp');";
         }
         echo "}\n
               js_db_libera();
               </script>\n
              ";
  }
  
  if ($db_opcao == 22 || $db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
  }
  
?>