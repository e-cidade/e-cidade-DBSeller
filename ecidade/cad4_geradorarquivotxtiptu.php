<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sql.php"));

$path = 'tmp/dados'.'_' . date('YmdHis').'.txt';

?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="javascript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/widgets/ProgressBar.widget.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <style media="screen" type="text/css">
            #log-processamento {
                height: 250px;
                overflow-y: auto;
                width: 100%;
                background-color: #000;
                padding-top: 3px;
            }

            #log-processamento .item-log {
                margin: 2 10 2 10;
                text-align: left;
                color: #878f87
            }
        </style>
    </head>
    <body class="body-default" >
        <div class="container">
            <fieldset style="width: 700px; padding: 2">
                <progress id="barra-progresso" value="0" style="width: 100%; height: 25px;">Processando</progress>
            </fieldset>
            <fieldset style="width: 700px; padding: 1 2">
                <div id="log-processamento"></div>
            </fieldset>
            <a href="<?php echo($path); ?>" download style="text-decoration: none;"><input type="button" id="btnArquivoDados" disabled="disabled" value="Download de Arquivo de Dados"></input></a>
            <a href="<?php echo('tmp/layout_' . date('Ymd') . '.txt'); ?>" download style="text-decoration: none;"><input type="button" id="btnArquivoLayout" disabled="disabled" value="Download de Arquivo de Layout"></input></a>
        </div>
        <script type="text/javascript">
            js_removeObj('divCarregando');
            var bar = $('barra-progresso');
            var logs = $('log-processamento');
            var progress = new ProgressBar(bar, logs);
        </script>
    </body>
</html>
<?php

try {

    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $parametros = db_utils::postMemory($_GET);

    $progressBar = new ProgressBar('progress');
    $progressBar->flush();

    $container = ECidade\Tributario\Library\Registry::getContainer();
    $session = $container->get('Session');
    $dataBase = $container->get('DataBase');

    $filtroHydrator = $container->get('Iptu\Arquivo\FiltroHydrator');
    $filtro = $filtroHydrator->build($parametros);

    $emissaoService = $container->get('Iptu\Arquivo\EmissaoService');
    $emissaoService->setBar($progressBar);
    $emissaoService->setInstituicao(InstituicaoRepository::getInstituicaoSessao());

    $dataBase->begin();

    $dataBase->execute("select fc_startsession()");
    $dataBase->execute("select fc_putsession('DB_instit', '{$session->getInstituicao()}')");
    $dataBase->execute("select fc_putsession('DB_anousu', '{$session->getAno()}')");

    $emissaoService->execute($filtro, $session, $path);

    $dataBase->commit();

    echo("<script type=\"text/javascript\">document.getElementById('btnArquivoDados').disabled='';document.getElementById('btnArquivoLayout').disabled='';</script>");

} catch (Exception $exception) {

    $dataBase->rollback();
    db_redireciona("db_erros.php?fechar=true&db_erro={$exception->getMessage()}");
}
