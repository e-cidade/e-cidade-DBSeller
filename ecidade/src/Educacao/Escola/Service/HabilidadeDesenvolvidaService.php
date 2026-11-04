<?php


namespace ECidade\Educacao\Escola\Service;

use ECidade\Educacao\Escola\Model\ConteudoDesenvolvido;
use ECidade\Educacao\Escola\Model\HabilidadeDesenvolvida;
use ECidade\Educacao\Escola\Model\HabilidadeDesenvolvidaReferencial;
use ECidade\Educacao\Escola\Registry\ConteudoDesenvolvidoRegistry;
use ECidade\Educacao\Escola\Repository\HabilidadeDesenvolvidaReferencialRepository;
use ECidade\Educacao\Escola\Repository\HabilidadeDesenvolvidaRepository;
use ECidade\Educacao\Secretaria\BNCC\Model\Disciplina;
use ECidade\Educacao\Secretaria\BNCC\Registry\DisciplinaRegistry;
use ECidade\Educacao\Secretaria\BNCC\Registry\HabilidadeReferencialCurricularEstadualRegistry;
use ECidade\Educacao\Secretaria\BNCC\Repository\HabilidadeReferencialCurricularEstadualRepository;
use ECidade\Educacao\Secretaria\Services\ParametrosGlobaisService;
use ECidade\Enum\Educacao\BNCC\TipoBaseCurricularEnum;
use Exception;
use stdClass;
use TurmaRepository;

/**
 * Class HabilidadesDesenvolvidasService
 * @package ECidade\Educacao\Escola\Service
 */
class HabilidadeDesenvolvidaService
{
    /**
     * @var HabilidadeDesenvolvidaRepository
     */
    private $repository;
    /**
     * @var HabilidadeReferencialCurricularEstadualRepository
     */
    private $referencialRepository;

    public function __construct()
    {
        $this->repository = new HabilidadeDesenvolvidaRepository();
        $this->referencialRepository = new HabilidadeDesenvolvidaReferencialRepository();
    }

    /**
     * @param stdClass $parametros
     * @throws Exception
     */
    public function salvarFromRpc($parametros)
    {
        if (empty($parametros->codigo)) {
            throw new Exception("Informe o código do conteúdo.");
        }
        if (empty($parametros->disciplinaBncc)) {
            throw new Exception("Informe o código da disciplina da BNCC.");
        }
        if (empty($parametros->turma)) {
            throw new Exception("Informe o código da Turma.");
        }

        $turma = TurmaRepository::getTurmaByCodigo($parametros->turma);
        $ano = $turma->getCalendario()->getAnoExecucao();

        /** @var ConteudoDesenvolvido $conteudoDesenvolvido */
        $conteudoDesenvolvido = ConteudoDesenvolvidoRegistry::get($parametros->codigo);
        $disciplinaBncc = DisciplinaRegistry::get($parametros->disciplinaBncc);
        $this->excluir($conteudoDesenvolvido, $disciplinaBncc);

        if (isset($parametros->habilidades)) {
            $configuracao = ParametrosGlobaisService::get();
            if ($configuracao->isReferencialCurricularEstadual()) {
                $this->salvarHabilidadeReferencial($parametros, $ano);
            } else {
                foreach ($parametros->habilidades as $codigoHabilidade) {
                    $habilidade = $this->salvarHabilidade($codigoHabilidade, $conteudoDesenvolvido, $disciplinaBncc);
                    $conteudoDesenvolvido->addHabilidade($habilidade);
                }
            }
        }
    }

    /**
     * @param $codigoHabilidade
     * @param $conteudoDesenvolvido
     * @param $disciplinaBncc
     * @return HabilidadeDesenvolvida
     * @throws Exception
     */
    private function salvarHabilidade($codigoHabilidade, $conteudoDesenvolvido, $disciplinaBncc)
    {
        $habilidade = new HabilidadeDesenvolvida();
        $habilidade->setCodigoHabilidade($codigoHabilidade);
        $habilidade->setConteudoDesenvolvido($conteudoDesenvolvido);
        $habilidade->setDisciplina($disciplinaBncc);
        $habilidade = $this->repository->salvar($habilidade);
        return $habilidade;
    }

    private function salvarHabilidadeReferencial($parametros, $ano)
    {
        $conteudoDesenvolvido = ConteudoDesenvolvidoRegistry::get($parametros->codigo);
        $disciplinaBncc = DisciplinaRegistry::get($parametros->disciplinaBncc);
        $habilidadesSalvar = [];
        $referencialRepository = new HabilidadeReferencialCurricularEstadualRepository();
        foreach ($parametros->habilidades as $codigoHabilidade) {
            $referencialRepository->resetScopes();
            $habilidadeReferencial = $referencialRepository->scopeCodigoReferencial($codigoHabilidade)
                ->scopeAno($ano)
                ->find();

            $key = $habilidadeReferencial->getCodigoHabilidade();
            if (!array_key_exists($key, $habilidadesSalvar)) {
                $habilidadesSalvar[$key] = (object)[
                    "codigo" => $key,
                    "codigosReferencial" => []
                ];
            }
            $habilidadesSalvar[$key]->codigosReferencial[] = $habilidadeReferencial->getCodigo();
        }
        foreach ($habilidadesSalvar as $habilidade) {
            $habilidadeSalva =  $this->salvarHabilidade($habilidade->codigo, $conteudoDesenvolvido, $disciplinaBncc);
            foreach ($habilidade->codigosReferencial as $codigo) {
                $habilidadeReferencial = new HabilidadeDesenvolvidaReferencial();
                $habilidadeReferencial->setHabilidadeDesenvolvida($habilidadeSalva);
                $habilidadeReferencial->setReferencialCurricular(
                    HabilidadeReferencialCurricularEstadualRegistry::get($codigo)
                );
                $habilidadeDesenvolvidaReferencia = $this->referencialRepository->salvar($habilidadeReferencial);
                $habilidadeSalva->addHabilidadeReferencial($habilidadeDesenvolvidaReferencia);
            }
            $conteudoDesenvolvido->addHabilidade($habilidadeSalva);
        }
    }


    /**
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @param Disciplina $disciplinaBncc
     * @return bool
     * @throws Exception
     */
    private function excluir(ConteudoDesenvolvido $conteudoDesenvolvido, Disciplina $disciplinaBncc)
    {
        $this->repository->scopeDisciplina($disciplinaBncc)
            ->scopeConteudoDesenvolvido($conteudoDesenvolvido)
            ->excluirByScope();

        return true;
    }

    /**
     * @param Disciplina $disciplina
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @return HabilidadeDesenvolvida[]
     * @throws Exception
     */
    public function getHabilidadesDesenvolvidas(Disciplina $disciplina, ConteudoDesenvolvido $conteudoDesenvolvido)
    {
        return $this->repository->scopeDisciplina($disciplina)
            ->scopeConteudoDesenvolvido($conteudoDesenvolvido)
            ->get();
    }

    /**
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @return HabilidadeDesenvolvida[]
     * @throws Exception
     */
    public function getHabilidadesConteudo(ConteudoDesenvolvido $conteudoDesenvolvido)
    {
        return $this->repository->scopeConteudoDesenvolvido($conteudoDesenvolvido)->get();
    }
}
