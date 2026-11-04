<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoSolicitacaoAssinatura;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Services\NoticarAssinanteDocumentoService;
use Ramsey\Uuid\Uuid;
use App\Domain\Core\Base\Repository\BaseRepository;
use App\Notifications\UserNotification;

class SolicitacaoAssinaturaService extends BaseRepository
{

    public static function listarDocumentos($processo, $despacho)
    {
        $documentos = ProcessoDocumento::where(
            "p01_protprocesso",
            "=",
            $processo
        )
            ->where("p01_procandamint", "=", $despacho)
            ->with("solicitacaoAssinatura.cgmAssinante")->get();
        if (!$documentos) {
            throw new \BusinessException("Nenhum documento encontrado!");
        }
        return $documentos;
    }

    /**
     * @throws \BusinessException
     */
    public static function solicitarMuitasAssinaturas(array $documentos, $cgmSolicitante)
    {
        foreach ($documentos as $documento) {
            $documento = (object)$documento;
            $documentoSolicitacaoAssintura = new DocumentoSolicitacaoAssinatura();
            $documentoSolicitacaoAssintura->setDocumentoId($documento->documento_id);
            $documentoSolicitacaoAssintura->setCgmSolicitante($cgmSolicitante);
            $documentoSolicitacaoAssintura->setCgmAssinante($documento->cgm_assinante);
            $documentoSolicitacaoAssintura->setDocumentoId($documento->documento_id);

            self::soliciarAssinatura($documentoSolicitacaoAssintura);
        }
    }

    public static function soliciarAssinatura(DocumentoSolicitacaoAssinatura $documento)
    {
        $cgmSolicitante = Cgm::find($documento->getCgmSolicitante());

        if (!$cgmSolicitante) {
            throw new \BusinessException("Cgm {$documento->getCgmSolicitante()} solicitante não encontrado!");
        }

        $cgmAssinante = Cgm::find($documento->getCgmAssinante());

        if (!$cgmAssinante) {
            throw new \BusinessException("Cgm {$documento->getCgmAssinante()} não encontrado!");
        }

        if (empty($cgmAssinante->z01_nome)) {
            throw new \BusinessException("Cgm {$documento->getCgmAssinante()} com nome em branco!");
        }

        if (empty($cgmAssinante->z01_cgccpf)) {
            throw new \BusinessException("Cgm {$documento->getCgmAssinante()} com CPF/CNPJ em branco!");
        }

        if (!\DBString::isCpfCnpj($cgmAssinante->z01_cgccpf)) {
            throw new \BusinessException("Cgm {$documento->getCgmAssinante()} com CPF/CNPJ inválido!");
        }

        if (empty($cgmAssinante->z01_email)) {
            throw new \BusinessException("Cgm {$documento->getCgmAssinante()} com E-MAIL em branco!");
        }

        $existe = DocumentoSolicitacaoAssinatura::where(
            "documento_id",
            "=",
            $documento->documento_id
        )->where(
            "cgm_assinante",
            "=",
            $documento->cgm_assinante
        )->exists();

        if (!$existe) {
            if (!$documento->save()) {
                new \BusinessException("Cgm {$documento->cgm_assinante} com erro ao salvar solicitação!");
                return;
            }
            $notificarService = new NoticarAssinanteDocumentoService();
            $notificarService->dispatchJobAssinatura(
                $cgmAssinante->z01_cgccpf,
                mb_convert_encoding($cgmAssinante->z01_nome, 'UTF-8', 'UTF-8'),
                $cgmAssinante->z01_email,
                $documento->documento()->first(),
                $cgmSolicitante,
                $documento
            );
        }
    }

