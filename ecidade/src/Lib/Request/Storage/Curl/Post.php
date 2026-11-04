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

use ECidade\Lib\Request\Storage\File as FileStorage;
use GuzzleHttp\Client;
use \JSON;

/**
 * Class Post
 * @package ECidade\Lib\Request\Storage\Curl
 *
 * @todo IMPLEMENTAÇÃO/TESTE NÃO CONCLUÍDOS
 */
class Post
{
    private $autenticacao;
    private $client;
    private $headers = [];

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

    public function execute(FileStorage $file)
    {

        $multipart = $this->prepareMultipart($file);

        $arquivo = $this->client->post($this->autenticacao->url() . '/api/files', array(
            'headers' => $this->headers,
            'multipart' => $multipart
        ));

        if ($arquivo->getStatusCode() != 201) {
            throw new \Exception($arquivo->getBody());
        }

        $response = JSON::create()->parse($arquivo->getBody()->getContents());

        if ($response->data->visibility == 'public') {
            $file->url($this->autenticacao->url() . '/' . $response->data->url);
        }

        return $response;
    }

    public function change($id, FileStorage $file)
    {
        $multipart = $this->prepareMultipart($file);
        $arquivo = $this->client->post($this->autenticacao->url() . '/api/files/' . $id . '/change', array(
            'headers' => $this->headers,
            'multipart' => $multipart
        ));

        if ($arquivo->getStatusCode() != 200) {
            throw new \Exception($arquivo->getBody());
        }

        $response = JSON::create()->parse($arquivo->getBody()->getContents());

        if ($response->data->visibility == 'public') {
            $file->url($this->autenticacao->url() . '/' . $response->data->url);
        }

        return $response;
    }


    private function prepareMultipart(FileStorage $file)
    {

        $multipart = array(
            array(
                'name' => 'file',
                'contents' => file_get_contents($file->realPath()),
                'filename' => $file->clientOriginalName()
            ),
            array(
                "name" => 'visibility',
                'contents' => $file->visibility()
            ),
        );

        $allowed = $file->allowed();
        if (!empty($allowed)) {
            $allowed = array_map(function ($id) {
                return array(
                    "name" => 'allowed[]',
                    'contents' => $id
                );
            }, $allowed);

            $multipart = array_merge($multipart, $allowed);
        }

        $signers = $file->signers();
        $signers_signed = $file->signersSigned();

        if (!empty($signers) || !empty($signers_signed)) {
            $multipart = array_merge($multipart, array(
                array(
                    "name" => 'sign_required',
                    "contents" => true
                )
            ));
        }

        if (!empty($signers)) {
            $signers = array_map(function ($signer) {
                return array(
                    "name" => 'signers[]',
                    "contents" => JSON::create()->stringify($signer)
                );
            }, $signers);

            $multipart = array_merge($multipart, $signers);
        }

        if (!empty($signers_signed)) {
            $signers_signed = array_map(function ($signer) {
                return array(
                    "name" => 'signers_signed[]',
                    "contents" => JSON::create()->stringify($signer)
                );
            }, $signers_signed);

            $multipart = array_merge($multipart, $signers_signed);
        }

        $file_father = $file->fileFather();
        if (!empty($file_father)) {
            $multipart = array_merge($multipart, array(
                array(
                    "name" => 'file_father',
                    "contents" => $file_father
                )
            ));
        }

        $metadata = $file->metadata();
        if (!empty($metadata)) {
            $multipart = array_merge($multipart, array(
                array(
                    "name" => 'metadata',
                    "contents" => JSON::create()->stringify($metadata)
                )
            ));
        }

        return $multipart;
    }


    public function run($path = null, $multipart = [])
    {

        $params = array(
            'headers' => $this->headers,
        );

        if (!empty($multipart) && count($multipart) > 0) {
            $params['multipart'] = $multipart;
        }

        $uri = $this->autenticacao->url();
        $uri .= '/api';

        if ($path) {
            $uri .= "{$path}";
        }

        $response = $this->client->post($uri, $params);

        if (!in_array($response->getStatusCode(), [201, 200])) {
            throw new \Exception($response->getBody());
        }

        return JSON::create()->parse($response->getBody()->getContents());
    }
}
