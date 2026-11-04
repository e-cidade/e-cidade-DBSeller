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
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <style>
        select:disabled,input:disabled {
            color: dimgrey;
        }
    </style>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Unidades PNCP</legend>
            <tr>
                <input type="hidden" id="unidade_documento" value="<?= $instituicao->getCNPJ();?>">
            </tr>
            <div class="subcontainer" id="div_unidades" style="width: 900px;">
                <fieldset>
                    <table id="data-table" class="table table-sm">
                    </table>
                </fieldset>
            </div>
    </fieldset>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    $.noConflict();

    window.addEventListener('load', async () => {
        var apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });

        const unidadeDocumento = document.getElementById("unidade_documento");
        const formData = new FormData();
        const tabelaUnidades = jQuery('#data-table');
        const routes = {
            buscarUnidades: `${apiUrl}/patrimonial/pncp/unidades/buscarUnidades/`,
            verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`,
        }
        function verificaIntegracao() {
            const formData = new FormData();
            formData.append('documento', unidadeDocumento.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                if (response.data === null) {
                    alert("Por favor, verifique a configuração de integração com o PNCP!");
                } else {
                    buscarUnidades();
                }
            })
        }
        verificaIntegracao();

        function buscarUnidades() {
            formData.append('documento', unidadeDocumento.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(routes.buscarUnidades, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.error)
                }
                tabelaUnidades.bootstrapTable('load', response.data);
            })
        }

        jQuery(document).ready(jQuery => {
            window.operateEvents = {
            };
            const colunas = [
                {
                    field: 'ativo',
                    title: 'Ativo',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        if (value) {
                            return `<i class="fas fa-check-circle fa-lg" style="color: #449d44"></i>`;
                        }
                        return `<i class="fas fa-times-circle fa-lg" style="color: #c9302c"></i>`;
                    },
                },
                {
                    field: 'codigoOrgao',
                    title: 'Código Órgão',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'orgao',
                    title: 'Órgão',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'codigoUnidade',
                    title: 'Código Unidade',
                    halign: 'center',
                    align: 'center',
                    sortOrder: 'desc'
                },
                {
                    field: 'nomeUnidade',
                    title: 'Nome da Unidade',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'data',
                    title: 'Data de Inclusão',
                    halign: 'center',
                    align: 'center',
                    formatter:(value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
            ];

            tabelaUnidades.bootstrapTable({
                locale: 'pt-BR',
                height: 500,
                search: false,
                class: "table table-sm",
                columns: colunas,
                showButtonText: false,
                detailView: false,
                cache: false,
                useRowAttrFunc: true,
                reorderableRows: true,
            });
        });
    });
</script>
</body>
