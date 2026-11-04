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
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("dbforms/db_classesgenericas.php"));
?>
<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="javascript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/dates.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        input:disabled:not([type=button]), select:disabled {
            background-color: #DEB887 !important;
            color: black !important;
        }
    </style>
</head>
<body class="body-default">
<div class="container">
    <form name="frmESocial" id="frmESocial">
        <fieldset>
            <legend>Informações</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="codigoRubrica">Rubrica:</label>
                    </td>
                    <td>
                        <input type="hidden" name="sequencial" id="sequencial">
                        <input type="hidden" name="instituicao" id="instituicao">
                        <input type="text" name="codigoRubrica" id="codigoRubrica" disabled="disabled"
                               style="width: 15%">
                        <input type="text" name="descricaoRubrica" id="descricaoRubrica" disabled="disabled"
                               style="width: 85%">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaPrevi">Incidência de Contrib. Previdenciária:</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaPreviNovo">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr id="infoProcessoCp" style="display: none;">
                    <td colspan="2">
                        <fieldset>
                            <legend>Informações de Isenção de Incidência de Contribuição Previdenciária</legend>
                            <table class="form-container">
                                <tr>
                                    <td>
                                        <label for="cbxTpProc">Tipo de Processo:</label>
                                    </td>
                                    <td>
                                        <select id="cbxTpProc">
                                            <option value="">Selecione...</option>
                                            <option value=1>1 - Administrativo</option>
                                            <option value=2>2 - Judicial</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="nrProcProcessoCp">Número do processo:</label>
                                    </td>
                                    <td>
                                        <input id="nrProcProcessoCp" type="text" maxlength="21">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="cbxExtDecisao">Extensão da decisão/sentença::</label>
                                    </td>
                                    <td>
                                        <select id="cbxExtDecisao">
                                            <option value="">Selecione...</option>
                                            <option value=1>1 - Contribuição previdenciária patronal</option>
                                            <option value=2>2 - Contribuição previdenciária patronal + descontada dos
                                                segurados
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="cbxCodSuspProcessoCp">Código indicativo da Suspensão:</label>
                                    </td>
                                    <td>
                                        <select id="cbxCodSuspProcessoCp">
                                            <option value="">Selecione...</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaIRRF">Incidência de IRRF:</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaIRRFNovo">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr id="infoIrrf" style="display: none;">
                    <td colspan="2">
                        <fieldset>
                            <legend>Informações de Isenção de Incidência de Imposto de Renda</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="nrProcIrrf">Número do Processo:</label>
                                    </td>
                                    <td>
                                        <input id="nrProcIrrf" type="text" maxlength="20">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="cbxCodSuspIrrf">Código indicativo da Suspensão:</label>
                                    </td>
                                    <td>
                                        <select id="cbxCodSuspIrrf">
                                            <option value="">Selecione...</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaFGTS">Incidência de FGTS:</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaFGTSNovo">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr id="infoFgts" style="display: none;">
                    <td colspan="2">
                        <fieldset>
                            <legend>Informações de Isenção de Incidência de FGTS</legend>
                            <table class="form-container">
                                <tr>
                                    <td>
                                        <label for="nrProcFgts">Número do Processo:</label>
                                    </td>
                                    <td>
                                        <input id="nrProcFgts" type="text" maxlength="20">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxNatureza">Natureza da Rubrica(Conforme tabela 3):</label>
                    </td>
                    <td>
                        <select id="cbxNatureza">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxSubCodigoTCE">SubCódigo TCE-RS:</label>
                    </td>
                    <td>
                        <select id="cbxSubCodigoTCE">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaRegimeProprio">Incidência da rubrica para RPPS/regime militar:</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaRegimeProprioNovo">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaPisPasep">Incidência de PIS/PASEP:</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaPisPasepNovo">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr id="infoPisPasep" style="display: none;">
                    <td colspan="2">
                    <fieldset>
                            <legend>Informações de Isenção de Incidência de Pis/Pasep</legend>
                            <table class="form-container">
                                <tr>
                                    <td>
                                        <label for="nrProcPisPasep">Número do Processo:</label>
                                    </td>
                                    <td>
                                        <input id="nrProcPisPasep" type="text" maxlength="20">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="cbxCodSuspPisPasep">Código indicativo da Suspensão:</label>
                                    </td>
                                    <td>
                                        <select id="cbxCodSuspPisPasep">
                                            <option value="">Selecione...</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                    </fieldset>
                    </td>
                </tr>
                </tr>
                <tr>
                    <td>
                        <label for="cbxIncidenciaTeto">Rubrica compõe o teto remuneratório específico (art. 37, XI, da
                            CF/1988):</label>
                    </td>
                    <td>
                        <select id="cbxIncidenciaTetoNovo">
                            <option value="">Selecione...</option>
                            <option value="S">Sim</option>
                            <option value="N">Não</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td title="Data de início da validade das informações para o eSocial.">
                        <label for="dataInicial">Início de validade:</label>
                    </td>
                    <td>
                        <input id="dataInicial" name="dataInicial" type="text">
                    </td>
                </tr>
                <tr>
                    <td title="Data final da validade das informações para o eSocial.">
                        <label for="dataFinal">Fim de validade:</label>
                    </td>
                    <td>
                        <input id="dataFinal" name="dataFinal" type="text">
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="button" name="btnSalvar" id="btnSalvar" value="Salvar">
    </form>
