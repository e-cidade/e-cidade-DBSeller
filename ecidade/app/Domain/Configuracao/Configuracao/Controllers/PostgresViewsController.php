<?php

namespace App\Domain\Configuracao\Configuracao\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PostgresViewsController extends Controller
{
    public function index()
    {
        $dados = DB::select("select viewname as nome from pg_catalog.pg_views where schemaname = 'public'");

        return new DBJsonResponse($dados);
    }
}
