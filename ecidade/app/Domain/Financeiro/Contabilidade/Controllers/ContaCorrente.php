<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\ImportarDdrRequest;
use App\Domain\Financeiro\Contabilidade\Services\ContaCorrente\CadastroContaMSCService;
use App\Domain\Financeiro\Contabilidade\Services\ContaCorrente\ImportarDdr;
use App\Domain\Financeiro\Contabilidade\Services\ContaCorrente\TemplateDdr;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ContaCorrente extends Controller
{
    public function implantarDDR(ImportarDdrRequest $request)
    {
        /**
         * @var $file UploadedFile
         */
        $file = $request->file('file');
        $exercicio = $request->get('exercicio');

        $service = new ImportarDdr($exercicio, $file->getRealPath());
        $service->importar();

        return new DBJsonResponse([], 'Processado com sucesso.');
    }

    public function template()
    {
        $data = Carbon::createFromFormat('Y-m-d', date("Y-m-d", $_SESSION["DB_datausu"]));
        $service = new TemplateDdr($data);
        return new DBJsonResponse($service->download(), 'Template gerado com sucesso.');
    }

    public function verificaExistenciaExeContaCorrente(Request $request)
    {
        $service = new CadastroContaMSCService();
        if ($service->verificaExistenciaExeContaCorrente($request->all())) {
            throw new Exception("Esta conta já possui saldo inicial por recurso no exercicio.", 403);
        }
        return new DBJsonResponse($service->verificaExistenciaExeContaCorrente($request->all()));
    }

    public function configurarSaldoInicialPorRecurso(Request $request)
    {
        $service = new CadastroContaMSCService();
        $service->configurarSaldoInicialPorRecurso($request->all());
        return new DBJsonResponse();
    }
}
