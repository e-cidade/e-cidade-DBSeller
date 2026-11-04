<?php

namespace App\Domain\Educacao\TransporteEscolar\Services;

use BusinessException;
use cl_alunonecessidade;
use cl_necessidade;
use cl_necessidadesubdivisao;
use cl_necessidadesubdivisaoaluno;
use DateTime;
use db_utils;
use Illuminate\Support\Facades\DB;
use ItinerarioVinculoAlunoRepository;
use LinhaItinerario;
use LinhaTransporte;
use ParameterException;
use stdClass;

class CarteiraTransporteService
{
    public function getLinhas($escola)
    {
        $retorno = DB::table('linhatransporte')
            ->select('tre06_sequencial', 'tre06_nome', 'tre06_abreviatura')
            ->whereExists(function ($query) use ($escola) {
                $query->select(DB::raw(1))
                    ->from('linhatransporte as lt')
                    ->leftJoin(
                        'linhatransporteitinerario',
                        'linhatransporteitinerario.tre09_linhatransporte',
                        '=',
                        'lt.tre06_sequencial'
                    )
                    ->leftJoin(
                        'itinerariologradouro',
                        'itinerariologradouro.tre10_linhatransporteitinerario',
                        '=',
                        'linhatransporteitinerario.tre09_sequencial'
                    )
                    ->leftJoin(
                        'linhatransportepontoparada',
                        'linhatransportepontoparada.tre11_itinerariologradouro',
                        '=',
                        'itinerariologradouro.tre10_sequencial'
                    )
                    ->leftJoin(
                        'pontoparada',
                        'pontoparada.tre04_sequencial',
                        '=',
                        'linhatransportepontoparada.tre11_pontoparada'
                    )
                    ->leftJoin(
                        'pontoparadadepartamento',
                        'pontoparadadepartamento.tre05_pontoparada',
                        '=',
                        'linhatransportepontoparada.tre11_pontoparada'
                    )
                    ->leftJoin(
                        'pontoparadaescolaproc',
                        'pontoparadaescolaproc.tre13_pontoparada',
                        '=',
                        'pontoparada.tre04_sequencial'
                    )
                    ->where('pontoparadadepartamento.tre05_db_depart', $escola)
                    ->whereColumn('lt.tre06_sequencial', 'linhatransporte.tre06_sequencial');
            })
            ->orderBy('tre06_sequencial')
            ->get();

        return $retorno;
    }

    /**
     * @throws ParameterException
     * @throws BusinessException
     */
    public function getAlunosVinculados($parametros)
    {
        $oParam = $parametros;
        $oRetorno = new stdClass();
        $oRetorno->aAlunos = array();

        if (!empty($oParam['iLinha'])) {
            $oLinha = new LinhaTransporte($oParam['iLinha']);
            foreach ($oLinha->getItinerarios() as $oLinhaItinerario) {
                foreach ($oLinhaItinerario->getLogradouros() as $oLinhaItinerarioLogradouro) {
                    foreach ($oLinhaItinerarioLogradouro->getPontosDeParada() as $oItinerarioPontoParada) {
                        foreach (ItinerarioVinculoAlunoRepository::
                        getItinerarioVinculoAlunoPorPontoParada($oItinerarioPontoParada) as $oVinculoAluno) {
                            $oAluno = $oVinculoAluno->getAluno();

                            $sNome = $oAluno->getNome();
                            $iCodigoAluno = $oAluno->getCodigoAluno();

                            if (!isset($oRetorno->aAlunos[$iCodigoAluno])) {
                                $oRetorno->aAlunos[$iCodigoAluno] = new stdClass();
                                $oRetorno->aAlunos[$iCodigoAluno]->codAluno = $iCodigoAluno;
                                $oRetorno->aAlunos[$iCodigoAluno]->nomeAluno = $sNome;
                            }

                            $sHoraSaida = $oVinculoAluno->getLinhaItinerarioHorario()->getHoraSaida();
                            $sHoraChegada = $oVinculoAluno->getLinhaItinerarioHorario()->getHoraChegada();

                            $sPontoParada = $oItinerarioPontoParada->getPontoParada()->getNome();
                            $sEscola = "";

                            foreach ($oAluno->getMatriculas() as $oMatricula) {
                                if (!$oMatricula->isConcluida()
                                    && $oMatricula->isAtiva()
                                    && $oMatricula->getSituacao() == 'MATRICULADO') {
                                    $sEscola = $oMatricula->getTurma()->getEscola()->getNome();
                                }
                            }

                            if (empty($sEscola)) {
                                $oEscolaProcedencia = $oAluno->getEscolaDeProcedencia();
                                if (!empty($oEscolaProcedencia)) {
                                    $sEscola = $oEscolaProcedencia->getNome();
                                }
                            }

                            // Se tipo == 2 o aluno esta retornando, portanto o embarque é na escola
                            if ($oLinhaItinerario->getTipo() == 2) {
                                $sEmbarque = $sEscola;
                                $sDesembarque = $sPontoParada;
                            } else {
                                $sEmbarque = $sPontoParada;
                                $sDesembarque = $sEscola;
                            }

                            if ($oLinhaItinerario->getTipo() == LinhaItinerario::IDA) {
                                $oRetorno->aAlunos[$iCodigoAluno]->embarqueIda = $sEmbarque;
                                $oRetorno->aAlunos[$iCodigoAluno]->desembarqueIda = $sDesembarque;
                                $oRetorno->aAlunos[$iCodigoAluno]->horaSaidaIda = $sHoraSaida;
                                $oRetorno->aAlunos[$iCodigoAluno]->horaChegadaIda = $sHoraChegada;
                            } else {
                                $oRetorno->aAlunos[$iCodigoAluno]->embarqueVolta = $sEmbarque;
                                $oRetorno->aAlunos[$iCodigoAluno]->desembarqueVolta = $sDesembarque;
                                $oRetorno->aAlunos[$iCodigoAluno]->horaSaidaVolta = $sHoraSaida;
                                $oRetorno->aAlunos[$iCodigoAluno]->horaChegadaVolta = $sHoraChegada;
                            }
                        }
                    }
                }
            }
        }

        // Ordena os alunos por nome
        usort($oRetorno->aAlunos, function ($oAlunoCorrente, $oProximoAluno) {
            return strcasecmp($oAlunoCorrente->nomeAluno, $oProximoAluno->nomeAluno);
        });

        return $oRetorno;
    }

