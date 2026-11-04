<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm;

use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RGF\XlsAnexoUm;
use Exception;

class AnexoUmMdfService extends AnexoUmService
{
    protected $sections = [
        'despesas' => [1, 15],
        'limite_legal' => [16, 23],
    ];

    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [
        1 => [3, 4, 6, 7, 8, 9],
        2 => [3, 4],
        5 => [6, 7],
        10 => [11, 12, 13, 14],
    ];

    protected $linhasSimplificado = [
    ];

    /**
     * Mapeia as linhas do relatório que não deve executar o cálculo do período.
     * Ou seja linhas totalizadoras ou que devemos processar na mão
     *
     * - Cálculo do período é o processamento dos balancetes no período selecionado.
     *
     * @var int[]
     */
    protected $linhasNaoProcessar = [1, 2, 5, 10, 15, 16, 17, 18, 19, 20, 21, 22, 23];

    /**
     * Mapeia as linhas do relatório que não deve executar mensalmente
     * @var int[]
     */
    protected $linhasNaoProcessarMensal = [3, 4, 6, 7, 16, 17, 18, 19, 20, 21, 22, 23];

    /**
     * Usado para saber quais as linhas devem ser processadas por: calcularTotalUltimos12Meses
     * @var int[]
     */
    protected $linhasMensais = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];

    protected $linhasProcessarDespesaDesdobramento = [3, 4, 6, 7, 8];

    /**
     * @var int[]
     */
    protected $linhasProcessarRP = [3, 4, 6, 7, 8, 9, 11, 12, 13, 14,];

    /**
     * @param $filtros
     * @throws Exception
     */
    public function __construct($filtros)
    {
        $template = TemplateFactory::getTemplate(
            $filtros['codigo_relatorio'],
            $filtros['periodo'],
            TemplateFactory::MODELO_MDF
        );

        $this->parser = new XlsAnexoUm($template);
        parent::__construct($filtros);
    }


    protected function processarPeriodo()
    {
        $this->processaLinhas($this->linhas);
    }

    protected function processarMensal()
    {
        $this->processaLinhasMensais($this->linhas);
        $this->processarLinhasDesdobramento();

        $this->processaRP();
    }

    protected function processarLinhasDesdobramento()
    {
        foreach ($this->linhas as $linha) {
            if (!in_array($linha->ordem, $this->linhasProcessarDespesaDesdobramento)) {
                continue;
            }

            $this->processaDespesaMensal($this->getDadosDespesaPorDesdobramento(), $linha);
        }
    }


    protected function posTotalizarLinhas()
    {
        // 15 DESPESA LÍQUIDA COM PESSOAL (III) = (I - II)
        $this->calculaLinhaSubtracao(15, 1, 10);
    }

    /**
     * Calcula o quado da Apuração do cumprimento do limite legal
     * @return void
     * @throws Exception
     */
    protected function processarLinhasRCL()
    {
        $service = $this->getServiceRCL();
        $valores = $service->getApuracaoCumprimentoLimiteLegal();

        $valorRCL = $valores[0]->total_meses;
        // 16 RECEITA CORRENTE LÍQUIDA - RCL (IV)
        $this->linhas[16]->total_meses = $valorRCL;
        $this->linhas[16]->percentual = '-';
        // 17 (-) Transferências obrigatórias da União relativas às emendas individuais (§ 13, art. 166 da CF) (V)
        $this->linhas[17]->total_meses = $valores[1]->total_meses;
        // 18 (-) Transferências obrigatórias da União relativas às emendas de bancada (art 166, § 16, da CF) (VI)
        $this->linhas[18]->total_meses = $valores[2]->total_meses;
        // calcula o percentual em cima da rcl das linhas 17 e 18
        $this->calculaPercentualRCL($this->linhas[17], $valorRCL);
        $this->calculaPercentualRCL($this->linhas[18], $valorRCL);

        // 19 RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL (VII)= (IV-V-VI)
        $this->linhas[19]->total_meses = $valorRCL - $this->linhas[17]->total_meses - $this->linhas[18]->total_meses;
        $this->linhas[19]->percentual = '-';

        // 20 DESPESA TOTAL COM PESSOAL - DTP (VIII) = (III a + III b)
        $inscricao_menos_anulacao_rp_nao_processado = $this->linhas[15]->inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[20]->total_meses = $this->linhas[15]->total_meses + $inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[20]->percentual = round($this->linhas[20]->total_meses / $this->linhas[19]->total_meses * 100, 2);

        // 21 LIMITE MÁXIMO (IX) (incisos I, II e III, art. 19 da LRF) é 54% da linha
        // RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL (VII)= (IV-V-VI)
        if ($this->selecionouCamanara) {
            $this->linhas[21]->percentual = 60;
            $this->linhas[21]->total_meses = round($this->linhas[19]->total_meses * 0.60, 2);
        } else {
            $this->linhas[21]->percentual = 54;
            $this->linhas[21]->total_meses = round($this->linhas[19]->total_meses * 0.54, 2);
        }

        //LIMITE PRUDENCIAL (X) = (0,95 x IX) (parágrafo único do art. 22 da LRF)
        $this->linhas[22]->total_meses = round($this->linhas[21]->total_meses * 0.95, 2);
        //LIMITE DE ALERTA (XI) = (0,90 x IX) (inciso II do §1º do art. 59 da LRF)
        $this->linhas[23]->total_meses = round($this->linhas[21]->total_meses * 0.90, 2);
        // calcula o percentual em cima da rcl ajustada das linhas 23 e 24
        $this->calculaPercentualRCL($this->linhas[22], $this->linhas[19]->total_meses);
        $this->calculaPercentualRCL($this->linhas[23], $this->linhas[19]->total_meses);
    }

    /**
     * retorna os dados das linhas simplificada
     * @return \stdClass
     */
    public function processaLinhasSimplificado()
    {
        $this->processar();

        $simplificado = new \stdClass();
        $simplificado->total_despesa_pessoal = $this->linhas[20]->total_meses;
        $simplificado->percentual_despesa_pessoal = $this->linhas[20]->percentual;
        $simplificado->total_limite_maximo = $this->linhas[21]->total_meses;
        $simplificado->percentual_limite_maximo = $this->linhas[21]->percentual;
        $simplificado->total_limite_prudencial = $this->linhas[22]->total_meses;
        $simplificado->percentual_limite_prudencial = $this->linhas[22]->percentual;
        $simplificado->total_limite_alerta = $this->linhas[23]->total_meses;
        $simplificado->percentual_limite_alerta = $this->linhas[23]->percentual;

        return $simplificado;
    }

    public function getLinhasProcessadas()
    {
        $this->processar();
        return $this->linhas;
    }
}
