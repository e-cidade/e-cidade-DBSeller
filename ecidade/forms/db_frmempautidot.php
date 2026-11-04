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

use ECidade\Financeiro\Orcamento\Repository\RecursoRepository;
use ECidade\Financeiro\Orcamento\Recurso\Complemento;

//MODULO: empenho
$clempautidot->rotulo->label();
$clrotulo = new rotulocampo;
$clorcsuplemval->rotulo->label();
$clorcdotacao->rotulo->label();
$clrotulo->label("o58_orgao");
$clrotulo->label("o46_codlei");
$clrotulo->label("o47_anousu");
$clrotulo->label("c53_descr");
$clrotulo->label("e54_valor");
$clrotulo->label("o56_elemento");
$clrotulo->label("o15_recurso");
$clrotulo->label("gestao");

$anoSessao = db_getsession("DB_anousu");
$dataLimite = date('Y-m-d', db_getsession('DB_datausu'));
$result = db_query($clempautitem->sql_query_file($e56_autori));
if (pg_num_rows($result) == 0) {
    $db_opcao_item = 33;
    $db_botao = false;
} else {
    $db_opcao_item = 1;

    if (isset($o47_coddot) && $o47_coddot != "") {
        $suspensaoOrcamentaria = new cl_suspensaoorcamentaria;
        if ($suspensaoOrcamentaria->verificaSuspensaoEmpenhoDotacao($o47_coddot, $anoSessao) == true) {
            db_msgbox("Ficha financeira suspensa para empenhos.");
            unset($o47_coddot);
        }
    }
}

$daoAndamentoAutorizacao = new cl_andamentoemppreautorizacao;
if (!empty($e56_autori) &&
    $daoAndamentoAutorizacao->travaAutorizacaoAndamento($e56_autori)
) {
    $db_botao =false;
    $db_botao_c =false;
    $msg = "2 - Não é possivel alterar a autorização no andamento em que ele está.";
    $msg .= " Verique o status da autorização";
    db_msgbox($msg);
}

