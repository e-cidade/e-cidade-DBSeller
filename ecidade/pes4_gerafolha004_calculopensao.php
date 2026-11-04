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

/**
 * Função que calcula as Pensões do Servidor
 * 
 * @param integer $icalc
 * @param integer $opcao_geral
 * @param integer $opcao_tipo
 * @param string  $chamada_geral_arquivo
 * @param integer $iFaixaCalculo
 * @param Rubrica $oRubrica
 */
function calc_pensao($icalc, $opcao_geral, $opcao_tipo, $chamada_geral_arquivo = null, $iFaixaCalculo = 0, Rubrica $oRubrica = null ) {

  global $aValorTotalPensoes;
   
  static $iDebugNumeroChamada;

  if ( is_null($iDebugNumeroChamada) ) {
    $iDebugNumeroChamada = 0;
  }
  $iDebugNumeroChamada++;
  
  $sCodigoRubricaPensao = $oRubrica->getCodigo();
  
  global $F001, $F002, $F004, $F005, $F006,
         $F007, $F008, $F009, $F010, $F011,
         $F012, $F013, $F014, $F015, $F016,
         $F017, $F018, $F019, $F020, $F021,
         $F022, $F023, $F006_clt, $F024, $F003, $F025, $F026, $F027, $F028;

  global $quais_diversos, $db_debug;

  eval($quais_diversos);

  global $anousu, $mesusu, $DB_instit;
  global $siglap, $db21_codcli, $cfpess, $subpes,$r110_regisi,$pensao;
  global ${$chamada_geral_arquivo}, $minha_calcula_pensao, $campos_pessoal;

  global $opcao_filtro,$opcao_gml,$r110_regisf,$r110_lotaci, $r110_lotacf,$faixa_regis,$faixa_lotac;


  if ($db_debug == true) {
    echo "[calc_pensao:Chamada : $iDebugNumeroChamada] INICIANDO CALCULO DA PENSÃO!   <br>";
    echo "[calc_pensao:Chamada : $iDebugNumeroChamada] opcao_geral ..: {$opcao_geral} <br>";
    echo "[calc_pensao:Chamada : $iDebugNumeroChamada] opcao_tipo ...: {$opcao_tipo}  <br>";
  }

  if ($opcao_geral == 1) {
    $sigla      = "r10_";
    $sigla1     = "r14_";
    $qual_ponto = "pontofs";
  } else if ($opcao_geral == 8) {
    $sigla      = "r47_";
    $sigla1     = "r48_";
    $qual_ponto = "pontocom";
  } else if ($opcao_geral == 3) {
    $sigla      = "r29_";
    $sigla1     = "r31_";
    $qual_ponto = "pontofe";
  } else if ($opcao_geral == 4) {
    $sigla      = "r19_";
    $sigla1     = "r20_";
    $qual_ponto = "pontofr";
  } else if ($opcao_geral == 5) {
    $sigla      = "r34_";
    $sigla1     = "r35_";
    $qual_ponto = "pontof13";
  }
  $siglag = $sigla1;

   
  
  if ($opcao_tipo == 2) {
     
    if ($opcao_geral== 1 || $opcao_geral== 8) {

      $stringferias  = " ('{$cfpess[0]["r11_ferias"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_fer13"]}',  ";
      $stringferias .= "  '{$cfpess[0]["r11_fer13a"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_ferabo"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_feradi"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_ferant"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_feabot"]}', ";
      $stringferias .= "  '{$cfpess[0]["r11_fadiab"]}') ";

      if ($opcao_geral == 1 ) {

        $condicaoaux  = " and ( r10_rubric in  " . $stringferias;
        if ( $db21_codcli == 54 ) {
          $condicaoaux .= " or r10_rubric in ('0270') ";
        }
        $condicaoaux .= "  or r10_rubric between '2000' and '3999' )";
        $retornar     = db_delete("pontofs", bb_condicaosubpes("r10_").$condicaoaux );

      } else if ($opcao_geral == 8) {

        $condicaoaux = " and ( r47_rubric in ".$stringferias;
        if ( $db21_codcli == 54 ) {
          $condicaoaux .= " or r47_rubric in ('0270') ";
        }
        $condicaoaux .= "  or r47_rubric between '2000' and '3999' )";
        $retornar = db_delete("pontocom", bb_condicaosubpes("r47_").$condicaoaux );
      }
    }
    if ($opcao_geral == 1) {
      
      $condicaoaux  = "   and rh05_recis is null ";
      $condicaoaux .= "   and exists ( select 1                                                                     \n";
      $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n";      
      $condicaoaux .= " order by r52_regist      ";
      db_selectmax("pensao", "select pensao.*,
                                     rh01_regist as r01_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac
                                from pensao
                                     inner join rhpessoalmov on pensao.r52_anousu         = rhpessoalmov.rh02_anousu
                                                            and pensao.r52_mesusu         = rhpessoalmov.rh02_mesusu
                                                            and pensao.r52_regist         = rhpessoalmov.rh02_regist
                                                            and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."
                                     inner join rhpessoal    on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                     inner join rhlota       on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                            and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                     inner join cgm          on cgm.z01_numcgm            = rhpessoal.rh01_numcgm
                                     left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                     ".bb_condicaosubpes("r52_" ).$condicaoaux );
    } else if ($opcao_geral == 2 ) {

      $condicaoaux  = "   and rh05_recis is null ";
      $condicaoaux .= "   and exists ( select 1                                                                     \n";
      $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n";
      $condicaoaux .= " order by r52_regist ";

      db_selectmax("pensao", "select pensao.*,
                                     rh01_regist as r01_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac
                                from pensao
                                     inner join rhpessoalmov on pensao.r52_anousu          = rhpessoalmov.rh02_anousu
                                                            and pensao.r52_mesusu          = rhpessoalmov.rh02_mesusu
                                                            and pensao.r52_regist          = rhpessoalmov.rh02_regist
                                                            and rhpessoalmov.rh02_instit   = ".db_getsession('DB_instit')."
                                     inner join rhpessoal    on rhpessoal.rh01_regist      = rhpessoalmov.rh02_regist
                                     inner join rhlota       on rhlota.r70_codigo          = rhpessoalmov.rh02_lota
                                                            and rhlota.r70_instit          = rhpessoalmov.rh02_instit
                                     inner join cgm          on cgm.z01_numcgm             = rhpessoal.rh01_numcgm
                                     left join rhpesrescisao on rhpesrescisao.rh05_seqpes  = rhpessoalmov.rh02_seqpes
                                     left join rhregime      on rhregime.rh30_codreg       = rhpessoalmov.rh02_codreg
                                                            and rhregime.rh30_instit       = rhpessoalmov.rh02_instit
                                     left join rhpesrubcalc  on rhpesrubcalc.rh65_seqpes   = rhpessoalmov.rh02_seqpes
                                     left join rhinssoutros  on rh51_seqpes                = rh02_seqpes
                                     left join rhpesprop     on rh19_regist                = rh02_regist
                                   ".bb_condicaosubpes("r52_" ).$condicaoaux );
    } else if ($opcao_geral == 3 ) {

      $condicaoaux  = " and  rh05_recis is null ";
      $condicaoaux .= "   and exists ( select 1                                                                     \n";
      $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n";      
      $condicaoaux .= " order by r52_regist ";

      db_selectmax("pensao", "select distinct(r52_regist+r52_numcgm),
                                     pensao.*,
                                     rh01_regist as r01_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac
                                from pensao
                                     inner join rhpessoalmov on pensao.r52_anousu         = rhpessoalmov.rh02_anousu
                                                            and pensao.r52_mesusu         = rhpessoalmov.rh02_mesusu
                                                            and pensao.r52_regist         = rhpessoalmov.rh02_regist
                                                            and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."
                                     left  join pontofe      on pontofe.r29_anousu        = rhpessoalmov.rh02_anousu
                                                            and pontofe.r29_mesusu        = rhpessoalmov.rh02_mesusu
                                                            and pontofe.r29_regist        = rhpessoalmov.rh02_regist
                                     inner join rhpessoal    on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                     inner join rhlota       on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                            and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                     inner join cgm          on cgm.z01_numcgm            = rhpessoal.rh01_numcgm
                                     left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                     ".bb_condicaosubpes("r52_" ).$condicaoaux );
    } else if ($opcao_geral == 4 ) {

      $condicaoaux  = " and  rh05_recis is not null                    ";
      $condicaoaux .= " and ( (  extract(year  from rh05_recis)  = ".db_sqlformat(substr("#".$subpes,1,4) );
      $condicaoaux .= "      and extract(month from rh05_recis) >= ".db_sqlformat(substr("#".$subpes,6,2) );
      $condicaoaux .= "     ) or extract(year from rh05_recis)       > ".db_sqlformat(substr("#".$subpes,1,4) ).")";
      $condicaoaux .= "   and exists ( select 1                                                                     \n";
      $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n";
      $condicaoaux .= " order by r52_regist ";
      db_selectmax("pensao", "select pensao.*,
                                     rh01_regist as r01_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac
                                from pensao
                                     inner join rhpessoalmov on pensao.r52_anousu         = rhpessoalmov.rh02_anousu
                                                            and pensao.r52_mesusu         = rhpessoalmov.rh02_mesusu
                                                            and pensao.r52_regist         = rhpessoalmov.rh02_regist
                                                            and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."
                                     inner join rhpessoal    on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                     inner join rhlota       on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                            and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                     inner join cgm          on cgm.z01_numcgm            = rhpessoal.rh01_numcgm
                                     left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                     ".bb_condicaosubpes("r52_" ).$condicaoaux );
    } else if ($opcao_geral == 5 ) {

      $condicaoaux  = "   and ( rh05_recis is null or rh05_recis >= ".db_sqlformat(db_ctod("01/".substr("#".$subpes,6,2)."/".substr("#".$subpes,1,4))).")";
      $condicaoaux .= "   and exists ( select 1                                                                     \n";
      $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n";
      $condicaoaux .= " order by r52_regist ";
      db_selectmax("pensao", "select distinct(r52_regist+r52_numcgm),
                                     pensao.*,
                                     rh01_regist as r01_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac,
                                     r34_regist
                                from pensao
                                     inner join rhpessoalmov on pensao.r52_anousu         = rhpessoalmov.rh02_anousu
                                                            and pensao.r52_mesusu         = rhpessoalmov.rh02_mesusu
                                                            and pensao.r52_regist         = rhpessoalmov.rh02_regist
                                                            and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."
                                     left  join pontof13     on pontof13.r34_anousu       = rhpessoalmov.rh02_anousu
                                                            and pontof13.r34_mesusu       = rhpessoalmov.rh02_mesusu
                                                            and pontof13.r34_regist       = rhpessoalmov.rh02_regist
                                     inner join rhpessoal    on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                     inner join rhlota       on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                            and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                     inner join cgm          on cgm.z01_numcgm            = rhpessoal.rh01_numcgm
                                     left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                     ".bb_condicaosubpes("r52_" ).$condicaoaux );
    } else if ( $opcao_geral == 8 ) {

      $sDataRescisao  = db_sqlformat( db_ctod( "01/" . substr("#".$subpes,6,2) . "/" . substr("#".$subpes,1,4) ) );
      $condicaoaux    = "   and ( r47_regist is not null or r29_regist is not null ) ";
      $condicaoaux   .= "   and ( rh05_recis is null or rh05_recis >= {$sDataRescisao} )";
      $condicaoaux   .= "   and exists ( select 1                                                                     \n";
      $condicaoaux   .= "                  from pensaopensaocalculo                                                   \n";
      $condicaoaux   .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
      $condicaoaux   .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
      $condicaoaux   .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
      $condicaoaux   .= "                   and rh118_regist = pensao.r52_regist                                      \n";
      $condicaoaux   .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
      $condicaoaux   .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n"; 
      $condicaoaux   .= " order by r52_regist ";

      db_selectmax("pensao", "select distinct(r52_regist+r52_numcgm),
                                     pensao.*,
                                     rh01_regist as r01_regist,
                                     r47_regist,
                                     trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac,
                                     r29_regist
                              from pensao
                                   inner join rhpessoalmov on rhpessoalmov.rh02_anousu  = pensao.r52_anousu
                                                          and rhpessoalmov.rh02_mesusu  = pensao.r52_mesusu
                                                          and rhpessoalmov.rh02_regist  = pensao.r52_regist
                                                          and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."
                                   left  join pontocom     on pontocom.r47_anousu       = rhpessoalmov.rh02_anousu
                                                          and pontocom.r47_mesusu       = rhpessoalmov.rh02_mesusu
                                                          and pontocom.r47_regist       = rhpessoalmov.rh02_regist
                                                          and pontocom.r47_instit       = rhpessoalmov.rh02_instit
                                   left  join pontofe      on pontofe.r29_anousu        = rhpessoalmov.rh02_anousu
                                                          and pontofe.r29_mesusu        = rhpessoalmov.rh02_mesusu
                                                          and pontofe.r29_regist        = rhpessoalmov.rh02_regist
                                                          and pontofe.r29_instit        = rhpessoalmov.rh02_instit
                                   inner join rhpessoal    on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                   inner join rhlota       on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                          and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                   inner join cgm          on cgm.z01_numcgm            = rhpessoal.rh01_numcgm
                                   left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                              ".bb_condicaosubpes("r52_" ).$condicaoaux );
    }

  } else {

    $condicaoaux = "";
    if ($opcao_geral <= 3 ) {
      $condicaoaux .= " and  rh05_recis is null ";
    }
    $condicaoaux .= db_condicaoaux($opcao_filtro,
                                   $opcao_gml,
                                   "rh02_",
                                   $r110_regisi,
                                   $r110_regisf,
                                   $r110_lotaci,
                                   $r110_lotacf,
                                   $faixa_regis,
                                   $faixa_lotac,
                                   "rh01_");
    
    $condicaoaux .= "   and exists ( select 1                                                                     \n";
    $condicaoaux .= "                  from pensaopensaocalculo                                                   \n";
    $condicaoaux .= "                       inner join pensaocalculo  on rh117_sequencial = rh118_pensaocalculo   \n";
    $condicaoaux .= "                 where rh118_anousu = pensao.r52_anousu                                      \n";
    $condicaoaux .= "                   and rh118_mesusu = pensao.r52_mesusu                                      \n";
    $condicaoaux .= "                   and rh118_regist = pensao.r52_regist                                      \n";
    $condicaoaux .= "                   and rh118_numcgm = pensao.r52_numcgm                                      \n";
    $condicaoaux .= "                   and rh117_ordem  = {$iFaixaCalculo} )                                     \n"; 
    $condicaoaux .= " order by r52_regist                                                                         \n";
    
    $sSql         = "select pensao.*,                                                                             \n";
    $sSql        .= "       rh01_regist as r01_regist,                                                            \n";
    $sSql        .= "       trim(TO_CHAR(RH02_LOTA,'9999')) as r01_lotac                                          \n";
    $sSql        .= "  from pensao                                                                                \n";
    $sSql        .= "       inner join rhpessoalmov  on pensao.r52_anousu         = rhpessoalmov.rh02_anousu      \n";
    $sSql        .= "                               and pensao.r52_mesusu         = rhpessoalmov.rh02_mesusu      \n";
    $sSql        .= "                               and pensao.r52_regist         = rhpessoalmov.rh02_regist      \n";
    $sSql        .= "                               and rhpessoalmov.rh02_instit  = ".db_getsession('DB_instit')."\n";
    $sSql        .= "       inner join rhpessoal     on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist      \n";
    $sSql        .= "       inner join rhlota        on rhlota.r70_codigo         = rhpessoalmov.rh02_lota        \n";
    $sSql        .= "                               and rhlota.r70_instit         = rhpessoalmov.rh02_instit      \n";
    $sSql        .= "       inner join cgm           on cgm.z01_numcgm            = rhpessoal.rh01_numcgm         \n";
    $sSql        .= "       left  join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes      \n";
    $sSql        .= "              ".bb_condicaosubpes("r52_" )."\n";
    $sSql        .= $condicaoaux;
    db_selectmax("pensao", $sSql );
  }

  $contador        = 1;
  $primeira_pensao = true;


  if ( count($pensao) > 0 ) {
    $minha_calcula_pensao=true;
  }

  if ($db_debug == true) {
    echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Pensões encontradas: ".count($pensao)."<br>";
    echo "<pre>";
    print_r($pensao);
    echo "</pre>";

  }

  /**
   * Caso não encontre pensões, termina execução aqui.
   */
  if ( count($pensao) == 0 ) {

    if ($db_debug == true) {
      echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Nenhuma Pensão Encontrada...: ".count($pensao)."<br>";
    }
    return true;
  }


  for ($Ipensao=0; $Ipensao<count($pensao); $Ipensao++) {

    $oDadosPensao = (object)$pensao[$Ipensao];
    if ( !isset($aValorTotalPensoes[$opcao_geral][$oDadosPensao->r52_regist] ) ) {
      $aValorTotalPensoes[$opcao_geral][$oDadosPensao->r52_regist]= 0;
    }
    
    $oServidor = ServidorRepository::getInstanciaByCodigo(
                                                          $pensao[$Ipensao]["r52_regist"], 
                                                          $pensao[$Ipensao]["r52_anousu"],
                                                          $pensao[$Ipensao]["r52_mesusu"]);
    
    $oVariaveisCalculo = DBPessoal::getVariaveisCalculo($oServidor);

    $F001     = $oVariaveisCalculo->f001;
    $F002     = $oVariaveisCalculo->f002;
    $F003     = $oVariaveisCalculo->f003;
    $F004     = $oVariaveisCalculo->f004;
    $F005     = $oVariaveisCalculo->f005;
    $F006     = $oVariaveisCalculo->f006;
    $F006_clt = $oVariaveisCalculo->f006_clt;
    $F007     = $oVariaveisCalculo->f007;
    $F008     = $oVariaveisCalculo->f008;
    $F009     = $oVariaveisCalculo->f009;
    $F010     = $oVariaveisCalculo->f010;
    $F011     = $oVariaveisCalculo->f011;
    $F012     = $oVariaveisCalculo->f012;
    $F013     = $oVariaveisCalculo->f013;
    $F014     = $oVariaveisCalculo->f014;
    $F015     = $oVariaveisCalculo->f015;
    $F022     = $oVariaveisCalculo->f022;
    $F024     = $oVariaveisCalculo->f024;
    $F025     = $oVariaveisCalculo->f025;

    $pvalor_obriga = 0;
    $pvalor_liquido = 0;
    $pvalor_bruto = 0;
    $pvalor_salfamilia = 0;
    $pvalor_ad_13salario = 0;

    if ($db_debug == true) {
      echo "[calc_pensao:Chamada : $iDebugNumeroChamada] calculando pensao $Ipensao... <br>";
      echo "[calc_pensao:Chamada : $iDebugNumeroChamada] chamada_geral_arquivo: {$chamada_geral_arquivo} <br>";  
      echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r11_mes13:".$cfpess[0]["r11_mes13"]." - mes:".$mesusu."<br>";
    }

    if ($chamada_geral_arquivo == "gerfs13" && $cfpess[0]["r11_mes13"] == $mesusu) {

      if ($db_debug == true) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Buscando valores das pensÃµes anteriores a ".$pensao[$Ipensao]["r52_mesusu"]." para o cgm ".$pensao[$Ipensao]["r52_numcgm"]." no ano ".$pensao[$Ipensao]["r52_anousu"]."<br>";
      }
      $sSqlValorPensao = "select sum(pensao.r52_val13) as r52_val13 
        from pensao 
        where r52_regist = ".$pensao[$Ipensao]["r52_regist"]." 
        and r52_numcgm = ".$pensao[$Ipensao]["r52_numcgm"]." 
        and r52_anousu = ".$pensao[$Ipensao]["r52_anousu"]." 
        and r52_mesusu < ".$pensao[$Ipensao]["r52_mesusu"]; 
      $rsValorPensao       = db_query($sSqlValorPensao);
      $pvalor_ad_13salario = pg_result($rsValorPensao,0,0);

      if ($db_debug == true) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Adiantamentos de 13Âº (pvalor_ad_13salario): {$pvalor_ad_13salario} <br>";
      }
    }

    if ($db_debug) {
      echo "[calc_pensao:Chamada : $iDebugNumeroChamada] chamada_geral_arquivo: $chamada_geral_arquivo...<br>";
    }    
    if ($chamada_geral_arquivo == "gerfs13") {

      if ('f' == $pensao[$Ipensao]["r52_pag13"]) {
        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r52_pag13:".$pensao[$Ipensao]["r52_pag13"]." - continuando calculo pulando o registro... <br>";
        }        
        continue;
      }

      $lCalculaPensaoAdiantamento13 = false;
      if ($cfpess[0]["r11_mes13"] != $mesusu) {

        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r11_mes13: ".$cfpess[0]["r11_mes13"]." != mes: $mesusu <br>";
        }
        if ($pensao[$Ipensao]["r52_adiantamento13"] == 't') {
          $lCalculaPensaoAdiantamento13 = true;
        } else {

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r52_adiantamento13: ".$pensao[$Ipensao]["r52_adiantamento13"]." - continuando calculo pulando o registro... <br>";
          }          
          continue;         
        }
      }

    } else if ($chamada_geral_arquivo == "gerfcom") {
      if ('f' == $pensao[$Ipensao]["r52_pagcom"]) {
        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r52_pagcom: ".$pensao[$Ipensao]["r52_pagcom"]." - continuando calculo pulando o registro... <br>";
        }        
        continue;
      }
    } else if ($chamada_geral_arquivo == "gerffer") {
      if ('f' == $pensao[$Ipensao]["r52_pagfer"]) {
        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r52_pagfer: ".$pensao[$Ipensao]["r52_pagfer"]." - continuando calculo pulando o registro... <br>";
        }        
        continue;
      }
    } else if ($chamada_geral_arquivo == "gerfres") {
      if ('f' == $pensao[$Ipensao]["r52_pagres"]) {
        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] r52_pagres: ".$pensao[$Ipensao]["r52_pagres"]." - continuando calculo pulando o registro... <br>";
        }        
        continue;
      }
    }

    $registrop = $pensao[$Ipensao]["r52_regist"];
    $numcgmp = $pensao[$Ipensao]["r52_numcgm"];
    $condicaoaux  = " and r52_regist = ".db_sqlformat($registrop);
    $condicaoaux .= " and r52_numcgm = ".db_sqlformat($numcgmp);
    $matriz1 = array();
    $matriz2 = array();
    $retornar = true;

    if ($chamada_geral_arquivo == "gerfs13") {

      $matriz1[1] = "r52_val13";
      $matriz2[1] = 0;
      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_val13 da tabela pensao para 0 quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
      } 
      db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

    } else if ($chamada_geral_arquivo == "gerfcom") {

      $matriz1[1] = "r52_valcom";
      $matriz2[1] = 0;
      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valcom da tabela pensao para 0 quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
      }        
      db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

    } else if ($chamada_geral_arquivo == "gerfres") {

      $matriz1[1] = "r52_valres";
      $matriz2[1] = 0;
      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valres da tabela pensao para 0 quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
      }        
      db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

    } else {

      $matriz1[1] = "r52_valor";
      $matriz2[1] = 0;
      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valor da tabela pensao para 0 quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
      }        
      db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

    }

    $condicaoaux  =  " and r30_regist = ".db_sqlformat($pensao[$Ipensao]["r52_regist"] ) ;
    $condicaoaux .= " order by r30_perai desc";
    global $cadferia;
    db_selectmax("cadferia", "select r30_regist,r30_proc2,r30_per2i, r30_per2f,r30_proc1,r30_per1i,r30_per1f,r30_paga13,r30_descad from cadferia ".bb_condicaosubpes("r30_" ).$condicaoaux );
    if (db_empty($cadferia[0]["r30_proc2"]) ) {
      $r30_proc = "r30_proc1";
      $r30_peri = "r30_per1i";
      $r30_perf = "r30_per1f";
    } else {
      $r30_proc = "r30_proc2";
      $r30_peri = "r30_per2i";
      $r30_peri = "r30_per2f";
    }
    $condicaoaux = " and ".$siglag."regist = ".db_sqlformat($pensao[$Ipensao]["r52_regist"] );
    $tem_calculo = db_selectmax($chamada_geral_arquivo, "select * from ".$chamada_geral_arquivo." ".bb_condicaosubpes($siglag ).$condicaoaux );
    if ($tem_calculo) {

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] encontrou calculo executando a query: select * from ".$chamada_geral_arquivo." ".bb_condicaosubpes($siglag ).$condicaoaux."<br>";
      }  

      $pvalor_bruto      = 0;
      $pvalor_liquido    = 0;
      $pvalor_obriga     = 0;
      $pvalor_salfamilia = 0;
      $qual_reg          = $sigla1."regist";
      $qual_rub          = $sigla1."rubric";
      $qual_tpp          = " ";
      if ($opcao_geral == 3) {
        $qual_tpp = $sigla1."tpp";
      }

      $chamada_geral_ = $$chamada_geral_arquivo;
      for ($Igeral=0; $Igeral<count($chamada_geral_); $Igeral++) {

        if( $chamada_geral_[$Igeral][$qual_rub] == "R993" ){
          continue;
        }

        if ($opcao_geral == 3
          && (( db_month($cadferia[0][$r30_peri]) > db_val(substr("#".$cadferia[0][$r30_proc],6,2)) &&
          db_year($cadferia[0][$r30_peri]) == db_val(substr("#".$cadferia[0][$r30_proc],1,4)))
          || ( db_month($cadferia[0][$r30_peri]) < db_val(substr("#".$cadferia[0][$r30_proc],6,2)) &&
          db_year($cadferia[0][$r30_peri]) > db_val(substr("#".$cadferia[0][$r30_proc],1,4)))
        )
        ) {
          if (strtolower($cfpess[0]["r11_fersal"]) == "f" && ('t' == $cadferia[0]["r30_paga13"]) ) {
            // Quando do Adiantamento de Férias , nÃ£o Calcula a PensÃ£o de Férias se for Pagar como Férias e somente 1/3 for sim 
            continue;
          }
          if ('f' == $cadferia[0]["r30_paga13"] && $cadferia[0][$r30_proc] < $subpes && strtolower($chamada_geral_[$Igeral][$qual_tpp]) == "d" ) {
            // NÃ£o Processar no Calculo da PensÃ£o de Férias as Rubricas de Férias Adiantadas quando somente 1/3 for nÃ£o  e  Data de Pagto nÃ£o Venceu  
            continue;
          }
        }
        // a rubrica de pensao passa a ser calculada no gerffer
        // e depois repassada para o salario ou complentar


        if ( ( ( strtolower($cfpess[0]["r11_fersal"]) == "f" && ('t' ==  $cadferia[0]["r30_paga13"]) &&
          db_month($cadferia[0][$r30_peri]) == db_val(substr("#".$cadferia[0][$r30_proc],6,2))
        )
        || 'f' == $cadferia[0]["r30_paga13"]
      )
      &&
      ( $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_ferias"]  || // Rubrica onde é pago as férias
      $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_fer13"] || // Rubrica onde é pago um 1/3 de férias
      $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_fer13a"] || // Rubrica onde é pago um 1/3 s/ abono de férias
      $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_ferabo"] || // Rubrica onde é pago o abono de férias
      $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_feradi"] || // Rubrica onde é pago o adiantamento de férias
      $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_fadiab"]    // Rubrica onde é descontado as férias pagas no mes anterior
    )
        ) {
          // As Rubricas Especiais nÃ£o entran no calculo da PensÃ£o, quando do Calculo da PensÃ£o no SalÃ¡rio ou na Complementar
          continue;
        }


        if ($opcao_geral != 3 && $opcao_geral != 4) {
          // No Calculo da PensÃ£o de Salario ou Complementar as rubricas de Férias existente no Ponto nÃ£o entram no Calculo da PensÃ£o
          // Somente no Calculo da PensÃ£o de Férias deve ler as rubricas 2000 e
          // os descontos referentess a ferias ( previdencia e ir )

          if (( substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) != "R" && ( db_val($chamada_geral_[$Igeral][$qual_rub] ) >= 2000
            && db_val($chamada_geral_[$Igeral][$qual_rub] ) < 4000 ))
            || $chamada_geral_[$Igeral][$qual_rub] == "R915" ) {
              continue;
            }
        }

        if ($opcao_geral == 3 ) {
          // Para pagamento somente 1/3 sim e restante em salario (Pagar como SalÃ¡rio)
          // para a geracao da PensÃ£o de Férias so deve levar em conta para o Calculo da PensÃ£o 1/3 Férias

          if (strtolower($cfpess[0]["r11_fersal"]) == "s" && ('t' == $cadferia[0]["r30_paga13"])) {
            if (( substr("#". $chamada_geral_[$Igeral][$qual_rub],1,1) != "R" && strtolower($chamada_geral_[$Igeral][$qual_tpp]) != "a" ) ) {
              continue;
            }
          }
        }


        if (  substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) != "R"
          || (( substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) == "R"
          && ( (   db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) < 950
          && $chamada_geral_[$Igeral][$qual_rub] != "R928" 
          && db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) > 900
        )
        || db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) == 980 ))) ) {
          //        $qual_val = str_replace(".","",db_formatar($chamada_geral_[$Igeral][$sigla1."valor"],'f'));
          $qual_val = $chamada_geral_[$Igeral][$sigla1."valor"];
          $qual_pd  = $chamada_geral_[$Igeral][$sigla1."pd"];
          if ($chamada_geral_arquivo == "gerfres") {
            if (db_at($chamada_geral_[$Igeral][$qual_rub], "R902-R905-R908-R911-R914-")>0) {
              if (('t' == $pensao[$Ipensao]["r52_pag13"])) {


                $pvalor_obriga += $qual_val;
                if ( $debug ){ 
                
                  echo "<BR> 1 pvalor_obriga ---> $pvalor_obriga rubrica --> ".$chamada_geral_[$Igeral][$qual_rub]." valor --> $qual_val" ;
                  echo "<BR>";
                }
              }
            } else if (substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) == "R"
              && db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) < 916 ) {
                $pvalor_obriga += $qual_val;
                if ( $debug ){
                
                  echo "<BR> 2 pvalor_obriga ---> $pvalor_obriga rubrica --> ".$chamada_geral_[$Igeral][$qual_rub]." valor --> $qual_val" ;
                  echo "<BR>";
                }
              }
          } else {
            // Obs : NÃ£o entra no Calculo das Obrigacoes os Descontos de Previdencia de Ferias 
            // quando tiver Calculando PensÃ£o no Salario ou Complemetar
            if (( substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) == "R" 
              && db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) < 916
              && ((db_at($chamada_geral_[$Igeral][$qual_rub], "R903-R906-R909-R912-") > 0 && $opcao_geral == 3)
              ||
              (db_at($chamada_geral_[$Igeral][$qual_rub], "R903-R906-R909-R912-") == 0 && $opcao_geral != 3)
            ))
            ||  ( $chamada_geral_[$Igeral][$qual_rub] == "R915" && $opcao_geral == 3 ) ) {
              $pvalor_obriga += $qual_val;
              if ( $debug ){
              
                echo "<BR> 3 pvalor_obriga ---> $pvalor_obriga rubrica --> ".$chamada_geral_[$Igeral][$qual_rub]." valor --> $qual_val" ;
                echo "<BR>";
              }
            }
            if ($opcao_geral == 3 &&  db_at($chamada_geral_[$Igeral][$qual_rub] , "R903-R906-R909-R912") > 0 ) {
              // para ferias nao deve considerar os descontos
              // de previdencia normais

              continue;
            }
          }
          if ($chamada_geral_[$Igeral][$qual_rub] == "R980") {
            //$pvalor_ad_13salario += $qual_val;
            continue;
          }
          if (substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) == "R"
            && db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) >= 918
            && db_val(substr("#".$chamada_geral_[$Igeral][$qual_rub],2,3)) <= 921 ) {
              $pvalor_salfamilia += $qual_val;
            }
          if ($chamada_geral_arquivo == "gerfres") {
            if (substr("#".$chamada_geral_[$Igeral][$qual_rub],1,1) !="R"
              && $chamada_geral_[$Igeral][$qual_rub] > "4000"
              && $chamada_geral_[$Igeral][$qual_rub] < "6000"
              && 'f' == $pensao[$Ipensao]["r52_pag13"] ) {
                continue;
              }
          }
          if ($qual_pd == 1) {
            $pvalor_bruto += $qual_val;
            //echo "<BR> 2 pvalor_bruto ---> $pvalor_bruto";
          } else {
            if ((($opcao_geral==1 && ('t' == $cadferia[0]["r30_paga13"]) ) || $opcao_geral == 3 )
              && ($chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_ferant"] // Rubrica onde é descontado as férias pagas no mes anterior
              || $chamada_geral_[$Igeral][$qual_rub] == $cfpess[0]["r11_feabot"] ) ) {
                // Rubrica em que serÃ¡ lanÃ§ado o abono do mes anterior
                $pvalor_bruto -= $qual_val;
                //echo "<BR> 2 palor_bruto ---> $pvalor_bruto";
              } else {
                //echo "<BR> 2 pvalor_liquido ---> $qual_val qual_rub --> ".$chamada_geral_[$Igeral][$qual_rub];
                $pvalor_liquido += $qual_val;
                //echo "<BR> 2 pvalor_liquido ---> $pvalor_liquido";
              }
          }
        }
      }

      if ($opcao_geral == 3 && $cadferia[0][$r30_proc] < $subpes) {
        ferias($pensao[$Ipensao]["r52_regist"]," " );

        // F019 - Numero de dias a pagar no mes
        // F020 - Numero de dias abono p/ pagar no mes

        // verificar a necessidade de proporcionalizar: por
        // exemplo  se for so 1/3 todo adiantado no mes
        // anterior , porem tem 25 dias de gozo neste mes e
        // outros 5 no proximo mes

        $pvalor_bruto -= ( ( $cadferia[0]["r30_descad"] / ($cadferia[0]["r30_ndias"]-$cadferia[0]["r30_abono"]+$F020) ) * ($F019+$F020) );
        //echo "<BR> 3 palor_bruto ---> $pvalor_bruto";
      }

      if ($pvalor_bruto < 0) {
        $pvalor_bruto = 0;
        //echo "<BR> 3 palor_bruto ---> $pvalor_bruto";
      }

      //echo "<BR> $pvalor_liquido = ( $pvalor_bruto-$pvalor_liquido > 0 ? $pvalor_bruto-$pvalor_liquido: 0 ) ;";
      $pvalor_liquido = ( $pvalor_bruto-$pvalor_liquido > 0 ? $pvalor_bruto-$pvalor_liquido: 0 ) ;
      //echo "<BR> 3 pvalor_liquido ---> $pvalor_liquido";

    }

    /**
     * Pensões Alimenticias
     */

    if ( isset($aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]] ) ) {

      if ($db_debug) {
    
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Valor Obrigações Antes soma de Pensões Acumuladas para a matricula {$aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]]}: $pvalor_obriga<br>";
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Valor Acumulado Anterior de Pensões.: {$aValorTotalPensoes[$opcao_geral]}<br>";
      }  


      $pvalor_obriga += $aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]];
      
      if ( $debug ){
      
        echo "<BR> 4 pvalor_obriga ---> $pvalor_obriga Pensoes -- valor --> $aValorTotalPensoes[$opcao_geral]" ;
        echo "<BR>";
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Valor Obrigações Após  soma de Pensões Acumuladas: $pvalor_obriga<br>";
      }
    }



    $formula_pensao = trim($pensao[$Ipensao]["r52_formul"]);
    $sFormula       = $formula_pensao;
    if (!db_empty($formula_pensao)) {

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] encontrou formula para a pensao... <br>";
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] formula: $formula_pensao <br>";
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor da Pensao: ".@$valor_pensao."<br>";
      }  

      if ($tem_calculo) {

        $formpensao = $pensao[$Ipensao]["r52_formul"];

        global $rubricas_;
        db_selectmax("rubricas_", "select * from rhrubricas  where rh27_instit = $DB_instit " );

        for ($Irubricas=0; $Irubricas<count($rubricas_); $Irubricas++) {

          if (db_at($rubricas_[$Irubricas]["rh27_rubric"],$pensao[$Ipensao]["r52_formul"]) > 0) {

            //echo "<BR> ".$rubricas_[$Irubricas]["rh27_rubric"] ;
            $condicaoaux  = " and ".$siglag."regist = ".db_sqlformat($pensao[$Ipensao]["r52_regist"] );
            $condicaoaux .= " and ".$siglag."rubric = ".db_sqlformat($rubricas_[$Irubricas]["rh27_rubric"] );
            if (db_selectmax($chamada_geral_arquivo, "select * from ".$chamada_geral_arquivo." ".bb_condicaosubpes($siglag ).$condicaoaux )) {

              $arq_       = $$chamada_geral_arquivo;
              $vararq     = $arq_[0][$sigla1."valor"] ;
              $formpensao = db_strtran($formpensao,$rubricas_[$Irubricas]["rh27_rubric"],db_strtran(db_str($vararq,15,2),",","."));
              //echo "<BR> formula da pensao 3 ->  $formpensao";
            } else {
              $formpensao = db_strtran($formpensao,$rubricas_[$Irubricas]["rh27_rubric"],"0");
              //echo "<BR> formula da pensao 4 ->  $formpensao";
            }
          }
        }

        /**
         * Busca os Valores para Substituição da Formula pelo Valor
         */
        while (1==1) {

          $temtroca = false;
          if (db_at("7777",$formpensao) > 0) {
            $formpensao = db_strtran($formpensao,"7777",db_strtran(db_str($pvalor_liquido,15,2),",","."));
            //echo "<BR> formula da pensao 5 ->  $formpensao";
            $temtroca = true;
          }
          if (db_at("8888",$formpensao) > 0) {

            $formpensao = db_strtran($formpensao,"8888",db_strtran(db_str($pvalor_obriga,15,2),",","."));
             

            //echo "<BR> formula da pensao 6 ->  $formpensao";
            $temtroca = true;
          }
          if (db_at("9999",$formpensao) > 0) {
            $formpensao = db_strtran($formpensao,"9999",db_strtran(db_str($pvalor_bruto,15,2),",","."));
            //echo "<BR> formula da pensao 7 ->  $formpensao";
            $temtroca = true;
          }
          if ($temtroca == false) {
            break;
          }

        }

        $formpensao = str_replace('D','$D',$formpensao);
        $formpensao = str_replace('F','$F',$formpensao);

        //            ver a possibilidade de incluir na formula o calculo de bases

        //            $formpensao = le_var_bxxx($formpensao,$qual_ponto, $chamada_geral_arquivo, $sigla, $sigla1, 0,"");

        //echo "<BR> formula da pensao 8 ->  $formpensao rubrica --> ".$pensao[$Ipensao]["r52_regist"];
        global $valor_pensao;
        ob_start();
        eval('$valor_pensao = '.$formpensao.";");
        db_alerta_erro_eval($pessoal[$Ipessoal]["r01_regist"],$formpensao,$sCodigoRubricaPensao);
        //echo "<BR> formula com % pensao = $valor_pensao * (".$pensao[$Ipensao]["r52_perc"]."/100)";

        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] calculando valor da pensao... <br>";
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Fórmula já sem os Valores: $sFormula  ... <br>";
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Fórmula já Com os Valores: $formpensao... <br>";
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao = valor_pensao * (r52_perc / 100) => $valor_pensao * $valor_pensao (".$pensao[$Ipensao]["r52_perc"]."/100) = ".$valor_pensao * ($pensao[$Ipensao]["r52_perc"]/100)."<br>";  
        }
        $valor_pensao = $valor_pensao * ($pensao[$Ipensao]["r52_perc"]/100);
        //echo "<BR> valor da Pensao Alimenticia --> $valor_pensao";
      } else {
        $valor_pensao = 0;
      }

    } else {

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] não encontrou formula para a pensao... <br>";
      }

      if (($pvalor_bruto - $pvalor_salfamilia) > 0) {

        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] pvalor_bruto($pvalor_bruto) - pvalor_salfamilia($pvalor_salfamilia) > 0 <br>";
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao = ".$pensao[$Ipensao]["r52_vlrpen"]."<br>";
        }
        $valor_pensao = $pensao[$Ipensao]["r52_vlrpen"];

      } else {

        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao = 0<br>";
        }
        $valor_pensao = 0;

      }

      if ($opcao_geral == 3) {

        if ($cadferia[0][$r30_proc] < $subpes
          && db_month($cadferia[0][$r30_peri] ) == db_month($cadferia[0][$r30_perf] )
          && db_month($cadferia[0][$r30_peri] ) != db_val(substr("#". $cadferia[0][$r30_proc],6,2)) ) {
            if ($db_debug) {
              echo "[calc_pensao:Chamada : $iDebugNumeroChamada] zerando valor da pensao<br>";
              echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao antes {$valor_pensao}<br>";
              echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao depois 0<br>";
            }  
            $valor_pensao = 0;
          }
      }
    }
    //echo "<BR> pensao 8 ->  $chamada_geral_arquivo";

    if ($pvalor_ad_13salario > 0) {

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] subtraindo pvalor_ad_13salario da variavel valor_pensao  <br>";
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor_pensao = {$valor_pensao} - {$pvalor_ad_13salario} = ".($valor_pensao-$pvalor_ad_13salario)."<br>";
      }        
      $valor_pensao -= $pvalor_ad_13salario;
    }

    if ($valor_pensao <= 0){
      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor da pensao menor que zero, zerando valor da pensao! <br>";
      }        
      $valor_pensao = 0;
    }

    if ($valor_pensao >= 0  ) {

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Valor da Pensao: {$valor_pensao} <br>";
      }

      /**
       * Se a Chamada do Cálculo For Referente a 13º ou Férias Soma os Numeros Referentes
       * Qualquer outra OCORRENCIA será com a Rubrica Normal
       */
      if ($chamada_geral_arquivo == "gerfs13") { 

        $rubrica_pensao = db_str( db_val($sCodigoRubricaPensao) + 4000, 4,0);

      } else if ($chamada_geral_arquivo == "gerffer") {

        $rubrica_pensao = db_str( db_val($sCodigoRubricaPensao) + 2000, 4,0);

      } else {

        $rubrica_pensao = $sCodigoRubricaPensao;

      }

      $condicaoaux  = " and ".$siglap."regist = ".db_sqlformat($pensao[$Ipensao]["r52_regist"] );
      $condicaoaux .= " and ".$siglap."rubric = ".db_sqlformat($rubrica_pensao );

      global ${$qual_ponto};
      if (db_selectmax($qual_ponto, "select * from ".$qual_ponto." ".bb_condicaosubpes($siglap ).$condicaoaux )) {
        $acao = "altera";
      } else {
        $acao = "insere";
      }

      if ($chamada_geral_arquivo == "gerfs13") {

        if ($lCalculaPensaoAdiantamento13) {
          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] calculando valor da pensao com adiantamento<br>";
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] valor da pensao = (valor da pensao * (r52_percadiantamento13)/100) = {$valor_pensao} * (".$pensao[$Ipensao]["r52_percadiantamento13"]."/100) = ".($valor_pensao * ($pensao[$Ipensao]["r52_percadiantamento13"]/100))."<br>";
          }  
          $valor_pensao = ($valor_pensao * ($pensao[$Ipensao]["r52_percadiantamento13"]/100));
        }
      }

      $ponto            = $$qual_ponto;
      $qual_val         = $sigla."valor";
      $qual_rep         = $sigla;
      $valor_pensao     = round($valor_pensao,2 );
      $nValorPensaoIRRF = 0;

      if ($db_debug) {
        echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Somando Valor da Pensão ao Total da Matricula{$pensao[$Ipensao]["r52_regist"]}: +$valor_pensao <br>";
      }
      if ( !isset( $aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]] ) ) {
        $aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]] = 0;
      }
      $aValorTotalPensoes[$opcao_geral][$pensao[$Ipensao]["r52_regist"]] += $valor_pensao;

      if ($valor_pensao > 0 ) {
        

        if ($db_debug) {
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] 13818 valor_pensao: $valor_pensao <br>";
          echo "[calc_pensao:Chamada : $iDebugNumeroChamada] opcao_geral: {$opcao_geral} <br>";
        }

        if ($opcao_geral == 1) {

          $matriz1 = array();
          $matriz2 = array();

          $matriz1[1] = "r10_regist";
          $matriz1[2] = "r10_rubric";
          $matriz1[3] = "r10_valor";
          $matriz1[4] = "r10_quant";
          $matriz1[5] = "r10_lotac";
          $matriz1[6] = "r10_anousu";
          $matriz1[7] = "r10_mesusu";
          $matriz1[8] = "r10_instit";

          $matriz2[1] = $pensao[$Ipensao]["r52_regist"];
          $matriz2[2] = $sCodigoRubricaPensao;//Aqui vai a Rubrica configurada na Faixa

          if ($primeira_pensao) {
            $matriz2[3] = round($valor_pensao,2);
          } else {

            if ($acao == "altera") {
              $matriz2[3] = round($ponto[0]["r10_valor"] + $valor_pensao,2);
            } else {
              $matriz2[3] = round($valor_pensao,2);
            }
          }
          $matriz2[4] = 1;
          $matriz2[5] = $pensao[$Ipensao]["r01_lotac"];
          $matriz2[6] = $anousu;
          $matriz2[7] = $mesusu;
          $matriz2[8] = $DB_instit;
          
          $nValorPensaoIRRF = $matriz2[3];
        } else if ($opcao_geral == 3 ) {

          $matriz1 = array();
          $matriz2 = array();
          $matriz1[1] = "r29_regist";
          $matriz1[2] = "r29_rubric";
          $matriz1[3] = "r29_valor";
          $matriz1[4] = "r29_quant";
          $matriz1[5] = "r29_lotac";
          $matriz1[6] = "r29_media";
          $matriz1[7] = "r29_calc";
          $matriz1[8] = "r29_tpp";
          $matriz1[9] = "r29_anousu";
          $matriz1[10]= "r29_mesusu";
          $matriz1[11]= "r29_instit";

          $matriz2[1] = $pensao[$Ipensao]["r52_regist"];
          $matriz2[2] = $rubrica_pensao;
          if ($primeira_pensao) {
            $matriz2[3] = round($valor_pensao,2);
          } else {
            if ($acao == "altera") {
              $matriz2[3] = round($ponto[0]["r29_valor"] + $valor_pensao,2);
            } else {
              $matriz2[3] = round($valor_pensao,2);
            }
          }
          $matriz2[4] = 1;
          $matriz2[5] = $pensao[$Ipensao]["r01_lotac"];
          $matriz2[6] = 0;
          $matriz2[7] = 0;
          $matriz2[8] = " ";
          $matriz2[9] = $anousu;
          $matriz2[10] = $mesusu;
          $matriz2[11] = $DB_instit;
          $nValorPensaoIRRF = $matriz2[3];
        } else if ($opcao_geral == 8) {

          $matriz1 = array();
          $matriz2 = array();

          $matriz1[1] = "r47_regist";
          $matriz1[2] = "r47_rubric";
          $matriz1[3] = "r47_valor";
          $matriz1[4] = "r47_quant";
          $matriz1[5] = "r47_lotac";
          $matriz1[6] = "r47_anousu";
          $matriz1[7] = "r47_mesusu";
          $matriz1[8]=  "r47_instit";

          $matriz2[1] = $pensao[$Ipensao]["r52_regist"];
          $matriz2[2] = $sCodigoRubricaPensao; //@TODO PensÃ£o Configurada na faixa
          if ($primeira_pensao) {
            $matriz2[3] = round($valor_pensao,2 );
          } else {
            if ($acao == "altera") {
              $matriz2[3] = round($ponto[0]["r47_valor"] + $valor_pensao, 2);
            } else {
              $matriz2[3] =  round($valor_pensao,2 );
            }
          }
          $matriz2[4] = 1;
          $matriz2[5] = $pensao[$Ipensao]["r01_lotac"];
          $matriz2[6] = $anousu;
          $matriz2[7] = $mesusu;
          $matriz2[8] = $DB_instit;
          $nValorPensaoIRRF = $matriz2[3];

        } else if ($opcao_geral == 4) {

          $matriz1 = array();
          $matriz2 = array();

          $matriz1[1] = "r19_regist";
          $matriz1[2] = "r19_rubric";
          $matriz1[3] = "r19_valor";
          $matriz1[4] = "r19_quant";
          $matriz1[5] = "r19_lotac";
          $matriz1[6] = "r19_tpp";
          $matriz1[7] = "r19_anousu";
          $matriz1[8] = "r19_mesusu";
          $matriz1[9]=  "r19_instit";

          $matriz2[1] = $pensao[$Ipensao]["r52_regist"];
          $matriz2[2] = $sCodigoRubricaPensao;
          if ($primeira_pensao) {
            $matriz2[3] = round($valor_pensao,2);
          } else {
            if ($acao == "altera") {
              $matriz2[3] = round($ponto[0]["r19_valor"] + $valor_pensao,2);
            } else {
              $matriz2[3] = round($valor_pensao,2);
            }
          }
          $matriz2[4] = 1;
          $matriz2[5] = $pensao[$Ipensao]["r01_lotac"];
          $matriz2[6] = " ";
          $matriz2[7] = $anousu;
          $matriz2[8] = $mesusu;
          $matriz2[9] = $DB_instit;
          $nValorPensaoIRRF = $matriz2[3];

        } else if ($opcao_geral == 5) {

          $matriz1 = array();
          $matriz2 = array();

          $matriz1[1] = "r34_regist";
          $matriz1[2] = "r34_rubric";
          $matriz1[3] = "r34_valor";
          $matriz1[4] = "r34_quant";
          $matriz1[5] = "r34_lotac";
          $matriz1[6] = "r34_media";
          $matriz1[7] = "r34_calc";
          $matriz1[8] = "r34_anousu";
          $matriz1[9] = "r34_mesusu";
          $matriz1[10]= "r34_instit";

          $matriz2[1] = $pensao[$Ipensao]["r52_regist"];
          $matriz2[2] = $rubrica_pensao;
          if ($primeira_pensao) {
            $matriz2[3] = $valor_pensao;
          } else {
            if ($acao == "altera") {
              $matriz2[3] = $ponto[0]["r34_valor"] + $valor_pensao;
            } else {
              $matriz2[3] = $valor_pensao;
            }
          }
          $matriz2[4] = 1;
          $matriz2[5] = $pensao[$Ipensao]["r01_lotac"];
          $matriz2[6] = 0;
          $matriz2[7] = 0;
          $matriz2[8] = $anousu;
          $matriz2[9] = $mesusu;
          $matriz2[10] = $DB_instit;
          $nValorPensaoIRRF = $matriz2[3];

        }

        $condicaoaux  = " and ".$siglap."regist = ".db_sqlformat($pensao[$Ipensao]["r52_regist"] );
        $condicaoaux .= " and ".$siglap."rubric = ".db_sqlformat($rubrica_pensao );
        if ($acao == "altera") {

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando {$qual_ponto}<br>";
            echo "Campos: <pre>";
            print_r($matriz1);
            echo "</pre>";
            echo "Valores: <pre>";
            print_r($matriz2);
            echo "</pre>";
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] <br>";
          }  
          db_update($qual_ponto, $matriz1, $matriz2, bb_condicaosubpes($siglap).$condicaoaux );

        } else {

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Inserindo em $qual_ponto<br>";
            echo "Campos: <pre>";
            print_r($matriz1);
            echo "</pre>";
            echo "Valores: <pre>";
            print_r($matriz2);
            echo "</pre>";
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] <br>";             
          }           
          db_insert($qual_ponto, $matriz1, $matriz2 );

        }
        $matriz1        = array();
        $matriz2        = array();
        $registrop      = $pensao[$Ipensao]["r52_regist"];
        $numcgmp        = $pensao[$Ipensao]["r52_numcgm"];

        $condicaoaux    = " and r52_regist = ".db_sqlformat($registrop);
        $condicaoaux   .= " and r52_numcgm = ".db_sqlformat($numcgmp);

        if ($opcao_geral == 5) {

          $matriz1[1] = "r52_val13";
          $matriz2[1] = $valor_pensao;

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_val13 para {$valor_pensao} quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
          }
          $retornar = db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

        } else if ($opcao_geral == 8) {

          $matriz1[1] = "r52_valcom";
          $matriz2[1] = $valor_pensao;

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valcom para {$valor_pensao} quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
          }           
          $retornar = db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

        } else if ($opcao_geral == 3) {

          $matriz1[1] = "r52_valfer";
          $matriz2[1] = $valor_pensao;

          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valfer para {$valor_pensao} quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
          }           
          $retornar = db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

        } else if ($opcao_geral == 1) {

          $matriz1[1] = "r52_valor";
          $matriz2[1] = $valor_pensao;
          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valor para {$valor_pensao} quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
          }           
          $retornar = db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

        } else if ($opcao_geral == 4) {

          $matriz1[1] = "r52_valres";
          $matriz2[1] = $valor_pensao;
          if ($db_debug) {
            echo "[calc_pensao:Chamada : $iDebugNumeroChamada] Alterando o valor do campo r52_valres para {$valor_pensao} quando ".bb_condicaosubpes("r52_").$condicaoaux."<br>";
          }           
          $retornar = db_update("pensao", $matriz1, $matriz2, bb_condicaosubpes("r52_").$condicaoaux );

        }
      }

    }
    $valor_pensao = 0;
    $primeira_pensao = false;
  }

  if ($db_debug == true) {
    echo "[calc_pensao:Chamada : $iDebugNumeroChamada] FIM DO CALCULO DA PENSÃO! <br>";
  }

}

