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

namespace App\Domain\Educacao\CentralMatriculas\Services;

use App\Domain\Educacao\Escola\Resources\EtapaResource;
use App\Domain\Educacao\MatriculaOnline\Models\Candidato;
use App\Domain\Educacao\MatriculaOnline\Models\Candidatura;
use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use App\Domain\Educacao\MatriculaOnline\Resources\CandidaturaResource;
use App\Domain\Educacao\MatriculaOnline\Resources\FaseResource;
use App\Domain\Educacao\CentralMatriculas\Models\CBOOcupacao;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\Matricula;
use App\Domain\Educacao\MatriculaOnline\Models\Alocado;

class ProcessoInscricaoService
{
    public function getEtapasFasePorData($fase, $data)
    {
        $fase = Fase::find($fase);
        $idade = $this->getIdade($fase->mo04_datacorte, new Carbon($data));
        $etapas = [];
        foreach ($fase->ciclo->ciclosEnsino as $cicloEnsino) {
            foreach ($cicloEnsino->ensino->etapas as $etapa) {
                $collectidadeEtapa = $etapa
                    ->idadeEtapa()
                    ->whereRaw("'{$idade}'::interval between mo15_idadeinicial and mo15_idadefinal")->get();

                if ($collectidadeEtapa->count() > 0) {
                    $etapas[] = EtapaResource::toResponse($collectidadeEtapa->first()->etapa);
                }
            }
        }
        return $etapas;
    }

    public function getFasePorData($data)
    {
        $idade = $this->getIdade($this->getDataCorte(), new Carbon($data));
        $fase = Fase::whereHas('ciclo', function (Builder $query) use ($idade) {
            $query->whereHas('ciclosEnsino', function (Builder $query) use ($idade) {
                $query->whereHas('ensino', function (Builder $query) use ($idade) {
                    $query->whereHas('etapas', function (Builder $query) use ($idade) {
                        $query->whereHas('idadeEtapa', function (Builder $query) use ($idade) {
                            $query->whereRaw("'{$idade}'::interval between mo15_idadeinicial and mo15_idadefinal");
                        });
                    });
                });
            });
        })->where('mo04_encerrada', false)->get()->filter(function ($fase) {
            $dataI = explode("-", $fase->mo04_dtini->format('Y-m-d'));
            $horaI = explode(":", $fase->mo04_hora_inicial);
            $dataF = explode("-", $fase->mo04_dtfim->format('Y-m-d'));
            $horaF = explode(":", $fase->mo04_hora_final);
            $dataI = Carbon::create(
                $dataI[0],
                $dataI[1],
                $dataI[2],
                $horaI[0],
                $horaI[1],
                $horaI[2],
                'America/Sao_Paulo'
            );
            $dataF = Carbon::create(
                $dataF[0],
                $dataF[1],
                $dataF[2],
                $horaF[0],
                $horaF[1],
                $horaF[2],
                'America/Sao_Paulo'
            );
            if (Carbon::now()->between($dataI, $dataF)) {
                return $fase;
            }
        })->first();
        if (!empty($fase)) {
            $fase = FaseResource::toResponse($fase);
            $fase->etapas = $this->getEtapasFasePorData($fase->codigo, $data);
        }
        return $fase;
    }

    public function getFasePorDataInterno($data)
    {
        $idade = $this->getIdade($this->getDataCorte(), new Carbon($data));
        $fase = Fase::whereHas('ciclo', function (Builder $query) use ($idade) {
            $query->whereHas('ciclosEnsino', function (Builder $query) use ($idade) {
                $query->whereHas('ensino', function (Builder $query) use ($idade) {
                    $query->whereHas('etapas', function (Builder $query) use ($idade) {
                        $query->whereHas('idadeEtapa', function (Builder $query) use ($idade) {
                            $query->whereRaw("'{$idade}'::interval between mo15_idadeinicial and mo15_idadefinal");
                        });
                    });
                });
            });
        })->where('mo04_encerrada', false)->get()->first();
        if (!empty($fase)) {
            $fase = FaseResource::toResponse($fase);
            $fase->etapas = $this->getEtapasFasePorData($fase->codigo, $data);
        }
        return $fase;
    }

    public function getIdade($data, $dataCorte)
    {
        $dateInterval = $data->diff($dataCorte);
        return "{$dateInterval->y} years {$dateInterval->m} months";
    }

    public function getDataCorte()
    {
        $fase = Fase::where('mo04_encerrada', false)->first();
        return $fase->mo04_datacorte;
    }

