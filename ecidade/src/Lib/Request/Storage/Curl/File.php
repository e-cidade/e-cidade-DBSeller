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

use ECidade\Lib\Request\Curl;
use Exception;

/**
 * Class File
 * @package ECidade\Lib\Request\Storage\Curl
 */
class File
{
    const ROUTE_PREFIX = '/api';
    const ROUTE = '/api/files/';

    const HEADER_ACCEPT = 'Accept';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_CONTENT_TYPE = 'Content-Type';

    const REQUISICAO_GET    = 'GET';
    const REQUISICAO_PUT    = 'PUT';
    const REQUISICAO_DELETE = 'DELETE';
    const REQUISICAO_POST   = 'POST';

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Autenticacao
     */
    private $autenticacao;

    /**
     * @var int
     */
    private $codigoArquivo;

    /**
     * @var array
     */
    protected $headers    = array();
    private $options    = array();
    protected $postFields = array();

    /**
     * @var string
     */
    private $tipoRequisicao;

    /**
     * @var string
     */
    private $routePrefix = null;

    /**
     * @var string
     */
    private $route;

    /**
     * File constructor.
     * @param Curl $curl
     * @param Autenticacao $autenticacao
     * @throws Exception
     */
    public function __construct(Autenticacao $autenticacao)
    {
        $this->autenticacao = $autenticacao;
        $this->curl = $this->autenticacao->getCurl();
        $this->setHeadersDefault();
    }

    /**
     * @throws Exception
     */
    private function setHeadersDefault()
    {
        $tokenType = $this->autenticacao->getTokenType();
        $accessToken = $this->autenticacao->getAccessToken();

        $this->headers[self::HEADER_ACCEPT] = self::HEADER_ACCEPT . ': application/json';
        $this->headers[self::HEADER_AUTHORIZATION] = self::HEADER_AUTHORIZATION . ": {$tokenType} {$accessToken}";
    }

    public function addHeader($header, $key = null)
    {
        $key = !empty($key) ? $key : count($this->headers) + 1;

        $this->headers[$key] = $header;
    }

    public function execute()
    {
        $this->curl->setOptions($this->getOptions());
        $this->curl->execute();

        return $this->curl->getResponse();
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        $this->options[CURLOPT_RETURNTRANSFER] = true;
        $this->options[CURLOPT_CUSTOMREQUEST] = $this->tipoRequisicao;
        $this->options[CURLOPT_SSL_VERIFYPEER] = !$this->autenticacao->isDisableSSLverification();

        $url  = $this->autenticacao->url();
        $url .= empty($this->route) ? self::ROUTE : $this->route;

        if (!empty($this->codigoArquivo)) {
            $url .= $this->codigoArquivo;
        }

        $this->options[CURLOPT_URL] = $url;

        if (!empty($this->headers)) {
            $this->options[CURLOPT_HTTPHEADER] = $this->headers;
        }

        if (in_array($this->tipoRequisicao, array(self::REQUISICAO_PUT, self::REQUISICAO_POST))) {
            $this->options[CURLOPT_POSTFIELDS] = $this->headers;
        }

        return $this->options;
    }

    public function setCodigoArquivo($codigoArquivo)
    {
        $this->codigoArquivo = $codigoArquivo;
    }

    public function getResponse()
    {
        return $this->curl->getResponse();
    }

    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getInfo($type = null)
    {
        if (!empty($type)) {
            return $this->curl->getInfo($type);
        }

        return $this->curl->getInfo();
    }

    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
        return $this;
    }

    public function setRoute($route)
    {
        $this->route  = is_null($this->routePrefix) ? self::ROUTE_PREFIX : $this->routePrefix;
        $this->route .= $route;
        return $this;
    }

    protected function setTipoRequisicao($tipoRequisicao)
    {
        $this->tipoRequisicao = $tipoRequisicao;
    }

    protected function addPostField($key, $value)
    {
        $this->postFields[$key] = $value;
    }
}
