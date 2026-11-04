<?php

namespace Tests\Unit\Financeiro\Contabilidade\Estrutural;

use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use Tests\TestCase;

class EstruturalReceitaPadraoTest extends TestCase
{
    public function estruturalDataProvider()
    {
        $cases = [
            // estruturais da União
            '1.0.0.0.00.0.0' =>
                (object)[
                    'ate_nivel' => '1',
                    'sem_mascara' => '10000000000000',
                    'com_mascara' => '1.0.0.0.00.0.0.00.00.00',
                    'nivel' => 1,
                    'estrutural_pai' => false,
                ],
            '1.1.0.0.00.0.0' =>
                (object)[
                    'ate_nivel' => '11',
                    'sem_mascara' => '11000000000000',
                    'com_mascara' => '1.1.0.0.00.0.0.00.00.00',
                    'nivel' => 2,
                    'estrutural_pai' => '1.0.0.0.00.0.0.00.00.00',
                ],
            '1.1.1.0.00.0.0' =>
                (object)[
                    'ate_nivel' => '111',
                    'sem_mascara' => '11100000000000',
                    'com_mascara' => '1.1.1.0.00.0.0.00.00.00',
                    'nivel' => 3,
                    'estrutural_pai' => '1.1.0.0.00.0.0.00.00.00',
                ],
            '1.1.1.1.00.0.0' =>
                (object)[
                    'ate_nivel' => '1111',
                    'sem_mascara' => '11110000000000',
                    'com_mascara' => '1.1.1.1.00.0.0.00.00.00',
                    'nivel' => 4,
                    'estrutural_pai' => '1.1.1.0.00.0.0.00.00.00',
                ],
            '1.1.1.1.01.0.0' =>
                (object)[
                    'ate_nivel' => '111101',
                    'sem_mascara' => '11110100000000',
                    'com_mascara' => '1.1.1.1.01.0.0.00.00.00',
                    'nivel' => 5,
                    'estrutural_pai' => '1.1.1.1.00.0.0.00.00.00',
                ],
            '1.1.1.1.01.0.1' =>
                (object)[
                    'ate_nivel' => '11110101',
                    'sem_mascara' => '11110101000000',
                    'com_mascara' => '1.1.1.1.01.0.1.00.00.00',
                    'nivel' => 7,
                    'estrutural_pai' => '1.1.1.1.01.0.0.00.00.00',
                ],
            '1.1.1.1.01.0.2' =>
                (object)[
                    'ate_nivel' => '11110102',
                    'sem_mascara' => '11110102000000',
                    'com_mascara' => '1.1.1.1.01.0.2.00.00.00',
                    'nivel' => 7,
                    'estrutural_pai' => '1.1.1.1.01.0.0.00.00.00',
                ],
            '1.1.1.2.01.0.0' =>
                (object)[
                    'ate_nivel' => '111201',
                    'sem_mascara' => '11120100000000',
                    'com_mascara' => '1.1.1.2.01.0.0.00.00.00',
                    'nivel' => 5,
                    'estrutural_pai' => '1.1.1.2.00.0.0.00.00.00',
                ],
            '1.1.1.2.01.1.0' =>
                (object)[
                    'ate_nivel' => '1112011',
                    'sem_mascara' => '11120110000000',
                    'com_mascara' => '1.1.1.2.01.1.0.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '1.1.1.2.01.0.0.00.00.00',
                ],
            '1.1.1.2.01.1.1' =>
                (object)[
                    'ate_nivel' => '11120111',
                    'sem_mascara' => '11120111000000',
                    'com_mascara' => '1.1.1.2.01.1.1.00.00.00',
                    'nivel' => 7,
                    'estrutural_pai' => '1.1.1.2.01.1.0.00.00.00',
                ],
            // estrutural RS
            '1.0.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '1',
                'sem_mascara' => '10000000000000',
                'com_mascara' => '1.0.0.0.00.0.0.00.00.00',
                'nivel' => 1,
                'estrutural_pai' => false,
            ],
            '1.1.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '11',
                'sem_mascara' => '11000000000000',
                'com_mascara' => '1.1.0.0.00.0.0.00.00.00',
                'nivel' => 2,
                'estrutural_pai' => '1.0.0.0.00.0.0.00.00.00',
            ],
            '1.1.1.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '111',
                'sem_mascara' => '11100000000000',
                'com_mascara' => '1.1.1.0.00.0.0.00.00.00',
                'nivel' => 3,
                'estrutural_pai' => '1.1.0.0.00.0.0.00.00.00',
            ],
            '1.1.1.2.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '1112',
                'sem_mascara' => '11120000000000',
                'com_mascara' => '1.1.1.2.00.0.0.00.00.00',
                'nivel' => 4,
                'estrutural_pai' => '1.1.1.0.00.0.0.00.00.00',
            ],
            '1.1.1.2.01.0.0.00.00.00' => (object)[
                'ate_nivel' => '111201',
                'sem_mascara' => '11120100000000',
                'com_mascara' => '1.1.1.2.01.0.0.00.00.00',
                'nivel' => 5,
                'estrutural_pai' => '1.1.1.2.00.0.0.00.00.00',
            ],
            '1.1.1.3.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '1113',
                'sem_mascara' => '11130000000000',
                'com_mascara' => '1.1.1.3.00.0.0.00.00.00',
                'nivel' => 4,
                'estrutural_pai' => '1.1.1.0.00.0.0.00.00.00',
            ],
            '1.1.1.3.03.0.0.00.00.00' => (object)[
                'ate_nivel' => '111303',
                'sem_mascara' => '11130300000000',
                'com_mascara' => '1.1.1.3.03.0.0.00.00.00',
                'nivel' => 5,
                'estrutural_pai' => '1.1.1.3.00.0.0.00.00.00',
            ],
            '1.1.1.3.03.1.0.00.00.00' => (object)[
                'ate_nivel' => '1113031',
                'sem_mascara' => '11130310000000',
                'com_mascara' => '1.1.1.3.03.1.0.00.00.00',
                'nivel' => 6,
                'estrutural_pai' => '1.1.1.3.03.0.0.00.00.00',
            ],
            '1.1.1.3.03.1.X.00.00.00' => (object)[
                'ate_nivel' => '1113031X',
                'sem_mascara' => '1113031X000000',
                'com_mascara' => '1.1.1.3.03.1.X.00.00.00',
                'nivel' => 7,
                'estrutural_pai' => '1.1.1.3.03.1.0.00.00.00',
            ],
            '1.1.1.3.03.1.X.01.00.00' => (object)[
                'ate_nivel' => '1113031X01',
                'sem_mascara' => '1113031X010000',
                'com_mascara' => '1.1.1.3.03.1.X.01.00.00',
                'nivel' => 8,
                'estrutural_pai' => '1.1.1.3.03.1.X.00.00.00',
            ],
            '1.1.1.3.03.1.X.01.01.00' => (object)[
                'ate_nivel' => '1113031X0101',
                'sem_mascara' => '1113031X010100',
                'com_mascara' => '1.1.1.3.03.1.X.01.01.00',
                'nivel' => 9,
                'estrutural_pai' => '1.1.1.3.03.1.X.01.00.00',
            ],
            '1.1.1.3.03.1.X.01.02.00' => (object)[
                'ate_nivel' => '1113031X0102',
                'sem_mascara' => '1113031X010200',
                'com_mascara' => '1.1.1.3.03.1.X.01.02.00',
                'nivel' => 9,
                'estrutural_pai' => '1.1.1.3.03.1.X.01.00.00',
            ],
            '7.0.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '7',
                'sem_mascara' => '70000000000000',
                'com_mascara' => '7.0.0.0.00.0.0.00.00.00',
                'nivel' => 1,
                'estrutural_pai' => false
            ],
            '7.1.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '71',
                'sem_mascara' => '71000000000000',
                'com_mascara' => '7.1.0.0.00.0.0.00.00.00',
                'nivel' => 2,
                'estrutural_pai' => '7.0.0.0.00.0.0.00.00.00'
            ],
            '7.1.1.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '711',
                'sem_mascara' => '71100000000000',
                'com_mascara' => '7.1.1.0.00.0.0.00.00.00',
                'nivel' => 3,
                'estrutural_pai' => '7.1.0.0.00.0.0.00.00.00'
            ],
            '7.1.1.2.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '7112',
                'sem_mascara' => '71120000000000',
                'com_mascara' => '7.1.1.2.00.0.0.00.00.00',
                'nivel' => 4,
                'estrutural_pai' => '7.1.1.0.00.0.0.00.00.00'
            ],
            '7.1.1.2.01.0.0.00.00.00' => (object)[
                'ate_nivel' => '711201',
                'sem_mascara' => '71120100000000',
                'com_mascara' => '7.1.1.2.01.0.0.00.00.00',
                'nivel' => 5,
                'estrutural_pai' => '7.1.1.2.00.0.0.00.00.00'
            ],
            '7.1.1.2.50.0.0.00.00.00' => (object)[
                'ate_nivel' => '711250',
                'sem_mascara' => '71125000000000',
                'com_mascara' => '7.1.1.2.50.0.0.00.00.00',
                'nivel' => 5,
                'estrutural_pai' => '7.1.1.2.00.0.0.00.00.00'
            ],
            '9.0.0.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '9',
                'sem_mascara' => '900000000000000',
                'com_mascara' => '9.0.0.0.0.00.0.0.00.00.00',
                'nivel' => 1,
                'estrutural_pai' => false
            ],
            '9.1.0.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '91',
                'sem_mascara' => '910000000000000',
                'com_mascara' => '9.1.0.0.0.00.0.0.00.00.00',
                'nivel' => 2,
                'estrutural_pai' => '9.0.0.0.0.00.0.0.00.00.00'
            ],
            '9.1.1.0.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '911',
                'sem_mascara' => '911000000000000',
                'com_mascara' => '9.1.1.0.0.00.0.0.00.00.00',
                'nivel' => 3,
                'estrutural_pai' => '9.1.0.0.0.00.0.0.00.00.00'
            ],
            '9.1.1.1.0.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '9111',
                'sem_mascara' => '911100000000000',
                'com_mascara' => '9.1.1.1.0.00.0.0.00.00.00',
                'nivel' => 4,
                'estrutural_pai' => '9.1.1.0.0.00.0.0.00.00.00'
            ],
            '9.1.1.1.2.00.0.0.00.00.00' => (object)[
                'ate_nivel' => '91112',
                'sem_mascara' => '911120000000000',
                'com_mascara' => '9.1.1.1.2.00.0.0.00.00.00',
                'nivel' => 5,
                'estrutural_pai' => '9.1.1.1.0.00.0.0.00.00.00'
            ],
            '9.1.1.1.2.01.0.0.00.00.00' => (object)[
                'ate_nivel' => '9111201',
                'sem_mascara' => '911120100000000',
                'com_mascara' => '9.1.1.1.2.01.0.0.00.00.00',
                'nivel' => 6,
                'estrutural_pai' => '9.1.1.1.2.00.0.0.00.00.00'
            ],
            '9.1.1.1.2.50.0.0.00.00.00' => (object)[
                'ate_nivel' => '9111250',
                'sem_mascara' => '911125000000000',
                'com_mascara' => '9.1.1.1.2.50.0.0.00.00.00',
                'nivel' => 6,
                'estrutural_pai' => '9.1.1.1.2.00.0.0.00.00.00'
            ],
            '9.1.1.1.2.53.0.0.00.00.00' => (object)[
                'ate_nivel' => '9111253',
                'sem_mascara' => '911125300000000',
                'com_mascara' => '9.1.1.1.2.53.0.0.00.00.00',
                'nivel' => 6,
                'estrutural_pai' => '9.1.1.1.2.00.0.0.00.00.00'
            ],
            '9.1.1.1.3.03.1.0.00.00.00' => (object)[
                'ate_nivel' => '91113031',
                'sem_mascara' => '911130310000000',
                'com_mascara' => '9.1.1.1.3.03.1.0.00.00.00',
                'nivel' => 7,
                'estrutural_pai' => '9.1.1.1.3.03.0.0.00.00.00'
            ],
        ];

        $testar = [];
        foreach ($cases as $estrutural => $math) {
            $testar[] = [$estrutural, $math];
        }

        return $testar;
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testNivel($estrutural, $esperado)
    {
        $formater = new EstruturalReceitaPadrao($estrutural);
        $this->assertEquals($formater->getNivel(), $esperado->nivel);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualSemMascara($estrutural, $esperado)
    {
        $formater = new EstruturalReceitaPadrao($estrutural);
        $this->assertEquals($formater->getEstrutural(), $esperado->sem_mascara);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualComMascara($estrutural, $esperado)
    {
        $formater = new EstruturalReceitaPadrao($estrutural);
        $this->assertEquals($formater->getEstruturalComMascara(), $esperado->com_mascara);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualPai($estrutural, $esperado)
    {
        $formater = new EstruturalReceitaPadrao($estrutural);
        $estruturalPai = $formater->getEstruturalPai();

        if (is_bool($estruturalPai)) {
            $this->assertFalse($estruturalPai);
        } else {
            $this->assertInstanceOf(EstruturalReceitaPadrao::class, $estruturalPai);
            $this->assertEquals($estruturalPai->getEstruturalComMascara(), $esperado->estrutural_pai);
        }
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualAteNivel($estrutural, $esperado)
    {
        $formater = new EstruturalReceitaPadrao($estrutural);
        $this->assertEquals($formater->getEstruturalAteNivel(), $esperado->ate_nivel);
    }
}
