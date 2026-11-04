<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres;

use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoTres;
use Exception;

class AnexoTresMdfService extends AnexoTresService
{
    protected $sections = [
        'receitas' => [1, 33],
    ];

    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [
        1 => [3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24],
        2 => [3, 4, 5, 6, 7],
        9 => [10, 11],
        15 => [16, 17, 18, 19, 20, 21, 22, 23, 24],
        25 => [26, 27, 28],
    ];

    protected $linhasSimplificado = [
        29 => 'Receita Corrente Líquida',
        31 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites de Endividamento',
        33 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites da Despesa com Pessoal',
    ];

    /**
     * Linhas que devem ser positivas mesmo que o sinal seja negativo.
     * @var int[]
     */
    protected $linhasAplicarAbs = [28];

    /**
     * Linhas que deve retornar para o Anexo I da RGF
     * @var int[]
     */
    protected $linhasApuracaoCumprimentoLegal = [
        29 => 'RCL',
        30 => 'Emenda individual',
        32 => 'Emenda de bancada'
    ];

    protected $linhaReceitasCorrentes = 1;
    protected $linhaDeducoes = 25;
    protected $linhaRcl = 29;

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
        $this->parser = new XlsAnexoTres($template);
        parent::__construct($filtros);
    }

    protected function posTotalizarLinhas()
    {
        // realiza as subtraçoes
        $this->calcularLinhaRCL();
        $this->calcularLinhaRclAjustadaLimiteEndividamento();
        $this->calcularLinhaRclAjustadaLimiteDespesaPessoal();
    }


    /**
     * Calculo da linha 29 RECEITA CORRENTE LÍQUIDA (III) = (I-II)
     * Como as deduções vem negativas, tive que dar um abs nos valores
     * @throws Exception
     */
    protected function calcularLinhaRCL()
    {
        $rcl = $this->linhas[$this->linhaRcl];
        $linhaReceita = $this->linhas[$this->linhaReceitasCorrentes];
        $linhaDeducoes = $this->linhas[$this->linhaDeducoes];
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($mesesProcessar as $mes) {
            $rcl->{$mes->coluna} = $linhaReceita->{$mes->coluna} - $linhaDeducoes->{$mes->coluna};
        }
        $rcl->total_meses = $linhaReceita->total_meses - $linhaDeducoes->total_meses;
        $rcl->previsao_atualizada = $linhaReceita->previsao_atualizada - $linhaDeducoes->previsao_atualizada;
    }

    protected function calcularLinhaRclAjustadaLimiteEndividamento()
    {
        $this->calculaLinhaSubtracao(31, 29, 30);
    }

    protected function calcularLinhaRclAjustadaLimiteDespesaPessoal()
    {
        $this->calculaLinhaSubtracao(33, 31, 32);
    }

    public function getLinhaEmendaIndividuais()
    {
        if (empty($this->linhas)) {
            $this->processar();
        }

        return $this->linhas[30];
    }
}
