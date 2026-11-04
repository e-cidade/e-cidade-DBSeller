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

//MODULO: fiscal
$cltipofiscaliza->rotulo->label();
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ty27_codtipo?>">
       <?=@$Ly27_codtipo?>
    </td>
    <td> 
<?php 
db_input('y27_codtipo',8,$Iy27_codtipo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty27_descr?>">
       <?=@$Ly27_descr?>
    </td>
    <td> 
<?php 
db_input('y27_descr',40,$Iy27_descr,true,'text',$db_opcao,"");
?>
    </td>
  </tr>
  <tr id="linhaTemplate">
         <td nowrap="nowrap" title="<?=@$Tp90_db_documentotemplate?>">
           <?php 
             db_ancora("<strong>Documento Template:</strong>","js_pesquisaDocumentoAutodeInfracao(true);",$db_opcao);
           ?>
         </td>
         <td nowrap="nowrap">
           <?php 
             db_input('y32_templateautoinfracao',10,@$Iy32_templateautoinfracao,true,'text',$db_opcao,'onchange="js_pesquisaDocumentoAutodeInfracao(false);"');
             db_input('a',40,$a,true,'hidden',$db_opcao,"");
             db_input('db82_descricaoautodeinfracao',50,$Idb82_descricao,true,'text',3,'','db82_descricaoautodeinfracao');
           ?>
         </td>
      </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_tipofiscaliza','func_fis_tipofiscaliza.php?funcao_js=parent.js_preenchepesquisa|y27_codtipo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_tipofiscaliza.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?abas=1&chavepesquisa='+chave";
  }
  ?>
}

function js_pesquisaDocumentoAutodeInfracao(lMostra) {

   if (lMostra) {
     sArquivoPesquisa = 'func_db_documentotemplate.php?funcao_js=parent.js_mostraDocumentoLookUpAutodeInfracao|db82_sequencial|db82_descricao&tipo=51,6000';
   } else {
     sArquivoPesquisa = 'func_db_documentotemplate.php?pesquisa_chave='+document.form1.y32_templateautoinfracao.value+'&funcao_js=parent.js_mostraDocumentoDigitacaoAutodeInfracao&tipo=51,6000';
   }

    js_OpenJanelaIframe('', 'db_iframe_db_documentotemplate', sArquivoPesquisa, 'Pesquisa', lMostra);
  }

  function js_mostraDocumentoDigitacaoAutodeInfracao(sRetorno, lErro){

    document.form1.db82_descricaoautodeinfracao.value = sRetorno;

    if (lErro) {
      document.form1.db82_descricaoautodeinfracao.focus();
      document.form1.db82_descricaoautodeinfracao.value = '';
    }
  }

  function js_mostraDocumentoLookUpAutodeInfracao(iCodigo, sRetorno) {

      document.form1.y32_templateautoinfracao.value     = iCodigo;
      document.form1.db82_descricaoautodeinfracao.value  = sRetorno;
      db_iframe_db_documentotemplate.hide();
  }
</script>
