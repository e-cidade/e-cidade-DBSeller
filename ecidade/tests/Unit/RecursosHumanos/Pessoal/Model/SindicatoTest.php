<?php

namespace Tests\Unit\RecursosHumanos\Pessoal\Model;

use ECidade\RecursosHumanos\Pessoal\Model\Sindicato;
use Exception;
use Tests\TestCase;

class SindicatoTest extends TestCase
{
    /**
     * @var Sindicato
     */
    private $sindicato;

    public function testSequencialDeveSerInteiro()
    {
        $sequencial = (string)$this->faker->numberBetween(1, 10000);

        $this->sindicato->setSequencial($sequencial);
        $this->assertTrue(is_int($this->sindicato->getSequencial()));
        $this->assertTrue((int)$sequencial === $this->sindicato->getSequencial());

        $this->sindicato->setSequencial(null);
        $this->assertTrue(0 === $this->sindicato->getSequencial());

        $this->sindicato->setSequencial(false);
        $this->assertTrue(0 === $this->sindicato->getSequencial());
    }

    public function testCodigoDeveSerString()
    {
        $tipoSalario = $this->faker->numberBetween(1, 10000);

        $this->sindicato->setCodigo($tipoSalario);
        $this->assertTrue(is_string($this->sindicato->getCodigo()));
        $this->assertTrue((string)$tipoSalario === $this->sindicato->getCodigo());

        $this->sindicato->setCodigo(false);
        $this->assertTrue('' === $this->sindicato->getCodigo());

        $this->sindicato->setCodigo(null);
        $this->assertTrue('' === $this->sindicato->getCodigo());

        $this->sindicato->setCodigo(true);
        $this->assertTrue('1' === $this->sindicato->getCodigo());
    }

    public function testCnpjDeveSerString()
    {
        $tipoSalario = $this->faker->numberBetween(1, 10000);

        $this->sindicato->setCnpj($tipoSalario);
        $this->assertTrue(is_string($this->sindicato->getCnpj()));
        $this->assertTrue((string)$tipoSalario === $this->sindicato->getCnpj());

        $this->sindicato->setCnpj(false);
        $this->assertTrue('' === $this->sindicato->getCnpj());

        $this->sindicato->setCnpj(null);
        $this->assertTrue('' === $this->sindicato->getCnpj());

        $this->sindicato->setCnpj(true);
        $this->assertTrue('1' === $this->sindicato->getCnpj());
    }

    public function testRazaoSocialDeveSerString()
    {
        $tipoSalario = $this->faker->numberBetween(1, 10000);

        $this->sindicato->setRazaoSocial($tipoSalario);
        $this->assertTrue(is_string($this->sindicato->getRazaoSocial()));
        $this->assertTrue((string)$tipoSalario === $this->sindicato->getRazaoSocial());

        $this->sindicato->setRazaoSocial(false);
        $this->assertTrue('' === $this->sindicato->getRazaoSocial());

        $this->sindicato->setRazaoSocial(null);
        $this->assertTrue('' === $this->sindicato->getRazaoSocial());

        $this->sindicato->setRazaoSocial(true);
        $this->assertTrue('1' === $this->sindicato->getRazaoSocial());
    }

    public function testMesDataBaseDeveSerInteiro()
    {
        $ano = (string)$this->faker->year;

        $this->sindicato->setMesDataBase($ano);
        $this->assertTrue(is_int($this->sindicato->getMesDataBase()));
        $this->assertTrue((int)$ano === $this->sindicato->getMesDataBase());

        $this->sindicato->setMesDataBase(null);
        $this->assertTrue(0 === $this->sindicato->getMesDataBase());

        $this->sindicato->setMesDataBase(false);
        $this->assertTrue(0 === $this->sindicato->getMesDataBase());
    }

    /**
     * @throws Exception
     */
    public function testDeveCriarComRegistroDoBanco()
    {
        $sequencial = $this->faker->numberBetween(1, 10000);
        $mesDataBase = $this->faker->month;
        $codigo = $this->faker->numberBetween(1, 10000);
        $cnpj = $this->faker->numberBetween(1, 10000);
        $razaoSocial = $this->faker->company;

        $state = array(
            'rh116_sequencial' => $sequencial,
            'rh116_codigo' => $codigo,
            'rh116_cnpj' => $cnpj,
            'rh116_descricao' => $razaoSocial,
            'rh116_mesdatabase' => $mesDataBase
        );

        $sindicato = Sindicato::fromState($state);

        $this->assertEquals($sequencial, $sindicato->getSequencial());
        $this->assertEquals($mesDataBase, $sindicato->getMesDataBase());
        $this->assertEquals($codigo, $sindicato->getCodigo());
        $this->assertEquals($cnpj, $sindicato->getCnpj());
        $this->assertEquals($razaoSocial, $sindicato->getRazaoSocial());
    }

    public function testDeveTransformarEmArray()
    {
        $sequencial = $this->faker->numberBetween(1, 10000);
        $mesDataBase = $this->faker->month;
        $codigo = $this->faker->numberBetween(1, 10000);
        $cnpj = $this->faker->numberBetween(1, 10000);
        $razaoSocial = $this->faker->company;

        $this->sindicato->setSequencial($sequencial);
        $this->sindicato->setMesDataBase($mesDataBase);
        $this->sindicato->setCnpj($cnpj);
        $this->sindicato->setCodigo($codigo);
        $this->sindicato->setRazaoSocial($razaoSocial);

        $sindicato = $this->sindicato->toArray();

        $this->assertEquals(5, count($sindicato));

        $this->assertArrayHasKey('sequencial', $sindicato);
        $this->assertArrayHasKey('codigo', $sindicato);
        $this->assertArrayHasKey('razaoSocial', $sindicato);
        $this->assertArrayHasKey('cnpj', $sindicato);
        $this->assertArrayHasKey('mesDataBase', $sindicato);

        $this->assertEquals($sequencial, $sindicato['sequencial']);
        $this->assertEquals($mesDataBase, $sindicato['mesDataBase']);
        $this->assertEquals($cnpj, $sindicato['cnpj']);
        $this->assertEquals($codigo, $sindicato['codigo']);
        $this->assertEquals($razaoSocial, $sindicato['razaoSocial']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sindicato = new Sindicato();
    }
}
