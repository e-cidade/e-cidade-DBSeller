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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_fis_retauto_classe.php"));
require_once(modification("classes/db_fis_retautolevanta_classe.php"));
require_once(modification("classes/db_fis_enderecopecas_classe.php"));

$clenderecopecas  = new cl_fis_enderecopecas;

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ERROR);

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clarrecad                       = new cl_arrecad;
$clarrecant                      = new cl_arrecant;
$clauto                          = new cl_fis_auto;
$clautoandam                     = new cl_fis_autoandam;
$clautocgm                       = new cl_fis_autocgm;
$clautoexec                      = new cl_fis_autoexec;
$clautofiscal                    = new cl_fis_autofiscal;
$clautoinscr                     = new cl_fis_autoinscr;
$clautolevanta                   = new cl_fis_autolevanta;
$clautolocal                     = new cl_fis_autolocal;
$clautomatric                    = new cl_fis_automatric;
$clautonumpre                    = new cl_fis_autonumpre;
$clautorec                       = new cl_fis_autorec;
$clautosanitario                 = new cl_fis_autosanitario;
$clautotestem                    = new cl_fis_autotestem;
$clautotipo                      = new cl_fis_autotipo;
$clautotipobaixa                 = new cl_fis_autotipobaixa;
$clautotipobaixaproc             = new cl_fis_autotipobaixaproc;
$clautotipobaixaprocproc         = new cl_fis_autotipobaixaprocproc;
$clautoultandam                  = new cl_fis_autoultandam;
$clautousu                       = new cl_fis_autousu;
$clcancdebitos                   = new cl_cancdebitos;
$clcancdebitosconcarpeculiar     = new cl_cancdebitosconcarpeculiar;
$clcancdebitosproc               = new cl_cancdebitosproc;
$clcancdebitosprocconcarpeculiar = new cl_cancdebitosprocconcarpeculiar;
$clcancdebitosprocreg            = new cl_cancdebitosprocreg;
$clcancdebitosprot               = new cl_cancdebitosprot;
$clcancdebitosreg                = new cl_cancdebitosreg;
$clfandam                        = new cl_fis_fandam;
$clfandamusu                     = new cl_fis_fandamusu;
$clfiscalcgm                     = new cl_fis_fiscalcgm;
$clfiscalinscr                   = new cl_fis_fiscalinscr;
$clfiscalmatric                  = new cl_fis_fiscalmatric;
$clfiscalsanitario               = new cl_fis_fiscalsanitario;
$cllevanta                       = new cl_fis_levanta;
$cllevantanotas                  = new cl_fis_levantanotas;
$cllevcgm                        = new cl_fis_levcgm;
$cllevinscr                      = new cl_fis_levinscr;
$cllevusu                        = new cl_fis_levusu;
$cllevvalor                      = new cl_fis_levvalor;
$cllevvalorpgtos                 = new cl_fis_levvalorpgtos;
$clparagrafoauto                 = new cl_fis_fiscalparagrafoauto;
$clparfiscal                     = new cl_fis_parfiscal;
$clprocfiscalauto                = new cl_fis_procfiscalauto;
$clprocfiscallevanta             = new cl_fis_procfiscallevanta;
$cltipofiscaliza                 = new cl_fis_tipofiscaliza;
$clretauto                       = new cl_fis_retauto;
$clretautolevanta                = new cl_fis_retautolevanta;

$db_opcao = 22;
$db_botao = false;

if(isset($chavepesquisa) || isset($y50_codauto)){
  $db_opcao = 2;
  $result   = $clauto->sql_record($clauto->sql_query($chavepesquisa,"*",null) );
  if ($clauto->numrows>0){
   db_fieldsmemory($result,0);
  }
  $result = $clautolocal->sql_record($clautolocal->sql_query($chavepesquisa,"*"));
  if($clautolocal->numrows > 0){
   db_fieldsmemory($result,0);
  }else{
    $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = 'A'");
    db_fieldsmemory($rsEndPecas,0);
  }
  $result = $clautoexec->sql_record($clautoexec->sql_query($chavepesquisa,"fis_autoexec.* , j14_nome as j14_nome_exec,j13_descr as j13_descr_exec"));
  if($clautoexec->numrows > 0){
   db_fieldsmemory($result,0);
  }
  $db_botao = true;
  $sqlprocfiscal = "select y111_procfiscal as procfiscal,z01_nome as nome
                    from fiscalizacao.fis_procfiscalauto
                        inner join fiscalizacao.fis_procfiscalcgm on y111_procfiscal = y101_procfiscal
                        inner join cgm           on y101_numcgm     = z01_numcgm
                    where y111_auto = $chavepesquisa ";

  $resultprocfiscal = db_query($sqlprocfiscal);
  $linhasprocfiscal = pg_num_rows($resultprocfiscal);

  if($linhasprocfiscal>0){
    db_fieldsmemory($resultprocfiscal,0);
  }else{
    $nome="";
  }

  $sqlProcesso = "SELECT *,p58_numero||'/'||p58_ano as procadm from fiscalizacao.fis_procfiscalauto
       INNER JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
       INNER JOIN fiscalizacao.fis_procfiscalprot
          ON  y100_sequencial = y105_procfiscal
       INNER JOIN protprocesso
          ON p58_codproc = y105_protprocesso
       WHERE y111_auto =".$chavepesquisa;

  $resultProcesso = db_query($sqlProcesso);
  $linhasProcesso = pg_num_rows($resultProcesso);

  if($linhasProcesso>0){
    db_fieldsmemory($resultProcesso,0);
  }
}

//-------------------------------/Busca Cgm e verifica se é por cgm,matricula,inscr,sanitario ou notificação/-----------------
if ($db_opcao==2||$db_opcao==3){

  $sSql = $clauto->sql_query_busca_tipo($y50_codauto);
  $result_ident = $clauto->sql_record($sSql);

  if($clauto->numrows>0){
    db_fieldsmemory($result_ident,0);
    $cod    = $dl_codigo;
    $inform = $dl_identificacao;

    // 1 = Inscrição
    // 2 = Matrícula
    // 3 = Sanitário
    // 4 = Cgm
    // 5 = Notificação
    // 6 = Nenhum
    if ($dl_identificacaotipo==1){
      $abre = "iss3_consinscr003.php?numeroDaInscricao";
      $q02_inscr = $dl_codigo;
      $quem = 'Inscrição';
    }else if ($dl_identificacaotipo==2){
      $abre = "cad3_conscadastro_002.php?cod_matricula";
      $j01_matric = $dl_codigo;
      $quem = 'Matricula';
    }else if ($dl_identificacaotipo==3){
      $abre = "fis3_fis_consultasani002.php?y80_codsani";
      $y80_codsani = $dl_codigo;
      $quem = 'Sanitário';
    }else if ($dl_identificacaotipo==4){
      $abre = "prot3_conscgm002.php?fechar=func_nome&numcgm";
      $z01_numcgm = $dl_codigo;
      $quem = 'CGM';
    }else if ($dl_identificacaotipo==5){
       $abre = "fis3_fis_fiscal006.php?y30_codnoti";
       $y30_codnoti = $dl_codigo;
       $quem = 'Noti';
    }
  }
}

