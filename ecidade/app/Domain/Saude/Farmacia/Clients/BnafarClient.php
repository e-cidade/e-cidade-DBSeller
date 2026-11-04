<?php

namespace App\Domain\Saude\Farmacia\Clients;

use App\Domain\Saude\Farmacia\Exceptions\BnafarException;
use App\Domain\Saude\Farmacia\Services\BnafarEnviosService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use UnidadeProntoSocorro;

class BnafarClient
{
    private $username;

    private $password;

    private $ibge;

    private $cnes;

    /**
     * @param UnidadeProntoSocorro $unidade
     * @throws Exception
     */
    public function __construct(UnidadeProntoSocorro $unidade)
    {
        if (strlen($unidade->getIBGE()) < 6) {
            throw new Exception(
                'Código IBGE do município inválido na unidade. O código deve possuir 6 dígitos ou mais!'
            );
        }
        if (strlen($unidade->getCNES()) != 7) {
            throw new Exception('Código CNES inválido na unidade. O código deve possuir 7 dígitos!');
        }

        $configuracao = DB::table('farmacia.horususuario')->where('fa66_unidade', $unidade->getCodigo())->first();
        if ($configuracao == null) {
            $descricao = $unidade->getDepartamento()->getNomeDepartamento();
            $erro = "Configurações de acesso ao sistema do hórus(Bnafar) não configurados para a unidade {$descricao}.";
            throw new Exception($erro);
        }

        $this->username = $configuracao->fa66_usuario;
        $this->password = $configuracao->fa66_senha;
        $this->ibge = substr($unidade->getIBGE(), 0, 6);
        $this->cnes = $unidade->getCNES();

        // testa as credenciais de acesso e coloca no cache.
        $this->getAcessToken();
    }

    /**
     * @param string $procedimento ex: entrada
     * @param array|object $dados
     * @return object
     * @throws BnafarException|Exception
     */
    public function criar($procedimento, $dados)
    {
        return $this->doRequest('POST', $this->getUriProduto($procedimento), $dados);
    }

    /**
     * @param string $procedimento ex: entrada
     * @param array|object $dados
     * @param integer $id
     * @return object
     * @throws BnafarException|Exception
     */
    public function alterar($procedimento, $dados, $id)
    {
        $procedimento .= "/{$id}";
        return $this->doRequest('PUT', $this->getUriProduto($procedimento), $dados);
    }

    /**
     * @param string $procedimento ex: entrada
     * @param integer $id
     * @return object
     * @throws BnafarException|Exception
     */
    public function apagar($procedimento, $id)
    {
        $procedimento .= "/{$id}";
        return $this->doRequest('DELETE', $this->getUriProduto($procedimento));
    }

    /**
     * @param string $procedimento ex: entrada
     * @param integer $id
     * @param bool $lote
     * @param int $pageNumber
     * @param int $pageSize
     * @return object
     * @throws Exception
     */
    public function consultar($procedimento, $id, $lote = false, $pageNumber = 0, $pageSize = 10)
    {
        if ($lote) {
            $procedimento .= "/consultar?protocolo={$id}&pageNumber={$pageNumber}&pageSize={$pageSize}";
        } else {
            $procedimento .= "/{$id}";
        }

        return $this->doRequest('GET', $this->getUriProduto($procedimento));
    }

    /**
     * @param string $procedimento
     * @param integer $id
     * @return object
     * @throws BnafarException|Exception
     */
    public function protocolo($procedimento, $id, $pageNumber = 0, $pageSize = 10)
    {
        $procedimento .= "/{$id}?pageNumber={$pageNumber}&pageSize={$pageSize}";
        return $this->doRequest('GET', $this->getUriProtocolo($procedimento));
    }

