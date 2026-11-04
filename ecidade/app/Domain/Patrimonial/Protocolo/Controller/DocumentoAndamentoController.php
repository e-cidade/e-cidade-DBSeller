<?php

namespace App\Domain\Patrimonial\Protocolo\Controller;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Ouvidoria\Services\SolicitacaoAssinaturaService;
use App\Domain\Patrimonial\Protocolo\Factories\DocumentoAndamentoFactory;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoSolicitacaoAssinatura;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumentoAssinatura;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoUsuario;
use App\Domain\Patrimonial\Protocolo\Model\ProcessoAtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Repository\DocumentosAndamentoRepository;
use App\Domain\RecursosHumanos\Pessoal\Services\ContraChequeService;
use Carbon\Carbon;
use ECidade\Lib\Session\DefaultSession;
use Exception;
use cl_portaria;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DocumentoAndamentoController extends Controller
{
    /**
     * @return DBJsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $usuario = Usuario::find(db_getsession('DB_id_usuario'));
        $documentosAndamentoRepository = new DocumentosAndamentoRepository();
        $documentosAndamentos = $documentosAndamentoRepository->buscarDocumentosPorUsuario(
            $usuario,
            $request->get("data_inicio") ? $request->get("data_inicio") : null,
            $request->get("data_fim") ? $request->get("data_fim") : null,
            $request->get("limit") ? $request->get("limit") : null,
            $request->get("filtro") ? $request->get("filtro") : null
        );
        $response = array();
        foreach ($documentosAndamentos as $documentoAndamento) {
            try {
                $documentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
                $response[] = $documentoService->montarObjetoTela();
            } catch (\Exception $ex) {
                Log::debug($ex->getMessage() . "=> andamento" . json_encode($documentoAndamento));
                continue;
            }
        }
        return new DBJsonResponse($response);
    }

    /**
     * @return DBJsonResponse
     * @throws Exception
     */
    public function total(Request $request)
    {
        $usuario = Usuario::find(auth()->user()->getAuthIdentifier());
        $documentosAndamentoRepository = new DocumentosAndamentoRepository();
        $documentosAndamentos = $documentosAndamentoRepository->buscarDocumentosPorUsuario(
            $usuario,
            null,
            null,
            100
        );
        return new DBJsonResponse(['total' => count($documentosAndamentos)]);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function conferir(Request $request)
    {
        $documentoAndamento = DocumentoAndamento::find($request->get('codigo_documento'));
        $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
        $documentoAndamentoService->conferir();
        return new DBJsonResponse([], "Documento conferido com sucesso");
    }

    /**
     * @throws Exception
     */
    public function conferirLote(Request $request)
    {
        if (!$request->has('documentos') || empty($request->get('documentos'))) {
            return new DBJsonResponse([], "Nenhum documento informado.");
        }

        $documentos = explode(',', $request->get('documentos'));
        foreach ($documentos as $codigo_documento) {
            $documentoAndamento = DocumentoAndamento::find($codigo_documento);
            $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
            $documentoAndamentoService->conferir();
        }
        return new DBJsonResponse([], "Documento conferido com sucesso");
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function devolver(Request $request)
    {
        if (empty($request->get('atividade_destino'))) {
            throw new Exception('Atividade de Destino não informada para devolução');
        }
        $documentoAndamento = DocumentoAndamento::find($request->get('codigo_documento'));
        $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
        $processoAtividadeExecucao = ProcessoAtividadeExecucao::find($request->get('atividade_destino'));
        $documentoAndamentoService->devolverAtividade($processoAtividadeExecucao);
        return new DBJsonResponse([], "Documento devolvido para atividade selecionada");
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function arquivar(Request $request)
    {
        $documentoAndamento = DocumentoAndamento::find($request->get('codigo_documento'));
        $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
        $documentoAndamentoService->arquivar();
        return new DBJsonResponse([], "Documento arquivado com sucesso");
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarAtividadesExecutadas(Request $request)
    {
        $documentoAndamento = DocumentoAndamento::find($request->get('codigo_documento'));
        $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
        $atividades = $documentoAndamentoService->buscarAtividadesExecutadas();

        foreach ($atividades as $atividade) {
            $atividade->atividade;
        }
        return new DBJsonResponse($atividades, "Lista de Atividades Executadas");
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarPorIdentificador(Request $request)
    {
        $request->get('qrcode');
        if (!$request->has('qrcode')) {
            return new DBJsonResponse([], "Informe o código identificador do arquivo.", 500);
        }
        if (!$request->has('tipoDocumento') || empty($request->get('tipoDocumento'))) {
            return new DBJsonResponse([], 'Tipo de documento não informado.', 500);
        }

        switch ($request->get('tipoDocumento')) {
            case 'documento':
                $documentosAndamentoRepository = new DocumentosAndamentoRepository();
                $documentoAndamento = $documentosAndamentoRepository->scopeQRCode($request->get('qrcode'))->first();

                if (!empty($documentoAndamento)) {
                    return new DBJsonResponse(
                        StorageHelper::getContentsBase64(
                            $documentoAndamento->processoDocumento->p01_documento
                        )
                    );
                }

                $processoDocumento = new ProcessoDocumento();
                $documento = $processoDocumento->hash($request->get('qrcode'))->first();
                if (!empty($documento)) {
                    return new DBJsonResponse(
                        StorageHelper::getContentsBase64($documento->p01_documento)
                    );
                }

                return new DBJsonResponse([], 'Não foi possível encontrar um documento com esse identificador', 500);
            case 'contracheque':
                $contraChequeService = new ContraChequeService();
                $base64 = $contraChequeService->getByCodigoAutenticacao($request->get('qrcode'));
                if (!$base64) {
                    $invalido = 'Contra-cheque não encontrado, o código de autenticidade não é válido! ';
                    $verifique = 'Verifique o código digitado e tente novamente.';
                    return new DBJsonResponse([], $invalido . $verifique, 500);
                }

                return new DBJsonResponse($base64);
            default:
                return new DBJsonResponse(
                    [],
                    "O Tipo de documento inválido, verifique o endereço digitado ou tente novamente mais tarde.",
                    500
                );
        }
    }

    public function orgaosDoUsuario(Request $request)
    {
        $result = DocumentosAndamentoRepository::orgaosDoUsuario(
            auth()->user()->id,
            $request->get("ano")
        );

        return new DBJsonResponse($result);
    }


    public function usuarioAtividadesEmExcetuacao(
        $usuario_id,
        DocumentosAndamentoRepository $andamentoRepository
    ) {

        try {
            $retorno = $andamentoRepository->documentosComAtividadeEmExecutacao($usuario_id);
            return new DBJsonResponse($retorno);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return new DBJsonResponse(
                [],
                "Ocorreu um erro ao consultar documentos que possuiem atividade em execução",
                500
            );
        }
    }

    public function atribuirPermissao(Request $request)
    {
        $this->validate($request, [
            'permissoes.*.processo' => 'required|numeric',
            'permissoes.*.atividadeexecuacao' => 'required|numeric',
            'permissoes.*.usuario' => 'required|numeric',
        ]);

        $permissoes =  $request->get("permissoes");
        foreach ($permissoes as $permissao) {
            ProcessoUsuario::updateOrCreate([
                "p119_protprocesso" => $permissao["processo"],
                "p119_id_usuario" => $permissao["usuario"],
                "p119_atividadeexecucao" => $permissao["atividadeexecuacao"]
            ]);
        }

        return new DBJsonResponse([], "Atribu�do com sucesso!");
    }

    public function removerPermissao(Request $request)
    {

        $this->validate($request, [
            'permissoes.*.processo' => 'required|numeric',
            'permissoes.*.atividadeexecuacao' => 'required|numeric',
            'permissoes.*.usuario' => 'required|numeric',
        ]);

        $permissoes =  $request->get("permissoes");
        foreach ($permissoes as $permissao) {
            ProcessoUsuario::where([
                "p119_protprocesso" => $permissao["processo"],
                "p119_id_usuario" => $permissao["usuario"],
                "p119_atividadeexecucao" => $permissao["atividadeexecuacao"]
            ])->delete();
        }

        return new DBJsonResponse([], "Removido com sucesso!");
    }

    /**
     * @throws Exception
     */
    public function substituirPermissao(Request $request)
    {
        $this->validate($request, [
            'permissoes.*.processo' => 'required|numeric',
            'permissoes.*.atividadeexecuacao' => 'required|numeric',
            'permissoes.*.usuario' => 'required|numeric',
            'permissoes.*.usuario_receber' => 'required|numeric',
        ]);


        $permissoes =  $request->get("permissoes");

        foreach ($permissoes as $permissao) {
            ProcessoUsuario::where([
                "p119_protprocesso" => $permissao["processo"],
                "p119_id_usuario" => $permissao["usuario"],
                "p119_atividadeexecucao" => $permissao["atividadeexecuacao"]
            ])->delete();

            ProcessoUsuario::updateOrCreate([
                "p119_protprocesso" => $permissao["processo"],
                "p119_id_usuario" => $permissao["usuario_receber"],
                "p119_atividadeexecucao" => $permissao["atividadeexecuacao"]
            ]);
        }

        return new DBJsonResponse([], "Substitu�do com sucesso!");
    }

    public function verificaSolicitacaoAssinatura($idDocumento)
    {
        $defaultSession = DefaultSession::getInstance();
        $user = UsuarioCgm::find($defaultSession->get(DefaultSession::DB_ID_USUARIO));

        $documentoSolicitacaoAssinatura = DocumentoSolicitacaoAssinatura::where([
            "documento_id" => $idDocumento,
            "cgm_assinante" => $user->cgmlogin,
        ])->whereNull("data_assinatura")->get();

        if ($documentoSolicitacaoAssinatura->isNotEmpty()) {
            foreach ($documentoSolicitacaoAssinatura as $documento) {
                $documento->setDataAssinatura(Carbon::now());
                if (!$documento->update()) {
                    throw new \Exception("Erro ao registrar data de assinatura");
                }

                SolicitacaoAssinaturaService::notificaAssinaturaDeDocumento($documento);
            }
        }
    }
}
