<?php

require_once(modification("libs/db_autoload.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

use App\Domain\Tributario\Arrecadacao\Services\DescontoTermoParcelamentoService;
use ECidade\Tributario\Arrecadacao\Custas\Service;
use ECidade\Tributario\Divida\Termo\Repository\Termo as TermoRepository;
use ECidade\Tributario\Arrecadacao\Repository\TermoTaxaParcela as TermoTaxaParcelaRepository;
use ECidade\Tributario\Arrecadacao\Repository\HonorarioParcelamentoRepository;

use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilha as ProcessoForoPartilhaRepository;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo as ProcessoForoRepository;

use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha;
use ECidade\Tributario\Juridico\InicialPartilha\Repository\InicialPartilha as InicialPartilhaRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;

$regraParcelamento               = 0;
$valorTotalSemDescontos          = 0;
$percentualTotalDescontos        = 0;
$valorJuros                      = 0;
$descontoJuros                   = 0;
$valorMulta                      = 0;
$descontoMulta                   = 0;
$numeroParcel                    = 0;
$valorParcelaSemDescontos        = 0;
$valorParcelaComDescontos        = 0;
$valorParcelaInicialSemDescontos = 0;
$valorParcelaInicialComDescontos = 0;
$valorParcelaFinalSemDescontos   = 0;
$valorParcelaFinalComDescontos   = 0;
$numeroParcelas                  = 0;

if (isset($_POST)) {
    if (isset($_POST['k40_cadtipoparc'])){
        $regraParcelamento = $_POST['k40_cadtipoparc'];
    }

    if (isset($_POST['parc'])) {
        $numeroParcelas = number_format($_POST['parc'], 2);
    }

    if (isset($_POST['tiposparc'])) {
        $descontoTermoParcelamento = new DescontoTermoParcelamentoService();
        $descontosEncontrados = $descontoTermoParcelamento->buscaPercentuaisDescontosDepoisLancamento($parc[0]);

        if($descontosEncontrados){
            $descontoMulta = $descontosEncontrados->percentualdescontojuros;
            $descontoJuros = $descontosEncontrados->percentualdescontomultas;
        } else {   
            $splitTiposParcelas = (array)explode('-', $_POST['tiposparc']);
            
            for ($index = count($splitTiposParcelas) - 1; $index >= 0; $index--) {
                $splitTipoParcela = $splitTiposParcelas[$index];
                $tipoParcela = explode('=', $splitTiposParcelas[$index]);
                $numeroMaximoParcelas = $tipoParcela[1];
                
                if ($numeroParcelas <= $numeroMaximoParcelas) {   
                    $descontoMulta = $tipoParcela[2];
                    $descontoJuros = $tipoParcela[3];
                }
            }
        }
    }

    if (isset($_POST['valortotal'])) {
        $valorTotalSemDescontos = $_POST['valortotal'];
    }

    if (isset($_POST['juros'])) {
        $valorJuros = $_POST['juros'];
    }

    if (isset($_POST['multa'])) {
        $valorMulta = $_POST['multa'];
    }

    $descontoTotal = (calculaPercentual($descontoJuros, $valorJuros) + calculaPercentual($descontoMulta, $valorMulta));

    if (isset($k40_aplicacao) && (int)$k40_aplicacao == 1) {
        $descontoTotal = 0;
    }

    $percentualTotalDescontos = (int)$descontoTotal ? 100 - ($descontoTotal * 100 / $valorTotalSemDescontos) : 0;

    if (isset($_POST['ent'])) {
        $valorParcelaInicialSemDescontos = removeVirgulas(number_format($_POST['ent'], 2));
        $valorParcelaInicialComDescontos = (int)$descontoTotal ? calculaPercentual($percentualTotalDescontos, $valorParcelaInicialSemDescontos) : $valorParcelaInicialSemDescontos;
    }

    if (isset($_POST['parcult'])) {
        $valorParcelaFinalSemDescontos = removeVirgulas(number_format($_POST['parcult'], 2));
        $valorParcelaFinalComDescontos = (int)$descontoTotal ? calculaPercentual($percentualTotalDescontos, $valorParcelaFinalSemDescontos) : $valorParcelaFinalSemDescontos;
    }

    if (isset($_POST['parcval'])) {
        $valorParcelaSemDescontos = removeVirgulas(number_format($_POST['parcval'], 2));
        $valorParcelaComDescontos = (int)$descontoTotal ? calculaPercentual($percentualTotalDescontos, $valorParcelaSemDescontos) : $valorParcelaSemDescontos;
    }
}

/**
 * a vari?vel $parc vem do programa cai3_gerfinanc062.php
 */

$modoBuscaMatricula = !empty($ver_matric) ? "M-{$ver_matric}" : '';
$modoBuscaInscricao = !empty($ver_inscr) ? "I-{$ver_inscr}" : '';
$modoBuscaCGM = !empty($ver_numcgm) ? "C-{$ver_numcgm}" : '';

$codigosParcelamentos = $parc[0];

$campos = implode(',', array(
    "arrecad.k00_numpar as parcela",
    "arrecad.k00_receit",
    "to_char(arrecad.k00_dtvenc, 'DD/MM/YYYY')  as data_vencimento",
    "sum(arrecad.k00_valor) as valor_parcela",
));

$sqlParcelamento = "
    select x.parcela, x.data_vencimento, sum(x.valor_parcela) as valor_parcela from (
        select {$campos}
          from termo
               inner join arrecad on arrecad.k00_numpre = termo.v07_numpre
         where termo.v07_parcel in ({$codigosParcelamentos})
         group by arrecad.k00_receit, arrecad.k00_numpar, arrecad.k00_dtvenc
         order by arrecad.k00_numpar
     ) as x group by parcela, data_vencimento order by 1;
";

$buscaParcelamento = db_query($sqlParcelamento);

if (!$buscaParcelamento) {
    die("Não foi possível pesquisar os dados do parcelamento. " . pg_last_error());
}
$totalRegistros = pg_num_rows($buscaParcelamento);

$pdf = new PDFDocument(PDFDocument::PRINT_PORTRAIT);
$pdf->SetFontSize(8);
$pdf->open();
$pdf->addHeaderDescription("SIMULAÇÃO DE PARCELAMENTO");
$pdf->addHeaderDescription("SIMULAÇÃO EM {$totalRegistros} PARCELA(S)");
$pdf->addHeaderDescription("Contribuinte: " . $z01_nome);
$pdf->addHeaderDescription("Data de Emissão: " . date('d/m/Y', db_getsession("DB_datausu")));
$pdf->addHeaderDescription("Origem: {$modoBuscaCGM}{$modoBuscaInscricao}{$modoBuscaMatricula}");
$pdf->addPage();

$pdf->SetFontSize(10);
$pdf->setBold(true);
$pdf->cell($pdf->getAvailWidth(), 10, 'Simulação de Parcelamento', 0, 1, 'C');
$pdf->setBold(false);
$pdf->SetFontSize(8);

$totalOrigem = quadro_debitos_origem($pdf, $codigosParcelamentos, $regraParcelamento);

try {
    $custas = mostrarQuadroCustas($codigosParcelamentos, $pdf);
} catch (Exception $e) {
    die($e->getMessage());
}

$agrupamentoCustas = array();
$agrupamentoHonorarios = array();

foreach ($custas as $custa) {
    if (empty($agrupamentoCustas[$custa['iParcela']])) {
        $agrupamentoCustas[$custa['iParcela']] = 0;
    }

    if (empty($agrupamentoHonorarios[$custa['iParcela']])) {
        $agrupamentoHonorarios[$custa['iParcela']] = 0;
    }

    if ($custa['bHonorario']) {
        $agrupamentoHonorarios[$custa['iParcela']] += $custa['fValor'];
    } else {
        $agrupamentoCustas[$custa['iParcela']] += $custa['fValor'];
    }
}

$pdf->ln(5);
cabecalho($pdf);

$totalizadorTributoComAcrescimos = 0;
$totalizadorTributoSemAcrescimos = 0;
$totalizadorHonorario            = 0;
$totalizadorCustas               = 0;
$totalizadorParcelas             = 0;

$textoAviso ="Obs.: Os valores apresentados nesta simulação são meramente demonstrativos, podendo sofrer alterações conforme regulamentação.
          O campo Total está listando o valor com desconto por regra, quando houver, considerando pagamento até o vencimento.
          Os valor das parcelas são calculados até a data desta simulação e podem sofrer acréscimos conforme legislação.
          ";
for ($rowParcela = 0; $rowParcela < $totalRegistros; $rowParcela++) {
    $stdParcelamento = db_utils::fieldsMemory($buscaParcelamento, $rowParcela);
    $sql = "select 
sum(
fc_corre(arrecad.k00_receit, arrecad.k00_dtoper, arrecad.k00_valor::float8, arrecad.k00_dtvenc, extract(year from arrecad.k00_dtvenc)::int4, arrecad.k00_dtvenc) +
(
( fc_corre(arrecad.k00_receit, arrecad.k00_dtoper, arrecad.k00_valor::float8, arrecad.k00_dtvenc, extract(year from arrecad.k00_dtvenc)::int4, arrecad.k00_dtvenc) ) * 
( fc_juros(arrecad.k00_receit, arrecad.k00_dtvenc, arrecad.k00_dtvenc, arrecad.k00_dtoper, false, extract(year from arrecad.k00_dtvenc)::integer)::numeric(20,10) ) ) +

(          
( fc_corre(arrecad.k00_receit, arrecad.k00_dtoper, arrecad.k00_valor::float8, arrecad.k00_dtvenc, extract(year from arrecad.k00_dtvenc)::int4, arrecad.k00_dtvenc) ) * 
  fc_multa(arrecad.k00_receit, arrecad.k00_dtvenc, arrecad.k00_dtvenc, arrecad.k00_dtoper, extract(year from arrecad.k00_dtvenc)::integer)::numeric(20, 10)
)
) as valor from arrecad where k00_numpre = (select v07_numpre from termo where v07_parcel = {$codigosParcelamentos})
  and k00_numpar = " . $stdParcelamento->parcela;

    $rsResult = db_query($sql);

    if (!$rsResult) {
        throw new Exception("Ocorreu um erro ao verificar se a custa já foi paga.");
    }

    $oValor = pg_fetch_object($rsResult, 0);

    $stdParcelamento->valor_parcela = $oValor->valor;

    $stdParcelamento->valor_honorario = !empty($agrupamentoHonorarios[$stdParcelamento->parcela])   ? $agrupamentoHonorarios[$stdParcelamento->parcela]  : 0;
    $stdParcelamento->valor_custas    = !empty($agrupamentoCustas[$stdParcelamento->parcela]) ? $agrupamentoCustas[$stdParcelamento->parcela] : 0;
    $valorTotalParcelaComDesconto     = $rowParcela == 0 ? $valorParcelaInicialComDescontos : ($rowParcela == $totalRegistros - 1  ? $valorParcelaFinalComDescontos : ($valorParcelaComDescontos));
    $valorTotalParcelaSemDesconto     = $rowParcela == 0 ? $valorParcelaInicialSemDescontos  : ($rowParcela == $totalRegistros - 1  ? $valorParcelaFinalSemDescontos  : ($valorParcelaSemDescontos));

    $valorTotal                       = $stdParcelamento->valor_parcela + $stdParcelamento->valor_honorario + $stdParcelamento->valor_custas;
    $stdParcelamento->valor_parcela   = $stdParcelamento->valor_parcela;
    $stdParcelamento->valor_honorario = $stdParcelamento->valor_honorario;
    $stdParcelamento->valor_custas    = $stdParcelamento->valor_custas;

    $pdf->cell(32, 4, $stdParcelamento->parcela, 1, 0, 'C');
    $pdf->cell(32, 4, $stdParcelamento->data_vencimento, 1, 0, 'C');
    $pdf->cell(32, 4, formataValorParaTabela($valorTotalParcelaComDesconto), 1, 0, 'R');
    $pdf->cell(32, 4, formataValorParaTabela($stdParcelamento->valor_honorario), 1, 0, 'R');
    $pdf->cell(32, 4, formataValorParaTabela($stdParcelamento->valor_custas), 1, 0, 'R');
    $pdf->cell(31, 4, formataValorParaTabela(
                        formataValorParaCalculo($valorTotalParcelaComDesconto)
                        + formataValorParaCalculo($stdParcelamento->valor_honorario)
                        + formataValorParaCalculo($stdParcelamento->valor_custas)
                      ), 1, 1, 'R');

    $totalizadorTributoSemAcrescimos += formataValorParaCalculo($valorTotalParcelaComDesconto);
    $totalizadorHonorario            += formataValorParaCalculo($stdParcelamento->valor_honorario);
    $totalizadorCustas               += formataValorParaCalculo($stdParcelamento->valor_custas);
    $totalizadorParcelas             += (
        formataValorParaCalculo($valorTotalParcelaComDesconto)
        + formataValorParaCalculo($stdParcelamento->valor_honorario)
        + formataValorParaCalculo($stdParcelamento->valor_custas)
    );
}

$pdf->setBold(true);
$pdf->cell(64, 4, "Total", 1, 0, 'C');
$pdf->cell(32, 4, trim(db_formatar($totalizadorTributoSemAcrescimos, 'f')), 1, 0, 'R');
$pdf->cell(32, 4, trim(db_formatar($totalizadorHonorario, 'f')), 1, 0, 'R');
$pdf->cell(32, 4, trim(db_formatar($totalizadorCustas, 'f')), 1, 0, 'R');
$pdf->cell(31, 4, trim(db_formatar($totalizadorTributoSemAcrescimos+$totalizadorHonorario+$totalizadorCustas, 'f')), 1, 1, 'R');
$pdf->setBold(false);

$pdf->ln(4);
$pdf->setBold(true);
$pdf->multicell($pdf->getAvailWidth(), 4, $textoAviso, 'J');
$pdf->setBold(false);
$nomeArquivo = 'SIMULACAO_' . date('dmY_His');
$pdf->savePDF($nomeArquivo);
$nomeArquivo = "{$nomeArquivo}.pdf";


/**
 * @param PDFDocument $pdf
 */
function cabecalho(PDFDocument $pdf)
{

    $pdf->setBold(true);
    $pdf->cell(191, 4, 'PARCELAMENTO SIMULADO', 1, 1, 'C');
    $pdf->cell(32, 4, 'Parcela', 1, 0, 'C');
    $pdf->cell(32, 4, 'Vencimento', 1, 0, 'C');
    $pdf->cell(32, 4, 'Tributo', 1, 0, 'C');
    $pdf->cell(32, 4, 'Honorário', 1, 0, 'C');
    $pdf->cell(32, 4, 'Custas', 1, 0, 'C');
    $pdf->cell(31, 4, 'Total', 1, 1, 'C');
    $pdf->setBold(false);
}

function buscarCustas($codigoTermo)
{
    $termoRepository = TermoRepository::getInstance()
        ->setReturnFullItem(true);

    $termo = $termoRepository->getByCode($codigoTermo);

    if (empty($termo)) {
        return array();
    }

    $ultimaParcela = $termo->getTotalParcelas();

    $simulacaoService = new Service\Simulacao($termo);
    $inicialPartilhas = $simulacaoService->processar();

    $termoTaxasParcela = TermoTaxaParcelaRepository::getInstance()->getByInstituicao();

    $custas = array();

    foreach ($inicialPartilhas as $key => $inicialPartilha) {
        foreach ($inicialPartilha->getCustas() as $custa) {
            $parcela = 0;
            $codigoTaxa = $custa->getTaxa()->getCodigoTaxa();

            foreach ($termoTaxasParcela as $termoTaxaParcela) {
                if ($termoTaxaParcela->getTaxa() == $custa->getCodigoTaxa()) {
                    $parcela = $termoTaxaParcela->getNumpar();

                    if ($parcela > $ultimaParcela) {
                        $parcela = $ultimaParcela;
                    }
                }
            }

            $liberaParcela = false;

            if ($inicialPartilha instanceof ProcessoForoPartilha) {
                $codigo = $inicialPartilha->getCodigoProcessoForo();

                $processoForoRepository = ProcessoForoRepository::getInstance();
                $processoForoRepository->setReturnFullItem(true);
                $model = $processoForoRepository->getByCodigo($codigo);

                $repository = ProcessoForoPartilhaRepository::getInstance();

                $liberaParcela = true;
            } else {
                if ($inicialPartilha instanceof InicialPartilha) {
                    $codigo = $inicialPartilha->getCodigoInicial();

                    $inicialRepository = InicialRepository::getInstance();
                    $inicialRepository->setReturnFullItem(true);
                    $model = $inicialRepository->getByCode($codigo);

                    $repository = InicialPartilhaRepository::getInstance();

                    $liberaParcela = true;
                }
            }

            $valorPago = 0;
            $valorCusta = $custa->getValor();

            if ($liberaParcela AND $custa->getTaxa()->isAplicaHonorario()) {
                $valorPago   = $repository->getValorPago($custa->getTaxa(), $model);
                $valorCusta -= $valorPago;
                
                if ($valorCusta < 0) {
                    $valorCusta = 0;
                }
            }

            if ($custa->getTaxa()->isAplicaHonorario() && $model->getParcelasHonorarios() > 0) {
                for ($parcela = 1; $parcela <= $model->getParcelasHonorarios(); $parcela++) {
                    $valor = 0;
                    if (isset($custas[$parcela . $codigoTaxa])) {
                        $valor = $custas[$parcela . $codigoTaxa]->fValor;
                    }

                    $custas[$parcela . $codigoTaxa . $codigo] = (object) array(
                        "iParcela"   => $parcela,
                        "sDescricao" => $custa->getTaxa()->getDescricao(),
                        "fValor"     => $valor + round($valorCusta / $model->getParcelasHonorarios(),2),
                        "sStatus"    => 'A pagar',
                        "lHonorario" => $custa->getTaxa()->isAplicaHonorario()
                    );
                }
            } else {
                $custas[$parcela . $codigoTaxa . $codigo] = (object) array(
                    "iParcela"   => $parcela,
                    "sDescricao" => $custa->getTaxa()->getDescricao(),
                    "fValor"     => $valorCusta,
                    "sStatus"    => 'A pagar',
                    "lHonorario" => $custa->getTaxa()->isAplicaHonorario()
                );
            }
        }
    }

    $sql = "select
            k00_tipo,
            v07_valor,
            v07_totpar
        from
            termo
        inner join arrecad on v07_numpre = k00_numpre
        where v07_parcel = ".$codigoTermo;
    $tipo = pg_fetch_result(db_query($sql),0);
    $total = pg_fetch_result(db_query($sql),1);

    //DIVIDA ATIVA SEM CDA 
    if($tipo == 14){
        $sql = "select
                    ar29_numpar,
                    ar36_descricao,
                    ar36_perc,
                    ar36_valor,
                    ar36_valormax,
                    ar36_valormin,
                    ar36_receita
                from
                    taxa
                inner join termotaxaparc on ar29_taxa = ar36_sequencial
                where
                    ar36_debitossemprocesso = true;";
        $rTaxas = pg_fetch_all(db_query($sql));

        foreach ($rTaxas as $key => $taxa) {
            $parcela = $taxa['ar29_numpar']; 
            $pctm = $taxa['ar36_perc'];
            $descricao = $taxa['ar36_descricao'];
            if ($pctm > 0) {
                $valor_taxa = round(($total / 100 * $pctm), 2);

                if ($taxa['ar36_valormin'] > 0 && $valor_taxa < $taxa['ar36_valormin']) {
                   $valor_taxa = $taxa['ar36_valormin'];
                }

                if ($taxa['ar36_valormax'] > 0 && $valor_taxa > $taxa['ar36_valormax']) {
                    $valor_taxa = $taxa['ar36_valormax'];
                }
            } else if ($taxa['ar36_valor'] > 0) {
                $valor_taxa = $taxa['ar36_valor'];
            }

            $custas = array(
                1280796 => (object) array(
                    "iParcela"   => $parcela,
                    "sDescricao" => $descricao,
                    "fValor"     => $valor_taxa,
                    "sStatus"    => 'A pagar',
                    "lHonorario" => true
                )
            );
        }
    }
    return $custas;
}

function mostrarQuadroCustas($parcel, $pdf)
{
    $termoRepository = TermoRepository::getInstance()->setReturnFullItem(true);

    $termo = $termoRepository->getByCode($parcel);

    if (empty($termo)) {
        return array();
    }

    $ultimaParcela = $termo->getTotalParcelas();

    $service = new Service\Documento($termo);
    $inicialPartilhas = $service->processar();

    $custas = array();

    $termoTaxasParcela = TermoTaxaParcelaRepository::getInstance()->getByInstituicao();

    foreach ($inicialPartilhas as $key => $inicialPartilha) {
        $liberaParcela = false;

        if ($inicialPartilha instanceof ProcessoForoPartilha) {
            $codigo        = $inicialPartilha->getCodigoProcessoForo();
            $liberaParcela = true;
            $processoForoRepository = ProcessoForoRepository::getInstance();
            $processoForoRepository->setReturnFullItem(true);
            $model = $processoForoRepository->getByCodigo($codigo);

            $repository    = ProcessoForoPartilhaRepository::getInstance();
        } else {
            if ($inicialPartilha instanceof InicialPartilha) {
                $codigo        = $inicialPartilha->getCodigoInicial();
                $liberaParcela = true;

                $inicialRepository = InicialRepository::getInstance();
                $inicialRepository->setReturnFullItem(true);
                $model = $inicialRepository->getByCode($codigo);

                $repository    = InicialPartilhaRepository::getInstance();
            }
        }

        if ($liberaParcela) {
            $model->setCodigo($codigo);

            $honorarioRepository = HonorarioParcelamentoRepository::getInstance();
            $honorario = $honorarioRepository->getByFactory($model);
        }

        #################################################################################

        foreach ($inicialPartilha->getCustas() as $custa) {
            $parcela = 0;
            $codigoTaxa = $custa->getTaxa()->getCodigoTaxa();

            foreach ($termoTaxasParcela as $termoTaxaParcela) {
                if ($termoTaxaParcela->getTaxa() == $custa->getCodigoTaxa()) {
                    $parcela = $termoTaxaParcela->getNumpar();

                    if ($parcela > $ultimaParcela) {
                        $parcela = $ultimaParcela;
                    }
                }
            }

            #################################################################################

            $bHonorario      = $custa->getTaxa()->isAplicaHonorario();
            $iNumeroParcelas = (!empty($honorario) ? $honorario->getNumeroParcelas() : 0);

            $iParcela   = $parcela;
            $sDescricao = $custa->getTaxa()->getDescricao();
            $fValor     = $custa->getValor();

            if ($bHonorario && intval($iNumeroParcelas) > 0) {
                $valorPago = $repository->getValorPago($custa->getTaxa(), $model, $termo->getDataLancamento());
                $valor     = $fValor - $valorPago;
                $fValor    = ($valorPago > 0 ? $valor : $fValor);

                if ($valor > 0) {
                    $fValor = $fValor / $model->getParcelasHonorarios();

                    for ($parcela = $iParcela; $parcela <= $model->getParcelasHonorarios(); $parcela++) {
                        $valorzinho = 0;

                        if (isset($custas[$parcela . $codigoTaxa])) {
                            $valorzinho = $custas[$parcela . $codigoTaxa]['fValor'];
                        }

                        $custas[$parcela . $codigoTaxa] = array(
                            "iParcela"   => (string) $parcela,
                            "sDescricao" => $sDescricao,
                            "fValor"     => $valorzinho + $fValor,
                            "bHonorario" => $bHonorario
                        );
                    }
                }
            } else {
                $custas[$parcela . $codigoTaxa] = array(
                    "iParcela"   => $parcela,
                    "sDescricao" => $sDescricao,
                    "fValor"     => $fValor,
                    "bHonorario" => $bHonorario
                );
            }

            #################################################################################
        }
    }

    if (!empty($custas)) {
        $aCustasSomadas = array();

        #################################################################################

        foreach ($custas as $oCustas) {
            if (isset($aCustasSomadas[$oCustas['sDescricao']]) and !$oCustas["bHonorario"]) {
                $aCustasSomadas[$oCustas['sDescricao']]['fValor'] += $oCustas['fValor'];
            } else {
                if (!$oCustas["bHonorario"]) {
                    $aCustasSomadas[$oCustas['sDescricao']] = array(
                        "iParcela"   => $oCustas['iParcela'],
                        "sDescricao" => $oCustas['sDescricao'],
                        "fValor"     => $oCustas['fValor'],
                        "bHonorario" => $oCustas["bHonorario"]
                    );
                } else {
                    $aCustasSomadas[$oCustas['sDescricao']][] = array(
                        "iParcela"   => $oCustas['iParcela'],
                        "sDescricao" => $oCustas['sDescricao'],
                        "fValor"     => $oCustas['fValor'],
                        "bHonorario" => $oCustas["bHonorario"]
                    );
                }
            }
        }


        foreach ($aCustasSomadas as $key => $aCusta) {
            if (intval(array_sum(array_map('is_array', $aCusta))) > 0) {
                unset($aCustasSomadas[$key]);

                foreach ($aCusta as $honorarios) {
                    $aCustasSomadas[$honorarios['sDescricao'] . " - " . $honorarios['iParcela']] = array(
                        "iParcela"   => $honorarios['iParcela'],
                        "sDescricao" => $honorarios['sDescricao'],
                        "fValor"     => $honorarios['fValor'],
                    );
                }
            }
        }

        foreach ($aCustasSomadas as $parcela) {
            $parcelas[] = $parcela['iParcela'];
        }

        array_multisort($parcelas, SORT_ASC, $aCustasSomadas);

        #################################################################################

        $agrupamentoValoresAdicionais = array();

        foreach ($custas as $custa) {
            if (array_key_exists($custa['sDescricao'] ,$agrupamentoValoresAdicionais)) {
                $agrupamentoValoresAdicionais[$custa['sDescricao']] = (float)$agrupamentoValoresAdicionais[$custa['sDescricao']] + (float)$custa['fValor'];
            } else {
                $agrupamentoValoresAdicionais[$custa['sDescricao']] = (float)$custa['fValor'];
            }
        }

        if (count($agrupamentoValoresAdicionais) > 0) {
            $totalizadorValoresAdicionais = 0;
        
            $pdf->setBold(true);
            $pdf->cell(95, 4, 'VALORES ADICIONAIS APLICADOS', 1, 1, 'C');
            $pdf->cell(65, 4, 'Receita', 1, 0, 'C');
            $pdf->cell(30, 4, 'Valor', 1, 1, 'C');
            $pdf->setBold(false);
        
            foreach ($agrupamentoValoresAdicionais as $descricao => $valorAplicado) {
                $totalizadorValoresAdicionais += $valorAplicado;
            
                $pdf->cell(65, 4, $descricao, 1, 0, 'L');
                $pdf->cell(30, 4, db_formatar($valorAplicado,'f'), 1, 1, 'R');
            }
        
            $pdf->setBold(true);
            $pdf->cell(65, 4, 'Total', 1, 0, 'L');
            $pdf->cell(30, 4, db_formatar($totalizadorValoresAdicionais, 'f'), 1, 1, 'R');
            $pdf->setBold(false);
        }

        $pdf->ln(5);
    }

    return $custas;
}

function quadro_debitos_origem(PDFDocument $pdf, $parcelamento, $codigoRegraParcelamento)
{
    $oDaoCadTipoParc = new cl_cadtipoparc();

    $sSqlRegraParcelamento = $oDaoCadTipoParc->sql_query_file(null, 'k40_separajuremul', null, "k40_codigo = $codigoRegraParcelamento");
    $rsRegraParcelamento   = db_query($sSqlRegraParcelamento);
    $regraParcelamento     = db_utils::getCollectionByRecord($rsRegraParcelamento)[0];

    $iFormaCorrecao = $regraParcelamento->k40_separajuremul == 't';

    $sql = "select * from termoreparc where v08_parcel = $parcelamento limit 1";

    $result_reparc = db_query($sql) or die($sql);

    if (pg_num_rows($result_reparc) > 0) {
        $reparcelamento = true;

        // select que tras os reparcelamentos corrigindo os valores com fc_calculaold
        $sql = " select 1 as select,                                                                                     \n";
        $sql .= "        debitos_old.v08_parcelorigem,                                                                    \n";
        $sql .= "        debitos_old.k00_numpar  as v01_exerc,                                                            \n";
        $sql .= "        debitos_old.k03_tipo,                                                                            \n";
        $sql .= "        debitos_old.k00_tipo    as tipo,                                                                 \n";
        $sql .= "        debitos_old.k00_descr,                                                                           \n";
        $sql .= "        debitos_old.k00_valor   as valor,                                                                \n";
        $sql .= "        debitos_old.vlrcor      as vlrcor,                                                               \n";
        $sql .= "        debitos_old.vlrjuros    as juros,                                                                \n";
        $sql .= "        debitos_old.vlrmulta    as multa,                                                                \n";
        $sql .= "        debitos_old.vlrdesconto as desconto,                                                             \n";
        $sql .= "        debitos_old.k00_dtvenc  as v01_dtvenc,                                                           \n";
        $sql .= "        debitos_old.v07_numpre,                                                                          \n";
        $sql .= "        debitos_old.k00_numpre,                                                                          \n";
        $sql .= "        debitos_old.k00_numpar,                                                                          \n";
        $sql .= "        (select coalesce(k00_matric, 0)                                                                  \n";
        $sql .= "           from arrematric                                                                               \n";
        $sql .= "                inner join termo on v07_numpre = k00_numpre                                              \n";
        $sql .= "                where v07_parcel = v08_parcelorigem                                                      \n";
        $sql .= "                order by k00_perc desc limit 1) as matric,                                               \n";
        $sql .= "        (select coalesce(k00_inscr, 0)                                                                   \n";
        $sql .= "           from arreinscr                                                                                \n";
        $sql .= "                inner join termo on v07_numpre = k00_numpre                                              \n";
        $sql .= "          where v07_parcel = v08_parcelorigem                                                            \n";
        $sql .= "          order by k00_perc desc limit 1) as inscr,                                                      \n";
        $sql .= "        0 as contr,                                                                                      \n";
        $sql .= "        '' as nomematric,                                                                                \n";
        $sql .= "        '' as nomeinscr,                                                                                 \n";
        $sql .= "        '' as nomecontr,                                                                                 \n";
        $sql .= "        '' as v03_descr                                                                                  \n";
        $sql .= "   from (                                                                                                \n";
        $sql .= "          select distinct                                                                                \n";
        $sql .= "                 termoreparc.*,                                                                          \n";
        $sql .= "                 arretipo.*,                                                                             \n";
        $sql .= "                 coalesce(tipoparc.descjur,0) as descjur,                                                \n";
        $sql .= "                 coalesce(tipoparc.descmul,0) as descmul,                                                \n";
        $sql .= "                 coalesce(tipoparc.descvlr,0) as desccor,                                                \n";
        $sql .= "                 termoori.v07_numpre,                                                                    \n";
        $sql .= "                 arreold.k00_numcgm ,                                                                    \n";
        $sql .= "                 arreold.k00_receit ,                                                                    \n";
        $sql .= "                 arreold.k00_tipojm,                                                                     \n";
        $sql .= "                 arreold.k00_numpre ,                                                                    \n";
        $sql .= "                 arreold.k00_numpar ,                                                                    \n";
        $sql .= "                 arreold.k00_numtot ,                                                                    \n";
        $sql .= "                 arreold.k00_numdig ,                                                                    \n";
        $sql .= "                 arreold.k00_valor ,                                                                     \n";
        $sql .= "                 arreold.k00_dtvenc,                                                                     \n";
        $sql .= "                 arreoldcalc.k00_vlrcor as vlrcor,                                                       \n";
        $sql .= "                 arreoldcalc.k00_vlrjur as vlrjuros,                                                     \n";
        $sql .= "                 arreoldcalc.k00_vlrmul as vlrmulta,                                                     \n";
        $sql .= "                 arreoldcalc.k00_vlrdes +                                                                \n";
        if ($iFormaCorrecao) {
            $sql .= "             (round( arreoldcalc.k00_vlrjur * descjur / 100,2)) + (round( arreoldcalc.k00_vlrmul * descmul / 100,2)) + (round( (arreoldcalc.k00_vlrcor - arreoldcalc.k00_vlrhis) * descvlr / 100,2)) as vlrdesconto, \n";
        } else {
            $sql .= "             (round( arreoldcalc.k00_vlrjur * descjur / 100,2)) + (round( arreoldcalc.k00_vlrmul * descmul / 100,2)) as vlrdesconto, \n";
        }
        $sql .= "                 (arreoldcalc.k00_vlrcor + arreoldcalc.k00_vlrjur + arreoldcalc.k00_vlrmul - arreoldcalc.k00_vlrdes) as total \n";
        $sql .= "            from termoreparc                                                                             \n";
        $sql .= "                 inner join termo termoori   on v08_parcelorigem         = termoori.v07_parcel           \n";
        $sql .= "                                            and termoori.v07_instit      = " . db_getsession('DB_instit') . "\n";
        $sql .= "                 inner join arreold          on termoori.v07_numpre      = arreold.k00_numpre            \n";
        $sql .= "                 inner join arreoldcalc      on arreoldcalc.k00_numpre   = arreold.k00_numpre            \n";
        $sql .= "                                            and arreoldcalc.k00_numpar   = arreold.k00_numpar            \n";
        $sql .= "                                            and arreoldcalc.k00_receit   = arreold.k00_receit            \n";
        $sql .= "                 inner join arretipo         on arreold.k00_tipo         = arretipo.k00_tipo             \n";
        $sql .= "                 inner join cadtipo          on arretipo.k03_tipo        = cadtipo.k03_tipo              \n";
        $sql .= "                 inner join termo termoatual on termoatual.v07_parcel    = termoreparc.v08_parcel        \n";
        $sql .= "                 left  join cadtipoparc      on cadtipoparc.k40_codigo   = termoatual.v07_desconto       \n";
        $sql .= "                 left  join ( select *                                                                   \n";
        $sql .= "                                from tipoparc                                                            \n";
        $sql .= "                                     inner join cadtipoparc on tipoparc.cadtipoparc   = cadtipoparc.k40_codigo        \n";
        $sql .= "                                                           and cadtipoparc.k40_instit = " . db_getsession('DB_instit') . "\n";
        $sql .= "                                     inner join termo       on termo.v07_desconto     = cadtipoparc.k40_codigo        \n";
        $sql .= "                                                           and termo.v07_instit       = " . db_getsession('DB_instit') . "\n";
        $sql .= "                               where termo.v07_parcel = $parcelamento                                                       \n";
        $sql .= "                                 and termo.v07_instit = " . db_getsession('DB_instit') . "                                \n";
        $sql .= "                                 and termo.v07_dtlanc between tipoparc.dtini and tipoparc.dtfim                       \n";
        $sql .= "                                 and termo.v07_totpar between 1 and tipoparc.maxparc order by maxparc limit 1 ) as tipoparc on tipoparc.cadtipoparc = cadtipoparc.k40_codigo \n";
        $sql .= "                 left  join cadtipoparcdeb   on cadtipoparc.k40_codigo      = cadtipoparcdeb.k41_cadtipoparc \n";
        $sql .= "                                            and cadtipoparcdeb.k41_arretipo = arreold.k00_tipo               \n";
        $sql .= "                                            and arreold.k00_dtvenc between k41_vencini and k41_vencfim       \n";
        $sql .= "                 left  join arrecad          on arrecad.k00_numpre          = termoatual.v07_numpre          \n";
        $sql .= "  where v08_parcel = $parcelamento  ) as debitos_old                                                               \n";

        $sql .= " union all \n";

        // select que tras parcelamentos de divida
        $sql .= " select 2 as select,                                                                                     \n";
        $sql .= "        0 as v08_parcelorigem,                                                                           \n";
        $sql .= "        divida.v01_exerc,                                                                                \n";
        $sql .= "        5 as k03_tipo,                                                                                   \n";
        $sql .= "        5 as tipo,                                                                                       \n";
        $sql .= "        (select k00_descr from arretipo where k00_tipo = 5 limit 1) as k00_descr,                        \n";
        $sql .= "        termodiv.valor,                                                                                  \n";
        $sql .= "        termodiv.vlrcor,                                                                                 \n";
        $sql .= "        termodiv.juros,                                                                                  \n";
        $sql .= "        termodiv.multa,                                                                                  \n";
        if ($iFormaCorrecao) {
            $sql .= "    termodiv.vlrdesccor + termodiv.vlrdescjur + termodiv.vlrdescmul + termodiv.desconto as desconto,   \n";
        } else {
            $sql .= "    termodiv.vlrdesccor + termodiv.vlrdescjur + termodiv.vlrdescmul + termodiv.desconto as desconto,   \n";
        }
        $sql .= "        divida.v01_dtvenc,                                                                               \n";
        $sql .= "        termo.v07_numpre,                                                                                \n";
        $sql .= "        divida.v01_numpre as k00_numpre,                                                                 \n";
        $sql .= "        divida.v01_numpar as k00_numpar,                                                                 \n";
        $sql .= "        coalesce(arrematric.k00_matric,0) as matric,                                                     \n";
        $sql .= "        coalesce(arreinscr.k00_inscr,0)   as inscr,                                                      \n";
        $sql .= "        coalesce(arrecontr.k00_contr,0)   as contr,                                                      \n";
        $sql .= "        case when a.j01_numcgm is not null then (select z01_nome from cgm where z01_numcgm = a.j01_numcgm) end as nomematric, \n";
        $sql .= "        case when q02_numcgm   is not null then (select z01_nome from cgm where z01_numcgm = q02_numcgm)   end as nomeinscr,  \n";
        $sql .= "        case when b.j01_numcgm is not null then (select z01_nome from cgm where z01_numcgm = b.j01_numcgm) end as nomecontr,  \n";
        $sql .= "        v03_descr                                                                                        \n";
        $sql .= "   from termodiv                                                                                         \n";
        $sql .= "        inner join termo       on v07_parcel            = parcel                                         \n";
        $sql .= "                              and v07_instit            = " . db_getsession('DB_instit') . "                 \n";
        $sql .= "        inner join divida      on v01_coddiv            = coddiv                                         \n";
        $sql .= "                              and v01_instit            = " . db_getsession('DB_instit') . "                 \n";
        $sql .= "        inner join proced      on v01_proced            = v03_codigo                                     \n";
        $sql .= "        left  join arrematric  on arrematric.k00_numpre = divida.v01_numpre                              \n";
        $sql .= "        left  join iptubase a  on arrematric.k00_matric = a.j01_matric                                   \n";
        $sql .= "        left  join arreinscr   on arreinscr.k00_numpre  = divida.v01_numpre                                \n";
        $sql .= "        left  join issbase     on arreinscr.k00_inscr   = issbase.q02_inscr                                \n";
        $sql .= "        left  join arrecontr   on arrecontr.k00_numpre  = divida.v01_numpre                                \n";
        $sql .= "        left  join contrib     on arrecontr.k00_contr   = contrib.d07_contri                             \n";
        $sql .= "        left  join iptubase b  on b.j01_matric          = contrib.d07_matric                             \n";
        $sql .= " where parcel = $parcelamento                                                                                  \n";

        $sql .= " union all \n";

        // select que tras parcelamentos de inicial
        $sql .= " select 3 as select,                                                                                     \n";
        $sql .= "        0 as v08_parcelorigem,                                                                           \n";
        $sql .= "        divida.v01_exerc,                                                                                \n";
        $sql .= "        18 as k03_tipo,                                                                                  \n"; // ver as colunas !!!
        $sql .= "        34 as tipo,                                                                                      \n";
        $sql .= "        (select k00_descr from arretipo where k00_tipo = 30 limit 1) as k00_descr,                       \n";
        $sql .= "        case when c.k00_vlrhis is not null then c.k00_vlrhis else divida.v01_vlrhis end as valor,        \n";
        $sql .= "        case when c.k00_vlrhis is not null then c.k00_vlrcor else divida.v01_vlrhis end as vlrcor,       \n";
        $sql .= "        coalesce(c.k00_vlrjur,0)  as juros,                                                              \n";
        $sql .= "        coalesce(c.k00_vlrmul,0)  as multa,                                                              \n";
        $sql .= "        coalesce(c.k00_vlrdes,0)  as desconto,                                                           \n";
        $sql .= "        divida.v01_dtvenc,                                                                               \n";
        $sql .= "        termo.v07_numpre,                                                                                \n";
        $sql .= "        termo.v07_numpre as k00_numpre,                                                                  \n";
        $sql .= "          divida.v01_numpar as k00_numpar,                                                                 \n";
        $sql .= "        coalesce(arrematric.k00_matric,0) as matric,                                                     \n";
        $sql .= "        coalesce(arreinscr.k00_inscr,0)   as inscr,                                                      \n";
        $sql .= "        coalesce(arrecontr.k00_contr,0)   as contr,                                                      \n";
        $sql .= "        case when a.j01_numcgm is not null then (select z01_nome from cgm where z01_numcgm = a.j01_numcgm) end as nomematric,\n";
        $sql .= "        case when q02_numcgm   is not null then (select z01_nome from cgm where z01_numcgm = q02_numcgm)   end as nomeinscr, \n";
        $sql .= "        case when b.j01_numcgm is not null then (select z01_nome from cgm where z01_numcgm = b.j01_numcgm) end as nomecontr, \n";
        $sql .= "        v03_descr                                                                                        \n";
        $sql .= "   from termoini                                                                                         \n";
        $sql .= "        inner join termo          on v07_parcel              = parcel                                    \n";
        $sql .= "                                 and v07_instit              = " . db_getsession('DB_instit') . "            \n";
        $sql .= "        inner join inicialcert    on inicialcert.v51_inicial = termoini.inicial                          \n";
        $sql .= "        inner join certid         on certid.v13_certid       = inicialcert.v51_certidao                  \n";
        $sql .= "                                 and certid.v13_instit       = " . db_getsession('DB_instit') . "            \n";
        $sql .= "        inner join certdiv        on certdiv.v14_certid      = certid.v13_certid                         \n";
        $sql .= "        inner join divida         on v01_coddiv              = v14_coddiv                                \n";
        $sql .= "                                 and v01_instit              = " . db_getsession('DB_instit') . "            \n";
        $sql .= "        inner join proced         on v01_proced              = v03_codigo                                \n";
        $sql .= "        left  join arreoldcalc c  on k00_numpre = v01_numpre and k00_numpar = v01_numpar                 \n";
        $sql .= "        left  join arrematric     on arrematric.k00_numpre   = divida.v01_numpre                         \n";
        $sql .= "        left  join iptubase a     on arrematric.k00_matric   = a.j01_matric                              \n";
        $sql .= "        left  join arreinscr        on arreinscr.k00_numpre    = divida.v01_numpre                         \n";
        $sql .= "        left  join issbase          on arreinscr.k00_inscr     = issbase.q02_inscr                         \n";
        $sql .= "        left  join arrecontr        on arrecontr.k00_numpre    = divida.v01_numpre                         \n";
        $sql .= "        left  join contrib        on arrecontr.k00_contr     = contrib.d07_contri                        \n";
        $sql .= "        left  join iptubase b     on b.j01_matric            = contrib.d07_matric                        \n";
        $sql .= " where parcel = {$parcelamento}                                                                                \n";

        $sql2 = "  select v08_parcelorigem,          \n";
        $sql2 .= "         v01_exerc,                 \n";
        $sql2 .= "         k03_tipo,                  \n";
        $sql2 .= "         tipo,                      \n";
        $sql2 .= "         k00_descr,                 \n";
        $sql2 .= "         sum(valor)    as valor,    \n";
        $sql2 .= "         sum(vlrcor)   as vlrcor,   \n";
        $sql2 .= "         sum(juros)    as juros,    \n";
        $sql2 .= "         sum(multa)    as multa,    \n";
        $sql2 .= "         sum(desconto) as desconto, \n";
        $sql2 .= "         v01_dtvenc,                \n";
        $sql2 .= "         v07_numpre,                \n";
        $sql2 .= "         k00_numpre,                \n";
        $sql2 .= "         k00_numpar,                \n";
        $sql2 .= "         matric,                    \n";
        $sql2 .= "         inscr,                     \n";
        $sql2 .= "         contr,                     \n";
        $sql2 .= "         nomematric,                \n";
        $sql2 .= "         nomeinscr,                 \n";
        $sql2 .= "         nomecontr,                 \n";
        $sql2 .= "         v03_descr                  \n";
        $sql2 .= "    from ($sql) as x                \n";
        $sql2 .= "group by v08_parcelorigem,          \n";
        $sql2 .= "         v01_exerc,                 \n";
        $sql2 .= "         k03_tipo,                  \n";
        $sql2 .= "         tipo,                      \n";
        $sql2 .= "         k00_descr,                 \n";
        $sql2 .= "         v01_dtvenc,                \n";
        $sql2 .= "         v07_numpre,                \n";
        $sql2 .= "         k00_numpre,                \n";
        $sql2 .= "         k00_numpar,                \n";
        $sql2 .= "         matric,                    \n";
        $sql2 .= "         inscr,                     \n";
        $sql2 .= "         contr,                     \n";
        $sql2 .= "         nomematric,                \n";
        $sql2 .= "         nomeinscr,                 \n";
        $sql2 .= "         nomecontr,                 \n";
        $sql2 .= "         v03_descr                  \n";
        $sql2 .= " order by v08_parcelorigem,         \n";
        $sql2 .= "          v01_exerc,                \n";
        $sql2 .= "          v07_numpre,               \n";
        $sql2 .= "              k00_numpar                \n";

        $sql = $sql2;
    } else {
        if (pg_num_rows($result) > 0) {
            // se for reparcelamento ou diversos...
            if (pg_result($result, 0, 'matric') > 0) {
                $numero = 'Matr. : ' . pg_result($result, 0, 'matric');
            } elseif (pg_result($result, 0, 'inscr') > 0) {
                $numero = 'Inscr.: ' . pg_result($result, 0, 'inscr');
            } else {
                $numero = 'Cgm : ' . pg_result($result, 0, 'v07_numcgm');
            }
            $xnumpre = pg_result($result, 0, 'v07_numpre');

            $sql = "select a.*, ";
            $sql .= "       a.k00_dtvenc as v01_dtvenc, ";
            $sql .= "      k00_numpar as v01_exerc, ";
            $sql .= "      cadtipo.k03_tipo, ";
            $sql .= "      arretipo.k00_descr, ";
            $sql .= "      dv09_descra as v03_descr, ";
            $sql .= "      coalesce(b.k00_matric) as matric, ";
            $sql .= "      coalesce(c.k00_inscr)  as inscr ";
            $sql .= " from arreold a ";
            $sql .= "      inner join diversos  on dv05_numpre = a.k00_numpre ";
            $sql .= "                          and dv05_instit = " . db_getsession('DB_instit') . "";
            $sql .= "      inner join procdiver on dv05_procdiver = dv09_procdiver ";
            $sql .= "                          and dv09_instit = " . db_getsession('DB_instit') . "";
            $sql .= "      inner join arretipo  on a.k00_tipo = arretipo.k00_tipo ";
            $sql .= "                          and arretipo.k00_instit = " . db_getsession('DB_instit');
            $sql .= "      inner join cadtipo   on arretipo.k03_tipo = cadtipo.k03_tipo ";
            $sql .= "      left outer join arrematric b  on b.k00_numpre = a.k00_numpre ";
            $sql .= "      left outer join arreinscr  c   on c.k00_numpre = a.k00_numpre ";
            $sql .= " where a.k00_numpre = $xnumpre ";
            $k00_descr = pg_result(db_query($sql), 0, "k00_descr");
            $xtipo = pg_result(db_query($sql), 0, "k00_tipo");
            $k03_tipo = pg_result(db_query($sql), 0, "k03_tipo");

            if ($k03_tipo == 4) {
                $sql1 = " select b.* ";
                $sql1 .= "   from arreold a ";
                $sql1 .= "        inner join arrematric c on c.k00_numpre = a.k00_numpre ";
                $sql1 .= "        inner join proprietario b on b.j01_matric = c.k00_matric ";
                $sql1 .= " where a.k00_numpre = $xnumpre limit 1";
                $tipo = 4;
                $setorquadralote = '';
            } else {
                if ($k03_tipo == 7) {
                    $tipo = 28;
                    $sql1 = "select z01_nome from cgm where z01_numcgm = " . pg_result(db_query($sql), 0, 'k00_numcgm');
                    $z01_nome = pg_result(db_query($sql1), 0, "z01_nome");
                } else {
                    $tipo = 21;
                    $sql1 = "select z01_nome from cgm where z01_numcgm = " . pg_result(db_query($sql), 0, 'k00_numcgm');
                    $z01_nome = pg_result(db_query($sql1), 0, "z01_nome");
                }
            }
        } else {
            $sql = " select * from (  ";
            $sql .= "     select 1 as ordem, ";
            $sql .= "              arrecad.k00_tipo,  ";
            $sql .= "              k03_tipo  ";
            $sql .= "     from termo ";
            $sql .= "              inner join arrecad    on termo.v07_numpre = arrecad.k00_numpre ";
            $sql .= "              inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre ";
            $sql .= "                                 and arreinstit.k00_instit = " . db_getsession('DB_instit');
            $sql .= "              inner join arretipo on arrecad.k00_tipo = arretipo.k00_tipo and arretipo.k00_instit = " . db_getsession('DB_instit');
            $sql .= "    where v07_parcel = $parcelamento and v07_instit = " . db_getsession('DB_instit');
            $sql .= " union ";
            $sql .= "    select 2 as ordem, ";
            $sql .= "                 arrecant.k00_tipo,  ";
            $sql .= "                 k03_tipo  ";
            $sql .= "      from termo ";
            $sql .= "                 inner join arrecant   on termo.v07_numpre = arrecant.k00_numpre ";
            $sql .= "                 inner join arreinstit on arreinstit.k00_numpre = arrecant.k00_numpre ";
            $sql .= "                                    and arreinstit.k00_instit = " . db_getsession('DB_instit');
            $sql .= "                 inner join arretipo on arrecant.k00_tipo = arretipo.k00_tipo and arretipo.k00_instit = " . db_getsession('DB_instit');
            $sql .= "     where v07_parcel = $parcelamento and v07_instit = " . db_getsession('DB_instit');
            $sql .= " union ";
            $sql .= "    select 3 as ordem, ";
            $sql .= "                 arreold.k00_tipo,  ";
            $sql .= "                 k03_tipo  ";
            $sql .= "      from termo ";
            $sql .= "                 inner join arreold   on termo.v07_numpre = arreold.k00_numpre ";
            $sql .= "                 inner join arreinstit on arreinstit.k00_numpre = arreold.k00_numpre ";
            $sql .= "                                    and arreinstit.k00_instit = " . db_getsession('DB_instit');
            $sql .= "                 inner join arretipo on arreold.k00_tipo = arretipo.k00_tipo and arretipo.k00_instit = " . db_getsession('DB_instit');
            $sql .= "     where v07_parcel = $parcelamento and v07_instit = " . db_getsession('DB_instit');
            $sql .= " ) as x order by ordem limit 1  ";
            $resarrecad = db_query($sql);
            if (pg_num_rows($resarrecad) > 0) {
                $tipo = pg_result($resarrecad, 0, 'k00_tipo');
                $k03_tipo = pg_result($resarrecad, 0, 'k03_tipo');
            } else {
                db_redireciona('db_erros.php?fechar=true&db_erro=Parcelas em aberto e/ou pagas não encontradas.');
                exit;
            }

            if ($k03_tipo == 13) { // parcelamento do foro
                $sql = "select distinct ";
                $sql .= "       max(x.z01_nome) as nomematric, ";
                $sql .= "       max(x.v03_descr) as v03_descr,  ";
                
                //$sql .= "       arreold.*,  ";
                $sql .= "       arreold.k00_numpre,  ";
                $sql .= "       arreold.k00_numpar,  ";
                $sql .= "       max(arreold.k00_numcgm) as k00_numcgm,  ";
                $sql .= "       max(arreold.k00_dtoper) as k00_dtoper,  ";
                $sql .= "       max(arreold.k00_receit) as k00_receit,  ";
                $sql .= "       max(arreold.k00_hist) as k00_hist,  ";
                $sql .= "       max(arreold.k00_dtvenc) as v01_dtvenc,  ";
                $sql .= "       max(arreold.k00_numtot) as k00_numtot,  ";
                $sql .= "       max(arreold.k00_numdig) as k00_numdig,  ";
                $sql .= "       max(arreold.k00_tipo) as k00_tipo,  ";
                $sql .= "       max(arreold.k00_tipojm) as k00_tipojm,  ";

                $sql .= "       max(k00_descr) as k00_descr, ";
                $sql .= "       max(k00_matric) as matric,  ";
                $sql .= "       max(k00_inscr) as inscr, ";
                $sql .= "       coalesce(max(vlrdesccor), 0) as vlrdesccor, ";
                $sql .= "       coalesce(max(vlrdescjur), 0) as vlrdescjur, ";
                $sql .= "       coalesce(max(vlrdescmul), 0) as vlrdescmul, ";
                $sql .= "       case when max(x.v01_exerc) = 0 then max(arreold.k00_numpar) else max(x.v01_exerc) end as v01_exerc, ";
                $sql .= "       max(x.v01_dtvenc) as v01_dtvenc,";
                $sql .= "       max(v70_codforo) as v70_codforo";
                $sql .= "  from (   select distinct  ";
                $sql .= "                case when v03_descr is null then 'Parcelamento : '||termo2.v07_parcel else v03_descr ";
                $sql .= "                end as v03_descr, ";
                $sql .= "                z01_nome, ";
                $sql .= "                vlrdesccor, ";
                $sql .= "                vlrdescjur, ";
                $sql .= "                vlrdescmul, ";
                $sql .= "                case when termo2.v07_numpre is not null then termo2.v07_numpre  ";
                $sql .= "                     else divida.v01_numpre  ";
                $sql .= "                end as numpre, ";
                $sql .= "                case when termo2.v07_numpre is not null then 0 ";
                $sql .= "                     else divida.v01_numpar ";
                $sql .= "                end as numpar, ";
                $sql .= "                case when termo2.v07_numpre is not null then 0 ";
                $sql .= "                     else divida.v01_exerc ";
                $sql .= "                end as v01_exerc, v01_dtvenc , v70_codforo, v70_sequencial";
                $sql .= "           from termo ";
                $sql .= "                inner join termoini            on termoini.parcel         = termo.v07_parcel ";
                $sql .= "                inner join inicial           on inicial.v50_inicial     = termoini.inicial ";
                $sql .= "                                            and inicial.v50_instit      = " . db_getsession('DB_instit');
                $sql .= "                inner join inicialcert       on inicialcert.v51_inicial = inicial.v50_inicial ";
                $sql .= "                inner join certid              on certid.v13_certid       = inicialcert.v51_certidao ";
                $sql .= "                                            and certid.v13_instit       = " . db_getsession('DB_instit');
                $sql .= "                left outer join certter        on certter.v14_certid      = inicialcert.v51_certidao ";
                $sql .= "                left outer join certdiv        on certdiv.v14_certid      = inicialcert.v51_certidao ";
                $sql .= "                left outer join termo termo2 on certter.v14_parcel      = termo2.v07_parcel ";
                $sql .= "                                            and termo2.v07_instit       = " . db_getsession('DB_instit');
                $sql .= "                left outer join divida         on divida.v01_coddiv       = certdiv.v14_coddiv ";
                $sql .= "                                            and divida.v01_instit       = " . db_getsession('DB_instit');
                $sql .= "                left outer join proced       on proced.v03_codigo       = divida.v01_proced ";
                $sql .= "                inner join cgm                 on termo.v07_numcgm        = z01_numcgm ";
                $sql .= "                left  join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial";
                $sql .= "                                 and processoforoinicial.v71_anulado is false";
                $sql .= "               left  join processoforo on processoforo.v70_sequencial = processoforoinicial.v71_processoforo";
                $sql .= "        where termo.v07_parcel = $parcelamento ) as x  ";
                $sql .= "    inner join arreold             on k00_numpre = x.numpre ";
                $sql .= "                                and (case when x.numpar > 0 then k00_numpar = x.numpar else true end ) ";
                $sql .= "                                and k00_receit not in (select j150_receita from juridico.processoforomulta where j150_processoforo = x.v70_sequencial) ";
                $sql .= "    inner join arretipo          on arreold.k00_tipo = arretipo.k00_tipo ";
                $sql .= "                                and arretipo.k00_instit = " . db_getsession('DB_instit');
                $sql .= "    left outer join arrematric     on arrematric.k00_numpre = arreold.k00_numpre ";
                $sql .= "    left outer join arreinscr      on arreinscr.k00_numpre  = arreold.k00_numpre ";
                $sql .= "group by arreold.k00_numpre, arreold.k00_numpar ";
                //$sql .= "    order by v01_dtvenc asc ";
            } elseif ($k03_tipo == 16) { // parcelamento de diversos
                $sql = "select distinct ";
                $sql .= "       max(x.z01_nome) as nomematric, ";
                $sql .= "       max(x.v03_descr) as v03_descr,  ";
                // $sql .= "       arreold.*,  ";
                $sql .= "       arreold.k00_numpre,  ";
                $sql .= "       arreold.k00_numpar,  ";
                $sql .= "       max(arreold.k00_numcgm) as k00_numcgm,  ";
                $sql .= "       max(arreold.k00_dtoper) as k00_dtoper,  ";
                $sql .= "       max(arreold.k00_receit) as k00_receit,  ";
                $sql .= "       max(arreold.k00_hist) as k00_hist,  ";
                $sql .= "       max(arreold.k00_dtvenc) as v01_dtvenc,  ";
                $sql .= "       max(arreold.k00_numtot) as k00_numtot,  ";
                $sql .= "       max(arreold.k00_numdig) as k00_numdig,  ";
                $sql .= "       max(arreold.k00_tipo) as k00_tipo,  ";
                $sql .= "       max(arreold.k00_tipojm) as k00_tipojm,  ";
                $sql .= "       max(k00_descr) as k00_descr, ";
                $sql .= "       max(k00_matric) as matric,  ";
                $sql .= "       max(k00_inscr) as inscr, ";
                $sql .= "       coalesce(max(vlrdescjur),0) as vlrdescjur, ";
                $sql .= "       coalesce(max(vlrdescmul),0) as vlrdescmul, ";
                $sql .= "       max(v01_exerc) as v01_exerc ";
                $sql .= "  from (   select distinct  ";
                $sql .= "                dv09_descra as v03_descr, ";
                $sql .= "                z01_nome, ";
                $sql .= "                dv10_vlrdescjur as vlrdescjur, ";
                $sql .= "                dv10_vlrdescmul as vlrdescmul, ";
                $sql .= "                diversos.dv05_numpre as numpre, ";
                $sql .= "                0 as numpar, ";
                $sql .= "                dv05_exerc as v01_exerc ";
                $sql .= "           from termo ";
                $sql .= "                inner join termodiver on termodiver.dv10_parcel   = termo.v07_parcel ";
                $sql .= "                inner join diversos   on termodiver.dv10_coddiver = dv05_coddiver ";
                $sql .= "                                     and dv05_instit = " . db_getsession('DB_instit') . "";
                $sql .= "                left  join procdiver  on procdiver.dv09_procdiver = diversos.dv05_procdiver ";
                $sql .= "                                     and dv09_instit = " . db_getsession('DB_instit') . "";
                $sql .= "                inner join cgm        on termo.v07_numcgm        = z01_numcgm ";
                $sql .= "        where termo.v07_parcel = $parcelamento and termo.v07_instit = " . db_getsession('DB_instit') . " ) as x  ";
                $sql .= "    inner join arreold             on k00_numpre = x.numpre ";
                $sql .= "                                and (case when x.numpar > 0 then k00_numpar = x.numpar else true end ) ";
                $sql .= "    inner join arretipo          on arreold.k00_tipo = arretipo.k00_tipo and arretipo.k00_instit = " . db_getsession('DB_instit');
                $sql .= "    left outer join arrematric     on arrematric.k00_numpre = arreold.k00_numpre ";
                $sql .= "    left outer join arreinscr      on arreinscr.k00_numpre  = arreold.k00_numpre ";
                $sql .= "group by arreold.k00_numpre, arreold.k00_numpar ";
            } elseif ($k03_tipo == 17) { // parcelamento de contribuicao de melhorias
                $sql = " select  ";
                $sql .= "        coalesce(termocontrib.vlrdescjur,0) as vlrdescjur,";
                $sql .= "        coalesce(termocontrib.vlrdescmul,0) as vlrdescmul,  ";
                $sql .= "        arreoldcalc.k00_vlrcor as vlrcor, ";
                $sql .= "        arreoldcalc.k00_vlrjur as vlrjuros, ";
                $sql .= "        arreoldcalc.k00_vlrmul as vlrmulta, ";
                $sql .= "        (arreoldcalc.k00_vlrcor + arreoldcalc.k00_vlrjur + arreoldcalc.k00_vlrmul - arreoldcalc.k00_vlrdes) as total, ";
                $sql .= "            extract(year from arreold.k00_dtoper) as v01_exerc, ";
                $sql .= "            'Contribuicao - '||d09_contri as v03_descr, ";
                $sql .= "            arreold.k00_tipo, ";
                $sql .= "            arreold.k00_dtvenc as v01_dtvenc, ";
                $sql .= "            arreold.k00_numpre as k00_numpre, ";
                $sql .= "              arreold.k00_numpar as k00_numpar, ";
                $sql .= "              arreold.k00_valor as valor, ";
                $sql .= "        arretipo.k00_descr, ";
                $sql .= "        coalesce(arrematric.k00_matric,0) as matric, ";
                $sql .= "        coalesce(arreinscr.k00_inscr,0) as inscr, ";
                $sql .= "        coalesce(arrecontr.k00_contr,0) as contr, ";
                $sql .= "        case when a.j01_numcgm is not null ";
                $sql .= "             then (select z01_nome from cgm where z01_numcgm = a.j01_numcgm) ";
                $sql .= "        end as nomematric, ";
                $sql .= "        case when q02_numcgm is not null ";
                $sql .= "             then (select z01_nome from cgm where z01_numcgm = q02_numcgm) ";
                $sql .= "        end as nomeinscr ";
                $sql .= "   from termocontrib ";
                $sql .= "        inner join termo       on v07_parcel             = parcel ";
                $sql .= "                              and v07_instit             = " . db_getsession('DB_instit');
                $sql .= "        inner join contricalc  on d09_sequencial         = contricalc ";
                $sql .= "        inner join arreold       on d09_numpre             = arreold.k00_numpre ";
                $sql .= "        inner join arreoldcalc on arreoldcalc.k00_numpre = arreold.k00_numpre ";
                $sql .= "                              and arreoldcalc.k00_numpar = arreold.k00_numpar ";
                $sql .= "                              and arreoldcalc.k00_receit = arreold.k00_receit ";
                $sql .= "        inner join arretipo    on arreold.k00_tipo       = arretipo.k00_tipo ";
                $sql .= "                              and arretipo.k00_instit    = " . db_getsession('DB_instit');
                $sql .= "        left  join arrematric  on arrematric.k00_numpre  = contricalc.d09_numpre ";
                $sql .= "        left  join iptubase a  on arrematric.k00_matric  = a.j01_matric ";
                $sql .= "        left  join arreinscr     on arreinscr.k00_numpre   = contricalc.d09_numpre ";
                $sql .= "        left  join issbase       on arreinscr.k00_inscr    = issbase.q02_inscr ";
                $sql .= "        left  join arrecontr     on arrecontr.k00_numpre   = contricalc.d09_numpre ";
                $sql .= " where parcel = $parcelamento";
            } else {
                $sql = " select  distinct ";
                $sql .= "        coalesce(termodiv.vlrdesccor,0) + coalesce(termodiv.vlrdescjur,0) + coalesce(termodiv.vlrdescmul,0) + coalesce(desconto,0) as desconto,";
                $sql .= "        divida.*, ";
                $sql .= "            divida.v01_numpre as k00_numpre, ";
                $sql .= "              divida.v01_numpar as k00_numpar, ";
                $sql .= "        v03_descr, ";
                $sql .= "        termodiv.*, ";
                $sql .= "        arretipo.k00_descr, ";
                $sql .= "        coalesce(arrematric.k00_matric,0) as matric, ";
                $sql .= "        coalesce(arreinscr.k00_inscr,0) as inscr, ";
                $sql .= "        coalesce(arrecontr.k00_contr,0) as contr, ";
                $sql .= "        case when a.j01_numcgm is not null ";
                $sql .= "             then (select z01_nome from cgm where z01_numcgm = a.j01_numcgm) ";
                $sql .= "        end as nomematric, ";
                $sql .= "        case when q02_numcgm is not null ";
                $sql .= "             then (select z01_nome from cgm where z01_numcgm = q02_numcgm) ";
                $sql .= "        end as nomeinscr, ";
                $sql .= "        case when b.j01_numcgm is not null ";
                $sql .= "             then (select z01_nome from cgm where z01_numcgm = b.j01_numcgm) ";
                $sql .= "        end as nomecontr ";
                $sql .= "   from termodiv  ";
                $sql .= "        inner join divida      on v01_coddiv = coddiv ";
                $sql .= "                            and v01_instit = " . db_getsession('DB_instit');
                $sql .= "        inner join arreold     on v01_numpre = arreold.k00_numpre ";
                $sql .= "                            and v01_numpar = arreold.k00_numpar ";
                $sql .= "                            and arreold.k00_valor > 0 ";
                $sql .= "        inner join arretipo  on arreold.k00_tipo = arretipo.k00_tipo ";
                $sql .= "                            and arretipo.k00_instit = " . db_getsession('DB_instit');
                $sql .= "        inner join proced      on v01_proced = v03_codigo ";
                $sql .= "        left outer join    arrematric on arrematric.k00_numpre = divida.v01_numpre ";
                $sql .= "        left outer join    iptubase a on arrematric.k00_matric = a.j01_matric ";
                $sql .= "        left outer join    arreinscr    on arreinscr.k00_numpre  =  divida.v01_numpre ";
                $sql .= "        left outer join    issbase      on arreinscr.k00_inscr = issbase.q02_inscr ";
                $sql .= "        left outer join    arrecontr    on arrecontr.k00_numpre  =  divida.v01_numpre ";
                $sql .= "        left outer join    contrib      on arrecontr.k00_contr  =  contrib.d07_contri ";
                $sql .= "        left outer join    iptubase b on b.j01_matric = contrib.d07_matric ";
                $sql .= " where parcel = {$parcelamento} order by v01_dtvenc";


                $tipo = 0;
                if (pg_result(db_query($sql), 0, 'matric') > 0) {
                    $numero = 'Matr. : ' . pg_result(db_query($sql), 0, 'matric');
                } elseif (pg_result(db_query($sql), 0, 'inscr') > 0) {
                    $numero = 'Inscr. : ' . pg_result(db_query($sql), 0, 'inscr');
                } else {
                    $numero = 'Cgm : ' . pg_result(db_query($sql), 0, 'v01_numcgm');
                }
            }
        }
    }


    $result = db_query($sql) or die("Ocorreu um erro ao efetuar o seguinte SQL:<br><br>{$sql}");

    $debitos = pg_fetch_all($result);

    $linha = 20;
    $Tv01_vlrhis = 0;
    $Tv01_valor = 0;
    $Tmulta = 0;
    $Tjuros = 0;
    $Tdesconto = 0;
    $Tv01_valor = 0;
    $Total = 0;

    $v01_vlrhis = 0;
    $v01_dtvenc = 0;
    $valor = 0;
    $multa = 0;
    $juros = 0;
    $desconto = 0;
    $vlrhis = 0;
    $k00_dtvenc = 0;
    $vlrhis = 0;
    $vlrmulta = 0;
    $vlrjuros = 0;
    $vlrdesccor = 0;
    $vlrdescjur = 0;
    $vlrdescmul = 0;

    $np = 0;
    $npa = 0;
    $primeiro = true;
    $arrTipo = array();
    $V = '';
    $arrTotHis = 0;
    $arrTotVar = 0;
    $arrTotJur = 0;
    $arrTotMul = 0;
    $arrTotDes = 0;

    $pdf->setBold(true);
    $pdf->cell(191, 4, 'ORIGEM DO PARCELAMENTO SIMULADO', 1, 1, 'C');
    $pdf->cell(15, 4, 'Mat/Insc', 1, 0, 'C');
    $pdf->cell(33, 4, 'Processo', 1, 0, 'C');
    $pdf->cell(10, 4, 'Exerc', 1, 0, 'C');
    $pdf->cell(20, 4, 'Venc', 1, 0, 'C');
    $pdf->cell(33, 4, 'Procedência', 1, 0, 'C');
    $pdf->cell(16, 4, 'Histórico', 1, 0, 'C');
    $pdf->cell(16, 4, 'Corrigido', 1, 0, 'C');
    $pdf->cell(16, 4, 'Multa', 1, 0, 'C');
    $pdf->cell(16, 4, 'Juros', 1, 0, 'C');
    $pdf->cell(16, 4, 'Total', 1, 1, 'C');
    $pdf->setBold(false);

    foreach ($debitos as $debito) {
        $desconto = 0;

        $debitoObj = (object) $debito;

        $sqlArreoldCalc = "select min(k00_dtvenc) as k00_dtvenc, ";
        $sqlArreoldCalc .= "       sum(k00_valor)  as vlrhis, ";
        $sqlArreoldCalc .= "       sum(k00_vlrcor) as vlrcor, ";
        $sqlArreoldCalc .= "       sum(k00_vlrjur) as vlrjuros, ";
        $sqlArreoldCalc .= "       sum(k00_vlrmul) as vlrmulta  ";
        $sqlArreoldCalc .= "  from arreold ";
        $sqlArreoldCalc .= "       left join arreoldcalc  on arreoldcalc.k00_numpre = arreold.k00_numpre ";
        $sqlArreoldCalc .= "                             and arreoldcalc.k00_numpar = arreold.k00_numpar ";
        $sqlArreoldCalc .= "                             and arreoldcalc.k00_receit = arreold.k00_receit ";
        $sqlArreoldCalc .= "                             and arreoldcalc.k00_hist   = arreold.k00_hist   ";
        $sqlArreoldCalc .= " where arreold.k00_numpre = {$debitoObj->k00_numpre} ";
        $sqlArreoldCalc .= "   and arreold.k00_numpar = {$debitoObj->k00_numpar} ";

        $resArreoldCalc = db_query($sqlArreoldCalc);

        if ($resArreoldCalc != false) {
            $arreoldCalc = pg_fetch_object($resArreoldCalc, 0);
        }

        $sqlTotal = "";

        if ($k03_tipo == 13) {
            $sqlTotal = "select round( coalesce(vlrdesccor,0) / (case when coalesce(vlrcor,0) = 0 then 1 else coalesce(vlrcor,0) end), 2 ) as percdesccor, ";
            $sqlTotal .= "       round( coalesce(vlrdescjur,0) / (case when coalesce(juros,0) = 0 then 1 else coalesce(juros,0) end), 2 ) as percdescjur, ";
            $sqlTotal .= "       round( coalesce(vlrdescmul,0) / (case when coalesce(multa,0) = 0 then 1 else coalesce(multa,0) end), 2 ) as percdescmul ";
            $sqlTotal .= "  from termoini where parcel = $parcelamento ";
        } elseif ($k03_tipo == 16) {
            $sqlTotal = "select round( coalesce(dv10_vlrdescjur,0) / (case when coalesce(dv10_juros,0) = 0 then 1 else coalesce(dv10_juros,0) end), 2 ) as percdescjur, ";
            $sqlTotal .= "       round( coalesce(dv10_vlrdescmul,0) / (case when coalesce(dv10_multa,0) = 0 then 1 else coalesce(dv10_multa,0) end), 2 ) as percdescmul ";
            $sqlTotal .= "  from termodiver where dv10_parcel = $parcelamento ";
        } elseif ($k03_tipo == 17) {
            $sqlTotal = "select round( coalesce(vlrdescjur,0) / (case when coalesce(juros,0) = 0 then 1 else coalesce(juros,0) end), 2 ) as percdescjur, ";
            $sqlTotal .= "       round( coalesce(vlrdescmul,0) / (case when coalesce(multa,0) = 0 then 1 else coalesce(multa,0) end), 2 ) as percdescmul ";
            $sqlTotal .= "  from termocontrib where parcel = $parcelamento ";
        }

        if ($sqlTotal <> "") {
            $rsTotalInicial = db_query($sqlTotal);

            if ($rsTotalInicial != false) {
                $intNumrows = pg_num_rows($rsTotalInicial);

                if ($rsTotalInicial != false && $intNumrows > 0) {
                    $totalInicial = pg_fetch_object($rsTotalInicial, 0);

                    $vlrdesccor = round((float)($arreoldCalc->vlrcor * $totalInicial->percdesccor), 2);
                    $vlrdescjur = round((float)($arreoldCalc->vlrmulta * $totalInicial->percdescmul), 2);
                    $vlrdescmul = round((float)($arreoldCalc->vlrjuros * $totalInicial->percdescjur), 2);
                }
            }
        }

        $v01_vlrhis = $arreoldCalc->vlrhis;
        $v01_dtvenc = $arreoldCalc->k00_dtvenc;
        $valor = $arreoldCalc->vlrhis;
        $multa = $arreoldCalc->vlrmulta;
        $juros = $arreoldCalc->vlrjuros;
        $desconto = $vlrdesccor + $vlrdescjur + $vlrdescmul;
        $vlrcor = $arreoldCalc->vlrcor;

        if ($np == $debitoObj->k00_numpre && $npa == $debitoObj->k00_numpar) {
            continue;
        } else {
            $np = $k00_numpre;
            $npa = $k00_numpar;
        }

        if (!empty($debitoObj->matric)) {
            $xnumero = 'M-' . $debitoObj->matric;
        } elseif (!empty($debitoObj->inscr)) {
            $xnumero = 'I-' . $debitoObj->inscr;
        } else {
            $xnumero = '';
        }

        $pdf->cell(15, 4, $xnumero, 1, 0, 'C');
        $pdf->cell(33, 4, $debitoObj->v70_codforo, 1, 0, 'C');
        $pdf->cell(10, 4, $debitoObj->v01_exerc, 1, 0, 'C');
        $pdf->cell(20, 4, db_formatar($debitoObj->v01_dtvenc, 'd'), 1, 0, 'C');
        $pdf->cell(33, 4, ($debitoObj->v03_descr == '' ? "Parcelamento: " . (pg_num_rows($result_reparc) > 0 ? $debitoObj->v08_parcelorigem : $parcelamento) : $debitoObj->v03_descr), 1, 0, 'L');
        $total = $vlrcor + $multa + $juros;
        if ($total > 999999) {
            $pdf->SetFontSize(5.7);
        }
        $pdf->cell(16, 4, number_format($valor, 2, ",", "."), 1, 0, 'R');
        $pdf->cell(16, 4, number_format($vlrcor, 2, ",", "."), 1, 0, 'R');
        $pdf->cell(16, 4, number_format($multa, 2, ",", "."), 1, 0, 'R');
        $pdf->cell(16, 4, number_format($juros, 2, ",", "."), 1, 0, 'R');
        $pdf->cell(16, 4, number_format($total, 2, ",", "."), 1, 1, 'R');
        $pdf->SetFontSize(8);

        $Tv01_vlrhis += $valor;
        $Tv01_valor += $vlrcor;
        $Tmulta += $multa;
        $Tjuros += $juros;
        $Tdesconto += $desconto;

        $Total += $vlrcor + $multa + $juros;

        if (array_key_exists($k00_descr, $arrTipo)) {
            $arrTipo[$k00_descr]['vlrhist'] += ((float)$valor);
            $arrTipo[$k00_descr]['vlrcor'] += ((float)$vlrcor);
            $arrTipo[$k00_descr]['vlrmulta'] += ((float)$multa);
            $arrTipo[$k00_descr]['vlrjuros'] += ((float)$juros);
            $arrTipo[$k00_descr]['vlrdesc'] += ((float)$desconto);
            $arrTipo[$k00_descr]['vlrtotal'] += ((float)$vlrcor + (float)$multa + (float)$juros - (float)$desconto);
        } else {
            $arrTipo[$k00_descr]['vlrhist'] = ((float)$valor);
            $arrTipo[$k00_descr]['vlrcor'] = ((float)$vlrcor);
            $arrTipo[$k00_descr]['vlrmulta'] = ((float)$multa);
            $arrTipo[$k00_descr]['vlrjuros'] = ((float)$juros);
            $arrTipo[$k00_descr]['vlrdesc'] = ((float)$desconto);
            $arrTipo[$k00_descr]['vlrtotal'] = ((float)$vlrcor + (float)$multa + (float)$juros - (float)$desconto);
        }
    }

    $pdf->setBold(true);
    $pdf->cell(15, 4, 'Total', 1, 0, "C", 0);
    $pdf->cell(96, 4, '', 1, 0, "c", 0);
    if ($Total > 999999) {
        $pdf->SetFontSize(5.7);
    }
    $pdf->cell(16, 4, number_format($Tv01_vlrhis, 2, ",", "."), 1, 0, "R", 0);
    $pdf->cell(16, 4, number_format($Tv01_valor, 2, ",", "."), 1, 0, "R", 0);
    $pdf->cell(16, 4, number_format($Tmulta, 2, ",", "."), 1, 0, "R", 0);
    $pdf->cell(16, 4, number_format($Tjuros, 2, ",", "."), 1, 0, "R", 0);
    $pdf->cell(16, 4, number_format($Total, 2, ",", "."), 1, 1, "R", 0);
    $pdf->SetFontSize(8);
    $pdf->setBold(false);

    $pdf->ln(8);

    return $Total;
}

function removeVirgulas ($valor) {
    return str_replace(',', '', $valor);
}

function calculaPercentual($percentage, $total) {
    return (int)$percentage ? (number_format($percentage, 2) / 100) * round((float)$total, 2) : 0;
}

function formataValorParaTabela($valor) {
    return db_formatar(formataValor($valor), 'f');
}

function formataValorParaCalculo($valor) {
    return formataValor($valor);
}

function formataValor($valor) {
    $sValor = str_replace(',', '.', strval($valor));
    $sValor = preg_replace('/\.(?=.*\.)/', '', $sValor);

    return $sValor;
}

function getCodigoForoPorSequencial($sequencialProcesso) {
    $oDaoProcessoForo = new cl_processoforo;
    $sSqlBuscaProcesso = $oDaoProcessoForo->sql_query_file($sequencialProcesso);
    $rsBuscaProcesso = db_query($sSqlBuscaProcesso);

    if (pg_num_rows($rsBuscaProcesso) == 0) {
        return null;
    }

    $processosEncontrados = db_utils::getCollectionByRecord($rsBuscaProcesso);

    return $processosEncontrados[0]->v70_codforo;
}
