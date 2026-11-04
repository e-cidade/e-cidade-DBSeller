<?php

namespace App\Domain\Tributario\Juridico\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Core\Services\QueueService;
use App\Domain\Tributario\Juridico\Repository\ListaCDARepository;
use App\Jobs\Tributario\Juridico\CancelamentoCDALista;
use Check;
use DBString;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnulacaoCDAPorListaController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function processar(Request $request)
    {
        $valida = Check::VaidacaoDados([
            [$request->k60_codigo, 'numeric', 15]
        ]);
        if ($valida) {
            return new DBJsonResponse([], 'Dados Invalidos', 400);
        }
        $observacao    = $request->v15_observacao ? (str_replace("'", "", $request->v15_observacao)) : '';
        $observacao    = 'Anulada conforme lista: ' . $request->k60_codigo . ' - ' . $observacao;
        $observacao    = DBString::utf8_encode_all($observacao);
        $DB_anousu     = db_getsession("DB_anousu");
        $DB_instit     = db_getsession("DB_instit");
        $DB_datausu    = db_getsession("DB_datausu");
        $DB_id_usuario = db_getsession('DB_id_usuario');
        $DB_acessado   = db_getsession("DB_acessado");

        try {
            if ($request->k60_codigo) {
                $verifica = ListaCDARepository::verificaTiposDebitos($request->k60_codigo);
            } else {
                return new DBJsonResponse([], 'informe o codigo da lista', 400);
            }
            $tipos = '';
            $erro = false;
            foreach ($verifica as $key => $value) {
                $tipos .= ' ' . $value->k03_descr . ',';
                if ($value->k03_tipo != 15 && $value->k03_tipo != 18) {
                    $erro = true;
                }
            }
            if ($erro) {
                return new DBJsonResponse([], 'Lista possui debitos de ' . $tipos, 400);
            }

            $queueService = new QueueService(CancelamentoCDALista::class);

            if (Cache::has('anulacaocda')) {
                return new DBJsonResponse([], 'Ja existe um processo em andamento', 400);
            }

            $cdas = ListaCDARepository::cdaslAtivas($request->k60_codigo);

            if (count($cdas) == 0) {
                return new DBJsonResponse([], 'Sem Parcelamentos Ativos', 400);
            }

            $array = array(
                "total" => count($cdas),
                "id"    => $queueService->getBatch()->id

            );
            Cache::store('file')->forever('anulacaocda', json_encode($array));

            $total = 0;

            foreach ($cdas as $cda) {
                dispatch(
                    (new CancelamentoCDALista(
                        $cda->v13_certid,
                        $observacao,
                        $cda->v51_inicial,
                        $DB_anousu,
                        $DB_instit,
                        $DB_datausu,
                        $DB_id_usuario,
                        $DB_acessado,
                        $queueService
                    )
                    )
                );
                $total++;
            }
            return new DBJsonResponse([]);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    /**
     * @return DBJsonResponse
     */
    public function getprocessamento()
    {
        $existCache = Cache::has('anulacaocda');

        if ($existCache) {
            $cache = Cache::get('anulacaocda');
            $array = json_decode($cache);
            $jobs = DB::select('select count(1) from queued_jobs where batch_id = ' . $array->id);
            $totaljobs = $jobs[0]->count;

            if ($totaljobs > 0) {
                $total = 100 - (round(($totaljobs / $array->total) * 100));

                return new DBJsonResponse([
                    "processamento" => true,
                    "quantidade" => $total
                ]);
            } else {
                Cache::forget('anulacaocda');
            }
        } else {
            return new DBJsonResponse(["processamento" => false]);
        }
    }

    public function verificaLista(Request $request)
    {
        try {
            $page = $request->get('page');
            $porPagina = $request->porPagina ? $request->porPagina : 10;
            $k60_codigo = $request->k60_codigo ? $request->k60_codigo : "";

            if ($k60_codigo) {
                $verifica = ListaCDARepository::verificaTiposDebitos($k60_codigo);
            } else {
                return new DBJsonResponse([], 'informe o codigo da lista', 400);
            }

            $tipos = '';
            $erro = false;
            foreach ($verifica as $key => $value) {
                $tipos .= ' ' . $value->k03_descr . ',';
                if ($value->k03_tipo != 15 && $value->k03_tipo != 18) {
                    $erro = true;
                }
            }
            if ($erro) {
                return new DBJsonResponse([], 'Lista possui debitos de ' . $tipos, 400);
            }
            $ListaDebRepository = new ListaCDARepository();
            $lista = $ListaDebRepository->getLista($k60_codigo, $porPagina, $page);

            return new DBJsonResponse($lista);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
