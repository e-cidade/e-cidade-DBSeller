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
 *
 */

//MODULO: Laboratório
require_once(modification("libs/db_conecta.php"));
$cllab_requisicao->rotulo->label();
$cllab_requiitem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("la02_i_codigo");
$clrotulo->label("la02_c_descr");
$clrotulo->label("la08_i_codigo");
$clrotulo->label("la08_c_descr");
$clrotulo->label("la09_i_codigo");
$clrotulo->label("la09_i_exame");
$clrotulo->label("descrdepto");
$clrotulo->label("nome");
$clrotulo->label("z01_i_cgsund");
$clrotulo->label("z01_v_nome");
$clrotulo->label("z01_nome");
$clrotulo->label("la23_i_codigo");
$clrotulo->label("la38_i_medico");
$clrotulo->label("sd03_i_crm");
$clrotulo->label("la24_i_laboratorio");

$flagAutorizarExames = $oConfig->la49_autorizarexamesaoconfirmar === 't' ? true : false;
$permitido = db_permissaomenu(date('Y'), db_getsession('DB_modulo'), 8344) == 'true';
$requisicaoAutorizada = (!empty($la22_i_autoriza) ? $la22_i_autoriza == 2 : false);
?>
<form name="form1" method="post" action="">
    <div class="container">
        <fieldset>
            <legend>Requisição</legend>

            <input id="flagAutorizarExames" type="hidden" value="<?php
            echo $flagAutorizarExames; ?>">
            <input id="habilitaGrupo" type="hidden" value="<?php
            echo $oConfig->la49_habilitargrupo; ?>">
            <input id="departamento" type="hidden" value="<?php
            echo $departamento; ?>">

            <fieldset style='border:none;'>
                <table class="form-container alinhaColuna">
                    <tr>
                        <td>
                            <label for='la22_i_codigo'>
                                <?php
                                echo $Lla22_i_codigo; ?>
                            </label>
                        </td>
                        <td>
                            <?php
                            db_input('la22_i_codigo', 10, $Ila22_i_codigo, true, 'text', 3, "") ?>
                            <label for="la22_d_data">
                                <b>Data:</b>
                            </label>
                            <?php
                            db_inputdata(
                                'la22_d_data',
                                @$la22_d_data_dia,
                                @$la22_d_data_mes,
                                @$la22_d_data_ano,
                                true,
                                'text',
                                3,
                                ""
                            ) ?>
                        </td>
                        <td id="viewNumeroControleInterno" colspan="6"></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset class='separator'>
                <legend>Profissional</legend>
                <table class="form-container alinhaColuna">
                    <tr>
                        <td>
                            <label for="la38_i_medico">
                                <?php
                                db_ancora($Lla38_i_medico, "js_pesquisala38_i_medico(true);", ""); ?>
                            </label>
                        </td>
                        <td>
                            <?php
                            db_input(
                                'la38_i_medico',
                                10,
                                $Ila38_i_medico,
                                true,
                                'text',
                                "",
                                " onchange='js_pesquisala38_i_medico(false);'"
                            );
                            db_input('z01_nome', 58, $Ila22_c_medico, true, 'text', 1, '');

                            if ($permitido) {
                                ?>
                                <input type="button" id="cadProf" title="Cadastro de Profissionais Fora da Rede"
                                       name="cadProf" value="Cadastro de Profissionais" onclick="js_abreCadProf();">
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>


            <fieldset class='separator'>
                <legend>Paciente</legend>
                <div id="status-microarea" class="alert-danger" style="text-align: center;" role="alert" hidden>
                    Paciente sem cadastro em uma microárea!
                </div>
                <table id="tabela1" class="form-container alinhaColuna">
                    <tr id="linhaIdade" style="display: none;">
                        <td colspan="2" id="linhaDaIdadeCompleta" style="text-align: end;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="la22_i_cgs">
                                <?php
                                $iOpcao = $db_opcao == 2 ? 3 : $db_opcao;
                                db_ancora($Lla22_i_cgs, "js_pesquisala22_i_cgs(true);", $iOpcao);
                                ?>
                            </label>
                        </td>
                        <td>
                            <?php
                            $iOpcao = $db_opcao == 2 ? 3 : $db_opcao;
                            db_input(
                                'la22_i_cgs',
                                10,
                                $Ila22_i_cgs,
                                true,
                                'text',
                                $iOpcao,
                                " onchange='js_pesquisala22_i_cgs(false);'"
                            );
                            db_input('z01_v_nome', 82, $Iz01_v_nome, true, 'text', 3, '');
                            db_input('z01_v_sexo', 50, "", true, 'hidden', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr style="display:<?php
                    echo (@$z01_v_sexo == "F") ? "" : "none"; ?>" id="linha_dum">
                        <td>
                            <label for="la22_d_dum"><?php
                                echo $Lla22_d_dum ?></label>
                        </td>

                        <td>
                            <?php
                            db_inputdata(
                                'la22_d_dum',
                                @$la22_d_dum_dia,
                                @$la22_d_dum_mes,
                                @$la22_d_dum_ano,
                                true,
                                'text',
                                $db_opcao,
                                ""
                            ) ?>

                            <div id="boxPrioridades" style=" display:
                                <?= (($oConfig->la49_identificaprioridade == 't' ||
                                    (!empty($prioridadeDaRequisicao) && $prioridadeDaRequisicao > 1)) ?
                                    'inline;' :
                                    'none;')
                                ?>
                                margin-left: 30px;">
                                <input
                                    id="prioridadeGestante"
                                    name="prioridadeGestante"
                                    <?= ((!empty($prioridadeDaRequisicao) && $prioridadeDaRequisicao == 2) ? 'checked' : '') ?>
                                    value="2"
                                    type="checkbox">

                                <label for="prioridadeGestante">Gestante</label>
                            </div>
                        </td>
                    </tr>
                    <input type="hidden" id="sexoPaciente" value="<?= (!empty($z01_v_sexo) ? $z01_v_sexo : '') ?>">
                    <tr>
                        <td>
                            <label for="la22_c_responsavel"><b>Responsavel:</b></label>
                        </td>
                        <td>
                            <?php
                            db_input('la22_c_responsavel', 64, $Ila22_c_responsavel, true, 'text', $db_opcao, '') ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="la22_c_contato"><b>Contato:</b></label>
                        </td>
                        <td>
                            <?php
                            db_input('la22_c_contato', 64, @$Ila22_c_contato, true, 'text', $db_opcao, '') ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset class='separator'>
                <legend>Outras Informações</legend>

                <div>
                    <div
                        style="display: flex; column-gap: 30px; justify-content: center; margin-top: 20px; margin-bottom: 20px; margin-left: -220px">
                        <div>
                            <label class="bold" for="la22_peso">Peso:</label>
                            <?php
                            db_input(
                                'la22_peso',
                                6,
                                @$Ila22_peso,
                                true,
                                'text',
                                '',
                                " onkeydown='validaInput(this, 4, event, true);'",
                                '',
                                'white',
                                '',
                                '7'
                            ); ?>
                            <span>kg</span>
                        </div>

                        <div>
                            <label class="bold" for="la22_altura">Altura:</label>
                            <?php
                            db_input('la22_altura', 3, @$Ila22_altura, true, 'text', '', "", '', 'white', '', '3'); ?>
                            <span>cm</span>
                        </div>

                        <div>
                            <label class="bold" for="la22_volumeamostra">Volume Amostra:</label>
                            <?php
                            db_input(
                                'la22_volumeamostra',
                                4,
                                @$Ila22_volumeamostra,
                                true,
                                'text',
                                '',
                                '',
                                '',
                                'white',
                                '',
                                '4'
                            ); ?>
                            <span>ml</span>
                        </div>
                    </div>

                    <div>
                        <input type="button" id="medicamentos" name="medicamentos" Value="Medicamentos"
                               onclick="js_lanca(1)">
                        <input type="button" id="diagnostico" name="diagnostico" value="Diagnóstico"
                               onclick="js_lanca(2)">
                        <input type="button" id="obs" name="obs" value="Observação" onclick="js_lanca(3)">
                    </div>
                </div>

            </fieldset>

            <fieldset>
                <legend>Exames</legend>
                <input type="hidden" id="input-liberadoisexames" value="<?= $oConfig->la49_i_exameduplo ?>">

                <?php
                if ($oConfig->la49_habilitargrupo === "t") { ?>
                    <fieldset>
                        <legend>Lançamento por Grupo</legend>
                        <table style="width:850px">
                            <tr>
                                <td style="width: 10%;">
                                    <div style="margin-top: 5px">
                                        <label id="ancora-grupo" style="font-weight: bold; font-size: 12px"
                                               for="codigo-grupo"><b>Grupo:</b>&nbsp;</label>
                                    </div>
                                </td>
                                <td style="width: 55%;">
                                    <div style="margin-top: 5px;">
                                        <input type="text" name="codigo-grupo" id="la66_codigo" class="field-size2">
                                        <input type="text" name="descricaogrupo" id="la66_descricao"
                                               class="field-size8">
                                    </div>
                                </td>
                                <td>
                                    <div style=" margin-top: 5px; margin-left: 20%">
                                        <label id=""><b>Quantidade: </b></label>
                                        <input type="text" id="input-quantidadegrupo" class="field-size2" value="1">
                                    </div>
                                </td>
                                <td>
                                    <div style=" margin-top: 5px;">
                                        <input type="button" Value="Lançar" name="lanc" id="lancGrupo"
                                               onclick="lancarGrupo();">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <?php
                } ?>
                <fieldset>
                    <legend>Lançamento por Exame</legend>
                    <table style="width:850px">
                        <tr>
                            <td style="width: 10%; ">
                                <label for="la09_i_exame">
                                    <?php
                                    db_ancora($Lla09_i_exame, "js_pesquisala09_i_exame(true);", $db_opcao); ?>
                                </label>
                            </td>
                            <td style="width: 55%;">
                                <?php
                                db_input(
                                    'la09_i_exame',
                                    10,
                                    $Ila38_i_medico,
                                    true,
                                    'text',
                                    1,
                                    'onchange="js_pesquisala09_i_exame(false);"'
                                );
                                db_input(
                                    'la08_c_descr',
                                    55,
                                    $Ila22_c_medico,
                                    true,
                                    'text',
                                    1,
                                    'onchange="js_validaDescricaoExame();"'
                                );
                                db_input('descricaoExameAutoComplete', 10, 1, true, 'hidden', 1);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo "<label style='margin-left: 20%' for='la21_i_quantidade'>$Lla21_i_quantidade </label>";
                                $la21_i_quantidade = 1;
                                db_input(
                                    'la21_i_quantidade',
                                    2,
                                    $Ila21_i_quantidade,
                                    true,
                                    'text',
                                    1,
                                    "onchange='js_validarQuantidade(this);' class='field-size2'"
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="la09_i_codigo"> <b><?php
                                        echo $Lla24_i_laboratorio; ?></b> </label>
                            </td>
                            <td>
                                <?php
                                $aOptions = array("0" => "Selecione::");
                                db_select(
                                    "la09_i_codigo",
                                    $aOptions,
                                    $Ila09_i_codigo,
                                    $db_opcao,
                                    "onchange=\"js_LoadSetorExame(this.value);\""
                                );
                                ?>
                            </td>
                            <td>
                                <label for="la21_d_data" style="margin-left: 20%">
                                    <a onclick="pegaPosMouse(event);js_calendario();" class="dbancora"
                                       id="ancora_calend"
                                       style="color: blue; text-decoration: underline; cursor: pointer;">
                                        <b>Data:</b>
                                    </a>
                                </label>
                                <?php
                                db_inputdata(
                                    'la21_d_data',
                                    @$la21_d_data_dia,
                                    @$la21_d_data_mes,
                                    @$la21_d_data_ano,
                                    true,
                                    'text',
                                    $db_opcao,
                                    " disabled "
                                ) ?>
                            </td>
                            <td>
                                <input type="button" Value="Lançar" name="lanc" id="lanc" onclick="js_IncluirExame();">
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class='separator'>
                    <legend>Exames Lançados</legend>
                    <div id="GridExames" name="GridExames"></div>
                    <select id='exameSelecionadoGrid' name="exames" style="display: none; "></select>
                </fieldset>

            </fieldset>

        </fieldset>
        <?php
        if ($flagAutorizarExames) { ?>
            <input name="confirma" type="submit" id="btnConfirma" value="Salvar & Autorizar"
                   onclick="return js_envia()">
            <?php
        } else { ?>
            <input name="confirma" type="submit" id="btnConfirma" value="Salvar" onclick="return js_envia()">
            <?php
        } ?>
        <input name="excluirExame" type="button" id="btnExcluirExames" value="Excluir Exames"
               onclick="js_excluirExames()">
        <input name="excluir" type="submit" id="db_opcao" value="Excluir Requisição" disabled
               onclick="return atualizaCota()">
        <?php
        if ($permitido && $flagAutorizarExames == false) { ?>
            <input name="autorizar" type="button" id="autorizar" value="Autorizar" onclick="js_autorizaExames()">
            <?php
        } ?>
        <input name="conprov" type="button" id="comprov" value="Comprovante" onclick="js_comprovante();" disabled>
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa()">
        <input name="nova" type="button" id="nova" value="Nova Requisição" onclick="js_nova()">
        <?php
        db_input('la21_c_dia', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la21_c_hora', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la08_i_dias', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la02_i_codigo', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la02_c_descr', 20, "", true, 'hidden', $db_opcao, '');
        db_input('requisitos', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la22_t_medicamento', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la22_t_diagnostico', 20, "", true, 'hidden', $db_opcao, '');
        db_input('la22_t_observacao', 20, "", true, 'hidden', $db_opcao, '');
        db_input('sStr', 20, "", true, 'hidden', $db_opcao, '');
        db_input('sUrgente', 20, "", true, 'hidden', $db_opcao, '');
        db_input('obrigaPeso', 20, "", true, 'hidden', $db_opcao, '');
        db_input('obrigaAltura', 20, "", true, 'hidden', $db_opcao, '');
        db_input('obrigaVolumeAmostra', 20, "", true, 'hidden', $db_opcao, '');
        db_input('idadeEmAnos', 20, "", true, 'hidden', $db_opcao, '');
        ?>
    </div>
</form>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script>

    const MENSAGEM_FORMULARIO_REQUISICAO = "saude.laboratorio.db_frmlab_requisicao.";
    let temExameColetado = false;
    let idSetorExame = null;
    let listaExames = {};

    //Inputs
    const inputLiberaDoisExames = document.getElementById('input-liberadoisexames');
    const inputQuantidadeGrupo = document.getElementById('input-quantidadegrupo');
    const la09_i_exame = document.getElementById("la09_i_exame");
    const la08_c_descr = document.getElementById("la08_c_descr");
    const la09_i_codigo = document.getElementById("la09_i_codigo");
    const la22_i_cgs = document.getElementById("la22_i_cgs");
    const flagAutorizarExames = document.getElementById("flagAutorizarExames");
    const habilitaGrupo = document.getElementById("habilitaGrupo");
    const departamento = document.getElementById("departamento");
    const inputCodigoGrupo = document.getElementById('la66_codigo');
    const inputDescricaoGrupo = document.getElementById('la66_descricao');

    if (habilitaGrupo.value === "t") {
        inputDescricaoGrupo.addEventListener('blur',function(){
            js_validaDescricaoGrupo();
        });
    }
    //Ancoras
    const ancoraGrupo = document.getElementById('ancora-grupo');

    //Buttons
    const nova = document.getElementById("nova");
    const lanc = document.getElementById("lanc");

    //Alert
    const alertify_ok = parent.document.getElementById("alertify-ok");

    //Grid
    var gridExamesLancados = "";

    var codigoGrupoLaboratorio = "";

    if (alertify_ok != null) {
        alertify_ok.addEventListener('click', function () {
            nova.focus();
        })
    }

    if (habilitaGrupo.value === "t") {

        new DBLookUp(ancoraGrupo, inputCodigoGrupo, inputDescricaoGrupo, {
            'sArquivo': 'func_lab_labgrupoexame.php',
            'sLabel': 'Pesquisar Grupo',
            'sObjetoLookUp': "db_iframe_lab_labgrupoexame",
            'aCamposAdicionais': ['la67_codigo'],
            'aParametrosAdicionais': ["departamento=" + departamento.value],
            'fCallBack': (codigoExame, descricaoExame, retornoCodigoGrupoLaboratorio) => {
                document.getElementById('lancGrupo').disabled = false;
                if(!codigoExame){
                    document.getElementById('lancGrupo').disabled = true;
                    inputCodigoGrupo.value = '';
                }
                inputDescricaoGrupo.value = descricaoExame;
                codigoGrupoLaboratorio = retornoCodigoGrupoLaboratorio;
            }
        });

        inputDescricaoGrupo.removeAttribute('readonly');
        inputDescricaoGrupo.removeAttribute('class');
        inputDescricaoGrupo.setAttribute('class', 'field-size8');

        //Autocomplete do Grupo
        oAutoCompleteGrupo = new dbAutoComplete(inputDescricaoGrupo, 'lab4_agendar.RPC.php?autoCompleteGrupos=1&departamento=' + departamento.value);
        oAutoCompleteGrupo.setTxtFieldId(inputCodigoGrupo);
        oAutoCompleteGrupo.setHeightList(180);
        oAutoCompleteGrupo.show();
    }

    /**
     * Variável informa se é utilizado uma forma de controlar os exames.
     *  0 => Não utiliza
     *  1 => Utiliza controle Financeiro
     *  2 => Utiliza controle Atendimento diário (Pacientes por Dia)
     * @type {Number}
     */

    var iControlaSaldoExames = 0;

    $('la22_i_codigo').addClassName('field-size2');
    $('la22_d_data').addClassName('field-size2');
    $('la38_i_medico').addClassName('field-size2');
    $('la22_i_cgs').addClassName('field-size2');
    $('la22_d_dum').addClassName('field-size2');
    $('la09_i_exame').addClassName('field-size2');
    $('la08_c_descr').addClassName('field-size8');
    $('la21_i_quantidade').addClassName('field-size1');
    $('la21_d_data').addClassName('field-size2');
    $('la09_i_codigo').setAttribute('rel', 'ignore-css');
    $('la22_c_responsavel').style.width = '100%';
    $('la22_c_contato').style.width = '100%';
    $('la09_i_codigo').style.width = '100%';
    $('GridExames').style = 'width:850px;'

    $('la08_c_descr').value = '';
    $('la08_c_descr').onkeydown = '';

    var urlRpcExame = 'lab4_agendar.RPC.php?tipo=2';

    //Autocomplete do Exame
    oAutoComplete1 = new dbAutoComplete(document.form1.la08_c_descr, urlRpcExame);
    oAutoComplete1.setTxtFieldId(document.getElementById('la09_i_exame'));
    oAutoComplete1.setHeightList(180);
    oAutoComplete1.show();

    oAutoComplete1.setCallBackFunction(function (id, label) {
        lanc.disabled = false;
        document.form1.la09_i_exame.value = id;
        document.form1.la08_c_descr.value = label;
        document.form1.descricaoExameAutoComplete.value = label;
        js_loadLaboratorios(id);
    });

    var viewNumeroControleInterno = new ViewNumeroControleInterno('viewNumeroControleInterno', false);
    viewNumeroControleInterno.setRequisicaoElemento($('la22_i_codigo'));

    var apenasLeitura = true;
    <?php
    if (isset($la22_i_autoriza) && $la22_i_autoriza == '1') { ?>
    apenasLeitura = false;
    <?php
    } ?>

    viewNumeroControleInterno.setApenasLeitura(apenasLeitura);
    viewNumeroControleInterno.show($('viewNumeroControleInterno'));

    <?php
    // Busca Número Controle Interno no retorno da pesquisa ou quando requisição for autorizada
    if (
    isset($_GET['chavepesquisa']) && !empty($_GET['chavepesquisa'])
    && (
        isset($_GET['retornopesquisa']) || (isset($_GET['autorizado']) && $_GET['autorizado'] == '1')
    )
    ) { ?>
    if (viewNumeroControleInterno.getParametroAtivo() === true) {
        viewNumeroControleInterno.getNumeroControleInternoPorRequisicao(<?php echo $_GET['chavepesquisa'] ?>);
    }
    <?php
    } ?>

    $('z01_nome').onkeydown = '';

    //Autocomplete do profissional
    oAutoComplete2 = new dbAutoComplete(document.form1.z01_nome, 'lab4_agendar.RPC.php?tipo=1');
    oAutoComplete2.setTxtFieldId(document.getElementById('la38_i_medico'));
    oAutoComplete2.setHeightList(180);
    oAutoComplete2.show();
    oAutoComplete2.setCallBackFunction(function (id, label) {

        $('la38_i_medico').value = id;
        $('z01_nome').value = label;
        js_desbloqueiaBotao();
    })


    //Inicializar Rotina
    sRPC = 'lab4_agendar.RPC.php';
    lab_ajax = new ws_ajax('lab4_agendar.RPC.php');

    var aHeader = ['Cod.', 'Laboratório', 'Exame', 'Quantidade', 'Coleta', 'Hora', 'Entrega', 'Urgente', 'obrigaPeso', 'obrigaAltura', 'obrigaVolumeAmostra', 'Situação'];
    var aWidth = ['1%', '20%', '20%', '11%', '10%', '5%', '8%', '7%', '1%', '1%', '1%', '13%'];
    var aAlign = ['center', 'right', 'right', 'right', 'right', 'center', 'center', 'center', 'center', 'center'];

    objGridExames = new DBGrid('oGridExames');
    objGridExames.nameInstance = 'objGridExames';
    objGridExames.setCheckbox(0);
    objGridExames.setCellWidth(aWidth);
    objGridExames.setCellAlign(aAlign);
    objGridExames.setHeader(aHeader);
    objGridExames.aHeaders[1].lDisplayed = false;
    objGridExames.aHeaders[9].lDisplayed = false;
    objGridExames.aHeaders[10].lDisplayed = false;
    objGridExames.aHeaders[11].lDisplayed = false;
    objGridExames.setHeight(100);
    objGridExames.show($('GridExames'));

    F = document.form1;
    F.dtjs_la21_d_data.style.display = 'none';
    document.getElementById('col1').removeAttribute('style');
    document.getElementById('col1').setAttribute('style', 'width:3%');
    <?php
    if ($cllab_setorexame->numrows > 0) {
        echo " js_LoadSetorExame(F.la09_i_codigo.value); ";
    }
    ?>

    const divAlert = document.getElementById('status-microarea');
    const inputCgs = {
        id: document.getElementById('la22_i_cgs'),
        nome: document.getElementById('z01_v_nome')
    };
    const validaCgs = new ValidaCgs(inputCgs);

    window.onload = () => {
        validaCgs.cadastroMicroarea(inputCgs, divAlert)
        let campoPeso = document.getElementById('la22_peso');
        campoPeso.value = campoPeso.value.replace('.', ',');
        acrescentaZerosCamposInput();
    }

    //Seletor de tipo
    function js_tipo(sexo) {

        var table = document.getElementById('tabela1');
        status = 'none';

        if (sexo == 'F') {
            status = '';
        }

        for (var r = 0; r < table.rows.length; r++) {

            var id = table.rows[r].id;

            if (id == 'linha_dum') {
                table.rows[r].style.display = status;
            }
        }
    }

    function js_AtualizaGrid() {

        objGridExames.clearAll(true);
        tam = F.exames.length;

        for (x = 0; x < tam; x++) {

            sText = F.exames.options[x].text;
            avet = sText.split('#');

            var sOpcaoDisabled = '';
            var sClassName = "";
            var scheck = (avet[6] == 1) ? ' checked ' : '';

            if (!empty(scheck)) {
                sClassName = 'error';
            }

            if (avet[11] && avet[11] != '10 - Nao Digitado') {

                sOpcaoDisabled = " disabled='disabled'";
                sClassName = 'disabled';
            }

            alinha = new Array();
            alinha[0] = avet[0]; //codigo exame
            alinha[1] = avet[1]; //descr  laboratorio
            alinha[2] = avet[2]; //descr  exame
            alinha[3] = avet[7]; //quantidade
            alinha[4] = avet[3]; //data coleta
            alinha[5] = avet[4]; //hora coleta
            alinha[6] = avet[5] + ' dias'; //data entrega
            alinha[8] = avet[8]; //obriga peso
            alinha[9] = avet[9]; //obriga altura
            alinha[10] = avet[10]; //obriga volume amostra
            alinha[11] = avet[11] || "0 - Não Salva";

            //Altera nomenclatura da requisição.
            if (alinha[11] == "10 - Nao Digitado") {
                alinha[11] = '10 - Requisitado';
            }

            var oCheckUrgente = '<input type="checkbox" id="urgente' + x + '" ' + scheck + sOpcaoDisabled;
            oCheckUrgente += ' onclick="js_marcaUrgenteLinhaGrid(' + x + ', this)">';
            alinha[7] = oCheckUrgente;

            objGridExames.addRow(alinha);
            objGridExames.aRows[x].addClassName(sClassName);

            if (!alinha[11].include('0 - Não Salva') && !alinha[11].include('10 - Requisitado') && !alinha[11].include('20 - Autorizado')) {
                temExameColetado = true;
            }

        }
        objGridExames.renderRows();
        ajustarDescricaoLaboratorioExame();
        if (F.exames.length > 0) {
            ajustarPrimeiraLinhaGrid();
        }

        camposObrigatoriosBloqueados();
    }

    function js_marcaUrgenteLinhaGrid(iLinha, oElemento) {

        var sIdLinhaGrid = objGridExames.sName + 'row' + objGridExames.sName + '' + iLinha;

        $(sIdLinhaGrid).removeClassName('error');

        if (oElemento.checked) {
            $(sIdLinhaGrid).addClassName('error');
        }
    }

    function js_DadosExames() {

        aVet = new Array();
        aVet[aVet.length] = F.la09_i_codigo.value;
        aVet[aVet.length] = F.la09_i_codigo.options[F.la09_i_codigo.selectedIndex].text;
        aVet[aVet.length] = F.la08_c_descr.value;
        aVet[aVet.length] = F.la21_d_data.value;
        aVet[aVet.length] = F.la21_c_hora.value;
        aVet[aVet.length] = F.la08_i_dias.value;
        aVet[aVet.length] = '0';
        aVet[aVet.length] = F.la21_i_quantidade.value;
        aVet[aVet.length] = F.obrigaPeso.value;
        aVet[aVet.length] = F.obrigaAltura.value;
        aVet[aVet.length] = F.obrigaVolumeAmostra.value;

        sStr = aVet.join('#');
        return sStr.replace('##', '');
    }

    function lancarGrupo() {
        if (inputCodigoGrupo.value) {
            var oParam = new Object();
            oParam.exec = 'buscaExamesPorGrupoELaboratorio';
            oParam.grupo = inputCodigoGrupo.value;
            oParam.departamento = departamento.value;

            var exames = js_ajax(oParam, 'js_retornoBuscaExamesPorGrupoELaboratorio', 'lab4_agendar.RPC.php', false);
            let examesFiltrados = [];
            let examesExistentesGrid = [];

            for (var i = 0; i < exames.length; i++) {
                if (inputLiberaDoisExames.value == 2 && js_verificaexame(exames[i].codigosetorexame)) {
                    examesFiltrados.push(exames[i]);
                } else if(inputLiberaDoisExames.value == 2){
                    examesExistentesGrid.push(exames[i]);
                }
            }
            exames = examesFiltrados;

            if(examesExistentesGrid.length > 0){

                let msgAlertaExamesExistentes = "O exame " + examesExistentesGrid[0].descricao.replaceAll('+', ' ');
                msgAlertaExamesExistentes +=" que pertence ao grupo ";
                msgAlertaExamesExistentes += inputCodigoGrupo.value.toString()+' - ';
                msgAlertaExamesExistentes += inputDescricaoGrupo.value.toString();
                msgAlertaExamesExistentes +=" já foi lançado.";

                if(examesExistentesGrid.length > 1){

                    msgAlertaExamesExistentes = "Os exames ";
                    for(var i = 0;i<examesExistentesGrid.length;i++){
                        if(i==examesExistentesGrid.length-2){
                            msgAlertaExamesExistentes += examesExistentesGrid[i].descricao.urlDecode().replaceAll('+', ' ')+' e ';
                        } else if(i<examesExistentesGrid.length-1) {
                            msgAlertaExamesExistentes += examesExistentesGrid[i].descricao.urlDecode().replaceAll('+', ' ')+', ';
                        } else {
                            msgAlertaExamesExistentes += examesExistentesGrid[i].descricao.urlDecode().replaceAll('+', ' ');
                        }
                    }

                    msgAlertaExamesExistentes +=" que pertencem ao grupo ";
                    msgAlertaExamesExistentes += inputCodigoGrupo.value.toString()+' - ';
                    msgAlertaExamesExistentes += inputDescricaoGrupo.value.toString();
                    msgAlertaExamesExistentes += " já foram lançados.";
                }

                alert(msgAlertaExamesExistentes);
            }

            let examesInativos = [];

            for (var i = 0; i < exames.length; i++) {

                if(!exames[i].exameativo){
                    examesInativos.push(exames[i].codigoexame+" - "+exames[i].descricao.urlDecode());
                    continue;
                }

                var data = new Date();
                var dd = String(data.getDate()).padStart(2, '0');
                var mm = String(data.getMonth() + 1).padStart(2, '0');
                var yyyy = data.getFullYear();

                today = dd + '/' + mm + '/' + yyyy;

                aVet = new Array();
                aVet[aVet.length] = exames[i].codigosetorexame;
                aVet[aVet.length] = exames[i].descricaolaboratorio.urlDecode();
                aVet[aVet.length] = exames[i].descricao.urlDecode();
                aVet[aVet.length] = today;
                aVet[aVet.length] = data.getHours() + ":" + data.getMinutes();
                aVet[aVet.length] = exames[i].entrega;
                aVet[aVet.length] = '0';
                aVet[aVet.length] = inputQuantidadeGrupo.value;
                aVet[aVet.length] = exames[i].obriga_peso;
                aVet[aVet.length] = exames[i].obriga_altura;
                aVet[aVet.length] = exames[i].obriga_volume_amostra;

                sStr = aVet.join('#');
                sStr = sStr.replace('##', '');

                F.exames.add(new Option(sStr, F.exames.length), null);

                js_AtualizaGrid();
            }

            if(examesInativos.length > 0 && examesInativos.length <= 2){

                let msgAlertaExames = "Os exames ";
                if(examesInativos.length == 1){
                    msgAlertaExames = "O exame ";
                }

                for(let a=0;a<examesInativos.length;a++){
                    msgAlertaExames += examesInativos[a];
                    if(a == examesInativos.length-2){
                        msgAlertaExames += " e ";
                    } else if(a<examesInativos.length-1){
                        msgAlertaExames += ", ";
                    }
                }

                if(examesInativos.length == 1){
                    msgAlertaExames += " encontra-se DESATIVADO e não será salvo na requisição!";
                } else {
                    msgAlertaExames += " encontram-se DESATIVADOS e não serão salvos na requisição!";
                }
                alert(msgAlertaExames);
            } else if(examesInativos.length > 0) {

                let msgAlertaExames = "Exames que encontram - se DESATIVADOS ";
                msgAlertaExames += "e, por isso, não serão salvos na requisição: \n ";
                for(let a=0;a<examesInativos.length;a++){
                    msgAlertaExames += "\n * "+examesInativos[a];
                }
                alert(msgAlertaExames);
            }

            inputCodigoGrupo.value = "";
            inputDescricaoGrupo.value = "";
            inputDescricaoGrupo.focus();
        }
    }

    function js_retornoBuscaExamesPorGrupoELaboratorio(oRetorno) {

        var oRetorno = JSON.parse(oRetorno.responseText);

        return oRetorno.exames;
    }

    function verificaExameAtivo() {
        var oParam = new Object();
        oParam.exec = 'verificaExameAtivo';
        oParam.codigoExame = $('la09_i_exame').value;

        return js_ajax(oParam, 'verificaExameAtivoRetorno', 'lab4_agendar.RPC.php', false);

    }

    function verificaExameAtivoRetorno(oRetorno) {
        var oRetorno = JSON.parse(oRetorno.responseText);

        return oRetorno.exameAtivo;
    }

    function js_IncluirExame() {
        if ( !['','0'].includes(document.getElementById('la09_i_codigo').value)) {

            if (document.getElementById('la08_c_descr').value.length === 0) {
                alert('A descrição do exame encontra- se em branco. Tente realizar o procedimento novamente!');
                return;
            }

            if (verificaExameAtivo() === false) {
                alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "exame_desativado"));
                return;
            }

            if (F.la21_d_data.value != '') {
                if (inputLiberaDoisExames.value == 2 && js_verificaexame(F.la09_i_codigo.value)) {
                    sStr = js_DadosExames();
                    F.exames.add(new Option(sStr, F.exames.length), null);

                    js_AtualizaGrid();

                    $('la09_i_exame').value = '';
                    $('la08_c_descr').value = '';
                    $('la09_i_codigo').value = '';

                    F.la08_c_descr.select();
                    F.la21_i_quantidade.value = 1;

                } else if(inputLiberaDoisExames.value == 2){
                    alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "exame_ja_lancado"));
                }
            } else {
                alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "informe_data"));
                const iframeData = document.getElementById('Janiframe_data_la21_d_data');
                iframeData.style.left = "45%";
                iframeData.style.top = "20%";
            }
        } else {
            alert('Nenhum laboratório selecionado!');
        }
    }

    function js_verificaexame(codigoExame) {
        tam = F.exames.length;

        if (tam == 0) {
            return true;
        }

        for (x = 0; x < tam; x++) {

            sStr = F.exames.options[x].text;
            aVet = sStr.split('#');

            if (aVet[0] == codigoExame) {
                return false;
            }
        }
        return true;
    }

    function js_excluirExames() {
        var aSelecionadosGrid = objGridExames.getSelection("array");
        var numeroRequisicao = document.getElementById('la22_i_codigo').value || "";
        var examesExcluidos = "";

        if (aSelecionadosGrid.length == 0) {
            alert("É necessario selecionar ao menos um exame para excluir!");
            return;
        }

        if (confirm("Você tem certeza que deseja apagar os exames selecionados?")) {
            aSelecionadosGrid.each(function (aItemSelecionado) {
                if (aItemSelecionado[12] == "10 - Requisitado" || aItemSelecionado[12] == "0 - Não Salva") {
                    var linha = document.getElementById("chkoGridExames" + aItemSelecionado[0]).parentElement.parentElement.rowIndex;
                    var titulo = document.getElementById("chkoGridExames" + aItemSelecionado[0]).parentElement.parentElement.childNodes[4].title;
                    var codigoExameRequisicao = F.exames[linha].text.split('#')[12];
                    if (aItemSelecionado[11] == "10 - Requisitado") {
                        if (examesExcluidos == "") {
                            examesExcluidos = codigoExameRequisicao;
                        } else {
                            examesExcluidos += "," + codigoExameRequisicao;
                        }
                    }
                    F.exames.remove(linha, titulo);
                    if (examesExcluidos != "") {
                        excluirExamesSelecionados(examesExcluidos);
                    }
                    js_AtualizaGrid();
                    alert("Exames excluidos com Sucesso.");
                } else {
                    alert("Não é possivel excluir o exame com esta situação: " + aItemSelecionado[12]);
                    return false;
                }
            });

        }
    }

    //fim funções do Grid

    //outras
    function js_loadLaboratorios(exame) {

        if ((exame != '') && (exame > 0)) {

            //Requisição ajax normal
            var oParam = new Object();
            oParam.exec = 'LoadLaboratorio';
            oParam.exame = exame;
            js_ajax(oParam, 'js_loadLaboratoriosReturn');

        } else {
            return false;
        }
    }

    function js_loadLaboratoriosReturn(oAjax) {

        oRetorno = lab_ajax.monta(oAjax);

        idSetorExame = oRetorno['codigos'][0];

        if (oRetorno.status == 1) {

            Tam = F.la09_i_codigo.length;

            for (x = Tam; x > 0; x--) {
                F.la09_i_codigo.remove(x - 1);
            }

            for (x = 0; x < oRetorno.codigos.length; x++) {
                F.la09_i_codigo.add(new Option(oRetorno.laboratorios[x].urlDecode(), oRetorno.codigos[x]), null);
            }

            js_LoadSetorExame(F.la09_i_codigo.value);

        } else {

            Tam = F.la09_i_codigo.length;

            for (x = Tam; x > 0; x--) {
                F.la09_i_codigo.remove(x - 1);
            }

            message_ajax(oRetorno.message);
        }
    }

    function js_LoadSetorExame(cod) {
        if ((cod != '') && (cod > 0)) {

            //Requisição ajax normal
            var oParam = new Object();
            oParam.exec = 'DadosExame';
            oParam.la09_i_codigo = cod;
            js_ajax(oParam, 'js_retornoDadosExame');

        } else {
            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "exame_nao_informado_setor"));
        }
    }

    function js_retornoDadosExame(oAjax) {

        oRetorno = lab_ajax.monta(oAjax);

        if (oRetorno.status == 1) {

            F.la08_i_dias.value = oRetorno.dias;
            F.la02_i_codigo.value = oRetorno.iLaboratorio;
            F.la02_c_descr.value = oRetorno.sLaboratorio.urlDecode();
            F.requisitos.value = oRetorno.sRequisitos;
            F.obrigaPeso.value = oRetorno.obrigaPeso;
            F.obrigaAltura.value = oRetorno.obrigaAltura;
            F.obrigaVolumeAmostra.value = oRetorno.obrigaVolumeAmostra;
        } else {
            message_ajax(oRetorno.message);
        }
    }

    function js_calendario() {

        if ($F(la21_i_quantidade) != '' && parseInt($F(la21_i_quantidade)) > 0) {
            show_calendariolaboratorio('la21_d_data', 'parent.js_HoraExame(); ', $F(la09_i_codigo), $F(la21_i_quantidade));
        } else {
            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "quantidade_invalida"));
        }
    }

    function js_HoraExame() {
        document.getElementById('lanc').focus();
        //retorno da função do calendario
    }


    function js_desbloqueiaBotao() {
        $('btnConfirma').setAttribute('disabled', 'disabled');
        if ($F('la22_i_cgs') != '' && $F('la38_i_medico') != '') {
            $('btnConfirma').removeAttribute('disabled');
        }
    }

    function js_validaCampos() {

        if ($F('la22_i_cgs') == '') {

            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "informe_cgs"));
            return false;
        }


        if ($F('la38_i_medico') == '') {

            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "informe_medico"));
            return false;
        }

        return validaCamposPreenchidos(F.exames);
    }

    function js_envia() {
        const requisicao = document.getElementById('la22_i_codigo').value || "";
        if (requisicao != "") {
            if (validaPossibilidadeAlteracao(requisicao)) {
                alert("Esta requisição possui exames já conferidos ou entregues, logo não é possível efetuar a alteração.");
                return false;
            }
        }

        if (!js_validaCampos()) {
            return false;
        }

        sStr = '';
        sUrgente = '';
        sSep = '';
        tam = F.exames.length;

        if (tam == 0) {

            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "lance_exame"));
            return false;
        }

        aSetores = new Array();
        aDatas = new Array();

        for (x = 0; x < tam; x++) {

            sStr += encodeURI(sSep + F.exames.options[x].text);
            mTmp = F.exames.options[x].text.split('#');
            aSetores[x] = mTmp[0];
            aDatas[x] = mTmp[3];

            if (document.getElementById('urgente' + x).checked == true) {
                sUrgente += sSep + '1';
            } else {
                sUrgente += sSep + '0';
            }
            sSep = '##';
        }

        // 1 => Utiliza controle Financeiro
        if (iControlaSaldoExames == 1) {
            // valida o saldo do controle financeiro
            // Envio o setorexame e a data do exame no formato brasileiro para a função
            if (!js_verificarSaldo(aSetores, aDatas)) {
                return false;
            }
        }

        // 2 => Utiliza controle Atendimento diário (Pacientes por Dia)
        if (iControlaSaldoExames == 2) {

            if (!validarLimiteDiario(aSetores, aDatas)) {
                return false;
            }
        }

        F.sStr.value = sStr;
        F.sUrgente.value = sUrgente;
        return true;

    }

    function js_lanca(iQual) {

        if (iQual == 1) {

            sNome = 'MEDICAMENTO';
            sTexto = F.la22_t_medicamento.value;
            sCampo = 'la22_t_medicamento';
        } else if (iQual == 2) {

            sNome = 'DIAGNÓSTICO';
            sTexto = F.la22_t_diagnostico.value;
            sCampo = 'la22_t_diagnostico';
        } else {

            sNome = 'OBSERVAÇÃO';
            sTexto = F.la22_t_observacao.value;
            sCampo = 'la22_t_observacao';
        }

        iTop = (screen.availHeight - 600) / 2;
        iLeft = (screen.availWidth - 600) / 2;
        js_OpenJanelaIframe("",
            "db_iframe_lab_box", "lab4_box001.php?sNome=" + sNome + "&sTexto=" + sTexto + "&sCampo=" + sCampo + "",
            "Pesquisa",
            true,
            iTop,
            iLeft,
            600,
            200);
    }

    function js_nova() {
        location.href = 'lab4_agendar001.php';
    }

    function js_comprovante() {

        var iExames = $('exameSelecionadoGrid').options.length;
        var aExamesSelecionados = [];

        for (var i = 0; i < iExames; i++) {
            aExamesSelecionados.push($('exameSelecionadoGrid').options[i].text.split('#')[0]);
        }

        if (aExamesSelecionados.length == 0) {

            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "lance_exame"));
            return false;
        }

        if ($F('la22_i_codigo') == '') {

            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "requisicao_sem_codigo"));
            return false;
        }

        var sUrl = 'lab2_comprovante001.php?sListaExames=' + aExamesSelecionados;
        sUrl += '&la22_i_codigo=' + $F('la22_i_codigo');

        jan = window.open(sUrl, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
        jan.moveTo(0, 0);
    }

    //Lookup's
    function js_pesquisala22_i_cgs(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe('',
                'db_iframe_cgs_und',
                'func_cgs_und.php?funcao_js=parent.js_mostracgs_und1|z01_i_cgsund|z01_v_nome|z01_v_sexo&nome_social=true',
                'Pesquise o Paciente',
                true);
        } else {

            if (document.form1.la22_i_cgs.value != '') {

                js_OpenJanelaIframe('',
                    'db_iframe_cgs_und',
                    'func_cgs_und.php?pesquisa_chave=' + document.form1.la22_i_cgs.value + '&funcao_js=parent.js_mostracgs_und&nome_social=true',
                    'Pesquise o Paciente',
                    false);
            } else {
                document.form1.z01_v_nome.value = '';
                apagaIdadeCompleta();
                js_desbloqueiaBotao();
            }
        }
    }

    function js_mostracgs_und(chave, erro, sexo) {

        document.form1.z01_v_nome.value = chave;

        if (erro == true) {

            document.form1.la22_i_cgs.focus();
            document.form1.la22_i_cgs.value = '';
        } else {
            js_tipo(sexo);
            document.getElementById('sexoPaciente').value = sexo;
            existeCgsUnd();
        }
        js_desbloqueiaBotao();
    }

    function js_mostracgs_und1(chave1, chave2, sexo) {
        document.form1.la22_i_cgs.value = chave1;
        document.form1.z01_v_nome.value = chave2;
        document.form1.la22_i_cgs.dispatchEvent(new Event('change'));
        js_tipo(sexo);
        document.getElementById('sexoPaciente').value = sexo;
        db_iframe_cgs_und.hide();
        js_desbloqueiaBotao();
        la22_i_cgs.focus();
    }

    function js_pesquisala09_i_exame(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('',
                'db_iframe_lab_exame',
                'func_lab_exame.php?funcao_js=parent.js_mostralab_exame1|la08_i_codigo|la08_c_descr&iVinculo=0' +
                '&iAtivo=1&sexoPaciente=' + document.getElementById('sexoPaciente').value +
                '&departamento='+'<?php echo db_getsession('DB_coddepto');?>',
                'Pesquisa exames',
                true);
        } else {
            if ($F('la09_i_exame') != '') {

                js_OpenJanelaIframe('',
                    'db_iframe_lab_exame',
                    'func_lab_exame.php?pesquisa_chave=' + document.form1.la09_i_exame.value + '&sexoPaciente=' + document.getElementById('sexoPaciente').value +
                    '&iAtivo=1&funcao_js=parent.js_mostralab_exame&iVinculo=0'+
                    '&departamento='+'<?php echo db_getsession('DB_coddepto');?>',
                    'Pesquisa',
                    false);
            } else {

                $('la08_c_descr').value = '';
                $('la09_i_exame').value = '';
                lanc.disabled = true;
                js_desbloqueiaBotao();
            }
        }
    }

    function js_mostralab_exame(chave, erro) {

        document.form1.la08_c_descr.value = chave;

        if (erro == true) {
            document.form1.la08_c_descr.value = "CÓDIGO DE EXAME INEXISTENTE";
            document.form1.la09_i_exame.focus();
            document.form1.la09_i_exame.value = '';
            lanc.disabled = true;
        } else {
            lanc.disabled = false;
            js_loadLaboratorios(document.form1.la09_i_exame.value);
        }
    }

    function js_mostralab_exame1(chave1, chave2) {
        lanc.disabled = false;
        document.form1.la09_i_exame.value = chave1;
        document.form1.la08_c_descr.value = chave2;
        js_loadLaboratorios(chave1);
        db_iframe_lab_exame.hide();
    }

    function js_validaDescricaoGrupo(){
        if (document.form1.la66_descricao.value.length === 0) {
            document.form1.la66_codigo.value = "";
        }
    }

    function js_validaDescricaoExame() {

        if (document.form1.la08_c_descr.value.length === 0
            || document.form1.la08_c_descr.value != document.form1.descricaoExameAutoComplete.value
        ) {
            lanc.disabled = true;
            document.form1.la09_i_exame.value = "";
        }
    }

    function js_pesquisala38_i_medico(mostra) {

        if (mostra == true) {

            js_OpenJanelaIframe('',
                'db_iframe_medicos',
                'func_medicos.php?funcao_js=parent.js_mostramedicos1|sd03_i_codigo|z01_nome' +
                '&lTodosTiposProf=true',
                'Pesquise o Profissional',
                true);
        } else {

            if (document.form1.la38_i_medico.value != '') {

                js_OpenJanelaIframe('',
                    'db_iframe_medicos',
                    'func_medicos.php?pesquisa_chave=' + document.form1.la38_i_medico.value +
                    '&funcao_js=parent.js_mostramedicos&lTodosTiposProf=true',
                    'Pesquise o Profissional',
                    false);
            } else {
                document.form1.z01_nome.value = '';
                js_desbloqueiaBotao();
            }
        }
    }

    function js_mostramedicos(chave, erro, chave2) {

        document.form1.z01_nome.value = chave;

        if (erro == true) {

            document.form1.la38_i_medico.focus();
            document.form1.la38_i_medico.value = '';
        }
        js_desbloqueiaBotao();
    }

    function js_mostramedicos1(chave1, chave2) {

        document.form1.la38_i_medico.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_medicos.hide();
        js_desbloqueiaBotao();
    }

    <?php
    if (isset($alinhasgrid)) {
        $linhasGrid = count($alinhasgrid);
        if ($linhasGrid > 0) {
            for ($x = 0; $x < $linhasGrid; $x++) {
                echo " F.exames.add(new Option('" . $alinhasgrid[$x] . "',F.exames.length),null);";
            }

            echo "js_AtualizaGrid(); ";
            echo "F.la21_d_data.value='';";
            echo "F.comprov.disabled=false;";
            echo "F.excluir.disabled=false;";
            if ($flagAutorizarExames) {
                echo "F.confirma.value='Salvar & Autorizar';";
            } else {
                echo "F.confirma.value='Salvar';";
            }

            if ($permitido && (isset($autorizado) && !$autorizado && $flagAutorizarExames == false)) {
                echo "F.autorizar.disabled=false;";
            }
        }
    }
    ?>


    function js_pesquisa() {

        js_OpenJanelaIframe('',
            'db_iframe_lab_requisicao',
            'func_lab_requisicao.php?iDepResitante=<?=$departamento?>&' +
            'funcao_js=parent.js_preenchepesquisa|la22_i_codigo', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {

        db_iframe_lab_requisicao.hide();
        if (validaPossibilidadeAlteracao(chave)) {
            alert("Esta requisição possui exames já conferidos ou entregues, logo não é possível efetuar a alteração.");
            return;
        }
        location.href = 'lab4_agendar001.php?retornopesquisa=1&chavepesquisa=' + chave;
    }

    function excluirExamesSelecionados(exames) {
        var oParam = new Object();
        oParam.exec = 'excluirExamesPorRequisicao';
        oParam.exames = exames;


        return js_ajax(oParam, 'js_retornoExcluirExamesSelecionados', 'lab4_agendar.RPC.php', false);
    }

    function js_retornoExcluirExamesSelecionados(oRetorno) {

        var oRetorno = JSON.parse(oRetorno.responseText);

        return oRetorno;
    }

    function validaPossibilidadeAlteracao(chave) {
        var oParam = new Object();
        oParam.exec = 'verificaSituacaoExamesRequisicao';
        oParam.requisicao = chave;
        oParam.situacao = '60 - Conferido';

        return js_ajax(oParam, 'js_retornoValidaPossibilidadeAlteracao', 'lab4_agendar.RPC.php', false);
    }

    function js_retornoValidaPossibilidadeAlteracao(oRetorno) {

        var oRetorno = JSON.parse(oRetorno.responseText);

        return oRetorno.possuiExamesMencionados;
    }

    function validaPossibilidadeExclusaoRequisicao() {
        const requisicao = document.getElementById('la22_i_codigo').value;
        var oParam = new Object();
        oParam.exec = 'verificaSituacaoExamesRequisicao';
        oParam.requisicao = requisicao;
        oParam.situacao = '20 - Autorizado';

        return js_ajax(oParam, 'js_retornoValidaPossibilidadeExclusaoRequisicao', 'lab4_agendar.RPC.php', false);
    }

    function js_retornoValidaPossibilidadeExclusaoRequisicao(oRetorno) {

        var oRetorno = JSON.parse(oRetorno.responseText);

        return oRetorno.possuiExamesMencionados;
    }

    function js_ajax(oParam, jsRetorno, sUrl, lAsync) {

        var mRetornoAjax;

        if (sUrl == undefined) {
            sUrl = 'lab4_agendar.RPC.php';
        }

        if (lAsync == undefined) {
            lAsync = false;
        }

        var oAjax = new Ajax.Request(sUrl,
            {
                method: 'post',
                asynchronous: lAsync,
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {

                    var evlJS = jsRetorno + '(oAjax);';
                    return mRetornoAjax = eval(evlJS);
                }
            }
        );
        return mRetornoAjax;
    }

    function js_preencheMedicoRecemCadastrado(iCod, sNome) {

        $('la38_i_medico').value = iCod;
        js_pesquisala38_i_medico(false);
    }

    function js_abreCadProf() {
        iTop = (screen.availHeight - 650) / 2;
        iLeft = (screen.availWidth - 800) / 2;

        if ($F('la38_i_medico') == '') {

            sGet = 's154_c_nome=' + $F('z01_nome') + '&sd03_i_tipo=2&lBotao=true';

            js_OpenJanelaIframe('', "db_iframe_cadprof", "sau1_sau_medicosforarede001.php?" + sGet,
                'Cadastro de Profissionais Fora da Rede', true, iTop, iLeft, 800, 300
            );
        } else {

            var oParam = new Object();
            oParam.exec = 'verificaForaRede';
            oParam.iMedico = $F('la38_i_medico');

            if (js_ajax(oParam, 'js_retornoVerificaForaRede', 'sau4_ambulatorial.RPC.php', false)) {

                sGet = 'chavepesquisa=' + $F('fa04_i_profissional') + '&lBotao=true';

                js_OpenJanelaIframe('', "db_iframe_cadprof", "sau1_sau_medicosforarede002.php?" + sGet,
                    'Cadastro de Profissionais Fora da Rede', true, iTop, iLeft, 800, 300
                );
            } else {
                alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "profissional_fora_rede"));
            }
        }
    }

    function js_verificarSaldo(aSetores, aDatas) {

        var oParam = new Object();
        oParam.exec = 'verificarSaldoExame';
        oParam.iSetorExame = aSetores;
        oParam.dData = aDatas;

        return js_ajax(oParam, 'js_retornoVerificarSaldo', 'lab4_laboratorio.RPC.php');
    }

    function js_retornoVerificarSaldo(oRetorno) {

        var oRetorno = JSON.parse(oRetorno.responseText);

        if (oRetorno.iStatus == 1) {

            if (oRetorno.lSaldoSuficiente == true) {
                return true;
            } else { // Saldo insuficiente

                if (oRetorno.lLiberarSemSaldo == true) {

                    var lLiberar = confirm(_M(MENSAGEM_FORMULARIO_REQUISICAO + "saldo_excedido"));

                    if (lLiberar) {
                        return true;
                    } else {
                        return false;
                    }
                }

                alert(oRetorno.sMessage.urlDecode());
                return false;
            }
        } else {

            alert(oRetorno.sMessage.urlDecode());
            return false;
        }
    }

    function js_autorizaExames() {
        if (flagAutorizarExames.value == '1') {
            objGridExames.checkAllRows();
        }

        var aSelecionadosGrid = objGridExames.getSelection("array");
        var aExames = [];
        var aSelecionados = [];

        if (aSelecionadosGrid.length == 0) {
            alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "selecione_exame"));
            return;
        }

        aSelecionadosGrid.each(function (aItemSelecionado) {
            if (aItemSelecionado[12] == "10 - Requisitado" || aItemSelecionado[12] == "0 - Não Salva") {
                aExames.push({sDataColeta: aItemSelecionado[5], iExame: aItemSelecionado[1]});
                aSelecionados.push(aItemSelecionado[1]);
            }
        });

        var oParametros = {};
        oParametros.exec = "autorizaExames";
        oParametros.iRequisicao = $F('la22_i_codigo');
        oParametros.aCodigosExames = aSelecionados;
        oParametros.aExames = aExames;
        oParametros.parametroAtivo = viewNumeroControleInterno.getParametroAtivo();
        oParametros.numeroControleInterno = viewNumeroControleInterno.getNumeroControleInterno();
        oParametros.ano = viewNumeroControleInterno.getAno();

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = js_retornoAutorizaExames;
        js_divCarregando(_M(MENSAGEM_FORMULARIO_REQUISICAO + "autorizando_exames"), "msgBoxA");
        new Ajax.Request('lab4_autorizacao.RPC.php', oRequest);
    }

    function js_retornoAutorizaExames(oAjax) {
        js_removeObj('msgBoxA');
        var oRetorno = JSON.parse(oAjax.responseText);
        var sugestoes_li = div_lista_la08_c_descr.children;

        if (flagAutorizarExames.value == '0' || parseInt(oRetorno.iStatus) == 2) {
            alert(oRetorno.sMensagem.urlDecode());
        }

        if (parseInt(oRetorno.iStatus) == 1) {
            location.href = "lab4_agendar001.php?autorizado=1&chavepesquisa=" + $F('la22_i_codigo');
        }

    }

    function js_validarQuantidade(oElement) {

        if (oElement.value == '' || parseInt(oElement.value) <= 0) {

            oElement.value = '';
            $('la21_d_data').value = '';
            $('la21_d_data_dia').value = '';
            $('la21_d_data_mes').value = '';
            $('la21_d_data_ano').value = '';
        }
    }


    (function () {
        const alertify = parent.document.getElementById('alertify');
        var alertifyExibindo = '';
        if (alertify) {
            alertifyExibindo = alertify.getAttribute('class') == 'alertify alertify-alert ';
        }

        //Table Exames Lançados
        gridExamesLancados = document.getElementById("oGridExamesbody");

        if (gridExamesLancados.childElementCount > 0 && alertifyExibindo == false) {
            //Esta validação serve para quando confirmar uma resquisição, colocar o foco no botão de nova requisição para o usuario dar sequencia na inclusão de novas requisições
            if (gridExamesLancados.childNodes[0].childNodes[0].getAttribute('class') == 'normal disabled') {
                nova.focus();
            }
        } else {
            //Esta validação é para quando efetuar o carregamento da pagina logo ao confirmar, não colocar o foco no paciente e retirar do ok  do alert.
            if (alertifyExibindo == false) {
                la22_i_cgs.focus();
            }
        }

        new AjaxRequest('lab4_cotasatendimento.RPC.php', {exec: 'verificaUsoCotas'}, function (oRetorno, lErro) {

            if (lErro) {
                alert(oRetorno.sMessage);
                return;
            }
            iControlaSaldoExames = oRetorno.tipo;
        }).setMessage(_M(MENSAGEM_FORMULARIO_REQUISICAO + 'verifica_uso_cotas')).execute();

        function selecionaPrimeiroElemento(elementoCodigo, elementoDescricao) {
            const list = document.getElementById(elementoDescricao);
            setTimeout(function () {
                    if (list.childElementCount > 0) {
                        var sugestoes = list.children[0];
                        var element_selected = 0;
                        if (sugestoes.children[element_selected].getAttribute('class') != 'selected') {
                            sugestoes.children[element_selected].setAttribute('class', 'selected');
                            elementoCodigo.value = sugestoes.children[element_selected].getAttribute('id');
                            var event = new Event('change');
                            elementoCodigo.dispatchEvent(event);
                        }
                    }
                }, 600
            );
        }

        function setaParaCima(lista, inputCodigo) {
            const pai = lista.firstChild;
            for (var i = 0; i < pai.childNodes.length; i++) {
                if (pai.childNodes[i].getAttribute('class') == "selected") {
                    pai.childNodes[i].removeAttribute('class');
                    if (pai.childNodes[i] == pai.lastChild) {
                        pai.firstChild.setAttribute('class', 'selected');
                        inputCodigo.value = pai.firstChild.getAttribute('id');
                        var event = new Event('change');
                        inputCodigo.dispatchEvent(event);
                    } else {
                        pai.childNodes[i + 1].setAttribute('class', 'selected');
                        inputCodigo.value = pai.childNodes[i + 1].getAttribute('id');
                        var event = new Event('change');
                        inputCodigo.dispatchEvent(event);
                    }
                    return;
                }
            }
        }

        function setaParaBaixo(lista, inputCodigo) {
            const pai = lista.firstChild;
            for (var i = 0; i < pai.childNodes.length; i++) {
                if (pai.childNodes[i].getAttribute('class') == "selected") {
                    pai.childNodes[i].removeAttribute('class');
                    if (pai.childNodes[i] == pai.firstChild) {
                        pai.lastChild.setAttribute('class', 'selected');
                        inputCodigo.value = pai.lastChild.getAttribute('id');
                        var event = new Event('change');
                        inputCodigo.dispatchEvent(event);
                        var event = new Event('change');
                    } else {
                        pai.childNodes[i - 1].setAttribute('class', 'selected');
                        inputCodigo.value = pai.childNodes[i - 1].getAttribute('id');
                        var event = new Event('change');
                        inputCodigo.dispatchEvent(event);
                    }
                    return;
                }
            }
        }

//40 = seta para baixo
//38 = seta para cima
//13 = enter

        la08_c_descr.addEventListener("keypress", function (event) {
            if (F.la21_d_data.value == '' && (event.which == 13 || (event.which == 13 && event.ctrlKey))) {
                F.la21_d_data.focus();
                alert(_M(MENSAGEM_FORMULARIO_REQUISICAO + "informe_data"));
                document.getElementById('ancora_calend').click();
                const iframeData = document.getElementById('Janiframe_data_la21_d_data');
                iframeData.style.left = "45%";
                iframeData.style.top = "20%";
                return;
            }
            if (event.ctrlKey && event.which == 13) {
                document.getElementById('btnConfirma').click();
                return;
            } else if (event.keyCode == 13) {
                var promise = new Promise(
                    (resolve, reject) => {
                        resolve(verificaExameAtivo());
                    }
                );

                promise.then(
                    (val) => {
                        if (val === true) {
                            document.getElementById("lanc").click();
                            document.getElementById("la09_i_exame").value = "";
                            document.getElementById("la08_c_descr").value = "";
                            document.getElementById("la09_i_codigo").value = "";
                        } else {
                            alert('Exame não encontra-se ativo.');
                        }
                    }
                );

                return;
            }
        });
        if (habilitaGrupo.value === "t") {
            inputDescricaoGrupo.addEventListener("keypress", function (event) {
                if (event.ctrlKey && event.which == 13) {
                    document.getElementById('btnConfirma').click();
                    return;
                } else if (event.keyCode == 13) {
                    document.getElementById("lancGrupo").click();
                    inputCodigoGrupo.value = "";
                    inputDescricaoGrupo.value = "";
                    return;
                }
            });

            inputDescricaoGrupo.addEventListener("keydown", function (event) {
                if (event.keyCode != 40 && event.keyCode != 38) {
                    selecionaPrimeiroElemento(inputCodigoGrupo, 'div_lista_descricaogrupo');
                }
                if (event.keyCode == 40) {
                    setaParaCima(div_lista_descricaogrupo, inputCodigoGrupo);
                }
                if (event.keyCode == 38) {
                    setaParaCima(div_lista_descricaogrupo, inputCodigoGrupo);
                }
            });
        }


        la08_c_descr.addEventListener("keydown", function (event) {
            if (event.keyCode != 40 && event.keyCode != 38) {
                selecionaPrimeiroElemento(la09_i_exame, 'div_lista_la08_c_descr');
            }
            if (event.keyCode == 40) {
                setaParaCima(div_lista_la08_c_descr, la09_i_exame);
            }
            if (event.keyCode == 38) {
                setaParaBaixo(div_lista_la08_c_descr, la09_i_exame)
            }
        });

        la08_c_descr.addEventListener("blur", function (event) {

            if (document.getElementById("la09_i_exame").value != "") {

                let iExame = document.getElementById("la09_i_exame").value;
                js_loadLaboratorios(iExame);
            }
        });

    })()

    /**
     * Controla o limete de atendimentos por dia por paciente
     * @param  {array} aSetorExame       Codigo do setorexame
     * @param  {array} aDatasAgendamento Data dos agendamentos de cada exame
     * @return {boolean}
     */
    function validarLimiteDiario(aSetorExame, aDatasAgendamento) {

        var iExames = aSetorExame.length;
        var aExamesAgendados = [];
        for (var i = 0; i < iExames; i++) {

            aExamesAgendados.push({
                iSetorExame: aSetorExame[i],
                dataAgenda: aDatasAgendamento[i]
            });
        }

        var oParametros = {
            'exec': 'alteraCotas',
            'aExamesAgendados': aExamesAgendados,
            'iCgs': $F('la22_i_cgs'),
            'lAdicionar': true
        }

        var lPermiteRequisicao = false;
        new AjaxRequest('lab4_cotasatendimento.RPC.php', oParametros, function (oRetorno, lErro) {

            if (lErro) {

                alert(oRetorno.sMessage);
                return lPermiteRequisicao = !lErro;
            }

            lPermiteRequisicao = true;

            /**
             * Valida se atingiu a cota
             */
            if (oRetorno.lAtingiuCotaDiaria) {

                var sMensagemConfirme = oRetorno.sMessage;
                sMensagemConfirme += "\nDeseja liberar essa requisição?";
                if (!confirm(sMensagemConfirme)) {
                    return lPermiteRequisicao = false;
                }

                /**
                 * Caso tenha atingido a cota e confirmado inclusão,
                 * força a inclusão de uma requisição incrementando assim as cotas
                 */
                oParametros.exec = 'forcarInclusaoCota';
                new AjaxRequest('lab4_cotasatendimento.RPC.php', oParametros, function (oRetornoForca, lErroForca) {

                    if (lErroForca) {

                        alert(oRetornoForca.sMessage);
                        return lPermiteRequisicao = !lErroForca;
                    }

                    return lPermiteRequisicao = true;
                }).asynchronous(false).execute();
            }

            return lPermiteRequisicao;
        }).asynchronous(false).setMessage(_M(MENSAGEM_FORMULARIO_REQUISICAO + 'validando_cota')).execute();

        return lPermiteRequisicao;
    }

    function atualizaCota() {
        if (validaPossibilidadeExclusaoRequisicao()) {
            alert("Esta requisição possui exames já autorizados, logo não é possivel efetuar a sua exclusão.");
            return false;
        }

        if (iControlaSaldoExames != 2) {
            return true;
        }

        var aExamesAgendados = [];
        var iQuantidadeExames = F.exames.length;
        for (x = 0; x < iQuantidadeExames; x++) {

            var aDadosExame = F.exames.options[x].text.split('#');
            aExamesAgendados.push({
                iSetorExame: aDadosExame[0],
                dataAgenda: aDadosExame[3]
            })
        }

        var oParametros = {
            'exec': 'alteraCotas',
            'aExamesAgendados': aExamesAgendados,
            'iCgs': $F('la22_i_cgs'),
            'lAdicionar': false
        }

        var lPermiteRequisicao = false;
        new AjaxRequest('lab4_cotasatendimento.RPC.php', oParametros, function (oRetorno, lErro) {

            if (lErro) {

                alert(oRetorno.sMessage);
                return lPermiteRequisicao = !lErro;
            }

            return lPermiteRequisicao = true
        }).asynchronous(false).setMessage(_M(MENSAGEM_FORMULARIO_REQUISICAO + 'validando_cota')).execute();

        return lPermiteRequisicao;
    }

    function ajustarDescricaoLaboratorioExame() {
        //Ajusta descriçãoes laboratório e exame
        for (x = 0; x < objGridExames.aRows.length; x++) {
            document.getElementById('oGridExamesrow' + x + 'cell1').setAttribute('title', document.getElementById('oGridExamesrow' + x + 'cell1').innerText);
            document.getElementById('oGridExamesrow' + x + 'cell1').style.textAlign = 'left';
            document.getElementById('oGridExamesrow' + x + 'cell1').innerText = document.getElementById('oGridExamesrow' + x + 'cell1').innerText.substr(0, 22) + '...';
            document.getElementById('oGridExamesrow' + x + 'cell2').setAttribute('title', document.getElementById('oGridExamesrow' + x + 'cell2').innerText);
            document.getElementById('oGridExamesrow' + x + 'cell2').style.textAlign = 'left';
            document.getElementById('oGridExamesrow' + x + 'cell2').innerText = document.getElementById('oGridExamesrow' + x + 'cell2').innerText.substr(0, 20) + '...';
        }
    }

    function ajustarPrimeiraLinhaGrid() {
        //Ajusta a primeira linha da tabela
        document.getElementById('oGridExamesrowoGridExames0').childNodes[0].style.width = '3%';
        var posicao = 2;
        for (x = 0; x < aWidth.length; x++) {
            document.getElementById('oGridExamesrowoGridExames0').childNodes[posicao].style.width = aWidth[x];
            posicao += 2;
        }
    }

    <?php
    if ($flagAutorizarExames && isset($chavepesquisa) && !isset($autorizado)) { ?>
    js_autorizaExames();
    <?php
    } ?>

    function validaInput(input, tamanho, event, virgula = false) {
        let valor = input.value;
        let tecla = event.key;

        if ((tecla === ',' || tecla === '.') && (valor.includes(',') || valor.includes('.'))) {
            alert('Decimal já digitado!');
            event.preventDefault();
            return;
        }

        if (tecla === '.' && virgula) {
            event.preventDefault();
            input.value = valor + ',';
        }

        if (isNaN(tecla)) {
            return;
        }

        if (valor.length + 1 == tamanho && !valor.includes('.') && !valor.includes(',') && tecla !== '.') {
            valor = valor.split('');
            valor.splice(tamanho - 1, 0, '.');
            string = valor.join('');

            if (virgula) {
                string = string.replace('.', ',');
            }
            input.value = string;
        }
    }

    function montaMensagemCamposObrigatorios(optionsExames) {
        const erros = [];
        const peso = document.getElementById('la22_peso').value;
        const altura = document.getElementById('la22_altura').value;
        const volumeAmostra = document.getElementById('la22_volumeamostra').value;
        let temErro = false;

        for (const optionExame of optionsExames) {
            const exame = optionExame.text.split('#');
            const isPesoObrigatorioEVazio = exame[8] === 't' && peso === '';
            const isAlturaObrigatoriaEVazia = exame[9] === 't' && altura === '';
            const isVolumeAmostraObrigatoriaEVazia = exame[10] === 't' && volumeAmostra === '';

            if (temErro && (isPesoObrigatorioEVazio || isAlturaObrigatoriaEVazia || isVolumeAmostraObrigatoriaEVazia)) {
                erros.push('\n');
                temErro = false;
            }

            if (isPesoObrigatorioEVazio) {
                erros.push(`Obrigatório informar o Peso para o exame "${exame[2]}".`)
                temErro = true;
            }

            if (isAlturaObrigatoriaEVazia) {
                erros.push(`Obrigatório informar a Altura para o exame "${exame[2]}".`)
                temErro = true;
            }

            if (isVolumeAmostraObrigatoriaEVazia) {
                erros.push(`Obrigatório informar o Volume da Amostra para o exame "${exame[2]}".`)
                temErro = true;
            }
        }

        if (erros.length > 0) {
            alert(erros.join("\n"));
            return false;
        }
        return true;
    }

    function validaCamposPreenchidos(exames) {
        return montaMensagemCamposObrigatorios(exames.options)
    }

    function camposObrigatoriosBloqueados() {
        const campoPeso = document.getElementById('la22_peso');
        const campoAltura = document.getElementById('la22_altura');
        const campoVolumeAmostra = document.getElementById('la22_volumeamostra');

        if (temExameColetado) {
            campoPeso.disabled = true;
            campoAltura.disabled = true;
            campoVolumeAmostra.disabled = true;
        }
    }

    function acrescentaZerosCamposInput() {
        let campoPeso = document.getElementById('la22_peso');

        if (campoPeso.value !== '') {
            campoPeso.value = new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 3}).format(parseFloat(campoPeso.value.replace(',', '.')));
        }
    }

    function existeCgsUnd() {
        let cgsUnd = document.getElementById('la22_i_cgs').value;

        if (cgsUnd !== '') {
            let parametros = {
                'sExecucao': 'buscarDadosCgs',
                'iCgs': cgsUnd
            };

            let dadosRequisicao = {};
            dadosRequisicao.method = 'post';
            dadosRequisicao.parameters = 'json=' + Object.toJSON(parametros);
            dadosRequisicao.onComplete = function (resposta) {

                let retorno = JSON.parse(resposta.responseText);

                retornaIdadePaciente(retorno);
            };

            const rpc = 'sau4_cgs.RPC.php';
            new Ajax.Request(rpc, dadosRequisicao);
            return;
        }

        return;
    }

    existeCgsUnd();

    function retornaIdadePaciente(retorno) {
        let idadeCompleta = retorno.oCgs.sIdadeCompleta.urlDecode();
        let idadeEmAnos = retorno.oCgs.idadeEmAnos;

        document.getElementById('linhaIdade').style.cssText = 'display: table-row;';

        $('linhaDaIdadeCompleta').innerHTML = `<p id="idadeCompleta" style="margin-right: 135px; font-weight: normal;" >Idade: <span style="color: red;">${idadeCompleta}</span></p>`;
        $('idadeEmAnos').value = idadeEmAnos;

        exibePrioridade(idadeEmAnos);
        return;
    }

    function apagaIdadeCompleta() {
        let tagIdadeCompleta = document.querySelector("#idadeCompleta");

        if (tagIdadeCompleta) {
            tagIdadeCompleta.remove();
        }
    }

    function exibePrioridade(idade) {
        $('boxPrioridades').style.display = 'inline';

        if (idade >= 60) {
            $('boxPrioridades').style.display = 'none';
        }
    }
</script>
