<?php

namespace App\Domain\Configuracao\Menu\Resources;

class ItensResource
{
    public static function toTreeNode($itens)
    {
        $array = [];
        foreach ($itens as $item) {
            $array[] = self::toNode($item);
        }

        return $array;
    }

    public static function toNode($item)
    {
        return (object)[
            'key' => $item->id_item,
            'label' => $item->descricao,
            'children' => static::toTreeNode($item->filhos)
        ];
    }
}
