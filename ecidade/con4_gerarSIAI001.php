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

$sqlOrcUnidade = $clOrcUnidade->sql_query_file(
    null,
    null,
    null,
    "o41_orgao as orgao, o41_unidade as unidade, o41_descr",
    "o41_orgao, o41_unidade",
    "o41_instit = ".db_getsession("DB_instit")." and o41_anousu = ".db_getsession("DB_anousu")
);
$rsUnidades = $clOrcUnidade->sql_record($sqlOrcUnidade);

for ($i = 0; $i < $clOrcUnidade->numrows; $i++) {
    $oLinhaUnidade = db_utils::fieldsMemory($rsUnidades, $i);
    
    $id = $oLinhaUnidade->orgao.".".$oLinhaUnidade->unidade;
    $conteudo  = $oLinhaUnidade->orgao.".".str_pad($oLinhaUnidade->unidade, 2, "0", STR_PAD_LEFT);
    $conteudo .= " - ".$oLinhaUnidade->o41_descr;
    $unidades[$id] = $conteudo;
}

$periodo = [6  => "1º Bimestre",
            7  => "2º Bimestre",
            8  => "3º Bimestre",
            9  => "4º Bimestre",
            10 => "5º Bimestre",
            11 => "6º Bimestre"];
?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
     .divRetorno {
         overflow:auto; 
         text-align: left;  
         line-height: 22px; 
         background-color: #FFFFFF; 
         padding: 10px;
         padding-left: 15px;
       }
     .linhaBotoes {
       text-align:center;
       height:40px;
     }  
    </style>
  </head>
  <body>
  <div class="container">
    <table>
     <tr>
      <td>
        <fieldset>
          <legend>Gerar SIAI</legend>
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
                    </tr>
                    <tr>  
                      <td>Orgão/Unidade:</td>
                      <td>
                        <?php
                            db_select("orgaoUnidade", $unidades, true, 2, 'onchange="getDadosTCE()"');
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td>Código/Nome TCE:</td>
                      <td>
                        <input type="text" maxlength="4" size="8" id='codUnidadeTCE' name="codUnidadeTCE">
                        <input type="text" maxlength="50" size="75" id='nomeUnidadeTCE' name="nomeUnidadeTCE">
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Desvincular dados da camara nos arquivos gerados:
                      </td>
                      <td>  
                        <?php
                          db_select(
                              "desvincularDadosInstituicaoCamara",
                              [true => "SIM", false  => "NÃO"],
                              true,
                              1,
                              "style='width: 80px'"
                          );
                            ?> 
                      </td>  
                    </tr>
                  </table>            
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <input type="button" id='selecionar-todos' value='Selecionar Todos' onclick="marcaTodos();"/>
                  <input type="button" id='limpar-selecao' value='Limpar Seleção' onclick="desmarcar();" />                  
                </td>
              </tr>
              <tr>
                <td>
                   <fieldset id='field-arquivos-rreo'>
                     <legend>RREO</legend>
                     <table id='arquivos-rreo' width=100%>
                       <tr>
                         <td>
                           <input type="checkbox" id='receita' value='Receita'>
                         </td>
                         <td>
                           <label for="Receita">Anexo I - Receita</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='despesa' value='Despesa'>
                         </td>
                           <td>
                           <label for="Despesa">Anexo I - Despesa</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-3-rreo' value='anexo-3-rreo' />
                         </td>
                           <td>
                           <label for="anexo-3-rreo">Anexo III - Receita Corrente Líquida - RCL</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-4-rreo' value='anexo-4-rreo' />
                         </td>
                           <td>
                           <label for="anexo-4-rreo">
                             Anexo IV - Demonstrativo das Receitas e Despesas do RPPS
                           </label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-6-rreo' value='anexo-6-rreo' />
                         </td>
                           <td>
                           <label for="anexo-6-rreo">
                             Anexo VI - Demonstrativo dos Resultados Primário e Nominal
                           </label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-7-rreo' value='anexo-7-rreo' />
                         </td>
                           <td>
                           <label for="anexo-7-rreo">Anexo VII - Demonstrativo dos Restos a Pagar</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-8-rreo' value='anexo-8-rreo' />
                         </td>
                           <td>
                           <label for="anexo-8-rreo">
                             Anexo VIII - Demonstrativo de Receitas. e Despesas MDE (FUNDEB)
                           </label>
                         </td>
                       </tr>                                                                            
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-12-rreo' value='anexo-12-rreo' />
                         </td>
                           <td>
                           <label for="anexo-12-rreo">
                             Anexo XII - Demonstrativo Saúde
                           </label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-13-rreo' value='anexo-13-rreo' />
                         </td>
                           <td>
                           <label for="anexo-13-rreo">
                             Anexo XIII - Demonstrativo das PPPs
                           </label>
                         </td>
                       </tr>                 
                     </table>
                   </fieldset>
                </td>
                <td>
                   <fieldset id='field-arquivos-rgf'>
                     <legend>RGF</legend>
                     <table id='arquivos-rgf' width=100%>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-1-rgf' value='anexo-1-rgf'>
                         </td>
                         <td>
                           <label for="anexo-1-rgf">Anexo I - Demonstrativo da Despesa com Pessoal</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-2-rgf' value='anexo-2-rgf'>
                         </td>
                         <td>
                           <label for="anexo-2-rgf">Anexo II - Demonstrativo da Dívida Consolidada Líquida</label>
                         </td>
                       </tr>   
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-3-rgf' value='anexo-3-rgf'>
                         </td>
                         <td>
                           <label for="anexo-3-rgf">Anexo III - Demonstrativo das Garantias e Contragarantias de Valores</label>
                         </td>
                       </tr> 
                       <tr>
                         <td>
                           <input type="checkbox" id='anexo-4-rgf' value='anexo-4-rgf'>
                         </td>
                         <td>
                           <label for="anexo-4-rgf">Anexo IV - Demonstrativo das Operações de Crédito</label>
                         </td>
                       </tr>                                                               
                     </table>                 
                   </fieldset>               
                </td>
                <td>
                   <fieldset id='field-arquivos'>
                     <legend>Outros arquivos</legend>
                     <table id='arquivos' width=100%>
                       <tr>
                         <td>
                           <input type="checkbox" id='pessoas' value='Pessoas'>
                         </td>
                         <td>
                           <label for="Pessoas">Pessoas</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='empenhos' value='Empenhos'>
                         </td>
                           <td>
                           <label for="Empenhos">Anexo XIV - Empenhos</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='suprimento' value='SuprimentoFundos'>
                         </td>
                         <td>
                           <label for="Suprimento">Anexo XXV - Suprimento de Fundos</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='contacorrente' value='ContaCorrente' />
                         </td>
                         <td>
                           <label for="ContaCorrente">Anexo XXVI - Conta Corrente</label>
                         </td>
                       </tr>
                       <tr>
                         <td>
                           <input type="checkbox" id='depesapessoalfundeb' value='DespesaPessoalFundeb'>
                         </td>
                         <td>
                           <label for="DespesaPessoalFundeb">Anexo XXVII - Despesa com Pessoal do Fundeb</label>
                         </td>
                       </tr>
                     </table>
                   </fieldset>
                </td>
              </tr>
            </table>
        </fieldset>
      </td>            
     </tr>
     <tr>
      <td colspan="3">
       <div class='linhaBotoes'>
        <input type="button" 
               id='processar' 
               value='Processar' 
               name='Processar' 
               onclick="processarSIAI();" />
       </div>
      </td>
     </tr>
     <tr>
       <td colspan="4">

          <fieldset id='field-gerados' style="display: none;">
            <legend>Arquivos Gerados</legend>
            <div id='retorno' class='divRetorno'></div>
          </fieldset>
       
       </td>
     </tr>
     <tr>
       <td colspan="4">
         <form name="formImportacaoArquivosSIAI" 
               id='formImportacaoArquivosSIAI' 
               enctype="multipart/form-data" 
               method="post">
           <fieldset id='fieldset_importacaoDePara'>
             <legend>  Importação de Arquivos "De-Para" </legend>
             <fieldset>
               <legend> Anexo I - Receita [Elementos da Receita]  </legend>
                 <table>
                   <tr>
                     <td id="ctnAnexoReceita"></td>
                     <td><input type="button" id="btnImportarArquivoReceita" value="Importar"></td>
                   </tr>
                 </table>
             </fieldset>
           </fieldset>  
         </form>
       </td>
     </tr>
    </table>
  </div>