    /**
     * @param string|null $dataInicial
     * @param string|null $dataFinal
     * @param integer $pageNumber
     * @param integer $pageSize
     * @param integer|null $tipoServico
     * @param integer|null $tipoOperacao
     * @return object
     * @throws BnafarException|Exception
     */
    public function pesquisarProtocolos(
        $dataInicial = null,
        $dataFinal = null,
        $pageNumber = 0,
        $pageSize = 10,
        $tipoServico = null,
        $tipoOperacao = null
    ) {
        $queryParams = [
            "pageNumber={$pageNumber}",
            "pageSize={$pageSize}"
        ];
        if ($dataInicial !== null) {
            $queryParams[] = "dataInicial={$dataInicial}";
        }
        if ($dataFinal !== null) {
            $queryParams[] = "dataFinal={$dataFinal}";
        }
        if ($tipoServico !== null) {
            $queryParams[] = "tipoServico={$tipoServico}";
        }
        if ($tipoOperacao !== null) {
            $queryParams[] = "tipoOperacao={$tipoOperacao}";
        }

        $procedimento = 'pesquisar?' . implode('&', $queryParams);
        return $this->doRequest('GET', $this->getUriProtocolo($procedimento));
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array|object $dados
     * @return object
     * @throws BnafarException|Exception
     */
    private function doRequest($method, $uri, $dados = null)
    {
        try {
            $client = $this->getClient();
            $options = [
                'synchronous' => true,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Authorization' => $this->getAcessToken()
                ]
            ];

            if ($dados != null) {
                $options['body'] = json_encode($dados);
            }

            $response = $client->requestAsync($method, $uri, $options)->wait();
            $response = (object)json_decode($response->getBody());

            if ($dados != null) {
                $protocolo = property_exists($response, 'protocolo') ? $response->protocolo : null;
                BnafarEnviosService::salvar($method, $uri, $dados, $protocolo);
            }

            return $response;
        } catch (ClientException $e) {
            throw new BnafarException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse(),
                $e->getPrevious(),
                $e->getHandlerContext()
            );
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getAcessToken()
    {
        $cacheKey = "access_token_bnafar#{$this->getAuthorizationCode()}#{$this->cnes}";
        $accessToken = Cache::get($cacheKey);
        if (!empty($accessToken)) {
            return $accessToken;
        }

        try {
            $client = $this->getClient();
            $options = [
                'auth' => [$this->username, $this->password],
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ];
            $response = $client->get('jwtauth/auth', $options);
            $response = (object)json_decode($response->getBody());

            $accessToken = "jwt {$response->access_token}";
            // coverte o tempo de expiração do token para minutos
            $expiresIn = $response->expires_in / 60000;
            Cache::put($cacheKey, $accessToken, $expiresIn);

            return $accessToken;
        } catch (Exception $e) {
            $erroMsg = $e->getMessage();
            if ($e instanceof ClientException) {
                $e = new BnafarException(
                    $e->getMessage(),
                    $e->getRequest(),
                    $e->getResponse(),
                    $e->getPrevious(),
                    $e->getHandlerContext()
                );
                $erroMsg = $e->getDetalhes();
            }
            throw new Exception("Erro ao autenticar usuário no sistema BNAFAR\nMensagem do erro:\n{$erroMsg}");
        }
    }

    /**
     * @return Client
     * @throws Exception
     */
    private function getClient()
    {
        return new Client(['base_uri' => env('SERVICOS_SUS_URL')]);
    }

    /**
     * @return string
     */
    private function getAuthorizationCode()
    {
        $data = "{$this->username}:{$this->password}";
        return base64_encode($data);
    }

    /**
     * @param string $procedimento
     * @return string
     */
    private function getUriProduto($procedimento)
    {
        return "bnafar/produto/ibge/{$this->ibge}/{$procedimento}";
    }

    /**
     * @param string $procedimento
     * @return string
     */
    private function getUriProcedimento($procedimento)
    {
        return "bnafar/procedimento/ibge/{$this->ibge}/{$procedimento}";
    }

    private function getUriProtocolo($procedimento)
    {
        return "bnafar/protocolo/ibge/{$this->ibge}/{$procedimento}";
    }
}
