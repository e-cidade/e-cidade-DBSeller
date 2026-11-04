<?PHP
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
require_once(modification("libs/db_"."conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

db_app::load("scripts.js");
db_app::load("strings.js");
db_app::load("prototype.js");
db_app::load("widgets/windowAux.widget.js");
db_app::load("widgets/dbmessageBoard.widget.js");
db_app::load("dbcomboBox.widget.js");
db_app::load("widgets/DBToogle.widget.js");

$clrotulo = new rotulocampo;
$clrotulo->label("ac16_sequencial");
$clrotulo->label("ac16_resumoobjeto");
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

<center>
    <form name="form1" method="post" action="">

        <fieldset style="margin-top: 30px; width: 600px;">
            <legend ><strong>Acerta Valores Item Acordo Dotação</strong></legend>


            <table align='left' >


                <tr>
                    <td nowrap title="<?php echo $Tac16_sequencial; ?>" width="130">
                        <?php db_ancora($Lac16_sequencial, "js_acordo(true);",1); ?>
                    </td>
                    <td colspan="2">
                        <?php
                        db_input('ac16_sequencial', 10, $Iac16_sequencial, true, 'text', 1, "onchange='js_acordo(false);'");
                        db_input('ac16_resumoobjeto', 40, $Iac16_resumoobjeto, true, 'text', 3);
                        ?>
                    </td>
                </tr>


            </table>


        </fieldset>
        <?php
        if (db_getsession('DB_login') === "dbseller" && db_getsession("DB_id_usuario") === "1") {
            ?>
            <input type="button" value='Processar' name='btnProcessa' id='btnProcessa' onclick='js_ProcessaAcerto();' style="margin-top: 10px;">
            <?php
        }
        ?>
    </form>

</center>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"),db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>

<script>

    var sUrlRpc = "aco4_acertaacordoitemdotacao.RPC.php";

    function js_ProcessaAcerto() {

        var oObject                 = new Object();
        oObject.exec            = "AcertaAcordoItemDotacao";
        oObject.ac16_sequencial = $F("ac16_sequencial");

        if (oObject.ac16_sequencial == '') {

            alert('Selecione um Acordo.');
            return false;
        }



        if ( !confirm("Iniciar Processamento ?") ) {
            return false;
        }

        js_divCarregando('Aguarde, Processando ConplanoExeSaldo...','msgBox');

        new Ajax.Request (sUrlRpc,{
                method:'post',
                parameters:'json='+Object.toJSON(oObject),
                onComplete:js_retornoProcessaConplanoExeSaldo
            }
        );
    }

    function js_retornoProcessaConplanoExeSaldo(oJson) {

        js_removeObj("msgBox");
        var oRetorno = eval("("+oJson.responseText+")");
        alert(oRetorno.sMessage.urlDecode());
        if (!oRetorno.lErro) {

            $("ac16_sequencial").value   = "";
            $("ac16_resumoobjeto").value = "";
        }
    }



    function js_acordo(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_acordo',
                'func_acordoinstit.php?funcao_js=parent.js_mostraAcordo1|ac16_sequencial|ac16_resumoobjeto',
                'Pesquisa',true);
        }else{
            if($F('ac16_sequencial').trim() != ''){
                js_OpenJanelaIframe('','db_iframe_depart',
                    'func_acordoinstit.php?pesquisa_chave='+$F('ac16_sequencial')+'&funcao_js=parent.js_mostraAcordo'+
                    '&descricao=true',
                    'Pesquisa',false);
            }else{
                $('ac16_resumoobjeto').value = '';
            }
        }
    }
    function js_mostraAcordo(chave, descricao, erro){

        $('ac16_resumoobjeto').value = descricao;
        if(erro==true){
            $('ac16_sequencial').focus();
            $('ac16_sequencial').value = '';
        }
    }
    function js_mostraAcordo1(chave1,chave2){
        $('ac16_sequencial').value = chave1;
        $('ac16_resumoobjeto').value = chave2;
        db_iframe_acordo.hide();
    }


</script>
