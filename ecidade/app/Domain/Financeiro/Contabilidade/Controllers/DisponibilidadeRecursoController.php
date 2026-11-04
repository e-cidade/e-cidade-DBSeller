<?php
namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\DisponibilidadeRecursoService;
use App\Http\Controllers\Controller;
use DBDate;
use Illuminate\Http\Request;
use stdClass;

/**
 * Class DisponibilidadeRecursoController
 * @package App\Domain\Financeiro\Contabilidade\Controllers
 */
class DisponibilidadeRecursoController extends Controller
{
    private $service;
    private $filtros;

    public function __construct(DisponibilidadeRecursoService $service)
    {
        $this->service = $service;
    }

    public function processarSaldoDisponibilidadeRecurso(Request $request)
    {
        $this->parseFiltros($request);
        $file = $this->service->relatorioSaldoDisponibilidadeRecurso($this->filtros);
        return new DBJsonResponse(['pdf' => $file]);
    }

    public function relatorioConferenciaPorRecurso(Request $request)
    {
        $this->parseFiltros($request);
        $file = $this->service->relatorioConferenciaPorRecurso($this->filtros);
        return new DBJsonResponse(['arquivos' => $file]);
    }

    public function obterDadosConferenciaPorRecurso(Request $request)
    {
        $this->parseFiltros($request);
        $response = $this->service->apenasDadosComDiferenca($this->filtros);
        return new DBJsonResponse(array_values($response));
    }

    private function parseFiltros(Request $request)
    {
        $rule = [

            "dataInicial" => ['required', 'date'],
            "dataFinal" => ['required', 'date'],
            "instituicoes" =>  ['required', 'array'],
            "instituicoes.*" =>  ['required', 'integer']
        ];

        $mensagens = [
            "instituicoes.*" => "Selecione uma Instituição Válida."
        ];


        validaRequest($request->all(), $rule, $mensagens);

        $filtros = new stdClass();
        $filtros->dataInicial = DBDate::converter($request->dataInicial);
        $filtros->dataFinal = DBDate::converter($request->dataFinal);
        $filtros->instituicoes = implode(", ", $request->instituicoes);
        $filtros->ano = $request->DB_anousu;

        $filtros->modeloEmissao = 1;
        if ($request->has('modeloEmissao')) {
            $filtros->modeloEmissao = $request->modeloEmissao;
        }

        $this->filtros =  $filtros;
    }
}
