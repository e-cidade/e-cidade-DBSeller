<?php

namespace App\Domain\Tributario\Notificacoes\Controllers;

use App\Domain\Tributario\Notificacoes\Model\Lista;
use App\Domain\Tributario\Notificacoes\Model\ListaDeb;
use App\Domain\Tributario\Notificacoes\Repository\ListaDebRepository;
use App\Domain\Tributario\Notificacoes\Repository\ListaRepository;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListaNotificaoController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function getLista(Request $request)
    {
        try {
            $porPagina = $request->porPagina ? $request->porPagina : 10;
            $k60_descr = $request->k60_descr ? $request->k60_descr : "";
            $k60_codigo = $request->k60_codigo ? $request->k60_codigo : "";
            $k60_datadeb = $request->k60_datadeb ? $request->k60_datadeb : "";
            $k60_tipo = $request->k60_tipo ? $request->k60_tipo : "";
            $k60_filtros = $request->k60_filtros ? $request->k60_filtros : "";

            $lista = ListaRepository::getByParams(
                $k60_descr,
                $k60_codigo,
                $k60_datadeb,
                $k60_tipo,
                $k60_filtros,
                $porPagina
            )->toArray();
            return new DBJsonResponse($lista);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function verificaLista(Request $request)
    {
        try {
            $page = $request->get('page');
            $porPagina = $request->porPagina ? $request->porPagina : 10;
            $k60_codigo = $request->k60_codigo ? $request->k60_codigo : "";

            if ($k60_codigo) {
                $verifica = ListaRepository::verificaTiposDebitos($k60_codigo);
            } else {
                return new DBJsonResponse([], 'informe o codigo da lista', 400);
            }
            $tipos = '';
            $erro = false;
            foreach ($verifica as $key => $value) {
                $tipos .= ' ' . $value->k03_descr . ',';
                if (!$value->k03_parcelamento) {
                    $erro = true;
                }
            }
            if ($erro) {
                return new DBJsonResponse([], 'Lista possui debitos de ' . $tipos, 400);
            }
            $ListaDebRepository = new ListaDebRepository();
            $lista = $ListaDebRepository->getByParams($k60_codigo, $porPagina, $page);

            return new DBJsonResponse($lista);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function verificaPacelamento(Request $request)
    {
        $dados = DB::table('arretipo')
            ->join('cadtipo', 'arretipo.k03_tipo', '=', 'cadtipo.k03_tipo')
            ->where('k00_tipo', $request->k00_tipo)
            ->where('k03_parcelamento', 'true')
            ->select('k00_tipo')
            ->get();
        if (count($dados) > 0) {
            return new DBJsonResponse(['parcelamento' => true]);
        } else {
            return new DBJsonResponse(['parcelamento' => false]);
        }
    }

    public function verificaAllParcelamentos(Request $request)
    {
        if ($request->k00_tipo == '') {
            return new DBJsonResponse(['parcelamento' => false]);
        }
        $array = explode(",", $request->k00_tipo);
        $dados = DB::table('arretipo')
            ->join('cadtipo', 'arretipo.k03_tipo', '=', 'cadtipo.k03_tipo')
            ->where('k03_parcelamento', true)
            ->whereIn('k00_tipo', $array)
            ->select('k00_tipo')
            ->get();
        if (count($dados) > 0) {
            return new DBJsonResponse(['parcelamento' => true]);
        } else {
            return new DBJsonResponse(['parcelamento' => false]);
        }
    }

    public function verificatipoCDA(Request $request)
    {
        $dados = DB::table('arretipo')
            ->join('cadtipo', 'arretipo.k03_tipo', '=', 'cadtipo.k03_tipo')
            ->where('k00_tipo', $request->k00_tipo)
            ->whereIn('arretipo.k03_tipo', [5, 15, 18])
            ->select('k00_tipo')
            ->get();
        if (count($dados) > 0) {
            return new DBJsonResponse(['parcelamento' => true]);
        } else {
            return new DBJsonResponse(['parcelamento' => false]);
        }
    }

    public function verificaAllCDA(Request $request)
    {
        if ($request->k00_tipo == '') {
            return new DBJsonResponse(['parcelamento' => false]);
        }
        $array = explode(",", $request->k00_tipo);
        $dados = DB::table('arretipo')
            ->join('cadtipo', 'arretipo.k03_tipo', '=', 'cadtipo.k03_tipo')
            ->whereIn('arretipo.k03_tipo', [5, 15, 18])
            ->whereIn('k00_tipo', $array)
            ->select('k00_tipo')
            ->get();
        if (count($dados) > 0) {
            return new DBJsonResponse(['parcelamento' => true]);
        } else {
            return new DBJsonResponse(['parcelamento' => false]);
        }
    }

    public function getRotulosPesquisaLista()
    {
        $resultados = ListaRepository::getRotulosPesquisaLista();

        return new DBJsonResponse($resultados);
    }
}
