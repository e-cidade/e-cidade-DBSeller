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
include(modification("classes/db_saltes_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_corrente_classe.php"));
include(modification("classes/db_saltescontrapartida_classe.php"));
include(modification("classes/db_saltesextra_classe.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clsaltes              = new cl_saltes;
$clcorrente            = new cl_corrente;
$clsaltescontrapartida = new cl_saltescontrapartida;
$clsaltesextra         = new cl_saltesextra;
$clsaltesdepartamento  = new cl_saltesdepartamento;
$db_botao              = false;
$db_opcao              = 33;

if (isset($submit) && $submit == "Excluir") {

    try {

        db_inicio_transacao();

        $res = $clcorrente->sql_record($clcorrente->sql_query_file(null, null, null, "*", null, "k12_conta=$k13_reduz"));
        if ($clcorrente->numrows > 0) {
            throw new Exception("Não é possivel excluir uma conta com lançamentos");
        }

        $clsaltesextra->excluir(null, "k109_saltes = {$k13_reduz}");
        if ($clsaltesextra->erro_status == "0") {
            throw new Exception($clsaltesextra->erro_msg);
        }

        $clsaltescontrapartida->excluir(null, "k103_saltes = {$k13_reduz}");
        if ($clsaltescontrapartida->erro_status == "0") {
            throw new Exception($clsaltescontrapartida->erro_msg);
        }

        $clsaltesdepartamento = new cl_saltesdepartamento();
        $clsaltesdepartamento->excluir(null, "k212_saltes = {$k13_reduz}");
        if ($clsaltesdepartamento->erro_status == 0) {
            throw new Exception($clsaltesdepartamento->erro_msg);
        }

        $clsaltes->excluir($k13_reduz);
        if ($clsaltes->erro_status == "0") {
            throw new Exception($clsaltes->erro_msg);
        }

        db_fim_transacao();
    } catch (Exception $eErro) {

        db_fim_transacao(true);

        $clsaltes->erro_status = 0;
        $clsaltes->erro_msg    = $eErro->getMessage();
        $erro                  = true;
    }
}

if (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $clsaltes->sql_record($clsaltes->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0);
    $sSqlContrapartida = $clsaltescontrapartida->sql_query_contrapartida(
        null,
        "k103_contrapartida,k13_descr as k103_descr",
        null,
        "k103_saltes = {$chavepesquisa}"
    );

    $rsContrapartida = $clsaltescontrapartida->sql_record($sSqlContrapartida);
    if ($clsaltescontrapartida->numrows > 0) {
        db_fieldsmemory($rsContrapartida, 0);
    }
    $sSqlContaextra = $clsaltesextra->sql_query_extra(
        null,
        "k109_contaextra as k109_saltesextra,k13_descr as k103_descrextra",
        null,
        "k109_saltes = {$chavepesquisa}"
    );

    $rsContaExtra = $clsaltesextra->sql_record($sSqlContaextra);
    if ($clsaltesextra->numrows > 0) {
        db_fieldsmemory($rsContaExtra, 0);
    }
    $db_botao = true;
}
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="container">
        <?php
        require_once(modification("forms/db_frmsaltes.php"));
        ?>
    </div>
    <?php
    db_menu();
    ?>
</body>

</html>
<?php
if (isset($submit) && $submit == "Excluir") {

    if ($clsaltes->erro_status == "0") {
        $clsaltes->erro(true, false);
    } else {
        $clsaltes->erro(true, true);
    }
}

if (!isset($chavepesquisa)) {
    echo "<script>$('pesquisar').click();</script>";
}
?>
