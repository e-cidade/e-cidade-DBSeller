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


require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("classes/db_cgs_und_ext_classe.php");

db_postmemory($_POST);

if (!isset($pesquisar) && isset($alterar_cgs)) {

    parse_str($_SERVER["QUERY_STRING"], $queryString);
    ?>
  <script>
    location.href = "sau1_cgs_und002.php?chavepesquisa=<?=$chave_z01_i_cgsund?>";
  </script>
    <?php
}

$clcgs_und = new cl_cgs_und_ext;
$clrotulo = new rotulocampo;
$clcgs_und->rotulo->label("z01_i_cgsund");
$clcgs_und->rotulo->label("z01_v_nome");
$clcgs_und->rotulo->label("z01_v_cgccpf");
$clcgs_und->rotulo->label("z01_v_ident");
$clrotulo->label("DBtxt30");
$clrotulo->label("DBtxt31");
$clrotulo->label("s115_c_cartaosus");

$aFuncaoParent = explode('|', $funcao_js);
$funcaoParent = $aFuncaoParent[0];

unset($aFuncaoParent[0]);
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <script>

    team = [];
    <?php

    /** Seleciona todos os calendários */
    $sql1 = "SELECT sd34_i_codigo,sd34_v_descricao ";
    $sql1 .= "  FROM microarea ";
    $sql1 .= " ORDER BY sd34_v_descricao";

    $sql_result = db_query($sql1);
    $num = pg_num_rows($sql_result);
    $conta = 0;
    $aArrayPai = array();

    while ($row = pg_fetch_array($sql_result)) {
        $conta = $conta + 1;
        $cod_micro = $row["sd34_i_codigo"];
        $aArrayFilho = array();

        $sub_sql = "SELECT sd35_i_codigo,sd33_v_descricao ";
        $sub_sql .= "  FROM familiamicroarea ";
        $sub_sql .= "       inner join familia on sd33_i_codigo = sd35_i_familia ";
        $sub_sql .= " WHERE sd35_i_microarea = '{$cod_micro}' ";
        $sub_sql .= " ORDER BY sd33_v_descricao ";

        $sub_result = db_query($sub_sql);
        $num_sub = pg_num_rows($sub_result);

        if ($num_sub >= 1) {
            $aArrayFilho[] = array('', '');
            $conta_sub = 0;

            while ($rowx = pg_fetch_array($sub_result)) {
                $codigo_fam = $rowx["sd35_i_codigo"];
                $nome_fam = $rowx["sd33_v_descricao"];
                $conta_sub = $conta_sub + 1;

                if ($conta_sub == $num_sub) {
                    $aArrayFilho[] = array(urlencode($nome_fam), $codigo_fam);
                    $conta_sub = "";
                } else {
                    $aArrayFilho[] = array(urlencode($nome_fam), $codigo_fam);
                }
            }
        } else {
            $aArrayFilho[] = array("Microarea sem famílias cadastradas.", '');
        }
        $aArrayPai[] = $aArrayFilho;
    }

    $sArrayJson = JSON::create()->stringify($aArrayPai);
    ?>
    team = <?php echo $sArrayJson; ?>;

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

        selectCtrl.options[0] = new Option('', '');
        j = 0;
      } else {
        selectCtrl.options[0] = new Option(prompt);
        j = 1;
      }

      if(itemArray != null) {
        // add new items
        for(i = 0; i < itemArray.length; i++) {
          selectCtrl.options[j] = new Option(itemArray[i][0].urlDecode());
          if(itemArray[i][1] != null) {
            selectCtrl.options[j].value = itemArray[i][1];
          }
          <?php if(isset($chave_z01_i_familiamicroarea) && $chave_z01_i_familiamicroarea != ""){?>
          if(<?php echo $chave_z01_i_familiamicroarea;?>==itemArray[i][1])
          {
            indice = i;
          }
            <?php } ?>
          j++;
        }
          <?php if(isset($chave_z01_i_familiamicroarea) && $chave_z01_i_familiamicroarea != ""){?>
        selectCtrl.options[indice].selected = true;
          <?php } else { ?>
        selectCtrl.options[0].selected = true;
          <?php } ?>
      }
    }

    function validaCgs(iCgs, parametros) {
      if (iCgs == '') {
        funcaoAnterior.apply(null, parametros);
        return;
      }

      var oParametros = {'sExecucao': 'validarCGS', 'cgs': iCgs, 'asynchronous': false};

      AjaxRequest.create('sau4_cgs.RPC.php', oParametros, function(oRetorno, lErro) {

        if(lErro) {

          alert(oRetorno.sMessage);
          return;
        }

        if(oRetorno.valido === false
          && confirm('O CGS está desatualizado. Gostaria de atualizá-lo?')) {

          manuntencaoCgs(iCgs, parametros);
          return;
        }

        funcaoAnterior.apply(null, parametros);
      }).setMessage('Aguarde, validando CGS...')
        .execute();
    }
  </script>
