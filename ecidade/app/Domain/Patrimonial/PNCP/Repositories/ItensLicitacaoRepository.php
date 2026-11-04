<?php

namespace App\Domain\Patrimonial\PNCP\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Patrimonial\Licitacoes\Models\Item;
use Illuminate\Support\Facades\DB;

class ItensLicitacaoRepository extends BaseRepository
{
    protected $modelClass = Item::class;

    public function get($campos, $where = null, $groupBy = null)
    {
        $query = $this->newQuery()
            ->selectRaw($campos)
            ->join('pcprocitem', 'pc81_codprocitem', 'l21_codpcprocitem')
            ->join('pcproc', 'pc80_codproc', 'pc81_codproc')
            ->join('solicitem', 'pc11_codigo', 'pc81_solicitem')
            ->leftJoin('solicitempcmater', 'pc16_solicitem', 'pc11_codigo')
            ->leftJoin('pcmater', 'pc01_codmater', 'pc16_codmater')
            ->leftJoin('solicitemunid', 'pc17_codigo', 'pc11_codigo')
            ->leftJoin('matunid', 'm61_codmatunid', 'pc17_unid')
            ->leftJoin('pcorcamitemlic', 'pc26_orcamitem', 'pc26_liclicitem')
            ->leftJoin('pcorcamval', 'pc26_orcamitem', 'pc23_orcamitem');

        if ($where != null) {
            $query->where('l21_codliclicita', $where);
        }

        if ($groupBy != null) {
            $query->groupBy($groupBy);
        }

        return $query->get();
    }
}
