<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$ativosNovo = 1;
$oGet = db_utils::postMemory($_GET);

if (isset($oGet->ativos)) {
    $ativosNovo = $oGet->ativos;
}

?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<style>
    a {
        color: black;
    }
</style>
<body class="body-default">
    <div class="container" id="">
        <div id="ctnBuscaProfissional">
            <fieldset>
                <legend>Buscar Profissional:</legend>
                <label for="ativos_for">Ativos:</label>
                <select name="ativos_name" id="ativos_id"style="width: 50px;">
                        <option value="1">SIM </option>
                        <option value="0">NÃO </option>
                </select>
                </br> </br>
                <table id="table-profissionais">
                </table>
            </fieldset>
        </div>
        <div id="ctnFormulario" style="display: none">
            <fieldset>
                <form id="formFormacao" name="formFormacao">
                    <legend>Formação do Profissional</legend>
                    <table class="form-container">
                        <tr>
                            <td>
                                <label for="">CGM / Profissional:</label>
                            </td>
                            <td>
                                <input type="hidden" name="ed183_id" id="ed183_id" value="">
                                <input
                                    class="readonly"
                                    type="text"
                                    name="ed183_cgm"
                                    id="ed183_cgm"
                                    style="width: 100px;"
                                    readonly
                                >
                                <input
                                    type="text" name="nomeProfissional"
                                    id="nomeProfissional"
                                    style="width: 555px;"
                                    disabled
                                >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ed183_nomecurso">Nome do Curso:</label>
                            </td>
                            <td>
                                <input type="text" name="ed183_nomecurso" id="ed183_nomecurso" style="width: 658px;">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ed183_tipoformacao">Tipo da Formação:</label>
                            </td>
                            <td>
                                <select name="ed183_tipoformacao" id="ed183_tipoformacao"style="width: 175px;">
                                    <option value="">Selecione </option>
                                </select>

                                <label for="ed183_areaformacao">Área da Formação:</label>
                                <select name="ed183_areaformacao" id="ed183_areaformacao"style="width: 175px;">
                                    <option value="">Selecione </option>
                                </select>

                                <label for="ed183_anoconclusao">Ano de Conclusão:</label>
                                <input type="text"
                                    name="ed183_anoconclusao" id="ed183_anoconclusao" style="width: 80px;">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>

                        <tr  id="anexopos">
                            <td>
                                <b>Anexo da Pós graduação:</b>
                            </td>
                                <td>
                                    <div style="display: flex; align-items: center; width:100%;">
                                        <iframe name="frame_imagemPosgraduacao" id="frame_imagemPosgraduacao" src="edu4_alunodocumentoposgraduacao.php" width="56" height="50" frameborder="1" scrolling="no"></iframe>
                                    <script>
                                        let arquivo = '';
                                        let arquivoPosgraduacao = '';
                                        frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+arquivoPosgraduacao;
                                    </script>
                                    <div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">
                                        <iframe name="frame_posgraduacao" id="frame_posgraduacao" src="edu1_frameposgraduacao.php" width="100%" height="31" frameborder="0" scrolling="no" style="margin-bottom: 0px;margin-top:2px;"></iframe>
                                        <input type="button" value="Excluir Imagem"
                                            onclick=excluirArquivoPosGraduacao();
                                            style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;">
                                    </div>
                                    <input name="oid_arquivoPosgraduacao" type="hidden" id="oid_arquivoPosgraduacao"  size="30">
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
            <button id="salvar">
            <i class="fas fa-save"></i>
                Salvar
            </button>
            <button id="buscar">
                <i class="fas fa-search"></i>
                Buscar Profissional
            </button>
        </div>
    </div>
    <div class="container" style="display: none;" id="ctnGridFormacoes">
        <fieldset>
            <legend>Formacões do Profissional: <span id="spanNomeProfissional"></span></legend>
            <table id="grid-formacoes">
            </table>
        </fieldset>
    </div>
</body>

<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>

