<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
use App\Domain\Configuracao\Helpers\StorageHelper;

//MODULO: educação
$clAlunoNecessidade->rotulo->label();
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
if (isset($opcao) && $opcao=="alterar") {
    $db_opcao = 2;
    $db_botao1 = true;
    $ed214_d_data_dia = substr($ed214_d_data, 0, 2);
    $ed214_d_data_mes = substr($ed214_d_data, 3, 2);
    $ed214_d_data_ano = substr($ed214_d_data, 6, 4);
} elseif (isset($opcao) && $opcao=="excluir" || isset($db_opcao) && $db_opcao==3) {
    $db_botao1 = true;
    $db_opcao = 3;
    $ed214_d_data_dia = substr($ed214_d_data, 0, 2);
    $ed214_d_data_mes = substr($ed214_d_data, 3, 2);
    $ed214_d_data_ano = substr($ed214_d_data, 6, 4);
} else {
    if (isset($alterar)) {
        $db_opcao = 2;
        $db_botao1 = true;
    } else {
        $db_opcao = 1;
    }
}
if ($ed214_i_aluno!="") {
    $sql4 = "SELECT ed56_i_escola as cod_escola FROM alunocurso WHERE ed56_i_aluno = $ed214_i_aluno";
    $query4 = db_query($sql4);
    $linhas4 = pg_num_rows($query4);
    if ($linhas4==0) {
        $db_botao = true;
    } elseif (db_getsession("DB_coddepto")!=pg_result($query4, 0, 0)) {
        $db_botao = false;
    } else {
        $db_botao = true;
    }
}

$arquivo = "";
$arquivoLaudo = "";

if (isset($ed214_i_codigo) && !empty($ed214_i_codigo)) {
    $sqlLaudo = $clAlunoNecessidade->sql_query_file($ed214_i_codigo, 'ed214_i_anexo_estorage');
    $rsLaudo = $clAlunoNecessidade->sql_record($sqlLaudo);
    if (!$rsLaudo) {
        db_msgbox('Não foi possível buscar o anexo.');
        return;
    }

    if ($clAlunoNecessidade->numrows > 0) {
        $idLaudo = db_utils::fieldsMemory($rsLaudo, 0)->ed214_i_anexo_estorage;
        $arquivo = !empty($idLaudo) ? StorageHelper::downloadArquivo($idLaudo): "" ;
        $arquivoLaudo = basename($arquivo);
    }
}

?>
<div class="container">

