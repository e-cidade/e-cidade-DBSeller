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

//MODULO: material

use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use ECidade\Patrimonial\Material\Helpers\Material as MaterialHelper;
use ECidade\Saude\Farmacia\Repositories\TipoMovimentacaoBnafarRepository;

$clmatestoqueinimei->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("coddepto");
$clrotulo->label("m60_descr");
$clrotulo->label("m70_codmatmater");
$clrotulo->label("m71_codmatestoque");
$clrotulo->label("m80_matestoqueitem");
$clrotulo->label("cc08_sequencial");
$clrotulo->label("m80_obs");
$clrotulo->label("m78_matfabricante");
$sDisplayPontoPedido = 'none';
$sTextoPontoPedido = '';

$departamento = db_getsession("DB_coddepto");

if (isset($m70_codmatmater) && trim($m70_codmatmater) != "") {

    try {

        $oAlmoxarifado = new Almoxarifado($departamento);
        $oMaterial = new materialEstoque($m70_codmatmater);

        $sSaldoItem = "
      select quantidade_estoque -
       coalesce((select sum(coalesce(case when m81_tipo = 4 then m82_quant end, 0)) as saida
                                      from matestoqueinimei
                                               inner join matestoqueitem on m71_codlanc = m82_matestoqueitem
                                               inner join matestoque trans on m71_codmatestoque = trans.m70_codigo
                                               inner join matestoqueini on m80_codigo = m82_matestoqueini
                                               left join matestoqueinil on m80_codigo = m86_matestoqueini
                                               inner join matestoquetipo on m80_codtipo = m81_codtipo
                                      where trans.m70_codigo = codigo_matestoque
                                        and m81_codtipo = 7
                                        and m86_matestoqueini IS NULL), 0) as quantidade_estoque
from (
         SELECT (Coalesce(Sum(CASE WHEN matestoquetipo.m81_tipo = 1 THEN matestoqueinimei.m82_quant end), 0) -
                 Coalesce(Sum(CASE WHEN matestoquetipo.m81_tipo = 2 THEN m82_quant end),
                          0)) AS quantidade_estoque,
                m70_codigo    as codigo_matestoque
         FROM matestoqueini
                  INNER JOIN matestoquetipo ON m80_codtipo = m81_codtipo
                  INNER JOIN matestoqueinimei ON m82_matestoqueini = m80_codigo
                  left JOIN matestoqueinimeipm ON m82_codigo = m89_matestoqueinimei
                  INNER JOIN matestoqueitem ON m82_matestoqueitem = m71_codlanc
                  INNER JOIN matestoque ON m71_codmatestoque = m70_codigo
         WHERE m70_codmatmater = {$m70_codmatmater}
           AND m70_coddepto = {$departamento}
         group by m70_codigo) as x
    ";

        $rsSaldoItem = db_query($sSaldoItem);
        $numrows_matestoque = pg_num_rows($rsSaldoItem);

        $oMaterialAlmoxarifado = new MaterialAlmoxarifado($m70_codmatmater);
        if (ControleEstoque::itemEstaNoPontoPedido($oMaterialAlmoxarifado, $oAlmoxarifado)) {
            $sTextoPontoPedido = "O item <b>{$oMaterialAlmoxarifado->getDescricao()}</b> atingiu o seu Ponto de Pedido: <b>";
            $sTextoPontoPedido .= $oMaterialAlmoxarifado->getPontoDePedidoNoAlmoxarifado($oAlmoxarifado) . "</b>.";
            $sDisplayPontoPedido = '';
        }
    } catch (Exception $eErro) {
        $numrows_matestoque = 0;
    }
}

$coddepto = db_getsession("DB_coddepto");
$daoDeposito = new cl_db_almox();
$sqlDeposito = $daoDeposito->sql_query_file(null, 'm91_codigo', null, " m91_depto = {$coddepto}");
$rsDeposito = db_query($sqlDeposito);
if ($rsDeposito) {
    $m91_codigo = pg_fetch_array($rsDeposito)['m91_codigo'];
}
?>
<div class="container" <?= !$m91_codigo ?: 'style="display: none"' ?>>
    <fieldset>
        <legend>Aviso Importante:</legend>
        <div style="padding: 10px 30px">
            <p>O Departamento logado não é um depósito. </p>

            <button onclick="Desktop.Window.createSettingModal(CurrentWindow)">
                <i class="fa fa-cog"></i> Alterar Departamento
            </button>
        </div>
    </fieldset>
