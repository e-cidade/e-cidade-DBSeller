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

?>
<center>
<form name="form1" method="post">
  	<fieldset>
    <legend>
      <b>Tipo Agrupamento Rubrica</b>
    </legend>
		<table border="0">
		<tr>
			<td nowrap title="Código">
				<strong>Código:</strong>
			</td>
			<td> 
				<?php
				db_input('rh238_sequencial', 10, $Irh238_sequencial, true, 'text', 3);
				?>
			</td>
		</tr>
		<tr>
			<td nowrap title="Descrição">
				<strong>Descrição:</strong>
			</td>
			<td> 
				<?php
				db_input('rh238_descricao', 40,	$Irh238_descricao, true, 'text', $oGet->opcao);
				?>
			</td>
		</tr>
		</table>
	</fieldset>
	<input name="<?=($oGet->opcao==1?"incluir":($oGet->opcao==2||$oGet->opcao==22?"alterar":"excluir"))?>" 
		   type="button" 
		   id="db_opcao" 
		   value="<?=($oGet->opcao==1?"Incluir":($oGet->opcao==2||$oGet->opcao==22?"Alterar":"Excluir"))?>"
           onClick="salvar();"/>

	<input name="pesquisar" 
		   type="button" 
		   id="pesquisar" 
		   value="Pesquisar" 
		   onClick="js_pesquisa();" />
</form>
</center>
<script>

    const sURL = 'pes1_tipoagrupamentorubrica.RPC.php';

    var opcao = <?php echo $oGet->opcao; ?>;

    function js_pesquisa() {

        var oJanela = js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_tipoagrupamentorubrica',
            'func_tipoagrupamentorubrica.php?funcao_js=parent.carregarDados|rh238_sequencial',
            'Pesquisa Tipos de Agrupamento de Rubricas',
            true
        );
    }

    function salvar(){

        var codigo    = $('rh238_sequencial').value;
        var descricao = $('rh238_descricao').value;

        if ( empty(codigo) && opcao == 2) {
            alert('Sequencial não informado.');
            return false;
        }
        
        if (empty(descricao)) {
            alert('Descrição não informado.');
            return false;
        }

        var oParametro = {
            exec               : 'save',
            iCodigo            : codigo,
            sDescricao         : descricao
        };  

        var fnRetorno = function(oRetorno, lErro) {

            alert(oRetorno.message.urlDecode());
            if (lErro) {
                return;
            }
            $('rh238_sequencial').value = oRetorno.iCodigo;
            limpaCampos();
        };

        new AjaxRequest(sURL, oParametro, fnRetorno).execute();
    } 

    function excluir(){

        var codigo = $('rh238_sequencial').value;
        
        if (empty(codigo)) {
            alert('Sequencial não informado.');
            return false;
        }

        var oParametro = {
            exec    : 'delete',
            iCodigo : codigo
        };  

        var fnRetorno = function(oRetorno, lErro) {

            alert(oRetorno.message.urlDecode());
            if (lErro) {
                return;
            }
            limpaCampos();
        };

        new AjaxRequest(sURL, oParametro, fnRetorno).execute();
    } 


    function carregarDados(iCodigo){
        
        var oParametro = {
            exec    : 'get',
            iCodigo : iCodigo
        };  

        var fnRetorno = function(oRetorno, lErro) {

            db_iframe_tipoagrupamentorubrica.hide();
            
            if (lErro || opcao == 1) {
                return;
            }
            $('rh238_sequencial').value = oRetorno.iCodigo;
            $('rh238_descricao').value  = oRetorno.sDescricao;
        };

        new AjaxRequest(sURL, oParametro, fnRetorno).execute();
    }

    function limpaCampos(){
        $('rh238_sequencial').value = '';
        $('rh238_descricao').value  = '';
    }

    if (opcao == 3) {
        var button = document.getElementById('db_opcao');
        button.onclick = excluir;
    }

</script>