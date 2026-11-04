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
 *  Copia da licenca no diretorio licenca/licenca_en.txt *                                licenca/licenca_pt.txt
 */


require_once("scripts/widgets/dbmessageBoard.widget.js");
require_once("scripts/widgets/dbcomboBox.widget.js");
require_once("scripts/widgets/dbtextField.widget.js");
require_once("scripts/strings.js");
require_once("scripts/widgets/dbtextFieldData.widget.js");
require_once("scripts/widgets/windowAux.widget.js");  
require_once("scripts/datagrid.widget.js");  
require_once("scripts/AjaxRequest.js");
DBViewLancamentoAtributoDinamico = function () {
  
  var me                     = this;
  this.sUrl                  = 'sys1_atributodinamico.RPC.php';
  this.oParentNode           = null;
  this.iGrupoValor           = null;
  this.iGrupoAtributos       = null;   
  this.sSaveCallBackFunction = null;
  this.sCallbackAfterRenderForm = function() {return true};
  this.sLabelFieldset        = 'Lista de Atributos';
  this.sLabelMessageBoard    = 'Atributos Dinâmicos';
  this.sDescrMessageBoard    = 'Lançamento de valores para os atributos dinâmicos';
  this.sAlignForm            = 'center';
  this.aCombos                = [];
  this.msgErroValidacao       = "";
  this.aValidacaoAtributos    = [];
  this.lErroValidacaoAtributo = false;
  this.parentForm             = null;
  
  /**
   *  Cria um formulário podendo ele ser uma janela ou anexado a um elemento HTML
   *  
   *  @param  void
   *  @return void      
   */    
  this.showForm = function () {
  
	  var sHTML  = "   <form name='mainFormAttributes' id='mainFormAttributes' action=''>";
	      sHTML += "      <table width='100%'>                     ";
	      sHTML += "        <tr>                                   ";
	      sHTML += "          <td align="+me.sAlignForm+">         ";
	      sHTML += "            <fieldset>                         ";
	      sHTML += "              <legend>                         ";
	      sHTML += "                <b>"+me.sLabelFieldset+"</b>   ";
	      sHTML += "              </legend>                        ";
	      sHTML += "              <table id='formAttributes'>      ";
	      sHTML += "              </table>";
	      sHTML += "            </fieldset>";       
	      sHTML += "          </td> ";
	      sHTML += "        </tr>";
	      sHTML += "      </table>";
        
        if (me.oParentNode == null) {
        
  	      sHTML += "    <table align="+me.sAlignForm+">";
  	      sHTML += "      <tr>";
  	      sHTML += "        <td align=center>";
  	      sHTML += "          <input type=button value='Salvar'   name='salvar'   id='salvar'>"
  	      sHTML += "          <input type=button value='Cancelar' name='cancelar' id='cancelar'>"
  	      sHTML += "        </td> ";
  	      sHTML += "      </tr>";
  	      sHTML += "    </table>";
        }
	      sHTML += "    </form>";
	      
    if (me.oParentNode == null) {
    
      me.window = new windowAux("windowCadDoc","", 550, 350);    
  	  me.window.setContent(sHTML);
    
  	  var oMessage = new DBMessageBoard('msgboard1', 
      	                                me.sLabelMessageBoard,
      	                                me.sDescrMessageBoard,
      	                                $("windowwindowCadDoc_content"));
  	  oMessage.show();
  
      me.window.show();
  	  
  	  me.window.setShutDownFunction(function(){
  	    me.window.destroy();
  	  });
  	     
  	  $('cancelar').onclick = function () { 
  	    me.window.destroy() 
  	  };
  	  
  	  $('salvar').onclick = function () { 
  	    me.save();        
  	  };  
      
    } else {
    
      me.oParentNode.innerHTML = sHTML;
    }
    
  }  


  /**
   *  Salva todas as informações do formulário
   *  
   *  @param  void
   *  @return void      
   */  
  this.save = function () {
  
    js_divCarregando('Salvando Dados...','msgBox');
   
    var aForm = $('mainFormAttributes').getElements();
    var aAtr  = new Array();
    
    aForm.each(function(input) {
      if (js_search_in_array(me.aInputsValidos, input.name)) {

        var sDescricao  = '';
        var oLabel = $$("label[for="+input.name+"]");
        if (oLabel.length != 0) {
          sDescricao = oLabel[0].innerHTML.replace(":", "");
        }
      
		    var oAtributo             = new Object();
		    oAtributo.iCodigoAtributo = input.name;
		    oAtributo.sValor          = input.value;
		oAtributo.sDescricao      = js_stripTags(sDescricao);

        if (js_search_in_array(me.aInputsValidar, input.name)) {
          if(!me.validaAtributo(oAtributo)){
            alert(me.msgErroValidacao);
            me.lErroValidacaoAtributo = true;
            throw $break;
          }
        }
        delete oAtributo.sDescricao
        aAtr.push(oAtributo);
      }      
    }); 
    
    var oJson             = new Object();
    oJson.sMethod         = "salvarValorAtributo";
    oJson.iGrupoAtributos = me.iGrupoAtributos;
    oJson.iGrupoValor     = me.iGrupoValor;
    oJson.aAtributos      = aAtr;
    if (me.lErroValidacaoAtributo) {
      
      me.lErroValidacaoAtributo = false;
      js_removeObj("msgBox");
      return false;
    }
    var oAjax = new Ajax.Request( me.sUrl, {
                                          method: 'post', 
                                          parameters: 'json='+Object.toJSON(oJson), 
                                          asynchronous : false,
                                          onComplete: function (oAjax) { me.retornoSave(oAjax) }
                                        }
                                   );
  }  

  
  /**
   *  Método de retorno do metodo save
   *  
   *  @param {ajax} oAjax
   *  @return void      
   */
  this.retornoSave = function (oAjax) {
    
    js_removeObj("msgBox");
    
    var oRetorno = JSON.parse(oAjax.responseText);
    
    if (oRetorno.iStatus == 2) {
    
	    alert(oRetorno.sMsg.urlDecode());
      return false;
    } else {
    
      me.getSaveCallBackFunction(oRetorno.iGrupoValor);
      me.window.destroy();
    }
  }


  /**
   *  Carrega o formulário com todas os atributos cadastrados para um conjunto de atributos
   *  
   *  @param {ajax} oAjax
   *  @return void      
   */  
  this.getFormAttribute = function (iGrupoAtributos) {
  
    js_divCarregando('Carregando Formulário...','msgBox');
  
    var oJson       = new Object();
    oJson.sMethod   = "consultarAtributos";
    oJson.iGrupoAtt = iGrupoAtributos;
             
    var oAjax   = new Ajax.Request( me.sUrl, {
	                                          method: 'post', 
	                                          parameters: 'json='+Object.toJSON(oJson), 
	                                          onComplete: me.returnGetFormAttribute 
                                          }
                                   );
  }
  

  /**
   *  Método de retorno do método getFormAttribute
   *  
   *  @param {ajax} oAjax
   *  @return void      
   */  
  this.returnGetFormAttribute = function (oAjax) {

    js_removeObj("msgBox");
    var oRetorno    =  JSON.parse(oAjax.responseText);

    me.createFormAttribute(oRetorno.aAtributos,false);

    me.sCallbackAfterRenderForm();
  }
  
 
  /**
   *  Cria uma lookup com os métodos de retorno a partir dos parâmetros informados
   *  
   *  @param  {string} sTabela      Nome tabela que será gerada a lookup
   *  @param  {string} sCampo       Campo a ser pesquisado na lookup
   *  @param  {string} sCampoForm   Campo a ser preenchido no formulário a partir do retorno da lookup
   *  @return sLookUp       
   */     
  this.geraLookUp = function(sTabela,sCampo,sCampoForm) {
    
    var sLookUp = "";
    
    var sIndex = sTabela+sCampo+sCampoForm;
    sLookUp += "function js_pesquisa"+sIndex+"(mostra){";
    sLookUp += "  if (mostra) {";
    sLookUp += "    js_OpenJanelaIframe('','db_iframe_"+sIndex+"','func_"+sTabela+".php?funcao_js=parent.js_mostra"+sIndex+"1|"+sCampo+"','Pesquisa',true);";
    sLookUp += "    $('Jandb_iframe_"+sIndex+"').style.zIndex = '999999999';";
    sLookUp += "  } else {"; 
    sLookUp += "    $('"+sCampoForm+"').value = '';"; 
    sLookUp += "  }";
    sLookUp += "}\n"; 
    
    sLookUp += "function js_mostra"+sIndex+"1(chave1){";
    sLookUp += "  $('"+sCampoForm+"').value = chave1;";
    sLookUp += "  db_iframe_"+sIndex+".hide();";
    sLookUp += "}\n";
    
    return sLookUp;
  }


  /**
   *  Popula o formulário com todas os atributos cadastrados para um conjunto de atributos
   *
   *  @param  {array} aAtributos	 
   *  @param  {bool}  lAteracao   Flag com a finalidade de não preencher valore default
   *  @return void      
   */    
  this.createFormAttribute = function(aAtributos,lAteracao){

    if (aAtributos.length == 0 ) {
      alert('Nenhum atributo informado!');
      return false;
    } 
 
    me.aInputsValidos = new Array();
    me.aInputsValidar = new Array();

    var totalAtributosHidden = 0;
    aAtributos.each(function(oAtributo) {

      if (oAtributo.iTipo === "7") {
        totalAtributosHidden++;
      }
    });

    var mostrarAtributosDinamicos = true;
    if (aAtributos.length === totalAtributosHidden) {
      mostrarAtributosDinamicos = false;
    }
    
    aAtributos.each(function(oAtributo) {

      var sInput           = "";
      var sFormula         = "";
      var iCodigo          = oAtributo.iCodigo;
      var sDescricao       = oAtributo.sDescricao.urlDecode();
      var oCampoReferencia = oAtributo.oCampoReferencia;
      var iTipo            = oAtributo.iTipo;

      var sValorDefault  = oAtributo.sValorDefault.urlDecode();
      if (lAteracao && iTipo != 6) {
        sValorDefault  = '';
      }

      if (iTipo === "7") {
        sValorDefault  = oAtributo.sValorDefault.urlDecode();
      }

      me.aInputsValidos.push(iCodigo);

      if (oAtributo.lObrigatorio == true) {
        me.aInputsValidar.push(iCodigo);

        validacaoAtributo = {
          'id' : iCodigo,
          'aValidacoes' : [{sTipo: "REQUIRED"}]
        };

        me.aValidacaoAtributos.push(validacaoAtributo);
      }

      if (!empty(oAtributo.formula)) {
        sFormula = "DBViewLancamentoAtributoDinamico.executarFormula(this,"+oAtributo.formula+", \""+oAtributo.sNome+"\", \""+me.parentForm+"\");";
      }

      switch (iTipo) {
        case "1": //TIPO VARCHAR

          eval("db_textfield_v"+iCodigo+"           = new DBTextField("+iCodigo+",'db_textfield_v"+iCodigo+"','"+sValorDefault+"','40')");
          eval("db_textfield_v"+iCodigo+".onKeyDown = 'return js_controla_tecla_enter(this,event);' ");
          eval("db_textfield_v"+iCodigo+".onKeyUp   = 'js_ValidaCampos(this,0,\""+sDescricao+"\",\"f\",\"t\",event)'");
          eval("db_textfield_v"+iCodigo+".onBlur    = 'js_ValidaMaiusculo(this,\"t\",event);"+sFormula+"'");
          eval("db_textfield_v"+iCodigo+".sStyle    = 'text-transform: uppercase'");
          eval("db_textfield_v"+iCodigo+".makeInput()");
          eval("sInput = db_textfield_v"+iCodigo+".toInnerHtml()");
          break;

        case "2": //TIPO INTEGER

          eval("db_textfield_i"+iCodigo+"         = new DBTextField("+iCodigo+",'db_textfield_i"+iCodigo+"','"+sValorDefault+"','20')");
          eval("db_textfield_i"+iCodigo+".onKeyUp = 'js_ValidaCampos(this,1,\""+sDescricao+"\",\"f\",\"f\",event)'");
          eval("db_textfield_i"+iCodigo+".onBlur    = '"+sFormula+"'");
          eval("db_textfield_i"+iCodigo+".makeInput()");
          eval("sInput = db_textfield_i"+iCodigo+".toInnerHtml()");
          break;

        case "3": //TIPO DATE

          eval("db_textfield_date"+iCodigo+" = new DBTextFieldData("+iCodigo+",'db_textfield_date"+iCodigo+"','"+sValorDefault+"','')");
          eval("sInput = db_textfield_date"+iCodigo+".toInnerHtml()");
          break;

        case "4": //TIPO FLOAT

          eval("db_textfield_f"+iCodigo+"           = new DBTextField("+iCodigo+",'db_textfield_f"+iCodigo+"','"+sValorDefault+"','20')");
          eval("db_textfield_f"+iCodigo+".onKeyUp   = 'js_ValidaCampos(this,4,\""+sDescricao+"\",\"f\",\"f\",event)'");
          eval("db_textfield_f"+iCodigo+".onKeyDown = 'return js_controla_tecla_enter(this,event)'");
          eval("db_textfield_f"+iCodigo+".onBlur    = 'js_ValidaMaiusculo(this,\"f\",event);"+sFormula+"'");
          eval("db_textfield_f"+iCodigo+".makeInput()");
          eval("sInput = db_textfield_f"+iCodigo+".toInnerHtml()");
          break;

        case "5": //TIPO BOOLEAN

          sInput  = "<select name="+iCodigo+" id="+iCodigo+" >";
          sInput += " <option value='t' "+ (sValorDefault=='t'?"selected":"") +"> SIM </option>";
          sInput += " <option value='f' "+ (sValorDefault=='f'?"selected":"") +"> NÃO </option>";
          sInput += "</select>";
          break;

        case "6": //TIPO COMBO

          sInput  = "<select name="+iCodigo+" id="+iCodigo+" style='width: 100%' nome_atributo='"+oAtributo.sNome+"' >";
          var aListaItens = sValorDefault.split("\|");
          // sValorDefault = '';
          // if (sValorDefault == '') {

              aListaItens = [];
               for (opcao in oAtributo.oOpcoes) {
                 if (typeof(oAtributo.oOpcoes[opcao]) == 'function') {
                   continue;
                 }
                 var item = opcao+"-"+oAtributo.oOpcoes[opcao].urlDecode();
                 aListaItens.push(item);
               }
          // }

          aListaItens.each(function(sItem) {

             var sDescricao = sItem;
             var sCodigo    = sItem;

             if (sItem.indexOf("-") !== false) {

               var aPartesDoItemDaLista = sItem.split("-");
               sCodigo    = aPartesDoItemDaLista[0].trim();
               sDescricao = aPartesDoItemDaLista[1].trim();
             }
             var selecionado = ""; 
             if (sValorDefault != '') {
                if (sCodigo == sValorDefault) {
                  selecionado = 'selected';
                }
             }
             sInput += " <option value='"+sCodigo+"' " + selecionado + ">"+ sDescricao +"</option>";

          });
          sInput += "</select>";

          break;

        case "7":

          sInput = "<input type='hidden' value='" + sValorDefault + "' id='"+iCodigo+"' name='"+iCodigo+"' nome_atributo='"+oAtributo.sNome+"' />";

          break;
      }

      if (oCampoReferencia != false) {

        sDescricao = "<a class='dbancora' onclick='js_pesquisa"+oCampoReferencia.sTabela+oCampoReferencia.sNome+iCodigo+"(true)' href='#'>"+sDescricao+"</a>";

        var oScript             = document.createElement("script");
            oScript.innerHTML  += me.geraLookUp(oCampoReferencia.sTabela, oCampoReferencia.sNome, iCodigo);
        document.getElementsByTagName("head")[0].appendChild(oScript);            
      }

      var styleTableRow = '';
      if (iTipo === "7") {
        styleTableRow = "style='display:none;'";
      }

         
      var sHtml  = "<tr " + styleTableRow + ">";
          sHtml += " <td align=left>";
          sHtml += "<label for='"+ iCodigo +"' style='font-weight:bold'>" +sDescricao+ ":</label> ";
          sHtml += " </td>";
          sHtml += " <td>"; 
          sHtml += sInput;
          sHtml += "</td>";
          sHtml += "</tr>";
      
      $('formAttributes').innerHTML += sHtml;
    });

    if (!mostrarAtributosDinamicos) {
      $('mainFormAttributes').style.display = 'none';
    }
  }


  /**
   *  Carrega o formulário com todos atributos a partir do código agrupador de atributos  informado
   *  
   *  @param {integer} iGrupoAtributos  Código agrupador de atributos
   *  @return void      
   */    
  this.newAttribute = function(iGrupoAtributos){
   
    me.iGrupoAtributos = iGrupoAtributos;
   
    me.showForm();
    me.getFormAttribute(iGrupoAtributos);
  }


  /**
   *  Carrega o formulário com todas as informações preenchidas a partir do código agrupador de valores lançados
   *  
   *  @param {integer} iGrupoValor  Código agrupador dos valores lançados para um conjunto de atributos
   *  @return void      
   */    
  this.loadAttribute = function(iGrupoValor){
  
    me.iGrupoValor = iGrupoValor;
  
    js_divCarregando('Carregando Formulário...','msgBox');
  
    var oJson         = new Object();
    oJson.sMethod     = "consultaAtributosValor";
    oJson.iGrupoValor = iGrupoValor;
    
    var oAjax   = new Ajax.Request( me.sUrl, {
                                               method: 'post', 
                                               parameters: 'json='+Object.toJSON(oJson), 
                                               onComplete: me.returnLoadAttribute,
                                               asynchronous:false

                                             }
                                  );  
  }
  


  /**
   *  Método de retorno do método loadAttribute
   *  
   *  @param {ajax} oAjax
   *  @return void      
   */  
  this.returnLoadAttribute = function (oAjax) {

    js_removeObj("msgBox");
    var oRetorno  = JSON.parse(oAjax.responseText);
    
    me.showForm();
    
    me.createFormAttribute(oRetorno.aAtributos,true);
    me.sCallbackAfterRenderForm();
 
    oRetorno.aValoresAtributos.each (function(valor) {
      $(valor.db110_db_cadattdinamicoatributos).value = valor.db110_valor.urlDecode();
    });

  }


  /**
   * Define a funço de retorno para o método save
   * 
   * @param  {function} sFunction
   * @return void      
   */
  this.setSaveCallBackFunction = function(sFunction){
    me.sSaveCallBackFunction = sFunction;
  }


  /**
   * Retorna a função de retorno do método save passando por parâmetro o código do documento gerado
   * 
   * @param {function} iCodAtributo
   * @return function  Função informada através do método setSaveCallBackFunction
   */
  this.getSaveCallBackFunction = function(iCodAtributo){
    me.sSaveCallBackFunction(iCodAtributo);
  }  

  
  /**
   * Informa o label do componente DBMessageBoard 
   *  
   * @param  {string} sLabel
   * @return void      
   */  
  this.setLabelMessageBoard = function (sLabel) {
    me.sLabelMessageBoard = sLabel;
  }
  
  
  /**
   * Informa a descrição do componente DBMessageBoard
   *  
   * @param  {string} sMessage
   * @return void      
   */  
  this.setDescrMessageBoard = function (sMessage) {
    me.sDescrMessageBoard = sMessage;
  }


  /**
   * Informa o label do fieldset principal do formulário
   *  
   * @param  {string} sLabel
   * @return void      
   */  
  this.setLabelFieldset = function (sLabel) {
    me.sLabelFieldset = sLabel;
  }


  /**
   * Informa "no" pai em que o formulário será exibido
   *  
   * @param  {HTMLElement} oNode
   * @return void   
   */  
  this.setParentNode = function (oNode) {
    me.oParentNode = oNode;
  }
  
  
  /**
   * Informa "na" pai em que o formulário será exibido
   *  
   * @param  {HTMLElement} oNode
   * @return void   
   */  
  this.setAlignForm = function (sAlign) {
    me.sAlignForm = sAlign;
  }
  
  this.validaAtributo = function (oAtributo) {
    
    this.lRetornoValido = true;
    self = this;
    
    for (oItemValidacaoAtributo of me.aValidacaoAtributos) {

      if(oItemValidacaoAtributo.id == oAtributo.iCodigoAtributo) {
        
        for (oValidacao of oItemValidacaoAtributo.aValidacoes) {
            
          switch (oValidacao.sTipo) {
            case "REQUIRED":
              if(typeof oAtributo.sValor == 'undefined' || oAtributo.sValor == null || oAtributo.sValor.trim() == '') {

                me.msgErroValidacao = "O campo "+ oAtributo.sDescricao +" não pode estar vazio.";
                self.lRetornoValido = false;
              }
              break;
            case "NUMBER_MORE_THAN":
              if(parseInt(oAtributo.sValor) <= parseInt(oValidacao.sValor)) {
                me.msgErroValidacao = "O campo deve ser maior que "+oValidacao.sValor;
                self.lRetornoValido = false;
                
              }
              break;
            case "COMBO_SELECIONE_DIFERENTE":
              if( parseInt(oAtributo.sValor) == parseInt(oValidacao.sValor) ) {
                me.msgErroValidacao = oValidacao.sMsg;
                self.lRetornoValido = false;
              }
              break;
          }
          if (!self.lRetornoValido) {
            break;
          }
        }
      }
      
      if (!self.lRetornoValido) {
        break;
        return false;
      }
    };
    
    return self.lRetornoValido;
  }

  this.setParentForm = function (formulario) {
    me.parentForm = formulario;
  }

}

DBViewLancamentoAtributoDinamico.executarFormula = function(elemento, formula, nome, formulario) {

  var parametros = {
    formula: formula,
    valor: elemento.value,
    nome: nome,
    formulario: $(formulario).serialize(true),
    sMethod: "executarFormula"
  }

  new AjaxRequest("sys1_atributodinamico.RPC.php", parametros, function(retorno, erro){
    if (erro){
      alert(retorno.sMsg.urlDecode());
      return false;
    }

    if (retorno.resultado.validacao == 't') {
      alert(retorno.resultado.mensagem.urlDecode());
      elemento.value = "";
    }

  }).execute();
}
