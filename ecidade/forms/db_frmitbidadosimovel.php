<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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

//MODULO: itbi
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
$clrotulo->label("it01_percentualareatransmitida");
$clrotulo->label("j40_refant");

$tipo = $oGet->tipo;


$sPrefix     = "da ";
$sTerraLabel = "Terra";
$sMedida     = "ha";
$sMedida1    = "";

if ( $oGet->tipo == "urbano") {
  $sPrefix     = "do ";
  $sTerraLabel = "Terreno";
  $sMedida     = "m";
  $sMedida1    = "m²";
}

//Verificamos se o usuário possui permissão para liberação de guia
if(db_permissaomenu(db_getsession("DB_anousu"), db_getsession("DB_modulo"),2571) == "true" || db_permissaomenu(db_getsession("DB_anousu"), db_getsession("DB_modulo"),8098) == "true"){
  $lLiberaGuia = true;
} else {
  $lLiberaGuia = false;
}

$cartorioExtraTipoRepository = CartorioExtraTipoRepository::getInstance();
$aCartorios = $cartorioExtraTipoRepository->setOuterCondition("j168_tiposcartorioextra IN (2,3)")
                                         ->setGroupBy("j167_sequencial, j167_descricao")
                                         ->setOrderBy("j167_sequencial")
                                         ->setCampos("cartorioextra.*")
                                         ->get();
?>
<style>
    #ctnGridTaxas {
        width: 1000px !important;
    }
