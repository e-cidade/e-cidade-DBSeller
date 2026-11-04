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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_arretipo_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

db_postmemory($HTTP_POST_VARS);

$clarretipo = new cl_arretipo;

use App\Domain\Tributario\Arrecadacao\Repositories\ConfiguracoesteftipodebitoRepository;
use App\Domain\Tributario\Arrecadacao\Models\Configuracoesteftipodebito;
use App\Domain\Tributario\Arrecadacao\Models\Operacoesteftipodebito;
use App\Domain\Tributario\Arrecadacao\Repositories\OperacoesteftipodebitoRepository;

$db_opcao = 1;
$db_botao = true;
$lErro = false;

if (isset($incluir)) {
    $sMensagem = null;

    if (!empty($k00_dtVencimentoDay)) {
        $clarretipo->k00_dtvencimento = $k00_dtVencimentoDay;
    }

    if (!empty($k00_dtVencimentoDate)) {
        $oDate = new \DBDate($k00_dtVencimentoDate);

        $clarretipo->k00_dtvencimento = $oDate->getDate();
    }

    if ($selectTipoVenc == 1) {
        $clarretipo->k00_dtvencimento = db_getsession("DB_anousu");
    }

    if (empty($clarretipo->k00_dtvencimento)) {
        $clarretipo->k00_dtvencimento = null;
    }

    if (!empty($k00_taxaespecifica)) {
        $clarretipo->k00_taxaespecifica = $k00_taxaespecifica;
    } else {
        $clarretipo->k00_taxaespecifica = 'null';
    }

    db_inicio_transacao();

    $clarretipo->k00_instit = db_getsession('DB_instit');
    $clarretipo->incluir($k00_tipo);
    if ($clarretipo->erro_status == '0') {
        $lErro = true;
    }

    $configuracoesteftipodebitoRepository = new ConfiguracoesteftipodebitoRepository();
    $configuracoesteftipodebito = new Configuracoesteftipodebito();
    $configuracoesteftipodebito->setSequencial($k196_sequencial);
    $configuracoesteftipodebito->setTipo($clarretipo->k00_tipo);
    $configuracoesteftipodebito->setAceitatef(isset($k196_aceitatef) ? "t" : "f");
    $configuracoesteftipodebito->setMaximoparcelas($k196_maximoparcelas);
    $configuracoesteftipodebito->setValorminimoparcelafisica($k196_valorminimoparcelafisica);
    $configuracoesteftipodebito->setValorminimoparcelajuridica($k196_valorminimoparcelajuridica);

    $k196_sequencial = $configuracoesteftipodebitoRepository->salvar($configuracoesteftipodebito);

    $operacoesteftipodebitoRepository = new OperacoesteftipodebitoRepository();
    $operacoesteftipodebito = new Operacoesteftipodebito();
    $operacoesteftipodebito->setConfiguracoesteftipodebito($k196_sequencial);

    $operacoesteftipodebitoRepository->deleteByConfig($k196_sequencial);

    foreach ($operacoes as $operacao) {
        if (intval($operacao) == 3 && empty($k196_maximoparcelas)) {
            $sMensagem = "Preencha o campo Máximo de Parcelas.";
            $lErro = true;
        }

        $operacoesteftipodebito->setOperacoestef($operacao);
        $operacoesteftipodebitoRepository->salvar($operacoesteftipodebito);
    }

    db_fim_transacao($lErro);
}
?>
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
<body class="body-default">
<?php
include(modification("forms/db_frmarretipo001.php"));

db_menu();
?>
</body>
<?php
if (isset($incluir)) {
    if ($clarretipo->erro_status == "0" && !$lErro) {
        $clarretipo->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clarretipo->erro_campo != "") {

            echo "<script> document.form1." . $clarretipo->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clarretipo->erro_campo . ".focus();</script>";
        }
    } else {
        if (empty($sMensagem)) {
            $clarretipo->erro(true, true);
        } else {
            db_msgbox($sMensagem);
        }
    }
}
?>
</html>
