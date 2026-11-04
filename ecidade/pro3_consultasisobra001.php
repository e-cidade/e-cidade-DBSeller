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
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#cccccc">
<div class="container">
  <fieldset>
        <legend>Consulta Documento Enviado via Sisobra</legend>
        <table class="form-container">
        <tr>
            <td>Tipo de Documento:</td>
            <td>
                <?php
                    $aTipoDocumento = [];
                    $aTipoDocumento = array(
                        1=>'Alvará',
                        2=>'Habite-se'
                    );
                    db_select("tipoDocumento", $aTipoDocumento, true, 1);
                ?>
            </td>      
        </tr>
        <tr>
            <td>Número do Documento:</td>
            <td>
                <?php db_input("numeroDocumento", 10, false, true, 'text', 1);?>
            </td>      
        </tr>
        <tr>
            <td>Ano do Documento:</td>
            <td>
                <?php db_input("anoDocumento", 10, false, true, 'text', 1);?>
            </td>      
        </tr>
        </table>
    </fieldset>
    <input type="button" onclick="js_pesquisa();" value="Pesquisar">
 </div>
 <?php
   db_menu( db_getsession("DB_id_usuario"),
            db_getsession("DB_modulo"),
            db_getsession("DB_anousu"),
            db_getsession("DB_instit") );
 ?>
</body>
<script>

function js_pesquisa() {
  var sQueryString = "tipoDocumento="    + $F('tipoDocumento');
		  sQueryString+= "&numeroDocumento=" + $F('numeroDocumento');
		  sQueryString+= "&anoDocumento="    + $F('anoDocumento');
      
  js_OpenJanelaIframe('',
                      'db_iframe_consultasisobra',
                      'pro3_consultasisobra002.php?' + sQueryString,
                      'Consulta Documento Sisobra',
                      true);
}

</script>
</html>
<script>

$("ob01_codobra").addClassName("field-size2");
$("j01_matric").addClassName("field-size2");
$("ob03_numcgm").addClassName("field-size2");
$("z01_nome").addClassName("field-size7");

</script>