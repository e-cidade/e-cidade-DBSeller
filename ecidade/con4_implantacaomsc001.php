<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Importe a planilha da Matriz de Saldos Contábeis (MSC) no formato CSV.<br>
</div>
<div class="container">
    <form id="form-upload" method="post" action="" enctype="multipart/form-data">
        <fieldset>
            <legend>Clique em <kbd>Arquivo</kbd>, selecione a planilha para importar</legend>
            <div class="text-left" style="margin-top: 5px; margin-bottom: 5px; display: flex">
                <div style="width: 85px;">
                    <label for="exercicio" class="bold">Competência :</label>
                </div>
                <div>
                    <input id="exercicio" name="exercicio" type="text" class="field-size1" maxlength="4"
                           oninput="js_ValidaCampos(this,1,'Ano','t','f',event);"> /
                    <input id="mes" name="mes" type="text" class="field-size1" maxlength="2"
                           oninput="js_ValidaCampos(this,1,'Mês','t','f',event);">
                </div>
            </div>
            <div id="ctnImportacao"></div>
        </fieldset>
        <button type="button" class="btn btn-light" id="importar" disabled>
            <i class="fa fa-save" aria-hidden="true"></i>
            Salvar
        </button>
    </form>
</div>

<?php db_menu() ?>

<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/widgets/DBFileUpload.widget.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script>

    const btnImportar = document.getElementById('importar');
    const inputExercicio = document.getElementById('exercicio');
    const inputMes = document.getElementById('mes');

    const routs = {
        importar: 'financeiro/contabilidade/msc/importar'
    }

    const erroRetornoArquivo = mensagem => {
        alert(mensagem);
        btnImportar.disabled = true;
        fileUpload.clear();
    }

    function retornoEnvioArquivo(retorno) {
        if (retorno.error) {
            erroRetornoArquivo(retorno.error);
            return false;
        }

        if (retorno.extension.toLowerCase() != 'csv') {
            erroRetornoArquivo('Arquivo inválido! O arquivo deve ser uma planilha em "csv".');
            return false;
        }

        btnImportar.disabled = false;
    }

    const fileUpload = new DBFileUpload({callBack: retornoEnvioArquivo, labelButton: 'Arquivo'});
    fileUpload.show($('ctnImportacao'));
    document.querySelector(".inputUploadFile").addClassName('field-size8');

    PHPSession.loadData().then(() => {

        inputExercicio.value = PHPSession.getValueSession('DB_anousu');

        btnImportar.addEventListener('click', () => {
            console.log(Number(inputExercicio.value));

            if (empty(inputExercicio.value)) {
                alert('Informe o exercício');
                return;
            }

            if (empty(inputMes.value)) {
                alert('Informe o mês.');
                return;
            }

            const formData = new FormData();
            formData.append('exercicio', inputExercicio.value);
            formData.append('mes', inputMes.value);
            formData.append('file', JSON.stringify({
                "extension": fileUpload.extension,
                "name": fileUpload.file,
                "path": fileUpload.filePath
            }));

            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.importar}`, {body: formData}).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                if (!response.data) {
                    location.reload();
                }
            });
        });
    });

</script>
