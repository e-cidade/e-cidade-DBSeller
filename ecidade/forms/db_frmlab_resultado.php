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

//MODULO: Laboratório
$cllab_resultado->rotulo->label();
$clrotulo = new rotulocampo;
$lab_parametros = new cl_lab_parametros();

$postgresObjectParametros = db_query($lab_parametros->sql_query_file('', 'la49_habilitarabsurdo'));
$rsParametros = pg_fetch_all($postgresObjectParametros);

if (empty($rsParametros)) {
    $habilitarAbsurdo = true;
} else {
    $habilitarAbsurdo = $rsParametros[0]['la49_habilitarabsurdo'] === 't' ? true : false;
}

$clrotulo->label("la21_i_requisicao");
$clrotulo->label("la22_i_codigo");
$clrotulo->label("z01_v_nome");
?>

<div class="container">
    <fieldset>
        <legend>Digitação de Resultados</legend>
        <form name="form1" method="post" action="">

            <input type="hidden" id="cgsId">

            <?php
            db_input('la52_i_codigo', 10, $Ila52_i_codigo, true, 'hidden', $db_opcao);
            ?>
            <fieldset class='separator'>
                <legend>Exames</legend>
                <table>
                    <tr>
                        <td id="viewNumeroControleInterno" colspan="2"></td>
                    </tr>
                    <tr>
                        <td title="<?= $Tla22_i_codigo ?>">
                            <label for="la22_i_codigo">
                                <?php
                                db_ancora($Lla21_i_requisicao, "js_pesquisaRequisicao(true);", $db_opcao);
                                ?>
                            </label>
                        </td>
                        <td>
                            <?php
                            db_input(
                                'la22_i_codigo',
                                10,
                                $Ila22_i_codigo,
                                true,
                                'text',
                                $db_opcao,
                                "tabindex='999' onchange='js_pesquisaRequisicao(false);'"
                            );
                            db_input('z01_v_nome', 50, $Iz01_v_nome, true, 'text', 3);
                            ?>
                        </td>
                        <td style="padding-right: 50px;">
                            <input disabled style="pointer-events: none" name="importarResultados" id="importarResultados" type="button"
                                   value="Importar Resultados">
                            <input id='parametroIntegracao' value="<?= $parametroIntegracao ?>" type='hidden'/>
                        </td>
                        <td id="linhaIdade">
                        </td>
                    </tr>
                    <tr>
                       <td>
                         <label for="codigoBarras"><b>Código de Barras:</b></label>
                       </td>
                       <td>
                         <input id="codigoBarras" 
                             type="text" 
                             value="" 
                             onchange="js_processaCodigoBarras()"/>
                       </td>
                    </tr>                    
                </table>
            </fieldset>
        </form>
        <div>
            <fieldset id="fildset-exames"
                      style="width:350px; height:581px; float:left; display:block; text-align: left; overflow:hidden;">
                <legend>Exames da requisição</legend>
                <div rel="ignore-css" id="ctnGridExames"
                     style='overflow:hidden;overflow-y:none;max-width:350px;height:100%'></div>
            </fieldset>
            <div style="width:5px; float:left;"> &nbsp;</div>
            <div id="ctnGridAtributos" style="width:850px; float:left;"></div>
            <div>
    </fieldset>
