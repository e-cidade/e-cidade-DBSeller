<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\RN\Layouts\SIAI\RREO\AnexoQuatroLayout;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoQuatroFactory;
use stdClass;

class AnexoQuatroService extends AnexosService
{
    const SIGLA = "A04";
    
    private $layout;
    
    public function __construct($exercicio, $filtros)
    {
        $this->exercicio = $exercicio;
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        
        $relatorio = AnexoQuatroFactory::getService($exercicio, $filtros);
        $emissao = $relatorio->emitir();
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->periodo = $relatorio->getPeriodo();
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $this->setArquivoXlsCarregado($reader->load($emissao["xls"]));
        
        $this->layout = new AnexoQuatroLayout();
        
        $this->nomeArquivo = self::SIGLA."_{$exercicio}".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setFilePath("tmp/{$this->nomeArquivo}.TXT");
    }
    
    public function makeHeader()
    {
        $header = new stdClass();
        $header->tipoRegistro = "0";
        $header->nomeArquivo = $this->nomeArquivo;
        $header->bimReferencia = explode("_", $this->nomeArquivo)[1];
        $header->tipoArquivo = "O";
        $header->dataGeracaoArq = date("d/m/Y");
        $header->horaGeracaoArq = date("H:i:s");
        $header->codigoOrgao = str_pad($this->codigoOrgao, 4, " ", STR_PAD_LEFT);
        $header->nomeOrgao = str_pad(substr($this->nomeOrgao, 0, 100), 100, " ");
        $header->brancos = str_repeat(" ", 268);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $linhasDetalhesProcessadas = [];
        
        foreach ($this->layout->getLinhas() as $linhaLayout) {
            foreach ($this->linhasProcessadas as $linhaRelatorio) {
                if ($linhaLayout->linha != $linhaRelatorio->linha
                    || in_array($linhaRelatorio->linha, $linhasDetalhesProcessadas)) {
                    continue;
                }
                
                $detalhe = $this->makeDetalhe($linhaLayout);
                $this->addDetalhe($detalhe);
                $linhasDetalhesProcessadas[] = $linhaRelatorio->linha;
            }
        }
    }
    
    private function makeDetalhe($linhaLayout)
    {
        $detalhe = new stdClass();
        $detalhe->tipoRegistro = $linhaLayout->tipoRegistro;
        $detalhe->brancos1 = " ";
        
        switch ($linhaLayout->tipoRegistro) {
            case 1:
                $detalhe->tipoQuadro = $linhaLayout->tipoQuadro;
                $detalhe->brancos2 = " ";
                $detalhe->campo = str_pad($linhaLayout->campo, 3, " ", STR_PAD_RIGHT);
                $detalhe->brancos3 = str_repeat(" ", 4);
                $detalhe->atualizada = $this->getValorCelulaArquivoXls($linhaLayout->cellVlrAtualizado);
                $detalhe->ateBimestre = $this->getValorCelulaArquivoXls($linhaLayout->cellVlrAteBimestre);
                $detalhe->brancos4 = str_repeat(" ", 369);
                break;
            case 2:
                $detalhe->tipoQuadro = $linhaLayout->tipoQuadro;
                $detalhe->brancos2 = " ";
                $detalhe->campo = str_pad($linhaLayout->campo, 3, " ", STR_PAD_RIGHT);
                $detalhe->brancos3 = str_repeat(" ", 4);
                $detalhe->dotacaoAtualizada = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrDotacaoAtualizada
                );
                $detalhe->despEmpAteBimestre = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrDespesasEmpenhadasAteBimestre
                );
                $detalhe->despLiqAteBimestre = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrDespesasLiquidadadasAteBimestre
                );
                $detalhe->despPagasAteBimestre = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrDespesasPagasAteBimestre
                );
                $detalhe->inscRestosNaoProcessados = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrInscritosRestosNaoProcessados
                );
                $detalhe->brancos4 = str_repeat(" ", 327);
                break;
            case 3:
                $detalhe->campo = str_pad($linhaLayout->campo, 3, " ", STR_PAD_RIGHT);
                $detalhe->brancos2 = str_repeat(" ", 6);
                $detalhe->valorPrevisaoOrcamentaria = $this->getValorCelulaArquivoXls(
                    $linhaLayout->cellVlrPrevisaoOrcamentaria
                );
                $detalhe->brancos3 = str_repeat(" ", 383);
                break;
            case 4:
                $detalhe->tipoQuadro = $linhaLayout->tipoQuadro;
                $detalhe->brancos2 = " ";
                $detalhe->campo = str_pad($linhaLayout->campo, 3, " ", STR_PAD_RIGHT);
                $detalhe->brancos3 = str_repeat(" ", 4);
                $detalhe->aporteRealizado = $this->getValorCelulaArquivoXls($linhaLayout->cellVlrAporteRealizado);
                $detalhe->brancos4 = str_repeat(" ", 383);
                break;
            case 5:
                $detalhe->tipoQuadro = $linhaLayout->tipoQuadro;
                $detalhe->brancos2 = " ";
                $detalhe->campo = str_pad($linhaLayout->campo, 3, " ", STR_PAD_RIGHT);
                $detalhe->brancos3 = str_repeat(" ", 4);
                $detalhe->saldoExercicio = $this->getValorCelulaArquivoXls($linhaLayout->cellVlrSaldoExercicio);
                $detalhe->brancos4 = str_repeat(" ", 383);
                break;
        }
        
        $detalhe->numRegistroLido = $this->getQtdLinhas();
        
        return $detalhe;
    }
    
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 407);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
