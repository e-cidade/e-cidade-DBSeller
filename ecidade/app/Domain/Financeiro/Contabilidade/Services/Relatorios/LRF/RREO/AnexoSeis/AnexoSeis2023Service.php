<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoSeis;

use DBDate;

class AnexoSeis2023Service extends AnexoSeisService
{
    protected $sections = [
        'receita_1' => [1, 43],  // RECEITAS PRIMÁRIAS
        'despesa_1' => [44, 64], // DESPESAS PRIMÁRIAS
        'baldesp_1' => [68, 69], // JUROS NOMINAIS
        'baldesp_2' => [71, 78], // CALCULO DO RESULTADO NOMINAL
        'baldesp_3' => [81, 88], // AJUSTE METODOLÓGICO
    ];

    protected $linhasProcessarRpsDespesaPrimaria = [
        44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61
    ];

    protected $totalizarSoma = [
        2 => [3, 4, 5, 6, 7],
        9 => [10, 11],
        12 => [13, 14, 15, 16, 17, 18, 19],
        20 => [21, 22],
        1 => [2, 8, 9, 12, 20],

        29 => [30, 31, 32],
        33 => [34, 35],
        36 => [37, 38],
        26 => [27, 28, 30, 31, 32, 34, 35, 37, 38],

        44 => [45, 46, 47],
        53 => [54, 55, 56, 57],
        51 => [52, 54, 55, 56, 57, 58],
    ];

    protected $totalizarSubtracao = [
        23 => [1, 10, 21],
        48 => [44, 46],
        73 => [74, 75, 76],
        78 => [71, 72],

    ];

    protected function processar()
    {
        $this->processaLinhas($this->linhas);

        $this->processarRpsDespesaPrimaria();
        $this->processaReceita($this->getBalanceteReceitaExercicioAnterior(), $this->linhas[81]);

        $this->criaProriedadesValor();
        $this->linhas[82]->saldo_final_acumulado = $this->linhas[31]->arrecadado_acumulado;

        $this->organizaLinhas();
        $this->totalizarLinhas();

        $this->valorManual1 = $this->linhas[67]->valor;
        $this->valorManual2 = $this->linhas[80]->valor;

        $this->valorPrevisaoAtualizada = $this->linhas[90]->previsao_atualizada;
        $this->valorSaldoFinalAcumulado = $this->linhas[91]->saldo_final_acumulado;
        $this->valorSaldoInicial = $this->linhas[92]->total_creditos;
    }

    public function getSimplificado()
    {
        $this->processar();

        $primario = $this->getObjetoSimplificado('RESULTADO PRIMÁRIO (SEM RPPS) - Acima da Linha');
        $nominal = $this->getObjetoSimplificado('RESULTADO NOMINAL (SEM RPPS) - Abaixo da Linha');

        $primario = $this->calculaSimplificado($primario, 66, 67);
        $nominal = $this->calculaSimplificado($nominal, 79, 80);

        return [$primario, $nominal];
    }

    protected function posTotalizar()
    {
        $this->calcularSubtracao();

        $this->totalizaLinha39();
        $this->totalizaLinha42();
        $this->totalizaLinha43();
        $this->totalizaLinha59();
        $this->totalizaLinha63();
        $this->totalizaLinha64();
        $this->totalizaLinha65();
        $this->totalizaLinha66();
        $this->totalizaLinha70();
        $this->totalizaLinha72();
        $this->totalizaLinha78();
        $this->totalizaLinha79();
        $this->totalizaLinha81();
        $this->totalizaLinha87();
        $this->totalizaLinha88();
    }

    /**
     * RECEITAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XIII) = [VII - (VIII + IX + X + XI + XII)]
     */
    protected function totalizaLinha39()
    {
        $this->subtraiLinha(39, [26, 27, 28, 30, 31, 37]);
    }

    /**
     * RECEITA PRIMÁRIA TOTAL (XVI) = (IV + V + XIII + XIV)
     */
    protected function totalizaLinha42()
    {
        $this->somarLinha(42, [23, 24, 39, 40]);
    }

    /**
     * RECEITA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XVII) = (IV + XIII)
     */
    protected function totalizaLinha43()
    {
        $this->somarLinha(43, [23, 39]);
    }

    /**
     * DESPESAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XXVIII) = [XXIII - (XXIV + XXV + XXVI + XXVII)]
     */
    protected function totalizaLinha59()
    {
        $this->subtraiLinha(59, [51, 54, 55, 56, 58]);
    }

    /**
     * DESPESA PRIMÁRIA TOTAL (XXXII) = (XX + XXI + XXVIII + XXIX + XXX)
     */
    protected function totalizaLinha63()
    {
        $this->somarLinha(63, [48, 49, 59, 60, 61]);
    }

    /**
     * DESPESA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XXXIII) = (XX + XXVIII + XXIX)
     */
    protected function totalizaLinha64()
    {
        $this->somarLinha(64, [48, 59, 60]);
    }