</div>
<script>
    var oGet = js_urlToObject();
    var oTreeView = new DBTreeView('Exames');
    oTreeView.allowFind(true);
    oTreeView.setFindOptions('matchedonly');
    oTreeView.show($('ctnGridExames'));
    oNoRaiz = oTreeView.addNode("0", "Setor");

    var oLancamento = new LancamentoExameLaboratorio('oLancamento', true);
    var flagHabilitarAbsurdo = <?php echo json_encode($habilitarAbsurdo) ?>;
    oLancamento.mostraCampoObservacao(true);
    oLancamento.setHabilitarAbsurdo(flagHabilitarAbsurdo);
    oLancamento.show($('ctnGridAtributos'));

    var viewNumeroControleInterno = new ViewNumeroControleInterno('viewNumeroControleInterno', true);
    viewNumeroControleInterno.setRequisicaoElemento($('la22_i_codigo'));
    viewNumeroControleInterno.show($('viewNumeroControleInterno'));

    window.onload = function () {
        departamentoPossuiLabQueInterfaceia();
        codigoRequisicao = document.getElementById('la52_i_codigo');
        codigoRequisicao.removeAttribute('tabindex');
        codigoRequisicao.setAttribute('tabindex', '998');
        descricaoRequisicao = document.getElementById('z01_v_nome');
        descricaoRequisicao.removeAttribute('tabindex');
        descricaoRequisicao.setAttribute('tabindex', '999');
    };

    if (viewNumeroControleInterno.getParametroAtivo() === true) {
        $('la22_i_codigo').setAttribute('style', 'margin-left:76px');
    }

    /**
     * Executado ao salvar com sucesso os atributos do exame.
     * Marca o exame selecionado na treeView e move para o próximo exame do setor
     */
    oLancamento.setCallbackSalvar(function (oRetorno) {
        if (oRetorno.status == 1) {
            var oNo = oUltimoExame.element.parentNode.nextSibling;
            oUltimoExame.element.lastChild.style.color = 'green';
            var labelExame = document.createElement('span');
            labelExame.innerHTML = '<i class="fas fa-check"></i>';
            labelExame.style.color = 'green';
            labelExame.style.marginLeft = '15px';
            labelExame.setAttribute('class', 'w3-animate-opacity');
            oUltimoExame.element.appendChild(labelExame);

            setTimeout(function () {
                labelExame.parentNode.removeChild(labelExame);
            }, 2000);

            var proximoSetor = oUltimoExame.element.parentNode.parentNode.parentNode.nextSibling;

            oLancamento.clear();
            if (oNo != null) {
                setTimeout(function () {
                    oNo.firstChild.childNodes[2].click();
                }, 2000);
            } else if (proximoSetor != null) {
                setTimeout(function () {
                    proximoSetor.firstChild.childNodes[3].firstChild.lastChild.click();
                }, 2000);
            } else {
                $('la22_i_codigo').focus();
            }
        }
    });

    oLancamento.setCallbackConferir(function (oRetorno) {
        if (oRetorno.iStatus == 1) {
            var oNo = oUltimoExame.element.parentNode.nextSibling;

            var node = oUltimoExame.element.lastChild;
            var nomeExame = node.textContent;

            if (node.parentNode) {
                node.parentNode.removeChild(node);
            }

            var labelExame = document.createElement('span');
            labelExame.textContent = nomeExame;
            labelExame.style.color = '#72796D';
            labelExame.style.textDecoration = 'line-through';
            oUltimoExame.element.appendChild(labelExame);

            var labelConferido = document.createElement('span');
            labelConferido.textContent = 'Conferido';
            labelConferido.style.color = '#72796D';
            labelConferido.style.marginLeft = '5px';
            oUltimoExame.element.appendChild(labelConferido);

            var proximoSetor = oUltimoExame.element.parentNode.parentNode.parentNode.nextSibling;
            oLancamento.clear();
            if (oNo != null) {
                setTimeout(function () {
                    oNo.firstChild.childNodes[2].click();
                }, 2000);
            } else if (proximoSetor != null) {
                setTimeout(function () {
                    proximoSetor.firstChild.childNodes[3].firstChild.lastChild.click();
                }, 2000);
            } else {
                $('la22_i_codigo').focus();
            }
        }
    });

    oLancamento.setCallBackNovaColeta(function (oRetorno) {
        if (oRetorno.status == 1) {
            console.log(oUltimoExame.element.parentNode.nextSibling);
            var oNo = oUltimoExame.element.parentNode.nextSibling;

            var node = oUltimoExame.element.lastChild;
            var nomeExame = node.textContent;

            if (node.parentNode) {
                node.parentNode.removeChild(node);
            }

            var labelExame = document.createElement('span');
            labelExame.textContent = nomeExame;
            labelExame.style.color = '#72796D';
            labelExame.style.textDecoration = 'line-through';
            oUltimoExame.element.appendChild(labelExame);

            var labelConferido = document.createElement('span');
            labelConferido.textContent = 'Nova Coleta';
            labelConferido.style.color = '#72796D';
            labelConferido.style.marginLeft = '5px';
            oUltimoExame.element.appendChild(labelConferido);

            var proximoSetor = oUltimoExame.element.parentNode.parentNode.parentNode.nextSibling;
            oLancamento.clear();
            if (oNo != null) {
                setTimeout(function () {
                    oNo.firstChild.childNodes[2].click();
                }, 2000);
            } else if (proximoSetor != null) {
                setTimeout(function () {
                    proximoSetor.firstChild.childNodes[3].firstChild.lastChild.click();
                }, 2000);
            } else {
                $('la22_i_codigo').focus();
            }
        }
    });

    function js_pesquisaRequisicao(lMostra) {
        resetaTreeView();

        var sUrl = "func_lab_requisicao.php?iLaboratorioLogado=<?=$iLaboratorioLogado?>&permissaoPorResponsavelLab=1";

        if (lMostra) {

            sUrl += '&funcao_js=parent.js_mostraRequisicao|la22_i_codigo|z01_v_nome|z01_i_cgsund';
            js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisição ', true);
        } else if (document.form1.la22_i_codigo.value != '') {

            sUrl += '&pesquisa_chave=' + $F('la22_i_codigo');
            sUrl += '&funcao_js=parent.js_mostraRequisicao';
            js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisição ', false);
        } else {

            $('la22_i_codigo').value = '';
            $('z01_v_nome').value = '';
            resetaTreeView();
            apagaIdadeCompleta();
        }
    }


    function resetaTreeView() {

        oNoRaiz.remove();

        iTabIndex = 1000;
        oNoRaiz = oTreeView.addNode("0", "Setor",
            null,
            null,
            null,
            null,
            null,
            null,
            iTabIndex);

        oLancamento.clear();
    }

    function js_mostraRequisicao(chave, erro) {
        if (typeof arguments[1] == 'boolean') {
            $('z01_v_nome').value = arguments[0];
            if (arguments[1]) {

                $('la22_i_codigo').value = '';
                $('la22_i_codigo').focus();
                resetaTreeView();
                return false;
            }
            document.getElementById('cgsId').value = arguments[2];
            existeCgsUnd();

            js_BuscaExames();

            return true;
        }

        $('la22_i_codigo').value = arguments[0];
        $('la22_i_codigo').focus();
        $('z01_v_nome').value = arguments[1];

        if (viewNumeroControleInterno.getParametroAtivo() === true && $('la22_i_codigo').value != '') {
            viewNumeroControleInterno.getNumeroControleInternoPorRequisicao($('la22_i_codigo').value);
        }

        db_iframe_lab_requisicao.hide();

        document.getElementById('cgsId').value = arguments[2];
        existeCgsUnd();

        js_BuscaExames();

        return true;
    }


    function js_BuscaExames() {

        var oParamentros = {exec: 'buscarExames', iRequisicao: $F('la22_i_codigo')};

        var oAjaxRequest = new AjaxRequest('lab4_requisicao.RPC.php', oParamentros, retornoExames);
        oAjaxRequest.setMessage('Buscando Exames...');
        oAjaxRequest.asynchronous(false);
        oAjaxRequest.execute();
    }

    /**
     * Monta a arvore de exames com os exames da requição selecionada
     */
    function retornoExames(oRetorno, lErro) {
        resetaTreeView();
        if (oRetorno.erro == true) {
            alert(oRetorno.sMessage.urlDecode());
            document.getElementById('la22_i_codigo').value = '';
            document.getElementById('z01_v_nome').value = '';
            document.getElementById('la22_i_codigo').focus();
            return;
        }

        if (oRetorno.laboratorios != "") {
            alert("Há exames solicitados nessa requisição pertencentes a outros laboratórios: \n\n" + oRetorno.laboratorios.urlDecode() +
                "\n\n Para digitar o resultado desta requisição será necessário atualizar a permissão cadastrando o profissional no respectivo Laboratório.");
        }

        if (lErro) {

            alert(oRetorno.sMessage.urlDecode());
            return;
        }
        /**
         * Ao pesquisar nova requisição, zera a treeView (remove os nós abaixo)
         */
        iTabIndex = 1001;
        oRetorno.aExamesRequisicao.each(function (oSetor, iSetor) {

            var sIdSetor = oSetor.iCodigo;
            var oNodeSetor = oTreeView.addNode(sIdSetor,
                oSetor.sNome.urlDecode(),
                '0',
                '',
                '',
                false,
                null,
                {'lProcessa': false},
                iTabIndex
            );
            iTabIndex++;

            oSetor.aExames.each(function (oExame, iIndexExame) {
                var oNode = oTreeView.addNode(oExame.iCodigo,
                    oExame.sNome.urlDecode(),
                    sIdSetor,
                    '',
                    '',
                    true,
                    function (oNo, Evento) {
                        clicouExame(oNo, Evento, true, oExame.permitidoconferencia);
                    },
                    {
                        'lProcessa': true, 'iCodigo': oExame.iCodigo,
                        'sNome': oExame.sNome, lPinta: oExame.lDigitado
                    },
                    iTabIndex
                );


                oNode.element.childNodes[2].addEventListener('focus', function (event) {
                    setTimeout(function () {
                        clicouExame(oNode, event, true, oExame.permitidoconferencia);
                    }, 1000);
                });

                iTabIndex++;

                if (oNode.lPinta) {
                    oNode.element.lastChild.style.color = 'green';
                }
                oNodeSetor.expand(); // abre os nós filhos do setor
            });

            oNoRaiz.element.childNodes[3].childNodes[0].childNodes[3].childNodes[0].childNodes[2].click();

            oNoRaiz.expand(null, true);
        });

    }

    /**
     * Sempre que clica sobe um exame, marca o exame como selecionado e envia o código do exame (item da requisição) para
     * o componente LancamentoExameLaboratorio
     */
    var oUltimoExame = '';

    function clicouExame(oNo, Evento, bSelecionou, permitidoConferencia) {
        if (oUltimoExame != '') {
            oUltimoExame.select(false);
        }
        oUltimoExame = oNo;
        oNo.select(true);
        oLancamento.setRequisicao(oNo.iCodigo, bSelecionou);
        oLancamento.setPermitidoConferir(permitidoConferencia);
    }

    if (oGet.la08_i_codigo) {

        js_BuscaExames();

        for (var i in oTreeView.aNodes) {
            if (oTreeView.aNodes[i].value == oGet.la47_i_requiitem) {

                oUltimoExame = oTreeView.aNodes[i];
                oTreeView.aNodes[i].select(true);
                oLancamento.setRequisicao(oGet.la47_i_requiitem);
            }
        }
    }

    habilitaBtn()

    function habilitaBtn() {

        document.getElementById('importarResultados').setStyle({
            'display': 'none'
        })

        if ($('parametroIntegracao').value === '1') {
            document.getElementById('importarResultados').setStyle({
                'display': ''
            })
        }
    }

    $('importarResultados').observe('click', function () {
        if (!confirm('Deseja Realizar a Importação dos Arquivos de Resultados?')) {
            return false;
        }

        importarResultadosLacem();
    });

    function existeCgsUnd() {
        let cgsUnd = document.getElementById('cgsId').value;

        if (cgsUnd !== '') {
            let parametros = {
                'sExecucao': 'buscarDadosCgs',
                'iCgs': cgsUnd
            };

            let dadosRequisicao = {};
            dadosRequisicao.method = 'post';
            dadosRequisicao.parameters = 'json=' + Object.toJSON(parametros);
            dadosRequisicao.onComplete = function (resposta) {

                let retorno = JSON.parse(resposta.responseText);

                retornaIdadePaciente(retorno);
            };

            const rpc = 'sau4_cgs.RPC.php';
            new Ajax.Request(rpc, dadosRequisicao);

        }


    }

    function retornaIdadePaciente(retorno) {
        let idadeCompleta = retorno.oCgs.sIdadeCompleta.urlDecode();

        $('linhaIdade').innerHTML = `<p id="idadeCompleta">Idade: <span style="color: red;">${idadeCompleta}</span></p>`;


    }

    function apagaIdadeCompleta() {
        let tagIdadeCompleta = document.querySelector("#idadeCompleta");

        if (tagIdadeCompleta) {
            tagIdadeCompleta.remove();
        }
    }

    function importarResultadosLepac(pastaLuckmanVazia, inconsistenciaLuckman, erroImportacaoLuckman) {
        new AjaxRequest('lab4_digitacaoexame.RPC.php',
            {
                exec: 'importarResultadosLepac'
            },
            function (retorno, lErro) {

                if (retorno.temInconsistencias === true) {

                    if (confirm(retorno.message.urlDecode())) {

                        var emissaoRelatorio = new EmissaoRelatorio(
                            'lab2_inconsistenciasimportacaoresultado002.php',
                            {
                                buscarRegistros: false,
                                arquivo: retorno.arquivo,
                                requisicao: retorno.requisicao
                            }
                        );

                        emissaoRelatorio.open();
                    }
                }

                if (retorno.erro) {
                    alert(retorno.message.urlDecode());
                }

                if (retorno.pastaInfinityVazia && pastaLuckmanVazia) {
                    alert('Nenhum arquivo a ser importado.');
                    return;
                }

                if (!retorno.temInconsistencias && !retorno.erro && !inconsistenciaLuckman && !erroImportacaoLuckman) {
                    alert('Resultados importados com sucesso');
                }
            }
        ).execute()
    }

    function departamentoPossuiLabQueInterfaceia() {
        new AjaxRequest('lab4_digitacaoexame.RPC.php',
            {
                exec: 'verificaInterfaceDepartamentoLaboratorio'
            },
            function (retorno, lErro) {
                if (retorno.departamentoPossuiInterface.erro) {
                    alert(retorno.departamentoPossuiInterface.mensagem.urlDecode());
                    return;
                }

                if (retorno.departamentoPossuiInterface.interfaceado) {
                    $('importarResultados').disabled = false;
                    $('importarResultados').style.cssText = 'pointer-events: all;';
                }

            }
        ).execute()
    }

    function importarResultadosLacem() {
        new AjaxRequest(
            'lab4_digitacaoexame.RPC.php',
            {
                exec: 'importarResultados'
            },
            function (retorno, lErro) {

                if (retorno.temInconsistencias === true) {

                    if (confirm(retorno.message.urlDecode())) {

                        var emissaoRelatorio = new EmissaoRelatorio(
                            'lab2_inconsistenciasimportacaoresultado002.php',
                            {
                                buscarRegistros: false,
                                arquivo: retorno.arquivo,
                                requisicao: retorno.requisicao
                            }
                        );

                        emissaoRelatorio.open();
                    }
                }

                /**
                 * Caso voltemos a importar apenas resultados do lacem devemos reorganizar a forma com que as
                 * mensagens aparecerão, de sucesso, pasta vazia e demais erros e o RPC lab4_digitacaoexame.RPC.php no respectivo exec também
                 */

                if (retorno.erro) {
                    alert(retorno.message.urlDecode());
                }

                importarResultadosLepac(retorno.pastaLuckmanVazia, retorno.temInconsistencias, retorno.erro);
            }
        ).execute()
    }

    function js_processaCodigoBarras(event){

        let codigoBarras = $('codigoBarras').value; 

        if(codigoBarras.length == 0) {
            $('la22_i_codigo').value = '';
            $('z01_v_nome').value = '';
            return false;
        } 

        if([12,14].includes(codigoBarras.length)){            
            let codigoRequisicao = parseInt(codigoBarras.substring(3, 12), 10);
            $('la22_i_codigo').value = codigoRequisicao;
            $('la22_i_codigo').dispatchEvent(new Event('change',{}));
            return false;
        }

        alert('Código de barras inválido!');
        $('codigoBarras').value = '';      
    }   
</script>
