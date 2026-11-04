<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Educacao\Escola\Models\ConteudoDesenvolvido;
use App\Domain\Educacao\Escola\Models\DisciplinaBNCC;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Repositories\ConteudoDesenvolvidoRepository;
use App\Domain\Educacao\Escola\Repositories\HabilidadeBNCCReferencialCurricularRepository;
use App\Domain\Educacao\Escola\Repositories\HabilidadeDesenvolvidaReferencialRepository;
use App\Domain\Educacao\Escola\Repositories\HabilidadeDesenvolvidaRepository;
use App\Domain\Educacao\Escola\Repositories\HabilidadesBNCCFundamentalRepository;
use App\Domain\Educacao\Escola\Repositories\HabilidadesBNCCInfantilRepository;
use App\Domain\Educacao\Escola\Repositories\ProfissionalEscolaRepository;
use App\Domain\Educacao\Escola\Repositories\RegenciaPeriodoRepository;
use App\Domain\Educacao\Escola\Repositories\RegenciaRepository;
use App\Domain\Educacao\Escola\Repositories\TurmaRepository;
use App\Domain\Educacao\Escola\Resources\ConteudoDesenvolvidoResource;
use App\Domain\Educacao\Escola\Resources\HabilidadeDesenvolvidaResource;
use App\Domain\Educacao\Escola\Resources\HabilidadesBNCCFundamentalResource;
use App\Domain\Educacao\Escola\Resources\HabilidadesBNCCInfantilResource;
use App\Domain\Educacao\Escola\Resources\TurmaResource;
use App\Domain\Educacao\Secretaria\Models\ParametrosGlobais;
use App\Domain\Educacao\Secretaria\Resources\DisicplinaBNCCResource;

class RegistroAulaService
{
    protected $conteudoRepository;
    protected $habilidadeDesenvolvidaRepository;
    protected $profissionalEscolaRepository;
    protected $regenciaRepository;
    protected $habilidadesInfantilRepository;
    protected $habilidadesFundamentalRepository;
    protected $habilidadeReferencialRepository;
    protected $habilidadeDesenvolvidaReferencialRepository;
    protected $turmaRepository;
    protected $configuracao;
    protected $regenciaPeriodoRepository;
    const DISCIPLINA_GLOBALIZADA = 'FA';

    public function __construct()
    {
        $this->conteudoRepository = new ConteudoDesenvolvidoRepository();
        $this->habilidadeDesenvolvidaRepository = new HabilidadeDesenvolvidaRepository();
        $this->habilidadeDesenvolvidaReferencialRepository = new HabilidadeDesenvolvidaReferencialRepository();
        $this->profissionalEscolaRepository = new ProfissionalEscolaRepository();
        $this->regenciaRepository = new RegenciaRepository();
        $this->habilidadesInfantilRepository = new HabilidadesBNCCInfantilRepository();
        $this->habilidadesFundamentalRepository = new HabilidadesBNCCFundamentalRepository();
        $this->habilidadeReferencialRepository = new HabilidadeBNCCReferencialCurricularRepository();
        $this->configuracao = ParametrosGlobais::all()->first();
        $this->turmaRepository = new TurmaRepository();
        $this->regenciaPeriodoRepository = new RegenciaPeriodoRepository();
    }

    public function getDadosRegente($user, $escola, $dataSistema, $periodos = false)
    {
        $user = Usuario::find($user);
        $cgm = $user->usuarioCgm->cgm;
        $turmas = $this->profissionalEscolaRepository
            ->getTurmasRegente($cgm->z01_numcgm, $escola, $dataSistema, $periodos);

        return (object) [
            'nome' => $user->nome,
            'cgm' => $cgm->z01_numcgm,
            'usuario' => $user->id_usuario,
            'turmas' => $turmas
        ];
    }

