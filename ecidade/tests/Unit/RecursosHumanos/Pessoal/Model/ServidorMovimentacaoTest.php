<?php

namespace Tests\Unit\RecursosHumanos\Pessoal\Model;

use DateTime;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorMovimentacao;
use Exception;
use Instituicao;
use Tests\TestCase;

class ServidorMovimentacaoTest extends TestCase
{
    /**
     * @var ServidorMovimentacao
     */
    private $servidorMovimentacao;

    public function testInstituicaoDeveSerInstanciaDeInstituicao()
    {
        $this->servidorMovimentacao->setInstituicao(new Instituicao());
        $this->assertInstanceOf(Instituicao::class, $this->servidorMovimentacao->getInstituicao());
    }

    public function testSequencialDeveSerInteiro()
    {
        $sequencial = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setSequencial($sequencial);
        $this->assertTrue(is_int($this->servidorMovimentacao->getSequencial()));
        $this->assertTrue((int)$sequencial === $this->servidorMovimentacao->getSequencial());

        $this->servidorMovimentacao->setSequencial(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getSequencial());

        $this->servidorMovimentacao->setSequencial(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getSequencial());
    }

    public function testAnoDeveSerInteiro()
    {
        $ano = (string)$this->faker->year;

        $this->servidorMovimentacao->setAno($ano);
        $this->assertTrue(is_int($this->servidorMovimentacao->getAno()));
        $this->assertTrue((int)$ano === $this->servidorMovimentacao->getAno());

        $this->servidorMovimentacao->setAno(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getAno());

        $this->servidorMovimentacao->setAno(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getAno());
    }

    public function testMesDeveSerInteiro()
    {
        $mes = (string)$this->faker->month;

        $this->servidorMovimentacao->setMes($mes);
        $this->assertTrue(is_int($this->servidorMovimentacao->getMes()));
        $this->assertTrue((int)$mes === $this->servidorMovimentacao->getMes());

        $this->servidorMovimentacao->setMes(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getMes());

        $this->servidorMovimentacao->setMes(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getMes());
    }

    public function testMatriculaDeveSerInteiro()
    {
        $matricula = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setMatricula($matricula);
        $this->assertTrue(is_int($this->servidorMovimentacao->getMatricula()));
        $this->assertTrue((int)$matricula === $this->servidorMovimentacao->getMatricula());

        $this->servidorMovimentacao->setMatricula(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getMatricula());

        $this->servidorMovimentacao->setMatricula(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getMatricula());
    }

    public function testRegimeDeveSerInteiro()
    {
        $regime = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setRegime($regime);
        $this->assertTrue(is_int($this->servidorMovimentacao->getRegime()));
        $this->assertTrue((int)$regime === $this->servidorMovimentacao->getRegime());

        $this->servidorMovimentacao->setRegime(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getRegime());

        $this->servidorMovimentacao->setRegime(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getRegime());
    }

    public function testTipoSalarioDeveSerString()
    {
        $tipoSalario = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setTipoSalario($tipoSalario);
        $this->assertTrue(is_string($this->servidorMovimentacao->getTipoSalario()));
        $this->assertTrue((string)$tipoSalario === $this->servidorMovimentacao->getTipoSalario());

        $this->servidorMovimentacao->setTipoSalario(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getTipoSalario());

        $this->servidorMovimentacao->setTipoSalario(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getTipoSalario());

        $this->servidorMovimentacao->setTipoSalario(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getTipoSalario());
    }

    public function testFolhaDeveSerString()
    {
        $folha = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setFolha($folha);
        $this->assertTrue(is_string($this->servidorMovimentacao->getFolha()));
        $this->assertTrue((string)$folha === $this->servidorMovimentacao->getFolha());

        $this->servidorMovimentacao->setFolha(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getFolha());

        $this->servidorMovimentacao->setFolha(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getFolha());

        $this->servidorMovimentacao->setFolha(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getFolha());
    }

    public function testFormaPagamentoDeveSerInteiro()
    {
        $formaPagamento = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setFormaPagamento($formaPagamento);
        $this->assertTrue(is_int($this->servidorMovimentacao->getFormaPagamento()));
        $this->assertTrue((int)$formaPagamento === $this->servidorMovimentacao->getFormaPagamento());

        $this->servidorMovimentacao->setFormaPagamento(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getFormaPagamento());

        $this->servidorMovimentacao->setFormaPagamento(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getFormaPagamento());
    }

    public function testTabelaCalculoPrevidenciaDeveSerInteiro()
    {
        $tabelaCalculoPrevidencia = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setTabelaCalculoPrevidencia($tabelaCalculoPrevidencia);
        $this->assertTrue(is_int($this->servidorMovimentacao->getTabelaCalculoPrevidencia()));
        $this->assertTrue(
            (int)$tabelaCalculoPrevidencia === $this->servidorMovimentacao->getTabelaCalculoPrevidencia()
        );

        $this->servidorMovimentacao->setTabelaCalculoPrevidencia(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTabelaCalculoPrevidencia());

        $this->servidorMovimentacao->setTabelaCalculoPrevidencia(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTabelaCalculoPrevidencia());
    }

