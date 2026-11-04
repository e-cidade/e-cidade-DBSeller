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

//MODULO: saude
$clprontuarios->rotulo->label();
$clcgs->rotulo->label();
$clcgs_und->rotulo->label();

//Prontuario/Agendamento
$clagendamentos->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_i_numcgs");
$clrotulo->label("s115_c_cartaosus");
$clrotulo->label("s115_c_tipo");

$clrotulo->label("descrdepto");

//Médico
$clrotulo->label("sd03_i_codigo");
$clrotulo->label("z01_nome");
//Unidade / Medicos
$clrotulo->label("sd04_i_cbo");
//especmedico
$clrotulo->label("sd27_i_codigo");

//CBO
$clrotulo->label("rh70_sequencial");
$clrotulo->label("rh70_estrutural");
$clrotulo->label("rh70_descr");

//Setor ambulatorial
$oDaoSetorAmbulatorial = new cl_setorambulatorial();
?>
<SCRIPT LANGUAGE="JavaScript">
    team = []
    <?php

    $sql1 = "SELECT sd34_i_codigo,sd34_v_descricao ";
    $sql1 .= "  FROM microarea  ";
    $sql1 .= "  ORDER BY sd34_v_descricao";
    $sql_result = db_query($sql1);
    $num = pg_num_rows($sql_result);
    $conta = "";

    $aArrayPai = array();
    while ($row = pg_fetch_array($sql_result)) {
        $conta = $conta + 1;
        $cod_micro = $row["sd34_i_codigo"];
        $aArrayFilho = array();

        $sub_sql = "SELECT sd35_i_codigo,sd33_v_descricao ";
        $sub_sql .= "  FROM familiamicroarea ";
        $sub_sql .= "       inner join familia on sd33_i_codigo = sd35_i_familia ";
        $sub_sql .= " WHERE sd35_i_microarea = '{$cod_micro}' ";
        $sub_sql .= " ORDER BY sd33_v_descricao";
        $sub_result = db_query($sub_sql);
        $num_sub = pg_num_rows($sub_result);

        if ($num_sub >= 1) {
            $aArrayFilho[] = array('', '');
            $conta_sub = "";

            while ($rowx = pg_fetch_array($sub_result)) {
                $codigo_fam = $rowx["sd35_i_codigo"];
                $nome_fam = $rowx["sd33_v_descricao"];
                $conta_sub = $conta_sub + 1;

                if ($conta_sub == $num_sub) {
                    $aArrayFilho[] = array(urlencode($nome_fam), $codigo_fam);
                    $conta_sub = "";
                } else {
                    $aArrayFilho[] = array(urlencode($nome_fam), $codigo_fam);
                }
            }
        } else {
            $aArrayFilho[] = array("Microarea sem familias cadastradas.", '');
        }
        $aArrayPai[] = $aArrayFilho;
    }
    $sArrayJson = JSON::create()->stringify($aArrayPai);
    ?>
    team = <?=$sArrayJson?>;

    //Inicio da função JS
    function fillSelectFromArray(selectCtrl, itemArray, goodPrompt, badPrompt, defaultItem) {

        var i, j;
        var prompt;

        // empty existing items
        for (i = selectCtrl.options.length; i >= 0; i--) {
            selectCtrl.options[i] = null;
        }

        prompt = (itemArray != null) ? goodPrompt : badPrompt;
        if (prompt == null) {

            selectCtrl.options[0] = new Option('', '');
            j = 0;
        } else {

            selectCtrl.options[0] = new Option(prompt);
            j = 1;
        }

        if (itemArray != null) {

            // add new items
            for (i = 0; i < itemArray.length; i++) {

                selectCtrl.options[j] = new Option(itemArray[i][0].urlDecode());

                if (itemArray[i][1] != null) {
                    selectCtrl.options[j].value = itemArray[i][1];
                }

                <?php
                if( isset($z01_i_familiamicroarea) && $z01_i_familiamicroarea != "" ) {?>
                if (<?=trim($z01_i_familiamicroarea)?> == itemArray[i][1]) {
                    indice = i;
                }
                <?php } ?>
                j++;
            }

            <?php if(isset($z01_i_familiamicroarea) && $z01_i_familiamicroarea != "") { ?>
            selectCtrl.options[indice].selected = true;
            <?php } else { ?>
            selectCtrl.options[0].selected = true;
            <?php } ?>
        }
    }
