<?php

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
        Selecione um plano e configure o Fator de correção.
    </div>
    <fieldset>
        <legend>Projeção da despesa</legend>
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

<div id="modalInfo" style="display: none">
    <fieldset>
        <legend>Detalhamento</legend>
        <form id="formInfo">
            <table style="width: 100%;">
                <tr>
                    <td class="bold field-size5">Estrutural:</td>
                    <td><input type="text" id="infoEstrutural" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Instituição:</td>
                    <td><input type="text" id="infoInstistuicao" class="readonly field-size-max" readonly/></td>
                </tr>

                <tr>
                    <td class="bold field-size5">Órgão:</td>
                    <td><input type="text" id="infoOrgao" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Unidade:</td>
                    <td><input type="text" id="infoUnidade" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Função:</td>
                    <td><input type="text" id="infoFuncao" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Subfunção:</td>
                    <td><input type="text" id="infoSubfuncao" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Programa:</td>
                    <td><input type="text" id="infoPrograma" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Iniciativa/Ação:</td>
                    <td><input type="text" id="infoIniciativa" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Elemento</td>
                    <td><input type="text" id="infoElemento" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Recurso: Siconfi - Subrecurso - Complemento:</td>
                    <td><input type="text" id="infoRecurso" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Elemento</td>
                    <td><input type="text" id="infoElemento" class="readonly field-size-max" readonly/></td>
                </tr>
                <tr>
                    <td class="bold field-size5">Recurso: Siconfi - Subrecurso - Complemento:</td>
                    <td><input type="text" id="infoRecurso" class="readonly field-size-max" readonly/></td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>

<div id="modalCalculo" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        <b>Atenção:</b> Clicar em <kbd>Recalcular</kbd> apagará os valores alterados manualmente e irá reprojetar os
        valores conforme os fatores de correção.<br>
        O Sistema salvará o valor editado após sair do campo.<br>
        Clique em <kbd>Incluir Detalhamento com Base na Execução</kbd> para incluir detalhamentos novos, baseados na
        criação de dotações durante o exercício.<br>
        Após realizar a manutenção, clique em <kbd>Processar Valores Sintéticos</kbd> para atualizar os Programas e
        Iniciativas/Ações.
    </div>
    <div class="container" style="width: 1400px;">
        <fieldset>
            <legend>Calculo das projeções - Base de cáculo: <label id="labelBaseCalculo"></label></legend>

            <form id="formCalculoProjecao" style="width: 1380px;">

                <table id="data-table"
                       class="table table-sm"
                       data-virtual-scroll="true"

                       style="width: 100%;">
                </table>
            </form>
        </fieldset>
        <select id="formaRecalculo" style="margin-right: 2px">
            <option value="1">Previsão Atualizada</option>
            <option value="2">Empenhado</option>
            <option value="3">Liquidado</option>
            <option value="4">Pago</option>
        </select>

        <button type="button" id="recalcular">
            <i class="fas fa-calculator"></i>
            Recalcular
        </button>

        <button type="button" id="criarDetalhamento" style="margin-left: 2px">
            <i class="fas fa-save"></i>
            Incluir Detalhamento com Base na Execução
        </button>

        <button type="button" id="processarValoresSinteticos" style="margin-left: 2px">
            <i class="fas fa-cog"></i>
            Processar Valores Sintéticos
        </button>
    </div>
</div>

<div id="modalFator" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Aplica o fator de correção para os anos de vigência do plano.<br>
        Se não informado a "Natureza da despesa", irá aplicar o fator informado a todas Naturezas de despesa do
        sistema.<br>
        Quando informado, aplica a própria e seus desdobramentos.<br>
        Para visualizar os elementos configurados, clique em <kbd>Imprimir</kbd>.
    </div>
    <div class="container" style="width: 450px;">
        <form id="formFatorCorrecao">
            <fieldset>
                <legend>Fator de correção - <label id="labelPlano"></label></legend>
                <div style="text-align: left">
                    <label class="bold" for="naturezaDespesa">Natureza Despesa:</label>
                    <input type="text" id='naturezaDespesa' class="field-size6" maxlength="7"
                           oninput="js_ValidaCampos(this, 1, 'Natureza Despesa', 'f', 'f', event)">
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

<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>

<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>

