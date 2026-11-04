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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("std/DBDate.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("model/educacao/avaliacao/iFormaObtencao.interface.php"));
require_once(modification("model/educacao/avaliacao/iElementoAvaliacao.interface.php"));

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
  db_app::load("scripts.js, prototype.js, strings.js, arrays.js, dbcomboBox.widget.js, datagrid.widget.js");
  db_app::load("estilos.css, grid.style.css");
  db_app::load("widgets/Input/DBInput.widget.js");
  db_app::load("widgets/Input/DBInputDate.widget.js");
  db_app::load("AjaxRequest.js");
  db_app::load("dates.js");
  db_app::load("widgets/windowAux.widget.js");
  db_app::load("dbmessageBoard.widget.js");
  db_app::load("classes/educacao/escola/DBViewRemocaoPeriodoGradeHorario.classe.js");

?>

    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        .btnArrow {
            width: 60px;
            height: 30px;
        }
    </style>
</head>

<?php

db_postmemory( $_POST );

$clregenciahorario      = new cl_regenciahorario;
$clregencia             = new cl_regencia;
$clregenteconselho      = new cl_regenteconselho;
$cldiasemana            = new cl_diasemana;
$clperiodoescola        = new cl_periodoescola;
$clescola               = new cl_escola;
$clturmaturnoadicional  = new cl_turmaturnoadicional;
$clturma                = new cl_turma;
$clrechumanoativ        = new cl_rechumanoativ;

$db_opcao = 1;
$db_botao = true;
$escola   = db_getsession("DB_coddepto");
$erro     = false;

$sCampos        = "ed57_i_sala,ed52_i_codigo as codcalendario,ed52_i_ano as anocal";
$sSqlDadosTurma = $clturma->sql_query( "", $sCampos, "", " ed57_i_codigo = $ed59_i_turma" );
$result_cal  = $clturma->sql_record($sSqlDadosTurma);

db_fieldsmemory($result_cal,0);
$sWhere      = " ed57_i_sala = $ed57_i_sala AND ed57_i_turno = $ed57_i_turno ";
$sWhere     .= " AND ed52_i_ano = $anocal AND ed57_i_codigo != '$ed59_i_turma'";
$sSqlTurma   = $clturma->sql_query( "", "ed57_i_codigo as codturmaadd", "ed57_i_codigo", $sWhere );

$result_sala = $clturma->sql_record($sSqlTurma);
$maisturmas  = "";
$sep         = "";
for ($r = 0; $r < $clturma->numrows; $r++) {

  db_fieldsmemory($result_sala,$r);
  $maisturmas .= $sep.$codturmaadd;
  $sep         = ",";
}

if (isset($incluir)) {
  $db_botao = true;

  try {
    db_inicio_transacao();

    $oTurma        = new Turma($ed59_i_turma);
    $oEtapa        = new Etapa($ed59_i_serie);
    $oGradeHorario = new GradeHorario($oTurma, $oEtapa);
    $oGradeHorario->setTipoGrade(PeriodoAula::GRADE_HORARIO);

    for ($x = 0; $x < $contp; $x++) {
      for ($y = 0; $y < $contd; $y++) {
        $valores  = "valorQ".$x.$y;
        $valores  = $$valores;
        $marcados = "marcadoQ".$x.$y;
        $marcados = $$marcados;

        if (trim($valores) != "" && trim($marcados) == "") {
          $dados        = explode("|",$valores);
          $oPeriodoAula = new PeriodoAula();

          $oPeriodoAula->setRegencia( RegenciaRepository::getRegenciaByCodigo($dados[0]) );
          $oPeriodoAula->setDiaSemana( $dados[1] - 1 );
          $oPeriodoAula->setPeriodoEscola( PeriodoEscolaRepository::getByCodigo($dados[2]) );
          $oPeriodoAula->setRegente($dados[3]);
          $oPeriodoAula->setDataInicio(new DBDate($ed58_datainicio));
          $oPeriodoAula->setDataFim(new DBDate($ed58_datafim));
          $oPeriodoAula->setAtivo(true);
          $oPeriodoAula->setTipoVinculo(PeriodoAula::GRADE_HORARIO);

          $oGradeHorario->adicionarPeriodo($oPeriodoAula);
        }
      }
    }

    $oGradeHorario->salvar();

    $result = $clregenteconselho->sql_record($clregenteconselho->sql_query("",
                                                                           "ed235_i_codigo",
                                                                           "",
                                                                           " ed235_i_turma = $ed59_i_turma"
                                                                          )
                                            );
    if (isset($conselheiro) && trim($conselheiro) == "") {

      if ($clregenteconselho->numrows > 0) {

        $clregenteconselho->excluir(""," ed235_i_turma = $ed59_i_turma");
        if ($clregenteconselho->erro_status == 0) {

          $sMensagemErro   = "Erro ao Excluir dados do conselheiro da turma.\\n ";
          $sMensagemErro  .= "Erro Técnico : {$clregenteconselho->erro_msg}";
          throw new BusinessException($sMensagemErro);
        }
      }

    } else if (isset($conselheiro) && trim($conselheiro) != "") {

      if ($clregenteconselho->numrows > 0) {

        db_fieldsmemory($result,0);
        $clregenteconselho->ed235_i_rechumano = $conselheiro;
        $clregenteconselho->ed235_i_codigo    = $ed235_i_codigo;
        $clregenteconselho->alterar($ed235_i_codigo);
      } else {

        $clregenteconselho->ed235_i_turma     = $ed59_i_turma;
        $clregenteconselho->ed235_i_rechumano = $conselheiro;
        $clregenteconselho->incluir(null);
      }

      if ($clregenteconselho->erro_status == 0) {

        $sMensagemErro   = "Erro ao salvar dados do conselheiro da turma.\\n ";
        $sMensagemErro  .= "Erro Técnico : {$clregenteconselho->erro_msg}";
        throw new BusinessException($sMensagemErro);
      }
    }

    db_fim_transacao(false);
    $clregenciahorario->erro_msg = "Dados salvos com sucesso!";
    $clregenciahorario->erro(true,false);
    $redireciona  = "edu1_regenciahorario001.php?ed59_i_turma=$ed59_i_turma&ed57_c_descr=$ed57_c_descr";
    $redireciona .= "&ed57_i_turno=$ed57_i_turno&ed59_i_serie=$ed59_i_serie&ed11_c_descr=$ed11_c_descr";
    db_redireciona($redireciona);
    exit;
  } catch (Exception $eBusinessException) {


    db_fim_transacao(true);
    db_msgbox($eBusinessException->getMessage());
  }
}


/**
 * Verificamos o tipo de frequencia e a forma de controle de frequencia da base curricular da turma
 */
$lControleIndividualPeriodo = false;
$sDesabilitaVinculo         = "";

$oTurma              = TurmaRepository::getTurmaByCodigo($ed59_i_turma);
$oBaseCurricular     = $oTurma->getBaseCurricular();

if ($oBaseCurricular->getControleFrequencia() == 'I' && $oBaseCurricular->getFrequencia() == 'P') {

  $lControleIndividualPeriodo = true;
  $sDesabilitaVinculo         = "disabled";
}

/**
 * Verificamos se a grade foi preenchida ou algum vinculo entre regente/disciplina, retornando o tipo de vinculo
 */
if (!isset($iTipoVinculo)) {

  $iTipoVinculo           = null;
  $oDaoRegenciaHorario    = new \cl_regenciahorario();
  $sCamposRegenciaHorario = "distinct ed58_i_regencia, ed58_tipovinculo";
  $sWhereRegenciaHorario  = "ed59_i_turma = {$ed59_i_turma} AND ed57_i_escola = {$escola}";
  $sSqlRegenciaHorario    = $oDaoRegenciaHorario->sql_query(null, $sCamposRegenciaHorario, null, $sWhereRegenciaHorario);
  $rsRegenciaHorario      = $oDaoRegenciaHorario->sql_record($sSqlRegenciaHorario);

  if ($oDaoRegenciaHorario->numrows > 0) {

    $oDadosRegenciaHorario = db_utils::fieldsMemory($rsRegenciaHorario, 0);
    $iTipoVinculo          = $oDadosRegenciaHorario->ed58_tipovinculo;
  }
}

$sSelectGrade   = "";
$sSelectVinculo = "";

if (empty($iTipoVinculo) || $iTipoVinculo == 2 || $lControleIndividualPeriodo) {
  $sSelectGrade = "selected";
} else {
  $sSelectVinculo = "selected";
}

?>
<body class="body-default">
<script type="text/javascript">
    var oGet = js_urlToObject();
</script>
 <fieldset style="width:95%">
   <legend>
     <select id="escolha" name="escolha" style="font-weight:bold;font-size:11px;"
             onchange="js_validaRegente();">
       <option value="gradeHorario"
               <?=$sSelectGrade?>>
         <b>Horários de Regências na Turma <?=@$ed57_c_descr?> - Etapa <?=@$ed11_c_descr?></b>
       </option>
       <option value="vinculaRegente"
               <?=$sSelectVinculo?>
               <?=$sDesabilitaVinculo?>>
         <b>Vínculos Regente / Disciplina na Turma <?=@$ed57_c_descr?> - Etapa <?=@$ed11_c_descr?></b>
       </option>
     </select>
   </legend>
   <?php
     if (!isset($excluir)) {

       /**
        * De acordo com o tipo de vinculo, carregamos o formulario correto
        */
       if (empty($iTipoVinculo) || $iTipoVinculo == 2 || $lControleIndividualPeriodo) {

         include(modification("forms/db_frmregenciahorario.php"));
         ?>
         <script>
           $('escolha').style.display         = "gradeHorario";
           $('frmGradeHorario').style.display = "inline";
         </script>
         <?php
       } else {

         include(modification("edu1_vinculaprofessordisciplina001.php"));
         ?>
         <script>
           $('escolha').style.display                       = "vinculaRegente";
           $('frmVinculaProfessorDisciplina').style.display = "inline";
           $('divVinculos').style.display                   = "";
         </script>
         <?php
       }
     }
   ?>
 </fieldset>
</body>
</html>
<script type="text/javascript">
    var oGet = js_urlToObject();
var sUrlRpc         = 'edu4_regente.RPC.php';
var sVinculoInicial = $('escolha').value;
var sTurma          = oGet.ed57_c_descr;
var sEtapa          = oGet.ed11_c_descr;

function js_escolha(sValor) {
    if (sValor == "gradeHorario") {
        location.href = 'edu1_regenciahorario001.php?ed59_i_turma=' + oGet.ed59_i_turma
            + '&ed57_c_descr=' + sTurma
            + '&ed57_i_turno=' + oGet.ed57_i_turno
            + '&ed59_i_serie=' + oGet.ed59_i_serie
            + '&ed11_c_descr=' + sEtapa
            + '&iTipoVinculo=2';
    } else {
        location.href = 'edu1_regenciahorario001.php?ed59_i_turma=' + oGet.ed59_i_turma
            + '&ed57_c_descr=' + sTurma
            + '&ed57_i_turno=' + oGet.ed57_i_turno
            + '&ed59_i_serie=' + oGet.ed59_i_serie
            + '&ed11_c_descr=' + sEtapa
            + '&iTipoVinculo=1';
    }
}

/**
 * Validamos se algum dos docentes da turma possui ausencia, e se ja tem substituto cadastrado
 */
function js_validaRegente() {
    var oParametro = {};
    oParametro.exec = 'validarRegente';
    oParametro.iTurma = oGet.ed59_i_turma;
    oParametro.iEtapa = oGet.ed59_i_serie;
    new Ajax.Request(sUrlRpc,
        {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_retornaValidaRegente
        }
    );
}

function js_retornaValidaRegente(oResponse) {
    var oRetorno = JSON.parse(oResponse.responseText);

    if (oRetorno.lTemRegenteAusente) {
        var sMsg = "Existe regente com ausência e substituto cadastrado. Para poder remover os vínculos ";
        sMsg += "Regente/Disciplina existentes, é necessário primeiramente excluir os vínculos dos substitutos.";

        alert(sMsg);
        $('escolha').value = sVinculoInicial;
        return false;
    } else {
        validaTrocaVinculo();
    }
}

/**
 * Verificamos se eh possivel alterar o tipo de vinculo
 */
function validaTrocaVinculo() {
    var msg = 'Ao trocar a grade de horário o sitema irá excluir todos os vínculos de todos professores informados.';
    msg += 'Incluindo os inativos. E seus registros de lançamento de frequência e conteúdo.';

    if (!confirm(msg)) {
        $('escolha').value = sVinculoInicial;
        return;
    }

    js_excluiVinculos();
}

/**
 * Excluimos os vinculos existentes
 */
function js_excluiVinculos() {
    var oParametro = {};
    oParametro.exec = 'excluiVinculos';
    oParametro.iTurma = oGet.ed59_i_turma;
    oParametro.iEtapa = oGet.ed59_i_serie;

    new Ajax.Request(sUrlRpc,
        {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_retornoExcluiVinculos
        }
    );
}

function js_retornoExcluiVinculos(oResponse) {
    var oRetorno = JSON.parse(oResponse.responseText);

    if (oRetorno.status != 2) {
        alert('Vínculos removidos com sucesso.');
        js_escolha($('escolha').value);
    } else {

        alert(oRetorno.message.urlDecode());
        return false;
    }
}

</script>
