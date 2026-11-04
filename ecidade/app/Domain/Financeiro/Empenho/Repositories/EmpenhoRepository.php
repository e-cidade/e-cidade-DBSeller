<?php

namespace App\Domain\Financeiro\Empenho\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Empenho\Models\Empenho;
use Illuminate\Support\Facades\DB;

class EmpenhoRepository extends BaseRepository
{
    protected $modelClass = Empenho::class;


    /**
     *
     * @param $filtros
     * @return mixed
     */
    public function getDataForDialogPesquisa($filtros)
    {
        $campos = [
            "e60_numemp", "e60_codemp", "e60_anousu", "e60_emiss", "e60_numcgm", "z01_nome",
            "e60_vlremp", "e60_vlranu", "e60_vlrliq", "e60_vlrpag",
            DB::raw("(e60_vlrliq-e60_vlrpag)::numeric(17,2) as saldo_liquido"),
            DB::raw("(e60_vlremp-e60_vlranu-e60_vlrpag)::numeric(17,2) AS saldo")
        ];

        return $this->newQuery()
            ->select($campos)
            ->join('protocolo.cgm', 'z01_numcgm', '=', 'e60_numcgm')
            ->join('configuracoes.db_config', 'codigo', '=', 'e60_instit')
            ->where('e60_instit', $filtros['instituicao'])
            ->when(!empty($filtros['numeroEmpenho']), function ($query) use ($filtros) {
                $query->whereRaw("e60_codemp || '/'|| e60_anousu = '{$filtros['numeroEmpenho']}'");
            })
            ->when(!empty($filtros['numemp']), function ($query) use ($filtros) {
                $query->where('e60_numemp', $filtros['numemp']);
            })
            ->when(!empty($filtros['numero']) && !empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('e60_codemp', $filtros['numero']);
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('e60_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['dataInicial']), function ($query) use ($filtros) {
                $query->where('e60_emiss', '>=', $filtros['dataInicial']);
            })
            ->when(!empty($filtros['dataFinal']), function ($query) use ($filtros) {
                $query->where('e60_emiss', '<=', $filtros['dataFinal']);
            })
            ->when(!empty($filtros['sortField']), function ($query) use ($filtros) {
                $query->orderBy($filtros['sortField'], $filtros['sortOrder']);
            })
            ->when(empty($filtros['sortField']), function ($query) {
                $query->orderBy('e60_numemp');
            })
            ->paginate($filtros["rows"]);
    }
}
