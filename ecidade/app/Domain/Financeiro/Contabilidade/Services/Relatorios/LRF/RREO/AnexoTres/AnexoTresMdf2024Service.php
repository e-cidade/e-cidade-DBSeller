<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres;

class AnexoTresMdf2024Service extends AnexoTresMdf2023Service
{
    protected $sections = [
        'receitas' => [1, 34],
    ];

    protected $linhasSimplificadoCompleto = [
        29 => 'Receita Corrente Líquida',
        30 => '(-) Transferências obrigatórias da União relativas às emendas individuais (art. 166-A, § 1o, da CF)',
        31 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites de Endividamento',
        32 => '(-) Transferências obrigatórias da União relativas às emendas de bancada (art. 166, § 16, da CF)',
        33 => '(-) Transf. da União relativas à remuneração dos agentes comunitários de saúde e de combate às endemias',
        34 => '(-) Outras Deduções Constitucionais ou Legais',
        35 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites da Despesa com Pessoal',
    ];

    protected $linhasSimplificado = [
        29 => 'Receita Corrente Líquida',
        31 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites de Endividamento',
        35 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites da Despesa com Pessoal',
    ];

    protected function calcularLinhaRclAjustadaLimiteDespesaPessoal()
    {
        $this->subtraiLinha(35, [31, 32, 33, 34]);
    }
}
