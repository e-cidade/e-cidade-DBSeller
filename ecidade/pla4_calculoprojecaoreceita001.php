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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
</head>
<body class="body-default">
<div class="container">
    <div class="alert alert-primary text-left" role="alert">
        Selecione um plano e configure o Fator de correção antes de calcular as projeções.
    </div>
    <fieldset>
        <legend>Projeção da receita</legend>
        <div style="text-align: left">
            <label class="bold" for="planejamento">Planejamento:</label>
            <select id="planejamento" class="field-size8">
                <option value="">Selecione um plano</option>
            </select>
        </div>
    </fieldset>
    <button type="button" id="fatorCorrecao" disabled>
        <i class="fas fa-percent"></i>
        Fator de Correção
    </button>
    <button type="button" id="calcular" disabled>
        <i class="fas fa-calculator"></i>
        Calcular
    </button>
</div>


<div id="modalFator" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Aplica o fator de correção para os anos de vigência do plano.<br>
        Para aplicar o fator informado a todas as Naturezas de Receita do Sistema, informar o Número <b>4</b> para as
        receitas e após o <b>9</b> para as Deduções de Receita.<br>
        Para visualizar os elementos configurados, clique em <kbd>Imprimir</kbd>.<br>

        Por padrão os indices são inflatores, para informar um deflator marque:
        "<kbd><i class="far fa-check-square"></i> Deflator</kbd>".
    </div>
    <div class="container" style="width: 450px;">
        <form id="formFatorCorrecao">
            <fieldset>
                <legend>Fator de correção - <label id="labelPlano"></label></legend>
                <div style="text-align: left">
                    <label class="bold" for="natureza" id="labelNatureza">Natureza da Receita: </label>
                    <input type="text" id='natureza' class="field-size6" maxlength="15"
                           oninput="js_ValidaCampos(this, 1, 'Natureza Receita', 'f', 'f', event)">
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: stretch; ">
                    <input type="checkbox" id='deflator'>
                    <label class="bold" for="deflator" id="labelDeflator" style="margin-left: 5px;">Deflator</label>
                </div>
                <fieldset class="separator">
                    <legend>Informe o valor percentual</legend>
                    <div id="containerValores"></div>
                </fieldset>
            </fieldset>

            <button type="button" id="salvarFatorCorrecao">
                <i class="far fa-save"></i>
                Salvar
            </button>

            <button type="button" id="imprimirFatorCorrecao">
                <i class="fas fa-print"></i>
                Imprimir
            </button>
        </form>
    </div>
</div>

<div id="modalCalculo" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Clicar em <kbd><i class="fas fa-calculator"></i> Recalcular</kbd> irá reprojetar os valores conforme os fatores
        de correção configurados e irá modificar os valores alterados/excluídos manualmente.<br>
        O Sistema salvará o valor editado após sair do campo.
        Para atualizar o valor das contas Sintéticas clicar em <kbd><i class="fas fa-sync"></i></kbd>.<br>
    </div>
    <div class="container" style="width: 1400px;">
        <fieldset>
            <legend>Calculo das projeções</legend>
            <form id="formCalculoProjecao" style="width: 1380px;">

                <table id="data-table"
                       class="table table-sm"
                       data-virtual-scroll="true"
                       data-detail-view="true"
                       style="width: 100%;">
                </table>
            </form>
        </fieldset>
        <button type="button" id="recalcular">
            <i class="fas fa-calculator"></i>
            Recalcular
        </button>
    </div>
</div>


<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>
<!-- requires alertfy -->
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>

<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>

