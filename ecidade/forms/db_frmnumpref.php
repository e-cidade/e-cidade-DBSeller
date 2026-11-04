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
$clnumpref->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("k06_descr");
$clrotulo->label("k00_descr");
$clrotulo->label("nomeinst");
$clrotulo->label("z01_nome");
$clrotulo->label("rh37_descr");
$clrotulo->label("k03_receitapadraocredito");
$clrotulo->label("k02_drecei");
$clrotulo->label("k03_diasvalidadecertidao");
$clrotulo->label("k03_diasreemissaocertidao");
$clrotulo->label('k03_usagrupotaxa');

if (empty($k03_diasreemissaocertidao) || !DBNumber::isInteger(trim($k03_diasreemissaocertidao))) {
    $k03_diasreemissaocertidao = '';
}

if (empty($k03_diasvalidadecertidao) || !DBNumber::isInteger(trim($k03_diasvalidadecertidao))) {
    $k03_diasvalidadecertidao = '';
}
?>
<form name="form1" method="post" action="" onSubmit="return validaCampo();">
    <!-- financeiro -->
    <table width="100%">
        <tr>
            <td>
                <fieldset>
                    <legend>
                        <b>Financeiro</b>
                    </legend>
                    <table border="0" width="100%">

                        <tr>
                            <td title="<?= @$Tk03_anousu ?>" width="35%">
                                <?= @$Lk03_anousu ?>
                            </td>
                            <td colspan='4'>
                                <?php
                                $k03_anousu = db_getsession('DB_anousu');
                                db_input('k03_anousu', 15, $Ik03_anousu, true, 'text', 3, "");

                                db_input('k03_instit', 10, $Ik03_instit, true, 'hidden', $db_opcao);
                                db_input('k03_numpre', 10, $Ik03_numpre, true, 'hidden', $db_opcao);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_defope ?>" width="35%">
                                <?= @$Lk03_defope ?>
                            </td>
                            <td width="20%">
                                <?php
                                db_input('k03_defope', 15, $Ik03_defope, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                            <td title="<?= @$Tk03_numsli ?>" width="25%">
                                <?= @$Lk03_numsli ?>
                            </td>
                            <td width="20%">
                                <?php
                                db_input('k03_numsli', 15, $Ik03_numsli, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_recjur ?>">
                                <?= @$Lk03_recjur ?>
                            </td>
                            <td>
                                <?php
                                db_input('k03_recjur', 15, $Ik03_recjur, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                            <td title="<?= @$Tk03_recmul ?>">
                                <?= @$Lk03_recmul ?>
                            </td>
                            <td>
                                <?php
                                db_input('k03_recmul', 15, $Ik03_recmul, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_codbco ?>">
                                <?= @$Lk03_codbco ?>
                            </td>
                            <td>
                                <?php
                                db_input('k03_codbco', 15, $Ik03_codbco, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                            <td title="<?= @$Tk03_codage ?>">
                                <?= @$Lk03_codage ?>
                            </td>
                            <td>
                                <?php
                                db_input('k03_codage', 15, $Ik03_codage, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_calrec ?>">
                                <?= @$Lk03_calrec ?>
                            </td>
                            <td>
                                <?php
                                $x = array("f" => "NÃO", "t" => "SIM");
                                db_select('k03_calrec', $x, true, $db_opcao, "style='width:115px'");
                                ?>
                            </td>
                            <td>
                                <b>Tipo Recibo Protocolo:</b>
                            </td>
                            <td>
                                <?php
                                db_input('k03_reciboprot', 15, $Ik03_reciboprot, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    <?php
                                    db_ancora("Tipo Recibo Retenção: ", "js_consultareciboretencao(true);", 1);
                                    ?>
                                </b>
                            </td>
                            <td colspan='4'>
                                <?php
                                db_input('k03_reciboprotretencao', 15, $Ik03_reciboprotretencao, true, 'text', $db_opcao, " onchange=js_consultareciboretencao(false);");
                                db_input('k00_descr', 48, $Ik00_descr, true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b><?= @$Lk03_pgtoparcial ?></b>
                            </td>
                            <td colspan='4'>
                                <?php

                                if (isset($k03_pgtoparcial)) {
                                    if ($k03_pgtoparcial == 't') {
                                        $k03_pgtoparcial = "true";
                                    } else {
                                        $k03_pgtoparcial = "false";
                                    }
                                }

                                $aPgtoParcial = array('true' => 'SIM',
                                    'false' => 'NÃO');

                                db_select("k03_pgtoparcial", $aPgtoParcial, true, $db_opcao, "style='width:115px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b><?= @$Lk03_toleranciapgtoparc ?></b>
                            </td>
                            <td colspan='4'>
                                <?php
                                db_input('k03_toleranciapgtoparc', 15, $Ik03_toleranciapgtoparc, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_toleranciacredito ?>">
                                <?= @$Lk03_toleranciacredito ?>
                            </td>
                            <td colspan='3'>
                                <?php
                                db_input('k03_toleranciacredito', 15, $Ik03_toleranciacredito, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b><?= @$Lk03_agrupadorarquivotxtbaixabanco ?></b>
                            </td>
                            <td colspan='4'>
                                <?php
                                $aAgrupadorArquivoTxtBaixaBancoValores = getValoresPadroesCampo("k03_agrupadorarquivotxtbaixabanco");
                                db_select("k03_agrupadorarquivotxtbaixabanco", $aAgrupadorArquivoTxtBaixaBancoValores, true, $db_opcao, "style='width:350px'");
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <!-- final financeiro -->

        <!-- tributario -->
        <tr>
            <td>
                <fieldset align="left">
                    <legend>
                        <b>Tributário</b>
                    </legend>
                    <table border='0' width="100%">
                        <tr>
                            <td title="<?= @$Tk03_impend ?>" width="35%">
                                <?= @$Lk03_impend ?>
                            </td>
                            <td width="20%">
                                <?php
                                $x = array("f" => "NÃO", "t" => "SIM");
                                db_select('k03_impend', $x, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                            <td title="<?= @$Tk03_unipri ?>" width="25%">
                                <?= @$Lk03_unipri ?>
                            </td>
                            <td width="20%">
                                <?php
                                $x = array("f" => "NÃO", "t" => "SIM");
                                db_select('k03_unipri', $x, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_certissvar ?>">
                                <?= @$Lk03_certissvar ?>
                            </td>
                            <td>
                                <?php
                                $x = array("f" => "NÃO", "t" => "SIM");
                                db_select('k03_certissvar', $x, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                            <td title="<?= @$Tk03_diasjust ?>">
                                <?= @$Lk03_diasjust ?>
                            </td>
                            <td>
                                <?php
                                db_input('k03_diasjust', 16, $Ik03_diasjust, true, 'text', $db_opcao, "")
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_respcgm ?>">
                                <?php
                                db_ancora(@$Lk03_respcgm, "js_pesquisak03_respcgm(true);", $db_opcao);
                                ?>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('k03_respcgm', 15, $Ik03_respcgm, true, 'text', $db_opcao, " onchange='js_pesquisak03_respcgm(false)' ");
                                db_input('z01_nome', 48, $Iz01_nome, true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_respcargo ?>">
                            <?php
                            db_ancora(@$Lk03_respcargo, "js_pesquisak03_respcargo(true)", $db_opcao);
                            ?>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('k03_respcargo', 15, $Ik03_respcargo, true, 'text', $db_opcao, " onchange='js_pesquisak03_respcargo(false)'");
                                db_input('rh37_descr', 48, $Irh37_descr, true, 'text', 3)
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_taxagrupo ?>">
                                <?php
                                db_ancora(@$Lk03_taxagrupo, "js_pesquisak03_taxagrupo(true);", $db_opcao);
                                ?>
                            </td>
                            <td colspan='3'>
                                <?php
                                db_input('k03_taxagrupo', 15, $Ik03_taxagrupo, true, 'text', $db_opcao, " onchange='js_pesquisak03_taxagrupo(false);'");
                                db_input('k06_descr', 48, $Ik06_descr, true, 'text', 3, '');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td wrap title="<?=@$Tk03_reemissaorecibo?>">
                              <?=@$Lk03_reemissaorecibo?>
                            </td>
                            <td colspan="3">
                               <?php
                                 $aReemiteRecibo = array(
                                    'f'=>"Não",
                                    't'=>"Sim"
                                 );

                                 db_select('k03_reemissaorecibo',$aReemiteRecibo,true,$db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_tipocertidao ?>">
                                <?= @$Lk03_tipocertidao ?>
                            </td>
                            <td colspan='3'>
                                <?php
                                $x = array("1" => "Conjunta", "2" => "Individualizada", "3" => "Sele&ccedil;&atilde;o Usu&aacute;rio");
                                db_select('k03_tipocertidao', $x, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_reccert ?>">
                                <?= @$Lk03_reccert ?>
                            </td>
                            <td colspan='3'>
                                <?php
                                $x = array("f" => "NÃO", "t" => "SIM");
                                db_select('k03_reccert', $x, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_tipocodcert ?>">
                                <?= @$Lk03_tipocodcert ?>
                            </td>
                            <td colspan="3">
                                <?php
                                $x = array('0' => 'Não codifica',
                                    '1' => 'Sequencial geral',
                                    '2' => 'Sequencial por Instituição',
                                    '3' => 'Sequencial por Tipo de Certidão Geral',
                                    '4' => 'Sequencial por Tipo de Certidão por Instituição',
                                    '5' => 'Código do Processo de Protocolo e Exercício');

                                db_select('k03_tipocodcert', $x, true, $db_opcao, "style='width:350px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk03_regracnd ?>">
                                <?= @$Lk03_regracnd ?>
                            </td>
                            <td colspan="3">
                                <?php
                                $aRegraCND = array("1" => "Verifica os débitos dos imóveis por matrícula",
                                    "2" => "Verifica os débitos dos imóveis por lote",
                                    "3" => "Verifica os débitos dos imóveis por idbql");
                                db_select('k03_regracnd', $aRegraCND, true, $db_opcao, "style='width:350px'");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php
                                db_ancora($Lk03_receitapadraocredito, 'js_pesquisaReceita(true)', $db_opcao)
                                ?>
                            </td>
                            <td colspan="3">
                                <?php
                                db_input('k03_receitapadraocredito', 15, $Ik03_receitapadraocredito, true, 'text', $db_opcao, "onchange='js_pesquisaReceita(false)'");
                                db_input('k02_drecei', 48, $Ik02_drecei, true, 'text', 3);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td title="<?= @$Tk03_tipoinscricao ?>">
                                <?= @$Lk03_tipoinscricao ?>
                            </td>
                            <td colspan="3">
                                <?php
                                $aTipoInscricao = array("0" => "Não informado", "1" => "Cobranca Administrativa", "2" => "Divida Ativa");
                                db_select('k03_tipoinscricao', $aTipoInscricao, true, $db_opcao, "style='width:350px'");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td title="<?= @$Tk03_filtrarreceita ?>" width="35%">
                                       <?= @$Lk03_filtrarreceita ?>
                            </td>
                            <td width="20%">
                                       <?php   $x = array("f" => "NÃO", "t" => "SIM");
                                            db_select('k03_filtrarreceita', $x, true, $db_opcao, "style='width:125px'");
                                        ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= $Tk03_usagrupotaxa ?>" width="35%">
                                <?= $Lk03_usagrupotaxa ?>
                            </td>
                            <td width="20%">
                                <?php
                                    $opcoesUsaGrupoTaxa = array("f" => "NÃO", "t" => "SIM");
                                    db_select('k03_usagrupotaxa', $opcoesUsaGrupoTaxa, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= $Tk03_processoparcelamento ?>" width="35%">
                                <?= $Lk03_processoparcelamento ?>
                            </td>
                            <td width="20%">
                                <?php
                                    $opcoesUsaGrupoTaxa = array("f" => "NÃO", "t" => "SIM");
                                    db_select('k03_processoparcelamento', $opcoesUsaGrupoTaxa, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td title="<?= $Tk03_custashonorarios ?>" width="35%">
                                <?= $Lk03_custashonorarios ?>
                            </td>
                            <td width="20%">
                                <?php
                                    $opcoesUsaGrupoTaxa = array("f" => "NÃO", "t" => "SIM");
                                    db_select('k03_custashonorarios', $opcoesUsaGrupoTaxa, true, $db_opcao, "style='width:125px'");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <fieldset align="left">
                                    <legend>
                                        <b>Cancelamentos</b>
                                    </legend>
                                    <table border='0' width="100%">
                                        <tr>
                                            <td title="<?= @$Tk03_filtrardepart ?>" width="35%">
                                                <?= @$Lk03_filtrardepart ?>
                                            </td>
                                            <td width="20%">
                                                <?php
                                                $x = array("f" => "NÃO", "t" => "SIM");
                                                db_select('k03_filtrardepart', $x, true, $db_opcao, "style='width:125px'");
                                                ?>
                                            </td>
                                            <td title="<?= @$Tk03_apenastiporenuncia ?>" width="25%">
                                                <?= @$Lk03_apenastiporenuncia ?>
                                            </td>
                                            <td width="20%">
                                                <?php
                                                $x = array("f" => "NÃO", "t" => "SIM");
                                                db_select('k03_apenastiporenuncia', $x, true, $db_opcao, "style='width:125px'");
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td title="<?= @$Tk03_imprimecancdebitos ?>" width="35%">
                                                <?= @$Lk03_imprimecancdebitos ?>
                                            </td>
                                            <td width="20%">
                                                <?php
                                                $x = array("f" => "NÃO", "t" => "SIM");
                                                db_select('k03_imprimecancdebitos', $x, true, $db_opcao, "style='width:125px'");
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <fieldset align="left">
                                    <legend>
                                        <b>Aplica juros e multa</b>
                                    </legend>
                                    <table border='0' width="100%">
                                        <tr>
                                            <td title="<?= @$Tk03_encargoscarneunica ?>" width="35%">
                                                <?= @$Lk03_encargoscarneunica?>
                                            </td>
                                            <td width="20%">
                                                <?php
                                                $x = array("f" => "NÃO", "t" => "SIM");
                                                db_select('k03_encargoscarneunica', $x, true, $db_opcao, "style='width:125px'");
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <!-- final tributario -->

        <?php
        $camposDiasCertidao = array(
            // Array de informações das certidões emitidas via ECidade
            array(
                "title" => "Certidões de Regularidade Fiscal - E-Cidade",
                "id" => "ecidade",
                "fieldsets" => array(
                    // Array da certidão regular
                    array(
                        "title" => "Certidão Regular",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidregular_cgm",
                                "label"         => @$Lk03_diascertidregular_cgm,
                                "title"         => @$Tk03_diascertidregular_cgm,
                                "campoTemplate" => "templatecertidao_cgm"
                            ),
                            array(
                                "campo"         => "diascertidregular_matric",
                                "label"         => @$Lk03_diascertidregular_matric,
                                "title"         => @$Tk03_diascertidregular_matric,
                                "campoTemplate" => "templatecertidao_matric"
                            ),
                            array(
                                "campo"         => "diascertidregular_inscr",
                                "label"         => @$Lk03_diascertidregular_inscr,
                                "title"         => @$Tk03_diascertidregular_inscr,
                                "campoTemplate" => "templatecertidao_inscr"
                            )
                        )
                    ),
                    // Array da certidão positiva
                    array(
                        "title" => "Certidão Positiva",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidpositiva_cgm",
                                "label"         => @$Lk03_diascertidpositiva_cgm,
                                "title"         => @$Tk03_diascertidpositiva_cgm,
                                "campoTemplate" => "templatecertidaopositiva_cgm"
                            ),
                            array(
                                "campo"         => "diascertidpositiva_matric",
                                "label"         => @$Lk03_diascertidpositiva_matric,
                                "title"         => @$Tk03_diascertidpositiva_matric,
                                "campoTemplate" => "templatecertidaopositiva_matric"
                            ),
                            array(
                                "campo"         => "diascertidpositiva_inscr",
                                "label"         => @$Lk03_diascertidpositiva_inscr,
                                "title"         => @$Tk03_diascertidpositiva_inscr,
                                "campoTemplate" => "templatecertidaopositiva_inscr"
                            )
                        )
                    ),
                    // Array da certidão negativa
                    array(
                        "title" => "Certidão Negativa",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidnegativa_cgm",
                                "label"         => @$Lk03_diascertidnegativa_cgm,
                                "title"         => @$Tk03_diascertidnegativa_cgm,
                                "campoTemplate" => "templatecertidaonegativa_cgm"
                            ),
                            array(
                                "campo"         => "diascertidnegativa_matric",
                                "label"         => @$Lk03_diascertidnegativa_matric,
                                "title"         => @$Tk03_diascertidnegativa_matric,
                                "campoTemplate" => "templatecertidaonegativa_matric"
                            ),
                            array(
                                "campo"         => "diascertidnegativa_inscr",
                                "label"         => @$Lk03_diascertidnegativa_inscr,
                                "title"         => @$Tk03_diascertidnegativa_inscr,
                                "campoTemplate" => "templatecertidaonegativa_inscr"
                            )
                        )
                    )
                )
            ),
            // Array de informações das certidões emitidas via DBPref
            array(
                "title" => "Certidões de Regularidade Fiscal - DBPref",
                "id" => "dbpref",
                "fieldsets" => array(
                    // Array da certidão regular
                    array(
                        "title" => "Certidão Regular",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidregularweb_cgm",
                                "label"         => @$Lk03_diascertidregularweb_cgm,
                                "title"         => @$Tk03_diascertidregularweb_cgm,
                                "campoTemplate" => "templatecertidaoweb_cgm"
                            ),
                            array(
                                "campo"         => "diascertidregularweb_matric",
                                "label"         => @$Lk03_diascertidregularweb_matric,
                                "title"         => @$Tk03_diascertidregularweb_matric,
                                "campoTemplate" => "templatecertidaoweb_matric"
                            ),
                            array(
                                "campo"         => "diascertidregularweb_inscr",
                                "label"         => @$Lk03_diascertidregularweb_inscr,
                                "title"         => @$Tk03_diascertidregularweb_inscr,
                                "campoTemplate" => "templatecertidaoweb_inscr"
                            )
                        )
                    ),
                    // Array da certidão positiva
                    array(
                        "title" => "Certidão Positiva",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidpositivaweb_cgm",
                                "label"         => @$Lk03_diascertidpositivaweb_cgm,
                                "title"         => @$Tk03_diascertidpositivaweb_cgm,
                                "campoTemplate" => "templatecertidaopositivaweb_cgm"
                            ),
                            array(
                                "campo"         => "diascertidpositivaweb_matric",
                                "label"         => @$Lk03_diascertidpositivaweb_matric,
                                "title"         => @$Tk03_diascertidpositivaweb_matric,
                                "campoTemplate" => "templatecertidaopositivaweb_matric"
                            ),
                            array(
                                "campo"         => "diascertidpositivaweb_inscr",
                                "label"         => @$Lk03_diascertidpositivaweb_inscr,
                                "title"         => @$Tk03_diascertidpositivaweb_inscr,
                                "campoTemplate" => "templatecertidaopositivaweb_inscr"
                            )
                        )
                    ),
                    // Array da certidão negativa
                    array(
                        "title" => "Certidão Negativa",
                        "campos" => array(
                            array(
                                "campo"         => "diascertidnegativaweb_cgm",
                                "label"         => @$Lk03_diascertidnegativaweb_cgm,
                                "title"         => @$Tk03_diascertidnegativaweb_cgm,
                                "campoTemplate" => "templatecertidaonegativaweb_cgm"
                            ),
                            array(
                                "campo"         => "diascertidnegativaweb_matric",
                                "label"         => @$Lk03_diascertidnegativaweb_matric,
                                "title"         => @$Tk03_diascertidnegativaweb_matric,
                                "campoTemplate" => "templatecertidaonegativaweb_matric"
                            ),
                            array(
                                "campo"         => "diascertidnegativaweb_inscr",
                                "label"         => @$Lk03_diascertidnegativaweb_inscr,
                                "title"         => @$Tk03_diascertidnegativaweb_inscr,
                                "campoTemplate" => "templatecertidaonegativaweb_inscr"
                            )
                        )
                    )
                )
            )
        );

        foreach ($camposDiasCertidao as $item) :
            ?>
            <tr>
                <td>
                    <fieldset id="field_certidao<?=$item["id"]?>" name="field_certidao<?=$item["id"]?>">
                        <legend>
                            <b><?= $item["title"] ?></b>
                        </legend>
                        <?php foreach ($item["fieldsets"] as $fieldset) : ?>
                            <fieldset>
                                <legend>
                                    <?= $fieldset["title"] ?>
                                </legend>
                                <table border='0' width="100%">
                                    <?php foreach ($fieldset["campos"] as $campos) : ?>
                                        <tr>
                                            <td title="<?= $campos["title"] ?>" width="35%">
                                                <?= $campos["label"] ?>
                                            </td>
                                            <td>
                                                <?php db_input("k03_".$campos["campo"], 2, $Ik03_.$campos["campo"],true,'text',$db_opcao, "", '', '', '', 3) ?>
                                            </td>
                                            <td nowrap="nowrap" title="<?= @$Tk03_.$campos["campo"] ?>" width="20%">
                                                <?php
                                                db_ancora("<strong>Documento Template:</strong>","js_pesquisaDocumento(true, '".$campos["campoTemplate"]."');",$db_opcao);
                                                ?>
                                            </td>
                                            <td nowrap="nowrap">
                                                <?php
                                                db_input("k03_".$campos["campoTemplate"],4,@$Ij18_templatecertidaoexitencia,true,'text',$db_opcao,
                                                    "onkeyup='js_pesquisaDocumento(false, \"{$campos["campoTemplate"]}\")'");
                                                db_input($campos["campoTemplate"],30,@$Idb82_descricao,true,'text',3,'', $campos["campoTemplate"]);
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </fieldset>
                        <?php endforeach; ?>
                    </fieldset>
                </td>
            </tr>

        <?php endforeach; ?>

        <tr>
            <td>
                <fieldset>
                    <legend>
                        Reemissão de Certidão
                    </legend>
                    <table border='0' width="">
                        <tr>
                            <td title="<?= @$Tk03_diasreemissaocertidao ?>">
                                <?= @$Lk03_diasreemissaocertidao ?>
                            </td>
                            <td>
                                <?php db_input("k03_diasreemissaocertidao", 2, $Ik03_diasreemissaocertidao, true, "text", $db_opcao, "", "", "", "", 3) ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <!-- Inicio mensagens-->
    <fieldset id="field_mensagem" name="field_mensagem">
        <legend>Mensagens</legend>
        <table>
            <tr>
                <td title="<?= @$Tk03_msg ?>" colspan="4" align="center">
                    <fieldset>
                        <legend><?= @$Lk03_msg ?></legend>
                        <?php
                        db_textarea('k03_msg', 0, 100, $Ik03_msg, true, 'text', $db_opcao, "");
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td title="<?= @$Tk03_msgcarne ?>" colspan='4' align="center">
                    <fieldset>
                        <legend><?= @$Lk03_msgcarne ?></legend>
                        <?php
                        db_textarea('k03_msgcarne', 0, 100, $Ik03_msgcarne, true, 'text', $db_opcao, "");
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td title="<?= @$Tk03_msgbanco ?>" colspan="4" align="center">
                    <fieldset>
                        <legend><?= @$Lk03_msgbanco ?></legend>
                        <?php
                        db_textarea('k03_msgbanco', 0, 100, $Ik03_msgbanco, true, 'text', $db_opcao, "");
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td title="<?= @$Tk03_msgautent ?>" colspan="4" align="center">
                    <fieldset>
                        <legend><?= @$Lk03_msgautent ?></legend>
                        <?php
                        db_textarea('k03_msgautent', 0, 100, $Ik03_msgautent, true, 'text', $db_opcao, "");
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
    </fieldset>
    <!-- Fim mensagens-->


    <input
            name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
            type="submit" id="db_opcao"
            value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
        <?= ($db_botao == false ? "disabled" : "") ?> /> <input name="pesquisar"
                                                                type="button" id="pesquisar" value="Pesquisar"
                                                                onclick="js_pesquisa();">
</form>
<script>

    oCertidbp = new DBToogle("field_certidaodbpref", false);
    oCertidao = new DBToogle("field_certidaoecidade", false);
    oMensagem = new DBToogle("field_mensagem", false);
    var MENSAGENS = 'tributario.arrecadacao.db_frmnumpref.';

    function validaCampo() {

        if ($F('k03_defope').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Operação"}));
            $('k03_defope').focus();
            return false;
        }

        if ($F('k03_recjur').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Receita Juros"}));
            $('k03_recjur').focus();
            return false;
        }

        if ($F('k03_codbco').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Banco"}));
            $('k03_codbco').focus();
            return false;
        }

        if ($F('k03_numsli').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Slip"}));
            $('k03_numsli').focus();
            return false;
        }

        if ($F('k03_recmul').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Receita Multa"}));
            $('k03_recmul').focus();
            return false;
        }

        if ($F('k03_codage').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Agência"}));
            $('k03_codage').focus();
            return false;
        }

        if ($F('k03_reciboprot').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Tipo do Recibo Protocolo"}));
            $('k03_reciboprot').focus();
            return false;
        }

        if ($F('k03_toleranciapgtoparc').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Valor Tolerância Diferença Pagamento"}));
            $('k03_toleranciapgtoparc').focus();
            return false;
        }

        if ($F('k03_toleranciacredito').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Tolerância para Crédito"}));
            $('k03_toleranciacredito').focus();
            return false;
        }

        if ($F('k03_diasjust').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias Justif."}));
            $('k03_diasjust').focus();
            return false;
        }

        if ($F('k03_taxagrupo').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Código do grupo de taxas"}));
            $('k03_taxagrupo').focus();
            return false;
        }

        if ($F('k03_diascertidregular_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por CGM via E-Cidade"}));
            $('k03_diascertidregular_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidregular_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por Matricula via E-Cidade"}));
            $('k03_diascertidregular_matric').focus();
            return false;
        }

                if ($F('k03_diascertidregular_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por Inscrição via E-Cidade"}));
            $('k03_diascertidregular_inscr').focus();
            return false;
        }

        if ($F('k03_diascertidpositiva_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por CGM via E-Cidade"}));
            $('k03_diascertidpositiva_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidpositiva_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por Matricula via E-Cidade"}));
            $('k03_diascertidpositiva_matric').focus();
            return false;
        }

        if ($F('k03_diascertidpositiva_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por Inscrição via E-Cidade"}));
            $('k03_diascertidpositiva_inscr').focus();
            return false;
        }

        if ($F('k03_diascertidnegativa_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por CGM via E-Cidade"}));
            $('k03_diascertidnegativa_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidnegativa_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por Matricula via E-Cidade"}));
            $('k03_diascertidnegativa_matric').focus();
            return false;
        }

        if ($F('k03_diascertidnegativa_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por Inscrição via E-Cidade"}));
            $('k03_diascertidnegativa_inscr').focus();
            return false;
        }

        if ($F('k03_diascertidregularweb_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por CGM via DBPref"}));
            $('k03_diascertidregularweb_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidregularweb_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por Matricula via DBPref"}));
            $('k03_diascertidregularweb_matric').focus();
            return false;
        }

        if ($F('k03_diascertidregularweb_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Regular por Inscrição via DBPref"}));
            $('k03_diascertidpositivaweb_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidpositivaweb_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por CGM via DBPref"}));
            $('k03_templatecertidaonegativa_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidpositivaweb_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por Matricula via DBPref"}));
            $('k03_diascertidpositivaweb_matric').focus();
            return false;
        }

        if ($F('k03_diascertidpositivaweb_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Positiva por Inscrição via DBPref"}));
            $('k03_diascertidpositivaweb_inscr').focus();
            return false;
        }

        if ($F('k03_diascertidnegativaweb_cgm').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por CGM via DBPref"}));
            $('k03_diascertidnegativaweb_cgm').focus();
            return false;
        }

        if ($F('k03_diascertidnegativaweb_matric').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por Matricula via DBPref"}));
            $('k03_diascertidnegativaweb_matric').focus();
            return false;
        }

        if ($F('k03_diascertidnegativaweb_inscr').trim() == "") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para vencimento da Certidão Negativa por Inscrição via DBPref"}));
            $('k03_diascertidnegativaweb_inscr').focus();
            return false;
        }

        if ($F('templatecertidao').trim() == "") {
            $('k03_templatecertidao').val('');
        }

        if ($F('templatecertidaoweb').trim() == "") {
            $('k03_templatecertidaoweb').val('');
        }

        if ($F('k03_diasreemissaocertidao').trim() == "") {

            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Dias para reemissão das Certidões"}));
            $('k03_diasreemissaocertidao').focus();
            return false;
        }

        if ($F('k03_tipoinscricao').trim() == "0") {
            alert(_M(MENSAGENS + "campo_nao_informado", {sCampo: "Tipo Inscrição"}));
            $('k03_tipoinscricao').focus();
            return false;
        }
    }

    var chave_k03_tipo = 14;

    function js_pesquisak03_respcargo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_respcargo', 'func_rhfuncao.php?funcao_js=parent.js_mostrarespcargo1|rh37_funcao|rh37_descr', 'Pesquisa', true);
        } else {
            if (document.form1.k03_respcargo.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_respcargo', 'func_rhfuncao.php?pesquisa_chave=' + document.form1.k03_respcargo.value + '&funcao_js=parent.js_mostrarespcargo2', 'Pesquisa', false);
            } else {
                document.form1.rh37_descr.value = '';
            }
        }
    }

    function js_mostrarespcargo1(chave1, chave2) {

        document.form1.k03_respcargo.value = chave1;
        document.form1.rh37_descr.value = chave2;
        db_iframe_respcargo.hide();

    }

    function js_mostrarespcargo2(chave1, erro) {

        document.form1.rh37_descr.value = chave1;
        if (erro == true) {
            document.form1.k03_respccargo.focus();
            document.form1.k03_respccargo.value = '';
        }

    }

    function js_pesquisak03_respcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_respcgm', 'func_cgm.php?funcao_js=parent.js_mostranomecgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {
            if (document.form1.k03_respcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_respcgm', 'func_cgm.php?pesquisa_chave=' + document.form1.k03_respcgm.value + '&funcao_js=parent.js_mostranomecgm2', 'Pesquisa', false);
            } else {
                document.form1.z01_nome.value = "";
            }
        }
    }

    function js_mostranomecgm1(chave1, chave2) {
        document.form1.k03_respcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_respcgm.hide();
    }

    function js_mostranomecgm2(erro, chave1) {

        document.form1.z01_nome.value = chave1;
        if (erro == true) {
            document.form1.k03_respcgm.focus();
            document.form1.k03_respcgm.value = '';
        }
    }

    function js_pesquisak03_taxagrupo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_taxagrupo', 'func_taxagrupo.php?funcao_js=parent.js_mostrataxagrupo1|k06_taxagrupo|k06_descr', 'Pesquisa', true);
        } else {
            if (document.form1.k03_taxagrupo.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_taxagrupo', 'func_taxagrupo.php?pesquisa_chave=' + document.form1.k03_taxagrupo.value + '&funcao_js=parent.js_mostrataxagrupo', 'Pesquisa', false);
            } else {
                document.form1.k06_descr.value = '';
            }
        }
    }

    function js_mostrataxagrupo(chave, erro) {
        document.form1.k06_descr.value = chave;
        if (erro == true) {
            document.form1.k03_taxagrupo.focus();
            document.form1.k03_taxagrupo.value = '';
        }
    }

    function js_mostrataxagrupo1(chave1, chave2) {
        document.form1.k03_taxagrupo.value = chave1;
        document.form1.k06_descr.value = chave2;
        db_iframe_taxagrupo.hide();
    }

    function js_pesquisak03_instit(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_db_config', 'func_db_config.php?funcao_js=parent.js_mostradb_config1|codigo|nomeinst', 'Pesquisa', true);
        } else {
            if (document.form1.k03_instit.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_db_config', 'func_db_config.php?pesquisa_chave=' + document.form1.k03_instit.value + '&funcao_js=parent.js_mostradb_config', 'Pesquisa', false);
            } else {
                document.form1.nomeinst.value = '';
            }
        }
    }

    function js_mostradb_config(chave, erro) {
        document.form1.nomeinst.value = chave;
        if (erro == true) {
            document.form1.k03_instit.focus();
            document.form1.k03_instit.value = '';
        }
    }

    function js_mostradb_config1(chave1, chave2) {
        document.form1.k03_instit.value = chave1;
        document.form1.nomeinst.value = chave2;
        db_iframe_db_config.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_numpref', 'func_numpref.php?funcao_js=parent.js_preenchepesquisa|k03_anousu|k03_instit', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave, chave1) {
        db_iframe_numpref.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($_SERVER["PHP_SELF"]) . "?chavepesquisa='+chave+'&chavepesquisa1='+chave1";
        }
        ?>
    }

    function js_consultareciboretencao(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_reciboprotretencao',
                'func_reciboprotretencao.php?k03_tipo=' + chave_k03_tipo + '&funcao_js=parent.js_mostrareciboretencao1|k00_tipo|k00_descr',
                'Pesquisa', true);
        } else {
            if (document.form1.k03_reciboprotretencao.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_reciboprotretencao',
                    'func_reciboprotretencao.php?k03_tipo=' + chave_k03_tipo + '&pesquisa_chave=' + document.form1.k03_reciboprotretencao.value + '&funcao_js=parent.js_mostrareciboretencao',
                    'Pesquisa', false);
            } else {
                document.form1.k03_reciboprotretencao.value = '';
                document.form1.k00_descr.value = '';
            }
        }
    }

    function js_mostrareciboretencao(chave, erro) {
        document.form1.k00_descr.value = chave;
        db_iframe_reciboprotretencao.hide();
        if (erro == true) {
            document.form1.k00_descr.focus();
            document.form1.k03_reciboprotretencao.value = '';
        }
    }

    function js_mostrareciboretencao1(chave1, chave2) {
        document.form1.k03_reciboprotretencao.value = chave1;
        document.form1.k00_descr.value = chave2;
        db_iframe_reciboprotretencao.hide();
    }

    function js_pesquisaReceita(lMostra) {

        if (lMostra) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_receita', 'func_tabrec.php?funcao_js=parent.js_mostraReceita|k02_codigo|k02_drecei', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_receita', 'func_tabrec.php?funcao_js=parent.js_mostraReceitaHide&pesquisa_chave=' + document.getElementById('k03_receitapadraocredito').value, 'Pesquisa', false);
        }

    }

    function js_mostraReceita(iCodigoReceita, sDescricao) {

        document.getElementById('k03_receitapadraocredito').value = iCodigoReceita;
        document.getElementById('k02_drecei').value = sDescricao;
        db_iframe_receita.hide();

    }

    function js_mostraReceitaHide(sDescricao, lErro) {

        if (lErro) {
            document.getElementById('k03_receitapadraocredito').value = '';
        }

        document.getElementById('k02_drecei').value = sDescricao;

    }

    var campo = "";

    /**
     * Pesquisa dados via lookup ou digitação
     * @param object   oElemento Elemento HTML base para pesquisa
     * @param boolean  lMostra   Valida se mostra a lookup de pesquisa
     */
    function js_pesquisaDocumento(lMostra, campoInput)
    {
        setCampo(campoInput);

        if (lMostra) {
            sArquivoPesquisa = 'func_db_documentotemplate.php?funcao_js=parent.js_mostraDocumentoLookUp|db82_sequencial|db82_descricao&tipo=57';
        } else {
            if (campo != '' && campo != null){
                sArquivoPesquisa = 'func_db_documentotemplate.php?pesquisa_chave=' + $F('k03_' + campo) + '&funcao_js=parent.js_mostraDocumentoDigitacao&tipo=57';
            }else {
                sArquivoPesquisa = 'func_db_documentotemplate.php?funcao_js=parent.js_mostraDocumentoLookUp|db82_sequencial|db82_descricao&tipo=57';
            }
        }
        /**
         * Abre a janela
         */
        js_OpenJanelaIframe('CurrentWindow.corpo',
            'db_iframe_db_documentotemplate',
            sArquivoPesquisa,
            'Pesquisa Documentos Template',
            lMostra);
    }

    function setCampo(campoInput)
    {
        campo = campoInput;
    }

    function js_mostraDocumentoLookUp(iCodigo, sRetorno)
    {
        $('k03_'+campo).value = iCodigo;
        $(campo).value = sRetorno;
        db_iframe_db_documentotemplate.hide();
    }

    function js_mostraDocumentoDigitacao(sRetorno, lErro)
    {
        if (lErro) {
            $(campo).value = '';
            $('k03_' + campo).value = '';
        } else {
            $(campo).value = sRetorno;
        }
    }
</script>
