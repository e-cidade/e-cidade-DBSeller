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

//MODULO: caixa
$clcaiparametro->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nomeinst");
$clrotulo->label("k13_descr");
$clrotulo->label("o15_recurso");
$clrotulo->label("o15_descr");

$oParametrosCaixa = new ParametroCaixa();
$tipo_transmissao = $oParametrosCaixa->getTipoTransmissaoPadrao();
$sConvenioBanco = $oParametrosCaixa->getConvenioBanco();
?>
<form name="form1" method="post" action="">
    <fieldset>
        <legend>
            <strong>Parâmetros Financeiro</strong>
        </legend>
        <table class="form-container">
            <tr>
                <td title="<?=$Tk29_boletimzerado ?>">
                    <?=$Lk29_boletimzerado ?>
                </td>
                <td>
                    <?php
                    $x = array("f" => "NAO", "t" => "SIM");
                    db_select('k29_boletimzerado', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_modslipnormal ?>">
                    <?=$Lk29_modslipnormal ?>
                </td>
                <td>
                    <?php
                    $x = array('36' => 'Normal/2 partes', '37' => 'Com assinaturas/1 parte');
                    db_select('k29_modslipnormal', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_modsliptransf ?>">
                    <?=$Lk29_modsliptransf ?>
                </td>
                <td>
                    <?php
                    $x = array('36' => 'Normal/2 partes', '37' => '2 partes/com assinatura');
                    db_select('k29_modsliptransf', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_chqemitidonaoautent ?>">
                    <?=$Lk29_chqemitidonaoautent ?>
                </td>
                <td>
                    <?php
                    db_inputdata(
                        'k29_chqemitidonaoautent',
                        (!isset($k29_chqemitidonaoautent_dia)?null:$k29_chqemitidonaoautent_dia),
                        (!isset($k29_chqemitidonaoautent_mes)?null:$k29_chqemitidonaoautent_mes),
                        (!isset($k29_chqemitidonaoautent_ano)?null:$k29_chqemitidonaoautent_ano),
                        true,
                        'text',
                        $db_opcao,
                        ""
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_saldoemitechq ?>">
                    <?=$Lk29_saldoemitechq ?>
                </td>
                <td>
                    <?php
                    $x = array('1' => 'Sim', '2' => 'Não');
                    db_select('k29_saldoemitechq', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_datasaldocontasextra ?>">
                    <?=$Lk29_datasaldocontasextra ?>
                </td>
                <td>
                    <?php
                    db_inputdata(
                        'k29_datasaldocontasextra',
                        (!isset($k29_datasaldocontasextra_dia)?null:$k29_datasaldocontasextra_dia),
                        (!isset($k29_datasaldocontasextra_mes)?null:$k29_datasaldocontasextra_mes),
                        (!isset($k29_datasaldocontasextra_ano)?null:$k29_datasaldocontasextra_ano),
                        true,
                        'text',
                        $db_opcao,
                        ""
                    )
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_trazdatacheque ?>">
                    <?=$Lk29_trazdatacheque ?>
                </td>
                <td>
                    <?php
                    $x = array('f' => 'Não', 't' => 'Sim');
                    db_select('k29_trazdatacheque', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_contassemmovimento ?>">
                    <?=$Lk29_contassemmovimento ?>
                </td>
                <td>
                    <?php
                    $x = array('f' => 'Não', 't' => 'Sim');
                    db_select('k29_contassemmovimento', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?php echo  @$Tk29_fr_contapagadora ?>">
                    <?php echo  @$Lk29_fr_contapagadora ?>
                </td>
                <td>
                    <?php
                        $x = array('1' => 'Fonte de Recurso', '2' => 'Subfonte');
                        db_select('k29_fr_contapagadora', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_filtrocontasdepartamento ?>">
                    <?=$Lk29_filtrocontasdepartamento ?>
                </td>
                <td>
                    <?php
                    $x = array('f' => 'Não', 't' => 'Sim');
                    db_select('k29_filtrocontasdepartamento', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td><a id='ancoraRecurso' href="#">Recurso:</a></td>
                <td>
                    <?php
                    db_input('k29_orctiporecfundeb', 10, $Ik29_orctiporecfundeb, true, 'hidden', 3);
                    db_input('o15_recurso', 10, $Io15_recurso, true, 'text', $db_opcao);
                    db_input('o15_descr', 40, $Io15_descr, true, 'text', 3);
                    ?>

                </td>
            </tr>
            <tr>
                <td title="<?=$Tk29_contapadraoslip?>">
                    <?php
                    db_ancora("<b>Conta padrão slip:</b>", "js_pesquisak29_contapadraoslip(true);", $db_opcao);
                    ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'k29_contapadraoslip',
                        10,
                        $Ik29_contapadraoslip,
                        true,
                        'text',
                        $db_opcao,
                        " onchange='js_pesquisak29_contapadraoslip(false);'"
                    );
                    db_input('k13_descr', 40, $Ik13_descr, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
       </table>
        <fieldset>
            <legend>Agenda de Pagamentos</legend>
            <table class="form-container">
                <tr>
                    <td title="<?=$Tk29_chqduplicado ?>">
                        Permitir cheques duplicados:
                    </td>
                    <td>
                        <?php
                        $x = array("f" => "NAO", "t" => "SIM");
                        db_select('k29_chqduplicado', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Tipo de Transmissão:
                    </td>
                    <td>
                        <?php
                        $oDaoTipoTransmissao = new cl_empagetipotransmissao();
                        $sSqlBuscaTipos = $oDaoTipoTransmissao->sql_query_file(null, "*", 'e57_sequencial');
                        $rsBuscaTipos = $oDaoTipoTransmissao->sql_record($sSqlBuscaTipos);
                        db_selectrecord("tipo_transmissao", $rsBuscaTipos, true, $db_opcao, "", "", "", "", "", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="convenio_banco">Convênio com Banco:</label></td>
                    <td>
                        <input size="8" maxlength="8" id="convenio_banco" name="convenio_banco"
                               value="<?=$sConvenioBanco ?>" type="text" />
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Tk29_gerarslipautomaticoreceitaretencao ?>">
                        Gerar Slip Automático das Retenções:
                    </td>
                    <td>
                        <?php
                        $x = array("0" => "Não gera Slips automaticamente",
                                   "1" => "SIM - no momento da apropriação da retenção",
                                   "2" => "SIM - na atualização do movimento na agenda");
                        db_select('k29_gerarslipautomaticoreceitaretencao', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo  @$Tk29_utilizarcontaextra ?>" class="bold">
                        Utiliza Conta Extra Orçamentária:
                    </td>
                    <td>
                        <?php
                        $x = array("f" => "NAO", "t" => "SIM");
                        db_select('k29_utilizarcontaextra', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>

                <tr title="Impacta apenas na geração do Slip e PLanilha da Folha. Se configurado como não usará a conta principal." >
                    <td nowrap class="bold">
                        Utiliza Conta Extra nas Planilhas e Slips da folha:
                    </td>
                    <td>
                        <?php
                        $x = array("f" => "NAO", "t" => "SIM");
                        db_select('k29_folhautilizarcontaextra', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?php echo  @$Tk29_validadatacreditobaixabanco ?>" class="bold">
                        Validar data Crédito da Baixa de Banco:
                    </td>
                    <td>
                        <?php
                        $x = array("f" => "NAO", "t" => "SIM");
                        db_select('k29_validadatacreditobaixabanco', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>

            </table>
        </fieldset>

    </fieldset>

    <input name="<?=($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
           type="submit"
           id="db_opcao"
           value="<?=($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
           <?=($db_botao == false ? "disabled" : "") ?> />
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">

</form>
<script>

    const lookupRecurso = new DBLookUp($('ancoraRecurso'), $('o15_recurso'), $('o15_descr'), {
        "sArquivo": "func_fonterecurso.php",
        "sObjetoLookUp": "db_iframe_orctiporec",
        "sLabel": "Pesquisar Recurso",
        "aCamposAdicionais": ['db_ids_recursos'],
        "aParametrosAdicionais": ["o15_complemento=0"]
    });

    lookupRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2]);
    });

    lookupRecurso.setCallBack('onChange', (erro, retorno) => {
        if (erro) {
            return;
        }

        preencheForm(retorno[2], retorno[0], retorno[3]);
    });

    const preencheForm = (recurso, descricao, id) => {
        $('k29_orctiporecfundeb').value = id;
        $('o15_recurso').value = recurso;
        $('o15_descr').value = descricao;
    };

    $('k29_contapadraoslip').className = 'field-size2';
    $('k13_descr').className = 'field-size8';


    function js_pesquisak29_instit(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                                'db_iframe_db_config',
                                'func_db_config.php?funcao_js=parent.js_mostradb_config1|codigo|nomeinst',
                                'Pesquisa',
                                true);
        } else {
            if (document.form1.k29_instit.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                                    'db_iframe_db_config',
                                    'func_db_config.php?pesquisa_chave=' + document.form1.k29_instit.value + '&funcao_js=parent.js_mostradb_config',
                                    'Pesquisa',
                                    false);
            } else {
                document.form1.nomeinst.value = '';
            }
        }
    }

    function js_mostradb_config(chave, erro) {
        document.form1.nomeinst.value = chave;
        if (erro == true) {
            document.form1.k29_instit.focus();
            document.form1.k29_instit.value = '';
        }
    }

    function js_mostradb_config1(chave1, chave2) {
        document.form1.k29_instit.value = chave1;
        document.form1.nomeinst.value = chave2;
        db_iframe_db_config.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo',
                            'db_iframe_caiparametro',
                            'func_caiparametro.php?funcao_js=parent.js_preenchepesquisa|k29_instit',
                            'Pesquisa',
                            true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_caiparametro.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_pesquisak29_contapadraoslip(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                                'db_iframe_saltes',
                                'func_saltes.php?funcao_js=parent.js_mostrasaltes1|k13_conta|k13_descr',
                                'Pesquisa',
                                true);
        } else {
            if (document.form1.k29_contapadraoslip.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                                    'db_iframe_saltes',
                                    'func_saltes.php?pesquisa_chave=' + document.form1.k29_contapadraoslip.value + '&funcao_js=parent.js_mostrasaltes',
                                    'Pesquisa',
                                    false);
            } else {
                document.form1.k13_descr.value = '';
            }
        }
    }

    function js_mostrasaltes(chave, erro) {
        document.form1.k13_descr.value = chave;
        if (erro == true) {
            document.form1.k29_contapadraoslip.focus();
            document.form1.k29_contapadraoslip.value = '';
        }
    }

    function js_mostrasaltes1(chave1, chave2) {
        document.form1.k29_contapadraoslip.value = chave1;
        document.form1.k13_descr.value = chave2;
        db_iframe_saltes.hide();
    }


</script>
