<?php

namespace App\Domain\Patrimonial\Licitacoes\Clients;

use App\Domain\Patrimonial\Licitacoes\Models\LicitaconUsuario;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;

class LicitaconObrasClient extends LicitaconClient
{
    private $version = 'v1';

    public function __construct()
    {
        $config = [];
        $config['base_uri'] = env('LICITACON_URL') . "api/obras/{$this->version}/";
        $config['headers']['Authorization'] = "Bearer {$this->getAccessToken()}";

        parent::__construct($config);
    }

    /**
     * @return mixed|void
     */
    private function getAccessToken()
    {
        $chaveCache = "access_token_licitacon";
        $tokenAcesso = Cache::get($chaveCache);

        if (!empty($tokenAcesso)) {
            return $tokenAcesso;
        }

        $licitaconUsuarios = new LicitaconUsuario();
        $usuario = $licitaconUsuarios->where(['l50_instituicao' => db_getsession('DB_instit')])->firstOrFail();

        $options = [];
        $options['json']['idExterno'] = $usuario->l50_id_externo;
        $options['json']['chave'] = $usuario->l50_chave;

        $client = new Client(['base_uri' => env('LICITACON_URL') . '/api/obras/']);
        $response = $client->post('autenticacao', $options);
        $autenticacao = json_decode($response->getBody()->getContents());

        if (!empty($autenticacao->token)) {
            Cache::put(
                $chaveCache,
                $autenticacao->token,
                !empty($autenticacao->expiraEm) ? $autenticacao->expiraEm / 60000 : 30
            );

            return $autenticacao->token;
        }
    }

    /**
     * @return array|false
     */
    public function buscarFamilias()
    {
        $response = $this->get($this->getUriBuscarFamilias());
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return string
     */
    private function getUriBuscarFamilias()
    {
        return "familias";
    }

    /**
     * @param array $familias
     * @return mixed
     */
    public function buscarSubFamilias(array $familias)
    {
        $options = [];
        $options['query']['familias'] = implode(',', $familias);

        $response = $this->get($this->getUriBuscarSubFamilias(), $options);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return string
     */
    private function getUriBuscarSubFamilias()
    {
        return "subfamilias";
    }

    /**
     * @param string $cnpj
     * @return array|bool
     */
    public function buscarOrgaoFiscalizado($cnpj)
    {
        $posicaoOrgaoFiscalizado = false;
        $response = $this->get($this->getUriOrgaosFiscalizados());
        $orgaosFiscalizados = json_decode($response->getBody()->getContents());

        foreach ($orgaosFiscalizados as $key => $orgao) {
            if ($orgao->cnpj === $cnpj) {
                $posicaoOrgaoFiscalizado = $key;
            }
        }

        return $posicaoOrgaoFiscalizado ? $orgaosFiscalizados[$posicaoOrgaoFiscalizado] : false;
    }

    /**
     * @return string
     */
    protected function getUriOrgaosFiscalizados()
    {
        return "orgaos-fiscalizados";
    }

    /**
     * @return mixed
     */
    public function buscarDetalhamentoCarateristicas()
    {
        $response = $this->get($this->getUriDetalhamentoCaracteristicas());
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return string
     */
    private function getUriDetalhamentoCaracteristicas()
    {
        return "detalhamentos-caracteristicas";
    }

    /**
     * @param $dados
     * @param $request
     * @return mixed
     */
    public function incluir($dados, $request)
    {
        $options = [];
        $options['body'] = json_encode($dados);

        $response = $this->post($this->getUriIncluirObra($request->cnpj), $options);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param $cnpj
     * @return string
     */
    public function getUriIncluirObra($cnpj)
    {
        return "orgaos/{$cnpj}/obras";
    }
}
