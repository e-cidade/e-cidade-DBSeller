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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$daoContaCorrente = new cl_conplanosistema();
$where = "c122_tipo = 2";
$campos = "c122_sequencial as codigo, c122_descricao as nome";
$sqlContaCorrente = $daoContaCorrente->sql_query_file(null, $campos, "c122_sequencial", $where);
$rsContaCorrentes = db_query($sqlContaCorrente);
$totalLinhas = pg_num_rows($rsContaCorrentes);
$contascorrentes = array("0" => "Selecione");
for ($i = 0; $i < $totalLinhas; $i++) {

    $dado = db_utils::fieldsMemory($rsContaCorrentes, $i);
    $contascorrentes[$dado->codigo] = $dado->nome;

}
?>
<style>
    table#atributosColunas td {
        white-space: nowrap;
        border: 1px solid black;
    }

</style>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBAncora.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbcomboBox.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<style>
    .ComboRazao {
        width: 220px;
    }

    #data1, #data2 {
        width: 70px;
    }
</style>
<body>
<div class="container" style="max-height: 600px; overflow: scroll;">
    <form name="frmVisao" id="frmVisao" method="post" action="">
        <fieldset>
            <legend>Visões de Conta Corrente</legend>
            <div style="text-align: left">
                <label for="nome_visao"><b>Nome:</b></label>

                <input type='hidden' name='id_visao' id='id_visao'/>
                <input type='text' name='nome_visao' id='nome_visao' class="ComboRazao"/>
            </div>

            <div style="text-align: left">
                <input type='button' name='btnMenus' id='btnMenus' value="Selecione o item de menu"/>
                <span id="caminhoMenu"></span>
            </div>
            <fieldset class="separator" id="flsFiltros">
                <legend>Filtros</legend>
                <table style="text-align: left" border='0'>
                    <tr>
                        <td nowrap align=left>
                            <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarPeriodo">
                            <b><label for="data1">Período:</label></b>
                        </td>
                        <td nowrap align=left>
                            <?php

                            db_inputdata('data1', null, null, null, true, 'text', 1, "");
                            echo "<strong><label for=\"data2\">a</label></strong>";
                            db_inputdata('data2', null, null, null, true, 'text', 1, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarEstrutural">
                            <label for="estrut_inicial"><strong> Estrutural: </strong></label></td>
                        <td>
                            <input type='text' name='estrut_inicial' id='estrut_inicial' size='15' maxlength='15'
                                   class="ComboRazao"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarContaContabil">
                            <?php
                            db_ancora('Conta Contábil:', 'pesquisaContaContabil(true)', 1);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('codigo_conta', 10, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false)"');
                            db_input('descricao_conta', 50, 4, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarContaCorrente">
                            <b><label for="contacorrente">Conta Corrente:</label></b>
                        </td>
                        <td>
                            <?php
                            db_select("contacorrente", $contascorrentes, true, 1);
                            ?>
                        </td>
                    </tr>
                </table>
                <fieldset class="separator">
                    <legend>Opções de Visualização</legend>

                    <div style="text-align: left">
                        <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarColunasContaCorrente">
                        <b>Selecione quais atributos deseja visualizar:</b></div>
                    <table id="atributosColunas">
                    </table>
                </fieldset>

                <div id='ctnLancadorAtributos' style="margin-top: 10px; width: 600px;">
                    <div>
                        <fieldset class="separator">
                            <legend>
                                <input type="checkbox" title="Mostrar campo ao usuário" id="mostrarAtributos">
                                Atributos Selecionados
                            </legend>
                            <div id="gridAtributos">
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div id='ctnConfigGrid' style="margin-top: 10px; width: 600px;">
                    <div>
                        <fieldset class="separator">
                            <legend>
                                Colunas a serem vizualizadas
                            </legend>
                            <div id="gridConfGrid">
                            </div>
                        </fieldset>
                    </div>
                </div>
                <table>
                    <tr>
                        <td>
                            <input type="checkbox" id="imprimirSiglaAtributo">
                        </td>
                        <td>
                            <label for="imprimirSiglaAtributo">Imprimir Sigla Atributos</label>
                        </td>
                    </tr>
                </table>
                <div id='ctnLancadorDocumentos' style="margin-top: 10px; width: 600px;"></div>
                <div id='ctnLancadorContas' style="margin-top: 10px; width: 600px;"></div>
            </fieldset>
        </fieldset>

        <div style="margin-top: 10px;">
            <input type="button" id="btnSalvar" value="Salvar" onClick="">
        </div>

        <div id='ctnVisoes' style="margin-top: 10px; width: 600px;">
            <div>
                <fieldset>
                    <legend>
                        Visões Lançadas
                    </legend>
                    <div id="ctnGridVisoes">
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>
</body>
</html>
<?php
/*
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
    db_getsession("DB_instit"));
*/
?>


<script>

    var rpc = "cai4_visoescontacorrente.RPC.php";
    var dadosRequisicao = {
        codigo: '',
        nome: "",
        modulo: '',
        id_item: '',
        dadosJson: {}
    };

    var dadosFormulario = {};
    $('btnMenus').observe("click", function () {

        js_OpenJanelaIframe('CurrentWindow.corpo',
            'db_iframe_lancarmenu',
            'sys4_mostraMenus001.php',
            'Selecione o item de menu', true);
    });

    /**
     * Funcao de callback da seleção de menus
     */
    function js_CadastrarMenu(iItemPai, iModulo) {

        db_iframe_lancarmenu.hide();

        dadosRequisicao.id_item = iItemPai;
        dadosRequisicao.modulo = iModulo;

        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec': 'getNomeMenu',
                'dados': dadosRequisicao
            },
            function (response, erro) {

                if (erro) {
                    return false;
                }
                $('caminhoMenu').innerHTML = response.dados.menu;
            }
        ).execute();
    }

    $('btnSalvar').observe("click", function () {

        var periodo = $('mostrarPeriodo').checked;
        var estrutural = $('mostrarEstrutural').checked;
        var contaContabil = $('mostrarContaContabil').checked;
        var contaCorrente = $('mostrarContaCorrente').checked;
        var atributos = $('mostrarAtributos').checked;
        var colunasContaCorrente = $('mostrarColunasContaCorrente').checked;
        var eventos = $('mostrarEventos').checked;
        var contas = $('mostrarContas').checked;

        var configuracaoGrid = {
            estrutural: {label: $F('estrutural'), visible: $('chkestrutural').checked},
            descricao: {label: $F('descricao'), visible: $('chkdescricao').checked},
            conta_corrente: {label: $F('conta_corrente'), visible: $('chkconta_corrente').checked},
            saldo_anterior: {label: $F('saldo_anterior'), visible: $('chksaldo_anterior').checked},
            debitos: {label: $F('debitos'), visible: $('chkdebitos').checked},
            creditos: {label: $F('creditos'), visible: $('chkcreditos').checked},
            saldo_final: {label: $F('saldo_final'), visible: $('chksaldo_final').checked}
        };

        var configuracaoVisao =
            {
                periodo: {
                    mostrar: periodo,
                    periodo_inicial: $F('data1'),
                    periodo_final: $F('data2'),
                },
                estrutural: {
                    mostrar: estrutural,
                    valor: $F('estrut_inicial')
                },
                conta_contabil: {
                    mostrar: contaContabil,
                    codigo: $F('codigo_conta'),
                    descricao: $F('descricao_conta')
                },
                conta_corrente: {
                    mostrar: contaCorrente,
                    codigo: $F('contacorrente'),
                    atributos_visualizacao: {
                        mostrar: colunasContaCorrente,
                        colunas: colunaAtributos()
                    },
                    atributos_filtros: {
                        mostrar: atributos,
                        atributos: filtroAtributo()
                    }
                },

                eventos: {
                    mostrar: eventos,
                    itens: []
                },
                contas: {
                    mostrar: contas,
                    itens: []
                },
                configuracaoGrid: configuracaoGrid,
                mostrarSiglaAtributos: $('imprimirSiglaAtributo').checked,
            };

        var contasSelecionadas = oLancadorContas.getRegistros();
        var documentosSelecionados = oLancadorDocumentos.getRegistros();
        for (var documento of documentosSelecionados) {
            configuracaoVisao.eventos.itens.push({codigo: documento.sCodigo, descricao: documento.sDescricao});
        }
        for (var conta of contasSelecionadas) {
            configuracaoVisao.contas.itens.push({codigo: conta.sCodigo, descricao: conta.sDescricao});
        }

        dadosRequisicao.codigo = $F('id_visao');
        dadosRequisicao.nome = $F('nome_visao');
        dadosRequisicao.dadosJson = configuracaoVisao;


        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec': 'salvar',
                'dados': dadosRequisicao
            },
            function (response, erro) {

                alert(response.mensagem);
                if (erro) {
                    return false;
                }
                $('frmVisao').reset();
                $('caminhoMenu').innerHTML = "";

                getVisoesContacorrente();
                limparFormulario();
            }
        ).execute();


    });

    function getVisoesContacorrente() {


        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec': 'getVisoes',
            },
            function (response, erro) {

                if (erro) {
                    return false;
                }

                gridVisoes.clearAll(true);

                for (var visao of response.dados) {

                    var registro = [
                        visao.nome,
                        visao.menu,
                        '<input type="button" value="A" onclick="js_alterar(' + visao.codigo + ')">' +
                        '<input type="button" value="E" onclick="js_remover(' + visao.codigo + ')">'

                    ];
                    gridVisoes.addRow(registro);
                }
                gridVisoes.renderRows();
            }
        ).execute();
    }

    function limparFormulario() {

        var elementos = document.querySelectorAll('input');
        Array.from(elementos).forEach(function (elemento) {

            if (elemento.type === 'text') {
                // elemento.value = '';
            }

            if (elemento.type === 'checkbox') {
                elemento.checked = false;
            }
        });

        $('contacorrente').value = '0';
        gridContaCorrenteAtributos.clearAll(true);
        oLancadorDocumentos.clearAll();
        oLancadorContas.clearAll();
    }


    const CAMINHO_MENSAGEM_TELA = "financeiro.contabilidade.con2_razaocontas001.";

    var rpc = "cons2_consultacontacorrente.RPC.php";
    var alturaGrid = "75px";

    var toogleFiltros = new DBToogle('flsFiltros', false);
    var gridVisoes = new DBGrid('gridVisoes');
    gridVisoes.nameInstance = 'gridVisoes';
    gridVisoes.clearAll(true);

    var headers = ["Nome", "Menu", "Ações"];
    gridVisoes.setCellAlign(["left", "left", "center"]);
    gridVisoes.setHeader(headers);
    gridVisoes.setHeight(150);
    gridVisoes.show($('ctnGridVisoes'));


    function getAtributosDoContaCorrente(colunas, valorPadraoAtributos) {

        var request = {
            exec: "getAtributos",
            conta_corrente: $F('contacorrente')
        };
        new AjaxRequest(rpc, request, function (response, erro) {

            if (erro) {
                return;
            }

            colunas = colunas || [];
            valorPadraoAtributos = valorPadraoAtributos || [];

            gridContaCorrenteAtributos.clearAll(true);
            $('atributosColunas').innerHTML = '';
            for (var dados of response.atributos) {
                var valorAtributo = '';
                for (var valorInformado of valorPadraoAtributos) {
                    if (valorInformado.sigla == dados.sigla) {
                        valorAtributo = valorInformado.valor;
                        break;
                    }
                }

                var linha = [
                    dados.sigla,
                    dados.descricao,
                    "<input type='text' value= '" + valorAtributo + "' style='width: 100%;' id='valor" + dados.sigla + "'>"
                ];
                gridContaCorrenteAtributos.addRow(linha);
            }
            gridContaCorrenteAtributos.renderRows();
            /* adiciona o hint nas linhas */
            response.atributos.each(
                function (atributo, linha) {

                    gridContaCorrenteAtributos.setHint(linha, 1, atributo.ajuda);
                }
            );

            /**
             * Mostra as colunas do relatorio
             */
            var itensImpressos = 0;
            for (var atributo of response.atributos) {

                if (itensImpressos == 0) {
                    var linha = document.createElement("tr");
                    $('atributosColunas').appendChild(linha);
                }
                var checked = ' checked ';
                if (!empty(colunas)) {
                    checked = colunas.in_array(atributo.sigla) ? ' checked ' : '';
                }
                var coluna = document.createElement("td");
                coluna.style.whiteSpace = "nowrap";
                coluna.noWrap = true;
                var checkbox = document.createElement("input");
                checkbox.id = 'atributo_coluna_' + atributo.sigla;
                checkbox.type = 'checkbox';
                checkbox.checked = checked;
                checkbox.className = 'coluna_atributo';
                checkbox.value = atributo.sigla;
                var label = document.createElement("label");
                label.htmlFor = checkbox.id;

                label.innerHTML = atributo.sigla;
                coluna.appendChild(checkbox);
                coluna.appendChild(label);
                linha.appendChild(coluna);
                itensImpressos++;
                if (itensImpressos > 4) {
                    itensImpressos = 0;
                }
            }
        }).setMessage('Aguarde, pesquisando atributos.').execute();
    }

    $('contacorrente').observe('change', function () {
        getAtributosDoContaCorrente();
    });

    var gridContaCorrenteAtributos = new DBGrid('gridAtributos');
    gridContaCorrenteAtributos.nameInstance = 'gridContaCorrenteAtributos';
    var headers = ["Sigla", "Descrição", "valor"];
    gridContaCorrenteAtributos.setCellWidth(["20%", "60%", "20%"]);
    gridContaCorrenteAtributos.setHeader(headers);
    gridContaCorrenteAtributos.setHeight(100);
    gridContaCorrenteAtributos.show($('gridAtributos'));

    var gridConfigGrid = new DBGrid('gridConfigGrid');
    gridConfigGrid.nameInstance = 'gridConfigGrid';

    var headersConfig = ["", "Coluna grid", "Label na Consulta"];
    gridConfigGrid.setCellWidth(["30px", "200px", "300px"]);
    gridConfigGrid.setHeader(headersConfig);
    gridConfigGrid.setHeight(100);
    gridConfigGrid.show($('gridConfGrid'));


    preencherGridColunas();

    function filtroAtributo() {
        var retorno = [];
        var linhas = gridContaCorrenteAtributos.aRows;
        if (linhas.length > 0) {
            for (var elemento of linhas) {

                var sigla = elemento.aCells[0].getValue()
                var valor = elemento.aCells[2].getValue();
                if (valor != '') {
                    retorno.push({atributo: sigla, valor: valor});
                }
            }
        }
        return retorno;
    }


    /**
     * Cria o lançador para os Documentos
     */
    function js_criarLancadorDocumentos() {

        oLancadorDocumentos = new DBLancador("oLancadorDocumentos");
        oLancadorDocumentos.setNomeInstancia("oLancadorDocumentos");
        oLancadorDocumentos.setLabelAncora("Documentos: ");
        oLancadorDocumentos.setTextoFieldset("<input type='checkbox' title='Mostrar campo ao usuário' id='mostrarEventos'>Seleção de Eventos");
        oLancadorDocumentos.setParametrosPesquisa("func_conhistdoc.php", ['c53_coddoc', 'c53_descr']);
        oLancadorDocumentos.setGridHeight(alturaGrid);
        oLancadorDocumentos.setTituloJanela("Pesquisar Documentos");
        oLancadorDocumentos.show($("ctnLancadorDocumentos"));
    }

    js_criarLancadorDocumentos();

    /**
     * Cria o lançador para as contas
     */
    function js_criarLancadorContas() {
        oLancadorContas = new DBLancador("oLancadorContas");
        oLancadorContas.setNomeInstancia("oLancadorContas");
        oLancadorContas.setLabelAncora("Contas: ");
        oLancadorContas.setTextoFieldset("<input type='checkbox' title='Mostrar campo ao usuário' id='mostrarContas'> Seleção de Contas Contábeis");
        oLancadorContas.setParametrosPesquisa("func_conplanoexe.php", ['c62_reduz', 'c60_descr']);
        oLancadorContas.setGridHeight(alturaGrid);
        oLancadorContas.setTituloJanela("Pesquisar Contas");
        oLancadorContas.show($("ctnLancadorContas"));
    }

    js_criarLancadorContas();


    function colunaAtributos() {
        var colunas = [];
        var listaCheckboxes = $$('.coluna_atributo');
        for (checkbox of listaCheckboxes) {

            if (!checkbox.checked) {
                continue;
            }
            colunas.push(checkbox.value);
        }
        return colunas;
    }

    $('flsdoLancadorDocumentos').className = 'separator';
    $('flsdoLancadorContas').className = 'separator';


    function consultaContaCorrente() {

        if (document.querySelector('#codigo_conta').value === '') {
            return false;
        }

        AjaxRequest.create(
            rpc,
            {'exec': 'getContaCorrentePorContaContabil', 'codigo_conta': document.querySelector('#codigo_conta').value},
            function (response, erro) {

                if (erro) {

                    alert(response.message);
                    return false;
                }

                document.querySelector('#contacorrente').value = response.codigo_conta_corrente;
                getAtributosDoContaCorrente();
            }
        ).execute();
    }

    function pesquisaContaContabil(mostrar) {

        if (document.querySelector('#codigo_conta').value === '' && !mostrar) {
            document.querySelector('#descricao_conta').value = '';
            return false;
        }

        var caminhoLookup = 'func_conplanosistemacontacorrente.php?funcao_js=parent.preencheContaContabil|c60_codcon|c60_descr';
        if (!mostrar) {
            caminhoLookup = 'func_conplanosistemacontacorrente.php?pesquisa_chave=' + $F('codigo_conta') + '&funcao_js=parent.completaContaContabil';
        }


        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conplano', caminhoLookup, 'Pesquisar Conta Contábil', mostrar);
    }

    function preencheContaContabil(codigoConta, descricaoConta) {

        document.querySelector('#codigo_conta').value = codigoConta;
        document.querySelector('#descricao_conta').value = descricaoConta;
        db_iframe_conplano.hide();
        consultaContaCorrente();
    }

    function completaContaContabil(descricaoConta, erro) {

        document.querySelector('#descricao_conta').value = descricaoConta;
        if (erro) {
            document.querySelector('#codigo_conta').value = '';
        }
        consultaContaCorrente();
    }

    function js_remover(id) {

        if (!confirm('A visão ' + $F('nome_visao') + ' será removida do sistema. \nConfirma a remoção?')) {
            return;
        }
        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec': 'excluir',
                dados: {
                    'codigo': id
                }
            },
            function (response, erro) {

                alert(response.mensagem);
                if (erro) {
                    return;
                }
                getVisoesContacorrente()
            }).setMessage("Aguarde, removendo os dados da visão").execute();
    }

    function js_alterar(id) {

        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec': 'getVisaoPorCodigo',
                dados: {'codigo': id}
            },
            function (response, erro) {

                if (erro) {
                    alert(response.message);
                    return;
                }
                preencherCampos(response.dados);
                preencherGridColunas();

            }).setMessage("Aguarde, pesquisando os dados da visão").execute();

    }

    /**
     *
     * @param dados
     */
    function preencherCampos(dados) {

        toogleFiltros.show(true);
        dadosFormulario = dados;

        $('mostrarPeriodo').checked = dadosFormulario.dadosJson.periodo.mostrar;
        $('data1').value = dadosFormulario.dadosJson.periodo.periodo_inicial;
        $('data2').value = dadosFormulario.dadosJson.periodo.periodo_final;

        $('mostrarEstrutural').checked = dadosFormulario.dadosJson.estrutural.mostrar;
        $('estrut_inicial').value = dadosFormulario.dadosJson.estrutural.valor;

        $('mostrarContaContabil').checked = dadosFormulario.dadosJson.conta_contabil.mostrar;
        $('codigo_conta').value = dadosFormulario.dadosJson.conta_contabil.codigo;
        $('descricao_conta').value = dadosFormulario.dadosJson.conta_contabil.descricao;

        $('mostrarContaCorrente').checked = dadosFormulario.dadosJson.conta_corrente.mostrar;
        $('contacorrente').value = dadosFormulario.dadosJson.conta_corrente.codigo;

        $('mostrarColunasContaCorrente').checked = dadosFormulario.dadosJson.conta_corrente.atributos_visualizacao.mostrar;
        $('mostrarAtributos').checked = dadosFormulario.dadosJson.conta_corrente.atributos_filtros.mostrar;
        getAtributosDoContaCorrente(dadosFormulario.dadosJson.conta_corrente.atributos_visualizacao.colunas,
            dadosFormulario.dadosJson.conta_corrente.atributos_filtros.atributos);

        $('mostrarEventos').checked = dadosFormulario.dadosJson.eventos.mostrar;
        $('mostrarContas').checked = dadosFormulario.dadosJson.contas.mostrar;

        var eventos = [];
        for (var evento of dadosFormulario.dadosJson.eventos.itens) {

            eventos.push([evento.codigo, evento.descricao]);
        }
        oLancadorDocumentos.carregarRegistros(eventos);
        var contas = [];

        for (var conta of dadosFormulario.dadosJson.contas.itens) {
            contas.push([conta.codigo, conta.descricao]);
        }

        oLancadorContas.carregarRegistros(contas);
        $('nome_visao').value = dadosFormulario.nome;
        $('caminhoMenu').innerHTML = dadosFormulario.menu;
        $('id_visao').value = dadosFormulario.codigo;

    }

    getVisoesContacorrente();

    /**
     *
     */
    function getDadosColunaVisao(identificador) {
        if (empty(dadosFormulario)) {
            return false;
        }

        if (!empty(dadosFormulario.dadosJson.configuracaoGrid[identificador])) {
            return dadosFormulario.dadosJson.configuracaoGrid[identificador];
        }
    }

    function preencherGridColunas() {

        gridConfigGrid.clearAll();
        var linhas = [
            {nome: "estrutural", label: "Conta"},
            {nome: "descricao", label: "Descrição"},
            {nome: "conta_corrente", label: "Conta Corrente"},
            {nome: "saldo_anterior", label: "Saldo Anterior"},
            {nome: "debitos", label: "Débitos"},
            {nome: "creditos", label: "Créditos"},
            {nome: "saldo_final", label: "Saldo Final"}
        ];

        linhas.each(
            function (oLinha, i) {

                var linhaMarcada = ' checked ';
                var labelPadrao = oLinha.label;
                var linhaConfigurada = getDadosColunaVisao(oLinha.nome);
                if (!empty(linhaConfigurada)) {
                    linhaMarcada = linhaConfigurada.visible ? ' checked ' : '';
                    labelPadrao = linhaConfigurada.label;
                }
                var sCheck = "<input type='checkbox' id='chk" + oLinha.nome + "' value='" + oLinha.nome + "'  " + linhaMarcada + "/>";
                var sInput = "<input type='text'     id='" + oLinha.nome + "'    value='" + labelPadrao + "' />";
                gridConfigGrid.addRow([sCheck, oLinha.label, sInput]);
            });

        gridConfigGrid.renderRows();
    }
</script>
