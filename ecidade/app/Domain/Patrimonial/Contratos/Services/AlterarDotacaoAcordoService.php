<?php

namespace App\Domain\Patrimonial\Contratos\Services;

use App\Domain\Patrimonial\Contratos\Models\AcordoItem;
use App\Domain\Patrimonial\Contratos\Models\AcordoItemDotacao;
use App\Domain\Patrimonial\Contratos\Models\AcordoOrigem;
use cl_orcreserva;
use cl_orcreservaacordoitemdotacao;
use db_utils;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Domain\Patrimonial\Contratos\Models\AcordoPosicao;
use Illuminate\Http\Request;

class AlterarDotacaoAcordoService
{

    public function addDotacao($dotacao)
    {
        $acordoItemDotacao = new AcordoItemDotacao();
        $acordoItemDotacao->ac22_coddot = $dotacao['ac22_coddot'];
        $acordoItemDotacao->ac22_anousu = $dotacao['ac22_anousu'];
        $acordoItemDotacao->ac22_acordoitem = $dotacao['ac22_acordoitem'];
        $acordoItemDotacao->ac22_valor = $dotacao['ac22_valor'];
        $acordoItemDotacao->ac22_quantidade = $dotacao['ac22_quantidade'];
        $acordoItemDotacao->save();
    }

    public function removerDotacao($sequencial)
    {
        $reservaAcordoItem = new cl_orcreservaacordoitemdotacao();

        $reservas = DB::table('orcreservaacordoitemdotacao')
            ->where('o84_acordoitemdotacao', '=', $sequencial)
            ->get();



        if (count($reservas) > 0) {
            $orcReserva = $reservas[0]->o84_orcreserva;

            DB::table('orcreservaacordoitemdotacao')
                ->where('o84_acordoitemdotacao', '=', $sequencial)
                ->delete();

            DB::table('orcreserva')
                ->where('o80_codres', '=', $orcReserva)
                ->delete();
        }


        AcordoItemDotacao::find($sequencial)->delete();
    }

    public function getDotacoes($acordoItem)
    {
        $dotacoes = AcordoItemDotacao::where('ac22_acordoitem', '=', $acordoItem)->get();
        return $dotacoes;
    }

    public function alterarDotacoes(Request $request)
    {
        foreach ($request->all() as $item) {
            if (is_array($item)) {
                $dotacoesAntigas = $this->getDotacoes($item['ac20_sequencial']);
                $this->verificaDotacoes($dotacoesAntigas, $item['dotacoes']);
            }
        }
        return true;
    }

    private function verificaDotacoes($dotacoesAntigas, $dotacoesNovas)
    {
        for ($i = 0; $i < count($dotacoesAntigas); $i++) {
            $this->atualizaSituacaoDotacao($dotacoesAntigas[$i], $dotacoesNovas);
        }

        for ($i = 0; $i < count($dotacoesNovas); $i++) {
            $this->adicionaNovaDotacao($dotacoesAntigas, $dotacoesNovas[$i]);
        }
    }

    private function atualizaSituacaoDotacao($dotacaoAntiga, $dotacoesNovas)
    {
        $existeDotacao = false;
        for ($i = 0; $i < count($dotacoesNovas); $i++) {
            if ($dotacaoAntiga['ac22_coddot'] == $dotacoesNovas[$i]['ac22_coddot']
                && $dotacaoAntiga['ac22_anousu'] == $dotacoesNovas[$i]['ac22_anousu']) {
                $existeDotacao = true;
                if ($dotacaoAntiga['ac22_valor'] != $dotacoesNovas[$i]['ac22_valor']) {
                    $this->updateValorDotacao($dotacaoAntiga, $dotacoesNovas[$i]['ac22_valor']);
                }
            }
        }
        if (!$existeDotacao) {
            $this->removerDotacao($dotacaoAntiga['ac22_sequencial']);
        }
    }