if (isset($cod)&&$cod!=""){
 $dados = "<a onClick=\"js_abre('".$abre."=$cod');return false;\" href=''>".$inform.": ".$cod." &nbsp;|&nbsp;".@$z01_nome."</a>";
}

if(isset($retificar)){

  // ------------------------------------------------------------------------------
  // - Inicio da inclusão do auto
  // -

  // $_SESSION['log'] = 1; // apagar depois
  // db_fim_transacao(true); // apagar depois

  db_inicio_transacao();
  $sqlerro = false;


  $levSql = $clautolevanta->sql_query(""," fis_autolevanta.*",""," y117_auto = $y50_codauto");
  $levRs  = db_query($levSql);

  /* Busca numpre dos levantamentos */
  $sLevWhere  = " in (select y66_numpre from fiscalizacao.fis_autolevanta ";
  $sLevWhere .= "           inner join fiscalizacao.fis_levanta on y60_codlev = y117_levanta ";
  $sLevWhere .= "           inner join fiscalizacao.fis_levnumpre on y66_codlev = y60_codlev where y117_auto = $y50_codauto)";

   /* Busca numpre do Auto */
  $sAutoWhere = " in (select y17_numpre from fiscalizacao.fis_autonumpre where y17_codauto = $y50_codauto)";

  /* ----------------- [ Verifica se esta na cobrança administrativa ] -----------------] */
  $sQueryDivLev = db_query("select * from divold where k10_numpre $sLevWhere");
  if( pg_num_rows( $sQueryDivLev ) > 0 ){
    $sqlerro = true;
    $erro    = "Auto foi para cobrança administrativa, não pode ser retificado.";
  }
  $sQueryDivAu = db_query("select * from divold where k10_numpre $sAutoWhere");
  if( pg_num_rows( $sQueryDivAu ) > 0  && $sqlerro == false ){
    $sqlerro = true;
    $erro    = "Auto foi para cobrança administrativa, não pode ser retificado.";
  }

  /* ----------------- [ Verifica se esta na diversos ] -----------------] */
  $sQueryLev = db_query("select * from diversos inner join termodiver on dv10_coddiver = dv05_coddiver where dv05_numpre $sLevWhere");
  if( pg_num_rows( $sQueryLev ) > 0 && $sqlerro == false ){
    $sqlerro = true;
    $erro    = "Auto foi parcelado como diversos, não pode ser retificado.";
  }
  $sQueryAu = db_query("select * from diversos inner join termodiver on dv10_coddiver = dv05_coddiver where dv05_numpre $sAutoWhere");
  if( pg_num_rows( $sQueryAu ) > 0  && $sqlerro == false ){
    $sqlerro = true;
    $erro    = "Auto foi parcelado como diversos, não pode ser retificado.";
  }
  /* ----------------- [ Verifica se em pagamento ] -----------------] */
  $sQueryLev = db_query("select * from arrepaga where k00_numpre $sLevWhere");
  if( pg_num_rows( $sQueryLev ) > 0 && $sqlerro == false ){
    $sqlerro = true;
    $erro    = "Auto está em pagamento, não pode ser retificado.";
  }
  $sQueryAu = db_query("select * from arrepaga where k00_numpre $sAutoWhere");
  if( pg_num_rows( $sQueryAu ) > 0  && $sqlerro == false ){
    $sqlerro = true;
    $erro    = "Auto está em pagamento, não pode ser retificado.";
  }



  // variaveis para o auto
  $clauto->y50_data     = $y50_data;
  $clauto->y50_hora     = $y50_hora;
  $clauto->y50_obs      = $y50_obs;
  $clauto->y50_setor    = $y50_setor;
  $clauto->y50_nome     = $y50_nome;
  $clauto->y50_dtvenc   = $y50_dtvenc;
  $clauto->y50_numbloco = $y50_numbloco;
  $clauto->y50_prazorec = $y50_prazorec;
  $clauto->y50_codtipo  = $y50_codtipo;
  $clauto->y50_instit   = db_getsession('DB_instit');

  // guardando o auto antigo
  $antigo = $y50_codauto;

  // resetando a variavel do auto
  $y50_codauto = '';

  $clauto->incluir($y50_codauto);

  if ($clauto->erro_status==0) {
    $erro    = 'Erro Codigo: 1\\n\\n'.$clauto->erro_msg;
    $sqlerro = true;
  }

  $novoAuto = $clauto->y50_codauto;
  if ($sqlerro==false){

    if($procfiscal !=""){

      $clprocfiscalauto->y111_procfiscal = $procfiscal;
      $clprocfiscalauto->y111_auto       = $novoAuto;
      $clprocfiscalauto->incluir(null);

      if ($clprocfiscalauto->erro_status==0) {
        $erro    = 'Erro Codigo: 2\\n\\n'.$clprocfiscalauto->erro_msg;
        $sqlerro = true;
      }

    }

  }

  if ($sqlerro==false){

    if ($clautolocal->numrows > 0 ){

      $clautolocal->y14_codauto=$novoAuto;
      $clautolocal->y14_codigo=$y14_codigo;
      $clautolocal->y14_codi=@$y14_codi;
      $clautolocal->y14_numero=$y14_numero;
      $clautolocal->y14_compl=$y14_compl;
      $clautolocal->incluir($novoAuto);
      if ($clautolocal->erro_status==0) {
        $erro    = 'Erro Codigo: 3\\n\\n'.$clautolocal->erro_msg;
        $sqlerro = true;
      }

  }else{

      $clenderecopecas->end01_codpeca  = $novoAuto;
      $clenderecopecas->end01_tipopeca = "A";
      $clenderecopecas->end01_rua      = $end01_rua;
      $clenderecopecas->end01_numero   = $end01_numero;
      $clenderecopecas->end01_compl    = $end01_compl;
      $clenderecopecas->end01_bairro   = $end01_bairro;

      $clenderecopecas->incluir();

      if( $clenderecopecas->erro_status == "0" ){
        $sqlerro = true;
        $erro    = $clenderecopecas->erro_msg;
      }
    }
  }

  if ($sqlerro==false){

    $clautoexec->y15_codauto=$novoAuto;
    $clautoexec->y15_codigo=$y15_codigo;
    $clautoexec->y15_codi=$y15_codi;
    $clautoexec->y15_numero=$y15_numero;
    $clautoexec->y15_compl=$y15_compl;
    $clautoexec->incluir($novoAuto);
    if ($clautoexec->erro_status==0) {
      $erro    = 'Erro Codigo: 4\\n\\n'.$clautoexec->erro_msg;
      $sqlerro = true;
    }
  }

  if ($sqlerro==false){
    if(isset($j01_matric) && $j01_matric != ""){
      $clautomatric->y53_matric=$j01_matric;
      $clautomatric->incluir($novoAuto);
      if ($clautomatric->erro_status==0) {
        $erro    = 'Erro Codigo: 5\\n\\n'.$clautomatric->erro_msg;
        $sqlerro = true;
      }
    }elseif(isset($q02_inscr)  && $q02_inscr  != ""){

      $clautoinscr->y52_inscr=$q02_inscr;
      $clautoinscr->incluir($novoAuto);
      if ($clautoinscr->erro_status==0) {
        $erro    = 'Erro Codigo: 6\\n\\n'.$clautoinscr->erro_msg;
        $sqlerro = true;
      }
    }elseif(isset($y80_codsani)  && $y80_codsani  != ""){

      $clautosanitario->y55_codsani=$y80_codsani;
      $clautosanitario->incluir($novoAuto);
      if ($clautosanitario->erro_status==0) {
        $erro    = 'Erro Codigo: 7\\n\\n'.$clautosanitario->erro_msg;
        $sqlerro = true;
      }
    }elseif(isset($y30_codnoti)  && $y30_codnoti  != ""){

      $clautofiscal->y51_codnoti=$y30_codnoti;
      $clautofiscal->incluir($novoAuto);
      if ($clautofiscal->erro_status==0) {
        $erro    = 'Erro Codigo: 8\\n\\n'.$clautofiscal->erro_msg;
        $sqlerro = true;
      }else{

        /**
         * Verifica a origem da notificação
         */
        //matricula
        $rsMatric = $clfiscalmatric->sql_record($clfiscalmatric->sql_query($y30_codnoti));
        if ( $clfiscalmatric->numrows > 0 ) {

          $oFiscalMatric = db_utils::fieldsmemory($rsMatric,0);
          $clautomatric->y53_matric=$oFiscalMatric->y35_matric;
          $clautomatric->incluir($novoAuto);
          if ($clautomatric->erro_status==0) {
            $erro    = 'Erro Codigo: 9\\n\\n'.$clautomatric->erro_msg;
            $sqlerro = true;
          }
        }

        //inscrição
        $rsInscr = $clfiscalinscr->sql_record($clfiscalinscr->sql_query($y30_codnoti));
        if ( $clfiscalinscr->numrows > 0 ) {

          $oFiscalInscr = db_utils::fieldsmemory($rsInscr,0);
          $clautoinscr->y52_inscr=$oFiscalInscr->q02_inscr;
          $clautoinscr->incluir($novoAuto);
          if ($clautoinscr->erro_status==0) {
            $erro    = 'Erro Codigo: 10\\n\\n'.$clautoinscr->erro_msg;
            $sqlerro = true;
          }
        }

        //sanitario
        $rsSanitario = $clfiscalsanitario->sql_record($clfiscalsanitario->sql_query($y30_codnoti));
        if ( $clfiscalsanitario->numrows > 0 ) {

          $oFiscalSanitario = db_utils::fieldsmemory($rsSanitario,0);
          $clautosanitario->y55_codsani=$oFiscalSanitario->y80_codsani;
          $clautosanitario->incluir($novoAuto);
          if ($clautosanitario->erro_status==0) {
            $erro    = 'Erro Codigo: 11\\n\\n'.$clautosanitario->erro_msg;
            $sqlerro = true;
          }
        }

        //cgm
        $rsCgm = $clfiscalcgm->sql_record($clfiscalcgm->sql_query($y30_codnoti));
        if ( $clfiscalcgm->numrows > 0 ) {

          $oFiscalCgm = db_utils::fieldsmemory($rsCgm,0);
          $clautocgm->y54_numcgm = $oFiscalCgm->y36_numcgm;
          $clautocgm->incluir($novoAuto);
          if ($clautocgm->erro_status==0) {
            $erro    = 'Erro Codigo: 12\\n\\n'.$clautocgm->erro_msg;
            $sqlerro = true;
          }
        }

      }
    }else{

      if(isset($z01_numcgm) && $z01_numcgm != ""){

        $clautocgm->y54_numcgm=$z01_numcgm;
        $clautocgm->incluir($novoAuto);
        if ($clautocgm->erro_status==0) {
          $erro    = 'Erro Codigo: 13\\n\\n'.$clautocgm->erro_msg;
          $sqlerro = true;
        }
      }

    }
  }

  if($sqlerro==false){

    $paSql  = "select * from fiscalizacao.fis_paragrafoauto where pl10_auto = $antigo";
    $paRs   = db_query($paSql);
    $paRows = pg_num_rows($paRs);

    if($paRows > 0){

      for ($i=0; $i < $paRows; $i++) {
        db_fieldsmemory($paRs, $i);
        $clparagrafoauto->pl10_auto      = $novoAuto;
        $clparagrafoauto->pl10_paragrafo = $pl10_paragrafo;
        $clparagrafoauto->pl10_texto     = addslashes($pl10_texto);
        $clparagrafoauto->pl10_usu       = $pl10_usu;
        $clparagrafoauto->incluir();

        if($clparagrafoauto->erro_status == 0){
          $erro    = 'Erro Codigo: 14\\n\\n'.$clparagrafoauto->erro_msg;
          $sqlerro = true;
          break;
        }

      }

    }

  }

  // -
  // - Fim da inclusão do auto
  // ------------------------------------------------------------------------------


  // ------------------------------------------------------------------------------
  // - Inicio da inclusão do Levantamento
  // -

  $levantamentoAntigo = array();
  $levantamentoNovo   = array();


  $levSql = $clautolevanta->sql_query(""," fis_autolevanta.*",""," y117_auto = $antigo");
  $levRs  = db_query($levSql);

  if(pg_num_rows($levRs) > 0){




    for ($il=0; $il < pg_num_rows($levRs); $il++){

      db_fieldsmemory($levRs, $il);
      $levantamentoAntigo[] = $y117_levanta;

      $result = $cllevanta->sql_record($cllevanta->sql_query_inf($y117_levanta));
      // echo $cllevanta->sql_query_inf($y117_levanta); exit;

      db_fieldsmemory($result,0);

      // if($y60_importado == 't'){
      //   $nops = true;
      //   // $db_botao = false;
      // }else{
      //   // $db_botao = true;
      // }

      // $sqlprocfiscal = "select y112_procfiscal as procfiscal,z01_nome as nome
      //                    from fiscalizacao.fis_procfiscallevanta
      //                         inner join fiscalizacao.fis_procfiscalcgm on y112_procfiscal = y101_procfiscal
      //                         inner join cgm on y101_numcgm=z01_numcgm
      //                   where y112_levanta = $chavepesquisa";
      // $resultprocfiscal = db_query($sqlprocfiscal);
      // $linhasprocfiscal = pg_num_rows($resultprocfiscal);
      // if($linhasprocfiscal>0){
      //   db_fieldsmemory($resultprocfiscal,0);
      // }else{
      //   $nome = "";
      // }

      // Definindo variaveis

      $y60_codlev = '';
      // $cllevanta->y60_data_dia   = $y60_data_dia;
      // $cllevanta->y60_data_mes   = $y60_data_mes;
      // $cllevanta->y60_data_ano   = $y60_data_ano;
      $cllevanta->y60_data       = $y60_data;
      $cllevanta->y60_contato    = $y60_contato;
      // $cllevanta->y60_dtini_dia  = $y60_dtini_dia;
      // $cllevanta->y60_dtini_mes  = $y60_dtini_mes;
      // $cllevanta->y60_dtini_ano  = $y60_dtini_ano;
      $cllevanta->y60_dtini      = $y60_dtini;
      // $cllevanta->y60_dtfim_dia  = $y60_dtfim_dia;
      // $cllevanta->y60_dtfim_mes  = $y60_dtfim_mes;
      // $cllevanta->y60_dtfim_ano  = $y60_dtfim_ano;
      $cllevanta->y60_dtfim      = $y60_dtfim;
      $cllevanta->y60_obs        = $y60_obs;
      $cllevanta->y60_importado  = 'f';
      $cllevanta->y60_proces     = ($y60_proces == '') ? 123456 : $y60_proces;
      $GLOBALS["HTTP_POST_VARS"]["y60_espontaneo"] = $y60_espontaneo;
      $cllevanta->y60_espontaneo = $y60_espontaneo;

      $cllevanta->incluir($y60_codlev);
      $y60_codlev = $cllevanta->y60_codlev;

      $levantamentoNovo[] = $y60_codlev;

      if ($cllevanta->erro_status==0) {
        $erro    = 'Erro Codigo: 15\\n\\n'.$cllevanta->erro_msg;
        $sqlerro = true;
      }

      if($sqlerro == false){
        if($procfiscal != ""){
          $clprocfiscallevanta->y112_procfiscal = $procfiscal;
          $clprocfiscallevanta->y112_levanta    = $y60_codlev;
          $clprocfiscallevanta->incluir(null);
          if ($clprocfiscallevanta->erro_status==0) {
            $erro    = 'Erro Codigo: 16\\n\\n'.$clprocfiscallevanta->erro_msg;
            $sqlerro = true;
          }
        }
      }

      if(!$sqlerro){
        if($q02_inscr != ''){
          $cllevinscr->y62_inscr  = $q02_inscr;
          $cllevinscr->y62_codlev = $y60_codlev;
          $cllevinscr->incluir($y60_codlev,$q02_inscr);
          if ($cllevinscr->erro_status==0) {
            $erro    = 'Erro Codigo: 17\\n\\n'.$cllevinscr->erro_msg;
            $sqlerro = true;
          }
        }else if($z01_numcgm != ''){
          $cllevcgm->y93_numcgm = $z01_numcgm;
          $cllevcgm->y93_codlev = $y60_codlev;
          $cllevcgm->incluir($y60_codlev,$z01_numcgm);
          if ($cllevcgm->erro_status==0) {
            $erro    = 'Erro Codigo: 18\\n\\n'.$cllevcgm->erro_msg;
            $sqlerro = true;
          }
        }
      }

      // Pegar valores do Levantamento
      $levValorSql = $cllevvalor->sql_query_file("","y63_sequencia,y63_mes,y63_ano,y63_codlev,y63_sequencia,y63_bruto,y63_aliquota,y63_pago,y63_saldo,y63_dtvenc,y63_histor,(y63_pago+y63_saldo) as y63_apagar"," y63_ano,y63_mes","y63_codlev=$y117_levanta");
      $levValorRs  = db_query($levValorSql);

      if(pg_num_rows($levValorRs) > 0){

        // $sqlerro=false;

        for ($ilv=0; $ilv < pg_num_rows($levValorRs); $ilv++) {
          db_fieldsmemory($levValorRs,$ilv);

          // $cllevvalor->y63_sequencia  = $y63_sequencia;
          $y63_sequenciaAnt = $y63_sequencia;
          $cllevvalor->y63_codlev     = $y60_codlev;
          $cllevvalor->y63_ano        = $y63_ano;
          $cllevvalor->y63_mes        = $y63_mes;
          // $cllevvalor->y63_dtvenc_dia = $y63_dtvenc_dia;
          // $cllevvalor->y63_dtvenc_mes = $y63_dtvenc_mes;
          // $cllevvalor->y63_dtvenc_ano = $y63_dtvenc_ano;
          $cllevvalor->y63_dtvenc     = $y63_dtvenc;
          $cllevvalor->y63_bruto      = $y63_bruto;
          $cllevvalor->y63_aliquota   = $y63_aliquota;
          $cllevvalor->y63_pago       = $y63_pago;
          $cllevvalor->y63_saldo      = $y63_saldo;
          $cllevvalor->y63_histor     = $y63_histor;

          // $cllevvalor->y63_pago = (($y63_pago == '') ? '0' : $y63_pago);
          $cllevvalor->incluir(null);
          // $erro = 'Erro Codigo: 1\\n\\n'.$cllevvalor->erro_msg;
          $y63_sequencia= $cllevvalor->y63_sequencia;
          if ($cllevvalor->erro_status==0) {
            $erro    = 'Erro Codigo: 19\\n\\n'.$cllevvalor->erro_msg;
            $sqlerro = true;
          }

          // Pegar valores pagos do Levantamento
          $rsValPg = db_query($cllevvalorpgtos->sql_query_file("$y63_sequenciaAnt","","y68_valor,y68_pgto,y68_seq"));

          if(pg_num_rows($rsValPg) > 0){
            db_fieldsmemory($rsValPg, 0);

            $cllevvalorpgtos->y68_sequencia = $y63_sequencia;
            $cllevvalorpgtos->y68_seq       = $y68_seq;
            $cllevvalorpgtos->y68_valor     = $y68_valor;
            // $cllevvalorpgtos->y68_pgto_dia  = $y68_pgto_dia;
            // $cllevvalorpgtos->y68_pgto_mes  = $y68_pgto_mes;
            // $cllevvalorpgtos->y68_pgto_ano  = $y68_pgto_ano;
            $cllevvalorpgtos->y68_pgto      = $y68_pgto;

            $cllevvalorpgtos->incluir($y63_sequencia,$y68_seq);
            if ($cllevvalorpgtos->erro_status==0) {
              $erro    = 'Erro Codigo: 20\\n\\n'.$cllevvalorpgtos->erro_msg;
              $sqlerro = true;
            }
          } // if levvalorpgtos

          //traz os dados da tabela levantanotas
          $rsLevantaNotas = db_query($cllevantanotas->sql_query_file(null,"*","y79_ordem","y79_sequencia=$y63_sequenciaAnt"));

          if(pg_num_rows($rsLevantaNotas) > 0){
            db_fieldsmemory($rsLevantaNotas, 0);

            $cllevantanotas->y79_codigo    = $y79_codigo;
            $cllevantanotas->y79_sequencia = $y63_sequencia;
            $cllevantanotas->y79_ordem     = $y79_ordem;
            $cllevantanotas->y79_documento = $y79_documento;
            $cllevantanotas->y79_valor     = $y79_valor;
            // $cllevantanotas->y79_data_dia  = $y79_data_dia;
            // $cllevantanotas->y79_data_mes  = $y79_data_mes;
            // $cllevantanotas->y79_data_ano  = $y79_data_ano;
            $cllevantanotas->y79_data      = $y79_data;

            $cllevantanotas->incluir(null);
            if ($cllevantanotas->erro_status==0) {
              $erro    = 'Erro Codigo: 21\\n\\n'.$cllevantanotas->erro_msg;
              $sqlerro = true;
            }
          } // if levantanotas

        } // for valores


        // Fiscal do levantamento
        $sqlLevusu = $cllevusu->sql_query($y117_levanta,"","y61_codlev,y61_id_usuario,nome,y61_obs");
        $rsLevusu = db_query($sqlLevusu);

        if(pg_num_rows($rsLevusu) > 0){
          for ($ilu=0; $ilu < pg_num_rows($rsLevusu); $ilu++) {
            db_fieldsmemory($rsLevusu, $ilu);
            $cllevusu->y61_codlev     = $y60_codlev;
            $cllevusu->y61_id_usuario = $y61_id_usuario;
            $cllevusu->y61_obs        = $y61_obs;

            $cllevusu->incluir($y60_codlev,$y61_id_usuario);
            if ($cllevusu->erro_status==0) {
              $erro    = 'Erro Codigo: 22\\n\\n'.$cllevusu->erro_msg;
              $sqlerro = true;
            }

          } // for fiscal

        } // if fiscal

      } // if levvalor

      if ($sqlerro==false){
        // Vincula o levantamento ao auto
        $clautolevanta->y117_auto    = $novoAuto;
        $clautolevanta->y117_levanta = $y60_codlev;
        $clautolevanta->incluir();
        if ($clautolevanta->erro_status==0) {
          $erro    = 'Erro Codigo: 23\\n\\n'.$clautolevanta->erro_msg;
          $sqlerro = true;
        }
      }

    } // for levantamento

  }


  if ($sqlerro==false){
    // Inclução de procedencias

    $sqlAutotipo = $clautotipo->sql_query_baixa(""," * ",""," y59_codauto = $antigo");
    $rsAutotipo  = db_query($sqlAutotipo);

    if(pg_num_rows($rsAutotipo) > 0){

      for ($iat=0; $iat < pg_num_rows($rsAutotipo); $iat++) {
        db_fieldsmemory($rsAutotipo, $iat);

        if ($y59_fator==""){
          $clautotipo->y59_fator='0';
        }
        $clautotipo->y59_codigo  = $y59_codigo;
        $clautotipo->y59_codauto = $novoAuto;
        $clautotipo->y59_codtipo = $y59_codtipo;
        $clautotipo->y59_valor   = $y59_valor;
        $clautotipo->y59_tipo    = $y59_tipo;
        $clautotipo->y59_fator   = $y59_fator;

        $clautotipo->incluir(null);
        if ($clautotipo->erro_status==0) {
          $erro    = 'Erro Codigo: 24\\n\\n'.$clautotipo->erro_msg;
          $sqlerro = true;
          break;
        }

      }

    }
  }

  if ($sqlerro==false){

    // Inclusão de receitas

    $sqlReceita = $clautorec->sql_query("","","*",""," y57_codauto = $antigo");
    $rsReceita  = db_query($sqlReceita);

    if(pg_num_rows($rsReceita) > 0){

      for ($ir=0; $ir < pg_num_rows($rsReceita); $ir++) {
        db_fieldsmemory($rsReceita, $ir);
        $clautorec->y57_valor = $y57_valor;
        $clautorec->y57_descr = $y57_valor;
        $clautorec->incluir($novoAuto,$y57_receit);
        if ($clautorec->erro_status==0) {
          $erro    = 'Erro Codigo: 25\\n\\n'.$clautorec->erro_msg;
          $sqlerro = true;
        }
      }
    }
  }

  if ($sqlerro==false){

    // Inclusão de fiscais

    $sqlFiscal = $clautousu->sql_query("",""," fis_autousu.*,db_usuarios.*",""," y56_codauto = $antigo");
    $rsFiscal  = db_query($sqlFiscal);

    if(pg_num_rows($rsFiscal) > 0){

      for ($if=0; $if < pg_num_rows($rsFiscal); $if++) {
        db_fieldsmemory($rsFiscal, $if);
        // $clfandamusu->y40_obs="0";
        // $clfandamusu->y40_id_usuario=$y56_id_usuario;
        // $clfandamusu->y40_codandam=$y39_codandam;
        // $clfandamusu->incluir($y39_codandam,$y56_id_usuario);
        // $erro=$clfandamusu->erro_msg;
        // if($clfandamusu->erro_status==0){
        //   $sqlerro = true;
        // }
        $clautousu->y56_codauto = $novoAuto;
        $clautousu->y56_id_usuario = $y56_id_usuario;
        $clautousu->y56_obs = $y56_obs;
        $clautousu->incluir($novoAuto,$y56_id_usuario);
        if ($clautousu->erro_status==0) {
          $erro    = 'Erro Codigo: 26\\n\\n'.$clautousu->erro_msg;
          $sqlerro = true;
        }
      }
    }
  }

  if ($sqlerro==false){

    // Inclusão de testemunhas

    $sqTestemunha = $clautotestem->sql_query("","","*",""," y24_codauto = $antigo");
    $rsTestemunha  = db_query($sqTestemunha);

    if(pg_num_rows($rsTestemunha) > 0){

      for ($it=0; $it < pg_num_rows($rsTestemunha); $it++) {
        db_fieldsmemory($rsTestemunha, $it);
        $clautotestem->y24_numcgm;
        $clautotestem->incluir($novoAuto,$y24_numcgm);
        if ($clautotestem->erro_status==0) {
          $erro    = 'Erro Codigo: 27\\n\\n'.$clautotestem->erro_msg;
          $sqlerro = true;
          break;
        }
      }
    }
  }

  if ($sqlerro==false){
    // andamento

    // andamento no auto Antigo
    $clfandam->y39_codtipo = 3;
    $clfandam->y39_data = date('Y-m-d');
    $clfandam->y39_hora = date('H:i');
    $clfandam->y39_id_usuario = db_getsession('DB_id_usuario');
    $clfandam->incluir(null);
    if ($clfandam->erro_status==0) {
      $erro    = 'Erro Codigo: 28\\n\\n'.$clfandam->erro_msg;
      $sqlerro = true;
    }

    if ($sqlerro==false){

      $y39_codandam = $clfandam->y39_codandam;

      $clautoultandam->excluir($antigo);
      $clautoultandam->y16_codauto  = $antigo;
      $clautoultandam->y16_codandam = $y39_codandam;
      $clautoultandam->incluir($antigo, $y39_codandam);
      if ($clautoultandam->erro_status==0) {
        $erro    = 'Erro Codigo: 29\\n\\n'.$clautoultandam->erro_msg;
        $sqlerro = true;
      }
    }

    if ($sqlerro==false){

      $clautoandam->y58_codauto = $antigo;
      $clautoandam->y58_codandam = $y39_codandam;
      $clautoandam->incluir($antigo, $y39_codandam);
      if ($clautoandam->erro_status==0) {
        $erro    = 'Erro Codigo: 30\\n\\n'.$clautoandam->erro_msg;
        $sqlerro = true;
      }
    }
  }

  if ($sqlerro==false){

    // andamento no auto novo
    $clfandam->y39_codtipo   = 8;
    $clfandam->y39_id_usuario = db_getsession('DB_id_usuario');
    $clfandam->incluir();
    if ($clfandam->erro_status==0) {
      $erro    = 'Erro Codigo: 31\\n\\n'.$clfandam->erro_msg;
      $sqlerro = true;
    }

    if ($sqlerro==false){

      $y39_codandam = $clfandam->y39_codandam;

      $clautoultandam->y16_codauto  = $novoAuto;
      $clautoultandam->y16_codandam = $y39_codandam;
      $clautoultandam->incluir($novoAuto, $y39_codandam);
      if ($clautoultandam->erro_status==0) {
        $erro    = 'Erro Codigo: 32\\n\\n'.$clautoultandam->erro_msg;
        $sqlerro = true;
      }
    }

    if ($sqlerro==false){

      $clautoandam->y58_codauto = $novoAuto;
      $clautoandam->y58_codandam = $y39_codandam;
      $clautoandam->incluir($novoAuto, $y39_codandam);
      if ($clautoandam->erro_status==0) {
        $erro    = 'Erro Codigo: 33\\n\\n'.$clautoandam->erro_msg;
        $sqlerro = true;
      }
    }
  }

  // Rotina de baixa
  // -------------------------------------------------------------------------------------------------------



  /* Verifica se auto foi importado para diversos */
  $sSqlDiver   = " select * from fiscalizacao.fis_autonumpre ";
  $sSqlDiver  .= "    inner join diverimportaold on y17_numpre = dv13_numpre";
  $sSqlDiver  .= "    inner join diversos on dv13_diversos = dv05_coddiver";
  $sSqlDiver  .= "  where y17_codauto =".$antigo;

  $ResultDiver = db_query($sSqlDiver);
  if(pg_num_rows($ResultDiver) > 0){
    $sqlerro = true;
    $erro = 'Débito importado para diversos.\\nFavor verificar origem do débito.\\n\\n Auto não pode ser Retificado.';
  }

  if($sqlerro == false){
     /* Verifica se auto foi importado para divida ativa */
    $sSqlDiv   = " select * from fiscalizacao.fis_autonumpre ";
    $sSqlDiv  .= "  inner join divold on y17_numpre = k10_numpre";
    $sSqlDiv  .= "  inner join divida on v01_coddiv = k10_coddiv";
    $sSqlDiv  .= "   where y17_codauto =".$antigo;

    $ResultDiv = db_query($sSqlDiv);
    if(pg_num_rows($ResultDiv) > 0){
      $sqlerro = true;
      $erro = ' Débitos inscritos em Divida \\n\\n Auto não pode ser Retificado ';
    }
  }
  if( $erro ){
     $erro .= "\nAuto só pode ser retificado no seu débito de origem.";
  }
  /*
  * Inicio Melhoria Ticket 102311
  */
  if($sqlerro == false){
    /*  Verifica se existem débitos no arrecad vinculados ao auto de infração  tabela autonumpre*/

    $sSqlAuto  = " select * from (select distinct y17_numpre FROM fiscalizacao.fis_autonumpre WHERE y17_codauto = ".$antigo;
    $sSqlAuto .= " UNION ALL select distinct q05_numpre FROM fiscalizacao.fis_autonumpre ";
    $sSqlAuto .= " LEFT JOIN fiscalizacao.fis_autolevanta ON y17_codauto = y117_auto ";
    $sSqlAuto .= " LEFT JOIN issvarlev ON y117_levanta = q18_codlev ";
    $sSqlAuto .= " LEFT JOIN issvar ON q18_codigo = q05_codigo ";
    $sSqlAuto .= " WHERE y17_codauto = ".$antigo.") as x where x.y17_numpre is not null";

    $ResultAuto = db_query($sSqlAuto);
    $numlinhas = pg_num_rows($ResultAuto);
    $debitos = array();

    if($numlinhas > 0){

      for ($i=0;$i<pg_num_rows($ResultAuto);$i++) {

        db_fieldsmemory($ResultAuto,$i);

        $sSqlArrecad = db_query("SELECT * FROM arrecad where k00_numpre = ".$y17_numpre);
        if(pg_num_rows($sSqlArrecad) > 0){
          $numpar = array();

          for ($iDebitos=0; $iDebitos < pg_num_rows($sSqlArrecad); $iDebitos++) {
            db_fieldsmemory($sSqlArrecad,$iDebitos);
            $numpar[] = $k00_numpar;
          }
          $debitos[] = array(
            'numpre' => $k00_numpre,
            'numpar' => $numpar
          );
        }
      }

      /*
       * Inicio Cancelamento do Processo
       */
      $k20_descr = "";
      if ($y114_processo != "" && $k20_descr == "") {
        $clcancdebitos->k20_descr = "Débito do auto $antigo cancelado por retificação de auto de infração nº $novoAuto";
      }

      $clcancdebitos->k20_cancdebitostipo = 1;
      $clcancdebitos->k20_hora    = db_hora();
      $clcancdebitos->k20_data    = date("Y-m-d", db_getsession("DB_datausu"));
      $clcancdebitos->k20_usuario = db_getsession("DB_id_usuario");
      $clcancdebitos->k20_instit  = db_getsession("DB_instit");
      $clcancdebitos->incluir(null);
      if ($clcancdebitos->erro_status == 0) {
        $sqlerro = true;
        $erro = $clcancdebitos->erro_msg;
      }
      $erro_msg = $clcancdebitos->erro_msg;


      if ($sqlerro == false) {
        if ($y114_processo != "") {
          $clcancdebitosprot->k25_codproc     = $y114_processo;
          $clcancdebitosprot->k25_cancdebitos = $clcancdebitos->k20_codigo;
          $clcancdebitosprot->incluir($clcancdebitos->k20_codigo);
          if ($clcancdebitosprot->erro_status == 0) {
            $sqlerro  = true;
            $erro = $clcancdebitosprot->erro_msg;
          }
        }
      }

      if ($sqlerro == false) {
        for ($i = 0; $i < count($debitos); $i ++) {
          $numpre2 = $debitos[$i]['numpre'];

          for ($iNumpar=0; $iNumpar < count($debitos[$i]['numpar']); $iNumpar++) {
            $numpar2 = $debitos[$i]['numpar'][$iNumpar];

            $sqlrec = "select distinct k00_receit from arrecad where k00_numpre =$numpre2 and k00_numpar = $numpar2";
            $resultrec= db_query($sqlrec);
            $linhasrec = pg_num_rows($resultrec);
            if ($resultrec>0) {
              for ($r=0; $r < $linhasrec; $r++) {
                db_fieldsmemory($resultrec,$r);
                if ($sqlerro == false) {
                  $clcancdebitosreg->k21_receit = $k00_receit;
                  $clcancdebitosreg->k21_hora   = db_hora();
                  $clcancdebitosreg->k21_data   = date("Y-m-d", db_getsession("DB_datausu"));
                  $clcancdebitosreg->k21_obs    = "Número do auto:".$antigo." \n Levantamento:".$numpre2."\n";
                  $clcancdebitosreg->k21_codigo = $clcancdebitos->k20_codigo;
                  $clcancdebitosreg->k21_numpre = $numpre2;
                  $clcancdebitosreg->k21_numpar = $numpar2;
                  $clcancdebitosreg->incluir("");
                  if ($clcancdebitosreg->erro_status == 0) {
                    $sqlerro = true;
                    $erro = $clcancdebitosreg->erro_msg;
                    break;
                  }
                }
              }// do for $r
            }
          }

        }


      }
      // db_msgbox($erro_msg);

      /*
       * Inicio Processa Cancelamento de Debitos
       */
      if ($sqlerro == false) {
        $k20_codigo = $clcancdebitos->k20_codigo;
        $k23_obs = $clcancdebitos->k20_descr;
        //declaro a variavel $er pq aum sei o que faz da erro de variaval indefinida
        $er = "";
        $result = $clcancdebitosreg->sql_record($clcancdebitosreg->sql_query("", "k21_sequencia,k21_codigo,k21_numpre,k21_numpar,k21_receit", "k21_numpre,k21_numpar,k21_receit", "k21_codigo=$k20_codigo"));
        $numrows = $clcancdebitosreg->numrows;
        $clcancdebitosproc->k23_data = date("Y-m-d", db_getsession("DB_datausu"));
        $clcancdebitosproc->k23_hora = date("H:i");
        $clcancdebitosproc->k23_usuario = db_getsession("DB_id_usuario");
        $clcancdebitosproc->k23_obs = "$k23_obs ";
        $clcancdebitosproc->k23_cancdebitostipo = 1;
        $clcancdebitosproc->incluir(null); //$k21_codigo);
        if ($clcancdebitosproc->erro_status == "0") {
          $erro = "Operação não Efetuada - $er".$clcancdebitosproc->erro_msg;
          $sqlerro = true;
        }else{
          $codigo_proc=$clcancdebitosproc->k23_codigo;
        }

        if ($sqlerro == false) {
          for ($x = 0; $x < $numrows; $x ++) {
            db_fieldsmemory($result, $x);
            $result_arrecad=$clarrecad->sql_record($clarrecad->sql_query_file_instit(null,"*",null,"arrecad.k00_numpre=$k21_numpre and k00_numpar=$k21_numpar " . ($k21_receit == 0?"":" and k00_receit = $k21_receit and k00_instit = ".db_getsession('DB_instit') )));
            if ($clarrecad->numrows==0){
              continue;
            }

            $data = db_getsession("DB_datausu");
            $result_deb = debitos_numpre($k21_numpre, 0, 0, $data, db_getsession("DB_anousu"), $k21_numpar);
            if (gettype($result_deb) == "boolean") {
              $sqlerro = true;
            } else {
              db_fieldsmemory($result_deb, 0);
            }
            if ($sqlerro==false){
              $clarrecant->incluir_arrecant($k21_numpre, $k21_numpar, $k21_receit, true);
              if ($clarrecant->erro_status == "0") {
                $sqlerro = true;
                $erro = "Operação não Efetuada - $er".$clarrecant->erro_msg;
              }
            }

            if ($sqlerro==false){
              $clcancdebitosprocreg->k24_codigo         = @$codigo_proc;
              $clcancdebitosprocreg->k24_cancdebitosreg = $k21_sequencia;
              $clcancdebitosprocreg->k24_vlrhis         = $vlrhis;
              $clcancdebitosprocreg->k24_vlrcor         = $vlrcor;
              $clcancdebitosprocreg->k24_juros          = $vlrjuros;
              $clcancdebitosprocreg->k24_multa          = $vlrmulta;
              $clcancdebitosprocreg->k24_desconto       = $vlrdesconto;
              $clcancdebitosprocreg->incluir(null);
              if ($clcancdebitosprocreg->erro_status == "0") {
                $sqlerro = true;
                $erro = "Operação não Efetuada - $er".$clcancdebitosprocreg->erro_msg;
              }
            }
          }
        }

        if ($sqlerro == true) {
          $erro = "Operação não Efetuada - ".@$erro_msg."\n";
        } else {
          $erro_msg .= " Cancelamento Efetuado -".@$codigo_proc."\n";
        }
      }


      /*
      * Fim Cancelamento de Debitos
      */

    }
  }

/*
 * Fim Melhoria Ticket 102311
 */
  if ($sqlerro == false) {

    // $dtbaixa      = $q07_databx_ano.'-'.$q07_databx_mes.'-'.$q07_databx_dia;
    $dtbaixa      = date('Y-m-d');
    $data         = date('Y-m-d', db_getsession('DB_datausu'));
    $usu          = db_getsession('DB_id_usuario');

    $sWhereAutoTipo  = "y87_dtbaixa   = '{$dtbaixa}'    and y87_data    = '{$data}' and y87_usuario = {$usu} and ";
    $sWhereAutoTipo .= "y59_codauto = {$antigo}                                                             ";

    if (isset($y114_processo) && !empty($y114_processo)) {
      $sWhereAutoTipo .= " and fis_autotipobaixaprocproc.y114_processo = {$y114_processo} ";
    }

    $sSqlAutoTipo    = $clautotipo->sql_query_baixa(null,"*",null,$sWhereAutoTipo);
    $result_baixa    = $clautotipo->sql_record($sSqlAutoTipo);

    if ($clautotipo->numrows == 0) {

      $clautotipobaixaproc->y87_dtbaixa  = $dtbaixa;
      $clautotipobaixaproc->y87_data     = date('Y-m-d',db_getsession('DB_datausu'));
      $clautotipobaixaproc->y87_hora     = db_hora();
      $clautotipobaixaproc->y87_usuario  = db_getsession('DB_id_usuario');
      $clautotipobaixaproc->incluir(null);
      $erro .= $clautotipobaixaproc->erro_msg;
      if ($clautotipobaixaproc->erro_status == 0) {
         $sqlerro=true;
      }

      $codigo = $clautotipobaixaproc->y87_baixaproc;
    } else {

      db_fieldsmemory($result_baixa,0);
      $codigo   = $y87_baixaproc;
      $erro   .= 'Inclusão efetuada com Sucesso!!';
    }

    if ($sqlerro == false) {

      if (isset($lProcProtBaixaAuto) && $lProcProtBaixaAuto == true || isset($y114_processo) && !empty($y114_processo)) {

        $clautotipobaixaprocproc->y114_baixaproc = $codigo;
        $clautotipobaixaprocproc->y114_processo  = $y114_processo;
        $clautotipobaixaprocproc->incluir(null);
        if ($clautotipobaixaprocproc->erro_status == 0) {

           $sqlerro  = true;
           $erro    .= $clautotipobaixaprocproc->erro_msg;
        }
      }
    }

    if ($sqlerro == false && isset($y59_codigo)) {

      // for($x = 0; $x < count($cods); $x++) {
      //   if ($sqlerro == false) {
          // die($y59_codigo);
          $clautotipobaixa->y86_codbaixaproc = $codigo;
          $clautotipobaixa->incluir($y59_codigo);
          if ($clautotipobaixa->erro_status == 0) {

            $sqlerro = true;
            $erro    .= $clautotipobaixa->erro_msg;
          }
      //   }
      // }

      $sWhereAutoNumpre = "y17_codauto = {$antigo}";
      $result_numpre    = $clautonumpre->sql_record($clautonumpre->sql_query_file(null,"*",null,$sWhereAutoNumpre));
      if ($clautonumpre->numrows > 0) {

        $numrows_numpre = $clautonumpre->numrows;
        // for($w = 0; $w < $numrows; $w++) {

        //  db_fieldsmemory($result_numpre,$w);
        //  $clarrecad->excluir(null,"k00_numpre = {$y17_numpre}");
        //  if ($clarrecad->erro_status == 0) {

        //    $sqlerro=true;
        //    $erro .= $clarrecad->erro_msg;
        //    break;
        //  }
        // }

        if ($sqlerro == false) {

          $clautonumpre->excluir(null,"y17_codauto = {$antigo}");
          if ($clautonumpre->erro_status == 0) {

            $sqlerro=true;
            $erro .= $clautonumpre->erro_msg;
          }
        }

        $sWhereAutoTipo  = "y59_codauto = {$antigo} and y86_codautotipo is null";
        $sSqlAutoTipo    = $clautotipo->sql_query_baixa(null,"*",null,$sWhereAutoTipo);
        $result_baixadas = $clautotipo->sql_record($sSqlAutoTipo);
        if ($clautotipo->numrows > 0) {

          $result_calc = $clauto->sql_calculo($antigo);
          db_fieldsmemory($result_calc,0);
          $info = $fc_autodeinfracao;
        }
      }

    }
  }

  if ($sqlerro == false) {
    // incluir na tabela que vincula os autos atigos e novos
    $clretauto->pl11_autoold = $antigo;
    $clretauto->pl11_autoret = $novoAuto;
    $clretauto->incluir();
    if ($clretauto->erro_status == 0) {
      $sqlerro = true;
      $erro   .= $clretauto->erro_msg;
    }
  }
  $LevantamentosInclui = array();

  for ($il=0; $il < count($levantamentoAntigo); $il++) {
      $LevantamentosInclui[$il]['antigo'] = $levantamentoAntigo[$il];
  }
  for ($il=0; $il < count($levantamentoNovo); $il++) {
     $LevantamentosInclui[$il]['novo']  = $levantamentoNovo[$il];
  }
  $consultacodigo = db_query("select pl11_codigo from fiscalizacao.fis_retauto where pl11_autoold = $antigo and pl11_autoret = $novoAuto");
  db_fieldsmemory($consultacodigo,0);
  if ($sqlerro == false) {
     // incluir na tabela que vincula os levantametos atigos e novos
    for ($i=0; $i <count($LevantamentosInclui) ; $i++) {
      $clretautolevanta->pl12_retcod     = $pl11_codigo;
      $clretautolevanta->pl12_levantaold = $LevantamentosInclui[$i]['antigo'];
      $clretautolevanta->pl12_levantaret = $LevantamentosInclui[$i]['novo'];
      $clretautolevanta->incluir();
      if ($clretautolevanta->erro_status == 0) {
        $sqlerro = true;
        $erro   .= $clretautolevanta->erro_msg;
        break;
      }
    }
  }




  // -----------------

  db_fim_transacao($sqlerro);
  db_msgbox($erro);

  // var_dump($sqlerro);
  // var_dump($erro);
  // exit;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
    <br>
  <?php
  if(isset($sqlerro) && $sqlerro == false){
    echo 'Auto antigo: '.$antigo.'<br>Novo Auto:'.$novoAuto.'<br><br>';

    echo '<br>Levantamentos Antigos: ';
    for ($il=0; $il < count($levantamentoAntigo); $il++) {
      echo $levantamentoAntigo[$il]. ' - ';
    }
    echo '<br>Levantamentos Novos: ';
    for ($il=0; $il < count($levantamentoNovo); $il++) {
      echo $levantamentoNovo[$il]. ' - ';
    }
  }else{
    if(isset($antigo)){
      $y50_codauto = $antigo;
    }
    if(isset($_POST['y114_processo'])){
      $y114_processo = $_POST['y114_processo'];
    }
    if(isset($_POST['p58_requer'])){
      $p58_requer = $_POST['p58_requer'];
    }
    include(modification("forms/db_frm_fis_autoretifica.php"));
  }
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
if(isset($alterar)){
  if($clparagrafo->erro_status=="0"){
    $clparagrafo->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clparagrafo->erro_campo!=""){
      echo "<script> document.form1.".$clparagrafo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clparagrafo->erro_campo.".focus();</script>";
    };
  }else{
    $clparagrafo->erro(true,true);
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
