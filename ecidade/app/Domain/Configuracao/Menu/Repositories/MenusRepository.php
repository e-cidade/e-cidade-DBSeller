<?php

namespace App\Domain\Configuracao\Menu\Repositories;

use App\Domain\Configuracao\Menu\Models\Menu;
use App\Domain\Core\Base\Repository\BaseRepository;

class MenusRepository extends BaseRepository
{
    protected $modelClass = Menu::class;

    public function getPosicaoMenu($codigoModulo)
    {
        return $this->newQuery()
            ->selectRaw('(max(menusequencia)+1) as posicao')
            ->where('db_menu.modulo', $codigoModulo)
            ->first()
            ->posicao;
    }
}