<script type="text/javascript">
    const routs = {
        buscaProfissionaisSuperior: `educacao/escola/recursos-humanos/profissionais-com-superior/`,
        buscarAreasFormacao: `educacao/censo/tabelas-censo/areas-pos-graduacao/`,
        buscarTiposFormacao: `educacao/censo/tabelas-censo/tipos-pos-graduacao/`,
        salvar: `educacao/escola/recursos-humanos/salvar-formacao-superior-profissional/`,
        buscarFormacoesDoProfissional: `educacao/escola/recursos-humanos/buscar-formacoes-superior-profissional/`,
        excluirFormacao: `educacao/escola/recursos-humanos/excluir-formacao-superior-profissional/`,
        buscarDocumentoPosGraduacao: `educacao/escola/recursos-humanos/buscar-documento-pos-graducao`,
        salvarDocumentoPosGraduacao: `educacao/escola/recursos-humanos/salvar-documento-pos-graducao`,
        excluirDocumentoPosGraduacao: `educacao/escola/recursos-humanos/excluir-documento-pos-graducao`
    }

    const tableProfisiionais = $('#table-profissionais');
    const gridFormacoes = $('#grid-formacoes');
    const spanNomeProfissional = document.querySelector('#spanNomeProfissional');

    const ctnFormulario = document.querySelector('#ctnFormulario');
    const ctnBuscaProfissional = document.querySelector('#ctnBuscaProfissional');
    const ctnGridFormacoes = document.querySelector('#ctnGridFormacoes');

    const inputCGM = document.querySelector('#ed183_cgm');
    const inputNome = document.querySelector('#nomeProfissional');
    const inputNomeCurso = document.querySelector('#ed183_nomecurso');
    const inputAnoConclusao = document.querySelector('#ed183_anoconclusao');
    const inputId = document.querySelector('#ed183_id');

    const arquivoPos = document.getElementById("oid_arquivoPosgraduacao");

    const btnSalvar = document.querySelector('#salvar');
    const btnBuscar = document.querySelector('#buscar');

    const selectAreaFormacao = document.querySelector('#ed183_areaformacao');
    const selecTipoFormacao = document.querySelector('#ed183_tipoformacao');

    const formulario = document.querySelector('#formFormacao');

    const ativos = document.querySelector('#ativos_id');
    const anexoPos = document.getElementById("anexopos");

    ativos.value = <?= @$ativosNovo ?>;

    const buscaFormacoesProfissional = async (cgm) => {
        const formData = new FormData();
        formData.append('cgm', cgm);
        return await HttpClient.post(`${PHPSession.requestApi}/${routs.buscarFormacoesDoProfissional}`,{body: formData})
            .then(response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                return response.data;
        })
    }

    const carregaGridFormacoes = async (cgm) => {
        spanNomeProfissional.innerHTML = inputNome.value;
        const acoesEventos = {
            'click .alterar': async (e, value, row, index) => {
                inputId.value = row.ed183_id;
                inputNomeCurso.value = row.ed183_nomecurso;
                inputAnoConclusao.value = row.ed183_anoconclusao;
                Object.values(selecTipoFormacao.options).each(opt =>{
                    if (opt.value == row.ed183_tipoformacao) {
                        opt.selected = true;
                    }
                })

                Object.values(selectAreaFormacao.options).each(opt =>{
                    if (opt.value == row.ed183_areaformacao) {
                        opt.selected = true;
                    }
                })
                anexoPos.style.display = '';
                buscarArquivoPosGraduacao(row.ed183_id);
            },
            'click .excluir': async (e, value, row, index) => {
                if (confirm('Tem certeza que deseja excluir?')) {
                    const formData = new FormData();
                    formData.append('ed183_id', row.ed183_id);
                    HttpClient.post(`${PHPSession.requestApi}/${routs.excluirFormacao}`, {body: formData})
                        .then(async response => {
                            alert(response.message);
                            if(response.error) {
                                return;
                            }
                            let formacoes = await buscaFormacoesProfissional(inputCGM.value);
                            gridFormacoes.bootstrapTable('load', formacoes);
                    })
                }
            }
        }

        const formataCampos = (value, row, index, field) => {
            let template = "";
            if (field == 'ed183_tipoformacao'){
                Object.values(selecTipoFormacao.options).each(opt =>{
                    if (opt.value == value) {
                        template = opt.text;
                    }
                })
            } else if (field == 'ed183_areaformacao'){
                Object.values(selectAreaFormacao.options).each(opt =>{
                    if (opt.value == value) {
                        template = opt.text;
                    }
                })
            } else if (field == 'ed183_docpos_estorage'){

                template = "Não";
                if (value !== null) {
                    template = "Sim";
                }

            } else if (field == 'acoes') {
                template =  `<a class="alterar" href="javascript:void(0)" title="Alterar">
                                <i class="fa fa-edit"></i>
                            </a>
                            &nbsp;&nbsp;
                            <a class="excluir" href="javascript:void(0)" title="Excluir">
                                <i class="fas fa-trash-alt"></i>
                            </a>`;
            }
            return template;
        };

        colunas = [
            {
                title: 'Nome do Curso',
                field: 'ed183_nomecurso',
                halign: 'center',
                align: 'right',
                width: '300',
            },
            {
                title: 'Tipo de Formação',
                field: 'ed183_tipoformacao',
                halign: 'center',
                align: 'right',
                width: '200',
                formatter: formataCampos,
            },
            {
                title: 'Área da Formação',
                field: 'ed183_areaformacao',
                halign: 'center',
                align: 'right',
                width: '400',
                formatter: formataCampos,
            },
            {
                title: 'Ano de Conclusão',
                field: 'ed183_anoconclusao',
                halign: 'center',
                align: 'right',
                width: '100',
            },
            {
                title: 'Anexo da Pós graduação',
                field: 'ed183_docpos_estorage',
                halign: 'center',
                align: 'right',
                width: '100',
                formatter: formataCampos,
            },
            {
                title: 'Ações',
                field: 'acoes',
                halign: 'center',
                align: 'right',
                width: '100',
                formatter: formataCampos,
                events: acoesEventos
            }
        ]

        gridFormacoes.bootstrapTable({
            columns: colunas,
            uniqueId: "ed183_id",
            locale: 'pt-BR',
            cache: false,
            search: true,
            class: "table table-sm",
            pageSize: 10,
            pagination: true,
        });

        let formacoes = await buscaFormacoesProfissional(cgm);
        gridFormacoes.bootstrapTable('load', formacoes);
        ctnGridFormacoes.style.display = '';
    }

    const buscarArquivoPosGraduacao = (ed183_id) => {
        const formData = new FormData();
        formData.append('ed183_id', ed183_id);
        HttpClient.post(`${PHPSession.requestApi}/${routs.buscarDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                document.getElementById('oid_arquivoPosgraduacao').value = response.data.arquivo;
                frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+response.data.arquivoPosgraduacao;

        })

    }

    const salvarArquivoPosGraduacao = (ed183_id, arquivo_id) => {
        const formData = new FormData();
        formData.append('ed183_id', ed183_id);
        formData.append('arquivo_id', arquivo_id);
        HttpClient.post(`${PHPSession.requestApi}/${routs.salvarDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                else{
                    frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+"";
                    document.getElementById('oid_arquivoPosgraduacao').value = "";
                }
        })
    }

    const excluirArquivoPosGraduacao = () => {
        const formData = new FormData();
        formData.append('ed183_id', inputId.value);
        HttpClient.post(`${PHPSession.requestApi}/${routs.excluirDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                console.log(response);
        })
        frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+"";
    }

    const carregaTableProfissionais = async () => {
        const acoesEventos = {
            'click .seleciona': async (e, value, row, index) => {
                inputCGM.value = row.cgm;
                inputNome.value = row.z01_nome;
                ctnBuscaProfissional.style.display = 'none';
                ctnFormulario.style.display = '';
                await carregaGridFormacoes(row.cgm);
            }
        }

        const formataCampos = (value, row, index, field) => {
            let template = "";
            if (field == 'cpf'){
                template = `<a class="seleciona">${mascaraCpf(row.cpf)}</a>`
            } else if (field == 'data_nascimento'){
                if (row.data_nascimento != undefined) {
                    let data = row.data_nascimento.split('-');
                    data = new Date(data);
                    template = `<a class="seleciona">${data.toLocaleDateString('pt-BR', {timeZone: 'UTC'})}</a>`;
                }
            } else if (field == 'possuipos') {
                template = value ?
                    `<span style="color:green"><i class="fas fa-check"></i></span>` :
                    `<span style="color:red"><i class="fas fa-times"></i></span>`;
            } else {
                template = `<a class="seleciona">${value}</a>`
            }
            return template;
        };

        const colunas = [
            {
                title: 'CGM',
                field: 'cgm',
                halign: 'center',
                align: 'right',
                width: '100',
                formatter: formataCampos,
                events: acoesEventos
            },
            {
                title: 'Nome do Profissional',
                field: 'z01_nome',
                halign: 'center',
                align: 'left',
                width: '300',
                formatter: formataCampos,
                events: acoesEventos
            },
            {
                title: 'CPF',
                field: 'cpf',
                halign: 'center',
                align: 'center',
                width: '300',
                formatter: formataCampos,
                events: acoesEventos
            },
            {
                title: 'Data de Nascimento',
                field: 'data_nascimento',
                halign: 'center',
                align: 'center',
                width: '300',
                formatter: formataCampos,
                events: acoesEventos
            },
            {
                title: 'Possui Pós Cadastrada',
                field: 'possuipos',
                halign: 'center',
                align: 'center',
                width: '300',
                formatter: formataCampos
            }
        ];

        tableProfisiionais.bootstrapTable({
            columns: colunas,
            uniqueId: "cgm",
            locale: 'pt-BR',
            cache: false,
            search: true,
            class: "table table-sm",
            pageSize: 10,
            pagination: true,
        });

        let escola = PHPSession.getValueSession('DB_coddepto');
        await HttpClient.get(`${PHPSession.requestApi}/${routs.buscaProfissionaisSuperior}${escola}/${ativos.value}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                tableProfisiionais.bootstrapTable('load', response.data);
        });
    }

    const carregaDadosSelects = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${routs.buscarTiposFormacao}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                Object.keys(response.data).each((chave) => {
                    selecTipoFormacao.add(new Option(response.data[chave], chave));
                })
        });

        await HttpClient.get(`${PHPSession.requestApi}/${routs.buscarAreasFormacao}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                Object.keys(response.data).each((chave) => {
                    selectAreaFormacao.add(new Option(response.data[chave], chave));
                })
        });
    }

    const carregaEventos = async () => {
        btnBuscar.addEventListener('click', event => {
            event.preventDefault();
            inputId.value = "";
            inputNomeCurso.value = "";
            inputAnoConclusao.value = "";
            window.location.reload();
        });

        btnSalvar.addEventListener('click', event => {
            event.preventDefault();
            if (formulario.ed183_cgm.value == '') {
                alert('Campo CGM deve ser informado!');
                return;
            }
            if (formulario.ed183_tipoformacao.value == '') {
                alert('Tipo de Formação deve ser informado!');
                return;
            }
            if (formulario.ed183_areaformacao.value == '') {
                alert('Área ds Formação deve ser informada!');
                return;
            }
            if (formulario.ed183_anoconclusao.value == '') {
                alert('Áno de conclusão deve ser informado!');
                return;
            }

            const formData = new FormData(formulario);


            HttpClient.post(`${PHPSession.requestApi}/${routs.salvar}`, {body: formData}).then(async response => {
                alert(response.message);
                if(response.error) {
                    return;
                }
                console.log(response.data);
                if (arquivoPos.value) {
                    salvarArquivoPosGraduacao(response.data, arquivoPos.value);
                }

                inputId.value = "";
                inputNomeCurso.value = "";
                inputAnoConclusao.value = "";
                Object.values(selecTipoFormacao.options).each(opt =>{
                    if (opt.value == "") {
                        opt.selected = true;
                    }
                })

                Object.values(selectAreaFormacao.options).each(opt =>{
                    if (opt.value == "") {
                        opt.selected = true;
                    }
                })

                let formacoes = await buscaFormacoesProfissional(inputCGM.value);
                gridFormacoes.bootstrapTable('load', formacoes);
            })
        });
    }

    ativos.addEventListener("change", event => {
        window.location.href = 'edu1_formacaoprofissional001.php?ativos='+ ativos.value;
    });

    window.addEventListener('load', async () => {
        PHPSession.loadData().then( async () => {
            await carregaDadosSelects();
            await carregaTableProfissionais();
            await carregaEventos();
        });
    })
</script>
<?php db_menu() ?>
