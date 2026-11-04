<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm;

class AnexoUmMdf2024Service extends AnexoUmMdfService
{
    protected $sections = [
        'despesas' => [1, 18],
        'limite_legal' => [19, 28],
    ];

    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [
        1 => [3, 4, 6, 7, 8, 9],
        2 => [3, 4],
        5 => [6, 7],
        10 => [11, 12, 13, 14, 15, 16, 17],
    ];

    protected $linhasSimplificado = [
    ];

    /**
     * Mapeia as linhas do relatório que não deve executar o cálculo do período.
     * Ou seja, linhas totalizadoras ou que devemos processar na mão
     *
     * - Cálculo do período é o processamento dos balancetes no período selecionado.
     *
     * @var int[]
     */
    protected $linhasNaoProcessar = [1, 2, 5, 10, 18, 19, 24, 25, 26, 27, 28];

    /**
     * Mapeia as linhas do relatório que não deve executar mensalmente
     * @var int[]
     */
    protected $linhasNaoProcessarMensal = [1, 2, 5, 10, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];

    /**
     * Usado para saber quais as linhas devem ser processadas por: calcularTotalUltimos12Meses
     * @var int[]
     */
    protected $linhasMensais = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];

    protected $linhasProcessarDespesaDesdobramento = [3, 4, 6, 7, 8];

    protected function posTotalizarLinhas()
    {
        // 18 DESPESA LÍQUIDA COM PESSOAL (III) = (I - II)
        $this->calculaLinhaSubtracao(18, 1, 10);
    }

    /**
     * Calcula as linhas
     * 19 -> RECEITA CORRENTE LÍQUIDA - RCL (IV)
     * 20 -> (-) Transferências ... emendas individuais
     * 21 -> (-) Transferências ... emendas de bancada
     * 22 -> (-) Transferências ... comunitários de saúde e de combate às endemias
     * 23 -> (-) Outras Deduções Constitucionais ou Legais
     * 24 -> RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL (VII)= (IV-V-VI)
     * @return void
     * @throws \Exception
     */
    protected function processarLinhasRCL()
    {
        $service = $this->getServiceRCL();
        $valores = $service->processaLinhasSimplificadoCompleto();

        $valorRCL = $valores[0]->ate_bimestre;
        $valorRCLAjustada = $valores[6]->ate_bimestre;
        // 19 RECEITA CORRENTE LÍQUIDA - RCL (IV)
        $this->linhas[19]->total_meses = $valorRCL;
        $this->linhas[19]->percentual = '-';
        // 20 (-) Transferências obrigatórias da União relativas às emendas individuais
        $this->linhas[20]->total_meses = $valores[1]->ate_bimestre;
        // 21 (-) Transferências obrigatórias da União relativas às emendas de bancada
        $this->linhas[21]->total_meses = $valores[3]->ate_bimestre;

        // 22 (-) Transf. da União relativas à remuneração dos agentes comunitários de saúde e de combate às endemias
        $this->linhas[22]->total_meses = $valores[4]->ate_bimestre;
        // 23 (-) Outras Deduções Constitucionais ou Legais
        $this->linhas[23]->total_meses = $valores[5]->ate_bimestre;
        // calcula o percentual em cima da rcl das linhas 17 e 18
        $this->calculaPercentualRCL($this->linhas[20], $valorRCLAjustada);
        $this->calculaPercentualRCL($this->linhas[21], $valorRCLAjustada);
        $this->calculaPercentualRCL($this->linhas[22], $valorRCLAjustada);
        $this->calculaPercentualRCL($this->linhas[23], $valorRCLAjustada);

        // 24 RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL
        $this->linhas[24]->total_meses = $valorRCLAjustada;
        $this->linhas[24]->percentual = '-';

        // 25 DESPESA TOTAL COM PESSOAL - DTP (VIII) = (III a + III b)
        $inscricao_menos_anulacao_rp_nao_processado = $this->linhas[18]->inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[25]->total_meses = $this->linhas[18]->total_meses + $inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[25]->percentual = round($this->linhas[25]->total_meses / $this->linhas[24]->total_meses * 100, 2);

        // 26 LIMITE MÁXIMO (IX) (incisos I, II e III, art. 19 da LRF) é 54% da linha
        // RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL
        if ($this->selecionouCamanara) {
            $this->linhas[26]->percentual = 60;
            $this->linhas[26]->total_meses = round($this->linhas[24]->total_meses * 0.60, 2);
        } else {
            $this->linhas[26]->percentual = 54;
            $this->linhas[26]->total_meses = round($this->linhas[24]->total_meses * 0.54, 2);
        }

        //LIMITE PRUDENCIAL (X) = (0,95 x IX) (parágrafo único do art. 22 da LRF)
        $this->linhas[27]->total_meses = round($this->linhas[26]->total_meses * 0.95, 2);
        //LIMITE DE ALERTA (XI) = (0,90 x IX) (inciso II do §1º do art. 59 da LRF)
        $this->linhas[28]->total_meses = round($this->linhas[26]->total_meses * 0.90, 2);
        // calcula o percentual em cima da rcl ajustada das linhas 23 e 24
        $this->calculaPercentualRCL($this->linhas[27], $this->linhas[24]->total_meses);
        $this->calculaPercentualRCL($this->linhas[28], $this->linhas[24]->total_meses);
    }

    public function processaLinhasSimplificado()
    {
        $this->processar();

        $simplificado = new \stdClass();
        $simplificado->total_despesa_pessoal = $this->linhas[25]->total_meses;
        $simplificado->percentual_despesa_pessoal = $this->linhas[25]->percentual;
        $simplificado->total_limite_maximo = $this->linhas[26]->total_meses;
        $simplificado->percentual_limite_maximo = $this->linhas[26]->percentual;
        $simplificado->total_limite_prudencial = $this->linhas[27]->total_meses;
        $simplificado->percentual_limite_prudencial = $this->linhas[27]->percentual;
        $simplificado->total_limite_alerta = $this->linhas[28]->total_meses;
        $simplificado->percentual_limite_alerta = $this->linhas[28]->percentual;

        return $simplificado;
    }
}
