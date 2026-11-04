<?php

namespace App\Domain\Patrimonial\Protocolo\Controller\Processo;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoDocumento;
use App\Http\Controllers\Controller;
use ECidade\Lib\Session\DefaultSession;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function all(Request $request)
    {
        $tiposDocumento = TipoDocumento::query();
        if ($request->get("descricao")) {
            $tiposDocumento = $tiposDocumento->where(
                'p91_descricao',
                'ilike',
                "%{$request->get("descricao")}%"
            );
        }

        if (!empty($request->get("exceto"))) {
            $exceto = $request->get("exceto");
            if (!is_array($request->get("exceto"))) {
                $exceto = explode(",", $request->get("exceto"));
            }
            $tiposDocumento = $tiposDocumento->whereNotIn(
                'p91_sequencial',
                $exceto
            );
        }
        return new DBJsonResponse($tiposDocumento->get());
    }

    public function tiposProcessos(TipoDocumento $tipo_documento, Request $request)
    {
        if (empty($tipo_documento->p91_sequencial)) {
            throw new \Exception("Documento não encontrado!");
        }

        $tiposProcesso = $tipo_documento->tiposDeprocesso()
            ->instituicoes([DefaultSession::getInstance()->get(DefaultSession::DB_INSTIT)]);

        if (!empty($request->get("descricao"))) {
            $tiposProcesso = $tiposProcesso->where("p51_descr", "ilike", "%{$request->get('descricao')}%");
        }

        return new DBJsonResponse($tiposProcesso->orderBy('p51_codigo')->limit(50)->get());
    }

    public function tiposProcessosByDocumento(TipoDocumento $tipo_documento, Request $request)
    {
        if (empty($tipo_documento->p91_sequencial)) {
            throw new \Exception("Documento não encontrado!");
        }

        $tiposProcesso = $tipo_documento->tiposDeprocesso()
            ->instituicoes([DefaultSession::getInstance()->get(DefaultSession::DB_INSTIT)]);

        $perpage = 15;
        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        if (!empty($request->get("codigo"))) {
            $tiposProcesso = $tiposProcesso->likeId($request->get("codigo"));
        }

        if (!empty($request->get("assunto"))) {
            $tiposProcesso = $tiposProcesso->likeDescricao($request->get("assunto"));
        }

        return new DBJsonResponse(
            $tiposProcesso
                ->where('tipoproc.p51_prottipodocumentoprocesso', '=', $tipo_documento->p91_sequencial)
                ->orderBy('p51_descr')
                ->paginate($perpage)
        );
    }
}
