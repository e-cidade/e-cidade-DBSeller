<?php

namespace Tests\Unit\Financeiro\Contabilidade\Msc;

use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\Lancamento as LancamentoRepository;
use Tests\TestCase;

class LancamentoRepositoryTest extends TestCase
{
    /**
     * @var LancamentoRepository
     */
    public $lancamentoRepository;

    public function setUp(): void
    {
        $this->lancamentoRepository = LancamentoRepository::getInstance();
        $this->lancamentoRepository->setDeParaPo(array(
            "0203" => "130001",
            '0201' => "101200",
            '2543' => '10131'
        ));

        $this->lancamentoRepository->setDeParaRecursos(array(
            "001" => "1001",
            '0040' => "1004",
            '050' => '050',
            '0108' => '11010',
        ));
    }

    /**
     * testa o de/para do Atributo PO
     */
    public function testDeParaAtributoPO()
    {
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('PO', '0201'), '101200');
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('PO', '0203'), '130001');
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('PO', '0302'), '0302');
    }

    /**
     * testa o de/para do Atributo PO
     */
    public function testDeParaAtributoFR()
    {
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('FR', '1001'), '1001');
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('FR', '1004'), '1004');
        $this->assertEquals($this->lancamentoRepository->transformarAtributosSiconfi('FR', '1500'), '1500');
    }
}
