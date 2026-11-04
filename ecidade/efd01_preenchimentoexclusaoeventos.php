<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

$instituicao = InstituicaoRepository::getInstituicaoSessao();
$integracao = \ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo::EFD_REINF;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="estilos/avaliacao.css">
    <link rel="stylesheet" type="text/css" href="estilos/awesomplete.css">
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputCpf.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputInteger.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputCheckboxRadio.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBRadio.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/avaliacao/DBViewResposta.classe.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/avaliacao/DBViewPergunta.classe.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/avaliacao/DBViewGrupoPerguntas.classe.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/avaliacao/DBViewFormulario.classe.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/eSocial/DBAutoCompleteEsocial.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/avaliacao/DBViewRespostaNula.classe.js"></script>
    <script src="scripts/awesomplete.js"></script>
    <script src="scripts/classes/avaliacao/DBAutoComplete.js"></script>
    <title>DBSeller Informática Ltda</title>
</head>
<body class="body-default">
<div class="container">
    <form id="form" style="width: 700px;">
        <fieldset>
            <legend>Exclusão de eventos do EFD-Reinf</legend>
            <table class="form-container" style="width: 100%;">
                <tbody>
                <tr>
                    <td>
                        <label for="descricao">Contribuinte:</label>
                    </td>
                    <td>
                        <input type="hidden" name="contribuinte" id="contribuinte"
                               value="<?php echo $instituicao->getCgm()->getCodigo() ?>">
                        <input type="text" name="descricao" id="descricao" class="readonly" disabled
                               value="<?php echo $instituicao->getDescricao() ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="evento">Evento:</label>
                    </td>
                    <td>
                        <select name="evento" id="evento">
                            <option value="R-2010">R-2010</option>
                            <option value="R-2020">R-2020</option>
                            <option value="R-2055">R-2055</option>
                            <option value="R-4010">R-4010</option>
                            <option value="R-4020">R-4020</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dataInicio">Data do envio:</label>
                    </td>
                    <td>
                        <input type="date" name="dataInicio" id="dataInicio"> à <input type="date" name="dataFinal" id="dataFinal">
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
        <input type="button" value="Consultar" id="consultar">
    </form>
    <fieldset style="margin-top: 10px;">
        <legend>Recibos</legend>
        <div id="recibos"></div>
    </fieldset>
</div>
<script type="text/javascript">
    const rpc = 'spedapi.RPC.php';
    const form = document.getElementById('form');
    const sugestao = document.getElementById('sugestao');
    const somenteLeitura = document.getElementById('somenteLeitura');
    const protocolo = document.getElementById('protocolo');
    const periodo = document.getElementById('periodo');
    const contribuinte = document.getElementById('contribuinte');
    const evento = document.getElementById('evento');
    const dataInicio = new DBInputDate(document.getElementById('dataInicio'));
    const dataFinal = new DBInputDate(document.getElementById('dataFinal'));
    const consultar = document.getElementById('consultar');
    const recibos = document.getElementById('recibos');

    const collectionProtocolos = new Collection().setId('codigo');
    const gridProtocolos = DatagridCollection.create(collectionProtocolos).configure({
        'order': false,
        'height': 200
    });

    gridProtocolos.addColumn('evento', {
        'label': 'Evento',
        'align': 'center',
        'width': '100px'
    });

    gridProtocolos.addColumn('recibo', {
        'label': 'Recibo',
        'align': 'center',
        'width': '300px'
    });

    gridProtocolos.addColumn('periodo', {
        'label': 'Período',
        'align': 'center',
        'width': '200px'
    });

    const selecionaRecibo = (event, item) => {
        let sugestao = JSON.stringify({
            'nrRecEvt': item.recibo,
            'perApur': item.periodo,
            'tpEvento': item.evento
        }).replace(/"/g, '\\"');

        let somenteLeitura = JSON.stringify([
            'nrRecEvt',
            'perApur',
            'tpEvento'
        ]).replace(/"/g, '\\"');

        let protocolo = item.recibo;
        let periodo   = item.periodo;

        let url = new URL('http://sped02_preenchimento.php');
        url.searchParams.append('integracao', 1);
        url.searchParams.append('formularioTipo', 25);
        url.searchParams.append('sugestao', sugestao)
        url.searchParams.append('somenteLeitura', somenteLeitura);
        url.searchParams.append('protocolo', protocolo);
        url.searchParams.append('periodo', periodo);

        action = url.host + url.search;
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_exclusao', action, 'Exclusão de Evento', true);
    };

    gridProtocolos.addAction(
        'Selecionar',
        'Selecionar',
        selecionaRecibo,
        true,
        'fa-trash'
    );

    gridProtocolos.show(recibos);

    consultar.addEventListener('click', () => {
        const data = new FormData();
        data.append('acao', 'consulta');
        data.append('rota', '/evento/recibos_validos');
        data.append('cgmContribuinte', contribuinte.value);
        data.append('idEvento', evento.value);
        data.append('eventoExclusao', 'R-9000');
        data.append('naoExcluidos', true);

        collectionProtocolos.clear();

        if (dataInicio.value) {
            data.append('dataInicio', dataInicio.getValue().toISOString());
        }

        if (dataFinal.value) {
            data.append('dataFinal', dataFinal.getValue().toISOString());
        }

        HttpClient.post(rpc, {body: data}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            const eventos = [];
            response.data.map(evento => {

                var eventoJson = JSON.parse(evento.evento),
                    periodo = null;

                switch (evento.tipo) {
                    case 'R-2010':
                        periodo = eventoJson.perApur;
                        break;
                    case 'R-2020':
                        periodo = eventoJson.ideEstabPrest.perApur;
                        break;
                    case 'R-2055':
                    case 'R-4010':
                    case 'R-4020':
                        periodo = eventoJson.perApur;
                        break;
                }

                evento.recibo.map(recibo => {
                    eventos.push({
                        'codigo': recibo.id,
                        'evento': evento.tipo,
                        'recibo': recibo.numero,
                        'periodo': periodo
                    });
                });
            });

            collectionProtocolos.add(eventos);
            gridProtocolos.reload();
        });
    });
</script>
<?php db_menu(); ?>
</body>
