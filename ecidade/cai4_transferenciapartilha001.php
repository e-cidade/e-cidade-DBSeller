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
require_once(modification("classes/db_empautoriza_classe.php"));

$clempautoriza = new cl_empautoriza;
db_postmemory($HTTP_POST_VARS);

$clempautoriza->rotulo->label();

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
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
<center>

    <div id='ficha' style="position: absolute; float:left;background-color:#ccc; width: 100%; height: 100%; display: none; padding-top: 10px;">
    </div>

    <form name="form1" method="post" action="">

        <fieldset style="margin-top:50px; width: 650px;">
            <legend><strong>Filtros para Pesquisa</strong></legend>

            <table  align="left"  cellpadding="2" cellspacing="2" border="0">
                <tr>
                    <td nowrap title="Código de retorno do arquivo bancário">
                        <?php
                          db_ancora("<b>Código de retorno do arquivo:</b>","js_pesquisacodret(true);", 1);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('codret',10,"",true,'text',1," onchange='js_pesquisacodret(false);'");
                        db_input('arqret',40,"",true,'text',3,'');
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="Código da classificação do arquivo">
                        <b>Código da classificação do arquivo:</b>
                    </td>
                    <td>
                        <select id="cboClassificacoes" name="cboClassificacoes">
                            <option value="0">Selecione</option>
                        </select>
                    </td>
                </tr>

            </table>

        </fieldset>
        <br>
        <input type="button" value="Pesquisar" name="pequisar" onclick="js_pesquisar();">

    </form>


