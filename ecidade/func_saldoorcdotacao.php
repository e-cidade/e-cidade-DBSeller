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

use ECidade\Financeiro\Orcamento\Repository\ComplementoRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orcreserva_classe.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_orcdotacao_classe.php"));
require_once(modification("classes/db_orcelemento_classe.php"));
require_once(modification("classes/db_db_config_classe.php"));

$clorcreserva = new cl_orcreserva;
$clorcreserva->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o58_coddot");
$clrotulo->label("o83_autori");
$clrotulo->label("DBtxtmes");
$clrotulo->label("DBtxtmesacumulado");
$clrotulo->label("DBtxtperiodoini");
$clrotulo->label("DBtxtperiodofim");

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($_POST);
db_postmemory($_GET);

if (isset($o58_coddot)) {
    $coddot = $o58_coddot;
}

if (empty($anousu)) {
    $anousu = db_getsession("DB_anousu");
}

if (!isset($nivel)) {
    if (isset($pesquisames)) {
        if (isset($DBtxtdia) && $DBtxtdia != 0) {
            if (isset($DBtxtmes) && $DBtxtmes == 02) {
                $iDias = cal_days_in_month(CAL_GREGORIAN, $DBtxtmes, $anousu);
                if ($DBtxtdia > $iDias) {
                    $iDiaFinal = $iDias;
                    $DBtxtdia = "0";
                } else {
                    $iDiaFinal = $DBtxtdia;
                }
            } else {
                $iDiaFinal = $DBtxtdia;
            }
        } else {
            $iDiaFinal = cal_days_in_month(CAL_GREGORIAN, $DBtxtmes, $anousu);
            $DBtxtdia = "0";
        }
        $DBtxtperiodoini = $anousu . "-" . $DBtxtmes . "-01";
        if ($DBtxtmes + 1 > 12) {
            $DBtxtperiodofim = "(" . ($anousu + 1) . "-01-01)-1";
        } else {
            $DBtxtperiodofim = "" . $anousu . "-{$DBtxtmes}-{$iDiaFinal}";
        }
        $dPeriodoIni = $DBtxtperiodoini;
        $dPeriodoFim = $DBtxtperiodofim;

        $result = db_dotacaosaldo(
            8,
            2,
            2,
            true,
            " o58_coddot = {$coddot} and o58_anousu = {$anousu}",
            $anousu,
            $DBtxtperiodoini,
            $DBtxtperiodofim
        );
        unset($DBtxtperiodoini_dia);
        unset($DBtxtperiodoini_mes);
        unset($DBtxtperiodoini_ano);
        unset($DBtxtperiodofim_dia);
        unset($DBtxtperiodofim_mes);
        unset($DBtxtperiodofim_ano);
    } else {
        if ($anousu != db_getsession("DB_anousu")) {
            if (!isset($DBtxtperiodoini)) {
                $DBtxtperiodoini = date("Y-m-d", db_getsession("DB_datausu"));
            }
            if (!isset($DBtxtperiodofim)) {
                $DBtxtperiodofim = date("Y-m-d", db_getsession("DB_datausu"));
            }
            $dPeriodoIni = $DBtxtperiodoini;
            $dPeriodoFim = $DBtxtperiodofim;
            $result = db_dotacaosaldo(
                8,
                2,
                2,
                true,
                " o58_coddot = {$coddot} and o58_anousu = {$anousu}",
                $anousu,
                $anousu . '-01-01',
                $anousu . "-12-31"
            );
        } else {
            $DBtxtmes = date("m", db_getsession("DB_datausu"));

            if (isset($DBtxtdia) && $DBtxtdia != 0) {
                if (isset($DBtxtmes) && $DBtxtmes == 02) {
                    $iDias = cal_days_in_month(CAL_GREGORIAN, $DBtxtmes, $anousu);

                    if ($DBtxtdia > $iDias) {
                        $iDiaFinal = $iDias;
                        $DBtxtdia = "0";
                    } else {
                        $iDiaFinal = $DBtxtdia;
                    }
                } else {
                    $iDiaFinal = $DBtxtdia;
                }
            } else {
                $iDiaFinal = cal_days_in_month(CAL_GREGORIAN, $DBtxtmes, $anousu);
                $DBtxtdia = "0";
            }

            $DBtxtperiodoini = $anousu . "-" . $DBtxtmes . "-01";
            if ($DBtxtmes + 1 > 12) {
                $DBtxtperiodofim = "(" . ($anousu + 1) . "-01-01)-1";
            } else {
                $DBtxtperiodofim = "" . $anousu . "-{$DBtxtmes}-{$iDiaFinal}";
            }
            $dPeriodoIni = $DBtxtperiodoini;
            $dPeriodoFim = $DBtxtperiodofim;
            $result = db_dotacaosaldo(
                8,
                2, 2,
                true,
                " o58_coddot = $coddot and o58_anousu = $anousu",
                $anousu,
                $DBtxtperiodoini,
                $DBtxtperiodofim
            );
        }
    }
} else {
    $dPeriodoIni = $DBtxtperiodoini;
    $dPeriodoFim = $DBtxtperiodofim;
    $result = db_dotacaosaldo(
        $nivel,
        2,
        2,
        true,
        " o58_coddot = $coddot and o58_anousu = $anousu",
        $anousu,
        date("Y-m-d", db_getsession("DB_datausu")),
        date("Y-m-d", db_getsession("DB_datausu"))
    );
}
if (pg_num_rows($result) > 0) {
    db_fieldsmemory($result, 0, true);
} else {
    $db_msg = "Dotação não cadastrada.";
    if (isset($diminui)) {
        $coddot = $coddot + 1;
    }
    if (isset($aumenta)) {
        $coddot = $coddot - 1;
    }
    db_redireciona("func_saldoorcdotacao.php?coddot=$coddot");
    exit;
}

