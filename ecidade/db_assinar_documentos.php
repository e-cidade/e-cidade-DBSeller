<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.css">
    <style>
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
<body>

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
    <fieldset>
        <legend>Assinar Documentos:</legend>
        <fieldset>
            <legend>Certificados</legend>
            <div class="mb-3">
                <select class="form-select" id="certificateSelect" aria-label="Default select example">
                    <option value="0">Selecione um certificado</option>
                </select>
            </div>
        </fieldset>
        <button type="button" id="btnAssinar"
                style="margin-bottom: 5px;background: #2c5676;color:white;border:none;padding:5px;border-radius: 5px">
            Assinar <i class="fa fa-pen"></i>
        </button>
        <a href="#" id="btnHabilitarAssinador" style="text-align: right"></a>
        <table
            id="data-table"
            class="table table-sm"
            data-height="500"
            data-virtual-scroll="true"
        >
        </table>
    </fieldset>

</div>
<input type="hidden" id="qrcode_link" value="<?= env('URL_AUTENTICIDADE_DOCUMENTO') ?>"/>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/socket.io.js"></script>

<script type="text/javascript">

    var urlApi;
    var urlApiProtocolo;
    var signerConfig;
    var timeoutHandle;

    var arquivosAssinar = [];
    var arquivosAssinando = [];
    var arquivosAssinados = [];

    const btnAssinar = document.getElementById("btnAssinar");
    const btnHabilitarAssinador = document.getElementById("btnHabilitarAssinador");
    const certificateSelect = document.getElementById('certificateSelect');
    const inputQrcodeLink = document.getElementById('qrcode_link');
    const codigoProcesso = '<?= $_GET["codigoProcesso"];?>';
    const procandamint = '<?= $_GET["procandamint"];?>';
    const divProgressbarDocumentos = document.getElementById('progressbar_documentos');
    const divTitleProgressbar = document.getElementById('title_progressbar');
    const progressbar = document.getElementById('progressbar');
    const divStatus = document.getElementById('progressbar_status');
    const divProgressbarCount = document.getElementById('progressbar_count');
    let totalProgresso = 0;
    const table = jQuery("#data-table");
    const socket = io('http://localhost:9000');

    socket.on('connect', () => {
        socket.emit('getCertificates', {});
        btnHabilitarAssinador.innerHTML = 'Assinador A3 conectado <i class="fa fa-circle" style="color: green"></i>';
    });

    socket.on('connect_error', () => {
        btnHabilitarAssinador.innerHTML = 'Assinador A3 desconectado <i class="fa fa-circle" style="color: red"></i>';
    });

    socket.on('disconnect', () => {
        btnHabilitarAssinador.innerHTML = 'Assinador A3 desconectado <i class="fa fa-circle" style="color: red"></i>';
    });

    socket.on('certificates', (event) => {
        let certificates = event.certList;
        let selectBox = certificateSelect;

        selectBox.options.length = 0;

        selectBox.add(new Option('Selecione um certificado', 0));

        certificates.forEach((value, index, arr) => {
            let newOption = new Option(value, value);
            selectBox.add(newOption);
        });

    });

    socket.on('signed', async (event) => {
        btnAssinar.disabled = false;
        let fileB64 = event.fileB64;
        let arquivoAssinado = arquivosAssinando.filter(file => {
            return parseInt(event.fileID) === parseInt(file.id_estorage);
        }).shift();
        console.log(`Recebido arquivo '${arquivoAssinado.fileID}' assinado`)
        arquivosAssinando = arquivosAssinando.filter(file => {
            return parseInt(event.fileID) !== parseInt(file.id_estorage);
        });
        arquivoAssinado.base64 = fileB64;
        arquivosAssinados.push(arquivoAssinado);
        atualizarBarraProgresso(arquivosAssinados.length);
        await assinarProximoDocumento();
    });

    socket.on('error', (event) => {
        window.clearTimeout(timeoutHandle);
        alert(event.urlDecode());
        limparTelaAssinador(false);
    });


    const limparTelaAssinador = (lTimeout = false) => {
        if (lTimeout) {
            alert("Erro ao assinar documentos, tempo de execução excedido.");
        }
        arquivosAssinar = [];
        arquivosAssinando = [];
        arquivosAssinados = [];
        fecharBarraProgresso();
        pesquisarDocumentos();
    }

    table.bootstrapTable({
        data: [],
        class: "table table-sm",
        // search: true,
        checkboxHeader: false,
        showFooter: true,
        columns: [
            {
                field: 'checkbox',
                checkbox:true
            },
            {
                title: 'Documento',
                field: 'descricao',
                align: 'center',
            },
            {
                title: 'Assinado Por',
                field: 'assinado_por',
                align: 'left',
                formatter: (value, row) => {
                    return row.nome_assinou;
                }
            },
            {
                title: 'Assinado',
                field: 'assinado',
                align: 'left',
                formatter: (value, row) => {
                    return value ? 'Sim' : 'Não'
                }
            }, {
                field: 'acoes',
                title: 'Açoes',
                align: 'center',
                events: {
                    'click .abrirPdf': function (e, value, row, index) {
                        window.open(`db_visualizar_estorage.php?id=${row.id_estorage}`, null, 'left=100,top=100,width=500,height=500;');
                    },
                },
                formatter: () => {
                    return `
                           <a href="#" class="abrirPdf"><i class="fas fa-file-pdf" style="color:red"></i> Ver PDF </a>
                    `
                }
            }
        ]
    });

    function atualizarBarraProgresso(progresso, texto = '') {
        if (texto !== '') {
            divTitleProgressbar.innerText = texto;
        }
        let porcentagem = Math.ceil((progresso * 100) / totalProgresso);
        divProgressbarCount.innerText = `${progresso} de ${totalProgresso}`;
        divStatus.style.width = `${porcentagem}%`;
        divStatus.innerText = `${porcentagem}%`;
    }

    function mostrarBarraProgresso(total, texto) {
        divTitleProgressbar.innerText = texto;
        divStatus.style.width = '0%';
        divStatus.innerText = '0%';
        totalProgresso = total;
        divProgressbarCount.innerText = `0 de ${totalProgresso}`;
        divProgressbarDocumentos.removeAttribute('hidden');
    }

    function fecharBarraProgresso() {
        totalProgresso = 0;
        divProgressbarDocumentos.setAttribute('hidden', 'hidden');
    }

    async function AssinarDocumentos() {
        arquivosAssinar = [];
        const documentosSelecionados = table.bootstrapTable("getSelections");
        if (documentosSelecionados.length < 1) {
            alert("Selecione no mínimo um documento para assinar!");
            return;
        }

        if (empty(certificateSelect.value)) {
            alert("Selecione o certificado");
            return;
        }
        arquivosAssinar = documentosSelecionados;
        mostrarBarraProgresso(arquivosAssinar.length, 'Assinando documentos, aguarde...');
        assinarProximoDocumento();
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(() => {
            limparTelaAssinador(true);
        }, 60*3*1000);
    }

    btnAssinar.addEventListener("click", AssinarDocumentos);

    async function assinarProximoDocumento() {
        if (arquivosAssinar.length === 0) {
            await salvarArquivosAssinados();
            return;
        }
        const arquivoAssinar = arquivosAssinar.shift();

        const codigoEstorage = arquivoAssinar.id_estorage;
        const parametrosAssinatura = {...signerConfig};
              parametrosAssinatura.fileID = codigoEstorage;
              parametrosAssinatura.fileB64 = "";
              parametrosAssinatura.fileCertificate = certificateSelect.value;
              parametrosAssinatura.isCertBase64 = false;
              parametrosAssinatura.qrcode_link = inputQrcodeLink.value;
              parametrosAssinatura.qrcode_hash = arquivoAssinar.qrcode_hash;

        const response = await HttpClient.get(
            `${PHPSession.requestApi}/assinador/obter-arquivo-base64/${codigoEstorage}`,
            {reportProgress: false}
        );

        let data = response.data;
        parametrosAssinatura.fileB64 = data.content_estorage;
        parametrosAssinatura.id_estorage = codigoEstorage;
        parametrosAssinatura.sequencial = arquivoAssinar.sequencial;
        parametrosAssinatura.qrcode_hash = arquivoAssinar.qrcode_hash;
        arquivosAssinando.push(parametrosAssinatura);
        socket.emit('sign', parametrosAssinatura);
        btnAssinar.disabled = true;
        console.log(`Enviando arquivo '${parametrosAssinatura.fileID}' para o assinador`);
    }

    async function salvarArquivosAssinados() {
        atualizarBarraProgresso(arquivosAssinados.length, 'Salvando documentos assinados...');
        let response = null;
        for (let arquivo of arquivosAssinados) {
            console.log(`Salvando... ${arquivo.sequencial}`);
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            formData.append('base64', arquivo.base64);
            formData.append('sequencial', arquivo.sequencial);
            formData.append('id_estorage', arquivo.id_estorage);
            formData.append('qrcode_hash', arquivo.qrcode_hash);
            response = await HttpClient.post(
                `${urlApiProtocolo}/documentos/atualizar-documento-assinado`,
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
        pesquisarDocumentos();
    }

    function pesquisarDocumentos() {
        js_divCarregando('Carregando documentos', 'loading_message');
        const data = new FormData();
        data.append('codigoProcesso', codigoProcesso);
        data.append('procandamint', procandamint);
        HttpClient.post(`${urlApi}/patrimonial/protocolo/processo/processodocumento/documentosPorProcAndamInt`, {body: data}).then(response => {
                if (response.error === true) {
                    alert(response.message);
                    js_removeObj("loading_message");
                    return;
                }

                var arquivosEstorage = response.data.filter((documento) => {
                    var re = /(?:\.([^.]+))?$/;
                    var extensao = re.exec(documento.descricao)[1];
                    return !extensao || extensao.includes('pdf', 'PDF');
                });

                js_removeObj("loading_message");
                if (arquivosEstorage.length === 0) {
                    alert("Nenhum documento encontrado para o processo.");
                    return false;
                }

                table.bootstrapTable('load', arquivosEstorage);

            });
    }

    function buscarConfiguracaoAssinador(){
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/assinador/obter-configuracao`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                signerConfig = response.data;
            });
    }

    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
            urlApiProtocolo = `${urlApi}/patrimonial/protocolo`;
            pesquisarDocumentos();
            buscarConfiguracaoAssinador();
        });

    });


</script>
</body>
</html>