    /**
     * @throws Exception
     */
    public function consultaInscricao($tipo, $valor, $cpfAluno = null, $edicao = false)
    {
        $edicao = $edicao === 'true';

        if (!is_null($cpfAluno)) {
            $candidatos = Candidato::where('mo01_cpf', preg_replace('/[^0-9]/', '', $cpfAluno))
                ->orWhere('mo01_rnm', $cpfAluno)
                ->orWhere('mo01_visto', $cpfAluno)->get();
            if ($candidatos->count() === 0) {
                throw new Exception('Nenhum candidato encontrado com este CPF!');
            }
        }
        $candidaturas = [];
        switch ($tipo) {
            case 'cpf':
                $query = Candidato::where('mo01_cpfresp', $valor);
                if (!is_null($cpfAluno)) {
                    $query->where('mo01_cpf', preg_replace('/[^0-9]/', '', $cpfAluno))
                        ->orWhere('mo01_rnm', $cpfAluno)
                        ->orWhere('mo01_visto', $cpfAluno);
                }
                $candidatos = $query->get();
                if ($candidatos->count() === 0) {
                    throw new Exception('Nenhum candidato encontrado para este CPF de responsável!');
                }
                $candidatos = $candidatos->filter(function ($candidato) {
                    return $candidato->candidaturas()->whereHas('fase', function (Builder $query) {
                        $query->where('mo04_encerrada', false);
                    })->get()->count() > 0;
                });
                foreach ($candidatos as $candidato) {
                    foreach ($candidato->candidaturas as $candidatura) {
                        $candidaturas[] = $candidatura;
                    }
                }
                break;
            case 'protocolo':
                $fase = Fase::find(intval(explode("-", $valor)[0]));

                if (is_null($fase)) {
                    throw new Exception("Protocolo Inválido");
                }
                if ($fase->mo04_encerrada) {
                    throw new Exception('O protocolo informado é de uma fase já encerrada!');
                }
                $query = Candidatura::where('mo12_protocolo', $valor);

                if (!is_null($cpfAluno)) {
                    $query->whereHas('candidato', function ($query) use ($cpfAluno) {
                        $query->where('mo01_cpf', preg_replace('/[^0-9]/', '', $cpfAluno))
                            ->orWhere('mo01_rnm', $cpfAluno)
                            ->orWhere('mo01_visto', $cpfAluno);
                    });
                }
                $candidato = $query->get();

                if ($candidato->count() === 0) {
                    throw new Exception('Nenhum candidato encontrado para este protocolo!');
                }

                $candidaturas = [$candidato->first()];
                break;
            case 'rneVisto':
                $queryRne = Candidato::where('mo01_rnmresponsavel', $valor);
                $queryVisto =  Candidato::where('mo01_vistoresponsavel', $valor);
                if (!is_null($cpfAluno)) {
                    $queryRne->where('mo01_cpf', preg_replace('/[^0-9]/', '', $cpfAluno))
                        ->orWhere('mo01_rnm', $cpfAluno)
                        ->orWhere('mo01_visto', $cpfAluno);

                    $queryVisto->where('mo01_cpf', preg_replace('/[^0-9]/', '', $cpfAluno))
                        ->orWhere('mo01_rnm', $cpfAluno)
                        ->orWhere('mo01_visto', $cpfAluno);
                }

                $candidatosRne = $queryRne->get();
                $candidatosVisto = $queryVisto->get();

                if ($candidatosRne->count() === 0 && $candidatosVisto->count() === 0) {
                    throw new Exception('Nenhum candidato encontrado para estes dados do responsável!');
                }

                $candidatos = $candidatosVisto->merge($candidatosRne);
                $candidatos = $candidatos->filter(function ($candidato) {
                    return $candidato->candidaturas()->whereHas('fase', function (Builder $query) {
                        $query->where('mo04_encerrada', false);
                    })->get()->count() > 0;
                });

                foreach ($candidatos as $candidato) {
                    foreach ($candidato->candidaturas as $candidatura) {
                        $candidaturas[] = $candidatura;
                    }
                }
                break;
        }

        return collect($candidaturas)->map(function ($candidatura) use ($edicao) {
            return $edicao ?
                CandidaturaResource::toResponseEdicao($candidatura) :
                CandidaturaResource::toResponseConsulta($candidatura);
        });
    }

