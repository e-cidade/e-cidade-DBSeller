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
        <style>
            #competenciaTr input[type="text"] {
                width: inherit;
            }
        </style>
    </head>
    <body>
        <form class="container">
            <fieldset>
                <legend>Reenvio de Eventos para o <span id="span_integracao"></span></legend>
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
                            <input type="text" id="descricao" name="descricao" class="readonly field-size-max" disabled>
                            <input type="hidden" id="contribuinte" name="contribuinte" class="readonly">
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
                    <tr id="competenciaTr" class="d-none">
                        <td id="labelCompetencia"></td>
                        <td id="formularioCompetencia"></td>
                    </tr>
                    <tr id="competenciaTrMensagem"  class="d-none">
                        <td id="competenciaMensagem" colspan="2">
                        </td>
                    </tr>
                    <tr id="selecaoTr" class="d-none">
                        <td>
                            <a href="#"><label for="r44_selec" id="lblSelecao">Sele&ccedil;&aacute;o:</label></a>
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
                    <tr id="tr_matricula" class="d-none">
                        <td colspan="2" id="td_matricula" style="padding-top: 15px"></td>
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
            <input type="button" id="processarDadosForcados" value="Reprocessamento de Dados">
            <input type="button" id="enviarApi" value="Enviar">
            <div>
                <br><br>
                <p style="text-align: left"><strong>Notas:</strong></p>
                <p style="text-align: left">Esta rotina é destinada a reenviar <strong>eventos</strong> que não foram diretamente alterados, mas devido alteração de outros eventos, </p>
                <p style="text-align: left">o mesmo precisa ser reenviado para atualização no portal do eSocial.</p>
            </div>

            <script rel="script" type="text/javascript">
                var dbLancadorMatricula = new DBLancador('dbLancadorMatricula');
                var text = '';

                (() => {
                    const EFD_REINF = '1';
                    const ESOCIAL = '2';
                    const urlParams = new URLSearchParams(window.location.search);
                    const integracao = urlParams.has('integracao') ? urlParams.get('integracao') : '2';
                    const trEmpregador = document.getElementById('tr_empregador');
                    const trContribuinte = document.getElementById('tr_contribuinte');
                    const trMatricula = document.getElementById('tr_matricula');
                    const trFiltros = document.getElementById('tr_filtros');
                    const selectEmpregador = document.getElementById('empregador');
                    const selectArquivos = document.getElementById('arquivos');
                    const inputDescricao = document.getElementById('descricao');
                    const inputContribuinte = document.getElementById('contribuinte');
                    const buttonProcessar = document.getElementById('processarDadosForcados');
                    const buttonEnviar = document.getElementById('enviarApi');
                    const spanIntegracao = document.getElementById('span_integracao');
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
                    var mensagemValidar = '';
                    var tipoValidacaoValidar = '';
                    var filtros;
                    var dadosSelecao;
                    var dadosMatricula;
                    var dadosCompetencia;
                    var dadosTipoDataPagamento;

                    const validar = () => {
                        return new Promise((resolve, reject) => {
                            if (integracao === EFD_REINF && inputContribuinte.value === '') {
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
                                forcado: true,
                                ano: null,
                                mes: null
                            };

                            parametros.indicativoPeriodoApuracao = $F('indicativoPeriodoApuracao');
                            parametros.ano = formularioCompetencia.querySelector("#ano").value;
                            parametros.mes = formularioCompetencia.querySelector("#mes").value;
                            parametros.tipoDataPagamento = $F("tipoDataPagamento");
                            //Adicionado forcar processamento de Matrículas
                            parametros.forcarMatricula = inputForcarMatricula.checked;
                            //Fim forcar processamento de Matriculas
                            parametros.selecao = $F("r44_selec");


                            if (integracao === EFD_REINF) {
                                parametros.cgm = inputContribuinte.value;
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
                                    parametros.cgm = inputContribuinte.value;
                                }

                                if (integracao === ESOCIAL) {
                                    parametros.cgm = selectEmpregador.value;
                                    parametros.ano = formularioCompetencia.querySelector("#ano").value;
                                    parametros.mes = formularioCompetencia.querySelector("#mes").value;

                                    const registros = dbLancadorMatricula.getRegistros();

                                    if (registros.length > 0) {
                                        parametros.matriculas = [];

                                        registros.each(registro => {
                                            parametros.matriculas.push(registro.sCodigo);
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
                            'integracao': integracao,
                            'forcado': true
                        };

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

                    const inicializarFiltroCompetencia = exibeCompetencia => {
                        dadosCompetencia = exibeCompetencia;
                        selectArquivos.addEventListener('change', () => {
                            changeCompetencia();
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
                        selectArquivos.addEventListener('change', () => {
                            filtrosPeriodoApuracao.classList.remove('d-none');

                            if (exibeIndicativoPeriodoApuracao.hasOwnProperty(selectArquivos.value)) {
                                $('ano').setAttribute('disabled', 'disabled');
                                $('mes').setAttribute('disabled', 'disabled');
                            } else {
                                filtrosPeriodoApuracao.classList.add('d-none');
                            }
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

                    const inicializar = () => {
                        if (integracao === ESOCIAL) {
                            text = 'eSocial';
                            spanIntegracao.innerText = text;
                        }

                        if (integracao === EFD_REINF) {
                            text = 'EFD-Reinf';
                            spanIntegracao.innerText = text;
                        }

                        dbLancadorMatricula.setLabelAncora('Matrícula:');
                        dbLancadorMatricula.setNomeInstancia('dbLancadorMatricula');
                        dbLancadorMatricula.setTituloJanela('Pesquisa de Matrícula');
                        dbLancadorMatricula.setTextoFieldset('Matrículas');
                        dbLancadorMatricula.setGridHeight(150);
                        dbLancadorMatricula.adicionarItensPrimeiraPosicao(true);
                        dbLancadorMatricula.show(document.getElementById('td_matricula'));

                        buttonEnviar.value = `Enviar para o ${text}`;

                        const formData = new FormData();
                        formData.append('acao', 'inicializar');
                        formData.append('integracao', integracao);
                        formData.append('reenvio', true);

                        HttpClient.post('sped02_preenchimento.RPC.php', {
                            body: formData
                        }).then(response => {
                            if (response.erro) {
                                throw response.mensagem;
                            }
                            inicializarFiltroCompetencia(response.exibeCompetencia);
                            inicializarFiltroMatriculas(response.exibeMatricula);
                            inicializarIndicativoPeriodoApuracao(response.exibeIndicativoPeriodoApuracao);
                            inicializarSelecao(response.exibeSelecao);
                            inicializarTipoDataPagamento(response.exibeTipoDataPagamento);
                            //Adicionado Filtro para forcar a matricula
                            inicializarFiltroForcarMatricula(response.exibeForcarMatricula);
                            //Fim Filtro para forcar a matricula

                            // sempre o ultimo a ser chamado
                            inicializarFiltroOpcao(response.exibeFiltro);

                            if (integracao === EFD_REINF) {
                                inputDescricao.defaultValue = response.contribuinte.descricao;
                                inputContribuinte.defaultValue = response.contribuinte.cgm.codigo;
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
                        competenciaTr.classList.remove('d-none');
                        competenciaTr.classList.add('d-none');
                        competenciaTrMensagem.classList.remove('d-none');
                        competenciaTrMensagem.classList.add('d-none');
                        trSelecao.classList.remove('d-none');
                        trSelecao.classList.add('d-none');
                        resetaCampos();

                        switch (opcao) {
                            case 'matricula':
                                changeMatricula();
                                break;
                            case 'competencia':
                                changeCompetencia();
                                break;
                            case 'selecao':
                                changeSelecao();
                                break;
                        }
                    }

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
                                        console.log('aqui');
                                        const registros = dbLancadorMatricula.getRegistros();
                                        if (registros.length == 0) {
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
                        if (dadosCompetencia.some(dadosCompetencia => dadosCompetencia.layout === parseInt(selectArquivos.value))) {
                            const elemento = dadosCompetencia.find(dadosCompetencia => dadosCompetencia.layout === parseInt(selectArquivos.value));
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

                    const changeMatricula = () => {
                        trMatricula.classList.remove('d-none');
                        dbLancadorMatricula.clearAll();

                        const arquivo = parseInt(selectArquivos.value);

                        if (dadosMatricula.some(dadosMatricula => dadosMatricula.layout === parseInt(arquivo))) {
                            const urlSearchParams = new URLSearchParams();
                            const searchParams = dadosMatricula.find(dadosMatricula => dadosMatricula.layout === parseInt(arquivo));;

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
                    };
                    inicializar();
                })();
                new DBLookUp($('lblSelecao'), $('r44_selec'), $('r44_descr'), {
                    sArquivo: 'func_selecao.php',
                    sObjetoLookUp: 'func_selecao',
                    sLabel: 'Seleção',
                });
            </script>
    </body>
</html>
