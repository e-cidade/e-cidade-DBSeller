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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('classes/db_assenta_classe.php');
require_once modification('classes/db_tipoasse_classe.php');
require_once modification('dbforms/db_funcoes.php');

use ECidade\RecursosHumanos\RH\Assentamento\Repository\LoteLancamentoRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

db_postmemory($HTTP_POST_VARS);

$classenta  = new cl_assenta;
$cltipoasse = new cl_tipoasse;
$classentamentofuncional         = new cl_assentamentofuncional;
$oDaoAssentamentoJustificativa   = new cl_assentamentojustificativaperiodo;
$oDaoAssentamentoHoraExtraManual = new cl_assentamentohoraextra;

$db_botao = false;
$db_opcao = 33;
$msgRetorno = "";

if (isset($excluir)) {
    try {
        db_inicio_transacao();
        $assentamento = AssentamentoRepository::getInstanceByCodigo($h16_codigo);
        $loteLancamento = LoteLancamentoRepository::getLotePorAssentamento($assentamento);
        if (AssentamentoRepository::excluiAssentamentoEfetividade($assentamento, true)) {
            if (!empty($loteLancamento)) {
                $loteLancamento->unsetAssentamento($assentamento);

                if (count($loteLancamento->getAssentamentos()) === 0) {
                    $resultado = LoteLancamentoRepository::delete($loteLancamento);
                    if (count($resultado->erros) > 0) {
                        throw new Exception('Não foi possível excluir o lote no qual o assentamento pertencia.');
                    }
                }
            }
            $msgRetorno = "Exclusão efetuada com sucesso.";
        }
        db_fim_transacao(false);
    } catch (Exception $oException) {
        db_fim_transacao(true);
        $paginaRedirecionamento = $_SERVER['PHP_SELF'];
        $paginaRedirecionamento .= "?";
        if (!empty($_SERVER["QUERY_STRING"])) {
            $paginaRedirecionamento .= $_SERVER['QUERY_STRING'];
            $paginaRedirecionamento .= "&";
        }
        $paginaRedirecionamento .= "=".$oException->getMessage();
        $classenta->erro_status  = "1";
        $msgRetorno = $oException->getMessage();
        db_msgbox($msgRetorno);
        if ($sOpcaoAssentamento == 1) {
            db_redireciona("rec1_assenta003.php?iTipoFuncionamento=1");
        }
        db_redireciona("rec1_assenta003.php");
    }
    db_msgbox($msgRetorno);
    if ($classenta->erro_status != "0") {
        $h12_codigo = $h12_assent = $h16_assent = $h12_natureza = '';
    }
} elseif (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $classenta->sql_record($classenta->sql_query($chavepesquisa));
    $classentamentofuncional = new cl_assentamentofuncional;
    $rsAssentamentoFuncional = db_query($classentamentofuncional->sql_query($chavepesquisa));
    $sOpcaoAssentamento      = 1;

    if ($rsAssentamentoFuncional && pg_num_rows($rsAssentamentoFuncional) > 0) {
        $sOpcaoAssentamento    = 2;
    }
    db_fieldsmemory($result, 0);
    $db_botao = true;

    switch ($h12_natureza) {
        case Assentamento::NATUREZA_JUSTIFICATIVA:
            $rsAssentamentoJustificativa   = $oDaoAssentamentoJustificativa->sql_record($oDaoAssentamentoJustificativa->sql_query_file($h16_codigo));
            if ($rsAssentamentoJustificativa && $oDaoAssentamentoJustificativa->numrows > 0) {
                db_utils::makeCollectionFromRecord($rsAssentamentoJustificativa, function ($oRetornoJustificativasPeriodo) use (&$periodoJustificativa1, &$periodoJustificativa2, &$periodoJustificativa3) {
                    switch ($oRetornoJustificativasPeriodo->rh206_periodo) {
                        case 1:
                            $periodoJustificativa1 = $oRetornoJustificativasPeriodo->rh206_periodo;
                            break;
                        case 2:
                            $periodoJustificativa2 = $oRetornoJustificativasPeriodo->rh206_periodo;
                            break;
                        case 3:
                            $periodoJustificativa3 = $oRetornoJustificativasPeriodo->rh206_periodo;
                            break;
                    }
                });
            }
            break;
        case Assentamento::NATUREZA_HE_MANUAL:
            $oDaoAssentamentoHoraExtraManual  = new cl_assentamentohoraextra;
            $whereAssentamentoHoraExtraManual = "h17_assenta = {$chavepesquisa}";
            $sqlAssentamentoHoraExtraManual   = $oDaoAssentamentoHoraExtraManual->sql_query_file(null, "*", 'h17_tipo', $whereAssentamentoHoraExtraManual);
            $rsAssentamentoHoraExtraManual    = db_query($sqlAssentamentoHoraExtraManual);
            if ($rsAssentamentoHoraExtraManual && pg_num_rows($rsAssentamentoHoraExtraManual) > 0) {
                db_utils::makeCollectionFromRecord($rsAssentamentoHoraExtraManual, function ($oRetornoHorasExtrasManuais) use (
                    &$horaExtraManual50Diurna,
                    &$horaExtraManual50Noturna,
                    &$horaExtraManual75Diurna,
                    &$horaExtraManual75Noturna,
                    &$horaExtraManual100Diurna,
                    &$horaExtraManual100Noturna
                ) {
                    switch ($oRetornoHorasExtrasManuais->h17_tipo) {
                        case BaseHora::HORAS_EXTRA50:
                            $horaExtraManual50Diurna  = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                        case BaseHora::HORAS_EXTRA75:
                            $horaExtraManual75Diurna  = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                        case BaseHora::HORAS_EXTRA100:
                            $horaExtraManual100Diurna  = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                        case BaseHora::HORAS_EXTRA50_NOTURNA:
                            $horaExtraManual50Noturna = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                        case BaseHora::HORAS_EXTRA75_NOTURNA:
                            $horaExtraManual75Noturna  = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                        case BaseHora::HORAS_EXTRA100_NOTURNA:
                            $horaExtraManual100Noturna = $oRetornoHorasExtrasManuais->h17_hora;
                            break;
                    }
                });
            }
            break;
    }
}
?>
<html>
    <head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/dates.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/dates.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/classes/http/http.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <center>
    <?php include(modification("forms/db_frmassenta.php")); ?>
        </center>
        <?php db_menu(); ?>
        <?php
        if (isset($h12_natureza)) {
            switch ($h12_natureza) {
                case Assentamento::NATUREZA_HE_MANUAL:
                    ?>
      <script type="text/javascript">
        $$('.hora-extra-manual')[0].style.display = 'table-row';
      </script>
                    <?php
                    break;
            }
        }
        ?>
    </body>
</html>
<?php
if (isset($excluir)) {
    if ($classenta->erro_status=="0") {
        $classenta->erro(true, false);
    } else {
        $classenta->erro(true, false);
        if ($sOpcaoAssentamento == 1) {
            db_redireciona("rec1_assenta003.php?iTipoFuncionamento=1");
        }
        db_redireciona("rec1_assenta003.php");
    }
}
if ($msgRetorno !== "") {
    db_msgbox($msgRetorno);
}
if ($db_opcao==33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
  js_tabulacaoforms("form1","excluir",true,1,"excluir",true);
</script>
