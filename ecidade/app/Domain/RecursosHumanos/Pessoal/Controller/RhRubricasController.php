<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\RhRubricas;
use App\Domain\RecursosHumanos\Pessoal\Model\Rubrica\ObservacaoRubrica;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\Rubrica\ObservacaoRubricaCsv;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\Rubrica\ObservacaoRubricaPdf;
use App\Domain\RecursosHumanos\Pessoal\Services\Rubrica\ObservacaoRubricaService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RhRubricasController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listRubrica(Request $request)
    {
        try {
            $campos = ['rh27_rubric', 'rh27_descr', 'rh27_instit'];
            $rhrubricas = RhRubricas::where(
                'rh27_instit',
                db_getsession('DB_instit')
            );

            if (isset($request->rh27_rubric)) {
                $rhrubricas = $rhrubricas->where(
                    'rh27_rubric',
                    $request->rh27_rubric
                );
            }
            if (isset($request->rh27_descr)) {
                $rhrubricas = $rhrubricas->where(
                    'rh27_descr',
                    'like',
                    "%{$request->rh27_descr}%"
                );
            }

            return new DBJsonResponse(
                $rhrubricas->get($campos)
            );
        } catch (Exception $e) {
            return new DBJsonResponse(
                [],
                $e->getMessage(),
                400
            );
        }
    }

    public function gerarObservacaoRubrica(Request $request)
    {
        $service = new ObservacaoRubricaService();
        $dados = $service->getDadosEmissaoRelatorio($request);
        
        if ($dados->isEmpty()) {
            return new DBJsonResponse([], 'Não foram encontrados registros para o filtro informado', 401);
        }

        if ($request->tipo == 1) {
            $emissao = new ObservacaoRubricaPdf($dados->toArray());
        } else {
            $emissao = new ObservacaoRubricaCsv($dados->toArray());
        }

        return new DBJsonResponse($emissao->emitir());
    }
}
