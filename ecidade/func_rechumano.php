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

require_once modification("libs/db_stdlibwebseller.php");
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

use \App\Domain\Educacao\Escola\Models\Disciplina;

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clrechumano   = new cl_rechumano;
$clatividaderh = new cl_atividaderh;
$clensino      = new cl_ensino;
$cldisciplina  = new cl_disciplina;
$clrotulo      = new rotulocampo;

$clrechumano->rotulo->label("ed20_i_codigo");
$clrotulo->label("z01_nome");
$clrotulo->label("z04_nomesocial");
$clrotulo->label("ed12_i_ensino");
$clrotulo->label("ed23_i_disciplina");
$clrotulo->label("ed284_i_rhpessoal");
$clrotulo->label("ed285_i_cgm");
$escola = db_getsession("DB_coddepto");
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script>
    nextfield = "campo1"; // nome do primeiro campo
    netscape = "";
    ver = navigator.appVersion;
    len = ver.length;

    for(iln = 0; iln < len; iln++) {

      if (ver.charAt(iln) == "(") {
        break;
      }
    }

    netscape = (ver.charAt(iln+1).toUpperCase() != "C");
    function keyDown(DnEvents) {

      k = (netscape) ? DnEvents.which : window.event.keyCode;
      if (k == 13) { // pressiona tecla enter
        if (nextfield == 'done') {
          return true; // envia quando termina os campos
        } else {
          document.getElementById(nextfield).focus();
          return false;
        }
      }
    }

    document.onkeydown = keyDown;
    if(netscape) {
      document.captureEvents(Event.KEYDOWN|Event.KEYUP);
    }

    function submete(valor,ativ) {
      const href = "func_rechumano.php?funcao_js=parent.js_preenchepesquisa|ed20_i_codigo&ensino="+valor+"&ativ="+ativ;
      location.href = href;
    }

    function atividade(valor) {

      regencias = document.form1.regente.value.split("|");
      tamanho   = regencias.length;

      for(i = 0; i<tamanho; i++) {

        if( valor == regencias[i] ) {

          document.form1.grupo.disabled    = false;
          document.form1.subgrupo.disabled = false;
          break;
        } else {

          document.form1.grupo.disabled    = true;
          document.form1.subgrupo.disabled = true;
          document.form1.grupo.value       = "";
          document.form1.subgrupo.value    = "";
        }
      }
    }

    team = new Array(
      <?php
        // Seleciona todos os calendários
        $sql = "SELECT DISTINCT ed10_i_codigo, ed10_c_descr, ed10_c_abrev
        FROM ensino
          inner join disciplina      on ed12_i_ensino     = ed10_i_codigo
          inner join relacaotrabalho on ed23_i_disciplina = ed12_i_codigo
          inner join rechumanoescola on ed75_i_codigo     = ed23_i_rechumanoescola
        WHERE ed75_i_escola = {$escola}
        ORDER BY ed10_c_abrev";
        $sql_result = db_query($sql);
        $num        = pg_num_rows($sql_result);
        $conta      = "";

        while ($row = pg_fetch_array($sql_result)) {
            $conta++;
            $cod_curso = $row["ed10_i_codigo"];

            echo "new Array(\n";
            $sub_sql = "SELECT DISTINCT ed12_i_codigo, ed232_c_descr
            FROM disciplina
              inner join caddisciplina   on ed232_i_codigo    = ed12_i_caddisciplina
              inner join relacaotrabalho on ed23_i_disciplina = ed12_i_codigo
              inner join rechumanoescola on ed75_i_codigo     = ed23_i_rechumanoescola
            WHERE ed12_i_ensino = {$cod_curso}
              AND ed75_i_escola = {$escola}
            ORDER BY ed232_c_descr";
            $sub_result = db_query($sub_sql);
            $num_sub    = pg_num_rows($sub_result);

            if ($num_sub >= 1) {
                // Se achar alguma base para o curso, marca a palavra Todas
                echo "new Array(\"\", ''),\n";
                $conta_sub = "";

                while ($rowx = pg_fetch_array($sub_result)) {
                    $codigo_base = $rowx["ed12_i_codigo"];
                    $base_nome   = $rowx["ed232_c_descr"];
                    $conta_sub++;

                    if ($conta_sub == $num_sub) {
                        echo "new Array(\"$base_nome\", $codigo_base)\n";
                        $conta_sub = "";
                    } else {
                        echo "new Array(\"$base_nome\", $codigo_base),\n";
                    }
                }
            } else {
                // Se nao achar base para o curso selecionado...
                echo "new Array(\"Ensino sem disciplinas.\", '')\n";
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
        for(i = selectCtrl.options.length; i >= 0; i--) {
          selectCtrl.options[i] = null;
        }

        prompt = (itemArray != null) ? goodPrompt : badPrompt;
        if(prompt == null) {

          document.form1.subgrupo.disabled = true;
          j = 0;
        } else {

          selectCtrl.options[0] = new Option(prompt);
          j = 1;
        }

        if(itemArray != null) {

          // add new items
          for(i = 0; i < itemArray.length; i++) {

            selectCtrl.options[j] = new Option(itemArray[i][0]);
            if(itemArray[i][1] != null) {
              selectCtrl.options[j].value = itemArray[i][1];
            }

            j++;
          }

          selectCtrl.options[0].selected   = true;
          document.form1.subgrupo.disabled = false;
        }
      }

    //Função não mais chamada de acordo com redmine 22048
    function fillSelectFromArray2(selectCtrl, itemArray, goodPrompt, badPrompt, defaultItem) {
      return false;
      var i, j;
      var prompt;

      // empty existing items
      for(i = selectCtrl.options.length; i >= 0; i--) {
        selectCtrl.options[i] = null;
      }

      prompt = (itemArray != null) ? goodPrompt : badPrompt;
      if(prompt == null) {

        document.form1.subgrupo.disabled = true;
        j = 0;
      } else {

        selectCtrl.options[0] = new Option(prompt);
        j = 1;
      }

      if(itemArray != null) {

        // add new items
        for(i = 0; i < itemArray.length; i++) {

          selectCtrl.options[j] = new Option(itemArray[i][0]);
          if(itemArray[i][1] != null) {
            selectCtrl.options[j].value = itemArray[i][1];
          }
          <?php if (isset($subgrupo)) {?>
          if(<?= trim($subgrupo)?>==itemArray[i][1]){
            indice = i;
          }
          <?php } ?>
          j++;
        }

        <?php if (isset($subgrupo)) {?>
          selectCtrl.options[indice].selected = true;
        <?php } else { ?>
          selectCtrl.options[0].selected = true;
        <?php } ?>
        document.form1.subgrupo.disabled = false;
      }
    }
    //End -->
  </script>
</head>
<body class="body-default">
<div class="container">
 <form name="form1" method="post" action="">
  <fieldset>
   <legend>Recursos Humanos da Escola</legend>
   <table class="form-container">
     <tr>
       <td title="<?= $Ted20_i_codigo?>">
         Matrícula:
       </td>
       <td>
         <?php
            db_input(
                "ed284_i_rhpessoal",
                10,
                $Ied284_i_rhpessoal,
                true,
                "text",
                4,
                "onFocus=\"nextfield='pesquisar2'\" oninput=\"js_ValidaCampos(this,1,'Matrícula','t','f',event);\"",
                "chave_ed284_i_rhpessoal"
            );
            ?>
       </td>
       <td> CGM: </td>
       <td>
         <?php
            db_input(
                "ed285_i_cgm",
                30,
                $Ied285_i_cgm,
                true,
                "text",
                4,
                "onFocus=\"nextfield='pesquisar2'\"",
                "chave_ed285_i_cgm"
            );
            ?>
       </td>
       <td>CPF:</td>
       <td>
         <?php
            db_input("ed285_i_cpf", 30, 1, true, "text", 4, "onFocus=\"nextfield='pesquisar2'\"", "chave_cpf");
            ?>
       </td>
     </tr>
     <tr>
     <td>
     </td>
     </tr>
     <tr>
       <td> Nome: </td>
       <td>
         <?php
            db_input(
                "z01_nome",
                50,
                $Iz01_nome,
                true,
                "text",
                4,
                "onFocus=\"nextfield='pesquisar2'\"",
                "chave_z01_nome"
            );
            ?>
       </td>
       <td> Nome Social: </td>
       <td>
         <?php
            db_input(
                "z04_nomesocial",
                30,
                $Iz01_nome,
                true,
                "text",
                4,
                "onFocus=\"nextfield='pesquisar2'\"",
                "chave_z04_nomesocial"
            );
            ?>
       </td>
       <td>ID INEP:</td>
       <td>
         <?php
            db_input("ed20_i_codigoinep", 30, 1, true, "text", 4, "onFocus=\"nextfield='pesquisar2'\"", "chave_idinep");
            ?>
       </td>
     </tr>
     <tr>
       <td> Atividade: </td>
       <td>
         <?php
            $sSqlAtividadeRh = $clatividaderh->sql_query_file("", "ed01_i_codigo, ed01_c_descr", "ed01_c_descr");
            $result_ativ = $clatividaderh->sql_record($sSqlAtividadeRh);
            $linhas_ativ = pg_num_rows($result_ativ);
            ?>
         <select name="atividaderh"
                 id="atividaderh"
                 onFocus="nextfield='pesquisar2'"
                 style="font-size: 10px; width: 150px;">
           <option value='' selected></option>
           <?php
            for ($x = 0; $x < $linhas_ativ; $x++) {
                db_fieldsmemory($result_ativ, $x);
                $e = "<option value='$ed01_i_codigo' ".(@$atividaderh == $ed01_i_codigo ? "selected" : "").">";
                $e .= $ed01_c_descr . '</option>';
                echo $e;
            }
            ?>
         </select>
       </td>
       <td> Tipo de Hora: </td>
       <td>
        <?php
            $rsTiposDeHora = TipoHoraTrabalhoRepository::listaTipoHoraTrabalho();
        ?>
         <select name="grupo"
                 onFocus="nextfield='pesquisar2'"
                 style="font-size:9px;width:200px;height:18px;">
                 <option value="" selected>Selecione</option>
           <?php
              $tiposHora = pg_fetch_all($rsTiposDeHora);
            foreach ($tiposHora as $hora) {
                ?>
                <option value="<?= $hora['codigo_hora']?>"><?= $hora['descricao_hora']?></option>
                <?php
            }
            ?>
         </select>
       </td>
       <td> <?= $Led23_i_disciplina?> </td>
       <td>
        <?php
            $rsDisciplinasRH = Disciplina::select(
                'ed232_c_descr AS disciplina_descricao',
                'ed232_i_codigo AS codigo_disciplina'
            )->get();
        ?>
         <select name="subgrupo"
                 onFocus="nextfield='pesquisar2'"
                 style="font-size:9px;width:200px;height:18px;" >
           <option value="" selected>Selecione</option>
           <?php
            $disciplinasRH = $rsDisciplinasRH;
            foreach ($disciplinasRH as $disciplinas){
            ?>
            <option value="<?=$disciplinas['codigo_disciplina']?>"><?=$disciplinas['disciplina_descricao']?></option>
           <?php } ?>
         </select>
       </td>
     </tr>
   </table>
  </fieldset>
  <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar" onFocus="nextfield='done'">
  <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_rechumano.hide();">
  <input name="regente" type="hidden" value="">
 </form>
<?php
$sSqlAtividadeRh = $clatividaderh->sql_query_file("", "ed01_i_codigo as reg", "ed01_c_descr", "ed01_c_regencia = 'S'");

$result_regente  = $clatividaderh->sql_record($sSqlAtividadeRh);

if ($clatividaderh->numrows > 0) {
    $sep       = "";
    $regencias = "";

    for ($x = 0; $x < $clatividaderh->numrows; $x++) {
        db_fieldsmemory($result_regente, $x);
        $regencias .= $sep.$reg;
        $sep        = "|";
    }
} else {
    $regencias = 0;
}
?>
<script>
  document.form1.regente.value = "<?= $regencias?>";
  <?php if (isset($ativ)) { ?>
  document.form1.atividaderh.value = <?= $ativ?>;
  <?php } ?>
  js_tabulacaoforms("form1","chave_z01_nome",true,1,"chave_z01_nome",true);
</script>
    <?php
    $escola = db_getsession("DB_coddepto");
    $termo  = false;
    if (!isset($pesquisa_chave)) {
        $campos = "rechumano.ed20_i_codigo,
      case when ed20_i_tiposervidor = 1
                then cgmrh.z01_nome
                else cgmcgm.z01_nome
        end as z01_nome,
                rechumanopessoal.ed284_i_rhpessoal AS dl_Matricula,
      case when ed20_i_tiposervidor = 1
                then rhpessoal.rh01_numcgm
                else rechumanocgm.ed285_i_cgm
        end as dl_CGM,
      case when ed20_i_tiposervidor = 1
                then cgmrh.z01_cgccpf
                else cgmcgm.z01_cgccpf
        end as \"dl_cpf\",
                ed01_c_descr AS dl_atividade,
                ed24_c_descr AS dl_Regime_de_Trabalho,
                ed128_descricao AS dl_Tipo_de_Hora,
                ed75_d_ingresso,
                ed75_i_saidaescola";
        $where = "";

    if (isset($chave_ed284_i_rhpessoal) && ( trim($chave_ed284_i_rhpessoal) != "" )) {
        $termo  = true;
        $where .= " AND rechumanopessoal.ed284_i_rhpessoal = {$chave_ed284_i_rhpessoal}";
    }

    if (isset($chave_ed285_i_cgm) && ( trim($chave_ed285_i_cgm) != "" )) {
        $termo  = true;
        $where .= " AND (rechumanocgm.ed285_i_cgm = {$chave_ed285_i_cgm} OR cgmrh.z01_numcgm = {$chave_ed285_i_cgm}) ";
    }

    if (isset($chave_z01_nome) && ( trim($chave_z01_nome) != "" )) {
        $termo  = true;
        $where .= " AND (cgmrh.z01_nome like '{$chave_z01_nome}%' OR cgmcgm.z01_nome like '{$chave_z01_nome}%') ";
    }

    if (isset($chave_z04_nomesocial) && ( trim($chave_z04_nomesocial) != "" )) {
        $termo  = true;
        $where .= " AND cgmfisico.z04_nomesocial like '{$chave_z04_nomesocial}%' ";
    }

    if (isset($atividaderh) && ( trim($atividaderh) != "" )) {
        $termo  = true;
        $where .= " AND rechumanoativ.ed22_i_atividade = {$atividaderh}";
    }

    if (isset($grupo) && ( trim($grupo) != "" )) {
        $termo  = true;
        $where .= " AND tipohoratrabalho.ed128_codigo = {$grupo}";
    }

    if (isset($subgrupo) && ( trim($subgrupo) != "" )) {
        $termo  = true;
        $where .= " AND caddisciplina.ed232_i_codigo = {$subgrupo}";
    }

    if (isset($chave_idinep) && (trim($chave_idinep) != "")) {
        $termo  = true;
        $where .= " AND rechumano.ed20_i_codigoinep = {$chave_idinep}";
    }

    if (isset($chave_cpf) && (trim($chave_cpf) != "")) {
        $termo  = true;
        $where .= " AND (cgmrh.z01_cgccpf = '{$chave_cpf}' or cgmcgm.z01_cgccpf = '{$chave_cpf}') ";
    }

    if (isset($iFuncaoAtividade) && !empty($iFuncaoAtividade)) {
        $sWhereTurma = "";
        if (isset($iTurma) && !empty($iTurma)) {
            $sWhereTurma = " and ed347_turma = {$iTurma}";
        }

        $termo  = true;
        $where .= " AND atividaderh.ed01_funcaoatividade = {$iFuncaoAtividade}";
        $where .= " AND ensino.ed10_i_tipoensino IS NULL ";
        $where .= " AND rechumanoescola.ed75_i_saidaescola IS NULL ";
        $where .= " AND not exists ( select 1";
        $where .= " from turmaoutrosprofissionais";
        $where .= " where ed347_rechumano = rechumano.ed20_i_codigo";
        $where .= " and ed347_funcaoatividade = {$iFuncaoAtividade}";
        $where .= " {$sWhereTurma})";
    }

    $w = "ed75_i_escola = {$escola} {$where} ";
    $sql     = $clrechumano->sql_query_escola_rechumano("", "distinct {$campos}", "z01_nome", $w);
    $repassa = array();

    if (isset($chave_ed284_i_rhpessoal)) {
        $repassa = array(
        "chave_ed284_i_rhpessoal" => $chave_ed284_i_rhpessoal,
        "chave_ed285_i_cgm" => $chave_ed285_i_cgm,
        "chave_z01_nome" => $chave_z01_nome,
        "chave_z01_cgccpf" => $chave_cpf,
        "chave_ed20_i_codigoinep" => $chave_idinep,
        "chave_z04_nomesocial" => $chave_z04_nomesocial,
        "subgrupo" => @$subgrupo,
        "grupo" => @$grupo,
        "atividaderh" => @$atividaderh
        );
    }

    if ($termo == true) {
        db_lovrot(@$sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    }

    $termo  = true;
    $where .= " AND caddisciplina.ed232_i_codigo = {$subgrupo}";
  } else {
        if ($pesquisa_chave != null && $pesquisa_chave != "") {
            $campos = "ed20_i_codigo,
        case when ed20_i_tiposervidor = 1
                  then cgmrh.z01_nome
                  else cgmcgm.z01_nome
        end as z01_nome,
        case when ed20_i_tiposervidor = 1
                  then cgmrh.z01_cgccpf
                  else cgmcgm.z01_cgccpf
        end as \"dl_cpf\",
        rh37_descr";

            $sWhereRecHumano = "ed20_i_codigo = {$pesquisa_chave} AND ed75_i_escola = {$escola}";

            if (isset($iFuncaoAtividade) && !empty($iFuncaoAtividade)) {
                $sWhereTurma = "";
                if (isset($iTurma) && !empty($iTurma)) {
                    $sWhereTurma = " and ed347_turma = {$iTurma}";
                }

                $termo  = true;
                $where .= " AND atividaderh.ed01_funcaoatividade = {$iFuncaoAtividade}";
                $where .= " AND not exists ( select 1";
                $where .= " from turmaoutrosprofissionais";
                $where .= " where ed347_rechumano = rechumano.ed20_i_codigo";
                $where .= " and ed347_funcaoatividade = {$iFuncaoAtividade}";
                $where .= " {$sWhereTurma})";
            }

            $sSqlRecHumano = $clrechumano->sql_query_escola_rechumano("", $campos, "z01_nome", $sWhereRecHumano);
            $result = $clrechumano->sql_record($sSqlRecHumano);

            if ($clrechumano->numrows != 0) {
                db_fieldsmemory($result, 0);
                echo "<script>".$funcao_js."('$z01_nome',false);</script>";
            } else {
                echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
            }
        } else {
            echo "<script>".$funcao_js."('',false);</script>";
        }
    }
    ?>
</div>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
