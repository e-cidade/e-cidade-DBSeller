<?php

namespace App\Domain\Educacao\Secretaria\Relatorios\Atividades;

use ECidade\Pdf\Pdf;
use Symfony\Component\Validator\Constraints\Length;

class AtividadesProfissionaisPdf extends Pdf
{
    protected $dados;
    public function __construct($dados)
    {
        parent::__construct('L');
        $this->dados = $dados;
        $this->addTitulo('RELATÓRIO RECURSOS HUMANOS POR ATIVIDADE');
        $this->addTitulo('');
        $atividades = $this->dados['listaAtividades'];
        $this->addTitulo("Atividade(s): $atividades");
    }

    public function calculaFatorAjuste()
    {
        $parametrosOpcionais = $this->dados['filtros']['parametrosOpcionais'];
        $tamanhoPadrao = 277.50;
        $fatorAjuste = 1;

        if (in_array('1', $parametrosOpcionais) &&
            in_array('2', $parametrosOpcionais) &&
            in_array('3', $parametrosOpcionais)) {
            $fatorAjuste = 1;
        } elseif ((in_array('1', $parametrosOpcionais) && in_array('3', $parametrosOpcionais)) ||
            (in_array('2', $parametrosOpcionais) && in_array('3', $parametrosOpcionais)) ||
            (in_array('1', $parametrosOpcionais) && in_array('2', $parametrosOpcionais))) {
            $fatorAjuste = $tamanhoPadrao / 260;
        } elseif (in_array('3', $parametrosOpcionais) && in_array('4', $parametrosOpcionais)) {
            $fatorAjuste = $tamanhoPadrao / 252.50;
        } elseif (in_array('2', $parametrosOpcionais) || (in_array('3', $parametrosOpcionais))) {
            $fatorAjuste = $tamanhoPadrao / 242.50;
        } elseif (in_array('4', $parametrosOpcionais)) {
            $fatorAjuste = $tamanhoPadrao / 235;
        } else {
            $fatorAjuste = $tamanhoPadrao / 225;
        }

        return $fatorAjuste;
    }

    public function calculaAlturaCelula($atividade, $regime, $nomeSocial, $fatorAjusteColuna)
    {
        $minFontSize = 5;
        $maxHeight = 10;

        $tamanhoFonteAtividade = $this->getFonteSizeByWidthCell(36 * $fatorAjusteColuna, 6, $atividade);
        $tamanhoFonteRegime = $this->getFonteSizeByWidthCell(26 * $fatorAjusteColuna, 6, $regime);

        $height = (($tamanhoFonteAtividade < $minFontSize || $tamanhoFonteRegime < $minFontSize) ||
                    !empty($nomeSocial)) ?
                $maxHeight : // Se sim, retorna 10
                $minFontSize; // Se não, retorna 5

        return $height;
    }

    /**
     * Adiciona colunas opcionais ao cabeçalho.
     *
     * @param int $filtro
     */

    public function adicionarColunaOpcional($filtro)
    {
        $fatorAjusteColuna = $this->calculaFatorAjuste();
        $coluna = '';
        switch ($filtro) {
            case 0:
                break;
            case 1:
                $coluna = 'DATA SAÍDA';
                break;
            case 2:
                $coluna = 'DATA FIM';
                break;
            case 3:
                $coluna = 'CPF';
                break;
            case 4:
                $coluna = 'ASSINATURA';
                break;
        }

        if (!empty($coluna) && $coluna == 'ASSINATURA') {
            $this->cell(50 * $fatorAjusteColuna, 10, $coluna, 1, 1, 'C', 1);
        } elseif (!empty($coluna)) {
            $this->cell(17.5 * $fatorAjusteColuna, 10, $coluna, 1, 0, 'C', 1);
        }
    }

