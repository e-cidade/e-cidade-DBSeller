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

    use ECidade\Tributario\Juridico\CartorioExtrajudicial\Repository\CartorioExtraTipoRepository;

    $clitbidadosimovel->rotulo->label();
    $clitbidadosimovelsetorloc->rotulo->label();
    $clitbi->rotulo->label();
    $clitburbano->rotulo->label();
    $clitbirural->rotulo->label();
    $clitbiruralcaract->rotulo->label();
    $cllocalidaderural->rotulo->label();

    $clrotulo = new rotulocampo;

    $clrotulo->label("it04_descr");
    $clrotulo->label("it07_descr");
    $clrotulo->label("j05_descr");
    $clrotulo->label("j31_codigo");
    $clrotulo->label("j40_refant");

    $tipo = $oGet->tipo;

    if ( $oGet->tipo == "urbano") {
        $sPrefix     = "do ";
        $sTerraLabel = "Terreno";
        $sMedida	   = "m²";
    } else {
        $sPrefix     = "da ";
        $sTerraLabel = "Terra";
        $sMedida	   = "ha";
    }

    $cartorioExtraTipoRepository = CartorioExtraTipoRepository::getInstance();
    $aCartorios = $cartorioExtraTipoRepository->setOuterCondition("j168_tiposcartorioextra IN (2,3)")
        ->setGroupBy("j167_sequencial, j167_descricao")
        ->setOrderBy("j167_sequencial")
        ->setCampos("cartorioextra.*")
        ->get();
