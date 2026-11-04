<?php


namespace ECidade\Educacao\Secretaria\BNCC\Service;

use ECidade\Educacao\Escola\Repository\HabilidadeDesenvolvidaReferencialRepository;
use ECidade\Educacao\Escola\Repository\HabilidadeDesenvolvidaRepository;
use ECidade\Educacao\Secretaria\BNCC\Model\BnccOriginalEducacaoInfantil;
use ECidade\Educacao\Secretaria\BNCC\Model\BnccOriginalEnsinoFundamental;
use ECidade\Educacao\Secretaria\BNCC\Model\HabilidadeEducacaoInfantil;
use ECidade\Educacao\Secretaria\BNCC\Model\HabilidadeReferencialCurricularEstadual;
use ECidade\Educacao\Secretaria\BNCC\Model\HabilidadesEnsinoFundamental;
use ECidade\Educacao\Secretaria\BNCC\Repository\BnccOriginalEducacaoInfantilRepository;
use ECidade\Educacao\Secretaria\BNCC\Repository\BnccOriginalEnsinoFundamentalRepository;
use ECidade\Educacao\Secretaria\BNCC\Repository\HabilidadeEducacaoInfantilRepository;
use ECidade\Educacao\Secretaria\BNCC\Repository\HabilidadeEnsinoFundamentalRepository;
use ECidade\Educacao\Secretaria\BNCC\Repository\HabilidadeReferencialCurricularEstadualRepository;
use ECidade\Enum\Educacao\BNCC\EnsinoEnum;
use Exception;

/**
 * Class BnccService
 * @package ECidade\Educacao\Secretaria\BNCC\Service
 */
class BnccService
{
    /**
     * @var BnccOriginalEducacaoInfantilRepository
     */
    private $reposistoryInfantil;
    /**
     * @var
     */
    private $reposistoryFundamental;

    public function __construct()
    {
        $this->reposistoryInfantil = new BnccOriginalEducacaoInfantilRepository();
        $this->reposistoryFundamental = new BnccOriginalEnsinoFundamentalRepository();
    }

    /**
     * @return BnccOriginalEducacaoInfantil[]
     * @throws Exception
     */
    public function getFiltrosEducacaoInfantil()
    {
        return $this->reposistoryInfantil->get(['distinct ed167_disciplina', 'ed167_faixa_etaria'], [1, 2]);
    }

    /**
     * @return BnccOriginalEnsinoFundamental[]
     * @throws Exception
     */
    public function getFiltrosEnsinoFundamental($opcao = null, $ano = null)
    {

        if ($opcao == 3) {
            $campos = ['distinct ed148_disciplina', 'ed148_unidade_tematica', 'ed148_objeto_conhecimento'];
            $reposistoryFundamental = new HabilidadeEnsinoFundamentalRepository();
            $reposistoryFundamental->scopeAno($ano);
            return $reposistoryFundamental->get($campos, [1, 2, 3]);
        }
        $campos = ['distinct ed166_disciplina', 'ed166_unidade_tematica', 'ed166_objeto_conhecimento'];
        return $this->reposistoryFundamental->get($campos, [1, 2, 3]);
    }

    /**
     * @param $parametros
     * @return BnccOriginalEducacaoInfantil[]
     * @throws Exception
     */
    public function buscaHabilidadesInfatilManutencao($parametros)
    {
        $this->reposistoryInfantil->scopeCampoExperiencia($parametros->camposExperiencia);
        if (!empty($parametros->faixaEtaria)) {
            $this->reposistoryInfantil->scopeFaixaEtaria($parametros->faixaEtaria);
        }
        return $this->reposistoryInfantil->getCompleto($parametros->ano);
    }