    public function testHorasSemanaisDeveSerDouble()
    {
        $horasSemanais = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setHorasSemanais($horasSemanais);
        $this->assertTrue(is_double($this->servidorMovimentacao->getHorasSemanais()));
        $this->assertTrue((double)$horasSemanais === $this->servidorMovimentacao->getHorasSemanais());
    }

    public function testHorasMensaisDeveSerDouble()
    {
        $horasMensais = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setHorasMensais($horasMensais);
        $this->assertTrue(is_double($this->servidorMovimentacao->getHorasMensais()));
        $this->assertTrue((double)$horasMensais === $this->servidorMovimentacao->getHorasMensais());
    }

    public function testAgentesNocivosDeveSerString()
    {
        $agentesNocivos = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setAgentesNocivos($agentesNocivos);
        $this->assertTrue(is_string($this->servidorMovimentacao->getAgentesNocivos()));
        $this->assertTrue((string)$agentesNocivos === $this->servidorMovimentacao->getAgentesNocivos());

        $this->servidorMovimentacao->setAgentesNocivos(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getAgentesNocivos());

        $this->servidorMovimentacao->setAgentesNocivos(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getAgentesNocivos());

        $this->servidorMovimentacao->setAgentesNocivos(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getAgentesNocivos());
    }

    public function testRecebeComplementacaoSalarialDeveSerBoolean()
    {
        $this->servidorMovimentacao->setRecebeComplementacaoSalarial('true');
        $this->assertTrue(is_bool($this->servidorMovimentacao->isRecebeComplementacaoSalarial()));
        $this->assertTrue($this->servidorMovimentacao->isRecebeComplementacaoSalarial());

        $this->servidorMovimentacao->setRecebeComplementacaoSalarial(1);
        $this->assertTrue($this->servidorMovimentacao->isRecebeComplementacaoSalarial());

        $this->servidorMovimentacao->setRecebeComplementacaoSalarial(0);
        $this->assertFalse($this->servidorMovimentacao->isRecebeComplementacaoSalarial());

        $this->servidorMovimentacao->setRecebeComplementacaoSalarial(null);
        $this->assertFalse($this->servidorMovimentacao->isRecebeComplementacaoSalarial());
    }

    public function testTipoContratoDeveSerInteiro()
    {
        $tipoContrato = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setTipoContrato($tipoContrato);
        $this->assertTrue(is_int($this->servidorMovimentacao->getTipoContrato()));
        $this->assertTrue((int)$tipoContrato === $this->servidorMovimentacao->getTipoContrato());

        $this->servidorMovimentacao->setTipoContrato(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTipoContrato());

        $this->servidorMovimentacao->setTipoContrato(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTipoContrato());
    }

    public function testVinculoDeveSerInteiro()
    {
        $vinculo = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setVinculo($vinculo);
        $this->assertTrue(is_int($this->servidorMovimentacao->getVinculo()));
        $this->assertTrue((int)$vinculo === $this->servidorMovimentacao->getVinculo());

        $this->servidorMovimentacao->setVinculo(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getVinculo());

        $this->servidorMovimentacao->setVinculo(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getVinculo());
    }

    public function testSalarioDeveSerDouble()
    {
        $salario = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setSalario($salario);
        $this->assertTrue(is_double($this->servidorMovimentacao->getSalario()));
        $this->assertTrue((double)$salario === $this->servidorMovimentacao->getSalario());
    }

    public function testLotacaoDeveSerInteiro()
    {
        $lotacao = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setLotacao($lotacao);
        $this->assertTrue(is_int($this->servidorMovimentacao->getLotacao()));
        $this->assertTrue((int)$lotacao === $this->servidorMovimentacao->getLotacao());

        $this->servidorMovimentacao->setLotacao(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getLotacao());

        $this->servidorMovimentacao->setLotacao(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getLotacao());
    }

    public function testFuncaoDeveSerInteiro()
    {
        $funcao = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setFuncao($funcao);
        $this->assertTrue(is_int($this->servidorMovimentacao->getFuncao()));
        $this->assertTrue((int)$funcao === $this->servidorMovimentacao->getFuncao());

        $this->servidorMovimentacao->setFuncao(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getFuncao());

        $this->servidorMovimentacao->setFuncao(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getFuncao());
    }

