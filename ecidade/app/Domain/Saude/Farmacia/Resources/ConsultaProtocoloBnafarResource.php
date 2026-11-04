<?php

namespace App\Domain\Saude\Farmacia\Resources;

use ECidade\Enum\Saude\Farmacia\SituacaoProcessamentoBnafarEnum;
use ECidade\Enum\Saude\Farmacia\TipoOperacaoBnafarEnum;
use ECidade\Enum\Saude\Farmacia\TipoServicoBnafarEnum;

class ConsultaProtocoloBnafarResource
{
    /**
     * @param object $response
     * @return object
     * @throws \Exception
     */
    public static function toBootstrapTable($response)
    {
        $dados = (object)[
            'total' => $response->totalElements,
            'rows' => []
        ];

        if (!property_exists($response, 'content')) {
            return $dados;
        }

        foreach ($response->content as $data) {
            $historico = $data->historico->map(function ($data) {
                return (object)[
                    'data' => $data->fa76_created_at->format('d/m/Y H:i'),
                    'erro' => $data->fa76_falhou ? 'SIM' : 'NÃO'
                ];
            });

            $dados->rows[] = (object)[
                'protocolo' => $data->protocolo,
                'codigoIbge' => $data->codigoIbge,
                'usuarioEnvio' => $data->usuarioEnvio,
                'procedimento' => $data->tipoServico,
                'dataProtocolo' => date_format(new \DateTime($data->dataProtocolo), 'd/m/Y H:i'),
                'situacao' => (new SituacaoProcessamentoBnafarEnum($data->situacao))->name(),
                'tipoServico' => (new TipoServicoBnafarEnum($data->tipoServico))->name(),
                'tipoOperacao' => (new TipoOperacaoBnafarEnum($data->tipoOperacao))->name(),
                'permiteReprocessamento' => $data->permiteReprocessamento,
                'situacaoSistema' => $data->situacaoSistema,
                'historico' => $historico
            ];
        }

        return $dados;
    }
}
