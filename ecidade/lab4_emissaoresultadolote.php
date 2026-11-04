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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('libs/db_utils.php'));

$cllab_labsetor   = new cl_lab_labsetor;
$cllab_requisicao = new cl_lab_requisicao;
$cllab_exame      = new cl_lab_exame;
$clrotulo         = new rotulocampo;

$clrotulo->label("la09_i_exame");
$clrotulo->label("la24_i_setor");
$clrotulo->label("la02_i_codigo");
$clrotulo->label("la24_c_descr");
$clrotulo->label("la23_c_descr");
$clrotulo->label("la23_i_codigo");

$iUsuario = db_getsession('DB_login');
$iDepto   = db_getsession('DB_coddepto');
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <!-- PLUGIN AUTENTICADORA - Adicionando script scripts/autenticadora-client.js -->
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
<body class="body-default">

  <div class="container">

    <form name='form1'>

      <fieldset style="width: 540px">
        <legend>Emissão/Reemissão de Resultado em Lote</legend>
        <table class="form-container">
          <tr>
            <td class="bold">
              Período:
            </td>
            <td>
              <?php db_inputdata( 'data1', @$dia1, @$mes1, @$ano1, true, 'text', 1 );?>
                 À
              <?php db_inputdata( 'data2', @$dia2, @$mes2, @$ano2, true, 'text', 1 )?>
            </td>
          </tr>
          <tr>
            <td nowrap title="Laborat&oacute;rio">
               <?php
               db_ancora('<strong>Laborat&oacute;rio:</strong>', "js_pesquisala02_i_laboratorio(true);", "");
               ?>
            </td>
            <td>
            <?php
              db_input( 'la02_i_codigo', 10, $Ila02_i_codigo, true, 'text', "", "onchange='js_pesquisala02_i_laboratorio(false);'");
              db_input( 'la02_c_descr',  50, @$Ila02_c_descr, true, 'text', 3 );
            ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?=@$Tla24_i_setor?>">
              <?php
              db_ancora( @$Lla24_i_setor, "js_pesquisala24_i_setor(true);", "" );
              ?>
            </td>
            <td>
            <?php
              db_input( 'la23_i_codigo',  10, $Ila23_i_codigo,  true, 'text', "", "onchange='js_pesquisala24_i_setor(false);'");
              db_input( 'la23_c_descr',  50,  $Ila23_c_descr, true, 'text', 3 );
            ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?=@$Tla09_i_exame?>">
              <?php
                db_ancora ( @$Lla09_i_exame, "js_pesquisala09_i_exame(true);", "" );
              ?>
            </td>
            <td>
            <?php
              db_input( 'la09_i_exame',  10, @$Ila09_i_exame, true, 'text', "", "onchange='js_pesquisala09_i_exame(false);'" );
              db_input( 'la09_i_codigo', 10, '',              true, 'hidden' );
              db_input( 'la08_c_descr',  50, @$Ila08_c_descr, true, 'text', 3 );
            ?>
            </td>
          </tr>
          <tr>
            <td>
                <strong>Impressão:</strong>
            </td>
            <td>
            <?php
              $aParam = Array(
                "0" => "SELECIONE",
                "1" => "PDF",
                "2" => "MATRICIAL",
              );
              db_select( 'atributos', $aParam, "", 1 );
            ?>
            </td>
          </tr>
        </table>

      </fieldset>

      <input name='start' type='button' value='Gerar' onclick="js_mandaDados()"/>
    </form>
  </div>
  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>
</html>
<script type="text/javascript">

function js_limpaCamposTrocaLab() {

  document.form1.la23_i_codigo.value  = '';
  document.form1.la23_c_descr.value  = '';
  js_limpaCamposTrocaSetor();
}

function js_limpaCamposTrocaSetor() {

  document.form1.la09_i_exame.value = '';
  document.form1.la08_c_descr.value = '';
}


