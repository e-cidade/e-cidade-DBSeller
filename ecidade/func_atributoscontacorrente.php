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
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
        <td height="63" align="center" valign="top">
            <table width="35%" border="0" align="center" cellspacing="0">
                <form name="form2" method="post" action="" >
                    <input name="Fechar" type="hidden" id="fechar" value="Fechar" onClick="parent.db_iframe_conlancamlan.hide();">
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php
                $infoComplementar = new \cl_infocomplementarvalor();

                switch ($siglaAtributo) {
                    case 'PO':
                        $sql = $infoComplementar->sql_query_consulta_POs();
                        break;
                    case 'FP':
                        $sql = $infoComplementar->sql_query_consulta_FPs();
                        break;
                    case 'FR' :
                        $sql = $infoComplementar->sql_query_consulta_FRs();
                        break;
                    case 'NR' :
                        $sql = $infoComplementar->sql_query_consulta_NRs();
                        break;
                    case 'ND' :
                        $sql = $infoComplementar->sql_query_consulta_NDs();
                        break;
                    case 'FS' :
                        $sql = $infoComplementar->sql_query_consulta_FSs();
                        break;
                    case 'DC' :
                        $sql = $infoComplementar->sql_query_consulta_DCs();
                        break;
                    default:
                        $sql = '';
                        break;
                }

                if (!empty($sql)) {
                    \db_lovrot($sql, 15, "()", "", $funcao_js);
                }
            ?>
        </td>
    </tr>
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
    ?>
    <script>
    </script>
    <?php
}
?>
<script type="text/javascript">
    (function() {
        var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
