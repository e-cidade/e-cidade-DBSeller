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

$iDesabilitaQuandoAlteracao = $db_opcao == 2 || $db_opcao == 22 ? 3 : $db_opcao;
$sDescricaoTela             = $db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir");
$cledu_relatmodel->rotulo->label();
?>
<form name="form1" method="post" action="">
  <fieldset >
    <legend><?php echo $sDescricaoTela?> Modelo de Relatório</legend>

    <table class='form-container'>

      <tr>
        <td nowrap='nowrap' class='field-size3' title="<?=@$Ted217_i_codigo?>">
           <?= $Led217_i_codigo?>
        </td>
        <td  nowrap='nowrap' >
          <?php db_input('ed217_i_codigo', 10, $Ied217_i_codigo, true, 'text', 3, "")?>
        </td>
      </tr>
      <tr>
        <td  nowrap='nowrap' class='field-size3' title="<?=@$Ted217_c_nome?>">
          <?= $Led217_c_nome?>
        </td>
        <td  nowrap='nowrap'>
          <?php db_input('ed217_c_nome', 20, $Ied217_c_nome, true, 'text', $db_opcao, "")?>
        </td>
      </tr>
      <tr>
        <td  nowrap='nowrap' class='field-size3' title="<?=@$Ted217_i_relatorio?>">
          <?= $Led217_i_relatorio?>
        </td>
        <td  nowrap='nowrap'>
          <?php
            $x = array("0"=>"SELECIONE",
                       "1"=>"HISTÓRICO ESCOLAR",
                       "2"=>"CERTIFICADO DE CONCLUSÃO",
                       "3"=>"ATA DE RESULTADOS FINAIS",
                       "4"=>"QUADRO DE RESULTADOS FINAIS",
                       "5"=>"ALUNOS VOTANTES",
                       "6"=>"RESPONSÁVEIS VOTANTES",
                       "7"=>"ATA DE PROGRESSÃO"
                      );
            db_select('ed217_i_relatorio', $x, true, $iDesabilitaQuandoAlteracao, "Onchange=js_remove();");
            ?>
        </td>
      </tr>
      <tr id="divTipoModelo" style="display:none">
        <td  nowrap='nowrap' class='field-size3' title="<?=@$Ted217_i_tipomodelo?>" >
          <?= $Led217_i_tipomodelo?>
        </td>
        <td  nowrap='nowrap' >
          <?php
            $aModelos = ["" => "SELECIONE", "1" => "Modelo 1", "2" => "Modelo 2"];
            db_select('ed217_i_tipomodelo', $aModelos, true, $db_opcao, "onChange='js_validaTipoModelo()'");
            ?>
        </td>
      </tr>
    </table>

    <table class='form-container'>
        <tr id="ctnOrientacao">
            <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_orientacao?>">
            <?=@$Led217_orientacao?>
            </td>
            <td nowrap='nowrap'  >
            <?php
                $xy = array("0"=>"SELECIONE",
                            "1"=>"PAISAGEM",
                            "2"=>"RETRATO"
                );
                db_select('ed217_orientacao', $xy, true, $db_opcao, "Onchange=js_orientacao();");
                ?>
            </td>
        </tr>
        <tr id="tipoBrasao" style="display:none">
            <td rel='ignore-css' nowrap='nowrap' class='field-size3'>
            <?= $Led217_brasao;?>
            </td>
            <td  nowrap='nowrap' >
            <?php
                $aTipoBrasao = array( 0 => "SELECIONE", 1 => "REPÚBLICA", 2 => "MUNICÍPIO" );
                db_select('ed217_brasao', $aTipoBrasao, true, $db_opcao);
            ?>
            </td>
        </tr>
    </table>

    <div id="div_orientacao" style="visibility:hidden; position:relative;">
      <table class="form-container">
        
        <tr id="ctnExibirTurma" style="display: none;">
          <td rel='ignore-css' nowrap='nowrap'  class='field-size3' title="<?=@$Ted217_exibeturma?>">
           <?= $Led217_exibeturma?>
          </td>
          <td  nowrap='nowrap'>
           <?php
             $xy = array("f"=>"NÃO", "t"=>"SIM");
             db_select('ed217_exibeturma', $xy, true, $db_opcao);
            ?>
         </td>
       </tr>

       <tr id="ctnExibeCargaHoraria" style="display: none;">
          <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibecargahoraria?>">
           <?=@$Led217_exibecargahoraria?>
          </td>
          <td nowrap='nowrap' >
            <?php
              $xy = array("f"=>"NÃO", "t"=>"SIM");
              db_select('ed217_exibecargahoraria', $xy, true, $db_opcao);
            ?>
          </td>
        </tr>
          <tr id="ctnExibeObsDiario" style="display: none;">
              <td  rel='ignore-css' nowrap='nowrap' class='field-size3'>
                  Exibe Observação Diário
              </td>
              <td nowrap='nowrap' >
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibe_obs_diario', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibeMantenedora">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibirmantenedora?>">
                  <?=@$Led217_exibirmantenedora?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibirmantenedora', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibeDistrito">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibirdistrito?>">
                  <?=@$Led217_exibirdistrito?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibirdistrito', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibePeriodo">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibirperiodo?>">
                  <?=@$Led217_exibirperiodo?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibirperiodo', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibeEtapaObs">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_etapa_obs?>">
                  <?=@$Led217_exibir_etapa_obs?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibir_etapa_obs', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibeCertidao">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibircertidao?>">
                  <?=@$Led217_exibircertidao?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibircertidao', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
          <tr id="ctnExibeIdentidade">
              <td rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibiridentidade?>">
                  <?=@$Led217_exibiridentidade?>
              </td>
              <td nowrap='nowrap'>
                  <?php
                    $xy = array("f"=>"NÃO", "t"=>"SIM");
                    db_select('ed217_exibiridentidade', $xy, true, $db_opcao);
                    ?>
              </td>
          </tr>
      </table>
    </div>
    <div id="div_orientacao2" style="visibility:hidden; position:relative;">
        <table class="form-container">
          <tr id="ctnExibeAssinaturaSecretario" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_assinatura_secretario?>">
            <?=@$Led217_exibir_assinatura_secretario?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_assinatura_secretario', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnExibeAssinaturaAdicional" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_assinatura_adicional?>">
            <?=@$Led217_exibir_assinatura_adicional?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_assinatura_adicional', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnExibeGradeAlunos" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_grade_alunos?>">
            <?=@$Led217_exibir_grade_alunos?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_grade_alunos', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnExibeColunaDisciplinas" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_coluna_disciplinas?>">
            <?=@$Led217_exibir_coluna_disciplinas?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_coluna_disciplinas', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnExibeColunaResultadoFinal" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_coluna_resultado_final?>">
            <?=@$Led217_exibir_coluna_resultado_final?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_coluna_resultado_final', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnExibeBrasao" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_exibir_brasao?>">
            <?=@$Led217_exibir_brasao?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("f"=>"NÃO", "t"=>"SIM");
                db_select('ed217_exibir_brasao', $xy, true, $db_opcao);
                ?>
            </td>
          </tr> 
          <tr id="ctnDisposicaoBrasao" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_disposicao_brasao?>">
            <?=@$Led217_disposicao_brasao?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("ACIMA"=>"ACIMA DO TEXTO", "LADO"=>"AO LADO DO TEXTO");
                db_select('ed217_disposicao_brasao', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>
          <tr id="ctnDisposicaoCabecalho" style="display: none;">
            <td  rel='ignore-css' nowrap='nowrap' class='field-size3' title="<?=@$Ted217_disposicao_cabecalho?>">
            <?=@$Led217_disposicao_cabecalho?>
            </td>
            <td nowrap='nowrap' >
                <?php
                $xy = array("ESQUERDA"=>"ESQUERDA", "DIREITA"=>"DIREITA", "CENTRALIZADO" => "CENTRALIZADO");
                db_select('ed217_disposicao_cabecalho', $xy, true, $db_opcao);
                ?>
            </td>
          </tr>         
      </table>
    </div>

    <fieldset class='separator' id="cabecalhoPadrao">
      <legend>Texto do Cabeçalho</legend>
      <label>(Máximo cinco linhas)</label></br>
      <?php
        db_textarea(
            'ed217_t_cabecalho',
            5,
            100,
            $Ied217_t_cabecalho,
            true,
            'text',
            $db_opcao,
            "onkeypress='return limitTextArea(this, event)'",
            "",
            "",
            200
        );
        ?>
    </fieldset>

    <fieldset class='separator' id="cabecalhoAta">
      <legend>Texto do Cabeçalho</legend>
      <label>(Máximo cinco linhas)</label></br>
      <?php
        db_textarea(
            'ed217_t_cabecalho',
            5,
            100,
            $Ied217_t_cabecalho,
            true,
            'text',
            $db_opcao,
            "onkeypress='return limitTextArea(this, event)'",
            "",
            "",
            400
        );
        ?>
    </fieldset>


    <fieldset class='separator'>
      <legend>Texto do Rodapé</legend>
      <?php db_textarea('ed217_t_rodape', 5, 100, $Ied217_t_rodape, true, 'text', $db_opcao, "", "", "", 500); ?>
    </fieldset>

    <fieldset class='separator'>

      <legend>Observações Gerais a Todos os Alunos</legend>
      <?php db_textarea('ed217_t_obs', 4, 100, $Ied217_t_obs, true, 'text', $db_opcao, "", "", ""); ?>
      <br>
    </fieldset>

    <div id='div_tamfontes' class="subcontainer" style="visibility:hidden;position:absolute; width:100%;">
      <fieldset class='separator' style=''>
        <legend>Tamanho das fontes</legend>
        <table class='form-container'>
          <tr>
            <td class='field-size3' nowrap='nowrap' title="<?=@$Ted217_gradenotas?>">
              <?= $Led217_gradenotas?>
            </td>
            <td  nowrap='nowrap'>
              <?php
                $x = array("0"=>"", "1"=>"6", "2"=>"8" );
                db_select('ed217_gradenotas', $x, true, $db_opcao, "");
                ?>
            </td>
          </tr>
          <tr>
            <td class='field-size3'  nowrap='nowrap' title="<?=@$Ted217_gradeetapas?>">
              <?=@$Led217_gradeetapas?>
            </td>
            <td  nowrap='nowrap'>
              <?php
                $x = array("0"=>"", "1"=>"6", "2"=>"8" );
                db_select('ed217_gradeetapas', $x, true, $db_opcao, "");
                ?>
            </td>
          </tr>
          <tr>
            <td class='field-size3' nowrap='nowrap' title="<?=@$Ted217_observacao?>">
              <?=@$Led217_observacao?>
            </td>
            <td  nowrap='nowrap'>
              <?php
                $x = array("0"=>"", "1"=>"6", "2"=>"8" );
                db_select('ed217_observacao', $x, true, $db_opcao, "");
                ?>
            </td>
          </tr>
        </table>
      </fieldset>
    </div>
  </fieldset>
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
         type="submit"
         id="db_opcao"
         value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
                <?=($db_botao==false?"disabled":"")?>
         onclick="return js_validaSubmit();">
  <input name="pesquisar"
         type="button"
         id="pesquisar"
         value="Pesquisar"
         onclick="js_pesquisa();" >
  <input name="novo"
         type="button"
         id="novo"
         value="Novo Registro"
         onclick="js_novo()" <?=$db_opcao==1?"disabled":""?>>

</form>

<script  type="text/javascript">

var MSG_MODELOS_RELATORIO = 'educacao.secretariaeducacao.db_frmedu_relatmodel.';
const cabecalho_elemento = document.querySelector('#ed217_t_cabecalho');

js_init();
function js_init() {

  js_remove();
  js_orientacao();
}
function js_pesquisa() {
  js_OpenJanelaIframe('CurrentWindow.corpo',
                      'db_iframe_edu_relatmodel',
                      'func_edu_relatmodel.php?funcao_js=parent.js_preenchepesquisa|ed217_i_codigo',
                      'Pesquisa',
                      true
                     );
}

function js_preenchepesquisa(chave) {
  db_iframe_edu_relatmodel.hide();
  <?php
    if ($db_opcao != 1) {
        echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
    }
    ?>
}
function js_novo() {
  location.href="edu1_relatmodel001.php";
}

function js_remove() {

  $('ed217_t_cabecalho').disabled  = false;
  $('divTipoModelo').style.display = 'none';

  if (document.form1.ed217_i_relatorio.value == 3) {

    document.form1.ed217_t_rodape.disabled = true;
    document.form1.ed217_t_obs.disabled    = false;
  } else if (document.form1.ed217_i_relatorio.value == 4) {

    document.form1.ed217_t_rodape.disabled = true;
    document.form1.ed217_t_obs.disabled    = true;
    $('ed217_i_tipomodelo').value          = '';
  } else {

    document.form1.ed217_t_rodape.disabled = false;
    document.form1.ed217_t_obs.disabled    = false;
    $('ed217_i_tipomodelo').value          = '';
  }
  document.getElementById("div_orientacao2").style.display = "none";
    document.getElementById("div_orientacao2").style.position = "relative";

  if (document.form1.ed217_i_relatorio.value == 1 || document.form1.ed217_i_relatorio.value == 2) {

    ajusta_limite_caracteres_outros_modelos()
    js_oculta_containers_ata();

    document.getElementById("div_orientacao").style.visibility = "visible";
    document.getElementById("div_orientacao").style.position = "relative";
    $('ctnExibirTurma').style.display        = "table-row";
    $('ctnExibeCargaHoraria').style.display  = "table-row";
    $('ctnExibeObsDiario').style.display  = "table-row";
    $('tipoBrasao').style.display            = "table-row";

    
    $('ctnOrientacao').style.display = "table-row";

  } else if ($('ed217_i_relatorio').value == 3) {

    $('divTipoModelo').style.display  = 'table-row';
    document.getElementById("div_orientacao").style.visibility = "hidden";
    document.getElementById("div_orientacao").style.position = "absolute";

    ajusta_limite_caracteres_outros_modelos();
    js_oculta_containers_ata();
    $('ctnOrientacao').style.display = "none";
    $('tipoBrasao').style.display = "none";

  } else if ($('ed217_i_relatorio').value == 7) {

    document.getElementById("div_orientacao2").style.visibility = "visible";
    document.getElementById("div_orientacao2").style.display = "block";
    document.getElementById("div_orientacao2").style.position = "relative";

    document.getElementById("div_orientacao").style.visibility = "hidden";
    document.getElementById("div_orientacao").style.position = "absolute";
    js_mostra_containers_ata();
    $('ctnOrientacao').style.display = "none";
    $('tipoBrasao').style.display = "contents";
    ajusta_limite_caracteres_ata();

  } else {

    document.getElementById("div_orientacao").style.visibility = "hidden";
    document.getElementById("div_orientacao").style.position = "absolute";
    document.form1.ed217_orientacao.value = "0";
    $('ctnOrientacao').style.display = "none";
    $('tipoBrasao').style.display = "none";
    ajusta_limite_caracteres_outros_modelos();
    js_oculta_containers_ata();
  }

  js_orientacao();
  js_validaTipoModelo();
}

function js_oculta_containers_ata() 
{
    const containersOcultos = [
        'ctnExibeAssinaturaSecretario', 'ctnExibeAssinaturaAdicional', 'ctnExibeGradeAlunos', 'ctnExibeColunaDisciplinas', 
        'ctnExibeColunaResultadoFinal', 'ctnExibeBrasao', 'ctnDisposicaoBrasao', 'ctnDisposicaoCabecalho'
    ];
    
  containersOcultos.map(function(elemento){
    document.getElementById(`${elemento}`).style.display = "none";
  });
}

function js_mostra_containers_ata() 
{
    const containersExibidos = [
        'ctnExibeAssinaturaSecretario', 'ctnExibeAssinaturaAdicional', 'ctnExibeGradeAlunos', 'ctnExibeColunaDisciplinas', 
        'ctnExibeColunaResultadoFinal', 'ctnExibeBrasao', 'ctnDisposicaoBrasao', 'ctnDisposicaoCabecalho'
    ];
    containersExibidos.map(function(elemento){
        $(`${elemento}`).style.display  = "block";
        $(`${elemento}`).style.display = "table-row";
    });
}

function ajusta_limite_caracteres_ata()
{
  const textoCabecalhoPadrao = document.querySelector('#cabecalhoPadrao').children[3].value;
  $('cabecalhoPadrao').style.display = "none";
  $('cabecalhoAta').style.display = "table-row";
  if (textoCabecalhoPadrao) {
    document.querySelector('#cabecalhoAta').children[3].value = textoCabecalhoPadrao;
  }
  cabecalho_elemento.nextElementSibling.nextElementSibling.children[3].textContent = "- Limite 400";
}

function ajusta_limite_caracteres_outros_modelos()
{
  const textoCabecalhoAta = document.querySelector('#cabecalhoAta').children[3].value;
  $('cabecalhoAta').style.display = "none";
  $('cabecalhoPadrao').style.display = "table-row";
  if (textoCabecalhoAta) {
    document.querySelector('#cabecalhoPadrao').children[3].value = textoCabecalhoAta;
  }
  cabecalho_elemento.nextElementSibling.nextElementSibling.children[3].textContent = "- Limite 200";
}

function js_orientacao() {

  if ( [1,2].in_array( $F('ed217_orientacao') ) ) {
    $('ed217_orientacao').options[0].setAttribute('disabled', 'disabled');
  }

  /**
   * Sempre que trocar orientação de retrato para paisagem, devemos se observação ultrapassou o limite de 500 caracteres
   */
  if ($F('ed217_orientacao') == 1)  {

    if ($F('ed217_t_obs').length > 500) {
      var sMsgConfirm  = _M( MSG_MODELOS_RELATORIO + "confirma_troca_orientacao");
      if (confirm(sMsgConfirm)) {

        $('ed217_t_obs').value       = $F('ed217_t_obs').substr(0, 500);
      } else {
        $('ed217_orientacao').value = 2;
        return;
      }
    }

  }

  if (document.form1.ed217_orientacao.value == 2) {

    document.getElementById("div_tamfontes").style.visibility = "visible";
    document.getElementById("div_tamfontes").style.position   = "relative";
    document.form1.ed217_t_rodape.disabled = true;

    if ( [1,2].in_array($F('ed217_i_relatorio')) ) {
      js_removeLimitacaoCaracter();
    }

  } else {

    document.getElementById("div_tamfontes").style.visibility = "hidden";
    document.getElementById("div_tamfontes").style.position = "absolute";
    document.form1.ed217_gradenotas.value  = "0";
    document.form1.ed217_gradeetapas.value = "0";
    document.form1.ed217_observacao.value  = "0";
    document.form1.ed217_t_rodape.disabled = false;
    js_validaTipoModelo();
  }

}

function limitTextArea(text, event) {

  var str         = text.value;
  var newStr      = "";
  var linhas      = new Array();
  var replaceLine = false;
  var aLinhas     = str.split("\n");
  var cont        = linhas.length;

  if (event.keyCode == 8 || event.keyCode == 16 || event.keyCode  == 20 ||
      event.keyCode == 18 || event.keyCode == 46) {
    return true;
  }

  /**
   * Verificamos se o tipo de relatorio eh Ata - Modelo 2, e se este atingiu o limite permitido de ate 60 caracteres
   */
  if ($('ed217_i_relatorio').value == 3 && $('ed217_i_tipomodelo').value == 2 && str.length > 59) {


    alert(_M( MSG_MODELOS_RELATORIO + "ata_resultados_finais_modelo2"));
    return false;
  }

  if (aLinhas.length > 5) {
    return false;
  }
  return true;
}

function js_validaSubmit() {

  if ( document.form1.ed217_orientacao.value == 2 && (document.form1.ed217_gradenotas.value == 0 || document.form1.ed217_gradeetapas.value == 0 || document.form1.ed217_observacao.value == 0) ) {

    alert(_M( MSG_MODELOS_RELATORIO + "campos_tamanhos_fontes"));
    return false;
  }

  if ($('ed217_i_relatorio').value == '3' && $('ed217_i_tipomodelo').value == '') {

    alert(_M( MSG_MODELOS_RELATORIO + "informe_tipo_modelo"));
    return false;
  }
  return true;
}

/**
 * Verificamos o tipo de modelo selecionado
 * Caso 1: Limpamos o conteudo e bloqueamos o campo ed217_t_cabecalho
 */
function js_validaTipoModelo() {

  $('ed217_t_cabecalho').disabled = false;

  if ($('ed217_i_tipomodelo').value == 1) {

    $('ed217_t_cabecalho').value    = '';
    $('ed217_t_cabecalho').disabled = true;
  }
}

function js_removeLimitacaoCaracter (argument) {
  $('ed217_t_obs').stopObserving('keyup');
  // $('ed217_t_obs').removeAttribute('onkeyup');
  $('ed217_t_obs').removeAttribute('keyup');


}

</script>
