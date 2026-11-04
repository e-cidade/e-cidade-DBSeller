<?php

namespace App\Domain\Patrimonial\Protocolo\Controller\Processo;

use App\Domain\Financeiro\Empenho\Services\DocumentoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;
use App\Domain\Patrimonial\Protocolo\Requests\Processo\ProcessoDocumento\Download as DownloadRequest;
use App\Domain\Patrimonial\Protocolo\Requests\Processo\ProcessoDocumento\DocumentosPorProcesso as DocumentosRequest;

class ProcessoDocumentoController extends Controller
{
    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct(ProcessoDocumentoRepository $processoRepository)
    {
        $this->repository = $processoRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return DBJsonResponse
     */
    public function index()
    {
        return new DBJsonResponse($this->repository->findAll());
    }

    /**
     * Realiza download do documento do storage
     *
     * @return DBJsonResponse
     */
    public function download(DownloadRequest $request)
    {
        return new DBJsonResponse(StorageHelper::downloadArquivo($request->id));
    }

    /**
     * Busca os documentos pelo código do processo ordenados
     *
     * @return DBJsonResponse
     */
    public function documentosPorProcesso(DocumentosRequest $request)
    {
        return new DBJsonResponse($this->repository->findByProcesso($request->codigoProcesso));
    }

    /**
     * Busca os documentos pelo código do andamento ordenados
     * utlizando procandamint
     * @return DBJsonResponse
     */
    public function documentosPorProcAndamInt(Request $request)
    {
        $this->validate($request, [
            'codigoProcesso' => 'required',
            'procandamint' => 'required|integer'
        ]);

        return new DBJsonResponse($this->repository->findByProcessoAndProcandamint(
            $request->codigoProcesso,
            $request->procandamint
        ));
    }

    public function documentosPorNota(Request $request, $codigo_nota)
    {
        $documentoService = new DocumentoService();
        return new DBJsonResponse($documentoService->getDocumentos($codigo_nota));
    }
}
