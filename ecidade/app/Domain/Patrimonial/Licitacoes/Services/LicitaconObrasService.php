<?php

namespace App\Domain\Patrimonial\Licitacoes\Services;

use App\Domain\Patrimonial\Contratos\Models\Acordo;
use App\Domain\Patrimonial\Licitacoes\Clients\LicitaconObrasClient;
use App\Domain\Patrimonial\Licitacoes\Models\LicitaconUsuario;
use App\Domain\Patrimonial\Licitacoes\Requests\IncluirObraRequest;
use App\Domain\Patrimonial\Licitacoes\Requests\SalvarUsuarioRequest;

class LicitaconObrasService
{
    public function verificarOrgaoFiscalizado($instituicao)
    {
        $client = new LicitaconObrasClient();
        $orgao = $client->buscarOrgaoFiscalizado($instituicao->cgc);

        return (bool)$orgao;
    }

    public function incluir(Acordo $acordo, IncluirObraRequest $request)
    {
        $instituicao = $acordo->instituicao()->firstOrFail();
        $client = new LicitaconObrasClient();

        $dados = $this->montarDadosInclusaoObra($acordo, $request);
        $request->cnpj = $instituicao->cgc;
        return $client->incluir($dados, $request);
    }

    public function montarDadosInclusaoObra(Acordo $acordo, IncluirObraRequest $request)
    {
        $dados = [];
        $dados['contrato'] = [];
        $dados['contrato']['anoContrato'] = $acordo->ac16_anousu;
        $dados['contrato']['numeroContrato'] = $acordo->ac16_numero;
        $dados['contrato']['tipoInstrumento'] = $request->tipoInstrumento;
        $dados['garantiaObra'] = !empty($request->garantia);

        $dados['subfamilias'] = [];
        $dados['subfamilias'][0]['codigoTipoFamilia'] = (int)$request->codigoTipoFamilia;
        $dados['subfamilias'][0]['codigoTipoSubfamilia'] = (int)$request->codigoTipoSubFamilia;

        $dados['localizacao'] = [];
        $dados['localizacao']['cep'] = $request->cep;
        $dados['localizacao']['logradouro'] = mb_convert_encoding($request->logradouro, 'UTF-8', 'ISO-8859-1');
        $dados['localizacao']['bairro'] = mb_convert_encoding($request->bairro, 'UTF-8', 'ISO-8859-1');
        $dados['localizacao']['municipio'] = mb_convert_encoding($request->municipio, 'UTF-8', 'ISO-8859-1');

        foreach ($request->caracteristicas as $caracteristica) {
            $dados['caracteristicas'][] = $caracteristica;
        }

        return $dados;
    }

    public function buscarFamilias()
    {
        $client = new LicitaconObrasClient();
        return $client->buscarFamilias();
    }

    public function buscarSubFamilias($familias)
    {
        $client = new LicitaconObrasClient();
        return $client->buscarSubFamilias($familias);
    }

    public function buscarDetalhamentoCarateristicas()
    {
        $client = new LicitaconObrasClient();
        return $client->buscarDetalhamentoCarateristicas();
    }

    /**
     * @param $instituicao
     * @return LicitaconUsuario
     */
    public function buscarUsuario($instituicao)
    {
        $usuario = new LicitaconUsuario();
        return $usuario->where(['l50_instituicao' => $instituicao])->first();
    }

    /**
     * @param $instituicao
     * @param SalvarUsuarioRequest $request
     * @return bool
     * @throws \Throwable
     */
    public function salvarUsuario($instituicao, SalvarUsuarioRequest $request)
    {
        $usuario = LicitaconUsuario::updateOrCreate(
            ['l50_instituicao' => $instituicao],
            [
                'l50_instituicao' => $instituicao,
                'l50_chave' => $request->chave,
                'l50_id_externo' => $request->idExterno,
            ]
        );

        return $usuario->saveOrFail();
    }
}
