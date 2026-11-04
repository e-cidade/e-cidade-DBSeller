<?php

namespace Tests\Unit\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Contracts\MedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Strategies\ProcedimentoBnafarStrategy;
use Illuminate\Support\Collection;
use Tests\TestCase;
use UnidadeProntoSocorro;

abstract class ProcedimentoBnafarStrategyTest extends TestCase
{
    /**
     * @var ProcedimentoBnafarStrategy
     */
    private $strategy;

    /**
     * @var MedicamentoBnafarRepository
     */
    private $repository;

    public function verificarInconsistenciasDataProvider()
    {
        $data = $this->buildData(true);
        $expected = $this->buildData(true);
        $expected->first()->erros = $this->buildExpectedErrors();
        $expected->first()->erro_bnafar = false;

        return [
            [$data, $expected],
            [collect([]), collect([])]
        ];
    }

    /**
     * @dataProvider verificarInconsistenciasDataProvider
     */
    public function testVerificarInconsistencias($data, $expected)
    {
        $periodo = [new \DateTime(), new \DateTime()];
        $this->repository->method('get')->willReturn($data);

        $inconsistencias = $this->strategy->verificarInconsistencias($periodo);

        self::assertEquals($expected, $inconsistencias);
    }

    public function processarDataProvider()
    {
        $data = $this->buildData();
        return [
            [$data, $this->buildExpected()[0]],
            [collect([]), null]
        ];
    }

    /**
     * @dataProvider processarDataProvider
     * @param Collection $data
     * @param object $expected
     */
    public function testProcessar(Collection $data, $expected)
    {
        $this->repository->method('get')->willReturn($data);

        if ($data->isEmpty()) {
            self::expectException(\Exception::class);
        }

        $procedimentoProcessado = $this->strategy->setCodigoMovimentacao(1)->processar();

        self::assertEquals($expected, $procedimentoProcessado);
    }

    public function processarLoteDataProvider()
    {
        $data = $this->buildData();
        return [
            [$data, $this->buildExpected()],
            [collect([]), null]
        ];
    }

    /**
     * @dataProvider processarLoteDataProvider
     * @param Collection $data
     * @param array $expected
     */
    public function testProcessarLote(Collection $data, $expected)
    {
        $this->repository->method('get')->willReturn($data);

        $periodo = [new \DateTime(), new \DateTime()];
        $loteProcessado = $this->strategy->processarLote($periodo);


        self::assertInstanceOf(\Generator::class, $loteProcessado);

        $loteProcessado = $loteProcessado->current();
        self::assertEquals($expected, $loteProcessado);
    }

    /**
     * @param $unidade
     * @param $repository
     * @return ProcedimentoBnafarStrategy
     */
    abstract protected function setUpConcreteStrategy($unidade, $repository);

    /**
     * @param boolean $testValidator
     * @return Collection
     */
    abstract protected function buildData($testValidator = false);

    /**
     * @return array
     */
    abstract protected function buildExpected();

    /**
     * @return array
     */
    abstract protected function buildExpectedErrors();

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(MedicamentoBnafarRepository::class)->getMock();
        $this->repository->method('getInconsistencias')->willReturn(collect([]));
        $this->repository->method('scopeUnidade')->willReturnSelf();
        $this->repository->method('scopeSomenteInconsistencias')->willReturnSelf();
        $this->repository->method('scopeSomentePendentes')->willReturnSelf();
        $this->repository->method('scopeEstoqueMovimentacao')->willReturnSelf();
        $this->repository->method('scopePeriodo')->willReturnSelf();

        $unidade = $this->getMockBuilder(UnidadeProntoSocorro::class)->getMock();
        $unidade->method('getCodigo')->willReturn(1);
        $unidade->method('getCNES')->willReturn(12345);

        $this->strategy = $this->setUpConcreteStrategy($unidade, $this->repository);
    }
}
