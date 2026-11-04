<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\UnidadesPNCP;

class UnidadeService
{
    private $http;

    public function __construct()
    {
        $this->http = new PNCPClient();
    }

    /**
     * @param $documento
     * @param $codigoIbge
     * @param $unidadeCodigo
     * @param $unidadeNome
     * @param $ativo
     * @return string
     * @throws \Exception
     */
    public function incluirUnidadeApi($documento, $codigoIbge, $unidadeCodigo, $unidadeNome, $ativo, $instituicao, $alt)
    {
        $dados = [
            "codigoIBGE" => $codigoIbge,
            "codigoUnidade" => $unidadeCodigo,
            "nomeUnidade" => $unidadeNome
        ];

        if ($this->buscarUnidade($documento, $unidadeCodigo) && $alt === 'false') {
            throw new \Exception('Não foi possível incluir a Unidade Compradora, pois já existe uma unidade com
             este mesmo código no PNCP.');
        }

        if (!$this->buscarUnidade($documento, $unidadeCodigo) && $alt === 'false') {
            try {
                $this->http->incluirUnidade($documento, $dados);
            } catch (CompraEditalAvisoExcpetion $e) {
                throw new \Exception($e->getErros());
            }
        }

        $this->incluirUnidade($unidadeCodigo, $ativo, $unidadeNome, $instituicao);
        $toggle =  "atualizada";
        if ($alt === 'false') {
            $toggle = "cadastrada";
        }
        return "Unidade compradora {$toggle} com sucesso!";
    }

    /**
     * @param $documento
     * @return object
     * @throws \Exception
     */
    public function buscaEntidade($documento)
    {
        try {
            return $this->http->buscarEntidade($documento);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    /**
     * @param $documento
     * @return array
     * @throws \Exception
     */
    public function buscarUnidades($documento)
    {
        $dados = [];
        try {
            $unidades = $this->http->buscarUnidades($documento);
        } catch (CompraEditalAvisoExcpetion $e) {
            return $dados;
        }

        foreach ($unidades as $unidade) {
            if ($unidade->codigoUnidade == 'QUALQUER UM' || $unidade->codigoUnidade == 'QUALQUER UM DOIS') {
                continue;
            }
            $data = new \DateTime($unidade->dataInclusao);
            $toggle = UnidadesPNCP::where('pn02_unidade', $unidade->codigoUnidade)->first();
            $ativo = true;
            if (!is_null($toggle)) {
                $ativo = $toggle->pn02_ativo;
            }
            $dados[] = [
                'codigoOrgao'    => $unidade->orgao->id,
                'orgao' => $unidade->orgao->razaoSocial,
                'codigoUnidade' => $unidade->codigoUnidade,
                'nomeUnidade'    => $unidade->nomeUnidade,
                'data'   => $data->format('Y-m-d H:i:s'),
                'ativo' => $ativo
            ];
        }
        usort($dados, function ($a, $b) {
            return $a['data'] < $b['data'];
        });

        return $dados;
    }

    public function buscarUnidaedsAtivas($instituicao)
    {
        return UnidadesPNCP::where('pn02_instit', $instituicao)
            ->where('pn02_ativo', true)->get();
    }

    /**
     * @param $documento
     * @param $unidadeCodigo
     * @return bool
     */
    public function buscarUnidade($documento, $codigoUnidade)
    {
        $unidades = $this->buscarUnidades($documento);

        foreach ($unidades as $unidade) {
            if ($unidade['codigoUnidade'] === $codigoUnidade) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $unidade
     * @param $ativo
     * @param $nome
     * @return void
     */
    private function incluirUnidade($unidade, $ativo, $nome, $instituicao)
    {
        UnidadesPNCP::updateOrCreate(
            ['pn02_unidade' => $unidade],
            [
                'pn02_unidade' => $unidade,
                'pn02_nome' => utf8_decode($nome),
                'pn02_ativo' => $ativo,
                'pn02_instit'=> $instituicao,
                'pn02_data' => date('Y-m-d')
            ]
        );
    }
}
