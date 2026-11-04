<?php

namespace App\Domain\Patrimonial\PNCP\Builders;

use Illuminate\Http\Request;

class RetificacaoAtaBuilder
{
    private $dados;

    /**
     * @param Request $dados
     * @return $this
     */
    public function setDados(Request $dados)
    {
        $this->dados = $dados;
        return $this;
    }

    public function build()
    {
        return $this->buildRetificacaoAta($this->dados);
    }

    private function buildRetificacaoAta(Request $request)
    {
        return (object)[
            'numeroAtaRegistroPreco'=> stripslashes(utf8_encode($request->get('numeroAtaRegistroPreco'))),
            'anoAta' => $request->get('anoAta'),
            'dataAssinatura' => $request->get('dataAssinatura'),
            'dataVigenciaInicio' => $request->get('dataVigenciaInicio'),
            'dataVigenciaFim' => $request->get('dataVigenciaFim'),
            'cancelado' => $request->get('cancelado') === 'sim',
            'dataCancelamento' => date("{$request->get('dataCancelamento')}\TH:i:s"),
            'justificativa' => stripslashes(utf8_encode($request->get('justificativa')))
        ];
    }
}
