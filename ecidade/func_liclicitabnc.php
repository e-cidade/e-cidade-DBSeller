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

db_postmemory($_GET);
db_postmemory($_POST);
$oGet = db_utils::postMemory($_GET);

parse_str($_SERVER["QUERY_STRING"], $queryString);

$clliclicitem = new cl_liclicitem;
$clliclicita = new cl_liclicita;

$clliclicita->rotulo->label("l20_codigo");
$clliclicita->rotulo->label("l20_numero");
$clliclicita->rotulo->label("l20_edital");
$clrotulo = new rotulocampo;
$clrotulo->label("l03_descr");
$iAnoSessao = db_getsession("DB_anousu");

// Adiciona restricao no sql
$sWhereCredenciamento = "";

// Variavel bCredenciamento controla se vai exibir somente as de Chamamento Publico, Outras ou Todas
// licitacoes na lookup
// Caso a variavel nao exista, exibe tudo
if (isset($lCredenciamento)) {
    // Valor default exibe todas que nao sao Chamamento
    $sWhereCredenciamento = " pctipocompratribunal.l44_sequencial <> 54 and ";
    // Caso a variavel seja true, exibe somente as licitacoes de credenciamento
    if (!empty($lCredenciamento)) {
        $sWhereCredenciamento = " pctipocompratribunal.l44_sequencial = 54 and ";
    }
}

$sWhere = "exists (select pc11_quant, pc23_valor
                              from solicitem
                                   inner join pcprocitem           on pc11_codigo = pc81_solicitem
                                   inner join liclicitem           on l21_codpcprocitem = pc81_codprocitem
                                   inner join pcorcamitemlic       on pc26_liclicitem   = l21_codigo
                                   inner join pcorcamitem          on pc26_orcamitem    = pc22_orcamitem
                                   inner join pcorcamjulg          on pc22_orcamitem    = pc24_orcamitem
                                                                  and pc24_pontuacao    = 1
                                   inner join pcorcamval           on  pc23_orcamitem    = pc24_orcamitem
                                                                  and  pc23_orcamforne   = pc24_orcamforne
                                   inner join liclicita licsaldo   on l21_codliclicita = licsaldo.l20_codigo
                                   inner join cflicita             on l20_codtipocom = l03_codigo
                                   inner join pctipocompratribunal on l03_pctipocompratribunal = l44_sequencial
                                   left join (select coalesce(sum(e55_quant),0) as quantidade,
                                                     coalesce(sum(e55_vltot),0) as valor,
                                                     e73_pcprocitem as item
                                                from empautitempcprocitem
                                                       inner join empautitem on e55_autori = e73_autori
                                                                            and e55_sequen = e73_sequen
                                                       inner join empautoriza on e55_autori = e54_autori
                                                 where e73_pcprocitem = liclicitem.l21_codpcprocitem
                                                   and e54_anulad is null
                                                   group by e73_pcprocitem ) as saldo_autorizacao on item = pc81_codprocitem
                            where (pc11_quant > coalesce(quantidade,0) or coalesce(pc23_valor,0) > valor)
                          and licsaldo.l20_codigo = liclicita.l20_codigo
                          )";

