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
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body>
<div class="container" style="width: 70%">
    <fieldset>
        <legend>Contratos</legend>
        <table id="data-table" class="table table-sm"></table>
        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
    </fieldset>

    <button type="button" id="btnAtualizar">
        <i class="fas fa-sync-alt"></i>
        Atualizar
    </button>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<script>
    $.noConflict();
    let rotasApi;
    const cnpj = jQuery('#cnpj').val();
    const botaoAtualizar = jQuery('#btnAtualizar');
    const tabelaContratos = jQuery('#data-table');

    function getRotasApi(baseUrl) {
        return {
            verificaIntegracao:
                baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
            buscarContratos:
                baseUrl + '/patrimonial/pncp/contratos/buscarContratos',
            excluirContrato:
                baseUrl + '/patrimonial/pncp/contratos/excluirContrato'
        }
    }

    function buscarContratos() {
        const formData = new FormData;
        PHPSession.appendFormData(formData);

        HttpClient.post(rotasApi.buscarContratos, {body: formData})
            .then(response => {
                if (response.erro) {
                    return alert(response.mensagem);
                }

                tabelaContratos.bootstrapTable('load', response.data)
            });
    }

    async function verificaIntegracaoAtiva() {
        const formData = new FormData();

        formData.append('documento', cnpj);
        PHPSession.appendFormData(formData);

        let integracaoAtiva = false;

        await HttpClient
            .post(rotasApi.verificaIntegracao, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                if (response.data !== null) {
                    integracaoAtiva = true;
                } else {
                    return alert('Por favor, verifique a configuração de integração com o PNCP!');
                }
            });

        return integracaoAtiva;
    }

    PHPSession.loadData().then(async () => {
        rotasApi = getRotasApi(PHPSession.requestApi);

        const integracaoAtiva = await verificaIntegracaoAtiva();
        if (!integracaoAtiva) {
            return;
        }

        buscarContratos();
    });

    jQuery(() => {
        window.operateEvents = {
            'click #excluir': (e, d, data) => {
                let mensagemExclusao = 'Confirma a exclusão? '
                mensagemExclusao += 'Esta ação excluirá também o evento de publicação criado de '
                mensagemExclusao += 'forma automática a partir da inclusão do contrato, '
                mensagemExclusao += 'bem como documentos anexados ao mesmo.'

                const exclusaoConfirmada = confirm(mensagemExclusao)

                if (!exclusaoConfirmada) {
                    return;
                }

                const formData = new FormData();
                PHPSession.appendFormData(formData);

                formData.append('cnpj', cnpj)
                formData.append('pn04_codigo', data.pn04_codigo);

                HttpClient.post(rotasApi.excluirContrato, {body: formData})
                    .then(response => {
                        if (response.error) {
                            alert(response.message)
                        }

                        alert(response.message);

                        buscarContratos();
                        tabelaContratos.bootstrapTable('refresh');
                    })
                    .catch(response => alert(response.message));
            }
        };

        let colunas = [
            {
                field: 'pn04_acordo',
                title: 'Acordo',
                halign: 'center',
            },
            {
                field: 'acordo.ac16_numero',
                title: 'Número',
                halign: 'center',
            },
            {
                field: 'pn04_ano',
                title: 'Ano',
                halign: 'center',
            },
            {
                field: 'pn04_numero',
                title: 'Código PNCP',
                halign: 'center',
            },
            {
                field: 'unidade_compradora.pn02_nome',
                title: 'Unidade Compradora',
                halign: 'center',
            },
            {
                field: 'pn04_datapublicacao',
                title: 'Data Publicação',
                halign: 'center',
                formatter: (value) => {
                    const data = new Date(value);
                    return data.toLocaleDateString();
                }
            },
            {
                field: 'acao',
                title: 'Ações',
                halign: 'center',
                width: '10%',
                formatter: () => {
                    return '<a id="excluir" title="Excluir"><i class="fas fa-trash-alt"></i></a>';
                },
                events: window.operateEvents
            }
        ]

        tabelaContratos.bootstrapTable({
            locale: 'pt-BR',
            height: 500,
            search: false,
            class: "table table-sm",
            columns: colunas,
            showButtonText: true,
            cache: false,
            useRowAttrFunc: true,
            reorderableRows: true,
        });
    });

    botaoAtualizar.on('click', () => {
        buscarContratos();
    })
</script>
</body>
</html>
