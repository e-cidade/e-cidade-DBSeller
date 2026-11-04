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

//MODULO: TFD
$oDaoCgsUnd->rotulo->label();
$oDaoTfdVeiculoDestino->rotulo->label();

$oRotulo = new rotulocampo;
$oRotulo->label("tf29_i_prontuario");
$oRotulo->label("tf30_i_encaminhamento");
$oRotulo->label("tf01_i_cgsund");
$oRotulo->label("s115_c_cartaosus");
$oRotulo->label("j13_codi");
$oRotulo->label("ve61_veicmotoristas");
$oRotulo->label("z01_nome");
$oRotulo->label("ve01_placa");
$oRotulo->label("tf17_c_localsaida");
$oRotulo->label("tf03_c_descr");

$utilizaGradeHorario = isset($oParametros->tf11_i_utilizagradehorario) && $oParametros->tf11_i_utilizagradehorario == '1';
?>
<form name="form1" method="post" action="">
    <div class="container">
        <fieldset class="form-container" style='width: 92%;'>
            <legend>Vincule o Passageiro ao Veículo de Saída</legend>
            <?php
            db_input('tf18_i_codigo', 10, $Itf18_i_codigo, true, 'hidden', $db_opcao, "");
            db_input('situacaoAgendamento', 10, '', true, 'hidden', $db_opcao, "");
            ?>
            <input id="codigoViagem" type="hidden">
            <table class="form-container" style="border: 0; width: 100%;">
                <tr>
                    <td nowrap title="<?= $Ttf18_i_veiculo ?>">
                        <?php
                        db_ancora($Ltf18_i_veiculo, "js_pesquisatf18_i_veiculo(true);", "");
                        ?>
                    </td>
                    <td nowrap colspan="2">
                        <?php
                        db_input(
                            'tf18_i_veiculo',
                            10,
                            $Itf18_i_veiculo,
                            true,
                            'text',
                            $db_opcaoNaoMudar,
                            " onchange='js_pesquisatf18_i_veiculo(false);'"
                        );
                        db_input('ve01_placa', 50, $Ive01_placa, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Tve61_veicmotoristas ?>">
                        <?php
                        db_ancora($Ltf18_i_motorista, "js_pesquisatf18_i_motorista(true);", $db_opcao);
                        ?>
                    </td>
                    <td nowrap colspan="2">
                        <?php
                        db_input(
                            'tf18_i_motorista',
                            10,
                            $Itf18_i_motorista,
                            true,
                            'text',
                            $db_opcao,
                            " onchange='js_pesquisatf18_i_motorista(false);'"
                        );
                        db_input('z01_nome', 50, $Iz01_nome, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Ttf18_i_destino ?>">
                        <?php
                        db_ancora('<b>Destino</b>', "js_pesquisatf18_i_destino(true);", $db_opcaoNaoMudar);
                        ?>
                    </td>
                    <td nowrap colspan="2">
                        <?php
                        db_input(
                            'tf18_i_destino',
                            10,
                            $Itf18_i_destino,
                            true,
                            'text',
                            $db_opcaoNaoMudar,
                            " onchange='js_pesquisatf18_i_destino(false);'"
                        );
                        db_input('tf03_c_descr', 50, $Itf03_c_descr, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Ttf18_d_datasaida ?>">
                        <?= $Ltf18_d_datasaida ?>
                    </td>
                    <td nowrap style="text-align: left">
                        <?php
                        $js = ' onchange="js_validaGrade();"';
                        $onClick = ' parent.js_validaGrade(); ';
                        if ((!isset($tf18_i_codigo) || $tf18_i_codigo == '') && $oParametros->tf11_obriga_hora_saida == 'f') {
                            $js = ' onchange="getVeiculosVinculados();"';
                            $onClick = ' parent.getVeiculosVinculados(); ';
                        }
                        db_inputdata(
                            'tf18_d_datasaida',
                            @$tf18_d_datasaida_dia,
                            @$tf18_d_datasaida_mes,
                            @$tf18_d_datasaida_ano,
                            true,
                            'text',
                            $db_opcaoNaoMudar,
                            $js,
                            '',
                            '',
                            $onClick
                        );
                        ?>
                    </td>
                    <td nowrap title="<?= $Ttf18_c_horasaida ?>" style="text-align: left">
                        <?php
                        echo $Ltf18_c_horasaida;
                        if ($oParametros->tf11_i_utilizagradehorario == 1) {
                            $db_opcaosaida = 3;
                            if ($db_opcaoNaoMudar == 3) {
                                $aX = array("$tf18_c_horasaida ## $tf18_c_localsaida ## $total" => $tf18_c_horasaida);
                            } else {
                                $aX = array('' => '');
                            }
                            db_select('tf18_c_horasaida', $aX, true, $db_opcao, " onchange=\"js_loadGridCgs();\"");
                        } else {
                            $dbOpcaoHora = $db_opcaoNaoMudar;
                            $js = "onKeyUp=\"mascara_hora(this.value, 'tf18_c_horasaida',  event);\" ";
                            if ($oParametros->tf11_obriga_hora_saida == 'f' && $dbOpcaoHora === 3 && $tf18_c_horasaida == '') {
                                $dbOpcaoHora = 1;
                            }
                            if ($oParametros->tf11_obriga_hora_saida == 't') {
                                $js .= ' onchange="js_validaGrade();"';
                            }
                            $db_opcaosaida = $db_opcao;
                            db_input('tf18_c_horasaida', 10, $Itf18_c_horasaida, true, 'text', $dbOpcaoHora, $js);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        Local da Saída:
                    </td>
                    <td nowrap colspan="2">
                        <?php
                        db_input('localsaida', 64, '', true, 'text', $db_opcaosaida, '');
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Ttf18_d_dataretorno ?>">
                        <?= $Ltf18_d_dataretorno ?>
                    </td>
                    <td nowrap style="text-align: left">
                        <?php
                        db_inputdata(
                            'tf18_d_dataretorno',
                            @$tf18_d_dataretorno_dia,
                            @$tf18_d_dataretorno_mes,
                            @$tf18_d_dataretorno_ano,
                            true,
                            'text',
                            $db_opcao
                        );
                        ?>
                    </td>
                    <td nowrap title="<?= $Ttf18_c_horaretorno ?>" style="text-align: left">
                        <?php
                        echo $Ltf18_c_horaretorno;

                        db_input(
                            'tf18_c_horaretorno',
                            10,
                            $Itf18_c_horaretorno,
                            true,
                            'text',
                            $db_opcao,
                            "onKeyUp=\"mascara_hora(this.value, 'tf18_c_horaretorno',  event);\" "
                        );
                        ?>
                        &nbsp;&nbsp;
                        <button
                            name="retorno"
                            type="button"
                            id="retorno"
                            onclick="js_retorno();"
                            <?= (isset($tf18_i_codigo) && !empty($tf18_i_codigo) ? '' : 'disabled') ?>
                            style="color: <?= (empty($tf18_i_codigo) ? 'gray' : '') ?>"
                        >
                            <i class="fas fa-reply"></i>
                            Retorno
                        </button>
                    </td>
                </tr>
            </table>

            <table class="form-container">
                <tr>
                    <td>
                        <fieldset style='width: 92%;'>
                            <?php
                            if ($oParametros->tf11_i_utilizagradehorario == 2) {
                                echo '<legend><b>Lotação do Veiculo</b></legend>';
                            } else {
                                echo '<legend><b>Lotação do Dia</b></legend>';
                            }
                            ?>
                            <table class="form-container" style="border: 0; width: 90%;">
                                <tr>
                                    <td nowrap>
                                        <b>Total lugares: </b>
                                    </td>
                                    <td nowrap>
                                        <?php
                                        if (!isset($total)) {
                                            $total = 0;
                                        }
                                        db_input('total', 1, "", true, 'text', 3, '');
                                        ?>
                                    </td>
                                    <td nowrap>
                                        <b> - Pacientes: </b>
                                    </td>
                                    <td nowrap>
                                        <?php
                                        if (!isset($numPac)) {
                                            $numPac = 0;
                                        }
                                        db_input('numPac', 1, "", true, 'text', 3, '');
                                        ?>
                                    </td>
                                    <td nowrap>
                                        <b> - Acompanhantes: </b>
                                    </td>
                                    <td nowrap>
                                        <?php
                                        if (!isset($numAcomp)) {
                                            $numAcomp = 0;
                                        }
                                        db_input('numAcomp', 1, "", true, 'text', 3, '');
                                        ?>
                                    </td>
                                    <td nowrap>
                                        <b> + Crianças de colo: </b>
                                    </td>
                                    <td nowrap>
                                        <?php
                                        if (!isset($numColo)) {
                                            $numColo = 0;
                                        }
                                        db_input('numColo', 1, "", true, 'text', 3, '');
                                        ?>
                                    </td>
                                    <td nowrap>
                                        <b> = Lugares livres: </b>
                                    </td>
                                    <td nowrap>
                                        <?php
                                        if (!isset($livre)) {
                                            $livre = 0;
                                        }
                                        db_input('livre', 1, "", true, 'text', 3, ''); ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:center">
                        <button name="pesquisar" type="button" id="pesquisar" onclick="js_pesquisa();">
                            <i class="fas fa-search"></i>
                            Pesquisar
                        </button>
                        <button name="limpar" type="button" id="limpar" onclick="js_limpar()">
                            <i class="fas fa-eraser"></i>
                            Limpar
                        </button>
                    </td>
                </tr>

            </table>

        </fieldset>
    </div>

    <div class="container">
        <fieldset style='width: 1000px;'>
            <legend>Pacientes</legend>
            <div id="status-microarea" class="alert-danger" style="text-align: center;" role="alert" hidden>
                Pacientes sem cadastro em uma microárea!
            </div>
            <br>
            <div id='grid_pedidostfd' style='width: 100%;'></div>
            <input name="numero" id="numero" type="hidden" value="0">
            <input name="sPassageirosSelecionados" id="sPassageirosSelecionados" type="hidden" value="">
            <input name="sPassageirosCGS" id="sPassageirosCGS" type="hidden" value="">
        </fieldset>
        <button
            name="<?= ((isset($tf18_i_codigo)) && ($tf18_i_codigo != '')) ? 'alterar' : 'confirmar' ?>"
            type="submit"
            id="confirmar"
            onclick="return js_montastr();"
        >
            <i class="fas fa-check-double"></i>
            Confirmar
        </button>

        <button
            name="lista"
            type="button"
            id="lista"
            onclick="js_listaDaer();"
            <?= (isset($tf18_i_codigo) && !empty($tf18_i_codigo) ? '' : 'disabled') ?>
            style="color: <?= (empty($tf18_i_codigo) ? 'gray' : '') ?>"
        >
            <i class="fas fa-print"></i>
            Lista DAER
        </button>

        <button
            name="lista"
            type="button"
            id="botaoCancelar"
            onclick="abreJanelaCancelamentoVeiculo()"
        >
            <i class="fas fa-ban"></i>
            <?= ((!empty($situacaoAgendamento) && mb_strpos($situacaoAgendamento, 'CANCELADO')) ? 'Visualizar Cancelamento' : 'Cancelar Veículo') ?>
        </button>
    </div>

    <div id="janelaCancelamento" style="padding: 15px 40px; display: none">
        <fieldset style="margin-bottom: 10px">
            <legend>Cancelar Veículo</legend>
            <div style="font-weight: bold">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px">
                    <div>
                        <label>Data Saída:</label>
                        <input type="text" id="dataSaida">
                    </div>

                    <div>
                        <label>Login:</label>
                        <input type="text" class="readonly" value="<?= $_SESSION['DB_login'] ?>" readonly id="loginUsuario">
                    </div>

                    <div style="margin-right: 25px;">
                        <label>Data/Hora:</label>
                        <input type="text" id="dataHoraCancelamento">
                    </div>
                </div>

                <div style="margin-bottom: 10px">
                    <label>Código do Agendamento:</label>
                    <input type="text" id="codigoAgendamento">
                </div>

                <div>
                    <div style="display: flex; flex-direction: column;">
                        <label>Motivo Cancelamento:</label>
                        <textarea id="motivoCancelamento" rows="3" cols="65" maxlength="120" style="font-weight:normal">
                        </textarea>
                    </div>
                </div>
            </div>
        </fieldset>

       <div style="display: flex; justify-content: center; column-gap: 5px">
           <button
               name="confirmarCancelamento"
               id="confirmarCancelamento"
               onclick="confirmaCancelamento()"
           >
               <i class="fas fa-check-double"></i>
               Confirmar
           </button>

           <button
               onclick="fechaJanelaCancelamentoVeiculo()"
           >
               <i class="fas fa-window-close"></i>
               Fechar
           </button>


       </div>
    </div>

</form>
<div id="modalVeiculosVinculados" style="padding: 0;">
    <div class="container" style="margin-top: 0;">
        <div class="alert alert-info" style="text-align: left; padding: 0;">
            <ul>
                <li>Clique em <kbd><i class="fas fa-edit"></i></kbd> caso deseje alterar um registro.</li>
                <li>Clique em <kbd><i class="fas fa-file"></i> Novo Vínculo</kbd> caso deseje iniciar um novo.</li>
            </ul>
        </div>
        <fieldset>
            <legend>Transportes vinculados na data</legend>
            <table id="table-veiculos-vinculados"
                   class="table table-sm">
            </table>
        </fieldset>
        <button onclick="novoVinculo();">
            <i class="fas fa-file"></i>
            Novo Vínculo
        </button>
    </div>
</div>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/saude/ValidaCgs.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script>
$.noConflict();
const utilizaGradeHorario = <?= $utilizaGradeHorario ? 'true' : 'false' ?>;
oDBGridPedidostfd = js_criaDataGrid();
sUrl = 'tfd4_pedidotfd.RPC.php';
$('tf18_d_datasaida').onblur = '';
let obrigaHoraSaida = true;
const divAlert = document.getElementById('status-microarea');
const wndVeiculosVinculados = new windowAux('wndVeiculosVinculados', 'Transportes vinculados na data', 800, 500);
wndVeiculosVinculados.setContent(document.getElementById('modalVeiculosVinculados'));
wndVeiculosVinculados.setShutDownFunction(fecharWindowAux);

const tableVeiculosVinculados = jQuery('#table-veiculos-vinculados');
const janelaCancelamento = new windowAux('cancelamentoVeiculo', 'Cancelamento de Veículo', 950, 300);

<?php
if ((isset($tf18_i_codigo)) && ($tf18_i_codigo != '')) {
    echo 'js_validaGrade();';
}
?>
let validaMicroarea = false;

window.onload = () => {
    if (utilizaGradeHorario) {
        $('tf18_c_horasaida').style.width = '100px';
    }
    cancelarAtivo();
}

async function getParametros() {
    const formData = new FormData();
    formData.append('acao', 'getParametros');
    const response = await HttpClient.post('sau4_tfd.RPC.php', {body: formData});
    if (response.erro) {
        alert(response.mensagem);
        return;
    }
    obrigaHoraSaida = response.utilizaGradeHorario || response.obrigaHoraSaida;
}

function js_ajax(oParam, jsRetorno) {
    var objAjax = new Ajax.Request(
        sUrl,
        {
            method: 'post',
            asynchronous: false,
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: function (objAjax) {
                var evlJS = jsRetorno + '(objAjax);';
                return eval(evlJS);
            }
        }
    );
}

/**** Bloco de funções botão retorno (início) */
function js_retorno() {
    sChave = '&tf31_i_veiculodestino=' + $F('tf18_i_codigo');

    if ($F('tf18_i_codigo') != '') {
        js_OpenJanelaIframe('', 'db_iframe_retorno', 'tfd4_tfd_passageiroretorno001.php?' + sChave, 'Retorno', true);
    }
}

/* Bloco de funções botão Retorno (fim) ****/


function js_listaDaer() {
    if ($F('tf18_i_destino') == '') {
        alert('Informe o destino.');
        return false;
    }
    if ($F('tf18_i_veiculo') == '') {
        alert('Informe o veículo.');
        return false;
    }

    if ($F('tf18_d_datasaida') == '') {
        alert('Informe a data de saída.');
        return false;
    }

    if ($F('tf18_c_horasaida') == null || $F('tf18_c_horasaida') == '') {
        alert('Informe a hora de saída');
        return false;
    }

    iCodVeiculo = 'codveiculo=' + $F('tf18_i_veiculo');
    iCodDestino = '&coddestino=' + $F('tf18_i_destino');
    sDatasaida = '&datasaida=' + $F('tf18_d_datasaida');
    iHora = '&hora=' + $F('tf18_c_horasaida');

    sChavePesquisa = iCodVeiculo + iCodDestino + sDatasaida + iHora;

    if ($F('tf18_i_codigo') != undefined && $F('tf18_i_codigo') != '') {
        oJan = window.open('tfd2_listapassageirodaer002.php?' + sChavePesquisa, '',
            'width=' + (screen.availWidth - 5) + ',height=' +
            (screen.availHeight - 40) + ',scrollbars=1,location=0 '
        );
        oJan.moveTo(0, 0);
    } else {
        alert('Código para geração do relatório não informado.');
        return false;
    }
}

function js_limparInformacoes() {
    oDBGridPedidostfd.clearAll(true);
    oDBGridPedidostfd.renderRows();

    $('numero').value = 0;
    $('retorno').disabled = true;
    $('lista').disabled = true;
    $('confirmar').name = 'confirmar';
    if ($('tf18_i_veiculo').value != '' && <?php echo $oParametros->tf11_i_utilizagradehorario; ?> != '2') {
        $('total').value = 0;
        $('numPac').value = 0;
        $('numAcomp').value = 0;
        $('numColo').value = 0;
        $('livre').value = 0;
    }
    $('tf18_i_codigo').value = '';
    $('tf18_d_dataretorno').value = '';
    $('tf18_c_horaretorno').value = '';
}

/**** Bloco de funções do grid início */

function js_criaDataGrid() {
    oDBGrid = new DBGrid('grid_pedidostfd');
    oDBGrid.nameInstance = 'oDBGridPedidostfd';
    oDBGrid.hasTotalizador = false;
    oDBGrid.setCellWidth(new Array('4%', '10%', '35%', '33%', '5%', '7%', '7%'));
    oDBGrid.setHeight(120);
    oDBGrid.allowSelectColumns(false);

    var aHeader = new Array();
    aHeader[0] = 'TFD';
    aHeader[1] = 'CGS';
    aHeader[2] = 'Paciente';
    aHeader[3] = 'Prestadora';
    aHeader[4] = '<input type="button" id="marcarTodos" onclick="js_marcarTodos();" value="M">';
    aHeader[5] = 'Fica';
    aHeader[6] = 'Colo';
    oDBGrid.setHeader(aHeader);

    var aAligns = new Array();
    aAligns[0] = 'center';
    aAligns[1] = 'center';
    aAligns[2] = 'left';
    aAligns[3] = 'left';
    aAligns[4] = 'center';
    aAligns[5] = 'center';
    aAligns[6] = 'center';

    oDBGrid.setCellAlign(aAligns);
    oDBGrid.show($('grid_pedidostfd'));
    oDBGrid.clearAll(true);

    return oDBGrid;
}

/****** fim bloco do Grid **/


/*
 * ==========================================================================
 * -> Percorre o Grid marcando ou desmarcando todos os Pacientes de uma vez
 * ==========================================================================
*/
function js_marcarTodos() {
    oElementos = document.getElementsByName('ckbox');
    if (document.getElementById('marcarTodos').value == 'M') {
        for (i = 0; i < oElementos.length; i++) {
            if (!oElementos[i].checked) {
                oElementos[i].click();
            }
        }
        document.getElementById('marcarTodos').value = 'D';
    } else {
        for (iCont = 0; iCont < oElementos.length; iCont++) {
            if (oElementos[iCont].checked) {
                oElementos[iCont].click();
            }
        }
        document.getElementById('marcarTodos').value = 'M';
    }
}

/*
 * ====================================================================
 * -> Função para abrir a func_veiculosalt
 *
 * -> A variavel iParam com o valor 1 deve ser setada para que a func
 *    retorne a capacidade do veiculo junto na função, pois sem ela
 *    o retorno traz apenas a placa.
 * ====================================================================
 */
function js_pesquisatf18_i_veiculo(mostra) {
    if (mostra == true) {
        js_OpenJanelaIframe('', 'db_iframe_veiculos', 'func_veiculosalt.php?funcao_js=parent.' +
            'js_mostraveiculo1|ve01_codigo|ve01_quantcapacidad|ve01_placa', 'Pesquisa', true
        );
    } else {
        if (document.form1.tf18_i_veiculo.value != '') {
            js_OpenJanelaIframe('', 'db_iframe_veiculos', 'func_veiculosalt.php?pesquisa_chave=' +
                document.form1.tf18_i_veiculo.value + '&funcao_js=parent.js_mostraveiculo&iParam=1',
                'Pesquisa', false
            );
        } else {
            document.form1.ve01_placa.value = '';
            js_limparInformacoes();
        }
    }
}

/*
 * ================================================================
 * -> Seta os valores do veiculo e tenta dar um load no Grid
 *    obs: o load só é efetuado se todas as variaveis necessarias
 *         para  a busca das informações estiverem setadas.
 * ================================================================
 */
function js_mostraveiculo(chave, capacidade, erro) {
    document.form1.ve01_placa.value = chave;
    if (!utilizaGradeHorario) {
        document.form1.total.value = capacidade;
        document.form1.livre.value = capacidade;
    }
    if (erro == true) {
        document.form1.tf18_i_veiculo.focus();
        document.form1.tf18_i_veiculo.value = '';
        js_limparInformacoes();
    } else {
        js_loadGridCgs();
    }
}

/*
 * ================================================================================
 * -> Seta os valores para veiculo e depois tenta dar um load no grid.
 *    obs: Se o total de pessoar a viajar não for gerenciado pela grade de horarios
 *         então a capacidade passa a ser gerenciada pela capacidade do veiculo.
 *
 *      tf11_i_utilizagradehorario = 1 (gerenciado pela grade de horarios)
 *      tf11_i_utilizagradehorario = 2 (gerenciado pela capacidade do veiculo)
 *
 * ================================================================================
 */
function js_mostraveiculo1(chave1, chave2, chave3) {
    js_limparInformacoes();
    document.form1.tf18_i_veiculo.value = chave1;
    if (!utilizaGradeHorario) {
        document.form1.total.value = chave2;
        document.form1.livre.value = chave2;
    }
    document.form1.ve01_placa.value = chave3;
    db_iframe_veiculos.hide();
    js_loadGridCgs();
}

function js_pesquisatf18_i_motorista(mostra) {
    if (mostra == true) {
        js_OpenJanelaIframe('', 'db_iframe_veicmotoristas', 'func_veicmotoristasalt.php?' +
            'funcao_js=parent.js_mostramotorista1|ve05_codigo|z01_nome',
            'Pesquisa', true
        );
    } else {
        if (document.form1.tf18_i_motorista.value != '') {
            js_OpenJanelaIframe('', 'db_iframe_veicmotoristas', 'func_veicmotoristasalt.php?pesquisa_chave=' +
                document.form1.tf18_i_motorista.value + '&funcao_js=parent.js_mostramotorista',
                'Pesquisa', false
            );
        } else {
            document.form1.z01_nome.value = '';
        }
    }
}

function js_mostramotorista(chave, erro) {
    document.form1.z01_nome.value = chave;
    if (erro == true) {
        document.form1.tf18_i_motorista.focus();
        document.form1.tf18_i_motorista.value = '';
    }
}

function js_mostramotorista1(chave1, chave2) {
    document.form1.tf18_i_motorista.value = chave1;
    document.form1.z01_nome.value = chave2;
    db_iframe_veicmotoristas.hide();
}

function js_pesquisatf18_i_destino(mostra) {
    if (mostra == true) {
        js_OpenJanelaIframe('', 'db_iframe_destino', 'func_tfd_destino.php?funcao_js=parent.js_mostradestino1' +
            '|tf03_i_codigo|tf03_c_descr&chave_validade=true', 'Pesquisa', true
        );
    } else {
        if (document.form1.tf18_i_destino.value != '') {
            js_OpenJanelaIframe('', 'db_iframe_destino', 'func_tfd_destino.php?pesquisa_chave=' +
                document.form1.tf18_i_destino.value + '&funcao_js=parent.js_mostradestino&chave_validade=true',
                'Pesquisa', false
            );
        } else {
            document.form1.tf03_c_descr.value = '';
            js_limparInformacoes();
        }
    }
}

function js_mostradestino(chave, erro) {
    document.form1.tf03_c_descr.value = chave;
    if (erro == true) {
        document.form1.tf18_i_destino.focus();
        document.form1.tf18_i_destino.value = '';
        js_limparInformacoes();
    } else {
        js_loadGridCgs();
    }
}

function js_mostradestino1(chave1, chave2) {
    document.form1.tf18_i_destino.value = chave1;
    document.form1.tf03_c_descr.value = chave2;
    db_iframe_destino.hide();
    js_loadGridCgs();
}


function js_validaGrade() {
    if (js_validaDbData($('tf18_d_datasaida'))) {
        if (utilizaGradeHorario && <?=$db_opcaoNaoMudar?> != 3) {
            js_getHorariosData();
        } else {
            js_loadGridCgs();
        }
    } else {
        js_limparInformacoes();
    }
}

function js_getHorariosData() {
    if ($F('tf18_d_datasaida') == '') {
        return false;
    }
    if ($F('tf18_i_destino') == '') {
        return false;
    }

    var oParam = new Object();
    oParam.exec = 'getHorariosData';
    oParam.dData = $F('tf18_d_datasaida');
    oParam.iDestino = $F('tf18_i_destino');
    js_ajax(oParam, 'js_retornogetHorariosData');

}

function js_retornogetHorariosData(oRetorno) {
    iTam = $('tf18_c_horasaida').options.length;
    for (iCont = 0; iCont < iTam; iCont++) { // for para remover todos os options
        $('tf18_c_horasaida').options[0] = null;
    }

    oRetorno = JSON.parse(oRetorno.responseText);

    if (oRetorno.iStatus == 1) {
        iCont = 0;
        oRetorno.oHorarios.each(function (oHorario) {
                $('tf18_c_horasaida').options[iCont] = new Option(
                    oHorario.sHora.urlDecode(),
                    `${oHorario.sHora.urlDecode()} ## ${oHorario.sLocalSaida.urlDecode()} ## ${oHorario.iLotacao}`
                );
                iCont++;
            }
        );

        js_loadGridCgs();
        js_selecionaHorario();
        js_localSaida();
    } else {
        alert('Nao foi possível encontrar horários de saída para a data indicada.');
    }
}

function js_selecionaHorario() {
    oSel = $('tf18_c_horasaida');
    for (iCont = 0; iCont < oSel.length; iCont++) {
        if (oSel.options[iCont].innerHTML == '<?=isset($tf18_c_horasaida) && !empty($tf18_c_horasaida) ? $tf18_c_horasaida : -1?>') {
            oSel.options[iCont].selected = true;
            break;
        }
    }
}

function js_localSaida() {
    aLocal = $F('tf18_c_horasaida').split(' ## ');
    if (utilizaGradeHorario) {
        $('tf18_c_localsaida').value = aLocal[1];
        document.form1.total.value = aLocal[2];
    }
    document.form1.livre.value = parseInt(document.form1.total.value, 10) -
        (parseInt(document.form1.numAcomp.value, 10) +
            parseInt(document.form1.numPac.value, 10)) +
        parseInt(document.form1.numColo.value, 10);
}

async function js_loadGridCgs() {
    await getParametros();
    const validaCgs = new ValidaCgs();
    iCodigo = $F('tf18_i_codigo');
    js_limparInformacoes();

    if (verificaAgendamentoCancelado()) {
        await buscaDadosViagemCancelada(iCodigo)
        return;
    }

    if ($F('tf18_i_veiculo') == '') {
        return false;
    }
    if ($F('tf18_i_destino') == '') {
        return false;
    }
    if ($F('tf18_d_datasaida') == '') {
        return false;
    }
    if (obrigaHoraSaida && $F('tf18_c_horasaida') == '') {
        return false;
    }

    var oParam = new Object();
    oParam.exec = 'getCgsDataSaida';
    aVet = $F('tf18_d_datasaida').split('/');
    oParam.sData = aVet[2] + '-' + aVet[1] + '-' + aVet[0];
    oParam.iCodigo = iCodigo;

    oParam.sHora = $F('tf18_c_horasaida');
    if (utilizaGradeHorario) {
        oParam.sHora = $('tf18_c_horasaida').options[$('tf18_c_horasaida').selectedIndex].text;
    }
    oParam.iDestino = $F('tf18_i_destino');
    oParam.iVeiculo = $F('tf18_i_veiculo');

    validaCgs.getParametros().then(response => {
        validaMicroarea = response.s103_validamicroarea;
        js_ajax(oParam, 'js_retornoGridCgs');
    });

}

function js_retornoGridCgs(oRetorno, cancelado = false) {

    if (!cancelado) {
        oRetorno = JSON.parse(oRetorno.responseText);
    }
    if (oRetorno.iStatus == 1) {
        divAlert.hidden = 'hidden';
        for (iCont = 0; iCont < oRetorno.aListaCgs.length; iCont++) {
            var aLinha = new Array();
            aLinha[0] = oRetorno.aListaCgs[iCont].tf01_i_codigo;
            aLinha[1] = oRetorno.aListaCgs[iCont].z01_i_cgsund;
            aLinha[2] = oRetorno.aListaCgs[iCont].tipo == 2 ? '+AC - ' : '';
            aLinha[2] += oRetorno.aListaCgs[iCont].z01_v_nome.urlDecode();
            aLinha[3] = oRetorno.aListaCgs[iCont].z01_nome.urlDecode();

            sChecado = oRetorno.aListaCgs[iCont].vinculado == 1 ? 'checked' : '';
            sDisabled = oRetorno.aListaCgs[iCont].tipo == 1 ? '' : ' disabled';

            aLinha[4] = '<input type="checkbox" name="ckbox" id="check' + iCont + '" ';
            aLinha[4] += ' value="' + oRetorno.aListaCgs[iCont].tipo + '##';
            aLinha[4] += oRetorno.aListaCgs[iCont].tf01_i_codigo + '##' + oRetorno.aListaCgs[iCont].z01_i_cgsund;
            aLinha[4] += '##' + iCont + '" ' + sDisabled;
            if (oRetorno.aListaCgs[iCont].tipo == 1) {
                aLinha[4] += ' onclick="js_calcmarcar(this, ' + oRetorno.aListaCgs[iCont].tipo + ')" ';
            } else {
                aLinha[4] += ' onclick="js_calcmarcar(this, ' + oRetorno.aListaCgs[iCont].tipo + ');';
                aLinha[4] += ' this.disabled = true;" ';
            }
            aLinha[4] += sChecado + ' >';

            sChecado = oRetorno.aListaCgs[iCont].tf19_i_fica == 1 ? 'checked' : '';
            sDisabled = oRetorno.aListaCgs[iCont].vinculado == 1 ? '' : ' disabled';

            aLinha[5] = '<input type="checkbox" name="ckboxfica" id="checkfica' + iCont + '" ';
            aLinha[5] += ' value="' + oRetorno.aListaCgs[iCont].z01_i_cgsund + '" ' + sChecado + sDisabled + '>';

            sChecado = oRetorno.aListaCgs[iCont].tf19_i_colo == 1 ? 'checked' : '';

            aLinha[6] = '<input type="checkbox" name="ckboxcolo" id="checkcolo' + iCont + '" ';
            aLinha[6] += ' value="' + oRetorno.aListaCgs[iCont].z01_i_cgsund + '" ';
            aLinha[6] += ' onclick="js_calcmarcar2(this, ' + oRetorno.aListaCgs[iCont].tipo + ')" ' + sChecado + sDisabled + ' >';
            let classe = null;
            if (oRetorno.aListaCgs[iCont].z01_i_familiamicroarea == null && validaMicroarea) {
                classe = 'error';
                divAlert.hidden = '';
            }
            oDBGridPedidostfd.addRow(aLinha, false, false, false, classe);

        }

        document.form1.numero.value = oRetorno.aListaCgs.length;
        oDBGridPedidostfd.renderRows();

        // Se trouxe passageiros vinculados a algum veículo, entra em modo alteração
        if (oRetorno.iVeiculoDestino != '') {
            $('tf18_i_codigo').value = oRetorno.iVeiculoDestino;
            $('tf18_d_dataretorno').value = oRetorno.dDataRetorno;
            $('tf18_c_horaretorno').value = oRetorno.sHoraRetorno;
            $('confirmar').name = 'alterar';
            $('retorno').disabled = false;
            $('lista').disabled = false;
        }
        js_getLotacaoDataHora();
        js_localSaida();

    } else {
        oDBGridPedidostfd.clearAll(true);
        oDBGridPedidostfd.renderRows();
        <?php
        if ($oParametros->tf11_i_utilizagradehorario != 2 && isset($tf18_i_veiculo) && $tf18_i_veiculo == "") {
            echo "document.form1.total.value    = 0;";
            echo "document.form1.livre.value    = 0;";
            echo "$('numero').value             = 0;";
            echo "document.form1.numAcomp.value = 0;";
            echo "document.form1.numPac.value   = 0;";
        }
        ?>
        alert('Nenhum CGS encontrado.');

    }
    verificaAgendamento();
}

async function js_getLotacaoDataHora() {
    if (verificaAgendamentoCancelado()) {
        await lotacaoViagemCancelada()
        return;
    }
    if ($F('tf18_i_veiculo') == '') {
        return false;
    }
    if ($F('tf18_i_destino') == '') {
        return false;
    }
    if ($F('tf18_d_datasaida') == '') {
        return false;
    }
    if ($F('tf18_c_horasaida') == '') {
        return false;
    }
    var oParam = new Object();
    oParam.exec = "getLotacaoDataHora";
    aVet = $F('tf18_d_datasaida').split('/');
    oParam.sData = aVet[2] + '-' + aVet[1] + '-' + aVet[0];
    oParam.sHora = $('tf18_c_horasaida').value;
    if (utilizaGradeHorario) {
        oParam.sHora = $('tf18_c_horasaida').options[$('tf18_c_horasaida').selectedIndex].text;
    }
    oParam.iDestino = $F('tf18_i_destino');
    oParam.iVeiculo = $F('tf18_i_veiculo');
    js_ajax(oParam, 'js_retornogetLotacaoDataHora');
}

function js_retornogetLotacaoDataHora(oRetorno, cancelado = false) {
    if (!cancelado) {
        oRetorno = JSON.parse(oRetorno.responseText);
    }
    if (oRetorno.iStatus == 1) {
        document.form1.numAcomp.value = oRetorno.iAcomp;
        document.form1.numPac.value = oRetorno.iPac;
        document.form1.numColo.value = oRetorno.iColo;
    } else {
        document.form1.numAcomp.value = 0;
        document.form1.numPac.value = 0;
    }
}

function marcarAcompanhantes(chk, sId) {
    const [, pedido] = chk.value.split('##');
    const proxChk = document.getElementById(`check${Number(sId) + 1}`);
    if (proxChk) {
        const[, pedidoProx] = proxChk.value.split('##');
        if (pedido == pedidoProx && chk.checked != proxChk.checked) {
            proxChk.disabled = !chk.checked;
            proxChk.checked = chk.checked;
            js_calcmarcar(proxChk, 2)
            marcarAcompanhantes(proxChk, Number(sId) + 1);
        }
    }
}

function js_calcmarcar(marca, tipo) {
    // numero do id dos checkbox fica e colo
    sId = marca.value.split('##')[3];
    if (marca.checked == true) {
        if (document.form1.livre.value == 0) {
            marca.checked = false;
            alert('Não hà mais lugares disponíveis!');
            return false;
        }
        if (tipo == 1) {
            document.form1.numPac.value++;
        } else {
            document.form1.numAcomp.value++;
        }

        document.form1.livre.value--;
        $('checkfica' + sId).disabled = false;
        $('checkcolo' + sId).disabled = false;
    } else {
        if (tipo == 1) {
            document.form1.numPac.value--;
        } else {
            document.form1.numAcomp.value--;
        }

        if ($('checkcolo' + sId).checked) {
            document.form1.numColo.value--;
        } else {
            document.form1.livre.value++;
        }

        $('checkfica' + sId).checked = false;
        $('checkcolo' + sId).checked = false;
        $('checkfica' + sId).disabled = true;
        $('checkcolo' + sId).disabled = true;
    }

    if (tipo === 1) {
        marcarAcompanhantes(marca, sId);
    }
}

function js_calcmarcar2(marca, tipo) {
    if (marca.checked == false) {
        if (document.form1.livre.value == 0) {
            marca.checked = true;
            alert('Não hà mais lugares disponíveis!');
            return false;
        }
        document.form1.numColo.value--;
        document.form1.livre.value--;
    } else {
        document.form1.numColo.value++;
        document.form1.livre.value++;
    }
}

function js_validaData() {
    aIni = document.form1.tf18_d_datasaida.value.split('/');
    aFim = document.form1.tf18_d_dataretorno.value.split('/');
    dIni = new Date(aIni[2], aIni[1], aIni[0]);
    dFim = new Date(aFim[2], aFim[1], aFim[0]);

    if (dFim < dIni) {
        alert('Data de retorno não pode ser menor que a data de saída.');
        document.form1.tf18_d_dataretorno.value = '';
        document.form1.tf18_d_dataretorno.focus();
        return false;
    }

    if (aIni[0] == aFim[0] && aIni[1] == aFim[1] && aIni[2] == aFim[2]) {
        aHoraIni = $F('tf18_c_horasaida').split(' ## ')[0];
        aHoraIni = aHoraIni.split(':');
        aHoraFim = $F('tf18_c_horaretorno').split(':');

        if (parseInt(aHoraFim[0], 10) < parseInt(aHoraIni[0], 10)) {
            alert('Hora de retorno não pode ser menor que a hora de saída.');
            return false;
        } else if (parseInt(aHoraFim[0], 10) == parseInt(aHoraIni[0], 10)) {
            if (parseInt(aHoraFim[1], 10) < parseInt(aHoraIni[1], 10)) {
                alert('Hora de retorno não pode ser menor que a hora de saída.');
                return false;
            }
        }
    }

    return true;
}

function js_montastr() {
    if ($F('tf18_i_veiculo') == '') {
        alert('Informe o veículo.');
        return false;
    }

    if ($F('tf18_i_destino') == '') {
        alert('Informe o destino.');
        return false;
    }

    if ($F('tf18_d_datasaida') == '') {
        alert('Informe a data de saída');
        return false;
    }

    if ($F('tf18_c_horasaida') == '' || $F('tf18_c_horasaida') == null) {
        alert('Informe a hora de saída');
        return false;
    }

    if ($F('tf18_d_dataretorno') == '') {
        alert('Informe a data de retorno');
        return false;
    }

    if ($F('tf18_c_horaretorno') == '' || $F('tf18_c_horaretorno') == null) {
        alert('Informe a hora de retorno');
        return false;
    }

    if (!js_validaData()) {
        return false;
    }

    var iContChecked = 0;
    iTam = document.form1.numero.value;
    if (iTam > 0) {
        var sPassageirosSelecionados = '';
        var sPassageirosCGS = '';
        var sSep = '';
        var iFica = '';
        var iColo = '';

        for (iCont = 0; iCont < iTam; iCont++) {
            if (document.getElementById("check" + iCont).checked) {
                aValor = $('check' + iCont).value.split('##');
                iFica = $('checkfica' + iCont).checked ? 1 : 2;
                iColo = $('checkcolo' + iCont).checked ? 1 : 2;
                /*
                A string dos passageiros selecionados é disposta da seguinte forma:
                  CGS,TFD,TIPO,FICA,COLO#CGS,TFD,TIPO,FICA,COLO...
                */
                sPassageirosSelecionados += sSep + aValor[2] + ',' + aValor[1] + ',' + aValor[0] + ',' + iFica + ',' + iColo;
                sPassageirosCGS += sSep + aValor[2];
                sSep = '#';
                iContChecked++;
            }
        }
        if (iContChecked == 0) {
            alert('Selecione passageiro! ');
            return false;
        }

        document.form1.sPassageirosSelecionados.value = sPassageirosSelecionados;
        document.form1.sPassageirosCGS.value = sPassageirosCGS;
    } else {
        alert('Selecione passageiro! ');
        return false;
    }

    return true;
}

function js_limpar() {
    location.href = 'tfd4_indveiculo001.php';
}

function js_pesquisa() {
    js_OpenJanelaIframe('', 'db_iframe_tfd_veiculodestino', 'func_tfd_veiculodestino.php?funcao_js=' +
        'parent.js_preenchepesquisa|tf18_i_codigo|Situacao', 'Pesquisa', true
    );
}

function js_preenchepesquisa(chave, situacao) {
    db_iframe_tfd_veiculodestino.hide();
    <?php
    echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?tf18_i_codigo='+chave+'&situacaoAgendamento='+situacao";
    ?>
}

function novoVinculo() {
    fecharWindowAux(false);
    js_validaGrade();
}

function fecharWindowAux(limparForm = true) {
    if (!!wndVeiculosVinculados.oDBMask) {
        wndVeiculosVinculados.oDBMask.destroy();
    }
    wndVeiculosVinculados.hide();
    return limparForm && js_limpar();
}

function montaTabelaVeiculosVinculados(veiculosVinculados) {
    const eventos = {
        'click .alterar': (e, d, data) => location.href += `?tf18_i_codigo=${data.id}`
    };
    const colunas = [
        {
            field: 'id',
            title: 'Código',
            halign: 'center',
            valign: 'center',
            width: 20
        },
        {
            field: 'veiculo',
            title: 'Veículo',
            halign: 'center',
            valign: 'left',
            width: 160
        },
        {
            field: 'motorista',
            title: 'Motorista',
            halign: 'center',
            valign: 'left',
            width: 320
        },
        {
            field: 'destino',
            title: 'Destino',
            halign: 'center',
            valign: 'left',
            width: 200
        },
        {
            field: 'hora',
            title: 'Saída',
            halign: 'center',
            valign: 'center',
            width: 60
        },
        {
            field: 'acoes',
            title: 'Ações',
            halign: 'center',
            valign: 'center',
            width: 20,
            events: eventos,
            formatter: () => {
                return '<a class="alterar" title="Alterar"><i class="fas fa-edit"></i></a>';
            }
        },
    ];
    tableVeiculosVinculados.bootstrapTable({
        columns: colunas,
        height: 300,
        search: true,
        locale: 'pt-BR',
    });

    tableVeiculosVinculados.bootstrapTable('load', veiculosVinculados);
}

async function getVeiculosVinculados() {
    if ($F('tf18_d_datasaida') === '') {
        return;
    }
    await getParametros();
    const formData = new FormData();
    formData.append('acao', 'getVeiculosVinculados');
    formData.append('idVeiculo', $F('tf18_i_veiculo'));
    formData.append('idMotorista', $F('tf18_i_motorista'));
    formData.append('idDestino', $F('tf18_i_destino'));
    formData.append('data', $F('tf18_d_datasaida'));

    const response = await HttpClient.post('sau4_tfd.RPC.php', { body: formData });
    if (response.erro) {
        alert(response.mensagem);
    }

    const { veiculosVinculados } = response;
    if (!veiculosVinculados.length) {
        return js_validaGrade();
    }

    wndVeiculosVinculados.show(0, 0, true);
    montaTabelaVeiculosVinculados(veiculosVinculados);
}

async function abreJanelaCancelamentoVeiculo() {
    await verificaPacientesRetorno();
    const containerCancelamento = $('janelaCancelamento');
    const dataSaida = document.getElementById('dataSaida');
    const codigoAgendamento = document.getElementById('codigoAgendamento');
    const motivoCancelamento = document.getElementById('motivoCancelamento');
    containerCancelamento.style.display = '';

    dataSaida.value = value = $('tf18_d_datasaida').value
    codigoAgendamento.value = $('codigoViagem').value

    await dadosCancelamento();

    readonlyCancelamento();

    janelaCancelamento.setContent(containerCancelamento);
    janelaCancelamento.show();
    motivoCancelamento.focus();
}

function fechaJanelaCancelamentoVeiculo() {
    janelaCancelamento.hide();
    $('motivoCancelamento').value = '';
}

function readonlyCancelamento() {
    const dataSaida = document.getElementById('dataSaida');
    const loginUsuario = document.getElementById('loginUsuario');
    const dataHoraCancelamento = document.getElementById('dataHoraCancelamento');
    const codigoAgendamento = document.getElementById('codigoAgendamento');
    const motivoCancelamento = document.getElementById('motivoCancelamento');

    dataSaida.readOnly = true;
    dataSaida.style.cssText = 'background: #DEB887; width: 85px; text-align: center; font-weight: normal; margin-left: 94px;';

    loginUsuario.readOnly = true;
    loginUsuario.style.cssText = 'background: #DEB887; font-weight: normal;';

    dataHoraCancelamento.readOnly = true;
    dataHoraCancelamento.style.cssText = 'background: #DEB887; font-weight: normal; width: 135px;';

    codigoAgendamento.readOnly = true;
    codigoAgendamento.style.cssText = 'background: #DEB887; font-weight: normal; width: 65px; text-align: center;';

    if (verificaAgendamentoCancelado()) {
        motivoCancelamento.readOnly = true;
        motivoCancelamento.style.cssText = 'background: #DEB887; font-weight:normal;';
    }
}

async function confirmaCancelamento() {
    if ($('motivoCancelamento').value.trim() === '') {
        alert('Campo "Motivo Cancelamento" obrigatório. Favor preencher.');
        return;
    }

    const formData = new FormData();
    formData.append('dataSaida', $('dataSaida').value);
    formData.append('codigoAgendamento', $('codigoAgendamento').value);
    formData.append('motivoCancelamento', $('motivoCancelamento').value);
    PHPSession.appendFormData(formData);

    let response = await HttpClient.post(`${PHPSession.requestApi}/saude/tfd/procedimento/viagem/cancelamento`, {body: formData});

    if (response.error === true) {
        alert('Falha ao cancelar registro!');
        return;
    }
    janelaCancelamento.hide();
    alert('Registro cancelado com sucesso.');
    location.href = 'tfd4_indveiculo001.php';
}

function verificaAgendamento() {
    if (verificaAgendamentoCancelado()) {
        let inputs = document.getElementsByTagName('input');

        for (const input of inputs) {
            input.disabled = true;
        }

        $('limpar').disabled = true;
        $('limpar').style.cssText = 'color: gray;';

        $('confirmar').disabled = true;
        $('confirmar').style.cssText = 'color: gray;';

        $('lista').disabled = true;
        $('lista').style.cssText = 'color: gray;';

        $('retorno').disabled = true;
        $('retorno').style.cssText = 'color: gray;';

        $('confirmarCancelamento').disabled = true;
        $('confirmarCancelamento').style.cssText = 'color: gray;';
    }
}

async function dadosCancelamento() {
    let cancelamento = $('codigoViagem').value;

    if (verificaAgendamentoCancelado()) {
       let response = await HttpClient.get(`${PHPSession.requestApi}/saude/tfd/consulta/viagem/cancelamento/${cancelamento}`);
       let dadosCancelamento = response.data[0];
       $('motivoCancelamento').value = dadosCancelamento.motivoCancelamento;
       $('dataHoraCancelamento').value = dadosCancelamento.dataHora;
       $('loginUsuario').value = dadosCancelamento.login;
    }
}

async function verificaPacientesRetorno() {
    let viagem = $('codigoViagem').value;

    if (!verificaAgendamentoCancelado()) {
        let response = await HttpClient.get(`${PHPSession.requestApi}/saude/tfd/consulta/viagem/${viagem}/passageiros-retorno`);

        let passageirosRetorno = response.data;

        if (passageirosRetorno.length > 0) {
            let listaPassageirosRetorno = '';
            for (const passageirosRetornoElement of passageirosRetorno) {
                listaPassageirosRetorno += `<br> ${passageirosRetornoElement.cgs} - ${passageirosRetornoElement.nome}`;
            }
            listaPassageirosRetorno += `<br><br> Caso necessário, coloque-os em outro veículo.`;
            alert(`Atenção! Há passageiros listados para retornar neste veículo: <br>${listaPassageirosRetorno}`);
        }
    }
}

function verificaAgendamentoCancelado() {
    let situacaoAgendamento = $('situacaoAgendamento').value;

    if (situacaoAgendamento.include('CANCELADO')) {
        return true;
    }
    return false;
}

async function buscaDadosViagemCancelada(viagem) {
    const response = await HttpClient.get(`${PHPSession.requestApi}/saude/tfd/consulta/viagem/${viagem}/passageiros-cancelados`);

    if (response.error){
        alert(response.message);
        return;
    }
    const passageiros = {
        aListaCgs: response.data.passageiros,
        dDataRetorno: response.data.dataRetorno,
        iStatus: 1,
        iVeiculoDestino: viagem,
        sHoraRetorno: response.data.horaRetorno,
    };

    js_retornoGridCgs(passageiros, true);
}

async function lotacaoViagemCancelada() {
    viagem = $F('codigoViagem');
    const response = await HttpClient.get(`${PHPSession.requestApi}/saude/tfd/consulta/viagem/${viagem}/lotacao-cancelados`);

    if (response.error){
        alert(response.message);
        return;
    }

    const lotacao = {
        iAcomp: response.data.totalAcompanhantes,
        iColo: response.data.totalColo,
        iPac: response.data.totalPacientes,
        iStatus: 1,
    }
    js_retornogetLotacaoDataHora(lotacao, true);
}

function cancelarAtivo() {
    $('codigoViagem').value = $('tf18_i_codigo').value

    if ($('codigoViagem').value === '') {
        $('botaoCancelar').disabled = true;
        $('botaoCancelar').style.cssText='color:gray;';
    }
}

</script>