<script type="text/javascript">
    $.noConflict();
    jQuery(document).ready(function ($) {

        const btnFatorCorrecao = document.getElementById('fatorCorrecao');
        const btnCalcular = document.getElementById('calcular');

        const routs = {
            projecao: {
                buscar: 'financeiro/planejamento/projecao/receita/buscar',
                alterar: 'financeiro/planejamento/projecao/receita/atualizar/valor-exercicio',
                valorBase: 'financeiro/planejamento/projecao/receita/atualizar/valor-base',
                recalcular: 'financeiro/planejamento/projecao/receita/recalcular',
                remover: 'financeiro/planejamento/receita/previsao/remover',
                removerNaturezas: 'financeiro/planejamento/receita/previsao/removerNaturezas',
            },

            fator: 'financeiro/planejamento/fator-correcao/receita',
            parametro : 'financeiro/orcamento/utiliza-decimal'
        };

        var precisao = 2;
        const planejamento = new Planejamento(document.getElementById('planejamento'));
        planejamento.getElement().addEventListener('change', () => {

            btnFatorCorrecao.removeAttribute('disabled');
            btnCalcular.removeAttribute('disabled');

            if (planejamento.getValue() === '') {
                btnFatorCorrecao.setAttribute('disabled', 'disabled');
                btnCalcular.setAttribute('disabled', 'disabled');
            }
        });

        planejamento.load();
        btnFatorCorrecao.addEventListener('click', () => {
            opemFatorCorrecao();
        });

        PHPSession.loadData().then(() => {

            let ano = PHPSession.getValueSession('DB_anousu');

            HttpClient.get(`${PHPSession.requestApi}/${routs.parametro}/${ano}`).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                precisao = response.data ? 2 : 0;
            });
        });

        btnCalcular.addEventListener('click', () => {
            opemModalCalculo();
        });

        const fechaModal = modal => {
            if (!!modal.oDBMask) {
                modal.oDBMask.destroy();
            }

            modal.hide();
        };

        /**
         * ------------------------------- FUNÇÕES PARA MANUTENÇÃO DO FATOR DE CORREÇÃO --------------------------------
         */

        const codigosValidacao = [4, 9];

        const modalFator = {
            container: document.getElementById('modalFator'),
            form: document.getElementById('formFatorCorrecao'),
            labelPlano: document.getElementById('labelPlano'),
            labelNatureza: document.getElementById('labelNatureza'),
            deflator: document.getElementById('deflator'),
            natureza: document.getElementById('natureza'),
            valores: document.getElementById('containerValores'),
            salvar: document.getElementById('salvarFatorCorrecao'),
            imprimir: document.getElementById('imprimirFatorCorrecao')
        };

        const valores = new Valores();

        var windowFator = new windowAux('windowFator', 'Configura o fator de correção', 850, 500);
        windowFator.setContent(modalFator.container);
        windowFator.setShutDownFunction(() => {
            modalFator.form.reset();
            if (modalFator.valores) {
                modalFator.valores.innerHTML = '';
            }

            fechaModal(windowFator);
        });

        modalFator.imprimir.addEventListener('click', () => {
            let url = `pla2_fator_correcao_receita.php?planejamento=${planejamento.getValue()}`;
            window.open(url, '', 'scrollbars=1,location=0');
        })


        const opemFatorCorrecao = () => {
            let plano = planejamento.getPlano();
            modalFator.labelPlano.innerHTML = plano.pl2_titulo;
            windowFator.show(0, 0, true);
            modalFator.container.style.display = '';

            valores.criaInputValores(modalFator.valores, plano);
        };

        const validaInputsModalFator = () => {
            try {
                if (modalFator.natureza.value === '') {
                    throw 'Você deve informar a Natureza.';
                }

                let codigo = Number(modalFator.natureza.value.substr(0, 1))
                if (!codigosValidacao.includes(codigo)) {
                    throw 'Código da natureza inválido.';
                }

                if (valores.existeValoresNaoInformados()) {
                    throw 'Você deve informar um valor percentual para todos os exercícios.';
                }
            } catch (e) {
                alert(e);
                return false;
            }

            if (!valores.validaPercentuais()) {
                return false;
            }

            return true;
        }

        modalFator.salvar.addEventListener('click', () => {
            if (!validaInputsModalFator()) {
                return;
            }

            let plano = planejamento.getPlano();

            const formData = new FormData();
            formData.append('planejamento', plano.pl2_codigo);
            formData.append('natureza', modalFator.natureza.value);
            formData.append('deflator', modalFator.deflator.checked ? 1 : 0);
            formData.append('valores', JSON.stringify(valores.getValores()));

            const parametros = {
                body: formData,
                reportProgress: true,
                reportMessage: `Aguarde, esse procedimento pode demorar um pouco.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.fator}`, parametros).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                modalFator.form.reset();
            });
        });

        /**
         * ----------------------------------- FUNÇÕES PARA MANUTENÇÃO DO CALCULO -------------------------------------
         */

        const modalCalculo = {
            container: document.getElementById('modalCalculo'),
            form: document.getElementById('formCalculoProjecao'),
            recalcular: document.getElementById('recalcular')
        };

        var table = $('#data-table');
        var estimativas = [];
        var excluiuRegistro = false;

        var windowCalculo = new windowAux('windowCalculo', 'Calcula as projeções do plano', 1450, 800);
        windowCalculo.setContent(modalCalculo.container);
        windowCalculo.allowCloseWithEsc(false);
        windowCalculo.setShutDownFunction(() => {
            fechaModal(windowCalculo);
        });

        const opemModalCalculo = () => {

            montaTabela();
            table.bootstrapTable('load', []);
            modalCalculo.container.style.display = '';
            windowCalculo.show(0, 0, true);
            buscarCalculo();
        }

        function montaTabela() {
            table.bootstrapTable('destroy');
            let colunas = montaColunas();

            table.bootstrapTable({
                columns: colunas,
                buttons: buttons,
                detailFormatter: detailFormatter,
                uniqueId: "idLinha",
                locale: 'pt-BR',
                cache: false,
                height: 500,
                search: true,
                class: "table table-sm"
            });
        };

        const adicionaAcao = (value, row, index) => {
            if ((row.id === null && row.contasDesdobramento.length === 0) ||
                (row.id !== null && row.temDesdobramento)) {
                return '';
            }

            return [
                '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                '  <i class="fas fa-trash-alt"></i>',
                '</a>'
            ].join('&nbsp;');
        };

        const formataValor = (value, row, index) => {
            let numObj = parseFloat(value);
            return numObj.toLocaleString('pt-BR');
        };

        const formataValorInput = (value, row, index, field) => {
            // conta sintetica sem desdobramento
            if (row.id === null && row.contasDesdobramento.length === 0) {
                return formataValor(value);
            }


            // conta analitica com desdobramento deve ter o input bloqueado
            if (row.id !== null && row.temDesdobramento) {
                return `
                <input type="text" class="valor field-size-max text-right readonly"
                       data-field="${field}" data-natureza="${row.fonte}" data-id-fonte="${row.orcfontes_id}"
                       data-cp="${row.concarpeculiar_id}"
                       value="${value}" readonly>`;
            }


            // por default deixamos o input liberado
            return `
                <input type="text" class="valor-editavel field-size-max text-right"
                       data-field="${field}" data-natureza="${row.fonte}" data-id-fonte="${row.orcfontes_id}"
                       data-cp="${row.concarpeculiar_id}" value="${value}" >`;
        };

        window.operateEvents = {
            'input .valor-editavel': function (event, value, row, index) {
                let natureza = event.target.dataset.natureza;
                if (!/^[0-9|.|-]+$/.test(event.target.value)) {
                    event.target.value = event.target.defaultValue;
                }

                if (!isNaN(Number(event.target.value))) {
                    if (Number(natureza.substr(0, 1)) === 9 && event.target.value > 0) {
                        event.target.value *= -1;
                    }
                    event.target.defaultValue = event.target.value;
                } else {
                    event.target.value = 0
                }

                return false;
            },
            'change .valor-editavel': function (e, value, row, index) {
                e.target.value = Number(e.target.value).toFixed(precisao);

                if (e.target.dataset.field === 'valor_base') {
                    calculaInflatores(e.target, row);
                } else {
                    calculaLinha(e.target, row);
                }
            },
            'click .excluir': function (e, value, row, index) {

                let linhasRemover = [];
                linhasRemover.push(row.fonte);
                let rota = `${PHPSession.requestApi}/${routs.projecao.remover}`;
                const formData = new FormData;
                formData.append('planejamento_id', planejamento.getValue());

                let msg = 'Tem certeza que deseja excluir a estimativa?';
                if (row.id === null) {
                    msg = 'Tem certeza que deseja excluir a estimativa e seus desdobramentos?';
                    row.contasDesdobramento.each(natureza => {
                        formData.append('orcfontes[]', natureza.codigo_fonte);
                        linhasRemover.push(natureza.fonte);
                    });
                    rota = `${PHPSession.requestApi}/${routs.projecao.removerNaturezas}`;
                } else {
                    formData.append('id', row.id);
                }

                alertify.confirm(`${msg}`, (e) => {
                    if (e) {

                        PHPSession.appendFormData(formData);
                        const parametros = {
                            body: formData,
                            reportProgress: true,
                            reportMessage: `Aguarde, removendo estimativa.`
                        }


                        HttpClient.post(rota, parametros).then(response => {
                            alert(response.message);
                            if (response.error) {
                                return;
                            }

                            linhasRemover.each(natureza => {
                                table.bootstrapTable('remove', {
                                    field: 'fonte',
                                    values: natureza
                                });
                            });

                            excluiuRegistro = true;
                        });
                    }
                });
            }
        };

        const montaColunas = () => {
            const colunas = [
                {
                    title: 'Natureza',
                    field: 'fonte_mascara',
                    align: 'center',
                    valign: 'middle',
                    width: 150,
                }, {
                    title: 'Descrição',
                    field: 'descricao_fonte',
                    align: 'left',
                    valign: 'center',
                    width: 350,
                    formatter: (value) => {
                        return `
                        <div style="width: 340px" title="${value}">
                          <div class="elipse">${value}</div>
                        </div>
                        `;
                    }
                }, {
                    title: 'Recurso',
                    field: 'fonte_recurso',
                    align: 'center',
                    valign: 'middle',
                    width: 20,
                }, {
                    title: 'CO',
                    field: 'codigo_complemento',
                    align: 'center',
                    valign: 'middle',
                    width: 20,
                }, {
                    title: 'Vlr Base',
                    field: 'valor_base',
                    align: 'right',
                    valign: 'middle',
                    formatter: formataValorInput,
                    events: window.operateEvents
                }
            ];

            let plano = planejamento.getPlano();

            for (let ano = plano.pl2_ano_inicial; ano <= plano.pl2_ano_final; ano++) {
                colunas.push({
                    title: ano,
                    field: `valor_${ano}`,
                    align: 'right',
                    valign: 'middle',
                    events: window.operateEvents,
                    formatter: formataValorInput
                });
            }

            colunas.push({
                title: 'Ações',
                field: 'acao',
                align: 'center',
                valign: 'right',
                clickToSelect: false,
                width: '100px',
                events: window.operateEvents,
                formatter: adicionaAcao
            });
            return colunas;
        };

        modalCalculo.recalcular.addEventListener('click', () => {
            alertify.confirm("Tem certeza que deseja recalcular a projeção? Todos valores manuais serão perdidos.", (e) => {
                if (e) {
                    table.bootstrapTable('load', []);
                    let rota = `${PHPSession.requestApi}/${routs.projecao.recalcular}`
                    let msg = 'Aguarde, recalculando projeção, esse procedimento pode demorar um pouco.';
                    executarRotaManutencaoPlanejamento(rota, msg);
                }
            });
        });

        const buscarCalculo = () => {
            let rota = `${PHPSession.requestApi}/${routs.projecao.buscar}`
            executarRotaManutencaoPlanejamento(rota, 'Aguarde, esse procedimento pode demorar um pouco.');
            excluiuRegistro = false;
        };

        const executarRotaManutencaoPlanejamento = (rota, msg) => {
            const formData = new FormData();
            formData.append('planejamento', planejamento.getValue());
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: false
            }

            js_divCarregando(msg, 'minha_mensagem');
            HttpClient.post(`${rota}`, parametros).then(response => {

                js_removeObj('minha_mensagem');
                if (response.error) {
                    alert(response.message);
                    return;
                }


                estimativas = response.data;

                for (let estimativa of estimativas) {
                    estimativa.idLinha = estimativa.fonte;
                    if (estimativa.concarpeculiar_id !== null) {
                        estimativa.idLinha = `${estimativa.fonte}#${estimativa.concarpeculiar_id}`
                    }
                }


                renderizaLinhasGrid();
            });
        };

        const renderizaLinhasGrid = () => {
            table.bootstrapTable('load', estimativas);
        };

        /**
         * Ação disparada quando alteramos o valor de uma receita analitica
         */
        const calcularReceitaAnalitica = (element, linha) => {

            let field = element.dataset.field;
            let valor = Number(element.value.replace(',', '.'));

            // atualiza valores sintéticos
            atualizarValoresReceitasSinteticas(linha.fontesPai, linha, field, valor);

            // atualiza o valor da própria linha alterada
            atualizarValorNatureza(linha.fonte, field, valor, linha.concarpeculiar_id);
            // salva a alteração
            if (field !== 'valor_base') {
                salvaAtualizacaoValorEstimativa(linha.id, field, valor);
            } else {
                atualizaValorBase(linha);
            }
            table.bootstrapTable('refresh');
        };

        /**
         * Quando editado o valor base, devemos recalcular o valor dos exercícios futuros aplicando os inflatores
         */
        const getInflatores = (linha) => {
            // se a conta for sintética, pega o inflator da primeira conta desdobrada.
            // conta sintética não possui valor
            if (linha.id === null) {
                let fonte = linha.contasDesdobramento[0].fonte
                table.bootstrapTable('getData')

                let x = estimativas.filter(estimativa => estimativa.fonte === fonte);
                return x[0].inflatores;
            }

            return linha.inflatores;
        }

        const calculaInflatores = (elemento, linha) => {

            let inflatores = getInflatores(linha);
            let fonte = linha.fonte;
            let valor = elemento.value

            // precisao = 2;
            inflatores.each(inflator => {
                let field = `valor_${inflator.ano}`;
                let input = getInput(field, fonte);

                if (inflator.deflator) {
                    inflator.percentual *= -1;
                }


                let x = (1 + (inflator.percentual / 100));

                valor = Number(valor * (1 + (inflator.percentual / 100))).toFixed(precisao);
                input.value = valor;

                calculaLinha(input, linha);
            });

            calculaLinha(elemento, linha);
        };

        const calculaLinha = (elemento, linha) => {
            if (linha.id === null) {
                calcularDesdobramento(elemento, linha)
            } else {
                calcularReceitaAnalitica(elemento, linha);
            }
        };


        /**
         * Ação disparada quando alteramos o valor de uma receita sintética
         */
        const calcularDesdobramento = (element, linha) => {

            let natureza = element.dataset.natureza
            let field = element.dataset.field;
            let valor = Number(element.value.replace(',', '.'));
            let totalCalculo = 0;

            // calcula valor do desdobramento
            linha.contasDesdobramento.each(dados => {
                dados.valor = Number((valor * dados.percentual) / 100).toFixed(precisao);
                totalCalculo += dados.valor
            });
            totalCalculo = totalCalculo.toFixed(precisao);

            if (totalCalculo != valor) {
                /**
                 * @todo se der diferença no valor calculado percentualmente, com o valor digitado na conta sintética
                 *    deve retirar / acrescentar a diferença na conta desdobrada onde o recurso é livre.
                 *    tem uma propriedade nos objetos dentro de linha.contasDesdobramento
                 */
            }

            let fontesPai = [];

            linha.contasDesdobramento.each(dados => {

                let idLinha = `${dados.fonte}#${dados.cp}`;
                let linhaAnalitica = table.bootstrapTable('getRowByUniqueId', idLinha);

                if (fontesPai.length === 0) {
                    fontesPai = linhaAnalitica.fontesPai;
                }

                // atualiza o valor de cada receita desdobrada
                atualizarValorNatureza(dados.fonte, field, dados.valor, dados.cp);
                // persiste a alteração

                if (field !== 'valor_base') {
                    salvaAtualizacaoValorEstimativa(linhaAnalitica.id, field, dados.valor);
                } else {
                    atualizaValorBase(linhaAnalitica);
                }
            });

            let naturezasAtualizar = fontesPai.filter(fontePai => {
                return (fontePai !== natureza);
            });

            atualizarValoresReceitasSinteticas(naturezasAtualizar, linha, field, valor);

            table.bootstrapTable('refresh');
        };

        /**
         * Atualiza os valores dos inputs conforme os parâmetros informados
         *
         * @param {string} natureza - natureza da receita (estrutural sem ponto... igual fonte)
         * @param {string} field - coluna da grid
         * @param {Number} valor - valor a atualizar
         * @param {string} cp - caracteristica peculiar
         */
        const atualizarValorNatureza = (natureza, field, valor, cp) => {

            let idLinha = `${natureza}#${cp}`;
            let linhaAnalitica = table.bootstrapTable('getRowByUniqueId', idLinha);
            let index = estimativas.findIndex(estimativa => estimativa.idLinha === idLinha);
            let input = getInput(field, natureza, cp);


            input.value = valor;
            linhaAnalitica[field] = valor;
            estimativas[index][field] = valor;
        }

        const getInput = (field, natureza, cp) => {
            if (cp === undefined) {
                return document.querySelector(`input[data-field="${field}"][data-natureza="${natureza}"]`);
            }

            return document.querySelector(`input[data-field="${field}"][data-natureza="${natureza}"][data-cp="${cp}"]`);
        };

        /**
         * Atualiza os inputs e objeto da grid das contas sintéticas
         * @param {[]} naturezasAtualizar - array com a lista de naturezas a serem atualizadas
         * @param {{}} linha - objeto da linha da grid
         * @param {string} field - coluna da grid
         * @param {Number} valorAtual - valor digitado no input
         */
        const atualizarValoresReceitasSinteticas = (naturezasAtualizar, linha, field, valorAtual) => {

            let valorAntigo = Number(linha[field]);
            linha[field] = Number(valorAtual);

            let novoValor = Number(valorAtual - valorAntigo);
            naturezasAtualizar.each(natureza => {
                let index = estimativas.findIndex(estimativa => estimativa.fonte === natureza);

                let valorAntigo = estimativas[index][field];
                valorAntigo += Number(novoValor);
                estimativas[index][field] = valorAntigo;
            });
        }

        /**
         * salva a alteração
         */
        const salvaAtualizacaoValorEstimativa = (id, field, valor) => {

            let ano = field.replace('valor_', '');

            const formData = new FormData();
            formData.append('id', id);
            formData.append('exercicio', Number(ano));
            formData.append('valor', valor);

            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: false
            }
            HttpClient.post(`${PHPSession.requestApi}/${routs.projecao.alterar}`, parametros).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
            });
        };

        const atualizaValorBase = (linha) => {

            const formData = new FormData();
            formData.append('id', linha.id);
            formData.append('valor', linha.valor_base);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: false
            }
            HttpClient.post(`${PHPSession.requestApi}/${routs.projecao.valorBase}`, parametros).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
            });
        };

        function buttons() {
            return {
                btnRefresh: {
                    text: 'Atualiza Grid',
                    icon: 'fa-sync',
                    event: function () {
                        if (excluiuRegistro) {
                            buscarCalculo();
                        } else {
                            renderizaLinhasGrid();
                        }
                    }
                }
            }
        };


        /**
         * -------------------------------------------------------------------------------------------------------------
         *                    FUNÇÕES PARA CRIAÇÃO DO DETAIL (QUANDO CLICA NO + DA GRID)
         * -------------------------------------------------------------------------------------------------------------
         */
        const formataDadosSintetico = (dadosLinha) => {
            return [
                [
                    {
                        label: "Natureza:",
                        valor: `${dadosLinha.fonte_mascara} - ${dadosLinha.descricao_fonte}`
                    }
                ]
            ];
        };

        const formataDadosAnalitico = (dadosLinha) => {
            return [
                [
                    {
                        label: "Natureza:",
                        valor: `${dadosLinha.fonte_mascara} - ${dadosLinha.descricao_fonte}`
                    },
                    {
                        label: "Instituição:",
                        valor: dadosLinha.nome_instituicao
                    }
                ],
                [
                    {
                        label: "Órgão:",
                        valor: `${dadosLinha.orcorgao_id} - ${dadosLinha.descricao_orgao}`
                    },
                    {
                        label: "Unidade:",
                        valor: `${dadosLinha.orcunidade_id} - ${dadosLinha.descricao_unidade}`
                    }
                ],
                [
                    {
                        label: "Fonte de Recurso :",
                        valor: `${dadosLinha.fonte_recurso} - ${dadosLinha.recurso}`
                    },
                    {
                        label: "Complemento da Fonte:",
                        valor: `${dadosLinha.codigo_complemento} - ${dadosLinha.complemento}`
                    }
                ],
                [
                    {
                        label: "CP :",
                        valor: `${dadosLinha.concarpeculiar_id} - ${dadosLinha.caracteristica_peculiar}`
                    },
                    {
                        label: "Esfera Orçamentária:",
                        valor: `${dadosLinha.esfera_orcamentaria}`
                    }
                ],
                [
                    {
                        label: "Indicador de Resultado primário :",
                        valor: `${dadosLinha.identificador_resultado}`
                    }
                ],
            ];
        };

        const createDetailSintetico = (dadosLinha) => {
            let linhas = formataDadosSintetico(dadosLinha);
            return detailFormaterTable.createDetail(linhas, 'Natureza da Receita Sintética');
        };
        const createDetailAnalitico = (dadosLinha) => {
            let linhas = formataDadosAnalitico(dadosLinha);
            return detailFormaterTable.createDetail(linhas, 'Natureza da Receita Analítica');
        };

        const detailFormatter = (index, row) => {

            if (row.id === null) {
                return createDetailSintetico(row);
            }
            return createDetailAnalitico(row);
        };
    });
</script>
</body>
