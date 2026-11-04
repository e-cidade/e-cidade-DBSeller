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

//MODULO: pessoal
$clrhregime->rotulo->label();

?>
<form name="form1" method="post" action="">
    <table border=0>
        <tr>
            <td align="center">
                <br>
                <fieldset>
                    <legend><b>Manutenção de Vinculos</b></legend>

                    <center>
                        <table border="0">
                            <tr>
                                <td nowrap title="<?= @$Trh30_codreg ?>">
                                    <?= @$Lrh30_codreg ?>
                                </td>
                                <td>
                                    <?php db_input('rh30_codreg', 10, $Irh30_codreg, true, 'text', 3, "") ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Trh30_descr ?>">
                                    <?= @$Lrh30_descr ?>
                                </td>
                                <td>
                                    <?php db_input('rh30_descr', 41, $Irh30_descr, true, 'text', $db_opcao, "") ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Trh30_utilizacao ?>">
                                    <?= @$Lrh30_utilizacao ?>
                                </td>
                                <td>
                                    <?php
                                    $arr_util = Array('1' => 'Ambos', '2' => 'Folha de Pagamento', '3' => 'Educação');
                                    db_select("rh30_utilizacao", $arr_util, true, $db_opcao, "onChange=\"js_mudautilizacao(this.value)\" style='width : 300px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Trh30_regime ?>">
                                    <?= @$Lrh30_regime ?>
                                </td>
                                <td>
                                    <?php
                                    $result_regime = $clrhcadregime->sql_record($clrhcadregime->sql_query_file());
                                    if ($db_opcao == 1)
                                    {
                                        db_selectrecord("rh30_regime", $result_regime, true, $db_opcao, " style='width : 300px;'", '', '', '', 'js_regime();js_regime_tipo();', 1);
                                    } else
                                    {
                                        db_selectrecord("rh30_regime", $result_regime, true, $db_opcao, " style='width : 300px;'", '', '', '', 'js_regime_tipo()', 1);
                                    }

                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Trh30_vinculo ?>">
                                    <?= @$Lrh30_vinculo ?>
                                </td>
                                <td>
                                    <?php
                                    $arr_vinculo = Array('A' => 'Ativo', 'I' => 'Inativo', 'P' => 'Pensionista');
                                    db_select("rh30_vinculo", $arr_vinculo, true, $db_opcao, "style='width : 300px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Natureza">
                                    <b>Natureza : </b>
                                </td>
                                <td>
                                    <?php
                                    $sSqlNatureza = "select rh71_sequencial,rh71_descricao from rhnaturezaregime";
                                    $rsNatureza = db_query($sSqlNatureza);
                                    $iNatureza = pg_num_rows($rsNatureza);
                                    $aNatureza = Array();
                                    for ($i = 0; $i < $iNatureza; $i++) {

                                        db_fieldsmemory($rsNatureza, $i);
                                        $aNatureza[$rh71_sequencial] = $rh71_descricao;

                                    }

                                    db_select("rh30_naturezaregime", $aNatureza, true, $db_opcao, "style='width : 300px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td> <?= $Lrh30_vinculomanad ?> </td>
                                <td>
                                    <?php
                                    include(modification("classes/db_vinculomanad_classe.php"));

                                    $clvinculomanad = new cl_vinculomanad();
                                    $sSqlVinculomanad = $clvinculomanad->sql_query_file();
                                    $rsVinculomanad = $clvinculomanad->sql_record($sSqlVinculomanad);

                                    db_selectrecord('rh30_vinculomanad', $rsVinculomanad, true, $db_opcao, "style='width : 300px;'", '', '', '', '', 1);

                                    ?>

                                </td>
                            </tr>
                            <tr>
                                <td> <?= $Lrh30_periodoaquisitivo ?></td>
                                <td>
                                    <?php
                                    $aPeriodoArquisitivo = array("1" => "12 meses", "2" => "6 meses");
                                    db_select("rh30_periodoaquisitivo", $aPeriodoArquisitivo, true, $db_opcao, "style='width : 300px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo $Trh30_vinculoemprego; ?>">
                                    <label class="bold" for="rh30_vinculoemprego"
                                           id="lbl_rh30_vinculoemprego"><?php echo $Srh30_vinculoemprego; ?>:</label>
                                </td>
                                <td>
                                    <?php
                                    db_select('rh30_vinculoemprego', array("f" => "Não", "t" => "Sim"), true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo $Trh30_codigocategoria; ?>">
                                    <label class="bold" for="rh30_codigocategoria"
                                           id="lbl_rh30_codigocategoria"><?php echo $Srh30_codigocategoria; ?>:</label>
                                </td>
                                <td>
                                    <?php

                                    $clcodigocategoria = new cl_rhcodigocategoria();
                                    $camposCodigoCategoria = 'rh255_codigo';
                                    $camposCodigoCategoria .=',rh255_descricao';
                                    $sqlCategoria = $clcodigocategoria->sql_query(null,$camposCodigoCategoria,"rh255_codigo","");
                                    $resultadoCategoria = $clcodigocategoria->sql_record($sqlCategoria);
                                    $registrosCategoria = pg_num_rows($resultadoCategoria);
                                    $listaCategoria = [];
                                    for ($i = 0; $i < $registrosCategoria; $i++) {
                                        db_fieldsmemory($resultadoCategoria, $i);
                                        $listaCategoria[$rh255_codigo] = $rh255_codigo . '-' . $rh255_descricao;
                                    }
                                    if ($db_opcao == 1) {
                                        $rh30_codigocategoria =301;
                                    }
                                    db_select('rh30_codigocategoria', $listaCategoria, true, $db_opcao, "style='width : 300px;'");
                                    ?>
                                </td>
                            </tr>
                            <?php
                                $style = 'collapse';

                             ?>
                            <tr id="select_provimento" style="visibility: <?= $style; ?>;">
                                <td nowrap title="<?= @$Trh30_provimento ?>">
                                    <?= @$Lrh30_provimento ?>
                                </td>
                                <td>
                                <?php
                                     $tipo_provimento = [
                                        '1' => '1 - Nomeação em cargo efetivo',
                                        '2' => '2 - Nomeação exclusivamente em cargo em comissão',
                                        '3' => '3 - Incorporação, matrícula ou nomeação (militar)',
                                        '5' => '5 - Redistribuição',
                                        '6' => '6 - Diplomação',
                                        '7' => '7 - Contratação por tempo determinado',
                                        '8' => '8 - Remoção (em caso de alteração do órgão declarante)',
                                        '9' => '9 - Designação',
                                        '10'=> '10 - Mudança de CPF',
                                        '99'=> '99 - Outros não relacionados acima'
                                    ];

                                     db_select("rh30_provimento", $tipo_provimento, true, $db_opcao, "style='width : 300px;'");
                                ?>
                                </td>
                            </tr>

                            <tr id="select_admissao" style="visibility: <?= $style; ?>">
                            <td nowrap title="<?= @$Trh30_admissao ?>">
                                    <?= @$Lrh30_admissao ?>
                                </td>
                                <td>
                                <?php
                                     $tipo_admissao = [
                                        '1'=>'1 - Admissão',
                                        '2'=>'2 - Transferência de empresa do mesmo grupo econômico ou transferência entre órgãos do mesmo Ente Federativo',
                                        '3'=>'3 - Transferência de empresa consorciada ou de consórcio',
                                        '4'=>'4 - Transferência por motivo de sucessão, incorporação, cisão ou fusão',
                                        '5'=>'5 - Transferência do empregado doméstico para outro representante da mesma unidade familiar',
                                        '6'=>'6 - Mudança de CPF'
                                    ];

                                     db_select("rh30_admissao", $tipo_admissao, true, $db_opcao, "style='width : 300px;'");
                                ?>'
                                </td>
                            </tr>
                        </table>
                    </center>

                </fieldset>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input
                    name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
                    type="submit" id="db_opcao"
                    value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
                <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
            </td>
        </tr>
    </table>
</form>

<script>
    const vinculoInicial = document.querySelector("#rh30_vinculoemprego").value;
    const form = document.querySelector("form[name=form1]");

    form.addEventListener("submit", function (event) {
        const vinculoAtual = document.querySelector("#rh30_vinculoemprego").value;
        const codigoVinculo = document.querySelector("#rh30_codreg");

        if (vinculoInicial !== vinculoAtual || codigoVinculo.value === "") {
            var mensagem = "";

            if (!codigoVinculo.value) {
                mensagem += "Incluir o vínculo de emprego como ";
            } else {
                mensagem += "Alterar o vínculo de emprego para ";
            }

            if (vinculoAtual === "t") {
                mensagem += "\"Sim\" fará com que todos os servidores vinculados a este regime\nsejam enviados no arquivo \"S-2200 - Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador\" do eSocial.\nDeseja continuar?"
            } else {
                mensagem += "\"Não\" fará com que todos os servidores vinculados a este regime\nsejam enviados no arquivo \"S-2300 - Trabalhador Sem Vínculo de Emprego/Estatutário - Início\" do eSocial.\nDeseja continuar?"
            }

            if (!confirm(mensagem)) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
    });

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhregime', 'func_rhregime.php?funcao_js=parent.js_preenchepesquisa|rh30_codreg', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_rhregime.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_mudautilizacao(valor) {
        if (valor == 3) {
            document.form1.rh30_regime[0].disabled = true;
            document.form1.rh30_regime[1].disabled = true;
            document.form1.rh30_regime[2].selected = true;
            document.form1.rh30_regimedescr[0].disabled = true;
            document.form1.rh30_regimedescr[1].disabled = true;
            document.form1.rh30_regimedescr[2].selected = true;
            document.form1.rh30_vinculo.value = "A";
            document.form1.rh30_vinculo[2].disabled = true;
            document.form1.rh30_naturezaregime.value = 4;
            selecionado = document.form1.rh30_naturezaregime.selectedIndex;
            tam = document.form1.rh30_naturezaregime.length;
            for (i = 0; i < tam; i++) {
                if (i != selecionado) {
                    document.form1.rh30_naturezaregime[i].disabled = true;
                }
            }
            document.form1.rh30_vinculomanad.value = 9;
            selecionado = document.form1.rh30_vinculomanad.selectedIndex;
            tam = document.form1.rh30_vinculomanad.length;
            for (i = 0; i < tam; i++) {
                if (i != selecionado) {
                    document.form1.rh30_vinculomanad[i].disabled = true;
                }
            }
        } else {
            document.form1.rh30_regime[0].disabled = false;
            document.form1.rh30_regime[1].disabled = false;
            document.form1.rh30_regimedescr[0].disabled = false;
            document.form1.rh30_regimedescr[1].disabled = false;
            document.form1.rh30_vinculo[2].disabled = false;
            tam = document.form1.rh30_naturezaregime.length;
            for (i = 0; i < tam; i++) {
                document.form1.rh30_naturezaregime[i].disabled = false;
            }
            tam = document.form1.rh30_vinculomanad.length;
            for (i = 0; i < tam; i++) {
                document.form1.rh30_vinculomanad[i].disabled = false;
            }
        }
    }

    <?php if(isset($chavepesquisa)){?>
    js_mudautilizacao(<?=$rh30_utilizacao?>);
    <?php }?>

    function js_regime() {
        let regime = document.querySelector("#rh30_regime").value
        if (regime == 1) {
            document.querySelector("#rh30_codigocategoria").value = 301;
        }
        if (regime == 2) {
            document.querySelector("#rh30_codigocategoria").value = 101;
        }
        if (regime == 3) {
            document.querySelector("#rh30_codigocategoria").value = 302;
        }
    }

    function js_regime_tipo() {
        let regime = document.querySelector("#rh30_regime").value

        if(regime == 1 || regime == 3){
            document.querySelector('#select_provimento').style.visibility='visible';
            document.querySelector('#select_admissao').style.visibility='collapse';

        }

        if(regime == 2){
            document.querySelector('#select_admissao').style.visibility='visible';
            document.querySelector('#select_provimento').style.visibility='collapse';
        }
    }
</script>
