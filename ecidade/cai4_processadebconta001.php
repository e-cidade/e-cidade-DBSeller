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

require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("libs/db_utils.php");

require_once  modification("classes/db_db_config_classe.php");
require_once  modification("classes/db_cadban_classe.php");
require_once  modification("classes/db_disarq_classe.php");
require_once  modification("classes/db_disbanco_classe.php");

require_once  modification("classes/db_debcontapedidotiponumpre_classe.php");
require_once  modification("classes/db_debcontapedido_classe.php");
require_once  modification("classes/db_debcontapedidocgm_classe.php");
require_once  modification("classes/db_debcontapedidomatric_classe.php");
require_once  modification("classes/db_debcontapedidoinscr_classe.php");
require_once  modification("classes/db_debcontapedidohistorico_classe.php");

require_once  modification("classes/db_debcontaarquivoregped_classe.php");
require_once  modification("classes/db_debcontaarquivoreg_classe.php");
require_once  modification("classes/db_debcontaarquivo_classe.php");
require_once  modification("classes/db_debcontaarquivoregmov_classe.php");
require_once  modification("classes/db_debcontaarquivoregret_classe.php");

require_once  modification("dbforms/db_funcoes.php");

// tipo B
require_once  modification("classes/db_debcontapedidomatric_classe.php");
require_once  modification("classes/db_debcontapedidotipo_classe.php");

db_postmemory($_POST);

$erro   = false;
$instit = db_getsession("DB_instit");

$clcadban                   = new cl_cadban;
$cldisarq                   = new cl_disarq;
$cldisbanco                 = new cl_disbanco;
$cldb_config                = new cl_db_config;

$cldebcontapedidotiponumpre = new cl_debcontapedidotiponumpre;
$cldebcontapedido           = new cl_debcontapedido;
$cldebcontapedidocgm        = new cl_debcontapedidocgm;
$cldebcontapedidomatric     = new cl_debcontapedidomatric;
$cldebcontapedidoinscr      = new cl_debcontapedidoinscr;
$cldebcontapedidohistorico  = new cl_debcontapedidohistorico;

$cldebcontaarquivoregped    = new cl_debcontaarquivoregped;
$cldebcontaarquivoreg       = new cl_debcontaarquivoreg;
$cldebcontaarquivo          = new cl_debcontaarquivo;
$cldebcontaarquivoreg       = new cl_debcontaarquivoreg;
$cldebcontaarquivoregret    = new cl_debcontaarquivoregret;
$cldebcontaarquivoregmov    = new cl_debcontaarquivoregmov;

$cldebcontapedidomatric     = new cl_debcontapedidomatric;
$cldebcontapedidotipo       = new cl_debcontapedidotipo;

$db_opcao = 1;
$db_botao = true;
$situacao = 0;

$iInstitSessao = db_getsession("DB_instit");

$result = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc"));
db_fieldsmemory($result, 0);

