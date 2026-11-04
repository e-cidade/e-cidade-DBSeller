<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = db_utils::postMemory($_GET);

$regencia = new Regencia($parametros->regencia);
$turma = $regencia->getTurma();
$etapa = $regencia->getEtapa();
$docentes = $regencia->getDocentes();
$professores = array_map(function ($docente) {
    return $docente->getNome();
}, $docentes);

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <style>
        .containerConteudo {
            border: 1px solid #A8A8A8;
            padding: 5px;
        }

        .columns > .btn {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<div class="container" style="width: 90%; max-width: 980px;">
    <fieldset style="clear: both">
        <legend>Lançamento de Notas Parciais</legend>
        <table class="form-container">
            <tr>
                <td style="width: 88px;">Turma:</td>
                <td style="width: 320px;"><?= $turma->getDescricao() ?></td>
                <td style="width: 80px; text-align: left">Etapa:</td>
                <td><?= $etapa->getNome() ?></td>
            </tr>
            <tr>
                <td>Disciplina:</td>
                <td><?= $regencia->getDisciplina()->getNomeDisciplina() ?></td>
                <td style="text-align: left">Professor(a):</td>
                <td><?= implode('<br>', $professores) ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="text-align: left"></td>
                <td></td>
            </tr>
            <tr>
                <td>Procedimento: </td>
                <td><?=$regencia->getProcedimentoAvaliacao()->getCodigo()?> - <?=$regencia->getProcedimentoAvaliacao()->getDescricao()?></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </fieldset>

    <div id='ctnAbas' style="clear: both"></div>
    <div id="contents"></div>
</div>

<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    const containerAbas = document.getElementById('ctnAbas');
    const abas = new DBAbas(containerAbas);

    const turma = <?=$turma->getCodigo()?>;
    let listaAbas = [];

    PHPSession.loadData().then(() => {
        const formData = new FormData();
        formData.append('etapa', <?=$etapa->getCodigo()?>);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/educacao/escola/turmas/${turma}/periodos-avaliacao`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            listaAbas = response.data;
            listaAbas.forEach(item => {
                const container = document.createElement('div');
                container.setAttribute('id', item.containerId);
                containerAbas.appendChild(container);
                item.container = container;
                const content = document.createElement('iframe');
                item.urlContent = `edu4_notasparciais<?=$parametros->grade?>.php?regencia=<?=$regencia->getCodigo()?>&encerrada=<?=$parametros->encerrada?>&periodoavaliacao=${item.periodoAvaliacao}`;
                content.setAttribute('src', '');
                if (item.ativo) {
                    content.setAttribute('src', item.urlContent);
                }
                content.setAttribute('style', 'border: 0; width: 960px; height: 480px');
                item.container.appendChild(content);
                item.aba = abas.adicionarAba(item.titulo, item.container, item.ativo);
                item.carregada = item.ativo;
                item.aba.setCallback(() => {
                    if (listaAbas.find(x => x.urlContent === item.urlContent && !x.carregada)) {
                        content.setAttribute('src', item.urlContent);
                        item.carregada = true;
                    }
                });
            });
        });
    });
</script>
</body>
</html>
