<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
?>
<form name="form1" method="post" action="">
  <fieldset style="width: 500;"><legend> <b>Departamentos</b></legend>
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
        <td>
          <?php   
             include modification("dbforms/db_classesgenericas.php");

             $oListaLota     = new cl_arquivo_auxiliar;
             $oListaLota->cabecalho     = "<strong>Lista de Departamentos</strong>";
             $oListaLota->codigo       = "coddepto"; //chave de retorno da func
             $oListaLota->descr        = "descrdepto"; //chave de retorno
             $oListaLota->nomeobjeto     = 'fd02_coddep';
             $oListaLota->funcao_js    = 'js_mostra';
             $oListaLota->funcao_js_hide   = 'js_mostra1';
             if(isset($chavepesquisa)){
              $oListaLota->sql_exec     = " select fd02_coddep, coddepto, descrdepto from fiscalizacao.fis_fisdocdep  inner join db_depart  on coddepto = fd02_coddep  where fd02_codtipo =".$y27_codtipo;
             }
             $oListaLota->func_arquivo   = "func_fis_fisdep.php";  //func a executar
             $oListaLota->nomeiframe     = "db_iframe_dep";
             $oListaLota->localjan     = "";
             $oListaLota->onclick      = "";
             $oListaLota->db_opcao     = 2;
             $oListaLota->concatenar_codigo   = true;
             $oListaLota->tipo       = 2;
             $oListaLota->top        = 0;
             $oListaLota->obrigarselecao = true;
             $oListaLota->linhas       = 10;
             $oListaLota->vwhidth      = '100%';
             $oListaLota->funcao_gera_formulario();
          ?>
        </td>
      </tr>
    </table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="js_select();" >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
  </fieldset>
</form>
<script>

function js_select(){
  for (x=0;x<document.form1.fd02_coddep.length;x++){
    document.form1.fd02_coddep.options[x].selected = true;
  }
  // if(document.form1.fd02_coddep.value == ''){
  //   alert('Selecione um Departamento');
  //   return false;
  // }
}
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
     sArquivoPesquisa = 'func_db_documentotemplate.php?funcao_js=parent.js_mostraDocumentoLookUpAutodeInfracao|db82_sequencial|db82_descricao&tipo=51';
   } else {
     sArquivoPesquisa = 'func_db_documentotemplate.php?pesquisa_chave='+document.form1.y32_templateautoinfracao.value+'&funcao_js=parent.js_mostraDocumentoDigitacaoAutodeInfracao&tipo=51';
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
      $('db82_descricaoautodeinfracao').value = sRetorno;
      db_iframe_db_documentotemplate.hide();
  }
</script>