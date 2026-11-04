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

use GuzzleHttp\Client;

class CurrentVersion
{
    const ROUTE = '/api/files/recent-version-id/';
    private $client;

    /**
     * @var Autenticacao
     */
    private $autenticacao;

    private $headers;

    /**
     * @throws \Exception
     */
    public function __construct(Autenticacao $autenticacao)
    {
        $optionsGuzzle = [];
        if ($autenticacao->isDisableSSLverification()) {
            $optionsGuzzle["verify"] = false;
        }
        $this->client = new Client($optionsGuzzle);
        $this->autenticacao = $autenticacao;
        $this->autenticacao->execute();
        $authorization = $this->autenticacao->getTokenType();
        $authorization .= ' ';
        $authorization .= $this->autenticacao->getAccessToken();
        $this->headers = [
            'Authorization' => $authorization,
            'Accept' => 'application/json',
        ];
    }

    /**
     * @param $idStorage
     * @return bool
     */
    public function execute($idStorage)
    {
        $response = $this->client->post($this->autenticacao->url() . self::ROUTE . $idStorage, [
            'headers' => $this->headers,
            'params' => [
                'oauth_client' => $this->autenticacao->getClientId()
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
