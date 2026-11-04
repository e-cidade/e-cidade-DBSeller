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

use App\Domain\Educacao\Escola\Models\Transposicao;

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
    <link type="text/css" href="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css"
          rel="stylesheet">
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
                    <?php
                    $transposicao = Transposicao::getTransposicaoAtual();
                    ?>
                    <input type="text" id="ano" class="field-size2" value="<?=$transposicao->ed353_ano?>" disabled>
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
</div>
<div class="container">
    <fieldset style="border: 2px groove #FFF">
        <legend>Cadastro de Vagas - Transposição</legend>
        <div style="width: 1200px">
            <table id="data-table"
                   class="table table-sm"
                   data-height="370"
                   style="width: 100%;">
            </table>
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
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });
    const transposicaoProcessada = '<?=$transposicao->ed353_processada?>';
    let escolasDisponiveis = [];

    const cboEtapa = document.getElementById("cboEtapa");
    const inputAno = document.getElementById("ano");
    const btnBuscar = document.getElementById("btnBuscar");
    const btnSalvar = document.getElementById("btnSalvar");

    window.events = {
        'change .vagas': function (e, value, row) {
            row.ed350_vagas = e.target.value;
            row.ed350_saldo = e.target.value;
            document.getElementById(`saldo${row.ed350_codigo}`).value = e.target.value;
        },
        'change .vagaspcd': function (e, value, row) {
            row.ed350_vagaspcd = e.target.value;
            row.ed350_saldopcd = e.target.value;
            document.getElementById(`saldopcd${row.ed350_codigo}`).value = e.target.value;
        },
        'change .saldo': function (e, value, row) {
            row.ed350_saldo = e.target.value;
        },
        'change .saldopcd': function (e, value, row) {
            row.ed350_saldopcd = e.target.value;
        }
    }

    const vagasFormatter = (value, row, index, field) => {
        let quantidade = row[field] === undefined ? 0 : row[field];
        let desabilitarVagas = transposicaoProcessada ? 'disabled' : '';
        let desabilitarSaldos = !transposicaoProcessada ? 'disabled' : '';

        if (field === 'ed350_saldo') {
            return `<input type="text" id="saldo${row.ed350_codigo}" value="${quantidade}" class="saldo" ${desabilitarSaldos} />`;
        }
        return `<input type="text" id="vaga${row.ed350_Codigo}" value="${quantidade}" class="vagas" ${desabilitarVagas} />`;
    }
    const vagasPcdFormatter = (value, row, index, field) => {
        let quantidade = row[field] === undefined ? 0 : row[field];
        let desabilitarVagas = transposicaoProcessada ? 'disabled' : '';
        let desabilitarSaldos = !transposicaoProcessada ? 'disabled' : '';

        if (field === 'ed350_saldopcd') {
            return `<input type="text" id="saldopcd${row.ed350_codigo}" value="${quantidade}" class="saldopcd" ${desabilitarSaldos} />`;
        }
        return `<input type="text" id="vagapcd${row.ed350_Codigo}" value="${quantidade}" class="vagaspcd" ${desabilitarVagas} />`;
    }
    const escolaFormatter = (value, row) => {
        return row.escola.ed18_c_nome;
    }

    var table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        cache: 'false',
        search: true,
        columns: [
            [
                {
                    title: 'ESCOLA',
                    field: 'ed18_c_nome',
                    rowspan: 2,
                    align: 'left',
                    valign: 'middle',
                    width: '200',
                    formatter: escolaFormatter
                },
                {
                    title: 'VAGAS',
                    field: '',
                    colspan: 2,
                    align: 'center',
                },
                {
                    title: 'VAGAS PCD',
                    field: '',
                    colspan: 2,
                    align: 'center',
                },
            ],
            [
                {
                    title: 'TOTAL',
                    field: 'ed350_vagas',
                    align: 'left',
                    valign: 'center',
                    width: '50',
                    formatter: vagasFormatter,
                    events: events
                },
                {
                    title: 'SALDO',
                    field: 'ed350_saldo',
                    align: 'left',
                    valign: 'center',
                    width: '50',
                    formatter: vagasFormatter,
                    events: events
                },
                {
                    title: 'TOTAL',
                    field: 'ed350_vagaspcd',
                    align: 'left',
                    valign: 'center',
                    width: '50',
                    formatter: vagasPcdFormatter,
                    events: events
                },
                {
                    title: 'SALDO',
                    field: 'ed350_saldopcd',
                    align: 'left',
                    valign: 'center',
                    width: '50',
                    formatter: vagasPcdFormatter,
                    events: events
                }
            ]
        ],
    })

    btnBuscar.addEventListener('click', () => {
        if (empty(cboEtapa.value)) {
            alert('Selecione uma Etapa');
            return;
        }
        buscarVagasEscolas();
    });

    const buscarVagasEscolas = () => {
        const formData = new FormData();
        formData.append('etapa', cboEtapa.value);
        formData.append('ano', inputAno.value);
        PHPSession.appendFormData(formData);
        HttpClient
            .post(`${urlApi}/educacao/escola/transposicao/vagas-etapa`, { body: formData })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                table.bootstrapTable('load', response.data);
            });
    }

    btnSalvar.addEventListener('click', () => {
        const vagas = table.bootstrapTable('getData');

        const formData = new FormData();
        formData.append('ano', inputAno.value);
        formData.append('etapa', cboEtapa.value);
        formData.append('vagas', JSON.stringify(vagas));
        PHPSession.appendFormData(formData);
        HttpClient
            .post(`${urlApi}/educacao/escola/transposicao/vagas`, { body: formData })
            .then((response) => {
                alert(response.message);
            });
    });
</script>
</body>
</html>
