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
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$resultedu = eduparametros(db_getsession("DB_coddepto"));
$cltransfescolarede = new cl_transfescolarede;
$clmatricula = new cl_matricula;
$clmatriculamov = new cl_matriculamov;
$clalunocurso = new cl_alunocurso;
$clregencia = new cl_regencia;
$clturma = new cl_turma;
$cldiario = new cl_diario;
$cldiarioavaliacao = new cl_diarioavaliacao;
$cldiarioresultado = new cl_diarioresultado;
$cldiariofinal = new cl_diariofinal;
$clamparo = new cl_amparo;
$db_opcao = 1;
$db_botao = true;
$escola = db_getsession("DB_coddepto");

if (isset($chavepesquisa)) {
    $campos  = "transfescolarede.ed103_i_codigo, ";
    $campos .= "transfescolarede.ed103_i_matricula, ";
    $campos .= "aluno.ed47_i_codigo, ";
    $campos .= "aluno.ed47_v_nome, ";
    $campos .= "escola.ed18_i_codigo as codescolaorig, ";
    $campos .= "escola.ed18_c_nome as nomeescolaorig, ";
    $campos .= "escoladestino.ed18_i_codigo as codescoladest, ";
    $campos .= "escoladestino.ed18_c_nome as nomeescoladest, ";
    $campos .= "atestvaga.ed102_i_base as codbasedest, ";
    $campos .= "base.ed31_c_descr as nomebasedest, ";
    $campos .= "cursoedu.ed29_i_avalparcial, ";
    $campos .= "atestvaga.ed102_i_calendario as codcaldest, ";
    $campos .= "calendario.ed52_c_descr as nomecaldest, ";
    $campos .= "calendario.ed52_i_ano as anocaldest, ";
    $campos .= "atestvaga.ed102_i_serie as codseriedest, ";
    $campos .= "serie.ed11_c_descr||' - '||ensino.ed10_c_abrev as nomeseriedest, ";
    $campos .= "atestvaga.ed102_i_turno as codturnodest, ";
    $campos .= "atestvaga.ed102_i_codigo as codatestdest, ";
    $campos .= "turno.ed15_c_nome as nometurnodest";
    $result = $cltransfescolarede->sql_record($cltransfescolarede->sql_query("", $campos, "", " ed103_i_codigo = $chavepesquisa"));
    db_fieldsmemory($result, 0);
}
?>
  <html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/educacao/DBViewFormularioEducacao.classe.js"></script>
    <script type="text/javascript" src="scripts/classes/educacao/escola/TurmaTurnoReferente.classe.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td width="360" height="18">&nbsp;</td>
      <td width="263">&nbsp;</td>
      <td width="25">&nbsp;</td>
      <td width="140">&nbsp;</td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
          <?php
          MsgAviso(db_getsession("DB_coddepto"), "escola");
          ?>
        <br>
        <form name="form1" method="post" action="">
          <fieldset style="width:95%">
            <legend><b>Matricular Alunos Transferidos (REDE)</b></legend>
            <table border="0" width="100%">
              <tr>
                <td colspan="2">
                    <?php
                    db_input('ed103_i_codigo', 15, @$Ied103_i_codigo, true, 'hidden', 3, "");
                    db_ancora("<b>Aluno:</b>", "js_pesquisatransf();", $db_opcao);
                    db_input('ed47_i_codigo', 15, @$Ied47_i_codigo, true, 'text', 3, '');
                    db_input('ed47_v_nome', 50, @$Ied47_v_nome, true, 'text', 3, '');
                    ?>
                </td>
              </tr>
                <?php
                if (isset($chavepesquisa)) {
                  $campos  = "matricula.ed60_c_concluida, ";
                  $campos .= "serie.ed11_i_codigo as codserieorig, ";
                  $campos .= "serie.ed11_c_descr||' - '||ensino.ed10_c_abrev as nomeserieorig, ";
                  $campos .= "base.ed31_i_codigo as codbaseorig, ";
                  $campos .= "calendario.ed52_i_codigo as codcalorig, ";
                  $campos .= "calendario.ed52_i_ano as anocalorig, ";
                  $campos .= "cursoedu.ed29_i_codigo as codcursoorig, ";
                  $campos .= "turma.ed57_i_codigo as codturmaorig, ";
                  $campos .= "turma.ed57_c_descr as nometurmaorig, ";
                  $campos .= "matricula.ed60_i_aluno, ";
                  $campos .= "matricula.ed60_d_datamatricula, ";
                  $campos .= "ed60_d_datasaida as datasaidaorig";
                  $result = $clmatricula->sql_record($clmatricula->sql_query("", $campos, "", " ed60_i_codigo = {$ed103_i_matricula}"));

                  db_fieldsmemory($result, 0);

                  $datamatriculaorig = db_formatar($ed60_d_datamatricula, 'd');
                  $datasaidaorig = db_formatar($datasaidaorig, 'd');
                  $concluida = $ed60_c_concluida == "S" ? "CONCLUÍDA" : "NÃO CONCLUÍDA";

                  if ($ed60_c_concluida == "S") {
                      $sql1  = "SELECT ed56_i_base as codbaseorig ";
                      $sql1 .= "  FROM alunocurso ";
                      $sql1 .= " WHERE ed56_i_aluno = {$ed47_i_codigo}";
                      $result1 = db_query($sql1);
                      db_fieldsmemory($result1, 0);
                  }

                  $result1 = $clalunocurso->sql_record(
                      $clalunocurso->sql_query_file("", "ed56_i_codigo, ed56_c_situacao", "", " ed56_i_aluno = {$ed47_i_codigo}")
                  );
                  db_fieldsmemory($result1, 0);
                  ?>
                <tr>
                  <td>
                    <fieldset style="width:95%;">
                      <legend><b>Dados de Origem</b></legend>
                      <table>
                        <tr>
                          <td>
                            <b>Matrícula:</b>
                          </td>
                          <td>
                              <?php
                              db_input('ed103_i_matricula', 10, @$ed103_i_matricula, true, 'text', 3, '');
                              db_input('concluida', 20, @$concluida, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Data Matrícula:</b>
                          </td>
                          <td>
                              <?php
                              db_input('datamatriculaorig', 10, @$datamatriculaorig, true, 'text', 3, '');
                              ?>

                              <b>Data Saída:</b>

                              <?php
                              db_input('datasaidaorig', 10, @$datasaidaorig, true, 'text', 3, '');
                              db_input('ed60_c_concluida', 10, @$ed60_c_concluida, true, 'hidden', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Escola:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codescolaorig', 15, @$codescolaorig, true, 'hidden', 3, '');
                              db_input('nomeescolaorig', 40, @$nomeescolaorig, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Situação:</b>
                          </td>
                          <td>
                              <?php
                              db_input('ed56_c_situacao', 40, @$ed56_c_situacao, true, 'text', 3, '');
                              db_input('codcursoorig', 50, @$codcursoorig, true, 'hidden', 3, '');
                              db_input('ed56_i_codigo', 50, @$ed56_i_codigo, true, 'hidden', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Etapa:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codserieorig', 15, @$codserieorig, true, 'hidden', 3, '');
                              db_input('nomeserieorig', 20, @$nomeserieorig, true, 'text', 3, '');
                              ?>

                              <b>Turma:</b>

                              <?php
                              db_input('codturmaorig', 15, @$codturmaorig, true, 'hidden', 3, '');
                              db_input('nometurmaorig', 20, @$nometurmaorig, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset style="width:95%;">
                      <legend><b>Dados de Destino (Atestado de Vaga)</b></legend>
                      <table border="0">
                        <tr>
                          <td>
                            <b>Escola:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codescoladest', 15, @$codescoladest, true, 'hidden', 3, '');
                              db_input('nomeescoladest', 40, @$nomeescoladest, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Etapa:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codseriedest', 15, @$codseriedest, true, 'hidden', 3, '');
                              db_input('nomeseriedest', 40, @$nomeseriedest, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Base:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codbasedest', 15, @$codbasedest, true, 'hidden', 3, '');
                              db_input('nomebasedest', 40, @$nomebasedest, true, 'text', 3, '');
                              db_input('ed29_i_avalparcial', 1, @$ed29_i_avalparcial, true, 'hidden', 3, '');
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Calendário:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codcaldest', 15, @$codcaldest, true, 'hidden', 3, '');
                              db_input('nomecaldest', 40, @$nomecaldest, true, 'text', 3, '');
                              if ($ed60_c_concluida == "S") {
                                ?>
                                <input type="button" name="alt_calendario" value="Alterar"
                                       onclick="js_destino(<?= $codatestdest ?>)">
                              <?php } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <b>Turno:</b>
                          </td>
                          <td>
                              <?php
                              db_input('codturnodest', 15, @$codturnodest, true, 'hidden', 3, '');
                              db_input('nometurnodest', 40, @$nometurnodest, true, 'text', 3, '');
                              ?>
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  </td>
                </tr>
                  <?php
                  $camposant = "turma.ed57_i_codigo as codturmadest, ";
                  $camposant .= "turma.ed57_c_descr as nometurmadest, ";
                  $camposant .= "calendario.ed52_d_inicio, ";
                  $camposant .= "calendario.ed52_d_fim, ";
                  $camposant .= "ed60_d_datasaida as datasaida, ";
                  $camposant .= "ed60_i_codigo as matriculaante";

                  $result_verif = $clmatricula->sql_record(
                    $clmatricula->sql_query(
                      "",
                      $camposant,
                      "",
                      " ed60_i_aluno = {$ed60_i_aluno} AND calendario.ed52_i_codigo= {$codcaldest} AND ed60_c_situacao = 'TRANSFERIDO REDE' AND turma.ed57_i_escola = " . db_getsession("DB_coddepto") . " AND ed60_c_ativa ='S'"
                    )
                  );

                  $linhas_verif = $clmatricula->numrows;

                  if ($clmatricula->numrows > 0) {
                      db_fieldsmemory($result_verif, 0);
                      $datahj = date("Y-m-d");
                      $datasaida_dia = substr($datasaida, 8, 2);
                      $datasaida_mes = substr($datasaida, 5, 2);
                      $datasaida_ano = substr($datasaida, 0, 4);
                      $data_in = mktime(0, 0, 0, $datasaida_mes, $datasaida_dia, $datasaida_ano);
                      $data_out = mktime(0, 0, 0, substr($datahj, 5, 2), substr($datahj, 8, 2), substr($datahj, 0, 4));
                      $data_entre = $data_out - $data_in;
                      $dias = ceil($data_entre / 86400);
                      ?>
                    <tr>
                      <td colspan="2">
                        <font color="red"><b>Aluno (<?= $ed60_i_aluno ?>) já possui matrícula nesta escola na turma abaixo
                            relacionada, com situação de TRANSFERIDO REDE a <?= @$dias ?>
                            dia<?= @$dias > 1 ? "(s)" : "" ?>.</b></font>
                      </td>
                    </tr>
                      <?php
                  }
                  ?>
              </table>
              <table>
                <tr id="linhaTurno">
                  <td>
                      <?php
                      db_ancora("<b>Turma Destino:</b>", "js_pesquisaturmadest(true);", 1);
                      ?>
                  </td>
                  <td>
                      <?php
                      db_input('codturmadest', 15, @$Icodturmadest, true, 'text', 3, '');
                      db_input('nometurmadest', 50, @$Inometurmadest, true, 'text', 3, '');
                      db_input('ed52_d_inicio', 10, @$Ied52_d_inicio, true, 'hidden', 3, '');
                      db_input('ed52_d_fim', 10, @$Ied52_d_fim, true, 'hidden', 3, '');

                      if ($clmatricula->numrows > 0) {
                        ?>
                        <b>Matrícula:</b>
                          <?php
                          db_input('matriculaante', 10, @$Imatriculaante, true, 'text', 3, '');
                          ?>
                        <b>Data Saída:</b>
                          <?php
                          db_inputdata('datasaida', @$datasaida_dia, @$datasaida_mes, @$datasaida_ano, true, 'text', 3, "");
                      }
                      ?>
                  </td>
                </tr>
              </table>
              <div>
                <iframe id="iframe_trocaturma" name="iframe_trocaturma" src="" width="100%" height="800"
                        frameborder="0"></iframe>
              </div>
              <?php } ?>
            </table>
          </fieldset>
        </form>
      </td>
    </tr>
  </table>

  <?php
  db_menu();
  ?>
  </body>
  </html>
  <script>
    var oTurmaTurno;
    var aNomeTurnoReferencia = {1: 'Manhã', 2: 'Tarde', 3: 'Noite'};

    js_tabulacaoforms("form1", "ed103_i_codigo", true, 1, "ed103_i_codigo", true);

    function js_pesquisaturmadest(mostra) {

      document.getElementById("iframe_trocaturma").style.visibility = "hidden";

      if(mostra) {

        js_OpenJanelaIframe('CurrentWindow.corpo',
          'db_iframe_turma',
          'func_turmatransfrede.php?avalparcial=' + document.form1.ed29_i_avalparcial.value +
          '&codaluno=' + document.form1.ed47_i_codigo.value +
          '&aluno=' + document.form1.ed47_v_nome.value +
          '&serie=' + document.form1.codseriedest.value +
          '&calendario=' + document.form1.codcaldest.value +
          '&turmasprogressao=f' +
          '&funcao_js=parent.js_mostraturma1|ed57_i_codigo|ed57_c_descr|ed52_d_inicio|ed52_d_fim',
          'Pesquisa de Turma de Destino',
          true
        );
      }
    }

    function js_mostraturma1(chave1, chave2, chave3, chave4) {

      document.form1.codturmadest.value = chave1;
      document.form1.nometurmadest.value = chave2;
      document.form1.ed52_d_inicio.value = chave3;
      document.form1.ed52_d_fim.value = chave4;
      db_iframe_turma.hide();

      if(oTurmaTurno instanceof DBViewFormularioEducacao.TurmaTurnoReferente) {
        oTurmaTurno.limpaLinhasCriadas();
      }
      js_mostraTurnosTurma();

      js_abreIframe();
    }

    function js_pesquisatransf() {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_transfescolarede', 'func_transfescolarede.php?funcao_js=parent.js_preenchepesquisa|ed103_i_codigo', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {

      db_iframe_transfescolarede.hide();
        <?php
        echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        ?>
    }

    function js_destino(atestado) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_atestvaga', 'edu1_atestvaga002.php?chavepesquisa=' + atestado, 'Alterar Calendário do Atestado de Vaga', true);
    }

    function js_mostraTurnosTurma() {

      oTurmaTurno = new DBViewFormularioEducacao.TurmaTurnoReferente($('linhaTurno'), $F('codturmadest'));
      oTurmaTurno.show();
    }

    function js_abreIframe() {

      var lValidacoesTurma = true;

      lValidacoesTurma = js_validaTurma();

      if(!lValidacoesTurma) {
        return false;
      }

      var sTurnoReferente = js_validaTurnosSelecionados().join(',');

      var sGet = "ed103_i_codigo=" + $F('ed103_i_codigo');
      sGet += "&matricula=" + $F('ed103_i_matricula');
      sGet += "&turmaorigem=" + $F('codturmaorig');
      sGet += "&turmadestino=" + $F('codturmadest');
      sGet += "&sTurnoReferente=" + sTurnoReferente;

      iframe_trocaturma.location.href = 'edu1_matriculatransf002.php?' + sGet;
      document.getElementById("iframe_trocaturma").style.visibility = "visible";
    }

    /**
     * Busca os checkbox que estão selecionados e os adiciona a um array de turno referencia
     */
    function js_validaTurnosSelecionados() {

      var aTurnoReferencia = new Array();
      // Verifica quais referências estão selecionadas e adicionas elas ao array aTurnoReferencia
      for(var i = 1; i < 4; i++) {

        if($("check_turno" + i) && $("check_turno" + i).checked) {
          aTurnoReferencia.push($F("check_turno" + i));
        }
      }

      return aTurnoReferencia;
    }

    /**
     * Verifica qual tipo de validação deve ser feita
     */
    function js_validaTurma() {

      var lValidacoesTurma = false;

      if(oTurmaTurno.lEnsinoInfantil && oTurmaTurno.lTurnoIntegral) {
        lValidacoesTurma = js_validaTurmaInfantilIntegral();
      } else {
        lValidacoesTurma = js_validaTurmaNormal();
      }

      return lValidacoesTurma;
    }

    /**
     * Validações aplicadas caso a turma seja de ensino infantil e o turno seja integral
     */
    function js_validaTurmaInfantilIntegral() {

      var aTurnoReferencia = new Array();

      aTurnoReferencia = js_validaTurnosSelecionados();

      // Verifica se ao menos 1 checkbox esta selecionado
      if(aTurnoReferencia.length == 0) {

        alert(_M('educacao.escola.db_frmalunocursomatr.selecione_turno'));
        return false;
      }

      // Verifica se existe vagas disponíveis nos turnos referentes
      var lTemVagas = true;
      var sMsg = "Turma não possui vaga no(s) turno(s):";
      for(var index = 0; index < aTurnoReferencia.length; index++) {

        var aVagasTurno = new Array();
        aVagasTurno = oTurmaTurno.getVagasDisponiveis(aTurnoReferencia[index]);

        if(aVagasTurno.length == 0) {
          lTemVagas = false;
          sMsg += "\n - " + aNomeTurnoReferencia[aTurnoReferencia[index]];
        }
      }

      if(!lTemVagas) {
        alert(sMsg);
      }
      return lTemVagas;

    }

    /**
     * Valida vagas quando a turma é diferente de infantil ou seu turno é diferente de integral
     */
    function js_validaTurmaNormal() {

      // Verifica se existe vagas disponíveis na turma
      if(!oTurmaTurno.temVagasDisponiveis()) {
        alert(_M('educacao.escola.db_frmalunocursomatr.turno_sem_vagas'));
        return false;
      }
      return true;
    }
  </script>
<?php if ($clmatricula->numrows > 0) { ?>
  <script>

    js_mostraTurnosTurma();

    var sTurnoReferente = js_validaTurnosSelecionados().join(',');

    var sUrl = 'edu1_matriculatransf002.php?ed103_i_codigo=<?=$ed103_i_codigo?>';
    sUrl += '&matricula=<?=$ed103_i_matricula?>&turmaorigem=<?=$codturmaorig?>';
    sUrl += '&turmadestino=<?=$codturmadest?>&matriculaante=<?=$matriculaante?>';
    sUrl += "&sTurnoReferente=" + sTurnoReferente;

    iframe_trocaturma.location.href = sUrl;
    document.getElementById("iframe_trocaturma").style.visibility = "visible";

  </script>
<?php } ?>