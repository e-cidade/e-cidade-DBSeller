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
$clcgs_und = new cl_cgs_und;
$clcgs_und->rotulo->label();
$clfar_retirada->rotulo->label();
$clfar_retiradaitens->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_i_cgsund");
$clrotulo->label("fa04_tiporetirada");
$clrotulo->label("fa03_i_codigo");
$clrotulo->label("descrdepto");
$clrotulo->label("fa06_i_matersaude");
$clrotulo->label("fa01_codigobarras");
$clrotulo->label("fa06_t_posologia");
$clrotulo->label("m60_controlavalidade");
$clrotulo->label("fa08_i_cgsund");
$clrotulo->label("z01_v_ender");
$clrotulo->label("z01_v_ident");
$clrotulo->label("m60_codmater");
$clrotulo->label("fa06_i_codigo");
$clrotulo->label("s115_c_cartaosus");
$clrotulo->label("fa41_i_origemreceita");
$clrotulo->label("fa40_c_descr");
$clrotulo->label("fa10_i_programa");
$clrotulo->label("fa04_d_dtvalidade");
$clrotulo->label("m60_descr");
$clrotulo->label("z01_v_cgccpf");

$sWhere = 'fa42_i_departamento = ' . db_getsession('DB_coddepto');

$oDaofar_tiporeceitapadrao = new cl_far_tiporeceitapadrao();
$sSql = $oDaofar_tiporeceitapadrao->sql_query(null, 'fa42_i_tiporeceita', null, $sWhere);
$rs = $oDaofar_tiporeceitapadrao->sql_record($sSql);
if ($oDaofar_tiporeceitapadrao->numrows > 0) {
    db_fieldsmemory($rs, 0);
} else {
    $fa42_i_tiporeceita = 0;
}

?>

<style>

    .cell {
        font-size: 13px;
    }

</style>

