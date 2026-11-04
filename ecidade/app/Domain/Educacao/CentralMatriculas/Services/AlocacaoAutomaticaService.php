<?php

namespace App\Domain\Educacao\CentralMatriculas\Services;

use DateTime;
use stdClass;
use Exception;
use Illuminate\Support\Facades\Log;
use ECidade\Educacao\MatriculaOnline\Model\Fase;
use ECidade\Educacao\MatriculaOnline\Model\Candidato;
use ECidade\Educacao\MatriculaOnline\Service\PrevisaoVagas;
use ECidade\Educacao\MatriculaOnline\Model\OpcaoListaEspera;
use ECidade\Enum\Educacao\MatriculaOnline\SituacoesListaEsperaEnum;
use ECidade\Educacao\MatriculaOnline\Repository\ListaEsperaRepository;
use ECidade\Educacao\MatriculaOnline\Repository\ListaEspera as ListaEspera;
use ECidade\Educacao\MatriculaOnline\Repository\OpcaoEscolaCandidatoRepository;
use ECidade\Educacao\MatriculaOnline\Repository\CandidatoNecessidadeEspecialRepository;

class AlocacaoAutomaticaService
{
    /**
     * @var array
     */
    private $listasProcessar = [];
    /**
     * @var Fase
     */
    private $fase;
    /**
     * @var OpcaoEscolaCandidatoRepository
     */
    private $opcaoEscolaCandidatoRepository;
    /**
     * @var DateTime
     */
    private $dataAtual;

    public function __construct()
    {
        $this->opcaoEscolaCandidatoRepository = new OpcaoEscolaCandidatoRepository();
        $this->dataAtual = new DateTime('now');
    }

