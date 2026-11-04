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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <?php
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
  ?>


</head>

<script>
function js_ponto_proximo(sDataInicio,sDataFim,aMatriculas,lMostraObservacoes,iEmiteTodosAfastamentos){
  location.hef = 'rec2_espelhopontotela002.php?sDataInicio='+sDataInicio+'&sDataFim='+sDataFim+'&aMatriculas='+aMatriculas+'&lMostraObservacoes='+lMostraObservacoes+'&iEmiteTodosAfastamentos='+iEmiteTodosAfastamentos;

}

</script>

<script>
  function js_mostradiv(liga,evt,vlr){

    evt= (evt)?evt:(window.event)?window.event:""; 

    document.getElementById('divlabel').style.left = evt.clientX + "px";
    document.getElementById('divlabel').style.top = evt.clientY + "px";

    if(liga){
      document.getElementById('vlr').innerHTML= vlr;
      document.getElementById('divlabel').style.visibility='visible';
    }else{
      document.getElementById('divlabel').style.visibility='hidden';
    }  
  }
  </script>
<?php

use ECidade\RecursosHumanos\RH\Efetividade\Repository\Periodo as PeriodoRepository;
use ECidade\RecursosHumanos\RH\PontoEletronico\Manutencao\EspelhoPonto;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model\ProcessamentoPontoEletronico;

$oParametros = \db_utils::postMemory(array_merge($_GET, $_POST));

$aMatriculas = explode(',', $oParametros->aMatriculas);

$iCodigoSelecao = !empty($oParametros->iCodigoSelecao) ? $oParametros->iCodigoSelecao : null;
$lMostraObservacoes = $oParametros->lMostraObservacoes ? $oParametros->lMostraObservacoes : false;
$lEmiteTodosAfastamentos = !empty($oParametros->iEmiteTodosAfastamentos) && $oParametros->iEmiteTodosAfastamentos == 1;
$limiteMatriculasInconsistentes = 50;

if (empty($oParametros->sDataInicio)) {
    throw new ParameterException("Informe a data início.");
}

if (empty($oParametros->sDataFim)) {
    throw new ParameterException("Informe a data fim.");
}

if (empty($aMatriculas)) {
    if (empty($iCodigoSelecao)) {
        throw new ParameterException("Informe uma seleção ou uma ou mais matrículas para emissão do espelho ponto.");
    }
}

if (!empty($iCodigoSelecao)) {
    $aMatriculas = array_keys(\ServidorRepository::getServidoresBySelecao(
        DBPessoal::getAnoFolha(),
        DBPessoal::getMesFolha(),
        $iCodigoSelecao
    ));
}

