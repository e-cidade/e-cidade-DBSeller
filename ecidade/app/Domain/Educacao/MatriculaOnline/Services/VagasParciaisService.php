<?php

namespace App\Domain\Educacao\MatriculaOnline\Services;

use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use DateTime;
use DBException;
use Illuminate\Support\Facades\DB;
use TurmaRepository;

class VagasParciaisService
{

    public function getFasesAbertas()
    {
        return Fase::where('mo04_encerrada', false)->get();
    }

    /**
     * @throws DBException
     */
    public function getDados($parametros)
    {
        $ano = $this->getAnoFase($parametros['fase']['dataCorte']);
        $etapas = $this->getEtapasFase($parametros['etapas']);
        $escolas = $this->getEscolaFase($parametros['escolas']);
        $turnos = $this->getTurnoFase($parametros['turnos']);

        $retornoEscolas = $this->getDadosEscola($ano, $etapas, $escolas, $turnos);
        $todosTurnos = $this->getTurnosFase($parametros, true);

        $dadosAgrupadosEscolas['dadosEscolas'] = $this->dadosAgrupadosEscolas($retornoEscolas);
        $this->dadosOrganizados($dadosAgrupadosEscolas['dadosEscolas']);
        $dadosAgrupadosEscolas['todosTurnos'] = $todosTurnos;

        return $dadosAgrupadosEscolas;
    }

    public function getEscolasFase($fase)
    {
        $escolas = DB::table('plugins.escolafase')
            ->distinct()
            ->select(
                'plugins.escolafase.mo50_escola AS codigo_escola',
                DB::raw('TRIM(plugins.escolas.mo53_nome) AS nome_escola')
            )
            ->join(
                'plugins.escolas',
                'escolas.mo53_codigo',
                '=',
                'escolafase.mo50_escola'
            )
            ->where('escolafase.mo50_fase', $fase)
            ->orderBy('nome_escola')
            ->get();

        return $escolas;
    }

    private function getAnoFase($dataCorte)
    {
        return substr($dataCorte, 6, 4);
    }

    private function getEtapasFase($etapas)
    {
        $aEtapas = [];
        foreach ($etapas as $etapa) {
            $aEtapas[] = $etapa['code'];
        }

        return $aEtapas;
    }

    private function getCodigoEscolasFase($escolas)
    {
        $aEscolas = [];
        foreach ($escolas as $escola) {
            $aEscolas[] = $escola['code'];
        }
        return $aEscolas;
    }

    private function getDadosEscola($ano, $etapas, $escolas, $turnos)
    {
        $dados = DB::table('escola')
            ->distinct()
            ->select(
                'ed18_i_codigo AS codigo_escola',
                DB::raw('TRIM(ed18_c_nome) AS nome_escola'),
                DB::raw('TRIM(ed18_c_abrev) AS abreviatura_escola'),
                'ed10_i_codigo AS codigo_ensino',
                DB::raw('TRIM(ed10_c_descr) AS nome_ensino'),
                'ed11_i_codigo AS codigo_etapa',
                DB::raw('TRIM(ed11_c_descr) AS nome_etapa'),
                'ed57_i_codigo AS codigo_turma',
                DB::raw('TRIM(ed57_c_descr) AS nome_turma'),
                'ed57_i_turno AS turno_turma',
                'ed15_i_codigo AS codigo_turno',
                DB::raw('TRIM(ed15_c_nome) AS nome_turno'),
                'ed10_ordem AS ordem_ensino',
                'ed11_i_sequencia AS sequencia_etapa',
                'ed15_i_sequencia AS sequencia_turno'
            )
            ->join(
                'turma',
                'turma.ed57_i_escola',
                '=',
                'escola.ed18_i_codigo'
            )
            ->join(
                'turno',
                'turno.ed15_i_codigo',
                '=',
                'turma.ed57_i_turno'
            )
            ->join(
                'calendario',
                'calendario.ed52_i_codigo',
                '=',
                'turma.ed57_i_calendario'
            )
            ->join(
                'turmaserieregimemat',
                'turmaserieregimemat.ed220_i_turma',
                '=',
                'turma.ed57_i_codigo'
            )
            ->join(
                'serieregimemat',
                'serieregimemat.ed223_i_codigo',
                '=',
                'turmaserieregimemat.ed220_i_serieregimemat'
            )
            ->join(
                'serie',
                'serie.ed11_i_codigo',
                '=',
                'serieregimemat.ed223_i_serie'
            )
            ->join(
                'ensino',
                'ensino.ed10_i_codigo',
                '=',
                'serie.ed11_i_ensino'
            )
            ->where('calendario.ed52_i_ano', $ano)
            ->whereIn('serie.ed11_i_codigo', $etapas)
            ->whereIn('turno.ed15_i_codigo', $turnos)
            ->whereIn('escola.ed18_i_codigo', $escolas)
            ->orderBy('nome_escola')
            ->orderBy('ed10_ordem')
            ->orderBy('ed11_i_sequencia')
            ->orderBy('ed15_i_codigo')
            ->get();

        return $dados;
    }

