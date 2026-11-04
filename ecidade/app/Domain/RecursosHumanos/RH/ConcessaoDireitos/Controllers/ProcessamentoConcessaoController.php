<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\AssentConfig;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\Concessao;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\ConcessaoCalculoProviders;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\ProcessConcessaoCountProviders;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\Rhpessoal as ProvidersRhpessoal;
use App\Jobs\RecursosHumanos\RH\ProcessamentoConcessao;
use Illuminate\Support\Facades\Cache;
use App\Domain\Core\Services\QueueService;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Services\ConcessaoDireitosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessamentoConcessaoController extends Controller
{

    public function getprocessamento(Request $request)
    {
        return new DBJsonResponse(ProcessConcessaoCountProviders::getFila());
    }

    public function pararprocessamento(Request $request)
    {
        return new DBJsonResponse(ProcessConcessaoCountProviders::pararFila());
    }

    public function show()
    {
        $assenta = DB::table('assentconf')
            ->join('tipoasse', 'h12_codigo', '=', 'rh500_assentamento')
            ->select('h12_codigo', 'h12_descr', 'rh500_sequencial', 'rh500_datalimite')
            ->get();
        return new DBJsonResponse($assenta);
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $matricula = $data['matricula'];
        $inst = $data['DB_instit'];
        $rh500_sequencial = $data['rh500_sequencial'];
        $data_processamento = substr($data['dataprocessamento'], 6, 4) .
            '-' . substr($data['dataprocessamento'], 3, 2) .
            "-" . substr($data['dataprocessamento'], 0, 2);
        try {
            $assentconfig = AssentConfig::where('rh500_sequencial', $rh500_sequencial)
                ->leftJoin('pessoal.selecao', 'r44_selec', '=', 'rh500_selecao')
                ->first();

            if (!empty($matricula)) {
                if (!ProvidersRhpessoal::verificarMatriculaSelecao($assentconfig->r44_where, $matricula)) {
                    return new DBJsonResponse('', 'Funcionário sem configuração para esta Concessão', 400);
                }
                $assentconfig = AssentConfig::where('rh500_sequencial', $rh500_sequencial)->first();
                if (strtotime($data_processamento) > strtotime($assentconfig->rh500_datalimite)) {
                    $data_processamento = $assentconfig->rh500_datalimite;
                }

                $assents_envolvidos = Concessao::todosAssentamntosEnvolvidos($rh500_sequencial);
                $periodos = Concessao::periodos($rh500_sequencial);
                $conc = new ConcessaoDireitosService();
                $proc = $conc->processamento(
                    $matricula,
                    $inst,
                    $rh500_sequencial,
                    $data_processamento,
                    $assents_envolvidos,
                    $periodos
                );
                
                $result = ConcessaoCalculoProviders::buscarconcessaocaluclo($matricula, $rh500_sequencial);
                if ($proc['erro']) {
                    return new DBJsonResponse([], $proc['msg'], 400);
                } else {
                    return new DBJsonResponse($result, 'Processado com Sucesso!', 200);
                }
            } else {
                if (Cache::has('processaconcessao')) {
                    return new DBJsonResponse([], 'Ja existe um processo em andamento', 400);
                }

                $queueService = new QueueService(ProcessamentoConcessao::class);

                $matriculas = ProvidersRhpessoal::matriculas(
                    $inst,
                    $data['DB_datausu'],
                    $assentconfig->r44_where,
                    null
                );

                if (count($matriculas) == 0) {
                    return new DBJsonResponse([], 'Regsitros na?o encontrados', 400);
                }
    
                $array = array(
                    "total" => count($matriculas),
                    "id"    => $queueService->getBatch()->id
                );
                Cache::store('file')->forever('processaconcessao', json_encode($array));

                $assentconfig = AssentConfig::where('rh500_sequencial', $rh500_sequencial)->first();
                $data_processamento = $data_processamento;
                if (strtotime($data_processamento) > strtotime($assentconfig->rh500_datalimite)) {
                    $data_processamento = $assentconfig->rh500_datalimite;
                }

                foreach ($matriculas as $value) {
                    ProcessamentoConcessao::dispatch(
                        $inst,
                        $rh500_sequencial,
                        $data_processamento,
                        $data['DB_datausu'],
                        $assentconfig->r44_where,
                        $value->rh01_regist,
                        $queueService,
                        $data['DB_id_usuario']
                    );
                }
                return new DBJsonResponse('', 'Processo Iniciado!', 201);
            }
        } catch (\Exception $th) {
            ProcessConcessaoCountProviders::salvarLogErro(
                null,
                'Matricula - ERRO ao iniciar Job - ' . $th->getMessage()
            );
            return new DBJsonResponse('', 'ERRO ao iniciar Job - ' .$th->getMessage(), 400);
        }
    }
}
