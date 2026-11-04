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
<div class="container" id="divCompras" style="width: 1200px">
    <fieldset>
        <legend>Histórico de Contratações</legend>
        <table id="data-table"
               class="table table-sm">
        </table>
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
<script type="text/javascript">
    $.noConflict();
    window.addEventListener('load', async () => {
        var apiUrl
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });
        const cnpj = document.getElementById('cnpj');
        const btnAtualizar = document.getElementById('btnAtualizar');
        const tabelaCompras = jQuery('#data-table');
        const routes = {
            buscarCompras: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarCompras`,
            excluirCompra: `${apiUrl}/patrimonial/pncp/compraEditalAviso/excluirCompra`,
            verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`,
        }

        function verificaIntegracao() {
            const formData = new FormData();
            formData.append('documento', cnpj.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                if (response.data === null) {
                    btnAtualizar.disabled = true;
                    alert("Por favor, verifique a configuração de integração com o PNCP!");
                } else {
                    buscarCompras();
                }
            })
        }
        verificaIntegracao();

        function buscarCompras() {
            const formData = new FormData;
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.buscarCompras, {body: formData}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                tabelaCompras.bootstrapTable('load', response.data)
            });
        }

        btnAtualizar.addEventListener('click', () => {
            buscarCompras();
        })
        jQuery(document).ready(jQuery => {

            window.operateEvents = {
                'click .excluir': (e, d, data) => {
                    if (!confirm('Confirma a exclusão?' +
                        ' Esta ação excluirá também o evento de publicação criado de forma automática' +
                        ' a partir da inclusão da contratação/edital/aviso.')) {
                        return false;
                    }
                    let formData = new FormData();
                    PHPSession.appendFormData(formData);
                    formData.append('cnpj', cnpj.value)
                    formData.append('codigoCompra', data.pn03_codigo);
                    HttpClient.post(routes.excluirCompra, {body: formData}).then(response => {
                        if (response.error) {
                            alert(response.message)
                        }
                        alert(response.message);
                        buscarCompras();
                        tabelaCompras.bootstrapTable('refresh');
                    })
                }
            };

            const colunas = [
                {
                    field: 'pn03_liclicita',
                    title: 'Licitação',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'pn03_solicita',
                    title: 'Solicitação',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'l20_numero',
                    title: 'Numeração',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'pn03_ano',
                    title: 'Ano Contratação',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'l03_descr',
                    title: 'Descrição da Modalidade',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'pn03_numero',
                    title: 'Codigo PNCP',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'pn02_nome',
                    title: 'Unidade Compradora',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'pn03_datapublicacao',
                    title: 'Data Publicacao',
                    halign: 'center',
                    align: 'center',
                    formatter:(value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
                {
                    field: 'acao',
                    title: 'Ações',
                    halign: 'center',
                    align: 'center',
                    formatter: (valor, data, index) => {
                        return ['<a class="excluir" href="javascript:void(0)" title="Excluir">',
                            '  <i class="fas fa-trash-alt"></i>',
                            '</a>'].join('')
                    },
                    events: window.operateEvents
                }
            ];
            tabelaCompras.bootstrapTable({
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
    });
</script>
</body>
</html>