</script>
<div class="container">
    <form name="form1" method="post" action="">
        <table width='65%'>
            <tr>
                <td>
                    <fieldset>
                        <legend><b>Paciente</b></legend>
                        <div id="status-microarea" class="alert-danger" style="text-align: center;" role="alert" hidden>
                            Paciente sem cadastro em uma microárea!
                        </div>
                        <table border="0" width="100%">
                            <tr>
                                <td align="right" nowrap title="<?= @$Tsd24_i_codigo ?>">
                                    <div style="width: 130px;">
                                        <label for="sd24_i_codigo"><?= @$Lsd24_i_codigo ?></label>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    db_input('sd24_i_codigo', 12, $Isd24_i_codigo, true, 'text', 3);
                                    ?>
                                </td>
                                <?php
                                if ($obj_sau_config->s103_c_lancafaa == "I") { ?>
                                    <td align="right" title="<?= @$Tsd23_i_codigo ?>">
                                        <label for="sd23_i_codigo">
                                            <?php
                                            db_ancora(@$Lsd23_i_codigo, "js_pesquisasd23_i_codigo(true);", $db_opcao);
                                            ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        db_input('sd23_i_codigo', 21, $Isd23_i_codigo, true, 'text', 3);
                                        ?>
                                    </td>
                                    <?php
                                } ?>
                            </tr>
                            <tr>
                                <td align="right" nowrap title="<?= @$Tsd24_i_unidade ?>">
                                    <label for="sd24_i_unidade">
                                        <?php
                                        db_ancora(@$Lsd24_i_unidade, "js_pesquisasd24_i_unidade(true);", 3);
                                        ?>
                                    </label>
                                </td>
                                <td colspan='3' nowrap>
                                    <?php
                                    db_input(
                                        'sd24_i_unidade',
                                        12,
                                        $Isd24_i_unidade,
                                        true,
                                        'text',
                                        3,
                                        "onchange='js_pesquisasd24_i_unidade(false);'"
                                    );
                                    db_input('descrdepto', 52, $Idescrdepto, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_i_cgsund ?>">
                                    <label for="z01_i_cgsund">
                                        <?php
                                        db_ancora(@$Lz01_i_cgsund, "js_pesquisaz01_i_cgsund(true);", $db_opcao);
                                        ?>
                                    </label>
                                </td>
                                <td colspan="3" nowrap>
                                    <?php
                                    db_input(
                                        'z01_i_cgsund',
                                        12,
                                        $Iz01_i_cgsund,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onchange='js_pesquisaz01_i_cgsund(false);'"
                                    );
                                    db_input('z01_v_nome', 52, $Iz01_v_nome, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_nome_social ?>">
                                    <label for="z01_nome_social"><?= @$Lz01_nome_social ?></label>
                                </td>
                                <td colspan="3" nowrap>
                                    <?php
                                    $corNomeSocial = !empty($z01_nome_social) ? "#97CAE6" : "";
                                    db_input(
                                        'z01_nome_social',
                                        68,
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


                            <!-- Micro Área / Familia -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tsd35_i_microarea ?>">
                                    <label for="z01_v_micro" class="bold">Micro:</label>
                                </td>
                                <td>
                                    <select
                                            name="z01_v_micro"
                                            class="readonly"
                                            style="pointer-events: none; touch-action: none;"
                                            aria-disabled="true"
                                            onChange="fillSelectFromArray(this.form.z01_i_familiamicroarea, ((this.selectedIndex == -1) ? null : team[this.selectedIndex-1]));">
                                        <option></option>
                                        <?php
                                        $sql1 = "SELECT sd34_i_codigo,sd34_v_descricao ";
                                        $sql1 .= "  FROM microarea ";
                                        $sql1 .= " ORDER BY sd34_v_descricao";
                                        $sql_result = db_query($sql1);

                                        while ($row = pg_fetch_array($sql_result)) {
                                            $cod_micro = $row["sd34_i_codigo"];
                                            $desc_micro = $row["sd34_v_descricao"];
                                            ?>
                                            <option value="<?= $cod_micro; ?>" <?= $cod_micro == @$sd34_i_codigo ? "selected" : "" ?>>
                                                <?= $desc_micro; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <?php
                                    db_input('sd34_i_codigo', 20, @$Isd34_i_codigo, true, 'hidden', $db_opcao);
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Tsd35_i_familia ?>" align="right">
                                    <label for="z01_i_familiamicroarea" class="bold">Família:</label>
                                </td>
                                <td>
                                    <select
                                            name="z01_i_familiamicroarea"
                                            class="readonly"
                                            style="pointer-events: none; touch-action: none;"
                                            aria-disabled="true"
                                            onchange="if(this.value=='')document.form1.z01_v_micro.value='';">
                                        <option value=""></option>
                                    </select>
                                    <?php
                                    if (isset($z01_i_familiamicroarea) && $z01_i_familiamicroarea != "") { ?>
                                        <script>fillSelectFromArray(document.form1.z01_i_familiamicroarea, team[document.form1.z01_v_micro.selectedIndex - 1]);</script>
                                        <?php
                                    } ?>
                                </td>
                            </tr>

                            <!-- CPF / CGS do municipio -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_cgccpf ?>">
                                    <label for="z01_v_cgccpf"><?= @$Lz01_v_cgccpf ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_v_cgccpf', 12, $Iz01_v_cgccpf, true, 'text', $db_opcao);
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Tz01_registromunicipio ?>" align="right">
                                    <label for="z01_registromunicipio" class="bold">CGS do Munic.</label>
                                </td>
                                <td>
                                    <?php
                                    $xz01_registromunicipio = array('t' => 'SIM', 'f' => 'NÃO');
                                    db_select(
                                        'z01_registromunicipio',
                                        $xz01_registromunicipio,
                                        true,
                                        $db_opcao,
                                        "onchange=js_municipio()"
                                    );
                                    ?>
                                </td>
                            </tr>

                            <!--  CEP  -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_cep ?>">
                                    <label for='z01_v_cep'>
                                        <?php
                                        if (!isset($z01_registromunicipio) || $z01_registromunicipio == 't') {
                                            echo @$Lz01_v_cep;
                                        } else {
                                            db_ancora(@$Lz01_v_cep, "js_cepcon(true);", 1);
                                        }
                                        ?>
                                    </label>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'z01_v_cep',
                                        12,
                                        $Iz01_v_cep,
                                        true,
                                        'text',
                                        (!isset($z01_registromunicipio) || $z01_registromunicipio == 't' ? 3 : $db_opcao)
                                    );
                                    ?>
                                    <input type="button"
                                           name="buscacep"
                                           value="Pesquisar"
                                           onClick="js_cepcon(false)" <?= (!isset($z01_registromunicipio) || $z01_registromunicipio == "t" ? "disabled" : "") ?> >
                                </td>

                                <td colspan="2">
                                    <label style="margin-left: -15px;"
                                           for="distritoSanitario"> <?= @$Lz01_distritosanitario ?> </label>

                                    <select name="z01_distritosanitario" style="width: 155px" id="distritoSanitario">
                                        <option value=""></option>

                                        <?php
                                        $sqlDistritos = "select s153_i_codigo as cod,s153_c_descr as label from sau_distritosanitario";
                                        $sqlDistritos .= ' where s153_ativo = true';
                                        $sqlDistritos .= ' order by s153_c_descr ASC;';
                                        $rsSql = db_query($sqlDistritos);

                                        if (pg_num_rows($rsSql) > 0) {
                                            $distritos = db_utils::getCollectionByRecord($rsSql);

                                            foreach ($distritos as $distrito) {
                                                ?>
                                                <option value="<?= $distrito->cod ?>"
                                                    <?= (@$z01_distritosanitario == $distrito->cod) ?
                                                        "selected" :
                                                        "" ?>>
                                                    <?= $distrito->label ?>
                                                </option>
                                                <?php
                                            }
                                        } ?>
                                    </select>
                                </td>
                            </tr>

                            <!--  Endereço -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_ender ?>">
                                    <label for='z01_v_ender'>
                                        <?php
                                        db_ancora(@$Lz01_v_ender, "js_ruas();", $db_opcao);
                                        ?>
                                    </label>
                                </td>
                                <td colspan="3">
                                    <?php
                                    db_input(
                                        'z01_v_ender',
                                        68,
                                        $Iz01_v_ender,
                                        true,
                                        'text',
                                        (!isset($z01_registromunicipio) || $z01_registromunicipio == 't' ? 3 : $db_opcao)
                                    );
                                    ?>
                                </td>
                            </tr>

                            <!--  Número / Complemento -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_i_numero ?>">
                                    <label for="z01_i_numero"><?= @$Lz01_i_numero ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_i_numero', 12, $Iz01_i_numero, true, 'text', $db_opcao);
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Tz01_v_compl ?>" align="right">
                                    <label for="z01_v_compl"><?= @$Lz01_v_compl ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_v_compl', 21, $Iz01_v_compl, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>

                            <!--  Bairro -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_bairro ?>">
                                    <label for='z01_v_bairro'>
                                        <?php
                                        db_ancora(@$Lz01_v_bairro, "js_bairro();", $db_opcao);
                                        ?>
                                    </label>
                                </td>
                                <td colspan="3">
                                    <?php
                                    db_input('j13_codi', 10, @$Ij13_codi, true, 'hidden', 3);
                                    db_input(
                                        'z01_v_bairro',
                                        68,
                                        $Iz01_v_bairro,
                                        true,
                                        'text',
                                        (!isset($z01_registromunicipio) || $z01_registromunicipio == 't' ? 3 : $db_opcao)
                                    );
                                    ?>
                                </td>
                            </tr>

                            <!--  Municipio / UF -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_munic ?>">
                                    <label for="z01_v_munic" class="bold">Município:</label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_v_munic', 30, $Iz01_v_munic, true, 'text', 3);
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Tz01_v_uf ?>" align="right">
                                    <label for="z01_v_uf"><?= @$Lz01_v_uf ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_v_uf', 2, $Iz01_v_uf, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>

                            <!--  Telefone / Cartão SUS -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_v_telcel ?>">
                                    <label for="z01_v_telcel"><?= @$Lz01_v_telcel ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'z01_v_telcel',
                                        12,
                                        $Iz01_v_telcel,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeypress='mascara(this, true)'; onpaste='mascaraPaste(this, true)';",
                                        '',
                                        '',
                                        '',
                                        15
                                    );
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Ts115_c_cartaosus ?>" align="right">
                                    <label for="s115_c_cartaosus"><?= @$Ls115_c_cartaosus ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('s115_i_codigo', 15, @$Is115_i_codigo, true, 'hidden', $db_opcao);
                                    db_input('s115_c_cartaosus', 15, $Is115_c_cartaosus, true, 'text', $db_opcao);

                                    $x = array("D" => "D", "P" => "P");
                                    db_select('s115_c_tipo', $x, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>

                            <!--  Nascimento / Sexo -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_d_nasc ?>">
                                    <label for="z01_d_nasc"><?= @$Lz01_d_nasc ?></label>
                                </td>
                                <td style="display: flex;">
                                    <?php
                                    db_inputdata(
                                        'z01_d_nasc',
                                        @$z01_d_nasc_dia,
                                        @$z01_d_nasc_mes,
                                        @$z01_d_nasc_ano,
                                        true,
                                        'text',
                                        $db_opcao
                                    );
                                    ?>
                                    <div style="padding-left: 5px; display: flex; align-items: center; height: 18px; width: 200px"
                                         id="linhaDaIdadeCompleta"></div>
                                </td>
                                <td nowrap title="<?= @$Tz01_v_sexo ?>" align="right">
                                    <label for="z01_v_sexo"><?= @$Lz01_v_sexo ?></label>
                                </td>
                                <td>
                                    <?php
                                    $x = array('M' => 'MASCULINO', 'F' => 'FEMININO');
                                    db_select('z01_v_sexo', $x, true, $db_opcao);
                                    ?>
                                </td>
                            </tr>

                            <!-- Data Cadastro / Login -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tz01_d_cadast ?>">
                                    <label for="z01_d_cadast" class="bold">Cadastro:</label>
                                </td>
                                <td>
                                    <?php
                                    db_inputdata(
                                        'z01_d_cadast',
                                        @$z01_d_cadast_dia,
                                        @$z01_d_cadast_mes,
                                        @$z01_d_cadast_ano,
                                        true,
                                        'text',
                                        3
                                    );
                                    ?>
                                </td>
                                <td nowrap title="<?= @$Tz01_i_login ?>" align="right">
                                    <label for="nome"><?= @$Lz01_i_login ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_i_login', 6, $Iz01_i_login, true, 'hidden', 3);
                                    db_input('nome', 21, @$nome, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tsd24_i_tipo ?>" align="right">
                                    <label for="sd24_i_tipo"><?= @$Lsd24_i_tipo ?></label>
                                </td>
                                <td colspan="3">
                                    <select name="sd24_i_tipo" id="sd24_i_tipo">
                                        <option value="">Selecione</option>
                                        <?php
                                        $sSql = "select s145_i_codigo as cod,s145_c_descr as label from sau_tiposatendimento";
                                        $rsSelect = db_query($sSql);

                                        for ($y = 0; $y < pg_num_rows($rsSelect); $y++) {
                                            db_fieldsmemory($rsSelect, $y);
                                            ?>
                                            <option value="<?= $cod ?>" <?= (@$sd24_i_tipo == $cod) ? "selected" : "" ?>><?= $label ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tsd24_i_motivo ?>" align="right">
                                    <label for="sd24_i_motivo"><?= @$Lsd24_i_motivo ?></label>
                                </td>
                                <td colspan="3">
                                    <select name="sd24_i_motivo" id="sd24_i_motivo">
                                        <option value="">Selecione</option>
                                        <?php
                                        $sSql = "select s144_i_codigo as cod,s144_c_descr as label from sau_motivoatendimento";
                                        $sSql .= ' where s144_situacao = true';
                                        //plugin ESF operation#4 - adicionando AND ao WHERE via plugin ESF
                                        $sSql .= ' order by s144_c_descr;';
                                        $rsSelect = db_query($sSql);

                                        for ($y = 0; $y < pg_num_rows($rsSelect); $y++) {
                                            db_fieldsmemory($rsSelect, $y);
                                            ?>
                                            <option value="<?= $cod ?>" <?= (@$sd24_i_motivo == $cod) ? "selected" : "" ?>><?= $label ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tz01_i_login ?>" align="right">
                                    <label for="sd24_i_acaoprog" class="bold">Ação Programática:</label>
                                </td>
                                <td colspan="3">
                                    <select name="sd24_i_acaoprog" id="sd24_i_acaoprog">
                                        <option value="">Selecione</option>
                                        <?php
                                        $clFarPrograma = new cl_far_programa;
                                        $campos = "fa12_i_codigo as cod,fa12_c_descricao as label";
                                        $order = "fa12_c_descricao";
                                        $where = "fa12_ativo = 't'";
                                        $sSql = $clFarPrograma->sql_query_file(null, $campos, $order, $where);
                                        $rsSelect = db_query($sSql);

                                        for ($y = 0; $y < pg_num_rows($rsSelect); $y++) {
                                            db_fieldsmemory($rsSelect, $y);
                                            ?>
                                            <option value="<?= $cod ?>" <?= (@$sd24_i_acaoprog == $cod) ? "selected" : "" ?>><?= $label ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td  nowrap align="right">
                                   <label for="sd24_unidadeencaminhadora" class="bold">Unidade Encaminhadora:</label>
                                </td>
                                <td colspan="3">
                                    <select name="sd24_unidadeencaminhadora" id="sd24_unidadeencaminhadora">
                                        <option value=""></option>
                                        <?php
                                            $daoUnidadesEncaminhadoras = new cl_unidadesencaminhadoras;
                                            $whereUnidadesEncaminhadoras = "sd112_ativo = true";
                                            $sqlUnidadesEncaminhadoras = $daoUnidadesEncaminhadoras->sql_query(
                                                null,
                                                '*',
                                                "sd112_descricao ASC",
                                                $whereUnidadesEncaminhadoras
                                            );                                            
                                            $result = db_query($sqlUnidadesEncaminhadoras);
                                            for($i=0;$i<pg_num_rows($result);$i++){
                                                $unidadesEncaminhadoras = db_utils::fieldsMemory($result,$i);                                                                                             
                                            ?>
                                                <option 
                                                    value="<?=$unidadesEncaminhadoras->sd112_sequencial?>" 
                                                    <?= (@$sd24_unidadeencaminhadora == $unidadesEncaminhadoras->sd112_sequencial) ? "selected" : "" ?>>
                                                   <?=$unidadesEncaminhadoras->sd112_descricao?>
                                                </option>
                                            <?php
                                            }
                                        ?>                                                                           
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap align="right" class="bold">
                                    <label for="sd24_setorambulatorial" class="bold">Setor:</label>
                                </td>
                                <td colspan="3">
                                    <?php
                                    $sCampos = "sd91_codigo,sd91_descricao";
                                    $sWhere = "sd91_local = 1 and sd91_unidades = " . db_getsession('DB_coddepto');
                                    $sSql = $oDaoSetorAmbulatorial->sql_query_file(null, $sCampos, null, $sWhere);
                                    $rsSelect = db_query($sSql);
                                    db_selectrecord(
                                        "sd24_setorambulatorial",
                                        $rsSelect,
                                        true,
                                        1,
                                        "",
                                        "",
                                        "",
                                        "",
                                        "",
                                        1
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $db_opcaoprof = isset($sd29_i_profissional) && (int)$sd29_i_profissional != 0 ? 3 : $db_opcao;
                    ?>
                    <fieldset>
                        <legend>Profissional de Atendimento</legend>
                        <table border="0" width="100%">
                            <!-- PROFISSIONAL -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tsd03_i_codigo ?>">
                                    <div style="width: 130px;">
                                        <label for='sd03_i_codigo'>
                                            <?php
                                            db_ancora(
                                                @$Lsd03_i_codigo,
                                                "js_pesquisasd03_i_codigo(true,1);",
                                                $db_opcaoprof
                                            );
                                            ?>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    db_input(
                                        'sd03_i_codigo',
                                        12,
                                        $Isd03_i_codigo,
                                        true,
                                        'text',
                                        $db_opcaoprof,
                                        "onchange='js_pesquisasd03_i_codigo(false,1);' onFocus=\"nextfield='rh70_estrutural'\""
                                    );
                                    db_input('z01_nome', 52, $Iz01_nome, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                            <!-- CBO -->
                            <tr>
                                <td align="right" nowrap title="<?= @$Tsd04_i_cbo ?>">
                                    <label for='rh70_estrutural'>
                                        <?php
                                        db_ancora(@$Lsd04_i_cbo, "js_pesquisasd04_i_cbo(true,false);", $db_opcaoprof);
                                        ?>
                                    </label>
                                </td>
                                <td>
                                    <?php
                                    db_input('sd27_i_codigo', 10, $Isd27_i_codigo, true, 'hidden', $db_opcaoprof);
                                    db_input('rh70_sequencial', 10, $Irh70_sequencial, true, 'hidden', $db_opcaoprof);
                                    db_input(
                                        'rh70_estrutural',
                                        12,
                                        $Irh70_estrutural,
                                        true,
                                        'text',
                                        $db_opcaoprof,
                                        "onchange='js_pesquisasd04_i_cbo(false,false);' onFocus=\"nextfield='sd23_d_consulta'\""
                                    );
                                    db_input('rh70_descr', 52, $Irh70_descr, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
        <input type="hidden" value="incluir" name="incluir"/>
        <button type="button" id="db_opcao">
            <i class="fas fa-check"></i>
            Confirmar
        </button>
        <button name="pesquisar" type="button" id="pesquisar" onclick="js_pesquisaprontuarios();">
            <i class="fas fa-search"></i>
            Pesquisar
        </button>
        <button name="limpar" type="button" id="limpar" onclick="js_limpa();">
            <i class="fas fa-file-medical"></i>
            Nova FAA
        </button>
        <button name="finalizarAtendimento" type="button" id="finalizarAtendimento"
                onclick="js_finalizarAtendimento();">
            <i class="fas fa-check-double"></i>
            Finalizar Atendimento
        </button>
        <button name="encaminhar" type="button" id="encaminhar" onclick="js_encaminharProcedimento();">
            <i class="fas fa-forward"></i>
            Encaminhar
        </button>
        <br>
        <button name="emitir" type="button" id="emitir" onclick="js_emitirFaa();">
            <i class="fas fa-print"></i>
            Emitir FAA
        </button>
        <?php
        $oSauConfig = loadConfig('sau_config');
        selectModelosFaa($oSauConfig->s103_i_modelofaa);
        ?>
    </form>
</div>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script>

    const MENSAGENS_FICHAATENDIMENTORPC = "saude.ambulatorial.sau4_fichaatendimento_RPC.";
    const divAlert = document.getElementById('status-microarea');
    const inputCgs = {
        id: document.getElementById('z01_i_cgsund'),
        nome: document.getElementById('z01_v_nome')
    };
    const validaCgs = new ValidaCgs(inputCgs);

    window.onload = () => {
        validaCgs.cadastroMicroarea(inputCgs, divAlert);
        recuperaCelularFormatado();
    }
    /**
     * Realiza a validação do CNS ao submeter o formulario
     */
    $('db_opcao').onclick = function () {
        validaCampos();
    };
    <?php
    if (isset($desabilitaencaminhamento) && !empty($desabilitaencaminhamento && !$incluir)) { ?>
    $('encaminhar').disabled = true;
    alert('Necessário confirmar Ficha de Atendimento antes de encaminhar.');
    <?php
    } ?>

    function validaCampos() {

        var lObrigarCNS = <?=$lObrigarCNS ? 1 : 0;?>

        if (lObrigarCNS && empty($F('s115_c_cartaosus'))) {

            alert(_M(MENSAGENS_FICHAATENDIMENTORPC + 'erro_buscar_cartao_sus'));
            return;
        }

        if (!empty($F('s115_c_cartaosus')) && !$F('s115_c_cartaosus').validaCNS()) {

            alert(_M(MENSAGENS_FICHAATENDIMENTORPC + 'numero_cns_invalido'));
            return false;
        }

        if (!empty($F('sd03_i_codigo')) && empty($F('rh70_estrutural'))) {

            alert(_M(MENSAGENS_FICHAATENDIMENTORPC + 'especialidade_nao_informado'));
            return false;
        }

        document.form1.submit();
    }

    var oGet = js_urlToObject();

    if (oGet.chavepesquisaprontuario && oGet.chavepesquisaprontuario != "") {

        var oParametro = new Object();
        oParametro.sExecucao = 'buscaUltimaObservacaoDaMovimentacao';
        oParametro.iProntuario = oGet.chavepesquisaprontuario;
        oParametro.iTelaOrigem = 1;

        var oObjeto = {};
        oObjeto.method = 'post';
        oObjeto.parameters = 'json=' + Object.toJSON(oParametro);
        oObjeto.onComplete = function (oAjax) {

            var oRetorno = JSON.parse(oAjax.responseText);

            if (oRetorno.iStatus != 1) {

                alert(_M(MENSAGENS_FICHAATENDIMENTORPC + 'erro_buscar_ultima_observacao'));
                return false;
            }

            if (oRetorno.sObservacao != '') {
                alert(oRetorno.sObservacao.urlDecode());
            }
        };

        oObjeto.asynchronous = true;

        new Ajax.Request("sau4_fichaatendimento.RPC.php", oObjeto);
    }


    function js_emitirFaa() {

        if ($F('sd24_i_codigo') != '') {

            var oParam = new Object();
            oParam.exec = 'gerarFAATXT';
            oParam.sChaveProntuarios = $F('sd24_i_codigo');
            oParam.iModelo = $F('s103_i_modelofaa');
            js_webajax(oParam, 'js_retornoEmissaofaa', 'sau4_ambulatorial.RPC.php');
        } else {
            alert('Nenhuma FAA para gerar.');
        }
    }

    function js_retornoEmissaofaa(oAjax) {

        oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.iStatus == 2) {

            message_ajax(oRetorno.sMessage.urlDecode());
            return false;
        } else {

            if (oRetorno.iTipo == 1) {
                js_emitiefaaPDF(oRetorno);
            } else {
                js_emitirfaaTXT(oRetorno);
            }
        }
    }

    function js_emitiefaaPDF(oDados) {

        sChave = '?chave_sd29_i_prontuario=' + oDados.sChaveProntuarios;
        var WindowObjectReference;
        var strWindowFeatures = "menubar=yes,location=no,resizable=yes,scrollbars=yes,status=yes";
        sArquivo = js_getArquivoFaa($F('s103_i_modelofaa'));
        WindowObjectReference = window.open(sArquivo + sChave, "CNN_WindowName", strWindowFeatures);
    }

    function js_emitirfaaTXT(oRetorno) {

        iTop = 20;
        iLeft = 5;
        iHeight = screen.availHeight - 210;
        iWidth = screen.availWidth - 35;
        sChave = 'sSessionNome=' + oRetorno.sSessionNome;

        js_OpenJanelaIframe(
            '',
            'db_iframe_visualizador',
            'sau2_fichaatend002.php?' + sChave,
            'Visualisador',
            true,
            iTop,
            iLeft,
            iWidth,
            iHeight
        );
    }

    function js_getArquivoFaa(iCodModelo) {

        oSel = $('sArquivoFaa');
        for (var iCont = 0; iCont < oSel.length; iCont++) {

            if (iCodModelo == oSel.options[iCont].value) {
                return oSel.options[iCont].text;
            }
        }
    }

    function js_pesquisasd03_i_codigo(mostra) {

        var sEspecialidade = '';

        if (!empty($F('rh70_sequencial'))) {
            sEspecialidade = '&iRhcboSequencial=' + $F('rh70_sequencial');
        }

        if (mostra == true) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_medicos',
                'func_medicos.php?funcao_js=parent.js_mostramedicos1|sd03_i_codigo|z01_nome'
                + '&chave_sd06_i_unidade=' + document.form1.sd24_i_unidade.value
                + sEspecialidade + '&prof_ativo=1',
                'Pesquisa Profissional',
                true
            );
        } else {

            if (document.form1.sd03_i_codigo.value != '') {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_medicos',
                    'func_medicos.php?pesquisa_chave=' + document.form1.sd03_i_codigo.value
                    + '&funcao_js=parent.js_mostramedicos'
                    + '&chave_sd06_i_unidade=' + document.form1.sd24_i_unidade.value
                    + sEspecialidade + '&prof_ativo=1',
                    'Pesquisa Profissional',
                    false
                );
            } else {
                limpaCamposEspecialidade();
                $('sd27_i_codigo').value = '';
                $('z01_nome').value = '';
            }
        }
    }

    function js_mostramedicos(chave, erro) {

        document.form1.z01_nome.value = chave;

        if (erro == true) {

            document.form1.sd03_i_codigo.focus();
            document.form1.sd03_i_codigo.value = '';
            document.form1.sd27_i_codigo.value = '';
            document.form1.rh70_estrutural.value = '';
            document.form1.rh70_descr.value = '';
        } else {

            if (empty($F('rh70_sequencial'))) {

                limpaCamposEspecialidade();
                js_pesquisasd04_i_cbo(true,true);
            } else {
                js_pesquisasd04_i_cbo(false,true);
            }
        }
    }

    function js_mostramedicos1(chave1, chave2) {

        document.form1.sd03_i_codigo.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_medicos.hide();

        if (empty($F('rh70_sequencial'))) {

            limpaCamposEspecialidade();
            js_pesquisasd04_i_cbo(true,true);
        } else {
            js_pesquisasd04_i_cbo(false,true);
        }
    }

    function js_pesquisasd04_i_cbo(mostra,limit) {

        var sUrl = 'func_especialidade_recepcao.php?funcao_js=parent.js_mostrarhcbo';
        if(limit){
            sUrl = 'func_especialidade_recepcao.php?limit=1&funcao_js=parent.js_mostrarhcbo';
        }        
        var sCampos = '|rh70_estrutural|rh70_descr|rh70_sequencial';

        if (!empty($F('sd03_i_codigo'))) {
            sCampos = '|sd27_i_codigo|rh70_estrutural|rh70_descr|rh70_sequencial';
        }

        if (mostra == true) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_especmedico',
                sUrl + sCampos + '&chave_sd04_i_unidade=' + document.form1.sd24_i_unidade.value
                + '&chave_sd04_i_medico=' + document.form1.sd03_i_codigo.value,
                'Pesquisa Especialidade',
                mostra
            );
        } else {

            if (document.form1.rh70_estrutural.value != '') {

                sUrl = 'func_especialidade_recepcao.php?pesquisa_chave=' + document.form1.rh70_estrutural.value;
                sUrl += '&funcao_js=parent.js_mostrarhcbo';

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_especmedico',
                    sUrl + '&chave_sd04_i_unidade=' + document.form1.sd24_i_unidade.value
                    + '&chave_sd04_i_medico=' + document.form1.sd03_i_codigo.value,
                    'Pesquisa Especialidade',
                    mostra
                );
            } else {
                limpaCamposEspecialidade();
            }
        }
    }

    function js_mostrarhcbo() {

        switch (arguments.length) {

            case 2:

                limpaCamposEspecialidade();
                $('rh70_descr').value = arguments[1];

                break;

            case 3:

                $('rh70_estrutural').value = arguments[0];
                $('rh70_descr').value = arguments[1];
                $('rh70_sequencial').value = arguments[2];

                break;

            case 4:

                $('sd27_i_codigo').value = arguments[0];
                $('rh70_estrutural').value = arguments[1];
                $('rh70_descr').value = arguments[2];
                $('rh70_sequencial').value = arguments[3];

                break;

            case 5:

                if ($F('sd03_i_codigo') != '') {
                    $('sd27_i_codigo').value = arguments[1];
                }
                $('rh70_estrutural').value = arguments[2];
                $('rh70_descr').value = arguments[3];
                $('rh70_sequencial').value = arguments[4];

                break;
        }

        db_iframe_especmedico.hide();
    }

    function js_pesquisasd23_i_codigo(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_prontagendamento',
                'func_prontagendamento.php?funcao_js=parent.js_mostraagendamento1|dl_FAA|dl_Agenda|sd23_i_numcgs',
                'Pesquisa Agenda',
                true
            );
        } else {
            document.form1.descrdepto.value = '';
        }
    }

    function js_mostraagendamento1(faa, agenda, cgs) {
        db_iframe_prontagendamento.hide();
        if (faa != "") {
            location.href = '<?=basename(
                $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
            )?>?chavepesquisaprontuario=' + faa + '&triagem=' + '<?=@$triagem?>&desabilitaencaminhamento=1';
        } else if (agenda != "") {
            location.href = '<?=basename(
                $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
            )?>?chavepesquisaagenda=' + agenda + '&triagem=' + '<?=@$triagem?>&desabilitaencaminhamento=1';
        } else {
            location.href = '<?=basename(
                $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
            )?>?chavepesquisacgs=' + cgs + '&triagem=' + '<?=@$triagem?>&desabilitaencaminhamento=1';
        }
    }

    function js_ruas() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_ruas',
            'func_ruas.php?rural=1&funcao_js=parent.js_preenchepesquisaruas|j14_codigo|j14_nome',
            'Pesquisa Endereço',
            true
        );
    }

    function js_preenchepesquisaruas(chave, chave1) {

        document.form1.z01_v_ender.value = chave1;
        db_iframe_ruas.hide();
    }

    function js_bairro() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_bairro',
            'func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro|j13_codi|j13_descr',
            'Pesquisa Bairro',
            true
        );
    }

    function js_preenchebairro(chave, chave1) {

        document.form1.j13_codi.value = chave;
        document.form1.z01_v_bairro.value = chave1;
        db_iframe_bairro.hide();
    }

    function js_ruas1() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_ruas1', 'func_ruas.php?rural=1&funcao_js=parent.js_preenchepesquisaruas1|j14_codigo|j14_nome',
            'Pesquisa',
            true
        );
    }

    function js_preenchepesquisaruas1(chave, chave1) {

        document.form1.z01_v_endcon.value = chave1;
        db_iframe_ruas1.hide();
    }

    function js_bairro1() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_bairro1',
            'func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1|j13_codi|j13_descr',
            'Pesquisa',
            true
        );
    }

    function js_preenchebairro1(chave, chave1) {

        document.form1.z01_v_baicon.value = chave1;
        db_iframe_bairro1.hide();
    }

    function js_pesquisasd24_i_unidade(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_unidades',
                'func_unidades.php?funcao_js=parent.js_mostraunidades1|sd02_i_codigo|descrdepto',
                'Pesquisa',
                true
            );
        } else {

            if (document.form1.sd24_i_unidade.value != '') {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_unidades',
                    'func_unidades.php?pesquisa_chave=' + document.form1.sd24_i_unidade.value
                    + '&funcao_js=parent.js_mostraunidades',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.descrdepto.value = '';
            }
        }
    }

    function js_mostraunidades(chave, erro) {

        document.form1.descrdepto.value = chave;

        if (erro == true) {

            document.form1.sd24_i_unidade.focus();
            document.form1.sd24_i_unidade.value = '';
        }
    }

    function js_mostraunidades1(chave1, chave2) {

        document.form1.sd24_i_unidade.value = chave1;
        document.form1.descrdepto.value = chave2;
        db_iframe_unidades.hide();
    }

    function js_pesquisaz01_i_cgsund(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_cgs_und',
                'func_cgs_und.php?funcao_js=parent.js_preenchecgs|z01_i_cgsund'
                + '&retornacgs=p.p.document.form1.z01_i_cgsund.value'
                + '&retornanome=p.p.js_preenchecgs(p.p.document.form1.z01_i_cgsund.value);p.p.document.form1.z01_v_nome.value',
                'Pesquisa CGS',
                true
            );
        } else {

            if (document.form1.z01_i_cgsund.value != '') {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_cgs_und',
                    'func_cgs_und.php?funcao_js=parent.js_preenchecgs|z01_i_cgsund'
                    + '&retornacgs=p.p.document.form1.z01_i_cgsund.value'
                    + '&retornanome=p.p.js_preenchecgs(p.p.document.form1.z01_i_cgsund.value);p.p.document.form1.z01_v_nome.value'
                    + '&chave_z01_i_cgsund=' + document.form1.z01_i_cgsund.value,
                    'Pesquisa CGS',
                    true
                );
            } else {
                document.form1.z01_i_numcgs.value = '';
            }
        }
    }

    function js_mostracgs(erro, chave) {

        document.form1.z01_v_nome.value = 'teste ->' + chave;

        if (erro == true) {

            document.form1.z01_i_cgsund.focus();
            document.form1.z01_v_nome.value = '';
        }
    }

    function js_mostracgs1(chave1, chave2) {

        document.form1.z01_i_cgsund.value = chave1;
        document.form1.z01_i_numcgs.value = chave2;
        db_iframe_cgs.hide();
    }


    <?php if(isset($triagem) && $triagem == "false") { ?>
    function js_pesquisaprontuarios() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_prontuarios002',
            'func_prontuarios002.php?funcao_js=parent.js_preenchepesquisa|sd24_i_codigo',
            'Pesquisa de FAA',
            true
        );
    }

    <?php } else { ?>
    function js_pesquisaprontuarios() {

        js_OpenJanelaIframe(
            '',
            'db_iframe_prontuarios',
            'func_prontuarios_novo.php?funcao_js=parent.js_preenchepesquisa|sd24_i_codigo',
            'Pesquisa de FAA',
            true
        );
    }
    <?php }?>

    function js_preenchecgs(chave) {

        db_iframe_cgs_und.hide();
        location.href = '<?=basename(
            $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
        )?>?chavepesquisacgs=' + chave + '&triagem=' + '<?=@$triagem?>';
    }

    <?php if( isset($triagem) && $triagem == "false" ) {?>

    function js_preenchepesquisa(chave) {
        db_iframe_prontuarios002.hide();
        location.href = '<?=basename(
            $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
        )?>?chavepesquisaprontuario=' + chave + '&triagem=' + '<?=@$triagem?>';
    }
    <?php } else {?>

    function js_preenchepesquisa(chave) {

        db_iframe_prontuarios.hide();
        location.href = '<?=basename(
            $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
        )?>?chavepesquisaprontuario=' + chave + '&triagem=' + '<?=@$triagem?>';
    }
    <?php }?>

    function js_limpa() {

        <?php
        echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "'";
        ?>
    }

    function js_cepcon(abre) {

        if (abre == true) {

            js_OpenJanelaIframe(
                "",
                'db_iframe_cep',
                'func_cep.php?funcao_js=parent.js_preenchecepcon|cep|cp06_logradouro|cp05_localidades'
                + '|cp05_sigla|cp01_bairro',
                'Pesquisa',
                true
            );
        } else {

            js_OpenJanelaIframe(
                "",
                'db_iframe_cep',
                'func_cep.php?pesquisa_chave=' + document.form1.z01_v_cep.value
                + '&funcao_js=parent.js_preenchecepcon|cep|cp06_logradouro|cp05_localidades'
                + '|cp05_sigla|cp01_bairro',
                'Pesquisa',
                false
            );
        }
    }

    function js_preenchecepcon(chave, chave1, chave2, chave3, chave4) {

        document.form1.z01_v_cep.value = chave;
        document.form1.z01_v_ender.value = chave1;
        document.form1.z01_v_munic.value = chave2;
        document.form1.z01_v_uf.value = chave3;
        document.form1.z01_v_bairro.value = chave4;
        db_iframe_cep.hide();
    }

    function js_anular() {

        if (document.form1.sd24_i_codigo.value == "") {
            alert("FAA não informada!");
        } else {

            iTop = (screen.availHeight - 600) / 2;
            iLeft = (screen.availWidth - 600) / 2;

            if (document.form1.anular.value == "Anular FAA") {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_prontanulado',
                    'sau1_prontanulado001.php?chavepesquisaprontuario=' + document.form1.sd24_i_codigo.value,
                    'Anular FAA',
                    true,
                    iTop,
                    iLeft,
                    600,
                    210
                );
            } else {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_prontanulado',
                    'sau1_prontanulado003.php?chavepesquisa=' + document.form1.sd24_i_codigo.value,
                    'Anular FAA',
                    true,
                    iTop,
                    iLeft,
                    600,
                    210
                );
            }
        }
    }

    function js_municipio() {

        if (document.form1.z01_i_cgsund.value != "") {
            location.href = '<?=basename(
                    $GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]
                )?>?chavepesquisacgs=' + document.form1.z01_i_cgsund.value
                + '&chavepesquisaprontuario=' + document.form1.sd24_i_codigo.value
                + '&chavepesquiamunicipio=' + document.form1.z01_registromunicipio.value;
        } else {

            query = '?chavepesquiamunicipio=' + document.form1.z01_registromunicipio.value;
            query += '&z01_v_nome=' + document.form1.z01_v_nome.value;
            query += '&sd34_i_codigo=' + document.form1.z01_v_micro.value;
            query += '&z01_v_cgccpf=' + document.form1.z01_v_cgccpf.value;
            location.href = '<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>' + query;
        }
    }

    $('z01_d_nasc').size = 12;
    $('z01_d_cadast').size = 12;

    $('z01_registromunicipio').className = 'field-size-max';
    $('z01_v_sexo').className = 'field-size-max';
    $('sd24_i_tipo').className = 'field-size-max';
    $('sd24_i_motivo').className = 'field-size-max';
    $('sd24_i_acaoprog').className = 'field-size-max';
    $('sd24_unidadeencaminhadora').className = 'field-size-max';
    $('sd24_setorambulatorial').className = 'field-size-max';

    /**
     * [js_finalizarAtendimento description]
     * @return {[type]} [description]
     */
    function js_finalizarAtendimento() {

        if ($F('sd24_i_codigo') != '') {

            var fCallbackSalvar = function () {
                location.href = 'sau4_recepcao001.php';
            };

            var oRetorno = new DBViewMotivosAlta();
            oRetorno.setCallbackSalvar(fCallbackSalvar);
            oRetorno.setProntuario($F('sd24_i_codigo'));
            oRetorno.show();
        } else {
            alert('Informe o procedimento');
        }
    }

    /**
     * [js_encaminharProcedimento description]
     * @return {[type]} [description]
     */
    function js_encaminharProcedimento() {

        if ($F('sd24_i_codigo') != '') {

            var fCallbackSalvar = function () {
                location.href = 'sau4_recepcao001.php';
            };

            var oRetorno = new DBViewEncaminhamento(DBViewEncaminhamento.RECEPCAO, $F('sd24_i_codigo'));
            oRetorno.setCallbackSalvar(fCallbackSalvar);
            oRetorno.show();
        } else {
            alert('Informe o procedimento');
        }
    }

    function limpaCamposEspecialidade() {

        $('sd27_i_codigo').value = '';
        $('rh70_sequencial').value = '';
        $('rh70_estrutural').value = '';
        $('rh70_descr').value = '';
    }

    pesquisaIdadeCompletaDoPaciente(document.getElementById('z01_i_cgsund').value,'linhaDaIdadeCompleta');

    function mascara(objeto, isCelular = false) {
        if (event.charCode >= 48 && event.charCode <= 57) {
            if (objeto.value.length == 0 || objeto.value.length == 1)
                objeto.value = '(' + objeto.value;

            if (objeto.value.length == 3)
                objeto.value = objeto.value + ')';

            if (objeto.value.length == 4)
                objeto.value = objeto.value + ' ';

            if (objeto.value.length == 10 && isCelular) {
                objeto.value = objeto.value + '-';
            } else if (objeto.value.length == 9 && isCelular == false) {
                objeto.value = objeto.value + '-';
            }
        } else {
            event.preventDefault();
            return false;
        }
    }

    function mascaraPaste(objeto, isCelular = false) {
        var telefone = event.clipboardData.getData('Text');
        telefone = telefone.replace('-', '');
        telefone = telefone.replace(' ', '');
        telefone = telefone.replace('(', '');
        telefone = telefone.replace(')', '');
        if (isCelular) {
            telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 7) + '-' + telefone.slice(7, 11);
        } else {
            telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 6) + '-' + telefone.slice(6, 10);
        }
        objeto.value = telefone;
    }

    function recuperaCelularFormatado() {
        let celular = document.getElementById('z01_v_telcel');

        if (celular.value !== '' && celular.value[0] !== '(') {
            celular.value = '(' + celular.value.slice(0, 2) + ') ' + celular.value.slice(2, 7) + '-' + celular.value.slice(7, 11);
        }
    }

</script>
