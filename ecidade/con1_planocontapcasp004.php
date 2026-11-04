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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_libdicionario.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_conparametro_classe.php"));

$iOpcao = 1;
$oGet   = db_utils::postMemory($_GET);

$planoConta = new ContaPlanoPCASP($oGet->iCodigoConta, db_getsession("DB_anousu"), null, db_getsession("DB_instit"));
$iTipoConta = $planoConta->getTipoConta();
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <?php
    db_app::load("scripts.js");
    db_app::load("prototype.js");
    db_app::load("strings.js");
    db_app::load("dbautocomplete.widget.js");
    db_app::load("DBViewContaBancaria.js");
    db_app::load("dbmessageBoard.widget.js");
    db_app::load("estilos.css");
    db_app::load("dbtextField.widget.js");
    db_app::load("dbcomboBox.widget.js");
    db_app::load("prototype.maskedinput.js");
    db_app::load("windowAux.widget.js");
    db_app::load("AjaxRequest.js");
    db_app::load("grid.style.css, datagrid.widget.js");

    $iCodigoRecurso = "0";
    $sDescricaoRecurso = "NÃO INFORMADO";
    ?>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body style="margin-top:25px;" onload="$('iCodigoInstituicao').focus()">

<form>
    <div class="container">
        <fieldset style="width: 600px">
            <legend><b>Reduzidos</b></legend>
            <table class="form-container">
                <tr>
                    <td><b>Código Conta:</b></td>
                    <td>
                        <?php
                        db_input("iCodigoConta", 10, null, true, "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Reduzido:</b></td>
                    <td>
                        <?php
                        db_input("iCodigoReduzido", 10, null, true, "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a id="ancoraFonteRecurso" href="#">Fonte de Recursos:</a>
                    </td>
                    <td>
                        <input type="hidden" id="iCodigoRecurso" readonly class="readonly">
                        <input type="text" style="width: 60px;" id="gestao" readonly class="readonly">
                        <input type="text" style="width: 60px;" id="o15_recurso" readonly class="readonly">
                        <input type="text" style="width: 360px;" id="descricaoRecurso" lang="descricao" readonly
                               class="readonly">
                    </td>
                </tr>
                <tr>
                    <td><label for="complementoRecurso" class="bold">Complemento:</label></td>
                    <td>
                        <input type="text" style="width: 122px;" id="o15_complemento" readonly class="readonly">
                        <input type="text" style="width: 360px;" id="o200_descricao" readonly class="readonly">
                    </td>
                </tr>
                <tr>
                    <td id="tdInstituicao">
                        <?php
                        db_ancora("<b>Instituição:</b>", "js_pesquisaInstituicao(true)", 1);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("iCodigoInstituicao", 10, null, false, "text", 1, "onchange='js_pesquisaInstituicao(false);'");
                        db_input("sDescricaoInstituicao", 50, null, true, "text", 3);
                        ?>
                    </td>
                </tr>

                <tr id='trdivContaBancaria' style='display:none'>
                    <td>
                        <?php
                        db_ancora("<b>Conta Bancária:</b>", "js_abreContaBancaria(true)", 1, null, "iAncoraConta");
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input("iContaBancaria", 5, false, 3, "text", 3);
                        db_input("sDescricaoContaBancaria", 35, false, 3, "text", 3, "", "", "", "width:81%;");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <p><input type="button" name="btnIncluirReduzido" id="btnIncluirReduzido" value="Incluir"></p>
    </div>
</form>
<fieldset>
    <legend><b>Reduzidos Cadastrados</b></legend>
    <div id="divGridReduzidos">

    </div>
</fieldset>
</body>
</html>


<script type="text/javascript">


    var oGridReduzido              = new DBGrid('oGridReduzido');
    oGridReduzido.nameInstance = 'oGridReduzido';
    oGridReduzido.sName        = 'oGridReduzido';
    oGridReduzido.setCellAlign(["center","center", "left", "left", "left", "center", 'center']);
    aHeaders                   = ["Código Conta", "Reduzido", "Instituição", "Recurso", 'Conta Bancária', "Ação", "Instit", "Recurso", 'ContaBancaria'];
    oGridReduzido.aWidths      = ["15%", "15%", " 25%", "20%", "20%", "5%"];
    oGridReduzido.setHeader(aHeaders);
    oGridReduzido.aHeaders[6].lDisplayed = false;
    oGridReduzido.aHeaders[7].lDisplayed = false;
    oGridReduzido.aHeaders[8].lDisplayed = false;
    oGridReduzido.show($('divGridReduzidos'));

    function js_carregaReduzidos() {

        js_divCarregando("Aguarde, carregando reduzidos...", "msgBox");

        var oParam          = new Object();
        oParam.exec         = "getReduzidos";
        oParam.iCodigoConta = <?php echo @$oGet->iCodigoConta;?>;

        var oAjax = new Ajax.Request("con4_conplanoPCASP.RPC.php",
            {method:'post',
                parameters:'json='+Object.toJSON(oParam),
                onComplete: js_preencheGridReduzidos
            }
        );
    }

    function js_preencheGridReduzidos(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oAjax.responseText);

        oGridReduzido.clearAll(true);
        if (oRetorno.aContasReduzidas.length > 0) {
            oRetorno.aContasReduzidas.each(function (oReduz, iLinha) {

                var descricaoConta = '';
                if (oReduz.db83_sequencial !== '') {

                    descricaoConta  = "Bco: "+oReduz.db89_db_bancos+" Ag: "+oReduz.db89_codagencia+"-"+oReduz.db89_digito;
                    descricaoConta += " Cta: "+oReduz.db83_conta+"-"+oReduz.db83_dvconta;
                }

                var aLinha = [];
                aLinha[0]  = oReduz.c61_codcon;
                aLinha[1]  = oReduz.c61_reduz;
                aLinha[2]  = oReduz.codigo +" - "+ oReduz.nomeinst.urlDecode();
                //aLinha[3]  = oReduz.o15_codigo +" - "+ oReduz.o15_descr.urlDecode();
                aLinha[3]  = oReduz.gestao +" - "+ oReduz.descricao.urlDecode() + ' - ' + oReduz.o200_descricao.urlDecode();
                aLinha[4]  = descricaoConta;
                aLinha[5]  = '<input type="button" id="btnReduzAlt_'+iLinha+'"';
                aLinha[5] += '       value="A" title="Alterar Registro" onclick="js_alterarReduzido('+iLinha+');">&nbsp;';
                aLinha[5] += '<input type="button" id="btnReduzExc_'+iLinha+'" value="E"';
                aLinha[5] += '       title="Excluir Registro" onclick="js_excluirReduzido('+oReduz.c61_reduz+', '+oReduz.codigo+')">';
                aLinha[6]  = oReduz.codigo; //instituicao
                aLinha[7]  = oReduz.o15_codigo; //recurso
                aLinha[8]  = ''; //contabancaria
                // Bco: 237 Ag: 0012-6 Cta: 99998-9
                if (oReduz.db83_sequencial !== '') {
                    aLinha[8] = oReduz.db83_sequencial+"#"+descricaoConta;
                }

                oGridReduzido.addRow(aLinha);
            });

            oGridReduzido.renderRows();
        }

        var oEvent = new Event('change');
        (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_conta.$('sIndicadorSuperavit').dispatchEvent(oEvent);
    }

    /**
     * Preenche os inputs do cadastro de REDUZIDO
     */
    function js_alterarReduzido(iLinha, iReduzido) {

        var oRowGrid = oGridReduzido.aRows[iLinha];

        $("iCodigoInstituicao").readOnly              = true;
        $("iCodigoInstituicao").style.backgroundColor = "#DEB887";
        $("iCodigoInstituicao").style.color           = "#000";
        $("tdInstituicao").innerHTML                  = "<b>Instituição:</b>";
        $('iCodigoReduzido').value    = oRowGrid.aCells[1].getValue();
        $('iCodigoInstituicao').value = oRowGrid.aCells[6].getValue();
        $('iCodigoRecurso').value     = oRowGrid.aCells[7].getValue();
        $('iContaBancaria').value = '';
        $('sDescricaoContaBancaria').value = '';
        var dadosContaBancaria = oRowGrid.aCells[8].getValue();

        if (dadosContaBancaria.trim() !== '') {
            var splitDadosContaBancaria = dadosContaBancaria.split('#');
            $('iContaBancaria').value = splitDadosContaBancaria[0];
            $('sDescricaoContaBancaria').value = splitDadosContaBancaria[1];

            js_verificaConciliacaoAbertaPorReduzido( $F('iCodigoReduzido') );
        }

        buscaRecursoPorId($('iCodigoRecurso').value);
        js_pesquisaInstituicao(false);
        $('btnIncluirReduzido').value = "Alterar";
    }


    function js_verificaConciliacaoAbertaPorReduzido(iReduzido)
    {
        let oParametros           = new Object();
            oParametros.exec      = "verificaConciliacaoAbertaPorReduzido";
            oParametros.iReduzido = iReduzido;
            oParametros.iCodCon   = $F("iCodigoConta");
            oParametros.iInstit   = $F("iCodigoInstituicao");

        new AjaxRequest("con4_conplanoPCASP.RPC.php", oParametros, js_retornoVerificaConciliacaoAbertaPorReduzido).execute();

    }
    function js_retornoVerificaConciliacaoAbertaPorReduzido(oRetorno)
    {
        // se possui conciliacao bloqueamos o campo para nao alterar a conta
        if (oRetorno.lPossuiConciliacaoVinculada) {

            $("iAncoraConta").setAttribute("onclick", "alert('Conta com Conciliacao Bancária nao pode ser alterada.')");
            $("iAncoraConta").setAttribute("class", "");
            $("iAncoraConta").setAttribute("style", "");

        } else {  // senao volta o campo ao estado normal

            $("iAncoraConta").setAttribute("class", "DBAncora");
            $("iAncoraConta").setAttribute("onclick", "js_abreContaBancaria(true)");
            $("iAncoraConta").setAttribute("style", "text-decoration:underline");

        }
    }



    /**
     *  Exclui reduzido
     */
    function js_excluirReduzido(iReduzido, iCodigoInstituicao) {

        if (!confirm("Confirma a exclusão do reduzido "+iReduzido+"?")) {
            return false;
        }

        js_divCarregando("Aguarde, removendo reduzido...", "msgBox");
        var oParam                = {};
        oParam.exec               = "excluirReduzido";
        oParam.iCodigoReduzido    = iReduzido;
        oParam.iCodigoInstituicao = iCodigoInstituicao;
        oParam.iCodigoPlanoConta  = <?php echo $oGet->iCodigoConta;?>;

        var oAjax = new Ajax.Request("con4_conplanoPCASP.RPC.php",
            {method:'post',
                parameters:'json='+Object.toJSON(oParam),
                onComplete: function(oAjax) {
                    js_removeObj("msgBox");
                    var oRetorno = JSON.parse(oAjax.responseText);
                    alert(oRetorno.message.urlDecode());
                    if (oRetorno.status == 1) {
                        js_carregaReduzidos();
                    }
                }
            }
        );
    }

    /**
     * Função que salva os reduzidos de uma conta.
     */

    $("btnIncluirReduzido").observe("click", function() {

        var iCodigoPlanoConta  = <?php echo $oGet->iCodigoConta;?>;
        var iCodigoInstituicao = $("iCodigoInstituicao").value;
        var iCodigoRecurso     = $("iCodigoRecurso").value;
        if (iCodigoInstituicao == "") {
            alert("Informe a instituição.");
            return false;
        }
        if (iCodigoRecurso == "") {
            alert("Informe o recurso.");
            return false;
        }

        var inputContaBancaria = {
          codigo : $('iContaBancaria'),
          descricao : $('sDescricaoContaBancaria')
        };

        var tipoConta = parent.iframe_conta.document.form1.iDetalhamentoSistema.value;
        if (Number(tipoConta) === 6 && inputContaBancaria.codigo.value === '') {

            var mensagem = "O campo Detalhamento do Sistema está selecionado como 6 - FINANCEIRO - BANCO, por este motivo é ";
            mensagem += "necessário informar uma conta bancária para esta conta.";
            alert(mensagem);
            return false;
        }

        js_divCarregando("Cadastrando reduzido, aguarde...", "msgBox");

        var oParam                = {};
        oParam.exec               = "salvarReduzido";
        oParam.iCodigoPlanoConta  = iCodigoPlanoConta;
        oParam.iCodigoInstituicao = iCodigoInstituicao;
        oParam.iCodigoRecurso     = iCodigoRecurso;
        oParam.iCodigoContaBancaria = Number(tipoConta) === 6 ? inputContaBancaria.codigo.value : null;

        if ($("iCodigoReduzido").value != "") {
            oParam.iCodigoReduzido = $("iCodigoReduzido").value;
        }

        var oAjax = new Ajax.Request("con4_conplanoPCASP.RPC.php",
            {method:'post',
                parameters:'json='+Object.toJSON(oParam),
                onComplete: js_retornoSalvarReduzidos
            }
        );
    });


    /**
     * Retorno do incluir de um novo reduzido
     */
    function js_retornoSalvarReduzidos(oAjax) {

        js_removeObj("msgBox");
        $('btnIncluirReduzido').value = "Incluir";
        var oRetorno = JSON.parse(oAjax.responseText);
        alert(oRetorno.message.urlDecode());
        $('iCodigoReduzido').value       = '';
        $('iCodigoInstituicao').value    = '';
        $('sDescricaoInstituicao').value = '';
        $('iCodigoRecurso').value = '';
        $('descricaoRecurso').value = '';
        $('gestao').value = '';
        $('o15_recurso').value = '';
        $('o15_complemento').value = '';
        $('o200_descricao').value = '';

        $('iContaBancaria').value = '';
        $('sDescricaoContaBancaria').value = '';
        js_carregaReduzidos();

        var sLinkInstituicao = "<a class=\"dbancora\" onclick=\"js_pesquisaInstituicao(true)\" style=\"text-decoration:underline;\" href=\"#\"><b>Instituição:</b></a>";

        $("iCodigoInstituicao").readOnly              = false;
        $("iCodigoInstituicao").style.backgroundColor = "#FFF";
        $("iCodigoInstituicao").style.color           = "#000";
        $("tdInstituicao").innerHTML                  = sLinkInstituicao;

    }

    /**
     * Funções de pesquisa das instituições cadastradas
     */
    function js_pesquisaInstituicao(lMostraWindow) {

        if (lMostraWindow) {
            var sUrl = 'func_instit.php?funcao_js=parent.js_preencheInstituicao|codigo|nomeinst';
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_reduzido','db_iframe_db_instit',sUrl,'Pesquisa',true,'0');
        } else {
            if($("iCodigoInstituicao").value != ''){
                var sUrl = 'func_instit.php?pesquisa_chave='+$("iCodigoInstituicao").value+'&funcao_js=parent.js_completaInstituicao';
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_reduzido','db_iframe_db_instit',sUrl,'Pesquisa',false);
            } else {
                $("sDescricaoInstituicao").value = '';
            }
        }
    }
    function js_preencheInstituicao(iCodigoInstit, sNomeInstit) {
        $('iCodigoInstituicao').value    = iCodigoInstit;
        $('sDescricaoInstituicao').value = sNomeInstit;
        db_iframe_db_instit.hide();
    }
    function js_completaInstituicao(sNomeInstit, lErro) {
        if (!lErro) {
            $('sDescricaoInstituicao').value = sNomeInstit;
        } else {
            $('iCodigoInstituicao').value    = '';
            $('sDescricaoInstituicao').value = sNomeInstit;
        }
    }

    const buscaRecursoPorId = (codigo) => {
        let sUrl = `func_novosRecursos.php?funcao_js=parent.pesquisaRecurso&pesquisa_chave=${codigo}`;
        js_OpenJanelaIframe('', 'db_iframe_orctiporec', sUrl, 'Pesquisa Fonte de Recurso', false);
    };

    const lookUpRecurso = new DBLookUp($('ancoraFonteRecurso'), $('gestao'), $('descricaoRecurso'), {
        'sArquivo': 'func_novosRecursos.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'aCamposAdicionais': ['o15_codigo', 'o15_complemento', 'o200_descricao', 'o15_recurso']
    });

    $('gestao').classList.remove('field-size2');
    $('descricaoRecurso').classList.remove('field-size8');
    lookUpRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2], retorno[3], retorno[4], retorno[5]);
    });

    const preencheForm = (recurso, descricao, id, codComplemento, descrComplemento, subrecurso) => {
        $('iCodigoRecurso').value = id;
        $('gestao').value = recurso;
        $('descricaoRecurso').value = descricao;
        $('o15_recurso').value = subrecurso;
        $('o15_complemento').value = codComplemento;
        $('o200_descricao').value = descrComplemento;
    };

    function pesquisaRecurso(erro, recurso, descricao, id, codComplemento, descrComplemento, subrecurso) {
        db_iframe_orctiporec.hide();
        if (erro) {
            $('iCodigoRecurso').value = '';
            $('gestao').value = '';
            $('descricaoRecurso').value = descricao;
            $('o15_recurso').value = '';
            $('o15_complemento').value = '';
            $('o200_descricao').value = '';
            return;
        }
        preencheForm(recurso, descricao, id, codComplemento, descrComplemento, subrecurso);
    }

    js_carregaReduzidos();
    $("iCodigoConta").value = <?php echo @$oGet->iCodigoConta;?>;


    /**
     *  Abre uma WINDOW com para preencher uma conta bancária ou cadastrar uma nova caso não exista
     */
    function js_abreContaBancaria() {

        var iWidth           = 650;
        var iHeight          = 400;
        oWindowContaBancaria = new windowAux('wndContaBAncaria', 'Infomar conta bancária', iWidth, iHeight);
        var sContent   = "<div id='msgContaBancaria' style='text-align:center;'>";
        sContent  += "  <div id='divContaBancaria'>";
        sContent  += "  </div>";
        sContent  += "  <input type='button' id='btnSalvarContaBancaria' name='btnSalvarContaBancaria' value='Salvar'>";
        sContent  += "</div>";
        oWindowContaBancaria.setContent(sContent);
        oWindowContaBancaria.setShutDownFunction(function (){
            oWindowContaBancaria.destroy();
        });

        var sMsgHelp    = 'Informe os dados abaixo, caso a conta não exista, é necessário acessar as rotinas de cadastro.';
        oMessageBoard   = new DBMessageBoard('msgBoard1',
            'Vinculo com Conta Bancária',
            sMsgHelp,
            oWindowContaBancaria.getContentContainer()
        );
        oContaBancaria       = new DBViewContaBancaria($F('iContaBancaria'), 'oContaBancaria',false);
        oContaBancaria.setContaPlano(true);
        // filtra o tipo de conta 1 conta corrente contabancaria.db83_tipoconta
        oContaBancaria.filtraTipoConta = '<?php echo $iTipoConta ?>';
        oContaBancaria.show($('divContaBancaria'));
        if ($F('iContaBancaria') != "") {

            oContaBancaria.getDados($F('iContaBancaria'));
            $('sDescricaoContaBancaria').value = oContaBancaria.getDadosConta();
        }

        oWindowContaBancaria.show();
        $('btnSalvarContaBancaria').observe("click", function () {

            $('iContaBancaria').value          = $('inputSequencialConta').value;
            $('sDescricaoContaBancaria').value = oContaBancaria.getDadosConta();
            oWindowContaBancaria.destroy();
        });
    }


    function verificarSistemaDeContas() {

        $('trdivContaBancaria').style.display = 'none';
        if (Number(parent.iframe_conta.document.form1.iDetalhamentoSistema.value) === 6) {
            $('trdivContaBancaria').style.display = '';
        }
    }
    verificarSistemaDeContas();

</script>
