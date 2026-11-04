<?php

namespace App\Domain\Configuracao\Menu\Repositories;

use App\Domain\Configuracao\Menu\Models\Item;
use App\Domain\Core\Base\Repository\BaseRepository;

class ItensRepository extends BaseRepository
{
    protected $modelClass = Item::class;

    public function getMenusFilho($codigoItem, $codigoModulo)
    {
        $query = $this->newQuery()
            ->select('db_itensmenu.*')
            ->join('db_menu', 'db_menu.id_item_filho', 'db_itensmenu.id_item')
            ->where('db_menu.id_item', $codigoItem)
            ->where('db_menu.modulo', $codigoModulo);

        return $query->orderBy('db_menu.menusequencia')->get();
    }
}
