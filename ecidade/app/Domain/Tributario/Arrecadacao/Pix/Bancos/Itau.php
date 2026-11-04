<?php

namespace App\Domain\Tributario\Arrecadacao\Pix\Bancos;

use \GuzzleHttp\Client;

use App\Domain\Configuracao\Banco\Models\Db_bancos_pix;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;
use App\Domain\Tributario\Arrecadacao\Pix\Adapter\PixBanco;

use DBSeller\SdkBancoItau\Configuration;
use DBSeller\SdkBancoItau\API\OauthApi;
use DBSeller\SdkBancoItau\API\CobrancaComVencimentoCobvApi;
use DBSeller\SdkBancoItau\API\CobrancaImediataCobApi;

use Ramsey\Uuid\Uuid;

use convenio;

class Itau extends Banco implements PixBanco
{

    const BANK_CODE = '341';

    /**
     *  Tipo de Ambiente
     *  2 - Homologacao
     *  1 - Producao
     * @var integer
     */
    protected $tipoAmbiente = 2;

    /**
     *  Url dos ambientes
     *  2 - Homologacao
     *  1 - Producao
     * @var array
     */
    protected $urlAmbiente = [
        1 => 'https://secure.api.itau/pix_recebimentos/v2',
        2 => 'https://devportal.itau.com.br/sandboxapi/pix_recebimentos_ext_v2/v2'
    ];

    /**
     * URL DE PRODUCAO
     */
    protected $urlProducao;

    /**
     * Url de Autenticacao do itau
     * @var string
     */
    protected $urlAutenticacao = [
        1 => 'https://sts.itau.com.br/api/oauth/token',
        2 => 'https://devportal.itau.com.br/api/jwt'
    ];

    protected $config;

    public function getTipoAmbiente()
    {
        return $this->tipoAmbiente;
    }

    public function isProducao()
    {
        return ($this->tipoAmbiente  == 1 ? true : false);
    }

    public function setTipoAmbiente($tipoAmbiente)
    {
        $this->tipoAmbiente = $tipoAmbiente;
    }

    protected function getUrlAplicacao()
    {
        return $this->urlAmbiente[$this->getTipoAmbiente()];
    }

    protected function geturlAutenticacao()
    {
        return $this->urlAutenticacao[$this->getTipoAmbiente()];
    }

    public function __construct()
    {
        $bancoPix = Db_bancos_pix::where(
            'db90_codban',
            self::CODIGO_BANCO
        )->first();

        if (!$bancoPix) {
            throw new \Exception("Pix não configurado para o Banco");
        }

        $this->setUsuario($bancoPix->db90_login);
        $this->setSenha($bancoPix->db90_senha);
        $this->setChavePix($bancoPix->db90_chave_pix);
        $this->setTipoAmbiente($bancoPix->db90_tipo_ambiente);
        $this->setCnpjMunicipio($bancoPix->db90_cnpj_municipio);
        $this->setCnpj($bancoPix->db90_cnpj);
        $this->config = $this->preparaConfiguracao();
    }

    protected function preparaConfiguracao()
    {
        $config = Configuration::getDefaultConfiguration();
        $config->setHost($this->getUrlAplicacao());
        $config->setUrlOAuth($this->geturlAutenticacao());
        $config->setApiKey('client_id', $this->usuario);
        $config->setApiKey('client_secret', $this->senha);
        $config->setModoProducao($this->isProducao());

        if ($this->isProducao()) {
            $certificados = $this->getCertificados();
            $config->setPathCertificado($certificados['certificado']);
            $config->setPathPrivateKey($certificados['chave']);
        }

        $clienteOauth = new OauthApi(
            new Client(),
            $config
        );

        $accessTokenOauth = $clienteOauth->gerarAccessToken();
        $config->setAccessToken($accessTokenOauth);

        return $config;
    }

    public function gerarPix()
    {
        if (!$this->validaEmissao()) {
            return;
        }
        /**
         * todo
         * tipo emissao cobranca con vencimento
         * somente
         */
        $tipoEmissao = 1;
        $response = null;
        $statusCode = null;
        $model = null;
        $headers = null;

        switch ($tipoEmissao) {
            case 1:
                $apiInstance = new CobrancaComVencimentoCobvApi(
                    new Client(),
                    $this->config
                );
                $uid = Uuid::uuid4()->toString();
                $uid = str_replace("-", "", $uid);
                $data = $this->buildCobVencimento();

                $response = $apiInstance->putcobvtxid(
                    $uid,
                    $data
                );
                list($model, $statusCode, $headers) = $response;
                break;
            case 2:
                $apiInstance = new CobrancaImediataCobApi(
                    new Client(),
                    $this->config
                );
                $data = $this->buildCobrancaImediata();
                $response = $apiInstance->postcob(
                    $data
                );
                list($model, $statusCode, $headers) = $response;
                $uid = $model->getTxid();
                break;
            default:
                throw new \Exception("Requisição não implementada.");
                break;
        }

        if ($statusCode === 201 && $model) {
            $reciboparpix = new Recibobarpix();
            $reciboparpix->k00_numpre = $this->getCodigoArrecadacao();
            $reciboparpix->k00_numpar = $this->getParcela();
            $reciboparpix->k00_codbar = $this->convenio->getCodigoBarra();
            $reciboparpix->k00_criacaosolicitacao = $model->getCalendario()->getCriacao();
            $reciboparpix->k00_estadosolicitacao = $model->getStatus();
            $reciboparpix->k00_conciliacaosolicitante = $uid;
            $reciboparpix->k00_numeroversaosolicitacaopagamento = $model->getRevisao();
            $reciboparpix->k00_linkqrcode = $model->getLoc()->getLocation();
            $reciboparpix->k00_qrcode = $model->getPixCopiaECola();
            $reciboparpix->save();
        }
    }

