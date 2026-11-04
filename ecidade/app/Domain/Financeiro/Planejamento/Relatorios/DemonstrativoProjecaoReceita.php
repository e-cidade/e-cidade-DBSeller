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

namespace App\Domain\Financeiro\Planejamento\Relatorios;

use stdClass;

class DemonstrativoProjecaoReceita extends Pdf
{

    protected $wLinha = 279;
    /**
     * @var int
     */
    protected $wDadosReceita = 96;
    /**
     * @var float|int
     */
    protected $wValorArrecadados;
    /**
     * @var float|int
     */
    protected $wValoresProjetados;

    protected $titulo = 'Demonstrativo das Projeções da Receita';

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
    }

    public function emitir()
    {
        $this->calculaColunas();
        $this->headers($this->titulo);
        $this->headerOrigemEmentario();
        $this->capa($this->titulo);
        $this->imprimeDados();

        $filename = sprintf('tmp/projecao-receita-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    /**
     * @param $titulo
     */
    protected function capa($titulo)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 30);
        $this->SetY(105);
        $this->Cell($this->wLinha, 12, $titulo, 0, 1, 'R');
        $this->Cell($this->wLinha, 12, $this->getTituloPlano(), 0, 1, 'R');
        $this->Cell($this->wLinha, 12, $this->periodo, 0, 1, 'R');
    }

    protected function imprimeDados()
    {
        $exercicios = $this->dados['planejamento']['exercicios'];
        $exerciciosAnteriores = $this->dados['exerciciosAnteriores'];
        $this->cabecalhoReceita();
        foreach ($this->dados['dados'] as $receita) {
            if ($this->getAvailHeight() < 6) {
                $this->cabecalhoReceita();
            }
            $this->imprimeLinha($receita, $exerciciosAnteriores, $exercicios);
        }

        $this->imprimirTotalizador();
    }

    protected function cabecalhoReceita()
    {
        $exercicioProjecao = $this->dados['planejamento']['pl2_ano_inicial'] - 1;
        $exercicios = $this->dados['planejamento']['exercicios'];
        $exerciciosAnteriores = $this->dados['exerciciosAnteriores'];

        $totalColunaValores = count($exercicios) + count($exerciciosAnteriores) + 1;

        $this->wValor = ($this->wLinha - $this->wDadosReceita) / $totalColunaValores;

        $this->wValorArrecadados = $this->wValor * count($exerciciosAnteriores);
        $this->wValoresProjetados = $this->wValor * count($exercicios);

        $this->AddPage();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($this->wDadosReceita, 5, 'Dados da Receita', 1, 0, 'C');
        $this->Cell($this->wValorArrecadados, 5, 'Valores Arrecadados', 1, 0, 'C');
        $this->cellAdapt(8, $this->wValor, 5, "Previsão Atualizada", 'LRT', 0, 'C');
        $this->Cell($this->wValoresProjetados, 5, "Valores Projetados", 1, 1, 'C');

        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, 'Estrutural', 1, 0, 'C');
        $this->Cell(61, 5, 'Descrição', 1, 0, 'C');
        $this->Cell(15, 5, 'Recurso', 1, 0, 'C');

        foreach ($this->dados['exerciciosAnteriores'] as $exercicio) {
            $this->Cell($this->wValor, 5, $exercicio, 1, 0, 'C');
        }

        $this->Cell($this->wValor, 5, $exercicioProjecao, 'RLB', 0, 'C');

        foreach ($exercicios as $exercicio) {
            $this->Cell($this->wValor, 5, $exercicio, 1, 0, 'C');
        }

        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    protected function calculaColunas()
    {
        $exercicios = $this->dados['planejamento']['exercicios'];
        $exerciciosAnteriores = $this->dados['exerciciosAnteriores'];

        $totalColunaValores = count($exercicios) + count($exerciciosAnteriores) + 1;

        $this->wValor = ($this->wLinha - $this->wDadosReceita) / $totalColunaValores;
        $this->wValorArrecadados = $this->wValor * count($exerciciosAnteriores);
        $this->wValoresProjetados = $this->wValor * count($exercicios);
    }

    protected function imprimirTotalizador()
    {
        $w = $this->wLinha - $this->wValoresProjetados;

        if ($this->getAvailHeight() < 6) {
            $this->cabecalhoReceita();
        }
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($w, 5, 'Total da projeção', 1, 0, 'R');
        foreach ($this->dados['totalizador'] as $valor) {
            $this->imprimeValor($valor);
        }
        $this->ln();
        $this->SetFont('Arial', '', 7);
    }

    protected function imprimeValor($valor, $h = 5)
    {
        $this->Cell($this->wValor, $h, formataValorMonetario($valor), 1, 0, 'R');
    }

    /**
     * @param $receita
     * @param $exerciciosAnteriores
     * @param $exercicios
     */
    protected function imprimeLinha(stdClass $receita, $exerciciosAnteriores, $exercicios)
    {
        $this->SetFont('Arial', '', 6);
        if ($receita->sintetico) {
            $this->SetFont('Arial', 'B', 6);
        }
        $this->cellAdapt(6, 20, 5, $receita->fonte, 1, 0, 'C');

        $content = $receita->descricao;
        if ($this->getFonteSizeByWidthCell(61, 6, $content) < 5) {
            $content = substr($content, 0, 63) . '...';
        }
        $this->cellAdapt(6, 61, 5, $content, 1, 0, 'L');
        $this->Cell(15, 5, "{$receita->recurso} - {$receita->complemento}", 1, 0, 'C');

        foreach ($exerciciosAnteriores as $exercicio) {
            $this->imprimeValor($receita->{"arrecadado_{$exercicio}"});
        }
        $this->imprimeValor($receita->valor_base);
        foreach ($exercicios as $exercicio) {
            $this->imprimeValor($receita->{"valor_{$exercicio}"});
        }

        $this->ln();
    }
}
