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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));

db_postmemory($_POST);


?>
<html>
 <head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
 </head>
 <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="" >
   <form name="form1" method="post" action="" class="container">
     <fieldset ><legend><b>Relatório de turmas cadastradas por escola e por ano letivo</b></legend>
       <table class='form-container'>
       <tr>    
          <?
              echo '<td class="bold field-size2">'; 
              echo ' <b>Escola:</b>';
              echo '</td>';
              echo '<td>';  
                    $modulo = db_getsession("DB_modulo"); 
                    $departamento = db_getsession("DB_coddepto");
      
                    //CASO ESCOLA/SECRETARIA;
                    if ($modulo == "1100747" || $modulo == "7159"){
                      
                      $sWhere = "";
                      // Escola
                      if($modulo == "1100747"){
                        $sWhere = "ed18_i_codigo = {$departamento}";
                      }

                      $oDaoEscola     = new cl_escola;  
                      $sSqlEscola     = $oDaoEscola->sql_query_file("","distinct ed18_i_codigo,ed18_c_nome","ed18_c_nome",$sWhere);                                                                      
                      $rsResultEscola = $oDaoEscola->sql_record($sSqlEscola);            
                      $iLinhas        = $oDaoEscola->numrows;                       
                      
                      echo '<select name="escola" id="escola"  onchange="js_buscaAno();">';
                      if($iLinhas > 1){
                        echo ' <option value="">Todos</option>'; 
                      }

                      for ($iCont = 0; $iCont < $iLinhas; $iCont++) {                       
                        $oDadosEscola = db_utils::fieldsmemory($rsResultEscola,$iCont);                          
                        echo " <option value='$oDadosEscola->ed18_i_codigo'>$oDadosEscola->ed18_c_nome</option>";               
                      }                   
                      echo ' </select>';
                      echo '</td>';

                    }         
            ?>          
         </tr>
         <tr>
           <td class='bold field-size2'>Ano:</td>
           <td colspan="5">
             <select id='ano' style="width: 100%;" onchange = "js_liberaimpressao()">
               <option value='' selected="selected">Selecione o ano</option>
             </select>
           </td>
         </tr>
       </table>
      </fieldset>
    <input type="button" name="imprimir" id="imprimir" value="Imprimir" onclick="js_emite()" disabled='disabled' />
  </form>
<?
db_menu();
?>
 </body>
</html>
<script>

var sRPC          = 'edu_educacaobase.RPC.php';

var oOptionSelecioneEtapa       = document.createElement('option');
oOptionSelecioneEtapa.value     = "";
oOptionSelecioneEtapa.innerHTML = "Selecione uma etapa";

var oOptionSelecioneAno       = document.createElement('option');
oOptionSelecioneAno.value     = "";
oOptionSelecioneAno.innerHTML = "Selecione o ano";

var oOptionTodos       = document.createElement('option');
oOptionTodos.value     = "";
oOptionTodos.innerHTML = "Todos";

/**
 * Busca os anos dos calendários da escola selecionada ou de todos calendários
 */

function js_buscaAno() {

  $('imprimir').setAttribute('disabled', 'disabled');

  var oParamentro     = new Object();
  oParamentro.exec    = 'pesquisaAnoLetivoEscola';
  oParamentro.iEscola = $F('escola');


  $('ano').options.length = 0;
  $('ano').appendChild(oOptionSelecioneAno);
  
  js_divCarregando("Aguarde, buscando ano...", "msgBox");
  new Ajax.Request(sRPC,
                   {method:     'post',
                    parameters: 'json='+Object.toJSON(oParamentro),
                    onComplete: js_retornoAno 
                   } 
                  );
}

function js_retornoAno(oAjax) {

  if ($('msgBox')) {
    js_removeObj('msgBox');
  }
  var oRetorno = JSON.parse(oAjax.responseText);

  if (oRetorno.status == 2) {
    
    alert(oRetorno.message.urlDecode());
    return false;
  }
  
  oRetorno.aAno.each(function (oAno) {

    var oOption       = document.createElement('option');
    oOption.value     = oAno.ed52_i_ano;
    oOption.innerHTML = oAno.ed52_i_ano;
    $('ano').appendChild(oOption);
  });


}

function js_liberaimpressao(){
  if($F('ano') != ''){
      $('imprimir').removeAttribute('disabled');
  }
  else {
    $('imprimir').setAttribute('disabled', 'disabled');
  }
}

js_buscaAno();


function js_emite(){

var sUrl  = 'edu2_turmascadastradasescolaanoletivo002.php?';
sUrl += 'escola='+$F('escola');
sUrl += '&anousu='+$F('ano');

jan = window.open(sUrl);
jan.moveTo(0,0);

}


</script>