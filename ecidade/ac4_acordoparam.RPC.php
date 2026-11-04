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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/exceptions/BusinessException.php"));
require_once(modification("libs/exceptions/DBException.php"));
require_once(modification("libs/exceptions/ParameterException.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_acordoparam_classe.php"));
use App\Domain\Patrimonial\Licitacoes\Services\LicitaconService;

$oJson                = new services_json();
$oParam               = $oJson->decode($oJson->encode($_POST));
$oRetorno             = new stdClass();
$oRetorno->erro      = false;
$oRetorno->message   = '';

$iInstituicaoSessao = db_getsession('DB_instit');
$iAnoSessao         = db_getsession('DB_anousu');

try {

  db_inicio_transacao();

  switch ($oParam->exec) {
    case 'getParametrosConfigurados':

      $cl_acordoparam = new cl_acordoparam;
      $retorno = $cl_acordoparam->sql_query_file(null, "*", null, "ac59_instituicao = {$iInstituicaoSessao}");
      $rsHomologacaoAcordo = $cl_acordoparam->sql_record($retorno);
      $dados = db_utils::fieldsMemory($rsHomologacaoAcordo, 0);

      $oRetorno->ac59_sequencial = $dados->ac59_sequencial;
      $oRetorno->ac59_homologacaoauto = $dados->ac59_homologacaoauto;
      $oRetorno->ac59_cadastrocomissoesfiscal = $dados->ac59_cadastrocomissoesfiscal;
      $oRetorno->ac59_validalicitacon = $dados->ac59_validalicitacon;
      $oRetorno->ac59_permiteporinstit = $dados->ac59_permiteporinstit;

      break;

    case 'salvarParametrosHomologacao':
      if ($oParam->ac59_validalicitacon > 1) {
          $cl_dbconfig = new cl_db_config;
          $rsDbconfig = $cl_dbconfig->sql_record($cl_dbconfig->sql_query_file(DB_getsession("DB_instit"), "cgc"));
          db_fieldsmemory($rsDbconfig, 0);

          $orgao = false;
          try {
              $licitaconService = new LicitaconService();
              $orgao = $licitaconService->verificarOrgao($cgc);
          } catch (Exception $exception) {
              throw new Exception("Variável LICITACON_URL não configurada ou incorreta, verifique.");
          }
          if (!$orgao) {
              throw new Exception("Não foi possível identificar o código do órgão para a instituição.");
          }
      }

      $cl_acordoparam = new cl_acordoparam;

      $retorno = $cl_acordoparam->sql_query_file(null, "*", null, "ac59_instituicao = {$iInstituicaoSessao}");
      $rsHomologacaoAcordo = $cl_acordoparam->sql_record($retorno);
      $dados = db_utils::fieldsMemory($rsHomologacaoAcordo, 0);

      $cl_acordoparam->ac59_instituicao = $iInstituicaoSessao;
      $cl_acordoparam->ac59_homologacaoauto = $oParam->ac59_homologacaoauto;
      $cl_acordoparam->ac59_cadastrocomissoesfiscal = $oParam->ac59_cadastrocomissoesfiscal;
      $cl_acordoparam->ac59_validalicitacon = $oParam->ac59_validalicitacon;
      $cl_acordoparam->ac59_permiteporinstit = $oParam->ac59_permiteporinstit;
      if(!empty($dados->ac59_sequencial)) {
        $cl_acordoparam->ac59_sequencial = $oParam->ac59_sequencial;
        $cl_acordoparam->alterar();

      } else {
        $cl_acordoparam->incluir();
      }

      break;

  }

  db_fim_transacao(false);

} catch (Exception $eErro) {

  db_fim_transacao(true);
  $oRetorno->erro    = true;
  $oRetorno->message = urlencode($eErro->getMessage());
}

echo $oJson->encode($oRetorno);
