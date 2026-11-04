<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\CentralMatriculas\Helpers\ImagensHelper;
use App\Domain\Educacao\MatriculaOnline\Enums\TiposImagensEnum;
use App\Domain\Educacao\MatriculaOnline\Models\ImagemPersonalizada;
use App\Domain\Educacao\MatriculaOnline\Resources\ImagemPersonalizadaResource;
use App\Domain\Educacao\MatriculaOnline\Services\ImagensPersonalizadasService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImagensPersonalizadasController extends Controller
{
    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        $id = array_remove($dados, 'tipo');
        $imagem = $this->helper->imagens()->update($dados, $id);
        return new DBJsonResponse($imagem);
    }

    public function excluir($tipo)
    {
        $imagem = $this->helper->imagens()->delet($tipo);
        return new DBJsonResponse($imagem);
    }

    public function index()
    {
        $imagens = $this->helper->imagens()->index();
        $tipos = collect(TiposImagensEnum::toArrayWithNames())->map(function ($tipo) use ($imagens) {
            $tp = (object) $tipo;
            switch ($tp->value) {
                case TiposImagensEnum::TOPO:
                    $tp->disabled = $imagens['topo'];
                    break;
                case TiposImagensEnum::ESQUERDA:
                    $tp->disabled = $imagens['esquerda'];
                    break;
                case TiposImagensEnum::DIREITA:
                    $tp->disabled = $imagens['direita'];
                    break;
            }
            return $tp;
        });
        $resposta = ['tipos' => $tipos, 'imagens' => []];
        foreach ($imagens as $key => $imagen) {
            if ($imagen) {
                $resposta['imagens'][$key]['url'] = env('CENTRAL_MATRICULAS_URL');
                switch ($key) {
                    case 'topo':
                        $resposta['imagens'][$key]['tipo'] = (new TiposImagensEnum(1))->toOject();
                        $resposta['imagens'][$key]['url'] .= '/imagens/topo.png';
                        break;
                    case 'esquerda':
                        $resposta['imagens'][$key]['tipo'] = (new TiposImagensEnum(2))->toOject();
                        $resposta['imagens'][$key]['url'] .= '/imagens/esquerda.png';
                        break;
                    case 'direita':
                        $resposta['imagens'][$key]['tipo'] = (new TiposImagensEnum(3))->toOject();
                        $resposta['imagens'][$key]['url'] .= '/imagens/direita.png';
                        break;
                }
            }
        }
        $resposta['imagens'] = array_values($resposta['imagens']);
        return new DBJsonResponse($resposta);
    }
}
