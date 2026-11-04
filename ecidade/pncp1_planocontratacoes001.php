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
            <div class="subcontainer">
                <fieldset>
                    <table class="form-container">
                        <tr>
                            <td>
                                <label for="pn05_codigo" id="ancoraPca">PCA:</label>
                            </td>
                            <td>
                                <input type="text" id="pn05_codigo">
                                <input id="pn02_nome" style="width: 296px">
                            </td>
                        </tr>
                        <tr id="trUnidadeEscolha">
                            <td>
                                <label for="unidadeEcidadeRequisitante">Unidade Requisitante:</label>
                            </td>
                            <td>
                                <select name="unidadeEcidadeRequisitante" id="unidadeEcidadeRequisitante">
                                    <option value="sim" selected>Sim</option>
                                    <option value="nao" >Não</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="trUnidadeRequisitante">
                            <td>
                                <label for="ac16_coddepto" id="ancoraUnidadeRequisitante">Unidade Requisitante:</label>
                            </td>
                            <td>
                                <input type="text" id="coddepto" value="<?= $departamento ?>">
                                <input id="descrdepto" style="width: 296px" maxlength="255">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="departamentoSessao" value="<?= $departamento ?>" hidden>
                            </td>
                        </tr>
                        <tr id="trUnidadeRequisitanteTexto" hidden>
                            <td>
                                <label  id="unidadeRequisitante">Unidade Requisitante:</label>
                            </td>
                            <td>
                                <input type="text" id="unidadeRequisitanteTexto" maxlength="255" class="field-size9">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="catalogoMaterialServico">Catálogo de materiais/serviços:</label>
                            </td>
                            <td>
                                <select name="catalogoMaterialServico" id="catalogoMaterialServico">
                                    <option value="1">Outros</option>
                                    <option value="2">CNBS</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="pc16_codmater" id="ancoraMaterial">Código do Material/Serviço:</label>
                            </td>
                            <td>
                                <input type="text" id="pc01_codmater">
                                <input type="text" id="pc01_descrmater" style="width: 296px">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="categoriaItem">Categoria do Item do PCA:</label>
                            </td>
                            <td>
                                <select name="categoriaItem" id="categoriaItem">
                                    <option value="0" disabled selected>Selecione</option>
                                    <option value="1">Material</option>
                                    <option value="2">Serviço</option>
                                    <option value="3">Obras</option>
                                    <option value="4">Serviços de Engenharia</option>
                                    <option value="5">Soluções de TIC</option>
                                    <option value="6">Locação de Imóveis</option>
                                    <option value="7">Alienação/Concessão/Permissão</option>
                                    <option value="8">Obras e Serviços de Engenharia</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="unidadeEcidade">Unidade de medida:</label>
                            </td>
                            <td>
                                <select name="unidadeEcidade" id="unidadeEcidade">
                                    <option value="sim" selected>Sim</option>
                                    <option value="nao">Não</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="unidadeFornecimento">Unidade de Fornecimento:</label>
                            </td>
                            <td>
                                <select name="unidadeFornecimento" id="unidadeFornecimento" hidden>
                                    <option value="0">Selecione</option>
                                </select>
                                <input type="text" class="field-size9" id="unidadeFornecimentoTexto" maxlength="255">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="quantidade">Quantidade:</label>
                            </td>
                            <td>
                                <input type="text" id="quantidade" class="field-size9">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="valorUnitario">Valor Unitário:</label>
                            </td>
                            <td>
                                <input id="valorUnitario" class="field-size9" maxlength="21">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="valorTotal">Valor Total:</label>
                            </td>
                            <td>
                                <input id="valorTotal" class="field-size9" maxlength="21" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="valorOrcamento">Valor Orçamento Exercício:</label>
                            </td>
                            <td>
                                <input id="valorOrcamento" class="field-size9" maxlength="21">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="dataDesejada">Data Desejada:</label>
                            </td>
                            <td>
                                <input id="dataDesejada">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
                            </td>
                            <td>
                                <input type="text" id="codigoGrupo" hidden>
                                <input type="text" id="descricaoGrupo" hidden>
                                <input type="text" id="classificacaoCatalogo" hidden>
                                <input type="text" id="anoPCA" hidden>
                                <input id="contadorPca" hidden>
                            </td>
                        </tr>
                    </table>
                </fieldset>
        </fieldset>
    </div>