<form name="form1" method="post" action="">

  <fieldset>
   <legend>PcD / Altas Habilidades</legend>
   <table>
    <tr>
     <td nowrap title="<?=@$Ted214_i_codigo?>">
      <?=@$Led214_i_codigo?>
     </td>
     <td>
      <?php db_input('ed214_i_codigo', 20, $Ied214_i_codigo, true, 'text', 3, "")?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=@$Ted214_i_aluno?>">
      <?php db_ancora(@$Led214_i_aluno, "", 3);?>
     </td>
     <td>
      <?php db_input('ed214_i_aluno', 10, $Ied214_i_aluno, true, 'text', 3)?>
      <?php db_input('ed47_v_nome', 31, @$Ied47_v_nome, true, 'text', 3, '')?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=@$Ted214_i_necessidade?>">
      <?php db_ancora('Deficiência / Altas Habilidades:', "js_pesquisaed214_i_necessidade(true);", $db_opcao);?>
     </td>
     <td>
      <?php db_input('ed214_i_necessidade', 10, $Ied214_i_necessidade, true, 'text', $db_opcao, " onchange='js_pesquisaed214_i_necessidade(false);'")?>
      <?php db_input('ed48_c_descr', 31, @$Ied48_c_descr, true, 'text', 3, '')?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=@$Ted214_i_apoio?>">
      <b>Apoio Pedagógico (Turma AEE):</b>
     </td>
     <td>
      <?php
        $x = array(""=>"","1"=>"SEM APOIO PEDAGÓGICO","2"=>"COM APOIO PEDAGÓGICO","3"=>"COM APOIO PEDAGÓGICO (OUTRO ESTABELECIMENTO)");
        db_select('ed214_i_apoio', $x, true, $db_opcao, "");
        ?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=@$Ted214_i_tipo?>">
      <?=@$Led214_i_tipo?>
     </td>
     <td>
      <?php
        $x = array(""=>"","1"=>"SEM LAUDO OU PARECER TÉCNICO","2"=>"PARECER OU RELATÓRIO MÉDICO","3"=>"LAUDO TÉCNICO");
        db_select('ed214_i_tipo', $x, true, $db_opcao, "onchange='js_mostraranexo()'");
        ?>
     </td>
    </tr>
    <tr>
     <td>
       <b>Cadeirante:</b>
     </td>
     <td>
       <?php
         $cadeirante = "f";
         $aCadeirante = array("f"=>"NÃO","t"=>"SIM");
         $clAlunoCadeirante = new cl_necessidadealunocadeirante;
         $clAlunoCadeirante->sql_record($clAlunoCadeirante->sql_query_file(
             null,
             "ed189_sequencial",
             null,
             "ed189_aluno = {$ed214_i_aluno}"
         ));
         if ($clAlunoCadeirante->numrows > 0) {
             $cadeirante = "t";
         }
         db_select('cadeirante', $aCadeirante, true, $db_opcao);
            ?>
     </td>
    </tr>
    <tr>
      <td colspan="2">
        <b>Possui BPC (Benificio de prestação continuada):</b>
        <?php
        $bpc = "f";
        $aBPC = array("f"=>"NÃO","t"=>"SIM");
        $clAlunoBPC = new cl_necessidadealunobpc;
        $clAlunoCadeirante->sql_record($clAlunoBPC->sql_query_file(
            null,
            "ed190_sequencial",
            null,
            "ed190_aluno = {$ed214_i_aluno}"
        ));
        if ($clAlunoCadeirante->numrows > 0) {
            $bpc = "t";
        }
        db_select('bpc', $aBPC, true, $db_opcao);
        ?>
      </td>
    </tr>
    <tr id="trTipoAtendimento">
      <td colspan="2">
        <fieldset style="min-height:31px; max-width: 491px;">
          <legend>Tipo de Atendimento</legend>
          <table id='tbTipoAtendimento' class="form-container"></table>
        </fieldset>
      </td>
    </tr>
    <tr id="trSubdivisao" style="display: none;">
       <td colspan="2">
         <fieldset style="min-height:31px; max-width: 491px;">
          <legend>Subdivisão</legend>
           <table id='tbSubdivisao' class="form-container"></table>
         </fieldset>
       </td>
    </tr>
      <?php
        $clAlunoNecessidade = new cl_alunonecessidade;
        $result = $clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query(
            null,
            'alunonecessidade.ed214_i_codigo, necessidade.ed48_c_descr, alunonecessidade.ed214_c_principal',
            'necessidade.ed48_c_descr',
            "ed214_i_aluno = $ed214_i_aluno"
        ));
        if ($clAlunoNecessidade->numrows > 0) {
            ?>
        <tr>
          <td valign="top">
            <b>Necessidade Maior Selecionada:</b>
          </td>
          <td>
             <?php
                db_fieldsmemory($clAlunoNecessidade->sql_record($clAlunoNecessidade->sql_query(
                    null,
                    'alunonecessidade.ed214_i_codigo as principal',
                    'necessidade.ed48_c_descr',
                    "ed214_i_aluno = $ed214_i_aluno and alunonecessidade.ed214_c_principal = 'SIM'"
                )), 0);
             db_selectrecord('principal', $result, true, 1, '', '', '', 1, '', 1);
                ?>
             <input name="atualizar" value="Atualizar" type="submit">
          </td>
        </tr>
        <?php } ?>
    <tr id='ctnAnexo' style="display: none;">
      <td>
          <b>Anexo do Comprovante:</b>
      </td>
      <td>

          <div style="display: flex; align-items: center; width:100%;">
          <iframe name="frame_imagemLaudo"
                  id="frame_imagemLaudo"
                  src="edu4_alunodocumentolaudo.php?imagem_gerada=<?=$arquivoLaudo?>"
                  width="56"
                  height="40"
                  frameborder="1"
                  scrolling="no"></iframe>
          <div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">
            <iframe name="frame_laudo"
                    id="frame_laudo"
                    src="edu1_framelaudo.php"
                    width="100%"
                    height="31"
                    frameborder="0"
                    scrolling="no"
                    style="margin-bottom: 0px;margin-top:2px;"></iframe>
              <input type="button"
                     value="Excluir Imagem"
                     style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;"
                     onclick="excluirImagem(<?=(isset($ed214_i_codigo)?$ed214_i_codigo:"")?>)" >
            <input name="oid_arquivoLaudo" type="hidden" id="oid_arquivoLaudo" value="<?=$arquivo?>" size="30">
          </div>
      </div>

    </tr>
    <tr>
     <td nowrap title="<?=@$Ted214_i_escola?>">
      <?=@$Led214_i_escola?>
     </td>
     <td>
      <?php db_input('ed214_i_escola', 20, $Ied214_i_escola, true, 'hidden', 3, "")?>
      <?php db_input('ed18_c_nome', 45, @$Ied18_c_nome, true, 'text', 3, '')?>
    <tr>
     <td nowrap title="<?=@$Ted214_d_data?>">
      <?=@$Led214_d_data?>
     </td>
     <td>
      <?php db_inputdata('ed214_d_data', @$ed214_d_data_dia, @$ed214_d_data_mes, @$ed214_d_data_ano, true, 'text', 3, "")?>
     </td>
    </tr>
   </table>
  </fieldset>

  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
     type="submit"
     id="db_opcao"
     value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
     <?=($db_botao==false?"disabled":"")?> >

   <input name="cancelar" type="submit" value="Cancelar" <?=(@$db_botao1==false?"disabled":"")?> >

   <input id="btnRecursosParaAvaliacao" name="btnRecursosParaAvaliacao" type="button" value="Recursos Para Avaliação" disabled >

