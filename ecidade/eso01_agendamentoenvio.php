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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

//PLUGIN ENVIOESOCIAL - Usuários configurados
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBSeller Serviços de Informática Ltda</title>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script src="scripts/scripts.js" rel="script" type="text/javascript"></script>
    <script src="scripts/prototype.js" rel="script" type="text/javascript"></script>
    <script src="scripts/object.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInput.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/DBInputHora.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputCep.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputCNPJ.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputCpf.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputDate.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputInteger.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputTelefone.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputValor.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputCheckboxRadio.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBCheckBox.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBRadio.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/Collection.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/DBLancador.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/avaliacao/DBViewFormulario.classe.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/avaliacao/DBViewGrupoPerguntas.classe.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/avaliacao/DBViewPergunta.classe.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/avaliacao/DBViewResposta.classe.js" rel="script" type="text/javascript"></script>
    <script src="scripts/AjaxRequest.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/http/http.js" rel="script" type="text/javascript"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js" rel="script"
            type="text/javascript"></script>
    <script src="scripts/classes/DBViewFormularioFolha/CaixaFolha.js" rel="script"
            type="text/javascript"></script>
    <style>
        #competenciaTr input[type="text"] {
            width: inherit;
        }
    </style>
</head>
<body>
<form class="container">
    <fieldset>
        <legend><span id="span_envio"></span> de informações para o <span class="span_integracao"></span></legend>
        <table class='form-container'>
            <tr id="tr_empregador" class="d-none">
                <td>
                    <label for="empregador">Empregador:</label>
                </td>
                <td>
                    <select name="empregador" id="empregador"></select>
                </td>
            </tr>
            <tr id="tr_contribuinte" class="d-none">
                <td>

                    <label for="descricao">Contribuinte:</label>
                </td>
                <td>
                    <select id="contribuinte" name="contribuinte"></select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="arquivos">Arquivo:</label>
                </td>
                <td>
                    <select id="arquivos" name="arquivos" class="field-size-max">
                        <option value="">Selecione...</option>
                    </select>
                </td>
            </tr>
            <tr id="tr_filtros" class="d-none">
                <td>
                    <label for="filtroOpcao">Filtrar por:</label>
                </td>
                <td>
                    <select id="filtroOpcao" name="filtroOpcao" class="field-size-max">
                        <option value="">Selecione...</option>
                    </select>
                </td>
            </tr>
            <tr id="periodoDataTr" class="d-none">
                <td>
                    <label for="indicativoPeriodoData">Período de Apuração:</label>
                </td>
                <td>
                    <div>
                        <span><strong>de</strong></span>
                        <input type="date" id="dataInicial">
                        <span><strong>até</strong></span>
                        <input type="date" id="dataFinal">
                    </div>
                </td>
            </tr>
            <tr id="periodoDataPreenchimentoTr" class="d-none">
                <td>
                    <label for="indicativoDataPreenchimento">Data de Preenchimento:</label>
                </td>
                <td>
                    <div style="display: flex; justify-content: center; align-items: center;">
                        <span style="padding: 0 2px 0 0"><strong>de</strong></span>
                        <input type="date" id="dataPreenchimentoInicial">
                        <span style="padding: 0 2px"><strong>até</strong></span>
                        <input type="date" id="dataPreenchimentoFinal">
                    </div>
                </td>
            </tr>
            <tr id="selecaoTr" class="d-none">
                <td>
                    <a href="#"><label for="r44_selec" id="lblSelecao">Sele&ccedil;&atilde;o:</label></a>
                </td>
                <td>
                    <?php db_input('r44_selec', null, 1, true, 'text', 2); ?>
                    <?php db_input('r44_descr', null, 0, true, 'text', 3); ?>
                </td>
            </tr>
            <tr id="selecaoTrMensagem"  class="d-none">
                <td id="selecaoMensagem" colspan="2">
                </td>
            </tr>
            <tr id="caixaTrMensagem"  class="d-none">
                <td id="caixaMensagem" colspan="2">
                </td>
            </tr>
            <tr id="tipoDataPagamentoTr" class="d-none">
                <td>
                    <label for="tipoDataPagamento">Data Pagamento:</label>
                </td>
                <td>
                    <select id="tipoDataPagamento" name="tipoDataPagamento" class="field-size-max">
                        <option value="">Selecione...</option>
                        <option value="ultimoDia">Ultimo dia da competência</option>
                        <option value="periodoAtual">Dia do pagamento preenchida no fechamento da folha</option>
                    </select>
                </td>
            </tr>

            <tr id="indFechReabTr" class="d-none">
                <td><label for="indFechReab">Indicativo: </label></td>
                <td>
                    <select id="indFechReab" name="indFechReab" class="field-size-max">
                        <option value="">Selecione...</option>
                        <option value="0">Fechamento</option>
                        <option value="1">Reabertura</option>
                    </select>
                </td>
            </tr>

            <!-- Adicionado filtro para rubrica -->
            <tr id="tr_rubrica" class="d-none">
                <td colspan="2" id="td_rubrica" style="padding-top: 15px"></td>
            </tr>
            <tr id="tr_matricula" class="d-none">
                <td colspan="2" id="td_matricula" style="padding-top: 15px"></td>
            </tr>
            <tr id="matriculaTrMensagem"  class="d-none">
                <td id="matriculaMensagem" colspan="2">
                </td>
            </tr>
            <tr id="filtrosPeriodoApuracao"  class="d-none">
                <td>
                    <label for="indicativoPeriodoApuracao">Indicativo de Período de Apuração:</label>
                </td>
                <td>
                    <select id="indicativoPeriodoApuracao" name="indicativoPeriodoApuracao" class="field-size-max">
                        <option value="">Selecione...</option>
                        <option value="1">Mensal</option>
                        <option value="2">Anual (13° salário)</option>
                    </select>
                </td>
            </tr>
            <tr id="indicativoPeriodoApuracaoTrMensagem"  class="d-none">
                <td id="indicativoPeriodoApuracaoMensagem" colspan="2">
                </td>
            </tr>
            <tr id="caixaTr" class="d-none">
                <td id="labelCaixa"></td>
                <td id="formularioCaixa"></td>
            </tr>
            <tr id="competenciaTr" class="d-none">
                <td id="labelCompetencia"></td>
                <td id="formularioCompetencia"></td>
            </tr>
            <tr id="competenciaTrMensagem"  class="d-none">
                <td id="competenciaMensagem" colspan="2">
                </td>
            </tr>
            <!-- Adicionado Filtro para forçar a matricula-->
            <tr id="tr_forcarmatricula" class="d-none">
                <td colspan="2" id="td_matricula" style="padding-top: 15px">
                    <input type="checkbox" id="forcarMatricula">
                    <label for="forcarMatricula">Deseja processar os dados de remuneração somente das Matrículas informadas?</label>
                </td>
            </tr>
            <tr id="tr_forcarmatriculaTexto" class="d-none">
                <td colspan="2" id="td_matricula" >
                    <p style="font-weight: normal;">O campo acima é utilizado para Funcionários que possuem mais de uma matrícula/vinculo e que se deseja o envio</p>
                    <p style="font-weight: normal;"> somente de uma matrícula em questão.</p>
                </td>
            </tr>
            <!-- Fim Filtro para forçar a matricula-->
        </table>
    </fieldset>
    <fieldset id='notasfield'>
        <legend>Notas</legend>
        <div id='notasreenvio'>
            <p style="text-align: left">Esta rotina é destinada a reenviar <strong>eventos</strong> que não foram diretamente alterados, mas devido alteração de outros eventos, </p>
            <p style="text-align: left">o mesmo precisa ser reenviado para atualização no portal do <span class="span_integracao"></span>.</p>
        </div>
    </fieldset>
    <input type="button" id="processarDados" value="Processar Dados">
    <input type="button" id="enviarApi" value="Enviar">
    <!-- PLUGIN ENVIOESOCIAL - Container botões Envio -->

    <script rel="script" type="text/javascript">
        var dbLancadorMatricula = new DBLancador('dbLancadorMatricula');
        var text = '';
        var dbLancadorRubrica = new DBLancador('dbLancadorRubrica');

        (() => {
            const EFD_REINF = '1';
            const ESOCIAL = '2';
            const urlParams = new URLSearchParams(window.location.search);
            const integracao = urlParams.has('integracao') ? urlParams.get('integracao') : '2';
            const trEmpregador = document.getElementById('tr_empregador');
            const trContribuinte = document.getElementById('tr_contribuinte');
            const trMatricula = document.getElementById('tr_matricula');
            const matriculaTrMensagem = document.getElementById('matriculaTrMensagem');
            const matriculaMensagem =  document.getElementById('matriculaMensagem');
            const trFiltros = document.getElementById('tr_filtros');
            const selectEmpregador = document.getElementById('empregador');
            const selectArquivos = document.getElementById('arquivos');
            const selectContribuinte = document.getElementById('contribuinte');
            const buttonProcessar = document.getElementById('processarDados');
            const buttonEnviar = document.getElementById('enviarApi');
            const spanIntegracao = document.getElementsByClassName('span_integracao');
            const spanEnvio = document.getElementById('span_envio');
            const competenciaTr = document.getElementById('competenciaTr');
            const competenciaTrMensagem = document.getElementById('competenciaTrMensagem');
            const labelCompetencia = document.getElementById('labelCompetencia');
            const competenciaMensagem = document.getElementById('competenciaMensagem');
            const formularioCompetencia = document.getElementById('formularioCompetencia');
            const filtrosPeriodoApuracao = document.getElementById('filtrosPeriodoApuracao');
            const trSelecao = document.getElementById('selecaoTr');
            const selecaoMensagem = document.getElementById('selecaoMensagem');
            const selecaoTrMensagem = document.getElementById('selecaoTrMensagem');
            const selectFiltros = document.getElementById('filtroOpcao');
            //Adicionado Filtro para forcar a matricula
            const trForcarMatricula = document.getElementById('tr_forcarmatricula');
            const trForcarMatriculaTexto = document.getElementById('tr_forcarmatriculaTexto');
            const inputForcarMatricula = document.getElementById('forcarMatricula');
            //Fim  Filtro para forcar a matricula
            const periodoDataTr = document.getElementById('periodoDataTr');
            const indicativoPeriodoApuracaoTr = document.getElementById('filtrosPeriodoApuracao');
            const periodoDataPreenchimentoTr = document.getElementById('periodoDataPreenchimentoTr');
            const dataPreenchimentoInicial = document.getElementById('dataPreenchimentoInicial');
            const dataPreenchimentoFinal = document.getElementById('dataPreenchimentoFinal');
            const dataInicial = document.getElementById('dataInicial');
            const dataFinal = document.getElementById('dataFinal');
            const indicativoPeriodoApuracaoTrMensagem = document.getElementById('indicativoPeriodoApuracaoTrMensagem');
            const notasField = document.getElementById('notasfield');
            const notasReenvio = document.getElementById('notasreenvio');
            const trRubrica = document.getElementById('tr_rubrica');
            const labelCaixa = document.getElementById('labelCaixa');
            const formularioCaixa = document.getElementById('formularioCaixa');
            const caixaTr = document.getElementById('caixaTr');

            var mensagemValidar = '';
            var tipoValidacaoValidar = '';
            var filtros;
            var dadosSelecao;
            var dadosMatricula;
            var dadosCompetencia;
            var dadosPeriodoData;
            var dadosDataPreenchimento;
            var dadosTipoDataPagamento;
            var dadosIndicativoPeriodoApuracao;
            var boolReenvio = urlParams.has('reenvio') ? urlParams.get('reenvio') : false;

            const validar = () => {
                return new Promise((resolve, reject) => {
                    if (integracao === EFD_REINF && selectContribuinte.value === '') {
                        return reject('O campo "Contribuinte" é obrigatório.');
                    }

                    if (integracao === ESOCIAL && selectEmpregador.value === '') {
                        return reject('O campo "Empregador" é obrigatório.');
                    }
                    resolve();
                });
            };


            const clickButtonProcessar = () => {
                if (selectArquivos.value === '') {
                    return alert('O campo "Arquivo" é obrigatório.');
                }
                if (!validaMensagemCampo()) {
                    return false;
                }

                validar().then(() => {
                    const formData = new FormData();
                    const parametros = {
                        exec: 'agendarEnvioLote',
                        layout: selectArquivos.value,
                        ano: null,
                        mes: null
                    };

                    parametros.indicativoPeriodoApuracao = $F('indicativoPeriodoApuracao');
                    parametros.ano = formularioCompetencia.querySelector("#ano").value;
                    parametros.mes = formularioCompetencia.querySelector("#mes").value;
                    parametros.anocaixa = formularioCaixa.querySelector("#anoCaixa").value;
                    parametros.mescaixa = formularioCaixa.querySelector("#mesCaixa").value;
                    parametros.tipoDataPagamento = $F("tipoDataPagamento");
                    parametros.selecao = $F("r44_selec");
                    parametros.dataInicio = dataInicial.value;
                    parametros.forcarMatricula = inputForcarMatricula.checked;
                    parametros.dataFim = dataFinal.value;
                    parametros.dataPreenchidaInicio = dataPreenchimentoInicial.value;
                    parametros.dataPreenchidaFim = dataPreenchimentoFinal.value;
                    parametros.forcado = boolReenvio;

                    if (integracao === EFD_REINF) {
                        parametros.cgm = selectContribuinte.value;

                        if (document.querySelector('#indFechReab').value.length) {
                            parametros.indFechReab = document.querySelector('#indFechReab').value;
                        }
                    }

                    if (integracao === ESOCIAL) {
                        parametros.cgm = selectEmpregador.value;

                        const registros = dbLancadorMatricula.getRegistros();

                        if (registros.length > 0) {
                            parametros.matriculas = [];

                            registros.each(registro => {
                                parametros.matriculas.push(registro.sCodigo);
                            });
                        }

                        const registrosRubrica = dbLancadorRubrica.getRegistros();

                        if (registrosRubrica.length > 0) {
                            parametros.rubricas = [];

                            registrosRubrica.each(registrosRubrica => {
                                parametros.rubricas.push(registrosRubrica.sCodigo);
                            });
                        }

                    }

                    formData.append('json', JSON.stringify(parametros));

                    HttpClient.post('eso4_esocialapi.RPC.php', {
                        body: formData
                    }).then(response => {
                        alert(response.sMessage);
                    });
                }).catch(e => alert(e));
            };
            selectArquivos.addEventListener('change', () => {
                resetaCampos();
            });

            const clickButtonEnviar = () => {
                validar().then(() => {
                    var mensagemConfirmacao = `Deseja realizar o envio das informações para o ${text}? Esse processo pode demorar alguns instantes.`;

                    if (selectArquivos.value === '') {
                        mensagemConfirmacao = `Enviar os dados dos preenchimentos para o ${text} sem um filtro de arquivo pode demorar alguns minutos, deseja continuar?`;
                    }

                    if (confirm(mensagemConfirmacao)) {
                        const formData = new FormData();
                        const parametros = {
                            exec: 'enviarEventosParaApi',
                            layout: selectArquivos.value
                        };

                        if (integracao === EFD_REINF) {
                            parametros.cgm = selectContribuinte.value;
                        }

                        if (integracao === ESOCIAL) {
                            parametros.cgm = selectEmpregador.value;
                            parametros.ano = formularioCompetencia.querySelector("#ano").value;
                            parametros.mes = formularioCompetencia.querySelector("#mes").value;
                            parametros.tipoDataPagamento = $F("tipoDataPagamento");
                            parametros.forcarMatricula = inputForcarMatricula.checked;
                            parametros.dataInicio = dataInicial.value;
                            parametros.dataFim = dataFinal.value;
                            parametros.dataPreenchidaInicio = dataPreenchimentoInicial.value;
                            parametros.dataPreenchidaFim = dataPreenchimentoFinal.value;
                            parametros.selecao = $F("r44_selec");
                            parametros.indicativoPeriodoApuracao = $F('indicativoPeriodoApuracao');

                            parametros.anoCaixa = formularioCaixa.querySelector("#anoCaixa").value;
                            parametros.mesCaixa = formularioCaixa.querySelector("#mesCaixa").value;

                            const registros = dbLancadorMatricula.getRegistros();

                            if (registros.length > 0) {
                                parametros.matriculas = [];

                                registros.each(registro => {
                                    parametros.matriculas.push(registro.sCodigo);
                                });
                            }

                            const registrosRubrica = dbLancadorRubrica.getRegistros();

                            if (registrosRubrica.length > 0) {
                                parametros.rubricas = [];

                                registrosRubrica.each(registrosRubrica => {
                                    parametros.rubricas.push(registrosRubrica.sCodigo);
                                });
                            }

                        }

                        formData.append('json', JSON.stringify(parametros));

                        HttpClient.post('eso4_esocialapi.RPC.php', {
                            body: formData
                        }).then(response => {
                            alert(response.sMessage);
                        });
                    }
                }).catch(e => alert(e));
            };

            const buscarArquivos = () => {
                const formData = new FormData();
                const parametros = {
                    'exec': 'getTipos',
                    'integracao': integracao
                };

                parametros.forcado = boolReenvio;


                formData.append('json', JSON.stringify(parametros));

                HttpClient.post('eso4_esocialapi.RPC.php', {
                    body: formData
                }).then(response => {
                    if (response.erro) {
                        throw response.mensagem;
                    }

                    response.tipos.map(tipo => {
                        selectArquivos.add(new Option(tipo.titulo, tipo.layout));
                    });
                }).catch(mensagem => alert(mensagem));
            };

            const adicionarListeners = () => {
                buttonProcessar.addEventListener('click', clickButtonProcessar);
                buttonEnviar.addEventListener('click', clickButtonEnviar);
            };

            var dbViewFormularioFolha = new DBViewFormularioFolha.CompetenciaFolha(false);
            dbViewFormularioFolha.renderizaLabel(labelCompetencia);
            dbViewFormularioFolha.renderizaFormulario(formularioCompetencia);
            var dbViewFormularioFolhaCaixa = new DBViewFormularioFolha.CaixaFolha(false);
            dbViewFormularioFolhaCaixa.renderizaLabel(labelCaixa);
            dbViewFormularioFolhaCaixa.renderizaFormulario(formularioCaixa);

            const inicializarFiltroCompetencia = exibeCompetencia => {
                dadosCompetencia = exibeCompetencia;
                selectArquivos.addEventListener('change', () => {
                    changeCompetencia();
                });
            };
            const inicializarFiltroPeriodoData = exibePeriodoData => {
                dadosPeriodoData = exibePeriodoData;
                selectArquivos.addEventListener('change', () => {
                    changePeriodoData();
                });
            };
            const inicializarFiltroDataPreenchimento = exibeDataPreenchimento => {
                dadosDataPreenchimento = exibeDataPreenchimento;
                selectArquivos.addEventListener('change', () => {
                    changeDataPreenchimento();
                });
            };
            const inicializarFiltroMatriculas = exibeMatricula => {
                dadosMatricula = exibeMatricula;
                selectArquivos.addEventListener('change', () => {
                    changeMatricula();
                });
            };
            const inicializarFiltroOpcao = exibeFiltro => {
                filtros = exibeFiltro;
                selectArquivos.addEventListener('change', () => {
                    trFiltros.classList.remove('d-none');
                    const arquivo = parseInt(selectArquivos.value);
                    selectFiltros.innerHTML = '';
                    if (exibeFiltro.hasOwnProperty(arquivo)) {
                        exibeFiltro[arquivo].opcoes.map(tipo => {
                            selectFiltros.add(new Option(tipo.titulo, tipo.tipo));
                        });
                        inicializaFiltro();
                    } else {
                        trFiltros.classList.add('d-none');
                    }
                });
            };
            const inicializarIndicativoPeriodoApuracao = (exibeIndicativoPeriodoApuracao) => {
                dadosIndicativoPeriodoApuracao = exibeIndicativoPeriodoApuracao;
                selectArquivos.addEventListener('change', () => {
                    changeIndicativoPeriodoApuracao();

                });
            };

            const inicializarFiltroCaixa = (exibePeriodoCaixa) => {
                dadosPeriodoCaixa = exibePeriodoCaixa;
                selectArquivos.addEventListener('change', () => {
                    changeExibePeriodoCaixa();
                });
            };


            $('indicativoPeriodoApuracao').addEventListener("change", (event) => {
                $('ano').setAttribute('disabled', 'disabled');
                $('mes').setAttribute('disabled', 'disabled');
                $('ano').value = '';
                $('mes').value = '';

                if ($F('indicativoPeriodoApuracao') == '') {
                    return;
                }

                $('ano').removeAttribute('disabled');
                $('mes').removeAttribute('disabled');
            });

            const inicializarFiltroRubricas = exibeRubrica => {
                dadosRubrica = exibeRubrica;
                selectArquivos.addEventListener('change', () => {
                    changeRubrica();
                });
            };

            const inicializar = () => {
                if (integracao === ESOCIAL) {
                    text = 'eSocial';
                    spanEnvio.innerText = 'Envio';
                    notasreenvio.style.display = 'none';
                    notasfield.style.display = 'none';
                }

                if (integracao === EFD_REINF) {
                    text = 'EFD-Reinf';
                    spanEnvio.innerText = 'Envio';
                    notasreenvio.style.display = 'none';
                    notasfield.style.display = 'none';
                }

                for (const span of spanIntegracao) {
                    span.innerText = text;
                }

                if (boolReenvio) {
                    spanEnvio.innerText = 'Reenvio';
                    notasfield.style.display = 'block';
                    notasreenvio.style.display = 'block';
                }

                dbLancadorMatricula.setLabelAncora('Matrícula:');
                dbLancadorMatricula.setNomeInstancia('dbLancadorMatricula');
                dbLancadorMatricula.setTituloJanela('Pesquisa de Matrícula');
                dbLancadorMatricula.setTextoFieldset('Matrículas');
                dbLancadorMatricula.setGridHeight(150);
                dbLancadorMatricula.adicionarItensPrimeiraPosicao(true);
                dbLancadorMatricula.show(document.getElementById('td_matricula'));

                dbLancadorRubrica.setLabelAncora('Rubrica:');
                dbLancadorRubrica.setNomeInstancia('dbLancadorRubrica');
                dbLancadorRubrica.setTituloJanela('Pesquisa de Rubrica');
                dbLancadorRubrica.setTextoFieldset('Rubricas');
                dbLancadorRubrica.setGridHeight(150);
                dbLancadorRubrica.adicionarItensPrimeiraPosicao(true);
                dbLancadorRubrica.setTipoValidacao('3');
                dbLancadorRubrica.setParametro('rh27_ativo', 't');
                dbLancadorRubrica.setParametro('rh27_pd','');
                dbLancadorRubrica.setParametro('opcao','t');

                dbLancadorRubrica.show(document.getElementById('td_rubrica'));

                buttonEnviar.value = `Enviar para o ${text}`;
                if (boolReenvio) {
                    buttonProcessar.value = 'Reprocessamento de Dados';
                    buttonEnviar.value = `Reenviar para o ${text}`;
                }

                const formData = new FormData();
                formData.append('acao', 'inicializar');
                formData.append('integracao', integracao);

                if (boolReenvio) {
                    formData.append('reenvio', true)
                } else {
                    formData.append('reenvio', false)
                }

                HttpClient.post('sped02_preenchimento.RPC.php', {
                    body: formData
                }).then(response => {
                    if (response.erro) {
                        throw response.mensagem;
                    }
                    inicializarFiltroCompetencia(response.exibeCompetencia);
                    inicializarFiltroPeriodoData(response.exibePeriodoData);
                    inicializarFiltroMatriculas(response.exibeMatricula);
                    inicializarIndicativoPeriodoApuracao(response.exibeIndicativoPeriodoApuracao);
                    inicializarFiltroDataPreenchimento(response.exibeDataPreenchimento);
                    inicializarSelecao(response.exibeSelecao);
                    inicializarTipoDataPagamento(response.exibeTipoDataPagamento);
                    inicializarFiltroCaixa(response.exibeCaixa);
                    if (boolReenvio) {
                        inicializarFiltroForcarMatricula(response.exibeForcarMatricula);
                    }
                    inicializarFiltroRubricas(response.exibeRubrica);

                    // sempre o ultimo a ser chamado
                    inicializarFiltroOpcao(response.exibeFiltro);

                    if (integracao === EFD_REINF) {
                        const contribuintes = response.contribuinte;
                        contribuintes.forEach(i => {
                            let option = new Option(i.descricao, i.cgm)
                            selectContribuinte.appendChild(option);
                        })
                        trContribuinte.classList.remove('d-none');
                    }

                    if (integracao === ESOCIAL) {
                        response.empregadores.map((empregadorOption, chave) => {
                            const selecionado = chave === 0;

                            selectEmpregador.add(
                                new Option(empregadorOption.nome, empregadorOption.cgm),
                                selecionado,
                                selecionado
                            );
                        });
                        trEmpregador.classList.remove('d-none');
                    }
                }).then(buscarArquivos).then(adicionarListeners).catch(mensagem => alert(mensagem));
            };

            const inicializaFiltro = () => {
                exibeFiltros(selectFiltros.value);

                selectFiltros.addEventListener('change', () => {
                    exibeFiltros(selectFiltros.value);
                });
            }
            const inicializarSelecao = (exibeSelecao) => {
                dadosSelecao = exibeSelecao;
                selectArquivos.addEventListener('change', () => {
                    changeSelecao();
                });
            };
            const inicializarTipoDataPagamento = (exibeTipoDataPagamento) => {
                dadosTipoDataPagamento = exibeTipoDataPagamento;
                selectArquivos.addEventListener('change', () => {
                    changeTipoDataPagamento();
                });
            }
            const exibeFiltros = (opcao) => {
                //inicializando os grupos de forma forcada
                trMatricula.classList.remove('d-none');
                trMatricula.classList.add('d-none');
                matriculaTrMensagem.classList.remove('d-none');
                matriculaTrMensagem.classList.add('d-none');

                competenciaTr.classList.remove('d-none');
                competenciaTr.classList.add('d-none');
                competenciaTrMensagem.classList.remove('d-none');
                competenciaTrMensagem.classList.add('d-none');

                var forcar_competencia = false;

                if (dadosCompetencia.some(dadosCompetencia => dadosCompetencia.layout == selectArquivos.value && dadosCompetencia.forcar == true)) {
                    forcar_competencia = true;
                    competenciaTr.classList.remove('d-none');
                    competenciaTrMensagem.classList.remove('d-none');
                }

                trSelecao.classList.remove('d-none');
                trSelecao.classList.add('d-none');
                trRubrica.classList.remove('d-none');
                trRubrica.classList.add('d-none');
                resetaCampos();

                switch (opcao) {
                    case 'matricula':
                        changeMatricula();
                        break;
                    case 'competencia':
                        if (forcar_competencia == false) {
                            changeCompetencia();
                        }
                        break;
                    case 'selecao':
                        changeSelecao();
                        break;
                    case 'rubrica':
                        changeRubrica();
                        break;
                }
            }

            /**
             * Indicativo de Reabertura ou fechamento do R-4099
             */
            const inicializaindFechReab = () => {
                let indFechReabTr = document.querySelector('#indFechReabTr');
                let indFechReab = document.querySelector('#indFechReab');
                if (selectArquivos.value == 'R-4099') {
                    indFechReabTr.classList.remove('d-none');
                } else {
                    indFechReab.value = '';
                    indFechReabTr.classList.add('d-none');
                }
            }
            selectArquivos.addEventListener('change', inicializaindFechReab);

            const validaMensagemCampo = () => {
                if (mensagemValidar != "" && mensagemValidar != undefined) {
                    switch (tipoValidacaoValidar) {
                        case "competencia":
                            if (!competenciaTr.classList.contains('d-none')) {
                                if ($('ano').value == '' || $('mes').value == '') {
                                    return confirm(mensagemValidar);
                                }
                            }
                            break;
                        case "selecao":
                            if (!trSelecao.classList.contains('d-none')) {
                                if ($('r44_selec').value == '') {
                                    alert(mensagemValidar);
                                    return false;
                                }
                            }
                            break;
                        case "matricula":
                            if (!trMatricula.classList.contains('d-none')) {
                                const registros = dbLancadorMatricula.getRegistros();
                                if (registros.length == 0) {
                                    alert(mensagemValidar);
                                    return false;
                                    return confirm(mensagemValidar);
                                }
                            }
                            break;
                    case "rubrica":
                            if (!trRubrica.classList.contains('d-none')) {
                                const registrosRubrica = dbLancadorRubrica.getRegistros();
                                if (registrosRubrica.length == 0) {
                                    alert(mensagemValidar);
                                    return false;
                                }
                            }
                            break;

                    }
                }
                return true;
            }

            const resetaCampos = () => {
                // matriculas
                dbLancadorMatricula.clearAll();
                // competencia
                $('ano').value = '';
                $('mes').value = '';
                // selecao
                $('r44_selec').value = '';
                $('r44_descr').value = '';
                mensagemValidar = '';
                // rubricas
                dbLancadorRubrica.clearAll();

            }

            const changeSelecao = () => {
                trSelecao.classList.remove('d-none');
                selecaoTrMensagem.classList.remove('d-none');
                if (dadosSelecao.some(dadosSelecao => dadosSelecao.layout === parseInt(selectArquivos.value))) {
                    const elemento = dadosSelecao.find(dadosSelecao => dadosSelecao.layout === parseInt(selectArquivos.value));
                    if (elemento.mensagem != undefined) {
                        selecaoMensagemMensagem.innerText = elemento.mensagem;
                    } else {
                        selecaoMensagem.innerText = '';
                    }
                    if (elemento.validar != undefined) {
                        tipoValidacaoValidar = 'selecao';
                        mensagemValidar = elemento.validar;
                    }
                } else {
                    trSelecao.classList.add('d-none');
                    selecaoTrMensagem.classList.add('d-none');
                }
            }

            const changeTipoDataPagamento = () => {
                const selectTipoDataPag = document.getElementById("tipoDataPagamentoTr");
                selectTipoDataPag.classList.remove('d-none');
                if (dadosTipoDataPagamento.some(
                    dadosTipoDataPagamento => dadosTipoDataPagamento.layout === parseInt(selectArquivos.value))
                ) {
                } else {
                    selectTipoDataPag.classList.add('d-none');

                }
            }

            const changeCompetencia = () => {
                if (dadosCompetencia.some(dadosCompetencia => dadosCompetencia.layout == selectArquivos.value)) {
                    const elemento = dadosCompetencia.find(dadosCompetencia => dadosCompetencia.layout == selectArquivos.value);
                    if (elemento.mensagem != undefined) {
                        competenciaMensagem.innerText = elemento.mensagem;
                    } else {
                        competenciaMensagem.innerText = '';
                    }
                    if (elemento.validar != undefined) {
                        tipoValidacaoValidar = 'competencia';
                        mensagemValidar = elemento.validar;
                    }

                    competenciaTr.classList.remove('d-none');
                    competenciaTrMensagem.classList.remove('d-none');

                    $('ano').value = '';
                    $('mes').value = '';

                    $('ano').removeAttribute('disabled');
                    $('mes').removeAttribute('disabled');
                } else {
                    competenciaTr.classList.add('d-none');
                    competenciaTrMensagem.classList.add('d-none');
                }
            }
            const changePeriodoData = () => {
                if (dadosPeriodoData.some(dadosPeriodoData => dadosPeriodoData.layout === parseInt(selectArquivos.value))) {
                    const elemento = dadosPeriodoData.find(dadosPeriodoData => dadosPeriodoData.layout === parseInt(selectArquivos.value));
                    if (elemento.mensagem != undefined) {
                        competenciaMensagem.innerText = elemento.mensagem;
                    } else {
                        competenciaMensagem.innerText = '';
                    }
                    if (elemento.validar != undefined) {
                        tipoValidacaoValidar = 'competencia';
                        mensagemValidar = elemento.validar;
                    }

                    periodoDataTr.classList.remove('d-none');


                } else {
                    periodoDataTr.classList.add('d-none');
                }
            }
            const changeDataPreenchimento = () => {
                if (dadosDataPreenchimento.some(dadosDataPreenchimento => dadosDataPreenchimento.layout === parseInt(selectArquivos.value))) {
                    const elemento = dadosDataPreenchimento.find(dadosDataPreenchimento => dadosDataPreenchimento.layout === parseInt(selectArquivos.value));
                    if (elemento.mensagem != undefined) {
                        competenciaMensagem.innerText = elemento.mensagem;
                    } else {
                        competenciaMensagem.innerText = '';
                    }
                    if (elemento.validar != undefined) {
                        tipoValidacaoValidar = 'competencia';
                        mensagemValidar = elemento.validar;
                    }

                    periodoDataPreenchimentoTr.classList.remove('d-none');


                } else {
                    periodoDataPreenchimentoTr.classList.add('d-none');
                }
            }
            const changeIndicativoPeriodoApuracao = () => {
                if (dadosIndicativoPeriodoApuracao.some(dadosIndicativoPeriodoApuracao => dadosIndicativoPeriodoApuracao.layout === parseInt(selectArquivos.value))) {
                    const elemento = dadosIndicativoPeriodoApuracao.find(dadosIndicativoPeriodoApuracao => dadosIndicativoPeriodoApuracao.layout === parseInt(selectArquivos.value));

                    indicativoPeriodoApuracaoTr.classList.remove('d-none');
                    if (indicativoPeriodoApuracao.value == "") {
                        $('ano').setAttribute('disabled', 'disabled');
                        $('mes').setAttribute('disabled', 'disabled');
                    }

                } else {
                    indicativoPeriodoApuracaoTr.classList.add('d-none');
                    indicativoPeriodoApuracaoTrMensagem.classList.add('d-none');
                }
            }
            const changeExibePeriodoCaixa = () => {
                if (dadosPeriodoCaixa.some(dadosPeriodoCaixa => dadosPeriodoCaixa.layout === parseInt(selectArquivos.value))) {
                    const elemento = dadosPeriodoCaixa.find(dadosPeriodoCaixa => dadosIndicativoPeriodoApuracao.layout === parseInt(selectArquivos.value));
                    caixaTr.classList.remove('d-none');
                } else {
                    caixaTr.classList.add('d-none');
                    caixaTrMensagem.classList.add('d-none');
                }
            }
            const changeMatricula = () => {
                trMatricula.classList.remove('d-none');
                matriculaTrMensagem.classList.remove('d-none');
                dbLancadorMatricula.clearAll();

                const arquivo = parseInt(selectArquivos.value);

                if (dadosMatricula.some(dadosMatricula => dadosMatricula.layout === parseInt(arquivo))) {
                    const urlSearchParams = new URLSearchParams();
                    const searchParams = dadosMatricula.find(dadosMatricula => dadosMatricula.layout === parseInt(arquivo));;
                    if (searchParams.mensagem != undefined) {
                        matriculaMensagem.innerText = searchParams.mensagem;
                    } else {
                        matriculaMensagem.innerText = '';
                    }
                    if (searchParams.validar != undefined) {
                        tipoValidacaoValidar = 'matricula';
                        mensagemValidar = searchParams.validar;
                    }

                    for (var key in searchParams.parametros) {
                        if (searchParams.parametros.hasOwnProperty(key)) {
                            urlSearchParams.set(key, searchParams.parametros[key]);
                        }
                    }

                    dbLancadorMatricula.setParametrosPesquisa(searchParams.funcao, [
                        'rh01_regist',
                        'z01_nome'
                    ], urlSearchParams.toString());
                } else {
                    trMatricula.classList.add('d-none');
                    matriculaTrMensagem.classList.add('d-none');
                }
            }
            const inicializarFiltroForcarMatricula = exibeForcarMatricula => {
                selectArquivos.addEventListener('change', () => {
                    inputForcarMatricula.checked = false;
                    trForcarMatricula.classList.remove('d-none');
                    trForcarMatriculaTexto.classList.remove('d-none');

                    if (exibeForcarMatricula.includes(parseInt(selectArquivos.value))) {

                    } else {
                        trForcarMatricula.classList.add('d-none');
                        trForcarMatriculaTexto.classList.add('d-none');
                    }
                });
            }
            const changeRubrica = () => {
                trRubrica.classList.remove('d-none');
                dbLancadorRubrica.clearAll();

                const arquivo = parseInt(selectArquivos.value);

                if (dadosRubrica.some(dadosRubrica => dadosRubrica.layout === parseInt(arquivo))) {
                    const urlSearchParams = new URLSearchParams();
                    const searchParams = dadosRubrica.find(dadosRubrica => dadosRubrica.layout === parseInt(arquivo));;

                    if (searchParams.validar != undefined) {
                        tipoValidacaoValidar = 'rubrica';
                        mensagemValidar = searchParams.validar;
                    }

                    for (var key in searchParams.parametros) {
                        if (searchParams.parametros.hasOwnProperty(key)) {
                            urlSearchParams.set(key, searchParams.parametros[key]);
                        }
                    }

                    dbLancadorRubrica.setParametrosPesquisa(searchParams.funcao, [
                        'rh27_rubric',
                        'rh27_descr'
                    ], urlSearchParams.toString());
                } else {
                    trRubrica.classList.add('d-none');
                }
            };
            inicializar();
        })();
        //PLUGIN ENVIOESOCIAL - Adiciona botoes de envio
        new DBLookUp($('lblSelecao'), $('r44_selec'), $('r44_descr'), {
            sArquivo: 'func_selecao.php',
            sObjetoLookUp: 'func_selecao',
            sLabel: 'Seleção',
        });
    </script>
</body>
</html>
