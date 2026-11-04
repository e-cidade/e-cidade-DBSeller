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
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Inserir Resultado(s)</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="l20_codigo" id="licitacao_ancora">Contratação/Edital/Aviso: </label>
                </td>
                <td>
                    <input type="text" id="pn03_liclicita" disabled/>
                    <input type="text" id="pn03_numero" hidden/>
                    <button type="button" id="btnCarregarLicitacao">
                        <i class="fas fa-search"></i>
                        Buscar Dados
                    </button>
                    <a style="color:inherit" id="linkCompra" target="_blank">
                        <button class="field-size2" id="btnConsultarCompra">
                            <i class="fas fa-search "></i>
                            PNCP
                        </button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="modalidadeCompra">Modalidade de Compra:</label>
                </td>
                <td>
                    <input type="text" id="modalidadeCompra" disabled style="width: 276px">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="instrumentoCompra">Instrumento Convocatório:</label>
                </td>
                <td>
                    <input type="text" id="instrumentoCompra" disabled style="width: 276px">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="numeroAnoCompra">Número/Ano da Compra:</label>
                </td>
                <td>
                    <input type="text" id="numeroAnoCompra" disabled style="width: 276px">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="objetoCompra">Objeto da Compra:</label>
                </td>
                <td>
                    <input type="text" id="objetoCompra" disabled style="width: 276px">
                </td>
            </tr>
            <tr>
                <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btnProcessar">
        <i class="fas fa-save"></i>
        Enviar Dados
    </button>