function js_pesquisala02_i_laboratorio(mostra) {

  if ( mostra == true ) {

    js_OpenJanelaIframe(
                         '',
                         'db_iframe_lab_laboratorio',
                         'func_lab_laboratorio.php?checkLaboratorio=true'
                                                +'&funcao_js=parent.js_mostralaboratorio1|la02_i_codigo|la02_c_descr',
                         'Pesquisa',
                         true
                       );
  } else {

    if ( document.form1.la02_i_codigo.value != '' ) {

       js_OpenJanelaIframe(
                            '',
                            'db_iframe_lab_laboratorio',
                            'func_lab_laboratorio.php?checkLaboratorio=true'
                                                   +'&pesquisa_chave='+document.form1.la02_i_codigo.value
                                                   +'&funcao_js=parent.js_mostralaboratorio',
                            'Pesquisa',
                            false
                          );
    } else {
      document.form1.la02_c_descr.value = '';
    }
  }
}

function js_mostralaboratorio( chave, erro ) {

  document.form1.la02_c_descr.value = chave;

  if ( erro == true ) {

    document.form1.la02_i_codigo.focus();
    document.form1.la02_i_codigo.value = '';
    return;
  }
  js_apagaSetor();
  js_limpaCamposTrocaSetor();
}

function js_mostralaboratorio1( chave1, chave2 ) {

  document.form1.la02_i_codigo.value = chave1;
  document.form1.la02_c_descr.value  = chave2;
  js_apagaSetor();
  js_limpaCamposTrocaSetor();
  db_iframe_lab_laboratorio.hide();
}

function js_pesquisala24_i_setor(mostra) {


  var sUrl  = 'func_lab_setor.php?';
  if(document.form1.la02_i_codigo.value.length > 0){
    sUrl += '&laboratorio='+document.form1.la02_i_codigo.value;
  }
  if(document.form1.la09_i_exame.value.length > 0){
    sUrl += '&exames='+document.form1.la09_i_exame.value;
  }

  if ( mostra == true ) {

    sUrl += '&funcao_js=parent.js_mostralab_labsetor1|la23_i_codigo|la23_c_descr';
    js_OpenJanelaIframe('', 'db_iframe_lab_setor', sUrl, 'Pesquisa de Setores', true );
  } else {

    if( $F('la23_i_codigo') != '' ) {

      sUrl += '&pesquisa_chave='+$F('la23_i_codigo');
      sUrl += '&funcao_js=parent.js_mostralab_labsetor';
      js_OpenJanelaIframe('', 'db_iframe_lab_setor', sUrl, 'Pesquisa de Setores', false);
    } else {

      $('la23_c_descr').value  = '';
      $('la23_i_codigo').value = '';
    }
  }
}

function js_mostralab_labsetor( chave, erro) {

  document.form1.la23_c_descr.value  = chave;

  if( erro == true ) {

    document.form1.la23_i_codigo.focus();
    document.form1.la23_i_codigo.value = '';
    return;
  }
}

function js_mostralab_labsetor1( chave1, chave2) {

  document.form1.la23_i_codigo.value = chave1;
  document.form1.la23_c_descr.value  = chave2;
  db_iframe_lab_setor.hide();
}

function js_pesquisala09_i_exame(mostra) {

  var sUrl = 'func_lab_setorexame.php?';
  
  if(document.form1.la02_i_codigo.value.length > 0){
    sUrl += '&la02_i_codigo='+document.form1.la02_i_codigo.value;
  }
  if(document.form1.la23_i_codigo.value.length > 0){
    sUrl += '&la23_i_codigo='+document.form1.la23_i_codigo.value;
  }
        
  if( mostra == true ) {
    sUrl += '&funcao_js=parent.js_mostralab_exame1|la08_i_codigo|la08_c_descr|la09_i_codigo';
    js_OpenJanelaIframe('', 'db_iframe_lab_setorexame', sUrl, 'Pesquisa Exames', true );
  } else {

    if( $F('la09_i_exame') != '' ) {

      sUrl += '&pesquisa_chave='+$F('la09_i_exame');
      sUrl += '&funcao_js=parent.js_mostralab_exame'

      js_OpenJanelaIframe('', 'db_iframe_lab_setorexame', sUrl, 'Pesquisa Exames', false);
    } else {

      document.form1.la08_c_descr.value  = '';
      document.form1.la09_i_codigo.value = '';
    }
  }
}

