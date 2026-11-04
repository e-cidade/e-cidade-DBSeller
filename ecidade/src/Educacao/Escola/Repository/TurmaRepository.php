<?php

namespace ECidade\Educacao\Escola\Repository;

use function Symfony\Component\Debug\Tests\testHeader;

class TurmaRepository
{
    private $dao;
    private $perEscolaRepository;
    private $regenciaRepository;
    private $regenciaHorarioRepository;

    private $turmaTurnoReferenteRepository;
    private $martriculaTurnoReferenteRepository;
    private $conteudoRepository;

    public function __construct()
    {
        $this->dao = new \cl_turma();
        $this->perEscolaRepository = new \ECidade\Educacao\Escola\Repository\PeriodoEscolaRepository();
        $this->regenciaRepository = new \ECidade\Educacao\Escola\Repository\RegenciaRepository();
        $this->regenciaHorarioRepository = new \ECidade\Educacao\Escola\Repository\RegenciaHorarioRepository();
        $this->turmaTurnoReferenteRepository = new \ECidade\Educacao\Escola\Repository\TurmaTurnoReferenteRepository();
        $this->conteudoRepository = new \ECidade\Educacao\Escola\Repository\ConteudoDesenvolvidoRepository();
        $this->martriculaTurnoReferenteRepository =
            new \ECidade\Educacao\Escola\Repository\MatriculaTurnoReferenteRepository();
    }

    /**
     * @throws \DBException
     * @throws \Exception
     */
    public function trocarTurnoTurma($parametros)
    {
        $turmaAntes = new \Turma($parametros['ed57_i_codigo']);
        $turnoAntes = $turmaAntes->getTurno();
        $turnoDepois = $parametros['ed57_i_turno'];

        if ($turnoAntes->getCodigoTurno() !== $turnoDepois) {
            $novoTurno = new \Turno($turnoDepois);
            $turnosReferentes = $novoTurno->getTurnoReferente();
            $vagas = [
                'manha' =>  isset($parametros['vagasmanha']) ? $parametros['vagasmanha'] : 0,
                'tarde' => isset($parametros['vagastarde']) ? $parametros['vagastarde'] : 0,
                'noite' => isset($parametros['vagasnoite']) ? $parametros['vagasnoite'] : 0,
                'turma' => isset($parametros['vagasTurma']) ? $parametros['vagasTurma'] : 0
            ];

            $this->desvinculaRegenciasHorario($turmaAntes->getCodigo(), $turnoDepois);
            $this->atualizaTurmaTurnoReferente($turmaAntes, $turnosReferentes, $vagas);
        }
        return true;
    }