</div>
<div class="subcontainer">
    <button class="btn btn-light" id="btnAdicionar">
        <i class="fa fa-plus-circle" aria-hidden="true"></i>
        Adicionar
    </button>
</div>
</div>
<div class="subcontainer">
    <fieldset id="ctnTable" style="width: 1500px">
        <legend>Itens do Plano</legend>
        <table id="table-itens"
               class="table table-sm"
               data-height="300"
               data-virtual-scroll="true"
               data-remember-order="true"
               style="width: 100%;">
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
    const codigoGrupo = document.getElementById('codigoGrupo');
    const classificacaoCatalogo = document.getElementById('classificacaoCatalogo');
    const descricaoGrupo = document.getElementById('descricaoGrupo');
    const categoriaItem = document.getElementById('categoriaItem');
    const catalogoMaterialServico = document.getElementById('catalogoMaterialServico');
    const codigoUnidade = document.getElementById('codigoUnidade');
    const cnpj = document.getElementById('cnpj');
    const dataDesejada = new DBInputDate(document.getElementById("dataDesejada"));
    const tabelaItens = jQuery('#table-itens');
    const ancoraMaterial = document.getElementById('ancoraMaterial');
    const codigoMaterial = document.getElementById('pc01_codmater');
    const descricaoMaterial = document.getElementById('pc01_descrmater');
    const ancoraUnidadeRequisitante = document.getElementById('ancoraUnidadeRequisitante');
    const ancoraPca = document.getElementById('ancoraPca');
    const trUnidadeRequisitante = document.getElementById('trUnidadeRequisitante');
    const trUnidadeEscolha = document.getElementById('trUnidadeEscolha');
    const trUnidadeRequisitanteTexto = document.getElementById('trUnidadeRequisitanteTexto');
    const unidadeRequisitanteTexto = document.getElementById('unidadeRequisitanteTexto');
    const codigoDepartamento = document.getElementById('coddepto');
    const codigoPca = document.getElementById('pn05_codigo');
    const unidadePca = document.getElementById('pn02_nome');
    const unidadeFornecimento = document.getElementById('unidadeFornecimento');
    const unidadeFornecimentoTexto = document.getElementById('unidadeFornecimentoTexto');
    const descricaoDepartamento = document.getElementById('descrdepto');
    const unidadeEcidadeRequisitante = document.getElementById('unidadeEcidadeRequisitante');
    const quantidade = document.getElementById('quantidade');
    const departamentoSessao = document.getElementById('departamentoSessao');
    const valorUnitario = document.getElementById('valorUnitario');
    const btnAdicionar = document.getElementById('btnAdicionar');
    const valorTotal = document.getElementById('valorTotal');
    const valorOrcamento = document.getElementById('valorOrcamento');
    const unidadeEcidade = document.getElementById('unidadeEcidade');
    const anoPCA = document.getElementById('anoPCA');
    const contadorPca = document.getElementById('contadorPca');
    const routes = {};
    let unidades = 0;
    let campo1Timeout;
    let contadorItem = 0;

    valorUnitario.addEventListener('change', () => {
        if (quantidade.value !== '' && valorUnitario.value !== '') {
            let quantidadeItem = parseFloat(quantidade.value.replace('.', '').replace(',', '.'));
            let valorUnitarioItem = parseFloat(valorUnitario.value.replace('.', '').replace(',', '.'));
            let total = (quantidadeItem * valorUnitarioItem);
            valorTotal.value = total.toLocaleString(
                'pt-BR',
                {style: 'decimal', useGrouping: 'true', minimumFractionDigits: '4', maximumFractionDigits: '4'}
            );
        }
    })

    unidadeEcidadeRequisitante.addEventListener('change', () => {
        codigoDepartamento.hidden = true;
        descricaoDepartamento.hidden = true;
        unidadeRequisitanteTexto.hidden = false;
        trUnidadeRequisitanteTexto.hidden = false;
        trUnidadeRequisitante.hidden = true;
        if (unidadeEcidadeRequisitante.value === 'sim') {
            trUnidadeRequisitanteTexto.hidden = true;
            trUnidadeRequisitante.hidden = false;
            codigoDepartamento.hidden = false;
            descricaoDepartamento.hidden = false;
            unidadeRequisitanteTexto.value = '';
            unidadeRequisitanteTexto.hidden = true;
        }
    })

    quantidade.addEventListener('input', listenerValor);

    valorUnitario.addEventListener('input', listenerValor);

    valorTotal.addEventListener('input', listenerValor);

    valorOrcamento.addEventListener('input', listenerValor);

    unidadeEcidade.addEventListener('change', () => {
        unidadeFornecimento.hidden = true;
        unidadeFornecimentoTexto.hidden = false;

        if (unidadeEcidade.value === 'sim') {
            unidadeFornecimento.hidden = false;
            unidadeFornecimentoTexto.hidden = true;
            unidadeFornecimentoTexto.value = '';
            buscaUnidadeFornecimento();
        }
    });

    function buscarItens() {
        const formData = new FormData();
        formData.append('codigoPca', codigoPca.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.buscarItens, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            tabelaItens.bootstrapTable('removeAll');

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
                    classificacaoCatalogo: item.pn06_classificacaocatalogo,
                    categoriaItem: 'item.categoriaItem',
                    codigoItem: item.pn06_codmater,
                    classificacaoSuperiorCodigo: item.pn06_classificacaosuperiorcodigo,
                    classificacaoSuperiorNome: item.pc03_descrgrupo,
                    unidadeFornecimentoCodigo: item.pn06_unidadefornecimentocodigo
                }

                tabelaItens.bootstrapTable('insertRow', {
                    index: item.pn06_item,
                    row: itemBusca
                });
                contadorItem = item.pn06_item;
            }
        });
    }
    window.addEventListener('load', async () => {

    })
    function verificaEnteAutorizado() {
        const formData = new FormData();
        formData.append('verificar', true);
        PHPSession.appendFormData(formData);
        HttpClient.post(routes.habilitar, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            if (response.data.length > 0 && response.data[0].pn07_habilitado) {
                trUnidadeEscolha.hidden = true;
                trUnidadeRequisitante.hidden = true;
            }
        })
    }

    function formataMoeda(valor) {
        return Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(valor)
    }
    btnAdicionar.addEventListener('click', () => {
        if (!validarCampos()) {
            return;
        }
        let update = false;
        contadorItem++;
        let unidade = unidadeFornecimento.selectedOptions[0].textContent;
        let unidadeRequisitanteDescricao = unidadeRequisitanteTexto.value;
        if (unidadeEcidade.value === 'nao') {
            unidade = unidadeFornecimentoTexto.value;
        }
        if (unidadeEcidadeRequisitante.value === 'sim') {
            unidadeRequisitanteDescricao = descricaoDepartamento.value;
        }
        const itens = {
            codigoPca: codigoPca.value,
            categoriaItemPca: categoriaItem.value,
            descricao: descricaoMaterial.value,
            numeroItem: contadorItem,
            unidadeFornecimento: unidade,
            codigoDepartamento: codigoDepartamento.value,
            unidadeFornecimentoCodigo: unidadeFornecimento.value,
            quantidade: quantidade.value,
            valorUnitario: valorUnitario.value,
            valorTotal: valorTotal.value,
            valorOrcamentoExercicio: valorOrcamento.value,
            unidadeRequisitante: unidadeRequisitanteDescricao,
            dataDesejada: dataDesejada.value,
            catalogo: catalogoMaterialServico.value,
            classificacaoCatalogo: classificacaoCatalogo.value,
            categoriaItemDescricao: categoriaItem.selectedOptions[0].textContent,
            codigoItem: codigoMaterial.value,
            classificacaoSuperiorCodigo: codigoGrupo.value,
            classificacaoSuperiorNome: descricaoGrupo.value,
            catalogoDescr: catalogoMaterialServico.selectedOptions[0].textContent,
            itemUpdate: contadorPca.value
        }

        for (let item of tabelaItens.bootstrapTable('getData')) {
            if (item.numeroItem === parseInt(contadorPca.value)) {
                update = true;
                const formData = new FormData();
                formData.append('item', JSON.stringify(itens));
                formData.append('codigoPca', codigoPca.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.editarItem, {body: formData}).then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    tabelaItens.bootstrapTable('removeAll')
                    for (let item of response.data) {
                        const itemBusca = {
                            numeroItem: item.pn06_item,
                            categoriaItemDescricao: item.pn06_categoriaitem,
                            descricao: item.pc01_descrmater,
                            unidadeFornecimento: item.m61_descr,
                            quantidade: item.pn06_quantidade.toLocaleString('pt-BR'),
                            valorUnitario: formataMoeda(item.pn06_valorunitario),
                            valorTotal: formataMoeda(item.pn06_valortotal),
                            valorOrcamentoExercicio: formataMoeda(item.pn06_valororcamento),
                            unidadeRequisitante: item.descrdepto,
                            dataDesejada: item.pn06_datadesejada,
                            classificacaoCatalogo: item.pn06_classificacaocatalogo,
                            categoriaItem: 'item.categoriaItem',
                            codigoItem: item.pn06_codmater,
                            classificacaoSuperiorCodigo: item.pn06_classificacaosuperiorcodigo,
                            classificacaoSuperiorNome: item.pc03_descrgrupo,
                            unidadeFornecimentoCodigo: item.pn06_unidadefornecimentocodigo
                        }

                        tabelaItens.bootstrapTable('insertRow', {
                            index: item.pn06_item,
                            row: itemBusca
                        });
                        contadorItem = item.pn06_item;
                    }
                })
            }
        }

        let itemPca = itens;
        itemPca = JSON.stringify(itemPca);
        const formData = new FormData();
        formData.append('itensPlano', itemPca);
        formData.append('codigoPca', codigoPca.value);
        formData.append('numeroItem', contadorItem);
        PHPSession.appendFormData(formData);
        if (!update) {
            HttpClient.post(routes.incluirItem, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                itens.numeroItem = response.data;
                contadorItem = response.data;
                tabelaItens.bootstrapTable('insertRow', {
                    index: contadorItem,
                    row: itens
                });
            });
        }
        limparCampos();
    })

    async function removerItem(item) {
        let responseItem;
        const formData = new FormData();
        formData.append('numeroItem', item);
        PHPSession.appendFormData(formData);
        await HttpClient.post(routes.removerItem, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                responseItem = false;
            }
            responseItem = true;
        })
        return responseItem;
    }

    function preencherFormulario(row) {
        contadorPca.value = row.numeroItem;
        codigoMaterial.value = row.codigoItem;
        categoriaItem.value = row.categoriaItemDescricao;
        unidadeFornecimento.value = row.unidadeFornecimentoCodigo;
        quantidade.value = row.quantidade;
        valorUnitario.value = row.valorUnitario.replace('R$', '').trim();
        valorTotal.value = row.valorTotal.replace('R$', '').trim();
        valorOrcamento.value = row.valorOrcamentoExercicio.replace('R$', '').trim();
        dataDesejada.value = row.dataDesejada;
        codigoGrupo.value = row.classificacaoSuperiorCodigo;
        codigoMaterial.dispatchEvent(new Event('change'));
        codigoGrupo.value = row.classificacaoSuperiorCodigo;
        classificacaoCatalogo.value = row.classificacaoCatalogo
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

    new DBLookUp(ancoraUnidadeRequisitante, codigoDepartamento, descricaoDepartamento, {
        'arquivo': 'func_db_depart.php',
        'label': 'Pesquisa',
        'objetoLookUp': 'db_iframe_db_depart',
    });

    new DBLookUp(ancoraPca, codigoPca, unidadePca, {
        'arquivo': 'func_planocontratacao.php',
        'label': 'Pesquisa',
        'objetoLookUp': 'db_iframe_planocontratacao',
        'aCamposAdicionais': ['pn05_ano'],
        'fCallBack': (a,b,ano) => {
            anoPCA.value = ano;
            buscarItens();
            verificaEnteAutorizado();
        }
    });

    new DBLookUp(ancoraMaterial, codigoMaterial, descricaoMaterial, {
        'arquivo': 'func_pcmatersolicita.php',
        'label': 'Pesquisa de contratos',
        'objetoLookUp': 'db_iframe_pcmater',
        'aCamposAdicionais': ['pc03_codgrupo', 'pc03_descrgrupo', 'pc01_servico'],
        'fCallBack': (a, b, grupoCodigo, grupoDescricao, servico) => {
            if (grupoCodigo !== 'f') {
                codigoGrupo.value = grupoCodigo;
            }
            classificacaoCatalogo.value = servico === 't' ? '2' : '1';
            descricaoGrupo.value = grupoDescricao;
        }
    });

    jQuery(async () => {
        let apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        routes.buscarEntidade = `${apiUrl}/patrimonial/pncp/unidades/buscarEntidade/`;
        routes.buscarUnidadesAtivas = `${apiUrl}/patrimonial/pncp/unidades/buscarUnidadesAtivas/`;
        routes.verificaIntegracao = `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`;
        routes.buscarUnidades = `${apiUrl}/patrimonial/pncp/planoContratacoes/buscar-unidades/`;
        routes.incluirPCA = `${apiUrl}/patrimonial/pncp/planoContratacoes/incluir/`;
        routes.incluirItem = `${apiUrl}/patrimonial/pncp/planoContratacoes/incluir-item/`;
        routes.buscarItens = `${apiUrl}/patrimonial/pncp/planoContratacoes/buscar-itens/`;
        routes.removerItem = `${apiUrl}/patrimonial/pncp/planoContratacoes/remover-item/`;
        routes.editarItem = `${apiUrl}/patrimonial/pncp/planoContratacoes/editar-item/`;
        routes.habilitar = `${apiUrl}/patrimonial/pncp/integracao/bloquear-unidade-requisitante/`;
        codigoDepartamento.dispatchEvent(new Event('change'));
        verificaIntegracaoAtiva();
        verificaEnteAutorizado();

        window.operateEvents = {
            'click .excluir': (e, d, row) => {
                removerItem(row.numeroItem).then((response) => {
                    if(response){
                        tabelaItens.bootstrapTable('remove', {
                            field: 'numeroItem',
                            values: [row.numeroItem]
                        });
                    }
                });
            },
            'click .alterar': (e, d, row) => {
                preencherFormulario(row);
            }
        }
        const colunasItens = [
            {
                field: 'numeroItem',
                title: 'Seq. Item',
                halign: 'center',
                align: 'center',
                sortable: true
            },
            {
                field: 'descricao',
                title: 'Descrição',
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
                    if (valor.toString() === '1' ) {
                        classificacao = 'Material'
                    }
                    return classificacao;
                }
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
            },
            {
                field: 'valorUnitario',
                title: 'Valor Unitário',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'valorTotal',
                title: 'Valor Total',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'valorOrcamentoExercicio',
                title: 'Valor Orçamento',
                halign: 'center',
                align: 'center',
            },
            {
                field: 'unidadeRequisitante',
                title: 'Unidade Requisitante',
                halign: 'center',
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

    function buscaUnidadeFornecimento() {
        unidadeFornecimento.hidden = false;
        unidadeFornecimentoTexto.hidden = true;
        HttpClient.post(routes.buscarUnidades).then(response => {
            if (response.error) {
                alert(response.message)
                return;
            }
            deleteChild(unidadeFornecimento);
            mostrarUnidades(response.data);
        });
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

    function mostrarUnidades(unidades) {
        unidades.forEach((unidades) => {
            let unidade = document.createElement('option');
            unidade.value = unidades.m61_codmatunid;
            let unidadeDescricao = document.createTextNode(unidades.m61_descr);
            unidade.appendChild(unidadeDescricao);
            unidadeFornecimento.appendChild(unidade);
        });
    }

    function limparCampos() {
        unidadeRequisitanteTexto.value = '';
        catalogoMaterialServico.value = '1';
        descricaoMaterial.value = '';
        codigoMaterial.value = '';
        unidadeFornecimentoTexto.value = '';
        quantidade.value = '';
        valorUnitario.value = '';
        valorTotal.value = '';
        valorOrcamento.value = '';
        dataDesejada.value = '';
        categoriaItem.value = '0';
    }

    function verificaIntegracaoAtiva() {
        catalogoMaterialServico.value = '1';
        catalogoMaterialServico.disabled = true;
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
                btnAdicionar.disabled = true;
                return;
            }
            buscaUnidadeFornecimento();
        });
    }

    function deleteChild(campo) {
        var child = campo.lastElementChild;
        while (child) {
            campo.removeChild(child);
            child = campo.lastElementChild;
        }
    }

    function validarCampos() {
        if (unidadeEcidadeRequisitante.value === 'nao' && unidadeRequisitanteTexto.value === '' ) {
            alert('Preencha a Unidade Requisitante.');
            return false;
        }
        if (unidadeEcidadeRequisitante.value === 'sim' && codigoDepartamento.value === '') {
            alert('Preencha a Unidade Requisitante.');
            return false;
        }
        if (codigoMaterial.value === '') {
            alert('Preencha o Código do Material/Serviço.');
            return false;
        }
        if (categoriaItem.value === '0') {
            alert('Preencha a categoria do item do PCA.');
            return false;
        }
        if (unidadeEcidade.value === 'nao' && unidadeFornecimentoTexto.value === '') {
            alert('Preencha a Unidade de Fornecimento.');
            return false;
        }
        if (quantidade.value === '') {
            alert('Preencha a quantidade.');
            return false;
        }
        if (valorUnitario.value === '') {
            alert('Preencha o Valor Unitário.');
            return false;
        }
        if (valorTotal.value === '') {
            alert('Preencha o Valor Total.');
            return false;
        }
        if (valorOrcamento.value === '') {
            alert('Preencha o Valor do Orçamento.');
            return false;
        }
        if (dataDesejada.value === null) {
            alert('Preencha a Data Desejada.');
            return false;
        }
        if (dataDesejada.__toLocaleDateString().slice(-4) !== anoPCA.value) {
            alert('Data Desejada para o Item é diferente ao Ano do PCA.');
            return false;
        }
        return true;
    }

    function listenerValor(e) {
        const target = e.target;
        target.value = target.value.replace(/[^\d,]/g, '');
        let [inteiro, decimal] = target.value.split(',');

        if (inteiro.replaceAll('.', '').length > 17) {
            inteiro = inteiro.substring(0, inteiro.length - 1);
        }

        inteiro = inteiro.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        target.value = inteiro;

        if (decimal !== undefined) {
            decimal = decimal.length > 4 ? decimal.substring(0, 4) : decimal;
            target.value = `${inteiro},${decimal}`;
        }
    }

</script>
</body>
</html>
