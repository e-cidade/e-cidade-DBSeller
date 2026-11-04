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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oDaoLista = new cl_lista();
$oDaoLista->rotulo->label();

$db_opcao = 1;

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php

      db_app::load("scripts.js, strings.js, numbers.js, prototype.js, estilos.css, grid.style.css");
      db_app::load("widgets/dbtextFieldData.widget.js, datagrid.widget.js, AjaxRequest.js");
    ?>
  </head>
  <body class="body-default">
    <div class="container">

     <form action="#" method="post" name="formulario" id="formulario">

      <fieldset>
        <legend>Excluir Item da Lista</legend>
        <table class="form-container">
          <tr>
            <td title="<?php echo $Tk60_codigo; ?>" >
              <?php
                db_ancora($Lk60_codigo,"js_pesquisalista(true);", 4);
              ?>
            </td>
            <td>
              <?php
                db_input('k60_codigo', 10, $Ik60_codigo, true, 'text', 4, "onchange='js_pesquisalista(false);'");
                db_input('k60_descr', 90, $Ik60_descr, true, 'text', 3);
              ?>
            </td>
          </tr>
        </table>

        <table class="form-container">
          <tr>
            <td>
              <fieldset id="containerResumo" class="separator hide">
                <legend>Resumo</legend>

                <textarea id="resumo" class="field-size2" readonly="readonly"></textarea>
              </fieldset>
            </td>
          </tr>

          <tr>
            <td>
              <fieldset id="containerLista" class="hide">
                <legend>Itens</legend>

                <div id="gridItensLista"></div>
              </fieldset>

            </td>
          </tr>
        </table>

      </fieldset>

      <input name="excluir" type="button" id="excluir" value="Excluir" onclick="js_excluir();" />
      <input name="limpar"  type="reset"  id="limpar"  value="Limpar" onclick="js_limpar();" />

     </form>

    </div>

    <?php
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>
  </body>
  </html>
  <script type="text/javascript">

   var sCaminhoMensagens = "tributario.notificacoes.cai4_excluiitemlista.";
   var sUrlRpc           = "cai4_excluiitemlista.RPC.php";

   /**
    * Grid Itens da Lista
    */
   oGridItensLista              = new DBGrid('GridItensLista');
   oGridItensLista.nameInstance = 'oGridItensLista';
   oGridItensLista.setCellWidth( new Array('75%',  '25%') );
   oGridItensLista.setCellAlign( new Array('left', 'center') );
   oGridItensLista.setHeader   ( new Array('','Notificado', 'Cód. Arrecadação / Parcela' ) );
   oGridItensLista.setCheckbox(0);
   oGridItensLista.setHeight(350);
   oGridItensLista.show( $('gridItensLista') );
   oGridItensLista.clearAll( true );

   /**
    * Lookup lista
    */
   function js_pesquisalista(mostra){

     var iCodigoLista = $F('k60_codigo');
     if(mostra==true){
       js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_lista.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr', 'Pesquisa', true);
     }else{
       js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_lista.php?pesquisa_chave='+iCodigoLista+'&funcao_js=parent.js_mostralista', 'Pesquisa', false);
     }
   }

   function js_mostralista(chave, erro){

     $('k60_descr').value = chave;

     if(erro==true){

       js_limpar();
       $('k60_descr').value = chave;
       $('k60_codigo').focus();
       $('k60_codigo').value = '';
     }else{
       getItensLista();
     }
   }

   function js_mostralista1(chave1,chave2){

     $('k60_codigo').value = chave1;
     $('k60_descr').value  = chave2;
     db_iframe.hide();
     getItensLista();
   }

   function js_limpar(){

     $('containerResumo').addClassName('hide');
     $('containerLista').addClassName('hide');
     oGridItensLista.clearAll();
     $('excluir').disabled = '';
     $('formulario').reset();
   }

   /**
    * Pesquisa os registros vinculados a lista
    */
   function getItensLista(){

     if( empty($F('k60_codigo')) ) {
       return false;
     }

     var oParametros = {
       sExecucao    : 'getItensLista',
       iCodigoLista : $F('k60_codigo')
     }

    new AjaxRequest(sUrlRpc, oParametros, function(oRetorno, erro) {

      if (erro) {

        alert(oRetorno.sMessage.urlDecode());
        js_limpar();
        return false;
      }

      oGridItensLista.clearAll( true );

      $('containerResumo').removeClassName('hide');
      $('containerLista').removeClassName('hide');

      oRetorno.aItensLista.each(

        function (oDado, iInd) {

          var aLinha    = new Array();
              aLinha[0] = oDado.sNome.urlDecode();
              aLinha[1] = oDado.iNumpre.urlDecode();

          if (aLinha[0].length > 70) {
            aLinha[0] = aLinha[0].substring(0, 70) + "...";
          }

          oGridItensLista.addRow(aLinha);
        }
      );

      oGridItensLista.renderRows();
      $('resumo').value = oRetorno.sResumoLista.urlDecode();

    }).setMessage( _M( sCaminhoMensagens + 'carregando_itens_lista' ) ).execute();
  }

  function js_excluir(){

    $('excluir').disabled = 'true';

    try {

      /**
       * Verificamos se o código da lista está preenchido
       */
      if( empty($F('k60_codigo')) ) {
        throw _M( sCaminhoMensagens + 'codigolista_obrigatorio' );
      }

      /**
       * Buscamos todos os elementos marcados da grid para pegar seus dados
       */
      var aItens         = {};
      var aItensMarcados = oGridItensLista.getElementsByClass('marcado');

      for (var iLinha in aItensMarcados) {

        if (!isNumeric(iLinha)) {
          break;
        }

        var aElementos = aItensMarcados[iLinha].getElementsByTagName('td');

        /**
         * Buscamos o elemento que contém o sequencial da condicionante, caso exista
         */
        var iNumpreNumpar = aElementos[2].textContent.trim();

        var aDadosItens = {
          sNumpreNumpar : iNumpreNumpar
        }

        aItens[iLinha] = aDadosItens;
      }

      iTotalItensMarcados = Object.keys(aItens).length;

      /**
       * Verificamos se houve alguma condicionante selecionada na grid
       */
      if (iTotalItensMarcados == 0) {
        throw _M( sCaminhoMensagens + 'itemlista_obrigatorio' );
      }

      if (iTotalItensMarcados >= oGridItensLista.getNumRows()) {
        throw _M( sCaminhoMensagens + 'todositens_selecionados' );
      }

      if (!confirm(_M( sCaminhoMensagens + 'confirma_exclusao' ))) {

        $('excluir').disabled = '';
        return false;
      }

    }catch ( sMensagemErro ) {

      alert( sMensagemErro );
      $('excluir').disabled = '';
      return false;
    }

    $('excluir').disabled = '';

    var oParametros = {
      sExecucao      : 'excluirItensLista',
      iCodigoLista   : $F('k60_codigo'),
      aItensMarcados : aItens
    }

    new AjaxRequest(sUrlRpc, oParametros, function(oRetorno, erro) {

      alert(oRetorno.sMessage.urlDecode());
      if (erro) {
        return false;
      }

      js_limpar();
    }).setMessage( _M( sCaminhoMensagens + 'excluindo_itens_lista' ) ).execute();

    return false;
  }

</script>