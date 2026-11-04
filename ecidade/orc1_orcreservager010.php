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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_orcreserva_classe.php"));
require_once(modification("classes/db_orcreservager_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));

db_postmemory($_POST);

$clorcreserva = new cl_orcreserva; // tabela de reserva
$clorcreservager = new cl_orcreservager; // tabela de reserva automatica
$clorcorgao = new cl_orcorgao;  // instancia orgãos
$clorcunidade = new cl_orcunidade; // instancia unidades
$clorcelemento = new cl_orcelemento; // instancia elemento
$clorcdotacao = new cl_orcdotacao; // instancia dotação

$anoSessao = db_getsession("DB_anousu");
$clpermusuario_dotacao = new cl_permusuario_dotacao($anoSessao, db_getsession("DB_id_usuario"));

$erro = false;
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
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
        <td width="360" height="18">&nbsp;</td>
        <td width="263">&nbsp;</td>
        <td width="25">&nbsp;</td>
        <td width="140">&nbsp;</td>
    </tr>
</table>
<form name="form1" mthod="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2"><br>
            </td>
        </tr>
        <tr>
            <td><strong>Atividade:</strong>
            </td>
            <td>
                <?php
                $result = $clorcdotacao->sql_record("
                    select distinct o.o58_projativ,p.o55_descr
                     from orcdotacao o
                          inner join (" . $clpermusuario_dotacao->sql . ") as x on x.o58_coddot = o.o58_coddot
                          inner join orcprojativ p on p.o55_anousu = o.o58_anousu and p.o55_projativ = o.o58_projativ
                     where o.o58_anousu = {$anoSessao} order by o.o58_projativ
                ");

                if ($clorcdotacao->numrows > 0){
                db_selectrecord("ativid", $result, true, 2, "", "", "", "", "document.form1.seleciona.click();");
                ?>
                <input name="seleciona" value="Seleciona" type="button"
                       onclick="location.href = 'orc1_orcreservager010.php?ativid='+document.form1.ativid.value">
                <br>
            </td>
        </tr>
        <tr>
            <td><strong>Recurso:</strong>
            </td>
            <td>
                <?php
                if (isset($ativid)) {
                    $campos = "distinct o58_codigo, o15_recurso ||' - ' || o15_descr ||' - ' || o200_descricao";
                    $where = " o58_anousu = {$anoSessao} and o58_projativ = {$ativid} and o58_instit = " . db_getsession("DB_instit");
                    $sql = $clorcdotacao->sql_query(null, null, $campos, "o58_codigo", $where);

                    $result = $clorcdotacao->sql_record($sql);
                    db_selectrecord("codigo", $result, true, 2, "", "", "", "", "document.form1.selecionarec.click();");
                    ?>
                    <input name="selecionarec" value="Seleciona" type="button"
                           onclick="document.getElementById('iframe_reserva').src = 'orc1_orcreservager012.php?ativid='+document.form1.ativid.value+'&recurso='+document.form1.codigo.value">
                    <br>
                    <?php
                }
                ?>
            </td>
        </tr>

        <tr>
            <td colspan="2" height="450" align="left" valign="top" bgcolor="#CCCCCC">
                <iframe id="iframe_reserva" name="iframe_reserva" src="" frameborder="0" marginwidth="0" leftmargin="0"
                        topmargin="0" height="450" scrolling="" width=100%>
                </iframe>
            </td>
        </tr>
        <tr>
            <td colspan="2" align='center'>
                <input name='atualiza' type='button' value='Atualiza' onclick='iframe_reserva.js_verifica_valores();'>
                <?php
                } else {
                    $erro = true;
                    $msg_erro = "Usuário sem permissão de empenho liberado.";
                }

                ?>
            </td>
        </tr>
    </table>
</form>
<?php
db_menu();
?>
</body>
</html>
<script>

    if ($('codigo')) {
        $('codigo').style.display = 'none';
    }
<?php
    if (!isset($ativid)) {
        echo "document.form1.seleciona.click();";
    } else {
        echo "document.form1.selecionarec.click();";
    }
?>
</script>
<?php
if ($erro == true)
    db_msgbox($msg_erro);
?>
