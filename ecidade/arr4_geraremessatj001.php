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

set_time_limit(0);
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

db_app::load("scripts.js");
db_app::load("prototype.js");
db_app::load("strings.js");

$instit = db_getsession("DB_instit");
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC bgcolor="#CCCCCC">

<center>
  <form name="form1" method="post">
    <fieldset style="margin-top: 50px;width: 500px;">
      <legend><b>Geração de Arquivos de Remessa de Cobrança Registrada ao TJ	</b></legend>
      <table border="0">
        <tr>
          <td align="right" nowrap title="Data" >
            Data para Processamento
          </td>
          <td align="left">
            <?php
              db_inputdata("dtProc", null, null, null, true, 'text', 1);
            ?>
          </td>
        </tr>
      </table>
    </fieldset> 
            <input type="button" id="processar" style="margin-top: 10px;"  value="Procesar" onclick="js_processar();">
  </form>
</center>
<?php 
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>

<script>

var sUrlRPC = "arr4_arqremessacobranca.RPC.php";

function js_processar(){

  var oParametros                        = new Object();
  var msgDiv                             = "Processando Arquivo \n Por Favor Aguarde ...";
  oParametros.exec                       = 'geraArqTj';  
  oParametros.dtProc                     = $F("dtProc");   
  if ( oParametros.dtProc == "") {
    alert("Informe a data para processamento do arquivo");
    return false;
  }  
  
  js_divCarregando(msgDiv,'msgBox');
   
   var oAjaxLista  = new Ajax.Request(sUrlRPC,
                                             {method: "post",
                                              parameters:'json='+Object.toJSON(oParametros),
                                              onComplete: js_retornoProcessar
                                             }); 

}

function js_retornoProcessar(oAjax){

	js_removeObj('msgBox');
	
  var oRetorno = JSON.parse(oAjax.responseText);
    
  if (oRetorno.status == 1) {
      
    if ( oRetorno.arquivo.length == 0 ) {
      return false;
    } 
      
    var listagem  = oRetorno.arquivo.urlDecode()+"# Download do Arquivo - "+ oRetorno.arquivo.urlDecode();
        js_montarlista(listagem,'form1');      
          
  } else {
    
    alert(oRetorno.message.urlDecode());
    location.href = "arr4_geraremessatj001.php";
    
  }

}

</script>