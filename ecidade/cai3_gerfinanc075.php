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

    use App\Domain\Tributario\Arrecadacao\Repositories\ConfiguracoesteftipodebitoRepository;
    use App\Domain\Tributario\Arrecadacao\Repositories\OperacoesteftipodebitoRepository;

    require_once(modification("libs/db_stdlib.php"));
    require_once(modification("libs/db_conecta.php"));
    require_once(modification("libs/db_sessoes.php"));
    require_once(modification("libs/db_usuariosonline.php"));
    require_once(modification("dbforms/db_funcoes.php"));
    require_once(modification("libs/db_app.utils.php"));

    if (!isset($oParametros)) {
        $oParametros = JSON::create()->parse(str_replace("\\", "", $_GET["parametros"]));
    }

    if (!isset($oParametros->bReciboAvulso)) {
        $cl_recibopaga = new \cl_recibopaga();

        $rResult = $cl_recibopaga->sql_record($cl_recibopaga->sql_query_file(
            null,
            "SUM(k00_valor) AS valor, k00_numcgm",
            null,
            "k00_numnov = {$oParametros->iNumnov}",
            ["k00_numcgm"]
        ));

        if ($cl_recibopaga->erro_status == "0") {
            throw new \Exception($cl_recibopaga->erro_msg);
        }

        $oRecibopaga = \db_utils::fieldsMemory($rResult, 0);

        $oParametros->sValor = $oRecibopaga->valor;
        $iCgm = $oRecibopaga->k00_numcgm;
    } else {
        $cl_recibo = new \cl_recibo();

        $rResult = $cl_recibo->sql_record($cl_recibo->sql_query_file(
            null,
            "k00_numcgm",
            null,
            "k00_numpre = {$oParametros->iNumnov}"
        ));

        if ($cl_recibo->erro_status == "0") {
            throw new \Exception($cl_recibo->erro_msg);
        }

        $oRecibo = \db_utils::fieldsMemory($rResult, 0);

        $iCgm = $oRecibo->k00_numcgm;
    }

    $configuracoesteftipodebitoRepository = new ConfiguracoesteftipodebitoRepository();
    $operacoesteftipodebitoRepository = new OperacoesteftipodebitoRepository();

    $oConfiguracoesteftipodebito = $configuracoesteftipodebitoRepository->getByTipo($oParametros->iTipoDebito);

    $nValorMinimoParcela = $oConfiguracoesteftipodebito->k196_valorminimoparcelafisica;

    $oCgm = \CgmFactory::getInstanceByCgm($iCgm);

    if ($oCgm instanceof CgmJuridico && !empty($oConfiguracoesteftipodebito->k196_valorminimoparcelajuridica)) {
        $nValorMinimoParcela = $oConfiguracoesteftipodebito->k196_valorminimoparcelajuridica;
    }

    $nValorMinimoParcela = !empty($nValorMinimoParcela) ? $nValorMinimoParcela : 0;

    $aOperacoes = $operacoesteftipodebitoRepository->getByConfig($oConfiguracoesteftipodebito->k196_sequencial);
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/classes/tributario/arrecadacao/WebsocketTEF.js"></script>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        form > fieldset {
            width: 620px;
            margin-left: 0px;
            margin-right: 0px;
        }

        legend {
            width: auto;
            margin-left: auto;
            margin-right: auto;
        }

        #gridgridTaxas {
            width: 99.5%;
        }

        #cntOperacoes {
            margin-bottom: 10px;
        }

        #enviarOperacao {
            display: block;
            margin-right: auto;
            margin-left: auto;
            margin-top: 6px;
        }

        #valorReceber, #saldoReceber, #valor {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container">
    <form name="formTipo" method="post">
        <fieldset>
            <legend><strong>TEF - Tranferência Eletrônica de Fundos</strong></legend>
            <table class="form-container">
                <tr>
                    <td title="" style="width: 70px;">
                        Valor a Receber R$:
                    </td>
                    <td>
                        <?php
                            db_input("valorReceber", 20, null, "valorReceber", "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="" style="width: 70px;">
                        Saldo a Receber R$:
                    </td>
                    <td>
                        <?php
                            db_input("saldoReceber", 20, null, "saldoReceber", "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset style="margin-top: 4px">
                            <legend><strong>Dados da Operação</strong></legend>
                            <table class="form-container">
                                <tr>
                                    <td title="" style="width: 70px;">
                                        Operação:
                                    </td>
                                    <td>
                                        <select id="operacao">
                                            <option value=""></option>
                                            <?php foreach ($aOperacoes as $oOperacao) : ?>
                                                <option value="<?= $oOperacao->k195_codigoperacao ?>" sequencial="<?= $oOperacao->k195_sequencial ?>"><?= $oOperacao->k195_descricao ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="" style="width: 70px;">
                                        Valor:
                                    </td>
                                    <td>
                                        <?php
                                            db_input("valor", 20, null, "valor", "text", 1);
                                        ?>
                                    </td>
                                </tr>
                                <tr id="trParcelas" style="display: none">
                                    <td title="" style="width: 70px;">
                                        Parcelas:
                                    </td>
                                    <td>
                                        <select id="parcelas"></select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input id="enviarOperacao" type="button" onclick="js_validaCampos();" value="Enviar">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>
        <div id="cntOperacoes"></div>
        <input id="processarPagamento" type="button" onclick="js_processarPagamento();" value="Processar Pagamento" disabled>
        <input id="desfazerTransacao" type="button" onclick="js_desfazerTransacao();" value="Desfazer Transação" disabled>
    </form>
</div>
<?php db_menu(); ?>
</body>
</html>
<script>
    const iNumnov = <?= $oParametros->iNumnov ?>;
    const sValor = <?= $oParametros->sValor ?>;
    const sUrlGeraRecibo = "<?= $oParametros->sUrlGeraRecibo ?>";
    const idAutenticadora = <?= $oParametros->idAutenticadora ?>;
    const sApiUrl =  "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
    const iCodigoInstituicao = "<?= db_getsession('DB_instit') ?>";
    const iCodigoDepartamento = "<?= db_getsession('DB_coddepto') ?>";
    const iCodigoUsuario = "<?= db_getsession('DB_id_usuario') ?>";
    const sDataUsu = "<?= db_getsession('DB_datausu') ?>";
    const iModulo = "<?= db_getsession('DB_modulo') ?>";
    const iMenuAcessado = "<?= db_getsession('DB_itemmenu_acessado') ?>";
    const iConta = "<?= $oParametros->iContaAutenticadora ?>";
    const aOperacoes = [];
    const iQuantidadeMaximaParcelas = <?= !empty($oConfiguracoesteftipodebito->k196_maximoparcelas) ? $oConfiguracoesteftipodebito->k196_maximoparcelas : 0 ?>;
    const nValorMinimoParcela = <?= $nValorMinimoParcela ?>;
    const aKeyCodeNumeros = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 8];
    let iGrupo = 1;

    var oGridTaxas = new DBGrid('gridTaxas');
    var aHeaders   = ["Ordem", "Operação", "Valor (R$)", "Parcelas", "Status"];
    var aCellWidth = ["10%", "40%", "20%", "15%", "15%"];
    var aCellAlign = ["center", "left", "right", "center", "center"];

    oGridTaxas.nameInstance = 'oGridOperacoes';
    oGridTaxas.setCellWidth(aCellWidth);
    oGridTaxas.setCellAlign(aCellAlign);
    oGridTaxas.setHeader(aHeaders);
    oGridTaxas.show($('cntOperacoes'));

    const oWebsocketTEF = new WebsocketTEF("<?= db_getsession("DB_ip") ?>");
    // oWebsocketTEF.simulaPinpad();

    function js_emitirBoleto() {
        const oJanela = window.open(
            sUrlGeraRecibo,
            'reciboweb2',
            `width=${(screen.availWidth - 5)},height=${(screen.availHeight - 40)},scrollbars=1,location=0`
        );

        oJanela.moveTo(0, 0);
    }

    async function js_enviarOperacao() {
        js_ajustaLoading("Enviando Transação");
        const chave = js_ajustaDadosOperacao();
        const oOperacao = aOperacoes[chave];

        await js_salvarOperacao(oOperacao, "incluir-operacao");

        const oRetorno = await oWebsocketTEF.envio(
            oOperacao.numeroTransacao,
            oOperacao.operacao.codigo,
            oOperacao.valorMostra,
            oOperacao.parcelas
        );

        oOperacao.resposta = oRetorno;
        oOperacao.status.descricao = oRetorno.status;
        if (oRetorno.erro) {
            oOperacao.status.erro = oRetorno.mensagemErro;
            oOperacao.valor = 0;
        } else {
            oOperacao.confirmadoautorizadora = 1;
        }

        await js_salvarOperacao(oOperacao, "alterar-operacao");

        js_carregaGrid();

        js_calculaSaldoReceber("0,00", document.getElementById("valor"));

        const saldoReceber = document.getElementById("saldoReceber").value;
        document.getElementById("valor").value = saldoReceber;

        js_calculaSaldoReceber(saldoReceber, document.getElementById("valor"));

        if (js_validaValorTransacoes()) {
            $("processarPagamento").enable();
        }

        if (js_validaTransacoesSucesso()) {
            $("desfazerTransacao").enable();
        }

        js_ajustaLoading();
    }

    function js_salvarOperacao(oOperacao, sRota) {
        const data = new FormData();
        data.append('sequencial', oOperacao.sequencial ? oOperacao.sequencial : "");
        data.append('numnov', iNumnov);
        data.append('valor', jsRemoveMascaraMoeda(oOperacao.valorMostra));
        data.append('operacaotef', oOperacao.operacao.sequencial);
        data.append('parcela', oOperacao.parcelas);
        data.append('confirmado', oOperacao.confirmado ? oOperacao.confirmado : 0);
        data.append('mensagemretorno', oOperacao.status.erro);
        data.append('desfeito', oOperacao.desfeito ? oOperacao.desfeito : 0);
        data.append('grupo', iGrupo);
        data.append('terminal', idAutenticadora);
        data.append('confirmadoautorizadora', oOperacao.confirmadoautorizadora ? oOperacao.confirmadoautorizadora : 0);

        if (oOperacao.resposta) {
            data.append('nsu', oOperacao.resposta.nsuCTF ? oOperacao.resposta.nsuCTF : "");
            data.append('bandeira', oOperacao.resposta.bandeira ? oOperacao.resposta.bandeira : "");
            data.append('codigoaprovacao', oOperacao.resposta.codigoAprovacao ? oOperacao.resposta.codigoAprovacao : "");
            data.append('nsuautorizadora', oOperacao.resposta.nsuAutorizadora ? oOperacao.resposta.nsuAutorizadora : "");
            data.append('cartao', oOperacao.resposta.cartao ? oOperacao.resposta.cartao : "");
            data.append('retorno', oOperacao.resposta.response ? JSON.stringify(oOperacao.resposta.response) : "");
        }

        data.append('DB_instit', iCodigoInstituicao);
        data.append('DB_coddepto', iCodigoDepartamento);
        data.append('DB_id_usuario', iCodigoUsuario);
        data.append('DB_datausu', sDataUsu);

        return new Promise((resolve) => {
            HttpClient.post(`${sApiUrl}tributario/arrecadacao/tef/${sRota}`, {body: data}).then(response => {
                if (response.error) {
                    alert(`${response.message}. Desfaça a transação e tente novamente`);
                    return false;
                }

                oOperacao.sequencial = response.data.sequencial;

                resolve(true);
            });
        });
    }

    async function js_processarPagamento() {
        if (!js_validaValorTransacoes()) {
            alert("O valor do(s) débito(s) não foram totalmente quitados!");
            return;
        }

        if (confirm("Realmente deseja processar o pagamento? Essa ação irá confirmar todas as transações e baixar o débito!")) {
            $("processarPagamento").disable();
            $("desfazerTransacao").disable();

            const data = new FormData();
            data.append('DB_instit', iCodigoInstituicao);
            data.append('DB_coddepto', iCodigoDepartamento);
            data.append('DB_id_usuario', iCodigoUsuario);
            data.append('DB_datausu', sDataUsu);
            data.append('numpre', iNumnov);
            data.append('valor', jsRemoveMascaraMoeda(document.getElementById("valorReceber").value));
            data.append('conta', iConta);

            HttpClient.post(`${sApiUrl}tributario/arrecadacao/tef/baixar-debito`, {body: data}).then(async (response) => {
                alert(response.message);

                if (response.error) {
                    $("processarPagamento").enable();
                    return;
                }

                for (const oOperacao of aOperacoes) {
                    if (oOperacao.confirmado || oOperacao.desfeito || oOperacao.resposta.erro) {
                        continue;
                    }

                    const oRetorno = await js_confirmarTransacao(oOperacao);

                    if (oRetorno.erro) {
                        alert(oRetorno.mensagem);
                        bExecutaBaixa = false;
                        break;
                    } else {
                        oOperacao.confirmado = 1;
                        await js_salvarOperacao(oOperacao, "confirmar-operacao");
                    }
                }

                js_ajustaLoading();

                js_emitirBoleto();

                <?php if (!isset($oParametros->bReciboAvulso)) : ?>
                    parent.document.formatu.pesquisar.click()
                <?php else : ?>
                    document.location.href = "cai4_recibo001.php";
                <?php endif; ?>

            }).catch(error => {
                $("processarPagamento").enable();
                alert(error.message);
            });
        }
    }

    async function js_confirmarTransacao(oOperacao, iTentativa = 1) {
        js_ajustaLoading();
        js_ajustaLoading(`Confirmando operação ${oOperacao.ordem}. Tentativa: ${iTentativa}`);
        const oRetorno = await oWebsocketTEF.confirmacao(oOperacao.numeroTransacao);

        if (oRetorno.erro) {
            if (iTentativa <= 5) {
                return await js_confirmarTransacao(oOperacao, iTentativa + 1);
            }
        }

        return oRetorno;
    }

    function js_ajustaLoading(sMensagem = null) {
        if (sMensagem) {
            js_divCarregando(sMensagem, 'loading_websocket');
        } else {
            js_removeObj('loading_websocket');
        }
    }

    function js_validaValorTransacoes() {
        const valorReceber = jsRemoveMascaraMoeda(document.getElementById("valorReceber").value);

        let valorOperacoes = 0;

        aOperacoes.forEach((oOperacao) => {
            valorOperacoes += Number(oOperacao.valor);
        });

        if (valorOperacoes < valorReceber) {
            return false;
        }

        return true;
    }

    function js_validaTransacoesSucesso() {
        let iQuantidadeOperacoesSucesso = 0;

        aOperacoes.forEach((oOperacao) => {
            if (!oOperacao.status.erro) {
                iQuantidadeOperacoesSucesso++;
            }
        });

        return iQuantidadeOperacoesSucesso;
    }

    async function js_desfazerTransacao() {
        if (confirm("Realmente deseja desfazer a transação? Essa ação irá desfazer todas as transações executadas até o momento!")) {
            let erro = false;

            try {
                for (const oOperacao of aOperacoes) {
                    if (oOperacao.confirmado || oOperacao.desfeito || oOperacao.resposta.erro) {
                        continue;
                    }

                    if (!await js_desfazerTrans(oOperacao)) {
                        alert(`Erro ao desfazer a transação ${oOperacao.ordem}. Tente novamente mais tarde.`);
                        erro = true;
                        break;
                    } else {
                        oOperacao.status.descricao = "Desfeito";
                        oOperacao.status.erro = "Desfeito";
                        oOperacao.valor = 0;
                        oOperacao.desfeito = 1;
                        erro = !await js_desfazerOperacao(oOperacao, "desfazer-operacao");
                    }
                }

                js_ajustaLoading();

                js_carregaGrid();

                js_calculaSaldoReceber("0,00", document.getElementById("valor"));

                const saldoReceber = document.getElementById("saldoReceber").value;
                document.getElementById("valor").value = saldoReceber;

                js_calculaSaldoReceber(saldoReceber, document.getElementById("valor"));

                $("processarPagamento").disable();
                $("desfazerTransacao").disable();
            } catch (error) {
                alert(error.message);
                erro = true;
            } finally {
                if (!erro) {
                    js_comprovanteDesfazimento();

                    iGrupo++;
                }
            }
        }
    }

    function js_desfazerOperacao(oOperacao, sRota) {
        return new Promise((res) => {
            js_salvarOperacao(oOperacao, sRota).then((resp) => {
                res(resp);
            });
        });
    }

    async function js_desfazerTrans(oOperacao, iTentativa = 1) {
        js_ajustaLoading();
        js_ajustaLoading(`Desfazendo operação ${oOperacao.ordem}. Tentativa: ${iTentativa}`);
        const oRetorno = await oWebsocketTEF.desfazimento(oOperacao.numeroTransacao);

        if (oRetorno.erro) {
            if (iTentativa <= 5) {
                return await js_desfazerTrans(oOperacao, iTentativa + 1);
            } else {
                return false;
            }
        }

        return true;
    }

    function js_calculaSaldoReceber(sValor, oCampo) {
        let valorOperacoesAnteriores = 0;

        aOperacoes.forEach((oOperacao) => {
            valorOperacoesAnteriores += Number(oOperacao.valor);
        });

        let valorReceber = document.getElementById("valorReceber").value;
        valorReceber = jsRemoveMascaraMoeda(valorReceber);

        const valorOperacao = jsRemoveMascaraMoeda(sValor);

        let valorSaldoReceber = valorReceber - (valorOperacao + valorOperacoesAnteriores);

        const saldoReceber = document.getElementById("saldoReceber");

        if (valorSaldoReceber < 0) {
            alert("O valor não pode ser maior que o saldo a receber!");

            valorSaldoReceber = (valorReceber - valorOperacoesAnteriores);

            oCampo.value = "";
        }

        saldoReceber.value = valorSaldoReceber.toLocaleString('pt-BR', {minimumFractionDigits: 2});

        js_carregaParcelas();
    }

    function js_carregaParcelas() {
        const parcelas = document.getElementById("parcelas");
        parcelas.innerHTML = "";

        let valor = document.getElementById("valor").value;
        valor = jsRemoveMascaraMoeda(valor);

        let quantidadeParcelas = Math.trunc(valor / nValorMinimoParcela);

        if (quantidadeParcelas > iQuantidadeMaximaParcelas) {
            quantidadeParcelas = iQuantidadeMaximaParcelas;
        }

        const optionVazio = document.createElement("option");
        optionVazio.setAttribute("value", "");
        parcelas.appendChild(optionVazio);

        for (let i = 1; i <= quantidadeParcelas; i++) {
            const option = document.createElement("option");
            option.setAttribute("value", i);
            option.innerHTML = i;
            parcelas.appendChild(option);
        }
    }

    function js_carregaGrid() {
        oGridTaxas.clearAll(true);

        aOperacoes.forEach((oOperacao) => {
            let aLinha = [];

            aLinha.push(oOperacao.ordem);
            aLinha.push(oOperacao.operacao.descricao);
            aLinha.push(oOperacao.valorMostra);
            aLinha.push(oOperacao.parcelas);

            const spanErro = document.createElement("span");
            let sErro = oOperacao.status.descricao;

            if (oOperacao.status.erro) {
                const i = document.createElement("i");
                i.setAttribute("class", "fas fa-info-circle");
                sErro += " "+i.outerHTML;

                spanErro.setAttribute("style", "color: red");
                spanErro.setAttribute("title", oOperacao.status.erro);
            }

            spanErro.innerHTML = sErro;

            aLinha.push(spanErro.outerHTML);

            oGridTaxas.addRow(aLinha);
        });

        oGridTaxas.renderRows();
    }

    function js_validaCampos() {
        const operacao = document.getElementById("operacao").value;
        const valor = document.getElementById("valor").value;
        const parcelas = document.getElementById("parcelas").value;

        if (!operacao) {
            alert("Selecione uma operação.");
            return;
        }

        if (!valor) {
            alert("Informe o valor.");
            return;
        }

        if (operacao == 113 && !parcelas) {
            alert("Selecione a quantidade de parcelas.");
            return;
        }

        js_enviarOperacao();
    }

    function js_ajustaDadosOperacao() {
        const operacao = document.getElementById("operacao");
        const oOperacao = [...operacao.children].find(option => option.value == operacao.value);
        let valor = document.getElementById("valor");
        const parcelas = document.getElementById("parcelas").value;

        const lenght = aOperacoes.push({
            ordem: (aOperacoes.length + 1),
            numeroTransacao: (js_validaTransacoesSucesso() + 1),
            operacao: {
                sequencial: oOperacao.getAttribute("sequencial"),
                codigo: operacao.value,
                descricao: oOperacao.innerText
            },
            valor: jsRemoveMascaraMoeda(valor.value),
            valorMostra: valor.value,
            parcelas: (operacao.value == 113 ? parcelas : ""),
            status: {
                descricao: "Enviando..",
                erro: ""
            },
            confirmado: false
        });

        operacao.value = "";
        valor.value = "";
        $("trParcelas").hide();
        js_carregaGrid();

        return (lenght - 1);
    }

    document.getElementById("valor").addEventListener("keyup", (oEvent) => {
        jsFormataMoeda(oEvent.target, (sValor, oCampo) => {
            if (aKeyCodeNumeros.includes(oEvent.keyCode)) {
                js_calculaSaldoReceber(sValor, oCampo);
            }
        });
    });

    document.getElementById('valor').addEventListener("paste", (oEvent) => {
        oEvent.preventDefault();
        return false;
    });

    document.getElementById('valor').addEventListener("click", js_ajustaCursor);
    document.getElementById('valor').addEventListener("focus", js_ajustaCursor);

    function js_ajustaCursor(oEvent) {
        oEvent.preventDefault();
        oEvent.target.selectionStart = oEvent.target.selectionEnd = oEvent.target.value.length;
    }

    document.getElementById("operacao").addEventListener("change", (oEvent) => {
        if (oEvent.target.value == 113) {
            $("trParcelas").show();
        } else {
            $("trParcelas").hide();
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        const sValorFormatado = sValor.toLocaleString('pt-BR', {minimumFractionDigits: 2});

        document.getElementById("valorReceber").value = sValorFormatado;
        document.getElementById("saldoReceber").value = Number(0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        document.getElementById('valor').value = sValorFormatado;
        document.getElementById("db-tooltip").innerHTML = "";
        js_carregaParcelas();
    });

    function js_comprovanteDesfazimento() {
        const data = new FormData();
        data.append('numnov', iNumnov);
        data.append('grupo', iGrupo);
        data.append('DB_modulo', iModulo);
        data.append('DB_itemmenu_acessado', iMenuAcessado);

        HttpClient.post(`${sApiUrl}tributario/arrecadacao/tef/comprovante-desfazimento`, {body: data}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            window.open(
                response.data.arquivo,
                'comprovanteDesfazimento',
                `width=${(screen.availWidth - 5)},height=${(screen.availHeight - 40)},scrollbars=1,location=0`
            );
        }).catch(error => {
            alert(error.message);
        });
    }
</script>
