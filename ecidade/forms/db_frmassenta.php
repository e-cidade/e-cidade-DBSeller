<?php
/**
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
use ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedico;
use ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedicoExame;
use ECidade\Enum\Common\EstadosEnum;

db_app::load("scripts.js");
db_app::load("strings.js");
db_app::load("prototype.js");
db_app::load("estilos.css");
db_app::load("widgets/DBLancador.widget.js");
db_app::load("widgets/DBLookUp.widget.js");
db_app::load("widgets/Input/DBInput.widget.js");
db_app::load("widgets/Input/DBInputDate.widget.js");
db_app::load("AjaxRequest.js");
db_app::load("classes/recursoshumanos/Efetividade/PeriodoEfetividade.js");
db_app::load("EmissaoRelatorio.js");
//MODULO: rh
$classenta->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("h12_codigo");
$clrotulo->label("h12_assent");
$clrotulo->label("rh131_rhferias");
$clrotulo->label("h26_tipoexameocupacional");
$clrotulo->label("h26_dataatestado");
$clrotulo->label("h26_resultadoatestado");
$clrotulo->label("h26_nomemedico");
$clrotulo->label("h26_crmmedico");
$clrotulo->label("h26_ufcrm");
$clrotulo->label("h26_cpfresponsavel");
$clrotulo->label("h26_nomeresponsavel");
$clrotulo->label("h26_crmresponsavel");
$clrotulo->label("h26_ufcrmresponsavel");
$clrotulo->label("h27_data");
$clrotulo->label("h27_resultado");
$clrotulo->label("h27_procedimento");
$clrotulo->label("h27_observacao");
$clrotulo->label("h27_ordem");
$clrotulo->label("h26_sequencial");

$meiodia = true;
if (!isset($opcao_dtterm)) {
    $meiodia = false;
    $opcao_dtterm = $db_opcao;
}

if (isset($registro_funcionario)) {
    global $h16_regist, $z01_nome;
    $h16_regist = $registro_funcionario;
    $z01_nome = $nome_funcionario;
}

if (isset($lAssentamentoFuncional) && $iCodigoEfetividade) {
    $oAssentamento = AssentamentoRepository::getInstanceByCodigo($iCodigoEfetividade);
    $oServidorAssentamento = ServidorRepository::getInstanciaByCodigo(
        $oAssentamento->getMatricula(),
        DBPessoal::getAnoFolha(),
        DBPessoal::getMesFolha()
    );

    $z01_nome = $oServidorAssentamento->getCgm()->getNome();
    $h16_dtconc_dia = $oAssentamento->getDataConcessao()->getDia();
    $h16_dtconc_mes = $oAssentamento->getDataConcessao()->getMes();
    $h16_dtconc_ano = $oAssentamento->getDataConcessao()->getAno();

    if ($oAssentamento->getDataTermino() instanceof DBDate) {
        $h16_dtterm_dia = $oAssentamento->getDataTermino()->getDia();
        $h16_dtterm_mes = $oAssentamento->getDataTermino()->getMes();
        $h16_dtterm_ano = $oAssentamento->getDataTermino()->getAno();
    }

    $h16_quant = $oAssentamento->getDias();
    $quantidade = $h16_quant;
    $h16_nrport = $oAssentamento->getCodigoPortaria();
    $h16_atofic = $oAssentamento->getDescricaoAto();
    $h16_anoato = $oAssentamento->getAnoPortaria();
    $h16_histor = $oAssentamento->getHistorico();
}

if (!empty($h16_hora)) {
    $horaFormatada = preg_replace('/(\d{1,2}\:\d{1,2})[\:\d]*$/', "$1", $h16_hora);
    $h16_hora = $h16_hora;
    if (!empty($horaFormatada) && (bool)preg_match('/^\d{1,2}\:\d{2}$/', $horaFormatada)) {
        $h16_hora = $horaFormatada;
    }
}

if (!empty($h16_codigo)) {
    $rh213_horainicio = '';
    $rh213_horafim = '';
    $oDaoAssentamentoAbonoFalta = new cl_assentamentoabonofalta();

    $sql = "SELECT * FROM  assentamentoabonofalta
    where rh213_codigo = {$h16_codigo};";

    $rAssentamentoAbonoFalta = db_query($sql);

    if ($rAssentamentoAbonoFalta && pg_num_rows($rAssentamentoAbonoFalta) > 0) {
        $oDadosHorario = \db_utils::fieldsMemory($rAssentamentoAbonoFalta, 0);
        $rh213_horainicio = $oDadosHorario->rh213_horainicio;
        $rh213_horafim = $oDadosHorario->rh213_horafim;
    }
}

$naturezasCampoHora = array(
    Assentamento::NATUREZA_PONTO_ELETRONICO,
    Assentamento::NATUREZA_ABONO_FALTA
);

$displayHoras = 'none';
$displayHorasAbonoFalta = 'none';

if (isset($h12_natureza)) {
    if ($h12_natureza == Assentamento::NATUREZA_PONTO_ELETRONICO) {
        $displayHoras = '';
    }

    if ($h12_natureza == Assentamento::NATUREZA_ABONO_FALTA) {
        $displayHoras = '';
        $displayHorasAbonoFalta = '';
    }
}
?>
<style type="text/css">
    #h16_dtconc, #h16_dtterm {
        width: 80px;
    }
    .celulas-percentual {
        display: none;
    }
    #h12_assentdescr, #z01_nome {
        width: 285px;
    }

    .controle-medico {
        visibility: collapse;
    }

    #h26_ufcrmresponsavel {
        width: 120px;
    }
    #h26_ufcrm {
        width: 40px;
    }

    #h26_tipoexameocupacional {
        width: 350px;
    }

    #h27_data, #h26_dataatestado {
        background-color: #fff!important;
    }

    #historico {
        padding-top: 5px;
    }

    .celulas-periodos-justificativa, .hora-extra-manual {
        display: none;
    }

    .ajuste-padding td{

        padding: 2px 0px 4px;
    }

</style>
<?php
if (isset($lAssentamentoFuncional)) {
    echo "
    <style>
        body{
            overflow: hidden;
        }
    </style>";
}?>
<form id="form1" name="form1" method="post" action="" class="container">
    <?php
    db_input('db_opcao', 10, $Ih16_codigo, true, 'hidden', 3, "");
    db_input(
        ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")),
        10,
        $Ih16_codigo,
        true,
        'hidden',
        3,
        ""
    );
    db_input('h80_db_cadattdinamicovalorgrupo', 10, $Ih16_codigo, true, 'hidden', 3, "");?>
    <input type="hidden" name="attDinamicoValorGrupoOrigem" id="attDinamicoValorGrupoOrigem" value="">
    <fieldset style="width:570px;margin:10px auto;">
        <legend><strong>Assentamentos</strong></legend>
        <table class="form-container">
            <?php
            db_input('h16_codigo', 6, $Ih16_codigo, true, 'hidden', 3, "");
            db_input('h12_vinculaperiodoaquisitivo', 6, '', true, 'hidden', 3);
            db_input('lBloqueiaPeriodoAquisitivo', 6, '', true, 'hidden', 3);
            $db_opcao_matricula = $db_opcao;
            if (isset($lAssentamentoFuncional)) {
                $db_opcao_matricula = 3;
            }
            ?>
            <tr id="trLancamento" class="ajuste-padding">
                <td>
                    <label for="lancamento">Lançamento</label>
                </td>

                <td>
                    <select id="lancamento">
                        <option value="1">Individual</option>
                        <option value="2">Lote</option>
                    </select>
                </td>
            </tr>

            <tr id="linhaMatricula" class="ajuste-padding">
                <td nowrap title="<?= @$Th16_regist; ?>">
                    <div id="labelServidorAncora">
                        <?php
                        if (!isset($iTipoFuncionamento)) {
                            $iTipoFuncionamento = 2;
                        }
                        db_ancora(
                            @$Lh16_regist,
                            "js_pesquisaServidores(true, $iTipoFuncionamento);",
                            $db_opcao_matricula
                        ); ?>
                    </div>
                    <div id="labelServidor" style="display: none;">
                        <label for="h16_regist">Servidor:</label>
                    </div>
                </td>
                <td colspan="3">
                    <input type="hidden" name="instituicao" id="instituicao"
                           value="<?= db_getsession('DB_instit'); ?>"/>
                    <?php
                    db_input(
                        'h16_regist',
                        8,
                        1,
                        true,
                        'text',
                        $db_opcao_matricula,
                        " onchange='js_pesquisaServidores(false, $iTipoFuncionamento);'"
                    );
                    db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr class="ajuste-padding">
                <td nowrap title="<?= @$Th16_assent; ?>">
                    <?php db_ancora(@$Lh16_assent, "js_pesquisah12_assent(true);", $db_opcao); ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input('h12_codigo', 6, "", true, 'text', $db_opcao, "");
                    db_input('h16_assent', 6, "", true, 'hidden', $db_opcao, "");
                    db_input('h12_natureza', 6, "", true, 'hidden', $db_opcao, "");
                    db_input('h12_natureza_novo_tipo', 6, "", true, 'hidden', $db_opcao, "");

                    $dbwhere = "";
                    if ($db_opcao != 1) {
                        if (!empty($h16_assent)) {
                            $dbwhere = "h12_codigo = " . @$h16_assent;
                            $result = $cltipoasse->sql_record(
                                $cltipoasse->sql_query_file(null, "h12_assent,h12_descr", 'h12_descr ASC', $dbwhere)
                            );
                            if ($cltipoasse->numrows > 0) {
                                db_fieldsmemory($result, 0);
                            }
                        }
                    }

                    if (isset($iTipoFuncionamento) && $iTipoFuncionamento == 1) {
                        $iCodigoDepartamento = db_getsession('DB_coddepto');
                        $sWhere = "exists (select 1 from tipoassedb_depart where rh184_db_depart = $iCodigoDepartamento
                            and rh184_tipoasse = h12_codigo)";
                        $result_tipoasse = $cltipoasse->sql_record(
                            $cltipoasse->sql_query_file(null, "trim(h12_assent),h12_descr", 'h12_descr ASC', $sWhere)
                        );
                    } else {
                        $result_tipoasse = $cltipoasse->sql_record(
                            $cltipoasse->sql_query_file(null, "trim(h12_assent),h12_descr", 'h12_descr ASC')
                        );
                    }

                    db_selectrecord(
                        "h12_assent",
                        $result_tipoasse,
                        true,
                        $db_opcao,
                        "rel='ignore-css'",
                        "",
                        "",
                        " -(Selecione)",
                        "js_pesquisah12_assent(false);"
                    );

                    if ($db_opcao == 1 || $db_opcao == 11) {
                        unset($h12_descr);
                    }
                    ?>
                </td>
            </tr>
            <tr class="vinculoperiodoaquisitivo">
                <td nowrap title="<?php echo $Trh131_rhferias; ?>">
                    <?php db_ancora($Lrh131_rhferias, "js_pesquisaPeriodoAquisitivo(true);", $db_opcao); ?>
                </td>
                <td colspan="3">
                    <?php
                    db_input(
                        'iPeriodoAquisitivo',
                        6,
                        '',
                        true,
                        'text',
                        3,
                        " onchange=js_pesquisaPeriodoAquisitivo(false);"
                    );
                    db_input('dtPeriodoAquisitivoInicio', 8, '', true, 'text', 3);
                    ?>
                    &nbsp;
                    a
                    &nbsp;
                    <?php db_input('dtPeriodoAquisitivoFinal', 8, '', true, 'text', 3); ?>
                </td>
            </tr>
            <tr class="vinculoperiodoaquisitivo">
                <td>
                    <strong>Saldo de Dias:</strong>
                </td>
                <td>
                    <?php db_input('iSaldoDias', 6, '', true, 'text', 3); ?>
                </td>
            </tr>

            <tr id="DataIni">
                <td nowrap title="<?= @$Th16_dtconc ?>">
                    <?= @$Lh16_dtconc ?>
                </td>
                <td>
                    <?php db_inputdata(
                        'h16_dtconc',
                        @$h16_dtconc_dia,
                        @$h16_dtconc_mes,
                        @$h16_dtconc_ano,
                        true,
                        'text',
                        $db_opcao,
                        "onchange='js_somar_dias(document.form1.h16_quant.value, 0)'",
                        "",
                        "",
                        "parent.js_somar_dias(parent.document.form1.h16_quant.value, 0)"
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="DataFim" nowrap title="<?= @$Th16_dtterm ?>"><?= @$Lh16_dtterm ?></div>
                </td>
                <td>
                    <?php
                    db_inputdata(
                        'h16_dtterm',
                        @$h16_dtterm_dia,
                        @$h16_dtterm_mes,
                        @$h16_dtterm_ano,
                        true,
                        'text',
                        $opcao_dtterm,
                        "onchange='js_somar_dias(0, 3)'",
                        "",
                        "",
                        "parent.js_somar_dias(0, 3)"
                    ); ?>
                </td>
                <td nowrap title="Somar dias" class="campos-quantidade">
                    <b>Quantidade:</b>
                </td>
                <td class="campos-quantidade">
                    <?php
                    db_input(
                        'h16_quant',
                        12,
                        $Ih16_quant,
                        true,
                        'text',
                        $opcao_dtterm,
                        "onchange='js_somar_dias(this.value, 1);'",
                        "quantidade"
                    ); ?>
                </td>
            </tr>
            <tr class="controle-medico">
                <td >
                    <strong>Assentamento de:</strong>
                </td>
                <td><input type="text" value="Controle Médico" readonly style="background-color:#DEB887;"></td>
            </tr>
            <tr class="controle-medico">
                <td colspan="4">
                    <fieldset>
                        <legend>Informa&ccedil;&otilde;es de exame m&eacute;dico ocupacional.</legend>
                        <table>
                            <tr>
                                <td><?php echo $Lh26_tipoexameocupacional;?></td>
                                <td>
                                    <?php
                                    db_input('h26_sequencial', 10, $Ih26_sequencial, true, 'hidden', 3, "");
                                    $opcoesTipoExameOcupacional = ControleMedico::getTiposExames();
                                    db_select("h26_tipoexameocupacional", $opcoesTipoExameOcupacional, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr class="controle-medico">
                <td colspan="4">
                    <fieldset>
                        <legend>Detalhamento das informa&ccedil;&otilde;es do Atestado de Sa&uacute;de Ocupacional - ASO.</legend>
                        <table>
                            <tr>
                                <td><?php echo $Lh26_dataatestado;?></td>
                                <td>
                                    <?php
                                    if (!empty($h26_dataatestado)) {
                                        $h26_dataatestado = explode("-", $h26_dataatestado);
                                        $h26_dataatestado_dia = $h26_dataatestado[2];
                                        $h26_dataatestado_mes = $h26_dataatestado[1];
                                        $h26_dataatestado_ano = $h26_dataatestado[0];
                                    }
                                    $h26_dataatestado_dia = !empty($h26_dataatestado_dia) ? $h26_dataatestado_dia : '';
                                    $h26_dataatestado_mes = !empty($h26_dataatestado_mes) ? $h26_dataatestado_mes : '';
                                    $h26_dataatestado_ano = !empty($h26_dataatestado_ano) ? $h26_dataatestado_ano : '';

                                    db_inputdata(
                                        'h26_dataatestado',
                                        $h26_dataatestado_dia,
                                        $h26_dataatestado_mes,
                                        $h26_dataatestado_ano,
                                        true,
                                        'text',
                                        $db_opcao
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $Lh26_resultadoatestado;?></td>
                                <td>
                                    <?php
                                    $opcoesTipoResultado = ControleMedico::getTiposResultados();
                                    db_select("h26_resultadoatestado", $opcoesTipoResultado, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr class="controle-medico">
                <td colspan="4">
                    <fieldset>
                        <legend>Detalhamento dos Exames.</legend>
                        <table>
                            <tr>
                                <td>
                                    <?php echo $Lh27_data;?>
                                </td>
                                <td>
                                    <?php
                                    $h27_data_dia = !empty($h27_data_dia) ? $h27_data_dia : '';
                                    $h27_data_mes = !empty($h27_data_mes) ? $h27_data_mes : '';
                                    $h27_data_ano = !empty($h27_data_ano) ? $h27_data_ano : '';
                                    db_inputdata(
                                        'h27_data',
                                        $h27_data_dia,
                                        $h27_data_mes,
                                        $h27_data_ano,
                                        true,
                                        'text',
                                        $db_opcao
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td title="<?php echo $Th27_procedimento;?>">
                                    <?php db_ancora($Lh27_procedimento, "abreJanelaProcedimentos('js_mostraProcedimentos')", $db_opcao, "", "procedimentos"); ?>
                                </td>
                                <td>
                                    <?php
                                    db_input("codigo_procedimento", 10, "", true, "hidden", 3, "");
                                    db_input("descricao_procedimento", 50, "", true, "text", 3, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $Lh27_observacao;?>
                                </td>
                                <td>
                                    <?php db_textarea('h27_observacao', 5, 47, $Ih27_observacao, true, 'text', $db_opcao, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $Lh27_ordem;?>
                                </td>
                                <td>
                                    <?php
                                    $opcoesOrdemExame = ControleMedicoExame::getDescricaoOrdemExame();
                                    db_select("h27_ordem", $opcoesOrdemExame, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $Lh27_resultado;?>
                                </td>
                                <td>
                                    <?php
                                    $opcoesResultadoExame = ControleMedicoExame::getDescricaoResultado();
                                    db_select("h27_resultado", $opcoesResultadoExame, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <center><input type="button" id="adicionaGridExame" value="Adicionar Exame"></center>
                        <table width="100%">
                            <tr>
                                <td>
                                    <input id="exames" style="display: none" type="hidden" name="exames">
                                    <input id="exame-sequencial" style="display: none" type="hidden">
                                    <div id="container-exames"></div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr class="controle-medico">
                <td colspan="4">
                    <fieldset>
                        <legend>Informa&ccedil;&otilde;es do M&eacute;dico emitente do ASO.</legend>
                        <table>
                            <tr>
                                <td nowrap title="<?php echo $Th26_nomemedico;?> colspan="2">
                                <?php
                                echo $Lh26_nomemedico;
                                db_input('h26_nomemedico', 70, $Ih26_nomemedico, true, 'text', $db_opcao, "", "", "#FFF");
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td title="<?php echo $Th26_crmmedico;?>">
                                    <?php
                                    echo $Lh26_crmmedico;
                                    db_input('h26_crmmedico', 10, $Ih26_crmmedico, true, 'text', $db_opcao, "", "", "#FFF", "", 10);
                                    echo $Lh26_ufcrm;
                                    $opcoesEstados = EstadosEnum::getSiglas();
                                    db_select("h26_ufcrm", $opcoesEstados, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr class="controle-medico">
                <td colspan="4">
                    <fieldset>
                        <legend>Informa&ccedil;&otilde;es do M&eacute;dico responsável do PCMSO. do ASO.</legend>
                        <table>
                            <tr>
                                <td nowrap title="<?php echo $Th26_nomeresponsavel;?>" colspan="1">
                                    <?php
                                    echo $Lh26_nomeresponsavel;
                                    db_input('h26_nomeresponsavel', 70, $Ih26_nomeresponsavel, true, 'text', $db_opcao, "", "", "#FFF");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    echo $Lh26_cpfresponsavel;
                                    db_input('h26_cpfresponsavel', 15, $Ih26_cpfresponsavel, true, 'text', 1, "", "", "#FFF");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo $Th26_cpfresponsavel;?>" colspan="2">
                                    <?php
                                    echo $Lh26_crmresponsavel;
                                    db_input('h26_crmresponsavel', 10, $Ih26_crmresponsavel, true, 'text', $db_opcao, "", "", "#FFF", "", 10);
                                    echo $Lh26_ufcrmresponsavel;
                                    $opcoesEstadosResponsavel = EstadosEnum::getSiglas(false);
                                    db_select("h26_ufcrmresponsavel", $opcoesEstadosResponsavel, true, $db_opcao, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr id="linhaJornadaServidor" style="display:<?php echo $displayHorasAbonoFalta;?>">
                <td>
                    <label for="dadosJornadaServidor">Jornada:</label>
                </td>
                <td colspan="2">
                    <input id="dadosJornadaServidor" type="text" value=""
                        disabled="disabled" class="readonly field-size4" />
                </td>
            </tr>
            <tr class="celulas-hora-periodo">
                <td class="celulas-hora" style="display:<?php echo $displayHorasAbonoFalta;?>" >
                    <label id="lbl_rh213_horainicio" for="rh213_horainicio">
                        <?php echo "Hora inicial :"; ?>
                    </label>
                </td>
                <td class="celulas-hora" style="display:<?php echo $displayHorasAbonoFalta;?>">
                    <?php db_input('rh213_horainicio', 5, '', true, 'text', $db_opcao); ?>
                </td>

                <td class="celulas-hora" style="display:<?php echo $displayHorasAbonoFalta;?>">
                    <label id="lbl_rh213_horafim" for="rh213_horafim"><?php echo "Hora fim :";?></label>
                </td>
                <td class="celulas-hora  horas-calc"  style="display:<?php echo $displayHorasAbonoFalta;?>">
                    <?php db_input('rh213_horafim', 5, '', true, 'text', $db_opcao); ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="celulas-hora" style="display:<?php echo $displayHoras;?>">
                    <label id="lbl_h16_hora" for="h16_hora"><?php echo $Lh16_hora;?></label>
                </td>
                <td class="celulas-hora" style="display:<?php echo $displayHoras;?>">
                    <?php db_input('h16_hora', 5, $Ih16_hora, true, "text", $db_opcao);?>
                </td>
            </tr>
            <tr>
                <td class="celulas-percentual"></td>
                <td class="celulas-percentual"></td>
                <td nowrap title="<?php echo $Th16_perc; ?>" class="celulas-percentual">
                    <label id="lbl_h16_perc" for="h16_perc"><?php echo $Lh16_perc; ?></label>
                </td>
                <td class="celulas-percentual">
                    <?php db_input('h16_perc', 10, $Ih16_perc, true, "text", $db_opcao);?>
                </td>
            </tr>
            <?php
            $opcao_quant = 3;
            if (isset($h12_tipo) && isset($h12_tipefe) && trim($h12_tipo) == "S" && trim($h12_tipefe) == "C") {
                $opcao_quant = 1;
                ?>
                <tr>
                    <td nowrap title="Ano">
                        <b>Anos:</b>
                    </td>
                    <td colspan="3">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <?php
                                    db_input('valor_ano', 4, 1, true, 'text', 1, "");
                                    ?>
                                </td>
                                <td nowrap title="Mês">
                                    <b>Meses:</b>
                                </td>
                                <td>
                                    <?php
                                    db_input('valor_mes', 4, 1, true, 'text', 1, "");
                                    ?>
                                </td>
                                <td nowrap title="Dia">
                                    <b>Dias:</b>
                                </td>
                                <td>
                                    <?php
                                    db_input('valor_dia', 4, 1, true, 'text', 1, "");
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
            }?>
            <tr class="campos-quantidade">
                <td nowrap title="<?=@$Th16_quant;?>">
                    <?=@$Lh16_quant;?>
                </td>
                <td colspan="3">
                    <?php
                    $opcaoquant = empty($opcaoquant) ? null : $opcaoquant;
                    db_input('h16_quant', 8, $Ih16_quant, true, 'text', ($opcao_dtterm == 3 ? 3 : $opcaoquant), "");?>
                </td>
            </tr>
            <tr class="assenta-old">
                <td nowrap title="<?=@$Th16_nrport;?>">
                    <?=@$Lh16_nrport;?>
                </td>
                <td>
                    <?php db_input('h16_nrport', 8, $Ih16_nrport, true, 'text', $db_opcao, "");?>
                </td>
                <td nowrap title="<?=@$Th16_atofic;?>">
                    <?=@$Lh16_atofic;?>
                </td>
                <td>
                    <?php db_input('h16_atofic', 12, $Ih16_atofic, true, 'text', $db_opcao, "");?>
                </td>
            </tr>
            <tr class="assenta-old">
                <td nowrap title="<?=@$Th16_anoato;?>">
                    <?=@$Lh16_anoato;?>
                </td>
                <td>
                    <?php db_input('h16_anoato', 8, $Ih16_anoato, true, 'text', $db_opcao, "");?>
                </td>
                <td>
                    Assentamento de:
                </td>
                <td>
                    <?php
                    $aOpcaoAssentamento = array(1 => 'Efetividade', 2 => 'Histórico Funcional');

                    if (isset($lAssentamentoFuncional)) {
                        $sOpcaoAssentamento = 2;
                    }

                    if ($db_opcao == 1 || $db_opcao == 11) {
                        $sOpcaoAssentamento = isset($iTipoFuncionamento) ? $iTipoFuncionamento : 2;
                    }

                    db_select('sOpcaoAssentamento', $aOpcaoAssentamento, true, 3, "", "", "");?>
                </td>
            </tr>

            <tr id="trTipoPesquisa" style="display: none" class="ajuste-padding">
                <td>
                    <label for="tipoPesquisa">Filtrar Por:</label>
                </td>

                <td>
                    <select id="tipoPesquisa">
                        <option value="1">Seleção</option>
                        <option value="2">Matrículas</option>
                    </select>
                </td>
            </tr>
            <tr id="linhaSelecao" style="display: none;" class="ajuste-padding">
                <td>
                    <?php db_ancora("Seleção:", "js_pesquisaSelecao(true)", 1); ?>
                </td>
                <td colspan="4">
                    <?php db_input('r44_selec', 8, 1, true, 'text', "", 'class="field-size2" onchange="js_pesquisaSelecao(false)"'); ?>
                    <?php db_input('r44_des', 30, "", true, 'text', 3, 'class="field-size7"'); ?>
                </td
            </tr>

            <tr id="linhaMatriculas" style="display: none;">
                <td id="linhaLancadorMatriculas" colspan="4"></td>
            </tr>
            <tr>
                <td colspan="4" title="<?=@$Th16_histor;?>" id="historico">
                    <fieldset>
                        <legend>Histórico</legend>
                        <?php db_textarea('h16_histor', 5, 47, $Ih16_histor, true, 'text', $db_opcao, "");?>
                    </fieldset>
                </td>
            </tr>
            <tr class="celulas-periodos-justificativa">
                <td>
                    Períodos da Justificativa:
                </td>
                <td>
                    <input type="checkbox" value="1" id="periodoJustificativa1" name="periodoJustificativa1"
                        <?php
                        echo $db_opcao == 3 ? 'disabled' : '';
                        echo !empty($periodoJustificativa1) ? 'checked' : '';
                        ?>
                    />
                    <label for="periodoJustificativa1">Entrada 1 - Saída 1</label>
                </td>
            </tr>
            <tr class="celulas-periodos-justificativa">
                <td></td>
                <td>
                    <input type="checkbox" value="2" id="periodoJustificativa2" name="periodoJustificativa2"
                        <?php
                        echo $db_opcao == 3 ? 'disabled' : '';
                        echo !empty($periodoJustificativa2) ? 'checked' : '';?>
                    />
                    <label for="periodoJustificativa2">Entrada 2 - Saída 2</label>
                </td>
            </tr>
            <tr class="celulas-periodos-justificativa">
                <td></td>
                <td>
                    <input type="checkbox" value="3" id="periodoJustificativa3" name="periodoJustificativa3"
                        <?php
                        echo $db_opcao == 3 ? 'disabled' : '';
                        echo !empty($periodoJustificativa3) ? 'checked' : '';
                        ?>
                    />
                    <label for="periodoJustificativa3">Entrada 3 - Saída 3</label>
                </td>
            </tr>
            <tr class="hora-extra-manual">
                <td colspan="4">
                    <fieldset>
                        <legend>Quantidade de Horas Extras</legend>
                        <table>
                            <tr class="celulas-hora-extra-manual">
                                <td><label for="horaExtraManual50Diurna">50% diurnas:</label></td>
                                <td><?php db_input('horaExtraManual50Diurna', 5, 0, true, 'text', $db_opcao);?></td>
                                <td><label for="horaExtraManual50Noturna">50% noturnas:</label></td>
                                <td><?php db_input('horaExtraManual50Noturna', 5, 0, true, 'text', $db_opcao);?></td>
                            </tr>
                            <tr class="celulas-hora-extra-manual">
                                <td><label for="horaExtraManual75Diurna">75% diurnas:</label></td>
                                <td><?php db_input('horaExtraManual75Diurna', 5, 0, true, 'text', $db_opcao);?></td>
                                <td><label for="horaExtraManual75Noturna">75% noturnas:</label></td>
                                <td><?php db_input('horaExtraManual75Noturna', 5, 0, true, 'text', $db_opcao);?></td>
                            </tr>
                            <tr class="celulas-hora-extra-manual">
                                <td><label for="horaExtraManual100Diurna">100% diurnas:</label></td>
                                <td><?php db_input('horaExtraManual100Diurna', 5, 0, true, 'text', $db_opcao);?></td>
                                <td><label for="horaExtraManual100Noturna">100% noturnas:</label></td>
                                <td><?php db_input('horaExtraManual100Noturna', 5, 0, true, 'text', $db_opcao);?></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
        <div id="conteudoCamposAdicionais"></div>
    </fieldset>
    <fieldset id="fieldsetDadosRetificacao" style='display: none;'>
        <legend class="bold">Dados da Retificação</legend>
        <table style="width: 100%;">
            <tr>
                <td colspan="2"><input type="hidden" id="sequencialRetificacao" name="sequencialRetificacao"></td>
            </tr>
            <tr>
                <td class="bold" style="width: 25%"><label for="origemProcesso">Origem do Processo:</label></td>
                <td style="width: 75%">
                    <select id="origemProcesso" name="origemProcesso">
                        <option value="">Selecione</option>
                        <option value="1">Por Iniciativa do Empregador</option>
                        <option value="2">Revisão Administrativa</option>
                        <option value="3">Determinação Judicial</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold"><label for="tipoProcesso">Tipo do Processo:</label></td>
                <td>
                    <select id="tipoProcesso" name="tipoProcesso">
                        <option value="">Selecione</option>
                        <option value="1">Administrativo</option>
                        <option value="2">Judicial</option>
                        <option value="3">Número de Benefício</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold"><label for="numeroProcesso">Número do Processo:</label></td>
                <td>
                    <?php db_input('numeroProcesso', 10, '3', true, 'text', 1); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <?php
    if (!isset($bloqueia)) { ?>
        <br/>
        <input id="botao-acao" name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir"));?>"
           type="submit" id="db_opcao"
           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir"));?>"
           title="<?= ($db_opcao == 1 ? "Inclusão" : ($db_opcao == 2 || $db_opcao == 22 ? "Alteração" : "Exclusão"));?>"
            <?= ($db_botao == false ? "disabled" : "");?> onclick="return salvarAssentamento()"
        />
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
        <?php
    }
    if ($db_opcao != 1) { ?>
        <input name="voltar" type="button" id="voltar" value="Voltar" onclick="js_pesquisa2();">
        <?php
    } ?>
    <input name="duplicar" type="hidden" id="duplicar" value="Duplicar"/>
</form>
<div style="width: 580px" id="campos_adicionais"></div>
<div class="container" style="display: none; padding: 15px;" id="datasAssentamentosDuplicados">
    <table class="form-container">
        <tr>
            <td>
                <label id="labelDataInicial">Data inicial:</label>
            </td>
            <td>
                <input name="dataInicialDuplicata" id="dataInicialDuplicata">
            </td>
        </tr>
        <tr>
            <td>
                <label id="labelDataFinal">Data final:</label>&nbsp;
            </td>
            <td>
                <input id="dataFinalDuplicata" name="dataFinalDuplicata">
            </td>
        </tr>
    </table>
    <button class="button button--sm button--light" id="salvarDuplicata" name="salvarDuplicata"
        style="margin-top: 15px;">Duplicar</button>
</div>

<div class="container" id="tabelaAssentamentos" style="display: none;">
    <fieldset>
        <legend><strong>Últimos assentamentos lançados</strong></legend>
        <div class="container" id="gridAssentamentos"> </div>
    </fieldset>
</div>

<?php
if (isset($lAssentamentoFuncional)) {
    unset($z01_nome);
    unset($h16_dtconc_dia);
    unset($h16_dtconc_mes);
    unset($h16_dtconc_ano);

    if ($oAssentamento->getDataTermino() instanceof DBDate) {
        unset($h16_dtterm_dia);
        unset($h16_dtterm_mes);
        unset($h16_dtterm_ano);
    }

    unset($h16_quant);
    unset($quantidade);
    unset($h16_nrport);
    unset($h16_atofic);
    unset($h16_anoato);
    unset($h16_histor);
}
?>
<script language="javascript" type="text/javascript" src="scripts/arrays.js"></script>
<script>


    function js_montaGrid() {
        gridUltimosAssentamentos = new DBGrid("dataGridRegistros");
        gridUltimosAssentamentos.sName = "dataGridRegistros";
        gridUltimosAssentamentos.nameInstance = "gridUltimosAssentamentos";
        gridUltimosAssentamentos.hasTotalizador = false;
        gridUltimosAssentamentos.setHeader(["Código", "Descrição", "Data Inicial", "Data Final", "Quantidade"]);
        gridUltimosAssentamentos.setCellWidth(["50px", "200px", "150px", "150px", "150px"]);
        gridUltimosAssentamentos.setCellAlign(["center", "center", "center", "center", "center"]);
        gridUltimosAssentamentos.show($('gridAssentamentos'));

        js_carregarRegistros();
    }

    document.getElementById('h16_regist').addEventListener("change", (event) => {
        gridUltimosAssentamentos.clearAll(true);
        js_carregarRegistros();
    });

    window.onload = function(){
        js_montaGrid();
        js_carregarRegistros();
    }

    function js_carregarRegistros() {

        const oParametros = {
            sExec: 'buscarAssentamentosCompetencia',
            matricula: document.form1.h16_regist.value
        };

        if (oParametros.matricula == '') {
            document.getElementById('tabelaAssentamentos').style.display = 'none';
            return;
        }

        document.getElementById('tabelaAssentamentos').style.display = '';

        const oAjaxRequest = new AjaxRequest('rec1_assenta.RPC.php', oParametros, function (oAjax) {
                gridUltimosAssentamentos.clearAll(true);
                for (const assentamento of oAjax.assentamentos) {
                    gridUltimosAssentamentos.addRow([
                        assentamento.h12_assent,
                        assentamento.h12_descr.urlDecode(),
                        js_formatar(assentamento.h16_dtconc,'d'),
                        !assentamento.h16_dtterm ? '' : js_formatar(assentamento.h16_dtterm,'d'),
                        assentamento.h16_quant
                    ]);
                }
                gridUltimosAssentamentos.renderRows();
            }
        );

        oAjaxRequest.setMessage('Carregando assentamentos...');
        oAjaxRequest.execute();
    }

    const lancamento = document.getElementById('lancamento');
    lancamento.onchange = () => {
        if (lancamento.value == 2) {
            document.getElementById('tabelaAssentamentos').style.display = 'none';
            linhaMatricula.style = 'display: none';
            trTipoPesquisa.style = "display: ' '";
            if (tipoPesquisa.value == 1) {
                linhaMatriculas.style = 'display: none';
                linhaSelecao.style = "display: ' '";
            } else {
                linhaMatriculas.style = "display: ' '";
                linhaSelecao.style = 'display: none';
            }
        } else {
            trTipoPesquisa.style = 'display: none';
            linhaMatriculas.style = 'display: none';
            linhaSelecao.style = 'display: none';
            linhaMatricula.style = "display:''";
            if (document.form1.h16_regist.value != '') {
                document.getElementById('tabelaAssentamentos').style.display = '';
            }
        }
    };

    var opcaoLancamento = "<?php echo $db_opcao;?>";
    const botaoAcao = document.getElementById('botao-acao');

    //Apresenta o lançador quando efetuado o evento de change no campo FILTRAR POR.
    var oLancadorMatriculas = new DBLancador('oLancadorMatriculas');
    oLancadorMatriculas.setNomeInstancia('oLancadorMatriculas');
    oLancadorMatriculas.setLabelAncora('Matrícula: ');
    oLancadorMatriculas.setParametrosPesquisa('func_rhpessoal.php', ['rh01_regist','z01_nome'], "");
    oLancadorMatriculas.show($('linhaLancadorMatriculas'));
    oLancadorMatriculas.adicionarItensPrimeiraPosicao(true);

    const tipoPesquisa = document.getElementById('tipoPesquisa');
    const linhaSelecao = document.getElementById('linhaSelecao');
    const linhaMatriculas = document.getElementById('linhaMatriculas');
    const linhaMatricula = document.getElementById('linhaMatricula');

    const trTipoPesquisa = document.getElementById('trTipoPesquisa');
    const trLancamento = document.getElementById('trLancamento');
    tipoPesquisa.onchange = () => {

        linhaSelecao.style = 'display: " "';
        linhaMatriculas.style = 'display: none';

        if(tipoPesquisa.value === '2') {
            linhaSelecao.style = 'display: none';
            linhaMatriculas.style = 'display: " "';
        }
    };

    //Caso não seja incluir, nao exibe nada
    if (opcaoLancamento != 1) {
        trTipoPesquisa.style = 'display: none';
        linhaMatriculas.style = 'display: none';
        linhaSelecao.style = 'display: none';
        trLancamento.style = 'display: none';
    }


    js_removeObj("oCarregando");
    var possuiProcessoEsocial = false;
    var obrigarRetificacao = false;

    var inputProcessos = {
        'sequencial': document.getElementById('sequencialRetificacao'),
        'origem': document.getElementById('origemProcesso'),
        'tipo': document.getElementById('tipoProcesso'),
        'numero': document.getElementById('numeroProcesso')
    };


    inputProcessos.origem.style.width = '100%';
    inputProcessos.tipo.style.width = '100%';
    inputProcessos.numero.style.width = '100%';

    const sUrlRPC = 'rec1_assenta.RPC.php';

    verificarExistenciaProcessoEsocial();

    var codigoTipoAssentamento = $F('h12_assent');
    var descricaoTipoAssentamento = $F('h12_assentdescr');

    var oHoraIncio = new DBInputHora($('rh213_horainicio'));
    var oHoraFim = new DBInputHora($('rh213_horafim'));

    const MENSAGENS = 'recursoshumanos.rh.rec1_assenta.';

    function carregarInformacoesProcesso()
    {
        $('attDinamicoValorGrupoOrigem').value = $F('h80_db_cadattdinamicovalorgrupo');

        if (possuiProcessoEsocial) {
            $('fieldsetDadosRetificacao').style.display = 'inline-block';
        }

        var codigoAssentamento = $F('h16_codigo');
        if (codigoAssentamento === "") {
            inputProcessos.sequencial.value = '';
            inputProcessos.origem.value = '';
            inputProcessos.tipo.value = '';
            inputProcessos.numero.value = '';
            return false;
        }

        AjaxRequest.create(
            sUrlRPC,
            {'sExec' : 'getDadosRetificacao', 'codigo_assenamento' : codigoAssentamento },
            function (retorno, erro) {
                if (erro) {
                    alert(retorno.sMensagem);
                    return;
                }
                inputProcessos.sequencial.value = retorno.retificacao.sequencial;
                inputProcessos.origem.value = retorno.retificacao.origem;
                inputProcessos.tipo.value = retorno.retificacao.tipo;
                inputProcessos.numero.value = retorno.retificacao.numero;
                verificaPossibilidadeDuplicataAssentamento();
            }
        ).execute();
    }

    function verificarExistenciaProcessoEsocial()
    {
        AjaxRequest.create(
            sUrlRPC,
            {'sExec': 'possuiProtocoloEsocial', 'codigo_assentamento' : $F('h16_codigo')},
            function(retorno, erro) {
                if (erro) {
                    alert(retorno.sMensagem.urlDecode());
                    return false;
                }
                possuiProcessoEsocial = retorno.possuiProtocoloEsocial;
                validarProtocoloExclusao();

                if (possuiProcessoEsocial) {
                    $('h16_regist').readonly;
                    $('h16_regist').setStyle({"backgroundColor" : "#DEB887", "color" : "black"});
                    $('labelServidorAncora').setStyle({"display" : "none"});
                    $('labelServidor').setStyle({"display" : "inline-block"});
                }
            }
        ).asynchronous(false).execute();
    }

    function validarProtocoloExclusao()
    {
        if (possuiProcessoEsocial
            && ($F('db_opcao').toLowerCase() == 'excluir' || $F('db_opcao').toLowerCase() == '3'))
        {
            alert("Não é possível excluir o assentamento pois o mesmo possui protocolo no eSocial.");
            var btnExcluir = document.querySelector("input[name=excluir][type=submit]");
            btnExcluir.disabled = true;
        }
    }

    $$("#rh213_horainicio").invoke('observe', 'blur', atualizarIntervaloHora);
    $$("#rh213_horafim").invoke('observe', 'blur', atualizarIntervaloHora);

    js_verificaVinculoPeriodoAquisitivo(false);

    var campoHora = new DBInputHora($('h16_hora'));
    var horaExtraManual50Diurna = new DBInputHora($('horaExtraManual50Diurna'));
    var horaExtraManual50Noturna = new DBInputHora($('horaExtraManual50Noturna'));
    var horaExtraManual75Diurna = new DBInputHora($('horaExtraManual75Diurna'));
    var horaExtraManual75Noturna = new DBInputHora($('horaExtraManual75Noturna'));
    var horaExtraManual100Diurna = new DBInputHora($('horaExtraManual100Diurna'));
    var horaExtraManual100Noturna = new DBInputHora($('horaExtraManual100Noturna'));

    function atualizarIntervaloHora()
    {
        var sum = 0;
        var inicial = $F('rh213_horainicio');
        var fim = $F("rh213_horafim");
        sum = diferencaHoras(inicial, fim);
        if (fim != "") {
            $('h16_hora').setValue(sum);
        }
    }

    function validarRetificacaoProcessos()
    {
        if (obrigarRetificacao && possuiProcessoEsocial && inputProcessos.origem.value === "") {
            alert("Campo Origem do Processo é de preenchimento obrigatório.");
            return false;
        }

        if (Number(inputProcessos.origem.value) === 2 || Number(inputProcessos.origem.value) === 3) {

            if (inputProcessos.tipo.value === "") {
                alert("Campo Tipo de Processo é de preenchimento obrigatório.");
                return false;
            }

            if (inputProcessos.numero.value.trim() === "") {
                alert("Campo Número do Processo é de preenchimento obrigatório.");
                return false;
            }
        }
        return true;
    }

    function validarDadosAssentamento()
    {
        var dadosAssentamento = {
            origem : codigoTipoAssentamento,
            destino : $F('h12_assent')
        };
        var erroRetorno = true;
        AjaxRequest.create(
            sUrlRPC,
            {
                'sExec' : 'validarDadosAssentamento',
                'dadosAssentamento' : dadosAssentamento,
                'possuiProcessoEsocial' : possuiProcessoEsocial
            },
            function (retorno, erro) {
                obrigarRetificacao = retorno.obrigarRetificacao;
                if (erro) {
                    alert(retorno.sMensagem.urlDecode());
                    $('h12_assent').value = codigoTipoAssentamento;
                    $('h12_assentdescr').value = descricaoTipoAssentamento;
                    erroRetorno = false;
                }
            }
        ).asynchronous(false).setMessage('Aguarde, verificando dados do assentamento...').execute();
        return erroRetorno;
    }

    function validacoesMensagem(dataInicial, dataFinal, dataCalculada, dias) {

        if (dataInicial.compararData(dataCalculada, COMPARACAO_MAIOR)) {
            alert(`Data inicial não deve ser superior à ${dias} dias da data atual.`);
            return false;
        }
        if (dataFinal.compararData(dataCalculada, COMPARACAO_MAIOR)) {
            alert(`Data final não deve ser superior à ${dias} dias da data atual.`);
            return false;
        }

        return true;
    }
    function validaPeriodoAfastamento()
    {
        var motivo = document.querySelector("[nome_atributo='motivo_esocial']");

        if (motivo == null) {
            return true;
        }

        if ($F('h16_dtterm') == '') {
            return true;
        }

        var codigoMotivo = Number(motivo.value);
        var dataInicial = Date.convertFrom($F('h16_dtconc'), DATA_PTBR);
        var dataFinal = Date.convertFrom($F('h16_dtterm'), DATA_PTBR);
        var dataAtual = new Date();

        if (codigoMotivo == 15) {
            var dataAtual60dias = somaDataDiaMesAno(dataAtual, 59, 0, 0);
            if (!validacoesMensagem(dataInicial, dataFinal, dataAtual60dias, 60)) {
                return false
            }
        } else if ([17, 18, 19, 20, 33].in_array(codigoMotivo)) {
            switch (codigoMotivo) {
                case 17:
                    var dataAtual120dias = somaDataDiaMesAno(dataAtual, 119, 0, 0);
                    console.log(dataAtual120dias.toLocaleString());
                    if (!validacoesMensagem(dataInicial, dataFinal, dataAtual120dias, 120)) {
                        return false
                    }
                    break;
                case 18:
                case 33:
                    var dataAtual180dias = somaDataDiaMesAno(dataAtual, 179, 0, 0);
                    console.log(dataAtual180dias.toLocaleString());
                    if (!validacoesMensagem(dataInicial, dataFinal, dataAtual180dias, 180)) {
                        return false
                    }
                    break;
                default:
                    return true;
            }
        }

        return true;
    }

    function js_verificacampos2()
    {
        var oParametros = new Object();

        if (!validarRetificacaoProcessos()) {
            return false;
        }
        //Caso inclusao individual
        if (lancamento.value ==  1) {
            if (document.form1.h16_regist.value == "") {
                alert("Informe a matrícula.");
                document.form1.h16_regist.focus();
                return false;
            }
        } else {
            oParametros.lote = true;
            // Caso selecao
            if (tipoPesquisa.value == 1) {
                // const selecao = document.getElementById("r44_selec");
                if (document.form1.r44_selec.value == "") {
                    alert("Informe o código da seleção.");
                    document.form1.r44_selec.focus();
                    return false;
                }
                oParametros.selecao = document.form1.r44_selec.value;
            } else {
                if (oLancadorMatriculas.getRegistros().length == 0 ) {
                    alert("Informe ao menos uma matrícula.");
                    return false;
                }
            }
        }

        if (document.form1.h16_dtconc_dia.value == ""
            || document.form1.h16_dtconc_mes.value == ""
            || document.form1.h16_dtconc_ano.value == "")
        {
            alert("Informe a data inicial.");
            document.form1.h16_dtconc.focus();
            document.form1.h16_dtconc.select();
            return false;
        }

        if (!validaPeriodoAfastamento()) {
            return false;
        }

        // Natureza Justificativa
        if (document.form1.h12_natureza_novo_tipo.value == 5) {
            if (!validarPeriodosJustificativa()) {
                return false;
            }
        }
        // Natureza HE Manual

        if (document.form1.h12_natureza_novo_tipo.value == 8) {
            if (!validarHorasExtrasManuais()) {
                return false;
            }
        }

        oParametros.codigoServidor = $F('h16_regist');
        oParametros.dataInicioAfastamento = $F('h16_dtconc');
        oParametros.tipoAssentamento = $F('h12_assent');
        oParametros.codigoAssentamento = $F('h16_codigo');
        var oRetorno = {};
        oParametros.sExec = 'validarUltimoAfastamentoServidorApiEsocial';

        js_divCarregando('Aguarde, Consultando assentamentos do servidor...', 'oCarregando');

        var ajax = new Ajax.Request(
            sUrlRPC,
            {
                method : 'post',
                asynchronous : false,
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: function (oAjax) {
                    js_removeObj("oCarregando");
                    oRetorno = JSON.parse(oAjax.responseText);
                }
            }
        );
        if (oRetorno.erro) {

            alert(oRetorno.sMensagem.urlDecode());
            return false;
        }

        if ($F('h12_natureza_novo_tipo') == 7 || $F('h12_natureza_novo_tipo') == 9) {
            if (empty($F('h16_hora'))) {
                alert("Informe a quantidade de horas");
                $('h16_hora').focus();
                return false;
            }
        }

        /*
         * Validação se existe o mesmo tipo de assentamento dentro do período informado 
         */
        var oParametrosValidacaoExistenciaAssentamentoPeriodo = new Object();
        oParametrosValidacaoExistenciaAssentamentoPeriodo.codigoServidor = $F('h16_regist');
        oParametrosValidacaoExistenciaAssentamentoPeriodo.dataInicioAfastamento = $F('h16_dtconc');
        oParametrosValidacaoExistenciaAssentamentoPeriodo.tipoAssentamento = $F('h12_codigo');
        oParametrosValidacaoExistenciaAssentamentoPeriodo.sExec = 'verificarExistenciaAssentamentoPeriodo';
        
        var oRetornoValidacaoExistenciaAssentamentoPeriodo = {};

        var ajax = new Ajax.Request(
            sUrlRPC,
            {
                method : 'post',
                asynchronous : false,
                parameters: 'json=' + Object.toJSON(oParametrosValidacaoExistenciaAssentamentoPeriodo),
                onComplete: function (oAjax) {
                    js_removeObj("oCarregando");
                    oRetornoValidacaoExistenciaAssentamentoPeriodo = JSON.parse(oAjax.responseText);
                }
            }
        );

        if (oRetornoValidacaoExistenciaAssentamentoPeriodo.existeAssentamento) {
            msgConfirmacao  = "Já existe lançamento com a data informada para este tipo de assentamento!\n";
            msgConfirmacao += "Deseja prosseguir com a operação?";
            if (!confirm(msgConfirmacao)) {
               return false;
            }
        }
        
        if ($('h12_vinculaperiodoaquisitivo').value == 't' && $('db_opcao').value != '3') {
            if ($F('h16_dtterm') == '') {
                alert(_M(MENSAGENS + 'data_final_obrigatorio'));
                return false;
            }
            oParametros = {}
            oParametros.sExec = 'validaSaldoDiasDireito';
            oParametros.iCodigoPeriodoAquisitivo = $F('iPeriodoAquisitivo');
            oParametros.iTipoAssentamento = $F('h16_assent');
            oParametros.iDias = $F('quantidade');
            oParametros.iSequencialAssentamento = $F('h16_codigo');

            var oRetorno = {};

            oDadosRequisicao = {}
            oDadosRequisicao.method = 'POST';
            oDadosRequisicao.asynchronous = false;
            oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
            oDadosRequisicao.onComplete = function (oAjax) {
                js_removeObj('oCarregando');

                oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.iStatus == "2") {
                    alert(oRetorno.sMensagem.urlDecode());
                    return false;
                }

                /**
                 * Valida se não foi adicionado mesmo periodo de gozo no mesmo periodo.
                 */
                oParametros.sExec = 'validaPeriodoDiasDireito';
                oParametros.iTipoAssentamento = $F('h16_assent');
                oParametros.sDataInicial = $F('h16_dtconc');
                oParametros.sDataFinal = $F('h16_dtterm');
                oParametros.iServidor = $F('h16_regist');
                oParametros.iSequencialAssentamento = $F('h16_codigo');
                oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
                oDadosRequisicao.onComplete = function (oAjax) {
                    oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.iStatus == "2") {
                        alert(_M(oRetorno.sMensagem.urlDecode()));
                        return false;
                    }
                    return true;
                }
                var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
            }
            js_divCarregando('Aguarde, Carregando Dias de Direito ...', 'oCarregando');
            var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
            return oRetorno.iStatus == "1";
        }
        return true;
    }

    function js_verificacampos()
    {
        const dadosGrid = gridExames.getCollection().get();
        const exames = [];
        dadosGrid.each(exame => {
            exames.push({
                codigo: exame.sequencial,
                data: exame.data,
                codigoProcedimento: exame.codigoProcedimento,
                codigoOrdem: exame.codigoOrdem,
                codigoResultado: exame.codigoResultado,
                observacao:exame.observacao
            });
        });
        inputExames.value = JSON.stringify(exames);

        if (!validarRetificacaoProcessos()) {
            return false;
        }

        if (document.form1.h16_regist.value == "") {
            alert("Informe a matrícula.");
            document.form1.h16_regist.focus();
            return false;
        } else {
            if (document.form1.h16_dtconc_dia.value == ""
                || document.form1.h16_dtconc_mes.value == ""
                || document.form1.h16_dtconc_ano.value == "")
            {
                alert("Informe a data inicial.");
                document.form1.h16_dtconc.focus();
                document.form1.h16_dtconc.select();
                return false;
            }
        }

        if (!validaPeriodoAfastamento()) {
            return false;
        }

        // Natureza Justificativa
        if (document.form1.h12_natureza_novo_tipo.value == 5) {
            if (!validarPeriodosJustificativa()) {
                return false;
            }
        }

        // Natureza HE Manual
        if (document.form1.h12_natureza_novo_tipo.value == 8) {
            if (!validarHorasExtrasManuais()) {
                return false;
            }
        }

        var oParametros = new Object();
        oParametros.codigoServidor = $F('h16_regist');
        oParametros.dataInicioAfastamento = $F('h16_dtconc');
        oParametros.tipoAssentamento = $F('h12_assent');
        oParametros.codigoAssentamento = $F('h16_codigo');
        var oRetorno = {};

        oParametros.sExec = 'validarUltimoAfastamentoServidorApiEsocial';

        js_divCarregando('Aguarde, Consultando assentamentos do servidor...', 'oCarregando');
        var ajax = new Ajax.Request(
            sUrlRPC,
            {
                method : 'post',
                asynchronous : false,
                parameters: 'json=' + Object.toJSON(oParametros),
                onComplete: function (oAjax) {
                    js_removeObj("oCarregando");
                    oRetorno = JSON.parse(oAjax.responseText);
                }
            }
        );

        if (oRetorno.erro) {
            alert(oRetorno.sMensagem.urlDecode());
            return false;
        }

        // if (!oRetorno.isDataInicialAfastamentoValido) {
        //     alert(_M(MENSAGENS + "data_inicio_menor_data_fim_api_esocial"));
        //     return false;
        // }

        if ($F('h12_natureza_novo_tipo') == 7 || $F('h12_natureza_novo_tipo') == 9) {
            if (empty($F('h16_hora'))) {
                alert("Informe a quantidade de horas");
                $('h16_hora').focus();
                return false;
            }
        }

        if ($('h12_vinculaperiodoaquisitivo').value == 't' && $('db_opcao').value != '3') {
            if ($F('h16_dtterm') == '') {
                alert(_M(MENSAGENS + 'data_final_obrigatorio'));
                return false;
            }
            oParametros = {}
            oParametros.sExec = 'validaSaldoDiasDireito';
            oParametros.iCodigoPeriodoAquisitivo = $F('iPeriodoAquisitivo');
            oParametros.iTipoAssentamento = $F('h16_assent');
            oParametros.iDias = $F('quantidade');
            oParametros.iSequencialAssentamento = $F('h16_codigo');

            var oRetorno = {};

            oDadosRequisicao = {}
            oDadosRequisicao.method = 'POST';
            oDadosRequisicao.asynchronous = false;
            oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
            oDadosRequisicao.onComplete = function (oAjax) {
                js_removeObj('oCarregando');

                oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.iStatus == "2") {
                    alert(oRetorno.sMensagem.urlDecode());
                    return false;
                }

                /**
                 * Valida se não foi adicionado mesmo periodo de gozo no mesmo periodo.
                 */
                oParametros.sExec = 'validaPeriodoDiasDireito';
                oParametros.iTipoAssentamento = $F('h16_assent');
                oParametros.sDataInicial = $F('h16_dtconc');
                oParametros.sDataFinal = $F('h16_dtterm');
                oParametros.iServidor = $F('h16_regist');
                oParametros.iSequencialAssentamento = $F('h16_codigo');
                oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
                oDadosRequisicao.onComplete = function (oAjax) {
                    oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.iStatus == "2") {
                        alert(_M(oRetorno.sMensagem.urlDecode()));
                        return false;
                    }
                    return true;
                }
                var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
            }
            js_divCarregando('Aguarde, Carregando Dias de Direito ...', 'oCarregando');
            var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
            return oRetorno.iStatus == "1";
        }
        return true;
    }


    function js_somar_dias(valor, opcao)
    {
        diai = new Number(document.form1.h16_dtconc_dia.value);
        mesi = new Number(document.form1.h16_dtconc_mes.value);
        anoi = new Number(document.form1.h16_dtconc_ano.value);

        diaf = new Number(document.form1.h16_dtterm_dia.value);
        diaf++;
        mesf = new Number(document.form1.h16_dtterm_mes.value);
        anof = new Number(document.form1.h16_dtterm_ano.value);

        if (diai != 0 && mesi != 0 && anoi != 0 && valor != "" && opcao != 3) {
            valor = new Number(valor);
            data = new Date(anoi, (mesi - 1), (diai + valor - 1));

            dia = data.getDate();
            mes = data.getMonth() + 1;
            ano = data.getFullYear();

            document.form1.h16_quant.value = valor;
            document.form1.h16_dtterm_dia.value = dia < 10 ? "0" + dia : dia;
            document.form1.h16_dtterm_mes.value = mes < 10 ? "0" + mes : mes;
            document.form1.h16_dtterm_ano.value = ano;
            document.form1.h16_dtterm.value = document.form1.h16_dtterm_dia.value
                + '/' + document.form1.h16_dtterm_mes.value + '/' + document.form1.h16_dtterm_ano.value;
            document.form1.h16_dtterm.value = (dia < 10 ? "0" + dia : dia)
                + '/' + (mes < 10 ? "0" + mes : mes) + '/' + ano;
        } else if (diai != 0 && mesi != 0 && anoi != 0 && diaf != 0 && mesf != 0 && anof != 0 && opcao == 3) {
            datai = new Date(anoi, (mesi - 1), diai);
            dataf = new Date(anof, (mesf - 1), diaf);

            datad = (dataf - datai) / 86400000;
            document.form1.h16_quant.value = datad.toFixed();
            document.form1.quantidade.value = datad.toFixed();

            if (datad.toFixed() <= 0) {
                alert('A data final não pode ser menor que a data inicial');
                document.form1.h16_dtterm_dia.value = '';
                document.form1.h16_dtterm_mes.value = '';
                document.form1.h16_dtterm_ano.value = '';
                document.form1.h16_dtterm.value = '';
                document.form1.h16_dtterm.focus();
                document.form1.h16_quant.value = '';
                document.form1.quantidade.value = '';
                return false;
            }

            ano = datad / 365;
            ano = ano.toFixed();
            mes = (datad - (ano * 365)) / 30;
            mes = mes.toFixed();
            dia = datad - (ano * 365) - (mes * 30);
            dia = dia.toFixed();

            if (document.form1.valor_dia) {
                document.form1.valor_dia.value = dia;
                document.form1.valor_mes.value = mes;
                document.form1.valor_ano.value = ano;
                document.form1.valor.value = dia + '/' + mes + '/' + ano;
            }
        } else if (opcao == 2) {
            alert("Informe a data inicial!");
            document.form1.h16_dtconc.focus();
            document.form1.h16_dtconc.select();
            document.form1.quantidade.value = "";
        }

        if (document.form1.h16_dtterm.value == '') {
            document.form1.quantidade.value = "0";
            document.form1.h16_quant.value = "0";
        }

        quant_dias = new Number(document.form1.quantidade.value);
        if (quant_dias == 0) {
            document.form1.h16_dtterm_dia.value = '';
            document.form1.h16_dtterm_mes.value = '';
            document.form1.h16_dtterm_ano.value = '';
            document.form1.h16_dtterm.value = '';
        }
        buscaJornada();
    }

    function js_pesquisah16_regist(mostra)
    {
        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_rhpessoal',
                'func_rhpessoal.php?lFormularioAfastamento'
                    + '=true&funcao_js=parent.js_mostrarhpessoal1|rh01_regist|z01_nome',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.h16_regist.value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_rhpessoal',
                    'func_rhpessoal.php?lFormularioAfastamento=true&pesquisa_chave='
                        + document.form1.h16_regist.value + '&funcao_js=parent.js_mostrarhpessoal',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostrarhpessoal(chave, erro)
    {
        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.h16_regist.focus();
            document.form1.h16_regist.value = '';
        }
        buscaJornada();
        js_limpaPeriodoAquisitivo();
    }

    function js_mostrarhpessoal1(chave1, chave2)
    {
        document.form1.h16_regist.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_rhpessoal.hide();

        buscaJornada();
        js_limpaPeriodoAquisitivo();
    }

    function js_pesquisah12_codigo(carregamentoInicial)
    {
        if(possuiProcessoEsocial && carregamentoInicial == undefined) {
            if (document.form1.h16_codigo.value !== '' && !validarDadosAssentamento()) {
                return false;
            }
        }

        var iframeParent = 'parent.';
        if (CurrentWindow.corpo === frameElement.ownerDocument.defaultView) {
            iframeParent = 'parent.frames.IFassentamentofuncional.';
        }

        if (document.form1.h12_codigo.value != '') {
           js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_tipoasse',
                <?php
                if (isset($iTipoFuncionamento) && $iTipoFuncionamento == 1) {
                    echo "'func_tipoasse.php?filtro_departamento=true&'";
                } else {
                    echo "'func_tipoasse.php?'";
                }
                ?>
                + 'lPesquisaNatureza=true&pesquisa_chave='+document.form1.h12_codigo.value
                + '&funcao_js=' + iframeParent
                + 'js_mostratipoasse_codigo',
                'Pesquisa',
                false
           );
        } else {
                document.form1.h12_assent.value = '';
                document.form1.h12_codigo.value = '';
                document.form1.submit();
                return;
        }
    }

    function js_mostratipoasse_codigo(assent, erro, descr, natureza, lVinculaPeriodoAquisitivo)
    {
        if (erro) {
            document.form1.h12_codigo.value = '';
            document.form1.h12_assent.value = '';
            document.form1.h12_assentdescr.value = '';
            return false;
        }
        document.form1.h16_assent.value = assent;
        document.form1.h12_assent.value = assent;
        document.form1.h12_natureza_novo_tipo.value = natureza;

        var sel1 = document.form1.elements["h12_assent"];
        var sel2 = document.form1.elements["h12_assentdescr"];

        for (var i = 0; i < sel1.options.length; i++) {
            if (sel1.options[i].value == assent) {
                sel1.options[i].selected = true;
                sel2.options[i].selected = true;
                break;
            }
        }

        posRetornoTipoAssentamento(lVinculaPeriodoAquisitivo);
    }
        
    function js_pesquisah12_assent(lMostra, carregamentoInicial)
    {
        if(possuiProcessoEsocial && carregamentoInicial == undefined) {
            if (document.form1.h16_codigo.value !== '' && !validarDadosAssentamento()) {
                return false;
            }
        }

        var iframeParent = 'parent.';
        if (CurrentWindow.corpo === frameElement.ownerDocument.defaultView) {
            iframeParent = 'parent.frames.IFassentamentofuncional.';
        }

        if (lMostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_tipoasse',
                <?php
                if (isset($iTipoFuncionamento) && $iTipoFuncionamento == 1) {
                    echo "'func_tipoasse.php?filtro_departamento=true&'";
                } else {
                    echo "'func_tipoasse.php?'";
                }?>
                    + 'funcao_js=' + iframeParent
                    + 'js_mostratipoasse1|h12_codigo|h12_assent|h12_descr|h12_natureza|h12_vinculaperiodoaquisitivo|',
                'Pesquisa',
                true
            );
        } else {
            if (document.form1.h12_assent.value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_tipoasse',
                    <?php
                    if (isset($iTipoFuncionamento) && $iTipoFuncionamento == 1) {
                        echo "'func_tipoasse.php?filtro_departamento=true&'";
                    } else {
                        echo "'func_tipoasse.php?'";
                    }?>
                        + 'chave_assent=' + document.form1.h12_assent.value
                        + '&funcao_js=' + iframeParent + 'js_mostratipoasse',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.h12_assent.value = '';
                // document.form1.h16_assent.value = '';
                document.form1.submit();
                return;
            }
        }
    }

    function js_mostratipoasse(chave, chave2, erro, chave3, chave4, lVinculaPeriodoAquisitivo)
    {

        var lExclusao = document.form1.h12_assent.readOnly;

        document.form1.h12_assent.value = lExclusao ? chave : chave3;
        document.form1.h12_assent.value = chave;

        if (erro == true) {
            document.form1.h12_codigo.value = '';
            document.form1.h12_assent.value = '';
            document.form1.h12_assent.focus();
        } else {
            document.form1.h12_codigo.value = chave3;
            document.form1.h16_assent.value = chave3;
            document.form1.h12_natureza_novo_tipo.value = chave4;
        }

        posRetornoTipoAssentamento(lVinculaPeriodoAquisitivo);
    }

    function js_mostratipoasse1(chave1, chave2, chave3, chave4, lVinculaPeriodoAquisitivo)
    {

        document.form1.h16_assent.value = chave1;
        document.form1.h12_codigo.value = chave1;
        document.form1.h12_assent.value = chave2;
        document.form1.h12_natureza_novo_tipo.value = chave4;

        var sel1 = document.form1.elements["h12_assent"];
        var sel2 = document.form1.elements["h12_assentdescr"];

        for (var i = 0; i < sel1.options.length; i++) {
            if (sel1.options[i].value == chave2) {
                sel1.options[i].selected = true;
                sel2.options[i].selected = true;
                break;
            }
        }

        posRetornoTipoAssentamento(lVinculaPeriodoAquisitivo);

    }

    function posRetornoTipoAssentamento(lVinculaPeriodoAquisitivo)
    {

        buscaJornada();
        if (db_iframe_tipoasse) {
           db_iframe_tipoasse.hide();
        }
        
        js_verificaVinculoPeriodoAquisitivo((lVinculaPeriodoAquisitivo == 't'));
        js_ocultaControleMedico();
        js_exibeControleMedico();
        renderizarFormulario();
        js_carregarRegistros();

        if (document.form1.h16_assent.value.trim() != ""
            && document.form1.h12_natureza_novo_tipo.value == document.form1.h12_natureza.value)
        {
            js_criarCamposAdicionais($F("h16_codigo"));
        } else {
            js_criarCamposAdicionais();
        }
    }

    /**
     * Cria campos adicionais na tela conforme natureza do assentamento
     * @return void
     */
    function js_criarCamposAdicionais(iCodigoAssentamento)
    {
        $('conteudoCamposAdicionais').innerHTML = '';
        require_once("scripts/classes/recursoshumanos/TipoAssentamentoFactory.js");

        if (!empty(iCodigoAssentamento)) {
            db_opcao = <?= isset($db_opcao) ? $db_opcao : ''?>;
            var oTipoAssentamento = TipoAssentamentoFactory.createFromAssentamento(iCodigoAssentamento);
        }

        if (!empty($F('h12_codigo')) && empty(iCodigoAssentamento)) {
            var oTipoAssentamento = TipoAssentamentoFactory.createFromTipoAssentamento($F('h12_codigo'));
        }

        if (oTipoAssentamento != undefined && oTipoAssentamento != 'undefined') {
            oTipoAssentamento.setDestino($('conteudoCamposAdicionais'));
            oTipoAssentamento.show();
        }
        delete db_opcao;
        return;
    }

    function js_pesquisa()
    {
        <?php
        if ($meiodia == true) { ?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_assmeio',
                'func_assmeio.php?funcao_js=parent.js_preenchepesquisa'
                    + '|h22_codigo<?php echo ($db_opcao == 33 || $db_opcao == 3? "" : "&bloqueia_assenta=true"); ?>',
                'Pesquisa',
                true
            );
            <?php
        } else {
            if (isset($registr) && $registr != null) {
                $chavecod = "&chave_h16_regist=" . $registr;
            } else {
                $chavecod = "";
            }?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_assenta',
                'func_assenta.php?iTipoFuncionamento=<?= (!isset($iTipoFuncionamento)) ? 2 : $iTipoFuncionamento;?>'
                    + '&funcao_js=parent.js_preenchepesquisa|h16_codigo' + '<?php echo $chavecod?>'
                    + '<?php echo($db_opcao == 2 || $db_opcao == 22 ? "&bloqueia_reajuste=true" : ""); ?>',
                'Pesquisa',
                true
            );
            <?php
        }?>
    }

    function js_pesquisa2()
    {
        <?php if ($meiodia == true) { ?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_assmeio',
                'func_assmeio.php?funcao_js=parent.js_preenchepesquisa'
                    + '|h22_codigo<?php echo ($db_opcao == 33 || $db_opcao == 3? "" : "&bloqueia_assenta=true");?>'
                    + '&chave_h16_regist=' + document.form1.h16_regist.value,
                'Pesquisa',
                true
            );
        <?php } else { ?>
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_assenta',
                'func_assenta.php?funcao_js=parent.js_preenchepesquisa|h16_codigo'
                    + '<?php echo($db_opcao == 2 || $db_opcao == 22 ? "&bloqueia_reajuste=true" : "");?>'
                    + '&chave_h16_regist=' + document.form1.h16_regist.value,
                document.form1.h16_regist.value + ' - ' + document.form1.z01_nome.value,
                true
            );
        <?php } ?>
    }

    function js_preenchepesquisa(chave)
    {
        <?php if ($meiodia == true) { ?>
            db_iframe_assmeio.hide();
        <?php } else { ?>
            db_iframe_assenta.hide();
        <?php }?>
        <?php
        if ($db_opcao != 1) {
            if (isset($bloqueia)) {
                $bloque = "&bloqueia=true";
            } else {
                $bloque = "";
            }
            $sUrlToRedirect = " location.href = '" . basename($_SERVER["PHP_SELF"])
                . "?chavepesquisa='+chave+'" . $bloque . "'";

            if (!isset($iTipoFuncionamento)) {
                $iTipoFuncionamento = 2;
            }
            $sUrlToRedirect .= "+'&iTipoFuncionamento={$iTipoFuncionamento}'";
            $sUrlToRedirect = $sUrlToRedirect;
            echo $sUrlToRedirect;
        }
        ?>
    }

    /**
     * Abre a func de pesquisa e busca o período aquisitivo
     *
     * @param Boolean lMostra -- Se deve ou não exibir a janela de pesquisa
     */
    function js_pesquisaPeriodoAquisitivo(lMostra)
    {
        var iRegist = $('h16_regist').value

        if (iRegist == '') {
            alert(_M(MENSAGENS + "servidor_nao_informado"));
            return;
        }
        if (lMostra) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_rhferias',
                'func_rhferias.php?rh109_regist=' + iRegist
                    + '&funcao_js=parent.js_mostraPeriodoAquisitivo'
                    + '|rh109_sequencial|rh109_periodoaquisitivoinicial|rh109_periodoaquisitivofinal|',
                'Pesquisa Período Aquisitivo',
                true
            );
        } else {

            if ($F('iPeriodoAquisitivo') != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_rhferias',
                    'func_rhferias.php?pesquisa_chave=' + $F('iPeriodoAquisitivo')
                        + '&funcao_js=parent.js_mostraPeriodoAquisitivo1',
                    'Pesquisa Período Aquisitivo',
                    false
                );
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    /**
     * Exibe os campos do período aquisitivo
     *
     * @param Integer iSequencial
     * @param String dtInicio
     * @param String dtFim
     */
    function js_mostraPeriodoAquisitivo(iSequencial, dtInicio, dtFim)
    {

        js_limpaPeriodoAquisitivo();
        $('iPeriodoAquisitivo').value = iSequencial;
        $('dtPeriodoAquisitivoInicio').value = dtInicio.replace(/(\d{4})-(\d{2})-(\d{2})/, '$3/$2/$1');
        $('dtPeriodoAquisitivoFinal').value = dtFim.replace(/(\d{4})-(\d{2})-(\d{2})/, '$3/$2/$1');

        if (db_iframe_rhferias) {
            db_iframe_rhferias.hide();
        }

        var oParametros = new Object(),
        oDadosRequisicao = new Object();

        oParametros.sExec = 'getSaldoDiasDireito';
        oParametros.iCodigoPeriodoAquisitivo = $F('iPeriodoAquisitivo');
        oParametros.iCodigoAssentamento = $F('h16_codigo');

        oDadosRequisicao.method = 'POST';
        oDadosRequisicao.asynchronous = false;
        oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
        oDadosRequisicao.onComplete = function (oAjax) {
            js_removeObj('oCarregando');

            var oRetorno = JSON.parse(oAjax.responseText);

            if (oRetorno.iStatus == "2") {
                js_limpaPeriodoAquisitivo();
                alert(oRetorno.sMensagem.urlDecode());
                return;
            }

            $('iSaldoDias').value = oRetorno.iDiasDireito || '0';
        }

        js_divCarregando('Aguarde, Carregando Dias de Direito ...', 'oCarregando');

        var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
    }

    /**
     * Função chamada na alteração ou exclusão
     *
     * @param Integer iSequencial
     * @param Boolean lErro
     * @param String dtInicio
     * @param String dtFim
     */
    function js_mostraPeriodoAquisitivo1(iSequencial, lErro, dtInicio, dtFim)
    {
        if (lErro) {
            js_limpaPeriodoAquisitivo();
            return false;
        }

        js_mostraPeriodoAquisitivo(iSequencial, dtInicio, dtFim);
    }

    /**
     * Verifica se deve exibir os campos do periodo aquisitivo e carregar os dados do mesmo
     *
     * @param Boolean lExibe
     */
    function js_verificaVinculoPeriodoAquisitivo(lExibe)
    {
        $$('.celulas-hora').each(function (celula) {
            celula.style.display = 'none';
        });
        $$('.celulas-periodos-justificativa').each(function (celula) {
            celula.style.display = 'none';
        });
        $$('.hora-extra-manual')[0].style.display = 'none';

        $('DataFim').closest('td').setStyle('display: table-cell;');

        $$('.data-digitacao').each(function (el) {
            el.setStyle('display:table-cell');
        });

        $$('.campos-quantidade').each(function (el) {
            el.setStyle('display: table-cell;');
        });
        $$('tr.campos-quantidade').each(function (el) {
            el.setStyle('display: table-row;');
        });

        $$('#DataIni strong').first().innerHTML = 'Data inicial:';

        if ($F('h12_natureza_novo_tipo') == 7) {
            $$('.data-digitacao').each(function (el) {
                el.setStyle('display:none');
            });

            $$('#DataIni strong').first().innerHTML = 'Data:';

            if ($F('h16_dtterm') == '' || $F('h16_dtterm') == null) {
                $('DataFim').closest('td').setStyle('display: none;');
                $('dtjs_h16_dtterm').closest('td').setStyle('display:none');
            }

            $$('.campos-quantidade').each(function (el) {
                el.setStyle('display: none;');
            });
        }

        if ($F('h12_natureza_novo_tipo') == 4 || $F('h12_natureza_novo_tipo') == 7
            || $F('h12_natureza_novo_tipo') == 9)
        {

            $$('.celulas-hora').each(function (celula) {
                celula.style.display = 'table-cell';
            });

            if ($F('h12_natureza_novo_tipo') != 9) {
                $$('.celulas-hora-periodo').each(function (celula) {
                    celula.style.display = 'none';
                });
            } else {
                $$('.celulas-hora-periodo').each(function (celula) {
                    celula.style.display = 'table-row';
                });
            }

            if ($F('h12_natureza_novo_tipo') == 7 && ($F('h16_hora') == '' || $F('h16_hora') == null)) {
                $('h16_hora').value = '23:59';
            }
        }

        if (document.form1.h12_natureza_novo_tipo.value == 5) {
            $$('.celulas-periodos-justificativa').each(function (celula) {
                celula.style.display = 'table-row';
            });
        }

        if (document.form1.h12_natureza_novo_tipo.value == 8) {
            $$('.hora-extra-manual')[0].style.display = 'table-row';
        }

        $('h12_vinculaperiodoaquisitivo').value = 'f';
        js_limpaPeriodoAquisitivo();

        $$('.vinculoperiodoaquisitivo').each(function (oElemento) {
            oElemento.hide();
        });

        if (lExibe && !$F('lBloqueiaPeriodoAquisitivo')) {
            $('h12_vinculaperiodoaquisitivo').value = 't';
            $$('.vinculoperiodoaquisitivo').each(function (oElemento) {
                oElemento.show();
            });

            if ($F('h16_codigo') != '') {
                var oParametros = new Object(),
                oDadosRequisicao = new Object();

                oParametros.sExec = 'getPeriodoAquisitivo';
                oParametros.iCodigoAssenta = $F('h16_codigo');
                oParametros.iMatriculaServidor = $F('h16_regist');

                oDadosRequisicao.method = 'POST';
                oDadosRequisicao.asynchronous = false;
                oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
                oDadosRequisicao.onComplete = function (oAjax) {

                    js_removeObj('oCarregando');

                    var oRetorno = JSON.parse(oAjax.responseText.urlDecode());

                    if (oRetorno.iStatus == "2") {
                        js_limpaPeriodoAquisitivo();
                        alert(oRetorno.sMensagem);
                        return;
                    }

                    $('iPeriodoAquisitivo').value = oRetorno.iCodigoPeriodoAquisitivo;
                    $('iPeriodoAquisitivo').onchange();
                }

                js_divCarregando('Aguarde, Carregando Período Aquisitivo ...', 'oCarregando');

                var oAjax = new Ajax.Request(sUrlRPC, oDadosRequisicao);
            }
        }
    }

    /**
     * Limpa os campos do período aquisitivo
     */
    function js_limpaPeriodoAquisitivo()
    {
        $('iPeriodoAquisitivo').value = '';
        $('dtPeriodoAquisitivoInicio').value = '';
        $('dtPeriodoAquisitivoFinal').value = '';
        $('iSaldoDias').value = '';
    }

    /**
     * Chamada da função que atualiza o tipoasse setando se vincula o periodo aquisitivo
     */
    if ($F('h12_assent') != '') {
        js_pesquisah12_assent(false, true);
        obrigarRetificacao = false;
    }

    require_once("scripts/classes/DBViewCadastroAtributoDinamico.js");
    require_once("scripts/classes/DBViewLancamentoAtributoDinamico.js");
    require_once("scripts/datagrid.widget.js");
    require_once("scripts/widgets/dbcomboBox.widget.js");
    require_once("scripts/widgets/dbmessageBoard.widget.js");
    require_once("scripts/widgets/dbtextField.widget.js");
    require_once("scripts/widgets/dbtextFieldData.widget.js");
    require_once("scripts/widgets/windowAux.widget.js");
    require_once("scripts/widgets/Collection.widget.js");
    require_once("scripts/widgets/DatagridCollection.widget.js");
    function renderizarFormulario()
    {
        require_once("scripts/AjaxRequest.js");

        var oAjaxRequest = new AjaxRequest(
            'rec1_assentamentoatributosdinamicos.RPC.php',
            {
                sAcao : 'getDados',
                iCodigoAssentamento : $F('h16_codigo'),
                sTipoAssentamento : $F('h12_assent') //Na verdade é h12_assent
            },
            js_retornoAtributos
        );

        oAjaxRequest.setMessage('Definindo Valores Dinâmicos...');
        oAjaxRequest.asynchronous(false);
        oAjaxRequest.execute();

    }

    $('h12_codigo').observe("change", renderizarFormulario);
    $('h12_codigo').observe("change", js_pesquisah12_codigo);
    $('h12_assentdescr').observe("change", renderizarFormulario);

    var fjs_verificacampos = js_verificacampos2;
    js_verificacampos = function () {
        if (!fjs_verificacampos()) {
            return false;
        }
        if (oAtributoDinamico) {
            oAtributoDinamico.setSaveCallBackFunction(salvar);
            oAtributoDinamico.save();
            return false;
        }

        return true;
    }

    function js_retornoAtributos(oAjaxResponse)
    {
        if (!oAjaxResponse.iCodigoGrupo && !oAjaxResponse.iCodigoFormulario) {
            $('campos_adicionais').innerHTML = "";
            $('h80_db_cadattdinamicovalorgrupo').value = "";
            oAtributoDinamico = null;
            return;
        }

        oAtributoDinamico = new DBViewLancamentoAtributoDinamico();
        oAtributoDinamico.setParentForm('form1');
        oAtributoDinamico.setAlignForm('left');
        oAtributoDinamico.setParentNode($('campos_adicionais'));
        oAtributoDinamico.sCallbackAfterRenderForm = function () {
            if (possuiProcessoEsocial) {
                var atributos = $$('[nome_atributo=motivo_esocial]');
                for (elemento of atributos) {
                   // elemento.disabled = true;
                }
            }
        }
        if (oAjaxResponse.iCodigoGrupo) {
            $('h80_db_cadattdinamicovalorgrupo').value = oAjaxResponse.iCodigoGrupo;
            oAtributoDinamico.loadAttribute(oAjaxResponse.iCodigoGrupo);
        } else {
            oAtributoDinamico.newAttribute(oAjaxResponse.iCodigoFormulario);
        }
    }

    function js_pesquisaServidores(mostra, iTipoFuncionamento)
    {
        if (mostra == true) {
            if (iTipoFuncionamento == 1) {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_rhpessoal',
                    'func_rhpessoal.php?lFormularioAfastamento='
                        + 'true&filtro_departamento=true&funcao_js=parent.js_mostrarhpessoal1|rh01_regist|z01_nome',
                    'Pesquisa',
                    true
                );
            }
            if (iTipoFuncionamento == 2) {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_rhpessoal',
                    'func_rhpessoal.php?lFormularioAfastamento=true&filtro_lotacao=true&funcao_js='
                        + 'parent.js_mostrarhpessoal1|rh01_regist|z01_nome',
                    'Pesquisa',
                    true
                );
            }
        } else {
            if (document.form1.h16_regist.value != '') {

                if (iTipoFuncionamento == 1) {
                    js_OpenJanelaIframe(
                        '',
                        'db_iframe_rhpessoal',
                        'func_rhpessoal.php?lFormularioAfastamento=true&filtro_departamento=true&pesquisa_chave='
                            + document.form1.h16_regist.value + '&funcao_js=parent.js_mostrarhpessoal',
                        'Pesquisa',
                        false
                    );
                }

                if (iTipoFuncionamento == 2) {
                    js_OpenJanelaIframe(
                        '',
                        'db_iframe_rhpessoal',
                        'func_rhpessoal.php?lFormularioAfastamento=true&filtro_lotacao=true&pesquisa_chave='
                            + document.form1.h16_regist.value + '&funcao_js=parent.js_mostrarhpessoal',
                        'Pesquisa',
                        false
                    );
                }
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function salvar(iCodigo)
    {
        $('h80_db_cadattdinamicovalorgrupo').value = iCodigo;
        document.form1.submit();
    }

    if ($F('h80_db_cadattdinamicovalorgrupo')) {
        oAtributoDinamico = new DBViewLancamentoAtributoDinamico();
        oAtributoDinamico.setAlignForm('left');
        oAtributoDinamico.setParentForm('form1');
        oAtributoDinamico.setParentNode($('campos_adicionais'));
        oAtributoDinamico.loadAttribute($F('h80_db_cadattdinamicovalorgrupo'));
    }

    function validarPeriodosJustificativa()
    {
        var periodoJustificativa1 = $('periodoJustificativa1');
        var periodoJustificativa2 = $('periodoJustificativa2');
        var periodoJustificativa3 = $('periodoJustificativa3');

        if($F('db_opcao').toLowerCase() == 'excluir' || $F('db_opcao').toLowerCase() == '3') {
            return true;
        }

        if (periodoJustificativa1.checked == false) {
            if (periodoJustificativa2.checked == false) {
                if (periodoJustificativa3.checked == false) {
                    alert('Informe um período para a Justificativa.');
                    periodoJustificativa1.focus();
                    return false;
                }
            }
        }
        return true;
    }

    function diferencaHoras(horaInicial, horaFinal)
    {
        if ((horaInicial == null || horaFinal == null) || (horaInicial == '' || horaFinal == '')) {
            return '';
        }

        var hIni = horaInicial.split(':');
        var hFim = horaFinal.split(':');
        var horasTotal = parseInt(hFim[0], 10) - parseInt(hIni[0], 10);
        var minutosTotal = parseInt(hFim[1], 10) - parseInt(hIni[1], 10);

        // ve se toal e menor  zero
        if (minutosTotal < 0) {
            minutosTotal += 60;
            horasTotal -= 1;
        }

        var diferenca = completaZeroEsquerda(horasTotal) + ":" + completaZeroEsquerda(minutosTotal);

        AjaxRequest.create(
            'rec4_pontoeletronico.RPC.php',
            {
                'exec' : 'consultarMarcacoes',
                'matricula' : $F('h16_regist'),
                'data' : $F('h16_dtconc'),
                'horaInicio' : horaInicial,
                'horaFim' : horaFinal,
            },
            function (response, error) {
                if (error) {
                    return;
                }
                if (response.mensagem) {
                    alert(response.mensagem);
                }
                if (response.diferenca != 0) {
                    diferenca = response.diferenca;
                }
            }
        ).asynchronous(false).setMessage('Consultando registros de marcações...').execute();
        return diferenca;
    }

    function isHoraInicialMenorHoraFinal(horaInicial, horaFinal)
    {
        horaIni = horaInicial.split(':');
        horaFim = horaFinal.split(':');

        // Verifica as horas. Se forem diferentes, é só ver se a inicial
        // é menor que a final.
        hIni = parseInt(horaIni[0], 10);
        hFim = parseInt(horaFim[0], 10);
        if (hIni != hFim) {
            return hIni < hFim;
        }

        // Se as horas são iguais, verifica os minutos então.
        mIni = parseInt(horaIni[1], 10);
        mFim = parseInt(horaFim[1], 10);
        if (mIni != mFim) {
            return mIni < mFim;
        }
    }

    function completaZeroEsquerda(numero)
    {
        return (numero < 10 ? "0" + numero : numero);
    }


    function validarHorasExtrasManuais()
    {
        var mensagem = "Não foram informadas horas extras.\nAo menos um campo de \"Quantidade de Horas Extras\" deve "
            + "estar preenchido.";

        var horaExtraManual50Diurna = $F('horaExtraManual50Diurna');
        var horaExtraManual50Noturna = $F('horaExtraManual50Noturna');
        var horaExtraManual75Diurna = $F('horaExtraManual75Diurna');
        var horaExtraManual75Noturna = $F('horaExtraManual75Noturna');
        var horaExtraManual100Diurna = $F('horaExtraManual100Diurna');
        var horaExtraManual100Noturna = $F('horaExtraManual100Noturna');

        if ((horaExtraManual50Diurna == null && horaExtraManual50Noturna == null)
            || (horaExtraManual50Diurna == '' && horaExtraManual50Noturna == ''))
        {
            if ((horaExtraManual75Diurna == null && horaExtraManual75Noturna == null)
                || (horaExtraManual75Diurna == '' && horaExtraManual75Noturna == ''))
            {
                if ((horaExtraManual100Diurna == null && horaExtraManual100Noturna == null)
                    || (horaExtraManual100Diurna == '' && horaExtraManual100Noturna == ''))
                {
                    alert(mensagem);
                    return false;
                }
            }
        }
        return true;
    }

    function buscaJornada()
    {
        $('dadosJornadaServidor').value = '';
        if ($F('h12_natureza_novo_tipo') != 9) {
            $('linhaJornadaServidor').setStyle({'display' : 'none'});
            return false;
        }

        $('linhaJornadaServidor').setStyle({'display' : ''});

        if (empty($F('h16_regist'))) {
            return false;
        }

        if (empty($F('h16_dtconc'))) {
            return false;
        }

        var parametros = {
            'exec' : 'buscaJornadaServidor',
            'matricula' : $F('h16_regist'),
            'periodo' : {
                'dataInicio' : $F('h16_dtconc'),
                'dataFim' : $F('h16_dtconc')
            }
        };

        new AjaxRequest(
            'rec4_pontoeletronico.RPC.php',
            parametros,
            function (retorno, erro) {
                var descricaoJornada = '';
                if (parseInt(retorno.jornadas.length) > 0) {
                    for (var jornada of retorno.jornadas) {
                        if (parametros.periodo.dataInicio.replace(/[^\d]/g, '') == jornada.data.replace(/[^\d]/g, '')) {
                            descricaoJornada += jornada.codigo;
                            descricaoJornada += ' - ';
                            descricaoJornada += jornada.horas.join(' / ');
                        }
                    }
                }
                $('dadosJornadaServidor').value = descricaoJornada;
            }
        ).execute();
    }

    const btnDuplicar = document.getElementById('duplicar');
    const tipoAssentamento = document.getElementById('h12_assent');
    const datasAssentamentosDuplicados = document.getElementById('datasAssentamentosDuplicados');
    const dataInicialDuplicata = new DBInputDate(document.getElementById('dataInicialDuplicata'));
    const dataFinalDuplicata = new DBInputDate(document.getElementById('dataFinalDuplicata'));
    const labelDataInicial = document.getElementById('labelDataInicial');
    const labelDataFinal = document.getElementById('labelDataFinal');
    const btnSalvarDuplicata = document.getElementById('salvarDuplicata');

    var windowDuplicar = new windowAux('windowDuplicar', 'Duplicar assentamento', 300, 200);
    windowDuplicar.allowCloseWithEsc(true);
    windowDuplicar.setShutDownFunction(() => {
        windowDuplicar.destroy();
    });

    var tipoOriginal = null;
    var exibeBotaoDuplicar = false;

    function verificaPossibilidadeDuplicataAssentamento()
    {
        const formData = new FormData();
        formData.append('acao', 'tiposAssentamentoPermitemDuplicata');

        HttpClient.post('rec1_assentamentos.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                throw response.mensagem;
            }
            btnDuplicar.setAttribute('type', 'hidden');

            response.tipos.forEach(tipo => {
                if (tipo.tag === tipoAssentamento.value) {
                    return btnDuplicar.setAttribute('type', 'button');
                }
            });
            validaExibicaoDuplicar();
            tipoOriginal = tipoAssentamento.value;
        }).catch(mensagem => alert(mensagem))
    }

    tipoAssentamento.addEventListener('change', (event) => {
        btnDuplicar.style.display = 'none';
        if (event.target.value === tipoOriginal && exibeBotaoDuplicar === true) {
            btnDuplicar.style.display = null;
        }
    });

    btnDuplicar.addEventListener('click', () => {
        datasAssentamentosDuplicados.style.display = 'block';
        windowDuplicar.setContent(datasAssentamentosDuplicados);
        windowDuplicar.show(200, 0, true);
    });

    btnSalvarDuplicata.addEventListener('click', () => {
        // Validamos se a data de termino original e igual ou inferior a data inicial do novo assentamento
        var dataValidacao = Date.convertFrom($F('h16_dtterm'), DATA_PTBR);
        if (dataInicialDuplicata.value === '' || dataInicialDuplicata.value === null) {
            alert("A data inicial deve ser preenchida.");
            return false;
        }
        if (dataInicialDuplicata.value.getTime() <= dataValidacao.getTime()) {
            alert("A data inicial do novo Assentamento deve ser superior a " + dataValidacao.getDateBR() + ".");
            return false;
        }

        if (dataFinalDuplicata.value !== '' && dataFinalDuplicata.value !== null) {
            if (dataFinalDuplicata.value.getTime() <= dataValidacao.getTime()) {
                alert("A nova data inicial deve ser superior a nova data final informada.");
                return false;
            }
            dataFinalDuplicataFormatada = dataFinalDuplicata.value.getDateBR();
        }

        if (oAtributoDinamico !== null) {
            oAtributoDinamico.iGrupoValor = null;
            oAtributoDinamico.setSaveCallBackFunction((codigoGrupoAtributosDinamicos) => {
                duplicarAssentamento(codigoGrupoAtributosDinamicos);
            });
            oAtributoDinamico.save();
        } else {
            duplicarAssentamento(null);
        }
    });

    function duplicarAssentamento(codigoGrupoAtributosDinamicos)
    {
        const data = new FormData(document.getElementById('form1'));
        data.set('h16_dtconc', dataInicialDuplicata.value.toISOString().slice(0, 10));

        if (dataFinalDuplicata.value === '' ||dataFinalDuplicata.value === null) {
            data.set('h16_quant', '0');
        }
        if(dataInicialDuplicata.value <= dataFinalDuplicata.value) {
            let quantidadeDias = (dataFinalDuplicata.value.getDate()+1) - (dataInicialDuplicata.value.getDate()+1);
            quantidadeDias = quantidadeDias+1;
            const quantidadeDiasAfastadoString = quantidadeDias.toString();
            data.set('h16_quant', quantidadeDiasAfastadoString);
        }

        if (dataFinalDuplicata.value !== '' && dataFinalDuplicata.value !== null) {
            data.set('h16_dtterm', dataFinalDuplicata.value.toISOString().slice(0, 10));
        } else {
            data.set('h16_dtterm', "");
        }
        data.delete('h16_dtconc_dia');
        data.delete('h16_dtconc_mes');
        data.delete('h16_dtconc_ano');

        data.delete('h16_dtterm_dia');
        data.delete('h16_dtterm_mes');
        data.delete('h16_dtterm_ano');

        data.append('acao', 'duplicaAssentamento');

        if (codigoGrupoAtributosDinamicos !== null) {
            data.set('h80_db_cadattdinamicovalorgrupo', codigoGrupoAtributosDinamicos);
        }

        HttpClient.post('rec1_assentamentos.RPC.php', {body : data}).then(response => {
            if (response.erro) {
                throw response.mensagem
            }
            if (response.codigo === '' || response.tipoFuncionamento === '') {
                throw 'Assentamento não pode ser duplicado.'
            }

            const assentamentoCodigo = response.codigo;
            const tipoFuncionamento = response.tipoFuncionamento;

            if (!response.erro) {
                alert('O assentamento foi duplicado.');
            }
            location.href = `rec1_assenta002.php?chavepesquisa=${assentamentoCodigo}&`
                + `iTipoFuncionamento=${tipoFuncionamento}`
        }).catch(mensagem => alert(mensagem));
    }

    function validaExibicaoDuplicar()
    {
        var dataTermino = document.getElementById("h16_dtterm");
        if (dataTermino.value === "") {
            btnDuplicar.setAttribute('type', 'hidden');
        } else {
            exibeBotaoDuplicar = true;
        }
    }

    (function() {
        carregarInformacoesProcesso();
    })();

    const trLinhaSelecao = document.getElementById('linhaSelecao');
    const valueH16Regist = document.getElementById('h16_regist');

    if(valueH16Regist.value !== '') {
        trTipoPesquisa.style = 'display: none';
        trLinhaSelecao.style = 'display: none';
    }

    function salvarAssentamento()
    {
        if (js_verificacampos2()) {
            if (oAtributoDinamico !== null) {
                oAtributoDinamico.iGrupoValor = null;
                oAtributoDinamico.setSaveCallBackFunction((codigoGrupoAtributosDinamicos) => {
                    incluirAlterarAssentamento(codigoGrupoAtributosDinamicos);
                });
                oAtributoDinamico.save();
            } else {
                incluirAlterarAssentamento(null);
            }
        }
        return false;
    }

    function incluirAlterarAssentamento(codigoGrupoAtributosDinamicos)
    {
        const data = new FormData(document.getElementById('form1'));

        // Controle Medico
        if (document.form1.h12_natureza_novo_tipo.value == 10) {
            const dadosGrid = gridExames.getCollection().get();
            const exames = [];
            dadosGrid.each(exame => {
                exames.push({
                    codigo: exame.sequencial,
                    data: exame.data,
                    codigoProcedimento: exame.codigoProcedimento,
                    codigoOrdem: exame.codigoOrdem,
                    codigoResultado: exame.codigoResultado,
                    observacao:exame.observacao
                });
            });
            data.append('exames', JSON.stringify(exames));
        }

        data.append('acao', 'incluiAssentamento');

        //Caso inclusao individual
        if (lancamento.value == 2) {
            data.append('lote', true);
            // Caso selecao
            if (tipoPesquisa.value == 1) {
                data.append('selecao', document.form1.r44_selec.value);
            } else {
                const loteMatricula = [];
                oLancadorMatriculas.getRegistros().forEach(function(matricula){
                    loteMatricula.push(matricula.sCodigo);
                });
                data.append('loteMatricula', loteMatricula);
            }
        }

        if (codigoGrupoAtributosDinamicos !== null) {
            data.set('h80_db_cadattdinamicovalorgrupo', codigoGrupoAtributosDinamicos);
        }

        HttpClient.post('rec1_assentamentos.RPC.php', {body : data}).then(response => {
            
            var titulo = "incluido"
            if (opcaoLancamento == 2) {
                var titulo = "alterado"
            }

            if (response.erro && !response.lTemInconsistencias) {
                throw response.mensagem;
            }

            if(response.lTemInconsistencias) {
                if (confirm(response.mensagem)) {
                    const oRelatorioInconsistencia = new EmissaoRelatorio('rec2_assentamentoloteinconsistencias002.php');
                    oRelatorioInconsistencia.open();
                }
                if (response.erro) {
                    return false;
                }
            }

            if (response.codigo === '' || response.tipoFuncionamento === '') {
                throw 'Assentamento não pode ser ' + titulo + '.'
            }

            const assentamentoCodigo = response.codigo;
            const tipoFuncionamento = response.tipoFuncionamento;

            if (!response.erro) {
                alert("Assentamento " + titulo + " com sucesso.");
            }
            if (opcaoLancamento == 1) {
                location.href = `rec1_assenta001.php?`
                    + `iTipoFuncionamento=${tipoFuncionamento}`
            }
            if (opcaoLancamento == 2){
                location.href = `rec1_assenta002.php?chavepesquisa=${assentamentoCodigo}&`
                    + `iTipoFuncionamento=${tipoFuncionamento}`
            }
        }).catch(e => alert(e.replaceAll('\\n', '\n')));
    }

    var titulo = "incluido";
    /**
     * Realiza a busca de seleções retornando o código e descrição da rubrica escolhida;
     */
    function js_pesquisaSelecao(lMostra) {
        if (lMostra) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_selecao',
                'func_selecao.php?funcao_js=parent.js_geraform_mostraselecao1|r44_selec|r44_descr&instit=<?=db_getsession("DB_instit")?>',
                'Pesquisa',
                true
            );
        } else {
            if ($F(r44_selec) != "") {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_selecao',
                    'func_selecao.php?pesquisa_chave=' + $F(r44_selec) + '&funcao_js=parent.js_geraform_mostraselecao&instit=<?=db_getsession("DB_instit")?>',
                    'Pesquisa',
                    false
                );
            } else {
                $(r44_des).setValue("");
            }
        }
    }

    /**
     * Trata o retorno da função js_pesquisaSelecao().
     */
    function js_geraform_mostraselecao(sDescricao, lErro) {
        if (lErro) {
            $(r44_selec).setValue('');
            $(r44_selec).focus();
        }
        $(r44_des).setValue(sDescricao);
    }

    /**
     * Trata o retorno da função js_pesquisaSelecao();
     */
    function js_geraform_mostraselecao1(sChave1, sChave2) {

        $(r44_selec).setValue(sChave1);

        if($(r44_des)) {
            $(r44_des).setValue(sChave2);
        }

        db_iframe_selecao.hide();
    }
