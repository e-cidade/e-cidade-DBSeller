<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <meta name="ECIDADE_REQUEST_PATH" content="<?= ECIDADE_REQUEST_PATH ?>">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.css">
    <script src="public/js/app.js"></script>

    <style>
        .columns {
            width: 75%;
        }
        .columns .btn {
            margin-right: 5px;
        }
        .signer-status {
            text-align: right;
            color: #999;
        }
        .conected {
            color: green !important;
        }

        #progressbar_documentos {
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            position: absolute;
            top: 0;
            left: 0;
            z-index: 999999;
        }
        #container_modal {
            background-color: #fff;
            width: 400px;
            margin: 0 auto;
            border-radius: 7px;
            transform: translateY(calc(50% + 100px));
            padding: 64px 16px;
        }
        #progressbar {
            width: 100%;
            height: 24px;
            border: 1px solid #2c5676;
            background-color: #edf5ff;
        }
        #progressbar_count {
            float: right;
        }
        #progressbar #progressbar_status {
            box-sizing: border-box;
            height: 23px;
            background-color: #2c5676;
            text-align: center;
            color: #fff;
            transition: width 1s;
        }
    </style>
</head>
<body class="body-default">
<div id="progressbar_documentos" hidden>
    <div id="container_modal">
        <div id="header_modal">
            <span id="title_progressbar"></span>
            <span id="progressbar_count"></span>
        </div>
        <div id="progressbar">
            <div id="progressbar_status" style="width: 0%">0%</div>
        </div>
    </div>
</div>

<div class="container">
    <div class="text-right" style="margin-bottom: 8px">
        <button type="button" id="baixarPfx">
            <i class="fa fa-key"></i>
            Download Certificado Digital
        </button>
    </div>
    <div class="alert alert-primary text-left" role="alert">
        Verifique seus documentos pendentes de atividade
    </div>
    <fieldset id="filtro" style="display:none">
        <legend>Filtro <span id="filtro_legenda"></span>
        </legend>
        <div id="filtroForm"></div>
    </fieldset>
    <fieldset>
        <legend>Lista de Documentos</legend>
        <div style="width: 1000px">
            <div id="toolbar">
                <b>Tipo de documento</b>
                 <select id="tipo_documento">
                       <option value="">--TODOS--</option>
                       <option value="6">EMPENHO</option>
                       <option value="7">PORTARIAS</option>
                 </select>
                <b>Data</b>
                <input type="date"
                       id="data_inicio"
                       placeholder="Data Inicio"
                >
                <b>à</b>
                <input type="date"
                       id="data_fim"
                       placeholder="Data Fim"
                >
                <button id="button"
                        class="btn btn-secondary"
                        onclick="buscarDocumentosUsuario()"
                        style="float:right;margin-right: 5px"
                >
                    Pesquisar
                </button>

            </div>
            <table id="data-table"
                   class="table table-sm"
                   data-toolbar="#toolbar"
                   data-height="500"
                   data-virtual-scroll="true"
                   style="width: 100%;">
            </table>
        </div>
    </fieldset>
</div>
<div id="modalCertificado" class="container">
    <div class="signer-status" id="status">
        <i class="fa fa-key" title="Assinador não conectado"></i>
    </div>
    <div id="certificatesDIV">
        <fieldset>
            <legend>Certificados</legend>
            <div class="mb-3">
                <select class="form-select" id="certificateSelect" aria-label="Default select example">
                    <option value="0">Selecione um certificado</option>
                </select>
            </div>
        </fieldset>
    </div>
    <div id="detalhes_documento"></div>
    <button class="btn btn-light" id="btnAssinar" style="margin-top: 10px">
        <i class="fas fa-file-signature" aria-hidden="true"></i>
        Assinar
    </button>
</div>
<div id="modalDevolver" class="container">
    <fieldset>
        <legend>Atividades anteriores: </legend>
        <input type="hidden" id="codigo_atividade_devolver" value="">
        <div class="mb-3">
            <select class="form-select" id="atividadeDevolver">
                <option value="">Selecione a atividade</option>
            </select>
        </div>
    </fieldset>
    <button class="btn btn-light" id="btnDevolver" style="margin-top: 10px">
        <i class="fas fa-undo" aria-hidden="true"></i>
        Devolver
    </button>