    private function dadosAgrupadosEscolas($retornoEscolas)
    {
        $escolas = [];
        foreach ($retornoEscolas as $dado) {
            if (!array_key_exists($dado->codigo_escola, $escolas)) {
                $escolas[$dado->codigo_escola] = (object)array(
                    'codigo' => $dado->codigo_escola,
                    'nome' => $dado->nome_escola,
                    'ensinos' => array(),
                );
            }
            $ensinos = $escolas[$dado->codigo_escola]
                ->ensinos;
            if (!array_key_exists($dado->codigo_ensino, $ensinos)) {
                $escolas[$dado->codigo_escola]
                    ->ensinos[$dado->codigo_ensino] = (object)array(
                    'codigo' => $dado->codigo_ensino,
                    'nome' => $dado->nome_ensino,
                    'etapas' => array(),
                    );
            }
            $etapas = $escolas[$dado->codigo_escola]
                ->ensinos[$dado->codigo_ensino]
                ->etapas;
            if (!array_key_exists($dado->codigo_etapa, $etapas)) {
                $escolas[$dado->codigo_escola]
                    ->ensinos[$dado->codigo_ensino]
                    ->etapas[$dado->codigo_etapa] = (object)array(
                    "codigo_etapa" => $dado->codigo_etapa,
                    "etapa" => $dado->nome_etapa,
                    "turmas" => array(),
                    "turnos" => array()
                    );
            }
            $turmas = $escolas[$dado->codigo_escola]
                ->ensinos[$dado->codigo_ensino]
                ->etapas[$dado->codigo_etapa]
                ->turmas;
            if (!array_key_exists($dado->codigo_turma, $turmas)) {
                $escolas[$dado->codigo_escola]
                    ->ensinos[$dado->codigo_ensino]
                    ->etapas[$dado->codigo_etapa]
                    ->turmas[$dado->codigo_turma] = (object)array(
                    "codigo_turma" => $dado->codigo_turma,
                    "nome_turma" => $dado->nome_turma,
                    "turno_turma" => $dado->turno_turma,
                    "vagas" => array(),
                    "vagas_disponiveis" => array(),
                    "vagas_ocupadas" => array()
                    );
            }
            $turnos = $escolas[$dado->codigo_escola]
                ->ensinos[$dado->codigo_ensino]
                ->etapas[$dado->codigo_etapa]
                ->turnos;
            if (!array_key_exists($dado->codigo_turno, $turnos)) {
                $escolas[$dado->codigo_escola]
                    ->ensinos[$dado->codigo_ensino]
                    ->etapas[$dado->codigo_etapa]
                    ->turnos[$dado->codigo_turno] = (object)array(
                    "codigo_turno" => $dado->codigo_turno,
                    "turno" => $dado->nome_turno,
                    "vagas_fornecidas" => array(),
                    "vagas_disponiveis" => array(),
                    "vagas_ocupadas" => array()
                    );
            }
        }

        return $escolas;
    }

    /**
     * @throws DBException
     */
    private function getVagas($turma)
    {
        $oTurma = TurmaRepository::getTurmaByCodigo($turma->codigo_turma);
        $turma->vagas = $oTurma->getVagas();
        $turma->vagas_disponiveis = $oTurma->getVagasDisponiveis();
        $turma->vagas_ocupadas = $oTurma->getVagasOcupadas();
    }