<table width="100%">
 <tr>
  <td valign="top"><br>
  <?php
    $chavepri= array("ed214_i_codigo"=>(!isset($ed214_i_codigo)?"":$ed214_i_codigo),
                   "ed214_i_aluno"=>(!isset($ed214_i_aluno)?"":$ed214_i_aluno),
                   "ed47_v_nome"=>(!isset($ed47_v_nome)?"":$ed47_v_nome),
                   "ed214_i_necessidade"=>(!isset($ed214_i_necessidade)?"":$ed214_i_necessidade),
                   "ed48_c_descr"=>(!isset($ed48_c_descr)?"":$ed48_c_descr),
                   "ed214_i_apoio"=>(!isset($ed214_i_apoio)?"":$ed214_i_apoio),
                   "ed214_i_tipo"=>(!isset($ed214_i_tipo)?"":$ed214_i_tipo),
                   "ed214_i_escola"=>(!isset($ed214_i_escola)?"":$ed214_i_escola),
                   "ed18_c_nome"=>(!isset($ed18_c_nome)?"":$ed18_c_nome),
                   "ed214_d_data"=>(!isset($ed214_d_data)?"":$ed214_d_data),
                   "ed214_c_principal"=>(!isset($ed214_c_principal)?"":$ed214_c_principal),
                   "anexo"=>(!isset($anexo)?"":$anexo));
    $cliframe_alterar_excluir->chavepri=$chavepri;
    $cliframe_alterar_excluir->sql = $clAlunoNecessidade->sql_query(
        "",
        "ed214_i_codigo, ed214_i_aluno, ed47_v_nome,
                                            ed214_i_necessidade, ed214_i_apoio, ed214_i_tipo, ed214_i_escola,
                                            ed48_i_codigo, ed48_c_descr, ed214_c_principal, ed18_c_nome, ed214_d_data,
                                            (case when coalesce(ed214_i_anexo_estorage,0) = 0 then 'NÃO' else 'SIM' end) as anexo",
        "ed48_c_descr",
        " ed214_i_aluno = {$ed214_i_aluno}"
    );
    $cliframe_alterar_excluir->campos  = "ed48_i_codigo,ed48_c_descr,ed214_c_principal,ed18_c_nome,ed214_d_data, anexo";
    $cliframe_alterar_excluir->legenda="Registros";
    $cliframe_alterar_excluir->msg_vazio ="Não foi encontrado nenhum registro.";
    $cliframe_alterar_excluir->textocabec ="#DEB887";
    $cliframe_alterar_excluir->textocorpo ="#444444";
    $cliframe_alterar_excluir->fundocabec ="#444444";
    $cliframe_alterar_excluir->fundocorpo ="#eaeaea";
    $cliframe_alterar_excluir->iframe_height ="100";
    $cliframe_alterar_excluir->iframe_width ="100%";
    $cliframe_alterar_excluir->tamfontecabec = 9;
    $cliframe_alterar_excluir->tamfontecorpo = 9;
    if ($linhas4==0) {
        $cliframe_alterar_excluir->opcoes = 1;
    } elseif (db_getsession("DB_coddepto")!=pg_result($query4, 0, 0)) {
        $cliframe_alterar_excluir->opcoes = 4;
    }
    $cliframe_alterar_excluir->formulario = false;
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
  </td>
 </tr>
