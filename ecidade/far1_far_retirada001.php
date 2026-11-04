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
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($_POST);

$fa04_d_dtvalidade_dia = '';
$fa04_d_dtvalidade_mes = '';
$fa04_d_dtvalidade_ano = '';


$clfar_retirada = new cl_far_retirada();
$clfar_retiradaitens = new cl_far_retiradaitens();
$clrotulo = new rotulocampo();
$clfar_tiporeceita = new cl_far_tiporeceita();
$clmatparam = new cl_matparam();
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir();
$fa04_i_unidades = DB_getsession("DB_coddepto");
$departamento = DB_getsession("DB_coddepto");
$descrdepto = DB_getsession("DB_nomedepto");
$login = DB_getsession("DB_id_usuario");
$db_opcao = 1;
$db_botao = false;
$dHoje = date("Y-m-d", db_getsession("DB_datausu"));

$oConfigFarmacia = loadConfig('far_parametros');
if (isset($oConfigFarmacia) && $oConfigFarmacia->fa02_i_acaoprog != 0 && $oConfigFarmacia->fa02_i_acaoprog != null) {
    $fa10_i_programa = $oConfigFarmacia->fa02_i_acaoprog;
}

function calcula_data($data, $dias = 0, $meses = 0, $ano = 0)
{
    $data = explode("-", $data);
    $novadata = date("Y-m-d", mktime(0, 0, 0, $data[1] + $meses, $data[2] + $dias, $data[0] + $ano));
    return $novadata;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <link type="text/css" href="grid.style.css" rel="styleshet">
    <link
            type="text/css"
            href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
            rel="stylesheet"
    />
    <link
            type="text/css"
            href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
            rel="stylesheet"
    />
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/webseller.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/saude/validaCNS.js"></script>

    <style type="text/css">
        .classContinuado {
            background-color: #87CEEB;
        }

        .classAlterar {
            background-color: #BEBEBE;
        }

        .classExcluir {
            background-color: #FF0000;
        }

        .classNull {
            background-color: #FFFFFF;
        }

        [disabled] {
            background-color: #DEB887;
            color: #696969;
        }
    </style>
</head>
<body class="body-default">
<?php
validaDepartamentoLogado('unidade'); ?>
<div class="container">
    <?php
    include(modification("forms/db_frmfar_retirada.php")); ?>
</div>
<?php
db_menu();
?>
</body>
</html>
<?php
if (isset($oConfigFarmacia)) {
    switch ($oConfigFarmacia->fa02_i_cursor) {
        case 1:
            $sCampoFoco = "fa04_i_cgsund";
            break;
        case 2:
            $sCampoFoco = "s115_c_cartaosus";
            break;
        case 3:
            $sCampoFoco = "z01_v_nome";
            break;
        case 4:
            $sCampoFoco = "z01_v_cgccpf";
            break;
        default:
            $sCampoFoco = "fa04_i_cgsund";
    }
} else {
    $sCampoFoco = "fa04_i_cgsund";
}
echo "<script> $('$sCampoFoco').focus(); </script>";
?>
