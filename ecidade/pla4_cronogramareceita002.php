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

    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Para editar os valores do cronograma de desembolso, clique em
    <kdb><i class="fas fa-calculator"></i></kdb> na coluna <b>ação</b>.
</div>
<div class="container">
    <fieldset>
        <legend>Cronograma</legend>
        <div id="formCronograma">

            <table id="data-table"
                   class="table table-sm"
                   data-locale="pt-BR"
                   data-cache="false"
                   data-height="600" ,
                   data-search="true"
                   style="width: 100%;">
            </table>
        </div>
    </fieldset>

    <button type="button" id="btnRecalcularGeral" class="btn btn-light">
        <i class="fas fa-calculator"></i>
        Recalcular Geral
    </button>
</div>

<div id="modalMetas" style="display: none">
    <div class="container">
        <fieldset>
            <legend>Metas de arrecadação <span id="exercicioMeta"></span></legend>
            <table class="form-container">
                <tr>
                    <td><label for="estruturalReceita">Estrutural:</label></td>
                    <td>
                        <input id="estruturalReceita" name="estruturalReceita"
                               class="readonly field-size3" readonly/>
                    </td>
                </tr>
                <tr>
                    <td><label for="descricaoReceita">Receita:</label></td>
                    <td>
                        <input id="descricaoReceita" name="descricaoReceita" class="readonly field-size8"
                               readonly/>
                    </td>
                </tr>
                <tr>
                    <td><label for="dadosOrgao">Orgão:</label></td>
                    <td>
                        <input id="dadosOrgao" name="dadosOrgao" class="readonly field-size8" readonly/>
                    </td>
                </tr>
                <tr>
                    <td><label for="dadosUnidade">Unidade:</label></td>
                    <td>
                        <input id="dadosUnidade" name="dadosUnidade" class="readonly field-size8" readonly/>
                    </td>
                </tr>
                <tr>
                    <td><label for="dadosInstituicao">Instituição:</label></td>
                    <td>
                        <input id="dadosInstituicao" name="dadosInstituicao" class="readonly field-size8"
                               readonly/>
                    </td>
                </tr>
                <tr>
                    <td><label for="valorMetaAnual">Meta Anual:</label></td>
                    <td>
                        <input type="text" id="valorMetaAnual" readonly class="readonly field-size3">
                    </td>
                </tr>
            </table>
            <fieldset class="separator">
                <legend>Competência</legend>
                <table class="form-container">
                    <tr style="border-bottom: 1px solid">
                        <th>Mês</th>
                        <th>Valor</th>
                    </tr>
                    <tr>
                        <td><label for="janeiro">Janeiro:</label></td>
                        <td><input type="text" id="janeiro" name="janeiro"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="fevereiro">Fevereiro:</label></td>
                        <td><input type="text" id="fevereiro" name="fevereiro"
                                   class="valoresCronograma field-size3">
                        </td>
                    </tr>
                    <tr>
                        <td><label for="marco">Março:</label></td>
                        <td><input type="text" id="marco" name="marco"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="abril">Abril:</label></td>
                        <td><input type="text" id="abril" name="abril"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="maio">Maio:</label></td>
                        <td><input type="text" id="maio" name="maio" class="valoresCronograma field-size3">
                        </td>
                    </tr>
                    <tr>
                        <td><label for="junho">Junho:</label></td>
                        <td><input type="text" id="junho" name="junho"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="julho">Julho:</label></td>
                        <td><input type="text" id="julho" name="julho"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="agosto">Agosto:</label></td>
                        <td><input type="text" id="agosto" name="agosto"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="setembro">Setembro:</label></td>
                        <td><input type="text" id="setembro" name="setembro"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="outubro">Outubro:</label></td>
                        <td><input type="text" id="outubro" name="outubro"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="novembro">Novembro:</label></td>
                        <td><input type="text" id="novembro" name="novembro"
                                   class="valoresCronograma field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="dezembro">Dezembro:</label></td>
                        <td><input type="text" id="dezembro" name="dezembro"
                                   class="valoresCronograma field-size3"></td>
                    </tr>

                    <tr style="border-top: 1px solid">
                        <td><label for="total">Total:</label></td>
                        <td>
                            <input type="text" id="totalCronograma" class="bold field-size3" readonly>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>

        <input type="hidden" id="codigoEstimativa" name="id">
        <button type="button" id="btnRecalcularCronograma" class="btn btn-light">
            <i class="fas fa-calculator"></i>
            Recalcular
        </button>
        <button type="button" id="btnSalvarCronograma" class="btn btn-light">
            <i class="far fa-save"></i>
            Salvar
        </button>
    </div>
