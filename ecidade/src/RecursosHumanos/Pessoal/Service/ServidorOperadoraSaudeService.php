<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\Pessoal\Service;

use DBCompetencia;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOperadoraSaude;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOperadoraSaudeDependente;
use ECidade\RecursosHumanos\Pessoal\Repository\OperadoraSaudeRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeDependenteRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use Exception;
use Instituicao;
use Ponto;
use RubricaRepository;
use ServidorRepository;
use stdClass;

/**
 * Class ServidorOperadoraSaudeService
 *
 * @package ECidade\RecursosHumanos\Pessoal\Service
 */
class ServidorOperadoraSaudeService
{
    /**
     * @var ServidorOperadoraSaudeRepository
     */
    private $repositorio;

    /**
     * ServidorOperadoraSaudeService constructor.
     */
    public function __construct()
    {
        $this->repositorio = new ServidorOperadoraSaudeRepository();
    }

    /**
     * @param  stdClass      $parametros
     * @param  Instituicao   $instituicao
     * @param  DBCompetencia $competencia
     * @return ServidorOperadoraSaude
     * @throws Exception
     */
    public function salvar(stdClass $parametros, Instituicao $instituicao, DBCompetencia $competencia)
    {
        if (empty($parametros->servidor)) {
            throw new Exception('É necessário informar o servidor.');
        }

        if (empty($parametros->operadora)) {
            throw new Exception('É necessário informar a operadora de saúde.');
        }

        if (empty($parametros->rubrica)) {
            throw new Exception('É necessário informar a rubrica.');
        }

        if (!isset($parametros->valor)) {
            throw new Exception('É necessário informar o valor.');
        }

        $servidor = ServidorRepository::getInstanciaByCodigo(
            $parametros->servidor,
            $competencia->getAno(),
            $competencia->getMes(),
            $instituicao->getCodigo()
        );
        $operadora = OperadoraSaudeRepository::find($parametros->operadora);
        $pontoService = new PontoService();

        $repositorio = $this->repositorio->scopeServidor($servidor)
            ->scopeAno($competencia->getAno())
            ->scopeMes($competencia->getMes())
            ->scopeInstituicao($instituicao);

        if (!empty($parametros->sequencial)) {
            $repositorio->scopeSequencial($parametros->sequencial, '!=');

            $servidorOperadoraSaudeAnterior = ServidorOperadoraSaudeRepository::find($parametros->sequencial);
            $pontoService->removerRubrica($servidor, $servidorOperadoraSaudeAnterior->getRubrica(), Ponto::FIXO);
            $pontoService->removerRubrica($servidor, $servidorOperadoraSaudeAnterior->getRubrica(), Ponto::SALARIO);
        }

        $rubrica = RubricaRepository::getInstanciaByCodigo($parametros->rubrica);

        if ($repositorio->scopeRubrica($rubrica)->count()) {
            throw new Exception("Já existe um plano de saúde configurado com a rubrica {$rubrica->getCodigo()}.");
        }

        $existe = $repositorio->removeScope('rubrica')->scopeOperadoraSaude($operadora)->count();

        if ($existe) {
            $msg = "Já existe um vínculo do servidor com a operadora informada na competência";
            throw new Exception("{$msg} {$competencia->getMes()}/{$competencia->getAno()}.");
        }

        $servidorOperadoraSaude = new ServidorOperadoraSaude();
        $servidorOperadoraSaude->setServidor($servidor);
        $servidorOperadoraSaude->setOperadoraSaude($operadora);
        $servidorOperadoraSaude->setRubrica($rubrica);
        $servidorOperadoraSaude->setInstituicao($instituicao);
        $servidorOperadoraSaude->setValor($parametros->valor);
        $servidorOperadoraSaude->setAno($competencia->getAno());
        $servidorOperadoraSaude->setMes($competencia->getMes());
        $servidorOperadoraSaude->setSequencial($parametros->sequencial);

        $servidorOperadoraSaudeRepository = new ServidorOperadoraSaudeRepository();
        $servidorQuantidadeOperadora = $servidorOperadoraSaudeRepository
            ->scopeServidor($servidor)
            ->scopeAno($competencia->getAno())
            ->scopeMes($competencia->getMes())
            ->count();
        //eSocial - Evento S-1210 v1.2
        if ($servidorQuantidadeOperadora > 99) {
            $msg = "A quantidade de operadora de saúde para este servidor ultrapassa a 99 na competência";
            throw new Exception("{$msg} {$competencia->getMes()}/{$competencia->getAno()}. Favor Revisar.");
        }
        $servidorOperadoraSaude = $servidorOperadoraSaudeRepository->save($servidorOperadoraSaude);

        $valor = $this->calcularTotalDesconto(
            $servidorOperadoraSaude,
            $servidorOperadoraSaude->getRubrica()->getCodigo()
        );
        if ($valor > 0) {
            $pontoService->adicionarRubrica($servidor, $rubrica, Ponto::FIXO, $valor, 1);
            $pontoService->adicionarRubrica($servidor, $rubrica, Ponto::SALARIO, $valor, 1);
        }

        return $servidorOperadoraSaude;
    }

