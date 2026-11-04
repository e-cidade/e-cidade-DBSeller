<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers;

use App\Domain\Tributario\Notificacoes\Model\Lista;
use App\Domain\Tributario\Notificacoes\Repository\ListaDebRepository;
use App\Domain\Tributario\Notificacoes\Repository\ListaRepository;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Core\Services\QueueService;
use App\Jobs\Tributario\Arrecadacao\CancelamentoParcelamentoJob;
use DBString;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CancelamentoParcelamentoPorListaController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function processar(Request $request)
    {
        $motivo = $request->motivo ? DBString::utf8_encode_all($request->motivo) : '';
        $motivo = 'Parcelamento anulado conforme lista: ' . $request->k60_codigo . ' - ' . $motivo;
        $processo = $request->processo ? $request->processo : '';
        $DB_anousu = db_getsession("DB_anousu");
        $DB_instit = db_getsession("DB_instit");
        $DB_datausu = date("Y-m-d", db_getsession("DB_datausu"));
        $DB_id_usuario = db_getsession('DB_id_usuario');

        try {
            if ($request->k60_codigo) {
                $verifica = ListaRepository::verificaTiposDebitos($request->k60_codigo);
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

            if (Cache::has('cancelaParcelamentolista')) {
                return new DBJsonResponse([], 'Ja existe um processo em andamento', 400);
            }

            $queueService = new QueueService(CancelamentoParcelamentoJob::class);

            $parcels = ListaDebRepository::parcelAtivos($request->k60_codigo);

            if (count($parcels) == 0) {
                return new DBJsonResponse([], 'Sem Parcelamentos Ativos', 400);
            }
            
            $array = array(
                "total" => count($parcels),
                "id" =>  $queueService->getBatch()->id
            );
            Cache::store('file')->forever('cancelaParcelamentolista', json_encode($array));

            foreach ($parcels as $parcel) {
                dispatch((new CancelamentoParcelamentoJob(
                    $parcel->parcelamento,
                    $motivo,
                    $processo,
                    $DB_anousu,
                    $DB_instit,
                    $DB_datausu,
                    $DB_id_usuario,
                    $queueService
                )
                ));
            }
            return new DBJsonResponse([]);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function getprocessamento()
    {
        $existCache = Cache::has('cancelaParcelamentolista');

        if ($existCache) {
            $cache = Cache::get('cancelaParcelamentolista');
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
                Cache::forget('cancelaParcelamentolista');
            }
        } else {
            return new DBJsonResponse(["processamento" => false]);
        }
    }
}
