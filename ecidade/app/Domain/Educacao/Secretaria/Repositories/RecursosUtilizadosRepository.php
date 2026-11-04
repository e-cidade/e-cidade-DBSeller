<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Secretaria\Models\RecursosUtilizados;

//use App\Domain\Educacao\Escola\Resources\AtendimentoEspecialResource;

class RecursosUtilizadosRepository extends BaseRepository
{
    /**
     * Model
     *
     * @var RecursosUtilizados
     */
    protected $modelClass = RecursosUtilizados::class;

    public function getRecursosUtilizados()
    {
        return $this->newQuery()
            ->distinct()
            ->orderBy('ed198_codigo')
            ->get();
    }

    public function deleteRecursoUtilizado($codigo)
    {
        return $this->newQuery()
            ->where('ed198_codigo', $codigo)
            ->delete();
    }

    public function persist($parametros)
    {

        return $this->newQuery()->create(['ed198_descricao' => $parametros['ed198_descricao']]);
    }

    public function update($parametros)
    {

        return $this->newQuery()
                    ->where('ed198_codigo', $parametros['ed198_codigo'])
                    ->update(['ed198_descricao' => $parametros['ed198_descricao']]);
    }
}
