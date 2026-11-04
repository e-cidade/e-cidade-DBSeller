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

require_once(modification("libs/db_libdicionario.php"));
require_once(modification("libs/db_utils.php"));

//MODULO: Acordos

$clacordo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ac17_sequencial");
$clrotulo->label("descrdepto");
$clrotulo->label("ac02_sequencial");
$clrotulo->label("ac08_descricao");
$clrotulo->label("ac50_descricao");
$clrotulo->label("z01_nome");

if ($db_opcao == 1) {
    $db_action = "aco1_acordo004.php";
} else if ($db_opcao == 2 || $db_opcao == 22) {
    $db_action = "aco1_acordo005.php";
} else if ($db_opcao == 3 || $db_opcao == 33) {
    $db_action = "aco1_acordo006.php";
}

$aClassificacao = array();
$oDaoAcordoClassificacao = new cl_acordoclassificacao();
$sSqlClassificacao = $oDaoAcordoClassificacao->sql_query_file();
$rsClassificacao = $oDaoAcordoClassificacao->sql_record($sSqlClassificacao);
if ($oDaoAcordoClassificacao->numrows > 0) {

    for ($iIndiceClassificacao = 0; $iIndiceClassificacao < $oDaoAcordoClassificacao->numrows; $iIndiceClassificacao++) {

        $oDadosClassificacao = db_utils::fieldsMemory($rsClassificacao, $iIndiceClassificacao);
        $aClassificacao[$oDadosClassificacao->ac46_sequencial] = $oDadosClassificacao->ac46_descricao;
    }

}

db_app::load("dbtextFieldData.widget.js");

$sTipoFiltro = isset($sTipoFiltro) ? $sTipoFiltro : '1';
$db_opcao_editavel = !empty($db_opcao_editavel) ? $db_opcao_editavel : $db_opcao;

$Tac16_sequencial = isset($Tac16_sequencial) ? $Tac16_sequencial : null;
$Lac16_sequencial = isset($Lac16_sequencial) ? $Lac16_sequencial : null;
$Tac16_origem = isset($Tac16_origem) ? $Tac16_origem : null;
$Lac16_origem = isset($Lac16_origem) ? $Lac16_origem : null;
$Tac16_acordoclassificacao = isset($Tac16_acordoclassificacao) ? $Tac16_acordoclassificacao : null;
$Tac16_acordogrupo = isset($Tac16_acordogrupo) ? $Tac16_acordogrupo : null;
$Lac16_acordogrupo = isset($Lac16_acordogrupo) ? $Lac16_acordogrupo : null;
$Tac16_numero = isset($Tac16_numero) ? $Tac16_numero : null;
$Lac16_numero = isset($Lac16_numero) ? $Lac16_numero : null;
$Tac16_contratado = isset($Tac16_contratado) ? $Tac16_contratado : null;
$Lac16_contratado = isset($Lac16_contratado) ? $Lac16_contratado : null;
$Tac16_deptoresponsavel = isset($Tac16_deptoresponsavel) ? $Tac16_deptoresponsavel : null;
$Tac16_acordocomissao = isset($Tac16_acordocomissao) ? $Tac16_acordocomissao : null;
$Tac16_lei = isset($Tac16_lei) ? $Tac16_lei : null;
$Lac16_lei = isset($Lac16_lei) ? $Lac16_lei : null;
$Tac16_numeroprocesso = isset($Tac16_numeroprocesso) ? $Tac16_numeroprocesso : null;
$Lac16_numeroprocesso = isset($Lac16_numeroprocesso) ? $Lac16_numeroprocesso : null;
$Tac16_qtdrenovacao = isset($Tac16_qtdrenovacao) ? $Tac16_qtdrenovacao : null;
$Iac16_qtdrenovacao = isset($Iac16_qtdrenovacao) ? $Iac16_qtdrenovacao : null;
$Tac16_dataassinatura = isset($Tac16_dataassinatura) ? $Tac16_dataassinatura : null;
$Lac16_dataassinatura = isset($Lac16_dataassinatura) ? $Lac16_dataassinatura : null;
$ac16_dataassinatura_dia = isset($ac16_dataassinatura_dia) ? $ac16_dataassinatura_dia : null;
$ac16_dataassinatura_ano = isset($ac16_dataassinatura_ano) ? $ac16_dataassinatura_ano : null;
$ac16_dataassinatura_mes = isset($ac16_dataassinatura_mes) ? $ac16_dataassinatura_mes : null;
$Tac50_descricao = isset($Tac50_descricao) ? $Tac50_descricao : null;
$Tac50_descricao = isset($Tac50_descricao) ? $Tac50_descricao : null;
$ac16_datainicio_dia = isset($ac16_datainicio_dia) ? $ac16_datainicio_dia : null;
$ac16_datainicio_ano = isset($ac16_datainicio_ano) ? $ac16_datainicio_ano : null;
$ac16_datainicio_mes = isset($ac16_datainicio_mes) ? $ac16_datainicio_mes : null;
$ac16_datafim_dia = isset($ac16_datafim_dia) ? $ac16_datafim_dia : null;
$ac16_datafim_mes = isset($ac16_datafim_mes) ? $ac16_datafim_mes : null;
$ac16_datafim_ano = isset($ac16_datafim_ano) ? $ac16_datafim_ano : null;
$Iac16_qtdperiodo = isset($Iac16_qtdperiodo) ? $Iac16_qtdperiodo : null;
$Tac16_objeto = isset($Tac16_objeto) ? $Tac16_objeto : null;
$Lac16_objeto = isset($Lac16_objeto) ? $Lac16_objeto : null;
$Tac16_resumoobjeto = isset($Tac16_resumoobjeto) ? $Tac16_resumoobjeto : null;
$Lac16_resumoobjeto = isset($Lac16_resumoobjeto) ? $Lac16_resumoobjeto : null;
?>
<style>
    .fieldsetinterno {
        border: 0px;
        border-top: 2px groove white;
        border-bottom: 2px groove white;
    }

    td {
        white-space: nowrap
    }

    .table-vigencia td {
        width: 8%;
        white-space: nowrap
    }

    #ac02_descricao, #nomecontratado, #descrdepto, #ac08_descricao {
        width: 75%;
    }

    #ac16_objeto {
        width: 100%;
    }

    #DBAncoraProcSis {
        color: blue !important;
        text-decoration: underline !important;
    }
