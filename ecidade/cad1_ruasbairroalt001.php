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
include(modification("classes/db_ruasbairro_classe.php"));
include(modification("classes/db_ruas_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clruasbairro = new cl_ruasbairro;
$clruas = new cl_ruas;
$db_opcao = 22;
$db_botao = false;
if (isset($alterar) || isset($excluir) || isset($incluir)) {
    $sqlerro = false;
    $clruasbairro->j16_lograd = $j16_lograd;
    $clruasbairro->j16_bairro = $j16_bairro;
    $clruasbairro->j16_cep = $j16_cep;
}
if (isset($incluir)) {
    if ($sqlerro == false) {
        try {
            $sqlruasbairro = "SELECT * FROM ruasbairro WHERE j16_bairro = {$clruasbairro->j16_bairro} AND j16_lograd = {$clruasbairro->j16_lograd}";
            $resultruasbairro = db_query($sqlruasbairro);
            $linharuasbairro = pg_num_rows($resultruasbairro);

            if ($linharuasbairro > 0) {
                throw new Exception("Bairro já cadastrado para este logradouro.");
            }

            db_inicio_transacao();

            $clruasbairro->incluir(null);
            $erro_msg = $clruasbairro->erro_msg;
            if ($clruasbairro->erro_status == 0) {
                $sqlerro = true;
            }

            db_fim_transacao();

        } catch (Exception $e) {
            db_fim_transacao(true);
            $clruasbairro->erro_status = 0;
            $clruasbairro->erro_msg = $e->getMessage();
            $erro_msg = $e->getMessage();

        }
    }
} else if (isset($alterar)) {
    if ($sqlerro == false) {
        try {
            $sqlruasbairro = "SELECT * FROM ruasbairro WHERE j16_bairro = {$clruasbairro->j16_bairro} AND j16_lograd = {$clruasbairro->j16_lograd}";
            $resultruasbairro = db_query($sqlruasbairro);
            $linharuasbairro = pg_num_rows($resultruasbairro);

            if ($linharuasbairro > 0) {
                throw new Exception("Bairro já cadastrado para este logradouro.");
            }

            db_inicio_transacao();

            $clruasbairro->j16_codigo = $j16_codigo;
            $clruasbairro->alterar($j16_codigo);
            $erro_msg = $clruasbairro->erro_msg;
            if ($clruasbairro->erro_status == 0) {
                $sqlerro = true;
            }

            db_fim_transacao();

        } catch (Exception $e) {
            db_fim_transacao(true);
            $clruasbairro->erro_status = 0;
            $clruasbairro->erro_msg = $e->getMessage();
            $erro_msg = $e->getMessage();
        }
    }
} else if (isset($excluir)) {
    if ($sqlerro == false) {
        db_inicio_transacao();
        $clruasbairro->j16_codigo = $j16_codigo;
        $clruasbairro->excluir($j16_codigo);
        $erro_msg = $clruasbairro->erro_msg;
        if ($clruasbairro->erro_status == 0) {
            $sqlerro = true;
        }
        db_fim_transacao($sqlerro);
    }
} else if (isset($opcao)) {
    $result = $clruasbairro->sql_record($clruasbairro->sql_query($j16_codigo));
    if ($result != false && $clruasbairro->numrows > 0) {
        db_fieldsmemory($result, 0);
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
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table width="790" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
            <center>
                <?php
                include(modification("forms/db_frmruasbairroalt.php"));
                ?>
            </center>
        </td>
    </tr>
</table>
</body>
</html>
<?php
if (isset($alterar) || isset($excluir) || isset($incluir)) {
    db_msgbox($erro_msg);
    if ($clruasbairro->erro_campo != "") {
        echo "<script> document.form1." . $clruasbairro->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1." . $clruasbairro->erro_campo . ".focus();</script>";
    }
}
?>