    public function cabecalho()
    {
        $fatorAjusteColuna = $this->calculaFatorAjuste();
        $this->setFont('Arial', 'B', 7);
        $this->cell(20 * $fatorAjusteColuna, 10, 'CGM / MAT', 1, 0, 'C', 1);
        $this->cell(60 * $fatorAjusteColuna, 10, 'NOME / NOME SOCIAL', 1, 0, 'C', 1);
        $this->cell(47.5 * $fatorAjusteColuna, 10, 'ATIVIDADE', 1, 0, 'C', 1);
        $this->cell(37.5 * $fatorAjusteColuna, 10, 'REGIME', 1, 0, 'C', 1);
        // $this->cell(15 * $fatorAjusteColuna, 10, 'TURNO', 1, 0, 'C', 1);
        $this->multiCell(20 * $fatorAjusteColuna, 5, 'DATA DE' . PHP_EOL . 'INGRESSO', 1, 'C', 1);
        $this->setY($this->getY() - 10);
        $this->setX($this->getX() + 185 * $fatorAjusteColuna);

        $parametrosOpcionais = $this->dados['filtros']['parametrosOpcionais'];
        $compararPorValor = function ($a, $b) {
            return $a - $b;
        };
        usort($parametrosOpcionais, $compararPorValor);

        if (!empty($parametrosOpcionais)) {
            foreach ($parametrosOpcionais as $filtro) {
                $this->adicionarColunaOpcional($filtro);
            }
        }

        $colunasOpcionais = $this->dados['filtros']['parametrosOpcionais'];
        $numColunasOpcionais = in_array('0', $colunasOpcionais) ? (count($colunasOpcionais) - 1)
                                                                : count($colunasOpcionais);
        if (!in_array('4', $parametrosOpcionais)) {
            $this->multiCell(20 * $fatorAjusteColuna, 5, 'STATUS' . PHP_EOL . 'SERVIDOR', 1, 'C', 1);
            $this->setY($this->getY() - 10);
            $this->setX($this->getX() + (205 + $numColunasOpcionais * 17.5) * $fatorAjusteColuna);
            $this->multiCell(20 * $fatorAjusteColuna, 5, 'STATUS' . PHP_EOL . 'ATIVIDADE', 1, 'C', 1);
        }
    }

