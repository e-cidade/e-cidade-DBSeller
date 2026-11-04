<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\Noticia;
use Carbon\Carbon;

class NoticiaResource
{
    public static function toResponse($noticia)
    {
        $noticia = (object) $noticia;
        $resposta =  (object) [
            'titulo' => stripslashes($noticia->titulo),
            'data' => $noticia->data,
            'texto' => stripslashes($noticia->texto),
            'imagem' => env('CENTRAL_MATRICULAS_URL') . "/" . $noticia->imagem,
            'ativa' => $noticia->ativa
        ];

        if (isset($noticia->id)) {
            $resposta->codigo = $noticia->id;
        }
        return $resposta;
    }
}