    public function testTipoAposentadoriaPensaoDeveSerInteiro()
    {
        $tipoAposentadoriaPensao = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setTipoAposentadoriaPensao($tipoAposentadoriaPensao);
        $this->assertTrue(is_string($this->servidorMovimentacao->getTipoAposentadoriaPensao()));
        $this->assertTrue($tipoAposentadoriaPensao === $this->servidorMovimentacao->getTipoAposentadoriaPensao());

        $this->servidorMovimentacao->setTipoAposentadoriaPensao(null);
        $this->assertTrue(null === $this->servidorMovimentacao->getTipoAposentadoriaPensao());

        $this->servidorMovimentacao->setTipoAposentadoriaPensao(false);
        $this->assertTrue(false === $this->servidorMovimentacao->getTipoAposentadoriaPensao());
    }

    public function testValidadePensaoDeveSerInstanciaDeDateTime()
    {
        $validadePensao = $this->faker->dateTime;

        $this->servidorMovimentacao->setValidadePensao($validadePensao);
        $this->assertInstanceOf(DateTime::class, $this->servidorMovimentacao->getValidadePensao());
        $this->assertTrue(
            $validadePensao->getTimestamp() === $this->servidorMovimentacao->getValidadePensao()->getTimestamp()
        );
    }

    public function testDeficienteFisicoDeveSerBoolean()
    {
        $this->servidorMovimentacao->setDeficienteFisico('true');
        $this->assertTrue(is_bool($this->servidorMovimentacao->isDeficienteFisico()));
        $this->assertTrue($this->servidorMovimentacao->isDeficienteFisico());

        $this->servidorMovimentacao->setDeficienteFisico(1);
        $this->assertTrue($this->servidorMovimentacao->isDeficienteFisico());

        $this->servidorMovimentacao->setDeficienteFisico(0);
        $this->assertFalse($this->servidorMovimentacao->isDeficienteFisico());

        $this->servidorMovimentacao->setDeficienteFisico(null);
        $this->assertFalse($this->servidorMovimentacao->isDeficienteFisico());
    }

    public function testPortadorMolestiaDeveSerBoolean()
    {
        $this->servidorMovimentacao->setPortadorMolestia('true');
        $this->assertTrue(is_bool($this->servidorMovimentacao->isPortadorMolestia()));
        $this->assertTrue($this->servidorMovimentacao->isPortadorMolestia());

        $this->servidorMovimentacao->setPortadorMolestia(1);
        $this->assertTrue($this->servidorMovimentacao->isPortadorMolestia());

        $this->servidorMovimentacao->setPortadorMolestia(0);
        $this->assertFalse($this->servidorMovimentacao->isPortadorMolestia());

        $this->servidorMovimentacao->setPortadorMolestia(null);
        $this->assertFalse($this->servidorMovimentacao->isPortadorMolestia());
    }

    public function testDataLaudoMolestiaDeveSerInstanciaDeDateTime()
    {
        $dataLaudoMolestia = $this->faker->dateTime;

        $this->servidorMovimentacao->setDataLaudoMolestia($dataLaudoMolestia);
        $this->assertInstanceOf(DateTime::class, $this->servidorMovimentacao->getDataLaudoMolestia());
        $this->assertTrue(
            $dataLaudoMolestia->getTimestamp() === $this->servidorMovimentacao->getDataLaudoMolestia()->getTimestamp()
        );
    }

    public function testTipoDeficienciaDeveSerInteiro()
    {
        $tipoDeficiencia = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setTipoDeficiencia($tipoDeficiencia);
        $this->assertTrue(is_int($this->servidorMovimentacao->getTipoDeficiencia()));
        $this->assertTrue((int)$tipoDeficiencia === $this->servidorMovimentacao->getTipoDeficiencia());

        $this->servidorMovimentacao->setTipoDeficiencia(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTipoDeficiencia());

        $this->servidorMovimentacao->setTipoDeficiencia(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getTipoDeficiencia());
    }

    public function testPermanenciaAbonadaDeveSerBoolean()
    {
        $this->servidorMovimentacao->setPermanenciaAbonada('true');
        $this->assertTrue(is_bool($this->servidorMovimentacao->isPermanenciaAbonada()));
        $this->assertTrue($this->servidorMovimentacao->isPermanenciaAbonada());

        $this->servidorMovimentacao->setPermanenciaAbonada(1);
        $this->assertTrue($this->servidorMovimentacao->isPermanenciaAbonada());

        $this->servidorMovimentacao->setPermanenciaAbonada(0);
        $this->assertFalse($this->servidorMovimentacao->isPermanenciaAbonada());

        $this->servidorMovimentacao->setPermanenciaAbonada(null);
        $this->assertFalse($this->servidorMovimentacao->isPermanenciaAbonada());
    }

    public function testDiasGozoFeriasDeveSerInteiro()
    {
        $diasGozoFerias = (string)$this->faker->numberBetween(1, 30);

        $this->servidorMovimentacao->setDiasGozoFerias($diasGozoFerias);
        $this->assertTrue(is_int($this->servidorMovimentacao->getDiasGozoFerias()));
        $this->assertTrue((int)$diasGozoFerias === $this->servidorMovimentacao->getDiasGozoFerias());

        $this->servidorMovimentacao->setDiasGozoFerias(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getDiasGozoFerias());

        $this->servidorMovimentacao->setDiasGozoFerias(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getDiasGozoFerias());
    }