?>
<form name="form1" method="post" action="">

    <table class="form-container">
        <tr>
            <td nowrap title="<?php echo @$Te56_autori ?>">
                <?php echo @$Le56_autori ?>
            </td>
            <td colspan='2'>
                <?php
                db_input('e56_autori', 8, $Ie56_autori, true, 'text', 3);
                $sql1 = $clorcreservaaut->sql_query_file(null, "o83_codres", "", "o83_autori=$e56_autori");
                $result = $clorcreservaaut->sql_record($sql1);
                if ($clorcreservaaut->numrows > 0) {
                    echo "<b>Já existe reserva para esta autorização</b>";
                } else {
                    echo "<b>Não existe reserva para esta autorização</b>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$Te56_anousu ?>">
                <?php echo $Le56_anousu ?>
            </td>
            <td>
                <?php
                if (empty($e56_anousu)) {
                    $e56_anousu = db_getsession('DB_anousu');
                }
                db_input('e56_anousu', 8, $Ie56_anousu, true, 'text', 3);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$To47_coddot ?>">
                <?php db_ancora(@$Lo47_coddot, "js_pesquisao47_coddot(true);", $db_opcao_item); ?> </td>
            <td><?php db_input('o47_coddot', 8, $Io47_coddot, true, 'text', 3); ?> </td>
            <td><input type="button" name="dot" value="Processar"
                       onclick="js_dot();"<?php echo ($db_botao == false ? "disabled" : "") ?> ></td>
            <td> &nbsp;</td>
            <td> &nbsp;</td>
            <td> &nbsp;</td>
        </tr>

        <?php /* busca dados da dotação  */

        if ((isset($o47_coddot) && !$o47_coddot == "") && empty($confirmar) && empty($cancelar)) {
            $instit = db_getsession('DB_instit');
            $where = "o58_coddot = {$o47_coddot} and o58_instit = {$instit} and o58_anousu = {$anoSessao}";
            $sql = $clorcdotacao->sql_recurso("", "", "*", "", $where);
            $result1 = $clorcdotacao->sql_record($sql);
            db_fieldsmemory($result1, 0);
            if ($clorcdotacao->numrows > 0) {
                $result = db_dotacaosaldo(8, 2, 2, "true", "o58_coddot=$o47_coddot", $anoSessao);

                db_fieldsmemory($result, 0);
                $atual = number_format(floatval($atual), 2, ",", ".");
                $reservado = number_format(floatval($reservado), 2, ",", ".");
                $atudo = number_format(floatval($atual_menos_reservado), 2, ",", ".");
            } else {
                $nops = " Dotação $o47_coddot  não encontrada ";
            }
        }
        ?>
        <tr>
            <td nowrap title="<?php echo @$To58_orgao ?>"><?php echo @$Lo58_orgao ?> </td>
            <td><?php db_input('o58_orgao', 8, "$Io58_orgao", true, 'text', 3, ""); ?> </td>
            <td style="text-align: left;"><?php db_input('o40_descr', 50, "", true, 'text', 3, ""); ?> </td>

        </tr>
        <tr>
            <td nowrap title="<?php echo @$To58_unidade ?>"><?php echo @$Lo58_unidade ?> </td>
            <td><?php db_input('o58_unidade', 8, "", true, 'text', 3, ""); ?> </td>
            <td style="text-align: left;"><?php db_input('o41_descr', 50, "", true, 'text', 3, ""); ?>  </td>

        </tr>
        <tr>
            <td nowrap title="<?php echo @$To58_funcao ?>"><?php echo @$Lo58_funcao ?> </td>
            <td> <?php db_input('o58_funcao', 8, "", true, 'text', 3, ""); ?> </td>
            <td style="text-align: left;"> <?php db_input('o52_descr', 50, "", true, 'text', 3, ""); ?>  </td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$To58_subfuncao ?>"><?php echo @$Lo58_subfuncao ?> </td>
            <td> <?php db_input('o58_subfuncao', 8, "", true, 'text', 3, ""); ?>  </td>
            <td style="text-align: left;"><?php db_input('o53_descr', 50, "", true, 'text', 3, ""); ?></td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$To58_programa ?>"><?php echo @$Lo58_programa ?> </td>
            <td><?php db_input('o58_programa', 8, "", true, 'text', 3, ""); ?> </td>
            <td style="text-align: left;"><?php db_input('o54_descr', 50, "", true, 'text', 3, ""); ?></td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$To58_projativ ?>"><?php echo @$Lo58_projativ ?> </td>
            <td><?php db_input('o58_projativ', 8, "", true, 'text', 3, ""); ?></td>
            <td style="text-align: left;"><?php db_input('o55_descr', 50, "", true, 'text', 3, ""); ?></td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$To56_elemento ?>"><?php echo @$Lo56_elemento ?> </td>
            <td> <?php db_input('o58_elemento', 8, "", true, 'text', 3, ""); ?></td>
            <td style="text-align: left;"> <?php db_input('o56_descr', 50, "", true, 'text', 3, ""); ?></td>
        </tr>
        <tr>
            <td nowrap title="<?php echo @$Tgestao ?>"><?php echo @$Lgestao ?></td>
            <td> <?php db_input('gestao', 8, "", true, 'text', 3, ""); ?> </td>
            <td style="text-align: left;"> <?php db_input('descricao', 50, "", true, 'text', 3, ""); ?> </td>
        </tr>

        <tr>
            <td nowrap class="bold">Complemento:</td>
            <td colspan="2">

            <?php
              $complemento = "";
              $compls = [];
            if (!empty($o47_coddot)) {
                $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($o47_coddot, $anoSessao);
                $recurso = $dotacao->getDadosRecurso();
                $complemento = $recurso->getComplemento();

                $complementosDisponiveis = RecursoRepository::getComplementosByGestao(
                    $gestao,
                    $recurso->getRecurso(),
                    $e56_anousu,
                    $dataLimite
                );

                foreach ($complementosDisponiveis as $complementos) {
                    $compls[$complementos->codigo] = $complementos->descricao;
                }
            }
              db_select('complemento', $compls, true, 1, 'style="width: 450px;"')
            ?>
            </td>
        </tr>
        <tr>
            <?php
            if (isset($o47_coddot)) {
                $rsDotacao = $clorcdotacao->sql_record($clorcdotacao->sql_query($anoSessao, $o47_coddot, "o15_tipo"));
                if ($clorcdotacao->numrows > 0) {
                    $oDotacao = db_utils::fieldsMemory($rsDotacao, 0);

                    if ($oDotacao->o15_tipo == 1) {
                        /*
                         * Buscamos as contrapartidas da dotacao
                         */
                        $oDaoDotacaocontr = new cl_orcdotacaocontr;
                        $oDaoTipoRec = new cl_orctiporec;
                        echo "<td><b>Contrapartida:</b></td>
                          <td >";
                        /*
                         * Procuramos contrapartidas cadastradas que estão ativas para a dotacao, caso nao encontramos nenhuma,
                         * trazemos todos os recursos cadastrados.
                         */
                        $rsContrapartidas = $oDaoDotacaocontr->sql_record(
                            $oDaoDotacaocontr->sql_query_convenios(
                                $o47_coddot,
                                $anoSessao,
                                date("Y-m-d", db_getsession("DB_datausu")),
                                null,
                                "o15_codigo,o15_descr"
                            )
                        );

                        $iNumRows = $oDaoDotacaocontr->numrows;
                        if ($oDaoDotacaocontr->numrows == 0) {

                            $sql = $oDaoTipoRec->sql_query_conveniosGestao(
                                date("Y-m-d", db_getsession("DB_datausu")),
                                null,
                                "o15_codigo, o15_descr, gestao, o15_recurso",
                                "gestao, o15_descr, o15_codigo"
                            );
                            $rsContrapartidas = $oDaoTipoRec->sql_record($sql);
                            $iNumRows = $oDaoTipoRec->numrows;

                            $aGestao = [];
                            $aGestao["0"] = "0";
                            for ($i = 0; $i < $oDaoTipoRec->numrows; $i++) {

                                $dados = db_utils::fieldsMemory($rsContrapartidas, $i);
                                $aGestao[$dados->o15_codigo] = $dados->gestao;
                            }

                            $aRecurso = [];
                            $aRecurso["0"] = "0";
                            for ($i = 0; $i < $oDaoTipoRec->numrows; $i++) {

                                $dados = db_utils::fieldsMemory($rsContrapartidas, $i);
                                $aRecurso[$dados->o15_codigo] = $dados->o15_recurso;
                            }
                        }
                        db_select("gestaoRecurso", $aGestao, true, 1, "style='width:85px' onchange='js_ajustaContrapartida(this.value)' ");
                        echo "</td> <td >";

                        db_select("recurso_o15_recurso", $aRecurso, true, 1, "style='width:85px' onchange='js_ajustaContrapartida(this.value)' ");

                        db_selectrecord("e56_orctiporec", $rsContrapartidas, true, $db_opcao, 'style="width: 450px;"', "", "", "0-Selecione");
                    }
                }
                echo "</td>";
            }

            $planosOrcamentarios = array("" => "Selecione");
            if (!empty($o47_coddot)) {
                $daoOrcDotacaPlanoOrcamento = new cl_orcdotacaoplanoorcamentario();
                $where = "o155_coddot = {$o47_coddot} and o155_anousu = " . $anoSessao;
                $sqlPlanos = $daoOrcDotacaPlanoOrcamento->sql_query_file(null, "*", "o155_sequencial", $where);
                $rsPlanos = db_query($sqlPlanos);
                if ($rsPlanos) {
                    db_utils::makeCollectionFromRecord($rsPlanos, function ($dados) use (&$planosOrcamentarios) {
                        $planosOrcamentarios[$dados->o155_sequencial] = $dados->o155_titulo;
                    });
                }
            }
            ?>
        </tr>
        <tr style="<?php echo $mostrarLinhaPacto; ?>">
            <td>
                <b>Plano Orçamentário:</b>
            </td>
            <td colspan="2">
                <?php
                db_select("planoorcamento", $planosOrcamentarios, true, $db_opcao, "onchange='js_pesquisaLinhaPactos(this.value);' style='width:100%'");
                ?>
            </td>
        </tr>
        <tr style="<?php echo $mostrarLinhaPacto; ?>">
            <td>
                <b>Linha de Pacto:</b>
            </td>
            <td colspan="2">
                <select id="e56_planoorcamentariolinhapacto" name="e56_planoorcamentariolinhapacto"
                        style="width: 100%">
                </select>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan='2'>
                <table>
                    <tr>
                        <td>Saldo da dotação:</td>
                        <td><?php db_input('atual', 13, "", true, 'text', 3, ""); ?></td>
                    </tr>
                    <td>Valor reservado:</td>
                    <td>  <?php db_input('reservado', 13, "", true, 'text', 3, ""); ?></td>
                    </tr>
                    <tr>
                        <td>Valor disponível:</td>
                        <td><?php db_input('atudo', 13, "", true, 'text', 3, ""); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $RLe54_valor ?></td>
                        <?php
                        $result = $clempautitem->sql_record($clempautitem->sql_query_file($e56_autori, null, "sum(e55_vltot) as e54_valor"));
                        db_fieldsmemory($result, 0);
                        if (isset($atual_menos_reservado) && isset($e54_valor)) {
                            $tot = number_format((floatval($atual_menos_reservado) - floatval($e54_valor)), 2, ",", ".");
                        }
                        $e54_valor = number_format(floatval($e54_valor), 2, ",", ".");

                        ?>
                        <td><?php db_input('e54_valor', 13, "", true, 'text', 3, ""); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <input name="confirmar" type="submit" id="db_opcao" value="<?php echo ($db_botao_c == false ? "Incluir" : "Atualizar") ?>"
           onclick="return js_verifica();" <?php echo ($db_botao == false ? "disabled" : "") ?> >
    <input name="cancelar" type="submit" id="db_opcao"
           value="Cancelar" <?php echo ($db_botao_c == false ? "disabled" : "") ?> >
    <?php
    $permissao_lancar = db_permissaomenu($anoSessao, 398, 3489);
    if ($permissao_lancar == "true") {
        ?>
        <input name="lancemp" type="button" id="lancemp" value="Lançar Empenho"
               onclick="(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautoriza.js_lanc_empenho();" <?php echo ($db_botao == false || $db_botao_c == false ? "disabled" : "") ?>>
        <?php
    }
    ?>
    <input name="relatorio" type="button" id="db_opcao" value="Relatório de autorização"
           onclick="(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_prazos.js_relatorio();" <?php echo ($db_botao == false || $db_botao_c == false ? "disabled" : "") ?>>
</form>
<?php
if (isset($nops)) {
    db_msgbox($nops);
}

if (isset($tot) && $tot < 0 && empty($cancelar) && isset($pesquisa_dot)) {
    echo "
  <script>
    document.form1.confirmar.disabled = true;
  </script>
  ";
    db_msgbox('Dotação sem saldo disponível!');
}
?>
<script>

  function js_ajustaContrapartida(valor){

    for (var i = 0; i < $("e56_orctiporec").options.length; i++) {

        if ($("e56_orctiporec").options[i].value == valor){

            $("e56_orctiporec").options[i].selected = true;
            $("e56_orctiporecdescr").options[i].selected = true;
            $("gestaoRecurso").options[i].selected = true;
            $("recurso_o15_recurso").options[i].selected = true;
        }
    }
  }

  $("e56_orctiporecdescr").observe('change', function () {
    js_ajustaContrapartida($("e56_orctiporecdescr").value)
  });
  $("e56_orctiporec").observe('change', function () {
    js_ajustaContrapartida($("e56_orctiporec").value)
  });
  $("recurso_o15_recurso").observe('change', function () {
    js_ajustaContrapartida($("recurso_o15_recurso").value)
  });

  $("e56_orctiporec").style.display = "none";
  js_ajustaContrapartida($("e56_orctiporec").value);



    function js_calc(e54_valor) {
        <?php
        if (isset($atual_menos_reservado)) {
            echo "atum = $atual_menos_reservado;\n";
            echo "
           tot=new Number(atum - e54_valor);\n
           document.form1.atudo.value= tot;\n
           conv=document.form1.atudo.value;\n
           t=conv.replace(\".\",\",\");\n
           document.form1.atudo.value= t;\n";
        }
        ?>

        document.form1.e54_valor.value = e54_valor;
    }

    function js_dot() {


        var opcao = document.createElement("input");
        opcao.setAttribute("type", "hidden");
        opcao.setAttribute("name", "pesquisa_dot");
        opcao.setAttribute("value", "true");
        document.form1.appendChild(opcao);
        document.form1.submit();

    }

    function js_verifica() {
        if (document.form1.o47_coddot.value == '') {
            alert('Código da dotação inválida!');
            return false;
        }
        tot = document.form1.atudo.value;

        while (tot.search(/\./) != '-1') {
            tot = tot.replace(/\./, '');
        }
        toti = tot.replace(",", ".");
        tot = new Number(toti);

        if (isNaN(tot) || tot < 0) {
            alert('Dotação sem saldo disponível!');
            document.form1.confirmar.disabled = true;
            return false;
        } else {
            document.form1.confirmar.disabled = false;
        }

        var linhaPacto = $('e56_planoorcamentariolinhapacto');
        if (!empty(linhaPacto.value)) {

            var comboSelecionado = linhaPacto.selectedIndex;
            var saldoFinal = new Number(linhaPacto.options[comboSelecionado].getAttribute('saldo_final'));

            if (new js_strToFloat($F('e54_valor')) > saldoFinal) {
                if (!confirm('O valor informado é maior que o saldo disponível da Linha de Pacto selecionada. Deseja continuar mesmo assim?')) {
                    return;
                }
            }
        }
        if (document.form1.atudo.value == '') {
            alert('Primeiro clique em pesquisar para calcular os valores!');
            return false;
        }


    }

    function js_pesquisao47_coddot(mostra) {
        elemento = (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautitem.document.form1.elemento01.value;
        query = '';
        if (elemento != '') {
            query = "elemento=" + elemento + "&";
        }

        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautidot', 'db_iframe_orcdotacao', 'func_permorcdotacao.php?' + query + 'funcao_js=parent.js_mostraorcdotacao1|o58_coddot', 'Pesquisa', true, 0);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautidot', 'db_iframe_orcdotacao', 'func_permorcdotacao.php?' + query + 'pesquisa_chave=' + document.form1.o47_coddot.value + '&funcao_js=parent.js_mostraorcdotacao', 'Pesquisa', false);
        }

    }

    function js_mostraorcdotacao(chave, erro) {
        if (erro == true) {
            document.form1.o47_coddot.focus();
            document.form1.o47_coddot.value = '';
        }
    }

    function js_mostraorcdotacao1(chave1) {
        document.form1.o47_coddot.value = chave1;
        js_dot();


        db_iframe_orcdotacao.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_empautidot', 'func_empautidot.php?funcao_js=parent.js_preenchepesquisa|e56_autori', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_empautidot.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    /**
     *
     * @param planoOrcamentario
     */
    function js_pesquisaLinhaPactos(planoOrcamentario, valorSelecionado) {


        var parametro = {
            "plano": planoOrcamentario,
            "exec": "getLinhasDePactoDoPlano"
        };

        $('e56_planoorcamentariolinhapacto').options.length = 0;
        new AjaxRequest('orc4_dotacao.RPC.php', parametro, function (response, erro) {

            var elementoPlano = $('e56_planoorcamentariolinhapacto');
            elementoPlano.options.length = 0;
            if (erro) {
                alert(response.mensagem)
            }
            quantidadeDeItens = response.linhas.length;
            for (var linhaPacto of response.linhas) {

                var itemSelecionado = quantidadeDeItens === 1;
                if (valorSelecionado != null) {
                    itemSelecionado = linhaPacto.codigo == valorSelecionado;
                }
                var option = new Option(linhaPacto.descricao + "  - Saldo Atual: R$ " + js_formatar(linhaPacto.saldo_final, 'f'), linhaPacto.codigo, itemSelecionado, itemSelecionado);
                option.setAttribute('saldo_final', linhaPacto.saldo_final);
                elementoPlano.add(option);
            }
        }).setMessage("Aguarde, pesquisando linhas de pacto.").execute();

    }
    <?php
    if (!empty($e56_planoorcamentariolinhapacto) && FONTE_RECURSO_UNIAO) {
        echo "js_pesquisaLinhaPactos(\$F('planoorcamento'), {$e56_planoorcamentariolinhapacto});\n";
    }
    ?>

     $("gestao").style.width = "80px";
     $("e56_orctiporec").style.width = "80px";
     $("e56_orctiporecdescr").style.width = "340px";

     //$("e56_orctiporec").style.display = "none";

</script>
<?php

if (isset($e56_autori) && $e56_autori != "") {
    $oDaoEmpAutItem = new cl_empautitem();

    $sSql = $oDaoEmpAutItem->sql_query_file(null, null, "e55_codele", null, "e55_autori = {$e56_autori}");

    $rsOrcDotacao = $oDaoEmpAutItem->sql_record($sSql);
    if ($oDaoEmpAutItem->numrows > 0) {
        $iElemento = db_utils::fieldsMemory($rsOrcDotacao, 0)->e55_codele;
        echo "<script> (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautoriza.completaElemento(" . $iElemento . ");</script>";
    }
}
?>