    /**
     * @param $parametros
     * @return BnccOriginalEnsinoFundamental[]|HabilidadeEnsinoFundamental[]
     * @throws Exception
     */
    public function buscaHabilidadesFundamentalManutencao($parametros)
    {
        if ($parametros->opcao == 3) {
            $reposistoryFundamental = new HabilidadeEnsinoFundamentalRepository();
            $reposistoryFundamental->scopeDisciplina($parametros->disciplina);
            $reposistoryFundamental->scopeAno($parametros->ano);
            if (!empty($parametros->unidadeTematica)) {
                $reposistoryFundamental->scopeUnidadeTematica($parametros->unidadeTematica);
            }
            if (!empty($parametros->objetoConhecimento)) {
                $reposistoryFundamental->scopeObjetoConhecimento($parametros->objetoConhecimento);
            }
            $retorno = $reposistoryFundamental->get();
            if (isset($parametros->referencial) && $parametros->referencial) {
                if (empty($retorno)) {
                    return "";
                }
                $referencialRepository = new HabilidadeReferencialCurricularEstadualRepository;
                $referencialRepository->scopeCodigoHabilidadeBNCC(pg_escape_string($retorno[0]->getCodigo()));

                $retorno[0]->setHabilidadesReferencialCurricular($referencialRepository->get());
            }
            return $retorno;
        }

        $this->reposistoryFundamental->scopeDisciplina($parametros->disciplina);
        if (!empty($parametros->unidadeTematica)) {
            $this->reposistoryFundamental->scopeUnidadeTematica($parametros->unidadeTematica);
        }
        if (!empty($parametros->objetoConhecimento)) {
            $this->reposistoryFundamental->scopeObjetoConhecimento($parametros->objetoConhecimento);
        }
        return $this->reposistoryFundamental->getCompleto($parametros->ano);
    }

    /**
     * @param string $ano
     * @param array $habilidades
     * @throws Exception
     */
    public function salvarHabilidadesEducacaoInfantil($ano, $habilidades)
    {
        $repository = new HabilidadeEducacaoInfantilRepository();

        foreach ($habilidades as $habilidade) {
            $habilidadeEducacaoInfantil = $repository->scopeAno($ano)
                ->scopeHabilidade($habilidade->codigo)
                ->get();
            $habilidadeEducacaoInfantil = array_shift($habilidadeEducacaoInfantil);

            if (is_null($habilidade->habilidadeComentada)) {
                if (!is_null($habilidadeEducacaoInfantil)) {
                    $habilidadeReferencialRepository = new HabilidadeReferencialCurricularEstadualRepository();
                    $habilidadesReferencial = $habilidadeReferencialRepository
                        ->scopeAno($habilidadeEducacaoInfantil->getAno())
                        ->scopeCodigoHabilidadeBNCC($habilidadeEducacaoInfantil->getCodigo())
                        ->get();

                    foreach ($habilidadesReferencial as $habilidadeReferencial) {
                        $habilidadeReferencialRepository->excluir($habilidadeReferencial);
                    }

                    $habilidadeDesenvolvidaRepository = new HabilidadeDesenvolvidaRepository();
                    $habilidadeDesenvolvidaRepository
                        ->scopeCodigoHabilidade($habilidadeEducacaoInfantil->getCodigo())
                        ->excluirByScope();

                    $repository->excluir($habilidadeEducacaoInfantil);
                }
            } else {
                if (is_null($habilidadeEducacaoInfantil)) {
                    $habilidadeEducacaoInfantil = new HabilidadeEducacaoInfantil();
                    $habilidadeEducacaoInfantil->setDisciplina($habilidade->disciplina);
                    $habilidadeEducacaoInfantil->setFaixaEtaria($habilidade->faixaEtaria);
                    $habilidadeEducacaoInfantil->setCodigo($habilidade->codigo);
                    $habilidadeEducacaoInfantil->setHabilidade($habilidade->habilidade_editada);
                    $habilidadeEducacaoInfantil->setAno($ano);
                } else {
                    $habilidadeEducacaoInfantil->setHabilidade($habilidade->habilidade_editada);
                }
                $repository->salvar($habilidadeEducacaoInfantil);
            }
        }
    }

