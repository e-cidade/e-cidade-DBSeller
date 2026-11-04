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

use App\Domain\Tributario\Arrecadacao\Repositories\OperacoestefRepository;

//MODULO: caixa
$clarretipo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("k03_tipo");


$clrotulo->label("k196_tipo");
$clrotulo->label("k196_aceitatef");
$clrotulo->label("k196_maximoparcelas");
$clrotulo->label("k196_valorminimoparcelafisica");
$clrotulo->label("k196_valorminimoparcelajuridica");

$k00_dtvencimento = isset($k00_dtvencimento) ? $k00_dtvencimento : '';
$k00_taxaespecifica = isset($k00_taxaespecifica) ? $k00_taxaespecifica : '';
$displayTaxaEspecifica = $db_opcao == 33 ? "style='display: none;'" : '';

$operacoestefRepository = new OperacoestefRepository();
$aOperacoes = $operacoestefRepository->get();
?>
<style>
    #k196_aceitatef {
        margin-left: 0px;
    }
</style>
<form name="form1" method="post" action="">
    <script language="JavaScript" type="text/javascript" src="ext/javascript/prototype.maskedinput.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css" rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css" rel="stylesheet"/>
    <!-- <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"> -->
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

    <div id="container">
        <div id="parametros">
            <input type="hidden" id="hiddendtvencimento" value="<?php echo $k00_dtvencimento; ?>"  />
            <fieldset class="fieldsetPrincipal">
                <legend>
                    Cadastro Tipo de Débito
                </legend>
                <table width="100%">
                    <tr>
                        <td title="<?= @$Tk00_tipo ?>">
                            <?= @$Lk00_tipo ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_tipo',
                                    10,
                                    $Ik00_tipo,
                                    true,
                                    'text',
                                    3,
                                    ""
                                );
                            ?>
                        </td>
                        <td title="<?= @$Tk00_marcado ?>">
                            <?= @$Lk00_marcado ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    't' => 'Marcado', 
                                    'f' => 'Desmarcado'
                                );

                                db_select(
                                    'k00_marcado',
                                    $x,
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_codbco ?>">
                            <?= @$Lk00_codbco ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_codbco',
                                    10,
                                    $Ik00_codbco,
                                    true,
                                    'text',
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                        <td title="<?= @$Tk00_codage ?>">
                            <?= @$Lk00_codage ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_codage',
                                    10,
                                    $Ik00_codage,
                                    true,
                                    'text',
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_emrec ?>">
                            <?= @$Lk00_emrec ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO", 
                                    "t" => "SIM"
                                );

                                db_select(
                                    'k00_emrec',
                                    $x,
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                        <td title="<?= @$Tk00_agnum ?>">
                            <?= @$Lk00_agnum ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO", 
                                    "t" => "SIM"
                                );

                                db_select(
                                    'k00_agnum',
                                    $x,
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_agpar ?>">
                            <?= @$Lk00_agpar ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO",
                                    "t" => "SIM"
                                );
                                
                                db_select(
                                    'k00_agpar', 
                                    $x, 
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                        <td title="<?= @$Tk00_txban ?>">
                            <?= @$Lk00_txban ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_txban', 
                                    10, 
                                    $Ik00_txban, 
                                    true, 
                                    'text', 
                                    $db_opcao, 
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_rectx ?>">
                            <?= @$Lk00_rectx ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_rectx', 
                                    10, 
                                    $Ik00_rectx, 
                                    true, 
                                    'text', 
                                    $db_opcao, 
                                    ""
                                );
                            ?>
                        </td>
                        <td title="<?= @$Tk00_vlrmin ?>">
                            <?= @$Lk00_vlrmin ?>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_vlrmin', 
                                    10, 
                                    $Ik00_vlrmin, 
                                    true, 
                                    'text', 
                                    $db_opcao, 
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_impval ?>">
                            <?= @$Lk00_impval ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO", 
                                    "t" => "SIM"
                                );
                                
                                db_select(
                                    'k00_impval',
                                    $x,
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                        <input name="codmodelo" type="hidden" value="0"></input>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_liberacarnesis ?>">
                            <?= @$Lk00_liberacarnesis ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO", 
                                    "t" => "SIM"
                                );

                                db_select(
                                    'k00_liberacarnesis',
                                    $x,
                                    true,
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                        <!-- <input name="codmodelo" type="hidden" value="0"></input> -->
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_liberacarnepref ?>">
                            <?= @$Lk00_liberacarnepref ?>
                        </td>
                        <td>
                            <?php
                                $x = array(
                                    "f" => "NAO", 
                                    "t" => "SIM"
                                );

                                db_select(
                                    'k00_liberacarnepref', 
                                    $x, 
                                    true, 
                                    $db_opcao, 
                                    ""
                                );
                            ?>
                        </td>
                        <!-- <input name="codmodelo" type="hidden" value="0"></input> -->
                    </tr>
                    <tr>
                        <td>
                            <b>Libera emissão de recibo DBpref</b>
                        </td>
                        <td colspan="3">
                            <?php
                                $lib = array(
                                    "1" => "Emissão liberada",
                                    "2" => "Mostrar débito e não emitir recibo",
                                    "3" => "Não mostrar débito e não emitir recibo"
                                );

                                db_select(
                                    'k00_recibodbpref', 
                                    $lib, 
                                    true, 
                                    $db_opcao, 
                                    "style='width: 400px'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Tipo de agrupamento </b>
                        </td>
                        <td colspan="3">
                            <?php
                                $arrayTipoAgrup = array(
                                    "1" => "Nenhum", 
                                    "2" => "Parcial",
                                    "3" => "Total"
                                );

                                db_select(
                                    'k00_tipoagrup', 
                                    $arrayTipoAgrup, 
                                    true, 
                                    $db_opcao, 
                                    "style='width: 400px'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= @$Lk00_formemissao ?>
                        </td>
                        <td colspan="3">
                            <?php
                                $aFormEmissao = array(
                                    "2" => "Com valores atualizados",
                                    "1" => "Com valores originais",
                                    "3" => "Ambos"
                                );

                                db_select(
                                    'k00_formemissao',
                                    $aFormEmissao,
                                    true,
                                    $db_opcao,
                                    "style='width: 400px'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk03_tipo ?>">
                            <?php
                                db_ancora(
                                    @$Lk03_tipo, 
                                    "js_pesquisak03_tipo(true);", 
                                    $db_opcao
                                );
                            ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k03_tipo',
                                    10,
                                    $Ik03_tipo,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onchange='js_pesquisak03_tipo(false);'"
                                );

                                db_input(
                                    'tipodescr',
                                    40,
                                    $Ik03_tipo,
                                    true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_receitacredito ?>">
                            <?php
                                db_ancora(
                                    @$Lk00_receitacredito, 
                                    "js_pesquisak00_receitacredito(true);", 
                                    $db_opcao
                                );
                            ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_receitacredito', 
                                    10, 
                                    $Ik00_receitacredito, 
                                    true, 
                                    'text', 
                                    $db_opcao, 
                                    "onchange='js_pesquisak00_receitacredito(false);'"
                                );
                                
                                db_input(
                                    'receitacreditodescr', 
                                    40, 
                                    $Ik00_receitacredito, 
                                    true, 
                                    'text', 
                                    3, 
                                    ''
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_descr ?>">
                            <?= @$Lk00_descr ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_descr',
                                    40,
                                    $Ik00_descr,
                                    true,
                                    'text',
                                    $db_opcao,
                                    ""
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_tercdigcarneunica ?>">
                            <?= @$Lk00_tercdigcarneunica ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_tercdigcarneunica',
                                    10,
                                    $Ik00_tercdigcarneunica,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onkeyup='js_controladig3(this.name);'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_tercdigcarnenormal ?>">
                            <?= @$Lk00_tercdigcarnenormal ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_tercdigcarnenormal',
                                    10,
                                    $Ik00_tercdigcarnenormal,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onkeyup='js_controladig3(this.name);'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_tercdigrecunica ?>">
                            <?= @$Lk00_tercdigrecunica ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_tercdigrecunica',
                                    10,
                                    $Ik00_tercdigrecunica,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onkeyup='js_controladig3(this.name);'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_tercdigrecnormal ?>">
                            <?= @$Lk00_tercdigrecnormal ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_tercdigrecnormal',
                                    10,
                                    $Ik00_tercdigrecnormal,
                                    true,
                                    'text',
                                    $db_opcao,
                                    "onkeyup='js_controladig3(this.name);'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_exercicioscarne ?>">
                            <?= @$Lk00_exercicioscarne ?>
                        </td>
                        <td colspan="3">
                            <?php
                                db_input(
                                    'k00_exercicioscarne',
                                    10,
                                    $Ik00_exercicioscarne,
                                    true,
                                    'text',
                                    $db_opcao
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Bloqueio Vencimento Guia Online:</td>
                        <td>
                            <select 
                                name="selectTipoVenc" 
                                id="selectTipoVenc" 
                                onchange="selecionaTipoVencimento(this)"
                            >
                                <option value="0"> Nenhum</option>
                                <option value="1"> Exercício</option>
                                <option value="2"> Dia</option>
                                <option value="3"> Data</option>
                                <option value="4"> Hora</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="lDataVencimento" style=" display: none">
                        <td class="bold"  id="lblDatavencimento"></td>
                        <td id="tdVencimento"></td>
                    </tr>
                    <tr id="hrIntervalo" style=" display: none"> 
                        <td class="bold" >Intervalo: </td>
                        <td>
                            <?php
                                db_input(
                                    'k00_horainicial',
                                    5,
                                    $Ik00_horainicial,
                                    true,
                                    'text',
                                    1,
                                    "onblur='js_validaHora24Horas(this, event);'"
                                );
                            ?>
                            até
                            <?php 
                                db_input(
                                    'k00_horafinal',
                                    5,
                                    $Ik00_horafinal,
                                    true,
                                    'text',
                                    1,
                                    "onblur='js_validaHora24Horas(this, event);'"
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="<?= @$Tk00_bloqnutil ?>">
                            <?= @$Lk00_bloqnutil ?>
                        </td>
                        <td>
                            <?php
                            $x = array("f" => "NAO", "t" => "SIM");
                            db_select('k00_bloqnutil', $x, true, $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Permite cobrança de taxa Administrativa:
                        </td>
                        <td>
                            <?php
                            $x = array("f" => "NAO", "t" => "SIM");
                            db_select('k00_taxaadm', $x, true, $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr <?=$displayTaxaEspecifica?>>
                        <td>
                            <a href="#" id="ancoraTaxaEspecifica">
                                <?=$Lk00_taxaespecifica;?>
                            </a>
                        </td>
                        <td>
                            <input id="k00_taxaespecifica" name="k00_taxaespecifica" type="text" value="<?=$k00_taxaespecifica?>" class="field-size2" />
                            <input id="descricaoTaxaEspecifica" type="text" value="" class="field-size7 readonly" disabled />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>

        <div id="mensagens">
            <fieldset class="fieldsetPrincipal">
                <legend>Mensagens</legend>

                <fieldset class="fieldsetSecundario">
                    <legend>Mensagens Cota única</legend>
                    <table width="100%">
                        <tr>
                            <td title="<?= @$Tk00_msguni ?>">
                                <strong>Guia Caixa/Prefeitura: </strong>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msguni',
                                        0,
                                        50,
                                        $Ik00_msguni,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"u\");'"
                                    );
                                ?>
                                <div id='u'></div>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk00_msguni2 ?>">
                                <strong>Guia Contribuinte: </strong>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msguni2',
                                        0,
                                        50,
                                        $Ik00_msguni2,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"u2\");'"
                                    );
                                ?>
                                <div id='u2'></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class="fieldsetSecundario">
                    <legend>
                        Mensagens parcelas
                    </legend>
                    <table width="100%">
                        <tr>
                            <td title="<?= @$Tk00_msgparc ?>">
                                <strong>Guia Contribuinte: </strong>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msgparc',
                                        0,
                                        50,
                                        $Ik00_msgparc,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"p\");'"
                                    );
                                ?>
                                <div id='p'></div>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk00_msgparc2 ?>">
                                <strong>Guia Caixa/Prefeitura: </strong>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msgparc2',
                                        0,
                                        50,
                                        $Ik00_msgparc2,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"p2\");'"
                                    );
                                ?>
                                <div id='p2'></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class="fieldsetSecundario">
                    <legend>Mensagens parcelas vencidas</legend>
                    <table width="100%">
                        <tr>
                            <td title="<?= @$Tk00_msgparcvenc ?>">
                                <b>Guia Contribuinte: </b>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msgparcvenc', 
                                        0,
                                        50,
                                        $Ik00_msgparcvenc,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"pv\");'"
                                    );
                                ?>
                                <div id='pv'></div>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tk00_msgparcvenc2 ?>">
                                <b>Guia Caixa/Prefeitura: </b>
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msgparcvenc',
                                        0,
                                        50,
                                        $Ik00_msgparcvenc2,
                                        true,
                                        'text',
                                        $db_opcao,
                                        "onkeyup='js_controlatextarea(this.name,150,\"pv2\");'",
                                        'k00_msgparcvenc2'
                                    );
                                ?>
                                <div id='pv2'></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class="fieldsetSecundario">
                    <legend>Mensagem Recibo</legend>
                    <table width=100%>
                        <tr>
                            <td title="<?= @$Tk00_msgrecibo ?>">
                                <?= @$Lk00_msgrecibo ?>&nbsp;
                            </td>
                            <td>
                                <?php
                                    db_textarea(
                                        'k00_msgrecibo', 
                                        0, 
                                        50, 
                                        $Ik00_msgrecibo, 
                                        true, 
                                        'text', 
                                        $db_opcao, 
                                        "onkeyup='js_controlatextarea(this.name,150,\"r\");'"
                                    );
                                ?>
                                <div id='r'></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </fieldset>
        </div>
        <div id="tef">
          <?php db_input('k196_sequencial', 10, $Ik196_sequencial, true, 'hidden', $db_opcao) ?>
          <fieldset class="fieldsetPrincipal" style="width:451px;">
              <legend>Configuração TEF</legend>
              <table>
                  <tr>
                      <td title="<?= @$Tk196_aceitatef ?>" style="width: 161px;">
                          <?= @$Lk196_aceitatef ?>
                      </td>
                      <td>
                          <?php
                              db_input('k196_aceitatef', 10, $Ik196_aceitatef, true, 'checkbox', $db_opcao)
                          ?>
                      </td>
                  </tr>
                  <tr>
                      <td title="<?= @$Tk196_maximoparcelas ?>">
                          <?= @$Lk196_maximoparcelas ?>
                      </td>
                      <td>
                          <?php
                              db_input('k196_maximoparcelas', 10, $Ik196_maximoparcelas, true, 'text', $db_opcao)
                          ?>
                      </td>
                  </tr>
                  <tr>
                      <td title="<?= @$Tk196_valorminimoparcelafisica ?>">
                          <?= @$Lk196_valorminimoparcelafisica ?>
                      </td>
                      <td>
                          <?php
                              db_input('k196_valorminimoparcelafisica', 10, $Ik196_valorminimoparcelafisica, true, 'text', $db_opcao)
                          ?>
                      </td>
                  </tr>
                  <tr>
                      <td title="<?= @$Tk196_valorminimoparcelajuridica ?>">
                          <?= @$Lk196_valorminimoparcelajuridica ?>
                      </td>
                      <td>
                          <?php
                              db_input('k196_valorminimoparcelajuridica', 10, $Ik196_valorminimoparcelajuridica, true, 'text', $db_opcao)
                          ?>
                      </td>
                  </tr>
                  <tr>
                      <td title="Operações que estarão disponíveis em pagamentos TEF">
                          <strong>Operações:</strong>
                      </td>
                      <td>
                        <?php foreach ($aOperacoes as $item) : ?>
                            <input type="checkbox"
                                   name="operacoes[]"
                                   id="<?= $item->k195_sequencial ?>"
                                   value="<?= $item->k195_sequencial ?>"
                                   style="margin-left: 0px"
                                   <?= (in_array($item->k195_sequencial, $aOperacoesSalvas) ? "checked" : "") ?>
                            >
                            <label for="<?= $item->k195_sequencial ?>" style="position: relative; top: -5px">
                                <?= $item->k195_descricao ?>
                            </label>
                            <br>
                        <?php endforeach; ?>
                      </td>
                  </tr>
              </table>
            </fieldset>
        </div>

        <?php
            if ($db_opcao == 2)
            {
                include(modification("forms/db_frmarretipo002.php"));
            }
        ?>
    </div>
    <div style="text-align: center; margin: 1em 0;">
        <input 
            name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
            type="submit"
            id="db_opcao"
            value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" 
            <?= ($db_botao == false ? "disabled" : "") ?> 
            onclick="return js_validaSelect();"
        >
        <input 
            id="btnActionPix"
            name="Alterar"
            value="Alterar"
            type="button"
            style="display: none;"
            onclick="return requestTipodeDebito();"
        >
        <?php
            if ($db_opcao != 1)
            {
                echo '<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" />';
            }   
        ?>
        </div>
    </div>
</form>

<script type="text/javascript">
    new MaskedInput("#k00_horainicial", "00:00", {placeholder:"0"});
    new MaskedInput("#k00_horafinal",   "00:00", {placeholder:"0"});

    const 
        inputAction    = document.getElementById("db_opcao"),
        inputActionPIX = document.getElementById("btnActionPix");

    var 
        hiddenDtVencimento = document.getElementById('hiddendtvencimento').value,
        selectTipoVenc     = document.getElementById('selectTipoVenc'),
        tdVencimento       = document.getElementById('tdVencimento'),
        selectList         = document.createElement('select'),
        days               = Array.from(
            {length: 31}, 
            function() { return k + 1; }
        );
    
    selectList.id = "dtVencimentoSelect";
    selectList.name = "k00_dtVencimentoDay";

    for (var i = 0; i < days.length; i++)
    {
        var option = document.createElement("option");

        option.value = i + 1;
        option.text  = i + 1;

        selectList.appendChild(option);
    }

    tdVencimento.appendChild(selectList);

    var div = document.createElement('div');

    div.id = "divDtVencimento";

    var input = document.createElement('input');

    input.id         = "dtVencimentoDate";
    input.name       = "k00_dtVencimentoDate";
    input.value      = hiddenDtVencimento;
    selectList.value = hiddenDtVencimento;

    div.appendChild(input);
    tdVencimento.appendChild(div);

    var oDataVencimento  =  DBInputDate.create($('dtVencimentoDate'));

    if (hiddenDtVencimento != "" && hiddenDtVencimento.length > 2)
    {
        if (hiddenDtVencimento != "" && hiddenDtVencimento.length == 4)
        {
            selectTipoVenc.value = "1";

            $('lDataVencimento').hide();
            $('dtVencimentoSelect').hide();
        }
        else
        {
            selectTipoVenc.value = "3";
            selectList.value     = "";

            document.getElementById('lblDatavencimento').innerText = "Data:";

            $('divDtVencimento').show();
            $('dtVencimentoSelect').hide();
            $('lDataVencimento').show();
        }
    }

    if($('k00_horainicial').value != "")
    {
        $('hrIntervalo').show();

        selectTipoVenc.value = "4";
    }
    else
    {
        $('hrIntervalo').hide();
    }

    if (
        hiddenDtVencimento != "" && 
        (
            hiddenDtVencimento.length == 2 || 
            hiddenDtVencimento.length == 1
        )
    )
    {
        selectTipoVenc.value = "2";
        input.value          = "";

        document.getElementById('lblDatavencimento').innerText = "Dia do Mês:";
        
        $('dtVencimentoSelect').show();
        $('divDtVencimento').hide();
        $('lDataVencimento').show();
    }

    function selecionaTipoVencimento(elementDom)
    {
        switch (elementDom.value)
        {
            case '0':
                input.value                = "";
                selectList.value           = "";
                $('k00_horainicial').value = null;
                $('k00_horafinal').value   = null;

                $('lDataVencimento').hide();
                $('dtVencimentoSelect').hide();
                $('hrIntervalo').hide();
                break;
            case '1':
                input.value                = "";
                selectList.value           = "";
                $('k00_horainicial').value = null;
                $('k00_horafinal').value   = null;

                $('lDataVencimento').hide();
                $('dtVencimentoSelect').hide();
                $('hrIntervalo').hide();
                break;
            case '2':
                document.getElementById('lblDatavencimento').innerText = "Dia do Mês:";

                input.value                = "";
                $('k00_horainicial').value = null;
                $('k00_horafinal').value   = null;

                $('dtVencimentoSelect').show();
                $('divDtVencimento').hide();
                $('hrIntervalo').hide();
                $('lDataVencimento').show();
                break;
            case '3':
                document.getElementById('lblDatavencimento').innerText = "Data:";
                $('divDtVencimento').show();

                selectList.value           = "";
                $('k00_horainicial').value = null;
                $('k00_horafinal').value   = null;

                $('dtVencimentoSelect').hide();
                $('lDataVencimento').show();
                $('hrIntervalo').hide();
                break;
            case '4':
                document.getElementById('lblDatavencimento').innerText = "Intervalo:";
                input.value      = "";
                selectList.value = "";

                $('divDtVencimento').hide();
                $('dtVencimentoSelect').hide();
                $('lDataVencimento').hide();
                $('hrIntervalo').show();
                break;
            default:
                $('lDataVencimento').hide();
                $('dtVencimentoSelect').hide();
                $('hrIntervalo').hide();

                input.value      = "";
                selectList.value = "";
                break;
        }
    }


    var oAbas = new DBAbas($("container"));

    oAbas.adicionarAba('Detalhes', $("parametros"));
    oAbas.adicionarAba('Mensagens', $("mensagens"));
    oAbas.adicionarAba('TEF', $("tef"));
    oAbas.adicionarAba('PIX', $("pix"));

    function js_controlatextarea(objt, max, dv)
    {
        obj = eval('document.form1.' + objt);
        atu = max - obj.value.length;

        document.getElementById(eval('dv')).innerHTML = 'Caracteres disponiveis : ' + atu + ' de ' + max;

        if (obj.value.length > max)
        {
            alert('A mensagem não pode ter no máximo 150 caracteres !');

            obj.value = obj.value.substr(0, 150);

            document.getElementById(eval('dv')).innerHTML = 'Caracteres disponiveis : 0 de ' + max;

            obj.select();
            obj.focus();
        }

        if (obj.value.length == 0)
        {
            document.getElementById(eval('dv')).innerHTML = '';
        }
    }

    function js_controladig3(dobj)
    {
        digobj = eval('document.form1.' + dobj);

        if (digobj.value != 6 && digobj.value != 7)
        {
            alert('O terceiro digito so pode ser 6 ou 7');

            digobj.value = '';

            digobj.select();
            digobj.focus();
        }
    }

    function js_pesquisak03_tipo(mostra)
    {
        if (mostra == true)
        {
            var sUrl = 'func_cadtipo.php?funcao_js=parent.js_mostracadtipo1|k03_tipo|k03_descr';

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_cadtipo',
                sUrl,
                'Pesquisa',
                true
            );
        }
        else
        {
            if (document.form1.k03_tipo.value != '')
            {
                var sUrl = 'func_cadtipo.php?pesquisa_chave=' + 
                    document.form1.k03_tipo.value + 
                    '&funcao_js=parent.js_mostracadtipo';

                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cadtipo', sUrl, 'Pesquisa', false);
            } 
            else
            {
                document.form1.k03_tipo.value = '';
            }
        }
    }

    function js_mostracadtipo(chave, erro)
    {
        document.form1.tipodescr.value = chave;

        if (erro == true)
        {
            document.form1.k03_tipo.focus();
            document.form1.k03_tipo.value = '';
        }
    }

    function js_mostracadtipo1(chave1, chave2)
    {
        document.form1.k03_tipo.value  = chave1;
        document.form1.tipodescr.value = chave2;

        db_iframe_cadtipo.hide();
    }

    function js_pesquisak00_receitacredito(mostra)
    {
        if (mostra)
        {
            var sUrl = 'func_tabrec.php?funcao_js=parent.js_mostratabrec1|k02_codigo|k02_descr';

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_tabrec',
                sUrl,
                'Pesquisa Receitas',
                true
            );
        }
        else
        {
            if (document.form1.k00_receitacredito.value != '')
            {
                var sUrl = 'func_tabrec.php?pesquisa_chave=' + 
                    document.form1.k00_receitacredito.value + 
                    '&funcao_js=parent.js_mostratabrec';

                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_tabrec',
                    sUrl,
                    'Pesquisa Receitas',
                    false
                );
            }
            else
            {
                document.form1.k00_receitacredito.value = '';
            }
        }
    }

    function js_mostratabrec(chave, erro)
    {
        document.form1.receitacreditodescr.value = chave;

        if (erro)
        {
            document.form1.k00_receitacredito.focus();
            document.form1.k00_receitacredito.value = '';
        }
    }

    function js_mostratabrec1(chave1, chave2)
    {
        document.form1.k00_receitacredito.value  = chave1;
        document.form1.receitacreditodescr.value = chave2;

        db_iframe_tabrec.hide();
    }


    function js_pesquisa()
    {
        var sUrl = 'func_arretipo.php?funcao_js=parent.js_preenchepesquisa|k00_tipo';

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_arretipo',
            sUrl,
            'Pesquisa',
            true
        );
    }

    function js_preenchepesquisa(chave)
    {
        db_iframe_arretipo.hide();

        <?php
            if ($db_opcao != 1)
            {
                echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
            }
        ?>
    }

    function js_validaSelect()
    {
        var sSelectTipoV = $('selectTipoVenc').value;
        var sHoraInicial = $('k00_horainicial').value;
        var sHoraFinal   = $('k00_horafinal').value;

        if (sSelectTipoV == 4)
        {
            if (sHoraInicial == "" || sHoraFinal == "")
            {
                alert("Informe o Intervalo de Horas para o Bloqueio!");

                return false;
            }
            if (
                sHoraInicial != "" && 
                sHoraFinal != "" && 
                sHoraInicial == sHoraFinal
            )
            {
                alert("Hora Inicial e Final não podem ser iguais!");

                return false;
            }
        }

        return true;
    }

  const ancoraTaxaEspecifica = $('ancoraTaxaEspecifica');
  const codigoTaxaEspecifica = $('k00_taxaespecifica');
  const descricaoTaxaEspecifica = $('descricaoTaxaEspecifica');
  const pesquisaTaxaEspecifica = mostra => {

    let mostraPesquisa = '|codsubrec|k07_descr';
    let naoMostraPesquisa = `&pesquisa_chave=${codigoTaxaEspecifica.value}`;
    let url = 'func_tabdesc.php?funcao_js=parent.retornoPesquisaTaxaEspecifica';
    url += mostra ? mostraPesquisa : naoMostraPesquisa;

    js_OpenJanelaIframe('', 'db_iframe', url, 'Pesquisa Taxa Específica', mostra);
  }

  function retornoPesquisaTaxaEspecifica() {

    if(typeof arguments[1] === "boolean") {
      descricaoTaxaEspecifica.value = arguments[0];

      if(arguments[1] === true) {
        codigoTaxaEspecifica.value = '';
      }
    } else {
      codigoTaxaEspecifica.value = arguments[0];
      descricaoTaxaEspecifica.value = arguments[1];
    }

    db_iframe.hide();
  }

  ancoraTaxaEspecifica.onclick = () => pesquisaTaxaEspecifica(true);
  codigoTaxaEspecifica.onchange = () => {

    if(codigoTaxaEspecifica.value === '') {
      codigoTaxaEspecifica.value = '';
      descricaoTaxaEspecifica.value = '';
    }

    pesquisaTaxaEspecifica(false);
  }

  document.body.onload = () => {

    if (codigoTaxaEspecifica.value !== '') {
      pesquisaTaxaEspecifica(false);
    }
  }

  $('k00_tipo').className = 'field-size2';
  $('k00_codbco').className = 'field-size2';
  $('k00_codage').className = 'field-size2';
  $('k00_marcado').className = 'field-size2';
  $('k00_rectx').className = 'field-size2';
  $('k00_txban').className = 'field-size2';
  $('k00_vlrmin').className = 'field-size2';
  $('k03_tipo').className = 'field-size2';
  $('k00_receitacredito').className = 'field-size2';
  $('k00_tercdigcarneunica').className = 'field-size2';
  $('k00_tercdigcarnenormal').className = 'field-size2';
  $('k00_tercdigrecunica').className = 'field-size2';
  $('k00_tercdigrecnormal').className = 'field-size2';
  $('k00_exercicioscarne').className = 'field-size2';

  $('tipodescr').className = 'field-size7';
  $('receitacreditodescr').className = 'field-size7';

  $('k00_descr').className = 'field-size-max';

  codigoTaxaEspecifica.className = 'field-size2';
  descricaoTaxaEspecifica.className = 'field-size7';

</script>