<?php db_menu();?>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBFileUpload.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>  
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
$("field-arquivos").style = "height: 300px;";
$("field-arquivos-rreo").style = "height: 300px;";
$("field-arquivos-rgf").style = "height: 300px;";

const oToogleImportacaoArquivo = new DBToogle('fieldset_importacaoDePara', false);
const sURL = "con4_processarSIAI.RPC.php";
const anexosNovoProcessamento = ["anexo-3-rreo",
    "anexo-4-rreo",
    "anexo-6-rreo",
    "anexo-7-rreo",
    "anexo-8-rreo",
    "anexo-12-rreo",
    "anexo-13-rreo",
    "anexo-1-rgf",
    "anexo-2-rgf",
    "anexo-3-rgf",
    "anexo-4-rgf"
    ];
const exercicio = <?php echo db_getsession("DB_anousu");?>;
const instituicao = <?php echo db_getsession("DB_instit");?>;

const fileUploadAnexoReceita = new DBFileUpload({callBack: retornoEnvioArquivoAnexoReceita, labelButton: 'Arquivo'});
function retornoEnvioArquivoAnexoReceita() {
  if (retorno.error) {
      alert(retorno.error);
      return false;
  }
}
fileUploadAnexoReceita.show($('ctnAnexoReceita'));

