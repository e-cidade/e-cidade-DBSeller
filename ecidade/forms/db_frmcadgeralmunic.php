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
 *  junto com este programa; se nao, escreva para a Free Software
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */
?>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css" />
    <link rel="stylesheet" type="text/css" href="estilos/grid.style.css" />
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbtextField.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbcomboBox.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/dbViewCadEndereco.classe.js"></script>
</head>

<style type="text/css">
    .fieldsetinterno {
        border: 0px;
        border-top: 2px groove white;
        margin-top: 10px;
    }

    td {
        white-space: nowrap
    }

    fieldset table td:first-child {
        width: 160px;
        white-space: nowrap
    }

    form label.required:after {
        color: red;
        content: " *";
    }

    .highlight-input {
        border-color: red;
    }
</style>
<?php

if (isset($_POST["cpf"])) {
    $cpfInformado = $_POST["cpf"];
}
if (isset($_POST["cnpj"])) {
    $cnpjInformado = $_POST["cnpj"];
}

$disable = 'disabled';
if ((isset($_GET['sequencialCgmAlteracao']) && $_GET['sequencialCgmAlteracao']) ||
    (isset($_GET['cgmOriginal']) && $_GET['cgmOriginal'])
) {
    $db_opcao = 11;
}

$btnDisabled = "";
if ($db_opcao == 33 || $db_opcao == 3 || $db_opcao == 11) {
    $btnDisabled = "disabled";
}

$clrotulo = new rotulocampo();
$clrotulo->label('z03_tipoempresa');
$clrotulo->label('rh70_sequencial');
$clrotulo->label('rh70_descr');
$clrotulo->label('z01_nomecomple');
$clrotulo->label('z04_nomesocial');
$clrotulo->label('z19_pais');
$clrotulo->label('z19_cidade');
$clrotulo->label('z19_logradouro');
$clrotulo->label('z19_bairro');
$clrotulo->label('z19_numero');
$clrotulo->label('z19_codigopostal');
$clrotulo->label('z19_complemento');

$ov02_sequencial = "";
$ov02_seq = "";
if (isset($oPost->ov02_sequencial) && trim($oPost->ov02_sequencial) != "") {
    $ov02_sequencial = $oPost->ov02_sequencial;
}
if (isset($oPost->ov02_seq) && trim($oPost->ov02_seq) != "") {
    $ov02_seq = $oPost->ov02_seq;
}
$funcaoRetorno = "";
if (isset($oGet->funcaoRetorno) && trim($oGet->funcaoRetorno) != "") {
    $funcaoRetorno = $oGet->funcaoRetorno;
}

/**
 * Record Set utilizado para montar o select
 */
