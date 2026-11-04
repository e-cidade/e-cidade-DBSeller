<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Secretaria\Models\TiposIntrumentosAvaliativos;
use App\Domain\Educacao\Secretaria\Resources\TiposInstrumentosAvaliativosResource;

class TiposIntrumentosAvaliativosRepository extends BaseRepository
{
    protected $modelClass = TiposIntrumentosAvaliativos::class;

    public function get($id)
    {
        return $this->newQuery()->find($id);
    }

    public function index()
    {
        return $this->newQuery()->get();
    }

    public function getByEnsino($ensino)
    {
        return $this->newQuery()->where('ed201_ensinos', '@>', $ensino)->ativo()->get();
    }
    
    public function salvar($parametros)
    {
        $parametros = TiposInstrumentosAvaliativosResource::toArrayModel($parametros);
        $pk = array_remove($parametros, 'ed201_id');
        if (is_null($pk)) {
            return $this->newQuery()->create($parametros);
        } else {
            return tap($this->get($pk))->update($parametros);
        }
    }

    public function excluir($codigo)
    {
        return $this->get($codigo)->delete();
    }
}
