<?php

/**
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

/**
 * Representa as configurações da tela de geração do empenho.
 *
 * @author $Author: dbmarcos $
 * @version $Revision: 1.6 $
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));


?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/ProgressBar.widget.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
    <script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
    <!-- requires bootstrap table -->
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="assets/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.css"
    >
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script src="assets/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
    <style>
        th{
            color:white;
        }

        #toolbar {
            text-align: right;
            display: flex;
            margin-left: 0.5em;
        }
    </style>
</head>
<body>
    <table
        id="table"
        data-toolbar="#toolbar"
        data-search="true"
        data-show-columns="true"
    ></table>
</body>
<script>
    const bootstrapTable = $('#table').bootstrapTable({
        pagination:true,
        sortable:true,
        exportDataType:'all',
        showExport:true,
        filterControl:true,
        exportTypes:['xml','csv', 'txt', 'excel', 'xlsx', 'pdf'],
        height:800,
        columns: [
            {
                field: 'numero_atendimento',
                title: 'N:. ATEND.',
                sortable: true
            }, {
                field: 'data_atendimento',
                title: 'DATA ATEND.',
                sortable: true,
                formatter: dataPtBr
            }, {
                field: 'matricula',
                title: 'MATRICULA',
                sortable: true
            },
            {
                field: 'cgm',
                title: 'CGM',
                sortable: true
            },
            {
                field: 'cpf',
                title: 'CPF',
                sortable: true
            },
            {
                field: 'nome',
                title: 'NOME',
                sortable: true,
            },
            {
                field: 'nome_instituicao',
                title: 'INSTITUIÇÃO',
                sortable: true,
                filterControl:'select'
            },
            {
                field: 'codigo_lotacao',
                title: 'CÓD. LOTAÇÃO',
                sortable: true
            },
            {
                field: 'descricao_lotacao',
                title: 'LOTAÇÃO',
                sortable: true
            },
            {
                field: 'status',
                title: 'STATUS',
                filterControl:'select'
            },
        ],
    });

    const RPC = 'rh4_recadastramento.RPC.php';
    const TableBody = document.getElementById("tabela_atendimento");

    function dataPtBr(date) {
        if (
            typeof date !== 'string' || 
            date === null || 
            date === '' || 
            date === 'undefined'
        ) {
            return date;
        }

        const splitsDate = date.split("-");

        if (typeof splitsDate !== 'object' || splitsDate.length !== 3) {
            return data;
        }

        return `${splitsDate[2]}/${splitsDate[1]}/${splitsDate[0]}`;
    }

    function filterDates(startDate, stopDate) {
        for(var arr=[],dt=new Date(startDate); dt<=stopDate; dt.setDate(dt.getDate()+1)){
            arr.push(new Date(dt));
        }
        const rangeDates =  arr.map((v)=>v.toISOString().slice(0,10))
        return rangeDates;
    }


    function getDados() {
        const form = {
            route: 'dados'
        };
        const oAjaxRequest = new AjaxRequest(RPC, form,
            function (resp, erro) {
                bootstrapTable.bootstrapTable("load", resp.data);
            });
        oAjaxRequest.execute();
    }

    function main() {
        getDados();

        setTimeout(() => {
            $(".columns.columns-right.btn-group.float-right").append(`
                <button 
                    type="button" 
                    class="btn btn-secondary" 
                    id="buttonRefresh" 
                >
                    <i class="fas fa-sync-alt"></i>
                </button>
            `);

            document.getElementById('buttonRefresh').addEventListener("click",function(){
               getDados();
            });
        }, 300);
    }

    main();
</script>
</html>