<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<script type="text/javascript">

    const routs = {
        planos: 'financeiro/planejamento/consulta/planos-em-desenvolvimento',
        fatorCorrecao: 'financeiro/planejamento/fator-correcao/despesa',
        projecao: {
            buscar: 'financeiro/planejamento/projecao/despesa/buscar',
            alterar: 'financeiro/planejamento/projecao/despesa/atualizar/valor-exercicio',
            valorBase: 'financeiro/planejamento/projecao/despesa/atualizar/valor-base',
            recalcular: 'financeiro/planejamento/projecao/despesa/recalcular',
            criarDetalhamento: 'financeiro/planejamento/projecao/despesa/criar-detalhamento-novas-dotacoes',
            valoresSinteticos: 'financeiro/planejamento/recalcular-valores-sinteticos',
        }
    }

    const cboPlanejamento = document.getElementById('planejamento');
    const btnFatorCorrecao = document.getElementById('fatorCorrecao');
    const btnCalcular = document.getElementById('calcular');

    const planejamento = new Planejamento(document.getElementById('planejamento'));
    const valoresFatorCorrecao = new Valores();
    const modalFator = {
        container: document.getElementById('modalFator'),
        form: document.getElementById('formFatorCorrecao'),
        labelPlano: document.getElementById('labelPlano'),
        deflator: document.getElementById('deflator'),
        naturezaDespesa: document.getElementById('naturezaDespesa'),
        valores: document.getElementById('containerValores'),
        salvar: document.getElementById('salvarFatorCorrecao'),
        imprimir: document.getElementById('imprimirFatorCorrecao')
    }

    const modalCalculo = {
        container: document.getElementById('modalCalculo'),
        form: document.getElementById('formCalculoProjecao'),
        formaRecalculo: document.getElementById('formaRecalculo'),
        recalcular: document.getElementById('recalcular'),
        criarDetalhamento: document.getElementById('criarDetalhamento'),
        valoresSinteticos: document.getElementById('processarValoresSinteticos'),
        labelBaseCalculo: document.getElementById('labelBaseCalculo'),
    }

    const modalInfo = {
        container: document.getElementById('modalInfo'),
        form: document.getElementById('formInfo'),
        instistuicao: document.getElementById('infoInstistuicao'),
        estrutural: document.getElementById('infoEstrutural'),
        orgao: document.getElementById('infoOrgao'),
        unidade: document.getElementById('infoUnidade'),
        funcao: document.getElementById('infoFuncao'),
        subfuncao: document.getElementById('infoSubfuncao'),
        programa: document.getElementById('infoPrograma'),
        iniciativa: document.getElementById('infoIniciativa'),
        elemento: document.getElementById('infoElemento'),
        recurso: document.getElementById('infoRecurso'),
    }

    planejamento.load();

    /**
     * @var planos é um array com todos os dados dos planos disponiveis
     */
    const planos = [];
    var table = $('#data-table');

    const fechaModal = (janela, modal) => {
        modal.form.reset();
        if (modal.valores) {
            modal.valores.innerHTML = '';
        }

        if (!!janela.oDBMask) {
            janela.oDBMask.destroy();
        }
        janela.hide();
    }

    var windowFator = new windowAux('windowFator', 'Configura o fator de correção', 850, 500);
    windowFator.setContent(modalFator.container);
    windowFator.setShutDownFunction(() => {
        fechaModal(windowFator, modalFator)
    });

    var windowCalculo = new windowAux('windowCalculo', 'Calcula as projeções do plano', 1450, 800);
    windowCalculo.setContent(modalCalculo.container);
    windowCalculo.allowCloseWithEsc(false);
    windowCalculo.setShutDownFunction(() => {
        fechaModal(windowCalculo, modalCalculo)
    });

    var windowInfo = new windowAux('windowInfo', 'Informações', 700, 400);
    windowInfo.setContent(modalInfo.container);

    windowInfo.setShutDownFunction(() => {
        if (!!windowInfo.oDBMask) {
            windowInfo.oDBMask.destroy();
        }
        windowInfo.hide();
    });

    windowCalculo.add(windowInfo);

    $.noConflict();
    jQuery(document).ready(function ($) {

        cboPlanejamento.addEventListener('change', () => {
            btnFatorCorrecao.setAttribute('disabled', 'disabled');
            btnCalcular.setAttribute('disabled', 'disabled');

            if (cboPlanejamento.value !== '') {
                btnFatorCorrecao.removeAttribute('disabled');
                btnCalcular.removeAttribute('disabled');
            }
        });

        btnFatorCorrecao.addEventListener('click', () => {
            windowFator.show(0, 0, true);
            modalFator.container.style.display = '';

            let plano = planejamento.getPlano();

            modalFator.labelPlano.innerHTML = plano.pl2_titulo;
            valoresFatorCorrecao.criaInputValores(modalFator.valores, plano);
        });

        const validaFormFatorCorrecao = () => {
            try {
                if (modalFator.naturezaDespesa.value == '') {
                    throw 'Você deve informar a Natureza da Despesa.';
                }
                if (modalFator.naturezaDespesa.value.substr(0, 1) != 3) {
                    throw "Código da natureza inválido! A natureza deve começar com o digito 3.";
                }

                if (valoresFatorCorrecao.existeValoresNaoInformados()) {
                    throw 'Informe todos os fatores de correção.';
                }
            } catch (e) {
                alert(e);
                return false;
            }

            if (!valoresFatorCorrecao.validaPercentuais()) {
                return false;
            }

            return true;
        }


        modalFator.salvar.addEventListener('click', () => {
            if (!validaFormFatorCorrecao()) {
                return;
            }

            const formData = new FormData();
            formData.append('planejamento', planejamento.getValue());
            formData.append('deflator', modalFator.deflator.checked ? 1 : 0);
            formData.append('natureza', modalFator.naturezaDespesa.value);
            formData.append('valores', JSON.stringify(valoresFatorCorrecao.getValores()));

            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: true,
                reportMessage: `Aguarde, esse procedimento pode demorar um pouco.`
            }
            HttpClient.post(`${PHPSession.requestApi}/${routs.fatorCorrecao}`, parametros).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                modalFator.form.reset();
            });
        });

        /**
         * @todo implementar
         */
        modalFator.imprimir.addEventListener('click', () => {
            let url = `pla2_fator_correcao_despesa.php?planejamento=${planejamento.getValue()}`;
            window.open(url, '', 'scrollbars=1,location=0');
        });


        btnCalcular.addEventListener('click', () => {

            windowCalculo.show(0, 0, true);
            modalCalculo.container.style.display = '';

            montaTabela();
            buscarDetalhamento();
        });

        function buscarDetalhamento() {
            table.bootstrapTable('load', []);
            const formData = new FormData();
            formData.append('planejamento', cboPlanejamento.value);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: true,
                reportMessage: `Aguarde, esse procedimento pode demorar um pouco.`
            }
            HttpClient.post(`${PHPSession.requestApi}/${routs.projecao.buscar}`, parametros).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                modalCalculo.labelBaseCalculo.innerHTML = response.data.baseCalculo.descricao;
                modalCalculo.formaRecalculo.value = response.data.baseCalculo.codigo;

                let dados = response.data.projecao.map((projecao) => {
                    projecao.valores.map((valor) => {
                        projecao[`valor_ano_${valor.ano}`] = valor.valor;
                    });
                    return projecao;
                });

                table.bootstrapTable('load', dados);
                aplicaDBInputValor();
            });
        }

        window.operateEvents = {
            'click .informacaoProjecao': function (e, value, row, index) {
                modalInfo.instistuicao.value = `${row.pl20_instituicao} - ${row.instituicao.nomeinst}`;
                modalInfo.estrutural.value = row.estrutural;

                modalInfo.orgao.value = `${row.pl20_orcorgao} - ${row.descricao_orgao}`;
                modalInfo.unidade.value = `${row.pl20_orcunidade} - ${row.descricao_unidade}`;
                modalInfo.funcao.value = `${row.pl20_orcfuncao} - ${row.descricao_funcao}`;
                modalInfo.subfuncao.value = `${row.pl20_orcsubfuncao} - ${row.descricao_subfuncao}`;
                modalInfo.programa.value = `${row.pl9_orcprograma} - ${row.descricao_programa}`;
                modalInfo.iniciativa.value = `${row.pl12_orcprojativ} - ${row.descricao_iniciativa}`;
                modalInfo.elemento.value = `${row.elemento} - ${row.descricao_elemento}`;
                modalInfo.recurso.value = `${row.siconfi} - ${row.subrecurso} - ${row.complemento}`;
                windowInfo.show(20, 50);
                modalInfo.container.style.display = '';
            },
            'change .valor-editavel': function (e, value, row, index) {
                e.target.value = Number(e.target.value).toFixed(2);

                if (e.target.dataset.field === 'valorBase') {
                    calculaInflatores(e.target, row);
                    row.valorBase = e.target.value;
                    atualizaValorBase(row);
                }

                if (e.target.dataset.field !== 'valorBase') {
                    row.valores.each((valor) => {
                        let coluna = `valor_ano_${valor.ano}`
                        if (e.target.dataset.field === coluna) {
                            salvaAtualizacaoValorEstimativa(row.codigo, valor.ano, e.target.value);
                        }
                    })
                }
            }
        }

        /**
         * essa função vai ser usada quando não houver fator de correção para o elemento
         */
        const replicaValorBase = (elemento, linha) => {
            linha.valores.each(valor => {
                let field = `valor_ano_${valor.ano}`;
                let input = getInput(field, linha.codigo);
                input.value = elemento.value;
                salvaAtualizacaoValorEstimativa(linha.codigo, valor.ano, elemento.value);
            });
        };

        const calculaInflatores = (elemento, linha) => {

            let inflatores = linha.inflatores;

            if (inflatores === null) {
                replicaValorBase(elemento, linha)
                return;
            }

            let valor = elemento.value;
            inflatores.each(inflator => {
                let field = `valor_ano_${inflator.ano}`;
                let input = getInput(field, linha.codigo);

                if (inflator.deflator) {
                    inflator.percentual *= -1;
                }

                valor = Number(valor * (1 + (inflator.percentual / 100))).toFixed(2);
                input.value = valor;

                salvaAtualizacaoValorEstimativa(linha.codigo, inflator.ano, valor);
            });
        };

        const getInput = (field, codigo) => {
            return document.querySelector(`input[data-field="${field}"][data-id="${codigo}"]`);
        };

        const adicionaAcao = (value, row, index) => {
            return [
                '<a class="informacaoProjecao" href="javascript:void(0)" title="Clique para ver detalhado os dados">',
                '  <i class="fas fa-info"></i>',
                '</a>',
            ].join('&nbsp;');
        }

        const formataValor = (value, row, index) => {
            let numObj = parseFloat(value);

            return numObj.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        };

        const formataValorInput = (value, row, index, field) => {
            return `<input type="text" class="valor-editavel field-size-max text-right" data-field="${field}" data-id="${row.codigo}" value="${value}">`;
        };

        const montaTabela = () => {
            table.bootstrapTable('destroy');

            let colunas = montaColunas();

            table.bootstrapTable({
                columns: colunas,
                uniqueId: "codigo",
                locale: 'pt-BR',
                cache: false,
                height: 500,
                pagination: true,
                pageSize: 10,
                pageList: [10, 25, 50, 100, 200, 'All'],
                search: true,
                class: "table table-sm"
            });
        }

        const montaColunas = () => {
            const colunas = [
                {
                    title: 'Natureza',
                    field: 'estrutural',
                    align: 'center',
                    valign: 'middle',
                    sortable: true
                }, {
                    title: 'Instituição',
                    field: 'instituicao.nomeinst',
                    align: 'left',
                    valign: 'center',
                    sortable: true
                }, {
                    title: 'Vlr Base',
                    field: 'valorBase',
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
                    field: `valor_ano_${ano}`,
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

        const aplicaDBInputValor = () => {
            [...document.querySelectorAll('input.valor-editavel')].map((element) => {
                new DBInputValor(element);
            });
        }

        const atualizaValorBase = (linha) => {
            const formData = new FormData();
            formData.append('id', linha.codigo);
            formData.append('valor', linha.valorBase);
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

        const salvaAtualizacaoValorEstimativa = (id, ano, valor) => {

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


        modalCalculo.recalcular.addEventListener('click', () => {

            let forma = modalCalculo.formaRecalculo.options[modalCalculo.formaRecalculo.selectedIndex].innerHTML;

            let msg = `Tem certeza que deseja recalcular a projeção usando como base de cálculo "${forma}"?
            Todos valores manuais serão perdidos.`;

            alertify.confirm(msg, (e) => {
                if (e) {
                    table.bootstrapTable('load', []);
                    let rota = `${PHPSession.requestApi}/${routs.projecao.recalcular}`
                    let msg = 'Aguarde, recalculando projeção, esse procedimento pode demorar um pouco.';
                    executarRotaManutencaoPlanejamento(rota, msg);
                }
            });
        });

        const executarRotaManutencaoPlanejamento = (rota, msg) => {
            const formData = new FormData();
            formData.append('planejamento', planejamento.getValue());
            formData.append('formaRecalculo', modalCalculo.formaRecalculo.value);
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
            }).then(response => {
                buscarDetalhamento();
            });
        };

        modalCalculo.valoresSinteticos.addEventListener('click', () => {
            const formData = new FormData();
            formData.append('planejamento_id', planejamento.getValue());

            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: true,
                reportMessage: `Aguarde, esse procedimento pode demorar um pouco.`
            }
            let rota = routs.projecao.valoresSinteticos;
            HttpClient.post(`${PHPSession.requestApi}/${rota}`, parametros).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }
            });
        });

        modalCalculo.criarDetalhamento.addEventListener('click', () => {

            let forma = modalCalculo.formaRecalculo.options[modalCalculo.formaRecalculo.selectedIndex].innerHTML;

            let msg = `Tem certeza que deseja incluir as dotações orçamentárias criadas durante o exercício
              usando como base de cálculo "${forma}"?`;

            alertify.confirm(msg, (e) => {
                if (e) {
                    table.bootstrapTable('load', []);
                    let rota = `${PHPSession.requestApi}/${routs.projecao.criarDetalhamento}`
                    let msg = 'Aguarde, buscando dotações, esse procedimento pode demorar um pouco.';
                    executarRotaManutencaoPlanejamento(rota, msg);
                }
            });

        });
    });
</script>
</body>
