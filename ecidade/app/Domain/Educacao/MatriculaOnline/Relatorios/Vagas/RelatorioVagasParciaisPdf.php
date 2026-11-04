<?php

namespace App\Domain\Educacao\MatriculaOnline\Relatorios\Vagas;

use ECidade\Pdf\Pdf;

class RelatorioVagasParciaisPdf extends Pdf
{
    /**
     * @var array
     */
    private $dados;

    /**
     * @var integer
     */
    private $alturaNomeEscola;

    /**
     * @var array
     */
    private $escola;

    /**
     * @var array
     */
    private $mapaTurnos = [];

    /**
     * @var integer
     */
    private $alturaFinalDados;

    /**
     * @param array $dados
     */
    public function __construct(array $dados)
    {
        parent::__construct('P');
        $this->dados = $dados;
        $this->addTitulo('RELATÓRIO DE PREVISÃO DE VAGAS', 1);
        $this->addTitulo('', 2);
        $this->setAutoPageBreak(true, 30);
    }

    public function emitir()
    {
        $this->initPdf();
        if (count($this->dados['dadosEscolas']) > 0) {
            foreach ($this->dados['dadosEscolas'] as $escola) {
                $this->escola = $escola;
                $this->verificaAlturaRestante();
                $this->getLegendaTurnos($this->dados['todosTurnos']);
                $this->imprimeRelatorio($this->escola);
            }
        } else {
            $this->verificaAlturaRestante();
            $this->setY(50);
            $this->setFont('Arial', 'B', 10);
            $this->setTextColor(61, 100, 128);
            $this->setDrawColor(61, 100, 128);
            $msg = 'NÃO EXISTEM TURNOS PARCIAIS PARA ESTA ETAPA NAS ESCOLAS SELECIONADAS!';
            $this->cell(190, 20, $msg, 'B', 0, 'C', 0);
            $this->setTextColor(0, 0, 0);
            $this->setDrawColor(0, 0, 0);
            $this->setFont('Arial', 'B', 9);
        }

        $fileName = 'tmp/previsao-vagas' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Vagas PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(10, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader(true, 1);
        $this->setExibeBrasao(true);
    }

    private function verificaAlturaRestante()
    {
        $alturaRestante = $this->getY();

        if (is_null($alturaRestante) || $alturaRestante + 40 >= $this->getH()) {
            $this->setDrawColor(0, 0, 0);
            $this->setTextColor(0, 0, 0);
            $this->addPage();
        }
    }

    private function imprimeRelatorio($escola, $isBreakPage = false)
    {
        $this->imprimeNomeEscola($escola);
        $this->imprimeEnsinos($escola->ensinos, $isBreakPage);
    }

    private function imprimeNomeEscola($escola)
    {
        $this->setFont('Arial', 'B', $this->calculaTamanhoFonte(70, $escola->nome, 9));
        $this->setY($this->getY() + 5);
        $this->alturaNomeEscola = $this->getY();
        $this->imprimeRetanguloEscola();
        $this->setTextColor(250, 250, 250);
        $this->setX(15);
        $this->cell(150, 5, $escola->nome, 0, 1, 'L', 0);
    }

    private function imprimeEnsinos($ensinos, $isBreakPage = false)
    {
        foreach ($ensinos as $ensino) {
            $this->setX(15);
            $this->setFont('Arial', '', $this->calculaTamanhoFonte(70, $ensino->nome, 8));
            $this->cell(150, 5, $ensino->nome, 0, 1, 'L', 0);
            $this->imprimeCabecalhoTabela();
            if (!$isBreakPage) {
                $this->imprimeDados($ensino->etapas);
            }
        }
    }

    private function imprimeRetanguloEscola()
    {
        $this->setDrawColor(61, 100, 128);
        $this->setFillColor(61, 100, 128);
        $this->rectBorderRadius(10, $this->alturaNomeEscola, 100, 10, 5, 5, 0, 0, 'FD');
    }

    private function imprimeCabecalhoTabela()
    {
        $this->setTextColor(61, 100, 128);
        $this->setFillColor(152, 193, 217);
        $this->setFont('Arial', 'B', 8);
        $this->cell(50, 5, 'Etapa', 'LB', 0, 'C', 1);
        $this->cell(50, 5, 'Turno', 'LB', 0, 'C', 1);
        $this->cell(45, 5, 'Turno Parcial', 'LTB', 0, 'C', 1);
        $this->cell(45, 5, 'Vagas Disponíveis', 1, 1, 'C', 1);
    }

    private function imprimeDados($etapas)
    {
        foreach ($etapas as $etapa) {
            $totalLinhasTurno =  $this->verificaTotalLinhasTurnos($etapa);
            if ($this->getY() + $totalLinhasTurno * 5 >= $this->getH() - 10) {
                $this->setDrawColor(0, 0, 0);
                $this->setTextColor(0, 0, 0);
                $this->addPage();
                $this->imprimeRelatorio($this->escola, true);
            }
            // Imprime Etapa
            $this->setFont('Arial', '', $this->calculaTamanhoFonte(30, $etapa->etapa, 8));
            $this->cell(50, 5 * $totalLinhasTurno, $etapa->etapa, 1, 0, 'C', 0);

            foreach ($etapa->turnos as $turno) {
                $this->setX(60);
                // Imprime Turno
                $this->setFont('Arial', '', $this->calculaTamanhoFonte(30, $turno->turno, 8));
                $turnosParciais = reset($turno->vagas_fornecidas);
                $nLinhasTurno = is_array($turnosParciais) ? count($turnosParciais) : 1;
                $this->cell(50, 5 * $nLinhasTurno, $turno->turno, 1, 0, 'C');

                    // Imprime Turno Parcial
                foreach ($turno->vagas_fornecidas as $vagasFornecidas) {
                    foreach ($vagasFornecidas as $keyTurno => $qtdVagas) {
                        $this->setX(110);
                        $this->setFont(
                            'Arial',
                            '',
                            $this->calculaTamanhoFonte(40, $this->mapaTurnos[$keyTurno], 8)
                        );
                        $this->cell(45, 5, $this->mapaTurnos[$keyTurno], 1, 1, 'C');
                    }
                }
                    // Imprime Vagas Disponíveis
                foreach ($turno->vagas_disponiveis as $vagasDisponiveis) {
                    // Aqui eu volto duas células para cima, para seguir na mesma linha inicial
                    $this->setY($this->getY() - 10);
                    foreach ($vagasDisponiveis as $qtdVagas) {
                        $this->setX(155);
                        $this->cell(45, 5, $qtdVagas == '0' ? '-' : $qtdVagas, 1, 1, 'C');
                    }
                }
            }
        }
        $this->setDrawColor(0, 0, 0);
        $this->setTextColor(0, 0, 0);
    }

    private function verificaTotalLinhasTurnos($etapa)
    {
        $totalLinhasTurno = 0;
        foreach ($etapa->turnos as $turno) {
            $qtdVagas = reset($turno->vagas_fornecidas);
            $totalLinhasTurno += count($qtdVagas);
        }
        return $totalLinhasTurno;
    }

    private function getLegendaTurnos($todosTurnos)
    {
        foreach ($todosTurnos as $turno) {
            $this->mapaTurnos[$turno->codigo_turno] = $turno->nome_turno;
        }
    }

    private function calculaTamanhoFonte($largura, $conteudo, $tamanhoFonte)
    {
        $fonteDimensionada = $this->getFonteSizeByWidthCell($largura, $tamanhoFonte, $conteudo);
        return min($fonteDimensionada, $tamanhoFonte);
    }
}
