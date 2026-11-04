<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Enums\TipoMensagemEnum;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\MatriculaOnline\Models\MensagemPersonalizada;
use App\Domain\Educacao\MatriculaOnline\Models\TiposMensagensPersonalizadas;
use App\Domain\Educacao\MatriculaOnline\Repositories\MensagemPersonalizadaRepository;
use App\Domain\Educacao\MatriculaOnline\Resources\MensagemPersonalizadaResource;
use App\Domain\Educacao\MatriculaOnline\Resources\TiposMensagensPersonalizadasResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MensagensPersonalizadasController extends Controller
{

    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }
    public function index()
    {
        $mensagens = collect($this->helper->mensagens()->index())->map(function ($mensagem) {
            $msg = $mensagem;
            $msg['tipo'] = (new TipoMensagemEnum(intval($msg['tipo'])))->toOject();
            return $msg;
        });
        return new DBJsonResponse($mensagens);
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        $id = array_remove($dados, 'codigo');
        $mensagem = $this->helper->mensagens()->update($dados, $id);
        return new DBJsonResponse($mensagem);
    }
}
