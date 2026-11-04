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
require_once(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

db_postmemory($HTTP_POST_VARS);

$clsiopeservidormanutencao = new cl_siopeservidormanutencao;
$clsiopeservidorqualificacao = new cl_siopeservidorqualificacao;
$clsiopesituacao = new cl_siopesituacao;
$clrhpessoalmov = new cl_rhpessoalmov;
$clsiopesegmentoatuacao = new cl_siopesegmentoatuacao;
$clsiopequalificacao = new cl_siopequalificacao;
$clrhpessoal = new cl_rhpessoal;

$db_opcao    = 22;
$db_botao    = false;
$sPosScripts = '';
$sqlerro     = false;
$rh02_anousu = DBPessoal::getAnoFolha();
$rh02_mesusu = DBPessoal::getMesFolha();

if (isset($rh01_regist)) {

   if ($txtCodigoSiope != "" || $iSituacaoSiope != "0" || $iSegmentoSiope != "0") {
 
     $clsiopeservidormanutencao->si06_servidor = $rh01_regist;
 
     if (isset($txtCodigoSiope) && !empty($txtCodigoSiope) && $txtCodigoSiope != "") {
        $clsiopeservidormanutencao->si06_categoria = $txtCodigoSiope;
     }
 
     if (isset($iSituacaoSiope) && !empty($iSituacaoSiope) && $iSituacaoSiope != "0") {
        $clsiopeservidormanutencao->si06_situacao = $iSituacaoSiope;
     }
 
     if (isset($iSegmentoSiope) && !empty($iSegmentoSiope) && $iSegmentoSiope != "0") {
        $clsiopeservidormanutencao->si06_segmento = $iSegmentoSiope;
     }

     if (isset($incluir) && !$sqlerro) {
        db_inicio_transacao();

        try {
          $clsiopeservidormanutencao->incluir($rh01_regist);
   
          if ($clsiopeservidormanutencao->erro_status == 0) {
             throw new DBException("1 - Não foi possível incluir os atributos do servidor {$rh01_regist}. Erro: {$clsiopeservidormanutencao->erro_msg}");
          }

          if (count($qualificacao) > 0) {

             foreach ($qualificacao as $qualificacaoRegistro) {
               $clsiopeservidorqualificacao->incluir($rh01_regist, $qualificacaoRegistro);

               if ($clsiopeservidorqualificacao->erro_status == 0) {
                  throw new DBException("1 - Não foi possível incluir as qualificações do servidor {$rh01_regist}. Erro: {$clsiopeservidormanutencao->erro_msg}");
               }
             }
          }

        } catch(Exception $oErro) {
          $sqlerro = true;
          $erro_msg = $oErro->getMessage();
        }
  
        db_fim_transacao($sqlerro);  
        db_msgbox($clsiopeservidormanutencao->erro_msg);
      } else if(isset($alterar) && !$sqlerro) {
        db_inicio_transacao();
  
        try {
          $clsiopeservidormanutencao->alterar($rh01_regist);

          if ($clsiopeservidormanutencao->erro_status == 0) {
             throw new DBException("2 - Não foi possível alterar os atributos do servidor {$rh01_regist}. Erro: {$clsiopeservidormanutencao->erro_msg}");
          }

          if (count($qualificacao) > 0) {
  
             $clsiopeservidorqualificacao->excluir($rh01_regist);
             if ($clsiopeservidorqualificacao->erro_status == 0) {
                throw new DBException("2 - Não foi possível excluir as qualificações do servidor {$rh01_regist}. Erro: {$clsiopeservidorqualificacao->erro_msg}");
             }

             foreach ($qualificacao as $qualificacaoRegistro) {
               $clsiopeservidorqualificacao->incluir($rh01_regist, $qualificacaoRegistro);
  
               if ($clsiopeservidorqualificacao->erro_status == 0) {
                  throw new DBException("2 - Não foi possível incluir as qualificações do servidor {$rh01_regist}. Erro: {$clsiopeservidorqualificacao->erro_msg}");
               }
             }
          }
  
        } catch(Exception $oErro) {
          $sqlerro = true;
          $erro_msg = $oErro->getMessage();
        }

        db_fim_transacao($sqlerro);
        db_msgbox($clsiopeservidormanutencao->erro_msg);
      }
   }

   if ($txtCodigoSiope == "0" && $iSituacaoSiope == "0" && $iSegmentoSiope == "0" && count($qualificacao) == 0) {
      db_inicio_transacao();
    
      try {

        $clsiopeservidormanutencao->excluir($rh01_regist);
        if ($clsiopeservidormanutencao->erro_status == 0) {
           throw new DBException("3 - Não foi possível excluir os atributos do servidor {$rh01_regist}. Erro: {$clsiopeservidormanutencao->erro_msg}");
        }

        $clsiopeservidorqualificacao->excluir($rh01_regist);
        if ($clsiopeservidorqualificacao->erro_status == 0) {
           throw new DBException("3 - Não foi possível excluir as qualificações do servidor {$rh01_regist}. Erro: {$clsiopeservidorqualificacao->erro_msg}");
        }

      } catch(Exception $oErro) {
        $sqlerro = true;
        $erro_msg = $oErro->getMessage();
      }

      db_fim_transacao($sqlerro);
      db_msgbox($clsiopeservidormanutencao->erro_msg);
   }

   db_redireciona(basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]));

}

if(isset($chavepesquisa)) {
  $db_opcao = 2;
  $db_botao = true;
  $result   = $clrhpessoal->sql_record( $clrhpessoal->sql_query_cgm($chavepesquisa, "rh01_regist, z01_nome, z01_numcgm") );
  db_fieldsmemory($result, 0);
}

if ($db_opcao == 22) {
    $sPosScripts .= "document.form1.pesquisar.click();\n";
}
  
$sPosScripts .=  'js_tabulacaoforms("form1", "rh01_regist", true, 1, "rh01_regist", true);';

include(modification("forms/db_frmmanutencaosiope.php"));
