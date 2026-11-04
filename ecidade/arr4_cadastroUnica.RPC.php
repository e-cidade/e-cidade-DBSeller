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

use App\Domain\Tributario\Fiscalizacao\Services\CheckUseModuloFiscalizacaoService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));

require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_recibounicageracao_classe.php"));

$oDaoReciboUnicaGeracao = new cl_recibounicageracao();
$oJson                  = new services_json();

$oParam                 = $oJson->decode(str_replace("\\","",$_POST["json"]));

$oRetorno               = new stdClass();
$oRetorno->status       = 1;
$oRetorno->message      = '';

$aDadosRetorno          = array();
$useFiscalizacao = CheckUseModuloFiscalizacaoService::verify();
try {
	switch ($oParam->exec) {

		case "getExercicios":

			$bTaxaIptu = false;

			if($oParam->iCadTipoDebito == 1){
				$sql = "SELECT
		            j18_taxaseparada
		                from
		                    cadastro.cfiptu
		                order by j18_anousu desc
		                limit 1";
			    $rs = db_query($sql);
			    $bTaxaIptu = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;
			}

			$sSqlExercicios   = $oDaoReciboUnicaGeracao->sql_query_debitosExercicios($oParam->sTipoPesquisa, $oParam->sChavePesquisa, $oParam->iCadTipoDebito, false, 0, $bTaxaIptu, $useFiscalizacao);
			$rsExercicios     = $oDaoReciboUnicaGeracao->sql_record($sSqlExercicios);

			if($rsExercicios &&  pg_num_rows($rsExercicios) > 0){
				$aDadosRetorno  = db_utils::getCollectionByRecord($rsExercicios, false, false, true);
			}

			break;

		case "getExerciciosGeral":

            $bTaxaIptu = false;

			if ($oParam->iCadTipoDebito == 1) {
				$sql = "SELECT j18_taxaseparada
		                  FROM cadastro.cfiptu
		                 ORDER BY j18_anousu DESC
		                 LIMIT 1";

			    $rs = db_query($sql);

			    $bTaxaIptu = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;
			}

			$sSqlExercicios = $oDaoReciboUnicaGeracao->sql_query_debitosExercicios($oParam->sTipoPesquisa, $oParam->sChavePesquisa, $oParam->iCadTipoDebito, true, 0, $bTaxaIptu, $useFiscalizacao);
			$rsExercicios   = $oDaoReciboUnicaGeracao->sql_record($sSqlExercicios);

			if ($rsExercicios AND pg_num_rows($rsExercicios) > 0) {
				$aDadosRetorno  = db_utils::getCollectionByRecord($rsExercicios, false, false, true);
			}

			break;

		case "getTiposDebito":

			$bTaxaIptu = false;

			$sql = "SELECT
		            j18_taxaseparada
		                from
		                    cadastro.cfiptu
		                order by j18_anousu desc
		                limit 1";
		    $rs = db_query($sql);
		    $bTaxaIptu = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;

			$sSqlTiposDebitos = $oDaoReciboUnicaGeracao->sql_query_pesquisa($oParam->sTipoPesquisa, $oParam->sChavePesquisa, false, null, $bTaxaIptu);
			$rsTiposDebitos   = $oDaoReciboUnicaGeracao->sql_record($sSqlTiposDebitos);

			if($rsTiposDebitos &&  pg_num_rows($rsTiposDebitos) > 0){
				$aDadosRetorno  = db_utils::getCollectionByRecord($rsTiposDebitos, false, false, true);
			}

			break;

		case "getDebitos":

			$bTaxaIptu = false;

			$sql  = "SELECT j18_taxaseparada				 ";
		    $sql .= "  FROM cadastro.cfiptu 				 ";

		    if($oParam->iExercicio){
			    $sql .= " WHERE 								 ";
				$sql .= "      j18_anousu = {$oParam->iExercicio}";
			} else {
				$sql .= " ORDER BY j18_anousu DESC";
			}

		    $sql .= " LIMIT 1								 ";
		    $rs = db_query($sql);
		    $bTaxaIptu = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;

			$sSqlDebitos = $oDaoReciboUnicaGeracao->sql_query_debitosExercicios($oParam->sTipoPesquisa, $oParam->sChavePesquisa, $oParam->iCadTipoDebito,false,$oParam->iExercicio, $bTaxaIptu, $useFiscalizacao);
			$rsDebitos   = $oDaoReciboUnicaGeracao->sql_record($sSqlDebitos);

			if($rsDebitos &&  pg_num_rows($rsDebitos) > 0){
			  $aDadosRetorno  = db_utils::getCollectionByRecord($rsDebitos, false, false, true);
			}

			break;

		default:
		  throw new ErrorException("Nenhuma Opção Definida");
	  	  break;
	}

} catch (ErrorException $eErro){
	$oRetorno->status  = 2;
	$oRetorno->msg     = urlencode($eErro->getMessage());
}
$oRetorno->aDados = $aDadosRetorno;
echo $oJson->encode($oRetorno);
