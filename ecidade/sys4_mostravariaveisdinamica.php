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
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("libs/db_libsys.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("libs/JSON.php"));
include(modification("dbagata/classes/core/AgataAPI.class"));
include(modification("classes/db_db_relatorio_classe.php"));
include(modification("model/dbColunaRelatorio.php"));
include(modification("model/dbFiltroRelatorio.php"));
include(modification("model/dbVariaveisRelatorio.php"));
include(modification("model/dbGeradorRelatorio.model.php"));
include(modification("model/dbOrdemRelatorio.model.php"));
include(modification("model/dbPropriedadeRelatorio.php"));

if ( isset($_SESSION['objetoXML'])) {
  
	$oXML = unserialize($_SESSION['objetoXML']);
  
  $oPropriedades  = $oXML->getPropriedades();
  $sNomeRelatorio = "";
  if ($oPropriedades) {
    $sNomeRelatorio = $oPropriedades->getNome();
  }

  
	$sHtml  = " <html> ";
	$sHtml .= " <head> ";
	$sHtml .= " <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title> ";
	$sHtml .= " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"> ";
	$sHtml .= " <meta http-equiv=\"Expires\" CONTENT=\"0\"> ";
	$sHtml .= " <script language=\"JavaScript\" type=\"text/javascript\" src=\"scripts/scripts.js\"></script> ";
	$sHtml .= " <script language=\"JavaScript\" type=\"text/javascript\" src=\"scripts/geradorrelatorios.js\"></script> ";
	$sHtml .= " <script language=\"JavaScript\" type=\"text/javascript\" src=\"scripts/prototype.js\"></script> ";
	$sHtml .= " <script language=\"JavaScript\" type=\"text/javascript\" src=\"scripts/strings.js\"></script> ";
	$sHtml .= " <link href=\"estilos.css\" rel=\"stylesheet\" type=\"text/css\"> ";
	$sHtml .= " </head> ";
	$sHtml .= " <body bgcolor=#CCCCCC leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onLoad=\"a=1\" > ";
	$sHtml .= " <table style='padding-top:20px;' width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> ";
	$sHtml .= "   <tr> ";
	$sHtml .= "     <td height=\"100%\" align=\"center\" valign=\"top\" bgcolor=\"#CCCCCC\"> ";
	$sHtml .= "     <center> ";
	$sHtml .= "       <form name=\"frmFiltros\" method=\"post\" action=\"\"> ";
	$sHtml .= "       <center> ";
	$sHtml .= "       <table border=0 align=\"center\"> ";
	$sHtml .= "   <tr> ";
	$sHtml .= "   <td> ";
	$sHtml .= "   <fieldset> ";
	$sHtml .= "       <legend><b>{$sNomeRelatorio}</b></legend> ";
	$sHtml .= "   <div> ";
	$sHtml .= "   <table width=100% border=0> ";
	
  $aVariaveis = $oXML->getVariaveis();
  
	foreach ($aVariaveis as $sNomeVariavel => $oVariavel) {

		if ( $oVariavel->getLabel() != '' ) {
			$sNomeVar = $oVariavel->getLabel(); 
		} else {
			$sNomeVar = $sNomeVariavel;
		}
   
	  $sHtml .= "  <tr> ";
	  $sHtml .= "     <td title=\"{$sNomeVar}\" align=\"left\"> ";
	  $sHtml .= "       <b> {$sNomeVar} : </b> ";
	  $sHtml .= "     </td> ";
	  $sHtml .= "     <td> ";
	
	  ob_start();
	  
	  switch ($oVariavel->getTipoDado()) {
	
	    case 'date':
	      db_inputdata(str_replace('$','',$oVariavel->getNome()),"","","",true,'text',1,"");
	      break;
	    case 'int4':
	      db_input(str_replace('$','',$oVariavel->getNome()),10,"1",true,"text",1,"");
	      break;
	    case 'float8':
	      db_input(str_replace('$','',$oVariavel->getNome()),10,"4",true,"text",1,"");
	      break;
	    case 'varchar':
	      db_input(str_replace('$','',$oVariavel->getNome()),50,"0",true,"text",1,"");
	      break;
	    case 'bool':
	      $aTipos = array("t" => "Sim",
	                      "f" => "Não" );
	      db_select(str_replace('$','',$oVariavel->getNome()),$aTipos,true,1,"");
	      break;
	    case 'select':
 //             if( $oVariavel->getSql() != '' ){ 
                  if( substr( strtolower(trim($oVariavel->getSql())) ,0,6)  == "select" ){
                     $res_sql = pg_query($oVariavel->getSql());
                     db_selectrecord(str_replace('$','',$oVariavel->getNome()), $res_sql, true, 2);
                  }
   //           }
              break;

	  }
	
	  $sHtml .= ob_get_contents();
	  
	  ob_end_clean();
	
	  $sHtml .= "     </td> ";
	  $sHtml .= "   </tr> ";
	
	}


        $sHtml .= "   <tr> ";
        $sHtml .= "     <td><b> Nova Saída:</b>";
        $sHtml .= "   </td> ";
        $sHtml .= "     <td> ";


        $nova_saida[1] = 'pdf';
        $nova_saida[2] = 'csv';
        $nova_saida[3] = 'txt';
        $nova_saida[4] = 'html';
        $sHtml .= " <select name='nova_saida'>
                    <option value='pdf' ".($oPropriedades->getTipoSaida()==$nova_saida[1]?' selected ':'')."'>".strtoupper($nova_saida[1])."</option>
                    <option value='csv' ".($oPropriedades->getTipoSaida()==$nova_saida[2]?' selected ':'')."'>".strtoupper($nova_saida[2])."</option>
                    <option value='txt' ".($oPropriedades->getTipoSaida()==$nova_saida[3]?' selected ':'')."'>".strtoupper($nova_saida[3])."</option>
                    <option value='html' ".($oPropriedades->getTipoSaida()==$nova_saida[4]?' selected ':'')."'>".strtoupper($nova_saida[4])."</option>
                    </select>";

        $sHtml .= "     </td> ";
        $sHtml .= "   </tr> ";




	$sHtml .= "         </table> ";
	$sHtml .= "        </fieldset> ";
	$sHtml .= "         </td> ";
	$sHtml .= "         </tr> ";
	$sHtml .= "         </table> ";
	$sHtml .= "         <input name=\"processar\" type=\"button\" id=\"processar\" value=\"Processar\" onclick=\"js_processar();\" >";
	$sHtml .= "         </center> ";
	$sHtml .= "       </form> ";
	$sHtml .= "     </center> ";
	$sHtml .= "   </td> ";
	$sHtml .= "   </tr> ";
	$sHtml .= " </table> ";
	$sHtml .= " <script> ";
	$sHtml .= "  function js_processar(){  ";
	$sHtml .= "    var aParametros   = new Array(); ";
	$sHtml .= "    var oFormElements = document.frmFiltros; ";
	$sHtml .= "    for(var i=0; i < oFormElements.length; i++) { ";
	$sHtml .= "      var obj = oFormElements.elements[i]; ";
	$sHtml .= "      if ( obj.type == 'text' || obj.type == 'select-one' ) { ";
	$sHtml .= "         var sVariavel   = obj.value ; ";
	$sHtml .= "         var sDescricao  = '$'+obj.name; ";
	$sHtml .= "         var objVariavel = new js_criaObjetoVariavel(sDescricao,sVariavel);";
	$sHtml .= "         aParametros.push( objVariavel ); ";
	$sHtml .= "      } ";
	$sHtml .= "    } ";
	$sHtml .= "    var sQuery = 'variaveis='+Object.toJSON(aParametros)+'&nova_saida='+document.frmFiltros.nova_saida.value; ";
	$sHtml .= "    jan = window.open('sys4_imprimerelatorio001.php?'+sQuery,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');";
  $sHtml .= "    jan.moveTo(0,0); ";
	$sHtml .= "  } ";
	$sHtml .= " </script> ";
	$sHtml .= " </body> ";
	$sHtml .= " </html> ";
	
	echo $sHtml;
}