    public function salvarConteudo($parametros)
    {
        $turma = Turma::find($parametros['turma']);
        $parametros['turmaTurnoReferente'] =  $turma->turnosReferentes
            ->filter(function ($turnoRef) use ($parametros) {
                return $turnoRef->turnoReferente->referencia->value() == $parametros['turnoReferente'];
            })->first()->ed336_codigo;

        $conteudo = $this->conteudoRepository->salvar($parametros);
        $periodo = $this->getPeriodoByConteudo($conteudo);
        $procAvaliacao = $this->getProcedimentoByConteudoPeriodo($conteudo, $periodo);
        if ($procAvaliacao == null) {
            throw new \Exception("Não foi possível salvar o registro de aula.
                                Verifique o procedimento de avaliação da turma.");
        }
        $this->atualizarAulasDadas($periodo, $procAvaliacao, $conteudo->regencia);
        return $conteudo;
    }

    public function getConteudosRegenteEscola($idUser, $idEscola, $linhas, $filtros)
    {
        return $this->conteudoRepository->getConteudosRegenteEscolaPaginator(
            $idUser,
            $idEscola,
            $linhas,
            $filtros
        )->map(function ($conteudo) {
            return ConteudoDesenvolvidoResource::toResponse($conteudo);
        });
    }

    public function excluirConteudo($codigo)
    {
        $conteudo = $this->conteudoRepository->find($codigo);
        $retorno = $this->conteudoRepository->excluir($codigo);

        $periodo = $this->getPeriodoByConteudo($conteudo);
        $procAvaliacao = $this->getProcedimentoByConteudoPeriodo($conteudo, $periodo);
        $this->atualizarAulasDadas($periodo, $procAvaliacao, $conteudo->regencia);
        return $retorno;
    }

    public function getDisciplinasBncc($regencia)
    {
        $disciplina = $this->regenciaRepository->find($regencia)->disciplinaEnsino->disciplina;
        return $disciplina->disciplnasEquivalentesBNCC->map(function ($disciplina) {
            return DisicplinaBNCCResource::toResponse($disciplina->disciplinaBncc);
        });
    }

    public function getHabilidadesBncc($regencia, $disciplinaBncc)
    {
        $disciplina = DisciplinaBNCC::find($disciplinaBncc);
        $regencia = $this->regenciaRepository->find($regencia);
        $ensino = $regencia->disciplinaEnsino->ensino;
        $etapa = $regencia->etapa;
        $ano = $regencia->turma->calendario->ed52_i_ano;

        if ($ensino->isInfantil) {
            $habilidades = $this->habilidadesInfantilRepository->getHabilidadesByDiscilina(
                $ano,
                $disciplina
            );

            if ($this->configuracao->isReferencialCurricularEstadual) {
                $habilidades = $habilidades->filter(function ($hab) {
                    return $hab->referenciaisCurricularEstadual->count() > 0;
                });
            }

            return HabilidadesBNCCInfantilResource::toResponse($habilidades);
        }

        $etapasBncc = $etapa->etapasEquivalentesBncc->map(function ($etapaEquivalente) {
            return $etapaEquivalente->etapaBncc;
        });

        $habilidades = $this->habilidadesFundamentalRepository
            ->getHabilidadesByEtapaDisciplina($ano, $disciplina, $etapasBncc);


        if ($this->configuracao->isReferencialCurricularEstadual) {
            $habilidades = $habilidades->filter(function ($hab) {
                return $hab->referenciaisCurricularEstadual->count() > 0;
            });
        }

        return HabilidadesBNCCFundamentalResource::toResponse($habilidades);
    }

    public function salvarHabilidadeDesenvolvida($parametros)
    {
        $filtros = ['disciplina' => $parametros['disciplina'], 'conteudo' => $parametros['conteudoDesenvolvido']];
        $this->habilidadeDesenvolvidaRepository->deleteByFilters($filtros);
        $habilidadesSalvas = [];
        foreach ($parametros['habilidades'] as $habilidade) {
            $model = $parametros;
            $model['habilidade'] = $habilidade;
            $model = HabilidadeDesenvolvidaResource::toArrayModel($model);
            $habilidadesSalvas[] = $this->habilidadeDesenvolvidaRepository->salvar($model);
        }

        if ($this->configuracao->isReferencialCurricularEstadual) {
            $cale = $this->conteudoRepository->find(
                $parametros['conteudoDesenvolvido']
            )->regencia->turma->calendario;
            $parametros['ano'] = $cale->ed52_i_ano;
            $this->salvarHabilidadeRefrencial($cale->ed52_i_ano, $habilidadesSalvas);
        }

        return $habilidadesSalvas;
    }

    public function salvarHabilidadeRefrencial($ano, $habilidadesSalvas)
    {
        foreach ($habilidadesSalvas as $habilidade) {
            $filtros = [
                'habilidade' => $habilidade->ed156_habilidade,
                'ano' => $ano,
                'referencial' =>true
            ];
            foreach ($this->habilidadeReferencialRepository->getByFilters($filtros) as $ref) {
                $parametros = [
                    'ed169_diario_classe_bncc_habilidade' => $habilidade->ed156_codigo,
                    'ed169_bnccreferencial' => $ref->ed168_codigo
                ];

                $this->habilidadeDesenvolvidaReferencialRepository->salvar($parametros);
            }
        }
    }

    public function getHabilidadesDesenvolvidas($parametros)
    {
        return $this->habilidadeDesenvolvidaRepository->getByFilters(
            $parametros,
            'ed156_habilidade as codigo'
        );
    }

    public function getAulasDadasByTurma($codigoTurma)
    {
        $medidaFrequencia = TurmaResource
            ::toResponse(
                $this->turmaRepository->find($codigoTurma),
                ['etapa', 'turnoReferente']
            )->medidaFrequencia;
        $registros = $this->conteudoRepository->getConteudosByTurma($codigoTurma);
        $aulasDadas = 0;
        if ($medidaFrequencia === 'P') {
            foreach ($registros as $registro) {
                $aulasDadas += (int) $registro->ed155_aulas_dadas;
            }

            return $aulasDadas;
        }

        if ($medidaFrequencia === 'D') {
            $registroPorData = [];
            foreach ($registros as $registro) {
                $registroPorData[$registro->ed155_data] = $registro->ed155_aulas_dadas;
            }
            return count($registroPorData);
        }
    }

    public function atualizarAulasDadas($periodo, $procAvaliacao, $regencia)
    {
        $aulasDadas = 0;
        $filtros['regencia'] = $regencia->ed59_i_codigo;
        $filtros['periodo'] = $periodo;
        if (trim($regencia->turma->ed57_c_medfreq) === 'DIAS LETIVOS') {
            if ($regencia->turma->base->ed31_c_contrfreq === 'G') {
                $filtros['regencia'] = $regencia->turma->regencias->map(function ($regencia) {
                    return $regencia->ed59_i_codigo;
                })->toArray();
            }
        }
        $etapasDistintasDaTurma = array_unique($regencia->turma->regencias
            ->map(function ($regenciaLista) {
                return $regenciaLista->ed59_i_serie;
            })->toArray());

        $isComportamentoTipoTurmaMultiEtapa = count($etapasDistintasDaTurma) > 1;

        $registros = $this->conteudoRepository->getByFiltros($filtros);
        if (trim($regencia->turma->ed57_c_medfreq) === 'DIAS LETIVOS') {
            $pordia = [];
            foreach ($registros as $registro) {
                $chave = $registro->ed155_data;
                if ($isComportamentoTipoTurmaMultiEtapa) {
                    $regenciaBusca = $this->regenciaRepository->find($registro->ed155_regencia);
                    $chave = "{$regenciaBusca->ed59_i_serie}#{$registro->ed155_data}";
                }
                $pordia[$chave] = $registro;
            }
            $aulasDadas = count($pordia);
            if ($isComportamentoTipoTurmaMultiEtapa) {
                $contagemAulasDadas = 0;
                foreach ($pordia as $conteudoDia) {
                    $regenciaGravadaContagem = $this->regenciaRepository->find($conteudoDia['ed155_regencia']);
                    if ($regenciaGravadaContagem->ed59_i_serie == $regencia->ed59_i_serie
                        && $regenciaGravadaContagem->ed59_i_turma == $regencia->ed59_i_turma
                    ) {
                        $contagemAulasDadas += $conteudoDia['ed155_aulas_dadas'];
                    }
                }
                $aulasDadas = $contagemAulasDadas;
            }
        } else {
            foreach ($registros as $registro) {
                $aulasDadas += $registro->ed155_aulas_dadas;
            }
        }

        if (trim($regencia->turma->ed57_c_medfreq) === 'DIAS LETIVOS') {
            if ($regencia->turma->base->ed31_c_contrfreq === 'G') {
                $regenciaCodigo = $regencia->turma->regenciaGlobal->ed59_i_codigo;
                if ($isComportamentoTipoTurmaMultiEtapa) {
                    $regenciaCodigo = $this->getCodigoRegenciaGlobalTurmaMultiEtapaByRegenciaEEtapa(
                        $regencia,
                        $regencia->etapa->ed11_i_codigo
                    );
                }
                if (isset($regenciaCodigo)) {
                    return $this->regenciaPeriodoRepository
                        ->salvar(
                            $regenciaCodigo,
                            $procAvaliacao->ed41_i_codigo,
                            $aulasDadas
                        );
                }
            }
        }
        return $this->regenciaPeriodoRepository
            ->salvar(
                $regencia->ed59_i_codigo,
                $procAvaliacao->ed41_i_codigo,
                $aulasDadas
            );
    }

    public function getPeriodoByConteudo($conteudo)
    {
         return $conteudo->regencia->turma->calendario->periodosCalendario()
            ->where('ed53_d_inicio', '<=', $conteudo->ed155_data)
            ->where('ed53_d_fim', '>=', $conteudo->ed155_data)->first();
    }

    public function getProcedimentoByConteudoPeriodo($conteudo, $periodo)
    {
        return $conteudo->regencia->procedimentoAvaliacao->procedimentosAvaliacao()
            ->where('ed41_i_periodoavaliacao', $periodo->ed53_i_periodoavaliacao)->first();
    }

    public function updateConteudosLote(array $conteudos)
    {
        $resposta = $this->conteudoRepository->updateConteudosLote($conteudos);

        foreach ($conteudos as $cont) {
            $conteudo = ConteudoDesenvolvido::find($cont['codigo']);
            $periodo = $this->getPeriodoByConteudo($conteudo);
            $procAvaliacao = $this->getProcedimentoByConteudoPeriodo($conteudo, $periodo);
            $this->atualizarAulasDadas($periodo, $procAvaliacao, $conteudo->regencia);
        }
        return $resposta;
    }

    private function getCodigoRegenciaGlobalTurmaMultiEtapaByRegenciaEEtapa($regencia, $etapaId)
    {
        $regenciaGlobalMultiEtapa = null;
        $regenciaGlobalMultiEtapa = $regencia->turma->regencias
            ->filter(function ($regenciaLista) use ($etapaId) {
                return $regenciaLista->ed59_i_serie === $etapaId
                && $regenciaLista->ed59_c_freqglob === $this::DISCIPLINA_GLOBALIZADA;
            })->first();

        if (is_null($regenciaGlobalMultiEtapa)) {
            throw new \Exception("Não foi possível localizar a disciplina global da turma!");
        }
        return $regenciaGlobalMultiEtapa->ed59_i_codigo;
    }
}