    public function consultaCandidato($tipo, $valor, $data = null, $bool = false)
    {
        $candidaturas = [];
        $matricula = null;
        $aluno = null;
        $alocado = null;
        $candidato = null;
        $anoBase = date("Y");
        $anoComparacao = null;
        $retornoBool = false;

        if ($data == 'null') {
            $data = null;
        }

        $alunoCampo = "ed47_v_cpf";

        switch ($tipo) {
            case "rnm":
            case "visto":
                $alunoCampo = "ed47_{$tipo}";
                break;
        }

        // Buscamos o aluno
        $aluno = Aluno::with('alunoNecessidade')
            ->with('necessidadeSubdivisaoAluno')
            ->with('paisNascimento')
            ->with('matriculas')
            ->where($alunoCampo, $valor)
            ->orderBy("ed47_i_codigo", "desc")
            ->first();

        // Buscamos o candidato na mobase
        $candidato = Candidato::with('candidaturas')
            ->with('listasEspera')
            ->where("mo01_{$tipo}", $valor)
            ->orderBy("mo01_codigo", "desc")
            ->first();

        // Buscamos dados da matricula
        if (!empty($aluno)) {
            $matricula = Matricula::where('ed60_i_aluno', $aluno->getCodigo())
                ->orderBy('ed60_i_codigo', 'desc')
                ->first();
        }

        // Buscamos o ano da matricula
        if (!empty($matricula)) {
            $anoComparacao = $matricula->ed60_d_datamatricula->format('Y');
        }

        if (!empty($candidato)) {
            $alocado = Alocado::with('situacao')
                ->where('mo13_base', $candidato->mo01_codigo)
                ->orderBy('mo13_codigo', 'desc')
                ->first();
        }

        //Caso nao venha a data
        if (empty($data)) {
            // caso encontremos o aluno, substituimos o campo data pela data de nascimento do aluno
            if (!empty($aluno)) {
                $data = $aluno->getDataNascimento()->format('Y-m-d');
            } else {
                // caso nao encontremos o aluno, procuramos nos candidatos
                if (!empty($candidato)) {
                    $data = $candidato->mo01_dtnasc->format('Y-m-d');
                }
            }
        }

        if (empty($data)) {
            $msg = "É necessário informar a data de nascimento.";
            if (!$bool) {
                return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
            } else {
                return true;
            }
        }

        /* $fase = $this->getFasePorData($data); */
        $fase = $this->getFasePorDataInterno($data);

        if (empty($fase)) {
            $msg = "Nenhuma fase encontrada para a data de Nascimento informada.";
            if (!$bool) {
                return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
            } else {
                return true;
            }
        }

        // validamos as configuracoes de fase
        $redeInterna = false;
        $redeExterna = false;

        foreach ($fase->publicosAlvo as $publicoAlvo) {
            switch ($publicoAlvo->value) {
                case 2:
                    $redeInterna = true;
                    break;
                case 3:
                    $redeExterna = true;
                    break;
            }
        }

        // validamos a fase
        if (!$fase->isEncerrada) {
            if (!empty($candidato)) {
                // Verificamos se o aluno esta em alguma lista de espera
                if (!empty($candidato->listasEspera)) {
                    $possuiListaEspera = false;
                    $possuiListaEsperaInativa = false;
                    foreach ($candidato->candidaturas as $candidatura) {
                        $faseCandidato = Fase::find($candidatura->mo12_fase);
                        if (!$faseCandidato->mo04_encerrada) {
                            foreach ($candidato->listasEspera as $listaEspera) {
                                if ($listaEspera->mo18_status == 1) {
                                    $possuiListaEspera = true;
                                }
                            }
                        } else {
                            $possuiListaEsperaInativa = true;
                        }
                    }
                    if ($possuiListaEspera) {
                        $msg = "Prezado Usuário, O candidato já possui uma inscrição ativa na lista de espera.";
                        if (!$bool) {
                            return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                        } else {
                            return true;
                        }
                    } elseif (!$possuiListaEspera && !$possuiListaEsperaInativa) {
                        if (!empty($alocado)) {
                            if (!$alocado->situacao->mo28_situacao == 3 && !$alocado->situacao->mo28_situacao == 4) {
                                $msg = "Prezado Usuário, A situação do candidato não permite uma nova inscrição";
                                if (!$bool) {
                                    return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                                } else {
                                    return true;
                                }
                            }
                        } else {
                            $msg = "Prezado Usuário, O candidato já possui uma inscrição ativa.";
                            if (!$bool) {
                                return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        // libera todos
        if ($redeExterna && $redeInterna) {
        } else {
            // Libera somente quem tem matricula ativa no ano corrente
            if ($redeInterna) {
                $msg = "{$valor} - Inscrição não permitida. Esta fase é destinada para"
                    . " candidatos que já estão matriculados na rede.";

                if (!empty($aluno)) {
                    if (!empty($matricula)) {
                        $permiteFase = ["MATRICULADO", "TRANSFERIDO REDE"];
                        if (in_array($matricula->ed60_c_situacao, $permiteFase)) {
                            if ($anoBase != $anoComparacao && $matricula->ed60_c_situacao == "MATRICULADO") {
                                if (!$bool) {
                                    return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                                } else {
                                    return true;
                                }
                            }
                        } else {
                            if (!$bool) {
                                return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                            } else {
                                return true;
                            }
                        }
                    } else {
                        if (!$bool) {
                            return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                        } else {
                            return true;
                        }
                    }
                } else {
                    if (!$bool) {
                        return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                    } else {
                        return true;
                    }
                }
            } else {
                // Libera somente quem não tem matricula ativa no ano corrente
                if ($redeExterna) {
                    if (!empty($alocado)) {
                        $bloqueia = [1, 2];
                        if (in_array($alocado->situacao->mo28_situacao, $bloqueia) &&
                            ($alocado->mo13_data->format('Y') === Carbon::now()->format('Y'))) {
                            $msg = "{$valor} - Inscrição não permitida. Esta fase é destinada para"
                            . " candidatos que não estão matriculados na rede.";
                            if (!$bool) {
                                return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                            } else {
                                return true;
                            }
                        }
                    }
                    if (!empty($aluno)) {
                        if (!empty($matricula)) {
                            if ($matricula->ed60_c_situacao == "MATRICULADO") {
                                if ($anoBase == $anoComparacao) {
                                    $msg = "{$valor} Inscrição não permitida. Esta fase é destinada para"
                                        . " candidatos que não estão matriculados na rede.";
                                    if (!$bool) {
                                        return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // Retorno Base
        if (!empty($candidato)) {
            if (isset($candidato->candidaturas)) {
                foreach ($candidato->candidaturas as $candidatura) {
                    $candidaturas[] = $candidatura;
                }
            }

            if (sizeof($candidaturas) == 0) {
                if (!empty($candidato)) {
                    $candidatura =  CandidaturaResource::toResponseEdicao($candidato);
                    unset($candidatura->protocolo);
                    unset($candidatura->fase);
                    unset($candidatura->redeOrigem);
                    unset($candidatura->opcoesEscola);
                    if (!$bool) {
                        return (object) ["erro" => false, "messagem" => "aluno encontrado", "dados" => $candidatura];
                    } else {
                        return $retornoBool;
                    }
                }
                // tentamos mais uma vez buscar os dados
                if (!empty($aluno)) {
                    $retorno =  CandidaturaResource::toResponseAluno($aluno);
                    if (!$bool) {
                        return (object) ["erro" => false, "messagem" => "aluno encontrado", "dados" => $retorno];
                    } else {
                        return $retornoBool;
                    }
                }
                $msg = "A inscrição para a Chamada Escolar não pode ser realizada para alunos já "
                    . "pertencentes à Rede Municipal de Ensino, considerando que o aluno já possui"
                    . " matrícula em escola da Rede.";
                if (!$bool) {
                    return (object) ["erro" => true, "messagem" => $msg, "dados" => null];
                } else {
                    return true;
                }
            }

            $retorno = collect($candidaturas)->map(function ($candidatura) {
                $candidatura =  CandidaturaResource::toResponseEdicao($candidatura);
                unset($candidatura->protocolo);
                unset($candidatura->fase);
                unset($candidatura->redeOrigem);
                unset($candidatura->opcoesEscola);
                return $candidatura;
            });

            if (sizeof($retorno) > 0) {
                if (!$bool) {
                    return (object) ["erro" => false, "messagem" => "candidato encontrado", "dados" => $retorno[0]];
                } else {
                    return $retornoBool;
                }
            }
        }

        if (!empty($aluno)) {
            $retorno =  CandidaturaResource::toResponseAluno($aluno);
            if (!$bool) {
                return (object) ["erro" => false, "messagem" => "aluno encontrado", "dados" => $retorno];
            } else {
                return $retornoBool;
            }
        }
        if (!$bool) {
            return (object) ["erro" => false, "messagem" => "", "dados" => null];
        }

        return $retornoBool;
    }

    public function excluirInscricao($protocolo)
    {
        $candidatura = Candidatura::where('mo12_protocolo', $protocolo)->first();
        $candidato = $candidatura->candidato;
        $candidatura->candidato->basesEscola->map(function ($baseEscola) {
            $baseEscola->opcaoTurno->delete();
        });
        $candidatura->candidato->atestadoNecessidadeEspecial()->delete();
        $candidatura->candidato->basesEscola()->delete();
        $candidatura->candidato->listasEspera()->delete();
        $candidatura->candidato->delete();
        $candidatura->delete();
        return (object)[
            "protocolo" => $protocolo,
            "opcoes" => utf8_encode_all($candidato->opcoesEscola)
        ];
    }

    public function getProfissoes()
    {
        $campos = [
            'mo304_codigo as codigo',
            'mo304_titulo as nome'
        ];
        return CBOOcupacao::query()
        ->select($campos)
        ->orderBy('mo304_titulo')
        ->get();
    }
}
