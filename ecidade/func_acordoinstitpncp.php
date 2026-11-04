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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_acordo_classe.php"));

$iTipoFiltro = 0;
$lAtivo = '';
db_postmemory($_POST);
db_postmemory($_GET);
parse_str($_SERVER["QUERY_STRING"]);

$clacordo = new cl_acordo;
$clacordo->rotulo->label("ac16_sequencial");
$clacordo->rotulo->label("ac16_numero");
$clacordo->rotulo->label("ac16_acordogrupo");
$clacordo->rotulo->label("ac16_origem");
$clacordo->rotulo->label("ac16_contratado");

$oGet = db_utils::postMemory($_GET);
$iInstituicaoSessao = db_getsession('DB_instit');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php
    db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js");
    db_app::load("dbmessageBoard.widget.js, prototype.js, contratos.classe.js");
    db_app::load("estilos.css, grid.style.css");
    ?>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<div class="container">
    <table>
        <tr>
            <td>
                <div class="subcontainer">
                    <form name="form2" method="post" action="">
                        <table>
                            <tr>
                                <td nowrap title="<?= $Tac16_sequencial ?>">
                                    <?= $Lac16_sequencial ?>
                                </td>
                                <td>
                                    <?php
                                    db_input("ac16_sequencial", 10, $Iac16_sequencial, true, "text", 4, "", "chave_ac16_sequencial");
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td title="<?php echo $Tac16_numero; ?>">
                                    <?php echo $Lac16_numero; ?>
                                </td>
                                <td>
                                    <?php
                                    db_input("ac16_numero", 10, 0, true, "text", 4, " placeholder='Número/Ano' ");
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td title="<?php echo $Tac16_origem; ?>">
                                    <?php echo $Lac16_origem; ?>
                                </td>
                                <td>
                                    <?php
                                    $acordoOrigem = new \App\Domain\Patrimonial\Contratos\Models\AcordoOrigem();
                                    $origens = $acordoOrigem
                                        ->orderBy('ac28_descricao')
                                        ->pluck('ac28_descricao', 'ac28_sequencial')
                                        ->all();

                                    $listaOrigens = ["Todas"];
                                    $listaOrigens += $origens;

                                    db_select('ac16_origem', $listaOrigens, true, 2);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td title="<?php echo $Tac16_contratado; ?>">
                                    <?php db_ancora('Contratado:', 'js_pesquisaac16_contratado(true);', 1); ?>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'ac16_contratado',
                                        5,
                                        $Iac16_contratado,
                                        true,
                                        'text',
                                        1,
                                        "onchange='js_pesquisaac16_contratado(false);'"
                                    );
                                    db_input('z01_nome', 35, "", true, 'text', 3);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap title="<?php echo $Tac16_acordogrupo; ?>">
                                    <?php db_ancora($Lac16_acordogrupo, "js_pesquisaac16_acordogrupo(true);", 1); ?>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'ac16_acordogrupo',
                                        5,
                                        $Iac16_acordogrupo,
                                        true,
                                        'text',
                                        1,
                                        "onchange='js_pesquisaac16_acordogrupo(false);'"
                                    );
                                    db_input('ac02_descricao', 35, "", true, 'text', 3);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" align="center">
                                    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                                    <input name="limpar" type="reset" id="limpar" value="Limpar">
                                    <input
                                        name="Fechar"
                                        type="button"
                                        id="fechar"
                                        value="Fechar"
                                        onClick="parent.db_iframe_acordo.hide();"
                                    >
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $aWhereAdicional = array();
                $aWhereAdicional[] = "ac16_instit = {$iInstituicaoSessao}";
                $aWhereAdicional[] = "ac16_dataassinatura IS NOT NULL";
                $aWhereAdicional[] = "ac16_dataassinatura >= '2021-04-01'";
                $aWhereAdicional[] = "ac16_datainicio >= '2021-04-01'";
                $aWhereAdicional[] = "ac16_anousu >= 2021";

                if (!empty($contratosPncp)) {
                    $aWhereAdicional[] = "pn04_codigo IS NOT NULL";
                }

                if (!empty($oGet->iCodigoCategoria)) {
                    $aWhereAdicional[] = "ac16_acordocategoria = {$oGet->iCodigoCategoria}";
                }

                if (!isset($pesquisa_chave)) {
                    $campos = "
                        ac16_sequencial,
                        (ac16_numero || '/' || ac16_anousu)::varchar as ac16_numeroacordo,
                        acordo.ac16_resumoobjeto::text,
                        ac17_descricao as ac16_acordosituacao,
                        z01_nome,
                        db_depart.descrdepto,
                        ac16_dataassinatura,
                        ac16_datainicio,
                        ac16_datafim,
                        ac28_descricao AS ac16_origem,
                        pn04_ano,
                        pn04_numero
                    ";

                    if (isset($chave_ac16_sequencial) && (trim($chave_ac16_sequencial) != "")) {
                        $aWhereAdicional[] = "ac16_sequencial = {$chave_ac16_sequencial}";
                    } elseif (isset($ac16_acordogrupo) && (trim($ac16_acordogrupo) != "")) {
                        $aWhereAdicional[] = "ac16_acordogrupo = '{$ac16_acordogrupo}'";
                    }

                    if (!empty($ac16_numero)) {
                        $aNumeroAcordo = explode('/', $ac16_numero);
                        $iNumero = $aNumeroAcordo[0];
                        $iAno = !empty($aNumeroAcordo[1]) ? $aNumeroAcordo[1] : db_getsession("DB_anousu");

                        $aWhereAdicional[] = "ac16_numero = '$iNumero'";
                        $aWhereAdicional[] = "ac16_anousu = '$iAno'";
                    }

                    if (!empty($ac16_origem)) {
                        $aWhereAdicional[] = "ac16_origem = {$ac16_origem}";
                    }

                    if (!empty($ac16_contratado)) {
                        $aWhereAdicional[] = "ac16_contratado = {$ac16_contratado}";
                    }

                    $sql = $clacordo->sql_query(
                        "",
                        $campos,
                        "ac16_sequencial DESC",
                        implode(" and ", $aWhereAdicional)
                    );

                    $repassa = array();
                    if (isset($chave_ac16_sequencial)) {
                        $repassa = array("chave_ac16_sequencial" => $chave_ac16_sequencial,);
                    }

                    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
                } else {
                    if (!empty($pesquisa_chave)) {
                        $aWhereAdicional[] = "ac16_sequencial = {$pesquisa_chave}";
                        $result = $clacordo->sql_record($clacordo->sql_query(null, "*", null, implode(" and ", $aWhereAdicional)));

                        if ($clacordo->numrows != 0) {
                            db_fieldsmemory($result, 0);
                            if (isset($descricao) && $descricao == 'true') {
                                if (isset($isLancador) && $isLancador == "true") {
                                    echo "<script>" . $funcao_js . "('$ac16_resumoobjeto',false);</script>";
                                } else {
                                    echo "<script>" . $funcao_js . "('$ac16_sequencial','$ac16_resumoobjeto', false, '$ac16_origem');" . "</script>";
                                }
                            } else {
                                echo "<script>" . $funcao_js . "('$ac16_sequencial',false);</script>";
                            }
                        } else {
                            if (isset($descricao) && $descricao == 'true') {
                                echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado','',true);" . "</script>";
                            } else {
                                echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);" . "</script>";
                            }
                        }
                    } else {
                        if (isset($descricao) && $descricao == 'true') {
                            echo "<script>" . $funcao_js . "('','',false);</script>";
                        } else {
                            echo "<script>" . $funcao_js . "('',false);</script>";
                        }
                    }
                }
                ?>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