if (isset($processar)) {

  db_postmemory($_FILES["arqret"]);

  $arq_name    = basename($name);
  $arq_type    = $type;
  $arq_tmpname = basename($tmp_name);
  $arq_size    = $size;
  $arq_array   = file($tmp_name);

  system("cp -f ".$tmp_name." ".ECIDADE_PATH."tmp/".$arq_name);
  
  $sSqlBuscaBanco = $clcadban->sql_query("","*",""," k15_codbco = $d63_banco and k15_codage = '$k15_codage' and k15_instit = $instit");
  $resultcadban   = $clcadban->sql_record($sSqlBuscaBanco);
 
  if ($clcadban->numrows == 0) {
    $erro_msg =  "Banco / Agencia não cadastrados para esta instituição.";
    $erro = true;
  }

  if ($erro == false) {
    db_fieldsmemory($resultcadban,0);
    $_tamanprilinha = $arq_array[0];
    $atipo          = substr($arq_array[0],0,3);
    $totalproc      = sizeof($arq_array)-2;
    $priregistro    = 1;
    $acodbco        = substr($arq_array[0],substr($k15_posbco,0,3),substr($k15_posbco,3,3));
    if (strlen($_tamanprilinha) != $k15_taman) {
      $erro_msg =  "Tamanho do registro [".strlen($arq_array[0])."] Sistema : [" .$k15_taman."] inválido";
      $erro = true;
    } else {
      if ($k15_codbco != $acodbco) {
        $erro_msg =  "Banco Digitado [$k15_codbco] não confere com o arquivo [$acodbco] especificado.";
        $erro = true;
      } else {
        $resultdisarq = $cldisarq->sql_record($cldisarq->sql_query("","*",""," arqret = '$arq_name'"));
        if ($cldisarq->numrows > 0) {
          db_fieldsmemory($resultdisarq,0);
        }
      }
      $totalvalorpago=0;
      for ($i=0; $i <= $totalproc; $i++) {
        $vlrpago  = (substr($arq_array[$i], substr($k15_posvlr, 0, 3) - 1 , substr($k15_posvlr, 3, 3)) / 100) + 0;
        $totalvalorpago += $vlrpago;
      }
      $situacao = 1;
    }
  } else {
    $alert = "alert('Ocorreu algum erro durante o processamento!\\nErro: $erro_msg')";
    echo "<script>$alert;</script>";
    db_redireciona();
  }
  if (isset($processar) and (isset($arq_name))) {
    $situacao = 2;
    global $arq_array;
    $arq_array = file(ECIDADE_PATH."tmp/".$arq_name);
  }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, prototype.js, estilos.css, EmissaoRelatorio.js");
?>
</head>
<body class="body-default" onLoad="a=1">
<?php
  include modification("forms/db_frmprocessadebconta.php");
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php

$oDaoNumpref = db_utils::getDao("numpref");
define('MENSAGENS', 'tributario.arrecadacao.cai4_baixabanco001.');
/**
 * Busca parametros da numpref
 */
$sCamposParametrosNumpref = "k03_agrupadorarquivotxtbaixabanco, k03_pgtoparcial";
$sSqlParametrosNumpref    = $oDaoNumpref->sql_query_file(db_getsession("DB_anousu"), db_getsession("DB_instit"), $sCamposParametrosNumpref);
$rsParametrosNumpref      = $oDaoNumpref->sql_record($sSqlParametrosNumpref);
$oDadosParametrosNumpref  = db_utils::fieldsMemory($rsParametrosNumpref, 0);

function geraTaxaBancaria( $oParametros ){

  $oTabDescCadBan = db_utils::getDao("tabdesccadban");
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
  $sWhere  = "     k15_codbco = {$oParametros->k15_codbco}   ";
  $sWhere .= " and k15_codage = '{$oParametros->k15_codage}' ";
  $sWhere .= " and (k07_dtval is null or k07_dtval > '".date("Y-m-d", db_getsession("DB_datausu"))."')";
  $sSqlTaxaBancaria    = $oTabDescCadBan->sql_query(null, "*", null, $sWhere);
  $rsTaxaBancaria      = $oTabDescCadBan->sql_record($sSqlTaxaBancaria);

  $iLinhasTaxaBancaria = $oTabDescCadBan->numrows;

  /**
   * Não pode haver mais de uma taxa configurada para o mesmo banco e agencia
   */
  if($iLinhasTaxaBancaria > 1){
    $erro_msg = _M( MENSAGENS . "taxa_especifica_duplicada" );
    return false;
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

      $rsMatriculaRecibo = $oArreMatric->sql_record($oArreMatric->sql_query_file($oParametros->numpre, null, "k00_matric") . " union select arrematric.k00_matric from recibopaga inner join arrematric on recibopaga.k00_numpre = arrematric.k00_numpre where k00_numnov = " . $oParametros->numpre);
      if ($oArreMatric->numrows > 0) {
        for ($iIndMatricula = 0; $iIndMatricula < $oArreMatric->numrows; $iIndMatricula++) {
          $oRecibo->setMatricula(db_utils::fieldsMemory($rsMatriculaRecibo, $iIndMatricula)->k00_matric);
        }
      }

      $rsInscricaoRecibo = $oArreInscr->sql_record($oArreInscr->sql_query_file($oParametros->numpre,null,"k00_inscr") . " union select arreinscr.k00_inscr from recibopaga inner join arreinscr on recibopaga.k00_numpre = arreinscr.k00_numpre where k00_numnov = " . $oParametros->numpre);
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
        $erro_msg = $oDisBanco->erro_msg;
        return false;
      }

    }

  }

  return $nVlrTaxaBancaria;

}

