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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clorcparamrecursoval = new cl_orcparamrecursoval;


$clrotulo = new rotulocampo;
$clrotulo->label("o48_seq");
$clrotulo->label("o48_anousu");
$clrotulo->label("o48_codrec");
$clrotulo->label("o48_valor");
$clrotulo->label("o15_recurso");
$clrotulo->label("o200_descricao");
$clrotulo->label("o15_descr");

$db_opcao = 1;
$db_botao = true;
$anousu = db_getsession("DB_anousu");

if (isset ($incluir) && $incluir == "Incluir") {
    $clorcparamrecursoval->o48_anousu = db_getsession("DB_anousu");
    $clorcparamrecursoval->o48_instit = db_getsession("DB_instit");
    $clorcparamrecursoval->o48_codrec = $o48_codrec;
    $clorcparamrecursoval->o48_grupo = "1"; // grupo das extras
    $clorcparamrecursoval->o48_valor = $o48_valor;
    $clorcparamrecursoval->incluir(null);
    if ($clorcparamrecursoval->erro_status == 0) {
        db_msgbox($clorcparamrecursoval->erro_msg);
    }
}
if (isset ($alterar) && $alterar == "Alterar") {
    $clorcparamrecursoval->o48_anousu = db_getsession("DB_anousu");
    $clorcparamrecursoval->o48_instit = db_getsession("DB_instit");
    $clorcparamrecursoval->o48_codrec = $o48_codrec;
    $clorcparamrecursoval->o48_valor = $o48_valor;
    $clorcparamrecursoval->o48_seq = $o48_seq;
    $clorcparamrecursoval->alterar($clorcparamrecursoval->o48_anousu, $clorcparamrecursoval->o48_codrec, $clorcparamrecursoval->o48_instit);
    if ($clorcparamrecursoval->erro_status == 0) {
        db_msgbox($clorcparamrecursoval->erro_msg);
    }
}
if (isset ($excluir) && $excluir == "Excluir") {
    $clorcparamrecursoval->excluir($o48_seq);
    if ($clorcparamrecursoval->erro_status == 0) {
        db_msgbox($clorcparamrecursoval->erro_msg);
    }
    $db_opcao = 1;
}

// as opções abaixo são geradas pelo iframe_automatico
if (isset ($opcao) && $opcao == "alterar") {
    $res = $clorcparamrecursoval->sql_record(
        $clorcparamrecursoval->sql_query(null, "*", "o48_codrec",
            "o48_anousu=" . db_getsession("DB_anousu") . "
       					   and o48_grupo=1  and o48_seq=$o48_seq"));
    if ($clorcparamrecursoval->numrows > 0) {
        db_fieldsmemory($res, 0);
    }

    $db_opcao = 2;
}
if (isset ($opcao) && $opcao == "excluir") {
    $res = $clorcparamrecursoval->sql_record(
        $clorcparamrecursoval->sql_query(null, "*", "o48_codrec",
            "o48_anousu=" . db_getsession("DB_anousu") . "
				         and o48_grupo=1 and o48_seq=$o48_seq"));
    if ($clorcparamrecursoval->numrows > 0) {
        db_fieldsmemory($res, 0);
    }
    $db_opcao = 3;
}
if ($db_opcao == 1) {
    $o48_codrec = "";
    $o48_anousu = "";
    $o48_descr = "";
    $o48_valor = "";
    $o15_descr = "";
    $o15_loaespecificacao = "";
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
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script>
        function js_receita() {
            document.form1.o15_descr.value = '';

        }
    </script>
</head>
<body >

<form name="form1" method="post">

    <table border=1 width=100%>
        <tr>
            <td>
                <table border=0 align=left>
                    <tr>
                        <td><b> EXTRA-ORÇAMENTARIA </b></td>
                    </tr>
                    <tr>
                        <td><?php db_ancora($Lo48_seq, '', '3'); ?></td>
                        <td><?php db_input('o48_seq', 10, $Io48_seq, true, 'text', 3); ?></td>
                    </tr>

                    <tr>
                        <td >
                            <a id="ancoraFonteRecurso" href="#">Fonte de Recursos:</a>
                        </td>
                        <td>
                            <?php
                                db_input('o48_codrec', 10, $Io48_codrec, true, 'hidden', 3);
                                db_input('o15_recurso', 10, $Io15_recurso, true, 'text', 1);
                                db_input('o15_descr', 10, $Io15_descr, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Complemento:</td>
                        <td>
                            <?=db_input('o200_descricao', 60, $Io200_descricao, true, 'text', 3);?>
                        </td>
                    </tr>

                    <tr>
                        <td><?php @ db_ancora($Lo48_valor, '', '3'); ?></td>
                        <td><?php @ db_input('o48_valor', 10, $Io48_valor, true, 'text', $db_opcao, ""); ?></td>
                    </tr>

                    <tr>
                        <td colspan=2 align=center>
                            <input
                                name=<?= ($db_opcao == 1 ? 'incluir' : ($db_opcao == 2 || $db_opcao == 22 ? 'alterar' : 'excluir')) ?>
                                type="submit"
                                id="db_opcao"
                                value=<?= ($db_opcao == 1 ? 'Incluir' : ($db_opcao == 2 || $db_opcao == 22 ? 'Alterar' : 'Excluir')) ?>
                                <?= ($db_botao == false ? 'disabled' : '') ?>
                            >
                            <?php
                            if ($db_opcao != 1) {
                                ?>
                                <input name="novo" value="Novo" type="submit">
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <br>
                <?php

                $sSql = $clorcparamrecursoval->sql_query(null, "*", "o48_codrec", "o48_grupo=1 and o48_anousu=" . db_getsession("DB_anousu"));
                $db_opcao = 1;
                $chavepri = array("o48_seq" => @$o48_seq);
                $cliframe_alterar_excluir->chavepri = $chavepri;
                $cliframe_alterar_excluir->sql = $sSql;
                $cliframe_alterar_excluir->campos = "o48_anousu,o15_recurso ,o15_descr, o200_descricao,o48_valor";
                $cliframe_alterar_excluir->legenda = "lista";
                $cliframe_alterar_excluir->fieldset = false;
                $cliframe_alterar_excluir->iframe_height = "240";
                $cliframe_alterar_excluir->iframe_width = "100%";
                $cliframe_alterar_excluir->iframe_alterar_excluir('1');

                ?>
            </td>
        </tr>
    </table>

</form>
</body>
</html>
<script>


    const lookUpRecurso = new DBLookUp($('ancoraFonteRecurso'), $('o15_recurso'), $('o15_descr'), {
        'sArquivo': 'func_fonterecursocomplemento.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'aCamposAdicionais': ['db_codigo', 'o200_descricao']
    });

    lookUpRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2], retorno[3]);
    });

    lookUpRecurso.setCallBack('onChange', (erro, retorno) => {
        if (erro) {
            return;
        }

        preencheForm(retorno[3], retorno[0], retorno[2], retorno[4]);
    });

    const preencheForm = (recurso, descricao, id, complemento) => {
        $('o48_codrec').value = id;
        $('o15_recurso').value = recurso;
        $('o15_descr').value = descricao;
        $('o200_descricao').value = complemento
    };
</script>
