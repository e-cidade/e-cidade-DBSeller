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
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$instituicao = new Instituicao(db_getsession("DB_instit"));

$periodo = array(
    "0"  => " 0 - Saldos de abertura",
    "1"  => " 1 - Janeiro          ",
    "2"  => " 2 - Fevereiro (1 Bim)",
    "3"  => " 3 - Março            ",
    "4"  => " 4 - Abril     (2 Bim)",
    "5"  => " 5 - Maio             ",
    "6"  => " 6 - Junho     (3 Bim)",
    "7"  => " 7 - Julho            ",
    "8"  => " 8 - Agosto    (4 Bim)",
    "9"  => " 9 - Setembro         ",
    "10" => "10 - Outubro   (5 Bim)",
    "11" => "11 - Novembro         ",
    "12" => "12 - Dezembro  (6 Bim)",
    "13" => "13 - Inscrição de RP  "
);

?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<style>
    legend {
        font-weight: bold;
    }

    #sigfis {
        width: 850px;
    }

    #lista-gerados {
        width: 850px;
    }
</style>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc">
    <div style="margin-top: 40px;" class="container">
        <fieldset id='sigfis'>
            <legend>Selecione as opções</legend>
            <div>
                <div style="display: block; height:68px;">
                    <table>
                        <tr>
                            <td><span style="font-weight: bold;">Arquivos do :</span></td>
                            <td>
                                <?php
                                db_select("periodosigfis", $periodo, true, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold;">Utilizar Plano de Contas :</span></td>
                            <td>
                            <?php
                                db_select(
                                    "utilizaPlanoUniao",
                                    ['f'=>'Estadual / Regional', 't'=>'União / Federação'],
                                    true,
                                    2
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold;">Gerar consolidado por UG :</span></td>
                            <td>
                            <?php
                                db_select("consolidado", ['t'=>'SIM', 'f'=>'NÃO'], true, 2);
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <fieldset id='field-orcamento' class="separator">
                    <legend>Orçamento</legend>
                    <table>
                        <tr>
                            <td>
                                <input type="checkbox" id='Orgao' value='Orgao' name="Orgao" />
                            </td>
                            <td>
                                <label for="Orgao">Órgão</label>
                            </td>
                            <td>
                                <input type="checkbox" id='UnidadeOrcamentaria' value='UnidadeOrcamentaria' name="UnidadeOrcamentaria" />
                            </td>
                            <td>
                                <label for="UnidadeOrcamentaria">Unidade Orçamentária</label>
                            </td>
                            <td>
                                <input type="checkbox" id='Dotacao' value='Dotacao' name="Dotacao" />
                            </td>
                            <td>
                                <label for="Dotacao">Dotação</label>
                            </td>
                            <td>
                                <input type="checkbox" id='PrevisaoDeReceita' value='PrevisaoDeReceita' name="PrevisaoDeReceita" />
                            </td>
                            <td>
                                <label for="PrevisaoDeReceita">Previsão da Receita</label>
                            </td>
                            <td>
                                <input type="checkbox" id='Programa' value='Programa' name="Programa" />
                            </td>
                            <td>
                                <label for="Programa">Programa</label>
                            </td>
                            <td>
                                <input type="checkbox" id='AcaoOrcamentaria' value='AcaoOrcamentaria' name="AcaoOrcamentaria" />
                            </td>
                            <td>
                                <label for="AcaoOrcamentaria">Ações Orçamentárias</label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset id='field-ppa' class="separator">
                    <legend>PPA</legend>
                    <table>
                        <tr>
                            <td>
                                <input type="checkbox" id='PPAPrograma' value='PPAPrograma' name="PPAPrograma" />
                            </td>
                            <td>
                                <label for="PPAPrograma">PPA Programa</label>
                            </td>
                            <td>
                                <input type="checkbox" id='PPAProgramaIndicador' value='PPAProgramaIndicador' name="PPAProgramaIndicador" />
                            </td>
                            <td>
                                <label for="PPAProgramaIndicador">PPA Programa Indicador</label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset id='field-informes' class="separator">
                    <legend>Informes mensais</legend>
                    <table>
                        <tr>

                            <td>
                                <input type="checkbox" id='AlteracaoOrcamentariaReceita'
                                    value='AlteracaoOrcamentariaReceita'
                                    name="AlteracaoOrcamentariaReceita" />
                            </td>
                            <td>
                                <label for="AlteracaoOrcamentariaReceita">Alteração Orçamentário de Receita</label>
                            </td>

                            <td>
                                <input type="checkbox" id='PagamentoEmpenho' value='PagamentoEmpenho' name="PagamentoEmpenho" />
                            </td>
                            <td>
                                <label for="PagamentoEmpenho">Pagamento de Empenho</label>
                            </td>

                            <td>
                                <input type="checkbox" id='PagamentoRestosPagarConsignacaoRetencao' value='PagamentoRestosPagarConsignacaoRetencao' name="PagamentoRestosPagarConsignacaoRetencao" />
                            </td>
                            <td>
                                <label for="PagamentoRestosPagarConsignacaoRetencao">Restos a Pagar - Pgto. Consig e Ret</label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input type="checkbox" id='ReceitaArrecadada' value='ReceitaArrecadada' name="ReceitaArrecadada" />
                            </td>
                            <td>
                                <label for="ReceitaArrecadada">Receita Arrecada</label>
                            </td>

                            <td>
                                <input type="checkbox" id='PagamentoDeEmpenhoConsignacaoRetencao' value='PagamentoDeEmpenhoConsignacaoRetencao' name="PagamentoDeEmpenhoConsignacaoRetencao" />
                            </td>
                            <td>
                                <label for="PagamentoDeEmpenhoConsignacaoRetencao">Pagamento de Empenho - Consig e Retenção</label>
                            </td>

                            <td>
                                <input type="checkbox" id='AnulacaoPagamentoRestosPagarConsignacaoRetencao' value='AnulacaoPagamentoRestosPagarConsignacaoRetencao' name="AnulacaoPagamentoRestosPagarConsignacaoRetencao" />
                            </td>
                            <td>
                                <label for="AnulacaoPagamentoRestosPagarConsignacaoRetencao">Restos a Pagar - Anul Pgto. Consig e Ret</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='AlteracaoOrcamentariaDespesa' value='AlteracaoOrcamentariaDespesa' name="AlteracaoOrcamentariaDespesa" />
                            </td>
                            <td>
                                <label for="AlteracaoOrcamentariaDespesa">Alteração Orçamentária de Despesa</label>
                            </td>
                            <td>
                                <input type="checkbox" id='AnulacaoPagamentoDeEmpenho' value='AnulacaoPagamentoDeEmpenho' name="AnulacaoPagamentoDeEmpenho" />
                            </td>
                            <td>
                                <label for="AnulacaoPagamentoDeEmpenho">Pagamento de Empenho - Anulação</label>
                            </td>

                            <td>
                                <input type="checkbox" id='ContaBancaria' value='ContaBancaria' name="ContaBancaria" />
                            </td>
                            <td>
                                <label for="ContaBancaria">Conta Bancária</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='DescentralizacaoOrcamentaria' value='DescentralizacaoOrcamentaria' name="DescentralizacaoOrcamentaria" />
                            </td>
                            <td>
                                <label for="DescentralizacaoOrcamentaria">Descentralização Orçamentária</label>
                            </td>
                            <td>
                                <input type="checkbox" id='AnulacaoPagamentoDeEmpenhoConsignacaoRetencao' value='AnulacaoPagamentoDeEmpenhoConsignacaoRetencao' name="AnulacaoPagamentoDeEmpenhoConsignacaoRetencao" />
                            </td>
                            <td>
                                <label for="AnulacaoPagamentoDeEmpenhoConsignacaoRetencao">Pagamento de Empenho - Anulação Consig e Ret</label>
                            </td>
                            <td>
                                <input type="checkbox" id='ConciliacaoBancaria' value='ConciliacaoBancaria' name="ConciliacaoBancaria" />
                            </td>
                            <td>
                                <label for="ConciliacaoBancaria">Conciliação Bancária</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='Empenho' value='Empenho' name="Empenho" />
                            </td>
                            <td>
                                <label for="Empenho">Empenho</label>
                            </td>
                            <td>
                                <input type="checkbox" id='RPInscricao' value='RPInscricao' name="RPInscricao" />
                            </td>
                            <td>
                                <label for="RPInscricao">Restos a Pagar - Inscrição</label>
                            </td>
                            <td>
                                <input type="checkbox" id='EmpenhoAtoJuridico' value='RegularizacaoConciliacaoBancaria' name="RegularizacaoConciliacaoBancaria" />
                            </td>
                            <td>
                                <label for="RegularizacaoConciliacaoBancaria">Regularização de Conciliação Bancária</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='EmpenhoAtoJuridico' value='EmpenhoAtoJuridico' name="EmpenhoAtoJuridico" />
                            </td>
                            <td>
                                <label for="EmpenhoAtoJuridico">Empenho - Ato jurídico</label>
                            </td>
                            <td>
                                <input type="checkbox" id='RPCancelamento' value='RPCancelamento' name="RPCancelamento" />
                            </td>
                            <td>
                                <label for="RPCancelamento">Restos a Pagar - Cancelamento</label>
                            </td>
                            <td>
                                <input type="checkbox" id='Balancete' value='Balancete' name="Balancete" />
                            </td>
                            <td>
                                <label for="Balancete">Balancete</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='EmpenhoInstrumentoPrevio' value='EmpenhoInstrumentoPrevio' name="EmpenhoInstrumentoPrevio" />
                            </td>
                            <td>
                                <label for="EmpenhoInstrumentoPrevio">Empenho - Instrumento Prévio</label>
                            </td>
                            <td>
                                <input type="checkbox" id='RPLiquidacao' value='RPLiquidacao' name="RPLiquidacao" />
                            </td>
                            <td>
                                <label for="RPLiquidacao">Restos a Pagar - Liquidação</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='AnulacaoEmpenho' value='AnulacaoEmpenho' name="AnulacaoEmpenho" />
                            </td>
                            <td>
                                <label for="AnulacaoEmpenho">Anulação de Empenho</label>
                            </td>
                            <td>
                                <input type="checkbox" id='RPLiquidacaoFolhaPagamento' value='RPLiquidacaoFolhaPagamento' name="RPLiquidacaoFolhaPagamento" />
                            </td>
                            <td>
                                <label for="RPLiquidacaoFolhaPagamento">Restos a Pagar - Liquidação Folha de Pagamento</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoDeEmpenho' value='LiquidacaoDeEmpenho' name="LiquidacaoDeEmpenho" />
                            </td>
                            <td>
                                <label for="LiquidacaoDeEmpenho">Liquidação de Empenho</label>
                            </td>
                            <td>
                                <input type="checkbox" id='RPLiquidacaoAdiantamento' value='RPLiquidacaoAdiantamento' name="RPLiquidacaoAdiantamento" />
                            </td>
                            <td>
                                <label for="RPLiquidacaoAdiantamento">Restos a Pagar - Liquidação Adiantamento</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoEmpenhoFolhaPagamento' value='LiquidacaoEmpenhoFolhaPagamento' name="LiquidacaoEmpenhoFolhaPagamento" />
                            </td>
                            <td>
                                <label for="LiquidacaoEmpenhoFolhaPagamento">Liquidação de Empenho - Folha de Pagamento</label>
                            </td>
                            <td>
                                <input type="checkbox" id='LiquidacaoRestosPagarDiaria' value='LiquidacaoRestosPagarDiaria' name="LiquidacaoRestosPagarDiaria" />
                            </td>
                            <td>
                                <label for="LiquidacaoRestosPagarDiaria">Restos a Pagar - Liquidação Diária</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoEmpenhoAdiantamento' value='LiquidacaoEmpenhoAdiantamento' name="LiquidacaoEmpenhoAdiantamento" />
                            </td>
                            <td>
                                <label for="LiquidacaoEmpenhoAdiantamento">Liquidação de Empenho - Adiantamento</label>
                            </td>
                            <td>
                                <input type="checkbox" id='LiquidacaoRestosPagarNotaFiscal' value='LiquidacaoRestosPagarNotaFiscal' name="LiquidacaoRestosPagarNotaFiscal" />
                            </td>
                            <td>
                                <label for="LiquidacaoRestosPagarNotaFiscal">Restos a Pagar - Liquidação Nota Fiscal</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoEmpenhoDiaria' value='LiquidacaoEmpenhoDiaria' name="LiquidacaoEmpenhoDiaria" />
                            </td>
                            <td>
                                <label for="LiquidacaoEmpenhoDiaria">Liquidação de Empenho - Diária</label>
                            </td>
                            <td>
                                <input type="checkbox" id='LiquidacaoRestosPagarDocumentoDiverso' value='LiquidacaoRestosPagarDocumentoDiverso' name="LiquidacaoRestosPagarDocumentoDiverso" />
                            </td>
                            <td>
                                <label for="LiquidacaoRestosPagarDocumentoDiverso">Restos a Pagar - Liquidacao Documento Diverso</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoEmpenhoNotaFiscal' value='LiquidacaoEmpenhoNotaFiscal' name="LiquidacaoEmpenhoNotaFiscal" />
                            </td>
                            <td>
                                <label for="LiquidacaoEmpenhoNotaFiscal">Liquidação de Empenho - Nota Fiscal</label>
                            </td>
                            <td>
                                <input type="checkbox" id='LiquidacaoRestosPagarEstorno' value='LiquidacaoRestosPagarEstorno' name="LiquidacaoRestosPagarEstorno" />
                            </td>
                            <td>
                                <label for="LiquidacaoRestosPagarEstorno">Restos a Pagar - Estorno de liquidação</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='LiquidacaoEmpenhoDocumentoDiverso' value='LiquidacaoEmpenhoDocumentoDiverso' name="LiquidacaoEmpenhoDocumentoDiverso" />
                            </td>
                            <td>
                                <label for="LiquidacaoEmpenhoDocumentoDiverso">Liquidação de Empenho - Documento Diverso</label>
                            </td>
                            <td>
                                <input type="checkbox" id='PagamentoRestosPagar' value='PagamentoRestosPagar' name="PagamentoRestosPagar" />
                            </td>
                            <td>
                                <label for="PagamentoRestosPagar">Restos a Pagar - Pagamento</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id='AnulacaoDeLiquidacaoDeEmpenho' value='AnulacaoDeLiquidacaoDeEmpenho' name="AnulacaoDeLiquidacaoDeEmpenho" />
                            </td>
                            <td>
                                <label for="AnulacaoDeLiquidacaoDeEmpenho">Anulação de Liquidação de Empenho</label>
                            </td>
                            <td>
                                <input type="checkbox" id='PagamentoRestosPagarAnulacao' value='PagamentoRestosPagarAnulacao' name="PagamentoRestosPagarAnulacao" />
                            </td>
                            <td>
                                <label for="PagamentoRestosPagarAnulacao">Restos a Pagar - Anulação de Pagamento</label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
        </fieldset>

        <div style="margin-top: 15px;margin-bottom:10px">
            <input type="button" id='selecionar-todos' value='Selecionar Todos' name='Selecionar Todos' onclick="js_marcaTodos();" />
            <input type="button" id='limpar-selecao' value='Limpar Seleção' name='Limpar Seleção' onclick="js_desmarcar();" />
            <input type="button" id='processar' value='Processar' name='Processar' onclick="js_processar();" />
        </div>

        <div>
            <fieldset id='lista-gerados'>
                <legend>Arquivos Gerados</legend>
                <div style='overflow:auto; text-align: left;' id='retorno'></div>
            </fieldset>
        </div>
    </div>
</body>

</html>
<?php db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
); ?>

<script type="text/javascript">
    var sURL = "con4_processarsigfis.RPC.php";

    var informesMensais = new DBToogle($('field-informes'), true);
    var orcamento = new DBToogle($('field-orcamento'), true);
    var ppa = new DBToogle($('field-ppa'), true);

    function js_processar() {

        var oParam = new Object();
        oParam.exec = "processarNovoSigfis";
        oParam.iPeriodo = $F('periodosigfis');
        oParam.gerarConsolidado = ($F('consolidado')=='t'?true:false);
        oParam.utilizaPlanoUniao = $F('utilizaPlanoUniao');
        oParam.sCodigoTribunal = encodeURIComponent('<?php echo $instituicao->getCodigoTribunal() ?>');

        oParam.aArquivos = new Array();
        var aArquivos = $$("input[type='checkbox']");
        aArquivos.each(function(oCheckbox, id) {

            with(oCheckbox) {

                if (checked) {
                    oParam.aArquivos.push(oCheckbox.name);
                }
            }

        });

        if (oParam.aArquivos.length == 0) {

            alert("Selecione ao menos uma Opção.");
            return false;
        }

        for (let i = 0; i < oParam.aArquivos.length; i++) {

            if ($F('periodosigfis') == '0' &&
                ( oParam.aArquivos[i] == 'RPInscricao' || 
                  oParam.aArquivos[i] == 'ContaBancaria' ||
                  oParam.aArquivos[i] == 'ConciliacaoBancaria'
                )) {
                continue;
            } else if ($F('periodosigfis') == '0') {
                let msg = "Para a competência 0, serão exigidos apenas três arquivos: ";
                msg += "inscrição de restos a pagar, Conta Bancária e Conciliação Bancária";
                alert(msg);
                return false;
            }

            if ($F('periodosigfis') == '13' && oParam.aArquivos[i] != 'RPInscricao') {
                let msg = "Para a competência 13, só será exigido o arquivo de ";
                msg += "Inscrição de restos a pagar.";
                alert(msg);
                return false;
            }
        }

        js_divCarregando('Aguarde, Processando Arquivos', 'msgBox');

        var oAjax = new Ajax.Request(sURL, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: js_retornoProcessaSigfis
        });
    }

    function js_retornoProcessaSigfis(oAjax) {

        js_removeObj('msgBox');

        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {

            if (oRetorno.arquivosNaoProcessados.length) {
                const arquivos = oRetorno.arquivosNaoProcessados.join("\n");
                const msg = `Alguns arquivos não foram processados. Verificar o log:\n ${arquivos}`;
                alert(msg);
            }

            var sRetorno = "";
            for (var i = 0; i < oRetorno.lista.length; i++) {

                with(oRetorno.lista[i]) {

                    sRetorno += "<a  download href='db_download.php?arquivo=" + path + "'>" + name + "</a><br>";
                }
            }

            $('retorno').innerHTML = sRetorno;
        } else {

            $('retorno').innerHTML = '';
            alert(oRetorno.message.urlDecode());
            return false;
        }
    }

    function js_marcaTodos() {

        var aCheckboxes = $$('input[type=checkbox]');
        aCheckboxes.each(function(oCheckbox) {
            oCheckbox.checked = true;
        });
    }

    function js_desmarcar() {

        var aCheckboxes = $$('input[type=checkbox]');
        aCheckboxes.each(function(oCheckbox) {
            oCheckbox.checked = false;
        });
    }
</script>
