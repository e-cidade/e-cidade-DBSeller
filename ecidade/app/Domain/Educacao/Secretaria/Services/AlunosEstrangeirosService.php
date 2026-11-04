<?php

namespace App\Domain\Educacao\Secretaria\Services;

use App\Domain\Educacao\Escola\Enums\NacionalidadeEnum;
use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Secretaria\Repositories\AlunoRepository;
use App\Domain\Educacao\Secretaria\Resources\AlunosEstrangeirosResource;

class AlunosEstrangeirosService
{
    private $repository;
    public function __construct(AlunoRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getAlunosEstrangeiros($escola, $ano)
    {
        $campos = [
            'ed47_i_codigo', 'ed47_v_nome', 'ed47_v_sexo', 'ed47_d_nasc', 'ed60_d_datamatricula', 'ed52_i_ano',
            'ed60_d_datasaida', 'ed60_c_situacao', 'ed11_c_descr', 'ed11_i_codigo', 'ed52_i_codigo', 'ed57_i_escola'
        ];
        return $this->repository->getAlunosEstrangeiros($escola, $campos, $ano);
    }

    public function getDadosRelatorio($escolas, $ano)
    {
        $retorno = [];
        foreach ($escolas as $escola) {
            $retorno[] = (object) [
                'escola' => Escola::find($escola)->ed18_c_nome,
                'alunosEstrangeiros' => $this->getAlunosEstrangeiros($escola, $ano)->map(function ($aluno) {
                    return AlunosEstrangeirosResource::toObjectToRelatorio($aluno);
                })->unique(),
                'ano' => $ano
            ];
        }
        return $retorno;
    }
}
