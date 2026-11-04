<?php

namespace Tests\Unit\Financeiro\Contabilidade\Estrutural;

use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalPcaspPadrao;
use Tests\TestCase;

class EstruturalPcaspPadraoTest extends TestCase
{
    public function estruturalDataProvider()
    {
        $cases = [
            '1.1.1.0.0.00.00' =>
                (object)[
                    'ate_nivel' => '111',
                    'sem_mascara' => '111000000000000',
                    'com_mascara' => '1.1.1.0.0.00.00.00.00.00',
                    'nivel' => 3,
                    'estrutural_pai' => '1.1.0.0.0.00.00.00.00.00',
                ],
            '1.1.1.1.0.00.00' =>
                (object)[
                    'ate_nivel' => '1111',
                    'sem_mascara' => '111100000000000',
                    'com_mascara' => '1.1.1.1.0.00.00.00.00.00',
                    'nivel' => 4,
                    'estrutural_pai' => '1.1.1.0.0.00.00.00.00.00',
                ],
            '1.1.1.1.1.00.00' =>
                (object)[
                    'ate_nivel' => '11111',
                    'sem_mascara' => '111110000000000',
                    'com_mascara' => '1.1.1.1.1.00.00.00.00.00',
                    'nivel' => 5,
                    'estrutural_pai' => '1.1.1.1.0.00.00.00.00.00',
                ],
            '1.1.1.1.1.01.00' =>
                (object)[
                    'ate_nivel' => '1111101',
                    'sem_mascara' => '111110100000000',
                    'com_mascara' => '1.1.1.1.1.01.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '1.1.1.1.1.00.00.00.00.00',
                ],
            '1.1.1.1.1.02.00' =>
                (object)[
                    'ate_nivel' => '1111102',
                    'sem_mascara' => '111110200000000',
                    'com_mascara' => '1.1.1.1.1.02.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '1.1.1.1.1.00.00.00.00.00',
                ],
            '1.1.1.1.1.06.00' =>
                (object)[
                    'ate_nivel' => '1111106',
                    'sem_mascara' => '111110600000000',
                    'com_mascara' => '1.1.1.1.1.06.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '1.1.1.1.1.00.00.00.00.00',
                ],
            '1.1.1.1.1.06.02' =>
                (object)[
                    'ate_nivel' => '111110602',
                    'sem_mascara' => '111110602000000',
                    'com_mascara' => '1.1.1.1.1.06.02.00.00.00',
                    'nivel' => 7,
                    'estrutural_pai' => '1.1.1.1.1.06.00.00.00.00',
                ],
            '1.1.1.1.1.06.03' =>
                (object)[
                    'ate_nivel' => '111110603',
                    'sem_mascara' => '111110603000000',
                    'com_mascara' => '1.1.1.1.1.06.03.00.00.00',
                    'nivel' => 7,
                    'estrutural_pai' => '1.1.1.1.1.06.00.00.00.00',
                ],
            '2.2.1.4.0.00.00' =>
                (object)[
                    'ate_nivel' => '2214',
                    'sem_mascara' => '221400000000000',
                    'com_mascara' => '2.2.1.4.0.00.00.00.00.00',
                    'nivel' => 4,
                    'estrutural_pai' => '2.2.1.0.0.00.00.00.00.00',
                ],
            '2.2.1.4.1.00.00' =>
                (object)[
                    'ate_nivel' => '22141',
                    'sem_mascara' => '221410000000000',
                    'com_mascara' => '2.2.1.4.1.00.00.00.00.00',
                    'nivel' => 5,
                    'estrutural_pai' => '2.2.1.4.0.00.00.00.00.00',
                ],
            '2.2.1.4.1.01.00' =>
                (object)[
                    'ate_nivel' => '2214101',
                    'sem_mascara' => '221410100000000',
                    'com_mascara' => '2.2.1.4.1.01.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '2.2.1.4.1.00.00.00.00.00',
                ],
            '2.2.1.4.1.02.00' =>
                (object)[
                    'ate_nivel' => '2214102',
                    'sem_mascara' => '221410200000000',
                    'com_mascara' => '2.2.1.4.1.02.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '2.2.1.4.1.00.00.00.00.00',
                ],
            '3.4.1.2.0.00.00' =>
                (object)[
                    'ate_nivel' => '3412',
                    'sem_mascara' => '341200000000000',
                    'com_mascara' => '3.4.1.2.0.00.00.00.00.00',
                    'nivel' => 4,
                    'estrutural_pai' => '3.4.1.0.0.00.00.00.00.00',
                ],
            '3.4.1.2.1.00.00' =>
                (object)[
                    'ate_nivel' => '34121',
                    'sem_mascara' => '341210000000000',
                    'com_mascara' => '3.4.1.2.1.00.00.00.00.00',
                    'nivel' => 5,
                    'estrutural_pai' => '3.4.1.2.0.00.00.00.00.00',
                ],
            '3.4.1.2.1.01.00' =>
                (object)[
                    'ate_nivel' => '3412101',
                    'sem_mascara' => '341210100000000',
                    'com_mascara' => '3.4.1.2.1.01.00.00.00.00',
                    'nivel' => 6,
                    'estrutural_pai' => '3.4.1.2.1.00.00.00.00.00',
                ],
            // uf
            '1.0.0.0.0.00.00.00.00.00' =>
                (object)[
                    'ate_nivel' => '1',
                    'sem_mascara' => '100000000000000',
                    'com_mascara' => '1.0.0.0.0.00.00.00.00.00',
                    'nivel' => 1,
                    'estrutural_pai' => '1.0.0.0.0.00.00.00.00.00',
                ],
            '1.1.0.0.0.00.00.00.00.00' =>
                (object)[
                    'ate_nivel' => '11',
                    'sem_mascara' => '110000000000000',
                    'com_mascara' => '1.1.0.0.0.00.00.00.00.00',
                    'nivel' => 2,
                    'estrutural_pai' => '1.0.0.0.0.00.00.00.00.00',
                ],
            '1.1.1.0.0.00.00.00.00.00' =>
                (object)[
                    'ate_nivel' => '111',
                    'sem_mascara' => '111000000000000',
                    'com_mascara' => '1.1.1.0.0.00.00.00.00.00',
                    'nivel' => 3,
                    'estrutural_pai' => '1.1.0.0.0.00.00.00.00.00',
                ],

            '8.3.2.3.1.01.01.01.00.00' =>
                (object)[
                    'ate_nivel' => '83231010101',
                    'sem_mascara' => '832310101010000',
                    'com_mascara' => '8.3.2.3.1.01.01.01.00.00',
                    'nivel' => 8,
                    'estrutural_pai' => '8.3.2.3.1.01.01.00.00.00',
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
        $formater = new EstruturalPcaspPadrao($estrutural);
        $this->assertEquals($formater->getNivel(), $esperado->nivel);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualSemMascara($estrutural, $esperado)
    {
        $formater = new EstruturalPcaspPadrao($estrutural);
        $this->assertEquals($formater->getEstrutural(), $esperado->sem_mascara);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualComMascara($estrutural, $esperado)
    {
        $formater = new EstruturalPcaspPadrao($estrutural);
        $this->assertEquals($formater->getEstruturalComMascara(), $esperado->com_mascara);
    }

    /**
     * @dataProvider estruturalDataProvider
     * @param string $estrutural
     * @param \stdClass $esperado
     */
    public function testEstrutualPai($estrutural, $esperado)
    {
        $formater = new EstruturalPcaspPadrao($estrutural);
        $estruturalPai = $formater->getEstruturalPai();

        if (is_bool($estruturalPai)) {
            $this->assertFalse($estruturalPai);
        } else {
            $this->assertInstanceOf(EstruturalPcaspPadrao::class, $estruturalPai);
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
        $formater = new EstruturalPcaspPadrao($estrutural);
        $this->assertEquals($formater->getEstruturalAteNivel(), $esperado->ate_nivel);
    }

}