    /**
     * RESULTADO PRIMÁRIO (COM RPPS) - Acima da Linha (XXXIV) = [XVIa - (XXXIIa +XXXIIb + XXXIIc)]
     */
    protected function totalizaLinha65()
    {
        $linhaXVI = $this->linhas[42];
        $linha = $this->linhas[63]; // linha XXXII
        $despesa = $linha->pago_acumulado + $linha->pagamento_rp_processado + $linha->pagamento_rp_nao_processado;

        $this->linhas[65]->valor = $linhaXVI->arrecadado_acumulado - ($despesa);
    }

    protected function totalizaLinha66()
    {
        $linhaXVI = $this->linhas[43];
        $linha = $this->linhas[64]; // linha XXXIII
        $despesa = $linha->pago_acumulado + $linha->pagamento_rp_processado + $linha->pagamento_rp_nao_processado;

        $this->linhas[66]->valor = $linhaXVI->arrecadado_acumulado - ($despesa);
    }

    /**
     * RESULTADO NOMINAL (SEM RPPS) - Acima da Linha (XXXVIII) = XXXV + (XXXVI - XXXVII)
     */
    protected function totalizaLinha70()
    {
        $valorJuros = $this->linhas[68]->saldo_final_acumulado - $this->linhas[69]->saldo_final_acumulado;
        $this->linhas[70]->valor = $this->linhas[66]->valor + ($valorJuros);
    }

    /**
     * DEDUÇÕES (XL)
     */
    protected function totalizaLinha72()
    {
        $this->somarLinha(72, [73, 77]);
    }
    /**
     *  DÍVIDA CONSOLIDADA LÍQUIDA (XLII) = (XXXIX - XL)
     */
    protected function totalizaLinha78()
    {
        $this->linhas[78]->saldo_anterior_acumulado = $this->linhas[71]->saldo_anterior_acumulado -
        $this->linhas[72]->saldo_anterior_acumulado;

        $this->linhas[78]->saldo_final_acumulado = $this->linhas[71]->saldo_final_acumulado -
        $this->linhas[72]->saldo_final_acumulado;
    }


    /**
     *  RESULTADO NOMINAL (SEM RPPS) - Abaixo da Linha (XLIII) = (XLIIa - XLIIb)
     */
    protected function totalizaLinha79()
    {
        $linha = $this->linhas[78]; // linha XLII
        $this->linhas[79]->valor = $linha->saldo_anterior_acumulado - $linha->saldo_final_acumulado;
        $this->linhas[79]->resultado_nominal_sem_rpps_abaixo_da_linha = $this->linhas[79]->valor;
    }

    /**
     * VARIAÇÃO DO SALDO RPP (XLIV) = (XLIa - XLIb)
     */
    protected function totalizaLinha81()
    {
        $linha = $this->linhas[75]; // linha XLI
        $this->linhas[81]->saldo_final_acumulado = $linha->saldo_final_acumulado - $linha->saldo_anterior_acumulado;
    }

    /**
     * RESULTADO NOMINAL AJUSTADO (SEM RPPS) AJUSTADO - Abaixo da Linha
     * (L) = [XLIII + (XLIV - XLV - XLVI + XLVII + XLVIII) +/- (XLIX)]
     *        79    + (81   - 82  - 83   +  84   + 85)     +/- 86
     * XLXIX se negativo subtrai, se positivo soma
     */
    protected function totalizaLinha87()
    {
        $this->linhas[87]->saldo_final_acumulado = (
            $this->linhas[79]->valor + (
            $this->linhas[81]->saldo_final_acumulado -
            $this->linhas[82]->saldo_final_acumulado -
            $this->linhas[83]->saldo_final_acumulado +
            $this->linhas[84]->saldo_final_acumulado +
            $this->linhas[85]->saldo_final_acumulado
            )
        );

        $outrosAjustes = $this->linhas[86]->saldo_final_acumulado;
        if ($outrosAjustes >= 0) {
            $this->linhas[87]->saldo_final_acumulado += $outrosAjustes;
        } else {
            $this->linhas[87]->saldo_final_acumulado -= $outrosAjustes;
        }
    }

    /**
     * RESULTADO PRIMÁRIO (SEM RPPS) - Abaixo da Linha (LI) = (L) - (XXXVI - XXXVII)
     */
    protected function totalizaLinha88()
    {
        $this->linhas[88]->valor = $this->linhas[87]->saldo_final_acumulado - (
            $this->linhas[68]->saldo_final_acumulado - $this->linhas[69]->saldo_final_acumulado
        );

        $this->linhas[88]->saldo_final_acumulado = $this->linhas[88]->valor;
    }

    protected function addParserVariaveisFixas()
    {
        parent::addParserVariaveisFixas();

        $this->parser->setVariavel('resultado_nominal_sem_rpps_abaixo_da_linha', $this->linhas[79]->valor);
    }
}
