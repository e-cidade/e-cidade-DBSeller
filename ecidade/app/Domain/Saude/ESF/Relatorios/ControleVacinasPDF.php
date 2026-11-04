<?php

namespace App\Domain\Saude\ESF\Relatorios;

use ECidade\Enum\Common\FaixaEtariaEnum;
use ECidade\Enum\Saude\ESF\SituacaoPacienteVacinacaoEnum;
use ECidade\Pdf\Pdf;

class ControleVacinasPDF extends Pdf
{
    /**
     * @var array
     */
    private $dados;

    /**
     * @var int
     */
    private $totalVacinas = 0;

    /**
     * @param  array  $dados
     */
    public function __construct(array $dados, \stdClass $request)
    {
        parent::__construct();
        $this->dados = $dados;

        $this->addTitulo('Relatório de Controle de Vacinas Aplicadas');

        $periodoInicial = db_formatar($request->periodoInicial, 'd');
        $periodoFinal = db_formatar($request->periodoFinal, 'd');

        $situacao = empty($request->situacao) ?
            'TODAS' :
            (new SituacaoPacienteVacinacaoEnum((int)$request->situacao))->name();

        $faixaEtaria = empty($request->faixaEtaria) ?
            'TODAS' :
            (new FaixaEtariaEnum((int)$request->faixaEtaria))->name();

        $this->addTitulo("Período: {$periodoInicial} até {$periodoFinal}");
        $this->addTitulo("Situação/Condição: {$situacao}");
        $this->addTitulo("Faixa Etária: {$faixaEtaria}");
    }

    public function emitir()
    {
        $this->initPdf();
        $this->addPage();

        foreach ($this->dados as $unidade) {
            if ($this->getAvailHeight() < 31) {
                $this->addPage();
            }

            $this->setFont('Arial', 'B', 8);
            $this->cell(195, 5, $unidade->descricao, 0, 1);

            $this->montaCabecalho();
            $this->setFont('Arial', '', 7);
            foreach ($unidade->vacinas as $vacina) {
                $this->montaLinha($vacina, $linha);
            }

            $this->cell(1, 4, '', 0, 1);
        }

        if (count($this->dados) > 1) {
            $this->setFont('Arial', 'B', 7);
            $this->cell(150, 4, 'TOTAL GERAL:', 0, 0, 'R');
            $this->setFont('Arial', '', 7);
            $this->cell(45, 4, "{$this->totalVacinas} vacinas aplicadas", 0, 1, 'L');
        }

        return $this->imprimir();
    }

    private function initPdf()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->setMargins(8, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFillColor(235);
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader(true);
    }

    private function montaCabecalho()
    {
        $this->setFillColor(210);
        $this->setFont('Arial', 'B', 8);
        $this->cell(80, 5, 'Imunobiológico', 1, 0, 'C', 1);
        $this->cell(60, 5, 'Estratégia', 1, 0, 'C', 1);
        $this->cell(34, 5, 'Dose', 1, 0, 'C', 1);
        $this->cell(20, 5, 'Quantidade', 1, 1, 'C', 1);
    }

