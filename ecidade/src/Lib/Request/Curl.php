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

namespace ECidade\Lib\Request;

use Exception;

/**
 * Class Curl
 * @package ECidade\Lib\Request
 */
class Curl
{
    const CODE_OK = 200;
    const CODE_INVALIDO = 400;
    const CODE_ACESSO_NEGADO = 401;

    /**
     * @var resource
     */
    private $resource;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var bool|resource
     */
    private $response;

    /**
     * @var array
     */
    private $headers = array();

    public function __construct()
    {
        $this->resource = curl_init();
    }

    /**
     * @param $option
     * @throws Exception
     */
    public function addOption($option)
    {
        if (empty($option)) {
            throw new Exception('Nenhuma opção informada.');
        }

        $this->options[] = $option;
    }

    /**
     * @param array $options
     */
    public function setOptions($options = array())
    {
        $this->options = $options;
    }

    /**
     * @throws \ParameterException
     */
    public function execute()
    {
        if (!empty($this->options)) {
            curl_setopt_array($this->resource, $this->options);
        }
        $this->response = curl_exec($this->resource);
    }

    public function close()
    {
        curl_close($this->resource);
    }

    /**
     * @return bool|resource
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $header
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return int
     */
    public function getCodeReturn()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * @param null|mixed $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        if (!empty($type)) {
            return curl_getinfo($this->resource, $type);
        }

        return curl_getinfo($this->resource);
    }

    public function getErro()
    {
        if (curl_errno($this->resource) == 0) {
            return false;
        }

        return curl_strerror(curl_errno($this->resource));
    }
}
