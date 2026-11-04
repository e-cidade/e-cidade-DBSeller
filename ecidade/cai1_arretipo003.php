<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

    use App\Domain\Tributario\Arrecadacao\Repositories\ConfiguracoesteftipodebitoRepository;
    use App\Domain\Tributario\Arrecadacao\Repositories\OperacoesteftipodebitoRepository;

    require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_arretipo_classe.php"));
require_once(modification("classes/db_arretipopix_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clarretipo    = new cl_arretipo;
$clarretipopix = new cl_arretipopix;

$db_opcao = 33;
$db_botao = false;
$lErro = false;

if (isset($excluir)) {
    $db_opcao = 3;
    db_inicio_transacao();
 
    if ($clarretipopix->excluir($k00_tipo))
    {
        $lErro = true;
    }

    $clarretipo->excluir($k00_tipo);
    if ($clarretipo->erro_status == '0') {
        $lErro = true;
    }

    db_fim_transacao($lErro);
} else {
    if (isset($chavepesquisa)) {
        $db_opcao = 3;
        $result = $clarretipo->sql_record($clarretipo->sql_query($chavepesquisa));
        $db_botao = true;

        db_fieldsmemory($result, 0);

        $receitacreditodescr = $k02_descr;
        $tipodescr = $k00_descr;

        $configuracoesteftipodebitoRepository = new ConfiguracoesteftipodebitoRepository();
        $oConfiguracoesteftipodebito = $configuracoesteftipodebitoRepository->getByTipo($chavepesquisa);

        db_postmemory((array) $oConfiguracoesteftipodebito);

        $operacoesteftipodebitoRepository = new OperacoesteftipodebitoRepository();
        $aOperacoes = $operacoesteftipodebitoRepository->getByConfig($oConfiguracoesteftipodebito->k196_sequencial);

        $aOperacoesSalvas = array_map(function ($oOperacao) {
            return $oOperacao->k197_operacoestef;
        }, $aOperacoes);
    }
}
?>
<!DOCTYPE html>
<html>
<head>

    <?php
    db_app::load('prototype.js, scripts.js, DBAbas.widget.js, DBAbasItem.widget.js');
    db_app::load("estilos.css, DBtab.style.css, time.js");
    ?>

  <style>
    body {
      padding: 0;
      margin: 16px 0 0 0;
    }

    .fieldsetPrincipal {
      width: 750px;
      margin: 20px auto 0 auto;
    }

    .fieldsetInterno {
      width: 750px;
      margin: 25px auto 0 auto;
    }

    .fieldsetSecundario {
      width: 700px;
      margin: 0 auto 0 auto;
    }

    fieldset legend {
      font-weight: bold;
    }

  </style>
</head>
<body bgcolor=#CCCCCC>
<?php
require_once(modification("forms/db_frmarretipo001.php"));
db_menu();
?>
</body>
<?php
if (isset($excluir)) {
    if ($clarretipo->erro_status == "0") {
        $clarretipo->erro(true, false);
    } else {
        $clarretipo->erro(true, true);
    }
}

if ($db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
  js_tabulacaoforms("form1", "excluir", true, 1, "excluir", true);
</script>
</html>