    public static function solitacoesAssinaturaCpfCnpj($cpfCnpj, $request, $filters = [])
    {
        $cgms = Cgm::where("z01_cgccpf", "=", $cpfCnpj)->get(["z01_numcgm", "z01_nome"]);

        if (!$cgms) {
            throw new \BusinessException("Não foi encontrado documentos vinculados ao CPF/CNPJ");
        }

        $documentoAssinatura = DocumentoSolicitacaoAssinatura::whereIn(
            "cgm_assinante",
            $cgms->pluck("z01_numcgm")
        )->with(["documento" => function ($query) {
            $query->select(
                "p01_descricao as descricao",
                "p01_documento_hash as documento_hash",
                "p01_sequencial",
                "p01_protprocesso",
                "p01_procandamint as codigo_despacho",
                "p01_documento as documento_storage"
            );
        }, 'documento.processo' => function ($query) {
            $query->select(
                "p58_codproc",
                "p58_numero as numero",
                "p58_ano as ano",
                "p58_codproc_crypt as codigo"
            );
        }, 'cgmSolicitante' => function ($query) {
            $query->select("z01_numcgm", "z01_nome", "z01_cgccpf");
        }]);

        if (!empty($filters['id'])) {
            $documentoAssinatura->where('id', $filters['id']);
        }

        if (!empty($filters['solicitante'])) {
            $documentoAssinatura->whereHas('cgmSolicitante', function ($query) use ($filters) {
                $query->where('z01_nome', 'ILIKE', '%' . $filters['solicitante'] . '%');
            });
        }

        if (!empty($filters['cgm'])) {
            $documentoAssinatura->whereHas('cgmSolicitante', function ($query) use ($filters) {
                $query->where('z01_numcgm', $filters['cgm']);
            });
        }

        if (!empty($filters['data_inicio'])) {
            $documentoAssinatura->whereDate('created_at', '>=', $filters['data_inicio']);
        }

        if (!empty($filters['data_fim'])) {
            $documentoAssinatura->whereDate('created_at', '<=', $filters['data_fim']);
        }

        if (!empty($filters['descricao'])) {
            $documentoAssinatura->whereHas('documento', function ($query) use ($filters) {
                $query->where('p01_descricao', 'ILIKE', '%' . $filters['descricao'] . '%');
            });
        }

        if (!empty($filters['processo'])) {
            $documentoAssinatura->whereHas('documento.processo', function ($query) use ($filters) {
                $query->where('p58_numero', 'ILIKE', '%' . $filters['processo'] . '%');
            });
        }

        if ($request->get("assinado") == "sim") {
            $documentoAssinatura->whereNotNull("data_assinatura");
        }

        if ($request->get("assinado") == "nao") {
            $documentoAssinatura->whereNull("data_assinatura");
        }

        $sortField = $request->get('sortField', 'id');
        $sortOrder = $request->get('sortOrder', '1');

        if (!in_array($sortField, ['id', 'created_at', 'data_assinatura'])) {
            $sortField = 'id';
        }

        $documentoAssinatura->orderBy($sortField, $sortOrder === '1' ? 'asc' : 'desc');

        $data = $documentoAssinatura->paginate((int)$request->get('perPage', 10));

        $data->getCollection()->transform(function ($doc) {
            try {
                $response = StorageHelper::currentVersion($doc->documento->documento_storage);

                if (!empty($response->data->codigo)) {
                    $processoDocumento = ProcessoDocumento::where(
                        "p01_procandamint",
                        "=",
                        $doc->documento->codigo_despacho
                    )->first();
                    $processoDocumento->p01_documento = $response->data->codigo;
                    $doc->documento->documento_storage = $response->data->codigo;
                }

                if (empty($doc->qrcode_hash)) {
                    $doc->qrcode_hash = Uuid::uuid4();
                }

                return $doc;
            } catch (\Exception $e) {
                return $doc;
            }
        });

        return $data;
    }

    /**
     * @param DocumentoSolicitacaoAssinatura $documentoSolicitacaoAssinatura
     * @return void
     */
    public static function notificaAssinaturaDeDocumento(DocumentoSolicitacaoAssinatura $documentoSolicitacaoAssinatura)
    {

        $usuarioSolicitante = UsuarioCgm::findByCgm($documentoSolicitacaoAssinatura->getCgmSolicitante());
        $usuarioNotificar =  Usuario::find($usuarioSolicitante->id_usuario);
        $assinate =  Cgm::find($documentoSolicitacaoAssinatura->getCgmAssinante());

        if ($usuarioSolicitante) {
            $processo = $documentoSolicitacaoAssinatura->documento->processo;
            $mensagem = "Documento " . $documentoSolicitacaoAssinatura->documento->getNomeDocumento()
                . "  Assinado por: " . $assinate->z01_nome." "
                . $processo->getNumero() . "/" . $processo->getAno();

            $usuarioNotificar->notify(
                new UserNotification(
                    "Solicitação de assinatura",
                    $mensagem
                )
            );
        }
    }
}
