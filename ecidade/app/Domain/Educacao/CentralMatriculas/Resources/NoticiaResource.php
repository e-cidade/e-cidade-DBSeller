<?php

namespace App\Domain\Educacao\CentralMatriculas\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\Noticia;

class NoticiaResource
{
    public static function toResponse(Noticia $noticia)
    {
        return (object) [
            'codigo' => $noticia->mo22_id,
            'titulo' => stripslashes($noticia->mo22_titulo),
            'data' => $noticia->mo22_data->format('Y-m-d'),
            'texto' => stripslashes($noticia->mo22_texto),
            'imagem' => stripslashes($noticia->imagem),
            'ativa' => $noticia->mo22_ativa
        ];
    }
}
