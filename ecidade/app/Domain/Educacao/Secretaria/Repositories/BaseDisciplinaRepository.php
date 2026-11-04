<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Secretaria\Models\BaseDisciplina;

class BaseDisciplinaRepository extends BaseRepository
{
    protected $modelClass = BaseDisciplina::class;

    public function getPorBaseEtapa($base, $etapa)
    {
        $disciplinas = $this->newQuery()->where('ed34_i_base', $base)->where('ed34_i_serie', $etapa)
            ->orderBy('ed34_i_ordenacao')->get();
        return $disciplinas;
    }

    public function salvar($parametros)
    {
        $pk = $parametros['ed34_i_codigo'];
        unset($parametros['ed34_i_codigo']);

        $parametros['ed34_i_ordenacao'] = is_null($parametros['ed34_i_ordenacao']) ?
            ($this->getQuantosRegistros($parametros['ed34_i_base'], $parametros['ed34_i_serie']) + 1) :
            $parametros['ed34_i_ordenacao'];

        if (is_null($pk)) {
            return $this->newQuery()->updateOrCreate(
                [
                    'ed34_i_base' => $parametros['ed34_i_base'],
                    'ed34_i_disciplina' => $parametros['ed34_i_disciplina'],
                    'ed34_i_serie' => $parametros['ed34_i_serie']
                ],
                $parametros
            );
        } else {
            return tap($this->newQuery()->find($pk))->update($parametros);
        }
    }

    public function getQuantosRegistros($base, $etapa)
    {
        return count($this->getPorBaseEtapa($base, $etapa));
    }

    public function excluir($codigo)
    {
        return $this->newQuery()->find($codigo)->delete();
    }
}
