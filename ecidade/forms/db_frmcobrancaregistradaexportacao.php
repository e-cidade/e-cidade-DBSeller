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
?>
<style>
  .checkbox-text {
    vertical-align: bottom;
    display: inline-block;
    margin-bottom: 5px;
  }

</style>
<form action="" method="post" name="formularioExportacao" id="formularioExportacao">
  <fieldset>
    <legend>Geração de Remessa</legend>
    <table>
      <tr>
        <td>
          <label class="bold" id="labelConvenio" for="codigoConvenio"><a href="javascript:;">Convênio:</a></label>
        </td>
        <td>
          <?php
            db_input("ar11_sequencial", 1, 1, true, "text", 1);
            db_input("ar11_nome", 1, 1, true, "text", 3);
          ?>
        </td>
      </tr>
      <tr>
        <td>
          <label class="bold" id="labelQuebraLinha" for="lQuebraLinha">Gerar última linha do arquivo com quebra de linha?</label>
        </td>
        <td>
          <?php
            $aOpcoes = array( 0 => 'NÃO', 1 => 'SIM');
            db_select('lQuebraLinha', $aOpcoes, true, 1, ""); 
          ?>
        </td>
      </tr>
      <tr>
        <td>
          <label class="bold" id="labelFiltrarDebistos" for="lFiltrarDebitos">Filtrar Débitos</label>
        </td>
        <td>
          <?php
            $aOpcoes = array( 0 => 'NÃO', 1 => 'SIM');
            db_select('lFiltrarDebitos', $aOpcoes, true, 1, ""); 
          ?>
        </td>        
      </tr>
      <tr id="dataEmissao" style='display:none;'>
        <td><label class="bold" id="labelDataEmissao" for="sDataEmissaoInicio">Data de Emissão dos Recibos:</label></td>
        <td>
          <?php db_inputdata("sDataEmissao", '', '', '', true, null, 1); ?>
        </td>
      </tr>               
      <tr id="trTiposDebito" style='display:none;'>
        <td colspan="3">
          <div id="tipoDebito"></div>
        </td>
      </tr>
       <tr id="trParcelas" style="display: none;">
        <td colspan="3">
          <table id='parcelasUnicas' style="display: none;">
            <tr class="labelParcela">
              <td>
                <label class="bold" id="labelTipoParcelas" for="lTipoParcelas">Parcelas Únicas:</label>
              </td>
            </tr>
            <tr class="trParcelas"></tr>
          </table>
          <table id='parcelas' style="display: none;">
            <tr class="labelParcela">
              <td>
                <label class="bold" id="labelTipoParcelas" for="lTipoParcelas">Parcelas:</label>
              </td>                
            </tr>
            <tr class="trParcelas"></tr>            
          </table>                   
        </td>
      </tr>  
    </table>
  </fieldset>
  <input type="button" id="processar" value="Processar"/>
