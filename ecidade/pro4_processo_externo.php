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
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
$tipoProcesso = $oGet->tipoProcesso;
$processo = $oGet->processo;
$ano = $oGet->ano;
$escondeBotoes = $oGet->escondeBotoes;
$codigoProcessoProtocolo = isset($oGet->codigoProcessoProtocolo) ? $oGet->codigoProcessoProtocolo : null;
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/classes/patrimonio/ouvidoria/processoeletronico/AprovacaoRejeicao.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBModal.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/patrimonio/ouvidoria/processoeletronico/camposAdicionais/AlvaraOnline.js"></script>
    <link href="skins/default/estilos/widgets/DBModal.css" rel="stylesheet" type="text/css">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        .button-action {
            text-align: center !important;
        }
    </style>
</head>
<body bgcolor="#CCCCCC" onLoad="a=1">
<div class="container">
    <div class="col-md-6"
    >
        <form id="form1" name="form1">
            <?php db_input('tipoProcesso', 10, 0, false, 'hidden', 3, ""); ?>
            <?php db_input('processo', 10, 0, false, 'hidden', 3, ""); ?>
            <?php db_input('ano', 10, 0, false, 'hidden', 3, ""); ?>
            <?php db_input('codigoProcessoProtocolo', 10, 0, false, 'hidden', 3, ""); ?>
            <fieldset id="containerAtendimento">
                <legend>Dados do Atendimento</legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3"><label for="processoAtendimento">Atendimento:</label></td>
                        <td><input type="text" name="processoAtendimento" id="processoAtendimento" disabled="" class="field-size7" /></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset id="containerRequerente" style="display: none;">
                <legend>Dados do requerente</legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3"><label for="nomeRequerente">Requerente:</label></td>
                        <td><input type="text" name="nomeRequerente" id="nomeRequerente" disabled="" class="field-size7" /></td>
                        <td class="field-size3"><label for="cpfRequerente">CPF/CNPJ:</label></td>
                        <td><input type="text" name="cpfRequerente" id="cpfRequerente" disabled="" class="field-size7" /></td>
                    </tr>
                </table>
            </fieldset>
            <div  id="containerInscricoes" name="containerInscricoes"></div>
            <fieldset id="containerSolicitacao" name="containerSolicitacao">
            </fieldset>
            <fieldset id="containerObservacaoMotivo">
                <legend>Observação</legend>
                  <textarea name="observacao_motivo" id="observacao_motivo" style="width: 100%;" rows="10"></textarea>
            </fieldset>
            <div class='button-container' <?= ($escondeBotoes == 'true') ? 'style="display:none;"' : ''?>>
                <input type="button" name="btnAprovar" id="btnAprovar" value="Aprovar"/>
                <input type="button" name="btnRejeitar" id="btnRejeitar" value="Rejeitar"/>
                <input type="button" name="btnVisualizarDocumentos" id="btnVisualizarDocumentos" value="Visualizar Documentos" style="display: none" />
            </div>
        </form>
    </div>
    <div class="col-md-6" id="dados-para-conferencia"></div>

</div>

