<?php

namespace App\Domain\Educacao\MatriculaOnline\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\MatriculaOnline\Models\DuvidasFrequentes;
use App\Domain\Educacao\MatriculaOnline\Models\RespostaDuvidaFrequente;

class DuvidasFrequentesRepository extends BaseRepository
{
    protected $modelClass = DuvidasFrequentes::class;
    public function salvar($parametros)
    {
        $pk = array_remove($parametros, 'mo15_id');
        $respostas = array_remove($parametros, 'respostas');
        if (is_null($pk)) {
            $retorno =  $this->newQuery()->create($parametros);
            $this->salvarRespostas($retorno->mo15_id, $respostas);
            return $retorno;
        } else {
            $retorno = tap($this->newQuery()->find($pk))->update($parametros);
            $this->salvarRespostas($retorno->mo15_id, $respostas);
            return $retorno;
        }
    }

    public function index()
    {
        return $this->newQuery()->where('mo15_ativo', true)->orderBy('mo15_ordem')->get();
    }

    public function excluir($codigo)
    {
        $this->excluirRespostas($codigo);
        return $this->newQuery()->find($codigo)->delete();
    }

    public function salvarRespostas($idPergunta, $respostas)
    {
        $this->excluirRespostas($idPergunta);
        $dados = [];
        foreach ($respostas as $resposta) {
            $dados[] = [
                'mo25_pergunta' => $idPergunta,
                'mo25_resposta' => stripslashes($resposta)
            ];
        }
        RespostaDuvidaFrequente::insert($dados);
    }

    public function excluirRespostas($idPergunta)
    {
        RespostaDuvidaFrequente::where('mo25_pergunta', $idPergunta)->delete();
    }
}
