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
require_once modification('libs/db_utils.php');
require_once modification('std/DBDate.php');

use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;
use ECidade\RecursosHumanos\RH\Assentamento\AssentamentoHoraExtraManual;

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

db_postmemory($_POST);

$classenta          = new cl_assenta;
$cltipoasse         = new cl_tipoasse;

$db_opcao = 22;
$db_botao = false;

 if (isset($chavepesquisa)) {

  $db_opcao = 2;
  $result = $classenta->sql_record($classenta->sql_query($chavepesquisa));
  $classentamentofuncional = new cl_assentamentofuncional;
  $rsAssentamentoFuncional = db_query($classentamentofuncional->sql_query($chavepesquisa));
  $sOpcaoAssentamento      = 1;

  if($rsAssentamentoFuncional && pg_num_rows($rsAssentamentoFuncional) > 0) {
    $sOpcaoAssentamento    = 2;
  }
  db_fieldsmemory($result,0);

  $periodoJustificativa1 = null;
  $periodoJustificativa2 = null;
  $periodoJustificativa3 = null;

  switch ($h12_natureza) {

    case Assentamento::NATUREZA_JUSTIFICATIVA:
      $oDaoAssentamentoJustificativa = new cl_assentamentojustificativaperiodo;
      $rsAssentamentoJustificativa   = $oDaoAssentamentoJustificativa->sql_record($oDaoAssentamentoJustificativa->sql_query_file($h16_codigo));

      if($rsAssentamentoJustificativa && $oDaoAssentamentoJustificativa->numrows > 0) {

        db_utils::makeCollectionFromRecord($rsAssentamentoJustificativa, function($oRetornoJustificativasPeriodo) use (&$periodoJustificativa1, &$periodoJustificativa2, &$periodoJustificativa3) {

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
      $sqlAssentamentoHoraExtraManual   = $oDaoAssentamentoHoraExtraManual->sql_query_file(null, "*", 'h17_tipo', $whereAssentamentoHoraExtraManual);;
      $rsAssentamentoHoraExtraManual    = db_query($sqlAssentamentoHoraExtraManual);

      if($rsAssentamentoHoraExtraManual && pg_num_rows($rsAssentamentoHoraExtraManual) > 0) {
        db_utils::makeCollectionFromRecord($rsAssentamentoHoraExtraManual, function($oRetornoHorasExtrasManuais) use (
          &$horaExtraManual50Diurna,
          &$horaExtraManual50Noturna,
          &$horaExtraManual75Diurna,
          &$horaExtraManual75Noturna,
          &$horaExtraManual100Diurna,
          &$horaExtraManual100Noturna
        ){
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
      case Assentamento::NATUREZA_CONTROLE_MEDICO:
          $controleMedico = new \ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedico($chavepesquisa);
          $h26_sequencial = $controleMedico->getCodigo();
          $h26_dataatestado = $controleMedico->getDataAtestado();
          $h26_resultadoatestado = $controleMedico->getResultadoAtestado();
          $h26_nomemedico = $controleMedico->getNomeMedico();
          $h26_crmmedico = $controleMedico->getCrmMedico();
          $h26_ufcrm = $controleMedico->getUfCrm();
          $h26_cpfresponsavel = $controleMedico->getCpfResponsavel();
          $h26_nomeresponsavel = $controleMedico->getNomeResponsavel();
          $h26_crmresponsavel = $controleMedico->getCrmResponsavel();
          $h26_ufcrmresponsavel = $controleMedico->getUfCrmResponsavel();
          $h26_tipoexameocupacional = $controleMedico->getTipoExameOcupacional();
          $aExames = [];
          $count = 0;
          foreach ($controleMedico->getExames() as $exame) {
              $count += 1;
              $std = new stdClass();
              $std->sequencial = $count;
              $std->codigoProcedimento = $exame->getProcedimento();
              $std->codigoOrdem = $exame->getOrdem();
              $std->observacao = $exame->getObservacao();
              $std->data = \DBDate::format($exame->getData());
              $std->codigoResultado = $exame->getResultado();
              $std->descricaoProcedimento = utf8_encode($exame->getDescricaoProcedimento());
              $aExames[] = $std;
          }

          break;
  }

  $oDaoAssentaAttr = new cl_assentadb_cadattdinamicovalorgrupo();
  $rsComplemento   = db_query($oDaoAssentaAttr->sql_query_file(null,null, "h80_db_cadattdinamicovalorgrupo", null, "h80_assenta = $h16_codigo"));

  if (pg_num_rows($rsComplemento) > 0) {
    db_fieldsmemory($rsComplemento,0);
  }

  $db_botao = true;

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
<script language="javascript" type="text/javascript" src="scripts/dates.js"></script>
<script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script language="javascript" type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
<script language="javascript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<script language="javascript" type="text/javascript" src="scripts/classes/http/http.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
  <?php
  include(modification("forms/db_frmassenta.php"));
  ?>
    </center>
  </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($msg)) {
  db_msgbox($msg);
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","h16_regist",true,1,"h16_regist",true);
</script>
