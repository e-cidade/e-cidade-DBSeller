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
$_SESSION["DB_itemmenu_acessado"] = "0";

  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));

  /**
  * Identifica qual foi o último menu acessado
  */
  $oDaoDBItensMenu = new cl_db_itensmenu();
  $sMenuAnterior   = "Nenhum menu acessado até o momento.";
  $sDisabled       = "disabled";
  $iMenuAcessado   = db_getsession('DB_itemmenu_acessado', false);

if (!empty($iMenuAcessado)) {

  $sSqlMenu = $oDaoDBItensMenu->sql_query_file(db_getsession("DB_itemmenu_acessado", false), "fc_montamenu($iMenuAcessado), funcao");
  $rsMenu   = db_query($sSqlMenu);

  if ( !$rsMenu ) {
    throw new DBException(" Item de menu não pode ser perquisado. Erro: " . pg_last_error() );
  }

  if ( pg_num_rows($rsMenu) == 0 ) {
    throw new BusinessException( "Nenhum item encontrado." );
  }
  $oDadosMenu = db_utils::fieldsMemory($rsMenu, 0);

  $sFuncaoRetornar = $oDadosMenu->funcao;
  $sMenuAnterior   = "$oDadosMenu->fc_montamenu";
  $sDisabled       = "";
}

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
      db_app::load("scripts.js, strings.js, prototype.js, estilos.css");
    ?>
  </head>
  <body style="background-color: #ccc; margin-top: 30px">
  <center>
  <fieldset style = "width:400px;">
  <legend>SQL Formatter</legend>
  <table width=850 border=0 class='tabletext'>
  <tr><td>      
  
  <form name="sqlform" style='display:inline;' action="http://sqlformat.appspot.com/format/" method="post" target='sqlframe'> 
  <textarea name='data' id='sql_src'  style='width:850px;height:250px;'></textarea>
  <table>
    <label><input type=checkbox name='remove_comments'>Remove comments</label>
    
    &nbsp;&nbsp;Keywords 
    <select name='keyword_case'>
      <option value=''>Unchanged
      <option value='lower'>Lower case
      <option value='upper' selected>Upper case
      <option value='capitalize'>Capitalize
    </select>

    &nbsp;&nbsp;Identifiers 
    <select name="identifier_case">
      <option value="">Unchanged</option>
      <option value="lower">Lower case</option>
      <option value="upper">Upper case</option>
      <option value="capitalize">Capitalize</option>
    </select>
    
    &nbsp;&nbsp;Indent space 
    <select name='n_indents'>
      <option value=1>1 Space
      <option value=2>2 Space
      <option value=3 selected>3 Space
      <option value=4>4 Space
      <option value=5>5 Space
    </select>
    
    &nbsp;&nbsp;Output Format 
    <select name="output_format">
      <option value="sql">SQL</option>
      <option value="python">Python</option>
      <option value="php">PHP</option>
    </select>
  
  </table>  
  </fieldset>
  </center>
  </form>
  
  <br/>
  <button onclick='proc_sql_submit()'><font style='font-size:17px'>Format SQL</font></button>
  <button onclick='sql_sample()'><font style='font-size:17px'>Sample Code</font></button>
  <button onclick='sql_cancel()'><font style='font-size:17px'>Undo</font></button>
   <fieldset style="width: 500px;margin-bottom: 5px; float:right;">
     <legend style="font-weight: bold;">Menu Anterior</legend>
      <table class="form-container">
       <tr>
         <td>
          <?php echo $sMenuAnterior; ?>
         </td>
       </tr>
       <tr>
         <td>
           <?php
           $sFuncaoRetornar = isset( $sFuncaoRetornar ) ? $sFuncaoRetornar : "";
           ?>
          <input type="button" class="field-size-max" funcao="<?=$sFuncaoRetornar;?>" id="btnVoltarMenuAnterior" value="Voltar" <?php echo $sDisabled; ?>>
         </td>
       </tr>
     </table>
    </fieldset>
  <p>
  <iframe name='sqlframe' id='sqlframe' src='' style="width:850px;height:300px;border:1px solid #DBAB4F;" border=0 frameborder=0></iframe>
  </table>
    <?php
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>
    </body>
  </html>
  
  <script>

  $('btnVoltarMenuAnterior').onclick = function() {
    location.href = this.getAttribute('funcao');
  }

  function proc_sql_submit(){
    var s=document.sqlform.data.value;
    if (!s) {
      alert('Please, input SQL!!');
      document.sqlform.data.focus();
      return;
    }
    $('sqlframe').src='http://iblogbox.com/img/wait.gif';  
    setTimeout(function(){
      document.sqlform.submit();
    },50);
  } 
  
  var prevdata={};
  function sql_sample(a){
    if (!a){
      prevdata.sql_src=_getid('sql_src').value;
    }
    $('sql_src').value='USE mydatabase;SELECT orders.customer, orders.day_of_order, orders.product, orders.quantity as number_ordered, inventory.quantity as number_instock, inventory.price FROM orders JOIN inventory ON orders.product = inventory.product;';
  }
  
  function sql_cancel(){
    if (prevdata.sql_src) $('sql_src').value=prevdata.sql_src;
  }
  
  var g_storage_name='devtools_sql_config';
  
  function savestorage(){
    if (!window.localStorage || !window.JSON) return;
  
    var a={}; 
    a.sql_remove_comments=document.sqlform.remove_comments.checked;
    a.sql_keyword_case=document.sqlform.keyword_case.value+'';
    a.sql_identifier_case=document.sqlform.identifier_case.value+'';
    a.sql_n_indents=document.sqlform.n_indents.value+'';
    a.sql_output_format=document.sqlform.output_format.value+'';
    
    var s=JSON.stringify(a);
    localStorage[g_storage_name]=s;
  }
  
  function loadstorage(){
    if (!window.localStorage || !window.JSON) {
      return; 
    }
    try{
      var a=JSON.parse(localStorage[g_storage_name]);
    }catch(err){
      return;
    }
    
    if (!a) return;
    if (a.sql_remove_comments!=null) document.sqlform.remove_comments.checked=a.sql_remove_comments;
    if (a.sql_keyword_case!=null) document.sqlform.keyword_case.value=a.sql_keyword_case;
    if (a.sql_identifier_case!=null) document.sqlform.identifier_case.value=a.sql_identifier_case;
    if (a.sql_n_indents!=null) document.sqlform.n_indents.value=a.sql_n_indents;
    if (a.sql_output_format!=null) document.sqlform.output_format.value=a.sql_output_format;  
  }
  
  var isinited=false;
  function init(){
    sql_sample(true);
    loadstorage();
    isinited=true;
  }
  
  function deinit(){
    if (isinited) savestorage();
  }
</script>