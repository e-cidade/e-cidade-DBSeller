<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios;

class AcompanhamentoPpaPdf extends AcompanhamentoPdf
{
    protected $wMetaFinanceira = 35;
    protected function imprimirDados()
    {
        foreach ($this->dados['programas'] as $programa) {
            $this->addPage();
            $this->setFont('Arial', 'B', 8);

            $this->imprimeObjetivos($programa, $programa['objetivos']);

            // imprime as linhas das iniciativas
            if (!empty($programa['iniciativas'])) {
                $linhas = $this->quantidadeExercicios + 3;
                $altura = $this->calculaAlturaLinha($linhas);
                if ($this->getAvailHeight() < ($altura + 5)) {
                    $this->AddPage();
                }

                $this->setFont('Arial', 'B', 8);
                $descricaoPrograma = sprintf('%s - %s', $programa['formatado'], $programa['o54_descr']);
                $this->imprimeLinha('Programa: ', $descricaoPrograma);
                $this->imprimeOrgaos($programa['orgaos']);
                $this->imprimeLinha('Objetivo:', ' - ');

                $this->imprimeIniciativas($programa['iniciativas']);
            }

            $this->ln();
        }
    }

    protected function imprimeOrgaos($orgaos)
    {
        $nomes = collect($orgaos)->map(function ($orgao) {
            return sprintf('%s - %s', $orgao['formatado'], $orgao['descricao']);
        })->implode(', ');


        $this->imprimeLinhaMultipla('Órgãos: ', $nomes);
    }