try {
    $oPeriodoRepository = new PeriodoRepository(null, null, true);
    $aPeriodos = $oPeriodoRepository->getPeriodosEntreDatas(new DBDate($oParametros->sDataInicio),
      new DBDate($oParametros->sDataFim));

    foreach ($aPeriodos as $oPeriodo) {
        $aDatasEfetividade = \DBDate::getDatasNoIntervalo($oPeriodo->getDataInicio(), $oPeriodo->getDataFim());
        $aDatasProcessar = array();
        $aDatasProcessarJustificativas = array();

        foreach (\DBDate::getDatasNoIntervalo($oPeriodo->getDataInicio(), $oPeriodo->getDataFim()) as $oDataProcessar) {
            $aDatasProcessar[] = $oDataProcessar->getDate();
            $aDatasProcessarJustificativas[] = (object)array('data' => $oDataProcessar->getDate());
        }
    }

    $aServidores = array();

    if (empty($aMatriculas)) {
        throw new BusinessException("Não há servidores para esta selecão.");
    }

    $matriculasInconsistentes = array();
    foreach ($aMatriculas as $iMatricula) {
        $sTipo = '';

        /**
         * Cria marcações caso não exista e vincula justificativas e afastamentos
         */
        try {
            ProcessamentoPontoEletronico::criarMarcacoesNasDatas($iMatricula, $aDatasProcessarJustificativas);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case '3': // Lotação não configurada
                    $sTipo = 'lotacao';
                    $matriculasInconsistentes['mensagens'][] = $e->getMessage();
                    break;
                case '4': // Não possuí escala configurada
                    $sTipo = 'escala';
                    break;
                case '5': // Não possuí escala na data
                    $sTipo = 'configuracaoescala';
                    break;
            }

            $matriculasInconsistentes[$sTipo][] = $iMatricula;
        }

        if (isset($matriculasInconsistentes[$sTipo]) && count($matriculasInconsistentes[$sTipo]) > $limiteMatriculasInconsistentes) {
            break;
        }
    }

    if (!empty($matriculasInconsistentes['escala']) || !empty($matriculasInconsistentes['configuracaoescala']) || !empty($matriculasInconsistentes['lotacao'])) {
        $mensagemMatriculasInconsistentes = '';

        if (!empty($matriculasInconsistentes['escala'])) {
            $mensagemMatriculasInconsistentes = "Não há escala configurada para a(s) seguinte(s) matrícula(s): ";

            if (count($matriculasInconsistentes['escala']) > 1) {
                if (count($matriculasInconsistentes['escala']) > $limiteMatriculasInconsistentes) {
                    $mensagemMatriculasInconsistentes = "Verifique se há escala configurada para as matrículas/seleção informada(s)";
                } else {
                    $mensagemMatriculasInconsistentes .= implode(', ', $matriculasInconsistentes['escala']);
                }
            } else {
                $mensagemMatriculasInconsistentes .= $matriculasInconsistentes['escala'][0];
            }

            $mensagemMatriculasInconsistentes .= ".\nPara configurar acesse: RH > Procedimentos > Efetividade > Manutenção da Escala de Funcionários.\n\n";
        }

        if (!empty($matriculasInconsistentes['configuracaoescala'])) {
            if (count($matriculasInconsistentes['configuracaoescala']) > $limiteMatriculasInconsistentes) {
                $mensagemMatriculasInconsistentes .= "Verifique se há escala configurada para as matrículas/seleção informada(s) neste período";
            } else {
                $mensagemMatriculasInconsistentes .= "Não há escala configurada no período para a(s) seguinte(s) matrícula(s): ";
            }

            if (count($matriculasInconsistentes['configuracaoescala']) > 1) {
                $mensagemMatriculasInconsistentes .= implode(', ', $matriculasInconsistentes['configuracaoescala']);
            } else {
                $mensagemMatriculasInconsistentes .= $matriculasInconsistentes['configuracaoescala'][0];
            }

            $mensagemMatriculasInconsistentes .= ".\nPara configurar acesse: RH > Procedimentos > Efetividade > Manutenção da Escala de Funcionários. \n\n";
        }

        if (!empty($matriculasInconsistentes['lotacao'])) {
            if (count($matriculasInconsistentes['lotacao']) > $limiteMatriculasInconsistentes) {
                $mensagemMatriculasInconsistentes .= "Verifique se a(s) lotação(ões) das matrículas/seleção informada contém configuração do ponto eletrônico";
            } else {
                $mensagemMatriculasInconsistentes .= "A(s) lotação(ões) da(s) seguinte(s) matrícula(s) não contém configuração do ponto eletrônico: ";
            }

            if (count($matriculasInconsistentes['lotacao']) > 1) {
                $mensagemMatriculasInconsistentes .= implode(', ', $matriculasInconsistentes['lotacao']);
            } else {
                $mensagemMatriculasInconsistentes .= $matriculasInconsistentes['lotacao'][0];
            }

            $mensagemMatriculasInconsistentes .= ".\nPara configurar acesse: RH > Procedimentos > Ponto Eletrônico > Configurações > aba Lotação. \n\n";
        }

        throw new Exception($mensagemMatriculasInconsistentes);
    }

    foreach ($aPeriodos as $oPeriodo) {

        /**
         * Processa cálculo de horas extras e faltas
         */
        ProcessamentoPontoEletronico::processarMatriculas($aMatriculas, $oPeriodo, $aDatasProcessar);
    }

    foreach ($aMatriculas as $iMatricula) {
        $oServidor = \ServidorRepository::getInstanciaByCodigo($iMatricula);
        $oEspelho = new EspelhoPonto($oServidor, $aPeriodos, InstituicaoRepository::getInstituicaoSessao());
        $oEspelho->calcularTotalizadores();
        $aServidores[] = $oEspelho->retornaDados();
    }

    $sDataInicio = implode('/', array_reverse(explode('-', $oParametros->sDataInicio)));
    $sDataFim = implode('/', array_reverse(explode('-', $oParametros->sDataFim)));

    $head3 = "{$sDataInicio} a {$sDataFim}";

    escreverPDF($aServidores, $lMostraObservacoes, $lEmiteTodosAfastamentos);
} catch (Exception $e) {
    db_redireciona('db_erros.php?db_erro=' . urlencode($e->getMessage()));
}

