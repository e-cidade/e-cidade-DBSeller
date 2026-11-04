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

//MODULO: orcamento
$clorcparamseq->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o42_descrrel");
if ($db_opcao == 1) {
    $db_action = "orc1_orcparamseq004.php";
} elseif ($db_opcao == 2 || $db_opcao == 22) {
    $db_action = "orc1_orcparamseq005.php";
} elseif ($db_opcao == 3 || $db_opcao == 33) {
    $db_action = "orc1_orcparamseq006.php";
}

use ECidade\Configuracao\RelatorioLegal\Enum\OrigemDadosEnum;

?>
<form name="form1" method="post" action="<?= $db_action ?>">
    <fieldset style="width:600px;">
        <center>
            <table border="0">
                <tr>
                    <td>
                        <label for="o69_codseq">
                            <strong>Linha:</strong>
                        </label>
                    </td>
                    <td>
                        <?php

                        $opcao1 = $opcao = $db_opcao;
                        if ($db_opcao == 2) {
                            $opcao = 1;
                            $opcao1 = 3;
                        } elseif ($db_opcao == 1) {
                            $opcao = 3;
                            $opcao1 = 1;
                        }

                        db_input('o69_codseq', 10, $Io69_codseq, true, 'text', $opcao, "");
                        if (isset($o69_codseq) && $o69_codseq != "") {
                            $o69_codseq_anterior = $o69_codseq;
                        }
                        db_input('o69_codseq_anterior', 10, null, true, 'hidden', $opcao, "");
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>
                            <?php
                            db_ancora('Relatório:', "js_pesquisao69_codparamrel(true);", $opcao1);
                            ?>
                        </strong>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'o69_codparamrel',
                            10,
                            $Io69_codparamrel,
                            true,
                            'text',
                            $opcao1,
                            " onchange='js_pesquisao69_codparamrel(false);'"
                        );
                        db_input('o42_descrrel', 40, $Io42_descrrel, true, 'text', 3, '')
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= @$To69_labelrel ?>">
                        <?= @$Lo69_labelrel ?>
                    </td>
                    <td>
                        <?php
                        db_input('o69_labelrel', 130, $Io69_labelrel, true, 'text', $db_opcao, '', '', '', 'width: 100%', 130)
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="o69_origem">
                            <strong>Origem dos Dados:</strong>
                        </label>
                    </td>
                    <td>
                        <?php
                        $aOrigemDados = OrigemDadosEnum::todas();
                        db_select('o69_origem', $aOrigemDados, true, $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$To69_nivellinha ?>">
                        <?= @$Lo69_nivellinha ?>
                    </td>
                    <td>
                        <?php
                        db_input('o69_nivellinha', 10, $Io69_nivellinha, true, 'text', $db_opcao, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$To69_manual?>">
                        <?=@$Lo69_manual?>
                    </td>
                    <td>
                        <?php
                        $x = array("f"=>"NAO","t"=>"SIM");
                        db_select('o69_manual',$x,true,$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$To69_desdobrarlinha ?>">
                        <?= @$Lo69_desdobrarlinha ?>
                    </td>
                    <td>
                        <?php
                        $x = array("f" => "NAO", "t" => "SIM");
                        db_select('o69_desdobrarlinha', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                </tr>
                <tr >
                    <td nowrap title="<?=@$To69_totalizador?>">
                        <?=@$Lo69_totalizador?>
                    </td>
                    <td>
                        <?php
                        $x = array("f"=>"NAO","t"=>"SIM");
                        db_select('o69_totalizador',$x,true,$db_opcao,"onchange='js_verificaLinhaTotalizadora()'");
                        ?>
                    </td>
                </tr>
            </table>
        </center>
    </fieldset>

    <?php
    if ($db_opcao != 1) {
        echo "<input name='novo' type='button'value='Novo' onclick='js_novo();' >";
    }
    ?>

    <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
           type="submit" id="db_opcao"
           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
</form>
<script>
    function js_pesquisao69_codparamrel(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcparamseq', 'db_iframe_orcparamrel',
                'func_orcparamrel.php?funcao_js=parent.js_mostraorcparamrel1|o42_codparrel|o42_descrrel|o69_codseq',
                'Pesquisa', true, '0', '1');
        } else {
            if (document.form1.o69_codparamrel.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_orcparamseq', 'db_iframe_orcparamrel',
                    'func_orcparamrel.php?chave_composta=' + document.form1.o69_codparamrel.value +
                    '&funcao_js=parent.js_chavecomposta', 'Pesquisa', false, '0', '1');
            } else {
                document.form1.o42_descrrel.value = '';
            }
        }
    }

    function js_chavecomposta(o69_codseq, o42_descrel, erro) {
        document.form1.o69_codseq.value = o69_codseq;
        document.form1.o42_descrrel.value = o42_descrel;
        if (erro == true) {
            document.form1.o69_codparamrel.focus();
            document.form1.o69_codparamrel.value = '';
        }
    }

    function js_novo() {
        parent.document.location.href = 'orc1_orcparamseq001.php';
    }

    function js_mostraorcparamrel(chave, erro) {
        document.form1.o42_descrrel.value = chave;
        if (erro == true) {
            document.form1.o69_codparamrel.focus();
            document.form1.o69_codparamrel.value = '';
        }
    }

    function js_mostraorcparamrel1(chave1, chave2, chave3) {
        document.form1.o69_codparamrel.value = chave1;
        document.form1.o42_descrrel.value = chave2;
        document.form1.o69_codseq.value = chave3;
        document.form1.o69_codseq_anterior.value = (chave3 - 1);
        db_iframe_orcparamrel.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo.iframe_orcparamseq',
            'db_iframe_orcparamseq',
            'func_orcparamseq.php?funcao_js=parent.js_preenchepesquisa|o69_codparamrel|o69_codseq&codigo_relatorio=' +
            document.form1.o69_codparamrel.value,
            'Pesquisa',
            true,
            '0',
            '1'
        );
    }

    function js_preenchepesquisa(chave, chave1) {
        db_iframe_orcparamseq.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave+'&chavepesquisa1='+chave1";
        }
        ?>
    }

    $('o69_origem').onchange = function() {
        $('o69_desdobrarlinha').value = 'f';
        $('o69_desdobrarlinha').disabled = 'disabled';

        if ($F('o69_origem') != 0) {
            $('o69_desdobrarlinha').removeAttribute('disabled');
        }
    };

    if ($F('db_opcao') == 'Incluir') {
        $('o69_origem').dispatchEvent(new Event('change'));
    }
</script>