</head>
<body>
<form name="form2" method="post" action="" class="container">
  <fieldset>
    <legend>Filtros da Pesquisa</legend>
    <table class="form-container">
      <tr>
        <td>
          <label for="chave_z01_i_cgsund">CGS:</label>
        </td>
        <td colspan="3">
            <?php
            db_input('z01_i_cgsund', 10, $Iz01_i_cgsund, true, 'text', 4, "", "chave_z01_i_cgsund", null, null, 15);
            ?>
        </td>
      </tr>
      <tr>
        <td>
          <label for="chave_z01_v_nome">Nome:</label>
        </td>
        <td colspan="3">
            <?php
            db_input('z01_v_nome', 30, $Iz01_v_nome, true, 'text', 4, "class='field-size-max'", 'chave_z01_v_nome');
            ?>
        </td>
      </tr>
      <tr>
        <td>
          <label for="chave_z01_d_nasc">Data de Nascimento:</label>
        </td>
        <td colspan="3">
            <?php
            $z01_d_nasc_dia = !empty($chave_z01_d_nasc_dia) ? $chave_z01_d_nasc_dia : "";
            $z01_d_nasc_mes = !empty($chave_z01_d_nasc_mes) ? $chave_z01_d_nasc_mes : "";
            $z01_d_nasc_ano = !empty($chave_z01_d_nasc_ano) ? $chave_z01_d_nasc_ano : "";
            db_inputdata('z01_d_nasc', $z01_d_nasc_dia, $z01_d_nasc_mes, $z01_d_nasc_ano, true, 'text', 4, "",
            'chave_z01_d_nasc'); ?>
            Ex: <?= date('d/m/Y'); ?>
            <!-- plugin cadweb - operation 6 -->
        </td>
      </tr>
      <tr>
        <td>
          <label for="chave_z01_v_cgccpf">CPF:</label>
        </td>
        <td>
            <?php
            db_input('z01_v_cgccpf', 15, $Iz01_v_cgccpf, true, 'text', 1, "class='field-size-max'", "chave_z01_v_cgccpf");
            ?>
        </td>
        <td>
          <label for="chave_s115_c_cartaosus">Cartão SUS:</label>
        </td>
        <td>
            <?php
            db_input('s115_c_cartaosus', 15, $Is115_c_cartaosus, true, 'text', 4, "class='field-size-max'",'chave_s115_c_cartaosus');
            ?>
        </td>
      </tr>
      <tr>
        <td>
          <label for="chave_z01_v_micro">Microárea:</label>
        </td>
        <td>
          <?php
            $sql1 = "SELECT sd34_i_codigo,sd34_v_descricao FROM microarea ORDER BY sd34_v_descricao";
            $sql_result = db_query($sql1);
            $options = [
              '0' => '',
              '-1' => 'Sem microárea'
            ];
            while ($row = pg_fetch_array($sql_result)) {
                $cod_micro = $row["sd34_i_codigo"];
                $desc_micro = $row["sd34_v_descricao"];
                $options[$cod_micro] = $desc_micro;
            }

            $itemArray = "((this.selectedIndex <= 1) ? null : team[this.selectedIndex-2])";
            $onChange = "onChange='fillSelectFromArray(this.form.chave_z01_i_familiamicroarea, {$itemArray});'";
            db_select('chave_z01_v_micro', $options, true, 1, $onChange);
          ?>
        </td>
        <td>
          <label for="chave_z01_i_familiamicroarea">Família:</label>
        </td>
        <td>
          <select id="chave_z01_i_familiamicroarea"
                  name="chave_z01_i_familiamicroarea"
                  style="font-size:9px;width:200px;height:18px;"
                  onchange="if(this.value=='')document.form2.chave_z01_v_micro.value='';">
            <option value=""></option>
          </select>
            <?php
            if ((isset($chave_z01_i_familiamicroarea) && $chave_z01_i_familiamicroarea != "") || (isset($chave_z01_v_micro) && !in_array($chave_z01_v_micro, ['', '-1']))) {
                ?>
              <script>fillSelectFromArray(document.form2.chave_z01_i_familiamicroarea, team[document.form2.chave_z01_v_micro.selectedIndex - 2]);</script>
                <?php
            }
            ?>
        </td>
      </tr>
      <tr>
        <td>
          <label for="chave_z01_b_inativo">Cadastro Inativo: </label>
        </td>
        <td>
          <?php
            $options = [
              '0' => '',
              '1' => 'NÃO',
              '2' => 'SIM'
            ];
            db_select('chave_z01_b_inativo', $options, true, 1);
          ?>
        </td>
        <td>
          <label for="chave_mostra_obito">Mostrar óbitos: </label>
        </td>
        <td>
          <?php
            db_select('chave_z01_b_faleceu', $options, true, 1);
          ?>
        </td>
      </tr>
    </table>
  </fieldset>
  <input name="pesquisar2" type="submit" id="pesquisar2" value="Pesquisar">
  <input name="limpar" type="button" id="limpar" value="Limpar" onClick="js_limpar();">
  <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="js_fechar('<?= @$campoFoco ?>');">
    <?php
    $disabled = "disabled";

    if (isset($retornacgs) || !empty($redireciona)) {
        $disabled = "";
    } else {
        if (!isset($retornacgs) && empty($redireciona)) {
            $disabled = "";
        }
    }

    if (!isset($lDesabilitaCgs)) {
        ?>
      <input id="manutencaoCgs" type="button" value="Manutenção CGS"
             onclick="manuntencaoCgs($F('chave_z01_i_cgsund'));"/>
        <?php
    }
    ?>