function js_mostralab_exame( chave, erro, chave2 ) {

  document.form1.la08_c_descr.value  = chave;
  document.form1.la09_i_codigo.value = chave2;

  if ( erro == true ) {

    document.form1.la09_i_exame.focus();
    document.form1.la09_i_exame.value = '';
  }
}

function js_mostralab_exame1( chave1, chave2, chave3 ) {

  document.form1.la09_i_exame.value  = chave1;
  document.form1.la08_c_descr.value  = chave2;
  document.form1.la09_i_codigo.value = chave3;
  db_iframe_lab_setorexame.hide();
}

function js_apagaSetor(){
  document.form1.la23_i_codigo.value = '';
  document.form1.la23_c_descr.value  = '';
}

function js_mandaDados() {

  if ( document.form1.data1.value == "" && document.form1.data2.value == "" ) {

     alert("Preencha um Período.");
     document.form1.data1.focus();
     return false;
  }

  if ( $F('data1') != '' && $F('data2') == '' ) {

    alert("Informe o período final.");
    return false;
  }

  if (  $F('data1') == '' && $F('data2') != '' ) {

    alert("Informe o período inicial.");
    return false;
  }

  if (js_comparadata($F('data1'), $F('data2'), ">")) {
    alert("Período inicial não pode ser maior que o período final.");
    return false;
  }

  if (document.form1.atributos.value == '0') {
    alert('Selecione um Modelo de Impressão.');
    return false;
  }

  var sDatas = '';
  if ($F('data1') != "" && $F('data2') != "" ) {
    sDatas = $F('data1') + ','+$F('data2');
  }

  var mensagemConfirmacao = "Deseja realmente imprimir todos os resultados de exames? Poderá haver muitas páginas";
  mensagemConfirmacao += " para impressão, você confirma esta operação?";

  if (!confirm(mensagemConfirmacao)) {
    return false;
  }

  var oF          = document.form1;
  var sParametros  = '';
      sParametros += 'datas='+sDatas;
      sParametros += '&laboratorio='+oF.la02_i_codigo.value;
      sParametros += '&labsetor='+oF.la23_i_codigo.value;
      sParametros += '&exame='+oF.la09_i_exame.value;
      sParametros += '&iAtributo='+oF.atributos.value;

  if (document.form1.atributos.value == '2') {
      new AjaxRequest('lab4_emissaoresultadoloteimpressao.php?' + sParametros , {}, function(retorno, erro) {

          if (erro || !retorno.utilizarAutenticadoraNova) {
              alert(retorno.mensagem);
              return false;
          }

          // PLUGIN AUTENTICADORA - Conectando com AutenticadoraClient
      }).setMessage("Aguarde, gerando os dados para impressão...").execute();
  }

  if (document.form1.atributos.value == '1') {
      var jan = window.open(
          'lab4_emissaoresultadoloteimpressao.php?' + sParametros,
          '',
          'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0'
      );
      jan.moveTo( 0, 0 );
  }
}

function js_validadata() {

  if( document.form1.data1.value != '' && document.form1.data2.value != '' ) {

    aIni = document.form1.data1.value.split('/');
    aFim = document.form1.data2.value.split('/');
    dIni = new Date(aIni[2], aIni[1], aIni[0]);
    dFim = new Date(aFim[2], aFim[1], aFim[0]);

    if(dFim < dIni) {

      alert("Data final não pode ser menor que a data inicial.");
      document.form1.data2.value = '';
      return false;
    }

    return true;
  } else {

    alert('Preencha o período.');
    return false
  }
}


$('data1').addEventListener('drop', js_changeValue.bind(this, $('data1').id));
$('data2').addEventListener('drop', js_changeValue.bind(this, $('data2').id));

function js_changeValue(sId, oEvent) {

  oEvent.preventDefault();
  oEvent.stopImmediatePropagation();

  return false;


}
</script>
