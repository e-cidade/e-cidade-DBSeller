<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\MatriculaOnline\Models\Noticia;
use App\Domain\Educacao\MatriculaOnline\Resources\NoticiaResource;
use App\Domain\Educacao\MatriculaOnline\Services\NoticiasService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    protected $helper;
    public function __construct(ConfiguracaoCentralHelper $helper)
    {
        $this->helper = $helper;
    }

    public function salvar(Request $request)
    {
        $parametros = $request->all();
        $pk = array_remove($parametros, 'id');
        if (is_null($pk)) {
            $noticia = $this->helper->noticias()->create($parametros);
        } else {
            $noticia = $this->helper->noticias()->update($parametros, $pk);
        }
        $noticia['imagem'] = env('CENTRAL_MATRICULAS_URL') . "/" .  $noticia['imagem'];
        return new DBJsonResponse($noticia);
    }

    public function excluir($id)
    {
        return new DBJsonResponse($this->helper->noticias()->delet($id));
    }

    public function index()
    {
        $noticias = collect($this->helper->noticias()->index())->map(function ($noticia) {
            $noticia['imagem'] = env('CENTRAL_MATRICULAS_URL') . "/" .  $noticia['imagem'];
            return $noticia;
        });
        return new DBJsonResponse($noticias);
    }
}