    /**
     * @param string $ano
     * @param array $habilidades
     * @throws Exception
     */
    public function salvarHabilidadesEnsinoFundamental($ano, array $habilidades)
    {
        $repository = new HabilidadeEnsinoFundamentalRepository();
        foreach ($habilidades as $habilidade) {
            $habilidadeEnsinoFundamental = $repository->scopeAno($ano)
                ->scopeHabilidade($habilidade->codigo)
                ->first();

            if (is_null($habilidade->habilidadeComentada)) {
                if (!is_null($habilidadeEnsinoFundamental)) {
                    $habilidadeReferencialRepository = new HabilidadeReferencialCurricularEstadualRepository();
                    $habilidadesReferencial = $habilidadeReferencialRepository
                        ->scopeAno($habilidadeEnsinoFundamental->getAno())
                        ->scopeCodigoHabilidadeBNCC($habilidadeEnsinoFundamental->getCodigo())
                        ->get();

                    foreach ($habilidadesReferencial as $habilidadeReferencial) {
                        $this->excluirHabilidadeReferencial($habilidadeReferencial);
                    }

                    $habilidadeDesenvolvidaRepository = new HabilidadeDesenvolvidaRepository();
                    $habilidadeDesenvolvidaRepository
                        ->scopeCodigoHabilidade($habilidadeEnsinoFundamental->getCodigo())
                        ->excluirByScope();
                    $repository->excluir($habilidadeEnsinoFundamental);
                }
            } else {
                if (is_null($habilidadeEnsinoFundamental)) {
                    $habilidadeEnsinoFundamental = new HabilidadesEnsinoFundamental();
                    $habilidadeEnsinoFundamental->setEtapa($habilidade->etapas_editada);
                    $habilidadeEnsinoFundamental->setCodigo($habilidade->codigo);
                    $habilidadeEnsinoFundamental->setHabilidade($habilidade->habilidade_editada);
                    $habilidadeEnsinoFundamental->setUnidadeTematica($habilidade->unidadeTematica);
                    $habilidadeEnsinoFundamental->setObjetoConhecimento($habilidade->objetoConhecimento);
                    $habilidadeEnsinoFundamental->setDisciplina($habilidade->disciplina);
                    $habilidadeEnsinoFundamental->setAno($ano);
                } else {
                    $habilidadeEnsinoFundamental->setHabilidade($habilidade->habilidade_editada);
                    $habilidadeEnsinoFundamental->setEtapa($habilidade->etapas_editada);
                }
                $repository->salvar($habilidadeEnsinoFundamental);
            }
        }
    }

    /**
     * @param string $ano
     * @param array $habilidades
     * @throws Exception
     */
    public function salvarHabilidadeEnsinoFundamental($ano, array $habilidades, $novaHabilidade = false)
    {
        $repository = new HabilidadeEnsinoFundamentalRepository();
        foreach ($habilidades as $habilidade) {
            if (!$novaHabilidade) {
                $codigo = isset($habilidade->editar) ? $habilidade->editar : $habilidade->codigo;
                $habilidadeEnsinoFundamental = $repository->scopeAno($ano)->scopeHabilidade($codigo)->first();
                if (is_null($habilidadeEnsinoFundamental)) {
                    $habilidadeEnsinoFundamental = new HabilidadesEnsinoFundamental();
                    $habilidadeEnsinoFundamental->setEtapa($habilidade->etapas);
                    $habilidadeEnsinoFundamental->setCodigo($codigo);
                    $habilidadeEnsinoFundamental->setHabilidade($habilidade->habilidade);
                    $habilidadeEnsinoFundamental->setUnidadeTematica($habilidade->unidadeTematica);
                    $habilidadeEnsinoFundamental->setObjetoConhecimento($habilidade->objetoConhecimento);
                    $habilidadeEnsinoFundamental->setDisciplina($habilidade->disciplina);
                    $habilidadeEnsinoFundamental->setAno($ano);
                } else {
                    if ($habilidade->habilidadeComentada == null) {
                        foreach ($habilidadeEnsinoFundamental->getHabilidadesReferencialCurricular() as $referencial) {
                            $this->excluirHabilidadeReferencial($referencial);
                        }
                    }
                    if (isset($habilidade->editar)) {
                        $habilidadeEnsinoFundamental->setCodigo($habilidade->codigo);
                        foreach ($habilidadeEnsinoFundamental->getHabilidadesReferencialCurricular() as $referencial) {
                            $referencial->setCodigoHabilidade($habilidade->codigo);
                            $repo = new HabilidadeReferencialCurricularEstadualRepository();
                            $repo->salvar($referencial);
                        }
                    }
                    $habilidadeEnsinoFundamental->setEtapa($habilidade->etapas_editada);
                    $habilidadeEnsinoFundamental->setHabilidade($habilidade->habilidade);
                }
            } else {
                $codigo = isset($habilidade->editar) ? $habilidade->editar : $habilidade->codigo;
                $habilidadeEnsinoFundamental = $repository->scopeAno($ano)
                    ->scopeHabilidade($codigo)->scopeObjetoConhecimento($habilidade->objetoConhecimento)->first();

                if (!is_null($habilidadeEnsinoFundamental)) {
                    if ($habilidadeEnsinoFundamental->getUnidadeTematica() != $habilidade->unidadeTematica) {
                        throw new Exception("Objeto de conhecimento cadastrado em outra Unidade Temática");
                    }
                    throw new Exception("Habilidade já cadastrada para esse objeto de conhecimento.");
                } else {
                    $habilidadeEnsinoFundamental = new HabilidadesEnsinoFundamental();
                    $habilidadeEnsinoFundamental->setEtapa($habilidade->etapas);
                    $habilidadeEnsinoFundamental->setCodigo($codigo);
                    $habilidadeEnsinoFundamental->setHabilidade($habilidade->habilidade);
                    $habilidadeEnsinoFundamental->setUnidadeTematica($habilidade->unidadeTematica);
                    $habilidadeEnsinoFundamental->setObjetoConhecimento($habilidade->objetoConhecimento);
                    $habilidadeEnsinoFundamental->setDisciplina($habilidade->disciplina);
                    $habilidadeEnsinoFundamental->setAno($ano);
                }
            }
            $repository->salvar($habilidadeEnsinoFundamental);
        }
    }

