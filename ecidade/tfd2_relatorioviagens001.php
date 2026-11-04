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
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");

$oRotulo = new rotulocampo;
$oRotulo->label("tf03_i_codigo");
$oRotulo->label("tf03_c_descr");
$oRotulo->label('tf27_i_codigo');
$oRotulo->label('rh70_descr');
$oRotulo->label('rh70_estrutural');

$aAssets = array( 
  "estilos.css",
  "scripts.js",
  "prototype.js",
  "strings.js",
  "dbautocomplete.widget.js",
);
?>
<html>
  <head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Expires" CONTENT="0">
    <?php db_app::load($aAssets); ?>
  </head>
  <body class="body-default">
    <form action="" class="container">
      <fieldset>
        <legend>Relatório de Viagens</legend>
        <table class="form-container">
          <tr>
            <td>
              <label for="periodoInicial"> Período: </label>
            </td>
            <td>
              <?php
                db_inputdata( 'periodoInicial', null, null, null, true, 'text', 1 );
              ?>
              <label for="periodoFinal"> até </label>
              <?php
                db_inputdata( 'periodoFinal',  null, null, null, true, 'text', 1 );
              ?>
            </td>
          </tr>
          <tr>
            <td title="Destino dos pedidos.">
              <?php
                db_ancora("<b>Destino:</b>", "pesquisaDestino();", 1);
              ?>
            </td>
            <td>
              <?php
                db_input( 'tf03_i_codigo', 10, $Itf03_i_codigo, true, 'hidden', 3 );
                db_input( 'tf03_c_descr',  65, $Itf03_c_descr,  true, 'text',   1 );
              ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Especialidade">
              <?php
                db_ancora("Especialidade: ", "js_pesquisatf01_i_rhcbo(true);", $db_opcao);
              ?>
            </td>
            <td colspan="2">
              <?php
                db_input('rh70_estrutural', 10, $Irh70_estrutural, true, 'text', $db_opcao,
                         " onchange='js_pesquisatf01_i_rhcbo(false);'"
                        );
                db_input('tf01_i_rhcbo', 10, $Itf01_i_rhcbo, true, 'hidden', 3);
                db_input('rh70_descr', 51, $Irh70_descr, true, 'text', $db_opcao, '');
              ?>
            </td>
          </tr>
          <tr>
            <td>
              <label for="">Situação:</label>
            </td>
            <td>
              <?php
              $aSituacoes      = array( 0 => "TODAS" );
              $oDaoSituacaoTfd = new cl_tfd_situacaotfd();
              $sSqlSituacaoTfd = $oDaoSituacaoTfd->sql_query_file();
              $rsSituacaoTfd   = db_query( $sSqlSituacaoTfd );

              if( $rsSituacaoTfd && pg_num_rows( $rsSituacaoTfd ) > 0 ) {

                for( $iContador = 0; $iContador < pg_num_rows( $rsSituacaoTfd ); $iContador++ ) {

                  $oDadosSituacao = db_utils::fieldsMemory( $rsSituacaoTfd, $iContador );
                  $aSituacoes[ $oDadosSituacao->tf26_i_codigo ] = $oDadosSituacao->tf26_c_descr;
                }
              }

              db_select( 'situacao', $aSituacoes, true, 1 );
              ?>
            </td>
          </tr>
        </table>
      </fieldset>
      <input id="btnImprimir" type="button" value="Imprimir" onclick="imprimir();" />
    </form>
  </body>
</html>

<?php
  db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>

<script>

