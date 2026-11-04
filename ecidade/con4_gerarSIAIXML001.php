<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clOrcUnidade = new cl_orcunidade;
$clDBConfig = new cl_db_config();

$unidades = [];

$rsInstit = $clDBConfig->sql_record($clDBConfig->sql_query_file(db_getsession("DB_instit"), "nomeinst"));
$oLinhaInstit = db_utils::fieldsMemory($rsInstit, 0);
$id = "00.".db_getsession("DB_instit");
$conteudo = "00.".str_pad(db_getsession("DB_instit"), 2, "0", STR_PAD_LEFT)." - ".$oLinhaInstit->nomeinst;
$unidades[$id] = $conteudo;

$whereUnidades = "o41_instit = ".db_getsession("DB_instit");
$whereUnidades .= " and o41_anousu = ".db_getsession("DB_anousu");
$whereUnidades .= " and o41_codigotribunalxml is not null";
$sqlOrcUnidade = $clOrcUnidade->sql_query_file(
    null,
    null,
    null,
    "o41_orgao as orgao, o41_unidade as unidade, o41_descr",
    "o41_orgao, o41_unidade",
    $whereUnidades
    );
$rsUnidades = $clOrcUnidade->sql_record($sqlOrcUnidade);

for ($i = 0; $i < $clOrcUnidade->numrows; $i++) {
    $oLinhaUnidade = db_utils::fieldsMemory($rsUnidades, $i);
    
    $id = $oLinhaUnidade->orgao.".".$oLinhaUnidade->unidade;
    $conteudo  = $oLinhaUnidade->orgao.".".str_pad($oLinhaUnidade->unidade, 2, "0", STR_PAD_LEFT);
    $conteudo .= " - ".$oLinhaUnidade->o41_descr;
    $unidades[$id] = $conteudo;
}

$periodo = [
             17 => "JANEIRO",
             18 => "FEVEREIRO",
             19 => "MARÇO",
             20 => "ABRIL",
             21 => "MAIO",
             22 => "JUNHO",
             23 => "JULHO",
             24 => "AGOSTO",
             25 => "SETEMBRO",
             26 => "OUTUBRO",
             27 => "NOVEMBRO",
             28 => "DEZEMBRO"
           ];

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body>
  <div class="container">
    <fieldset id='siai'>
      <legend>Gerar SIAI XML</legend>
        <table class="form-container">
          <tr>
            <td colspan="2">

              <table class="form-container">
                <tr>
                  <td>Período:</td>
                  <td>
                    <?php
                      db_select("periodo", $periodo, true, 2);
                    ?>
                  </td>
                  <td>Unidade:</td>
                  <td>
                    <?php
                        db_select("orgaoUnidade", $unidades, true, 2, 'onchange="getDadosTCE()"');
                    ?>
                  </td>
                </tr>
                <tr>
                  <td>Cód. TCE:</td>
                  <td><input type="text" maxlength="4" size="4" id='codUnidadeTCE' name="codUnidadeTCE"></td>
                  <td>Nome TCE:</td>
                  <td><input type="text" maxlength="50" size="60" id='nomeUnidadeTCE' name="nomeUnidadeTCE"></td>
                </tr>
              </table>            
            
            </td>
          </tr>
          <tr>
            <td valign="top">
            
               <fieldset id='field-xml'>
                 <legend>Arquivos XML</legend>
                 <table id='arquivos'>
                   <tr>
                     <td>
                       <input type="checkbox" id='empenhos_xml' value='empenhos_xml' name="Anexo14XML" />
                     </td>
                     <td>
                       <label for="Empenhos XML">Anexo 14 - Empenhos</label>
                     </td>
                   </tr>
                  <tr>
                     <td>
                       <input type="checkbox" 
                              id='empenhos_dados_complementares_xml' 
                              value='empenhos_dados_complementares_xml' 
                              name="Anexo14DadosComplementaresXML" />
                     </td>
                     <td>
                       <label for="Empenhos Dados Complementares XML">Anexo 14 - Dados Complementares</label>
                     </td>
                   </tr>            
                 </table>  
               </fieldset>            
               
               <?php
                if (db_getsession("DB_anousu") >= 2020) {
                    ?>
                  <fieldset id='fieldset_retificacoes'>
                   <legend>  Anexo 14 - Empenhos - Informações de Retificação </legend>
                   <fieldset>
                     <legend> Lista de Empenhos: </legend>
                     <input type="text" name="listaEmpenho" id="listaEmpenho" size=100>
                   </fieldset>
                   <fieldset>
                     <legend> Lista de Liquidações: </legend>
                     <input type="text" name="listaLiquidacao" id="listaLiquidacao" size=100>
                   </fieldset>
                   <fieldset>
                     <legend> Lista de Pagamentos: </legend>
                     <input type="text" name="listaPagamento" id="listaPagamento" size=100>
                   </fieldset>
                  </fieldset>
                    <?php
                } else {
                    echo "<input type='hidden' name='listaEmpenho'    id='listaEmpenho'>";
                    echo "<input type='hidden' name='listaLiquidacao' id='listaLiquidacao'>";
                    echo "<input type='hidden' name='listaPagamento'  id='listaPagamento'>";
                }
                ?>   
        
            </td>
            
            <td valign="top">

               <fieldset id='field-gerados'>
                 <legend>Arquivos Gerados</legend>
                 <div style='overflow:auto; text-align: left;' id='retorno'></div>
               </fieldset>
            
            </td>

          </tr>
        </table>
    </fieldset>
    <input type="button"
           id='selecionar-todos' 
           value='Selecionar Todos' 
           name='Selecionar Todos'
           onclick="js_marcaTodos();"/>
    <input type="button" id='limpar-selecao' value='Limpar Seleção' name='Limpar Seleção' onclick="js_desmarcar();" />
    <input type="button" id='processar' value='Processar' name='Processar' onclick="js_processar();" />
  </div>
