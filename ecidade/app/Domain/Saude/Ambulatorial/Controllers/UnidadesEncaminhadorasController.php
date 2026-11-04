<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Services\UnidadesEncaminhadorasService;
use App\Domain\Saude\Ambulatorial\Requests\UnidadesEncaminhadorasRequest;

class UnidadesEncaminhadorasController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new UnidadesEncaminhadorasService();
    }

    public function get()
    {
        $serviceResponse = $this->service->get();
        return new DBJsonResponse($serviceResponse, 'Unidades encaminhadoras listadas com sucesso!', 200);
    }
    public function getByUnidade(Request $request)
    {
        $this->validationFields($request);
        $serviceResponse = $this->service->getByUnidade($request->all());
        return new DBJsonResponse($serviceResponse, 'Unidade encaminhadora listada com sucesso!', 200);
    }
    
    public function save(UnidadesEncaminhadorasRequest $request)
    {
        $serviceResponse = $this->service->saveUnidade($request->all());
        return new DBJsonResponse([], 'Unidade encaminhadora salva com sucesso!', 200);
    }

    public function delete(Request $request)
    {
        $this->validationFields($request);
        $serviceResponse = $this->service->deleteUnidade($request->all());
        return new DBJsonResponse([], 'Unidade encaminhadora excluída com sucesso!', 200);
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
