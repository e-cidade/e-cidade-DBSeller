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
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
$clitbicancela = new cl_itbicancela;
$oGet            = db_utils::postmemory($_GET);
$cldb_config = new cl_db_config;
$clparitbi   = new cl_paritbi;
$db_botao      = true;
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <style type="text/css">
        .texto {
            background-color: white
        }
    </style>
</head>

<body class="body-default">
    <?php
    $rsparitbi = $clparitbi->sql_record($clparitbi->sql_query_file(db_getsession("DB_anousu"), "it24_emiteguiaquitacao"));
    db_fieldsmemory($rsparitbi, 0);
    $bEmiteDeclarQuit = $it24_emiteguiaquitacao == 't' ? true : false;
    ?>
    <center>
        <fieldset style="margin-top:25px; max-width: 450px">
            <legend>Emitir Guia/Declaração de Quitação</legend>
            <form name="form1" method="post" action="">
                <table style="margin-top: 10px;">
                    <tr>
                        <td align='right'>
                            <b>Tipo :</b>
                        </td>
                        <td colspan='3'>
                            <?php
                            if ($bEmiteDeclarQuit) {
                                $aTipo = array(
                                    'n' => 'Guia normal',
                                    'q' => 'Declaração de quitação'
                                );
                            } else {
                                $aTipo = array('n' => 'Guia normal');
                            }
                            db_select('tipoguia', $aTipo, true, 2, " style='width:275px;'");
                            ?>
                        </td>
                    </tr>
                </table>
                <input style="margin-top: 10px;" name="emitirguia" type="button" id="emitirguia" value="Emitir Guia" <?= ($db_botao == false ? "disabled" : "") ?>
                    onClick="return js_emitir()" />
            </form>
        </fieldset>
    </center>
</body>

</html>
<script>
    var mensagem = "tributario.itbi.db_frmitbiemite.";


    function js_emitir() {

        var iGuia = <?= $oGet->guia ?>;
        var iTipo = document.form1.tipoguia.value;

        if (isNaN(iGuia) || iGuia == '') {

            alert(_M(mensagem + "codigo_itbi_invalido"));
            return false;
        }


        var sArquivo = "reciboitbi.php";

        if (iTipo == 'q') {

            sArquivo = "reciboitbiGuiaQuitacao.php"
            
        }



        var sParam = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height=" +
            (screen.height - 100) + ",width=" + (screen.width - 100);

        if (iGuia != "") {
            window.open(sArquivo + '?itbi=' + iGuia + '&tipoguia=' + iTipo, "", sParam);
            return true;
        } else {
            alert('Escolha uma guia para emissão!')
            return false;
        }
    }
</script>