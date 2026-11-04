<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait Pagination
{
    public function paginate($items = [], $porPagina = 5, $pagina = null, $options = [])
    {
        $pagina = $pagina ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($pagina, $porPagina)->values(),
            $items->count(),
            $porPagina,
            $pagina,
            $options
        );
    }
}