</body>
</html>
<?php db_menu()?>

<script type="text/javascript">

$("field-xml").style = "height: 80px;";
$("field-gerados").style = "height: 300px; width: 300px;";

var oToogleRetificacoes = new DBToogle('fieldset_retificacoes', false);

var sURL = "con4_processarSIAI.RPC.php";

window.onload = function() {
    getDadosTCE(); 
}

function getDadosTCE() {
  var oParam           = new Object();
  oParam.exec          = "getDadosTCE";
  if ($('orgaoUnidade')) {
    var codigo_unidade = $F('orgaoUnidade').split(".");
    oParam.orgao   = codigo_unidade[0];
    oParam.unidade = codigo_unidade[1];
  }

  js_divCarregando('Aguarde, obtendo os dados', 'msgBox');
  var oAjax = new Ajax.Request(sURL,
    {
      method:'post',
      parameters:'json='+Object.toJSON(oParam),
      onComplete:retornoDadosTCE
    });
}

function retornoDadosTCE(oAjax) {
  js_removeObj('msgBox');
  var oRetorno = eval("("+oAjax.responseText+")");
  if (oRetorno.status == 1) {
    $('codUnidadeTCE').value  = oRetorno.codigoOrgaoXml;
    $('nomeUnidadeTCE').value = oRetorno.nomeUnidade;
  } else {
      alert(oRetorno.msg);
  }

  if ($F('codUnidadeTCE') == "") {
      alert("Código do TCE para a unidade informada não encontrado, por favor preencha manualmente");
      $('codUnidadeTCE').focus();
      return false;
  }

  if ($F('nomeUnidadeTCE') == "") {
      alert("Nome do orgão/unidade do TCE não informado no cadastro da unidade, por favor preencha manualmente");
      $('nomeUnidadeTCE').focus();
      return false;
  }
}

function marcaTodos() {
  var aCheckboxes = $$('input[type=checkbox]');
    aCheckboxes.each(function(oCheckbox) {
    oCheckbox.checked = true;
  });
}

function desmarcar() {
  var aCheckboxes = $$('input[type=checkbox]');
    aCheckboxes.each(function (oCheckbox) {
    oCheckbox.checked = false;
  });
}


function js_processar() {
  msg = "Deseja gerar os dados selecionados para a unidade ";
  msg += document.getElementById('orgaoUnidade').options[document.getElementById('orgaoUnidade').selectedIndex].text; 
  msg += " no período "+document.getElementById('periodo').options[
      document.getElementById('periodo').selectedIndex].text+"?";
  if(!confirm(msg)) {
    return false;
  }

  var oParam           = new Object();
  oParam.exec          = "processarSiai";
  oParam.periodo      = $F('periodo');
  if ($('orgaoUnidade')) {
    var codigo_unidade = $F('orgaoUnidade').split(".");
    oParam.orgao   = codigo_unidade[0];
    oParam.unidade = codigo_unidade[1];
  }
  oParam.codigoOrgaoTCE    = document.getElementById('codUnidadeTCE').value;
  oParam.codigoOrgaoTCEXml = document.getElementById('codUnidadeTCE').value;
  oParam.nomeUnidadeTCE    = document.getElementById('nomeUnidadeTCE').value;
  oParam.listaEmpenho      = document.getElementById('listaEmpenho').value;
  oParam.listaLiquidacao   = document.getElementById('listaLiquidacao').value;
  oParam.listaPagamento    = document.getElementById('listaPagamento').value;
  oParam.aArquivos  = new Array();
  var aArquivos     = $$("input[type='checkbox']");
  aArquivos.each(function (oCheckbox, id) {

    with (oCheckbox) {

      if (checked) {
        oParam.aArquivos.push(oCheckbox.name);
      }
    }

  });

  if ((oParam.aArquivos.length == 0) 
          && (oParam.listaEmpenho == "" && oParam.listaLiquidacao == "" && oParam.listaPagamento == "")
     ) {
      
    alert("Selecione ao menos uma Opção.");
    return false;
    
  }      

  js_divCarregando('Aguarde, Processando Arquivos', 'msgBox');
  var oAjax = new Ajax.Request(sURL,
                               {
                                 method:'post',
                                 parameters:'json='+Object.toJSON(oParam),
                                 onComplete:js_retornoProcessaSiai
                               }
                             );
}

function js_retornoProcessaSiai(oAjax) {

  js_removeObj('msgBox');
  var oRetorno = eval("("+oAjax.responseText+")");
  if (oRetorno.status == 1) {

    var sRetorno = "";
    
    for (var i = 0; i < oRetorno.lista.length; i++) {

      with (oRetorno.lista[i]) {

       sRetorno += "<a target='_blank' href='db_download.php?arquivo="+caminho+"'>"+nome+"</a><br>";
      }
    }

    $('retorno').innerHTML = sRetorno;
  } else {

    $('retorno').innerHTML = '';
    alert(oRetorno.message.urlDecode());
    return false;
  }
}

</script>