</div>
<input type="hidden" id="qrcode_link" value="<?=env('URL_AUTENTICIDADE_DOCUMENTO')?>" />
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/socket.io.js"></script>
<script type="text/javascript">

    var urlApi;
    var urlApiProtocolo;
    var signerConfig;
    var timeoutHandle;
    const inputDataInicio = document.getElementById("data_inicio");
    const inputDataFim = document.getElementById("data_fim");
    const selectOrgao = document.getElementById("orgao");
    const selectTipoDocumento = document.getElementById("tipo_documento");
    const filtroLegenda = document.getElementById("filtro_legenda");
    const filtro = document.getElementById("filtro");
    const filtroDocumento = document.getElementById("tipo_documento");
    const filtroForm = document.getElementById("filtroForm");
    selectTipoDocumento.addEventListener("change",function(e){
        filtro.style.display = 'none';
        filtroForm.innerHTML = '';
        switch(e.target.value){
             case '6':
                 filtroEmpenho();
                 break;
         }
    });

    const filtroEmpenho = () =>{
        filtro.style.display = 'block';
        filtroLegenda.innerText = 'Empenho';
        filtroForm.innerHTML = `
            <form id="form_filtro">
                <b>Orgão:</b>
                <select id="orgao" name="orgao">
                </select>
            <form>
        `;
        buscarOrgao();
    }

    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
            urlApiProtocolo = `${urlApi}/patrimonial/protocolo`;
            buscarDocumentosUsuario({limit:100});
            buscarConfiguracaoAssinador();
        });
    });

    const buscarOrgao  = async () =>{
        js_divCarregando('Carregando orgãos', 'orgao_loading');
        try {
            const selectOrgao = document.getElementById("orgao");
            const resp  = await window.axios.get(`${window.urlApi}/patrimonial/protocolo/documentos/usuario/orgaos`);
            const data  = await resp.data;
            let optionsSelect = [];
            optionsSelect.push(`<option value="null">--TODOS--</option>`);
            if(data.error === false){
                data.data.forEach(el => {
                    optionsSelect.push(
                        `<option value="${el.o40_orgao}">${el.o40_orgao} - ${el.o40_descr}</option>`
                    );
                });
            }
            selectOrgao.innerHTML = optionsSelect.join("");
            js_removeObj("orgao_loading");
        }catch (e) {
            js_removeObj("orgao_loading");
        }
    }

    const buscarDocumentosUsuario = async (options = {limit:null}) => {

        const form = {};
        form.data_inicio = inputDataInicio.value;
        form.data_fim = inputDataFim.value;
        form.filtro = {};
        form.filtro.tipo = filtroDocumento.value;
        form.limit = options.limit;
        const formFiltro =  document.getElementById("form_filtro");
        if(formFiltro){
            formFiltro.forEach(el =>{
                console.log(el.name,el.value);
                 form.filtro[el.name] = el.value;
            });
        }


        table.bootstrapTable('showLoading');

        const resp = await window.axios.post(
            `${window.urlApi}/patrimonial/protocolo/documentos/usuario`,
            form
        );

        table.bootstrapTable('hideLoading');

       const data = await resp.data;

       if(data.error){
           table.bootstrapTable('load', []);
           alert(response.message);
           return;
       }
        const documentos = data.data;
        table.bootstrapTable('load', documentos);
    }

    const buscarConfiguracaoAssinador = () => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/assinador/obter-configuracao`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                signerConfig = response.data;
            })

    }

    const containerDetalhes = document.getElementById('detalhes_documento');
    const btnAssinar = document.getElementById('btnAssinar');
    const btnDevolver = document.getElementById('btnDevolver');
    const inputFileId = document.getElementById('file_id');
    const inputDocumentoAndamento = document.getElementById('documento_andamento_id');
    const cboCodigoTipoProcesso = document.getElementById('p51_codigo');
    const inputCodigoAtividadeDevolver = document.getElementById('codigo_atividade_devolver');
    const inputQrcodeLink = document.getElementById('qrcode_link');
    const certificateSelect = document.getElementById('certificateSelect');
    const cboAtividadeDevolver = document.getElementById('atividadeDevolver');
    const btnBaixarPfx = document.getElementById('baixarPfx');

    var table = jQuery('#data-table');

    const callbackAcoes = (value, row) => {
        let botoes = '';
        if (row.atividade_atual.p118_ordem > 1) {
            botoes += `<a href="#" class="devolverAtividade" alt="Devolver Atividade"><i class="fas fa-arrow-circle-left"></i> Devolver </a>`;
        }
        botoes += `<a href="#" class="executarAtividade"><i class="fas fa-arrow-circle-right"></i> ${row.proxima_atividade.atividade.p114_atividade} </a>`;
        return botoes;
    }
    const callbackPdf = (value, row) => {
        return `<a href="#" class="abrirPdf"><i class="fas fa-file-archive"></i> Ver PDF </a>`;
    }

    const callbackConsulta = (value, row) => {
        return `<a href="#" class="abrirConsulta"><i class="fas fa-file-archive"></i> Ver PDF </a>`;
    }

    const dataFormatter = (value, row) => {
        let data = new Date(value);
        let intl = new Intl.DateTimeFormat('pt-BR', {timeZone: 'UTC'});
        return intl.format(data);
    }

    const descricaoFormatter = (value, row) => {
        let descricao_extra = '';
        if (row.hasOwnProperty('descricao_extra')) {
            descricao_extra = row.descricao_extra;
        }
        return `<a href="#" class="abrirConsulta">${value}</a> - ${descricao_extra}`;
    }

    const statusFormatter = (value, row) => {
        let status = row.atividade_atual.atividade.p114_status;
        if (row.isDevolvido) {
            status += " (Devolvido)";
        }
        return status;
    }

    const detailFormatter = (index, row) => {
        return detailFormaterTable.createDetail(row.detalhes, 'Detalhes: ');
    }
    const metadadosFormatter = (index, row) => {
        return JSON.stringify(row.detalhes);
    }

    window.operateEvents = {
        'click .abrirPdf': function (e, value, row, index) {
            window.open(`db_visualizar_estorage.php?id=${row.documento_estorage}`);
            // js_OpenJanelaIframe(
            //     'CurrentWindow.corpo',
            //     'db_visualizador_imagens',
            //     `db_visualizador_documentos.php?ids=${row.documento_estorage}`,
            //     'Visualizador de documentos',
            //     true
            // );
        },
        'click .abrirConsulta': function (e, value, row, index) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                row.consulta.name,
                row.consulta.funcao,
                row.consulta.label,
                true
            );
        },
        'click .devolverAtividade': function (e, value, row, index) {
            inputCodigoAtividadeDevolver.value = row.p116_codigo;
            windowDevolucao.show(0, 0, true);
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            formData.append('codigo_documento', row.p116_codigo);
            HttpClient.post(`${urlApiProtocolo}/documentos/atividades-executadas`, {body: formData})
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    cboAtividadeDevolver.length = 1;
                    const atividadesExecutadas = response.data;
                    atividadesExecutadas.map((atividadeExecutada) => {
                        let descricao = atividadeExecutada.p118_ordem+' - '+atividadeExecutada.atividade.p114_status;
                        cboAtividadeDevolver.add(new Option(descricao, atividadeExecutada.p118_codigo));
                    })
                });
        },
        'click .executarAtividade': function (e, value, row, index) {
            let atividadeExecutar = row.proxima_atividade.atividade;
            if (atividadeExecutar.p114_codigo === 2) {
                let selecionados = table.bootstrapTable('getSelections').map((documento) => {
                    if (documento.proxima_atividade.p118_atividadesexecucao != 2) {
                        return null;
                    }
                    return documento.p116_codigo;
                });
                selecionados = selecionados.filter(item => item != null);

                const elementoClicado = selecionados.filter(item => item === row.p116_codigo);
                if (elementoClicado.length === 0) {
                    selecionados.push(row.p116_codigo);
                }
                const formData = new FormData();
                PHPSession.appendFormData(formData);
                formData.append('documentos', selecionados.join(','));
                HttpClient.post(`${urlApiProtocolo}/documentos/conferir-em-lote`, {body: formData})
                    .then((response) => {
                        alert(response.message);
                        if (response.error) {
                            return;
                        }
                        buscarDocumentosUsuario();
                    });
                return;
            }

            if (atividadeExecutar.p114_codigo === 3) {
                let selecionados = table.bootstrapTable('getSelections').map((documento) => {
                    if (documento.proxima_atividade.p118_atividadesexecucao != 3) {
                        return null;
                    }
                    return {
                        documento_estorage: documento.documento_estorage,
                        documento_andamento: documento.p116_codigo,
                        qrcode_hash: documento.p116_qrcode
                    };
                });

                selecionados = selecionados.filter(item => item != null);

                const elementoClicado = selecionados.filter(item => item.documento_andamento === row.p116_codigo);
                if (elementoClicado.length === 0) {
                    selecionados.push({
                        documento_estorage: row.documento_estorage,
                        documento_andamento: row.p116_codigo,
                        qrcode_hash: row.p116_qrcode
                    });
                }
                arquivosAssinar = selecionados;
                windowAssinatura.show(0, 0, true);
                return;
            }

            if (atividadeExecutar.p114_codigo === 4) {
                const formData = new FormData();
                PHPSession.appendFormData(formData);
                formData.append('codigo_documento', row.p116_codigo);
                HttpClient.post(`${urlApiProtocolo}/documentos/arquivar`, {body: formData})
                    .then((response) => {
                        alert(response.message);
                        if (response.error) {
                            return;
                        }
                        buscarDocumentosUsuario();
                    });
                return;
            }

            alert(`Funcionalidade não implementada! \n${atividadeExecutar.p114_codigo} - ${atividadeExecutar.p114_atividade}`);
        }
    }

    const buttons = () => {
        return {
            btnConferir: {
                text: 'Selecionar conferir',
                event: () => {
                    table.bootstrapTable('uncheckAll');
                    table.bootstrapTable('getData').map((documento) => {
                        if (documento.proxima_atividade.p118_atividadesexecucao == 2) {
                            table.bootstrapTable('checkBy', {field: 'p116_codigo', values: [documento.p116_codigo]});
                        }
                    });
                },
                attributes: {
                    title: 'Clique para selecionar todos os documentos para conferir'
                }
            },
            btnAssinar: {
                text: 'Selecionar assinar',
                event: () => {
                    table.bootstrapTable('uncheckAll');
                    table.bootstrapTable('getData').map((documento) => {
                        if (documento.proxima_atividade.p118_atividadesexecucao == 3) {
                            table.bootstrapTable('checkBy', {field: 'p116_codigo', values: [documento.p116_codigo]});
                        }
                    });
                },
                attributes: {
                    title: 'Clique para selecionar todos os documentos para assinar'
                }
            },
            btnDesmarcar: {
                text: 'Desmarcar todos',
                event: () => {
                    table.bootstrapTable('uncheckAll');
                },
                attributes: {
                    title: 'Clique para desmarcar todos os documentos'
                }
            }
        }
    }

    const callbackCheck = (row, element) => {
        table.bootstrapTable('refreshOptions', {});
        if (element === undefined) {
            return;
        }
        const selecionados = table.bootstrapTable('getSelections');
        let atividadeCheckboxSelecionado = row.proxima_atividade.p118_atividadesexecucao;
        let isDiferente = false;
        selecionados.map(selecao => {
            let atividadeSelecao = selecao.proxima_atividade.p118_atividadesexecucao;
            if (atividadeCheckboxSelecionado != atividadeSelecao) {
                isDiferente = true;
            }
        });

        if (isDiferente) {
            table.bootstrapTable('uncheck', element[0].getAttribute('data-index'));
            alert("Você não pode selecionar documentos com Atividades diferentes.");
        }
    }

    const refreshOptions = () => {
        table.bootstrapTable('refreshOptions', {});
    }
    table.bootstrapTable({
        data: [],
        uniqueId :"p116_codigo",
        locale: 'pt-BR',
        showButtonText: true,
        buttonsAlign: 'left',
        buttons: buttons,
        class: "table table-sm",
        search: false,
        detailView: true,
        detailFormatter: detailFormatter,
        checkbox: true,
        checkboxHeader: false,
        onCheck: callbackCheck,
        onUncheck: refreshOptions,
        onUncheckAll: refreshOptions,
        showFooter: true,
        columns: [
            {
                checkbox: 'checkbox',
                field: 'checkbox',
            }, {
                title: 'Emissão',
                field: 'p116_data_criacao',
                align: 'left',
                formatter: dataFormatter
            }, {
                title: 'Descrição',
                field: 'p116_descricao',
                align: 'left',
                width: '500',
                events: window.operateEvents,
                formatter: descricaoFormatter
            }, {
                field: 'pdf',
                title: 'Documento',
                align: 'center',
                events: window.operateEvents,
                formatter: callbackPdf
            }, {
                title: 'Status',
                field: 'p116_atividade_atual',
                align: 'left',
                formatter: statusFormatter
            }, {
                field: 'acoes',
                title: 'Açoes',
                align: 'center',
                falign: 'left',
                events: window.operateEvents,
                formatter: callbackAcoes,
                footerFormatter: (data) => {
                    return 'Total: ' + data.length + ' Selecionados: ' + table.bootstrapTable('getSelections').length;
                }
            }, {
                field: 'metadados',
                title: '',
                visible: false,
                formatter: metadadosFormatter
            }
        ]
    });

    // Modal Seleção de Atividade para qual deve ser Devolvida
    const modalDevolucao = document.getElementById('modalDevolver');
    const hideWindowDevolucao = () => {
        if (!!windowDevolucao.oDBMask) {
            windowDevolucao.oDBMask.destroy();
        }
        cboAtividadeDevolver.length = 1;
        inputCodigoAtividadeDevolver.value = '';
        windowDevolucao.hide();
    }
    var windowDevolucao = new windowAux('windowDevolucao', 'Selecionar atividade para devolução', 600, 300);
    windowDevolucao.setContent(modalDevolucao);
    windowDevolucao.allowCloseWithEsc(true);
    windowDevolucao.setShutDownFunction(function () {
        hideWindowDevolucao();
    });

    // Modal Seleção de Certificado para Assinatura
    const modalCertificado = document.getElementById('modalCertificado');
    const hideWindowAssinatura = () => {
        if (!!windowAssinatura.oDBMask) {
            windowAssinatura.oDBMask.destroy();
        }
        windowAssinatura.hide();
    }
    var windowAssinatura = new windowAux('windowAssinatura', 'Selecionar Certificado para Assinatura', 600, 300);
    windowAssinatura.setContent(modalCertificado);
    windowAssinatura.allowCloseWithEsc(true);
    windowAssinatura.setShutDownFunction(function () {
        hideWindowAssinatura();
    });

    let arquivosAssinar = [];
    let arquivosAssinando = [];
    let arquivosAssinados = [];

    //Instanciar socket.io
    var socket = io.connect('http://localhost:9000');

    socket.on('connect', () => {
        document.getElementById('status').classList.add('conected');
        document.querySelector('.signer-status > i').setAttribute('title', 'Conectado ao Assinador A3');
        socket.emit('getCertificates', {});
    });

    socket.on('disconnect', () => {
        document.getElementById('status').classList.remove('conected');
        document.querySelector('.signer-status > i').setAttribute('title', 'Assinador não Conectado');
    });

    const limparTelaAssinador = (lTimeout = false) => {
        if (lTimeout) {
            alert("Erro ao assinar documentos, tempo de execução excedido.");
        }
        btnAssinar.disabled = false;
        arquivosAssinar = [];
        arquivosAssinando = [];
        arquivosAssinados = [];
        fecharBarraProgresso();
        hideWindowAssinatura();
        buscarDocumentosUsuario();
    }

    socket.on('error', (event) => {
        window.clearTimeout(timeoutHandle);
        alert(event.urlDecode());
        limparTelaAssinador(false);
    });

    //Recebe resposta dos certificados e propaga o select
    socket.on('certificates', (event) => {
        let certificates = event.certList;
        let selectBox = certificateSelect;

        selectBox.options.length = 0;

        selectBox.add(new Option('Selecione um certificado', 0));

        certificates.forEach((value, index, arr) => {
            let newOption = new Option(value, value);
            selectBox.add(newOption);
        });

        js_removeObj('loading_message_a3');

    });

    socket.on('signed', async (event) => {
        btnAssinar.disabled = false;
        let fileB64 = event.fileB64;
        let arquivoAssinado = arquivosAssinando.filter(file => {
            return event.fileID == file.fileID
        }).shift();
        console.log(`Recebido arquivo '${arquivoAssinado.fileID}' assinado`)
        arquivosAssinando = arquivosAssinando.filter(file => {
            return event.fileID != file.fileID
        });
        arquivoAssinado.base64 = fileB64;
        arquivosAssinados.push(arquivoAssinado);
        atualizarBarraProgresso(arquivosAssinados.length);
        await assinarProximoDocumento();
    });

    const salvarArquivosAssinados = async () => {
        atualizarBarraProgresso(arquivosAssinados.length, 'Salvando documentos assinados...');
        let response = null;
        for (let arquivo of arquivosAssinados) {
            console.log(`Salvando... ${arquivo.fileID}`);
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            formData.append('base64', arquivo.base64);
            formData.append('codigoDocumentoAndamento', arquivo.codigoDocumentoAndamento);
            formData.append('codigoEstorage', arquivo.fileID);

            response = await HttpClient.post(
                `${urlApiProtocolo}/documentos/salvar-documento-assinado`,
                {body: formData}
            );
        }
        arquivosAssinar = [];
        arquivosAssinando = [];
        arquivosAssinados = [];
        fecharBarraProgresso();
        alert(response.message);
        if (response.error) {
            return;
        }
        hideWindowAssinatura();
        buscarDocumentosUsuario();
    }

    const assinarProximoDocumento = async () => {
        if (arquivosAssinar.length === 0) {
            await salvarArquivosAssinados();
            return;
        }
        const arquivoAssinar = arquivosAssinar.shift();

        const codigoEstorage = arquivoAssinar.documento_estorage;
        const codigoDocumentoAndamento = arquivoAssinar.documento_andamento;

        const parametrosAssinatura = {...signerConfig};
        parametrosAssinatura.fileID = codigoEstorage;
        parametrosAssinatura.fileCertificate = certificateSelect.value;
        parametrosAssinatura.codigoDocumentoAndamento = codigoDocumentoAndamento;
        parametrosAssinatura.qrcode_link = inputQrcodeLink.value;
        parametrosAssinatura.qrcode_hash = arquivoAssinar.qrcode_hash;

        const response = await HttpClient.get(
            `${PHPSession.requestApi}/assinador/obter-arquivo-base64/${codigoEstorage}`,
            { reportProgress: false }
        );

        let data = response.data;
        parametrosAssinatura.fileB64 = data.content_estorage;
        arquivosAssinando.push(parametrosAssinatura);
        socket.emit('sign', parametrosAssinatura);
        btnAssinar.disabled = true;
        console.log(`Enviando arquivo '${parametrosAssinatura.fileID}' para o assinador`);
    }

    btnAssinar.addEventListener('click', () => {
        if (empty(certificateSelect.value)) {
            alert("Selecione o certificado");
            return;
        }
        hideWindowAssinatura();
        mostrarBarraProgresso(arquivosAssinar.length, 'Assinando documentos, aguarde...');
        assinarProximoDocumento();
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(() => {
            limparTelaAssinador(true);
        }, 60*3*1000);
    });

    btnDevolver.addEventListener('click', (event) => {
        let atividadeDevolver = cboAtividadeDevolver.value;
        if (empty(atividadeDevolver)) {
            alert("Seleciona para qual atividade deve retornar o documento.");
            return;
        }

        const formData = new FormData();
        PHPSession.appendFormData(formData);
        formData.append('codigo_documento', inputCodigoAtividadeDevolver.value);
        formData.append('atividade_destino', atividadeDevolver);
        HttpClient.post(`${urlApiProtocolo}/documentos/devolver`, {body: formData})
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                hideWindowDevolucao();
                buscarDocumentosUsuario();
            });
    })

    const divProgressbarDocumentos = document.getElementById('progressbar_documentos');
    const divTitleProgressbar = document.getElementById('title_progressbar');
    const progressbar = document.getElementById('progressbar');
    const divStatus = document.getElementById('progressbar_status');
    const divProgressbarCount = document.getElementById('progressbar_count');

    let totalProgresso = 0;
    const mostrarBarraProgresso = (total, texto) => {
        divTitleProgressbar.innerText = texto;
        divStatus.style.width = '0%';
        divStatus.innerText = '0%';
        totalProgresso = total;
        divProgressbarCount.innerText = `0 de ${totalProgresso}`;
        divProgressbarDocumentos.removeAttribute('hidden');
    }
    const atualizarBarraProgresso = (progresso, texto = '') => {
        if (texto != '') {
            divTitleProgressbar.innerText = texto;
        }
        let porcentagem = Math.ceil((progresso*100)/totalProgresso);
        divProgressbarCount.innerText = `${progresso} de ${totalProgresso}`;
        divStatus.style.width = `${porcentagem}%`;
        divStatus.innerText = `${porcentagem}%`;
    }
    const fecharBarraProgresso = () => {
        totalProgresso = 0;
        divProgressbarDocumentos.setAttribute('hidden', 'hidden');
    }

    btnBaixarPfx.addEventListener('click', () => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/assinador/recuperar-pfx`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                window.open("db_download.php?arquivo="+response.data.path);
            });
    });
</script>
</body>
</html>