function escreverPDF($aServidores, $lMostraObservacoes, $lEmiteTodosAfastamentos)
{

    global $head3, $sDataInicio, $sDataFim, $iEmiteTodosAfastamentos, $lMostraObservacoes;

    echo "<table  border='1' width='100%'>";
    echo "<tr>";

    foreach ($aServidores as $servidor) {
        $lQuebraPaginaObservacoes = false;
        $iLimiteObservacoesHorarios = 6;
        $iLimiteCarateresObservacoes = 120;
        $dadosServidor = $servidor['dados'];

        echo "<tr><td ><strong>Servidor: </strong></td>";
        echo "<td colspan='6'><strong>".$dadosServidor->nome."</strong></td>";
        echo "<td align='left'><strong>Período:</strong></td>";
        echo "<td colspan='6'><strong>$head3</strong></td></tr>";

        echo "<tr><td ><strong>Matricula: </strong></td>";
        echo "<td colspan='6'><strong> ".$dadosServidor->matricula."</strong></td>";
        echo "<td ><strong> Admissão: </strong></td>";
        echo "<td colspan='6'><strong> ".$dadosServidor->admissao."</strong></td></tr>";

        $sHorasJornada = '';
        $aJornadasServidor = $servidor['aHorasJornada'];
        $iLimiteObservacoesHorarios -= count($aJornadasServidor);
        $contadorHorasJornada = 0;

        //$pdf->Cell(26, 5, 'Horários:', 'TL', 0, "R");
        echo "<tr><td> <strong>Horários: </strong></td>";

        $lMostrarLegendaAfastamentos = false;
        foreach ($aJornadasServidor as $iCodigo => $jornada) {
            if (!$jornada->diaTrabalhado) {
                continue;
            }

            if( $contadorHorasJornada > 0 ){

              echo "<td> </td>";

            }

            $sHorasJornada = $iCodigo . ' - ';

            foreach ($jornada->horas as $oHora) {
                $sHorasJornada .= ' ' . $oHora->oHora->format('H:i');
            }

            //$pdf->Cell(169, 5, $sHorasJornada, 'RT', 1, "L");
            echo "<td colspan='13'> <strong>$sHorasJornada </strong></td></tr>";
 
            $contadorHorasJornada++;
        }

    echo "<tr align='center'>";
    echo "<td align='left'><strong>Data</strong></td>";
    echo "<td><strong>Código</strong></td>";
    echo "<td><strong>Entrada 1</strong></td>";
    echo "<td><strong>Saída 1</strong></td>";
    echo "<td><strong>Entrada 2</strong></td>";
    echo "<td><strong>Saída 2</strong></td>";
    echo "<td><strong>Entrada 3</strong></td>";
    echo "<td><strong>Saída 3</strong></td>";
    echo "<td><strong>Trabalho</strong></td>";
    echo "<td><strong>Faltas</strong></td>";
    echo "<td><strong>Ext 50</strong></td>";
    echo "<td><strong>Ext 75</strong></td>";
    echo "<td><strong>Ext 100</strong></td>";
    echo "<td><strong>Adic Not.</strong></td>";
    echo "</tr>";


        $aDatasServidor = $servidor['datas'];

        foreach ($aDatasServidor as $indDatas => $oData) {
            if ((!!preg_match('/^\d{1,2}\/(\d{1,2})\/\d{1,4}$/', $oData->data, $aMes)) !== true) {
                throw new BusinessException("Não foi possível identificar o mês.");
            }
            $mesAtual = $aMes[1];

            $oData->jornada = $oData->oJornada->codigo;
            $oData->entrada1 = $oData->oJornada->tipo_descricao;
            $oData->saida1 = $oData->oJornada->tipo_descricao;
            $oData->entrada2 = $oData->oJornada->tipo_descricao;
            $oData->saida2 = $oData->oJornada->tipo_descricao;
            $oData->entrada3 = $oData->oJornada->tipo_descricao;
            $oData->saida3 = $oData->oJornada->tipo_descricao;

            if (!$oData->oJornada->dsr_folga || $oData->lTemMarcacoes) {
                $oEntrada1 = $oData->aMarcacoes[0]->oEntrada;
                $oSaida1 = $oData->aMarcacoes[0]->oSaida;
                $oEntrada2 = $oData->aMarcacoes[1]->oEntrada;
                $oSaida2 = $oData->aMarcacoes[1]->oSaida;
                $oEntrada3 = $oData->aMarcacoes[2]->oEntrada;
                $oSaida3 = $oData->aMarcacoes[2]->oSaida;

                $oData->entrada1 = montarMarcacao($oEntrada1, $lEmiteTodosAfastamentos, $oData->afastamento,
                  $lMostrarLegendaAfastamentos);
                $oData->saida1 = montarMarcacao($oSaida1, $lEmiteTodosAfastamentos, $oData->afastamento,
                  $lMostrarLegendaAfastamentos);
                $oData->entrada2 = montarMarcacao($oEntrada2, $lEmiteTodosAfastamentos, $oData->afastamento,
                  $lMostrarLegendaAfastamentos);
                $oData->saida2 = montarMarcacao($oSaida2, $lEmiteTodosAfastamentos, $oData->afastamento,
                  $lMostrarLegendaAfastamentos);
                $oData->entrada3 = montarMarcacao($oEntrada3, $lEmiteTodosAfastamentos, $oData->afastamento,
                  $lMostrarLegendaAfastamentos);
                $oData->saida3 = montarMarcacao($oSaida3, $lEmiteTodosAfastamentos, $oData->afastamento,$lMostrarLegendaAfastamentos);

                for ($iIndMarcacoes = 0; $iIndMarcacoes < count($oData->aMarcacoes); $iIndMarcacoes++) {
                    $oMarcacao = $oData->aMarcacoes[$iIndMarcacoes];

                    if ($oMarcacao->oEntrada->manual) {
                        switch ($iIndMarcacoes) {
                            case 0:
                                $oData->entrada1 .= ' *';
                                break;
                            case 1:
                                $oData->entrada2 .= ' *';
                                break;
                            case 2:
                                $oData->entrada3 .= ' *';
                                break;
                        }
                    }

                    if ($oMarcacao->oSaida->manual) {
                        switch ($iIndMarcacoes) {
                            case 0:
                                $oData->saida1 .= ' *';
                                break;
                            case 1:
                                $oData->saida2 .= ' *';
                                break;
                            case 2:
                                $oData->saida3 .= ' *';
                                break;
                        }
                    }
                }
            }

            if ($oData->lFeriado) {
                $oData->entrada1 = 'FERIADO';
                $oData->saida1 = 'FERIADO';
                $oData->entrada2 = 'FERIADO';
                $oData->saida2 = 'FERIADO';
                $oData->entrada3 = 'FERIADO';
                $oData->saida3 = 'FERIADO';
            }

            if ($indDatas > 0) {
                $sTotal = empty($servidor['observacoes']) ? 42 : 31;

                if (!($indDatas % $sTotal)) {
                    //$pdf->AddPage();
                }
            }

            escreverGrade($dadosServidor->matricula, $oData);
        }

        echo "<tr align='center'>";
        echo "<td align='left'><strong>* Alterado Manualmente</strong></td>";
        echo "<td colspan='7' align='right'><strong>Totais:</strong></td>";
        //$pdf->Cell(117, 5, 'Totais:', 0, 0, "R");
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasNormais']), 0, 0, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasNormais'])."</strong></td>";
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasFaltas']), 0, 0, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasFaltas'])."</strong></td>";
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasExt50']), 0, 0, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasExt50'])."</strong></td>";
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasExt75']), 0, 0, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasExt75'])."</strong></td>";
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasExt100']), 0, 0, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasExt100'])."</strong></td>";
        //$pdf->Cell(13, 5, somarHora($servidor['nTotalHorasAdicional']), 0, 1, "C");
        echo "<td ><strong>".somarHora($servidor['nTotalHorasAdicional'])."</strong></td>";
        echo "</tr>";
 
        //$pdf->setFontSize(18);
        //$pdf->Cell(3, 7, '*', 0, 0, "C");
  
        //$pdf->setFontSize(8);
        //$pdf->Cell(190, 5, 'Alterado manualmente', 0, 1, "L");

        if ($lMostrarLegendaAfastamentos) {
            //$pdf->setFontSize(10);
            //$pdf->Cell(3, 5, '+', 0, 0, "C");
            //$pdf->setFontSize(8);
            //$pdf->Cell(190, 5, 'Existe mais de uma ocorrência de Afastamento/Justificativas.', 0, 1, "L");

            echo "<tr >";
            echo "<td colspan='14' align='left'><strong>Existe mais de uma ocorrência de Afastamento/Justificativas.</strong></td>";
            echo "</tr>";
        }
        if ($lMostraObservacoes) {
            $aObservacoesServidor = $servidor['observacoes'];

            if (count($aObservacoesServidor) > 0) {
                //$pdf->Cell(26, 5, 'Justificativas:', 0, 0, "L");
                //$pdf->Cell(169, 5, '', 0, 1, "R");
                echo "<tr >";
                echo "<td colspan='14' align='left'><strong>Justificativas:</strong></td>";
                echo "</tr>";
            }

            for ($iObsServidor = 0; $iObsServidor < count($aObservacoesServidor); $iObsServidor++) {
                $sObservacao = $aObservacoesServidor[$iObsServidor];

                if ($iLimiteObservacoesHorarios <= 0 || $iObsServidor >= $iLimiteObservacoesHorarios) {
                    $lQuebraPaginaObservacoes = true;
                    break;
                }

                if (strlen($sObservacao) > $iLimiteCarateresObservacoes) {
                    $sObservacao = substr($sObservacao, 0, $iLimiteCarateresObservacoes);
                    $sObservacao .= '...';
                }

                //$pdf->Cell(195, 5, "  {$sObservacao}", 0, 1, "L");

                echo "<tr>";
                echo "<td colspan='14' align='left'>$sObservacao</td>";
                echo "</tr>";
            }

            $aObservacoesServidor = array_slice($aObservacoesServidor, $iObsServidor);
        }

        //escreverAssinaturas('', $dadosServidor->nome, $dadosServidor->supervisor);

        if ($lMostraObservacoes) {

            if (count($aObservacoesServidor) > 0) {
                //$pdf->Cell(26, 5, 'Justificativas:', 0, 0, "L");
                //$pdf->Cell(169, 5, '', 0, 1, "R");a

                echo "<tr >";
                echo "<td colspan='14' align='left'><strong>Justificativas:</strong></td>";
                echo "</tr>";

            }

            foreach ($aObservacoesServidor as $sObservacao) {
                if (strlen($sObservacao) > $iLimiteCarateresObservacoes) {
                    $sObservacao = substr($sObservacao, 0, $iLimiteCarateresObservacoes);
                    $sObservacao .= '...';
                }

                //$pdf->Cell(195, 5, "  {$sObservacao}", 0, 1, "L");
                echo "<tr>";
                echo "<td colspan='14' align='left'>$sObservacao</td>";
                echo "</tr>";

            }

            if (count($aObservacoesServidor) > 0) {
                escreverAssinaturas('', $dadosServidor->nome, $dadosServidor->supervisor);
            }
        }
    }

    //$pdf->Output();

    echo "</table>";
