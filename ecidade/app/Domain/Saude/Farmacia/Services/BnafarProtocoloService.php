<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Clients\BnafarClient;
use App\Domain\Saude\Farmacia\Exceptions\BnafarException;
use App\Domain\Saude\Farmacia\Models\BnafarBatchProtocolo;
use App\Domain\Saude\Farmacia\Models\BnafarEnvio;
use App\Domain\Saude\Farmacia\Relatorios\ProtocoloBnafarPdf;
use App\Domain\Saude\Farmacia\Requests\RelatorioProtocoloBnafarRequest;
use App\Domain\Saude\Farmacia\Resources\ConsultaProtocoloBnafarResource;
use Illuminate\Database\Eloquent\Builder;

class BnafarProtocoloService
{
    /**
     * @param \UnidadeProntoSocorro $unidade
     * @param \DateTime[] $periodo
     * @param integer $pagina
     * @param integer $tamanho
     * @return object
     * @throws \Exception
     */
    public function consultar(\UnidadeProntoSocorro $unidade, array $periodo, $pagina, $tamanho)
    {
        $periodoInicio = $periodo[0]->format('Y-m-d');
        $periodoFim = $periodo[1]->format('Y-m-d');
        $dataAtual = new \DateTime();
        if ($dataAtual->getTimestamp() < $periodo[1]->getTimestamp()) {
            $periodoFim = $dataAtual->format('Y-m-d');
        }

        try {
            $client = new BnafarClient($unidade);
            $response = $client->pesquisarProtocolos($periodoInicio, $periodoFim, $pagina - 1, $tamanho);
            $response = $this->verificarSituacaoSistema($response);
            return ConsultaProtocoloBnafarResource::toBootstrapTable($response);
        } catch (BnafarException $e) {
            throw new \Exception($e->getDetalhes());
        }
    }

    /**
     * @param RelatorioProtocoloBnafarRequest $request
     * @return string[]
     */
    public function gerarRelatorio(RelatorioProtocoloBnafarRequest $request)
    {
        $dados = json_decode(stripslashes(utf8_encode($request->data)));
        $dados = \DBString::utf8_decode_all($dados);

        $pdf = new ProtocoloBnafarPdf($dados);

        return $pdf->imprimir();
    }

    private function verificarSituacaoSistema($response)
    {
        if (!property_exists($response, 'content')) {
            return $response;
        }

        foreach ($response->content as $envio) {
            $query = BnafarBatchProtocolo::where('fa76_protocolo', $envio->protocolo);
            $envio->permiteReprocessamento = false;
            $envio->historico = $query->get();

            $processado = BnafarEnvio::where('fa70_protocolo', $envio->protocolo)
                    ->where(function (Builder $query) {
                        $query->whereNotNull('fa70_codigobnafar');
                        $query->orWhereHas('inconsistencias');
                    })->count() > 0;

            if ($processado) {
                $envio->situacaoSistema = 'CONCLUÍDO';
                continue ;
            }

            $processando = $query->where('fa76_falhou', false)->whereHas('bnafarBatch', function ($query) {
                $query->where('fa75_concluido', false);
            })->count() > 0;

            $envio->situacaoSistema = !$processando ? 'FALHOU' : 'PROCESSANDO';
            $envio->permiteReprocessamento = !$processando;
        }

        return $response;
    }
}
