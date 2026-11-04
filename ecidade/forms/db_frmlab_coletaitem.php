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

use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Enum\ModeloImpressao;
use ECidade\Saude\Laboratorio\Repository\Parametros;

//MODULO: Laboratório
$cllab_coletaitem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nome");
$clrotulo->label("la21_i_codigo");
$clrotulo->label("la22_i_codigo");
$clrotulo->label("z01_v_nome");
$clrotulo->label("la24_i_laboratorio");
$clrotulo->label("la09_i_exame");
$clrotulo->label("la21_d_data");
$clrotulo->label("la21_c_hora");
$clrotulo->label("la21_d_entrega");

function somardata($data, $dias = 0, $meses = 0, $ano = 0)
{
    $data = explode("/", $data);
    $novadata = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));

    return $novadata;
}

$modelosTipo = ModeloImpressao::getTiposDescricoes();
$modelosCodigo = ModeloImpressao::getTiposCodigos();

try {
    $parametrosRepository = Parametros::getInstancia();
    $parametros = $parametrosRepository->buscar();
} catch (Exception $erro) {
    db_msgbox($erro->getMessage());
}
?>
<div class="container">
    <form name="form1" method="post" action="">
        <fieldset style='width: 100%;'>
            <legend><b>Exames</b></legend>

            <input type="hidden" id="cgsId">

            <table border="0" style='width: 95%;'>
                <tr>
                    <td id="viewNumeroControleInterno" colspan="2"></td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tla22_i_codigo ?>">
                        <?php
                        db_ancora(
                            "<stronger><b>Requisição</b></stronger>",
                            "js_pesquisala22_i_codigo(true);",
                            $db_opcao
                        ); ?>
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
                            "onchange='js_pesquisala22_i_codigo(false);'"
                        );
                        db_input('z01_v_nome', 50, $Iz01_v_nome, true, 'text', 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                      <label for="codigoBarras"><b>Código de Barras:</b></label>
                    </td>
                    <td>
                      <input id="codigoBarras" 
                          name = "codigoBarras" 
                          type="text" 
                          value=""                                                   
                          onchange="js_processaCodigoBarras()"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="GridExames" id="GridExames"></div>
                        <select name="exames" style="display:none"></select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <input value="Falta material" name="falta" id="falta" type="button"
                               onclick="return js_salvar(0)" disabled>
                    </td>
                </tr>
            </table>
        </fieldset>
        <table style="width: 100%;" border="0">
            <tr>
                <td valign="top">
                    <fieldset style='width: 90%;'>
                        <legend><b>Entrega</b></legend>
                        <table style='width: 95%;'>
                            <tr>
                                <td nowrap title="<?= @$Tla32_d_entrega ?>">
                                    <?= @$Lla32_d_entrega ?>
                                </td>
                                <td nowrap>
                                    <?php
                                    db_inputdata(
                                        'la32_d_entrega',
                                        @$la32_d_entrega_dia,
                                        @$la32_d_entrega_mes,
                                        @$la32_d_entrega_ano,
                                        true,
                                        'text',
                                        $db_opcao
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tla32_c_horaentrega ?>">
                                    <?= @$Lla32_c_horaentrega ?>
                                </td>
                                <td>
                                    <?php
                                    db_input('la32_c_horaentrega', 5, $Ila32_c_horaentrega, true, 'text', $db_opcao); ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tla32_i_avisapaciente ?>" colspan="2">
                                    <input type="checkbox" name="avisa" id="avisa">
                                    <?= substr(@$Lla32_i_avisapaciente, 8, -10) ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td valign="top">
                    <fieldset style='width: 90%;'>
                        <legend><b>Opções</b></legend>
                        <table style='width: 95%;'>
                            <tr>
                                <td>
                                    <select name="modelos" style="width:450px;">
                                        <?php
                                        foreach ($modelosTipo as $tipo => $descricao) {
                                            $codigoTipo = (int)$modelosCodigo[$tipo];
                                            $codigoParametros = (int)$parametros->getModeloColetaAmostra();
                                            $selected = $codigoTipo === $codigoParametros ? "selected='selected'" : '';
                                            ?>
                                            <option value="<?= $tipo ?>" <?= $selected ?>><?= $descricao ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="button" name="imprimir" id="imprimir" value="Imprimir etiqueta"
                                           onclick="js_emite();"
                                           disabled>
                                    <input type="button" name="incluir" id="incluir" value="Salvar"
                                           onclick="return js_salvar(1)"
                                           disabled>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
    sRPC = 'lab4_agendar.RPC.php';
    objGridExames = new DBGrid('GridExames');
    var oGet = js_urlToObject();

    var viewNumeroControleInterno = new ViewNumeroControleInterno('viewNumeroControleInterno', true)
    viewNumeroControleInterno.setRequisicaoElemento($('la22_i_codigo'))
    viewNumeroControleInterno.show($('viewNumeroControleInterno'))

    if (viewNumeroControleInterno.getParametroAtivo() === true) {
        $('la22_i_codigo').setAttribute('style', 'margin-left: 51px');
    }

    objGridExames.setCallbackSingle(function () {

        let desabilitaSalvar = false;
        let desabilitaFaltaMaterial = false;
        const aRequisicoesSelecionadas = objGridExames.getSelection('array');

        aRequisicoesSelecionadas.each(function (aRequisito) {
            var situacaoRequisicao = aRequisito[6];
            if (situacaoRequisicao == "f - falta material") {
                desabilitaFaltaMaterial = true;
            }
            if (situacaoRequisicao != "20 - Autorizado" && situacaoRequisicao != "f - falta material") {
                desabilitaSalvar = true;
                desabilitaFaltaMaterial = true;
            }
        });

        if (desabilitaSalvar) {
            $("incluir").setAttribute('disabled', 'disabled');
        } else {
            $("incluir").removeAttribute('disabled');
        }
        if (desabilitaFaltaMaterial) {
            $("falta").setAttribute('disabled', 'disabled');
        } else {
            $("falta").removeAttribute('disabled');
        }
    });

    if (!empty(oGet.la22_i_codigo)) {

        js_OpenJanelaIframe(
            '',
            'db_iframe_lab_requisicao',
            'func_lab_requisicao.php?iLaboratorioLogado=<?=$iLaboratorioLogado?>'
            + '&pesquisa_chave=' + oGet.la22_i_codigo
            + '&funcao_js=parent.js_mostralab_requisicao',
            + '&permissaoPorResponsavelLab=1',
            'Pesquisa',
            false
        );
    }

    F = document.form1;
    js_init();
    <?php
    if (isset($la22_i_codigo)) {
        echo "js_carregaexames($la22_i_codigo);";
    }
    ?>
    //validação
    async function js_salvar(entrega) {

        var aLista = [];
        var aSituacoes = [];
        let itensRequisicaoInfinity = [];

        $aRequisicoesSelecionadas = objGridExames.getSelection('array');
        var situacaoInvalida = false;
        let possuiFaltaMaterial = false;
        $aRequisicoesSelecionadas.each(function (aRequisito) {
            var situacaoRequisicao = aRequisito[6];
            if (situacaoRequisicao == 'f - falta material' && entrega == 0) {
                possuiFaltaMaterial = true;
            }
            if (situacaoRequisicao != "20 - Autorizado" && situacaoRequisicao != "f - falta material") {
                situacaoInvalida = true;
            }
            aLista.push(aRequisito[0]);
            aSituacoes.push(situacaoRequisicao);
        });

        if (aLista.length == 0) {
            alert('Marque um exame!');
            return false;
        }
        if (possuiFaltaMaterial) {
            alert('Apenas exames com situação "Autorizado" podem realizar este procedimento.');
            return false;
        }
        if (situacaoInvalida) {
            alert('Apenas exames com a situação "Autorizado" ou "falta de material" podem realizar este procedimento.');
            return false;
        }

        if (confirm("Tem certeza que deseja registrar a coleta?")) {
            // lab4_coletaexame.RPC.php

            const relacaoSetorRequiitem = await recuperaSetoresPorRequiitem(aLista);
            const requiitemsOrganizadosPorSetor = organizaRequiitemPorSetor(relacaoSetorRequiitem.dadosRequiitem);

            aLista = requiitemsOrganizadosPorSetor[0];
            itensRequisicaoInfinity = requiitemsOrganizadosPorSetor[1];

            var oParam = new Object();
            oParam.exec = 'salvar';
            oParam.iAvisaPaciente = $('avisa').checked == true ? 1 : 2;
            oParam.dtEntrega = $F('la32_d_entrega');
            oParam.sHoraEntrega = $F('la32_c_horaentrega');
            oParam.lFalta = entrega == 1 ? false : true;
            oParam.aItemRequisicao = aLista;
            oParam.aSituacaoRequisicao = aSituacoes;            
            oParam.codigoRequisicao = $F('la22_i_codigo');

            var oDadosRequest = new Object();
            oDadosRequest.methot = 'post';
            oDadosRequest.parameters = 'json=' + Object.toJSON(oParam);
            oDadosRequest.onComplete = js_retornoSalvar;

            if (aLista.length > 0) {
                js_divCarregando("Aguarde, salvando requisições...", "msgBox");
                new Ajax.Request("lab4_coletaexame.RPC.php", oDadosRequest);
            }

            if (itensRequisicaoInfinity.length > 0) {
                coletaDadosInfinity(itensRequisicaoInfinity, aSituacoes, entrega);
            }

        } else {
            return false;
        }
    }

    function js_retornoSalvar(oResponse) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oResponse.responseText);

        alert(oRetorno.sMensagem.urlDecode());
        if (oRetorno.iStatus == 1) {
            js_carregaexames(F.la22_i_codigo.value);
        }
    }

    function js_emite() {

        var aLista = [];

        $aRequisicoesSelecionadas = objGridExames.getSelection('array');
        $aRequisicoesSelecionadas.each(function (aRequisito) {
            aLista.push(aRequisito[0]);
        });

        let url = 'lab4_etiquetacoleta002.php?sLista=' + aLista;

        if (aLista.length > 0) {
            if (document.form1.modelos.value == "M2") {
                url = 'lab4_etiquetacoleta003.php?sLista=' + aLista;
            }

            if (document.form1.modelos.value == "M3") {
                url = 'lab4_etiquetacoleta004.php?sLista=' + aLista + '&requisicao=' + $F('la22_i_codigo');
            }

            window.open(url, janela, 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
        } else {
            alert('Seleciona um Exame');
        }
    }

    //grid exames
    function js_init() {

        objGridExames.nameInstance = 'objGridExames';
        objGridExames.setCheckbox(7);
        objGridExames.setHeader(new Array("Cod.", "Laboratório", "Exame", "Coleta", "Hora", "Situação", "Urgente", "la21_i_codigo"));
        objGridExames.setHeight(80);
        objGridExames.aHeaders[8].lDisplayed = false;
        objGridExames.show($('GridExames'));
    }

    function js_AtualizaGrid() {

        objGridExames.clearAll(true);
        tam = F.exames.length;

        for (x = 0; x < tam; x++) {

            sText = F.exames.options[x].text;
            avet = sText.split('#');

            alinha = new Array();
            alinha[0] = avet[0]; //codigo Setor/Exame
            alinha[1] = avet[1].urlDecode(); //descr  laboratorio
            alinha[2] = avet[2].urlDecode(); //descr  exame
            alinha[3] = avet[3]; //data coleta
            alinha[4] = avet[4]; //hora coleta
            alinha[5] = avet[5]; //data entrega
            scheck = (avet[6] == 1) ? ' checked ' : '';
            alinha[6] = '<input type="checkbox" id="urgente' + x + '" ' + scheck + ' >';
            alinha[7] = avet[7];

            objGridExames.addRow(alinha);
        }
        objGridExames.renderRows();
    }

    function js_mudadata(data) {

        F.la32_d_entrega.value = data;

    }

    function js_carregaexames(requisicao, cgs = null) {
        var oParam = new Object();
        oParam.exec = 'CarregaGridAutorizado';
        oParam.requisicao = requisicao;
        oParam.iLaboratorioLogado = <?=$iLaboratorioLogado?>;
        js_ajax(oParam, 'js_retornocarregaexames');

        if (cgs !== null) {
            document.getElementById('cgsId').value = cgs;
            apagaIdadeCompleta();
            exibeDivComIdadeCompleta(cgs)
        }
    }

    function js_retornocarregaexames(objAjax) {
        oAjax = JSON.parse(objAjax.responseText);
        while (F.exames.length > 0) {
            F.exames.remove(0);
        }
        if (oAjax.status == 1) {
            if (oAjax.alinhasgrid.length > 0) {
                for (x = 0; x < oAjax.alinhasgrid.length; x++) {
                    F.exames.add(new Option(oAjax.alinhasgrid[x], F.exames.length), null);
                }
                js_AtualizaGrid();
                F.falta.disabled = false;
                F.incluir.disabled = false;
                F.imprimir.disabled = false;
            } else {
                objGridExames.clearAll(true);
            }
        }
    }

    //lookup's

    function js_pesquisala22_i_codigo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', 'func_lab_requisicao.php?iLaboratorioLogado=<?=$iLaboratorioLogado?>&permissaoPorResponsavelLab=1&funcao_js=parent.js_mostralab_requisicao1|la22_i_codigo|z01_v_nome|z01_i_cgsund', 'Pesquisa', true);
        } else {
            if (document.form1.la22_i_codigo.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', 'func_lab_requisicao.php?iLaboratorioLogado=<?=$iLaboratorioLogado?>&permissaoPorResponsavel=1&pesquisa_chave=' + document.form1.la22_i_codigo.value + '&funcao_js=parent.js_mostralab_requisicao', 'Pesquisa', false);
            } else {
                $('la22_i_codigo').value = '';
                $('z01_v_nome').value = '';
                objGridExames.clearAll();
                apagaIdadeCompleta();
            }
        }
    }

    function js_mostralab_requisicao(chave, erro, cgs) {
        document.form1.z01_v_nome.value = chave;
        if (erro == true) {
            document.form1.la22_i_codigo.focus();
            document.form1.la22_i_codigo.value = '';
            objGridExames.clearAll();
        } else {
            js_carregaexames(F.la22_i_codigo.value, cgs);
        }

        if (viewNumeroControleInterno.getParametroAtivo() === true && $('la22_i_codigo').value != '') {
            viewNumeroControleInterno.getNumeroControleInternoPorRequisicao($('la22_i_codigo').value);
        }
    }

    function js_mostralab_requisicao1(chave1, chave2, cgs) {
        document.form1.la22_i_codigo.value = chave1;
        document.form1.z01_v_nome.value = chave2;
        db_iframe_lab_requisicao.hide();

        if (viewNumeroControleInterno.getParametroAtivo() === true && $('la22_i_codigo').value != '') {
            viewNumeroControleInterno.getNumeroControleInternoPorRequisicao($('la22_i_codigo').value);
        }

        js_carregaexames(chave1, cgs);
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lab_coletaitem', 'func_lab_coletaitem.php?funcao_js=parent.js_preenchepesquisa|la32_i_codigo', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_lab_coletaitem.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_ajax(objParam, jsRetorno) {
        var objAjax = new Ajax.Request(
            sRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(objParam),
                onComplete: function (objAjax) {
                    var evlJS = jsRetorno + '( objAjax );';
                    eval(evlJS);
                }
            }
        );
    }

    function apagaIdadeCompleta() {
        let tagIdadeCompleta = document.querySelector("#linhaIdadeCompleta");

        if (tagIdadeCompleta) {
            tagIdadeCompleta.remove();
        }
    }

    function exibeDivComIdadeCompleta(cgs){

        let divNumeroControleInterno = document.querySelector("#viewNumeroControleInterno");

        let divIdadeCompleta = document.createElement("div");
        divIdadeCompleta.id = "linhaIdadeCompleta";
        divIdadeCompleta.style = "height:1em;padding-left:50%;";

        divNumeroControleInterno.appendChild(divIdadeCompleta);

        if(viewNumeroControleInterno.getParametroAtivo() === true){
            divNumeroControleInterno.removeChild(divIdadeCompleta);
            let div = divNumeroControleInterno.firstChild;
            div.style = "display:flex;align-items:center;gap:0.5%";
            divIdadeCompleta.style = "padding-left:14%;"
            div.appendChild(divIdadeCompleta);
        }

        pesquisaIdadeCompletaDoPaciente(cgs,"linhaIdadeCompleta");
    }

    /**
     * A partir dos códigos de requiitem iremos retornar a qual setor o exame daquele requiitem pertence.
     * @param codigosRequiitem
     * @returns resposta
     */
    async function recuperaSetoresPorRequiitem(codigosRequiitem) {
        const params = {
            exec: 'pegarSetoresPorRequiitem',
            codigosRequiitem: codigosRequiitem
        };
        const paramsJson = Object.toJSON(params);
        const formData = new FormData();
        formData.append('json', paramsJson);

        const resposta = await HttpClient.post(`lab4_agendar.RPC.php`, {body: formData});

        if (resposta.message.urlDecode()) {
            alert(resposta.message.urlDecode());
            return;
        }
        return resposta;
    }

    /**
     * Organiza os códigos requiitem de acordo com o setor. Índice 1 serão exames para o infinity, índice 0 exames para luckmann
     * @param dadosSetoresExames
     * @returns array
     */
    function organizaRequiitemPorSetor(dadosSetoresExames) {
        const listaSetoresExames = [];
        const setoresInfinity = [22, 23, 24, 27, 28];
        const setoresExamesLuckmann = [];
        const setoresExamesInfinity = [];

        for (setorExame of dadosSetoresExames) {
            if (setoresInfinity.includes(setorExame.setor)) {
                setoresExamesInfinity.push(setorExame.requiitem);
                continue;
            }
            setoresExamesLuckmann.push(setorExame.requiitem);
        }

        listaSetoresExames.push(setoresExamesLuckmann)
        listaSetoresExames.push(setoresExamesInfinity)

        return listaSetoresExames;
    }

    function coletaDadosInfinity(dadosInfinity, situacoes, entrega) {
        let oParam = new Object();
        oParam.exec = 'salvarDadosInfinity';
        oParam.iAvisaPaciente = $('avisa').checked == true ? 1 : 2;
        oParam.dtEntrega = $F('la32_d_entrega');
        oParam.sHoraEntrega = $F('la32_c_horaentrega');
        oParam.lFalta = entrega == 1 ? false : true;
        oParam.aSituacaoRequisicao = situacoes;        
        oParam.codigoRequisicao = $F('la22_i_codigo');
        oParam.itensRequisicaoInfinity = dadosInfinity;

        let oDadosRequest = new Object();
        oDadosRequest.methot = 'post';
        oDadosRequest.parameters = 'json=' + Object.toJSON(oParam);
        oDadosRequest.onComplete = js_retornoSalvar;

        js_divCarregando("Aguarde, salvando requisições...", "msgBox");
        new Ajax.Request("lab4_coletaexame.RPC.php", oDadosRequest);
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
