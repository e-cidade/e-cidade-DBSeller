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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/DBDate.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("classes/db_edu_parametros_classe.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("libs/db_libparagrafo.php"));
require_once(modification("model/educacao/avaliacao/iFormaObtencao.interface.php"));
require_once(modification("model/educacao/avaliacao/iElementoAvaliacao.interface.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("std/db_stdClass.php"));

$dDataDia = date('d');
$dDataMes = date('m');
$dDataAno = date('Y');
$escola          = db_getsession("DB_coddepto");
$clmatricula     = new cl_matricula;
$clobsboletim    = new cl_obsboletim;
$clprocresultado = new cl_procresultado;
$clprocavaliacao = new cl_procavaliacao;
$clregencia      = new cl_regencia;
$clturma         = new cl_turma;
$resultedu       = eduparametros(db_getsession("DB_coddepto"));

db_postmemory($_GET);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/strings.js"></script>
<script type="text/javascript" src="scripts/DBFormCache.js"></script>
<script type="text/javascript" src="scripts/DBFormSelectCache.js"></script>
<script type="text/javascript" src="scripts/AjaxRequest.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<SCRIPT LANGUAGE="JavaScript">
  team = new Array(
  <?php
  # Seleciona todos os calendários
  $sql = "SELECT DISTINCT ed52_i_codigo,
                          ed52_c_descr,
                          ed52_i_ano
                     FROM serie
               INNER JOIN matriculaserie ON ed221_i_serie=ed11_i_codigo
               INNER JOIN matricula ON ed60_i_codigo=ed221_i_matricula
               INNER JOIN turma ON ed57_i_codigo=ed60_i_turma
               INNER JOIN calendario ON ed57_i_calendario=ed52_i_codigo
               INNER JOIN calendarioescola ON ed38_i_calendario = ed52_i_codigo
               INNER JOIN ensino ON ed10_i_codigo = ed11_i_ensino
                    WHERE matriculaserie.ed221_c_origem = 'S'
                      AND ed38_i_escola = $escola
                      AND ed52_c_passivo = 'N'
                      AND EXISTS
                           (SELECT 1
                              FROM regencia
                             WHERE ed59_c_encerrada = 'S'
                               AND ed59_i_turma = ed57_i_codigo)
                          ORDER BY ed52_i_ano DESC ";
  $sql_result = db_query($sql);
  $num        = pg_num_rows($sql_result);
  $conta      = "";

  while ($row = pg_fetch_array($sql_result)) {

    $conta     = $conta+1;
    $cod_curso = $row["ed52_i_codigo"];
    echo "new Array(\n";
    $sub_sql    = " SELECT DISTINCT ed220_i_turma||'.'||ed11_i_codigo as a_turma,ed57_c_descr,ed11_c_descr,ed11_i_codigo ";
    $sub_sql   .= "   FROM turma ";
    $sub_sql   .= "        inner join matricula           on ed60_i_turma      = ed57_i_codigo ";
    $sub_sql   .= "        inner join turmaserieregimemat on ed220_i_turma     = ed57_i_codigo ";
    $sub_sql   .= "        inner join serieregimemat      on ed223_i_codigo    = ed220_i_serieregimemat ";
    $sub_sql   .= "        inner join serie               on ed11_i_codigo     = ed223_i_serie ";
    $sub_sql   .= "        inner join matriculaserie      on ed221_i_matricula = ed60_i_codigo ";
    $sub_sql   .= "                                      and ed221_i_serie     = ed223_i_serie ";
    $sub_sql   .= "  WHERE ed57_i_calendario = $cod_curso ";
    $sub_sql   .= "    AND ed57_i_escola     = $escola ";
    $sub_sql   .= "    AND ed221_c_origem    = 'S' ";
    $sub_sql   .= "  ORDER BY ed57_c_descr,ed11_c_descr ";

    $sub_result = db_query($sub_sql);
    $num_sub    = pg_num_rows($sub_result);

    if ($num_sub >= 1) {

      # Se achar alguma base para o curso, marca a palavra Todas
      echo "new Array(\"\", ''),\n";
      $conta_sub = "";
      while ($rowx = pg_fetch_array($sub_result)) {

        $codigo_base = $rowx["a_turma"];
        $base_nome   = $rowx["ed57_c_descr"];
        $serie_nome  = $rowx["ed11_c_descr"];
        $etapa_cod   = $rowx["ed11_i_codigo"];
        $conta_sub   = $conta_sub+1;
        if ($conta_sub == $num_sub) {
          echo "new Array(\"$base_nome - $serie_nome\", $codigo_base)\n";
          $conta_sub = "";
        } else {
          echo "new Array(\"$base_nome - $serie_nome\", $codigo_base),\n";
        }
      }
    } else {

      #Se nao achar base para o curso selecionado...
      echo "new Array(\"Calendário sem turmas cadastradas\", '')\n";
    }

    if ($num > $conta) {
      echo "),\n";
    }
}
echo ")\n";
echo ");\n";
?>
//Inicio da função JS
function fillSelectFromArray(selectCtrl, itemArray, goodPrompt, badPrompt, defaultItem) {

  var i, j;
  var prompt;
  // empty existing items
  for (i = selectCtrl.options.length; i >= 0; i--) {
    selectCtrl.options[i] = null;
  }
  prompt = (itemArray != null) ? goodPrompt : badPrompt;
  if (prompt == null) {
    document.form1.subgrupo.disabled = true;
    j = 0;
  } else {
    selectCtrl.options[0] = new Option(prompt);
    j = 1;
  }
  if (itemArray != null) {
   // add new items
    for (i = 0; i < itemArray.length; i++) {
      selectCtrl.options[j] = new Option(itemArray[i][0]);
      if (itemArray[i][1] != null) {
        selectCtrl.options[j].value = itemArray[i][1];
      }
      j++;
    }
    selectCtrl.options[0].selected   = true;
    document.form1.subgrupo.disabled = false;
  }
  document.form1.procurar.disabled = true;
 <?php if (isset($turma)) {?>
     qtd = document.form1.alunosdiario.length;
     for (i = 0; i < qtd; i++) {
       document.form1.alunosdiario.options[0] = null;
     }
     qtd = document.form1.alunos.length;
     for (i = 0; i < qtd; i++) {
       document.form1.alunos.options[0] = null;
     }
 <?php } ?>
}
function fillSelectFromArray2(selectCtrl, itemArray, goodPrompt, badPrompt, defaultItem) {

  var i, j;
  var prompt;
  // empty existing items
  for (i = selectCtrl.options.length; i >= 0; i--) {
    selectCtrl.options[i] = null;
  }
  prompt = (itemArray != null) ? goodPrompt : badPrompt;
  if (prompt == null) {
    document.form1.subgrupo.disabled = true;
    j = 0;
  } else {
    selectCtrl.options[0] = new Option(prompt);
    j = 1;
  }
  if (itemArray != null) {
  // add new items
    for (i = 0; i < itemArray.length; i++) {
      selectCtrl.options[j] = new Option(itemArray[i][0]);
      if (itemArray[i][1] != null) {
        selectCtrl.options[j].value = itemArray[i][1];
      }
    <?php if (isset($turma)) {?>
        if (<?=trim($turma)?> == itemArray[i][1]) {
          indice = i;
        }
    <?php } ?>
      j++;
  }
  <?php if (isset($turma)) {?>
      selectCtrl.options[indice].selected = true;
      document.form1.procurar.disabled    = false;
  <?php } else {?>
      selectCtrl.options[0].selected = true;
  <?php } ?>
    document.form1.subgrupo.disabled = false;
  }
}
//End -->
</script>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1;" >
  <table width="790" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <div class="center">
    <form name="form1" method="post" action="">
      <?php
      MsgAviso(db_getsession("DB_coddepto"),"escola");
      ?>
      <br>
      <fieldset style="width:95%">
        <legend><b>Relatório Certificado de Conclusão (Novo)</b></legend>
        <table border="0" align="left">
          <tr>
            <td colspan="3">
              <table border="0" align="left">
                <tr>
                  <td>
                    <b>Selecione o Calendário:</b><br>
                    <select name="grupo"
                            onChange="fillSelectFromArray(this.form.subgrupo, ((this.selectedIndex == -1) ? null : team[this.selectedIndex-1]));"
                            style="font-size:9px;width:200px;height:18px;">
                      <option></option>
                      <?php
                      #Seleciona todos os grupos para setar os valores no combo
                      $sql = "SELECT DISTINCT ed52_i_codigo,
                                              ed52_c_descr,
                                              ed52_i_ano
                                         FROM serie
                                   INNER JOIN matriculaserie ON ed221_i_serie=ed11_i_codigo
                                   INNER JOIN matricula ON ed60_i_codigo=ed221_i_matricula
                                   INNER JOIN turma ON ed57_i_codigo=ed60_i_turma
                                   INNER JOIN calendario ON ed57_i_calendario=ed52_i_codigo
                                   INNER JOIN calendarioescola ON ed38_i_calendario = ed52_i_codigo
                                   INNER JOIN ensino ON ed10_i_codigo = ed11_i_ensino
                                        WHERE matriculaserie.ed221_c_origem = 'S'
                                          AND ed38_i_escola = $escola
                                          AND ed52_c_passivo = 'N'
                                          AND EXISTS
                                                (SELECT 1
                                                   FROM regencia
                                                  WHERE ed59_c_encerrada = 'S'
                                                    AND ed59_i_turma = ed57_i_codigo)
                                                ORDER BY ed52_i_ano DESC ";
                      $sql_result = db_query($sql);

                      while( $row = pg_fetch_array($sql_result) ) {

                        $cod_curso  = $row["ed52_i_codigo"];
                        $desc_curso = $row["ed52_c_descr"];
                        ?>
                        <option value="<?=$cod_curso;?>" <?=isset( $calendario ) && $cod_curso == $calendario ? "selected" : ""?>><?=$desc_curso;?></option>
                        <?php
                      }
                      #Popula o segundo combo de acordo com a escolha no primeiro
                      ?>
                    </select>
                  </td>
                  <td>
                    <b>Selecione a Turma:</b><br>
                    <select name="subgrupo"
                            style="font-size:9px;width:200px;height:18px;"
                            disabled
                            onchange="js_botao(this.value);">
                      <option value=""></option>
                    </select>
                  </td>
                  <td valign='bottom'>
                    <input type="button" name="procurar" value="Procurar"
                           onclick="js_procurar(document.form1.grupo.value,document.form1.subgrupo.value)" disabled>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <?php
          if( isset( $turma ) ) {
            ?>
            <script>fillSelectFromArray2(document.form1.subgrupo, ((document.form1.grupo.selectedIndex == -1) ? null : team[document.form1.grupo.selectedIndex-1]));</script>
            <tr>
              <td valign="top">
                <?php
$arr_turma = explode(".",$turma);
$cod_turma = $arr_turma[0];
$eta_turma = $arr_turma[1];
//Lucas

                $sql    = " SELECT ed47_i_codigo,ed47_v_nome,ed60_i_codigo ";
                $sql   .= "   FROM matricula ";
                $sql   .= "        inner join aluno               on ed47_i_codigo     = ed60_i_aluno ";
                $sql   .= "        inner join turma               on ed57_i_codigo     = ed60_i_turma ";
                $sql   .= "        inner join turmaserieregimemat on ed220_i_turma     = ed57_i_codigo ";
                $sql   .= "        inner join serieregimemat      on ed223_i_codigo    = ed220_i_serieregimemat ";
                $sql   .= "        inner join serie               on ed11_i_codigo     = ed223_i_serie ";
                $sql   .= "        inner join matriculaserie      on ed221_i_matricula = ed60_i_codigo ";
                $sql   .= "        inner join calendario          on ed57_i_calendario = ed52_i_codigo ";
                $sql   .= "  WHERE ed220_i_turma  = {$cod_turma} ";
                $sql   .= "    AND ed221_c_origem = 'S' ";
                $sql   .= "    AND ed221_i_serie  = ed223_i_serie ";
                $sql   .= "    AND ed60_c_situacao IN ('MATRICULADO','REMATRICULADO') ";
                $sql   .= "    AND (select count(*) from historicomps where ed62_i_historico IN (select ed61_i_codigo from  historico where ed61_i_aluno = ed60_i_aluno) AND ed62_c_resultadofinal = 'A'  AND ed62_i_serie = $eta_turma AND trim(ed62_i_turma) = trim(ed57_c_descr)) > 0 ";
                $sql   .= "  ORDER BY ed60_i_numaluno,to_ascii(ed47_v_nome) ";
                $result = db_query($sql);
                $linhas = pg_num_rows($result);
                ?>
                <b>Alunos:</b><br>
                <select name="alunosdiario" id="alunosdiario" size="10" onclick="js_desabinc()"
                        style="font-size:9px;width:330px;height:120px" multiple>
                <?php
                for($i = 0; $i < $linhas; $i++) {

                  db_fieldsmemory($result,$i);
                  echo "<option value='$ed60_i_codigo'>$ed47_i_codigo - $ed47_v_nome</option>\n";
                }
                ?>
              </select>
            </td>
            <td align="center">
              <br>
              <table border="0">
                <tr>
                  <td>
                    <input name="incluirum" title="Incluir" type="button" value=">"
                           onclick="js_incluir();" style="border:1px outset;border-top-color:#f3f3f3;border-left-color:#f3f3f3;
                           background:#cccccc;font-size:12px;font-weight:bold;width:30px;height:15px;padding:0px;" disabled>
                  </td>
                </tr>
                <tr>
                  <td height="1"></td>
                </tr>
                <tr>
                  <td>
                    <input name="incluirtodos" title="Incluir Todos" type="button" value=">>" onclick="js_incluirtodos();"
                           style="border:1px outset;border-top-color:#f3f3f3;border-left-color:#f3f3f3;background:#cccccc;
                           font-size:12px;font-weight:bold;width:30px;height:15px;padding:0px;" <?=$linhas==0?"disabled":""?>>
                  </td>
                </tr>
                <tr>
                  <td height="3"></td>
                </tr>
                <tr>
                  <td><hr></td>
                </tr>
                <tr>
                  <td height="3"></td>
                </tr>
                <tr>
                  <td>
                    <input name="excluirum" title="Excluir" type="button" value="<" onclick="js_excluir();"
                           style="border:1px outset;border-top-color:#f3f3f3;border-left-color:#f3f3f3;
                           background:#cccccc;font-size:12px;font-weight:bold;width:30px;height:15px;padding:0px;" disabled>
                  </td>
                </tr>
                <tr>
                  <td height="1"></td>
                </tr>
                <tr>
                  <td>
                    <input name="excluirtodos" title="Excluir Todos" type="button" value="<<" onclick="js_excluirtodos();"
                           style="border:1px outset;border-top-color:#f3f3f3;border-left-color:#f3f3f3;
                           background:#cccccc;font-size:12px;font-weight:bold;width:30px;height:15px;padding:0px;" disabled>
                  </td>
                </tr>
              </table>
            </td>
            <td valign="top">
              <b>Alunos para gerar o Certificado:</b><br>
              <select name="alunos[]" id="alunos" size="10" onclick="js_desabexc()"
                      style="font-size:9px;width:330px;height:120px" multiple>
              </select>
            </td>
          </tr>
          <tr>
              <td colspan="3">
                  <label for="tipocertificado" ><b>Tipo do Modelo:</b></label>
                  <select name="tipocertificado" id= "tipocertificado" class="field-size8">
                  </select>
              </td>
          </tr>
          <tr>
              <td colspan="3">
                <b>Data da Emissão: </b>
                <?php db_inputdata('dtAtual', "$dDataDia", "$dDataMes", "$dDataAno", true, 'text', '1', ""); ?>
              </td>
            </tr>
            <tr>
            <td align="center" colspan="3">
                <input name="pesquisar"
                       type="button"
                       id="pesquisar"
                       value="Processar"
                       onclick="js_pesquisa(document.form1.subgrupo.value);" disabled>
              <br><br>
              <fieldset style="align:center">
                Para selecionar mais de um aluno<br>mantenha pressionada a tecla CTRL <br>e clique sobre o nome dos alunos.
              </fieldset>
              <input type="hidden" name="base"  value="<?=isset( $base ) ? $base : ""?>">
              <input type="hidden" name="curso" value="<?=isset( $curso ) ? $curso : ""?>">
            </td>
          </tr>
          <?php
          }
          ?>
        </table>
      </fieldset>
    </form>
  </div>
  <?php
  db_menu(db_getsession("DB_id_usuario"),
            db_getsession("DB_modulo"),
            db_getsession("DB_anousu"),
            db_getsession("DB_instit")
           );
  ?>
</body>
</html>
<script type="text/javascript">
    const cboTipoCertificado = document.getElementById('tipocertificado');

    var oParametros = {};
    oParametros.exec = 'getTipoCertificado';
    oParametros.escola = <?=db_getsession('DB_coddepto')?>;

    new Ajax.Request('edu4_escola.RPC.php',
        {
            method: "post",
            parameters:'json='+Object.toJSON(oParametros),
            onComplete: (response) => {
                let retorno = JSON.parse(response.responseText);
                cboTipoCertificado.options.lenght = 0;
                retorno.aResultTipoCertificado.map((tipo) => {
                    cboTipoCertificado.options.add(new Option(tipo.ed217_c_nome.urlDecode(), tipo.ed217_i_codigo));
                });
            }
        });

function js_init() {

  if (<?=(isset($calendario) && isset($turma))?'true':'false'?>) {
    js_remove();
  }
}

function js_remove() {

  if (document.form1.disciplinas != undefined) {

    if (document.form1['disciplinas'].options[1].value == 'T') {
      document.form1['disciplinas'].remove(1);
    } else {

      espera=new Option("TODAS","T");
      document.form1.disciplinas.options.add(espera,1);
    }
  }
}

function js_padrao() {

  if (document.form1.padrao.checked == true) {
    document.getElementById("optpadrao").style.visibility = "visible";
  } else {
    document.getElementById("optpadrao").style.visibility = "hidden";
  }
}

function js_incluir() {

  var Tam = document.form1.alunosdiario.length;
  var F   = document.form1;

  for(x = 0; x < Tam; x++) {

    if (F.alunosdiario.options[x].selected == true) {

      F.elements['alunos[]'].options[F.elements['alunos[]'].options.length] = new Option(F.alunosdiario.options[x].text,F.alunosdiario.options[x].value)
      F.alunosdiario.options[x] = null;
      Tam--;
      x--;
    }
  }

  if (document.form1.alunosdiario.length > 0) {
    document.form1.alunosdiario.options[0].selected = true;
  } else {

    document.form1.incluirum.disabled    = true;
    document.form1.incluirtodos.disabled = true;
  }

  document.form1.pesquisar.disabled    = false;
  document.form1.excluirtodos.disabled = false;
  document.form1.alunosdiario.focus();
}

function js_incluirtodos() {

  var Tam = document.form1.alunosdiario.length;
  var F   = document.form1;

  for(i=0;i<Tam;i++){

    F.elements['alunos[]'].options[F.elements['alunos[]'].options.length] = new Option(F.alunosdiario.options[0].text,F.alunosdiario.options[0].value)
    F.alunosdiario.options[0] = null;
  }

  document.form1.incluirum.disabled    = true;
  document.form1.incluirtodos.disabled = true;
  document.form1.excluirtodos.disabled = false;
  document.form1.pesquisar.disabled    = false;
  document.form1.alunos.focus();
}

function js_excluir() {

  var F = document.getElementById("alunos");
  Tam   = F.length;

  for(x = 0; x < Tam; x++) {

    if (F.options[x].selected == true) {

      document.form1.alunosdiario.options[document.form1.alunosdiario.length] = new Option(F.options[x].text,F.options[x].value);
      F.options[x] = null;
      Tam--;
      x--;
    }
  }

  if (document.form1.alunos.length>0){
    document.form1.alunos.options[0].selected = true;
  }

  if (F.length == 0) {

    document.form1.pesquisar.disabled    = true;
    document.form1.excluirum.disabled    = true;
    document.form1.excluirtodos.disabled = true;
    document.form1.incluirtodos.disabled = false;
  }

  document.form1.alunos.focus();
}

function js_excluirtodos() {

  var Tam = document.form1.alunos.length;
  var F = document.getElementById("alunos");

  for (i = 0; i < Tam; i++) {

    document.form1.alunosdiario.options[document.form1.alunosdiario.length] = new Option(F.options[0].text,F.options[0].value);
    F.options[0] = null;
  }

  if (F.length == 0) {

    document.form1.pesquisar.disabled    = true;
    document.form1.excluirum.disabled    = true;
    document.form1.excluirtodos.disabled = true;
    document.form1.incluirtodos.disabled = false;
  }

  document.form1.alunosdiario.focus();
}

function js_desabinc() {

  for(i = 0; i < document.form1.alunosdiario.length; i++) {

    if (document.form1.alunosdiario.length>0 && document.form1.alunosdiario.options[i].selected) {

      if (document.form1.alunos.length>0){
        document.form1.alunos.options[0].selected = false;
      }

      document.form1.incluirum.disabled = false;
      document.form1.excluirum.disabled = true;
    }
  }
}

function js_desabexc() {

  for(i = 0; i < document.form1.alunos.length; i++) {

    if (document.form1.alunos.length>0 && document.form1.alunos.options[i].selected) {

      if (document.form1.alunosdiario.length>0) {
        document.form1.alunosdiario.options[0].selected = false;
      }

      document.form1.incluirum.disabled = true;
      document.form1.excluirum.disabled = false;
    }
  }
}

function js_botao(valor) {

  if ($('pesquisar')) {
    $('pesquisar').setAttribute('disabled', 'disabled');
  }

  if (valor != "") {
    document.form1.procurar.disabled = false;
  } else {
    document.form1.procurar.disabled = true;
  }
  <?php
  if (isset($turma)) {

    ?>

    qtd = document.form1.alunosdiario.length;
    for (i = 0; i < qtd; i++) {
      document.form1.alunosdiario.options[0] = null;
    }

    qtd = document.form1.alunos.length;

    for (i = 0; i < qtd; i++) {
      document.form1.alunos.options[0] = null;
    }
  <?php
  }
  ?>
}

function js_procurar(calendario,turma) {

    location.href = "edu2_cerconclusao001.php?calendario="+calendario+"&turma="+turma;

}

function js_pesquisa(iTurma) {

  F      = document.form1.alunos;
  alunos = "";
  sep    = "";

  for(i = 0; i < F.length; i++) {

    alunos += sep+F.options[i].value;
    sep     = ",";
  }

  var sUrlBoletim = 'edu2_cerconclusao002.php';
  var dtAtual = $F('dtAtual');

  jan = window.open(sUrlBoletim+'?tipoRelatorio='+cboTipoCertificado.value+'&alunos='+alunos+
                    '&turma='+document.form1.subgrupo.value+'&dtAtual='+dtAtual,'',
                    'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');


  location.href = "edu2_cerconclusao001.php?calendario="+document.form1.grupo.value+"&turma="+document.form1.subgrupo.value;

}

<?php
if (!isset($turma) && pg_num_rows($sql_result) > 0) {

  ?>
  fillSelectFromArray2(document.form1.subgrupo,team[0]);
  document.form1.grupo.options[1].selected = true;
<?php
}
?>

</script>