    /**
     * @param HabilidadeReferencialCurricularEstadual $habilidadeReferencial
     * @throws Exception
     */
    public function excluirHabilidadeReferencial(HabilidadeReferencialCurricularEstadual $habilidadeReferencial)
    {
        $habilidadeDesenvolvidaReferencialRepository = new HabilidadeDesenvolvidaReferencialRepository();
        $habilidadesDesenvolvidasReferencial = $habilidadeDesenvolvidaReferencialRepository
            ->scopeReferencial($habilidadeReferencial)
            ->get();

        foreach ($habilidadesDesenvolvidasReferencial as $habilidadeDesenvolidaReferencial) {
            $habilidadeDesenvolvidaReferencialRepository->excluir($habilidadeDesenvolidaReferencial);
        }

        $repository = new HabilidadeReferencialCurricularEstadualRepository();
        $repository->excluir($habilidadeReferencial);
    }

    /**
     * @param $habilidade
     * @return HabilidadeReferencialCurricularEstadual
     * @throws Exception
     */
    public function adicionarReferencial($habilidade)
    {
        $repository = new HabilidadeReferencialCurricularEstadualRepository();

        $habilidadeReferencial = new HabilidadeReferencialCurricularEstadual();
        $habilidadeReferencial->setEnsino(new EnsinoEnum($habilidade->ensino));
        $habilidadeReferencial->setEtapa($habilidade->etapa);
        $habilidadeReferencial->setCodigoHabilidade($habilidade->codigoHabilidade);
        $habilidadeReferencial->setCodigoReferencial($habilidade->codigoReferencial);
        $habilidadeReferencial->setHabilidade($habilidade->habilidade);
        $habilidadeReferencial->setAno($habilidade->ano);
        $habilidadeReferencial->setObjetoConhecimento(
            property_exists($habilidade, 'objetoConhecimento') ?
                $habilidade->objetoConhecimento : ""
        );

        return $repository->salvar($habilidadeReferencial);
    }

    /**
     * @param $parametros
     * @param $novoNome
     * @return boolean
     * @throws Exception
     */
    public function editarObjetoConhecimento($novoNome, $parametros)
    {
        $repository = new HabilidadeEnsinoFundamentalRepository();

        return $repository->editarObjetoConhecimento($novoNome, $parametros);
    }


    /**
     * @param $parametros
     * @param $novoNome
     * @return boolean
     * @throws Exception
     */
    public function excluirObjetoConhecimento($objeto)
    {
        $repository = new HabilidadeEnsinoFundamentalRepository();

        return $repository->excluirObjetoConhecimento($objeto);
    }

    public function excluirHabilidadeEF($codigo, $objetoConhecimento, $ano)
    {
        $repository = new HabilidadeEnsinoFundamentalRepository();

        $habilidadeEnsinoFundamental = $repository->scopeAno($ano)->scopeHabilidade($codigo)
            ->scopeObjetoConhecimento($objetoConhecimento)->get();

        foreach ($habilidadeEnsinoFundamental as $habilidade) {
            if ($repository->temVinculoDiarioDeClasse($habilidade->getId())) {
                throw new Exception("Habilidade está vinculada a Diarios de Classe.");
            }
        }
        foreach ($habilidadeEnsinoFundamental as $habilidade) {
            $repository->excluir($habilidade);
        }
        return true;
    }
}
