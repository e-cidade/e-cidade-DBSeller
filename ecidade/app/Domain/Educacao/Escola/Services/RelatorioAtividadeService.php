<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Repositories\AtividadeProfissionalRepository;

/**
 * Class RelatorioAtividadesService
 * @package App\Domain\Educacao\Escola\Services
 */

class RelatorioAtividadeService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new AtividadeProfissionalRepository();
    }

    public function getDados($filtros)
    {
        $timeInicioRepository = microtime(true);
        $bModuloEscola = ($filtros['DB_modulo'] == 1100747) ? true : false;
        $atividades = $this->repository->getByFiltros($filtros);
        $timeFimRepository = microtime(true);
        $dadosProcessados = [];
        $timeInicioDadosProcessados = microtime(true);
        foreach ($atividades as $atividade) {
            foreach ($atividade->atividadesProfissionaisEscola as $profissionalAtividade) {
                $profissionalView = $profissionalAtividade->profissionalEscola->profissionaisView;

                if ($profissionalView->escola->ed18_i_codigo != $filtros['DB_coddepto']
                    && $bModuloEscola) {
                    continue;
                }
                $cgm = $profissionalView->cgm;
                $matricula = "----";
                if (isset($profissionalView->rhPessoal)) {
                    $matricula = $profissionalView->rhPessoal;
                }
                $escola = $profissionalView->escola;
                $regimeDescr = 'NÃO INFORMADO';
                if (isset($profissionalAtividade->profissionalEscola->profissional->regime)) {
                    $regimeDescr = $profissionalAtividade->profissionalEscola->profissional->regime->rh30_descr;
                }
                $profissionalEscola = $profissionalAtividade->profissionalEscola;

                $dataFim = $profissionalAtividade->ed22_datafim;
                $dataFim = $dataFim ? $dataFim->format("d-m-Y") : null;

                $dataSaida = $profissionalEscola->ed75_i_saidaescola;
                $dataSaida = $dataSaida ? $dataSaida->format("d-m-Y") : null;

                $dataIngresso = $profissionalEscola->ed75_d_ingresso;
                $dataIngresso = $dataIngresso ? $dataIngresso->format("d-m-Y") : null;

                $dadosProcessados[] = [
                    'cgm' => $cgm->z01_numcgm,
                    'matricula' => isset($matricula->rh01_regist) ? $matricula->rh01_regist : "----",
                    'nome' => $cgm->z01_nome,
                    'nomeSocial' => isset($cgm->cgmFisico)?$cgm->cgmFisico->z04_nomesocial:$cgm->z01_nome,
                    'cpf' => $cgm->z01_cgccpf,
                    'atividade' => rtrim($profissionalAtividade->atividade->ed01_c_descr),
                    'regime' => rtrim($regimeDescr),
                    'dataSaida' => $dataSaida,
                    'dataIngresso' => $dataIngresso,
                    'dataFim' => $dataFim,
                    'turno' => isset($profissionalAtividade->turnoReferente->turno->ed15_c_nome) ?
                            $profissionalAtividade->turnoReferente->turno->ed15_c_nome :
                            'SEM TURNO',
                    'situacaoServ' => $dataSaida == null ? 'Ativo' : 'Inativo',
                    'situacaoAtiv' => $dataFim == null ? 'Ativa' : 'Inativa',
                    'ativo' => $profissionalAtividade->ed22_ativo,
                    'escola' => rtrim($escola->ed18_c_nome),
                    'cod_escola' => $escola->ed18_i_codigo,
                ];
            }
        }
        $timeFimDadosProcessados = microtime(true);
        $timeInicioOrdenamento = microtime(true);
        // Função de ordenação com base no nome ou atividade
        $order = strtolower($filtros['order']);

        $compareFunction = function ($a, $b) use ($order) {
            $key = ($order === 'nome') ? 'nome' : 'atividade';
            return strcmp($a[$key], $b[$key]);
        };

        usort($dadosProcessados, $compareFunction);
        $timeFimOrdenamento = microtime(true);
        $atividadesArray = [];
        $escolasArray = [];
        $dadosDaEscola = [];
        $dadosPorEscola = [];
        $atividadesTotalizador = [];
        $atividadesTotalizadorCgm = [];
        $atividadesTotalizadorAtivas = [];
        $atividadesTotalizadorInativas = [];


        $timeInicioAgrupamentoEscola = microtime(true);
        // Salvando lista de atividades e agrupando dados por escola
        foreach ($dadosProcessados as $dado) {
            $atividade = trim($dado['atividade']);
            if (!empty($atividade) && !in_array($atividade, $atividadesArray)) {
                $atividadesArray[] = $atividade;
            }

            if ($filtros['DB_coddepto'] == $dado['cod_escola'] && $bModuloEscola) {
                $escola = $dado['escola'];
                if (!isset($dadosDaEscola[$escola])) {
                    $indice = $dado['escola'];
                    $dadosDaEscola[$escola] = [];
                }
                $dadosDaEscola[$escola][] = $dado;
            } elseif (!$bModuloEscola) {
                $escola = $dado['escola'];
                if (!isset($dadosPorEscola[$escola])) {
                    $dadosPorEscola[$escola] = [];
                }
                $dadosPorEscola[$escola][] = $dado;
            }
        }
        $timeFimAgrupamentoEscola = microtime(true);

        $listaAtividades = implode(', ', $atividadesArray);
        $timeInicioDadosFiltrados = microtime(true);
        // Filtrando os dados conforme situação
        $dadosFiltrados = [];

        function atendeFiltros($dado, $filtros)
        {
            $situacaoAtividades = $filtros['codSituacaoAtividades'];
            $situacaoServidores = $filtros['codSituacaoServidores'];

            $situacaoAtiv = $dado['situacaoAtiv'];
            $situacaoServ = $dado['situacaoServ'];

            // Lógica de atender aos filtros
            if ((
                ($situacaoAtividades == 1 && $situacaoAtiv == 'Ativa') ||
                ($situacaoAtividades == 2 && $situacaoAtiv == 'Inativa') ||
                ($situacaoAtividades == 3)
                )
                &&
                (
                ($situacaoServidores == 1 && $situacaoServ == 'Ativo') ||
                ($situacaoServidores == 2 && $situacaoServ == 'Inativo') ||
                ($situacaoServidores == 3)
                )
            ) {
                return true; // Atende aos filtros
            }
            return false; // Não atende aos filtros
        }

        if (empty($dadosDaEscola)) {
            foreach ($dadosPorEscola as $escola) {
                foreach ($escola as $dado) {
                    $atividade = trim($dado['atividade']);
                    $cgm = $dado['cgm'];
                    $dataFim = $dado['dataFim'];
                    $dataSaida = $dado['dataSaida'];

                    if (!isset($atividadesTotalizador[$atividade])) {
                        $atividadesTotalizador[$atividade] = 0;
                        $atividadesTotalizadorCgm[$atividade] = [];
                        $atividadesTotalizadorAtivas[$atividade] = 0;
                        $atividadesTotalizadorInativas[$atividade] = 0;
                    }

                    if (!array_key_exists($cgm, $atividadesTotalizadorCgm[$atividade])) {
                        $atividadesTotalizadorCgm[$atividade][$cgm] = [];
                        $atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'] = [$dataFim];
                        $atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'] = [$dataSaida];

                        $atividadesTotalizador[$atividade]++;

                        $statusServ = $dado['situacaoServ'];
                        if ($statusServ == 'Ativo') {
                            $atividadesTotalizadorAtivas[$atividade]++;
                        } elseif ($statusServ == 'Inativo') {
                            $atividadesTotalizadorInativas[$atividade]++;
                        }

                        if (atendeFiltros($dado, $filtros)) {
                            $dadosFiltrados[] = $dado;
                            $school = trim($dado['escola']);
                            if (!empty($school) && !in_array($school, $escolasArray)) {
                                $escolasArray[] = $school;
                            }
                        }
                    }

                    $dataFimRepetida = false;
                    $dataSaidaRepetida = false;

                    // Verifica se a $dataFim se repete em todos os registros já contabilizados para esse $cgm
                    foreach ($atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'] as $registroDataFim) {
                        if ($registroDataFim === $dataFim) {
                            $dataFimRepetida = true;
                            break;
                        }
                    }
                    foreach ($atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'] as $registroDataSaida) {
                        if ($registroDataSaida === $dataSaida) {
                            $dataSaidaRepetida = true;
                            break;
                        }
                    }

                    if (!$dataFimRepetida || !$dataSaidaRepetida) {
                        $atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'][] = $dataFim;
                        $atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'][] = $dataSaida;

                        $atividadesTotalizador[$atividade]++;

                        $statusServ = $dado['situacaoServ'];
                        if ($statusServ == 'Ativo') {
                            $atividadesTotalizadorAtivas[$atividade]++;
                        } elseif ($statusServ == 'Inativo') {
                            $atividadesTotalizadorInativas[$atividade]++;
                        }

                        if (atendeFiltros($dado, $filtros)) {
                            $dadosFiltrados[] = $dado;
                            $school = trim($dado['escola']);
                            if (!empty($school) && !in_array($school, $escolasArray)) {
                                $escolasArray[] = $school;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($dadosDaEscola[$indice] as $dado) {
                $atividade = trim($dado['atividade']);
                $cgm = $dado['cgm'];
                $dataFim = $dado['dataFim'];
                $dataSaida = $dado['dataSaida'];

                if (!isset($atividadesTotalizador[$atividade])) {
                    $atividadesTotalizador[$atividade] = 0;
                    $atividadesTotalizadorCgm[$atividade] = [];
                    $atividadesTotalizadorAtivas[$atividade] = 0;
                    $atividadesTotalizadorInativas[$atividade] = 0;
                }

                if (!array_key_exists($cgm, $atividadesTotalizadorCgm[$atividade])) {
                    $atividadesTotalizadorCgm[$atividade][$cgm] = [];
                    $atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'] = [$dataFim];
                    $atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'] = [$dataSaida];

                    $atividadesTotalizador[$atividade]++;

                    $statusServ = $dado['situacaoServ'];
                    if ($statusServ == 'Ativo') {
                        $atividadesTotalizadorAtivas[$atividade]++;
                    } elseif ($statusServ == 'Inativo') {
                        $atividadesTotalizadorInativas[$atividade]++;
                    }

                    if (atendeFiltros($dado, $filtros)) {
                        $dadosFiltrados[] = $dado;
                        $school = trim($dado['escola']);
                        if (!empty($school) && !in_array($school, $escolasArray)) {
                            $escolasArray[] = $school;
                        }
                    }
                }

                $dataFimRepetida = false;
                $dataSaidaRepetida = false;

                // Verifica se a $dataFim se repete em todos os registros já contabilizados para esse $cgm
                foreach ($atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'] as $registroDataFim) {
                    if ($registroDataFim === $dataFim) {
                        $dataFimRepetida = true;
                        break;
                    }
                }
                foreach ($atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'] as $registroDataSaida) {
                    if ($registroDataSaida === $dataSaida) {
                        $dataSaidaRepetida = true;
                        break;
                    }
                }

                if (!$dataFimRepetida || !$dataSaidaRepetida) {
                    $atividadesTotalizadorCgm[$atividade][$cgm]['dataFim'][] = $dataFim;
                    $atividadesTotalizadorCgm[$atividade][$cgm]['dataSaida'][] = $dataSaida;

                    $atividadesTotalizador[$atividade]++;

                    $statusServ = $dado['situacaoServ'];
                    if ($statusServ == 'Ativo') {
                        $atividadesTotalizadorAtivas[$atividade]++;
                    } elseif ($statusServ == 'Inativo') {
                        $atividadesTotalizadorInativas[$atividade]++;
                    }

                    if (atendeFiltros($dado, $filtros)) {
                        $dadosFiltrados[] = $dado;
                        $school = trim($dado['escola']);
                        if (!empty($school) && !in_array($school, $escolasArray)) {
                            $escolasArray[] = $school;
                        }
                    }
                }
            }
        }
        $timeFimDadosFiltrados = microtime(true);

        $timers = [
            'Repository' => ($timeFimRepository - $timeInicioRepository),
            'DadosProcessados' => ($timeFimDadosProcessados - $timeInicioDadosProcessados),
            'Ordenamento' => ($timeFimOrdenamento - $timeInicioOrdenamento),
            'AgrupamentoEscola' => ($timeFimAgrupamentoEscola - $timeInicioAgrupamentoEscola),
            'DadosFiltrados' => ($timeFimDadosFiltrados - $timeInicioDadosFiltrados),
        ];

        $resultados = [
            'dados' => $dadosFiltrados,
            'escolas' => $escolasArray,
            'listaAtividades' => $listaAtividades,
            'filtros' => $filtros,
            'totalizador' => $atividadesTotalizador,
            'totalizadorAtivas' => $atividadesTotalizadorAtivas,
            'totalizadorInativas' => $atividadesTotalizadorInativas,
            'timers' => $timers
        ];

        return $resultados;
    }
}
