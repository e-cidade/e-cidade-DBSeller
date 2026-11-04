<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\ExcecaoBloqueioNota;
use App\Domain\Educacao\Escola\Models\ParametrosBloqueioNota;
use App\Domain\Educacao\Escola\Models\PeriodoCalendario;
use App\Domain\Educacao\Escola\Models\TurmaEtapaRegimeMatricula;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ParamentrosBloqueioNotasController extends Controller
{
    public function show()
    {
        $parametros = ParametrosBloqueioNota::first();
        return new DBJsonResponse($parametros);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function update(Request $request)
    {
        $codigo = $request->get('mo632_codigo');
        try {
            $parametros = ParametrosBloqueioNota::findOrFail($codigo);
        } catch (Exception $exception) {
            throw new Exception("Erro ao buscar parametros (Código: {$codigo})");
        }
        $parametros->ed362_tipobloqueio = $request->get('mo632_tipobloqueio');
        $parametros->ed362_prazodias = $request->get('mo632_prazodias');
        $parametros->save();

        return new DBJsonResponse([], 'Parametros atualizados com sucesso!');
    }

    public function listagemExcecoes()
    {
        $excecoes = ExcecaoBloqueioNota::where('ed363_ativo', true)->get();
        $excecoesResponse = [];
        foreach ($excecoes as $excessao) {
            $excecaoResponse = (object)[
                'ed363_codigo' => $excessao->ed363_codigo,
                'escola' => $excessao->turma->escola->ed18_c_nome,
                'turma' => $excessao->turma->ed57_c_descr,
                'disciplina' => 'TODAS',
                'periodo' => $excessao->periodoAvaliacao->ed09_c_descr,
                'data_limite' => $excessao->ed363_datalimite->format('d/m/Y'),
                'ativo' => $excessao->ed363_ativo,
            ];
            if (!empty($excessao->ed363_regencia)) {
                $excecaoResponse->disciplina = $excessao->regencia->disciplinaEnsino->disciplina->ed232_c_descr;
            }
            $excecoesResponse[] = $excecaoResponse;
        }
        return new DBJsonResponse($excecoesResponse);
    }

    public function salvarExcecao(Request $request)
    {
        $this->validate($request, [
            'turma' => 'required|integer',
            'regencia' => 'integer',
            'periodocalendario' => 'required|integer',
            'datalimite' => 'required|date_format:d/m/Y'
        ]);

        $turmaEtapa = TurmaEtapaRegimeMatricula::findOrFail($request->get('turma'));

        $dataLimite = explode('/', $request->get('datalimite'));
        $dataLimite = "{$dataLimite[2]}-{$dataLimite[1]}-{$dataLimite[0]}";
        $excecao = new ExcecaoBloqueioNota();
        $excecao->ed363_turma = $turmaEtapa->ed220_i_turma;
        $excecao->ed363_regencia = null;
        if (!empty($request->get('regencia'))) {
            $excecao->ed363_regencia = $request->get('regencia');
        }
        $periodoCalendario = PeriodoCalendario::find($request->get('periodocalendario'));
        $excecao->ed363_periodoavaliacao = $periodoCalendario->ed53_i_periodoavaliacao;
        $excecao->ed363_datalimite = $dataLimite;
        $excecao->ed363_ativo = true;
        $excecao->ed363_usuario = db_getsession('DB_id_usuario');
        $excecao->save();

        return new DBJsonResponse($excecao, 'Exceção salva com sucesso!');
    }

    public function encerrarExcecao(Request $request, $codigo)
    {
        $excecao = ExcecaoBloqueioNota::findOrFail($codigo);
        $excecao->ed363_ativo = false;
        $excecao->save();

        return new DBJsonResponse([], 'Exceção encerrada com sucesso!');
    }
}
