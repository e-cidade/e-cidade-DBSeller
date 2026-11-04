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
    <style>
        input:disabled {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <fieldset>
            <legend>Contrato</legend>

            <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
            <input type="hidden" id="valorGlobal" name="valor-global">
            <input type="hidden" id="dadosBuscados" value="0">

            <table class="form-container">
                <tr>
                    <td>
                        <label for="compra" id="ancoraFamilia">
                            Família:
                        </label>
                    </td>
                    <td>
                        <input type="text" name="sequencialFamilia" id="sequencialFamilia" class="field-size2" disabled>
                        <input type="text" name="descricaoFamilia" id="descricaoFamilia" class="field-size6">
                    </td>
                </tr>
            </table>
        </fieldset>
        <button class="btn" id="btnSalvar">
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

        let dadosCompra;

        const ancoraFamilia = document.getElementById('ancoraFamilia');
        const inputFamiliaId = document.getElementById('sequencialFamilia');
        const inputFamiliaDescricao = document.getElementById('descricaoFamilia');
        const botaoSalvar = jQuery('#btnSalvar');

        let rotasApi;

        function getRotasApi(baseUrl) {
            return {
                excluir:
                    baseUrl + '/patrimonial/material/cadastros/familias/excluir/'
            }
        }

        PHPSession.loadData().then(async () => {
            rotasApi = getRotasApi(PHPSession.requestApi);
        });

        function verificaCamposObrigatorios() {
            if (inputEstaVazio('sequencialFamilia')) {
                alert('Selecione a família.');
                return false;
            }

            return true;
        }

        function inputEstaVazio(id) {
            let valorInput = jQuery(`#${id}`).val();

            return (
                valorInput === '' || valorInput === undefined ||
                valorInput === false || valorInput === 0 ||
                valorInput === null
            );
        }

        function montarCamposEnvio() {
            const formData = new FormData;
            formData.append('sequencial', jQuery('#sequencialFamilia').val());

            return formData;
        }

        botaoSalvar.on('click', function () {
            if (!verificaCamposObrigatorios()) {
                return;
            }

            const formData = montarCamposEnvio();
            HttpClient
                .post(rotasApi.excluir, { body: formData })
                .then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    alert(response.message);
                    limparFormulario();
                })
                .catch(e => {
                    return alert(e.message);
                });
        });

        function limparFormulario() {
            inputFamiliaId.value = '';
            inputFamiliaDescricao.value = '';
        }

        // Lookups
        const familiaLookup = new DBLookUp(ancoraFamilia, inputFamiliaId, inputFamiliaId, {
            'arquivo': 'func_matfamilias.php',
            'label': 'Pesquisa de compras',
            'objetoLookUp': 'db_iframe_matfamilias',
            'camposAdicionais': ['m105_sequencial', 'm105_descricao']
        });

        familiaLookup.setCallBack('onClick', async retorno => {
            jQuery('#sequencialFamilia').val(retorno[2]);
            jQuery('#descricaoFamilia').val(retorno[3]);
        });
    </script>
</body>

</html>