<?php

namespace Tests\Unit\RecursosHumanos\Service;

use DBCompetencia;
use ECidade\Core\Helpers\HourHelper;
use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasCalculoParametros;
use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasMatriculas;
use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasParametros;
use ECidade\RecursosHumanos\Pessoal\Model\ControleRubricasParametrosRubricas;
use ECidade\RecursosHumanos\Pessoal\Repository\ControleRubricasMatriculasRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ControleRubricasParametrosRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ControleRubricasParametrosRubricasRepository;
use ECidade\RecursosHumanos\Pessoal\Service\ControleRubricasCalculoService;
use Instituicao;
use Mockery;
use Mockery\Mock;
use Rubrica;
use Servidor;
use Tests\TestCase;

class ControleRubricasCalculoServiceTest extends TestCase
{
    /**
     * @var ControleRubricasCalculoService
     */
    private $service;

    /**
     * @var Instituicao
     */
    private $instituicao;

    /**
     * @var DBCompetencia
     */
    private $competencia;

    /**
     * @var Servidor
     */
    private $servidor;

    /**
     * @var Rubrica
     */
    private $rubrica;

    /**
     * @var ControleRubricasCalculoParametros
     */
    private $calculoParametros;
    /**
     * @var Mock|ControleRubricasMatriculasRepository
     */
    private $repositoryMatriculas;
    /**
     * @var Mock|ControleRubricasParametrosRepository
     */
    private $repositoryParametros;

    protected function setUp(): void
    {
        $this->instituicao = new Instituicao();
        $this->instituicao->setSequencial(1);
        $this->competencia = new DBCompetencia(2019, 10);
        $this->servidor = new Servidor(null, 2019, 10, $this->instituicao->getSequencial());
        $this->servidor->setMatricula(1234);
        $this->adicionaRubricasPontoServidor($this->servidor);
        $this->rubrica = new Rubrica();

        $this->repositoryMatriculas = Mockery::mock(ControleRubricasMatriculasRepository::class)->makePartial();

        $this->repositoryParametros = Mockery::mock(ControleRubricasParametrosRepository::class)->makePartial();

        $this->service = Mockery::mock(ControleRubricasCalculoService::class, [
            $this->repositoryMatriculas,
            $this->repositoryParametros,
            new ControleRubricasParametrosRubricasRepository(),
            new HourHelper()
        ])->makePartial();

        $this->calculoParametros = new ControleRubricasCalculoParametros(
            $this->instituicao,
            $this->competencia,
            $this->servidor,
            $this->rubrica,
            10,
            true,
            'pontofs'
        );

    }

    private function adicionaRubricasPontoServidor(Servidor $servidor)
    {
        $rubricaPontoFixo = new Rubrica();
        $rubricaPontoSalario = new Rubrica();
        $rubricaPontoAdiantamento = new Rubrica();

        $rubricaPontoFixo->setCodigo('0001');
        $rubricaPontoFixo->setTabelaServidor('pontofx');
        $rubricaPontoFixo->setQuantidadeAtualServidor(10);

        $rubricaPontoSalario->setCodigo('0001');
        $rubricaPontoSalario->setTabelaServidor('pontofs');
        $rubricaPontoSalario->setQuantidadeAtualServidor(10);

        $rubricaPontoAdiantamento->setCodigo('0001');
        $rubricaPontoAdiantamento->setTabelaServidor('pontofa');
        $rubricaPontoAdiantamento->setQuantidadeAtualServidor(10);

        $servidor->setRubricasPonto([$rubricaPontoFixo, $rubricaPontoSalario, $rubricaPontoAdiantamento]);
    }

    private function preparaRubricaParaSerAdicionada($codigoRubrica)
    {
        $controleRubricasParametrosRubricas = new ControleRubricasParametrosRubricas();
        $rubrica = new Rubrica();
        $rubrica->setCodigo($codigoRubrica);
        $controleRubricasParametrosRubricas->setRubrica($rubrica);
        $this->service
            ->shouldReceive('buscaConfiguracaoRubrica')
            ->andReturn($controleRubricasParametrosRubricas);
    }

    private function preparaConfiguracoesServidor($horasLiberadas)
    {
        $controleRubricasMatriculas = Mockery::mock(ControleRubricasMatriculas::class)->makePartial();
        $controleRubricasMatriculas->allows(['getHorasLiberadas' => $horasLiberadas]);
        $this->repositoryMatriculas->allows(['buscaConfiguracoesMatricula' => $controleRubricasMatriculas]);
        $this->service->shouldReceive('buscaServidorComRubricasPonto')->andReturn($this->servidor);
    }

