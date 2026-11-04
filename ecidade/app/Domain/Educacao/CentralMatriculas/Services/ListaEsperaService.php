<?php

namespace App\Domain\Educacao\CentralMatriculas\Services;

use App\Domain\Educacao\CentralMatriculas\Models\Escola;
use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Escola\Resources\EtapaResource;
use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use ECidade\Educacao\MatriculaOnline\Repository\ConfiguracaoEscolaRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ListaEsperaService
{
    /**
     * Obtém as etapas abertas disponíveis na tabela de lista de espera.
     *
     * Este método realiza uma consulta na tabela `plugins.listaespera` e
     * retorna uma lista de etapas (séries) associadas às fases abertas.
     * As etapas são filtradas pelo status da lista de espera (mo18_status)
     * e organizadas de acordo com a ordem do ensino e a sequência da série.
     *
     * @return object Objeto contendo:
     * - `error` (bool): Indica se houve um erro na obtenção das etapas.
     * - `message` (string): Mensagem informativa sobre o resultado da consulta.
     * - `data` (Illuminate\Support\Collection|null):
     *  Coleção de etapas encontradas ou null se nenhuma etapa foi encontrada.
     */
    public function getEtapasListaEspera()
    {
        $etapas =
            DB::table('plugins.listaespera')
                ->distinct()
                ->select(
                    'ed11_i_codigo as codigo',
                    'ed11_c_descr as nome',
                    'ensino.ed10_ordem',
                    'serie.ed11_i_sequencia'
                )
                ->join('escola.serie', 'serie.ed11_i_codigo', '=', 'listaespera.mo18_serie')
                ->join('escola.ensino', 'ensino.ed10_i_codigo', '=', 'serie.ed11_i_ensino')
                ->where('listaespera.mo18_status', '=', 1)
                ->whereIn('listaespera.mo18_base', function ($query) {
                    $query->select('basefase.mo12_base')
                        ->from('plugins.basefase')
                        ->whereIn('basefase.mo12_fase', function ($subquery) {
                            $subquery->select('fase.mo04_codigo')
                                ->from('plugins.fase')
                                ->where('fase.mo04_encerrada', '=', false);
                        });
                })
                ->orderBy('ensino.ed10_ordem')
                ->orderBy('serie.ed11_i_sequencia')
                ->get();

        if (count($etapas) > 0) {
            return (object) [
                "error" => false,
                "message" => "Etapas obtidas com sucesso!",
                "data" => $etapas
            ];
        }

        return (object) [
            "error" => false,
            "message" => "Não foram encontradas etapas para as fases abertas!",
            "data" => null
        ];
    }

    /**
     * @throws Exception
     */
    public function getEscolasListaEspera($etapa)
    {
        /**
         * Obtém as escolas disponíveis na tabela de lista de espera.
         *
         * Este método realiza uma consulta na tabela `plugins.listaespera` e
         * retorna uma lista de escolas associadas às fases abertas.
         * As escolas são filtradas pelo código da etapa (ed11_i_codigo)
         * e organizadas de acordo com a ordem do nome da escola.
         *
         * @return object Objeto contendo:
         * - `error` (bool): Indica se houve um erro na obtenção das escolas.
         * - `message` (string): Mensagem informativa sobre o resultado da consulta.
         * - `data` (Illuminate\Support\Collection|null):
         *  Coleção de escolas encontradas ou null se nenhuma escola foi encontrada.
         */

        $escolas =
            DB::table('plugins.listaespera')
                ->distinct()
                ->select('listaespera.mo18_escola as codigo', 'escolas.mo53_nome as nome')
                ->join('escola.serie', 'serie.ed11_i_codigo', '=', 'listaespera.mo18_serie')
                ->join('plugins.escolas', 'escolas.mo53_codigo', '=', 'listaespera.mo18_escola')
                ->where('listaespera.mo18_status', '=', 1)
                ->where('serie.ed11_i_codigo', '=', $etapa)
                ->whereIn('listaespera.mo18_base', function ($query) {
                    $query->select('basefase.mo12_base')
                        ->from('plugins.basefase')
                        ->whereIn('basefase.mo12_fase', function ($subquery) {
                            $subquery->select('fase.mo04_codigo')
                                ->from('plugins.fase')
                                ->where('fase.mo04_encerrada', '=', false);
                        });
                })
                ->orderBy('escolas.mo53_nome')
                ->get();

        if ($escolas->count() > 0) {
            return (object) [
                "error" => false,
                "message" => "Escolas disponíveis!",
                "data" => $escolas
            ];
        }

        return (object) [
            "error" => true,
            "message" => 'Não foram encontradas escolas para essa etapa!',
            "data" => null,
            "status" => 401
        ];
    }

    /**
     * @throws Exception
     */
    public function getTurnosListaEspera($etapa, $escola)
    {
        $turnos =
            DB::table('plugins.listaespera')
                ->distinct()
                ->select('listaespera.mo18_turno as codigo', 'turno.ed15_c_nome as nome')
                ->join('escola.serie', 'serie.ed11_i_codigo', '=', 'listaespera.mo18_serie')
                ->join('escola.turno', 'listaespera.mo18_turno', '=', 'turno.ed15_i_codigo')
                ->join('plugins.escolas', 'escolas.mo53_codigo', '=', 'listaespera.mo18_escola')
                ->where('listaespera.mo18_status', '=', 1)
                ->where('serie.ed11_i_codigo', '=', $etapa)
                ->where('listaespera.mo18_escola', '=', $escola)
                ->whereIn('listaespera.mo18_base', function ($query) {
                    $query->select('basefase.mo12_base')
                        ->from('plugins.basefase')
                        ->whereIn('basefase.mo12_fase', function ($subquery) {
                            $subquery->select('fase.mo04_codigo')
                                ->from('plugins.fase')
                                ->where('fase.mo04_encerrada', '=', false);
                        });
                })
                ->orderBy('turno.ed15_c_nome')
                ->get();

        if ($turnos->count() > 0) {
            return (object) [
                "error" => false,
                "message" => "Turnos encontrados com sucesso!",
                "data" => $turnos
            ];
        }

        return (object) [
            "error" => true,
            "message" => 'Não foram encontrados turnos para o filtro selecionado!',
            "data" => null,
            "status" => 401
        ];
    }

    public function getCandidatosListaEspera($etapa, $escola, $turno)
    {
        $candidatos =
            DB::table('plugins.listaespera')
                ->distinct()
                ->select(
                    'mo18_classificacao as classificacao',
                    'mo12_protocolo as protocolo',
                    'mo05_descr as rede_origem',
                    'mo01_necess as necessidade_especial'
                )
                ->join('plugins.mobase', 'mobase.mo01_codigo', '=', 'listaespera.mo18_base')
                ->join('plugins.basefase', 'basefase.mo12_base', '=', 'mobase.mo01_codigo')
                ->join('plugins.fase', 'fase.mo04_codigo', '=', 'basefase.mo12_fase')
                ->join('plugins.redeorigem', 'redeorigem.mo05_codigo', '=', 'mobase.mo01_redeorigem')
                ->where('mo18_serie', '=', $etapa)
                ->where('mo18_escola', '=', $escola)
                ->where('mo18_turno', '=', $turno)
                ->where('mo18_status', '=', 1)
                ->where('mo04_encerrada', '=', false)
                ->orderBy('mo18_classificacao')
                ->get();

        if ($candidatos->count() > 0) {
            return (object) [
                "error" => false,
                "message" => "Candidato(s) encontrado(s) com sucesso!",
                "data" => $candidatos
            ];
        }

        return (object) [
            "error" => true,
            "message" => 'Não foram encontrados candidatos para os filtros selecionados!',
            "data" => null,
            "status" => 401
        ];
    }
}
