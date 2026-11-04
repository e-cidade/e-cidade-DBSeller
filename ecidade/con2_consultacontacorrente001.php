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
$where  = "c122_tipo = 2";
$campos = "c122_sequencial as codigo, c122_descricao as nome";
$sqlContaCorrente = $daoContaCorrente->sql_query_file(null, $campos, "c122_sequencial", $where);
$rsContaCorrentes = db_query($sqlContaCorrente);
$totalLinhas      = pg_num_rows($rsContaCorrentes);
$contascorrentes = array("0" => "Selecione");
for ($i = 0; $i < $totalLinhas; $i++) {

    $dado = db_utils::fieldsMemory($rsContaCorrentes, $i);
    $contascorrentes[$dado->codigo] = $dado->nome;

}
$estiloPadrao = !empty($_GET['codigo_visao']) ? ' display: none;': "";

?>
<style>
    table#atributosColunas  td {
        white-space: nowrap;
        border:1px solid black;
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
    <script language="JavaScript" type="text/javascript" src="scripts/classes/infoLancamentoContabil.classe.js"></script>
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
    <form name="form1" method="post" action="">
        <fieldset>
            <legend id="lblNomeVisao">Consulta de Conta Corrente</legend>
            <fieldset class="separator">
                <legend>Filtros</legend>
                <table style="text-align: left; " border='0'>
                    <tr id="mostrarPeriodo"  class="campos_consulta" style= "<?=$estiloPadrao?>">
                        <td nowrap align=left>
                            <b><label for="data1">Período:</label></b>
                        </td>
                        <td nowrap align=left>
                            <?php
                            $dia = '01';
                            $mes = '01';
                            $ano = date("Y", db_getsession("DB_datausu"));
                            $dia2 = date("d", db_getsession("DB_datausu"));
                            $mes2 = date("m", db_getsession("DB_datausu"));
                            $ano2 = date("Y", db_getsession("DB_datausu"));
                            db_inputdata('data1', @$dia, @$mes, @$ano, true, 'text', 1, "");
                            echo "<strong><label for=\"data2\">a</label></strong>";
                            db_inputdata('data2', @$dia2, @$mes2, @$ano2, true, 'text', 1, "");
                            ?>
                        </td>
                    </tr>


                    <tr id="mostrarFonteDeDados" style= "<?=$estiloPadrao?>">
                        <td align="left"><label for="fonte_de_dados"><strong> Fonte de Dados: </strong></label></td>
                        <td>
                            <?php
                              $aFonteDeDados = array( "1" => "Conta Corrente",
                                                      "2" => "MSC" );
                              db_select("fonte_de_dados", $aFonteDeDados, true, 1);
                            ?>
                        </td>
                    </tr>


                    <tr id="mostrarEstrutural" style= "<?=$estiloPadrao?>">
                        <td align="left"><label for="estrut_inicial"><strong> Estrutural: </strong></label></td>
                        <td>
                            <input type='text' name='estrut_inicial' id='estrut_inicial' size='15' maxlength='15' class="ComboRazao" />
                        </td>
                    </tr>
                    <tr  id="mostrarContaContabil"  class="campos_consulta" style= "<?=$estiloPadrao?>">
                        <td class="bold">
                            <?php
                            db_ancora('Conta Contábil:', 'pesquisaContaContabil(true, 1)', 1);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('codigo_conta', 5, 1, true, 'hidden', 3);
                            db_input('reduzido', 5, 1, true, 'hidden', 3);
                            $Sestrutural_conta = 'Estrutural';
                            db_input('estrutural_conta', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 1)"');
                            db_input('descricao_conta', 54, 4, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr id="mostrarContaCorrente"  class="campos_consulta" style= "<?=$estiloPadrao?>">
                        <td>
                            <b><label for="contacorrente">Conta Corrente:</label></b>
                        </td>
                        <td>
                            <?php
                            db_select("contacorrente", $contascorrentes, true, 1);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="separator campos_consulta" id="mostrarColunasContaCorrente" style= "<?=$estiloPadrao?>">
                <legend>Opções de Visualização</legend>
                <div style="text-align: left"><b>Selecione quais atributos deseja visualizar:</b></div>
                <table id="atributosColunas">
                </table>
            </fieldset>
            <div id='ctnLancadorAtributos' style="margin-top: 10px; width: 600px;<?=$estiloPadrao;?>">
                <div>
                    <fieldset class="separator campos_consulta" id="mostrarAtributos">
                        <legend>Atributos Selecionados</legend>
                        <div id="gridAtributos">
                        </div>
                    </fieldset>
                </div>
            </div>
            <div id='ctnLancadorDocumentos'  class="campos_consulta" style="margin-top: 10px; width: 600px;<?=$estiloPadrao;?>"></div>
            <div id="ctnLancadorContas"  style="margin-top: 10px; width: 650px;">
                <fieldset id="fieldsetContasContabeis">
                    <legend class="bold" id="legendContasContabeis">Seleção de Contas Contábeis</legend>
                    <table>
                        <tr>
                            <td>
                                <?php
                                db_ancora('Conta Contábil:', 'pesquisaContaContabil(true, 2)', 1);
                                db_input('lancador_codigo_conta', 3, 1, true, 'hidden', 3);
                                db_input('lancador_estrutural_conta', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 2)"');
                                db_input('lancador_descricao_conta', 35, 1, true, 'text', 3);
                                ?>
                                <input type="button" value="Adicionar" onclick="adicionarContaContabil()"/>
                            </td>
                        </tr>
                    </table>
                    <div id="ctnGridLancadorContasContabeis" style="width: 98%"></div>
                </fieldset>
            </div>
        </fieldset>
        <div style="margin-top: 10px;">
            <input type="button" id="pesquisa" value="Pesquisar" onClick="js_imprimir(1)">
            <input type="button" id="emitirCsv" value="Emitir CSV" onClick="js_imprimir(2)">
        </div>
    </form>
</div>
</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>
<script>

    const CAMINHO_MENSAGEM_TELA = "financeiro.contabilidade.con2_razaocontas001.";

    var codigoVisao = <?=(!empty($_GET['codigo_visao']) ? $_GET['codigo_visao']: "''")?>;

    var rpc = "cons2_consultacontacorrente.RPC.php";
    var alturaGrid = "75px";
    dadosConsulta = [];

    $('fonte_de_dados').observe('change',  () => {

        let iOpcao = $F("fonte_de_dados");

        gridContaCorrenteAtributos.clearAll(true);
        $("codigo_conta").value = "";
        $("reduzido").value = "";
        $("estrutural_conta").value = "";
        $("descricao_conta").value = "";

        switch(iOpcao) {

            case "1":
                $("mostrarContaCorrente").setAttribute("style", "display:table-row");
            break;

            case "2":
                $("mostrarContaCorrente").setAttribute("style", "display:none");
            break;

        }

    });


    function getAtributosDoContaCorrente(colunas, valorPadraoAtributos) {

        var request = {
            exec:           "getAtributos",
            conta_corrente: $F('contacorrente'),
            fonte_de_dados: $F("fonte_de_dados"),
            codigo_conta: $F("codigo_conta"),
            reduzido : $F("reduzido")
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
    gridContaCorrenteAtributos.setHeight(120);
    gridContaCorrenteAtributos.show($('gridAtributos'));

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
        oLancadorDocumentos.setTextoFieldset("Seleção de Eventos");
        oLancadorDocumentos.setParametrosPesquisa("func_conhistdoc.php", ['c53_coddoc', 'c53_descr']);
        oLancadorDocumentos.setGridHeight(alturaGrid);
        oLancadorDocumentos.setTituloJanela("Pesquisar Documentos");
        oLancadorDocumentos.show($("ctnLancadorDocumentos"));
    }

    js_criarLancadorDocumentos();


    function colunaAtributos()
    {
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

    /**
     * Retorno do relatorio
     * @param oAjax
     * @returns {boolean}
     */
    function retornoRelatorio(oAjax) {

        js_removeObj('msgbox');

        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.erro) {
            alert(oRetorno.message.urlDecode());
            return false;
        }

        var conf = oRetorno.configuracaoVisao.configuracaoGrid;

        var configuracaoConsulta = {
                                        estrutural:      { label: conf.estrutural.label, visible: conf.estrutural.visible },
                                        descricao:       { label: conf.descricao.label,      visible: conf.descricao.visible },
                                        conta_corrente:  { label: conf.conta_corrente.label, visible: conf.conta_corrente.visible },
                                        saldo_anterior:  { label: conf.saldo_anterior.label, visible: conf.saldo_anterior.visible },
                                        debitos:         { label: conf.debitos.label,        visible: conf.debitos.visible },
                                        creditos:        { label: conf.creditos.label,       visible: conf.creditos.visible },
                                        saldo_final:     { label: conf.saldo_final.label,    visible: conf.saldo_final.visible }
                                   };

        if (oRetorno.tipo == 2) {

            var oDownload = new DBDownload();
            oDownload.setHelpMessage('Clique no link abaixo para fazer download do relatório.');
            oDownload.addFile(oRetorno.dados, 'Lancamentos de Conta Corrente');
            oDownload.show();
        } else {

            var tamanhoDaJanela = window.innerWidth - 30;
            wndCOnsutlaContaCorrente = new windowAux('wndRetornoPesquisa', 'Consulta de Conta Corrente', tamanhoDaJanela, '600');
            var content = "<div style='width: 99%'>";
            content += "  <fieldset>";
            content += "  <legend> Lista de Contas";
            content += "  </legend>";
            content += "  <div id='ctnGrid'>";
            content += "  </div>";
            content += "  </fieldset>";
            content += "</div>";

            wndCOnsutlaContaCorrente.setContent(content);

            if ($F("fonte_de_dados") == 1) { // fonte de dados conta corrente

                var textoAjuda = 'Consulta dos dados do conta corrente '+ $('contacorrente').options[$('contacorrente').selectedIndex].innerHTML;
            } else {  // fonte de dados MSC
                var textoAjuda = 'Consulta dos dados do conta corrente na MSC ';
            }

            if (temVisaoCadastrada) {
                textoAjuda = dadosFormulario.nome;
            }

            var oMessageBoard = new DBMessageBoard('msgboard1',
                textoAjuda,
                'Período:<b>'+$F('data1') + "</b> a <b>"+ $F('data2')+ "</b>",
                wndCOnsutlaContaCorrente.getContentContainer());
            oMessageBoard.show();
            wndCOnsutlaContaCorrente.setShutDownFunction(function () {
                wndCOnsutlaContaCorrente.destroy();

            });

            wndCOnsutlaContaCorrente.show();
            var gridContaCorrente = new DBGrid('gridTeste');
            gridContaCorrente.nameInstance = 'gridContaCorrente';
            gridContaCorrente.clearAll(true);


            var tamanhos = [
                tamanhoDaJanela *0.10,
                tamanhoDaJanela *0.25,
                tamanhoDaJanela *0.25,
                tamanhoDaJanela *0.08,
                tamanhoDaJanela *0.08,
                tamanhoDaJanela *0.08,
                tamanhoDaJanela *0.08,
            ];

            var headers = [];

            headers.push(configuracaoConsulta.estrutural.label);
            headers.push(configuracaoConsulta.descricao.label);
            headers.push(configuracaoConsulta.conta_corrente.label);
            headers.push(configuracaoConsulta.saldo_anterior.label);
            headers.push(configuracaoConsulta.debitos.label);
            headers.push(configuracaoConsulta.creditos.label);
            headers.push(configuracaoConsulta.saldo_final.label);

            gridContaCorrente.hasTotalizador = true;
            gridContaCorrente.setHeader(headers);
            gridContaCorrente.setCellWidth(tamanhos);
            gridContaCorrente.setCellAlign( ["left", "left", "left", "right", "right", "right", "right"]);
            gridContaCorrente.setHeight(400);

            gridContaCorrente.aHeaders[0].lDisplayed = configuracaoConsulta.estrutural.visible;
            gridContaCorrente.aHeaders[1].lDisplayed = configuracaoConsulta.descricao.visible;
            gridContaCorrente.aHeaders[2].lDisplayed = configuracaoConsulta.conta_corrente.visible;
            gridContaCorrente.aHeaders[3].lDisplayed = configuracaoConsulta.saldo_anterior.visible;
            gridContaCorrente.aHeaders[4].lDisplayed = configuracaoConsulta.debitos.visible;
            gridContaCorrente.aHeaders[5].lDisplayed = configuracaoConsulta.creditos.visible;
            gridContaCorrente.aHeaders[6].lDisplayed = configuracaoConsulta.saldo_final.visible;

            gridContaCorrente.show($('ctnGrid'));
            var totais = {
                saldo_anterior: 0,
                valor_debito: 0,
                valor_credito: 0,
                saldo_final: 0
            }

            dadosConsulta = oRetorno.dados.registros;
            for (var dados of oRetorno.dados.registros) {

                if (configuracaoConsulta.estrutural.visible && configuracaoConsulta.descricao.visible) {
                    var linha = [
                        "<b>" + dados.estrutural + "</b>",
                        "<b>" + dados.nome_conta + "</b>",
                        '',
                        '',
                        '',
                        '',
                        ''
                    ];
                    gridContaCorrente.addRow(linha);
                }
                for (var movimentacoes of dados.movimentacoes) {

                    var stringMovimentacao = "<span  style='cursor: pointer' ondblclick=\"detalhamentoHash('"+dados.estrutural+"', '"+movimentacoes.conta_corrente+"' , '"+movimentacoes.documento+"'   )\">";
                    stringMovimentacao +=movimentacoes.conta_corrente+"</span>";
                    var linhaMovimentacoes = new Array();
                    linhaMovimentacoes.push("");
                    linhaMovimentacoes.push("");

                    linhaMovimentacoes.push(stringMovimentacao);
                    // linhaMovimentacoes.push(movimentacoes.documento);
                    linhaMovimentacoes.push(js_formatar(movimentacoes.saldo_anterior, 'f') + " <b>" + movimentacoes.natureza_anterior + "</b>");
                    linhaMovimentacoes.push(js_formatar(movimentacoes.valor_debito, 'f'));
                    linhaMovimentacoes.push(js_formatar(movimentacoes.valor_credito, 'f'));
                    linhaMovimentacoes.push(js_formatar(movimentacoes.saldo_final, 'f') + "  <b>" + movimentacoes.natureza_final + "</b>");
                    gridContaCorrente.addRow(linhaMovimentacoes);

                    totais.saldo_anterior += movimentacoes.natureza_anterior == 'D' ? movimentacoes.saldo_anterior * -1 : movimentacoes.saldo_anterior;
                    totais.valor_debito   += movimentacoes.valor_debito;
                    totais.valor_credito  += movimentacoes.valor_credito;
                    totais.saldo_final    += movimentacoes.natureza_final == 'D' ?movimentacoes.saldo_final * -1 : movimentacoes.saldo_final;
                }
            }
            $('TotalForCol3').innerHTML = js_formatar(Math.abs(totais.saldo_anterior), 'f') + '<b>'+(totais.saldo_anterior < 0 ? 'D' : 'C')+ '</b>';
            $('TotalForCol4').innerHTML = js_formatar((totais.valor_debito), 'f');
            $('TotalForCol5').innerHTML = js_formatar(Math.abs(totais.valor_credito), 'f');
            $('TotalForCol6').innerHTML = js_formatar(Math.abs(totais.saldo_final), 'f') + '<b>'+(totais.saldo_final < 0 ? 'D' : 'C')+ '</b>';
            gridContaCorrente.renderRows();
        }
    }

    function js_imprimir(tipo) {

        filtroAtributos = filtroAtributo();
        colunas = colunaAtributos();

        if (colunas.length == 0 ) {
            alert('Nenhum Atributo para visualização foi informado.<br> Ao menos um atributo deve ser selecionado.');
            return
        }
        let iContaCorrente = $F('contacorrente');
        if (empty($F('contacorrente'))) {


            if ( $F("fonte_de_dados") == 1) {


                alert('Campo conta Corrente deve ser informado.');
                return
            } else {

                iContaCorrente = 1;
               // $('contacorrente').value = 1;
            }

        }





        js_divCarregando('Aguarde, gerando relatório...', 'msgbox');

        var oParam = new Object();
        var filtros = new Object();

        var data1 = document.form1.data1_ano.value + "-" + document.form1.data1_mes.value + "-" + document.form1.data1_dia.value;
        var data2 = document.form1.data2_ano.value + "-" + document.form1.data2_mes.value + "-" + document.form1.data2_dia.value;

        var documentosSelecionados = oLancadorDocumentos.getRegistros();
        var documentos = [];
        var contas = [];
        for (var documento of documentosSelecionados) {
            documentos.push(documento.sCodigo);
        }

        gridLancadorContas.aRows.each(
            function(row) {
                contas.push(row.aCells[0].getValue());
            }
        );




        var estrut_inicial = $F('estrut_inicial');

        if (data1.valueOf() > data2.valueOf()) {

            alert(_M(CAMINHO_MENSAGEM_TELA + "data_inicial_maior_final"));
            js_removeObj('msgbox');
            return false;
        }

        var data1 = js_formatar($F("data1"), 'd');
        var data2 = js_formatar($F("data2"), 'd');

        filtros.data_inicial = data1;
        filtros.data_final = data2;
        filtros.conta_corrente = iContaCorrente;//$F('contacorrente');
        filtros.atributos = filtroAtributos;
        filtros.documentos = documentos;
        filtros.contas = contas;
        filtros.colunas = colunas;
        filtros.estrutural_inicial = estrut_inicial;
        filtros.conta_contabil = document.querySelector('#codigo_conta').value;
        filtros.reduzido = document.querySelector('#reduzido').value;
        oParam.filtros = filtros;
        oParam.codigoVisao = codigoVisao;

        oParam.exec = 'gerarRelatorio';
        oParam.fonte_de_dados = $F("fonte_de_dados");
        oParam.tipo = tipo;
        var oAjax = new Ajax.Request(
            rpc,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: retornoRelatorio
            }
        );
    }


    function pesquisarAtributo() {
        var siglaAtributo = document.getElementById('codigoAtributos').value;
        if (siglaAtributo && siglaAtributo !== '') {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_pesquisa_atributos', 'func_atributoscontacorrente.php?siglaAtributo=' + siglaAtributo + '&funcao_js=parent.preencheValorAtributo|*', 'Pesquisa Atributos', true);
        }
    }

    function preencheValorAtributo(valor) {
        var atributoValor = document.getElementById('valorAtributo');
        var buttonAdicionarAtributo = document.getElementById('adicionarAtributo');
        if (valor && valor != '') {
            atributoValor.value = valor;
            buttonAdicionarAtributo.click();
            atributoValor.value = '';
        }

        db_iframe_pesquisa_atributos.hide();
    }

    var toogleDocumentos = new DBToogle('flsdoLancadorDocumentos', false);
    var toogleContas = new DBToogle('fieldsetContasContabeis', true);
    $('flsdoLancadorDocumentos').className = 'separator';
    $('fieldsetContasContabeis').className = 'separator';

    function consultaContaCorrente() {

        if (document.querySelector('#codigo_conta').value === '') {
            return false;
        }

        AjaxRequest.create(
            rpc,
            {'exec' : 'getContaCorrentePorContaContabil', 'codigo_conta' : document.querySelector('#codigo_conta').value},
            function (response, erro) {

                if (erro) {

                    if ( $F("fonte_de_dados") == "1" ) { // mante a validacao somente se for fonte de dados por CC

                        alert(response.message);
                        return false;
                    }
                }

                document.querySelector('#contacorrente').value = response.codigo_conta_corrente;
                getAtributosDoContaCorrente();
            }
        ).execute();
    }

    function pesquisaContaContabil(mostrar, tipoPesquisa) {

        if (document.querySelector('#estrutural_conta').value === '' && !mostrar && tipoPesquisa === 1) {
            document.querySelector('#descricao_conta').value = '';
            document.querySelector('#codigo_conta').value = '';
            document.querySelector('#reduzido').value = '';
            return false;
        }

        if (document.querySelector('#lancador_estrutural_conta').value === '' && !mostrar && tipoPesquisa === 2) {
            document.querySelector('#lancador_descricao_conta').value = '';
            document.querySelector('#lancador_codigo_conta').value = '';
            return false;
        }

        var funcaoPreenche = 'preencheContaContabil';
        var funcaoCompleta = 'completaContaContabil';
        var valorPesquisaEstrutural = $F('estrutural_conta');
        var campoRetorno = 'c60_codcon';

        if (tipoPesquisa === 2) {
            funcaoPreenche = 'preencheContaContabilLancador';
            funcaoCompleta = 'completaContaContabilLancador';
            valorPesquisaEstrutural = $F('lancador_estrutural_conta');
            campoRetorno = 'c61_reduz';
        }
        var sMatriz = "lMatriz=0&";
        if ($F("fonte_de_dados") == '2') {
            sMatriz = "lMatriz=1&";
        }
        var caminhoLookup = 'func_conplanosistemacontacorrente.php?'+sMatriz+'funcao_js=parent.'+funcaoPreenche+'|'+campoRetorno+'|c60_descr|c60_estrut|c61_reduz';
        if (!mostrar) {
            caminhoLookup = 'func_conplanosistemacontacorrente.php?'+sMatriz+'funcao_js=parent.'+funcaoPreenche+'&pesquisa_chave='+valorPesquisaEstrutural+'&chave_c60_estrut=' +valorPesquisaEstrutural +  '&funcao_js=parent.'+funcaoCompleta+'&pesquisaEstrutural=true';
        }

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conplano', caminhoLookup, 'Pesquisar Conta Contábil', mostrar);
    }

    function preencheContaContabil(codigoConta, descricaoConta, estrutural, reduzido) {

        //alert(reduzido);
        $("reduzido").value = reduzido;
        document.querySelector('#codigo_conta').value = codigoConta;
        document.querySelector('#descricao_conta').value = descricaoConta;
        document.querySelector('#estrutural_conta').value = estrutural;
        db_iframe_conplano.hide();
        consultaContaCorrente();
    }

    function preencheContaContabilLancador(codigoConta, descricaoConta, estrutural) {

        document.querySelector('#lancador_codigo_conta').value = codigoConta;
        document.querySelector('#lancador_descricao_conta').value = descricaoConta;
        document.querySelector('#lancador_estrutural_conta').value = estrutural;
        db_iframe_conplano.hide();
    }

    function completaContaContabil(descricaoConta, erro, estrutural, codigoConta, reduzido) {

        document.querySelector('#descricao_conta').value = descricaoConta;
        document.querySelector('#codigo_conta').value = codigoConta;
        document.querySelector('#estrutural_conta').value = estrutural;
        document.querySelector('#reduzido').value = reduzido;
        if (erro) {
            document.querySelector('#codigo_conta').value = '';
            document.querySelector('#estrutural_conta').value = '';
            document.querySelector('#reduzido').value = '';
        }
        consultaContaCorrente();
    }

    function completaContaContabilLancador(descricaoConta, erro, estrutural, codigoConta, codigoReduzido) {

        document.querySelector('#lancador_descricao_conta').value = descricaoConta;
        document.querySelector('#lancador_codigo_conta').value = codigoReduzido;
        document.querySelector('#lancador_estrutural_conta').value = estrutural;
        if (erro) {
            document.querySelector('#lancador_codigo_conta').value = '';
            document.querySelector('#lancador_estrutural_conta').value = '';
        }
    }

    dadosFormulario = {};
    var temVisaoCadastrada = false;

    /**
     * @param dados
     */
    function preencherCampos( dados ) {

        dadosFormulario = dados;

        temVisaoCadastrada = true;
        $('mostrarPeriodo').style.display = dadosFormulario.dadosJson.periodo.mostrar ? 'inline' : 'none';
        $('data1').value = dadosFormulario.dadosJson.periodo.periodo_inicial;
        $('data2').value = dadosFormulario.dadosJson.periodo.periodo_final;

        $('mostrarEstrutural').style.display = dadosFormulario.dadosJson.estrutural.mostrar ? '' : 'none';
        $('estrut_inicial').value = dadosFormulario.dadosJson.estrutural.valor;

        $('mostrarContaContabil').style.display = dadosFormulario.dadosJson.conta_contabil.mostrar ? '' : 'none';
        $('codigo_conta').value = dadosFormulario.dadosJson.conta_contabil.codigo;
        $('descricao_conta').value = dadosFormulario.dadosJson.conta_contabil.descricao;

        $('mostrarContaCorrente').style.display = dadosFormulario.dadosJson.conta_corrente.mostrar ? '' : 'none';
        $('contacorrente').value = dadosFormulario.dadosJson.conta_corrente.codigo;

        $('mostrarColunasContaCorrente').style.display = dadosFormulario.dadosJson.conta_corrente.atributos_visualizacao.mostrar ? '' : 'none';
        $('mostrarAtributos').style.display = dadosFormulario.dadosJson.conta_corrente.atributos_filtros.mostrar ? '' : 'none';
        getAtributosDoContaCorrente(dadosFormulario.dadosJson.conta_corrente.atributos_visualizacao.colunas,
            dadosFormulario.dadosJson.conta_corrente.atributos_filtros.atributos);

        $('ctnLancadorDocumentos').style.display = dadosFormulario.dadosJson.eventos.mostrar ? '' : 'none';
        $('ctnLancadorContas').style.display = dadosFormulario.dadosJson.contas.mostrar ? '' : 'none';

        var eventos = [];
        for (var evento of dadosFormulario.dadosJson.eventos.itens) {

            eventos.push([evento.codigo, evento.descricao]);
        }
        oLancadorDocumentos.carregarRegistros(eventos);

        gridLancadorContas.clearAll(true);
        dadosFormulario.dadosJson.contas.itens.each(
            function(conta) {

                gridLancadorContas.addRow([
                    conta.codigo,
                    conta.estrutural,
                    conta.descricao,
                    ""
                ]);
            }
        );
        gridLancadorContas.renderRows();
        $('lblNomeVisao').innerHTML = dadosFormulario.nome;
    }
    var get = js_urlToObject();

    if (!empty(get.codigo_visao)) {

        AjaxRequest.create(
            'cai4_visoescontacorrente.RPC.php',
            {
                'exec':   'getVisaoPorCodigo',
                dados: {'codigo': get.codigo_visao}
            },
            function (response, erro) {

                if (erro) {
                    alert(response.message);
                    return;
                }
                preencherCampos(response.dados);

            }).setMessage("Aguarde, pesquisando os dados da visão").execute();

    }

    function detalhamentoHash(conta, hash, documento) {

        var reduzido = null;
        var contaCorrente = null;
        for (var dados of dadosConsulta) {
            if (dados.estrutural == conta) {
                reduzido = dados.reduzido;
                for (var movimentacao of dados.movimentacoes) {

                    if (movimentacao.conta_corrente == hash ) {

                        if (documento != "undefined" && documento == movimentacao.documento) {
                            contaCorrente = movimentacao;
                            break;
                        } else {
                            contaCorrente = movimentacao;
                            break;
                        }
                    }
                }
            }
        }
        if (contaCorrente != null) {
            abrirJanelaDetalhamento(conta, contaCorrente, reduzido)
        }
    }

    function abrirJanelaDetalhamento(conta, contaCorrente, reduzido)
    {
        var tamanhoDaJanela = window.innerWidth - 150;
        wndConsultaContaCorrenteDetalhe = new windowAux('wndConsultaContaCorrenteDetalhe', 'Detalhamento de Conta Corrente', tamanhoDaJanela, '500');
        var content = "<div style='width: 99%'>";
        content += "  <fieldset>";
        content += "  <legend> Lançamentos";
        content += "  </legend>";
        content += "  <div id='ctnGridDetalhe'>";
        content += "  </div>";
        content += "  </fieldset>";
        content += "</div>";
        wndConsultaContaCorrenteDetalhe.setContent(content);
        wndConsultaContaCorrenteDetalhe.setChildOf(wndCOnsutlaContaCorrente);
        var textoAjuda = 'Detalhamento do conta corrente';
        var textoTotais = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Saldo Anterior</b>: "+js_formatar(contaCorrente.saldo_anterior, 'f') + " <b>" +contaCorrente.natureza_anterior + "</b>";
        textoTotais += " <b> | Débito:</b> "+ js_formatar(contaCorrente.valor_debito, 'f');
        textoTotais += " <b> | Crédito:</b> "+ js_formatar(contaCorrente.valor_credito, 'f');
        textoTotais += " <b> | Saldo Final:</b> "+ js_formatar(contaCorrente.saldo_final, 'f') + " <b>" + contaCorrente.natureza_final + "</b>";
        var oMessageBoardDetalhe = new DBMessageBoard('msgboard1',
            textoAjuda,
            '<b>Conta:</b> '+ conta + ' <b>Conta Corrente: </b>'+ contaCorrente.conta_corrente+ "<br>"+textoTotais,
            wndConsultaContaCorrenteDetalhe.getContentContainer());
        oMessageBoardDetalhe.show();

        wndConsultaContaCorrenteDetalhe.setShutDownFunction(function () {
            wndConsultaContaCorrenteDetalhe.destroy();

        });
        wndConsultaContaCorrenteDetalhe.show();

        gridContaCorrenteDetalhe = new DBGrid('gridDetalhamentoContaCorrente');
        gridContaCorrenteDetalhe.nameInstance = 'gridContaCorrenteDetalhe';
        gridContaCorrenteDetalhe.clearAll(true);
        gridContaCorrenteDetalhe.setCellAlign( ["right", "ledt", "center", "right", "right"]);
        gridContaCorrenteDetalhe.setHeader(["Lançamento", "Documento", "Data", "Débito", "Crédito"]);
        gridContaCorrenteDetalhe.hasTotalizador = true;
        gridContaCorrenteDetalhe.setHeight(300);
        gridContaCorrenteDetalhe.show($('ctnGridDetalhe'));
        getLancamentosContaCorrente(reduzido, contaCorrente);

    }


    getLancamentosContaCorrente = function(reduzido, contaCorrente) {

        gridContaCorrenteDetalhe.clearAll(true);
        AjaxRequest.create(
            rpc,
            {
                'exec':   'getLancamentos',
                'reduzidos': reduzido,
                'lista_lancamentos' : contaCorrente.codigos_lancamentos

            },
            function (response, erro) {

                if (erro) {
                    alert(response.message);
                    return;
                }
                var totais =  {
                    debito: 0,
                    credito: 0
                };
                for (lancamento of response.lancamentos ) {

                    var descricaoDocumento = "<a href='#' onclick='detalheLancamento("+ lancamento.codigo_lancamento+");return false;'>";
                    descricaoDocumento += lancamento.documento+" - "+lancamento.descricao_documento+ "</a>";
                    var linha = [
                        lancamento.codigo_lancamento,
                        descricaoDocumento,
                        js_formatar(lancamento.data, 'd'),
                        js_formatar(lancamento.valor_debito, 'f'),
                        js_formatar(lancamento.valor_credito, 'f')
                    ];
                    gridContaCorrenteDetalhe.addRow(linha);
                    totais.debito += new Number(lancamento.valor_debito);
                    totais.credito +=  new Number(lancamento.valor_credito);
                }
                gridContaCorrenteDetalhe.renderRows();
                var totalizadores = $$('table#tablegridDetalhamentoContaCorrentefooter .gridtotalizador');
                for (var totalizador of totalizadores) {
                    if (totalizador.id == 'TotalForCol0') {
                        totalizador.innerHTML = '<b> Totais</b>';
                    }
                    if (totalizador.id == 'TotalForCol3') {
                        totalizador.innerHTML = js_formatar(Math.abs(totais.debito), 'f');
                    }
                    if (totalizador.id == 'TotalForCol4') {
                        totalizador.innerHTML = js_formatar(Math.abs(totais.credito), 'f');
                    }
                }

            }).setMessage("Aguarde, pesquisando lançamentos").execute();


    }

    function detalheLancamento(codigoLancamento) {
        new infoLancamentoContabil(codigoLancamento, wndConsultaContaCorrenteDetalhe);
    }



    function adicionarContaContabil()
    {
        var dadosLancador = {
            'codigo_conta' : $('lancador_codigo_conta'),
            'estrutural_conta' : $('lancador_estrutural_conta'),
            'descricao_conta' : $('lancador_descricao_conta'),
        };

        if (dadosLancador.codigo_conta.value === '') {
            return alert('Estrutural é de preenchimento obrigatório.');
        }

        var contaJaAdicionada = false;
        gridLancadorContas.aRows.each(
            function (row) {
                if (row.aCells[0].getValue() == dadosLancador.codigo_conta.value) {
                    contaJaAdicionada = true;
                }
            }
        );

        if (contaJaAdicionada) {
            return alert('Código da Conta já adicionado.');
        }

        gridLancadorContas.addRow([
            dadosLancador.codigo_conta.value,
            dadosLancador.estrutural_conta.value,
            dadosLancador.descricao_conta.value,
            "<input type='button' value='Remover' onclick='removerContaLancador("+dadosLancador.codigo_conta.value+")' />"
        ], true);

        dadosLancador.codigo_conta.value = '';
        dadosLancador.estrutural_conta.value = '';
        dadosLancador.descricao_conta.value = '';
    }

    function removerContaLancador(codigoConta) {

        var indiceExcluir = null;
        gridLancadorContas.aRows.each(
            function (row, indice) {
                if (row.aCells[0].getValue() == codigoConta) {
                    indiceExcluir = indice;
                }
            }
        );
        delete(gridLancadorContas.aRows[indiceExcluir]);
        gridLancadorContas.renderRows();
    }

    var gridLancadorContas = new DBGrid('gridLancadorContas1');
    gridLancadorContas.nameInstance = 'gridLancadorContas';
    gridLancadorContas.setCellAlign(['center', 'center', 'left', 'center']);
    gridLancadorContas.setHeader(['Código', 'Estrutural', 'Descrição', 'Ação']);
    gridLancadorContas.setCellWidth(['0', '20%', '60%', '19%']);
    gridLancadorContas.aHeaders[0].lDisplayed = false;
    gridLancadorContas.show($('ctnGridLancadorContasContabeis'));
    gridLancadorContas.clearAll(true);

    $('legendContasContabeis').click();
</script>
