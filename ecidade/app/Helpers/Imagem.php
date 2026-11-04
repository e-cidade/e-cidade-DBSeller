<?php

namespace App\Helpers;

class Imagem
{

    /**
     * @param $caminhoAArquivo
     *
     * @return false|string
     */
    public static function caminhoArquivoParaBase64($caminhoAArquivo)
    {
        if (! file_exists($caminhoAArquivo)) {
            return false;
        }
        $base64 = base64_encode(file_get_contents($caminhoAArquivo));

        return 'data:'.mime_content_type($caminhoAArquivo).';base64,'.$base64;
    }
}
