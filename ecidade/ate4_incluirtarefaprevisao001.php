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

@require(modification("libs/db_stdlib.php"));
@require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_termoanuproc_classe.php"));
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link rel="stylesheet" type="text/css" href="estilos.css" >
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  <style>
		.marcado 
		 {
 	     background-color:#c3d5d5;
		 }
  </style>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbtextFieldData.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<form name="form1" method="post" action="">
<center>
<table border=0 style='align:center;padding-top:30px;'>
  <tr align="center">
    <td>
      <fieldset>
        <legend>
          <b>Inclusão de Tarefas</b>
        </legend>
	      <table>
				  <tr>
				    <td>
				      <?php
				        db_ancora('<b>Tarefa:</b>','js_pesquisaTarefa(true)',1,'');
				        db_input('tarefa'     ,10,'',true,'text',1,'onChange="js_pesquisaTarefa(false)"');
				        db_input('descrtarefa',40,'',true,'text',1,'onChange="js_validaDescricao()"');
				      ?>
				      <input type="button" id="incluir" value="Incluir" onClick="js_incluirTarefa()" disabled>
				    </td>
				  </tr>
				</table>
			</fieldset>
		</td>
	</tr>		 
  <tr>
    <td>
      <table>
        <tr>
          <td>
            <fieldset>
              <legend>
                <b>Tarefas</b>
              </legend>
              <table style='border:2px inset white;width: 100%' cellpadding="0" cellspacing="0">
	              <tr>
	                <td class='table_header' width="70px">
	                   Código
	                </td>
	                <td class='table_header' width="450px;">
	                   Descrição
	                </td>
	              </tr>
	              <tbody id='listaTarefas' style='height: 300px;overflow: scroll; overflow-x:hidden; background-color: white'>
	              </tbody>
	              <tfoot>
	                <tr>
	                  <td colspan="2" id="totalRegistros">
	                  </td>
	                </tr>
	              </tfoot>
              </table>              
            </fieldset>
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table>
        <tr>
          <td>
            <img style="cursor:hand;cursor:pointer;"  src="imagens/btnSetaUp.gif" onClick="js_moveUp();">
          </td>
        </tr>
        <tr>
          <td>
            <img style="cursor:hand;cursor:pointer;"  src="imagens/btnSetaDown.gif" onClick="js_moveDown();">
          </td>
        </tr>           
        <tr>
          <td>
            <img style="cursor:hand;cursor:pointer;"  src="imagens/btnExcluir.gif" onClick="js_excluirTarefa();">
          </td>
        </tr>        
      </table>
    </td>    
  </tr>
  <tr align="center">
	  <td>
	    <input type="button" id="salvar" value="Salvar" onClick="js_save();">
	  </td>
	</tr>
</table>
</center>
</form>
</center>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>

var sUrlRPC       = 'ate4_tarefas.RPC.php';
var aListaTarefas = new Array(); 

var oAutoCompleteCod = new dbAutoComplete($('tarefa'),'ate4_pesquisatarefacod.RPC.php');
    oAutoCompleteCod.setTxtFieldId($('descrtarefa'));
    oAutoCompleteCod.show();
  
   oAutoCompleteCod.setCallBackFunction( 
	   function(id,label){
	     $('tarefa').value = label;
	     js_desableIncluir(false);
	     $('descrtarefa').focus();
	   }
	 );

