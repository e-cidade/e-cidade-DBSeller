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
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_aguaisencaorec_classe.php"));
include(modification("classes/db_aguaconstrcar_classe.php"));
include(modification("classes/db_agualeitura_classe.php"));
include(modification("classes/db_aguahidrotroca_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("agu3_conscadastro_002_classe.php"));

$tipo = null;
$data_ocorrencia_inicio_ano = '';
$data_ocorrencia_inicio_mes = '';
$data_ocorrencia_inicio_dia = '';
$data_ocorrencia_final_ano  = '';
$data_ocorrencia_final_mes  = '';
$data_ocorrencia_final_dia  = '';

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script type="text/javascript" src="scripts/prototype.js"></script>
        <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
        <style>
            .db_area {
                font-family : courier; 
            }
        </style>
    </head>
    <body>
    <?php
        db_postmemory($HTTP_GET_VARS, 0);

        $Consulta = new ConsultaAguaBase($parametro);
        $rotulo   = new rotulocampo();

        // CARACTERISTICAS
        if ($solicitacao == "CaracteristicasDoImovel") {
            db_lovrot($Consulta->GetAguaBaseCarSQL(), 15, "()", "", "");
        
        //
        // ISENCOES
        //
        } elseif ($solicitacao == "Isencoes") {
            db_lovrot($Consulta->GetAguaIsencaoRecSQL(), 15, "()", "", "");

        //
        // ENDERECO DE ENTREGA
        //
        } elseif ($solicitacao == "EnderecoDeEntrega") {
            db_lovrot($Consulta->GetAguaBaseCorrespSQL(), 15, "()", "", "");

        //
        // CONSTRUCOES
        //
        } elseif ($solicitacao == "Construcoes") {
            $claguaconstrcar = $Consulta->GetAguaConstrCarDAO();
            $claguaconstrcar->rotulo->label();

            $rotulo->label("x11_numero");
            $rotulo->label("x11_complemento");
            $rotulo->label("x11_area");
            $rotulo->label("x11_pavimento");
            $rotulo->label("x11_qtdfamilia");
            $rotulo->label("x11_qtdpessoas");
            $rotulo->label("j31_codigo");
            $rotulo->label("j31_descr");
            $rotulo->label("j32_descr");
            $rotulo->label("j32_tipo");

            $result = $Consulta->RecordSetAguaConstrCar();
            $linhas = pg_num_rows( $result );  

            // Percorre construcoes;
            if ($linhas != 0) {
                echo "<table width=\"70%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"2\">";
        
                for ($contador = 0; $contador < $linhas; $contador++) {
                    db_fieldsmemory($result, $contador);
                
                    // Mostra Dados da Construcao
                    if ($x11_codconstr <> @$ant) {
                        $ant = $x11_codconstr;
                
                        // Construcao: 0
                        echo "<tr align=center>";
                        echo "  <td colspan=7 bgcolor=#333333>";
                        echo "    <font color=#FFFFFF>$Lx12_codconstr $x12_codconstr</font>";
                        echo "  </td>";
                        echo "</tr>";

                        echo " <tr align=\"center\"> ";
                        echo "   <td colspan=\"4\">&nbsp; </td> ";
                        echo " </tr> ";

                        // NUMERO - COMPLEMENTO
                        echo " <tr align=\"center\"> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_numero</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_numero&nbsp;</td> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_complemento</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_complemento&nbsp; ";
                        echo "   </td> ";
                        echo " </tr> ";
                
                        // AREA - PAVIMENTO
                        echo " <tr align=\"center\"> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_area</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_area m2&nbsp;</td> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_pavimento</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_pavimento&nbsp; ";
                        echo "   </td> ";
                        echo " </tr> ";
                
                        // QTDFAMILIA - QTDPESSOAS
                        echo " <tr align=\"center\"> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_qtdfamilia</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_qtdfamilia&nbsp;</td> ";
                        echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lx11_qtdpessoas</td> ";
                        echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$x11_qtdpessoas&nbsp; ";
                        echo "   </td> ";
                        echo " </tr> ";

                        echo " <tr align=\"center\"> ";
                        echo "   <td colspan=\"4\">&nbsp; </td> ";
                        echo " </tr> ";
                    }

                    // j31_codigo - j31_descr
                    echo " <tr align=\"center\"> ";
                    echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lj31_codigo</td> ";
                    echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$j31_codigo&nbsp;</td> ";
                    echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lj31_descr</td> ";
                    echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$j31_descr&nbsp; ";
                    echo "   </td> ";
                    echo " </tr> ";

                    // j32_descr - j32_tipo
                    echo " <tr align=\"center\"> ";
                    echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lj32_descr</td> ";
                    echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$j32_descr&nbsp;</td> ";
                    echo "   <td width=\"30%\"align=\"right\" nowrap bgcolor=\"#CCCCCC\">$Lj32_tipo</td> ";
                    echo "   <td align=\"left\" nowrap bgcolor=\"#FFFFFF\">$j32_tipo&nbsp; ";
                    echo "   </td> ";
                    echo " </tr> ";
                }
        
                echo "</table>";
            }
        } elseif ($solicitacao == "Leitura") { ?>
            <center>
                <input name="imprimir" type="button" value="Imprimir Leituras" onclick="js_imprime_leituras();">
            </center>
        <?php
            db_lovrot($Consulta->GetAguaLeituraSQL(), 12, "()", "", "");
        } elseif ($solicitacao == "Condominio") {
            db_lovrot($Consulta->GetAguaCondominioMatricSQL(), 10, "()", "", "");
        } elseif ($solicitacao == "Hidrometro") {
            db_lovrot($Consulta->GetAguaHidroMatricSQL(), 10, "()", "", "");
        } elseif ($solicitacao == "Corte") {
            echo "<br>";
        ?>
            <center>
                <input name="imprimir" type="button" value="Imprimir" onclick="js_imprime();">
            </center>
        <?php
            db_lovrot($Consulta->GetAguaCorteMatMovSQL(), 15, "()", "", "");
        } elseif ($solicitacao == "Calculo") {
            $rotulo->label("x22_area");
            $rotulo->label("x22_numpre");
            $rotulo->label("x19_conspadrao");
            $rotulo->label("x19_descr");
            $rotulo->label("x21_consumo");
            $rotulo->label("x21_excesso");

            $rotulo->label("x23_valor");
            $rotulo->label("x25_descr");
            $rotulo->label("x25_receit");
            $rotulo->label("k02_descr");
        
            $rCalc   = $Consulta->RecordSetAguaCalc();
            $iLinhas = pg_numrows($rCalc);

            if ($iLinhas == 0) {
                echo "<table>";
                echo "  <tr align=center>"; 
                echo "    <td colspan=7></td>";
                echo "  </tr>";
                echo "  <tr align=center>"; 
                echo "    <td colspan=7><b>Nenhum registro encontrado</b></td>";
                echo "  </tr>";
                echo "</table>";

                exit;
            } 
        ?>
        <table style="width: 60%;">    
            <?php 
                for ($indx=0; $indx < $iLinhas; $indx ++) {
                    db_fieldsmemory($rCalc, $indx);
                    $nomemes = strtoupper(db_mes($x22_mes));
            ?>
            <tr align="center">
                <td bgcolor="#333333">
                    <font color=#FFFFFF>
                        <b><?php echo $nomemes .' / '. $x22_exerc ?></b>
                    </font>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 15%;">
                                <?php echo $Lx22_area; ?>
                            </td>
                            <td style="width: 40%;">
                                <?php echo db_formatar($x22_area,'f'); ?>
                            </td>
                            <td style="width: 10%;">
                                <?php echo $Lx22_numpre; ?>
                            </td>
                            <td style="width: 35%;">
                                <?php echo db_numpre($x22_numpre, $x22_mes, 1, 0); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $Lx19_conspadrao; ?>
                            </td>
                            <td>
                                <?php 
                                echo db_formatar($x19_conspadrao,'f') . " m3"; 
                                echo db_formatar($x21_consumo,'f') . " m3";
                                ?>
                            </td>
                            <td>
                                <?php echo $Lx19_descr; ?>
                            </td>
                            <td>
                                <?php echo $x19_descr; ?>
                            </td>
                        </tr> 
                        <tr>
                            <td>
                                <?php echo $Lx21_consumo; ?>
                            </td>
                            <td>
                                <?php 
                                echo db_formatar($x21_consumo + $x21_excesso,'f') . " m3";
                                ?>
                            </td>
                            <td>
                                <?php echo $Lx21_excesso; ?>
                            </td>
                            <td>
                                <?php echo db_formatar($x21_excesso, 'f') . " m3"; ?>
                            </td>
                        </tr>  
                        <tr>
                            <td colspan="4">
                                <table style="border: 1px solid black; width: 100%;">
                                <tr>
                                    <td>
                                        <?php echo $Lx25_receit; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo $Lk02_descr;
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $Lx25_descr; ?>
                                    </td>
                                    <td>
                                        <?php echo $Lx23_valor; ?>
                                    </td>
                                </tr> 
                                <?php 
                                    $rCalcVal = $Consulta->RecordSetAguaCalcVal($x22_codcalc);
                                    $nSoma    = 0;
                                
                                    for ($indy = 0; $indy < pg_numrows($rCalcVal); $indy++ ) {
                                        db_fieldsmemory($rCalcVal, $indy);
                                        $nSoma += $x23_valor;
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo $x25_receit?>
                                        </td>
                                        <td>
                                            <?php echo $k02_descr?>
                                        </td>
                                        <td>
                                            <?php echo $x25_descr?>
                                        </td>
                                        <td>
                                            <?php echo db_formatar($x23_valor,'f'); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="3">
                                        <b>T O T A L &nbsp&nbsp C A L C U L A D O :</b>
                                    </td>
                                    <td>
                                        <b><?php echo db_formatar($nSoma,'f'); ?></b>
                                    </td>
                                </tr>  
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp</td>
            </tr>
            <?php } ?>
        </table>
        <?php 
            } elseif ($solicitacao == "Ocorrencia") {
                echo "<br>";
        ?>
            <form action="" method="GET">
                <input type="hidden" name="solicitacao" value="Ocorrencia">
                <input type="hidden" name="parametro" value="<?= $parametro ?>">
                <table style="margin-bottom: 0.5em">
                    <tr>
                        <td>Tipos:</td>
                        <td>
                            <select value="<?= $tipo ?>" name="tipo">
                                <option value="">Todos</option>
                                <option <?= ($tipo == 1) ? 'selected' : '' ?> value="1">Automática</option>
                                <option <?= ($tipo == 2) ? 'selected' : '' ?> value="2">Manual</option>
                            </select>
                        </td>
                        <td>Data da ocorrência:</td>
                        <td style="position: relative;">
                            <?= 
                                db_inputdata(
                                    "data_ocorrencia_inicio",
                                    $data_ocorrencia_inicio_dia,
                                    $data_ocorrencia_inicio_mes,
                                    $data_ocorrencia_inicio_ano,
                                    false,
                                    'text',
                                    1
                                ) 
                            ?>
                        </td>
                        <td> até </td>
                        <td>
                            <?= 
                                db_inputdata(
                                    "data_ocorrencia_final",
                                    $data_ocorrencia_final_dia,
                                    $data_ocorrencia_final_mes,
                                    $data_ocorrencia_final_ano,
                                    false,
                                    'text',
                                    1
                                )
                            ?>    
                        </td>
                        <td>
                        <input type="submit" value="Pesquisar">
                        </td>
                    </tr>
                </table>
            </form>
        <?php
                $Consulta->tipo = $tipo;

                if (!empty($data_ocorrencia_inicio)) {
                    $Consulta->dataOcorrenciaInicial = implode('-', [
                        $data_ocorrencia_inicio_ano,
                        $data_ocorrencia_inicio_mes,
                        $data_ocorrencia_inicio_dia
                    ]);
                }

                if (!empty($data_ocorrencia_final)) {
                    $Consulta->dataOcorrenciaFinal = implode('-', [
                        $data_ocorrencia_final_ano,
                        $data_ocorrencia_final_mes,
                        $data_ocorrencia_final_dia
                    ]);
                }

                $tipoLabel = null;

                if ($tipo == 1) {
                    $tipoLabel = 'Autom&aacute;tica';
                } elseif ($tipo == 2) {
                    $tipo = 'Manual';
                } else {
                    $tipoLabel = 'Todos';
                }

                $dataInicioLabel = null;
                $dataFinalLabel  = null;

                if (!empty($data_ocorrencia_inicio)) {
                    $dataInicioLabel = date_format(date_create(implode('-', [
                        $data_ocorrencia_inicio_ano,
                        $data_ocorrencia_inicio_mes,
                        $data_ocorrencia_inicio_dia
                    ])), 'd/m/Y');
                }

                if (!empty($data_ocorrencia_final)) {
                    $dataFinalLabel = date_format(date_create(implode('-', [
                        $data_ocorrencia_final_ano,
                        $data_ocorrencia_final_mes,
                        $data_ocorrencia_final_dia
                    ])), 'd/m/Y');
                }

                db_lovrot(
                    $Consulta->getHistOcorrenciaMatric(),
                    8,
                    "()",
                    "",
                    "",
                    "",
                    "NoMe",
                    array(),
                    true,
                    array(),
                    'V',
                    null,
                    [
                        '<b>Matricula:</b> ' . $parametro,
                        '<b>Tipo:</b> ' . $tipoLabel,
                        '<b>Data da ocorr&ecirc;ncia</b>: de ' . 
                            $dataInicioLabel . 
                            ' at&eacute; ' . $dataFinalLabel
                    ]
                );
                // echo "<script>function mensagem(mensagem){ alert(mensagem); }</script>";
                /////////////////////////////////////////////////////////////////////////////////////////////////////
        ?>
        <?php
            } elseif ($solicitacao == "BaixaImoveis") {
                echo "<br>";   
                db_lovrot($Consulta->getHistAguaBaixaImoveis(), 15, "()", "", "");
            }
        ?>
    </body>
</html>
<script>
function mensagem(mensagem) {
    var iHeight = 350;
    var iWidth  = 950;

    var windowDetalhes = new windowAux('wndDetalhes',
        'Ocorrencia',
        iWidth,
        iHeight
    );

    const detalhes = document.createElement('div');
    detalhes.style = 'padding: 5px;';
    
    const paragrafo = document.createElement('p');
    
    paragrafo.style = 'padding-right: 10px; padding-left: 10px; padding-bottom: 10px; background-color: white';
    paragrafo.innerHTML = mensagem;
    detalhes.appendChild(paragrafo);
    windowDetalhes.setContent(detalhes);
    windowDetalhes.show(null, null, true);
    windowDetalhes.setShutDownFunction(function() {
        windowDetalhes.destroy();
    });
}

function js_imprime() {
    window.open(
        'agu4_relcorte.php?matric=<?php echo @$parametro; ?>',
        'Relatorio',
        'toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no'
    );
    
    return false;
}

function js_imprime_leituras() {
    window.open(
        'agu4_relleituras002.php?matric=<?php echo @$parametro; ?>',
        'Relatorio',
        'toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no'
    );
    
    return false;  
}
</script>
