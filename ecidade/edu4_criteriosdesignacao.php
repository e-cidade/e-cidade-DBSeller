<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
          name="viewport">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css" />
    <link rel="stylesheet" href="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Transposição - Filtros</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="ano">Ano: &nbsp;&nbsp;</label></td>
                <td>
                    <input type="text" id="ano" class="field-size2" value="<?=db_getsession('DB_anousu')+1?>" disabled>
                </td>
            </tr>
            <tr>
                <td><label for="cboEtapa">Etapa: </label></td>
                <td>
                    <?php
                    $daoSerie = new cl_serie();
                    $sqlEtapas = $daoSerie->sql_query_file(
                        null,
                        "ed11_i_codigo, ed11_c_descr",
                        'ed11_i_ensino desc, ed11_i_sequencia asc',
                        ' ed11_i_ensino in (1, 3, 4) and ed11_i_codigo <> 18'
                    );
                    $rsEtapas = db_query($sqlEtapas);
                    if (!$rsEtapas) {
                        die("Erro ao buscar etapas");
                    }
                    ?>
                    <select name="cboEtapa" id="cboEtapa" class="field-size7">
                        <option value="">Selecione</option>
                        <?php
                        while ($etapa = pg_fetch_object($rsEtapas)) {
                            echo "<option value='{$etapa->ed11_i_codigo}'>{$etapa->ed11_c_descr}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnBuscar">
        <i class="fa fa-search"></i>
        Buscar
    </button>

    <fieldset style="border: 2px groove #FFF">
        <legend>Critérios de Desempate</legend>
        <div class="alert alert-info" role="alert">
            Para alterar a ordem clique no crtério, arraste e solte.
        </div>
        <div style="width: 400px">
            <table id="data-table" style="width: 100%;"></table>
        </div>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnSalvar">
        <i class="fa fa-save"></i>
        Salvar
    </button>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript" src="assets/jquery-tablednd/jquery.tablednd.js"></script>
<script src="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });
    let escolasDisponiveis = [];

    const cboEtapa = document.getElementById("cboEtapa");
    const inputAno = document.getElementById("ano");
    const btnBuscar = document.getElementById("btnBuscar");
    const btnSalvar = document.getElementById("btnSalvar");

    const salvarOrdenacao = (data) => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        formData.append('criteriosOrdenacao', JSON.stringify(data));
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/salvar-criterios-ordenacao`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                table.bootstrapTable('load', response.data);
            });
    }

    var table = jQuery('#data-table');
    table.bootstrapTable({
        data: [],
        locale: 'pt-BR',
        showButtonText: true,
        class: "table table-sm",
        useRowAttrFunc: true,
        reorderableRows: true,
        onReorderRow: salvarOrdenacao,
        uniqueId: 'ed352_codigo',
        height: 270,
        columns: [
            {
                title: "::",
                align: "center",
                width: 30,
                formatter: () => ('<b style="cursor: move">::</b>')
            },
            {
                title: 'Critério',
                field: 'descricao',
                align: 'left',
                formatter: (value, row) => row.criterio.ed351_descricao
            }, {
                title: 'Ordem',
                field: 'ed352_ordem',
                align: 'left',
                width: '100',
            }
        ]
    });

    btnBuscar.addEventListener('click', () => {
        if (empty(cboEtapa.value)) {
            alert('Selecione uma Etapa');
            return;
        }
        buscarCriterios();
    });

    const buscarCriterios = () => {
        const formData = new FormData();
        formData.append('etapa', cboEtapa.value);
        formData.append('ano', inputAno.value);
        PHPSession.appendFormData(formData);
        HttpClient
            .post(`${urlApi}/educacao/escola/transposicao/criterios-ordenacao`, { body: formData })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                table.bootstrapTable('load', response.data);
            });
    }

    btnSalvar.addEventListener('click', () => {
        salvarOrdenacao(table.bootstrapTable('getData'));
    });
</script>
</body>
</html>

