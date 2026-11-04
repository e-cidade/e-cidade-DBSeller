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

//MODULO: Farmácia
$oDaoFarParametros->rotulo->label();
$oRotulo = new rotulocampo;
$oRotulo->label("db77_descr");
$oRotulo->label("fa12_c_descricao");

//verifica se a opção da numeração pelo material esta Disponivel para seleção
$sSql = $oDaoFarMaterSaude->sql_query_file(null, ' count(*) as total ');
$rs = $oDaoFarMaterSaude->sql_record($sSql);
if ($oDaoFarMaterSaude->numrows == 1) {
    $oDados = db_utils::fieldsmemory($rs, 0);
} else {
    echo "<script>alert('Erro Técnico - Verifique a tabela far_matersaude');</script>";
    $iLibera = 3;
}
if ($oDados->total == 0) {
    $iLibera = 1;
} else {
    $iLibera = 3;
}
?>
<form name="form1" method="post" action="">
    <div class="container">
        <fieldset>
            <legend><b>Inclusão de Parâmetros</b></legend>
            <div class="subcontainer">
                <table>
                    <tr>
                        <td>
                            <fieldset class='fieldsetSeparator'>
                                <legend><b>Parâmetros Gerais</legend>
                                <table>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_codigo ?>">
                                            <?= @$Lfa02_i_codigo ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('fa02_i_codigo', 5, $Ifa02_i_codigo, true);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_dbestrutura ?>">
                                            <?php
                                            db_ancora(
                                                @$Lfa02_i_dbestrutura,
                                                "js_pesquisafa02_i_dbestrutura(true);",
                                                $db_opcao1
                                            );
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input(
                                                'fa02_i_dbestrutura',
                                                5,
                                                $Ifa02_i_dbestrutura,
                                                true,
                                                'text',
                                                $db_opcao1,
                                                " onchange='js_pesquisafa02_i_dbestrutura(false);'"
                                            );
                                            db_input('db77_descr', 40, $Idb77_descr, true);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_c_descr ?>">
                                            <?= @$Lfa02_c_descr ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('fa02_c_descr', 49, $Ifa02_c_descr, true, 'text', $db_opcao);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_c_digitacao ?>">
                                            <b>Digitação Medicamentos</b>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array("S" => "SIM", "N" => "NÃO");
                                            db_select('fa02_c_digitacao', $aX, true, $db_opcao);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_b_numestoque ?>">
                                            <?= @$Lfa02_b_numestoque ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array("f" => "NÃO", "t" => "SIM");
                                            db_select('fa02_b_numestoque', $aX, true, $iLibera);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_b_novaretirada ?>">
                                            <?= @$Lfa02_b_novaretirada ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array("f" => "NÃO", "t" => "SIM");
                                            db_select('fa02_b_novaretirada', $aX, true, $db_opcao);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_origemreceita ?>">
                                            <?= @$Lfa02_i_origemreceita ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array('1' => 'SIM', '2' => 'NÃO');
                                            db_select('fa02_i_origemreceita', $aX, true, $db_opcao);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_acumularsaldocontinuado ?>">
                                            <?= @$Lfa02_i_acumularsaldocontinuado ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array('1' => 'SIM', '2' => 'N&Atilde;O');
                                            db_select('fa02_i_acumularsaldocontinuado', $aX, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_cursor ?>">
                                            <?= @$Lfa02_i_cursor ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array('1' => 'CGS', '2' => 'Cartão SUS', '3' => 'Nome', '4' => 'CPF');
                                            db_select('fa02_i_cursor', $aX, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_validalote ?>">
                                            <?= @$Lfa02_i_validalote ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array('2' => 'NÃO', '1' => 'SIM');
                                            db_select('fa02_i_validalote', $aX, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_validavencimento ?>">
                                            <?= @$Lfa02_i_validavencimento ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array(
                                                '1' => 'Não validar',
                                                '2' => 'Somente alertar',
                                                '3' => 'Alertar e Bloquear'
                                            );
                                            db_select('fa02_i_validavencimento', $aX, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_acaoprog ?>">
                                            <?php
                                            db_ancora(
                                                @$Lfa02_i_acaoprog,
                                                "js_pesquisafa02_i_acaoprog(true);",
                                                $db_opcao
                                            );
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input(
                                                'fa02_i_acaoprog',
                                                10,
                                                '',
                                                true,
                                                'text',
                                                $db_opcao,
                                                " onchange='js_pesquisafa02_i_acaoprog(false);' "
                                            );
                                            db_input('fa12_c_descricao', 50, $Ifa12_c_descricao, true);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>


                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_avisoretirada ?>">
                                            <?= @$Lfa02_i_avisoretirada ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!isset($fa02_i_avisoretirada)) {
                                                $fa02_i_avisoretirada = 0;
                                            }
                                            db_input(
                                                'fa02_i_avisoretirada',
                                                5,
                                                $Ifa02_i_avisoretirada,
                                                true,
                                                'text',
                                                $db_opcao
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_verificapacientehiperdia ?>">
                                            <?= @$Lfa02_i_verificapacientehiperdia ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array('2' => 'NÃO', '1' => 'SIM');
                                            db_select('fa02_i_verificapacientehiperdia', $aX, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_i_numdiasmedcontinativo ?>">
                                            <?= @$Lfa02_i_numdiasmedcontinativo ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!isset($fa02_i_numdiasmedcontinativo)) {
                                                $fa02_i_numdiasmedcontinativo = 0;
                                            }
                                            db_input(
                                                'fa02_i_numdiasmedcontinativo',
                                                5,
                                                $Ifa02_i_numdiasmedcontinativo,
                                                true,
                                                'text',
                                                $db_opcao
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap
                                            title="Parâmetro responsável pela exibição do tipo de receita vinculada ao medicamento">
                                            <label class="bold">Informar o tipo de receita do medicamento:</label>
                                        </td>
                                        <td>
                                            <?php
                                            $opcoes = ['f' => 'NÃO', 't' => 'SIM'];
                                            db_select('fa02_informa_receita_medicamento', $opcoes, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap
                                            title="Parâmetro responsável pela alteração da validade inicial
                                                do medicamento na rotina do medicamento continuado">
                                            <label class="bold">Permitir alterar a validade inicial do medicamento continuado:</label>
                                        </td>
                                        <td>
                                            <?php
                                            $opcoes = ['f' => 'NÃO', 't' => 'SIM'];
                                            db_select('fa02_altera_validade_medcontinuado', $opcoes, true, 1);
                                            ?>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td nowrap title="<?= $Tfa02_alerta_nao_morador ?>">
                                            <label class="bold">
                                                Exibir alerta de paciente não morador do município:
                                            </label>
                                        </td>
                                        <td>
                                            <?php
                                            $opcoes = ['f' => 'NÃO', 't' => 'SIM'];
                                            db_select('fa02_alerta_nao_morador', $opcoes, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $display = $fa02_alerta_nao_morador == 't' ? '' : 'display: none;';
                                    ?>
                                    <tr id="alerta-nao-morador-ibge" style="<?= $display ?>">
                                        <td></td>
                                        <td>
                                            <label for="fa02_ibge_municipio" class="bold">Código IBGE: </label>
                                            <?php
                                            db_input(
                                                'fa02_ibge_municipio',
                                                5,
                                                $Ifa02_ibge_municipio,
                                                true,
                                                'text',
                                                $db_opcao
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap
                                            title="Parâmetro ativa modificações na entrada/saída de materiais com campos necessários para exportar pro BNAFAR e habilita o uso do item de menu da integração.">
                                            <label class="bold">Utilizar integração BNAFAR:</label>
                                        </td>
                                        <td>
                                            <?php
                                            $opcoes = ['f' => 'NÃO', 't' => 'SIM'];
                                            db_select('fa02_utiliza_bnafar', $opcoes, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <fieldset class='fieldsetSeparator'>
                                <legend><b>Comprovante de Entrega</b></legend>
                                <table>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_b_comprovante ?>">
                                            <?= @$Lfa02_b_comprovante ?>
                                        </td>
                                        <td>
                                            <?php
                                            $aX = array("f" => "NÃO", "t" => "SIM");
                                            db_select('fa02_b_comprovante', $aX, true, $db_opcao);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td nowrap title="<?= @$Tfa02_utilizaimpressoratermica ?>">
                                            <?= @$Lfa02_utilizaimpressoratermica ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($fa02_utilizaimpressoratermica)) {
                                                $fa02_utilizaimpressoratermica = $fa02_utilizaimpressoratermica == 't'
                                                    ? 'true' : 'false';
                                            }
                                            $aUtilizaImpressora = array('false' => 'NÃO', 'true' => 'SIM');
                                            db_select('fa02_utilizaimpressoratermica', $aUtilizaImpressora, true, 1);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    <tr>
                        <td nowrap colspan='2'>
                            <fieldset class='fieldsetSeparator'>
                                <legend><b>Pr&oacute;xima Retirada</b></legend>
                                <table border="0">
                                    <tr>
                                        <td nowrap>
                                            <?php
                                            $sC1 = $sC2 = '';
                                            if (@$fa02_i_tipoperiodocontinuado == 1) {
                                                $sC1 = 'checked';
                                            } elseif (@$fa02_i_tipoperiodocontinuado == 2) {
                                                $sC2 = 'checked';
                                            }
                                            ?>
                                            <input
                                                    type='radio'
                                                    id="tipoperiodocontinuado-datacontinuado"
                                                    name='fa02_i_tipoperiodocontinuado'
                                                <?= $sC1 ?>
                                                    value='1'
                                            >
                                            <label for="tipoperiodocontinuado-datacontinuado">
                                                &Agrave; partir da validade inicial no cadastro de continuado
                                            </label>
                                            <input
                                                    type='radio'
                                                    id="periodocontinuado-ultimaretirada"
                                                    name='fa02_i_tipoperiodocontinuado'
                                                <?= $sC2 ?>
                                                    value='2'
                                            >
                                            <label for="periodocontinuado-ultimaretirada">
                                                &Agrave; partir da data da &uacute;ltima retirada
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td height="18">&nbsp;</td>
                        <td height="18">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <input
                name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
                type="submit" id="db_opcao"
                value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
            <?= ($db_botao == false ? "disabled" : "") ?>>
    </div>
</form>

<script>
    const selectAlertaNaoMorador = document.getElementById('fa02_alerta_nao_morador');
    const trIbgeMunicipio = document.getElementById('alerta-nao-morador-ibge');
    const inputIbge = document.getElementById('fa02_ibge_municipio');

    selectAlertaNaoMorador.addEventListener('change', () => {
        trIbgeMunicipio.style.display = selectAlertaNaoMorador.value === 't' ? '' : 'none';
    });

    document.addEventListener('submit', validaForm);

    function js_pesquisafa02_i_acaoprog(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_far_programa', 'func_far_programa.php?funcao_js=' +
                'parent.js_mostrafar_programa1|fa12_i_codigo|fa12_c_descricao',
                'Pesquisa', true
            );
        } else {
            if (document.form1.fa02_i_acaoprog.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_far_programa', 'func_far_programa.php?pesquisa_chave=' +
                    document.form1.fa02_i_acaoprog.value + '&funcao_js=parent.js_mostrafar_programa',
                    'Pesquisa', false
                );
            } else {
                document.form1.fa12_c_descricao.value = '';
            }
        }
    }

    function js_mostrafar_programa(chave, erro) {
        document.form1.fa12_c_descricao.value = chave;
        if (erro == true) {
            document.form1.fa02_i_acaoprog.focus();
            document.form1.fa02_i_acaoprog.value = '';

        }
    }

    function js_mostrafar_programa1(chave1, chave2) {
        document.form1.fa02_i_acaoprog.value = chave1;
        document.form1.fa12_c_descricao.value = chave2;
        db_iframe_far_programa.hide();
    }

    function js_pesquisafa02_i_dbestrutura(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_db_estrutura', 'func_db_estrutura.php?funcao_js=' +
                'parent.js_mostradb_estrutura1|db77_codestrut|db77_descr', 'Pesquisa', true
            );
        } else {
            if (document.form1.fa02_i_dbestrutura.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_db_estrutura', 'func_db_estrutura.php?pesquisa_chave=' +
                    document.form1.fa02_i_dbestrutura.value + '&funcao_js=parent.js_mostradb_estrutura',
                    'Pesquisa', false
                );

            } else {
                document.form1.db77_descr.value = '';
            }
        }
    }

    function js_mostradb_estrutura(chave, erro) {
        document.form1.db77_descr.value = chave;
        if (erro == true) {
            document.form1.fa02_i_dbestrutura.focus();
            document.form1.fa02_i_dbestrutura.value = '';
        }
    }

    function js_mostradb_estrutura1(chave1, chave2) {
        document.form1.fa02_i_dbestrutura.value = chave1;
        document.form1.db77_descr.value = chave2;
        db_iframe_db_estrutura.hide();
    }

    function validaForm(event) {
        if (selectAlertaNaoMorador.value === 't' && inputIbge.value === '') {
            event.preventDefault();
            alert('Informe o código IBGE do munícipio.');
        }
    }
</script>
