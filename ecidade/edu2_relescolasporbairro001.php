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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_classesgenericas.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("libs/db_app.utils.php"));

db_postmemory($_SERVER);
db_postmemory($_POST);

$clrotulo = new rotulocampo;

$db_opcao = 1;
$db_botao = true;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php 
  db_app::load('strings.js,scripts.js,datagrid.widget.js,prototype.js');
  db_app::load('estilos.css,grid.style.css');
?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
      <form name="form1" method="post" action="">
      <center>
      <table border="0">
	<tr>
	  <td colspan="4">
          <fieldset>
              <legend>Escolas por Bairro</legend>
              <table>
                <tr>
                    <td style="text-align: right;"><strong>Escola:</strong></td>
                    <td>
                        <select name="filtroEscola" id="filtroEscola">
                            <option value="0">TODAS</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>Bairro:</strong></td>
                    <td>
                        <select name="filtroBairro" id="filtroBairro">
                            <option value="0">TODOS</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>Tipo de Saída:</strong></td>
                    <td>
                        <select name="formatoImpressao" id="formatoImpressao">
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </td>
                </tr>
	          </table>
          </fieldset>
	  </td>
	</tr>
	
	</table>
        <button id="imprimir" type="button" onClick="js_relatorio();">
            <i class="fas fa-print"></i>
            Imprimir
        </button> 
      </center>
      </form>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>

const apiUrl = "edu4_escola.RPC.php";
const filtroEscola = document.getElementById('filtroEscola');
const filtroBairro = document.getElementById('filtroBairro');
const formatoImpressao = document.getElementById('formatoImpressao');

window.addEventListener('load', async () => {
    var oParametros = new Object();
    oParametros.exec = 'getEscolasBairros';
    const body = {
    method:'post',
    parameters: 'json='+Object.toJSON(oParametros),
    onComplete: js_retornoPreencheFiltros
    }
    js_divCarregando('Aguarde... Carregando filtros','msgbox');
    var oAjax = new Ajax.Request(apiUrl ,body);
});

const js_retornoPreencheFiltros = (oAjax) => {
    const retorno = JSON.parse(oAjax.responseText);
    retorno.bairros.each(function(bairro, iSeq) {
        var optionBairro = document.createElement("option");
        optionBairro.value = bairro.j13_codi;
        optionBairro.innerHTML = bairro.j13_descr.urlDecode();
        filtroBairro.appendChild(optionBairro);
   });

   js_removeObj("msgbox");

   retorno.escolas.each(function(escola, iSeq) {
        var optionEscola = document.createElement("option");
        optionEscola.value = escola.mo53_codigo;
        optionEscola.innerHTML = escola.mo53_nome.urlDecode();
        filtroEscola.appendChild(optionEscola);
   });
}

const js_relatorio = () => {
    if (formatoImpressao.value == 'pdf') {
        var query = "";
        if (filtroEscola.value > 0) {
            query += "?filtroEscola=" + filtroEscola.value;
        }

        if (filtroBairro.value > 0) {
            var separador = "&";
            if (query == "") {
                separador = "?";
            }

            query += separador + "filtroBairro=" + filtroBairro.value;
        }

        jan = window.open('edu2_relescolasporbairro002.php'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
        jan.moveTo(0,0);
        return false;
    }
  
    var oParametros = new Object();
    oParametros.exec = 'processarRelatorioEscolasPorBairroCSV';
    oParametros.filtroEscola = filtroEscola.value;
    oParametros.filtroBairro = filtroBairro.value;
    const body = {
    method:'post',
    parameters: 'json='+Object.toJSON(oParametros),
    onComplete: js_trataRetornoRelatorioCSV
    }
    js_divCarregando('Aguarde... Imprimindo o relatório','msgbox');
    var oAjax = new Ajax.Request(apiUrl, body);
}

const js_trataRetornoRelatorioCSV = (oAjax) => {
    const retorno = JSON.parse(oAjax.responseText);
    js_removeObj("msgbox");
    if (retorno.iStatus != 0 && retorno.pathRelatorioCSV != "") {
    jan = window.open(retorno.pathRelatorioCSV,'');
    } else {
    alert(retorno.sMessage.urlDecode());
    } 
}
  
</script>