?>
  <div align="left" id="divlabel" style="position:relative; width:200px; z-index:12; top:-20; left:-100px; visibility: hidden; border: 2px outset #666666; background-color: #6699cc; font-style:italic;"><span color="#9966cc" id="vlr"></span></div>
<?php

    echo "</body>";
    echo "</html>";

}

function escreverGrade($pdf, $dados, $lHeader = false)
{


    $colunas = (array)$dados;
    $iMaximoDeLinhas = 5;
    $aColunasNaoContar = array(
      'afastamento',
      'oJornada',
      'data_dia',
      'aMarcacoes',
      'oPeriodoEfetividade',
      'data',
      'possuiEvento',
      'dadosEvento'
    );
    foreach ($colunas as $campo => $coluna) {
        if (in_array($campo, $aColunasNaoContar)) {
            continue;
        }

        $iAlturaLinha = 5 ; //$pdf->NbLines(13, trim($coluna)) * 5;
        if ($iAlturaLinha > $iMaximoDeLinhas) {
            $iMaximoDeLinhas = $iAlturaLinha;
        }
    }
    echo "<tr align='center'>";
    //$alturaAtual = //$pdf->getY();
    //$pdf->Multicell(26, 5, $dados->data_dia, 'TLR', "L");
    //$pdf->SetXY(36, $alturaAtual);


    $sql = "select distinct rh229_hora as hora , rh229_serial as serial 
            from recursoshumanos.pontoeletronicoarquivoimportacaoregistro 
            where rh229_matricula = $pdf
              and rh229_data = '".substr($dados->data_dia,6,4)."-".substr($dados->data_dia,3,2)."-".substr($dados->data_dia,0,2)."' 
            order by rh229_hora";

    $linha = "";

    $res = pg_exec($sql);
    if(pg_num_rows($res) > 0 ){

      $hora = "";
      for($i=0;$i<pg_num_rows($res);$i++){

         $linha .= pg_result($res,$i,'hora')." - Serial : ";
         $linha .= pg_result($res,$i,'serial')."<br>";

      }

    }

    if(  $linha != "" ){
       echo "<td  align='left' onmouseover=\"js_mostradiv(true,event,'$linha')\" onmouseout=\"js_mostradiv(false,event)\">".$dados->data_dia."</td>";
    }else{
       echo "<td align='left'>".$dados->data_dia."</td>";
    }

    //$pdf->Multicell(13, 5, $dados->jornada, 'TLR', "C");
    //$pdf->SetXY(49, $alturaAtual);
    echo "<td>".$dados->jornada."</td>";
    //$pdf->Multicell(13, 5, $dados->entrada1, 'TLR', "C");
    //$pdf->SetXY(62, $alturaAtual);
   
    if ( $dados->normais == "" and $dados->faltas != "" ){

       echo "<td >FALTA</td>";
       echo "<td ></td>";
       echo "<td ></td>";
       echo "<td ></td>";
       echo "<td ></td>";
       echo "<td ></td>";

    }else{

    echo "<td>".$dados->entrada1."</td>";
    //$pdf->Multicell(13, 5, $dados->saida1, 'TLR', "C");
    //$pdf->SetXY(75, $alturaAtual);
    echo "<td>".$dados->saida1."</td>";
    //$pdf->Multicell(13, 5, $dados->entrada2, 'TLR', "C");
    //$pdf->SetXY(88, $alturaAtual);
    echo "<td>".$dados->entrada2."</td>";
    //$pdf->Multicell(13, 5, $dados->saida2, 'TLR', "C");
    //$pdf->SetXY(101, $alturaAtual);
    echo "<td>".$dados->saida2."</td>";
    //$pdf->Multicell(13, 5, $dados->entrada3, 'TLR', "C");
    //$pdf->SetXY(114, $alturaAtual);
    echo "<td>".$dados->entrada3."</td>";
    //$pdf->Multicell(13, 5, $dados->saida3, 'TLR', "C");
    //$pdf->SetXY(127, $alturaAtual);
    echo "<td>".$dados->saida3."</td>";

    }

    //$pdf->Multicell(13, 5, $dados->normais, 'TLR', "C");
    //$pdf->SetXY(140, $alturaAtual);
    echo "<td>".$dados->normais."</td>";
    //$pdf->Multicell(13, 5, $dados->faltas, 'TLR', "C");
    //$pdf->SetXY(153, $alturaAtual);
    echo "<td>".$dados->faltas."</td>";
    //$pdf->Multicell(13, 5, $dados->ext50, 'TLR', "C");
    //$pdf->SetXY(166, $alturaAtual);
    echo "<td>".$dados->ext50."</td>";
    //$pdf->Multicell(13, 5, $dados->ext75, 'TLR', "C");
    //$pdf->SetXY(179, $alturaAtual);
    echo "<td>".$dados->ext75."</td>";
    //$pdf->Multicell(13, 5, $dados->ext100, 'TLR', "C");
    //$pdf->SetXY(192, $alturaAtual);
    echo "<td>".$dados->ext100."</td>";
    //$pdf->Multicell(13, 5, $dados->adicional, 'TLR', "C");
    echo "<td>".$dados->adicional."</td>";
    if ($iMaximoDeLinhas > 5) {
        //$pdf->SetY($alturaAtual + $iMaximoDeLinhas);
    }
    /**
     * fechamos as linhas das celulas*
     */
    $posicaoInicioLinha = 10;

    foreach (range(1, 15) as $coluna) {
        $tamanho = 13;
        if ($coluna == 1) {
            $tamanho = 26;
        }
        //$pdf->line($posicaoInicioLinha, $alturaAtual, $posicaoInicioLinha, //$pdf->GetY());
        $posicaoInicioLinha += $tamanho;
    }

    //$pdf->line(10, //$pdf->GetY(), 205, //$pdf->GetY());
    if ($lHeader) {
        //$pdf->EndBold();
        //$pdf->SetFontSize(8);
    }

    echo "</tr>";

}