</table>
</form>
</div>
<script>
var sRpc = 'edu4_aluno.RPC.php';
const ctnAnexo = document.getElementById('ctnAnexo');

if (document.form1.ed214_i_tipo.value == '2' || document.form1.ed214_i_tipo.value == '3') {
  ctnAnexo.style.display = '';
} else {
  ctnAnexo.style.display = 'none';
}

document.getElementById('trSubdivisao').style.display = 'none';

function js_mostraranexo() {

  if (document.form1.ed214_i_tipo.value == '2' || document.form1.ed214_i_tipo.value == '3') {
    ctnAnexo.style.display = '';

  } else if (document.form1.oid_arquivoLaudo.value.length != 0){
    alert('Exclua o arquivo antes de alterar o tipo de diagnótico');
    return;
  } else {
    ctnAnexo.style.display = 'none';
  }
}

$('btnRecursosParaAvaliacao').observe("click", function() {
  js_carregaWindowRecursos();
});

/**
 * Verificamos se o aluno possui alguma necessidade especial. Caso possua, habilitamos o botao Recursos Para Avaliação
 */
function js_temNecessidade() {

  var oParametro    = new Object;
  oParametro.exec   = 'temNecessidade';
  oParametro.iAluno = $F('ed214_i_aluno');

  var oAjax = new Ajax.Request(
                                sRpc,
                                {
                                  method:     'post',
                                  parameters: 'json='+Object.toJSON(oParametro),
                                  onComplete: js_retornoTemNecessidade
                                }
                              );
}

/**
 * Retorno da verificacao se o aluno possui alguma necessidade especial
 */
function js_retornoTemNecessidade(oResponse) {

  $('btnRecursosParaAvaliacao').disabled = true;
  var oRetorno = JSON.parse(oResponse.responseText);

  if (oRetorno.lTemNecessidade) {

    $('btnRecursosParaAvaliacao').disabled = false;
  }
}

/**
 * Carregamos a windowAux dos recursos para avaliação
 */
