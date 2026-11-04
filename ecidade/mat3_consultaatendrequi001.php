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
//MODULO: material
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_matrequi_classe.php"));
include(modification("classes/db_atendrequi_classe.php"));
include(modification("classes/db_matrequiitem_classe.php"));
include(modification("classes/db_db_depart_classe.php"));
include(modification("classes/db_db_usuarios_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$daoDeposito = new cl_db_almox();
$clmatrequi = new cl_matrequi;
$clatendrequi = new cl_atendrequi;
$clmatrequiitem = new cl_matrequiitem;
$cldb_depart = new cl_db_depart;
$cldb_usuarios = new cl_db_usuarios;
$clmatrequi->rotulo->label();
$clatendrequi->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("nome");
$db_opcao = 3;
if (isset($codigo) && $codigo != "") {
    $codatendrequi = $codigo;
    $sql = $clatendrequi->sql_query_requi(
        $codigo,
        "distinct atendrequi.*,
                m40_depto,
                db_depart.descrdepto,
                m40_almox,
                deposito.descrdepto as descricao_deposito,
                nome,
                m40_codigo"
    );

    $result_atendrequi = $clatendrequi->sql_record($sql);
    if ($clatendrequi->numrows != 0) {
        db_fieldsmemory($result_atendrequi, 0);
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
    <script>
        function js_matrequi(codigo) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_requi',
                'mat3_consultarequi001.php?codigo=' + codigo,
                'Consulta Requisição',
                true
            );
        }
    </script>
</head>
<body class="body-container">
<div class="container">
    <table class="form-container">
        <tr>
            <td nowrap title="<?= @$Tm42_codigo ?>">
                <b>Código: </b>
            </td>
            <td>
                <?php
                db_input('m42_codigo', 10, $Im42_codigo, true);
                db_ancora('Requisição:', "js_matrequi($m40_codigo);", 1);
                db_input('m40_codigo', 10, $Im40_codigo, true);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Tm40_almox ?>">
                <?php
                db_ancora($Lm40_almox, "", 3);
                ?>
            </td>
            <td>
                <?php
                db_input('m40_almox', 10, $Im40_almox, true);
                db_input('descricao_deposito', 40, $Idescrdepto, true);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Tm40_depto ?>">
                <?php
                db_ancora($Lm40_depto, "", 3);
                ?>
            </td>
            <td>
                <?php
                db_input('m40_depto', 10, $Im40_depto, true);
                db_input('descrdepto', 40, $Idescrdepto, true);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Tm42_login ?>">
                <?php
                db_ancora(@$Lm42_login, "", 3);
                ?>
            </td>
            <td>
                <?php
                db_input('m42_login', 10, $Im42_login, true);
                db_input('nome', 40, $Inome, true);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Tm42_data ?>">
                <?= @$Lm42_data ?>
            </td>
            <td nowrap title="<?= @$Tm42_hora ?>">
                <?php
                db_inputdata('m42_data', @$m42_data_dia, @$m42_data_mes, @$m42_data_ano)
                ?>
                <?= @$Lm42_hora ?>
                <?php
                db_input('m42_hora', 5, $Im42_hora, true);
                ?>
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table class="form-container">
        <tr>
            <td align=center>
                <iframe name="atendrequiitem" id="atendrequiitem"
                        src="mat3_conatendrequiiframe001.php?codigo=<?= $codatendrequi ?>" width="600"
                        height="200" marginwidth="0" marginheight="0" frameborder="0">
                </iframe>
                <br>
                <br>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
