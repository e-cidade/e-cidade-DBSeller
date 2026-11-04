<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Funcionários Ativos</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td>
                    <label for="">Período: </label>
                </td>
                <td>
                    <input type="date" id="data_inicial"> a
                    <input type="date" id="data_final">
                </td>
            </tr>
        </table>
        <?php
        if (db_getsession('DB_modulo') == 7159) {
        ?>
        <fieldset class="separator">
            <legend>Escolas</legend>
            <select name="cboEscolas" id="cboEscolas" style="min-width: 400px" size="8" multiple>
                <option value="0">TODOS</option>
                <?php
                $daoEscola = new cl_escola();
                $escolas = \App\Domain\Educacao\Escola\Models\Escola::query()->orderBy('ed18_i_codigo')->get();
                foreach ($escolas as $escola) {
                    ?>
                    <option value="<?=$escola->ed18_i_codigo?>"><?=$escola->ed18_i_codigo?> - <?=$escola->ed18_c_nome?></option>
                    <?php
                }
                ?>
            </select>
        </fieldset>
        <?php
        }
        ?>
        <fieldset class="separator">
            <legend>Função</legend>
            <select name="cboAtividadesRh[]" id="cboAtividadesRh" style="min-width: 400px" size="8" multiple>
                <option value="0">TODOS</option>
                <?php
                $daoAtividadeRh = new cl_atividaderh();
                $sqlAtividades = $daoAtividadeRh->sql_query_file(null, 'ed01_i_codigo, ed01_c_descr', 'ed01_i_codigo');
                $rsAtividades = db_query($sqlAtividades);
                while ($atividadeRh = pg_fetch_object($rsAtividades)) {
                    ?>
                    <option value="<?=$atividadeRh->ed01_i_codigo?>"><?=$atividadeRh->ed01_i_codigo?> - <?=$atividadeRh->ed01_c_descr?></option>
                    <?php
                }
                ?>
            </select>
        </fieldset>
        <fieldset class="separator">
            <legend>Cargo</legend>
            <select name="cboCargos[]" id="cboCargos" style="min-width: 400px" size="8" multiple>
                <option value="0">TODOS</option>
                <?php
                $daoRhFuncao = new cl_rhfuncao();
                $sqlFuncao = $daoRhFuncao->sql_query_file(null, db_getsession('DB_instit'), 'rh37_funcao, rh37_descr', 'rh37_funcao');
                $rsFuncao = db_query($sqlFuncao);
                while ($rhFuncao = pg_fetch_object($rsFuncao)) {
                    ?>
                    <option value="<?=$rhFuncao->rh37_funcao?>"><?=$rhFuncao->rh37_funcao?> - <?=$rhFuncao->rh37_descr?></option>
                    <?php
                }
                ?>
            </select>
        </fieldset>
    </fieldset>
    <button type="button" class="btn btn-light" id="button_emitir">
        <i class="fas fa-print"></i>
        Emitir relatório
    </button>
</div>

<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });

    const inputDataInicial = new DBInputDate(document.getElementById("data_inicial"));
    const inputDataFinal = new DBInputDate(document.getElementById("data_final"));
    const cboAtividadesRh = document.getElementById("cboAtividadesRh");
    const cboCargos = document.getElementById("cboCargos");
    const cboEscolas = document.getElementById("cboEscolas");
    const btnEmitir = document.getElementById("button_emitir");

    // var lancadorFuncao = new DBLancador('LancadorFuncao');
    // lancadorFuncao.setLabelAncora("Função");
    // lancadorFuncao.setTextoFieldset("Funções");
    // lancadorFuncao.setTituloJanela("Pesquisar Função");
    // lancadorFuncao.setNomeInstancia("lancadorFuncao");
    // lancadorFuncao.setParametrosPesquisa("func_atividaderh.php", ["ed01_i_codigo", "ed01_c_descr"], '');
    // lancadorFuncao.setGridHeight(150);
    // lancadorFuncao.show(ctnFuncao);

    const getSelecao = (element) => {
        const total = element.length;
        const selecionados = [];
        for (let x = 0; x < total; x++) {
            if (element.options[x].selected) {
                selecionados.push(element.options[x].value)
            }
        }
        return selecionados;
    }

    btnEmitir.addEventListener('click', () => {
        if (inputDataInicial.value != null &&
            inputDataFinal.value != null &&
            inputDataInicial.value > inputDataFinal.value) {
            alert('Data inicial não pode ser maior que a data final.');
            return;
        }

        const formData = new FormData();
        formData.append(
            'dataInicial',
            inputDataInicial.__toLocaleDateString() ? js_formatar(inputDataInicial.__toLocaleDateString(), 'd') : ''
        );
        formData.append(
            'dataFinal',
            inputDataFinal.__toLocaleDateString() ? js_formatar(inputDataFinal.__toLocaleDateString(), 'd') : ''
        );
        if (cboEscolas != null) {
            formData.append('escolas', getSelecao(cboEscolas).join(','));
        }
        formData.append('funcoes', getSelecao(cboAtividadesRh).join(','));
        formData.append('cargos', getSelecao(cboCargos).join(','));
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/secretaria/relatorios/funcionarios-ativos-escola`, { body: formData })
            .then((response) => {
            if (response.error) {
                alert(response.message);
                return;
            }
            window.open(
                response.data.file,
                '',
                'height=' + screen.height + ',width=' + screen.width + 'scrollbars=1,location=0'
            );
        });
    });
</script>
</body>
</html>
