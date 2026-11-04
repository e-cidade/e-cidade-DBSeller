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

/**
 * 
 * @author Iuri Guntchnigg
 * @revision $Author: dbjeferson.belmiro $
 * @version $Revision: 1.4 $
 */
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_app.utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
$oPost     = db_utils::postMemory($_POST);
$sFileName = "";
$sPcpasp   = '';
$sUrl      = 'con4_importarplanocontatce002.php';
if (USE_PCASP) {

  $sPcpasp = ' do PCASP';
  $sUrl    = 'con4_importarplanocontapcasp002.php';
}
if (isset($oPost->btnEnviar)) {
  
  move_uploaded_file($_FILES["arquivotce"]["tmp_name"], "tmp/{$_FILES["arquivotce"]["name"]}");
  $sFileName  = $_FILES["arquivotce"]["name"];
}
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
     db_app::load("scripts.js, prototype.js");
     db_app::load("estilos.css");
    ?>
  </head>
  <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table >
      <tr height="25">
        <td>
          &nbsp;
        </td>
      </tr>
     </table>
     <center>
       <form name='form1' method="post" enctype="multipart/form-data">
       <table>
         <tr>
           <td>
             <fieldset>
               <legend>
                 <b>
                   Importar Plano de Contas <?=$sPcpasp?>
                 </b>
                </legend>
                <table>
                  <tr>  
                    <td>
                      <b>Informe o Arquivo:</b>
                    </td>
                    <td>
                      <input type="file" name='arquivotce' id='arquivotce'>
                    </td>
                  </tr>
                </table>
             </fieldset>
           </td>
         </tr>
         <tr>
           <td colspan="2" style="text-align: center">
             <input type="submit" name='btnEnviar' value='Importar Arquivo'>
           </td>
         </tr>
       </table>
       </form>
     </center>
  </body>
</html>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<script>
  var sFileName  = '<?=$sFileName?>';
  
  if (sFileName  != "") {
  
    js_OpenJanelaIframe('','iframe_importar',
                        '<?=$sUrl?>?sFileName='+sFileName
                        );
  
  }
</script>