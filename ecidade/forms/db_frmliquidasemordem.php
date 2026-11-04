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

//MODULO: empenho
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("o56_elemento");
$clrotulo->label("e69_numero");
$clrotulo->label("e11_cfop");
$clrotulo->label("e10_cfop");
$clrotulo->label("e11_seriefiscal");
$clrotulo->label("e11_inscricaosubstitutofiscal");
$clrotulo->label("e11_valoricmssubstitutotrib");
$clrotulo->label("e11_basecalculoicmssubstitutotrib");
$clrotulo->label("e11_basecalculoicms");
$clrotulo->label("e11_valoricms");
$clrotulo->label("e12_descricao");
$clrotulo->label("e10_descricao");
$clrotulo->label("cc31_classificacaocredores");
$db_opcao = 1;
$sData = date("d/m/Y", db_getsession("DB_datausu"));
$clorctiporec->rotulo->label();
$clempempenho->rotulo->label();
$clorcdotacao->rotulo->label();
$clpagordemele->rotulo->label();
$clpagordemnota->rotulo->label();
$clempnota->rotulo->label();
$clempnotaele->rotulo->label();
$cltabrec->rotulo->label();
if ($tela_estorno) {
    $operacao = 2; //operacao a ser realizada:1 = liquidacao, 2 estorno
    $labelVal = "SALDO A ESTORNAR";
    $metodo = "estornarLiquidacaoAJAX";
} else {
    $operacao = 1; //operacao a ser realizada:1 = liquidacao, 2 estorno
    $labelVal = "SALDO A LIQUIDAR";
    $metodo = "liquidarAjax";
}
$db_opcao_inf = 1;
$aParamKeys = array(
    "cc09_anousu" => db_getsession("DB_anousu"),
    "cc09_instit" => db_getsession("DB_instit"),
);
$aParametrosCustos = db_stdClass::getParametro("parcustos", $aParamKeys);
$iTipoControleCustos = 0;
$iControlaPit = 0;

if (count($aParametrosCustos) > 0) {
    $iTipoControleCustos = $aParametrosCustos[0]->cc09_tipocontrole;
}

$aParamKeys = array(
    db_getsession("DB_instit")
);
$aParametrosPit = db_stdClass::getParametro("matparaminstit", $aParamKeys);
if (count($aParametrosPit) > 0) {
    $iControlaPit = $aParametrosPit[0]->m10_controlapit;
}

$lUsaPCASP = "false";
if (USE_PCASP) {
    $lUsaPCASP = "true";
}

$e60_anousu = isset($e60_anousu) ? $e60_anousu : null;
$Le60_vlremp = isset($Le60_vlremp) ? $Le60_vlremp : null;
$Le60_vlranu = isset($Le60_vlranu) ? $Le60_vlranu : null;
$Le60_vlrliq = isset($Le60_vlrliq) ? $Le60_vlrliq : null;
$Le60_vlrpag = isset($Le60_vlrpag) ? $Le60_vlrpag : null;
$Te11_cfop = isset($Te11_cfop) ? $Te11_cfop : null;
$isPB = isParaiba();
$Ie11_basecalculoicms = isset($Ie11_basecalculoicms) ? $Ie11_basecalculoicms : null;
$Ie11_basecalculosubstitutotrib = isset($Ie11_basecalculosubstitutotrib) ? $Ie11_basecalculosubstitutotrib : null;
$isRJ = isRioDeJaneiro();

// Diarias
$labelDiariaTipo = 'Tipo diária:';
$labelDiariaDestino = 'Destino:';
$labelObjetoDiaria = 'Motivo/Descrição';
$exibirCamposAdicionaisRJ = false;
$listaTiposDiaria = ['0' => 'Internacional', '1' => 'Nacional'];

if ($isRJ) {
    $exibirCamposAdicionaisRJ = true;
    $labelDiariaDestino = 'Cidade(s) / Destino:';
    $labelObjetoDiaria = 'Objeto da Diária:';
    $labelDiariaTipo = 'Destino:';
    $diariaTipo = 1;

}

?>
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
<style>
    div.files {
        display: inline-block;
        background: #fff;
        padding: 5px;
        border-radius: 5px;
        border: solid #0a0a0a 1px;
    }

    div#arquivos-selecionados {
        width: 400px;
        height: auto;
        margin-bottom: 20px;
    }
