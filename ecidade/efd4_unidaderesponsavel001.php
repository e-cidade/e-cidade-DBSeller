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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<!DOCTYPE html>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <script src="scripts/scripts.js"></script>
    <script src="scripts/strings.js"></script>
    <script src="scripts/prototype.js"></script>
    <script src="scripts/AjaxRequest.js"></script>
    <script src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet">
    <link href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

</head>

<body class="body-default">
    <div class="container">
        <form id="unidadeRespForm">
            <fieldset>
                <legend>Dados da Unidade</legend>
                <table class="form-container">
                    <tr>
                        <td class="bold">
                            <label for="z01_numcgm">
                                <a href="javascript:;" id="ancoraCgm">Cgm: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="z01_numcgm" size="4">
                            <input type="text" id="z01_nome" disabled>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <button>Incluir</button>
        </form>
        <hr/>
        <div>
            <fieldset>
                <table id="data-table"
                       class="table table-sm"
                       data-height="250"
                       data-virtual-scroll="true"
                       style="width: 100%;">
                </table>
            </fieldset>
        </div>
    </div>
    <script src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script src="scripts/classes/http/http.js"></script>
    <script src="scripts/session.js"></script>
</body>

</html>
<script>
    $.noConflict();

    // Elementos
    const ancoraCgm       = document.querySelector('#ancoraCgm');
    const inputCgm        = document.querySelector('#z01_numcgm');
    const inputCgmDescr   = document.querySelector('#z01_nome');
    const table           = jQuery('#data-table');
    const unidadeRespForm = document.querySelector('#unidadeRespForm');
    let urlIntegracao   = '';

    PHPSession.loadData().then(async () => {
        urlIntegracao = PHPSession.requestApi + '/integracoes/efd-reinf/unidaderesponsavel';
        await getUnidadesResp();
    });

    unidadeRespForm.addEventListener('submit', saveUnidadeResp);

    // Lookups
    const cgmLoockup = new DBLookUp(ancoraCgm, inputCgm, inputCgmDescr, {
        'sArquivo': 'func_cgmjuridico.php'
    });

    // busca as unidades responsaveis cadastrada e monta grid
    async function getUnidadesResp() {
        const url = urlIntegracao + '/get';

        const formData = new FormData;
        PHPSession.appendFormData(formData);

        const options = {
            body: formData,
            reportMessage: 'Buscando Unidades...',
        }

        let response = await HttpClient.post(url, options);

        if (response.error == true) {
            let msg = 'Não foi possível buscar as unidades: \n';
            msg += response.message;

            alert(msg);
            return;
        }

        buildGrid(response.data);
    }

    // funcao para incluir uma unidade
    async function saveUnidadeResp(event) {
        event.preventDefault();

        if (!validadeForm()) {
            return;
        }

        const url = urlIntegracao + '/save';
        const formData = new FormData;

        formData.append('cgm', inputCgm.value);
        PHPSession.appendFormData(formData);

        const options = {
            body: formData,
            reportMessage: 'Incluindo Unidade...'
        }

        let response = await HttpClient.post(url, options);

        if (response.error == true) {
            let msg = 'Não foi possível incluir a unidade: \n';
            msg += response.message;

            alert(msg);
            return;
        }

        getUnidadesResp();
        unidadeRespForm.reset();
        alert(response.message);
    }

    function validadeForm() {
        if (!inputCgm.value) {
            alert('Você deve informar o cgm');
            return false;
        }

        return true;
    }

    // funcao para excluir uma unidade
    async function deleteUnidadeResp(el) {
        const confirmation = confirm('Tem certeza que deseja excluir esta unidade?');
        if (!confirmation) {
            return;
        }

        const url = urlIntegracao + '/delete';
        const formData = new FormData;
        const unidadeResponsavelId = el.dataset.id;

        formData.append('id', unidadeResponsavelId);
        PHPSession.appendFormData(formData);

        const options = {
            body: formData,
            reportMessage: 'Deletando Unidade...'
        }

        let response = await HttpClient.post(url, options);

        if (response.error == true) {
            let msg = 'Não foi possível deletar a unidade: \n';
            msg += response.message;

            alert(msg);
            return;
        }

        unidadeRespForm.reset();
        getUnidadesResp();
        alert(response.message);
    }

    // funcao para montar a grid com os dados da unidades
    function buildGrid(data = null) {

        const callbackAcoes = (value, row, index, field) => {
            const btnExcluir = `
                <button data-id='${row.id}' onclick='deleteUnidadeResp(this)'>
                    <i class='fas fa-trash'></i> Excluir
                </button>`;
            return btnExcluir;
        }

        const cnpjFormatter = (value, row, index, field) => {
            value = js_formatar(value, 'cpfcnpj', 0);
            let base   = '<strong>' + value.substr(0, 10) + '</strong>';
            let filial = value.substr(10);
            let cnpj   = base + filial
            return cnpj;
        }

        const columns = [
            {
                title: 'Cgm',
                field: 'cgm',
                align: 'center'
            },
            {
                title: 'Descrição',
                field: 'descricao',
                align: 'center'
            },
            {
                title: 'CNPJ',
                field: 'cnpj',
                align: 'center',
                formatter: cnpjFormatter
            },
            {
                field: 'acoes',
                title: 'Ações',
                align: 'center',
                formatter: callbackAcoes
            }
        ];

        table.bootstrapTable('destroy');
        table.bootstrapTable({
            data: data,
            locale: 'pt-BR',
            columns: columns,
            search: true
        })
    }
</script>
