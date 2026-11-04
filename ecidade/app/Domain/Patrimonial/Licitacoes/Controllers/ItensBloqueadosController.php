<?php

namespace App\Domain\Patrimonial\Licitacoes\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Http\Request;
use App\Domain\Patrimonial\Licitacoes\Services\Relatorios\ItensBloqueadosService;
use App\Domain\Patrimonial\Licitacoes\Relatorios\ItensBloqueados;
use App\Http\Controllers\Controller;

class ItensBloqueadosController extends Controller
{
    public function buscarRegistrosDePreco()
    {
        $service = new ItensBloqueadosService();
        $list = $service->buscarRegistrosDePreco();
        return new DBJsonResponse($list, '');
    }

    public function emitir(Request $request)
    {
        $service = new ItensBloqueadosService();

        $dados = $service->buscarDadosLicitacao($request->licitacao);

        $relatorio = new ItensBloqueados();
        if ($dados) {
            foreach ($dados as $dadosLicitacao) {
                $relatorio->setLicitacao($dadosLicitacao->licitacao);
                $relatorio->setModalidade($dadosLicitacao->modalidade);
                $relatorio->setRegistroPreco($dadosLicitacao->solicitacao);
                $relatorio->setMovimentacao($dadosLicitacao->movimentacao);
            }
        } else {
            $dadosHeaders = $service->buscarDadosHeadersLicitacao($request->licitacao);
            foreach ($dadosHeaders as $dadosLicitacaoHeader) {
                $relatorio->setLicitacao($dadosLicitacaoHeader->licitacao);
                $relatorio->setModalidade($dadosLicitacaoHeader->modalidade);
                $relatorio->setRegistroPreco($dadosLicitacaoHeader->solicitacao);
            }
        }
        $solicita = $service->buscarSolicitacao($request->licitacao);
        $list = $service->buscarItensBloqueados($solicita);
        if (!empty($list)) {
            $relatorio->setDados($list);
        }
        return new DBJsonResponse($relatorio->emitir(), '');
    }
}