</form>

<?php
if (isset($lValidaCGS)) {
    echo <<<HTML
      <script>
        var js_validaCGS = function() {
          validaCgs(arguments[0], arguments);
        };
        var funcaoAnterior = {$funcaoParent};
      </script>
HTML;

    $funcao_js = "js_validaCGS|" . implode("|", $aFuncaoParent);
}

if (!isset($pesquisa_chave)) {
    if (isset($campos) == false) {
      if (file_exists("funcoes/db_func_cgs_und_ext.php") == true) {
        include(modification("funcoes/db_func_cgs_und_ext.php"));
      } else {
        $campos = [];
        $campos[] = "cgs_und.z01_i_cgsund";
        $campos[] = "z01_v_nome";
        $campos[] = "z01_v_cgccpf";
        $campos[] = "(
          case when s115_c_cartaosus is not null
            then s115_c_cartaosus
              else (
                select s115_c_cartaosus
                from cgs_cartaosus as cartaop
                where cartaop.s115_i_cgs = cgs_und.z01_i_cgsund
                  and s115_c_tipo = 'P'
                order by s115_i_codigo desc
                limit 1
              )
            end ) as s115_c_cartaosus";
        $campos[] = "z01_d_nasc";
        $campos[] = "z01_v_sexo";
        $campos[] = "z01_c_raca";
        $campos[] = "z01_v_ender";
        $campos[] = "z01_i_numero";
        $campos[] = "z01_v_bairro";
        $campos[] = "z01_v_munic";
        $campos[] = "z01_v_telcel";
        $campos[] = "z01_v_ident as DB_z01_v_ident";
        $campos[] = "z01_v_mae";
        $campos[] = "z01_nome_social";
        //plugin ESF operation#0 - adicionando novo campo psf_nome_equipe ao select da query
        $campos[] = "sd34_v_descricao as dl_Microarea";
        $campos[] = "sd33_v_descricao as dl_Familia";
        $campos[] = "
            case
              when z01_b_inativo is true
              then z01_b_inativo
              else false
            end as DL_Inativo
        ";
        $campos = implode(', ', $campos);
      }
    }
    $where = [];
    if (isset($chave_z01_v_nome) && (trim($chave_z01_v_nome) != "")) {
      $where[] = "to_ascii(z01_v_nome) like to_ascii('{$chave_z01_v_nome}%')";
    }
    if (isset($chave_z01_v_cgccpf) && (trim($chave_z01_v_cgccpf) != "")) {
      $where[] = "z01_v_cgccpf like '{$chave_z01_v_cgccpf}%'";
    }
    if (isset($chave_z01_d_nasc) && (trim($chave_z01_d_nasc) != "")){
      $where[] = "z01_d_nasc = '{$chave_z01_d_nasc}'";
    }
    if (isset($chave_s115_c_cartaosus) && (trim($chave_s115_c_cartaosus) != "")) {
      $where[] = "s115_c_cartaosus = '{$chave_s115_c_cartaosus}'";
    }
    if (isset($chave_z01_v_micro) && $chave_z01_v_micro == '-1') {
      $where[] = "familiamicroarea.sd35_i_microarea is NULL";
    } else if (isset($chave_z01_v_micro) && (trim($chave_z01_v_micro) != "0")) {
      $where[] = "familiamicroarea.sd35_i_microarea = {$chave_z01_v_micro}";
    }
    if (isset($chave_z01_i_familiamicroarea) && (trim($chave_z01_i_familiamicroarea) != "")) {
      $where[] = "z01_i_familiamicroarea = '{$chave_z01_i_familiamicroarea}'";
    }
    if (isset($chave_z01_b_faleceu) && trim($chave_z01_b_faleceu) == '1') {
      $where[] = "z01_b_faleceu is not true";
    } else if (isset($chave_z01_b_faleceu) && trim($chave_z01_b_faleceu) == '2') {
      $where[] = "z01_b_faleceu is true";
    }
    if (isset($chave_z01_b_inativo) && trim($chave_z01_b_inativo) == '1') {
      $where[] = 'z01_b_inativo is not true';
    } else if (isset($chave_z01_b_inativo) && trim($chave_z01_b_inativo) == '2') {
      $where[] = 'z01_b_inativo is true';
    }

    $where = implode(' AND ', $where);

    if (!isset($chave_profissional) || empty($chave_profissional) || !isset($chave_unidade) || empty($chave_unidade)) {
      $chave = "";
      if (isset($chave_z01_i_cgsund) && (trim($chave_z01_i_cgsund) != "")) {
          $chave = $chave_z01_i_cgsund;
      }
      $sql = $clcgs_und->sql_query_ext($chave, $campos, "z01_v_nome", $where);
    } else { // Traz todos os CGSs que sao pacientes do profissional indicado na variavel $chave_profissional
      $chave = "";
      if (isset($chave_z01_i_cgsund) && (trim($chave_z01_i_cgsund) != "")) {
        $chave = $chave_z01_i_cgsund;
      }
      $sql = $clcgs_und->sql_query_cgs_profissional(
        $chave,
        $chave_profissional,
        $chave_unidade,
        $campos,
        "z01_v_nome",
        "{$where}"
      );
    }
    if (isset($nao_mostra)) {
        $sSep = '';
        $aFuncao = explode('|', $funcao_js);
        $rs = $clcgs_und->sql_record($sql);

        if ($clcgs_und->numrows == 0) {
            echo '<script>' . $aFuncao[0] . "('','Chave(" . $chave_z01_i_cgsund . ") não Encontrado');</script>";
        } else {
            db_fieldsmemory($rs, 0);
            if ($dl_inativo == 't' && !isset($aceitaInativo)) {
              echo "<script>{$aFuncao[0]}('','CGS Inativo');</script>";
              exit;
            }
            $sFuncao = $aFuncao[0] . '(';
            for ($iCont = 1; $iCont < count($aFuncao); $iCont++) {
                $sFuncao .= $sSep . '"' . eval('return @$' . $aFuncao[$iCont] . ';') . '"';
                $sSep = ', ';
            }

            $sFuncao = substr($sFuncao, 0, strlen($sFuncao));
            $sFuncao .= ');';
            echo "<script>" . $sFuncao . '</script>';
        }
    }

    $repassa = array(
      "chave_z01_i_cgsund"           => isset($chave_z01_i_cgsund) ? $chave_z01_i_cgsund : '',
      "chave_z01_v_nome"             => !empty($chave_z01_v_nome) ? $chave_z01_v_nome : '',
      "chave_z01_v_cgccpf"           => !empty($chave_z01_v_cgccpf) ? $chave_z01_v_cgccpf : '',
      "chave_z01_d_nasc"             => !empty($chave_z01_d_nasc) ? $chave_z01_d_nasc : '',
      "chave_z01_d_nasc_dia"         => !empty($chave_z01_d_nasc_dia) ? $chave_z01_d_nasc_dia : '',
      "chave_z01_d_nasc_mes"         => !empty($chave_z01_d_nasc_mes) ? $chave_z01_d_nasc_mes : '',
      "chave_z01_d_nasc_ano"         => !empty($chave_z01_d_nasc_ano) ? $chave_z01_d_nasc_ano : '',
      "chave_s115_c_cartaosus"       => !empty($chave_s115_c_cartaosus) ? $chave_s115_c_cartaosus : '',
      "chave_z01_i_familiamicroarea" => !empty($chave_z01_i_familiamicroarea) ? $chave_z01_i_familiamicroarea : '',
      "chave_z01_v_micro"            => !empty($chave_z01_v_micro) ? $chave_z01_v_micro : '',
      "chave_z01_b_inativo"          => isset($chave_z01_b_inativo) ? $chave_z01_b_inativo : '',
      "chave_z01_b_faleceu"          => isset($chave_z01_b_faleceu) ? $chave_z01_b_faleceu : ''
    );

    if (isset($sql)) {
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
        db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa, false);
        echo '  </fieldset>';
        echo '</div>';
    }
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        if (!isset($chave_profissional) || empty($chave_profissional) || !isset($chave_unidade) || empty($chave_unidade)) {
            $campos = [];
            $campos[] = "(
              case
                when s115_c_cartaosus is not null
                  then s115_c_cartaosus
                else (
                  select s115_c_cartaosus
                  from cgs_cartaosus as cartaop
                  where cartaop.s115_i_cgs = cgs_und.z01_i_cgsund
                    and s115_c_tipo = 'P'
                  order by s115_i_codigo desc
                  limit 1
                )
              end ) as s115_c_cartaosus";
            $campos[] = "
                case
                  when z01_b_inativo is true
                  then z01_b_inativo
                  else false
                end as inativo
            ";
            $campos[] = "*";
            $campos = implode(', ', $campos);
            $sql = $clcgs_und->sql_query_ext($pesquisa_chave, $campos);
            $result = $clcgs_und->sql_record($sql);
          } else {
            $sql = $clcgs_und->sql_query_cgs_profissional($pesquisa_chave,$chave_profissional,$chave_unidade);
            $clcgs_und->sql_record($sql);
        }

        if ($clcgs_und->numrows != 0) {
            db_fieldsmemory($result, 0);
            if ($inativo == 't' && !isset($aceitaInativo)) {
              echo "<script>{$funcao_js}('CGS Inativo', true);</script>";
              exit;
            }
            if(isset($mostrarNomeSocial)){
              echo "<script>{$funcao_js}('$z01_v_nome',false,'$z01_v_sexo','$z01_v_telcel', '$s115_c_cartaosus', '$z01_d_nasc','$z01_nome_social', '$z01_v_cgccpf');</script>";
            } else {
              echo "<script>{$funcao_js}('$z01_v_nome',false,'$z01_v_sexo','$z01_v_telcel', '$s115_c_cartaosus', '$z01_d_nasc', '$z01_v_cgccpf');</script>";
            }
        } else {
            echo "<script>{$funcao_js}('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>{$funcao_js}('',false);</script>";
    }
    exit;
}
?>
</body>
</html>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script>
  const validaCgsClass = new ValidaCgs();
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);

  window.onload = () => {
    validaCgsClass.getParametros().then(response => {
      let verificarMicroarea = false;
      if(response.s103_validamicroarea) {
        verificarMicroarea = true;
      }
      mapeiaLovrot(verificarMicroarea);
    });

    document.body.addEventListener('keydown', function(event){
      if(event.which == 13){
        document.form2.pesquisar2.click();
      }
    });
  };

  async function mapeiaLovrot(verificarMicroarea) {
    js_divCarregando('Aguarde... Buscando dados adicionais do CGS!', 'busca_cgs');
    const table = document.getElementById('TabDbLov');
    const linhasLovrot = table.querySelectorAll('tr');

    const validaMicroArea = (cadastrado, tr)=> {
      if (!cadastrado) {
        let colunas = tr.querySelectorAll('td');
        colunas.forEach(td => {
          td.bgColor = '';
        });
        tr.addClassName('alert-danger');
      }
    };

    const eventCgsInativo = event => {
      event.preventDefault;
      alert('CGS Inativo!');
      return false;
    };

    const validaCgsInavito = (inativo, tr) => {
      if (inativo) {
        let colunas = tr.querySelectorAll('td');
        colunas.forEach(td => {
          td.firstChild.onclick = null;
          td.addEventListener('click', eventCgsInativo);
        });
      }
    }

    for (const tr of linhasLovrot) {
      let cgs = tr.children[0].childNodes[0].innerHTML;
      if (cgs != undefined) {
        if (verificarMicroarea) {
          let response = await validaCgsClass.isCadastradoMicroarea(cgs);
          validaMicroArea(response, tr);
        }

        if (urlParams.get('aceitaInativo') === null) {
          let response = await validaCgsClass.isInativo(cgs);
          validaCgsInavito(response, tr);
        }
      }
    }

    js_removeObj('busca_cgs');
  };

  /**
   * Botoão Fechar
   * campoFoco = foco de retorno quando fechar
   */
  function js_fechar(campoFoco) {

    if(campoFoco != undefined && campoFoco != '') {

      eval("parent.document.getElementById('" + campoFoco + "').focus(); ");
      eval("parent.document.getElementById('" + campoFoco + "').select(); ");
    }
    parent.db_iframe_cgs_und.hide();
  }

  function js_limpar() {

    document.form2.chave_z01_v_nome.value = "";
    document.form2.chave_z01_i_cgsund.value = "";
    document.form2.chave_z01_v_cgccpf.value = "";
    document.form2.chave_z01_d_nasc.value = "";
    document.form2.chave_z01_d_nasc_dia.value = "";
    document.form2.chave_z01_d_nasc_mes.value = "";
    document.form2.chave_z01_d_nasc_ano.value = "";
    document.form2.chave_s115_c_cartaosus.value = "";
    document.form2.chave_z01_v_micro.value = "";
    document.form2.chave_z01_i_familiamicroarea.value = "";
    document.form2.pesquisar2.click();

  }

  document.form2.chave_z01_v_nome.focus();

  function manuntencaoCgs(iCgs, parametros) {
    var sUrl = 'sau1_manutencaocgs001.php?lBloqueiaBotoes&lBloqueiaMenu';
    sUrl += iCgs != '' ? '&cgs=' + iCgs : '';

    var janela = js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cgs', sUrl, 'Manutenção de CGS', true, 0, 0);
    janela.moldura.style.zIndex = 1500;
    janela.setLargura("calc(100% - 25px)");
    janela.setAltura("calc(100% - 25px)");
    janela.janFrame.contentDocument.forms[0].addEventListener("submit", function() {

      janela.hide();
      funcaoAnterior.apply(null, parametros);
    });
  }

  (function() {
    var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();


</script>