    /**
     *  Monta o corpo da cobranca com vencimento
     * @return stdClass
     */
    private function buildCobVencimento()
    {
        $cpfCnpj = $this->verifyCPFCNPJ($this->cgm->z01_cgccpf);
        $keyDevedor = (strlen($cpfCnpj) > 13 ? 'cnpj' : 'cpf');
        $body = [
            'calendario' => (object)[
                'dataDeVencimento' => $this->getVencimento()
            ],
            'valor' => (object)[
                'original' => $this->getValor()
            ],
            'devedor' => (object)[
                'nome' => utf8_encode($this->cgm->z01_nome),
                $keyDevedor => $cpfCnpj
            ],
            'chave' => $this->getChavePix(),
            'solicitacaoPagador' => 'Pagamento de tributos',
            'infoAdicionais' => [
                (object) [
                    'nome' => 'codbarra',
                    'valor' => $this->convenio->getCodigoBarra()
                ]
            ]
        ];
        return (object) $body;
    }

    /**
     * Monta o corpo da cobranca imediata
     *
     * @return stdClass
     */
    private function buildCobrancaImediata()
    {
        $cpfCnpj = $this->verifyCPFCNPJ($this->cgm->z01_cgccpf);
        $keyDevedor = (strlen($cpfCnpj) > 13 ? 'cnpj' : 'cpf');
        $body = [
            'calendario' => (object)[
                'expiracao' => $this->getSegundosExpiracao()
            ],
            'valor' => (object)[
                'original' => $this->getValor()
            ],
            'devedor' => (object)[
                'nome' => $this->cgm->z01_nome,
                $keyDevedor => $cpfCnpj
            ],
            'chave' => $this->getChavePix(),
            'solicitacaoPagador' => 'Pagamento de tributos',
            'infoAdicionais' => [
                (object) [
                    'nome' => 'codbarra',
                    'valor' => $this->convenio->getCodigoBarra()
                ]
            ]
        ];
        return (object) $body;
    }

    /**
     * Verifica o CPF ou CNPJ se CNPJ estiver habilitado no banco ele retorna o CNPJ
     * do banco para gerar cobran�a
     *
     * @param string $documento CPF ou CNPJ
     *
     * @return string
     */
    public function verifyCPFCNPJ($document)
    {
        if (strlen($document) === 11 || strlen($document) === 14) {
            return $document;
        }

        if ($this->getCnpjMunicipio()) {
            return $this->getCnpj();
        }

        return '';
    }

    /**
     * Calcula vencimento em segundos
     * return integer
     */
    protected function getSegundosExpiracao()
    {
        $dataInicial = new \DateTime();
        $dataFinal   = new \DateTime("{$this->getVencimento()} 23:59:59");
        $diferenca   = ($dataFinal->getTimestamp() - $dataInicial->getTimestamp());

        return $diferenca;
    }

    /**
     * Valida Emissao do pix para casos
     *  com cadastro desatualizados
     * @return boolean
     */
    protected function validaEmissao()
    {
        if (empty($this->cgm) || !$this->cgm instanceof Cgm) {
            return false;
        }

        if (empty($this->convenio) || !$this->convenio instanceof convenio) {
            return false;
        }

        if ((bool)$this->getCnpjMunicipio() === false
            && (empty($this->cgm->z01_cgccpf) || !validarCPFCNPJ($this->cgm->z01_cgccpf))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retorna o caminho dos certificados
     * @return array
     */
    private function getCertificados()
    {

        $certificado = ECIDADE_PATH . 'storage/app/certificados/bancos/itau/pix.crt';
        $chave = ECIDADE_PATH . 'storage/app/certificados/bancos/itau/pix.key';

        if (!file_exists($chave) || !file_exists($certificado)) {
            throw new \Exception("Certificado não encontrado.");
        }

        return [
            'chave' => $chave,
            'certificado' => $certificado
        ];
    }
}