    private function preparaConfiguracoesRubrica()
    {
        $controleRubricasParametros = new ControleRubricasParametros();

        $controleRubricasParametrosRubricas = new ControleRubricasParametrosRubricas();
        $rubrica = new Rubrica();
        $rubrica->setCodigo('0001');
        $controleRubricasParametrosRubricas->setRubrica($rubrica);

        $controleRubricasParametrosRubricas2 = new ControleRubricasParametrosRubricas();
        $rubrica2 = new Rubrica();
        $rubrica2->setCodigo('0002');
        $controleRubricasParametrosRubricas2->setRubrica($rubrica2);

        $controleRubricasParametrosRubricas3 = new ControleRubricasParametrosRubricas();
        $rubrica3 = new Rubrica();
        $rubrica3->setCodigo('0003');
        $controleRubricasParametrosRubricas3->setRubrica($rubrica3);

        $controleRubricasParametros->addControleHorasExtrasRubricas($controleRubricasParametrosRubricas);
        $controleRubricasParametros->addControleHorasExtrasRubricas($controleRubricasParametrosRubricas2);
        $controleRubricasParametros->addControleHorasExtrasRubricas($controleRubricasParametrosRubricas3);

        $this->repositoryParametros->allows()->buscarPorInstituicaoECompetencia($this->instituicao, $this->competencia)
            ->andReturn($controleRubricasParametros);
    }

    public function testDevePermitirInclusaoQuandoNaoHaConfiguracaoRubrica()
    {
        $this->service->allows()
            ->buscaConfiguracaoRubrica($this->instituicao, $this->competencia, $this->rubrica->getCodigo())
            ->andReturn(false);

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertTrue($permite);
    }

    public function testNaoDevePermitirInclusaoQuandoNaoHaConfiguracaoServidor()
    {
        $matricula = $this->servidor->getMatricula();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Servidor(a) [{$matricula}] n");
        $this->repositoryMatriculas->allows(['buscaConfiguracoesMatricula' => []]);
        $this->service->allows(['buscaConfiguracaoRubrica' => new ControleRubricasParametrosRubricas()]);
        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
    }

    public function testDevePermitirAlteracaoQuandoQuantidadeNaoUltrapassaConfiguracao()
    {
        $this->preparaConfiguracoesServidor('30:00');
        $this->preparaConfiguracoesRubrica();
        $this->preparaRubricaParaSerAdicionada('0001');

        $this->calculoParametros->setIsAlteracao(true);
        $this->calculoParametros->setQuantidadeAdicionada(10);
        $this->calculoParametros->setTabela('pontofs');

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertTrue($permite);
    }

    public function testNaoDevePermitirAlteracaoQuandoQuantidadeUltrapassaConfiguracao()
    {
        $this->preparaConfiguracoesServidor('30:00');
        $this->preparaConfiguracoesRubrica();
        $this->preparaRubricaParaSerAdicionada('0001');

        $this->calculoParametros->setIsAlteracao(true);
        $this->calculoParametros->setQuantidadeAdicionada(11);
        $this->calculoParametros->setTabela('pontofs');

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertFalse($permite);
    }

    public function testNaoDevePermitirInclusaoQuandoQuantidadeUltrapassaConfiguracao()
    {
        $this->preparaConfiguracoesServidor('40:00');
        $this->preparaConfiguracoesRubrica();
        $this->preparaRubricaParaSerAdicionada('0010');

        $this->calculoParametros->setIsAlteracao(false);
        $this->calculoParametros->setQuantidadeAdicionada(21);
        $this->calculoParametros->setTabela('pontofs');

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertFalse($permite);
    }

    public function testDevePermitirInclusaoQuandoQuantidadeNaoUltrapassaConfiguracao()
    {
        $this->preparaConfiguracoesServidor('40:00');
        $this->preparaConfiguracoesRubrica();
        $this->preparaRubricaParaSerAdicionada('0010');

        $this->calculoParametros->setIsAlteracao(false);
        $this->calculoParametros->setQuantidadeAdicionada(20);
        $this->calculoParametros->setTabela('pontofs');

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertFalse($permite);
    }

    public function testDeveCalcularHorasQuebradas()
    {
        $this->preparaConfiguracoesServidor('40:00');
        $this->preparaConfiguracoesRubrica();
        $this->preparaRubricaParaSerAdicionada('0001');

        $this->calculoParametros->setIsAlteracao(true);
        $this->calculoParametros->setQuantidadeAdicionada(19.5);
        $this->calculoParametros->setTabela('pontofs');

        $permite = $this->service->verificaInclusaoRubricaServidor($this->calculoParametros);
        self::assertTrue($permite);
    }
}
