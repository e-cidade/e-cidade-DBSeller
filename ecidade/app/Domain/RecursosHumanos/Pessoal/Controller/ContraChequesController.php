<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Core\Models\BatchJob;
use App\Domain\Core\Models\FailedJob;
use App\Domain\Core\Models\QueuedJob;
use App\Domain\RecursosHumanos\Pessoal\Services\ContraChequeService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

class ContraChequesController extends Controller
{
    /**
     * @param  Request  $request
     *
     * @return DBJsonResponse
     * @throws Exception
     */
    public function processarEmissao(Request $request)
    {
        validaRequest(
            $request->all(),
            [
                'DB_instit' => ['required', 'integer'],
                'ano'       => ['required', 'integer'],
                'mes'       => ['required', 'integer'],
            ],
            [
                'DB_instit.required' => 'Instituição não informada',
            ]
        );

        $contraChequeService = new ContraChequeService();
        $contraChequeService->gerarContraChequePdf(
            $request->get('ano'),
            $request->get('mes'),
            db_getsession('DB_instit')
        );

        return new DBJsonResponse(
            [],
            'Emissão de contra cheques gerada com sucesso.'
        );
    }

    /**
     * @param  Request  $request
     *
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarEmitidos(Request $request)
    {
        if (! $request->has('DB_instit')) {
            throw new Exception("Instituição não informada");
        }

        $contrachequeService = new ContraChequeService();
        $itens
                             = $contrachequeService->buscarLotes($request->get('DB_instit'));

        return new DBJsonResponse($itens, '');
    }

    public function cancelarEmissao(Request $request)
    {
        if (! $request->has('batch_id')) {
            throw new Exception("Código do Lote não informado");
        }

        $batchJob            = BatchJob::find($request->get('batch_id'));
        $batchJob->cancelled = true;
        $batchJob->save();

        return new DBJsonResponse([], 'Lote cancelado');
    }

    /**
     * @param $batch_id
     *
     * @return DBJsonResponse
     */
    public function falhas($batch_id)
    {
        $falhas = QueuedJob::where(
            'batch_id',
            $batch_id
        )->whereNotNull('failed_at')
                           ->with('failedJob')
                           ->get();

        $falhas->map(function ($falha) {
            $payload      = json_decode($falha->payload);
            $job          = unserialize($payload->data->command);
            $contracheque = $job->getContraChequePdf();
            if ($contracheque) {
                $falha->contracheque = [
                    'ano'         => $contracheque->getAno(),
                    'folha'       => $contracheque->getFolha(),
                    'instituicao' => $contracheque->getInstituicao(),
                    'matricula'   => $contracheque->getMatricula(),
                    'mes'         => $contracheque->getMes(),
                ];
            }

            return $falha;
        });

        return new DBJsonResponse($falhas);
    }

    public function reprocessarFalha(Request $request)
    {
        $this->validate($request, [
            'jobs.*.queuedjob_id' => 'required|numeric',
        ]);

        foreach ($request->input("jobs") as $job) {
            $queuedJob = QueuedJob::find($job["queuedjob_id"]);

            if (! $queuedJob) {
                throw new \Exception("Falha não encontrada!");
            }

            if (! empty($job["failedjob_id"])) {
                $failedJob = FailedJob::find($job["failedjob_id"]);
            }


            if (! empty($failedJob)) {
                Artisan::call("queue:retry", [
                    'id' => [$job["failedjob_id"]],
                ]);
            } else {
                $payload = json_decode($queuedJob->payload, true);
                if (isset($payload['attempts'])) {
                    $payload['attempts'] = 0;
                }

                Queue::connection($queuedJob->job_connection)->pushRaw(
                    json_encode($payload),
                    $queuedJob->queue
                );
            }

            $queuedJob->failed_at = null;
            $queuedJob->save();
        }


        return new DBJsonResponse(
            [],
            "Contra cheque será processado! acompanhe"
        );
    }
}
