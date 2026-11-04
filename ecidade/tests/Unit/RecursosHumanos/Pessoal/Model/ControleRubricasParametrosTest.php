<?php

namespace Tests\Unit\RecursosHumanos\Pessoal\Model;

use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasParametros;
use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasParametrosRubricas;
use Instituicao;
use Selecao;
use Tests\TestCase;

class ControleRubricasParametrosTest extends TestCase
{
    /**
     * @var ControleRubricasParametros
     */
    private $ControleRubricasParametros;
    /**
     * @var ControleRubricasParametrosRubricas
     */
    private $ControleRubricasParametrosRubricas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ControleRubricasParametros = new ControleRubricasParametros();
    }

    public function testSequencialRetornarInteiro()
    {
        $sequencial = $this->faker->numberBetween(1, 10000);
        $this->ControleRubricasParametros->setSequencial($sequencial);
        self::assertTrue(is_int($this->ControleRubricasParametros->getSequencial()));
    }

    public function testInstituicaoRetornarObj()
    {
        $this->ControleRubricasParametros->setInstituicao(new Instituicao());
        self::assertInstanceOf(Instituicao::class, $this->ControleRubricasParametros->getInstituicao());
    }

    public function testSelecaoRetornarObj()
    {
        $this->ControleRubricasParametros->setSelecao(new Selecao());
        self::assertInstanceOf(Selecao::class, $this->ControleRubricasParametros->getSelecao());
    }

    public function testAnoRetornarInteiro()
    {
        $ano = (int) $this->faker->year;
        $this->ControleRubricasParametros->setAno($ano);
        self::assertTrue(is_int($this->ControleRubricasParametros->getAno()));
        self::assertTrue($ano === $this->ControleRubricasParametros->getAno());
    }

    public function testMesRetornarInteiro()
    {
        $mes = (int) $this->faker->month;
        $this->ControleRubricasParametros->setMes($mes);
        self::assertTrue(is_int($this->ControleRubricasParametros->getMes()));
        self::assertTrue($mes === $this->ControleRubricasParametros->getMes());
    }


    public function testAdicionaControleHorasExtrasRubricasRetornandoUmArray( )
    {
        $ControleRubricasParametrosRubricas = $this->entregaDadosControleHorasExtrasRubricasParaTest();
        $this->ControleRubricasParametros->addControleHorasExtrasRubricas($ControleRubricasParametrosRubricas);
        self::assertTrue(is_array($this->ControleRubricasParametros->getControleHorasExtrasRubricas()));
    }

    public function testFromStateRetornaUmObjetoComOsDadosQueForamMocados()
    {
        //faker dados
        $sequencial = $this->faker->numberBetween(1, 100000);
        $ano = $this->faker->year;
        $mes = $this->faker->month;

        // Dados
        $state = [
            'rh232_sequencial' => $sequencial,
            'rh232_ano' => $ano,
            'rh232_mes' => $mes
        ];

        $retorno = ControleRubricasParametros::fromState($state);

        // Teste de retorno
        self::assertInstanceOf(ControleRubricasParametros::class, $retorno);

        //Teste de igualdade de valor
        self::assertEquals($sequencial, $retorno->getSequencial());
        self::assertEquals($ano, $retorno->getAno());
        self::assertEquals($mes, $retorno->getMes());
    }



    // -------------------- DADOS --------------------
    public function entregaDadosControleHorasExtrasRubricasParaTest()
    {
        //instância o objeto que será passado para o método testAdicionaControleHorasExtrasRubricasRetornandoUmArray
        $this->ControleRubricasParametrosRubricas = new ControleRubricasParametrosRubricas();
        $sequencial = $this->faker->numberBetween(1, 10000);
        $controleHorasExtras = new ControleRubricasParametros();
        $instituicao = new Instituicao();
        $permiteExclusao = $this->faker->boolean;

        $this->ControleRubricasParametrosRubricas->setSequencial($sequencial);
        $this->ControleRubricasParametrosRubricas->setControleHorasExtras($controleHorasExtras);
        $this->ControleRubricasParametrosRubricas->setInstituicao($instituicao);
        $this->ControleRubricasParametrosRubricas->setPermiteExclusao($permiteExclusao);

        return $this->ControleRubricasParametrosRubricas;
    }

}
