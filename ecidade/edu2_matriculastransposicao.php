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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");

$transposicoes = Transposicao::query()->orderByDesc('ed353_ano')->get();
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
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>

    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
</head>

<body class="body-default">
<div class="container">
    <input type="hidden" id="escolaOrigem" value="<?php echo db_getsession('DB_coddepto') ?>"/>
    <fieldset>
        <legend>Designados para Matricular</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="transposicao">Ano transposição: &nbsp;&nbsp;</label></td>
                <td>
                    <select name="transposicao" id="transposicao">
                        <?php
                        foreach ($transposicoes as $transposicao) :
                            ?>
                            <option value="<?php echo $transposicao->ed353_codigo ?>"><?php echo $transposicao->ed353_ano ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <!--            <tr>-->
            <!--                <td><label for="cboEtapa">Etapa: </label></td>-->
            <!--                <td>-->
            <!--                    <select name="cboEtapa" id="cboEtapa">-->
            <!--                        <option value="">Selecione uma etapa de destino</option>-->
            <!--                    </select>-->
            <!--                </td>-->
            <!--            </tr>-->
        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnBuscar">
        <i class="fa fa-search"></i>
        Buscar
    </button>
</div>
<div class="container">
    <fieldset style="border: 2px groove #FFF">
        <legend>Lista de Designados para Matrícula</legend>
        <div style="width: 1200px">
            <table id="data-table" class="table table-sm" data-height="400" style="width: 100%;">
            </table>
        </div>
    </fieldset>
</div>

<script type="text/javascript" src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    let urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });

    const inputTransposicao = document.getElementById('transposicao');
    const btnBuscar = document.getElementById('btnBuscar');

    let table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        search: true,
        cache : false,
        showExport : true,
        exportTypes : ['csv', 'excel'],
        columns: [
            {
                title: 'Escola Origem',
                field: 'escola_origem',
                align: 'left',
                width: '350'
            },
            {
                title: 'Etapa Destino',
                field: 'etapa_destino',
                align: 'left',
                width: 55
            },
            {
                title: 'Cód. Aluno',
                field: 'codigo_aluno',
                align: 'left',
                width: 55
            },
            {
                title: 'Aluno',
                field: 'aluno',
                align: 'left'
            },
            {
                title: 'Situação',
                field: 'situacao',
                align: 'left',
                width: 150
            },
            {
                title: 'Permite Matricular',
                field: 'permite_matricular',
                align: 'center',
                formatter: (value) => {
                    if (value) {
                        return `<i class="fas fa-check-circle fa-lg" style="color: #449d44"></i>`;
                    }
                    return `<i class="fas fa-times-circle fa-lg" style="color: #c9302c"></i>`;
                },
                width: 120
            },
            {
                title: 'Dados',
                field: 'permite_matricular',
                align: 'center',
                formatter: (value, row) => {
                    if (value) {
                        return `<a href="#" onclick="abrirAlteracaoAluno(${row.codigo_aluno})"><i class="fas fa-search fa-lg"></i></a>`;
                    }
                    return `-`;
                }
            }
        ]
    });

    const abrirAlteracaoAluno = (aluno) => {
        let title = "Alunos > Alteração";

        let oParams = {
            action: `edu1_alunoabas002.php?editarAluno=${aluno}`,
            iInstitId: <?php echo db_getsession('DB_instit')?>,
            iAreaId: 8,
            iModuloId: 1100747
        }
        parent.Desktop.Window.create(title, oParams);
    }

    btnBuscar.addEventListener('click', () => {
        if (empty(inputTransposicao.value)) {
            alert('Informe o ano de transposição');
            return;
        }
        const formData = new FormData();
        formData.append('transposicao', inputTransposicao.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/transposicao/matriculas-designados`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
            table.bootstrapTable('load', response.data);
        });
    });
</script>
</body>
</html>