</form>
<script type="text/javascript">

  /**
   * Constante contendo o caminho para o RPC
   */
  const 
    RPC           = "arr4_cobrancaregistrada.RPC.php",    
    trParcelas    = $('trParcelas'),
    tableUnicas   = $('parcelasUnicas'),
    tBodyUnicas   = tableUnicas.querySelector('tbody'),
    tableParcelas = $('parcelas'),
    tBodyParcelas = tableParcelas.querySelector('tbody'),
    selectFiltrarDebitos = $('lFiltrarDebitos');

  var 
    oLancadorTipoDebito  = new DBLancador('oLancadorTipoDebito'),
    countUnicas          = 0,
    countParcelas        = 0,
    arrKtiposAdicionados = [];

  // new DBToogle('fieldTipoDebito', true);

  var oLookUpConvenio = new DBLookUp($('labelConvenio'), $('ar11_sequencial'), $('ar11_nome'), {
    'sArquivo'      : 'func_cadconvenio.php',
    'sObjetoLookUp' : 'db_iframe_cadconvenio',
    'sLabel'        : 'Pesquisar Convênio'
  });

  oLancadorTipoDebito.setNomeInstancia('oLancadorTipoDebito');
  oLancadorTipoDebito.setLabelAncora('Tipo de débito: ');
  oLancadorTipoDebito.setTituloJanela('Pesquisar Tipos de Débito');
  oLancadorTipoDebito.setGridHeight(100);
  oLancadorTipoDebito.setCallbackAncora(verificaTiposDebitos);
  oLancadorTipoDebito.setCallbackBotao(callbackBotao);
  oLancadorTipoDebito.setCallbackRemover(callbackRemover);
  oLancadorTipoDebito.show($('tipoDebito'));

  /**
   * Capturamos o evento de click do botão processar para que possamos realizar
   * a emissão da remessa desejada
   */
  $('processar').observe('click', () => {

    if (empty( $('ar11_sequencial').value ) ) {
      return alert("O campo Convênio é de preenchimento obrigatório.");
    }

    const 
      filtrarDebitos  = selectFiltrarDebitos.value,
      sFiltrarDebitos = `&filtrarDebitos=${filtrarDebitos}`; 
    var 
      sTiposDebito = '&sDebitos=',
      sUnicas      = '',
      sParcelas    = '';

    if(!!filtrarDebitos){
      var aTiposDebito = [];
      if(oLancadorTipoDebito.getRegistros().length > 0) {

        oLancadorTipoDebito.getRegistros().each(function(oTipoDebito) {
          aTiposDebito.push(oTipoDebito.sCodigo);
        });
        
        sTiposDebito += aTiposDebito.join(',');
      }
      
      var 
        todasUnicas = tableUnicas.querySelectorAll(['input:checked.checkbox-parcelas']),
        unicasSelecionadas = [].filter.call(todasUnicas, function(elemento){
          return !!elemento.checked;
        }),
        sUnicas = '',
        arrUnicas = [];

      for(unica of unicasSelecionadas){
        if(!!arrUnicas[unica.name]){
          arrUnicas[unica.name] += `,${unica.value}`
        } else {
          arrUnicas[unica.name] = `&unica[${unica.name}]=${unica.value}`;
        }
      }
      sUnicas = arrUnicas.join("");

      var 
        todasParcelas = tableParcelas.querySelectorAll(['input:checked.checkbox-parcelas']),
        parcelasSelecionadas = [].filter.call(todasParcelas, function(elemento){
          return !!elemento.checked;
        }),
        arrParcelas = [],
        sParcelas = '';

      for(parcela of parcelasSelecionadas){
        if(!!arrParcelas[parcela.name]){
          arrParcelas[parcela.name] += `,${parcela.value}`
        } else {
          arrParcelas[parcela.name] = `&parcela[${parcela.name}]=${parcela.value}`;
        }
      }

      sParcelas = arrParcelas.join("");
    }
   
    sDataEmissao = `&dataemissao=${$F("sDataEmissao")}`;

    js_OpenJanelaIframe(
      'CurrentWindow.corpo',
      'db_iframe_carne',
      `arr4_cobrancaregistradaexportacaogeracao.php?codigo_convenio=${$('ar11_sequencial').value}&lQuebraLinha=${$('lQuebraLinha').value}${sFiltrarDebitos}${sTiposDebito}${sUnicas}${sParcelas}${sDataEmissao}`,
      'Processando Geração...',
      true
    );
  });

  selectFiltrarDebitos.addEventListener('change', (event) => {
    var 
      target = event.target;

    if(target.value == 1){
      $('trTiposDebito').style.display = 'table-row';
      trParcelas.style.display = 'table-row';
      $('dataEmissao').style.display = 'table-row';
      verificaParcelasTela();
    } else {
      trParcelas.style.display = 'none';
      $('trTiposDebito').style.display = 'none';
      $('dataEmissao').style.display = 'table-row';
    }
  });
 
  /**
   * Função de retorno da pesquisa do Lookup do lançador
   */
  oLancadorTipoDebitoRetornoPesquisaLookUp = oLancadorTipoDebito.retornoPesquisaLookUp;
  oLancadorTipoDebito.retornoPesquisaLookUp = function () {
    var sequencial = arguments[0];

    oLancadorTipoDebitoRetornoPesquisaLookUp.apply(this, arguments);
    this.oElementos.oInputCodigo.value = sequencial;

  }.bind(oLancadorTipoDebito);

  /**
   * Função disparada ao clickar na ancora de pesquisa
   */
  function verificaTiposDebitos() {
    oLancadorTipoDebito.setParametrosPesquisa('func_arretipo.php', ['k00_tipo','k00_descr'], 'js_mostraarretipo1|k00_tipo|k00_descr');
  };

  /**
   * Callback do botão de adicionar
   */
  function callbackBotao(){
    var 
      recursosSelecionados = oLancadorTipoDebito.getRegistros();

    recursosSelecionados.each(function (recurso){
      if(!arrKtiposAdicionados.includes(recurso.sCodigo)){
        geraInputsUnicas(recurso.sCodigo);
        arrKtiposAdicionados.push(recurso.sCodigo);
      }
    });

  }

  /**
   * Calback do botão de remover
   */
  function callbackRemover(){
    var
      recursosSelecionados = oLancadorTipoDebito.getRegistros(),
      arrIds = [];

    recursosSelecionados.each(function (recurso){
      arrIds.push(recurso.sCodigo);
    });

    for(var ktipo of arrKtiposAdicionados){
      if(!arrIds.includes(ktipo)){
        var 
          tds = document.querySelectorAll(`.ktipo_${ktipo}`);

        for(var td of tds){
          var trPai = td.parentElement;
          td.remove();
          if(!trPai.classList.contains('trParcelas')){
            if(trPai.childElements().length == 0)
              trPai.remove();
          }
        }
        arrKtiposAdicionados.splice(arrKtiposAdicionados.indexOf(ktipo), 1);
      }
    }
    
    verificaParcelasTela();
  }
  
  function verificaParcelasTela(){    
    if(tableUnicas.querySelector('.trParcelas').childElements().length == 0 )
      tableUnicas.style.display = 'none';
    else
      tableUnicas.style.display = 'table-row';
    if(tableParcelas.querySelector('.trParcelas').childElements().length == 0 )
      tableParcelas.style.display = 'none';
    else
      tableParcelas.style.display = 'table-row';
  }

  /**
   * Função que busca as parcelas unicas e demais parcelas do iptu e popula os inputs
   */
  function geraInputsUnicas(k00_tipo){
    var
      formData = new FormData(),
      objPost = { sExecucao : 'buscarParcelasUnicas', k00_tipo : k00_tipo };

    formData.append('json', JSON.stringify(objPost));

    HttpClient.post(RPC, {body: formData}).then(response => {
      if(response.unicas.length > 0){
        for(var unica of response.unicas){
          var
            td = document.createElement('td'),
            checkBox = document.createElement('input'),
            label = document.createElement('label'),
            textDiv = document.createElement('textDiv');

          checkBox.setAttribute('type', 'checkbox');
          checkBox.setAttribute('name', `${k00_tipo}`);
          checkBox.setAttribute('value', unica.k00_dtvenc);
          checkBox.setAttribute('class', 'checkbox-parcelas');
          textDiv.setAttribute('class', 'checkbox-text');
          label.appendChild(checkBox);
          label.appendChild(textDiv);
          textDiv.appendChild(document.createTextNode(`${unica.k00_descr} - Vencimento: ${js_formatar(unica.k00_dtvenc, 'd')} - Lançamento: ${js_formatar(unica.k00_dtoper, 'd')} - Desconto: ${unica.k00_percdes}%`));
          td.appendChild(label);
          td.setAttribute('class', `ktipo_${k00_tipo}`);

          if(countUnicas == 1){
            countUnicas = 0;
            var trUnicas = document.createElement('tr');
            trUnicas.appendChild(td);
            tBodyUnicas.appendChild(trUnicas);
            countUnicas++;
          } else {
            var trUnicas = tBodyUnicas.lastElementChild;
            trUnicas.appendChild(td);
            countUnicas++;
          }

          trParcelas.style.display = 'table-row';
          tableUnicas.style.display = 'table-row';
        }
        countUnicas = 0;
      }
      geraInputsParcelamento(k00_tipo);
    });
  }

  function geraInputsParcelamento(k00_tipo){
    var
      formData = new FormData(),
      objPost = { sExecucao : 'buscarParcelas', k00_tipo: k00_tipo};

    formData.append('json', JSON.stringify(objPost));

    HttpClient.post(RPC, {body: formData}).then(response => {
      if(response.parcelas.length > 0){
        for(var parcela of response.parcelas){
          var
            td = document.createElement('td'),
            checkBox = document.createElement('input'),
            label = document.createElement('label'),
            textDiv = document.createElement('textDiv');

          checkBox.setAttribute('type', 'checkbox');
          checkBox.setAttribute('name', `${k00_tipo}`);
          checkBox.setAttribute('value', `${parcela.k00_dtvenc}.${parcela.k00_numpar}`);
          checkBox.setAttribute('class', 'checkbox-parcelas');
          textDiv.setAttribute('class', 'checkbox-text');
          label.appendChild(checkBox);
          label.appendChild(textDiv);
          textDiv.appendChild(document.createTextNode(`${parcela.k00_descr} - Vencimento: ${js_formatar(parcela.k00_dtvenc, 'd')} - Parcela: ${parcela.k00_numpar}`));
          td.appendChild(label);
          td.setAttribute('class', `ktipo_${k00_tipo}`);

          if(countParcelas == 2){
            countParcelas = 0;
            var trParcelas = document.createElement('tr');
            trParcelas.appendChild(td);
            tBodyParcelas.appendChild(trParcelas);
            countParcelas++;
          } else {
            var trParcelas = tBodyParcelas.lastElementChild;
            trParcelas.appendChild(td);
            countParcelas++;
          }

          trParcelas.style.display = 'table-row';
          tableParcelas.style.display = 'table-row';
        }
        countParcelas = 0;
      }
    });
  }

  $('dataEmissao').style.display = 'table-row';
</script>