    public function desvinculaRegenciasHorario($codigoTurma, $turnoSelcionado)
    {
        $regencias = $this->regenciaRepository->getByTurma($codigoTurma, 'ed59_i_codigo');
        $codigosRegencias = array_column($regencias, 'ed59_i_codigo');
        $periodos = $this->regenciaHorarioRepository->getPeriodosAulaByRegencia($codigosRegencias);
        $escola = db_getsession('DB_coddepto');
        $hoje = date('Y-m-d', $_SESSION['DB_datausu']);

        foreach ($periodos as $periodo) {
            $periodoCorrespondente = $this->perEscolaRepository->getByPeriodoAulaEscolaTurno(
                $escola,
                $periodo['ed08_i_codigo'],
                $turnoSelcionado,
                'ed17_i_codigo'
            );

            $parametros = [
                'ed58_datafim' => $hoje,
                'ed58_ativo' => false,
                'ed58_i_periodo' => $periodoCorrespondente['ed17_i_codigo']
            ];

            $resposta = $this->regenciaHorarioRepository->update($periodo['ed58_i_codigo'], $parametros);
            if (!$resposta) {
                throw new Exception('Falha ao desvincular regencia horário');
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function atualizaTurmaTurnoReferente(\Turma $turma, $turnosReferentes, $vagas)
    {
        $turmaTurnoReferente = $this->turmaTurnoReferenteRepository->getByTurma(
            $turma->getCodigo()
        );

        if ($turma->getTurno()->isIntegral()) {
            $conteudoAtualizar = [];
            $matriculasExcluir = [];
            //variavel para guardar matricula do aluno de turma integral que foi excluido um turno de seu cadastro
            $matriculaCriar = [];
            foreach ($turmaTurnoReferente as $turnoRefrenteTurma) {
                if ($turnoRefrenteTurma['ed336_turnoreferente'] != $turnosReferentes[0]) {
                    $conteudoAtualizar = $this->conteudoRepository
                        ->scopeTurno($turnoRefrenteTurma['ed336_codigo'])->get();

                    $matriculasExcluir = $this->martriculaTurnoReferenteRepository
                        ->getByTurmaTurnoReferente($turnoRefrenteTurma['ed336_codigo']);
                }
            }

            foreach ($matriculasExcluir as $matricula) {
                $registrosMatriculaTurnoReferente =
                    $this->martriculaTurnoReferenteRepository->getByMatricula($matricula['ed337_matricula']);

                if (count($registrosMatriculaTurnoReferente) === 1) {
                    $matriculaCriar[] = $matricula['ed337_matricula'];
                }

                $pk = $matricula['ed337_codigo'];
                $resposta = $this->martriculaTurnoReferenteRepository->excluir($pk);
                if (!$resposta) {
                    throw new Exception('Falha ao atualizar matriculaturnoreferente');
                }
            }

            foreach ($turmaTurnoReferente as $turnoRefrenteTurma) {
                if ($turnoRefrenteTurma['ed336_turnoreferente'] == $turnosReferentes[0]) {
                    foreach ($conteudoAtualizar as $conteudo) {
                        $conteudo->setCodigoTurmaTurnoReferente($turnoRefrenteTurma['ed336_codigo']);
                        $this->conteudoRepository->salvar($conteudo);
                    }

                    foreach ($matriculaCriar as $matricula) {
                        $parametros = [
                            'ed337_codigo' => null,
                            'ed337_matricula' => $matricula,
                            'ed337_turmaturnoreferente' => $turnoRefrenteTurma['ed336_codigo']
                        ];
                        $this->martriculaTurnoReferenteRepository->save($parametros);
                    }
                }
            }

            foreach ($turmaTurnoReferente as $turnoRefrenteTurma) {
                if ($turnoRefrenteTurma['ed336_turnoreferente'] != $turnosReferentes[0]) {
                    $this->turmaTurnoReferenteRepository->excluir($turnoRefrenteTurma['ed336_codigo']);
                }
            }
        } else {
            $pk = $turmaTurnoReferente[0]['ed336_codigo'];
            if (count($turnosReferentes) > 1) {
                foreach ($turnosReferentes as $turnoReferente) {
                    $numVagas = $this->calculaVagas($vagas, $turnoReferente);
                    if ($turmaTurnoReferente[0]['ed336_turnoreferente'] != $turnoReferente) {
                        $parametros = [
                            'ed336_turma' => $turma->getCodigo(),
                            'ed336_turnoreferente' => $turnoReferente,
                            'ed336_vagas' => $numVagas
                        ];
                        $resposta = $this->turmaTurnoReferenteRepository->save($parametros);

                        if (!$resposta) {
                            throw new Exception('Falha ao salvar turmaturnoreferente');
                        }

                        $matriculasIncluir = $this->martriculaTurnoReferenteRepository
                            ->getByTurmaTurnoReferente($turmaTurnoReferente[0]['ed336_codigo']);

                        foreach ($matriculasIncluir as $matricula) {
                            $parametros = [
                                'ed337_matricula' => $matricula['ed337_matricula'],
                                'ed337_turmaturnoreferente' => $resposta->ed336_codigo
                            ];

                            $response = $this->martriculaTurnoReferenteRepository->save($parametros);
                            if (!$response) {
                                throw new Exception('Falha ao salvar matriculaturnoreferente');
                            }
                        }
                    } else {
                        $parametros = ['ed336_vagas' => $numVagas];
                        $resposta =  $this->turmaTurnoReferenteRepository->update($pk, $parametros);
                        if (!$resposta) {
                            throw new Exception('Falha ao atualizar turmaturnoreferente');
                        }
                    }
                }
            } else {
                $parametros = ['ed336_turnoreferente' => $turnosReferentes[0]];
                $resposta =  $this->turmaTurnoReferenteRepository->update($pk, $parametros);
                if (!$resposta) {
                    throw new Exception('Falha ao atualizar turmaturnoreferente');
                }
            }
        }
    }

    public function calculaVagas($vagas, $turno)
    {
        $numVagas = 0;

        switch ($turno) {
            case 1:
                $numVagas = $vagas['manha'] > 0 ? $vagas['manha'] : $vagas['turma'];
                break;
            case 2:
                $numVagas = $vagas['tarde'] > 0 ? $vagas['tarde'] : $vagas['turma'];
                break;
            case 3:
                $numVagas = $vagas['noite'];
                break;
        }
        return $numVagas;
    }
}
