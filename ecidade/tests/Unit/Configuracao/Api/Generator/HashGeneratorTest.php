<?php

namespace Tests\Unit\Configuracao\Api\Generator;

use ECidade\Configuracao\Api\Generator\HashGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class HashGeneratorTest
 * @package Tests\Unit\Configuracao\Api\Generator
 */
class HashGeneratorTest extends TestCase
{
    private $key;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = time() . '_KEY';
    }

    /**
     * @dataProvider provideData
     * @param array $data
     */
    public function testShouldEncryptData($data)
    {
        $hash = HashGenerator::encrypt($data, $this->key);
        self::assertNotNull($hash);
    }

    /**
     * @dataProvider provideData
     * @param array $data
     */
    public function testShouldDecryptData(array $data)
    {
        $hash = HashGenerator::encrypt($data, $this->key);
        $decrypted = HashGenerator::decrypt($hash, $this->key);

        self::assertEquals("john", $decrypted['name']);
        self::assertEquals(3000, $decrypted['i_love_u']);
    }

    public function testShouldDecryptDeepArray()
    {
        $data = array(
            'name' => 'John',
            'options' => array(
                'opt1' => 'аю',
                'opt2' => 'BB',
                0 => array(
                    'outro'
                )
            )
        );
        $hash = HashGenerator::encrypt($data, $this->key);
        $decrypted = HashGenerator::decrypt($hash, $this->key);

        self::assertNotEmpty($decrypted);
        self::assertEquals("John", $decrypted['name']);
        self::assertEquals('аю', $decrypted['options']->opt1);
        self::assertEquals('BB', $decrypted['options']->opt2);
        self::assertEquals('outro', $decrypted['options']->{0}[0]);
    }

    /**
     * Provides data
     * @return array
     */
    public function provideData()
    {
        return array(
            array(
                array(
                    'name' => 'john',
                    'i_love_u' => 3000
                )
            )
        );
    }
}
