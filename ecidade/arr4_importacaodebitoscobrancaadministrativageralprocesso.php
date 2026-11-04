<?php
/**
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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sql.php"));

use \ECidade\Tributario\Divida\ImportacaoDividaAtiva;

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
        </div>
        <script type="text/javascript">
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

    $oProgressBar = new ProgressBar('progress');
    $oProgressBar->flush();

    $oProgressBar->setMessageLog("Preparando registros (Etapa 1/11)");

    $parametros = db_utils::postMemory($_GET);

    if (empty($parametros->dados)) {
        throw new Exception('Não foram informados os parâmetros necessários para execução da importação.');
    }

    $parametros->lUnificarDebitos = $parametros->lUnificarDebitos === 'true' ? 1 : 0;

    db_inicio_transacao();

    $importacao = new ImportacaoDividaAtiva();
    $importacao->setProgressBar($oProgressBar);
    $importacao->setTipoDebitoOrigem($parametros->iTipoDebitoOrigem);
    $importacao->setObservacoes(db_stdClass::normalizeStringJsonEscapeString($parametros->sObservacoes));
    $importacao->setUnificarDebitos($parametros->lUnificarDebitos);

    if ((bool)$parametros->processoSistema === true && !empty($parametros->codigoProcesso)) {
        $importacao->setProcessoProtocolo(new processoProtocolo($parametros->codigoProcesso));
    }

    if ((bool)$parametros->processoSistema === false) {

        $importacao->setProcesso((object)array(
            'codigo' => $parametros->codigoProcesso,
            'titular' => $parametros->titularProcesso,
            'data' => $parametros->dataProcesso,
        ));
    }

    $importacao->setListaDebitos($parametros->listaDebitos);

    if ($parametros->lUnificarDebitos) {

        $importacao->setOrdemDataVencimento((int)$parametros->iTipoDataVencimento);

        if (!empty($parametros->sDataVencimento)) {
            $importacao->setDataVencimento(new DBDate($parametros->sDataVencimento));
        }
    }

    $dados = explode(',', $parametros->dados);

    foreach ($dados as $dado) {

        list($codigoProcedencia, $codigoReceita) = explode(':', $dado);

        $procedencia = new ProcedenciaDivida($codigoProcedencia);
        $importacao->adicionarReceitaProcedencia($codigoReceita, $procedencia);
    }

    $importacao->importacaoGeral();

    $oProgressBar->setMessageLog("Importação processada com sucesso.");

    db_fim_transacao(false);

    echo "<script type=\"text/javascript\">alert('Importação processada com sucesso')</script>\n";

} catch (Exception $exception) {
    db_fim_transacao(true);
    db_redireciona("db_erros.php?fechar=true&db_erro={$exception->getMessage()}");
}