</div>

<div id="modalRecalculoCronograma" style="display: none">
    <div class="container">
        <fieldset>
            <legend>Recalcule o cronograma</legend>
            <table class="form-container">
                <tr>
                    <td><label for="formulaRecalculo">Fórmula:</label></td>
                    <td>
                        <select id="formulaRecalculo">
                            <option value="1">Dividir recursos alocados por 12</option>
                            <option value="2">Aplicar total do recurso em um mês</option>
                        </select>
                    </td>
                </tr>
                <tr style="display: none;" id="linhaMeses">
                    <td><label for="mesRecalculo">Mês:</label></td>
                    <td><select id="mesRecalculo">
                            <option value="janeiro">Janeiro</option>
                            <option value="fevereiro">Fevereiro</option>
                            <option value="marco">Março</option>
                            <option value="abril">Abril</option>
                            <option value="maio">Maio</option>
                            <option value="junho">Junho</option>
                            <option value="julho">Julho</option>
                            <option value="agosto">Agosto</option>
                            <option value="setembro">Setembro</option>
                            <option value="outubro">Outubro</option>
                            <option value="novembro">Novembro</option>
                            <option value="dezembro">Dezembro</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <button type="button" id="btnSalvarRecalculo" class="btn btn-light">
            <i class="far fa-save"></i>
            Salvar
        </button>
    </div>
</div>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>
<!-- requires alertfy -->
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript"
        src="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>

<!--<link type="text/css" href="assets/bootstrap-4.5.3/css/bootstrap.min.css" rel="stylesheet">-->
<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="estilos.css"/>
<link type="text/css" href="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css"
      rel="stylesheet">

<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>

