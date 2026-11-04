<?php

namespace Tests\Unit\Financeiro\Contabilidade\Lancamento;

use App\Domain\Financeiro\Contabilidade\Mappers\LancamentoManualAtributosMinimos;
use Tests\TestCase;

class LancamentoManualAtributosMinimosTest extends TestCase
{
    public function dataProvider()
    {
        $cases = [
            '111110100' => ['RECURSO'],
            '111111900' => ['RECURSO'],
            '113410101' => ['CGM', 'RECURSO'],
            '115610100' => ['RECURSO'],
            '122110107' => ['CGM', 'RECURSO'],
            '123110101' => ['RECURSO'],
            '211110101' => ['CGM', 'RECURSO'],
            '212310101' => ['CGM', 'RECURSO'],
            '213110101' => ['CGM', 'RECURSO'],
            '217919900' => ['CGM', 'RECURSO'],
            '218810102' => ['CGM', 'RECURSO'],
            '221110403' => ['CGM', 'RECURSO'],
            '237110100' => ['RECURSO'],
            '311110131' => ['RECURSO'],
            '411210200' => ['RECURSO'],
            '521110000' => ['RECEITA'],
            '521210100' => ['RECEITA'],
            '522110100' => ['DOTACAO'],
            '522130100' => ['DOTACAO'],
            '531100000' => ['EMPENHO'],
            '621100000' => ['RECEITA'],
            '622110000' => ['DOTACAO'],
            '622130100' => ['EMPENHO'],
            '631100000' => ['EMPENHO'],
            '711110105' => ['CGM', 'RECURSO'],
            '712210100' => ['CGM', 'RECURSO'],
            '721110000' => ['RECURSO'],
            '732110000' => ['CGM', 'RECURSO'],
            '741110000' => ['CGM', 'RECURSO'],
            '752000000' => ['CGM', 'RECURSO'],
            '791130000' => ['RECURSO'],
            '811110109' => ['CGM', 'RECURSO'],
            '811310102' => ['CGM', 'RECURSO'],
            '821110100' => ['RECURSO'],
            '821110200' => ['RECURSO'],
            '832310100' => ['CGM', 'RECURSO'],
            '852100000' => ['CGM', 'RECURSO'],
            '891290000' => ['RECURSO'],
        ];

        $testar = [];
        foreach ($cases as $estrutural => $math) {
            $testar[] = [$estrutural, $math];
        }

        return $testar;
    }

    /**
     * @dataProvider dataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testAtributos($estrutural, $esperado)
    {
        $this->assertEquals(LancamentoManualAtributosMinimos::atributo($estrutural), $esperado);
    }
}
