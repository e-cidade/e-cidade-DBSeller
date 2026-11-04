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
require_once(modification("classes/db_saltes_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_saltescontrapartida_classe.php"));
require_once(modification("classes/db_saltesextra_classe.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$oPost = db_utils::postMemory($_POST);

$clsaltes              = new cl_saltes;
$clsaltescontrapartida = new cl_saltescontrapartida;
$clsaltesextra         = new cl_saltesextra;
$db_opcao = 22;
$db_botao = false;

if (isset($submit) && $submit == "Alterar") {

    try {
        db_inicio_transacao();
        /**
         * Verificamos se a data de criaçao eh menor ou igual a data do saldo;
         */
        if (!empty($k13_datvlr)) {

            if (
                db_strtotime(implode("-", array_reverse(explode("/", $k13_datvlr)))) <
                db_strtotime(implode("-", array_reverse(explode("/", $k13_dtimplantacao))))
            ) {
                throw new Exception("Data de Criação da conta deve ser menor ou igual a data de atualização do saldo");
            }
        }

        $db_opcao            = 2;
        $k13_conta           = $k13_reduz;
        $clsaltes->k13_conta = $k13_reduz;
        $clsaltes->alterar($k13_reduz);

        if (!empty($k103_contrapartida)) {

            $clsaltescontrapartida->excluir(null, "k103_saltes = {$k13_reduz}");
            if ($clsaltescontrapartida->erro_status == "0") {
                throw new Exception($clsaltescontrapartida->erro_msg);
            }

            $clsaltescontrapartida->k103_contrapartida = $k103_contrapartida;
            $clsaltescontrapartida->k103_saltes        = $k13_reduz;
            $clsaltescontrapartida->incluir(null);
        } else {
            $clsaltescontrapartida->excluir(null, "k103_saltes = {$k13_reduz}");
        }
        if ($clsaltescontrapartida->erro_status == "0") {
            throw new Exception($clsaltescontrapartida->erro_msg);
        }

        if (!empty($k109_saltesextra)) {

            $clsaltesextra->excluir(null, "k109_saltes = {$k13_reduz}");
            if ($clsaltesextra->erro_status == "0") {
                throw new Exception($clsaltesextra->erro_msg);
            }

            $clsaltesextra->k109_contaextra = $k109_saltesextra;
            $clsaltesextra->k109_saltes     = $k13_reduz;
            $clsaltesextra->incluir(null);
        } else {
            $clsaltesextra->excluir(null, "k109_saltes = {$k13_reduz}");
        }
        if ($clsaltesextra->erro_status == "0") {
            throw new Exception($clsaltesextra->erro_msg);
        }

        $clSaltesDepartamento = new cl_saltesdepartamento();
        $clSaltesDepartamento->excluir(null, "k212_saltes = {$k13_reduz}");
        if ($clSaltesDepartamento->erro_status == 0) {
            throw new Exception($clSaltesDepartamento->erro_msg);
        }

        if (!empty($departamentos)) {

            $clsaltesdepartamento = new cl_saltesdepartamento();
            $clsaltesdepartamento->excluir(null, "k212_saltes = {$k13_reduz}");
            if ($clsaltesdepartamento->erro_status == 0) {
                throw new Exception($clsaltesdepartamento->erro_msg);
            }

            $aDepartamentos = json_decode(stripslashes($departamentos));
            foreach ($aDepartamentos as $iInd => $oDepartamento) {

                $clSaltesDepartamento = new cl_saltesdepartamento();
                $clSaltesDepartamento->k212_departamento = $oDepartamento->codigo;
                $clSaltesDepartamento->k212_saltes = $k13_reduz;
                $clSaltesDepartamento->k212_principal = ($oDepartamento->principal == "t")?"true":"false";
                $clSaltesDepartamento->incluir(null);
                if ($clSaltesDepartamento->erro_status == "0") {
                    throw new Exception($clSaltesDepartamento->erro_msg);
                }
            }
        }

        db_fim_transacao();
    } catch (Exception $eErro) {

        db_fim_transacao(true);

        $clsaltes->erro_status = 0;
        $clsaltes->erro_msg    = $eErro->getMessage();
    }
}

if (isset($chavepesquisa)) {

    $db_opcao = 2;

    $result   = $clsaltes->sql_record($clsaltes->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0, true);

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
if (isset($submit) && $submit == "Alterar") {

    if ($clsaltes->erro_status == "0") {

        $clsaltes->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.submit.disabled=false;</script>  ";

        if ($clsaltes->erro_campo != "") {

            echo "<script> document.form1." . $clsaltes->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clsaltes->erro_campo . ".focus();</script>";
        }
    } else {
        $clsaltes->erro(true, true);
    }
}

if (!isset($chavepesquisa)) {
    echo "<script>$('pesquisar').click();</script>";
}
?>