    private function updateValorDotacao($dotacao, $novoValor)
    {
        $acordoItemDotacao = AcordoItemDotacao::find($dotacao['ac22_sequencial']);
        if ($acordoItemDotacao) {
            DB::table('orcreservaacordoitemdotacao')
                ->where('o84_acordoitemdotacao', '=', $dotacao['ac22_sequencial'])
                ->delete();

            $acordoItemDotacao->ac22_valor = $novoValor;
            $acordoItemDotacao->save();
        }
    }

    private function adicionaNovaDotacao($dotacoesAntigas, $novaDotacao)
    {
        $existeDotacao = false;
        for ($i = 0; $i < count($dotacoesAntigas); $i++) {
            if ($dotacoesAntigas[$i]['ac22_coddot'] == $novaDotacao['ac22_coddot']
                && $dotacoesAntigas[$i]['ac22_anousu'] == $novaDotacao['ac22_anousu']) {
                $existeDotacao = true;
                break;
            }
        }
        if (!$existeDotacao) {
            $this->addDotacao($novaDotacao);
        }
    }


    public function buscarPosicoes($acordo)
    {
        $instit = db_getsession('DB_instit');
        $posicoesIgnorar = [
            AcordoPosicao::TIPO_VIGENCIA,
            AcordoPosicao::TIPO_ALTERACAO_DOTACAO,
            AcordoPosicao::TIPO_SUPRESSAO,
            AcordoPosicao::TIPO_ALTERACAO_CESSAO_CONTRATADO,
        ];
        $posicoes = AcordoPosicao::with('periodo')
            ->join('acordoposicaotipo', 'ac27_sequencial', '=', 'ac26_acordoposicaotipo')
            ->join('acordo', 'ac16_sequencial', '=', 'ac26_acordo')
            ->where('ac26_acordo', $acordo)
            ->where('ac16_instit', $instit)
            ->where('ac16_acordosituacao', '=', '4')
            ->where(function ($query) {
                $dept = db_getsession('DB_coddepto');
                $query->where('ac16_coddepto', '=', $dept)
                    ->orWhere('ac16_deptoresponsavel', '=', $dept);
            })
            ->orderBy('ac26_sequencial')
            ->get();
        $posicoesRetorno = [];
        foreach ($posicoes as $posicao) {
            if (!in_array($posicao->ac26_acordoposicaotipo, $posicoesIgnorar)) {
                $posicoesRetorno[] = $posicao;
            }
        }
        return $posicoesRetorno;
    }

    public function getDescricaoOrigens()
    {
        $origens = AcordoOrigem::orderBy('ac28_descricao')
            ->pluck('ac28_descricao', 'ac28_sequencial')
            ->all();
        return $origens;
    }

    public function getItensPosicao($acordoPosicao)
    {
        $itens = AcordoItem::with('dotacoes')->
        join('pcmater', 'pc01_codmater', '=', 'ac20_pcmater')
            ->where('ac20_acordoposicao', $acordoPosicao)
            ->orderBy('ac20_ordem')
            ->get();

        foreach ($itens as $item) {
            $item->valorAutorizar = $this->getValorAutorizar($item->ac20_sequencial);
        }

        return $itens;
    }

    private function getValorAutorizar($ac20_sequencial)
    {
        $item = new \AcordoItem($ac20_sequencial);
        $saldo = $item->getSaldos();
        return $saldo->valorautorizar;
    }

    public function alterarElementos(Request $request)
    {
        foreach ($request as $item) {
            if (is_array($item)) {
                $acordoItem = AcordoItem::find($item['ac20_sequencial']);
                if ($acordoItem) {
                    if ($acordoItem->ac20_elemento != $item['ac20_elemento']) {
                        $acordoItem->ac20_elemento = $item['ac20_elemento'];
                        $acordoItem->save();
                    }
                }
            }
        }
        return true;
    }

    public function realizarAlteracoes($request)
    {

        DB::beginTransaction();
        try {
            $this->alterarElementos($request);
            $this->alterarDotacoes($request);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao realizar as alterações");
        }
        DB::commit();
        return true;
    }
}
