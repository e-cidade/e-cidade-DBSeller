<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Saude\Ambulatorial\Models\UnidadesEncaminhadoras;

class UnidadesEncaminhadorasRepository extends UnidadesEncaminhadoras
{

    protected $model;

    public function __construct()
    {
        $this->model = new UnidadesEncaminhadoras();
    }
    public function getByUnidade($dados)
    {
        $unidadesEncaminhadoras = UnidadesEncaminhadoras::find($dados['codigo']);
        return $unidadesEncaminhadoras;
    }

    public function get()
    {
        $campos = [
            'sd112_sequencial as codigo',
            'sd112_descricao as descricao',
            'sd112_ativo as ativo'
        ];
        $unidadesEncaminhadoras = UnidadesEncaminhadoras::select($campos)
        ->orderBy('sd112_sequencial', 'DESC')
        ->get();
        return $unidadesEncaminhadoras;
    }

    public function createUnidade($dados)
    {
        $this->model->sd112_descricao = $dados["descricao"];
        $this->model->sd112_ativo = $dados["ativo"];
        return $this->model->save();
    }

    public function updateUnidade($dados)
    {
        $unidadesEncaminhadoras = UnidadesEncaminhadoras::find($dados['codigo']);

        if ($unidadesEncaminhadoras) {
            $unidadesEncaminhadoras->sd112_descricao = $dados["descricao"];
            $unidadesEncaminhadoras->sd112_ativo = $dados["ativo"];
            $unidadesEncaminhadoras->save();
        }
        return $unidadesEncaminhadoras;
    }
       
    public function deleteUnidade($dados)
    {
        $unidadesEncaminhadoras = UnidadesEncaminhadoras::find($dados['codigo']);
        if ($unidadesEncaminhadoras) {
            $unidadesEncaminhadoras->delete();
        }
        return $unidadesEncaminhadoras;
    }
}
