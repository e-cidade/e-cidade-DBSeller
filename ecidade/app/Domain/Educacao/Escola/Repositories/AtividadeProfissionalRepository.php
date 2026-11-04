<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\AtividadeProfissional;

class AtividadeProfissionalRepository extends BaseRepository
{
    protected $modelClass = AtividadeProfissional::class;

    public function index()
    {
        return $this->newQuery()->get();
    }

    public function getByFiltros(array $filtros)
    {
        $query = $this->newQuery();

        foreach ($filtros as $key => $filtro) {
            switch ($key) {
                case 'atividade':
                    $query->whereIn('ed01_i_codigo', $filtro);
                    break;
                case 'situacaoAtividade':
                    $query->whereHas('atividadesProfissionaisEscola', function ($query) use ($filtro) {
                        $query->where('ed22_ativo', $filtro);
                    });
                    break;
                case 'situacaoServidores':
                    $query->whereHas('atividadesProfissionaisEscola', function ($query) use ($filtro) {
                        $query->whereHas('profissionalEscola', function ($query) use ($filtro) {
                            $operador = $filtro ? '=' : '<>';
                            $query->where('ed75_i_saidaescola', $operador, null);
                        });
                    });
                    break;
            }
        }

        $retorno = $query->get();

        return $retorno;
    }
}
