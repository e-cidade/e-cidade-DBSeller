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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));
$clrotulo = new rotulocampo;
$clrotulo->label("e80_data");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e80_codage");
$clrotulo->label("e50_codord");
$clrotulo->label("e50_numemp");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e60_emiss");
$clrotulo->label("e82_codord");
$clrotulo->label("e87_descgera");
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("e21_sequencial");
$clrotulo->label("e21_descricao");
$db_opcao = 1;
$displayRecursoPadrao = FONTE_RECURSO_UNIAO ? 'none' : '';
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">

    <style type="text/css">
        #fieldset_credores, #fieldset_saltes, #fieldset_retencoes, #fieldset_recursosUniao {
            width: 400px;
            text-align: center;
        }

        /*#fieldset_credores table, #fieldset_saltes table, #fieldset_retencoes table, #fieldset_recursosUniao table {*/
        /*    margin: 0 auto;*/
        /*}*/
    </style>
</head>
<body bgcolor=#CCCCCC>
<div class="container" style="width: 640px;">
    <form name='form1' id="form1">
        <fieldset>
            <legend>Relatório de Retenções</legend>
            <fieldset class="separator">
                <legend>Filtros para emissão</legend>
                <table class="form-container">
                    <tr>
                        <td title="<?php echo @$Te82_codord ?>">
                            <?php db_ancora(@$Le82_codord, "js_pesquisae82_codord(true);", $db_opcao); ?>
                        </td>
                        <td>
                            <?php
                            db_input('e82_codord', 10, $Ie82_codord, true, 'text', $db_opcao, " onchange='js_pesquisae82_codord(false);'")
                            ?>
                            <?php
                            db_ancora("<b>até:</b>", "js_pesquisae82_codord02(true);", $db_opcao);
                            ?>
                            <?php
                            db_input('e82_codord2', 10, $Ie82_codord, true, 'text', $db_opcao, "onchange='js_pesquisae82_codord02(false);'", "e82_codord02")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Data Inicial:</b>
                        </td>
                        <td>
                            <?php
                            db_inputdata("datainicial", null, null, null, true, "text", 1);
                            ?>
                            <b>Data Final:</b>
                            <?php
                            db_inputdata("datafinal", null, null, null, true, "text", 1);
                            ?>
                        </td>
                    </tr>
                </table>
                <div id="lancadorCredor"></div>
                <div id="lancadorRetencoes"></div>
                <div id="lancadorConta"></div>
                <div id="lancadorRecursos">
                    <?php  require_once modification('forms/db_frmfiltrorecursos.php')  ?>
                </div>
            </fieldset>
            <fieldset class="separator">
                <legend>
                    <strong>Outros Filtros</strong>
                </legend>
                <table class="form-container">
                    <tr>
                        <td>Quebra:</td>
                        <td>
                            <?php
                            $aQuebras = [1 => "Nenhuma", 2 => "Conta", 3 => "Credor", 4 => "Recurso", 5 => "Unidade Orçamentária", 6 => "Unidade Orçamentária e Credor"];
                            db_select("group", $aQuebras, true, 1, "style='width:10em'");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Ordem:</td>
                        <td>
                            <?php
                            $aOrdem = [1 => "Númerica", 2 => "Descrição"];
                            db_select("order", $aOrdem, true, 1, "style='width:10em'");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tipo:</td>
                        <td>
                            <?php
                            $aPagamento = ["p" => "Pagamento",
                                           "l" => "Liquidacao",
                                           "n" => "Data da Nota"];
                            db_select("pagamento", $aPagamento, true, 1, "style='width:10em'");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>OP's:</td>
                        <td>
                            <?php
                            $aOps = ["t" => "Todas", "p" => "Pagas", "np" => "Não Pagas"];
                            db_select("ops", $aOps, true, 1, "style='width:10em'");
                            ?>
                        </td>
                    </tr>
                    <tr>
                      <td class="bold">
                        <label for="orgao_numero"><?php db_ancora('Órgão:', 'buscarOrgao(true)', $db_opcao, null, 'orgao_numero_ancora'); ?></label>
                      </td>
                      <td>
                        <?php
                        $Sorgao_numero = 'Órgão';
                        db_input('orgao_numero', 14, 1, true, 'text', $db_opcao, 'onChange="buscarOrgao(false)"');
                        db_input('orgao_descricao', 44, 0, true, 'text', 3);
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="bold">
                        <label for="unidade_numero>">
                          <?php db_ancora('Unidade:', 'buscarUnidade(true)', $db_opcao, null, 'unidade_numero_ancora'); ?>
                        </label>
                      </td>
                      <td>
                        <?php
                        $Sunidade_numero = 'Unidade';
                        db_input('unidade_numero', 14, 1, true, 'text', $db_opcao, 'onChange="buscarUnidade(false)"');
                        db_input('unidade_descricao', 44, 0, true, 'text', 3);
                        db_input('instituicao_unidade', 10, 0, true, 'hidden');
                        ?>
                      </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>
        <input type='button' value='Emitir' onclick='js_emitir()'>
    </form>
