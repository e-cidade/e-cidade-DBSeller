<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2016  DBseller Servicos de Informatica
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

require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_utils.php"));
require_once (modification("libs/db_app.utils.php"));
require_once (modification("libs/db_conecta_plugin.php"));
require_once (modification("libs/db_sessoes.php"));
require_once (modification("dbforms/db_funcoes.php"));
require_once (modification("libs/JSON.php"));

$oParametros = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno    = new stdClass();

$oRetorno->erro     = false;
$oRetorno->sMessage = '';

try {

  db_inicio_transacao();
  switch ($oParametros->sExecucao) {

    case "getComparativoRetencao":

      if(empty($oParametros->sInscr) and empty($oParametros->iCompMes) and empty($oParametros->iCompAno) and empty($oParametros->sCgm)){
        throw new Exception("É obrigatório o preenchimento de pelo menos um filtro para a emissão do relatório.");
      }

      if(!empty($oParametros->iCompMes) and empty($oParametros->iCompAno)){
        throw new Exception("Não é permitido preencher apenas o mês para a emissão do relatório.");
      }

      if(!empty($oParametros->iCompAno) and strlen($oParametros->iCompAno) < 4){
        throw new Exception("Campo Ano da Competência deve ser preenchido com 4 dígitos");
      }

      if(!empty($oParametros->iCompMes) and $oParametros->iCompMes > 12){
        throw new Exception("Valor do campo mês inválido");
      }

      if((!empty($oParametros->iCompMes) and $oParametros->iCompMes > date("m")) and
         (!empty($oParametros->iCompAno) and $oParametros->iCompAno > date("Y"))
      ){
        throw new Exception("Compêtencia não pode ser maior que a atual.");
      }

      $oComparativoRetencaoNovo = new ComparativoRetencaoNovo($oParametros->sCgm,$oParametros->iCompAno,$oParametros->iCompMes,$oParametros->sInscr);
      $oRetorno->sArquivo = $oComparativoRetencaoNovo->Pdf();
      break;
  }

  db_fim_transacao(false);

} catch (Exception $oErro){

  db_fim_transacao(true);
  $oRetorno->erro     = true;
  $oRetorno->sMessage = $oErro->getMessage();
}

echo JSON::create()->stringify($oRetorno);