    public function testHorasDiariasDeveSerInteiro()
    {
        $horasDiarias = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setHorasDiarias($horasDiarias);
        $this->assertTrue(is_int($this->servidorMovimentacao->getHorasDiarias()));
        $this->assertTrue((int)$horasDiarias === $this->servidorMovimentacao->getHorasDiarias());

        $this->servidorMovimentacao->setHorasDiarias(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getHorasDiarias());

        $this->servidorMovimentacao->setHorasDiarias(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getHorasDiarias());
    }

    public function testOnusDeveSerString()
    {
        $onus = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setOnus($onus);
        $this->assertTrue(is_string($this->servidorMovimentacao->getOnus()));
        $this->assertTrue((string)$onus === $this->servidorMovimentacao->getOnus());

        $this->servidorMovimentacao->setOnus(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getOnus());

        $this->servidorMovimentacao->setOnus(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getOnus());

        $this->servidorMovimentacao->setOnus(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getOnus());
    }

    public function testRessarcimentoDeveSerString()
    {
        $ressarcimento = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setRessarcimento($ressarcimento);
        $this->assertTrue(is_string($this->servidorMovimentacao->getRessarcimento()));
        $this->assertTrue((string)$ressarcimento === $this->servidorMovimentacao->getRessarcimento());

        $this->servidorMovimentacao->setRessarcimento(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getRessarcimento());

        $this->servidorMovimentacao->setRessarcimento(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getRessarcimento());

        $this->servidorMovimentacao->setRessarcimento(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getRessarcimento());
    }

    public function testDataCedenciaDeveSerInstanciaDeDateTime()
    {
        $dataCedencia = $this->faker->dateTime;

        $this->servidorMovimentacao->setDataCedencia($dataCedencia);
        $this->assertInstanceOf(DateTime::class, $this->servidorMovimentacao->getDataCedencia());
        $this->assertTrue(
            $dataCedencia->getTimestamp() === $this->servidorMovimentacao->getDataCedencia()->getTimestamp()
        );
    }

    public function testCnpjCedenciaDeveSerString()
    {
        $cnpjCedencia = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setCnpjCedencia($cnpjCedencia);
        $this->assertTrue(is_string($this->servidorMovimentacao->getCnpjCedencia()));
        $this->assertTrue((string)$cnpjCedencia === $this->servidorMovimentacao->getCnpjCedencia());

        $this->servidorMovimentacao->setCnpjCedencia(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getCnpjCedencia());

        $this->servidorMovimentacao->setCnpjCedencia(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getCnpjCedencia());

        $this->servidorMovimentacao->setCnpjCedencia(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getCnpjCedencia());
    }

    public function testCedenciaDeveSerString()
    {
        $cedencia = $this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setCedencia($cedencia);
        $this->assertTrue(is_string($this->servidorMovimentacao->getCedencia()));
        $this->assertTrue((string)$cedencia === $this->servidorMovimentacao->getCedencia());

        $this->servidorMovimentacao->setCedencia(false);
        $this->assertTrue('' === $this->servidorMovimentacao->getCedencia());

        $this->servidorMovimentacao->setCedencia(null);
        $this->assertTrue('' === $this->servidorMovimentacao->getCedencia());

        $this->servidorMovimentacao->setCedencia(true);
        $this->assertTrue('1' === $this->servidorMovimentacao->getCedencia());
    }

    public function testRegimeJornadaTrabalhoDeveSerInteiro()
    {
        $regimeJornadaTrabalho = (string)$this->faker->numberBetween(1, 10000);

        $this->servidorMovimentacao->setRegimeJornadaTrabalho($regimeJornadaTrabalho);
        $this->assertTrue(is_int($this->servidorMovimentacao->getRegimeJornadaTrabalho()));
        $this->assertTrue((int)$regimeJornadaTrabalho === $this->servidorMovimentacao->getRegimeJornadaTrabalho());

        $this->servidorMovimentacao->setRegimeJornadaTrabalho(null);
        $this->assertTrue(0 === $this->servidorMovimentacao->getRegimeJornadaTrabalho());

        $this->servidorMovimentacao->setRegimeJornadaTrabalho(false);
        $this->assertTrue(0 === $this->servidorMovimentacao->getRegimeJornadaTrabalho());
    }

