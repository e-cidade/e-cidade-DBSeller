<?php

namespace App\Domain\Tributario\Juridico\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Core\Services\QueueService;
use App\Domain\Tributario\Juridico\Repository\CDARepository;
use App\Domain\Tributario\Juridico\Repository\ListaCDARepository;
use App\Jobs\Tributario\Juridico\InclusaoInicialListaJob;
use Certidao;
use Check;
use DBString;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IncluirInicialListaController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function processar(Request $request)
    {
        $valida = Check::VaidacaoDados([
            [$request->k60_codigo, 'numeric', 15],
            [$request->agrupa, null, 2]
        ]);

        if ($valida) {
            return new DBJsonResponse([], 'Dados Invalidos', 400);
        }
        $agrupa = $request->agrupa;
        $v50_advog = $request->v50_advog;
        $v50_codlocal = $request->v50_codlocal;
        $observacao    = 'Incluida conforme lista: ' . $request->k60_codigo;
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

            $queueService = new QueueService(InclusaoInicialListaJob::class);

            if (Cache::has('inclusaoiniciallista')) {
                return new DBJsonResponse([], 'Ja existe um processo em andamento', 400);
            }
            $cdas = CDARepository::getCDAMatricInscr($request->k60_codigo, $agrupa);

            if (count($cdas) == 0) {
                return new DBJsonResponse([], 'Sem Parcelamentos Ativos', 400);
            }

            $array = array(
                "total" => count($cdas),
                "id"    => $queueService->getBatch()->id

            );
            Cache::store('file')->forever('inclusaoiniciallista', json_encode($array));

            $matric_ant = "";
            $inscr_ant  = "";
            $cgm_ant    = "";
            $cert_ant   = "";
            $gera       = false;

            if (count($cdas) > 0) {
                $gera = false;
                for ($w = 0; $w < count($cdas); $w++) {
                    $obj = $cdas[$w];
                    $oCertidao = new Certidao($obj->certid);

                    if ($oCertidao->isCobrancaExtrajudicial()) {
                        break;
                    }

                    if ($agrupa == "mi") {
                        if ($obj->matric != "") {
                            if ($obj->matric != $matric_ant) {
                                $gera = true;
                                $matric_ant = $obj->matric;
                                $cert_ant = $obj->certid;
                            } else {
                                $gera = false;
                            }
                        } elseif ($obj->inscr != "") {
                            if ($obj->inscr != $inscr_ant) {
                                $gera = true;
                                $inscr_ant = $obj->inscr;
                                $cert_ant = $obj->certid;
                            } else {
                                $gera = false;
                            }
                        } else {
                            $gera = true;
                        }
                    } elseif ($agrupa == "c") {
                        if ($obj->numcgm != $cgm_ant) {
                            $gera = true;
                            $cgm_ant = $obj->numcgm;
                            $cert_ant = $obj->certid;
                        } else {
                            $gera = false;
                        }
                    } else {
                        $gera = true;
                    }
                    dispatch(
                        (new InclusaoInicialListaJob(
                            $obj->certid,
                            $observacao,
                            $v50_advog,
                            $v50_codlocal,
                            $DB_anousu,
                            $DB_instit,
                            $DB_datausu,
                            $DB_id_usuario,
                            $DB_acessado,
                            $queueService,
                            $gera,
                            $cert_ant
                        )
                        )
                    );
                }
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
        $existCache = Cache::has('inclusaoiniciallista');

        if ($existCache) {
            $cache = Cache::get('inclusaoiniciallista');
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
                Cache::forget('inclusaoiniciallista');
            }
        } else {
            return new DBJsonResponse(["processamento" => false]);
        }
    }

    public function verificaLista(Request $request)
    {
        try {
            $k60_codigo = $request->k60_codigo ? $request->k60_codigo : "";

            if ($k60_codigo) {
                $verifica = ListaCDARepository::verificaTiposDebitos($k60_codigo);
            } else {
                return new DBJsonResponse([], 'Informe o codigo da lista', 400);
            }

            $tipos = '';
            $erro = false;
            foreach ($verifica as $key => $value) {
                $tipos .= ' ' . $value->k03_descr . ',';
                if ($value->k03_tipo != 15) {
                    $erro = true;
                }
            }
            if ($erro) {
                return new DBJsonResponse([], 'Lista possui debitos de ' . $tipos, 400);
            }
            $ListaDebRepository = new ListaCDARepository();
            $lista = $ListaDebRepository->pesquisaCDAinclusaoInicial($k60_codigo);

            return new DBJsonResponse($lista);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    public function gethistorico(Request $request)
    {
        try {
            $k60_codigo = $request->k60_codigo ? $request->k60_codigo : "";
            if ($k60_codigo == '') {
                return new DBJsonResponse([], 'Informe o codigo da lista', 400);
            }
            $ListaDebRepository = new ListaCDARepository();
            $lista =  $ListaDebRepository->getHistoricoInicial($k60_codigo);
            return new DBJsonResponse($lista[0]);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 404);
        }
    }
}
