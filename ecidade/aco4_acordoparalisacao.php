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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_acordo_classe.php"));
require_once(modification("classes/db_acordomovimentacao_classe.php"));

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clacordo             = new cl_acordo;
$clacordomovimentacao = new cl_acordomovimentacao;

$db_opcao = $oGet->dbopcao;

$clacordo->rotulo->label();
$clacordomovimentacao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ac16_sequencial");
$clrotulo->label("ac16_resumoobjeto");
$clrotulo->label("ac10_obs");



?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
  db_app::load("scripts.js, strings.js, prototype.js, datagrid.widget.js");
  db_app::load("widgets/messageboard.widget.js, widgets/windowAux.widget.js");
  db_app::load("estilos.css, grid.style.css");
?>
<style>
td {
  white-space: nowrap;
}

fieldset table td:first-child {
  width: 80px;
  white-space: nowrap;
}
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<center>

<!-- <input type='hidden' id='ac47_sequencial'  /> -->

      <fieldset style="margin-top: 30px; width: 400px;">

        <legend><strong><label id='sTituloFieldSet'></label>&nbsp;Paralisação de Acordos</strong></legend>

	      <table align="center" border="0">
	        <tr>
	          <td title="<?=@$Tac16_sequencial?>" align="left">
	            <?php

	            if ($db_opcao == 2 || $db_opcao == 3) {
	              db_ancora($Lac16_sequencial, "js_pesquisaParalisacao(true);", 1);
	            } else {
	              db_ancora($Lac16_sequencial, "js_pesquisaac16_sequencial(true);", 1);
	            }

              ?>
	          </td>
	          <td align="left">
	            <?php

	            if ($db_opcao == 2 || $db_opcao == 3) {
	              db_input('ac16_sequencial',10,$Iac16_sequencial,true,'text', 1," onchange='js_pesquisaParalisacao(false);'");
	            } else {
	              db_input('ac16_sequencial',10,$Iac16_sequencial,true,'text', 1," onchange='js_pesquisaac16_sequencial(false);' ");
	            }
                db_input('ac16_resumoobjeto',40,$Iac16_resumoobjeto,true,'text',3);
              ?>
	          </td>
	        </tr>

	        <tr>
	          <td><strong>Data de Inicio:</strong></td>
	          <td>
	            <?php db_inputdata('ac47_datainicio',null,null,null, true, 'text',$db_opcao );
	            ?>
	          </td>
	        </tr>
          <tr>
	          <td><strong>Tipo de Paralisação:</strong></td>
	          <td> 
                <select name="" id="tipoParalisacao" class="field-size6" >
                    <option value="">Selecione</option>
                </select>
	          </td>
	        </tr>
          <tr id="linhaProcesso" style="display:none">
	          <td><strong>Processo:</strong></td>
	          <td> 
                <input type="text" placeholder = "Número/Ano" id="processo" class="field-size4" >
	          </td>
	        </tr>
		      <tr>
		        <td colspan="2">
		          <fieldset id="fieldsetobservacao" class="fieldsetinterno">
		            <legend>
		              <b>Observação</b>
		            </legend>
		              <?php
		                db_textarea('ac10_obs',5,64,$Iac10_obs,true,'text',1,"");
		              ?>
		          </fieldset>
		        </td>
		      </tr>
	      </table>

      </fieldset>


      <div style="margin-top: 10px; text-align: center;">
        <input id="incluir" name="incluir" type="button" value="Incluir" onclick="return js_paralisarContrato();">
        <?php

          if ($db_opcao == 2 || $db_opcao == 3) {
           // echo "<input id='pesquisarParalisado' name='pesquisarParalisacao' type='button' value='Pesquisar Acordos' onclick='js_pesquisaParalisacao(true);'>";
          } else {
          //  echo "<input id='pesquisarHomologado' name='pesquisarParalisacao' type='button' value='Pesquisar Acordos' onclick='js_pesquisaac16_sequencial(true);'>";
          }
        ?>
        <input type='button' value ='Consultar Acordo' onclick="js_verAcordo();" />
      </div>
</center>


</body>

