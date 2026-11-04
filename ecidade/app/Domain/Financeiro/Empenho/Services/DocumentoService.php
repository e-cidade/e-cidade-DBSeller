<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;
use App\Domain\Patrimonial\Protocolo\Services\EmpenhoDocumentoService;
use cl_protprocessodocumento;
use EmpenhoFinanceiro;
use OrdemDeCompra;

class DocumentoService
{
    public function getDocumentos($codigoNota)
    {
        $documentos = array();
        $sql = "select c70_codlan, p01_documento, p01_upload_name
                        from conlancamemp
                        inner join conlancam          on c70_codlan = c75_codlan
                        inner join protprocessodocumento on p01_c70codlan = c75_codlan
                        inner join empempenho         on c75_numemp = e60_numemp
                        inner join conlancamordem     on conlancamordem.c03_codlan = conlancam.c70_codlan
                        inner join conlancamdoc       on c71_codlan   = c70_codlan
                        inner join conhistdoc         on c53_coddoc     = c71_coddoc
                        inner join conlancamcompl      on c72_codlan  =c70_codlan
                        inner join conlancamnota       on c66_codlan  =c70_codlan
                        left join conlancamord        on c80_codlan  =c70_codlan
                        inner join empnota             on c66_codnota = e69_codnota
                        where e69_codnota = '{$codigoNota}' ";
        $result = db_query($sql);
        while ($arquivos = pg_fetch_assoc($result)) {
            $arquivo = (object)[
                "codigo_arquivo" => $arquivos['p01_documento'],
                "nome_arquivo" => $arquivos['p01_upload_name'],
                "codigo_lancamento" => $arquivos['c70_codlan']
            ];
            $documentos[] = $arquivo;
        }
        return $documentos;
    }

    /**
     * @throws \BusinessException
     * @throws \Exception
     */
    public function deleteDocumentos($documentosDelete, $anulacaoEntrada = false)
    {
        $processoRepository = new ProcessoDocumentoRepository();
        if (!$anulacaoEntrada) {
            $jsonString = stripslashes($documentosDelete);
            $arrayDocDelete = json_decode($jsonString, true);
            foreach ($arrayDocDelete as $doc) {
                //se for null ele nao delete pq o doc é a ordem de pagamento
                if ($doc["nome_arquivo"] != null) {
                    StorageHelper::deleteArquivo($doc["codigo_arquivo"]);
                    $id = (integer) $doc["codigo_arquivo"];
                    $processoRepository->deleteByP01Documento($id);
                }
            }
        } else {
            foreach ($documentosDelete as $doc) {
                StorageHelper::deleteArquivo($doc->codigo_arquivo);
                $id = (integer) $doc->codigo_arquivo;
                $processoRepository->deleteByP01Documento($id);
            }
        }
    }

    /**
     * @throws \BusinessException
     * @throws \Exception
     */
    public function saveDocumentos(
        $documentos,
        $codigoOrdemCompra = null,
        $empenhoCodigo = null,
        $tratarDocumentos = true,
        $codigoLancamento = null
    ) {
        if ($codigoOrdemCompra != null) {
            $oOrdemDeCompra = new OrdemDeCompra($codigoOrdemCompra);
            $oEmpenhoFinanceiro = $oOrdemDeCompra->getEmpenhoFinanceiro();
        } else {
            $oEmpenhoFinanceiro = new EmpenhoFinanceiro($empenhoCodigo);
        }
        $empenhoDocumentoService = new EmpenhoDocumentoService($oEmpenhoFinanceiro);

        if (!is_null($empenhoDocumentoService->getDocumentoAndamento())) {
            if ($tratarDocumentos) {
                $jsonString = stripslashes($documentos);
                $arrayDoc = json_decode($jsonString, true);
                foreach ($arrayDoc as $doc) {
                    if (isset($doc["caminho"])) {
                        $filename = basename($doc["caminho"]);
                        $parts = explode("-", $filename);
                        $filenameOnly = end($parts);

                        $idArquivo = StorageHelper::uploadArquivo($doc["caminho"], [], true);
                        $arquivo = (object)[
                            "id" => $idArquivo,
                            "name" => "Nota Fiscal",
                            "lancContabilID" => $codigoLancamento != null ? $codigoLancamento :
                                $this->getCodigoLancamento($empenhoDocumentoService),
                            "nomeUpload" => $filenameOnly
                        ];

                        $empenhoDocumentoService->vincularDocumento($arquivo, "Nota Fiscal");
                    }
                }
            } else {
                foreach ($documentos as $doc) {
                    if (isset($doc->caminho)) {
                        $filename = basename($doc->caminho);
                        $parts = explode("-", $filename);
                        $filenameOnly = end($parts);

                        $idArquivo = StorageHelper::uploadArquivo($doc->caminho, [], true);
                        $arquivo = (object)[
                            "id" => $idArquivo,
                            "name" => "Nota Fiscal",
                            "lancContabilID" => $codigoLancamento != null ? $codigoLancamento :
                                $this->getCodigoLancamento($empenhoDocumentoService),
                            "nomeUpload" => $filenameOnly
                        ];

                        $empenhoDocumentoService->vincularDocumento($arquivo, "Nota Fiscal");
                    }
                }
            }
        }
    }

    public function getCodigoLancamento($empenhoDocumentoService)
    {
        $processo = $empenhoDocumentoService->getDocumentoAndamento()->p116_protprocesso;
        $clprotprocessodocumento = new cl_protprocessodocumento();

        $sql = "SELECT p01_c70codlan FROM
                            protprocessodocumento WHERE p01_protprocesso = $processo and p01_c70codlan is not null";
        $rs = $clprotprocessodocumento->sql_record($sql);
        $rs = pg_fetch_assoc($rs);

        return $rs['p01_c70codlan'];
    }

    /**
     * @throws \BusinessException
     * @throws \Exception
     */
    public function isEletronico($codigoOrdemCompra = null, $empenhoCodigo = null)
    {
        if ($codigoOrdemCompra != null) {
            $oOrdemDeCompra = new OrdemDeCompra($codigoOrdemCompra);
            $oEmpenhoFinanceiro = $oOrdemDeCompra->getEmpenhoFinanceiro();
        } else {
            $oEmpenhoFinanceiro = new EmpenhoFinanceiro($empenhoCodigo);
        }
        $empenhoDocumentoService = new EmpenhoDocumentoService($oEmpenhoFinanceiro);
        if (!is_null($empenhoDocumentoService->getDocumentoAndamento())) {
            return true;
        }
        return false;
    }
}
