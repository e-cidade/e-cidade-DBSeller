<?php

namespace App\Domain\Patrimonial\Licitacoes\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BncException extends ClientException
{
    /**
     * @var object
     */
    private $erro;

    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        $erroJson = $message;

        if (!empty($response)) {
            $erroJson = $response->getBody()->getContents();
            $this->erro = (object)json_decode($erroJson);
        }

        parent::__construct(
            $this->getMensagemErro(),
            $request,
            $response,
            $previous,
            $handlerContext
        );
    }

    /**
     * @return string
     */
    public function getMensagemErro()
    {
        if (property_exists($this->erro, 'Message')) {
            return 'BNC: ' . mb_convert_encoding("{$this->erro->Message}", 'ISO-8859-1');
        }

        return $this->getMessage();
    }
}
