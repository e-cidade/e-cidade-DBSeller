<?php

namespace App\Domain\Configuracao\Menu\Resources;

use Illuminate\Contracts\Support\Arrayable;

class ModulosResource
{
    /**
     * @param Arrayable $modulos
     * @return array
     */
    public static function toResponse(Arrayable $modulos)
    {
        $dados = [];
        foreach ($modulos as $modulo) {
            $dados[] = [
                'codigo' => $modulo->id_item,
                'descricao' => $modulo->nome_modulo
            ];
        }

        return $dados;
    }
}
