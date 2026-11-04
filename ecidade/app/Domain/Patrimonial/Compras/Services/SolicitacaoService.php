<?php

namespace App\Domain\Patrimonial\Compras\Services;

use App\Domain\Patrimonial\Compras\Models\Solicitacao;
use Illuminate\Http\Request;

class SolicitacaoService
{
    public function buscar(Request $request)
    {
        $solicitacao = new Solicitacao();
        $condicoes = $this->montaCondicoesSolicitacao($request);

        return $solicitacao
            ->where($condicoes)
            ->with([
                'itens',
                'itens.solicitacaoProcessoCompraMaterial.processoCompraMaterial',
                'itens.processoCompraItem.orcamentoItemProcesso.orcamentoItem.julgamentoVencedor',
                'itens.itemUnidade.materialUnidade',
                'processoAdministrativo',
                'compraPncp',
            ])
            ->get();
    }

    private function montaCondicoesSolicitacao(Request $request)
    {
        $condicoes = [];

        if (!empty($request->pc10_numero)) {
            $condicoes['pc10_numero'] = $request->pc10_numero;
        }

        return $condicoes;
    }
}
