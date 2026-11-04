<?php


namespace Tests\Unit\RecursosHumanos\Pessoal\Model;


use Servidor;
use Tests\TestCase;

class ServidorTest extends TestCase
{
    /**
     * @var Servidor
     */
    private $servidor;

    protected function setUp(): void
    {
        $this->servidor = new Servidor(null,2019, 10, 1);
    }

    public function testDeveBuscarPorcentagemHorasMensais()
    {
        $this->servidor->setHorasMensais(120);
        self::assertEquals(30, $this->servidor->getPorcentagemHorasMensais());
        self::assertEquals(60, $this->servidor->getPorcentagemHorasMensais(0.50));
        self::assertEquals(120, $this->servidor->getPorcentagemHorasMensais(1));
        self::assertEquals(0, $this->servidor->getPorcentagemHorasMensais(0));
    }

    public function testDeveRetornarZeroAoBuscarPercentualHorasMensaisVazio()
    {
        $this->assertEquals(0, $this->servidor->getPorcentagemHorasMensais());
    }
}