<form name="form1" method="post" action="">
    <fieldset class='subcontainer' style='width:800px;'>
        <legend>Dados da retirada</legend>
        <div id="status-microarea" class="alert-danger" style="text-align: center;" role="alert" hidden>
            Paciente sem cadastro em uma microárea!
        </div>
        <table class="form-container" name="tabela1" id="tabela1">

            <tr style="display:none">
                <td class="bold" nowrap='nowrap' title="<?= @$Tfa04_i_unidade ?>">
                    <?php
                    db_input('hiperdia', 5, '', true, 'hidden', 3, "");
                    $validadeContinuado = 0;
                    db_input('validadeContinuado', 5, '', true, 'hidden', 3, "");
                    db_ancora(@$Lfa04_i_unidades, "js_pesquisafa04_i_unidades(true);", 3);
                    ?>
                </td>
                <td nowrap='nowrap' colspan="3">
                    <?php
                    db_input(
                        'fa04_i_unidades',
                        10,
                        @$Ifa04_i_unidades,
                        true,
                        'text',
                        3,
                        " onchange='js_pesquisafa04_i_unidades(false);'"
                    );
                    db_input('descrdepto', 55, @$Idescrdepto, true, 'text', 3, "");
                    db_input('fa04_i_codigo', 5, @$Ifa04_i_codigo, true, 'hidden', 3, "");
                    ?>
                </td>
            </tr>

            <tr>
                <td class="bold" nowrap='nowrap' title="<?= @$Ts115_c_cartaosus ?>">
                    <?= @$Ls115_c_cartaosus ?>
                </td>

                <td nowrap='nowrap'>
                    <?php
                    db_input(
                        's115_c_cartaosus',
                        14,
                        $Is115_c_cartaosus,
                        true,
                        'text',
                        $db_opcao,
                        ' onchange="js_getCgsCns();"'
                    );
                    ?>

                    <label style="margin-left: 23px;"> <?= @$Lz01_v_cgccpf ?> </label>

                    <?php
                    db_input(
                        'z01_v_cgccpf',
                        14,
                        $Iz01_v_cgccpf,
                        true,
                        'text',
                        $db_opcao,
                        ' onchange="js_getCgsCpf();"',
                        '',
                        'white',
                        '',
                        11
                    );
                    ?>

                    <input type="button" id="atualizaCpfCns" title="Atualizar CPF e CNS"
                           name="atualizaCpfCns" value="Atualizar CPF e CNS" onclick="atualizarCpfCns()">
                </td>

                <td class="bold" nowrap='nowrap'></td>

                <td style="display: flex;" nowrap='nowrap'>
                    <b>Tipo de Retirada:</b>
                    <div style="width: 165px;">
                        <?php
                        $aOptions = array("1" => "Normal", "2" => "Não Padronizada");
                        db_select(
                            "fa04_tiporetirada",
                            $aOptions,
                            $Ifa04_tiporetirada,
                            $db_opcao,
                            "onchange=\"js_tiporeceita(this.value);\"  "
                        );
                        ?>
                </td>
                </div>
            </tr>

            <tr>
                <td class="bold" nowrap='nowrap' title="<?= @$Tfa04_i_cgsund ?>">
                    <?php
                    db_ancora(@$Lfa04_i_cgsund, "js_pesquisafa04_i_cgsund(true);", '', '', 'ancora_cgs');
                    ?>
                </td>
                <td nowrap='nowrap'>
                    <?php
                    db_input(
                        'fa04_i_cgsund',
                        10,
                        @$Ifa04_i_cgsund,
                        true,
                        'text',
                        "",
                        "onchange='js_pesquisafa04_i_cgsund(false); js_init();'"
                    );
                    db_input(
                        'z01_v_nome',
                        45,
                        @$Iz01_v_nome,
                        true,
                        'text',
                        1,
                        "onchange=validaCampoNome()"
                    );
                    ?>
                    &nbsp;
                </td>
                <td colspan="2">
                    <input type='button' id='novoCgs' name='novoCgs' value='Novo CGS' onclick="js_novoCgs();">
                    <input type='button' id='historico' name='historico' value='Histórico'
                           onclick="js_abre_historico();">
                    <?php
                    if (db_permissaomenu(date('Y'), db_getsession('DB_modulo'), 6990) == 'true') { ?>
                        <input type='button' id='continuados' name='continuados' value='Continuados'
                               onclick="js_abre_continuados();" disabled>
                        <?php
                    } ?>
                    <input type="button" id="demandaReprimida" name="demandaReprimida" value="Demanda Reprimida"
                           disabled>
                </td>
            </tr>

            <tr>
                <td class="bold" nowrap='nowrap'>
                    <b>Nome Social:</b>
                </td>
                <td colspan="2">
                    <?php
                    $corNomeSocial = !empty($z01_nome_social) ? "#97CAE6" : "";
                    db_input(
                        'z01_nome_social',
                        58,
                        2,
                        true,
                        'text',
                        $db_opcao,
                        "",
                        "",
                        $corNomeSocial
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold" nowrap='nowrap'>
                    <b>Tipo de Receita:</b>
                </td>
                <td nowrap='nowrap' title="<?= @$Tfa04_d_dtvalidade ?>">
                    <?php
                    $campos = 'fa03_i_codigo, fa03_c_descr, fa03_c_requisitante,';
                    $campos .= 'fa03_c_posologia,fa03_i_prescricaomedica,';
                    $campos .= 'fa03_c_profissional,fa03_c_numeroreceita,fa03_data_prescricao,fa03_dias_prescricao,';
                    $campos .= 'fa03_numero_notificacao';
                    $sSql = $clfar_tiporeceita->sql_query_file('', $campos, 'fa03_c_descr', 'fa03_i_ativa = 1');
                    $result_tprec = $clfar_tiporeceita->sql_record($sSql);
                    ?>
                    <select name="fa04_i_tiporeceita" id="fa04_i_tiporeceita" rel='ignore-css'
                            onchange="js_tpreceita();">
                        <option value=""></option>
                        <?php
                        for ($y = 0; $y < pg_num_rows($result_tprec); $y++) {
                            db_fieldsmemory($result_tprec, $y); ?>
                            <?php
                            $sValor = "$fa03_c_requisitante/$fa03_i_codigo/$fa03_c_posologia/";
                            $sValor .= "$fa03_i_prescricaomedica/$fa03_c_profissional/$fa03_c_numeroreceita/";
                            $sValor .= "$fa03_data_prescricao/$fa03_dias_prescricao/$fa03_numero_notificacao";

                            ?>
                            <option value="<?= $sValor ?>"
                                <?= (@$fa04_i_tiporeceita == "$fa03_c_requisitante/$fa03_i_codigo") ? "selected" : "" ?>
                            >
                                <?= $fa03_c_descr ?>
                            </option>
                            <?php
                        } ?>
                    </select>
                </td>
                <td>
                    <?php
                    db_ancora($Lfa04_i_receita, 'js_pesquisareceita(true)', '');
                    ?>

                </td>
                <td>
                    <?php
                    db_input(
                        'fa04_i_receita',
                        10,
                        $Ifa04_i_receita,
                        true,
                        'text',
                        $db_opcao,
                        'onchange="js_pesquisareceita(false);"'
                    );
                    ?>
                    <label style="padding-left: 10px;" class="bold" nowrap='nowrap'>
                        Validade:
                    </label>
                    <?php
                    db_inputdata(
                        'fa04_d_dtvalidade',
                        $fa04_d_dtvalidade_dia,
                        $fa04_d_dtvalidade_mes,
                        $fa04_d_dtvalidade_ano
                    );
                    ?>
                </td>
            </tr>


            <tr>
                <td class="bold" nowrap='nowrap' title="<?= $Tfa04_data_prescricao ?>">
                    <?= $Lfa04_data_prescricao ?>
                </td>
                <td nowrap='nowrap'>
                    <?php
                    db_inputdata(
                        'fa04_data_prescricao',
                        "",
                        "",
                        "",
                        true,
                        'text',
                        1,
                        " onchange='isDataPrescricaoValida();'",
                        "",
                        "",
                        "parent.isDataPrescricaoValida()"
                    );
                    ?>
                </td>
                <td class="bold" nowrap='nowrap'>
                    Número de Notificação:
                </td>
                <td>
                    <?php
                    db_input(
                        'fa04_numeronotificacao',
                        10,
                        $Ifa04_numeronotificacao,
                        true,
                        'text',
                        $db_opcao,
                        " onBlur='validaNumeroNotificacao();'"
                    );
                    ?>
                </td>
            </tr>


            <tr style=" <?= ($oConfigFarmacia->fa02_i_origemreceita == 1 ? "''" : 'display: none;') ?>">
                <td class="bold" nowrap='nowrap' title="<?= @$Tfa41_i_origemreceita ?>">
                    <?php
                    db_ancora($Lfa41_i_origemreceita, 'js_pesquisafa41_i_origemreceita(true)', '') ?>
                </td>
                <td nowrap='nowrap' colspan="3">
                    <?php
                    db_input(
                        'fa41_i_origemreceita',
                        10,
                        $Ifa41_i_origemreceita,
                        true,
                        'hidden',
                        $db_opcao,
                        'js_pesquisafa41_i_origemreceita(true)'
                    );
                    db_input('fa40_c_descr', 45, $Ifa40_c_descr, true, 'text', $db_opcao);
                    ?>
                </td>
            </tr>
            <tr style="height: 3px;"></tr>
            <tr>
                <td nowrap title="<?= @$Tfa04_i_profissional ?>">
                    <?php
                    db_ancora(
                        @$Lfa04_i_profissional,
                        "js_pesquisafa04_i_profissional(true);",
                        $db_opcao,
                        '',
                        'ancora_profissional'
                    );
                    ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input(
                        'fa04_i_profissional',
                        10,
                        @$Ifa04_i_profissional,
                        true,
                        'text',
                        "",
                        " onchange='js_pesquisafa04_i_profissional(false);'"
                    );
                    db_input('z01_nome', 45, @$z01_nome, true, 'text', 1);

                    if (db_permissaomenu(date('Y'), 1000004, 8675) == 'true') {
                        ?>
                        <input type="button" id="cadProf" title="Cadastro de Profissionais Fora da Rede"
                               name="cadProf" value="Cadastro de Profissionais" onclick="js_abreCadProf();">
                        <?php
                    }
                    ?>

                </td>
            </tr>


            <tr <?= (!isset($fa08_i_cgsund)) ? "style=\"display:none\"" : "" ?> id="linha1">
                <td nowrap title="<?= @$Tfa08_i_cgsund ?>">
                    <?php
                    db_ancora(@$Lfa08_i_cgsund, "js_pesquisafa08_i_cgsund(true);", 1); ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input(
                        'fa08_i_cgsund',
                        10,
                        @$Ifa08_i_cgsund,
                        true,
                        'text',
                        "",
                        " onchange='js_pesquisafa08_i_cgsund(false);'"
                    );
                    db_input(
                        'z01_v_nomecgs',
                        55,
                        @$z01_v_nomecgs,
                        true,
                        'text',
                        1,
                        "onchange='limpaDadosRequisitante();'"
                    );
                    ?>
                </td>
            </tr>
            <tr <?= (!isset($ender)) ? "style=\"display:none\"" : "" ?> id="linha1">
                <td nowrap title="<?= @$Tz01_v_ender ?>">
                    <?php
                    db_ancora(@$Lz01_v_ender, "", 3); ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input('ender', 45, @$Iz01_v_ender, true, 'text', 1);
                    db_input('numero', 10, @$z01_i_numero, true, 'text', 1);
                    ?>
                </td>
            </tr>
            <tr <?= (!isset($ident)) ? "style=\"display:none\"" : "" ?> id="linha1">
                <td nowrap title="<?= @$Tz01_v_ident ?>">
                    <?= @$Lz01_v_ident ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input('ident', 15, @$Iz01_v_ident, true, 'text', 1, '', '', '', '', 11); ?>
                </td>
            </tr>
        </table>
    </fieldset>


    <fieldset class='subcontainer' style='width:800px;'>
        <legend>Medicamentos</legend>
        <table class='form-container' id="tabela_medicamento">

            <tr>
                <td>
                    <?= $Lfa01_codigobarras ?>
                </td>
                <td>
                    <?php
                    db_input(
                        'fa01_codigobarras',
                        20,
                        $Ifa01_codigobarras,
                        true,
                        'text',
                        1,
                        " onchange='pesquisaMedicamentoCodigoBarras();'"
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td class='bold field-size3' rel="ignore-css" nowrap='nowrap' title="Pesquise o medicamento.">
                    <?php
                    db_ancora($Lfa06_i_matersaude, "jsPesquisaMedicamento(true);", $db_opcao);
                    ?>
                </td>
                <td nowrap='nowrap'>
                    <?php

                    db_input(
                        'fa06_i_matersaude',
                        10,
                        $Ifa06_i_matersaude,
                        true,
                        'text',
                        1,
                        " onchange='jsPesquisaMedicamento(false);'"
                    );

                    db_input('m60_descr', 45, $Im60_descr, true, 'text', 1, 'onblur="js_callBackMedicamento();" ');

                    $mostra = $oConfigFarmacia->fa02_informa_receita_medicamento == 't' ? 'text' : 'hidden';
                    db_input('tipoReceitaMedicamento', 40, '', true, $mostra, 3);
                    ?>
                    <input name="checkContinuado" type="checkbox" id="checkContinuado" onclick="js_ativaContinuado();">
                    <b>Continuado</b>
                </td>
            </tr>
            <tr id="linha_continuado" style="display: none;">
                <td colspan="2">
                    <fieldset class='separator' style='width: 98%;'>
                        <legend>Continuados</legend>
                        <table>
                            <tr>
                                <td nowrap="nowrap" class='bold field-size3'>
                                    Quantidade:
                                </td>
                                <td nowrap="nowrap">
                                    <?php
                                    db_input(
                                        'contiQuant',
                                        10,
                                        "",
                                        true,
                                        'text',
                                        1,
                                        'onchange="$(\'fa06_f_quant\').value=this.value;"'
                                    );
                                    ?>
                                </td>
                                <td nowrap="nowrap" class='bold'>
                                    Frequência:
                                </td>
                                <td nowrap="nowrap">
                                    <?php
                                    db_input('contiFreq', 10, "", true, 'text', 1, "onchange='js_prazo();'"); ?>
                                </td>
                                <td nowrap="nowrap" class='bold'>
                                    Margem:
                                </td>
                                <td nowrap="nowrap">
                                    <?php
                                    db_input('contimargem', 10, "", true, 'text', 1, "onchange='js_margem();'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap="nowrap" class="bold field-size3">
                                    <?php
                                    db_ancora(@$Lfa10_i_programa, "js_pesquisafa10_i_programa(true);", $db_opcao);
                                    ?>
                                </td>
                                <td nowrap="nowrap" colspan="5">
                                    <?php
                                    db_input(
                                        'fa10_i_programa',
                                        10,
                                        @$Ifa10_i_programa,
                                        true,
                                        'text',
                                        "",
                                        " onchange='js_pesquisafa10_i_programa(false);'"
                                    );
                                    db_input('fa12_c_descricao', 42, @$fa12_c_descricao, true);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td class="bold" nowrap="nowrap">
                    Quantidade Dispensada:
                </td>
                <td nowrap="nowrap">
                    <?php
                    db_input(
                        'fa06_f_quant',
                        10,
                        @$Ifa06_f_quant,
                        true,
                        'text',
                        "",
                        "onchange='js_quantidade(this.value)'"
                    );
                    ?>
                    <b><a name="lotes" id="lotes" href='' onclick="js_mostraLotes('','fa06_f_quant'); return false;"
                          style="padding-left: 9px; padding-right: 9px;">Ver Lotes</a> </b>
                    &nbsp;&nbsp;
                    <b>Saldo :</b>
                    <?php
                    db_input('quant_disp', 5, @$Ifa06_f_quant, true, 'text', 3, ""); ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" name="fa06_t_posologia" id="fa06_t_posologia" value="Posologia" disabled
                           onclick="js_lancar();">
                    <input type="button" name="ultimas" id="ultimas" value="Últimas Retiradas" disabled
                           onclick="js_ultimas();">
                    <?php
                    db_input('lote_edit', 10, 'Lote', true, 'hidden', 3, "");
                    db_input('lote', 10, 'Lote', true, 'hidden', 3, "");
                    db_input('pontoPedido', 10, 'Lote', true, 'hidden', 3, "");
                    ?>
                    <input type="hidden" name="validade_edit" id="validade_edit" value="">
                    <input type="hidden" name="posologia_edit" id="posologia_edit" value="">
                    <input type="hidden" id="localizacao" value="">
                    <input type="button" id="demandaReprimidaMed" name="demandaReprimidaMed"
                           value="Incluir Demanda Reprimida" disabled>
                    <input name="incluir" type="button" id="incluir" value="Incluir" onclick='js_incluiremedio();'>
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset class='subcontainer' style='width:1300px;'>
        <legend>Medicamentos Dispensados</legend>
        <div id="gridRemedios"></div>
        <select name="DadosGridRemedios" id="DadosGridRemedios" style="display:none;"></select>
    </fieldset>


    <div class="container">

        <input name="confirmar" type="button" id="confirmar" value="Confirmar" disabled onClick='js_confirma();'>
        <input name="comprovante" type="button" id="comprovante" value="Comprovante" disabled onclick='js_imprime();'>
        <input name="retirada"
               type="button"
               id="retirada"
               value="Nova Retirada"
               disabled
               onclick='location.href="far1_far_retirada001.php"'>
        <input name="continuar"
               type="button"
               id="continuar"
               value="Continuar Retirada"
               disabled
               onClick='location.href="far1_far_retirada001.php?fa04_i_cgsund="+$(fa04_i_cgsund).value
           +"&lContinuar=true&z01_v_nome="+$(z01_v_nome).value
           +"&z01_nome_social="+$(z01_nome_social).value
           +"&s115_c_cartaosus="+$(s115_c_cartaosus).value
           +"&continuandoRetirada="+true
           +"&z01_v_cgccpf="+$(z01_v_cgccpf).value'>

        <input nome="m40_codigo" id="m40_codigo" type="hidden" value="">

        <?php db_input('continuandoRetirada', 5, '', false, 'hidden'); ?>
    </div>

</form>


<?php
// PHP responsavel pela busca dos medicamentos prestes a vencer
$sSql = $clmatparam->sql_query_file("", "m90_prazovenc", "", "");
$result_venc = $clmatparam->sql_record($sSql);
if (pg_num_rows($result_venc) <= 0 || pg_num_rows($result_venc) == false) {
    db_msgbox("Parametro do material não encontrado!");
} else {
    db_fieldsmemory($result_venc, 0);
}
$data_ini = date("Y-m-d", db_getsession("DB_datausu"));
$data_fim = calcula_data($data_ini, $m90_prazovenc);
$departamento = db_getsession("DB_coddepto");
$nome_departamento = db_getsession("DB_nomedepto");
//SQL para buscar os medicamentos proximos da data de vencimento
$sCampos = " fa01_i_codigo || ' - ' || m60_descr as item, ";
$sCampos .= " m60_codmater, ";
$sCampos .= " m77_dtvalidade, ";
$sCampos .= " m77_lote, ";
$sCampos .= " (m71_quant - m71_quantatend) as m70_quant, ";
$sCampos .= " m77_dtvalidade - '$data_ini' as dias ";
$sWhere = "     m70_coddepto = $departamento ";
$sWhere .= " and m77_dtvalidade is not null ";
$sWhere .= " and m77_dtvalidade between '$data_ini' and '$data_fim'";
$sWhere .= " and m60_ativo = 't'";
$sWhere .= " and (m71_quant - m71_quantatend) > 0";
$oDaoFarMaterSaude = new cl_far_matersaude;
$sql_venc = $oDaoFarMaterSaude->sql_query_matmater("", $sCampos, "m77_dtvalidade asc", $sWhere);
$result_venc = $oDaoFarMaterSaude->sql_record($sql_venc);

$num_rows = $oDaoFarMaterSaude->numrows;
if ($oDaoFarMaterSaude->numrows <= 0) {
    ?>
    <div class="container">

        <table>
            <tr>
                <td align='center'>
                    <big><b>Nenhum medicamento pr&oacute;ximo &agrave; data de validade</b></big>
                </td>
            </tr>
        </table>
    </div>
    <?php
} else {
    ?>
    <!-- TABELA COM OS MEDICAMENTOS PRESTES A VENCER -->
    <div class="container">
        <table border='1'>
            <tr>
                <td colspan='6' align='center' nowrap>
                    <b><big>Medicamentos com prazo de validade a vencer</big></b>
                </td>
            </tr>
            <tr>
                <td align='center'>
                    <b>Medicamento</b>
                </td>
                <td>
                    <b>Cód. Mater</b>
                </td>
                <td>
                    <b>Lote</b>
                </td>
                <td>
                    <b>Dias</b>
                </td>
                <td>
                    <b>Validade</b>
                </td>
                <td>
                    <b>Quantidade</b>
                </td>
            </tr>
            <?php
            function formata_data($dData, $iTipo = 1)
            {
                if ($iTipo == 1) {
                    $dData = explode('/', $dData);
                    $dData = $dData[2] . '-' . $dData[1] . '-' . $dData[0];
                    return $dData;
                }

                $dData = explode('-', $dData);
                $dData = $dData[2] . '/' . $dData[1] . '/' . $dData[0];
                return $dData;
            }


            for ($i = 0; $i < $num_rows; $i++) {
                db_fieldsmemory($result_venc, $i);
                $m77_dtvalidade = formata_data($m77_dtvalidade, 2);
                echo "<tr>
            <td>
              $item
            </td>
            <td>
              $m60_codmater
            </td>
            <td>
              $m77_lote
            </td>
            <td>
              $dias
            </td>
            <td>
              $m77_dtvalidade
            </td>
            <td>
              $m70_quant
            </td>
          </tr>";
            }
            ?>
        </table>
    </div>
    <?php
}
?>
<div id="divAvisoRetirada">
    <div class="alert alert-info" id="alertAvisoRetirada"></div>
    <div class="subcontainer">
        <table id="table-aviso-retirada"
               class="table table-sm">
        </table>
    </div>
</div>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script>
    $.noConflict();

    const alertAvisoRetirada = document.getElementById('alertAvisoRetirada');
    const divAvisoRetirada = document.getElementById('divAvisoRetirada');
    const tabelaAvisoRetirada = jQuery("#table-aviso-retirada");

    let valorCgsEncontrado = '';
    let codigoCartaoSusEncontrado = '';
    let cpfAtual = '';
    let cnsAtual = '';

    //Variável que indica quando um buscar por cgs é feita através do campo de cgs ou do campo nome
    let buscouPeloNomeOuCgs = false;

    document.getElementById('demandaReprimida').addEventListener('click', () => openDemandaReprimida(false));
    document.getElementById('demandaReprimidaMed').addEventListener('click', () => openDemandaReprimida(true));

    const fechaModal = () => {
        if (!!windowAvisoRetirada.oDBMask) {
            windowAvisoRetirada.oDBMask.destroy();
        }
        windowAvisoRetirada.hide();
    }

    const windowAvisoRetirada = new windowAux('windowAvisoRetirada', 'Últimas Retiradas', 810, 450);
    windowAvisoRetirada.setContent(divAvisoRetirada);
    windowAvisoRetirada.setShutDownFunction(() => {
        fechaModal();
    });

    var lImpressaoCupom = <?=$oConfigFarmacia->fa02_utilizaimpressoratermica == 't' ? 'true' : 'false'?>;
    <?php
    if (isset($lAbreAcompanhamento) && $lAbreAcompanhamento == 'true') {
        echo "js_abreAcompanhamentoHiperdia($iCgs, $iRetirada);";
    }
    ?>
    //inicializa rotina
    objGridRemedios = new DBGrid('objGridRemedios');
    <?php if ($fa42_i_tiporeceita != 0) : ?>
    js_select_tipo_receita();
    <?php endif; ?>
    js_ativaContinuado();
    document.getElementById('m60_descr').addEventListener('blur', () => {
        js_callBackMedicamento();
        oAutoComplete.oObjAutoComplete.hideDiv();
    });
    //Autocomplete do medicamento
    oAutoComplete = new dbAutoComplete(document.form1.m60_descr, 'far4_retirada_autonomeRPC.php?tipo=1');
    oAutoComplete.setTxtFieldId(document.getElementById('fa06_i_matersaude'));
    oAutoComplete.show();
    oAutoComplete.setCallBackFunction(function (id, label, oRetorno) {
        if (!validaRetiradaMedicamento()) {
            document.form1.m60_descr.value = '';
            return false;
        }
        document.form1.fa06_i_matersaude.value = id;
        document.form1.fa01_codigobarras.value = oRetorno.codigo_barras;
        document.form1.m60_descr.value = label;
        document.form1.m60_descr.blur();
    });

    const divAlert = document.getElementById('status-microarea');
    const inputCgs = {
        id: document.getElementById('fa04_i_cgsund'),
        nome: document.getElementById('z01_v_nome')
    };
    const validaCgs = new ValidaCgs(inputCgs);

    window.onload = () => {
        validaCgs.cadastroMicroarea(inputCgs, divAlert);
        bloqueiaCamposContinuacaoRetirada()
    }

    // Autocomplete do profissional
    oAutoComplete2 = new dbAutoComplete(document.form1.z01_nome, 'far4_retirada_autonomeRPC.php?tipo=5');
    oAutoComplete2.setTxtFieldId(document.getElementById('fa04_i_profissional'));
    oAutoComplete2.setHeightList(180);
    oAutoComplete2.show();
    oAutoComplete2.setCallBackFunction((id, label) => {
        document.form1.fa04_i_profissional.value = id;
        document.form1.z01_nome.value = label;
        document.form1.z01_nome.blur();
    });

    // Autocomplete do CGS
    oAutoComplete3 = new dbAutoComplete(document.form1.z01_v_nome, 'far4_retirada_autonomeRPC.php?tipo=3');
    oAutoComplete3.setTxtFieldId(document.getElementById('fa04_i_cgsund'));
    oAutoComplete3.setHeightList(390);
    oAutoComplete3.show();
    oAutoComplete3.setCallBackFunction(function (id, label) {
        document.form1.fa04_i_cgsund.value = id;
        document.form1.z01_v_nome.value = label;

        if (document.form1.continuados !== undefined) {
            document.form1.continuados.disabled = false;
        }
        document.form1.demandaReprimida.disabled = false;
        $('s115_c_cartaosus').value = '';
        js_init();
        document.form1.fa04_i_cgsund.dispatchEvent(new Event('change'));
        document.form1.z01_v_nome.blur();
    });

    // Autocomplete do CGS
    oAutoComplete4 = new dbAutoComplete(document.form1.z01_v_nomecgs, 'far4_retirada_autonomeRPC.php?tipo=3');
    oAutoComplete4.setTxtFieldId(document.getElementById('fa08_i_cgsund'));
    oAutoComplete4.show();
    oAutoComplete4.setCallBackFunction(function (id, label) {
        document.form1.fa08_i_cgsund.value = id;
        document.form1.z01_v_nomecgs.value = label;
        document.form1.z01_v_nomecgs.blur();
        js_pesquisafa08_i_cgsund(false);
    });

    // Autocomplete da Origem da Receita
    oAutoComplete5 = new dbAutoComplete(document.form1.fa40_c_descr, 'far4_retirada_autonomeRPC.php?tipo=4');
    oAutoComplete5.setTxtFieldId(document.getElementById('fa41_i_origemreceita'));
    oAutoComplete5.show();
    oAutoComplete5.setCallBackFunction((id, label) => {
        document.form1.fa41_i_origemreceita.value = id;
        document.form1.fa40_c_descr.value = label;
        document.form1.fa40_c_descr.blur();
    });
    sUrl = 'far1_far_retiradaRPC.php';

    // Array global - popula as linhas da tabela dinamicamente
    aGridRemediosAtual = new Array();

    jQuery(document).ready(jQuery => {
        tabelaAvisoRetirada.bootstrapTable({
            height: 350,
            search: true,
            columns: [
                {
                    field: 'data',
                    title: 'Data',
                    halign: 'center',
                    align: 'center',
                    width: 100,
                    formatter: value => {
                        return `<strong>${value}</strong>`;
                    }
                },
                {
                    field: 'departamento',
                    title: 'Departamento',
                    halign: 'center',
                    align: 'left',
                    width: 300
                },
                {
                    field: 'medicamento',
                    title: 'Medicamento',
                    halign: 'center',
                    align: 'left',
                    width: 300
                },
                {
                    field: 'quantidade',
                    title: 'Quantidade',
                    halign: 'center',
                    align: 'center',
                    width: 80
                }
            ]
        });
    });

    if ($('fa04_i_cgsund').value != "") {
        document.form1.demandaReprimida.disabled = false;
        if (document.form1.continuados != undefined) {
            document.form1.continuados.disabled = false;
        }
        js_init();
    }

    function openDemandaReprimida(incluiRemedio = false) {
        if ($F('fa04_i_cgsund') == '') {
            alert('Informe o Paciente!');
            return;
        }

        const getUrl = () => {
            let url = 'far1_demanda_reprimida.php';

            let params = [];
            params.push(`paciente=${$F('fa04_i_cgsund')}`);

            if (incluiRemedio) {
                params.push(`medicamento=${$F('fa06_i_matersaude')}`);
            }

            return `${url}?${params.join('&')}`;
        };

        let top = 20;
        let left = 5;
        let height = screen.availHeight - 210;
        let width = screen.availWidth - 30;

        js_OpenJanelaIframe(
            "",
            "db_iframe_demandareprimida",
            getUrl(),
            "Cadastro de Demanda Reprimida",
            true,
            top,
            left,
            width,
            height
        );
    }

    function getAvisoRetirada() {
        if ($F('fa04_i_cgsund') == '') {
            return;
        }

        const formData = new FormData();

        formData.append('acao', "getAvisoRetirada");
        formData.append('idCgs', $F('fa04_i_cgsund'));

        HttpClient.post('far_retirada.RPC.php', {body: formData}).then(response => {
            tabelaAvisoRetirada.bootstrapTable('removeAll');
            if (response.erro) {
                return;
            }
            windowAvisoRetirada.show(0, 0, true);

            alertAvisoRetirada.innerHTML = `Últimas retiradas encontradas no período de ${response.periodo} dias!`;
            for (let retirada of response.retiradas) {
                for (let medicamento of retirada.medicamentos) {
                    tabelaAvisoRetirada.bootstrapTable('append', {
                        data: retirada.data,
                        departamento: retirada.departamento,
                        medicamento: medicamento.m60_descr,
                        quantidade: medicamento.fa06_f_quant
                    });
                }
            }

            // fecha o modal com o click da tecla enter
            document.body.addEventListener('keydown', event => {
                if (event.which == 13) {
                    fechaModal();
                }
            });
        });
    }

    async function validaCgsMunicipio(idCgs) {
        if (idCgs === '') {
            return true;
        }

        const formData = new FormData();
        formData.append('acao', 'validaCgsMunicipio');
        formData.append('idCgs', idCgs);

        const response = await HttpClient.post('far_retirada.RPC.php', {body: formData});
        if (response.erro || !response.validar) {
            return true;
        }

        return new Promise(resolve => {
            const mensagem = `Paciente: ${$F('z01_v_nome')} não reside no município.
                Cadastrado em: ${response.municipio}. Deseja continuar a retirada?`;
            alertify.confirm(mensagem, confirm => {
                if (!confirm) {
                    document.form1.fa04_i_cgsund.value = ''
                    document.form1.z01_v_nome.value = '';
                    document.form1.s115_c_cartaosus.value = '';
                    document.form1.z01_v_cgccpf.value = '';
                    return resolve(false);
                }
                return resolve(true);
            });
        });
    }

    function js_ajax(objParam, jsRetorno, sUrl, lAsync) {
        var mRetornoAjax;

        if (sUrl == undefined) {
            sUrl = 'far1_far_retiradaRPC.php';
        }

        if (lAsync == undefined) {
            lAsync = true;
        }

        var objAjax = new Ajax.Request(
            sUrl,
            {
                method: 'post',
                asynchronous: lAsync,
                parameters: 'json=' + Object.toJSON(objParam),
                onComplete: function (objAjax) {
                    var evlJS = jsRetorno + '( objAjax );';
                    return mRetornoAjax = eval(evlJS);
                }
            }
        );

        return mRetornoAjax;
    }

    function js_abre_continuados() {
        // Grava o número de remédios da grid antes da abertura da modal de Continuados
        iTam = $('DadosGridRemedios').length;

        // Para cada registro de remédio, persiste a informação no array global
        for (x = 0; x < iTam; x++) {
            aLinhaGridRemedios = new Array();
            sText = $('DadosGridRemedios').options[x].text;
            avet = sText.split('#');

            /*
        * Descrição do array Avet
        * avet[0] - Tipo do Medicamento. C - Controlado, N - Normal
        * avet[1] - Código do Medicamento
        * avet[2] - Descrição do Medicamento
        * avet[3] - Próx. Disp.
        * avet[4] - Freq.
        * avet[5] - Marg.
        * avet[6] - Qtde.
        * avet[7] - Saldo do Produto
        * avet[8] - Val. Continuado
        * avet[9] - Estoque
        * avet[10] - Validade
        * avet[11] - Label Lote
        * avet[12] - Qtde. Disp.
        * avet[13] - Observação
        * avet[14] - Lote
        * avet[15] - Ponto Pedido
        * avet[16] - false
        * avet[17] - Hiperdia ~ boolean
        * avet[18] - Data Fim
        * avet[19] - Localização no estoque
        * */
            /*
        * Descrição Linhas da Grid Remedios
        * aLinhaGridRemedios[0] - avet[1] - Código Medicamento
        * aLinhaGridRemedios[1] - avet[2] - Descrição Medicamento
        * aLinhaGridRemedios[2] - avet[3] - Próx. Disp.
        * aLinhaGridRemedios[3] - avet[4] - Freq.        ~~~~~~~ está recebendo avet[12] - Qtde. Disp.
        * aLinhaGridRemedios[4] - avet[5] - Marg.
        * aLinhaGridRemedios[5] - avet[6] - Qtde.
        * aLinhaGridRemedios[6] - avet[7] - Saldo do Produto
        * aLinhaGridRemedios[7] - avet[8] - Val. Continuado
        * aLinhaGridRemedios[8] - avet[9] - Estoque
        * aLinhaGridRemedios[9] - avet[10] - Validade
        * aLinhaGridRemedios[10] - avet[11] - Label Lote
        * aLinhaGridRemedios[11] - contador x do FOR
        * */
            if (avet[0] == "N") {
                aLinhaGridRemedios[0] = avet[1];
                aLinhaGridRemedios[1] = avet[2];
                aLinhaGridRemedios[2] = avet[3];
                //aLinhaGridRemedios[3]   = avet[4];
                aLinhaGridRemedios[3] = avet[12];
                aLinhaGridRemedios[4] = avet[5];
                aLinhaGridRemedios[5] = avet[6];
                aLinhaGridRemedios[6] = avet[7];
                aLinhaGridRemedios[7] = avet[8];
                aLinhaGridRemedios[8] = avet[9];
                aLinhaGridRemedios[9] = avet[10];
                aLinhaGridRemedios[10] = avet[11];
                aLinhaGridRemedios[11] = x;

                bloqueio = '';
                // Validação de produto 'aNormal'
                if (avet[0] != 'N') {
                    aPaint[aPaint.length] = x;
                    // Se Controlado, desabilita
                    if (avet[0] == 'C') {
                        bloqueio = 'disabled';
                    }
                    <?php if ($oConfigFarmacia->fa02_i_validavencimento > 1) : ?>
                    // Valida se possuir data de validade
                    if (avet[10] != '' && avet[10] != undefined) {
                        aData = avet[10].split('/');
                        validade = new Date(aData[2], (aData[1] - 1), aData[0]);
                        // Valida se não possui Próx. Disp.
                        if (avet[3] == '') {
                            <?php $aVet = explode('-', $dHoje);
                            echo " hoje = new Date($aVet[0]," . ($aVet[1] - 1) . ",$aVet[2]); ";?>
                            tratamento = somaDataDiaMesAno(hoje, avet[4], 0, 0);
                        } else {
                            aData = avet[3].split('/');
                            tratamento = new Date(aData[2], (aData[1] - 1), aData[0]);
                        }
                        // Valida se data de vencimento persistir durante tratamento
                        if (validade.getTime() <= tratamento.getTime()) {
                            if (bAlertas != undefined && bAlertas == true) {
                                alert('Medicamento ' + avet[2] + ' vai vencer durante o tratamento!');
                            }
                            <?php if ($oConfigFarmacia->fa02_i_validavencimento == 3) : ?>
                            $('validadeContinuado').value = 1;
                            <?php endif; ?>
                        }
                    }
                    <?php endif; ?>
                }
                // Controle da data de Vencimento do Continuado
                if (avet[10] != '' && avet[10].split('/').length == 3) {
                    aData = avet[10].split('/');
                    validade = aData[2].substr(0, 4) + aData[1] + aData[0];
                    <?php $aVet = explode('-', $dHoje);
                    echo " hoje = $aVet[0]$aVet[1]$aVet[2]; ";?>
                    if (validade <= hoje) {
                        if (bAlertas != undefined && bAlertas == true) {
                            alert(' O lote do medicamento ' + avet[2] + ' está vencido! ');
                        }
                        <?php if ($oConfigFarmacia->fa02_i_validavencimento == 3) { ?>
                        $('validadeContinuado').value = 1;
                        <?php } ?>
                    }
                }
                // Controle de Saldo -  Se tipo N seta -1, se tipo C seta 0
                if (avet[7] == '') {
                    if (avet[0] == 'N') {
                        saldo = -1;
                    } else {
                        saldo = 0;
                    }
                } else {
                    saldo = avet[7]
                }
                // Controle de estoque
                if (avet[9] == '') {
                    estoque = 0;
                } else {
                    estoque = avet[9];
                }
                // Saldo do Medicamento
                if (avet[12] > 0) {
                    $('confirmar').disabled = false;
                    $('retirada').disabled = false;
                }

                if (avet[0] != 'N') {
                    if (avet[17] == 't') {
                        sLibera = 'checked';
                    } else {
                        sLibera = '';
                    }
                    if (avet[7] == 0 || avet[9] == 0) {
                        sSemsaldo = 'disabled';
                    } else {
                        sSemsaldo = '';
                    }
                }

                objGridRemedios.addRow(aLinhaGridRemedios);
                // Informação de LOTE movida para posição [12]
                aLinhaGridRemedios[12] = avet[14];
                aLinhaGridRemedios[13] = avet[14];
                aLinhaGridRemedios[14] = avet[14];
                //Localização no estoque
                aLinhaGridRemedios[15] = avet[19];
                aGridRemediosAtual.push(aLinhaGridRemedios);
            }
        }

        if (document.form1.fa04_i_cgsund.value != '' && document.form1.fa04_i_cgsund.value == parseInt(document.form1.fa04_i_cgsund.value)) {
            sCgs = 'fa11_i_cgsund=' + document.form1.fa04_i_cgsund.value;
            sBotaoContinuado = '&lBotao=true';

            var iTop = 20;
            var iLeft = 5;
            var iHeight = screen.availHeight - 210;
            var iWidth = screen.availWidth - 35;

            js_OpenJanelaIframe(
                "",
                "db_iframe_continuados",
                "far1_far_controlemed001.php?" + sCgs + sBotaoContinuado,
                "Medicamentos Continuados",
                true,
                iTop,
                iLeft,
                iWidth,
                iHeight
            );

            if (document.getElementById('fechardb_iframe_continuados') != undefined) {
                document.getElementById('fechardb_iframe_continuados').onclick = function () {
                    js_fechaFrameContinuados();
                }
            }
        } else {
            alert('Selecione um CGS Antes!');
            document.form1.continuados.disabled = true;
        }
    }

    function js_abreCadProf() {
        var iTop = 20;
        var iLeft = 5;
        var iHeight = screen.availHeight - 210;
        var iWidth = screen.availWidth - 35;

        if ($F('fa04_i_profissional') == '') {
            sGet = 's154_c_nome=' + $F('z01_nome') + '&sd03_i_tipo=2&lBotao=true';
            js_OpenJanelaIframe(
                "",
                "db_iframe_cadprof",
                "sau1_sau_medicosforarede001.php?" + sGet,
                "Cadastro de Profissionais Fora da Rede",
                true,
                iTop,
                iLeft,
                iWidth,
                iHeight
            );
        } else {
            var oParam = new Object();
            oParam.exec = 'verificaForaRede';
            oParam.iMedico = $F('fa04_i_profissional');

            if (js_ajax(oParam, 'js_retornoVerificaForaRede', 'sau4_ambulatorial.RPC.php', false)) {
                sGet = 'chavepesquisa=' + $F('fa04_i_profissional') + '&lBotao=true';
                js_OpenJanelaIframe(
                    '',
                    "db_iframe_cadprof",
                    "sau1_sau_medicosforarede002.php?" + sGet,
                    'Cadastro de Profissionais Fora da Rede',
                    true,
                    iTop,
                    iLeft,
                    iWidth,
                    iHeight
                );
            } else {
                alert('Profissional selecionado não é um profissional de fora da rede.');
            }
        }
    }

    function js_retornoVerificaForaRede(oRetorno) {
        oRetorno = JSON.parse(oRetorno.responseText);
        return oRetorno.lForaRede == 'true';
    }

    function js_fechaFrameContinuados() {
        js_init();
        db_iframe_continuados.hide();
    }

    function js_init() {
        var oMarcar = '<input name="marcarTodos" type="button" id="marcarTodos" ';
        oMarcar += 'value="M" style="margin: 0;" onClick="js_marcarTodos();">';
        var arrHeader = [
            "Cód.",
            "Medicamento",
            "Próx. Disp.",
            "Freq.",
            "Marg.",
            "Qtde.",
            "Saldo",
            "Val. Continuado",
            "Estoque",
            "Validade",
            "Lote ",
            'Localização',
            "Qtde. Disp.",
            '<label style="margin-left: 10px; margin-right: 10px;">'
            + "Dispensa"
            + '</label>'
            + oMarcar
        ];
        objGridRemedios = new DBGrid('objGridRemedios');
        objGridRemedios.nameInstance = 'objGridRemedios';
        objGridRemedios.setHeader(arrHeader);
        objGridRemedios.setCellWidth(['4%', '28%', '6%', '3%', '3%', '3%', '5%', '8%', '5%', '6%', '5%', '8%', '6%', '10%']);
        objGridRemedios.setHeight(140);
        objGridRemedios.show($('gridRemedios'));
        objGridRemedios.setStatus('* Estoque em vermelho significa que material está em ponto de pedido');

        if ($F('fa04_i_cgsund') != '') {
            js_getcontinuado();
        }
    }

    function js_tpreceita() {
        tipo = document.form1.fa04_i_tiporeceita.value;
        if (tipo != "") {
            vet = tipo.split('/');
            var table = document.getElementById('tabela1');
            id = "tabela1";
            if (vet[0] == 'S') {
                for (var r = 0; r < table.rows.length; r++) {
                    var id2 = table.rows[r].id;
                    if (id2 == 'linha1') {
                        table.rows[r].style.display = '';
                    }
                }

            } else {
                for (var r = 0; r < table.rows.length; r++) {
                    var id2 = table.rows[r].id;
                    if (id2 == 'linha1') {
                        table.rows[r].style.display = 'none';
                    }
                }
            }
            js_init();
        }
    }

    function js_ativaContinuado() {
        if ($('checkContinuado').checked == true) {
            sEstado = '';
        } else {
            sEstado = 'none';
        }
        var table = document.getElementById('tabela_medicamento');
        for (r = 0; r < table.rows.length; r++) {
            var id2 = table.rows[r].id;
            if (id2 == 'linha_continuado') {
                table.rows[r].style.display = sEstado;
            }
        }
    }

    function js_select_tipo_receita() {
        var count_opt = document.form1.fa04_i_tiporeceita.length;
        for (var i = 0; i < count_opt; i++) {
            vet_op = document.form1.fa04_i_tiporeceita.options[i].value.split('/');
            <?php
            if (!isset($fa42_i_tiporeceita)) {
                echo "alert('Verifique os parametros da farmacia!');return false;";
            }
            ?>
            if (vet_op[1] == <?php if (!isset($fa42_i_tiporeceita) || $fa42_i_tiporeceita == null) {
                echo '0';
            } else {
                echo $fa42_i_tiporeceita;
            }
                ?>) {
                document.form1.fa04_i_tiporeceita.options[i].selected = true;
                js_tpreceita();
                break;
            }
        }
    }

    /**
     * Função Busca medicamento continuado de um CGS
     */
    function js_getcontinuado() {
        objGridRemedios.setStatus('* Estoque em vermelho significa que material está em ponto de pedido');
        //Pesquisa Procedimentos
        var objParam = new Object();
        objParam.exec = "getGridRemedioscont";
        objParam.fa04_i_cgsund = $F('fa04_i_cgsund');
        vet = $('fa04_i_tiporeceita').value.split('/');
        if (vet[3] == '') {
            vet[3] = '0';
        }
        objParam.prescricao = vet[3];
        js_ajax(objParam, 'js_retornoGridRemedioscont');
    }

    /**
     * Retorno Grid Continuados
     */
    function js_retornoGridRemedioscont(objAjax) {
        var objRetorno = JSON.parse(objAjax.responseText);

        while ($('DadosGridRemedios').length != 0) {
            $('DadosGridRemedios').remove(0);
        }
        aCodMedicamentoGrid = new Array();

        if (objRetorno.itens != undefined && objRetorno.itens.length > 0) {
            objRetorno.itens.each(function (objremedio, intIterator) {
                aCodMedicamentoGrid.push(objremedio.fa10_i_medicamento);
                sStr = 'C';
                sStr += '#' + objremedio.fa10_i_medicamento;
                sStr += '#' + objremedio.m60_descr.urlDecode().substring(0, 35);
                sStr += '#' + objremedio.prox_data.urlDecode();
                sStr += '#' + objremedio.fa10_i_prazo.urlDecode();
                sStr += '#' + objremedio.margem.urlDecode();
                sStr += '#' + objremedio.fa10_i_quantidade;
                sStr += '#' + objremedio.saldo;
                sStr += '#' + objremedio.fa10_d_datafim.urlDecode();
                sStr += '#' + objremedio.saldo_estoque;
                sStr += '#' + objremedio.validade.urlDecode();
                sStr += '#' + objremedio.label_lote;
                sStr += '#';
                sStr += '#' + objremedio.fa11_t_obs;
                sStr += '#' + objremedio.lote;
                sStr += '#' + objremedio.m64_pontopedido;
                sStr += '#' + objremedio.hiperdia;
                sStr += '#f';
                sStr += '#';
                sStr += `#${objremedio.localizacao}`; // Localização no estoque

                $('DadosGridRemedios').add(new Option(sStr, $('DadosGridRemedios').length), null);
            });
        }

        if (aGridRemediosAtual.length > 0) {
            for (var i = 0; i < aGridRemediosAtual.length; i++) {
                if (inArray(aGridRemediosAtual[i][0], aCodMedicamentoGrid)) {

                } else {
                    sStr = 'N';                                              // tipo
                    sStr += '#' + aGridRemediosAtual[i][0];                     // codigo medicamento
                    sStr += '#' + aGridRemediosAtual[i][1].substring(0, 35);     // descricao
                    sStr += '#' + aGridRemediosAtual[i][2];                     // prox. data

                    //sStr += '#'+aGridRemediosAtual[i][3];                   // freq.
                    sStr += '#';

                    sStr += '#' + aGridRemediosAtual[i][4];                     // margem
                    sStr += '#' + aGridRemediosAtual[i][5];                     // qtde
                    sStr += '#' + aGridRemediosAtual[i][6];                     // saldo
                    sStr += '#' + aGridRemediosAtual[i][7];                     // Val. Continuado

                    // ver pq ta vindo sempre red text
                    sStr += '#' + aGridRemediosAtual[i][8];                     // estoque
                    //sStr += '#';

                    sStr += '#' + aGridRemediosAtual[i][9];                     // validade
                    sStr += '#' + aGridRemediosAtual[i][10];                     // label_lote

                    //sStr += '#'+aGridRemediosAtual[i][10];                  // input qtde disp.
                    sStr += '#' + aGridRemediosAtual[i][3];                     // Qtde. Disp.
                    sStr += '#';                                              // Observação

                    sStr += '#' + aGridRemediosAtual[i][12];

                    sStr += '#' + aGridRemediosAtual[i][11];                    // Ponto Pedido

                    sStr += '#' + 'false';
                    sStr += '#f';
                    sStr += '#';
                    sStr += '#' + aGridRemediosAtual[i][15]; // Localização no estoque

                    $('DadosGridRemedios').add(new Option(sStr, $('DadosGridRemedios').length), null);
                }
            }
        }
        aGridRemediosAtual = [];
        js_AtualizaGrid(true);
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for (var i = 0; i < length; i++) {
            if (haystack[i] == needle) return true;
        }
        return false;
    }

    function js_marcarTodos() {
        for (iI = 0; iI < objGridRemedios.getNumRows(); iI++) {
            if ($('marcarTodos').value == 'M' && !$('editLibera' + iI).disabled) {
                $('editLibera' + iI).checked = true;
                sText = $('DadosGridRemedios').options[iI].text;
                avet = sText.split('#');
                avet[17] = 't';
                sText = avet.join('#');
                $('DadosGridRemedios').options[iI].text = sText;
            } else {
                $('editLibera' + iI).checked = false;
                sText = $('DadosGridRemedios').options[iI].text;
                avet = sText.split('#');
                avet[17] = 'f';
                sText = avet.join('#');
                $('DadosGridRemedios').options[iI].text = sText;
            }
        }
        js_AtualizaGrid();
        if ($('marcarTodos').value == 'M') {
            $('marcarTodos').value = 'D';
        } else {
            $('marcarTodos').value = 'M';
        }
    }

    function js_margem() {
        if ($('contiFreq').value != '') {
            if (Number($('contimargem').value) > (Number($('contiFreq').value) / 2)) {
                alert("Margem tem que ser menor ou igual a metade da frequência.");
                $('contimargem').value = "";
            }
        }
    }

    function js_prazo() {
        if ($('contimargem').value != '') {
            if (Number($('contimargem').value) > (Number($('contiFreq').value) / 2)) {
                alert("Margem tem que ser menor ou igual a metade da frequência.");
                $('contimargem').value = "";
            }
        }
    }

    /*
 * Estrutura dos dados no select hidden:
 *
 * Os dados são separados por um sustenido (#) no texto do option
 *
 * 0   tipo           - C = continuado  N = Normal A = add adicionar nos continuados
 * 1   cod.           - Codigo do medicamento
 * 2   Medicamento    - Nome do medicamento
 * 3   Prox.Disp.     - Data da proxima dispensação
 * 4   Freq.          - frequencia do continuado
 * 5   Marg.          - margen do continuado
 * 6   Qtd.           - quantidade do continuado
 * 7   Saldo.         - saldo do continuado
 * 8   Validade Continuado.         - Val. Continuado
 * 9   Estoque        - estoque do medicamento no departamento
 * 10   Validade       - data de validade do medicamento
 * 11  Lote           - Lote do medicamento
 * 12  Qtd. Disp.     - Quantidade que será dispensada
 * 13  Posologia      - Posologia
 * 14  Cod. matestoqueitem - codigo da matestoqueitem selecionado (código sequencial)
 * 15  ponto pedido   - quantidade minima do material
 * 16  hiperdia       - indica se o medicamento pertence ao hiper dia (boolean)
 * 17  Libera tudo    - indica se é para liberar todo saldo disponivel de continuado
 * 18  Ação prog.     - ação programatica do continuado.
 * 19  localizacao    - localização do material no estoque
 *
 * Ex: N#3#Clonazepan#12/05/2020#30#3#10#30#5048#10/10/2015#ABDDC#10#2x ao dia#28#15#false#false#C3P5
 */
    function js_AtualizaGrid(bAlertas) {
        objGridRemedios.clearAll(true);
        iTam = $('DadosGridRemedios').length;
        $('confirmar').disabled = false;
        $('comprovante').disabled = false;
        $('retirada').disabled = false;
        $('validadeContinuado').value = 0;
        aAvencer = new Array();
        aPaint = new Array();

        for (x = 0; x < iTam; x++) {
            sText = $('DadosGridRemedios').options[x].text;
            avet = sText.split('#');
            alinha = new Array();
            alinha[0] = avet[1];
            alinha[1] = avet[2];
            if (avet[0] == "N" || (avet[7] == "" || parseInt(avet[7], 10) > 0)) {
                sFont1 = '';
                sFont2 = '';
            } else {
                sFont1 = '<font color="red">';
                sFont2 = '</font>';
            }
            alinha[2] = sFont1 + avet[3] + sFont2;
            alinha[3] = avet[4];
            alinha[4] = avet[5];
            alinha[5] = avet[6];
            alinha[6] = avet[7];
            if (avet[15] == '' || parseInt(avet[15], 10) < parseInt(avet[9], 10)) {
                sFont1 = '';
                sFont2 = '';
            } else {
                sFont1 = '<font color="red">';
                sFont2 = '</font>';
                aAvencer[aAvencer.length] = avet[2];
            }

            alinha[7] = avet[8];
            alinha[8] = sFont1 + avet[9] + sFont2;
            alinha[9] = avet[10];
            alinha[10] = avet[11];
            alinha[11] = avet[19];
            alinha[12] = '<input type="text" id="editQuant' + x + '" ';
            bloqueio = '';
            if (avet[0] != 'N') {
                aPaint[aPaint.length] = x;
                if (avet[0] == 'C') {
                    bloqueio = 'disabled';
                }
                <?php if ($oConfigFarmacia->fa02_i_validavencimento > 1) : ?>
                if (avet[10] != '' && avet[10] != undefined) {
                    aData = avet[10].split('/');
                    validade = new Date(aData[2], (aData[1] - 1), aData[0]);
                    if (avet[3] == '') {
                        <?php $aVet = explode('-', $dHoje);
                        echo " hoje = new Date($aVet[0]," . ($aVet[1] - 1) . ",$aVet[2]); ";?>
                        tratamento = somaDataDiaMesAno(hoje, avet[4], 0, 0);
                    } else {
                        aData = avet[3].split('/');
                        tratamento = new Date(aData[2], (aData[1] - 1), aData[0]);
                    }
                    if (validade.getTime() <= tratamento.getTime()) {
                        if (bAlertas != undefined && bAlertas == true) {
                            alert('Medicamento ' + avet[2] + ' vai vencer durante o tratamento!');
                        }
                        <?php if ($oConfigFarmacia->fa02_i_validavencimento == 3) : ?>
                        $('validadeContinuado').value = 1;
                        <?php endif; ?>
                    }
                }
                <?php endif; ?>
            }
            if (avet[10] != '' && avet[10].split('/').length == 3) {
                aData = avet[10].split('/');
                validade = aData[2].substr(0, 4) + aData[1] + aData[0];
                <?php $aVet = explode('-', $dHoje);
                echo " hoje = $aVet[0]$aVet[1]$aVet[2]; ";?>
                if (validade <= hoje) {
                    if (bAlertas != undefined && bAlertas == true) {
                        alert(' O lote do medicamento ' + avet[2] + ' está vencido! ');
                    }
                    <?php if ($oConfigFarmacia->fa02_i_validavencimento == 3) : ?>
                    $('validadeContinuado').value = 1;
                    <?php endif; ?>
                }
            }
            if (avet[7] == '') {
                if (avet[0] == 'N') {
                    saldo = -1;
                } else {
                    saldo = 0;
                }
            } else {
                saldo = avet[7]
            }

            if (avet[9] == '') {
                estoque = 0;
            } else {
                estoque = avet[9];
            }
            if (avet[12] > 0) {
                $('confirmar').disabled = false;
                $('retirada').disabled = false;
            }
            if (avet[17] == 't') {
                alinha[12] += ' value="' + avet[7] + '" size="5" readonly style="background-color: #DEB887;"';
            } else {
                alinha[12] += ' value="' + avet[12] + '" size="5" ';
            }
            alinha[12] += ' onchange="validaQuantidade(' + x + ',' + saldo + ',' + estoque + ')" > ';
            alinha[13] = '';
            if ($('fa04_tiporetirada').value == '1') {
                alinha[13] += '<input type="button" name="rateiolote" value="Lote" onclick="js_ratioLote(' + x + ')">';
            }
            alinha[13] += '&nbsp;<input type="button" name="posologia" value="P" onclick="js_lancar(' + x + ',\'' + avet[13] + '\')">';
            alinha[13] += '&nbsp;<input type="button" name="excluir" value="E" onclick="js_excluirlinha(' + x + ')" ' + bloqueio + '>';
            if (avet[0] != 'N') {
                if (avet[17] == 't') {
                    sLibera = 'checked';
                } else {
                    sLibera = '';
                }
                if (avet[7] == 0 || avet[9] == 0) {
                    sSemsaldo = 'disabled';
                } else {
                    sSemsaldo = '';
                }
                alinha[13] += '<input type="checkbox" id="editLibera' + x + '" ' + sSemsaldo + ' ';
                alinha[13] += sLibera + ' onclick="js_atualizaCampo(' + x + ',js_libera(this.checked),17)">';
            }
            objGridRemedios.addRow(alinha);
        }
        objGridRemedios.renderRows();
        iTam = aPaint.length;
        for (iY = 0; iY < iTam; iY++) {
            $('objGridRemediosrowobjGridRemedios' + aPaint[iY]).className = 'classContinuado';
        }
        if (bAlertas != undefined && bAlertas == true) {
            iTam = aAvencer.length;
            if (iTam > 0) {
                sStr = aAvencer.join(',');
                alert('Medicamento(s) ' + sStr + ' , esta em ponto de Pedido ');
            }
        }
    }

    function js_libera(valor) {
        if (valor == true) {
            return 't';
        } else {
            return 'f';
        }
    }

    function validaQuantidade(x, saldo_tratamento, saldo_estoque) {
        if (saldo_tratamento != -1) {
            menor = saldo_tratamento;
            if (saldo_estoque < saldo_tratamento && $F('fa04_tiporetirada') == 1) {
                menor = saldo_estoque;
            }
        } else {
            if ($F('fa04_tiporetirada') == 1) {
                menor = saldo_estoque;
            } else {
                menor = -1;
            }
        }
        if ($('editQuant' + x).value > menor && menor != -1) {
            alert('Quantidade ' + $('editQuant' + x).value + ' não disponivel, valor disponivel ' + menor);
            $('editQuant' + x).value = menor;
        }
        js_atualizaCampo(x, $('editQuant' + x).value, 12);
    }

    function js_ratioLote(x) {
        sText = $('DadosGridRemedios').options[x].text;
        avet = sText.split('#');
        valor = $('editQuant' + x).value;
        item = avet[1];

        if (valor != 0) {
            sUrl = 'far1_mostraitemlote.php';
            sUrl += '?iCodDepto=<?=$departamento?>';
            sUrl += '&nValor=' + valor;
            sUrl += '&nValorSolicitado=' + valor;
            sUrl += '&fa01_i_codigo=' + item;
            sUrl += '&iGrid=' + x;
            js_OpenJanelaIframe('', 'db_iframe_lotes', sUrl, 'Lotes ', true);
        }
    }

    function js_atualizaCampo(x, valor, index) {
        sText = $('DadosGridRemedios').options[x].text;
        avet = sText.split('#');
        avet[index] = valor;
        sText = avet.join('#');
        $('DadosGridRemedios').options[x].text = sText;
        js_AtualizaGrid();
    }

    function js_atualizaPosologia(x, posologia) {
        sText = $('DadosGridRemedios').options[x].text;
        avet = sText.split('#');
        avet[13] = posologia;
        sText = avet.join('#');
        $('DadosGridRemedios').options[x].text = sText;
        js_AtualizaGrid();
    }

    function js_excluirlinha(x) {
        if (confirm('Tem certeza que deseja remover da grade?')) {
            $('DadosGridRemedios').remove(x);
            js_AtualizaGrid();
        }
    }

    function js_abre_historico() {
        if ($F('fa04_i_cgsund') != '' && $F(fa04_i_cgsund) == parseInt($F('fa04_i_cgsund'))) {
            cgs = 'cgs_get=' + document.form1.fa04_i_cgsund.value;
            nome = '&nome=' + document.form1.z01_v_nome.value;

            iTop = 20;
            iLeft = 5;
            iHeight = screen.availHeight - 210;
            iWidth = screen.availWidth - 35;

            js_OpenJanelaIframe(
                "",
                "db_iframe_historico",
                "far3_historicopaciente_popup.php?" + cgs + nome,
                "Hist&oacute;rico de Retiradas de Medicamentos",
                true,
                iTop,
                iLeft,
                iWidth,
                iHeight
            );
        } else {
            alert('Selecione um CGS Antes!');
        }
    }

    function js_pesquisafa04_i_cgsund(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_cgs_und',
                'func_cgs_und.php?funcao_js=parent.js_mostracgs_und1|z01_i_cgsund|z01_v_nome|s115_c_cartaosus|z01_nome_social|z01_v_cgccpf|s115_i_codigo',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.fa04_i_cgsund.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_cgs_und',
                    'func_cgs_und.php?pesquisa_chave=' + document.form1.fa04_i_cgsund.value +
                    '&funcao_js=parent.js_mostracgs_und&mostrarNomeSocial=true',
                    'Pesquisa',
                    false
                );
            } else {
                if (document.form1.continuados != undefined) {
                    document.form1.continuados.disabled = true;
                }
                document.form1.z01_v_nome.value = '';
                document.form1.s115_c_cartaosus.value = '';
                document.form1.demandaReprimida.disabled = true;
                document.form1.z01_nome_social.value = '';
                document.form1.z01_nome_social.style.backgroundColor = "";
                document.form1.z01_v_cgccpf.value = '';
                valorCgsEncontrado = '';
                codigoCartaoSusEncontrado = '';
                cpfAtual = '';
                cnsAtual = '';
            }
        }
    }

    async function js_mostracgs_und(chave, erro, sexo, telefone, cns, dataNascimento, nomeSocial, cpf) {
        document.form1.z01_v_nome.value = chave;

        if (!erro) {
            document.form1.s115_c_cartaosus.value = cns;
            document.form1.z01_nome_social.value = nomeSocial;

            if (nomeSocial.length > 0) {
                document.form1.z01_nome_social.style.backgroundColor = "#97CAE6";
            } else {
                document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";
            }
            document.form1.z01_v_cgccpf.value = cpf;
            valorCgsEncontrado = $F('fa04_i_cgsund');
            cpfAtual = cpf;
            cnsAtual = cns;

            buscouPeloNomeOuCgs = true;
            js_getCgsCns();
        }

        const continuar = await validaCgsMunicipio(document.form1.fa04_i_cgsund.value);
        if (!continuar || erro) {
            document.form1.fa04_i_cgsund.focus();
            document.form1.fa04_i_cgsund.value = '';
            document.form1.s115_c_cartaosus.value = '';
            document.form1.demandaReprimida.disabled = true;
            document.form1.z01_v_cgccpf.value = '';
            document.form1.z01_nome_social.value = '';
            document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";

            valorCgsEncontrado = '';
            codigoCartaoSusEncontrado = '';
            cpfAtual = '';
            cnsAtual = '';

            if (document.form1.continuados != undefined) {
                document.form1.continuados.disabled = true;
            }
        } else {
            document.form1.demandaReprimida.disabled = false;
            if (document.form1.continuados != undefined) {
                document.form1.continuados.disabled = false;
            }
            js_init();
            getAvisoRetirada();
        }
    }

    function js_mostracgs_und1(chave1, chave2, cns, nomeSocial, cpf) {
        document.form1.fa04_i_cgsund.value = chave1;
        document.form1.z01_v_nome.value = chave2;
        document.form1.s115_c_cartaosus.value = cns;
        document.form1.z01_nome_social.value = nomeSocial;
        document.form1.z01_v_cgccpf.value = cpf;
        valorCgsEncontrado = chave1;
        cpfAtual = cpf;
        cnsAtual = cns;

        buscouPeloNomeOuCgs = true;

        //Função utilizada para recuperar id de cns caso o mesmo tenha seu valor como vazio
        js_getCgsCns();
        document.form1.fa04_i_cgsund.dispatchEvent(new Event('change'));
        db_iframe_cgs_und.hide();
    }

    function js_pesquisafa04_i_tiporeceita(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_far_tiporeceita',
                'func_far_tiporeceita.php?' +
                'funcao_js=parent.js_mostrafar_tiporeceita1|fa03_i_codigo|fa03_c_descr',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.fa04_i_tiporeceita.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_far_tiporeceita',
                    'func_far_tiporeceita.php?' +
                    'pesquisa_chave=' + document.form1.fa04_i_tiporeceita.value +
                    '&funcao_js=parent.js_mostrafar_tiporeceita',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.fa03_i_codigo.value = '';
            }
        }
    }

    function js_mostrafar_tiporeceita(chave, erro) {
        document.form1.fa03_c_descr.value = chave;
        if (erro == true) {
            document.form1.fa04_i_tiporeceita.focus();
            document.form1.fa04_i_tiporeceita.value = '';
        }
    }

    function js_mostrafar_tiporeceita1(chave1, chave2) {
        document.form1.fa04_i_tiporeceita.value = chave1;
        document.form1.fa03_c_descr.value = chave2;
        db_iframe_far_tiporeceita.hide();
    }

    function js_pesquisafa04_i_profissional(mostra) {
        if (document.form1.fa04_i_tiporeceita.value == " ") {
            alert("informe o tipo da receita");
            document.form1.fa04_i_profissional.value = "";
        } else {
            if (mostra == true) {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_medicos',
                    'func_medicos.php?funcao_js=parent.js_mostramedicos1|sd03_i_codigo|z01_nome' +
                    '&lTodosTiposProf=true',
                    'Pesquisa',
                    true
                );
            } else {
                if (document.form1.fa04_i_profissional.value != '') {
                    js_OpenJanelaIframe(
                        '',
                        'db_iframe_medicos',
                        'func_medicos.php?' +
                        'pesquisa_chave=' + document.form1.fa04_i_profissional.value +
                        '&funcao_js=parent.js_mostramedicos&lTodosTiposProf=true',
                        'Pesquisa',
                        false
                    );
                } else {
                    document.form1.z01_nome.value = '';
                }
            }
        }
    }

    function js_mostramedicos(chave, erro) {
        document.form1.z01_nome.value = chave;
        if (erro === true) {
            document.form1.fa04_i_profissional.focus();
            document.form1.fa04_i_profissional.value = '';
        }
    }

    function js_mostramedicos1(chave1, chave2) {
        document.form1.fa04_i_profissional.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_medicos.hide();
    }

    /**
     * Valida se o endereço foi preenchido
     * @return bool
     */
    function validaEnderecoParaRetirada() {
        if (document.form1.ender.value == "") {
            alert("Campo endereço não informado.Cadastre-o no CGS");
            return false;
        }
        if (document.form1.numero.value == "") {
            alert("Campo número não informado.Cadastre-o no CGS");
            return false;
        }
        if (document.form1.ident.value == "") {
            alert("Campo identidade não informado.Cadastre-o no CGS");
            return false;
        }

        return true;
    }

    function validaRetiradaMedicamento() {
        if ($('fa04_i_cgsund').value == '') {
            alert('Campo CGS não informado!');
            return false;
        }

        if ($('fa04_i_tiporeceita').value == '') {
            alert('Selecione um tipo de receita!');
            return false;
        }

        if (!isDataPrescricaoValida()) {
            return false;
        }

        if (!obrigaNumeroNotificacao()) {
            return false;
        }

        vet = document.form1.fa04_i_tiporeceita.value.split("/");

        if (vet[0] == "S" && !validaEnderecoParaRetirada()) {
            return false;
        }

        return true;
    }

    function limparCamposMedicamento() {
        $('fa01_codigobarras').value = '';
        $('fa06_i_matersaude').value = '';
        $('fa06_f_quant').value = '';
        $('m60_descr').value = '';
        $('tipoReceitaMedicamento').value = '';
        $('quant_disp').value = '';
        $('contiQuant').value = '';
        $('contiFreq').value = '';
        $('fa10_i_programa').value = '';
        $('fa12_c_descricao').value = '';
        $('checkContinuado').checked = false;
        //desativa se tiver ativo
        js_ativaContinuado();
    }

    function jsPesquisaMedicamento(lMostra) {
        if (!validaRetiradaMedicamento()) {
            limparCamposMedicamento();
            return false;
        }

        prescricao = '';
        //verificar qual o numero da prescricao
        vet = $('fa04_i_tiporeceita').value.split('/');
        if ((vet[3] != '') && (vet[3] != '0') && (vet[3] != undefined)) {
            prescricao = 'prescricao=' + vet[3] + '&';
        }

        var sUrl = 'func_far_matersaude.php?' + prescricao;
        if (lMostra) {
            sUrl += 'funcao_js=parent.js_mostramatersaude1|fa01_i_codigo|m60_descr|fa01_codigobarras';
            js_OpenJanelaIframe('', 'db_iframe_far_matersaude', sUrl, 'Pesquisa Medicamento', true);
        } else if ($F('fa06_i_matersaude') != '') {
            sUrl += 'pesquisa_chave=' + $F(fa06_i_matersaude) + '&funcao_js=parent.js_mostramatersaude';
            js_OpenJanelaIframe('', 'db_iframe_far_matersaude', sUrl, 'Pesquisa Medicamento', false);
        } else {
            limparCamposMedicamento();
        }
    }

    function pesquisaMedicamentoCodigoBarras() {
        if (!validaRetiradaMedicamento()) {
            limparCamposMedicamento();
            return false;
        }

        prescricao = '';
        //verificar qual o numero da prescricao
        vet = $('fa04_i_tiporeceita').value.split('/');
        if ((vet[3] != '') && (vet[3] != '0') && (vet[3] != undefined)) {
            prescricao = 'prescricao=' + vet[3] + '&';
        }

        var sUrl = 'func_far_matersaude.php?' + prescricao;
        if ($F('fa01_codigobarras') != '') {
            sUrl += 'codigo_barras=' + $F(fa01_codigobarras) + '&funcao_js=parent.js_mostramatersaude';
            js_OpenJanelaIframe('', 'db_iframe_far_matersaude', sUrl, 'Pesquisa Medicamento', false);
        } else {
            limparCamposMedicamento();
        }
    }

    function js_mostramatersaude(chave1, chave2, erro, codigoBarras) {
        document.form1.m60_descr.value = chave2;
        if (erro == true) {
            document.form1.fa01_codigobarras.focus();
            document.form1.fa01_codigobarras.value = '';
            document.form1.fa06_i_matersaude.value = '';
            document.form1.demandaReprimidaMed.disabled = true;
        } else {
            $('fa06_i_matersaude').value = chave1;
            $('fa01_codigobarras').value = codigoBarras;
            $('fa06_t_posologia').disabled = false;
            $('ultimas').disabled = false;
            js_callBackMedicamento();
        }
    }

    function js_mostramatersaude1(chave1, chave2, chave3) {
        document.form1.fa06_i_matersaude.value = chave1;
        document.form1.m60_descr.value = chave2;
        document.form1.fa01_codigobarras.value = chave3;

        db_iframe_far_matersaude.hide();

        $('fa06_t_posologia').disabled = false;
        $('ultimas').disabled = false;
        js_callBackMedicamento();
    }

    function js_mostraLotes(material, nome_up) {
        if ($('fa04_tiporetirada').value == 2) {
            alert('Tipo de retirada não suporta LOTE!');
            return false;
        }
        if ((material != null) && (material != '')) {
            campo = 'iCodMater';
            campo_up = nome_up;
            item = material;
            valor = $(nome_up).value
        } else {
            campo = 'fa01_i_codigo';
            campo_up = 'fa06_f_quant';
            item = $('fa06_i_matersaude').value;
            valor = $('fa06_f_quant').value;
        }
        if (valor != 0) {
            var sUrl = 'far1_mostraitemlote.php?iCodDepto=<?=$departamento?>&nValor=' + valor;
            sUrl += '&nValorSolicitado=' + valor;
            sUrl += '&updateField=' + campo_up;
            sUrl += '&' + campo + '=' + item;
            <?php if ($oConfigFarmacia->fa02_i_validalote == 1) : ?>
            sUrl += '&ilancaDireto=1';
            <?php endif; ?>
            js_OpenJanelaIframe('', 'db_iframe_lotes', sUrl, 'Lotes ', true);
        }
        return false;
    }

    function js_imprime() {
        var obj = document.form1;
        var query = "";
        if (obj.m40_codigo.value != "") {
            if (!lImpressaoCupom) {
                query = 'ini=' + obj.fa04_i_codigo.value;
                query += '&fim=' + obj.fa04_i_codigo.value;
                query += '&iTipoRetirada=' + $F('fa04_tiporetirada');
                jan = window.open(
                    'far1_atendretira001.php?nvias=1&' + query,
                    '',
                    'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 '
                );
                jan.moveTo(0, 0);
            } else {
                js_impressaoCupom(obj.fa04_i_codigo.value);
            }
        } else {
            $('comprovante').disabled = true;
            js_confirma(true);
        }
    }

    function js_impressaoCupom(iRetirada) {
        var oParam = new Object();
        oParam.exec = 'impressaoComprovante';
        oParam.iTipoRetirada = $F('fa04_tiporetirada');
        oParam.iRetirada = iRetirada;

        js_divCarregando('Aguarde, imprimindo', 'msgBox');
        var oAjax = new Ajax.Request(
            'far4_impressaoComprovante.RPC.php',
            {
                method: 'post',
                asynchronous: false,
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: js_retornoCupom
            }
        );
    }

    function js_retornoCupom(oAjax) {
        js_removeObj('msgBox');
        /*
         * @todo verificar se deve ser mostrada a mensagem de terminal nao cadastrado para emissao do comprovante
         * Para mostrar a mensagem, basta descomentar o trecho de codigo abaixo
         */
        /*
        var obj = JSON.parse(oAjax.responseText);
        if (obj.status == 2) {
            alert(obj.message.urlDecode());
        }
        */
    }

    function js_pesquisafa08_i_cgsund(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_cgs_und',
                'func_cgs_far.php?' +
                'funcao_js=parent.js_mostracgs_undi1|z01_i_cgsund|z01_v_nome|z01_v_ender|z01_i_numero|z01_v_ident',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.fa08_i_cgsund.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_cgs_und',
                    'func_cgs_far.php?pesquisa_chave=' + $F('fa08_i_cgsund') + '&funcao_js=parent.js_mostracgs_undi',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.fa08_i_cgsund.value = '';
                document.form1.ender.value = '';
                document.form1.numero.value = '';
                document.form1.ident.value = '';
                document.form1.z01_v_nomecgs.value = '';
            }
        }
    }

    function js_mostracgs_undi(chave, chave2, chave3, chave4, erro) {
        document.form1.ender.value = chave2;
        document.form1.numero.value = chave3;
        document.form1.ident.value = chave4;
        document.form1.z01_v_nomecgs.value = chave;
        if (erro == true) {
            document.form1.fa08_i_cgsund.focus();
            document.form1.fa08_i_cgsund.value = '';
        }
    }

    function js_mostracgs_undi1(chave1, chave2, chave3, chave4, chave5) {
        document.form1.fa08_i_cgsund.value = chave1;
        document.form1.z01_v_nomecgs.value = chave2;
        document.form1.ender.value = chave3;
        document.form1.numero.value = chave4;
        document.form1.ident.value = chave5;
        db_iframe_cgs_und.hide();
    }

    function js_tpreceita() {
        tipo = document.form1.fa04_i_tiporeceita.value;
        if (tipo != "") {
            vet = tipo.split('/');
            var table = document.getElementById('tabela1');
            id = "tabela1";
            if (vet[0] == 'S') {
                for (var r = 0; r < table.rows.length; r++) {
                    var id2 = table.rows[r].id;
                    if (id2 == 'linha1') {
                        table.rows[r].style.display = '';
                    }
                }
            } else {
                for (var r = 0; r < table.rows.length; r++) {
                    var id2 = table.rows[r].id;
                    if (id2 == 'linha1') {
                        table.rows[r].style.display = 'none';
                    }
                }
            }
            js_init();
        }
    }

    function js_lancar(iGrid, texto) {
        iTop = (screen.availHeight - 600) / 2;
        iLeft = (screen.availWidth - 600) / 2;
        posologia = $('posologia_edit').value;
        sGrid = '';
        if ((iGrid != undefined) && (iGrid != -1)) {
            sGrid = '&iGrid=' + iGrid;
            posologia = texto;
        }
        js_OpenJanelaIframe(
            "",
            "db_iframe_posologia",
            "far1_far_posologia001.php?posologiat=" + posologia + sGrid,
            "Pesquisa",
            true,
            iTop,
            iLeft,
            600,
            200
        );
    }

    function js_quantidade(valor) {
        if (valor != "") {
            if (valor > new Number(document.form1.quant_disp.value)) {
                alert('Quantidade requisitada maior que a disponível!');
                $('fa06_f_quant').value = '';
            }
            vet = $('fa04_i_tiporeceita').value.split('/');
            <?php if ($oConfigFarmacia->fa02_i_validalote == 1) : ?>
            js_mostraLotes('', 'fa06_f_quant');
            <?php endif; ?>
            if (vet[2] == 'S') {
                js_lancar();
            }
        }
    }

    function js_callBackMedicamento() {
        document.form1.demandaReprimidaMed.disabled = true;
        document.form1.quant_disp.value = '';
        if (document.form1.m60_descr.value == '') {
            limparCamposMedicamento();
            return;
        }

        if ($('fa06_i_matersaude').value != "") {
            //Pesquisa Procedimentos
            var objParam = new Object();
            objParam.exec = "ConsultaSaldo";
            objParam.fa06_i_matersaude = $F('fa06_i_matersaude');
            objParam.fa04_i_cgsund = $F('fa04_i_cgsund');
            js_ajax(objParam, 'js_retornoMaterial');

            const formData = new FormData();
            formData.append('acao', 'getDetalhesMedicamento');
            formData.append('medicamento', $F('fa06_i_matersaude'));

            HttpClient.post('far_retirada.RPC.php', {body: formData}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                document.getElementById('tipoReceitaMedicamento').value = response.detalhes.fa20_c_prescricao;
            });
        }
    }

    function js_retornoMaterial(oAjax) {
        var obj = JSON.parse(oAjax.responseText);
        if (obj.status == 1) {
            if (obj.iExContinuado == 0) {
                document.form1.demandaReprimidaMed.disabled = true;
                if (obj.quant_disp == 0) {
                    document.form1.demandaReprimidaMed.disabled = false;
                }
                $('quant_disp').value = obj.quant_disp;
                $('lote_edit').value = obj.lote;
                $('lote').value = obj.loteReal;
                $('validade_edit').value = obj.validade;
                $('pontoPedido').value = obj.m64_pontopedido;
                $('hiperdia').value = obj.hiperdia;
                $('fa06_t_posologia').disabled = false;
                $('ultimas').disabled = false;
                $('localizacao').value = obj.localizacao;
            } else {
                //adiciona no grid o continuado que retornou

                if (!js_verificaLanc($F('fa06_i_matersaude'))) {
                    return false;
                }
                sStr = 'C';
                sStr += '#' + $F('fa06_i_matersaude');
                sStr += '#' + $F('m60_descr').substring(0, 35);
                sStr += '#' + obj.prox_data.urlDecode();
                sStr += '#' + obj.fa10_i_prazo.urlDecode();
                sStr += '#' + obj.fa10_i_margem.urlDecode();
                sStr += '#' + obj.fa10_i_quantidade;
                sStr += '#' + obj.saldo_atual;
                sStr += '#' + obj.fa10_d_datafim;
                sStr += '#' + obj.quant_disp;
                sStr += '#' + obj.validade.urlDecode();
                sStr += '#' + obj.loteReal;
                sStr += '#';
                sStr += '#';
                sStr += '#' + obj.lote;
                sStr += '#' + obj.m64_pontopedido;
                sStr += '#' + obj.hiperdia;
                sStr += '#f';
                sStr += '#';
                sStr += `#${obj.localizacao}`; // localização no estoque

                $('DadosGridRemedios').add(new Option(sStr, $('DadosGridRemedios').length), null);
                js_AtualizaGrid();
                $('fa06_i_matersaude').value = '';
                $('m60_descr').value = '';
            }
        } else {
            alert(obj.message.urlDecode());
        }
    }

    function js_incluiremedio() {
        if ($F('fa04_i_cgsund') == '') {
            alert('Campo CGS não informado!');
            return;
        }

        if ($F('fa04_i_tiporeceita') == "") {
            alert("Informe o tipo de receita");
            return false;
        }

        fa06_f_quant = new Number(document.form1.fa06_f_quant.value);
        if ($('fa04_tiporetirada').value == 1 || fa06_f_quant == 0) {
            quant_disp = new Number(document.form1.quant_disp.value);

            if (fa06_f_quant > quant_disp || fa06_f_quant == 0) {
                alert('Informe uma Quantidade Válida!');
                document.form1.fa06_f_quant.value = "";
                document.form1.fa06_f_quant.focus();
                return false;
            }
        }

        vet = document.form1.fa04_i_tiporeceita.value.split('/');
        if (document.form1.posologia_edit.value == "") {
            if (vet[2] == 'S') {
                js_lancar();
                alert("Campo Posologia Obrigatório");
                return false;
            }
        }
        if (document.form1.fa04_i_profissional.value == "") {
            if (vet[4] == 'S') {
                alert("Campo Profissional Obrigatório");
                return false;
            }
        }
        if (document.form1.fa04_i_receita.value == "") {
            if (vet[5] == 'S') {
                alert("Campo Numero receita Obrigatório");
                return false;
            }
        }
        if (!js_verificaLanc($F('fa06_i_matersaude'))) {
            return false;
        }
        if ($('checkContinuado').checked == true) {
            sStr = 'A';
            if (new Number($F('contiQuant')) < new Number($F('fa06_f_quant'))) {
                alert(' A quantidade retirada não pode ultrapassar a quantidade do tratamento continuado! ');
                return false;
            }
        } else {
            sStr = 'N';
        }

        if (!isDataPrescricaoValida()) {
            return false;
        }

        if (!obrigaNumeroNotificacao()) {
            return false;
        }

        sStr += '#' + $F('fa06_i_matersaude');
        sStr += '#' + $F('m60_descr').substring(0, 35);
        sStr += '#';//prox. disp.
        sStr += '#' + $F('contiFreq');
        sStr += '#' + $F('contimargem');
        sStr += '#' + $F('contiQuant');
        sStr += '#' + $F('contiQuant');
        sStr += '#';//Val. Continuado
        sStr += '#' + $F('quant_disp');
        sStr += '#' + $F('validade_edit');
        sStr += '#' + $F('lote');
        sStr += '#' + $F('fa06_f_quant');
        sStr += '#' + $F('posologia_edit');
        sStr += '#' + $F('lote_edit');
        sStr += '#' + $F('pontoPedido');
        if ($('checkContinuado').checked == true) {
            sStr += '#' + $('hiperdia').value;
        } else {
            sStr += '#false';
        }
        sStr += '#f';
        sStr += '#' + $F('fa10_i_programa');
        sStr += '#' + $F('localizacao'); // localização no estoque

        $('DadosGridRemedios').add(new Option(sStr, $('DadosGridRemedios').length), null);
        js_AtualizaGrid();

        //bloqueia os campos acima  "#DEB887"
        $('fa06_t_posologia').disabled = true;
        $('ultimas').disabled = true;
        //cgs
        $('fa04_i_cgsund').disabled = true;
        //prfissional
        $('fa04_i_profissional').disabled = true;
        // document.links[1].style.color      = "black";
        //tipo receita
        $('fa04_i_tiporeceita').disabled = true;
        //data prescrição
        $('fa04_data_prescricao').disabled = true;
        $('dtjs_fa04_data_prescricao').disabled = true;
        //prfissional
        $('fa04_i_receita').disabled = true;
        //tipo receita
        $('fa04_d_dtvalidade').disabled = true;
        //requisitante
        $('fa08_i_cgsund').disabled = true;
        //nome requisitante
        $('z01_nome').disabled = true;
        //nome CGS
        $('z01_v_nome').disabled = true;
        //cartão sus
        $('s115_c_cartaosus').disabled = true;
        //CPF
        $('z01_v_cgccpf').disabled = true;
        //nome social
        $('z01_nome_social').disabled = true;
        //âncora cgs
        $('ancora_cgs').onclick = '';

        //liberar confirmar
        $('confirmar').disabled = false;
        $('retirada').disabled = false;
        $('fa06_t_posologia').disabled = false;
        $('ultimas').disabled = false;

        //limpar campos
        $('fa06_i_matersaude').value = '';
        $('fa01_codigobarras').value = '';
        $('m60_descr').value = '';
        $('fa06_f_quant').value = '';
        $('lote_edit').value = '';
        $('validade_edit').value = '';
        $('quant_disp').value = '';
        $('contiQuant').value = '';
        $('contiFreq').value = '';
        $('contimargem').value = '';
        $('tipoReceitaMedicamento').value = '';
        $('checkContinuado').checked = false;
        $('posologia_edit').value = '';
        $('localizacao').value = '';

        js_ativaContinuado();
    }

    function js_verificaLanc(iCodigo) {
        iTam = $('DadosGridRemedios').length
        for (iX = 0; iX < iTam; iX++) {
            aVetmed = $('DadosGridRemedios').options[iX].text.split('#');
            if (iCodigo == aVetmed[1]) {
                alert('Medicamento já lançado!');
                return false;
            }
        }
        return true;
    }

    function js_confirma(lComprovante) {
        if (lComprovante == undefined) {
            lComprovante = false;
        }

        if ($F('fa04_i_cgsund') == '') {
            alert('Campo CGS não informado!');
            return;
        }

        if (!isDataPrescricaoValida()) {
            document.form1.m60_descr.value = '';
            return false;
        }

        if (!obrigaNumeroNotificacao()) {
            document.form1.m60_descr.value = '';
            return false;
        }

        $('confirmar').disabled = true;
        <?php if ($oConfigFarmacia->fa02_b_novaretirada == "f") : ?>
        if (confirm('Tem certeza que deseja efetuar a retirada deste(s) medicamento(s)!')) {
            <?php endif; ?>
            if (document.form1.fa04_i_tiporeceita.value == "") {
                alert("Informe o tipo de receita");
                $('confirmar').disabled = false;
                return false;
            }

            var table = document.getElementById("objGridRemediosbody").rows;
            var enableConfirm = false;

            for (tr in table) {
                if (typeof table[tr] != 'object') {
                    continue;
                }

                if (table[tr].getAttribute('class') != 'classContinuado') {
                    continue;
                }

                for (var i = 0; i < table[tr].childNodes.length; i++) {
                    if (typeof table[tr].childNodes[i].getAttribute !== "undefined") {
                        if (table[tr].childNodes[i].getAttribute('id') == "objGridRemediosrow" + tr + "cell12") {
                            var input = table[tr].childNodes[i];
                            if (input.childNodes[0].getAttribute('value') != "") {
                                enableConfirm = true;
                            }
                        }
                    }
                }
            }

            if ($F('validadeContinuado') != 0 && enableConfirm) {
                alert("Existem medicamentos próximos da data de vencimento ou vencidos!");
                $('confirmar').disabled = false;
                return false;
            }

            vet = document.form1.fa04_i_tiporeceita.value.split('/');
            if (document.form1.fa04_i_profissional.value == "") {
                if (vet[4] == 'S') {
                    alert("Campo Profissional Obrigatório");
                    $('confirmar').disabled = false;
                    return false;
                }
            }
            if (document.form1.fa04_i_receita.value == "") {
                if (vet[5] == 'S') {
                    alert("Campo aNumero receita Obrigatório");
                    $('confirmar').disabled = false;
                    return false;
                }
            }

            var objParam = new Object();
            objParam.exec = "Confirma_Remedio";
            objParam.iTiporetirada = $('fa04_tiporetirada').value;
            objParam.numero_receita = $('fa04_i_receita').value;
            objParam.validade_receita = $('fa04_d_dtvalidade').value;
            objParam.profissional = $('fa04_i_profissional').value;
            objParam.cgs = document.form1.fa04_i_cgsund.value;
            objParam.nomeSocial = document.form1.z01_nome_social.value;
            objParam.iNumeronotificacao = $('fa04_numeronotificacao').value;
            objParam.lReceitaSistema = lReceitaSistema;
            objParam.dataPrescricao = $('fa04_data_prescricao').value;

            if (<?=$oConfigFarmacia->fa02_i_origemreceita?> == 1) {
                if ($F('fa40_c_descr') != '') {
                    objParam.sOrigemReceita = document.form1.fa40_c_descr.value;
                }
            }

            tipo = $('fa04_i_tiporeceita').value;

            if (tipo != "") {
                vet = tipo.split('/');

                if (vet[0] == 'S') {
                    objParam.tipo_receita = vet[1];

                    if (($('fa08_i_cgsund').value != null) && ($('fa08_i_cgsund').value != '')) {
                        objParam.requi_cgs = $('fa08_i_cgsund').value;
                    }

                    objParam.requi_nome = $('z01_v_nomecgs').value;
                    objParam.requi_ender = $('ender').value;
                    objParam.requi_numero = $('numero').value;
                    objParam.requi_ident = $('ident').value;
                } else {
                    objParam.tipo_receita = vet[1];
                }
            } else {
                alert('Tipo receita não selecionado');
                return false;
            }
            //dados do grid
            tam = $('DadosGridRemedios').length;
            aMedicamentos = new Array();

            for (iX = 0; iX < tam; iX++) {

                sText = $('DadosGridRemedios').options[iX].text;
                avet = sText.split('#');
                if (($('editQuant' + iX).value > 0 && $('editQuant' + iX).value != '')
                    || avet[17] == 't' || avet[17] == 'true') {

                    if (avet[17] == 'f' || avet[17] == 'false') {
                        avet[12] = $('editQuant' + iX).value;
                    } else {
                        if (parseInt(avet[7], 10) < parseInt(avet[9], 10)) {
                            avet[12] = avet[7];
                        } else {
                            avet[12] = avet[9];
                        }
                    }
                    avet[2] = ''; // Retiro o nome do medicamento pq dá problema quando tem % no nome
                    //É retirado qualquer caracter que possa influenciar na requisição AJAX na pososlogia posição[13]
                    var reg = /[^A-Za-z0-9]/g;
                    sStr = retirarAcento(avet[13]);
                    sStr = sStr.replace(reg, ' ');
                    avet[13] = sStr;
                    aMedicamentos[aMedicamentos.length] = avet.join('_|_');
                }
            }
            if (aMedicamentos.length > 0) {
                objParam.aMedicamentos = aMedicamentos;
            } else {
                alert("Nenhum medicamento para retirar!");
                $('confirmar').disabled = false;
                return false;
            }
            objParam.lImprimirComprovante = lComprovante;
            js_divCarregando('Aguarde, salvando retirada de medicamentos...', 'msgBoxA');
            js_ajax(objParam, 'js_retornoConfirma');

            $('confirmar').disabled = true;
            <?php if ($oConfigFarmacia->fa02_b_novaretirada == 'f') : ?>
        } else {
            $('confirmar').disabled = false;
        }
        <?php endif; ?>
    }

    function retirarAcento(text) {
        text = text.replace(new RegExp('[ÁÀÂÃ]', 'gi'), 'A');
        text = text.replace(new RegExp('[ÉÈÊ]', 'gi'), 'E');
        text = text.replace(new RegExp('[ÍÌÎ]', 'gi'), 'I');
        text = text.replace(new RegExp('[ÓÒÔÕ]', 'gi'), 'O');
        text = text.replace(new RegExp('[ÚÙÛ]', 'gi'), 'U');
        text = text.replace(new RegExp('[Ç]', 'gi'), 'C');
        text = text.replace(new RegExp('[àáâã]', 'gi'), 'a');
        text = text.replace(new RegExp('[éèê]', 'gi'), 'e');
        text = text.replace(new RegExp('[íìî]', 'gi'), 'i');
        text = text.replace(new RegExp('[óòôõ]', 'gi'), 'o');
        text = text.replace(new RegExp('[úùû]', 'gi'), 'u');
        text = text.replace(new RegExp('[ç]', 'gi'), 'c');
        return text;
    }

    function js_retornoConfirma(objAjax) {
        js_removeObj('msgBoxA');
        var objRetorno = JSON.parse(objAjax.responseText);

        if (objRetorno.status == 1) {
            // Verifico se o acompanhamento do hiperdia deve ser aberto
            lHiperdia = js_verificaHiperdia(objRetorno.iRetirada, false);
            iCgs = $F('fa04_i_cgsund');
            let nomeSocial = $('z01_nome_social').value;
            nomeSocial = nomeSocial == null ? '' : nomeSocial;

            if (nomeSocial.length > 0) {
                $('z01_nome_social').style.backgroundColor = "#97CAE6";
            } else {
                $('z01_nome_social').style.backgroundColor = "#E6E4F1";
            }

            <?php
            // Se o parâmetro para verificação dos medicamentos do hiperdia estiver setado
            if ($oConfigFarmacia->fa02_i_verificapacientehiperdia == 1 && $oConfigFarmacia->fa02_b_novaretirada != "t") {
                echo "js_verificaHiperdia(objRetorno.iRetirada);";
            }
            ?>

            //retorno confirmar
            $('fa04_i_codigo').value = objRetorno.fa04_i_codigo;
            $('m40_codigo').value = objRetorno.m40_codigo;

            //Blokear todas os campos do formulario
            $('fa06_f_quant').disabled = true;
            $('fa06_i_matersaude').disabled = true;
            $('m60_descr').disabled = true;

            //limpando grids
            objGridRemedios.clearAll(true);
            objGridRemedios.renderRows();

            //liberar os outros 3 botões
            $('comprovante').disabled = false;
            $('retirada').disabled = false;
            $('continuar').disabled = false;
            $('confirmar').disabled = true;

            while ($('DadosGridRemedios').length != 0) {
                $('DadosGridRemedios').remove(0);
            }

            if (objRetorno.lImprimirComprovante == true || '<?=$oConfigFarmacia->fa02_b_comprovante?>' == 't') {
                js_imprime();
            }

            <?php
            if ($oConfigFarmacia->fa02_b_novaretirada == "t") {
                // Se o parâmetro para verificação dos medicamentos do h
                if ($oConfigFarmacia->fa02_i_verificapacientehiperdia == 1) {
                    echo "location.href = 'far1_far_retirada001.php?lAbreAcompanhamento='";
                    echo "+lHiperdia+'&iRetirada='+objRetorno.iRetirada+'&iCgs='+iCgs;";
                } else {
                    echo "alert('Retirada efetuada com sucesso!');";
                    echo "location.href = 'far1_far_retirada001.php';";
                }
            } else {
                echo "alert('Retirada efetuada com sucesso!');";
            }
            ?>
        } else {
            alert(objRetorno.message.urlDecode());
        }
    }

    function js_abreAcompanhamentoHiperdia(iCgs, iRetirada) {
        iTop = 200 / 6;
        iLeft = 15 / 5;
        js_OpenJanelaIframe(
            '',
            'db_iframe_acompanhamento',
            'far4_far_cadacomppachiperdia001.php?' + 'iCgs=' + iCgs + '&iRetirada=' + iRetirada,
            "Acompanhamento de Paciente do  Hiperdia",
            true,
            iTop,
            iLeft,
            screen.availWidth - 30,
            screen.availHeight - 200
        );
    }

    function js_verificaHiperdia(iRetirada, lAbrePopUp) {
        if (lAbrePopUp == undefined) {
            lAbrePopUp = true;
        }
        oSel = $('DadosGridRemedios');
        iNumRowsGrid = oSel.length;

        for (var iCont = 0; iCont < iNumRowsGrid; iCont++) {
            // Se o medicamento foi dispensado
            if ($F('editQuant' + iCont) != '' && $F('editQuant' + iCont) > 0) {
                var aDados = oSel.options[iCont].text.split('#');

                /* Se o medicamento é um medicamento do hiperdia e está nos continuados do paciente
         ou foi marcado para cadastrar nos continuados */
                if (aDados[16] == 'true' && (aDados[0] == 'C' || aDados[0] == 'A')) {
                    if (lAbrePopUp) {
                        js_abreAcompanhamentoHiperdia($F('fa04_i_cgsund'), iRetirada);
                    }
                    return 'true';
                }
            }
        }
        return 'false';
    }

    function js_ultimas() {
        var iTop = (screen.availHeight - 600) / 5;
        var left = (screen.availWidth - 600) / 5;
        var remedio = $('fa06_i_matersaude').value;
        var cgs = $('fa04_i_cgsund').value;
        if (remedio != '') {
            js_OpenJanelaIframe(
                "",
                "db_iframe_ultimas",
                "far1_far_ultimas_retiradas001.php?medicamento="
                + remedio + "&cgs=" + cgs,
                "Pesquisa",
                true,
                iTop,
                left,
                800,
                300
            );
        } else {
            alert('Remédio não informado!');
        }
    }

    function js_select_tipo_receita() {
        var count_opt = document.form1.fa04_i_tiporeceita.length;
        for (var i = 0; i < count_opt; i++) {
            vet_op = document.form1.fa04_i_tiporeceita.options[i].value.split('/');
            <?php
            if (!isset($fa42_i_tiporeceita)) {
                echo "alert('Verifique os parametros da farmacia!');return false;";
            }
            ?>
            if (vet_op[1] == <?php if (!isset($fa42_i_tiporeceita) || $fa42_i_tiporeceita == null) {
                echo '0';
            } else {
                echo $fa42_i_tiporeceita;
            }
                ?>) {
                document.form1.fa04_i_tiporeceita.options[i].selected = true;
                js_tpreceita();
                break;
            }
        }
    }

    function js_getCgsCns() {
        // Caso passe daqui significa que existe um CGS ou CNS válido existe para que possa buscar o CNS
        if ($F('s115_c_cartaosus') == '' && !buscouPeloNomeOuCgs) {
            return false;
        }

        const campoCns = document.getElementById('s115_c_cartaosus');

        if (campoCns.value !== '' && !campoCns.value.validaCNS()) {
            alert('Número de CNS inválido para busca e atualização.');
            $('s115_c_cartaosus').value = '';
            return;
        }

        var oParam = new Object();
        oParam.exec = "getCgsCns";
        oParam.iCns = $F('s115_c_cartaosus');
        oParam.cgs = $F('fa04_i_cgsund');

        js_ajax(oParam, 'js_retornogetCgsCns');
    }

    function js_retornogetCgsCns(oRetorno) {
        oRetorno = JSON.parse(oRetorno.responseText);

        if (oRetorno.z01_i_cgsund == '') {
            if (!buscouPeloNomeOuCgs && valorCgsEncontrado === '') {
                alert('CNS não encontrado.');
            }

            if (buscouPeloNomeOuCgs) {
                codigoCartaoSusEncontrado = '';
                buscouPeloNomeOuCgs = false;
            }
            return false;
        }

        const msgConfirm = `Esse CNS pertence ao CGS ${oRetorno.z01_i_cgsund} - ${oRetorno.z01_v_nome.urlDecode()}.\nDeseja contnuar?`;

        //Se a busca for feita pelo campo cns irá cair na validação
        if (cnsAtual !== $F('s115_c_cartaosus') && !buscouPeloNomeOuCgs && valorCgsEncontrado !== '' && !confirm(msgConfirm)) {
            $('s115_c_cartaosus').value = '';
            return;
        }

        if (oRetorno.z01_b_inativo) {
            $('z01_v_nome').value = 'CGS Inativo';
            $('z01_v_cgccpf').value = '';
            $('fa04_i_cgsund').value = '';
            $('s115_c_cartaosus').value = '';
            document.form1.z01_nome_social.value = '';
            document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";
            codigoCartaoSusEncontrado = '';
            valorCgsEncontrado = '';
            cpfAtual = '';
            cnsAtual = '';
            return;
        }

        if (!buscouPeloNomeOuCgs) {
            $('fa04_i_cgsund').value = oRetorno.z01_i_cgsund;
            $('z01_v_nome').value = oRetorno.z01_v_nome.urlDecode();
            $('z01_v_cgccpf').value = oRetorno.z01_v_cgccpf;
            document.form1.z01_nome_social.value = oRetorno.z01_nome_social.urlDecode();

            const nomeSocial = oRetorno.z01_nome_social.urlDecode();

            if (nomeSocial.length > 0) {
                document.form1.z01_nome_social.style.backgroundColor = "#97CAE6";
            } else {
                document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";
            }

            valorCgsEncontrado = oRetorno.z01_i_cgsund;
            cpfAtual = oRetorno.z01_v_cgccpf;

            validaCgsMunicipio($('fa04_i_cgsund').value);
        }

        cnsAtual = $F('s115_c_cartaosus');
        codigoCartaoSusEncontrado = oRetorno.s115_i_codigo;
        js_init();

        buscouPeloNomeOuCgs = false;
        validaCgs.cadastroMicroarea(inputCgs, divAlert);
    }

    function js_pesquisafa41_i_origemreceita(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_far_origemreceita',
                'func_far_origemreceita.php?funcao_js=parent.js_mostraorigemreceita1|fa40_i_codigo|' +
                'fa40_c_descr&chave_validade=true',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.fa41_i_origemreceita.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_far_origemreceita',
                    'func_far_origemreceita.php?funcao_js=parent.js_mostraorigemreceita1|fa40_i_codigo|' +
                    'fa40_c_descr&pesquisa_chave=' + $F('fa41_i_origemreceita') + '&chave_validade=true',
                    'Pesquisa',
                    true
                );
            } else {
                document.form1.fa40_c_descr.value = '';
            }
        }
    }

    function js_mostraorigemreceita(chave, erro) {
        document.form1.fa40_c_descr.value = chave;
        if (erro == true) {
            document.form1.fa41_i_origemreceita.focus();
            document.form1.fa41_i_origemreceita.value = '';
        }
    }

    function js_mostraorigemreceita1(chave1, chave2) {
        document.form1.fa41_i_origemreceita.value = chave1;
        document.form1.fa40_c_descr.value = chave2;
        db_iframe_far_origemreceita.hide();
    }

    function js_preencheMedicoRecemCadastrado(iCod, sNome) {
        $('fa04_i_profissional').value = iCod;
        js_pesquisafa04_i_profissional(false);
    }

    function js_pesquisareceita(mostra) {
        if ($F('fa04_i_cgsund') == '') {
            alert('Informe o paciente (CGS) primeiro.');
            $('fa04_i_receita').value = '';
            return false;
        }

        if (mostra == true) {
            js_OpenJanelaIframe('',
                'db_iframe_sau_receitamedica',
                'func_sau_receitamedica.php?' +
                'funcao_js=parent.js_mostrareceita|s158_i_codigo|db_s158_i_tiporeceita|' +
                'db_s158_i_profissional|dl_profissional|s158_d_validade' +
                '&lFiltrarAnuladas=true&lFiltrarAtendidas=true&iChaveCgs=' + $F('fa04_i_cgsund'),
                'Pesquisa de Receitas',
                true
            );
        } else {
            if ($F('fa04_i_receita') != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_sau_receitamedica',
                    'func_sau_receitamedica.php?' +
                    'chave_s158_i_codigo=' + $F('fa04_i_receita') + '&funcao_js=parent.js_mostrareceita|' +
                    's158_i_codigo|db_s158_i_tiporeceita|db_s158_i_profissional|dl_profissional|s158_d_validade' +
                    '&lFiltrarAnuladas=true&lFiltrarAtendidas=true&iChaveCgs=' + $F('fa04_i_cgsund') +
                    '&nao_mostra=true',
                    'Pesquisa de Receitas',
                    false
                );
            } else {
                js_limpaDadosReceita();
            }
        }
    }

    var lReceitaSistema = true;

    function js_mostrareceita(iCodReceita, iTipoReceita, iProfissional, sNomeProf, dValidade) {
        /**
         * Removido aviso de recita inválida. Deve aceitar o código da receita do usuário
         */
        if (iCodReceita == '') {
            lReceitaSistema = false;

            $('fa04_i_cgsund').disabled = false;
            $('z01_v_nome').disabled = false;
            $('fa04_i_profissional').disabled = false;
            $('z01_nome').disabled = false;
            $('s115_c_cartaosus').disabled = false;
            $('fa04_d_dtvalidade').value = '';
            $('fa04_i_tiporeceita').value = 'N/3/N//N/N';
            js_tpreceita();
            objGridRemedios.clearAll(true);
            return false;
        }

        sAtual = '<?=date('Ymd', db_getsession('DB_datausu'))?>';
        sVal = dValidade.split('-').join('');

        if (sAtual > sVal) { // Receita vencida
            alert('Esta receita venceu dia ' + js_formataData(dValidade));
            $('fa04_i_receita').value = '';
            db_iframe_sau_receitamedica.hide();
            return false;
        }

        $('fa04_i_receita').value = iCodReceita;
        js_selectOptionByValue($('fa04_i_tiporeceita'), iTipoReceita);
        js_tpreceita();
        iInd = $('fa04_i_tiporeceita').selectedIndex;
        $('fa04_i_tiporeceita').onchange = function () {
            js_selectDisabled($('fa04_i_tiporeceita'), iInd);
        };
        $('fa04_i_profissional').value = iProfissional;
        $('z01_nome').value = sNomeProf;
        $('fa04_d_dtvalidade').value = js_formataData(dValidade);
        $('fa04_i_cgsund').disabled = true;
        $('z01_v_nome').disabled = true;
        $('fa04_i_profissional').disabled = true;
        $('z01_nome').disabled = true;
        $('s115_c_cartaosus').disabled = true;
        $('ancora_cgs').onclick = '';
        $('ancora_profissional').onclick = '';

        db_iframe_sau_receitamedica.hide();
        js_abreMedicamentosReceita();
    }

    function js_limpaDadosReceita() {
        $('fa04_i_receita').value = '';
        $('fa04_i_profissional').value = '';
        $('z01_nome').value = '';
        $('fa04_d_dtvalidade').value = '';
        $('fa04_i_cgsund').disabled = false;
        $('z01_v_nome').disabled = false;
        $('fa04_i_profissional').disabled = false;
        $('z01_nome').disabled = false;
        $('s115_c_cartaosus').disabled = false;
        $('ancora_cgs').onclick = function () {
            js_pesquisafa04_i_cgsund(true);
        };
        $('ancora_profissional').onclick = function () {
            js_pesquisafa04_i_profissional(true);
        };
        $('fa04_i_tiporeceita').onchange = function () {
            js_tpreceita();
        };
    }

    function js_selectOptionByValue(oSel, sValue) {
        for (var iCont = 0; iCont < oSel.options.length; iCont++) {
            if (oSel.options[iCont].value.split('/')[1] == sValue) {
                oSel.selectedIndex = iCont;
                break;
            }
        }
    }

    function js_selectDisabled(oSel, iValue) {
        oSel.selectedIndex = iValue;
    }

    function js_formataData(dData) {
        if (dData == undefined || dData.length != 10) {
            return dData;
        }
        return dData.substr(8, 2) + '/' + dData.substr(5, 2) + '/' + dData.substr(0, 4);
    }

    function js_abreMedicamentosReceita() {
        if ($F('fa04_i_receita') == '') {
            return false;
        }

        sGet = 's159_i_receita=' + $F('fa04_i_receita') + '&fa04_i_cgsund=' + $F('fa04_i_cgsund');

        iTop = (screen.availHeight - 710) / 2;
        iLeft = (screen.availWidth - 800) / 2;
        js_OpenJanelaIframe(
            '',
            'db_iframe_medicamentosreceita',
            'sau4_medicamentosreceita.iframe.php?' + sGet,
            'Medicamentos da Receita',
            true,
            iTop,
            iLeft,
            800,
            500
        );
    }

    /**** funcoes que setam campos do iframe dos continuados */

    function js_mostrafar_matersaude(chave, erro) {
        oFormFrame = document.getElementById('IFdb_iframe_continuados').contentDocument.form1;
        oFormFrame.m60_descr.value = chave;

        if (erro == true) {
            oFormFrame.fa10_i_medicamento.focus();
            oFormFrame.fa10_i_medicamento.value = '';
        }
    }

    function js_mostrafar_matersaude1(chave1, chave2) {
        oFormFrame = document.getElementById('IFdb_iframe_continuados').contentDocument.form1;
        oFormFrame.fa10_i_medicamento.value = chave1;
        oFormFrame.m60_descr.value = chave2;
        db_iframe_far_matersaude.hide();
    }

    function js_mostrafar_programa(chave, erro) {
        oFormFrame = document.getElementById('IFdb_iframe_continuados').contentDocument.form1;
        oFormFrame.fa12_c_descricao.value = chave;
        if (erro == true) {
            oFormFrame.fa10_i_programa.focus();
            oFormFrame.fa10_i_programa.value = '';
        }
    }

    function js_mostrafar_programa1(chave1, chave2) {
        oFormFrame = document.getElementById('IFdb_iframe_continuados').contentDocument.form1;
        oFormFrame.fa10_i_programa.value = chave1;
        oFormFrame.fa12_c_descricao.value = chave2;
        db_iframe_far_programa.hide();
    }

    /*  fim das funcoes do iframe dos continuados ****/

    <?php
    if ($oConfigFarmacia->fa02_i_acaoprog != 0 && $oConfigFarmacia->fa02_i_acaoprog != null) {
        echo "js_pesquisafa10_i_programa(false);";
    }
    ?>
    function js_pesquisafa10_i_programa(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_far_programa',
                'func_far_programa.php?funcao_js=parent.js_mostrafar_programa1|fa12_i_codigo|fa12_c_descricao',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.fa10_i_programa.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_far_programa',
                    'func_far_programa.php?pesquisa_chave=' + document.form1.fa10_i_programa.value +
                    '&funcao_js=parent.js_mostrafar_programa',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.fa12_i_codigo.value = '';
            }
        }
    }

    function js_mostrafar_programa(chave, erro) {
        document.form1.fa12_c_descricao.value = chave;
        if (erro == true) {
            document.form1.fa10_i_programa.focus();
            document.form1.fa10_i_programa.value = '';
        }
    }

    function js_mostrafar_programa1(chave1, chave2) {
        document.form1.fa10_i_programa.value = chave1;
        document.form1.fa12_c_descricao.value = chave2;
        db_iframe_far_programa.hide();
    }

    function js_tiporeceita(iOpcao) {
        $('lotes').disabled = iOpcao != 1;
    }

    function js_novoCgs() {
        sUrl = 'sau1_manutencaocgs001.php';
        js_OpenJanelaIframe('', 'db_iframe_cgs_und', sUrl, 'Cadastro de CGM', true);
    }

    function js_mostrarNovoCgs() {
        $('fa04_i_cgsund').value = arguments[0];
        $('z01_v_nome').value = arguments[1];
        $('s115_c_cartaosus').value = '';
    }

    if ($('fa01_codigobarras')) {
        $('fa01_codigobarras').observe('keyup', function (event) {
            if (event.which == 13) {
                document.form1.m60_descr.focus();
            }
        });
    }

    function isDataPrescricaoValida() {
        const vet = document.form1.fa04_i_tiporeceita.value.split('/');
        if (vet[6] != 't') {
            return true;
        }

        if (document.form1.fa04_data_prescricao.value == "") {
            alert("O campo Data Prescrição deve ser preenchido!");
            return false;
        }

        const dataAceita = new Date(js_formatar($F('fa04_data_prescricao'), 'd'));
        dataAceita.setDate(dataAceita.getDate() + parseInt(vet[7]));
        const diaAtual = new Date();

        if (diaAtual > dataAceita) {
            alert("Receita fora da validade, conforme Data Prescrição informada");
            return false;
        }
        return true;
    }

    function obrigaNumeroNotificacao() {
        const vet = document.form1.fa04_i_tiporeceita.value.split('/');
        if (vet[8] != 't') {
            return true;
        }

        if (document.form1.fa04_numeronotificacao.value == "") {
            alert("O Número da Notificação é obrigatório, favor informar.");
            return false;
        }

        return true;
    }

    function validaNumeroNotificacao() {
        let numeroNotificacao = document.form1.fa04_numeronotificacao;

        if (numeroNotificacao.value.length > 19) {
            alert('Número de Notificação informado inválido!');
            document.form1.fa04_numeronotificacao.value = '';

        }
    }

    function limpaDadosRequisitante() {
        if (document.form1.z01_v_nomecgs.value === '') {
            document.form1.fa08_i_cgsund.value = '';
            document.form1.ender.value = '';
            document.form1.numero.value = '';
            document.form1.ident.value = '';
        }
    }

    function validaCampoNome() {
        if (document.form1.z01_v_nome.value === '') {
            document.form1.fa04_i_cgsund.value = '';
            document.form1.s115_c_cartaosus.value = '';
            document.form1.z01_v_cgccpf.value = '';
            document.form1.z01_nome_social.value = '';
            document.form1.z01_nome_social.style.backgroundColor = "";
            document.form1.demandaReprimida.disabled = true;

            valorCgsEncontrado = '';
            codigoCartaoSusEncontrado = '';
            cpfAtual = '';
            cnsAtual = '';

            if (document.form1.continuados != undefined) {
                document.form1.continuados.disabled = true;
            }
            js_init();
        }
    }

    function validaRg() {
        let campoRg = document.getElementById('ident');

        campoRg.addEventListener('keyup', function () {
            if (isNaN(campoRg.value)) {
                campoRg.value = '';
                alert('O RG deve ser preenchido apenas com números!');
            }
        })
    }

    validaRg();

    function js_getCgsCpf() {
        const campoCpf = document.getElementById('z01_v_cgccpf');

        if ($F('z01_v_cgccpf') === '') {
            return false;
        }

        if (campoCpf.value !== '' && !validaCPF(campoCpf)) {
            alert('Número de CPF Inválido para busca e atualização.');
            campoCpf.value = '';
            return;
        }

        let oParam = new Object();
        oParam.exec = "getCgsCpf";
        oParam.cpf = $F('z01_v_cgccpf');

        js_ajax(oParam, 'js_retornogetCgsCpf');
    }

    function js_retornogetCgsCpf(oRetorno) {
        oRetorno = JSON.parse(oRetorno.responseText);

        if (oRetorno.z01_i_cgsund == '') {
            if (valorCgsEncontrado === '') {
                alert('CPF não encontrado.');
            }
            return false;
        }

        const msgConfirm = `Esse CPF pertence ao CGS ${oRetorno.z01_i_cgsund} - ${oRetorno.z01_v_nome.urlDecode()}.\nDeseja contnuar?`;

        if (cpfAtual !== $('z01_v_cgccpf').value && valorCgsEncontrado !== '' && !confirm(msgConfirm)) {
            $('z01_v_cgccpf').value = '';
            return;
        }

        if (oRetorno.z01_b_inativo) {
            $('z01_v_nome').value = 'CGS Inativo';
            $('z01_v_cgccpf').value = '';
            $('fa04_i_cgsund').value = '';
            $('s115_c_cartaosus').value = '';
            document.form1.z01_nome_social.value = '';
            document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";
            codigoCartaoSusEncontrado = '';
            valorCgsEncontrado = '';
            cpfAtual = '';
            cnsAtual = '';
            return;
        }

        $('fa04_i_cgsund').value = oRetorno.z01_i_cgsund;
        $('z01_v_nome').value = oRetorno.z01_v_nome.urlDecode();
        $('s115_c_cartaosus').value = oRetorno.s115_c_cartaosus;
        document.form1.z01_nome_social.value = oRetorno.z01_nome_social.urlDecode();

        const nomeSocial = oRetorno.z01_nome_social.urlDecode();

        if (nomeSocial.length > 0) {
            document.form1.z01_nome_social.style.backgroundColor = "#97CAE6";
        } else {
            document.form1.z01_nome_social.style.backgroundColor = "#E6E4F1";
        }

        codigoCartaoSusEncontrado = oRetorno.s115_i_codigo;
        valorCgsEncontrado = oRetorno.z01_i_cgsund;
        cpfAtual = $('z01_v_cgccpf').value;
        cnsAtual = oRetorno.s115_c_cartaosus;
        js_init();

        validaCgsMunicipio($('fa04_i_cgsund').value);
        validaCgs.cadastroMicroarea(inputCgs, divAlert);
    }

    async function atualizarCpfCns() {
        if (valorCgsEncontrado === '') {
            alert('Busque um CGS antes de atualizar.');
            return;
        }

        const campoCpf = document.getElementById('z01_v_cgccpf');
        const campoCns = document.getElementById('s115_c_cartaosus');

        if (!isCpfValido(campoCpf)) {
            return;
        }

        if (!isCnsValido(campoCns)) {
            return;
        }

        if (campoCpf.value === '' && campoCns.value === '') {
            alert('CPF ou CNS deve ser preenchido para atualizar.');
            return;
        }

        if (!confirm('Tem certeza que deseja atualizar?')) {
            return;
        }

        const formData = new FormData();
        formData.append('cgsPaciente', valorCgsEncontrado);
        formData.append('idCartaoSus', codigoCartaoSusEncontrado);
        formData.append('cpf', campoCpf.value);
        formData.append('cns', campoCns.value);
        PHPSession.appendFormData(formData);

        let response = await HttpClient.post(
            `${PHPSession.requestApi}/saude/farmacia/procedimento/atualizar/paciente/cgs-cpf`,
            {body: formData, reportProgress: true, reportMessage: 'Atualizando CNS e CPF.'}
        );

        //Mensagem de erro genérica para erro não previsto
        if (response.file) {
            alert('Houve um erro na atualização dos dados.');
            return
        }

        alert(response.message);

    }

    function isCpfValido(cpf) {
        const valorCpf = cpf.value;

        if (cpf.value !== '' && !validaCPF(cpf)) {
            alert('CPF Inválido.')
            cpf.value = valorCpf;
            return false;
        }
        return true;
    }

    function isCnsValido(cns) {
        if (cns.value !== '' && !cns.value.validaCNS()) {
            alert('CNS Inválido.');
            return false;
        }
        return true;
    }

    function bloqueiaCamposContinuacaoRetirada() {
        const continuandoRetirada = $F('continuandoRetirada');

        if (continuandoRetirada === 'true') {
            $('s115_c_cartaosus').disabled = true;
            $('z01_v_cgccpf').disabled = true;
            $('z01_v_nome').disabled = true;
            $('z01_nome_social').disabled = true;
            $('fa04_i_cgsund').disabled = true;
            $('ancora_cgs').onclick = '';
        }
    }
</script>
