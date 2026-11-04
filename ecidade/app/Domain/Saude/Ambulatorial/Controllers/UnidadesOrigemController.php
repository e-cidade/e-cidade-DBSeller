<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Services\UnidadesOrigemService;
use App\Domain\Saude\Ambulatorial\Requests\UnidadesOrigemRequest;

class UnidadesOrigemController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new UnidadesOrigemService();
    }

    public function get()
    {
        $serviceResponse = $this->service->get();
        return new DBJsonResponse($serviceResponse, 'Unidades de origem listadas com sucesso!', 200);
    }
    public function getByUnidade(Request $request)
    {
        $this->validationFields($request);
        $serviceResponse = $this->service->getByUnidade($request->all());
        return new DBJsonResponse(null, 'GetUnidade', 200);
    }
    
    public function save(UnidadesOrigemRequest $request)
    {
        $serviceResponse = $this->service->saveUnidade($request->all());
        return new DBJsonResponse([], 'Unidade de origem salva com sucesso!', 200);
    }

    public function delete(Request $request)
    {
        $this->validationFields($request);
        $serviceResponse = $this->service->deleteUnidade($request->all());
        return new DBJsonResponse([], 'Unidade de origem excluída com sucesso!', 200);
    }

    private function validationFields(Request $request)
    {
        
        $rule = [
            "codigo" => ['required', 'int']
        ];

        $mensagens = [
          "codigo.*" => "Campo código requerido!"
        ];

        validaRequest($request->all(), $rule, $mensagens);
    }
}
