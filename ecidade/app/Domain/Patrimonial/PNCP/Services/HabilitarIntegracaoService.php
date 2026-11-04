<?php

namespace App\Domain\Patrimonial\PNCP\Services;

use App\Domain\Patrimonial\PNCP\Clients\PNCPClient;
use App\Domain\Patrimonial\PNCP\Exceptions\CompraEditalAvisoExcpetion;
use App\Domain\Patrimonial\PNCP\Models\IntegracaoPNCP;
use App\Domain\Patrimonial\PNCP\Models\VerificaUnidadeRequisitante;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HabilitarIntegracaoService
{
    private $http;

    public function __construct()
    {
        $this->http = new PNCPClient();
    }

    /**
     * @param $documento
     * @param $instituicao
     * @return string
     */
    public function habilitarIntegracaoPNCP($documento, $instituicao, $usuario)
    {
        if (!$this->verificaEnteAutorizado($documento)) {
            $this->incluirEnteAutorizado($documento);
        }
        $this->incluirIntegracaoPNCP($instituicao, $usuario);
        $mensagem = "Ação processada. Lista de entes autorizados no PNCP atualizada.";

        return $mensagem;
    }

    /**
     * @param $instituicao
     * @return Builder|Model|null
     */
    public function verificaIntegracao($instituicao)
    {
        return IntegracaoPNCP::query()->where('pn01_instit', '=', $instituicao)->first();
    }

    /**
     * @param $documento
     * @return bool
     * @throws \Exception
     */
    public function verificaEnteAutorizado($documento)
    {
        try {
            $response = $this->http->verificaEnteAutorizado(env("PNCP_CLIENTE_ID"));
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }

        $entes = $response->entesAutorizados;
        foreach ($entes as $ente) {
            if ($ente->cnpj === $documento) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $documento
     * @return void
     * @throws \Exception
     */
    private function incluirEnteAutorizado($documento)
    {
        $dados = ["entesAutorizados" => ["{$documento}"]];
        $client_id = env("PNCP_CLIENTE_ID");
        try {
            $this->http->incluirEnteAutorizado($client_id, $dados);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }


    /**
     * @param $instituicao
     * @return void
     */
    private function incluirIntegracaoPNCP($instituicao, $usuario)
    {
        IntegracaoPNCP::create([
            'pn01_data' => date('Y-m-d'),
            'pn01_instit' => $instituicao,
            'pn01_habilitado' => true,
            'pn01_usuario' => intval($usuario)
        ]);
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function incluirOrgao(Request $request)
    {
        $cnpj = str_replace(
            '/',
            '',
            str_replace('-', '', str_replace('.', '', $request->cnpj))
        );
        $dados = [
            'cnpj' => $cnpj,
            'razaoSocial' => utf8_encode($request->razaoSocial),
            'poderId' => $request->poder,
            'esferaId' => $request->esfera
        ];
        try {
            $this->http->incluirOrgao($dados);
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }

    public function bloquearUnidade(Request $request)
    {
        try {
            if (isset($request->verificar)) {
                return VerificaUnidadeRequisitante::where('pn07_instit', $request->DB_instit)
                    ->get()
                    ->toArray();
            }
            $table = new VerificaUnidadeRequisitante();
            $table->pn07_habilitado = $request->bloquear;
            $table->pn07_instit = $request->DB_instit;
            $table->pn07_usuario = $request->DB_id_usuario;
            $table->save();
        } catch (CompraEditalAvisoExcpetion $e) {
            throw new \Exception($e->getErros());
        }
    }
}