</div>
<div id="modalRubrica" style="display: none;">
    <div class="containerModal">
        <fieldset>
            <legend>Deseja processar e enviar a rubrica para o eSocial?</legend>
            <table style="text-align: center">
                <tr>
                    <td style="padding-bottom: 25px">
                        <label for="modal_rubrica">
                            <strong>Selecione o empregador:</strong>
                        </label>
                    </td>
                    <td style="padding-bottom: 25px">
                        <select id="empregadorEsocial">
                            <option value="">Selecione...</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="button" value="Salvar" id="fechar">
                        <input type="button" name="btnEsocial" value="Salvar/Enviar para o eSocial" id="confirmar">
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</div>
</body>
</html>

<script type="text/javascript">
    const urlParams = new URLSearchParams(window.location.search);
    const urlRpc = 'pes1_esocialrhrubricas001.RPC.php';
    const dataInicial = new DBInputDate($('dataInicial'));
    const dataFinal = new DBInputDate($('dataFinal'));

    const comboTCE = document.getElementById('cbxSubCodigoTCE');
    const comboNatureza = document.getElementById('cbxNatureza');

    const incidenciaPreviNovo = document.getElementById('cbxIncidenciaPreviNovo');
    const incidenciaIRRFNovo = document.getElementById("cbxIncidenciaIRRFNovo");
    const incidenciaFGTSNovo = document.getElementById("cbxIncidenciaFGTSNovo");
    const incidenciaPisPasepNovo = document.getElementById("cbxIncidenciaPisPasepNovo");

    const infoProcessoCp = document.getElementById('infoProcessoCp');
    const infoIrrf = document.getElementById('infoIrrf');
    const infoFgts = document.getElementById('infoFgts');
    const infoPisPasep = document.getElementById('infoPisPasep');

    var opcoesSubGrupo;

    /**
     * Instancias da modal
     */
    const modalEsocialRubrica = document.querySelector('#modalRubrica');
    const botaoConfirmar = document.querySelector('#confirmar');
    document.querySelector('#fechar').addEventListener('click', () => {
        modalEsocialRubrica.style.display = 'none';
    });

    document.querySelector('#confirmar').addEventListener('click', () => {
        modalEsocialRubrica.style.display = 'none';
    });

    if (!urlParams.get('codigoRubrica')) {
        setFormReadOnly($('frmESocial'), true);
    }

    $('codigoRubrica').value = urlParams.get('codigoRubrica');
    $('descricaoRubrica').value = urlParams.get('descricaoRubrica');

    function buscar() {
        js_divCarregando('Aguarde, buscando informações...', 'loading_message');
        const formData = new FormData();
        formData.append('acao', 'buscar');
        formData.append('codigoRubrica', $F('codigoRubrica'));

        return fetch(urlRpc, {
            method: 'POST',
            body: formData,
            credentials: 'include',
        }).then(response => {
            js_removeObj('loading_message');
            return response;
        }).then(response => response.json()).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            opcoesSubGrupo = response.subgruposrubricas;

            response.opcoesNatureza.forEach(opcao => {
                $('cbxNatureza').add(new Option(opcao.label, opcao.value));
            });

            /** Preenche o array de opções com os dados recebidos pelo RPC */
            response.opcoesIncCP.forEach(opcao => {
                $('cbxIncidenciaPreviNovo').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesIncIRRF.forEach(opcao => {
                $('cbxIncidenciaIRRFNovo').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesIncFGTS.forEach(opcao => {
                $('cbxIncidenciaFGTSNovo').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesIncCPRP.forEach(opcao => {
                $('cbxIncidenciaRegimeProprioNovo').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesIncPisPasep.forEach(opcao => {
                $('cbxIncidenciaPisPasepNovo').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesCodSuspProcessoCp.forEach(opcao => {
                $('cbxCodSuspProcessoCp').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesCodSuspIrrf.forEach(opcao => {
                $('cbxCodSuspIrrf').add(new Option(opcao.label, opcao.value));
            });

            response.opcoesCodSuspPisPasep.forEach(opcao => {
                $('cbxCodSuspPisPasep').add(new Option(opcao.label, opcao.value));
            });


            response.empregadores.forEach(opcao => {
                $('empregadorEsocial').add(new Option(opcao.nome, opcao.cgm));
            });

            $('cbxIncidenciaPreviNovo').value = (response.rubrica.incCP) ? response.rubrica.incCP : '';

            $('cbxIncidenciaIRRFNovo').value = (response.rubrica.incIRRF) ? response.rubrica.incIRRF : '';

            $('cbxIncidenciaFGTSNovo').value = (response.rubrica.incFGTS) ? response.rubrica.incFGTS : '';

            $('cbxIncidenciaRegimeProprioNovo').value = (response.rubrica.incCPRP) ? response.rubrica.incCPRP : '';

            $('cbxIncidenciaPisPasepNovo').value = (response.rubrica.incPisPasep) ? response.rubrica.incPisPasep : '';

            $('empregadorEsocial').value = (response.rubrica.empregadorEsocial)
                ? response.rubrica.empregadorEsocial : '';

            $('cbxIncidenciaTetoNovo').value = (response.rubrica.tetoRemun) ? response.rubrica.tetoRemun : '';

            $('sequencial').value = response.rubrica.sequencial;
            $('instituicao').value = response.rubrica.instituicao;
            $('cbxNatureza').value = (response.rubrica.natureza) ? response.rubrica.natureza : '';

            if (response.rubrica.dataInicial) {
                dataInicial.setValue(response.rubrica.dataInicial);
            }

            if (response.rubrica.dataFinal) {
                dataFinal.setValue(response.rubrica.dataFinal);
            }

            // Novos campos adicionados
            $('cbxTpProc').value = response.rubrica.tpProc || '';
            $('nrProcProcessoCp').value = response.rubrica.nrProcProcessoCP || '';
            $('cbxExtDecisao').value = response.rubrica.extDecisao || '';
            $('cbxCodSuspProcessoCp').value = response.rubrica.codSuspProcessoCP || '';
            $('nrProcIrrf').value = response.rubrica.nrProcIRRF || '';
            $('cbxCodSuspIrrf').value = response.rubrica.codSuspIRRF || '';
            $('nrProcFgts').value = response.rubrica.nrProcFGTS || '';
            $('nrProcPisPasep').value = response.rubrica.nrProcPisPasep || '';
            $('cbxCodSuspPisPasep').value = response.rubrica.codSuspPisPasep || '';

            mostrarInfoProcessoCp();
            mostrarInfoFgts();
            mostrarInfoIrrf();
            mostrarInfoPisPasep();
            atualizaSubGrupo(response.rubrica.subgrupotce);
        });
    }

    buscar();

    $('confirmar').onclick = function () {
        if (!validarFormularioEsocial()) {
            return false;
        }
        processamento(true);
    }

    $('fechar').onclick = function () {
        processamento(false);
    }

    $('btnSalvar').onclick = function () {
        modalEsocialRubrica.style.display = 'flex';
    };

    function processamento(value) {

        if (!validarFormulario()) {
            return false;
        }

        let enviaEsocial = value;
        const incCP = $F('cbxIncidenciaPreviNovo');
        const incIRRF = $F('cbxIncidenciaIRRFNovo');
        const incFGTS = $F('cbxIncidenciaFGTSNovo');
        const incCPRP = $F('cbxIncidenciaRegimeProprioNovo');
        const incPisPasep = $F('cbxIncidenciaPisPasepNovo');
        const tetoRemun = $F('cbxIncidenciaTetoNovo');
        const empregadorEsocial = $F('empregadorEsocial');
        const tpProc = $F('cbxTpProc');
        const nrProcProcessoCP = $F('nrProcProcessoCp');
        const extDecisao = $F('cbxExtDecisao');
        const codSuspProcessoCP = $F('cbxCodSuspProcessoCp');
        const nrProcIRRF = $F('nrProcIrrf');
        const codSuspIRRF = $F('cbxCodSuspIrrf');
        const nrProcFGTS = $F('nrProcFgts');
        const nrProcPisPasep = $F('nrProcPisPasep');
        const codSuspPisPasep = $F('cbxCodSuspPisPasep');

        var rubrica = {
            sequencial: $F('sequencial'),
            rubrica: $F('codigoRubrica'),
            instituicao: $F('instituicao'),
            natureza: $F('cbxNatureza'),
            dataInicial: dataInicial.getValue(),
            dataFinal: dataFinal.getValue(),
            subgrupotce: comboTCE.value,
            incCP: incCP,
            incIRRF: incIRRF,
            incFGTS: incFGTS,
            incCPRP: incCPRP,
            incPisPasep: incPisPasep,
            tetoRemun: tetoRemun,
            cgm: empregadorEsocial,
            tpProc: tpProc,
            nrProcProcessoCP: nrProcProcessoCP,
            extDecisao: extDecisao,
            codSuspProcessoCP: codSuspProcessoCP,
            nrProcIRRF: nrProcIRRF,
            codSuspIRRF: codSuspIRRF,
            nrProcFGTS: nrProcFGTS,
            nrProcPisPasep: nrProcPisPasep,
            codSuspPisPasep: codSuspPisPasep
        };

        js_divCarregando('Aguarde, salvando informações...', 'loading_message');
        const formData = new FormData();
        formData.append('acao', 'salvar');
        formData.append('rubrica', JSON.stringify(rubrica));
        formData.append('enviaEsocial', enviaEsocial);

        return fetch(urlRpc, {
            method: 'POST',
            body: formData,
            credentials: 'include',
        }).then(response => {
            js_removeObj('loading_message');
            return response;
        }).then(response => response.json()).then(response => {
            alert(response.mensagem);

            if (response.erro) {
                return;
            }

            $('sequencial').value = response.rubrica.sequencial;
            $('instituicao').value = response.rubrica.instituicao;
        });
    }

    function validarFormulario() {
        let isPB = '<?php echo isParaiba() ?>';

        if (!isPB) {
            if ($F('cbxIncidenciaPreviNovo') == '') {
                alert('Incidência de Contrib. Previdenciária não informada.');
                return false;
            }

            if ($F('cbxIncidenciaIRRFNovo') == '') {
                alert('Incidência de IRRF não informada.');
                return false;
            }

            if ($F('cbxIncidenciaFGTSNovo') == '') {
                alert('Incidência de FGTS não informada.');
                return false;
            }

            if ($F('cbxNatureza') == '') {
                alert('Natureza da rubrica não informada.');
                return false;
            }
        }

        if ($F('codigoRubrica') == '') {
            alert('Código da rubrica não informado.');
            return false;
        }

        if (dataInicial.getValue() == '') {
            alert('Data de início de validade não informada.');
            return false;
        }

        if (dataFinal.getValue()) {
            const inicial = Date.convertFrom($('dataInicial').value, DATA_PTBR);
            const final = Date.convertFrom($('dataFinal').value, DATA_PTBR);

            if (inicial.getTime() > final.getTime()) {
                alert('Data de Inicio de validade deve ser menor que a Data de Fim de validade.');
                return false;
            }
        }

        return true;
    }

    function validarFormularioEsocial() {
        if ($F('empregadorEsocial') == '') {
            alert('É necessário selecionar um empregador.');
            return false;
        }
        return true;
    }

    function atualizaSubGrupo(selecionado = '') {
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Selecione...';
        comboTCE.innerHTML = "";
        comboTCE.appendChild(opt);
        opcoesSubGrupo.forEach((elemento) => {
            if (elemento.grupo == comboNatureza.value) {
                elemento.dado.forEach((dado) => {
                    var opt = document.createElement('option');
                    opt.value = dado.value;
                    opt.innerHTML = dado.label;
                    comboTCE.appendChild(opt);
                    if (dado.value == selecionado) {
                        comboTCE.value = dado.value;
                    }
                });
            }
        });
    }

    function mostrarInfoProcessoCp() {
        let codigoHabilitarCampos = ['91', '92', '93', '94', '95', '96', '97', '98'];
        let tpProc = document.getElementById('cbxTpProc');
        let nrProcProcessoCP = document.getElementById('nrProcProcessoCp');
        let extDecisao = document.getElementById('cbxExtDecisao');
        let codSuspProcessoCP = document.getElementById('cbxCodSuspProcessoCp');

        let ultimoCodigo = mostrarInfoProcessoCp.ultimoCodigo || null;

        if (codigoHabilitarCampos.includes(incidenciaPreviNovo.value)) {
            infoProcessoCp.style.display = 'block'; // Exibe o fieldset

            if (ultimoCodigo && ultimoCodigo !== incidenciaPreviNovo.value) {
                tpProc.value = '';
                nrProcProcessoCP.value = '';
                extDecisao.value = '';
                codSuspProcessoCP.value = '';
            }
        } else {
            infoProcessoCp.style.display = 'none'; // Oculta o fieldset
            tpProc.style.display = '';
            nrProcProcessoCP.value = ''; 
            extDecisao.value = ''; 
            codSuspProcessoCP.value = '';
        }
        mostrarInfoProcessoCp.ultimoCodigo = incidenciaPreviNovo.value;
    }

    function mostrarInfoIrrf() {
        let codigoHabilitarCampos = [
            "9011", "9012", "9013", "9014", "9031", "9032", "9033", "9034",
            "9831", "9832", "9833", "9834", "9041", "9042", "9043", "9046",
            "9047", "9048", "9051", "9052", "9053", "9054", "9061", "9062",
            "9063", "9064", "9065", "9066", "9067", "9082", "9083"
        ];
        let nrProcIRRF = document.getElementById('nrProcIrrf');
        let codSuspIRRF = document.getElementById('cbxCodSuspIrrf');
        let ultimoCodigo = mostrarInfoIrrf.ultimoCodigo || null;

        if (codigoHabilitarCampos.includes(incidenciaIRRFNovo.value)) {
            infoIrrf.style.display = 'block';

            if (ultimoCodigo && ultimoCodigo !== incidenciaIRRFNovo.value) {
                nrProcIRRF.value = '';
                codSuspIRRF.value = '';
            }
        } else {
            infoIrrf.style.display = 'none';
            nrProcIRRF.value = ''; 
            codSuspIRRF.value = '';
        }
        mostrarInfoFgts.ultimoCodigo = incidenciaIRRFNovo.value;
    }

    function mostrarInfoFgts() {
        let codigoHabilitarCampos = ['91', '92', '93'];
        let nrProcFGTS = document.getElementById('nrProcFgts');
        let ultimoCodigo = mostrarInfoFgts.ultimoCodigo || null;

        if (codigoHabilitarCampos.includes(incidenciaFGTSNovo.value)) {
            infoFgts.style.display = 'block';

            if (ultimoCodigo && ultimoCodigo !== incidenciaFGTSNovo.value) {
                nrProcFGTS.value = '';
            }
        } else {
            infoFgts.style.display = 'none';
            nrProcFGTS.value = '';
        }
        mostrarInfoFgts.ultimoCodigo = incidenciaFGTSNovo.value;
    }

    function mostrarInfoPisPasep() {
        let codigoHabilitarCampos = ['91', '92']; // Lista de códigos que habilitam os campos extras
        let nrProcPisPasep = document.getElementById('nrProcPisPasep');
        let cbxCodSuspPisPasep = document.getElementById('cbxCodSuspPisPasep');
        
        // Variável que armazena o último código selecionado
        let ultimoCodigo = mostrarInfoPisPasep.ultimoCodigo || null;
        
        // Se o código atual está na lista
        if (codigoHabilitarCampos.includes(incidenciaPisPasepNovo.value)) {
            infoPisPasep.style.display = 'block';
        
            // Se trocou de código habilitado, limpa os campos extras
            if (ultimoCodigo && ultimoCodigo !== incidenciaPisPasepNovo.value) {
                nrProcPisPasep.value = '';
                cbxCodSuspPisPasep.value = '';
            }
        } else {
            infoPisPasep.style.display = 'none';
            // Limpa os campos extras ao sair da lista
            nrProcPisPasep.value = '';
            cbxCodSuspPisPasep.value = '';
        }
        // Atualiza o último código selecionado
        mostrarInfoPisPasep.ultimoCodigo = incidenciaPisPasepNovo.value;
    }

    incidenciaPreviNovo.addEventListener('change', mostrarInfoProcessoCp);
    incidenciaIRRFNovo.addEventListener('change', mostrarInfoIrrf);
    incidenciaFGTSNovo.addEventListener('change', mostrarInfoFgts);
    incidenciaPisPasepNovo.addEventListener('change', mostrarInfoPisPasep);

    $('cbxNatureza').onchange = function () {
        atualizaSubGrupo();
    };
</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .containerModal {
        position: absolute;
        background: #e1dede;
        border: 1px solid #9b9898;
        justify-content: center;
        box-shadow: rgba(0, 0, 0, 0.1) 0 5px 5px 0, rgba(0, 0, 0, 0.01) 0 5px 5px 5px, rgba(0, 0, 0, 0.01) 0 5px 5px 0;
        left: 50%;
        transform: translate(-50%, -50%)
    }
</style>