var oWindowRecursosEspeciais = null;
function js_carregaWindowRecursos() {

  var iTamanhoJanela = document.body.getWidth()/2.2;
  var iAlturaJanela  = document.body.getHeight()/1.1;
   oWindowRecursosEspeciais = new windowAux('wndRecursosEspeciais',
                                            'Recursos Especiais do Aluno',
                                            iTamanhoJanela,
                                            iAlturaJanela);

  var sConteudo  = "<div id='ctnRecursos' style='width: 600px'>";
      sConteudo += "  <form id='frmSalvarRecursos' name='frmSalvarRecursos' method='post'";
      sConteudo += "  </form>";
      sConteudo += "</div>";
      sConteudo += "<center>";
      sConteudo += "  <input id='btnSalvar' name='btnSalvar' type='button' value='Salvar' onClick='js_salvarRecursos()'>";
      sConteudo += "</center>";
  oWindowRecursosEspeciais.setContent(sConteudo);

  oWindowRecursosEspeciais.setShutDownFunction(function() {
    oWindowRecursosEspeciais.destroy();
  });

  var sMensagem     = 'Selecione um recurso abaixo, caso necessário.';
  var oMessageBoard = new DBMessageBoard('messageRecursosEspeciais',
                                         'Recursos Especiais do Aluno',
                                         sMensagem,
                                         oWindowRecursosEspeciais.getContentContainer()
                                        );

  oMessageBoard.show();
  oWindowRecursosEspeciais.show();

  oGridRecursos              = new DBGrid("gridRecursos");
  oGridRecursos.nameInstance = 'oGridRecursos';
  oGridRecursos.setCheckbox(0);
  oGridRecursos.setHeight(220);
  oGridRecursos.setCellAlign(new Array("center", "left"));
  oGridRecursos.setCellWidth(new Array("10%", "90%"));
  oGridRecursos.setHeader(new Array("Código", "Recurso Necessário"));

  oGridRecursos.show($('ctnRecursos'));
  js_pesquisaRecursos();
}

/**
 * Pesquisamos os recursos para avaliação do inep
 */
function js_pesquisaRecursos() {

  var oParametro    = new Object();
  oParametro.exec   = 'getRecursosAvaliacaoInep';
  oParametro.iAluno = $F('ed214_i_aluno');

  var oAjax = new Ajax.Request(
                                sRpc,
                                {
                                  method:     'post',
                                  parameters: 'json='+Object.toJSON(oParametro),
                                  onComplete: js_retornoPesquisaRecursos
                                }
                              );
}

/**
 * Retorno da pesquisa de recursos para avaliacao do inep
 */

var aNecessidadesEspeciaisAluno = [];
function js_retornoPesquisaRecursos(oResponse) {

  var oRetorno = JSON.parse(oResponse.responseText);

  aNecessidadesEspeciaisAluno = oRetorno.aNecessidades;
  oGridRecursos.clearAll(true);
  oRetorno.aRecursosAvaliacaoInep.each(function(oLinha, iSeq) {

    var aLinha = new Array(oLinha.iCodigo, oLinha.sDescricao.urlDecode());
    var lCheck = false;

    if (oLinha.lTemRecurso) {
      lCheck = true;
    }

    oGridRecursos.addRow(aLinha, false, false, lCheck);
  });
  oGridRecursos.renderRows();
}

/**
 * Salvamos os recursos para avaliacao do inep selecionados para o aluno
 */