    private function montaLinha($vacina, &$linha)
    {
        $linha = 0;
        foreach ($vacina->estrategias as $estrategia) {
            foreach ($estrategia->doses as $dose) {
                if ($this->getAvailHeight() < 21) {
                    $this->addPage();
                    $this->montaCabecalho();
                    $this->setFont('Arial', '', 7);
                    $linha = 0;
                }

                $linha++;
                $color = !!($linha % 2);

                $numeroLinhasOcupadas = 1;

                $vacinaDescricaoPossuiMaisLinhas = false;
                $estrategiaDescricaoPossuiMaisLinhas = false;
                $doseDescricaoPossuiMaisLinhas = false;
                $doseQuantidadePossuiMaisLinhas = false;

                /**
                 * Verifica o número de linhas utilizado por cada célula
                 */
                $nmrLinhasVacinaDescricao = $this->nbLines(80, $vacina->descricao);
                $nmrLinhasEstrategiaDescricao = $this->nbLines(60, $estrategia->descricao);
                $nmrLinhasDoseDescricao = $this->nbLines(34, $dose->descricao);
                $nmrLinhasDoseQuantidade = $this->nbLines(20, $dose->quantidade);

                /**
                 * As seguintes estruturas IF verificam qual célula terá o maior número de linhas
                 */
                if ($nmrLinhasVacinaDescricao > $numeroLinhasOcupadas) {
                    $numeroLinhasOcupadas = $nmrLinhasVacinaDescricao;
                    $vacinaDescricaoPossuiMaisLinhas = true;
                }

                if ($nmrLinhasEstrategiaDescricao > $numeroLinhasOcupadas) {
                    $numeroLinhasOcupadas = $nmrLinhasEstrategiaDescricao;
                    $estrategiaDescricaoPossuiMaisLinhas = true;
                }

                if ($nmrLinhasDoseDescricao > $numeroLinhasOcupadas) {
                    $numeroLinhasOcupadas = $nmrLinhasDoseDescricao;
                    $doseDescricaoPossuiMaisLinhas = true;
                }

                if ($nmrLinhasDoseQuantidade > $numeroLinhasOcupadas) {
                    $numeroLinhasOcupadas = $nmrLinhasDoseQuantidade;
                    $doseQuantidadePossuiMaisLinhas = true;
                }

                /**
                 * Altura máxima que será obtida nessa iteração levando em conta que todas as células envolvidas
                 * tenham a mesma altura inicial.
                 */
                $linhaAlturaMaxima = $numeroLinhasOcupadas * 5;

                $x1 = $this->getX();
                $y1 = $this->getY();

                /**
                 * Os ternários a seguir irão levar a em conta o número de linhas utilizadas pela célula
                 * E se a mesma é q que possui o maior número de linhas nesta iteração.
                 */
                $this->multiCell(
                    80,
                    ($vacinaDescricaoPossuiMaisLinhas ?
                        5 :
                        ($nmrLinhasVacinaDescricao > 1 ?
                            $linhaAlturaMaxima / $nmrLinhasVacinaDescricao :
                            5 * $numeroLinhasOcupadas
                        )
                    ),
                    $vacina->descricao,
                    0,
                    'C',
                    $color
                );
                $this->setXY($x1 + 80, $y1);

                $x1 = $this->getX();
                $y1 = $this->getY();
                $this->multiCell(
                    60,
                    ($estrategiaDescricaoPossuiMaisLinhas ?
                        5 :
                        ($nmrLinhasEstrategiaDescricao > 1 ?
                            $linhaAlturaMaxima / $nmrLinhasEstrategiaDescricao :
                            5 * $numeroLinhasOcupadas
                        )
                    ),
                    $estrategia->descricao,
                    0,
                    'C',
                    $color
                );
                $this->setXY($x1 + 60, $y1);

                $x1 = $this->getX();
                $y1 = $this->getY();
                $this->multiCell(
                    34,
                    ($doseDescricaoPossuiMaisLinhas ?
                        5 :
                        ($nmrLinhasDoseDescricao > 1 ?
                            $linhaAlturaMaxima / $nmrLinhasDoseDescricao :
                            5 * $numeroLinhasOcupadas
                        )
                    ),
                    $dose->descricao,
                    0,
                    'C',
                    $color
                );
                $this->setXY($x1 + 34, $y1);

                $x1 = $this->getX();
                $y1 = $this->getY();
                $this->multiCell(
                    20,
                    ($doseQuantidadePossuiMaisLinhas ?
                        5 :
                        ($nmrLinhasDoseQuantidade > 1 ?
                            $linhaAlturaMaxima / $nmrLinhasDoseQuantidade :
                            5 * $numeroLinhasOcupadas
                        )
                    ),
                    $dose->quantidade,
                    0,
                    'C',
                    $color
                );
            }
        }

        $this->cell(1, 1, '', 0, 1);
        $this->cell(150, 4, 'Total por Estratégia:', 0, 0, 'R');
        $x = $this->GetX();
        $total = 0;
        foreach ($vacina->estrategias as $estrategia) {
            $total += $estrategia->total;
            $this->SetX($x);
            $this->multiCell(45, 4, "{$estrategia->descricao} - {$estrategia->total}", 0, 'L');
        }

        $this->cell(1, 1, '', 0, 1);
        $this->setFont('Arial', 'B', 7);
        $this->cell(150, 4, 'TOTAL:', 0, 0, 'R');
        $this->setFont('Arial', '', 7);
        $this->cell(45, 4, "{$total} vacinas aplicadas", 0, 1, 'L');
        $this->cell(1, 3, '', 0, 1);

        $this->totalVacinas += $total;
    }

    private function imprimir()
    {
        $fileName = 'tmp/controle_vacinas_aplicadas'.time().'.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Controle de Vacinas Aplicadas",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH.$fileName
        ];
    }
}
