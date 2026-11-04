<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBDate.php"));

$parametros = JSON::requestParameters();
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" href="estilos.css"/>
    <link rel="stylesheet" href="estilos/grid.style.css"/>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css"/>
    <script src="scripts/scripts.js"></script>
    <script src="scripts/prototype.js"></script>
    <script src="scripts/strings.js"></script>
    <script src="scripts/datagrid.widget.js"></script>
    <script src="scripts/webseller.js"></script>
    <script src="scripts/widgets/dbcomboBox.widget.js"></script>
    <script src="scripts/classes/http/http.js"></script>
    <script src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js"></script>
    <script src="scripts/widgets/Collection.widget.js"></script>
    <script src="scripts/widgets/DatagridCollection.widget.js"></script>
    <style>
        label {
            font-weight: bold;
        }
    </style>
</head>
<body bgcolor="#cccccc" style='margin-top: 30px;'>
<div class="container" style="width: 525px;">
    <fieldset>
        <input type="hidden" name="tipoIntegracao" id="tipoIntegracao" value="<?php echo $parametros->integracao; ?>">
        <legend id='legend'></legend>
        <table style="width: 450px;">
            <tr id="tr_empregador" class="d-none">
                <td>
                    <label for="empregador">Empregador:</label>
                </td>
                <td>
                    <select name="empregador" id="empregador" style="width: 100%;"></select>
                </td>
            </tr>
            <tr id="tr_contribuinte" class="d-none">
                <td>
                    <label for="contribuinte">Contribuinte: </label>
                </td>
                <td>
                    <select id="contribuinte" name="contribuinte">
                </td>
            </tr>
            <tr>
                <td align="left"><label for="comboRetorno">Retorno:</label></td>
                <td colspan="2" id='comboRetorno'></td>
            </tr>
            <tr>
                <td id="labelCompetencia"></td>
                <td id="formularioCompetencia"></td>
            </tr>
        </table>
        <table id='processamentoRetornos' style="width: 525px;">
            <tr>
                <td colspan='3' id='eventos'></td>
            </tr>
        </table>
    </fieldset>
    <input type="button" id="buscaRetornos" value="Buscar">
