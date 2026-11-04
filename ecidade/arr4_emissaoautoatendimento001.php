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
  require_once(modification("dbforms/db_classesgenericas.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("classes/db_declaracaoquitacao_classe.php"));
  
  $clrotulo           = new rotulocampo;

  $clrotulo->label('k60_codigo');
  $clrotulo->label('k60_descr');
  $clrotulo->label('ar11_sequencial');
  $clrotulo->label('ar11_nome');

  $dataInicial = new DateTime();
  $dataInicial->modify("+1 day");

  $lDiaUtilIni = false;
  while (!$lDiaUtilIni ) {
    $dataIni = $dataInicial->format('Y-m-d');
    $dataVerificarIni = new \DBDate($dataIni);
    $diaSemanaIni = $dataVerificarIni->diaUtil();
    if ($diaSemanaIni) {
      $lDiaUtilIni = true;
    } else {
      $dataInicial->modify("+1 day");
    }
  }

  $dataFinal = clone $dataInicial;
  $dataFinal->modify("+1 month");
  $dataFinal->setDate($dataFinal->format('Y'), $dataFinal->format('m'), 1);
  $dataFinal->modify("-1 day");

  $lDiaUtilFim = false;
  while (!$lDiaUtilFim ) {
    $dataFim = $dataFinal->format('Y-m-d');
    $dataVerificarFim = new \DBDate($dataFim);
    $diaSemanaFim = $dataVerificarFim->diaUtil();
    if ($diaSemanaFim) {
      $lDiaUtilFim = true;
    } else {
      $dataFinal->modify("+1 day");
    }
  }

  $diaDataInicial = $dataInicial->format('d');
  $mesDataInicial = $dataInicial->format('m');
  $anoDataInicial = $dataInicial->format('Y');
  
  $diaDataFinal = $dataFinal->format('d');
  $mesDataFinal = $dataFinal->format('m');
  $anoDataFinal = $dataFinal->format('Y');
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
      db_app::load("scripts.js");
      db_app::load("strings.js");
      db_app::load("prototype.js");
      db_app::load("estilos.css");
      db_app::load("AjaxRequest.js");
      db_app::load("widgets/DBLookUp.widget.js");
    ?>
  </head>
  <body bgcolor=#CCCCCC>
  <div class="container">
    <form name="form1">
      <fieldset>
        <legend>
          <strong>Emissão</strong>
        </legend>
        <table class="form-container">
          <tr>
            <td nowrap title="<?php echo $Tk60_codigo?>" >
              <?php
                db_ancora($Lk60_codigo, "js_pesquisalista(true);", 4);
              ?>
            </td>

            <td>
              <?php
                db_input("k60_codigo",  4, $Ik60_codigo, true, "text", 4, "class=\"field-size2\" onchange='js_pesquisalista(false);'");
                db_input("k60_descr",  40, $Ik60_descr,  true, "text", 3, "class=\"field-size8\"");
              ?>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <fieldset>
                <legend>
                  <strong>Vigência</strong>
                </legend>
                <table>
                  <tr>
                    <td title="Data Inicial">
                      <strong>De:</strong>
                      <?php db_inputdata('datainicial', $diaDataInicial, $mesDataInicial, $anoDataInicial, true, 'text', 1); ?>
                      <strong>à</strong>
                      <?php db_inputdata('datafinal', $diaDataFinal, $mesDataFinal, $anoDataFinal, true, 'text', 1); ?>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
      </fieldset>
      <div id="buttons" class="container">
        <input type="button" name="processar" value="Processar" onclick= "emitirArquivoAutoAtendimento()"/>
      </div>
      <div id="arquivo" class="container"></div>
    </form>
  </div>

<script type="text/javascript">

  function js_pesquisalista(mostra){
    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lista','func_lista.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr','Pesquisa',true);
    }else{
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lista','func_lista.php?pesquisa_chave='+document.form1.k60_codigo.value+'&funcao_js=parent.js_mostralista','Pesquisa','false');
    }
  }

  function js_mostralista(chave,erro){
    document.form1.k60_descr.value = chave;
    if(erro==true){
      document.form1.k60_descr.focus();
      document.form1.k60_descr.value = '';
    }
    db_iframe_lista.hide();
  }

  function js_mostralista1(chave1,chave2){
    document.form1.k60_codigo.value = chave1;
    document.form1.k60_descr.value = chave2;
    db_iframe_lista.hide();
  }


  function emitirArquivoAutoAtendimento() {

      AjaxRequest.create(
          'arr4_emissaoautoatendimento.RPC.php',
          {
              'exec' : 'getCodigoConvenio',
              'dini' : $F('datainicial'),
              'dfim' : $F('datafinal')
          },
          function(retorno, erro) {

              if(retorno.mensagem) {
                  alert(retorno.mensagem);
              }
              
              if(erro) {
                  return;
              }

              var queryString,  destinoLookUp, objetoLookUp;
                  queryString  = 'arr4_emissaoautoatendimento002.php';
                  queryString += '?codigoLista='    + $F('k60_codigo');
                  queryString += '&datainicial='    + $F('datainicial');
                  queryString += '&datafinal='      + $F('datafinal');
                  queryString += '&codigoConvenio=' + retorno.codigoConvenio;

                  objetoLookUp = 'iframe_emissao';

              var oJanela = js_OpenJanelaIframe(
                  'CurrentWindow.corpo',
                  objetoLookUp,
                  queryString,
                  'Emitindo arquivo de débitos',
                  true
              );
          }
      ).setMessage("Aguarde, buscando débitos").execute();
  }
</script>
<?php db_menu(); ?>
</body>
</html>
 