var oAutoCompleteDescr = new dbAutoComplete($('descrtarefa'),'ate4_pesquisatarefadescr.RPC.php');
    oAutoCompleteDescr.setTxtFieldId($('tarefa'));
    oAutoCompleteDescr.show();
  
   oAutoCompleteDescr.setCallBackFunction( 
     function(id,label){
       $('tarefa').value      = id;
       $('descrtarefa').value = label;
       js_desableIncluir(false);
       $('tarefa').focus();
     }
   );


	function js_consultaTarefas() {
	
	  js_divCarregando("Aguarde, buscando registros","msgBox");
	 
	  strJson = '{"exec":"getTarefasPrevisao"}';
	  var oAjax   = new Ajax.Request( sUrlRPC, {
	                                             method: 'post', 
	                                             parameters: 'json='+strJson, 
	                                             onComplete: js_carregaGrid
	                                           }
	                                );
	}

	function js_carregaGrid(oAjax) {
	   
	    var obj = JSON.parse(oAjax.responseText);
	
	    if (obj.status && obj.status == 2){
	       js_removeObj("msgBox");
	       alert(obj.sMensagem.urlDecode());
	       return false ;
	    }
	    
	    $('tarefa').value           = '';
	    $('descrtarefa').value      = '';
	    $('listaTarefas').innerHTML = '';
	    aListaTarefas               = new Array();
	    
	    if (obj) {
	    
	      for (var iInd = 0; iInd < obj.length; iInd++) {
	        with (obj[iInd]){
	        
	          js_addRow(tarefa,descricao.urlDecode());
	          
	        }
	      }
	    }
	    
	    js_acertaTotalizador();
	    js_removeObj("msgBox");
	}

	function js_seleciona(idRow){
	  
	  var aListaMarcados = $$('#listaTarefas .marcado');
	  var iIdMarcado     = '';
	    
	  aListaMarcados.each(
	    function ( oMarcado, iInd) {
	      oMarcado.className = 'desmarcado';
	      iIdMarcado         = oMarcado.id;
	    }
	  );
	  
	  if ( idRow != iIdMarcado ) {
	    $(idRow).className = 'marcado';
	  }
	  
	}

	function js_moveUp(){
	    
	  var objMarcados = $$('#listaTarefas .marcado');
	  
	  var row    = objMarcados[0];
	  var tbody  = $('listaTarefas');
	  var rowId  = row.rowIndex;
	  var hTable = tbody.parentNode;
	  var nextId = rowId-1;
	    
	  if ( nextId == 0 )  {
	    return false;
	  }
	      
	  var next = hTable.rows[nextId];
	  tbody.removeChild(row);
	  tbody.insertBefore(row, next);
	    
	} 
	  
  
	function js_moveDown(){
	  
	  var objMarcados = $$('#listaTarefas .marcado');
	   
	  var row    = objMarcados[0];
	  var tbody  = $('listaTarefas');
	  var rowId  = row.rowIndex;
	  var hTable = tbody.parentNode;
	  var nextId = parseInt(rowId)+2;
	  
	  if ( nextId+1 == hTable.rows.length ) {
	    return false;
	  }
	   
	  if ( (nextId+1) == hTable.rows.length ) {
	    var next = hTable.rows[nextId+1];
	  } else {
	    var next = hTable.rows[nextId];
	  }
	  
	  tbody.removeChild(row);
	  tbody.insertBefore(row, next);
	   
	}


  function js_pesquisaTarefa( lMostra ){
    js_desableIncluir(true);
    if( lMostra ){
      js_OpenJanelaIframe('','db_iframe_tarefa','func_tarefalista.php?lista=false&funcao_js=parent.js_mostratarefa1|at40_sequencial|at40_descr','Pesquisa Tarefas',true);
    } else {
      if( $F('tarefa') != '' ){ 
        js_OpenJanelaIframe('','db_iframe_tarefa','func_tarefalista.php?lista=false&pesquisa_chave='+$F('tarefa')+'&funcao_js=parent.js_mostratarefa','Pesquisa Tarefas',false);
      }else{
        document.form1.descrtarefa.value = '';
      }
    }
    
  }
  
  function js_mostratarefa(chave,lErro){
    
    if( lErro ){ 
      document.form1.tarefa.focus(); 
      document.form1.tarefa.value      = '';
      document.form1.descrtarefa.value = '';
      alert(chave);
      return false;
      js_desableIncluir(true); 
    } else {
	    document.form1.descrtarefa.value = chave;
	    js_desableIncluir(false);
    }
    
  }
  
  function js_mostratarefa1(chave1,chave2){
    document.form1.tarefa.value      = chave1;
    document.form1.descrtarefa.value = chave2;
    js_desableIncluir(false);
    db_iframe_tarefa.hide();
  }
  
  function js_incluirTarefa() {

    if ( $F('tarefa').trim() == '' ) {
      alert('Número da tarefa não informado!');
      return false;
    }
  
    js_addRow($F('tarefa'),$F('descrtarefa'));
  
    $('tarefa').value      = ''; 
    $('descrtarefa').value = '';
    
  }

  
  function js_excluirTarefa() {
    
    var objMarcados = $$('#listaTarefas .marcado');
    
    if ( objMarcados.length == 0 ) {
      alert('Nenhum tarefa selecionada!');
      return false;
    }
    
    js_deleteRow(objMarcados[0].id);

  }

  function js_verificaTarefaDuplicidade(iCodTarefa){
    
    lRetorno = true;

    aListaTarefas.each(
      function ( iTarefa ) {
        if ( iTarefa == iCodTarefa ) {
          alert('Tarefa '+iCodTarefa+' já incluída na lista!');
          lRetorno = false;
        }
      }
    );
    
    return lRetorno;
    
  }
  
  function js_addRow(iCodTarefa,sDescricao){
    
    if ( !js_verificaTarefaDuplicidade(iCodTarefa) ) {
      return false;
    }
  
    var oRow              = document.createElement("TR");
        oRow.className    = "desmarcado";
        oRow.id           = iCodTarefa; 
        oRow.style.height = '1em';
        oRow.onclick      = function () { js_seleciona(iCodTarefa); }             
  
    var aElemRow = $$('#listaTarefas tr:not([id="ultimaLinha"])');
    var iOrdem   = ( aElemRow.length + 1 );
       
    var oCodigo = document.createElement("TD");
        oCodigo.style.textAlign = 'center';
        oCodigo.className       = "linhagrid";
        oCodigo.innerHTML       = iCodTarefa;
              
    var oDescricao = document.createElement("TD");
        oDescricao.style.textAlign = 'left';
        oDescricao.className       = "linhagrid";
        oDescricao.innerHTML       = '&nbsp;'+sDescricao.substr(0,70);
        oDescricao.noWrap          = true;
                      
        oRow.appendChild(oCodigo);
        oRow.appendChild(oDescricao);
        
        if ( $('ultimaLinha') != undefined ) {
	        $('listaTarefas').removeChild($('ultimaLinha'));
        }
        
        $('listaTarefas').appendChild(oRow);
        aListaTarefas.push(iCodTarefa);
        js_acertaTotalizador();

    var elemUltimaLinha              = document.createElement("tr");
        elemUltimaLinha.id           = 'ultimaLinha';
        elemUltimaLinha.style.height = 'auto';
        elemUltimaLinha.innerHTML    = "<td>&nbsp;</td>";
          
        $('listaTarefas').appendChild(elemUltimaLinha);
          
                
  }

  function js_deleteRow(idRow){

    aListaTarefas.each(
      function ( iCodTarefa, iInd ) {
        if ( iCodTarefa == $(idRow).id ) {
          aListaTarefas = aListaTarefas.without(iCodTarefa);
        }
      }
    );

    var iRowIndex = $(idRow).rowIndex;

    if ( (iRowIndex+1 ) >= $('listaTarefas').rows.length ) {
      if ( (iRowIndex+1) == 0 ) {
        $('listaTarefas').rows[iRowIndex-2].className = 'marcado';
      }      
    } else {
      $('listaTarefas').rows[iRowIndex].className = 'marcado';
    }
    
    $('listaTarefas').removeChild($(idRow));
    js_acertaTotalizador();
  
  }


  function js_save(){
    
    var aElemRow = $$('#listaTarefas tr:not([id="ultimaLinha"])');
    var aTarefas = new Array(); 
 
    aElemRow.each(
      function ( eElem ) {
        aTarefas.push(eElem.id);      
      }
    );
  
    js_divCarregando("Salvando...","msgBox");
   
    var oParametro = new Object();
        oParametro.exec     = 'salvarTarefasPrevisao';
        oParametro.aTarefas = aTarefas;
        
        var oAjax   = new Ajax.Request( sUrlRPC, {
                                               method: 'post', 
                                               parameters: 'json='+Object.toJSON(oParametro), 
                                               onComplete: js_returnSave
                                             }
                                  );
            
    
  
  }

  function js_returnSave(oAjax) {

    var obj = JSON.parse(oAjax.responseText);
  
    js_removeObj("msgBox");

    alert(obj.sMensagem.urlDecode());
    
    if (obj.status && obj.status == 2){
       return false ;
    } else {
      js_consultaTarefas();
    }
  
  }

  function js_acertaTotalizador(){
    $('totalRegistros').innerHTML = '<b>&nbsp;Total Registros : '+($('listaTarefas').rows.length-1)+'</b>';
  }
 
  function js_desableIncluir(lDesabled){
    $('incluir').disabled = lDesabled;
  }
  
  function js_validaDescricao(){
    
    if ( new String($('descrtarefa').value).trim() == '' ) {
      $('tarefa').value = '';
      js_desableIncluir(true);
    }
    
  }
  
  
  js_consultaTarefas();

</script>
