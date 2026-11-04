<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Services\UnificacaoCgsService;
use App\Domain\Saude\Ambulatorial\Requests\UnificacaoCgsRequest;
use App\Domain\Saude\Ambulatorial\Resources\UnificacaoCgsResource;
use Exception;

class UnificacaoCgsController extends Controller
{
    protected $service;
    /**
     * ITEM DE MENU RESPONSAVEL PELA VERIFICACAO DA PERMISSAO DO USUARIO
     */
    const PERMISSAO_PROCESSAR = 229303;

    public function __construct()
    {
        $this->service = new UnificacaoCgsService();
    }

    public function listarUnificacoes(Request $request)
    {
        $msgResponse = 'Unificacoes do Cgs listadas com sucesso!';
        $response = $this->service->listarUnificacoes($request->all());
        $responseFormatado = UnificacaoCgsResource::toArray($response);
        return new DBJsonResponse($responseFormatado, $msgResponse, 200);
    }

    public function listarCgs(Request $request)
    {
        $cgsList = $this->service->listarCgs($request->all());
        return new DBJsonResponse($cgsList, "CGS listado com sucesso");
    }

    public function detalhar(Request $request)
    {
        UnificacaoCgsRequest::validaRequestDetalharUnificacao($request->all());
        $msgResponse =  'Unificacoes do Cgs listadas com sucesso!';
        return new DBJsonResponse($this->service->detalhar($request->all()), $msgResponse, 200);
    }
     
    public function salvar(Request $request)
    {
        UnificacaoCgsRequest::validaRequestSalvar($request->all());
        $this->service->salvar($request->all());
        $msgResponse = "A unificação do CGS foi salva, mas ainda não foi finalizada.";
        $msgResponse .= "  Para concluir o processo,";
        $msgResponse .= " verifique se a unificação pendente está em conformidade.";
        $msgResponse .= " Em seguida, processe a unificação para concluí-la.";
        return new DBJsonResponse([], $msgResponse, 200);
    }

    public function excluir(Request $request)
    {
        UnificacaoCgsRequest::validaRequestExcluir($request->all());
        $this->service->excluir($request->all());
        return new DBJsonResponse([], 'Unificacao do CGS excluido com sucesso!', 200);
    }

    public function processar(Request $request)
    {
        $temPermissao = $this->service->verificaPermissaoUsuario(self::PERMISSAO_PROCESSAR);
        if (!$temPermissao) {
            $msgErro = "Você não tem permissão para processar a unificação dos CGSs.";
            $msgErro .= " Por favor, contate o suporte para fechar o fluxo da unificação!";
            throw new Exception($msgErro);
        }

        UnificacaoCgsRequest::validaRequestProcessarUnificacao($request->all());
        $this->service->processar($request->all());
        return new DBJsonResponse([], 'Unificacao do CGS processado com sucesso!', 200);
    }
}
