<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Saude\Ambulatorial\Models\UnidadesOrigem;

class UnidadesOrigemRepository extends UnidadesOrigem
{

    protected $model;

    public function __construct()
    {
        $this->model = new UnidadesOrigem();
    }
    public function getByUnidade($dados)
    {
        $unidadesOrigem = UnidadesOrigem::find($dados['codigo']);
        return $unidadesOrigem;
    }

    public function get()
    {
        $campos = [
            'sd113_sequencial as codigo',
            'sd113_descricao as descricao',
            'sd113_ativo as ativo'
        ];
        $unidadesOrigem = UnidadesOrigem::select($campos)
        ->orderBy('sd113_sequencial', 'DESC')
        ->get();
        return $unidadesOrigem;
    }

    public function createUnidade($dados)
    {
        $this->model->sd113_descricao = $dados["descricao"];
        $this->model->sd113_ativo = $dados["ativo"];
        return $this->model->save();
    }

    public function updateUnidade($dados)
    {
        $unidadesOrigem = UnidadesOrigem::find($dados['codigo']);

        if ($unidadesOrigem) {
            $unidadesOrigem->sd113_descricao = $dados["descricao"];
            $unidadesOrigem->sd113_ativo = $dados["ativo"];
            $unidadesOrigem->save();
        }
        return $unidadesOrigem;
    }
       
    public function deleteUnidade($dados)
    {
        $unidadesOrigem = UnidadesOrigem::find($dados['codigo']);
        if ($unidadesOrigem) {
            $unidadesOrigem->delete();
        }
        return $unidadesOrigem;
    }
}