function js_salvarRecursos() {

  var aSelecionados = oGridRecursos.getSelection("object");
  var aRecursos     = new Array();
  var lSalvar       = true;

  aSelecionados.each(function(oRecurso, iSeq) {
    aRecursos.push(oRecurso.aCells[0].getValue());
  });

  /**
   * Sempre que aluno possuir as deficiencias, 101 - Cegueira ou 105 - Sudorcegueira, e marcar o recuso 102,
   * ele deve marcar mais de um recurso. (Para censo 2015)
   */
  var lValidou = true;
  if ( aRecursos.in_array(102) && aRecursos.length == 1) {

    aNecessidadesEspeciaisAluno.each(function( oNecessidadeAluno ) {

      if ( [101,105].in_array(oNecessidadeAluno.iCodigo) ) {

        alert('Ao ser informado AUXILIO TRANSCRICAO, deve ser selecionado mais de um Recurso Especial.');
        lValidou = false;
        return $break;
      }
    });
  }

  if ( !lValidou ) {
    return;
  }

  if (aRecursos.length == 0) {
    if (!confirm('Aluno não necessita de recursos especiais?')) {
      lSalvar = false;
    }
  }

  var iTotalRecursos = 0;
  aRecursos.each(function(iRecurso, iSeq) {

    if (iRecurso == 106 || iRecurso == 107 || iRecurso == 108) {
      iTotalRecursos++;
    }
  });

  if (iTotalRecursos > 1) {

    var sMensagem  = 'É permitido informar apenas 1 recurso entre Prova Ampliada (Fonte Tamanho 16), Prova Ampliada';
        sMensagem += ' (Fonte Tamanho 20) e Prova Ampliada (Fonte Tamanho 24).';
    alert(sMensagem);
    return false;
  }

  if (lSalvar) {

    var oParametro                   = new Object();
    oParametro.exec                  = 'salvarRecursosAvaliacao';
    oParametro.iAluno                = $F('ed214_i_aluno');
    oParametro.aRecursosSelecionados = aRecursos;

    var oAjax = new Ajax.Request(
                                 sRpc,
                                 {
                                   method:     'post',
                                   parameters: 'json='+Object.toJSON(oParametro),
                                   onComplete: js_retornoSalvarRecursos
                                 }
                                );
  }
}

/**
 * Retorno do salvar os recursos
 */
function js_retornoSalvarRecursos(oResponse) {

  var oRetorno = JSON.parse(oResponse.responseText);

  if (oRetorno.status == 1) {

    alert("Dados salvos com sucesso!");
    oWindowRecursosEspeciais.destroy();
    js_carregaWindowRecursos();
  } else {

    alert(oRetorno.message.urlDecode());
    return false;
  }
  return true;
}

function js_pesquisaed214_i_necessidade(mostra) {

  if (mostra == true) {

    js_OpenJanelaIframe('', 'db_iframe_necessidade','func_necessidade.php?funcao_js='+
                        'parent.js_mostranecessidade1|ed48_i_codigo|ed48_c_descr',
                        'Pesquisa', true
                       );

  } else {

    if (document.form1.ed214_i_necessidade.value != '') {

      js_OpenJanelaIframe('', 'db_iframe_necessidade',
                          'func_necessidade.php?pesquisa_chave='+
                          document.form1.ed214_i_necessidade.value+
                          '&funcao_js=parent.js_mostranecessidade',
                          'Pesquisa', false
                         );

    } else {
      document.form1.ed48_c_descr.value  = '';
      document.getElementById('trSubdivisao').style.display = 'none';
    }
  }
}

function js_mostranecessidade(sChave, lErro) {

  document.form1.ed48_c_descr.value = sChave;
  if (lErro == true) {

    document.form1.ed214_i_necessidade.focus();
    document.form1.ed214_i_necessidade.value = '';
    document.getElementById('trSubdivisao').style.display = 'none';
  } else {
    buscaSubdivisao();
  }
}

function js_mostranecessidade1(sChave1, sChave2){

  document.form1.ed214_i_necessidade.value = sChave1;
  document.form1.ed48_c_descr.value        = sChave2;
  db_iframe_necessidade.hide();
  buscaSubdivisao();
}

function excluirImagem(necessidade) {
    url  = 'edu1_alunonecessidade001.php?excluirlaudo';
    url += '&ed214_i_aluno='+$F("ed214_i_aluno");
    url += '&ed47_v_nome='+$F("ed47_v_nome");
    url += '&ed214_i_codigo='+necessidade;
    location.href=url;
}

js_temNecessidade();

function buscaSubdivisao() {
  if ( empty($F('ed214_i_necessidade')) ) {
    $('tbSubdivisao').innerHTML = '';
    return false;
  }

  js_divCarregando("Pesquisando Subdivisões ..." , 'msgBox');
  var sJson = '{exec:"pesquisaSubdivisao", necessidade: '+$F('ed214_i_necessidade')+'}';
  var oAjax = new Ajax.Request(
    'edu2_necessidadeEspecial.RPC.php',
    { method:'post', parameters:'json='+sJson, onComplete: js_retornaSubdivisao }
  );
}

