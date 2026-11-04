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

//MODULO: orcamento
require_once(modification("libs/db_conecta.php"));
$clorcdotacao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o54_anousu");
$clrotulo->label("o55_descr");
$clrotulo->label("o41_descr");
$clrotulo->label("o40_descr");
$clrotulo->label("o41_descr");
$clrotulo->label("o52_descr");
$clrotulo->label("o53_descr");
$clrotulo->label("o56_elemento");
$clrotulo->label("o56_descr");
$clrotulo->label("o15_descr");
$clrotulo->label("o61_codigo");
$clrotulo->label("o11_descricao");
$clrotulo->label("DB_txtdotacao");
$clrotulo->label("o58_concarpeculiar");
$clrotulo->label("c58_descr");
$clrotulo->label("o15_recurso");
$clrotulo->label("o200_descricao");

$clestrutura = new cl_estrutura;
$display = FONTE_RECURSO_UNIAO ? '' : "display: none";

$anoSessao = db_getsession("DB_anousu");

if (isset($atualizar)) {
    $o58_orgao = '';
    $o58_unidade = '';
    $o58_funcao = '';
    $o58_subfuncao = '';
    $o58_programa = '';
    $o58_projativ = '';
    $o56_elemento = '';
    $o61_codigo = '';
    $o58_codigo = '';
    $o40_descr = '';
    $o41_descr = '';
    $o52_descr = '';
    $o53_descr = '';
    $o54_descr = '';
    $o56_descr = '';
    $o55_descr = '';
    $o15_descr = '';
    $o15_contra_recurso = '';

    if ($o50_estrutdespesa == "") {
        $tot = '0';
    } else {

        // adicionei um replace pois teve impacto colocando o explode \.
        $o50_estrutdespesa = str_replace("\.", ".",$o50_estrutdespesa);
        $matriz = explode('.', $o50_estrutdespesa);
        $tot = count($matriz);
    }

    for ($i = 0; $i < $tot; $i++) {
        switch ($i) {
            case 0://orgao
                $result = $clorcorgao->sql_record(
                    $clorcorgao->sql_query_file(
                        $anoSessao,
                        $matriz[$i],
                        'o40_descr,o40_orgao as o58_orgao'
                    )
                );
                if ($clorcorgao->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o40_descr = 'Chave ('.$matriz[$i].') não encontrado';
                    $o58_orgao = '';
                }
                break;
            case 1://unidade
                if ($o58_orgao != '') {
                    $result = $clorcunidade->sql_record(
                        $clorcunidade->sql_query_file(
                            $anoSessao,
                            $o58_orgao,
                            $matriz[$i],
                            'o41_descr, o41_unidade as o58_unidade, o41_instit'
                        )
                    );
                    if ($clorcunidade->numrows > 0) {
                        db_fieldsmemory($result, 0);
                        $o58_instit  = $o41_instit;
                        $sql         = "select nomeinst from db_config where codigo = $o41_instit";
                        $nomeinst    = pg_fetch_result(db_query($sql), 0, "nomeinst");
                    } else {
                        $o58_unidade = '';
                        $o41_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                    }
                } else {
                    $o58_unidade = '';
                    $o41_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 2://funcao
                $result = $clorcfuncao->sql_record(
                    $clorcfuncao->sql_query_file(
                        $matriz[$i],
                        'o52_descr, o52_funcao as o58_funcao'
                    )
                );
                if ($clorcfuncao->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o58_funcao = '';
                    $o52_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 3://subfuncao
                $result = $clorcsubfuncao->sql_record(
                    $clorcsubfuncao->sql_query_file(
                        $matriz[$i],
                        'o53_descr, o53_subfuncao as o58_subfuncao'
                    )
                );
                if ($clorcsubfuncao->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o58_subfuncao = '';
                    $o53_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 4://programa
                $result = $clorcprograma->sql_record(
                    $clorcprograma->sql_query_file(
                        $anoSessao,
                        $matriz[$i],
                        'o54_descr, o54_programa as o58_programa'
                    )
                );
                if ($clorcprograma->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o58_programa = '';
                    $o54_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 5://projativ
                $result = $clorcprojativ->sql_record(
                    $clorcprojativ->sql_query_file(
                        $anoSessao,
                        $matriz[$i],
                        'o55_descr, o55_projativ as o58_projativ'
                    )
                );
                if ($clorcprojativ->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o58_projativ = '';
                    $o55_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 6://elemento de despesa
                $result = $clorcelemento->sql_record(
                    $clorcelemento->sql_query(
                        null,
                        null,
                        "o56_descr, o56_elemento ",
                        "",
                        "o56_anousu = " . $anoSessao . " and o56_elemento like '" . substr($matriz[$i], 0, 12) . "%' "
                    )
                );
                if ($clorcelemento->numrows > 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o56_elemento = '';
                    $o56_descr = ' Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 7://tipo de  recurso
                $campos = "o15_descr, o15_recurso, o200_descricao, o15_codigo as o58_codigo, o15_complemento";
                $where = "o15_recurso ='{$matriz[$i]}'";
                $sql1 = $clorctiporec->sql_queryComplemento(null, $campos, 'o15_complemento', $where);

                $result = $clorctiporec->sql_record($sql1);
                if ($clorctiporec->numrows != 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o58_codigo = '';
                    $o15_descr = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 8://contra recurso
                $result = $clorctiporec->sql_record(
                    $clorctiporec->sql_query_file(
                        $matriz[$i],
                        "o15_descr as o15_contra_recurso, o15_codigo as o61_codigo"
                    )
                );
                if ($clorctiporec->numrows != 0) {
                    db_fieldsmemory($result, 0);
                } else {
                    $o61_codigo = '';
                    $o15_contra_recurso = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
                break;
            case 9://localiador gastos
                $oDaoLocalizador = new cl_ppasubtitulolocalizadorgasto();
                $sSqlSubtitulo = $oDaoLocalizador->sql_query_file($matriz[$i]);
                $rsLocalizador = $oDaoLocalizador->sql_record($rsLocalizador);
                if ($oDaoLocalizador->numrows != 0) {
                    db_fieldsmemory($rsLocalizador, 0);
                } else {
                    $o58_localiadorgastos = '';
                    $o11_descriacao = 'Chave (' . $matriz[$i] . ') não encontrado';
                }
        }
    }
}

?>
<form name="form1" method="post" action="">
    <table>
        <tr>
            <td>
                <fieldset>
                    <legend><b>Dotação</b></legend>
                    <table>
                        <tr>
                            <td nowrap title="<?=@$To58_anousu?>">
                                <?php echo $Lo58_anousu;?>
                            </td>
                            <td>
                                <?php
                                $o58_anousu = db_getsession('DB_anousu');
                                db_input('o58_anousu', 4, $Io58_anousu, true, 'text', 3, "");
                                ?>
                            </td>
                        </tr>
                        <?php
                        $clestrutura->reload = true;
                        $clestrutura->size = 70;
                        $clestrutura->estrutura("o50_estrutdespesa");
                        ?>
                        <tr>
                            <td nowrap title="<?=@$To58_coddot?>">
                                <?=@$Lo58_coddot?>
                            </td>
                            <td>
                                <?php db_input('o58_coddot', 11, $Io58_coddot, true, 'text', 3, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_instit?>">
                                <?=@$Lo58_instit?>
                            </td>
                            <td>
                                <?php
                                db_input('o58_instit', 11, $Io58_instit, true, 'text', 3, '');
                                db_input('nomeinst', 55, $Io58_instit, true, 'text', 3, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_orgao?>">
                                <?php db_ancora(@$Lo58_orgao, "js_pesquisao58_orgao(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_orgao',
                                    11,
                                    $Io58_orgao,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_orgao(false);'"
                                );
                                db_input('o40_descr', 55, $Io40_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <?php
                        $mostraUni = 3;
                        if (isset($o58_orgao) && @$o58_orgao != '') {
                            $mostraUni = $db_opcao;
                            $filtrar   = "orgao=$o58_orgao&";
                        }
                        ?>
                        <tr>
                            <td nowrap title="<?=@$To58_unidade?>">
                                <?php db_ancora(@$Lo58_unidade, "js_pesquisao58_unidade(true);", $mostraUni);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_unidade',
                                    11,
                                    $Io58_unidade,
                                    true,
                                    'text',
                                    $mostraUni,
                                    " onchange='js_pesquisao58_unidade(false);'"
                                );
                                db_input('o41_descr', 55, $Io41_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_funcao?>">
                                <?php db_ancora(@$Lo58_funcao, "js_pesquisao58_funcao(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_funcao',
                                    11,
                                    $Io58_funcao,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_funcao(false);'"
                                );
                                db_input('o52_descr', 55, $Io52_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_subfuncao?>">
                                <?php db_ancora(@$Lo58_subfuncao, "js_pesquisao58_subfuncao(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_subfuncao',
                                    11,
                                    $Io58_subfuncao,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_subfuncao(false);'"
                                );
                                db_input('o53_descr', 55, $Io53_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_programa?>">
                                <?php db_ancora(@$Lo58_programa, "js_pesquisao58_programa(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_programa',
                                    11,
                                    $Io58_programa,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_programa(false);'"
                                );
                                db_input('o54_descr', 55, $Io54_anousu, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_projativ?>">
                                <?php db_ancora(@$Lo58_projativ, "js_pesquisao58_projativ(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_projativ',
                                    11,
                                    $Io58_projativ,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_projativ(false);'"
                                );
                                db_input('o55_descr', 55, $Io55_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_elemento?>">
                                <?php db_ancora(@$Lo56_elemento, "js_pesquisao58_codele(true);", $db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o56_elemento',
                                    11,
                                    $Io56_elemento,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " onchange='js_pesquisao58_codele(false);'"
                                );
                                db_input('o56_descr', 55, $Io56_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td title="<?= @$To15_recurso ?>">
                                <a id="ancoraFonteRecurso" href="#">Fonte de Recursos:</a>
                            </td>
                            <td colspan=3>
                                <?php
                                db_input('o58_codigo', 10, $Io58_codigo, true, 'hidden', $db_opcao);
                                db_input('gestao', 5, $Io15_recurso, true, 'text', 3);
                                db_input('o15_recurso', 5, $Io15_recurso, true, 'text', 3);
                                db_input('descricao', 51, $Io15_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold">Complemento:</td>
                            <td>
                                <?php
                                db_input('o15_complemento', 11, $Io15_descr, true, 'text', 3);
                                db_input('o200_descricao', 55, $Io15_descr, true, 'text', 3);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td nowrap title="<?=@$To58_localizadorgastos?>">
                                <?php
                                db_ancora(
                                    @$Lo58_localizadorgastos,
                                    "js_pesquisao58_localizadorgastos(true);",
                                    $db_opcao,
                                    "",
                                    "o58_gastosancora"
                                );
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_localizadorgastos',
                                    11,
                                    $Io58_localizadorgastos,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onchange='js_pesquisao58_localizadorgastos(false);'"
                                );
                                db_input('o11_descricao', 55, $Io11_descricao, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_concarpeculiar?>">
                                <?php
                                db_ancora(
                                    @$Lo58_concarpeculiar,
                                    "js_pesquisao58_concarpeculiar(true);",
                                    $db_opcao,
                                    "",
                                    "o58_concarpeculiarancora"
                                );
                                ?>
                            </td>
                            <td>
                                <?php
                                db_input(
                                    'o58_concarpeculiar',
                                    11,
                                    $Io58_concarpeculiar,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onchange='js_pesquisao58_concarpeculiar(false);'"
                                );
                                db_input('c58_descr', 55, $Ic58_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr style="<?=$display?>">
                            <td><label for="o58_esferaorcamentaria"><b>Esfera Orçamentária:</b></label></td>
                            <td>
                                <?php
                                $dadosEsfera = ECidade\Financeiro\Orcamento\EsferaOrcamentaria::getAll();
                                $dadosEsfera[0] = "Selecione";
                                db_select("o58_esferaorcamentaria", $dadosEsfera, true, $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$To58_valor?>">
                                <?=$Lo58_valor?>
                            </td>
                            <td>
                                <?php db_input('o58_valor', 11, $Io58_valor, true, 'text', $db_opcao, " ");?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>
    <input name="db_opcao"
           type="submit"
           id="db_opcao"
           value="<?=$labelBotao?>"
           <?=($db_botao==false?"disabled":"")?> >
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
    const lookUpRecurso = new DBLookUp($('ancoraFonteRecurso'), $('gestao'), $('descricao'), {
        'sArquivo': 'func_novosRecursos.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'aCamposAdicionais': ['o15_codigo', 'o15_complemento', 'o200_descricao', 'o15_recurso']
    });

    if (<?=$db_opcao?> == 3) {
        lookUpRecurso.desabilitar();
    }

    $('gestao').classList.remove('field-size2');
    $('descricao').classList.remove('field-size8');
    lookUpRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2], retorno[3], retorno[4], retorno[5]);
    });

    const preencheForm = (recurso, descricao, id, codComplemento, descrComplemento, subrecurso) => {
        $('o58_codigo').value = id;
        $('gestao').value = recurso;
        $('descricao').value = descricao;
        $('o15_recurso').value = subrecurso;
        $('o15_complemento').value = codComplemento;
        $('o200_descricao').value = descrComplemento;
    };

    function js_pesquisao58_orgao(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcorgao',
                                'func_orcorgao.php?funcao_js=parent.js_mostraorcorgao1|o40_orgao|o40_descr',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcorgao',
                                'func_orcorgao.php?pesquisa_chave='+ document.form1.o58_orgao.value
                                +'&funcao_js=parent.js_mostraorcorgao',
                                'Pesquisa', false);
        }
    }

    function js_mostraorcorgao(chave, erro)
    {
        document.form1.o40_descr.value = chave;
        if (erro == true) {
            document.form1.o58_orgao.focus();
            document.form1.o58_orgao.value = '';
        } else {
            document.form1.o58_unidade.value = '';
            document.form1.o41_descr.value   = '';
            document.form1.submit();
        }

    }

    function js_mostraorcorgao1(chave1,chave2)
    {
        document.form1.o58_orgao.value   = chave1;
        document.form1.o40_descr.value   = chave2;
        document.form1.o58_unidade.value = '';
        document.form1.o41_descr.value   = '';
        db_iframe_orcorgao.hide();
        document.form1.submit();
    }

    function js_pesquisao58_unidade(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcunidade',
                                'func_orcunidade.php?<?=@$filtrar;?>'
                                + 'funcao_js=parent.js_mostraorcunidade1|o41_unidade|o41_descr|nomeinst|o41_instit',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcunidade',
                                'func_orcunidade.php?<?=@$filtrar;?>pesquisa_chave=' + document.form1.o58_unidade.value
                                + '&funcao_js=parent.js_mostraorcunidade',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcunidade(chave, erro, chave2, chave3)
    {
        document.form1.o41_descr.value   = chave;
        if (chave2 != undefined) {
            document.form1.nomeinst.value    = chave2;
            document.form1.o58_instit.value  = chave3;
        }
        if (erro == true) {
            document.form1.o58_unidade.focus();
            document.form1.o58_unidade.value = '';
        }
    }

    function js_mostraorcunidade1(chave1, chave2, chave3, chave4)
    {
        document.form1.o58_unidade.value = chave1;
        document.form1.o41_descr.value   = chave2;
        document.form1.nomeinst.value    = chave3;
        document.form1.o58_instit.value  = chave4;
        db_iframe_orcunidade.hide();
    }

    function js_pesquisao58_funcao(mostra)
    {
        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcfuncao',
                                'func_orcfuncao.php?funcao_js=parent.js_mostraorcfuncao1|o52_funcao|o52_descr',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcfuncao',
                                'func_orcfuncao.php?pesquisa_chave=' + document.form1.o58_funcao.value
                                + '&funcao_js=parent.js_mostraorcfuncao',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcfuncao(chave, erro)
    {
        document.form1.o52_descr.value = chave;
        if (erro == true) {
            document.form1.o58_funcao.focus();
            document.form1.o58_funcao.value = '';
        }
    }

    function js_mostraorcfuncao1(chave1, chave2)
    {
        document.form1.o58_funcao.value = chave1;
        document.form1.o52_descr.value = chave2;
        db_iframe_orcfuncao.hide();
    }

    function js_pesquisao58_subfuncao(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcsubfuncao',
                                'func_orcsubfuncao.php?funcao_js=parent.js_mostraorcsubfuncao1|o53_subfuncao|o53_descr',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcsubfuncao',
                                'func_orcsubfuncao.php?pesquisa_chave=' + document.form1.o58_subfuncao.value
                                + '&funcao_js=parent.js_mostraorcsubfuncao',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcsubfuncao(chave, erro)
    {
        document.form1.o53_descr.value = chave;
        if (erro == true) {
            document.form1.o58_subfuncao.focus();
            document.form1.o58_subfuncao.value = '';
        }
    }

    function js_mostraorcsubfuncao1(chave1, chave2)
    {
        document.form1.o58_subfuncao.value = chave1;
        document.form1.o53_descr.value = chave2;
        db_iframe_orcsubfuncao.hide();
    }

    function js_pesquisao58_programa(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcprograma',
                                'func_orcprograma.php?funcao_js=parent.js_mostraorcprograma1|o54_programa|o54_descr',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcprograma',
                                'func_orcprograma.php?pesquisa_chave=' + document.form1.o58_programa.value
                                + '&funcao_js=parent.js_mostraorcprograma',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcprograma(chave, erro)
    {
        document.form1.o54_descr.value = chave;
        if (erro == true) {
            document.form1.o58_programa.focus();
            document.form1.o54_descr.value = '';
            document.form1.o58_programa.value = '';
        }
    }

    function js_mostraorcprograma1(chave1, chave2)
    {
        document.form1.o58_programa.value = chave1;
        document.form1.o54_descr.value = chave2;
        db_iframe_orcprograma.hide();
    }

    function js_pesquisao58_projativ(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
            'db_iframe_orcprojativ',
            'func_orcprojativ.php?insti=1&funcao_js=parent.js_mostraorcprojativ1|o55_projativ|o55_descr',
            'Pesquisa',
            true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcprojativ',
                                'func_orcprojativ.php?insti=1&pesquisa_chave=' + document.form1.o58_projativ.value
                                + '&funcao_js=parent.js_mostraorcprojativ',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcprojativ(chave, erro)
    {
        document.form1.o55_descr.value = chave;
        if (erro == true) {
            document.form1.o58_projativ.focus();
            document.form1.o58_projativ.value = '';
        }
    }

    function js_mostraorcprojativ1(chave1, chave2)
    {
        document.form1.o58_projativ.value = chave1;
        document.form1.o55_descr.value = chave2;
        db_iframe_orcprojativ.hide();
    }

    function js_mostraorcprojativ(chave, erro)
    {
        document.form1.o55_descr.value = chave;
        if (erro == true) {
            document.form1.o58_projativ.focus();
            document.form1.o58_projativ.value = '';
        }
    }

    function js_mostraorcprojativ1(chave1, chave2)
    {
        document.form1.o58_projativ.value = chave1;
        document.form1.o55_descr.value = chave2;
        db_iframe_orcprojativ.hide();
    }

    function js_pesquisao58_codele(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcelemento',
                                'func_orcelemento.php?funcao_js=parent.js_mostraorcelemento1|o56_elemento|o56_descr&analitica=1',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orcelemento',
                                'func_orcelemento.php?pesquisa_chave=' + document.form1.o56_elemento.value
                                + '&funcao_js=parent.js_mostraorcelemento&tipo_pesquisa=1&analitica=1',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorcelemento(chave, erro)
    {
        document.form1.o56_descr.value = chave;
        if (erro == true) {
            document.form1.o56_elemento.focus();
            document.form1.o56_elemento.value = '';
        }
    }

    function js_mostraorcelemento1(chave1, chave2)
    {
        document.form1.o56_elemento.value = chave1;
        document.form1.o56_descr.value = chave2;
        db_iframe_orcelemento.hide();
    }

    function js_pesquisao61_codigo(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orctiporec',
                                'func_orctiporec.php?funcao_js=parent.js_mostraorctiporec2|o15_codigo|o15_descr',
                                'Pesquisa',
                                true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                'db_iframe_orctiporec',
                                'func_orctiporec.php?pesquisa_chave=' + document.form1.o61_codigo.value
                                + '&funcao_js=parent.js_mostraorctiporec3',
                                'Pesquisa',
                                false);
        }
    }

    function js_mostraorctiporec3(chave, erro)
    {
        document.form1.o15_contra_recurso.value = chave;
        if (erro == true) {
            document.form1.o61_codigo.focus();
            document.form1.o61_codigo.value = '';
        }
    }

    function js_mostraorctiporec2(chave1, chave2)
    {
        document.form1.o61_codigo.value = chave1;
        document.form1.o15_contra_recurso.value = chave2;
        db_iframe_orctiporec.hide();
    }

    function js_pesquisao58_localizadorgastos(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                'db_iframe_ppasubtitulolocalizadorgasto',
                'func_ppasubtitulolocalizadorgasto.php?funcao_js=parent.js_mostrappasubtitulolocalizadorgasto1|o11_sequencial|o11_descricao',
                'Pesquisa',
                true);
        } else {
            if (document.form1.o58_localizadorgastos.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                    'db_iframe_ppasubtitulolocalizadorgasto',
                                    'func_ppasubtitulolocalizadorgasto.php?pesquisa_chave='
                                    + document.form1.o58_localizadorgastos.value
                                    + '&funcao_js=parent.js_mostrappasubtitulolocalizadorgasto',
                                    'Pesquisa',
                                    false);
            } else {
                document.form1.o11_descricao.value = '';
            }
        }
    }

    function js_pesquisao58_concarpeculiar(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                'db_iframe_concarpeculiar',
                'func_concarpeculiar.php?funcao_js=parent.js_mostraconcarpeculiar1|c58_sequencial|c58_descr',
                'Pesquisa',
                true);
        } else {
            if (document.form1.o58_concarpeculiar.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                                    'db_iframe_ppasubtitulolocalizadorgasto',
                                    'func_concarpeculiar.php?pesquisa_chave='
                                    + document.form1.o58_concarpeculiar.value.trim()
                                    + '&funcao_js=parent.js_mostraconcarpeculiar',
                                    'Pesquisa',
                                    false);
            } else {
                document.form1.c58_descr.value = '';
            }
        }
    }

    function js_mostraconcarpeculiar(chave, erro)
    {
        document.form1.c58_descr.value = chave;
        if (erro == true) {
            document.form1.o58_concarpeculiar.focus();
            document.form1.o58_concarpeculiar.value = '';
        }
    }

    function js_mostraconcarpeculiar1(chave1, chave2)
    {
        document.form1.o58_concarpeculiar.value =chave1;
        document.form1.c58_descr.value = chave2;
        db_iframe_concarpeculiar.hide();
    }

    function js_mostrappasubtitulolocalizadorgasto(chave, erro)
    {
        document.form1.o11_descricao.value = chave;
        if (erro == true) {
            document.form1.o58_localizadorgastos.focus();
            document.form1.o58_localizadorgastos.value = '';
        }
    }

    function js_mostrappasubtitulolocalizadorgasto1(chave1, chave2)
    {
        document.form1.o58_localizadorgastos.value = chave1;
        document.form1.o11_descricao.value = chave2;
        db_iframe_ppasubtitulolocalizadorgasto.hide();
    }

    function js_pesquisa()
    {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcdotacao',
                            'db_iframe_orcdotacao',
                            'func_orcdotacao.php?funcao_js=parent.js_preenchepesquisa|o58_coddot',
                            'Pesquisa',
                            true);
    }

    function js_preenchepesquisa(chave1)
    {
        chave = '<?=db_getsession('DB_anousu')?>';
        db_iframe_orcdotacao.hide();

        parent.iframe_orcdotacao.document.form1.o58_coddot.value = chave1;
        parent.iframe_dotacaoplanoorcamentario.getPlanos();

        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave+'&chavepesquisa1='+chave1";
        }
        ?>
    }



</script>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    ?>
    <script type="text/javascript">
        window.onload = function() {
            /*document.form1.o58_coddot.value = '';*/
            document.form1.o58_valor.focus();
        };
    </script>
    <?php
}
?>