$('btnImportarArquivoReceita').observe('click', importarArquivoReceita);

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
    $('codUnidadeTCE').value  = oRetorno.codigoOrgao;
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

function verificaNovoProcessamentoAnexos(idProcessamento) {
    if (anexosNovoProcessamento.includes(idProcessamento)) {
        return true;    
    }
    return false;
}

function processarSIAI() {
  msg = "Deseja gerar os dados selecionados para a unidade ";
  msg += document.getElementById('orgaoUnidade').options[document.getElementById('orgaoUnidade').selectedIndex].text; 
  msg += " no período "+document.getElementById('periodo').options[
      document.getElementById('periodo').selectedIndex].text+"?";
  if(!confirm(msg)) {
    return false;
  }

  lNovoProcessamento = false;
  
  $('retorno').innerHTML = '';

  var oParam           = new Object();
  oParam.exec          = "processarSiai";
  oParam.periodo      = $F('periodo');
  oParam.codigoOrgaoTCE = document.getElementById('codUnidadeTCE').value;
  oParam.nomeUnidadeTCE = document.getElementById('nomeUnidadeTCE').value;
  if ($('orgaoUnidade')) {
    var codigo_unidade = $F('orgaoUnidade').split(".");
    oParam.orgao   = codigo_unidade[0];
    oParam.unidade = codigo_unidade[1];
  }
  oParam.aArquivos  = new Array();
  
  var aArquivos     = $$("input[type='checkbox']");
  aArquivos.each(function (oCheckbox, id) {

    with (oCheckbox) {

      if (checked) {

        /*
         * Verificamos se eh processamento dos arquivos antigos
         * e realizamos a requisição para as classes antigas
         * Senao enviamos a requisicao para o novo processamento 
         */
        if (!verificaNovoProcessamentoAnexos(oCheckbox.value)) {
          oParam.aArquivos.push(oCheckbox.value);

        } else {
          lNovoProcessamento = true;
          enviaProcessamentoAnexo(oCheckbox.value);
        }
      }
    }
  });

  if (oParam.aArquivos.length == 0 && !lNovoProcessamento) {
    alert("Selecione ao menos uma Opção.");
    return false;
  }

  
  if (oParam.aArquivos.length > 0) {
    js_divCarregando('Aguarde, Processando Arquivos', 'msgBox');
    var oAjax = new Ajax.Request(sURL,
                                      {
                                        method:'post',
                                        parameters:'json='+Object.toJSON(oParam),
                                        onComplete:retornoProcessamento
                                      }
                                );
  }
}