</div>
</body>
</html>
<?php
db_menu();
?>
<script>
    const FONTE_RECURSO_UNIAO = <?php echo FONTE_RECURSO_UNIAO ? 'true' : 'false'?>;
    const cntLancadorCredor = document.getElementById('lancadorCredor');

    var lancadorCredor = new DBLancador("lancadorCredor");
    lancadorCredor.iGridHeight = 100;
    lancadorCredor.sTextoFieldset = 'Filtrar Credores(s)';
    lancadorCredor.setLabelAncora("Credor:");
    lancadorCredor.setNomeInstancia("lancadorCredor");
    lancadorCredor.setHabilitado(true);
    lancadorCredor.selecionarAposPesquisar = true;
    lancadorCredor.setParametrosPesquisa("func_nome.php", ["z01_numcgm", "z01_nome"]);
    lancadorCredor.show(cntLancadorCredor);

    const cntLancadorRetencoes = document.getElementById('lancadorRetencoes');
    var lancadorRetencoes = new DBLancador("lancadorRetencoes");
    lancadorRetencoes.iGridHeight = 100;
    lancadorRetencoes.sTextoFieldset = 'Filtrar Retenções(s)';
    lancadorRetencoes.setLabelAncora("Retenção:");
    lancadorRetencoes.setNomeInstancia("lancadorRetencoes");
    lancadorRetencoes.setHabilitado(true);
    lancadorRetencoes.selecionarAposPesquisar = true;
    lancadorRetencoes.setParametrosPesquisa("func_retencaotiporec.php", ["e21_sequencial", "e21_descricao"]);
    lancadorRetencoes.show(cntLancadorRetencoes);

    const cntLancadorConta = document.getElementById('lancadorConta');
    var lancadorConta = new DBLancador("lancadorConta");
    lancadorConta.iGridHeight = 100;
    lancadorConta.sTextoFieldset = 'Filtrar Conta(s)';
    lancadorConta.setLabelAncora("Conta:");
    lancadorConta.setNomeInstancia("lancadorConta");
    lancadorConta.setHabilitado(true);
    lancadorConta.selecionarAposPesquisar = true;
    lancadorConta.setParametrosPesquisa("func_saltes.php", ["k13_conta", "k13_descr"]);
    lancadorConta.show(cntLancadorConta);

    oDBToogleCredor    = new DBToogle('flsdlancadorCredor', false);
    oDBToogleRetencoes = new DBToogle('flsdlancadorRetencoes', false);
    oDBToogleConta     = new DBToogle('flsdlancadorConta', false);
    oDBToogleRecurso   = new DBToogle('fieldsetRecursos', false);

    function buscarOrgao(lMostrar) {

        var sQuerySring = 'funcao_js=parent.retornoOrgao|0|2';
        var sArquivo    = 'func_orcorgao.php';
        var sTituloTela = 'Pesquisar Órgão';

        if (!lMostrar) {
            sQuerySring = 'pesquisa_chave=' + $F('orgao_numero') + '&funcao_js=parent.retornoOrgaoChave';
        }

        js_OpenJanelaIframe('', 'db_iframe_orcorgao', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);

    }

    function retornoOrgaoChave(sDescricao, lErro) {

        $('unidade_numero').value    = '';
        $('unidade_descricao').value = '';

        retornoOrgao($F('orgao_numero'), sDescricao, lErro);
    }

    function retornoOrgao(iCodigo, sDescricao, lErro) {

        //Se o valor selecionado for diferente do atual, limpa a unidade.
        if ($('orgao_numero').value != iCodigo) {

            $('unidade_numero').value    = '';
            $('unidade_descricao').value = '';

        }
        db_iframe_orcorgao.hide();
        retorno('orgao', iCodigo, sDescricao, lErro);
    }


    function buscarUnidade(lMostrar) {

        var iOrgao = $F('orgao_numero');

        if (iOrgao == '') {

        alert("Para selecionar uma unidade, você deve primeiro informar o Órgão.");
        return false;
        }

        var sQuerySring = 'orgao=' + iOrgao + '&funcao_js=parent.retornoUnidade|2|4|0|o41_instit';
        var sArquivo    = 'func_orcunidade.php';
        var sTituloTela = 'Pesquisar Unidade';

        if (!lMostrar) {
            sQuerySring = 'pesquisa_chave=' + $F('unidade_numero') + '&orgao=' + iOrgao + '&funcao_js=parent.retornoUnidadeChave';
        }

        js_OpenJanelaIframe('', 'db_iframe_orcunidade', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
    }


    function retornoUnidadeChave(sDescricao, lErro, sNomeInstituicao, iInstituicao, iCodigoOrgao, iExercicio) {

        if (lErro) {
            iExercicio   = '';
        }
        retornoUnidade($F('unidade_numero'), sDescricao, iExercicio, iInstituicao, lErro);
    }


    function retornoUnidade(iCodigo, sDescricao, iExercicio, iInstituicaoUnidade, lErro) {


        if (lErro) {
            iExercicio = '';
            iInstituicaoUnidade = '';
        }

        db_iframe_orcunidade.hide();
        retorno('unidade', iCodigo, sDescricao, lErro);

    }

    function retorno(sCampo, iCodigo, sDescricao, lErro) {

        $(sCampo+'_numero').value = iCodigo;
        if (lErro) {
        $(sCampo+'_numero').value = '';
        }
        $(sCampo+'_descricao').value = sDescricao;
    }



    function js_emitir() {
        if ($F('datainicial') == "") {
            alert('A data do inicial do pagamento deve ser informada');
            return false;
        }

        var parametro = {
            "datainicial": $F('datainicial'),
            "datafinal": $F('datafinal'),
            "iPagamento": $F('pagamento'),
            "sOps": $F('ops'),
            "iOrdemIni": $F('e82_codord'),
            "iOrdemFim": $F('e82_codord02'),
            "order": $F('order'),
            "group": $F('group'),
            "orgao": $F("orgao_numero"),
            "unidade": $F("unidade_numero")
        };

        let credores = [];
        lancadorCredor.getRegistros().map(credor => {
            credores.push(credor.sCodigo)
        });

        let retencoes = [];
        lancadorRetencoes.getRegistros().map(retencao => {
            retencoes.push(retencao.sCodigo)
        });

        let contas = [];
        lancadorConta.getRegistros().map(conta => {
            contas.push(conta.sCodigo)
        });

        let recursos = [];
        collectionRecurso.build().map(recurso => {
            recursos.push(recurso.codigo)
        });

        parametro.contas = contas;
        parametro.credores = credores;
        parametro.recursos = recursos;
        parametro.retencoes = retencoes;

        var sFiltros = JSON.stringify(parametro);
        var sUrlRelatorio = "emp2_confereretencoes002.php?sFiltros=" + sFiltros;
        var jan = window.open(
            sUrlRelatorio,
            '',
            'width=' + (screen.availWidth - 5) +
            ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 '
        );
        jan.moveTo(0, 0);
    }

    function js_pesquisae82_codord(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_pagordem', 'func_pagordem.php?funcao_js=parent.js_mostrapagordem1|e50_codord', 'Pesquisa', true);
        } else {
            ord01 = new Number(document.form1.e82_codord.value);
            ord02 = new Number(document.form1.e82_codord02.value);
            if (ord01 > ord02 && ord01 != "" && ord02 != "") {
                alert("Selecione uma ordem menor que a segunda!");
                document.form1.e82_codord.focus();
                document.form1.e82_codord.value = '';
            }
        }
    }

    function js_mostrapagordem1(chave1) {
        document.form1.e82_codord.value = chave1;
        db_iframe_pagordem.hide();
    }

    //-----------------------------------------------------------
    //---ordem 02
    function js_pesquisae82_codord02(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_pagordem', 'func_pagordem.php?funcao_js=parent.js_mostrapagordem102|e50_codord', 'Pesquisa', true);
        } else {
            ord01 = new Number(document.form1.e82_codord.value);
            ord02 = new Number(document.form1.e82_codord02.value);
            if (ord01 > ord02 && ord02 != "" && ord01 != "") {
                alert("Selecione uma ordem maior que a primeira");
                document.form1.e82_codord02.focus();
                document.form1.e82_codord02.value = '';
            }
        }
    }

    function js_mostrapagordem102(chave1, chave2) {
        document.form1.e82_codord02.value = chave1;
        db_iframe_pagordem.hide();
    }

    function js_pesquisae60_codemp(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_empempenho', 'func_empempenho.php?funcao_js=parent.js_mostraempempenho2|e60_codemp', 'Pesquisa', true);
        } else {
            // js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho02','func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho','Pesquisa',false);
        }
    }

</script>
