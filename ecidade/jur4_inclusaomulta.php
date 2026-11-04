<?php
/**
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

require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_usuariosonline.php");
require_once("dbforms/db_funcoes.php");
require_once("libs/db_app.utils.php");
$clrotulo = new rotulocampo;
$clrotulo->label('v70_sequencial');
$clrotulo->label('v70_codforo');
$clrotulo->label('k02_descr');

$clrotulo2 = new rotulo('juridico.processoforomulta');
$clrotulo2->label();

$db_opcao = 1;

?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <?php
        db_app::load("scripts.js, strings.js, numbers.js, prototype.js, AjaxRequest.js, datagrid.widget.js");
        db_app::load("widgets/Collection.widget.js, widgets/DatagridCollection.widget.js, widgets/DBLookUp.widget.js" );
        ?>
        <link type="text/css" rel="stylesheet" href="estilos.css">
    </head>
    <body>
    <div class="container">
        <form name="form1" id="form1">
        <fieldset>
            <legend>Valores Adicionais</legend>
            <table class="form-container">
                <tr>
                    <td title="<?=@$Tv70_codforo?>" >


                        <?php
                        db_ancora(@$Lv70_codforo, "js_pesquisaprocessoforo(true);", 4);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("v70_sequencial",  10, $Iv70_sequencial, true, "text", 4, "onchange='js_pesquisaprocessoforo(false);'");
                        db_input("v70_codforo",  40, $Iv70_codforo,  true, "text", 3);
                        ?>
                    </td>
                </tr>
                <td nowrap title="Receita de Origem">
                    <label id="lblReceita" for="j150_receita">Receita:</label>
                </td>
                <td>
                    <?php
                    db_input('j150_receita',10,$Ij150_receita,true,'text',$db_opcao,"data='k02_codigo'");
                    db_input('k02_descr',   40,$Ik02_descr,true,'text',3);
                    ?>
                </td>
                <tr>
                    <td>
                        <b>Data de Lançamento:</b>
                    </td>
                    <td>
                        <?php db_inputdata("j150_data", null, null, null, true, 'text', $db_opcao )?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Percentual:</b>
                    </td>
                    <td>
                        <?php db_input("j150_percentual", 10, $Ij150_percentual, true, 'text', $db_opcao)?>
                    </td>
                </tr>

            </table>
        </fieldset>
        <input type="button" id="incluir" value="Incluir" onclick="return js_processar();">
        <input type="button" id="btnExcluir" value="Excluir" onclick="return excluirMulta();">
        </form>

        <div>
            <fieldset>
                <legend>Valores Adicionais Lançados ao Processo</legend>
                <div id="gridMultasContainer"></div>
            </fieldset>
        </div>
    </div>

    </body>
</html>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
<script>

    var RPC = "jur4_inclusaomulta.RPC.php";
    var collection         = new Collection().setId('id');
    var gridMultasLancadas = DatagridCollection.create(collection).configure("order", false);

    var campoReceita     = $('j150_receita');
    var descricaoReceita = $('k02_descr');
    var dataLancamento   = $('j150_data');
    var percentual       = $('j150_percentual');

    gridMultasLancadas.addColumn("codigo",    {label: "Código",     align: "center", width: "15%"});
    gridMultasLancadas.addColumn("receita",   {label: "Receita",    align: "center", width: "50%"});
    gridMultasLancadas.addColumn("data",      {label: "Data",       align: "center", width: "20%"});
    gridMultasLancadas.addColumn("percentual",{label: "%",          align: "center", width: "15%"});

    var lookupReceitas  = new DBLookUp($('lblReceita'), $('j150_receita'), $('k02_descr'),{sArquivo: 'func_tabrec.php'});
    gridMultasLancadas.show($('gridMultasContainer'));

    buscarMultasDoProcesso = function () {

        var codigoProcessoForo = $F('v70_sequencial');

        var oParam = {
          exec: "buscarMultasDoProcesso",
          dados : {
              processo: codigoProcessoForo
          }
        };

        limparCampos
        campoReceita.value = '';
        descricaoReceita.value = '';
        collection.clear();

        new AjaxRequest(RPC, oParam,  function(response, erro) {

            if (erro) {
                alert(response.message);
                return false;
            }

            for (var multa of response.multas) {
                collection.add(multa);
            }
            gridMultasLancadas.reload();

            consultaParcelamento(codigoProcessoForo);

        }).setMessage('Aguarde, pesquisando dados...').execute() ;
    };

    function consultaParcelamento(codigoProcessoForo) {

        new AjaxRequest(
            RPC,
            {
                exec : 'consultaParcelamento',
                codigoProcessoForo : codigoProcessoForo
            },
            function (resposta, erro) {

                if(erro) {
                    alert(resposta.message);
                    return false;
                }

                if(resposta.possuiParcelamento == true) {

                    alert("O processo informado possui parcelamentos vinculados, não será possível lançar valores adicionais. \nPara realizar o lançamento, os parcelamentos devem ser anulados.");
                    $('incluir').setAttribute('disabled', 'disabled');
                    $('btnExcluir').setAttribute('disabled', 'disabled');
                }
            }
        ).execute();
    }

    function js_processar(){

        if (empty($F('j150_percentual'))) {
            alert('Campo Percentual deve ser informado.');
            return false;
        }

        if (empty($F('v70_sequencial'))) {
            alert('Campo Processo do Foro deve ser informado.');
            return false;
        }

        if (empty($F('j150_data'))) {
            alert('Campo Data deve ser informada.');
            return false;
        }

        if (empty($F('j150_receita'))) {
            alert('Campo Data do Foro deve ser informada.');
            return false;
        }


        var oParam = new Object();
        oParam.exec = 'salvar';
        oParam.oDados = $('form1').serialize(true);


        new AjaxRequest(RPC, oParam,  function(response, erro) {

            alert(response.message);
            if (erro) {
                return false;
            }
            limparCampos();
            buscarMultasDoProcesso();

        }).setMessage('Salvando dados do Favorecido...').execute() ;
    }

    limparCampos = function(){

        campoReceita.value     = '';
        descricaoReceita.value = '';
        dataLancamento.value   = '';
        percentual.value       = '';
        gridMultasLancadas.reload();

    }

    function js_pesquisaprocessoforo(mostra) {

        if (mostra == true) {

            var sUrl = 'func_processoforo.php?lAnuladas=false&funcao_js=parent.js_mostraprocessoforo1|v70_sequencial|v70_codforo';
            js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', true);
        } else {

            if (document.form1.v70_sequencial.value != '') {

                var sUrl = 'func_processoforo.php?pesquisa_chave='+document.form1.v70_sequencial.value+'&funcao_js=parent.js_mostraprocessoforo'+'&lAnuladas=false';
                js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', false);
            } else {
              $('v70_codforo').value = '';
              collection.clear();
              gridMultasLancadas.reload();
            }
        }

        if ($F('v70_sequencial') == '') {
            $('incluir').setAttribute('disabled', 'disabled');
        }
    }

    function js_mostraprocessoforo(chave,erro,chave2){

        document.form1.v70_codforo.value = chave;
        $('v70_codforo').value = chave2;
        db_iframe_processoforo.hide();

        if ( $F('v70_sequencial') != '' ) {
            $('incluir').removeAttribute('disabled');
            $('btnExcluir').removeAttribute('disabled');
        }
        if (erro == true) {

            $('v70_sequencial').value = '';
            document.form1.v70_codforo.focus();
            document.form1.v70_codforo.value = '';
            $('v70_codforo').value = chave;
            return;
        }

        buscarMultasDoProcesso();
    }

    function js_mostraprocessoforo1(chave1,chave2){

        document.form1.v70_sequencial.value = chave1;
        document.form1.v70_codforo.value = chave2;
        db_iframe_processoforo.hide();

        if ( $F('v70_sequencial') != '' ) {
            $('incluir').removeAttribute('disabled');
            $('btnExcluir').removeAttribute('disabled');
        }

        buscarMultasDoProcesso();
    }

    function js_pesquisak70_recori(mostra){

        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_tabrec','func_tabrec.php?funcao_js=parent.js_mostratabrec1|k02_codigo|k02_descr','Pesquisa',true);
        }else{
            if(document.form1.j150_receita.value != ''){
                js_OpenJanelaIframe('','db_iframe_tabrec','func_tabrec.php?pesquisa_chave='+document.form1.j150_receita.value+'&funcao_js=parent.js_mostratabrec','Pesquisa',false);
            }else{
                document.form1.k02_descr.value = '';
            }
        }
    }

    function js_mostratabrec(chave,erro){
        document.form1.k02_descr.value = chave;
        if(erro==true){
            document.form1.j150_receita.focus();
            document.form1.j150_receita.value = '';
        }
    }

    function js_mostratabrec1(chave1,chave2){
        document.form1.j150_receita.value = chave1;
        document.form1.k02_descr.value  = chave2;
        db_iframe_tabrec.hide();
    }

    excluirMulta = function () {

        if (empty($F('v70_sequencial'))) {
            alert('Campo Processo do Foro deve ser informado.');
            return false;
        }
        if (!confirm('Confirma a exclusão das multas?')) {
            return;
        }

        var oParam = {
            exec: "excluirMultasDoProcesso",
            dados : {
                processo: $F('v70_sequencial')
            }
        };

        new AjaxRequest(RPC, oParam,  function(response, erro) {

            alert(response.message);
            if (erro) {
                return false;
            }
            collection.clear();
            gridMultasLancadas.reload();
        }).setMessage('Aguarde, excluindo multas do processo...').execute();

    }

$('v70_sequencial').className += ' field-size2';
$('v70_codforo').className += ' field-size8';
</script>