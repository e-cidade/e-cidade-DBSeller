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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

$clDbDepart = new cl_db_depart();
$clIframeSeleciona = new cl_iframe_seleciona;

$sWhere = " 1 != 1 ";

if (isset($_GET["departamentos"])) {
    $departamentos = $_GET["departamentos"];

    $sWhere = " coddepto IN ({$departamentos}) ";
}

$db_opcao = 1;

$clIframeSeleciona->chaves        = "coddepto";
$clIframeSeleciona->campos        = "coddepto, descrdepto";
$clIframeSeleciona->legenda       = "Departamentos";
$clIframeSeleciona->sql           = $clDbDepart->sql_query_file(null, "coddepto, descrdepto", "coddepto", "instit = ".db_getsession("DB_instit"));
$clIframeSeleciona->sql_marca     = $clDbDepart->sql_query_file(null, "coddepto, descrdepto", "descrdepto", $sWhere);
$clIframeSeleciona->iframe_height ="600";
$clIframeSeleciona->iframe_width  ="600";
$clIframeSeleciona->tamfontecabec ="13";
$clIframeSeleciona->tamfontecorpo ="12";
$clIframeSeleciona->iframe_nome   ="ifr_departamentos";
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
    <body>
        <div class="container">
            <form name="form1" method="post">
                <table id="table" class="form-container">
                    <?php $clIframeSeleciona->iframe_seleciona($db_opcao); ?>
                </table>
                <input name="salvar" id="salvar" type="button" onclick="js_salvarDepartamentos();" value="Salvar">
            </form>
        </div>
        <?php db_menu(); ?>
    </body>
</html>
<script>
    var departamentos = "";
    
    function js_inserirDepartamentos()
    {
        var iframeLista = document.getElementById("ativ");
        var elemento = iframeLista.contentWindow.document.querySelectorAll('input[type="checkbox"]:checked');

        elemento.forEach(function(elemento){
            if (departamentos == "") {               
                departamentos = elemento.value;
            } else {
                departamentos = departamentos+","+elemento.value;
            }
        });
    }

    function js_salvarDepartamentos()
    {
        js_inserirDepartamentos();

        parent.departamentos.value = departamentos;
        parent.js_ocultaDepartamento();
    }
</script>