</script>
<script>
    /**
     * Controle Medico
     */
    /**
     * funcaoRetorno - Nome da função de retorno para preencher input
     */
    function abreJanelaProcedimentos(funcaoRetorno) {
        js_OpenJanelaIframe(
            '',
            'db_iframe_procedimentos',
            'func_controlemedicoprocedimentos.php?funcao_js='+funcaoRetorno,
            'Procedimentos',
            true
        );
    }

    function js_mostraProcedimentos(codigo, descricao) {
        document.getElementById('codigo_procedimento').value = codigo;
        document.getElementById('descricao_procedimento').value = descricao;
    }

    const inputAdicionaGridExame = document.getElementById('adicionaGridExame');
    var sequencialGrid = 0;
    const containerExames = document.getElementById('container-exames');
    const collectionExames = new Collection().setId('sequencial');
    const inputExames = document.getElementById("exames");
    const inputSequencialExames = document.getElementById("exame-sequencial");
    const gridExames = DatagridCollection.create(collectionExames).configure({'order': false, height: 150});
    const descricaoProcedimento = document.getElementById('descricao_procedimento')
    const dataExame = document.getElementById('h27_data');
    const codigoProcedimento = document.getElementById('codigo_procedimento');
    const observacao = document.getElementById('h27_observacao');
    const codigoOrdem = document.getElementById('h27_ordem');
    const codigoResultado = document.getElementById('h27_resultado');
    const btnIncluir = document.getElementById('adicionaGridExame');
    const dataAtestado = document.getElementById('h26_dataatestado');
    const nomeMedico = document.getElementById('h26_nomemedico');
    const crmMedico = document.getElementById('h26_crmmedico');
    const ufCrm = document.getElementById('h26_ufcrm');
    const cpfResponsavel = document.getElementById('h26_cpfresponsavel');
    const nomeResponsavel = document.getElementById('h26_nomeresponsavel');
    const crmResponsavel = document.getElementById('h26_crmresponsavel');
    const ufCrmResponsavel = document.getElementById('h26_ufcrmresponsavel');
    var inicializa = true;

    gridExames.addColumn('codigoProcedimento', {label: 'Código do Procedimento', align: 'center'});
    gridExames.addColumn('codigoResultado', {label: 'Código do Resultado', align: 'center'});
    gridExames.addColumn('codigoOrdem', {label: 'Código da Ordem', align: 'center'});
    gridExames.addColumn('observacao', {label: 'Observação', align: 'center'});
    gridExames.addColumn('data', {label: 'data', align: 'center', width: '15%'});
    gridExames.addColumn('procedimento', {label: 'Procedimento', align: 'center', width: '40%'});
    gridExames.addColumn('ordem', {label: 'Ordem', align: 'center', width: '15%'});
    gridExames.addColumn('resultado', {label: 'Resultado', align: 'center', width: '15%'});
    gridExames.hideColumns([0,1,2,3])
    gridExames.addAction('Alterar', 'Alterar', (event, linha) => {
        btnIncluir.value = 'Alterar Exame';
        inputSequencialExames.value = linha.sequencial;
        dataExame.value = linha.data;
        descricaoProcedimento.value = linha.procedimento;
        codigoProcedimento.value = linha.codigoProcedimento;
        codigoOrdem.value = linha.codigoOrdem;
        codigoResultado.value = linha.codigoResultado;
        observacao.value = linha.observacao;
    }, true, 'fa-edit');

    gridExames.addAction('Excluir', 'Excluir', (event, linha) => {
        if (confirm(`Deseja excluir o procedimento: ${linha.procedimento}?`)) {
            const data = new FormData();
            data.append('id', linha.sequencial);
            collectionExames.remove(linha.sequencial);
            gridExames.reload();
            btnIncluir.value = 'Adicionar Exame';
            inputSequencialExames.value = "";
        }
    }, true, 'fa-trash');

    function adicionaGridExame() {
        sequencialGrid += 1;
        var sequencial = sequencialGrid;
        var descricaoOrdem = codigoOrdem.options[codigoOrdem.value].text;
        var descricaoResultado = codigoResultado.options[codigoResultado.value].text;

        if (inputSequencialExames.value) {
            sequencial = inputSequencialExames.value;
            inputSequencialExames.value = "";
        }

        if (!dataExame.value) {
            alert("Data do Exame não informada.");
            return false;
        }
        if (!codigoProcedimento.value) {
            alert("Procedimento não informado.");
            return false;
        }
        var dado = {
            sequencial: sequencial,
            data: dataExame.value,
            procedimento: descricaoProcedimento.value,
            resultado: descricaoResultado,
            ordem: descricaoOrdem,
            codigoProcedimento: codigoProcedimento.value,
            codigoOrdem: codigoOrdem.value,
            codigoResultado: codigoResultado.value,
            observacao:observacao.value
        }
        collectionExames.add(dado);
        gridExames.reload();
        btnIncluir.value = 'Adicionar Exame';
    }

    inputAdicionaGridExame.addEventListener('click', () => {
        adicionaGridExame();
    });
    gridExames.show(containerExames);

    function js_ocultaControleMedico() {
        $$('.controle-medico').each(function (el) {
            el.setStyle('visibility:collapse');
        });
        $$('.assenta-old').each(function (el) {
            el.setStyle('visibility:visible');
        });
        resetaCamposControleMedico();
    }

    function resetaCamposControleMedico() {
        if (!inicializa) {
            gridExames.clear();
            collectionExames.clear();
            inputSequencialExames.value = "";
            dataExame.value = "";
            descricaoProcedimento.value = "";
            codigoProcedimento.value = "";
            codigoOrdem.value = 0;
            codigoResultado.value = 0;
            observacao.value = "";
            dataAtestado.value = "";
            nomeMedico.value = "";
            crmMedico.value = "";
            ufCrm.value = "AC";
            cpfResponsavel.value = "";
            nomeResponsavel.value = "";
            crmResponsavel.value = "";
            ufCrmResponsavel.value = "";
        }
        inicializa = false;
    }

    function js_exibeControleMedico()
    {
        if (document.form1.h12_natureza_novo_tipo.value == 10) {
            $$('.campos-quantidade').each(function (el) {
                el.setStyle('display: none;');
            });

            $$('.celulas-hora').each(function (celula) {
                celula.style.display = 'none';
            });
            $$('.celulas-periodos-justificativa').each(function (celula) {
                celula.style.display = 'none';
            });
            $$('.hora-extra-manual')[0].style.display = 'none';

            $$('.data-digitacao').each(function (el) {
                el.setStyle('display:table-cell');
            });

            $$('.controle-medico').each(function (el) {
                el.setStyle('visibility:visible');
            });

            $$('tr.campos-quantidade').each(function (el) {
                el.setStyle('display: none;');
            });

            $$('#DataIni strong').first().innerHTML = 'Data Inicial:';

            $$('.vinculoperiodoaquisitivo').each(function (oElemento) {
                oElemento.hide();
            });

            $$('.assenta-old').each(function (el) {
                el.setStyle('visibility:collapse');
            });
        }
    }



</script>
<script>
    <?php
    if (!empty($aExames) && sizeof($aExames) > 0) {
        $exames = json_encode($aExames);
        echo "
            document.addEventListener(\"DOMContentLoaded\", function(e) {
                var exames = {$exames};
                exames.each(exame => {
                    adicionaExameCollection(exame);
                });
                gridExames.reload();
            });
            ";
    }
    ?>
    const adicionaExameCollection = (exame) => {
        var descricaoOrdem = codigoOrdem.options[exame.codigoOrdem].text;
        var descricaoResultado = codigoResultado.options[exame.codigoResultado].text;
        sequencialGrid += 1;
        var dado = {
            sequencial: exame.sequencial,
            data: exame.data,
            procedimento: exame.descricaoProcedimento,
            resultado: descricaoResultado,
            ordem: descricaoOrdem,
            codigoProcedimento: exame.codigoProcedimento,
            codigoOrdem: exame.codigoOrdem,
            codigoResultado: exame.codigoResultado,
            observacao:exame.observacao
        }
        collectionExames.add(dado);
    };

</script>