</style>
<form name="form1" method="post" action="<?= $db_action ?>">
    <center>
        <table border="0" cellspacing="0" cellpadding="0" width="36%">
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td>
                                    <fieldset style='border:0px'>
                                        <table border="0" cellpadding="0" width="100%">
                                            <tr>
                                                <td nowrap title="<?= $Tac16_sequencial ?>">
                                                    <?= $Lac16_sequencial ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_sequencial', 10, $Iac16_sequencial, true, 'text', 3, "");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_origem ?>">
                                                    <?= $Lac16_origem ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $aValores = array(
                                                        0 => 'Selecione',
                                                        1 => 'Processo de Compras',
                                                        2 => 'Licitação',
                                                        3 => 'Manual',
                                                    );
                                                    db_select('ac16_origem', $aValores, true, $db_opcao,
                                                        " onchange='js_desabilitaselecionar();js_exibeBotaoJulgamento();js_validaCampoValor();' style='width:100%;'");

                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td nowrap title="<?= $Tac16_acordoclassificacao ?>">
                                                    <strong>Classificação:</strong>
                                                </td>
                                                <td>
                                                    <?php

                                                    db_select('ac16_acordoclassificacao', $aClassificacao, true, $db_opcao,
                                                        " style='width:100%;'");
                                                    ?>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td nowrap title="<?= $Tac16_acordogrupo ?>">
                                                    <?php
                                                    db_ancora($Lac16_acordogrupo, "js_pesquisaac16_acordogrupo(true);", $db_opcao);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_acordogrupo', 10, $Iac16_acordogrupo, true, 'text', $db_opcao,
                                                        "onchange='js_pesquisaac16_acordogrupo(false);'");
                                                    db_input('ac02_descricao', 30, $Iac02_sequencial, true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_numero ?>">
                                                    <?= $Lac16_numero ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_numero', 10, $Iac16_numero, true, 'text', 1);
                                                    echo "/";
                                                    db_input('ac16_anousu', 5, $Iac16_anousu, true, 'text', 1);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_contratado ?>">
                                                    <?php
                                                    db_ancora($Lac16_contratado, "js_pesquisaac16_contratado(true);", $db_opcao);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_contratado', 10, $Iac16_contratado, true, 'text', $db_opcao,
                                                        "onchange='js_pesquisaac16_contratado(false);'");
                                                    db_input('nomecontratado', 30, $Iz01_nome, true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_deptoresponsavel ?>">
                                                    <?php
                                                    db_ancora("<b>Depto Responsável: </b>", "js_pesquisaac16_deptoresponsavel(true);",
                                                        $db_opcao);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_deptoresponsavel', 10, $Iac16_deptoresponsavel, true, 'text',
                                                        $db_opcao, "onchange='js_pesquisaac16_deptoresponsavel(false)';");
                                                    db_input('descrdepto', 30, $Idescrdepto, true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $cl_acordoparam = new cl_acordoparam;
                                            $iInstituicaoSessao = db_getsession('DB_instit');
                                            $sql = $cl_acordoparam->sql_query_file(null, "*", null, "ac59_instituicao = {$iInstituicaoSessao}");
                                            $retorno = pg_fetch_assoc($cl_acordoparam->sql_record($sql));
                                            $parametro_exibe_fiscal = $retorno['ac59_cadastrocomissoesfiscal'] === 't' ? true : false; ?>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_acordocomissao ?>">
                                                    <?php
                                                    db_ancora('<b>Comissão:</b>', "onchange=js_pesquisaac16_acordocomissao(true)",
                                                        $db_opcao);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_acordocomissao', 10, $Iac16_acordocomissao, true, 'text', $db_opcao,
                                                        "onchange=js_pesquisaac16_acordocomissao(false)");
                                                    db_input('ac08_descricao', 30, $Iac08_descricao, true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            if ($parametro_exibe_fiscal): ?>
                                                <tr>
                                                    <td nowrap title="Gestor">
                                                        <?php
                                                        db_ancora('<b>Gestor:</b>', "onchange=js_pesquisa_gestor()",
                                                            $db_opcao);
                                                        ?>
                                                    </td>
                                                    <?php
                                                    if ($gestorcgm) {
                                                        $clcgm = new cl_cgm;
                                                        $sql_cgm = $clcgm->sql_query($gestorcgm, "z01_nome, z01_numcgm");
                                                        $rs_cgm = db_query($sql_cgm);
                                                        db_fieldsmemory($rs_cgm, 0);
                                                        $z01_nome_gestor = $z01_nome;
                                                        $z01_numcgm_gestor = $z01_numcgm;
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php
                                                        db_input('z01_nome_gestor', 30, $Iz01_nome, true, 'text', 3);
                                                        db_input('z01_numcgm_gestor', 30, "", true, 'text', 3);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td nowrap title="<?= $Tz01_numcgm ?>">
                                                        <?php
                                                        db_ancora('<b>Fiscal:</b>', "onchange=js_pesquisaz01_numcgm(true)",
                                                            $db_opcao);
                                                        ?>
                                                    </td>
                                                    <?php
                                                    if ($fiscalcgm) {
                                                        $clcgm = new cl_cgm;
                                                        $clrh = new cl_rhpessoal;
                                                        $cgm = implode(', ', $fiscalcgm);
                                                        $sql_cgm = $clcgm->sql_query("","z01_nome, z01_cgccpf, z01_numcgm", "", "z01_numcgm in ($cgm)");
                                                        $sql_rh = $clrh->sql_query("", "rh01_regist, rh01_numcgm", null, "rh01_numcgm in ($cgm)");
                                                        $rs_cgm = db_query($sql_cgm);
                                                        $rs_rh = db_query($sql_rh);
                                                        for ($i = 0; $i < pg_num_rows($rs_cgm); $i++) {
                                                            $cgm = db_utils::fieldsMemory($rs_cgm, $i);
                                                            $cgm->z01_nome = utf8_encode($cgm->z01_nome);
                                                            $array_cgm[] = $cgm;

                                                            $rh = db_utils::fieldsMemory($rs_rh, $i);
                                                            $array_rh[] = $rh;
                                                        }
                                                        $array_cgm_encode = json_encode($array_cgm);
                                                        $array_rh_encode = json_encode($array_rh);
                                                    }
                                                    ?>
                                                    <td>
                                                        <div id="gridFiscal"></div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr id="trLei">
                                                <td nowrap title="<?= $Tac16_lei ?>">
                                                    <?= $Lac16_lei ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_lei', 50, $Iac16_lei, true, 'text', $db_opcao);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="bold" for="licitacaoCompartilhada">Licitação
                                                        Compartilhada:</label>
                                                </td>
                                                <td>
                                                    <select id="licitacaoCompartilhada" class="field-size11">
                                                        <option value="0">Não se aplica</option>
                                                        <option value="1">Consórcio</option>
                                                        <option value="2">Orgão Gerenciador</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="licitacaoCgm" hidden>
                                                <td>
                                                    <a id="ancoraCgm" class="bold">Cgm:</a>
                                                </td>
                                                <td>
                                                    <input id="z01_numcgm" type="text" class="field-size3">
                                                    <input id="z01_nome" type="text" class="field-size8">
                                                </td>
                                            </tr>
                                            <tr id="trModalidade" hidden>
                                                <td>
                                                    <label class="bold" for="modalidadeCodigo">Modalidade:</label>
                                                </td>
                                                <td>
                                                    <select id="modalidadeCodigo">

                                                    </select>
                                                    <select id="modalidadeDescr">

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="trNumero" hidden>
                                                <td>
                                                    <label class="bold" for="numeroLicitacao">Numeração:</label>
                                                </td>
                                                <td>
                                                    <input type="text" class="field-size3" id="numeroLicitacao"
                                                           maxlength="10"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                    /
                                                    <input type="text" class="field-size2" id="anoLicitacao"
                                                           maxlength="4"
                                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="bold" for="lprocsis">Processo do Sistema:</label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $aProcSistema = array("s" => "Sim", "n" => "Não");
                                                    db_select('lprocsis', $aProcSistema, true, $db_opcao_editavel);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_numeroprocesso ?>">
                                                    <span
                                                        id="ancoraProcSis"><?php db_ancora($Lac16_numeroprocesso, "js_pesquisaac16_numeroprocesso(true);", $db_opcao_editavel, "", "DBAncoraProcSis"); ?></span>
                                                    <span id="procSis"
                                                          style="display: none;"><?= $Lac16_numeroprocesso ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_numeroprocesso', 10, $Iac16_numeroprocesso, true, 'text', $db_opcao_editavel, "onChange='js_pesquisaac16_numeroprocesso(false);' placeholder='Número/Ano' ");
                                                    db_input('ac62_protprocesso', 15, "", true, 'hidden', $db_opcao_editavel);
                                                    db_input('ac16_descprocesso', 30, "", true, 'text', 3, "");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?= $Tac16_qtdrenovacao ?>">
                                                    <?= $Lac16_qtdrenovacao; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_qtdrenovacao', 2, $Iac16_qtdrenovacao, true, 'text', $db_opcao,
                                                        "", "", "");
                                                    db_select("ac16_tipounidtempo", getValoresPadroesCampo("ac16_tipounidtempo"),
                                                        true, $db_opcao);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Contrato Emergencial:</b>
                                                </td>
                                                <td>
                                                    <?php
                                                    $aEmergencial = array("f" => "Não", "t" => "Sim");
                                                    db_select("ac26_emergencial", $aEmergencial, true, $db_opcao, "style='width:100%'");
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td nowrap title="<?= $Tac16_dataassinatura ?>">
                                                    <?= $Lac16_dataassinatura ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    db_inputdata('ac16_dataassinatura', $ac16_dataassinatura_dia, $ac16_dataassinatura_mes,
                                                        $ac16_dataassinatura_ano, true, 'text', $db_opcao);
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <b>Períodos por Mês Comercial:</b>
                                                </td>
                                                <td>
                                                    <?php
                                                    $iCampo = 1;
                                                    $sDisabled = ($db_opcao != 1 || $db_opcao != "1") ? " disabled " : "";
                                                    if ($db_opcao == 2 || $db_opcao == 22) {
                                                        $iCampo = 3;
                                                    }

                                                    $aDivisaoPeriodos = array("true" => "SIM", "false" => "NÃO");
                                                    db_select("ac16_periodocomercial", $aDivisaoPeriodos, true, $db_opcao, "style='width:100%' {$sDisabled}");
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td nowrap title="<?= $Tac50_descricao ?>">
                                                    <?php
                                                    db_ancora('<b>Categoria:</b>', "onchange=js_pesquisaac50_descricao(true)", $db_opcao);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac50_sequencial', 10, $Iac50_descricao, true, 'text', $db_opcao,
                                                        "onchange=js_pesquisaac50_descricao(false)");
                                                    db_input('ac50_descricao', 30, $Iac50_descricao, true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <label for="ac16_tipoinstrumento" class="bold">Tipo de
                                                        Instrumento:</label>
                                                </td>

                                                <td>
                                                    <?php
                                                    $aTipoInstrumento = Acordo::getTiposInstrumento();
                                                    $aTipoInstrumento = ['0' => 'Selecione'] + $aTipoInstrumento;
                                                    db_select('ac16_tipoinstrumento', $aTipoInstrumento, true, $db_opcao_editavel, "onchange='js_desabilitaSelecionarTipoInstrumento();'");
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <label for="ac16_dependeordeminicio" class="bold">Depende da Ordem
                                                        de Início:</label>
                                                </td>

                                                <td>
                                                    <?php
                                                    $aValores = array(
                                                        'f' => 'Não',
                                                        't' => 'Sim',
                                                    );
                                                    db_select('ac16_dependeordeminicio', $aValores, true, $db_opcao_editavel);
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr id='trValorAcordo'>
                                                <td nowrap title="<?= $Tac50_descricao ?>">
                                                    <strong>Valor do Acordo:</strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_valor', 10, $Iac16_valor, true, 'text', $db_opcao);
                                                    ?>
                                                </td>
                                            </tr>


                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <fieldset class='fieldsetinterno'>
                                        <legend>
                                            <b>Vigência</b>
                                        </legend>
                                        <table cellpadding="0" border="0" width="100%" class="table-vigencia">
                                            <tr>
                                                <td width="1%">
                                                    <b>Início:</b>
                                                </td>
                                                <td>
                                                    <?php
                                                    $iCampo = 1;

                                                    if ($db_opcao == 2 || $db_opcao == 22) {
                                                        $iCampo = 3;
                                                    }

                                                    db_inputdata('ac16_datainicio', $ac16_datainicio_dia, $ac16_datainicio_mes,
                                                        $ac16_datainicio_ano, true, 'text', $iCampo,
                                                        "onchange='return js_somardias();'", "", "",
                                                        "return parent.js_somardias();");
                                                    ?>
                                                </td>
                                                <td>
                                                    <b>Fim:</b>
                                                </td>
                                                <td>
                                                    <?php

                                                    db_inputdata('ac16_datafim', $ac16_datafim_dia, $ac16_datafim_mes, $ac16_datafim_ano,
                                                        true, 'text', $iCampo, "onchange='return js_somardias();'",
                                                        "", "", "return parent.js_somardias();");
                                                    ?>
                                                </td>
                                                <td>
                                                    <b>Dias:</b>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('diasvigencia', 10, "", true, 'text', 3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="Período">
                                                    <strong>Período:</strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    db_input('ac16_qtdperiodo', 2, $Iac16_qtdperiodo, true, 'text', $db_opcao,
                                                        "", "", "");
                                                    db_select("ac16_tipounidtempoperiodo", getValoresPadroesCampo("ac16_tipounidtempoperiodo"),
                                                        true, $db_opcao);
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= $Tac16_objeto ?>" colspan="2">
                                    <fieldset>
                                        <legend><?= $Lac16_objeto ?></legend>
                                        <?php
                                        db_textarea('ac16_objeto', 3, 58, $Iac16_objeto, true, 'text', $db_opcao, "");
                                        ?>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= $Tac16_resumoobjeto ?>" colspan="2">
                                    <fieldset>
                                        <legend><?= $Lac16_resumoobjeto ?></legend>
                                        <?php
                                        db_input('ac16_resumoobjeto', 40, $Iac16_resumoobjeto, true, 'text', $db_opcao, "", "",
                                            "", "width:100%");
                                        ?>
                                    </fieldset>
                                </td>
                            </tr>
                            <!-- ContratosPADRS: base legal de contratacao -->
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>

        <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
               type="button"
               id="db_opcao"
               value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
            <?= ($db_botao == false ? "disabled" : "") ?> >
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">

        <input name="pesquisarlicitacoes" type="button" id="pesquisarlicitacoes" style="display: none;"
               value="Julgamentos" onclick="oContrato.verificaLicitacoes();">

        <input name="pesquisarEmpenhos" type="button" id="pesquisarEmpenhos" value="Empenhos"
               onclick="js_MostraEmpenhos();" style="display: none; ">

        <input name="" type="button" id="btImprimirContrato" value="Imprimir" onclick="js_imprimirContrato()">

    </center>
</form>
<style>
    .filtroEmpenho {

        width: 91px;
    }
</style>
<script>
    var sCaminhoMensagens = "patrimonial.contratos.db_frmacordo";
    var dbopcao = '<?php echo $db_opcao ?>';
    const licitacaoCompartilhada = document.getElementById('licitacaoCompartilhada');
    const cgm = document.getElementById('numcgm');
    const licitacaoCgm = document.getElementById('licitacaoCgm');
    const trNumero = document.getElementById('trNumero');
    const trModalidade = document.getElementById('trModalidade');
    const ancoraCgm = document.getElementById('ancoraCgm');
    const codigoCgm = document.getElementById('z01_numcgm');
    const descricacaoCgm = document.getElementById('z01_nome');
    const modalidadeCodigo = document.getElementById('modalidadeCodigo');
    const modalidadeDescr = document.getElementById('modalidadeDescr');
    const numeroLicitacao = document.getElementById('numeroLicitacao');
    const anoLicitacao = document.getElementById('anoLicitacao');
    const origem = document.getElementById('ac16_origem');
    const LICITACAO = '2';

    origem.addEventListener('change', () => {
        licitacaoCompartilhada.disabled = origem.value === LICITACAO;
        if (origem.value === LICITACAO) {
            licitacaoCompartilhada.value = '0'
            licitacaoCgm.hidden = true;
            trNumero.hidden = true;
            trModalidade.hidden = true;
            codigoCgm.value = '';
            descricacaoCgm.value = '';
            numeroLicitacao.value = '';
            anoLicitacao.value = '';
        }
    });

    modalidadeCodigo.addEventListener('change', () => {
        modalidadeDescr.value = modalidadeCodigo.value;
    });

    modalidadeDescr.addEventListener('change', () => {
        modalidadeCodigo.value = modalidadeDescr.value;
    });

    new DBLookUp(ancoraCgm, codigoCgm, descricacaoCgm, {
        'arquivo': 'func_nome.php',
        'label': 'Pesquisa',
        'objetoLookUp': 'db_iframe_numcgm',
    });

    licitacaoCompartilhada.addEventListener('change', () => {
        codigoCgm.value = '';
        descricacaoCgm.value = '';
        numeroLicitacao.value = '';
        anoLicitacao.value = '';
        licitacaoCgm.hidden = licitacaoCompartilhada.value === '0';
        trNumero.hidden = licitacaoCompartilhada.value === '0';
        trModalidade.hidden = licitacaoCompartilhada.value === '0';

    })

    $('trValorAcordo').style.display = 'none';
    if (dbopcao == 1) {
        $('ac16_anousu').value = '<?php echo db_getsession('DB_anousu') ?>';
    }

    /**
     * Processo Administrativo
     */
    $("ac16_numeroprocesso").addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9\/]/g, '').replace(/(\/?)([0-9]*)(\/?)([0-9]{0,4})(.*)(\/?)/, '$2$3$4')
    });

    if (dbopcao == 3) {
        $('ancoraProcSis').setAttribute("onclick", "js_pesquisaac16_numeroprocesso(true);");
    }

    $("lprocsis").addEventListener("change", function () {
        $('ac16_numeroprocesso').value = '';
        $('ac16_descprocesso').value = '';
        $('ac62_protprocesso').value = '';
        if (this.value == 's') {
            $('ancoraProcSis').show();
            $('procSis').hide();
            $('ac16_descprocesso').show();
            $('ac16_numeroprocesso').setAttribute("onchange", "js_pesquisaac16_numeroprocesso(false);");
            $('ac16_numeroprocesso').setAttribute("size", "10");
        } else {
            $('ancoraProcSis').hide();
            $('procSis').show();
            $('ac16_descprocesso').hide();
            $('ac16_numeroprocesso').removeAttribute("onchange");
            $('ac16_numeroprocesso').setAttribute("size", "50");
        }
    });

    if ($("ac62_protprocesso").value == '') {
        $("lprocsis").value = 'n';
        $('ancoraProcSis').hide();
        $('procSis').show();
        $('ac16_descprocesso').hide();
        $('ac16_numeroprocesso').removeAttribute("onchange");
        $("ac16_numeroprocesso").setAttribute("size", "50");
    }

    function js_validaCampoValor() {

        var iOrigem = $('ac16_origem').value;
        if (iOrigem == 6) {

            $('trValorAcordo').style.display = 'table-row';
            $('ac16_valor').readOnly = false;
        } else {
            $('trValorAcordo').style.display = 'none';
            $('ac16_valor').value = '';
            $('ac16_valor').readOnly = true;
        }

    }

    /**
     * valida antes de colar no campo valor
     */
    $('ac16_valor').onpaste = function (event) {
        return /^[0-9|.]+$/.test(event.clipboardData.getData('text/plain'));
    };


    /*
     * funcao que ira realizar vinculo dos empenhos selecionados
     */
    function js_vincularEmpenhos(iNumCgm) {

        var sListaEmpenhos = js_getEmpenhosSelecionados();
        var iOrigem = $F('ac16_origem');
        var iAcordo = $F('ac16_sequencial');
        var oParametros = new Object();

        oParametros.exec = 'vincularEmpenhos';
        oParametros.iNumCgm = iNumCgm;
        oParametros.sListaEmpenhos = sListaEmpenhos;
        oParametros.iOrigem = iOrigem;
        oParametros.iAcordo = iAcordo;

        js_divCarregando(_M(sCaminhoMensagens + '.vincular_empenho'), 'msgBox');

        var oAjaxLista = new Ajax.Request("ac4_acordoinclusao.rpc.php",
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: js_retornoVincularEmpenhos
            });

    }

    function js_buscarModalidades() {
        let oParametros = {};
        oParametros.exec = 'buscarModalidades';

        let oAjaxLista = new Ajax.Request("con4_contratos.RPC.php",
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: js_retornoModalidades
            });
    }

    js_buscarModalidades();

    function js_retornoModalidades(oAjax) {
        let oRetorno = JSON.parse(oAjax.responseText);
        const modalidades = oRetorno.modalidades;
        modalidades.forEach((modalidade) => {
            let optionCodigo = new Option(modalidade.l03_codigo, modalidade.l03_codigo)
            modalidadeCodigo.options[modalidadeCodigo.options.length] = optionCodigo;

            let optionDescr = new Option(modalidade.l03_descr, modalidade.l03_codigo)
            modalidadeDescr.options[modalidadeDescr.options.length] = optionDescr;
        });
    }

    function js_retornoVincularEmpenhos(oAjax) {

        oGridEmpenhos.clearAll(true);
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.iStatus == 2) {
            alert(oRetorno.sMessage.urlDecode());
            return false;
        }
        alert(oRetorno.sMessage.urlDecode());
        js_getEmpenhosVinculados();
    }

    /*
     * funcao que ira retornar os empenhos selecionados
     */
    function js_getEmpenhosSelecionados() {

        var aListaCheckbox = oGridEmpenhos.getSelection();
        var aListaEmpenhos = new Array();

        aListaCheckbox.each(
            function (aRow) {
                aListaEmpenhos.push(aRow[0]);
            }
        )

        return aListaEmpenhos;
    }


    function js_getEmpenhosVinculados() {


        var iAcordo = $F("ac16_sequencial");

        if (iAcordo == "") {
            return false;
        }

        js_divCarregando(_M(sCaminhoMensagens + '.empenhos_vinculados'), 'msgBox');

        var oParametros = new Object();
        oParametros.iAcordo = iAcordo;
        oParametros.exec = "getEmpenhosVinculadosAcordo";

        var oAjax = new Ajax.Request("ac4_acordoinclusao.rpc.php",
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: js_retornoCompletaEmpenhos
            });

    }

    /*
     * função para retornar os empenhos do cgm do contratado selecionado
     *  e os possiveis filtros selecionados na windowaux
     */
    function js_getEmpenhos(iNumCgm) {

        var iCodigoEmpenho = $F("e60_numemp");
        var iNumeroEmpenho = $F("e60_codemp");
        var dtInicial = $F("oTxtDataEmissaoInicial");
        var dtFinal = $F("oTxtDataEmissaoFinal");
        var iAcordo = $F("ac16_sequencial");

        //var msgDiv         = "Pesquisando Empenhos<br>Aguarde ...";
        var oParametros = new Object();

        oParametros.exec = 'getEmpenhos';
        oParametros.iNumCgm = iNumCgm;
        oParametros.iCodigoEmpenho = iCodigoEmpenho;
        oParametros.iNumeroEmpenho = iNumeroEmpenho;
        oParametros.dtInicial = dtInicial;
        oParametros.dtFinal = dtFinal;
        oParametros.iAcordo = iAcordo;

        //js_divCarregando(msgDiv,'msgBox');
        js_divCarregando(_M(sCaminhoMensagens + '.pesquisando_empenhos'), 'msgBox');

        var oAjaxLista = new Ajax.Request("ac4_acordoinclusao.rpc.php",
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: js_retornoCompletaEmpenhos
            });

    }

    // função monta grid com empenhos retornados do cgm
    function js_retornoCompletaEmpenhos(oAjax) {


        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);


        if (oRetorno.iStatus == 1) {
            oGridEmpenhos.clearAll(true);
            oRetorno.aDadosRetorno.each(
                function (oDado, iInd) {

                    var lVinculado = false;
                    if (oDado.lVinculado == 'true') {
                        lVinculado = true;
                    }

                    var aRow = new Array();
                    aRow[0] = oDado.e60_numemp;
                    aRow[1] = oDado.e60_codemp;
                    aRow[2] = oDado.e60_emiss;
                    aRow[3] = oDado.e60_vlremp;
                    aRow[4] = oDado.e60_resumo.urlDecode();
                    oGridEmpenhos.addRow(aRow, true, false, lVinculado);

                });
            oGridEmpenhos.renderRows();
        } else {
            alert(oRetorno.sMessage.urlDecode());
        }
    }

    //---    WINDOW AUX para exibição de Empenhos ======================//
    function js_MostraEmpenhos() {


        var iNumCgm = $F('ac16_contratado');
        var sContratado = $F("nomecontratado");
        var iLarguraJanela = screen.availWidth - 80;
        var iAlturaJanela = screen.availHeight - 250;
        var iAcordo = $F('ac16_sequencial');

        if (iNumCgm == '' || iNumCgm == null) {

            alert(_M(sCaminhoMensagens + '.mostrar_empenhos'));//'Selecione o Cgm do Contratado.');
            return false;
        }

        windowEmpenhos = new windowAux('windowEmpenhos',
            'Empenhos',
            iLarguraJanela,
            iAlturaJanela
        );

        var sConteudoEmpenhos = "<div>";
        sConteudoEmpenhos += "<div id='sTituloWindow'></div> "; // container do message box

        sConteudoEmpenhos += "<div>";
        sConteudoEmpenhos += " <fieldset><legend><strong>Filtros para Seleção de Empenho</strong></legend>";
        sConteudoEmpenhos += "  <table border ='0'>";

        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <strong>Contratado:</strong>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "      <td style='color: blue;'>";
        sConteudoEmpenhos += iNumCgm + " - " + sContratado;
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";

        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <strong>Acordo:</strong>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <input  type='text' class='filtroEmpenho' value='" + iAcordo + "'  id='iAcordo' readonly='readonly' style='background-color: #DEB887 ' \>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";

        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <strong>Código do Empenho:</strong>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <input class='filtroEmpenho' type='text' id='e60_numemp' onkeyup='js_ValidaCampos(this,1,\"Código do Empenho\",\"f\",\"f\",event);' >";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";
        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <strong>Número do Empenho:</strong>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <input class='filtroEmpenho' type='text' id='e60_codemp' onkeyup='js_ValidaCampos(this,1,\"Número do Empenho\",\"f\",\"f\",event);'>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";
        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "        <strong>Período de Emissão:</strong>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "      <td>";
        sConteudoEmpenhos += "       <label id='ctnDataInicial' style='float:left;'></label>";
        sConteudoEmpenhos += "         <strong style='float:left;'>&nbsp;até&nbsp;</strong>";
        sConteudoEmpenhos += "       <div id='ctnDataFinal' style='float:left;' ></div>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";
        sConteudoEmpenhos += "    <tr>";
        sConteudoEmpenhos += "      <td colspan='2' align='center'>";
        sConteudoEmpenhos += "        <input style='margin-top:10px;' type='button' id='filtraEmpenho' name='filtraEmpenho' value='Filtrar' onclick='js_getEmpenhos(" + iNumCgm + ");'>";
        sConteudoEmpenhos += "      </td>";
        sConteudoEmpenhos += "    </tr>";
        sConteudoEmpenhos += "  </table>";
        sConteudoEmpenhos += " ";
        sConteudoEmpenhos += "<div>";
        sConteudoEmpenhos += "<div id='sContGrid' style='width: 100%;'></div></fieldset> ";    // container da grid com empenhos;
        sConteudoEmpenhos += "<center style='margin-top:10px;'>";
        sConteudoEmpenhos += "  <input type='button' value='Salvar' onclick='js_vincularEmpenhos(" + iNumCgm + ");' />";
        sConteudoEmpenhos += "  <input type='button' value='Cancelar' onclick='windowEmpenhos.destroy();' />";
        sConteudoEmpenhos += "<center>";

        sConteudoEmpenhos += "</div>";

        windowEmpenhos.setContent(sConteudoEmpenhos);

        //============  MESAGE BORD PARA TITULO da JANELA de Empenhos
        var sTextoMessageBoard = 'Procedimento para vincular ou desvincular empenhos ao acordo. <br> ';
        messageBoard = new DBMessageBoard('msgboard1',
            'Empenhos Vinculados e a Vincular',
            sTextoMessageBoard,
            $('sTituloWindow'));

        // instancia dos objetos db_inputdata
        oTxtDataEmissaoInicial = new DBTextFieldData('oTxtDataEmissaoInicial', 'oTxtDataEmissaoInicial', null);
        oTxtDataEmissaoFinal = new DBTextFieldData('oTxtDataEmissaoFinal', 'oTxtDataEmissaoFinal', null);


        //    funcao para corrigir a exibição do window aux, apos fechar a primeira vez
        windowEmpenhos.setShutDownFunction(function () {
            windowEmpenhos.destroy();
        });

        windowEmpenhos.show();
        messageBoard.show();                    // chamada que monta message board
        oTxtDataEmissaoInicial.show($('ctnDataInicial')); // chamada que monta input data inicial
        oTxtDataEmissaoFinal.show($('ctnDataFinal'));   // chamada que monta input data final
        js_montaGridEmpenhos();                           // chamada função que monta a grid

        js_getEmpenhosVinculados();
    }

    //função para chamada da grid que tera erros e avisos

    function js_montaGridEmpenhos() {

        var sNameGrid = 'Empenhos';
        oGridEmpenhos = new DBGrid(sNameGrid);
        oGridEmpenhos.nameInstance = 'oGridEmpenhos';
        oGridEmpenhos.allowSelectColumns(false);
        oGridEmpenhos.setCheckbox(0);

        oGridEmpenhos.setCellWidth(new Array('10%',
            '10%',
            '10%',
            '10%',
            '60%'
        ));

        oGridEmpenhos.setCellAlign(new Array('center',
            'right',
            'center',
            'right',
            'left'
        ));

        oGridEmpenhos.setHeader(new Array('Código do Empenho',
            'Número do Empenho',
            'Emissão',
            'Valor Empenhado',
            'Resumo'
        ));

        oGridEmpenhos.setHeight(200);
        oGridEmpenhos.show($('sContGrid'));
        oGridEmpenhos.clearAll(true);

        $('grid' + sNameGrid).style.width = '99%';
    }

    /*
     * funcao que controla a exibição
       dos botoes JULGAMENTOS e EMPENHOS
       se a origem for Licitacao || Processo compras exibimos os julgamentos
       se a origem for empenho exibimos o botao para selecionar empenhos
     */
    function js_exibeBotaoJulgamento() {

        var iOrigem = $F('ac16_origem');
        var iAcordo = $F("ac16_sequencial");

        switch (iOrigem) {

            case  '1':
                $('pesquisarlicitacoes').style.display = 'inLine';
                $('pesquisarEmpenhos').style.display = 'none';
                break;

            case  '2':
                $('pesquisarlicitacoes').style.display = 'inLine';
                $('pesquisarEmpenhos').style.display = 'none';
                break;

            case '6' :

                if ($F('ac16_sequencial') != '') {
                    $('pesquisarEmpenhos').style.display = 'inLine';
                }

                $('pesquisarlicitacoes').style.display = 'none';
                break;

            default  :

                $('pesquisarlicitacoes').style.display = 'none';
                $('pesquisarEmpenhos').style.display = 'none';
                break;

        }

        if (iAcordo != '' || iAcordo != null) {
            //alert(5555);
            //$('ac16_origem').disabled        = true;
        }

    }


    var oContrato = new contrato();
    <?php if ($parametro_exibe_fiscal): ?>
    oContrato.parametro_exibe_fiscal = true;
    <?php endif ?>
    var sURL = "con4_contratos.RPC.php";

    function js_somardias() {

        var sDataInicio = $('ac16_datainicio').value;
        var sDataFim = $('ac16_datafim').value;

        if (js_somarDiasVigencia(sDataInicio, sDataFim) != false) {
            $('diasvigencia').value = js_somarDiasVigencia(sDataInicio, sDataFim);
        }
    }

    function js_desabilitaselecionar() {

        //Limpa opções de contratado previamente selecionadas
        $("ac16_contratado").value = '';
        $("nomecontratado").value = '';

        var iAcordoOrigem = $('ac16_origem').value;

        if (iAcordoOrigem != 0) {
            $('ac16_origem').options[0].disabled = true;
        }
    }

    function js_pesquisaac16_acordogrupo(mostra) {

        if (mostra == true) {

            var sUrl = 'func_acordogrupo.php?funcao_js=parent.js_mostraacordogrupo1|ac02_sequencial|ac02_descricao';
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_acordogrupo',
                sUrl,
                'Pesquisar Grupos de Acordo',
                true,
                '0');
        } else {

            if ($('ac16_acordogrupo').value != '') {

                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_acordogrupo',
                    'func_acordogrupo.php?pesquisa_chave=' + $('ac16_acordogrupo').value +
                    '&funcao_js=parent.js_mostraacordogrupo',
                    'Pesquisar Grupos de Acordo',
                    false,
                    '0');
            } else {
                $('ac02_sequencial').value = '';
            }
        }
    }

    function js_mostraacordogrupo(chave, erro) {

        $('ac02_descricao').value = chave;
        if (erro == true) {

            $('ac16_acordogrupo').focus();
            $('ac16_acordogrupo').value = '';
        } else {

            var oGet = js_urlToObject();

            /*
             * Verifica se está sendo setada a variavel chavepesquisa na url. Caso sim, quer dizer que é um procedimento de alteração ou exclusão,
             * sendo assim o programa não pode chamar a nova numeração
             *
             */
            if (!oGet.chavepesquisa) {
                oContrato.getNumeroAcordo();
            }

        }

    }

    function js_mostraacordogrupo1(chave1, chave2) {

        $('ac16_acordogrupo').value = chave1;
        $('ac02_descricao').value = chave2;
        $('ac16_acordogrupo').focus();

        var oGet = js_urlToObject();

        /*
         * Verifica se está sendo setada a variavel chavepesquisa na url. Caso sim, quer dizer que é um procedimento de alteração ou exclusão,
         * sendo assim o programa não pode chamar a nova numeração
         *
         */
        if (!oGet.chavepesquisa) {
            oContrato.getNumeroAcordo();
        }

        db_iframe_acordogrupo.hide();
    }

    function js_pesquisa() {

        var sUrlLookup = 'func_acordo.php';
        var aParametros = [
            'funcao_js=parent.js_preenchepesquisa|ac16_sequencial',
            'iTipoFiltro=<?php echo $sTipoFiltro ?>',
            'lAtivo=1',
            'lComExecucao=false'
        ];
        var sUrl = sUrlLookup + '?' + aParametros.join('&');

        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo', 'db_iframe_acordo', sUrl, 'Pesquisar Acordos', true, '0', '1');
    }

    function js_preenchepesquisa(chave) {

        db_iframe_acordo.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>

    }

    function js_pesquisaac16_contratado(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_contratado',
                'func_nome.php?funcao_js=parent.js_mostracontratado1|z01_nome|z01_numcgm',
                'Pesquisar CGM',
                true,
                '0');
        } else {

            if ($('ac16_contratado').value != '') {

                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_acordogrupo',
                    'func_nome.php?pesquisa_chave=' + $F('ac16_contratado') +
                    '&funcao_js=parent.js_mostracontratado',
                    'Pesquisa',
                    false,
                    '0');
            } else {
                $('nomecontratado').value = '';
            }
        }
    }

    function js_mostracontratado(erro, chave) {
        $('nomecontratado').value = chave;
        if (erro == true) {
            $('ac16_contratado').focus();
            $('ac16_contratado').value = '';
        } else {
            oContrato.verificaLicitacoes();
            if ($('ac16_origem').value == 6) {
                //js_MostraEmpenhos();
            }
        }
    }

    function js_mostracontratado1(chave1, chave2) {

        $('ac16_contratado').value = chave2;
        $('nomecontratado').value = chave1;
        oContrato.verificaLicitacoes();

        if ($('ac16_origem').value == 6) {
            //js_MostraEmpenhos();
        }

        db_iframe_contratado.hide();
    }

    function js_pesquisaz01_numcgm(mostra) {
        if (mostra == true) {
            <?php if (isRioDeJaneiro()) :?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo.iframe_acordo',
                'db_iframe_cgm',
                'func_rhpessoal.php?funcao_js=parent.js_mostracgm|rh01_regist|rh01_numcgm|z01_cgccpf|z01_nome&lTodos=1',
                'Pesquisar CGM',
                true,
                '0'
            );
            <?php else: ?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo.iframe_acordo',
                'db_iframe_cgm',
                'func_rhpessoal.php?funcao_js=parent.js_mostracgm|rh01_regist|rh01_numcgm|z01_cgccpf|z01_nome',
                'Pesquisar CGM',
                true,
                '0'
            );
            <?php endif ?>
        } else {
            if ($('rh01_regist').value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_cgm',
                    'func_rhpessoal.php?pesquisa_chave=' + $F('rh01_regist') +
                    '&funcao_js=parent.js_mostracgm',
                    'Pesquisa',
                    false,
                    '0'
                );
            } else {
                $('z01_nome').value = '';
                $('z01_cgccpf').value = '';
                $('rh01_regist').value = '';
            }
        }
    }

    function js_pesquisa_gestor () {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo.iframe_acordo',
            'db_iframe_cgm',
            'func_rhpessoal.php?funcao_js=parent.js_mostracgm_gestor|z01_nome|rh01_numcgm&lTodos=1',
            'Pesquisar CGM',
            true,
            '0'
        );
    }

    function js_mostracgm(regist, cgm, cpf, nome) {
        db_iframe_cgm.hide();
        addFiscal(nome, cpf, regist, cgm)
    }

    function js_mostracgm_gestor(nome, cgm) {
        $('z01_nome_gestor').value = nome;
        $('z01_numcgm_gestor').value = cgm;
        db_iframe_cgm.hide();
    }

    function js_pesquisaac16_deptoresponsavel(mostra) {

        if (mostra == true) {

            var sUrl = 'func_db_depart.php?funcao_js=parent.js_mostradeptoresponsavel1|coddepto|descrdepto';
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_deptoresponsavel',
                sUrl,
                'Pesquisar CGM',
                true,
                '0');
        } else {

            if ($('ac16_deptoresponsavel').value != '') {

                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_acordogrupo',
                    'func_db_depart.php?pesquisa_chave=' + $F('ac16_deptoresponsavel') +
                    '&funcao_js=parent.js_mostradeptoresponsavel',
                    'Pesquisa',
                    false,
                    '0');
            } else {
                $('descrdepto').value = '';
            }
        }
    }

    function js_mostradeptoresponsavel(chave, erro) {

        $('descrdepto').value = chave;
        if (erro == true) {

            $('ac16_deptoresponsavel').focus();
            $('ac16_deptoresponsavel').value = '';
        }
    }

    function js_mostradeptoresponsavel1(chave1, chave2) {

        $('ac16_deptoresponsavel').value = chave1;
        $('descrdepto').value = chave2;
        $('ac16_deptoresponsavel').focus();

        db_iframe_deptoresponsavel.hide();
    }

    function js_pesquisaac16_acordocomissao(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_comissao',
                'func_acordocomissao.php?funcao_js=parent.js_mostracomissao1|' +
                'ac08_sequencial|ac08_descricao',
                'Pesquisar Comissões de Vistoria',
                true,
                '0');
        } else {

            if ($('ac16_acordocomissao').value != '') {

                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_comissao',
                    'func_acordocomissao.php?pesquisa_chave=' + $F('ac16_acordocomissao') +
                    '&funcao_js=parent.js_mostracomissao',
                    'Pesquisar Comissões de Vistoria',
                    false,
                    '0');
            } else {
                $('ac08_descricao').value = '';
            }
        }
    }

    function js_mostracomissao(chave, erro) {

        $('ac08_descricao').value = chave;
        if (erro) {

            $('ac16_acordocomissao').focus();
            $('ac16_acordocomissao').value = '';
        }
    }

    function js_mostracomissao1(chave1, chave2) {

        $('ac16_acordocomissao').value = chave1;
        $('ac08_descricao').value = chave2;
        $('ac16_acordocomissao').focus();

        db_iframe_comissao.hide();
    }

    function js_pesquisaac50_descricao(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_acordocategoria',
                'func_acordocategoria.php?funcao_js=parent.js_mostraacordocategoria1|' +
                'ac50_sequencial|ac50_descricao',
                'Pesquisar Categorias de Acordo',
                true,
                '0');
        } else {

            if ($('ac50_sequencial').value != '') {

                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_acordocategoria',
                    'func_acordocategoria.php?pesquisa_chave=' + $F('ac50_sequencial') +
                    '&funcao_js=parent.js_mostraacordocategoria',
                    'Pesquisar Categorias de Acordo',
                    false,
                    '0');
            } else {
                $('ac50_descricao').value = '';
            }
        }
    }

    function js_mostraacordocategoria(chave1, chave2) {

        $('ac50_descricao').value = chave1;
        $('ac50_sequencial').focus();

        db_iframe_acordocategoria.hide();
    }

    function js_mostraacordocategoria1(chave1, chave2) {

        $('ac50_sequencial').value = chave1;
        $('ac50_descricao').value = chave2;
        $('ac50_sequencial').focus();

        db_iframe_acordocategoria.hide();
    }

    function js_pesquisaac16_numeroprocesso(mostra) {
        if (mostra) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                'db_iframe_proc',
                'func_protprocesso_protocolo.php?funcao_js=parent.js_mostraprocesso1|p58_numero|dl_codigo_do_processo|dl_titular',
                'Pesquisa',
                true,
                '0');
        } else {
            if ($('ac16_numeroprocesso').value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
                    'db_iframe_proc',
                    'func_protprocesso_protocolo.php?pesquisa_chave=' + $('ac16_numeroprocesso').value + '&funcao_js=parent.js_mostraprocesso&sCampoRetorno=p58_codproc',
                    'Pesquisa',
                    false);
            } else {
                $('ac16_descprocesso').value = '';
            }
        }
    }

    function js_mostraprocesso(iCodigoProcesso, sNome, lErro) {
        $('ac16_descprocesso').value = sNome;

        if (lErro) {
            $('ac16_numeroprocesso').focus();
            $('ac16_numeroprocesso').value = '';
            $('ac62_protprocesso').value = '';
            return false;
        }

        $('ac62_protprocesso').value = iCodigoProcesso;
        db_iframe_proc.hide();
    }

    function js_mostraprocesso1(iNumeroProcesso, iCodigoProcesso, sNome) {
        $('ac16_numeroprocesso').value = iNumeroProcesso;
        $('ac62_protprocesso').value = iCodigoProcesso;
        $('ac16_descprocesso').value = sNome;

        db_iframe_proc.hide();
    }

    function js_imprimirContrato() {

        var iContrato = $('ac16_sequencial').value;
        var iTipoOrigem = $('ac16_origem').value;

        var sUrl = 'aco2_impressaoacordo001.php?iContrato=' + iContrato + '&iTipoOrigem=' + iTipoOrigem;
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_acordo',
            'db_iframe_impressaocontrato',
            sUrl,
            'Impressão do Contrato',
            true,
            '0');
    }

    $('db_opcao').observe("click", function () {
        let iAno = $('ac16_anousu').value;
        let anoExercicio = '<?php echo db_getsession('DB_anousu') ?>'
        let anoAtual = '<?php echo date("Y") ?>'
        let salvaContrato = true;

        if (iAno > anoAtual) {
            alert("Não é possível inclusão de contrato com ano superior ao atual, por favor, verifique!")
            salvaContrato = false;
        }

        if (iAno != anoExercicio && salvaContrato) {
            salvaContrato = confirm("O exercicio informado(" + iAno
                + ") difere da sessão(" + anoExercicio
                + "), deseja realmente incluir o contrato para o exercício de (" + iAno
                + ")?");
        }
        if (salvaContrato) {
            oContrato.saveContrato();
        }
    });

    function js_desabilitaSelecionarTipoInstrumento() {
        const tipoInstrumento = $('ac16_tipoinstrumento').value;

        if (tipoInstrumento != 0) {
            $('ac16_tipoinstrumento').options[0].disabled = true;
        }
    }


    // $('db_opcao').onclick = oContrato.saveContrato;
    <?php
    if ($db_opcao == 2 || $db_opcao == 3) {
        echo "\noContrato.getContrato({$chavepesquisa});\n";
    } else {
        echo "\njs_somardias();\n";
    }
    ?>

    $('ac16_valor').observe("change", function () {

        $('ac16_valor').value = js_formatar($('ac16_valor').value, "f");
    });

    /**
     * Cria uma Grid de Fiscais
     */
    oGridFiscal = new DBGrid('gridFiscal');
    oGridFiscal.nameInstace = 'oGridFiscal';
    if (dbopcao == 1) {
        oGridFiscal.setCellAlign(new Array("center", "center", "center", "center", "center"));
        oGridFiscal.setCellWidth(new Array("35%", "20%", "15%", "15%", "15%"));
        oGridFiscal.setHeader(new Array("Nome", "CPF", "Matrícula", "Cgm", "Ação"));
    } else {
        oGridFiscal.setCellAlign(new Array("center", "center", "center", "center"));
        oGridFiscal.setCellWidth(new Array("40%", "30%", "15%", "15%"));
        oGridFiscal.setHeader(new Array("Nome", "CPF", "Matrícula", "Cgm"));
    }
    oGridFiscal.setHeight(50);
    oGridFiscal.show($('gridFiscal'));

    var aFiscalItem = new Array();

    if (dbopcao != 1) {
        <?php if ($fiscalcgm): ?>
        const arrayCgm = <?php echo $array_cgm_encode ?>;
        const arrayRh = <?php echo $array_rh_encode ?>;

        let sFiscalNome = "";
        let sFiscalCPF = "";
        let iFiscal = "";
        let rh01_regist = "";

        for (let i = 0; i < arrayCgm.length; i++) {
            const sFiscalNome = arrayCgm[i].z01_nome;
            const sFiscalCPF = arrayCgm[i].z01_cgccpf;
            const iFiscal = arrayCgm[i].z01_numcgm;
            const rh01_regist = arrayRh[i].rh01_regist;

            addFiscal(sFiscalNome, sFiscalCPF, rh01_regist, iFiscal);
        }

        <?php endif ?>
    }

    function addFiscal(nome, cpf, regist, cgm) {
        var oFiscal = new Object();

        if (nome == "" || cpf == "") {

            alert("Fiscal deve serem preenchido.");
            return false;
        }

        oFiscal.sFiscalNome = nome;
        oFiscal.sFiscalCPF = cpf;
        oFiscal.rh01_regist = regist;
        oFiscal.iFiscal = cgm;
        aFiscalItem.push(oFiscal);

        showFiscal();
    }

    function showFiscal() {
        oGridFiscal.clearAll(true);

        aFiscalItem.each(function(oFiscal, iLinha) {
            var aRow = new Array();
            aRow[0] = oFiscal.sFiscalNome;
            aRow[1] = oFiscal.sFiscalCPF;
            aRow[2] = oFiscal.rh01_regist;
            aRow[3] = oFiscal.iFiscal;
            aRow[4] = "<input type='button' id='excluirFiscal' value='Excluir' onclick='js_excluiFiscal(" + iLinha + ");'>";
            oGridFiscal.addRow(aRow);
        });
        oGridFiscal.renderRows();
    }

    function js_excluiFiscal(iLinha) {
        aFiscalItem.splice(iLinha, 1);
        showFiscal();
    }

</script>