</div>
<div class="container" <?= $m91_codigo ?: 'style="display: none"' ?>>
    <form name="form1" method="post" action="">
        <table>
            <tr>
                <td>
                    <fieldset>
                        <legend><b>Saida de Materiais</b></legend>
                        <table border="0">
                            <tr>
                                <td nowrap title="<?= @$Tm70_codmatmater ?>" align="right">
                                    <?php
                                    db_ancora(@$Lm70_codmatmater, "js_pesquisam70_codmatmater(true);",
                                        ((isset($m70_codmatmater) && trim($m70_codmatmater) != "" && (isset($numrows_matestoque) && $numrows_matestoque > 0)) ? "3" : "1"));
                                    ?>
                                </td>
                                <td align="left" <?= FarmaciaHelper::utilizaIntegracaoBnafar() ? "colspan='3'" : '' ?>
                                    nowrap>
                                    <?php
                                    db_input('m70_codmatmater', 10, $Im70_codmatmater, true, "text",
                                        ((isset($m70_codmatmater) && trim($m70_codmatmater) != "" && (isset($numrows_matestoque) && $numrows_matestoque > 0)) ? "3" : "1"),
                                        "onchange='js_pesquisam70_codmatmater(false);'");
                                    db_input('m60_descr', 70, $Im60_descr, true, "text", 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap class='bordas' align='right' colspan='1'>
                                    <b>Obs.:</b>
                                </td>
                                <td <?= FarmaciaHelper::utilizaIntegracaoBnafar() ? "colspan='3'" : '' ?>>
                                    <?php
                                    db_textarea('m80_obs', 2, 109, $Im80_obs, true, 'text', $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <?php if (FarmaciaHelper::utilizaIntegracaoBnafar()): ?>
                                <tr>
                                    <td nowrap title="Tipo de Saida" class='bordas' align='right' colspan='1'>
                                        <b>Tipo:</b>
                                    </td>
                                    <td>
                                        <?php
                                        $tipoMovimentacaoRepository = new TipoMovimentacaoBnafarRepository();
                                        $tipos = $tipoMovimentacaoRepository->scopeSaida()->get();
                                        $oTipo = [];
                                        $oTipo[''] = 'Selecione...';
                                        foreach ($tipos as $tipo) {
                                            $oTipo[$tipo->getCodigo()] = $tipo->getDescricao();
                                        }
                                        db_select("cod_tipo", $oTipo, true, $db_opcao, "onchange='js_verificatipo(this);'");
                                        ?>
                                    </td>
                                </tr>
                                <tr id="unidadedoador" style="display:none;">
                                    <td nowrap title="Unidade Destino" class='bordas' align='right' colspan='1'>
                                        <?php
                                        db_ancora("<b>Unidade Destino:</b>", "js_pesquisaunidadedoador(true)", $db_opcao);
                                        ?>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('unidade', 10, null, true, 'text', $db_opcao, "onchange='js_pesquisaunidadedoador(false);'");
                                        db_input('unidadedescr', 40, null, true, 'text', 3, '');
                                        ?>
                                    </td>
                                </tr>
                                <tr id="cgmdoador" style="display:none;">
                                    <td nowrap title="CGM Destino" class='bordas' align='right' colspan='1'>
                                        <?php
                                        db_ancora("<b>CGM Destino:</b>", "js_pesquisacgmdoador(true)", $db_opcao);
                                        ?>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('cgm', 10, null, true, 'text', $db_opcao, "onchange='js_pesquisacgmdoador(false);'");
                                        db_input('descr', 40, null, true, 'text', 3, '');
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            endif;

                            if ($iTipoControleCustos > 0) {

                                echo "<tr><td style='text-align:right'>";
                                db_ancora("<b>Centro de de Custo:", 'js_adicionaCentroCusto()', 1);
                                echo "</td><td>";
                                db_input('cc08_sequencial', 10, $Icc08_sequencial, true, "text", 3);
                                db_input('cc08_descricao', 70, $Im60_descr, true, "text", 3);
                                echo "</td></tr>";

                            }
                            ?>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <fieldset>
                        <legend>
                            <b>Saldo do Material</b>
                        </legend>
                        <table cellspacing=0 cellpadding=0 width='100%' style='border:2px inset white'>
                            <tr>
                                <th class='table_header'>Código</th>
                                <th class='table_header'>Material</th>
                                <th class='table_header'>Unidade de Saida</th>
                                <th class='table_header'>Qtde Em Estoque</th>
                                <th class='table_header'>Lote</th>
                                <th class='table_header'>Retirar</th>
                                <th class='table_header' width='18px'>&nbsp;</th>
                            </tr>
                            <tbody id='dadosrequisicao'
                                   style='height:80;width:95%;overflow:scroll;overflow-x:hidden;background-color:white'>
                            <?php
                            if (isset($rsSaldoItem) && pg_num_rows($rsSaldoItem)) {

                                $dados = $oMaterial->getDados();
                                $sSqlUnidadeMaterial = "SELECT m61_descr FROM matunid WHERE m61_codmatunid = {$dados->m60_codmatunid}";

                                $rsUnidadeMaterial = pg_fetch_assoc(db_query($sSqlUnidadeMaterial));

                                while ($oSaldoMaterial = pg_fetch_object($rsSaldoItem)) {
                                    $oSaldoMaterial->quantidade_estoque = MaterialHelper::arredondarQuantidade($oSaldoMaterial->quantidade_estoque);
                                    echo "<tr style='height:1em'>";
                                    echo "  <td class='linhagrid'>";
                                    echo $oMaterial->getcodMater();
                                    echo "  </td>";
                                    echo "  <td class='linhagrid'width='40%'>";
                                    echo $dados->m60_descr;
                                    echo "  </td>";
                                    echo "  <td class='linhagrid'>";
                                    echo $rsUnidadeMaterial['m61_descr'];
                                    echo "  </td>";
                                    echo "  <td class='linhagrid' id='saldo{$m70_codmatmater}'>";
                                    echo $oSaldoMaterial->quantidade_estoque;
                                    echo "  </td>";
                                    echo "  <td class='linhagrid'>";
//              if ($oSaldoMaterial->m60_controlavalidade != 3) {
//
//                echo "<b><a href='' onclick='js_mostraLotes({$oMaterial->getcodMater()},".$departamento.");return false;'>";
//                echo "Ver Lotes</a></b>";
//
//              }
                                    echo "&nbsp;";
                                    echo "  </td>";
                                    echo "  <td class='linhagrid' style='width:6%'>";
                                    echo "   <input type='text' style='width:100%;text-align:right' class='valores' ";
                                    echo "   id='saida{$oMaterial->getcodMater()}' name='saidaMaterial' value=\"{$oSaldoMaterial->quantidade_estoque}\" onblur='js_valDev(this.value, this.id)'>";
                                    echo "  </td>";
                                    echo "</tr>";

                                }

                            } else {
                                if (isset($rsSaldoItem) && pg_num_rows($rsSaldoItem) == 0) {
                                    echo "<tr><td colspan=10>Não Existe saldo para esse Item";
                                    $db_botao = false;
                                }
                            }
                            ?>
                            <tr style='height:auto'>
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </td>
            <tr>
                <td colspan="3">
                </td>
            </tr>
            </tr>
        </table>
        <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
               type="button"
               id="db_opcao"
               value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
            <?= ($db_botao == false ? "disabled" : "") ?> onclick='return js_saidaMaterial();'>
        <input name="voltar" type="button" id="voltar" value="Voltar"
               onclick="document.location.href='mat1_matestoquesai001.php'">
    </form>
    <div
        style="text-align: left; display: <?= $sDisplayPontoPedido; ?>; background-color: #fcf8e3;border: 1px solid #fcc888;padding: 10px">
        <?= $sTextoPontoPedido; ?>
    </div>
</div>
<script>
    iTipoControle = <?=$iTipoControleCustos;?>;

    function js_pesquisam70_codmatmater(mostra) {
        qry = "&setdepart=true";
        qry += "&codigododepartamento=<?=($departamento)?>";
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matmater', 'func_matmaterdepto.php?funcao_js=parent.js_mostramatmater1|m60_codmater|m60_descr' + qry, 'Pesquisa', true);
        } else {
            if (document.form1.m70_codmatmater.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matmater', 'func_matmaterdepto.php?pesquisa_chave=' + document.form1.m70_codmatmater.value + '&funcao_js=parent.js_mostramatmater' + qry, 'Pesquisa', false);
            } else {
                document.form1.m60_descr.value = '';
            }
        }
    }

    function js_mostramatmater(chave, erro) {
        document.form1.m60_descr.value = chave;
        if (erro == true) {
            document.form1.m70_codmatmater.focus();
            document.form1.m70_codmatmater.value = '';
        } else {
            document.form1.submit();
        }
    }

    function js_mostramatmater1(chave1, chave2) {
        document.form1.m70_codmatmater.value = chave1;
        document.form1.m60_descr.value = chave2;
        db_iframe_matmater.hide();
        document.form1.submit();
    }

    function js_pesquisam82_matestoqueini(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matestoqueini', 'func_matestoqueini.php?funcao_js=parent.js_mostramatestoqueini1|m80_codigo|m80_matestoqueitem', 'Pesquisa', true);
        } else {
            if (document.form1.m82_matestoqueini.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matestoqueini', 'func_matestoqueini.php?pesquisa_chave=' + document.form1.m82_matestoqueini.value + '&funcao_js=parent.js_mostramatestoqueini', 'Pesquisa', false);
            } else {
                document.form1.m80_matestoqueitem.value = '';
            }
        }
    }

    function js_mostramatestoqueini(chave, erro) {
        document.form1.m80_matestoqueitem.value = chave;
        if (erro == true) {
            document.form1.m82_matestoqueini.focus();
            document.form1.m82_matestoqueini.value = '';
        }
    }

    function js_mostramatestoqueini1(chave1, chave2) {
        document.form1.m82_matestoqueini.value = chave1;
        document.form1.m80_matestoqueitem.value = chave2;
        db_iframe_matestoqueini.hide();
    }

    function js_pesquisam82_matestoqueitem(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matestoqueitem', 'func_matestoqueitem.php?funcao_js=parent.js_mostramatestoqueitem1|m71_codlanc|m71_codmatestoque', 'Pesquisa', true);
        } else {
            if (document.form1.m82_matestoqueitem.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matestoqueitem', 'func_matestoqueitem.php?pesquisa_chave=' + document.form1.m82_matestoqueitem.value + '&funcao_js=parent.js_mostramatestoqueitem', 'Pesquisa', false);
            } else {
                document.form1.m71_codmatestoque.value = '';
            }
        }
    }

    function js_mostramatestoqueitem(chave, erro) {
        document.form1.m71_codmatestoque.value = chave;
        if (erro == true) {
            document.form1.m82_matestoqueitem.focus();
            document.form1.m82_matestoqueitem.value = '';
        }
    }

    function js_mostramatestoqueitem1(chave1, chave2) {
        document.form1.m82_matestoqueitem.value = chave1;
        document.form1.m71_codmatestoque.value = chave2;
        db_iframe_matestoqueitem.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matestoqueinimei', 'func_matestoqueinimei.php?funcao_js=parent.js_preenchepesquisa|m82_codigo', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_matestoqueinimei.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_mostraLotes(iItem, iCodEstoque) {
        iCodItem = new Number(iItem);//código do material
        nValor = new Number($F('saida' + iCodItem));//Quantidade digitada pelo usuário
        nValorReqItem = new Number($F('saida' + iCodItem));

        if (nValor == 0) {
            alert('Informe a quantidade');
        } else {

            sUrl = 'mat4_mostraitemlotes.php?iCodMater=' + iCodItem + '&iCodDepto=' + iCodEstoque + '&nValor=' + nValor;
            sUrl += '&nValorSolicitado=' + nValorReqItem + '&updateField=saida' + iCodItem;
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lotes', sUrl, 'Lotes ', true);

        }

    }

    function js_saidaMaterial() {

        if ($F("m80_obs") == "") {

            alert("Necessário preenchimento da observação");
            return false;
        }
        aItens = js_getElementbyClass(form1, "valores");
        sJsonItens = '';
        sVirgula = '';
        for (var i = 0; i < aItens.length; i++) {

            var nValor = new Number(aItens[i].value);
            if (nValor <= 0) {

                alert('Há itens com valores inválidos.Confira.\nOperação Cancelada.');
                return false;

            }
            var iCodigoCriterioCusto = "";
            /*
             * controlamos se deve ser solicitado o centro de custo para o item.
             * iTipoControle = 2 Uso Obrigatorio.
             *                 1 uso nao obrigatorio
             *                 0 Nao usa
             */
            if (iTipoControle == 2) {

                if ($F('cc08_sequencial') == "") {

                    alert("Deve ser informado o centro de custo.");
                    $('pesquisar').disabled = false;
                    $('confirmar').disabled = false;
                    return false;

                }
                iCodigoCriterioCusto = $F('cc08_sequencial');
            } else if (iTipoControle == 1) {
                iCodigoCriterioCusto = $F('cc08_sequencial');
            }
            sJsonItens += sVirgula + '{"iCodMater":' + $('m70_codmatmater').value + ',"nQuantidade":"' + nValor + '","sObs":"'
            sJsonItens += $F('m80_obs') + '","iCriterioCustoRateio":' + iCodigoCriterioCusto + '}';
            sVirgula = ",";
        }

        let iTipoSaida = $('cod_tipo') != undefined ? $F('cod_tipo') : '';

        let sJsonTipo = '';
        if (iTipoSaida != '') {
            let cgmDestino = $F('cgm') == '' ? '""' : $F('cgm');
            sJsonTipo = `,"tipoMovimentacao":${iTipoSaida},"unidadeDestino":${$F('unidade')},"cgmDestino":${cgmDestino}`;
        }
        if (sJsonTipo == '' || confirm('Confirma saída do material?')) {

            js_divCarregando("Aguarde, efetuando saida", "msgBox");
            sJson = '{"exec":"saidaMaterial","params":[{"itens":[' + sJsonItens + ']}] ' + sJsonTipo + '}';
            var url = 'mat4_requisicaoRPC.php';
            var oAjax = new Ajax.Request(
                url,
                {
                    method: 'post',
                    parameters: 'json=' + sJson,
                    onComplete: js_saidaAtendimento
                }
            );
        }
    }

    function js_saidaAtendimento(oAjax) {

        js_removeObj("msgBox");
        var obj = JSON.parse(oAjax.responseText);
        if (obj.status == 2) {

            alert(obj.message.urlDecode());
            return false;

        } else {

            alert(obj.message.urlDecode());
            $('voltar').click();

        }
    }

    function js_adicionaCentroCusto() {

        var iOrigem = 2;
        var sUrl = 'iOrigem=' + iOrigem + '&iCodItem=' + $F('m70_codmatmater') + '&iCodigoDaLinha=' + $F('m70_codmatmater');
        sUrl += '&iCodigoDepto=<?= $departamento?>';
        if ($F('m70_codmatmater')) {

            js_OpenJanelaIframe('',
                'db_iframe_centroCusto',
                'cus4_escolhercentroCusto.php?' + sUrl,
                'Centro de Custos',
                true,
                '25',
                '1',
                (document.body.scrollWidth - 10),
                (document.body.scrollHeight - 100)
            );
        }


    }

    function js_completaCustos(iCodigo, iCriterio, iDescr) {

        $('cc08_sequencial').value = iCriterio;
        $('cc08_descricao').value = iDescr;
        db_iframe_centroCusto.hide();

    }

    function js_valDev(quantidade_retirada, campo) {

        var quantidade_estoque = new Number($(campo.replace("saida", "saldo")).innerHTML);
        var quantidade_retirada = new Number(quantidade_retirada);

        if (quantidade_retirada > quantidade_estoque) {
            alert('A quantidade de retirada não pode ser maior que a de estoque!');
            $(campo).value = '';
            $(campo).focus();
        }

    }


