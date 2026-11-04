<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\HabilidadeDsenvolvidaReferencial;

class HabilidadeDesenvolvidaReferencialRepository extends BaseRepository
{
    protected $modelClass = HabilidadeDsenvolvidaReferencial::class;
    public function salvar($parametros)
    {
        $pk = array_remove($parametros, 'ed169_codigo');
        if (is_null($pk)) {
            return $this->newQuery()->create($parametros);
        } else {
            return tap($this->find($pk))->update($parametros);
        }
    }
}
