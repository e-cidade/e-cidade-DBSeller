<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres;

/**
 * Class AnexoTresMdf2023Service
 * O Anexo III do exercício de 2023 tem o mais em comum com a versão de 2021 do que com 2022 por isso foi extendido
 * essa classe.
 */
class AnexoTresMdf2023Service extends AnexoTresMdfService
{
    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [
        1 => [3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23],
        2 => [3, 4, 5, 6, 7],
        9 => [10, 11],
        15 => [16, 17, 18, 19, 20, 21, 22],
        24 => [25, 26, 27, 28],
    ];

    protected $linhasSimplificadoCompleto = [
        29 => 'Receita Corrente Líquida',
        30 => '(-) Transferências obrigatórias da União relativas às emendas individuais (art. 166-A, § 1o, da CF)',
        31 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites de Endividamento',
        32 => '(-) Transferências obrigatórias da União relativas às emendas de bancada (art. 166, § 16, da CF)',
        33 => 'Receita Corrente Líquida Ajustada para Cálculo dos Limites da Despesa com Pessoal',
    ];

    protected $linhaDeducoes = 24;
}