$iDia = (!empty($DBtxtdia)) ? $DBtxtdia : $iDiaFinal;
$DBtxtmes = empty($DBtxtmes) ? "01" : $DBtxtmes;
$iDia = empty($iDia) ? "01" : $iDia;
$sDataFinal = "$anousu-$DBtxtmes-$iDia";
$oDataFinal = new DBDate($sDataFinal);

$oDotacao = new Dotacao($coddot, $anousu);
$oSaldoReservado = $oDotacao->getValorReservadoAteData($oDataFinal);

$fonte = \App\Domain\Financeiro\Orcamento\Models\FonteRecurso::query()
    ->where('exercicio', $oDotacao->getAno())
    ->where('orctiporec_id',$oDotacao->getRecurso())
    ->first();

$fonteRecurso = "{$fonte->codigo_siconfi} - {$fonte->recurso->o15_recurso}";
$descricao = $fonte->descricao;

$complemento = $fonte->recurso->o15_complemento;
$complementoDescricao = $fonte->recurso->complemento->o200_descricao;

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        .coluna-valor {
            outline: 1px solid #ccc;
            background: #fff;
        }

        .coluna-label {
            font-weight: bold;
            text-align: left;
        }

        .coluna-label,
        .coluna-valor {
            padding: 4px;
        }

        #valores-dotacao .coluna-valor {
            width: 160px;
        }

        #valores-dotacao .topo {
            text-align: center;
        }

    </style>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript"
            src="scripts/classes/financeiro/PesquisaLinhaPacto.js"></script>
    <script>
        function js_verifica() {
            valor = new Number(document.form1.o58_coddot.value);
            if (!isNaN(valor)) {
                document.form1.submit();
            }
        }

        function js_imprimir() {

            var iCodDot = document.form1.o58_coddot.value;
            var iAnoUsu = <?php echo(db_getsession("DB_anousu"))?>;
            var iNivel = <?php echo @$nivel ? @$nivel : 8?>;
            var iDiaUsu = document.form1.DBtxtdia.value;

            qry = "coddot=" + iCodDot;
            qry += "&anousu=" + iAnoUsu;
            qry += "&mesusu=<?php echo $DBtxtmes?>";
            qry += "&nivel=" + iNivel;
            qry += "&dPeriodoIni=<?php echo $dPeriodoIni?>";
            qry += "&dPeriodoFim=<?php echo $dPeriodoFim?>";
            if (iDiaUsu != 0) {
                qry += "&diausu=" + iAnoUsu + "-<?php echo $DBtxtmes?>-" + iDiaUsu;
            }
            jan = window.open(
                'orc2_saldodotacao002.php?' + qry,
                '',
                'width=' + (screen.availWidth - 5) + ',height=' +
                (screen.availHeight - 40) + ',scrollbars=1,location=0 '
            );
            jan.moveTo(0, 0);
        }
    </script>