</div>
<div class="subcontainer">
    <fieldset id="ctnTable" style="width: 1500px">
        <legend>Itens</legend>
        <table id="table-itens"
               class="table table-sm"
               data-height="300"
               data-virtual-scroll="true"
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
    window.addEventListener('load', async () => {
        var apiUrl
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        const btnProcessar = document.getElementById('btnProcessar');
        const btnCarregarLicitacao = document.getElementById('btnCarregarLicitacao');
        const btnConsultarCompra = document.getElementById('btnConsultarCompra');
        const cnpj = document.getElementById('cnpj');
        const tabelaItens = jQuery('#table-itens');
        const licitacaoAncora = document.getElementById('licitacao_ancora');
        const licitacaoCodigo = document.getElementById('pn03_liclicita');
        const licitacaoObjeto = document.getElementById('pn03_numero');
        const modalidadeCompra = document.getElementById('modalidadeCompra');
        const instrumentoCompra = document.getElementById('instrumentoCompra');
        const numeroAnoCompra = document.getElementById('numeroAnoCompra');
        const objetoCompra = document.getElementById('objetoCompra');
        const linkCompra = document.getElementById('linkCompra');
        const routes = {
            incluirRespostaItem: `${apiUrl}/patrimonial/pncp/compraEditalAviso/incluirRespostaItem`,
            buscarLicitacao: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarLicitacao`,
            verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`,
        }
        let linkCompraSite = '';
        btnProcessar.disabled = true;

        function verificaIntegracao() {
            const formData = new FormData();
            formData.append('documento', cnpj.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    return alert(response.message);
                }
                if (response.data === null) {
                    alert("Por favor, verifique a configuração de integração com o PNCP!");
                    btnCarregarLicitacao.disabled = true;
                    btnConsultarCompra.disabled = true;
                }
            })
        }
        verificaIntegracao();

        btnConsultarCompra.addEventListener('click', () => {
            if (licitacaoCodigo.value === '') {
                alert('Selecione a Contratação/Edital/Aviso para consultá-la no PNCP.');
                console.log(licitacaoCodigo.value === '', linkCompraSite);
            }
        });

        btnCarregarLicitacao.addEventListener('click', () => {
            const formData = new FormData();
            if (licitacaoCodigo.value === "") {
                alert("Selecione a licitação.");
                return;
            }
            formData.append('licitacao', licitacaoCodigo.value);
            formData.append('resultadoItem', 'true');
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.buscarLicitacao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message)
                    return;
                }
                console.log(response);
                const resultadoItem = response.data.resultadoItem
                modalidadeCompra.value = resultadoItem.modalidadeCompra;
                instrumentoCompra.value = resultadoItem.instrumentoConvocatorio
                numeroAnoCompra.value = resultadoItem.numeroCompra
                objetoCompra.value = resultadoItem.objetoCompra
                btnProcessar.disabled = false;
                tabelaItens.bootstrapTable('load', response.data.itens);
            })
        })

        function limparDados() {
            modalidadeCompra.value = "";
            instrumentoCompra.value = '';
            numeroAnoCompra.value = '';
            objetoCompra.value = '';
            tabelaItens.bootstrapTable("removeAll");
        }

        btnProcessar.addEventListener('click', () => {
            const formData = new FormData();
            let itens = JSON.stringify(tabelaItens.bootstrapTable('getData'));

            formData.append('licitacao', licitacaoCodigo.value);
            formData.append('cnpj', cnpj.value);
            formData.append('itensCompra', itens);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.incluirRespostaItem, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message)
                    return;
                }
                alert('Resultado(s) inserido(s) com sucesso para Contratação/Edital/Aviso!');
                limparDados();
                licitacaoCodigo.value = '';
                btnProcessar.disabled = true;
                linkCompra.removeAttribute('href');
            })
        })

        const comprasLookup = new DBLookUp(licitacaoAncora, licitacaoCodigo, licitacaoObjeto, {
            'sArquivo': 'func_compraspncp.php',
            'sLabel': 'Pesquisa de publicações',
            'sObjetoLookUp': 'db_iframe_compraspncp',
            'camposAdicionais': ['pn03_numero', 'pn03_ano', 'pn03_link'],
        });

        comprasLookup.setCallBack('onClick', retorno => {
            limparDados();
            const anoCompra = retorno[3];
            const numeroCompra = retorno[2];
            linkCompraSite = 'https://pncp.gov.br/app/editais/';
            linkCompraSite += `${cnpj.value}/${anoCompra}/${numeroCompra}`

            jQuery('#linkCompra').attr('href', linkCompraSite);
        })

        const detailFormatter = (index, row) => {
            let dados = formataDadosAnalitico(index, row);
            if (!row.dadosFornecedor) {
                return;
            }
            return detailFormaterTable.createDetail(dados, 'Resultado(s)');
        }

        const formataDadosAnalitico = (value, dadosLinha) => {
            let dadosFornecedor = dadosLinha.dadosFornecedor;
            return [
                [
                    {
                        label: "Fornecedor:",
                        valor: `${dadosFornecedor.numcgm} - ${dadosFornecedor.niFornecedor}`
                    },
                    {
                        label: "Nome:",
                        valor: dadosFornecedor.nomeRazaoSocialFornecedor
                    },
                    {
                        label: "Tipo Pessoa:",
                        valor: dadosFornecedor.tipoPessoaId
                    },
                    {
                        label: "Tipo Empresa:",
                        valor: tipoEmpresa(dadosFornecedor.porteFornecedorId)
                    },
                    {
                        label: "Data Homologacao:",
                        valor: dadosLinha.dataHomologacao
                    },
                    {
                        label: "Valor Unitário:",
                        valor: dadosLinha.valorUnitarioHomologado
                    },
                    {
                        label: "Valor Total:",
                        valor: dadosLinha.valorTotalHomologado
                    },
                ],
            ];
        };

        function tipoEmpresa(tipoEmpresa) {
            switch (tipoEmpresa) {
                case 1:
                    return 'ME';
                    break;
                case 2:
                    return 'EPP';
                    break;
                default:
                    return 'Normal';
            }
        }

        function formatterIndicadorSubContratacao(value, row) {
            return "<select data-numero='" + row.numeroItem + "' class='selectIndicadorSubContratacao'>" +
                "<option value='0' selected>Selecione</option>" +
                "<option value='true'>Sim</option>" +
                "<option value='false'>Não</option>" +
                "</select>"
        }

        jQuery(document).ready(jQuery => {
            window.operateEvents = {
                'change .selectIndicadorSubContratacao': (e, value, row, index) => {
                    row.indicadorSubcontratacao = e.target.value;
                },
            }
            const colunasItens = [
                {
                    field: 'numeroItem',
                    title: 'Número do Item',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'descricao',
                    title: 'Descrição',
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
                    field: 'unidadeMedida',
                    title: 'Unidade de medida',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'valorUnitarioEstimado',
                    title: 'Vlr unit. estimado',
                    halign: 'center',
                    align: 'center',
                    formatter: (value, row) => {
                        return value.replace('.', ',');
                    }
                },
                {
                    field: 'valorTotalEstimado',
                    title: 'Valor Total',
                    halign: 'center',
                    align: 'center',
                    formatter: (value, row) => {
                        return value.replace('.', ',');
                    }
                },
                {
                    field: 'indicadorSubcontratacao',
                    title: 'Indicador sub-contratação',
                    halign: 'center',
                    align: 'center',
                    formatter: formatterIndicadorSubContratacao,
                    events: operateEvents
                },
            ];

            tabelaItens.bootstrapTable({
                locale: 'pt-BR',
                height: 250,
                class: "table table-sm",
                columns: colunasItens,
                showButtonText: true,
                useRowAttrFunc: true,
                reorderableRows: true,
                detailView: true,
                detailFormatter: detailFormatter,
                onPostBody: (data) => {
                    const selectsSubcontratacao = document.getElementsByClassName('selectIndicadorSubContratacao');
                    selectsSubcontratacao.forEach((selectSubcontratacao) => {
                        const numero = selectSubcontratacao.dataset.numero;
                        data.map((item) => {
                            if (item.numeroItem === numero) {
                                selectSubcontratacao.value = item.indicadorSubcontratacao;
                            }
                        });
                    });
                }
            });
        });
    });
</script>
</body>
</html>
