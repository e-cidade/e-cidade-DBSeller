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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/verticalTab.widget.php"));

$oGet = db_utils::postMemory($_GET);
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <?
        db_app::load("scripts.js, strings.js, prototype.js, datagrid.widget.js, widgets/windowAux.widget.js");
        db_app::load("widgets/dbmessageBoard.widget.js,widgets/dbtextField.widget.js");
        db_app::load("DBViewAcordoPrevisao.classe.js,widgets/dbtextFieldData.widget.js,classes/DBViewAcordoExecucao.classe.js, widgets/DBHint.widget.js");
        db_app::load("estilos.css, grid.style.css,tab.style.css");
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <center>
            <table><tr><td>
                <fieldset>
                    <Legend><b>Imprimir pesquisa</b></Legend>
                    <form>
                        <table>
                            <tr>
                                <td>
                                    <b>Escolha o que será impresso no relatório:</b>
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sDadosAcordo' value='1' checked>
                                    <label for='sDadosAcordo'>Dados do acordo</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sMovimentacoes' value='1' checked>
                                    <label for='sMovimentacoes'>Movimentações</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sPosicoes' value='1' checked>
                                    <label for='sPosicoes'>Posições</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sEmpenhamentos' value='1' checked>
                                    <label for='sEmpenhamentos'>Empenhamentos</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sComissões' value='1' checked>
                                    <label for='sComissões'>Comissões</label>
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Orientação da página:</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sRetrato' value='1' checked>
                                    <label for='sRetrato'>Retrato</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class='sOpcoes' type='checkbox' id='sPaisagem' value='1'>
                                    <label for='sPaisagem'>Paisagem</label>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <center><input type="button" name="btnImprimir" id="btnImprimir" value="Visualizar" onclick="js_imprimir()"/></center>
                    </form>
                </fieldset>
            </td></tr></table>
        </center>
    </body>
</html>

<script type="text/javascript">
function js_imprimir() {
    var sUrl = 'con4_consacordosdetalhesimprimir002.php?ac16_sequencial=<?=$oGet->ac16_sequencial?>';

    var checks = [
        $('sDadosAcordo').checked,
        $('sMovimentacoes').checked,
        $('sPosicoes').checked,
        $('sEmpenhamentos').checked,
        $('sComissões').checked
    ];

    if (!checks.includes(true)) {
        alert('Selecione pelo menos uma opção.');
        return false;
    }

    if ($('sDadosAcordo').checked) {
        sUrl += '&dadosAcordo=true';
    }

    if ($('sMovimentacoes').checked) {
        sUrl += '&movimentacoes=true';
    }

    if ($('sPosicoes').checked) {
        sUrl += '&posicoes=true';
    }

    if ($('sEmpenhamentos').checked) {
        sUrl += '&empenhamentos=true';
    }

    if ($('sComissões').checked) {
        sUrl += '&comissoes=true';
    }

    if ($('sRetrato').checked) {
        sUrl += '&retrato=true';
    }
    
    if ($('sPaisagem').checked) {
        sUrl += '&paisagem=true';
    }

    if ($('sRetrato').checked && $('sPaisagem').checked) {
        alert('Não é possível selecionar duas orientações de página.');
        return false;
    }

    if (!$('sRetrato').checked && !$('sPaisagem').checked) {
        alert('Selecione pelo menos uma orientação de página.');
        return false;
    }

    var oJanela = window.open(sUrl, 'relatorioacordo',
                'width='+(screen.availWidth)+', height='+(screen.availHeight-40)+', scrollbars=1, location=0');

    oJanela.moveTo(0,0);
}
</script>