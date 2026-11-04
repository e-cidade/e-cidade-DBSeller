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
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <style>
        select:disabled,
        input:disabled {
            color: dimgrey;
        }
    </style>
</head>

<body>
    <div class="container">
        <fieldset>
            <legend>Contratação</legend>
            <table class="form-container">
                <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
                <tr>
                    <td>
                        <label for="compra" id="ancoraCompra">
                            Numero (ID PNCP):
                        </label>
                    </td>
                    <td>
                        <input type="text" name="numero-compra" id="numeroCompra" class="field-size4">

                        <label for="ano-compra">Ano:</label>
                        <input type="text" name="ano-compra" id="anoCompra" class="field-size3">

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
                        <label for="compra">
                            Origem:
                        </label>
                    </td>
                    <td>
                        <select id="origemContratacao" class="field-size9">
                            <option value="0" selected>Selecione</option>
                            <option value="1">Licitação</option>
                            <option value="2">Solicitação</option>
                        </select>
                    </td>
                </tr>

                <tr id="rowLicitacao">
                    <td>
                        <label for="l20_codigo" id="ancoraLicitacao">
                            Licitação:
                        </label>
                    </td>
                    <td>
                        <input type="text" id="l20_codigo" style="width:100%" />
                    </td>
                </tr>

                <tr id="rowSolicitacao" style="display:none">
                    <td>
                        <label for="pc10_numero" id="ancoraSolicitacao">
                            Solicitação:
                        </label>
                    </td>
                    <td>
                        <input type="text" id="pc10_numero" style="width:100%" />
                    </td>
                </tr>
            </table>
        </fieldset>
        <button type="button" id="btnSalvar">
            <i class="fas fa-save"></i>
            Enviar Dados
        </button>
    </div>
    <script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script rel="script" type="text/javascript" src="scripts/session.js"></script>
    <script>
        $.noConflict();

        const selectOrigemContratacao = document.getElementById('origemContratacao');
        const ancoraLicitacao = document.getElementById('ancoraLicitacao');
        const inputLicitacaoId = document.getElementById('l20_codigo');
        const ancoraSolicitacao = document.getElementById('ancoraSolicitacao');
        const inputSolicitacaoId = document.getElementById('pc10_numero');
        const botaoConsultaCompra = jQuery('#linkCompra');
        const botaoSalvarCompra = jQuery('#btnSalvar');
        const numero = jQuery('#numeroCompra');
        const cnpj = jQuery('#cnpj').val();
        const ano = jQuery('#anoCompra');

        let rotasApi;

        function getRotasApi(baseUrl) {
            return {
                importarCompra:
                    baseUrl + '/patrimonial/pncp/compraEditalAviso/importarCompraEditalAviso',
            }
        }

        function montarLinkCompra(ano, numero) {
            if (validarCamposEnvio() === false) {
                botaoConsultaCompra.removeAttr('href');
                return alert('Informe o número e ano da compra para consultá-la.')
            }

            let linkCompraSite = 'https://pncp.gov.br/app/editais/';
            linkCompraSite += `${cnpj}/${ano.val()}/${numero.val()}`

            botaoConsultaCompra.attr('href', linkCompraSite);
        }

        function validarCamposEnvio() {
            if (Number(ano.val()) <= 0 || isNaN(Number(ano.val()))) {
                return false;
            }

            if (Number(numero.val()) <= 0 || isNaN(Number(numero.val()))) {
                return false;
            }

            return true;
        }

        function salvarCompra() {

            if (!validarCamposEnvio()) {
                return alert('Informe o número e ano da compra.');
            }

            formData = new FormData();
            formData.append('cnpj', cnpj);
            formData.append('ano', ano.val());
            formData.append('numero', numero.val());

            if (selectOrigemContratacao.value === '1' && inputLicitacaoId.value !== '') {
                formData.append('licitacao', inputLicitacaoId.value)
            }

            if (selectOrigemContratacao.value === '2' && inputSolicitacaoId.value !== '') {
                formData.append('solicitacao', inputSolicitacaoId.value)
            }

            PHPSession.appendFormData(formData);
            HttpClient
                .post(rotasApi.importarCompra, { body: formData })
                .then(function (response) {
                    limparCampos()
                    if (response.message) {
                        return alert(response.message)
                    }

                });
        }

        function limparCampos() {
            numero.val('');
            ano.val('');
            inputLicitacaoId.value = '';
            inputSolicitacaoId.value = '';
            selectOrigemContratacao.value = '0';
        }

        const comprasLookup = new DBLookUp(ancoraLicitacao, inputLicitacaoId, inputLicitacaoId, {
            'arquivo': 'func_liclicita.php',
            'label': 'Pesquisa de Licitações',
            'objetoLookUp': 'db_iframe_licitacoes',
        });

        const solicitacaoLookup = new DBLookUp(ancoraSolicitacao, inputSolicitacaoId, inputSolicitacaoId, {
            'arquivo': 'func_solicita.php',
            'label': 'Pesquisa de Solicitações',
            'objetoLookUp': 'db_iframe_solicitacoes',
            'parametrosAdicionais': ['passar=true']
        });

        botaoConsultaCompra.on('click', () => montarLinkCompra(ano, numero));
        botaoSalvarCompra.on('click', () => salvarCompra());

        selectOrigemContratacao.on('change', function () {
            inputLicitacaoId.value = '';
            inputSolicitacaoId.value = '';

            if (this.value === '2') {
                jQuery('#rowLicitacao').hide();
                jQuery('#rowSolicitacao').show();
            } else {
                jQuery('#rowLicitacao').show();
                jQuery('#rowSolicitacao').hide();
            }
        });

        PHPSession.loadData().then(async () => {
            rotasApi = getRotasApi(PHPSession.requestApi);
        });

    </script>
</body>
