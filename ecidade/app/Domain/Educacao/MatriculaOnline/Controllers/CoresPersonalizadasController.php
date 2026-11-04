<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Enums\TipoCorEnum;
use App\Domain\Educacao\CentralMatriculas\Enums\TipoMensagemEnum;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\MatriculaOnline\Models\CorPersonalizada;
use App\Domain\Educacao\MatriculaOnline\Models\ItemCorPersonalizada;
use App\Domain\Educacao\MatriculaOnline\Repositories\CorPersonalizadaRepository;
use App\Domain\Educacao\MatriculaOnline\Resources\CorPersonalizadaResource;
use App\Domain\Educacao\MatriculaOnline\Resources\ItemCorPersonalizadaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoresPersonalizadasController extends Controller
{
    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        $id = array_remove($dados, 'codigo');
        $cores = $this->helper->cores()->update($dados, $id);
        return new DBJsonResponse($cores);
    }

    public function index()
    {
        $cores = collect($this->helper->cores()->index())->map(function ($cor) {
            $cr = $cor;
            $cr['tipo'] = (new TipoCorEnum($cr['tipo']))->toOject();
            return $cr;
        });
        return new DBJsonResponse($cores);
    }
}
