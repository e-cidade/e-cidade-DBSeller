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
require_once(modification("libs/db_conecta_plugin.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);

?>
<html>
<head>
<title>DBSeller Informática Ltda - Página Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script>
    function js_emite(){
        var sUrl = "edu2_professoresoutrosprofissionais002.php?";
        sUrl += "ano="+document.form1.ano.value;
        sUrl += "&formato=PDF";
        jan = window.open(sUrl,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
        jan.moveTo(0,0);
    }
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
    body {
        display: flex;
        justify-content: center;
    }
</style>
</head>
<body>
    <table width="790" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <br>
                <br>
                <form name="form1" method="post" action="">
                    <div align="center">
                        <table  align="center">
                            <tr>
                                <td nowrap>
                                    <fieldset>
                                        <legend><b>Professores e outros profissionais</b></legend>
                                        <table width="100%" border="0">
                                            <tbody>
                                                <tr>
                                                    <td title="Ano de referência" align="left">
                                                        <b> Ano de referência :</b>
                                                    </td>
                                                    <td>
                                                        <input title="" name="ano" type="text" id="ano" value="" size="10">
                                                        <input type="button" value="Processar" name="processar" onclick="js_emite();">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </td>
        </tr>
        <script>
            const inputEle = document.getElementById('ano');
            inputEle.addEventListener('keyup', function(e){
                var key = e.which || e.keyCode;
                if (key == 13) { 
                    js_emite();
                }
            });
        </script>
    </body>
</html>
