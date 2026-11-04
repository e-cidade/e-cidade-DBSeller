<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Enums\NacionalidadeEnum;
use App\Domain\Educacao\Escola\Models\Aluno;

class AlunoRepository extends BaseRepository
{
    protected $modelClass = Aluno::class;
    public function getAlunosEstrangeiros($escola, $campos, $ano)
    {
        return $this->newQuery()
            ->select($campos)
            ->join('matricula', 'ed60_i_aluno', '=', 'ed47_i_codigo')
            ->join('turma', 'ed60_i_turma', '=', 'ed57_i_codigo')
            ->join('regencia', 'ed59_i_turma', '=', 'ed57_i_codigo')
            ->join('serie', 'ed59_i_serie', '=', 'ed11_i_codigo')
            ->join('calendario', 'ed57_i_calendario', '=', 'ed52_i_codigo')
            ->where('ed47_i_nacion', NacionalidadeEnum::ESTRANGEIRA)
            ->where('ed57_i_escola', $escola)
            ->where('ed52_i_ano', $ano)
            ->distinct()
            ->get();
    }
}