    public function getTurnosFase(array $parametros, $todosTurnos = false)
    {
        $codEnsino = reset($parametros['fase']['ciclo']['ensinos'])['codigo'];
        $escolas = $this->getCodigoEscolasFase($parametros['escolas']);

        /* Crio uma cte de todos os turnos retornados na query */
        $cteTurnos = DB::table('cursoturno')
            ->distinct()
            ->select(
                'ed15_i_codigo AS codigo_turno',
                DB::raw('TRIM(ed15_c_nome) AS nome_turno'),
                'turnoreferente.ed231_i_referencia'
            )
            ->join(
                'cursoedu',
                'cursoturno.ed85_i_curso',
                '=',
                'cursoedu.ed29_i_codigo'
            )
            ->join(
                'turno',
                'turno.ed15_i_codigo',
                '=',
                'cursoturno.ed85_i_turno'
            )
            ->join(
                'turnoreferente',
                'turno.ed15_i_codigo',
                '=',
                'turnoreferente.ed231_i_turno'
            )
            ->where('cursoedu.ed29_i_ensino', $codEnsino)
            ->whereIn('cursoturno.ed85_i_escola', $escolas);

        if ($todosTurnos) {
            return $cteTurnos->get();
        }

        /* Filtro os turnos que tiveram mais de um retorno da cte*/
        return DB::table(
            DB::raw("({$cteTurnos->toSql()}) AS cte_turnos")
        )
            ->mergeBindings($cteTurnos)
            ->select('codigo_turno', 'nome_turno')
            ->groupBy('codigo_turno', 'nome_turno')
            ->havingRaw('COUNT(codigo_turno) > 1')
            ->get();
    }

    private function getTurnoFase($turnos)
    {
        $aTurnos = [];

        foreach ($turnos as $turno) {
            $aTurnos[] = $turno['code'];
        }

        return $aTurnos;
    }

    private function getEscolaFase($escolas)
    {
        $aEscolas = [];
        foreach ($escolas as $escola) {
            $aEscolas[] = $escola['code'];
        }
        return $aEscolas;
    }

    /**
     * @throws DBException
     */
    private function dadosOrganizados(array &$dadosAgrupadosEscolas)
    {
        foreach ($dadosAgrupadosEscolas as $escola) {
            foreach ($escola->ensinos as $ensino) {
                foreach ($ensino->etapas as $etapa) {
                    foreach ($etapa->turmas as $turma) {
                        $this->getVagas($turma);
                        // O loop abaixo organiza os dados fazendo a contagem das vagas que retornam das turmas e
                        // insere-as nas suas respectivas vagas, respeitando as etapas e turnos.
                        foreach ($etapa->turnos as $turno) {
                            if ($turma->turno_turma === $turno->codigo_turno) {
                                foreach ($turma->vagas as $chave => $vaga) {
                                    if (!isset($turno->vagas_fornecidas[$turno->codigo_turno])) {
                                        $turno->vagas_fornecidas[$turno->codigo_turno] = array();
                                    }
                                    if (!isset($turno->vagas_fornecidas[$turno->codigo_turno][$chave])) {
                                        $turno->vagas_fornecidas[$turno->codigo_turno][$chave] = 0;
                                    }

                                    $turno->vagas_fornecidas[$turno->codigo_turno][$chave] += (int)$vaga;
                                }

                                foreach ($turma->vagas_ocupadas as $chave => $vaga_ocupada) {
                                    if (!isset($turno->vagas_ocupadas[$turno->codigo_turno])) {
                                        $turno->vagas_ocupadas[$turno->codigo_turno] = array();
                                    }
                                    if (!isset($turno->vagas_ocupadas[$turno->codigo_turno][$chave])) {
                                        $turno->vagas_ocupadas[$turno->codigo_turno][$chave] = 0;
                                    }

                                    $turno->vagas_ocupadas[$turno->codigo_turno][$chave] += (int)$vaga_ocupada;
                                }

                                foreach ($turma->vagas_disponiveis as $chave => $vaga_disponivel) {
                                    if (!isset($turno->vagas_disponiveis[$turno->codigo_turno])) {
                                        $turno->vagas_disponiveis[$turno->codigo_turno] = array();
                                    }
                                    if (!isset($turno->vagas_disponiveis[$turno->codigo_turno][$chave])) {
                                        $turno->vagas_disponiveis[$turno->codigo_turno][$chave] = 0;
                                    }

                                    $turno->
                                    vagas_disponiveis[$turno->codigo_turno][$chave] += (int)$vaga_disponivel;
                                }
                                    // Ordena os arrays pelas suas chaves (índices) de forma crescente
                                    ksort($turno->vagas_fornecidas[$turno->codigo_turno]);
                                    ksort($turno->vagas_ocupadas[$turno->codigo_turno]);
                                    ksort($turno->vagas_disponiveis[$turno->codigo_turno]);
                            }
                        }
                    }
                    unset($etapa->turmas); // Remove a propriedade turmas após o processamento
                }
            }
        }
    }
}