<script>
  
    var   sUrl = 'aco4_acordo.RPC.php';
    var   oGet = js_urlToObject(window.location.search);
    var   iDbOpcao = oGet.dbopcao;
    var   sExec = '';
    const CAMINHO_MENSSAGENS = "patrimonial.contratos.aco4_acordoparalisacao.";
    
    var selectTipos = document.querySelector('#tipoParalisacao');
    var inputProcesso = document.querySelector('#processo');
    
    window.addEventListener('load', () => {
      var codigos = [9, 10, 11];
      var oParam = new Object();
          oParam.exec = "getTiposParalisacao";
          oParam.codes = codigos;
          new Ajax.Request(
              sUrl,
              {
                  method: 'post',
                  parameters: 'json='+js_objectToJson(oParam),
                  onComplete: retorno => {
                      dados = JSON.parse(retorno.responseText);
                      for (let key in dados.tiposParalisacao) {
                          let value = dados.tiposParalisacao[key];
                          let options = `<option value="${value.codigo}">${value.descricao.urlDecode()}</option>`;
                          selectTipos.innerHTML += options;
                      }
                  }
              }
          );
    })


    selectTipos.addEventListener("change", (event) => {
        var tipoEvento = selectTipos.options[selectTipos.selectedIndex].value;
        switch(tipoEvento) {
          case '9':
          case '10':
                document.querySelector("#linhaProcesso").style = "display: visible";
                document.querySelector('#processo').value = '';
                break;
            default: 
                document.querySelector("#linhaProcesso").style = "display: none";
                document.querySelector('#processo').value = ''; 
                break;
           
        }
    })
  
    var sAcao = "Incluir";
    
    switch (iDbOpcao) {
      
      case "1" :
        
        sAcao = "Incluir";
        sExec = "salvarParalisacao";
        js_pesquisaac16_sequencial(true);
        break;

        case "2" :

        sAcao = "Alterar";
        sExec = "alterarParalisacao";
        js_pesquisaParalisacao(true);
        break;

        case "3" :
            sAcao = "Excluir";
            sExec = "excluirParalisacao";
            document.querySelector("#ac10_obs").setAttribute("disabled", "disabled");
            selectTipos.setAttribute("disabled", "disabled");
            inputProcesso.setAttribute("disabled", "disabled");
            js_pesquisaParalisacao(true);
            break;
      }

      $("incluir").value = sAcao;
      $('sTituloFieldSet').innerHTML = sAcao;
      
      
    function js_verAcordo( iAcordo ){
      
      var iAcordo  = $("ac16_sequencial").value;
      if ( iAcordo == '') {

        alert(_M(CAMINHO_MENSSAGENS + "contrato_invalido" ));
        return false;
      };

      js_OpenJanelaIframe('CurrentWindow.corpo',
      'db_iframe_consultaacordo',
          'con4_consacordos003.php?ac16_sequencial='+iAcordo,
          'Consulta Dados Acordo',
          true);

        }

        
        /**
     * funcao para retornar dados da paralisação
     usada apenas no caso de alteracao ou exclusao
    */
   function js_buscaDadosParalisacao( iAcordo ){

     var oParam              = new Object();
          oParam.exec         = "getDadosParalisacao";
          oParam.iAcordo      = iAcordo;
      new Ajax.Request( sUrl, {
        method: 'post',
                                parameters: 'json='+js_objectToJson(oParam),
                                onComplete: js_retornoGetDadosParalisacao
      });
    }

    /**
     * retorno da funcao getDadosParalisacao
     * ira preenher os campos
     */
    function js_retornoGetDadosParalisacao(oAjax) {
      
      var oRetorno = JSON.parse(oAjax.responseText);
      var sMensagem = oRetorno.message.urlDecode();

      if (oRetorno.status > 1) {
        return alert(sMensagem);
      }
      
      $("ac47_datainicio").value = oRetorno.oDados.dtInicial;
      $("ac10_obs").value = oRetorno.oDados.sObservacao.urlDecode();
      selectTipos.value = oRetorno.oDados.sTipoEvento;
      if (selectTipos.value == '9' || selectTipos.value == '10') {
        document.querySelector("#linhaProcesso").style = "display: visible";
      }
      inputProcesso.value = oRetorno.oDados.sAnoProcesso;
    }

    /**
     * Incluir paralisacao
     */
    function js_paralisarContrato() {
      if (iDbOpcao == 3) {
          if (!confirm('A paralisação e o evento criado automaticamente serão excluidos. Deseja prosseguir com a exclusão?')) {
              return;
          };
      }
      var oErro = {};
          oErro.acao = sAcao.toLowerCase();
          if (iDbOpcao != 3) {
              if ($('ac16_sequencial').value == '') {
                alert(  _M(CAMINHO_MENSSAGENS + 'contrato_invalido' ) ); return false;
              }

              if ( $("ac47_datainicio").value == '' ) {
                alert(  _M(CAMINHO_MENSSAGENS + 'data_invalida' ) ); return false;
              }

              if ( $('ac10_obs').value == '' ) {
                alert(  _M(CAMINHO_MENSSAGENS + 'observacao_branco' , oErro) ); return false;
              }

              if (selectTipos.value == '') {
                  alert("O tipo de Paralisação deve ser informado.");
                  return false;
              }
              if (selectTipos.value == '9' || selectTipos.value == '10') {
                  if (inputProcesso.value == '') {
                    alert("O Ano e o Processo devem ser informados.");
                    return false;
                  }
              }
          }



      js_divCarregando(_M(CAMINHO_MENSSAGENS + 'incluindo_paralisacao' ),'msgBox');

      var iAcordo      = $("ac16_sequencial").value;
      var dtInicial    = js_formatar($("ac47_datainicio").value, 'd');
      var sObservacao  = $("ac10_obs").value;
      var tipoParalisacao = selectTipos.value;
      var oParam = new Object();

      if (inputProcesso.value != "") {
          var processoAno = inputProcesso.value.split("/");
          oParam.processo = processoAno[0];
          oParam.ano = processoAno[1];
      }
     
      oParam.exec = sExec;
      oParam.iAcordo = iAcordo;
      oParam.dtInicial = dtInicial;
      oParam.tipoParalisacao = tipoParalisacao;
     
      oParam.sObservacao  = encodeURIComponent(tagString(sObservacao));
      new Ajax.Request(
          sUrl,
          {
              method: 'post',
              parameters: 'json=' + js_objectToJson(oParam),
              onComplete: res => {
                  var oRetorno = JSON.parse(res.responseText);
                  js_removeObj("msgBox");
  
                  alert(oRetorno.message.urlDecode());
            
                  if (oRetorno.status == '1') {
                      
                      $("ac16_sequencial")  .value = '';
                      $("ac47_datainicio")  .value = '';
                      $("ac16_resumoobjeto").value = '';
                      $("ac10_obs")         .value = '';
                      document.querySelector('#processo').value = '';
                      selectTipos.value = '';
                  }
              }
          }
      );

    }

    /**
     * Retorna os dados da homologacao
     */
    
    /**
     * Pesquisa acordos Paralisados para serem Alteradas ou excluidas
     */
    function js_pesquisaParalisacao(lMostrar) {

      var sTituloJanela = 'Pesquisar Acordos Paralisados';

      if (lMostrar == true) {

        var sUrl = 'func_acordo.php?funcao_js=parent.js_mostraParalisacao1|ac16_sequencial|ac16_resumoobjeto&iTipoFiltro=5';
        js_OpenJanelaIframe('CurrentWindow.corpo',
        'db_iframe_acordoParalisado',
                            sUrl,
                            sTituloJanela,
                            true);
      } else {
        
        if ($('ac16_sequencial').value != '') {

          var sUrl = 'func_acordo.php?descricao=true&pesquisa_chave='+$('ac16_sequencial').value+
                    '&funcao_js=parent.js_mostraParalisacao&iTipoFiltro=5';
                    
          js_OpenJanelaIframe('CurrentWindow.corpo',
                              'db_iframe_acordoParalisado',
                              sUrl,
                              sTituloJanela,
                              false);
        } else {
          $('ac16_sequencial').value = '';
        };

      };
    }
    
    /**
     * Retorno da pesquisa acordos
     */
    function js_mostraParalisacao(chave1,chave2,erro) {
      
      if (erro == true) {

        $('ac16_sequencial').value   = '';
        $('ac16_resumoobjeto').value = chave1;
        $('ac16_sequencial').focus();
        $('ac47_datainicio') . value = '';
        $('ac10_obs')        . value = '';
      } else {

        $('ac16_sequencial').value   = chave1;
        $('ac16_resumoobjeto').value = chave2;
        js_buscaDadosParalisacao(chave1);
      }
    }

    /**
     * Retorno da pesquisa acordos paralisados
     */
    function js_mostraParalisacao1(chave1,chave2) {

      $('ac16_sequencial').value    = chave1;
      $('ac16_resumoobjeto').value  = chave2;
      db_iframe_acordoParalisado.hide();
      js_buscaDadosParalisacao(chave1);
    }
    

    /**
     * Pesquisa acordos Homologados para serem paralisados
     */
    function js_pesquisaac16_sequencial(lMostrar) {

      var sTituloJanela = 'Pesquisar Acordos Homologados';

      if (lMostrar == true) {

        var sUrl = 'func_acordo.php?funcao_js=parent.js_mostraacordo1|ac16_sequencial|ac16_resumoobjeto&iTipoFiltro=4';
        js_OpenJanelaIframe('CurrentWindow.corpo',
        'db_iframe_acordo',
                            sUrl,
                            sTituloJanela,
                            true);
      } else {
        
        if ($('ac16_sequencial').value != '') {

          var sUrl = 'func_acordo.php?descricao=true&pesquisa_chave='+$('ac16_sequencial').value+
                    '&funcao_js=parent.js_mostraacordo&iTipoFiltro=4';
                    
          js_OpenJanelaIframe('CurrentWindow.corpo',
                              'db_iframe_acordo',
                              sUrl,
                              sTituloJanela,
                              false);
                            } else {
          $('ac16_sequencial').value = '';
          $('ac16_resumoobjeto').value = '';
        };
      };
    }
    
    /**
     * Retorno da pesquisa acordos
     */
    function js_mostraacordo(chave1,chave2,erro) {

      if (erro == true) {
        
        $('ac16_sequencial').value   = '';
        $('ac16_resumoobjeto').value = chave1;
        $('ac16_sequencial').focus();
      } else {

        $('ac16_sequencial').value   = chave1;
        $('ac16_resumoobjeto').value = chave2;
      }
    }

    /**
     * Retorno da pesquisa acordos
     */
    function js_mostraacordo1(chave1,chave2) {
      
      $('ac16_sequencial').value    = chave1;
      $('ac16_resumoobjeto').value  = chave2;
      db_iframe_acordo.hide();
    }
    
</script>
<?PHP db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit")); ?>

</html>
