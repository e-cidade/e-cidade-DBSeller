<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018\Layout;

use ECidade\Financeiro\Contabilidade\Relatorio\Layout;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018\AnexoXIII as Relatorio;
use PDFDocument;

class AnexoXIII extends Layout
{
    const HEADER_1 = 'RELATÓRIO RESUMIDO DA EXECUÇÃO ORÇAMENTÁRIA';
    const HEADER_2 = 'DEMONSTRATIVO DAS PARCERIAS PÚBLICO-PRIVADAS';
    const HEADER_3 = 'ORÇAMENTOS FISCAL E DA SEGURIDADE SOCIAL';
    const LARGURA_IMPACTOS_CONTRATACOES_PPP = 82;
    const LARGURA_DESPESAS_PPP = 82;
    const LARGURA_SALDO_TOTAL = 97.250;
    const LARGURA_REGISTROS_EFETUADOS = 99.626;
    const LARGURA_NO_BIMESTRE = 49.813;
    const LARGURA_ATE_BIMESTRE = 49.813;
    const LARGURA_EXERCICIO = 16.600;
    const LARGURA_EXERCICIO_ANTERIOR = 24.5;
    const LARGURA_EXERCICIO_CORRENTE = 22.96;

    private $linhas;

    protected function montar()
    {
        $this->linhas = $this->relatorio->getLinhas();

        $this->montarLinhaIntroducao()
            ->montarCabecalhoImpactosContratacoesPPP()
            ->montarLinhasImpactosContratacoesPPP()
            ->montarCabecalhoDespesasPPP()
            ->montarLinhasDespesasPPP();
    }

    private function montarLinhaIntroducao()
    {
        $this->pdf->Cell(
            138,
            static::ALTURA_LINHA,
            'RREO - Anexo 13 (Lei nº 11.079, de 30.12.2004, arts. 22, 25 e 28)',
            0,
            0,
            PDFDocument::ALIGN_LEFT
        );
        $this->pdf->Cell(139, static::ALTURA_LINHA, 'Em reais', 0, 1, PDFDocument::ALIGN_RIGHT);

        return $this;
    }

    private function montarCabecalhoImpactosContratacoesPPP()
    {
        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            self::LARGURA_IMPACTOS_CONTRATACOES_PPP,
            static::ALTURA_LINHA * 3,
            'IMPACTOS DAS CONTRATAÇÕES DE PPP'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_SALDO_TOTAL,
            static::ALTURA_LINHA,
            "SALDO TOTAL EM\n31 DE DEZEMBRO DO\nEXERCÍCIO ANTERIOR",
            $x
        );
        $this->montarCabecalhoColuna(
            static::LARGURA_REGISTROS_EFETUADOS,
            static::ALTURA_LINHA * 2,
            "REGISTROS EFETUADOS EM {$this->relatorio->getAno()}",
            $x
        );

        $y = $this->pdf->GetY() + static::ALTURA_LINHA * 2;

        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(
            static::LARGURA_NO_BIMESTRE,
            static::ALTURA_LINHA,
            'No bimestre',
            1,
            0,
            PDFDocument::ALIGN_CENTER,
            1
        );
        $this->pdf->Cell(
            static::LARGURA_ATE_BIMESTRE,
            static::ALTURA_LINHA,
            'Até o Bimestre',
            1,
            1,
            PDFDocument::ALIGN_CENTER,
            1
        );

        return $this;
    }

    private function montarCabecalhoDespesasPPP()
    {
        $this->pdf->SetFont('arial', 'B', static::TAMANHO_FONTE);

        $x = $this->montarCabecalhoColuna(
            static::LARGURA_DESPESAS_PPP,
            static::ALTURA_LINHA * 2,
            'DESPESAS DE PPP'
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_EXERCICIO_ANTERIOR,
            static::ALTURA_LINHA,
            "EXERCÍCIO\nANTERIOR",
            $x
        );
        $x = $this->montarCabecalhoColuna(
            static::LARGURA_EXERCICIO_CORRENTE,
            static::ALTURA_LINHA,
            "EXERCÍCIO CORRENTE ({$this->relatorio->getAno()})",
            $x
        );

        for ($exercicioFuturo = 1; $exercicioFuturo <= 9; $exercicioFuturo++) {
            $ano = $this->relatorio->getAno() + $exercicioFuturo;

            $x = $this->montarCabecalhoColuna(static::LARGURA_EXERCICIO, static::ALTURA_LINHA * 2, $ano, $x);
        }

        $this->pdf->Ln();

        return $this;
    }

    private function montarLinhasImpactosContratacoesPPP()
    {
        $linhas = array_filter($this->linhas, function ($linha) {
            return $linha->ordem <= Relatorio::LINHA_OUTROS_PASSIVOS_CONTINGENTES;
        });

        $preenche = false;

        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;

            $this->montarLinha(
                static::LARGURA_IMPACTOS_CONTRATACOES_PPP,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
            ->montarLinha(static::LARGURA_SALDO_TOTAL, $linha->saldo_anterior, $totalizar, $preenche)
            ->montarLinha(static::LARGURA_NO_BIMESTRE, $linha->saldo_anterior_no_bimestre, $totalizar, $preenche)
            ->montarLinha(static::LARGURA_ATE_BIMESTRE, $linha->saldo_final, $totalizar, $preenche, true);
        }

        return $this;
    }

    private function montarLinhasDespesasPPP()
    {
        $linhas = array_filter($this->linhas, function ($linha) {
            return $linha->ordem > Relatorio::LINHA_OUTROS_PASSIVOS_CONTINGENTES;
        });

        foreach ($linhas as $linha) {
            $totalizar = $linha->totalizar;
            $preenche = false;

            if ($linha === end($linhas)) {
                $preenche = true;
            }

            if (static::ALTURA_LINHA * 4 > $this->pdf->getAvailHeight()) {
                $this->pdf->Cell(277, 1, '', 'T');
                $this->pdf->AddPage();
                $this->montarLinhaIntroducao()->montarCabecalhoDespesasPPP();
            }

            $this->montarLinha(
                static::LARGURA_DESPESAS_PPP,
                $linha->descricao,
                $totalizar,
                $preenche,
                false,
                $linha->nivel
            )
            ->montarLinha(static::LARGURA_EXERCICIO_ANTERIOR, $linha->exercicio_anterior, $totalizar, $preenche)
            ->montarLinha(static::LARGURA_EXERCICIO_CORRENTE, $linha->exercicio_corrente, $totalizar, $preenche);

            $this->pdf->SetFont('arial', '', static::TAMANHO_FONTE-1);

            $this->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_1, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_2, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_3, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_4, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_5, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_6, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_7, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_8, $totalizar, $preenche)
                ->montarLinha(static::LARGURA_EXERCICIO, $linha->exercicio_corrente_9, $totalizar, $preenche, true);
        }

        return $this;
    }
}
