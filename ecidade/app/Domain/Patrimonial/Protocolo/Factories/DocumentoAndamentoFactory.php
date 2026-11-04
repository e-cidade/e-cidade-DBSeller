<?php

namespace App\Domain\Patrimonial\Protocolo\Factories;

use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Services\DocumentoAndamentoService;
use App\Domain\Patrimonial\Protocolo\Services\EmpenhoDocumentoService;
use App\Domain\Patrimonial\Protocolo\Services\PortariaDocumentoService;
use EmpenhoFinanceiro;
use Exception;
use Portaria;

class DocumentoAndamentoFactory
{

    /**
     * @param DocumentoAndamento $documentoAndamento
     * @return DocumentoAndamentoService
     * @throws Exception
     */
    public static function getService(DocumentoAndamento $documentoAndamento)
    {
        $tipo = $documentoAndamento->processo->tipoProcesso->p51_prottipodocumentoprocesso;

        switch ((int)$tipo) {
            case 6:
                $empenho = new EmpenhoFinanceiro($documentoAndamento->p116_codigo_origem);
                return new EmpenhoDocumentoService($empenho, $documentoAndamento);
            default:
                throw new Exception('Erro ao buscar Documento Service.');
        }
    }
}
