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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
$oConfig = loadConfig("lab_parametros");

$oJson              = new services_json();
$oParam             = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno           = new stdClass();
$oRetorno->iStatus  = 1;
$oRetorno->sMessage = '';

try {

  db_inicio_transacao();

  switch ($oParam->exec) {

    case "buscarExames":

      $sWhere  = "     la21_i_requisicao = {$oParam->iRequisicao} ";
      
      /**
       * Se o parametro da triagem laboratorial tiver habilitado e houverem
       * laboratorios/unidades dispensados deve - se verificar apenas a 
       * situação da coleta. Além disso, se não houver unidades dispensadas,
       * a situação de processamento, oriunda da triagem, deve ser considerada.
       */

       $unidadesDispensadasTriagem = $oConfig->la49_unidadesdispensadastriagem;
      if($oConfig->la49_habilitartriagemlaboratorial == 'f'){
        $sWhere .= " and la21_c_situacao in ('30 - Coletado', '50 - Lancado') ";
      } elseif($oConfig->la49_habilitartriagemlaboratorial == 't' && !empty($unidadesDispensadasTriagem)) {
        $sWhere .= " 
          and case when la02_i_codigo in ($unidadesDispensadasTriagem) then
                    la21_c_situacao in ('30 - Coletado', '50 - Lancado') 
              else  la21_c_situacao in ('45 - Em processamento','50 - Lancado') end";
      } elseif($oConfig->la49_habilitartriagemlaboratorial == 't'){
        $sWhere .= " and la21_c_situacao in ('45 - Em processamento','50 - Lancado') ";
      }

      $sCampos = " la23_i_codigo, la23_c_descr, la21_i_codigo, la08_i_codigo, la08_c_descr, la21_c_situacao, la02_i_codigo ";

      $oDaoRequisicao = new cl_lab_requisicao();
      $oDaoLabResp = new cl_lab_labresp();
      $usuarioSessao = db_getsession('DB_id_usuario');
      $sSqlExames     = $oDaoRequisicao->sql_query_coleta_amostra(null, $sCampos, "la23_c_descr, la08_c_descr", $sWhere);
      $rsExames       = db_query($sSqlExames);

      if (!$rsExames) {
        throw new DBException("Erro ao executar a query.\n".pg_last_error(), 1);
      }
      $iLinhas = pg_num_rows($rsExames);
      if ($iLinhas == 0) {
        throw new Exception("Requisição sem exames para digitação.");
      }

      $aSetorExame = array();
      $oRetorno->laboratorios = "";
      for($i = 0; $i < $iLinhas; $i++) {

        $oDados = db_utils::fieldsMemory($rsExames, $i);
        $whereData = " (la06_d_fim >= '".date('Y-m-d')."' or la06_d_fim is null)";
        $whereUsuarioSessao = " and db_usuacgm.id_usuario = ".$usuarioSessao;
        $whereLaboratorio = " and la06_i_laboratorio= ".$oDados->la02_i_codigo;
        $where = $whereData . $whereUsuarioSessao . $whereLaboratorio;
        $sql = $oDaoLabResp->sql_query_setor(
          null, "la06_i_laboratorio, la06_permitidoconferencia", null, $where  
        );
        $rsPermissaoLaboratorio       = db_query($sql);

        $iLinhasPermissaoLaboratorio = pg_num_rows($rsPermissaoLaboratorio);

        if($iLinhasPermissaoLaboratorio == 0){
          $oDaoLaboratorio = new cl_lab_laboratorio();
          $oDaoLaboratorio->la02_i_codigo = $oDados->la02_i_codigo;
          $sql = $oDaoLaboratorio->sql_query_file($oDados->la02_i_codigo);
          $rsLaboratorio = db_query($sql);
          $oDadosLaboratorio = db_utils::fieldsMemory($rsLaboratorio, 0);
          if($oRetorno->laboratorios == ""){
            $oRetorno->laboratorios = urlencode("* ".$oDados->la08_i_codigo." - ".$oDados->la08_c_descr." - ".$oDadosLaboratorio->la02_i_codigo . " - " . $oDadosLaboratorio->la02_c_descr);          
          }else{
            $oRetorno->laboratorios .= urlencode("\n* ".$oDados->la08_i_codigo." - ".$oDados->la08_c_descr." - ". $oDadosLaboratorio->la02_i_codigo . " - " . $oDadosLaboratorio->la02_c_descr);          
          }
          continue;
        }
        
        if ( !array_key_exists($oDados->la23_i_codigo, $aSetorExame) ) {
          
          $oSetor          = new stdClass();
          $oSetor->sNome   = urlencode($oDados->la23_c_descr);
          $oSetor->iCodigo = $oDados->la23_i_codigo;
          $oSetor->aExames = array();
          
          $aSetorExame[ $oDados->la23_i_codigo ] = $oSetor;
        }
        
        $oExame            = new stdClass();
        $oExame->iCodigo   = $oDados->la21_i_codigo;
        $oExame->sNome     = urlencode($oDados->la08_c_descr);
        $oExame->lDigitado = $oDados->la21_c_situacao == '50 - Lancado';
        $oExame->permitidoconferencia = pg_fetch_all($rsPermissaoLaboratorio)[0]['la06_permitidoconferencia'];

        $aSetorExame[ $oDados->la23_i_codigo ]->aExames[] = $oExame;
      }
      $oRetorno->aExamesRequisicao = $aSetorExame;
      sort($oRetorno->aExamesRequisicao);

      break;
  }

  db_fim_transacao(false);

} catch (Exception $eErro){

  db_fim_transacao(true);
  $oRetorno->iStatus  = 2;
  $oRetorno->sMessage = urlencode($eErro->getMessage());
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo $oJson->encode($oRetorno);