function retornoProcessamento(oAjax) {
  js_removeObj('msgBox');
  var oRetorno = eval("("+oAjax.responseText+")");
  if (oRetorno.status == 1) {

    var sRetorno = "";
    
    for (var i = 0; i < oRetorno.lista.length; i++) {

      with (oRetorno.lista[i]) {

       sRetorno += "<a target='_blank' href='db_download.php?arquivo="+caminho+"'>"+nome+"</a><br>";
      }
    }

    $('retorno').innerHTML += sRetorno;
    $('field-gerados').style = "display=true";
  } else {
    alert(oRetorno.msg);
    return false;
  }
}

function enviaProcessamentoAnexo(anexo) {
    js_divCarregando('Aguarde, buscando informações do anexo...', 'msgBoxAnexo');
    var oParametros   = new Object();
    oParametros.exec  = "getDadosAnexo";
    oParametros.anexo = anexo;
        
    var oAjax = new Ajax.Request(sURL,
            {
              method:'post',
              parameters:'json='+Object.toJSON(oParametros),
              onComplete: function (oAjax) {
                  js_removeObj('msgBoxAnexo');
                  var oRetorno = eval("("+oAjax.responseText+")");
                  if (oRetorno.status == 0) {
                      alert(oRetorno.msg);
                      return false;
                  }
                  processaArquivoAnexo(oRetorno.anexo, oRetorno.codigo_relatorio);
              }
            });
}

function processaArquivoAnexo(anexo, codigo_relatorio) {
    js_divCarregando('Aguarde, Processando '+anexo+'...', 'msgBoxAnexo'+anexo);

    const Route = "/financeiro/contabilidade/siai/"+anexo;
    const formData = new FormData();
    formData.append('codigo_relatorio', codigo_relatorio);
    formData.append('periodo', $F("periodo"));
    formData.append('orgaoUnidade', $F("orgaoUnidade"));
    formData.append('codigoOrgaoTCE', $F("codUnidadeTCE"));
    formData.append('nomeOrgaoTCE', $F("nomeUnidadeTCE"));
    formData.append('desvincularDadosInstituicaoCamara', $F("desvincularDadosInstituicaoCamara"));
    formData.append('instituicoes[]', instituicao);
    PHPSession.appendFormData(formData);

    HttpClient.post(`${PHPSession.requestApi}${Route}`, {body: formData}).then(response => {
        js_removeObj('msgBoxAnexo'+anexo);
        if (response.error) {
            alert(response.message);
            return;
        }
        var html = "<a target='_blank' href='db_download.php?arquivo="+response.data+"'>"+response.message+"</a><br>";
        $('retorno').innerHTML += html;
        $('field-gerados').style = "display=true";
    });
}


function importarArquivoReceita() {
      if (empty(fileUploadAnexoReceita.file)) {
          alert("Informe o arquivo.");
          return false;
      }
      
      js_divCarregando('Aguarde... importando arquivo...', 'msgbox');
      var oParametro = new Object();
      oParametro.exec = 'importarArquivoDePara';
      oParametro.tipoArquivo = 'anexoI_elementos_receita';
      oParametro.exercicio = exercicio;
      oParametro.arquivo = JSON.stringify({
          "extension": fileUploadAnexoReceita.extension,
          "name": fileUploadAnexoReceita.file,
          "path": fileUploadAnexoReceita.filePath
      });

      var oAjax = new Ajax.Request(sURL,
                                   {method:'post',
                                    parameters:'json='+Object.toJSON(oParametro),
                                    onComplete: function (oAjax) {
                                        js_removeObj('msgbox');
                                        var oRetorno = eval("("+oAjax.responseText+")");
                                        alert(oRetorno.msg);
                                    }
                                   });
}
</script>
</body>
</html>