    public function emitir()
    {
        $this->initPdf();
        $this->imprimeDados();
        $fileName = 'tmp/atividades_profissionais-' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Atividades Profissionais",
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

    public function imprimeSubtotalAtivos()
    {

        $isActive = $this->dados['filtros']['codSituacaoServidores'] == 1;
        $isInactive = $this->dados['filtros']['codSituacaoServidores'] == 2;
        $isAll = $this->dados['filtros']['codSituacaoServidores'] == 3;
        $totalLinhasTotalizador = count($this->dados['totalizadorAtivas']);

        if (($this->getY() + ($totalLinhasTotalizador * 5 + 35)) > 180) {
            $this->addPage();
        }

        $this->setFont('Arial', 'B', 9);
        $lineHeight = $this->getY();
        $this->setY($lineHeight + 5);
        $this->cell(277.5, 5, 'TOTALIZADOR POR ATIVIDADE(S)', 0, 1, 'C', 1);
        $this->line(10, $lineHeight + 10, 287, $lineHeight + 10);
        $this->line(10, $lineHeight + 10.2, 287, $lineHeight + 10.2);
        $this->setY($lineHeight + 12);

        $totalizador = 0;
        $alturaInicial = $this->getY();
        if (!$isInactive) {
            // Imprimindo Totalizador de servidores ativos
            $subtotalAtivas = 0;
            $this->setX($isAll ? 43.75 : 98.5);
            $this->setFont('Arial', 'B', 8);
            $this->cell(100, 5, 'RECURSOS HUMANOS ATIVOS', 'B', 1, 'C', 1);
            $this->setX($isAll ? 43.75 : 98.5);
            $this->cell(50, 5, 'ATIVIDADE', 'B', 0, 'C', 0);
            $this->cell(50, 5, 'QUANTIDADE', 'B', 1, 'C', 0);

            $this->setFont('Arial', '', 8);
            foreach ($this->dados['totalizadorAtivas'] as $atividade => $quantidade) {
                $this->setX($isAll ? 43.75 : 98.5);
                $this->cell(50, 5, $atividade, 0, 0, 'L', 0);
                $this->cell(50, 5, $quantidade, 0, 1, 'C', 0);
                $subtotalAtivas += $quantidade;
                $totalizador += $quantidade;
            }

            $this->setFont('Arial', 'B', 8);
            $this->setX(!$isActive && !$isInactive ? 43.75 : 98.5);
            $this->cell(50, 5, 'SUBTOTAL', 'T', 0, 'C', 0);
            $this->cell(50, 5, $subtotalAtivas, 'T', 1, 'C', 0);
            $this->setY($this->getY() + 5);
        }
        $this->imprimeSubtotalInativos($totalizador, $alturaInicial);
    }

    public function imprimeSubtotalInativos($totalizador, $alturaInicial)
    {
        $isActive = $this->dados['filtros']['codSituacaoServidores'] == 1;
        $isAll = $this->dados['filtros']['codSituacaoServidores'] == 3;

        if (!$isActive) {
            // Imprimindo Totalizador de servidores inativos
            $subtotalInativas = 0;
            if ($isAll) {
                $this->setY($alturaInicial);
            }
            $this->setX($isAll ? 153.75 : 98.5);
            $this->setFont('Arial', 'B', 8);
            $this->cell(100, 5, 'RECURSOS HUMANOS INATIVOS', 'B', 1, 'C', 1);
            $this->setX($isAll ? 153.75 : 98.5);
            $this->cell(50, 5, 'ATIVIDADE', 'B', 0, 'C', 0);
            $this->cell(50, 5, 'QUANTIDADE', 'B', 1, 'C', 0);

            $this->setFont('Arial', '', 8);
            foreach ($this->dados['totalizadorInativas'] as $atividade => $quantidade) {
                $this->setX($isAll ? 153.75 : 98.5);
                $this->cell(50, 5, $atividade, 0, 0, 'L', 0);
                $this->cell(50, 5, $quantidade, 0, 1, 'C', 0);
                $subtotalInativas += $quantidade;
                $totalizador += $quantidade;
            }

            $this->setFont('Arial', 'B', 8);
            $this->setX($isAll ? 153.75 : 98.5);
            $this->cell(50, 5, 'SUBTOTAL', 'T', 0, 'C', 0);
            $this->cell(50, 5, $subtotalInativas, 'T', 1, 'C', 0);
        }

        $this->imprimeTotalizador($totalizador);
    }

    public function imprimeTotalizador($totalizador)
    {
        $this->setFont('Arial', 'B', 9);
        $lineHeight = $this->getY();
        $this->line(10, $lineHeight + 5, 287, $lineHeight + 5);
        $this->line(10, $lineHeight + 5.2, 287, $lineHeight + 5.2);
        $this->setY($lineHeight + 6);
        $this->setX(73.5);
        $this->cell(75, 5, 'TOTAL GERAL', 'B', 0, 'C', 1);
        $this->cell(75, 5, $totalizador, 'B', 1, 'C', 1);
        $this->setFont('Arial', '', 7);
    }

    public function imprimeDados()
    {
        $this->addPage();
        $this->setFillColor(200);
        $this->cabecalho();
        $this->setMargins(10, 8, 8);
        $this->setFillColor(230);
        $fatorAjusteColuna = $this->calculaFatorAjuste();

        // Verificando quantas colunas opcionais foram selecionadas e
        // ajustando o tamanho da célula que contém o nome da escola
        $colunasOpcionais = $this->dados['filtros']['parametrosOpcionais'];
        $numColunasOpcionais = in_array('0', $colunasOpcionais) ? (count($colunasOpcionais) - 1)
                                                        : count($colunasOpcionais);
        $larguraCelulaNomeEscola = 225 + ($numColunasOpcionais * 17.5);

        if (in_array('4', $colunasOpcionais)) {
            $larguraCelulaNomeEscola = 235 + (($numColunasOpcionais - 1) * 17.5);
        }
        $warningMessage = 'NÃO FORAM ENCONTRADOS SERVIDORES PARA OS FILTROS SELECIONADOS';

        if (empty($this->dados['dados'])) {
            $this->cell($larguraCelulaNomeEscola * $fatorAjusteColuna, 5, $warningMessage, 1, 1, 'C', 0);
            return;
        }

        foreach ($this->dados['escolas'] as $escola) {
            $this->setFont('Arial', 'B', 7);
            $this->cell($larguraCelulaNomeEscola * $fatorAjusteColuna, 5, $escola, 1, 1, 'C', 1);

            foreach ($this->dados['dados'] as $dado) {
                if ($escola == $dado['escola']) {
                    $this->setFont('Arial', '', 6);
                    $border = 1;

                    // Preparando dados para enviar para o método calculaAlturaCelula
                    $atividade = $dado['atividade'];
                    $regime = $dado['regime'];
                    $nomeSocial = $dado['nomeSocial'];
                    $height = $this->calculaAlturaCelula($atividade, $regime, $nomeSocial, $fatorAjusteColuna);

                    if (!empty($dado['nomeSocial'])) {
                        $this->cell(20 * $fatorAjusteColuna, $height, $dado['cgm'] .
                                                                ' / ' .
                                                                $dado['matricula'], $border, 0, 'C', 0);
                        $this->multiCell(60 * $fatorAjusteColuna, $height / 2, $dado['nome'] .
                                                                '      / ' .
                                                                PHP_EOL .
                                                                $dado['nomeSocial'], $border, 'C', 0);
                        $this->setY($this->getY() - 10);
                        $this->setX($this->getX() + 80 * $fatorAjusteColuna);
                    } else {
                        $this->cell(20 * $fatorAjusteColuna, $height, $dado['cgm'] .
                                                                ' / ' .
                                                                $dado['matricula'], $border, 0, 'C', 0);
                        $this->cell(60 * $fatorAjusteColuna, $height, $dado['nome'], $border, 0, 'C', 0);
                    }

                    $content = $dado['atividade'];
                    $tamanhoFonte = $this->getFonteSizeByWidthCell(36 * $fatorAjusteColuna, 6, $content);
                    if ($tamanhoFonte < 5) {
                        $this->multiCell(47.5 * $fatorAjusteColuna, $height / 2, $content, $border, 'C', 0);
                        $this->setY($this->getY() - 10);
                        $this->setX($this->getX() + 120 * $fatorAjusteColuna);
                    } else {
                        $this->setFont('Arial', '', 6);
                        $this->cell(47.5 * $fatorAjusteColuna, $height, $content, $border, 0, 'C', 0);
                    }

                    $content = $dado['regime'];
                    $tamanhoFonte = $this->getFonteSizeByWidthCell(26 * $fatorAjusteColuna, 6, $content);
                    if ($tamanhoFonte < 5) {
                        $this->multiCell(37.5 * $fatorAjusteColuna, $height / 2, $content, $border, 'C', 0);
                        $this->setY($this->getY() - 10);
                        $this->setX($this->getX() + 165 * $fatorAjusteColuna);
                    } else {
                        $this->setFont('Arial', '', 6);
                        $this->cell(37.5 * $fatorAjusteColuna, $height, $content, $border, 0, 'C', 0);
                    }

                    $this->setFont('Arial', '', 6);

                    // $this->cell(15 * $fatorAjusteColuna, $height, $dado['turno'], $border, 0, 'C', 0);
                    $this->cell(20 * $fatorAjusteColuna, $height, $dado['dataIngresso'], $border, 0, 'C', 0);

                    $parametrosOpcionais = $this->dados['filtros']['parametrosOpcionais'];
                    $compararPorValor = function ($a, $b) {
                        return $a - $b;
                    };

                    usort($parametrosOpcionais, $compararPorValor);
                    $line = '-----';

                    if (!empty($parametrosOpcionais)) {
                        foreach ($parametrosOpcionais as $filtro) {
                            switch ($filtro) {
                                /***********************
                                *  1 - Data de Saída;  *
                                *  2 - Data Fim;       *
                                *  3 - CPF;            *
                                *  4 - Assinatura      *
                                ************************/
                                case 0:
                                    break;
                                case 1:
                                    $dataSaida = isset($dado['dataSaida']) ? $dado['dataSaida'] : $line;
                                    $this->cell(17.5 * $fatorAjusteColuna, $height, $dataSaida, $border, 0, 'C', 0);
                                    break;
                                case 2:
                                    $dataFim = isset($dado['dataFim']) ? $dado['dataFim'] : $line;
                                    $this->cell(17.5 * $fatorAjusteColuna, $height, $dataFim, $border, 0, 'C', 0);
                                    break;
                                case 3:
                                    $cpf = isset($dado['cpf']) ? $dado['cpf'] : 'CPF não informado';
                                    $this->cell(17.5 * $fatorAjusteColuna, $height, $cpf, $border, 0, 'C', 0);
                                    break;
                                case 4:
                                    $this->cell(50 * $fatorAjusteColuna, $height, '', $border, 1, 'C', 0);
                                    break;
                            }
                        }
                    }
                    if (!in_array('4', $parametrosOpcionais)) {
                        $this->cell(20 * $fatorAjusteColuna, $height, $dado['situacaoServ'], $border, 0, 'C', 0);
                        $this->cell(20 * $fatorAjusteColuna, $height, $dado['situacaoAtiv'], $border, 1, 'C', 0);
                    }

                    $height = 5;
                    if ($this->getY() > 180) {
                        $this->addPage();
                        $this->cabecalho();
                    }
                }
            }
        }
        $this->imprimeSubtotalAtivos();
    }
}