    /**
     * @throws Exception
     */
    public function testDeveCriarComRegistroDoBanco()
    {
        $sequencial = $this->faker->numberBetween(1, 10000);
        $ano = $this->faker->year;
        $mes = $this->faker->month;
        $matricula = $this->faker->numberBetween(1, 10000);
        $regime = $this->faker->numberBetween(1, 10000);
        $tipoSalario = $this->faker->numberBetween(1, 10000);
        $folha = $this->faker->numberBetween(1, 10000);
        $formaPagamento = $this->faker->numberBetween(1, 10000);
        $tabelaCalculoPrevidencia = $this->faker->numberBetween(1, 10000);
        $horasSemanais = $this->faker->numberBetween(1, 10000);
        $horasMensais = $this->faker->numberBetween(1, 10000);
        $agentesNocivos = $this->faker->numberBetween(1, 10000);
        $tipoContrato = $this->faker->numberBetween(1, 10000);
        $vinculo = $this->faker->numberBetween(1, 10000);
        $salario = $this->faker->numberBetween(1, 10000);
        $lotacao = $this->faker->numberBetween(1, 10000);
        $funcao = $this->faker->numberBetween(1, 10000);
        $tipoAposentadoriaPensao = $this->faker->numberBetween(1, 10000);
        $validadePensao = $this->faker->dateTime;
        $dataLaudoMolestia = $this->faker->dateTime;
        $tipoDeficiencia = $this->faker->numberBetween(1, 10000);
        $diasGozoFerias = $this->faker->numberBetween(1, 30);
        $horasDiarias = $this->faker->numberBetween(1, 10000);
        $onus = $this->faker->numberBetween(1, 10000);
        $ressarcimento = $this->faker->numberBetween(1, 10000);
        $dataCedencia = $this->faker->dateTime;
        $cnpjCedencia = $this->faker->numberBetween(1, 10000);
        $cedencia = $this->faker->numberBetween(1, 10000);
        $regimeJornadaTrabalho = $this->faker->numberBetween(1, 10000);
        $recebeComplementacaoSalarial = $this->faker->boolean;
        $deficienteFisico = $this->faker->boolean;
        $portadorMolestia = $this->faker->boolean;
        $permanenciaAbonada = $this->faker->boolean;
        $dataAbonoPermanencia = $this->faker->dateTime;

        $state = array(
            'rh02_seqpes' => $sequencial,
            'rh02_anousu' => $ano,
            'rh02_mesusu' => $mes,
            'rh02_regist' => $matricula,
            'rh02_codreg' => $regime,
            'rh02_tipsal' => $tipoSalario,
            'rh02_folha' => $folha,
            'rh02_fpagto' => $formaPagamento,
            'rh02_tbprev' => $tabelaCalculoPrevidencia,
            'rh02_hrsmen' => $horasMensais,
            'rh02_hrssem' => $horasSemanais,
            'rh02_ocorre' => $agentesNocivos,
            'rh02_equip' => $recebeComplementacaoSalarial ? 't' : 'f',
            'rh02_tpcont' => $tipoContrato,
            'rh02_vincrais' => $vinculo,
            'rh02_salari' => $salario,
            'rh02_lota' => $lotacao,
            'rh02_funcao' => $funcao,
            'rh02_rhtipoapos' => $tipoAposentadoriaPensao,
            'rh02_validadepensao' => $validadePensao->format('Y-m-d'),
            'rh02_deficientefisico' => $deficienteFisico ? 't' : 'f',
            'rh02_portadormolestia' => $portadorMolestia ? 't' : 'f',
            'rh02_datalaudomolestia' => $dataLaudoMolestia->format('Y-m-d'),
            'rh02_tipodeficiencia' => $tipoDeficiencia,
            'rh02_abonopermanencia' => $permanenciaAbonada ? 't' : 'f',
            'rh02_diasgozoferias' => $diasGozoFerias,
            'rh02_horasdiarias' => $horasDiarias,
            'rh02_onus' => $onus,
            'rh02_ressarcimento' => $ressarcimento,
            'rh02_datacedencia' => $dataCedencia->format('Y-m-d'),
            'rh02_cnpjcedencia' => $cnpjCedencia,
            'rh02_cedencia' => $cedencia,
            'rh02_regimejornadatrabalho' => $regimeJornadaTrabalho,
            'rh02_dataabonopermanencia' => $dataAbonoPermanencia->format('Y-m-d')
        );

        $servidorMovimentacao = ServidorMovimentacao::fromState($state);

        $this->assertEquals(null, $servidorMovimentacao->getInstituicao());
        $this->assertEquals($sequencial, $servidorMovimentacao->getSequencial());
        $this->assertEquals($ano, $servidorMovimentacao->getAno());
        $this->assertEquals($mes, $servidorMovimentacao->getMes());
        $this->assertEquals($matricula, $servidorMovimentacao->getMatricula());
        $this->assertEquals($regime, $servidorMovimentacao->getRegime());
        $this->assertEquals($tipoSalario, $servidorMovimentacao->getTipoSalario());
        $this->assertEquals($folha, $servidorMovimentacao->getFolha());
        $this->assertEquals($formaPagamento, $servidorMovimentacao->getFormaPagamento());
        $this->assertEquals($tabelaCalculoPrevidencia, $servidorMovimentacao->getTabelaCalculoPrevidencia());
        $this->assertEquals($horasSemanais, $servidorMovimentacao->getHorasSemanais());
        $this->assertEquals($horasMensais, $servidorMovimentacao->getHorasMensais());
        $this->assertEquals($agentesNocivos, $servidorMovimentacao->getAgentesNocivos());
        $this->assertEquals($tipoContrato, $servidorMovimentacao->getTipoContrato());
        $this->assertEquals($vinculo, $servidorMovimentacao->getVinculo());
        $this->assertEquals($salario, $servidorMovimentacao->getSalario());
        $this->assertEquals($lotacao, $servidorMovimentacao->getLotacao());
        $this->assertEquals($funcao, $servidorMovimentacao->getFuncao());
        $this->assertEquals($tipoAposentadoriaPensao, $servidorMovimentacao->getTipoAposentadoriaPensao());
        $this->assertEquals(
            $validadePensao->format('d/m/Y'),
            $servidorMovimentacao->getValidadePensao()->format('d/m/Y')
        );
        $this->assertEquals(
            $dataLaudoMolestia->format('d/m/Y'),
            $servidorMovimentacao->getDataLaudoMolestia()->format('d/m/Y')
        );
        $this->assertEquals($tipoDeficiencia, $servidorMovimentacao->getTipoDeficiencia());
        $this->assertEquals($diasGozoFerias, $servidorMovimentacao->getDiasGozoFerias());
        $this->assertEquals($horasDiarias, $servidorMovimentacao->getHorasDiarias());
        $this->assertEquals($onus, $servidorMovimentacao->getOnus());
        $this->assertEquals($ressarcimento, $servidorMovimentacao->getRessarcimento());
        $this->assertEquals(
            $dataCedencia->format('d/m/Y'),
            $servidorMovimentacao->getDataCedencia()->format('d/m/Y')
        );
        $this->assertEquals($cnpjCedencia, $servidorMovimentacao->getCnpjCedencia());
        $this->assertEquals($cedencia, $servidorMovimentacao->getCedencia());
        $this->assertEquals($regimeJornadaTrabalho, $servidorMovimentacao->getRegimeJornadaTrabalho());
        $this->assertEquals($recebeComplementacaoSalarial, $servidorMovimentacao->isRecebeComplementacaoSalarial());
        $this->assertEquals($deficienteFisico, $servidorMovimentacao->isDeficienteFisico());
        $this->assertEquals($portadorMolestia, $servidorMovimentacao->isPortadorMolestia());
        $this->assertEquals($permanenciaAbonada, $servidorMovimentacao->isPermanenciaAbonada());
        $this->assertEquals(
            $dataAbonoPermanencia->format('d/m/Y'),
            $servidorMovimentacao->getDataPermanenciaAbonada()->format('d/m/Y')
        );
    }