    /**
     * @param  ServidorOperadoraSaude $servidorOperadoraSaude
     * @return ServidorOperadoraSaudeDependente|float|mixed
     * @throws Exception
     */
    public function calcularTotalDesconto(ServidorOperadoraSaude $servidorOperadoraSaude, $codigoRubrica)
    {
        $dependentes =  $servidorOperadoraSaude->getServidorOperadoraSaudeDependente();
        if (empty($dependentes)) {
            $dependentes = $this->dependentes($servidorOperadoraSaude);
        }
        $valorDependentes =
            array_reduce(
                $dependentes,
                function ($a, ServidorOperadoraSaudeDependente $b) use ($codigoRubrica) {
                    if ($b->getRubrica()->getCodigo() == $codigoRubrica) {
                        return $a + $b->getValor();
                    }
                    return $a;
                },
                0
            );
        if ($servidorOperadoraSaude->getRubrica()->getCodigo() == $codigoRubrica) {
            $valorDependentes += $servidorOperadoraSaude->getValor();
        }

        return $valorDependentes;
    }

    /**
     * @param  stdClass $parametros
     * @throws Exception
     */
    public function excluir(stdClass $parametros)
    {
        if (empty($parametros->sequencial)) {
            throw new Exception('É necessário informar o código sequencial.');
        }

        $servidorOperadoraSaude = ServidorOperadoraSaudeRepository::find($parametros->sequencial);
        $servidorOperadoraSaudeDependenteRepository = new ServidorOperadoraSaudeDependenteRepository();
        $servidorOperadoraSaudeDependenteRepository->scopeServidorOperadoraSaude($servidorOperadoraSaude)->delete();

        $this->repositorio->delete($servidorOperadoraSaude);

        $servidor = $servidorOperadoraSaude->getServidor();
        $rubrica = $servidorOperadoraSaude->getRubrica();

        $pontoService = new PontoService();
        $pontoService->removerRubrica($servidor, $rubrica, Ponto::FIXO);
        $pontoService->removerRubrica($servidor, $rubrica, Ponto::SALARIO);
    }

    /**
     * @param  ServidorOperadoraSaude $servidorOperadoraSaude
     * @return ServidorOperadoraSaudeDependente[]
     * @throws Exception
     */
    public function dependentes(ServidorOperadoraSaude $servidorOperadoraSaude)
    {
        $repositorio = new ServidorOperadoraSaudeDependenteRepository();
        return $repositorio->scopeServidorOperadoraSaude($servidorOperadoraSaude)->get();
    }

    /**
     * @param  DBCompetencia $competenciaAtual
     * @param  DBCompetencia $competenciaNova
     * @param  Instituicao   $instituicao
     * @throws Exception
     */
    public function virarCompetencia(
        DBCompetencia $competenciaAtual,
        DBCompetencia $competenciaNova,
        Instituicao $instituicao
    ) {
        $servidores = $this->repositorio
            ->scopeAno($competenciaAtual->getAno())
            ->scopeMes($competenciaAtual->getMes())
            ->scopeInstituicao($instituicao)->get();

        foreach ($servidores as $servidorOperadoraSaude) {
            $parametros = new stdClass();
            $parametros->servidor = $servidorOperadoraSaude->getServidor()->getMatricula();
            $parametros->operadora = $servidorOperadoraSaude->getOperadoraSaude()->getSequencial();
            $parametros->rubrica = $servidorOperadoraSaude->getRubrica()->getCodigo();
            $parametros->valor = $servidorOperadoraSaude->getValor();

            $novoServidorOperadoraSaude = $this->salvar(
                $parametros,
                $servidorOperadoraSaude->getInstituicao(),
                $competenciaNova
            );
            $this->virarCompetenciaDependentes($servidorOperadoraSaude, $novoServidorOperadoraSaude);
        }
    }

    /**
     * @param  ServidorOperadoraSaude $servidorOperadoraSaude
     * @param  ServidorOperadoraSaude $novoServidorOperadoraSaude
     * @throws Exception
     */
    private function virarCompetenciaDependentes(
        ServidorOperadoraSaude $servidorOperadoraSaude,
        ServidorOperadoraSaude $novoServidorOperadoraSaude
    ) {
        $dependentes = $this->dependentes($servidorOperadoraSaude);

        $serviceDependente = new ServidorOperadoraSaudeDependenteService();
        foreach ($dependentes as $dependente) {
            $parametros = new stdClass();
            $parametros->codigoDependente = $dependente->getDependente()->getCodigo();
            $parametros->tipoDependente = $dependente->getTipo();
            $parametros->codigoPlanoSaudeServidor = $novoServidorOperadoraSaude->getSequencial();
            $parametros->valor = $dependente->getValor();
            $parametros->rubricaDependente = $dependente->getRubrica()->getCodigo();
            $serviceDependente->salvar($parametros);
        }
    }

    /**
     * @param  DBCompetencia $competenciaAtual
     * @throws Exception
     */
    public function voltarCompetencia(DBCompetencia $competenciaAtual, Instituicao $instituicao)
    {
        $servidores = $this->repositorio
            ->scopeMes($competenciaAtual->getMes())
            ->scopeAno($competenciaAtual->getAno())
            ->scopeInstituicao($instituicao)
            ->get();
        $this->repositorio->resetScopes();

        foreach ($servidores as $servidorOperadoraSaude) {
            $parametros = new stdClass();
            $parametros->sequencial = $servidorOperadoraSaude->getSequencial();

            $this->excluir($parametros);
        }
    }
}
