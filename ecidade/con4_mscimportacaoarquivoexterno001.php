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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
    db_app::load("AjaxRequest.js");
    db_app::load("prototype.js");
    db_app::load("widgets/windowAux.widget.js");
    db_app::load("datagrid.widget.js");
    db_app::load("strings.js");
    db_app::load("grid.style.css");
    db_app::load("estilos.css");
    db_app::load("widgets/dbmessageBoard.widget.js");
    db_app::load("dbcomboBox.widget.js");
    ?>

    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<div class="container">

    <form name="form1" method="post" action="">
        <fieldset style="margin-top:50px; width: 650px;">
            <legend><strong>Filtros para Pesquisa</strong></legend>
            <table  align="left"  cellpadding="2" cellspacing="2" border="0">
                <tr>
                    <td class="bold">
                        <label for="ano">Competência:</label>
                    </td>
                    <td>
                        <input type="text" id="ano" name="ano" maxlength="4" size="10" onkeypress="return js_teclas(event)">
                        <input type="text" id="mes" name="mes" maxlength="2" size="5"  onkeypress="return js_teclas(event)">
                    </td>
                </tr>
                <tr>
                    <td class="bold">
                        <label for="codigo_tribunal">Código do tribunal:</label>
                    </td>
                    <td colspan="3">
                        <input type="text" id="codigo_tribunal" name="codigo_tribunal" maxlength="4" size="10">
                    </td>
                </tr>
                <tr>
                    <td style="width: 70px;"><b>Arquivo:</b></td>
                    <td>
                        <input type="file" id="arquivo" name="arquivo" style="height: 25px;"/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="button" id="btnImportar" name="btnImportar" value="Importar">
        <fieldset style="margin-top:10px; width: 650px;">
            <legend><strong>Arquivos Importados</strong></legend>
            <div id='gridRegistros' style='width:99%; float:left;'> </div>
        </fieldset>
    </form>

</div>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>

    var sURLRPC = "con4_mscimportacaoarquivoexterno.RPC.php";

    function js_criaGrid()
    {
        oGridRegistros = new DBGrid('Registros');
        oGridRegistros.nameInstance = 'oGridRegistros';
        oGridRegistros.setCellWidth( new Array('60%','20%','20%','20%') );
        oGridRegistros.setCellAlign( new Array('left','center','center','center'));
        oGridRegistros.setHeader( new Array('Arquivo', 'Competência', 'Cód. Trib.', 'Ações'));
        oGridRegistros.setHeight((screen.availHeight-800));
        oGridRegistros.show($('gridRegistros'));
    }

    function js_pesquisar()
    {

        oGridRegistros.clearAll(true);

        var oParametros  = new Object();
        var msgDiv       = "Carregando lista de arquivos importados\n Aguarde ...";
        oParametros.exec = 'getArquivos';

        js_divCarregando(msgDiv,'msgBox');

        var oAjaxLista  = new Ajax.Request(sURLRPC,
            {
                method: "post",
                parameters:'json='+Object.toJSON(oParametros),
                onComplete: js_retornoPesquisar
            });
    }

    function js_retornoPesquisar(oAjax)
    {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 1) {

            oRetorno.arquivos.each(
                function (oDado, iInd) {

                    aRow     = new Array();
                    aRow[0]  = oDado.nome;
                    aRow[1]  = oDado.competencia;
                    aRow[2]  = oDado.codtrib;
                    var sHtmlAcoes  = "<input type='button' id='btnExcluir"+oDado.codigo+"' value='Excluir' onclick='js_excluir("+oDado.codigo+")'>";
                    aRow[3]  = sHtmlAcoes;
                    oGridRegistros.addRow(aRow);
                });

            oGridRegistros.renderRows();
            if (oRetorno.dados.length == 0) {
                oGridRegistros.setStatus('Nenhuma arquivo encontrado!');
            }
        } else {
            alert(oRetorno.message.urlDecode());
        }
    }

    $('btnImportar').observe('click', function()
    {
        if ($F('arquivo') == '') {
            alert("Campo Arquivo é de preenchimento obrigatório.");
            return false;
        }

        var sMensagem = "Confirma a importação do arquivo?";

        if (!confirm(sMensagem)) {
            return false;
        }

        var anoCompetencia = $F('ano');
        var mesCompetencia = $F('mes');
        var codigoTribunal = $F('codigo_tribunal');

        if (anoCompetencia === undefined || anoCompetencia === "") {
            alert('Favor informar o ano da competência.');
            return false;
        }

        if (mesCompetencia === undefined || mesCompetencia === "") {
            alert('Favor informar o mês da competência.');
            return false;
        }

        if (codigoTribunal === undefined || codigoTribunal === "") {
            alert('Código do tribunal não informado. Por favor informe para prosseguir.');
            return false;
        }

        var oParametros = {
            exec: "importarArquivo",
            ano : anoCompetencia,
            mes : mesCompetencia,
            codigo_tribunal: codigoTribunal
        };

        new AjaxRequest(
            sURLRPC,
            oParametros,
            function(oRetorno, lErro) {
                alert(oRetorno.message.urlDecode());
                $('arquivo').value = '';
                js_pesquisar();
            }
        ).addFileInput($('arquivo'))
            .setMessage('Aguarde, efetuando o upload do arquivo...')
            .execute();
    });

    function js_excluir (codigo)
    {
        var sMensagem = "Confirma a exclusão do arquivo?";

        if (!confirm(sMensagem)) {
            return false;
        }

        var oParametros = { exec: "remover", codigo: codigo};

        new AjaxRequest(
            sURLRPC,
            oParametros,
            function(oRetorno, lErro) {
                alert(oRetorno.message.urlDecode());
                $('arquivo').value = '';
                $('ano').value = '';
                $('mes').value = '';
                js_pesquisar();
            }
        ).setMessage('Aguarde, efetuando a exclusão do arquivo...')
         .execute();
    }

    js_criaGrid();
    js_pesquisar();

</script>