</style>
<form name=form1 action="" method="POST">
    <table width='80%' cellspacing='0' style='padding:0px; margin-top: 20px;' border='0'>
        <tr>
            <td style='padding:0px' valign="top">
                <fieldset>
                    <legend><b>&nbsp;Empenho&nbsp;</b></legend>
                    <table>
                        <tr>
                            <td><?= db_ancora($Le60_codemp, "js_JanelaAutomatica('empempenho',\$F('e60_numemp'))", $db_opcao_inf) ?></td>
                            <td><?php db_input('e60_codemp', 13, $Ie60_codemp, true, 'text', 3) ?> </td>
                            <td nowrap="nowrap"><?= db_ancora($Le60_numemp, "js_JanelaAutomatica('empempenho',\$F('e60_numemp'))", $db_opcao_inf) ?></td>
                            <td><?php db_input('e60_numemp', 13, $Ie60_numemp, true, 'text', 3) ?> </td>
                        </tr>
                        <tr>
                            <td><?= db_ancora($Le60_numcgm, "js_JanelaAutomatica('cgm',\$F('e60_numcgm'))", $db_opcao_inf) ?></td>
                            <td><?php db_input('e60_numcgm', 13, $Ie60_numcgm, true, 'text', 3); ?> </td>
                            <td colspan=2><?php db_input('z01_nome', 52, $Iz01_nome, true, 'text', 3, ''); ?></td>
                        </tr>
                        <tr>
                            <td><label for="codigo_classificacao" class="bold"><?= $Lcc31_classificacaocredores ?></label></td>
                            <td nowrap colspan="3">
                                <?php
                                db_input('codigo_classificacao', 13, '', true, 'text', 3);
                                db_input('descricao_classificacao', 52, '', true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= db_ancora('<b>Credor:</b>', "js_pesquisae49_numcgm(true)", 1) ?></td>
                            <td><?php db_input('e49_numcgm', 13, $Ie60_numcgm, true, 'text', 1, "onchange='js_pesquisae49_numcgm(false)'"); ?> </td>
                            <td colspan=2><?php db_input('z01_credor', 52, $Iz01_nome, true, 'text', 3, ''); ?></td>
                        </tr>
                        <tr>
                            <td><?= db_ancora($Le60_coddot, "js_JanelaAutomatica('orcdotacao',\$F('e60_coddot'),'" . $e60_anousu . "')", $db_opcao_inf) ?></td>
                            <td nowrap><?php db_input('e60_coddot', 13, $Ie60_coddot, true, 'text', 3); ?></td>
                            <td width="20"><?= db_ancora($Lo15_codigo, "", 3) ?></td>
                            <td nowrap><?php db_input('o15_codigo', 5, $Io15_codigo, true, 'text', 3);
                                        db_input('o15_descr', 33, $Io15_descr, true, 'text', 3) ?></td>
                        </tr>
                        <tr id='controlepit' style='display: <?= $iControlaPit == 1 ? "" : "none" ?>'>
                            <td><b>Tipo da Entrada: </b></td>
                            <td colspan="4">
                                <?php
                                $oDaoDocumentoFiscais = new cl_tipodocumentosfiscal;
                                $rsDocs = $oDaoDocumentoFiscais->sql_record($oDaoDocumentoFiscais->sql_query(null, "*", "e12_sequencial"));
                                $aItens[0] = "selecione";
                                for ($i = 0; $i < $oDaoDocumentoFiscais->numrows; $i++) {
                                    $oItens = db_utils::fieldsMemory($rsDocs, $i);
                                    $aItens[$oItens->e12_sequencial] = $oItens->e12_descricao;
                                }
                                db_select('e69_tipodocumentofiscal', $aItens, true, 1, "onchange=js_abreNotaExtra()");
                                ?>
                                <a href='#' onclick='js_abreNotaExtra()' style='display: none' id='dadosnotacomplementar'>Outros Dados</a>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label class="bold" for="e03_numeroprocesso">Processo Administrativo:</label>
                            </td>
                            <td>
                                <?php db_input('e03_numeroprocesso', 13, '', true, 'text', $db_opcao, null, null, null, null, 15); ?>
                            </td>
                            <td class="regime_competencia" style="display: none">
                                <label for="competencia_regime"><b>Competência:</b></label>
                            </td>
                            <td class="regime_competencia" style="display: none">
                                <input id="competencia_regime" class="field-size3">
                            </td>
                        </tr>
                        <!--[PLUGIN] CONTRATO PADRS -->

                        <!--[Extensao OrdenadorDespesa] inclusao_ordenador-->

                        <?php if ($isRJ) : ?>
                            <tr>
                                <td>
                                    <label class="bold" for="competenciaFolhaPagamentolabel" id="competenciaFolhaPagamentolabel">Data Competência:</label>
                                </td>
                                <td>
                                    <?php db_input('competenciaFolhaPagamento', 13, '', true, 'text', $db_opcao, null, null, null, null, 15); ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </table>


                    <fieldset class="separator">
                        <legend>Nota</legend>

                        <table width="60%">

                            <!--[Extensao ContratosPADRS] serie nota -->

                            <tr id="linhaTipoNota" style="display: none">
                                <td><label for="tipoNota" class="bold">Tipo de Nota:</label></td>
                                <td colspan="3">
                                    <select id="tipoNota" nome="tipoNota" class="field-size-max">
                                    </select>
                                </td>
                            </tr>

                            <tr id="linhaNumeroChave" style="display: none">
                                <td><label for="numeroChave" class="bold">Número da Chave: </label></td>
                                <td colspan="3">
                                    <input type="text" id="numeroChave" name="numeroChave" class="field-size-max" maxlength="44">
                                </td>
                            </tr>
                            <tr id="linhaNumeroSerie" style="display: none">
                                <td><label for="numeroSerie" class="bold">Número de Série: </label></td>
                                <td colspan="3">
                                    <input type="text" id="numeroSerie" name="numeroSerie" class="field-size-max">
                                </td>
                            </tr>
                            <tr id="linhaNumeroNota">
                                <td nowrap>
                                    <label class="bold" for="e69_numnota">Número da Nota: </label>
                                </td>

                                <td>
                                    <?php db_input('e69_numnota', 13, null, true, 'text', 1, "onchange='js_verificaNota();'", null, null, null, 20); ?>
                                    <input id='btnNotasVinculadas' style="display: none ;" onclick="js_verificaNota();" title="Empenhos Vinculados à Nota" type="button" value="E" />
                                </td>

                                <td nowrap>
                                    <label class="bold" for="e69_dtnota">Data da Nota:</label>
                                </td>

                                <td>
                                    <div style="float: right;">
                                        <?php db_inputData('e69_dtnota', '', '', '', true, 'text', 1); ?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap>
                                    <label class="bold" for="e69_serienota">Série da NF:</label>
                                </td>

                                <td>
                                    <?php db_input('e69_serienota', 13, 1, true, 'text', 1, null, null, null, null, 20); ?>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap>
                                    <label class="bold" for="e69_dtrecebe">Data de Recebimento:</label>
                                </td>

                                <td>
                                    <?php db_inputData('e69_dtrecebe', '', '', '', true, 'text', 1); ?>
                                </td>

                                <td nowrap>
                                    <label class="bold" for="e69_dtvencimento">Data de Vencimento:</label>
                                </td>

                                <td>
                                    <div style="float: right;">
                                        <?php $Ne69_dtvencimento = ''; ?>
                                        <?php db_inputData('e69_dtvencimento', '', '', '', true, 'text', 1); ?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap>
                                    <label class="bold" for="e69_localrecebimento">Local de Recebimento:</label>
                                </td>

                                <td colspan="3">
                                    <?php db_input('e69_localrecebimento', 13, '', true, 'text', 1, 'style="width: 100%;"'); ?>
                                </td>
                            </tr>
                            <tr id="componente-arquivos">
                                <td nowrap>
                                    <label class="bold" for="arquivo_notafsical">Arquivo Nota Fiscal:</label>
                                </td>
                                <td colspan="3">
                                    <input type="file" name="arquivo" id="arquivo" multiple>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset id="field-arquivos">
                        <div id="arquivos-selecionados"></div>
                    </fieldset>
                    <fieldset id="infoSagres" class="separator" style="display: none">
                        <legend>Informações Sagres - TCE/PB</legend>
                        <table>
                            <tr>
                                <td>
                                <td nowrap>
                                    <label class="bold" for="codigo_agrupamento">Agrupamento da folha de pagamento:</label>
                                </td>
                                <td>
                                    <?php db_input('codigo_agrupamento', 8, 1, true, 'text', 1, "onkeypress='validaTamanho(event)'"); ?>
                                </td>
            </td>
        </tr>
    </table>
    </fieldset>
    <fieldset id="infoDiarias" class="separator d-none">
        <legend>Diárias</legend>
        <table>
            <tr>
                <td>
                    <label for="diariaRegist">
                        <b><a href="#" onclick="js_pesquisarh01_regist(true)">Matrícula: </a></b>
                    </label>
                </td>
                <td>
                    <input type="text" id="diariaRegist" size="10" onchange="js_pesquisarh01_regist(false)">
                    <input type="text" id="diariaNome" size="30" disabled>
                </td>
            </tr>
            <tr>
                <td><label for="diariaSaida"><b>Data de Saída:</b></label></td>
                <td><?php db_inputdata('diariaSaida', '', '', '', true, 'text', 1) ?>
                    <div style="<?= $exibirCamposAdicionaisRJ ? 'display:inline;' : 'display:none;'; ?>">
                        <label for="qtde"><b>Qtde:</b></label>
                        <?php db_input('qtde', 13, null, true, 'text', 1, "", null, null, null, 20); ?>
                    </div>
                </td>

            </tr>
            <tr>
                <td><label for="diariaRetorno"><b>Data de Retorno:</b></label></td>
                <td><?php db_inputdata('diariaRetorno', '', '', '', true, 'text', 1) ?></td>
            </tr>
            <tr>
                <td><label for="diariaTipo"></label><b><?= $labelDiariaTipo ?></b></td>
                <td>
                    <?php db_select("diariaTipo", $listaTiposDiaria, true, 1); ?>
                </td>
            </tr>
            <tr>
                <td><label for="diariaDestino"><b><?= $labelDiariaDestino ?></b></label></td>
                <td><input type="text" name="diariaDestino" id="diariaDestino" size="40"></td>
            </tr>
            <tr class="<?= $exibirCamposAdicionaisRJ ? '' : 'd-none'; ?>">
                <td nowrap>
                    <label class="bold" for="estadoDestino">Estado(s) de Destino:</label>
                </td>
                <td>
                    <?php
                    db_input('estadoDestino', 40, null, true, 'text', 1, "", null, null, null, 20);

                    $estados = "select db71_sigla, db71_descricao from cadenderestado where db71_cadenderpais = 1";
                    $rsEstados = db_query($estados);
                    db_selectrecord("listaEstados", $rsEstados, true, 1, null, null, null, null, null, 1);
                    ?>
                </td>
            </tr>

            <tr class="<?= $exibirCamposAdicionaisRJ ? '' : 'd-none'; ?>">
                <td nowrap>
                    <label class="bold" for="paisDestino">País de Destino:</label>
                </td>
                <td>
                    <?php db_input('paisDestino', 40, null, true, 'text', 1, "", null, null, null, 20); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <fieldset>
                        <legend><b><?= $labelObjetoDiaria ?></b></legend>
                        <textarea name="diariaDescr" id="diariaDescr" cols="60" rows="5"></textarea>
                    </fieldset>
                </td>
            </tr>
        </table>
    </fieldset>
    <fieldset id="infoSigfis" class="separator d-none" style="margin-top: 0.5rem;">
        <legend>Informações SIGFIS - TCE/RJ</legend>

        <!-- tipo documento  -->
        <div id="infoTipodocliquidacao" class="d-none">
            <table>
                <tr>
                    <td>
                        <label for="tipodocliquidacao">
                            <b>Tipo de documento: </b>
                        </label>
                        <select id="tipodocliquidacao">
                            <option value="" selected disabled>Selecione</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <!-- danfe -->
        <div id="infoDanfe" class="d-none">
            <fieldset>
                <legend>Danfe</legend>
                <table>
                    <tr>
                        <td>
                            <label for=""><b>Número: </b></label>
                            <input id="danfeNumero" type="text" />
                        </td>
                        <td>
                            <label for=""><b>Chave: </b></label>
                            <input id="danfeChave" type="text" pattern="" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>

        <!-- atestadores -->
        <div id="infoAtestadores" class="d-none"></div>
    </fieldset>
    </fieldset>
    </td>
    <td valign='top' style='padding:0px'>
        <fieldset>
            <legend><b>&nbsp;Valores do Empenho&nbsp;</b></legend>
            <table style="width:200px;height:100%">
                <tr>
                    <td nowrap><?= $Le60_vlremp ?></td>
                    <td align=right><?php db_input('e60_vlremp', 12, $Ie60_vlremp, true, 'text', 3, '', '', '', 'text-align:right') ?></td>
                </tr>
                <tr>
                    <td nowrap><?= $Le60_vlranu ?></td>
                    <td align=right><?php db_input('e60_vlranu', 12, $Ie60_vlranu, true, 'text', 3, '', '', '', 'text-align:right') ?></td>
                </tr>
                <tr>
                    <td nowrap><?= $Le60_vlrliq ?></td>
                    <td align=right><?php db_input('e60_vlrliq', 12, $Ie60_vlrliq, true, 'text', 3, '', '', '', 'text-align:right') ?></td>
                </tr>
                <tr>
                    <td nowrap><?= $Le60_vlrpag ?></td>
                    <td align=right><?php db_input('e60_vlrpag', 12, $Ie60_vlrpag, true, 'text', 3, '', '', '', 'text-align:right') ?></td>
                </tr>
                <!-- Extensao [CotaMensalLiquidacao] - Parte 1 -->
                <tr>
                    <td nowrap><b>Saldo</b></td>
                    <td align=right><?php db_input('saldodis', 12, 0, true, 'text', 3, '', '', '', 'text-align:right') ?></td>
                </tr>
            </table>
        </fieldset>
    </td>
    </tr>
    <tr>
        <td colspan='2' style='padding:0px'>
            <fieldset>
                <legend><b>&nbsp;Itens&nbsp;</b></legend>
                <div style='border:2px inset white'>
                    <table cellspacing=0 cellpadding=0 width='100%'>
                        <tr>
                            <th class='table_header'>
                                <input type='checkbox' style='display:none' id='mtodos' onclick='js_marca()'>
                                <a onclick='js_marca()' style='cursor:pointer'>M</a></b>
                            </th>
                            <th class='table_header' width='30%'>Material</th>
                            <th class='table_header'>Sequência</th>
                            <th class='table_header'>Valor Unitário</th>
                            <th class='table_header'>Quantidade</th>
                            <th class='table_header'>Valor Total</th>
                            <th class='table_header'>Quantidade <br>Entregue</th>
                            <th class='table_header'>Valor <br>Entregue</th>
                            <?php
                            if ($iTipoControleCustos > 0) {
                                echo "<th class='table_header'>Centro de Custo</th>";
                            }
                            ?>
                            <th class='table_header' style='width:18px'>&nbsp;</th>
                        </tr>
                        <tbody id='dados' style='height:150;width:95%;overflow:scroll;overflow-x:hidden;background-color:white'>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan='7' style='text-align:right' class='table_footer'>Valor Total:</th>
                                <th class='table_footer' id='valorTotalItens'>&nbsp;</th>
                                <?php if ($iTipoControleCustos > 1) {
                                    echo "<th class='table_footer' >&nbsp;</th>";
                                }
                                ?>
                                <th class='table_footer' style='width:18px'>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </td>
    </tr>
    <tr>
        <td colspan='2' style='padding:0px'>
            <fieldset>
                <legend><b>&nbsp;Histórico&nbsp;</b></legend>
                <table>
                    <tr>
                        <td>
                            <?php db_textarea('historico', 5, 200, 0, true, 'text', 1, "") ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'>

                        </td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>
    </table>

    <input name="confirmar" type="button" id="confirmar" value="Confirmar" onclick="return js_liquidar('<?= $metodo ?>')" disabled>
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    <input name="zeraritens" type="button" id="zeraritens" value="Zerar Itens" onclick="js_zeraItens();">
    <input name="preenche" type="button" id="preenche" value="Preencher Itens" onclick="js_preencheItens();">
    <input name="retencoes" type="button" id="retencoes" disabled value="Retenções" onclick="js_lancarRetencao();">
    <input name="iCodMov" type="hidden" id="e81_codmov" value="">
    <input name="iCodOrd" type="hidden" id="e50_codord" value="">
    <input name="iCodNota" type="hidden" id="e69_codnota" value="">

</form>

<div id='divDadosNotaAux' style='display:none; text-align: center;'>
    <table width="100%">
        <tr>
            <td>
                <fieldset>
                    <legend>
                        <b>Dados Complementares</b>
                    </legend>
                    <table>
                        <tr>
                            <td nowrap title="<?= $Te11_cfop ?>">
                                <?php db_ancora("<b>CPOF</b>", "js_pesquisae11_cfop(true);", $db_opcao); ?>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_cfop', 10, $Ie11_cfop, true, 'hidden', 3, " onchange='js_pesquisae11_cfop(false);'");
                                db_input('e10_cfop', 10, $Ie10_cfop, true, 'text', $db_opcao, " onchange='js_pesquisae11_cfop(false);'");
                                db_input('e10_descricao', 40, $Ie10_descricao, true, 'text', 3, '')
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Série:</b>
                            </td>
                            <td nowrap>
                                <?php db_input('e11_seriefiscal', 10, $Ie11_seriefiscal, true, 'text', 1, ''); ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Inscrição Subst.Fiscal:</b>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_inscricaosubstitutofiscal', 10, $Ie11_inscricaosubstitutofiscal, true, 'text', 1, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Base Calculo ICMS:</b>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_basecalculoicms', 10, $Ie11_basecalculoicms, true, 'text', 1, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Valor ICMS:</b>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_valoricms', 10, $Ie11_valoricms, true, 'text', 1, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Base Calculo ICMS Substituto:</b>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_basecalculosubstitutotrib', 10, $Ie11_basecalculosubstitutotrib, true, 'text', 1, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap>
                                <b>Valor ICMS Substituto:</b>
                            </td>
                            <td nowrap>
                                <?php
                                db_input('e11_valoricmssubstitutotrib', 10, $Ie11_valoricmssubstitutotrib, true, 'text', 1, '');
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td colspan="4" style='text-align: center'>
                <input type='button' value='Salvar Informações' onclick='windowAuxiliarNota.hide()'>
            </td>
        </tr>
    </table>
</div>
<script>
    const exibirCamposAdicionaisRJ = '<?= $exibirCamposAdicionaisRJ ?>' === '1';
    iTipoControle = <?= $iTipoControleCustos; ?>;
    iControlaPit = <?= $iControlaPit ?>;

    var lUsaPCASP = <?php echo $lUsaPCASP; ?>;
    var sDataSessao = '<?= $sData ?>';
    var isEmpenhoFolha = false;
    var UF = '<?= getEstadoInstituicao() ?>';
    var isPB = UF === 'PB'
    var isRJ = UF === 'RJ'
    var lEmpenhoDiaria = false;
    var isDespesaPessoal = false;

    $('e69_dtrecebe').value = '<?= $sData; ?>';
    $('e69_dtnota').value = '<?= $sData; ?>';
    var aParcelasRegimeCompetencia = [];

    const linhaTipoNota = document.getElementById('linhaTipoNota');
    const inputTipoNota = document.getElementById('tipoNota');
    const linhaNumeroChave = document.getElementById('linhaNumeroChave');
    const linhaNumeroNota = document.getElementById('linhaNumeroNota');
    const inputNumeroChave = document.getElementById('numeroChave');
    const linhaNumeroSerie = document.getElementById('linhaNumeroSerie');
    const inputNumeroSerie = document.getElementById('numeroSerie');
    const inputNumeroNota = document.getElementById('e69_numnota');
    const inputDataNota = document.getElementById('e69_dtnota');

    if (!isPB) {
        linhaNumeroNota.style.display = 'table-row';
    }

    if (isRJ) {
        const oInputCompetenciaFolha = new MaskedInput($('competenciaFolhaPagamento'), '99/9999');
    }

    const tiposNota = [];
    if (isPB) {
        inputTipoNota.addEventListener('change', () => {
            if (inputTipoNota.value == '') {
                return
            }

            let tipo = getTipoNota();
            linhaNumeroChave.style.display = 'none';
            linhaNumeroSerie.style.display = 'none';
            if (tipo.chave != 0) {
                linhaNumeroChave.style.display = 'table-row';
            }
            if (tipo.serie != 0) {
                linhaNumeroSerie.style.display = 'table-row';
            }

            if (tipo.data == 0) {
                inputDataNota.value = '';
            } else {
                $('e69_dtnota').value = '<?= $sData; ?>';
            }
        });
    }

    /**
     * Considera os tipos de nota da Paraíba
     * @returns {*}
     */
    const getTipoNota = () => {
        return tiposNota.filter((tipo) => {
            return tipo.id == inputTipoNota.value;
        }).shift();
    };

    function js_emitir(codordem) {
        jan = window.open('emp2_emitenotaliq002.php?codordem=' + codordem, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0');
        jan.moveTo(0, 0);
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_empempenho', 'func_empempenho.php?funcao_js=parent.js_preenchepesquisa|e60_numemp', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_empempenho.hide();
        js_consultaEmpenho(chave, <?= $operacao ?>);
    }

    function js_marca() {

        obj = document.getElementById('mtodos');
        if (obj.checked) {
            obj.checked = false;
        } else {
            obj.checked = true;
        }
        itens = js_getElementbyClass(form1, 'chkmarca');
        for (var i = 0; i < itens.length; i++) {
            if (itens[i].disabled == false) {
                if (obj.checked == true) {
                    itens[i].checked = true;
                    js_marcaLinha(itens[i]);
                } else {
                    itens[i].checked = false;
                    js_marcaLinha(itens[i]);
                }
            }
        }
    }

    function js_pesquisae11_cfop(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_cfop',
                'func_cfop.php?funcao_js=parent.js_mostracfop1|e10_sequencial|e10_descricao|e10_cfop',
                'Pesquisa CFOP', true);
        } else {
            if ($('e10_cfop').value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_cfop',
                    'func_cfop.php?pesquisa_chave=' + $('e10_cfop').value + '&funcao_js=parent.js_mostracfop',
                    'Pesquisa CFOP', false);
            } else {
                $('e10_descricao').value = '';
            }
        }
    }

    function js_mostracfop(chave, chave2, erro) {

        $('e10_descricao').value = chave;
        $('e11_cfop').value = chave2;
        if (erro == true) {
            $('e10_cfop').focus();
            $('e10_cfop').value = '';
        }
    }

    function js_mostracfop1(chave1, chave2, chave3) {

        $('e11_cfop').value = chave1;
        $('e10_descricao').value = chave2;
        $('e10_cfop').value = chave3;
        db_iframe_cfop.hide();

    }

    var lInformarCompetencia = false;

    function js_consultaEmpenho(iNumeroEmpenho, operacao) {
        lInformarCompetencia = false;
        js_divCarregando("Aguarde, efetuando pesquisa", "msgBox");
        strJson = '{"method":"getEmpenhos","pars":"' + iNumeroEmpenho + '","operacao":"1","itens":"1","iEmpenho":"' + iNumeroEmpenho + '"}';
        $('dados').innerHTML = '';
        $('e69_tipodocumentofiscal').value = 0;
        $('e11_valoricms').value = "";
        $('e11_valoricmssubstitutotrib').value = "";
        $('e11_basecalculosubstitutotrib').value = "";
        $('e11_basecalculoicms').value = "";
        $('e11_inscricaosubstitutofiscal').value = "";
        $('e11_cfop').value = "";
        $('e69_dtvencimento').value = "";
        //$('pesquisar').disabled = true;
        url = 'emp4_liquidacao004.php';
        oAjax = new Ajax.Request(
            url, {
                method: 'post',
                parameters: 'json=' + strJson,
                onComplete: js_saida
            }
        );
    }

    function js_criarCaixaHint(obj) {

        for (var i = 0; i < obj.data.length; i++) {

            var sTextEvent = "<b>Elemento: </b>" + obj.data[i].elemento + "</br>";

            if (obj.data[i].unidademed == '' || obj.data[i].unidademed == undefined) {
                sTextEvent += "<b>Unidade: </b> UNIDADE </br>";
            } else {
                sTextEvent += "<b>Unidade: </b>" + obj.data[i].unidademed + "</br>";
            }

            if (obj.data[i].resumo == '' || obj.data[i].resumo == undefined) {
                sTextEvent += "<b>Resumo: </b> Sem observação";
            } else {
                sTextEvent += "<b>Resumo: </b>" + obj.data[i].resumo.urlDecode();
            }

            var oDBHint = eval("oDBHint_" + 'descr' + [i + 1] + " = new DBHint('oDBHint_" + 'descr' + [i + 1] + "')");
            oDBHint.setText(sTextEvent);
            oDBHint.setShowEvents(["onmouseover"]);
            oDBHint.setHideEvents(["onmouseout"]);
            oDBHint.setPosition('B', 'L');
            oDBHint.setUseMouse(true);
            oDBHint.setWidth(350);
            oDBHint.make($('descr' + [i + 1]));
        }
    }

    /**
     * valida exibicao do aviso dos grupos de itens de consumo imediato
     */
    var iEmpenho = null;
    var lLiberaItemLiquidacao = true;

    /* Extensao [CotaMensalLiquidacao] - Parte 2 */

    function js_saida(oAjax) {

        js_removeObj("msgBox");
        obj = JSON.parse(oAjax.responseText);

        if (obj.lSuspenderLiquidacao == 't') {
            alert("Ficha financeira suspensa para liquidações.");
            js_pesquisa();
            return false;
        }

        if (obj.lEmpenhoRestoAPagar) {
            habilitaRegimeCompetencia(false);
            lInformarCompetencia = false;
        } else {
            habilitaRegimeCompetencia(obj.contratoPossuiRegimeCompetencia);
            lInformarCompetencia = obj.contratoPossuiRegimeCompetencia;
        }

        var sMensagemGrupoDesdobramento = null;

        if (obj.e60_numemp != iEmpenho) {
            lLiberaItemLiquidacao = true;
        }

        if (typeof obj.lAnteriorImplantacaoDepreciacao === "undefined") {
            obj.lAnteriorImplantacaoDepreciacao = false;
        }

        /**
         * Exibe aviso dos grupos 7,8,9 e 10 de desdobramentos de consumo imediato
         */
        if (obj.e60_numemp != iEmpenho && (lUsaPCASP == true || lUsaPCASP == 'true')) {
            /**
             * Para nao exibir mais de uma vez
             */
            if (obj.oGrupoElemento.iGrupo != "") {
                var sGrupo = obj.oGrupoElemento.sGrupo.urlDecode();

                switch (obj.oGrupoElemento.iGrupo) {
                    case "9":
                        if (!obj.lAnteriorImplantacaoDepreciacao) {
                            sMensagemGrupoDesdobramento = "O desdobramento deste empenho está no grupo " + sGrupo;
                            sMensagemGrupoDesdobramento += "\nPara desdobramentos deste grupo não é possivel liquidar através desta rotina.";
                            lLiberaItemLiquidacao = false;
                        }
                        break;
                    case "7":
                    case "8":
                    case "10":
                        sMensagemGrupoDesdobramento = _M('financeiro.empenho.emp4_empempenho004.liquidacao_item_consumo_imediato', {
                            sGrupo: sGrupo
                        });
                        break;
                }
            }
        }

        $('e60_codemp').value = obj.e60_codemp.urlDecode() + "/" + obj.e60_anousu.urlDecode();
        $('e60_numemp').value = obj.e60_numemp.urlDecode();
        $('e60_numcgm').value = obj.e60_numcgm.urlDecode();
        $('z01_nome').value = obj.z01_nome.urlDecode();
        $('e49_numcgm').value = '';
        $('z01_credor').value = '';
        $('e60_coddot').value = obj.e60_coddot.urlDecode();
        $('o15_codigo').value = obj.o58_codigo;
        $('o15_descr').value = obj.o15_descr.urlDecode();
        $('e60_vlremp').value = obj.e60_vlremp;
        $('e60_vlranu').value = obj.e60_vlranu;
        $('e60_vlrpag').value = obj.e60_vlrpag;
        $('e60_vlrliq').value = obj.e60_vlrliq;
        $('historico').value = obj.e60_resumo.urlDecode();
        $('saldodis').value = obj.saldo_dis;
        $('e69_numnota').value = 'S/N';
        $('e69_localrecebimento').value = obj.nomeInstituicao;

        if (isPB) {
            if (obj.isEmpenhoFolha && obj.necessitaCodigoAgrupamento) {
                $('infoSagres').show();
                isEmpenhoFolha = true;
            }
            linhaTipoNota.style.display = 'table-row';
            inputTipoNota.options.length = 0;
            inputTipoNota.add(new Option('Selecione', ''));

            for (let tipo of obj.tipoDeNotas) {
                inputTipoNota.add(new Option(tipo.label.urlDecode(), tipo.id));
                tiposNota.push(tipo);
            }
        } else {
            $('infoSagres').hide();
        }

        if (isRJ) {
            isDespesaPessoal = obj.isDespesaPessoal;
            if (isDespesaPessoal == true) {
                document.getElementById("competenciaFolhaPagamentolabel").style.display = "block";
                document.getElementById("competenciaFolhaPagamento").style.display = "block";
            } else {
                document.getElementById("competenciaFolhaPagamentolabel").style.display = "none";
                document.getElementById("competenciaFolhaPagamento").style.display = "none";
            }

            clearSigfis();
        }

        if (empty(obj.sClassificacao)) {
            obj.sClassificacao = "Empenho não classificado";
        }


        if (!empty(obj.sDataVencimento)) {
            $('e69_dtvencimento').value = obj.sDataVencimento.urlDecode();
        }

        $('codigo_classificacao').value = obj.iClassificacao;
        $('descricao_classificacao').value = obj.sClassificacao.urlDecode().toUpperCase();

        if (obj.lEmpenhoDiaria == true) {
            lEmpenhoDiaria = true;
            document.querySelector('#infoDiarias')
                .classList.remove('d-none');
            if (isRJ && lEmpenhoDiaria) {
                ajustarOption('tipodocliquidacao', '6');
            }
        } else {
            lEmpenhoDiaria = false;
            document.querySelector('#infoDiarias')
                .classList.add('d-none');
        }

        saida = '';
        $('dados').innerHTML = '';
        iTotItens = 0;

        var lBloquearItens = false;
        if (obj.e60_vlremp == obj.e60_vlrpag) {
            lBloquearItens = true;
        }

        if (obj.numnotas > 0) {

            for (i = 0; i < obj.data.length; i++) {

                var sDisabilitaQuantidade = '';
                var sDesabilitaValor = '';
                descrmater = obj.data[i].pc01_descrmater.replace(/\+/g, " ");
                descrmater = unescape(descrmater);
                sClassName = 'normal';
                if (obj.data[i].libera.trim() == "disabled" || !lLiberaItemLiquidacao) {
                    sClassName = 'disabled';
                } else {
                    iTotItens++;
                }
                if (obj.data[i].pc01_fraciona == 'f') {
                    var lFraciona = false;
                } else {
                    var lFraciona = true;
                }
                sDisabilitaValor = '';

                let servico = obj.data[i].pc01_servico == 't';
                let controlaQuantidade = servico && obj.data[i].servicoquantidade == "t" ? true : false;
                let codigo = obj.data[i].e62_sequen;
                let desabilitaGeral = '';

                if (servico && !controlaQuantidade) {
                    sDisabilitaQuantidade = 'disabled';
                }
                if (servico && controlaQuantidade) {
                    sDesabilitaValor = 'disabled';
                }

                if (lBloquearItens || !lLiberaItemLiquidacao) {
                    desabilitaGeral = 'disabled';
                }

                let alteraQuantidade = !servico || (servico && controlaQuantidade);

                saida += "<tr class='" + sClassName + "' id='trchk" + codigo + "' style='height:1em'>";
                saida += "  <td class='linhagrid' style='text-align:center'>";
                saida += "    <input type='checkbox' " + obj.data[i].libera + " onclick='js_marcaLinha(this)'";
                saida += "           class='chkmarca' name='chk" + codigo + "'";
                saida += "           id='chk" + codigo + "' value='" + codigo + "'>";
                saida += "  </td>";
                saida += "  <td class='linhagrid' id='descr" + codigo + "' style='text-align:left'>";
                saida += descrmater;
                saida += "  </td>";
                saida += "  <td class='linhagrid' style='text-align:right'>";
                saida += codigo;
                saida += "  </td>";
                saida += "  <td class='linhagrid' id='vlruni" + codigo + "' style='text-align:right'>";
                saida += obj.data[i].e62_vlrun;
                saida += "  </td>";
                saida += "  <td class='linhagrid' id='saldo" + codigo + "' style='text-align:right'>";
                saida += obj.data[i].saldo;
                saida += "  </td>";
                saida += "  <td class='linhagrid' id='saldovlr" + codigo + "' style='text-align:right'>"
                saida += obj.data[i].e62_vlrtot;
                saida += "  </td>";
                saida += "  <td class='linhagrid' style='text-align:center;width:10%'>";
                saida += "    <input type='text' name='qtdesol" + codigo + "'";
                saida += "           id='qtdesol" + codigo + "' " + sDisabilitaQuantidade;
                saida += "           value='" + obj.data[i].saldo + "' style='text-align:right'";
                saida += "           size='5' onkeypress='return js_validaFracionamento(event," + lFraciona + ",this)'";

                saida += `onblur='js_calculaValor(${codigo}, 1, ${alteraQuantidade})' ${desabilitaGeral}`;

                saida += "  </td>";
                saida += "  <td class='linhagrid' style='text-align:center;width:10%'>";
                saida += "    <input type='text' style='text-align:right' name='vlrtot" + codigo + "'";
                saida += "           id='vlrtot" + codigo + "'  value='" + obj.data[i].e62_vlrtot + "' " + sDesabilitaValor;
                saida += "           size='5' class='valores' onkeypress='return js_teclas(event)'";
                saida += `onblur='js_calculaValor(${codigo}, 2, ${alteraQuantidade})' ${desabilitaGeral}`;
                // saida += "          onblur='js_calculaValor("+codigo+",2)' "+obj.data[i].libera+">"
                saida += "  </td>";
                if (iTipoControle > 0) {

                    saida += "  <td class='linhagrid' id='custo" + codigo + "' style='text-align:left'>";
                    saida += "  <span id='cc08_sequencial" + codigo + "'></span>";
                    saida += "  <a id='cc08_descricao" + codigo + "' href='#' ";
                    saida += "     onclick='js_adicionaCentroCusto(" + codigo + "," + obj.data[i].e62_sequencial + ");";
                    saida += " return false'>Escolher</a>";
                    saida += "  </td>";

                }
                saida += "</tr>";
            }
        }
        saida += "<tr style='height:auto'><td>&nbsp;</td></tr>";
        $('dados').innerHTML = saida;

        $('pesquisar').disabled = false;
        if (iTotItens == 0 && lLiberaItemLiquidacao) {
            alert("Todos os Itens já foram liquidados, ou estão em ordem de compra.");
            $('confirmar').disabled = true;
        } else {
            if (!empty(sMensagemGrupoDesdobramento)) {
                alert(sMensagemGrupoDesdobramento);
            }

            $('confirmar').disabled = false;
            js_setValorTotal();
        }

        /**
         * Obj nao possui propriedade empenho, cria com valor da proprieade e60_numemp
         */
        if (!obj.hasOwnProperty('empenho') && obj.hasOwnProperty('e60_numemp')) {
            obj.empenho = obj.e60_numemp;
        }

        if (iEmpenho != obj.empenho) {

            $('retencoes').disabled = true;
            $('e81_codmov').value = "";
            $('e69_codnota').value = "";
            $('e50_codord').value = "";
        }

        iEmpenho = obj.e60_numemp;

        if (obj.eletronico === false) {
            var elemento = document.getElementById("componente-arquivos");
            elemento.style.display = "none";
            var elemento2 = document.getElementById("field-arquivos");
            elemento2.style.display = "none";
        }

        aParcelasRegimeCompetencia = obj.aParcelasRegimeCompetencia;

        js_criarCaixaHint(obj);
        alertaConfirmaData();
        /* Extensao [CotaMensalLiquidacao] - Parte 3 */
    }

    function js_marcaLinha(obj) {

        if (obj.checked) {
            $('tr' + obj.id).className = 'marcado';
        } else {
            $('tr' + obj.id).className = 'normal';
        }
        js_setValorTotal();
    }

    function js_liquidar(metodo) {
        itens = js_getElementbyClass(form1, 'chkmarca');
        notas = '';
        sV = '';
        if ($F('e69_dtnota') == '' && !isPB) {
            alert('Preencha a data da nota.');
            $('e69_dtnota').focus();
            return false;
        }
        if (isPB) {
            if ($F('codigo_agrupamento') == '' && isEmpenhoFolha) {
                alert("o código de agrupamento da folha deve estar preenchido")
                return false;
            }
        }

        if (isRJ && isDespesaPessoal) {
            if ($F('competenciaFolhaPagamento').trim() == "" || $F('competenciaFolhaPagamento').trim() == "/") {
                alert("Campo Data competência é de preenchimento obrigatório.");
                return false;
            }
        }

        $('pesquisar').disabled = true;
        $('confirmar').disabled = true;
        valorTotal = 0;
        var aNotas = new Array();
        for (var i = 0; i < itens.length; i++) {
            if (itens[i].checked == true) {

                if (js_strToFloat($("saldovlr" + itens[i].value).innerHTML) < $F('vlrtot' + itens[i].value)) {

                    sMsgErro = 'Item ' + itens[i].value + '(' + $("descr" + itens[i].value).innerHTML.trim() + ')';
                    sMsgErro += 'com valor total maior que o saldo disponivel.\nVerifique';
                    alert(sMsgErro);
                    $('pesquisar').disabled = false;
                    $('confirmar').disabled = false;
                    return false;
                }

                if (js_strToFloat($("saldo" + itens[i].value).innerHTML) < $F('qtdesol' + itens[i].value)) {

                    sMsgErro = 'Item ' + itens[i].value + '(' + $("descr" + itens[i].value).innerHTML.trim() + ')';
                    sMsgErro += 'com valor total maior que o saldo.\nVerifique';
                    alert(sMsgErro);
                    $('pesquisar').disabled = false;
                    $('confirmar').disabled = false;
                    return false;
                }

                if ($F('qtdesol' + itens[i].value) <= 0 || $F('vlrtot' + itens[i].value) <= 0) {
                    alert("Valor do item " + itens[i].value + "(" + $('descr' + itens[i].value).innerHTML.trim() + ") inválido.");
                    $('pesquisar').disabled = false;
                    $('confirmar').disabled = false;
                    return false;

                }
                var iCodigoCriterioCusto = "";
                /*
                 * controlamos se deve ser solicitado o centro de custo para o item.
                 * iTipoControle = 2 Uso Obrigatorio.
                 *                 1 uso nao obrigatorio
                 *                 0 Nao usa
                 */
                if (iTipoControle == 2) {
                    if ($('cc08_sequencial' + itens[i].value).innerHTML.trim() == "") {
                        alert("Item " + itens[i].value + "(" + $('descr' + itens[i].value).innerHTML.trim() + ") sem centro de custo Informado");
                        $('pesquisar').disabled = false;
                        $('confirmar').disabled = false;
                        return false;

                    }
                    iCodigoCriterioCusto = $('cc08_sequencial' + itens[i].value).innerHTML.trim();
                } else if (iTipoControle == 1) {
                    iCodigoCriterioCusto = $('cc08_sequencial' + itens[i].value).innerHTML.trim();
                }


                /*

                 JSON escrito manualmente. Alterado para objeto Javascript e então passado para o RPC com o Object.toJSON()

                 notas += sV+'{"sequen":"'+itens[i].value+'","quantidade":"'+$F('qtdesol'+itens[i].value)+'","vlrtot":"';
                 notas += $F('vlrtot'+itens[i].value)+'","vlruni":"'+$('vlruni'+itens[i].value).innerHTML+'",';
                 notas += '"iCodigoCriterioCusto":'+iCodigoCriterioCusto+'}';
                 */

                var oDadosNota = new Object();
                oDadosNota.sequen = itens[i].value;
                oDadosNota.quantidade = $F('qtdesol' + itens[i].value);
                oDadosNota.vlrtot = $F('vlrtot' + itens[i].value);
                oDadosNota.vlruni = $('vlruni' + itens[i].value).innerHTML;
                oDadosNota.iCodigoCriterioCusto = iCodigoCriterioCusto;
                aNotas.push(oDadosNota);

                sV = ",";
                valorTotal += new Number($F('vlrtot' + itens[i].value));
                valorTotal = valorTotal.toFixed(2);
            }
        }

        if (!validarCompetencia()) {
            $('pesquisar').disabled = false;
            $('confirmar').disabled = false;
            return false;
        }

        if (!outrasValidacoes()) {
            $('pesquisar').disabled = false;
            $('confirmar').disabled = false;
            return false;
        }

        if (!$F(e03_numeroprocesso) && UF === 'RJ') {
            $('pesquisar').disabled = false;
            $('confirmar').disabled = false;
            alert('Processo Administrativo deve ser informado.');
            return false;
        }

        /**
         * Sigfis Validacoes
         */
        if (UF === 'RJ') {
            if (!js_validaTidocliquidacao()) {
                $('pesquisar').disabled = false;
                $('confirmar').disabled = false;
                return false;
            }

            if (!js_validaAtestadores()) {
                $('pesquisar').disabled = false;
                $('confirmar').disabled = false;
                return false;
            }
        }

        if (aNotas.length != 0) {
            if (valorTotal > js_strToFloat($F('saldodis'))) {
                var sErroMsg = "Você está tentando liquidar um valor superior ao saldo disponível.\n";
                sErroMsg += "Verifique os dados constantes em cada item da nota fiscal do credor,\n";
                sErroMsg += "pois podem haver diferenças em quantidades ou mesmo arredondamento no cálculo do valor total.";
                alert(sErroMsg);
                $('pesquisar').disabled = false;
                $('confirmar').disabled = false;
                return false;

            }
            var iTipoDocumentoFiscal = $F('e69_tipodocumentofiscal');
            var iCfop = $F('e11_cfop');
            var iInscrSubstituto = $F('e11_inscricaosubstitutofiscal');
            var nBaseCalculoICMS = $F('e11_basecalculoicms');
            var nValorICMS = $F('e11_valoricms');
            var nBaseCalculoSubst = $F('e11_basecalculosubstitutotrib');
            var nValorICMSSubst = $F('e11_valoricmssubstitutotrib');
            var sSerieFiscal = $F('e11_seriefiscal');
            if (iTipoDocumentoFiscal == 0 && iControlaPit == 1) {

                alert('Informe o Tipo da Nota Fiscal');
                $('pesquisar').disabled = false;
                $('confirmar').disabled = false;
                return false;

            }

            if (iControlaPit == 1) {
                /**
                 * Caso o documento fiscal for do tipo 50, devemos obrigar o usuário
                 * a selecionar uma cfop
                 */
                if (iTipoDocumentoFiscal == '50') {
                    if (iCfop == "") {
                        alert('Campo cfop Deve ser preenchido!');
                        js_abreNotaExtra();
                        $('pesquisar').disabled = false;
                        $('confirmar').disabled = false;
                        return false;

                    }
                }
            } else {

                /**
                 * senao é controlado o pit, tipo do documento fiscal = 4 - Outros
                 */
                iTipoDocumentoFiscal = 4;
            }

            /* Extensao [CotaMensalLiquidacao] - Parte 4 */

            js_divCarregando("Aguarde, Liquidando Empenho ", "msgLiq");

            var eTipoInstrumentoContratual = document.getElementById('tipo_instrumento_contratual');
            var oParam = {};
            var oInfoNota = {};

            oInfoNota.iCfop = iCfop;
            oInfoNota.iTipoDocumentoFiscal = iTipoDocumentoFiscal;
            oInfoNota.iInscrSubstituto = iInscrSubstituto;
            oInfoNota.nBaseCalculoICMS = nBaseCalculoICMS;
            oInfoNota.nValorICMS = nValorICMS;
            oInfoNota.nBaseCalculoSubst = nBaseCalculoSubst;
            oInfoNota.nValorICMSSubst = nValorICMSSubst;
            oInfoNota.sSerieFiscal = sSerieFiscal;

            oParam.chaveNota = inputNumeroChave.value;
            oParam.serieNota = inputNumeroSerie.value;
            oParam.e69_nota = $F('e69_numnota');
            oParam.e69_dtnota = $F('e69_dtnota');
            oParam.e69_dtrecebe = $F('e69_dtrecebe');
            oParam.e69_dtvencimento = $F('e69_dtvencimento');
            oParam.e69_serienota = $F('e69_serienota');
            oParam.e69_localrecebimento = encodeURIComponent(tagString($F('e69_localrecebimento')));
            oParam.method = "geraOC";
            oParam.valorTotal = valorTotal;
            oParam.competencia = getValorCompetencia();
            oParam.iEmpenho = $F('e60_numemp');
            oParam.notas = aNotas;
            oParam.historico = encodeURIComponent(tagString($F("historico")));
            oParam.pars = $F('e60_numemp');
            oParam.z01_credor = $F('e49_numcgm');
            oParam.e03_numeroprocesso = encodeURIComponent($F('e03_numeroprocesso'));

            if (isPB) {
                let outrosDados = {}
                outrosDados.codigo_agrupamento = $('codigo_agrupamento').value;
                oParam.outrosDados = js_objectToJson(outrosDados);

                oParam.outrosDadosNota = JSON.stringify({
                    "tipo_nota": inputTipoNota.value,
                    "chave_nota": inputNumeroChave.value,
                    "serie_nota": inputNumeroSerie.value
                });
            }

            oParam.oInfoNota = oInfoNota;

            if (eTipoInstrumentoContratual) {
                oParam.tipo_instrumento_contratual = eTipoInstrumentoContratual.value;
            }

            oParam.competenciaPessoal = null;
            if (isRJ && isDespesaPessoal) {
                oParam.competenciaPessoal = $F('competenciaFolhaPagamento');
            }

            oParam.uploadNota = fileList;

            if (lEmpenhoDiaria) {
                oParam.lEmpenhoDiaria = true;
                oParam.oInfoDiaria = {
                    'saida': $F('diariaSaida'),
                    'retorno': $F('diariaRetorno'),
                    'tipo': $F('diariaTipo'),
                    'destino': encodeURIComponent(tagString($F('diariaDestino'))),
                    'descr': encodeURIComponent(tagString($F('diariaDescr'))),
                    'regist': $F('diariaRegist')
                };

                if (exibirCamposAdicionaisRJ) {
                    oParam.oInfoDiaria.qtde = $F('qtde');
                    oParam.oInfoDiaria.estadoDestino = $F('estadoDestino');
                    oParam.oInfoDiaria.paisDestino = $F('paisDestino');
                }
            }

            // sigfis
            if (isRJ) {
                // atestadores
                if (oDBLancadorAtestadores.getRegistros().length) {
                    oParam.atestadores = oDBLancadorAtestadores.getRegistros();
                }

                // tipo documento
                if ($F('tipodocliquidacao')) {
                    oParam.tipodocliquidacao = $F('tipodocliquidacao');
                }

                // danfe
                if ($F('danfeNumero') || $F('danfeChave')) {
                    oParam.outrosDadosNota = JSON.stringify({
                        "danfe": {
                            "numero": $F('danfeNumero'),
                            "chave": $F('danfeChave')
                        }
                    });
                }
            }

            url = 'emp4_liquidacao004.php';
            oAjax = new Ajax.Request(
                url, {
                    method: 'post',
                    parameters: 'json=' + js_objectToJson(oParam),
                    onComplete: js_saidaLiquidacao
                }
            );
        } else {
            alert('Selecione ao menos 1 (uma) nota para liquidar');
            $('pesquisar').disabled = false;
            $('confirmar').disabled = false;

        }
    }

    function limparCampos() {

        var aDataSessao = sDataSessao.split('/');

        $('e03_numeroprocesso').value = '';
        $('e69_dtrecebe').value = sDataSessao;
        $('e69_dtnota').value = sDataSessao;
        // $('e69_dtnota_dia').value = aDataSessao[0];
        // $('e69_dtnota_mes').value = aDataSessao[1];
        // $('e69_dtnota_ano').value = aDataSessao[2];
        // $('e69_dtrecebe_dia').value = aDataSessao[0];
        // $('e69_dtrecebe_mes').value = aDataSessao[1];
        // $('e69_dtrecebe_ano').value = aDataSessao[2];
        $('e69_dtnota').value = '';
        $('e69_localrecebimento').value = '';
    }

    function js_saidaLiquidacao(oAjax) {

        js_removeObj("msgLiq");
        $('pesquisar').disabled = false;
        $('confirmar').disabled = false;
        obj = JSON.parse(oAjax.responseText);
        mensagem = obj.mensagem.replace(/\+/g, " ");
        mensagem = unescape(mensagem);
        if (obj.erro == 2) {
            alert(mensagem);
        }
        if (obj.erro == 1) {

            limparCampos();
            msgConfirm = "A Ordem de Pagamento " + obj.e50_codord + " foi gerada.\nDeseja Visualiza-la?";
            if (isPB) {
                msgConfirm = "A Nota de Liquidação" + obj.e50_codord + " foi gerada.\nDeseja Visualiza-la?";
            }
            if (confirm(msgConfirm)) {
                js_emitir(obj.e50_codord);
                iCodigoOrdemPagamento = obj.e50_codord;
            }

            js_consultaEmpenho($F('e60_numemp'), <?= $operacao ?>);
            $('retencoes').disabled = false;
            $('e81_codmov').value = obj.iCodMov;
            $('e50_codord').value = obj.e50_codord;
            $('e69_codnota').value = obj.iCodNota;
        }
    }

    function js_calculaValor(id, tipo, alteraQuantidade) {
        nVlrUni = new Number($('vlruni' + id).innerHTML);
        nQtde = new Number($F('qtdesol' + id));
        nVlrTotal = new Number($F('vlrtot' + id));
        iSaldo = new Number($('saldo' + id).innerHTML);
        iSaldovlr = new Number($('saldovlr' + id).innerHTML);
        if (tipo == 1) {
            nTotal = (nVlrUni * nQtde);
            nTotal = new Number(nTotal);
            if ((nQtde <= iSaldo)) {
                if (nTotal > 0) {
                    $('vlrtot' + id).value = nTotal.toFixed(2);
                    $('confirmar').disabled = false;
                    if ($('chk' + id).checked == false) {
                        $('chk' + id).click();
                    } else {
                        js_setValorTotal();
                    }
                }
            } else {
                alert("Valor total maior que o saldo restante.");
                $('confirmar').disabled = true;
            }
        } else if (tipo == 2) {
            $('pesquisar').disabled = false;
            $('confirmar').disabled = false;
            if (iSaldo != 0) {
                nTotal = new Number(nVlrTotal / nVlrUni);
                if ((nVlrTotal <= iSaldovlr)) {
                    if (nVlrTotal > 0) {
                        if (alteraQuantidade) {
                            $('qtdesol' + id).value = nTotal;
                            alert('Você está alterando diretamente o valor do item sem ajustar a quantidade.\n' +
                                'O sistema fará a alteração automática e proporcionalmente ao valor parcial informado.');
                        }
                        $('confirmar').disabled = false;
                        if ($('chk' + id).checked == false) {
                            $('chk' + id).click();
                        } else {
                            js_setValorTotal();
                        }
                    } else {
                        $('chk' + id).checked = false;
                        alert("Valor total deve ser maior que zero.");
                        $('confirmar').disabled = true;
                    }
                } else {
                    alert("Valor total maior que o saldo restante.");
                    $('confirmar').disabled = true;
                }
            }
        }
    }

    //zera os itens (valor, e quantidade )do empenho
    function js_zeraItens() {

        sMsg = 'Esta rotina ira zerar os valores lançados.';
        sMsg += '\nTodas a alterações serão perdidas.';
        if (confirm(sMsg)) {

            itens = js_getElementbyClass(form1, 'chkmarca');
            for (var iInd = 0; iInd < itens.length; iInd++) {

                if (!itens[iInd].disabled) {

                    $('vlrtot' + itens[iInd].value).value = 0;
                    $('qtdesol' + itens[iInd].value).value = 0;

                }
            }
            js_setValorTotal();
        }
    }

    //preenche os itens (valor, e quantidade )do empenho com o saldo Atual
    function js_preencheItens() {

        sMsg = 'Esta rotina ira preencher os valores dos itens com seus saldos atuais.';
        sMsg += '\nTodas a alterações serão perdidas.';
        if (confirm(sMsg)) {

            itens = js_getElementbyClass(form1, 'chkmarca');
            for (iInd = 0; iInd < itens.length; iInd++) {
                if (!itens[iInd].disabled) {

                    $('vlrtot' + itens[iInd].value).value = $('saldovlr' + itens[iInd].value).innerHTML.trim();
                    $('qtdesol' + itens[iInd].value).value = $('saldo' + itens[iInd].value).innerHTML.trim();

                }
            }
            js_setValorTotal();
        }
    }

    function js_pesquisae49_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cgm', 'func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome',
                'Consulta CGM', true);
        } else {
            if (document.form1.e49_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cgm',
                    'func_nome.php?pesquisa_chave=' + document.form1.e49_numcgm.value +
                    '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
            } else {
                document.form1.z01_credor.value = '';
            }
        }
    }

    function js_mostracgm(erro, chave) {
        document.form1.z01_credor.value = chave;
        if (erro == true) {
            document.form1.e49_numcgm.focus();
            document.form1.e49_numcgm.value = '';
        }
    }

    function js_mostracgm1(chave1, chave2) {
        document.form1.e49_numcgm.value = chave1;
        document.form1.z01_credor.value = chave2;
        db_iframe_cgm.hide();
    }

    function js_setValorTotal() {

        var aItens = js_getElementbyClass(form1, 'chkmarca', "checked==true");
        var nTotal = new Number;
        for (var iInd = 0; iInd < aItens.length; iInd++) {

            if (!aItens[iInd].disabled) {
                nTotal += new Number($('vlrtot' + aItens[iInd].value).value);
            }
        }
        if (nTotal > js_strToFloat($F('saldodis'))) {
            $('valorTotalItens').style.color = "#FF0000";
        } else {
            $('valorTotalItens').style.color = "#000000";
        }
        $('valorTotalItens').innerHTML = js_formatar(nTotal, 'f');
    }

    function js_lancarRetencao() {

        var lSession = "false";
        var iCodOrd = $F('e50_codord');
        var iCodMov = $F('e81_codmov');
        var iCodNota = $F('e69_codnota');
        var iNumEmp = $F('e60_numemp');
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_retencao',
            'emp4_lancaretencoes.php?iNumNota=' + iCodNota +
            '&iNumEmp=' + iNumEmp + '&iCodOrd=' + iCodOrd + "&lSession=" + lSession +
            '&iCodMov=' + iCodMov + '&callback=true',
            'Lancar Retenções', true);

    }


    function js_atualizaValorRetencao(iCodMov, nValor, iNota, iCodOrdem, lFechar = true) {

        if (lFechar) {
            db_iframe_retencao.hide();
        }
        if (nValor > 0) {
            if (confirm("As retenções lançadas alteraram o valor líquido da OP " + iCodOrdem + ". Deseja reimprimir?")) {
                js_emitir(iCodOrdem);
            }
        }

    }

    function js_adicionaCentroCusto(iLinha, iCodItem) {

        var iOrigem = 1;
        var iEmpenho = $F('e60_numemp');
        var sUrl = 'iOrigem=' + iOrigem + '&iNumEmp=' + iEmpenho + '&iCodItem=' + iCodItem + '&iCodigoDaLinha=' + iLinha;
        js_OpenJanelaIframe('',
            'db_iframe_centroCusto',
            'cus4_escolhercentroCusto.php?' + sUrl,
            'Centro de Custos',
            true,
            '25',
            '1',
            (document.body.scrollWidth - 10),
            (document.body.scrollHeight - 100)
        );


    }

    function js_completaCustos(iCodigo, iCriterio, iDescr) {

        $('cc08_sequencial' + iCodigo).innerHTML = iCriterio;
        $('cc08_descricao' + iCodigo).innerHTML = iDescr;
        db_iframe_centroCusto.hide();

    }

    function js_abreNotaExtra() {

        if ($F('e69_tipodocumentofiscal') == 50) {

            if (!$('wnddadosnota')) {
                js_createJanelaDadosComplentar();
            }

            windowAuxiliarNota.show(100, 300);
            $('dadosnotacomplementar').style.display = '';
            $('e10_cfop').focus();
        } else {

            $('dadosnotacomplementar').style.display = 'none';

            if ($('wnddadosnota')) {
                $('dadosnotacomplementar').style.display = 'none';
                windowAuxiliarNota.hide();
            }

            /*
             if (windowAuxiliarNota) {
             windowAuxiliarNota.hide();
             }
             */
        }

        if ($F('e69_tipodocumentofiscal') == 0) {
            $('e69_numnota').readOnly = true;
            $('e69_numnota').style.background = "#DEB887";
            $('e69_numnota').value = "";
        } else {
            $('e69_numnota').readOnly = false;
            $('e69_numnota').style.background = "#FFFFFF";
        }
        js_validarNumeroNota()

    }

    function js_createJanelaDadosComplentar() {

        windowAuxiliarNota = new windowAux('wnddadosnota', 'DadosComplementares', 600, 500);
        windowAuxiliarNota.setObjectForContent($('divDadosNotaAux'));
        $('dadosnotacomplementar').style.display = '';

    }



    function js_validarNumeroNota() {
        if ($F('e69_tipodocumentofiscal') == 50) {
            $('e69_numnota').value = '';
            $('e69_numnota').observe("keypress", function(event) {
                var lValidar = js_mask(event, "0-9");
                if (!lValidar) {
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                } else {
                    return true;
                }
            });
        } else {
            $('e69_numnota').stopObserving("keypress");
        }
    }


    function habilitaRegimeCompetencia(lHabilitar) {

        $('competencia_regime').value = '';
        for (oCampo of $$('td.regime_competencia')) {
            oCampo.style.display = lHabilitar ? '' : 'none';
        }
    }


    var oInputCompetencia = new MaskedInput($('competencia_regime'), '99/9999', {
        placeholder: '_'
    });

    function validarCompetencia() {

        if (lInformarCompetencia) {
            competencia = getValorCompetencia();

            var aPartesCompetencia = competencia.split("/");
            var oMesCompetencia = new Number(aPartesCompetencia[0]);
            var iAnoCompetencia = aPartesCompetencia[1];

            if ((oMesCompetencia.valueOf() < 1) || (oMesCompetencia.valueOf() > 12)) {

                alert('O mês da competência informada é invalida!');
                return false;
            }

            if (iAnoCompetencia.length != 4) {
                alert('Ano da competência está inválido!');
                return false;
            }

            var lCompetenciaValida = false;

            aParcelasRegimeCompetencia.each(function(oParcela) {

                var oMesParcela = new Number(oParcela.mes);
                var oAnoParcela = new Number(oParcela.ano);

                if (oMesCompetencia.valueOf() == oMesParcela.valueOf() && iAnoCompetencia == oAnoParcela.valueOf()) {
                    lCompetenciaValida = true;
                }
            });

            if (!lCompetenciaValida && aParcelasRegimeCompetencia.length > 0) {

                var sMensagem = "Nenhuma parcela, correspondente a competência informada, foi encontrada na Programação do Regime";
                sMensagem += " de Competência do acordo. Para verificar as parcelas, acesse:";
                sMensagem += "\n\n- DB:PATRIMONIAL > Contratos > Procedimentos > Regime de Competência > Programação,";
                sMensagem += " informando o acordo vinculado ao Empenho.";

                alert(sMensagem);
                return false;
            }
        }
        return true;
    }

    function getValorCompetencia() {

        var oCampoCompetencia = $F('competencia_regime');
        valorCompetencia = oCampoCompetencia.replace(/_/g, '');
        return valorCompetencia;

    }

    const exibeEmpenhosDaNota = (empenhos) => {

        var sNota = $F("e69_numnota");
        $("btnNotasVinculadas").style.display = "";
        windowNotasEmpenho = new windowAux('wnddadosnota', 'Empenhos Vinculados a Nota: ' + sNota, 600, 400);
        var quebra = 1;

        var total = empenhos.length;
        var sContent = "";
        sContent += "<fieldset>";
        sContent += "  <legend><strong>Empenhos Vinculados</strong></legend>";
        sContent += "  <div id='ctnVinculos' style='background-color: white; height: 300px; overflow: auto;'>";

        sContent += "   <table>";
        sContent += "     <tr>";

        for (i = 0; i < total; i++) {

            empenho = empenhos[i];
            sContent += "       <td style='width:105px'>" + empenho + "</td>";
            if (quebra == 7) {

                sContent += "</tr>";
                quebra = 0;
            }

            quebra++;
        }

        sContent += "    </table>";
        sContent += "  </div>";
        sContent += "</fieldset>";

        windowNotasEmpenho.setContent(sContent);
        windowNotasEmpenho.show(100, 300);
    }

    function js_verificaNota() {

        var sNota = $F('e69_numnota');
        if (sNota != '') {

            js_divCarregando("Aguarde, Validando nota", "msgNota");

            url = 'emp4_liquidacao004.php';

            var oParametros = new Object();
            oParametros.method = 'verificaNota';
            oParametros.sNota = encodeURIComponent(tagString(sNota));
            oParametros.iCgmFornecedor = $F('e60_numcgm');
            oParametros.m51_codordem = "";
            oParametros.iEmpenho = $F("e60_numemp");

            var oAjaxLista = new Ajax.Request(url, {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: js_retornoVerificaNota
            });

        }
    }

    function js_retornoVerificaNota(oAjax) {

        js_removeObj("msgNota");

        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {
            exibeEmpenhosDaNota(oRetorno.sEmpenho);
        } else {
            $("btnNotasVinculadas").style.display = "none";
        }
    }

    const validaTamanho = (event) => {
        let codigo = $F('codigo_agrupamento');
        codigo += event.key;
        if (codigo.length > 8) {
            alert("O código de agrupamento deve ter no máximo 8 dígitos");
            event.preventDefault();
        }
    }

    const validacoesCampoParaiba = () => {
        let tipo = getTipoNota();
        if (tipo === undefined) {
            alert(`Você deve selecionar o "Tipo de Nota".`);
            return false;
        }

        let label = tipo.label.urlDecode();
        if (tipo.chave == 1 && inputNumeroChave.value == '') {
            alert(`A chave da Nota é obrigatória para o tipo "${label}"`);
            return false;
        }

        if (tipo.chave == 1 && inputNumeroChave.value.length != 44) {
            alert(`A chave da Nota deve ter 44 caractéres."`);
            return false;
        }

        if (tipo.numero == 1 && (inputNumeroNota.value == '' || inputNumeroNota.value == 'S/N')) {
            alert(`O número da Nota é obrigatória para o tipo "${label}"`);
            return false;
        }
        if (tipo.serie == 1 && inputNumeroSerie.value == '') {
            alert(`O número de Série é obrigatória para o tipo "${label}"`);
            return false;
        }
        if (tipo.data == 1 && inputDataNota.value == '') {
            alert(`A Data da Nota é obrigatória para o tipo "${label}"`);
            return false;
        }
        if (tipo.chave == 0 && inputNumeroChave.value != '') {
            alert(`A chave da Nota não pode ser informada para o tipo "${label}"`);
            return false;
        }
        if (tipo.numero == 0 && (inputNumeroNota.value != '' && inputNumeroNota.value != 'S/N')) {
            alert(`O número da Nota não pode ser informada para o tipo "${label}"`);
            return false;
        }
        if (tipo.serie == 0 && inputNumeroSerie.value != '') {
            alert(`O número de Série não pode ser informado para o tipo "${label}"`);
            return false;
        }
        if (tipo.data == 0 && inputDataNota.value != '') {
            alert(`A Data da Nota não pode ser informada para o tipo "${label}"`);
            return false;
        }
        return true;
    };

    const outrasValidacoes = () => {
        if (isPB) {
            return validacoesCampoParaiba();
        }

        if (lEmpenhoDiaria) {
            return js_validaDiaria();
        }

        return true;
    };

    function alertaConfirmaData() {
        <?php
        $clconparametro = new cl_conparametro();
        $rsconparametro = $clconparametro->sql_record($clconparametro->sql_query_file(null, "c90_confirmadata"));
        $conparametro = db_utils::fieldsMemory($rsconparametro, 0);
        ?>
        let c90_confirmadata = '<?php echo $conparametro->c90_confirmadata ?>'
        if (c90_confirmadata == 't') {
            const msg = "Antes de realizar a operação confirme a data em que deseja incluir o movimento";
            alert(msg)
        }
    }

    /**
     * Funcoes referentes a Epenhos de diaria
     **/

    function js_pesquisarh01_regist(mostra) {
        const func = 'func_rhpessoal.php?funcao_js=';
        const chave = $F('diariaRegist');
        const instit = <?= (db_getsession("DB_instit")); ?>

        const diariaRegist = document.querySelector('#diariaRegist');
        const diariaNome = document.querySelector('#diariaNome');

        let funcao_js = '';
        let params = '';

        if (mostra == true) {
            funcao_js = 'parent.js_mostrapessoal1|rh01_regist|z01_nome';
            params = `&lTodos=true`;

        } else {
            if (chave != '') {
                funcao_js = 'parent.js_mostrapessoal';
                params = `&pesquisa_chave=${chave}&lTodos=true`;
            } else {
                diariaNome.value = '';
            }
        }

        js_OpenJanelaIframe('',
            'db_iframe_rhpessoal',
            `${func}${funcao_js}${params}`,
            'Pesquisa',
            mostra
        );
    }

    function js_mostrapessoal(chave, erro) {
        diariaNome.value = chave;
        if (erro == true) {
            diariaRegist.focus();
            diariaRegist.value = '';
        }
    }

    function js_mostrapessoal1(chave1, chave2) {
        diariaRegist.value = chave1;
        diariaNome.value = chave2;
        db_iframe_rhpessoal.hide();
    }

    function js_validaDiaria() {
        // matriucla
        if (!$F('diariaRegist')) {
            alert('[Diária] Matrícula não informada.');
            return false;
        }

        // datas
        if (!$F('diariaSaida') || !$F('diariaRetorno')) {
            alert('[Diária] Data de saída e retorno deverão ser informadas');
            return false;
        }

        // destino
        if (!$F('diariaDestino')) {
            alert('[Diária] Destino deve ser informado.');
            return false;
        }

        // descricao
        if (!$F('diariaDescr')) {
            alert('[Diária] Descrição deve ser informado.');
            return false;
        }

        if (exibirCamposAdicionaisRJ) {
            return js_validaDiariaCamposAdicionaisRJ();
        }

        return true;
    }

    function js_validaDiariaCamposAdicionaisRJ() {
        // qtde
        if (!$F('qtde')) {
            alert('[Qtde] não informada.');
            return false;
        }

        // estadoDestino
        if (!$F('estadoDestino')) {
            alert('[Estado de destino] não informado.');
            return false;
        }

        //paisDestino
        if (!$F('paisDestino')) {
            alert('[País de destino] não informado.');
            return false;
        }
        return true;
    }

    /**
     * Sessao destinada ao SIGFIS TCE/RJ
     **/
    if (isRJ) {

        // Elementos
        const infoSigfisEl = document.querySelector('#infoSigfis');
        const infoAtestadoresEl = document.querySelector('#infoAtestadores');
        const infoTipodocliquidacao = document.querySelector('#infoTipodocliquidacao');
        const tipodocliquidacao = document.querySelector('#tipodocliquidacao');
        const infoDanfe = document.querySelector('#infoDanfe');

        infoSigfisEl.classList.remove('d-none');

        // Preeche tipo de documento liquidacao
        const getTipodocliquidacao = async () => {
            const url = 'v4/api/financeiro/empenho/sigfis/tipodocliquidacao';
            const request = await CurrentWindow.axios(url);
            const {
                data: response
            } = request;
            const {
                data: responseData
            } = response;

            if (responseData.message == true) {
                alert('Erro ao buscar tipos de documento de liquidacao.');
                return false;
            }

            infoTipodocliquidacao.classList.remove('d-none');

            const tipos = responseData.tipos;
            tipos.forEach(item => {
                const option = document.createElement('option');

                option.innerHTML = item.e177_descr;
                option.value = item.e177_sequencial;
                option.dataset.codigo = item.e177_codigo;
                option.dataset.decimo = item.e177_decimo;
                option.dataset.tipodiverso = item.e177_tipodiverso;

                tipodocliquidacao.appendChild(option);
            });
        }

        getTipodocliquidacao();

        // Lancador Atestadores
        var oDBLancadorAtestadores = new DBLancador('oDBLancadorAtestadores');
        oDBLancadorAtestadores.setNomeInstancia('oDBLancadorAtestadores');
        oDBLancadorAtestadores.setTextoFieldset("Atestadores");
        oDBLancadorAtestadores.setLabelAncora('cgm:');
        oDBLancadorAtestadores.setGridHeight(100);
        oDBLancadorAtestadores.setParametrosPesquisa('func_cgm.php', ['z01_numcgm', 'z01_nome']);
        oDBLancadorAtestadores.show(infoAtestadoresEl);

        // Atestadores
        const showFieldAtestadores = (option) => {
            const tipo = Number(option.dataset.codigo);
            const tipodocaccept = [1, 3];

            if (tipodocaccept.includes(tipo)) {
                infoAtestadoresEl.classList.remove('d-none');
            } else {
                infoAtestadoresEl.classList.add('d-none');
            }
        }

        function js_validaAtestadores() {
            const optionEl = tipodocliquidacao.options[tipodocliquidacao.selectedIndex];
            const tipo = Number(optionEl.dataset.codigo);
            const tipodocaccept = [1, 3];

            if (tipodocaccept.includes(tipo)) {
                if (!oDBLancadorAtestadores.getRegistros().length) {
                    alert('[Atestador] Atestadores devem ser informados.');
                    return false;
                }
            }

            return true;
        }

        function js_validaTidocliquidacao() {
            if (!tipodocliquidacao.value) {
                alert('Tipo documento deve ser informado.');
                return false;
            }

            return true
        }

        // danfe
        const showFieldDanfe = (option) => {
            const tipo = Number(option.dataset.codigo);
            const tipodocaccept = [1];

            if (tipodocaccept.includes(tipo)) {
                infoDanfe.classList.remove('d-none');
            } else {
                infoDanfe.classList.add('d-none');
            }
        }

        // listeners
        const handleTipodocChange = (e) => {
            const selectEl = e.target;
            const optionEl = selectEl.options[selectEl.selectedIndex];

            showFieldAtestadores(optionEl);
            showFieldDanfe(optionEl);
        }

        tipodocliquidacao.addEventListener('change', handleTipodocChange);

        // Reseta os campos destinado ao sigfis
        function clearSigfis() {

            // Limpa tipo documento
            tipodocliquidacao.value = '';

            // Limpa Atestadores
            infoAtestadoresEl.classList.add('d-none');
            oDBLancadorAtestadores.clearAll();

            // Limpa danfe
            infoDanfe.classList.add('d-none');
            infoDanfe.querySelectorAll('input').forEach(e => e.value = '');
        }

        $("listaEstados").options[0] = new Option('Selecione', '', true, true);
        $("listaEstados").style.width = "280px";
        $('estadoDestino').style.display = 'none';
        $('paisDestino').value = "BRASIL";
        $('paisDestino').readOnly = 'readonly';

        $('diariaTipo').observe("change", function(event) {

            $('estadoDestino').value = "";
            $('listaEstados').options[0].selected = true;

            switch (this.value) {

                case '1':

                    // esconder estadoDestino
                    $('estadoDestino').style.display = 'none';
                    // aparecer listaEstados
                    $('listaEstados').style.display = '';
                    $('paisDestino').value = "BRASIL";
                    $('paisDestino').readOnly = 'readonly';
                    break;

                case '0':

                    // esconder listaEstados
                    $('listaEstados').style.display = 'none';
                    // aparecer estadoDestino
                    $('estadoDestino').style.display = '';
                    $('paisDestino').value = "";
                    $('paisDestino').readOnly = '';
                    break;
            }

        });

        $('listaEstados').observe("change", function(event) {
            $('estadoDestino').value = this.value;
        });


    }

    /**
     * Função para ajustar as opções de um elemento <select>.
     * A função verifica se o elemento <select> existe na página e, em seguida,
     * itera sobre todas as suas opções.
     *
     * Se o valor da opção não corresponder ao valor fornecido, a opção é removida do <select>.
     * Se o valor da opção corresponder ao valor fornecido, a opção é marcada como selecionada e desabilitada.
     *
     * @param {string} selectId - O id do elemento <select> a ser ajustado.
     * @param {string} valorParaManter - O valor da opção que deve ser mantida, selecionada e desabilitada.
     */
    const ajustarOption = (selectId, valorParaManter) => {

        const optionTag = document.querySelector(`#${selectId}`);


        if (optionTag) {

            const options = optionTag.querySelectorAll('option');


            options.forEach(option => {

                if (option.value !== valorParaManter) {
                    option.remove();
                } else {

                    option.selected = true;
                    option.disabled = true;
                }
            });
        }
    };

    const FILE_RPC = 'pro4_andamento_processo.RPC.php';
    const fileInput = document.getElementById("arquivo");
    const divArquivosSelecionados = document.getElementById("arquivos-selecionados");
    var fileList = [];

    fileInput.addEventListener('change', function(e) {
        let i = 0;
        var data = new FormData();
        for (i; i < fileInput.files.length; i++) {
            data.append('anexos[]', fileInput.files[i]);
        }

        data.append('acao', 'prepararDocumentos');
        data.append('getExtArq', 0);
        HttpClient.post(FILE_RPC, {
            body: data
        }).then(function(response) {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            response.documentos.map(function(documento) {
                fileList.push(Object.assign({}, documento));
                renderNamesFiles();
            });
        });
        fileInput.value = "";
    });

    function removeFile(indexFile) {
        fileList = fileList.filter(function(file, index) {
            console.log(index, indexFile, (index != indexFile))
            return index != indexFile;
        });
        renderNamesFiles();
    }

    function renderNamesFiles() {
        let html = [];

        fileList.forEach(function(file, index) {
            html.push(`
                  <div class='files'>${file.descricao} <a onclick="removeFile(${index})">X<a></div>
               `);
        });
        divArquivosSelecionados.innerHTML = html.join("");
    }
</script>
