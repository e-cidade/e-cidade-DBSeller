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
$clrhpessoal->rotulo->label();
$clrhpesfgts->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("z04_nomesocial");
$clrotulo->label("rh06_descr");
$clrotulo->label("rh21_descr");
$clrotulo->label("rh08_descr");
$clrotulo->label("rh18_descr");
$clrotulo->label("rh37_descr");
$clrotulo->label("r70_descr");
$clrotulo->label("r59_descr");
$clrotulo->label("db90_descr");
$clrotulo->label("rh50_oid");
$clrotulo->label("rh116_descricao");
$clrotulo->label("rh164_datafim");
$clrotulo->label("rh01_registrapontoeletronico");
$clrotulo->label("rh252_residencia");
$clrotulo->label("rh252_condicao");
$clrotulo->label("rh164_assecuratoria");

$rh01_instit = db_getsession("DB_instit");
$mostra = 1;
$registparam = "";
if ($db_opcao == 1) {
    $result_parametros = $clrhcfpessmatr->sql_record(
        $clrhcfpessmatr->sql_query_file(null, "rh13_matricula", null, "rh13_instit=" . db_getsession("DB_instit"))
    );
    if ($clrhcfpessmatr->numrows > 0) {
        db_fieldsmemory($result_parametros, 0);
        if (trim($rh13_matricula)!="" && $rh13_matricula!=0) {
            $mostra = 3;
        }
    }
}

if ($db_opcao == 1) {
    $db_action="pes1_rhpessoal004.php";
} else if($db_opcao == 2 || $db_opcao == 22) {
    $mostra = 3;
    $db_action="pes1_rhpessoal005.php";
} else if($db_opcao == 3|| $db_opcao == 33) {
    $mostra = 3;
    $db_action = "pes1_rhpessoal006.php";
}

