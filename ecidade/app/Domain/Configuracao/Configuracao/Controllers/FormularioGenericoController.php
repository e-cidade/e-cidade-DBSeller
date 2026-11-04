<?php

namespace App\Domain\Configuracao\Configuracao\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Configuracao\Configuracao\Services\FormularioGenericoService;
use stdClass;

class FormularioGenericoController extends Controller

{
    public function getAll(Request $request,$id, $instituicao = null)
    {
        $filter = $request->get('filter');
        $page = new stdClass();
        $page = [
            "number" => $request->get('number_page'),
            "size" => $request->get('size_page')
        ];
     
        $formulario = new FormularioGenericoService($filter,$page);

        return $formulario->getAll($id,$instituicao);

    }

}