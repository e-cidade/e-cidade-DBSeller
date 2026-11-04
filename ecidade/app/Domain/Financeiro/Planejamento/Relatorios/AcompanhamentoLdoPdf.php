<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios;

class AcompanhamentoLdoPdf extends AcompanhamentoPdf
{
    protected $wTotalizador;

    protected function calculaColunas()
    {
        $this->wIniciativa = $this->wLinha;
        $this->wIniciativa -= $this->wValoresFonteRecuso;
        $this->wTotalizador = $this->wIniciativa;

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
            $this->wTotalizador -= $this->wExecucao;
        }
    }

    protected function imprimirDados()
    {
        foreach ($this->dados['detalhamentos'] as $orgao) {
            $this->addPage();
            $this->setFont('Arial', 'B', 8);

            $nome = $this->retornaDescricao($orgao);
            foreach ($orgao->unidades as $unidade) {
                $this->imprimeUnidades($nome, $unidade);
            }

            $this->imprimeTotalizadorOrgao($orgao);
        }
    }

    protected function retornaDescricao($obj)
    {
        return sprintf('%s - %s', $obj->formatado, $obj->descricao);
    }

    protected function imprimeIniciativas($iniciativas)
    {
        foreach ($iniciativas as $iniciativa) {
            $produto = $iniciativa->produto;
            $regionalizacao = $iniciativa->regionalizacoes;
            $descricao = sprintf(
                '%s - %s',
                $iniciativa->formatado,
                $iniciativa->descricao
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

            if (count($iniciativa->metas)) {
                foreach ($this->exercicios as $exercicio) {
                    $this->setX($xAtualizado);
                    if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
                        $meta = $iniciativa->metas[$exercicio];
                        $this->cellAdapt(6, 15, 4, $meta->unidade, 1, 0, 'C');
                        $this->cell(20, 4, $meta->meta_fisica, 1, 0, 'C');
                    }

                    $valor = $iniciativa->valores[$exercicio];
                    $valorLivre = '';
                    $valorVinculado = formataValorMonetario($valor->valor);

                    if ($iniciativa->recurso_livre) {
                        $valorLivre = formataValorMonetario($valor->valor);
                        $valorVinculado = '';
                    }
                    $this->cell(9, 4, $exercicio, 1, 0, 'C');
                    $this->cell(22, 4, $valorLivre, 1, 0, 'R');
                    $this->cell(22, 4, $valorVinculado, 1, 0, 'R');
                    if ($this->filtros['apresentarExecucao'] == 1) {
                        $this->cell($this->wExecucao, 4, formataValorMonetario($valor->execucao), 1, 0, 'R');
                    }
                    $this->ln();
                }
            }

            $this->setY($alturaFinal);
        }
    }

    protected function cabecalhoIniciativas()
    {
        $this->ln();
        $this->bold();

        if (32 > $this->getAvailHeight()) {
            $this->AddPage();
        }

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
        $this->cell($this->wValoresFonteRecuso, 5, 'Valores Fonte Recurso', 1, 0, 'C', 1);
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
        $this->cell(22, 5, 'Livre', 1, 0, 'C', 1);
        $this->cell(22, 5, 'Vinculado', 1, 0, 'C', 1);
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->cell($this->wExecucao, 5, '', 1, 0, 'C', 1);
        }
        $this->Ln();
        $this->setFont('Arial', '', 7);
    }

    private function imprimeUnidades($orgao, $unidade)
    {
        $nome = $this->retornaDescricao($unidade);
        foreach ($unidade->funcoes as $funcao) {
            $this->imprimeFuncao($orgao, $nome, $funcao);
        }
    }

    private function imprimeFuncao($orgao, $unidade, $funcao)
    {
        $nome = $this->retornaDescricao($funcao);
        foreach ($funcao->subfuncoes as $subfuncao) {
            $this->imprimeSubfuncoes($orgao, $unidade, $nome, $subfuncao);
        }
    }

    private function imprimeSubfuncoes($orgao, $unidade, $funcao, $subfuncao)
    {
        $nome = $this->retornaDescricao($subfuncao);
        foreach ($subfuncao->programas as $programa) {
            $this->imprimeProgramas($orgao, $unidade, $funcao, $nome, $programa);
        }
    }

    private function imprimeProgramas($orgao, $unidade, $funcao, $subfuncao, $programa)
    {
        foreach ($programa->objetivos as $objetivo) {
            $h = ($this->nbLines(260, $objetivo->descricao) * $this->alturaLinha) + ($this->alturaLinha * 5);
            if ($h > $this->getAvailHeight()) {
                $this->AddPage();
            }

            $this->setFont('Arial', 'B', 8);
            $this->imprimeLinha('Órgão: ', $orgao);
            $this->imprimeLinha('Unidade: ', $unidade);
            $this->imprimeLinha('Função: ', $funcao);
            $this->imprimeLinha('Subfunção: ', $subfuncao);
            $this->imprimeLinha('Programa: ', $this->retornaDescricao($programa));
            $this->bold();
            $this->imprimeLinhaMultipla('Objetivo: ', $objetivo->descricao);
            $this->regular();
            $this->cabecalhoIniciativas();
            $this->imprimeIniciativas($objetivo->iniciativas);
        }
        $this->ln();
    }

    private function imprimeTotalizadorOrgao($orgao)
    {
        $this->cabecalhoTotalizador();
        $this->regular();
        $h = (count($this->exercicios) * $this->alturaLinha);
        $this->cell($this->wTotalizador, $h, $this->retornaDescricao($orgao), 1, 0, 'L');
        foreach ($orgao->valores as $valor) {
            $this->setX(10 + $this->wTotalizador);
            $this->cell(9, 4, $valor->ano, 1, 0, 'C');
            $this->cell(22, 4, formataValorMonetario($valor->valorLivre), 1, 0, 'R');
            $this->cell(22, 4, formataValorMonetario($valor->valorVinculado), 1, 0, 'R');
            if ($this->filtros['apresentarExecucao'] == 1) {
                $this->cell($this->wExecucao, 4, formataValorMonetario($valor->execucao), 1, 0, 'R');
            }
            $this->ln();
        }
    }

    protected function cabecalhoTotalizador()
    {
        $h = 10 + (count($this->exercicios) * $this->alturaLinha);
        if ($h > $this->getAvailHeight()) {
            $this->AddPage();
        }
        $this->setFont('Arial', 'B', 8);
        $this->cell($this->wTotalizador, 10, "Totalização do Órgão", 1, 0, 'C', 1);
        $this->cell($this->wValoresFonteRecuso, 5, 'Valores Fonte Recurso', 1, 0, 'C', 1);
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->cell($this->wExecucao, 5, 'Execução', 1, 0, 'C', 1);
        }
        $this->ln();
        $this->setX(10 + $this->wTotalizador);
        $this->cell(9, 5, 'Ano', 1, 0, 'C', 1);
        $this->cell(22, 5, 'Livre', 1, 0, 'C', 1);
        $this->cell(22, 5, 'Vinculado', 1, 0, 'C', 1);
        if ($this->filtros['apresentarExecucao'] == 1) {
            $this->cell($this->wExecucao, 5, '', 1, 0, 'C', 1);
        }
        $this->Ln();
        $this->setFont('Arial', '', 7);
    }
}