    public function testDeveTransformarEmArray()
    {
        $instituicao = new Instituicao();
        $sequencial = $this->faker->numberBetween(1, 10000);
        $ano = $this->faker->year;
        $mes = $this->faker->month;
        $matricula = $this->faker->numberBetween(1, 10000);
        $regime = $this->faker->numberBetween(1, 10000);
        $tipoSalario = $this->faker->numberBetween(1, 10000);
        $folha = $this->faker->numberBetween(1, 10000);
        $formaPagamento = $this->faker->numberBetween(1, 10000);
        $tabelaCalculoPrevidencia = $this->faker->numberBetween(1, 10000);
        $horasSemanais = $this->faker->numberBetween(1, 10000);
        $horasMensais = $this->faker->numberBetween(1, 10000);
        $agentesNocivos = $this->faker->numberBetween(1, 10000);
        $tipoContrato = $this->faker->numberBetween(1, 10000);
        $vinculo = $this->faker->numberBetween(1, 10000);
        $salario = $this->faker->numberBetween(1, 10000);
        $lotacao = $this->faker->numberBetween(1, 10000);
        $funcao = $this->faker->numberBetween(1, 10000);
        $tipoAposentadoriaPensao = $this->faker->numberBetween(1, 10000);
        $validadePensao = $this->faker->dateTime;
        $dataLaudoMolestia = $this->faker->dateTime;
        $tipoDeficiencia = $this->faker->numberBetween(1, 10000);
        $diasGozoFerias = $this->faker->numberBetween(1, 30);
        $horasDiarias = $this->faker->numberBetween(1, 10000);
        $onus = $this->faker->numberBetween(1, 10000);
        $ressarcimento = $this->faker->numberBetween(1, 10000);
        $dataCedencia = $this->faker->dateTime;
        $cnpjCedencia = $this->faker->numberBetween(1, 10000);
        $cedencia = $this->faker->numberBetween(1, 10000);
        $regimeJornadaTrabalho = $this->faker->numberBetween(1, 10000);
        $recebeComplementacaoSalarial = $this->faker->boolean;
        $deficienteFisico = $this->faker->boolean;
        $portadorMolestia = $this->faker->boolean;
        $permanenciaAbonada = $this->faker->boolean;
        $dataPermanenciaAbonada = $this->faker->dateTime;

        $this->servidorMovimentacao->setInstituicao($instituicao);
        $this->servidorMovimentacao->setSequencial($sequencial);
        $this->servidorMovimentacao->setAno($ano);
        $this->servidorMovimentacao->setMes($mes);
        $this->servidorMovimentacao->setMatricula($matricula);
        $this->servidorMovimentacao->setRegime($regime);
        $this->servidorMovimentacao->setTipoSalario($tipoSalario);
        $this->servidorMovimentacao->setFolha($folha);
        $this->servidorMovimentacao->setFormaPagamento($formaPagamento);
        $this->servidorMovimentacao->setTabelaCalculoPrevidencia($tabelaCalculoPrevidencia);
        $this->servidorMovimentacao->setHorasSemanais($horasSemanais);
        $this->servidorMovimentacao->setHorasMensais($horasMensais);
        $this->servidorMovimentacao->setAgentesNocivos($agentesNocivos);
        $this->servidorMovimentacao->setRecebeComplementacaoSalarial($recebeComplementacaoSalarial);
        $this->servidorMovimentacao->setTipoContrato($tipoContrato);
        $this->servidorMovimentacao->setVinculo($vinculo);
        $this->servidorMovimentacao->setSalario($salario);
        $this->servidorMovimentacao->setLotacao($lotacao);
        $this->servidorMovimentacao->setFuncao($funcao);
        $this->servidorMovimentacao->setTipoAposentadoriaPensao($tipoAposentadoriaPensao);
        $this->servidorMovimentacao->setValidadePensao($validadePensao);
        $this->servidorMovimentacao->setDeficienteFisico($deficienteFisico);
        $this->servidorMovimentacao->setPortadorMolestia($portadorMolestia);
        $this->servidorMovimentacao->setDataLaudoMolestia($dataLaudoMolestia);
        $this->servidorMovimentacao->setTipoDeficiencia($tipoDeficiencia);
        $this->servidorMovimentacao->setPermanenciaAbonada($permanenciaAbonada);
        $this->servidorMovimentacao->setDiasGozoFerias($diasGozoFerias);
        $this->servidorMovimentacao->setHorasDiarias($horasDiarias);
        $this->servidorMovimentacao->setOnus($onus);
        $this->servidorMovimentacao->setRessarcimento($ressarcimento);
        $this->servidorMovimentacao->setDataCedencia($dataCedencia);
        $this->servidorMovimentacao->setCnpjCedencia($cnpjCedencia);
        $this->servidorMovimentacao->setCedencia($cedencia);
        $this->servidorMovimentacao->setRegimeJornadaTrabalho($regimeJornadaTrabalho);
        $this->servidorMovimentacao->setDataPermanenciaAbonada($dataPermanenciaAbonada);

        $servidorMovimentacao = $this->servidorMovimentacao->toArray();

        $this->assertEquals(37, count($servidorMovimentacao));

        $this->assertArrayHasKey('instituicao', $servidorMovimentacao);
        $this->assertArrayHasKey('sequencial', $servidorMovimentacao);
        $this->assertArrayHasKey('ano', $servidorMovimentacao);
        $this->assertArrayHasKey('mes', $servidorMovimentacao);
        $this->assertArrayHasKey('matricula', $servidorMovimentacao);
        $this->assertArrayHasKey('regime', $servidorMovimentacao);
        $this->assertArrayHasKey('tipoSalario', $servidorMovimentacao);
        $this->assertArrayHasKey('folha', $servidorMovimentacao);
        $this->assertArrayHasKey('formaPagamento', $servidorMovimentacao);
        $this->assertArrayHasKey('tabelaCalculoPrevidencia', $servidorMovimentacao);
        $this->assertArrayHasKey('horasSemanais', $servidorMovimentacao);
        $this->assertArrayHasKey('horasMensais', $servidorMovimentacao);
        $this->assertArrayHasKey('agentesNocivos', $servidorMovimentacao);
        $this->assertArrayHasKey('recebeComplementacaoSalarial', $servidorMovimentacao);
        $this->assertArrayHasKey('tipoContrato', $servidorMovimentacao);
        $this->assertArrayHasKey('vinculo', $servidorMovimentacao);
        $this->assertArrayHasKey('salario', $servidorMovimentacao);
        $this->assertArrayHasKey('lotacao', $servidorMovimentacao);
        $this->assertArrayHasKey('funcao', $servidorMovimentacao);
        $this->assertArrayHasKey('tipoAposentadoriaPensao', $servidorMovimentacao);
        $this->assertArrayHasKey('validadePensao', $servidorMovimentacao);
        $this->assertArrayHasKey('deficienteFisico', $servidorMovimentacao);
        $this->assertArrayHasKey('portadorMolestia', $servidorMovimentacao);
        $this->assertArrayHasKey('dataLaudoMolestia', $servidorMovimentacao);
        $this->assertArrayHasKey('tipoDeficiencia', $servidorMovimentacao);
        $this->assertArrayHasKey('permanenciaAbonada', $servidorMovimentacao);
        $this->assertArrayHasKey('diasGozoFerias', $servidorMovimentacao);
        $this->assertArrayHasKey('horasDiarias', $servidorMovimentacao);
        $this->assertArrayHasKey('onus', $servidorMovimentacao);
        $this->assertArrayHasKey('ressarcimento', $servidorMovimentacao);
        $this->assertArrayHasKey('dataCedencia', $servidorMovimentacao);
        $this->assertArrayHasKey('cnpjCedencia', $servidorMovimentacao);
        $this->assertArrayHasKey('cedencia', $servidorMovimentacao);
        $this->assertArrayHasKey('regimeJornadaTrabalho', $servidorMovimentacao);
        $this->assertArrayHasKey('dataPermanenciaAbonada', $servidorMovimentacao);

        $this->assertEquals($instituicao->toArray(), $servidorMovimentacao['instituicao']);
        $this->assertEquals($sequencial, $servidorMovimentacao['sequencial']);
        $this->assertEquals($ano, $servidorMovimentacao['ano']);
        $this->assertEquals($mes, $servidorMovimentacao['mes']);
        $this->assertEquals($matricula, $servidorMovimentacao['matricula']);
        $this->assertEquals($regime, $servidorMovimentacao['regime']);
        $this->assertEquals($tipoSalario, $servidorMovimentacao['tipoSalario']);
        $this->assertEquals($folha, $servidorMovimentacao['folha']);
        $this->assertEquals($formaPagamento, $servidorMovimentacao['formaPagamento']);
        $this->assertEquals($tabelaCalculoPrevidencia, $servidorMovimentacao['tabelaCalculoPrevidencia']);
        $this->assertEquals($horasSemanais, $servidorMovimentacao['horasSemanais']);
        $this->assertEquals($horasMensais, $servidorMovimentacao['horasMensais']);
        $this->assertEquals($agentesNocivos, $servidorMovimentacao['agentesNocivos']);
        $this->assertEquals($tipoContrato, $servidorMovimentacao['tipoContrato']);
        $this->assertEquals($vinculo, $servidorMovimentacao['vinculo']);
        $this->assertEquals($salario, $servidorMovimentacao['salario']);
        $this->assertEquals($lotacao, $servidorMovimentacao['lotacao']);
        $this->assertEquals($funcao, $servidorMovimentacao['funcao']);
        $this->assertEquals($tipoAposentadoriaPensao, $servidorMovimentacao['tipoAposentadoriaPensao']);
        $this->assertEquals($validadePensao->format('d/m/Y'), $servidorMovimentacao['validadePensao']);
        $this->assertEquals($dataLaudoMolestia->format('d/m/Y'), $servidorMovimentacao['dataLaudoMolestia']);
        $this->assertEquals($tipoDeficiencia, $servidorMovimentacao['tipoDeficiencia']);
        $this->assertEquals($diasGozoFerias, $servidorMovimentacao['diasGozoFerias']);
        $this->assertEquals($horasDiarias, $servidorMovimentacao['horasDiarias']);
        $this->assertEquals($onus, $servidorMovimentacao['onus']);
        $this->assertEquals($ressarcimento, $servidorMovimentacao['ressarcimento']);
        $this->assertEquals($dataCedencia->format('d/m/Y'), $servidorMovimentacao['dataCedencia']);
        $this->assertEquals($cnpjCedencia, $servidorMovimentacao['cnpjCedencia']);
        $this->assertEquals($cedencia, $servidorMovimentacao['cedencia']);
        $this->assertEquals($regimeJornadaTrabalho, $servidorMovimentacao['regimeJornadaTrabalho']);
        $this->assertEquals($recebeComplementacaoSalarial, $servidorMovimentacao['recebeComplementacaoSalarial']);
        $this->assertEquals($deficienteFisico, $servidorMovimentacao['deficienteFisico']);
        $this->assertEquals($portadorMolestia, $servidorMovimentacao['portadorMolestia']);
        $this->assertEquals($permanenciaAbonada, $servidorMovimentacao['permanenciaAbonada']);
        $this->assertEquals($dataPermanenciaAbonada->format('d/m/Y'), $servidorMovimentacao['dataPermanenciaAbonada']);

    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->servidorMovimentacao = new ServidorMovimentacao();
    }
}
