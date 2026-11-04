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
require_once(modification("classes/db_sepultamentos_classe.php"));
require_once(modification("classes/db_renovacoes_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

use App\Domain\Tributario\Cemiterio\Repositories\Cemiterio\Parametros\ParametrosCemiterioRepository;

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
db_postmemory($_GET);

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clsepultamentos   = new cl_sepultamentos;
$clrenovacoes      = new cl_renovacoes;
$clitenserv        = new cl_itenserv;
$cltaxaserv        = new cl_taxaserv;
$cltaxaservval     = new cl_taxaservval;
$cltxsepultamentos = new cl_txsepultamentos;
$clrotulo          = new rotulocampo;
$db_opcao          = 1;
$db_botao          = true;
$lErro             = false;
$sMsgErro          = "AVISO!\\nCGM informado já Sepultado.";

$anoSessao = (db_getsession('DB_anousu'));
$taxaObrigatoria = false;

$parametrosCemiterioRepository =new ParametrosCemiterioRepository();
$parametrosCadastrados = $parametrosCemiterioRepository->buscaParametros($anoSessao);

if (count($parametrosCadastrados) > 0) {
  $taxaObrigatoria = $parametrosCadastrados[0]['cem36_obrigatoriedadetaxasepultamento'];
}

  /*
   * Verifica se o sepultamente já está cadastrado
   */
if (isset($cm01_i_codigo)) {

  $rsResult = $clsepultamentos->sql_record($clsepultamentos->sql_query_file($cm01_i_codigo));
  if ($rsResult) {

    if ($clsepultamentos->numrows > 0) {
      $lErro = true;
    }
  }
}
/**
 * Verificamos se o sepultado é pessoa juridica
 */
$oCgmSepultado = CgmFactory::getInstanceByCgm($cm01_i_codigo);
if (!$oCgmSepultado->isFisico()) {

  $lErro    = true;
  $sMsgErro = "Sepultado informado não pode ser Pessoa Jurídica.";
}

/**
 * Verificamos se o declarante é pessoa juridica
 */
if (!empty($cm01_i_declarante)) {

  $oCgmDeclarante = CgmFactory::getInstanceByCgm($cm01_i_declarante);

  if (!$oCgmDeclarante->isFisico()) {

    $lErro    = true;
    $sMsgErro = "Declarante informado não pode ser Pessoa Jurídica.";
  }
}

$oDataHoje = new DBDate(date('d/m/Y'));
$oDataFalecimento = new DBDate($cm01_d_falecimento);

if (strtotime($oDataHoje->getDate()) < strtotime($oDataFalecimento->getDate())) {

  $lErro    = true;
  $sMsgErro = "Data de Falecimento não pode ser maior que a data atual.";
}
if ($lErro) {

  db_msgbox($sMsgErro);
  echo "<script>
             parent.document.formaba.a2.disabled=true;
             parent.document.formaba.a3.disabled=true;
             parent.mo_camada('a1');
           </script>";
}
//resgata os valores
$clsepultamentos->cm01_i_medico        = (isset($cm01_i_medico) ? $cm01_i_medico : null);
$clsepultamentos->cm01_c_nomemedico    = (isset($cm32_nome) ? $cm32_nome : null);
$clsepultamentos->cm01_c_nomehospital  = (isset($nome_hospital) ? $nome_hospital : null);
$clsepultamentos->cm01_c_nomefuneraria = (isset($nome_funeraria) ? $nome_funeraria : null);
$clsepultamentos->cm01_i_cemiterio     = $cm01_i_cemiterio;
$clsepultamentos->cm01_c_conjuge       = $cm01_c_conjuge;
$clsepultamentos->cm01_c_cor           = $cm01_c_cor;
$clsepultamentos->cm01_d_falecimento   = $cm01_d_falecimento;
$clsepultamentos->cm01_observacoes     = urldecode($cm01_observacoes);
$clsepultamentos->cm01_d_cadastro      = date("Y-m-d", db_getsession("DB_datausu"));
$clsepultamentos->cm01_i_funcionario   = db_getsession("DB_id_usuario");
if (isset($incluir) && !$lErro) {

  db_inicio_transacao();
  $clsepultamentos->incluir($cm01_i_codigo);

  //adiciona data de falecimento no cadastro cgm
  $oCgm = new CgmFisico($cm01_i_codigo);
  $oCgm->setDataFalecimento($cm01_d_falecimento);
  $oCgm->save();

  db_fim_transacao();

  //cadastra em renovacoes
  $clrenovacoes->cm07_i_sepultamento = $clsepultamentos->cm01_i_codigo;
  $clrenovacoes->cm07_i_renovante    = $clsepultamentos->cm01_i_declarante;
  $clrenovacoes->cm07_d_vencimento   = $cm07_d_vencimento_ano . "-" . $cm07_d_vencimento_mes . "-" . $cm07_d_vencimento_dia;
  $clrenovacoes->cm07_d_ultima       = $clrenovacoes->cm07_d_vencimento;
  $clrenovacoes->incluir(null);
}
$db_opcao = 1;
$db_botao = true;
$lSqlErro = false;
if (isset($cm01_i_declarante) && trim($cm01_i_declarante) == "") {

  $sMsgDeclarate = 'Declarante não informado!\nAtualize o cadastro do Sepultamento e informe o Declarante.';
  unset($incluir);
}

if (isset($incluir)) {

  $result_numpre = db_query("select nextval('numpref_k03_numpre_seq')");
  $oNumpre = db_utils::fieldsMemory($result_numpre, 0);

  db_inicio_transacao();

  $taxaIncluida = (isset($oPost->cm10_f_valor) 
                    && (trim($oPost->cm10_f_valor) != '') 
                    && isset($oPost->cm10_f_valortaxa) 
                    && (trim($oPost->cm10_f_valortaxa) != '')
                  );

  if (!$lSqlErro && ($taxaObrigatoria || $taxaIncluida)) {

    $clitenserv->cm10_i_numpre      = $oNumpre->nextval;
    $clitenserv->cm10_d_data        = $cm01_d_falecimento;
    $clitenserv->cm10_i_taxaserv    = $oPost->cm10_i_taxaserv;
    $oPost->cm10_f_valor            = str_replace(".", "", strval($oPost->cm10_f_valor));
    $clitenserv->cm10_f_valor       = str_replace(",", ".", strval($oPost->cm10_f_valor));
    $clitenserv->cm10_t_obs         = $oPost->cm10_t_obs;
    $clitenserv->cm10_i_usuario     = db_getsession("DB_id_usuario");
    $oPost->cm10_f_valortaxa        = str_replace(".", "", strval($oPost->cm10_f_valortaxa));
    $clitenserv->cm10_f_valortaxa   = str_replace(",", ".", strval($oPost->cm10_f_valortaxa));

    $clitenserv->incluir(null);

    $sErroMsg = $clitenserv->erro_msg;
    if ($clitenserv->erro_status == 0) {
      $lSqlErro = true;
    }
    
    if (!$lSqlErro) {
      
      $codigoSepultamento = isset($oPost->cm31_i_sepultamento) ? $oPost->cm31_i_sepultamento : $oGet->cm01_i_codigo;
      $cltxsepultamentos->cm31_i_sepultamento = $codigoSepultamento;
      $cltxsepultamentos->cm31_i_itenserv     = $clitenserv->cm10_i_codigo;
      $cltxsepultamentos->incluir(null);
      
      $sErroMsg = $cltxsepultamentos->erro_msg;
      if ($cltxsepultamentos->erro_status == 0) {
        $lSqlErro = true;
      }
    }
    
    if (!$lSqlErro) {

      if ($clitenserv->numrows_incluir != 0) {
        
        $sSql = "select fc_cemitarrecad(2,{$oNumpre->nextval},true) as retorno";
        $result_arrecad = db_query($sSql) or die("Erro ao incluir em arrecad.");
        $oArrecad       = db_utils::fieldsMemory($result_arrecad, 0);
        
        if (substr($oArrecad->retorno, 0, 1) != '9') {
          db_msgbox($oArrecad->retorno);
        }
      }
    }
  }

  db_fim_transacao($lSqlErro);
}
?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
  <?php
  db_app::load("scripts.js, strings.js, prototype.js, datagrid.widget.js");
  db_app::load("widgets/messageboard.widget.js, widgets/windowAux.widget.js");
  db_app::load("estilos.css, grid.style.css");
  ?>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body class="abas">
  <div class="container">
    <?php
    include(modification("forms/db_frmsepultamentosinclusao.php"));
    ?>
  </div>
</body>

</html>
<?php
if (isset($incluir) && !$lErro) {

  if ($clsepultamentos->erro_status == "0") {

    db_msgbox($clsepultamentos->erro_msg);
    $db_botao = true;

    echo "<script>document.form1.db_opcao.disabled=false;</script>";

    if ($clsepultamentos->erro_campo != "") {
      echo "<script> document.form1." . $clsepultamentos->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clsepultamentos->erro_campo . ".focus();</script>";
    } 
  } else {

    db_msgbox($clrenovacoes->erro_msg);
?>
    <script>
      parent.document.formaba.a2.disabled = true;
      parent.document.formaba.a3.disabled = false;
      (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_a3.location.href = 'cem1_sepultamentos003.php?sepultamento=<?= $cm01_i_codigo ?>&cemiterio=<?= $cm01_i_cemiterio ?>';
      parent.mo_camada('a3');
    </script>
<?php
  }
}
if (isset($incluir)) {

  if ($clitenserv->erro_status == "0") {

    $db_botao = true;

    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";

    if ($clitenserv->erro_campo != "") {

      echo "<script> document.form1." . $clitenserv->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clitenserv->erro_campo . ".focus();</script>";
    }
  }

  if (isset($sErroMsg)) {

    if (!empty($sErroMsg)) {
      db_msgbox($sErroMsg);
    }
  }
}

if (isset($sMsgDeclarate)) {
  db_msgbox($sMsgDeclarate);
}
?>