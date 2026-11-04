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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/PrevisaoFase.php"));
require_once(modification("model/PrevisaoFaseCollection.php"));
require_once(modification("std/DBTime.php"));
require_once(modification("classes/db_calend_classe.php"));
require_once(modification("classes/db_tarefaprevisaofaserecurso_classe.php"));
require_once(modification("classes/db_tarefaprevisaocadfase_classe.php"));

$oJson    = new services_json();
$oParam   = @$oJson->decode(str_replace("\\","",@$_POST["json"]));

//var_dump($oParam);

switch ($oParam->exec) {

  case "getTarefas": 
 
  $aTarefas = getTarefas();

  echo $oJson->encode($aTarefas);
  
  break;

  case "getDetalheTarefa":

  $oDaoTarefas = db_utils::getDao('tarefa');

  $sCampos  = " distinct ";
  $sCampos .= " at40_sequencial         as tarefa, ";
  $sCampos .= " substr(at40_descr,1,30) as descricao, ";
  $sCampos .= " at40_descr              as descricao_completa, ";
  $sCampos .= " nome                    as responsavel, ";
  $sCampos .= " at40_diaini             as inicio_previsto, ";
  $sCampos .= " at40_diafim             as final_previsto, ";
  $sCampos .= " at40_obs                as tarefa_obs, ";
  $sCampos .= " at40_horainidia         as hora_inicio_previsto, ";
  $sCampos .= " at40_horafim            as hora_final_previsto, ";
  $sCampos .= " at40_progresso          as progresso ";
  $sWhere  = "  at40_sequencial = {$oParam->iCodTarefa} ";
  $rsTarefas = $oDaoTarefas->sql_record($oDaoTarefas->sql_query_previsao(null,$sCampos, "final_previsto",$sWhere));
  if ($rsTarefas) {
    $oTarefa = db_utils::fieldsMemory($rsTarefas,0,false,false,true);
  } else {
    $sMensagem = "Nenhum dado retornado";
    $iStatus   = 2;
    $oTarefa   = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

  $sSqlTotaisSituacao  = "    select at46_codigo, ";
  $sSqlTotaisSituacao .= "           at46_descr,  ";
  $sSqlTotaisSituacao .= "           cast( sum( cast(at43_horafim as time) - ";
  $sSqlTotaisSituacao .= "                      cast(at43_horainidia as time) ) as time  ";
  $sSqlTotaisSituacao .= "           ) as esforco ";
  $sSqlTotaisSituacao .= "      from tarefalog    ";
  $sSqlTotaisSituacao .= "           inner join tarefalogsituacao on tarefalogsituacao.at48_tarefalog = tarefalog.at43_sequencial ";
  $sSqlTotaisSituacao .= "           inner join tarefacadsituacao on tarefacadsituacao.at46_codigo    = tarefalogsituacao.at48_situacao ";
  $sSqlTotaisSituacao .= "     where at43_tarefa = {$oParam->iCodTarefa} ";
  $sSqlTotaisSituacao .= "       and trim(at43_horafim) <> '' ";
  $sSqlTotaisSituacao .= "    group by at46_codigo, ";
  $sSqlTotaisSituacao .= "             at46_descr   ";
  $sSqlTotaisSituacao .= "    order by at46_codigo, ";
  $sSqlTotaisSituacao .= "             at46_descr   ";

  $rsTotaisSituacao = $oDaoTarefas->sql_record($sSqlTotaisSituacao);
  $aSituacaoTarefas = db_utils::getCollectionByRecord($rsTotaisSituacao,false,false,true);
  $aRetorno = array("oDetalheTarefa"=>$oTarefa,"aSituacaoTarefas"=>$aSituacaoTarefas);

  echo $oJson->encode($aRetorno);
  break;	
	
  case "getTarefas_old": 

  $oDaoTarefas = db_utils::getDao('tarefa');

  $sCampos  = " distinct ";
  $sCampos .= " at40_sequencial         as tarefa,      ";
  $sCampos .= " at40_autorizada         as autorizada,  ";  
  $sCampos .= " at81_ordem,                             ";  
  $sCampos .= " substr(at40_descr,1,65) as descricao,   ";
  $sCampos .= " nome                    as responsavel, ";
  $sCampos .= " (select at82_dataini        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial   ";
  $sCampos .= "   order by to_timestamp((at82_dataini|| ' ' ||at82_horaini)::text, 'YYYY-MM-DD HH24:MI') limit 1 ";
  $sCampos .= " ) as inicio_previsto,                 ";
  // $sCampos .= " at40_diaini             as inicio_previsto, ";
  $sCampos .= " (select at82_datafim        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial and at82_datafim is not null ";
  $sCampos .= "   order by to_timestamp((at82_datafim|| ' ' ||at82_horafim)::text, 'YYYY-MM-DD HH24:MI') desc limit 1 ";
  $sCampos .= " ) as final_previsto,                  ";
  // $sCampos .= " at40_diafim             as final_previsto, ";
  $sCampos .= " (select at82_horaini        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial ";
  $sCampos .= "   order by to_timestamp((at82_dataini|| ' ' ||at82_horaini)::text, 'YYYY-MM-DD HH24:MI') limit 1 "; 
  $sCampos .= " ) as hora_inicio_previsto,                 ";
  // $sCampos .= " at40_horainidia         as hora_inicio_previsto, ";
  $sCampos .= " (select at82_horafim        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial and at82_datafim is not null";
  $sCampos .= "   order by to_timestamp((at82_datafim|| ' ' ||at82_horafim)::text, 'YYYY-MM-DD HH24:MI') desc limit 1 "; 
  $sCampos .= " ) as hora_final_previsto,   ";
  // $sCampos .= " at40_horafim            as hora_final_previsto,  ";
  $sCampos .= " at40_progresso          as progresso             ";

/*
  $sWhere  = "     at40_diaini >= '2009-09-01' ";
  $sWhere .= " and at55_motivo = 2 "; // Tarefas de melhoria 
  $sWhere .= " and at40_responsavel in ( select db_usuarios.id_usuario "; 
  $sWhere .= "                             from db_usuarios ";
  $sWhere .= "                                  inner join db_depusu on db_depusu.id_usuario = db_usuarios.id_usuario "; 
  $sWhere .= "                            where usuarioativo = '1' "; 
  $sWhere .= "                              and coddepto = 2 ) "; // Tarefas dos usuarios do departamento Programação dbseller
  $sWhere .= " and at40_autorizada is true "; // Tarefas autorizadas
  $sWhere .= " and at39_usuario = 11136005 "; // Tarefas autorizadas pelo usuario Robson
*/

  $rsTarefas = $oDaoTarefas->sql_record($oDaoTarefas->sql_query_previsao(null,$sCampos, "at81_ordem,final_previsto"));
  
  if ($rsTarefas) {
    $aTarefas = db_utils::getCollectionByRecord($rsTarefas,false,false,true); 
  } else {
    $sMensagem  = "Nenhum dado retornado".pg_last_error();
    $iStatus    = 2;
    $aTarefas = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

  $aDadosValores = array();

  echo $oJson->encode($aTarefas);
  break;

  case "getDetalheTarefa":

  $oDaoTarefas = db_utils::getDao('tarefa');

  $sCampos  = " distinct ";
  $sCampos .= " at40_sequencial         as tarefa, ";
  $sCampos .= " substr(at40_descr,1,30) as descricao, ";
  $sCampos .= " at40_descr              as descricao_completa, ";
  $sCampos .= " nome                    as responsavel, ";
  $sCampos .= " at40_diaini             as inicio_previsto, ";
  $sCampos .= " at40_diafim             as final_previsto, ";
  $sCampos .= " at40_obs                as tarefa_obs, ";
  $sCampos .= " at40_horainidia         as hora_inicio_previsto, ";
  $sCampos .= " at40_horafim            as hora_final_previsto, ";
  $sCampos .= " at40_progresso          as progresso ";
  $sWhere  = "  at40_sequencial = {$oParam->iCodTarefa} ";
  $rsTarefas = $oDaoTarefas->sql_record($oDaoTarefas->sql_query_previsao(null,$sCampos, "final_previsto",$sWhere));
  if ($rsTarefas) {
    $oTarefa = db_utils::fieldsMemory($rsTarefas,0,false,false,true);
  } else {
    $sMensagem = "Nenhum dado retornado";
    $iStatus   = 2;
    $oTarefa   = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

  $sSqlTotaisSituacao  = "    select at46_codigo, ";
  $sSqlTotaisSituacao .= "           at46_descr,  ";
  $sSqlTotaisSituacao .= "           cast( sum( cast(at43_horafim as time) - ";
  $sSqlTotaisSituacao .= "                      cast(at43_horainidia as time) ) as time  ";
  $sSqlTotaisSituacao .= "           ) as esforco ";
  $sSqlTotaisSituacao .= "      from tarefalog    ";
  $sSqlTotaisSituacao .= "           inner join tarefalogsituacao on tarefalogsituacao.at48_tarefalog = tarefalog.at43_sequencial ";
  $sSqlTotaisSituacao .= "           inner join tarefacadsituacao on tarefacadsituacao.at46_codigo    = tarefalogsituacao.at48_situacao ";
  $sSqlTotaisSituacao .= "     where at43_tarefa = {$oParam->iCodTarefa} ";
  $sSqlTotaisSituacao .= "       and trim(at43_horafim) <> '' ";
  $sSqlTotaisSituacao .= "    group by at46_codigo, ";
  $sSqlTotaisSituacao .= "             at46_descr   ";
  $sSqlTotaisSituacao .= "    order by at46_codigo, ";
  $sSqlTotaisSituacao .= "             at46_descr   ";

  $rsTotaisSituacao = $oDaoTarefas->sql_record($sSqlTotaisSituacao);
  $aSituacaoTarefas = db_utils::getCollectionByRecord($rsTotaisSituacao,false,false,true);
  $aRetorno = array("oDetalheTarefa"=>$oTarefa,"aSituacaoTarefas"=>$aSituacaoTarefas);

  echo $oJson->encode($aRetorno);
  break;

  case "getRegistros":

  $oDaoTarefaLog = db_utils::getDao('tarefalog');

  $sCampos  = " at43_descr,      ";
  $sCampos .= " at43_obs,        ";
  $sCampos .= " at43_diaini,     ";
  $sCampos .= " at43_diafim,     ";
  $sCampos .= " at43_horainidia, ";
  $sCampos .= " at43_horafim,    ";
  $sCampos .= " at43_tarefa,     ";
  $sCampos .= " nome             ";
  $sWhere  = "  at43_tarefa = {$oParam->iCodTarefa} ";
  $rsTarefaLog = $oDaoTarefaLog->sql_record($oDaoTarefaLog->sql_query_usua(null,$sCampos, "at43_diaini,at43_horainidia desc",$sWhere));

  if ($rsTarefaLog) {
    $aRegistros = db_utils::getCollectionByRecord($rsTarefaLog,false,false,true);
  } else {
    $sMensagem  = "Nenhum dado retornado";
    $iStatus    = 2;
    $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

  echo $oJson->encode($aRegistros);
  break;
  
  case "getPrevisaoTarefa":

  $sSqlLancamentos  = "select at84_sequencial  as at46_codigo,      ";
  $sSqlLancamentos .= "       at84_descricao   as at46_descr,       ";
  $sSqlLancamentos .= "       (select id_usuario      ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "               inner join tarefaprevisaofaserecurso on tarefaprevisaofaserecurso.at83_tarefaprevisaofase = tarefaprevisaofase.at82_sequencial ";
  $sSqlLancamentos .= "               inner join db_usuarios    on db_usuarios.id_usuario                      = tarefaprevisaofaserecurso.at83_usuario ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' LIMIT 1) as id_usuario, ";
  $sSqlLancamentos .= "       (select nome      ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "               inner join tarefaprevisaofaserecurso on tarefaprevisaofaserecurso.at83_tarefaprevisaofase = tarefaprevisaofase.at82_sequencial ";
  $sSqlLancamentos .= "               inner join db_usuarios    on db_usuarios.id_usuario                      = tarefaprevisaofaserecurso.at83_usuario ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC LIMIT 1) as nome, ";
  $sSqlLancamentos .= "       (select at82_qtdhoras ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC  LIMIT 1 ) as at82_qtdhoras, ";
  $sSqlLancamentos .= "       (select at82_dataini ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC  LIMIT 1 ) as at82_dataini, ";
  $sSqlLancamentos .= "       (select at82_horaini ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC  LIMIT 1 ) as at82_horaini, ";
  $sSqlLancamentos .= "       (select at82_datafim ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC  LIMIT 1 ) as at82_datafim, ";
  $sSqlLancamentos .= "       (select at82_horafim ";
  $sSqlLancamentos .= "          from tarefaprevisaofase ";
  $sSqlLancamentos .= "               inner join tarefaprevisao on tarefaprevisao.at81_sequencial              = tarefaprevisaofase.at82_tarefaprevisao   ";
  $sSqlLancamentos .= "                                        and tarefaprevisao.at81_tarefa                  = {$oParam->iCodTarefa} ";
  $sSqlLancamentos .= "         where tarefaprevisaofase.at82_tarefaprevisaocadfase = tarefaprevisaocadfase.at84_sequencial  and tarefaprevisaofase.at82_ativo = 't' ORDER BY at82_sequencial DESC  LIMIT 1 ) as at82_horafim ";
  $sSqlLancamentos .= "  from tarefaprevisaocadfase  ";
  $sSqlLancamentos .= "  ";
  //die($sSqlLancamentos);
  $rsLancamentos    = db_query($sSqlLancamentos);

  if ($rsLancamentos && pg_num_rows($rsLancamentos) > 0) {
    $aRegistros = db_utils::getCollectionByRecord($rsLancamentos,false,false,true);
  } else {
    $sMensagem  = "Nenhum dado retornado";
    $iStatus    = 2;
    $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }
  $oRetorno = new stdClass();
  $oRetorno->iCodTarefa = $oParam->iCodTarefa;
  $oRetorno->aRegistros = $aRegistros;
  echo $oJson->encode($oRetorno);
  break;

  case "lancarRegistros":
  	
  	$oDaoTarefaPrevisao        = db_utils::getDao('tarefaprevisao');
	  $oDaoTarefaPrevisaoFase    = db_utils::getDao('tarefaprevisaofase');
	  $oDaoTarefaPrevisaoRecurso = db_utils::getDao('tarefaprevisaofaserecurso');
	  
	  $sSqlTarefaPrevisao = $oDaoTarefaPrevisao->sql_query_file(null, "at81_sequencial", "", "at81_tarefa={$oParam->iCodTarefa}");
	  $rsTarefaPrevisao   = $oDaoTarefaPrevisao->sql_record($sSqlTarefaPrevisao);
	  $tarefaprevisao     = db_utils::fieldsMemory($rsTarefaPrevisao,0)->at81_sequencial;
	  
	  $sSqlPrevisaoFase = $oDaoTarefaPrevisaoFase->sql_query(null, "*, 
                                                            (SELECT at83_usuario FROM tarefaprevisaofaserecurso
                                                             WHERE at83_tarefaprevisaofase = tarefaprevisaofase.at82_sequencial) as recurso", 
                                                           "", "at82_ativo='t'");
    $rsPrevisaoFase   = $oDaoTarefaPrevisaoFase->sql_record($sSqlPrevisaoFase);

    $aPrevisaoFase = new PrevisaoFaseCollection();
    
    /*
     * CARREGA AS PREVISÕES DO BANCO PARA O OBJETO PrevisaoFaseCollection
     */
    if ($rsPrevisaoFase) {
        
      $aPrevisoes = db_utils::getCollectionByRecord($rsPrevisaoFase);
      
      // Adiciona os objetos PrevisaoFase para o array //
      foreach ($aPrevisoes as $oPrevFase) {
          
        $iDtIni = strtotime("{$oPrevFase->at82_dataini} {$oPrevFase->at82_horaini}");
        $iDtFim = strtotime("{$oPrevFase->at82_datafim} {$oPrevFase->at82_horafim}");
          
        $oPrevisaoFase = new PrevisaoFase();
          
        $oPrevisaoFase->setCodTarefa($oPrevFase->at81_tarefa);
        $oPrevisaoFase->setCodTarefaPrevisao($oPrevFase->at81_sequencial);
        $oPrevisaoFase->setCodFase($oPrevFase->at82_sequencial);
        $oPrevisaoFase->setCodSituacao($oPrevFase->at84_sequencial);
        $oPrevisaoFase->setCodUsuario($oPrevFase->recurso);
        $oPrevisaoFase->setQtdHoras($oPrevFase->at82_qtdhoras);
        $oPrevisaoFase->setDtIni($iDtIni);
        $oPrevisaoFase->setDtFim($iDtFim);
        $oPrevisaoFase->setStatus("A");
          
        $aPrevisaoFase->addPrevisaoFase($oPrevisaoFase);
      }
    }
	  
    /*
     * CRIA E INSERE AS NOVAS PREVISÔES NO OBJETO PrevisaoFaseCollection
     */
	  foreach ($oParam->aRegistros as $oRegistro) {
	      
		  $sDataIni = $oRegistro->sDtIni ? implode('-',array_reverse(explode('/',$oRegistro->sDtIni))) : '';
      $sHoraIni = $oRegistro->sDtIni ? ($oRegistro->sHoraIni?$oRegistro->sHoraIni:'00:00') : '';        
      $sDataFim = $oRegistro->sDtFim ? implode('-',array_reverse(explode('/',$oRegistro->sDtFim))) : '';
      $sHoraFim = $oRegistro->sDtFim ? ($oRegistro->sHoraFim?$oRegistro->sHoraFim:'00:00') : '';
      
      /*
       * TRASNFORMA DATAS EM INTEIROS TIMESTAMPS 
       */      
      $iDtIni = strtotime("{$sDataIni} {$sHoraIni}");
      $iDtFim = strtotime("{$sDataFim} {$sHoraFim}");
             
	    $oNewPrevisaoFase = new PrevisaoFase();
	    $oNewPrevisaoFase->setCodTarefa($oParam->iCodTarefa);
	    $oNewPrevisaoFase->setCodTarefaPrevisao($tarefaprevisao);
	    $oNewPrevisaoFase->setCodSituacao($oRegistro->iCodSituacao);
	    $oNewPrevisaoFase->setCodUsuario($oRegistro->iCodUsuario);
	    $oNewPrevisaoFase->setQtdHoras($oRegistro->nQtdHoras);
	    $oNewPrevisaoFase->setDtIni($iDtIni);
	    $oNewPrevisaoFase->setDtFim($iDtFim);
	    $oNewPrevisaoFase->setStatus("N");
	    
	    $aPrevisaoFase->insertPrevisao($oNewPrevisaoFase);		  			  	
		}
	  /*
	   * APÓS PROCESSAR OS DADOS EM MEMÓRIA PERSISTE NA BASE
	   */
	  $sqlerro = false;
	  db_inicio_transacao();	  
	  if (!$aPrevisaoFase->persist())
	    $sqlerro = true; 	  
	  db_fim_transacao($sqlerro);
	  
	  if ($sqlerro) {
	  	
    	$sMensagem  = "Erro ao incluir registros";
		  $iStatus    = 0;  
		  $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));		  
		  
	  } else {
	  	
		  $sMensagem  = "Registros incluidos com sucesso";
      $iStatus    = 1;  
      $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
	  }

	 echo $oJson->encode($aRegistros);
   break;	
	  
	  //die;
	  //print_r($oParam->aRegistros);
	  //die;
/*	  
	  foreach ($oParam->aRegistros as $oRegistro) {
      
	  	$iCodUsuario = $oRegistro->iCodUsuario;

//	  	die($iCodUsuario);
	  	
	    if ($iCodUsuario) {
	  	
	      $nHoras = $oRegistro->nQtdHoras;
	  
			  if ($oRegistro->sDtIni) { 
				  $sDataIni = implode('-',array_reverse(explode('/',$oRegistro->sDtIni)));
				  $sHoraIni = $oRegistro->sHoraIni;
			  }
			  
			  if ($oRegistro->sDtFim != "") {
				  $sDataFim = implode('-',array_reverse(explode('/',$oRegistro->sDtFim)));
				  $sHoraFim = $oRegistro->sHoraFim;
			  }
			  
			  $sDataFinal   = "{$sDataFim} {$sHoraFim}";
			  $sDataInicial = "{$sDataIni} {$sHoraIni}";
			  		  
			  $sWhere  = "at83_usuario = {$iCodUsuario} ";
			  $sWhere .= "AND ";
			  $sWhere .= "(";
			  $sWhere .= "(at82_dataini||' '||at82_horaini BETWEEN '{$sDataInicial}' AND '{$sDataFinal}') ";
			  $sWhere .= "OR (at82_datafim||' '||at82_horafim BETWEEN '{$sDataInicial}' AND '{$sDataFinal}')";
			  $sWhere .= ")"; 
			  
			  $sSql         = $oDaoTarefaPrevisaoRecurso->sql_query(null, "*", "", $sWhere);
		    //echo $sWhere."<br>";
	      
		    die($sSql);
	      
		    $rsNConflitos = $oDaoTarefaPrevisaoRecurso->sql_record($sSql);
	      $oConflito   = db_utils::fieldsMemory($rsNConflitos,0);
	
	      if ($oConflito->nconflitos > 0) 
	        $lConflito = true;
		    
	    }
	  } */

	 /* 
	  die;
  	
		  $lErro = false;
		  db_inicio_transacao();
		
		  $rsTarefaPrevisao          = $oDaoTarefaPrevisao->sql_record($oDaoTarefaPrevisao->sql_query_file(null,"at81_sequencial",null,"at81_tarefa = {$oParam->iCodTarefa}"));
		  if (!$rsTarefaPrevisao || pg_num_rows($rsTarefaPrevisao) == 0) {
		    $lErro     = true;
		    $sMensagem = "Previsao da tarefa não encontrada, tarefa : {$oParam->iCodTarefa}";
		  }
		  $oTarefaPrevisao = db_utils::fieldsMemory($rsTarefaPrevisao,0);
		
		  $rsTarefaPrevisaoFase = $oDaoTarefaPrevisaoFase->sql_record($oDaoTarefaPrevisaoFase->sql_query(null,"distinct at82_sequencial",null,"at81_tarefa = {$oParam->iCodTarefa}"));
		  if ($rsTarefaPrevisaoFase && pg_num_rows($rsTarefaPrevisaoFase) > 0 ) {
		    for ($i = 0; $i < pg_num_rows($rsTarefaPrevisaoFase); $i++) {
		      $oTarefaPrevisaoFase = db_utils::fieldsMemory($rsTarefaPrevisaoFase,$i);
		      $oDaoTarefaPrevisaoRecurso->excluir(null,"at83_tarefaprevisaofase = {$oTarefaPrevisaoFase->at82_sequencial}");
		      if ($oDaoTarefaPrevisaoRecurso->erro_status == '0') {
		        $lErro     = true;
		        $sMensagem = $oDaoTarefaPrevisaoRecurso->erro_msg;
		      }
		    }
		  }
		
		  $oDaoTarefaPrevisaoFase->excluir(null,"at82_tarefaprevisao = {$oTarefaPrevisao->at81_sequencial}");
		  if ($oDaoTarefaPrevisaoFase->erro_status == '0') {
		    $lErro     = true;
		    $sMensagem = $oDaoTarefaPrevisaoFase->erro_msg;
		  }
		
		  foreach ($oParam->aRegistros as $oRegistro) {
		    
		    $sDataIni = "";
		    $sDataFim = "";
		    $oDaoTarefaPrevisaoFase->at82_tarefaprevisao        = $oTarefaPrevisao->at81_sequencial;
		    $oDaoTarefaPrevisaoFase->at82_tarefaprevisaocadfase = $oRegistro->iCodSituacao;
		    $oDaoTarefaPrevisaoFase->at82_qtdhoras              = $oRegistro->nQtdHoras;
		    if ($oRegistro->sDtIni != ""){
		      $sDataIni = implode('-',array_reverse(explode('/',$oRegistro->sDtIni)));
		    }
		    if ($oRegistro->sDtFim != ""){
		      $sDataFim = implode('-',array_reverse(explode('/',$oRegistro->sDtFim)));
		    }
		    $oDaoTarefaPrevisaoFase->at82_dataini           = $sDataIni;
		    $oDaoTarefaPrevisaoFase->at82_horaini           = $oRegistro->sHoraIni;
		    $oDaoTarefaPrevisaoFase->at82_datafim           = $sDataFim;
		    $oDaoTarefaPrevisaoFase->at82_horafim           = $oRegistro->sHoraFim;
		    $oDaoTarefaPrevisaoFase->at82_ativo             = 't';
		    $oDaoTarefaPrevisaoFase->incluir(null);
		    if ($oDaoTarefaPrevisaoFase->erro_status == '0') {
		      $lErro     = true;
		      $sMensagem = $oDaoTarefaPrevisaoFase->erro_msg;
		      break;
		    }
		
		    if (isset($oRegistro->iCodUsuario) && $oRegistro->iCodUsuario != "" ) {
		      $oDaoTarefaPrevisaoRecurso->at83_tarefaprevisaofase = $oDaoTarefaPrevisaoFase->at82_sequencial;
		      $oDaoTarefaPrevisaoRecurso->at83_usuario            = $oRegistro->iCodUsuario;
		      $oDaoTarefaPrevisaoRecurso->incluir(null);
		      if ($oDaoTarefaPrevisaoRecurso->erro_status == '0') {
		        $lErro     = true;
		        $sMensagem = $oDaoTarefaPrevisaoRecurso->erro_msg;
		        break;
		      }
		    }
		
		  }
		
		  db_fim_transacao($lErro);
		  
			if ($lErro) {
		    // $sMensagem  = "Previsão nao econtrada";
		    $iStatus    = 2;
		    $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
		  }else{
		    $sMensagem  = "Registros incluidos com sucesso";
		    $iStatus    = 1;
		    $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
		
		  }  
  */


  
  case "getTarefasPrevisao":

	
  $oDaoTarefa       = db_utils::getDao('tarefa');
  
  $sCampos  = " tarefa.at40_sequencial as tarefa,   ";      
  $sCampos .= " tarefa.at40_descr      as descricao ";
  
	$sSqlListaTarefas = $oDaoTarefa->sql_query_semprevisao(null,$sCampos);
  $rsTarefas        = db_query($sSqlListaTarefas);

  if ($rsTarefas) {
    $aTarefas = db_utils::getCollectionByRecord($rsTarefas,false,false,true); 
  } else {
    $sMensagem = "Nenhum dado retornado".pg_last_error();
    $iStatus   = 2;
    $aTarefas  = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

  $aDadosValores = array();

  echo $oJson->encode($aTarefas);
  break;

  
  case "salvarTarefasPrevisao":

	
  $oDaoTarefaPrevisao = db_utils::getDao('tarefaprevisao');
  $oDaoTarefa         = db_utils::getDao('tarefa');

  $lErro     = false;
  $sMensagem = 'Alteração efetuada com sucesso!';

  db_inicio_transacao();
  
  $sSqlListaSemPrevisao = $oDaoTarefa->sql_query_semprevisao(null,'at40_sequencial');
  $sSqlExcluirTarefas   = "delete from tarefaprevisao where at81_tarefa in ( $sSqlListaSemPrevisao )";
  $rsExcluirLista       = db_query($sSqlExcluirTarefas);
  
  if ( !$rsExcluirLista ) {
  	$sMensagem = "Erro ao recriar lista de tarefas!";
  	$lErro     = true;
  }
  
  if ( count($oParam->aTarefas) && !$lErro ) {

  	foreach ( $oParam->aTarefas as $iInd => $iCodTarefa ) {
  		
	 	  $oDaoTarefaPrevisao->at81_tarefa  = $iCodTarefa;
		  $oDaoTarefaPrevisao->at81_usuario = db_getsession('DB_id_usuario');
		  $oDaoTarefaPrevisao->at81_dtlanc  = date('Y-m-d',db_getsession('DB_datausu'));
		  $oDaoTarefaPrevisao->at81_hora    = db_hora();
		  $oDaoTarefaPrevisao->at81_ordem   = ($iInd+1);
		  $oDaoTarefaPrevisao->incluir(null); 
		  
		  if ( $oDaoTarefaPrevisao->erro_status == 0 ) {
		    $lErro     = true;     
		    $sMensagem = $oDaoTarefaPrevisao->erro_msg;
		    break;
		  }
		    		
  	}
  	
  }
  
  db_fim_transacao($lErro);


  if ($lErro) {
    $iStatus = 2;
  }else{
    $iStatus = 1;
  }
  
  $aRegistros = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  
  echo $oJson->encode($aRegistros);
  break;

}	

function getTarefas($tar=0, $aTarefasFinal=array()) {

  $oDaoTarefas = db_utils::getDao('tarefa');

  $sCampos  = " distinct ";
  $sCampos .= " at40_sequencial         as tarefa,      ";
  $sCampos .= " at40_autorizada         as autorizada,  ";  
  $sCampos .= " at81_ordem,                             ";  
  $sCampos .= " substr(at40_descr,1,65) as descricao,   ";
  $sCampos .= " nome                    as responsavel, ";
  $sCampos .= " (select at82_dataini        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial AND at82_ativo = 't'  ";
  $sCampos .= "   order by to_timestamp((at82_dataini|| ' ' ||at82_horaini)::text, 'YYYY-MM-DD HH24:MI') DESC limit 1 ";
  $sCampos .= " ) as inicio_previsto,                 ";
  $sCampos .= " (select at82_datafim        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial and at82_datafim is not null  AND at82_ativo = 't' ";
  $sCampos .= "   order by to_timestamp((at82_datafim|| ' ' ||at82_horafim)::text, 'YYYY-MM-DD HH24:MI') desc limit 1 ";
  $sCampos .= " ) as final_previsto,                  ";
  $sCampos .= " (select at82_horaini        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial  AND at82_ativo = 't' ";
  $sCampos .= "   order by to_timestamp((at82_dataini|| ' ' ||at82_horaini)::text, 'YYYY-MM-DD HH24:MI') DESC  limit 1 "; 
  $sCampos .= " ) as hora_inicio_previsto,                 ";
  $sCampos .= " (select at82_horafim        ";
  $sCampos .= "    from tarefaprevisaofase  ";
  $sCampos .= "         inner join tarefaprevisao on tarefaprevisao.at81_sequencial = tarefaprevisaofase.at82_tarefaprevisao ";
  $sCampos .= "   where at81_tarefa = at40_sequencial and at82_datafim is not null  AND at82_ativo = 't' ";
  $sCampos .= "   order by to_timestamp((at82_datafim|| ' ' ||at82_horafim)::text, 'YYYY-MM-DD HH24:MI') desc limit 1 "; 
  $sCampos .= " ) as hora_final_previsto,   ";
  
  $sCampos .= " (select count(*)       ";
  $sCampos .= "    from tarefadependencia  ";
  $sCampos .= "   where at85_tarefapai = at40_sequencial ";
  $sCampos .= " ) as nfilhas,   ";
  
  $sCampos .= " at40_progresso          as progresso             ";

  if ($tar > 0) {
    $sWhere  = " at40_sequencial in (select at85_tarefa from tarefadependencia where at85_tarefapai = {$tar})";
  } else {
  	$sWhere  = " not exists (select * from tarefadependencia where at85_tarefa = at40_sequencial)";  	
  }
  
  $rsTarefas = $oDaoTarefas->sql_record($oDaoTarefas->sql_query_previsao(null,$sCampos, "at81_ordem,final_previsto", $sWhere));

  if ($rsTarefas) {  	
    $aTarefas = db_utils::getCollectionByRecord($rsTarefas,false,false,true);
    
    foreach($aTarefas as $oTarefa) {
    	
    	
    	if ($oTarefa->nfilhas > 0) { 
    	  $oTarefa->aFilhas = getTarefas($oTarefa->tarefa, array());
    	  $oTarefa->iTemfilhas = 1;
    	} else {
    		$oTarefa->iTemfilhas = 0;  
    	}
    	
    	$aTarefasFinal[] = $oTarefa;
    }
    return $aTarefasFinal;
    
  } else {
    $sMensagem  = "Nenhum dado retornado".pg_last_error();
    $iStatus    = 2;
    $aTarefas = array("iStatus"=>$iStatus, "sMensagem"=>urlencode($sMensagem));
  }

}
/*(
function verificaConflitos($aReg = null, $aConflitos = array()) {
	
	if ($aReg!=null) {
		
    foreach ($aReg as $oRegistro) {
    	
      $iCodUsuario = $oRegistro->iCodUsuario;

      if ($iCodUsuario) {
      
        $nHoras = $oRegistro->nQtdHoras;
    
        if ($oRegistro->sDtIni) { 
          $sDataIni = implode('-',array_reverse(explode('/',$oRegistro->sDtIni)));
          $sHoraIni = $oRegistro->sHoraIni;
        }
        
        if ($oRegistro->sDtFim != "") {
          $sDataFim = implode('-',array_reverse(explode('/',$oRegistro->sDtFim)));
          $sHoraFim = $oRegistro->sHoraFim;
        }
        
        $sDataFinal   = "{$sDataFim} {$sHoraFim}";
        $sDataInicial = "{$sDataIni} {$sHoraIni}";
              
        $sWhere  = "at83_usuario = {$iCodUsuario} ";
        $sWhere += "AND ";
        $sWhere += "(";
        $sWhere += "at82_dataini||' '||at82_horaini BETWEEN '{$sDataInicial}' AND '{$sDataFinal}')";
        $sWhere += "OR (at82_datafim||' '||at82_horafim BETWEEN '{$sDataInicial}' AND '{$sDataFinal}')";
        $sWhere += ")"; 
        
        $sSql         = $oDaoTarefaPrevisaoRecurso->sql_query(null, "*", "", $sWhere);
        $rsNConflitos = $oDaoTarefaPrevisaoRecurso->sql_record($sSql);
         
        if ($rsNConflitos) {
          
        	$aConflitos = db_utils::getCollectionByRecord($rsNConflitos);
        	
        	foreach ($aConflitos as $oConflito) {
        		
        		$oConfTemp = new stdClass();
        		$oConfTemp->iCodSituacao = '';//$oConflito->
        		$oConfTemp->iCodUsuario  = '';//$oConflito->
        		$oConfTemp->nQtdHoras    = '';//$oConflito->
        		$oConfTemp->sDtIni       = '';//$oConflito->
        		$oConfTemp->sHoraIni     = '';//$oConflito->
        		$oConfTemp->sDtFim       = '';//$oConflito->
        		$oConfTemp->sHoraFim     = '';//$oConflito->
        		
        	
        	}
            
        }
        
      }
    }
	}

	
	else {

		return $aConflitos;
	}
 
}
*/
?>
