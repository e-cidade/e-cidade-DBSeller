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


  require(modification("libs/db_stdlib.php"));
  require(modification("libs/db_conecta.php"));
  include(modification("libs/db_sessoes.php"));
  include(modification("libs/db_usuariosonline.php"));
  include(modification("dbforms/db_funcoes.php"));
  $clrotulo = new rotulocampo;
  $clrotulo->label("v07_parcel");
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
      <meta http-equiv="Expires" CONTENT="0">
      <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
      <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type="text/css">
      .inputContainer {
        text-align: right!important;
      }
    </style>
  </head>
  <body bgcolor=#CCCCCC  onLoad="document.form1.v07_parcel.focus()" >
    <form class="container" name="form1" method="post" >
      <fieldset>
          <legend>Alterar Parcelamento do Foro para Dívida</legend>
          <table class="form-container">
            <tr>
              <td nowrap title="<?=@$Tparcel?>" >
              <?php
               db_ancora(@$Lv07_parcel,"js_pesquisaparcel(true);",4)
              ?>
              </td>
              <td>
              <?php
              db_input('v07_parcel',10,$Iv07_parcel,true,'text',4,"onchange='js_pesquisaparcel(false);'")
              ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="Filtrar/Remover dívidas" >
                <label>Filtrar Dívidas:</label>
              </td>
              <td class="field-size2">
                <select id="filtrar_dividas">
                  <option value="0">Não</option>
                  <option value="1">Sim</option>
                </select>
              </td>
            </tr>
            <tr>
              <table id="container-filtros" style="display:none" class="form-container">
                <tr>
                  <td nowrap title="Tipo(k00_tipo) que ficará as dividas removidas">
                    <?php
                     db_ancora("Tipo Débito Destino:","js_pesquisadb02_idparag(true);",4)
                    ?>
                  </td>
                  <td class="inputContainer">
                    <input type="text" id="tipo_debito" name="tipo_debito" class="field-size2"></input>
                  </td>
                </tr>
                <tr>
                  <td nowrap title="Receitas(k00_receit) que serão removidas, separadas por vírgula">
                    <label>Receitas:</label>
                  </td>
                  <td class="inputContainer">
                    <input type="text" id="receitas" name="receitas" class="field-size2"></input>
                  </td>
                </tr>
                <tr>
                  <td nowrap title="Dívidas(v01_coddiv) que serão removidas, separadas por vírgula">
                    <label>Dívidas:</label>
                  </td>
                  <td class="inputContainer">
                    <input type="text" id="dividas" name="dividas" class="field-size2"></input>
                  </td>
                </tr>
                <tr>
                  <td nowrap title="Exercícios(v01_exerc) que serão removidos">
                    <label>Exercício:</label>
                  </td>
                  <td class="field-size2" class="inputContainer">
                    <input type="text" id="exercicio_inicio" name="exercicio_inicio" class="field-size1"></input> à <input type="text" id="exercicio_fim" name="exercicio_fim" class="field-size1"></input>
                  </td>
                </tr>
              </table>
            </tr>
          </table>
      </fieldset>
      <input name="desfazer_parcelamento" type="button" id="desfazer_parcelamento" value="Executar">
   </form>

  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));

    $func_iframe = new janela('db_iframe','');
    $func_iframe->posX=1;
    $func_iframe->posY=20;
    $func_iframe->largura=780;
    $func_iframe->altura=430;
    $func_iframe->titulo='Pesquisa';
    $func_iframe->iniciarVisivel = false;
    $func_iframe->mostrar();
  ?>
  </body>
</html>

<script>
const
  inputParcel = $("v07_parcel"),
  containerFiltros = $("container-filtros"),
  inputFiltrar = $("filtrar_dividas"),
  inputTipoDebito = $("tipo_debito"),
  inputReceitas = $("receitas"),
  inputDividas = $("dividas"),
  inputExercicioInicio = $("exercicio_inicio"),
  inputExercicioFim = $("exercicio_fim"),
  urlRpc = 'arr4_alteracaoparcelamento.RPC.php';

inputParcel.addClassName("field-size2");

$("desfazer_parcelamento").addEventListener("click", event => {
  if(inputParcel.value == ''){
    alert('Parcelamento Obrigatório!');
    return false;
  }

  var formData = new FormData();
  formData.append('exec', 'desfazerParcelamento');
  formData.append('parcelamento', inputParcel.value);

  formData.append('filtrar', inputFiltrar.value);
  formData.append('tipoDebito', inputTipoDebito.value);
  formData.append('receitas', inputReceitas.value);
  formData.append('dividas', inputDividas.value);
  formData.append('exercicioInicio', inputExercicioInicio.value);
  formData.append('exercicioFim', inputExercicioFim.value);

  return HttpClient.post(urlRpc, {body: formData}).then(response => {
    alert(response.mensagem);
  });
});

inputFiltrar.addEventListener("change", event => {
  if(inputFiltrar.value === "1"){
    containerFiltros.style.display = 'table';
  } else {
    containerFiltros.style.display = 'none';
  }
});

function js_pesquisaparcel(mostra){
  if(mostra==true){
    db_iframe.jan.location.href = 'func_termo.php?funcao_js=parent.js_mostratermo1|0';
    db_iframe.mostraMsg();
    db_iframe.show();
    db_iframe.focus();
  }else{
    db_iframe.jan.location.href = 'func_termo.php?pesquisa_chave='+document.form1.v07_parcel.value+'&funcao_js=parent.js_mostratermo';
  }
}

function js_pesquisadb02_idparag(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_arretipo','func_arretipo.php?funcao_js=parent.js_mostradb_paragrafo1|k00_tipo|k00_descr','Pesquisa',true);
  }
}

function js_mostratermo(chave,erro){
  if(erro==true){
     document.form1.v07_parcel.focus();
     document.form1.v07_parcel.value = '';
  }
}

function js_mostradb_paragrafo1(chave1,chave2){
  document.form1.tipo_debito.value = chave1;
  db_iframe_arretipo.hide();
}

function js_mostratermo1(chave1){
     document.form1.v07_parcel.value = chave1;
     db_iframe.hide();
}

</script>
