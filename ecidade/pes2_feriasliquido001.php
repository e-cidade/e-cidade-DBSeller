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
require(modification("libs/db_conecta.php"));
require(modification("libs/db_sessoes.php"));
require(modification("libs/db_usuariosonline.php"));
require(modification("dbforms/db_funcoes.php"));

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js"></script>
</head>
<body>
<div class="container">
    <form name="form1" method="post">
        <fieldset>
            <legend>Relatório Líquido de Férias</legend>

            <table class="form-container">
                <tr>
                    <td>
                        <label>Competência:</label>
                    </td>
                    <td id="formularioCompetencia" width="500"></td>
                </tr>

                <tr>
                    <td>
                        <label for="exibeSemPeriodoGozo">Exibir Servidor Sem Período de Gozo:</label>
                    </td>
                    <td>
                        <select id="exibeSemPeriodoGozo" name="exibeSemPeriodoGozo" style="width: 105px;">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="field-size1">
                        <label for="ordem">Ordem:</label>
                    </td>
                    <td>
                        <select id="ordem" name="ordem" style="width: 105px;">
                            <option value="n">Numérica</option>
                            <option value="a">Alfabética</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>

        <input name="emite2" id="emite2" type="button" value="Emitir" onclick="js_emite();">

    </form>
</div>
</body>
<?php db_menu(); ?>
<script>

    var oCompetenciaFolha = new DBViewFormularioFolha.CompetenciaFolha(true);
    oCompetenciaFolha.renderizaFormulario($('formularioCompetencia'));

    function js_emite(){
      jan = window.open('pes2_feriasliquido002.php?ordem='+document.form1.ordem.value+'&ano='+document.form1.ano.value+'&mes='+document.form1.mes.value+'&exibeSemPeriodoGozo='+document.form1.exibeSemPeriodoGozo.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
      jan.moveTo(0,0);
    }
</script>
</html>






