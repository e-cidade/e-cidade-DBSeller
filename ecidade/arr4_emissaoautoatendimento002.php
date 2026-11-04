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

  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("dbforms/db_funcoes.php"));
  require_once(modification("dbforms/db_classesgenericas.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("classes/db_declaracaoquitacao_classe.php"));

use ECidade\Tributario\Arrecadacao\Arquivo\Autoatendimento\RCB800\Repository\TipoDebito;
use ECidade\V3\Extension\Registry;
?>
<html>
<head>
  <?php
  db_app::load('scripts.js, strings.js, prototype.js, estilos.css, datagrid.widget.js, AjaxRequest.js, ProgressBar.widget.js, DBDownload.widget.js');
  ?>
  <style media="screen" type="text/css">
    #logs {
      overflow-y: auto;
      width: 100%;
      background-color: #000;
      padding: 3px 0;
      border-radius: 3px;
    }

    #logs .item-log {
      margin: 05px 10px 2px 10px;
      color: rgba(230, 221, 221, 0.85);
    }
    #fechar {
      margin-top: 14px;
    }
  </style>
</head>
<body class="body-default">
  <div class="container">
    <fieldset style="width: 700px; padding: 2px">
      <progress id="barra-progresso-arquivo" value="0" style="width: 100%; height: 25px;">Processando</progress>
    </fieldset>
    <div id="logs"></div>
    <input type="button" name="fechar" id="fechar" value="Fechar" />
    <div id="arquivo" class="container"></div>
  </div>
<script type="text/javascript">
    var barraArquivo          = $('barra-progresso-arquivo');
    var barraProgressoArquivo = new ProgressBar(barraArquivo, $('logs'));

    function download(oRetorno)
    {
        var download = new DBDownload();
        download.addFile(oRetorno.url, oRetorno.label);
        download.show();
        return;

        $('arquivo').childElements().forEach(item => item.remove());

        if(oRetorno.label && oRetorno.url) {

          var label = oRetorno.label;
          var url   = oRetorno.url;
          var oLink = new DBAncora( label, url );
              oLink.onClick( function() {
                js_arquivo_abrir(this.sUrl.urlEncode());
                return;
              });
              oLink.show($('arquivo'));
        }
    }

    $('fechar').on('click', function(event) {
        parent.iframe_emissao.limpaTela();
        parent.iframe_emissao.hide();
    })
</script>
<?php
try {
    db_postmemory($_GET);

    $nomeArquivo  = 'AutoAtendimento_RCB800_';
    $nomeArquivo .= date('Ymd');
    $nomeArquivo .= '.txt';
    $path         = 'tmp/';

    $producao = true;
    if(db_getsession('DB_DEBUG', false) !== true) {
      $producao = null;
    }

    $datainicial = new \DBDate($datainicial);
    $datafinal   = new \DBDate($datafinal);

    $progressBar = new ProgressBar('barraProgressoArquivo');

    $containerTributario   = Registry::get('app.container')->get('tributario.container');
    $serviceEmissaoArquivo = $containerTributario->get('Arquivo\Autoatendimento\RCB800\EmissaoService');
    $filtro = $containerTributario->get('Arquivo\Autoatendimento\RCB800\FiltroHydrator')->hydrate((object)array(
                                                                                                    'codigoLista'     => $codigoLista
                                                                                                   ,'datainicial'     => $datainicial->getDate()
                                                                                                   ,'datafinal'       => $datafinal->getDate()
                                                                                                   ,'producao'        => $producao
                                                                                                   ,'codigoConvenio'  => $codigoConvenio
                                                                                                 ));
    $tiposDebitoNaoProcessados = $serviceEmissaoArquivo->execute($filtro, $progressBar, $path . $nomeArquivo);

    $tipoDebitoRepository = $containerTributario->get('Arquivo\Autoatendimento\RCB800\Repository\TipoDebito');
    $virgula = "";
    $tipos = "";

    $tipoDebitoRepository = \ECidade\Tributario\Arrecadacao\Repository\TipoDebito::getInstance();
    foreach( $tiposDebitoNaoProcessados as $tipo ) {
      $tipoDebito = $tipoDebitoRepository->getTipoDebitoPorTipo($tipo);
      $tipo = $virgula . $tipoDebito->getDescricao();
      $virgula = ", ";
    }

    if (!empty($tiposDebitoNaoProcessados)) {
        $mensagem = "Os débitos, cujo tipo são $tipo não serão processados.";
        echo "<script>alert('$mensagem')</script>";
    }

    download($path, $nomeArquivo);

} catch (\Exception $e) {

    db_redireciona("db_erros.php?fechar=true&db_erro={$e->getMessage()}");
}

function download($path, $name)
{
    $path .= $name;

    echo "
        <script>
            var retorno = {
                label : \"{$name}\",
                url   : \"{$path}\"
            }
            download(retorno)
        </script>
    ";
}
?>
</body>
</html>

