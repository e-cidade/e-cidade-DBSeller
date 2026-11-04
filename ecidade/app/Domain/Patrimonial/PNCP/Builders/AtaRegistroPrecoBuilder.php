<?php

namespace App\Domain\Patrimonial\PNCP\Builders;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AtaRegistroPrecoBuilder
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
        return $this->buildAta($this->dados);
    }

    private function buildAta(Request $request)
    {
        return (object)[
            'numeroAtaRegistroPreco'=> stripslashes(utf8_encode($request->get('numeroAtaRegistroPreco'))),
            'anoAta' => $request->get('anoAta'),
            'dataAssinatura' => $request->get('dataAssinatura'),
            'dataVigenciaInicio' => $request->get('dataVigenciaInicio'),
            'dataVigenciaFim' => $request->get('dataVigenciaFim'),
        ];
    }
}