<script type="text/javascript">

    $.noConflict();
    jQuery(document).ready(function ($) {
        //
        const containerCronograma = document.getElementById('formCronograma');
        let screenWidth = window.screen.availWidth - 50;
        containerCronograma.style.width = `${screenWidth}px`;

        /**
         * @type {*[]}
         */
        var estimativas = [];
        var linhaEditandoMeta = {};

        const get = js_urlToObject();
        const routs = {
            buscar: 'financeiro/planejamento/receita/cronograma/buscar',
            salvarMetas: 'financeiro/planejamento/receita/cronograma/salvar-metas',
            recalcular: 'financeiro/planejamento/receita/cronograma/recalcular',
        }

        const modalMetas = {
            container: document.getElementById('modalMetas'),
            estruturalReceita: document.getElementById('estruturalReceita'),
            descricaoReceita: document.getElementById('descricaoReceita'),
            dadosOrgao: document.getElementById('dadosOrgao'),
            dadosUnidade: document.getElementById('dadosUnidade'),
            dadosInstituicao: document.getElementById('dadosInstituicao'),
            codigoEstimativa: document.getElementById('codigoEstimativa'),
            btnRecalcularCronograma: document.getElementById('btnRecalcularCronograma'),
            btnSalvarCronograma: document.getElementById('btnSalvarCronograma'),
            valorMetaAnual: document.getElementById('valorMetaAnual'),
            janeiro: document.getElementById('janeiro'),
            fevereiro: document.getElementById('fevereiro'),
            marco: document.getElementById('marco'),
            abril: document.getElementById('abril'),
            maio: document.getElementById('maio'),
            junho: document.getElementById('junho'),
            julho: document.getElementById('julho'),
            agosto: document.getElementById('agosto'),
            setembro: document.getElementById('setembro'),
            outubro: document.getElementById('outubro'),
            novembro: document.getElementById('novembro'),
            dezembro: document.getElementById('dezembro'),
            inputTotalCronograma: document.getElementById('totalCronograma'),
            totalizador: 0
        };


        const modalRecalculo = {
            container: document.getElementById('modalRecalculoCronograma'),
            formula: document.getElementById('formulaRecalculo'),
            linhaMeses: document.getElementById('linhaMeses'),
            mes: document.getElementById('mesRecalculo'),
            btnSalvar: document.getElementById('btnSalvarRecalculo'),
        };


        const formataValor = (value, row, index) => {
            let numObj = parseFloat(value);
            return numObj.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        };

        const formataValorInput = (value, row, index, field) => {
            return formataValor(value);
        };

        const adicionaRecalcular = (value, row, index) => {
            if ((row.id === null && row.contasDesdobramento.length === 0) ||
                (row.id !== null && row.temDesdobramento)) {
                return '';
            }

            return `
                <a class="fas fa-calculator recalcular" href="javascript:void(0)"
                   title="Alterar metas da receita" style="min-width: 30px"></a>
            `;
        };

        window.operateEvents = {
            'click .recalcular': function (e, value, row, index) {
                windowMetas.show(0, 0, true);
                modalMetas.container.style.display = ''
                linhaEditandoMeta = row;
                montaFormMetas(row);
            }
        };


        const createColumn = (title, field, align, width) => {
            return {
                title: title,
                field: field,
                halign: 'center',
                valign: 'middle',
                align: align,
                width: width,
            }
        };

        const createColumnValor = (title, field) => {
            return {
                title: title,
                field: field,
                halign: 'center',
                align: 'right',
                valign: 'middle',
                width: 120,
                formatter: formataValorInput
            };

        }


        const montaColunas = () => {
            let descricao = createColumn('Descrição', 'descricao_fonte', 'left', 350);
            descricao.formatter = (value) => {
                return `
                    <div style="width: 340px" title="${value}">
                      <div class="elipse">${value}</div>
                    </div>
                `;
            };

            let recalcular = createColumn('Ação', 'acao', 'center', 50)
            recalcular.events = window.operateEvents;
            recalcular.formatter = adicionaRecalcular;

            const colunas = [];
            colunas.push(createColumn('Natureza', 'fonte_mascara', 'left', 150));
            colunas.push(descricao);
            colunas.push(createColumn('Recurso', 'fonte_recurso', 'center', 90));
            colunas.push(createColumn('CO', 'codigo_complemento', 'center', 50));
            colunas.push(createColumn('CP', 'concarpeculiar_id', 'center', 40));
            colunas.push(createColumnValor('Meta Anual', 'valor_base'));
            colunas.push(recalcular);
            colunas.push(createColumnValor('Janeiro', 'janeiro'));
            colunas.push(createColumnValor('Fevereiro', 'fevereiro'));
            colunas.push(createColumnValor('Março', 'marco'));
            colunas.push(createColumnValor('Abril', 'abril'));
            colunas.push(createColumnValor('Maio', 'maio'));
            colunas.push(createColumnValor('Junho', 'junho'));
            colunas.push(createColumnValor('Julho', 'julho'));
            colunas.push(createColumnValor('Agosto', 'agosto'));
            colunas.push(createColumnValor('Setembro', 'setembro'));
            colunas.push(createColumnValor('Outubro', 'outubro'));
            colunas.push(createColumnValor('Novembro', 'novembro'));
            colunas.push(createColumnValor('Dezembro', 'dezembro'));

            return colunas;
        }

        var table = $('#data-table');
        table.bootstrapTable({
            columns: montaColunas(),
            data: estimativas,
        }).bootstrapTable('showLoading');

        const criaTabela = () => {
            table.bootstrapTable('destroy').bootstrapTable({
                columns: montaColunas(),
                data: estimativas,
                fixedColumns: true,
                fixedNumber: 6,
            });
        }

        const buscaCronogramaDesembolso = () => {
            const formData = new FormData();
            formData.append('planejamento_id', get.plano);
            formData.append('exercicio', get.exercicio);
            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando as estimativas de receitas.`
            };
            HttpClient.post(`${PHPSession.requestApi}/${routs.buscar}`, parametros).then(response => {

                estimativas = response.data;
                for (let estimativa of estimativas) {
                    estimativa.idLinha = estimativa.fonte;
                    if (estimativa.concarpeculiar_id !== null) {
                        estimativa.idLinha = `${estimativa.fonte}#${estimativa.concarpeculiar_id}`
                    }
                }

                criaTabela();
            });
        };

        PHPSession.loadData().then(() => {
            buscaCronogramaDesembolso();
        });

        modalMetas.btnRecalcularCronograma.addEventListener('click', () => {
            windowRecalculo.show(0, 0, true);
            modalRecalculo.container.style.display = ''
        });

        var windowRecalculo = new windowAux('windowRecalculo', 'Recalcula o cronograma', 450, 300);
        windowRecalculo.setContent(modalRecalculo.container);
        windowRecalculo.allowCloseWithEsc(false);

        var windowMetas = new windowAux('windowMetas', 'Metas de Arrecadação', 550, 650);
        windowMetas.setContent(modalMetas.container);
        windowMetas.allowCloseWithEsc(false);
        windowMetas.add(windowRecalculo)
        windowMetas.setShutDownFunction(() => {
            if (!!windowMetas.oDBMask) {
                windowMetas.oDBMask.destroy();
            }
        });

        /**
         * retorna os elementos de valores do cronograma
         * @returns {*[]}
         */
        const getElementosValoresCronograma = () => {
            return [...document.querySelectorAll('input.valoresCronograma')];
        };

        /**
         * define o change para o valor do cronograma
         */
        modalMetas.inputTotalCronograma.addEventListener('change', () => {
            let valorTotal = 0;
            getElementosValoresCronograma().map(elemento => {
                valorTotal += Number(elemento.value.replace(',', '.'));
            });

            valorTotal = valorTotal.toFixed(2);
            modalMetas.totalizador = valorTotal;
            modalMetas.inputTotalCronograma.value = formataValor(valorTotal);
            modalMetas.inputTotalCronograma.classList.remove('alert-danger');
            if (modalMetas.totalizador > linhaEditandoMeta.valor_base ||
                modalMetas.totalizador < linhaEditandoMeta.valor_base) {
                modalMetas.inputTotalCronograma.classList.add('alert-danger');
            }
        });

        /**
         * define os eventos dos inputs de valores do cronograma
         */
        getElementosValoresCronograma().map(elemento => {
            new DBInputValor(elemento);
            elemento.addEventListener('change', () => {
                modalMetas.inputTotalCronograma.dispatchEvent(new Event('change'));
            });
        });

        const montaFormMetas = (linha) => {
            modalMetas.estruturalReceita.value = linha.fonte;
            modalMetas.descricaoReceita.value = linha.descricao_fonte;
            modalMetas.valorMetaAnual.value = formataValor(linha.valor_base);
            if (linha.id !== null) {
                modalMetas.dadosOrgao.value = `${linha.orcunidade_id} - ${linha.descricao_orgao}`
                modalMetas.dadosUnidade.value = `${linha.orcorgao_id} - ${linha.descricao_unidade}`
                modalMetas.dadosInstituicao.value = `${linha.nome_instituicao}`
            }

            modalMetas.janeiro.value = linha.janeiro;
            modalMetas.fevereiro.value = linha.fevereiro;
            modalMetas.marco.value = linha.marco;
            modalMetas.abril.value = linha.abril;
            modalMetas.maio.value = linha.maio;
            modalMetas.junho.value = linha.junho;
            modalMetas.julho.value = linha.julho;
            modalMetas.agosto.value = linha.agosto;
            modalMetas.setembro.value = linha.setembro;
            modalMetas.outubro.value = linha.outubro;
            modalMetas.novembro.value = linha.novembro;
            modalMetas.dezembro.value = linha.dezembro;

            modalMetas.inputTotalCronograma.dispatchEvent(new Event('change'));
        };

        /**
         *
         * @param estimativa É um objeto dentro das estimativas... representa uma linha
         * @param percentual valor percentual para aplicar
         */
        const recalculaLinhaDesdobrada = (estimativa, percentual) => {
            getElementosValoresCronograma().map(elemento => {
                let valor = elemento.value.replace(',', '.');
                estimativa[elemento.name] = Number((valor * percentual) / 100).toFixed(2);
                return estimativa;
            });
        };

        const atualizaMetaContasDesdobramento = (linha) => {
            return linha.contasDesdobramento.map((conta) => {
                let estimativa = buscaLinhaEstimativa(`${conta.fonte}#${conta.cp}`)
                recalculaLinhaDesdobrada(estimativa, conta.percentual);
                return estimativa;
            });
        };

        /**
         * busca o elemento dentro da coleção de estimativas
         * @type {{}}
         */
        const buscaLinhaEstimativa = (idLinha) => {
            let index = estimativas.findIndex(obj => obj.idLinha === idLinha);
            return estimativas[index];
        };

        modalMetas.btnSalvarCronograma.addEventListener('click', () => {
            if (modalMetas.totalizador > linhaEditandoMeta.valor_base) {
                let passou = modalMetas.totalizador - linhaEditandoMeta.valor_base;
                alert(`Valor total das metas esta maior que a Meta Anual.\nValor faltante ${formataValor(passou)}.`);
                return;
            }
            if (modalMetas.totalizador < linhaEditandoMeta.valor_base) {
                let passou = linhaEditandoMeta.valor_base - modalMetas.totalizador;
                alert(`Valor total das metas esta menor que a Meta Anual.\nValor faltante ${formataValor(passou)}.`);
                return;
            }

            // atualiza os valores no objeto da linha que esta sendo editada.
            getElementosValoresCronograma().map(elemento => {
                let estimativa = buscaLinhaEstimativa(linhaEditandoMeta.idLinha);
                estimativa[elemento.name] = elemento.value.replace(',', '.');
                linhaEditandoMeta[elemento.name] = elemento.value.replace(',', '.');
            });

            let metasArrecacadaco = []
            if (linhaEditandoMeta.id === null && linhaEditandoMeta.contasDesdobramento.length > 0) {
                metasArrecacadaco = atualizaMetaContasDesdobramento(linhaEditandoMeta);
            } else {
                metasArrecacadaco.push(linhaEditandoMeta);
            }

            const formData = new FormData();
            formData.append('metasArrecacadacao', JSON.stringify(metasArrecacadaco));
            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, salvando as estimativas de receitas.`
            };
            HttpClient.post(`${PHPSession.requestApi}/${routs.salvarMetas}`, parametros).then(response => {

                if (response.error) {
                    alert(response.message);
                    return
                }

                windowMetas.destroy();
                table.bootstrapTable('load', estimativas);
            });
        });


        modalRecalculo.formula.addEventListener('change', () => {
            let formula = modalRecalculo.formula.value;
            modalRecalculo.linhaMeses.style.display = formula == 1 ? 'none' : 'table-row';
        });

        modalRecalculo.btnSalvar.addEventListener('click', () => {
            let metasArrecacadaco = []
            if (linhaEditandoMeta.id === null && linhaEditandoMeta.contasDesdobramento.length > 0) {
                metasArrecacadaco = estimativasDesdobramento(linhaEditandoMeta);
            } else {
                metasArrecacadaco.push(linhaEditandoMeta);
            }
            const formData = new FormData();
            formData.append('exercicio', get.exercicio);
            formData.append('formula', modalRecalculo.formula.value);
            formData.append('mes', modalRecalculo.mes.value);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, recalculando as metas de receitas.`
            };
            for (let meta of metasArrecacadaco) {
                formData.append('estimativas[]', meta.estimativareceita_id);
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.recalcular}`, parametros).then(response => {

                if (response.error) {
                    alert(response.message);
                    return
                }

                windowMetas.destroy();
                buscaCronogramaDesembolso();
            });
        });

        const estimativasDesdobramento = (linha) => {
            return linha.contasDesdobramento.map((conta) => {
                let idLinha = `${conta.fonte}#${conta.cp}`;
                return buscaLinhaEstimativa(idLinha);
            });
        };

        document.getElementById('btnRecalcularGeral').addEventListener('click', () => {
            let msg = 'Tem certeza que deseja recalcular TODOS os valores do cronograma da Receita? \nTodos os ';
            msg += 'valores editados manualmente serão perdidos. \nEsse procedimento poderá demorar alguns minutos.';
            alertify.confirm(`${msg}`, (e) => {
                if (e) {
                    const formData = new FormData();
                    formData.append('formula', 1);
                    // formData.append('mes', 'janeiro');
                    formData.append('exercicio', get.exercicio);

                    for (let estimativa of estimativas) {
                        if (estimativa.id !== null ) {
                            formData.append('estimativas[]', estimativa.estimativareceita_id);
                        }
                    }

                    const parametros = {
                        body: formData,
                        reportMessage: `Aguarde, recalculando metas.`
                    };

                    HttpClient.post(`${PHPSession.requestApi}/${routs.recalcular}`, parametros).then(response => {

                        if (response.error) {
                            alert(response.message);
                            return
                        }

                        windowMetas.destroy();
                        buscaCronogramaDesembolso();
                    });
                }
            });
        });
    });
</script>
