<?php
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/JSON.php');
require_once modification('fpdf151/pdf.php');

use ECidade\RecursosHumanos\ESocial\Factory\Mapeador as MapeadorFactory;
use ECidade\Integracao\Sped\Common\Configuracao\ConfiguracaoFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\FormularioRepository;

$oJson = new services_json();
$oParam = JSON::create()->parse(str_replace('\\', "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMessage = '';

try {
    db_inicio_transacao();
    ini_set('memory_limit', '-1');

    switch ($oParam->exec) {
        case "getPreenchimentoRubrica":

            break;
        case "imprimirRelatorio":
            $filtros = $oParam->filtros;
            $relatorioFactory = MapeadorFactory::get($filtros->tipoFormulario);
            $formulario = ConfiguracaoFactory::getFormularioDoTipoNaVersaoAtual(Tipo::ESOCIAL, $filtros->tipoFormulario);
            $filtroRubricas = null;
            if (!empty($filtros->rubricas)  ) {
                $filtroRubricas = $filtros->rubricas;
            }
            if (empty($filtros->ano)) {
                throw new BusinessException("Ano da Competência não informado.");
            }
            if (empty($filtros->mes)) {
                throw new BusinessException("Mês da Competência não informado.");
            }

            $instituicao = \InstituicaoRepository::getInstituicaoSessao();

            $preenchimentosFormulario = FormularioRepository::getRespostasFormularioById(
                $formulario->formulario,
                $relatorioFactory,
                $instituicao->getCodigo(),
                $filtroRubricas
            );
            $relatorioFactory->setDadosFormulario($preenchimentosFormulario);
            unset($preenchimentosFormulario);

            $dados = [];
            $dados['titulos'] = $relatorioFactory->getColunas();
            $dados['registros'] = [];
            //Buscamos as informacoes do eCidade
            $relatorioFactory->processarDadosEcidade($filtros->ano, $filtros->mes);
            // Montamos a linha do Relatorio
            $identificador = $relatorioFactory->getIdentificador();

            // largura max = 192
            $colunas = [
                'rubrica' => 42,
                'inss' => 50,
                'irrf' => 50,
                'fgts' => 50
            ];
            foreach ($relatorioFactory->getDadosEcidade() as $dadoEcidade){
                $encontrou = false;
                foreach ($relatorioFactory->getDadosFormulario()->respostas as $resposta) {
                    // Adicionamos do array de dados os dados agrupados do esocial com o
                    // ecidade baseado nos dados do ecidade
                    if ($dadoEcidade[$identificador] == $resposta->{$identificador}) {
                        $dado = [];
                        foreach ($dadoEcidade as $ecidade => $valor) {
                            $dado['ecidade'][$ecidade] = $valor;
                            if (!empty($resposta->{$ecidade})) {
                                $dado['formulario'][$ecidade] = $resposta->{$ecidade};
                            }
                        }
                        $encontrou = true;
                        $dados['registros'][] = $dado;
                    } else {
                        continue;
                    }
                }
                if (!$encontrou) {
                    $dado = [];
                    foreach ($dadoEcidade as $ecidade => $valor) {
                        $dado['ecidade'][$ecidade] = $valor;
                        $dado['formulario'][$ecidade] = "Não foi gerada a Carga da Rubrica";
                    }
                    $dados['registros'][] = $dado;
                }
            }
            $caminhoPdf = "tmp/relatorio_esocial_" .  date('dmYhis') . ".pdf";

            $head1 = "\nCompetência: {$filtros->mes}/{$filtros->ano}" ;
            $head2 = "\nQuantidade de registros: " . (count($dados['registros']));

            $pdf = new PDF();
            $pdf->Open();
            $pdf->AliasNbPages();
            $pdf->addpage();

            $pdf->SetAutoPageBreak(true, 15);
            $esocial = 'eSocial';
            $ecidade = 'e-cidade';
            $altura = 5;
            geraCabecalho($pdf, $colunas);
            $cor = 1;
            foreach ($dados['registros'] as $dado) {
                if ($pdf->GetY() > 250) {
                    $pdf->addpage();
                    geraCabecalho($pdf, $colunas);
                }
                $pdf->setfillcolor(235);
                $pdf->setfont('arial', 'B', 6);
                $texto = $dado['ecidade']['codrubr'];
                if (strlen($texto) > 35) {
                    $texto = substr($texto, 0, 35) . "...";
                }
                $pdf->cell($colunas['rubrica'], $altura, substr($dado['ecidade']['codrubr'] . ' - ' . $dado['ecidade']['dscrubr'], 0, 35), 1, 0,'L', $cor);
                $pdf->setfont('arial', '', 6);
                $texto = $dado['formulario']['codinccp'];
                if (strlen($texto) > 45) {
                    $texto = substr($texto, 0, 45) . "...";
                }
                $pdf->cell($colunas['inss'], $altura, $texto, 1, 0,'L', $cor);
                $texto = $dado['formulario']['codincirrf'];
                if (strlen($texto) > 45) {
                    $texto = substr($texto, 0, 45) . "...";
                }
                $pdf->cell($colunas['irrf'], $altura, $texto, 1, 0,'L', $cor);
                $texto = $dado['formulario']['codincfgts'];
                if (strlen($texto) > 45) {
                    $texto = substr($texto, 0, 45) . "...";
                }
                $pdf->cell($colunas['fgts'], $altura, $texto, 1, 0,'L', $cor);
                $pdf->ln();
                $pdf->cell($colunas['rubrica'], $altura, "Dados e-Cidadde", 1, 0,'L', $cor);
                $imprime =  'Não';
                if (!empty($dado['ecidade']['codinccp'])) {
                    $imprime = 'Sim';
                }
                $pdf->cell($colunas['inss'], $altura, $imprime, 1, 0,'C', $cor);
                $imprime =  'Não';
                if (!empty($dado['ecidade']['codincirrf'])) {
                    $imprime = 'Sim';
                }
                $pdf->cell($colunas['irrf'], $altura, $imprime, 1, 0,'C', $cor);
                $imprime =  'Não';
                if (!empty($dado['ecidade']['codincfgts'])) {
                    $imprime = 'Sim';
                }
                $pdf->cell($colunas['fgts'], $altura, $imprime, 1, 0,'C', $cor);
                if ($cor) {
                    $cor = 0;
                } else {
                    $cor = 1;
                }
                $pdf->ln();
            }
            $pdf->Output($caminhoPdf, false, true);

            if (!file_exists($caminhoPdf)) {
                throw new Exception("Erro ao gerar o relatório.\nContate o suporte.");
            }

            $oRetorno->nomeArquivo = $caminhoPdf;
            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMessage = $eErro->getMessage();
}


function geraCabecalho(&$pdf, $colunas) {
    $pdf->setfillcolor(235);
    $pdf->setfont('arial', 'B', 8);
    $pdf->cell($colunas['rubrica'], 5, "Código Rubrica", 1, 0, 'C', 1);
    $pdf->cell($colunas['inss'], 5, "INSS", 1, 0, 'C', 1);
    $pdf->cell($colunas['irrf'], 5, "IRRF", 1, 0, 'C', 1);
    $pdf->cell($colunas['fgts'], 5, "FGTS", 1, 1, 'C', 1);
    $pdf->setfont('arial', '', 6);
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo JSON::create()->stringify($oRetorno);