</div>
<?php db_menu(); ?>
</body>
<script type="text/javascript">
    const inicializarCompetencia = () => {
        const competenciaFolha = new DBViewFormularioFolha.CompetenciaFolha(false);
        competenciaFolha.renderizaLabel(document.getElementById('labelCompetencia'));
        competenciaFolha.renderizaFormulario(document.getElementById('formularioCompetencia'));
    };

    inicializarCompetencia();

    const EFD_REINF = '1';
    const ESOCIAL = '2';
    const rpcEsocialApi = 'eso4_esocialapi.RPC.php';
    const rpcSped = 'spedapi.RPC.php';
    const rpcRetorno = 'spedretorno.RPC.php';
    const comboRetorno = new DBComboBox('tipoRetorno', 'tipoRetorno', [], 400);
    const retornoCollection = new Collection().setId('evento');
    const gridRetorno = DatagridCollection.create(retornoCollection).configure("order", false);
    const trEmpregador = document.getElementById('tr_empregador');
    const trContribuinte = document.getElementById('tr_contribuinte');
    const selectEmpregador = document.getElementById('empregador');
    const selectContribuinte = document.getElementById('contribuinte');
    const legend = document.getElementById('legend');
    const integracao = document.getElementById('tipoIntegracao').value;
    const buscaRetornos = document.getElementById('buscaRetornos');
    const anoCompetencia = document.getElementById('ano');
    const mesCompetencia = document.getElementById('mes');
    const eventos = document.getElementById('eventos');

    comboRetorno.show(document.getElementById('comboRetorno'));

    const imprimir = (event, item) => {
        const data = new FormData();
        data.append('acao', 'consulta');
        data.append('rota', '/evento/totalizador');
        data.append('idEvento', item.evento);
        data.append('competencia', item.periodo);
        data.append('idEventoRetorno', comboRetorno.getValue());

        if (integracao === EFD_REINF) {
            data.append('cgmContribuinte', selectContribuinte.value);
        } else {
            data.append('cgmEmpregador', selectEmpregador.value);

        }
        HttpClient.post(rpcSped, {body: data}).then(response => {
            if (response.erro) {
                throw response.mensagem;
            }
            const data = new FormData();
            data.append('acao', 'gerar');
            data.append('integracao', integracao);
            data.append('competencia', item.periodo);
            data.append('data', JSON.stringify(response.data.eventos));
            data.append('layout', comboRetorno.getValue());
            data.append('evento', item.evento);
            data.append('dt', response.data.data);

            if (integracao === EFD_REINF) {
                data.append('cgmResponsavel', selectContribuinte.value);
            } else {
                data.append('cgmResponsavel', selectEmpregador.value);
            }

            HttpClient.post(rpcRetorno, {body: data}).then(response => {
                if (response.erro) {
                    throw response.mensagem;
                }
                window.open("db_download.php?arquivo=" + response.caminho);
            }).catch(mensagem => alert(mensagem));
        }).catch(mensagem => alert(mensagem));
    };

    const exportarCSV = (event, item) => {
        const data = new FormData();
        data.append('acao', 'consulta');
        data.append('rota', '/evento/totalizador');
        data.append('idEvento', item.evento);
        data.append('competencia', item.periodo);
        data.append('idEventoRetorno', comboRetorno.getValue());
        if (integracao === EFD_REINF) {
            data.append('cgmContribuinte', selectContribuinte.value);
        } else {
            data.append('cgmEmpregador', selectEmpregador.value);

        }

        HttpClient.post(rpcSped, {body: data}).then(response => {
            if (response.erro) {
                throw response.mensagem;
            }

            const data = new FormData();
            data.append('acao', 'gerarCSV');
            data.append('integracao', integracao);
            data.append('competencia', item.periodo);
            data.append('data', JSON.stringify(response.data.eventos));
            data.append('layout', comboRetorno.getValue());
            data.append('evento', item.evento);
            if (integracao === EFD_REINF) {
                data.append('cgmResponsavel', selectContribuinte.value);
            } else {
                data.append('cgmResponsavel', selectEmpregador.value);
            }

            HttpClient.post(rpcRetorno, {body: data}).then(response => {
                if (response.erro) {
                    throw response.mensagem;
                }

                window.open("db_download.php?arquivo=" + response.caminho);
            }).catch(mensagem => console.log(mensagem));
        }).catch(mensagem => console.log(mensagem));
    };

    const inicializarGrid = () => {
        gridRetorno.addColumn("periodo", {label: "Competência", "width": "120px"}).setOption("align", "center");
        gridRetorno.addColumn("evento", {label: "Evento", "width": "160px"}).setOption("align", "center");
        gridRetorno.addAction("Imprimir", null, (event, item) => imprimir(event, item),true, 'fa-print');
        gridRetorno.addAction("Exportar", null, (event, item) => exportarCSV(event, item),true, 'fas fa-file-csv');
        gridRetorno.show(eventos);
    };

    const populaSelectRetornos = (arrTipos) => {
        for (var tipo of arrTipos) {
            comboRetorno.addItem(tipo.tipo, tipo.titulo.urlDecode());
        }
    };

    const carregarCombo = () => {
        const
            formData = new FormData(),
            objData = {
                exec: "getTiposRetorno",
                integracao: integracao
            };

            formData.append('json', JSON.stringify(objData));
            return HttpClient.post(rpcEsocialApi, {body: formData}).then(response => {

                if (!!response.erro) {
                    return alert(response.sMessage);
                }

                populaSelectRetornos(response.tipos);
            });
    };

    buscaRetornos.addEventListener('click', () => {
        if (anoCompetencia.value === '') {
            return alert('É necessário preencher a competência para buscar os retornos.');
        }

        var competencia = `${anoCompetencia.value}`;
        // Caso o processamento seja mensal
        if (mesCompetencia.value != '') {
            competencia += `-${mesCompetencia.value}`;
        }
        const data = new FormData();
        data.append('acao', 'consulta');
        data.append('rota', '/evento/arquivo_retorno');
        data.append('competencia', competencia);
        data.append('competenciaRetorno', `${anoCompetencia.value}-${mesCompetencia.value}`);

        if (integracao === EFD_REINF) {
            data.append('cgmContribuinte', selectContribuinte.value);
        }

        if (integracao === ESOCIAL) {
            data.append('cgmEmpregador', selectEmpregador.value);
            data.append('ignoracompetencia', true);
        }

        data.append('idEventoRetorno', comboRetorno.getValue());

        HttpClient.post(rpcSped, {body: data}).then(response => {
            if (response.erro) {
                throw response.mensagem;
            }

            if (response.data.length === 0) {
                return alert('Nenhum retorno encontrado com os filtros informados.');
            }
            gridRetorno.clear();

            for (var dado of response.data) {
                retornoCollection.add({
                    periodo: dado.competencia.replace('-', '/'),
                    evento: dado.layout
                });
                gridRetorno.reload();
            }
        }).catch(mensagem => alert(mensagem));
    });

    const inicializar = () => {
        var text = 'Retornos';
        if (integracao === ESOCIAL) {
            text += ' eSocial';
        }

        if (integracao === EFD_REINF) {
            text += ' EFD-Reinf';
        }

        legend.innerText = text;

        const formData = new FormData();
        formData.append('acao', 'inicializar');
        formData.append('integracao', integracao);

        HttpClient.post('sped02_preenchimento.RPC.php', {
            body: formData
        }).then(response => {
            if (response.erro) {
                throw response.mensagem;
            }

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
        }).catch(mensagem => alert(mensagem));
    };

    carregarCombo();
    inicializarGrid();
    inicializar();
</script>
</html>