$sWhereContratos = " and 1 = 1 ";
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body>
<table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
        <td height="63" align="center" valign="top">
            <table width="35%" border="0" align="center" cellspacing="0">
                <form name="form2" method="post" action="">
                    <tr>
                        <td width="4%" align="right" nowrap title="<?php echo $Tl20_codigo ?>">
                            <?php echo $Ll20_codigo ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("l20_codigo", 10, $Il20_codigo, true, "text", 4, "", "chave_l20_codigo");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="4%" align="right" nowrap title="<?php echo $Tl20_edital ?>">
                            <?php echo $Ll20_edital ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("l20_edital", 10, $Il20_edital, true, "text", 4, "", "chave_l20_edital");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="4%" align="right" nowrap title="<?php echo $Tl20_numero ?>">
                            <?php echo $Ll20_numero ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("l20_numero", 10, $Il20_numero, true, "text", 4, "", "chave_l20_numero");
                            ?>
                        </td>
                    </tr>
                    <tr>

                    <tr>
                        <td width="4%" align="right" nowrap title="<?php echo $Tl03_descr ?>">
                            <?php echo $Ll03_descr ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            $clcflicita = new cl_cflicita;
                            $dbWhereModalidade = "";
                            $todos = "0";

                            if (!empty($iModalidadeLicitacao)) {
                                $dbWhereModalidade = " and l03_codigo in ({$iModalidadeLicitacao})";
                                $todos = "";
                            }
                            $dbWhereClcflicita = "l03_instit = " . db_getsession("DB_instit") . "{$dbWhereModalidade}";

                            $resultClcflicita = $clcflicita->sql_record($clcflicita->sql_query("", "l03_codigo, l03_descr", "l03_codigo", $dbWhereClcflicita));

                            db_selectrecord("l03_codigo", $resultClcflicita, true, 1, "", "chave_l03_codigo", "", $todos, "", "2");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <b>Ano:</b>
                        </td>
                        <td>
                            <?php
                            db_input("l20_anousu", 10, "int", true, "text", 1, null, null, null, null, 4);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_liclicita.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php
            $and = "and ";
            $dbwhere = " 1=1 and ";

            if (isset($tipo) && trim($tipo) != "") {
                $dbwhere = " l08_altera is true and ";
            }
            if (isset($situacao) && trim($situacao) != '') {
                $dbwhere .= " l20_licsituacao in ($situacao) and ";
            }

            if (!empty($sigla)) {
                $dbwhere .= " l44_sigla = upper('$sigla') and ";
            }

            if (!empty($oGet->validasaldo)) {
                $dbwhere .= " $sWhere and ";
            }

            if (!empty($sTipoCompra)) {
                $dbwhere .= " pc50_codcom in ({$sTipoCompra}) and ";
            }

            $dbwhere_instit = " l20_instit = " . db_getsession("DB_instit");

            // Não deixa exibir nas autorizacoes as licitacoes que nao geram despesas
            if (db_getsession('DB_itemmenu_acessado') == 4718) {
                $dbwhere_instit .= " and l20_tipo = 1 ";
            }


            // Não deixa exibir as licitações de itens que estao vinculados a contratos.
            if (isset($lContratos) && $lContratos == 1) {
                $sWhereContratos .= " and ac24_sequencial is null ";
                $dbwhere_instit .= $sWhereContratos;
            }
            if (!isset($pesquisa_chave)) {
                if (isset($campos) == false) {
                    $campos = "liclicita.l20_codigo,
                    l44_sigla,
                    pctipocompra.pc50_descr,
                    liclicita.l20_numero,
                    liclicita.l20_anousu,
                    l08_descr,
                    liclicita.l20_dataaber,
                    liclicita.l20_objeto";
                }

                $dbwhere .= $sWhereCredenciamento;
                $anoAtual = date('Y');

                $where = [];
                $where[] = "l20_anousu BETWEEN " . ($anoAtual - 1) . " AND {$anoAtual} ";
                if (isset($chave_l20_numero) && (trim($chave_l20_numero) != "")) {
                    $where[] = "l20_numero = $chave_l20_numero";
                }
                if (isset($chave_l03_descr) && (trim($chave_l03_descr) != "")) {
                    $where[] = "l03_descr like '$chave_l03_descr%'";
                }
                if (isset($chave_l03_codigo) && (trim($chave_l03_codigo) != "0")) {
                    $where[] = "l03_codigo = $chave_l03_codigo";
                }
                if (isset($chave_l20_edital) && (trim($chave_l20_edital) != "")) {
                    $where[] = "l20_edital = $chave_l20_edital";
                }
                if (isset($l20_anousu) && (trim($l20_anousu) != "")) {
                    $where[] = "l20_anousu = $l20_anousu";
                }

                $sql = $clliclicita->sql_queryContratos(
                    "",
                    "distinct " . $campos,
                    "l20_codigo",
                    "$dbwhere $dbwhere_instit"
                );
                if (!empty($where)) {
                    $sWhere = implode(' and ', $where);
                    $sql = $clliclicita->sql_queryContratos(
                        "",
                        "distinct " . $campos,
                        "l20_codigo",
                        "$dbwhere $sWhere and $dbwhere_instit"
                    );
                }
                if (isset($chave_l20_codigo) && (trim($chave_l20_codigo) != "")) {
                    $sql = $clliclicita->sql_queryContratos(
                        null,
                        "distinct " . $campos,
                        "l20_codigo",
                        "$dbwhere  l20_codigo = $chave_l20_codigo and $dbwhere_instit"
                    );
                }

                $aRepassa = array();
                db_lovrot($sql . ' desc ', 15, "()", "", $funcao_js, null, 'NoMe', $aRepassa, false);
            } else {
                if ($pesquisa_chave != null && $pesquisa_chave != "") {
                    if (isset($param) && trim($param) != "") {
                        $result = $clliclicitem->sql_record($clliclicitem->sql_query_inf($pesquisa_chave));

                        if ($clliclicitem->numrows != 0) {
                            db_fieldsmemory($result, 0);
                            echo "<script>" . $funcao_js . "('$l20_codigo',false);</script>";
                        } else {
                            echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                        }
                    } else {
                        $result = $clliclicita->sql_record($clliclicita->sql_queryContratos(
                            null,
                            "*",
                            null,
                            "$dbwhere l20_codigo = $pesquisa_chave $and $dbwhere_instit "
                        ));

                        if ($clliclicita->numrows != 0) {
                            db_fieldsmemory($result, 0);
                            die("Com chave");
                            echo "<script>" . $funcao_js . "('$l20_codigo',false);</script>";
                        } else {
                            echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                        }
                    }
                } else {
                    echo "<script>" . $funcao_js . "('',false);</script>";
                }
            }
            ?>
        </td>
    </tr>
</table>
</body>

</html>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();

    const limpar = document.getElementById('limpar');
    const sequencial = document.getElementById('chave_l20_codigo');
    limpar.addEventListener('click', function () {
        select.options[select.selectedIndex].removeAttribute('selected');
        sequencial.value = '';
    })

    var select = document.getElementById("chave_l03_codigo");
    var optionValue = select.options[select.selectedIndex].value;

    document.getElementById('chave_l03_codigodescr').value = optionValue;
</script>
