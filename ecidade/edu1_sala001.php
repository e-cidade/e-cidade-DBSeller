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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_sala_classe.php"));
require_once(modification("classes/db_edu_parametros_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
$clsala = new cl_sala;
$clsala->rotulo->label('ed16_i_codigo');
db_postmemory($_POST);

$cledu_parametros = new cl_edu_parametros;
$db_opcao = 1;
$db_botao = true;
$ed16_i_escola = db_getsession("DB_coddepto");
$descrdepto = db_getsession("DB_nomedepto");
$sql = $cledu_parametros->sql_query_file("", "*", "", "ed233_i_escola = $ed16_i_escola");

$result_param = db_query($sql);
db_fieldsmemory($result_param, 0);
if (isset($incluir)) {
    db_inicio_transacao();
    if ($ed16_c_pertence === 'S') {
        $ed16_local_funcionamento = '';
    }
    $clsala->ed16_local_funcionamento = $ed16_local_funcionamento;
    $clsala->incluir($ed16_i_codigo);
    $ed16_i_codigo = $clsala->ed16_i_codigo;
    db_fim_transacao();
}
if (isset($alterar)) {

    if ($ed16_c_pertence === 'S') {
        $ed16_local_funcionamento = '';
    }
    db_inicio_transacao();
    $db_opcao = 2;
    $clsala->ed16_local_funcionamento = $ed16_local_funcionamento;
    $clsala->alterar($ed16_i_codigo);
    db_fim_transacao();
}
if (isset($excluir)) {

    $sSql = "select 1  ";
    $sSql .= "  from sala ";
    $sSql .= " where exists (    select 1 from turma where ed57_i_sala = {$ed16_i_codigo} ";
    $sSql .= "                union all ";
    $sSql .= "                   select 1 from turmaac where ed268_i_sala = {$ed16_i_codigo} ";
    $sSql .= "              ) ";
    $sSql .= "limit 1 ";

    $rsVinculoTurma = db_query($sSql);
    $lVinculadoTurma = false;
    if ($rsVinculoTurma && pg_num_rows($rsVinculoTurma) > 0) {
        $lVinculadoTurma = true;
    }

    if (!$lVinculadoTurma) {

        db_inicio_transacao();
        $db_opcao = 3;
        $clsala->excluir($ed16_i_codigo);
        db_fim_transacao();
    }
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
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="container">
    <?php
    MsgAviso(db_getsession("DB_coddepto"), "escola");
    include(modification("forms/db_frmsala.php"));

    db_menu();
    ?>
</div>
</body>
</html>
<script>
    js_tabulacaoforms("form1", "ed16_i_tiposala", true, 1, "ed16_i_tiposala", true);
</script>
<?php
if (isset($incluir)) {
    if ($clsala->erro_status == "0") {
        $clsala->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clsala->erro_campo != "") {
            echo "<script> document.form1." . $clsala->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clsala->erro_campo . ".focus();</script>";
        };
    } else {
        $clsala->erro(true, true);
    };
};
if (isset($alterar)) {
    if ($clsala->erro_status == "0") {
        $clsala->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clsala->erro_campo != "") {
            echo "<script> document.form1." . $clsala->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clsala->erro_campo . ".focus();</script>";
        };
    } else {
        $clsala->erro(true, true);
    };
};
if (isset($excluir)) {

    if ($lVinculadoTurma) {
        echo "<script>alert('Dependência não pode ser excluída pois possui vinculo com uma ou mais turmas.')</script>";
    } else {
        $clsala->erro(true, true);
    };
};
if (isset($cancelar)) {
    echo "<script>location.href='" . $clsala->pagina_retorno . "'</script>";
}
?>