function js_pesquisatf01_i_rhcbo(mostra) {

if (mostra == true) {

  js_OpenJanelaIframe('', 'db_iframe_rhcbo', 'func_rhcbosaude.php?funcao_js=parent.js_mostrarhcbo1|'+
                      'rh70_estrutural|rh70_descr|rh70_sequencial', 'Pesquisa', true
                     );

} else {

  if ($F("rh70_estrutural") != '') {

    js_OpenJanelaIframe('', 'db_iframe_rhcbo', 'func_rhcbosaude.php?pesquisa_chave='+
                        $F('rh70_estrutural')+'&funcao_js=parent.js_mostrarhcbo',
                        'Pesquisa', false
                       );

  } else {

     $("tf01_i_rhcbo").value = '';
     $("rh70_descr").value   = '';
     $('tf23_i_procedimento').value = $('sd63_c_nome').value = $('sd63_c_procedimento').value = '';

   }

}

}
function js_mostrarhcbo(chave1, chave2, chave3, erro) {

  $("rh70_estrutural").value = chave1;
  $("rh70_descr").value      = chave2;
  $("tf01_i_rhcbo").value    = chave3;

if (erro == true) {
  $("rh70_estrutural").focus();
}

}
function js_mostrarhcbo1(chave1, chave2, chave3) {

$("rh70_estrutural").value = chave1;
$("rh70_descr").value      = chave2;
$("tf01_i_rhcbo").value    = chave3;

db_iframe_rhcbo.hide();

}

/* AUTOCOMPLETE DESTINO */

$('tf03_c_descr').onkeydown = '';
oAutoCompleteDestino  = new dbAutoComplete($('tf03_c_descr'), 'sau4_autocompletesaude.RPC.php');
oAutoCompleteDestino.setTxtFieldId($('tf03_c_descr'));



oAutoCompleteDestino.setHeightList(180);
oAutoCompleteDestino.show();
oAutoCompleteDestino.setCallBackFunction(function(iId, sLabel) {

                                          $('tf03_i_codigo').value = iId;
                                          $('tf03_c_descr').value  = sLabel;
                                         }
                                        );
oAutoCompleteDestino.setQueryStringFunction(function() { 

                                              $('tf03_i_codigo').value = '';
                                              var oParamComplete       = new Object();
                                              oParamComplete.exec      = 'DesinoPedidoTFD';
                                              oParamComplete.string    = $('tf03_c_descr').value;
                                              return 'json='+Object.toJSON(oParamComplete); 
                                            } 
                                           );

/* FIM AUTOCOMPLETE DESTINO */

const MENSAGENS_RELATORIOVIAGENS001 = "saude.tfd.tfd2_relatorioviagens001.";

function pesquisaDestino() {

  js_OpenJanelaIframe(
                      '', 
                      'db_iframe_tfd_destino', 
                      'func_tfd_destino.php?funcao_js=parent.retornoPesquisaDestino|tf03_i_codigo|tf03_c_descr',
                      'Pesquisa',
                      true 
                     );

}

function retornoPesquisaDestino() {

  $('tf03_i_codigo').value = arguments[0];
  $('tf03_c_descr').value  = arguments[1];
  db_iframe_tfd_destino.hide();

}

function imprimir() {
  
  if ( empty( $F('periodoInicial') ) ) {

    alert( _M( MENSAGENS_RELATORIOVIAGENS001 + "informe_periodo_inicial" ) );
    return false;
  }

  if ( empty(  $F('periodoFinal') ) ) {

    alert( _M( MENSAGENS_RELATORIOVIAGENS001 + "informe_periodo_final" ) );
    return false;
  }

  if ( $F('periodoInicial') > $F('periodoFinal') ) {

    alert( _M( MENSAGENS_RELATORIOVIAGENS001 + "periodo_inicial_maior_periodo_final" ) );
    return false;
  }

  if ( $F('tf03_c_descr') == '' ) {
    $('tf03_i_codigo').value = '';
  }

  var sArquivo  = 'tfd2_relatoriodeviagens002.php?';
      sArquivo += 'dataInicial=' + $F('periodoInicial');
      sArquivo += '&dataFinal='  + $F('periodoFinal');
      sArquivo += '&iDestino='   + $F('tf03_i_codigo');
      sArquivo += '&iSituacao='  + $F('situacao');
      sArquivo += '&iEspecialidade=' + $F('rh70_estrutural');

 var oJan = window.open( sArquivo, '', 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+
                               ',scrollbars=1,location=0 ' );
     oJan.moveTo(0, 0);
}

$('situacao').style.width = 100;
</script>
