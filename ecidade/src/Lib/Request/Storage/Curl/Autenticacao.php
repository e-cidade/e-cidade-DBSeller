<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Lib\Request\Storage\Curl;

use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Lib\Request\Curl;
use ECidade\V3\Extension\Registry;
use Exception;

/**
 * Class Autenticacao
 * @package ECidade\Lib\Request\Storage\Curl
 */
class Autenticacao
{
    const ROUTE = 'oauth/token';

    private $url;
    private $grant_type;
    private $client_id;
    private $client_secret;
    private $scope;

    /**
     * @var Autenticacao
     */
    private static $instance;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var array
     */
    private $properties;

    private $disableSSLverification;

    /**
     * Autenticacao constructor.
     * @param Curl $curl
     */
    private function __construct(Curl $curl)
    {
        $configStorage = StorageHelper::getStorageConfig();
        $this->url           = $configStorage->url;
        $this->grant_type    = $configStorage->grant_type;
        $this->client_id     = $configStorage->client_id;
        $this->client_secret = $configStorage->client_secret;
        $this->scope         = isset($configStorage->scope) ? $configStorage->scope : '*';
        $this->disableSSLverification = false;
        if (!empty($configStorage->disableSSLverification)) {
            $this->disableSSLverification = (bool)  $configStorage->disableSSLverification;
        }
        $this->curl = $curl;
    }

    /**
     * @return Autenticacao
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Autenticacao(new Curl());
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        if ($this->hasPropertiesSession()) {
            return $this;
        }
        $this->curl->setOptions($this->getOptions());
        $this->curl->execute();

        $this->validateRequest();
        $this->setProperties();

        return $this;
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        $aplicacao = array(
          'grant_type'    => $this->grant_type,
          'client_id'     => $this->client_id,
          'client_secret' => $this->client_secret,
          'scope'         => $this->scope
        );

        $options = array();
        $options[CURLOPT_URL] = $this->url . '/' . self::ROUTE;
        $options[CURLOPT_POSTFIELDS] = $aplicacao;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_SSL_VERIFYPEER] = !$this->disableSSLverification;

        return $options;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function validateRequest()
    {
        switch ($this->curl->getCodeReturn()) {
            case Curl::CODE_INVALIDO:
                throw new Exception('Requisição Inválida.');
                break;

            case Curl::CODE_ACESSO_NEGADO:
                throw new Exception('Acesso Negado');
                break;

            case Curl::CODE_OK:
                break;
        }

        return true;
    }

    private function setProperties()
    {
        $this->properties = json_decode($this->curl->getResponse(), true);
        $_SESSION['estorage_properties'] = $this->properties;
    }


    /**
     * @return array|mixed
     * @throws Exception
     */
    public function getAccessToken()
    {
        return $this->getProperties('access_token');
    }

    /**
     * @param $key
     * @return array|mixed
     * @throws Exception
     */
    public function getProperties($key = null)
    {
        if (empty($this->properties)) {
            $logger = Registry::get('app.container')->get('app.logger');
            $logger->error($this->curl->getErro());
            throw new Exception('Propriedades de autenticação não existentes.');
        }

        if (!empty($key)) {
            if (!isset($this->properties[$key])) {
                throw new Exception('Propriedade {$this->properties[$key]} de autenticação inexistente.');
            }

            return $this->properties[$key];
        }

        return $this->properties;
    }

    /**
     * @return array|mixed
     * @throws Exception
     */
    public function getTokenType()
    {
        return $this->getProperties('token_type');
    }

    public function getCurl()
    {
        return $this->curl;
    }

    public function close()
    {
        return $this->curl->close();
    }

    public function url($url = null)
    {
        if (!empty($url)) {
            $this->url = $url;
            return $this;
        }

        return $this->url;
    }

    private function hasPropertiesSession()
    {
        if (isset($_SESSION['estorage_properties'])) {
            $this->properties = $_SESSION['estorage_properties'];
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getGrantType()
    {
        return $this->grant_type;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }



    /**
     * @return bool
     */
    public function isDisableSSLverification()
    {
        return $this->disableSSLverification;
    }
}
