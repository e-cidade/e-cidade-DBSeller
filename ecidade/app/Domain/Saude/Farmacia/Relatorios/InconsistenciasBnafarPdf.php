<?php

namespace App\Domain\Saude\Farmacia\Relatorios;

use ECidade\Pdf\Pdf;
use Illuminate\Support\Collection;

class InconsistenciasBnafarPdf extends Pdf
{
    /**
     * @var Collection
     */
    private $dados;

    /**
     * @var string
     */
    private $titulo;

    public function __construct(Collection $dados, $titulo, array $periodo)
    {
        $this->dados = $dados;
        $this->titulo = $titulo;

        parent::__construct();
        $this->addTitulo($this->titulo);
        $this->addTitulo('');
        $this->addTitulo("Competência: {$periodo[0]->format('m/Y')}");
        $this->addTitulo("Período: {$periodo[0]->format('d')} até {$periodo[1]->format('d')}");
    }

    public function imprimir()
    {
        $this->initPdf();
        $this->addPage();
        $this->imprimirCabelho();
        $linhaImpressa = 0;
        foreach ($this->dados as $incosistencia) {
            $linhaImpressa++;
            $this->imprimirLinha($incosistencia, $linhaImpressa);
        }

        return $this->emitir();
    }

    protected function imprimirLinha($inconsistencia, &$linhaImpressa)
    {
        $this->setFont('ARIAL', '', 7);
        $erros = implode("\n", $inconsistencia->erros);
        $alturaErros = $this->nbLines(100, $erros) * 4;
        if ($this->getAvailableHeight() < $alturaErros) {
            $this->addPage();
            $this->imprimirCabelho();
            $this->setFont('ARIAL', '', 7);
            $linhaImpressa = 1;
        }

        $cor = !($linhaImpressa % 2);
        $this->cell(20, $alturaErros, $inconsistencia->codigo_origem, 0, 0, 'C', $cor);
        $this->cell(72, $alturaErros, $inconsistencia->m60_descr, 0, 0, 'L', $cor);
        $this->multiCell(100, 4, $erros, 0, 'L', $cor);
    }

    final protected function imprimirCabelho()
    {
        $this->setFont('ARIAL', 'B', 8);
        $this->cell(20, 4, 'Lançamento', 1, 0, 'C', 1);
        $this->cell(72, 4, 'Medicamento', 1, 0, 'C', 1);
        $this->cell(100, 4, 'Erros', 1, 1, 'C', 1);
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(8, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFillColor(235);
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader();
        $this->setExibeBrasao(true);
    }

    private function emitir()
    {
        $path = 'tmp/' . str_replace(' ', '-', strtolower($this->titulo)) . time() . '.pdf';
        $path = \DBString::removerAcentuacao($path);
        $this->output('F', $path);

        return [
            'name' => $this->titulo,
            'path' => $path,
            'pathExterno' => ECIDADE_REQUEST_PATH . $path
        ];
    }
}
