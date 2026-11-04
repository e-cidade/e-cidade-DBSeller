<form name="form1" id="form1">
<fieldset style="margin: 25px auto; width: 800px;">

    <legend><strong>Inicial - Altera??o de Advogados</strong></legend>
    <table class="form-container">
        <tr>
            <td>
            <?php
                db_input('v50_inicial',10,$Iv50_inicial,true,'hidden',$db_opcao);
            ?>
            </td>
        </tr>
        <tr>
            <td title="<?=@$Tv50_advog?>">
            <?php
                db_ancora($Lv50_advog,' js_advog(true); ',$db_opcao);
            ?>
            </td>
            <td>
            <?php
                db_input('v50_advog',6,$Iv50_advog,true,'text',$db_opcao,"onchange='js_advog(false)'");
                db_input('z01_nome',40,$Iz01_nome,true,'text',3,"","z01_nomeadvog");
            ?>
            </td>
        </tr>

        <tr>
            <td nowrap title="<?=@$Tk60_codigo?>" >
                <?php
                    db_ancora(@$Lk60_codigo,"js_pesquisalista(true);",$db_opcao)
                ?>
            </td>
            <td>
                <?php
                    db_input('k60_codigo',10,$Ik60_codigo,true,'text',$db_opcao,"onchange='js_pesquisalista(false);'");
                    db_input('k60_descr',40,$Ik60_descr,true,'text',3,'');
                ?>
            </td>
        </tr>
        
        </table>
</fieldset>

<div id="botao" style="text-align:center; ">
  <input type="button" name="processar" id="processar" value="Processar" onclick="js_processar()" />
</div>

</form>

<script>

function js_advog(mostra){
  
    var advog = document.form1.v50_advog.value;
  
    if(mostra==true){
      
      var sUrl = 'func_advog.php?funcao_js=parent.js_mostraadvog|v57_numcgm|z01_nome';
      js_OpenJanelaIframe('', 'db_iframe', sUrl, 'Pesquisa', true);
    }else{

      var sUrl = 'func_advog.php?pesquisa_chave='+advog+'&funcao_js=parent.js_mostraadvog1';
      js_OpenJanelaIframe('', 'db_iframe', sUrl, 'Pesquisa', false);
  }
}

function js_mostraadvog(chave1,chave2){
  document.form1.v50_advog.value = chave1;
  document.form1.z01_nomeadvog.value = chave2;
  db_iframe.hide();
}
function js_mostraadvog1(chave,erro){
  document.form1.z01_nomeadvog.value = chave;
  if(erro==true){
    document.form1.v50_advog.focus();
    document.form1.v50_advog.value = '';
  }
}

function js_pesquisalista(mostra){
     if(mostra==true){
       db_iframe.jan.location.href = 'func_lista.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr';
       db_iframe.mostraMsg();
       db_iframe.show();
       db_iframe.focus();
     }else{
       db_iframe.jan.location.href = 'func_lista.php?pesquisa_chave='+document.form1.k60_codigo.value+
                                     '&funcao_js=parent.js_mostralista';
     }
}
function js_mostralista(chave,erro){
  document.form1.k60_descr.value = chave;
  if(erro==true){
     document.form1.k60_descr.focus();
     document.form1.k60_descr.value = '';
  }
}
function js_mostralista1(chave1,chave2){
     document.form1.k60_codigo.value = chave1;
     document.form1.k60_descr.value = chave2;
     db_iframe.hide();
}

const sUrl = 'jur4_alteraradvog.RPC.php';

function js_processar() {

	var oParam        = new Object();
	var oGet          = js_urlToObject();
	var iAdvogado      = document.form1.v50_advog.value;
	var iCodigo     = document.form1.k60_codigo.value;

	oParam.iAdvogado = iAdvogado;  
	oParam.iCodigo     = iCodigo;
	oParam.sExec     = 'alterarAdvogados';  
		
	js_divCarregando(_M('tributario.juridico.jur4_alteraradvog.processando_registros'), 'msgbox');

	var oAjax = new Ajax.Request(sUrl, 
														  {
		  												 method    : 'POST',
															 parameters: 'json='+Object.toJSON(oParam),
															 onComplete: js_retornoProcesso 
															});
	
}

function js_retornoProcesso(oAjax) {
  		 
	js_removeObj('msgbox');

	var oRetorno = JSON.parse(oAjax.responseText);
	var sListaArquivos = '';

	if (oRetorno.iStatus == 1) {
		
        alert(oRetorno.sMessage);
        limpaCampos();
		
	} else {
		
		alert(oRetorno.sMessage.urlDecode().replace(/\\n/g,'\n'));
		
	}
	
}

function limpaCampos(){
    document.form1.v50_advog.value = '';
    document.form1.k60_codigo.value = '';
    document.form1.z01_nomeadvog.value = '';
    document.form1.k60_descr.value = '';
}
</script>