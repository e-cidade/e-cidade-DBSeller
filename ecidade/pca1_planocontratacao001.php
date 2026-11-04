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
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('dbforms/db_funcoes.php');
$instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));
$departamento = db_getsession('DB_coddepto');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <style>
        select:disabled, input:disabled {
            color: dimgrey;
        }
    </style>
</head>
<body>
<div>
    <div class="container">
        <fieldset>
            <legend>Plano de Contratações</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="orgao">Órgão:</label>
                    </td>
                    <td>
                        <input type="text" id="orgao" style="margin-left: 52px" class="field-size9" disabled>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="codigoUnidade">Unidade Compradora:</label>
                    </td>
                    <td>
                        <select id="codigoUnidade" name="codigoUnidade" style="width: 384px; margin-left: 52px">
                            <option value="0" selected disabled>Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="anoPCA">Ano PCA:</label>
                    </td>
                    <td>
                        <input
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
                            id="anoPCA"
                            maxlength="4"
                            style="margin-left: 52px"
                            class="field-size9"
                        >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="codigoStatus">Status:</label>
                    </td>
                    <td>
                        <select id="codigoStatus" name="codigoStatus" style="width: 384px; margin-left: 52px">
                            <option value="0" selected disabled>Selecione</option>
                            <option value="1">Em elaboração</option>
                            <option value="2">Fechado</option>
                            <option value="3">Enviado ao PNCP</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
                    </td>
                    <input type="text" id="editar" value="false" hidden>
                    <input type="text" id="codigoPca" value="false" hidden>
                </tr>
            </table>
        </fieldset>
    </div>
</div>
<div class="subcontainer">
    <button class="btn btn-light" id="btnSalvar">
        <i class="fa fa-save" aria-hidden="true"></i>
        Salvar
    </button>
</div>
<div id="modalItens" class="container">
    <div class="subcontainer">
        <fieldset>
            <legend>Itens</legend>
            <table id="table-itens-plano"
                   class="table table-sm"
                   data-virtual-scroll="true"
                   data-show-footer="true"
                   data-footer-style="footerStyle"
                   style="width: 1400px;">
            </table>
        </fieldset>
    </div>
</div>
<div class="subcontainer">
    <fieldset id="ctnTable" style="width: 800px">
        <legend>Planos</legend>
        <table id="table-itens"
               class="table table-sm"
               data-virtual-scroll="true"
               data-remember-order="true"
        </table>
    </fieldset>
</div>

