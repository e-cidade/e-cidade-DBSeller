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

require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_utils.php");
require_once  modification("libs/db_app.utils.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("dbforms/db_funcoes.php");

db_app::import('exceptions.*');

db_postmemory($HTTP_POST_VARS);

$cldb_config            = db_utils::getDao("db_config");
$clCadBan               = db_utils::getDao("cadban");
$clDisArq               = db_utils::getDao("disarq");
$clDisBanco             = db_utils::getDao("disbanco");
$clDisBancoTXT          = db_utils::getDao("disbancotxt");
$clDisBancoTXTReg       = db_utils::getDao("disbancotxtreg");
$oDaoNumpref            = db_utils::getDao("numpref");

define('MENSAGENS', 'tributario.arrecadacao.cai4_baixabanco001.');

$situacao      = "";
$sMd5Arquivo   = null;

/**
 * Variável de Debug da rotina
 */
$lDebugAtivo   = false;
$iInstitSessao = db_getsession("DB_instit");

$result        = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc, db21_codcli"));
db_fieldsmemory($result, 0);

/**
 * Busca parametros da numpref
 */
$sCamposParametrosNumpref = "k03_agrupadorarquivotxtbaixabanco, k03_pgtoparcial";
$sSqlParametrosNumpref    = $oDaoNumpref->sql_query_file(db_getsession("DB_anousu"), db_getsession("DB_instit"), $sCamposParametrosNumpref);
$rsParametrosNumpref      = $oDaoNumpref->sql_record($sSqlParametrosNumpref);
$oDadosParametrosNumpref  = db_utils::fieldsMemory($rsParametrosNumpref, 0);

/**
 * Função responsavel por controlar a taxa bancaria
 */
function geraTaxaBancaria( $oParametros ){

  $oTabDescCadBan = db_utils::getDao("tabdesccadban");
  $oTabDesc       = db_utils::getDao("tabdesc");
  $oArreNumCgm    = db_utils::getDao("arrenumcgm");
  $oArreMatric    = db_utils::getDao("arrematric");
  $oArreInscr     = db_utils::getDao("arreinscr");
  $oDisBanco      = db_utils::getDao("disbanco");

  require_once  modification("model/recibo.model.php");

  $nVlrTaxaBancaria = 0;
  $iCodigoHistCalc  = 507;

  /*
 * Verificamos se existe taxa específica configurada para o banco e agencia
 * Não pode haver mais de uma taxa configurada para o mesmo banco e agencia
 * Caso a data de validade esteja setada a mesma deve ser respeitada
 */
////  $sWhere  = "     k15_codbco = {$oParametros->k15_codbco}   ";
////  $sWhere .= " and k15_codage = '{$oParametros->k15_codage}' ";
////  $sWhere .= " and (k07_dtval is null or k07_dtval > '".date("Y-m-d", db_getsession("DB_datausu"))."')";
  $sWhere = " codsubrec = 51";
  $sSqlTaxaBancaria    = $oTabDesc->sql_query(null, "*", null, $sWhere);

  $rsTaxaBancaria      = $oTabDesc->sql_record($sSqlTaxaBancaria);

  $iLinhasTaxaBancaria = $oTabDesc->numrows;

  /**
   * Não pode haver mais de uma taxa configurada para o mesmo banco e agencia
   */
  if($iLinhasTaxaBancaria > 1){
    throw new DBException( _M( MENSAGENS . "taxa_especifica_duplicada" ) );
  }

  if ($iLinhasTaxaBancaria > 0) {

    $oDadosTaxaBancaria = db_utils::fieldsMemory($rsTaxaBancaria, 0);
    $nVlrTaxaBancaria   = $oDadosTaxaBancaria->k07_valorf;

    /*
 * Verificamos se o pagamento parcial está ativado
 * Caso esteja ativado, será gerado um recibo avulso para a taxa bancaria e este valor será classificado
 */
    if ( $oParametros->k03_pgtoparcial == "t" ) {

      $rsCgmRecibo = $oArreNumCgm->sql_record($oArreNumCgm->sql_query_file(null, $oParametros->numpre));
      if ($oArreNumCgm->numrows > 0) {

        $oCgmRecibo = db_utils::getColectionByRecord($rsCgmRecibo);
        $iCgmRecibo = $oCgmRecibo[0]->k00_numcgm;

      } else {

        $sSqlNumCgm  = " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from arrecad                                    ";
        $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from arrecant                                   ";
        $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from arreold                                    ";
        $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from arreforo                                   ";
        $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k30_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from arreprescr                                 ";
        $sSqlNumCgm .= "   where k30_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from recibo                                     ";
        $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
        $sSqlNumCgm .= "   union                                            ";
        $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
        $sSqlNumCgm .= "    from recibopaga                                 ";
        $sSqlNumCgm .= "   where k00_numnov = $oParametros->numpre limit 1) ";
        $rsNumCgm    = $oArreNumCgm->sql_record($sSqlNumCgm);
        if ($oArreNumCgm->numrows > 0) {
          $iCgmRecibo = db_utils::fieldsMemory($rsNumCgm, 0)->numcgm;
        }

      }

      /**
       * Caso o cgm seja nulo o nao deve ser gerado o recibo avulso.
       *  - Este caso apenas ocorrera quando nao encontrar os dados do numpre
       *  - O parametro de pagamento parcial estiver ativo
       *  - Tiver taxa especifica configurada para taxa bancaria
       *  - E o cliente não for Araruama / RJ
       */
      if(empty($iCgmRecibo)){
        return 0;
      }

      $oRecibo = new Recibo(1, $iCgmRecibo);
      $oRecibo->adicionarReceita($oDadosTaxaBancaria->k07_codigo,
        $oDadosTaxaBancaria->k07_valorf,
        $oDadosTaxaBancaria->codsubrec);
      $oRecibo->setCodigoHistorico($iCodigoHistCalc);
      $sMsgHistorico = _M( MENSAGENS . 'historico_recibo_taxa_bancaria',  $oParametros);
      $oRecibo->setHistorico($sMsgHistorico);

      /*
 * Vinculação do recibo com CGM, Matricula e Inscrição
 */
      foreach($oCgmRecibo as $oCgm) {
        $oRecibo->setVinculoCgm($oCgm->k00_numcgm);
      }

      $rsMatriculaRecibo = $oArreMatric->sql_record($oArreMatric->sql_query_file($oParametros->numpre));
      if ($oArreMatric->numrows > 0) {

        for ($iIndMatricula = 0; $iIndMatricula < $oArreMatric->numrows; $iIndMatricula++) {
          $oRecibo->setMatricula(db_utils::fieldsMemory($rsMatriculaRecibo, $iIndMatricula)->k00_matric);
        }
      }

      $rsInscricaoRecibo = $oArreInscr->sql_record($oArreInscr->sql_query_file($oParametros->numpre));
      if ($oArreInscr->numrows > 0) {

        for($iIndInscricao = 0; $iIndInscricao < $oArreInscr->numrows; $iIndInscricao++) {
          $oRecibo->setInscricao(db_utils::fieldsMemory($rsInscricaoRecibo, $iIndInscricao)->k00_inscr);
        }
      }
      /*
 * Fim das vinculações
 */

      $oRecibo->emiteRecibo();

      /**
       * Pega numpre do recibo gerado e insere na disbanco
       *
       */
      $iNumpreReciboTaxaBancaria = $oRecibo->getNumpreRecibo();

      /**
       * Insere o recibo na disbanco
       */
      $oDisBanco->codret     = $oParametros->codret;
      $oDisBanco->k15_codbco = $oParametros->k15_codbco;
      $oDisBanco->k15_codage = $oParametros->k15_codage;
      $oDisBanco->dtarq      = $oParametros->dtarq;
      $oDisBanco->dtpago     = $oParametros->dtpago;
      $oDisBanco->dtcredito  = $oParametros->dtcredito;
      $oDisBanco->vlrpago    = $oDadosTaxaBancaria->k07_valorf;
      $oDisBanco->vlrcalc    = $oDadosTaxaBancaria->k07_valorf;
      $oDisBanco->vlrtot     = $oDadosTaxaBancaria->k07_valorf;
      $oDisBanco->classi     = "false";
      $oDisBanco->k00_numpre = $iNumpreReciboTaxaBancaria;
      $oDisBanco->k00_numpar = "0";
      $oDisBanco->instit     = db_getsession("DB_instit");
      $oDisBanco->incluir(null);
      if ($oDisBanco->erro_status == "0") {
        throw new DBException($oDisBanco->erro_msg);
      }

    }

  }

  return $nVlrTaxaBancaria;

}

/**
 * Função que controla variável de sessao da trigger da disbanco
 * permitindo ou nao a insercao de numpre's de outras intituicoes
 * @param  boolean $lPermitir
 * @return void
 */
function permiteNumpreOutraInstituicao($lPermitir = false)
{

    $sSql = "select fc_putsession('DB_permiteNumpreOutraInstituicao', '{$lPermitir}')";
    db_query($sSql);
}

/**
 * Função responsavel por controlar honorarios
 */
function geraHonorarios( $oParametros ) {

  $oTabDescCadBan = db_utils::getDao("tabdesccadban");
  $oArreNumCgm    = db_utils::getDao("arrenumcgm");
  $oArreMatric    = db_utils::getDao("arrematric");
  $oArreInscr     = db_utils::getDao("arreinscr");
  $oDisBanco      = db_utils::getDao("disbanco");

  require_once  modification("model/recibo.model.php");

  $nVlrHonorarios = $oParametros->valorhonorarios;
  $iCodigoHistCalc  = 215;

  $oDadosTaxaBancaria = db_utils::fieldsMemory($rsTaxaBancaria, 0);
  $nVlrTaxaBancaria   = $oDadosTaxaBancaria->k07_valorf;

  $rsCgmRecibo = $oArreNumCgm->sql_record($oArreNumCgm->sql_query_file(null, $oParametros->numpre));
  if ($oArreNumCgm->numrows > 0) {

    $oCgmRecibo = db_utils::getColectionByRecord($rsCgmRecibo);
    $iCgmRecibo = $oCgmRecibo[0]->k00_numcgm;

  } else {

    $sSqlNumCgm  = " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from arrecad                                    ";
    $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from arrecant                                   ";
    $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from arreold                                    ";
    $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from arreforo                                   ";
    $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k30_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from arreprescr                                 ";
    $sSqlNumCgm .= "   where k30_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from recibo                                     ";
    $sSqlNumCgm .= "   where k00_numpre = $oParametros->numpre limit 1) ";
    $sSqlNumCgm .= "   union                                            ";
    $sSqlNumCgm .= " (select k00_numcgm as numcgm                       ";
    $sSqlNumCgm .= "    from recibopaga                                 ";
    $sSqlNumCgm .= "   where k00_numnov = $oParametros->numpre limit 1) ";
    $rsNumCgm    = $oArreNumCgm->sql_record($sSqlNumCgm);
    if ($oArreNumCgm->numrows > 0) {
      $iCgmRecibo = db_utils::fieldsMemory($rsNumCgm, 0)->numcgm;
    }

  }

  /**
   * Caso o cgm seja nulo o nao deve ser gerado o recibo avulso.
   *  - Este caso apenas ocorrera quando nao encontrar os dados do numpre
   *  - O parametro de pagamento parcial estiver ativo
   *  - Tiver taxa especifica configurada para taxa bancaria
   *  - E o cliente não for Araruama / RJ
   */
  if(empty($iCgmRecibo)){
    return 0;
  }

  $oRecibo = new Recibo(1, $iCgmRecibo);
  $oRecibo->adicionarReceita(517,
    $nVlrHonorarios,
    0);
  $oRecibo->setCodigoHistorico($iCodigoHistCalc);
  $sMsgHistorico = "Recibo avulso referente aos honorarios do pagamento do numpre $numpre";
  $oRecibo->setHistorico($sMsgHistorico);

  /*
   * Vinculação do recibo com CGM, Matricula e Inscrição
   */
  foreach($oCgmRecibo as $oCgm) {
    $oRecibo->setVinculoCgm($oCgm->k00_numcgm);
  }

  $rsMatriculaRecibo = $oArreMatric->sql_record($oArreMatric->sql_query_file($oParametros->numpre));
  if ($oArreMatric->numrows > 0) {

    for ($iIndMatricula = 0; $iIndMatricula < $oArreMatric->numrows; $iIndMatricula++) {
      $oRecibo->setMatricula(db_utils::fieldsMemory($rsMatriculaRecibo, $iIndMatricula)->k00_matric);
    }
  }

  $rsInscricaoRecibo = $oArreInscr->sql_record($oArreInscr->sql_query_file($oParametros->numpre));
  if ($oArreInscr->numrows > 0) {

    for($iIndInscricao = 0; $iIndInscricao < $oArreInscr->numrows; $iIndInscricao++) {
      $oRecibo->setInscricao(db_utils::fieldsMemory($rsInscricaoRecibo, $iIndInscricao)->k00_inscr);
    }
  }
  /*
   * Fim das vinculações
   */

  $oRecibo->emiteRecibo();

  /**
   * Pega numpre do recibo gerado e insere na disbanco
   *
   */
  $iNumpreRecibo = $oRecibo->getNumpreRecibo();

  /**
   * Insere o recibo na disbanco
   */
  $oDisBanco->codret     = $oParametros->codret;
  $oDisBanco->k15_codbco = $oParametros->k15_codbco;
  $oDisBanco->k15_codage = $oParametros->k15_codage;
  $oDisBanco->dtarq      = $oParametros->dtarq;
  $oDisBanco->dtpago     = $oParametros->dtpago;
  $oDisBanco->dtcredito  = $oParametros->dtcredito;
  $oDisBanco->vlrpago    = $nVlrHonorarios;
  $oDisBanco->vlrcalc    = $nVlrHonorarios;
  $oDisBanco->vlrtot     = $nVlrHonorarios;
  $oDisBanco->classi     = "false";
  $oDisBanco->k00_numpre = $iNumpreRecibo;
  $oDisBanco->k00_numpar = "0";
  $oDisBanco->instit     = db_getsession("DB_instit");
  $oDisBanco->incluir(null);
  if ($oDisBanco->erro_status == "0") {
    throw new DBException($oDisBanco->erro_msg);
  }

  return $nVlrHonorarios;

}

/**
 * Testa se cliente MARICA inclui fonte específico
 */
if ( $db21_codcli == 19985 ) {

  if( isset($k15_codbco) && isset($k15_codage) ){

    if( !($k15_codbco == 104 && $k15_codage == '1244F') ){

      require_once  modification("cai4_baixabanco001_codcli_19985.php");
      exit;
    }
  }
}

if (isset ($processar)) {

  try {

    /**
     * Recebe codbco,codage,tamanho
     * Verifica banco e agencia
     */
    db_postmemory($_FILES["arqret"]);
    $arq_name    = basename($name);
    $arq_type    = $type;
    $arq_tmpname = basename($tmp_name);
    $arq_size    = $size;
    $arq_array   = file($tmp_name);

    $sMd5Arquivo = md5(file_get_contents($tmp_name));
    db_putsession('disarq_md5', $sMd5Arquivo);

    /**
     * Verifica se arquivo já foi importado
     */
    $sSqlArquivoImportado = $clDisArq->sql_query_file(null, 'true', null, "md5 = '$sMd5Arquivo'");
    $rsArquivoImportado   = $clDisArq->sql_record($sSqlArquivoImportado);

    if ($clDisArq->numrows > 0) {
      throw new BusinessException( _M( MENSAGENS . "arquivo_importado" ) );
    }

    system("cp -f ".$tmp_name." ".ECIDADE_PATH."/tmp/", $intret);

    if($intret != 0) {

      $oParametrosMsg               = new stdClass();
      $oParametrosMsg->sNomeArquivo = $tmp_name;
      $sMsg                         = _M( MENSAGENS . 'erro_ao_copiar_arquivo', $oParametrosMsg);
      throw new Exception($sMsg);
    }

    $sWhere     = "     k15_codbco = {$k15_codbco}    ";
    $sWhere    .= " and k15_codage = '{$k15_codage}'  ";
    $sWhere    .= " and k15_instit = {$iInstitSessao} ";
    $sSqlCadBan = $clCadBan->sql_query(null,"*",null,$sWhere);
    $rsCadBan   = $clCadBan->sql_record($sSqlCadBan);

    if ($clCadBan->numrows == 0) {
      throw new Exception( _M( MENSAGENS . "banco_agencia_nao_encontrados" ) );
    }

    db_fieldsmemory($rsCadBan, 0);

    $_tamanprilinha = $arq_array[0];
    $atipo          = substr($arq_array[0], 0, 3);
    $totalproc      = sizeof($arq_array) - 2;
    $priregistro    = 1;
    $acodbco        = substr($arq_array[0], substr($k15_posbco, 0, 3), substr($k15_posbco, 3, 3));

    if ($cgc == '88073291000199') { // bage

      if (substr($arq_name, 0, 4) == "daeb") {

        if (substr($arq_array[0], 0, 3) == "826") {

          $_tamanprilinha = str_repeat(" ", $k15_taman);
          $atipo          = "XXX";
          $totalproc      = sizeof($arq_array);
          $priregistro    = 0;
          $acodbco        = 999;
        }
      }
    }

    $k15_codbco = (int) $k15_codbco;
    $acodbco    = (int) $acodbco;

    if ( strlen($_tamanprilinha) != $k15_taman ) {

      $oParametrosMsg                   = new stdClass();
      $oParametrosMsg->sTamanhoRegistro = strlen($arq_array[0]);
      $oParametrosMsg->sSistema          = $k15_taman;
      $sMsg                             = _M( MENSAGENS . 'tamanho_sistema_invalido', $oParametrosMsg);
      throw new Exception($sMsg);
    }

    if ($k15_codbco != $acodbco && $atipo != "BSJ") {

      $oParametrosMsg                   = new stdClass();
      $oParametrosMsg->sBancoDigitado   = $k15_codbco;
      $oParametrosMsg->sArquivoCodBanco = $acodbco;
      $sMsg                             = _M( MENSAGENS . 'banco_digitado_nao_confere', $oParametrosMsg);
      throw new Exception($sMsg);
    }

    $situacao = 1;
    $sCampos  = "codret     as codretexiste,   ";
    $sCampos .= "k15_codbco as bancoexiste,    ";
    $sCampos .= "k15_codage as agenciaexiste,  ";
    $sCampos .= "dtarquivo  as dtarquivoexiste ";
    $rsDisArq = $clDisArq->sql_record($clDisArq->sql_query_file(null,
      $sCampos,
      null,
      "md5 = '{$sMd5Arquivo}' and instit = $iInstitSessao"));
    
    if ($clDisArq->numrows > 0) {
      db_fieldsmemory($rsDisArq, 0);
    }

    $totalvalorpago = 0;

    for ($i = $priregistro; $i <= $totalproc - ($priregistro == 0 ? 1 : 0); $i ++) {

      /**
       * Grava arquivo disbanco
       */
      if ($k15_taman == 242) {

        //Acerto 1/2 para arapiraca
        $totalproc = sizeof($arq_array) - 3;
        if (substr($arq_array[$i], 7, 1) != '3' || substr($arq_array[$i], 13, 1) != 'U') {
          continue;
        }

      } elseif ($k15_taman == 402) {

        if (substr($arq_array[$i], 0, 1) == '9') {
          continue;
        }
      } elseif ($k15_taman == 90) {

        if (substr($arq_array[$i], 0, 5) <> 'BSJI2') {
          continue;
        }
      } elseif (substr($arq_array[$i], 0, 1) <> "G") {

        if (substr($arq_array[$i], 0, 3) <> "104" && substr($arq_array[$i], 0, 3) <> "BSJ") {
          continue;
        }
      }

      if (substr($arq_array[$i], 0, 4) == 'BSJI') {

        if (substr($arq_array[0], 0, 5) == 'BSJI0' && $i == 1) {

          if (substr($k15_plano, 3, 3) == '002') {
            $dtarq = '20'.substr($arq_array[0], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
          } else {
            $dtarq = substr($arq_array[0], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
          }

          $dtarq .= "-".substr($arq_array[0], substr($k15_plmes, 0, 3) - 1, substr($k15_plmes, 3, 3));
          $dtarq .= "-".substr($arq_array[0], substr($k15_poslan, 0, 3) - 1, substr($k15_poslan, 3, 3));
        }

      } else {

        if (substr($k15_plano, 3, 3) == '002') {
          $dtarq = '20'.substr($arq_array[$i], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        } else {
          $dtarq = substr($arq_array[$i], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        }

        $dtarq .= "-".substr($arq_array[$i], substr($k15_plmes, 0, 3) - 1, substr($k15_plmes, 3, 3));
        $dtarq .= "-".substr($arq_array[$i], substr($k15_poslan, 0, 3) - 1, substr($k15_poslan, 3, 3));

      }

      if (substr($k15_ppano, 3, 3) == '002') {
        $dtpago = '20'.substr($arq_array[$i], substr($k15_ppano, 0, 3) - 1, substr($k15_ppano, 3, 3));
      } else {
        $dtpago = substr($arq_array[$i], substr($k15_ppano, 0, 3) - 1, substr($k15_ppano, 3, 3));
      }

      $dtpago .= "-".substr($arq_array[$i], substr($k15_ppmes , 0, 3) - 1, substr($k15_ppmes, 3, 3));
      $dtpago .= "-".substr($arq_array[$i], substr($k15_pospag, 0, 3) - 1, substr($k15_pospag, 3, 3));

      if ($dtpago == '0000-00-00') {
        $dtpago = $dtarquivo;
        $dtarq  = $dtarquivo;
      }

      if (substr($k15_anocredito, 3, 3) == '002') {
        $dtcredito = '20'.substr($arq_array[$i], substr($k15_anocredito, 0, 3) - 1, substr($k15_anocredito, 3, 3));
      } else {
        $dtcredito = substr($arq_array[$i], substr($k15_anocredito, 0, 3) - 1, substr($k15_anocredito, 3, 3));
      }

      $dtcredito .= "-".substr($arq_array[$i], substr($k15_mescredito, 0, 3) - 1, substr($k15_mescredito, 3, 3));
      $dtcredito .= "-".substr($arq_array[$i], substr($k15_diacredito, 0, 3) - 1, substr($k15_diacredito, 3, 3));

      if (empty($dtcredito) || $dtcredito == '0000-00-00') {
        $dtcredito = $dtpago;
      }

      $vlrpago  = (substr($arq_array[$i], substr($k15_posvlr, 0, 3) - 1, substr($k15_posvlr, 3, 3)) / 100) + 0;
      $vlrjuros = (substr($arq_array[$i], substr($k15_posjur, 0, 3) - 1, substr($k15_posjur, 3, 3)) / 100) + 0;
      $vlrmulta = (substr($arq_array[$i], substr($k15_posmul, 0, 3) - 1, substr($k15_posmul, 3, 3)) / 100) + 0;
      $vlracres = (substr($arq_array[$i], substr($k15_posacr, 0, 3) - 1, substr($k15_posacr, 3, 3)) / 100) + 0;
      $vlrdesco = (substr($arq_array[$i], substr($k15_posdes, 0, 3) - 1, substr($k15_posdes, 3, 3)) / 100) + 0;
      $convenio =  substr($arq_array[$i], substr($k15_poscon, 0, 3) - 1, substr($k15_poscon, 3, 3));
      $cedente  =  substr($arq_array[$i], substr($k15_posced, 0, 3) - 1, substr($k15_posced, 3, 3));

      $totalvalorpago += $vlrpago;

    };

  } catch (Exception $oException) {
    db_msgbox("{$oException->getMessage()}");
  }
} else if (isset ($geradisbanco)) {

  $situacao = 2;
  $sWhere   = "    k15_codbco = {$k15_codbco}     ";
  $sWhere  .= " and k15_codage = '{$k15_codage}'  ";
  $sWhere  .= " and k15_instit = {$iInstitSessao} ";
  $rsCadBan = $clCadBan->sql_record($clCadBan->sql_query_file(null,"*",null,$sWhere));
  db_fieldsmemory($rsCadBan, 0);

  $arq_array = file(ECIDADE_PATH."/tmp/".$arqret);

  if ($arq_array == false) {

    $oParametrosMsg                = new stdClass();
    $oParametrosMsg->sDocumentRoot = ECIDADE_PATH;
    $sMsg                          = _M( MENSAGENS . 'erro_permissao_pasta', $oParametrosMsg);
    db_msgbox( $sMsg );
    exit;
  }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
  <?php
  db_app::load("scripts.js, strings.js, prototype.js, estilos.css");
  ?>
</head>
<body class="body-default" onLoad="a=1">
<?php

if ($situacao == "") {
  include modification ("forms/db_caiarq001.php");
} else if ($situacao == 1 && empty($codretexiste)) {
  include modification ("forms/db_caiarq002.php");
} else if ($situacao == 2) {
  include modification ("forms/db_caiarq003.php");
}
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>
<?php

if ($situacao == 1) {

  if (isset ($codretexiste) && $codretexiste != "") {

    $oParametrosMsg                   = new stdClass();
    $oParametrosMsg->sBancoExiste     = $bancoexiste;
    $oParametrosMsg->sAgenciaExiste   = $agenciaexiste;
    $oParametrosMsg->sDtArquivoExiste = db_formatar($dtarquivoexiste, 'd');
    $sMsg                             = _M( MENSAGENS . 'arquivo_ja_existe', $oParametrosMsg);

    echo "<script type=\"text/javascript\">
    alert('$sMsg');
    location.href='cai4_baixabanco001.php';
    </script>";
    flush();

  }
}

if ($situacao == 2) {

  echo "<script type=\"text/javascript\">
        function js_termometro(xvar){
         document.form1.processa.value = xvar;
        }
        </script>";

  flush();

  try {

    if ($lDebugAtivo == true) {
      echo "<br> tamanho: $k15_taman ...<br><br><br>";
    }

    /**
     * Grava arquivo disarq  
     */
    db_inicio_transacao();

    /**
     * Verifica se arquivo já foi importado
     */
    $disarqMd5 = db_getsession('disarq_md5');
    $sSqlArquivoImportado = $clDisArq->sql_query_file(null, 'true', null, "md5 = '$disarqMd5'");
    $rsArquivoImportado   = $clDisArq->sql_record($sSqlArquivoImportado);
    
    if ($clDisArq->numrows > 0) {
      throw new BusinessException( _M( MENSAGENS . "arquivo_importado" ) );
    }

    if (substr($k15_pdano, 3, 3) == '002') {
      $dtarquivo = '20'.substr($arq_array[0], substr($k15_pdano, 0, 3) - 1, substr($k15_pdano, 3, 3));
    } else {
      $dtarquivo = substr($arq_array[0], substr($k15_pdano, 0, 3) - 1, substr($k15_pdano, 3, 3));
    }

    $dtarquivo .= "-".substr($arq_array[0], substr($k15_pdmes, 0, 3) - 1, substr($k15_pdmes, 3, 3));
    $dtarquivo .= "-".substr($arq_array[0], substr($k15_posdta, 0, 3) - 1, substr($k15_posdta, 3, 3));

    if ($cgc == '88073291000199') { // bage
      if (substr($arqname, 0, 4) == "daeb") {

        if (substr($arq_array[0], 0, 3) == "826") {
          $dtarquivo = substr($arqname, 4, 8);
        }
      }
    }

    $clDisArq->id_usuario = db_getsession("DB_id_usuario");
    $clDisArq->k15_codbco = $k15_codbco;
    $clDisArq->k15_codage = $k15_codage;
    $clDisArq->arqret     = $arqname;
    $clDisArq->textoret   = "";
    $clDisArq->dtretorno  = date('Y-m-d', db_getsession("DB_datausu"));
    $clDisArq->dtarquivo  = $dtarquivo;
    $clDisArq->k00_conta  = $k15_conta;
    $clDisArq->autent     = "false";
    $clDisArq->instit     = $iInstitSessao;
    $clDisArq->md5        = db_getsession('disarq_md5');
    $clDisArq->incluir(null);

    if ($clDisArq->erro_status == "0") {
      $oParametrosMsg           = new stdClass();
      $oParametrosMsg->sMsgErro = $clDisArq->erro_msg;
      $sMsg                     = _M( MENSAGENS . 'erro_inclusao_disarq', $oParametrosMsg);
      throw new DBException($sMsg);
    }

    $codret         = $clDisArq->codret;

    $achou_arrecant = 0;

    $sWhereCadBan  = "     k15_codbco = {$k15_codbco}    ";
    $sWhereCadBan .= " and k15_codage = '{$k15_codage}'  ";
    $sWhereCadBan .= " and k15_instit = {$iInstitSessao} ";
    $sSqlCabBan    = $clCadBan->sql_query_file(null, "k15_seq, k15_poscon, k15_contat", null, $sWhereCadBan);
    $rsCadBan      = $clCadBan->sql_record($sSqlCabBan);

    db_fieldsmemory($rsCadBan, 0);

    $k15_numpreori = $k15_numpre;
    $k15_numparori = $k15_numpar;
    $priregistro = 1;

    if ($cgc == '88073291000199') { // bage
      if (substr($arqname, 0, 4) == "daeb") {
        $priregistro = 0;
      }
    }

    /**
     * Processa Registros do Arquivo para Gravar em DISBANCO
     */
    $passou_pelo_t  = true;
    $k15_numbco_ant = $k15_numbco;

    $total_tx_bancaria = 0;

    for ($i = $priregistro; $i <= $totalproc - ($priregistro == 0 ? 1 : 0); $i ++) {

      if ($lDebugAtivo == true) {
        echo "<br><br> INICIO DO IDRET <br><br>";
        echo substr($arq_array[$i], 0, 100) . "<br>";
        echo "i: $i - cgc: $cgc - passou_pelo_t: $passou_pelo_t<br/>";
      }

      $k15_numbco    = $k15_numbco_ant;
      $tipo_convenio = "";

      /**
       * Testa tipo do registro
       */
      if ($k15_taman == 242) {

        if (substr($arq_array[$i], 7, 1) != '3' && (substr($arq_array[$i], 13, 1) != 'U' && substr($arq_array[$i], 13, 1) != 'T')) {

          if ($lDebugAtivo == true) {
            echo "   continuando 1.00110011...<br/>";
          }

          continue;

        }

      } elseif ($k15_taman == 402) {

        if (substr($arq_array[$i], 0, 1) == '9') {
          continue;
        }

      } elseif ($k15_taman == 90) {

        if (substr($arq_array[$i], 0, 5) <> 'BSJI2') {
          continue;
        }
      } elseif (substr($arq_array[$i], 0, 1) <> "G") {

        if (substr($arq_array[$i], 0, 3) <> "104" && substr($arq_array[$i], 0, 3) <> "BSJ") {
          continue;
        }
      }

      $lArquivoArrecadacao = false;

      if ( $k15_taman == 152 ) {
        $lArquivoArrecadacao = true;
      }

      /**
       * Com registro
       */
      if ( $k15_codbco == 1 && ($k15_codage == "0728C" ||$k15_codage == "0404C")) {

        if ( substr($arq_array[$i],0,1) == "2" ) {

         //if ( trim(substr($arq_array[$i],41,30)) == "MUNICIPIO DE NITEROI" ) {

            if ($lDebugAtivo == true) {
              echo "vvvvvvvvvvvvvvvvvvvvv - continue: " . substr($arq_array[$i],41,30) . "<br><br><br>";
            }

            continue;

         //}

        } else if ( substr($arq_array[$i],0,1) == "7" && ( substr($arq_array[$i],108,2) == "05" || substr($arq_array[$i],108,2) == "06"  || substr($arq_array[$i],108,2) == "07" || substr($arq_array[$i],108,2) == "08") ) {

          $numbco = substr($arq_array[$i], 64, 17);

        } else {

          if ($lDebugAtivo == true) {
            echo "zzzzzzzzzzzzzzzzzzzzz - continue: " . substr($arq_array[$i],41,30) . "<br><br><br>";
          }

          continue;
        }
      }

      /**
       * Grava arquivo disbanco
       */
      $k15_numpre = $k15_numpreori;
      $k15_numpar = $k15_numparori;

      if (@$numpre == "") {
        $numpre = substr($arq_array[$i], substr($k15_numpre, 0, 3) - 1, substr($k15_numpre, 3, 3));
        $numpar = substr($arq_array[$i], substr($k15_numpar, 0, 3) - 1, substr($k15_numpar, 3, 3));
      }

      //   echo "numpre: $numpre - numpar: $numpar";

      // bage
      if ($cgc == '88073291000199') {

        if (substr($numpre, 0, 2) == "00") {

          if (substr($arqname, 0, 4) == "daeb") {

            $k15_numpre = "034008";
            $k15_numpar = "042003";
          } else {

            $k15_numpre = "071008";
            $k15_numpar = "079003";
          }
        }
      }

      // itaqui
      if ($cgc == '88120662000146') {

        if (substr($numpre, 0, 2) == "00") {

          $k15_numpre = "071008";
          $k15_numpar = "079003";
        }
      }

      // osorio
      if ($cgc == '88814181000130') {

        if (substr($numpre, 0, 2) == "00") {

          $k15_numpre = "052008";
          $k15_numpar = "060003";
        }
      }

      //arroio
      if ($cgc == '91103093000135') {

        // numpre do sistema novo
        if (substr($numpre, 0, 2) == "00") {

          $k15_numpre = "071008";
          $k15_numpar = "079003";
        }
      }

      // capivari
      if ($cgc == '01610503000141') {

        // numpre do sistema novo
        if (substr($numpre, 0, 2) == "00") {

          $k15_numpre = "071008";
          $k15_numpar = "079003";
        }
      }

      // dom feliciano
      if ($cgc == '88601943000110') {

        // numpre do sistema novo
        if ( (int) substr($numpre, 0, 4) == 0 ) {

          $k15_numpre = "071008";
          $k15_numpar = "079003";
          $k15_numbco = "";
        } else { // sistema antigo

          $teste_numpre = (int) substr($arq_array[$i], 65, 1);
          $xxx          = substr($arq_array[$i], 60, 10);

          if ( $teste_numpre == 0 ) {

            $k15_numpre = "070011";
            $k15_numpar = "079003";
          }

        }
      }

      // araruama/rj
      if ($cgc == '28531762000133' && $k15_contat != 'BDL') {

        $k15_seq  = "";
        $convenio = "";

        if ($k15_codbco != 399 || ($k15_codbco == 399 && strcmp(trim($k15_codage), "1252") == 0) ) {

          $numbco   = "";

          // verificar se numpre eh do nosso sistema ou do anterior, posicao 65,5
          $isNumpre = (substr($arq_array[$i], 64, 5) === "00000");

          // Se nao eh numpre do e-cidade...
          if (!$isNumpre) {

            $iTipo = (int)substr($arq_array[$i], 64, 3);

            // Verifica se eh DAM do sistema anterior
            $isDam = ($iTipo == 803);

            if ($isDam) {

              $k15_numpre = "068014";
              $k15_numbco = $k15_numpre;

              $numbco   = substr($arq_array[$i], substr($k15_numbco, 0, 3) - 1, substr($k15_numbco, 3, 3));
              $k15_seq  = "";
              $convenio = "";

              /**
               * DAM do sistema anterior $numbco
               */
            } else {

              /* Verificar REFIS em funcao da parcela com 3 digitos */
              if ($iTipo >= 201 && $iTipo <= 380) {

                $k15_numpre = "072008";

                $numpre = substr($arq_array[$i], substr($k15_numpre, 0, 3) - 1, substr($k15_numpre, 3, 3));
                $numpar = (200 - $iTipo);

              } else {

                $k15_numpre = "072008";
                $k15_numpar = "080002";

                $numpre = substr($arq_array[$i], substr($k15_numpre, 0, 3) - 1, substr($k15_numpre, 3, 3));
                $numpar = substr($arq_array[$i], substr($k15_numpar, 0, 3) - 1, substr($k15_numpar, 3, 3));
              }

            }

          }

        }

      } // fim araruama/rj

      if ($cgc == '12198693000158') {

        // arapiraca
        if (substr($arq_array[$i], 13, 1) == 'T') {

          if($k15_codage == '542-8' && substr($numpre,0,3) != '000'){

            if ($lDebugAtivo == true) {
              echo "   continuando 1.001 === 542-8...<br/>";
            }

            $passou_pelo_t = false;

          }

        }

      }

      // coruripe sigcb
      if ($cgc == '12264230000147') {

        if (substr($arq_array[$i], 13, 1) == 'T') {

          if ($lDebugAtivo == true) {
            echo "   continuando 1.002001 === numpre: $numpre...<br/>";
          }

          if($k15_codage == '2117S' && substr($numpre,0,3) != '000') {

            if ($lDebugAtivo == true) {
              echo "   continuando 1.002002 === 2117S...<br/>";
            }

            /**
             * Necessario para arapiraca sigcb/sicob
             */
          }
        }
      }

      /**
       * Duplicado if de arapiraca para que o sistema consiga baixar debitos
       *   com convenio do tipo sicob pois atualmente ja funciona em arapiraca
       *   TAREFA 36779
       */
      // sapiranga
      if ($cgc == '87366159000102') {

        // sapiranga
        if (substr($arq_array[$i], 13, 1) == 'T' || substr($arq_array[$i], 13, 1) == 'U') {

          if ($lDebugAtivo == true) {
            echo "      continuando 1.1 - k15_codage: $k15_codage<br/>";
          }

          if($k15_codage == '00514' && $k15_codbco != '104') {
            $tipo_convenio = "sigcb";
            $passou_pelo_t = false;
          }

        }

      }

      // SIGCB - k15_contat
      if ($k15_contat == 'BDL' || $k15_contat == 'SIGCB') {

        if (substr($arq_array[$i], 13, 1) == 'T' || substr($arq_array[$i], 13, 1) == 'U') {

          $tipo_convenio = strtolower($k15_contat);
          $passou_pelo_t = false;
        }

        if (substr($arq_array[$i], 13, 1) == 'U' && substr($arq_array[$i],15,2) == '50' ) {

          $numpre = "";
          $numpar = "";
          continue;
        }

      }

      // itaqui sigcb
      if ($cgc == '88120662000146') {

        if (substr($arq_array[$i], 13, 1) == 'T' || substr($arq_array[$i], 13, 1) == 'U') {

          if ($lDebugAtivo == true) {
            echo "      continuando 1.2 - k15_codage: $k15_codage<br/>";
            echo "   continuando 1.003003 === ITAQUI ( 104/0484X )... - tipo_convenio: $tipo_convenio<br/>";
          }

          if($k15_codage == '0484X' && substr($numpre,0,3) != '000'){
            $tipo_convenio = "sigcb";
          }
        }
      }

      // arapiraca sigcb
      if ($cgc == '12198693000158') {

        if ($lDebugAtivo == true) {
          echo "      continuando 1.3<br/>";
        }

        if ( substr($arq_array[$i], 13, 1) == 'T' || substr($arq_array[$i], 13, 1) == 'U' ) {

          if ($lDebugAtivo == true) {
            echo "      continuando 1.4 - k15_codage: $k15_codage<br/>";
          }

          if ($k15_codage == '0056') {

            if ($lDebugAtivo == true) {
              echo "      passando para false 1... numpre: $numpre<br/>";
            }
            $tipo_convenio = "sigcb";
          }
        }
      }

      // coruripe sigcb
      if ($cgc == '12264230000147') {

        if ($lDebugAtivo == true) {
          echo "      continuando 1.5<br/>";
        }

        if ( substr($arq_array[$i], 13, 1) == 'T' || substr($arq_array[$i], 13, 1) == 'U' ) {

          if ($lDebugAtivo == true) {
            echo "      continuando 1.6 - k15_codage: $k15_codage<br/>";
          }

          if ($k15_codage == '2117S') {

            if ($lDebugAtivo == true) {
              echo "      passando para false 1... numpre: $numpre<br/>";
            }
            $tipo_convenio = "sigcb";
          }
        }
      }





//      echo ( "x: $i === " . substr ( substr($arq_array[$i], 64, 17 ), 0, 17) . "<br>" );

      // Niteroi
      if ( $cgc == '28521748000159' and $k15_taman == 152 ) {

        // numpre do sistema novo
        if ( substr ( substr($arq_array[$i], substr($k15_numbco, 0, 3) - 1, substr($k15_numbco, 3, 3)), 0, 6) == '000000' ) {
          $k15_numpre = "071008";
          $k15_numpar = "079003";
          $k15_numbco = "";
        }

      }

      if ($k15_taman == 242) {

        if (substr($arq_array[$i], 13, 1) == 'T') {

          $numbco   = substr($arq_array[$i], substr($k15_numbco, 0, 3) - 1, substr($k15_numbco, 3, 3));
          $convenio = substr($arq_array[$i], substr($k15_poscon, 0, 3) - 1, substr($k15_poscon, 3, 3));
          if ($lDebugAtivo == true) {
            echo "   continuando 5...<br/>";
          }
          continue;
        }

      } else {

        $convenio = substr($arq_array[$i], substr($k15_poscon, 0, 3) - 1, substr($k15_poscon, 3, 3));
        $numbco   = substr($arq_array[$i], substr($k15_numbco, 0, 3) - 1, substr($k15_numbco, 3, 3));

        $numbco   = str_replace(" ","0",$numbco);

        if ($lDebugAtivo == true) {
          echo "<br><br>      AAAAAAAAAAAAAAAAAAAAAAAAAA === $numbco " . strlen($numbco) . " <br><br>";
        }

      }
    
      // dom feliciano
      if ($cgc == '88601943000110') {

        // numpre do sistema antigo
        if ( (int) substr($numpre, 0, 4) != 0 ) {

          $teste_numpre = (int) substr($arq_array[$i], 65, 1);

          if ( $teste_numpre == 0 ) {
            $k15_numbco = "071011";
            $numbco     = substr($arq_array[$i], substr($k15_numbco, 0, 3) - 1, substr($k15_numbco, 3, 3));
          }
        }
      }

      if ($lDebugAtivo == true) {
        echo "   continuando 6...<br/>";
      }

      if ($numbco != "") {

        if ($lDebugAtivo == true) {
          echo "   continuando 6.1...<br/>";
        }

        $numbco = $k15_seq.$convenio.$numbco;

        $sSqlBuscaArrebanco  = "select arrebanco.k00_numpre as numpre,                                       ";
        $sSqlBuscaArrebanco .= "       arrebanco.k00_numpar as numpar                                        ";
        $sSqlBuscaArrebanco .= "  from arrebanco                                                             ";
        $sSqlBuscaArrebanco .= "       inner join arreinstit on arreinstit.k00_numpre = arrebanco.k00_numpre ";
        $sSqlBuscaArrebanco .= " where arrebanco.k00_numbco  = trim('".trim($numbco)."')                     ";
        $sSqlBuscaArrebanco .= "   and arreinstit.k00_instit = $iInstitSessao                                ";
        $sSqlBuscaArrebanco .= " and ( case when ( select count(*) from recibopaga a where a.k00_numnov = arrebanco.k00_numpre ) > 0 then ( select count(*) from recibopaga a left join arrecad b on a.k00_numpre = b.k00_numpre and a.k00_numpar = b.k00_numpar and a.k00_receit = b.k00_receit where ( ( a.k00_hist not in ( 400, 401, 918 ) ) and ( not ( a.k00_receit = 411 and a.k00_valor = 2.90 ) ) and ( not ( a.k00_receit = 411 and a.k00_valor = 3.10 ) ) ) and a.k00_numnov = arrebanco.k00_numpre and b.k00_numpre is null ) = 0 else true end ) ";

        $sSqlBuscaArrebanco  = "select arrebanco.k00_numpre as numpre,                                       ";
        $sSqlBuscaArrebanco .= "       arrebanco.k00_numpar as numpar                                        ";
        $sSqlBuscaArrebanco .= "  from arrebanco                                                             ";
        $sSqlBuscaArrebanco .= "       inner join arreinstit on arreinstit.k00_numpre = arrebanco.k00_numpre ";
        $sSqlBuscaArrebanco .= " where arrebanco.k00_numbco  = trim('".trim($numbco)."')                     ";
        $sSqlBuscaArrebanco .= "   and arreinstit.k00_instit = $iInstitSessao                                ";

        if ($lDebugAtivo == true) {
          echo( $sSqlBuscaArrebanco . "<br>" );
        }

        $rsArrebanco = db_query($sSqlBuscaArrebanco);

        if (pg_num_rows($rsArrebanco) == 0) {

          $numpre = 0;
          $numpar = 0;

        } else  {
          db_fieldsmemory($rsArrebanco, 0);
          if ($lDebugAtivo == true) {
            echo "   continuando 6.3 - achou numpre: $numpre - numpar: $numpar ...<br/>";
          }
        }

      } else {

        if ($lDebugAtivo == true) {
          echo "   continuando 6.2...<br/>";
        }

        $processaposnumpre = true;

        // Arapiraca
        if ($numpre <> "" && $cgc=='12198693000158') {
          $processaposnumpre = false;
        }

        // Coruripe
        if ($numpre <> "" && $cgc=='12264230000147') {
          $processaposnumpre = false;
        }

        // Alegrete
        if ($numpre <> "" && $cgc=='87896874000157') {
          $processaposnumpre = false;
        }

        if ( $k15_contat == "BDL" || $k15_contat == "SIGCB" ) {
          $processaposnumpre = false;
        }

        /**
         * Duplicado if de arapiraca para que o sistema consiga baixar debitos
         *   com convenio do tipo sicob pois atualmente ja funciona em arapiraca
         *   TAREFA 36779
         */
        // Sapiranga
        if ($numpre <> "" && $cgc=='87366159000102') {
          $processaposnumpre = false;
        }

        if( $processaposnumpre == true ) {
          $numpre = substr($arq_array[$i], substr($k15_numpre, 0, 3) - 1, substr($k15_numpre, 3, 3));
          $numpar = substr($arq_array[$i], substr($k15_numpar, 0, 3) - 1, substr($k15_numpar, 3, 3));
        }

      }

      if ($lDebugAtivo == true) {
        echo "      continuando 7 - tipo_convenio: $tipo_convenio - numpre: $numpre - numpar: $numpar<br/>";
      }

      if ($k15_codage === "00110" && $k15_codbco == 41) {

        $sqlbanco    = "select arrebanco.k00_numpre, arrebanco.k00_numpar                            ";
        $sqlbanco   .= "  from arrebanco                                                             ";
        $sqlbanco   .= "       inner join arreinstit on arreinstit.k00_numpre = arrebanco.k00_numpre ";
        $sqlbanco   .= " where arrebanco.k00_numbco  = '".substr($arq_array[$i], ( (int) substr($k15_numbco, 0, 3) ) - 1, ( (int) substr($k15_numbco, 3, 3) ) )."'";
        $sqlbanco   .= "   and arreinstit.k00_instit = $iInstitSessao ";
        $resultbanco = db_query($sqlbanco) or die($sqlbanco);

        if (pg_num_rows($resultbanco) == 0) {

          $oParametrosMsg            = new stdClass();
          $oParametrosMsg->iNumBanco = substr($arq_array[$i], 6, 13);
          $sMsg                      = _M( MENSAGENS . 'erro_numbanco_nao_encontrado', $oParametrosMsg);
          echo "<script>alert('".$sMsg."');</script>";
        } else {

          db_fieldsmemory($resultbanco, 0, true);
          $numpre = $k00_numpre;
          $numpar = $k00_numpar;
        }

      } else if ($k15_codage === "00712" && $k15_codbco == 41 && $cgc === "01610503000141" ) {

        // Capivari
        $sqlbanco    = "select arrebanco.k00_numpre, arrebanco.k00_numpar                            ";
        $sqlbanco   .= "  from arrebanco                                                             ";
        $sqlbanco   .= "       inner join arreinstit on arreinstit.k00_numpre = arrebanco.k00_numpre ";
        $sqlbanco   .= " where arrebanco.k00_numbco  = '".substr($arq_array[$i], ( (int) substr($k15_numbco, 0, 3) ) - 1, ( (int) substr($k15_numbco, 3, 3) ) )."'";
        $sqlbanco   .= "   and arreinstit.k00_instit = $iInstitSessao ";
        $resultbanco = db_query($sqlbanco) or die($sqlbanco);

        if (pg_num_rows($resultbanco) == 0) {

          $oParametrosMsg            = new stdClass();
          $oParametrosMsg->iNumBanco = $numbco;
          $sMsg                      = _M( MENSAGENS . 'erro_arrebanco_numbanco_nao_encontrado', $oParametrosMsg);
          echo "<script>alert('".$sMsg."');</script>";
        } else {

          db_fieldsmemory($resultbanco, 0, true);
          $numpre = $k00_numpre;
          $numpar = $k00_numpar;
        }

      } elseif ($tipo_convenio == "sigcb" && $tipo_convenio == "bdl" ) {

        if ($lDebugAtivo == true) {
          echo "      continuando 8 ( numbco: $numbco )...<br/>";
        }

        $numbco = trim($numbco);

        if ($lDebugAtivo == true) {
          echo "         continuando 8.1 ( numbco: $numbco )...<br/>";
        }

        $sSqlArrebanco  = "select arrebanco.k00_numpre, arrebanco.k00_numpar                            ";
        $sSqlArrebanco .= "  from arrebanco                                                             ";
        $sSqlArrebanco .= "       inner join arreinstit on arreinstit.k00_numpre = arrebanco.k00_numpre ";
        $sSqlArrebanco .= " where arrebanco.k00_numbco = '$numbco'                                      ";
        $sSqlArrebanco .= "   and arreinstit.k00_instit = $iInstitSessao                                ";
        $rsArrebanco    = db_query($sSqlArrebanco);

        if (pg_num_rows($rsArrebanco) == 0) {

          $oParametrosMsg            = new stdClass();
          $oParametrosMsg->iNumBanco = $numbco;
          $sMsg                      = _M( MENSAGENS . 'erro_numbanco_nao_encontrado_instit', $oParametrosMsg);
          echo "<script>alert('".$sMsg."');</script>";
        } else {

          db_fieldsmemory($rsArrebanco, 0, true);
          $numpre = $k00_numpre;
          $numpar = $k00_numpar;
        }

        if ($lDebugAtivo == true) {
          echo "            continuando 8.2 ( numbco: $numbco )...<br/>";
        }

      }





      $vlrpago  = (substr($arq_array[$i], substr($k15_posvlr, 0, 3) - 1, substr($k15_posvlr, 3, 3)) / 100) + 0;

      if ($lDebugAtivo == true) {
        echo("   111 === i: $i - numbco: $numbco - numpre: $numpre - numpar: $numpar - vlrpago: $vlrpago - dtarquivo: $dtarquivo - dtarq: $dtarq <br><br>");
      }




      // Niteroi
      if ( $cgc == '28521748000159' and $k15_taman == 152 ) { // arrecadacao

        if ($lDebugAtivo == true) {
          echo "<br> A R R E C A D A C A O ...<br>";
        }

        if ( $numbco != "" and $numpre == 0 and $numpar == 0 ) {

          $iReceita = substr($numbco,0,3);

          $aReceitas = array();
          $aReceitas["701"] = array(701,"M");
          $aReceitas["702"] = array(702,"M");
          $aReceitas["302"] = array(302,"I");
          $aReceitas["901"] = array(321,"I");
          $aReceitas["706"] = array(706,"I");
          $aReceitas["510"] = array(411,"M");
          $aReceitas["809"] = array(611,"M");
          $aReceitas["846"] = array(752,"M");
          $aReceitas["897"] = array(806,"M");
          $aReceitas["905"] = array(517,"M");
          $aReceitas["898"] = array(807,"M");
          $aReceitas["505"] = array(406,"M");
          $aReceitas["515"] = array(416,"I");
          $aReceitas["101"] = array(101,"M");
          $aReceitas["523"] = array(423,"I");
          $aReceitas["904"] = array(516,"M");
          $aReceitas["704"] = array(704,"I");
          $aReceitas["896"] = array(805,"I");
          $aReceitas["498"] = array(400,"I");
          $aReceitas["499"] = array(401,"I");
          $aReceitas["893"] = array(797,"I");
          $aReceitas["509"] = array(410,"I");
          $aReceitas["506"] = array(407,"I");
          $aReceitas["705"] = array(705,"I");
          $aReceitas["703"] = array(703,"I");
          $aReceitas["891"] = array(795,"I");
          $aReceitas["330"] = array(316,"I");
          $aReceitas["548"] = array(434,"I");
          $aReceitas["817"] = array(741,"I");
          $aReceitas["817"] = array(741,"I");
          $aReceitas["807"] = array(609,"I");
          $aReceitas["538"] = array(441,"I");
          $aReceitas["708"] = array(707,"I");
          $aReceitas["507"] = array(408,"I");
          $aReceitas["902"] = array(322,"I");
          $aReceitas["011"] = array(1519,"I");
          $aReceitas["810"] = array(612,"I");
          $aReceitas["546"] = array(432,"I");
          $aReceitas["992"] = array(322,"I");
          $aReceitas["528"] = array(729,"I");
          $aReceitas["892"] = array(796,"I");
          $aReceitas["876"] = array(780,"I");
          $aReceitas["000"] = array(101,"M");
          $aReceitas["111"] = array(101,"M");
          $aReceitas["140"] = array(441,"M");
          $aReceitas["111"] = array(101,"I");
          $aReceitas["286"] = array(101,"M");
//           echo "iReceita: $iReceita<br>";
// ALTERAÇÃO DO RENAN

     if(substr($numbco,0,2) == '55'){
               continue;
           }

          if ( isset($aReceitas[$iReceita]) == true ) {

            $iReceitaNova = $aReceitas[$iReceita][0];


            $iOrigem = substr($numbco,5,7);
            if ( substr($numbco,3,3) == '999' or $iOrigem == '0000000' ) {
              $sTipo = "C";
            } else {
              $sTipo = $aReceitas[$iReceita][1];
            }

//             db_criatabela(pg_query("select * from cadtipo limit 3"));

            if ( $sTipo == "M" or $sTipo == "I" ) {
              $sCgm = "select j01_numcgm as cgm, 'M' as tipo_origem from iptubase where j01_matric = $iOrigem union select q02_numcgm as cgm, 'I' as tipo_origem from issbase where q02_inscr = $iOrigem";
            } else if ( $sTipo == "C" ) {
              $sCgm = "select 10 as cgm, 'C'::varchar as tipo_origem";
            } else {
              die("\n\n\nSEM TIPO DEFINIDO\n\n\n");
            }
            $rsNumCgm = db_query($sCgm) or die("errox: $sCgm");

            if ( pg_num_rows($rsNumCgm) == 0 ) {
              $iCgmRecibo = 10;
              $sTipoOrigem = "C";
            } else {
              $iCgmRecibo  = pg_fetch_result($rsNumCgm,0,0);
              $sTipoOrigem = pg_fetch_result($rsNumCgm,0,1);
            }

            $rsNumpre      = db_query("select coalesce ( max( k00_numpre ), 0) + 1 as k03_numpre from recibo where k00_numpre between 39000000 and 39999999");
            $iNumpreBaixa  = db_utils::fieldsMemory($rsNumpre, 0)->k03_numpre;

            $historico = "criado automatico pela baixa de banco - numbco: [$numbco]";

            if ( $iNumpreBaixa == 1 ) {
              $iNumpreBaixa = 39000001;
            }

            $sSql = "insert into recibo (k00_numcgm,
                                         k00_dtoper,
                                         k00_receit,
                                         k00_hist,
                                         k00_valor,
                                         k00_dtvenc,
                                         k00_numpre,
                                         k00_numpar,
                                         k00_numtot,
                                         k00_numdig,
                                         k00_tipo,
                                         k00_numnov,
                                         k00_codsubrec)
                                 values ($iCgmRecibo,
                                         '$dtarquivo',
                                         $iReceitaNova,
                                         311,
                                         $vlrpago,
                                         '$dtarquivo',
                                         $iNumpreBaixa,
                                         1,
                                         1,
                                         0,
                                         11,
                                         0,
                                         0)";
            $rsInsert = db_query($sSql) or die($sSql);

            $sRecurso = "insert into reciborecurso( k00_sequen,
                                                 k00_numpre,
                                                 k00_recurso)
                                        values ( nextval('reciborecurso_k00_sequen_seq'),
                                                 $iNumpreBaixa,
                                                 100)";
            $rsRecurso = db_query($sRecurso) or die($sRecurso);

            if ( $sTipoOrigem == "M" ) {
              $sArre = "insert into arrematric values ($iNumpreBaixa, $iOrigem)";
              $rsArre = db_query($sArre) or die($sArre);
            } else if ( $sTipoOrigem == "I" ) {
              $sArre = "insert into arreinscr values ($iNumpreBaixa, $iOrigem)";
              $rsArre = db_query($sArre) or die($sArre);
            }

            $sHist = "insert into arrehist (k00_numpre,
                                             k00_numpar,
                                             k00_hist,
                                             k00_dtoper,
                                             k00_hora,
                                             k00_id_usuario,
                                             k00_histtxt,
                                             k00_limithist,
                                             k00_idhist 
                                           ) values (
                                             $iNumpreBaixa,
                                             1,
                                             311,
                                             '$dtarquivo',
                                             '00:00',
                                             1,
                                             '$historico',
                                             null,
                                             nextval('arrehist_k00_idhist_seq')
                                           )";
            $rsHist = db_query($sHist) or die($sHist);

            $numbco = "";
            $numpre = $iNumpreBaixa;
            $numpar = 1;

//              echo("   222 === i: $i - numbco: $numbco - numpre: $numpre - numpar: $numpar <br>");

          } else {
            echo("   333 === i: $i - numbco: $numbco - numpre: $numpre - numpar: $numpar - receita: $iReceita <br>");
            exit;
          }

        }

      }

      if (substr($arq_array[0], 0, 5) == 'BSJI0') {

        if (substr($k15_plano, 3, 3) == '002') {
          $dtarq = '20'.substr($arq_array[0], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        } else {
          $dtarq = substr($arq_array[0], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        }

        $dtarq .= "-".substr($arq_array[0], substr($k15_plmes, 0, 3) - 1, substr($k15_plmes, 3, 3));
        $dtarq .= "-".substr($arq_array[0], substr($k15_poslan, 0, 3) - 1, substr($k15_poslan, 3, 3));

      } else {

        if (substr($k15_plano, 3, 3) == '002') {
          $dtarq = '20'.substr($arq_array[$i], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        } else {
          $dtarq = substr($arq_array[$i], substr($k15_plano, 0, 3) - 1, substr($k15_plano, 3, 3));
        }

        $dtarq .= "-".substr($arq_array[$i], substr($k15_plmes, 0, 3) - 1, substr($k15_plmes, 3, 3));
        $dtarq .= "-".substr($arq_array[$i], substr($k15_poslan, 0, 3) - 1, substr($k15_poslan, 3, 3));

      }

      if (substr($k15_ppano, 3, 3) == '002') {
        $dtpago = '20'.substr($arq_array[$i], substr($k15_ppano, 0, 3) - 1, substr($k15_ppano, 3, 3));
      } else {
        $dtpago = substr($arq_array[$i], substr($k15_ppano, 0, 3) - 1, substr($k15_ppano, 3, 3));
      }

      $dtpago .= "-".substr($arq_array[$i], substr($k15_ppmes, 0, 3) - 1, substr($k15_ppmes, 3, 3));
      $dtpago .= "-".substr($arq_array[$i], substr($k15_pospag, 0, 3) - 1, substr($k15_pospag, 3, 3));

      if ($dtpago == '0000-00-00') {
        $dtpago = $dtarquivo;
        $dtarq  = $dtarquivo;
      }

      if (substr($k15_anocredito, 3, 3) == '002') {
        $dtcredito = '20'.substr($arq_array[$i], substr($k15_anocredito, 0, 3) - 1, substr($k15_anocredito, 3, 3));
      } else {
        $dtcredito  = substr($arq_array[$i], substr($k15_anocredito, 0, 3) - 1, substr($k15_anocredito, 3, 3));
      }

      $dtcredito .= "-".substr($arq_array[$i], substr($k15_mescredito, 0, 3) - 1, substr($k15_mescredito, 3, 3));
      $dtcredito .= "-".substr($arq_array[$i], substr($k15_diacredito, 0, 3) - 1, substr($k15_diacredito, 3, 3));

      if (empty($dtcredito) || $dtcredito == '0000-00-00') {
        $dtcredito = $dtpago;
      }

      if ($lDebugAtivo == true) {
        echo "1234=vlrpago: $vlrpago - numpre = $numpre<br>";
      }

      $vlrpago  = (substr($arq_array[$i], substr($k15_posvlr, 0, 3) - 1, substr($k15_posvlr, 3, 3)) / 100) + 0;
      $vlrjuros = (substr($arq_array[$i], substr($k15_posjur, 0, 3) - 1, substr($k15_posjur, 3, 3)) / 100) + 0;
      $vlrmulta = (substr($arq_array[$i], substr($k15_posmul, 0, 3) - 1, substr($k15_posmul, 3, 3)) / 100) + 0;
      $vlracres = (substr($arq_array[$i], substr($k15_posacr, 0, 3) - 1, substr($k15_posacr, 3, 3)) / 100) + 0;
      $vlrdesco = (substr($arq_array[$i], substr($k15_posdes, 0, 3) - 1, substr($k15_posdes, 3, 3)) / 100) + 0;
      $cedente  = $convenio;
      $convenio = "";

      $k15_posvlrhonorarios = "151013";
      $vlrhonorarios = (substr($arq_array[$i], substr($k15_posvlrhonorarios, 0, 3) - 1, substr($k15_posvlrhonorarios, 3, 3)) / 100) + 0;

      if ($lDebugAtivo == true) {
        echo "5678=vlrpago: $vlrpago - vlrhonorarios: $vlrhonorarios<br>";
      }

      /**
       * Com registro
       */
      /**if ( $k15_codbco == 1 && $k15_codage == "0728C" ) {

        if ( substr($arq_array[$i],0,1) == "7" && ( substr($arq_array[$i],108,2) == "05" || substr($arq_array[$i],108,2) == "06" || substr($arq_array[$i],108,2) == "07" || substr($arq_array[$i],108,2) == "08") ) {

          if ($lDebugAtivo == true) {
            echo("xxxxxx - continue <br><br><br>");
          }

          $aGuardar["numpre"]    = $numpre;
          $aGuardar["numpar"]    = $numpar;
          $aGuardar["numbco"]    = $numbco;
          $aGuardar["dtpago"]    = $dtpago;
          $aGuardar["dtarq"]     = $dtarq;
          $aGuardar["dtcredito"] = $dtcredito;

        } else {

          if ($lDebugAtivo == true) {
            echo("yyyyyy - nao continue<br><br><br>");
          }

          $numpre    = $aGuardar["numpre"];
          $numpar    = $aGuardar["numpar"];
          $numbco    = $aGuardar["numbco"];
          $dtpago    = $aGuardar["dtpago"];
          $dtarq     = $aGuardar["dtarq"];
          $dtcredito = $aGuardar["dtcredito"];
          $vlrpago   = $vlrpago - 1.90; // desconsiderar taxa do favorecido que é somado no valor da prefeitura quando o txt é gerado...

        }

      } else */
      if ( $k15_codbco == 1 && $k15_codage == "2280F" && false ) {

        /**
         * Pagamento em cheque - não processar registro, pois vem com valor zerado
         */
        if (substr($arq_array[$i], 13, 1) == 'U' && substr($arq_array[$i],15,2) == '50' ) {
          $numpre = "";
          $numpar = "";
          continue;
        }

      }

      /**
       * D A E B
       *
       * Caso o cnpj seja da instituição DAEB
       * Incluímos o arquivo cai4_baixabanco_daeb.php que possui a lógica própria para o processamento dos arquivos
       * de baixa bancária destinados ao DAEB.
       */
      if ($cgc == '90940172000138') {
        include modification("cai4_baixabanco_daeb.php");
        continue;
      }

      if ($k15_codage == '88888') {

        /**
         * Inclui fonte que contém a lógica para quando agencia for 88888 (numpremigra)
         */
        include modification('cai4_baixabanco001_agencia_88888.php');

      } else {

        // Araruama/rj
        if ($cgc == '28531762000133') {

          $numpre_procura = $numpre;
          $sql_recibo     = "select k00_numpre from recibopaga where k00_numnov = $numpre_procura limit 1";
          $result_recibo  = db_query($sql_recibo);

          if (pg_num_rows($result_recibo) > 0) {
            $numpre_procura = pg_fetch_result($result_recibo, 0);
          }

          $sql_arretipo    = " (select k00_tipo from arrecad    where k00_numpre = $numpre_procura limit 1) ";
          $sql_arretipo   .= "   union                                                                      ";
          $sql_arretipo   .= " (select k00_tipo from arrecant   where k00_numpre = $numpre_procura limit 1) ";
          $sql_arretipo   .= "   union                                                                      ";
          $sql_arretipo   .= " (select k00_tipo from arreold    where k00_numpre = $numpre_procura limit 1) ";
          $sql_arretipo   .= "   union                                                                      ";
          $sql_arretipo   .= " (select k00_tipo from arreforo   where k00_numpre = $numpre_procura limit 1) ";
          $sql_arretipo   .= "   union                                                                      ";
          $sql_arretipo   .= " (select k30_tipo from arreprescr where k30_numpre = $numpre_procura limit 1) ";
          $sql_arretipo   .= "   union                                                                      ";
          $sql_arretipo   .= " (select k00_tipo from recibo     where k00_numpre = $numpre_procura limit 1) ";
          $result_arretipo = db_query($sql_arretipo);

          if (pg_num_rows($result_arretipo)>0) {
            $k00_tipo = pg_fetch_result($result_arretipo, 0);
          } else {

            /**
             * Força ser o ARRETIPO 1-IPTU caso NAO ENCONTRE o tipo de debito do NUMPRE
             */
            $k00_tipo = 1;

          }

          /**
           * @todo verificar possibilidade de utilizar o cadastro de taxas especificas ao invés do arretipo
           * remover lógica que busca a taxa bancaria da paritbi pelo cadtipo passando a verificar apenas por arretipo
           * retirar o campo it24_taxabancaria da tabela paritbi
           */
          $sSqlGrupoDebito = "select k03_tipo from cadtipo natural join arretipo where k00_tipo = $k00_tipo";
          $rsGrupoTipo     = db_query($sSqlGrupoDebito);
          $iGrupoTipo      = db_utils::fieldsMemory($rsGrupoTipo, 0)->k03_tipo;

          if ($iGrupoTipo == 8) {

            $oDaoParITBI     = new cl_paritbi();
            $oData           = new DBDate($dtpago);
            $iAnoPagamento   = $oData->getAno();
            $rsTxParItbi     = db_query($oDaoParITBI->sql_query($iAnoPagamento, 'it24_taxabancaria'));

            if (pg_num_rows($rsTxParItbi) > 0) {
              $k00_txban = db_utils::fieldsMemory($rsTxParItbi, 0)->it24_taxabancaria;
            } else {
              $k00_txban = 0;
            }

          } else {

            $sql_arretipo    = "select k00_txban from arretipo where k00_tipo = $k00_tipo and k00_instit = $iInstitSessao";
            $result_arretipo = db_query($sql_arretipo);

            if (pg_num_rows($result_arretipo)>0) {
              $k00_txban = pg_fetch_result($result_arretipo, 0);
            } else {
              $k00_txban = 0;
            }
          }

          if ( (float)$vlrpago > $k00_txban ) {
            $total_tx_bancaria += $k00_txban;
            $vlrpago           -= $k00_txban;
          }

          $nTaxaExpediente = $k00_txban;
          $nTarifaBancaria = $k15_txban;

          $clDisBancoTXT->k34_numpremigra = (string) $numpre;
          $clDisBancoTXT->k34_valor       = (string) ($nTaxaExpediente+0);
          $clDisBancoTXT->k34_dtvenc      = $dtpago;
          $clDisBancoTXT->k34_dtpago      = $dtpago;
          $clDisBancoTXT->k34_codret      = $codret;
          $clDisBancoTXT->k34_diferenca   = $nTarifaBancaria+0;
          $clDisBancoTXT->incluir(null);

          if ($clDisBancoTXT->erro_status == "0") {
            $oParametrosMsg            = new stdClass();
            $oParametrosMsg->iLinhaTXT = $arq_array[$i];
            $oParametrosMsg->sMsgErro  = $clDisBancoTXT->erro_msg;
            $sMsg                      = _M( MENSAGENS . 'erro_inclusao_disbancotxt', $oParametrosMsg);
            throw new DBException($sMsg);
          }

          $k34_sequencial = $clDisBancoTXT->k34_sequencial;

          $vlracres = 0;
          $vlrdesco = 0;

        } else {

          if ($lDebugAtivo == true ) {
            echo "55555555555555555555555 - numpre: $numpre <br><br>";
          }

          $sql_arretipo    = " select ";
          $sql_arretipo   .= " (select k00_txban from arrecad a inner join arretipo on a.k00_tipo = arretipo.k00_tipo   where k00_numpre = $numpre limit 1) ";
          $sql_arretipo   .= "   union                                                              ";
          $sql_arretipo   .= " (select k00_txban from arrecant a inner join arretipo on a.k00_tipo = arretipo.k00_tipo  where k00_numpre = $numpre limit 1) ";
          $sql_arretipo   .= "   union                                                              ";
          $sql_arretipo   .= " (select k00_txban from arreold a inner join arretipo on a.k00_tipo = arretipo.k00_tipo   where k00_numpre = $numpre limit 1) ";
          $sql_arretipo   .= "   union                                                              ";
          $sql_arretipo   .= " (select k00_txban from arreforo a inner join arretipo on a.k00_tipo = arretipo.k00_tipo  where k00_numpre = $numpre limit 1) ";
          $sql_arretipo   .= "   union                                                              ";
          $sql_arretipo   .= " (select k00_txban from arreprescr a inner join arretipo on a.k30_tipo = arretipo.k00_tipo where k30_numpre = $numpre limit 1) ";
          $sql_arretipo   .= "   union                                                              ";
          $sql_arretipo   .= " (select k00_txban from recibo a inner join arretipo on a.k00_tipo = arretipo.k00_tipo    where k00_numpre = $numpre limit 1) ";
          $result_arretipo = db_query($sql_arretipo) or die("erro: $sql_arretipo");

          $iPassa = 1;

          $dDtVencTaxaExpediente = '2015-07-24';
          $iNumpreTaxa = 45766882;

//          $sSqlTaxaExpedienteReciboPaga = "select k00_numpre from recibopaga where k00_numnov = $numpre and k00_dtoper < '$dDtVencTaxaExpediente'";
          $sSqlTaxaExpedienteReciboPaga = "select k00_numpre from recibopaga where k00_numnov = $numpre and k00_numnov < $iNumpreTaxa";
          $rsTaxaExpedienteReciboPaga = db_query($sSqlTaxaExpedienteReciboPaga) or die("erro: $sSqlTaxaExpedienteReciboPaga");

          $sSqlTaxaExpedienteReciboPagaIPTU = "select arrecad.k00_numpre from recibopaga inner join arrecad on recibopaga.k00_numpre = arrecad.k00_numpre and recibopaga.k00_numpar = arrecad.k00_numpar where k00_numnov = $numpre and k00_tipo = 65";
          $rsTaxaExpedienteReciboPagaIPTU = db_query($sSqlTaxaExpedienteReciboPagaIPTU) or die("erro: $sSqlTaxaExpedienteReciboPagaIPTU");

//          $sSqlTaxaExpedienteRecibo = "select k00_numpre from recibo where k00_numpre = $numpre and k00_dtvenc < '$dDtVencTaxaExpediente'";
          $sSqlTaxaExpedienteRecibo = "select k00_numpre from recibo where k00_numpre = $numpre and k00_numpre < $iNumpreTaxa";
          $rsTaxaExpedienteRecibo = db_query($sSqlTaxaExpedienteRecibo) or die("erro: $sSqlTaxaExpedienteRecibo");

          $sSqlArrecad = "select k00_numpre from arrecad where k00_numpre = $numpre and k00_numpar = $numpar";
          $rsArrecad = db_query($sSqlArrecad) or die("erro: $sSqlArrecad");

          $sSqlSuspenso = "select k00_numpre from arresusp where k00_numpre = $numpre and k00_numpar = $numpar";
          $rsSuspenso = db_query($sSqlSuspenso) or die("erro: $sSqlSuspenso");

          #$cCodigoBarras = substr($arq_array[$i],37,44);
          #$sSqlCarne = "select k00_numpre from recibocodbarcarne where k00_codbar = '$cCodigoBarras'";
          #$rsCarne = db_query($sSqlCarne) or die("erro: $sSqlCarne");

//    if ( pg_num_rows($rsTaxaExpedienteReciboPaga) > 0 or pg_num_rows($rsTaxaExpedienteRecibo) > 0 or ( pg_num_rows($rsArrecad) > 0 and pg_num_rows($rsCarne) == 0 ) ) {
          if ( pg_num_rows($rsCarne) > 0 ) {
            $lGeraReceita = false;
          } elseif ( pg_num_rows($rsTaxaExpedienteReciboPagaIPTU) > 0 ) {
            $lGeraReceita = true;
          } elseif ( pg_num_rows($rsSuspenso) > 0 ) {
            $lGeraReceita = true;
          } elseif ( pg_num_rows($rsTaxaExpedienteReciboPaga) == 0 and pg_num_rows($rsTaxaExpedienteRecibo) == 0 and pg_num_rows($rsCarne) == 0 and pg_num_rows($rsTaxaExpedienteReciboPagaIPTU) == 0 and pg_num_rows($rsArrecad) > 0 ) {
            $lGeraReceita = true;
          } elseif ( pg_num_rows($rsTaxaExpedienteReciboPaga) > 0 or pg_num_rows($rsTaxaExpedienteRecibo) > 0 ) {
            $lGeraReceita = true;
          } else {
            $lGeraReceita = false;
          }
//echo("numpre: $numpre/$numpar - lGeraReceita: " . ( $lGeraReceita?1:0) . " - codbar: $cCodigoBarras - recibopaga: " . pg_num_rows($rsTaxaExpedienteReciboPaga) . " - recibo: " . pg_num_rows($rsTaxaExpedienteRecibo) . " - arrecad: " . pg_num_rows($rsArrecad) . " - carne: " . pg_num_rows($rsCarne) . " - iptu: " . pg_num_rows($rsTaxaExpedienteReciboPagaIPTU) . " - suspenso: " . pg_num_rows($rsSuspenso) .   "<br>");

          if ( pg_num_rows($result_arretipo) > 0 ) {
            $k00_txban = pg_fetch_result($result_arretipo, 0);
            if ( $k00_txban + 0 == 0 ) {
              $iPassa = 0;
            }
          }

//          if ( $numpre > 40000000 and $cgc == '28521748000159' and $iPassa == 1 ) {
          if ( $numpre > 40000000 and $cgc == '28521748000159' and $lGeraReceita == true and db_getsession("DB_anousu") <= 2015 ) {

            /**
             * Função de tratamento da taxa bancaria
             */
            $oParametrosTaxaBancaria = new stdClass();
            $oParametrosTaxaBancaria->codret          = $codret;
            $oParametrosTaxaBancaria->numpre          = $numpre;
            $oParametrosTaxaBancaria->k15_codage      = $k15_codage;
            $oParametrosTaxaBancaria->k15_codbco      = $k15_codbco;
            $oParametrosTaxaBancaria->k03_pgtoparcial = $oDadosParametrosNumpref->k03_pgtoparcial;
            $oParametrosTaxaBancaria->dtarq           = $dtarq;
            $oParametrosTaxaBancaria->dtpago          = $dtpago;
            $oParametrosTaxaBancaria->dtcredito       = $dtcredito;

            $iVlrTaxaBancaria = geraTaxaBancaria($oParametrosTaxaBancaria);

            if ($iVlrTaxaBancaria > 0 && $vlrpago > $iVlrTaxaBancaria) {
              $vlrpago -= $iVlrTaxaBancaria;
            }
            
            /**if ( $k15_codbco == 1 && $k15_codage == "0728C" ) {
              
               // Função de tratamento honorarios
              

              $oParametrosHonorarios = new stdClass();
              $oParametrosHonorarios->codret          = $codret;
              $oParametrosHonorarios->valorhonorarios = $vlrhonorarios;
              $oParametrosHonorarios->numpre          = $numpre;
              $oParametrosHonorarios->k15_codage      = $k15_codage;
              $oParametrosHonorarios->k15_codbco      = $k15_codbco;
              $oParametrosHonorarios->k03_pgtoparcial = $oDadosParametrosNumpref->k03_pgtoparcial;
              $oParametrosHonorarios->dtarq           = $dtarq;
              $oParametrosHonorarios->dtpago          = $dtpago;
              $oParametrosHonorarios->dtcredito       = $dtcredito;

              $iVlrHonorarios = geraHonorarios($oParametrosHonorarios);

            }*/

          }
        }

        $clDisBanco->codret     = $codret;
        $clDisBanco->k15_codbco = $k15_codbco;
        $clDisBanco->k15_codage = $k15_codage;
        $clDisBanco->k00_numbco = $numbco;
        $clDisBanco->dtarq      = $dtarq;
        $clDisBanco->dtpago     = $dtpago;
        $clDisBanco->dtcredito  = $dtcredito;
        $clDisBanco->vlrpago    = "$vlrpago";
        $clDisBanco->vlrjuros   = "$vlrjuros";
        $clDisBanco->vlrmulta   = "$vlrmulta";
        $clDisBanco->vlracres   = "$vlracres";
        $clDisBanco->vlrdesco   = "$vlrdesco";
        $clDisBanco->vlrcalc    = "$vlrpago+$vlrjuros+$vlrmulta+$vlracres-$vlrdesco";
        $clDisBanco->cedente    = $cedente;
        $clDisBanco->vlrtot     = "$vlrpago+$vlrjuros+$vlrmulta+$vlracres-$vlrdesco";
        $clDisBanco->classi     = "false";
        $clDisBanco->k00_numpre = "".($numpre+0)."";
        $clDisBanco->k00_numpar = "".($numpar+0)."";
        $clDisBanco->convenio   = $convenio;
        $clDisBanco->instit     = $iInstitSessao;
        $clDisBanco->incluir(null);

        if ($clDisBanco->erro_status == "0") {
          $oParametrosMsg           = new stdClass();
          $oParametrosMsg->iNumpre  = $clDisBanco->k00_numpre;
          $oParametrosMsg->sMsgErro = $clDisBanco->erro_msg;
          $sMsg                      = _M( MENSAGENS . 'erro_inclusao_disbanco_numpre', $oParametrosMsg);
          throw new DBException($sMsg);
        }

        $idRet = $clDisBanco->idret;

        // Araruama/rj
        if ($cgc == '28531762000133') {

          $clDisBancoTXTReg->k35_disbancotxt = $k34_sequencial;
          $clDisBancoTXTReg->k35_idret       = $idRet;
          $clDisBancoTXTReg->incluir(null);
          if ($clDisBancoTXTReg->erro_status == "0") {

            $oParametrosMsg           = new stdClass();
            $oParametrosMsg->sMsgErro = $clDisBancoTXTReg->erro_msg;
            $sMsg                     = _M( MENSAGENS . 'erro_inclusao_disbancotxtreg', $oParametrosMsg);
            throw new DBException($sMsg);
          }
        }

        echo "<script>js_termometro(". $i.");</script>";
        flush();

        $numpre = "";
        $numpar = "";

      }

      if ($lDebugAtivo == true ) {
        echo "<br><br><br><br><br> FIM DO IDRET <br><br><br><br><br>";
      }

    }
    if ($lDebugAtivo == true ) {

      $sSqlDisbanco = " select codret, dtarq, idret, disbanco.k00_numpre, disbanco.k00_numpar, vlrpago, k00_numbco, k00_histtxt, 

                        ( select count(*) from recibopaga a inner join arrecad b on a.k00_numpre = b.k00_numpre and a.k00_numpar = b.k00_numpar where k00_numnov = disbanco.k00_numpre and a.k00_conta = 0 ) as count1,

                        ( select count(*) from recibopaga a left join arrecad b on a.k00_numpre = b.k00_numpre and a.k00_numpar = b.k00_numpar and a.k00_receit = b.k00_receit where a.k00_hist not in ( 400, 401, 918 ) and a.k00_numnov = disbanco.k00_numpre and b.k00_numpre is null ) as count2

                        from disbanco 
                        left join arrehist on arrehist.k00_numpre = disbanco.k00_numpre and arrehist.k00_numpar = disbanco.k00_numpar 
                        where codret = $codret
                        order by 3;
                      ";

      db_criatabela( db_query ( $sSqlDisbanco ) );

      echo "<br/>F I M<br/>";
      exit;

    }

    $sql  = "  select dtarq,                  ";
    $sql .= "         sum(vlrpago)            ";
    $sql .= "    from disbanco                ";
    $sql .= "   where codret = $codret        ";
    $sql .= "     and instit = $iInstitSessao ";
    $sql .= "group by dtarq                   ";
    $result = db_query($sql);

    $total = 0;

    for ($x = 0; $x < pg_num_rows($result); $x ++) {

      db_fieldsmemory($result, $x, true);
      echo "Data: $dtarq - Valor: ".db_formatar($sum, "f")."<br/>";
      $total += $sum;
    }

    $oParametrosMsg                 = new stdClass();
    $oParametrosMsg->iTotalArquivo  = trim(db_formatar($total, "f"));
    $oParametrosMsg->iCodRet        = $codret;

    echo "Total do Arquivo: ".db_formatar($total, "f")." - Codret: $codret<br/>";

    // Araruama/rj
    if ($cgc == '28531762000133') {

      $oParametrosMsg->iTotalTaxaBancaria = trim(db_formatar($total_tx_bancaria, "f"));
      $oParametrosMsg->iTotalArquivo      = trim(db_formatar($total+$total_tx_bancaria, "f"));

      echo "Total Taxa Bancária: ".db_formatar($total_tx_bancaria, "f")."<br/>";
      echo "Total do Arquivo: ".db_formatar($total+$total_tx_bancaria, "f")."<br/>";
    }

    if ($achou_arrecant == 0) {

      /**
       * Verificamos a forma de processamento do arquivo txt (k03_agrupadorarquivotxtbaixabanco)
       *
       * 0 - Classificação por arquivo:           Gerado somente um registros na disarq
       * 1 - Classificação por data do Pagamento: Gerado mais de um registro na disarq de acordo com a quantidade
       *                                          de datas de pagamentos encontradas no arquivo (campo: dtpago)
       * 2 - Classificação por data de Crédito:   Gerado mais de um registro na disarq de acordo com a quantidade
       *                                          de datas de créditos encontradas no arquivo (campo: dtcredito)
       */
      if (   $oDadosParametrosNumpref->k03_agrupadorarquivotxtbaixabanco == 1
        || $oDadosParametrosNumpref->k03_agrupadorarquivotxtbaixabanco == 2) {

        $iCodRet = $codret;
        include_once modification("cai4_desmembramentodisbanco001.php");
      }

      db_fim_transacao(false);
      unset($_POST);

      $sTipoMensagem = 'documento_processado';
      if( !empty($oParametrosMsg->iTotalTaxaBancaria) ){
        $sTipoMensagem = 'documento_processado_com_taxa_bancaria';
      }

      db_msgbox( _M( MENSAGENS . $sTipoMensagem, $oParametrosMsg ) );
      db_redireciona();

    } else {
      throw new BusinessException( _M( MENSAGENS . 'documento_nao_processado' ) );
    }

  } catch (DBException $eErro){           // DB Exception

    db_fim_transacao(true);
    echo $eErro->getMessage();
    db_msgbox($eErro->getMessage());
    db_redireciona();

  } catch (BusinessException $eErro){     // Business Exception

    db_fim_transacao(true);
    db_msgbox($eErro->getMessage());
    db_redireciona();

  } catch (ParameterException $eErro){     // Parameter Exception

    db_fim_transacao(true);
    db_msgbox($eErro->getMessage());

  } catch (Exception $eErro){

    db_fim_transacao(true);
    db_msgbox($eErro->getMessage());
  }

}
?>
