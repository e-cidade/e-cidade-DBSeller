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


/**  * * * * * * * * * * * *
 * Opções padrão           *
 * * * * * * * * * * * * * *
 * $db_opcao      = 1;     *
 * $db_botao      = true;  *
 * $limpar_campos = false; *
 * $lErro         = false; *
 * * * * * * * * * * * * * */
require_once(modification("libs/db_conecta.php"));
$oPost    = db_utils::postMemory($_POST);
$iNumCGM  = ( !isset( $db_opcaoal ) || $db_opcaoal == 33 ) ? null : $r52_numcgm;

  db_inicio_transacao();
  $sMensagemErro  = true;
if ( isset($oPost->incluir) || isset($oPost->alterar) || isset($oPost->excluir) ) {

  /**
   * Faixas de Cálculo de Pensões Alimentícias
   */

  $sCondicaoCGM            = !empty($registro_atual) ? " and rh118_pensaocalculo = $registro_atual" : " and rh118_numcgm = {$r52_numcgm}";
 require_once(modification("classes/db_pensaopensaocalculo_classe.php"));
  $oDaoPensaoPensaoCalculo = new cl_pensaopensaocalculo();
  $oDaoPensaoPensaoCalculo->excluir( null, "     rh118_anousu = {$r52_anousu}
                                             and rh118_mesusu = {$r52_mesusu}
                                             and rh118_regist = {$r52_regist}
                                             $sCondicaoCGM" );
  if ( $oDaoPensaoPensaoCalculo->erro_status == "0" ) {

    $sMensagemErro  = $oDaoPensaoPensaoCalculo->erro_msg;
    $lErro = true;
  }


}
/**
 * Inicio INCLUSAO
 */
if ( !isset($oPost->opcao) ){


  if ( isset( $oPost->incluir ) ) {

    $clpensao->r52_valfer = "0";
    $clpensao->r52_pagfer = 'f';


    if ( $r52_pag13 == 'f' ) {

      $r52_adiantamento13 = 'f';
      $clpensao->r52_adiantamento13 = 'false';
    }

    if ( $r52_adiantamento13 == 'f' ) {
      $clpensao->r52_percadiantamento13 = "0";
    }

    $clpensao->incluir( $r52_anousu, $r52_mesusu, $r52_regist, $r52_numcgm );

    if ( $clpensao->erro_status == "0" ) {

      $sMensagemErro  = $clpensao->erro_msg;
      $lErro = true;
    }

    if ( ! $lErro ) {

      if ( isset( $rh77_retencaotiporec ) && trim( $rh77_retencaotiporec ) != '' ) {

        $clpensaoretencao->rh77_anousu = $r52_anousu;
        $clpensaoretencao->rh77_mesusu = $r52_mesusu;
        $clpensaoretencao->rh77_numcgm = $r52_numcgm;
        $clpensaoretencao->rh77_regist = $r52_regist;
        $clpensaoretencao->rh77_retencaotiporec = $rh77_retencaotiporec;
        $clpensaoretencao->incluir( null );

        if ( $clpensaoretencao->erro_status == "0" ) {
          $lErro = true;
        }
      }
    }

    /**
     * Faixa de Pensoes
     */
    $oDaoPensaoPensaoCalculo = new cl_pensaopensaocalculo();
    $oDaoPensaoPensaoCalculo->rh118_anousu        = $r52_anousu;
    $oDaoPensaoPensaoCalculo->rh118_mesusu        = $r52_mesusu;
    $oDaoPensaoPensaoCalculo->rh118_regist        = $r52_regist;
    $oDaoPensaoPensaoCalculo->rh118_numcgm        = $r52_numcgm;
    $oDaoPensaoPensaoCalculo->rh118_pensaocalculo = $rh117_sequencial;
    $oDaoPensaoPensaoCalculo->incluir(null);

    if ( $oDaoPensaoPensaoCalculo->erro_status == "0" ) {

      $sMensagemErro  = $oDaoPensaoPensaoCalculo->erro_msg;
      $lErro = true;
    }

    if ( ! $lErro ) {
      $limpar_campos = true;
      if ( isset( $db_opcaoal ) && $db_opcaoal == 22 ) {
        $clicar = "clicar";
      }
    }




    /**
     * Inicio ALTERACAO
     */
  } elseif ( isset( $oPost->alterar ) ) {


    if ( $r52_pagres == 'f' ) {
      $clpensao->r52_valres = "0";
    }

    if ( $r52_pag13 == 'f' ) {
      $clpensao->r52_val13 = "0";
    }

    if ( $r52_pagfer == 'f' ) {
      $clpensao->r52_valfer = "0";
    }

    if ( $r52_pagcom == 'f' ) {
      $clpensao->r52_valcom = "0";
    }

    if ( $r52_pag13 == 'f' ) {

      $r52_adiantamento13 = 'f';
      $clpensao->r52_adiantamento13 = 'false';
    }

    if ( $r52_adiantamento13 == 'f' ) {
      $clpensao->r52_percadiantamento13 = "0";
    }

    $clpensao->alterar( $r52_anousu, $r52_mesusu, $r52_regist, $r52_numcgm );

    if ( $clpensao->erro_status == "0" ) {

      $sMensagemErro  = $clpensao->erro_msg;
      $lErro = true;
    }

    if ( ! $lErro ) {

      $clpensaoretencao->rh77_anousu = $r52_anousu;
      $clpensaoretencao->rh77_mesusu = $r52_mesusu;
      $clpensaoretencao->rh77_numcgm = $r52_numcgm;
      $clpensaoretencao->rh77_regist = $r52_regist;

      $sWhereRetencao = "    rh77_numcgm = {$r52_numcgm} ";
      $sWhereRetencao .= "and rh77_regist = {$r52_regist} ";
      $sWhereRetencao .= "and rh77_anousu = {$r52_anousu} ";
      $sWhereRetencao .= "and rh77_mesusu = {$r52_mesusu} ";
      $sWhereRetencao .= "and rh77_regist = {$r52_regist} ";

      $sCamposRetencao = "rh77_sequencial,     ";
      $sCamposRetencao .= "rh77_retencaotiporec ";

      $rsRetencao = $clpensaoretencao->sql_record( $clpensaoretencao->sql_query_file( null, $sCamposRetencao, null, $sWhereRetencao ) );

      if ( $clpensaoretencao->numrows > 0 ) {

        $oRetencao = db_utils::fieldsMemory( $rsRetencao, 0 );

        if ( trim( $rh77_retencaotiporec ) != '' ) {

          if ( $oRetencao->rh77_retencaotiporec != $rh77_retencaotiporec ) {

            $clpensaoretencao->rh77_retencaotiporec = $rh77_retencaotiporec;
            $clpensaoretencao->rh77_sequencial = $oRetencao->rh77_sequencial;
            $clpensaoretencao->alterar( $oRetencao->rh77_sequencial );

            if ( $clpensaoretencao->erro_status == "0" ) {

              $sMensagemErro  = $clpensaoretencao->erro_msg;
              $lErro = true;
            }
          }
        } else {

          $clpensaoretencao->excluir( $oRetencao->rh77_sequencial );

          if ( $clpensaoretencao->erro_status == "0" ) {
            $sMensagemErro  = $clpensaoretencao->erro_msg;
            $lErro = true;
          }
        }
      } else {

        if ( trim( $rh77_retencaotiporec ) != '' ) {

          $clpensaoretencao->rh77_retencaotiporec = $rh77_retencaotiporec;
          $clpensaoretencao->incluir( null );

          if ( $clpensaoretencao->erro_status == "0" ) {

            $sMensagemErro  = $clpensaoretencao->erro_msg;
            $lErro = true;
          }
        }
      }
    }

    /**
     * Faixa de Pensoes
     */
    $oDaoPensaoPensaoCalculo = new cl_pensaopensaocalculo();
    $oDaoPensaoPensaoCalculo->rh118_anousu        = $r52_anousu;
    $oDaoPensaoPensaoCalculo->rh118_mesusu        = $r52_mesusu;
    $oDaoPensaoPensaoCalculo->rh118_regist        = $r52_regist;
    $oDaoPensaoPensaoCalculo->rh118_numcgm        = $r52_numcgm;
    $oDaoPensaoPensaoCalculo->rh118_pensaocalculo = $rh117_sequencial;
    $oDaoPensaoPensaoCalculo->incluir(null);

    if ( $oDaoPensaoPensaoCalculo->erro_status == "0" ) {

      $sMensagemErro  = $oDaoPensaoPensaoCalculo->erro_msg;
      $lErro = true;
    }




    if ( $lErro ) {
      $db_opcao = "2";
    } else {

      if ( isset( $db_opcaoal ) && $db_opcaoal == 22 ) {
        $clicar = "clicar";
      }
      $limpar_campos = true;
    }


    /**
     * Inicio EXCLUSAO
     */
  } elseif ( isset( $oPost->excluir ) ) {

    $numcgm = null;

    if ( isset( $db_opcaoal ) && $db_opcaoal != 33 ) {
      $numcgm = $r52_numcgm;
    }

    $sWhereRetencao = "    rh77_regist = {$r52_regist} ";
    $sWhereRetencao .= "and rh77_anousu = {$r52_anousu} ";
    $sWhereRetencao .= "and rh77_mesusu = {$r52_mesusu} ";
    $sWhereRetencao .= "and rh77_regist = {$r52_regist} ";

    if ( trim( $numcgm ) != '' ) {
      $sWhereRetencao .= "and rh77_numcgm = {$numcgm}   ";
    }

    $clpensaoretencao->excluir( null, $sWhereRetencao );

    if ( $clpensaoretencao->erro_status == "0" ) {

      $sMensagemErro  = $clpensaoretencao->erro_msg;
      $lErro = true;
    }

    if ( ! $lErro ) {
      $clpensao->excluir( $r52_anousu, $r52_mesusu, $r52_regist, $numcgm );
      if ( $clpensao->erro_status == "0" ) {
        $sMensagemErro  = $clpensao->erro_msg;
        $lErro = true;
      }
    }

    if ( $lErro ) {
      $db_opcao = "3";
    } else {
      if ( isset( $db_opcaoal ) && $db_opcaoal == 33 ) {
        unset( $r52_regist, $z01_nome );
        unset( $clicar );
      } else if ( isset( $db_opcaoal ) && $db_opcaoal == 22 ) {
        $clicar = "clicar";
      }
      $limpar_campos = true;
    }


  }

  if ( $lErro ) {
    db_msgbox($sMensagemErro);
  }
}
if ( !isset($opcao) && ( isset($oPost->incluir) || isset($oPost->alterar) || isset($oPost->excluir) ) ) {
  db_fim_transacao( $lErro );
}
