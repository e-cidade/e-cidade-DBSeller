<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="estilos/DBtab.style.css">
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
</head>

<body>
    <!-- abas -->
    <div id="abas"></div>

    <!-- Exportar dados -->
    <div id="abaExportacao" class="container">
        <fieldset>
            <legend>Dados</legend>
            <form id="form_exportar">
                <table class="form-container">
                    <tr>
                        <td>
                            <b><a href="#" title="l20_codigo" class="licitacao_link">Licitacao: </a></b>
                        </td>
                        <td>
                            <input id="licitacao_codigo_export" title="l20_codigo" name="licitacao_codigo_export"
                                type="text" size="10" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Separador de Colunas: </td>
                        <td>
                            <input type="text" value="|" size="2" style="text-align: center" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="casas_decimais">Casas decimais: </label>
                        </td>
                        <td>
                            <select name="casas_decimais" id="casas_decimais">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="layout_export">Layout: </label>
                        </td>
                        <td>
                            <select name="layout_export" id="layout_export" disabled>
                                <option value="comprasbr">Compras BR</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <button type="submit">Processar</button>
            </form>
        </fieldset>
    </div>

    <!-- Importar dados -->
    <div id="abaImportacao" class="container">
        <fieldset>
            <legend>Dados</legend>
            <form id="form_importar">
                <table class="form-container">
                    <tr>
                        <td>
                            <b><a href="#" title="l20_codigo" class="licitacao_link">Licitacao: </a></b>
                        </td>
                        <td>
                            <input id="licitacao_codigo_reprocessar" title="l20_codigo" name="licitacao_codigo_import"
                                type="text" size="10" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="comprasbr_import_file">Arquivo: </label></td>
                        <td>
                            <input type="file" name="comprasbr_import_file" id="comprasbr_import_file">
                        </td>
                    </tr>
                </table>
                <br>
                <button type="submit">Importar</button>
            </form>
        </fieldset>
    </div>

    <div id="abaReprocessamento" class="container">
        <fieldset>
            <legend>Dados</legend>
            <form id="form_reprocessar">
                <table class="form-container">
                    <tr>
                        <td>
                            <b><a href="#" title="l20_codigo" class="licitacao_link">Licitacao: </a></b>
                        </td>
                        <td>
                            <input id="licitacao_codigo_import" title="l20_codigo" name="licitacao_codigo_import"
                                type="text" size="10" disabled>
                        </td>
                    </tr>
                </table>
                <br>
                <button type="submit">Reprocessar</button>
            </form>
        </fieldset>
    </div>

    <script>
        // globais
        const url = "<?= ECIDADE_REQUEST_PATH ?>";

        // janelas aux
        const dowloadWindow = new windowAux('download-window', 'Download', 300, 300);

        // define abas
        const oDBAba = new DBAbas($('abas'));
        const oAbaImportacao = oDBAba.adicionarAba("Exportar Dados", $('abaExportacao'));
        const oAbaExportacao = oDBAba.adicionarAba("Importar Dados", $('abaImportacao'));
        const oAbaReprocessamento = oDBAba.adicionarAba("Reprocessar", $('abaReprocessamento'));

        // fields
        const casas_decimais = document.querySelector('#casas_decimais');

        /**
         *  Licitacao funcoes de pesquisa
         **/
        const licitacao_link = document.querySelectorAll('.licitacao_link');
        const licitacao_codigo_export = document.querySelector('#licitacao_codigo_export');
        const licitacao_codigo_import = document.querySelector('#licitacao_codigo_import');
        const licitacao_codigo_reprocessar = document.querySelector('#licitacao_codigo_reprocessar');

        function licitacaoFunc(codigo) {
            licitacao_codigo_export.value = codigo;
            licitacao_codigo_import.value = codigo;
            licitacao_codigo_reprocessar.value = codigo;

            db_iframe_liclicita.hide();
        }

        licitacao_link.forEach(i => {
            i.addEventListener('click', (e) => {
                let option = e.target.dataset.option;

                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_liclicita',
                    'func_liclicita.php?sTipoCompra=10,5,4&funcao_js=parent.licitacaoFunc|l20_codigo',
                    'Pesquisa',
                    true
                );
            });
        });

        const form_reprocessar = document.querySelector('#form_reprocessar');
        form_reprocessar.addEventListener('submit', sendFormReprocessar);

        function validate_reprocessar() {
            if (licitacao_codigo_reprocessar.value == null || licitacao_codigo_reprocessar.value == '') {
                return false;
            }

            return true;
        }

        async function sendFormReprocessar(event) {
            event.preventDefault();
            if (!validate_reprocessar()) {
                return alert('Informe o código da licitação.');;
            }

            let action = url + 'v4/api/patrimonial/licitacoes/integracaocomprasbr/reprocessar';

            const formData = new FormData;
            formData.append('licitacao', licitacao_codigo_reprocessar.value);

            try {
                let response = await HttpClient.post(action, { body: formData });
                let page = false;

                if (response.error == true) {
                    alert ('Houve um erro ao reprocessar: \n\n' + response.message);
                } else {
                    alert('Reprocessamento concluído, importe os dados novamente na aba de importação.')
                }

            } catch (error) {
                alert('Houve um erro ao reprocessar.');
            }
        }

        /**
         * Exportacao
         * */
        const form_exportar = document.querySelector('#form_exportar');
        form_exportar.addEventListener('submit', sendFormExport);

        function validate_export() {
            if (licitacao_codigo_export.value == null || licitacao_codigo_export.value == '') {
                alert('Você deve informar a licitação');
                return false;
            }

            return true;
        }

        async function sendFormExport(event) {
            event.preventDefault();
            if (!validate_export()) {
                return;
            }

            let action = url + 'v4/api/patrimonial/licitacoes/integracaocomprasbr/exportar';

            const formData = new FormData;
            formData.append('licitacao', licitacao_codigo_export.value);
            formData.append('casas_decimais', casas_decimais.value);

            try {
                let response = await HttpClient.post(action, {
                    body: formData,
                    reportMessage: 'Gerando arquivo...'
                });
                let page = false;

                if (response.error == true || response.path == undefined) {
                    let msg = 'Algo de errado aconteceu na geração do arquivo: \n\n' + response.message;
                    alert(msg);
                    return false;
                }

                downloadFile(response.path);
            } catch (error) {
                console.log(error);
                alert('Algo de errado aconteceu na geração do arquivo.');
            }
        }

        function downloadFile(link) {
            let a = document.createElement('a');
            a.href = link;
            a.setAttribute('download', '');
            a.click();
        }

        /**
         * Importacao
         * */
        const form_importar = document.querySelector('#form_importar');
        form_importar.addEventListener('submit', sendFormImport);

        const import_file = document.querySelector('#comprasbr_import_file');

        function validate_import() {
            let file = import_file.files;
            let extensions = /(\.csv|\.txt)$/i;

            if (licitacao_codigo_import.value == null || licitacao_codigo_import.value == '') {
                alert('Você deve informar a licitação');
                return false;
            }

            if (file.length == 0) {
                alert('Você deve enviar um arquivo');
                return false;
            }

            let filename = file[0].name;
            if (!filename.match(extensions)) {
                alert('Formato de arquivo não suportado');
                return;
            }

            return true;
        }

        async function sendFormImport() {
            event.preventDefault();
            if (!validate_import()) {
                return;
            }

            const action = url + 'v4/api/patrimonial/licitacoes/integracaocomprasbr/importar';

            const formData = new FormData;
            formData.append('importFile', import_file.files[0]);
            formData.append('licitacao', licitacao_codigo_import.value);

            const options = {
                reportMessage: 'Importando arquivo...'
            }
            options.body = formData;

            try {
                let response = await HttpClient.post(action, options);

                if (response.error == true) {
                    let msg = 'Algo de errado aconteceu na importacao do arquivo: \n\n' + response.message;
                    alert(msg);
                    return false;
                }

                alert('Arquivo importado com sucesso.')
                return true;

            } catch (error) {
                console.log(error);
                alert('Algo de errado aconteceu na importacao do arquivo.');
            }
        }
    </script>
</body>

</html>