    protected function calculaColunas()
    {
        $this->wIniciativa = $this->wLinha;
        $this->wIniciativa -= $this->wMetaFinanceira;

        // 10 é o valor da margem
        $this->xInicioValoresIniciativa = 10 + $this->wIniciativa;

        if ($this->filtros['apresentaRegionalizacao'] == 1) {
            $this->wIniciativa -= $this->wRegionalizacao;
        }

        if ($this->filtros['apresentaProduto'] == 1) {
            $this->wIniciativa -= $this->wProduto;
        }
        if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
            $this->wIniciativa -= $this->wMetaFisica;
            $this->xInicioValoresIniciativa -= $this->wMetaFisica;
        }
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->wIniciativa -= $this->wExecucao;
            $this->xInicioValoresIniciativa -= $this->wExecucao;
        }
    }

    protected function imprimeObjetivos($programa, $objetivos)
    {
        if (empty($objetivos)) {
            return;
        }

        foreach ($objetivos as $objetivo) {
            if (empty($objetivo['iniciativas'])) {
                continue;
            }
            $this->setFont('Arial', '', 8);

            $descricaoObjetivo = sprintf(
                '%s - %s',
                str_pad($objetivo['pl11_numero'], 5, ' ', STR_PAD_LEFT),
                $objetivo['pl11_descricao']
            );

            $nbLines = $this->nbLines(260, $descricaoObjetivo);
            $linhas = ($nbLines > $this->quantidadeExercicios) ? $nbLines : $this->quantidadeExercicios;
            $linhas += 2;

            // valida se vai caber na página ao menos todos títulos e uma linha da iniciativa
            $altura = $this->calculaAlturaLinha($linhas);
            if ($this->getAvailHeight() < ($altura + 5)) {
                $this->AddPage();
            }

            $descricaoPrograma = sprintf('%s - %s', $programa['formatado'], $programa['o54_descr']);
            $this->imprimeLinha('Programa: ', $descricaoPrograma);
            $this->imprimeOrgaos($programa['orgaos']);
            $this->setFont('Arial', 'B', 8);

            $h = $nbLines * $this->alturaLinha;
            $this->cell(20, $h, 'Objetivo:');
            $this->setFont('Arial', '', 8);

            $this->multiCell(260, $this->alturaLinha, $descricaoObjetivo, 0, 'L');

            if (!empty($objetivo['iniciativas'])) {
                $this->imprimeIniciativas($objetivo['iniciativas']);
            }
            $this->ln();
        }
    }

    protected function imprimeIniciativas($iniciativas)
    {
        $this->cabecalhoIniciativa();

        foreach ($iniciativas as $iniciativa) {
            $produto = $iniciativa['descricao_produto'];
            $regionalizacao = collect($iniciativa['regionalizacoes'])->implode('o11_descricao', ', ');
            $descricao = sprintf(
                '%s - %s',
                $iniciativa['acao'],
                $iniciativa['descricao_acao']
            );

            $this->setFont('Arial', '', 6);
            $alturaLinha = $this->getAlturaLinha($descricao, $regionalizacao, $produto);

            if ($alturaLinha > $this->getAvailHeight()) {
                $this->AddPage();
            }

            $x = $this->GetX();
            $y = $this->GetY();

            $alturaFinal = $y + $alturaLinha;
            // desenha linhas
            $this->Line($x, $y, $x, $alturaFinal); // primeira coluna
            $this->Line($x, $y, 203, $y); // linha de cima
            $this->Line($x, $alturaFinal, 290, $alturaFinal); // linha de baixo

            $this->MultiCell($this->wIniciativa, 4, $descricao);
            $xAtualizado = $x + $this->wIniciativa;
            $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); // coluna final iniciativas

            if ($this->filtros['apresentaRegionalizacao'] == 1) {
                $this->setXY($xAtualizado, $y);
                $this->MultiCell($this->wRegionalizacao, 4, $regionalizacao, 0, 'L');
                $xAtualizado = $xAtualizado + $this->wRegionalizacao;
                $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); //  coluna final regionalizacao;
            }
            if ($this->filtros['apresentaProduto'] == 1) {
                $this->setXY($xAtualizado, $y);
                $this->MultiCell($this->wProduto, 4, $produto, 0, 'L');
                $xAtualizado = $xAtualizado + $this->wProduto;
                $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); //  coluna final produto;
            }

            $this->setXY($xAtualizado, $y);

            if (count($iniciativa['metas'])) {
                if (!$this->isPPA) {
                    $valor = array_shift($iniciativa['metas']);
                    $iniciativa['metas'] = [$valor];
                }

                foreach ($iniciativa['metas'] as $meta) {
                    $this->setX($xAtualizado);

                    if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
                        $this->cellAdapt(6, 15, 4, $meta['unidade'], 1, 0, 'C');
                        $this->cell(20, 4, $meta['meta_fisica'], 1, 0, 'C');
                    }
                    $this->cell(9, 4, $meta['exercicio'], 1, 0, 'C');
                    $this->cell(26, 4, formataValorMonetario($meta['meta_financeira']), 1, 0, 'R');
                    if ($this->filtros['apresentarExecucao'] == 1) {
                        $this->cell($this->wExecucao, 4, formataValorMonetario($meta['execucao']), 1, 0, 'R');
                    }
                    $this->ln();
                }
            } else {
                $exercicios = $this->exercicios;
                if (!$this->isPPA) {
                    $valor = array_shift($exercicios);
                    $exercicios = [$valor];
                }

                foreach ($exercicios as $exercicio) {
                    $this->setX($xAtualizado);
                    $this->cell(9, 4, $exercicio, 1, 0, 'C');
                    $this->cell(26, 4, '', 1, 0, 'R');
                    if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
                        $this->cell(15, 4, '', 1, 0, 'C');
                        $this->cell(20, 4, '', 1, 0, 'C');
                    }
                    if ($this->filtros['apresentarExecucao'] == 1) {
                        $this->cell($this->wExecucao, 4, '', 1);
                    }
                    $this->ln();
                }
            }

            $this->setY($alturaFinal);
        }
    }

    protected function cabecalhoIniciativa()
    {
        if (32 > $this->getAvailHeight()) {
            $this->AddPage();
        }
        $this->ln();
        $this->setFont('Arial', 'B', 8);
        $this->cell($this->wIniciativa, 10, "Ações / Iniciativas", 1, 0, 'C', 1);

        if ($this->filtros['apresentaRegionalizacao'] == 1) {
            $this->cell($this->wRegionalizacao, 10, 'Regionalização', 1, 0, 'C', 1);
        }
        if ($this->filtros['apresentaProduto'] == 1) {
            $this->cell($this->wProduto, 10, 'Produto', 1, 0, 'C', 1);
        }

        if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
            $this->cell($this->wMetaFisica, 5, 'Metas Fisicas', 1, 0, 'C', 1);
        }
        $this->cell($this->wMetaFinanceira, 5, 'Metas Financeiras', 1, 0, 'C', 1);
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->cell($this->wExecucao, 5, 'Execução', 1, 0, 'C', 1);
        }
        $this->ln();

        $this->setX($this->xInicioValoresIniciativa);
        $this->setFont('Arial', 'B', 6);
        if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
            $this->cell(15, 5, 'Unid. Medida', 1, 0, 'C', 1);
            $this->cell(20, 5, 'Metas Fisicas', 1, 0, 'C', 1);
        }
        $this->cell(9, 5, 'Ano', 1, 0, 'C', 1);
        $this->cell(26, 5, 'Valor', 1, 0, 'C', 1);
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->cell($this->wExecucao, 5, '', 1, 0, 'C', 1);
        }
        $this->Ln();
        $this->setFont('Arial', '', 7);
    }
}