<script>
    js_tabulacaoforms("form2", "chave_ac16_sequencial", true, 1, "chave_ac16_sequencial", true);

    function js_pesquisaac16_acordogrupo(mostra) {
        if (mostra == true) {
            let sUrl = 'func_acordogrupo.php?funcao_js=parent.js_mostraacordogrupo1|ac02_sequencial|ac02_descricao';
            js_OpenJanelaIframe('',
                'db_iframe_pesquisagrupo',
                sUrl,
                'Pesquisar Grupos de Acordo',
                true,
                '0');
        } else {

            if ($('ac16_acordogrupo').value != '') {
                js_OpenJanelaIframe('',
                    'db_iframe_pesquisagrupo',
                    'func_acordogrupo.php?pesquisa_chave=' + $('ac16_acordogrupo').value +
                    '&funcao_js=parent.js_mostraacordogrupo',
                    'Pesquisar Grupos de Acordo',
                    false,
                    '0');
            } else {
                $('ac16_acordogrupo').value = '';
                $('ac02_descricao').value = '';
            }
        }
    }

    function js_pesquisaac16_contratado(mostra) {
        if (mostra == true) {
            let sUrl = 'func_nome.php?funcao_js=parent.js_mostracontratado|z01_numcgm|z01_nome';
            js_OpenJanelaIframe('',
                'db_iframe_cgm',
                sUrl,
                'Pesquisar CGM',
                true,
                '0');
        } else {
            if ($('ac16_contratado').value != '') {
                js_OpenJanelaIframe('',
                    'db_iframe_cgm',
                    'func_nome.php?pesquisa_chave=' + $('ac16_contratado').value +
                    '&funcao_js=parent.js_mostracontratado1',
                    'Pesquisar CGM',
                    false,
                    '0');
            } else {
                $('ac16_contratado').value = '';
                $('z01_nome').value = '';
            }
        }
    }

    function js_mostracontratado(chave1, chave2) {

        $('ac16_contratado').value = chave1;
        $('z01_nome').value = chave2;
        $('ac16_contratado').focus();

        db_iframe_cgm.hide();
    }

    function js_mostracontratado1(erro, chave2) {

        $('z01_nome').value = chave2;

        if (erro == true) {
            $('z01_nome').value = '';
            $('ac16_contratado').value = '';
        }

        $('ac16_contratado').focus();

        db_iframe_cgm.hide();
    }

    function js_mostraacordogrupo(chave, erro) {

        $('ac02_descricao').value = chave;

        if (erro == true) {
            $('ac16_acordogrupo').focus();
            $('ac16_acordogrupo').value = '';
            $('ac02_descricao').value = '';
        }
    }

    function js_mostraacordogrupo1(chave1, chave2) {

        $('ac16_acordogrupo').value = chave1;
        $('ac02_descricao').value = chave2;
        $('ac16_acordogrupo').focus();

        db_iframe_pesquisagrupo.hide();
    }
</script>

<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