</style>
<center>
  <form name="form1" method="post" action="">
    <table width="720px;">
      <tr align="center">
        <td>
          <strong>I.T.B.I. <?php echo strtoupper($oGet->tipo); ?></strong>
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
                <td width="105px">
                  <strong>Código da ITBI:</strong>
                </td>
                <td align="left">
                  <?php
                    db_input('it01_guia',       20, $Iit01_guia, true, 'text',   3);
                    db_input('j01_matric',      10, "",          true, 'hidden', 3);
                    db_input('it22_sequencial', 10, "",          true, 'hidden', 3);
                    db_input('listaFormas',     10, "",          true, 'hidden', 3);
                    db_input('tipo',            10, "",          true, 'hidden', 3);
                  ?>
                </td>
              </tr>
              <tr>
                <td nowrap title="<?php echo $Tit01_mail; ?>">
                  <?php echo $Lit01_mail; ?>
                </td>
                <td>
                  <?php
                    db_input('it01_mail',50,$Iit01_mail,true,'text',$db_opcao,"");
                  ?>
                </td>
              </tr>
              <tr>
                <td>
                  <strong>Tipo:</strong>
                </td>
                <td>
                  <select name="codigoTipoTaxa" id="tipoTaxa" onchange="js_buscarTaxaTipo(this.value)">
                    <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                  </select>
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
              <strong>Dados do Imóvel - <?php echo $sTerraLabel; ?></strong>
            </legend>
            <table>
              <tr>
                <td>
                  <fieldset>
                    <legend>
                      <strong>Localização</strong>
                    </legend>
                    <table>
                      <tr>
                        <td colspan="1">
                          <strong>Setor/Bairro:</strong>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it22_setor',20,$Iit22_setor,true,'text', @$db_opcao_plugin);
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="1" nowrap title="<?php echo $Tit22_descrlograd; ?>">
                           <?php echo $Lit22_descrlograd; ?>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it22_descrlograd',114,$Iit22_descrlograd,true,'text',@$db_opcao_plugin,"");
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="1" nowrap title="<?php echo $Tit22_numero; ?>">
                          <?php echo $Lit22_numero; ?>
                        </td>
                        <td colspan="1" width="165px">
                          <?php
                            db_input('it22_numero',20,$Iit22_numero,true,'text',@$db_opcao_plugin,"");
                          ?>
                        </td>
                        <td colspan="1" width="86px" nowrap title="<?php echo $Tit22_compl; ?>">
                          <?php echo $Lit22_compl; ?>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it22_compl',77,$Iit22_compl,true,'text',@$db_opcao_plugin,"");
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="1" nowrap title="<?php echo $Tit22_quadra; ?>">
                          <?php echo $Lit22_quadra; ?>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it22_quadra',20,$Iit22_quadra,true,'text',@$db_opcao_plugin,"");
                          ?>
                        </td>
                        <td colspan="1" nowrap title="<?php echo $Tit22_lote; ?>">
                          <?php echo $Lit22_lote; ?>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it22_lote',20,$Iit22_lote,true,'text',@$db_opcao_plugin,"");
                          ?>
                        </td>
                      </tr>

            <?php if ( $oGet->tipo == "urbano" ) {?>

                      <tr>
                        <td colspan="1" nowrap title="<?php echo $Tit05_itbisituacao; ?>">
                          <?php
                            db_ancora($Lit05_itbisituacao,"js_pesquisait05_itbisituacao(true);",$db_opcao);
                          ?>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it05_itbisituacao',20,$Iit05_itbisituacao,true,'text',$db_opcao," onchange='js_pesquisait05_itbisituacao(false);'");
                            db_input('it07_descr',90,$Iit07_descr,true,'text',3,'');
                          ?>
                        </td>
                      </tr>

            <?php } else { ?>

                      <tr>
                        <td colspan="1">
                          <?php echo $Lit18_coordenadas; ?>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it18_coordenadas',114,$Iit18_coordenadas,true,'text',$db_opcao);
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="1">
                          <?php
                            db_ancora($Lj137_sequencial,"js_pesquisalocalidaderural(true);",$db_opcao);
                          ?>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('j137_sequencial',20,$Ij137_sequencial,true,'text',$db_opcao," onchange='js_pesquisalocalidaderural(false);'");
                            db_input('j137_descricao',90,$Ij137_descricao,true,'text',3,'');
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="1">
                          <strong>Localização:</strong>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it18_localimovel',114,$Iit18_localimovel,true,'text',$db_opcao);
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
                             db_input('flagModificadoImovel',20,"",true,'hidden',$db_opcao,"");
                           ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="4">
                           <?php
                             db_ancora("<strong>Característica de Utilização do Imóvel</strong>","js_caract('util');",$db_opcao);
                             db_input('valorCaracUtil',20,"",true,'hidden',$db_opcao,"");
                             db_input('flagModificadoUtil',20,"",true,'hidden',$db_opcao,"");
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
                        <td colspan="1">
                          <strong>Área Total:</strong>
                        </td>
                        <td colspan="1" width="185px">
                          <?php
                            db_input('it01_areaterreno', 20, $Iit01_areaterreno, true, 'text', @$db_opcao_plugin, " onblur=\"js_limpaCalculo()\"");
                          ?>
                          <strong><?php echo $sMedida1; ?></strong>
                        </td>

                        <td colspan="1" width="170px">
                          <label class="bold" for="it01_percentualareatransmitida" id="lbl_it01_percentualareatransmitida"><?php echo $Lit01_percentualareatransmitida; ?></label>
                        </td>
                        <td colspan="1" width="180px">
                          <?php
                            db_input("it01_percentualareatransmitida", 20, $Iit01_percentualareatransmitida, true, "text", $db_opcao, " onblur=\"js_calculaPorcentagem(this, $('it01_areatrans'), false)\"");
                          ?>
                        </td>

                        <td colspan="1" width="105px">
                          <strong>Área Transmitida: </strong>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it01_areatrans', 14, $Iit01_areatrans, true, 'text', $db_opcao, " onblur=\"js_calculaPorcentagem(this, $('it01_percentualareatransmitida'), true)\"");
                          ?>
                          <strong><?php echo $sMedida1; ?></strong>
                        </td>
                      </tr>

                    <?php if ( $oGet->tipo == "urbano") { ?>

                      <tr>
                        <td colspan="1">
                          <strong>Frente:</strong>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it05_frente',20,$Iit05_frente,true,'text',@$db_opcao_plugin,"");
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
                        </td>
                        <td colspan="1" <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                          <strong>Fundos:</strong>
                        </td>
                        <td colspan="3" <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                          <?php
                            db_input('it05_fundos',20,$Iit05_fundos,true,'text',$db_opcao);
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
                        </td>
                      </tr>

                      <tr <?= (isset($db_opcao_plugin) ? "style='display:none;'" : "") ?>>
                        <td colspan="1" width="75px">
                          <strong>Lado Direito:</strong>
                        </td>
                        <td colspan="1">
                          <?php
                            db_input('it05_direito',20,$Iit05_direito,true,'text',$db_opcao,"");
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
                        </td>

                        <td colspan="1">
                          <strong>Lado Esquerdo:</strong>
                        </td>
                        <td colspan="$db_opcao">
                          <?php
                            db_input('it05_esquerdo',20,$Iit05_esquerdo,true,'text',$db_opcao,"");
                          ?>
                          <strong><?php echo $sMedida?></strong>
                        </td>
                      </tr>

                    <?php } else { ?>

                      <tr>
                        <td nowrap title="<?php echo $Tit18_frente; ?>">
                          <?php echo $Lit18_frente; ?>
                        </td>
                        <td>
                          <?php
                            db_input('it18_frente',20,$Iit18_frente,true,'text',$db_opcao,"");
                            db_input('it18_guia',10,$Iit18_guia,true,'hidden',$db_opcao,"");
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
                        </td>
                        <td nowrap title="<?php echo $Tit18_fundos; ?>" colspan="1">
                          <?php echo $Lit18_fundos; ?>
                        </td>
                        <td colspan="3">
                          <?php
                            db_input('it18_fundos',20,$Iit18_fundos,true,'text',$db_opcao,"");
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
                        </td>
                      </tr>

                      <tr>
                        <td nowrap title="<?php echo $Tit18_prof; ?>">
                          <?php echo $Lit18_prof; ?>
                        </td>
                        <td colspan="4">
                          <?php
                            db_input('it18_prof',20,$Iit18_prof,true,'text',$db_opcao,"");
                          ?>
                          <strong><?php echo $sMedida; ?></strong>
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
                      <td colspan="1" width="57px;">
                        <?php
                          db_ancora("<strong>Setor:</strong>","js_pesquisait29_setorloc(true);",$db_opcao);
                        ?>
                      </td>
                      <td colspan="1">
                        <?php
                          db_input('it29_setorloc',20,$Iit29_setorloc,true,'text',$db_opcao,"onChange='js_pesquisait29_setorloc(false);'");
                          db_input('j05_descr',92,$Ij05_descr,true,'text',3);
                        ?>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="1">
                        <strong>Quadra:</strong>
                      </td>
                      <td colspan="1">
                        <?php
                          db_input('it22_quadrari',20,$Iit22_quadrari,true,'text',$db_opcao,"");
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="1">
                        <strong>Lote:</strong>
                      </td>
                      <td colspan="1">
                        <?php
                          db_input('it22_loteri',20,$Iit22_loteri,true,'text',$db_opcao,"");
                        ?>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="1">
                        <strong>Matrícula:</strong>
                      </td>
                      <td colspan="1">
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
              <strong>Dados da Transação</strong>
            </legend>
            <table width="100%">
              <tr>
                <td title="<?php echo $Tit01_tipotransacao; ?>" width="108px" colspan="1">
                  <?php
                    db_ancora($Lit01_tipotransacao,"js_pesquisait01_tipotransacao(true);",$db_opcao);
                  ?>
                </td>
                <td colspan="5">
                  <?php
                    db_input('it01_tipotransacao',20,$Iit01_tipotransacao,true,'text',$db_opcao," onBlur='js_pesquisait01_tipotransacao(false);'");
                  ?>
                  <?php
                    db_input('it04_descr',87,$Iit04_descr,true,'text',3,'');
                  ?>
                </td>
              </tr>
              <tr>
                <td colspan="1">
                  <strong>Valor  <?=$sPrefix.$sTerraLabel?>:</strong>
                </td>
                <td colspan="1" width="165px">
                  <?php
                    db_input('it01_valorterreno',20,$Iit01_valorterreno,true,'text',$db_opcao,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "", 50, true);
                  ?>
                </td>
                <td colspan="1" width="130px">
                  <strong>Valor das Benfeitorias:</strong>
                </td>
                <td colspan="1" width="165px">
                  <?php
                    db_input('it01_valorconstr',20,$Iit01_valorconstr,true,'text',$db_opcao,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "", 50, true);
                  ?>
                </td>
                <td colspan="1" width="63px">
                  <strong>Valor Total:</strong>
                </td>
                <td colspan="1">
                  <?php
                    db_input('it01_valortransacao',20,$Iit01_valortransacao,true,'text',$db_opcao,"onkeyup='jsFormataMoeda(this, (nValor) => {js_validaValores(this);})'", "", "", "", 50, true);
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
            <legend>
              <strong>Dados de Pagamento</strong>
            </legend>
            <div id="listaFormasPgto" width="700px"></div>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td>
          <fieldset>
            <legend>
              <strong>Taxas</strong>
            </legend>
            <div id="ctnGridTaxas"></div>
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
                    db_textarea('it01_obs', 3, 130, 0, true, 'text', $db_opcao, 'onkeyup=""');
                  ?>
                </td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>

    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  onClick=" return js_validaCampos();">
    <?php if ( $db_opcao != 1 ) { ?>
      <input name="visualizar" type="button" id="visualizar" value="Visualizar Guia" onclick="js_visualizar(<?php echo !empty($it01_guia) ? $it01_guia : ''; ?>);" <?=($db_botao==false?"disabled":"")?>>
      <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
      <input name="liberacao" type="submit" id="liberacao" value="<?=$sBtnLiberacao?>" <?=($db_botao==false?"disabled":"")?> onClick=" return js_validaCampos();">
      <input name="envialiberacao" type="hidden" id="envialiberacao" value="<?=$sBtnEnviaLiberacao?>">

      <?php
        if ($lLiberar  && $lLiberaGuia) {
          echo "<input name=\"liberar\" type=\"button\" id=\"liberar\" value=\"Liberar Guia\"".($db_botao==false?"disabled":"")." onClick=\" return js_liberaguia(".$it01_guia.");\">";
        }

      }
    ?>
  </form>
</center>
<script type="text/javascript">

var oGridTaxas = new DBGrid('gridTaxas');
var aHeaders   = ["Código", "Descrição", "Tipo de Valor", "Calcula Sobre", "Aliquota %", "Faixa", "Valor"];
var aCellWidth = ["10%", "25%", "15%", "15%", "7%", "18%", "10%"];
var aCellAlign = ["center", "left", "center", "center", "center", "center", "center"];

oGridTaxas.nameInstance = 'oGridTaxas';
oGridTaxas.setCellWidth(aCellWidth);
oGridTaxas.setCellAlign(aCellAlign);
oGridTaxas.setHeader(aHeaders);
oGridTaxas.setHeight(100);
oGridTaxas.show($('ctnGridTaxas'));

const spanPersonalizado_gridTaxas = document.getElementById("spanPersonalizado_gridTaxas");
spanPersonalizado_gridTaxas.setAttribute("style", "float: right; color: blue");

// document.getElementById("it01_valorterreno").value = document.getElementById("it01_valorterreno_value").value;
// document.getElementById("it01_valorconstr").value = document.getElementById("it01_valorconstr_value").value;
// document.getElementById("it01_valortransacao").value = document.getElementById("it01_valortransacao_value").value;

(function(){

  if( $F('it01_areatrans') != '' ){
    js_calculaPorcentagem($('it01_areatrans'), $('it01_percentualareatransmitida'), true);
  }
})();

function js_calculaPorcentagem(oElemento, oElementoCalculo, lReverse) {

  var oAreaTotal = $('it01_areaterreno');

  if (oAreaTotal.value == '') {

    alert('Campo Área Total deve ser preenchido.');
    return;
  }

  if (oElemento.value != '') {

    if (parseFloat(oElemento.value) > 100 && !lReverse) {
        oElemento.value = 100;
        alert("O percentual não pode ser maior que 100%.");
    } else {
        if (parseFloat(oElemento.value) > parseFloat(oAreaTotal.value) && lReverse) {
            oElemento.value = oAreaTotal.value;
            alert("A área transmitida não pode ser maior que a área total.");
        }
    }

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

function js_limpaCalculo() {

  $('it01_percentualareatransmitida').value = '';
  $('it01_areatrans').value                 = '';
}

function js_liberaguia(iCodGuia){

  js_divCarregando('Aguarde...','msgBoxA');

  var url      = "itb4_consultaformaPagamentoRPC.php";
  var sQuery   = "iCodGuia="+iCodGuia;
      sQuery  += "&tipoPesquisa=validaLiberacao";
  var oAjax    = new Ajax.Request( url, {
                                          method: 'post',
                                          parameters: sQuery,
                                          onComplete: function (oAjax) {
                                            var oRetorno = JSON.parse(oAjax.responseText);
                                            js_removeObj("msgBoxA");
                                            if (oRetorno.lValidacao) {
                                              parent.location.href='itb1_itbiavalia001.php?chavepesquisa='+iCodGuia;
                                            } else {
                                              alert(oRetorno.sMensagem.urlDecode());
                                            }
                                          }
                                        }
                                 );
}


function js_visualizar(guia) {
  var iGuia  = guia;
  var sParam = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height="+
                (screen.height-100)+",width="+(screen.width-100);
  window.open('reciboitbi.php?itbi='+iGuia,"",sParam);
}

function js_frenteLogradouro(sValor){

  if ( sValor == "s") {
    document.getElementById('frenteLogradouro').style.display = "";
  } else {
    document.getElementById('frenteLogradouro').style.display = "none";
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

    }

    if ( doc.it01_areaterreno.value == "" ) {
      alert("Área Total não informada!");
      return false;
    }

    if ( doc.it01_areaterreno.value <= 0 ) {
      alert("Valor da área do terreno deve ser maior que zero");
      return false;
    }

    if( !isNumeric( doc.it01_percentualareatransmitida.value ) ) {

      alert("Percentual da área transmitida invélido.");
      doc.it01_percentualareatransmitida.value = '';
      doc.it01_percentualareatransmitida.focus();
      return false;
    }

    if ( doc.it01_tipotransacao.value == "" ) {
      alert("Tipo de transação não informado!");
      return false;
    }


    if ( doc.it01_valortransacao.value == "" ) {
      alert("Valor total não informado!");
      return false;
    }

    var aObjFormasPgto = js_getElementbyClass(document.all,'formasPgto');
    var sQuery     = "";

    if (aObjFormasPgto.length == 0) {

      alert('Nenhuma forma de pagamento informada!')
      return false;

    } else {

      var sPrefix = "";
      for ( var iInd=0; iInd < aObjFormasPgto.length; iInd++ ) {
        sQuery += sPrefix+aObjFormasPgto[iInd].id+"X"+aObjFormasPgto[iInd].value.replaceAll(".", "").replace(",", ".");
        sPrefix = "|";
      }

      document.form1.listaFormas.value = sQuery;

    }

    const it01_valorterreno = document.getElementById("it01_valorterreno");
    const it01_valorconstr = document.getElementById("it01_valorconstr");
    const it01_valortransacao = document.getElementById("it01_valortransacao");

    it01_valorterreno.value = it01_valorterreno.value.replaceAll(".", "").replace(",", ".");
    it01_valorconstr.value = it01_valorconstr.value.replaceAll(".", "").replace(",", ".");
    it01_valortransacao.value = it01_valortransacao.value.replaceAll(".", "").replace(",", ".");
  }

function js_controlaValoresFormaPgto(oInputTransacao)
{
    const it01_valortransacao = document.getElementById("it01_valortransacao");
    const nValorTotal = new Number(it01_valortransacao.value.replaceAll(".", "").replace(",", "."));
    const nValorAlterado = new Number(oInputTransacao.value.replaceAll(".", "").replace(",", "."));
    let nValorResto = 0;

    const aFormasPgto = [...document.getElementsByClassName("formasPgto")];

    aFormasPgto.forEach(function (oInput){
        if (oInput.name != "primeiro") {
            var nValorLinha = new Number(oInput.value.replaceAll(".", "").replace(",", "."));
            nValorResto += nValorLinha;
        }
    });

    let nValorAvista = new Number(nValorTotal - nValorResto);

    if (nValorAvista < 0) {
        nValorAvista = nValorTotal - (nValorResto - nValorAlterado);
        alert("A soma dos valores das formas de pagamento não conferem com o valor total do imóvel!");
        oInputTransacao.value = 0;
    }

    document.form1.primeiro.value = nValorAvista.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

function js_validaValores(oCampo, bAtualizaPagamentos = true)
{
    const it01_valorterreno = document.getElementById("it01_valorterreno");
    const it01_valorconstr = document.getElementById("it01_valorconstr");
    const it01_valortransacao = document.getElementById("it01_valortransacao");

    const nValorTerreno = (it01_valorterreno.value != NaN ? new Number(it01_valorterreno.value.replaceAll(".", "").replace(",", ".")) : 0);
    const nValorBenfeitoria = (it01_valorconstr.value != NaN ? new Number(it01_valorconstr.value.replaceAll(".", "").replace(",", ".")) : 0);
    const nValorTotal = (it01_valortransacao.value != NaN ? new Number(it01_valortransacao.value.replaceAll(".", "").replace(",", ".")) : 0);

    if (nValorTerreno != 0 || nValorBenfeitoria != 0) {
        it01_valortransacao.disabled = true;
        it01_valortransacao.value = new Number(nValorTerreno + nValorBenfeitoria).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    } else {
        if (nValorTerreno == 0 && nValorBenfeitoria == 0 && nValorTotal != 0 && oCampo.name == "it01_valortransacao") {
            it01_valorterreno.disabled = true;
            it01_valorconstr.disabled = true;
        } else {
            if (nValorTerreno == 0 && nValorBenfeitoria == 0 && oCampo.name != "it01_valortransacao") {
                it01_valortransacao.value = 0;
                it01_valortransacao.disabled = false;
            } else {
                it01_valorterreno.disabled = false;
                it01_valorconstr.disabled = false;
                it01_valortransacao.disabled = false;
            }
        }
    }

    js_calculaTaxas();

    if (document.form1.primeiro != undefined && bAtualizaPagamentos) {
        js_limpaValorFormaPgto();

        document.form1.primeiro.value = new Number(it01_valortransacao.value.replaceAll(".", "").replace(",", ".")).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    }
}

function js_limpaValorFormaPgto(){

  var aObjFormasPgto = js_getElementbyClass(document.all,'formasPgto');
  for ( var iInd=0; iInd < aObjFormasPgto.length; iInd++ ) {
     aObjFormasPgto[iInd].value = 0;
  }
}

function js_criaGrid() {

  gridFormasPgto              = new DBGrid("listaFormasPgto");
  gridFormasPgto.nameInstance = "gridFormasPgto";

  gridFormasPgto.setCellAlign( new Array("left","center","right") );
  gridFormasPgto.setHeader   ( new Array("Descrição","Alíquota %","Valor"));
  gridFormasPgto.setCellWidth( new Array("60%","20%","20%"));
  gridFormasPgto.setHeight(80);
  gridFormasPgto.show(document.getElementById('listaFormasPgto'));

  closeOnSave    = false;
}

function js_consultaFormaPgto(iCodTransacao){

  js_divCarregando('Aguarde...','msgBoxB');

  var url     = "itb4_consultaformaPagamentoRPC.php";
  var sQuery  = "codtransacao="+iCodTransacao;
      sQuery += "&tipoPesquisa=formasDisponiveis";
      sQuery += "&tipoITBI="+document.form1.tipo.value;
  var oAjax   = new Ajax.Request( url, {
                                        method: 'post',
                                        parameters: sQuery,
                                        onComplete: js_retornoFormaPgto
                                      }
                                );
}

function js_consultaFormaPgtoCadastrada(iGuia){

  js_divCarregando('Aguarde...','msgBoxC');

  var url          = "itb4_consultaformaPagamentoRPC.php";
  var sQuery     = "codguia="+iGuia;
      sQuery    += "&tipoPesquisa=formasCadastradas";
  var oAjax        = new Ajax.Request( url, {
                                              method: 'post',
                                              parameters: sQuery,
                                              onComplete: js_retornoFormaPgtoCadastrada
                                            }
                                      );
}

function js_retornoFormaPgto(oAjax){

  js_removeObj("msgBoxB");
  var objListaForma = JSON.parse(oAjax.responseText);
  var nValor    = 0;

  gridFormasPgto.clearAll(true);

  if ( objListaForma.iStatus && objListaForma.iStatus == 2){
    alert(objListaForma.sMensagem.urlDecode());
    return false ;
  }

  for ( var iInd = 0; iInd < objListaForma.length; iInd++ ) {

    with (objListaForma[iInd]) {

      if ( iInd == 0 ) {
        nValor         = document.form1.it01_valortransacao.value;
        var sDisabled  = "disabled";
        var sNomeCampo = "name='primeiro'";
      } else {
        nValor         = 0;
        var sDisabled  = "";
        var sNomeCampo = "";
      }

      var sInputValor  = "<input type='text' id='"+it25_sequencial.urlDecode()+"' class='formasPgto' value='"+nValor+"'";
          sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' "+sDisabled+" "+sNomeCampo+"";
          sInputValor += "onkeyup='jsFormataMoeda(this, (nValor) => {js_controlaValoresFormaPgto(this);})'>";

      var aLinha  = new Array();
          aLinha[0] = it27_descricao.urlDecode();
        aLinha[1] = js_formatar(it27_aliquota.urlDecode(),'f');
        aLinha[2] = sInputValor;

      gridFormasPgto.addRow(aLinha);
      gridFormasPgto.renderRows();
    }
  }

  document.form1.it01_valortransacao.focus();
}

function js_retornoFormaPgtoCadastrada(oAjax){

  js_removeObj("msgBoxC");
  var objListaForma = JSON.parse(oAjax.responseText);
  var nValor    = 0;

  gridFormasPgto.clearAll(true);

  if ( objListaForma.iStatus && objListaForma.iStatus == 2){

    alert(objListaForma.sMensagem.urlDecode());
    return false ;
  }

  for ( var iInd = 0; iInd < objListaForma.length; iInd++ ) {

    with (objListaForma[iInd]) {

      if ( iInd == 0 ) {
        var sDisabled  = "disabled";
        var sNomeCampo = "name='primeiro'";
      } else {
        var sDisabled  = "";
        var sNomeCampo = "";
      }

      const nValor = new Number(it26_valor.urlDecode()).toLocaleString('pt-BR', {minimumFractionDigits: 2});

      var sInputValor  = "<input type='text' id='"+it25_sequencial.urlDecode()+"' class='formasPgto' value='"+nValor+"'";
        sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' "+sDisabled+" "+sNomeCampo+"";
        sInputValor += "onkeyup='jsFormataMoeda(this, (nValor) => {js_controlaValoresFormaPgto(this);})'>";

      var aLinha  = new Array();
          aLinha[0] = it27_descricao.urlDecode();
        aLinha[1] = js_formatar(it27_aliquota.urlDecode(),'f');
        aLinha[2] = sInputValor;

      gridFormasPgto.addRow(aLinha);
      gridFormasPgto.renderRows();
    }
  }

  js_validaValores(document.getElementById("it01_valortransacao"), false);
}

function js_caract(sTipo){
  var sQuery  = "?guia=" + document.form1.it01_guia.value;
  sQuery += "&tipo=" + sTipo;

  if (sTipo == 'imovel') {
    sQuery += "&caracteristicas=" + $('valorCaracImovel').value + "&flagModificadoImovel=" + $('flagModificadoImovel').value;
  } else {
    sQuery += "&caracteristicas=" + $('valorCaracUtil').value + "&flagModificadoUtil=" + $('flagModificadoUtil').value;
  }

  js_OpenJanelaIframe('', 'db_iframe_caract', 'itb1_itbiruralcaract002.php' + sQuery, 'Pesquisa', true, 0);
}

function js_fecha(){
  db_iframe_caract.hide();
}

function js_mostraProcesso(iCodProcesso, sRequerente) {

document.form1.it01_processo.value = iCodProcesso;
document.form1.p58_requer.value = sRequerente;
// document.form1.numeroProcesso.value = numeroProcesso;

db_iframe_matric.hide();

}

function js_mostraProcessoHidden(iCodProcesso, sNome, lErro, p58_codproc) {

if(lErro == true) {
  document.form1.it01_processo.value = "";
  document.form1.p58_requer.value = "";
} else {
  document.form1.p58_requer.value = sNome;
  processo = p58_codproc
}
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

function js_pesquisait01_tipotransacao(mostra){

  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_itbitransacao','func_itbitransacao.php?validadata=true&funcao_js=parent.js_mostraitbitransacao1|it04_codigo|it04_descr','Pesquisa',true);
  }else{

     if(document.form1.it01_tipotransacao.value != ''){
        js_OpenJanelaIframe('','db_iframe_itbitransacao','func_itbitransacao.php?validadata=true&pesquisa_chave='+document.form1.it01_tipotransacao.value+'&funcao_js=parent.js_mostraitbitransacao','Pesquisa',false);
     }else{
       document.form1.it04_descr.value = '';
     }
  }
}

function js_mostraitbitransacao(chave,erro){

  document.form1.it04_descr.value = chave;

  if(erro==true){

    document.form1.it01_tipotransacao.focus();
    document.form1.it01_tipotransacao.value = '';
  } else {
    js_consultaFormaPgto(document.form1.it01_tipotransacao.value);
  }
}

function js_mostraitbitransacao1(chave1,chave2){

  document.form1.it01_tipotransacao.value = chave1;
  document.form1.it04_descr.value         = chave2;
  db_iframe_itbitransacao.hide();
  js_consultaFormaPgto(chave1);
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

const it05_itbisituacao = document.getElementById("it05_itbisituacao");

if(it05_itbisituacao != undefined && it05_itbisituacao.value != ""){
    js_pesquisait05_itbisituacao(false);
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

function js_mostraitbisituacao(chave,erro){

  document.form1.it07_descr.value = chave;
  if(erro==true){
    document.form1.it05_itbisituacao.focus();
    document.form1.it05_itbisituacao.value = '';
  }
}

function js_mostraitbisituacao1(chave1,chave2){

  document.form1.it05_itbisituacao.value = chave1;
  document.form1.it07_descr.value        = chave2;
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

js_criaGrid();

<?php if (isset($bGuiaRetificativa) && $bGuiaRetificativa) : ?>
function js_pesquisa() {
    js_OpenJanelaIframe('',
        'db_iframe_itbi',
        'func_itbilib.php?funcao_js=parent.js_preenchepesquisa|it01_guia', 'Pesquisa', true, 0);
}

function js_preenchepesquisa(chave) {
    var sTipo = '<?=$oGet->tipo?>';
    db_iframe_itbi.hide();
    location.href = 'itb1_itbiretificacaodadosimovel001.php?chavepesquisa=' + chave + '&tipo=' + sTipo;
}
<?php else : ?>
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_itbi','func_itbinaocancelado.php?liberada='+false+'&funcao_js=parent.js_preenchepesquisa|it01_guia','Pesquisa',true);
}

function js_preenchepesquisa(chave){

  db_iframe_itbi.hide();
  <?php
    if($db_opcao == 2 || $db_opcao == 22){
      echo " location.href = 'itb1_itbidadosimovel002.php?chavepesquisa='+chave+'&tipo={$oGet->tipo}';";
    }elseif($db_opcao == 33 || $db_opcao == 3){
      echo " location.href = 'itb1_itbidadosimovel003.php?chavepesquisa='+chave+'&tipo={$oGet->tipo}';";
    }
  ?>
}
<?php endif; ?>
<?php
  if ( isset($oGet->chavepesquisa) ) {

//     echo "js_validaValores(document.form1.it01_valortransacao);";
    echo "js_consultaFormaPgtoCadastrada(".$oGet->chavepesquisa.");";
  }
?>

js_buscaTipos();

function js_buscaTipos()
{
  const tipo = document.getElementById("tipo").value;
  const tipoTaxa = document.getElementById("tipoTaxa");

  var oParam = new Object();
  oParam.executa = "listarTipos";
  oParam.tipo = tipo;

  new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
      if (oRetorno.erro) {
        alert(oRetorno.mensagem);
        return;
      }

      const aTipos = oRetorno.aTipos;

      aTipos.forEach(function (oTipo){
        const option = document.createElement("option");
        option.setAttribute("value", oTipo.it36_sequencial);
        option.innerHTML = oTipo.it36_descricao;
        if (aTipos.length == 1 || oTipo.it36_sequencial == <?= ((isset($oItbitaxasitbi->it38_taxasitbi) AND !empty($oItbitaxasitbi->it38_taxasitbi)) ? $oItbitaxasitbi->it38_taxasitbi : 0) ?>) {
            option.setAttribute("selected", "selected");
            js_buscarTaxaTipo(oTipo.it36_sequencial);
        }

        tipoTaxa.appendChild(option);
      });
  }).execute();
}

var aTaxas = [];

function js_buscarTaxaTipo(codigo)
{

    if (codigo != "" && codigo != undefined) {
        const tipo = document.getElementById("tipo").value;
        const matricula = document.getElementById("j01_matric").value;

        if (tipo == "urbano" && !matricula) {
            return;
        }

        var oParam = new Object();
        oParam.executa = "buscarTaxasTipo";
        oParam.it36_sequencial = codigo;
        oParam.tipo = tipo;
        oParam.matricula = matricula;

        new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
            if (oRetorno.erro) {
              alert(oRetorno.mensagem);
              return;
            }

            aTaxas = oRetorno.aTaxas;

            js_verificaTaxasFaixa();
            js_montaGridTaxas((document.getElementById("it01_guia").value == "" ? true : false));
        }).execute();
    }
}

function js_verificaTaxasFaixa()
{
    const it01_valorterreno = document.getElementById("it01_valorterreno").value.replaceAll(".", "").replace(",", ".");
    const it01_valorconstr = document.getElementById("it01_valorconstr").value.replaceAll(".", "").replace(",", ".");
    const it01_valortransacao = document.getElementById("it01_valortransacao").value.replaceAll(".", "").replace(",", ".");

    aTaxas.forEach(function (oTaxa, key){
        if (oTaxa.ar44_tipo == 3) {
            if (oTaxa.it37_calculasobre == 1) {
                if (!(parseFloat(it01_valorterreno) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valorterreno) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            } else if (oTaxa.it37_calculasobre == 2) {
                if (!(parseFloat(it01_valorconstr) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valorconstr) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            } else if (oTaxa.it37_calculasobre == 3) {
                if (!(parseFloat(it01_valortransacao) >= oTaxa.it37_iniciofaixa && parseFloat(it01_valortransacao) <= oTaxa.it37_fimfaixa)) {
                    aTaxas[key].bMostra = false;
                } else {
                    aTaxas[key].bMostra = true;
                }
            }
        }
    });
}

function js_montaGridTaxas(bRequest = false)
{
    oGridTaxas.clearAll(true);

    for (const oTaxa of aTaxas) {
        if (!oTaxa.bMostra) {
            continue;
        }

        var aLinha = [];
        aLinha.push(oTaxa.ar44_sequencial);
        aLinha.push(oTaxa.ar44_descricao);

        if (oTaxa.ar44_tipo == 2 || oTaxa.ar44_tipo == 3) {
            if (oTaxa.ar44_tipo == 3) {
                aLinha.push("Fixo Sobre Faixa");
            } else {
                aLinha.push("Percentual");
            }

            if (oTaxa.it37_calculasobre == 1) {
                aLinha.push("Valor do Terreno");
            } else if (oTaxa.it37_calculasobre == 2) {
                aLinha.push("Valor da Construção");
            } else if (oTaxa.it37_calculasobre == 3) {
                aLinha.push("Ambos");
            }

            aLinha.push(oTaxa.aliquota.toLocaleString('pt-BR', {maximumFractionDigits: 2}));

            if (oTaxa.ar44_tipo == 3) {
                aLinha.push(`${oTaxa.it37_iniciofaixa} à ${oTaxa.it37_fimfaixa}`);
            } else {
                aLinha.push("");
            }
        } else {
            aLinha.push("Fixo");
            aLinha.push("");
            aLinha.push("");
            aLinha.push("");
        }

        const span = document.createElement("span");
        span.setAttribute("id", "id_"+oTaxa.ar44_sequencial);
        span.setAttribute("isTaxa", "true");
        span.innerHTML = oTaxa.i02_valor.toLocaleString('pt-BR', {maximumFractionDigits: 2});

        aLinha.push(span.outerHTML);

        oGridTaxas.addRow(aLinha);
    }

    oGridTaxas.renderRows();

    if (bRequest) {
        const it01_valorterreno = document.getElementById("it01_valorterreno");
        const it01_valorconstr = document.getElementById("it01_valorconstr");

        if (it01_valorterreno.value == 0 && it01_valorconstr.value == 0) {
            js_validaValores(document.getElementById("it01_valortransacao"));
        } else {
            js_validaValores(it01_valorterreno);
            js_validaValores(it01_valorconstr);
        }
    }
}

function js_calculaTaxas()
{
    js_verificaTaxasFaixa();
    js_montaGridTaxas();

    js_atualizaValorTotal();
}

js_atualizaValorTotal();

function js_atualizaValorTotal()
{
    const aSpans = document.querySelectorAll("[isTaxa='true']");
    var valor = 0;

    aSpans.forEach(function (aSpan){
        const valorTaxa = aSpan.innerText.replaceAll(".", "").replace(",", ".");
        valor = valor + parseFloat(valorTaxa);
    });

    spanPersonalizado_gridTaxas.innerHTML = "Valor Total: "+valor.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL', minimumFractionDigits: 2});
}

function js_processoSistema(lProcessoSistema) {
    if(lProcessoSistema == 1) {
        document.getElementById('processoExterno1').style.display = 'none';
        document.getElementById('processoExterno2').style.display = 'none';
        document.getElementById('processoExterno3').style.display = 'none';
        document.getElementById('processoSistema').style.display = '';

        $('it01_processoexterno').value = "";
        $('it01_tituprocesso').value = "";
        $('it01_dtprocesso').value = "";

        if ($('it01_processo').value != "") {
            js_pesquisaProcesso(false);
        }
    } else {
        document.getElementById('processoExterno1').style.display = '';
        document.getElementById('processoExterno2').style.display = '';
        document.getElementById('processoExterno3').style.display = '';
        document.getElementById('processoSistema').style.display = 'none';

        $('it01_processo').value = "";
        $('p58_requer').value = "";
    }
}

function js_pesquisaProcesso(lMostra) {

var sTitulo = 'Pesquisa Processo';

if(lMostra) {

  js_OpenJanelaIframe(
    '',
    'db_iframe_matric',
    'func_protprocesso.php?funcao_js=parent.js_mostraProcesso|p58_numero|z01_nome',
    sTitulo,
    lMostra
  );
} else {

  js_OpenJanelaIframe(
    '',
    'db_iframe_matric',
    'func_protprocesso.php?chave_p58_numero=' + document.form1.it01_processo.value + '&funcao_js=parent.js_mostraProcessoHidden|p58_numero|z01_nome',
    sTitulo,
    lMostra
  );
}
}

<?php if (empty($it01_dtprocesso)) : ?>
    js_processoSistema(1);
<?php else : ?>
    js_processoSistema(0);
<?php endif; ?>

window.addEventListener("load", function (element){
    const it01_guia = document.getElementById("it01_guia").value;

    js_validaValores(document.getElementById("it01_valortransacao"), (it01_guia == ""));
});
</script>
