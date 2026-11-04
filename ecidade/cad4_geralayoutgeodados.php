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

require_once(modification("fpdf151/scpdf.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_iptucalc_classe.php"));
require_once(modification("classes/db_iptunump_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_massamat_classe.php"));
require_once(modification("classes/db_iptuender_classe.php"));
require_once(modification("fpdf151/impcarne.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_db_config_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

$cliptucalc  = new cl_iptucalc;
$cliptuender = new cl_iptuender;
$cliptunump  = new cl_iptunump;
$clmassamat  = new cl_massamat;


db_postmemory($HTTP_POST_VARS);

?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default" >
  <div class="container">
    <form name="form1" action="" method="post" >

      <fieldset>
        <legend>Levantamento Cadastral</legend>
        <table>
          <tr>
            <td nowrap="nowrap">
              <label class="bold">Formato:</label>
            </td>
            <td nowrap="nowrap">
                <?php
                  
                  $aOpcoes = array (
                                    //"1" => "Geodados"  ,
                                    "2" => "Versão 2"    , 
                                    "3" => "Lista Pontos"
                                   );
                  db_select('formato', $aOpcoes, true, 1);
                ?>
            </td>
          </tr>

          <tr id="container-separador">
            <td nowrap="nowrap" >
              <label class="bold">Separador:</label>
            </td>

            <td>
              <?php
                $aOpcoes = array(
                    ';' => ';',
                    '|' => '|'
                  );

                db_select('separador', $aOpcoes, true, 1);
              ?>
            </td>
          </tr>
          
        </table>
      </fieldset>
      <input name="geracarnes"  type="button" id="geracarnes" value="Gera Arquivo" onclick="js_geraDados();">
    </form>
  </div>
  <?php
    db_menu( db_getsession("DB_id_usuario"), 
             db_getsession("DB_modulo"), 
             db_getsession("DB_anousu"), 
             db_getsession("DB_instit") );
  ?>

  <script>

    if ($F('formato') != 2) {
      $('container-separador').hide()
    }

    var sUrlRPC = 'cad4_geralayoutgeodados.RPC.php';  
    var oParam  = new Object();


    function js_geraDados() {

      var oParametros      = new Object();
      
      oParametros.exec       = 'geraDados';  
      oParametros.iFormato   = $F('formato');   
      oParametros.sSeparador = $F('separador');
      
      js_divCarregando("Processando Dados. \n Aguarde ...", 'msgBox');
       
       var oAjaxLista  = new Ajax.Request(sUrlRPC, {
                                                      method: "post",
                                                      parameters:'json='+Object.toJSON(oParametros),
                                                      onComplete: js_retornoGeraDados
                                                    });   
    }

    function js_retornoGeraDados(oAjax) {
      
      js_removeObj('msgBox');
      var oRetorno = JSON.parse(oAjax.responseText);

      
      if (oRetorno.iStatus == 1) {

        var listagem  = oRetorno.sNomeArquivo + "# Download do Arquivo " + oRetorno.sNomeArquivo;

        js_montarlista(listagem,'form1'); 
        
      } else {

        alert(oRetorno.sMessage.url_decode());

      }
    }

    function js_mostra_processando(){
      
      document.form1.processando.style.visibility='visible';
    }

    function termo(qual, total, sql){
      
      if (sql==0) {
        document.getElementById('termometro').innerHTML='processando registro... '+qual+' de '+total;
      } else {
        document.getElementById('termometro').innerHTML='processando select...';
      }
    }

    $('formato').observe('change', function() {
      
      $('container-separador').show()

      if (this.value != 2) {
        $('container-separador').hide()
      }
    })
      
  </script>
</body>
</html>