</script>
<?php if (FarmaciaHelper::utilizaIntegracaoBnafar()): ?>
    <script type="text/javascript">
        js_verificatipo(document.form1.cod_tipo);

        function js_verificatipo(obj) {
            const tipoMovimentacao = obj.options[obj.selectedIndex].value;

            document.getElementById("unidadedoador").style.display = "none";
            document.getElementById("cgmdoador").style.display = "none";

            if (tipoMovimentacao === '3') {
                document.getElementById("unidade").value = "<?php echo db_getsession("DB_coddepto") ?>";
                document.getElementById("cgm").value = "";
                document.getElementById("descr").value = "";
                return;
            }

            const deParaUnidade = ['7', '10', '11', '12', '14'];
            const deParaCgm = ['1', '2', '9', '11', '12', '13', '14', '15'];
            if (deParaUnidade.includes(tipoMovimentacao)) {
                document.getElementById("unidadedoador").style.display = "";
            }
            if (deParaCgm.includes(tipoMovimentacao)) {
                document.getElementById("cgmdoador").style.display = ""
            }

            // Limpa os campos de quem não está sendo exibido
            if (document.getElementById("unidadedoador").style.display == 'none' || document.getElementById("unidadedescr").value == "") {
                document.getElementById("unidade").value = "";
                document.getElementById("unidadedescr").value = "";
            }
            if (document.getElementById("cgmdoador").style.display == 'none') {
                document.getElementById("cgm").value = "";
                document.getElementById("descr").value = "";
            }

            js_ValidaBloqueioCampo(document.form1.cgm, document.form1.unidade.value != "");
            js_ValidaBloqueioCampo(document.form1.unidade, document.form1.cgm.value != "");
        }

        function js_pesquisacgmdoador(mostra) {
            if (document.form1.unidade.value.trim() != "") {
                return false;
            }
            if (mostra == true) {
                js_OpenJanelaIframe('top.corpo', 'db_iframe_cgm', 'func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
            } else {
                if (document.form1.cgm.value != '') {
                    js_OpenJanelaIframe('top.corpo', 'db_iframe_cgm', 'func_cgm.php?pesquisa_chave=' + document.form1.cgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
                } else {
                    js_ValidaBloqueioCampo(document.form1.unidade, false);
                    document.form1.descr.value = '';
                }
            }
        }

        function js_mostracgm(erro, chave) {
            document.form1.descr.value = chave;
            if (erro == true) {
                document.form1.cgm.focus();
                document.form1.cgm.value = '';
                js_ValidaBloqueioCampo(document.form1.unidade, false);
            } else {
                js_ValidaBloqueioCampo(document.form1.unidade, true);
            }
        }

        function js_mostracgm1(chave1, chave2) {
            js_ValidaBloqueioCampo(document.form1.unidade, true);
            document.form1.cgm.value = chave1;
            document.form1.descr.value = chave2;
            db_iframe_cgm.hide();
        }

        function js_pesquisaunidadedoador(mostra) {
            if (document.form1.cgm.value.trim() != "") {
                return false;
            }
            if (mostra == true) {
                js_OpenJanelaIframe('top.corpo', 'db_iframe_unidade', 'func_unidades.php?funcao_js=parent.js_mostraunidade1|sd02_i_codigo|descrdepto', 'Pesquisa', true);
            } else {
                if (document.form1.unidade.value != '') {
                    js_OpenJanelaIframe('top.corpo', 'db_iframe_unidade', 'func_unidades.php?pesquisa_chave=' + document.form1.unidade.value + '&funcao_js=parent.js_mostraunidade', 'Pesquisa', false);
                } else {
                    js_ValidaBloqueioCampo(document.form1.cgm, false);
                    document.form1.unidadedescr.value = '';
                }
            }
        }

        function js_mostraunidade(chave, erro) {
            document.form1.unidadedescr.value = chave;
            if (erro == true) {
                document.form1.unidade.focus();
                document.form1.unidade.value = '';
                js_ValidaBloqueioCampo(document.form1.cgm, false);
            } else {
                js_ValidaBloqueioCampo(document.form1.cgm, true);
            }
        }

        function js_mostraunidade1(chave1, chave2) {
            js_ValidaBloqueioCampo(document.form1.cgm, true);
            document.form1.unidade.value = chave1;
            document.form1.unidadedescr.value = chave2;
            db_iframe_unidade.hide();
        }

        function js_ValidaBloqueioCampo(oCampo, lBloqueio) {
            if (lBloqueio) {
                oCampo.readOnly = true;
                oCampo.style.background = '#DEB887';
            } else {
                oCampo.readOnly = false;
                oCampo.style.background = '#FFFFFF';
            }
        }
    </script>
<?php endif; ?>