function js_retornaSubdivisao( oAjax ) {
  js_removeObj("msgBox");
  var oRetorno = eval("("+oAjax.responseText+")");
  if( oRetorno.status == 0 ) {
    $("tbSubdivisao").innerHTML = "<tr><td><b>"+oRetorno.message.urlDecode()+"</b></td></tr>";
    document.getElementById('trSubdivisao').style.display = '';
    return;
  }

  var todas = "<input type='checkbox' name='todas' id='todas' "+((<?=$db_opcao;?>=='3')?"disabled":"")+" value='' onclick='js_todassubdivisao();'>Todas";

  sHtml = `
    <tr>
      <td style='padding-top:0;'>
        <b style='display:flex;align-items:center;'>` + todas + `</b></td>`;
  sHtml +=    "<td style='padding-top:0;'>";
  sHtml +=    "<table><tr style='display:flex;flex-wrap:wrap;'>";

  for (var oSubdivisao of oRetorno.subdivisoes ) {
    sHtml += "<td style='display:inline-flex;align-items:center;padding-top:0;min-width:160px;'>";
    sHtml += "  <input class='opt_subdivisao' type='checkbox' name='codsubdivisao[]' "+((<?=$db_opcao;?>=='3')?"disabled":"")+" onclick='js_marcarTodas()' id='codsubdivisao' data-tag='cod_"+oSubdivisao.sequencial+"' value='"+oSubdivisao.sequencial+"'>";
    sHtml += oSubdivisao.descricao.urlDecode()+"</td>";
  }
  sHtml += "</tr></table>";
  sHtml += "</td>";
  sHtml += "</tr>";
  $("tbSubdivisao").innerHTML = sHtml;

  if(<?=$db_opcao?> != 1) {
    js_divCarregando("Pesquisando Subdivisões do Aluno ..." , 'msgBox');
    var sJson = '{exec:"pesquisaSubdivisaoAluno", aluno: '+$F('ed214_i_aluno')+', necessidade: '+$F('ed214_i_necessidade')+'}';
    var oAjax = new Ajax.Request(
      'edu2_necessidadeEspecial.RPC.php',
      { method:'post', parameters:'json='+sJson, onComplete: js_getSelecionados }
    );
  } else {
    document.getElementById('trSubdivisao').style.display = '';
  }
}

function js_getSelecionados(oAjax){
  js_removeObj("msgBox");
  document.getElementById('trSubdivisao').style.display = '';
  var oRetorno = eval("("+oAjax.responseText+")");
  for (var oSubdivisao of oRetorno.subdivisoes ) {
    let elem = '#tbSubdivisao input[data-tag="cod_'+oSubdivisao.subdivisao+'"]';
    document.querySelector(elem).checked = true;
  }
  js_marcarTodas();
}

function buscaTipoAtendimento() {
  js_divCarregando("Pesquisando Tipo de Atendimento ..." , 'msgBoxTipoAtendimento');
  var sJson = '{exec:"pesquisaTipoAtendimento", order:"ed186_descricao"}';
  var oAjax = new Ajax.Request(
    'edu2_necessidadeEspecial.RPC.php',
    { method:'post', parameters:'json='+sJson, onComplete: js_retornaTipoAtendimento }
  );
}
buscaTipoAtendimento();

