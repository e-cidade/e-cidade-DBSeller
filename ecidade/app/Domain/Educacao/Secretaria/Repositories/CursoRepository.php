<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\CursoEdu;

class CursoRepository extends BaseRepository
{
    protected $modelClass = CursoEdu::class;

    public function salvar($parametros)
    {
        $dados = [
            'ed29_c_descr' => $parametros['nomeCurso'],
            'ed29_i_ensino' => $parametros['nivelEnsino'],
            'ed29_censocursoprofiss' =>
                $parametros['cursoProfissional'] === '' ? null : $parametros['cursoProfissional'],
            'ed29_c_historico' => $parametros['incluiHistorico'] == 1 ? 'S' : 'N',
            'ed29_i_avalparcial' => $parametros['aprovParcial'] == '' ? 0 : 1,
            'ed29_ativo' => $parametros['isAtivo'] == '1'
        ];

        if (isset($parametros['codigo'])) {
            return tap($this->newQuery()->find($parametros['codigo']))->update($dados);
        } else {
            return $this->newQuery()->create($dados);
        }
    }

    public function excluir($codigo)
    {
        return $this->newQuery()->find($codigo)->delete();
    }

    public function getDisciplinas($curso)
    {
        $disciplinas = $this->newQuery()->find($curso)->ensino->disciplinas;
        return $disciplinas;
    }
}
