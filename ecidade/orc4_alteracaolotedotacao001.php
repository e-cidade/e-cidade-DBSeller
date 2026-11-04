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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script  type="text/javascript" src="scripts/scripts.js"></script>
    <script  type="text/javascript" src="scripts/strings.js"></script>
    <script  type="text/javascript" src="scripts/prototype.js"></script>
    <script  type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script  type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>


    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="container">

    <fieldset>
        <legend class="bold">Filtrar Dotações</legend>
        <table>
            <tr>
                <td class="bold"><a href="#" id="ancOrgao">Órgão:</a></td>
                <td>
                    <input type="text" id="o40_orgao" size="10">
                    <input type="text" id="o40_descr" size="60">
                </td>
            </tr>
            <tr>
                <td class="bold"><a href="#" id="ancUnidade">Unidade:</a></td>
                <td>
                    <input type="text" id="o41_unidade" size="10">
                    <input type="text" id="o41_descr" size="60">
                </td>
            </tr>
            <tr>
                <td class="bold"><a href="#" id="ancFuncao">Função:</a></td>
                <td>
                    <input type="text" id="o52_funcao" size="10">
                    <input type="text" id="o52_descr" size="60">
                </td>
            </tr>
            <tr>
                <td class="bold"><a href="#" id="ancSubfuncao">Subfunção:</a></td>
                <td>
                    <input type="text" id="o53_subfuncao" size="10">
                    <input type="text" id="o53_descr" size="60">
                </td>
            </tr>

            <tr>
                <td class="bold"><a href="#" id="ancPrograma">Programa:</a></td>
                <td>
                    <input type="text" id="o54_programa" size="10">
                    <input type="text" id="o54_descr" size="60">
                </td>
            </tr>

            <tr>
                <td class="bold"><a href="#" id="ancProjeto">Projeto/Atividade:</a></td>
                <td>
                    <input type="text" id="o55_projativ" size="10">
                    <input type="text" id="o55_descr" size="60">
                </td>
            </tr>

        </table>
    </fieldset>
    <p>
        <input type="button" id="btnPesquisa" value="Pesquisar" onclick="pesquisar()" />
    </p>
</div>
</body>
</html>
<?php db_menu(); ?>


<script>

    var input = {
        'codigo_orgao' : $('o40_orgao'),
        'codigo_unidade' : $('o41_unidade'),
        'codigo_funcao' : $('o52_funcao'),
        'codigo_subfuncao' : $('o53_subfuncao'),
        'codigo_programa' : $('o54_programa'),
        'codigo_projeto' : $('o55_projativ'),
    };

     var LookupOrgao = new DBLookUp($('ancOrgao'), input.codigo_orgao, $('o40_descr'), {
        "sArquivo" : "func_orcorgao.php",
        "sObjetoLookUp" : "db_iframe_orcorgao",
        "sLabel" : "Pesquisa de Órgão"
    });

     var LookupUnidade = new DBLookUp($('ancUnidade'), input.codigo_unidade, $('o41_descr'), {
         "sArquivo" : "func_orcunidade.php",
         "sObjetoLookUp" : "db_iframe_orcunidade",
         "sLabel" : "Pesquisa de Unidade"
     });

     var LookupFuncao = new DBLookUp($('ancFuncao'), input.codigo_funcao, $('o52_descr'), {
         "sArquivo" : "func_orcfuncao.php",
         "sObjetoLookUp" : "db_iframe_orcfuncao",
         "sLabel" : "Pesquisa de Função"
     });

     var LookupSubfuncao = new DBLookUp($('ancSubfuncao'), input.codigo_subfuncao, $('o53_descr'), {
         "sArquivo" : "func_orcsubfuncao.php",
         "sObjetoLookUp" : "db_iframe_orcsubfuncao",
         "sLabel" : "Pesquisa de Subfunção"
     });

     var LookupPrograma = new DBLookUp($('ancPrograma'), input.codigo_programa, $('o54_descr'), {
         "sArquivo" : "func_orcprograma.php",
         "sObjetoLookUp" : "db_iframe_orcprograma",
         "sLabel" : "Pesquisa de Programa"
     });

     var LookupProjeto = new DBLookUp($('ancProjeto'), input.codigo_projeto, $('o55_descr'), {
         "sArquivo" : "func_orcprojativ.php",
         "sObjetoLookUp" : "db_iframe_orcprojativ",
         "sLabel" : "Pesquisa de Projeto/Atividade"
     });



    function pesquisar() {

        let queryString = [];
        queryString.push('orgao='+input.codigo_orgao.value);
        queryString.push('unidade='+input.codigo_unidade.value);
        queryString.push('funcao='+input.codigo_funcao.value);
        queryString.push('subfuncao='+input.codigo_subfuncao.value);
        queryString.push('programa='+input.codigo_programa.value);
        queryString.push('projeto='+input.codigo_projeto.value);
        let implodeQueryString = 'orc4_alteracaolotedotacao002.php?'+queryString.join('&');
        js_OpenJanelaIframe('CurrentWindow.corpo', 'pesquisaDotacoes', implodeQueryString, 'Dotações Encontradas', true);
    }
</script>