function js_retornaTipoAtendimento( oAjax ) {

  js_removeObj("msgBoxTipoAtendimento");
  var oRetorno = eval("("+oAjax.responseText+")");

  if( oRetorno.status == 0 ) {
    $("tbTipoAtendimento").innerHTML = "<td><b>"+oRetorno.message.urlDecode()+"</b></td>";
    return;
  }

  var todas = "<input type='checkbox' name='todasTipoAtendimento' id='todasTipoAtendimento' "+((<?=$db_opcao;?>=='3')?"disabled":"")+" value='' onclick='js_todastipoatendimento();'>Todas";

  sHtml = `
    <tr>
      <td style='padding-top:0;'>
        <b style='display:flex;align-items:center;'>` + todas + `</b></td>`;
  sHtml +=    "<td style='padding-top:0;'>";
  sHtml +=    "<table><tr style='display:flex;flex-wrap:wrap;'>";

  for (var oTipoAtendimento of oRetorno.tiposAtendimentos ) {
    sHtml += "<td style='display:inline-flex;align-items:center;padding-top:0;min-width:160px;'>";
    sHtml += "  <input class='opt_tipoatendimento' type='checkbox' name='codtipoatendimento[]' "+((<?=$db_opcao;?>=='3')?"disabled":"")+" onclick='js_marcarTodasTipoAtendimento()' id='codtipoatendimento' data-tag='cod_"+oTipoAtendimento.sequencial+"' value='"+oTipoAtendimento.sequencial+"'>";
    sHtml += oTipoAtendimento.descricao.urlDecode()+"</td>";
  }
  sHtml += "</tr></table>";
  sHtml += "</td>";
  sHtml += "</tr>";
  $("tbTipoAtendimento").innerHTML = sHtml;

  js_divCarregando("Pesquisando Tipo de Atendimento do Aluno ..." , 'msgBox');
  var sJson = '{exec:"pesquisaTipoAtendimentoAluno", aluno: '+$F('ed214_i_aluno')+'}';
  var oAjax = new Ajax.Request(
    'edu2_necessidadeEspecial.RPC.php',
    { method:'post', parameters:'json='+sJson, onComplete: js_getTipoAtendimentoSelecionados }
  );
}

function js_getTipoAtendimentoSelecionados(oAjax){
  js_removeObj("msgBox");
  var oRetorno = eval("("+oAjax.responseText+")");

  for (var oTipoAtendimento of oRetorno.tiposAtendimentos ) {
    let elem = '#tbTipoAtendimento input[data-tag="cod_'+oTipoAtendimento.tipoatendimento+'"]';
    document.querySelector(elem).checked = true;
  }
  js_marcarTodasTipoAtendimento();
}

function js_marcarTodas() {
  if( document.form1.todas.checked == true ) {
    document.form1.todas.checked = false;
  } else {
    if(document.querySelectorAll('#tbSubdivisao input[type="checkbox"]:checked').length
      == document.querySelectorAll('.opt_subdivisao').length)
      document.form1.todas.checked = true;
  }
}

function js_marcarTodasTipoAtendimento() {
  if( document.form1.todasTipoAtendimento.checked == true ) {
    document.form1.todasTipoAtendimento.checked = false;
  } else {
    if(document.querySelectorAll('#tbTipoAtendimento input[type="checkbox"]:checked').length
      == document.querySelectorAll('.opt_tipoatendimento').length)
      document.form1.todasTipoAtendimento.checked = true;
  }
}

function js_todassubdivisao() {
  tam = document.form1.codsubdivisao.length;

  if( tam == undefined ) {

    if( document.form1.todas.checked == true ) {
      document.form1.codsubdivisao.checked = true;
    } else {
      document.form1.codsubdivisao.checked = false;
    }
  } else {

    for( t = 0; t < tam; t++ ) {

      if( document.form1.todas.checked == true ) {
        document.form1.codsubdivisao[t].checked = true;
      } else {
        document.form1.codsubdivisao[t].checked = false;
      }
    }
  }
}

function js_todastipoatendimento() {
  tam = document.form1.codtipoatendimento.length;

  if( tam == undefined ) {

    if( document.form1.todasTipoAtendimento.checked == true ) {
      document.form1.codtipoatendimento.checked = true;
    } else {
      document.form1.codtipoatendimento.checked = false;
    }
  } else {

    for( t = 0; t < tam; t++ ) {

      if( document.form1.todasTipoAtendimento.checked == true ) {
        document.form1.codtipoatendimento[t].checked = true;
      } else {
        document.form1.codtipoatendimento[t].checked = false;
      }
    }
  }
}
if(!empty($F('ed214_i_necessidade'))) {
  buscaSubdivisao();
}

</script>
