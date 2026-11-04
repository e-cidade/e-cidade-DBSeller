<?php


namespace Tests\Unit\RecursosHumanos\Pessoal\Service;


use ECidade\Core\Helpers\HourHelper;
use ECidade\RecursosHumanos\Pessoal\Builders\ControleRubricasMatriculasBuilder;
use ECidade\RecursosHumanos\Pessoal\Repository\ControleRubricasMatriculasRepository;
use ECidade\RecursosHumanos\Pessoal\Service\ControleRubricasMatriculasService;
use Exception;
use Instituicao;
use Mockery;
use Mockery\Mock;
use Selecao;
use Servidor;
use Tests\TestCase;

class ControleRubricasMatriculasServiceTest extends TestCase
{
    /**
     * @var Mock|ControleRubricasMatriculasRepository
     */
    private $repository;

    /**
     * @var ControleRubricasMatriculasBuilder
     */
    private $builder;

    /**
     * @var Mock|ControleRubricasMatriculasService
     */
    private $service;

    /**
     * @var Instituicao
     */
    private $instituicao;

    /**
     * @var Selecao
     */
    private $selecao;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(ControleRubricasMatriculasRepository::class)->makePartial();
        $this->builder = new ControleRubricasMatriculasBuilder();
        $this->instituicao = new Instituicao();
        $this->selecao = new Selecao();
        $this->service = Mockery::mock(ControleRubricasMatriculasService::class, [
            $this->repository,
            $this->builder
        ])->makePartial();
        $this->preparaServidor();
        $this->bloqueiaCruds();
    }

    private function preparaServidor()
    {
        $servidor = new Servidor(null, 2019, 12, 1);
        $servidor->setMatricula(123456);
        $servidor->setHorasMensais(100);
        $this->service->shouldReceive('buscaServidor')->andReturn($servidor);
    }

    private function bloqueiaCruds()
    {
        $this->service->allows([
            'removerControleHorasExtrasMatricula' => null,
        ]);
    }

    public function testDeveRetornarOitentaHorasQuandoServidorEmSelecao()
    {
        $servidoresSelecao = [
            123456 => new Servidor(null, 2019, 12, 1),
            654987 => new Servidor(null, 2019, 12, 1)
        ];
        $this->service->shouldReceive('buscaServidorSelecao')->andReturn($servidoresSelecao);

        $horasLiberadas = $this->service->getHorasLiberadasParaServidor(
            $this->instituicao,
            new HourHelper(),
            $this->selecao,
            123456,
            2019,
            12
        );
        self::assertEquals('80:00', $horasLiberadas);
    }

    public function testDeveRetornarPorcentagemHorasMensaisQuandoServidorNaoEstaEmSelecao()
    {
        $servidoresSelecao = [
            111111 => new Servidor(null, 2019, 12, 1),
            654987 => new Servidor(null, 2019, 12, 1)
        ];
        $this->service->shouldReceive('buscaServidorSelecao')->andReturn($servidoresSelecao);

        $horasLiberadas = $this->service->getHorasLiberadasParaServidor(
            $this->instituicao,
            new HourHelper(),
            $this->selecao,
            123456,
            2019,
            12
        );
        self::assertEquals('25:00', $horasLiberadas);
    }

    public function testDeveBloquearInclusaoQuandoUltrapassaMaximoHorasConfiguradasServidor()
    {
        $horasConfiguradasServidor = '10:00';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("O limite m");
        $this->service->shouldReceive('getHorasLiberadasParaServidor')->andReturn($horasConfiguradasServidor);
        $this->service->salvarControleHorasExtrasMatricula(
            $this->instituicao,
            new HourHelper(),
            $this->selecao,
            123456,
            2019,
            12,
            '15:00'
        );
    }

    public function testDevePermitirInclusaoQuandoNaoUltrapassaMaximoHorasConfiguradasServidor()
    {
        $this->service->shouldReceive('getHorasLiberadasParaServidor')->andReturn('10:00');
        $this->repository->shouldReceive('save')->once()->andReturn(null);
        $this->service->salvarControleHorasExtrasMatricula(
            $this->instituicao,
            new HourHelper(),
            $this->selecao,
            123456,
            2019,
            12,
            '10:00'
        );

    }

    public function testDevePropagarACompetenciaAoSalvar()
    {
        $this->service->shouldReceive('getHorasLiberadasParaServidor')->andReturn('10:00');
        $this->repository->shouldReceive('save')->times(13)->andReturn(null);
        $this->service->salvaPropagaCompetencia(
            $this->instituicao,
            new HourHelper(),
            $this->selecao,
            123456,
            '10:00',
            2019,
            12,
            2020,
            12
        );


    }
}

