function somarHora($horarios)
{

    $nTotalMinutos = 0;
    foreach ($horarios as $horario) {
        if (is_null($horario) || $horario == '') {
            continue;
        }

        list($iHora, $iMinute) = explode(':', $horario);
        $nTotalMinutos += $iHora * 60;
        $nTotalMinutos += $iMinute;
    }

    $iHoras = floor($nTotalMinutos / 60);
    $nTotalMinutos -= $iHoras * 60;

    return sprintf('%02d:%02d', $iHoras, $nTotalMinutos);
}

function escreverAssinaturas($pdf, $nomeServidor, $nomeSupervisor)
{

    //$pdf->Cell(65, 18, '', 'B', 0, "C");
    //$pdf->Cell(65, 18, '', 0, 0, "C");
    //$pdf->Cell(65, 18, '', 'B', 1, "C");
    //$pdf->Cell(65, 7, $nomeServidor, 0, 0, "C");
    //$pdf->Cell(65, 7, '', 0, 0, "C");
    //$pdf->Cell(65, 7, $nomeSupervisor, 0, 1, "C");
}

/**
 * Monta a string da Marcacao
 * @param $marcacao
 * @param $mostrarAfastamento
 * @return string
 */
function montarMarcacao($marcacao, $mostrarAfastamento, $afastamento, &$mostrarLegenda)
{

    $aDados = array();
    $string = '';
    $iTotalAfastamento = 0;
    if ($afastamento->isAfastado) {
        $aDados[] = $afastamento->abreviacao;
        $iTotalAfastamento++;
    }
    if (!is_null($marcacao->oJustificativa)) {
        $aDados[] = $marcacao->oJustificativa->abreviacao;
        $iTotalAfastamento++;
    }
    $aDados[] = $marcacao->hora;
    $string = $aDados[0];
    if ($mostrarAfastamento) {
        $string = implode("\n", $aDados);
    }
    if ($iTotalAfastamento > 1 && !$mostrarAfastamento) {
        $string .= "+";
        $mostrarLegenda = true;
    }

    return $string;
}

?>



