<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\LancamentoLog;
use App\Domain\Financeiro\Contabilidade\Relatorios\NotaLancamentoPdf;
use App\Domain\Financeiro\Contabilidade\Repositories\LancamentoManualRepository;
use App\Domain\Financeiro\Contabilidade\Resources\Lancamentos\ConsultaLancamentoManualResource;

class NotaLancamentoService
{
    public function processar($idLote)
    {
        $dados = (new LancamentoManualRepository())->getResmo(['idLote' => $idLote]);
        $porLote = ConsultaLancamentoManualResource::organizaPorLote($dados);
        $porLote = array_shift($porLote);
        $porLote->emissor = $this->buscaLog($porLote->lancamentos[0]->lancamento);
        return $porLote;
    }

    public function emitirLote($idLote)
    {
        $dados = $this->processar($idLote);
        $pdf = new NotaLancamentoPdf();
        $pdf->setDados($dados);
        return $pdf->emitir();
    }

    private function buscaLog($lancamento)
    {
        $log = LancamentoLog::query()
            ->with('usuario')
            ->where('codlan', $lancamento)
            ->first();
        return $log->usuario->nome;
    }
}
