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
include(modification("classes/db_matrequiitem_classe.php"));
include(modification("classes/db_db_depart_classe.php"));
include(modification("classes/db_db_usuarios_classe.php"));
include(modification("classes/db_matestoquedev_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$clmatrequi = new cl_matrequi;
$clmatrequiitem = new cl_matrequiitem;
$cldb_depart = new cl_db_depart;
$cldb_usuarios = new cl_db_usuarios;
$clmatestoquedev = new cl_matestoquedev;
$clmatestoquedev->rotulo->label();
$clmatrequi->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("nome");
$db_opcao = 3;
if (isset($codigo) && $codigo != "") {
    $sql = $clmatestoquedev->sql_query($codigo, "*, db_depart.descrdepto as descricao_deposito, b.descrdepto as descricao_departamento");
    $result_devolucao = $clmatestoquedev->sql_record($sql);
    if ($clmatestoquedev->numrows != 0) {
        db_fieldsmemory($result_devolucao, 0);
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
        function js_atendrequi(codigo) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_atendrequi', 'mat3_consultaatendrequi001.php?codigo=' + codigo, 'Consulta Atendimento da Requisição', true);
        }
    </script>
    <style>
        .bordas {
            border: 2px solid #cccccc;
            border-top-color: #999999;
            border-right-color: #999999;
            border-left-color: #999999;
            border-bottom-color: #999999;
            background-color: #999999;
        }

        .bordas_corp {
            border: 1px solid #cccccc;
            border-top-color: #999999;
            border-right-color: #999999;
            border-left-color: #999999;
            border-bottom-color: #999999;
            background-color: #cccccc;
        }
    </style>
</head>
<body class="body-container">
<div class="container">
        <table class="form-container">
            <tr>
                <td nowrap title="<?= @$Tm45_codigo ?>">Código:</td>
                <td>
                    <?php
                    db_input('m45_codigo', 10, $Im45_codigo, true);
                    ?>
                    <?php
                    db_ancora('Atendimento:', "js_atendrequi($m45_codatendrequi);", 1);
                    ?>

                    <?php
                    db_input('m45_codatendrequi', 10, $Im45_codatendrequi, true);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tm40_almox ?>">
                    <?php
                    db_ancora(@$Lm40_almox, "", 3);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('m40_almox', 10, $Im40_almox, true);
                    ?>
                    <?php
                    db_input('descricao_deposito', 40, $Idescrdepto, true);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tm40_depto ?>">
                    <?php
                    db_ancora(@$Lm40_depto, "", 3);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('m40_depto', 10, $Im40_depto, true);
                    ?>
                    <?php
                    db_input('descricao_departamento', 40, $Idescrdepto, true)
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tm45_login ?>">
                    <?php
                    db_ancora(@$Lm45_login, "", 3);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('m45_login', 10, $Im45_login, true, 'text', 3, "")
                    ?>
                    <?php
                    db_input('nome', 40, $Inome, true, 'text', 3, '')
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tm45_data ?>">
                    <?= @$Lm45_data ?>
                </td>
                <td nowrap title="<?= @$Tm45_hora ?>">
                    <?php
                    db_inputdata('m45_data', @$m45_data_dia, @$m45_data_mes, @$m45_data_ano, true, 'text', 3, "")
                    ?>
                    <?= @$Lm40_hora ?>
                    <?php
                    db_input('m45_hora', 5, $Im45_hora, true)
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tm45_obs ?>">
                    <?= @$Lm45_obs ?>
                </td>
                <td>
                    <?php
                    db_textarea('m45_obs', 0, 50, $Im45_obs, true, 'text', $db_opcao)
                    ?>
                </td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td align=center>
                    <iframe name="matestoquedev" id="matestoquedev"
                            src="mat3_consdevolucaoitens001.php?codigo=<?= $codigo ?>" width="800"
                            height="200" marginwidth="0" marginheight="0" frameborder="0">
                    </iframe>
                    <br>
                    <br>
                </td>
            </tr>
        </table>
</div>
<script>
</script>
</body>
</html>
