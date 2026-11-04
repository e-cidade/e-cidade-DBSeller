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
include_once(modification("libs/db_sessoes.php"));
include_once(modification("libs/db_usuariosonline.php"));
include_once(modification("classes/db_cgmerrado_classe.php"));
include_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
db_postmemory($_SERVER);
$clcgmerrado = new cl_cgmerrado;
$db_opcao = 3;
$db_botao = false;

if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    db_inicio_transacao();
    $clcgmerrado->incluir($z11_codigo, $z11_numcgm);
    db_fim_transacao();
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

<body background-color=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
    <div class="container">
        <table width="790" cellspacing="0" cellpadding="0">
            <tr>
                <td height="430" valign="top" background-color="#CCCCCC">
                    <?php
                    include_once(modification("forms/db_frmcgmerrado.php"));
                    ?>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    if ($clcgmerrado->erro_status == "0") {
        $clcgmerrado->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        echo "<script> document.form1.db_opcao.value='Incluir';</script>  ";
        if ($clcgmerrado->erro_campo != "") {
            echo "<script> document.form1." . $clcgmerrado->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clcgmerrado->erro_campo . ".focus();</script>";
        };
    } else {
        $clcgmerrado->erro(true, false);
        echo "
         <script>
         function js_src(){
           parent.iframe_cgmerrado.location.href ='pro1_cgmerrado001.php?z11_codigo=$z11_codigo&abas=1';\n
         }
         js_src();
         </script>
       ";
    };
};
?>
