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

require_once modification('dbforms/db_classesgenericas.php');
require_once modification("classes/db_cgm_classe.php");

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
//MODULO: Cedencia
$clrhcedencia->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("rh261_codcategoriaorigem");
$clrotulo->label("rh261_dtorigemadmissao");
$clrotulo->label("rh261_tiporegimeorigem");
$clrotulo->label("rh261_tiporegimeprev");

$clrotulo->label("rh261_indicadoconselho");

$cgmnome = '';
if (!empty($rh261_numcgm)) {
    $clCgm = new cl_cgm();
    $sqlCgm = $clCgm->sql_query($rh261_numcgm, 'z01_nome as cgmnome');
    $resultado = $clCgm->sql_record($sqlCgm);
    db_fieldsmemory($resultado, 0);
}


?>
<form name="form1" method="post" action="">
    <center>
        <table border="0">
            <tr>
                <td nowrap title="<?= @$Trh31_regist ?>">
                    <fieldset>
                        <legend>
                            <b>Cedência/Disponibilidade de Servidores</b>
                        </legend>
                        <table align="center">
                            <tr>
                                <td nowrap title="<?= @$Trh261_regist ?>">
                                    <?php
                                    db_ancora(@$Lrh261_regist, "", 3);
                                    ?>
                                </td>
                                <td align='left'>
                                    <?php
                                    db_input('rh261_regist', 6, $Irh261_regist, true, 'text', 3, "");
                                    db_input('rh261_sequencial', 6, $Irh261_sequencial, true, 'hidden', 3);
                                    db_input('z01_nome', 30, $Iz01_nome, true, 'text', 3, '')
                                    ?>
                                </td>
                                <td nowrap title="<?= $Trh261_dtorigemadmissao ?>">
                                    <b>
                                        <?= isset($Srh261_dtorigemadmissao) ? @$Srh261_dtorigemadmissao : 'Data admissão origem' ?>:
                                    </b>
                                </td>
                                <td>
                                    <?php
                                    if (isset($rh261_dtorigemadmissao) && $rh261_dtorigemadmissao != null && str_contains($rh261_dtorigemadmissao, '-')) {
                                        $data = explode('-', $rh261_dtorigemadmissao);
                                        $rh261_dtorigemadmissao_ano = $data[0];
                                        $rh261_dtorigemadmissao_mes = $data[1];
                                        $rh261_dtorigemadmissao_dia = $data[2];
                                    }
                                    db_inputdata('rh261_dtorigemadmissao', $rh261_dtorigemadmissao_dia, $rh261_dtorigemadmissao_mes, $rh261_dtorigemadmissao_ano, true, 'text', $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong> Tipo: </strong>
                                </td>
                                <td>
                                    <?php
                                    $arrayOpcoesTipo = [
                                        "" =>  'Não se aplica',
                                        "A" => 'A - Adido',
                                        "C" => 'C - Cedido/Disposição'
                                    ];
                                    db_select("rh261_credencial", $arrayOpcoesTipo, true, $db_opcao, "style=margin-right:50px;");
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Trh261_tiporegimeorigem ?>">
                                    <b>
                                        <?= isset($Lrh261_tiporegimeorigem) ? @$Lrh261_tiporegimeorigem : 'Tipo regime Trab. Origem' ?>:
                                    </b>
                                </td>
                                <td>
                                    <?php
                                    $listaRegime = [
                                        "" => "Não se aplica",
                                        "1" => "1 - CLT",
                                        "2" => "2 - Estatutário"
                                        ];
                                    db_select('rh261_tiporegimeorigem', $listaRegime, true, $db_opcao, "style='width : 100px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Ônus:</strong>
                                </td>
                                <td>
                                    <?php
                                    $array_opcoes_onus = [
                                        "X"  => 'Não se aplica',
                                        "N" => 'N - Não',
                                        "S" => 'S - Sim'
                                    ];
                                    db_select("rh261_onus", $array_opcoes_onus, true, $db_opcao);
                                    ?>
                                </td>
                                <td nowrap title="<?= $Trh261_tiporegimeprev ?>">
                                    <label class="bold" for="rh261_tiporegimeprev" id="lbl_rrh261_tiporegimeprev"><?= isset($Srh261_tiporegimeprev) ? @$Srh261_tiporegimeprev : 'Tipo regime previdenciário' ?>:</label>
                                </td>
                                <td>
                                    <?php
                                    $listaRegimePrev = [
                                        0 => 'Não se aplica',
                                        1 => '1 - Regime Geral de Previdência Social - RGPS',
                                        2 => '2 - Regime Próprio de Previdência Social - RPPS ou Sistema de Proteção Social dos Militares',
                                        3 => '3 - Regime de Previdência Social no exterior'
                                    ];
                                    db_select('rh261_tiporegimeprev', $listaRegimePrev, true, $db_opcao, "style='width : 200px;'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>
                                        Ressarcimento:
                                    </strong>
                                </td>
                                <td>
                                    <?php
                                    $arrayOpcoesRessarcimento = [
                                        "X"  => 'Não se aplica',
                                        "N" => 'N - Não',
                                        "S" => 'S - Sim'
                                    ];
                                    db_select("rh261_ressarcimento", $arrayOpcoesRessarcimento, true, $db_opcao, "style=margin-right:50px;");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Data Movimentação:</strong>
                                </td>
                                <td>
                                    <?php
                                    if (isset($rh261_datamovimentacao) && $rh261_datamovimentacao != null && str_contains($rh261_datamovimentacao, '-')) {
                                        $data = explode('-', $rh261_datamovimentacao);
                                        $rh261_datamovimentacao_ano = $data[0];
                                        $rh261_datamovimentacao_mes = $data[1];
                                        $rh261_datamovimentacao_dia = $data[2];
                                    }
                                    db_inputdata('rh261_datamovimentacao', $rh261_datamovimentacao_dia, $rh261_datamovimentacao_mes, $rh261_datamovimentacao_ano, true, 'text', $db_opcao, "");
                                    ?>
                                </td>
                </td>
            </tr>
            <tr>
                <td>
                    <b>
                        Data Devolução:
                    </b>
                </td>
                <td>
                    <?php
                    if (isset($rh261_devolucao) && $rh261_devolucao != null && str_contains($rh261_devolucao, '-')) {
                        $data = explode('-', $rh261_devolucao);
                        $rh261_devolucao_ano = $data[0];
                        $rh261_devolucao_mes = $data[1];
                        $rh261_devolucao_dia = $data[2];
                    }
                    db_inputdata('rh261_devolucao', $rh261_devolucao_dia, $rh261_devolucao_mes, $rh261_devolucao_ano, true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="CGM">
                        <?php
                        db_ancora('Cgm Origem/Destino:', 'buscarCGM(true)', 1);
                        ?>
                    </label>
                </td>
                <td>
                    <?php
                    db_input('rh261_numcgm', 10, $Irh261_numcgm, true, 'text', $db_opcao, 'onChange="buscarCGM(false)"');
                    db_input('cgmnome', 30, $cgmnome, true);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= @$Lrh261_matorigemcedente ?>
                </td>
                <td>
                    <?php
                    db_input('rh261_matorigemcedente', 20, $Irh261_matorigemcedente, true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= $Trh261_codcategoriaorigem ?>">
                    <b>
                        <?= isset($Srh261_codcategoriaorigem) ? @$Srh261_codcategoriaorigem : 'Código de Categoria' ?>:
                    </b>
                </td>
                <td>
                    <?php
                    $clcodigocategoria = new cl_rhcodigocategoria();
                    $camposCodigoCategoria = 'rh255_codigo';
                    $camposCodigoCategoria .= ',rh255_descricao';
                    $sqlCategoria = $clcodigocategoria->sql_query(null, $camposCodigoCategoria, null, "");
                    $resultadoCategoria = $clcodigocategoria->sql_record($sqlCategoria);
                    $registrosCategoria = pg_num_rows($resultadoCategoria);
                    $listaCategoria = [];
                    $listaCategoria[0] = 'Não se aplica';
                    for ($i = 0; $i < $registrosCategoria; $i++) {
                        db_fieldsmemory($resultadoCategoria, $i);
                        $listaCategoria[$rh255_codigo] = $rh255_codigo . '-' . $rh255_descricao;
                    }
                    db_select('rh261_codcategoriaorigem', $listaCategoria, true, $db_opcao, "style='width : 300px;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Servidor Cedido será informado no eSocial(S1200/S1202)?</b>
                </td>
                <td>
                    <?php
                    $arrayOpcoes = [
                        "N" => 'N - Não',
                        "S" => 'S - Sim'
                    ];
                    db_select("rh261_servidorcedido", $arrayOpcoes, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>
                        Servidor Indicado para Conselho (cód. categoria eSocial 305)?
                    </b>
                </td>
                <td>
                    <?php
                    db_input('rh261_indicadoconselho', 1, 'rh261_indicadoconselho', true, 'checkbox', $db_opcao, 'onclick="isconselheiro(this)"');
                    ?>
                </td>
                <script>
                    function isconselheiro(checkbox) {
                        if (checkbox.checked) {
                            checkbox.value = 't';
                            document.form1.rh261_indicadoconselho = checkbox.value;
                        } else {
                            checkbox.value = 'f';
                            document.form1.rh261_indicadoconselho = checkbox.value;
                        }
                    }
                </script>
            </tr>
            </fieldset>
            </td>
            </tr>
        </table>
        <div class="container">
            <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" onclick="js_validaCedidos()" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> class="center-block">
            <style>
                .container {
                    text-align: center;
                }

                .center-block {
                    display: inline-block;
                    margin-right: 5px;
                }
            </style>
    </center>
    <?
    if (isset($opcao)) {
        echo "<input name='novo' type='button' id='novo' value='Novo' onclick='document.location.href=\"pes1_rhcedente001.php?rh261_regist=$rh261_regist&novo=true\"' >";
    }
    ?>
    </div>
    <table width="100%">
        <tr>
            <td valign="top" width="100%" heigth="100%">
                <?php
                $sql = $clrhcedencia->sql_query_file(null, $camposRhcdencia, 'rh261_sequencial desc', 'rh261_regist=' . $rh261_regist);
                //  $sql = $clrhcedencia->sql_query_file(null,$camposRhcdencia,'rh261_datamovimentacao,rh261_devolucao desc','rh261_regist='.$rh261_regist);

                $asopcoes = 1;

                $chavepri = array("rh261_sequencial" => $rh261_sequencial);
                $cliframe_alterar_excluir->chavepri = $chavepri;
                $cliframe_alterar_excluir->sql = $sql;
                $cliframe_alterar_excluir->campos  = "rh261_datamovimentacao,rh261_devolucao,rh261_matorigemcedente,rh261_servidorcedido";
                $cliframe_alterar_excluir->legenda = "Histórico Cedência/Disposição";
                $cliframe_alterar_excluir->iframe_height = "100%";
                $cliframe_alterar_excluir->iframe_width = "100%";
                $cliframe_alterar_excluir->opcoes = $asopcoes;
                $cliframe_alterar_excluir->iframe_alterar_excluir(1);
                ?>
            </td>
        </tr>
    </table>
</form>
<script>
    const tipoCedencia = document.getElementById('rh261_credencial');
    const onusCedencia = document.getElementById('rh261_onus');
    const ressarcimentoCedencia = document.getElementById('rh261_ressarcimento');
    const servidorCedido = document.getElementById('rh261_servidorcedido');
    const dataMovimetacao = document.getElementById('rh261_datamovimentacao');
    const numCgmCedencia = document.getElementById('rh261_numcgm');
    const matriculaCedente = document.getElementById('rh261_matorigemcedente');
    const cgmNomeCedencia = document.getElementById('cgmnome');

    function buscarCGM(lMostrar) {
        cgmNomeCedencia.value = '';
        if (lMostrar) {
            numCgmCedencia.value = '';
            js_OpenJanelaIframe("", 'func_nome', 'func_cgm.php?condition="somenteAtivos"&funcao_js=parent.js_preencheCGM|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {
            js_divCarregando("Pesquisando ...", 'msgBox');
            js_OpenJanelaIframe("", 'func_nome', 'func_cgm.php?condition="somenteAtivos"&pesquisa_chave=' + numCgmCedencia.value + '&funcao_js=parent.js_preencheCGM1', 'Pesquisa', false);
        }
    }

    function js_preencheCGM(numcgm, nome) {
        numCgmCedencia.value = numcgm;
        cgmNomeCedencia.value = nome;
        func_nome.hide();
    }

    function js_preencheCGM1(lErro, nome) {
        js_removeObj('msgBox');
        if (!lErro) {
            cgmNomeCedencia.value = Nome;
        } else {
            numCgmCedencia.value = "";
            cgmNomeCedencia.value = Nome;
        }
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('', 'db_iframe_rhdepend', 'func_rhdepend.php?funcao_js=parent.js_preenchepesquisa|rh31_regist', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_rhdepend.hide();
        <?
        if ($db_opcao != 1) {
            echo "  location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_pesquisarh01_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'func_dependete', 'func_dependete.php?funcao_js=parent.js_mostracgm1|z01_nome|z01_sexo|z01_nasc|z01_cgccpf', 'Pesquisa', true, '0');
        }
    }

    function js_mostracgm(erro, chave1, chave2, chave3, chave4) {

        document.form1.rh01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        if (chave3 != "") {
            document.form1.rh01_sexo.value = chave3;
        }
        if (chave4 != "") {
            document.form1.rh01_nasc_dia.value = chave4.substr(8, 2);
            document.form1.rh01_nasc_mes.value = chave4.substr(5, 2);
            document.form1.rh01_nasc_ano.value = chave4.substr(0, 4);
        }
        if (erro == true) {
            document.form1.rh01_numcgm.focus();
            document.form1.rh01_numcgm.value = '';
        }
    }

    function js_mostracgm1(chave1, chave2, chave3, chave4) {

        document.form1.rh31_nome.value = chave1;
        document.form1.dp01_sexo.value = chave2;
        if (chave3 != "") {
            document.form1.rh31_dtnasc_dia.value = chave3.substr(8, 2);
            document.form1.rh31_dtnasc_mes.value = chave3.substr(5, 2);
            document.form1.rh31_dtnasc_ano.value = chave3.substr(0, 4);
            document.form1.rh31_dtnasc.value = chave3.substr(8, 2) + "/" + chave3.substr(5, 2) + "/" + chave3.substr(0, 4);
        }
        document.form1.dp01_cpf.value = chave4;
        func_dependete.hide();
    }

    function js_validaCedidos() {
        if (servidorCedido.value == 'S') {
            let erros = 0;
            mensagem = 'Campos não preenchidos em relação a cedência:\n'
            if (tipoCedencia.value == '') {
                erros++;
                mensagem += ' * Tipo\n'
            }

            if (dataMovimetacao.value == '') {
                erros++;
                mensagem += ' * Data Movimentação\n'
            }
            if (numCgmCedencia.value == '') {
                erros++;
                mensagem += ' * Número CGM\n'
            }
            
            if (erros > 0) {
                alert(mensagem);
                return false;
            }
        }
        return true;
    }
</script>