$optionDatasContratosEmergenciais = ($db_opcao == 1 ? $db_opcao : 3);
if (($db_opcao == 2 || $db_opcao == 22) && isset($rh01_regist) && $rh01_regist != "" ) {
    try{
        $clrhcontratoemergencialrenovacao = new cl_rhcontratoemergencialrenovacao;
        $sWhereContratoEmergencialRenovacoes = " rh163_matricula = {$rh01_regist}";
        $sSqlContratosEmergenciais = $clrhcontratoemergencialrenovacao->sql_query(null,"count(*) as totalcontratosemergenciaisparamatricula", null, $sWhereContratoEmergencialRenovacoes);
        $rsContratosEmergenciais = db_query($sSqlContratosEmergenciais);

        if (!$rsContratosEmergenciais) {
            throw new DbException("Error ao buscar total de contratos emergenciais para a matrícula");
        }

        if (pg_num_rows($rsContratosEmergenciais) > 0) {
            $totalContratosEmergenciaisParaMatricula = db_utils::fieldsMemory($rsContratosEmergenciais, 0)
                ->totalcontratosemergenciaisparamatricula;
        }

        if ($totalContratosEmergenciaisParaMatricula <= 1) {
            $optionDatasContratosEmergenciais = 2;
        }

    } catch (Exception $oErro) {
        $sMessageErro = $oErro->getMessage();
        echo "<script type=\"text/javascript\">alert(\"$sMessageErro\");</script>";
    }
}
?>
<form name="form1" method="post" action="<?=$db_action?>">
    <input type="hidden" name="rh01_funcao" value="<?=@$rh02_funcao?>">
    <table border='0'>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>DADOS PESSOAIS</b></legend>
                    <table>
                        <tr>
                            <td nowrap title="<?=@$Trh50_oid?>" rowspan="7" id='fotofunc'>
                                <?php
                                $regist = "";
                                if(isset($rh01_numcgm)){
                                    $regist = $rh01_numcgm;
                                }
                                db_foto($regist, $db_opcao, "js_alterafoto();");
                                ?>
                            </td>
                            <td nowrap title="<?=@$Trh01_regist?>">
                                <?=@$Lrh01_regist?>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('rhimp', 6, 0, true, 'hidden', 3, "");
                                db_input('rh01_regist', 6, $Irh01_regist, true, 'text', $mostra, "onchange='validaMatricula();'");
                                db_input('rh01_instit', 6, $Irh01_instit, true, 'hidden', 3, "");
                                db_input('registparam', 6, 0, true, 'hidden', 3, "");
                                db_input('localrecebefoto', 6, 0, true, 'hidden', 3, "");
                                ?>
                            </td>
                            <td nowrap title="<?=@$Trh01_sexo?>">
                                <?=@$Lrh01_sexo?>
                            </td>
                            <td nowrap>
                                <?php
                                $arr_sexo = ['M' => 'Masculino','F' => 'Feminino'];
                                db_select("rh01_sexo", $arr_sexo, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh01_numcgm?>">
                                <?php db_ancora(@$Lrh01_numcgm, "js_pesquisarh01_numcgm(true);", $db_opcao);?>
                            </td>
                            <td nowrap>
                                <?php
                                db_input(
                                    'rh01_numcgm',
                                    6,
                                    $Irh01_numcgm,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onchange='js_pesquisarh01_numcgm(false);' tabIndex='1'"
                                );
                                db_input('z01_nome', 33, $Iz01_nome, true, 'text', 3, '');
                                ?>
                                <input type='button' value='alterar' onclick="js_alterarCgm($F('rh01_numcgm'))">
                            </td>
                            <td nowrap title="<?=@$Trh01_raca?>">
                                <?php db_ancora(@$Lrh01_raca, "js_pesquisarh01_raca(true);", 3);?>
                            </td>
                            <td nowrap>
                                <?php
                                $result_raca = $clrhraca->sql_record($clrhraca->sql_query_file());
                                db_selectrecord("rh01_raca", $result_raca, "", $db_opcao, " tabIndex='2'");
                                ?>
                            </td>
                        </tr>
                        <?php if (isset($z04_nomesocial) && $z04_nomesocial != "") {?>
                        <tr>
                            <td nowrap title="<?=@$Tz04_nomesocial?>">
                                <?=@$Lz04_nomesocial?>
                            </td>
                            <td>
                                <?php db_input('z04_nomesocial', 42, $Iz04_nomesocial, true, 'text', 3, '');?>
                            </td>
                        </tr>
                        <?php }?>
                        <tr>
                            <td nowrap title="<?=@$Trh01_instru?>">
                                <?php db_ancora(@$Lrh01_instru, "js_pesquisarh01_instru(true);", 3);?>
                            </td>
                            <td nowrap>
                                <?php
                                $result_instru = $clrhinstrucao->sql_record($clrhinstrucao->sql_query_file());
                                db_selectrecord("rh01_instru", $result_instru, "", $db_opcao);
                                ?>
                            </td>
                            <td nowrap title="<?=@$Trh01_estciv?>">
                                <?php
                                db_ancora(@$Lrh01_estciv, "js_pesquisarh01_estciv(true);", 3);
                                ?>
                            </td>
                            <td nowrap>
                                <?php
                                $result_estciv = $clrhestcivil->sql_record($clrhestcivil->sql_query_file());
                                db_selectrecord("rh01_estciv", $result_estciv, "", $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh01_nacion?>">
                                <?php db_ancora(@$Lrh01_nacion, "js_pesquisarh01_nacion(true);", 3);?>
                            </td>
                            <td nowrap>
                                <?php
                                if (!isset($rh01_nacion)) {
                                    $rh01_nacion = 10;
                                }
                                $result_nacion = $clrhnacionalidade->sql_record(
                                    $clrhnacionalidade->sql_query_file(null, "*", "rh06_nacionalidade")
                                );
                                db_selectrecord(
                                    "rh01_nacion",
                                    $result_nacion,
                                    "",
                                    $db_opcao,
                                    "",
                                    "",
                                    "",
                                    "",
                                    "js_mudaanoche(this.value);"
                                );
                                ?>
                            </td>
                            <td nowrap title="<?=@$Trh01_anoche?>">
                                <?=@$Lrh01_anoche?>
                            </td>
                            <td nowrap>
                                <?php
                                $iMaxLen_rh01_anoche = 4;
                                db_input(
                                    'rh01_anoche',
                                    4,
                                    $Irh01_anoche,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "",
                                    "",
                                    "",
                                    "",
                                    $iMaxLen_rh01_anoche
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh01_natura?>">
                                <?=@$Lrh01_natura?>
                            </td>
                            <td nowrap>
                                <?php db_input('rh01_natura', 42, $Irh01_natura, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Trh01_nasc?>">
                                <?=@$Lrh01_nasc?>
                            </td>
                            <td nowrap>
                                <?php
                                db_inputdata(
                                    'rh01_nasc',
                                    @$rh01_nasc_dia,
                                    @$rh01_nasc_mes,
                                    @$rh01_nasc_ano,
                                    true,
                                    'text',
                                    $db_opcao,
                                    ""
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?php echo $Trh01_rhsindicato; ?>">
                                <?php db_ancora($Lrh01_rhsindicato, 'js_pesquisaSindicato()', $db_opcao);?>
                            </td>
                            <td nowrap colspan="3">
                                <?php db_input('rh01_rhsindicato', 10, null, true, 'hidden', 3);?>
                                <?php db_input('rh116_descricao', 42, $Irh116_descricao, true, 'text', 3);?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>DADOS ADMISSIONAIS</b></legend>
                    <center>
                        <table border="0">
                            <tr>
                                <td nowrap title="<?=@$Trh01_admiss?>">
                                    <?=@$Lrh01_admiss?>
                                </td>
                                <td colspan="2" nowrap>
                                    <?php
                                    db_inputdata(
                                        'rh01_admiss',
                                        @$rh01_admiss_dia,
                                        @$rh01_admiss_mes,
                                        @$rh01_admiss_ano,
                                        true,
                                        'text',
                                        $optionDatasContratosEmergenciais,
                                        ""
                                    );
                                    ?>
                                </td>
                                <td nowrap title="<?=@$Trh01_tipadm?>" align="right">
                                    <?=@$Lrh01_tipadm?>
                                </td>
                                <td colspan="2" nowrap>
                                    <?php
                                    $h01_tipadm = [
                                        1 => 'Admissao do 1o emprego',
                                        2 => 'Admissao c/ emprego anterior',
                                        3 => 'Transf de empreg s/ onus p/ a cedente',
                                        4 => 'Transf de empreg c/ onus p/ a cedente'
                                    ];
                                    db_select("rh01_tipadm", $h01_tipadm, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh01_vale?>" align="left">
                                    <?=@$Lrh01_vale?>
                                </td>
                                <td colspan="2" nowrap>
                                    <?php
                                    $clrotulo->label("rh08_descr");
                                    $arr_vale = ['S' => 'Sim', 'N'=>'Não'];
                                    db_select("rh01_vale", $arr_vale, true, $db_opcao, "");
                                    ?>
                                </td>
                                <td nowrap title="<?=@$Trh01_ponto?>" align="right">
                                    <?=@$Lrh01_ponto?>
                                </td>
                                <td nowrap>
                                    <?php db_input('rh01_ponto', 6, $Irh01_ponto, true, 'text', $db_opcao, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh01_clas1?>" align="left">
                                    <?=@$Lrh01_clas1?>
                                </td>
                                <td nowrap colspan="2">
                                    <?php db_input('rh01_clas1', 6, $Irh01_clas1, true, 'text', $db_opcao, "");?>
                                </td>
                                <td nowrap title="<?=@$Trh01_clas2?>" align="right">
                                <?=@$Lrh01_clas2?>
                                </td>
                                <td nowrap>
                                    <?php
                                    db_inputdata('rh01_clas2',@$rh01_clas2_dia,@$rh01_clas2_mes,@$rh01_clas2_ano,true,'text',$db_opcao,"")
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Trh01_trienio?>">
                                    <b>Triênio/Quinquênio:</b>
                                </td>
                                <td nowrap>
                                    <?php
                                    db_inputdata(
                                        'rh01_trienio',
                                        @$rh01_trienio_dia,
                                        @$rh01_trienio_mes,
                                        @$rh01_trienio_ano,
                                        true,
                                        'text',
                                        $db_opcao,
                                        ""
                                    );
                                    ?>
                                </td>
                                <td></td>
                                <td nowrap title="<?=@$Trh01_progres?>" align="right">
                                    <?=@$Lrh01_progres;?>
                                </td>
                                <td nowrap>
                                    <?php
                                    db_inputdata(
                                        'rh01_progres',
                                        @$rh01_progres_dia,
                                        @$rh01_progres_mes,
                                        @$rh01_progres_ano,
                                        true,
                                        'text',
                                        $db_opcao,
                                        ""
                                    );
                                    ?>
                                </td>
                            </tr>
                            <!-- [Extensao Data Assentamentos] campos -->
                            <tr>
                                <td colspan="6">
                                <fieldset>
                                    <legend>
                                        <?= $Lrh01_observacao?>
                                    </legend>
                                    <?php
                                    db_textarea('rh01_observacao', 2, 10, $Irh01_observacao, true, 'text', $db_opcao);
                                    ?>
                                </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <fieldset class="fieldset-hr"></fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Imigrante" align="left">
                                    <strong>Imigrante:</strong>
                                </td>
                                <td colspan="2">
                                    <?php
                                    $imigrante = false;
                                    $visualizaImigrante = 'collapse';
                                    if (isset($rh252_condicao) && !empty($rh252_condicao)) {
                                        $imigrante = true;
                                        $visualizaImigrante = 'visible';
                                    }
                                    db_input(
                                        'imigrante',
                                        1,
                                        '',
                                        false,
                                        'checkbox',
                                        true,
                                        'onclick="js_validaImigrante(this)"'
                                    );
                                    ?>
                                </td>
                                <td colspan="4" title="<?=@$Trh252_residencia?>" id="labelImigranteResidencia" style="visibility: <?php echo $visualizaImigrante?>">
                                    <?php
                                    echo $Lrh252_residencia;
                                    $opcoesImigranteResidencia = \Imigrante::getDescricoesResidencia();
                                    db_select("rh252_residencia", $opcoesImigranteResidencia, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <tr id="labelImigranteCondicaoLabel" style="visibility: <?php echo $visualizaImigrante?>">
                                <td colspan="2"></td>
                                <td colspan="4">
                                    <div style="margin-left:5px;">
                                        <?php echo $Lrh252_condicao;?>
                                    </div>
                                </td>
                            </tr>
                            <tr id="labelImigranteCondicao" style="visibility: <?php echo $visualizaImigrante?>">
                                <td colspan="2"></td>
                                <td title="<?=@$Trh252_condicao?>" colspan="4">
                                    <div style="margin-left: 5px;">
                                        <?php
                                        $opcoesImigranteCondicao = \Imigrante::getDescricoesCondicoes();
                                        db_select("rh252_condicao", $opcoesImigranteCondicao, true, $db_opcao, "");
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Contrato Emergencial/Aprendiz" align="left">
                                <strong>Contrato Emergencial/Aprendiz:</strong>
                                </td>
                                <td >
                                    <?php
                                    db_input(
                                        'contratoEmergencial',
                                        1,
                                        '',
                                        true,
                                        'checkbox',
                                        $optionDatasContratosEmergenciais,
                                        'onclick="js_validaContratoEmergencial(this)"'
                                    );
                                    ?>
                                </td>
                                <td  nowrap title="<?php echo @$Trh164_assecuratoria;?>" id="labelAssecuratoria" style="visibility: <?=($visibilityContratoEmergencial == true ? 'visible' : 'hidden') ?>" align="right">
                                    <?php echo @$Lrh164_assecuratoria;?>
                                </td>
                                <td nowrap title="<php echo @$Trh164_assecuratoria?>" id="idAssecuratoria" style="visibility: <?=($visibilityContratoEmergencial == true ? 'visible' : 'hidden') ?>" >
                                    <?php
                                    $aOptions = ['N'=>'Não', 'S' => 'Sim'];
                                    db_select("rh164_assecuratoria", $aOptions, true, $db_opcao, "");
                                    ?>
                                </td>
                                <td nowrap title="<?=@$Trh164_datafim?>" id="labelTerminoContratoEmergencial" style="visibility: <?=($visibilityContratoEmergencial == true ? 'visible' : 'hidden') ?>" align="right">
                                    <?=@$Lrh164_datafim;?>
                                </td>
                                <td  nowrap title="<?=@$Trh164_datafim?>" id="terminoContratoEmergencial" style="visibility: <?=($visibilityContratoEmergencial == true ? 'visible' : 'hidden') ?>" >
                                    <?php
                                    db_inputdata(
                                        'rh164_datafim',
                                        @$rh164_datafim_dia,
                                        @$rh164_datafim_mes,
                                        @$rh164_datafim_ano,
                                        true,
                                        'text',
                                        $optionDatasContratosEmergenciais,
                                        ""
                                    );
                                    $hasContratoEmergencial = (isset($contratoEmergencial) && $contratoEmergencial) ? $contratoEmergencial : false;
                                    db_input('hasContratoEmergencial', 1, '', true, 'hidden', 1, '');
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <label for="rh01_registrapontoeletronico">
                                    <?=$Lrh01_registrapontoeletronico;?>
                                </label>
                                </td>
                                <td>
                                    <?php
                                    $aOptions = ['t' => 'Sim', 'f' => 'Não'];
                                    db_select("rh01_registrapontoeletronico", $aOptions, true, $db_opcao, "");
                                    ?>
                                </td>
                                <td>
                                    <input name="registraPontoEletronicoAtual" type="hidden" value="<?php echo !empty($rh01_registrapontoeletronico) ? $rh01_registrapontoeletronico : '' ?>" />
                                </td>
                                <td>
                                    <label for="dataRegistraPontoEletronico" class="bold">Data Referente ao Registro do Ponto Eletrônico:</label>
                                    <input id="dataRegistraPontoEletronico" name="dataRegistraPontoEletronico" type="text" value="" />
                                </td>
                                <td>
                                    <input id="historicaoRegistraPontoEletronico" type="button" value="Histórico de Alterações" disabled="disabled" />
                                </td>
                            </tr>
                        </table>
                    </center>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td align="center" nowrap>
                <fieldset>
                    <legend align="left"><b>FGTS</b></legend>
                    <table width="60%" border="0">
                        <tr>
                            <td nowrap title="<?=@$Trh15_data?>">
                                <?php db_ancora(@$Lrh15_data, "", 3);?>
                            </td>
                            <td colspan="3" nowrap>
                                <?php
                                db_inputdata(
                                    'rh15_data',
                                    @$rh15_data_dia,
                                    @$rh15_data_mes,
                                    @$rh15_data_ano,
                                    true,
                                    'text',
                                    $db_opcao,
                                    ""
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh15_banco?>">
                                <?php db_ancora(@$Lrh15_banco, "js_pesquisarh15_banco(true);", $db_opcao);?>
                            </td>
                            <td colspan="3" nowrap>
                                <?php
                                db_input(
                                    'rh15_banco',
                                    5,
                                    $Irh15_banco,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onchange='js_pesquisarh15_banco(false);'"
                                );
                                ?>
                                <?php db_input('db90_descr', 40, $Idb90_descr, true, 'text', 3, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh15_agencia?>">
                                <?=@$Lrh15_agencia?>
                            </td>
                            <td nowrap>
                                <?php db_input('rh15_agencia', 5, $Irh15_agencia, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Trh15_agencia_d?>">
                                <?=@$Lrh15_agencia_d?>
                            </td>
                            <td nowrap>
                                <?php db_input('rh15_agencia_d', 1, $Irh15_agencia_d, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh15_contac?>">
                                <?=@$Lrh15_contac?>
                            </td>
                            <td nowrap>
                                <?php db_input('rh15_contac', 15, $Irh15_contac, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Trh15_contac_d?>">
                                <?=@$Lrh15_contac_d?>
                            </td>
                            <td nowrap>
                                <?php db_input('rh15_contac_d', 1, $Irh15_contac_d, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>
    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="return js_verificacampos();">
    <input name="pesquisar" type="button" id="pesquisar" value="<?=$db_opcao==1?"Importar":"Pesquisar"?>" onclick="js_pesquisa();">
</form>
<script>
    document.getElementById('rh01_observacao').style.width='100%';

    var opcao = '<?=$mostra;?>';
    new DBInputDate($('dataRegistraPontoEletronico'));

    function js_verificacampos(){

        <?php
        if ($db_opcao == 1 || $db_opcao == 2) {
        ?>
            if (document.form1.rh01_numcgm.value == "") {
                alert("Informe o CGM do funcionário.");
                document.form1.rh01_numcgm.focus();
                return false;
            }

            if ((document.form1.rh01_anoche.value.length < 4 && document.form1.rh01_nacion.value != 10) ||
                (document.form1.rh01_anoche.value == ""      && document.form1.rh01_nacion.value != 10) )
            {
                alert("Informe o ano de chegada com 04 caracteres.");
                document.form1.rh01_anoche.focus();
                return false;
            } else if (document.form1.rh01_nacion.value == 10) {
                document.form1.rh01_anoche.value = 0;
            }

            if (document.form1.rh01_anoche.value > document.form1.rh01_admiss_ano.value) {
                alert("O ano de chegada não pode ser maior que o ano de admissão.");
                document.form1.rh01_anoche.focus();
                return false;
            }

            if (document.form1.rh01_nasc_dia.value == ""
                || document.form1.rh01_nasc_mes.value == ""
                || document.form1.rh01_nasc_ano.value == ""
            ) {
                alert("Informe a data de nascimento.");
                document.form1.rh01_nasc_dia.focus();
                return false;
            }

            /* INICIO VALIDAÇÃO PARA E-SOCIAL  */
            var datanasc = new Date(document.form1.rh01_nasc_ano.value + '-' + document.form1.rh01_nasc_mes.value + '-' + document.form1.rh01_nasc_dia.value);
            var datamin  = new Date('1890-01-01');

            if (datanasc < datamin) {
                alert('Data de nascimento inválida');
                return false;
            }
            /* FINAL VALIDAÇÃO PARA E-SOCIAL  */

            if(document.form1.rh01_admiss_dia.value == ""
                || document.form1.rh01_admiss_mes.value == ""
                || document.form1.rh01_admiss_ano.value == ""
            ) {
                alert("Informe a data de admissão.");
                document.form1.rh01_admiss_dia.focus();
                return false;
            }

            if (document.form1.rh01_rhsindicato.value == "") {
                alert("Informe o sindicato.");
                document.form1.rh01_admiss_dia.focus();
                return false;
            }

            <?php
            if($db_opcao == 1){
            ?>
                if (!js_validaTerminoContratoEmergencial()) {
                    return false;
                }
            <?php
            }
            ?>
            return true;
        <?php
        }
        ?>
    }

    function js_mudaanoche(valor) {
        if (valor != 10) {
            document.form1.rh01_anoche.style.backgroundColor = '';
            document.form1.rh01_anoche.readOnly = false;
        }else{
            document.form1.rh01_anoche.style.backgroundColor = '#DEB887';
            document.form1.rh01_anoche.readOnly = true;
            document.form1.rh01_anoche.value = "0";
        }
    }

    function js_alterafoto() {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_localfoto','func_localfoto.php','Foto do funcionário',true,0);
    }

    function js_pesquisarh15_banco(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_db_bancos','func_db_bancos.php?funcao_js=parent.js_mostrabancos1|db90_codban|db90_descr','Pesquisa',true,0);
        } else {
            if (document.form1.rh15_banco.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_db_bancos','func_db_bancos.php?pesquisa_chave='+document.form1.rh15_banco.value+'&funcao_js=parent.js_mostrabancos','Pesquisa',false,0);
            } else {
                document.form1.db90_descr.value = '';
            }
        }
    }

    function js_mostrabancos(chave, erro) {
        document.form1.db90_descr.value = chave;
        if(erro==true){
            document.form1.rh15_banco.focus();
            document.form1.rh15_banco.value = '';
        }
    }

    function js_mostrabancos1(chave1, chave2) {
        document.form1.rh15_banco.value = chave1;
        document.form1.db90_descr.value = chave2;
        db_iframe_db_bancos.hide();
    }

    function js_pesquisarh01_numcgm(mostra){
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','func_nome','func_nome.php?campos='+'cgm.z01_numcgm\, z01_nome\,trim\(z01_cgccpf\) as z01_cgccpf\, trim\(z01_ender\) as z01_ender\, z01_munic\, z01_uf\, z01_cep\, z01_email\,z01_sexo\,z01_nasc\,z04_nomesocial'+'&filtro=1&testanome=true&funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome|z01_sexo|z01_nasc|z04_nomesocial|z01_estciv|z01_escolaridade|z01_naturalidade','Pesquisa',true,'0');
        } else {
            if (document.form1.rh01_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','func_nome','func_nome.php?novosvalores=|z01_numcgm|z01_nome|z01_sexo|z01_nasc|z04_nomesocial|z01_estciv|z01_escolaridade|z01_naturalidade&testanome=true&filtro=1&pesquisa_chave='+document.form1.rh01_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false,'0');
            } else {
                document.form1.z01_nome.value = '';
                document.form1.z04_nomesocial.value = '';
            }
        }
    }

    function js_mostracgm(erro, chave1, chave2, chave3, chave4, chave5,chave6, chave7, chave8) {
        if (!validaCGM(chave1)) {
            return false;
        }

        document.form1.rh01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        if (chave3 != "") {
            document.form1.rh01_sexo.value = chave3;
        }
        if (chave4 != "") {
            $('rh01_nasc_dia').setValue(chave4.substr(8,2));
            $('rh01_nasc_mes').setValue(chave4.substr(5,2));
            $('rh01_nasc_ano').setValue(chave4.substr(0,4));
            $('rh01_nasc').setValue($F('rh01_nasc_dia') + "/" + $F('rh01_nasc_mes') + "/" + $F('rh01_nasc_ano'));
        }
        if (erro==true) {
            document.form1.rh01_numcgm.focus();
            document.form1.rh01_numcgm.value = '';
        }


        if (chave6 != "") {
            document.form1.rh01_estciv.value = chave6;
        }
        if (chave7 != "") {
            escolaridadeServidor = deParaEscolaridade(chave7);
            document.form1.rh01_instru.value = escolaridadeServidor;
            document,form1.rh01_instrudescr.value = escolaridadeServidor;
        }

        if (chave8 != "") {
            document.form1.rh01_natura.value = chave8;
        }

        if (chave5 != "") {
            document.form1.z04_nomesocial.value = chave5;
        }
    }

    function js_mostracgm1(chave1,chave2,chave3,chave4, chave5, chave6, chave7, chave8) {
        if (!validaCGM(chave1)) {
            return false;
        }
        document.form1.rh01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        if (chave3 != "") {
            document.form1.rh01_sexo.value = chave3;
        }
        if (chave4 != "") {
            $('rh01_nasc_dia').setValue(chave4.substr(8,2));
            $('rh01_nasc_mes').setValue(chave4.substr(5,2));
            $('rh01_nasc_ano').setValue(chave4.substr(0,4));
            $('rh01_nasc').setValue($F('rh01_nasc_dia') + "/" + $F('rh01_nasc_mes') + "/" + $F('rh01_nasc_ano'));
        }


        if (chave6 != "") {
            document.form1.rh01_estciv.value = chave6;
        }

        if (chave7 != "") {
            escolaridadeServidor = deParaEscolaridade(chave7);
            document.form1.rh01_instru.value = escolaridadeServidor;
            document,form1.rh01_instrudescr.value = escolaridadeServidor;
        }

        if (chave8 != "") {
            document.form1.rh01_natura.value = chave8;
        }


        if (chave5 != "") {
            document.form1.z04_nomesocial.value = chave5;
        }

        func_nome.hide();
    }

    function validaCGM(iCgm) {
        var sUrlRPC = "rhpessoalmov001.RPC.php";
        var oParam = new Object();
        oParam.acao = "buscaMatricula";
        oParam.iCgm = iCgm;
        var lRetorno = true;

        js_divCarregando('Aguarde, validando o CGM ...','msgBox');
        var oAjax = new Ajax.Request(
            sUrlRPC,
            {
                method:'post',
                asynchronous:false,
                parameters:'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {
                    var oRetorno = JSON.parse(oAjax.responseText);
                    var aMatriculas = oRetorno.matriculas;
                    js_removeObj('msgBox');

                    if (aMatriculas.length > 0) {
                        var sMsg  = "Já existe uma matrícula cadastrada para este CGM.";
                            sMsg += "Deseja mesmo continuar a operação?";

                        if (!confirm(sMsg)) {
                            document.form1.rh01_numcgm.value = "";
                            document.form1.z01_nome.value = "";
                            document.form1.z04_nomesocial.value = "";
                            lRetorno = false;
                        }
                    }
                }
            }
        );
        return lRetorno;
    }

    function validaMatricula(matricula) {
        var lRetorno = true;
        if (document.form1.rh01_regist.value != '') {
            var sUrlRPC = "rhpessoalmov001.RPC.php";
            var oParam = new Object();
            oParam.acao = "buscaMatricula";
            oParam.matricula = document.form1.rh01_regist.value;
            js_divCarregando('Aguarde, validando a Matrícula...','msgBox');
            var oAjax = new Ajax.Request(
                sUrlRPC,
                {
                    method:'post',
                    asynchronous:false,
                    parameters:'json=' + Object.toJSON(oParam),
                    onComplete: function (oAjax) {
                        var oRetorno = JSON.parse(oAjax.responseText);
                        var aMatriculas = oRetorno.matriculas;
                        js_removeObj('msgBox');

                        if (aMatriculas.length > 0) {
                            var sMsg  = "Já existe uma matrícula cadastrada para este código.";
                                sMsg += "Deseja mesmo continuar a operação?";

                            if (!confirm(sMsg)) {
                                document.form1.rh01_regist.value = "";
                                lRetorno = false;
                            }
                        }
                    }
                }
            );
        }
        return lRetorno;
    }

    function js_pesquisarh01_nacion(mostra) {
        if (mostra==true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhnacionalidade','func_rhnacionalidade.php?funcao_js=parent.js_mostrarhnacionalidade1|rh06_nacionalidade|rh06_descr','Pesquisa',true,'0');
        } else {
            if (document.form1.rh01_nacion.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhnacionalidade','func_rhnacionalidade.php?pesquisa_chave='+document.form1.rh01_nacion.value+'&funcao_js=parent.js_mostrarhnacionalidade','Pesquisa',false,'0');
            }else{
                document.form1.rh06_descr.value = '';
                js_mudaanoche();
            }
        }
    }

    function js_mostrarhnacionalidade(chave, erro) {
        document.form1.rh06_descr.value = chave;
        js_mudaanoche(document.form1.rh01_nacion.value);
        if (erro == true) {
            document.form1.rh01_nacion.focus();
            document.form1.rh01_nacion.value = '';
        }
    }

    function js_mostrarhnacionalidade1(chave1, chave2) {
        document.form1.rh01_nacion.value = chave1;
        document.form1.rh06_descr.value = chave2;
        db_iframe_rhnacionalidade.hide();
        js_mudaanoche(document.form1.rh01_nacion.value);
    }

    function js_pesquisarh01_instru(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhinstrucao','func_rhinstrucao.php?funcao_js=parent.js_mostrarhinstrucao1|rh21_instru|rh21_descr','Pesquisa',true,'0');
        } else {
            if (document.form1.rh01_instru.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhinstrucao','func_rhinstrucao.php?pesquisa_chave='+document.form1.rh01_instru.value+'&funcao_js=parent.js_mostrarhinstrucao','Pesquisa',false,'0');
            } else {
                document.form1.rh21_descr.value = '';
            }
        }
    }

    function js_mostrarhinstrucao(chave, erro) {
        document.form1.rh21_descr.value = chave;
        if (erro == true) {
            document.form1.rh01_instru.focus();
            document.form1.rh01_instru.value = '';
        }
    }

    function js_mostrarhinstrucao1(chave1, chave2) {
        document.form1.rh01_instru.value = chave1;
        document.form1.rh21_descr.value = chave2;
        db_iframe_rhinstrucao.hide();
    }

    function js_pesquisarh01_estciv(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhestcivil','func_rhestcivil.php?funcao_js=parent.js_mostrarhestcivil1|rh08_estciv|rh08_descr','Pesquisa',true,'0');
        } else {
            if (document.form1.rh01_estciv.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhestcivil','func_rhestcivil.php?pesquisa_chave='+document.form1.rh01_estciv.value+'&funcao_js=parent.js_mostrarhestcivil','Pesquisa',false,'0');
            } else {
                document.form1.rh08_descr.value = '';
            }
        }
    }

    function js_mostrarhestcivil(chave, erro) {
        document.form1.rh08_descr.value = chave;
        if (erro == true) {
            document.form1.rh01_estciv.focus();
            document.form1.rh01_estciv.value = '';
        }
    }

    function js_mostrarhestcivil1(chave1, chave2) {
        document.form1.rh01_estciv.value = chave1;
        document.form1.rh08_descr.value = chave2;
        db_iframe_rhestcivil.hide();
    }

    function js_pesquisarh01_raca(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhraca','func_rhraca.php?funcao_js=parent.js_mostrarhraca1|rh18_raca|rh18_descr','Pesquisa',true,'0');
        } else {
            if (document.form1.rh01_raca.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhraca','func_rhraca.php?pesquisa_chave='+document.form1.rh01_raca.value+'&funcao_js=parent.js_mostrarhraca','Pesquisa',false,'0');
            } else {
                document.form1.rh18_descr.value = '';
            }
        }
    }

    function js_mostrarhraca(chave, erro) {
        document.form1.rh18_descr.value = chave;
        if (erro == true) {
            document.form1.rh01_raca.focus();
            document.form1.rh01_raca.value = '';
        }
    }

    function js_mostrarhraca1(chave1, chave2) {
        document.form1.rh01_raca.value = chave1;
        document.form1.rh18_descr.value = chave2;
        db_iframe_rhraca.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpessoal','db_iframe_rhpessoal','func_rhpessoal.php?<?=($db_opcao==2 || $db_opcao == 22 ? "testarescisao=ra&" : "")?>funcao_js=parent.js_preenchepesquisa|rh01_regist&instit=<?=db_getsession("DB_instit")?>','Pesquisa',true,0);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_rhpessoal.hide();
        <?php echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";?>
    }

    <?php
    if(isset($rh01_nacion) && trim($rh01_nacion) != "") {
        echo "js_mudaanoche('$rh01_nacion');\n";
    }
    ?>

    function js_alterarCgm(iCgm) {
        if (iCgm != "") {
            js_OpenJanelaIframe(
                '',
                'db_iframe_novocgm',
                'prot1_cadgeralmunic005.php?chavepesquisa=' + iCgm +
                    '&testanome=false&lCpf=true&execfunction=parent.CurrentWindow.corpo.iframe_rhpessoal.teste',
                'Novo CGM',
                true,
                '0'
            );
        }
    }

    function teste(iCgm) {
        db_iframe_novocgm.hide();
        $('rh01_numcgm').value = iCgm;
        js_pesquisarh01_numcgm(false);
    }

    function js_pesquisaSindicato() {
        js_OpenJanelaIframe('',
            'db_iframe_rhsindicato',
            'func_rhsindicato.php?funcao_js=parent.js_retornoPesquisaSindicato|rh116_descricao|rh116_sequencial',
            'Pesquisa',
            true,
            '0'
        );
    }

    function js_retornoPesquisaSindicato(sDescricao, iSindicato) {
        document.getElementById('rh01_rhsindicato').value = iSindicato;
        document.getElementById('rh116_descricao').value = sDescricao;
        db_iframe_rhsindicato.hide();
    }

    function js_validaContratoEmergencial(checkbox) {
        if (checkbox.readOnly) {
            return true;
        }

        if (checkbox.checked) {
            document.getElementById('labelTerminoContratoEmergencial').style.visibility = 'visible';
            document.getElementById('terminoContratoEmergencial').style.visibility = 'visible';
            checkbox.value = 't';
            document.getElementById('labelAssecuratoria').style.visibility = 'visible';
            document.getElementById('idAssecuratoria').style.visibility = 'visible';
        } else {
            document.getElementById('labelTerminoContratoEmergencial').style.visibility = 'hidden';
            document.getElementById('terminoContratoEmergencial').style.visibility = 'hidden';
            document.getElementById('labelAssecuratoria').style.visibility = 'hidden';
            document.getElementById('idAssecuratoria').style.visibility = 'hidden';
            document.getElementById('hasContratoEmergencial').value = 'f';
            document.form1.rh164_datafim.value = '';
            document.form1.rh164_datafim_dia.value = '';
            document.form1.rh164_datafim_mes.value = '';
            document.form1.rh164_datafim_ano.value = '';
            document.form1.rh164_assecuratoria.value = '';
        }
    }

    function js_validaImigrante(checkbox) {
        if (checkbox.readOnly) {
            return true;
        }

        if (checkbox.checked) {
            document.getElementById('labelImigranteResidencia').style.visibility = 'visible';
            document.getElementById('labelImigranteCondicao').style.visibility = 'visible';
            document.getElementById('labelImigranteCondicaoLabel').style.visibility = 'visible';
            checkbox.value = 't';
        } else {
            document.getElementById('labelImigranteResidencia').style.visibility = 'hidden';
            document.getElementById('labelImigranteCondicao').style.visibility = 'hidden';
            document.getElementById('labelImigranteCondicaoLabel').style.visibility = 'hidden';
        }
    }

    function js_validaTerminoContratoEmergencial() {
        var sDataAdmissao = document.getElementById('rh01_admiss').value;
        var sDataTermino = document.getElementById('rh164_datafim').value;

        if (document.getElementById('contratoEmergencial').checked) {
            if (sDataTermino.trim() == '') {
                alert("Preencha a data de término do contrato emergencial.");
                return false;
            }

            sDataAdmissao = getDateInDatabaseFormat(sDataAdmissao);
            sDataTermino = getDateInDatabaseFormat(sDataTermino);

            if (sDataTermino <= sDataAdmissao) {
                alert("A data de término deve ser maior que a data de admissão.");
                return false;
            }
        }
        return true;
    }

    if (opcao === '3') {
        $('historicaoRegistraPontoEletronico').removeAttribute('disabled');
    }

    function historicoAlteracoes() {
        var content = "<div id='ctnGridHistorico'></div>";

        var windowHistorico = new windowAux('windowHistorico', 'Histórico de Alterações', 600, 400);
        windowHistorico.setContent(content);
        windowHistorico.setShutDownFunction(function() {
            windowHistorico.destroy();
        });

        new DBMessageBoard(
            'messageHistorico',
            'Alterações referentes a opção Registra Ponto Eletrônico',
            '',
            windowHistorico.getContentContainer()
        );

        var parametros = {
            'executa': 'historicoRegistraPontoEletronico',
            'matricula': $F('rh01_regist')
        };

        new AjaxRequest(
            'pes4_servidor.RPC.php',
            parametros,
            function(retorno, erro) {
                if (erro) {
                    alert(retorno.mensagem);
                    return false;
                }

                if (empty(retorno.historicos)) {
                    alert('Sem histórico de alteração no registro de ponto eletrônico.');
                    return false;
                }

                windowHistorico.show(100, 100);

                var collectionHistorico = new Collection();
                collectionHistorico.setId('sequencial');

                var gridHistorico = new DatagridCollection(collectionHistorico).configure({'height': '220px'});
                gridHistorico.addColumn('sequencial', {'label': 'Código'});
                gridHistorico.addColumn('data', {'label': 'Data da Alteração', 'align': 'center'});
                gridHistorico.addColumn('registraPonto', {'label': 'Registra Ponto Eletrônico', 'align': 'center'});
                gridHistorico.hideColumns([0]);
                gridHistorico.show($('ctnGridHistorico'));

                retorno.historicos.each(function(historico) {
                    collectionHistorico.add(historico);
                });

                gridHistorico.reload();
        }).execute();
    }

    $('historicaoRegistraPontoEletronico').observe('click', historicoAlteracoes);

    let instituicao = "<?= $rh01_instit;?>";
    let matriculaS = "";
    <?php if (!empty($rh01_regist)) {?>
        matriculaS = "<?= $rh01_regist;?>";
    <?php } else { ?>
        localStorage.removeItem('matricula');
        localStorage.removeItem('cgm');
        localStorage.removeItem('servidor');
    <?php } ?>
    let nomeServidor = document.form1.z01_nome.value
    let cgm = document.form1.rh01_numcgm.value
    const instituicaoObj = JSON.stringify(instituicao);
    const matriculaObj = JSON.stringify(matriculaS);
    const cgmObj = JSON.stringify(cgm);
    const servObj = JSON.stringify(nomeServidor)
    localStorage.setItem('instituicao', instituicaoObj);
    localStorage.setItem('matricula', matriculaObj);
    localStorage.setItem('cgm', cgmObj);
    localStorage.setItem('servidor', servObj);

    function validaRaca() {
        let codigoRaca = document.getElementById("rh01_raca").value;
        if (codigoRaca !== "") {
            let codigosValidos = ['1', '2', '4', '6', '8'];
            let teste = codigosValidos.includes(codigoRaca);
            if (teste == false) {
                alert("O campo raça/cor deve ser informado.");
            }
        }
    }

    function deParaEscolaridade(escolaridadeCgm) {
        escolaridadeCgmServidor =  {
                0: 0,
                1: 1,
                2: 4,
                3: 5,
                4: 6,
                5: 7,
                6: 8,
                7: 9,
                8: 10,
                9:11
        };
        return escolaridadeCgmServidor[escolaridadeCgm];
    }

    document.getElementById("rh01_racadescr").onchange = function() {
        js_ProcCod_rh01_raca('rh01_racadescr','rh01_raca');
        validaRaca();
    }

    document.getElementById("rh01_raca").onchange = function() {
        js_ProcCod_rh01_raca('rh01_raca','rh01_racadescr');
        validaRaca();
    }

    (function () {
        validaRaca();
    });

</script>