    public function getDados($parametros)
    {
        $oParam = $parametros;

        foreach ($oParam['alunos'] as $key => $aluno) {
            $dadosAluno = $this->getDadosAluno($aluno['codAluno']);
            $oParam['alunos'][$key] = array_merge($aluno, (array) $dadosAluno);
            if ($dadosAluno->necessidade !== null) {
                $necessidadesAluno = $this->getNecessidades($aluno['codAluno']);
                $oParam['alunos'][$key]['necessidade'] = $necessidadesAluno;
            }
        }
        return $oParam;
    }

    private function getDadosAluno($codAluno)
    {
        $dadosAluno = DB::table('aluno')
            ->select(
                'aluno.ed47_v_cpf as cpfAluno',
                'aluno.ed47_d_nasc as dataNascimento',
                DB::raw('TRIM(aluno.ed47_c_foto) as fotoperfil'),
                DB::raw('TRIM(aluno.ed47_v_mae) as nomeResponsavelUm'),
                DB::raw('TRIM(aluno.ed47_v_pai) as nomeResponsavelDois'),
                DB::raw('TRIM(aluno.ed47_c_nomeresp) as nomeResponsavel'),
                'aluno.ed47_v_telcel as contatoUm',
                'aluno.ed47_v_telef as contatoDois',
                'tiposanguineo.sd100_tipo as tipoSanguineo',
                'necessidade.ed48_c_descr as necessidade',
                DB::raw("
                CASE WHEN necessidadealunocadeirante.ed189_sequencial IS NULL THEN 'NÃO' ELSE 'SIM' END AS Cadeirante
                ")
            )
            ->leftJoin(
                'alunonecessidade',
                'alunonecessidade.ed214_i_aluno',
                '=',
                'aluno.ed47_i_codigo'
            )
            ->leftJoin(
                'necessidade',
                'necessidade.ed48_i_codigo',
                '=',
                'alunonecessidade.ed214_i_necessidade'
            )->leftJoin(
                'tiposanguineo',
                'tiposanguineo.sd100_sequencial',
                '=',
                'aluno.ed47_tiposanguineo'
            )
            ->leftJoin(
                'necessidadealunocadeirante',
                'necessidadealunocadeirante.ed189_aluno',
                '=',
                'aluno.ed47_i_codigo'
            )
            ->where('aluno.ed47_i_codigo', $codAluno)
            ->get();

        return $dadosAluno[0];
    }

    public function insereMascaraCPF($dados)
    {
        foreach ($dados['alunos'] as &$dadosAluno) {
            if (isset($dadosAluno['cpfAluno']) && strlen($dadosAluno['cpfAluno']) == 11) {
                $dadosAluno['cpfAluno'] =
                      substr($dadosAluno['cpfAluno'], 0, 3) . '.'
                    . substr($dadosAluno['cpfAluno'], 3, 3) . '.'
                    . substr($dadosAluno['cpfAluno'], 6, 3) . '-'
                    . substr($dadosAluno['cpfAluno'], 9, 2);
            }
        }
        return $dados;
    }

    public function formataDataNascimento($dados)
    {
        foreach ($dados['alunos'] as &$dadosAluno) {
            if (isset($dadosAluno['dataNascimento'])) {
                $date = DateTime::createFromFormat('Y-m-d', $dadosAluno['dataNascimento']);
                if ($date !== false) {
                    $dadosAluno['dataNascimento'] = $date->format('d/m/Y');
                }
            }
        }
        return $dados;
    }

    public function formataContatos($dados)
    {
        foreach ($dados['alunos'] as &$dadosAluno) {
            if (isset($dadosAluno['contatoUm']) && strlen($dadosAluno['contatoUm']) == 11) {
                $dadosAluno['contatoUm'] = '('
                    . substr($dadosAluno['contatoUm'], 0, 2) . ') '
                    . substr($dadosAluno['contatoUm'], 2, 1) . ' '
                    . substr($dadosAluno['contatoUm'], 3, 4) . '-'
                    . substr($dadosAluno['contatoUm'], 7, 4);
            }
            if (isset($dadosAluno['contatoDois']) && strlen($dadosAluno['contatoDois']) == 11) {
                $dadosAluno['contatoDois'] = '('
                    . substr($dadosAluno['contatoDois'], 0, 2) . ') '
                    . substr($dadosAluno['contatoDois'], 2, 1) . ' '
                    . substr($dadosAluno['contatoDois'], 3, 4) . '-'
                    . substr($dadosAluno['contatoDois'], 7, 4);
            }
        }
        return $dados;
    }

    private function getNecessidades($codAluno)
    {
        $necessidades = [];
        $dbNecessidade = new cl_alunonecessidade();
        $dbWhere = "ed214_i_aluno = $codAluno";
        $sSqlNecessidadeAluno = $dbNecessidade->sql_query_file(null, '*', null, $dbWhere);
        $result = $dbNecessidade->sql_record($sSqlNecessidadeAluno);

        if (pg_num_rows($result) > 0) {
            for ($i = 0; $i < pg_num_rows($result); $i++) {
                $necessidade = db_utils::fieldsMemory($result, $i);
                $necessidades[$necessidade->ed214_i_necessidade] = [
                    "codNecessidade" => $necessidade->ed214_i_necessidade,
                    "necessidadePrincipal" => $necessidade->ed214_c_principal,
                    "subNecessidades" => []  // Inicializar o array de subnecessidades
                ];
            }
        }

        if (count($necessidades) > 0) {
            $necessidadesPai = [];
            $dbNecessidadePai = new cl_necessidade();
            foreach ($necessidades as $key => $value) {
                $dbWherePai = "ed48_i_codigo = $value[codNecessidade]";
                $sSqlNecessidadePai = $dbNecessidadePai->sql_query_file(null, '*', null, $dbWherePai);
                $resultNecessidadePai = $dbNecessidadePai->sql_record($sSqlNecessidadePai);

                if (pg_num_rows($resultNecessidadePai) > 0) {
                    for ($i = 0; $i < pg_num_rows($resultNecessidadePai); $i++) {
                        $necessidadePai = db_utils::fieldsMemory($resultNecessidadePai, $i);
                        $necessidadesPai[$necessidadePai->ed48_i_codigo] = $necessidadePai->ed48_c_descr;
                    }
                }
            }

            // Adicionar descricaoPai ao array necessidades
            foreach ($necessidades as $key => &$necessidade) {
                if (isset($necessidadesPai[$key])) {
                    $necessidade['descricaoPai'] = $necessidadesPai[$key];
                }
            }

            // Obter todas as subnecessidades
            $subNecessidades = [];
            $dbNecessidadeSub = new cl_necessidadesubdivisao();
            $dbWhereSub = "ed185_necessidade IN (" . implode(',', array_keys($necessidades)) . ")";
            $sSqlNecessidadeSub = $dbNecessidadeSub->sql_query_file(null, '*', null, $dbWhereSub);
            $resultNecessidadeSub = $dbNecessidadeSub->sql_record($sSqlNecessidadeSub);

            if (pg_num_rows($resultNecessidadeSub) > 0) {
                for ($i = 0; $i < pg_num_rows($resultNecessidadeSub); $i++) {
                    $subNecessidade = db_utils::fieldsMemory($resultNecessidadeSub, $i);
                    $subNecessidades[$subNecessidade->ed185_sequencial] = [
                        "sequencialSub" => $subNecessidade->ed185_sequencial,
                        "necessidadeSub" => $subNecessidade->ed185_necessidade,
                        "descricaoSub" => $subNecessidade->ed185_descricao,
                    ];
                }
            }

            // Obter subnecessidades do aluno
            $dbSubNecessidadeAluno = new cl_necessidadesubdivisaoaluno();
            $dbWhereSubAluno = "ed187_aluno = $codAluno";
            $sSqlSubNecessidadeAluno = $dbSubNecessidadeAluno->sql_query_file(
                null,
                'ed187_necessidadesubdivisao',
                null,
                $dbWhereSubAluno
            );
            $resultNecessidadeSubAluno = $dbSubNecessidadeAluno->sql_record($sSqlSubNecessidadeAluno);

            if (pg_num_rows($resultNecessidadeSubAluno) > 0) {
                for ($i = 0; $i < pg_num_rows($resultNecessidadeSubAluno); $i++) {
                    $subs = db_utils::fieldsMemory($resultNecessidadeSubAluno, $i);
                    if (isset($subNecessidades[$subs->ed187_necessidadesubdivisao])) {
                        $necessidadeSub = $subNecessidades[$subs->ed187_necessidadesubdivisao];
                        $necessidades[$necessidadeSub['necessidadeSub']]['subNecessidades'][] = $necessidadeSub;
                    }
                }
            }
        }
        return $necessidades;
    }
}