</head>
<body>
<form name="form1" method="post">
    <div class="container" style="display: flex;justify-content: center">
        <fieldset style="height: 345px;">
            <legend>Dados da Dotação
                - <?php echo $anousu . ' ' . ($dot_ini > 0 ? '' : ' - Crédito Especial') ?></legend>
            <table class="tabela-consulta-dotacao">

                <tr>
                    <td class="coluna-label">Órgão:</td>
                    <td class="coluna-valor"><?php echo $o58_orgao ?></td>
                    <td class="coluna-valor"><?php echo $o40_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Unidade:</td>
                    <td class="coluna-valor"><?php echo $o58_unidade ?></td>
                    <td class="coluna-valor"><?php echo $o41_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Função:</td>
                    <td class="coluna-valor"><?php echo $o58_funcao ?></td>
                    <td class="coluna-valor"><?php echo $o52_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Subfunção:</td>
                    <td class="coluna-valor"><?php echo $o58_subfuncao ?></td>
                    <td class="coluna-valor"><?php echo $o53_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">
                        Programa
                    </td>
                    <td class="coluna-valor"><?php echo $o58_programa ?></td>
                    <td class="coluna-valor"><?php echo $o54_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Proj./Atividade:</td>
                    <td class="coluna-valor"><?php echo $o58_projativ ?></td>
                    <td class="coluna-valor"><?php echo $o55_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Elemento:</td>
                    <td class="coluna-valor"><?php echo db_formatar($o58_elemento, 'elemento_int') ?></td>
                    <td class="coluna-valor"><?php echo $o56_descr ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Recurso:</td>
                    <td class="coluna-valor"><?php echo $fonteRecurso ?></td>
                    <td class="coluna-valor"><?php echo $descricao ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Complemento:</td>
                    <td class="coluna-valor"><?php echo $complemento ?></td>
                    <td class="coluna-valor"><?php echo $complementoDescricao ?></td>
                </tr>
                <tr>
                    <td class="coluna-label">Reduzido:</td>
                    <td>
                        <?php
                        db_input(
                            "o58_coddot",
                            5,
                            $Io58_coddot,
                            true,
                            'text',
                            2,
                            "style='width: 100%' onchange='js_verifica();'"
                        );
                        ?>
                    </td>
                    <td>
                        <input name="anterior" value="Anterior" type="button"
                               onclick="location.href='func_saldoorcdotacao.php?diminui=0&coddot=<?php echo($coddot - 1 < 1 ? $coddot : $coddot - 1) ?>'">
                        <input name="Proximo" value="Próximo" type="button"
                               onclick="location.href='func_saldoorcdotacao.php?aumenta=0&coddot=<?php echo $coddot + 1 ?>'">
                    </td>
                </tr>

                <tr>
                    <td class="coluna-label">Ms:</td>
                    <td>
                        <?php
                        $x = array("01" => "Janeiro",
                            "02" => "Fevereiro",
                            "03" => "Março",
                            "04" => "Abril",
                            "05" => "Maio",
                            "06" => "Junho",
                            "07" => "Julho",
                            "08" => "Agosto",
                            "09" => "Setembro",
                            "10" => "Outubro",
                            "11" => "Novembro",
                            "12" => "Dezembro");
                        if ($DBtxtmes == 0) {
                            $DBtxtmes = db_hora(db_getsession("DB_datausu"), "m");
                        }
                        db_select(
                            "DBtxtmes",
                            $x,
                            true,
                            2,
                            "style='width: 100%' onchange='document.form1.pesquisames.click()'"
                        );
                        ?>
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="coluna-label">Dia:</td>
                    <td>
                        <?php
                        $iDias = cal_days_in_month(CAL_GREGORIAN, $DBtxtmes, db_getsession("DB_anousu"));
                        $yDias = array();
                        $sIncrem = "";

                        for ($iInd = 1; $iInd < $iDias + 1; $iInd++) {
                            $sDia = str_pad($iInd, 2, "0", STR_PAD_LEFT);
                            $yDias[$sDia] = $sDia . "/" . $DBtxtmes . "/" . $anousu;
                        }

                        $yDias[0] = "Selecione";
                        $yDias = array_reverse($yDias, true);

                        if (!isset($DBtxtdia)) {
                            $DBtxtdia = "0";
                        }

                        db_select(
                            "DBtxtdia",
                            $yDias,
                            true,
                            2,
                            "style='width: 100%' onchange='document.form1.pesquisames.click()'"
                        );
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>

            </table>
        </fieldset>

        <fieldset style="height: 345px;min-width: 400px;">
            <legend>Saldos da Dotação</legend>
            <table style="width: 100%">
                <?php
                if (isset($DBtxtdia) && $DBtxtdia != 0) {
                    $lData = $x[$DBtxtmes];
                    $lAcumulado = "at " . $DBtxtdia . "/" . $DBtxtmes . "/" . $anousu;
                } else {
                    $lData = $x[$DBtxtmes];
                    $lAcumulado = "";
                }
                ?>

                <tr class="descricao">
                    <td class="coluna-label" align="center"> Financeiro</td>
                    <td class="coluna-label topo" align="center"> <?php echo $lData ?></td>
                    <td class="coluna-label topo" align="center"> Acumulado <?php echo $lAcumulado ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Saldo Inicial:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($dot_ini, 'f') ?></td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($dot_ini, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Saldo Anterior:</td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($saldo_anterior, 'f') ?></td>
                    <td class="coluna-valor" align="right"> <?php echo 0 ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Suplementação:</td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($suplementado, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($suplementado_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Redução:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($reduzido, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($reduzido_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Empenhado:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($empenhado, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($empenhado_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Anulado:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($anulado, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($anulado_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Liquidado:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($liquidado, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($liquidado_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Pago:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($pago, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($pago_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> A Pagar Liquidado:</td>
                    <td class="coluna-valor"
                        align="right"> <?= db_formatar($atual_a_pagar_liquidado, 'f') ?></td>
                    <td class="coluna-valor"
                        align="right"> <?= db_formatar($liquidado_acumulado - $pago_acumulado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> A Pagar Emp.:</td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($atual_a_pagar, 'f') ?></td>
                    <td class="coluna-valor" align="right">
                        <?= db_formatar(
                            ($empenhado_acumulado - $anulado_acumulado - $liquidado_acumulado),
                            'f'
                        ); ?>
                    </td>
                </tr>

                <tr>
                    <td class="coluna-label"> Saldo Dotação:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($atual, 'f') ?></td>
                    <td class="coluna-valor" align="right">
                        <?= db_formatar(
                            ($dot_ini + $suplementado_acumulado - $reduzido_acumulado)
                            - $empenhado_acumulado + $anulado_acumulado,
                            'f'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <td class="coluna-label"> Reservado:</td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($reservado, 'f') ?></td>
                    <td class="coluna-valor" align="right"> <?php echo db_formatar($reservado, 'f') ?></td>
                </tr>

                <tr>
                    <td class="coluna-label"> Saldo Disponível:</td>
                    <td class="coluna-valor"
                        align="right"> <?php echo db_formatar($atual_menos_reservado, 'f') ?></td>
                    <td class="coluna-valor" align="right">
                        <?= db_formatar(
                            ($dot_ini + $suplementado_acumulado - $reduzido_acumulado)
                            - $empenhado_acumulado + $anulado_acumulado - $reservado,
                            'f'
                        ) ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="container">
        <div style="width: 975px;">
            <fieldset>
                <legend>Planos Orçamentários</legend>
                <div id="ctnGridLinhaPacto">
                </div>
            </fieldset>
        </div>
    </div>

    <div class="container">
        <fieldset style="width: 948px;">
            <legend>Reservas Encontradas</legend>
            <div>
                <table style="width:100%">
                    <?php
                    if (isset($reservado) and ($reservado > 0)) {
                        $ini = $anousu . "-" . $DBtxtmes . "-01";

                        if ($DBtxtmes == date("m", db_getsession("DB_datausu"))) {
                            $ini = date("Y-m-d", db_getsession("DB_datausu"));
                        }

                        $fim = $anousu . "-12-31";
                        $sCampos = "
                            o80_codres, o80_dtini, o80_dtfim, o80_valor, o80_descr,
                            o81_codsup, o120_rhempenhofolha, o84_acordoitemdotacao,
                            pc11_seq,
                            pc11_codigo as o82_solicitem, o84_codres, o83_autori,
                            pc11_numero, o46_codlei as projeto, ac16_numero as numerocontrato,
                            ac20_ordem, ac16_numero, ac16_anousu, ac16_sequencial, ac16_tipoinstrumento
                        ";
                        $sWhere = "
                            o80_coddot= {$coddot}
                            and o80_anousu = {$anousu}
                            and ('$fim' >= o80_dtfim  and o80_dtfim >='$ini')
                            and '$ini'>= o80_dtini
                        ";
                        $sSql = $clorcreserva->sql_query_reservas(null, $sCampos, "o80_dtini, o80_codres", $sWhere);
                        $res = $clorcreserva->sql_record($sSql);

                        if ($clorcreserva->numrows > 0) {
                            echo "
                                <tr>
                                    <td class=\"coluna-label\" style=\"text-align: center\"><b>Código</b></td>
                                    <td class=\"coluna-label\"><b>Tipo</b></td>
                                    <td class=\"coluna-label\" style=\"text-align: center\"><b>Data Inicial</b></td>
                                    <td class=\"coluna-label\" style=\"text-align: center\"><b>Data Final</b></td>
                                    <td class=\"coluna-label\" style=\"text-align: center\"><b>Valor da Reserva</b></td>
                                </tr>
                            ";

                            for ($x = 0; $x < $clorcreserva->numrows; $x++) {
                                db_fieldsmemory($res, $x, true);
                                ?>
                                <tr>
                                    <td class="coluna-valor" style="text-align: center">
                                        <?php echo $o80_codres ?>
                                    </td>
                                    <td class="coluna-valor" title="<?= $o80_descr?>">
    
                                        <?php if ($o81_codsup != "") {
                                            echo "Suplementação: {$o81_codsup} Projeto: {$projeto} ";
                                        } elseif ($o82_solicitem != "") {
                                            echo 'Solicitação de Compras:';
                                            db_ancora($pc11_numero, 'js_busca_solicita(this)', 1);
                                            echo "Item: {$pc11_seq}";
                                        } elseif ($o84_codres != "") {
                                            echo "Reserva Automática  ";
                                        } elseif ($o83_autori != "") {
                                            echo "Autorização de Empenho:";
                                            db_ancora($o83_autori, 'js_busca_empenho(this)', 1);
                                        } elseif ($o120_rhempenhofolha != "") {
                                            echo "Empenho para Folha:  {$o120_rhempenhofolha}";
                                        } elseif ($o84_acordoitemdotacao != "") {
                                            echo Acordo::getTiposInstrumento()[$ac16_tipoinstrumento];
                                            db_ancora("{$ac16_numero}/{$ac16_anousu}:", "js_busca_contrato({$ac16_sequencial})", 1);
                                            echo "Item {$ac20_ordem}";
                                        } else {
                                            echo " Manual ";
                                        }
                                        ?></td>
                                    <td class="coluna-valor" style="text-align: center">
                                        <?php echo $o80_dtini ?>
                                    </td>
                                    <td class="coluna-valor" style="text-align: center">
                                        <?php echo $o80_dtfim ?>
                                    </td>
                                    <td class="coluna-valor" style="text-align: center">
                                        <?php echo db_formatar($o80_valor, 'f'); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td>Nenhuma reserva at a data.</td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<b>Nenhuma reserva encontrada.</b>";
                    }
                    ?>
                </table>
            </div>
        </fieldset>
    </div>

    <input name="relatorio" value="Imprimir consulta" type="button" onclick="js_imprimir();"
           style="margin: 0 auto; display: block; margin-top: 10px;">
    <input name="pesquisames" value="Pesquisa" type="submit" style="display:none">
</form>
</body>
</html>

<?php
$data_inicial = db_getsession("DB_anousu") . "-01-01";
$data_final = db_getsession("DB_anousu") . "-12-31";
?>
<script type="text/javascript">

    var data_inicial = '<?=$data_inicial;?>';
    var data_final = '<?=$data_final;?>';
    var parametro = {
        'exec': 'pesquisar',
        'data_inicial': data_inicial,
        'data_final': data_final,
        'dotacao': document.querySelector('#o58_coddot').value,
    };

    var gridPesquisaPlano = new DBGrid('gridPesquisaPlano');
    gridPesquisaPlano.nameInstance = 'gridPesquisaPlano';
    gridPesquisaPlano.setHeader([
        'Plano Orçamentário',
        'Linha de Pacto',
        'Previsto',
        'Remanejado',
        'Realizado',
        'Saldo'
    ]);
    gridPesquisaPlano.setCellAlign(['left', 'left', 'right', 'right', 'right', 'right']);
    gridPesquisaPlano.setCellWidth(['20%', '20%', '8%', '10%', '10%', '10%', '10%']);
    gridPesquisaPlano.setHeight(100);
    gridPesquisaPlano.show($('ctnGridLinhaPacto'));


    AjaxRequest.create(
        'orc3_planoorcamentarioRPC.php',
        parametro,
        function (response, erro) {

            gridPesquisaPlano.clearAll(true);
            if (erro) {

                alert(response.mensagem);
                return false;
            }

            response.planos_orcamentarios.each(
                function (dados) {
                    let codigoLinhaPacto = dados.linha_pacto;
                    let codigoAcao = dados.acao;

                    gridPesquisaPlano.addRow(
                        [
                            dados.descricao_plano,
                            "<a href='#' onclick='abrirConsulta(" + codigoLinhaPacto
                            + ", " + codigoAcao + ", \"" + dados.descricao_linha
                            + "\")'>" + dados.descricao_linha + "</a>",
                            js_formatar(dados.valor_previsto, 'f'),
                            js_formatar(dados.valor_alterado_remanejado, 'f'),
                            js_formatar(dados.valor_realizado, 'f'),
                            js_formatar(dados.saldo_final, 'f')
                        ]
                    );

                }
            );
            gridPesquisaPlano.renderRows();
        }
    ).setMessage('Aguarde, pesquisando informações...').execute();


    function abrirConsulta(codigoLinha, codigoAcao, descricaoLinha) {
        new PesquisaLinhaPacto(codigoLinha, codigoAcao, descricaoLinha);
    }

    function js_busca_solicita(ancoraSolicita) {
        let numeroSolicitacao = ancoraSolicita.innerText;

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_solicita',
            `com3_conssolicitacao002.php?pc10_numero=${numeroSolicitacao}`,
            'Pesquisa',
            true
        );
    }

    function js_busca_empenho(ancoraEmpenho) {
        let numeroEmpenho = ancoraEmpenho.innerText;

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_empempenhoaut001',
            'func_empempenhoaut001.php?e54_autori=' + numeroEmpenho,
            'Pesquisa',
            true
        );
    }

    function js_busca_contrato(numeroContrato) {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_consultaacordo',
            'con4_consacordos003.php?ac16_sequencial=' + numeroContrato,
            'Pesquisa',
            true
        );
    }
</script>
