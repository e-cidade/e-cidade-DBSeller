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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_utils.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
require_once modification("libs/db_app.utils.php");

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<div class="container">
    <input type="hidden" name="codigo_material" id="codigo_material" value="<?= $codmater ?>">
    <div>
        <fieldset id="ctnTable">
            <legend>Estoque</legend>
            <div style="width: 1000px">
                <table id="data-table"
                       class="table table-sm"
                       data-height="320"
                       data-virtual-scroll="true"
                       data-detail-view="true"
                       style="width: 100%;">
                </table>
            </div>
        </fieldset>
    </div>
</div>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>

<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<script type="text/javascript">
    $.noConflict();
    jQuery(document).ready(function ($) {
        const codigoMaterial = document.getElementById('codigo_material').value;
        var table = $('#data-table');

        const formatarDescricao = (value, row, index) => {
            return `${row.codigo_deposito} - ${row.descricao_deposito}`;
        }
        const formatarValor = (value, row, index) => {
            return `R$ ${value}`;
        }

        const formatarTransferencias = (value, row, index) => {
            let totalTransferido = 0;
            value.map((transferencia) => {
                totalTransferido += parseInt(transferencia.quantidade_transferida);
            });

            return totalTransferido;
        }

        const colunas = [
            {
                title: 'Depósito',
                field: 'descricao_deposito',
                align: 'left',
                valign: 'center',
                formatter: formatarDescricao
                // sortable: true
            }, {
                title: 'Último Preço Médio',
                field: 'preco_medio',
                align: 'left',
                valign: 'center',
                // sortable: true,
                width: 120,
                formatter: formatarValor
            }, {
                title: 'Quantidade em estoque',
                field: 'quantidade_total',
                align: 'center',
                valign: 'center',
                width: 30,
                // sortable: true
            }, {
                title: 'Valor em estoque',
                field: 'valor_estoque',
                align: 'left',
                valign: 'center',
                width: 110,
                // sortable: true,
                formatter: formatarValor
            }, {
                title: 'Transferências',
                field: 'transferencias',
                align: 'center',
                valign: 'center',
                width: 30,
                // sortable: true,
                formatter: formatarTransferencias
            }, {
                title: 'Saldo disponível',
                field: 'quantidade_disponivel',
                align: 'center',
                valign: 'center',
                width: 30,
                // sortable: true
            }
        ];

        const detailFormatter = (index, row) => {
            let dados = formataDadosAnalitico(row);
            return detailFormaterTable.createDetail(dados, 'Transferências: ');
        }

        const formData = new FormData();
        formData.append('acao', 'buscarEstoques');
        formData.append('codigo_material', codigoMaterial);
        HttpClient.post('mat1_material.RPC.php', {body: formData}).then((response) => {
            const estoques = response.estoques;

            // table.bootstrapTable('destroy');
            table.bootstrapTable({
                columns: colunas,
                data: estoques,
                detailFormatter: detailFormatter,
                uniqueId: "codigo_deposito",
                locale: 'pt-BR',
                cache: false,
                // pagination: true,
                // pageSize: 10,
                // pageList: [10, 25, 50, 100, 200, 'All'],
                // showButtonText: true,
                search: true,
                class: "table table-sm"
            });
        });

        const formataDadosAnalitico = (dadosLinha) => {
            return dadosLinha.transferencias.map((transferencia) => {
                return [
                    {
                        label: "Código:",
                        valor: `${transferencia.codigo}`
                    },
                    {
                        label: "Depósito destino:",
                        valor: `${transferencia.codigo_deposito_destino} - ${transferencia.descricao_deposito_destino}`
                    },
                    {
                        label: "Quantidade:",
                        valor: `${transferencia.quantidade_transferida}`
                    },
                ];
            });
        };
    });
</script>
</body>
</html>