<script>

    const PROCESSO = <?=$processo;?>;
    const ANO = <?=$ano;?>;
    const divDadosParaConferencia = document.getElementById("dados-para-conferencia");
    const modal = new DBModal();
    var visualizarEmOutraJanela = <?=$visualizarEmOutraJanela?>;
    var ecidadeInfo = {
        codigoInstituicao: null,
        codigoDepartamento: null,
        codigoUsuario: null,
        apiUrl: ''
    };

    var codigosEStorage = [];

    const oClasses = {};

    const urlRpc = 'ouv4_solicitacaoprocessoeletronico.RPC.php';
    const elemento = $('containerSolicitacao');
    const tipoProcesso = $('tipoProcesso');
    const processo = $('processo');
    const ano = $('ano');
    const codigoProcessoProtocolo = $('codigoProcessoProtocolo');

    getEcidadeInfo().then(ecidadeInfo => {
        console.log(ecidadeInfo);
        getDadosProcesso().then(response => {
                acaoJson = response.acao;
                montaDadosFormulario(response, elemento);
            });
    });

    function montarSecaoForm(secao, elemento){

        var fieldsetSecao = document.createElement('fieldset');
        var legend = document.createElement('legend');
        var tableContainer = criarFromTabelaDados(secao.campos);

        tableContainer.className = 'form-container';
        fieldsetSecao.className = 'separator';
        legend.innerHTML = secao.label;

        fieldsetSecao.appendChild(legend);
        fieldsetSecao.appendChild(tableContainer);
        elemento.appendChild(fieldsetSecao);
        elemento.appendChild(document.createElement('br'));
    }

    function criarFromTabelaDados(campos) {

        var contadorCampos = 0;
        var tableContainer = document.createElement('table');
        var trCampo = document.createElement('tr');

        for (let index in campos) {
            let campo = campos[index];

            if (!campo.label || (campo.resposta instanceof Array)) {
                continue;
            }

            contadorCampos++;

            if (contadorCampos == 1) {
                trCampo = document.createElement('tr');
            }

            var tdLabelCampo = document.createElement('td');
            var tdValorCampo = document.createElement('td');
            var labelCampo = document.createElement('label');
            var inputCampo = document.createElement('input');
            var textAreaCampo =  document.createElement('textarea');
            tdLabelCampo.className = 'field-size3';
            labelCampo.for = campo.nome;
            labelCampo.innerHTML = '<b>' + campo.label + ':</b>';
            const textAreaOptions = ['observacoes'];
            if(textAreaOptions.includes(campo.nome)){
                textAreaCampo.id = campo.nome;
                textAreaCampo.type = 'text';
                textAreaCampo.disabled = 'disabled';
                textAreaCampo.className = 'field-size7';
                if (campo.resposta) {
                    textAreaCampo.value =  typeof campo.resposta == 'object' ? campo.resposta.descricao : campo.resposta;
                }
                tdValorCampo.appendChild(textAreaCampo);
            }else{
                inputCampo.id = campo.nome;
                inputCampo.type = 'text';
                inputCampo.disabled = 'disabled';
                inputCampo.className = 'field-size7';
                if (campo.resposta) {
                    inputCampo.value =  typeof campo.resposta == 'object' ? campo.resposta.descricao : campo.resposta;
                }
                tdValorCampo.appendChild(inputCampo);
            }

            tdLabelCampo.appendChild(labelCampo);

            trCampo.appendChild(tdLabelCampo);
            trCampo.appendChild(tdValorCampo);

            if (contadorCampos == 2 || (campos.length - 1) == index ) {
                tableContainer.appendChild(trCampo);
                contadorCampos = 0;
            }
        }

        return tableContainer;
    }


    function montarGrid(secao, elemento, possuiAcao) {

        var fieldsetSecao = document.createElement('fieldset');
        var legend = document.createElement('legend');
        var divGridContainer = document.createElement('div');
        divGridContainer.id =  secao.nome;
        fieldsetSecao.className = 'separator';

        var collection = new Collection().setId('codigo');
        var grid = DatagridCollection.create(collection).configure({'order': false, 'height': 100});
        var contadorColunas = 0;
        var maxColunas = 6;
        var percentualTotalColunas = (secao.campos.length > maxColunas || possuiAcao) ? 90 : 100;
        var tamanhoColunas = percentualTotalColunas / ((secao.campos.length > maxColunas) ? maxColunas  : secao.campos.length);

        secao.campos.forEach((campo) => {
            if (contadorColunas < maxColunas) {
                grid.addColumn( campo.nome, {label: campo.label, width: tamanhoColunas + '%'});
            }
            contadorColunas++;
        });


        if (secao.resposta && Array.isArray(secao.resposta)) {

            secao.resposta.forEach((resposta) => {
                for (var prop in resposta) {
                    if (resposta[prop]) {
                        if (typeof resposta[prop] == 'object') {
                            resposta[prop] = resposta[prop].descricao;
                        }
                    } else {
                        resposta[prop] = "";
                    }
                }
            });

            collection.add(secao.resposta);
        }


        if (secao.campos.length > maxColunas) {
            montarAcaoDetalhes(grid);
        }

        if (secao.tipo == 'anexo') {
            montarAcaoDownload(grid);
        }


        legend.innerHTML = secao.label;
        fieldsetSecao.appendChild(legend);
        fieldsetSecao.appendChild(divGridContainer);

        elemento.appendChild(fieldsetSecao);
        elemento.appendChild(document.createElement('br'));
        grid.show(divGridContainer);
    }

    function montarAcaoDownload(grid) {
        grid.addAction("Download", null, function(event, item) {

          const data = new FormData();
          data.append('id', item.codigo);

          HttpClient.post(`${ecidadeInfo.apiUrl}patrimonial/protocolo/processo/processodocumento/download`, {body: data}).then(response => {

            if(response.error == true){
              alert(response.message);
              return;
            }

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_download', 'db_download.php?arquivo=' + response.data, 'Download de arquivos', false);
          });
        }, true, 'fa-download', ['button-action']);
    }

    function montarAcaoDetalhes(grid) {
        grid.addAction("Detalhes", null, function(event, item) {

            var iHeight = 350;
            var iWidth  = 950;
            var divDetalhes = document.createElement('div');
            var fieldsetDetalhes = document.createElement('fieldset');
            var legendDetalhes = document.createElement('legend');
            var campos = [];

            var windowDetalhes = new windowAux('wndDetalhes',
              'Detalhes',
              iWidth,
              iHeight
            );

            for (var prop in item) {

                if (typeof item[prop] == 'object' || typeof item[prop] == 'function') {
                    continue;
                }

                var campo = {};
                campo.label = prop;
                campo.nome = prop;
                campo.resposta = item[prop];
                campos.push(campo);
            }

            var tableContainer = criarFromTabelaDados(campos);
            legendDetalhes.innerHTML = 'Detalhes';

            fieldsetDetalhes.appendChild(legendDetalhes);
            fieldsetDetalhes.appendChild(tableContainer);
            divDetalhes.appendChild(fieldsetDetalhes);

            windowDetalhes.setContent(divDetalhes);
            windowDetalhes.show(null, null, true);

            windowDetalhes.setShutDownFunction(function() {
                windowDetalhes.destroy();
            });

        }, true, 'fa-info-circle', ['button-action']);
    }

    function montarSecaoAnexo(secao, elemento){

        $('btnVisualizarDocumentos').style.display = '';

        secao.campos = [
            {nome: 'codigo', label: 'Código'},
            {nome: 'descricao', label: 'Descrição'}
        ];


        if (secao.resposta) {
            secao.resposta.forEach((resposta) => {
                codigosEStorage.push(resposta.codigo);
            });
        }

        montarGrid(secao, elemento, true);
    }

    function montaDadosFormulario(dadosSolicitacao, elemento) {
        var legend = document.createElement('legend');
        var tr = document.createElement('tr');
        var contadorLista = 0;
        legend.innerHTML = 'Solicitação - ' + dadosSolicitacao.descricao;
        elemento.appendChild(legend);

        dadosSolicitacao.secoes.forEach((secao) => {

            switch(secao.tipo) {
                case 'form':
                    montarSecaoForm(secao, elemento);
                    break;
                case 'tabela':
                    montarGrid(secao, elemento, false);
                    break;
                case 'anexo':
                    montarSecaoAnexo(secao, elemento);
                    break;
            }

            criaCamposAdicionais(
                (dadosSolicitacao.hasOwnProperty("acao") ? dadosSolicitacao.acao : null),
                secao
            );
        });
        if(dadosSolicitacao.hasOwnProperty("acao") && dadosSolicitacao.acao == 'gerarAlvara'){
            oClasses.AlvaraOnline.criarCamposInscricoes(
                ecidadeInfo,
                ANO,
                PROCESSO
            );
        }

    }

    function criaCamposAdicionais(acao, secao) {
        instaciaClasses(acao);

        switch (acao) {
            case "gerarAlvara":
                switch (secao.nome) {
                    case "atividades":
                        oClasses.AlvaraOnline.setSecao(secao).criaCampoAtividadeProvisoria().criaGrauRisco();
                        break;
                }
                break;
        }
    }

    function instaciaClasses(acao) {
        switch (acao) {
            case "gerarAlvara":
                if (!oClasses.hasOwnProperty("AlvaraOnline")) {
                    oClasses.AlvaraOnline = new AlvaraOnline();
                }
                break;
        }
    }

    function getDadosProcesso() {

        const data = new FormData();
        data.append('numeroProcesso', processo.value);
        data.append('anoProcesso', ano.value);


        return HttpClient.post(`${ecidadeInfo.apiUrl}patrimonial/ouvidoria/atendimento/atendimento/buscarSolicitacaoOuvidoria`, {body: data}).then(response => {
            if(response.error == true){
                alert(response.message);
            }

            $('processoAtendimento').value = response.data.processo;

            if (response.data.cidadao) {
                $('nomeRequerente').value = response.data.cidadao.ov02_nome;
                $('cpfRequerente').value = js_formatar(response.data.cidadao.ov02_cnpjcpf, 'cpfcnpj');
                $('containerRequerente').style.display = '';
            }

            if(response.data.formareclamacao == 9 && response.data.cgm){
                const cgm = response.data.cgm;
                let dadosFiliacao = '';

                if (cgm.z01_cgccpf.length == 11) {
                    dadosFiliacao = `<tr>
                    <td class="field-size3 text-left">
                        <label><b>Pai:</b></label>
                    </td>
                    <td>${cgm.z01_pai}</td>
                    <td class="field-size3 text-left">
                        <label><b>Mãe:</b></label>
                    </td>
                    <td>${cgm.z01_mae}</td>
                    </tr>`;
                }


                divDadosParaConferencia.innerHTML = `
                    <fieldset>
                    <legend>Dados do CGM para conferência</legend>
                    <table class="form-container">
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>CPF/CGC:</b></label>
                        </td>
                        <td>${cgm.cpfCgcMask}</td>
                        <td class="field-size3 text-left">
                            <label><b>Nome/Razao Social:</b></label></td>
                        <td>${cgm.z01_nome}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>Nascimento/Abertura:</b></label>
                        </td>
                        <td>${cgm.nascimentoMask}</td>
                        <td class="field-size3 text-left">
                            <label><b>Email:</b></label>
                        </td>
                        <td>${cgm.z01_email}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>Telefone:</b></label>
                        </td>
                        <td>${cgm.z01_telef}</td>
                        <td class="field-size3 text-left">
                            <label><b>Celular:</b></label>
                        </td>
                        <td>${cgm.z01_telcel}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>CEP:</b></label></td>
                        <td>${cgm.z01_cep}</td>
                        <td class="field-size3 text-left">
                            <label><b>Pais:</b></label></td>
                        <td>${!cgm.endereco_primario ? '' : cgm.endereco_primario.endereco.local.bairro_rua.bairro.municipio.estado.pais.db70_descricao}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>Estado:</b></label>
                        </td>
                        <td>
                            ${!cgm.endereco_primario ? '' : cgm.endereco_primario.endereco.local.bairro_rua.bairro.municipio.estado.db71_descricao}
                        </td>
                        <td class="field-size3 text-left">
                            <label><b>Municipio:</b></label>
                        </td>
                        <td>${cgm.z01_munic}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>Bairro:</b></label>
                        </td>
                        <td>${cgm.z01_bairro}</td>
                        <td class="field-size3 text-left">
                            <label><b>Logradouro:</b></label>
                        </td>
                        <td>${cgm.z01_ender}</td>
                    </tr>
                    <tr>
                        <td class="field-size3 text-left">
                            <label><b>Número:</b></label>
                        </td>
                        <td>${cgm.z01_numero}</td>
                        <td class="field-size3 text-left">
                            <label><b>Complemento:</b></label>
                        </td>
                        <td>${cgm.z01_compl}</td>
                    </tr>
                    <tr>
                    <td class="field-size3 text-left">
                        <label><b>UF:</b></label>
                        </td>
                        <td>${cgm.z01_uf}</td>
                        <td class="field-size3 text-left">
                        </td>
                        <td></td>
                    </tr>
                      ${dadosFiliacao}
                    </table>
                    </fieldset>
                `;
            }

            return JSON.parse(response.data.metadados);
        });
    }

    function getEcidadeInfo() {

        if (ecidadeInfo.apiUrl != '') {
            return ecidadeInfo;
        }

        const data = new FormData();
        data.append('acao', 'info');
        return HttpClient.post('con4_ecidadeinfo.RPC.php', { body: data }).then(function (response) {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            ecidadeInfo.codigoInstituicao = response.instituicao.sequencial;
            ecidadeInfo.codigoDepartamento = response.departamento.sequencial;
            ecidadeInfo.codigoUsuario = response.usuario.sequencial;
            ecidadeInfo.apiUrl = response.url + 'v4/api/';
            return ecidadeInfo;
        });

    }

    function ajustaCamposAdicionais() {
        const oCamposAdicionais = new Object();

        function verificaExistenciaSecao(oCampo) {
            const sSecao = oCampo.getAttribute("secao");

            if (!oCamposAdicionais.hasOwnProperty(sSecao)) {
                oCamposAdicionais[sSecao] = new Object();
            }

            return oCamposAdicionais[sSecao];
        }

        function verificaExistenciaCampo(oSecao, oCampo) {
            const sCampo = oCampo.getAttribute("campo");

            if (!oSecao.hasOwnProperty(sCampo)) {
                criaCampoSecao(oSecao, sCampo, [])
            }

            return oSecao[sCampo];
        }

        function criaCampoSecao(oSecao, sCampo, valor) {
            oSecao[sCampo] = valor;
        }

        const aCamposAdicionais = [...document.getElementsByClassName("camposAdicionais")];

        aCamposAdicionais.forEach((oCampo) => {
            const oSecao = verificaExistenciaSecao(oCampo);
            criaCampoSecao(oSecao, oCampo.name, oCampo.value);
        });

        const aCamposAdicionaisGrid = [...document.getElementsByClassName("camposAdicionaisGrid")];

        for (oCampo of aCamposAdicionaisGrid) {
            if (!oCampo.value) {
                continue;
            }

            const oSecao = verificaExistenciaSecao(oCampo);
            const aCampo = verificaExistenciaCampo(oSecao, oCampo);

            aCampo.push({
                indice: oCampo.getAttribute("indice"),
                valor: oCampo.value
            });
        }

        return oCamposAdicionais;
    }

    $('btnAprovar').addEventListener('click', event => {
        switch (acaoJson){
            case 'gerarAlvara':
                checarSeExisteInscricao();
                break;
                default:
                    aprovarProcesso(getDadosFormulario());
                    break;
        }
    });

    $('btnRejeitar').addEventListener('click', event => {

        const observacao = document.getElementById("observacao_motivo").value.trim();

        if (observacao == '' || observacao == null) {
            alert("O campo observação é obrigatório para rejeição.");
            return;
        }

        const data = new FormData();
        data.append('numeroProcesso', processo.value);
        data.append('anoProcesso', ano.value);
        data.append('DB_instit', ecidadeInfo.codigoInstituicao);
        data.append('DB_coddepto', ecidadeInfo.codigoDepartamento);
        data.append('DB_id_usuario', ecidadeInfo.codigoUsuario);
        data.append('motivo', observacao);

        HttpClient.post(`${ecidadeInfo.apiUrl}patrimonial/ouvidoria/atendimento/atendimento/rejeitarProcessoOuvidoria`, {body: data}).then(response => {
            alert(response.message);
            if (response.error === true) {
                return;
            }
            parent.db_iframe_processo_externo.hide();
            parent.location.reload();
        });
    });

    $('btnVisualizarDocumentos').onclick = function() {

        if (codigosEStorage.length == 0) {
            alert("Nenhum documento encontrado para o processo.");
            return false;
        }

        if(visualizarEmOutraJanela){
            window.open(`db_visualizador_documentos.php?ids=${codigosEStorage}`);
        }else{
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_visualizador_imagens',
                `db_visualizador_documentos.php?ids=${codigosEStorage}`,
                'Visualizador de documentos',
                true
            );
        }


    };

    function checarSeExisteInscricao() {
        const inscricoes = getInscricoesElement();
        if (inscricoes.length > 0) {
            modal.setTitle("Opções");
            modal.setButtons([{
                label: "Fechar",
                onclick: () => modal.destroy(),
                disabled: false,
                type: "button",
                styles: `border:1px solid #8e8e8e;margin-right:5px;`
            }, {
                label: "Nova Inscrição",
                onclick: () => {
                    modal.destroy();
                    aprovarProcesso(getDadosFormulario());
                },
                disabled: false,
                type: "button",
                styles: `border:1px solid #8e8e8e;margin-right:5px;`
            }, {
                label: "Alterar Inscrição",
                onclick: () => alterarAlvara(getDadosFormulario()),
                disabled: false,
                type: "button",
                styles: `border:1px solid #8e8e8e;`
            }]);
            modal.show();
            modal.oDivContainer.style.cssText = `
                background: #ccc;
                width: 380px;
                margin: auto;
                margin-top: 15%;
                border-radius: 5px;
                padding:10px;
            `;
        } else {
            aprovarProcesso(getDadosFormulario());
        }
    }

    function aprovarProcesso(data) {
        HttpClient.post(
            `${ecidadeInfo.apiUrl}patrimonial/ouvidoria/atendimento/atendimento/aprovarProcessoOuvidoria`,
            {body: data}
        ).then(response => {
            alert(response.message);
            if (response.error === true) {
                return;
            }
            parent.db_iframe_processo_externo.hide();
            parent.location.reload();
        });
    }

    function alterarAlvara(data) {
        modal.destroy();
        const inscricoesElement = getInscricoesElement();
        var inscricaoSelecionada = false;
        for (const inscricao of inscricoesElement) {
            if (inscricao.checked) {
                inscricaoSelecionada = inscricao.value;
                break;
            }
        }

        if (!inscricaoSelecionada) {
            alert("Selecione uma inscrição para efetuar a alteração!");
            return;
        }
        data.append('inscricao',inscricaoSelecionada);
        aprovarProcesso(data);
    }

    function abrirInscricao(inscricao){
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_consulta_inscricao',
            `iss3_consinscr003.php?numeroDaInscricao=${inscricao}`,
            'Consulta Inscrição',
            true
        );
    }

    function getDadosFormulario(){
        const data = new FormData();
        const observacao = document.getElementById("observacao_motivo").value.trim();
        data.append('numeroProcesso', processo.value);
        data.append('anoProcesso', ano.value);
        data.append('DB_instit', ecidadeInfo.codigoInstituicao);
        data.append('DB_coddepto', ecidadeInfo.codigoDepartamento);
        data.append('DB_id_usuario', ecidadeInfo.codigoUsuario);
        data.append('camposAdicionais', JSON.stringify(ajustaCamposAdicionais()));
        data.append('observacao',observacao);
        return data;
    }
    function getInscricoesElement(){
       return document.querySelectorAll('input[name=inscricao]');
    }


</script>
</body>
</html>
