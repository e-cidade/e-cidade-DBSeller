<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Secretaria\Models\RecursosUtilizados;
use App\Domain\Educacao\Secretaria\Models\RecursosAtendimentos;

//use App\Domain\Educacao\Escola\Resources\AtendimentoEspecialResource;

class RecursosAtendimentosRepository extends BaseRepository
{
    /**
     * Model
     *
     * @var RecursosAtendimentos
     */
    protected $modelClass = RecursosAtendimentos::class;

    public function getRecursosAtendimentos($aluno)
    {
        return $this->newQuery()
            ->join('alunoatendimentoespecial', 'ed197_codigo', '=', 'ed199_alunoatendimentos')
            ->join('recursosutilizados', 'ed198_codigo', '=', 'ed199_recursosutilizados')
            ->where('ed197_aluno', $aluno)
            ->get();
    }

    public function deleteRecursoAtendimentos($codigo)
    {
        return $this->newQuery()
            ->where('ed199_alunoatendimentos', $codigo)
            ->delete();
    }

    public function persist($codigoAtendimento, $parametros)
    {
        foreach ($parametros['recursos'] as $recurso) {
            $this->newQuery()->create([
                'ed199_alunoatendimentos' => $codigoAtendimento,
                'ed199_recursosutilizados' => $recurso
            ]);
        }
        return true;
    }

    public function update($parametros)
    {
        foreach ($parametros['recursos'] as $key => $recurso) {
            if ($key < count($parametros['recursoAtendimento'])) {
                tap($this->newQuery()
                    ->find($parametros['recursoAtendimento'][$key]))
                    ->update([
                        'ed199_alunoatendimentos' => $parametros['codigo'],
                        'ed199_recursosutilizados' => $recurso
                    ]);
            } else {
                $this->newQuery()->create([
                    'ed199_alunoatendimentos' => $parametros['codigo'],
                    'ed199_recursosutilizados' => $recurso
                ]);
            }
        }
        return true;
    }
}
