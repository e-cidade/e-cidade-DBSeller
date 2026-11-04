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
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("model/webservices/ControleAcessoAluno.model.php"));
$oRetorno          = new stdClass();
$oRetorno->status  = 1;
$oRetorno->message = "";
$oJson          = new services_json();
if (isset($_POST["json"])) {
  $oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
  $lEncode = true;
} else {
  $oParam = db_utils::postMemory($_POST);
  $lEncode = false;
}

switch ($oParam->exec) {

  case 'getDisciplinasRegenteEscola':

    $oRetorno->codigo_regente = 0;
    $oRetorno->nome_regente   = '';
    $oDaoDBUsusario   = db_utils::getDao("db_usuacgm");
    $iCodigoUsuario   = db_getsession("DB_id_usuario");
    $sSqlDadosRegente = $oDaoDBUsusario->sql_query($iCodigoUsuario, "z01_numcgm, z01_nome");
    $rsDadosRegente   = $oDaoDBUsusario->sql_record($sSqlDadosRegente);
    if ($oDaoDBUsusario->numrows > 0) {

      $oDadosRegente            = db_utils::fieldsMemory($rsDadosRegente, 0);
      $oRetorno->codigo_regente =  $oDadosRegente->z01_numcgm;
      $oRetorno->nome_regente   =  $oDadosRegente->z01_nome;
    }
    if (isset($_SESSION["DIAS_LETIVOS_ESCOLA"])) {
       unset($_SESSION["DIAS_LETIVOS_ESCOLA"]);
     }
     $sWhere  = " ed57_i_escola = ".db_getsession("DB_coddepto");
     $sWhere .= " and (rh01_numcgm = {$oDadosRegente->z01_numcgm} or ed285_i_cgm = {$oDadosRegente->z01_numcgm})";
     $sWhere .= " and ed52_i_ano = ".db_getsession("DB_anousu");

     $oDaoRegenciaHorario = db_utils::getDao("regenciahorario");
     $sCampos             = "distinct ed232_i_codigo as codigo_disciplina, ed232_c_descr as descricao_disciplina";
     $sSqlDisciplinasRegente = $oDaoRegenciaHorario->sql_query_diario_classe_periodo(null,
                                                                                     $sCampos,
                                                                                     "ed232_c_descr",
                                                                                      $sWhere
                                                                                   );
     $rsDisciplinasRegente = $oDaoRegenciaHorario->sql_record($sSqlDisciplinasRegente);
     $aDisciplinas = array();
     for ($iRowDisciplina = 0; $iRowDisciplina< $oDaoRegenciaHorario->numrows; $iRowDisciplina++) {

       $oDadoDisciplina = db_utils::fieldsMemory($rsDisciplinasRegente, $iRowDisciplina);
       $oDadoDisciplina->descricao_disciplina = utf8_encode($oDadoDisciplina->descricao_disciplina);
       $aDisciplinas[] = $oDadoDisciplina;
     }


     $oRetorno->itens      = $aDisciplinas;
     break;

  case 'getDatasProfessorDisciplina':

    if (isset($_SESSION["DIAS_LETIVOS_ESCOLA"])) {
      unset($_SESSION["DIAS_LETIVOS_ESCOLA"]);
    }
    $aDiasSemana = array (0 => "DOMINGO",
                          1 => "SEGUNDA",
                          2 => "TERÇA",
                          3 => "QUARTA",
                          4 => "QUINTA",
                          5 => "SEXTA",
                          6 => "SÁBADO"
                         );
    $aDiasLetivos = array();
    $sWhere  = " ed57_i_escola    = ".db_getsession("DB_coddepto");
    $sWhere .= " and (rh01_numcgm = {$oParam->iRegente} or ed285_i_cgm = {$oParam->iRegente})";
    $sWhere .= " and ed52_i_ano   = ".db_getsession("DB_anousu");
    $sWhere .= " and ed232_i_codigo = {$oParam->iCodigoDisciplina}";


    $oDaoRegenciaHorario     = db_utils::getDao("regenciahorario");
    $sCampos                 = "distinct ed58_i_diasemana, ed57_i_codigo as codigo_turma, ed57_c_descr as descricao_turma";
    $sSqlDiasDaSemanaComAula = $oDaoRegenciaHorario->sql_query_diario_classe_periodo(null,
                                                                                    $sCampos,
                                                                                    null,
                                                                                     $sWhere);
    $rsDiasDaSemanaComAula     = $oDaoRegenciaHorario->sql_record($sSqlDiasDaSemanaComAula);
    $aDiasDeAula               = array();
    $iTotalDiasDaSemanaComAula = $oDaoRegenciaHorario->numrows;
    for ($iAula = 0; $iAula < $iTotalDiasDaSemanaComAula; $iAula++) {

      /**
       * Diminuimos 1 do dia da semana, pois o dia do semana para o php, inicia em 0 para domingo,
       * e termina em 6 (sabado.), na tabela diasemana, o inicio 1 para domingo, e 7 para Sabado;
       */
      $oDadosDiaSemana = db_utils::fieldsMemory($rsDiasDaSemanaComAula, $iAula);
      $iDiaNaSemana    = $oDadosDiaSemana->ed58_i_diasemana -1;
      if (!isset($aDiasDeAula[$iDiaNaSemana])) {
        $aDiasDeAula[$iDiaNaSemana] = new stdClass();
      }

      $oTurma = new stdClass();
      $oTurma->codigo_turma       = $oDadosDiaSemana->codigo_turma;
      $oTurma->descricao_turma    = utf8_encode($oDadosDiaSemana->descricao_turma);
      $aDiasDeAula[$iDiaNaSemana]->turmas[] = $oTurma;
    }

    /**
     * Carregamos as datas do calendario.
     */
    $sCampos             = "min(ed52_d_inicio) as inicio, max(ed52_d_fim) as maximo, ";
    $sCampos            .= "array_to_string(array_accum(ed52_i_codigo), ', ') as calendarios,";
    $sCampos            .= "max(ed52_d_fim) - min(ed52_d_inicio) as numero_dias_aula";
    $sSqlDatasCalendario = $oDaoRegenciaHorario->sql_query_diario_classe_periodo(null,
                                                                                 $sCampos,
                                                                                 null,
                                                                                 $sWhere
                                                                                 );

    $rsDatasCalendario   = $oDaoRegenciaHorario->sql_record($sSqlDatasCalendario);
    try {

      if ($oDaoRegenciaHorario->numrows == 0) {
        throw new Exception("Periodo sem calendario informado.\n{$oDaoRegenciaHorario->erro_msg}");
      }
      $oDadosAnoLetivo = db_utils::fieldsMemory($rsDatasCalendario, 0);

      /**
       * Criamos uma collection de com os feriados cadastrados para o(s) Calendarios que o Professor esta vinculado.
       */
      $oDaoFeriado = db_utils::getDao("feriado");
      $sSqlFeriado = $oDaoFeriado->sql_query_file(null,
                                                  "ed54_d_data as data, ed54_c_dialetivo as dia_letivo",
                                                  "ed54_d_data",
                                                  "ed54_i_calendario in({$oDadosAnoLetivo->calendarios})"
                                                 );

      $rsFeriado  = $oDaoFeriado->sql_record($sSqlFeriado);
      $aFeriados  = db_utils::getCollectionByRecord($rsFeriado);

      list($iAnoInicial, $iMesInicial, $iDiaInicial) = explode("-", $oDadosAnoLetivo->inicio);
      for ($iDia = 0; $iDia <= $oDadosAnoLetivo->numero_dias_aula; $iDia++) {

        $sDataDiaTimeStamp = mktime(0, 0, 0, $iMesInicial, $iDiaInicial+$iDia, $iAnoInicial);
        $sDataFormatada    = date("Y-m-d", $sDataDiaTimeStamp);
        $iDiaSemana        = date("w" , $sDataDiaTimeStamp);

        /**
         * Carregamos apenas as datas iguais  ou anteriores ao dia da sessÃ£o.
         */
        if (db_strtotime($sDataFormatada) > db_strtotime(date("Y-m-d", db_getsession("DB_datausu")))) {
          continue;
        }

        /**
         * caso nao seja um dia de aula (dia da semana) do professor, passamos para o proximo dia
         */
        if (!array_key_exists($iDiaSemana, $aDiasDeAula)) {
          continue;
        }

        $oDiaLetivo        = new stdClass();
        /**
         * verificamos o dia Ã© um feriado. caso seja passamos para o proximo dia.
         */
        if ($iFeriado = db_stdClass::inCollection("data", $sDataFormatada, $aFeriados)) {

          $oFeriado = $aFeriados[$iFeriado];
          if ($oFeriado->dia_letivo == 'N') {
             continue;
          }
        }

        /**
         * Criamos a Estrutura com os Dados do dia Letivo
         */
        $oDiaLetivo            = new stdClass();
        $oDiaLetivo->data      = $sDataFormatada;
        $oDiaLetivo->diasemana = utf8_encode($aDiasSemana[$iDiaSemana]);
        $oDiaLetivo->turmas    = $aDiasDeAula[$iDiaSemana]->turmas;
        $aDiasLetivos[]        = $oDiaLetivo;
      }

      $_SESSION["DIAS_LETIVOS_ESCOLA"] = $aDiasLetivos;
      $oRetorno->aDiasLetivos          = $aDiasLetivos;
      $oRetorno->dataatual             = date("Y-m-d", db_getsession("DB_datausu"));
    } catch (Exception $eErro) {

      $oRetorno->status  = 2;
      $oRetorno->message = urlencode($eErro->getMessage());
    }
    break;


  case 'getTurmasNoDia' :

    $aTurmas = array();
    if (isset($oParam->dtAula) && isset($oParam->dtAula)) {

      if (isset($_SESSION["DIAS_LETIVOS_ESCOLA"])) {

        foreach ($_SESSION["DIAS_LETIVOS_ESCOLA"] as $oData) {

          if ($oData->data == $oParam->dtAula) {
            $aTurmas = $oData->turmas;
            break;
          }
        }
      }
    }
    $oRetorno->aTurmas = $aTurmas;
    break;


  case 'getAlunosTurma' :

    $oDaoMatricula                   = db_utils::getDao("matricula");
    $oDaoRegenciaHorario             = db_utils::getDao("regenciahorario");
    $oDaoDiarioClasseAlunoFalta      = db_utils::getDao("diarioclassealunofalta");
    $oDaoDiarioClasseRegenciaHorario = db_utils::getDao("diarioclasseregenciahorario");
    $oDaoControleAcessoAluno         = db_utils::getDao("controleacessoalunoregistro");
    $oDaoControleAcesso              = db_utils::getDao("controleacessoaluno");

    $sListaCampos  = " aluno.ed47_v_nome as  nome, ";
    $sListaCampos .= " aluno.ed47_i_codigo as codigo, ";
    $sListaCampos .= " matricula.ed60_i_numaluno as ordem_turma, ";
    $sListaCampos .= " matricula.ed60_c_situacao as situacao, ";
    $sListaCampos .= " matricula.ed60_i_codigo as codigo_matricula ";
    $sSqlAlunosMatriculadosNaTurma  = $oDaoMatricula->sql_query("",
                                                             $sListaCampos,
                                                             "matricula.ed60_i_numaluno, to_ascii(ed47_v_nome)",
                                                             "ed60_i_turma = {$oParam->iCodigoTurma}"
                                                            );


    $aPeriodosDeAulaDoDia        = array();
    $iDiaDaSemana                = date('w', db_strtotime($oParam->dtAula)) + 1;

    $sWhere  = " ed57_i_escola        = ".db_getsession("DB_coddepto");
    $sWhere .= " and (rh01_numcgm     = {$oParam->iRegente} or ed285_i_cgm = {$oParam->iRegente})";
    $sWhere .= " and ed52_i_ano       = ".db_getsession("DB_anousu");
    $sWhere .= " and ed232_i_codigo   = {$oParam->iCodigoDisciplina}";
    $sWhere .= " and ed58_i_diasemana = {$iDiaDaSemana}";
    $sWhere .= " and ed59_i_turma     = {$oParam->iCodigoTurma}";
    /**
     * Carregamos os periodos do dia.
     *
     */
    $sWhereHorarioAula  = " ed57_i_escola  = ".db_getsession("DB_coddepto");
    $sWhereHorarioAula .= " and ed57_i_codigo  = {$oParam->iCodigoTurma}";

    $sSqlHorarioAula = $oDaoControleAcesso->sql_query_horario_aula_turma(null,
                                                                         "min(ed17_h_inicio) as horainicio, 
                                                                          max(ed17_h_fim) as horatermino",
                                                                         null,
                                                                         $sWhereHorarioAula
                                                                        );
    $rsHorarioAula    = $oDaoControleAcesso->sql_record($sSqlHorarioAula);
    if ($oDaoControleAcesso->numrows == 0) {
      throw new Exception("Turma sem períodos de aula cadastrados.");
    }
    $sHoraInicioAula   = db_utils::fieldsMemory($rsHorarioAula, 0)->horainicio;
    $sHoraTerminoAula  = db_utils::fieldsMemory($rsHorarioAula, 0)->horatermino;
    $sCampos           = " distinct ed58_i_codigo as sequencial, ed08_i_sequencia,";
    $sCampos          .= " ed08_c_descr     as descricao_periodo, ";
    $sCampos          .= " ed58_i_codigo    as codigo_regencia_periodo ";
    $sSqlPeriodosAula = $oDaoRegenciaHorario->sql_query_diario_classe_periodo(null,
                                                                              $sCampos,
                                                                              'ed08_i_sequencia',
                                                                              $sWhere
                                                                             );
    $rsPeriodosAula              = $oDaoRegenciaHorario->sql_record($sSqlPeriodosAula);
    $oRetorno->aPeriodosAulaDia  = db_utils::getCollectionByRecord($rsPeriodosAula, false, false, $lEncode);
    $aPeriodosAula               = array();
    foreach ($oRetorno->aPeriodosAulaDia as $oPeriodo) {
      $aPeriodosAula[] = $oPeriodo->codigo_regencia_periodo;
    }
    $_SESSION["PERIODOS_AULA_DIA"] = $oRetorno->aPeriodosAulaDia;

    /**
     * Pegamos os dados de aula do aluno
     */
    $rsAlunosMatriculadosNaTurma = $oDaoMatricula->sql_record($sSqlAlunosMatriculadosNaTurma);
    $aAlunosMatriculados         = array();
    for ($iAluno = 0; $iAluno < $oDaoMatricula->numrows; $iAluno++) {

      $oAluno                = db_utils::fieldsMemory($rsAlunosMatriculadosNaTurma, $iAluno, false, false, $lEncode);

      $oAluno->acessoescola = ControleAcessoAluno::alunoEstaNaEscola($oAluno->codigo,
                                                                     $oParam->dtAula,
                                                                     $sHoraInicioAula,
                                                                     $sHoraTerminoAula
                                                                    );

      /**
       * Verificamos se existe falta para o Aluno em algum periodo no dia
       */
      $sWhereFaltas = "ed302_regenciahorario in (".implode(",", $aPeriodosAula).") ";
      $sWhereFaltas .= " and ed300_datalancamento = '{$oParam->dtAula}' ";
      $sWhereFaltas .= " and ed301_aluno          = {$oAluno->codigo} ";
      $sSqlAlunoFaltas = $oDaoDiarioClasseAlunoFalta->sql_query_aluno_falta(null,
                                                                             "ed302_regenciahorario as periodo",
                                                                              null,
                                                                              $sWhereFaltas
                                                                             );
      $aFaltas               = array();
      $rsAlunoFaltas         = $oDaoDiarioClasseAlunoFalta->sql_record($sSqlAlunoFaltas);
      $aAlunoFaltas          = db_utils::getCollectionByRecord($rsAlunoFaltas);
      foreach ($aAlunoFaltas as $oFalta) {
        $aFaltas[] = $oFalta->periodo;
      }
      unset($aAlunoFaltas);
      $oAluno->faltas        = $aFaltas;
      $aAlunosMatriculados[] = $oAluno;
    }
    $_SESSION['ALUNOS'] = $aAlunosMatriculados;
    $oRetorno->aAlunos   = $aAlunosMatriculados;
    $oRetorno->sAulaData = '';
    /**
     * Retornamos os dados do diario de classe
     */
    $sWhereDiarioClasse  = "ed302_regenciahorario in (".implode(",", $aPeriodosAula).") ";
    $sWhereDiarioClasse .= " and ed300_datalancamento = '{$oParam->dtAula}' ";
    $sSqlDiarioClasse    = $oDaoDiarioClasseRegenciaHorario->sql_query(null,
                                                                       "distinct ed300_auladesenvolvida",
                                                                        null,
                                                                        $sWhereDiarioClasse
                                                                      );
    $rsDiarioClasse      = $oDaoDiarioClasseRegenciaHorario->sql_record($sSqlDiarioClasse);
    if ($oDaoDiarioClasseRegenciaHorario->numrows > 0) {
      $oRetorno->sAulaData = urlencode(db_utils::fieldsMemory($rsDiarioClasse, 0)->ed300_auladesenvolvida);
    }

    break;


  /**
   * Salva a falta por aluno, Ã© utilizado no modelo mobile
   */
  case "salvarFaltaPorAluno":

    db_inicio_transacao();
    try {

      $oDaoDiarioClasseAlunoFalta      = db_utils::getDao("diarioclassealunofalta");
      $oDaoDiarioClasse                = db_utils::getDao("diarioclasse");
      $oDaoDiarioClasseRegenciaHorario = db_utils::getDao("diarioclasseregenciahorario");

      $lAlterarProximoPeriodo = false;
      $aPeriodosAula          = $_SESSION['PERIODOS_AULA_DIA'];
      $aPeriodosFaltaPresenca = array();
      if (isset($aPeriodosAula) && count($aPeriodosAula) > 0) {

        foreach ($aPeriodosAula as $oPeriodo) {

          if ($oPeriodo->codigo_regencia_periodo == $oParam->iCodigoRegenciaPeriodo) {
            $lAlterarProximoPeriodo   = true;
          }
          if ($lAlterarProximoPeriodo) {
            $aPeriodosFaltaPresenca[] = $oPeriodo->codigo_regencia_periodo;
          }
        }
      }

      $sRegenciasPeriodo = implode(",", $aPeriodosFaltaPresenca);
      $sWhere            = "    ed300_datalancamento = '{$oParam->dtAula}' ";
      $sWhere           .= "and ed301_aluno = {$oParam->iCodigoAluno} ";
      $sWhere           .= "and ed302_regenciahorario in({$sRegenciasPeriodo})";
      $sSqlDiarioClasse  = $oDaoDiarioClasse->sql_query_faltas(null,
                                                               "distinct ed300_sequencial as diario_classe,
                                                               ed302_sequencial,
                                                               ed302_regenciahorario as regencia_horario",
                                                               null,
                                                               $sWhere);
      $rsDiarioClasse         = $oDaoDiarioClasse->sql_record($sSqlDiarioClasse);
      $lIsFalta               = true;
      $lExcluiuDiarioClasse   = false;
      $iCodigoDiarioClasse    = null;

      /**
       * Tratamento para as faltas existentes para o aluno
       */
      if ($oDaoDiarioClasse->numrows > 0) {

        for ($iRowDiario = 0; $iRowDiario < $oDaoDiarioClasse->numrows; $iRowDiario++) {

          $oStdDiarioClasse         = db_utils::fieldsMemory($rsDiarioClasse, $iRowDiario);
          $iCodigoDiarioClasse      = $oStdDiarioClasse->diario_classe;
          $sWhereExcluirAlunoFalta  = "    ed301_diarioclasseregenciahorario = {$oStdDiarioClasse->ed302_sequencial} ";
          $sWhereExcluirAlunoFalta .= "and ed301_aluno = {$oParam->iCodigoAluno} ";
          $oDaoDiarioClasseAlunoFalta->excluir(null, $sWhereExcluirAlunoFalta);
          if ($oDaoDiarioClasseAlunoFalta->erro_status == "0") {
            throw new Exception("Não foi possível excluir o vínculo da falta do aluno com o período.");
          }

          if ($oDaoDiarioClasseAlunoFalta->numrows_excluir > 0 && $oStdDiarioClasse->regencia_horario == $oParam->iCodigoRegenciaPeriodo) {
            $lIsFalta = false;
          }

          $oDaoDiarioClasseRegenciaHorario->excluir($oStdDiarioClasse->ed302_sequencial);
          if ($oDaoDiarioClasseRegenciaHorario->erro_status == "0") {
            throw new Exception("Não foi possível excluir o vínculo da regencia com o diário de classe.");
          }
        }

        $sWhereDiarioVinculado    = "ed302_diarioclasse = {$iCodigoDiarioClasse}";
        $sSqlBuscaDiarioVinculado = $oDaoDiarioClasseRegenciaHorario->sql_query_file(null, "*", null, $sWhereDiarioVinculado);
        $rsBuscaDiarioVinculado   = db_query($sSqlBuscaDiarioVinculado);
        if ( !$rsBuscaDiarioVinculado) {
          throw new Exception("Erro ao buscar diário de classe existente.");
        }

        if (pg_num_rows($rsBuscaDiarioVinculado) == 0) {

          $oDaoDiarioClasse = db_utils::getDao('diarioclasse');
          $oDaoDiarioClasse->excluir($iCodigoDiarioClasse);
          $lExcluiuDiarioClasse = true;
          if ($oDaoDiarioClasse->erro_status == "0") {
            throw new Exception("Erro ao excluir o diário de classe.");
          }
          unset($oDaoDiarioClasse);
        }
      }

      if ($lExcluiuDiarioClasse || $oDaoDiarioClasse->numrows == 0) {

        $oDaoDiarioClasse = db_utils::getDao('diarioclasse');
        $oDaoDiarioClasse->ed300_datalancamento   = $oParam->dtAula;
        $oDaoDiarioClasse->ed300_auladesenvolvida = '';
        $oDaoDiarioClasse->ed300_hora             = db_hora();
        $oDaoDiarioClasse->ed300_id_usuario       = db_getsession("DB_id_usuario");
        $oDaoDiarioClasse->incluir(null);
        if ($oDaoDiarioClasse->erro_status == "0") {
          throw new Exception("Não foi possível incluir um diário de classe.");
        }
        $iCodigoDiarioClasse = $oDaoDiarioClasse->ed300_sequencial;
      }

      if ($lIsFalta) {

        foreach ($aPeriodosFaltaPresenca as $iCodigoRegenciaPeriodo) {

          $oDaoDiarioClasseRegenciaHorario->ed302_diarioclasse    = $iCodigoDiarioClasse;
          $oDaoDiarioClasseRegenciaHorario->ed302_regenciahorario = $iCodigoRegenciaPeriodo;
          $oDaoDiarioClasseRegenciaHorario->incluir(null);
          if ($oDaoDiarioClasseRegenciaHorario->erro_status == 0) {
            throw new Exception("Erro ao vincular a regencia com o diário de classe.");
          }

          $oDaoDiarioClasseAlunoFalta->ed301_aluno                       = $oParam->iCodigoAluno;
          $oDaoDiarioClasseAlunoFalta->ed301_diarioclasseregenciahorario = $oDaoDiarioClasseRegenciaHorario->ed302_sequencial;
          $oDaoDiarioClasseAlunoFalta->incluir(null);
          if ($oDaoDiarioClasseAlunoFalta->erro_status == 0) {
            throw new Exception("Erro ao vincular a regência com a falta do aluno.");
          }
        }
      }


      $oRetorno->aPeriodosSeguintes = $aPeriodosFaltaPresenca;
      $oRetorno->lIsFalta           = $lIsFalta;

      /**
       * Buscamos a turma pelo codigo das Regencias;
       */
      $oDaoRegenciaHorario             = db_utils::getDao("regenciahorario");
      $sWhereTurma = "ed58_i_codigo in({$oParam->iCodigoRegenciaPeriodo})";
      $sSqlTurma   = $oDaoRegenciaHorario->sql_query(null, "distinct ed57_i_codigo, ed58_i_regencia, ed59_i_serie",
                    null,
                    $sWhereTurma);
      $rsTurma     = $oDaoRegenciaHorario->sql_record($sSqlTurma);
      if ($oDaoRegenciaHorario->numrows != 1) {
        throw new Exception("Turma com regencias configuradas incorretamente!");
      }
      $oDadosRegencia    = db_utils::fieldsMemory($rsTurma, 0);
      $iCodigoDaTurma    = $oDadosRegencia->ed57_i_codigo;
      $oRegencia         = new Regencia($oDadosRegencia->ed58_i_regencia);
      $oTurma            = new Turma($iCodigoDaTurma);
      $oEtapaOrigem      = new Etapa($oDadosRegencia->ed59_i_serie);
      $aPeriodoAvalicao  = $oTurma->getCalendario()->getPeriodoPorData(new DBDate($oParam->dtAula));
      if (count($aPeriodoAvalicao) > 1 || count($aPeriodoAvalicao) == 0) {
        throw new Exception("Existem periodos com datas inconsistentes para o Calendario {$oTurma->getCalendario()->getDescricao()}!");
      }

      /**
       * Atualizamos o total de faltas no periodo
       */
      $oAluno                 = AlunoRepository::getAlunoByCodigo($oParam->iCodigoAluno);
      $oMatricula            = $oAluno->getMatriculaByTurma($oTurma);
      if (!empty($oMatricula)) {

        $oPeriodoAvaliacaoNoDia = $aPeriodoAvalicao[0];

        $oDiarioClasse  = $oMatricula->getDiarioDeClasse();
        $oDisciplina    = $oDiarioClasse->getDisciplinasPorRegencia($oRegencia);
        $iTotalDeFaltas = $oDisciplina->getTotalDeFaltasPorPeriodoDeAula($oPeriodoAvaliacaoNoDia->getPeriodoAvaliacao());
        foreach ($oDisciplina->getAvaliacoes() as $oAvaliacao) {

          if (!$oAvaliacao->getElementoAvaliacao()->isResultado()) {

            $oPeriodoAvaliacao = $oAvaliacao->getElementoAvaliacao()->getPeriodoAvaliacao();
            if ($oPeriodoAvaliacao->getCodigo() == $oPeriodoAvaliacaoNoDia->getPeriodoAvaliacao()->getCodigo()) {

              $oAvaliacao->setNumeroFaltas($iTotalDeFaltas);
              $oDisciplina->salvar();
              unset($oAvaliacao);
              unset($oPeriodoAvaliacao);
              break;
            }
          }
          unset($oDisciplina);
          unset($oDiarioClasse);
          unset($oMatricula);
        }
      }

      db_fim_transacao(false);

    } catch (Exception $eErro) {

      db_fim_transacao(true);
      $oRetorno->status  = 2;
      $oRetorno->message = utf8_encode($eErro->getMessage());
    }
  break;


  case 'verificaPresencaAluno':

    $aSessionAlunos = $_SESSION['ALUNOS'];
    $aPeriodosFalta = array();
    $iCodigoAluno   = 0;
    foreach ($aSessionAlunos as $iIndice => $oAluno) {

      if ($oAluno->codigo_matricula == $oParam->iCodigoMatricula) {
        $aPeriodosFalta = $oAluno->faltas;
        $iCodigoAluno   = $oAluno->codigo;
      }
    }

    $aInputsRetorno = array();
    $aPeriodosAula = $_SESSION["PERIODOS_AULA_DIA"];
    foreach ($aPeriodosAula as $iIndice => $oPeriodo) {

      $sChecked = "";
      if (!in_array($oPeriodo->codigo_regencia_periodo, $aPeriodosFalta)) {
        $sChecked = "checked";
      }
      $sDescricaoCheckBox = "{$oPeriodo->descricao_periodo}";
      $sNameCheckBox      = "checkbox-{$oPeriodo->sequencial}";

      $aInputsRetorno[] = "<input type='checkbox' name='{$sNameCheckBox}' id='{$sNameCheckBox}' class='custom' onclick='js_falta({$oPeriodo->sequencial}, {$oPeriodo->codigo_regencia_periodo});' {$sChecked}/><label for='{$sNameCheckBox}'>".utf8_encode($sDescricaoCheckBox)."</label>";
    }


    $oRetorno->iCodigoAluno = $iCodigoAluno;
    $oRetorno->inputs = $aInputsRetorno;
    break;
}
echo $oJson->encode($oRetorno);