</center>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
    var sURLRPC = "cai4_transferenciapartilha.RPC.php";

    /**
     * Array com os itens da autorizacao selecionada
     */
    var aItensAutorizacao = new Array();


    function js_montaWindow() {

        if ($('gridRegistros')) {
            return true;
        }
        var sContent  = "<div id='gridRegistros' style='width:99%; float:left;'> </div>                                  ";
        sContent += "<div style='width:99%; float:left;'>                                                               ";
        sContent += " <table border = '0' align='center' style='margin-top:20px;'>                                      ";
        sContent += "   <tr> ";
        sContent += "     <td> <input type='button' value='Confirmar' onclick='js_gerarSlips();' /></td>        ";
        sContent += "     <td> <input type='button' value='Fechar' onclick='windowSlips.destroy();' />  </td>    ";
        sContent += "   </tr>                                                                                           ";
        sContent += " </table>                                                                                          ";
        sContent += "</div>                                                                                             ";


        windowSlips  = new windowAux('wndSlips', 'Lista de transferências', (screen.availWidth - 130), (screen.availHeight-250));
        windowSlips.setContent(sContent);
        windowSlips.allowCloseWithEsc(false);
        windowSlips.setShutDownFunction(function(){
            windowSlips.destroy();
        });
        oMsgBoardAutorizacoes = new DBMessageBoard('msgboardSlips',
            'Transferências disponíveis para o arquivo selecionado',
            'Transferências disponíveis para o arquivo selecionado, clique em confirmar para gerar as transferências.',
            windowSlips.getContentContainer()).show();
        windowSlips.show(50,50);

        js_gridRegistros();

    }

    function js_gridRegistros() {


        oGridRegistros = new DBGrid('Registros');

        oGridRegistros.nameInstance = 'oGridRegistros';
        oGridRegistros.setCellWidth( new Array('30%','5%','20%','5%','30%','10%') );
        oGridRegistros.setCellAlign( new Array('left','center','left','center','left','right' ));
        oGridRegistros.setHeader( new Array('Favorecido',
                                            'Cod. Rec',
                                            'Receita',
                                            'Reduz',
                                            'Conta',
                                            'Valor',
                                            'lista_idret',
                                            'cgm'));
        oGridRegistros.setHeight((screen.availHeight-450));
        oGridRegistros.aHeaders[6].lDisplayed = false;
        oGridRegistros.aHeaders[7].lDisplayed = false;
        oGridRegistros.show($('gridRegistros'));
    }

    function js_pesquisar(){

        var iCodRet = $F('codret');

        var codcla  = $F('cboClassificacoes');
        if (codcla === undefined || codcla == "0") {
            alert("Selecione o código da classificação do arquivo para continuar.");
            return false;
        }
        js_montaWindow();

        oGridRegistros.clearAll(true);

        var oParametros     = new Object();
        var msgDiv          = "Carregando lista de transferências\n Aguarde ...";
        oParametros.exec    = 'pesquisarRegistros';
        oParametros.iCodRet = iCodRet;
        oParametros.codcla  = $('cboClassificacoes').value;

        js_divCarregando(msgDiv,'msgBox');

        var oAjaxLista  = new Ajax.Request(sURLRPC,
            {
                method: "post",
                parameters:'json='+Object.toJSON(oParametros),
                onComplete: js_retornoPesquisarPartilhas
            });
    }

    function js_retornoPesquisarPartilhas(oAjax) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 1) {

            oRetorno.dados.each(
                function (oDado, iInd) {

                    aRow = new Array();
                    aRow[0]  = oDado.nome.urlDecode();
                    aRow[1]  = oDado.receita_codigo;
                    aRow[2]  = oDado.receita_descricao.urlDecode();
                    aRow[3]  = oDado.reduzido;
                    aRow[4]  = oDado.conta.urlDecode();
                    aRow[5]  = js_formatar(oDado.valor,'f');
                    aRow[6]  = '';
                    aRow[7]  = oDado.cgm;
                    oGridRegistros.addRow(aRow);
                });

            oGridRegistros.renderRows();
            if (oRetorno.dados.length == 0) {
                oGridRegistros.setStatus('Nenhuma Autorização encontrada!');
            }
        } else {
            alert(oRetorno.message.urlDecode());
        }
    }

    function js_gerarSlips() {

        var aSlips = oGridRegistros.getRows();
        var codcla  = $F('cboClassificacoes');
        if (codcla === undefined || codcla == "0") {
            alert("Selecione o código da classificação do arquivo para continuar.");
            return false;
        }

        var sMsg  = 'Confirma a geração das transferências de custas?';
        if (!confirm(sMsg)) {
            return false;
        }
        js_divCarregando('Aguarde, processando', 'msgBox');
        var aSlipsGerar = new Array();
        aSlips.each( function(oSlip, iSeq) {
            aSlipsGerar.push(  {
                conta_credito: oSlip.aCells[1].getValue(),
                conta_debito:  oSlip.aCells[3].getValue(),
                valor:         js_strToFloat(oSlip.aCells[5].getValue()).valueOf(),
                cgm:           oSlip.aCells[7].getValue()
            } );
        });

        var oParametros         = new Object();
        oParametros.exec        = 'gerarSlip';
        oParametros.codcla      = codcla;
        oParametros.aSlipsGerar = aSlipsGerar;

        var oAjaxLista  = new Ajax.Request(sURLRPC,
            {method: "post",
                parameters:'json='+Object.toJSON(oParametros),
                onComplete: js_retornoGerarSlips
            });
    }

    function js_retornoGerarSlips(oAjax) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {

            alert(oRetorno.message.urlDecode());

            windowSlips.destroy();
            buscaClassificacoes();
        } else {
            alert(oRetorno.message.urlDecode());
        }
    }

    function buscaClassificacoes(){

        var oParametros    = new Object();
        oParametros.exec   = 'buscarClassificacoes';
        oParametros.codret = $('codret').value;
        js_divCarregando('Aguarde, buscando classificações do arquivo', 'msgBox');

        var oAjaxLista  = new Ajax.Request(sURLRPC,
            {method: "post",
                parameters:'json='+Object.toJSON(oParametros),
                onComplete: retornoBuscaClassificacoes
            });
    }

    function retornoBuscaClassificacoes(oAjax)
    {
        js_removeObj('msgBox');

        var oRetorno = JSON.parse(oAjax.responseText);
        var oCboClassificacoes = $('cboClassificacoes');
        oCboClassificacoes.options = 0;

        oRetorno.dados.each(function(classificacao) {

            var sDescricao = "Classificação: "+js_formatar(classificacao.data_classificacao,'d')+" Autenticação: "+js_formatar(classificacao.data_autenticacao,'d');
            var oOption = new Option(sDescricao.urlDecode(), classificacao.classificacao);
            oCboClassificacoes.add(oOption);
        });
    }


    function js_pesquisacodret(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_disarq','func_disarq_alt.php?funcao_js=parent.js_mostracodret1|codret|arqret','Pesquisa',true);
        }else{
            if(document.form1.codret.value != ''){
                js_OpenJanelaIframe('','db_iframe_disarq','func_disarq_alt.php?pesquisa_chave='+document.form1.codret.value+'&funcao_js=parent.js_mostracodret','Pesquisa',false);
            }else{
                document.form1.arqret.value = '';
            }
        }
    }

    function js_mostracodret(chave,erro){
        document.form1.arqret.value = chave;
        if(erro==true){
            document.form1.codret.focus();
            document.form1.codret.value = '';
            return false;
        }
        buscaClassificacoes();
    }

    function js_mostracodret1(chave1,chave2){
        document.form1.codret.value = chave1;
        document.form1.arqret.value = chave2;
        db_iframe_disarq.hide();
        buscaClassificacoes();
    }

</script>
