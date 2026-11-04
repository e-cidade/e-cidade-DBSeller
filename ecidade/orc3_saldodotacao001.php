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
include(modification("dbforms/db_funcoes.php"));
require(modification("libs/db_liborcamento.php"));
include(modification("classes/db_orcorgao_classe.php"));
include(modification("classes/db_orcunidade_classe.php"));
include(modification("classes/db_orcdotacao_classe.php"));
include(modification("classes/db_orcdotacaocontr_classe.php"));
include(modification("classes/db_orcparametro_classe.php"));
$clorcparametro = new cl_orcparametro;
$clorcorgao = new cl_orcorgao;
$clorctiporec = new cl_orctiporec;
$clorcunidade = new cl_orcunidade;
$clorcdotacao = new cl_orcdotacao;
$clorcdotacaocontr = new cl_orcdotacaocontr;
$clestrutura = new cl_estrutura;
$clorctiporec->rotulo->label();
$clorcorgao->rotulo->label();
$clorcunidade->rotulo->label();
$clorcdotacao->rotulo->label();
$clorcdotacaocontr->rotulo->label();
db_postmemory($HTTP_POST_VARS);
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script>

        function js_abre(coddot) {
            if (typeof coddot === 'undefined' || coddot == '') {
                alert('Código da dotação inválido.');
                return false;
            }
            js_OpenJanelaIframe('', 'db_iframe_orgao', 'func_saldoorcdotacao.php?coddot=' + coddot, 'pesquisa', true);
        }

        function js_pesquisa() {
            var obj = document.form1;
            var query = '1=1';
            if (obj.o58_coddot.value != "") {
                var query = query + "&o58_coddot=" + obj.o58_coddot.value;
            } else if (obj.o50_estrutdespesa.value != '') {
                var query = query + "&o50_estrutdespesa=" + obj.o50_estrutdespesa.value;
            } else {
                if (obj.o40_orgao.value != "0") {
                    query = query + "&o40_orgao=" + obj.o40_orgao.value;
                    if (obj.o41_unidade.value != "") {
                        query = query + "&o41_unidade=" + obj.o41_unidade.value;
                    }
                }
                if (obj.o52_funcao.value != "0") {
                    query = query + "&o52_funcao=" + obj.o52_funcao.value;
                }
                if (obj.o53_subfuncao.value != "0") {
                    query = query + "&o53_subfuncao=" + obj.o53_subfuncao.value;
                }
                if (obj.o55_projativ.value != "0") {
                    query = query + "&o55_projativ=" + obj.o55_projativ.value;
                }
                if (obj.o56_elemento.value != "0") {
                    query = query + "&o56_elemento=" + obj.o56_elemento.value;
                }

                if (obj.gestao.value != "0") {
                    query = query + "&gestao=" + obj.gestao.value;
                }
            }
            js_OpenJanelaIframe('', 'db_iframe_pesquisa', 'orc3_saldodotacao002.php?' + query, 'pesquisa', true);
        }

        function js_limpa() {
            location.href = 'orc3_saldodotacao001.php';
        }

        function js_troca(nome) {
            ordem = new Array("o40_orgao", "o41_unidade", "o52_funcao", "o53_subfuncao", "o54_programa", "o55_projativ", "o56_elemento", "gestao");
            for (i = (ordem.length - 1); i > 0; i--) {
                if (ordem[i] == nome) {
                    break;
                } else {
                    if (eval("document.form1." + ordem[i] + ".options;")) {
                        if (eval("document.form1." + ordem[i] + ".options.length>'1';")) {
                            eval("document.form1." + ordem[i] + ".options[0].selected=true;");
                            eval("document.form1." + ordem[i] + "descr.options[0].selected=true;");
                        } else {
                            eval("document.form1." + ordem[i] + ".options[0].value='0';");
                            eval("document.form1." + ordem[i] + ".options[0].text='0';");
                            eval("document.form1." + ordem[i] + "descr.options[0].value='0';");
                            eval("document.form1." + ordem[i] + "descr.options[0].text='0';");
                        }
                    }
                }
            }
            document.form1.submit();
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body >
<div class="container">
<form name="form1" method="post">
    <table >

        <tr>
            <td align="left" nowrap title="<?= $To58_coddot ?>">
                <?= $Lo58_coddot ?>
            </td>
            <td align="left" nowrap>
                <?php
                db_input("o58_coddot", 6, $Io58_coddot, true, "text", 4);
                ?>
            </td>
        </tr>

        <?php
        $clestrutura->estrutura('o50_estrutdespesa');
        ?>

        <tr>
            <td><?= $Lo40_orgao ?></td>
            <td>
                <?php
                $result = $clorcorgao->sql_record($clorcorgao->sql_query(null, null, "o40_orgao,o40_descr", "o40_orgao", "o40_anousu=" . db_getsession("DB_anousu") . " and o40_instit=" . db_getsession("DB_instit")));
                db_selectrecord("o40_orgao", $result, true, 2, "", "", "", "0", $onchange = " js_troca('o40_orgao');");
                ?>
            </td>
        </tr>

        <tr>
            <td><?= $Lo41_unidade ?></td>
            <td>
                <?php
                if (isset($o40_orgao)) {
                    $result = $clorcunidade->sql_record($clorcunidade->sql_query(null, null, null, "o41_unidade,o41_descr", "o41_unidade", "o41_anousu=" . db_getsession("DB_anousu") . "  and o41_orgao=$o40_orgao "));
                    db_selectrecord("o41_unidade", $result, true, 2, "", "", "", ($clorcunidade->numrows > 1 ? "0" : ""), $onchange = "  js_troca('o41_unidade');");
                } else {
                    db_input("o41_unidade", 6, 0, true, "hidden", 0);
                }
                ?>
            </td>
        </tr>

        <tr>
            <td><?= $Lo58_funcao ?></td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= " and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }
                $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2(null, null, "distinct o52_funcao,o52_descr", "o52_funcao", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere"));

                db_selectrecord("o52_funcao", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), $onchange = "  js_troca('o52_funcao');");
                ?>
            </td>
        </tr>
        <tr>
            <td><?= $Lo58_subfuncao ?></td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= " and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }

                if (isset($o52_funcao) && $o52_funcao > 0) {
                    $dbwhere .= " and  o58_funcao = $o52_funcao ";
                }

                $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2(null, null, "distinct o53_subfuncao,o53_descr", "o53_subfuncao", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere"));
                db_selectrecord("o53_subfuncao", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), $onchange = "  js_troca('o53_subfuncao');");
                ?>
            </td>
        </tr>

        <tr>
            <td><?= $Lo58_programa ?></td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= "  and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }
                if (isset($o52_funcao) && $o52_funcao > 0) {
                    if ($dbwhere != "") {
                        $dbwhere .= "  and o58_funcao = $o52_funcao ";
                    }
                }

                if (isset($o53_subfuncao) && $o53_subfuncao > 0) {
                    $dbwhere .= "  and o58_subfuncao = $o53_subfuncao ";
                }

                $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2(null, null, "distinct o54_programa,o54_descr", "o54_programa", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere"));
                db_selectrecord("o54_programa", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), $onchange = "  js_troca('o54_programa');");
                ?>
            </td>
        </tr>

        <tr>
            <td><?= $Lo58_projativ ?></td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= "  and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }
                if (isset($o52_funcao) && $o52_funcao > 0) {
                    $dbwhere .= "  and o58_funcao = $o52_funcao ";
                }
                if (isset($o53_subfuncao) && $o53_subfuncao > 0) {
                    $dbwhere .= "  and o58_subfuncao = $o53_subfuncao ";
                }
                if (isset($o54_programa) && $o54_programa > 0) {
                    $dbwhere .= "  and o58_programa = $o54_programa ";
                }
                $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2(null, null, "distinct o55_projativ,o55_descr", "o55_projativ", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere"));
                db_selectrecord("o55_projativ", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), $onchange = "  js_troca('o55_projativ');");
                ?>
            </td>
        </tr>
        <tr>
            <td><?= $Lo58_codele ?></td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= " and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }
                if (isset($o52_funcao) && $o52_funcao > 0) {
                    $dbwhere .= " and o58_funcao = $o52_funcao ";
                }
                if (isset($o53_subfuncao) && $o53_subfuncao > 0) {
                    $dbwhere .= " and  o58_subfuncao = $o53_subfuncao ";
                }
                if (isset($o54_programa) && $o54_programa > 0) {
                    $dbwhere .= " and o58_programa = $o54_programa ";
                }
                if (isset($o55_projativ) && $o55_projativ > 0) {
                    $dbwhere .= " and  o58_projativ = $o55_projativ ";
                }
                $result = $clorcdotacao->sql_record($clorcdotacao->sql_query2(null, null, "distinct o56_elemento,o56_descr", "o56_elemento", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere"));
                db_selectrecord("o56_elemento", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), " js_troca('o56_elemento');");
                ?>
            </td>
        </tr>
        <tr>
            <td class="bold">Fonte de Recurso</td>
            <td>
                <?php
                $dbwhere = "";
                if (isset($o40_orgao) && $o40_orgao > 0) {
                    $dbwhere .= " and o58_orgao= $o40_orgao ";
                }
                if (isset($o41_unidade) && $o41_unidade > 0) {
                    $dbwhere .= " and o58_unidade = $o41_unidade ";
                }
                if (isset($o52_funcao) && $o52_funcao > 0) {
                    $dbwhere .= " and o58_funcao = $o52_funcao ";
                }
                if (isset($o53_subfuncao) && $o53_subfuncao > 0) {
                    $dbwhere .= " and o58_subfuncao = $o53_subfuncao ";
                }
                if (isset($o54_programa) && $o54_programa > 0) {
                    $dbwhere .= " and o58_programa = $o54_programa ";
                }
                if (isset($o55_projativ) && $o55_projativ > 0) {
                    $dbwhere .= " and o58_projativ = $o55_projativ ";
                }
                if (isset($o56_elemento) && $o56_elemento > 0) {
                    $dbwhere .= " and o56_elemento = '$o56_elemento' ";
                }

                $dbwhere .= " and (o15_datalimite is null or o15_datalimite > '" . date('Y-m-d', db_getsession('DB_datausu')) . "')";
                $sSqlRecursos = $clorcdotacao->sql_query2(null, null, "distinct gestao, descricao", "gestao", "o58_anousu=" . db_getsession("DB_anousu") . " and o58_instit=" . db_getsession("DB_instit") . " $dbwhere");

                $result = $clorcdotacao->sql_record($sSqlRecursos);

                db_selectrecord("gestao", $result, true, 2, "", "", "", ($clorcdotacao->numrows > 1 ? "0" : ""), " js_troca('gestao');");
                ?>
            </td>
        </tr>
        <tr height="20px">
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input name="pesquisa" type="button" onclick='js_pesquisa();' value="Pesquisa">
                <input name="limpa" type="button" onclick='js_limpa();' value="Limpar campos">
            </td>
        </tr>
    </table>
</form>
</div>
<?php
db_menu();
?>
</body>
</html>
