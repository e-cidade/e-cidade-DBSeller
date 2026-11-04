<?php
/**
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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$Lcc31_classificacaocredores = null;
$oRotulo = new rotulocampo();
$oRotulo->label("cc31_classificacaocredores");
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/arrays.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        /**
         * Aqui faz a alinhamento na mão para que o botão do lançador não quebre de linha.
         */
        #ctnLancadorListaClassificacao #divacoes,
        #ctnLancadorRecurso #divacoes {
            white-space: nowrap;
        }

        /**
         * Deixando o tamanho do campo descrição dos lançadores 'Credor' e 'Recurso' com tamanho maior
         * para que se alinhem com a grid.
         */
        #txtDescricaooLancadorFornecedor, #txtDescricaooLancadorRecurso {
            width: 437px;
        }
    </style>
</head>
<body>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<div class="container">
    <div id="ctnGlobalAbas">
        <div id="containerFiltrosBasicos">
            <fieldset style="width: 670px;">
                <legend class="bold">Relatório de Lista de Classificação de Credores</legend>
                <fieldset class="separator">
                    <legend class="bold">Filtros</legend>

                    <table style="width: 100%">
                        <tr>
                            <td class="bold" style="width:100px;" nowrap="nowrap"><label for="exercicio">Exercício do
                                    Empenho:</label></td>
                            <td>
                                <?php
                                $Sexercicio = "Exercício do Empenho";
                                db_input("exercicio", 10, 1, true, 'text', 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold"><label for='data_vencimento_inicial'>Data de Vencimento:</label></td>
                            <td class="bold">
                                <?php
                                db_inputdata('data_vencimento_inicial', null, null, null, true, 'text', 1);
                                echo " <label for='data_vencimento_final'>até</label> ";
                                db_inputdata('data_vencimento_final', null, null, null, true, 'text', 1);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="bold"><label for='data_inicial_nota'>Data de Nota:</label></td>
                            <td class="bold">
                                <?php
                                db_inputdata('data_inicial_nota', null, null, null, true, 'text', 1);
                                echo " <label for='data_final_nota'>até</label> ";
                                db_inputdata('data_final_nota', null, null, null, true, 'text', 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold"><label for='data_inicial_liquidacao'>Data da Liquidação:</label></td>
                            <td class="bold">
                                <?php
                                db_inputdata('data_inicial_liquidacao', null, null, null, true, 'text', 1);
                                echo " <label for='data_final_nota'>até</label> ";
                                db_inputdata('data_final_liquidacao', null, null, null, true, 'text', 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold"><label for="situacao">Situação:</label></td>
                            <td>
                                <?php
                                $aOpcoes = array(0 => "Todos", RelatorioEmpenhoClassificacaoCredores::SITUACAO_PAGOS => "Pagos", RelatorioEmpenhoClassificacaoCredores::SITUACAO_APAGAR => "A Pagar");
                                db_select('situacao_pagamento', $aOpcoes, true, 1);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div id="ctnLancadorListaClassificacao"></div>
                            </td>
                        </tr>

                    </table>
                </fieldset>
                <fieldset class="separator">
                    <legend class="bold">Apresentação:</legend>
                    <table>
                        <tr>
                            <td class="bold"><label for="ordenacao">Ordenação por:</label></td>
                            <td>
                                <select id="ordenacao">
                                    <option value="1">Data da nota de liquidação</option>
                                    <option value="2">Data de vencimento</option>
                                    <option value="3">Data de liquidação</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </fieldset>
        </div>

        <!-- CONTAINER FILTRO CREDOR -->
        <div id="containerAbaFiltroCredor" style="width: 670px;">
            <div id="ctnLancadorCredorEmpenho"></div>
        </div>

        <!-- CONTAINER FILTRO RECURSO -->
        <div id="containerAbaRecurso" style="width: 670px;">
            <?php require_once modification('forms/db_frmfiltrorecursos.php') ?>
        </div>
    </div>
    </fieldset>
    <p>
        <input type="button" id="btnEmitir" value="Emitir"/>
    </p>

</div>

<?php db_menu(); ?>
</body>
</html>

<script type="text/javascript">

    var oAbas = new DBAbas($('ctnGlobalAbas'));
    oAbas.adicionarAba('Principal', $('containerFiltrosBasicos'));
    oAbas.adicionarAba('Credores', $('containerAbaFiltroCredor'));
    oAbas.adicionarAba('Recursos', $('containerAbaRecurso'));

    var oLancadorLista, oLancadorFornecedor, oLancadorRecurso;
    var oInputExercicio = $('exercicio');
    var oInputDataInicial = $('data_vencimento_inicial');
    var oInputDataFinal = $('data_vencimento_final');
    var oInputSituacaoPagamento = $('situacao_pagamento');
    var oInputOrdenacao = $('ordenacao');
    var oInputDataInicialNota = $('data_inicial_nota');
    var oInputDataFinalNota = $('data_final_nota');
    var oInputDataInicialLiquidacao = $('data_inicial_liquidacao');
    var oInputDataFinalLiquidacao = $('data_final_liquidacao');
    oInputSituacaoPagamento.style.width = '140px';
    oInputExercicio.maxLength = 4;

    oLancadorLista = new DBLancador('oLancadorLista');
    oLancadorLista.setNomeInstancia('oLancadorLista');
    oLancadorLista.setLabelAncora('Lista de Classificação de Credores:');
    oLancadorLista.setParametrosPesquisa('func_classificacaocredores.php', ['cc30_codigo', 'cc30_descricao']);
    oLancadorLista.setTextoFieldset("Lista de Classificação de Credores");
    oLancadorLista.setTituloJanela("Pesquisa de Lista de Classificação de Credores");
    oLancadorLista.setGridHeight(100);
    oLancadorLista.show($('ctnLancadorListaClassificacao'));

    oLancadorFornecedor = new DBLancador('oLancadorFornecedor');
    oLancadorFornecedor.setNomeInstancia('oLancadorFornecedor');
    oLancadorFornecedor.setLabelAncora('Credor:');
    oLancadorFornecedor.setParametrosPesquisa('func_cgm_empenho.php', ['e60_numcgm', 'z01_nome']);
    oLancadorFornecedor.setTextoFieldset("Credores");
    oLancadorFornecedor.setTituloJanela("Pesquisa de Credores");
    oLancadorFornecedor.show($('ctnLancadorCredorEmpenho'));

    $('btnEmitir').observe(
        'click',
        function () {

            if (js_comparadata(oInputDataInicial.value, oInputDataFinal.value, '>')) {

                alert('A Data de Vencimento Inicial não pode ser maior que a Data de Vencimento Final.');
                return false;
            }

            var aListas = [], aCredor = [], aRecurso = [];
            oLancadorLista.getRegistros().each(
                function (oLista) {
                    aListas.push(oLista.sCodigo);
                }
            );

            oLancadorFornecedor.getRegistros().each(
                function (oCredor) {
                    aCredor.push(oCredor.sCodigo);
                }
            );

            collectionRecurso.build().map((recurso) => {
                aRecurso.push(recurso.codigo)
            });


            var sPathRelatorio = "emp2_empenhoslistaclassificacao002.php?";
            sPathRelatorio += "&exercicio=" + oInputExercicio.value;
            sPathRelatorio += "&data_inicial=" + oInputDataInicial.value;
            sPathRelatorio += "&data_final=" + oInputDataFinal.value;
            sPathRelatorio += "&situacao_pagamento=" + oInputSituacaoPagamento.value;
            sPathRelatorio += "&listas=" + aListas.implode(',');
            sPathRelatorio += "&credores=" + aCredor.implode(',');
            sPathRelatorio += "&recursos=" + aRecurso.implode(',');
            sPathRelatorio += "&data_inicial_nota=" + oInputDataInicialNota.value;
            sPathRelatorio += "&data_final_nota=" + oInputDataFinalNota.value;
            sPathRelatorio += "&data_inicial_liquidacao=" + oInputDataInicialLiquidacao.value;
            sPathRelatorio += "&data_final_liquidacao=" + oInputDataFinalLiquidacao.value;
            sPathRelatorio += "&ordenacao=" + oInputOrdenacao.value;

            var oJanela = window.open(
                sPathRelatorio,
                '',
                'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            oJanela.moveTo(0, 0);
        }
    );
</script>
