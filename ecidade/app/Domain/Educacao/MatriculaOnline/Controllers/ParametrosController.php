<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Enums\TipoParametroEnum;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParametrosController extends Controller
{
    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }

    public function salvar(Request $request)
    {
        return  new DBJsonResponse($this->helper->parametros()->save($request->all()));
    }

    public function index()
    {
        $parametros = $this->helper->parametros()->first();
        unset($parametros['id']);
        $dados = collect(array_keys($parametros))->map(function ($tipo) use ($parametros) {
            $objeto = (object)[];
            $enum = new TipoParametroEnum($tipo);
            $objeto->campo = $enum->value();
            $objeto->label = $enum->name();
            $objeto->componente = $enum->componente();
            $objeto->data = $parametros[$tipo];

            if ($objeto->componente === 'Dropdown') {
                $objeto->options = $enum->options();
                $dado = "";
                foreach ($objeto->options as $opt) {
                    if ($opt->value === $parametros[$tipo]) {
                        $dado = $opt;
                    }
                }
                $objeto->data = $dado;
            }

            return $objeto;
        });
        return new DBJsonResponse($dados);
    }
}