    public function setFase(Fase $fase)
    {
        $this->fase = $fase;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function processarAlocacoes()
    {
        Log::info("Fase: {$this->fase->getCodigo()}");
        $this->listasProcessar = $this->getListasEsperaComCandidatos();

        $alocarCandidatos = true;
        while ($alocarCandidatos) {
            foreach ($this->listasProcessar as $listaEspera) {
                if ($listaEspera->previsaoVagas->vagas_restantes == 0) {
                    continue;
                }
                $this->alocarCandidatos($listaEspera);
            }
            $alocarCandidatos = $this->verificarCandidatosAlocados();
        }

        foreach ($this->listasProcessar as $listaEspera) {
            if (count($listaEspera->candidatosAlocar) == 0) {
                continue;
            }
            Log::info("==================================================");
            Log::info(
                "Lista de Espera: Fase: {$this->fase->getCodigo()} "
                ."- Escola: {$listaEspera->escola->getCodigo()} "
                ."- Etapa: {$listaEspera->etapa->getCodigo()} - Turno: {$listaEspera->turno->getCodigoTurno()}"
            );
            foreach ($listaEspera->candidatosAlocar as $opcaoListaEspera) {
                $this->efetivarAlocacaoCandidato($opcaoListaEspera);
            }
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getListasEsperaComCandidatos()
    {
        $listasEspera = ListaEsperaRepository::buscarEtapaEscolaEnsino($this->fase);
        $listaEsperaRepository = new ListaEsperaRepository();

        $qtdListas = count($listasEspera);
        Log::info("Listas de Espera nesta fase: {$qtdListas}");

        $listasEsperarProcessar = [];
        foreach ($listasEspera as $listaEspera) {
            $escola = $listaEspera['escola'];
            $etapa = $listaEspera['etapa'];
            $turno = $listaEspera['turno'];
            $log = sprintf(
                "Buscando Fase: %s - Etapa: %s - Escola: %s - Turno: %s",
                $this->fase->getCodigo(),
                $etapa->getCodigo(),
                $escola->getCodigo(),
                $turno->getCodigoTurno()
            );
            Log::info($log);

            $candidatosNaLista = $listaEsperaRepository->scopeFase($this->fase)
                ->scopeEscola($escola)
                ->scopeEtapa($etapa)
                ->scopeTurno($turno)
                ->scopeSituacao(new SituacoesListaEsperaEnum(SituacoesListaEsperaEnum::ATIVA))
                ->get();

            foreach ($candidatosNaLista as $candidatoNaLista) {
                $candidatoNecessidadeRepository = new CandidatoNecessidadeEspecialRepository();
                $candidatoNaLista->getCandidato()->setNecessidadeEspecial(
                    $candidatoNecessidadeRepository->getNecessidades($candidatoNaLista->getCandidato())
                );
            }

            usort($candidatosNaLista, function (OpcaoListaEspera $a, OpcaoListaEspera $b) {
                return $a->getClassificacao() > $b->getClassificacao();
            });

            // Verifica se tem candidatos para designar
            if (count($candidatosNaLista) == 0) {
                Log::info("Sem candidatos na Lista de Espera.");
                continue;
            }
            Log::info("Total de Candidatos na Lista de Espera: " . count($candidatosNaLista));

            // Verifica se tem vagas disponíveis
            $vagasPcdService = new VagasPcdService();
            $vagasPcd = $vagasPcdService->buscarVagasPcd($this->fase, $escola, $etapa, $turno);
            $previsaoVagas = PrevisaoVagas::resumo(
                $escola->getCodigo(),
                $turno->getCodigoTurno(),
                $etapa->getCodigo(),
                $this->fase->getCodigo(),
                $this->fase->getAno()
            );

            $previsaoVagas->vagas_pcd = $vagasPcd->mo64_vagas;
            $previsaoVagas->restantes_pcd = $vagasPcd->mo64_vagas - $previsaoVagas->deficientes;
            $listasEsperarProcessar[] = (object)[
                'escola' => $escola,
                'etapa' => $etapa,
                'turno' => $turno,
                'previsaoVagas' => $previsaoVagas,
                'candidatosNaLista' => $candidatosNaLista,
                'candidatosAlocar' => [],
            ];
        }

        return $listasEsperarProcessar;
    }

    private function alocarCandidatos($listaEspera)
    {
        $vagasDisponiveis = $listaEspera->previsaoVagas;

        foreach ($listaEspera->candidatosNaLista as $key => $opcaoListaEspera) {
            if ($vagasDisponiveis->vagas_restantes == 0) {
                break;
            }

            if (count($opcaoListaEspera->getCandidato()->getNecessidadeEspecial()) > 0
                && $vagasDisponiveis->restantes_pcd > 0
            ) {
                $listaEspera->candidatosAlocar[] = $opcaoListaEspera;
                $vagasDisponiveis->vagas_restantes--;
                $vagasDisponiveis->restantes_pcd--;
            } else {
                if (count($opcaoListaEspera->getCandidato()->getNecessidadeEspecial()) == 0) {
                    $listaEspera->candidatosAlocar[] = $opcaoListaEspera;
                    $vagasDisponiveis->vagas_restantes--;
                }
            }
            unset($listaEspera->candidatosNaLista[$key]);
        }
    }


    private function alocarCandidato(Candidato $candidato, $opcaoVerificar)
    {
        $opcaoEscolaCandidatoRepository = new OpcaoEscolaCandidatoRepository();
        $opcaoPrioridadeCandidato = $opcaoEscolaCandidatoRepository
            ->resetScopes()
            ->scopeCandidato($candidato)
            ->scopeOpcao($opcaoVerificar)
            ->first();

        $listasEspera = array_filter(
            $this->listasProcessar,
            function ($lista) use ($opcaoPrioridadeCandidato) {
                $escola = $opcaoPrioridadeCandidato->getEscola();
                $turno = $opcaoPrioridadeCandidato->getTurno();
                $etapa = $opcaoPrioridadeCandidato->getCandidato()->getEtapa();

                return $lista->escola->getCodigo() == $escola->getCodigo()
                && $lista->turno->getCodigoTurno() == $turno->getCodigoTurno()
                && $lista->etapa->getCodigo() == $etapa->getCodigo();
            }
        );
        $listaEspera = array_shift($listasEspera);
        $opcaoListaEspera = array_filter(
            $listaEspera->candidatosNaLista,
            function ($candidato) use ($opcaoPrioridadeCandidato) {
                return $candidato->getCandidato()->getCodigo() ==
                    $opcaoPrioridadeCandidato->getCandidato()->getCodigo();
            }
        );
        $opcaoListaEspera = array_shift($opcaoListaEspera);
        /**
         * @var $opcaoListaEspera OpcaoListaEspera
         */
        if ($opcaoListaEspera->getClassificacao() > $listaEspera->previsaoVagas->vagas_restantes) {
            return false;
        }
        return true;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    private function verificarCandidatosAlocados()
    {
        $fazerMaisAlocacoes = false;
        foreach ($this->listasProcessar as $listaEspera) {
            foreach ($listaEspera->candidatosAlocar as $key => $candidatoAlocado) {
                $algumCandidatoRemovido = $this->verificarOutraLista($candidatoAlocado, $key, $listaEspera);
                if ($algumCandidatoRemovido) {
                    $fazerMaisAlocacoes = true;
                }
            }
        }

        return $fazerMaisAlocacoes;
    }

    /**
     * @param  OpcaoListaEspera $opcaoListaEsperaA
     * @param  $keyA
     * @return bool
     * @throws Exception
     */
    private function verificarOutraLista(OpcaoListaEspera $opcaoListaEsperaA, $keyA, $listaEsperaA)
    {
        $candidatoRemovido = false;
        $opcaoEscolaCandidatoA = $this->opcaoEscolaCandidatoRepository
            ->resetScopes()
            ->scopeCandidato($opcaoListaEsperaA->getCandidato())
            ->scopeEscola($opcaoListaEsperaA->getEscola())
            ->scopeTurno($opcaoListaEsperaA->getTurno())
            ->first();

        foreach ($this->listasProcessar as $listaEsperaB) {
            foreach ($listaEsperaB->candidatosAlocar as $keyB => $opcaoListaEsperaB) {
                if ($opcaoListaEsperaB->getCandidato()->getCodigo() == $opcaoListaEsperaA->getCandidato()->getCodigo()
                ) {
                    $opcaoEscolaCandidatoB = $this->opcaoEscolaCandidatoRepository
                        ->resetScopes()
                        ->scopeCandidato($opcaoListaEsperaB->getCandidato())
                        ->scopeEscola($opcaoListaEsperaB->getEscola())
                        ->scopeTurno($opcaoListaEsperaB->getTurno())
                        ->first();

                    if (is_null($opcaoEscolaCandidatoA) || is_null($opcaoEscolaCandidatoB)) {
                        throw new Exception(
                            "Candidato sem opcao: {$opcaoListaEsperaA->getCandidato()->getNome()}".
                            " - {$opcaoListaEsperaA->getEtapa()->getNome()}"
                        );
                    }

                    if ($opcaoEscolaCandidatoA->getOpcao() === $opcaoEscolaCandidatoB->getOpcao()) {
                        continue;
                    }

                    if ($opcaoEscolaCandidatoA->getOpcao() < $opcaoEscolaCandidatoB->getOpcao()) {
                        if (count($opcaoListaEsperaB->getCandidato()->getNecessidadeEspecial()) > 0) {
                            $listaEsperaB->previsaoVagas->restantes_pcd++;
                        }
                        $candidatoRemovido = true;
                        Log::info(
                            "Removendo {$opcaoListaEsperaB->getCandidato()->getNome()}".
                            " opcao {$opcaoEscolaCandidatoB->getOpcao()} e"
                            ." mantendo opcao {$opcaoEscolaCandidatoA->getOpcao()}"
                        );
                        if (isset($listaEsperaB->candidatosAlocar[$keyB])) {
                            unset($listaEsperaB->candidatosAlocar[$keyB]);
                            $listaEsperaB->previsaoVagas->vagas_restantes++;
                        }
                    } else {
                        if (count($opcaoListaEsperaA->getCandidato()->getNecessidadeEspecial()) > 0) {
                            $listaEsperaA->previsaoVagas->restantes_pcd++;
                        }
                        $candidatoRemovido = true;
                        Log::info(
                            "Removendo {$opcaoListaEsperaA->getCandidato()->getNome()}"
                            ." opcao {$opcaoEscolaCandidatoA->getOpcao()}"
                            ." e mantendo opcao {$opcaoEscolaCandidatoB->getOpcao()}"
                        );
                        if (isset($listaEsperaA->candidatosAlocar[$keyA])) {
                            unset($listaEsperaA->candidatosAlocar[$keyA]);
                            $listaEsperaA->previsaoVagas->vagas_restantes++;
                        }
                    }
                }
            }
        }

        return $candidatoRemovido;
    }

    /**
     * @param  OpcaoListaEspera $opcaoListaEspera
     * @return void
     * @throws Exception
     */
    private function efetivarAlocacaoCandidato(OpcaoListaEspera $opcaoListaEspera)
    {
        $opcaoEscolaCandidatoRepository = new OpcaoEscolaCandidatoRepository();
        $opcaoEscolaCandidato = $opcaoEscolaCandidatoRepository
            ->resetScopes()
            ->scopeCandidato($opcaoListaEspera->getCandidato())
            ->scopeEscola($opcaoListaEspera->getEscola())
            ->scopeTurno($opcaoListaEspera->getTurno())
            ->first();
        Log::info(
            "Alocando candidato: {$opcaoListaEspera->getCandidato()->getNome()}"
            ." Opcao: {$opcaoEscolaCandidato->getOpcao()}"
        );

        $parametros = new stdClass();
        $parametros->codigoListaEspera = $opcaoListaEspera->getCodigo();
        $parametros->observacao = "{$this->dataAtual->format('d/m/Y')} - ALOCAÇÃO AUTOMÁTICA";
        $parametros->tipoSituacao = 1;
        $parametros->retiraTodasListas = true;
        $parametros->notificarCandidato = true;
        ListaEspera::atualizarCandidato($parametros);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function buscarListaImpedida()
    {
        $listasEspera = $this->getListasEsperaComCandidatos();

        $listasEspera = array_filter(
            $listasEspera,
            function ($listaEspera) {
                return $listaEspera->previsaoVagas->restantes_pcd <= 0;
            }
        );
        $listasEspera = array_filter(
            $listasEspera,
            function ($listaEspera) {
                return $listaEspera->previsaoVagas->vagas_restantes > 0;
            }
        );

        $listasImpedidas = [];
        foreach ($listasEspera as $listaEspera) {
            foreach ($listaEspera->candidatosNaLista as $candidato) {
                if (count($candidato->getCandidato()->getNecessidadeEspecial()) > 0) {
                    $listasImpedidas[] = (object)[
                        "fase_codigo" => $this->fase->getCodigo(),
                        "fase_descricao" => $this->fase->getDescricao(),
                        "escola_codigo" => $listaEspera->escola->getCodigo(),
                        "escola_descricao" => $listaEspera->escola->getNome(),
                        "etapa_codigo" => $listaEspera->etapa->getCodigo(),
                        "etapa_nome" => $listaEspera->etapa->getNome(),
                        "turno_codigo" => $listaEspera->turno->getCodigoTurno(),
                        "turno_nome" => $listaEspera->turno->getDescricao(),
                        "matriculas_pcd" => $listaEspera->previsaoVagas->deficientes,
                        "vagas_pcd" => $listaEspera->previsaoVagas->vagas_pcd,
                    ];
                    break;
                }
            }
        }

        return $listasImpedidas;
    }
}
