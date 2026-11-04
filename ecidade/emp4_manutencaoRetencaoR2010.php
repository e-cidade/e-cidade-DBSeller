<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2022  DBSeller Servicos de Informatica
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("classes/empenho.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <meta name="ECIDADE_REQUEST_PATH" content="<?= ECIDADE_REQUEST_PATH ?>">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script src="public/js/app.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name='form1' action=''>
    <input type="hidden" name="retencao_sequencial" id="retencao_sequencial">
    <input type="hidden" name="receitasadicionais_sequencial"  id="receitasadicionais_sequencial">

    <div class="container">
        <!-- Dados da Prestação de serviço -->
        <fieldset>
            <legend>Dados da Prestação de serviço</legend>

            <table>
                <tr>
                    <td>
                        <label for="nome_prestador"><b>Razão Social: </b></label>
                    </td>
                    <td>
                        <input type="text" name="nome_prestador" id="nome_prestador" disabled size="50">
                        <input type="text" name="cnpj_prestador" id="cnpj_prestador" disabled size="20" data-mask="cnpj">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="empenho"><b>Empenho: </b></label>
                    </td>
                    <td>
                        <input type="text" name="empenho" id="empenho" disabled hidden>
                        <input type="text" name="empenho_numero" id="empenho_numero" disabled>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="codigo_nota"><b>Nota de Liquidação: </b></label>
                    </td>
                    <td>
                        <input type="text" name="codigo_nota" id="codigo_nota" disabled>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="indicativo_obra_tipo"><b>Tipo de Serviço: </b></label>
                    </td>
                    <td>
                        <select name="indicativo_obra_tipo" id="indicativo_obra_tipo" onchange="js_changeLabelCno(this)">
                            <option value="" selected>Selecione:</option>
                            <option value="0">0 - Não é obra de construção civil ou não está sujeita a matrícula de obra</option>
                            <option value="1">1 - É obra de construção civil, modalidade empreitada total</option>
                            <option value="2">2 - É obra de construção civil, modalidade empreitada parcial</option>
                        </select>
                        <input type="text" maxlength="12" name="indicativo_obra_cno" id="indicativo_obra_cno" placeholder="CNO">
                    </td>
                </tr>
            </table>
        </fieldset>
        <br>
        <fieldset>
            <legend>Dados da Nota Fiscal</legend>

            <table class="form-container">
                <!-- informacao da nf -->
                <tr>
                    <td>
                        <label for="numero_nota"><b>Número da NF:</b></label>
                    </td>
                    <td>
                        <input type="text" name="numero_nota" id="numero_nota">
                    </td>
                    <td>
                        <label for="serie_nota"><b>Série da NF:</b></label>
                    </td>
                    <td>
                        <input type="text" name="serie_nota" id="serie_nota">
                    </td>
                    <td>
                        <label for="data_emissao"><b>Data da NF:</b></label>
                    </td>
                    <td>
                        <input type="text" name="data_emissao" id="data_emissao" disabled data-mask="date">
                    </td>
                </tr>

                <!-- valores da nf -->
                <tr>
                    <td>
                        <label for="valor_nota_liq"><b>Valor bruto:</b></label>
                    </td>
                    <td>
                        <input type="text" name="valor_nota_liq" id="valor_nota_liq" data-mask="money" disabled>
                    </td>
                    <td>
                        <label for="notas_nao_retidas"><b>Valor notas relacionadas:</b></label>
                    </td>
                    <td>
                        <input type="text" name="notas_nao_retidas" id="notas_nao_retidas" data-mask="money" disabled>
                        <a href="#" onclick="js_notas()">Notas</a>
                    </td>
                    <td>
                        <label for="valor_bruto_final"><b>Valor bruto final:</b></label>
                    </td>
                    <td>
                        <input type="text" name="valor_bruto_final" id="valor_bruto_final" data-mask="money" disabled>
                    </td>
                </tr>

                <!-- Dados rentencao-->
                <tr>
                <td>
                    <label for="valor_base_retido"><b>Valor base:</b></label>
                    </td>
                    <td>
                        <input type="text" name="valor_base_retido" id="valor_base_retido" data-mask="money" disabled>
                    </td>

                    <td>
                        <label for="aliquota"><b>Alíquota:</b></label>
                    </td>
                    <td>
                        <input type="text" name="aliquota" id="aliquota" disabled>
                    </td>
                    <td>
                        <label for="valor_retencao"><b>Valor Retido:</b></label>
                    </td>
                    <td>
                        <input type="text" name="valor_retencao" id="valor_retencao" data-mask="money" disabled>
                    </td>
                </tr>

                <!-- dados do servico -->
                <tr>
                    <td>
                        <label for="indicativo_valor_base">Referência para cálculo: </label>
                    </td>
                    <td>
                        <select id="indicativo_valor_base">
                            <option value="true">Valor base</option>
                            <option value="false">Valor bruto final</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="referencia_tipo_servico"><b>Tipo de Serviço: </b></label>
                    </td>
                    <td>
                        <select name="referencia_tipo_servico" id="referencia_tipo_servico">
                            <option value="" selected>Selecione:</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <br>
        <fieldset>
            <legend>Dados Adicionais</legend>

            <table class="form-container">
                <tr>
                    <td>
                        <fieldset>
                            <legend>Valores Não Retidos</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="valor_nao_retido_principal"><b>Principal:</b></label>
                                    </td>
                                    <td>
                                        <input type="text" name="valor_nao_retido_principal" id="valor_nao_retido_principal" data-mask="money">
                                    </td>
                                    <td>
                                        <label for="valor_nao_retido_adicional"><b>Adicional:</b></label>
                                    </td>
                                    <td>
                                        <input type="text" name="valor_nao_retido_adicional" id="valor_nao_retido_adicional" data-mask="money">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td>
                        <fieldset>
                            <legend>Serviços Prestados em Condições Especiais</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="valor_servicos_15"><b>15 anos:</b></label>
                                    </td>
                                    <td>
                                        <input type="text" name="valor_servicos_15" id="valor_servicos_15" data-mask="money">
                                    </td>
                                    <td>
                                        <label for="valor_servicos_20"><b>20 anos:</b></label>
                                    </td>
                                    <td>
                                        <input type="text" name="valor_servicos_20" id="valor_servicos_20" data-mask="money">
                                    </td>
                                    <td>
                                        <label for="valor_servicos_25"><b>25 anos:</b></label>
                                    </td>
                                    <td>
                                        <input type="text" name="valor_servicos_25" id="valor_servicos_25" data-mask="money">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="center">
        <input name="salvar" id="Salvar" type="button" value="salvar" onClick="js_sendForm()"/>
    </div>
</form>

<?php
db_menu();
?>
</body>
</html>
<script>
    /**
     * API (controller) das retencoes
     * */
    const API = 'v4/api/integracoes/efd-reinf/retencao';

    /**
     * Variavel de armazenamento dos dados da rentencao
     */
    let retencao = {};

    /**
     * Funcoes iniciadas no carregamento inicial
     */
    document.addEventListener('DOMContentLoaded', init);
    function init() {
        js_setRetencaoFromSessionStorage();
        js_getTipoServicoNota();
        js_fillForm(retencao);
        js_setValorBrutoFinal();
    };

    /**
     * Funcao para prencher o formulario
     * onde a chave do objeto informado corresponde ao id do input, select..
     */
    function js_fillForm(fields) {
        let inputsNames = [...Object.keys(fields)]
        inputsNames.forEach(key => {
            let el = document.querySelector('#' + key);
            if (document.body.contains(el)) {
                el.value = fields[key] ?? ""
            }
        });

        js_maskInputs();
    }

    /**
     * Mascara dos inputs
     */
    function js_maskInputs() {
        let elemets = document.querySelectorAll('[data-mask]');

        for (const el of elemets) {
            switch (el.dataset.mask) {
                case 'money':
                    el.setAttribute('placeholder', '00,00');
                    el.value = (el.value != '') ? js_formatar(el.value, 'f') : '';
                    el.addEventListener('input', e => jsFormataMoeda(e.target));
                    break;
                case 'date':
                    el.setAttribute('placeholder', '__/__/____');
                    el.value = (el.value != '') ? js_formatar(el.value, 'd') : '';
                    break;
                case 'cnpj':
                    el.value = el.value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3.$4-$5");
                    break;
            }
        }
    }

    /**
     * Funcao para recuperar os dados do sessionStoroge setado
     * na pagina pai e colocar na variavel global retencao
     */
    function js_setRetencaoFromSessionStorage() {
        let json = sessionStorage.getItem('retencao');
        retencao = JSON.parse(json);
    }

    /**
     * Consulta os tipos de servico. das notas fiscais
     */
    async function js_getTipoServicoNota() {
        js_divCarregando("Aguarde, recebendo os tipos de serviço", "msgNota");

        try {
            const action = `${API}/tipo-servico-nota`;
            const res = await axios.get(action);
            const resData = res.data;

            if (resData.error) {
                alert(resData.message);
                return false;
            }

            js_montaTipoServicoNota(resData.data.tipos);

        } catch (error) {
            alert(`Erro ao buscar o tipo de servico.`);
            console.error('An error occurred:', error.message);
        } finally {
            js_removeObj('msgNota');
        }
    }

    /**
     * Retorno dos tipos de servico de NF
     * e monta select
     */
    function js_montaTipoServicoNota(tipos) {
        if (tipos) {
            let el = document.querySelector('#referencia_tipo_servico');
            let option = '';

            tipos.forEach(item => {
                option           = document.createElement('option');
                option.value     = item.e18_sequencial;
                option.innerHTML = item.e18_descricao;

                el.appendChild(option);
            });

            el.value = retencao.referencia_tipo_servico;
        }
    }

    /**
     * validar formulario
     */
    function js_validateForm() {
        let elemets = '';
        let msg = '';
        let valor_nao_retido_principal = jsRemoveMascaraMoeda($F('valor_nao_retido_principal'));
        let valor_nao_retido_adicional = jsRemoveMascaraMoeda($F('valor_nao_retido_adicional'));
        let valor_retencao = jsRemoveMascaraMoeda($F('valor_retencao'));
        let valor_servicos_15 = jsRemoveMascaraMoeda($F('valor_servicos_15'));
        let valor_servicos_20 = jsRemoveMascaraMoeda($F('valor_servicos_20'));
        let valor_servicos_25 = jsRemoveMascaraMoeda($F('valor_servicos_25'));
        let serie_nota = $F('serie_nota');
        let sumServices = 0;

        // validar indicativo de obra
        if ($F('indicativo_obra_tipo') == "" || $F('indicativo_obra_tipo') == null) {
            alert('O Indicativo de obra deve ser informado');
            return false;
        }

        if ($F('indicativo_obra_tipo') > 0 && $F('indicativo_obra_cno') == "") {
            alert('O CNO deve ser informado');
            return false;
        }

        if($F('indicativo_obra_cno') != "" && $F('indicativo_obra_cno').length < 12){
            alert('O CNO deve possuir 12 caracteres');
            return false;
        }

        // validar tipo serviço da nota fiscal
        if ($F('referencia_tipo_servico') == "" || $F('referencia_tipo_servico') == null) {
            alert('O Tipo de Serviço da NF deve ser informado');
            return false;
        }

        // validar valor não retido principal
        if (valor_nao_retido_principal !== "" && valor_nao_retido_principal > 0) {
            if (valor_nao_retido_principal > valor_retencao) {
                alert("O <em><b>valor não retido principal</b></em> não deve ser maior do que o valor da rentenção");
                return false;
            }
        }

        // validar valor não retido adicional
        if (valor_nao_retido_adicional !== "" && valor_nao_retido_adicional > 0) {
            sumServices = ((valor_servicos_15 * 0.04) + (valor_servicos_20 * 0.03) + (valor_servicos_25 * 0.02)).toFixed(2)
            if (valor_nao_retido_adicional > sumServices) {
                msg = "O <em><b>valor não retido adicional</b></em> é maior que o cálculo dos serviços prestados em condições especiais:\n";
                msg += "(4% sobre {vlrServicos15} + 3% sobre {vlrServicos20} + 2% sobre {vlrServicos25})"
                alert(msg);
                return false;
            }
        }

        // validar somatorio dos serviços prestados em condições especiais
        if (valor_servicos_15 > 0 || valor_servicos_20 > 0 || valor_servicos_25) {
            sumServices = (valor_servicos_15 + valor_servicos_20 + valor_servicos_25).toFixed(2);
            if (sumServices > valor_retencao) {
                msg = "A soma dos serviços prestados em condições especiais é maior que o valor da retenção";
                alert(msg);
                return false;
            }
        }

        // validar serie da nota
        if (serie_nota.length > 5) {
            msg = "A série nota deve conter no máximo <b>5</b> caracteres";
            alert(msg);
            return false;
        }

        return true
    }

    /**
     * Cria objeto com os valores dos inputs
     */
    function js_mapForm() {
        let obj   = {};
        let value = '';
        let form  = document.querySelector("[name='form1']");

        for (const el of form.elements) {

            switch (el.dataset.mask) {
                case 'money':
                    value = jsRemoveMascaraMoeda(el.value);
                    break;
                default:
                    value = el.value;
                    break;
            }

            obj[el.id] = value;
        }

        return obj;
    }

    /**
     * Envia formulario
     */
    async function js_sendForm() {
        if (!js_validateForm()) { return false; }

        js_divCarregando("Aguarde, Salvando dados da retenção", "msgRetencao");

        try {
            const action  = `${API}/save-retencao`;
            let params = js_mapForm();

            params.evento = 'r2010';
            params.nome_prestador = '';

            const response = await axios.post(action, params);
            const responseData = response.data;

            if (responseData.error) {
                alert(responseData.message);
                return false;
            }

            parent.js_getRetencoes();
            alert('Os dados da retenção foram salvos com sucesso');

        } catch (error) {
            alert(`Erro ao salvar Retenção`);
            console.error('An error occurred:', error.message);
        } finally {
            js_removeObj("msgRetencao");
        }
    }

    /**
     * Funcao para mudar o label do cno
     * quando mudar o ind. de obra
     */
    function js_changeLabelCno(ev) {
        let el = document.querySelector('#indicativo_obra_cno');
        el.removeAttribute('disabled');

        switch (ev.value) {
            case "1":
                el.setAttribute('placeholder', 'CNO Prestador');
                break;
            case "2":
                el.setAttribute('placeholder', 'CNO Contribuinte');
                break;
            default:
                el.setAttribute('placeholder', 'CNO');
                el.setAttribute('disabled', 'true');
                el.value = '';
                break;
        }
    }

    /**
     * Modal/Action da janela de alterar a retencao
     * */
    function js_notas() {
        let action = `emp1_empconsultanf002.php?e69_numero=${retencao.numero_nota}&z01_numcgm=${retencao.identificador_prestador}`;
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_notas',
            action,
            'Notas', true);
    }

    /** Valor bruto final */
    function js_setValorBrutoFinal() {
        let valor = Number(retencao.valor_nota_liq);
        if (retencao.notas_nao_retidas) {
            valor += Number(retencao.notas_nao_retidas);
        }

        document.querySelector('#valor_bruto_final').value = valor.toLocaleString('pt-br', {minimumFractionDigits: 2});
    }

    /**
     * Validar apenas valores numéricos e limite para 12 caracteres no input CNO
     * */
    document.addEventListener('DOMContentLoaded', function() {
        const numericInput = document.getElementById('indicativo_obra_cno');

        // Adiciona um ouvinte de evento de entrada para validar a entrada
        numericInput.addEventListener('input', function() {
            const inputValue = numericInput.value;
            // Usa uma expressão regular para remover caracteres não numéricos
            const numericValue = inputValue.replace(/[^0-9]/g, '');
            // Limita o valor ao comprimento máximo
            numericInput.value = numericValue.substring(0, numericInput.maxLength);
        });
    });
</script>
