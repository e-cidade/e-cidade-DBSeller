<?php
/*
*  E-cidade Software Publico para Gestao Municipal
*  Copyright (C) 2009 DBSeller Servicos de Informatica
*  www.dbseller.com.br
*  e-cidade@dbseller.com.br
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

//MODULO: recursos humanos
$cladmissao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("rh01_matriculaanterior");
$clrotulo->label("rh37_funcao");
$clrotulo->label("rh37_descr");
$clrotulo->label("h04_descr");
$arr_SouN = array("f" => "NAO", "t" => "SIM");
$clrotulo->label("h25_nrdispositivo");
$clrotulo->label("h25_nomeacao");
$clrotulo->label("h25_irfonte");
$clrotulo->label("h25_referenciair");
$clrotulo->label("h25_portariaaposentadoria");
$clrotulo->label("h25_dataaposentadoria");
$clrotulo->label("h25_contaraposentadoria");
$clrotulo->label("h25_processoaposentadoria");
$clrotulo->label("p58_numero");
$clrotulo->label("h25_portariaexoneracao");
$clrotulo->label("h25_dataexoneracao");
$clrotulo->label("h25_processoexoneracao");
$clrotulo->label("h25_portariareintegracao");
$clrotulo->label("h25_tiporeintegracao");
$clrotulo->label("h25_dataefetivoretorno");
$clrotulo->label("h25_numeroprocesso");
$clrotulo->label("h25_leianistia");
$clrotulo->label("h25_datareintegracao");
$clrotulo->label("h25_processoreintegracao");
$clrotulo->label("h25_contarexoneracao");
$clrotulo->label("p58_codproc");
$clrotulo->label("h25_hipleg");
$clrotulo->label("h25_dtbase");
$clrotulo->label("rh253_fisica");
$clrotulo->label("rh253_visual");
$clrotulo->label("rh253_auditiva");
$clrotulo->label("rh253_mental");
$clrotulo->label("rh253_intelectual");
$clrotulo->label("rh253_reabilitado");
$clrotulo->label("rh253_cota");
$clrotulo->label("rh253_observacao");
$clrotulo->label("rh253_matricula");
$clrotulo->label("rh253_instit");
$clrotulo->label("rh260_naturezaestagio");
$clrotulo->label("rh260_nivelestagio");
$clrotulo->label("rh260_dataterminoestagio");
$clrotulo->label("rh260_cnpjinstensino");
$clrotulo->label("rh260_cnpjagentintegracao");
$clrotulo->label("rh260_areaatuacao");
$clrotulo->label("rh260_apoliceseguro");
$clrotulo->label("rh260_cpfsupervisor");
$clrotulo->label("rh312_modalidade");
$clrotulo->label("rh312_cnpjefetivada");
$clrotulo->label("rh312_cnpjqualificadora");
$clrotulo->label("rh312_cnpjpratica");

$simNao = ["f" => "Não", "t" => "Sim"];

?>
<form name="form1" method="post" action="">
    <table>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>DADOS DA ADMISSÃO</b></legend>
                    <table  border="0">
                        <tr>
                            <td nowrap title="<?=@$Th07_regist?>">
                                <?php  db_ancora(@$Lh07_regist,"js_pesquisah07_regist(true);",($db_opcao == 1 ? 1 : 3));?>
                            </td>
                            <td colspan="5">
                                <?php
                                db_input('h07_regist',6,$Ih07_regist,true,'text',($db_opcao == 1 ? 1 : 3)," onchange='js_pesquisah07_regist(false);'");
                                db_input('z01_nome',70,$Iz01_nome,true,'text',3,'');
                                db_input('modeloposse',10,"",true,'hidden',3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_tipadm?>">
                                <?=@$Lh07_tipadm?>
                            </td>
                            <td >
                                <?php
                                $arr_tipadm = Array(
                                    '' => 'Não Informado.',
                                    '01' => '01 - Por concurso público', #01
                                    '02' => '02 - Por prazo determinado', #02
                                    '03' => '03 - Sem fundamentação legal', #03
                                    '04' => '04 - Por decisão judicial', #04
                                    '05' => '05 - Reenquadramento', #05
                                    '06' => '06 - Transferência município-mãe', #06
                                    '07' => '07 - Trasnposição reg jurídico', #07
                                    '08' => '08 - Transferência (outro órgão)', #08
                                    '09' => '09 - Readaptação', #09
                                    '10' => '10 - Readmissão', #10
                                    '11' => '11 - Recondução', #11
                                    '12' => '12 - Reintegração', #12
                                    '13' => '13 - Nomeação', #13

                                    /** Código para extensão */

                                    '14' => '14 - Contrato temporário', #14
                                    '15' => '15 - Cargo em comissão', #15
                                    '16' => '16 - CLT',  #16
			                        '17' => '17 - Estagiário' #17

                                    /** Fim Código para extensão  */

                                    );
                                    db_select('h07_tipadm', $arr_tipadm, true, $db_opcao, "style='width: 200'");
                                ?>
                            </td>
                            <td nowrap title="<?=@$Th07_tempor?>">
                                <?=@$Lh07_tempor?>
                            </td>
                            <td>
                                <?php
                                if(!isset($h07_tempor) || (isset($h07_tempor) && trim($h07_tempor) == "")){
                                    $h07_tempor = "f";
                                }
                                db_select('h07_tempor', $arr_SouN, true, $db_opcao, "onChange=jsChangeHipotese(this.id,this.value)");
                                ?>
                            </td>
                        </tr>
                        <tr>
                        <?php
                        $displayHipotese = "display: none;";
                        if ($h07_tempor == 't') {
                            $displayHipotese = '';
                        }
                        ?>
                            <td id="lblh25_hipleg" style="<?php echo $displayHipotese;?>" nowrap title="<?=$Th25_hipleg?>" >
                                <?=$Lh25_hipleg?>
                            </td>
                            <td id="id25_hipleg" style="<?php echo $displayHipotese;?>">
                                <?php
                                echo "<script> const exibeHipotese = " . $h07_tempor . ";</script>";
                                $hipoteses = \AdmissaoDado::getDescricoesHipotese();
                                db_select('h25_hipleg', $hipoteses, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_nrato?>">
                                <b> No. do Ato Nomeação: </b>
                            </td>
                            <td>
                                <?php db_input('h07_nrato', 12, $Ih07_nrato, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Th07_nrfich?>">
                                <?=@$Lh07_nrfich?>
                            </td>
                            <td>
                                <?php db_input('h07_nrfich', 6, $Ih07_nrfich, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_dato?>">
                                <b>Data do Ato / Nomeação</b>
                            </td>
                            <td>
                                <?php db_inputdata('h07_dato', @$h07_dato_dia, @$h07_dato_mes, @$h07_dato_ano, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Th07_icon?>">
                                <?=@$Lh07_icon?>
                            </td>
                            <td>
                                <?php
                                if (!isset($h07_icon) || (isset($h07_icon) && trim($h07_icon) == "")) {
                                    $h07_icon = "t";
                                }
                                db_select('h07_icon', $arr_SouN, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_dpubl?>">
                                <?=@$Lh07_dpubl?>
                            </td>
                            <td>
                                <?php db_inputdata('h07_dpubl', @$h07_dpubl_dia, @$h07_dpubl_mes, @$h07_dpubl_ano, true, 'text', $db_opcao, "");?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lh25_nomeacao}</b>"?>
                            </td>
                            <td>
                                <?php db_inputdata('h25_nomeacao', $h25_nomeacao_dia, $h25_nomeacao_mes, $h25_nomeacao_ano, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_impofi?>">
                                <?=@$Lh07_impofi?>
                            </td>
                            <td>
                                <?php db_input('h07_impofi', 30, $Ih07_impofi, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_fundam?>">
                                <?php db_ancora(/* @$Lh07_fundam */ "Tipo de dispositivo" ,"js_pesquisah07_fundam(true)",$db_opcao);?>
                            </td>
                            <td>
                                <?php
                                db_input('h07_fundam', 6, $Ih07_fundam, true, 'text', $db_opcao, "onchange='js_pesquisah07_fundam(false)'");
                                db_input('h04_descr', 20, $Ih04_descr, true, 'text', 3, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lh25_nrdispositivo}</b>"?>
                            </td>
                            <td>
                                <?php db_input('h25_nrdispositivo', 30, $Ih25_nrdispositivo, true, 'text', $db_opcao, ""); ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_defet?>">
                                <b>Data da posse</b>
                            </td>
                            <td>
                                <?php db_inputdata('h07_defet', @$h07_defet_dia, @$h07_defet_mes, @$h07_defet_ano, true, 'text', $db_opcao, "");?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Trh37_funcao?>">
                                <?php db_ancora("<b>Cargo atual:</b>", "", $db_opcao);?>
                            </td>
                            <td colspan="5">
                                <?php
                                db_input('rh37_funcao', 6, $Irh37_funcao, true, 'text', 3, "");
                                db_input('rh37_descr', 70, $Irh37_descr, true, 'text', 3, "onchange='js_pesquisah07_cant(false);'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_cant?>">
                                <?php db_ancora(@$Lh07_cant, "js_pesquisah07_cant(true);", $db_opcao);?>
                            </td>
                            <td colspan="5">
                                <?php
                                db_input('h07_cant', 6, $Ih07_cant, true, 'text', $db_opcao, "onchange='js_pesquisah07_cant(false);'");
                                db_input('rh37_descr2', 70, $Irh37_descr, true, 'text', 3, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_ddem?>">
                                <?=@$Lh07_ddem?>
                            </td>
                            <td>
                                <?php db_inputdata('h07_ddem', @$h07_ddem_dia, @$h07_ddem_mes, @$h07_ddem_ano, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_class?>">
                                <?=@$Lh07_class?>
                            </td>
                            <td>
                                <?php db_input('h07_class', 6, $Ih07_class, true, 'text', $db_opcao, "");?>
                            </td>
                            <td nowrap title="<?=@$Th07_termin?>">
                                <?=@$Lh07_termin?>
                            </td>
                            <td >
                                <?php db_inputdata('h07_termin', @$h07_termin_dia, @$h07_termin_mes, @$h07_termin_ano, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="declaracao_if_fonte">
                                <?php echo "<b>{$Lh25_irfonte}</b>"?>
                            </td>
                            <td>
                                <?php
                                echo "<script> const exibeFonte = " . $h25_irfonte . ";</script>";
                                $array = array("1"=>"Sim", "0"=>"Não");
                                db_select("h25_irfonte", $array, true, $db_opcao, "onChange=jsChangeReferencia(this.id,this.value)");
                                ?>
                            </td>
                            <td id="jsLabelRefencia">
                                <?php echo "<b>{$Lh25_referenciair}</b>";?>
                            </td>
                            <td id="jsInputRefencia">
                                <?php db_input('h25_referenciair', 10, $h25_referenciair, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_dtbase}</b>"?>
                            </td>
                            <td>
                                <?php
                                $mesNumero = \DBDate::getMesesExtenso();
                                $mesNumero[''] = "Não Informado.";

                                db_select("h25_dtbase", $mesNumero, true, $db_opcao, '');
                                ?>
                            </td>
                        </tr>                        <tr>
                            <td nowrap title="<?=@$Th07_refe?>">
                                <?php db_ancora(@$Lh07_refe, "js_pesquisah07_refe(true)", $db_opcao);?>
                            </td>
                            <td colspan="5">
                                <?php
                                db_input('h07_refe', 6, $Ih07_refe, true, 'text', $db_opcao, "onchange='js_pesquisah07_refe(false)'");
                                db_input('h06_concur', 70, $Ih04_descr, true, 'text', 3, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_area?>">
                                <?php db_ancora(@$Lh07_area, "js_pesquisah07_area(true)", $db_opcao);?>
                            </td>
                            <td colspan="5">
                                <?php
                                db_input('h07_area', 6, $Ih07_area, true, 'text', $db_opcao, "onchange='js_pesquisah07_area(false)'");
                                db_input('h05_descr', 70, $Ih04_descr, true, 'text', 3, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?=@$Th07_justif?>">
                                <?=@$Lh07_justif?>
                            </td>
                            <td colspan="5">
                                <?php db_textarea('h07_justif', 2, 78, $Ih07_justif, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                          <td>
                            <input type="button" id="Nom" value="Adicionar Nomeação" onclick='js_mostraNom(true);'>
                          </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>

       <!-- Código para extensão -->

        <tr>
            <td>
                <fieldset>
                    <legend>Dados da aposentadoria</legend>
                    <table>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_portariaaposentadoria}</b>"?>
                            </td>
                            <td colspan="3">
                                <?php db_input('h25_portariaaposentadoria', 30, $h25_portariaaposentadoria, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_dataaposentadoria}</b>";?>
                            </td>
                            <td>
                                <?php db_inputdata('h25_dataaposentadoria', @$h25_dataaposentadoria_dia, @$h25_dataaposentadoria_mes, @$h25_dataaposentadoria_ano, true, 'text', $db_opcao, "");?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lh25_contaraposentadoria}</b>";?>
                            </td>
                            <td>
                                <?php db_inputdata('h25_contaraposentadoria', @$h25_contaraposentadoria_dia, @$h25_contaraposentadoria_mes, @$h25_contaraposentadoria_ano, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_processoaposentadoria}</b>"?>
                            </td>
                            <td>
                                <?php
                                $x = [ "0" => "Não", "1" => "Sim"];
                                db_select("processoaposentadoria", $x, true, $db_opcao, "onChange=jsMontaCampoProcesso(this.id,this.value)");
                                ?>
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php
                                    //db_input('h25_nrprocessoaposentadoria', 30, $h25_nrprocessoaposentadoria, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            $displayAposentadoria = "display: none;";
                            if ($processoaposentadoria) {
                                $displayAposentadoria = '';
                            }
                            ?>
                            <td id="ProcAposentadoria" name="ProcAposentadoria">
                                <div id="processoaposentadoria_id1" style="<?php echo $displayAposentadoria?>">
                                    <?php db_ancora("<b>Processo :</b>","jsPesquisaProcesso(true, 'A')",$db_opcao); ?>
                                </div>
                            </td>
                            <td id="ProcAposentadoriaValor" name="ProcAposentadoriaValor">
                                <div id="processoaposentadoria_cod1" style="<?php echo $displayAposentadoria?>">
                                <?php
                                db_input('h25_nrprocessoaposentadoria', 14, $Ip58_numero, true, 'text', $db_opcao, 'onchange="jsPesquisaProcessoLimpa(false, 1);"', "", "", "", 14);
                                db_input('h25_processoaposentadoria', 10, $Ip58_codproc, true, 'hidden', $db_opcao, '');
                                db_input('p58_requer_h25_processoaposentadoria', 40, $Iz01_nome, true, 'text', 3, '');
                                ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend>Dados da exoneração</legend>
                    <table>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_portariaexoneracao}</b>"?>
                            </td>
                            <td>
                                <?php db_input('h25_portariaexoneracao', 30, $h25_portariaexoneracao, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_dataexoneracao}</b>";?>
                            </td>
                            <td>
                                <?php
                                if (isset($h25_dataexoneracao) && $h25_dataexoneracao != null) {
                                   $data = explode('-', $h25_dataexoneracao);
                                   $h25_dataexoneracao_ano = $data[0];
                                   $h25_dataexoneracao_mes = $data[1];
                                   $h25_dataexoneracao_dia = $data[2];
                                }
                                db_inputdata('h25_dataexoneracao', @$h25_dataexoneracao_dia, @$h25_dataexoneracao_mes, @$h25_dataexoneracao_ano, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lh25_contarexoneracao}</b>";?>
                            </td>
                            <td>
                                <?php
                                if (isset($h25_contarexoneracao) && $h25_contarexoneracao != null) {
                                   $data = explode('-', $h25_contarexoneracao);
                                   $h25_contarexoneracao_ano = $data[0];
                                   $h25_contarexoneracao_mes = $data[1];
                                   $h25_contarexoneracao_dia = $data[2];
                                }
                                db_inputdata('h25_contarexoneracao', @$h25_contarexoneracao_dia, @$h25_contarexoneracao_mes, @$h25_contarexoneracao_ano, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_processoexoneracao}</b>"?>
                            </td>
                            <td>
                                <?php
                                $x = ["0" => "Não", "1" => "Sim"];
                                db_select("processoexoneracao", $x, true, $db_opcao, "onChange=jsMontaCampoProcesso(this.id,this.value)");
                                ?>
                            </td>
                            <td>
                                <!-- <b>Número do processo</b> -->
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            $displayExoneracao = "display: none;";
                            if ($processoexoneracao) {
                                $displayExoneracao = '';
                            }
                            ?>
                            <td id="ProcExoneracao" name="ProcExoneracao">
                                <div id="processoexoneracao_id1" style="<?php echo $displayExoneracao;?>">
                                    <?php db_ancora("<b>Processo :</b>", "jsPesquisaProcesso(true,'E')", $db_opcao);?>
                                </div>
                            </td>
                            <td id="ProcExoneracaoValor" name="ProcExoneracaoValor">
                                <div id="processoexoneracao_cod1" style="<?php echo $displayExoneracao;?>">
                                <?php
                                db_input('h25_nrprocessoexoneracao', 14, $Ip58_numero, true, 'text', $db_opcao, 'onchange="jsPesquisaProcessoLimpa(false,2);"', "", "", "", 14);
                                db_input('h25_processoexoneracao', 10, $Ip58_codproc, true, 'hidden', $db_opcao, '');
                                db_input('p58_requer_h25_processoexoneracao', 40, $Iz01_nome, true, 'text', 3, '');
                                ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend>Dados da reintegração</legend>
                    <table>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_portariareintegracao}</b>";?>
                            </td>
                            <td>
                                <?php db_input('h25_portariareintegracao', 30, $h25_portariareintegracao, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr> <tr>
                            <td>
                                <?php echo "<b>{$Lh25_tiporeintegracao}</b>";?>
                            </td>
                            <td>
                                <?php 
                                    $aOpcoesReintegracao  = array(
                                    0 => 'Não informado',
                                    1 => '1 - Reintegração por decisão judicial',
                                    2 => '2 - Reintegração por anistia legal', 
                                    3 => '3 - Reversão de servidor público', 
                                    4 => '4 - Recondução de servidor público', 
                                    5 => '5 - Reinclusão de militar', 
                                    6 => '6 - Revisão de reforma de militar', 
                                    9 => '9 - Outros'); 

                                    db_select('h25_tiporeintegracao', $aOpcoesReintegracao, true, 1, "onchange='jsMostraCampo(this)';onload='jsMostraCampo(this)'");
                                ?>
                            </td>
                        </tr>
                        <tr id="tdnumeroprocesso" style="visibility:collapse;">
                            <td>
                                <?php echo "<b>{$Lh25_numeroprocesso}</b>"?>
                            </td>
                            <td>
                                <?php
                                    db_input('h25_numeroprocesso', 20, $Ih25_numeroprocesso, true, 'hidden', $db_opcao, 'onchange="js_ValidaProcesso(this)"');
                                ?>
                            </td>
                        </tr>
                        <tr id="tdleianistia" style="visibility:collapse;">
                            <td>
                                <?php echo "<b>{$Lh25_leianistia}</b>"?>
                            </td>
                            <td>
                                <?php
                                    db_input('h25_leianistia', 13, $Ih25_leianistia, true, 'hidden', $db_opcao, 'onchange="js_ValidaLeiAnistia(this)"');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lh25_dataefetivoretorno}</b>"?>
                            </td>
                            <td>
                                <?php
                                    db_inputdata('h25_dataefetivoretorno', $h25_dataefetivoretorno_dia, $h25_dataefetivoretorno_mes, $h25_dataefetivoretorno_ano, true, 'text', $db_opcao, "onchange='jsValidaData(this)'");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lh25_datareintegracao}</b>"?>
                            </td>
                            <td>
                                <?php
                                    db_inputdata('h25_datareintegracao', $h25_datareintegracao_dia, $h25_datareintegracao_mes, $h25_datareintegracao_ano, true, 'text', $db_opcao, "onchange='jsValidaData(this)'");
                                ?>
                            </td>   
                        </tr>
                    <tr>
                        <?php
                        $displayReintegracao = "display: none;";
                        if ($processoreintegracao) {
                            $displayReintegracao = '';
                        }
                        ?>
                        <td id="ProcReintegracao" name="ProcReintegracao">
                            <div id="processoreintegracao_id1" style="<?php echo $displayReintegracao;?>">
                                <?php db_ancora("<b>Processo :</b>", "jsPesquisaProcesso(true,'R')", $db_opcao); ?>
                            </div>
                        </td>
                        <td id="ProcReintegracaoValor" name="ProcReintegracaoValor">
                            <div id="processoreintegracao_cod1" style="<?php echo $displayReintegracao;?>">
                                <?php
                                db_input('h25_nrprocessoreintegracao', 14, $Ip58_numero, true, 'text', $db_opcao, 'onchange="jsPesquisaProcessoLimpa(false,3);"', "", "", "", 14);
                                db_input('h25_processoreintegracao', 10, $Ip58_codproc, true, 'hidden', $db_opcao, '');
                                db_input('p58_requer_h25_processoreintegracao', 40, $Iz01_nome, true, 'text', 3, '');
                                ?>
                            </div>
                        </td>
                    </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <!-- Fim do Código para extensão -->
        <tr>
            <td>
                <fieldset>
                    <legend>Dados da deficiência</legend>
                    <table>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lrh253_fisica}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_input('rh253_matricula', 6, $Irh253_matricula, true, 'hidden', 3, "");
                                db_input('rh253_instit', 6, $Irh253_instit, true, 'hidden', 3, "");
                                db_select("rh253_fisica", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_visual}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_visual", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_auditiva}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_auditiva", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_mental}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_mental", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_intelectual}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_intelectual", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_reabilitado}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_reabilitado", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                            <td>
                                <?php echo "<b>{$Lrh253_cota}</b>";?>
                            </td>
                            <td>
                                <?php
                                db_select("rh253_cota", $simNao, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo "<b>{$Lrh253_observacao}</b>";?>
                            </td>
                            <td colspan="10">
                                <?php
                                   db_textarea('rh253_observacao', 3, 78, $Irh253_observacao, true, 'text', $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend>Dados Estagiários eSocial</legend>
                    <table>
                        <tr>
                            <td>
                                <b>Natureza do Estágio:</b>
                            </td>
                            <td>
                                <?php
                                $arrayOpcoesNatureza = [
                                    ""  => "Não Informado",
                                    "O" => 'O - Obrigatório',
                                    "N" => 'N - Não Obrigatório'
                                ];
                                db_select("rh260_naturezaestagio", $arrayOpcoesNatureza, true, $db_opcao, "style=margin-right:50px;");
                                ?>
                            </td>
                            <td>
                                <b>Nível do Estágio:</b>
                            </td>
                            <td>
                                <?php
                                $arrayOpcoesNivelEstagio = [
                                    ""  => "Não Informado",
                                    "1" => '1 - Fundamental',
                                    "2" => '2 - Médio',
                                    "3" => '3 - Formação Profissional',
                                    "4" => '4 - Superior',
                                    "8" => '8 - Especial',
                                    "9" => '9 - Mãe social (Lei 7.644/1987)'
                            ];
                                db_select("rh260_nivelestagio", $arrayOpcoesNivelEstagio, true, $db_opcao, "");
                                ?>
                            </td>
                            <tr>
                                <td>
                                    <b>Data Prevista Término do Estágio:</b>
                                </td>
                                <td>
                                <?php
                                    if (isset($rh260_dataterminoestagio) && $rh260_dataterminoestagio != null) {
                                        $data = explode('-', $rh260_dataterminoestagio);
                                        $rh260_dataterminoestagio_ano = $data[0];
                                        $rh260_dataterminoestagio_mes = $data[1];
                                        $rh260_dataterminoestagio_dia = $data[2];
                                    }
                                    db_inputdata('rh260_dataterminoestagio', @$rh260_dataterminoestagio_dia, @$rh260_dataterminoestagio_mes, @$rh260_dataterminoestagio_ano, true, 'text', $db_opcao, "");
                                ?>
                                </td>
                                <td>
                                    <b>Área de Atuação:</b>
                                </td>
                                <td>
                                    <?php db_input('rh260_areaatuacao', 14, $rh260_areaatuacao, true, 'text', $db_opcao, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>CNPJ Instituição de Ensino:</b>
                                </td>
                                <td>
                                    <?php db_input('rh260_cnpjinstensino', 14, $rh260_cnpjinstensino, true, 'text', $db_opcao, "");?>
                                </td>
                                <td>
                                    <b>Nº Apólice Seguro:</b>
                                </td>
                                <td>
                                    <?php db_input('rh260_apoliceseguro', 14, $rh260_apoliceseguro, true, 'text', $db_opcao, "");?>
                                </td>                            </tr>
                            <tr>
                                <td>
                                    <b>CNPJ Agente de Integração:</b>
                                </td>
                                <td>
                                    <?php db_input('rh260_cnpjagentintegracao', 14, $rh260_cnpjagentintegracao, true, 'text', $db_opcao, "");?>
                                </td>
                                <td>
                                    <b>CPF Supervisor Estágio:</b>
                                </td>
                                <td>
                                    <?php db_input('rh260_cpfsupervisor', 14, $rh260_cpfsupervisor, true, 'text', $db_opcao, "");?>
                                </td>
                            </tr>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend>Dados Migração eSocial</legend>
                    <table>
                        <tr>
                            <td>
                                <?php echo @$Lrh01_matriculaanterior?>
                            </td>
                            <td>
                                <?php db_input('rh01_matriculaanterior', 14, $rh01_matriculaanterior, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend>Dados Aprendiz eSocial</legend>
                    <table>
                        <tr>
                            <td>
                                <b>Modalidade de Contratação do Aprendiz:</b>
                            </td>
                            <td>
                                <?php
                                $arrayIndAprend = [
                                    ""  => "Não Informado",
                                    "1" => '1 - Contratação direta',
                                    "2" => '2 - Contratação indireta'
                                ];
                                db_select("rh312_modalidade", $arrayIndAprend, true, $db_opcao, "style=margin-right:50px; onchange='validaAprendiz();'");
                                ?>
                            </td>
                        </tr>
                        <tr id="aprendizDireto" style="display: none;">
                            <td>
                                <b>CNPJ da Entidade Qualificadora, contratação direta:</b>
                            </td>
                            <td>
                                <?php db_input('rh312_cnpjqualificadora', 14, $rh312_cnpjqualificadora, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr id="aprendizIndireto" style="display: none;">
                            <td>
                                <p style="margin: 0px;">
                                    <b>Inscrição do estabelecimento para o qual a contratação </b>                                    
                                </p>
                                <p style="margin: 0px;">
                                <b>de aprendiz foi efetivada, no caso de contratação indireta:</b>                                    
                                </p>
                            </td>
                            <td>
                                <?php db_input('rh312_cnpjefetivada', 14, $rh312_cnpjefetivada, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p style="margin: 0px;">
                                    <b>CNPJ do estabelecimento onde estão</b>
                                </p>
                                <p style="margin: 0px;">
                                    <b>sendo realizadas as atividades práticas:</b>
                                </p>
                            </td>
                            <td>
                                <?php db_input('rh312_cnpjpratica', 14, $rh312_cnpjpratica, true, 'text', $db_opcao, "");?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
            </td>
        </tr>
    </table>
</form>

<!-- Código para extensão -->

<?php
if (db_getsession('DB_nome_modulo') == 'Pessoal') {
    $iFrame = '.iframe_rhadmissional';
} else {
    $iFrame = '';
}
?>

<!-- Fim do código para extensão -->

<script>

    jsMostraCampo(document.form1.h25_tiporeintegracao);

    if(document.form1.h07_regist.value != '' && document.form1.z01_nome.value == ''){
        js_OpenJanelaIframe('','db_iframe_rhpessoal','func_rhpessoal.php?pesquisa_chave='+document.form1.h07_regist.value+'&funcao_js=parent.js_mostrarhpessoal','Pesquisa',false);
    }

    function js_imprime(){
        var oVariavel   = new js_criaObjetoVariavel("$matricula",document.form1.h07_regist.value);
        var iModelo     = document.form1.modeloposse.value;
        var aParametros = new Array();
        aParametros[0] = oVariavel;
        if ( iModelo == "") {
            alert('Configurar modelo de termo de posse!');
            return false;
        }
        js_imprimeRelatorio(iModelo,js_downloadArquivo,JSON.stringify(aParametros));
    }

    function js_pesquisah07_area(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_areas','func_areas.php?funcao_js=parent.js_mostraareas1|h05_codigo|h05_descr','Pesquisa',true);
        }else{
            if(document.form1.h07_area.value != ''){
                js_OpenJanelaIframe('','db_iframe_areas','func_areas.php?pesquisa_chave='+document.form1.h07_area.value+'&funcao_js=parent.js_mostraareas','Pesquisa',false);
            }else{
                document.form1.h05_descr.value = '';
            }
        }
    }

    function js_mostraareas(chave,erro){
        document.form1.h05_descr.value = chave;
        if(erro==true){
            document.form1.h07_area.focus();
            document.form1.h07_area.value = '';
        }
    }

    function js_mostraareas1(chave1,chave2){
        document.form1.h07_area.value = chave1;
        document.form1.h05_descr.value = chave2;
        db_iframe_areas.hide();
    }

    function js_pesquisah07_refe(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_concur','func_concur.php?funcao_js=parent.js_mostraconcur1|h06_refer|h06_concur','Pesquisa',true);
        }else{
            if(document.form1.h07_refe.value != ''){
                js_OpenJanelaIframe('','db_iframe_concur','func_concur.php?pesquisa_chave='+document.form1.h07_refe.value+'&funcao_js=parent.js_mostraconcur','Pesquisa',false);
            }else{
                document.form1.h06_concur.value = '';
            }
        }
    }

    function js_mostraconcur(chave,erro){
        document.form1.h06_concur.value = chave;
        if(erro==true){
            document.form1.h07_refe.focus();
            document.form1.h07_refe.value = '';
        }
    }

    function js_mostraconcur1(chave1,chave2){
        document.form1.h07_refe.value = chave1;
        document.form1.h06_concur.value = chave2;
        db_iframe_concur.hide();
    }

    function js_pesquisah07_fundam(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_flegal','func_flegal.php?funcao_js=parent.js_mostraflegal1|h04_codigo|h04_descr','Pesquisa',true);
        }else{
            if(document.form1.h07_fundam.value != ''){
                js_OpenJanelaIframe('','db_iframe_flegal','func_flegal.php?pesquisa_chave='+document.form1.h07_fundam.value+'&funcao_js=parent.js_mostraflegal','Pesquisa',false);
            }else{
                document.form1.h04_descr.value = '';
            }
        }
    }

    function js_mostraflegal(chave,erro){
        document.form1.h04_descr.value = chave;
        if(erro==true){
            document.form1.h07_fundam.focus();
            document.form1.h07_fundam.value = '';
        }
    }

    function js_mostraflegal1(chave1,chave2){
        document.form1.h07_fundam.value = chave1;
        document.form1.h04_descr.value = chave2;
        db_iframe_flegal.hide();
    }

    function js_pesquisah07_cant(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_rhfuncao','func_rhfuncao.php?funcao_js=parent.js_mostrarhfuncao1|rh37_funcao|rh37_descr','Pesquisa',true);
        }else{
            if(document.form1.h07_cant.value != ''){
                js_OpenJanelaIframe('','db_iframe_rhfuncao','func_rhfuncao.php?pesquisa_chave='+document.form1.h07_cant.value+'&funcao_js=parent.js_mostrarhfuncao','Pesquisa',false);
            }else{
                document.form1.rh37_descr2.value = '';
            }
        }
    }

    function js_mostrarhfuncao(chave,erro){
        document.form1.rh37_descr2.value = chave;
        if(erro==true){
            document.form1.h07_cant.focus();
            document.form1.h07_cant.value = '';
        }
    }

    function js_mostrarhfuncao1(chave1,chave2){
        document.form1.h07_cant.value = chave1;
        document.form1.rh37_descr2.value = chave2;
        db_iframe_rhfuncao.hide();
    }

    function js_pesquisah07_regist(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_rhpessoal','func_rhpessoal.php?funcao_js=parent.js_mostrarhpessoal1|rh01_regist|z01_nome','Pesquisa',true);
        }else{
            if(document.form1.h07_regist.value != ''){
                js_OpenJanelaIframe('','db_iframe_rhpessoal','func_rhpessoal.php?pesquisa_chave='+document.form1.h07_regist.value+'&funcao_js=parent.js_mostrarhpessoal','Pesquisa',false);
            }else{
                document.form1.z01_nome.value = '';
                <?php
                echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."'";
                ?>
            }
        }
    }

    function js_mostrarhpessoal(chave,erro){
        document.form1.z01_nome.value = chave;
        if(erro==true){
            document.form1.h07_regist.focus();
            document.form1.h07_regist.value = '';
        }else{
            document.form1.submit();
        }
    }

    function js_mostrarhpessoal1(chave1,chave2){
        document.form1.h07_regist.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_rhpessoal.hide();
        document.form1.submit();
    }

    function js_pesquisa(){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_admissao','func_admissao.php?funcao_js=parent.js_preenchepesquisa|h07_regist','Pesquisa',true);
    }

    function js_preenchepesquisa(chave){
        db_iframe_admissao.hide();
        <?php
        if($db_opcao != 1 && db_getsession('DB_nome_modulo') != 'Pessoal'){
            echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
        }
        ?>
    }

    /** Códigos para extensão */
    /**
     * Monta o campo do processo
     *
     * @param {string} id
     * @param {string} value
     * @return void
     */
     function jsMontaCampoProcesso(id, value){
        var fieldId1 = id+'_id1'
        , fieldCode1 = id+'_cod1'
        , idImputProcesso = 'h25_'+id
        , idImputNrProcesso = 'h25_nr'+id
        , descricao = 'p58_requer_h25_'+id;

        if (value == '1') {
            $(fieldId1).style.display = '';
            $(fieldCode1).style.display = '';
        } else {
            $(fieldId1).style.display = 'none';
            $(fieldCode1).style.display = 'none';
            $(idImputProcesso).value = '';
            $(idImputNrProcesso).value = '';
            $(descricao).value = '';
        }
    }

    var processo = '';
    var campoProcesso = 'numero_processo_';

    /**
     * Abre o iframe para pesquisa do processo
     * O parâmetro param recebe as letras que definem o tipo de processo.
     *
     * @param {boolean} mostra
     * @param {string} param
     * @return void
     */
     function jsPesquisaProcessoLimpa(mostra, param){
        switch(param) {
            case 1:
                document.form1.p58_requer_h25_processoaposentadoria.value = '';
                document.form1.h25_processoaposentadoria.value = '';
            break;
            case 2:
                document.form1.p58_requer_h25_processoexoneracao.value = '';
                document.form1.h25_processoexoneracao.value = '';

            break;
            case 3:
                document.form1.p58_requer_h25_processoreintegracao.value = '';
                document.form1.h25_processoreintegracao.value = '';
            break;
        }

     }

     function jsPesquisaProcesso(mostra, param){
        switch(param) {
            case 'A':
            processo = 'aposentadoria';
            break;
            case 'E':
            processo = 'exoneracao';
            break;
            case 'R':
            processo = 'reintegracao';
            break;
        }

        var pesquisa = 'j39_processo_'+ processo;

        if(mostra==true) {
            js_OpenJanelaIframe('','db_iframe_processo','func_protprocesso.php?funcao_js=parent.jsMostraProtProcesso1|p58_numero|z01_nome|p58_codproc','Pesquisa',true);
        } else {
            js_OpenJanelaIframe('','db_iframe_processo','func_protprocesso.php?pesquisa_chave='+document.getElementById(pesquisa).value+'&funcao_js=parent.jsMostraProtProcesso&chave_p58_numero=1','Pesquisa',false);
        }
     }

    /**
     * Mostra o processo
     *
     * @param {string} chave
     * @param {string} chave1
     * @param {string} chave2
     * @param {string} erro
     * @return void
     */
    function jsMostraProtProcesso(chave,chave1,chave2,erro){
        var field2 = 'p58_requer_'+processo;
        var field3 = j39_processo_ + processo;

        document.getElementById(field1).value = chave1;
        if(erro==true){
            document.getElementById(field2).focus();
            document.getElementById(field2).value = '';
        }
    }

    /**
     * Mostra o processo
     *
     * @param {string} chave1
     * @param {string} chave2
     * @return void
     */
    function jsMostraProtProcesso1(chave1,chave2, chave3){

        var field1 = 'h25_processo'+processo;
        var field2 = 'p58_requer_h25_processo'+processo;
        var field3 = 'h25_nrprocesso'+processo;
        var str = chave1.split('/');
        document.getElementById(field1).value = chave3;
        document.getElementById(field2).value = chave2;
        document.getElementById(field3).value = chave1;
        db_iframe_processo.hide();
    }

    function jsChangeReferencia(id, value) {
        if (value == 1) {
            document.getElementById('jsLabelRefencia').style.display = '';
            document.getElementById('jsInputRefencia').style.display = '';
        } else {
            document.getElementById('jsLabelRefencia').style.display = 'none';
            document.getElementById('jsInputRefencia').style.display = 'none';
        }
    }


    function jsChangeHipotese(id, value) {
        if (value == 't') {
            document.getElementById('lblh25_hipleg').style.display = '';
            document.getElementById('id25_hipleg').style.display = '';
        } else {
            document.getElementById('lblh25_hipleg').style.display = 'none';
            document.getElementById('id25_hipleg').style.display = 'none';
        }
    }
    /** Fim do Código para extensão */

function js_mostraNom(mostra){
    if(mostra==true){
        js_OpenJanelaIframe('','db_iframe_assenta','rec1_assentanom001.php?h16_regist=<?=$h07_regist?>&h12_assent=NOM&h12_codigo=192','Nomeação',true,0);
    }
}

function jsMostraCampo(element){
    const h25_numeroprocesso   = document.getElementById("h25_numeroprocesso");
    const tdh25_numeroprocesso = document.getElementById("tdnumeroprocesso");
    const h25_leianistia       = document.getElementById("h25_leianistia");
    const tdleianistia         = document.getElementById("tdleianistia");

    h25_numeroprocesso.type = 'hidden';
    tdh25_numeroprocesso.style.visibility = 'collapse';
    h25_leianistia.type = 'hidden';
    tdleianistia.style.visibility = 'collapse';

    if(element.value == "1"){
        h25_numeroprocesso.type = 'text';
        tdh25_numeroprocesso.style.visibility = 'visible';
    }else if(element.value == "2"){
        h25_leianistia.type = 'text';
        tdleianistia.style.visibility = 'visible';
    }
}

function jsValidaData(element){
    let dataResc = criarData('<?php echo $sDataResc; ?>');
    if(element.name == "h25_datareintegracao"){
        let dataRetorno = criarData(document.getElementById('h25_dataefetivoretorno').value)
        if (isNaN(dataRetorno.getTime())) {
            element.value = "";
            alert("Data do efetivo retorno deve ser preenchida!");
            return;
        }
        let dataReintegracao =  criarData(element.value);
        if(dataReintegracao.getTime() > dataRetorno.getTime() || dataReintegracao.getTime() < dataResc.getTime()){
            element.value = "";
            alert("Deve ser uma data igual ou anterior à data do efetivo retorno ao trabalho e posterior à data do desligamento");
            return;
        }
    }else if(element.name == "h25_dataefetivoretorno"){
        let dataRetorno = criarData(element.value)
        
        if(dataResc.getTime() > dataRetorno.getTime()){
            element.value = "";
            alert("Deve ser uma data posterior à data de desligamento do trabalhador!");
            return;
        }
    }
}

function js_ValidaProcesso(element){
    if(element.value.length != 20){
        alert("Número do processo precisa ter 20 digitos!");
        element.value = "";
    }
}

function js_ValidaLeiAnistia(element){
    if(element.value.length < 5){
        alert("Lei de Anistia precisa ter no mínimo 5 digitos e no máximo 13!");
        element.value = "";
    }
}

function criarData(str) {
    var partes = str.split("/");
    return new Date(partes[2], partes[1] - 1, partes[0]);
}

    function validaAprendiz()
    {
        let modalidade = document.getElementById("rh312_modalidade").value;
        let contratacaoDireta = document.getElementById("aprendizDireto");
        let contratacaoIndireta = document.getElementById("aprendizIndireto");

        contratacaoIndireta.style.display = "none";
        contratacaoDireta.style.display = "none";

        if (modalidade == "1") {
            contratacaoDireta.style.display = "contents";
        }

        if (modalidade == "2") {
            contratacaoIndireta.style.display = "contents";
        }        
    }
    
    (function () {
        validaAprendiz();


        if (exibeHipotese != 't') {
            document.getElementById('lblh25_hipleg').style.display = 'none';
            document.getElementById('idh25_hipleg').style.display = 'none';
        }

        if (exibeFonte != 1) {
            document.getElementById('jsLabelRefencia').style.display = 'none';
            document.getElementById('jsInputRefencia').style.display = 'none';
        }

        if(document.getElementById('processoaposentadoria').value == '1') {
            document.getElementById('processoaposentadoria_id1').style.display = 'block';
            document.getElementById('processoaposentadoria_cod1').style.display = 'block';

        }else{
            document.getElementById('processoaposentadoria_id1').style.display = 'none';
            document.getElementById('processoaposentadoria_cod1').style.display = 'none';
        }

        if (document.getElementById('processoexoneracao').value == '1') {
            document.getElementById('processo_exoneracao_id').style.display = 'block';
            document.getElementById('processo_exoneracao_cod').style.display = 'block';
        } else {
            document.getElementById('processo_exoneracao_id').style.display = 'none';
            document.getElementById('processo_exoneracao_cod').style.display = 'none';
        }

        if (document.getElementById('processoreintegracao').value == '1') {
            document.getElementById('processo_reintegracao_id').style.display = 'block';
            document.getElementById('processo_reintegracao_cod').style.display = 'block';
        } else {
            document.getElementById('processo_reintegracao_id').style.display = 'none';
            document.getElementById('processo_reintegracao_cod').style.display = 'none';
        }
        
    })();

</script>