<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    $.noConflict();
    const tabelaItens = jQuery('#table-itens');
    const tabelaItensPlano = jQuery('#table-itens-plano');
    const orgao = document.getElementById('orgao');
    const cnpj = document.getElementById('cnpj');
    const anoPCA = document.getElementById('anoPCA');
    const codigoUnidade = document.getElementById('codigoUnidade');
    const codigoStatus = document.getElementById('codigoStatus');
    const sequencialPNCP = document.getElementById('sequencialPNCP');
    const codigoPca = document.getElementById('codigoPca');
    const btnSalvar = document.getElementById('btnSalvar');
    const routes = {};
    let unidades = 0;
    const editar = document.getElementById('editar');

    const modalItens = document.getElementById('modalItens');
    let windowItens = new windowAux('windowItens', 'Itens do Plano', 1500, 650);
    windowItens.setContent(modalItens);
    windowItens.allowCloseWithEsc(true);
    windowItens.setShutDownFunction(function () {
        windowItens.oDBMask.destroy();
    });

    btnSalvar.addEventListener('click', () => {
        if (!validarCampos()) {
            return;
        }
        elaborar();
    })

    function buscarEntidade() {
        const formData = new FormData();
        formData.append('documento', cnpj.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.buscarEntidade, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            orgao.value = response.data.razaoSocial;
        });
    }

    function getStatus(codigo) {
        let descricao;
        switch (codigo) {
            case 1:
                descricao = 'Em elaboração';
                break;
            case 2:
                descricao = 'Fechado';
                break;
            case 3:
                descricao = 'Enviado ao PNCP';
                break;
            default:
                descricao = 'Codigo não existente';
        }
        return descricao;
    }

    function elaborar() {
        const formData = new FormData();
        formData.append('unidade', codigoUnidade.value);
        formData.append('ano', anoPCA.value);
        formData.append('status', codigoStatus.value);
        formData.append('codigoPca', codigoPca.value);
        PHPSession.appendFormData(formData);
        if (editar.value === 'true') {
            HttpClient.post(routes.editarPlano, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return
                }
                tabelaItens.bootstrapTable('removeAll');
                for (item of response.data) {
                    const pca = {
                        codigoPca: item.pn05_codigo,
                        unidadeCompradora: item.pn02_nome,
                        unidadeCodigo: item.pn05_unidade,
                        anoPca: item.pn05_ano,
                        status: getStatus(item.pn05_status),
                        statusCodigo: item.pn05_status
                    }

                    tabelaItens.bootstrapTable('insertRow', {
                        index: pca.codigoPca,
                        row: pca
                    });
                }
            })
        } else {
            HttpClient.post(routes.elaborar, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message)
                    return;
                }

                const pca = {
                    codigoPca: response.data[0].pn05_codigo,
                    orgao: orgao.value,
                    unidadeCompradora: response.data[0].pn02_nome,
                    unidadeCodigo: response.data[0].pn05_unidade,
                    anoPca: response.data[0].pn05_ano,
                    status: getStatus(parseInt(response.data[0].pn05_status))
                }

                tabelaItens.bootstrapTable('insertRow', {
                    index: pca.codigoPca,
                    row: pca
                });
            })
        }
        editar.value = false;
        limparcampos();
    }

    function limparcampos() {
        anoPCA.value = '';
        codigoStatus.value = '0';
        sequencialPNCP.value = '';
    }

    function buscarUnidades() {
        const formData = new FormData();
        formData.append('documento', cnpj.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.buscarUnidadesAtivas, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message)
            }
            unidades = response.data.length;
            if (response.data.length === 0) {
                alert('Não foi possível identificar nenhuma unidade compradora,' +
                    ' por favor, verifique o cadastro de unidade.');
            } else if (response.data.length === 1) {
                deleteChild(codigoUnidade);
                codigoUnidade.disabled = true;
                let unidade = document.createElement('option');
                unidade.value = response.data[0].pn02_unidade;
                let unidadeNome = document.createTextNode(response.data[0].pn02_nome);
                unidade.appendChild(unidadeNome);
                codigoUnidade.appendChild(unidade);
            } else {
                mostrarUnidadesAtivas(response.data);
            }
        })
    }

    function mostrarUnidadesAtivas(unidadeCompradora) {
        unidadeCompradora.forEach((unidadeCompradora) => {
            let unidade = document.createElement('option');
            unidade.value = unidadeCompradora.pn02_unidade;
            let unidadeNome = document.createTextNode(unidadeCompradora.pn02_nome);
            unidade.appendChild(unidadeNome);
            codigoUnidade.appendChild(unidade);
        });
    }

    function deleteChild(campo) {
        var child = campo.lastElementChild;
        while (child) {
            campo.removeChild(child);
            child = campo.lastElementChild;
        }
    }

    function getPlanoContratacao() {
        const formData = new FormData();
        PHPSession.appendFormData(formData)
        HttpClient.post(routes.buscarPlanos, {body: formData}).then((response) => {
            if (response.error) {
                alert(response.message)
                return;
            }
            tabelaItens.bootstrapTable('removeAll');
            for (item of response.data) {
                const pca = {
                    codigoPca: item.pn05_codigo,
                    unidadeCompradora: item.pn02_nome,
                    unidadeCodigo: item.pn05_unidade,
                    anoPca: item.pn05_ano,
                    sequencialPca: item.pn05_pncp,
                    status: getStatus(item.pn05_status),
                    statusCodigo: item.pn05_status
                }

                tabelaItens.bootstrapTable('insertRow', {
                    index: pca.codigoPca,
                    row: pca
                });
            }
        })
    }

    function validarCampos() {
        if (codigoUnidade.value === '0') {
            alert('Selecione uma Unidade Compradora');
            return false;
        }
        if (anoPCA.value === '') {
            alert('Preencha o Ano do Plano de Contratação.');
            return false;
        }
        if (codigoStatus.value === '0') {
            alert('Selecione um status');
            return false;
        }

        return true;
    }

    function verificaIntegracaoAtiva() {
        const formData = new FormData();
        formData.append('documento', cnpj.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            if (response.data === null) {
                alert("Por favor, verifique a configuração de integração com o PNCP!");
                btnSalvar.disabled = true;
                return;
            }
            buscarEntidade();
            buscarUnidades();
        });
    }

    function preencherCampos(row) {
        if (row.statusCodigo === 3) {
            alert('Plano de Contratação já enviado ao PCA.');
            return;
        }
        codigoPca.value = row.codigoPca;
        editar.value = true;
        codigoUnidade.value = row.unidadeCodigo;
        anoPCA.value = row.anoPca;
        codigoStatus.value = row.statusCodigo;
    }

    function excluirPlano(plano) {
        if (plano.statusCodigo === 3) {
            alert('Plano de Contratação já enviado ao PCA.');
            return;
        }
        const formData = new FormData();
        formData.append('codigoPca', plano.codigoPca);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.excluirPlano, {body: formData}).then((response) => {
            if (response.error) {
                alert(response.message);
                return;
            }
            getPlanoContratacao();
        })
    }

    function buscarItens(codigoPca, visualizar = false){
        const formData = new FormData();
        formData.append('codigoPca', codigoPca);
        if (visualizar) {
            formData.append('visualizar', visualizar);
        }
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.buscarItens, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            for (let item of response.data) {
                const itemBusca = {
                    numeroItem: item.pn06_item,
                    categoriaItemDescricao: item.pn06_categoriaitem,
                    descricao: item.pc01_descrmater,
                    unidadeFornecimento: item.m61_descr,
                    quantidade: new Intl.NumberFormat().format(item.pn06_quantidade),
                    valorUnitario: formataMoeda(item.pn06_valorunitario),
                    valorTotal: formataMoeda(item.pn06_valortotal),
                    valorOrcamentoExercicio: formataMoeda(item.pn06_valororcamento),
                    unidadeRequisitante: item.descrdepto,
                    dataDesejada: item.pn06_datadesejada,
                    classificacaoCatalogo: item.pn06_classificacaocatalogo.toString(),
                    categoriaItem: 'item.categoriaItem',
                    codigoItem: item.pn06_codmater,
                    classificacaoSuperiorCodigo: item.pn06_classificacaosuperiorcodigo,
                    classificacaoSuperiorNome: item.pc03_descrgrupo,
                    unidadeFornecimentoCodigo: item.pn06_unidadefornecimentocodigo
                }

                tabelaItensPlano.bootstrapTable('insertRow', {
                    index: item.pn06_item,
                    row: itemBusca
                });
            }
        });
    }

    function enviarPlano(row) {
        if (row.statusCodigo === 3) {
            let msg = 'Plano de Contratação já enviado ao PCA.';
            let url = `https://pncp.gov.br/app/pca/${cnpj.value}/${row.anoPca}/${row.sequencialPca}`;
            let link = `<a target="_blank" href="${url}">Clique aqui para acessar.</a>`;
            alert(`${msg}\n${link}`);
            return;
        }
        const formData = new FormData()

        formData.append('codigoPca', row.codigoPca);
        formData.append('ano', row.anoPca);
        formData.append('unidadeCodigo', row.unidadeCodigo);
        formData.append('visualizar', true);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.incluir, {body: formData}).then((response) => {
            if (response.error) {
                alert(response.message);
                return;
            }
            let msg = "Plano de Contratação publicado com sucesso.";
            let link = `<a target="_blank" href="${response.data.link}">Clique aqui para acessar.</a>`;
            alert(`${msg}\n${link}`);
            getPlanoContratacao();
        })
    }

    function obterDescricao(codigo) {
        let descricao;

        switch (codigo) {
            case 1:
                descricao = "Material";
                break;
            case 2:
                descricao = "Serviço";
                break;
            case 3:
                descricao = "Obras";
                break;
            case 4:
                descricao = "Serviços de Engenharia";
                break;
            case 5:
                descricao = "Soluções de TIC";
                break;
            case 6:
                descricao = "Locação de Imóveis";
                break;
            case 7:
                descricao = "Alienação/Concessão/Permissão";
                break;
            default:
                descricao = "Código não reconhecido";
                break;
        }

        return descricao;
    }

    function visualizarItens(row) {
        tabelaItensPlano.bootstrapTable('removeAll');
        windowItens.show(0, 0, true);
        buscarItens(row.codigoPca, true)
    }

    function imprimirRelatorio(row) {
        const formData = new FormData();
        formData.append('codigoPca', row.codigoPca);
        formData.append('visualizar', true);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.emitir, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            window.open(response.data.path);
        });
    }

    function formataMoeda(valor) {
        return Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(valor)
    }

    jQuery(async () => {
        let apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        routes.buscarEntidade = `${apiUrl}/patrimonial/pncp/unidades/buscarEntidade/`;
        routes.buscarUnidadesAtivas = `${apiUrl}/patrimonial/pncp/unidades/buscarUnidadesAtivas/`;
        routes.verificaIntegracao = `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`;
        routes.buscarUnidades = `${apiUrl}/patrimonial/pncp/planoContratacoes/buscar-unidades/`;
        routes.elaborar = `${apiUrl}/patrimonial/pncp/planoContratacoes/elaboracao/`;
        routes.buscarPlanos = `${apiUrl}/patrimonial/pncp/planoContratacoes/buscar-planos/`;
        routes.editarPlano = `${apiUrl}/patrimonial/pncp/planoContratacoes/editar-plano/`;
        routes.excluirPlano = `${apiUrl}/patrimonial/pncp/planoContratacoes/excluir-plano/`;
        routes.buscarItens = `${apiUrl}/patrimonial/pncp/planoContratacoes/buscar-itens/`;
        routes.incluir = `${apiUrl}/patrimonial/pncp/planoContratacoes/incluir/`;
        routes.emitir = `${apiUrl}/patrimonial/pncp/planoContratacoes/relatorios/itens-plano`;
        verificaIntegracaoAtiva();
        getPlanoContratacao();

        window.operateEvents = {
            'click .excluir': (e, d, row) => {
                excluirPlano(row);
            },
            'click .alterar': (e, d, row) => {
                preencherCampos(row);
            },
            'click .visualizar': (e, d, row) => {
                visualizarItens(row);
            },
            'click .enviar': (e, d, row) => {
                enviarPlano(row);
            },
            'click .relatorio': (e, d, row) => {
                imprimirRelatorio(row);
            }
        }

        const colunasItensPlano = [
            {
                field: 'numeroItem',
                title: 'Número do Item',
                halign: 'center',
                align: 'center',
                sortable: true,
                footerFormatter : () => {
                    return 'Totalizador';
                }
            },
            {
                field: 'descricao',
                title: 'Descricao',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'categoriaItemDescricao',
                title: 'Categoria Item',
                halign: 'center',
                align: 'center',
                formatter:(valor, data, index) => {
                    if (typeof valor !== 'number') {
                        return valor;
                    }
                    return obterDescricao(valor);
                }
            },
            {
                field: 'classificacaoCatalogo',
                title: 'Material/Serviço',
                halign: 'center',
                align: 'center',
                formatter: (valor, data, index) => {
                    let classificacao = 'Serviço'
                    if (valor === '1') {
                        classificacao = 'Material'
                    }
                    return classificacao;
                }
            },
            {
                field: '',
                title: 'Catálogo',
                halign: 'center',
                align: 'center',
                formatter: () => {
                    return 'Outros';
                }
            },
            {
                field: 'classificacaoSuperiorNome',
                title: 'Grupo Material/Serviço',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'unidadeFornecimento',
                title: 'Unidade Fornecimento',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'quantidade',
                title: 'Quantidade',
                halign: 'center',
                align: 'center',
                footerFormatter: (data) => {
                    const total = data.map((row) => {
                        return +row.quantidade
                    }).reduce((sum, i) => {
                        return sum + i;
                    }, 0);

                    return total.toLocaleString('pt-br', {minimumFractionDigits: 2});
                }
            },
            {
                field: 'valorUnitario',
                title: 'Valor Unitário',
                halign: 'center',
                align: 'center',
                footerFormatter: (data) => {
                    const total = data.map((row) => {
                        return +parseFloat(row.valorTotal.substring(2).replace('.', '').replace(',', '.'))
                    }).reduce((sum, i) => {
                        return sum + i;
                    }, 0);

                    return "R$ " + total.toLocaleString('pt-br', {minimumFractionDigits: 2});
                }
            },
            {
                field: 'valorTotal',
                title: 'Valor Total',
                halign: 'center',
                align: 'center',
                footerFormatter: (data) => {
                    const total = data.map((row) => {
                        return +parseFloat(row.valorTotal.substring(2).replace('.', '').replace(',', '.'))
                    }).reduce((sum, i) => {
                        return sum + i;
                    }, 0);

                    return "R$ " + total.toLocaleString('pt-br', {minimumFractionDigits: 2});
                }
            },
            {
                field: 'valorOrcamentoExercicio',
                title: 'Valor Orçamento',
                halign: 'center',
                align: 'center',
                footerFormatter: (data) => {
                    const total = data.map((row) => {
                        return +parseFloat(row.valorOrcamentoExercicio.substring(2).replace('.', '').replace(',', '.'))
                    }).reduce((sum, i) => {
                        return sum + i;
                    }, 0);

                    return "R$ " + total.toLocaleString('pt-br', {minimumFractionDigits: 2});
                },
            },
            {
                field: 'unidadeRequisitante',
                title: 'Unidade Requisitante',
                halign: 'center',
                align: 'center',
                sortable: true
            },
        ];
        tabelaItensPlano.bootstrapTable({
            locale: 'pt-BR',
            height: 500,
            uniqueId: "numeroItem",
            class: "table table-sm",
            columns: colunasItensPlano,
            showButtonText: true,
            useRowAttrFunc: true,
            reorderableRows: true,
        });

        const colunasItens = [
            {
                field: 'codigoPca',
                title: 'PCA',
                align: 'center',
                sortable: true
            },
            {
                field: 'unidadeCompradora',
                title: 'Unidade Compradora',
                align: 'center',
            },
            {
                field: 'anoPca',
                title: 'Ano',
                align: 'center',
            },
            {
                field: 'status',
                title: 'Status',
                align: 'center',
            },
            {
                field: 'sequencialPca',
                title: 'Seq. PCA',
                align: 'center',
            },
            {
                field: 'acao',
                title: 'Ações',
                halign: 'center',
                align: 'center',
                formatter: (valor, data, index) => {
                    return [
                        '<a class="alterar" href="javascript:void(0)" title="Alterar">',
                        '  <i class="fa fa-edit"></i>',
                        '</a>',
                        '&nbsp;&nbsp;',
                        '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                        '  <i class="fas fa-trash-alt"></i>',
                        '</a>',
                        '&nbsp;&nbsp;',
                        '<a class="visualizar" href="javascript:void(0)" title="Visualizar">',
                        '  <i class="fas fa-eye"></i>',
                        '</a>',
                        '&nbsp;&nbsp;',
                        '<a class="enviar" href="javascript:void(0)" title="Enviar">',
                        '  <i class="fas fa-share"></i>',
                        '</a>',
                        '&nbsp;&nbsp;',
                        '<a class="relatorio" href="javascript:void(0)" title="Imprimir Relatório">',
                        '  <i class="fas fa-file-archive"></i>',
                        '</a>'
                    ].join('');
                },
                events: window.operateEvents
            }
        ];
        tabelaItens.bootstrapTable({
            locale: 'pt-BR',
            height: 250,
            uniqueId: "numeroItem",
            class: "table table-sm",
            columns: colunasItens,
            showButtonText: true,
            useRowAttrFunc: true,
            reorderableRows: true,
        });
    });
</script>