?>
<center>
    <form name="form1" method="post" action="">
        <table width="750px;">
            <tr align="center">
                <td>
                    <strong>I.T.B.I. <?=strtoupper($oGet->tipo)?></strong>
                </td>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <legend>
                            <strong>Dados ITBI</strong>
                        </legend>
                        <table width="100%">
                            <tr>
                                <td width="16%">
                                    <strong>Código da ITBI:</strong>
                                </td>
                                <td align="left">
                                    <?php
                                        db_input('it01_guia'      ,20,$Iit01_guia,true,'text',3);

                                        db_input('j01_matric'     ,10,"",true,'hidden',3);
                                        db_input('it22_sequencial',10,"",true,'hidden',3);
                                        db_input('listaFormas'    ,10,"",true,'hidden',3);
                                        db_input('tipo'	         ,10,"",true,'hidden',3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?=@$Tit01_mail?>">
                                    <?=@$Lit01_mail?>
                                </td>
                                <td>
                                    <?php
                                        db_input('it01_mail',50,$Iit01_mail,true,'text',$db_opcao,"");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Processos registrado no sistema?">
                                    <strong>Processo do Sistema:</strong>
                                </td>
                                <td nowrap >
                                    <select name="" id="" onchange='js_processoSistema(this.value)' style='width: 95px'>
                                        <option value="1" <?= (empty($it01_dtprocesso) ? "selected='selected'" : "") ?>>SIM</option>
                                        <option value="0" <?= (!empty($it01_dtprocesso) ? "selected='selected'" : "") ?>>NÃO</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="processoSistema">
                                <td nowrap title="<?= @$Tp58_codproc ?>">
                                    <strong>
                                        <?php
                                            db_ancora('Processo:', 'js_pesquisaProcesso(true)', $db_opcao);
                                        ?>
                                    </strong>
                                </td>
                                <td nowrap>
                                    <?php
                                        db_input('it01_processo', 10, false, true, 'text', $db_opcao, 'onchange="js_pesquisaProcesso(false)"');
                                        db_input('p58_requer', 60, false, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                            <tr id="processoExterno1" style="display: none;">
                                <td nowrap title="Número do processo externo">
                                    <strong>Processo:</strong>
                                </td>
                                <td nowrap>
                                    <?php
                                        db_input('it01_processoexterno', 10, "", true, 'text', $db_opcao, null, null, null,
                                            "background-color: rgb(230, 228, 241);");
                                    ?>
                                </td>
                            </tr>

                            <tr id="processoExterno2" style="display: none;">
                                <td nowrap title="Número do processo externo">
                                    <strong>
                                        Titular do Processo:
                                    </strong>
                                </td>
                                <td nowrap>
                                    <?php
                                        db_input('it01_tituprocesso', 74, 'false', true, 'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>

                            <tr id="processoExterno3" style="display: none;">
                                <td nowrap title="Número do processo externo">
                                    <strong>
                                        Data do Processo:
                                    </strong>
                                </td>
                                <td nowrap>
                                    <?php
                                        db_inputdata('it01_dtprocesso', @$it01_dtprocesso_dia, @$it01_dtprocesso_mes, @$it01_dtprocesso_ano, true,
                                            'text', $db_opcao);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap>
                                    <strong>Cartório:</strong>
                                </td>
                                <td nowrap >
                                    <select name="j167_sequencial" id="j167_sequencial">
                                        <option value="NULL"></option>
                                        <?php foreach ($aCartorios as $oCatorio) : ?>
                                            <option value="<?= $oCatorio->j167_sequencial ?>" <?= ((isset($it01_cartorioextra) AND $it01_cartorioextra == $oCatorio->j167_sequencial) ? "selected='selected'" : "") ?>><?= $oCatorio->j167_descricao ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <legend>
                            <strong>Dados do Imóvel - <?=$sTerraLabel?></strong>
                        </legend>
                        <table width="100%">
                            <tr>
                                <td>
                                    <fieldset>
                                        <legend>
                                            <strong>Localização</strong>
                                        </legend>
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    <strong>Setor/Bairro:</strong>
                                                </td>
                                                <td colspan="1">
                                                    <?php
                                                        db_input('it22_setor',20,$Iit22_setor,true,'text',@$db_opcao_plugin);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?=@$Tit22_descrlograd?>">
                                                    <?=@$Lit22_descrlograd?>
                                                </td>
                                                <td colspan="3">
                                                    <?php
                                                        db_input('it22_descrlograd',112,$Iit22_descrlograd,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?=@$Tit22_numero?>">
                                                    <?=@$Lit22_numero?>
                                                </td>
                                                <td style="width: 160px;">
                                                    <?php
                                                        db_input('it22_numero',20,$Iit22_numero,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                </td>
                                                <td nowrap title="<?=@$Tit22_compl?>" style="width: 87px">
                                                    <?=@$Lit22_compl?>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it22_compl',74,$Iit22_compl,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td nowrap title="<?=@$Tit22_quadra?>">
                                                    <?=@$Lit22_quadra?>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it22_quadra',20,$Iit22_quadra,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                </td>
                                                <td nowrap title="<?=@$Tit22_lote?>" style="width: 87px">
                                                    <?=@$Lit22_lote?>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it22_lote',20,$Iit22_lote,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                </td>
                                            </tr>

                                            <?php if ( $oGet->tipo == "urbano" ) {?>

                                                <tr>
                                                    <td nowrap title="<?=@$Tit05_itbisituacao?>">
                                                        <?php
                                                            db_ancora(@$Lit05_itbisituacao,"js_pesquisait05_itbisituacao(true);",$db_opcao);
                                                        ?>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('it05_itbisituacao',20,$Iit05_itbisituacao,true,'text',$db_opcao," onchange='js_pesquisait05_itbisituacao(false);'");
                                                            db_input('it07_descr',87,$Iit07_descr,true,'text',3,'');
                                                        ?>
                                                    </td>
                                                </tr>

                                            <?php } else { ?>
                                                <tr>
                                                    <td>
                                                        <?=@$Lit18_coordenadas?>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('it18_coordenadas',112,$Iit18_coordenadas,true,'text',$db_opcao);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php
                                                            db_ancora(@$Lj137_sequencial,"js_pesquisalocalidaderural(true);",$db_opcao);
                                                        ?>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('j137_sequencial',20,@$Ij137_sequencial,true,'text',$db_opcao," onchange='js_pesquisalocalidaderural(false);'");
                                                            db_input('j137_descricao',88,@$Ij137_descricao,true,'text',3,'');
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Localização:</strong>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('it18_localimovel',112,$Iit18_localimovel,true,'text',$db_opcao);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Distância da Cidade:</strong>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('it18_distcidade',20,$Iit18_distcidade,true,'text',$db_opcao);
                                                        ?>
                                                        <strong>Km</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        <strong>Imóvel faz frente para logradouro ?</strong>
                                                        <input type="radio" name="lFrenteLogradouro" value="s" onChange="js_frenteLogradouro(this.value);"<?=((isset($it18_nomelograd) && trim($it18_nomelograd)!="")?"checked":"")?>>Sim</input>
                                                        <input type="radio" name="lFrenteLogradouro" value="n" onChange="js_frenteLogradouro(this.value);"<?=(!isset($it18_nomelograd)||(isset($it18_nomelograd) && trim($it18_nomelograd)=="")?"checked":"")?>>Não</input>
                                                    </td>
                                                </tr>
                                                <tr id="frenteLogradouro" <?=($db_opcao!=1||(isset($it18_nomelograd) && trim($it18_nomelograd)!="")?"":"style='display:none'")?>>
                                                    <td>
                                                        <strong>Nome Logradouro:</strong>
                                                    </td>
                                                    <td colspan="3">
                                                        <?php
                                                            db_input('it18_nomelograd',112,$Iit18_nomelograd,true,'text',$db_opcao);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        <?php
                                                            db_ancora("<strong>Característica do Imóvel</strong>","js_caract('imovel');",$db_opcao);
                                                            db_input('valorCaracImovel',20,"",true,'hidden',$db_opcao,"");
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        <?php
                                                            db_ancora("<strong>Característica de Utilização do Imóvel</strong>","js_caract('util');",$db_opcao);
                                                            db_input('valorCaracUtil',20,"",true,'hidden',$db_opcao,"");
                                                        ?>
                                                    </td>
                                                </tr>

                                            <?php } ?>

                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <fieldset>
                                        <legend>
                                            <strong>Medidas</strong>
                                        </legend>
                                        <table width="100%">
                                            <tr>
                                                <td style="width: 74px !important;">
                                                    <strong>Área Total:</strong>
                                                </td>
                                                <td style="width: 190px !important;">
                                                    <?php
                                                        db_input('it01_areaterreno',20,$Iit01_areaterreno,true,'text',@$db_opcao_plugin,"");
                                                    ?>
                                                    <strong><?=$sMedida?></strong>
                                                </td>
                                                <td colspan="1" width="170px">
                                                    <label class="bold" for="it01_percentualareatransmitida" id="lbl_it01_percentualareatransmitida"><?php echo $Lit01_percentualareatransmitida; ?></label>
                                                </td>
                                                <td colspan="1" width="180px">
                                                    <?php
                                                        db_input("it01_percentualareatransmitida", 20, $Iit01_percentualareatransmitida, true, "text", $db_opcao, " onblur=\"js_calculaPorcentagem(this, $('it01_areatrans'), false)\"");
                                                    ?>
                                                </td>
                                                <td>
                                                    <strong>Área Transmitida:</strong>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it01_areatrans',15,$Iit01_areatrans,true,'text',$db_opcao,"");
                                                    ?>
                                                    <strong><?=$sMedida?></strong>
                                                </td>
                                            </tr>

                                            <?php if ( $oGet->tipo == "urbano") {?>

                                                <tr>
                                                    <td>
                                                        <strong>Frente:</strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            db_input('it05_frente',20,$Iit05_frente,true,'text',@$db_opcao_plugin,"");
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                    <td <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                                                        <strong>Fundos:</strong>
                                                    </td>
                                                    <td <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                                                        <?php
                                                            db_input('it05_fundos',20,$Iit05_fundos,true,'text',$db_opcao,"");
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                </tr>
                                                <tr <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                                                    <td>
                                                        <strong>Lado Direito:</strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            db_input('it05_direito',20,$Iit05_direito,true,'text',$db_opcao,"");
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                    <td>
                                                        <strong>Lado Esquerdo:</strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            db_input('it05_esquerdo',20,$Iit05_esquerdo,true,'text',$db_opcao,"");
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                </tr>

                                            <?php } else { ?>
                                                <tr>
                                                    <td nowrap title="<?=@$Tit18_frente?>">
                                                        <?=@$Lit18_frente?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            db_input('it18_frente',20,$Iit18_frente,true,'text',$db_opcao,"");
                                                            db_input('it18_guia',10,$Iit18_guia,true,'hidden',$db_opcao,"");
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                    <td nowrap title="<?=@$Tit18_fundos?>">
                                                        <?=@$Lit18_fundos?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            db_input('it18_fundos',20,$Iit18_fundos,true,'text',$db_opcao,"")
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td nowrap title="<?=@$Tit18_prof?>">
                                                        <?=@$Lit18_prof?>
                                                    </td>
                                                    <td colspan="2">
                                                        <?php
                                                            db_input('it18_prof',20,$Iit18_prof,true,'text',$db_opcao,"")
                                                        ?>
                                                        <strong><?=$sMedida?></strong>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <fieldset>
                                        <legend>
                                            <strong>Dados Registro de Imóvel</strong>
                                        </legend>
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    <?php
                                                        db_ancora("<strong>Setor:</strong>","js_pesquisait29_setorloc(true);",$db_opcao);
                                                    ?>
                                                </td>
                                                <td colspan="3">
                                                    <?php
                                                        db_input('it29_setorloc',20,$Iit29_setorloc,true,'text',$db_opcao,"onChange='js_pesquisait29_setorloc(false);'");
                                                        db_input('j05_descr',87,$Ij05_descr,true,'text',3);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Quadra:</strong>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it22_quadrari',20,$Iit22_quadrari,true,'text',$db_opcao,"");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Lote:</strong>
                                                </td>
                                                <td>
                                                    <?php
                                                        db_input('it22_loteri',20,$Iit22_loteri,true,'text',$db_opcao,"");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Matrícula</strong>
                                                </td>
                                                <td colspan="3">
                                                    <?php
                                                        db_input('it22_matricri',20,$Iit22_matricri,true,'text',$db_opcao);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php if ($tipo == "urbano") : ?>
                                                <tr>
                                                    <td nowrap title="<?php echo $Tj40_refant; ?>">
                                                        <?php echo $Lj40_refant; ?>
                                                    </td>
                                                    <td colspan="1">
                                                        <?php
                                                            db_input('j40_refant',20,$Ij40_refant,true,'text',$db_opcao);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <td>
                    <fieldset>
                        <legend>
                            <strong>Observações</strong>
                        </legend>
                        <table width="100%">
                            <tr>
                                <td>
                                    <?php
                                        db_textarea('it01_obs',3,134,$Iit01_obs,true,'text',$db_opcao,"");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
        <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  onClick=" return js_validaCampos();">
        <?php if ( $db_opcao != 1 ) {?>
            <input name="visualizar" type="button" id="visualizar" value="Visualizar Guia" onclick="js_visualizar(<?=$it01_guia?>);" <?=($db_botao==false?"disabled":"")?>>
            <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
        <?php } ?>
    </form>
</center>
<script>
    function js_visualizar(guia) {
        var iGuia  = guia;
        var sParam = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height="+
            (screen.height-100)+",width="+(screen.width-100);
        window.open('reciboitbi.php?itbi='+iGuia,"",sParam);
    }

    function js_frenteLogradouro(sValor){

        if (document.getElementById('frenteLogradouro') != null) {
            if ( sValor == "s") {
                document.getElementById('frenteLogradouro').style.display = "";
            } else {
                document.getElementById('frenteLogradouro').style.display = "none";
            }
        }
    }

    function js_validaCampos() {

        var doc = document.form1;

        if ( doc.tipo.value == "urbano"  ) {

            if (<?= (isset($db_opcao_plugin) ? "false && " : "") ?> doc.it05_itbisituacao.value == "" ) {
                alert("Campo situação não informado!");
                return false;
            }
            if ( doc.it05_frente.value == "" ) {
                alert("Campo Frente não informado!");
                return false;
            }
            if ( doc.it05_direito.value == "" ) {
                alert("Campo Lado Direito não informado!");
                return false;
            }
            if ( doc.it05_esquerdo.value == "" ) {
                alert("Campo Lado Esquerdo não informado!");
                return false;
            }
            if ( doc.it05_fundos.value == "" ) {
                alert("Campo Fundos não informado!");
                return false;
            }

        } else {

            if (<?= (isset($db_opcao_plugin_rural) ? "false && " : "") ?> doc.it18_localimovel.value == "" ) {
                alert("Localização do imóvel não informada!");
                return false;
            }
            if ( doc.it18_distcidade.value == "" ) {
                alert("Distância da cidade não informada!");
                return false;
            }
            if ( doc.it18_frente.value == "" ) {
                alert("Campo Frente não informado!");
                return false;
            }
            if ( doc.it18_prof.value == "" ) {
                alert("Campo Profundidade não informado!");
                return false;
            }
            if ( doc.it18_fundos.value == "" ) {
                alert("Campo Fundos não informado!");
                return false;
            }

        }

        if ( doc.it01_areaterreno.value == "" ) {
            alert("Área Total não informada!");
            return false;
        }

        if ( doc.it01_areatrans.value == "" ) {
            alert("Área Transmitida não informada!");
            return false;
        }

        if ( doc.valorCaracImovel.value == "" ) {
            alert("Característica do Imóvel não informada!");
            return false;
        }

    }

    function js_caract(sTipo){

        var sQuery  = "?guia="+document.form1.it01_guia.value;
        sQuery += "&tipo="+sTipo;

        js_OpenJanelaIframe('','db_iframe_caract','itb1_itbiruralcaract002.php'+sQuery,'Pesquisa',true,0);

    }

    function js_fecha(){
        db_iframe_caract.hide();
    }

    function js_pesquisait22_itbi(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itbi','func_itbi.php?funcao_js=parent.js_mostraitbi1|it01_guia|it01_guia','Pesquisa',true);
        }else{
            if(document.form1.it22_itbi.value != ''){
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itbi','func_itbi.php?pesquisa_chave='+document.form1.it22_itbi.value+'&funcao_js=parent.js_mostraitbi','Pesquisa',false);
            }else{
                document.form1.it01_guia.value = '';
            }
        }
    }

    function js_mostraitbi(chave,erro){
        document.form1.it01_guia.value = chave;
        if(erro==true){
            document.form1.it22_itbi.focus();
            document.form1.it22_itbi.value = '';
        }
    }

    function js_mostraitbi1(chave1,chave2){
        document.form1.it22_itbi.value = chave1;
        document.form1.it01_guia.value = chave2;
        db_iframe_itbi.hide();
    }


    function js_pesquisalocalidaderural( mostra ){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_localidaderural','func_localidaderural.php?funcao_js=parent.js_mostralocalidaderural1|j137_sequencial|j137_descricao','Pesquisa',true);
        }else{
            if(document.form1.j137_sequencial.value != ''){
                js_OpenJanelaIframe('','db_iframe_localidaderural','func_localidaderural.php?pesquisa_chave='+document.form1.j137_sequencial.value+'&funcao_js=parent.js_mostralocalidaderural','Pesquisa',false);
            }else{
                document.form1.j137_descricao.value = '';
            }
        }
    }

    function js_mostralocalidaderural(chave, erro) {

        document.form1.j137_descricao.value = chave;
        if (erro == true) {

            document.form1.j137_sequencial.focus();
            document.form1.j137_sequencial.value = '';
        }
    }

    function js_mostralocalidaderural1(chave1, chave2) {

        document.form1.j137_sequencial.value = chave1;
        document.form1.j137_descricao.value = chave2;
        db_iframe_localidaderural.hide();
    }

    function js_pesquisait05_itbisituacao(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_itbisituacao','func_itbisituacao.php?funcao_js=parent.js_mostraitbisituacao1|it07_codigo|it07_descr','Pesquisa',true);
        }else{
            if(document.form1.it05_itbisituacao.value != ''){
                js_OpenJanelaIframe('','db_iframe_itbisituacao','func_itbisituacao.php?pesquisa_chave='+document.form1.it05_itbisituacao.value+'&funcao_js=parent.js_mostraitbisituacao','Pesquisa',false);
            }else{
                document.form1.it07_descr.value = '';
            }
        }
    }

    if (document.form1.it05_itbisituacao.value != "") {
        js_pesquisait05_itbisituacao(false);
    }

    function js_mostraitbisituacao(chave,erro){
        document.form1.it07_descr.value = chave;
        if(erro==true){
            document.form1.it05_itbisituacao.focus();
            document.form1.it05_itbisituacao.value = '';
        }
    }

    function js_mostraitbisituacao1(chave1,chave2){
        document.form1.it05_itbisituacao.value = chave1;
        document.form1.it07_descr.value = chave2;
        db_iframe_itbisituacao.hide();
    }


    function js_pesquisait29_setorloc(mostra){
        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe_setorregimovel','func_setorregimovel.php?funcao_js=parent.js_mostrasetorregimovel1|j69_sequencial|j69_descr','Pesquisa',true);
        }else{
            if(document.form1.it29_setorloc.value != ''){
                js_OpenJanelaIframe('','db_iframe_setorregimovel','func_setorregimovel.php?pesquisa_chave='+document.form1.it29_setorloc.value+'&funcao_js=parent.js_mostrasetorregimovel','Pesquisa',false);
            }else{
                document.form1.j05_descr.value = '';
            }
        }
    }

    function js_mostrasetorregimovel(chave,erro){
        document.form1.j05_descr.value = chave;
        if(erro==true){
            document.form1.it29_setorloc.focus();
            document.form1.it29_setorloc.value = '';
        }
    }

    function js_mostrasetorregimovel1(chave1,chave2){
        document.form1.it29_setorloc.value = chave1;
        document.form1.j05_descr.value = chave2;
        db_iframe_setorregimovel.hide();
    }

    function js_pesquisa(){
        js_OpenJanelaIframe('',
            'db_iframe_itbi',
            'func_itbilib.php?funcao_js=parent.js_preenchepesquisa|it01_guia','Pesquisa',true,0);
    }
    function js_preenchepesquisa(chave){
        if (sessionStorage.getItem("iAlteraguiaLib") == 3) {
            sessionStorage.setItem("sGuiaItbiLib", chave);
            sessionStorage.setItem("sGuiaItbiLibAbaDados", true);
            parent.document.location.reload();
        }

        db_iframe_itbi.hide();
        <?php
        if ($db_opcao == 2 || $db_opcao == 22) {
            echo " location.href = 'itb1_itbilibdadosimovel002.php?chavepesquisa='+chave+'&tipo={$oGet->tipo}';";
        } else if($db_opcao == 33 || $db_opcao == 3) {
            echo " location.href = 'itb1_itbilibdadosimovel003.php?chavepesquisa='+chave+'&tipo={$oGet->tipo}';";
        }
        ?>
    }

    function js_processoSistema(lProcessoSistema) {
        if(lProcessoSistema == 1) {
            document.getElementById('processoExterno1').hide();
            document.getElementById('processoExterno2').hide();
            document.getElementById('processoExterno3').hide();
            document.getElementById('processoSistema').show();

            $('it01_processoexterno').value = "";
            $('it01_tituprocesso').value = "";
            $('it01_dtprocesso').value = "";

            if ($('it01_processo').value != "") {
                js_pesquisaProcesso(false);
            }
        } else {
            document.getElementById('processoExterno1').show();
            document.getElementById('processoExterno2').show();
            document.getElementById('processoExterno3').show();
            document.getElementById('processoSistema').hide();

            $('it01_processo').value = "";
            $('p58_requer').value = "";
        }
    }

    function js_pesquisaProcesso(lMostra)
    {
        var sTitulo = 'Pesquisa Processo';

        if(lMostra) {
            js_OpenJanelaIframe('', 'db_iframe_proc', 'func_protprocesso.php?funcao_js=parent.js_mostraProcesso|p58_numero|z01_nome', sTitulo, lMostra);
        } else {
            if (document.form1.it01_processo.value != "") {
                js_OpenJanelaIframe('', 'db_iframe_proc', 'func_protprocesso.php?chave_p58_numero=' + document.form1.it01_processo.value + '&funcao_js=parent.js_mostraProcessoHidden|p58_numero|z01_nome', sTitulo, lMostra);
            } else {
                document.form1.p58_requer.value = "";
            }
        }
    }

    function js_mostraProcesso(iCodProcesso, sRequerente)
    {
        document.form1.it01_processo.value = iCodProcesso;
        document.form1.p58_requer.value = sRequerente;

        db_iframe_proc.hide();
    }

    function js_mostraProcessoHidden(iCodProcesso, sNome, lErro, p58_codproc)
    {
        if(lErro == true) {
            document.form1.it01_processo.value = "";
            document.form1.p58_requer.value = "";
        } else {
            document.form1.p58_requer.value = sNome;
            processo = p58_codproc
        }
    }

    <?php if (empty($it01_dtprocesso)) : ?>
    js_processoSistema(1);
    <?php else : ?>
    js_processoSistema(0);
    <?php endif; ?>

    function js_calculaPorcentagem(oElemento, oElementoCalculo, lReverse) {

        var oAreaTotal = $('it01_areaterreno');

        if (oAreaTotal.value == '') {

            alert('Campo Área Total deve ser preenchido.');
            return;
        }

        if (oElemento.value != '') {

            var iDecimal = 6;

            oElementoCalculo.value = (lReverse) ? ((+oElemento.value * 100) / +oAreaTotal.value)
                : (+oAreaTotal.value * (+oElemento.value/100));

            if( lReverse ){
                iDecimal = 8;
            }

            if (isNaN(oElementoCalculo.value) || oElementoCalculo.value == Infinity || !isNumeric(oElementoCalculo.value)) {

                oElementoCalculo.value = '';
                oElemento.value        = '';
            } else {
                oElementoCalculo.value = parseFloat( (new Number (oElementoCalculo.value)).toFixed(iDecimal) );
            }

        } else {
            oElementoCalculo.value = '';
        }
    }
</script>
