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
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
      db_app::load('scripts.js');
      db_app::load('estilos.css');
    ?>
  </head>
  <body bgcolor=#CCCCCC>
    <form name="form1" method="post" style="margin-top: 40px;">
      <fieldset style="width: 500px; margin: 0 auto;">
        <legend>
          <strong>Importação</strong>
        </legend>
        <table width="500" style="margin-top: 10px;">
          <tr>
            <td title="Arquivo Importação">
              <strong>Arquivo Importação:</strong>
            </td>
            <td>
              <input type="file" name="arquivo_importacao" id="arquivo_importacao" class="arquivo_importacao" onchange="this.form.arquivo_importacao_falso.value = this.value;" />
            </td>
          </tr>
          <tr>
            <td colspan="4" align="center"><br/>
              <input type="submit" name="importar" value="Importar" onclick= "return js_validatipolista()"/>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
    <?php
      db_menu(db_getsession("DB_id_usuario"),
              db_getsession("DB_modulo"),
              db_getsession("DB_anousu"),
              db_getsession("DB_instit"));
    ?>
  </body>
</html> 

<script type="text/javascript">

  /*function js_validatipolista(){
    if (document.form1.k60_codigo.value == ""){
      alert ("Preencha o campo Código da lista ! ");
      document.form1.k60_codigo.focus();
      return false;
    }else{
      return true;
    }
  }*/

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

  function js_validatipolista() {
   js_divCarregando("Aguarde, consultando registros","msgBox");
   
   var lista    = document.getElementById('k60_codigo').value;
   strJson      = '{"exec":"getTipoDebito", "lista":'+lista+'}';
   var url      = 'arre4_emissaobbRPC.php';
   var oAjax    = new Ajax.Request( url, {
                                          method: 'post', 
                                          parameters: 'json='+strJson, 
                                          onComplete: js_saida
                                        }
                                 );
  }

  function js_saida(oAjax) {
    
   var obj = JSON.parse(oAjax.responseText);
    
   if ( obj.erro && obj.erro == true ){
       js_removeObj("msgBox");
       alert(obj.mensagem.urlDecode());
       return false ;
    }
    js_removeObj("msgBox");
    alert(obj.mensagem.urlDecode());
    parent.db_iframe_anulaparc1.hide();
    parent.db_iframe_mostrainscr.hide();
    parent.db_iframe_anulaparc1conf.hide(); 
    parent.document.formatu.pesquisar.click();
  }

</script>