function getRubricasPensaoAlimenticia( $iInstituicao  = null) {


  $iInstituicao = is_null($iInstituicao) ? db_getsession( "DB_instit" ) : $iInstituicao;

  /**
   * SQL Com as Faixas de Cálculo
   */
  $oDaoPensao = new cl_pensaocalculo();
  $sCamposFaixas = "rh117_sequencial, rh117_ordem, rh117_rubrica, rh117_instit";
  $sSqlFaixas = $oDaoPensao->sql_query_file( null, "*", null, " rh117_instit = " . db_getsession( "DB_instit" ) . " order by rh117_ordem " );
  $rsFaixas = db_query( $sSqlFaixas );

  if ( ! $rsFaixas ) {
    throw new DBException( "Erro ao Buscar os dados das Faixas de Cálculo das Pensões.");
  }

  $aRubricasPensaoAlimenticia       = array();
  $aRubricasPensaoAlimenticiaFerias = array();
  $aRubricasPensaoAlimenticia13o    = array();

  foreach ( db_utils::getCollectionByRecord( $rsFaixas ) as $oStdFaixas ) {

    $aRubricasPensaoAlimenticia[$oStdFaixas->rh117_rubrica]       = $oStdFaixas->rh117_rubrica;
    $aRubricasPensaoAlimenticiaFerias[$oStdFaixas->rh117_rubrica] = $oStdFaixas->rh117_rubrica + 2000;
    $aRubricasPensaoAlimenticia13o[$oStdFaixas->rh117_rubrica]    = $oStdFaixas->rh117_rubrica + 4000;
  }

  $oRetorno                  = new stdClass();
  $oRetorno->aRubricas       = $aRubricasPensaoAlimenticia      ;
  $oRetorno->aRubricasFerias = $aRubricasPensaoAlimenticiaFerias;
  $oRetorno->aRubricas13o    = $aRubricasPensaoAlimenticia13o   ;

  return $oRetorno;

}