if (isset($geradebcta)) {
 
  unset($processar, $geradebcta);
  
  $arq_array = file(ECIDADE_PATH."tmp/".$arq_name);
  $totalproc = count($arq_array);
  $sqlerro   = false;

  db_inicio_transacao();
  $sSql = $clcadban->sql_query("","*",""," k15_codbco = $d63_banco and k15_codage = '$k15_codage' and k15_instit = $instit");
  // echo $sSql;
  $resultcadban = $clcadban->sql_record($sSql);
  
  if ($clcadban->numrows == 0) {
    $erro_msg =  "Banco / Agencia não cadastrados para esta instituição.";
    $erro     = true;
    $sqlerro  = true;
  }

  if ($sqlerro == false) {

    db_fieldsmemory($resultcadban,0);

    $dtarquivo  = substr($arq_array[0],substr($k15_pdano,0,3)-1,substr($k15_pdano,3,3));
    $dtarquivo .= "-".substr($arq_array[0],substr($k15_pdmes,0,3)-1,substr($k15_pdmes,3,3));
    $dtarquivo .= "-".substr($arq_array[0],substr($k15_posdta,0,3)-1,substr($k15_posdta,3,3));

    $sMd5Arquivo = md5(file_get_contents(ECIDADE_PATH."tmp/".$arq_name));

    /**
     * Verifica se arquivo já foi importado
     */
    $sSqlArquivoImportado = $cldisarq->sql_query_file(null, 'true', null, "md5 = '$sMd5Arquivo'");
    $rsArquivoImportado   = $cldisarq->sql_record($sSqlArquivoImportado);

    if ($cldisarq->numrows > 0) {
      $erro_msg = _M( MENSAGENS . "arquivo_importado" );
      $sqlerro  = true;
    }
    
    $cldisarq->k15_codbco = $d63_banco;
    $cldisarq->k15_codage = $k15_codage;
    $cldisarq->arqret     = $arq_name;
    $cldisarq->dtretorno  = date('Y-m-d',db_getsession("DB_datausu"));
    $cldisarq->id_usuario = db_getsession("DB_id_usuario");
    $cldisarq->dtarquivo  = $dtarquivo;
    $cldisarq->textoret   = implode("", $arq_array);
    $cldisarq->k00_conta  = $k15_conta;
    $cldisarq->autent     = "false";
    $cldisarq->instit     = db_getsession("DB_instit");
    $cldisarq->md5        = $sMd5Arquivo;
    $cldisarq->incluir(null);

    if ($cldisarq->erro_status == 0) {

      $sqlerro  = true;
      $erro_msg = "disarq - " . $cldisarq->erro_msg;
    }
    $codret = $cldisarq->codret;
  }
  
  
  if ($sqlerro == false) {

    $achou_arrecant = 0;

    $k15_numpreori = $k15_numpre;
    $k15_numparori = $k15_numpar;
    $priregistro   = 1;

    if ($sqlerro == false) {
      
      $verifica_arq         = false;
      $valor_processado     = 0;
      $valor_nao_processado = 0;

      $_debug = false;

      db_criatermometro('termometro', 'Concluido...', 'blue', 1);
      flush();

      //
      // Processa Registros do Arquivo para Gravar em DISBANCO
      //

      for ($i=0; $i < $totalproc; $i++) {

        //Ignorar entrada "X" do arquivo
        if (substr($arq_array[$i],0,1) == "X") { 
          continue;
        }
        
        $cldebcontapedido->d63_datalanc = null;
        $cldebcontapedido->d63_horalanc = null;
        $cldebcontapedido->d63_instit   = 0;

        $cldebcontapedido->d63_banco     = 0;
        $cldebcontapedido->d63_agencia   = null;
        $cldebcontapedido->d63_conta     = null;
        $cldebcontapedido->d63_status    = 0;
        $cldebcontapedido->d63_idempresa = null;
      
        db_atutermometro($i, $totalproc, 'termometro');

        // Testa tipo do registro
        if (substr($arq_array[$i],0,1) <> "F") {
 
          // Tipo B
          if (substr($arq_array[$i],0,1) == "B") {

            $emiterel     = true;
            $banco        = $d63_banco;
            $agencia      = substr($arq_array[$i], 26, 4);
            $conta        = substr($arq_array[$i], 30, 14);
            $idempresa    = intval(str_pad(trim(substr($arq_array[$i], 1, 25)), 18, "0", STR_PAD_LEFT));
            $acao         = substr($arq_array[$i], 149, 1);
            $matricula    = intval(trim(substr($arq_array[$i], 1, 25)));
            $dtlanca      = substr($arq_array[$i], 44, 8);
            $hrlanca      = '00:00';
            $hrlancahist  = date('H:i');
            $ano          = $ano;
            $tipo         = 1;

            $check_matricula = db_query("select j01_matric,j01_numcgm as k00_numcgm from iptubase where j01_matric = " . $matricula);
            if (pg_numrows($check_matricula) == 0) {
              continue;
            }
           
            // $acao == 1 { exclusão }
            // $acao == 2 { inclusão }

            // $status == 1 { pendente }
            // $status == 2 { ativo }
            // $status == 3 { inativo }

            if($acao == 2){
              $status = 2;
            }elseif($acao == 1){
              $status = 3;
            }

            if($acao == 2){

              $cldebcontapedido->d63_datalanc  = $dtlanca;
              $cldebcontapedido->d63_horalanc  = $hrlanca;
              $cldebcontapedido->d63_instit    = db_getsession("DB_instit");
              $cldebcontapedido->d63_banco     = $banco;
              $cldebcontapedido->d63_agencia   = $agencia;
              $cldebcontapedido->d63_conta     = $conta;
              $cldebcontapedido->d63_status    = $status;
              $cldebcontapedido->d63_idempresa = $idempresa;

              /* Histórico da DebContaPedido */
              $cldebcontapedidohistorico->d83_datalanc  = $dtlanca;
              $cldebcontapedidohistorico->d83_horalanc  = $hrlancahist;
              $cldebcontapedidohistorico->d83_instit    = db_getsession("DB_instit");
              $cldebcontapedidohistorico->d83_banco     = $banco;
              $cldebcontapedidohistorico->d83_agencia   = $agencia;
              $cldebcontapedidohistorico->d83_conta     = $conta;
              $cldebcontapedidohistorico->d83_status    = $status;
              $cldebcontapedidohistorico->d83_idempresa = $idempresa;
              $cldebcontapedidohistorico->d83_codret    = $codret;
              /* Histórico da DebContaPedido */

              $datalanc = substr($dtlanca, 0, 4).'-'.substr($dtlanca, 4, 2).'-'.substr($dtlanca, 6, 2);

              $sqlDebContaPedidoData = "select 
                              d63_codigo    as ud63_codigo, 
                              d63_datalanc  as ud63_datalanc, 
                              d63_idempresa as ud63_idempresa, 
                              d63_status    as ud63_status
                            from 
                              debcontapedido 
                            where 
                              d63_idempresa::integer = $idempresa 
                            and 
                              d63_datalanc <= '$datalanc' and d63_instit = ".db_getsession("DB_instit");
              $rsDebContaPedidoData = db_query($sqlDebContaPedidoData);

              // echo $sqlDebContaPedidoData . '<br>';

              $sqlNumpre = "select
                              distinct 
                                arrecad.k00_numpre as mnumpre, 
                                arrecad.k00_numpar as mnumpar,
                                j20_numpre,
                                j20_anousu,
                                arrecad.k00_tipo
                            from arrecad 
                              inner join arrematric on arrecad.k00_numpre = arrematric.k00_numpre
                              inner join arretipo on arrecad.k00_tipo = arretipo.k00_tipo
                              inner join iptunump on arrecad.k00_numpre = j20_numpre
                            where 
                            arretipo.k03_tipo = $tipo
                            and j20_anousu = $ano
                            and k00_matric = $matricula 
                            order by 1";
                  
              $rsNumpre = db_query($sqlNumpre);
              $k00_tipoNumPre = pg_result($rsNumpre,0,4); 

              if(pg_numrows($rsDebContaPedidoData) > 0){

                db_fieldsmemory($rsDebContaPedidoData,0);
                $cldebcontapedido->d63_codigo   = $ud63_codigo;
                $cldebcontapedido->d63_datalanc = $dtlanca;
                $cldebcontapedido->alterar($ud63_codigo);

                /* Histórico da DebContaPedido */
                $cldebcontapedidohistorico->d83_acao = 2; /* ALTERACAO */
                $d83_debcontapedido = $ud63_codigo;
                /* Histórico da DebContaPedido */

              }else{

                $sqlDebContaPedido = "select 
                                *
                              from 
                                debcontapedido 
                              where 
                                d63_idempresa::integer = $idempresa
                                and d63_instit = ".db_getsession("DB_instit");
                $rsDebContaPedido = db_query($sqlDebContaPedido);
                
                if(pg_numrows($rsDebContaPedido) == 0){
                  $cldebcontapedido->incluir($d63_codigo);

                  $codpedido = $cldebcontapedido->d63_codigo;

                  $cldebcontapedidomatric->d68_matric = $matricula;
                  $cldebcontapedidomatric->incluir($codpedido);

                  /* Histórico da DebContaPedido */
                  $cldebcontapedidohistorico->d83_acao = 1; /* INCLUSAO */
                  $d83_debcontapedido = $codpedido;
                  /* Histórico da DebContaPedido */

                  $cldebcontapedidotiponumpre->d67_codigo = $codpedido;

                  if(pg_numrows($rsNumpre) > 0){
                    for($np = 0; $np < pg_numrows($rsNumpre); $np++){
                      db_fieldsmemory($rsNumpre, $np);

                      $cldebcontapedidotiponumpre->d67_numpre = $mnumpre;
                      $cldebcontapedidotiponumpre->d67_numpar = $mnumpar;

                      $cldebcontapedidotiponumpre->incluir();

                      if ($cldebcontapedidotiponumpre->erro_status == 0) {
                        $sqlerro = true;
                        $erro_msg = "debcontapedidotiponumpre - " . $cldebcontapedidotiponumpre->erro_msg;
                        break;
                      }
                    }
                  }
                }

              }
              if ($cldebcontapedido->erro_status == 0) {
                $sqlerro = true;
                $erro_msg = "1 debcontapedido - " . $cldebcontapedido->erro_msg;
                // echo $sqlDebContaPedido; exit;
                break;
              }

              $sqldebcontapedidotipo = $cldebcontapedidotipo->sql_query("","d66_sequencial",""," d66_codigo = $d83_debcontapedido ");
              $resultdebcontapedidotipo = $cldebcontapedidotipo->sql_record($sqldebcontapedidotipo);
              if ($cldebcontapedidotipo->numrows == 0) {
                $cldebcontapedidotipo->d66_arretipo = $k00_tipoNumPre;
                $cldebcontapedidotipo->d66_codigo   = $d83_debcontapedido;
                $cldebcontapedidotipo->incluir();
              } else {
                db_fieldsmemory($resultdebcontapedidotipo,0);
                $cldebcontapedidotipo->d66_arretipo   = $k00_tipoNumPre;
                $cldebcontapedidotipo->d66_codigo     = $d83_debcontapedido;
                $cldebcontapedidotipo->d66_sequencial = $d66_sequencial;
                $cldebcontapedidotipo->alterar($d66_sequencial);
              }

              if ($cldebcontapedidotipo->erro_status == 0) {
                $sqlerro  = true;
                $erro_msg = "1 debcontapedidotipo - " . $cldebcontapedidotipo->erro_msg;
                break;
              }
              
              $cldebcontapedidohistorico->d83_debcontapedido = $d83_debcontapedido;
              $cldebcontapedidohistorico->incluir();

            }elseif($acao == 1){

              $sqlMatric = "select d63_codigo as d68_codigo from debcontapedido where d63_banco = $banco AND right('0000'||trim(d63_agencia),4) = right('0000'||'$agencia',4) and right('00000000000000'||trim(d63_conta),14) = right('00000000000000'||'$conta',14) and d63_idempresa::integer = $idempresa and d63_instit = ".db_getsession("DB_instit");
              //echo $sqlMatric;
              $rsMatric  = db_query($sqlMatric);
              
              if(pg_numrows($rsMatric) > 0){
                db_fieldsmemory($rsMatric, 0);

                $cldebcontapedido->d63_codigo   = $d68_codigo;
                $cldebcontapedido->d63_datalanc = $dtlanca;
                $cldebcontapedido->d63_status   = $status;
                $cldebcontapedido->alterar($d68_codigo);

                if ($cldebcontapedido->erro_status == 0) {
                  $sqlerro = true;
                  $erro_msg = "2 debcontapedido - " . $cldebcontapedido->erro_msg;
                  break;
                }

                /* Histórico da DebContaPedido */
                $cldebcontapedidohistorico->d83_datalanc  = $dtlanca;
                $cldebcontapedidohistorico->d83_horalanc  = $hrlancahist;
                $cldebcontapedidohistorico->d83_instit    = db_getsession("DB_instit");
                $cldebcontapedidohistorico->d83_banco     = $banco;
                $cldebcontapedidohistorico->d83_agencia   = $agencia;
                $cldebcontapedidohistorico->d83_conta     = $conta;
                $cldebcontapedidohistorico->d83_status    = $status;
                $cldebcontapedidohistorico->d83_idempresa = $idempresa;
                $cldebcontapedidohistorico->d83_codret    = $codret;
                $cldebcontapedidohistorico->d83_acao      = 3; /* EXCLUSAO */
                $cldebcontapedidohistorico->d83_debcontapedido = $d68_codigo;
                $cldebcontapedidohistorico->incluir();
                /* Histórico da DebContaPedido */

              }

            }

            continue;

          }else{
            
            continue;
          }
        }

        // grava arquivo disbanco
        $teste = '002025';
        $debcta = intval(str_pad(trim(substr($arq_array[$i],substr($teste,0,3)-1,substr($teste,3,3))), 18, "0", STR_PAD_LEFT));
        $posdebctaped = '098010';
        $debctaped = intval(trim(substr($arq_array[$i],substr($posdebctaped,0,3)-1,substr($posdebctaped,3,3))));

        if ($_debug) {
          echo "processando: $i - total $totalproc - debcta $debcta <br>";
          flush();
        }

        $numbco = substr($arq_array[$i],substr($k15_numbco,0,3)-1,substr($k15_numbco,3,3));
        $dtarq  = $dtarquivo;
        
        $dtpago  = substr($arq_array[$i], substr($k15_ppano, 0, 3) - 1, substr($k15_ppano, 3, 3));
        $dtpago .= "-".substr($arq_array[$i], substr($k15_ppmes , 0, 3) - 1, substr($k15_ppmes, 3, 3));
        $dtpago .= "-".substr($arq_array[$i], substr($k15_pospag, 0, 3) - 1, substr($k15_pospag, 3, 3));

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
        
        $vlrpago  = empty($k15_posvlr) ? 0 : (substr($arq_array[$i], substr($k15_posvlr, 0, 3) - 1, substr($k15_posvlr, 3, 3)) / 100) + 0;
        $vlrjuros = empty($k15_posjur) ? 0 : (substr($arq_array[$i], substr($k15_posjur, 0, 3) - 1, substr($k15_posjur, 3, 3)) / 100) + 0;
        $vlrmulta = empty($k15_posmul) ? 0 : (substr($arq_array[$i], substr($k15_posmul, 0, 3) - 1, substr($k15_posmul, 3, 3)) / 100) + 0;
        $vlracres = empty($k15_posacr) ? 0 : (substr($arq_array[$i], substr($k15_posacr, 0, 3) - 1, substr($k15_posacr, 3, 3)) / 100) + 0;
        $vlrdesco = empty($k15_posdes) ? 0 : (substr($arq_array[$i], substr($k15_posdes, 0, 3) - 1, substr($k15_posdes, 3, 3)) / 100) + 0;

        $convenio =  substr($arq_array[$i], substr($k15_poscon, 0, 3) - 1, substr($k15_poscon, 3, 3));
        $cedente  =  substr($arq_array[$i], substr($k15_posced, 0, 3) - 1, substr($k15_posced, 3, 3));

        $tiporet  = substr($arq_array[$i], 67, 2);
        // echo '<pre>';
        // echo 'debcta - debcta : '.$debcta.' { i : '.(substr($teste,0, 3)-1).' | q : '.substr($teste,3, 3).' }<br>';

        // echo 'numbco - k15_numbco : '.$k15_numbco.' { i : '.(substr($k15_numbco,0, 3)-1).' | q : '.substr($k15_numbco,3, 3).' }<br>';
        // echo 'dtpago - k15_ppano  : '.$k15_ppano. ' { i : '.(substr($k15_ppano, 0, 3)-1).' | q : '.substr($k15_ppano, 3, 3).' }<br>';
        // echo 'dtpago - k15_ppmes  : '.$k15_ppmes. ' { i : '.(substr($k15_ppmes, 0, 3)-1).' | q : '.substr($k15_ppmes, 3, 3).' }<br>';
        // echo 'dtpago - k15_pospag : '.$k15_pospag.' { i : '.(substr($k15_pospag, 0, 3)-1).' | q : '.substr($k15_pospag, 3, 3).' }<br>';

        // echo 'vlrpago  - k15_posvlr : '.$k15_posvlr.' { i : '.(substr($k15_posvlr,0, 3)-1).' | q : '.substr($k15_posvlr,3, 3).' }<br>';
        // echo 'vlrjuros - k15_posjur : '.$k15_posjur.' { i : '.(substr($k15_posjur,0, 3)-1).' | q : '.substr($k15_posjur,3, 3).' }<br>';
        // echo 'vlrmulta - k15_posmul : '.$k15_posmul.' { i : '.(substr($k15_posmul,0, 3)-1).' | q : '.substr($k15_posmul,3, 3).' }<br>';
        // echo 'vlracres - k15_posacr : '.$k15_posacr.' { i : '.(substr($k15_posacr,0, 3)-1).' | q : '.substr($k15_posacr,3, 3).' }<br>';
        // echo 'vlrdesco - k15_posdes : '.$k15_posdes.' { i : '.(substr($k15_posdes,0, 3)-1).' | q : '.substr($k15_posdes,3, 3).' }<br>';
        // echo 'convenio - k15_poscon : '.$k15_poscon.' { i : '.(substr($k15_poscon,0, 3)-1).' | q : '.substr($k15_poscon,3, 3).' }<br>';
        // echo 'cedente  - k15_posced : '.$k15_posced.' { i : '.(substr($k15_posced,0, 3)-1).' | q : '.substr($k15_posced,3, 3).' }<br>';

        // echo ($arq_array[$i] + 1).' - '.$arq_array[$i].'<br>';
        // echo 'debcta - '.$debcta.' | numbco - '.$numbco.' | dtpago - '.$dtpago.' | vlrpago - '.$vlrpago.' | vlrjuros - '.$vlrjuros.' | vlrmulta - '.$vlrmulta.' | vlracres - '.$vlracres.' | vlrdesco - '.$vlrdesco.' | convenio - '.$convenio.' | cedente - '.$cedente.' | '.$tiporet.'<br>';
        // echo '</pre>';
        //exit;
        
        if ($debcta != "") {

          $sqldebcontapedido = $cldebcontapedido->sql_query("","d63_codigo",""," d63_codigo = $debctaped AND trim(d63_idempresa) = '" . $debcta . "'");
      
          if ($_debug) {
            
            echo "  >>  passo 1 <br>";
            flush();
          }
          
          $resultdebcontapedido = $cldebcontapedido->sql_record($sqldebcontapedido);
          if ($cldebcontapedido->numrows == 0) {
            
            $erro_msg = "Não foi encontrado Cadastro no Debito em Conta com ID Empresa (".trim($debcta)."). Arquivo Recusado!!";
            $sqlerro  = true;
            break;
          }
          db_fieldsmemory($resultdebcontapedido,0);
          /*

          ESSA PORCAO DE CODIGO FOI UTILIZADA PARA PROCESSAMENTO DE ARQUIVO DE RETORNO SEM REMESSA GERADA NO DBPORTAL2(utilizado no DAEB para processar remessas geradas no sistema anterior)

          $sqlprocura = "select d68_matric from debcontapedidomatric where d68_codigo = $d63_codigo";
          $resultprocura = db_query($sqlprocura) or die($sqlprocura);
          if (pg_numrows($resultprocura) == 0) {
            //die("\nerro 1\n");
            die($sqlprocura);
          }
          db_fieldsmemory($resultprocura,0);

          $sqlprocura = "
          select distinct numpre, numpar from(select arrecad.k00_numpre as numpre,
          arrecad.k00_numpar as numpar
          from arrematric
          inner join arrecad on arrecad.k00_numpre = arrematric.k00_numpre
          where k00_matric = $d68_matric and k00_tipo = 37 and k00_numpar = 2
          union
          select arrecant.k00_numpre as numpre,
          arrecant.k00_numpar as numpar
          from arrematric
          inner join arrecant on arrecant.k00_numpre = arrematric.k00_numpre
          where k00_matric = $d68_matric and k00_tipo = 37 and k00_numpar = 2
          ) as x
          ";
          $resultprocura = db_query($sqlprocura) or die($sqlprocura);
          if (pg_numrows($resultprocura) > 0) {
            db_fieldsmemory($resultprocura,0);
          } else {
            //die("\nerro 2\n");
            die($sqlprocura);
          }
          */

          $numpre = substr($arq_array[$i],73,8);
          $numpar = substr($arq_array[$i],82,3);

          // Efetuar a troca do NUMPRE pela work_arreinstit
          //
          // D A E B
          //
          if ($cgc == '90940172000138') {
            //
            $sqlwork  = "select k00_numpre_dst ";
            $sqlwork .= "  from work_arreinstit ";
            $sqlwork .= " where k00_numpre_ori = {$numpre} ";

            $rsWork = pg_query($sqlwork);

            if(pg_numrows($rsWork)>0) {
              db_fieldsmemory($rsWork, 0);
              $numpre = $k00_numpre_dst;
            }
          }

          $sqlarrecad  = "select k00_dtvenc, k00_tipo from arrecad where k00_numpre = $numpre and k00_numpar = $numpar ";
          $sqlarrecad .= "union ";
          $sqlarrecad .= "select k00_dtvenc, k00_tipo from arrecant where k00_numpre = $numpre and k00_numpar = $numpar ";
          $sqlarrecad .= "union ";
          $sqlarrecad .= "select k00_dtvenc, k00_tipo from arreold where k00_numpre = $numpre and k00_numpar = $numpar ";

          //die($sqlarrecad);
          if ($_debug) {
            
            echo "  >>  passo 2 <br>";
            flush();
          }

          $resultarrecad = db_query($sqlarrecad) or die($sqlarrecad);
          if (pg_numrows($resultarrecad) == 0) {
            
            $sqlerro = true;
            $erro_msg = "linha: ".($i+1)." idempresa: $debcta numpre: $numpre - numpar: $numpar - tipo: $k00_tipo nao encontrado em arrecad/arrecant/arreold";
            break;
          } else {
            db_fieldsmemory($resultarrecad, 0);
          }

          if ($sqlerro == false) {
            $sql = $cldebcontaarquivo->sql_query_tipo("", "distinct d72_nsa, d72_codigo", "",
                                                 "    d72_tipo   = 1
                                                  and d72_numpar = $k00_numpar
                                                  and d72_banco  = $d63_banco
                                                  and d72_instit = ".db_getsession("DB_instit")."
                                                  and case
                                                        when d79_arretipo is not null then
                                                          d79_arretipo = $k00_tipo
                                                        else
                                                          d72_arretipo = $k00_tipo
                                                      end
                                                  ");
            if ($_debug) {
              
              echo "  >>  passo 3 <br>";
              flush();
              // if ($i == 40) {
              //   echo "$sql <br>";
              // }
            }

            $resultdebcontaarquivo = $cldebcontaarquivo->sql_record($sql);

            if ($cldebcontaarquivo->numrows == 0) {
              
              $erro_msg = "linha: ".($i+1)." idempresa: $debcta numpar: $numpar - tipo: 1 - arretipo: $k00_tipo - banco: $d63_banco nao encontrado no debcontaarquivo";
              $sqlerro = true;
              break;
            } else {
              db_fieldsmemory($resultdebcontaarquivo, 0);
            }

            //$sqldebcontaarquivo = $cldebcontaarquivo->sql_query("","d72_nsa",""," d72_tipo = 2 and d72_numpar = $numpar and d72_arretipo = $k00_tipo and d72_banco = $d63_banco");
            $sqldebcontaarquivo  = "select count(*) as d99_conta ";
            $sqldebcontaarquivo .= "  from debcontaarquivo ";
            $sqldebcontaarquivo .= "       inner join debcontaarquivoreg     on d73_codigo = d72_codigo ";
            $sqldebcontaarquivo .= "       inner join debcontaarquivoregret  on d76_debcontaarqreg  = d73_sequencial ";
            $sqldebcontaarquivo .= "                                        and d76_debcontatiporet = 0 ";
            $sqldebcontaarquivo .= "       inner join arretipo               on arretipo.k00_tipo = debcontaarquivo.d72_arretipo ";
            $sqldebcontaarquivo .= "       inner join bancos                 on bancos.codbco     = debcontaarquivo.d72_banco ";
            $sqldebcontaarquivo .= "       inner join cadtipo                on cadtipo.k03_tipo  = arretipo.k03_tipo ";
            $sqldebcontaarquivo .= " where d72_tipo     = 2 ";
            $sqldebcontaarquivo .= "   and d72_numpar   = $numpar ";
            $sqldebcontaarquivo .= "   and d72_arretipo = $k00_tipo ";
            $sqldebcontaarquivo .= "   and d72_banco    = $d63_banco ";
            $sqldebcontaarquivo .= "   and d72_instit   = ".db_getsession("DB_instit");
            $sqldebcontaarquivo .= "   and extract(year from d72_data) = ".db_getsession("DB_anousu");

            if ($_debug) {
              
              echo "  >>  passo 4 <br>";
              flush();
            }
            
            $resultdebcontaarquivo = $cldebcontaarquivo->sql_record($sqldebcontaarquivo);
            db_fieldsmemory($resultdebcontaarquivo, 0);
            
            if ($d99_conta == 0 && $verifica_arq==false) {
                $verifica_arq = true;
                $cldebcontaarquivo->d72_nsa      = $d72_nsa;
                $cldebcontaarquivo->d72_tipo     = 2;
                // retorno
                $cldebcontaarquivo->d72_data     = $cldisarq->dtretorno;
                $cldebcontaarquivo->d72_hora     = db_hora();
                $cldebcontaarquivo->d72_usuario  = $cldisarq->id_usuario;
                $cldebcontaarquivo->d72_nome     = $arq_name;
                $cldebcontaarquivo->d72_conteudo = implode("", $arq_array);
                $cldebcontaarquivo->d72_numpar   = $numpar;
                $cldebcontaarquivo->d72_arretipo = $k00_tipo;
                $cldebcontaarquivo->d72_banco    = $d63_banco;
                $cldebcontaarquivo->d72_instit   = $instit;

                if ($_debug) {
                  
                  echo "  >>  passo 5 <br>";
                  flush();
                }

                $cldebcontaarquivo->incluir(null);
                if ($cldebcontaarquivo->erro_status == 0) {
                  
                  $sqlerro = true;
                  $erro_msg = "debcontaarquivo - " . $cldebcontaarquivo->erro_msg;
                  break;
                }

              }

              $cldebcontaarquivoreg->d73_codigo = $d72_codigo;
              $cldebcontaarquivoreg->d73_tipo   = 2;
              ///// retorno do envio
              if ($_debug) {
                echo "  >>  passo 6 <br>";
                flush();
              }

              $cldebcontaarquivoreg->incluir(null);
              if ($cldebcontaarquivoreg->erro_status == 0) {
                
                $sqlerro = true;
                $erro_msg = "debcontaarquivoreg - " . $cldebcontaarquivoreg->erro_msg;
                break;
              }
              
              if ($sqlerro == false) {

                $cldebcontaarquivoregret->d76_debcontatiporet = $tiporet;
                $cldebcontaarquivoregret->d76_debcontaarqreg  = $cldebcontaarquivoreg->d73_sequencial;

                if ($_debug) {
                  
                  echo "  >>  passo 7 <br>";
                  flush();
                }

                $cldebcontaarquivoregret->incluir(null);
                if ($cldebcontaarquivoregret->erro_status == 0) {
                  
                  $sqlerro = true;
                  $erro_msg = "debcontaarquivoregret - " . $cldebcontaarquivoregret->erro_msg;
                  break;
                }

                if ($sqlerro == false) {

                  $cldebcontaarquivoregmov->d75_codigo  = $cldebcontaarquivoreg->d73_sequencial;
                  $cldebcontaarquivoregmov->d75_venc  = $k00_dtvenc;
                  $cldebcontaarquivoregmov->d75_valor = $vlrpago+$vlrjuros+$vlrmulta+$vlracres-$vlrdesco;
                  $cldebcontaarquivoregmov->d75_numpar  = $numpar;

                  if ($_debug) {
                    
                    echo "  >>  passo 8 <br>";
                    flush();
                  }

                  $cldebcontaarquivoregmov->incluir(null);
                  if ($cldebcontaarquivoregmov->erro_status == 0) {
                    
                    $sqlerro = true;
                    $erro_msg = "debcontaarquivoregmov - " . $cldebcontaarquivoregmov->erro_msg;
                    break;
                  }

                  if ($sqlerro == false) {

                    $cldebcontaarquivoregped->d80_arquivoreg  = $cldebcontaarquivoreg->d73_sequencial;
                    $cldebcontaarquivoregped->d80_pedido  = $d63_codigo;

                    if ($_debug) {
                      
                      echo "  >>  passo 9 <br>";
                      flush();
                    }

                    $cldebcontaarquivoregped->incluir(null);
                    if ($cldebcontaarquivoregped->erro_status == 0) {
                      
                      $sqlerro = true;
                      $erro_msg = "debcontaarquivoregped - " . $cldebcontaarquivoregped->erro_msg;
                      break;
                    }

                  }

                }

              }

            }

          } else {

            $k15_numpre = $k15_numpreori;
            $k15_numpar = $k15_numparori;
            $numpre     = substr($arq_array[$i],substr($k15_numpre,0,3)-1,substr($k15_numpre,3,3));
            $numpar     = substr($arq_array[$i],substr($k15_numpar,0,3)-1,substr($k15_numpar,3,3));
          }

          if ( ($sqlerro == false) && ( $cldebcontaarquivoregret->d76_debcontatiporet == "00" or $cldebcontaarquivoregret->d76_debcontatiporet == "31" ) ) {

            if ( $numpre > 40000000 and $cgc == '28521748000159' ) {

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

              if ( $k15_codbco == 1 && $k15_codage == "0728C" ) {
                /**
                 * Função de tratamento honorarios
                */

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

              }

            }

            $cldisbanco->codret     = $cldisarq->codret;
            $cldisbanco->k15_codbco = $k15_codbco;
            $cldisbanco->k15_codage = $k15_codage;
            $cldisbanco->k00_numbco = $numbco;
            $cldisbanco->dtarq      = $dtarq;
            $cldisbanco->dtpago     = $dtpago;
            $cldisbanco->vlrpago    = $vlrpago;
            $cldisbanco->vlrjuros   = "$vlrjuros";
            $cldisbanco->vlrmulta   = $vlrmulta;
            $cldisbanco->vlracres   = $vlracres;
            $cldisbanco->vlrdesco   = $vlrdesco;
            $cldisbanco->cedente    = $cedente;
            $cldisbanco->vlrtot     = $vlrpago+$vlrjuros+$vlrmulta+$vlracres-$vlrdesco;
            $cldisbanco->vlrcalc    = $vlrpago+$vlrjuros+$vlrmulta+$vlracres-$vlrdesco;
            $cldisbanco->classi     = "false";
            $cldisbanco->k00_numpre = $numpre+0;
            $cldisbanco->k00_numpar = $numpar+0;
            $cldisbanco->convenio   = $convenio;
            $cldisbanco->k00_numbco = "0";
            $cldisbanco->instit     = db_getsession("DB_instit");
            $cldisbanco->dtcredito  = $dtpago;

            if ($_debug) {
              echo "  >>  passo 10 <br>";
              flush();
            }
            
            $cldisbanco->incluir(null);
            if ($cldisbanco->erro_status==0) {
              $sqlerro = true;
              $erro_msg = "disbanco - " . $cldisbanco->erro_msg;
              break;
            }

          } else {
            $valor_nao_processado += $vlrpago;
          }

        }

      }

      if ($sqlerro == true) {

        db_fim_transacao(true);        
        $alert = "alert('Ocorreu algum erro durante o processamento!\\nErro: $erro_msg')";
        echo "<script>$alert;</script>";
        db_redireciona();
      } else {

        $sql = "select dtarq, sum(vlrpago) from disbanco where codret = " . $cldisbanco->codret .  " group by dtarq";
        $result = db_query($sql);

        $total = 0;
        $_msg = "";
        for ($x = 0; $x < pg_numrows($result); $x++) {
          
          db_fieldsmemory($result,$x,true);
          $_msg .= "\\nCodRet: ".$cldisarq->codret."\\nData: $dtarq\\nValor Processado: R$" . db_formatar($sum,"f");
          $total += $sum;
        }
        $_msg .= "\\nValor Nao Processado: R$" . db_formatar($valor_nao_processado,"f");
        $_msg .= "\\nTotal Arquivo: R$" . db_formatar($total + $valor_nao_processado,"f");

        if ($achou_arrecant == 0) {
          
          db_fim_transacao($sqlerro);

          if (isset($emiterel)){
            ?><script>
            var iCodRet     = <?php echo $cldisarq->codret;
            echo "
            var oParametros = {
              codret       : iCodRet
            };
            alert('Arquivo Processado com SUCESSO!!\\n$_msg');
                  new EmissaoRelatorio('arr2_reldebcontapedidobcoagencia003.php', oParametros).open();
            </script>";
          } else {
            echo "<script>alert('Arquivo Processado com SUCESSO!!\\n$_msg');</script>";
          }
        } else {
          echo "<script>alert('Arquivo Nao Processado porque existem pagamentos!')';</script>";
        }

      }

  } else {
    db_fim_transacao(true);        
    $alert = "alert('Ocorreu algum erro durante o processamento!\\nErro: $erro_msg')";
    echo "<script>$alert;</script>";
    db_redireciona();
  }

}
?>