$sCampos         = "db98_sequencial as z03_tipoempresa, db98_descricao";
$sSqlTipoEmpresa = $cltipoempresa->sql_query_file(null, $sCampos, "db98_sequencial", "");
$rsTipoEmpresa   = $cltipoempresa->sql_record($sSqlTipoEmpresa);
?>
<form action="" method="post" name="form1" id="form1">
    <input type="hidden" name="ov02_sequencial" id="ov02_sequencial" value="<?= $ov02_sequencial ?>" />
    <input type="hidden" name="ov02_seq" id="ov02_seq" value="<?= $ov02_seq ?>" />
    <input type="hidden" id="flag-inclusao-processo" value="<?php echo $oGet->inclusao_processo ?>" />
    <input type="hidden" id="janela-pri-id-municipio" value="" />
    <input type="hidden" id="janela-pri-descricao-municipio" value="" />
    <table>
        <tr>
            <td rowspan="5" valign="top">
                <img src="imagens/none1.jpeg" width="95" height="120" id='fotocgm' style="border: 1px inset white" />
            </td>
            <td>
                <fieldset>
                    <legend> <strong>Dados Gerais</strong> </legend>
                    <table>
                        <tr>
                            <td title='<?= $Tz01_numcgm ?>' nowrap>
                                <label id="z01_numcgm-label" for="z01_numcgm"><?= $Lz01_numcgm ?></label>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('z01_numcgm', 10, $Iz01_numcgm, true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label id="z01_cadast-label" for="z01_cadast"><?= @$Lz01_cadast ?></label>
                            </td>
                            <td align="left">
                                <?php

                                if ($db_opcao == 1) {
                                    $z01_cadast = date('d/m/Y', db_getsession("DB_datausu"));
                                } else if ($db_opcao == 2 || $db_opcao == 22 && isset($oCgm)) {
                                    $z01_cadast     = implode("/", array_reverse(explode("-", $oCgm->z01_cadast)));
                                }

                                db_input('z01_cadast', 10, @$Iz01_cadast, true, 'text', 3, "", '', '', '', 11);
                                ?>
                            </td>
                            <?php if (!empty($oCgm->z01_numcgm)) { ?>
                                <td align="right">
                                    <label for="z01_ultalt"><?= @$Lz01_ultalt ?></label>
                                </td>
                                <td align="left">
                                    <?php

                                    $timestamp = strtotime($oCgm->z01_ultalt);

                                    $z01_ultalt_ano = date('Y', $timestamp);
                                    $z01_ultalt_mes = date('m', $timestamp);
                                    $z01_ultalt_dia = date('d', $timestamp);

                                    db_inputdata('z01_ultalt', @$z01_ultalt_dia, @$z01_ultalt_mes, @$z01_ultalt_ano, true, 'text', 3);
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>

                        <!-- Pessoa Fisica z01_cgccpf = 11 -->
                        <?php

                        if ($lPessoaFisica) {

                        ?>
                            <tr>
                                <td title="CPF">
                                    <label for="z01_cpf" class="required"><strong>CPF:</strong></label>
                                </td>
                                <td align="left">
                                    <?php
                                    if (isset($oPost->cpf) && strlen($oPost->cpf) == 11) {
                                        $z01_cpf = $oPost->cpf;
                                    }

                                    db_input('z01_cpf', 15, @$Iz01_cpf, true, 'text', $db_opcao, "onBlur='js_verificaCGCCPF(this);'", '', '', 'text-align:left;', 11);

                                    ?>
                                </td>
                                <td align="right" title="<?= @$Tz01_ident ?>">
                                    <label id="z01_ident-label" for="z01_ident"><?= @$Lz01_ident ?></label>
                                </td>
                                <td align="left">
                                    <?php
                                    db_input('z01_ident', 15, $Iz01_ident, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label id="z01_identorgao-label" for="z01_identorgao"><?= @$Lz01_identorgao ?></label>
                                </td>
                                <td align="left">
                                    <?php
                                    if (isset($oPost->cpf) && strlen($oPost->cpf) == 11) {
                                        $z01_cpf = $oPost->cpf;
                                    }

                                    db_input('z01_identorgao', 15, @$Iz01_identorgao, true, 'text', $db_opcao);
                                    ?>
                                </td>
                                <td align="right">
                                    <label id="z01_identdtexp-label" for="z01_identdtexp"><?= @$Lz01_identdtexp ?></label>
                                </td>
                                <td align="left">
                                    <?php
                                    db_inputdata('z01_identdtexp', @$z01_identdtexp_dia, @$z01_identdtexp_mes, @$z01_identdtexp_ano, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr title="<?= @$Lz01_incest ?>">
                                <td nowrap="nowrap">
                                    <label id="z01_incest-label" for="z01_incest"><?= @$Lz01_incest ?></label>
                                </td>
                                <td>
                                    <?php
                                    db_input('z01_incest', 15, @$Iz01_incest, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title=<?= @$Tz01_nome ?>>
                                    <label for="z01_nome" class="required"><?= @$Lz01_nome ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz01_nome ?>" colspan="3">
                                    <?php
                                    db_input('z01_nome', 50, $Iz01_nome, true, 'text', $db_opcao, '', '', '', 'width: 100%;');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title=<?= @$Tz01_nomecomple ?>>
                                    <label id="z01_nomecomple-label" for="z01_nomecomple-label"><?= @$Lz01_nomecomple ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz01_nomecomple ?>" colspan="3">
                                    <?php
                                    db_input('z01_nomecomple', 50, $Iz01_nomecomple, true, 'text', $db_opcao, '', '', '', 'width: 100%;');
                                    ?>
                                </td>
                            </tr>
                            <tr>

                                <td nowrap title=<?= @$Tz04_nomesocial ?>>
                                    <label for="z04_nomesocial"><?= @$Lz04_nomesocial ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz04_nomesocial ?>" colspan="3">
                                    <?php
                                    db_input('z04_nomesocial', 50, $Iz04_nomesocial, true, 'text', $db_opcao, '', '', '', 'width: 100%;');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title='<?= @$Tz03_tipoempresa ?>'>
                                    <label id="z03_tipoempresa-label" for="z03_tipoempresa"><strong>Tipo Empresa:</strong></label>
                                </td>
                                <td colspan="3" nowrap="nowrap">
                                    <?php
                                    db_selectrecord('z03_tipoempresa', $rsTipoEmpresa, true, $db_opcao, '', '', '', '', '', 2);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title=<?= @$Tz01_pai ?>>
                                    <label id="z01_pai-label" for="z01_pai"><?= @$Lz01_pai ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz01_pai ?>" colspan="3">
                                    <?php
                                    db_input('z01_pai', 50, $Iz01_pai, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title=<?= @$Tz01_mae ?>>
                                    <label id="z01_mae-label" for="z01_mae"><?= @$Lz01_mae ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz01_mae ?>" colspan="3">
                                    <?php
                                    db_input('z01_mae', 50, $Iz01_mae, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title=<?= @$Tz01_naturalidade ?>>
                                    <label id="z01_naturalidade-label" for="z01_naturalidade"><?= @$Lz01_naturalidade ?></label>
                                </td>
                                <td nowrap title="<?= @$Tz01_naturalidade ?>" colspan="3">
                                    <?php
                                    db_input('z01_naturalidade', 50, $Iz01_naturalidade, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= $Tz01_nasc ?>">
                                    <label id="z01_nasc-label" for="z01_nasc"><?= $Lz01_nasc ?></label>
                                </td>
                                <td nowrap title="<?= $Tz01_nasc ?>">
                                    <?php
                                    db_inputdata('z01_nasc', @$z01_nasc_dia, @$z01_nasc_mes, @$z01_nasc_ano, true, 'text', $db_opcao);
                                    ?>
                                </td>
                                <td nowrap title="<?= $Tz01_dtfalecimento ?>" align="right">
                                    <?= $Lz01_dtfalecimento ?>
                                </td>
                                <td nowrap title="<?= $Tz01_dtfalecimento ?>" align="right">
                                    <?php
                                    db_inputdata('z01_dtfalecimento', @$z01_dtfalecimento_dia, @$z01_dtfalecimento_mes, @$z01_dtfalecimento_ano, true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= $Tz04_paisnascimento ?>">
                                    <?php db_ancora("País Nascimento", "abreJanelaPaises('js_mostraPaisNascimento')", $db_opcao, "", "municipioibge"); ?>
                                </td>
                                <td colspan="4">
                                    <?php
                                    db_input("z04_paisnascimento",  10, "", true, "hidden", 3, "");
                                    db_input("cod_paisnasc",  10, "",  true, "text", 3, "");
                                    db_input("descr_paisnasc",  50, "",  true, "text", 3, "");
                                    ?>
                                </td>
                                <td>
                                    <input type="button" id="btnLimpaPaisNasc" value="Limpar" />

                                </td>
                            </tr>

                            <tr>
                                <td nowrap title="<?= $Tz04_paisnacionalidade ?>">
                                    <?php db_ancora("País Nacionalidade", "abreJanelaPaises('js_mostraPaisNacionalidade')", $db_opcao, "", ""); ?>
                                </td>
                                <td colspan="4">
                                    <?php
                                    db_input("z04_paisnacionalidade",  10, "", true, "hidden", 3, "");
                                    db_input("cod_paisnac",  10, "",  true, "text", 3, "");
                                    db_input("descr_paisnac",  50, "",  true, "text", 3, "");
                                    ?>
                                </td>
                                <td>
                                    <input type="button" id="btnLimpaPaisNac" value="Limpar" />
                                </td>
                            </tr>

                            <tr>
                                <td nowrap title="<?= $Tz01_estciv ?>">
                                    <label id="z01_estciv-label" for="z01_estciv"><?= $Lz01_estciv ?></label>
                                </td>
                                <td nowrap title="<?= $Tz01_estciv ?>">
                                    <?php
                                    $x = array(
                                        "1" => "Solteiro",
                                        "2" => "Casado",
                                        "3" => "Viúvo",
                                        "4" => "Divorciado",
                                        "5" => "Separado Consensual",
                                        "6" => "Separado Judicial",
                                        "7" => "União Estavel"
                                    );

                                    db_select('z01_estciv', $x, true, $db_opcao, 'style="width:125px;"');
                                    ?>
                                </td>
                                <td nowrap title="<?= $Tz01_sexo ?>" align="right">
                                    <?= $Lz01_sexo ?>
                                </td>
                                <td nowrap title="<?= $Tz01_sexo ?>" align="right">
                                    <?php
                                    $sex = array("M" => "Masculino", "F" => "Feminino");
                                    db_select('z01_sexo', $sex, true, $db_opcao, 'style="width:125px;"');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= $Tz01_nacion ?>">
                                    <?= $Lz01_nacion ?>
                                </td>
                                <td nowrap title="<?= $Tz01_nacion ?>">
                                    <?php
                                    $x = array("1" => "Brasileira", "2" => "Estrangeira");
                                    db_select('z01_nacion', $x, true, $db_opcao, 'style="width:125px;"');
                                    ?>
                                </td>
                                <td nowrap title="CGM do Município" align="right">
                                    <strong>CGM do Município:</strong>
                                </td>
                                <td nowrap title="Cgm do Município" align="right">
                                    <?php
                                    $x = array("t" => "Sim", "f" => "Não");
                                    db_select('municipio', $x, true, $db_opcao, 'onChange="js_alteraMunicipio();" " style="width:125;"');
                                    ?>
                                </td>
                            </tr>
                    </table>

                    <fieldset id="divEstrangeiro" style="display:none;">
                        <legend class="bold">Dados para Nacionalidade Estrangeira</legend>
                        <table>
                            <tr id="trDocumentoEstrangeiro">
                                <td><label for="z09_documento"><b>Documento:</b></label></td>
                                <td colspan="3">
                                    <?php
                                    db_input('z09_documento', 30, null, true, 'text', 1);
                                    ?>
                                </td>
                            </tr>
                            <tr id="trCidadeEstrangeiro">
                                <td><label for="z09_cidade"><b>Cidade:</b></label></td>
                                <td colspan="3">
                                    <?php
                                    db_input('z09_cidade', 30, null, true, 'text', 1);
                                    ?>
                                </td>
                            </tr>
                            <tr id="trPaisEstrangeiro">
                                <td><label for="z09_pais"><b>País:</b></label></td>
                                <td colspan="3">
                                    <?php
                                    db_input('z09_pais', 30, null, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>

                    <table>
                        <tr>
                            <td nowrap title=<?= @$Tz01_escolaridade ?>>
                                <label id="z01_escolaridade-label" for="z01_escolaridade"><?= @$Lz01_escolaridade ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_escolaridade ?>" colspan="3">
                                <?php
                                $aEscolaridade = array(
                                    '0' => 'SEM DEFINIÇÃO',
                                    '1' => 'ANALFABETO',
                                    '2' => 'FUNDAMENTAL INCOMPLETO',
                                    '3' => 'FUNDAMENTAL COMPLETO',
                                    '4' => 'ENSINO MÉDIO INCOMPLETO',
                                    '5' => 'ENSINO MÉDIO COMPLETO',
                                    '6' => 'ENSINO SUPERIOR INCOMPLETO',
                                    '7' => 'ENSINO SUPERIOR COMPLETO',
                                    '8' => 'MESTRADO',
                                    '9' => 'DOUTORADO'
                                );
                                db_select('z01_escolaridade', $aEscolaridade, true, 1, 'style="width:100%;"');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_telef ?>">
                                <label id="z01_telef-label" for="z01_telef"><?= @$Lz01_telef ?>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('z01_telef', 15, $Iz01_telef, true, 'text', $db_opcao);
                                ?>
                            </td>
                            <td nowrap title="<?= @$Tz01_telcel ?>" align="right">
                                <label id="z01_telcel-label" for="z01_telcel"><?= @$Lz01_telcel ?></label>
                            </td>
                            <td nowrap align="right">
                                <?php
                                db_input('z01_telcel', 15, $Iz01_telcel, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr title="<?= @$Tz01_fax ?>">
                            <td nowrap>
                                <label id="z01_fax-label" for="z01_fax"><?= @$Lz01_fax ?></label>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('z01_fax', 15, $Iz01_fax, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_email ?>">
                                <label id="z01_email-label" for="z01_email"><?= @$Lz01_email ?></label>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('z01_email', 50, $Iz01_email, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset class="rfieldsetinterno">
                                    <legend> <strong>Endereço Primário</strong></legend>
                                    <div align="center">
                                        <?php
                                        db_input('idEnderPrimario', 10, '', true, 'hidden', $db_opcao);
                                        db_input('endPrimario', 52, '', true, 'text', 3);
                                        ?>
                                        <input type="button" value="Lançar" id="btnLancarEndPrimario" onclick="js_lancaEnderPrimario();"
                                            <?= $btnDisabled ?> />
                                        <input type="button" value="Excluir" id="btnExcluirEndPrimario" onclick="js_ExcluiEnderPrimario();"
                                            <?= $btnDisabled ?> />
                                    </div>
                                    <div title='<?= @$Tz01_cxpostal ?>'>
                                        <div style='font-weight: bold; width: 150px; float: left; margin-top: 5px;'>
                                            <label id="z01_cxpostal-label" for="z01_cxpostal"><?= @$Lz01_cxpostal ?></label>
                                        </div>
                                        <div style=' margin-top: 5px;'><?php db_input("z01_cxpostal", 10, null, true, 'text', $db_opcao); ?></div>
                                        <div style='clear: left; display: none;'></div>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset class="rfieldsetinterno">
                                    <legend> <strong>Endereço Exterior</strong></legend>
                                    <table>
                                        <tr>
                                            <td nowrap title="<?= $Tz19_pais ?>">
                                                <?php db_ancora("País", "abreJanelaPaises('js_mostraPaisEndereco', 'N')", $db_opcao, "", "municipioibge"); ?>
                                            </td>
                                            <td>
                                                <?php
                                                db_input("z19_pais",  10, "", true, "hidden", 3, "");
                                                db_input("cod_paisendereco",  10, "", true, "text", 3, "");
                                                db_input("descr_paisendereco",  50, "",  true, "text", 3, "");

                                                ?>
                                                <input type="button" id="btnLimpaPaisExterior" value="Limpar" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_logradouro; ?>">
                                                <label class="bold" for="z19_logradouro" id="lbl_z19_logradouro"><?php echo $Sz19_logradouro; ?>:</label>
                                            </td>
                                            <td>
                                                <?php db_input('z19_logradouro', 100, $Iz19_logradouro, true, 'text', $db_opcao, ""); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_numero; ?>">
                                                <label class="bold" for="z19_numero" id="lbl_z19_numero"><?php echo $Sz19_numero; ?>:</label>
                                            </td>
                                            <td>
                                                <?php
                                                db_input('z19_numero', 10, $Iz19_numero, true, 'text', $db_opcao, "");
                                                ?>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_complemento; ?>">
                                                <label class="bold" for="z19_complemento" id="lbl_z19_complemento"><?php echo $Sz19_complemento; ?>:</label>
                                            </td>
                                            <td>
                                                <?php db_input('z19_complemento', 30, $Iz19_complemento, true, 'text', $db_opcao, ""); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_bairro; ?>">
                                                <label class="bold" for="z19_bairro" id="lbl_z19_bairro"><?php echo $Sz19_bairro; ?>:</label>
                                            </td>
                                            <td>
                                                <?php db_input('z19_bairro', 90, $Iz19_bairro, true, 'text', $db_opcao, ""); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_cidade; ?>">
                                                <label class="bold" for="z19_cidade" id="lbl_z19_cidade"><?php echo $Sz19_cidade; ?>:</label>
                                            </td>
                                            <td>
                                                <?php db_input('z19_cidade', 50, $Iz19_cidade, true, 'text', $db_opcao, ""); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td nowrap title="<?php echo $Tz19_codigopostal; ?>">
                                                <label class="bold" for="z19_codigopostal" id="lbl_z19_codigopostal"><?php echo $Sz19_codigopostal; ?>:</label>
                                            </td>
                                            <td>
                                                <?php db_input('z19_codigopostal', 12, $Iz19_codigopostal, true, 'text', $db_opcao, ""); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset class="fieldsetinterno">
                                    <legend> <strong>Dados do Emprego:</strong></legend>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_profis ?>>
                                <label id="z01_profis-label" for="z01_profis"><?= @$Lz01_profis ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_profis ?>" colspan="3">
                                <?php
                                db_input('z01_profis', 50, $Iz01_profis, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>

                        <tr title="<?= @$Trh70_descr ?>">
                            <td align="left" nowrap>
                                <label id="z04_rhcbo-label" for="rh70_sequencial">
                                    <strong>
                                        <?php
                                        db_ancora("CBO", "js_pesquisaCbo(true);", $db_opcao);
                                        ?>
                                    </strong>
                                </label>
                            </td>
                            <td colspan="4" align="left">
                                <?php
                                db_input("rh70_sequencial",  10, "", true, "text", $db_opcao, "onchange='js_pesquisaCbo(false);'");
                                db_input("rh70_descr",  50, "",  true, "text", 3, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td nowrap title=<?= @$Tz01_pis ?>>
                                <label id="z01_pis-label" for="z01_pis"><?= @$Lz01_pis ?></label>
                            </td>
                            <td colspan="4" nowrap title="<?= @$Tz01_pis ?>">
                                <?php
                                db_input('z01_pis', 15, $Iz01_pis, true, 'text', $db_opcao, "onblur = js_validaPis(this.value);");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= $Tz01_trabalha ?>">
                                <label id="z01_trabalha-label" for="z01_trabalha"><?= $Lz01_trabalha ?></label>
                            </td>
                            <td nowrap title="<?= $Tz01_trabalha ?>">
                                <?php
                                $x = array("t" => "Sim", "f" => "Não");
                                db_select('z01_trabalha', $x, true, $db_opcao, 'style="width:80%;text-align:left;"');
                                ?>
                            </td>
                            <td nowrap title="<?= $Lz01_renda ?>" align="right">
                                <label id="z01_renda-label" for="z01_renda"><?= $Lz01_renda ?></label>
                            </td>
                            <td nowrap title="Cgm do Município" align="right">
                                <?php
                                db_input('z01_renda', 15, $Iz01_renda, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_localtrabalho ?>>
                                <label id="z01_localtrabalho-label" for="z01_localtrabalho"><?= @$Lz01_localtrabalho ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_localtrabalho ?>" colspan="3">
                                <?php
                                db_input('z01_localtrabalho', 50, $Iz01_localtrabalho, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_telcon ?>">
                                <label id="z01_telcon-label" for="z01_telcon"><?= @$Lz01_telcon ?></label>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('z01_telcon', 15, $Iz01_telcon, true, 'text', $db_opcao);
                                ?>
                            </td>
                            <td nowrap title="<?= @$Tz01_celcon ?>" align="right">
                                <label id="z01_celcon-label" for="z01_celcon"><?= @$Lz01_celcon ?></label>
                            </td>
                            <td nowrap align="right">
                                <?php
                                db_input('z01_celcon', 15, $Iz01_celcon, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_emailc ?>">
                                <label id="z01_emailc-label" for="z01_emailc"><?= @$Lz01_emailc ?></label>
                            </td>
                            <td nowrap colspan="3">
                                <?php
                                db_input('z01_emailc', 50, $Iz01_emailc, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset class="rfieldsetinterno">
                                    <legend><strong>Endereço Secundário</strong></legend>
                                    <div align="center">
                                        <?php
                                        db_input('idEnderSecundario', 10, '', true, 'hidden', $db_opcao);
                                        db_input('endSecundario', 52, '', true, 'text', 3);
                                        ?>
                                        <input type="button" value="Lançar" id="btnLancarEndSecundario" onclick="js_lancaEnderSecundario();"
                                            <?= $btnDisabled ?> />
                                        <input type="button" value="Excluir" id="btnExcluirEndSecundario" onclick="js_ExcluiEnderSecundario();"
                                            <?= $btnDisabled ?> />
                                    </div>
                                    <div title="<?= $Tz01_cxposcon ?>">
                                        <div style='font-weight: bold; width: 150px; float: left; margin-top: 5px;'>Caixa Postal: </div>
                                        <div style=' margin-top: 5px;'><?php db_input("z01_cxposcon", 10, null, true, 'text', $db_opcao); ?></div>
                                        <div style='clear: left; display: none;'></div>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset>
                                    <legend><label id="z01_obs-label" for="z01_obs"><strong>Observações</strong></label></legend>
                                    <?php db_textarea('z01_obs', 5, 80, null, true, '', $db_opcao); ?>
                                </fieldset>
                            </td>
                        </tr>

                    <?php

                        } else {

                    ?>

                        <!-- ******************************** Fim de pessoa Fisica ***************************************************** -->
                        <!-- Inicio pessoa Jurídica -->

                        <tr>
                            <td nowrap title="<?= @$Tz01_cgc ?>"><strong>CNPJ:</strong></td>
                            <td>
                                <?php

                                if (isset($oPost->cnpj) && strlen($oPost->cnpj) == 14) {
                                    $z01_cgc = $oPost->cnpj;
                                }

                                db_input('z01_cgc', 15, @$Iz01_cgc, true, 'text', $db_opcao, "onBlur='js_verificaCGCCPF(this);js_testanome(\"\",\"\",this.value)'", '', '', 'text-align:left;');
                                ?>
                            </td>
                            <td nowrap title="CGM do Município" align="right">
                                <strong>CGM do Município:</strong>
                            </td>
                            <td nowrap title="<?= $Tz01_dtfalecimento ?>" align="right">
                                <?php
                                $x = array("t" => "Sim", "f" => "Não");
                                db_select('municipio', $x, true, $db_opcao, 'onChange="js_alteraMunicipio();"  " style="width:95%;text-align:left;"');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_nome ?>>
                                <label for="z01_nome" class="required"><?= @$Lz01_nome ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_nome ?>" colspan="3">
                                <?php
                                db_input('z01_nome', 50, $Iz01_nome, true, 'text', $db_opcao, 'onBlur="js_ToUperCampos(\'z01_nome\');js_copiaNome();" onkeyup="";');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_nomecomple ?>>
                                <label id="z01_nomecomple-label"><?= @$Lz01_nomecomple ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_nomecomple ?>" colspan=3>
                                <?php
                                db_input('z01_nomecomple', 50, $Iz01_nomecomple, true, 'text', $db_opcao, "onkeyup=''; onblur='js_ToUperCampos(\"z01_nomecomple\");'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_nomefanta ?>>
                                <label id="z01_nomefanta-label" for="z01_nomefanta"><?= @$Lz01_nomefanta ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_nomefanta ?>" colspan="3">
                                <?php
                                db_input('z01_nomefanta', 50, $Iz01_nomefanta, true, 'text', $db_opcao, "onkeyup=''; onblur='js_ToUperCampos(\"z01_nomefanta\");'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title=<?= @$Tz01_contato ?>>
                                <label id="z01_contato-label" for="z01_contato"><?= @$Lz01_contato ?></label>
                            </td>
                            <td nowrap title="<?= @$Tz01_contato ?>" colspan="3">
                                <?php
                                db_input('z01_contato', 50, $Iz01_contato, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title='<?= @$Tz03_tipoempresa ?>'>
                                <label id="z03_tipoempresa-label" for="z03_tipoempresa"><strong>Tipo Empresa:</strong>
                            </td>
                            <td colspan="3" nowrap="nowrap">
                                <?php
                                db_selectrecord('z03_tipoempresa', $rsTipoEmpresa, true, $db_opcao, '', '', '', '', '', 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_incest ?>">
                                <label id="z01_incest-label" for="z01_incest"><?= @$Lz01_incest ?></label>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('z01_incest', 15, $Iz01_incest, true, 'text', $db_opcao);
                                ?>
                            </td>
                            <td align="right">
                                <label id="z08_nire-label" for="z08_nire"><strong>Nire:</strong></label>
                            </td>
                            <td align="right">
                                <?php
                                db_input('z08_nire', 15, '', true, 'text', $db_opcao, '', '', "#E6E4F1");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_telef ?>">
                                <label id="z01_telef-label" for="z01_telef"><?= @$Lz01_telef ?></label>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('z01_telef', 15, $Iz01_telef, true, 'text', $db_opcao);
                                ?>
                            </td>
                            <td nowrap title="<?= @$Tz01_telcel ?>" align="right">
                                <label id="z01_telcel-label" for="z01_telcel"><?= @$Lz01_telcel ?></label>
                            </td>
                            <td nowrap align="right">
                                <?php
                                db_input('z01_telcel', 15, $Iz01_telcel, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr title="<?= @$Tz01_fax ?>">
                            <td nowrap>
                                <label id="z01_fax-label" for="z01_fax"><?= @$Lz01_fax ?></label>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('z01_fax', 15, $Iz01_fax, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_email ?>">
                                <label id="z01_email-label" for="z01_email"><?= @$Lz01_email ?></label>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('z01_email', 50, $Iz01_email, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset>
                                    <legend> <strong>Endereço Primário</strong> </legend>
                                    <div align="center">
                                        <?php
                                        db_input('idEnderPrimario', 10, '', true, 'hidden', $db_opcao);
                                        db_input('endPrimario', 52, '', true, 'text', 3);
                                        ?>
                                        <input type="button" value="Lançar" id="btnLancarEndPrimario" onclick="js_lancaEnderPrimario();" <?= $btnDisabled ?>>
                                        <input type="button" value="Excluir" id="btnExcluirEndPrimario" onclick="js_ExcluiEnderPrimario();" <?= $btnDisabled ?>>
                                    </div>
                                    <div title='<?= @$Tz01_cxpostal ?>'>
                                        <div style='font-weight: bold; width: 150px; float: left; margin-top: 5px;'>
                                            <label id="z01_cxpostal-label" for="z01_cxpostal"><?= @$Lz01_cxpostal ?></label>
                                        </div>
                                        <div style=' margin-top: 5px;'><?php db_input("z01_cxpostal", 10, null, true, 'text', $db_opcao); ?></div>
                                        <div style='clear: left; display: none;'></div>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_telcon ?>">
                                <label id="z01_telcon-label" for="z01_telcon"><?= @$Lz01_telcon ?></label>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('z01_telcon', 15, $Iz01_telcon, true, 'text', $db_opcao);
                                ?>
                            </td>
                            <td nowrap title="<?= @$Tz01_celcon ?>" align="right">
                                <label id="z01_celcon-label" for="z01_celcon"><?= @$Lz01_celcon ?></label>
                            </td>
                            <td nowrap align="right">
                                <?php
                                db_input('z01_celcon', 15, $Iz01_celcon, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Tz01_emailc ?>">
                                <label id="z01_emailc-label" for="z01_emailc"><?= @$Lz01_emailc ?></label>
                            </td>
                            <td nowrap colspan="3">
                                <?php
                                db_input('z01_emailc', 50, $Iz01_emailc, true, 'text', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= $Tz01_nacion ?>">
                                <?= $Lz01_nacion ?>
                            </td>
                            <td nowrap title="<?= $Tz01_nacion ?>">
                                <?php
                                $x = array("1" => "Brasileira", "2" => "Estrangeira");
                                db_select('z01_nacion', $x, true, $db_opcao, 'style="width:125px;"');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset id="divEmpresaEstrangeira" style="display:none;">
                                    <table>
                                        <tr id="trDocumentoEstrangeiro">
                                            <td><label for="z09_documento"><b>Documento:</b></label></td>
                                            <td colspan="3">
                                                <?php
                                                db_input('z09_documento', 30, null, true, 'text', 1);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr id="trCidadeEstrangeiro">
                                            <td><label for="z09_cidade"><b>Cidade:</b></label></td>
                                            <td colspan="3">
                                                <?php
                                                db_input('z09_cidade', 30, null, true, 'text', 1);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr id="trPaisEstrangeiro">
                                            <td><label for="z09_pais"><b>País:</b></label></td>
                                            <td colspan="3">
                                                <?php
                                                db_input('z09_pais', 30, null, true, 'text', 1);
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset>
                                    <legend> <strong>Endereço Secundário</strong> </legend>
                                    <div align="center">
                                        <?php
                                        db_input('idEnderSecundario', 10, '', true, 'hidden', $db_opcao);
                                        db_input('endSecundario', 52, '', true, 'text', 3);
                                        ?>
                                        <input type="button" value="Lançar"
                                            id="btnLancarEndSecundario" onclick="js_lancaEnderSecundario();" <?= $btnDisabled ?>>
                                        <input type="button" value="Excluir"
                                            id="btnExcluirEndSecundario" onclick="js_ExcluiEnderSecundario();" <?= $btnDisabled ?>>
                                    </div>
                                    <div title="<?= $Tz01_cxposcon ?>">
                                        <div style='font-weight: bold; width: 150px; float: left; margin-top: 5px;'>Caixa Postal: </div>
                                        <div style=' margin-top: 5px;'><?php db_input("z01_cxposcon", 10, null, true, 'text', $db_opcao); ?></div>
                                        <div style='clear: left; display: none;'></div>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset>
                                    <legend><strong>Observações</strong></legend>
                                    <?php db_textarea('z01_obs', 5, 80, "", true, '', $db_opcao, ""); ?>
                                </fieldset>
                            </td>
                        </tr>

                        <!-- Fim pessoa Jurídica -->
                    <?php
                        }
                    ?>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr align="center">
            <td>
                <?php if (empty($_GET['sequencialCgmAlteracao']) && empty($_GET['cgmOriginal'])) { ?>
                    <input name="btnSubmit" type="button" id="btnSubmit"
                           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
                        <?= ($db_botao == false ? "disabled" : "") ?>>

                    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
                <?php } ?>

                <?php
                $lPermissaoMenu = db_permissaomenu(db_getsession("DB_anousu"), 604, 7901);
                if ($db_opcao == 2 && $lPermissaoMenu == true) {
                ?>
                    <input name="btnVincular" type="button" id="btnVincular" value="Vincular Cidadao ao CGM"
                        onclick="js_vinculaCadastroCidadaoCGM();" style="display: none;" />
                    <input name="btnImportar" type="button" id="btnImportar" value="Importar dados do Cidadão" style="display: none;">
                <?php
                }
                ?>
                <?php
                if (!empty($_GET['sequencialCgmAlteracao'])) { ?>
                    <input type="button" id="original" value="Original" onclick="getCgmOriginal();">
                <?php
                } ?>

                <?php
                if (!empty($_GET['sequencialCgmAlteracao']) || !empty($_GET['cgmOriginal'])) { ?>
                    <input type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_cgmalt2.hide();">
                    <input type="button" id="alteracoes" value="Alterações" onclick="abreJanelaAlteracoes();">
                <?php
                } ?>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    var btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.addEventListener('click', () => {
        js_sendForm();
    })

    var lPessoaFisica = <?= json_encode($lPessoaFisica) ?>;

    const sequencialCgmAlteracao = '<?php echo $_GET['sequencialCgmAlteracao'] ?>';

    // Variáveis php são definidas em prot1_cadgeralmunic005.php, são adquiridas pelo POST do arquivo func_nome.php
    const cpfInformado = '<?php echo $cpfInformado ?>';
    const cnpjInformado = '<?php echo $cnpjInformado ?>';

    var camposObrigatorios = [];
    if (lPessoaFisica) {

        $('btnLimpaPaisExterior').addEventListener('click', () => {
            $('z19_pais').value = '';
            $('cod_paisendereco').value = '';
            $('descr_paisendereco').value = '';
        });

        $('btnLimpaPaisNasc').addEventListener('click', () => {
            $('z04_paisnascimento').value = '';
            $('cod_paisnasc').value = '';
            $('descr_paisnasc').value = '';
        });

    }


    $('z01_nome').addEventListener('blur', (event) => {
        $('z01_nomecomple').value = event.target.value;
    });

    window.onload = function() {
        isPessoaFisica = lPessoaFisica == '1' ? 'fisica' : 'juridica';

        let parametros = `tipo_pessoa=${isPessoaFisica}&exec=buscaCamposObrigatorios`;

        HttpClient.get(`pro1_protparametrocgm.RPC.php?${parametros}`).then(response => {
            if (response.erro) {
                alert(response.message);
            }

            var camposMensagem = '';

            response.camposObrigatorios.map((campo) => {
                camposObrigatorios.push({
                    id: campo.p73_html_id,
                    label: campo.p73_label
                });
                let elLabel = document.getElementById(campo.p73_html_id + '-label');
                let el = document.getElementById(campo.p73_html_id);

                if ($('flag-inclusao-processo').value) {
                    if (el && !el.value) {
                        el.classList.add('highlight-input');

                        el.addEventListener('change', function() {
                            if (this.value != '') {
                                el.classList.remove('highlight-input');
                            } else {
                                if (!el.classList.contains('highlight-input')) {
                                    el.classList.add('highlight-input');
                                }
                            }
                        });

                        camposMensagem += camposMensagem ? ', ' + campo.p73_label : campo.p73_label;
                    }
                }

                if (elLabel) {
                    elLabel.classList.add('required');
                }
            });

            if (camposMensagem) {
                return alert('Os seguintes campos são obrigatórios: ' + camposMensagem);
            }

        });
    }

    /**@param
     *
     * funcaoRetornoDados - Nome da função de retorno para preencher input
     * retornaBrasil - recebe S ou N
     *
     */
    function abreJanelaPaises(funcaoRetornoDados, retornaBrasil = 'S') {
        js_OpenJanelaIframe(
            '',
            'db_iframe_pais',
            'func_paises.php?funcao_js=' + funcaoRetornoDados + '&retornaBrasil=' + retornaBrasil,
            'Pesquisa Países',
            true
        );
    }

    function js_mostraPaisEndereco(codigo, codpais, descricao) {

        document.getElementById('z19_pais').value = codigo;
        document.getElementById('cod_paisendereco').value = codpais;
        document.getElementById('descr_paisendereco').value = descricao;
    }

    function js_mostraPaisNascimento(codigo, codpais, descricao) {

        document.getElementById('z04_paisnascimento').value = codigo;
        document.getElementById('cod_paisnasc').value = codpais;
        document.getElementById('descr_paisnasc').value = descricao
    }

    function js_mostraPaisNacionalidade(codigo, codpais, descricao) {

        document.getElementById('z04_paisnacionalidade').value = codigo;
        document.getElementById('cod_paisnac').value = codpais;
        document.getElementById('descr_paisnac').value = descricao;
        document.getElementById('z09_pais').value = descricao;
    }

    function abreJanelaAlteracoes() {
        js_OpenJanelaIframe(
            '',
            'db_iframe_cgmaltres',
            'func_cgmaltresum.php?pesquisa_numcgm=' + $('z01_numcgm').value + '&funcao_js=parent.js_mostracgmalt|z05_sequencia|z05_numcgm',
            'Pesquisa',
            true
        );
    }

    function js_mostracgmalt(sequencialCgmAlteracao, numeroCgm) {
        db_iframe_cgmaltres.hide();
        location.href = `prot1_cadgeralmunic005.php?chavepesquisa=${numeroCgm}&sequencialCgmAlteracao=${sequencialCgmAlteracao}`;
    }

    function getCgmOriginal() {
        const formData = new FormData();

        formData.append('exec', 'getNumeroCgmOriginal');
        formData.append('sequencialCgmAlteracao', sequencialCgmAlteracao);

        HttpClient.post('prot1_cadgeralmunic.RPC.php', {
                body: formData
            })
            .then((response) => {
                if (response.status == 2) {
                    alert(response.message.urlDecode());
                    return;
                }

                window.location.href = `prot1_cadgeralmunic005.php?chavepesquisa=${response.numeroCgm}&cgmOriginal=1`;
            });
    }

    //========================================    ANCORA DA CBO =========================

    function js_pesquisaCbo(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_Cbo', 'func_rhcbo.php?funcao_js=parent.js_mostraCbo|rh70_sequencial|rh70_descr|rh70_estrutural', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('', 'db_iframe_Cbo', 'func_rhcbo.php?lCadastroCgm=true&pesquisa_chave=' + document.form1.rh70_sequencial.value + '&funcao_js=parent.js_mostraCboHide', 'Pesquisa', false);
        }

    }

    function js_mostraCboHide(chave, chave2, chave3, erro) {

        if (chave2 != false) {

            if (erro == true) {

                document.form1.rh70_sequencial.value = '';
                document.form1.rh70_sequencial.focus();

            }

            document.form1.rh70_descr.value = chave3 + ' - ' + chave2;

        } else {

            document.form1.rh70_sequencial.value = '';
            document.form1.rh70_descr.value = '';

        }

    }

    function js_mostraCbo(chave1, chave2, chave3) {

        document.form1.rh70_sequencial.value = chave1;
        document.form1.rh70_descr.value = chave3 + ' - ' + chave2;
        db_iframe_Cbo.hide();

    }

    if (parent.document.getElementById('cgm')) {
        parent.document.getElementById('cgm').style.display = '';
    }

    if (parent.document.getElementById('documentos')) {
        parent.document.getElementById('documentos').style.display = '';
    }

    if (parent.document.getElementById('fotos')) {
        parent.document.getElementById('fotos').style.display = '';
    }

    var j14_codigo = "";
    var j13_codi = "";

    var funcaoRetorno = "<?= $funcaoRetorno ?>";

    /* -----------------------------------------------------*/

    function js_ToUperCampos(campos) {

        var sCampo = $F(campos).toUpperCase();
        $(campos).value = sCampo;
    }

    if (lPessoaFisica) {

        if ($('z01_naturalidade')) {
            $('z01_naturalidade').observe('keyup', function() {
                $('z01_naturalidade').style.textTransform = 'uppercase';
            });
        }

    } else {

        $('z01_nome').observe('keyup', function() {
            $('z01_nome').style.textTransform = 'uppercase';
        });

        $('z01_nomecomple').observe('keyup', function() {
            $('z01_nomecomple').style.textTransform = 'uppercase';
        });

        $('z01_nomefanta').observe('keyup', function() {
            $('z01_nomefanta').style.textTransform = 'uppercase';
        });

        $('z08_nire').setAttribute("maxlength", "11");

    }
    /*-----------------------------------Trata do endereço primário ----------------------------------- */
    /* Função do onclick do botão Lancar do endereço primário */
    //func_ruas.php

    function js_ruas() {
        js_OpenJanelaIframe('', 'db_iframe',
            'func_ruas.php?rural=1&funcao_js=parent.js_preenchepesquisaruas|j14_codigo|j14_nome',
            'Pesquisa', true, 0, 0);
    }

    function js_ruas1() {
        js_OpenJanelaIframe('', 'db_iframe',
            'func_ruas.php?rural=1&funcao_js=parent.js_preenchepesquisaruas1|j14_codigo|j14_nome',
            'Pesquisa', true, 0, 0);
    }

    function js_preenchepesquisaruas(chave, chave1) {
        j14_codigo = chave;
        db_iframe.hide();
        js_bairro();
    }

    function js_preenchepesquisaruas1(chave, chave1) {

        j14_codigo = chave;
        db_iframe.hide();
        js_bairro1();
    }

    function js_bairro() {
        js_OpenJanelaIframe('', 'db_iframe_bairro',
            'func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro|j13_codi|j13_descr',
            'Pesquisa', true);
    }

    function js_bairro1() {
        js_OpenJanelaIframe('', 'db_iframe_bairro',
            'func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1|j13_codi|j13_descr',
            'Pesquisa', true);
    }

    function js_preenchebairro(chave, chave1) {

        j13_codi = chave;
        db_iframe_bairro.hide();
        js_abreEnderPrimario();
    }

    function js_preenchebairro1(chave, chave1) {

        j13_codi = chave;
        db_iframe_bairro.hide();
        js_abreEnderSecundario();
    }


    function js_lancaEnderPrimario() {

        j13_codi = '';
        j14_codigo = '';

        //Tem que validar se o cgm é do municipio ai tem que selecionar a rua e o bairro
        //chamar a cgm ruas e depois o bairro do cadastro imobiliário
        if ($F('municipio') == 't' && $F('idEnderPrimario').trim() == "") {
            js_ruas();
        } else {
            js_abreEnderPrimario();
        }
    }

    function js_abreEnderPrimario() {
        var ov02_sequencial = $F('ov02_sequencial');
        var ov02_seq = $F('ov02_seq');

        var idEnderPrimario = '';
        if ($F('idEnderPrimario') != "") {

            idEnderPrimario = $F('idEnderPrimario');
        }

        let parametroCamposObrigatorios = $F('municipio') === 't' ? [] : camposObrigatorios;

        oEnderPrimario = new DBViewCadastroEndereco('pri', 'oEnderPrimario', idEnderPrimario, parametroCamposObrigatorios);

        var lEnderMunic = $F('municipio') == 't' ? true : false;

        oEnderPrimario.setMunicipio($('janela-pri-id-municipio').value);
        oEnderPrimario.setNomeMunicipio($('janela-pri-descricao-municipio').value);
        oEnderPrimario.setObjetoRetorno($('idEnderPrimario'));
        oEnderPrimario.setTipoValidacao(2);
        oEnderPrimario.setEnderecoMunicipio(lEnderMunic);
        oEnderPrimario.setCallBackFunction(function() {
            js_lancaEnderPrimarioCallBack()
        });
        oEnderPrimario.setCodigoBairroMunicipio(j13_codi);
        oEnderPrimario.setCodigoRuaMunicipio(j14_codigo);

        oEnderPrimario.setCodigoCidadao(ov02_sequencial, ov02_seq);

        if (lEnderMunic) {
            oEnderPrimario.buscaEndereco();
        }
        oEnderPrimario.show();
    }

    /* Função disparada no retorno do fechamneto da janela de endereço */
    function js_lancaEnderPrimarioCallBack() {

        var oEndereco = new Object();
        oEndereco.exec = 'findEnderecoByCodigo';
        oEndereco.iCodigoEndereco = $F('idEnderPrimario');
        js_AjaxCgm(oEndereco, js_retornoEnderPrimario);

        function js_retornoEnderPrimario(oAjax) {

            js_removeObj('msgBox');

            var oRetorno = JSON.parse(oAjax.responseText);

            var sExpReg = new RegExp('\\\\n', 'g');

            if (oRetorno.endereco == false) {

                var strMessageUsuario = "Falha ao ler o endereço cadastrado! ";
                js_messageBox(strMessageUsuario, '');
                return false;
            } else {

                oRetorno.endereco[0].stipo = 'P';
                js_PreencheEndereco(oRetorno.endereco);
            }
        }
    }

    /*-----------------------------------Fim do endereço primário ----------------------------------- */

    /*-----------------------------------Trata do endereço secundário-------------------------------- */
    function js_lancaEnderSecundario() {

        j13_codi = '';
        j14_codigo = '';

        //Tem que validar se o cgm é do municipio ai tem que selecionar a rua e o bairro
        //chamar a cgm ruas e depois o bairro do cadastro imobiliário
        js_abreEnderSecundario();
    }

    function js_abreEnderSecundario() {

        var idEnderSecundario = '';
        if ($F('idEnderSecundario') != "") {
            idEnderSecundario = $F('idEnderSecundario');
        }

        oEnderSecundario = new DBViewCadastroEndereco('sec', 'oEnderSecundario', idEnderSecundario);
        oEnderSecundario.setObjetoRetorno($('idEnderSecundario'));

        oEnderSecundario.setTipoValidacao(2);
        oEnderSecundario.setCallBackFunction(function() {
            js_lancaEnderSecundarioCallBack()
        });
        oEnderSecundario.setCodigoBairroMunicipio(j13_codi);
        oEnderSecundario.setCodigoRuaMunicipio(j14_codigo);

        oEnderSecundario.show();
    }

    /* Função disparada no retorno do fechamneto da janela de endereço */
    function js_lancaEnderSecundarioCallBack() {

        var oEndereco = new Object();
        oEndereco.exec = 'findEnderecoByCodigo';
        oEndereco.iCodigoEndereco = $F('idEnderSecundario');

        js_AjaxCgm(oEndereco, js_retornoEnderSecundario);

        function js_retornoEnderSecundario(oAjax) {

            js_removeObj('msgBox');

            var oRetorno = JSON.parse(oAjax.responseText);

            var sExpReg = new RegExp('\\\\n', 'g');

            if (oRetorno.endereco == false) {

                var strMessageUsuario = "Falha ao ler o endereço cadastrado! ";
                js_messageBox(strMessageUsuario, '');
                return false;
            } else {

                oRetorno.endereco[0].stipo = 'S';
                js_PreencheEndereco(oRetorno.endereco);
            }
        }
    }

    /*-----------------------------------Fim do endereço secundário ----------------------------------- */

    function js_pesquisaTipoEmpresa(mostra) {
        if (mostra == true) {

            var funcao = 'parent.js_mostraTipoEmpresa1|db98_sequencial|db98_descricao';
            js_OpenJanelaIframe('', 'db_iframe_tipoempresa',
                'func_tipoempresa.php?funcao_js=' + funcao,
                'Pesquisa', true, 0, 0);
        } else {
            if ($F('z03_tipoempresa') != '') {

                var pesquisaChave = $F('z03_tipoempresa');
                var funcao = 'parent.js_mostraTipoEmpresa';
                js_OpenJanelaIframe('',
                    'db_iframe_tipoempresa',
                    'func_tipoempresa.php?pesquisa_chave=' + pesquisaChave + '&funcao_js=' + funcao,
                    'Pesquisa', false, 0, 0);
            } else {
                $('db98_descricao').value = '';
            }
        }
    }

    function js_mostraTipoEmpresa(chave, erro) {
        $('db98_descricao').value = chave;
        if (erro == true) {
            $('z03_tipoempresa').focus();
            $('z03_tipoempresa').value = '';
        }
    }

    function js_mostraTipoEmpresa1(chave1, chave2) {
        $('z03_tipoempresa').value = chave1;
        $('db98_descricao').value = chave2;
        db_iframe_tipoempresa.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('',
            'func_nome',
            'func_nome.php?funcao_js=parent.js_preenchepesquisa|0|1|2&ifrname=func_nome',
            'Pesquisa', true, '0', '1');
    }

    function js_preenchepesquisa(chave, nome, cpfCnpj) {
        func_nome.hide();

        <?php
        if ($db_opcao != 1) { ?>
            if (cpfCnpj.length != 11 && cpfCnpj.length != 14) {
                window.parent.location.href = `prot1_selecionatipopessoacgm.php?numeroCgm=${chave}&nome=${nome}`;
            } else {
                <?php echo " location.href = '" . basename($_SERVER["PHP_SELF"]) . "?chavepesquisa='+chave"; ?>
            }
        <?php
        } ?>
    }

    function js_findCidadao(chavePesquisa) {

        if (chavePesquisa == "" || chavePesquisa == null) {
            return false;
        }

        sUrlRpc = "prot1_cadgeralmunic.RPC.php";

        var oCidadao = new Object();
        oCidadao.exec = 'findCidadao';
        oCidadao.ov02_sequencial = chavePesquisa;

        var msgDiv = "Aguarde ... Carregando dados do Cidadão.";
        js_divCarregando(msgDiv, 'msgBox');

        var oAjax = new Ajax.Request(
            sUrlRpc, {
                parameters: 'json=' + Object.toJSON(oCidadao),
                method: 'post',
                onComplete: js_retornoFindCidadao
            }

        );
    }

    function js_retornoFindCidadao(oAjax) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        var sExpReg = new RegExp('\\\\n', 'g');

        if (oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            parent.location.href = "prot1_cadgeralmunic001.php";
            return false;
        } else {

            if (oRetorno.cidadao.ov02_cnpjcpf.length == 11) {
                $('z01_cpf').value = oRetorno.cidadao.ov02_cnpjcpf;
                $('z01_ident').value = oRetorno.cidadao.z01_ident;
            } else if (oRetorno.cidadao.ov02_cnpjcpf.length == 14) {

                $('z01_cgc').value = oRetorno.cidadao.ov02_cnpjcpf;
                $('z01_nomecomple').value = oRetorno.cidadao.z01_nome.urlDecode();
            }

            $('z01_nome').value = oRetorno.cidadao.z01_nome.urlDecode();
            $('z01_telef').value = oRetorno.cidadao.z01_telef;
            $('z01_email').value = oRetorno.cidadao.z01_email.urlDecode();
            $('ov02_sequencial').value = oRetorno.cidadao.ov02_sequencial;
            $('ov02_seq').value = oRetorno.cidadao.ov02_seq;

            if (oRetorno.endereco != false) {

                js_PreencheEndereco(oRetorno.endereco);
            }
        }

    }

    function js_findCgm(chavePesquisa) {
        sUrlRpc = "prot1_cadgeralmunic.RPC.php";

        var oCgm = new Object();

        oCgm.exec = !sequencialCgmAlteracao ? 'findCgm' : 'findCgmAlteracao';
        oCgm.sequencialCgmAlteracao = sequencialCgmAlteracao | '';
        oCgm.numcgm = chavePesquisa;

        var parametros = {
            parameters: 'json=' + Object.toJSON(oCgm),
            method: 'post',
            onComplete: js_retornoFindCgm
        };

        var msgDiv = "Aguarde ...";
        js_divCarregando(msgDiv, 'msgBox');

        var oAjax = new Ajax.Request(
            sUrlRpc,
            parametros
        );
    }

    function js_retornoFindCgm(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        var sExpReg = new RegExp('\\\\n', 'g');

        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            parent.location.href = "prot1_cadgeralmunic002.php";

            return false;
        } else {
            if (oRetorno.endereco && oRetorno.endereco != false) {
                js_PreencheEndereco(oRetorno.endereco);
            }

            if (oRetorno.tipoempresa && oRetorno.tipoempresa != false) {
                js_PreencheTipoEmpresa(oRetorno.tipoempresa);
            }

            js_PreencheFormulario(oRetorno.cgm);

            js_cgmMunicipio(oRetorno.cgmmunicipio);

            if (oRetorno.lPermissaoCidadao) {
                if (oRetorno.cidadaocgm != false) {
                    var ov02_sequencial = oRetorno.cidadaocgm[0].ov03_cidadao;
                    var ov02_seq = oRetorno.cidadaocgm[0].ov03_seq;
                    var ov03_numcgm = oRetorno.cidadaocgm[0].ov03_numcgm;

                    $('btnVincular').style.display = 'none';
                    $('btnImportar').style.display = '';
                    $('btnImportar').observe('click', function() {
                        js_MICidadao(ov02_sequencial, ov02_seq, ov03_numcgm);
                    });
                } else {
                    $('btnVincular').style.display = '';
                    $('btnImportar').style.display = 'none';
                }
            }
        }
    }

    function js_cgmMunicipio(lCgmMunicipio) {

        $('municipio').value = lCgmMunicipio == true ? 't' : 'f';
    }

    function js_PreencheTipoEmpresa(aTipoEmpresa) {

        $('z03_tipoempresa').value = aTipoEmpresa[0].z03_tipoempresa;
        js_ProcCod_z03_tipoempresa('z03_tipoempresa', 'z03_tipoempresadescr');
    }

    /*-----------------Função para peencher os endereços primário e secundário do form ----------------------*/
    function js_PreencheEndereco(aEndereco) {

        var iNumEndereco = aEndereco.length;
        for (var iInd = 0; iInd < iNumEndereco; iInd++) {

            var sEndereco = "";
            sEndereco += aEndereco[iInd].srua.urlDecode();
            sEndereco += ",  nº " + aEndereco[iInd].snumero.urlDecode();
            sEndereco += " " + aEndereco[iInd].scomplemento.urlDecode();
            sEndereco += " - " + aEndereco[iInd].sbairro.urlDecode();
            sEndereco += " - " + aEndereco[iInd].smunicipio.urlDecode();
            sEndereco += " - " + aEndereco[iInd].ssigla.urlDecode();

            if (aEndereco[iInd].stipo == 'P') {

                $('idEnderPrimario').value = aEndereco[iInd].iendereco;
                $('endPrimario').value = sEndereco;
            } else {
                $('idEnderSecundario').value = aEndereco[iInd].iendereco;
                $('endSecundario').value = sEndereco;
            }
        }
    }
    /*-----------------------Fim da função que preenche os endereços e secundário do form ----------------------*/


    function js_PreencheFormulario(oCgm) {
        const cpfCnpj = typeof oCgm.z01_cpf === 'undefined' ? oCgm.z01_cgc : oCgm.z01_cpf;

        $('z01_nome').value = oCgm.z01_nome.urlDecode();
        $('z01_nomecomple').value = oCgm.z01_nomecomple.urlDecode();
        $('z01_numcgm').value = oCgm.z01_numcgm;

        if ($('z01_cpf')) {
            $('z01_cpf').value = cpfInformado ? cpfInformado : cpfCnpj;
        } else {
            $('z01_cgc').value = cnpjInformado ? cnpjInformado : cpfCnpj;
        }

        if (oCgm.data_alteracao) {
            $('z01_ultalt').value = oCgm.data_alteracao;
        }

        $('z01_cadast').value = oCgm.z01_cadast;
        $('z01_obs').value = oCgm.z01_obs ? oCgm.z01_obs.urlDecode() : '';


        if (lPessoaFisica == true) {

            $('z01_ident').value = oCgm.z01_ident;
            $('z01_pai').value = oCgm.z01_pai ? oCgm.z01_pai.urlDecode() : '';
            $('z01_mae').value = oCgm.z01_mae ? oCgm.z01_mae.urlDecode() : '';
            $('z01_nasc').value = oCgm.z01_nasc ? js_formatar(oCgm.z01_nasc, 'd', '') : '';
            $('z01_estciv').value = oCgm.z01_estciv;
            $('z01_sexo').value = oCgm.z01_sexo;
            $('z01_nacion').value = oCgm.z01_nacion;
            $('z01_profis').value = oCgm.z01_profis ? oCgm.z01_profis.urlDecode() : '';
            $('z01_telef').value = oCgm.z01_telef;
            $('z01_telcel').value = oCgm.z01_telcel;
            $('z01_email').value = oCgm.z01_email ? oCgm.z01_email.urlDecode() : '';
            $('z01_telcon').value = oCgm.z01_telcon;
            $('z01_celcon').value = oCgm.z01_celcon;
            $('z01_emailc').value = oCgm.z01_emailc ? oCgm.z01_emailc.urlDecode() : '';
            $('z01_dtfalecimento').value = oCgm.z01_dtfalecimento ? js_formatar(oCgm.z01_dtfalecimento, 'd', '') : '';
            $('z01_identdtexp').value = oCgm.z01_identdtexp ? js_formatar(oCgm.z01_identdtexp, 'd', '') : '';
            $('z01_naturalidade').value = oCgm.z01_naturalidade ? oCgm.z01_naturalidade.urlDecode() : '';
            $('z01_escolaridade').value = oCgm.z01_escolaridade ? oCgm.z01_escolaridade.urlDecode() : '';
            $('z01_identorgao').value = oCgm.z01_identorgao ? oCgm.z01_identorgao.urlDecode() : '';
            $('z01_trabalha').value = oCgm.z01_trabalha == true ? 't' : 'f';
            $('z01_localtrabalho').value = oCgm.z01_localtrabalha ? oCgm.z01_localtrabalha.urlDecode() : '';
            $('z01_renda').value = oCgm.z01_renda;
            $('z01_pis').value = oCgm.z01_pis;
            $('rh70_sequencial').value = oCgm.z04_rhcbo;
            /**
             *  Campos para e-social - Inicio
             */
            $('z04_nomesocial').value = oCgm.z04_nomesocial;
            $('z04_paisnascimento').value = oCgm.z04_paisnascimento;
            $('descr_paisnasc').value = oCgm.descr_paisnasc.urlDecode();
            $('cod_paisnasc').value = oCgm.cod_paisnasc;
            $('z04_paisnacionalidade').value = oCgm.z04_paisnacionalidade;
            $('descr_paisnac').value = oCgm.descr_paisnac.urlDecode();
            $('cod_paisnac').value = oCgm.cod_paisnac;
            $('z19_pais').value = oCgm.z19_pais;
            $('descr_paisendereco').value = oCgm.descr_paisendereco.urlDecode();
            $('cod_paisendereco').value = oCgm.cod_paisendereco;
            $('z19_cidade').value = oCgm.z19_cidade.urlDecode();
            $('z19_logradouro').value = oCgm.z19_logradouro.urlDecode();
            $('z19_bairro').value = oCgm.z19_bairro.urlDecode();
            $('z19_numero').value = oCgm.z19_numero;
            $('z19_codigopostal').value = oCgm.z19_codigopostal.urlDecode();
            $('z19_complemento').value = oCgm.z19_complemento.urlDecode();
            /**
             *  Campos para e-social - Fim
             */
            //Novos Campos
            $('z01_fax').value = oCgm.z01_fax ? oCgm.z01_fax.urlDecode() : '';
            $('z01_cxpostal').value = oCgm.z01_cxpostal;
            $('z01_cxposcon').value = oCgm.z01_cxposcon;
            $('z01_incest').value = oCgm.z01_incest;
            $('z01_obs').value = oCgm.z01_obs ? oCgm.z01_obs.urlDecode() : '';
            $('z09_documento').value = oCgm.z09_documento ? oCgm.z09_documento.urlDecode() : '';
            $('z09_pais').value = oCgm.z09_pais ? oCgm.z09_pais.urlDecode() : '';
            $('z09_cidade').value = oCgm.z09_cidade ? oCgm.z09_cidade.urlDecode() : '';

            if ($('z01_nacion').value == 2) {
                verificaNacionalidade();
            }

            if (oCgm.z01_foto != null) {
                $('fotocgm').src = 'func_mostrarimagem.php?oid=' + oCgm.z01_foto;
            } else {
                $('fotocgm').src = 'imagens/none1.jpeg';
            }

            js_pesquisaCbo(false);
        } else if (lPessoaFisica == false) {

            if (oCgm.z01_nacion) {

                $('z01_nacion').value = oCgm.z01_nacion;

                var oInputNacionalidade = $('z01_nacion');

                if (oInputNacionalidade) {
                    oInputNacionalidade.observe('change', verificaNacionalidade);

                    if ($('z01_nacion').value == 2) {
                        verificaNacionalidade();

                        $('z09_documento').value = oCgm.z09_documento ? oCgm.z09_documento.urlDecode() : '';
                        $('z09_pais').value = oCgm.z09_pais ? oCgm.z09_pais.urlDecode() : '';
                        $('z09_cidade').value = oCgm.z09_cidade ? oCgm.z09_cidade.urlDecode() : '';
                    } else {

                        $('divEmpresaEstrangeira').style.display = 'none';
                    }
                }
            }

            $('z01_incest').value = oCgm.z01_incest;
            $('z01_telef').value = oCgm.z01_telef;
            $('z01_telcel').value = oCgm.z01_telcel;
            $('z01_email').value = oCgm.z01_email.urlDecode();
            $('z01_nasc').value = oCgm.z01_nasc ? js_formatar(oCgm.z01_nasc, 'd', '') : '';
            $('z01_telcon').value = oCgm.z01_telcon;
            $('z01_celcon').value = oCgm.z01_celcon;
            $('z01_emailc').value = oCgm.z01_emailc.urlDecode();
            $('z01_contato').value = oCgm.z01_contato.urlDecode();
            $('z01_nomefanta').value = oCgm.z01_nomefanta.urlDecode();
            $('z01_nomecomple').value = oCgm.z01_nomecomple.urlDecode();
            $('z01_nome').value = oCgm.z01_nome.urlDecode();
            $('z08_nire').value = oCgm.nire;
            console.log("novos campos");
            //Novos Campos
            $('z01_fax').value = oCgm.z01_fax;
            $('z01_cxpostal').value = oCgm.z01_cxpostal;
            $('z01_cxposcon').value = oCgm.z01_cxposcon;
            $('z01_obs').value = oCgm.z01_obs.urlDecode();




            if (oCgm.z01_foto != null) {
                $('fotocgm').src = 'func_mostrarimagem.php?oid=' + oCgm.z01_foto;
            } else {
                $('fotocgm').src = 'imagens/none1.jpeg';
            }
        }

    }
    /*------------------ Funções de Validação do formulário ---------------------------------*/
    function js_tamnome() {

        var nome = $('z01_nome').value;
        var tam = nome.split(" ");
        var passa = true;
        if (tam.length < 2) {

            var strMessageUsuario = "Nome inconsistente (regra 1)!";
            js_messageBox(strMessageUsuario, '');
            $('z01_nome').value = "";
            $('z01_nome').focus;
            passa = false;
        } else if (1 == 2) {

            for (i = 0; i < tam.length; i++) {

                if (pessoa == 'f') {

                    if (tam[0].length < 2 || tam[1].length < 2) {

                        alert("Nome inconsistente (regra 2)!");
                        $('z01_nome').value = "";
                        $('z01_nome').focus;
                        passa = false;
                        break;
                    }
                }
            }
        }
        if (lPessoaFisica == false) {

            nomecomple = $('z01_nomecomple').value;
            tamcomple = nomecomple.split(" ");
            if (tamcomple.length < 2) {

                alert("Nome Completo inconsistente (regra 3)!");
                $('z01_nomecomple').value = "";
                $('z01_nomecomple').focus();
                passa = false;
            }
        }
        if (passa == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Valida se CPF/CNPJ ja esta cadastrado para outro cgm
     *
     * @param {String} btnValue
     */
    function js_validarCGCCPF(btnValue) {

        var cgcCpf = '';

        if (lPessoaFisica) {

            cgcCpf = $F('z01_cpf');

            if (!js_verificaCGCCPF($('z01_cpf'))) {
                return alert('CPF/CNPJ não informado.');
            }

        } else {

            cgcCpf = $F('z01_cgc');

            if (!js_verificaCGCCPF($('z01_cgc'))) {
                return false;
            }
        }

        if (empty(cgcCpf) || btnValue == 'Excluir') {
            return js_sendForm(btnValue);
        }

        js_divCarregando("Aguarde verificando CPF/CNPJ.", "msgBox");

        var oPesquisa = {
            exec: "findCpfCnpj",
            iCpfCnpj: cgcCpf
        };
        var sUrlRpc = "prot1_cadgeralmunic.RPC.php";
        var oParametros = {
            parameters: 'json=' + Object.toJSON(oPesquisa),
            method: 'post',
            onComplete: function(oAjax) {

                js_removeObj("msgBox");
                var oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.z01_numcgm == false || oRetorno.z01_numcgm == $F('z01_numcgm')) {
                    return js_sendForm(btnValue)
                } else {
                    alert("usuário:\n\n Cnpj/Cpf já cadastrado para o CGM " + oRetorno.z01_numcgm);
                    return false;
                }

            }
        };

        var oAjax = new Ajax.Request(sUrlRpc, oParametros);
    }

    function validaIncluirPessoaFisica() {
        if (!js_verificaCGCCPF($('z01_cpf'))) {
            return alert('CPF/CNPJ não informado.');
        }

        /**
         * Caso email seja preenchido valida email
         */
        if ($F('z01_email') != '') {

            if (!js_validaEmail($F('z01_email'))) {
                return false;
            }
        }

        /**
         * valida email comercial
         */
        if ($F('z01_emailc') != '') {

            if (!js_validaEmail($F('z01_emailc'))) {
                return false;
            }
        }

        //Modificacao Plugin validacoes pvh

        if ($F('z01_cpf').trim() == "") {
            js_messageBox("Campo CPF não informado !", '');
            $('z01_cpf').focus();
            return false;
        }

        if ($F('z01_nome').trim() == "") {
            js_messageBox("Campo Nome/Razão Social não informado!", '');
            $('z01_nome').focus();
            return false;
        }

        if (!js_tamnome()) {
            return false;
        }


        if ($F('idEnderPrimario').trim() == "") {
            js_messageBox("Endereço Primário não informado!", '');
            $('endPrimario').focus();
            js_lancaEnderPrimario();
            return false;
        }

        if ($F('z01_dtfalecimento') != '' && $F('z01_nasc') != '') {

            var mValidaDatas = js_diferenca_datas(js_formatar($F('z01_nasc'), 'd'), js_formatar($F('z01_dtfalecimento'), 'd'), 3);

            /**
             * Data de falecimento menor
             * - mValidaDatas = 'i' quando datas forem iguais
             */
            if (mValidaDatas != 'i' && mValidaDatas) {

                js_messageBox('Data de falecimento não pode ser menor que data de nascimento.', '');
                $('z01_dtfalecimento').focus();
                return false;
            }

        }

        /**
         * Caso valor da renda possua hifen
         */
        if ($F('z01_renda') != '' && $F('z01_renda').indexOf('-') != -1) {
            alert('Valor para renda inválido.');
            return false;
        }

        return true;
    }

    function validaIncluirPessoaJuridica() {
        if (!js_verificaCGCCPF($('z01_cgc'))) {
            return false;
        }

        if ($F('z01_email') != '') {
            if (!js_validaEmail($F('z01_email'))) {
                return false;
            }
        }

        if ($F('z01_emailc') != '') {
            if (!js_validaEmail($F('z01_emailc'))) {
                return false;
            }
        }

        //Modificacao Plugin validacoes pvh

        if ($F('z01_cgc').trim() == "") {
            js_messageBox("Campo CNPJ não informado !", '');
            $('z01_cgc').focus();
            return false;
        }

        if ($F('z01_nome').trim() == "") {
            js_messageBox("Campo Nome/Razão Social não informado!", '');
            $('z01_nome').focus();
            return false;
        }

        if (!js_tamnome()) {
            return false;
        }

        if ($F('z01_nomecomple').trim() == "") {
            js_messageBox("Campo Nome Completo não informado!", '');
            $('z01_nomecomple').focus();
            return false;
        }

        if (!js_tamnome()) {
            return false;
        }

        if ($F('idEnderPrimario').trim() == "") {
            js_messageBox("Endereço Primário não informado!", '');
            $('endPrimario').focus();
            js_lancaEnderPrimario();
            return false;
        }

        if ($F('z01_nacion') == "2") {
            if ($F('z09_documento').trim() == "" || $F('z09_cidade').trim() == "" || $F('z09_pais').trim() == "") {
                js_messageBox("Os campos Documento, Cidade e País devem ser informados", '');
                $('z09_documento').focus();
                return false;
            }
        }

        return true;
    }

    async function incluirAlterarPessoaFisica() {
        let valido = validaIncluirPessoaFisica();

        if (!valido) {
            return;
        }

        const btnSubmit = $('btnSubmit').value;

        var oIncluir = {};
        oIncluir.exec = 'incluirAlterar';
        oIncluir.lPessoaFisica = true;
        oIncluir.action = "incluir";
        if (btnSubmit === "Alterar") {
            oIncluir.action = "alterar";
        }

        var oPessoa = {};

        oPessoa.z01_numcgm = $F('z01_numcgm').trim();
        oPessoa.z01_cgccpf = $F('z01_cpf');
        oPessoa.z01_ident = $F('z01_ident');
        oPessoa.z01_nome = ($F('z01_nome'));
        oPessoa.z01_nomecomple = ($F('z01_nomecomple'));
        oPessoa.z01_pai = ($F('z01_pai'));
        oPessoa.z01_mae = ($F('z01_mae'));

        oPessoa.z01_nasc = js_formatar($F('z01_nasc'), 'd');
        oPessoa.z01_estciv = $F('z01_estciv');
        oPessoa.z01_sexo = $F('z01_sexo');
        oPessoa.z01_nacion = $F('z01_nacion');

        oPessoa.z01_profis = tagString($F('z01_profis'));
        oPessoa.z01_telef = $F('z01_telef');
        oPessoa.z01_telcel = $F('z01_telcel');
        oPessoa.z01_email = tagString($F('z01_email'));
        oPessoa.z01_telcon = $F('z01_telcon');

        oPessoa.z01_celcon = $F('z01_celcon');
        oPessoa.z01_emailc = tagString($F('z01_emailc'));
        oPessoa.z01_cadast = js_formatar($F('z01_cadast'), 'd');

        if ($('z01_ultalt')) {
            oPessoa.z01_ultalt = js_formatar($F('z01_ultalt'), 'd');
        }

        oPessoa.z01_dtfalecimento = js_formatar($F('z01_dtfalecimento'), 'd');
        oPessoa.z01_identdtexp = js_formatar($F('z01_identdtexp'), 'd');
        oPessoa.z01_identorgao = tagString($F('z01_identorgao'));

        oPessoa.z01_naturalidade = tagString($F('z01_naturalidade'));
        oPessoa.z01_escolaridade = tagString($F('z01_escolaridade'));
        oPessoa.z01_localtrabalho = tagString($F('z01_localtrabalho'));

        oPessoa.z01_renda = tagString($F('z01_renda'));
        oPessoa.z01_pis = tagString($F('z01_pis'));
        oPessoa.z01_trabalha = $F('z01_trabalha');

        oPessoa.z01_fax = $F('z01_fax');
        oPessoa.z01_cxpostal = $F('z01_cxpostal');
        oPessoa.z01_cxposcon = $F('z01_cxposcon');
        oPessoa.z01_incest = $F('z01_incest');

        oPessoa.z01_obs = $F('z01_obs');

        oPessoa.z04_rhcbo = $F('rh70_sequencial');
        oPessoa.z04_nomesocial = $F('z04_nomesocial').trim() == '' ? null : $F('z04_nomesocial').trim();
        oPessoa.z04_paisnascimento = $F('z04_paisnascimento').trim() == '' ? null : $F('z04_paisnascimento');
        oPessoa.z04_paisnacionalidade = $F('z04_paisnacionalidade').trim() == '' ? null : $F('z04_paisnacionalidade');
        oPessoa.z19_pais = $F('z19_pais').trim() == '' ? null : $F('z19_pais').trim();
        oPessoa.z19_cidade = $F('z19_cidade').trim() == '' ? null : $F('z19_cidade').trim();
        oPessoa.z19_logradouro = $F('z19_logradouro').trim() == '' ? null : $F('z19_logradouro').trim();
        oPessoa.z19_bairro = $F('z19_bairro').trim() == '' ? null : $F('z19_bairro').trim();
        oPessoa.z19_numero = $F('z19_numero').trim() == '' ? null : $F('z19_numero').trim();
        oPessoa.z19_codigopostal = $F('z19_codigopostal').trim() == '' ? null : $F('z19_codigopostal').trim();
        oPessoa.z19_complemento = $F('z19_complemento').trim() == '' ? null : $F('z19_complemento').trim();

        oPessoa.z09_documento = encodeURIComponent(tagString($F('z09_documento')));
        oPessoa.z09_cidade = encodeURIComponent(tagString($F('z09_cidade')));
        oPessoa.z09_pais = encodeURIComponent(tagString($F('z09_pais')));

        var oEndereco = {};
        oEndereco.idEndPrimario = $F('idEnderPrimario');
        oEndereco.idEndSecundario = $F('idEnderSecundario');

        var oTipoEmpresa = {};
        oTipoEmpresa.iTipoEmpresa = $F('z03_tipoempresa');

        var oCidadao = {};
        oCidadao.ov02_sequencial = $F('ov02_sequencial');
        oCidadao.ov02_seq = $F('ov02_seq');

        oIncluir.pessoa = {};
        oIncluir.pessoa = oPessoa;

        oIncluir.endereco = {};
        oIncluir.endereco = oEndereco;

        oIncluir.tipoEmpresa = {};
        oIncluir.tipoEmpresa = oTipoEmpresa;

        oIncluir.cidadao = {};
        oIncluir.cidadao = oCidadao;

        js_AjaxCgm(oIncluir, js_retornoIncluirFisica);
    }

    async function incluirAlterarPessoaJuridica() {
        let valido = validaIncluirPessoaJuridica();

        if (!valido) {
            return;
        }

        const btnSubmit = $('btnSubmit').value;

        var oIncluir = {};
        oIncluir.exec = 'incluirAlterar';
        oIncluir.lPessoaFisica = false;
        oIncluir.action = "incluir";
        if (btnSubmit === "Alterar") {
            oIncluir.action = "alterar";
        }

        var oPessoa = {};
        oPessoa.z01_numcgm = $F('z01_numcgm').trim();
        oPessoa.z01_cgccpf = $F('z01_cgc');
        oPessoa.z01_nome = tagString($F('z01_nome'));
        oPessoa.z01_contato = tagString($F('z01_contato'));
        oPessoa.z01_incest = $F('z01_incest');
        oPessoa.z01_telef = $F('z01_telef');
        oPessoa.z01_telcel = $F('z01_telcel');
        oPessoa.z01_email = tagString($F('z01_email'));
        oPessoa.z01_telcon = $F('z01_telcon');
        oPessoa.z01_celcon = $F('z01_celcon');
        oPessoa.z01_fax = $F('z01_fax');
        oPessoa.z01_cxpostal = $F('z01_cxpostal');
        oPessoa.z01_cxposcon = $F('z01_cxposcon');
        oPessoa.z01_emailc = tagString($F('z01_emailc'));
        oPessoa.z01_cadast = js_formatar($F('z01_cadast'), 'd');


        if ($('z01_ultalt')) {
            oPessoa.z01_ultalt = js_formatar($F('z01_ultalt'), 'd');
        }
        oPessoa.z01_nomecomple = tagString($F('z01_nomecomple'));
        oPessoa.z01_nomefanta = tagString($F('z01_nomefanta'));
        oPessoa.z01_obs = $F('z01_obs');


        oPessoa.z01_nacion = $F('z01_nacion');


        oPessoa.z09_documento = $F('z09_documento');
        oPessoa.z09_cidade = $F('z09_cidade');
        oPessoa.z09_pais = $F('z09_pais');



        var oEndereco = {};
        oEndereco.idEndPrimario = $F('idEnderPrimario');
        oEndereco.idEndSecundario = $F('idEnderSecundario');

        var oTipoEmpresa = {};
        oTipoEmpresa.iTipoEmpresa = $F('z03_tipoempresa');

        var oNire = {};
        oNire.z08_nire = $F('z08_nire');

        var oCidadao = {};
        oCidadao.ov02_sequencial = $F('ov02_sequencial');
        oCidadao.ov02_seq = $F('ov02_seq');

        oIncluir.pessoa = {};
        oIncluir.pessoa = oPessoa;

        oIncluir.endereco = {};
        oIncluir.endereco = oEndereco;

        oIncluir.tipoEmpresa = {};
        oIncluir.tipoEmpresa = oTipoEmpresa;

        oIncluir.nire = {};
        oIncluir.nire = oNire;

        oIncluir.cidadao = {};
        oIncluir.cidadao = oCidadao;

        js_AjaxCgm(oIncluir, js_retornoIncluirJuridica);
    }

    function incluirAlterarCgm() {
        if (lPessoaFisica == true) {
            incluirAlterarPessoaFisica();
            return;
        }

        incluirAlterarPessoaJuridica();
    }

    function js_sendForm() {
        const btnSubmit = $('btnSubmit').value;

        switch (btnSubmit) {
            case 'Incluir':
            case 'Alterar':
                incluirAlterarCgm();
                return;
            case 'Excluir':
                excluirCgm();
                return;

        }
    }

    function js_retornoExcluirCgm(oAjax) {

        js_removeObj("msgBox");

        var oRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');

        if (oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            return false;
        } else if (oRetorno.status == 1) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            location.href = 'prot1_cadgeralmunic006.php';
            return false;
        }

    }

    function js_AjaxCgm(oSend, jsRetorno) {

        var msgDiv = "Aguarde ...";
        js_divCarregando(msgDiv, 'msgBox');

        var sUrlRpc = "prot1_cadgeralmunic.RPC.php";

        var oAjax = new Ajax.Request(
            sUrlRpc, {
                parameters: 'json=' + Object.toJSON(oSend),
                method: 'post',
                onComplete: jsRetorno
            }
        );
    }

    function js_retornoIncluirFisica(oAjax) {
        js_removeObj("msgBox");

        var oRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');

        if ($F('flag-inclusao-processo')) {
            if (oRetorno.status == '2') {
                return alert(oRetorno.message.urlDecode());
            } else {
                alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));

                if (parent.document.getElementById('alterou')) {
                    parent.document.getElementById('alterou').value = 1;
                }

                parent.document.getElementById('p58_numcgm').value = oRetorno.z01_numcgm;
                parent.document.getElementById('z01_nome').value = oRetorno.z01_nome;

                if (parent.iframe_cadastrocgm) {
                    parent.iframe_cadastrocgm.hide();
                }

                if (parent.iframe_cgm) {
                    parent.iframe_cgm.hide();
                }
            }

            return;
        }


        if (oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
        } else if (oRetorno.status == 1) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            if (oRetorno.action == 'incluir') {
                if (funcaoRetorno != '' && funcaoRetorno != undefined) {

                    eval(funcaoRetorno + '(' + oRetorno.z01_numcgm + ');');

                } else {
                    location.href = 'prot1_cadgeralmunic005.php?chavepesquisa=' + oRetorno.z01_numcgm;
                }
            } else {

                if (funcaoRetorno != '' && funcaoRetorno != undefined) {
                    eval(funcaoRetorno + '(' + oRetorno.z01_numcgm + ');');

                } else {

                    location.href = 'prot1_cadgeralmunic005.php';
                }
            }

            return false;
        }

    }

    function js_retornoIncluirJuridica(oAjax) {
        js_removeObj("msgBox");

        var oRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');

        if ($F('flag-inclusao-processo')) {
            if (oRetorno.status == '2') {
                return alert(oRetorno.message.urlDecode());
            } else {
                alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));

                if (parent.document.getElementById('alterou')) {
                    parent.document.getElementById('alterou').value = 1;
                }


                parent.document.getElementById('p58_numcgm').value = oRetorno.z01_numcgm;
                parent.document.getElementById('z01_nome').value = oRetorno.z01_nome;

                if (parent.iframe_cadastrocgm) {
                    parent.iframe_cadastrocgm.hide();
                }

                if (parent.iframe_cgm) {
                    parent.iframe_cgm.hide();
                }
            }

            return;
        }

        if (oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            return false;
        } else if (oRetorno.status == 1) {
            alert(oRetorno.message.urlDecode().replace(sExpReg, '\n'));
            parent.location.href = document.referrer;
            parent.Jandb_iframe_altcgm.hide();
            if (oRetorno.action == 'incluir') {
                if (funcaoRetorno != '' && funcaoRetorno != undefined) {
                    eval(funcaoRetorno + '(' + oRetorno.z01_numcgm + ');');

                } else {
                    location.href = 'prot1_cadgeralmunic005.php?chavepesquisa=' + oRetorno.z01_numcgm;
                }
            } else {


                if (funcaoRetorno != '' && funcaoRetorno != undefined) {

                    eval(funcaoRetorno + '(' + oRetorno.z01_numcgm + ');');

                } else {

                    location.href = 'prot1_cadgeralmunic005.php';
                }
            }
            return false;
        }

    }

    function js_validaEmail(email) {

        var email = email;
        var expReg0 = new RegExp("[A-Za-z0-9_.-]+@([A-Za-z0-9_]+\.)+[A-Za-z]{2,4}");
        var expReg1 = new RegExp("[!#$%*<>,:;?°ºª~/|]");

        if (email.match(expReg1) != null || email.indexOf('\\') != -1 || email.indexOf(' ') != -1) {
            alert('Usuário:\n\nEmail informado não é válido ou esta vazio!\n\n exemplo de email: xxx@xx.xx\n\n Email pode conter: \n  letras, números, hifen(-), sublinhado _\n\n Email não pode conter:\n  caracteres especiais, virgula(,), ponto e virgula (;), dois pontos (:) \n\nAdministrador:\n\n');
            return false;
        }

        if (email.match(expReg0) == null) {
            alert('Usuário:\n\nEmail informado não é válido ou esta vazio!\n\n exemplo de email: xxx@xx.xx\n\n Email pode conter: \n  letras, números, hifen(-), sublinhado _\n\n Email não pode conter:\n  caracteres especiais, virgula(,), ponto e virgula (;), dois pontos (:) \n\nAdministrador:\n\n');
            return false;
        }
        return true;

    }

    function js_messageBox(strMessageUsuario, strMessageAdministrador) {

        var strMessage = "usuário:";
        strMessage += "\n\n\t" + strMessageUsuario + "\n\n";
        strMessage += "administrador:";
        strMessage += "\n\n" + strMessageAdministrador + "\n\n";

        alert(strMessage);
    }

    function js_copiaNome() {

        if ($('z01_nomecomple')) {

            $('z01_nomecomple').value = $F('z01_nome');
        }
    }
    /*----------------------------Funções para manipular o cidadao----------------------*/

    function js_importaCadastroCidadao() {

        js_OpenJanelaIframe('', 'db_iframe_cidadao',
            'func_cidadaovinculos.php?funcao_js=parent.js_mostracidadao1|0|4&liberado=true&ativo=true&vinculocgm=false',
            'Pesquisa', true);

    }

    function js_MICidadao(ov02_sequencial, ov02_seq, ov03_numcgm) {

        var sQuery = "";
        sQuery += "importa=true";
        sQuery += "&ov02_sequencial=" + ov02_sequencial;
        sQuery += "&ov02_seq=" + ov02_seq;
        sQuery += "&ov03_numcgm=" + ov03_numcgm;
        js_OpenJanelaIframe('', 'db_iframe',
            'prot1_cidadaocgmdetalhe.php?' + sQuery,
            'Pesquisa', true);
    }

    function js_retornoAlteraCgmCidadao() {

        db_iframe.hide();
        js_findCgm($F('z01_numcgm'));
    }

    function js_vinculaCadastroCidadaoCGM() {

        js_OpenJanelaIframe('', 'db_iframe_cidadao',
            'func_cidadaovinculos.php?funcao_js=parent.js_vinculaCidadaoCGM|0|1&liberado=true&ativo=true&vinculocgm=false',
            'Pesquisa', true
        );

    }

    function js_vinculaCidadaoCGM(ov02_sequencial, ov02_seq) {

        db_iframe_cidadao.hide();

        var oVincular = new Object();

        oVincular.acao = 'vincular';
        oVincular.ov03_cidadao = ov02_sequencial;
        oVincular.ov03_seq = ov02_seq;
        oVincular.ov03_numcgm = $F('z01_numcgm');

        var sDados = Object.toJSON(oVincular);
        var msgDiv = 'Aguarde vinculando Cidadão ao CGM.....';
        js_divCarregando(msgDiv, 'msgBox');

        sUrl = 'ouv1_cidadao.RPC.php';
        var sQuery = 'dados=' + sDados;
        var oAjax = new Ajax.Request(sUrl, {
            method: 'post',
            parameters: sQuery,
            onComplete: js_retornoVincularDados
        });

    }

    function js_retornoVincularDados(oAjax) {

        js_removeObj("msgBox");

        var aRetorno = JSON.parse(oAjax.responseText);

        var sExpReg = new RegExp('\\\\n', 'g');

        alert(aRetorno.message.urlDecode().replace(sExpReg, '\n'));

        if (aRetorno.status == 0) {
            return false;
        } else if (aRetorno.status == 1) {

            var z01_numcgm = aRetorno.ov03_numcgm;
            location.href = 'prot1_cadgeralmunic005.php?chavepesquisa=' + z01_numcgm;
        }

    }
    /*----------------------------Fim Funções para manipular o cidadao----------------------*/

    function js_ExcluiEnderSecundario() {
        if ($F('endSecundario') != '') {
            if (confirm('usuário:\n\n Deseja excluir o endereço ?')) {
                $('idEnderSecundario').value = '';
                $('endSecundario').value = '';
                return false;
            } else {
                return false;
            }
        }
    }

    function js_ExcluiEnderPrimario() {
        if ($F('endPrimario') != '') {
            if (confirm('usuário:\n\n Deseja excluir o endereço ?\n\n')) {
                $('idEnderPrimario').value = '';
                $('endPrimario').value = '';
                return false;
            } else {
                return false;
            }
        }
    }

    function js_alteraMunicipio() {

        if ($F('idEnderPrimario') != '') {

            if (confirm('usuario:\n\nO endereço primário será excluído !\n\nDeseja continuar ?\n\n')) {

                $('idEnderPrimario').value = '';
                $('endPrimario').value = '';

            } else {

                if ($F('municipio') == 'f') {
                    $('municipio').value = 't';
                } else {
                    $('municipio').value = 'f';
                }
                return false;
            }
        }
    }

    function js_validaPis(pis) {

        if (pis != '') {

            if (!js_ChecaPIS(pis)) {

                alert("Pis inválido.Verifique.");
                document.form1.z01_pis.focus();
                document.form1.z01_pis.value = '';
                return (false);
            } else {
                return (true);
            }
        }
    }

    $('z01_nomecomple').addClassName('field-size-max');

    if (lPessoaFisica) {

        if ($('z09_documento')) {
            $('z09_documento').maxLength = 30;
        }

        if ($('z01_nacion')) {
            var oInputNacionalidade = $('z01_nacion');
            oInputNacionalidade.observe('change', verificaNacionalidade);
        }
    } else {

        var oInputNacionalidadeEmpresa = $('z01_nacion');
        oInputNacionalidadeEmpresa.observe('change', verificaNacionalidade);

    }

    function verificaNacionalidade() {

        if (lPessoaFisica) {

            document.getElementById('divEstrangeiro').style.display = 'none';
            if (oInputNacionalidade.value == '2') {
                document.getElementById('divEstrangeiro').style.display = '';
            }
        } else {

            document.getElementById('divEmpresaEstrangeira').style.display = 'none';
            if (oInputNacionalidadeEmpresa.value == '2') {
                document.getElementById('divEmpresaEstrangeira').style.display = '';
            }
        }
    }
</script>
